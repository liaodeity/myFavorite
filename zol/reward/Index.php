<?php
/**
 * 接受请求，并处理
 * @author suhy
 * @date 2015-12-17
 */
#header('Content-type:text/html; Charset=UTF-8');
class Reward_Page_Index extends Reward_Page_Abstract
{
	public function validate(ZOL_Request $input, ZOL_Response $output)
	{
		#Libs_Global_PageHtml::setExpires(3600);  #设置缓存时间
        if (!parent::baseValidate($input, $output)) {
        	return false;
		}
		$hasToken = $input->request('_auth_token_str');
		if(isset($hasToken)){
			$output->_token 		= $input->request('_auth_token_str');
			$output->userId 		= $input->request('userid') ? $input->request('userid') : 'guest'; # 进行打赏的用户，可能为空
			$output->articleId 		= $input->request('flag_id_1') ? $input->request('flag_id_1') : 0;# 文章id/帖子id
			$output->ext1 			= $input->request('flag_id_2') ? $input->request('flag_id_2') : 0;# 版块id/其他
			$output->ext2 			= $input->request('flag_id_3') ? $input->request('flag_id_3') : 0;# bbsid/其他
			$output->orderName 		= $input->request('title');# 页面标题
			$output->url		 	= $input->request('url');
			$money 					= $input->request('money');
			$output->money 			= $input->request('money') ? $money*100 : 1;# 以“分”为单位
			$output->message 		= $input->request('message') ? $input->request('message') : '打赏文章id为'.$output->articleId.'的作者';
			$output->payment 		= $input->request('payment');
			$output->payee			= $input->request('payee');#收款人
			$output->serviceType	= $output->ext1>0 && $output->ext2>0 ? 2 : 1;# 1资讯文章打赏 2.论坛（开发中。。）
		}

		return true;
	}
	/**
	 * 接收表单 生成订单,并显示支付二维码
	 * @param ZOL_Request $input
	 * @param ZOL_Response $output
	 */
    public function doDefault(ZOL_Request $input, ZOL_Response $output){
    	#队列异步回调的url的域名部分
    	$baseUrl = 'http://reward.suhy.test.zol.com.cn';
    	#支付的baseurl
    	$payBaseUrl = 'http://10.19.38.115:8990';
    	# 报错模板的html
    	$errorHtml = $output->fetchCol('ShowPayError');
    	# token验证
    	if(!Helper_Reward_RewardFunc::verityRewardToken($input,$output)){
    		# 显示报错模板
    		$errorArr  = array(
	    		'flag'		=> 'error',
	    		'msg'		=> '请不要重复提交！',
	    		'errorCode'	=> 8001,
    			'toLog'		=>1,
    			'showHtml'  =>$errorHtml,
    		);
    		Helper_Reward_RewardFunc::ajaxExit($errorArr);
    	}else{
    		# 重置该用户的token
    	    //验证的时候就已经重置了
    	}
    	if(!$output->url || !$output->money || !$output->articleId){
    		# 显示报错模板
    		Helper_Reward_RewardFunc::ajaxExit(array(
    			'flag'		=> 'error',
    			'msg'		=> '金额不合法或缺少文章id',
    			'showHtml'  =>$errorHtml,
    		));
    	}
    	# 文章页的话的收款人是文章作者，其他业务的不同
        if($output->ext1 <= 0){
        	$output->payee 		= Helper_Reward_RewardFunc::getArticleAuthor(array('articleId'=>$output->articleId,));
        }else{
        	
        }
        $output->orderNo 		= Helper_Reward_RewardFunc::getOrderNumber();
        $output->userIp			= ZOL_Api::run("Service.Area.getClientIp" , array());
        /*
    	# 订单入库
    	$id = Helper_Reward_RewardModel::insertData(array(
    			'order_name'	=> $output->orderName,
    			'order_number'	=> $output->orderNo,
    			'article_id' 	=> $output->articleId,
    			'url'			=> $output->url,
    			'insert_date'  	=> SYSTEM_DATE,
    			'pay_time'		=> NULL,
    			'custmer'  		=> $output->userId,
    			'money'    		=> $output->money,
    			'message'		=> $output->message,
    			'payment'  		=> $output->payment,
    			'order_status'  => 0,
    			'payee'   		=> $output->payee,#
    			'service_type'  => 1,# 1资讯文章打赏 2.论坛（暂不支持）
    			#'debug'			=> 1,
    	));*/
        
        # 消息体/数据体
        $putQueueBody = array(
    			'order_name'	=> $output->orderName,
    			'order_number'	=> $output->orderNo,
    			'article_id' 	=> $output->articleId,
    			'ext1'			=> $output->ext1,
    			'ext2'			=> $output->ext2,
    			'url'			=> $output->url,
    			'insert_date'  	=> SYSTEM_DATE,
    			'pay_time'		=> NULL,
    			'custmer'  		=> $output->userId,
    			'money'    		=> $output->money,
    			'message'		=> $output->message,
    			'payment'  		=> $output->payment,# 支付方式
    			'order_status'  => 0,
    			'payee'   		=> $output->payee,# 收款者/文章作者
    			'service_type'  => $output->serviceType,# 1资讯文章打赏 2.论坛（开发中。。）
    			#'debug'			=> 1,
    	);
        $putQueueData = array(
        		'queue' 		=> 'ArticleReward',
        		'producer'		=> iconv('GB2312', 'UTF-8//IGNORE','媒体平台-打赏'),
        		'consumerUrls'	=>'http://reward.suhy.test.zol.com.cn/?c=Queue_DealQueue&a=DealQueueOrder',#c=Queue_DealQueue&a=DealQueueOrder#c=Index&a=DealQueueOrder
        		'msgType'		=>'json',
        		'charset'		=>'utf-8',
        		'priority'		=> 0,
        		'persistent'	=> true,
        		'version'		=>'1.0',
        		'content'		=>iconv('GBK','UTF-8//IGNORE',$output->message),#
        		'flag'			=>1,
        		'id'			=>123,
        		'body'			=>api_json_encode($putQueueBody),# 转成utf-8
        );
        #var_dump($putQueueData);exit();
    	# 将订单数据推入队列，进行异步插入数据库
    	$result = Libs_Reward_CurlRequest::curlPostMethod(array(
    			'url'		=> 'http://10.19.38.115:8990/queue/putmessage?queuename=ArticleReward&token=8951f7339caee44b23383cdded9a3d2b66',
    			'pull_data'	=> json_encode($putQueueData),
    			'data_type'	=>'json',
    	));
    	$result = json_decode($result,true);
    	# 如果800，则入队成功
    	if($result['resultCode']!=800){
    		Helper_Reward_RewardFunc::ajaxExit(array(
	    		'flag'		=> 'error',
	    		'errorCode'	=> 8002,
	    		'queueErrorCode'=>$result['resultCode'],
	    		'msg'		=> '[8002]生成订单失败！,加入队列失败！',
	    		'toLog'		=>1,
	    		'showHtml'  =>$errorHtml,
    		));
    	}
    	# 此时已经是“分”作为单位
        $moneyCents = $output->money;
        # 默认支付宝
    	$payType = 'alipay'; 
    	switch ($output->payment){
    		case 1: $payType = 'alipay'; break;
    		case 2: $payType = 'wxpay'; break;
    	}
    	$notifyUrl = 'http://reward.suhy.test.zol.com.cn/?c=Queue_DealQueue&amp;a=DealPayNotice&amp;sign=';
    	$callbackUrl = 'http://reward.suhy.test.zol.com.cn/?c=FormHtml&amp;a=ShowPaySuccess';
    	# 处理支付事宜,给支付网关发送xml
    	$xmlStr = Libs_Reward_SimpleXML::getXmlStr(array(
    		'root_node'			=>'xml',
	    	'data_array'		=>array(
				'partnerId'		=>101,
				'userId'		=>$output->userId,
				'orderId'		=>$output->orderNo,
				'goodsDesc'		=>$output->message,
				'payType'		=>$payType,
				'goodsName'		=>$output->orderName,
				'goodsUrl'		=>$output->url, # $output->url
				'fee'			=>$moneyCents, # $output->money
				'clientType'	=>'PC',
				'clientIp'		=>$output->userIp,
				'attach'		=>'',# 附加/扩展
				'signType'		=>'md5',
				'notifyUrl'		=>$notifyUrl,#http://reward.suhy.test.zol.com.cn/?c=Index&a=DealPayNotice
				'callbackUrl'	=>$callbackUrl,
			),
    	));
    	#var_dump($xmlStr);exit();
    	#$xmlStr = '<xml><partnerId>101</partnerId><orderId>2015122312123</orderId><goodsDesc>测试订单</goodsDesc><payType>WXPAY</payType><goodsName>龙芯一号</goodsName><goodsUrl>http://www.zol.com/detail/diy_host/ZXDN/25544595.html</goodsUrl><fee>6999</fee><clientType>PC</clientType><clientIp>10.16.38.115</clientIp><attach>附件</attach><signType>md5</signType></xml>';
    	#$xmlStr =  '<xml><partnerId>101</partnerId><orderId>2015122312123</orderId><goodsDesc>打赏文章</goodsDesc><payType>WXPAY</payType><goodsName>龙芯一号</goodsName><goodsUrl>http://www.zol.com/detail/diy_host/ZXDN/25544595.html</goodsUrl><fee>69119</fee><clientType>PC</clientType><clientIp>10.16.38.115</clientIp><attach>附件</attach><signType>md5</signType></xml>';
    	#$xmlStrGBK = $xmlStr;
    	$xmlStr = iconv('GBK', 'UTF-8//IGNORE', $xmlStr);
    	$xmlStr = base64_encode($xmlStr);
    	# Crypt3Des加密
    	$rep = new Libs_Reward_Crypt3Des('@!@!@');
    	$sign = md5($rep->encrypt($xmlStr));
    	#var_dump($xmlStr,$sign);exit();
    	/* # 请求ZOL支付网关
    	$data = Libs_Reward_CurlRequest::curlPostMethod(array(
    		'url'		=> 'http://10.19.37.162:8080/paygate/payment/scan/prepay?sign='.$sign,
    		'pull_data'	=> $xmlStr,
    		'data_type'	=>'xml',
    	));
    	if(!$data){
    		# 异常，记录日志
    		Helper_Reward_RewardFunc::ajaxExit(array(
	    		'flag'		=> 'error',
	    		'errorCode'	=> 8004,
	    		'msg'		=> '[8004]xml捕获失败，请求支付网关的xml，返回异常！',
	    		'toLog'		=> 1,
	    		'showHtml'	=> $errorHtml,
    		));
    	}
    	# ZOL支付接口状态码
    	$payStatusCode = array(
    		'800'=>'正常',
    		'801'=>'XML报文为NULL',
    		'802'=>'签名验证错误',
    		'803'=>'XML解析失败',
    		'805'=>'支付类型错误',
    		'806'=>'没有找到接入方ID',
    	);
    	# 解析xml
    	$xml = new SimpleXMLElement($data);
    	$xmlArr = array();
    	foreach($xml as $k=>$v){
    		$xmlArr[$k] = $v;
    	}
    	#var_dump($xmlArr);exit();
    	$statusCode = (array)$xml->resultCode;
    	if($statusCode[0] != 800){
    		$errorMsg = isset($payStatusCode[$statusCode[0]]) ? $payStatusCode[$statusCode[0]] : '';
    		Helper_Reward_RewardFunc::ajaxExit(array(
	    		'flag'		=> 'error',
	    		'errorCode'	=> 8003,
	    		'payErrorCode'=>$statusCode[0],
	    		'msg'		=> '[8003]支付发生异常！请求支付网关后，返回的xml状态码异常！'.$errorMsg."$statusCode[0]",
	    		'toLog'		=> 1,
	    		'showHtml'	=> $errorHtml,
    		));
    	}
    	$src = $output->src = isset($xmlArr['payUrl']) ? $xmlArr['payUrl'] : ''; */
    	# 如果是支付宝，则进行跳转新页面  1表示支付宝
    	if($output->payment == 1){
    		#$src = $output->src = 'http://10.19.38.115:8080'.$src;
    		#header('Location:'.$src);
    	}
    	$targetStr = $output->payment == 1 ? '' : 'target="iframeQCode"';
    	# 方案2 Start  20160104  http://localhost/www/class_of_me/www/reward/test.php   http://cashier.zol.com/paygate/pay?partnerId=101
    	$way2 = '<form id="form_zolpaygate" action="http://cashier.zol.com/paygate/pay?partnerId=101" method="post" '.$targetStr.'>
					<input type="hidden" name="_data" value="'.$xmlStr.'">
					<input type="hidden" name="sign" value="'.$sign.'">
					<script type="text/javascript">document.forms["form_zolpaygate"].submit();</script>
				</form>';
    	$output->way2 = $way2;
    	#echo $way2;return '';
    	# 方案2 End
    	
    	# 二维码的HTML
    	$output->qCodeHtml = $qCodeHtml = Reward_Plugin_PayQCode::getPayHtml($input, $output);
    	
    	$output->setTemplate('RewardQCode');
    	return '';
	}
	/**
	 * 支付异步通知-修改订单状态等操作
	 */
	public function doDealPayNotice(ZOL_Request $input, ZOL_Response $output){
		# 过滤ip，只允许内网ip进行访问
		if(!API_Item_Security_Auth::isInCompany()){
			exit();
		}
		$data = @file_get_contents('php://input');
		#$data1 = '<xml><partnerId>101</partnerId><userId>guest</userId><orderId>2015122910210153</orderId><pay3sn>2015122921001004210087237475</pay3sn><fee>1</fee><payResult>1</payResult><signType>MD5</signType><date>2015-12-28 16:00:00.0 UTC</date></xml>';
		$dataOri = $data;
		# 这里接收到的是Unicode编码字符，需要将其装换成utf-8
		$data = Helper_Reward_RewardFunc::unicodeDecode(array(
			'content'=>$data,
		));
		# 记录日志
		Helper_Reward_LogModel::insertLog(array(
			'order_number'		=>'',
			'msg'				=>$data,
			'content'			=>'error[支付异步通知-通知的数据：];data:'.$data."dataOri:$dataOri",
		));
		# 对签名进行验证 -> 暂时不验证sign
		/* $sign = $input->request('sign');
		if(!$sign){
			Helper_Reward_LogModel::insertLog(array(
				'order_number'		=>'',
				'msg'				=>"[8005]签名验证失败，签名的get参数未接收到。sign:$sign",
				'content'			=>'error[支付异步通知-通知的数据：];data:'.$data."dataOri:$dataOri",
			));
		} */
		/* if(!$sign){
			Helper_Reward_LogModel::insertLog(array(
				'order_number'		=>'',
				'msg'				=>"[8005]签名验证失败，签名的get参数未接收到。sign:$sign",
				'content'			=>'error[支付异步通知-通知的数据：];data:'.$data."dataOri:$dataOri",
			));
		}else{
			# 验证签名
			# Crypt3Des加密
			$rep = new Libs_Reward_Crypt3Des('@!@!@');
			$signGet = md5($rep->encrypt($data));
			if($signGet !== $sign){
				Helper_Reward_RewardFunc::ajaxExit(array(
					'resultCode'		=> 8004,
					'resultDesc'		=> "[8004]异步接受请求失败,签名验证失败！。$signGet《=!=》$sign",
					'retry'				=> false,
					'forQueue'			=>1,
					'toLog'				=>1,
				));
			}
		} */
		if(!$data){
			Helper_Reward_RewardFunc::ajaxExit(array(
				'resultCode'		=>802,
				'resultDesc'		=>"[802]异步接受请求失败,接收xml异常。XML:$data",
				'retry'				=>false,
				'forQueue'			=>1,
				'toLog'				=>1,
			));	
		}
		# 解析回调的数据 解析xml
		$xml = new SimpleXMLElement($data);
		$xmlArr = array();
    	foreach($xml as $k=>$v){
    		$xmlArr[$k] = (array)$v;
    	}
		if($xmlArr['payResult'][0] == 1){
			Helper_Reward_RewardModel::updateOrderStatus(array(
				'order_number'	=> $xmlArr['orderId'][0], # 资讯打赏交易号 己方订单号
				'pay3sn'		=> $xmlArr['pay3sn'][0], #第三方支付交易号
				#'paysn'			=> $xmlArr['paysn'], #支付网关交易号
				'pay_time'		=> mb_substr($xmlArr['date'][0],0,19),# 支付时间
				'order_status'	=> 1, # 订单状态
				'debug'			=> 0,
			));
			# 订单成功以后，推送对账
			# 商品详情数据
			$goodsArr = array(
				'goodsId'		=>-1,#商品ID
				'goodsName'		=> iconv('GB2312', 'UTF-8//IGNORE','CMS文章打赏'),#商品名称
				'num'			=>1,#购买数量
				'goodsPrice'	=>(int)$xmlArr['fee'][0],#商品单价，单位为分
				'subcateId'		=>-1,#子类ID
				'subcateName'	=>'CMS',#子类名称
				'manuId'		=>-1,#品牌ID
				'manuName'		=>'CMS',#品牌名称
			);
			$accountArr = array(
				'orderSN'		=>$xmlArr['orderId'][0],# 订单号
				'businessType'	=>'DS',#业务类型(SC=商城 TG=团购 Z= Z+)     这里“DS”表示“打赏”  
				'clientType'	=>1,#客户端类型(1=PC 2=wap 3=app)
				'payType'		=>1,#支付类型(1=支付宝 2=微信 3=其他)
				'pay3Number'	=>$xmlArr['pay3sn'][0],
				'dealAmount'	=>(int)$xmlArr['fee'][0],#整形
				'pay3Time'		=>$xmlArr['date'][0],
				'merchantId'	=>1,#网店ID
				'detail'		=>array($goodsArr),#商品详情，格式见表4
				'remark'		=>'',#备注  可为空
				'detailUrl'		=>$xmlArr['orderId'][0],#订单详细地址
				'extId'			=>'',#扩展id   可为空
			);
			$putQueueData = array(
				'queue' 		=> 'ArticleReward',
				'producer'		=> iconv('GBK', 'UTF-8//IGNORE','打赏对账'),
				'consumerUrls'	=>'http://10.19.38.116:8080/AccountService/syncPersonalOrder',#http://10.19.38.116:8080/AccountService/syncPersonalOrder
				'msgType'		=>'json',
				'charset'		=>'utf-8',
				'priority'		=> 0,
				'persistent'	=> true,
				'version'		=>'1.0',
				'content'		=>iconv('GBK', 'UTF-8//IGNORE','打赏对账'),
				'flag'			=>1,
				'id'			=>123,
				'body'			=>json_encode($accountArr),
			);
			
			$cacheKey = 'CMS_Reward_Queue_Test:insertData';
			ZOL_Api::run("Kv.MongoCenter.set" , array(
				'module'         => 'cms',                    		#业务名
				'tbl'            => 'zol_index',              		#表名
				'key'            => $cacheKey, 						#key
				'data'           => array('data'=>$putQueueData,'accountArr'=>$accountArr,'dataJson'=>json_encode($putQueueData),),#数据
				'life'           => 1*3600,                   		#生命期,3天
			));
			
			# 将对账数据推入队列
			$result = Libs_Reward_CurlRequest::curlPostMethod(array(
				'url'		=> 'http://10.19.38.115:8990/queue/putmessage?queuename=ArticleReward&token=8951f7339caee44b23383cdded9a3d2b66',
				'pull_data'	=> json_encode($putQueueData),
				'data_type'	=>'json',
			));
			$result = json_decode($result,true);
			# 如果800，则入队成功
			if($result['resultCode']!=800){
				#  写入日志
				Helper_Reward_LogModel::insertLog(array(
					'order_number'		=>'',
					'msg'				=>"对账数据入队列异常。订单号：".$xmlArr['orderId'][0],
					'content'			=>"error对账数据入队列异常.入队返回数据：".json_encode($result),
				));
			}
			
			Helper_Reward_RewardFunc::ajaxExit(array(
				'resultCode'		=>800,
				'resultDesc'		=>"[800]接收xml并处理成功！",
				'retry'				=>false,
				'forQueue'			=>1,
				'toLog'				=>1,
			));
		}else{
			Helper_Reward_RewardFunc::ajaxExit(array(
				'resultCode'		=> 8003,
				'resultDesc'		=> "[8003]接受的xml参数中的payResult不为1！结果：".json_encode($xmlArr),
				'retry'				=> false,
				'forQueue'			=>1,
				'toLog'				=>1,
			));
		}
		
		return '';
	}
	/**
	 * 接受队列过来的数据进行处理-生成订单
	 * /?c=Index&a=DealQueueOrder
	 */
	public function doDealQueueOrder(ZOL_Request $input, ZOL_Response $output){
		$data = @file_get_contents('php://input');
		if(!$data){
			# 未接收到数据
			Helper_Reward_LogModel::insertLog(array(
				'order_number'		=>'',
				'msg'				=>"[8010]处理订单队列异常",
				'content'			=>'error[处理订单队列-post接收到的数据：];data:'.$data,
			));
			Helper_Reward_RewardFunc::ajaxExit(array(
				'resultCode'	=>8010,
				'resultDesc'	=>'error[处理订单队列-post接收到的数据：];data:'.$data,
				'retry'			=>false,
				'forQueue'		=>1,
				'toLog'			=>1,
			));
		}
		$jsonReturnArr = array();
		#$data = mb_convert_encoding($data, 'utf-8', 'gb2312');
		#$dataObj = json_decode($data);
		$dataObj = api_json_decode($data);
		/* $cacheKey = 'CMS_Reward_Queue_Test:insertData';
		ZOL_Api::run("Kv.MongoCenter.set" , array(
			'module'         => 'cms',                    		#业务名
			'tbl'            => 'zol_index',              		#表名
			'key'            => $cacheKey, 						#key
			'data'           => $dataObj,                  		#数据
			'life'           => 1*3600,                   		#生命期,3天
		)); */
		# 查询文章的作者
		$dataObj['payee'] = Helper_Reward_RewardFunc::getArticleAuthor(array('articleId'=>$dataObj['article_id'],));
		if(!$dataObj){
			$jsonReturnArr['resultCode'] 	= 801;
			$jsonReturnArr['resultDesc'] 	= "[801]error-接收过来的额xml进行api_json_decode处理后异常.data:$data";
			$jsonReturnArr['retry'] 		= false;
		}else{
			$id = Helper_Reward_RewardModel::insertData(array(
				'order_name'	=> $dataObj['order_name'],
				'order_number'	=> $dataObj['order_number'],
				'article_id' 	=> $dataObj['article_id'],
				'ext1'			=> $dataObj['ext1'],
				'ext2'			=> $dataObj['ext2'],
				'url'			=> $dataObj['url'],
				'insert_date'  	=> $dataObj['insert_date'],
				'pay_time'		=> NULL,
				'custmer'  		=> $dataObj['custmer'],
				'money'    		=> $dataObj['money'],
				'message'		=> $dataObj['message'],
				'payment'  		=> $dataObj['payment'],
				'order_status'  => $dataObj['order_status'],
				'payee'   		=> $dataObj['payee'],#
				'service_type'  => $dataObj['service_type'],# 1资讯文章打赏 2.论坛
				#'debug'			=> 1,
			));
		}
		if($id){
			$jsonReturnArr['resultCode'] 	= 800;
			$jsonReturnArr['resultDesc'] 	= 'success';
			$jsonReturnArr['retry'] 		= false;
		}
		
		Helper_Reward_RewardFunc::ajaxExit(array(
			'resultCode'	=>$jsonReturnArr['resultCode'],
			'resultDesc'	=>$jsonReturnArr['resultDesc'],
			'retry'			=>$jsonReturnArr['retry'],
			'forQueue'		=>1,
			'toLog'			=>1,
		));
		/*
		$id = Helper_Reward_RewardModel::insertData(array(
				'order_name'	=> $dataObj->order_name,
				'order_number'	=> $dataObj->order_number,
				'article_id' 	=> $dataObj->article_id,
				'url'			=> $dataObj->url,
				'insert_date'  	=> $dataObj->insert_date,
				'pay_time'		=> NULL,
				'custmer'  		=> $dataObj->custmer,
				'money'    		=> $dataObj->money,
				'message'		=> $dataObj->message,
				'payment'  		=> $dataObj->payment,
				'order_status'  => $dataObj->order_status,
				'payee'   		=> $dataObj->payee,#
				'service_type'  => $dataObj->service_type,# 1资讯文章打赏 2.论坛（暂不支持）
				#'debug'			=> 1,
		));*/
		return '';
	}
	/**
	 * 获取指定文章打赏成功的用户
	 */
	public static function doGetRewardUser(ZOL_Request $input, ZOL_Response $output){
		$articleId = $input->request('articleId');
		$callback = $input->request('callback');
		$articleId = 5591168;
		if(!$articleId){
			Helper_Reward_RewardFunc::ajaxExit(array(
				'flag'			=> 'error',
				'msg'			=> '文章id未知！',
				'toLog'			=> 0,
			));
		}
		$res1 = Helper_Reward_RewardModel::getRewardUser(array(
			'articleId'			=>$articleId,
			'orderStatus'		=>1,
			'debug'				=>0,
		));
		//var_dump($res1);
		if($res1['data']){
			# 获取用户的头像,先获取6个用户名
			$userArr = $guestUserArr = array();
			foreach($res1['data'] as $k=>$v){
				if($v['custmer'] == 'guest'){
					$guestUserArr[] = $v;
				}else{
					$userArr[] = $v;
				}
			}
			$userArr = array_merge($userArr,$guestUserArr);
			$i = 1;
			foreach($userArr as $k=>$v){
				if($i>6) break;
				if($v['custmer']=='guest')continue;
				$dataArr = ZOL_Api::run("User.Base.getUserInfo" , array(
						'userid'         => $v['custmer'],   #userid
						'rtnCols'        => 'photo,nickName',         #
				));
				$userArr[$k]['photo'] 		= $dataArr['photo'];
				$userArr[$k]['nickName'] 	= $dataArr['nickName'];
				$userArr[$k]['myCenter'] 	= 'http://my.zol.com.cn/'.$v['custmer'];
				$i++;
			}
			#var_dump($userArr);exit();
			Helper_Reward_RewardFunc::ajaxExit(array(
				'flag'			=>'success',
				'data'			=>$userArr,
				'callback'		=>$callback,
				'totalCount'	=>$res1['totalCount'],# 打赏总数
				'toLog'			=>0,
			));
		}else{
			Helper_Reward_RewardFunc::ajaxExit(array(
				'flag'			=> 'error',
				'data'			=> '没有数据！',
				'toLog'			=> 0,
			));
		}
		
		
	}

}