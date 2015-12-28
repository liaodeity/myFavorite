<?php 
/**
 * @describe	文章页相关阅读数据-猜你喜欢和本周必读
 * @author		weixj
 * @date		2014-2-17
 * @changelog	20150713 suhy
 * @desc
 * 1.优先同频道文章，排除“Z超值文章”
 */
ini_set('display_errors', 0);
# 调试模式  false
if(0){
	ini_set("display_errors", "On");
	error_reporting(E_ALL | E_STRICT);
}
include "../../include/public_connect.php";
define('ZOL_API_ISFW', false);      //是否使用ZOL新框架，true为使用
define('ZOL_API_UTF8', false);      //是否是以UTF8返回数据，此行可以省略
require_once('/www/zdata/Api.php'); //引入入口文件
$expire = 1800;
if(!isset($_GET['refresh'])){
	ZOL_Api::run("Base.Page.setExpires" , array('second'=>$expire));  // 3600*0.5 半小时
}
#初始化参数
$classId = isset($_GET['class_id']) && (int)$_GET['class_id'] ? (int)$_GET['class_id'] : 0;
$doc_id = isset($_GET['doc_id']) && (int)$_GET['doc_id'] ? (int)$_GET['doc_id'] : 0;
$userid  = addslashes(  trim( strip_tags($_COOKIE['zol_userid']) ) );
$callback = isset($_GET['callback']) && $_GET['callback'] ? trim($_GET['callback']) : 'callback';
# 是否获取文章的简介，pv等信息,（互动的个人中心需要用到。如果值为6，则获取）   
$otherInfo = (int)addslashes($_GET['otherInfo']);
# 适配文章页的猜你喜欢，加上url参数以作控制。
$dataNum = (int)addslashes($_GET['dataNum']);
$dataNum = $dataNum > 0 ? $dataNum : 14;
if($dataNum>50) $dataNum = 50;
# 配合m端的接口，，当这个参数值为 2 的时候，则不返回weekRead的参数（本周必读）。
$noWeekRead = isset($_GET['noWeekRead']) ? (int)addslashes($_GET['noWeekRead']) : 0;

if(!$classId || !$callback){
	exit;
}
# 使用从库 进行查询
$db_doc = $db_doc_read = new DB_Document_Read;

########################Start######
# 测试代码 suhy
/* $classArr = array(295,291,194,210,373);
$total = count($classArr);
$icIndex = mt_rand(0, $total-1);

$classId = $classArr[$icIndex]; 

echo $classId;
echo '<br><br>'; */
########################END######

#要返回的json数组
$resultArr = array();
#是否刷新
$refresh = (int)$_GET['refresh']==1 ? true : false;
#缓存key
$cacheKey = 'article_guess_you_like'.$doc_id.':'.$dataNum.':'.$noWeekRead;
$dataArr = ZOL_Api::run("Kv.MongoCenter.get" , array(
		'module'         => 'cms',        #业务名
		'key'            => $cacheKey,    #key
));
// $dataArr && $refresh
$historyNum = count($dataArr);
if($dataArr && !$refresh && $historyNum==$dataNum ){
	// print_r($dataArr);exit('35');
	echo $callback.'('.json_encode($dataArr).')';
	exit('');
}

#猜你喜欢
##############################
# 1.根据当前文章的标题用其关键词获取相关文章推送给用户  by suhy
##############################
# 从100中随机取36条。   #####  36是 $dataNum 的一个实例，下面的程序都是  ########  表示猜你喜欢返回的数据数量
$randNum = 100;
# 用于排重
$unArr = array(999999,);
# 获取当前的doc_id
$docArr = array();
if($doc_id){
	$docArr[] = $doc_id;
	# 根据当前的文章id，取最高优先级的规则的数据.
	$res1_0 = get_arti_by_word($docArr[0],$dataNum);
	//var_dump($res1_0);exit('96_1');
	$num0 = count($res1_0);
	# 记录进入日志
	if($num0 <= 0) {
		//toLog($docArr[0]);
	}
	//var_dump($dataNum.'=='.$num0);exit('91_1');
	# 第一优先级取出的数据不足36的时候   $num0 < 36  true  	#####  36是 $dataNum 的一个实例，下面的程序都是  ########
	if($num0 < $dataNum){
		# 获取第一优先级的数据的docId
		$res1_0_1 = array();
		# 储存第一优先级的文章id，用于排重
		foreach($res1_0 as $key=>$value){
			$unArr[] = $key;
			$res1_0_1[] = $key;
		}
		# 得到一个文章关联的产品id
		$proId = get_product_id($docArr);
		//var_dump($proId);exit('103_1');
		# 没有proId则使用“看了又看”   $proId
		if($proId){
			# 返回形如array(0=>5255718,1=>5255719...)
			$docArr1_1 = get_docid_by_proid($proId,$dataNum-$num0,$unArr);
			# 如果产品id获取不到相关的文章，则使用第三优先级规则
			if(!$docArr1_1){
				$docArr1_1 = get_docid_by_lookmore($docArr,$dataNum-$num0,$unArr);
				$docArr1_1 = array_keys($docArr1_1);
				# 经过proId却没有数据
				$proNoIdFlag = true;
			}
			# 第一优先级和第二优先级的规则的数据合并
			$docArr1_1 = array_merge($res1_0_1,$docArr1_1);
			if(is_array($docArr1_1)){
				$docNum = count($docArr1_1);
				$docArr1_1_1 = array();
				# 用于排重
				foreach($docArr1_1 as $key=>$value){
					$docArr1_1_1[$value] = 9;
					$unArr[] = $value;
				}
			}else{
				$docNum = 0;
			}
			//var_dump($docArr1_1);exit('128_1');
			# 通过产品id获取的文章id不够多
			$docArr1_2 = array();
			if($docNum < $dataNum && !isset($proNoIdFlag)){
				$addNum = $dataNum - (int)$docNum;
				$docArr1_2 = get_docid_by_lookmore($docArr,$addNum,$unArr);
			}elseif(!isset($proNoIdFlag)){
				$docArr1_2 = array();
			}
			if(isset($docArr1_1_1)){
				$docArr1_1_1 = array_keys($docArr1_1_1);
				$docArr1_2 = array_keys($docArr1_2);
				$docArr1_2 = array_merge($docArr1_1_1,$docArr1_2);
			}else{
				$docArr1_2 = array_keys($docArr1_2);
			}
			$docArr1_2 = array_values($docArr1_2);
			#var_dump($docArr1_2); exit('144_3');
		}else{
			$docArr1_2 = get_docid_by_lookmore($docArr,$dataNum-$num0); //  52346513=>324
			# 如果第一优先级有数据，则进行补全，数组合并，一共36条
			if($num0 > 0){
				$docArr1_2 = $res1_0 + $docArr1_2;
			}
			$docArr1_2 = array_keys($docArr1_2);
		}
	}else{
		# 第一优先级规则取出的数据足够
		$docArr1_2 = is_array($res1_0) ? array_keys($res1_0) : array();
		#var_dump($docArr1_2); exit('156_3');
		//exit('158');
	}

	if(is_array($docArr1_2)) $num = count($docArr1_2);

	#var_dump($docArr1_2);exit('164_2');

	//至此的$docArr1_2格式是：
	// array(1) {
	// 		[0]=>
	// 		int(5249741)
	// 	}

	if($num < $dataNum){
		$fResult_1 = array();
		if($num > 0){
			# part1
			$fResult_1 = get_data_by_docid($docArr1_2);
			#将返回的数组，用其对应的docId作为键
			$fResult = array();
			foreach($fResult_1 as $key=>$value){
				$fResult[$value['document_id']] = $value;
			}
			# 重新赋值
			$fResult_1 = array();
			# 按照原始的id顺序 排回
			foreach($docArr1_2 as $key=>$value){
				$fResult_1[$value] = $fResult[$value];
			}
		}
		# part2   lookmore中取出的docId不足，使用C计划补充
		//$resDocArr2 = get_data_by_hot_v3($randNum,$dataNum-$num,$docArr1_2,true);
		$resDocArr2 = count($docArr1_2) < $dataNum ? get_data_by_hot_v3($randNum,$dataNum-$num,$docArr1_2,true) : array();
		$fResult = array_merge($fResult_1,$resDocArr2);
		#var_dump($dataNum-$num,$fResult_1,$resDocArr2,$fResult);exit('193-5');
	}else{
		# 取100，从中随机36条按uv倒序
		$resDocArr = array_values(array_slice($docArr1_2,0,$dataNum));
		$fResult_1 = get_data_by_docid($resDocArr);
		//var_dump($fResult_1);exit('202_1');
		#将返回的数组，用其对应的docId作为键
		$fResult = array();
		foreach($fResult_1 as $key=>$value){
			$fResult[$value['document_id']] = $value;
		}
		# 按照原始的id顺序 排回
		$fResult_1 = array();
		foreach($resDocArr as $key=>$value){
			$fResult_1[$value] = $fResult[$value];
		}
		$fResult = $fResult_1;
		#var_dump($resDocArr);exit('208');
	}
	#var_dump($fResult);exit('210_1');
}elseif($ip_key){
	# 没有查询到redis中的doc_id数据 通过B计划或C计划获取36条数据
	$fResult =  get_data_by_hot_v3($randNum,$dataNum,array(),true);
	//exit('205_1');
}else{
	# 如果连ip_ck都不存在，使用C计划获取数据
	$fResult =  get_data_by_hot_v3($randNum,$dataNum,array(),true);
	//exit('210_1');
}
$likeData = result_recombination($fResult);

#var_dump($likeData);exit('#224-1#');
# 获取文章的其他信息
if($otherInfo && $otherInfo == 6){
	$newRes = array();
	foreach($likeData as $key=>$value){
		# 获取文章的pv和评论数
		$likeData[$key]['replyNum'] =  ZOL_Api::run("Article.Comment.getCount" , array(
		'docId'          => $value['docId'],         #文章ID
		));
		$dataArr = ZOL_Api::run("Article.Function.getHitsAndScore" , array(
				'docId'          => $value['docId'],         #文章ID
				'rtnCols'        => 'hits',          #
		));
		$likeData[$key]['hits'] = (int)$dataArr['hits'];
		# 获取文章的简介
		$dataArr = ZOL_Api::run("Article.Doc.getDocContent" , array(
			'docId'          => $value['docId'],         #文章ID
			'len'            => 40,              #长度
			'getDetailFlag'  => 1,               #只获得内容
			'rtnCols'        => 'content',       #
		));
		$dataArr['content'] = iconv('GB2312', 'UTF-8', $dataArr['content']);
		$likeData[$key]['content'] = $dataArr['content'] ? $dataArr['content'] : $value['title'];
	}
}
#var_dump($likeData);exit('#over#');
# 如果传了这个参数且为2，则不需要 “本周必读” 的数据
if($noWeekRead == 2){
	if($likeData && 0){
		#写入缓存
		ZOL_Api::run("Kv.MongoCenter.set" , array(
		'module'         => 'cms',            #业务名
		'key'            => $cacheKey,        #key
		'data'           => $resultArr,  	  #数据
		'life'           => $expire,             #生命期  3600*2    2小时
		));
	}
	#处理结果数组
	$resultArr['like'] =  $likeData;
	echo $callback.'('.json_encode($resultArr).')';
	exit();
}

# 本周必读
##############################
# 1.取热门文章50篇
# 2.从50篇文章中随机取出14条展示
# 3.文章都是本周内发表的
##############################
$w = date('w');
$w = $w ? $w : 7;
$timeStamp = $w*24;
# 4条带图文章，取得是 本周内的，按照点击倒序（只针对这4条）
$dataArr0 = ZOL_Api::run("Article.Doc.getList" , array(
	'cid'            => $classId,             #频道ID
	'showimg'        => 1,               	#获取有导读图的文章
	'imgwidth'       => 151,             	#导读图宽度  240
	'imgheight'      => 113,              	#导读图高度  180
	'orderby'        => 2,               	#排序
	'hourbtn'        => $timeStamp,         #多少小时以内的文章
	'num'            => 4,               	#数量
));
$restNum = 14;
if(is_array($dataArr0) && $dataArr0){
	$restNum = 14 - count($dataArr0);
}
if($w == 1)$w++;
$timeStamp = $w*24*3600;
$dataArr1 = ZOL_Api::run("Recsys.Content.getHotDocList" , array(
		'isAll'          => 1,        #获取全部频道
		'num'            => 50,              #数量
		'timeStamp'      => $timeStamp,      #时间戳
));
$dataArr = array();
$keyArr = array_rand($dataArr1,$restNum);
foreach($keyArr as $key=>$value){
	$dataArr[] = $dataArr1[$value];
}
$dataArr = array_merge($dataArr0,$dataArr);// 理想情况下是4条本频道的+10条热门的文章数据

$newArr2 = array();
foreach($dataArr as $key=>$value){
	$value['docId'] = $value['docId']>0 ? $value['docId'] : $value['doc_id'];
	$dataArr[$key]['doc_id'] = $value['docId'];
	$pic_src = ZOL_Api::run("Article.Function.getThumbPic" , array(
		'docId'          => $value['docId'],         #文章ID
		'width'          => 151,             #宽度   240
		'height'         => 113,             #高度  180
		'default'        => 1,               #无图时返回占位图
	));
	$docInfo = ZOL_Api::run("Article.Doc.getDocInfo" , array(
			'docId'          => $value['docId'],         #文章ID  5094009
			'rtnCols'        => 'title,classInfo,class_id',   #
	));
	$preId = substr($value['docId'],0,3);
	$url = 'http://'.$docInfo['classInfo']['host'].'/'.$preId.'/'.$value['docId'].'.html';
	$newArr2[] = array(
			'pic_src'			=>$pic_src,
			'docId'          	=> $value['docId'],
			'url'				=>$url,
			'title'				=>mb_convert_encoding($docInfo['title'], 'utf-8', 'GBK'),
		);
}


foreach($newArr2 as $key=>$value){
	# 前4条图文需要有短标题
	if($key < 4){
		$value['did'] = (int)$value['docId']>0 ? $value['did'] : $value['doc_id'];
		$value['docId'] = (int)$value['docId']>0 ? $value['docId'] : $value['did'];
		if($value['docId']){
			$titleArr = ZOL_Api::run("Article.Doc.getDocInfo" , array(
					'docId'          => $value['docId'],         #文章ID
					'rtnCols'        => 'short_title',   #
			));
			$newArr2[$key]['short_title'] = mb_convert_encoding($titleArr['short_title'], 'utf-8', 'GBK');
			if(!$newArr2[$key]['short_title']) $newArr2[$key]['short_title'] = $value['title'];
		}
	}else{
		break;
	}
}

# 获取文章的其他信息-本周必读
if($otherInfo && $otherInfo == 6){
	$newRes = array();
	foreach($newArr2 as $key=>$value){
		# 获取文章的pv和评论数
		$newArr2[$key]['replyNum'] =  ZOL_Api::run("Article.Comment.getCount" , array(
			'docId'          => $value['docId'],         #文章ID
		));
		$dataArr = ZOL_Api::run("Article.Function.getHitsAndScore" , array(
				'docId'          => $value['docId'],         #文章ID
				'rtnCols'        => 'hits',          #
		));
		$newArr2[$key]['hits'] = (int)$dataArr['hits'];
		# 获取文章的简介
		$dataArr = ZOL_Api::run("Article.Doc.getDocContent" , array(
			'docId'          => $value['docId'],         #文章ID
			'len'            => 40,              #长度
			'getDetailFlag'  => 1,               #只获得内容
			'rtnCols'        => 'content',       #
		));
		$dataArr['content'] = iconv('GB2312', 'UTF-8', $dataArr['content']);
		$newArr2[$key]['content'] = $dataArr['content'] ? $dataArr['content'] : $value['title'];
	}
}


#处理结果数组
$resultArr['like'] =  $likeData;
//$resultArr['news']  = $newData;
$resultArr['weekRead']  = $newArr2;

#写入缓存
ZOL_Api::run("Kv.MongoCenter.set" , array(
	'module'         => 'cms',            #业务名
	'key'            => $cacheKey,        #key
	'data'           => $resultArr,  	  #数据
	'life'           => $expire,             #生命期  3600*2    2小时
));

//var_dump($resultArr); exit('364_1');

echo $callback.'('.json_encode($resultArr).')';
exit();

//**************************以下是需要用到的函数*********************************************//
# 取"猜你喜欢"数据时如果不够数量，使用最新的文章进行补充
function data_make_up($classId,$needNum){
	$newArr = array();
	$newArr = ZOL_Api::run("Article.Doc.getList" , array(
			'cid'            => $classId,        #频道ID
			'showimg'        => 1,               #获取有导读图的文章
			'imgwidth'       => 151,             #导读图宽度   240
			'imgheight'      => 113,             #导读图高度  180
	        'hourbtn'        => 360,             #多少小时以内的文章
			'orderby'        => 1,               #排序
			'num'			 => $needNum,
	));
	# 如果不够，使用手机频道的数据进行补充
	$nNUm = count($newArr);
	if(is_array($newArr) && $nNUm<$needNum && $classId!=74){
		$num1 = $needNum - $nNUm;
		$newArr2 = ZOL_Api::run("Article.Doc.getList" , array(
				'cid'            => 74,        #频道ID
				'showimg'        => 1,               #获取有导读图的文章
				'imgwidth'       => 151,             #导读图宽度
				'imgheight'      => 113,             #导读图高度
		        'hourbtn'        => 360,         #多少小时以内的文章
				'orderby'        => 1,               #排序
				'num'			 => $num1,
		));
		$newArr = array_merge($newArr,$newArr2);
	}
	$resArr = array();
	if($newArr){
		foreach($newArr as $key=>$value){
			if($key == $needNum) break;
			$resArr[] = array(
					'pic_src' => $value['pic_src'],
					'docId'  => $value['doc_id'],
					'title'   => mb_convert_encoding($value['title'], 'utf-8', 'GBK'),
					'url'   => $value['url'],
			);
		}
	}
	
	return $resArr;
}

#########################################################
# 以下是新规则的文章页的“猜你喜欢”规则需要用到的函数
#########################################################

# 根据一串doc_id获取需要的数据
function get_data_by_docid($idArr,$fields='document_id,title,class_id'){
	global $db_doc_read;
	// 需要获得的字段：document_id,title,url,pic_src
	$fields='document_id,title,class_id,date';
	$idStr = trim(implode(',',$idArr),',');
	$sql = 'select '.$fields.' from doc_index where document_id in ('.$idStr.') order by date desc';
	$result = $db_doc_read -> get_results($sql,'A');
	//var_dump($result);exit('369_2');
	if($result){
		foreach($result as $key=>$value){
			# 从mongo中查看是否有数据
			$getDataArr = ZOL_Api::run("Kv.MongoCenter.get" , array(
			'module'         => 'cms',           		#业务名
			'tbl'            => 'zol_index',     		#表名
			'key'            => 'get_rel_doc'.$value['document_id'], 	#key
			));
			# 暂时不适用缓存 is_array($getDataArr) && $getDataArr    false
			if(is_array($getDataArr) && $getDataArr){
				# 对使用了mongo缓存的数据进行标记
				$getDataArr['isMongo'] = true;
				$result[$key] = $getDataArr;
				// 				# 测试，将误操作的key置空
				// 				ZOL_Api::run("Kv.MongoCenter.set" , array(
				// 					'module'         => 'cms',                    		#业务名
				// 					'tbl'            => 'zol_index',              		#表名
				// 					'key'            => $value['document_id'], 			#key
				// 					'data'           => '',                  #数据
				// 					'life'           => 3*24*3600,                   	#生命期,3天
				// 				));
			}else{
				# 获取文章地址
				//$result[$key]['url'] = 'http://dynamic.zol.com.cn/channel/mainpage/get_doc_url.php?docId='.$value['document_id'];
				$result[$key]['url'] 	= ZOL_Api::run("Urls.Doc.getDocUrl" , array(
						'docId'          => $value['document_id'],         	# 文章ID
						'classId'        => $value['class_id'],             # 频道ID
						'fullUrl'        => 1,               				# 全URL
						'getSlideUrl'    => 0,               				# 组图文章url类型:0文章模式 1组图地址 默认0（兼容老业务的处理）
				));
				$result[$key]['pic_src'] = ZOL_Api::run("Article.Function.getThumbPic" , array(
						'docId'          => $value['document_id'],         #文章ID
						'width'          => 150,             #宽度
						'height'         => 104,             #高度
						'default'        => 1,               #无图时返回占位图
				));

				#并且将其存放在mongo缓存中
				ZOL_Api::run("Kv.MongoCenter.set" , array(
				'module'         => 'cms',                    		#业务名
				'tbl'            => 'zol_index',              		#表名
				'key'            => 'get_rel_doc'.$value['document_id'], 			#key
				'data'           => $result[$key],                  #数据
				'life'           => 7*24*3600,                   	#生命期,3天
				));
			}
				
		}# foreach 结束
	}else{
		//echo $sql;
		return array();
	}

	return $result;
}

/**
 * 从取100条($randNum)结果中条数据，再从中随机36条，按uv倒序返回。  by suhy 20150616
 * @params array $array 传入所有的结果集，从中取100条，再此100条中随机取36条,形如： array(36) {[5255718]=>int(819)[5264685]=>int(593)
 * @params int	$getNum 从给定的数组中，随机取多少条，默认取36条。
 * @return 返回形如： array(36) {[5255718]=>int(819)[5264685]=>int(593)...
 */
function get_from_rand($array,$getNum=36){
	$randNum = $getNum;
	$arrNum = count($array);
	if($arrNum < $randNum){
		$randNum = $arrNum-1;
	}
	if($randNum<1)return $array;
	$baseArr = array_slice(array_keys($array),0,$randNum);
	$res1 = array_rand($baseArr,$getNum);

	if($res1 && is_array($res1)){
		$newArray1 = array();
		foreach($res1 as $key=>$value){
			$newArray1[] = $baseArr[$value];
		}
		# 将取到的数据随机打乱
		# shuffle($newArray1);
		$newArray2 = array();
		foreach($newArray1 as $key=>$value){
			$newArray2[$value] = $array[$value];
		}
		arsort($newArray2);
	}else{
		# 如果只随机取一条
		$newArray1 = $baseArr[$res1];
		$newArray2 = array();
		$newArray2[$newArray1] = $array[$newArray1];

		return $newArray2;
	}

	return $newArray2;
}

# 通过一篇/组文章id获取一个产品id
function get_product_id($docArr){
	if(!is_array($docArr))return false;
	foreach($docArr as $key=>$value){
		$dataArr = ZOL_Api::run("Article.Doc.getDocPro" , array(
				'docId'          => $value,         #文章id
		));
		$proId = (int)$dataArr[0]['proId'];
		if($proId > 0) break;
		if($key > 0) break;
	}
	if(!isset($proId) || empty($proId)) $proId = 0;

	return $proId;
}

# 跟据产品id，获取相关的文章id
function get_docid_by_proid($proId,$num=36,$unArr=array()){
	if(!$proId) return array();
	global $db_doc_read;
	if(is_array($proId)) $proIdStr = implode(',',$proId);
	# 无论如何取多一点，以防不够
	# 获取产品id，取近3个月的数据
	$sql = 'select document_id from document_index_hardware doc where `date` < "'.date('Y-m-d H:i:s').'" and doc.date > "'.date('Y-m-d H:i:s',time()-(2160*3600)).'"  and doc_type_id in (1,2,3,4,5,6) and hardware_id in('.$proIdStr.') limit 36';
	$dataArr = $db_doc_read->get_results($sql);
	if($dataArr){
		$newArr = array();
		foreach($dataArr as $key=>$value){
			$docId = (int)$value['document_id'];
			# 排重，并将其放入新数组
			if(in_array($docId,$unArr)){
				continue;
			}
			$newArr[] = $docId;
		}
	}
	#需要多少条则截取多少条数据
	$dataArr = array_slice($newArr, 0,$num);
	if(!$dataArr){
		return array();
	}
	shuffle($dataArr);
	
	return $dataArr;
}

# 根据看了又看，获取指定数量的文章id
function get_docid_by_lookmore($docArr,$paramNum=36,$unArr=array()){
	global $db_doc_read;
	$docStr = implode(',',$docArr);
	# 数据库操作对象 $db_doc
	$sql = 'select * from z_lookmore where doc_id in('.$docStr.') limit 100';
	$res1 = $db_doc_read->get_results($sql,'O');

	//var_dump($res1);exit('506_4');
	$resArr = array();
	if($res1){
		$arrayArr = array();
		foreach($res1 as $key=>$value){
			$arrStr = $value->more;
			$strNum = strlen($arrStr);
			$strposi = $strNum < 5000 ? strrpos($arrStr,',') : strpos($arrStr,',',$strNum-1);
			$arrStr = substr($arrStr,0,$strposi);
			$cmdStr1 = '$res1_1['.$key.'] = array('.$arrStr.');';
			eval($cmdStr1);
			foreach($res1_1[$key] as $k1=>$v1){
				if(array_key_exists($k1,$arrayArr)){
					$arrayArr[$k1] = $arrayArr[$k1] + $v1;
				}
			}
				
			$arrayArr += $res1_1[$key];
		}
		# 排重
		if($unArr){
			foreach($unArr as $k1=>$v1){
				if(array_key_exists($v1,$arrayArr)){
					unset($arrayArr[$v1]);
				}
			}
		}
		# 排除已经阅读过的文章id
		foreach($docArr as $key=>$value){
			if($arrayArr[$value]){
				unset($arrayArr[$value]);
			}
		}
		# 排除后，如果没有数据了，则返回空数组
		if(!$arrayArr) return array();
		$resArr1 = $arrayArr;

		# 按照阅读次数，进行倒序排
		arsort($resArr1);
		if(is_array($resArr1)){
			$num = count($resArr1);
		}
		$resArr2 = $num > $paramNum ? get_from_rand($resArr1,$paramNum) : $resArr1;

		return $resArr2;
	}else{
		# lookmore中没有数据  返回空数组
		return $resArr;
	}
}

/**
 * 将最后的结果，进行筛选，防止数组中不必要的数组单元进行回传，浪费流量，只传必要的数据
 */
function result_recombination($result){
	global $db_doc_read,$dataNum;
	if(!$result){
		return false;
	}else{
		$fResult = $result;
	}
	$idArr = array();
	$fResultNew = array();
	foreach($fResult as $key=>$value){
		if(!$value['document_id']) continue;
		$idArr[] = $value['document_id'];
		$fResultNew[$value['document_id']]['url'] = $value['url'];
		$fResultNew[$value['document_id']]['title'] = mb_convert_encoding($value['title'], 'utf-8', 'GBK');
		$fResultNew[$value['document_id']]['pic_src'] = $value['pic_src'];
		$fResultNew[$value['document_id']]['docId'] = $value['document_id'];
	}

	$idStr = implode(',',$idArr);
	$sql = 'SELECT document_id,short_title from document_index_title where document_id in('.$idStr.') limit 100';
	$res2_1 = $db_doc_read->get_results($sql,'A');
	# 使得到的数据保持顺序
	if($res2_1){
		$res2_2 = array();
		foreach($res2_1 as $key=>$value){
			$res2_2[$value['document_id']] = $value;
		}
		$res2_1 = $res2_2;
	}

	if($res2_1){
		$fResult = array();
		$i = 0;
		foreach($idArr as $key=>$v_0){
			if($key > $dataNum) break;
			$value['document_id'] = $v_0;
			if(isset($res2_1[$v_0])){
				$value = $res2_1[$v_0];
				$value['short_title'] = mb_convert_encoding($value['short_title'],'UTF-8','GBK');
			}else{
				$value['short_title'] = $fResultNew[$value['document_id']]['title'];
			}

			$value['short_title'] = strlen($value['short_title']) > 10 ? $value['short_title'] : $fResultNew[$value['document_id']]['title'];

			$fResultNew[$value['document_id']]['short_title'] = $value['short_title'];
			$fResult[$i] = $fResultNew[$value['document_id']];
			$i++;
			$value = array();
		}
	}
	

	return $fResult;
}

/**
 *  通过访问的文章uv推文章
 *  @params int 	$num 		从多少条数据中随机取数据。例如从100条数据中随机取数据。
 *  @parmas int 	$getNum 	返回的数据条数的数量，例如需要补充21条数据，此时传21 就好了
 *  @params array 	$unDocArr 	查询数据，需要排除进行的文章id集合，一维数组。
 * 	@params boolean	$is_miss 	用于标记是否是真正需要推的数据，true表示错过，不是真正要推的
 */
function get_data_by_hot_v3($num=100,$getNum=36,$unDocArr=array(),$is_miss=true){
	global $db_doc_read,$doc_id,$dataNum;
	# 对一部分排重
	if($unDocArr){
		$unStr = implode(',',$unDocArr);
		$whereStr = ' where docId not in('.$unStr.') ';
	}else{
		$whereStr = ' where 1 AND  docId<>'.$doc_id.'  ';
	}
	$sql = 'SELECT docId,uv from article_monitor_v2 '.$whereStr.' ORDER BY uv desc limit '.$num;
	$result2 = $db_doc_read->get_results($sql);
	# 在100个单元中随机取出36个单元，并按uv排倒序
	if($result2){
		# 将可能存在的字串类型转成int型
		$result2_1 = array();
		foreach($result2 as $key=>$value){
			$value1 = array();
			$value1['docId'] = (int)$value['docId'];
			$value1['uv'] = (int)$value['uv'];
			$result2_1[$key] = $value1;
		}
		$result2 = $result2_1;
		$res1 = array_rand($result2,$getNum);
		if(!is_array($res1)){
			$res1 = array($res1);
		}
		if($res1){
			$newArray1 = array();
			foreach($res1 as $key=>$value){
				$newArray1[] = $result2[$value];
			}
			$newArray2 = array();
			foreach($newArray1 as $key=>$value){
				$newArray2[$value['docId']] = $value['uv'];
			}
			arsort($newArray2);
			# 根据docId获取文章的title,short_title,pic_src
			# 重组一个去获取文章信息的docId集合
			$docArr = array_keys($newArray2);
			$dataArr = get_data_by_docid($docArr);
			# 如果标记了不是命中推送的话，就加上url参数标记  // $is_miss  文章页的“猜你喜欢”不用添加vlike=miss参数  change 20150804
			if($is_miss){
				foreach($dataArr as $key=>$value){
					$dataArr[$key]['url'] .= '?vlike=miss&from=article_guess';
				}
			}
			# 将含有文章信息的数据用其docId作为键
			$newArray3 = array();
			foreach($dataArr as $key=>$value){
				$newArray3[$value['document_id']] = $value;
			}
			//var_dump($result2);exit('634_1');
			# 获取之后，按照uv倒序的顺序返回
			$newArray4 = array();
			foreach($newArray2 as $key=>$value){
				$newArray4[$key] = $newArray3[$key];
			}
		}
	}else{
		$newArray4 = array();
	}

	return $newArray4;
}

/**
 * 通过关键字获取相关文章id
 * @params int $docId 	根据用户浏览的文章ID
 * @params int $num		需要返回的文章id的数量，最多是36（此参数暂时不用）
 */
function get_arti_by_word($docId,$num2=36){
	global $randNum,$classId,$db_doc_read;
	if(!$docId>0) return false;
	# 将小结果集放前边（小结果集驱动大结果集）
	$fields = 't.article_id,t.article_type_id,t.uv,t.word,t.class_id';
	# 对96频道的全国行情特殊判断
	$whereEle1 = $classId==96 ? ' ' : ' AND class_id<>96 ';
	$wheres1 = ' t2.word is not null AND t.bbsUrl="" AND t.article_type_id not in(6,7) AND class_id<>370 AND class_id='.$classId.$whereEle1;
	# 同频道的没有，则推其他频道的
	$wheres2 = ' t2.word is not null AND t.bbsUrl="" AND t.article_type_id not in(6,7) AND class_id<>370 AND class_id<>'.$classId.$whereEle1;
	//$sql = 'SELECT t.article_id,t.uv,t.word,t2.word as w,count(*) as cnt from (SELECT word from tongji_article_title_words where article_id='.$docId.') as t2 LEFT JOIN  tongji_article_title_words t
	//on t.word=t2.word where t2.word is not null GROUP BY t.article_id ORDER BY uv desc limit '.$randNum;
	$sql = '(SELECT '.$fields.',t2.word as w,count(*) as cnt,t.bbsUrl from (SELECT word from tongji_article_title_words where article_id='.$docId.') as t2 LEFT JOIN  tongji_article_title_words t
	on t.word=t2.word where '.$wheres1.' GROUP BY t.article_id ORDER BY uv desc limit '.$randNum.') UNION 
			(SELECT '.$fields.',t2.word as w,count(*) as cnt,t.bbsUrl from (SELECT word from tongji_article_title_words where article_id='.$docId.') as t2 LEFT JOIN  tongji_article_title_words t
	on t.word=t2.word where '.$wheres2.' GROUP BY t.article_id ORDER BY uv desc limit '.$randNum.')';
	#echo $sql;exit();
	$resArr1 = $db_doc_read->get_results($sql);
	if(is_array($resArr1)){
		$num = count($resArr1);
		$resArr2 = $resArr2_1= array();
		$resArr2_1['currnetClass'] = $resArr2_1['otherClass'] = array();
		foreach($resArr1 as $key=>$value){
			# 将docId和uv等值转为整形
			$value['article_id'] = (int)$value['article_id'];
			$value['class_id'] = (int)$value['class_id'];
			$value['uv'] = (int)$value['uv'];
			$value['cnt'] = (int)$value['cnt'];
			# 获取uv*cnt的值
			$value['uvCount'] = $value['uv']*$value['cnt'];
			$resArr2[$value['article_id']] = $value;
			if($value['class_id'] == $classId){
				$resArr2_1['currnetClass'][] = $resArr2[$value['article_id']];
			}else{
				$resArr2_1['otherClass'][] = $resArr2[$value['article_id']];
			}
		}
		#同频道的和非同频道的分开,再合并成同频道的数据放前面，因为同频道的数据优先级更高
		$resArr2 = array_merge($resArr2_1['currnetClass'],$resArr2_1['otherClass']);
		# 将数据重组成以article_id为键的数组
		$resArr2_1 = array();
		foreach($resArr2 as $k=>$v){
			$resArr2_1[$v['article_id']] = $v;
		}
		$resArr2 = $resArr2_1;
		# 排除用于查找相关文章的文章id（也就是排除自己）
		if(isset($resArr2[$docId])) unset($resArr2[$docId]);
		uasort($resArr2, 'cmp_uv_cnt');
		# 排序后生成一个只有docId的数组
		$resArr3 = array();
		foreach($resArr2 as $key=>$value){
			# 合成docId为键，uv*cnt为值的数组
			$resArr3[$key] = $value['uvCount'];
		}
		# 如果多余，则在取出的这么多数据中进行随机取指定的条数
		if($num>$num2){
			//var_dump($num2);exit('751_1');
			return get_from_rand($resArr3,$num2);
		}else{
			$finalNum = count($resArr3);
			if($finalNum > $num2){
				return get_from_rand($resArr3,$num2);
			}else{
				return $resArr3;
			}
		}
	}else{
//		mail('su.hanyu@zol.com.cn','【ZOL首页自"猜你喜欢"查出的数据不是数组】',"get_arti_by_word\r\n".'查出的数据不是数组'.$sql);
		return array();
	}
}

/**
 * 使数组按照uv*cnt的值倒序排列的回调
 */
function cmp_uv_cnt($a, $b) {
	if ($a['uvCount'] == $b['uvCount']) {
		return 0;
	}
	return ($a['uvCount'] > $b['uvCount']) ? -1 : 1;
}

/**
 * 将没有分词的文章id记录入日志，存在mogoDb中
 * @params	int	$msg	记录的article_id
 */
function toLog($msg){
	return false;
	if(!$msg){
		return false;
	}
	$str .= $msg.',';
	$key = 'guess_you_like_logo';
	# 	获取之前的值
	# 从mongo中查看是否有数据
	$getDataArr = ZOL_Api::run("Kv.MongoCenter.get" , array(
	'module'         => 'cms',           		#业务名
	'tbl'            => 'zol_index',     		#表名
	'key'            => $key, 	#key
	));
	if($getDataArr && is_array($getDataArr)){
		$getDataArr[] = $msg;
	}else{
		$getDataArr = array();
		$getDataArr[] = $msg;
	}
	#并且将其存放在mongo缓存中
	ZOL_Api::run("Kv.MongoCenter.set" , array(
	'module'         => 'cms',                    		#业务名
	'tbl'            => 'zol_index',              		#表名
	'key'            => $key, 							#key
	'data'           => $getDataArr,                  	#数据
	'life'           => 7*24*3600,                   	#生命期,3天
	));

	

	return true;
}






