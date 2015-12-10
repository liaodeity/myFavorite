<?php 
/**
 * 自动运行推荐过来的文章id，帅选pv情况
 * @author luyx 
 * @add by luyx in 2014-08-28
 * @version 1.2
 * @copyright  http://www.zol.com.cn/
 */
set_time_limit(0);
ini_set("display_errors",1);
require_once("/www/article/html/admin/include/global_url.inc");
require_once(INCLUDE_PATH."/connection.php");

define('ZOL_API_ISFW', false);      //是否使用ZOL新框架，true为使用
define('ZOL_API_UTF8', false);      //是否是以UTF8返回数据，此行可以省略
require_once('/www/zdata/Api.php'); //引入入口文件

$show = (int)$_GET['show'];
//$cachPath = '../cache/';
$cachPath = '/www/admin/html/mainpage/index_manage_2014/other/cache/';
$weekTopFilename = $cachPath.'_week_top.php';
if(!file_exists($weekTopFilename))exit('缓存生成失败了');
require $weekTopFilename;
#按照频道热度获得该频道数据
$proFileName = $cachPath.'_prohot_cache.php';
if(!file_exists($proFileName))exit('缓存生成失败了');
require $proFileName;

#加载域名所属事业部 字典
$cacheFileName = $cachPath.'_shiyebu_domain_map.php';
require $cacheFileName;

#数据来源，通过recommend进行相关推荐
$dataArr = ZOL_Api::run("Kv.MongoCenter.get" , array(
	'module'         => 'cms',           #业务名
	'tbl'            => 'zol_index',     #表名
	'key'            => 'index_data_auto_2014_recommend',   #key
));

#获取今日头条以及右侧手工的文章id
$removeArr = ZOL_Api::run("Kv.MongoCenter.get" , array(
		'module'         => 'cms',           		#业务名
		'tbl'            => 'zol_index',     		#表名
		'key'            => 'index_data_focus', 	#key
));

#两模块ID数据拼装成字符串
$removeStr = '';
if($removeArr){
	$removeStr = ','.implode(',',$removeArr).",";
}

$outTmpArr = array();
#时间段
$hour   = date("H")- 1; 
$startTime = date('Y-m-d')." 00:00:00"; 
$endTime = date('Y-m-d')." 23:59:59"; 
unset($dataArr['dtime']);

#取出一个小时前线上数据
$sql ='select * from z_index_hours where  hours <= '.$hour." and idate between '{$startTime}' and '{$endTime}' ";

$res = $DB_Document_obj->get_results($sql,"C");

if($res){
	foreach($res as $vv){
		#拼装key值 
		$key = $vv['hours']."_".$vv['pos1']."_".$vv['pos2']."_".$vv['pos3'];
		#去掉同位置重复值 ，保留最大值
	 	if(isset($outTmpArr[$key]) && ($outTmpArr[$key]['pv'] - $vv['pv']) > 0 ){
			continue;
		}
		$outTmpArr[$key]['pv']     = $vv['pv'];
		$outTmpArr[$key]['docid']  = $vv['docment_id'];
		$outTmpArr[$key]['hour']   = $vv['hours'];
		 
	}
}

#获得文章pv数据  来自于  z_index_hours
$tmpArr = array();
if($outTmpArr){
	foreach($outTmpArr as $kk=>$vv){
		$arr = explode("_", $kk);
		$tmpArr[$arr['1']][$vv['docid']] = $vv['pv'];
		
	}
	foreach($tmpArr as $tk => $tv){
		arsort($tmpArr[$tk]); #每个模块PV 降序排序
	}

}



#取每个模块后四个数据   来至于  z_index_hours
$lastArr = array();
$delDoc  = '';
foreach($tmpArr as $tk => $tvv){
	$docArr       = array_keys($tvv);//取文章ID
	if(in_array(date("N"),array(6,7))){//判断是否是周六日
		$len          = count($docArr)- 4;//是则每个模块数量减少四个
		$lastArr[$tk] = implode(',', array_slice($docArr,0,$len));
		
		$delDoc .= implode(',', array_slice($docArr,-4,4));//同时取出删除的后四个 
	}else{
		$lastArr[$tk] = implode(',', $docArr);//不是周六日全部要
	}
}

#取出头条池和已经在线上的文章详细信息  
$sqlArr = trim(str_replace(",,",",",implode(',', $dataArr).",".implode(",",$lastArr)),',');
$sql    = "select class_id,document_id,date from doc_index 
		  where document_id in (".$sqlArr.") and document_id not in (5018786,5022734) and status = 0 and published <> 0 
		  and  date <='".date("Y-m-d H:i:s")."' and rootid = 0 order by date desc ";

$docRes = $DB_Document_obj->get_results($sql,"C");

#文章的集合 &&	排除掉今日焦点以及右侧手工模块已经存在的文章
$docArr = array();
if($docRes){
	foreach($docRes as $vv){
		if(strpos($removeStr,','.$vv['document_id'].",") !==false) continue;
		$docArr['docid'][$vv['document_id']] = $vv;
		$docArr['class'][$vv['class_id']][$vv['document_id']] = $vv;
	}
}




#各区块配置读取条数
$docNums = array(1=>15,2=>15,3=>9,4=>9);
#特殊判断一下，文章发布时间需要控制，考虑周六周日
if(!in_array(date("N"),array(7))){ //不是周日  重新覆盖$lastArr 中每个模块数据
	 
	foreach($tmpArr as $tk => $tvv){  //$tmpArr 数据来至于  z_index_hours
	
		$lenNums = 2;
		$docArrs       = array_keys($tvv);
		$noRepeatArr   = array();
		foreach($docArrs as $kk=>$dv){
			$dates = strtotime($docArr['docid'][$dv]['date']) ;
			 
			if($dates >= strtotime(date("Y-m-d")) && $dates <= strtotime(date("Y-m-d 23:59:59"))){
				$noRepeatArr[] = $dv; //非周日当天的文章 存储下来
			}else{
				$lenNums -= 1;
				$delDoc .= $dv.','; //不是当天的文章 扔到删除里边
			}
		}
		 
		 
		$len   = $lenNums - ($docNums[$tk] - count($noRepeatArr));
		 
		if($len > 0){
			$len2 = count($noRepeatArr) - $len;
			$lastArr[$tk] = implode(',', array_slice($noRepeatArr,0,$len2));
			$start = "-".$len2;
			$delDoc .= implode(',', array_slice($noRepeatArr,$start,$len2));
			 
		}else{
			
			$lastArr[$tk] = implode(',', array_slice($noRepeatArr,0));
		}
	}
}

$productHotNew = $productHot;

#线上数据数据
foreach($lastArr as $kk => $lvv){
	$arr = explode(',',$lvv);
	foreach($arr as $av){
		$classid = $docArr['docid'][$av]['class_id'];
		if($productHot[$kk][$classid]['z_num'] <= 0 ) {//配置项中对应该频道配置的显示数目控制数据是0 干掉已经有的该频道数据
			#去掉数据够用的频道
			$lastArr[$kk] = preg_replace("#".$av."#i", '', $lastArr[$kk]);
			$lastArr[$kk] = str_replace(",,", ",", $lastArr[$kk]);
			continue;
		}
		$productHot[$kk][$classid]['z_num']  =  $productHot[$kk][$classid]['z_num'] - 1;
		#去掉线上重复的id
		unset($docArr['class'][$classid][$av]);
	}
}

foreach($productHot as $pk => $pv){//$pk  模块ID  1,2,3,4
	foreach($pv as $ppv){
		$lastArr[$pk] = trim($lastArr[$pk],",");
		$nums = substr_count($lastArr[$pk], ',') + 1; //计算ID个数
		if($docNums[$pk] <= $nums)continue; #已经存在的数据大于配置限制个数 
		if($ppv['z_num'] <= 0 ){continue;} #配置项中对应该频道配置的显示数目控制数据是0 频道数据不再要了 
		$artArr = $docArr['class'][$ppv['z_class_id']];
	    $docnum = count($artArr); 
		if($docnum <=0) continue;#频道没有文章的不要
		foreach($artArr as $kdv => $dv){
			if($ppv['z_num'] <= 0 )continue; #频道已经有的数据不再要了
			//列为删除的不在继续处理
			if((strpos($delDoc, "$kdv") !== false) || (strpos($lastArr[$pk], "$kdv") !== false)) continue;
			$qnums = $docNums[$pk] - $nums; #每个区块取值的数量
			if($qnums <= 0 )continue;
			$lastArr[$pk] .= ','.$kdv;
			$nums += 1;
			$ppv['z_num'] -= 1;
			unset($docArr['class'][$ppv['z_class_id']][$kdv]);
		}
		
	}
}

foreach($lastArr as $lk => $lv){
	$lv = trim($lv,",");
	$nums = substr_count($lv, ',') + 1;
	if($docNums[$lk] <= $nums)continue; #判读数据不够
	foreach($productHotNew[$lk] as  $ppv){
		$qnums = $docNums[$lk] - $nums; #每个区块取值的数量
		if($qnums <= 0 ){
			continue;
		}
		if($ppv['z_num'] <= 0 )continue; #频道已经有的数据不再要了
		$docnum = count($docArr['class'][$ppv['z_class_id']]);
		if( $docnum <=0 ) continue;
		if(isset($docArr['class'][$ppv['z_class_id']])){ 
			foreach($docArr['class'][$ppv['z_class_id']] as $kdv => $dv){
				if($ppv['z_num'] <= 0 )continue; #频道已经有的数据不再要了
				if(strpos($delDoc, "$kdv") !== false || strpos($lastArr[$lk], "$kdv") !== false) continue;
				$qnums = $docNums[$lk] - $nums; 
				if($qnums <= 0 )continue;#每个区块取值的数量达上限不要了
				$lastArr[$lk] .= ','.$kdv;
				$ppv['z_num'] -= 1;
				$nums += 1;
			}
		}
	}
}
##################################
#重组数据：
#1、各区块中大黑字PV最高的多页文章   
#2、同一行放同频道文章
#3、同频道文章一行不够两条拿同区块同事业部其他频道补足两条
#4、如果同事业部没有则用同区块其他事业部的补足
#5、晚22到第二天早上8点 用论坛数据替换 非当天文章
#6、增加专题数据展示
#add   by  huangjl  23/09/2014
##################################

//拆分每个模块的ID字符串
$tmpDoc   		= array();			
$docInfo 		= array();		#文章和频道域名对应关系
$docInfoChannel = array();		#文章按频道分组
$topOneArticleArr = array(); 	#各模块中PV最大的

/*拆分各模块字符串为数组*/
foreach($lastArr as $kk=>$vv){
	$tmpDoc[$kk] = explode(",",$vv);
}


#单页文章处理(筛选结果中多页的文章)
foreach($tmpDoc as $kk=>$vv){
	$onePage = array();
	$vv 	= implode(',', $vv);
	$sql 	= "select document_id,rootid from doc_index where rootid in (".$vv." )";
	$docOneRes = $DB_Document_obj->get_results($sql,"C");
	if($docOneRes){
		foreach($docOneRes as $dvv){
			$onePage[$dvv['rootid']] = $dvv['document_id'];
		}
	}
	foreach($tmpDoc[$kk] as $tkk => $tvv){
		if($onePage[$tvv]){
			$oneArr[$kk][$tvv] = $tkk;
		}
	}
}

/*计算各模块中多页文章PV  最大的放到第一个大黑字部分*/
$tmp = array();
$tmpArticleArr = array();
if($oneArr){
	foreach ($oneArr as $k=>$v){
		$tmp[$k] = array_flip($v);
	}
	foreach ($tmp as $k=>$v){
		foreach ($v as $kk=>$vv){
			$pv=getCurrentHourArticlePv($vv);
			$tmpArticleArr[$k][$vv] = $pv;
		}
	}

	foreach ($tmpArticleArr as $k=>$v){
		$sv = $v;
		rsort($v);
		$topOne=array_shift($v);
		$documentId= array_search($topOne, $sv);
		if($documentId){
			$topOneArticleArr[$k]= array_search($topOne, $sv);
		}
	}
}
//file_put_contents('./writeRunHour.txt','$docArr:'."\r\n".var_export($docArr,true)."\r\n",FILE_APPEND|LOCK_EX);
//exit("This is exit...\r\n");
/* 去掉 $tmpDoc 中作为大黑字的 */
if($topOneArticleArr){
	foreach ($topOneArticleArr as $k=>$v){
		unset($tmpDoc[$k][array_search($v, $tmpDoc[$k])]);
	}
}

/* 获取各文章对应的频道(域名) */
foreach ($tmpDoc as $key=>$v){
	$tpArr = array();
	foreach ($v as $k=>$vv){
		$classid = $docArr['docid'][$vv]['class_id'];
		$tpArr[$vv] = getClassInfo($classid);
	}
	$docInfo[$key] = $tpArr;
} 

/*按频道分组 数据*/
foreach ($docInfo as $k =>$v){
	foreach ($v as $key=>$vv){
		//$groupId= $dataInfo[$vv];
		if($topOneArticleArr[$k]!=$key){#排除预留大黑字的一条 ，不参与下面的排序
			$docInfoChannel[$k][$vv][] = $key;
		}
	}
} 

 
/*筛选出同频道(同二级域名)不够两条的*/
$tmp = array(); #同频道(同二级域名)不够两条的
foreach ($docInfoChannel as $k=>$v){
 	 foreach ($v as $kk=>$vv){
 	 	$num=count($vv);
 	 	if($num==2){ #同频道两条刚好OK
 	 		continue ;
 	 	}elseif($num==1){#频道一条的筛选出去
 	 		unset($docInfoChannel[$k][$kk]);
 	 		$tmp[$k][$kk] = $vv;
 	 	} elseif($num==3){#频道三条的取出两条，另外key存放； 并将余下的一条存放到筛选
 	 		$ckey =$kk."_1";
 	 		$docInfoChannel[$k][$ckey] = array_slice($vv, 0,2);
 	 		$tmp[$k][$kk] = array($vv[2]);
 	 		unset($docInfoChannel[$k][$kk]);
 	 	}elseif($num==4){#同频道四条（不超过四条） 分两组完事
 	 		$ckey1 =$kk."_1";
 	 		$ckey2 =$kk."_2";
 	 		$docInfoChannel[$k][$ckey1] = array_slice($vv, 0,2);
 	 		$docInfoChannel[$k][$ckey2] = array_slice($vv, 2,2);
 	 		unset($docInfoChannel[$k][$kk]);
 	 	} 
	 }
} 

/*把频道相关数据转换为 事业部关联数据 */
foreach ($tmp as $k => $v){
	foreach ($v as $kk => $vv){
		foreach ($vv as $kkk => $vvv){
			$groupId= $dataInfo[$kk];
			$tmp[$k][$groupId][] = $vvv;
			unset($tmp[$k][$kk]);
		}	
	}
}


/*频道不够两条 拿同事业部其他频道补齐 */
foreach ($tmp as $k =>$v){
	foreach ($v as $kk => $vv){
		$num = count($vv);
		if($num==1){#一条的不处理 留在$tmp里边 后续按照不同事业部补全方式合并
			continue ;
		}
		if($num==2){#同事业部两条 直接拿走存放到 $docInfoChannel中
			$docInfoChannel[$k][$kk] = $vv;
		 	unset($tmp[$k][$kk]);
		}elseif($num==3){#同事业部三条 取走两条存放到 $docInfoChannel中 并把余一条放到 $tmp[$k][$kk]第一条即： $tmp[$k][$kk][0] 并 unset掉后两条
			$docInfoChannel[$k][$kk] = array_slice($vv, 0,2);
			$tmp[$k][$kk][0] = $tmp[$k][$kk][2];
			unset($tmp[$k][$kk][1]);
			unset($tmp[$k][$kk][2]);
		}elseif($num==4){#同事业部四条 分两组完事 并unset掉$tmp[$k][$kk]
 	 		$ckey1 =$kk."_1";
 	 		$ckey2 =$kk."_2";
 	 		$docInfoChannel[$k][$ckey1] = array_slice($vv, 0,2);
 	 		$docInfoChannel[$k][$ckey2] = array_slice($vv, 2,2);
 	 		unset($tmp[$k][$kk]);
 	 	} 
	}
}

/*频道再不够两条 ,本模块不同事业部补全*/
foreach ($tmp as $k =>$v){
	$num = count($v);
	echo ($k ."=>".$num."\n");
	if($num == 2){
		$key	= key($v);
	 	$tpArr 	= next($v);
		$tmp[$k][$key][] = $tpArr[0];
		unset($tmp[$k][key($v)]);
	}elseif($num == 4){
		$tmpArr = array();
		$tmpArr = array_chunk($v,2);
		$tmp[$k] = array();
		foreach ($tmpArr as $kk => $val){#数组降维 分组
			 foreach ($val as $KKK=> $value){
			 		$tmpArr[$kk][$KKK] = $value[0];
			 }
			 $tmp[$k][$kk] =$tmpArr[$kk];
		}
	}
}

foreach ($tmp as $k =>$v){
	foreach ($v as $kk => $vv){
		$num = count($vv);
		if($num==2){
			if(array_key_exists($kk, $docInfoChannel[$k])){
				$key = $kk.'-2-1';
				$docInfoChannel[$k][$key] = $vv;
			}else{
				$docInfoChannel[$k][$kk] = $vv;
			}
			unset($tmp[$k][$kk]);
		}
	}
}

/*计算每行 两条数据总PV*/
foreach ($docInfoChannel as $k=>$v){
	foreach ($v as $kk=>$vv){
		$totalPv = 0;
		foreach ($vv as $vvv){
			$pv=getCurrentHourArticlePv($vvv);
			if(!$pv) $pv=0;
			$totalPv+=$pv;
			$docInfoChannel[$k][$kk]['totalPv'] = $totalPv;
		}
	}
}
 
/*计算每行平均PV  按分组平均PV降序排序*/
if($docInfoChannel){
	foreach ($docInfoChannel as $k=>$v){
		foreach ($v as $key=>$vv){
			$totalPv = $vv['totalPv'];
			unset($vv['totalPv']);
			unset($docInfoChannel[$k][$key]['totalPv']);
			$num = count($vv);
			$docInfoChannel[$k][$key]['averagePv'] = floor($totalPv/$num);
		}
		usort($docInfoChannel[$k], "arrSortFunc");
	}
} 

/*重组,覆盖旧 $tmpDoc  中的值*/
$tmpDoc   = array();
foreach ($docInfoChannel as $k=>$v){
	foreach ($v as $kk => $vv){
		unset($vv['averagePv']);
		foreach ($vv as $kkk=>$vvv){
			$tmpDoc[$k][] = $vvv;
		}
	}
} 



/*补充第一条大黑字数据*/
foreach ($topOneArticleArr as $k=>$v){
	array_unshift($tmpDoc[$k], $v);
}

$lastArr = array();
foreach($oneArr as $okk=>$ovv){
	$tmpDoc[$okk] = array_unique($tmpDoc[$okk]);
	$lastArr[$okk] = implode(",",$tmpDoc[$okk]);
}
$lastArr['dtime'] = date('Y-m-d H:i:s');
// by suhy 20150820    24号前diy头条区域 大黑字显示 id为5346120的文章  && false
if(date('md') == 1022 || date('md') == 920){
	//$tmp1Arr = explode(',',$lastArr[3]);
	//$tmp1Arr[0] = 5419077;
	//$lastArr[2] = implode(',',$tmp1Arr);
	$lastArr[3] = str_replace('5469668', '5465866', $lastArr[3]);
}
print_r($lastArr);
#Kv.MongoCenter.set
$dataArr = ZOL_Api::run("Kv.MongoCenter.set" , array(
	'module'         => 'cms',                    		#业务名
	'tbl'            => 'zol_index',              		#表名
	'key'            => 'index_data_auto_2014',  		#key
	'data'           => $lastArr,                  		#数据
	'life'           => 865555,                   		#生命期
));

/**
 * 获取文章频道信息
 * @param int $classId
 * @return Ambigous <array, boolean>
 */
function  getClassInfo($classId){
	global $DB_Document_obj;
	$classId = (int)$classId;
	if(!$classId){
		return   false;
	}
	$sql = "SELECT  hostname FROM  document_class  WHERE web='zol'  AND class_id={$classId} ";
	return  $DB_Document_obj->get_var($sql);
}

/**
 * 获取文章1小时前的PV
 * @param int $docId
 * @return boolean|multitype:
 */
function getCurrentHourArticlePv($docId){
	
	global $DB_Document_obj,$hour,$startTime,$endTime,$show;
	$tmpArr = array();
	$pv 	= 0;
	$docId 	= (int)$docId;
	if(!$docId){
		return 0  ;
	}
	/*$sql ='select pv from z_index_hours where  hours = '.$hour." and idate between '{$startTime}' and '{$endTime}' and docment_id= {$docId}  order by  idate desc,pv  desc   limit 1";
	$pv=$DB_Document_obj->get_var($sql);*/
	
	$pv=$DB_Document_obj->get_var("select hours_pv  from assess_hours  where document_id={$docId}  ");
	return  (int)$pv;
}


/**
 * 按照平均PV字段  对二维数组重新排序
 * @param int $a
 * @param int $b
 * @return number
 */
function arrSortFunc($a,$b){
	if($a['averagePv'] == $b['averagePv']){
		return 0;
	}
	return($a['averagePv']<$b['averagePv']) ?1 : -1;
}

/**
 * 根据二维数组中第二维每个元素个数排序
 * @param unknown $arr
 * @return unknown
 */
function bubbleSort($arr)
{
	$len = count($arr);
	for($i=1; $i<$len; $i++)//最多做n-1趟排序
	{
		$flag = false;    //本趟排序开始前，交换标志应为假
		for($j=$len-1;$j>=$i;$j--)
		{	$count1 = count($arr[$j]);
			$count2 = count($arr[$j-1]);
			if($count1<$count2)//交换记录  
			{ 
				$x=$arr[$j];
				$arr[$j]=$arr[$j-1];
				$arr[$j-1]=$x;
				$flag = true;//发生了交换，故将交换标志置为真
			}
		}
		if(!$flag)//本趟排序未发生交换，提前终止算法
			return $arr;
	}
}
/**
 * 对文章pv排序的回调函数
 */
function pvSort($a,$b){
	if ($a == $b) return 0;
	return ($a > $b) ? -1 : 1;
}
/**
 * 检查文章大黑字是否是当天的文章
 */
function checkIsTodayArticle($docId){
	if(!$docId)return false;
	$dataArr = ZOL_Api::run("Article.Doc.getDocInfo" , array(
			'docId'          => $docId,         #文章ID
			'rtnCols'        => 'pub_date',      #
	));
	return strtotime($dataArr['pub_date']) < strtotime(date('Y-m-d 00:00:00')) ? false : true;
}
/**
 * 当前文章id不是今天的文章，获取下一个id，以便将今天的文章作为大黑字
 */
function getNextDocId(&$arr){
	if(!$arr) return false;
	next($arr);
	$topOne = key($arr);

	return $topOne;
}
