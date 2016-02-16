<?php
# 调试模式
if(0){
	ini_set("display_errors", "On");
	error_reporting(E_ALL | E_STRICT);
}
# 包含公共头 by suhy 20150929
$headerFile = '/www/admin/html/mainpage/index_manage_2014/statics/public_mainpage/header.html';
if(file_exists($headerFile)){
	require($headerFile);
}
?>
<?php 
//'0106',
$cssName = in_array(date('md'),array('0107','0106')) ? 'index20160106tmp.css?2015010601' : 'index20151023.css?2015';
$isOptimiz = isset($guessConfigArr['template'])&&$guessConfigArr['template']=='zol-test' ? 'OPTIMIZ' : 'CODE';
print_css($cssName,$isOptimiz,'REMOTE_NEW');

$festival2016 = time()>strtotime('2016-02-05 15:56:00') && time()<strtotime('2016-02-13 23:59:59');
// $festival2016 = false;
if($festival2016){
?>
<link rel="stylesheet" href="http://icon.zol-img.com.cn/mainpage/css/index20160128tmp.css?2016">
<?php 
}
?>
<?php //print_css('index20150528.css?2014091278','OPTIMIZ','REMOTE_NEW');?>
<?php print_css('area_auto_2014.css','CODE');?>
<?php print_css('soft_auto.css','CODE');?>
<?php echo get_auto_element(array(
    'type'      => 'css',   
    'startTime' => '2014-05-30 12:00:00',       
    'endTime'   => '2014-06-03 09:00:00',    
)); 
?>
<?php 
    echo set_topic_info('css',$define_topic_height);//设置包版
?>
<!-- 加载广告需要的js Start 20151117-1142 index20160106tmp global-template  --> 
<?php print_js('zol-swf-combo.js','CODE','REMOTE');?>
<?php print_js('adsload.js','CODE','REMOTE');?>
<!-- 加载广告需要的js End-->
	<?php get_N1_data(); ?>
	<?php 
	$str = '';
	switch($guessConfigArr['template']){
		case 'baidu': 
			$str .= 'baidu';
			break;
		case 'zol': 
			$str.= 'zol';
			break;
		case 'baifendian': 
			$str.= 'baifendian';
			break;
		default:
			$str .= 'zol';
			break;
	}
	echo '<script>var ourRule = "'.$str.'";</script>';
	?>
	<base target="_blank"/>
</head>

<body>
<!-- 顶导S mainpage-global<?php echo date('Y-m-d H:i:s');?> -->
<div class="top_bar">
    <div class="wrapper clearfix">
        <!-- 手机客户端入口 add by jialp at 20140917 -->
		<div class="client-enter" id="client-enter">
			<a href="http://www.zol.com.cn/help/index.html" class="client-links">手机客户端</a>
			<div class="client-enter-body" style="display: none;">
				<h3>数码爱好者必备神器</h3>
				<div class="client-enter-inner clearfix">
					<img src="http://icon.zol-img.com.cn/mainpage/help/20140918/client-enter-code-new.png" width="99" height="99" alt="" class="client-code">
					<h4>中关村在线客户端</h4>
					<div class="client-btns">
						<a href="http://sj.zol.com.cn/down.php?softid=20061&amp;subcateid=73&amp;site=11" class="client-android">Android版</a>
						<a href="https://itunes.apple.com/cn/app/zhong-guan-cun-zai-xianiphone/id539824445?mt=8" class="client-iphone">iPhone版</a>
					</div>
				</div>
			</div>
		</div>
		<!-- //手机客户端入口 add by jialp at 20140917 -->
        <a href="javascript:;" target="_self" class="add-fav" id="addFav">加入收藏</a>
         <!-- add by hanjw 20150618 -->
        <a href="http://e.zol.com.cn/" class="starpromotion-link">产品入库<i class="icon-hot"></i></a>
        <a href="http://dealer.zol.com.cn/register_explain.php" class="starpromotion-link">广告联盟</a>
        <div id="userInfo">
			
        </div>
    </div>
</div>
<!-- 顶导 E  -->
<div class="wrapper">
<?php 
print_ad('a2015_top_980_90.inc');
?>
</div>
<?php
// 20160107高通广告 Start
if(in_array(date('md'),array('0107','0106'))){
	echo '<div style="background:#fff;width:1002px;margin:0px auto;position: relative;padding:-2px;">';
}
?>

<div class="header">
<?php 
if($festival2016){
?>
	<div class="newyear2016-logo">中关村在线zol.com.cn</div>
	<div class="newyear2016-gif"></div>
	<div class="newyear2016-banger"></div>
<?php 
}
?>
    <h1 class="logo">中关村在线zol.com.cn</h1>
	<div class="search">
		<ul class="search-type">
			<li id="search_all" class="active">综合</li>
			<li id="search_pro">产品</li>
			<li id="search_article">资讯</li>
			<li id="search_bbs">论坛</li>
			<li id="search_xiazai">下载</li>
			<li id="search_ask">问答</li>
			<li id="search_pic">图片</li>
			<li id="search_video">视频</li>
			<li id="search_koubei">口碑</li>
		</ul>
	    <div class="search-box" id="search_ad">
			<form id="search_frm" method="get" action="http://search.zol.com.cn/s/all.php">
				<div class="search-keyword">
					<input id="keyword" type="text" name="keyword" data-source="all" autocomplete="off" hidefocus="true" maxlength="385">
				</div>
				<input type="submit" value="搜 索" class="search-btn" hidefocus="true">
				<input type="hidden" id="hide_c" name="c" value="SearchList">
				<input type="hidden" id="hide_p" name="" value="">
			</form>
		</div>
	</div>
	<div class="tools-nav"><a class="tools-price" href="http://detail.zol.com.cn/">查报价</a> <a class="tools-rank" href="http://top.zol.com.cn/">产品排行</a> <a class="tools-diy" href="http://zj.zol.com.cn/">模拟攒机</a></div>
</div>

<div class="nav">
	<div class="main-nav">
		<div class="main-nav-item">
			<a href="http://news.zol.com.cn/" class=""><strong>新闻</strong></a>
			<a href="http://labs.zol.com.cn/">评测</a>
			<a href="http://price.zol.com.cn/">行情</a>
			<a href="http://zhibo.zol.com.cn/">直播</a>
			<a href="http://digi.zol.com.cn/">导购</a>
			<a href="http://v.zol.com.cn/">视频</a>
			<a href="http://tupian.zol.com.cn/">图片</a> 
		</div>
		<div class="main-nav-line">|</div>
		<div class="main-nav-item">
			<a href="http://www.zol.com/"><strong>商城</strong></a>
			<a href="http://tuan.zol.com/?hmsr=zoli_tuan2012827">团购</a>
			<a href="http://dealer.zol.com.cn/">经销商</a>
			<a href="http://2.zol.com.cn/">二手</a>
			<a href="http://hh.zol.com/">全民微商</a>
		</div>
		<div class="main-nav-line">|</div>
		<div class="main-nav-item">
			<a href="http://bbs.zol.com.cn/"><strong>论坛</strong></a>
			<a href="http://ask.zol.com.cn/">问答</a>
			<a href="http://huodong.zol.com.cn/">活动</a>
			<a href="http://try.zol.com.cn/">试用</a>
		</div>
		<div class="main-nav-line">|</div>
		<div class="main-nav-item">
			<a href="http://soft.zol.com.cn/"><strong>软件</strong></a>
			<a href="http://xiazai.zol.com.cn/">下载</a>
			<a href="http://game.zol.com.cn/" class="last">游戏</a>
		</div>
	</div>
	
    <div class="nav-section clearfix"> 
        <!-- 导航切换 -->
        <ul class="nav-switch clearfix">
            <li class="current" id="navswitc-cate-tab" rel="navswitc-cate"><a href="http://www.zol.com.cn/webcenter/map.html">分类导航</a></li>
            <li rel="navswitc-new" class=""><a href="http://detail.zol.com.cn/product_new/">首发新品</a></li>
            <li rel="navswitc-phone" class=""><a href="http://detail.zol.com.cn/cell_phone_index/subcate57_list_1.html">热门手机</a></li>
            <li rel="navswitc-bk" class="navswitc-bk-tab"><a href="http://tuan.zol.com/index.php?c=List&a=HotTuan">Z爆款</a></li>
            <li rel="navswitc-cz" class="navswitc-cz"><a href="http://z.zol.com.cn/">优惠推荐</a></li>
        </ul>
		<div class="line"></div>
		<!-- 滚动头条S -->
			<?php 
                   $adH = get_file_height(AD_PATH.'a2013_topic_right_190_20.inc'); 
                    if($adH > 0){
                        echo '<div class="ad-link">';
                        print_ad('a2013_topic_right_190_20.inc');
                        echo '</div>';
                     }else{
						print_module_content('get_zuichaozhi_manual','最超值手工');
                     }                                      
        	?>
        <!-- 滚动头条E -->	
    </div>
    <!-- 分类导航 -->
<?php
# 包含公共的二导航 by suhy 20150929
$navFile = '/www/admin/html/mainpage/index_manage_2014/statics/public_mainpage/category_nav.html';
if(file_exists($navFile)){
	require($navFile);
}
?>
    <!-- 首发新品 -->
    <div class="new-launch" id="navswitc-new" style="display: none;">       
        <?php
            print_module_content('get_newlaunch_2014','首发新品');
        ?>   
    </div>
    <!-- 热门手机 -->
    <div class="new-launch" id="navswitc-phone" style="display: none;">        
   		<?php
        print_module_content('get_hotphone_2014','热门手机');
        ?>     
    </div>
    <!-- Z爆款 -->
    <div class="z-bk-list" id="navswitc-bk" style="display: none;">
    	<?php
            print_module_content('get_zbaokuan_nav_2014','z爆款导航');
        ?>
    </div>
    
	<!-- 超值推荐 -->		
	<div class="z-cz-list" id="navswitc-cz" style="display: none;">        
		<?php
            print_module_content('best_valuable_recommend','超值推荐');
        ?>      
	</div>
    
    <!-- 全国行情 -->
    <div class="other-website" id="z_site_city"><span><a href="http://price.zol.com.cn/">全国行情</a>：</span><?php print_module_content('get_top_sub_2014','全国分站');?> <a href="http://price.zol.com.cn/">40城市<em>&gt;&gt;</em></a></div>
     <!-- 二维码 -->
<?php
// http://i2.uppic.fd.zol-img.com.cn/g5/M00/0C/08/ChMkJlapi06IeOkIAABKl7U3Mt0AAHw9AHjyIoAAEqv940.jpg
// http://i2.uppic.fd.zol-img.com.cn/g5/M00/0D/00/ChMkJ1aqHhWIXuxjAABLIF7hPWoAAHzBQHUCP0AAEs4174.jpg
$dateArr = array('0128','0129','0130','0131','0201','0202','0203',);
if(!in_array(date('md'),$dateArr) && false){
?>
	<div class="quick-mark" >
		<i class="quick-mark-close"></i>
		<a href="http://www.zol.com.cn/help/index.html" class="quick-mark-pic">
			<img src="http://icon.zol-img.com.cn/cms/zollogo/mqrcode.png" alt="中关村在线手机站" width="50" height="50">
			<span>扫码下载ZOL客户端</span>
		</a>
	</div>
<?php 
}elseif(false){
?>
 	<div class="quick-mark" >
		<i class="quick-mark-close"></i>
		<a href="http://www.zol.com.cn/help/index.html" class="quick-mark-pic" >
			<img class="tmp-style" src="http://i2.uppic.fd.zol-img.com.cn/g5/M00/0D/00/ChMkJ1aqHhWIXuxjAABLIF7hPWoAAHzBQHUCP0AAEs4174.jpg" alt="中关村在线手机站" width="92" height="150">
		</a>
	</div>
<?php 
}
?>
    
</div>


<div class="wrapper clearfix">
    <div class="side-news">
        <?php
           print_module_content('get_zshentong_201503','Z神通自媒体联盟');
        ?>
    </div>
	<div class="main">
	    <div class="ad-box" id="ipadpic">
	    	<ul class="ad-text clearfix">
	        <?php
	           print_module_content('get_nav_ad_top_2014','大图上文字广告');
        	?>
	        </ul>
	        <ul class="ad-window">
	            <li class="ad-turn"><?php print_ad('a2013_focus_left_145_90.inc');?></li>
	            <li class="ad-center"><?php print_ad('a2013_focus_430_100.inc');?></li>
	            <li class="ad-turn last"><?php print_ad('a2013_focus_right_145_90.inc');?></li>
	        </ul>
	        <ul class="ad-text clearfix">
	        <?php
	           print_module_content('get_nav_ad_bottom_2014','大图下文字广告');
        	?>
	        </ul>
	    </div>
	     <!--AD0AD-->
		<?php 
	        //通栏广告
        	print_ad_area_2014(0);
			set_topic_info('php',$define_topic_height,3);//设置包版
        	//print_module_content('get_intel_ad_2014','intel2014 商业配合');
			print_module_content('get_intel_ad_2015','intel2015 商业配合');
     	?>
	</div>
</div>

<?php 
	set_topic_info('php',$define_topic_height,1);//设置包版
?>

<!-- 双12 Start  20151211 -->
<?php
include '/www/mainpage/html/include/cms_active.html';
?>
<!-- 双12 End -->
<?php 
if($festival2016){
	print_module_content('new_year_speacial_edition_2016','新春特辑');
}
?>

<!-- 主要新闻区块 -->
<a name="news" class="page-anchor"></a>
<div class="wrapper clearfix">
	<div class="main">
		<div class="main-news-box">
			<!-- 焦点图切换卡 -->
			<div class="section" id="scroll-stop">
			<?php 
			if($festival2016){
			?>
			<div class="newyear2016-focus-title">新春快乐</div>
			<?php
			}
                 print_module_content('get_hot_day_201509','首页首屏焦点图');
            ?>
			</div>
			<!-- //焦点图切换卡 -->
			<!-- 爆款模块 -->
			<?php 
			if($festival2016){
				print_module_content('festival_planning_2016','节日策划');
			}else{
			?>
			<div class="section zbk-module">
				<div class="zbk-tabs clearfix">
					<?php
	                  print_module_content('bk_recommend','爆款模块');
               		?>
				</div>
				<div class="tab-news-icon-bg">
					<em class="tab-news-icon">
					<!-- <span class="current"></span><span></span><span></span> -->
						<?php
		                  print_module_content('bk_btn','爆款模块切换按钮');
               			?>
               		
                    </em>
				</div>
			</div>
			<?php 
			}
			?>
			<!-- //爆款模块 -->
			
		</div>
		
		<div class="main-headline-box">
			<!-- 今日焦点&新品首测&本地行情 S -->
			<div class="section">
				<div class="section-tab">
					<ul class="switc clearfix">
						<li class="active" rel="focus_news_1"><a href="http://news.zol.com.cn/">今日焦点</a></li>
						<li rel="new_product_1"><a href="http://labs.zol.com.cn/">新品首测</a></li>
						<li rel="pro-price-tab"><a href="http://price.zol.com.cn/">降价促销</a></li>
						<li rel="zhibo-20151120" class="zhibo-title-20151120"><a href="http://zhibo.zol.com.cn/">事件</a></li>
					</ul>
					<div class="line" style="left:0; width:72px;"></div>
				</div>
				<!-- 今日焦点 -->
				<?php 
				if($festival2016){
				?>
				<div id="focus_news_1" class="headline-news-section" >
					 <?php print_module_content('get_today_focus_news_2016','焦点新闻')?>
				</div>
				<?php 
				}else{
				?>
				<div id="focus_news_1" class="headline-news-section" >
					 <?php print_module_content('get_focus_news_201407','焦点新闻')?>
				</div>
				<?php 
				}
				?>
				<!-- //今日焦点 -->
				
				<!-- 新品首测 -->
				<div id="new_product_1" class="headline-news-section new-product" style="display:none;">
					<?php 
						print_module_content('new_first_test','新品首测文字');
					?>
				</div>
				<!-- //新品首测 -->
				
				<!-- 本地行情 -->
				<div id="pro-price-tab" class="headline-news-section" style="display:none;">
					 
				</div>
				<!-- //本地行情 -->
				<!-- 日历 -->
                <div id="zhibo-20151120" class="headline-news-section zhibo-20151120-con" style="display:none;">
                    <div id="datepicker"></div>
                    <div id="calendarShowDiv"></div>
                </div>
                <!-- //日历 -->
			</div>
			<!-- 今日焦点&新品首测&本地行情 E -->
			
			
			
		</div>
	</div>
	
	<div class="sidebar">
		<!-- 诺基亚N1 -->
		<div class="n1-pad-entrance-wrap">
			<?php
				print_ad_area_2014(12);
			?>
		</div>
		<!-- //诺基亚N1 -->
		
	</div>
</div>
<!-- 猜您喜欢 Start 20160119 -->
<?php 
$style1 = $guessConfigArr['template']=='baidu' ? 'style="display:block;"' : 'style="display:none;"';
?>
<div id="J_YouLike" class="wrapper you-like" <?=$style1?> >
<?php 
if($guessConfigArr['template']=='baidu'){
?>
<!-- 百度推荐 -->
		<div id="hm_t_23453"> 
			<script  type="text/template">
				<div class="you-like-header">
					<h3>猜你喜欢</h3>
					<span id="J_YouLikeSwitch" class="you-like-switch">换一换</span>
					<span id="J_YouLikeClose" class="you-like-close">关闭</span>
				</div>
				
				<div class="you-like-list-box">

<?php 
for($i=0,$j=0;$i<35;$i+=3,$j++){
  if($j > 8) break;
  $style1 = $i<=0 ? 'style="display:block;"' : 'style="display:none;"';
  
  if(($i)%9 == 0){
    if($i>0) echo '</div>';
    echo '<div class="you-like-list" '.$style1.'>';
  }
  echo '
        <div class="you-like-item clearfix">
          <a class="pic-item" href="#{imgResult['.$j.'].url}?baiduvlike=yes" title="#{imgResult['.$j.'].title}">
            <img src="#{imgResult['.$j.'].img_url}" width="100" height="75" />
            <span>#{imgResult['.$j.'].title}</span>
          </a>
          <ul class="text-item">
            <li><a href="#{txtResult['.$i.'].url}?baiduvlike=yes" title="">#{txtResult['.($i).'].title}</a></li>
            <li><a href="#{txtResult['.($i+1).'].url}?baiduvlike=yes" title="">#{txtResult['.($i+1).'].title}</a></li>
            <li><a href="#{txtResult['.($i+2).'].url}?baiduvlike=yes" title="">#{txtResult['.($i+2).'].title}</a></li>
          </ul>
        </div>';

  if(($i+2)==26) echo '</div>';
}
?>

			</div>
		</script>
		<script src="http://crs.baidu.com/tapi.js?planId=23453&siteId=ae5edc2bc4fc71370807f6187f0a2dd0"></script>
	</div>

<?php
}else{
?>
	<div class="you-like-header">
		<h3>猜你喜欢</h3>
		<span id="J_YouLikeSwitch" class="you-like-switch" style="display:none;">换一换</span>
		<span id="J_YouLikeClose" class="you-like-close" style="display:none;">关闭</span>
	</div>
	<div class="you-like-list-box">

	</div>
<?php 
}
?>
</div>
<!-- 猜您喜欢 End -->


<div class="wrapper clearfix">
	<div class="main">
		<div class="main-bbs-box">
			<!-- 论坛精选&问答堂 -->
			<div class="section">
				<div class="section-tab">
					<ul class="switc clearfix">
						<li class="active" rel="bbs_goods_selection"><a href="http://bbs.zol.com.cn/" >论坛精选</a></li>
						<li rel="question_answer"><a href="http://ask.zol.com.cn/" >问答堂</a></li>
						<li rel="product_comment"><a href="http://detail.zol.com.cn/koubei/" style="display:none;" >产品点评</a></li>
					</ul>
					<div class="line" style="left:0; width:72px;"></div>
				</div>
				<div class="bbs-tabs" style="display:block;" id="bbs_goods_selection">
					
					<?php
	                  print_module_content('get_hot_bbs_201407','论坛精选区块');
               		?>
				</div>
				<div class="bbs-tabs" style="display:none;" id="question_answer">
					<?php
	                  print_module_content('get_bbs_ask_201407','问答堂区块');
               		?>
				</div>
				<div class="bbs-tabs" style="display:none;" id="product_comment">
					<?php
	                  print_module_content('get_product_comment_201506','产品点评区块');
               		?>
				</div>
			</div>
			<!-- //论坛精选&问答堂 -->
			<!-- 免费试用&论坛活动 -->
			<div class="section">
				<?php print_module_content('free_fee_using_bbs_action','免费试用&论坛活动')?>
			</div>
			<!-- //免费试用&论坛活动 -->
		</div>
		<div class="product-news-box">
			<div class="section">
				<!-- 广告 -->
				<div class="text-banner"> 
					<?php prin_ad_2015('a2013_info_nb_bottom_355_28.inc'); ?>
				</div>
				<!-- //广告 -->
				<!-- 手机数码 -->
				<div class="cate-news-item">
					 <?php print_module_content('get_mobile_dc_top_2014','手机数码头条'); ?>
				</div>
				<!-- //手机数码-->
				<!-- DIY配件 -->
				<div class="cate-news-item cate-news-item-diy">
					<?php print_module_content('get_diy_top_2014','diy配件头条'); ?>
				</div>
				<!-- //DIY配件 -->
				<!-- 广告 -->
				<div class="text-banner"> 
					<?php prin_ad_2015('a2014_topnews_diy_bottom_370_28.inc'); ?>
				</div>
				<!-- //广告 -->
				<!-- 家电汽车 -->
				<div class="cate-news-item">
					<?php print_module_content('get_jd_top_2014','家电头条'); ?>
				</div>
				<!-- //家电汽车-->
				<!-- 企业软件 -->
				<div class="cate-news-item cate-news-item-smb">
					<?php print_module_content('get_smb_soft_top_2014','企业软件头条'); ?>
				</div>
				<!-- //企业软件 -->
			</div>
		</div>
	</div>
	<div class="sidebar">
		<!-- 产品报价导航 -->
		<div class="module product-module">
			<div class="module-head">
				<h2><a href="http://detail.zol.com.cn/">产品报价</a></h2>
				<!-- delete by hanjw 20150618<a class="all-entry" href="http://detail.zol.com.cn/tujie/">[高清图解]</a>  -->
				<!-- add by hanjw 20150618 -->
        		<a href="http://e.zol.com.cn/" class="starpromotion-link">产品入库<i class="icon-hot"></i></a>
			</div>
			<div id="productNav" class="product-nav">
				<?php
	         		 print_module_content('get_price_hardwork_201407','产品报价');
        		 ?>
			</div>
			<div class="product-more"> <a class="all" href="http://detail.zol.com.cn/">全部产品报价<i>&gt;</i></a> <a class="all-brand" href="http://www.zol.com.cn/brand.html">所有产品品牌<i>&gt;</i></a> 
			</div>
		</div>
		<!-- //产品报价导航 -->
	</div>
</div>
<!-- 包版位置二  S-->
<?php 
  set_topic_info('php',$define_topic_height,2);//设置包版
?>
<!-- 包版位置二  E -->
<!-- 广告1 -->
<div class="wrapper"><?php print_ad_area_2014(1); ?></div>


<!-- 电商区块-1 -->
<?php
 require('/www/admin/html/mainpage/index_manage_2014/statics/public_mainpage/zol_shop.php');
?>
<!-- //电商区块 -->
<!-- 广告8 -->
<div class="wrapper"><?php print_ad_area_2014(8); ?></div>


<!-- 北京值得买 -->
<div class="wrapper">
	<div class="section worthy-buy"> 
		<div class="section-tab worthy-buy-tab">
			<ul class="switc clearfix">
				<li rel="worthyBuyArea" class="active"><a href="#" id="city_title" city="beijingshi" >北京值得买</a></li>
				<li rel="preferentialArea"><a href="http://dealer.zol.com.cn/dealer_article/topic/3998216.html">特惠专区</a></li>
			</ul>
			<div class="line" style="left:0; width:90px;"></div>
		</div> 
		<div id="worthyBuyArea" class="worthy-buy-tabpanel">
			<div class="market-city" style="display: block;"><a href="http://shanghai.zol.com.cn/"  city="shanghai" class="active">上海</a><a href="http://gz.zol.com.cn/" city="guangzhou" class="active">广州</a><a href="http://price.zol.com.cn/" id="productTab" city="beijingshi" class="active">北京</a>  
			</div>
			<div class="other-citys" style="display: block;"> <span class="more-city-trigger">更多</span>
				<div class="other-citys-box">
					<dl>
						<dt>东北华北：</dt>
						<dd> <span class="city" city="beijingshi">北京</span> <span class="city" city="tianjinshi">天津</span> <span class="city" city="liaoningsheng">沈阳</span> <span class="city" city="jilinsheng">长春</span> <span class="city" city="heilongjiangsheng">哈尔滨</span>									<span class="city" city="hebeisheng">石家庄</span> <span class="city" city="shanxisheng">太原</span> <span class="city" city="shanxisheng_datongshi">大同</span> </dd>
					</dl>
					<dl>
						<dt>华东华北：</dt>
						<dd> <span class="city" city="shanghaishi">上海</span> <span class="city" city="zhejiangsheng">杭州</span> <span class="city" city="zhejiangsheng_ningboshi">宁波</span> <span class="city" city="zhejiangsheng_wenzhoushi">温州</span> <span class="city" city="jiangsusheng">南京</span>									<span class="city" city="shandongsheng">济南</span> <span class="city" city="shandongsheng_yantaishi">烟台</span> <span class="city" city="shandongsheng_qingdaoshi">青岛</span> <span class="city" city="fujiansheng">福州</span> <span class="city" city="fujiansheng_xiamenshi">厦门</span>									<span class="city" city="anhuisheng">合肥</span> <span class="city" city="anhuisheng_anqingshi">安庆</span> <span class="city" city="jiangxisheng">南昌</span> <span class="city" city="fujiansheng_quanzhoushi">泉州</span> </dd>
					</dl>
					<dl>
						<dt>华南地区：</dt>
						<dd> <span class="city" city="guangdongsheng_shenzhenshi">深圳</span> <span class="city" city="guangdongsheng">广州</span> <span class="city" city="guangdongsheng_foshanshi">佛山</span> <span class="city" city="guangdongsheng_dongshi">东莞</span> <span class="city"
							city="hunansheng">长沙</span> <span class="city" city="guangxi">南宁</span> <span class="city" city="guizhousheng">贵阳</span> </dd>
					</dl>
					<dl>
						<dt>中西部：</dt>
						<dd> <span class="city" city="shan3xisheng">西安</span> <span class="city" city="sichuansheng">成都</span> <span class="city" city="zhongqingshi">重庆</span> <span class="city" city="henansheng">郑州</span> <span class="city" city="gansusheng">兰州</span>
							<span
							class="city" city="hubeisheng">武汉</span> <span class="city" city="yunnansheng">昆明</span> <span class="city" city="neimenggu">呼和浩特</span> <span class="city" city="xinjiang">乌鲁木齐</span> </dd>
					</dl>
					<p class="more-city"><a href="http://price.zol.com.cn/">更多城市&gt;&gt;</a>
					</p>
				</div>
			</div>
			<div class="clearfix" id="local_info">
				
			</div> 
		</div>
		<div id="preferentialArea" class="worthy-buy-tabpanel teihui-buy-tabpanel" style="display: none;">	
		<?php
        	 print_module_content('preference_zone201503','电商特惠专区');
        ?>
		</div>
	</div>
</div>
<!-- //北京值得买 -->



<a name="mobile" class="page-anchor"></a>
<!-- 手机区块 -->
<div class="wrapper mobile-wrap clearfix">
	<div class="main">
		<div class="section-head clearfix">
			<h2><a href="http://mobile.zol.com.cn/">手机</a></h2>
			<p><a href="http://4g.zol.com.cn/">4G</a><a href="http://mobile.zol.com.cn/android.html">安卓</a><a href="http://mobile.zol.com.cn/iphone.html">苹果</a><a href="http://mobile.zol.com.cn/windows.html">Windows Phone</a><a href="http://sj.zol.com.cn/">手机软件</a><a href="http://minipower.zol.com.cn/">移动电源</a></p>
		</div>
		
			<?php
				print_module_content('get_mobile_201505','手机资讯');
			?>
			
			<!--AD011AD-->
		<?php print_ad_area_2014(11); ?>
	</div>
	<div class="sidebar">
		<div class="module">
			<div class="module-head">
				<h2><a href="http://top.zol.com.cn/compositor/cell_phone.html">手机关注排行</a></h2>
			</div>
			<!--SET1423491909_9SET--> 
			<?php
               print_module_content('get_mobile_rank_201505','手机排行榜');
           ?>
		</div>
	</div>
</div>
<!-- //手机区块 -->

<!-- 笔记本区块 -->
<div class="wrapper nb-wrap clearfix">
	<div class="main">
		<div class="section-head clearfix">
			<h2><a href="http://nb.zol.com.cn/">笔记本</a></h2>
			<p><a href="http://nb.zol.com.cn/portable.html">平板本</a><a href="http://pc.zol.com.cn/">台式机</a><a href="http://aio.zol.com.cn/">一体机</a><a href="http://robot.zol.com.cn/ ">机器人</a></p>
		</div>
		
			<?php
               print_module_content('get_nb_pc_201505','笔记本资讯');
           ?>
			<!--AD02AD is need ".adSpace"? -->
			<div class="adSpace"><?php print_ad_area_2014(2); ?> </div>
	</div>
	<div class="sidebar">
		<div class="module">
			<div class="module-head">
				<h2><a href="http://top.zol.com.cn/compositor/notebook.html">笔记本关注排行</a></h2>
			</div>
			
			<?php
            	print_module_content('get_nb_rank_201505','笔记本排行榜');
            ?>
		</div>
	</div>
</div>
<!-- //笔记本区块 -->
<a name="dc" class="page-anchor"></a> 
<!-- 数码影音区块 -->
<div class="wrapper dc-wrap clearfix">
	<div class="main">
		<div class="section-head clearfix">
			<h2><a href="http://dcdv.zol.com.cn/">数码影音</a></h2>
			<p><a href="http://dv.zol.com.cn/">摄像机</a><a href="http://dcdv.zol.com.cn/dslr.html">单反相机</a><a href="http://dcdv.zol.com.cn/travel.html">旅游摄影</a><a href="http://headphone.zol.com.cn/">耳机</a><a href="http://speaker.zol.com.cn/">音箱</a><a href="http://hifi.zol.com.cn/">HIFI</a><a href="http://pj.zol.com.cn/">配件</a><a href="http://mst.zol.com.cn/subcate_37.html">U盘</a><a href="http://minipower.zol.com.cn/">移动电源</a><a href="http://mst.zol.com.cn/subcate_54.html">存储卡</a>
			<a href="http://ht.zol.com.cn/ ">家庭影院</a>
			</p>
		</div>
		
			<?php
            	print_module_content('get_dcdv_201505','数码影音资讯');
            ?>
		<!--AD04AD-->
		<?php print_ad_area_2014(4); ?>
		
	</div>
	<div class="sidebar">
		<div class="module">
			<div class="module-head">
				<h2><a href="http://top.zol.com.cn/compositor/digital_camera.html">相机关注排行</a></h2>
			</div>
			<?php
             print_module_content('get_dcdv_rank_201505','相机关注排行榜');
            ?>
		</div>
	</div>
</div>
<!-- //数码影音区块 --> 
<a name="diy" class="page-anchor"></a> 
<!-- 硬件/外设区块 -->
<div class="wrapper diy-wrap clearfix">
	<div class="main">
		<div class="section-head clearfix">
			<h2><a href="http://diy.zol.com.cn/">硬件/外设</a></h2>
			<p><a href="http://cpu.zol.com.cn/">CPU</a><a href="http://mb.zol.com.cn/">主板</a><a href="http://vga.zol.com.cn/">显卡</a><a href="http://power.zol.com.cn/">机箱电源</a><a href="http://ssd.zol.com.cn/">SSD</a><a href="http://mouse.zol.com.cn/">键鼠</a><a href="http://lcd.zol.com.cn/">显示器</a><a href="http://memory.zol.com.cn/">内存</a><a href="http://memory.zol.com.cn/pc_disk.html">硬盘</a><a href="http://smartwear.zol.com.cn/">智能穿戴</a><a href="http://esports.zol.com.cn/">电子竞技</a></p>
		</div>
		
			<?php
             print_module_content('get_diy_201505','硬件外设资讯；硬件资讯');
            ?>
		
		<!--AD0AD-->
		<?php print_ad_area_2014(5); ?>
	</div>
	<div class="sidebar">
		<div class="module">
			<div class="module-head">
				<h2><a href="http://top.zol.com.cn/compositor/lcd.html">显示器关注排行</a></h2>
			</div>
			<?php
             print_module_content('get_lcd_rank_201505','显示器关注排行榜');
            ?>
			
		</div>
	</div>
</div>
<!-- //硬件外设区块    -->
<a name="nb" class="page-anchor"></a>
<!-- 平板区块 -->
<div class="wrapper pad-wrap clearfix">
	<div class="main">
		<div class="section-head clearfix">
			<h2><a href="http://pad.zol.com.cn/">平板</a></h2>
			<p><a href="http://mid.zol.com.cn/">国产平板</a><a href="http://phablet.zol.com.cn/">平板手机</a></p>
		</div>
		
			<?php
            	print_module_content('get_ipad_201505','平板资讯；平板电脑资讯');
            ?>
		
		<!--AD100 003 AD-->
		<?php 
			print_ad_area_2014(3);
		?> 
	</div>
	<div class="sidebar">
		<div class="module">
			<div class="module-head">
				<h2><a href="http://top.zol.com.cn/compositor/tablepc.html">平板关注排行</a></h2>
			</div>
			<?php
            	print_module_content('get_pad_rank_201505','平板关注排行榜');
            ?>
			
		</div>
	</div>
</div>
<!-- //平板区块 -->
<a name="oa" class="page-anchor"></a> 
<!-- 企业办公区块 -->
<div class="wrapper oa-wrap clearfix">
	<div class="main">
		<div class="section-head clearfix">
			<h2><a href="http://smb.zol.com.cn/">企业</a>/<a href="http://oa.zol.com.cn/">办公</a></h2>
			<p><a href="http://biz.zol.com.cn/">商用</a><a href="http://security.zol.com.cn/">安防</a><a href="http://server.zol.com.cn/">服务器</a><a href="http://net.zol.com.cn/">网络</a><a href="http://oa.zol.com.cn/printer.html">打印机</a><a href="http://projector.zol.com.cn/">投影机</a><a href="http://cio.zol.com.cn/">信息化</a><a href="http://gps.zol.com.cn/">汽车电子</a><a href="http://oa.zol.com.cn/3dprinter.html">3D打印</a><a href="http://auto.zol.com.cn/">汽车科技</a></p>
		</div>
		
			<?php
             	print_module_content('get_oa_201505','办公资讯 企业办公');
            ?>
		
		<!--AD0AD--> 
		<?php print_ad_area_2014(7); ?>
	</div>
	<div class="sidebar">
		<div class="module">
			<div class="module-head">
				<h2><a href="http://top.zol.com.cn/compositor/Laser_printers.html">打印机关注排行</a></h2>
			</div>
			<?php
             	print_module_content('get_print_rank_201505','打印机关注排行榜');
            ?>
		</div>
	</div>
</div>
<!-- //企业办公区块  --> 
<a name="jd" class="page-anchor"></a> 
<!-- 家电区块 -->
<div class="wrapper jd-wrap clearfix">
	<div class="main">
		<div class="section-head clearfix">
			<h2><a href="http://jd.zol.com.cn/">家电</a></h2>
			<p><a href="http://sh.zol.com.cn/">家居</a><a href="http://tv.zol.com.cn/">电视</a><a href="http://washer.zol.com.cn/">洗衣机</a><a href="http://ac.zol.com.cn/">空调</a><a href="http://icebox.zol.com.cn/">冰箱</a><a href="http://xjd.zol.com.cn/">小家电</a><a href="http://hd.zol.com.cn/">高清</a><a href="http://smartvideo.zol.com.cn/">智能影音</a></p>
		</div>
			<?php
             print_module_content('get_dhome_201505','家电资讯');
            ?>
		<!--AD100  004AD-->
		<?php 
			//print_ad_area_2014(4);
		?> 
	</div>
	<div class="sidebar">
		<div class="module">
			<div class="module-head">
				<h2><a href="http://top.zol.com.cn/compositor/digital_tv.html">平板电视关注排行</a></h2>
			</div>
			<?php
             	print_module_content('get_led_rank_201505','平板电视关注排行榜;led电视');
            ?>
		</div>
	</div>
</div>
<!-- //家电区块    --> 

<a name="soft" class="page-anchor"></a> 
<!-- 软件区块 -->
<div class="wrapper soft-wrap clearfix">
	<div class="main">
		<div class="section-head clearfix">
			<h2><a href="http://xiazai.zol.com.cn/">软件</a>/<a href="http://game.zol.com.cn/">游戏</a></h2>
			<p><a href="http://soft.zol.com.cn/school/">教程</a><a href="http://driver.zol.com.cn/">驱动</a><a href="http://desk.zol.com.cn/">壁纸</a><a href="http://sj.zol.com.cn/">手机软件</a></p>
		</div>
		
			<?php
             	print_module_content('get_soft_game_201505','软件资讯');
            ?>
		
		<div class="module-app">
			<div class="module-head"><span class="title"><span>精品</span>软件</span></div>
			<?php
			print_module_content('best_software_201505','精品软件');
			?>
		</div>
	</div>
	<div class="sidebar">
		<div class="module">
			<div class="module-head">
				<h2><a href="http://youxi.zol.com.cn/ol/ol_1.html">游戏关注排行</a></h2>
			</div>
			<?php
             	print_module_content('get_game_rank_201505','游戏关注排行榜');
            ?>
		</div>
	</div>
</div>
<!-- //软件区块  done！   --> 

<!--AD09AD--> 
<?php print_ad_area_2014(9); ?>

<?php 
$adHeight = get_file_height(AD_PATH.'a2013_info_price_top_760_60.inc');
if($adHeight > 0){
    echo '<div class="wrapper">';
    print_ad('a2013_info_price_top_760_60.inc');
    echo '</div>';
}
?>

<!-- 论坛区块1 -->
<div class="wrapper bbs-wrap">
	<?php
		print_module_content('bbs_daily_forum','互动区块数据');
	?>
</div>
<!-- //论坛区块1  -->

<?php 
$adHeight = get_file_height(AD_PATH.'a2015_recommend_game_top_1000_60.inc');
if($adHeight > 0){
    echo '<div class="wrapper">';
    print_ad('a2015_recommend_game_top_1000_60.inc');
    echo '</div>';
}
?>



<!-- 排行榜 -->
<div class="wrapper clearfix ranking-section">
	<div class="section">
		<div class="section-head"><h2>一周内<span>热门文章</span>排行榜</h2></div>
		<?php
		print_module_content('hotest_article_in_7days','一周热门文章');
		?>
	</div>
	<div class="section">
		<div class="section-head"><h2>一周内<span>热帖点击</span>排行榜</h2></div>
		<?php
		print_module_content('hotest_bbs_in_7days','一周热门论坛帖子');
		?>
	</div>
	<div class="section fr">
		<div class="section-head"><h2>一周内<span>热门视频</span>排行榜</h2></div>
		<?php
		print_module_content('hotest_vedio_in_7days','一周热门视频');
		?>
	</div>
</div>
<!-- //排行榜 -->


<!-- 底部区域 -->
<?php
# 包含首页公共底部 by suhy 20151012
$footerFile = '/www/admin/html/mainpage/index_manage_2014/statics/public_mainpage/footer.html';
if(file_exists($footerFile)){
	require($footerFile);
}
?>

<!-- //底部区域 -->
<!-- 分站弹框 -->
<div id="_cityMarketWindow" class="winbox" style="bottom: -268px; display: none;"></div>
<!-- //分站弹框 -->

<!-- 右下角弹层 V2 -->
<?php print_module_content('get_index_right_bottom_layer_data','右下角弹层');?>
<!-- //右下角弹层 V2 -->

<!-- 返回顶部&建议反馈 
<div id="guideWidget" class="guide-widget" style="display: none;">
	<a href="http://service.zol.com.cn/complain/" class="survey" target="_blank" title="建议反馈">建议反馈</a>
	<a href="#top" class="gotop" target="_self" title="返回顶部">返回顶部</a>
</div>-->
<!-- //返回顶部&建议反馈 --> 

<script type="text/javascript">
/* nav-user-cookie */
var getCookie=function(n){
    var v = '',
    c = ' ' + document.cookie + ';',
    s = c.indexOf((' ' + n + '='));
    if (s >= 0) {
        s += n.length + 2;
        v = unescape(c.substring(s, c.indexOf(';', s)));
    }
    return v;
}
var userStr,bbsStr,
userContainer = document.getElementById("userInfo"),
userid = getCookie("zol_userid"),
backUrl = document.URL,
nickname = getCookie("zol_nickname");
nickname = nickname ? nickname : userid;
function filterStrChar(str) {if(!str){return ''} str = str.replace(/<\/?[^>]*>/g,'').replace(/[ | ]*\n/g,'\n').replace(/\n[\s| | ]*\r/g,'\n').replace(/['"]*/g,'').replace(/=*/g,'').replace(/>*/g,'').replace(/<*/g,'');return str;}
userid = filterStrChar(userid);
nickname = filterStrChar(nickname);
if (userid) {
    userStr = '<div class="sitenav-personal-center">\
			        <a href="http://service.zol.com.cn/user/login.php?type=quit" class="sitenav-personal-login-out" target="_self">退出</a>\
			        <a href="http://my.zol.com.cn/index.php?c=Message_Private" class="sitenav-personal-msg" title="短消息" target="_blank"><i style="display: none;" id="sitenav-personal-msg"></i></a>\
			        <div class="sitenav-personal-welcome">欢迎您，<a href="http://my.zol.com.cn/'+userid+'/">'+userid+'</a></div>\
			    </div>';
    userContainer.innerHTML = userStr;
} else {
    userStr = '\
    <div class="sitenav-login-bar">\
        <div class="sitenav-login-box">\
    	<span class="sitenav-login-link" id="sitenavLoginBtn">登录</span>\
    	<div class="sitenav-login-form" style="display: none;" id="sitenavLoginBox">\
			<iframe src="" frameborder="0" scrolling="no" width="1000px" height="200px"></iframe>\
	    </div>\
	</div>\
	<div class="sitenav-login-links">\
	    <a href="http://service.zol.com.cn/user/api/sina/jump.php?from=220" target="_self" class="sitenav-weibo" title="使用新浪微博登录"><i></i></a>\
	    <a href="http://service.zol.com.cn/user/api/qq/libs/oauth/redirect_to_login.php?from=220" target="_self" class="sitenav-qq" title="使用腾讯QQ登录"><i></i></a>\
	</div>\
	</div>';
    userContainer.innerHTML = userStr;
}
</script>
<script type="text/javascript" src="http://icon.zol-img.com.cn/public/js/jquery-1.7.1.min.js"></script> 
<script src="http://icon.zol-img.com.cn/public/js/search.js?v=13355" type="text/javascript"></script> 
<?php 
	require('/www/admin/html/mainpage/index_manage_2014/statics/public_mainpage/topic_active.js.php');
	echo set_topic_info('js',$define_topic_height);//设置包版
?>
<?php print_ad('a2013_public_end.inc','return');?>
<!-- 侧边广告适应新版首页宽度 add by suhy -->
<style>
#search_ad_l,#search_ad_r{width: 100px;height: 300px;border: none;position:absolute;right:50%;margin-right:500px;display:none;}
</style>
<!-- Intel 的检测代码 start 2014/12/23  --> 
<IFRAME SRC="http://ad.doubleclick.net/adi/N5751.138759.7505627606321/B8422041.114068364;sz=80x60;ord=[timestamp]?" WIDTH=80 HEIGHT=60 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no BORDERCOLOR='#000000' style="width: 0; height: 0;display: none;">
<SCRIPT language='JavaScript1.1' SRC="http://ad.doubleclick.net/adj/N5751.138759.7505627606321/B8422041.114068364;abr=!ie;sz=80x60;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/jump/N5751.138759.7505627606321/B8422041.114068364;abr=!ie4;abr=!ie5;sz=80x60;ord=[timestamp]?" style="width: 0; height: 0;display: none;">
<IMG SRC="http://ad.doubleclick.net/ad/N5751.138759.7505627606321/B8422041.114068364;abr=!ie4;abr=!ie5;sz=80x60;ord=[timestamp]?" BORDER=0 WIDTH=80 HEIGHT=60 ALT="Advertisement" style="width: 0; height: 0;display: none;"></A>
</NOSCRIPT>
</IFRAME>
<!-- // Intel 的检测代码 start 2014/12/23  -->
<script>
//执行广告
try {adsLoad('run');}catch(e){}
</script> 
<script>var zol_click_id = "1364";var zol_check_key = "9e3f82e3";</script> 
<script type="text/javascript" src="http://icon.zol-img.com.cn/mainpage/js/click.js?1"></script> 
<script>var z_mac = 1;</script> 
<script src="http://icon.zol-img.com.cn/public/js/web_foot.js" type="text/javascript"></script> 
<script type="text/javascript">
window.onerror=function(){return true;}
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-5405767-1']);
_gaq.push(['_trackPageview']);

(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
//setGuideWidgetPosition();
//重大事件相关JS
var calendarData  = <?php calendarJson();?>; 
var now           = '<?php echo date('Y-m-d');?>';
</script>
<?php 
# 百度推荐 猜你喜欢 Start
if($guessConfigArr['template']=='baidu'){
?>
<script>document.write(unescape('%3Cdiv id="hm_t_23453"%3E%3C/div%3E%3Cscript charset="utf-8" src="http://crs.baidu.com/t.js?siteId=ae5edc2bc4fc71370807f6187f0a2dd0&planId=23453&async=0&referer=') + encodeURIComponent(document.referrer) + '&title=' + encodeURIComponent(document.title) + '&rnd=' + (+new Date) + unescape('"%3E%3C/script%3E'));</script>
<?php 
}
# 百度推荐 猜你喜欢 End
?>
<script  type="text/javascript">var a =+ new Date;document.write('<img src="http://imp.zol.com.cn/imphit0001.gif?impid=smcmp1&barid=71037&type=1&tmp='+a+'" width=0 height=0 border=0 style="display:none">');</script> 
<script type="text/javascript" src="http://icon.zol-img.com.cn/public/js/zol_quick_news.js"></script>
<script type="text/javascript" src="http://icon.zol-img.com.cn/cms/js/jquery-ui-1.10.4.custom.min.js?2015"></script>
<?php //print_js('index20150310_test.js?20140','OPTIMIZ','REMOTE');?>
<?php //print_js('index20150322.js?20140','OPTIMIZ','REMOTE');?>
<?php print_js('index20150322.js?20140','EXT-MIN','REMOTE');?>

<?php
// 20160107高通广告 Start
if(in_array(date('md'),array('0107','0106'))){
	echo '</div>';
}
?>
</body>
</html>
