<?php 
/**
 * mongo 的批量insert 和批量select查询操作，用于“猜你喜欢”
 * @auhtor suhy
 * @date 2015-11-14
 */
# 调试模式
// if(1){
// 	ini_set("display_errors", "On");
// 	error_reporting(E_ALL);
// }
// function mongo_insert($daraArr,$timeOut=10000){
// 	$conn = new Mongo('mongodb://othermongodb:30000');
// 	$db = $conn->selectDB('cms');
// 	$collection=$db->selectCollection('guess_you_like_document');
// 	try {
// 		#$collection->insert(array('docId'=>543113,'data'=>array()),true);
// 		$collection->batchInsert($daraArr,array('safe'=>true,'fsync'=>false,'timeout'=>$timeOut));
// 	}catch(MongoCursorException $e){
// 		echo "Can't save the same person twice!\n";
// 	}
// 	return true;
// }

class GuessMongo{
	protected $myCollection = null;
	/**
	 * 构造函数,创建Mongo操作对象，并选择库，选择集合
	 * @param $collection 集合名称
	 */
	public function __construct($collection='guess_you_like_document'){
		$conn = new Mongo('mongodb://othermongodb:30000');
		$db = $conn->selectDB('cms');
		$this->myCollection = $db->selectCollection($collection);
		return $this->myCollection;
	}
	/**
	 * 添加数据到集合
	 */
	public function mongo_insert($daraArr,$timeOut=10000){
		if(!$this->myCollection || !$daraArr)return false;
		try {
			#$collection->insert(array('docId'=>543113,'data'=>array()),true);
			$this->myCollection->batchInsert($daraArr,array('safe'=>true,'fsync'=>false,'timeout'=>$timeOut));
		}catch(MongoCursorException $e){
			echo "Can't save the same person twice!\n";
		}
		return true;
	}
	/**
	 * 查询指定字段值的数据
	 * @param $docIdArr 一组文章id
	 * 形如：array(5455040,5467503...) 
	 */
	public function mongo_select($docIdArr=array()){
		if(!$this->myCollection)return false;
		try {
			$arr = array("docId"=>array('$in'=>$docIdArr));
			$result = $docIdArr ? $this->myCollection->find($arr) : $this->myCollection->find();
		}catch(MongoCursorException $e){
			echo "Can't find the data!\n";
		}
		$dataArr = array();
		if($result){
			foreach($result as $k=>$v){
				if(!isset($v['docId'])) continue;
				$v['docId'] = (int)$v['docId'];
				$dataArr[$v['docId']] = $v['data'];
			}
		}
		return $dataArr;
	}
	
	
	
}# 类结束括号

#$db = new GuessMongo();

#var_dump($db->mongo_select());exit('#78_3#');




