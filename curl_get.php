<?php
// php 使用curl实现代理抓取，可以调整referer
public static function getHtml($w)
    {
    
        $userAgentArr = array(
                'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
                'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:35.0) Gecko/20100101 Firefox/35.0',
                'Mozilla/5.0 (Windows NT 6.1; rv:17.0) Gecko/20100101 Firefox/17.0',
                'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36',
                'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.63 Safari/537.36',
                'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0',
                'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.24 Safari/537.36',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_5) AppleWebKit/601.6.17 (KHTML, like Gecko) Version/9.1.1 Safari/601.6.17',
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.11; rv:46.0) Gecko/20100101 Firefox/46.0'
        );
        $userAgent = $userAgentArr[mt_rand(0,9)];
        $url = 'https://www.baidu.com/s?wd='.$w.'&rsv_spt=1&rsv_iqid=0xe54014cb00202f3d&issp=1&f=8&rsv_bp=1&rsv_idx=2&ie=utf-8&rqlang=cn&tn=baiduhome_pg';
        $data = Helper_Soft_SoftBase::fetchHtml(array(
                'url'       => $url,        // 抓取页面链接
                'fetchType'	=> 'get',       // 抓取方式 get post
                'isLocation'=> 1,           // 是否允许跳转
                'isShow'	=> 1,           // 是否显示抓取内容
                'isBody'    => 0,           // 是否返回抓取的body体
                'time'      => 5,           // 抓取时间
                'encoding' 	=> 0,           // 需要解压缩
                'ip'        => '211.121.112.111',   // 伪造IP
                'userAgent'	=> $userAgent,  // 伪造UA
                'refer' 	=> 'https://www.baidu.com/',  // 伪造来源
        ));
        if (!empty($data)) {
            file_put_contents('./tmphtml.html', $data);
            return true;
        } else {
            return false;
        }
    
    }




/**
 	 *
 	 *@desc curl抓取
 	 *
 	 *
 	 **/
 	public static function fetchHtml($paramArr){
 	    $options = array(
 	            'url' 		=> '',	//抓取页面链接
 	            'fetchType'	=> 'get',//抓取方式 get post
 	            'isLocation'=> 1,	//是否允许跳转
 	            'isShow'	=> 1,	//是否显示抓取内容
 	            'isBody'	=> 0,   //是否返回抓取的body体
 	            'time'		=> 5,	//抓取时间
 	            'encoding' 	=> 0,		//需要解压缩
 	            'ip' 		=> '8.8.8.8',//伪造IP
 	            'userAgent'	=>'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.57 Safari/537.36',//伪造UA
 	            'refer' 	=>'http://www.baidu.com',//伪造来源
 	    );
 	    is_array($paramArr) && $options = array_merge($options,$paramArr);
 	    extract($options);
 	    if(!$url) return false;
 	    $ch = curl_init();
 	    curl_setopt($ch, CURLOPT_URL,$url);
 	    curl_setopt($ch, CURLOPT_HEADER, 0);
 	    $header = array(
 	            'X-FORWARDED-FOR:'.$ip,
 	            'CLIENT-IP:'.$ip,
 	    );
 	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  //构造IP
 	    curl_setopt($ch, CURLOPT_REFERER, $refer);   	//构造来路
 	    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent );//构造UA
 	    //$data=array('downloadKey=expires=1413136219~access=/us/r30/Purple4/v4/70/ac/33/70ac3308-e585-375a-830c-16c1a1b96c83/mzps8583090621023582440.D2.dpkg.ipa*~md5=eb613ac3487ad7af74ef0cc2ce19388b');
 	    //curl_setopt($ch, CURLOPT_POST, 1);              //提交方式 0get 1post
 	    //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);	//构造参数 如果参数是文件,则在文件名前加@
 	    curl_setopt($ch, CURLOPT_NOBODY, $isBody);	//是否抓主体
 	    if($encoding){
 	        curl_setopt($ch, CURLOPT_ENCODING, "gzip");   //解压缩方式
 	    }
 	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,$isLocation);   //允许跳转
 	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,$isShow);    //0输出 1不输出
 	    curl_setopt($ch, CURLOPT_TIMEOUT, $time);
 	    $html = curl_exec($ch);
 	    if(curl_error($ch)){
 	        print_r(curl_error($ch));
 	    }
 	    curl_close($ch);
 	    #去掉空格、换行符,方便匹配
 	    $html = preg_replace('#[\t\n\r]+#','',$html);
 	    return $html;
 	}
