<?php
/**
 * 首页包版管理
 *
 * LICENSE:
 * @author 沈通 shen.tong@zol.com.cn
 * @version 1.0
 * @copyright  http://www.zol.com.cn
 * @todo
 * @changelog 
 * 2011-03-29 created by shen.tong@zol.com.cn
 * 2011-10-18 增加可两包版同时上线
 * 2012-02-23 更新配置,重新设置2012包版
 */

require_once INCLUDE_PATH . 'func_ad.php';

//echo INCLUDE_PATH . 'func_ad.php';
$config_bb_array = array(
    '980*240'=>array(
        'name'=>'高度240包版(980*240)',
        'default'=>1,
        'child'=>array(1=>'包版1',2=>'包版2',3=>'包版3',4=>'包版4',5=>'包版5',6=>'包版6',7=>'包版7',8=>'包版8',9=>'包版9',10=>'包版10',11=>'包版11',12=>'包版12',13=>'包版13'),
        'bgurl'=>array(
            1=>'http://icon.zol-img.com.cn/2013/baoban/1329961339.png',
            2=>'http://icon.zol-img.com.cn/2013/baoban/1329979005.png',
            3=>'http://icon.zol-img.com.cn/2013/baoban/1329980793.png',
            4=>'http://icon.zol-img.com.cn/2013/baoban/1329984735.png',
            5=>'http://icon.zol-img.com.cn/2013/baoban/baobanbg2013.png',
        	6=>'http://pic.zol-img.com.cn/2013/11/1384139325.png',
        	7=>'http://icon.zol-img.com.cn/2013/baoban/1312272224.png',
        	8=>'http://icon.zol-img.com.cn/2013/baoban/ces20140108.png',
        	9=>'http://icon.zol-img.com.cn/2014/baoban/chinajoy-2014.png',
        	10=>'http://icon.zol-img.com.cn/2014/baoban/iPhone6-bg.png',
        	11=>'http://icon.zol-img.com.cn/2014/baoban/double112014.png',
        	12=>'http://icon.zol-img.com.cn/2014/baoban/ces-bg.png',
        	13=>'http://icon.zol-img.com.cn/2014/baoban/mwc-title.png',
         ),
    ),
    '980*140'=>array(
        'name'=>'高度140包版(980*140)',
        'default'=>1,
        'child'=>array(1=>'包版1',2=>'包版2',3=>'包版3',4=>'包版4',5=>'包版5',6=>'包版6',7=>'包版7',8=>'包版8'),
        'bgurl'=>array(
            1=>'http://icon.zol-img.com.cn/2013/baoban/1329986445.png',
            2=>'http://icon.zol-img.com.cn/2013/baoban/1329987113.png',
            3=>'http://icon.zol-img.com.cn/2013/baoban/1329987709.png',
            4=>'http://icon.zol-img.com.cn/2013/baoban/1329988668.png',
            5=>'http://icon.zol-img.com.cn/2014/baoban/IPhone-new-product-bg.png',
            6=>'http://icon.zol-img.com.cn/2014/baoban/newbbsbb-bg.png',
            7=>'http://icon.zol-img.com.cn/2014/baoban/baoban-20141121-bg.png',
            8=>'http://icon.zol-img.com.cn/2014/baoban/20141211-bb-bg.jpg',
         ),
    ),
    //by suhy
    '1000*180'=>array(
    		'name'=>'高度180包版(1000*180)',
    		'default'=>1,
    		'child'=>array(1=>'包版1',),
    		'bgurl'=>array(
    				1=>'http://icon.zol-img.com.cn/2014/baoban/jw-bb-20150306-bg.png',
    		),
    ),
    
    //by huangjialin
    '1000*140'=>array(
    		'name'=>'高度140包版(1000*140)',
    		'default'=>1,
    		'child'=>array(1=>'包版1',2=>'包版2',3=>'包版3',),
    		'bgurl'=>array(
    				1=>'http://icon.zol-img.com.cn/2015/baoban/jw-bb-20150313-bg.png',
    				2=>'http://icon.zol-img.com.cn/2014/baoban/20141211-bb-bg.jpg',
    				3=>'http://icon.zol-img.com.cn/mainpage/baoban/20151117/background.jpg',
    		),
    ),
    //by suhy 
    '1000*240'=>array(
    		'name'=>'高度240包版(1000*240)',
    		'default'=>1,
    		'child'=>array(1=>'包版1',2=>'包版2',3=>'包版3',4=>'包版4',5=>'包版5',6=>'包版6',),
    		'bgurl'=>array(
    				1=>'http://icon.zol-img.com.cn/2015/baoban/201505/jw-bb-20150520-bg.png',
    				2=>'http://icon.zol-img.com.cn/2015/baoban/201507/jw-bb-20150727-bg.png',
    				3=>'http://pic.zol-img.com.cn/2015/06/1433149054.png',
    				4=>'http://icon.zol-img.com.cn/2015/baoban/201509/bb-title-link.jpg',
    				6=>'http://icon.zol-img.com.cn/2015/baoban/201509/bb-title-link.jpg',
    		),
    ),
   /*  '980*110'=>array(
        'name'=>'高度110包版(980*110)',
        'default'=>1,
        'child'=>array(1=>'包版1',2=>'包版2',3=>'包版3'),
        'bgurl'=>array(
            1=>'http://icon.zol-img.com.cn/2013/baoban/1329989311.png',
            2=>'http://icon.zol-img.com.cn/2013/baoban/1329989882.png',
            3=>'http://icon.zol-img.com.cn/2013/baoban/1329990514.png',
         ),
    ),
     */
    '980*180'=>array(
    		'name'=>'高度180包版(980*180)',
    		'default'=>1,
    		'child'=>array(1=>'包版1'),
    		'bgurl'=>array(
    				1=>'http://icon.zol-img.com.cn/2014/baoban/jw-bb-20150306-bg.png',
    		),
    ),
    
    '760*90'=>array(
    		'name'=>'高度90包版(760*90)[专用于Z神通自媒体联盟左侧]',
    		'default'=>1,
    		'child'=>array(1=>'包版1'),
    		'bgurl'=>array(
    				1=>'http://icon.zol-img.com.cn/2013/baoban/1329989311.png',
    		),
    ),
    
   
);

//设置
$php_self = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?m=mod_topic';
$cat_href = '?m=mod_topic';

//新增包版选择
$select_bb = isset($_REQUEST['select_bb']) ? trim($_REQUEST['select_bb']) : 0;
$select_bb_arr = $config_bb_array[$select_bb]['child'];
$default_bb = $config_bb_array[$select_bb]['default'];//默认第几个包版
if (!$default_bb) {$default_bb = 1;}

//修改包版
$s_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'list';

//当前用户
$ad_uid = $_COOKIE['S_uid'];

/**
 * 判断id是否存在
 * @param unknown $id
 * @return boolean
 */
function check_id_in_no_publish($id){
	return  in_array($id, array(16,17,18,19,20));
}

if('changeposition' == $type){
	$id = (int)$_GET['id'];
	$position = (int)$_GET['position'];
	if($id && !check_id_in_no_publish($id)){
		$strsql = "update ".TABLE_TOPIC." set bb_position='{$position}'  where id='{$id}' and (bb_width='980' or bb_width='1000')";
		$db_doc->query($strsql);
	}
	echo '<script>';
	echo 'location.href = "index.php?m=mod_topic&type=list";';
	echo '</script>';
	exit;
}elseif ('list' == $type) {	
    //更新 $update 为包版id  $status状态:0-生效,1-未生效  $online_flag 上线使用文件(1.后备文件 0.首选文件)
    $update = isset($_REQUEST['update']) ? intval($_REQUEST['update']) : 0;
    $status = isset($_REQUEST['status']) ? intval($_REQUEST['status']) : 1;
    if ($update && in_array($ad_uid,$super_topic_user) && !check_id_in_no_publish($update)) {
    	
        $strsql = "select count('x') from ".TABLE_TOPIC." where status=0 and id!='{$update}'";
        if ($db_doc->get_var($strsql) > 1) {
            echo '<script>';
            echo 'alert("已同时有两个在线包版,请先将在线的设为无效");';
            echo 'location.href = "index.php?m=mod_topic&type=list";';
            echo '</script>';
            exit;
        } else {
            $online_flag = 0;
            //下线
            if (1 == $status) {

                $strsql = "select online_flag from ".TABLE_TOPIC." where id='{$update}'";
                $online_flag = $db_doc->get_var($strsql);
                //上线下下线 func_ad.php
                set_topic($status,$update,$online_flag);
                //更新
                $strsql = "update ".TABLE_TOPIC." set status='{$status}' , online_flag=0 where id='{$update}'";
                $db_doc->query($strsql);
  
            } else {
 
                $strsql = "select online_flag from ".TABLE_TOPIC." where status=0 and id!='{$update}'";
                $online_flag = $db_doc->get_var($strsql);
               
                if (null === $online_flag) {
                    $online_flag = 0;
                } else {
                    $online_flag = !$online_flag;
                }
                
                //上线下下线 func_ad.php
                set_topic($status,$update,$online_flag);
                //更新
                $strsql = "update ".TABLE_TOPIC." set status='{$status}',online_flag='{$online_flag}' where id='{$update}'";
                $db_doc->query($strsql);
            }
            //执行
            echo '<script>';
            echo 'location.href = "index.php?m=mod_topic&type=list";';
            echo '</script>';
            exit;
        }
    }
    //查询sql
    $strsql = "select * from ".TABLE_TOPIC;
    $where = '';
    if (!in_array($ad_uid,$super_topic_user)) {
        $where = " where person like'%{$ad_uid},%'";
    }
    //读取总数 
    $total_strsql = "select count(*) from ".TABLE_TOPIC.$where;
    $total_num = $db_doc->get_var($total_strsql);
    
    //分页
    $pagesize = isset($_REQUEST['pagesize']) ? $_REQUEST['pagesize'] : 50;
    $pagenum = isset($_REQUEST['pagenum']) ? $_REQUEST['pagenum'] : 1;
    if ($total_num) {
        $pagenum_total = ceil($total_num/$pagesize);
        $pagenum = $pagenum<1 ? 1 : $pagenum;
        $pagesize = $pagesize<0 ? 0 : $pagesize;
    }
    $strsql .= $where." order by id desc";
    if ($pagenum_total) {
        $strsql .= ' limit '.($pagenum-1)*$pagesize.','.$pagesize;
    }
    $res = $db_doc->get_results($strsql,'O');
    
} else if ('mod' == $type) {//修改
    $strsql = "select * from ".TABLE_TOPIC." where id='{$s_id}'";
    $r_row = $db_doc->get_row($strsql,'O');
    $s_width = $r_row->bb_width;
    $s_height = $r_row->bb_height;
    $s_arr = $config_bb_array[$s_width.'*'.$s_height];
    //$s_name = $s_arr['name'].' '.$s_arr['child'][$r_row->bb_num];
    $s_name = $s_width.'*'.$s_height.' '.$s_arr['child'][$r_row->bb_num];
} else if ('create_bg' == $type) {//生成背景预览图 2011-05-27
    if ($config_bb_array) {
        $str = '<style>.bg_a{color:#006699;text-decoration:none;} .bg_a a:hover,.bg_a a:link,.bg_a a:visited{color:#006699}</style>';
        foreach ($config_bb_array as $key=>$config_bb) {
            list($w,$h) = explode('*',$key);
            $str .= '<h1 style="color:red;clear:left;">'.$config_bb['name'].'</h2>';
            if ($config_bb['child']) {
                foreach ($config_bb['child'] as $num=>$row) {
                    $bg_url = $config_bb['bgurl'][$num];
                    $str .= '
                    <div style="float:left;clear:left;">
                        '.$key.' 第'.$num.'版'.'<br />
                        <a href="'.$bg_url.'" target="_blank" class="bg_a">[下载背景模板]</a>
                    </div>
                    <div style="float:left;">
                        <iframe frameborder="0" scrolling="No" width="'.$w.'px" height="'.($h+50).'px" src="./temp/views/preview/topic_preview.php?width='.$w.'&height='.$h.'&num='.$num.'&notip=1"></iframe>
                    </div>
                    ';
                }
            }
        }
        
        $filename_bg = dirname(__FILE__).'/topic_bg.html';

        $fp = fopen($filename_bg,'w+');
        fputs($fp,$str);
        fclose($fp);
        chmod($filename_bg,0777);
    }
    exit;//退出
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>首页包版管理</title>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<style type="text/css">
    body{font-size:12px;text-align:center;}
	#main{border:1px solid #D0DCF0;width:1200px;text-align:left;padding:5px;margin:0 auto;height:1000px;}
	#main .d a{text-decoration:none;color:#000;}
	.mt10{margin-top:10px;}
	#div_list th,#div_list td{border:1px solid #bbb;height:20px; line-height:20px;}
</style>
<script language="JavaScript" type="text/javascript">
/*构造ajax对象*/
function AjaxObject(){
    var request = null;
    try{
        request = new ActiveXObject("Msxml2.XMLHTTP");
    }catch (e){
        try{
            request = new ActiveXObject("Microsoft.XMLHTTP");
        }catch (oc){
            request = null;
        }
    }
    if (!request && typeof XMLHttpRequest != "undefined"){
        try{
            request =  new XMLHttpRequest();
        }catch (fa){
            alert("抱歉，您的浏览器不支持这个功能，请选择IE 6.0或FireFox浏览器。")
            request = null;
        }
    }
    return request;
}
function ajax_category() {
    //分类
    var channel_id = document.getElementById('channel_id').value;
    if (channel_id == '') {
        if (null !== document.getElementById('category')) {
            document.getElementById('category').value = '';
        }
        return;
    }
    //发送ajax 生成demo页
    var http_request = AjaxObject();
    var url = 'admin_ajax.php?type=category&cid='+channel_id+'&select=<?php echo $category;?>&r='+Math.random();
	http_request.onreadystatechange = function(){
		if (http_request.readyState == 4) {
			if (http_request.status == 400) {
				alert("There was a problem with the request.");
			} else {
			    var ret = http_request.responseText;
			    document.getElementById('sp_category').innerHTML = ret;
			}
		}
	}
	http_request.open("GET", url, true);
	http_request.send(null);
}
function check_submit() {

    var msg = '';
    if (document.getElementById('newcomment').value == '') {
        msg += "请输入评论内容!\r\n";
    }
    if (msg) {
        alert(msg);
        return false;
    }
    return true;
}
//改变包版预览iframe和增加iframe地址
function change_iframe(wh,num) {
    var wh_arr = wh.split('*');
    if (wh_arr.length !=2) {
        return;
    }
    //包版宽高
    var width = parseInt(wh_arr[0],10);
    var height = parseInt(wh_arr[1],10);
    //该宽高下第几个
    num = parseInt(num,10);
    if (height && num) {
        var view_iframe_src = './temp/views/preview/topic_preview.php?width='+width+'&height='+height+'&num='+num;
        var add_iframe_src = './temp/views/preview/topic_save.php?width='+width+'&height='+height+'&num='+num;
        var prev_obj = document.getElementById('prev');
        if (0 != prev_obj.value) {
            //包版切换时提示
//            if (!confirm('切换包版选项时,将不会保存当前包版信息,确认继续吗?')) {
//                return;
//            }
            //去掉前一个act状态
            document.getElementById('bb_tab'+prev_obj.value).className = 'd';
        }
        //增加自身act状态
        document.getElementById('bb_tab'+num).className = 'd act';
        prev_obj.value = num;
        //改变iframe地址
        document.getElementById('view_iframe').height = (height+20)+'px';
        document.getElementById('view_iframe').src = view_iframe_src+'&r='+Math.random();
        document.getElementById('add_iframe').src = add_iframe_src+'&r='+Math.random();
    } else {
        alert('参数有误!');
    }
}
</script>
</head>
<body><!-- onload="set_default();"-->

<style type="text/css">
.a{float:left; border-width:1px 0; border-color:#bbbbbb; border-style:solid;}
.b{height:22px; border-width:0 1px; border-color:#bbbbbb; border-style:solid; margin:0 -1px; background:#e3e3e3; position:relative; float:left;}
.c{line-height:10px; color:#f9f9f9; background:#f9f9f9; border-bottom:2px solid #eeeeee;}
.d{padding:0 8px; line-height:22px; font-size:12px; color:#000000; clear:both; margin-top:-12px; cursor:pointer;display:block;text-decoration:none;}
.act{background:orange;}
body{padding:0px;margin:0px;}
</style>
    <div id="main" style="position:relative;">
        <div style="position:absolute;top:1px;left:-100px;">
<!--            <h3>首页包版管理</h3>-->
        </div>
        <div>
            <div class="a"><div class="b"><div class="c">&nbsp;</div><div class="d <?php echo 'list' == $type ? 'act': '';?>"><a href="<?php echo $php_self;?>&type=list">首页包版列表</a></div></div></div>
            <?php
                if ($s_id) {
            ?>
            <div class="a"><div class="b"><div class="c">&nbsp;</div><div class="d <?php echo 'mod' == $type ? 'act': '';?>"><a href="<?php echo $php_self;?>&type=mod&id=<?php echo $s_id;?>">修改ID:<?php echo $s_id;?></a>&nbsp;[<?php echo $s_name;?>]</div></div></div>
            <?php
                } else {
                    //特定人员可以新增
                    if (in_array($ad_uid,$super_topic_user)) {
            ?>
            
            <div class="a"><div class="b"><div class="c">&nbsp;</div><div class="d <?php echo 'add' == $type ? 'act': '';?>"><a href="<?php echo $php_self;?>&type=add">新增</a><?php echo $select_bb ? ('-'.$config_bb_array[$select_bb]['name'].'<span style="font-size:9px;">[<a href="'.$php_self.'&type=add">重新选择</a>]</span>') : '';?></div></div></div>
            <?php
                    }
                }
            ?>
        </div>
        <br clear="all" />
        <?php
            if ('add' == $type) {
        ?>
                <div id="div_add" style="margin-left:20px;height:100%;margin-top:10px;">
                    <?php
                        if ($select_bb_arr) {
                    ?>
                        <input type="hidden" id="prev" value="0" />
                        <?php
                            //包版选择选项卡
                            foreach ($select_bb_arr as $key=>$val) {
                        ?>
                        <div class="a"><div class="b"><div class="c">&nbsp;</div><div class="d" id="bb_tab<?php echo $key;?>"onclick="change_iframe('<?php echo $select_bb;?>','<?php echo $key;?>');"><?php echo $val;?></div></div></div>
                        <?php
                            }
                        ?>
                        <br clear="all" />
                        <div style=" border:1px solid #bbb;margin-top:-1px;margin-left:-1px;">
                            <iframe id="view_iframe" frameborder="0" scrolling="No" src="#" width="100%"></iframe>
                        </div>
                        
                        <div style="margin-left:-1px;" class="mt10">
                            <iframe id="add_iframe" frameborder="0" scrolling="No" src="#" width="100%" height="800px"></iframe>
                        </div>
                    <?php        
                        } else {
                            echo '<div style="height:200px;text-align:center;padding-top:10%;">';
                            foreach ($config_bb_array as $key=>$val) {
                    ?>
                                <div class="a" style="margin-right:10px;">
                                    <div class="b">
                                        <div class="c">&nbsp;</div>
                                        <a class="d" href="<?php echo $php_self.'&type=add&select_bb='.$key;?>"><?php echo $val['name'];?></a>
                                    </div>
                                </div>
                    <?php
                            }
                            echo '</div>';
                        }
                    ?>
                </div>
        <?php
            } else if ('mod' == $type) {
        ?>
                <!--<div style="float:right;">
                    <a href="#" onclick="document.getElementById('view_iframe').src=document.getElementById('view_iframe').src;return false;">点击刷新预览</a>
                </div>-->
                <div id="div_add" style="margin-left:20px;height:100%;margin-top:10px;">
                    <br clear="all" />
                    <div style=" border:1px solid #bbb;margin-top:-1px;margin-left:-1px;">
                        <iframe id="view_iframe" frameborder="0" scrolling="No" src="./temp/views/preview/topic_preview.php?id=<?php echo $s_id;?>" width="100%" height="<?php echo $s_height+20;?>px"></iframe>
                    </div>
                    
                    <div style="margin-left:-1px;" class="mt10">
                        <iframe id="add_iframe" frameborder="0" scrolling="No" src="./temp/views/preview/topic_save.php?id=<?php echo $s_id;?>" width="100%" height="800px"></iframe>
                    </div>
                </div>
        <?php
            } else if ('list' == $type) {
        ?>
        <br clear="all" />
        <div class="mt10" id="div_list">
            <table cellpadding="2" cellspacing="0" border="1" style="border:1px solid #bbb;">
                <tr>
                    <th width="25px">&nbsp;</th>
                    <th width="190px">标题</th>
                    <th width="90px" align="center">版本</th>
                    <th width="210px" align="center">执行时间</th>
                    <th width="30px" align="center">状态</th>
                    <th width="330px" align="center">操作</th>
                </tr>
                <?php
                    if ($res) {
                        foreach ($res as $key=>$row) {
                            //其它人看不到测试的
                            if ($row->title == '测试测试') {
                                if ('shentong' == $_COOKIE['S_uid']) {
                                    continue;
                                } else {
                                    continue;
                                }
                            }
                            $tip_str = "以下人员有权限:".str_replace(',',"&nbsp;/&nbsp;",rtrim($row->person,','));
                ?>
                <tr title="<?php echo $tip_str;?>" onmouseover="this.style.backgroundColor='#D9D9D9';" onmouseout="this.style.backgroundColor='<?php echo $row->status ? '#ccc' : '';?>';" style="<?php echo $row->status ? 'background-color:#ccc;' : '';?>">
                    <td align="center"><?php echo $total_num - ($pagenum-1)*$pagesize - $key;?></td>
                    <td><?php echo '<a href="'.$row->link.'" target="_blank">'.$row->title.'</a>';?>&nbsp;</td>
                    <td align="center"><?php echo $row->bb_width.'*'.$row->bb_height.' 第'.$row->bb_num.'版';?></td>
                    <td align="center"><?php echo $row->starttime.' '.(strlen($row->starthour)==1 ? '0'.$row->starthour : $row->starthour).'点 - '.$row->endtime.' '.(strlen($row->endhour)==1 ? '0'.$row->endhour : $row->endhour).'点';?>&nbsp;</td>
                    <td align="center"><?php echo $row->status ? '<i>无效</i>' : '<span style="color:red;">有效</span>';?></td>
                    <td align="center">
                    <?php
                    if (in_array($ad_uid,$super_topic_user) && !check_id_in_no_publish($row->id)) {
                    ?>
                        <a href="<?php echo $php_self.'&type=list&update='.$row->id;?>&status=<?php echo $row->status ? 0 : 1;?>" <?php if ($row->status) {?> onclick='return confirm("慎重!慎重!慎重! \r\n<?php echo $row->title;?>,上线后预览发布即可生效,确认内容已填写完成吗?");' <?php }?>><?php echo $row->status ? '上线' : '下线';?></a>     
                    <?php
                    }
                    ?>
                    &nbsp;
                    <a href="<?php echo $php_self.'&type=mod&id='.$row->id;?>">修改</a>    
                    <?php 
                    	//if($ad_uid=='huangjl'){
                    	
                    
                    ?>
                      &nbsp;
                  	<a href="<?php echo './temp/views/preview/topic_preview.php?id='.$row->id.'&all=1';?>" target="_blank">整体预览</a>
                  	<?php 
						//}
                  	
                  	?>
                  	<?php 
                  		if(in_array($ad_uid,$super_topic_user) && ($row->bb_width==980 || $row->bb_width==1000) && !check_id_in_no_publish($row->id)){#通栏才能上下移动
								
                  	?>
                  	 &nbsp;     
                    <a href="<?php echo $php_self.'&type=changeposition&id='.$row->id;?>&position=<?php echo $row->bb_position==1 ? 2 : 1;?>" onclick='return confirm("你确定<?php echo $row->bb_position==1 ? '下移' : '上移';?>操作码？操作后请重发布首页生效\r\n")'><?php echo $row->bb_position==1 ? '下移' : '上移';?></a> 
                  	
                  	<?php }?>
                    &nbsp;     
                    <a href="<?php echo './temp/views/preview/topic_preview.php?id='.$row->id;?>" target="_blank">只包版预览</a>     
                    </td>
                </tr>
                <?php
                        }
                    }
                ?>
            </table>
            <div class="mt10">
                <?php
                    for($i=1;$i<=$pagenum_total;$i++) {
                        if ($pagenum == $i) {
                ?>
                            <strong><?php echo $i;?></strong>
                <?php
                        } else {
                            if ($i%20 == 0) echo '<br />';//超过10个
                ?>
                            <a href="<?php echo $cat_href.'&pagenum='.$i;?>">[<?php echo $i;?>]</a>
                <?php
                        }
                    }
                ?>
                <?php
                    if ($pagenum < $pagenum_total) {
                ?>
                <a href="<?php echo $cat_href.'&pagenum='.($pagenum+1);?>">[下一页]</a>
                <?php
                    }
                ?>
                <?php
                    if ($total_num) {
                ?>
                共<?php echo $total_num;?>个/<?php echo $pagenum_total;?>页
                <?php
                    }
                ?>
                <a href="http://admin.zol.com.cn/mainpage/index_manage_2013/index.php?m=mod_topic">13版列表</a>
            </div>
        </div>
        <?php
            }
        ?>
        <?php
            if (in_array($ad_uid,$super_topic_user)) {
        ?>
        <div style="border:2px solid #eee;padding:5px;color:#666;margin-top:100px;" class="mt10">
        以下管理员可见:<br /><br />
        1.拥有自动滚动包版上线后,点击<a href="cron/publish_10_min.php" target="_blank">这里</a>立即刷新滚动内容缓存(缓存每10分钟会自动刷新)
        <br />
        <br />
        2.查找有没有喜欢的包版,查找背景图什么限制,点<a href="topic_bg.html" target="_blank">这里</a>
        <br />
        <br />
        3.其它更新<a href="?m=mod_topic&type=create_bg" target="_blank">这里</a>
        </div>
        <?php
            }
        ?>
    </div>
<?php
    //添加时设置默认
    if ('add' == $type) {
?>
<script>
change_iframe('<?php echo $select_bb;?>',<?php echo $default_bb;?>);
</script>
<?php
    }
?>
</body>
</html>
