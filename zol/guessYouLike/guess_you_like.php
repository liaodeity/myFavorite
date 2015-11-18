<?php 
if($_COOKIE['S_uid'] != 'suhanyu'){
	//exit();
}
/**
 * ZOL首页“猜你喜欢”项目
 * author by suhy 20150601
 * date 20150601
 * version 3.0
 * copyright  http://www.zol.com.cn/
 * @desc [根据不同用户，返回用户自己习惯点击的哪一类的数据]
 * 1.从雷总那边的redis获取用户浏览习惯的相关频道排名以及文章排名
 * 2.根据每个用户的cookie中的ip_ck不一样来判断不同用户
 * （1）先根据redis中返回的doc_id进行数据库查询得到用户点击的文章id（用户点击过的3篇），根据用户阅读次数来倒序。
 * （2）根据这些文章id，通过对他们的title分词，获取类似的文章。此视为“规则a”。
 * （2）根据这些文章id获取与该文章关联的产品id（注意是产品id）。此视为“规则b”。
 * （3）根据这些文章id，从“看了又看”表中，获取相关的文章推荐。此视为“规则c”。
 * （3）从指定的表中，获取uv倒序的文章，进行推荐。此视为“规则d”。
 * 3.关于命中率：只有“规则d”推的文章，才算是没被命中（vlike=miss）位于get_data_by_hot_v3方法中。
 */

# 获取资源
//include "../../include/public_connect.php";
include('/www/dynamic/html/admin/include/connection.php');
define('ZOL_API_ISFW', false);         #是否使用ZOL新框架，true为使用
require('/www/zdata/Api.php');    #引入入口文件
require '/www/dynamic/html/include/mongoOperate.php';# mongo的操作函数

# 调试模式
if(0){
	ini_set("display_errors", "On");
	error_reporting(E_ALL | E_STRICT);
}
// $db_doc = new DB_Document;
$db_doc = new DB_Document_Read;
//  && !isset($_GET['ip_ck'])
if(!isset($_GET['type'])) { echo '参数错误（error）';exit('[002]'); }

//$default = isset($_GET['type'])?intval($_GET['type']):1;
$ip_key = addslashes($_GET['ip_ck']);
if(!isset($_GET['ip_ck']) || strlen($_GET['ip_ck'])<5) $ip_key = addslashes($_COOKIE['ip_ck']);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$needNum = isset($_GET['needNum']) ? (int)$_GET['needNum'] : 12;
$callback = isset($_GET['callback']) ? addslashes(trim($_GET['callback'])) : '';

//var_dump($_COOKIE['ip_ck']);  http://dynamic.zol.com.cn/channel/mainpage/guess_you_like.php
$redis = new Redis();
// $redis->connect('10.15.187.70', 6380);
// $redis->connect('10.15.187.70', 6380);  //redis_cache_server_1_6380.zoldbs.com.cn  // 10.15.185.118
$redis->connect('10.19.34.18', 6380);
$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);    // use built-in serialize/unserialize

# 从300中随机取36条。
$randNum = 100;

#从数据库中查询数据
if(!$redis->select(1)){
	mail('su.hanyu@zol.com.cn','【ZOL首页自"猜你喜欢"】',"redis服务器链接失败\r\n ".'!$redis->select(1)');
	exit('error[01]');
}

# $ip_key = '58WB5PL2j7QuMDIwMzgxLjE0NDE1MDMyMDQ=';
/* 压测时用到的代码  S */
include('/www/dynamic/html/channel/mainpage/ip_cp_array.php');
// $icNum = count($ipckArr);
$icIndex = mt_rand(0, 4999);
$ip_key = $specialIpCkArr1[$icIndex];
/* 压测时用到的代码 E */


if($ip_key){
	$result = $redis->hGetAll($ip_key);
	//$key = '';
	$fields = 'document_id,title,url,pic_src';
	# 解析从redis过来的数据
	$newArr = array();
	foreach($result as $key=>$value){
		$value = str_replace('(u','(',$value);
		$value = str_replace(array('[(',')]','\'','[',']','\\'),'',$value);
		$arr1 = explode('), (',trim($value,"'"));
		$res1_1 = array();
		foreach($arr1 as $k2=>$v2){
			$res1_1[$k2] = explode(', ',trim($v2,"'"));
		}
		$newArr[$key] = $res1_1;
	}
	# 从redis获得两组数据doc_id和domain  $newArr['article_id'] ; $newArr['domain']
}

# 用于排重
$unArr = array(999999,);
# 获取用户读取的doc_id
$docArr = array();
#var_dump($newArr);exit('95_4');
//$newArr['article_id'] = array(0=>array(0=>5455040),);// testdata
// false 加false 是配合秋月的清表 操作。 by suhy 20151020  配合完毕 20151021 去除 && false
if($newArr['article_id']){
	# 获取3个需要用的docId
	$i = 0;
	foreach($newArr['article_id'] as $key=>$value){
		if(!$value[0]) continue;
		if($i>2) break;
		$docArr[] = (int)$value[0];
		$i++;
	}
	# 根据第一个用户浏览的文章id，取最高优先级的规则的数据.返回形如：
	$resDataAll = get_arti_by_word($docArr,$needNum);
	$res1_0 = isset($resDataAll['article']) ? $resDataAll['article'] : array();
	#var_dump($resDataAll);exit('110_1');
	$num0 = count($res1_0);
	# 记录进入日志  取消
	if($num0 <= 0) {
		// $docStr = implode(',',$docArr);
		// toLog($docStr);
	}
	# 第一优先级取出的数据不足36的时候   $num0 < 12  true
	if($num0 < $needNum){
		# 获取第一优先级的数据的docId
		$res1_0_1 = array();
		# 储存第一优先级的文章id，用于排重
		foreach($res1_0 as $key=>$value){
			$unArr[] = $key;
			$res1_0_1[] = $key;
		}
		# 得到一个文章关联的产品id
		$proIdArr = get_product_id($docArr);
		#var_dump($proIdArr);exit('128_1');
		# 没有proId则使用“看了又看”   $proId
		if($proIdArr){
			# 返回形如array(0=>5255718,1=>5255719...)
			$docArr1_1 = get_docid_by_proid($proIdArr,$needNum-$num0,$unArr);
			//var_dump($docArr1_1);exit();
			# 通过proId获得了文章id
			if(is_array($docArr1_1)){
				# 第一优先级和第二优先级的规则的数据合并
				$docArr1_1 = array_merge($res1_0_1,$docArr1_1);
				
			}else{
				$docArr1_1 = $res1_0_1;
			}
			//var_dump($docArr1_1);exit('144_1'); // array(0=>5255718,...)
			# 用于排重
			foreach($docArr1_1 as $key=>$value){
				if(array_search($unArr, $value))
					$unArr[] = $value;
			}
			
			$docArr1_2 = array_values($docArr1_1);
		}else{
			# 没有产品id时的情况
			//var_dump($docArr1_2); exit('163_3');
			$docArr1_2 = is_array($res1_0) ? array_keys($res1_0) : array();
		}
	}else{
		# 第一优先级规则取出的数据足够36条
		$docArr1_2 = is_array($res1_0) ? array_keys($res1_0) : array();
	}
	if(is_array($docArr1_2)) $num = count($docArr1_2);
	#var_dump($docArr1_2);exit('#159_1#');
	//$num < 36
	if($num < $needNum){
		$lookDocIdArr = get_docid_by_lookmore($docArr,$needNum-$num,$unArr);
		//$lookDocIdArr = array_keys($lookMoreDocId);
		$docArr1_2 = array_merge($docArr1_2,$lookDocIdArr);
		//var_dump($lookDocIdArr,$docArr1_2);exit('#164_3#');
		# 先对第一优先级规则的docId，获取title等相关文章信息
		$fResult_1 = array();
		if($num >= 0){
			#var_dump($docArr1_2);exit('#169#');
			# part1
			$fResult_2 = get_data_by_docid($docArr1_2);// array(5255718=>array('document_id'=>....))
			# 按照原始的id顺序 排回
			foreach($docArr1_2 as $key=>$value){
				$fResult_1[$value] = $fResult_2[$value];
			}
		}
		# part2   lookmore中取出的docId不足，使用C计划补充
		$resDocArr2 = count($docArr1_2) < $needNum ? get_data_by_hot_v3($randNum,$needNum-$num,$docArr1_2,true) : array();
		
		$fResult = array_merge($fResult_1,$resDocArr2);

	}else{
		$resDocArr = array_values($docArr1_2);
		$fResult_1 = get_data_by_docid($resDocArr);
		#将返回的数组，用其对应的docId作为键
		$fResult = array();
		foreach($fResult_1 as $key=>$value){
			$value['document_id'] = isset($value['document_id']) ? $value['document_id'] : $value['docId'];
			$fResult[$value['document_id']] = $value;
		}
		# 按照原始的id顺序 排回
		$fResult_1 = array();
		foreach($resDocArr as $key=>$value){
			$fResult_1[$value] = $fResult[$value];
		}
		$fResult = $fResult_1;
	}
}elseif($ip_key){
	# 没有查询到redis中的doc_id数据
	$fResult =  get_uv_data_from_mongo();
}else{
	# 如果连ip_ck都不存在，使用C计划获取数据
	$fResult =  get_uv_data_from_mongo();
}



$idArr = array();
$fResultNew = array();
foreach($fResult as $key=>$value){
	$idArr[] = $value['document_id'];

	$fResultNew[$value['document_id']]['url'] = $value['url'];
	$fResultNew[$value['document_id']]['title'] = $value['title'];
	$fResultNew[$value['document_id']]['pic_src'] = $value['pic_src'];
}
$idStr = implode(',',$idArr);
$sql = 'SELECT document_id,short_title from document_index_title where document_id in('.$idStr.')';
$res2_1 = $db_guess->get_results($sql,'A');
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
		if($key > $needNum) break;
		$value = $res2_1[$v_0];
		$value['document_id'] = $v_0;
		if(isset($res2_1[$v_0])){
			$value = $res2_1[$v_0];
		}else{
			$value['short_title'] = $fResultNew[$value['document_id']]['title'];
		}
		$value['short_title'] = strlen($value['short_title']) > 10 ? $value['short_title'] : $fResultNew[$value['document_id']]['title'];
		
		$fResultNew[$value['document_id']]['short_title'] = $value['short_title'];
		
		$fResult[$i] = $fResultNew[$value['document_id']];
		$fResult[$i]['document_id'] = (int)$value['document_id'];
		
		$i++;
	}
}

# 将将指定key的位置数据替换成论坛数据 (不包括key为0的图片位置)
$keyPositionArr = array(1,2,3,5,6,7,9,10,11); 
# 随机获取若干个文字链的位置
$getPositionKey = array_rand($keyPositionArr,4);
$getPositionArr = array();
foreach($getPositionKey as $k=>$v){
	$getPositionArr[] = $keyPositionArr[$v];
}
$i = 0;
foreach($getPositionArr as $key=>$value){
	if(!$value || $i>3) break;
	if(isset($resDataAll['bbs'][$i]) && !empty($resDataAll['bbs'][$i])){
		$bbsValue = $resDataAll['bbs'][$i];
		$fResult[$value]['url'] = $bbsValue['bbsUrl'].'?dataFrom=bbs';
		if(strpos($bbsValue['title'], '】') == false && $bbsValue['page_type_id']<4){
			$fResult[$value]['title'] = '【贴】'.$bbsValue['title'];
			$fResult[$value]['short_title'] = '【贴】'.$bbsValue['title'];
		}else{
			$fResult[$value]['title'] = $bbsValue['title'];
			$fResult[$value]['short_title'] = $bbsValue['title'];
		}
	}else{
		continue;
	}
	$i++;
}

# 记录文章的展现次数 ,只记录1tab（1屏）的
$docArr = array();$i = 1;
foreach($fResult as $k=>$v){
	if($i > 12) break;
	if(in_array($k,$getPositionArr)) continue;
	$docArr[] = $v['document_id'];
	$i++;
}

// recordArticle($docArr);
# 数据条数
$finalNum = count($fResult);

$fResult[0]['dataSource'] = 'zol';
$fResult[0]['dataPageMax'] = $finalNum/12;
$fResult[0]['dataDebug'] = $docArr[0];


$msg = '数据不足'.$needNum.'条';
if($finalNum < $needNum){ 
	//mail('su.hanyu@zol.com.cn','【ZOL首页"猜你喜欢"数据故障】',date('Y-m-d H:i:s ==> ').$msg."\r\n".__FILE__.__LINE__); 
	toLog(array($ip_key=>count($fResult),));
}

$fResult = api_json_encode($fResult);
$j = json_encode($fResult);
echo $callback ? $callback.'('.$j.')' : $j;
return '';
exit('');


/*********************************以下是一些需要用到的函数**********************************************************/
# 只从C计划获取数据
function get_data_by_hot($num=36){
	global $db_doc;
	$fields='document_id,title,url,pic_src';
	$sql = 'select '.$fields.' from article_monitor_all where publish_time>"'.date('Y-m-d',strtotime('-3 days')).'" AND is_del=0 group by document_id order by score_time_position desc limit '.$num;
	$result2 = $db_doc -> get_results($sql,'A');
	if($result2){
		foreach($result2 as $key=>$value){
			$result2[$key]['url'] = $value['url'].'?vlike=miss';
		}
	}
	
	return $result2;
}

# 根据一串doc_id获取需要的数据
function get_data_by_docid($idArr,$fields='document_id,title,class_id'){
	global $db_doc;
	# 从mongo缓存中获取数据
	$dataArr1 = get_data_by_zol_api($idArr);
	#count($idArr) = count($dataArr1);
	$newIdArr = array();
	foreach ($idArr as $k=>$v){
		if(array_key_exists($v,$dataArr1)) continue;
		$newIdArr[] = $v;
	}
	$idArr = $newIdArr;
	$result = array();
	if($idArr){
		// 需要获得的字段：document_id,title,url,pic_src
		$fields='document_id,title,class_id,date';
		$idStr = trim(implode(',',$idArr),',');
		$sql = 'select '.$fields.' from doc_index where document_id in ('.$idStr.') order by date desc';
		$result = $db_doc -> get_results($sql,'A');
	}
	$newResultTotal = $newResult = $dataBox = array();
	if($result){
		foreach($result as $key=>$value){
			$docIdKey = (int)$value['document_id'];
			$newResult[$docIdKey]['document_id'] = (int)$value['document_id'];
			$newResult[$docIdKey]['docId'] = $newResult[$docIdKey]['document_id'];
			$newResult[$docIdKey]['title'] = $value['title'];
			# 获取文章地址
			$newResult[$docIdKey]['url'] = 'http://dynamic.zol.com.cn/channel/mainpage/get_doc_url.php?docId='.$value['document_id'];
			$newResult[$docIdKey]['pic_src'] = ZOL_Api::run("Article.Function.getThumbPic" , array(
					'docId'          => $value['document_id'],         #文章ID
					'width'          => 150,             #宽度
					'height'         => 104,             #高度
					'default'        => 1,               #无图时返回占位图
			));
			$newResultTotal[] = $newResult[$docIdKey];
		}
		# 如果有经过私有云查询的数据，则将其放入mongo缓存的集合中 
		insert_data_by_zol_api($newResultTotal,3600);
	}
	$newResult += $dataArr1;
	
	return $newResult;
}

/** 
 * 从取100条($randNum)结果中条数据，再从中随机36条，按uv倒序返回。  by suhy 20150616
 * @params array $array 传入所有的结果集，从中取100条，再此100条中随机取36条,形如： array(36) {[5255718]=>int(819)[5264685]=>int(593)
 * @params int	$getNum 从给定的数组中，随机取多少条，默认取36条。
 * @return 返回形如： array(36) {[5255718]=>int(819)[5264685]=>int(593)...
 */
function get_from_rand($array,$getNum=36){
	global $randNum;
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

# 使用一期规则，获取100篇分值倒序文章，再从中随机取36条，按分值倒序返回。 by suhy 20150616
function get_data_by_hot_v2($num=100,$getNum=36){
	global $db_doc;
	$fields='document_id,title,url,pic_src,score_time_position';
	$sql = 'select '.$fields.' from article_monitor_all where publish_time>"'.date('Y-m-d',strtotime('-3 days')).'" AND is_del=0 group by document_id order by score_time_position desc limit '.$num;
	$result2 = $db_doc -> get_results($sql,'A');
	
	if($result2){
		$res1 = array_rand($result2,$getNum);
		if($res1){
			$newArray1 = array();
			foreach($res1 as $key=>$value){
				$newArray1[] = $result2[$value];
			}
			$newArray2 = array();
			$newArray3 = array();
			foreach($newArray1 as $key=>$value){
				$newArray2[$value['document_id']] = $value['score_time_position'];
				$newArray3[$value['document_id']] = $value;
			}
			arsort($newArray2);
			$newArray4 = array();
			foreach($newArray2 as $key=>$value){
				$newArray4[$key] = $newArray3[$key];
			}
		}
		
	}
	
	if($newArray4){
		foreach($newArray4 as $key=>$value){
			$newArray4[$key]['url'] = $value['url'].'?vlike=miss';
		}
	}
	
	return $newArray4;
}
# 通过一组文章id获取产品id 返回的是数组
function get_product_id($docArr){
	if(!is_array($docArr))return false;
	$proIdArr = array();
	foreach($docArr as $key=>$value){
		$dataArr = ZOL_Api::run("Article.Doc.getDocPro" , array(
				'docId'          => $value,         #文章id
		));
		$proId = (int)$dataArr[0]['proId'];
		if(!$proId) continue;
		$proIdArr[] = $proId;
		//if($proId > 0) break;
		//if($key > 0) break;
	}
	//if(!isset($proId) || empty($proId)) $proId = 0;
	
	return $proIdArr;
}

# 跟据产品id，获取相关的文章id
function get_docid_by_proid($proId,$num=36,$unArr=array()){
	if(!$proId) return array();
	global $db_doc;
	if(is_array($proId)) $proIdStr = implode(',',$proId);
	# 无论如何取36条，因为需要排重，以防不够
// 	$dataArr = ZOL_Api::run("Article.Doc.getListByPro" , array(
// 			'docType'        => '1,2,3,4,5,6',   #文章类型 1.新闻 2.行情 3.评测 4.非导购 5.导购 6.非行情&导购（评测 新闻） 7.非行情 （导购 评测 新闻)
// 			'proId'          => $proIdStr,          #产品IDS    //$proId
// 			'hourbtn'        => 720,             #多少小时以内的文章   一个月以内的
// 			'num'            => 36,              #数量
// 			'rtnCols'        => '*(document_id)',
// 	));
	# 获取产品id，取近3个月的数据
	$sql = 'select document_id from document_index_hardware doc where `date` < "'.date('Y-m-d H:i:s').'" and doc_type_id in (1,2,3,4,5,6) and hardware_id in('.$proIdStr.') and doc.date > "'.date('Y-m-d H:i:s',time()-(2160*3600)).'" limit 36';
	$dataArr = $db_doc->get_results($sql);
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
	global $db_guess;
	$docStr = implode(',',$docArr);
	# 数据库操作对象 $db_guess
	$sql = 'select * from z_lookmore where doc_id in('.$docStr.') limit 100';
	$res1 = $db_guess->get_results($sql,'O');
	#var_dump($docStr,$res1);exit('506_4');
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
			//if(is_array($arrayArr) && count($arrayArr) >= 36) break;
		}
		#var_dump($arrayArr);exit('#576_1#');
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
		#var_dump($resArr1);
		if(is_array($resArr1)){
			$num = count($resArr1);
		}
		#$resArr2 = get_from_rand($resArr1,$paramNum);
		$resArr = array();
		$i = 1;
		foreach($resArr1 as $k=>$v){
			if($i>$paramNum) break;
			$resArr[] = $k;
			$i++;
		}
		#var_dump($resArr);exit('#601-3#');
		
		return $resArr;
	}else{
		// lookmore中没有数据  返回空数组
		return $resArr;
	}
	
	# 以下作废
	/* if($num > 36){
		$paramNum =  $paramNum > $num ? $num : $paramNum;
		return array_slice($resArr1,0,$paramNum,true);
	}else{
		$msg = '报警信息:猜你喜欢的“看了又看”的数据还不足36';
		mail('su.hanyu@zol.com.cn','【ZOL首页猜你喜欢-数据故障】',date("Y-m-d H:i:s ==> \r\n").$msg."\r\n\r\n");
	} */
	
}

/**
 *  通过访问的文章uv推文章
 *  @params int 	$num 		从多少条数据中随机取数据。例如从100条数据中随机取数据。
 *  @parmas int 	$getNum 	返回的数据条数的数量，例如需要补充21条数据，此时传21 就好了
 *  @params array 	$unDocArr 	查询数据，需要排除进行的文章id集合，一维数组。
 * 	@params boolean	$is_miss 	用于标记是否是真正需要推的数据，true表示错过，不是真正要推的
 */
function get_data_by_hot_v3($num=100,$getNum=36,$unDocArr=array(),$is_miss=true){
	global $db_guess;
	# 对一部分排重
	if($unDocArr){
		$unStr = implode(',',$unDocArr);
		$whereStr = 'where docId not in('.$unStr.') ';
	}else{
		$whereStr = '';
	}
	$sql = 'SELECT docId,uv from article_monitor_v2 '.$whereStr.' ORDER BY uv desc limit '.$num;
	$result2 = $db_guess->get_results($sql);
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
			# 如果标记了不是命中推送的话，就加上url参数标记
			if($is_miss){
				foreach($dataArr as $key=>$value){
					$dataArr[$key]['url'] .= '&vlike=6';
				}
			}
			# 将含有文章信息的数据用其docId作为键
			$newArray3 = array();
			foreach($dataArr as $key=>$value){
				$newArray3[$value['document_id']] = $value;
			}
			//var_dump($newArray3);exit('680_1');
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
 * return 返回以带有文章数据和论坛数据的数组,  array('article'=>array(5315078=>43,...),'bbs'=>array())
 */
function get_arti_by_word($docIdArr,$num=12){
	if(!$docIdArr) return false;
	global $db_guess,$randNum,$needNum;
	//$sql = 'SELECT t.article_id,t.uv,t.word,t2.word as w,count(t.article_id) as cnt from tongji_article_title_words t LEFT JOIN (SELECT word from tongji_article_title_words where article_id='.$docId.') 
	//as t2 on t.word=t2.word where t2.word is not null GROUP BY t.article_id ORDER BY uv desc limit '.$randNum;
	$docIdStr = $docIdArr;
	if(is_array($docIdArr)) {
		$docIdStr = implode(',',$docIdArr);
		$docIdStr = 'article_id in ('.$docIdStr.')';
	}else{
		$docIdStr = 'article_id='.$docIdStr;
	}
	
	# 将小结果集放前边（小结果集驱动大结果集）
	$sql = '(SELECT t.article_id,t.uv,t.word,t.title,t.page_type_id,t2.word as w,count(*) as cnt,t.bbsUrl,t.flag from (SELECT word from tongji_article_title_words where '.$docIdStr.') as t2 LEFT JOIN  tongji_article_title_words t
on t.word=t2.word where t2.word is not null AND t.bbsUrl="" GROUP BY t.article_id ORDER BY uv desc limit 20)UNION (SELECT t.article_id,t.uv,t.word,t.title,t.page_type_id,t2.word as w,count(*) as cnt,t.bbsUrl,t.flag from (SELECT word from tongji_article_title_words where '.$docIdStr.') as t2 LEFT JOIN  tongji_article_title_words t
on t.word=t2.word where t2.word is not null AND t.bbsUrl<>"" AND page_type_id<>4 GROUP BY t.bbsUrl ORDER BY uv desc limit 4)';
	# $sql = 'SELECT t.article_id,t.id,t.uv,t.word,t2.word as w,count(*) as cnt,t.bbsUrl,t.flag from (SELECT word from tongji_article_title_words where article_id='.$docId.') as t2 LEFT JOIN  tongji_article_title_words t on t.word=t2.word where t2.word is not null GROUP BY t.article_id ORDER BY uv desc limit '.$randNum;
// 	$sql = '(SELECT t.article_id,t.uv,t.word,t.title,t.page_type_id,t2.word as w,count(*) as cnt,t.bbsUrl,t.flag from (SELECT word from tongji_article_title_words where article_id=5455040) as t2 LEFT JOIN  tongji_article_title_words t
//  on t.word=t2.word where t2.word is not null AND t.bbsUrl="" GROUP BY t.article_id ORDER BY uv desc limit 50)UNION (SELECT t.article_id,t.uv,t.word,t.title,t.page_type_id,t2.word as w,count(*) as cnt,t.bbsUrl,t.flag from (SELECT word from tongji_article_title_words where article_id=5455040) as t2 LEFT JOIN  tongji_article_title_words t
//  on t.word=t2.word where t2.word is not null AND t.bbsUrl<>"" AND page_type_id<>4 GROUP BY t.bbsUrl ORDER BY uv desc limit 4)';
	//exit('793_2');
	$resArr1 = $db_guess->get_results($sql);
	# 文章属性，优先展示第一类属性
	$propertyArr1 = array('nproduct','nmanu','nsubcat','eng','nproperty','ntype','nbooktitle');
	$propertyArr2 = array('n','nr','nz');
	if(is_array($resArr1)){
		$bbsData = array();
		$articleData = array();
		# 先将论坛数据和文章数据进行剥离
		$i = 0;
		foreach($resArr1 as $key=>$value){
			# 只需要取4条论坛数据
			if($value['bbsUrl']){
				if($i > 4) continue;
				$bbsData[] = $value;
				$i++;
			}else{
				$articleData[] = $value;
			}
		}
		$resArr1 = $articleData;
		$num = count($resArr1);
		$resArr2 = $resArr2_1 = array();
		$value0 = array();
		foreach($resArr1 as $key=>$value){
			# 将docId和uv等值转为整形
			$value0['article_id'] = (int)$value['article_id'];
			$value0['uv'] = (int)$value['uv'];
			$value0['cnt'] = (int)$value['cnt'];
			# 获取uv*cnt的值
			$value0['uvCount'] = $value['uv']*$value['cnt'];
			
			$resArr2[$value0['article_id']] = $value0;
			$resArr2_1[$value['flag']][] = $value0;
		}
		# 排除用于查找相关文章的文章id
		if(isset($docId) && isset($resArr2[$docId])) unset($resArr2[$docId]);
		// uasort($resArr2, 'cmp_uv_cnt');
		# 排序后生成一个只有docId的数组
		$resArr3 = array();
		$resArr3_1 = array();
		$resArr3_2 = array();
		$newArr2 = array();
		foreach($propertyArr1 as $v){
			if(!isset($resArr2_1[$v])) continue;
			$newArr2 = array_merge($newArr2,$resArr2_1[$v]);
		}
		$resArr3 = $newArr2;
		//var_dump($resArr2);
		/*
		foreach($resArr2 as $key=>$value){
			# 合成docId为键，uv*cnt为值的数组
			// $resArr3[$key] = $value['uvCount'];
			if(in_array($value['flag'],$propertyArr1)){
				$resArr3_1[$key] = $value['uvCount'];
			}else{
				$resArr3_2[$key] = $value['uvCount'];
			}
		}
		# 合并
		$resArr3 = $resArr3_1 + $resArr3_2;
		var_export($resArr3);//exit('804_2');*/
		//var_dump($resArr3);exit('#788_1#');
		# 如果多于36条，则将其分成3组，每组在内部随机顺序
		if($num >= $needNum){
			$newArr3 = array();
			# 一共3组，分别进行各自的随机排序，再组合
			//var_dump($resArr3);exit('807_2');
			$resArr3 = array_chunk($resArr3,12);
			foreach($resArr3 as $key=>$valueArr){
				if($key > 2) break;
				shuffle($valueArr);
				$resArr3_1 = $valueArr;
				//var_dump($resArr3_1);exit('816_1');
				$newArr3 += $resArr3_1;
				//var_dump($newArr3);exit('816_1');
			}
			$newArr2 = array();
			# 将文章的返回数据 转成以文章id为键的数组
			foreach ($newArr3 as $k=>$v){
				$newArr2[$v['article_id']] = $v;
			}
			//var_dump($newArr3);exit('814_1');
			return array('article'=>$newArr2,'bbs'=>$bbsData);
			//return get_from_rand($resArr3);
		}else{
			# 如果不足36条，也将其打乱顺序返回
			$resArr3 = array_flip($resArr3);
			shuffle($resArr3);
			//exit('821_1');
			return array('article'=>array_flip($resArr3),'bbs'=>$bbsData);
		}
	}else{
		//mail('su.hanyu@zol.com.cn','【ZOL首页自"猜你喜欢"查出的数据不是数组】',"get_arti_by_word\r\n".'查出的数据不是数组'.$sql);
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
 * @params	string	$file	记录article_id的文件地址，请传入绝对路径的地址
 */
function toLog($msg){
	return false;
	if(!$msg){ return false; }
	$key = 'guess_you_like_log2015';
	$time = time();
	# 	获取之前的值
	# 从mongo中查看是否有数据
	$getDataArr = ZOL_Api::run("Kv.MongoCenter.get" , array(
			'module'         => 'cms',           		#业务名
			'tbl'            => 'zol_index',     		#表名
			'key'            => $key, 	#key
	));
	if($getDataArr && is_array($getDataArr)){
		$num = count($getDataArr);
		if($num > 5000)return false;
		foreach($msg as $k=>$v){
			if(array_key_exists($k, $getDataArr)){
				$getDataArr[$k][$time] = $v;
			}else{
				$eleData = array();
				$eleData[$time] = $v;
				$getDataArr[$k] = $eleData;
			}
		}
		
	}else{
		$getDataArr = array();
		foreach($msg as $k=>$v){
			if(array_key_exists($k, $getDataArr)){
				$getDataArr[$k][$time] = $v;
			}else{
				$eleData = array();
				$eleData[$time] = $v;
				$getDataArr[$k] = $eleData;
			}
		}
	}
	#并且将其存放在mongo缓存中
	ZOL_Api::run("Kv.MongoCenter.set" , array(
		'module'         => 'cms',                    		#业务名
		'tbl'            => 'zol_index',              		#表名
		'key'            => $key, 							#key
		'data'           => $getDataArr,                  	#数据
		'life'           => 7*24*3600,                   	#生命期,7天
	));
	
	return true;
}

/**
 * 将<=36个文章id，记录下来
 * @changelog 20150821
 */
function recordArticle($array){
	if(!is_array($array)) return false;
	# 限定条数，超过600条，使用另外一个键的数组
	$threshold = 600;
	$timeStamp = $time = time();
	$i = date('H')%2 == 0 ? 1 : 2;
	$recordKey = 'guess_you_like_record_'.$i;
	# 从mongo中查看是否有数据
	$getDataArrTotal = ZOL_Api::run("Kv.MongoCenter.get" , array(
		'module'         => 'cms',           		#业务名
		'tbl'            => 'zol_index',     		#表名
		'key'            => $recordKey, 	#key
	));
	if(!isset($getDataArrTotal['lock'])){
		$getDataArrTotal = array();
		$getDataArrTotal['data'] = array();
		$getDataArrTotal['data']['show'] = array();
		$getDataArrTotal['data']['click'] = array();
		$getDataArrTotal['date'] = $timeStamp;
		$getDataArrTotal['lock'] = false;
		$getDataArrTotal['num'] = 0;
		$getDataArrTotal['status'] = true;
		$getDataArrTotal['key'] = $recordKey;
	}
	if($getDataArrTotal['lock'] == true){
		$i = date('H')%2 == 0 ? 2 : 1;
		$recordKey = 'guess_you_like_record_'.$i;
		# 从mongo中查看是否有数据
		$getDataArrTotal = ZOL_Api::run("Kv.MongoCenter.get" , array(
			'module'         => 'cms',           		#业务名
			'tbl'            => 'zol_index',     		#表名
			'key'            => $recordKey, 	#key
		));
		if(!isset($getDataArrTotal['lock'])){
			$getDataArrTotal['data'] = array();
			$getDataArrTotal['data']['show'] = array();
			$getDataArrTotal['data']['click'] = array();
			$getDataArrTotal['date'] = $time;
			$getDataArrTotal['lock'] = false;
			$getDataArrTotal['num'] = 0;
			$getDataArrTotal['status'] = true;
			$getDataArrTotal['key'] = $recordKey;
		}
	}
	$getDataArr = $getDataArrTotal['data']['show'];
	
	if($getDataArr && is_array($getDataArr)){
		foreach($array as $k=>$v){
			if(array_key_exists($v,$getDataArr)){
				$getDataArr[$v] = (int)$getDataArr[$v];
				$getDataArr[$v]++;
			}else{
				if($v > 0)
					$getDataArr[$v] = 1;
			}
		}
	}else{
		$getDataArr = array();
		foreach($array as $k=>$v){
			$getDataArr[$v] = 1;
		}
	}
	
	$getDataArrTotal['data']['show'] = $getDataArr;
	$getDataArrTotal['num'] = count($getDataArr);
	// 引入时间是为了解决冲突
	$getDataArrTotal['date'] = time();
	$getDataArrTotal['status'] = $getDataArrTotal['num'] >= $threshold ? false : true;
	
	#并且将其存放在mongo缓存中
	ZOL_Api::run("Kv.MongoCenter.set" , array(
		'module'         => 'cms',                    		#业务名
		'tbl'            => 'zol_index',              		#表名
		'key'            => $recordKey, 					#key
		'data'           => $getDataArrTotal,               #数据
		'life'           => 7*24*3600,                   	#生命期,3天
	));

	return true;
}
/**
 * 通过文章id，循环着从mongo中取缓存数据 20151118
 */
function get_data_by_zol_api($docIdArr){
	if(!$docIdArr)return false;
	$cacheKey = 'Guess_you_like:document_id:';
	$dataTotal = array();
	foreach($docIdArr as $k=>$v){
		$mDataArr = ZOL_Api::run("Kv.MongoCenter.get" , array(
				'module'         => 'cms',        #业务名
				'key'            => $cacheKey.$v,    #key
		));
		if($mDataArr){
			$dataTotal[$mDataArr['docId']] = $mDataArr;
			
		}else{
			continue;
		}
	}
	return $dataTotal;
}
/**
 * 
 */
function insert_data_by_zol_api($dataArr,$time=7200){
	if(!$dataArr)return false;
	$cacheKey = 'Guess_you_like:document_id:';
	foreach($dataArr as $k=>$v){
		#写入缓存
		ZOL_Api::run("Kv.MongoCenter.set" , array(
			'module'         => 'cms',            		#业务名
			'key'            => $cacheKey.$v['docId'],        		#key
			'data'           => $v,  				#数据
			'life'           => $time,            		#生命期   1*24*3600
		));
		//echo $cacheKey.$v['docId']."\r\n";
	}
	return true;
}
/**
 * 如果没有ip_ck或者redis中无记录，则调用补救规则，补救规则使用mongo缓存
 */
function get_uv_data_from_mongo(){
	global $randNum,$needNum;
	$mDataArr = $data = array();
	#缓存key
	$cacheKey = 'Guess_you_like:get_data_by_hot_v3';
	$mDataArr = ZOL_Api::run("Kv.MongoCenter.get" , array(
			'module'         => 'cms',        #业务名
			'key'            => $cacheKey,    #key
	));
	if(!$mDataArr){
		$data = get_data_by_hot_v3($randNum,$needNum,array(),true);
		#写入缓存
		ZOL_Api::run("Kv.MongoCenter.set" , array(
			'module'         => 'cms',            		#业务名
			'key'            => $cacheKey,        		#key
			'data'           => $data,  				#数据
			'life'           => 86400,            		#生命期   1*24*3600
		));
	}else{
		$data = $mDataArr;
	}
	shuffle($data);
	
	return $data;
}



