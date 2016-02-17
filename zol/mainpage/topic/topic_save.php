<?php
/**
 * 包版模板配置文件
 *
 * LICENSE:
 * @author 沈通 shen.tong@zol.com.cn
 * @version 1.0
 * @copyright  http://www.zol.com.cn
 * @todo
 * @changelog 
 * 2011-03-30 created by shen.tong@zol.com.cn
 */
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__).'/../../../'));
define('INCLUDE_PATH', ROOT . DS . 'include' . DS);
require_once INCLUDE_PATH . 'global.php';
require_once INCLUDE_PATH . 'func_ad.php';
//240第一版对应module_arr信息
$module_total_arr = array(
    '980*240*1'=>array(
        '351'=>array('name'=>'左大黑字','color'=>'#CC0000'),
        '352'=>array('name'=>'左大黑字下文字链'),
        '353'=>array('name'=>'左图文字链(80*60)'),
        '354'=>array('name'=>'右图上导读字','color'=>'#000000'),
        '355'=>array('name'=>'右图文字链(160*90)','color'=>'#333333'),
        '356'=>array('name'=>'右图右侧文字链'),
        '357'=>array('name'=>'底部导读字','color'=>'#000000'),
        '358'=>array('name'=>'滚动文字链','color'=>'#333333','check_scroll'=>1),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
    '980*240*2'=>array(
        '367'=>array('name'=>'左大黑字','color'=>'#CC0000'),
        '368'=>array('name'=>'左大黑字下文字链'),
        '369'=>array('name'=>'右图文字链(80*60)','color'=>'#333333'),
        '370'=>array('name'=>'底部导读字','color'=>'#000000'),
        '371'=>array('name'=>'滚动文字链','color'=>'#333333','check_scroll'=>1),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
    '980*240*3'=>array(
        '377'=>array('name'=>'标签1-导读字'),
        '378'=>array('name'=>'标签1-左大黑字','color'=>'#CC0000'),
        '379'=>array('name'=>'标签1-左大黑字下文字链'),
        '380'=>array('name'=>'标签1-右图文字链(80*60)','color'=>'#333333'),
        
        '381'=>array('name'=>'标签2-导读字'),
        '382'=>array('name'=>'标签2-左大黑字'),
        '383'=>array('name'=>'标签2-左大黑字下文字链'),
        '384'=>array('name'=>'标签2-右图文字链(80*60)'),
        
        '385'=>array('name'=>'标签3-导读字'),
        '386'=>array('name'=>'标签3-左大黑字'),
        '387'=>array('name'=>'标签3-左大黑字下文字链'),
        '388'=>array('name'=>'标签3-右图文字链(80*60)'),
        
        '389'=>array('name'=>'底部左侧字','color'=>'#000000'),
        '390'=>array('name'=>'滚动文字链','color'=>'#333333','check_scroll'=>1),
        '391'=>array('name'=>'底部右侧字','color'=>'#000000'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*4'=>array(
        '407'=>array('name'=>'左图文字链(180*120)','color'=>'#333333'),
        '408'=>array('name'=>'中间大黑字','color'=>'#CC0000'),
        '409'=>array('name'=>'中间大黑字下文字链'),
        '410'=>array('name'=>'右上导读字','color'=>'#000000'),
        '411'=>array('name'=>'右图文字链(200*60)','color'=>'#333333'),
        '412'=>array('name'=>'右图下文字链'),
        '413'=>array('name'=>'底部导读字','color'=>'#000000'),
        '414'=>array('name'=>'滚动文字链','color'=>'#333333','check_scroll'=>1),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*5'=>array(
        '912'=>array('name'=>'左图文字链(75*50)'),
        '913'=>array('name'=>'左侧底部切换标题','color'=>'#333333'),
        '914'=>array('name'=>'中间图文字链(240*180)'),
        
        '915'=>array('name'=>'右侧标签1导读字'),
        '916'=>array('name'=>'右侧标签1大黑字','color'=>'#CC0000'),
        '917'=>array('name'=>'右侧标签1大黑字下文字链'),
        
        '918'=>array('name'=>'右侧标签2导读字'),
        '919'=>array('name'=>'右侧标签2下图文(95*70)'),
        
        '920'=>array('name'=>'右侧标签3导读字'),
        '921'=>array('name'=>'右侧标签3下图文字链(110*82)'),
        
        '922'=>array('name'=>'底部导读字','color'=>'#ffffff'),
        '923'=>array('name'=>'滚动文字链','color'=>'#333333','check_scroll'=>1),
        
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*6'=>array(
     		'1211'=>array('name'=>'tab标题和右上角2个链接'),
     		'1195'=>array('name'=>'左侧图片'),
     		'1196'=>array('name'=>'左侧文字链'),
     		'1197'=>array('name'=>'tab1焦点图'),
     		'1198'=>array('name'=>'tab1右侧图片'),
     		'1199'=>array('name'=>'tab2小图'),
     		'1200'=>array('name'=>'tab2大图'),
     		'1201'=>array('name'=>'tab2(标题简介链接)'),
     		'1209'=>array('name'=>'tab3(投票图)'),
     		'1202'=>array('name'=>'tab4左侧(大标题链接)'),
     		'1204'=>array('name'=>'tab4左侧(小标题链接)'),
     		'1205'=>array('name'=>'tab4左侧下(标签文字链)'),
     		'1206'=>array('name'=>'tab4右切换1(图文链)'),
     		'1207'=>array('name'=>'tab4右切换2(图文链)'),
     		'1208'=>array('name'=>'tab4右切换3(图文链)'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*7'=>array(
     		'1243'=>array('name'=>'右上角两个链接'),
     		'1244'=>array('name'=>'左侧滚动图片部分'),
     		'1245'=>array('name'=>'左侧滚动文字链'),
     		'1246'=>array('name'=>'焦点图'),
     		'1247'=>array('name'=>'右侧图文链'),
     		
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*8'=>array(
     		'1253'=>array('name'=>'标签和右上角两个链接'),
     		'1254'=>array('name'=>'左侧滚动图片部分'),
     		'1255'=>array('name'=>'左侧滚动文字链'),
     		'1256'=>array('name'=>'tab1-焦点图'),
     		'1257'=>array('name'=>'tab1-右侧图文链'),
     		'1258'=>array('name'=>'tab2-左侧大红字标题'),
     		'1259'=>array('name'=>'tab2-左侧文字链'),
     		'1260'=>array('name'=>'tab2-左侧图文链'),
     		'1261'=>array('name'=>'tab2-右侧图文链'),
     		'1262'=>array('name'=>'tab3-图片文字简介'),
     		'1263'=>array('name'=>'tab4-图文链'),
     		'1264'=>array('name'=>'滚动文字链','check_scroll'=>1),
     		
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*9'=>array(
     		'1447'=>array('name'=>'左上角两个按钮'),
     		'1448'=>array('name'=>'左侧图文滚动'),
     		'1449'=>array('name'=>'图文切换'),
     		'1450'=>array('name'=>'右侧tab1 大黑字'),
     		'1451'=>array('name'=>'右侧tab1 大黑字下文字链'),
     		'1452'=>array('name'=>'右侧tab1 三条图文'),
     		'1453'=>array('name'=>'右侧tab2 图文'),
     		'1454'=>array('name'=>'右侧tab3 图文简介'),
     		'1455'=>array('name'=>'滚动文字链','check_scroll'=>1),
     		'1465'=>array('name'=>'左侧切换和右侧tab标签文字'), #2014-7-29 补加
     
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*10'=>array(
     		'1485'=>array('name'=>'中间tab1 图片'),
     		'1486'=>array('name'=>'中间tab2 大黑字'),
     		'1487'=>array('name'=>'中间tab2 大黑字下文字链'),
     		'1488'=>array('name'=>'中间tab3 图文'),
     		'1489'=>array('name'=>'中间tab4 图文'),
     		'1490'=>array('name'=>'右侧切换 图文'),
     		'1491'=>array('name'=>'右侧切换和中间tab标签文字'),
     		
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*11'=>array(
     		'1515'=>array('name'=>'左侧文字链【四条】'),
     		'1516'=>array('name'=>'顶部导航tab文字【四条】'),
     		'1517'=>array('name'=>'tab1左侧焦点图文字链【四条】'),
     		'1518'=>array('name'=>'tab1右侧tab切换标题【三条】'),
     		'1519'=>array('name'=>'tab1右侧tab第一组【三条】'),
     		'1520'=>array('name'=>'tab1右侧tab第二组【四条】'),
     		'1521'=>array('name'=>'tab1右侧tab第三组【四条】'),
     		'1522'=>array('name'=>'tab2右侧tab标题【三条】'),
     		'1523'=>array('name'=>'tab2左侧tab标题【三条】'),
     		'1524'=>array('name'=>'tab2右侧tab第一组【四条】'),
     		'1525'=>array('name'=>'tab2右侧tab第二组【四条】'),
     		'1526'=>array('name'=>'tab2右侧tab第三组【四条】'),
     		'1527'=>array('name'=>'tab2左侧tab第一组【三条】'),
     		'1528'=>array('name'=>'tab2左侧tab第二组【三条】'),
     		'1529'=>array('name'=>'tab2左侧tab第三组【三条】'),
     		'1530'=>array('name'=>'tab3左侧图片链接【一条】'),
     		'1531'=>array('name'=>'tab3左侧文字链接【三条】'),
     		'1532'=>array('name'=>'tab3右侧图片链接【四条】'),
     		'1533'=>array('name'=>'tab4图片轮播【15条】'),
     		 
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*240*12'=>array(
     		'1609'=>array('name'=>'ces-中间图'),
     		'1610'=>array('name'=>'ces-劲爆焦点-右侧图'),
     		'1611'=>array('name'=>'ces-左侧附图文字链'),
     		'1612'=>array('name'=>'ces-劲爆焦点-右侧红色头条'),
     		'1613'=>array('name'=>'ces-劲爆焦点-普通文字链'),
     		'1614'=>array('name'=>'ces-新品速评-右侧图组'),
     		'1615'=>array('name'=>'ces-发布直击-右侧图及描述'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     
     '980*240*13'=>array(
     		'1669'=>array('name'=>'中间大图'),
     		'1670'=>array('name'=>'右侧图文'),
     		'1668'=>array('name'=>'左侧附图文字链'),
     		
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     
     
     '980*140*1'=>array(
        '423'=>array('name'=>'大黑字','color'=>'#CC0000'),
        '424'=>array('name'=>'大黑字下文字链'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*140*2'=>array(
        '427'=>array('name'=>'左大黑字','color'=>'#CC0000'),
        '428'=>array('name'=>'左大黑字下文字链'),
        '429'=>array('name'=>'右图文字链(200*60)','color'=>'#333333'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*140*3'=>array(
        '433'=>array('name'=>'左大黑字','color'=>'#CC0000'),
        '434'=>array('name'=>'左大黑字下文字链'),
        '435'=>array('name'=>'右图文字链(80*60)','color'=>'#333333'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*140*4'=>array(
        '439'=>array('name'=>'大黑字','color'=>'#CC0000'),
        '440'=>array('name'=>'大黑字下文字链'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     
     '980*140*5'=>array(
     		'1506'=>array('name'=>'左侧进入专题和观看直播地址'),
     		'1507'=>array('name'=>'中间大黑字'),
     		'1508'=>array('name'=>'中间大黑字下方双链'),
     		'1510'=>array('name'=>'右侧图片文字双链'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     
     '980*140*6'=>array(
     		'1572'=>array('name'=>'左侧图文[3条]'),
     		'1573'=>array('name'=>'右侧大黑字[1条]'),
     		'1574'=>array('name'=>'右侧大黑字下双链[4条]')
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     

     '980*140*7'=>array(
     		'1580'=>array('name'=>'左侧大黑字[1条]'),
     		'1581'=>array('name'=>'左侧大黑字先文字链[4条]'),
     		'1582'=>array('name'=>'右侧图文链[99x69 8条]')
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     
     '980*140*8'=>array(
     		'1602'=>array('name'=>'顶部链接两条'),
     		'1603'=>array('name'=>'中间图文链接（最大40条）')
     ),//慎重
     
     '980*110*1'=>array(
        '443'=>array('name'=>'左图(200*60)'),
        '444'=>array('name'=>'右大黑字','color'=>'#CC0000'),
        '445'=>array('name'=>'右大黑字下文字链'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*110*2'=>array(
        '449'=>array('name'=>'左图(80*60)'),
        '450'=>array('name'=>'右大黑字','color'=>'#CC0000'),
        '451'=>array('name'=>'右大黑字下文字链'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '980*110*3'=>array(
        '455'=>array('name'=>'大黑字','color'=>'#CC0000'),
        '456'=>array('name'=>'大黑字下文字链'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     
     '760*90*1'=>array(
     		'1578'=>array('name'=>'测试一','color'=>'#CC0000'),
     		 
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     
     
     '980*180*1'=>array(
     		'1678'=>array('name'=>'中间图片切换文字链','color'=>'#CC0000'),
     		'1677'=>array('name'=>'中间文字链','color'=>'#CC0000'),
     		'1679'=>array('name'=>'中间大黑字','color'=>'#CC0000'),
     		'1680'=>array('name'=>'右侧两图片和链接','color'=>'#CC0000'),
     
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*180*1'=>array(
     		'1678'=>array('name'=>'中间图片切换文字链','color'=>'#CC0000'),
     		'1677'=>array('name'=>'中间文字链','color'=>'#CC0000'),
     		'1679'=>array('name'=>'中间大黑字','color'=>'#CC0000'),
     		'1680'=>array('name'=>'右侧两图片和链接','color'=>'#CC0000'),
     		 
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*140*1'=>array(
     		'1686'=>array('name'=>'1大黑字'),
     		'1687'=>array('name'=>'大黑字下四条'),
     		'1688'=>array('name'=>'右侧图片一条')
     
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*140*2'=>array(
     		'1602'=>array('name'=>'顶部链接两条'),
     		'1603'=>array('name'=>'中间图文链接（最大40条）')
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*140*3'=>array(
     		'1818'=>array('name'=>'左侧进入专题和观看直播地址'),
     		'1819'=>array('name'=>'中间大黑字'),
     		'1820'=>array('name'=>'中间大黑字下方双链'),
     		'1821'=>array('name'=>'右侧图片文字双链'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*240*1'=>array(
     		'1609'=>array('name'=>'ces-中间图'),
     		'1610'=>array('name'=>'ces-劲爆焦点-右侧图'),
     		'1611'=>array('name'=>'ces-左侧附图文字链'),
     		'1612'=>array('name'=>'ces-劲爆焦点-右侧红色头条'),
     		'1613'=>array('name'=>'ces-劲爆焦点-普通文字链'),
     		'1614'=>array('name'=>'ces-新品速评-右侧图组'),
     		'1615'=>array('name'=>'ces-发布直击-右侧图及描述'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*240*2'=>array(
     		'1758'=>array('name'=>'CJ-最新资讯-中间大图和6链接'),
     		'1759'=>array('name'=>'CJ-最新资讯-新闻-1大红字'),
     		'1760'=>array('name'=>'CJ-最新资讯-新闻-6普通文字链'),
     		'1761'=>array('name'=>'CJ-最新资讯-新闻-3图片'),
     		'1762'=>array('name'=>'CJ-最新资讯-下方滚动新闻'),
     		'1763'=>array('name'=>'CJ-最新资讯-游戏-6图片'),
     		'1764'=>array('name'=>'CJ-最新资讯-花絮-3(多)组图文'),
     		'1765'=>array('name'=>'CJ-BestInCJ-3(多)张轮播图'),
     		'1766'=>array('name'=>'CJ-热辣美模-6张(投票)图'),
     		'1767'=>array('name'=>'CJ-特卖互动-中间轮播焦点图'),
     		'1768'=>array('name'=>'CJ-特卖互动-右侧3组图'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*240*3'=>array(
     		'1243'=>array('name'=>'右上角两个链接'),
     		'1244'=>array('name'=>'左侧滚动图片部分'),
     		'1245'=>array('name'=>'左侧滚动文字链'),
     		'1246'=>array('name'=>'焦点图'),
     		'1247'=>array('name'=>'右侧图文链'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*240*4'=>array(
     		'1785'=>array('name'=>'左侧-二维码+文字+链接'),
     		'1786'=>array('name'=>'中间-5轮播图+链接'),
     		'1787'=>array('name'=>'右侧-图文链接'),
     		'1788'=>array('name'=>'右侧下方-更多按钮+链接'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*240*5'=>array(
     		'351'=>array('name'=>'左大黑字','color'=>'#CC0000'),
     		'352'=>array('name'=>'左大黑字下文字链'),
     		'353'=>array('name'=>'左图文字链(80*60)'),
     		'354'=>array('name'=>'右图上导读字','color'=>'#000000'),
     		'355'=>array('name'=>'右图文字链(160*90)','color'=>'#333333'),
     		'356'=>array('name'=>'右图右侧文字链'),
     		'357'=>array('name'=>'底部导读字','color'=>'#000000'),
     		'358'=>array('name'=>'滚动文字链','color'=>'#333333','check_scroll'=>1),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
     '1000*240*6'=>array(
     		'1835'=>array('name'=>'1000*240-中间普通文字链','color'=>'#CC0000'),
     		'1836'=>array('name'=>'1000*240-中间红色粗体文字链'),
     		'1837'=>array('name'=>'1000*240-中间普通文字链'),
     		'1838'=>array('name'=>'1000*240-右侧图片','color'=>'#000000'),
     ),//慎重:*****模块ID若要修改: 1.添加新手工模块,得到新ID 2.手工写SQL执行,更新TABLE_TOPIC_MODULE
);

//从数据库中读一些分类信息,以上的$config_bb_array数组 暂时不用，作为备份   by suhy 2016-02-16
//$sql = 'select * from index_topic_category where parent_id=0 limit 100';
//$res = $db_doc->get_results($sql);



//颜色配置
$color_arr = array(
    '#CC0000'=>'红色',
    '#000000'=>'黑色',
    '#333333'=>'深灰色',
    
    '#134BA0'=>'红色',
    '#651924'=>'暗红色',
    '#484848'=>'灰色',
    '#474747'=>'浅灰色',
    '#666666'=>'淡灰色',
    '#878787'=>'淡灰色2',
    '#ffffff'=>'白色',
    '#FFFFCC'=>'淡黄色',
);
//参数
$s_width = isset($_REQUEST['width']) ? intval($_REQUEST['width']) : 0;
$s_height = isset($_REQUEST['height']) ? intval($_REQUEST['height']) : 0;
$s_num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : 0;

//来源
$from_url = $_SERVER['HTTP_REFERER'];

//当前用户
$ad_uid = $_COOKIE['S_uid'];

//是否管理员
$is_admin = in_array($ad_uid,$super_topic_user);

//修改
$s_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
if ($s_id) {
    //当前顺序
    $strsql = "select * from ".TABLE_TOPIC." where id='{$s_id}'";
    $r_row = $db_doc->get_row($strsql,'O');
    //所属
    $s_width = $r_row->bb_width;
    $s_height = $r_row->bb_height;
    $s_num = $r_row->bb_num;
    $s_person = $r_row->person;
    if (!$is_admin && false === strpos($s_person,$ad_uid.',')) {
        die('当前查看ID信息不属于您');
    }
//    if ($s_width != $f_width || $s_height != $f_height || $s_num != $f_num) {
//        die('当前查看ID信息不符合相应版本');
//    }
    $title = $r_row->title;
    $link = $r_row->link;
    $pic = $r_row->pic;
    $f_pic = $pic;//现在
    $del_pic = $r_row->del_pic;//已删除
    $starttime = $r_row->starttime;//
    $starthour = $r_row->starthour;
    $endtime = $r_row->endtime;
    $endhour = $r_row->endhour;
    $person = $r_row->person;
    $is_online = !$r_row->status;
    
    //所具有模块
    $strsql = "select * from ".TABLE_TOPIC_MODULE." where tid='{$s_id}'";
    $res = $db_doc->get_results($strsql,'O');
    $module_arr = array();
    if ($res) {
        foreach ($res as $row) {
            $module_arr[$row->module_id] = $row;
        }
    }
}
$m_arr = $module_total_arr[$s_width.'*'.$s_height.'*'.$s_num];
if (!$m_arr) {
    die('参数有误,未取到相关内容');
}

$act = isset($_POST['act']) ? intval($_POST['act']) : 0;
//提交时
if ($act) {
    $title = $_POST['title'];
    $link = $_POST['link'];
    $pic = $_POST['pic'];
    $f_pic = $_POST['f_pic'];
    $del_pic = $_POST['del_pic'];
    //修改时 更新模块配置用
    $p_color_arr = $_POST['color'];
    $p_scroll_auto = $_POST['scroll_auto'];
    $p_scroll_id_str = $_POST['scroll_id_str'];
    
    //人员权限
    $post_person_arr = $_POST['person_arr'];//选择后原来人员
    $person = trim($_POST['person']);//新人员
    $new_person_arr = array();
    if ($person) {
        $new_person_arr = explode(',',$person);
    }
    if($new_person_arr){
    	foreach ($new_person_arr as $key=>$val){
    		$new_person_arr[$key] = trim($val);
    	}
    }
    if (!$post_person_arr) {
        $post_person_arr = array();
    }
    $person_arr = array_unique(array_merge($post_person_arr,$new_person_arr));//组合的唯一的
    $person = implode(',',$person_arr).',';//组合的sql人员串
    
    //删除人员的数组
    $f_person = $_POST['f_person'];//原来人员
    $f_person_arr = array();//原有人员数组 ----> 删除人员数组
    if ($f_person) {
        $f_person_arr = explode(',',rtrim($f_person,','));
        $f_person_arr = array_diff($f_person_arr,$post_person_arr);//array_diff注意顺序
    }
    //执行时间
    $starttime = $_POST['starttime'];
    $endtime = $_POST['endtime'];
    $starthour = $_POST['starthour'];
    $endhour = $_POST['endhour'];
    if ($starttime > $endtime) {
        die('时间异常:'.$starttime.'__'.$endtime);
    } else {
        if ($starttime == $endtime && $starthour > $endhour) {
            die('时间异常:'.$starttime.'__'.$endtime);
        }
    }
    
    //修改
    if ($s_id) {
        if ($f_pic && $f_pic != $pic) {
            $del_pic .= ','.$f_pic;
        }
        $strsql = "update ".TABLE_TOPIC." set 
            title='{$title}',
            link='{$link}',
            pic='{$pic}',
            del_pic='{$del_pic}',
            starttime='{$starttime}',
            starthour='{$starthour}',
            endtime='{$endtime}',
            endhour='{$endhour}',
            
            person='{$person}'
            where id='{$s_id}'
        ";
    } else {
        $strsql = "insert into ".TABLE_TOPIC."
        (title,link,pic,starttime,starthour,endtime,endhour,person,bb_width,bb_height,bb_num,input_person,input_time) 
        values
        ('{$title}','{$link}','{$pic}','{$starttime}','{$starthour}','{$endtime}','{$endhour}','{$person}','{$s_width}','{$s_height}','{$s_num}','{$_COOKIE['S_uid']}','".date('Y-m-d H:i:s')."')";
    }
    if ('shentong' == $_COOKIE['S_uid']) {
        //echo $strsql;
    }
    $db_doc->query($strsql);
    
    if (!$s_id) {
        $s_id = $db_doc->last_insert_id();
        foreach ($m_arr as $mid=>$row) {
            //默认
            $mname = $row['name'];
            $mcolor = $row['color'];
            
            //获取模板模块信息
            $strsql = "select * from ".TABLE_MODULE." where module_id='{$mid}'";
            $m_row = $db_doc->get_row($strsql,'O');
            if ($m_row) {
                //生成新模块 复制
                $insert_sql = "insert into ".TABLE_MODULE." set module_name='版本".$s_width.'*'.$s_height.'*'.$s_num.'-'.$mname."'";
                foreach ($m_row as $key=>$val) {
                    if ('module_name' == $key || 'module_id' == $key) continue;
                    $insert_sql .= ",`{$key}` = '{$val}'";
                }
                $db_doc->query($insert_sql);
                
                //新模块ID
                $new_mid = $db_doc->last_insert_id();
                
                //插入
                $strsql = "insert into ".TABLE_TOPIC_MODULE."
                    (tid,new_module_id,module_id,color) 
                    values
                    ('{$s_id}','{$new_mid}','{$mid}','{$mcolor}')";
                $db_doc->query($strsql);
                
                //设置人员权限
                if ($new_person_arr) {
                    foreach ($new_person_arr as $user_id) {
                        $strsql = "Insert into " . TABLE_PERMISSION . " (module_id,userid) values ('{$new_mid}','{$user_id}')";
                        $db_doc->query($strsql);
                    }
                }
            } else {
                die('模块ID:'.$mid.'获取失败,请查找原因后再行操作');
            }
        }
    } else {
        
        //所具有模块
        $strsql = "select * from ".TABLE_TOPIC_MODULE." where tid='{$s_id}'";
        $res = $db_doc->get_results($strsql,'O');
        if ($res) {
            foreach ($res as $row) {
                
                $new_mid = $row->new_module_id;
                //更新配置
                $s_color = $p_color_arr[$new_mid];
                $s_scroll_auto = $p_scroll_auto[$new_mid];
                $s_scroll_id_str = $p_scroll_id_str[$new_mid];
                $strsql = "update ".TABLE_TOPIC_MODULE." set color='{$s_color}',scroll_auto='{$s_scroll_auto}',scroll_id_str='{$s_scroll_id_str}' where id='{$row->id}'";
                $db_doc->query($strsql);
                
                //设置人员权限
                if ($new_person_arr) {
                    foreach ($new_person_arr as $user_id) {
                        $strsql = "Insert into " . TABLE_PERMISSION . " (module_id,userid) values ('{$new_mid}','{$user_id}')";
                        $db_doc->query($strsql);
                    }
                }
                //设置人员权限
                if ($f_person_arr) {
                    foreach ($f_person_arr as $user_id) {
                        $strsql = "Delete From " . TABLE_PERMISSION . " Where userid='{$user_id}' and module_id='{$new_mid}'";
                        $db_doc->query($strsql);
                    }
                }
            }
        }
    }
    //插入新人员 更新权限缓存
    if ($new_person_arr) {
        foreach ($new_person_arr as $user_id) {
            get_cache($user_id,1);
        }
    }
    //删除旧人员 更新权限缓存
    if ($f_person_arr) {
        foreach ($f_person_arr as $user_id) {
            get_cache($user_id,1);
        }
    }
    //在线
    if ($is_online) {
        //得到上线所在版本
        $strsql = "select online_flag from ".TABLE_TOPIC." where id='{$s_id}'";
        $online_flag = $db_doc->get_var($strsql);
        //执行生成步骤
        set_topic(0,$s_id,$online_flag);
    }
    echo '<script>';
    echo 'parent.location.href = "../../../index.php?m=mod_topic&type=list";';
    echo '</script>';
    exit;
}


//默认小时
if (!isset($starthour)) {
    $starthour = date('H');
}
if (!isset($endhour)) {
    $endhour = 23;
}
if (!isset($starttime)) {
    $starttime = date('Y-m-d');
}
if (!isset($endtime)) {
    $endtime = date('Y-m-d');
}
?>
<!doctype html>
<html>
<head>
    <title>a</title>
    <meta charset="gbk" /> 
    <link type="text/css" rel="stylesheet" href="../../../tmp/js/lib/jscal2/css/jscal2.css" />
    <link type="text/css" rel="stylesheet" href="../../../tmp/js/lib/jscal2/css/border-radius.css" />
    <script src="../../../tmp/js/lib/jscal2/js/jscal2.js"></script>
    <script src="../../../tmp/js/lib/jscal2/js/lang/cn.js"></script>
    <script>
    	//注册时间选择器
        function reg_cal(type) {
            var trigger_id = input_id = '';
            if (type == 'start') {
                trigger_id = 'starttime';
                input_id = 'starttime';
            } else {
                trigger_id = 'endtime';
                input_id = 'endtime';
            }
            var cal = Calendar.setup({
                trigger    : trigger_id,
                inputField : input_id,
                onSelect   : function() { this.hide();}
            });
        }
        function check_form() {
            var err = '';
            if('' == document.myform.title.value){
                err += "请填写标题内容.\r\n";
            }
            if('' == document.myform.pic.value){
                err += "请上传背景图片.\r\n";
            }
            if('' == document.myform.starttime.value || '' == document.myform.endtime.value){
                err += "请选择开始时间与结束时间.\r\n";
            } else {
                if (document.myform.starttime.value > document.myform.endtime.value) {
                    err += "开始时间请小于等于结束时间.\r\n";
                } else {
                    //相同时间时判断小时
                    if (document.myform.starttime.value == document.myform.endtime.value) {
                        if (parseInt(document.myform.starthour.value,10) > parseInt(document.myform.endhour.value,10)) {
                            err += "同一天,开始小时请小于等于结束小时.\r\n";
                        }
                    }
                }
            }
            if (err) {
                alert(err);
                return false;
            }
            return true;
        }
    </script>
    <style type="text/css">
    	.btn{border:1px solid #aaa;color:#aaa; width:86px;}
    	body{font-size:12px;}
    	.tcon{ border:solid #bbb;border-width:1px 0 0 1px;height:300px;}
    	.tcon td{ border:solid #bbb;border-width:0 1px 1px 0;}
    	.tcon2{ border:solid #bbb;border-width:1px 0 0 1px;height:300px;}
    	.tcon2 td{ border:solid #bbb;border-width:0 1px 1px 0;}
    	.tcon .clear_right{ border-right:0px;}
    	.vmiddle{vertical-align:middle;}
    	.red{color:red;}
    </style>
</head>
<body>
<div style="float:left;width:380px;">
<form name="myform" action="" method="POST" onsubmit="return check_form();">
<input type="hidden" name="s_width" value="<?php echo $s_width;?>" />
<input type="hidden" name="s_height" value="<?php echo $s_height;?>" />
<input type="hidden" name="s_num" value="<?php echo $s_num;?>" />
<input type="hidden" name="s_id" value="<?php echo $s_id;?>" />
<input type="hidden" name="refer" value="<?php echo $from_url;?>" />
<table cellpadding="2" cellspacing="0" border="1" class="tcon" width="370px">
    <tr>
        <td width="60px" style="border-right:0px;" align="center">&nbsp;</td>
        <td style="height:25px; line-height:25px;font-weight:bold;font-size:20px;">基本配置&nbsp;&nbsp;&nbsp;<?php echo $is_online ? '<span class="red">此包版当前在线</span>':'<i style="color:#bbb;">此包版未上线</i>';?></td>
    </tr>
    <tr>
        <td align="right">标题:</td>
        <td><input type="text" name="title" value="<?php echo $title;?>" size="30" /></td>
    </tr>
    <tr>
        <td align="right">链接:</td>
        <td><input type="text" name="link" value="<?php echo $link;?>" size="30" /></td>
    </tr>
    <tr>
        <td align="right">背景:</td>
        <td>
            <?php
                if ($is_admin) {
            ?>
                    <input type="text" name="pic" id="pic" value="<?php echo $pic;?>" size="30" /><br />
                    <iframe src="../../../index.php?m=mod_upload&pid=pic" frameborder="0" width="300px" height="28px" scrolling="No" class="vmiddle"></iframe>
            <?php
                } else {
            ?>
                    <a href="<?php echo $pic;?>" target="_blank"><img src="http://icon.zol-img.com.cn/2011/picture.png" border="0" /></a>
                    <input type="hidden" name="pic" id="pic" value="<?php echo $pic;?>" size="30" /><br />
            <?php
                }
            ?>
            
            <input type="hidden" name="f_pic" value="<?php echo $pic;?>" />
            <input type="hidden" name="del_pic" value="<?php echo $del_pic;?>" />
            
        </td>
    </tr>
    <tr>
        <td align="right">开始时间:</td>
        <td>
            <?php
                if ($is_admin) {
            ?>
                    <input type="text" name="starttime" id="starttime" value="<?php echo $starttime;?>" size="10" readonly />
                    <select name="starthour">
                    <?php
                    for($i=0;$i<24;$i++) {
                    ?>
                    <option value="<?php echo $i;?>" <?php echo $i == $starthour ? 'selected' : '';?>><?php echo $i;?>点</option>
                    <?php
                    }
                    ?>
                    </select>
                    <script>
                    reg_cal('start');
                    </script>
            <?php
                } else {
            ?>
                    <?php echo $starttime.' '.$starthour.'点';?>
                    <input type="hidden" name="starttime" id="starttime" value="<?php echo $starttime;?>" size="10" readonly />
                    <input type="hidden" name="starthour" value="<?php echo $starthour;?>" size="10" readonly />
            <?php
                }
            ?>
        </td>
    </tr>
    <tr>
        <td align="right">结束时间:</td>
        <td>
            <?php
                if ($is_admin) {
            ?>
                    <input type="text" name="endtime" id="endtime" value="<?php echo $endtime;?>" size="10" readonly />
                    <select name="endhour">
                    <?php
                    for($i=0;$i<24;$i++) {
                    ?>
                    <option value="<?php echo $i;?>" <?php echo $i == $endhour ? 'selected' : '';?>><?php echo $i;?>点</option>
                    <?php
                    }
                    ?>
                    </select>
                    <script>
                    reg_cal('end');
                    </script>
            <?php
                } else {
            ?>
                    <?php echo $endtime.' '.$endhour.'点';?>
                    <input type="hidden" name="endtime" id="endtime" value="<?php echo $endtime;?>" size="10" readonly />
                    <input type="hidden" name="endhour" value="<?php echo $endhour;?>" size="10" readonly />
            <?php
                }
            ?>
        </td>
    </tr>
    <tr>
        <td align="right" height="200px" style="height:114px;">权限用户:
            <?php
                //普通人员不能看到
                if (in_array($ad_uid,$super_topic_user)) {
            ?>
            <br />
            <a href='../../../index.php?m=get_uid&name=沈通,沈通&type=0' target="_blank" style="color:#bbb;text-decoration:none;">查询</a>&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                }
            ?>
        </td>
        <td>
            <?php
                $person_arr = explode(',',$person);
                if ($person_arr) {
                    foreach ($person_arr as $key=>$val) {
                        if (!$val) continue;
                        if ($key && $key%3 == 0) {
                            echo '<br />';
                        }
            ?>
                        <input type="checkbox" name="person_arr[]" value="<?php echo $val;?>" checked <?php echo $is_admin ? '' : 'disabled';?>><?php echo $val;?>&nbsp;
                        <?php
                        //不是管理员时生成
                        if (!$is_admin) {
                        ?>
                        <input type="hidden" name="person_arr[]" value="<?php echo $val;?>" />
                        <?php
                        }
                        ?>

            <?php
                    }
                }
            ?><br />
            <?php
                //普通人员不能看到
                if (in_array($ad_uid,$super_topic_user)) {
            ?>
            <input type="text" name="person" value="" size="20" /><i>(多个用户名用逗号分割)</i>
            <?php
                }
            ?>
            <input type="hidden" name="f_person" value="<?php echo $person;?>" />
        </td>
    </tr>
</table>
</div>
<div style="float:left;">
<table cellpadding="0" cellspacing="0" border="0" class="tcon2" width="530px">
    <tr>
        <td width="160px" style="border-right:0px;height:25px; line-height:25px;font-weight:bold;font-size:20px;" align="center">模块配置</td>
        <td width="350px" align="right"><span style="color:#bbb;font-size:10px;">管理手工内容后 </span><a href="#" onclick="parent.document.getElementById('view_iframe').src=parent.document.getElementById('view_iframe').src;return false;">点击刷新预览</a>&nbsp;</td>
    </tr>
    <?php
        if ($s_id) {
            foreach ($m_arr as $mid=>$mrow) {
                $mname = $mrow['name'];//默认名称
                $default_color = $mrow['color'];//默认颜色
                $check_scroll = $mrow['check_scroll'];//是否滚动 滚动需选择是手工还是自动
                
                $temp_row = $module_arr[$mid];
                //重新覆盖mid
                $mid = $temp_row->new_module_id;
                $mcolor = $temp_row ? $temp_row->color : $default_color;
                $mscroll_auto = $temp_row->scroll_auto;
                $mscroll_id_str = $temp_row->scroll_id_str;
    ?>
    <tr onmouseover="this.style.backgroundColor='#ccc';" onmouseout="this.style.backgroundColor='';">
        <td height="26px">
        &nbsp;<?php echo '['.$mid.']'.$mname;?>
        </td>
        <td>
            <?php
            if ($default_color) {
            ?>
                <select name="color[<?php echo $mid;?>]" style="color:<?php echo $mcolor == '#ffffff' ? '#000' : $mcolor;?>;width:100px;">
                <?php
                    foreach ($color_arr as $color=>$name) {
                ?>
                <option value="<?php echo $color;?>" style="color:<?php echo $color;?>;background-color:#bbb;" <?php echo $color == $mcolor ? 'selected' : '';?>><?php echo $name;?></option>
                <?php
                    }
                ?>
                </select>字
            <?php
            } else {
            ?>
                <div style="float:left;width:118px;">&nbsp;</div>
            <?php  
            }
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <script>
            	function openwindow(url,name,iWidth,iHeight)
                {
                    var url; //转向网页的地址;
                    var name; //网页名称，可为空;
                    var iWidth; //弹出窗口的宽度;
                    var iHeight; //弹出窗口的高度;
                    var iTop = (window.screen.availHeight-30-iHeight)/2; //获得窗口的垂直位置;
                    var iLeft = (window.screen.availWidth-10-iWidth)/2; //获得窗口的水平位置;
                    window.open(url,name,'height='+iHeight+',,innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',toolbar=no,menubar=no,scrollbars=yes,resizeable=no,location=no,status=no');
                }
            </script>
            <input type="button" value="管理手工内容&nbsp;" class="btn" onclick="<?php echo false === strpos($s_person,$ad_uid.',') ? "alert('您还没开通此包版手工权限!');return false;" : '';?>var aa = openwindow('../../../index.php?m=mod_list&in_block=8&module_id=<?php echo $mid;?>&from=topic&r='+Math.random(),'window',960,600);" /><!--parent.document.getElementById('view_iframe').src=parent.document.getElementById('view_iframe').src;-->
<!--            <input type="button" value="管理手工内容" class="btn" onclick="var aa = window.showModalDialog('../../../index.php?m=mod_list&module_id=<?php echo $mid;?>&from=topic&r='+Math.random(),window,'dialogWidth=800px;');parent.document.getElementById('view_iframe').src=parent.document.getElementById('view_iframe').src;" />-->
<!--            <input type="button" value="管理手工内容" class="btn" onclick="var aa = window.showModalDialog('../../../test2.php?&r='+Math.random(),window,'dialogWidth=800px;');parent.document.getElementById('view_iframe').src=parent.document.getElementById('view_iframe').src;" />-->
            <?php
            if ($check_scroll) {
            ?>
                <br />
                <input type="radio" name="scroll_auto[<?php echo $mid;?>]" value="0" <?php echo $mscroll_auto == 0 ? 'checked' : '';?>>手工内容
                <br />
                <input type="radio" name="scroll_auto[<?php echo $mid;?>]" id="scroll_auto_<?php echo $mid;?>" value="1" <?php echo $mscroll_auto == 1 ? 'checked' : '';?>>自动ID
                <input type="text" onfocus="document.getElementById('scroll_auto_<?php echo $mid;?>').checked = true;" name="scroll_id_str[<?php echo $mid;?>]" value="<?php echo $mscroll_id_str;?>" size="10"><font style="color:#666;">多个逗号分割<br/>&nbsp;&nbsp;&nbsp;&nbsp;ID为文章扩展标签的ID,如为频道关键字，开头加个k，</font>
            <?php
            }
            ?>
        </td>
    </tr>
    <?php
            }
        } else {
    ?>
        <tr><td colspan="2" class="red" align="center" style="height:290px;"><?php echo '请先保存基本配置';?></td></tr>
    <?php   
        }
    ?>
    
</table>
</div>

<br clear="all" />
<hr />
<div style="float:right;">
    <a href="#" onclick="parent.document.getElementById('view_iframe').src=parent.document.getElementById('view_iframe').src;return false;">点击刷新预览</a>
</div>
<div style="text-align:center;">
    <input type="hidden" name="act" value="1" />
    <input type="submit" name="sub" value="&nbsp;保存&nbsp;" />
</div>
</form>
</body>