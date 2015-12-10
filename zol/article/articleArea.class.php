<?php

/* * ************************************************
 *  文章页面底部和右侧区域发布程序，不能从外部直接调用
 *  马伟然，仲伟涛
 *  2010-12
 * ************************************************ */
include_once("/www/article/html/admin/include/kind_cache.php");
include "/www/article/html/template/new/include/app_cfg.php";
include "/www/article/html/template/new/template_engine/ZolCmsTemplate.php"; //模板引擎
include '/www/article/html/template/new/template_engine/zol_template_func.php'; //模版公用函数
include_once("/www/article/html/admin/include/baidu_recommend.php");
set_time_limit(0);
//var_dump($baiduRecommendConf);
//error_reporting(0);
/* 传递的参数
  $array = array (
  'classId' => '74',
  'className' => '手机频道',
  'url' => 'mobile.zol.com.cn',
  'path' => '/www/mobile/html',
  'subPath' => '',
  'subcatid' => '57',
  'relcatid' => '0',
  )
 */
class ArticleArea {
    /*     * *****************各频道不同部分******************* */
    /*     * *****************头部部分************* */

    //-------------------------------------------
    // 今日导读 读频道最新文章
    //-------------------------------------------
    public static function guideRead($array) {
        global $DB_Document_Read;

        $classId = $array['classId'];
        $limit = (210 == $classId || 74 == $classId || 300 == $classId || 257 == $classId) ? 20 : 12;
        $limit = (180 == $classId) ? 15 : $limit;
        $paramArr = array(
            'cid' => $classId,
            'subclassNamelen' => "3",
            'getsubclassFlag' => "1",
            'prop' => 2,
            'orderby' => 1,
            'len' => 27,
            'limit' => 'limit 0,' . $limit
        );
        $rows = PageHelper::getArticleList($paramArr);
        $artiStr = '';
        if ($rows) {
            foreach ($rows as $row) {
                if ($row["subclassName"]) {
                    if (96 == $classId) {
                        $url = get_hangqing_list_url_new($row['city_id'], 0, $row['subclassId']);
                        $sql = 'select z_cityname from z_price_city where z_id = ' . $row['city_id'] . ' and z_status = 0';
                        $row['subclassName'] = $DB_Document_Read->get_var($sql);
                        $row["subclassTmp"] = ' title="' . $row['subclassName'] . '"';
                    }
                    $url = $url ? $url : $row["subclassUrl"];
                    $sub_str = '<a class="sub_manu" href="' . $url . '"' . $row["subclassTmp"] . '>[' . $row["subclassName"] . ']</a>';
                } else {
                    $sub_str = '<a class="sub_manu">[其他]</a>';
                }
                $artiStr.= '<li>' . $sub_str . '<a href="' . $row["url"] . '" title="' . $row["ftitle"] . '">' . $row["title"] . '</a></li>';
            }
        }
        return $artiStr;
    }

    //频道LOGO右侧品牌 读对应厂商专区对应手工内容
    public static function headManu($array) {
        if (228 == $array['classId']) {//游戏频道特殊判断
            return '';
        }
        //各频道厂商专区对应手工ID
        $moduleArr = array(//多个手工Id','分隔（注意为半角逗号）在前的先显示
            180 => '15882', 195 => '932', 168 => '11761,946', 206 => '3854', 210 => '15380',
            212 => '1045', 164 => '4087', 165 => '16260', 63 => '5318', 196 => '16849',
            145 => '891', 76 => '1188', 74 => '14962', 106 => '2117', 199 => '6260',
            230 => '8206', 193 => '912', 188 => '1143', 194 => '2451', 238 => '9311',
            265 => '12496', 129 => '1003', 132 => '1217', 272 => '13328',
            282 => '14602', 289 => '15598', 303 => '12682', 231 => '17095', 328 => '17095',372=> '17095', //334=>'17371'
        );
        $classId = $array['classId'];
        if (275 == $classId) {
            $array['url'] = 'www.zol.com.cn/help/iphone.html';
        }
        //频道LOGO
        $logoStr = '<a href="http://' . $array['url'] . '" class="logo" target="_self"><img src="http://icon.zol-img.com.cn/article/2011/logo/' . $classId . '.gif"  height="24" alt="' . $array['className'] . '"/></a>';
        if (74 == $classId) {
            $logoStr .= '<a href="http://www.bbk.com/?hmsr=%E7%BD%91%E7%BB%9C%E5%AA%92%E4%BD%93&hmmd=zolmobile&hmpl=content&hmkw=content&hmci=" class="bbg_logo" title="vivo智能手机"><img src="http://icon.zol-img.com.cn/mobile/bbg/bbg_article_logo.jpg"/></a>';
        }
        //厂商专区品牌列表
        $manuStr = '';
        if ($moduleArr[$classId]) {
            $moduleIdArr = explode(",", $moduleArr[$classId]);
            if (231 == $classId || 328 == $classId) { //软件保留12个手工位防窄屏折行
                $totalNum = 12;
            } else if (180 == $classId) {
                $totalNum = 8;
            } else {
                $totalNum = 12;
            }
            $limit = 'limit 0,' . $totalNum;
            $rows = array();
            foreach ($moduleIdArr as $moduleId) {
                $tmp_rows = array();
                if ($moduleId) {
                    $paramArr = array(
                        'moduleids' => $moduleId,
                        'orderby' => 'order by date desc',
                        'limit' => $limit
                    );
                    $tmp_rows = PageHelper::getModuleArt($paramArr);
                    $rows = array_merge($rows, $tmp_rows);
                    $num = count($tmp_rows);
                    if (!($num < $totalNum))
                        break;
                    $limit = 'limit 0,' . ($totalNum - $num);
                }
            }
            if ($rows) {
                $comma = '';
                foreach ($rows as $row) {
                    $manuStr.= $comma . '<a href="' . $row["url"] . '">' . $row["title"] . '</a>';
                    $comma = ' | ';
                }
            }
            $manuStr = '<div class="h_r">' . $manuStr . '</div>';
        }
        $str = '';
        if ($logoStr || $manuStr) {
            $str = '<div class="header clearfix">' . $logoStr . $manuStr . '</div>';
        }
        return $str;
    }
    
//频道LOGO右侧品牌 读对应厂商专区对应手工内容
    public static function headManu2014($array) {
        if (228 == $array['classId']) {//游戏频道特殊判断
            return '';
        }
        //各频道厂商专区对应手工ID
        $moduleArr = array(//多个手工Id','分隔（注意为半角逗号）在前的先显示
            180 => '15882', 195 => '932', 168 => '11761,946', 206 => '3854', 210 => '15380',
            212 => '1045', 164 => '4087', 165 => '16260', 63 => '5318', 196 => '16849',
            145 => '891', 76 => '1188', 74 => '14962', 106 => '2117', 199 => '6260',
            230 => '8206', 193 => '912', 188 => '1143', 194 => '2451', 238 => '9311',
            265 => '12496', 129 => '1003', 132 => '1217', 272 => '13328',
            282 => '14602', 289 => '15598', 303 => '12682', 231 => '17095', 328 => '17095',359 => '17095',372=> '17095', //359 => '17095',334=>'17371'
        );
        $classId = $array['classId'];
        if (275 == $classId) {
            $array['url'] = 'www.zol.com.cn/help/iphone.html';
        }
    
        //厂商专区品牌列表
        $manuStr = '';
        if ($moduleArr[$classId]) {
            $moduleIdArr = explode(",", $moduleArr[$classId]);
            if (231 == $classId || 328 == $classId || 359 == $classId) { //软件保留12个手工位防窄屏折行 || 359 == $classId
                $totalNum = 12;
            } else if (180 == $classId) {
                $totalNum = 8;
            } else {
                $totalNum = 12;
            }
            $limit = 'limit 0,' . $totalNum;
            $rows = array();
            foreach ($moduleIdArr as $moduleId) {
                $tmp_rows = array();
                if ($moduleId) {
                    $paramArr = array(
                        'moduleids' => $moduleId,
                        'orderby' => 'order by date desc',
                        'limit' => $limit
                    );
                    $tmp_rows = PageHelper::getModuleArt($paramArr);
                    $rows = array_merge($rows, $tmp_rows);
                    $num = count($tmp_rows);
                    if (!($num < $totalNum))
                        break;
                    $limit = 'limit 0,' . ($totalNum - $num);
                }
            }
            if ($rows) {
                foreach ($rows as $row) {
                    $manuStr.= '<a href="' . $row["url"] . '">' . $row["title"] . '</a>';
                }
                
                if($classId==231 || 328 == $classId || 359 == $classId){ // || $classId==359
                    $manuStr = '<div class="hotbrand"><span>热门软件：</span>' . $manuStr . '</div>';
                }else{
                    $manuStr = '<div class="hotbrand"><span>热门品牌：</span>' . $manuStr . '<a href="/manu_list.html" class="more-link">更多>></a>'. '</div>';
                }
                
                 
            }
            
        }
        $str = '';
        if ( $manuStr) {
            $str =$manuStr;
        }
        return $str;
    }

    //读对应厂商专区对应手工内容
    public static function headManu_new($array) {
        //各频道厂商专区对应手工ID
        $moduleArr = array(//多个手工Id','分隔（注意为半角逗号）在前的先显示
            180 => '15882', 195 => '932', 168 => '11761,946', 206 => '3854', 210 => '15380',
            212 => '1045', 164 => '4087', 165 => '16260', 63 => '5318', 196 => '16849',
            145 => '891', 76 => '1188', 74 => '14962', 106 => '2117', 199 => '6260',
            230 => '8206', 193 => '912', 188 => '1143', 194 => '2451', 238 => '9311',
            265 => '12496', 129 => '1003', 132 => '1217', 272 => '13328',
            282 => '14602', 289 => '15598', 303 => '12682', 231 => '17095', 328 => '17095', //334=>'17371'
        );
        $classId = $array['classId'];
        //厂商专区品牌列表
        $manuStr = '';
        if ($moduleArr[$classId]) {
            $moduleIdArr = explode(",", $moduleArr[$classId]);
            if (231 == $classId || 328 == $classId) { //软件保留12个手工位防窄屏折行
                $totalNum = 12;
            } else {
                $totalNum = 12;
            }
            $limit = 'limit 0,' . $totalNum;
            $rows = array();
            foreach ($moduleIdArr as $moduleId) {
                $tmp_rows = array();
                if ($moduleId) {
                    $paramArr = array(
                        'moduleids' => $moduleId,
                        'orderby' => 'order by date desc',
                        'limit' => $limit
                    );
                    $tmp_rows = PageHelper::getModuleArt($paramArr);
                    $rows = array_merge($rows, $tmp_rows);
                    $num = count($tmp_rows);
                    if (!($num < $totalNum))
                        break;
                    $limit = 'limit 0,' . ($totalNum - $num);
                }
            }
            if ($rows) {
                $comma = '';
                $i = 1;
                foreach ($rows as $row) {
                    $class_flag = $i == 1 ? 'class="one_li"' : '';
                    $manuStr.= '<li ' . $class_flag . '><a href="' . $row["url"] . '">' . $row["title"] . '</a></li>';
                    $i++;
                }
            }
        }
        $str = '';
        if ($manuStr) {
            $str = '<span class="brand">品牌：</span><ul class="brand-links">' . $manuStr . '</ul>';
        }
        return $str;
    }

    /*     * *****************左侧底部部分************* */

    //-------------------------------------------
    // 文章分页导航下热词及分享
    //-------------------------------------------
    public static function artiWordAndShare($array) {
        global $DB_Document_Read;

        $classId = $array['classId'];
        $moduleName = '09文章页--热词';
        $sql = "select module_id from template_module_class where module_name='" . $moduleName . "' and classid=" . $classId . " and status=0";
        $moduleId = $DB_Document_Read->get_var($sql);
        $paramArr = array(
            'moduleids' => $moduleId,
            'limit' => 'limit 0,3',
            'len' => 35,
        );
        $rows = PageHelper::getModuleArt($paramArr);
        $wordStr = '';
        if ($rows) {
            foreach ($rows as $row) {
                $wordStr.= '<a href="' . $row['url'] . '">' . $row['title'] . '</a>&nbsp;&nbsp;';
            }
        }
        if ($wordStr)
            $str = '<div class="hot_c"><span><b>频道热词：</b>' . $wordStr . '</span></div>';

        /* <p><em class="n_1"></em><a title="将该文章分享到新浪微博" target="_self" href="javascript:repost(1);">新浪</a>
          <em class="n_2"></em><a title="将该文章分享到开心网"  target="_self" href="javascript:repost(4);">开心</a>
          <em class="n_3"></em><a title="将该文章分享到QQ空间"  target="_self" href="javascript:repost(8);">QQ空间</a>
          <em class="n_7"></em><a title="将该文章分享到搜狐微博"  target="_self" href="javascript:repost(2);">搜狐</a>
          <em class="n_5"></em><a title="将该文章分享到人人网"  target="_self" href="javascript:repost(5);">人人</a>
          <em class="n_6"></em><a title="将该文章分享到腾讯微博"  target="_self" href="javascript:repost(9);">腾讯</a>
          <em class="n_8"></em><a title="将该文章分享到网易微博"  target="_self" href="javascript:repost(7);">网易</a>
          </p> */
        return $str;
    }
    //-------------------------------------------
    // 底部频道热词搜索2014
    //-------------------------------------------
	public static function artiWord2014($array) {
    	global $DB_Document_Read;
    	$hotStr = self::hotWord($array);
    	//此处判断不好，如遇到问题请检查这里就好
    	$hotStr = str_replace('<div class="r_bd mt10 pb10"><div class="hotword">','',$hotStr);
    	$hotStr = str_replace('</div></div>','',$hotStr);
    	$hotStr = str_replace('<a href=""></a>','',$hotStr);
    	$str = '<div class="hot-search">
				<div class="section-head">
					<div class="search">
						<form id="searchform2" method="get" action="http://search.zol.com.cn/s/search.php">
						<input class="skey placeholder" name="kword" value="请输入关键词" id="hotSearchInput" type="text" data-source="" autocomplete="off">
						<span class="sbtnbox"><input class="sbtn" value="搜索" type="submit"></span>
						</form>
					</div>
					<h3>热门搜索</h3>
				</div><div class="hotword clearfix">'.$hotStr.'</div>
			</div>';
    	return $str;
    }
    //-------------------------------------------
    // 选机中心
    //-------------------------------------------
    public static function chooseCenter($array) {
        global $DB_Product_Read;
        //品牌 各频道对应手工ID
        $manuArr = array(
            180 => '15882', 195 => '932', 168 => '11761', 206 => '3854', 210 => '15380',
            212 => '1045', 165 => '16260', 63 => '5318', 196 => '5745',
            145 => '15302', 76 => '1188', 74 => '14962', 106 => '2117', 199 => '6260',
            230 => '8206', 193 => '912', 188 => '1143', 194 => '2451', 238 => '9311',
            227 => '10267', 265 => '12496', 129 => '1003', 132 => '1217', 272 => '13328',
            282 => '14602', 289 => '15598', 303 => '12682', //334=>'17371'
        );
        $manuArr2 = array(74 => '1136', 168 => '946', 210 => '864', 180 => '745', 165 => '16348', 145 => '891');
        //最后一参数 各频道对应手工ID
        $styleArr = array(
            74 => '12409', 257 => '12409', 210 => '6898', 145 => '4855', 212 => '12419', 272 => '13330',
            230 => '12420', 206 => '5060', 195 => '4981', 164 => '6627', 168 => '974', 63 => '1742',
            193 => '5590', 265 => '12494', 165 => '1215', 180 => '5262', 194 => '4976', 76 => '1195',
            62 => '994', 238 => '12424', 188 => '4945', 196 => '5736', 132 => '3389', 129 => '3270',
        );
        $str = '';
        $classId = $array['classId'];
        $subcat_id = $array['subcatid'];
        $relcat_id = $array['relcatid'];
        $sub_id_str = '';
        if ($subcat_id) {
            $sub_id_str .= $subcat_id;
            if ($relcat_id) {
                $sub_id_str .= ',' . $relcat_id;
            }
        }
        if ($sub_id_str) {
            $sub_arr = array_unique(explode(',', $sub_id_str));
            $sub_i = 1;
            $sub_icnt = count($sub_arr);
            foreach ($sub_arr as $subcatId) {
                if ($subcatId) {
                    //获得产品线名称
                    $sql = 'select name from subcategory where id=' . $subcatId;
                    $subName = $DB_Product_Read->get_var($sql);
                    //获得产品线英文名
                    $sql = 'select brief from subcategory_extra_info where subcategory_id=' . $subcatId;
                    $subEName = trim($DB_Product_Read->get_var($sql));

                    $sub_url = get_price_url($subcatId);
                    //获得品牌
                    $manuStr = "";
                    if ($manuArr[$classId]) {//读取手工的
                        $limit = 17;
                        if (132 == $classId)
                            $limit = 18; //服务器频道 多读
                        $paramArr = array(
                            'moduleids' => $manuArr[$classId],
                            'orderby' => 'order by date desc',
                            'limit' => 'limit 0,' . $limit,
                        );
                        $rows = PageHelper::getModuleArt($paramArr);
                        if ($rows) {
                            foreach ($rows as $row) {
                                //填写的链接地址不是品牌专区
                                if (strpos(false === $row["url"], 'manu_')) {
                                    $manuUrl = $row["url"];
                                } else {
                                    $endStr = explode("_", $row["url"]);
                                    $tmpManuId = substr($endStr[1], 0, -6);
                                    $tmpSubId = is_int($row["digest"]) ? intval($row["digest"]) : $subcatId;
                                    $manuUrl = get_price_url($tmpSubId, $tmpManuId);
                                    ;
                                }
                                $manuStr.= '<a href="' . $manuUrl . '">' . $row["title"] . '</a>';
                            }
                        }
                        //手机频道多取一个手工,国产,手工内容链接指向品牌专区需处理指向产品库
                        if ($manuArr2[$classId]) {
                            $paramArr = array(
                                'moduleids' => $manuArr2[$classId],
                                'orderby' => 'order by date desc',
                                'limit' => 'limit 0,' . ($limit - count($rows)),
                            );
                            $rows = PageHelper::getModuleArt($paramArr);
                            if ($rows) {
                                foreach ($rows as $row) {
                                    //填写的链接地址不是品牌专区
                                    if (strpos(false === $row["url"], 'manu_')) {
                                        $manuUrl = $row["url"];
                                    } else {
                                        $endStr = explode("_", $row["url"]);
                                        $tmpManuId = substr($endStr[1], 0, -6);
                                        $tmpSubId = is_int($row["digest"]) ? intval($row["digest"]) : $subcatId;
                                        $manuUrl = get_price_url($tmpSubId, $tmpManuId);
                                        ;
                                    }
                                    $manuStr .= '<a href="' . $manuUrl . '">' . $row["title"] . '</a>';
                                }
                            }
                        }
                    } else {//自动读取
                        $paramArr = array(
                            'sub_id' => $subcatId,
                            'orderby' => 3,
                            'limit' => 'limit 0,15',
                        );
                        $rows = PageHelper::getManuList($paramArr);
                        if ($rows) {
                            foreach ($rows as $row) {
                                $manuStr.= '<a href="' . $row["proListUrl"] . '">' . $row["showName"] . '</a>';
                            }
                        }
                    }
                    //获得价格区间
                    $priceStr = '';
                    $rows = PageHelper::getProductPriceRank($subcatId);
                    if ($rows) {
                        foreach ($rows as $row) {
                            $priceStr.= '<a href="' . $row["url"] . '">' . $row["btwn_name_yuan"] . '</a>';
                        }
                    }
                    //获取最后一参数
                    $styleStr = '';
                    $lastStyle = '';
                    $rows = PageHelper::getProductParamArr($subcatId, 2, 0, 0, 0, 0, 1);
                    if ($rows) {
                        $i = 0;
                        $icnt = count($rows[0]);
                        foreach ($rows[0] as $param_id => $param_info) {
                            $i++;
                            if ($icnt == $i) {
                                $class = ' lastdl';
                            } else {
                                $class = '';
                            }
                            $styleStr .= '<dl class="nav_dl clearfix' . $class . '">';
                            $styleStr .= '<dt>' . $param_info["name"] . '</dt><dd>';
                            $j = 0;
                            foreach ($rows[1][$param_id] as $key => $val) {
                                $styleStr .= '<a href="' . $rows[2][$param_id][$key] . '">' . $val . '</a>';
                                $j++;
                            }
                            $styleStr .= '</dl>';
                        }
                    } else {
                        $lastStyle = ' lastdl';
                    }
                    if ($sub_icnt > 1) {
                        if (1 == $sub_i) {
                            $str .= '<!--#if expr="$sub_id=' . $subcatId . '" -->';
                        } else if ($sub_icnt == $sub_i) {
                            $str .= '<!--#else -->';
                        } else {
                            $str .= '<!--#elif expr="$sub_id=' . $subcatId . '" -->';
                        }
                    }
                    $fastUp = (641 != $subcatId && 702 != $subcatId) ? '<a class="s_s" href="http://top.zol.com.cn/compositor/' . $subcatId . '/hit_wave.html">上升最快的' . $subName . '</a>' : '';
                    $str .= '<div class="syzp mt10">
				       <div class="tit_7"><a href="' . $sub_url . '" class="tit_name">' . $subName . '报价</a><span><a class="r_m" href="http://top.zol.com.cn/compositor/' . $subcatId . '/' . $subEName . '.html">热门' . $subName . '</a><a class="p_p" href="http://top.zol.com.cn/compositor/' . $subcatId . '/manu_attention.html">' . $subName . '品牌</a>' . $fastUp . '</span></div>
				       <dl class="nav_dl clearfix onedl"><dt>品 牌</dt> <dd>' . $manuStr . '</dd></dl>
				       <dl class="nav_dl clearfix' . $lastStyle . '"><dt>价格</dt><dd>' . $priceStr . '</dd></dl>
				       ' . $styleStr . '
			        </div>';
                    if ($sub_icnt > 1 && $sub_icnt == $sub_i) {
                        $str .= '<!--#endif -->';
                    }
                    $sub_i++;
                }
            }
        } else {
            $str = '<!--#if expr="$sub_id > 0 && $location_id > 0" --><!--#include virtual="/include/article_domain_choosecenter_${sub_id}.html"--><!--#endif -->';
        }
        return $str;
    }
    //-------------------------------------------
    // 选机中心
    //-------------------------------------------
    public static function chooseCenter2014($array) {
    	global $DB_Product_Read;
    	//品牌 各频道对应手工ID
    	$manuArr = array(
    			180 => '15882', 195 => '932', 168 => '11761', 206 => '3854', 210 => '15380',
    			212 => '1045', 165 => '16260', 63 => '5318', 196 => '5745',
    			145 => '15302', 76 => '1188', 74 => '14962', 106 => '2117', 199 => '6260',
    			230 => '8206', 193 => '912', 188 => '1143', 194 => '2451', 238 => '9311',
    			227 => '10267', 265 => '12496', 129 => '1003', 132 => '1217', 272 => '13328',
    			282 => '14602', 289 => '15598', 303 => '12682', //334=>'17371'
    	);
    	$manuArr2 = array(74 => '1136', 168 => '946', 210 => '864', 180 => '745', 165 => '16348', 145 => '891');
    	//最后一参数 各频道对应手工ID
    	$styleArr = array(
    			74 => '12409', 257 => '12409', 210 => '6898', 145 => '4855', 212 => '12419', 272 => '13330',
    			230 => '12420', 206 => '5060', 195 => '4981', 164 => '6627', 168 => '974', 63 => '1742',
    			193 => '5590', 265 => '12494', 165 => '1215', 180 => '5262', 194 => '4976', 76 => '1195',
    			62 => '994', 238 => '12424', 188 => '4945', 196 => '5736', 132 => '3389', 129 => '3270',
    	);
    	$str = '';
    	$classId = $array['classId'];
    	$subcat_id = $array['subcatid'];
    	$relcat_id = $array['relcatid'];
    	$sub_id_str = '';
    	if ($subcat_id) {
    		$sub_id_str .= $subcat_id;
    		if ($relcat_id) {
    			$sub_id_str .= ',' . $relcat_id;
    		}
    	}
    	if ($sub_id_str) {
    		$sub_arr = array_unique(explode(',', $sub_id_str));
    		$sub_i = 1;
    		$sub_icnt = count($sub_arr);
    		foreach ($sub_arr as $subcatId) {
    			if ($subcatId) {
    				//获得产品线名称
    				$sql = 'select name from subcategory where id=' . $subcatId;
    				$subName = $DB_Product_Read->get_var($sql);
    				//获得产品线英文名
    				$sql = 'select brief from subcategory_extra_info where subcategory_id=' . $subcatId;
    				$subEName = trim($DB_Product_Read->get_var($sql));
    
    				$sub_url = get_price_url($subcatId);
    				//获得品牌
    				$manuStr = "";
    				if ($manuArr[$classId]) {//读取手工的
    					$limit = 17;
    					if (132 == $classId)
    						$limit = 18; //服务器频道 多读
    					$paramArr = array(
    							'moduleids' => $manuArr[$classId],
    							'orderby' => 'order by date desc',
    							'limit' => 'limit 0,' . $limit,
    					);
    					$rows = PageHelper::getModuleArt($paramArr);
    					if ($rows) {
    						foreach ($rows as $row) {
    							//填写的链接地址不是品牌专区
    							if (strpos(false === $row["url"], 'manu_')) {
    								$manuUrl = $row["url"];
    							} else {
    								$endStr = explode("_", $row["url"]);
    								$tmpManuId = substr($endStr[1], 0, -6);
    								$tmpSubId = is_numeric($row["digest"]) ? intval($row["digest"]) : $subcatId;
    								if($tmpSubId==$subcat_id){
    									$manuUrl = get_price_url($tmpSubId, $tmpManuId);
    								}else{
    									$manuUrl = $row["url"];
    								}
    							}
    							$manuStr.= '<a href="' . $manuUrl . '">' . $row["title"] . '</a>';
    						}
    					}
    					//手机频道多取一个手工,国产,手工内容链接指向品牌专区需处理指向产品库
    					if ($manuArr2[$classId]) {
    						$paramArr = array(
    								'moduleids' => $manuArr2[$classId],
    								'orderby' => 'order by date desc',
    								'limit' => 'limit 0,' . ($limit - count($rows)),
    						);
    						$rows = PageHelper::getModuleArt($paramArr);
    						if ($rows) {
    							foreach ($rows as $row) {
    								//填写的链接地址不是品牌专区
    								if (strpos(false === $row["url"], 'manu_')) {
    									$manuUrl = $row["url"];
    								} else {
    									$endStr = explode("_", $row["url"]);
    									$tmpManuId = substr($endStr[1], 0, -6);
    									$tmpSubId = is_int($row["digest"]) ? intval($row["digest"]) : $subcatId;
    									$manuUrl = get_price_url($tmpSubId, $tmpManuId);
    									;
    								}
    								$manuStr .= '<a href="' . $manuUrl . '">' . $row["title"] . '</a>';
    							}
    						}
    					}
    				} else {//自动读取
    					$paramArr = array(
    							'sub_id' => $subcatId,
    							'orderby' => 3,
    							'limit' => 'limit 0,15',
    					);
    					$rows = PageHelper::getManuList($paramArr);
    					if ($rows) {
    						foreach ($rows as $row) {
    							$manuStr.= '<a href="' . $row["proListUrl"] . '">' . $row["showName"] . '</a>';
    						}
    					}
    				}
    				//获得价格区间
    				$priceStr = '';
    				$rows = PageHelper::getProductPriceRank($subcatId);
    				if ($rows) {
    					foreach ($rows as $row) {
    						$priceStr.= '<a href="' . $row["url"] . '">' . $row["btwn_name_yuan"] . '</a>';
    					}
    				}
    				//获取最后一参数
    				$styleStr = '';
    				$lastStyle = '';
    				$rows = PageHelper::getProductParamArr($subcatId, 2, 0, 0, 0, 0, 1);
    				if ($rows) {
    					$i = 0;
    					$icnt = count($rows[0]);
    					foreach ($rows[0] as $param_id => $param_info) {
    						$i++;
    						if ($icnt == $i) {
    							$class = ' lastdl';
    						} else {
    							$class = '';
    						}
    						$styleStr .= '<div class="param-item">';
    						$styleStr .= ' <strong>' . $param_info["name"] . '</strong>';
    						$styleStr .= '<div class="param-links">';
    						$j = 0;
    						foreach ($rows[1][$param_id] as $key => $val) {
    							$styleStr .= '<a href="' . $rows[2][$param_id][$key] . '">' . $val . '</a>';
    							$j++;
    						}
    						$styleStr .= '</div></div>';
    					}
    				} else {
    					$lastStyle = ' lastdl';
    				}
    				if ($sub_icnt > 1) {
    					if (1 == $sub_i) {
    						$str .= '<!--#if expr="$sub_id=' . $subcatId . '" -->';
    					} else if ($sub_icnt == $sub_i) {
    						$str .= '<!--#else -->';
    					} else {
    						$str .= '<!--#elif expr="$sub_id=' . $subcatId . '" -->';
    					}
    				}
    				$fastUp = (641 != $subcatId && 702 != $subcatId) ? '<a class="fast-phone" href="http://top.zol.com.cn/compositor/' . $subcatId . '/hit_wave.html">上升最快的' . $subName . '</a>' : '';
    				$str .='
					  <div class="section-head clearfix">
					    <div class="links"><a class="all-phone" href="http://top.zol.com.cn/compositor/' . $subcatId . '/' . $subEName . '.html">'.$subName.'品牌大全</a>
					    		<a class="hot-phone" href="http://top.zol.com.cn/compositor/' . $subcatId . '/' . $subEName . '.html">热门' . $subName . '</a>
					    		' . $fastUp . '</div>
					    		<h2><a href="' . $sub_url . '" class="tit_name">' . $subName . '报价</a></h2>
					  </div>
					  <div class="param-nav">
					    <div class="param-item"> <strong>品牌</strong>
					      <div class="param-links">' . $manuStr . '</div>
					    </div>
					  
					    <div class="param-item"> <strong>价格</strong>
					      <div class="param-links">' . $priceStr . '</div>
					    </div>
					   ' . $styleStr.'</div>';
					    				
    				
    				if ($sub_icnt > 1 && $sub_icnt == $sub_i) {
    					$str .= '<!--#endif -->';
    				}
    				$sub_i++;
    			}
    		}
    	} else {
    		$str = '<!--#if expr="$sub_id > 0 && $location_id > 0" --><!--#include virtual="/include/article_domain_choosecenter_${sub_id}_2014.html"--><!--#endif -->';
    	}
    	return $str;
    }
    
    //-------------------------------------------
    // 文章底部热词 通过手工ID获得
    //-------------------------------------------
    public static function hotWord($array) {
        $classId = $array['classId'];

        //特殊处理频道配置表
        $configArr = array(
            '90' => array('moduleid' => 15925, 'limit' => 'limit 0,10'), //新闻中心
            '74' => array('moduleid' => 15790, 'limit' => 'limit 0,500', 'xml' => 'http://www.cnmo.com/cg_keyword_zol_cnmo.xml'), //手机频道，推广手机中国
            '196' => array('moduleid' => 15790, 'limit' => 'limit 0,500', 'xml' => 'http://www.cnmo.com/cg_keyword_zol_xgo.xml'), //GPS,推广XGO
            '145' => array('moduleid' => 15790, 'limit' => 'limit 0,500', 'xml' => 'http://www.cnmo.com/cg_keyword_zol_xgo.xml'), //数码影像,推广XGO
            '289' => array('moduleid' => 15790, 'limit' => 'limit 0,500', 'xml' => 'http://www.cnmo.com/cg_keyword_zol_xgo.xml'), //摄像机,推广XGO
        );
        $wordStr = '';
        //需特殊处理频道
        if (in_array($classId, array_keys($configArr))) {
            $paramArr = array(
                'moduleids' => $configArr[$classId]['moduleid'],
                'limit' => $configArr[$classId]['limit'],
                'len' => 35,
            );
            $rows = PageHelper::getModuleArt($paramArr);
            if ($rows) {
                foreach ($rows as $row) {
                    $wordStr.= '<a href="' . $row['url'] . '">' . $row['title'] . '</a>';
                }
            }
            //分站热词推广
            /* 			if($configArr[$classId]['xml']){				
              $xmlStr = file_get_contents($configArr[$classId]['xml']);
              $xmlObj = new SimpleXMLElement($xmlStr);
              if($xmlObj && !empty($xmlObj->data)){
              $strLen = 0;
              $i=1;
              foreach ($xmlObj->data as $data) {
              $title = iconv('UTF-8','GBK',$data->title);
              //				        $tmpLen = strlen($title);
              //				        if($strLen + $tmpLen > $limitLen)continue;//限制字数，如果字数超过限制跳过
              //				        $strLen += $tmpLen;
              $wordStr.= '<a href="'.$data->url.'">'.$title.'</a>';
              if($i>=4)break;
              $i++;
              }
              }
              } */
        } else {
            $paramArr = array(
                'moduleids' => 15790,
                'limit' => 'limit 0,500',
                'len' => 35,
            );
            $rows = PageHelper::getModuleArt($paramArr);
            if ($rows) {
                foreach ($rows as $row) {
                    $wordStr.= '<a href="' . $row['url'] . '">' . $row['title'] . '</a>';
                }
            }
            $array_channel = array(74, 210, 145, 96);
            $out = '';
            if (!in_array($classId, $array_channel)) {
                $url = "http://product.pchome.net/Index.php?c=Api&action=RecCorpLink&key=419c0473e21348439e585932fc109a99&keywords=zolArticle";
                $content = file_get_contents($url);
                $content = json_decode($content);
                if (is_array($content)) {
                    shuffle($content);
                    $new_chunk = array_chunk($content, 2); //分割数组
                    for ($i = 0; $i < 5; $i++) {
                        $str = "str_" . $i;
                        $linktmp = iconv("utf-8", "GBK", $new_chunk[$i][0]->title);
                        $linktmp1 = iconv("utf-8", "GBK", $new_chunk[$i][1]->title);
                        $linkurl = $new_chunk[$i][0]->link;
                        $linkurl1 = $new_chunk[$i][1]->link;
                        $$str = '<a href="' . $linkurl . '">' . $linktmp . '</a><a href="' . $linkurl1 . '">' . $linktmp1 . '</a>';
                    }
                    $out = '<!--#if expr="$doc_id = /[0,5]$/ " -->
				            ' . $str_0 . '
						 <!--#elif expr="$doc_id = /[1,6]$/ " -->
				             ' . $str_1 . '
						  <!--#elif expr="$doc_id = /[2,7]$/ " -->
					        ' . $str_2 . '
						 <!--#elif expr="$doc_id = /[3,8]$/ " -->
				            ' . $str_3 . '
						 <!--#elif expr="$doc_id = /[4,9]$/ " -->
					        ' . $str_4 . '
						 <!--#else -->
						 <!--#endif -->';
                }
            }
        }
        if ($wordStr) {
            $wordStr = '<div class="r_bd mt10 pb10"><div class="hotword">' . $wordStr . $out . '</div></div>';
        }
        if (342 == $classId) {
            $wordStr = '<!--#include virtual="/include/soft_hots_word4.html"-->' . $wordStr;
        }
        //if($classId == 359) $wordStr = '';
        return $wordStr;
    }

    //-------------------------------------------
    // 网友摄影作品
    //-------------------------------------------	
    public static function dcdvPhoto($array) {
        $dcdvStr = '';
        if (145 == $array['classId']) {
            $paramArr = array(
                'moduleids' => 21,
                'dbname' => 'DB_Dcbbs_Read',
                'enname' => 'dcbbs',
                'limit' => 'limit 0,4',
                'len' => 36,
            );
            $rows = PageHelper::getBbsModule($paramArr);
            if ($rows) {
                foreach ($rows as $row) {
                    if ($row["url"]) {
                        $title_str = '<a href="' . $row["url"] . '" ' . $row["title_tmp"] . '>' . $row["title"] . '</a>';
                    } else {
                        $title_str = '<span class="show_title">' . $row["title"] . '</span>';
                    }
                    $dcdvStr.= '<dl class="zp_dl">
				            	<dt></dt>
				            	<dd><a href="' . $row["pic_url"] . '" class="show_pic"><img .src="' . $row["pic_src"] . '" width="150" height="100"></a>' . $title_str . '作者：<a href="' . $row["user_url"] . '" class="a_2">' . $row["user_name"] . '</a></dd>
				          	</dl>';
                }
                if ($dcdvStr) {
                    $dcdvStr = '<div class="syzp mt10">
						        <div class="tit_7"> <a href="http://dcbbs.zol.com.cn/">查看更多摄影作品>></a> 网友摄影作品 </div>
						        <div class="clearfix dcdv_show" id="dcdvShowPhotoList">' . $dcdvStr . ' </div>
						      </div>';
                }
            }
        }
        return $dcdvStr;
    }

    /*     * ****************右侧部分*************** */

    //-------------------------------------------
    // DELL广告
    //-------------------------------------------	
    public static function dellAd() {
        //取得切换的小图
        return '';
        $paramArr = array('moduleids' => '12415', 'limit' => 'limit 0,2', 'len' => '9', 'getImageFlag' => '1',);

        $rows = PageHelper::getModuleArt($paramArr);
        $imgstrArr = array();
        if ($rows) {
            $i = 1;
            foreach ($rows as $row) {
                $imgstrArr[$i] = '<a href="' . $row['url'] . '" ' . $row['title_tmp'] . '><img src="' . $row['pic_src'] . '" width="60" height="45" alt="' . $row['title'] . '">' . $row['title'] . '</a>';
                $i++;
            }
        }
        //切换右侧的三条文字链
        $typeArr = array("导购", "行情", "促销", "新闻");
        $paramArr = array(
            'cid' => '210,212',
            'sid' => '1080,1088',
            'orderby' => '1',
            'limit' => 'limit 0,6',
            'len' => '26',
            'byHardWareFlag' => '1',
            'manuids' => '21',
        );
        $listStr1 = $listStr2 = '';
        $rows = PageHelper::getArticleList($paramArr);
        if ($rows) {
            $i = 1;
            foreach ($rows as $row) {
                $tmpstr = '<li><em>[' . $typeArr[rand(0, 3)] . ']</em><a href="' . $row['url'] . '"' . $row['stitl_tmp'] . '>' . $row['title'] . '</a></li>';
                if ($i > 3) {
                    $listStr2 .= $tmpstr;
                } else {
                    $listStr1 .= $tmpstr;
                }
                $i++;
            }
        }
        //底部滚动
        $paramArr = array('moduleids' => '12416', 'limit' => 'limit 0,3', 'len' => '100', 'getImageFlag' => '2',);
        $marqStr = '';
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) {
            foreach ($rows as $row) {
                $marqStr .= '<a href="' . $row['url'] . '" ' . $row['title_tmp'] . '>' . $row['title'] . '</a>&nbsp;&nbsp;';
            }
        }
        $welcomeStr = '<a href="http://altfarm.mediaplex.com/ad/ck/10592-62014-23401-191">买家用超值本,免费电话400-881-9941更多惊喜在线咨询</a><br /><a href="http://altfarm.mediaplex.com/ad/ck/10592-62014-23401-191">买商用超值本,免费电话400-889-7177更多惊喜在线咨询</a>';
        return '<div class="r_cx mt10">
		        <div class="tit_4"> <b>戴尔促销</b>
		          <ul class="switch"><li rel="dell_s2" class="now">特价</li><li rel="dell_s1">整机/笔记本 </li><li rel="dell_s3">优惠</li></ul>
		        </div>
		        <div id="dell_s1" style="display:none">
			        <dl class="cx_dl clearfix">
			          <dt>' . $imgstrArr[1] . '</dt>
			          <dd><ul>' . $listStr1 . '</ul></dd>
			        </dl>
			        <span class="cx_sp">' . $welcomeStr . '</span>
		        </div>		        
		        <div id="dell_s2" style="position:relative;">
		        <a style="position:absolute;width:130px;height:50px;left:0;bottom:0;z-index:2;background:url(about:blank);" href="http://webchat.b.qq.com/webchat.htm?sid=2188z8p8p8p8q8K8K8080"></a>
		        <a style="position:absolute;width:290px;height:105px;left:0;bottom:0;z-index:1;background:url(about:blank);" href="http://chat1.ap.dell.com/netagent/cimlogin.aspx?questid=5C467048-CBFB-4FB1-852A-78293DA9A4EE&portid=CF24AC26-21CB-478E-B599-0B6EE15069DF&nareferer=https://dellchatappdev.us.dell.com/netagent/cimlogin.aspx?&dgc=IR&cid=odg_campaigns_intel&lid=ODGDellchat"></a><img src="http://icon.zol-img.com.cn/article/201203/2951051.jpg" width="295" height="105" /></div>
		        <div id="dell_s3" style="position:relative;display:none;">
		        <a style="position:absolute;width:130px;height:50px;left:0;bottom:0;z-index:2;background:url(about:blank);" href="http://altfarm.mediaplex.com/ad/bk/10592-61498-3840-0?cn_smbACCpagechat_qqsales=1&mpuid=&mpro=http://b.qq.com/webc.htm?new=0&sid=800051116&o=www.dell.com.cn&q=7"></a>
		        <a style="position:absolute;width:290px;height:105px;left:0;bottom:0;z-index:1;background:url(about:blank);" href="http://www1.ap.dell.com/content/topics/topic.aspx/global/shared/chat/contact/zh/ap/cn/bsd/sales_chat_bcrp?c=cn&l=zh&s=bsd"></a><img src="http://icon.zol-img.com.cn/article/201203/2951052.jpg" width="295" height="105" /></div>
		        <div id="dell_s4" style="display:none">
			        <dl class="cx_dl clearfix">
			          <dt>' . $imgstrArr[2] . '</dt>
			          <dd><ul>' . $listStr2 . '</ul></dd>
			        </dl>
			        <span class="cx_sp">' . $welcomeStr . '</span>
		        </div>
		        <div class="t_j"> <img src="http://icon.zol-img.com.cn/sound/090401/logo_dell.gif" width="85" height="20"><marquee scrolldelay="120" onmouseout="this.start()" onmouseover="this.stop()" style="width:204px;float:right">' . $marqStr . '</marquee></div>
		      </div>';
    }

    //-------------------------------------------
    // Intel广告
    //-------------------------------------------	
    public static function intelAd($array) {
    	
    	return self::intelAd2014($array);
    	
    	
        //require_once('/www/article/html/admin/Dcommend/model/docAnalysis.class.php');
        //$analysis = new docAnalysis();
        //echo $analysis->findMatchWord($docid);
        //return '<div id="intelAd" class="mt10"><script src="http://doc.zol.com.cn/intelcorp/dcommend_2011.php?document_id=<!--#echo var="doc_id" -->&classid=<!--#echo var="class_id" -->" type="text/javascript"></script></div>';
        return '<div id="intelAd" class="mt10"><script src="http://doc.zol.com.cn/intelcorp/dcommend_2014q2.php?document_id=<!--#echo var="doc_id" -->&classid=<!--#echo var="class_id" -->&vflag=1" type="text/javascript"></script></div>';
        
    }

 	public static function intelAd2014($array) {
 	  /* $str = <<<EOT
<div id="intelAd" class="mt10 module">
<script>
if(typeof(DCAD_CurrentTab)=="undefined"){
    var DCAD_CurrentTab="con";
}
var intelFrameSrc = '/intel-smartzone.html?DCAD_CurrentTab='+DCAD_CurrentTab;
document.write('<iframe src="'+intelFrameSrc+'" frameborder="0" width="300" height="340" allowtransparency="" align="center" marginwidth="0" marginheight="0" scrolling="no" ></iframe>');
</script>
<div style="display:none;"></div>
</div>     
EOT;
//return $str;
*/
 	        $str = <<<EOT
<div id="intelAd" class="mt10 module">
<script>
if(typeof(DCAD_CurrentTab)=="undefined"){
    var DCAD_CurrentTab="con";
}	            
</script>
<iframe src="/intel-smartzone.html" frameborder="0" width="300" height="340" allowtransparency="" align="center" marginwidth="0" marginheight="0" scrolling="no" ></iframe>
<div style="display:none;"></div>
</div>
EOT;
        return $str;

        //require_once('/www/article/html/admin/Dcommend/model/docAnalysis.class.php');
        //$analysis = new docAnalysis();
        //echo $analysis->findMatchWord($docid);
        //return '<div id="intelAd" class="mt10"><script src="http://doc.zol.com.cn:8088/intelcorp/dcommend_2014_new.php?document_id=<!--#echo var="doc_id" -->&classid=<!--#echo var="class_id" -->" type="text/javascript" language="javascript"></script></div>';
        //return '<div id="intelAd" class="mt10"><script src="http://doc.zol.com.cn/intelcorp/dcommend_2014q2.php?document_id=<!--#echo var="doc_id" -->&classid=<!--#echo var="class_id" -->" type="text/javascript" ></script></div>';
    }
    
    # 文章页慧聪换量
    public static function huicongExchangeFlow($array){
    	
    	$array['classId'] = (int)$array['classId'];
    	# subclass_id => 文件名
    	$conf = array(
    		164=>array(1074 =>'hifi',),# 音频=>hifi
    		353 => 'pj',# 数码配件
    		334 => 'minipower',#移动电源
    		200=>array(1508 => 'washer',1506=>'ac',),# 家电=> 洗衣机 空调
    		301 => 'cloud',#云计算
    		291 => 'security',# 安防监控
    		220 => 'epc',# 工作站
    		364 => array(1732=>'hifi',),# 测试
        );
        
    	if(!array_key_exists($array['classId'], $conf)) return '';
    	
    	if(is_array($conf[$array['classId']])){
    		foreach($conf[$array['classId']] as $k=>$v){
    			if(!$v) continue;
    			if(in_array($k,$array['subIdArr'])){
    				$str = '<script> var HuiCongConfig='.json_encode($conf).';var usingSubId=true;</script>';
    				return '<iframe id="HuiCongAd" src="http://zol.hc360.com/zol/'.$conf[$array['classId']][$k].'.html" scrolling="no" frameborder="0" style="width:300px;height:250px;"></iframe>'.$str;
    			}
    		}
    	}else{
    		$str = '<script> var HuiCongConfig='.json_encode($conf).';var usingSubId=false;</script>';
    		return '<iframe id="HuiCongAd" src="http://zol.hc360.com/zol/'.$conf[$array['classId']].'.html" scrolling="no" frameborder="0" style="width:300px;height:250px;"></iframe>'.$str;
    	}
    }
    
    # 文章页intel宝典
    public static function intelAdFrame($array) {
        #笔记本启用iframe
        $codeStr = '<!doctype html>
        <html>
        <head>
        	<meta charset="GBK" />
        	<title>Intel Smartzone</title>
<script type="text/javascript">
document.domain="zol.com.cn";
if(typeof(window.parent.DCAD_CurrentTab)!="undefined"){
    window.DCAD_CurrentTab = window.parent.DCAD_CurrentTab;
}else{
    window.DCAD_CurrentTab = "con";
}
</script>
        </head>
        <body>';
        switch ((int)$array['classId']){
            #笔记本
            case 210:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116453746;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116453746;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116453746;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;
                
            #台式
            case 212:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937311;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937311;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937311;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;

            #332 C_超极本
            case 332:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937310;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937310;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937310;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;
                
            #DIY
            case 182:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937309;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937309;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937309;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break; 
                
            #CPU
            case 62:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937308;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937308;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937308;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;
                
            #AIO  一体机 20150916
            case 265:
                $codeStrOld ='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                $codeStrOld ='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                $codeStr .='<SCRIPT type="text/javascript" SRC="http://fw.adsafeprotected.com/rjss/dc/42992/6274320/ddm/adj/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://fw.adsafeprotected.com/rfw/dc/42992/6274319/ddm/ad/N5751.138759.7505627606321/B8606609.116937307;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;
                
            #pad
            case 300:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937306;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937306;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937306;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;
                
            #game
            case 228:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937305;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937305;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937305;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;
            
             #mobile
            case 74:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937304;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937304;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937304;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;   
                
            #soft
            case 231:
                $codeStr .='<SCRIPT language=\'JavaScript1.1\' SRC="http://ad.doubleclick.net/ddm/adj/N5751.138759.7505627606321/B8606609.116937303;sz=300x330;ord=[timestamp]?">
</SCRIPT>
<NOSCRIPT>
<A HREF="http://ad.doubleclick.net/ddm/jump/N5751.138759.7505627606321/B8606609.116937303;sz=300x330;ord=[timestamp]?">
<IMG SRC="http://ad.doubleclick.net/ddm/ad/N5751.138759.7505627606321/B8606609.116937303;sz=300x330;ord=[timestamp]?" BORDER=0 WIDTH=300 HEIGHT=330 ALT="Advertisement"></A>
</NOSCRIPT>';
                break;
                
            default:
                $codeStr .=''; 
        }
        $codeStr .='</body></html>';
        return $codeStr;
    }
    
    public static function intelModule($array) {
        return;
        if (132 != $array['classId'])
            return;

        $paramArr = array('moduleids' => '16227', 'limit' => 'limit 0,1', 'getImageFlag' => '1');
        $rows = PageHelper::getModuleArt($paramArr);
        $picLink = '';
        if ($rows) {
            foreach ($rows as $row) {
                $picLink = '<a href="' . $row['url'] . '"><img height="75" width="280" alt="' . $row['title'] . '" src="' . $row['pic_src'] . '"></a>';
            }
        }
        $paramArr = array('moduleids' => '16228', 'limit' => 'limit 0,2', 'getImageFlag' => '2');
        $rows = PageHelper::getModuleArt($paramArr);
        $textLink = '';
        if ($rows) {
            foreach ($rows as $row) {
                $textLink .= '<li><a href="' . $row['url'] . '" ' . $row['title_tmp'] . '>' . $row['title'] . '</a></li>';
            }
        }
        if ($picLink || $textLink) {
            return '<div class="r_bd mt10 pb10 add_intel">
					<div class="tit_5 tit_6">IT号外</div>' . $picLink . '
					<ul>' . $textLink . '</ul>
				</div>';
        } else {
            return;
        }
    }

    //-------------------------------------------
    //相关组图
    //-------------------------------------------
    static function slideList($array) {
        $slideStr = '';
        return $slideStr;
        $module_arr = array(74 => '12367', 257 => '12367');
        if ($module_arr[$array['classId']]) {
            $paramArr = array(
                'moduleids' => $module_arr[$array['classId']],
                'limit' => 'limit 0,4',
                'orderby' => 'order by date desc',
                'getimageflag' => '1',
                'len' => '36',
            );
            $rows = PageHelper::getModuleArt($paramArr);
        } else {
            $paramArr = array(
                'cid' => $array['classId'],
                'getslideflag' => 1,
                'prop' => 2,
                'len' => 36,
                'showimg' => 1,
                'imgwidth' => 120,
                'imgheight' => 90,
                'orderby' => 1,
                'limit' => 'limit 0,4'
            );
            $rows = PageHelper::getArticleList($paramArr);
        }
        if ($rows) {
            foreach ($rows as $row) {
                $slideStr.= '<li><a href="' . $row["url"] . '" ' . $row["title_tmp"] . '><img src="' . $row["pic_src"] . '" width="120" height="90" alt="' . $row["ftitle"] . '">' . $row["title"] . '</a></li>';
            }
            if ($slideStr) {
                $slideStr = '<div class="r_bd mt10 pb10">
        						<div class="tit_5 tit_6"><a href="http://' . $array['url'] . '/slide_1.html">查看更多' . $array['className'] . '美图>></a>' . $array['className'] . '图库</div>
								<ul class="ft_ul clearfix">' . $slideStr . '</ul>
     						</div>';
            }
        }
        return $slideStr;
    }

    //-------------------------------------------
    // 手机软件              线上正在使用的  ing
    //-------------------------------------------	
    static function mobileSoft($array) {
        $str = $softStr = $gameStr = '';
        //软件
        $paramArr = array(
            'orderby' => "order by date desc",
            'limit' => "limit 0,12",
            'len' => 8,
            'moduleids' => "15948", //15948
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) {
            foreach ($rows as $row) {
            	//强制走50x50小图
        		$row['pic_src'] = str_replace('fd.zol-img.com.cn/','fd.zol-img.com.cn/t_s50x50/',$row['pic_src']);
                $softStr .= '<li><a href="' . $row['url'] . '" ' . $row['title_tmp'] . '><img .src="' . $row['pic_src'] . '" width="50" height="50" alt="' . $row['ftitle'] . '" />' . $row['title'] . '</a></li>';
            }
        }
        //游戏
        $paramArr = array(
            'orderby' => "order by date desc",
            'limit' => "limit 0,12",
            'len' => 8,
            'moduleids' => "19942", //19942
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) {
            foreach ($rows as $row) {
            	//强制走50x50小图
        		$row['pic_src'] = str_replace('fd.zol-img.com.cn/','fd.zol-img.com.cn/t_s50x50/',$row['pic_src']);
                $gameStr .= '<li><a href="' . $row['url'] . '" ' . $row['title_tmp'] . '><img .src="' . $row['pic_src'] . '" width="50" height="50" alt="' . $row['ftitle'] . '" />' . $row['title'] . '</a></li>';
            }
        }
        $str .= '<div class="r_bd mt10 pb10"><div class="tit_5 tit_6"><a href="http://sj.zol.com.cn/">查看更多手机软件>></a>手机软件</div><ul class="tab_ul switch clearfix"><li class="now" rel="mobileSoftList_1">软件</li><li rel="mobileSoftList_2">游戏</li></ul>';
        if ($softStr) {
            $str .= '<ul class="rj_ul clearfix" id="mobileSoftList_1" style="border-top:1px solid #AAC5F2;margin-top: -1px;">' . $softStr . '</ul>';
        }
        if ($gameStr) {
        	$str .= '<ul class="rj_ul clearfix" id="mobileSoftList_2" style="display:none;border-top:1px solid #AAC5F2;margin-top: -1px;">' . $gameStr . '</ul>';
        }
        $str .= '</div>';
        return $str;
    }

    //-------------------------------------------
    // 手机软件-2014   右侧区块3(2014)
    //-------------------------------------------
    static function mobileSoft2014($array) {
    	$str = $softStr = $gameStr = '';
    	//软件
    	$paramArr = array(
    		'orderby'   => "order by date desc",
    		'limit'     => "limit 0,16",
    		'len'       => 8,
    		'moduleids' => "15948", //15948
    	);
    	$softFlag = $gameFlag = false;
    	$rows = PageHelper::getModuleArt($paramArr);
    	if ($rows) {
    		$i=1;
    		$icnt = count($rows);
    		if($icnt/8 > 1) $softFlag = true;
    		foreach ($rows as $row) {
    			//强制走50x50小图
    			$row['pic_src'] = str_replace('fd.zol-img.com.cn/','fd.zol-img.com.cn/t_s50x50/',$row['pic_src']);
    			if($i==1){
    				$softStr .='<ul class="app-list clearfix" rel="1">';
    			}elseif($icnt > 9 && $i==9){
    				$softStr .='<ul class="app-list clearfix" rel="2" style="display:none;">';
    			}
    			$softStr .= '<li><a href="'.$row['url'].'" title="'.$row['ftitle'].'">
								<img .src="'.$row['pic_src'].'" alt="" width="50" height="50">
								<span>'.$row['title'].'</span></a>
							</li>';
    			if(($icnt > 9 && $i==8) || $i==$icnt){
    				$softStr .='</ul>';
    			}
    			if(16==$i) break;
    			$i++;
    		}
    	}
    	//游戏
    	$paramArr = array(
    		'orderby'   => "order by date desc",
    		'limit'     => "limit 0,16",
    		'len'       => 8,
    		'moduleids' => "19942", //19942
    	);
    	$rows = PageHelper::getModuleArt($paramArr);
    	if ($rows) {
    		$i=1;
    		$icnt = count($rows);
    		if($icnt/8 > 1) $gameFlag = true;
    		foreach ($rows as $row) {
    			//强制走50x50小图
    			$row['pic_src'] = str_replace('fd.zol-img.com.cn/','fd.zol-img.com.cn/t_s50x50/',$row['pic_src']);
    			if($i==1){
    				$gameStr .='<ul class="app-list clearfix" rel="1">';
    			}elseif($icnt > 9 && $i==9){
    				$gameStr .='<ul class="app-list clearfix" rel="2" style="display:none;">';
    			}
    			$gameStr .= '<li><a href="'.$row['url'].'" title="'.$row['ftitle'].'">
								<img .src="'.$row['pic_src'].'" alt="" width="50" height="50">
								<span>'.$row['title'].'</span></a>
							</li>';
    			if(($icnt > 9 && $i==8) || $i==$icnt){
    				$gameStr .='</ul>';
    			}
    			if(16==$i) break;
    			$i++;
    		}
    	}
    	$str .= '<div class="module mobile-app">
        			<div class="module-header"><h3>手机应用</h3></div>
        			<ul class="rank-tab switch clearfix">
						<li class="first current" rel="tab_app_1">软件</li>
						<li rel="tab_app_2">游戏</li>
					</ul>';
    	if ($softStr) {
    		$str .= '<div id="tab_app_1">' . $softStr;
    		if($softFlag) {
    			$str .= '<div class="clearfix"><div class="page-tab"><a href="javascript:;" target="_self" class="next-btn">下一个</a><a href="javascript:;" target="_self" class="prev-btn">上一个</a><span><em id="soft_app_num">1</em>/2</span></div></div>';
    		}
    		$str .= '</div>';
    	}
    	if ($gameStr) {
    		$str .= '<div id="tab_app_2" style="display:none;">' . $gameStr;
    		if($gameFlag) {
    			$str .= '<div class="clearfix"><div class="page-tab"><a href="javascript:;" target="_self" class="next-btn">下一个</a><a href="javascript:;" target="_self" class="prev-btn">上一个</a><span><em id="game_app_num">1</em>/2</span></div></div>';
    		}
    		$str .= '</div>';
    	}
    	$str .= '</div>';
    	return $str;
    }
    
    //-------------------------------------------
    // 看了又看后面跟随一广告  suhy  20150728
    //-------------------------------------------
    static function adFollowLookAndLook($array) {
    	$str = '<div class="module showFocusLook clearfix" id="showFocusLookBottom" style="display: block;">
					<div id="look-and-look"></div>
					<div id="ad-box-under-look">
						'.ArticleAd::bottomAd201507($array).'
					</div>
				</div>';
    	return $str;
    }
        
    //-------------------------------------------
    // 企业人物访谈 读专题
    //-------------------------------------------	
    static function personInterview($array) {
        $str = '';
        $paramArr = array(
            'classid' => $array['classId'],
            'showPicFlag' => 1,
            'width' => 120,
            'height' => 90,
            'orderby' => 1,
            'limit' => "limit 0,4",
            'len' => 40
        );
        $rows = PageHelper::getTopicList($paramArr);
        if ($rows) {
            foreach ($rows as $row) {
                $str .= '<li><a href="' . $row['url'] . '" ' . $row['title_tmp'] . '><img src="' . $row['img'] . '" width="120" height="90" alt="' . $row['title'] . '" />' . $row['title'] . '</a></li>';
            }
        }
        if ($str) {
            if ($array['classId'] == 291) {
                $tit = '热点专题';
            } else {
                $tit = '企业人物访谈';
            }
            $str = '<div class="r_bd mt10 pb10">
				        <div class="tit_5 tit_6">' . $tit . '</div>
				        <ul class="ft_ul clearfix">
						' . $str . '
				        </ul>
				      </div>';
        }
        return $str;
    }

    //-------------------------------------------
    //产品排行榜    右侧区域2
    //-------------------------------------------	
    public static function productRank($array) {
        global $DB_Product_Read, $db_read2;
        $showCnt = 10; //显示的条数
        $showLen = 25; //显示的标题长度
        //配合产品库推广新品，此模板进行独立发布 2011-3-15 wangml
        $productStr = '';
        $subcat_id = $array['subcatid'];
        $relcat_id = $array['relcatid'];
        $productStr = '<!--#if expr="$sub_id > 0" --><!--#include virtual="/dynamic/article_domain_product_${sub_id}.html"--><!--#else --><!--#include virtual="/dynamic/article_domain_product_' . $subcat_id . '.html"--><!--#endif -->';
        return $productStr;


        $sub_id_str = '';
        if ($subcat_id) {
            $sub_id_str .= $subcat_id;
            if ($relcat_id) {
                $sub_id_str .= ',' . $relcat_id;
            }
        }
        if ($sub_id_str) {
            $sub_arr = array_unique(explode(',', $sub_id_str));
            $sub_i = 1;
            $sub_icnt = count($sub_arr);
            foreach ($sub_arr as $subcatId) {
                //保存评测文章，避免重复取得
                $testArticleArr = array();
                if ($subcatId) {
                    //获得产品线英文名
                    $sql = 'select brief from subcategory_extra_info where subcategory_id=' . $subcatId;
                    $subEName = trim($DB_Product_Read->get_var($sql));
                    //获得产品线名称
                    $sql = 'select name from subcategory where id=' . $subcatId;
                    $subName = $DB_Product_Read->get_var($sql);
                    //同产品 最热
                    $productList1 = '';
                    $paramArr1 = array(
                        'sub_id' => $subcatId,
                        'showimgFlag' => 1,
                        'titlelen' => $showLen,
                        'showimg' => 1,
                        'pwidth' => 80,
                        'pheight' => 60,
                        'priceFlag' => 1,
                        'priceUrlFlag' => 1,
                        'hudongflag' => 1,
                        'merchantFlag' => 1,
                        'orderby' => 2,
                        'limit' => 'limit 0,' . $showCnt
                    );
                    $rows = PageHelper::getProductList($paramArr1);
                    if ($rows) {
                        $i = 0;
                        $hotest = 0;
                        foreach ($rows as $row) {
                            $i++;
                            $pcArti = '';
                            $pid = $row['pid'];
                            if (isset($testArticleArr[$pid])) {//先查看以前是否获得过
                                $artis = $testArticleArr[$pid];
                            } else {
                                $artis = self::getTechTestArt($array['classId'], $pid, 1, 36); //评测文章
                                $testArticleArr[$pid] = $artis;
                            }
                            $pcArti = '';
                            if ($artis) {
                                foreach ($artis as $art) {
                                    $pcArti.= '<span class="pc">[评测] <a href="' . $art['url'] . '" ' . $art['title_tmp'] . '>' . $art['title'] . '</a></span>';
                                }
                            }
                            //热度
                            if (1 == $i)
                                $hotest = 0 != (int) $row['ip'] ? (int) $row['ip'] : 1;
                            $hot = ceil(($row['ip'] / $hotest) * 30);
                            //用户评分
                            $star = ceil($row['user_mark'] / 5 * 55);
                            $liClass = 1 == $i ? ' class="cur" id="rank_li_one"' : '';
                            $priceStr = '';
                            if (!(0 == $row['mMin'] && 0 == $row['mMax'])) {
                                if (strlen((int) $row['mMax']) > 5) {
                                    $priceStr = '￥' . $row['mMax'];
                                } else {
                                    $priceStr = $row['mMin'] == 0 && $row['mMax'] == 0 ? '' : '￥' . $row['mMin'] . '-' . '￥' . $row['mMax'];
                                }
                            }

                            $priceStr = $priceStr ? '<li>商家报价：<em>' . $priceStr . '</em></li>' : '';
                            $productList1.='<li' . $liClass . '>
										<em class="clearfix">
										<span class="nu_' . $i . '"></span><a href="' . $row['url'] . '" class="a_hover" ' . $row['titletmp'] . '>' . $row['title'] . '</a>
										<em class="rd" style="width:' . $hot . 'px"></em>
										</em>
							            <dl class="lidl clearfix">
							              <dt><a href="' . $row['url'] . '"><img src="' . $row['pic'] . '" width="80" height="60" alt="' . $row['ftitle'] . '"></a></dt>
							              <dd>
							                <ul>
							                ' . $priceStr . '
							                <li><b>ZOL评分：</b><span class="xbg2"><em class="xbg3" style="width:' . $star . 'px;"></em></span></li>
							                 <li>' . $row['mark_num'] . '用户点评</li>
							                </ul>
							              </dd>
							            </dl>
							            ' . $pcArti . '						            
						          	</li>';
                        }
                    }


                    //同价位 最热
                    $productList2 = '';
                    //获得该产品线的所有价位列表，然后用ssi判断区分开
                    $psql = "select price_low,price_high from price_range where subcategory_id = {$subcatId} order by price_low";
                    $res = $DB_Product_Read->get_results($psql, "O");

                    if ($res) {
                        $j = 0;
                        foreach ($res as $re) {
                            $j++;
                            $priceHigh = $re->price_high == 0 ? 9999999 : $re->price_high;
                            if ($j == 1) {
                                $productList2 .= '<!--#if expr="$price_level =' . $j . '" -->';
                            } else {
                                $productList2 .= '<!--#elif expr="$price_level =' . $j . '" -->';
                            }
                            $paramArr3 = array(
                                'sub_id' => $subcatId,
                                'showimgFlag' => 1,
                                'titlelen' => $showLen,
                                'showimg' => 1,
                                'pwidth' => 80,
                                'pheight' => 60,
                                'priceFlag' => 1,
                                'priceUrlFlag' => 1,
                                'hudongflag' => 1,
                                'merchantFlag' => 1,
                                'orderby' => 2,
                                'priceLow' => $re->price_low,
                                'priceHigh' => $re->price_high,
                                'limit' => 'limit 0,' . $showCnt
                            );
                            $rows = PageHelper::getProductList($paramArr3);
                            //					var_dump($rows);exit;
                            if ($rows) {
                                $i = 0;
                                $hotest = 0;
                                foreach ($rows as $row) {
                                    $i++;
                                    $pcArti = '';
                                    $pid = $row['pid'];
                                    if (isset($testArticleArr[$pid])) {//先查看以前是否获得过
                                        $artis = $testArticleArr[$pid];
                                    } else {
                                        $artis = self::getTechTestArt($array['classId'], $pid, 1, 36); //评测文章
                                        $testArticleArr[$pid] = $artis;
                                    }
                                    $pcArti = '';
                                    if ($artis) {
                                        foreach ($artis as $art) {
                                            $pcArti.= '<span class="pc">[评测] <a href="' . $art['url'] . '" ' . $art['title_tmp'] . '>' . $art['title'] . '</a></span>';
                                        }
                                    }
                                    //热度
                                    if (1 == $i)
                                        $hotest = 0 != (int) $row['ip'] ? (int) $row['ip'] : 1;
                                    $hot = ceil(($row['ip'] / $hotest) * 30);
                                    //用户评分
                                    $star = ceil($row['user_mark'] / 5 * 55);
                                    $liClass = 1 == $i ? ' class="cur" id="rank_li_one"' : '';
                                    $priceStr = '';
                                    if (!(0 == $row['mMin'] && 0 == $row['mMax'])) {
                                        if (strlen((int) $row['mMax']) > 5) {
                                            $priceStr = '￥' . $row['mMax'];
                                        } else {
                                            $priceStr = $row['mMin'] == 0 && $row['mMax'] == 0 ? '' : '￥' . $row['mMin'] . '-' . '￥' . $row['mMax'];
                                        }
                                    }
                                    $priceStr = $priceStr ? '<li>商家报价：<em>' . $priceStr . '</em></li>' : '';
                                    $productList2 .='<li' . $liClass . '>
												<em class="clearfix">
												<span class="nu_' . $i . '"></span><a href="' . $row['url'] . '" class="a_hover" ' . $row['titletmp'] . '>' . $row['title'] . '</a>
												<em class="rd" style="width:' . $hot . 'px"></em>
												</em>
									            <dl class="lidl clearfix">
									              <dt><a href="' . $row['url'] . '"><img src="' . $row['pic'] . '" width="80" height="60" alt="' . $row['ftitle'] . '"></a></dt>
									              <dd>
									                <ul>' . $priceStr . '
									                 <li><b>ZOL评分：</b><span class="xbg2"><em class="xbg3" style="width:' . $star . 'px;"></em></span></li>
									                  <li>' . $row['mark_num'] . '用户点评</li>
									                </ul>
									              </dd>
									            </dl>
									            ' . $pcArti . '						            
								          	</li>';
                                }
                            }
                        }
                        $productList2 .= '<!--#endif -->';
                    }

                    //新品 最新
                    $productList3 = '';
                    $paramArr3 = array(
                        'sub_id' => $subcatId,
                        'showimgFlag' => 1,
                        'titlelen' => $showLen,
                        'showimg' => 1,
                        'pwidth' => 80,
                        'pheight' => 60,
                        'priceFlag' => 1,
                        'priceUrlFlag' => 1,
                        'hudongflag' => 1,
                        'merchantFlag' => 1,
                        'orderby' => 1,
                        'level' => '+0',
                        'limit' => 'limit 0,' . $showCnt
                    );
                    $rows = PageHelper::getProductList($paramArr3);
                    if ($rows) {
                        $i = 0;
                        $hotest = 0;
                        foreach ($rows as $row) {
                            $i++;
                            $pcArti = '';
                            $pid = $row['pid'];
                            if (isset($testArticleArr[$pid])) {//先查看以前是否获得过
                                $artis = $testArticleArr[$pid];
                            } else {
                                $artis = self::getTechTestArt($array['classId'], $pid, 1, 36); //评测文章
                                $testArticleArr[$pid] = $artis;
                            }
                            $pcArti = '';
                            if ($artis) {
                                foreach ($artis as $art) {
                                    $pcArti.= '<span class="pc">[评测] <a href="' . $art['url'] . '" ' . $art['title_tmp'] . '>' . $art['title'] . '</a></span>';
                                }
                            }
                            //热度
                            /* if(1==$i)$hotest = 0!=(int)$row['ip']?(int)$row['ip']:1;
                              $hot = ceil(($row['ip']/$hotest)*30);
                              $hot = $hot > 30 ? 30 : $hot; */
                            //新品的点击时不规则的，所以只能这样计算一下
                            $hot = 30 - ($i - 1) * 2;

                            //用户评分
                            $star = ceil($row['user_mark'] / 5 * 55);
                            $liClass = 1 == $i ? ' class="cur" id="rank_li_one"' : '';
                            $priceStr = '';
                            if (!(0 == $row['mMin'] && 0 == $row['mMax'])) {
                                if (strlen((int) $row['mMax']) > 5) {
                                    $priceStr = '￥' . $row['mMax'];
                                } else {
                                    $priceStr = $row['mMin'] == 0 && $row['mMax'] == 0 ? '' : '￥' . $row['mMin'] . '-' . '￥' . $row['mMax'];
                                }
                            }
                            $priceStr = $priceStr ? '<li>参考报价：<em>' . $priceStr . '</em></li>' : '';
                            $productList3.='<li' . $liClass . '>
										<em class="clearfix">
										<span class="nu_' . $i . '"></span><a href="' . $row['url'] . '" class="a_hover" ' . $row['titletmp'] . '>' . $row['title'] . '</a>
										<em class="rd" style="width:' . $hot . 'px"></em>
										</em>
							            <dl class="lidl clearfix">
							              <dt><a href="' . $row['url'] . '"><img src="' . $row['pic'] . '" width="80" height="60" alt="' . $row['ftitle'] . '"></a></dt>
							              <dd>
							                <ul>' . $priceStr . '
							                <li><b>ZOL评分：</b><span class="xbg2"><em class="xbg3" style="width:' . $star . 'px;"></em></span></li>
							                  <li>' . $row['mark_num'] . '用户点评</li>
							                </ul>
							              </dd>
							            </dl>
							            ' . $pcArti . '						            
						          	</li>';
                        }
                    }

                    //第四个切换层 各产品线不同
                    $lastLi = '';
                    $productList4 = '';
                    if (16 == $subcatId || 31 == $subcatId) {//笔记本、服务器 加系列
                        $lastLi = '<li rel="productRank_4">系列</li>';
                        $paramArrTmp = array(
                            'limit' => "limit 0,10",
                            'orderby' => "2",
                            'sub_id' => $subcatId,
                            'len ' => $showLen,
                            'getproflag' => '1',
                            'imgwidth' => '80',
                            'imgheight' => '60',
                            'premanu' => 1
                        );
                        $rows = PageHelper::getProductSeries($paramArrTmp);
                        if ($rows) {
                            $i = 0;
                            foreach ($rows as $row) {
                                $i++;
                                //得到主产品的上市时间
                                $mainPid = $row['main_pro_id'];
                                $sql = 'select input_time from product_main_id_set where this_id=' . $mainPid;
                                $shangShi = $DB_Product_Read->get_var($sql);
                                $shangShi = str_replace('-', '年', substr(trim($shangShi), 0, 7)) . '月';
                                //得到该系列的报价区间
                                $sql = 'select min(price),max(price) from product_search_index where productid in (' . $row['pro_ids'] . ') and price > 0';
                                $priceArr = $DB_Product_Read->get_results($sql);
                                $priceMin = (int) $priceArr[0]['min(price)'];
                                $priceMax = (int) $priceArr[0]['max(price)'];
                                $priceStr = '';
                                if ($priceMin && $priceMax && $priceMin != $priceMax && strlen($priceMax) < 6) {
                                    $priceStr = '￥' . $priceMin . '-￥' . $priceMax;
                                } else if ($priceMin) {
                                    $priceStr = '￥' . $priceMin;
                                } else if ($priceMax) {
                                    $priceStr = '￥' . $priceMax;
                                }
                                $priceStr = $priceStr ? '<li>商家报价：<em>' . $priceStr . '</em></li>' : '';
                                //获得评测文章
                                $pid = $row['pro_ids'];
                                if (isset($testArticleArr[$pid])) {//先查看以前是否获得过
                                    $artis = $testArticleArr[$pid];
                                } else {
                                    $artis = self::getTechTestArt($array['classId'], $pid, 1, 36); //评测文章
                                    $testArticleArr[$pid] = $artis;
                                }
                                $pcArti = '';
                                if ($artis) {
                                    foreach ($artis as $art) {
                                        $pcArti.= '<span class="pc">[评测] <a href="' . $art['url'] . '" ' . $art['title_tmp'] . '>' . $art['title'] . '</a></span>';
                                    }
                                }

                                $liClass = 1 == $i ? ' class="cur" id="rank_li_one"' : '';
                                $hot = ceil($row['hot'] * 30 / 100);
                                $productList4.='<li' . $liClass . '>
											<em class="clearfix">
											<span class="nu_' . $i . '"></span><a href="' . $row['url'] . '" class="a_hover" ' . $row['titletmp'] . '>' . $row['title'] . '</a>
											<em class="rd" style="width:' . $hot . 'px"></em>
											</em>
								            <dl class="lidl clearfix">
								              <dt><a href="' . $row['url'] . '"><img src="' . $row['pic_src'] . '" width="80" height="60" alt="' . $row['ftitle'] . '"></a></dt>
								              <dd>
								                <ul>' . $priceStr . '
								                <li>上市时间： ' . $shangShi . '</li>
												<li>产品数： ' . $row['pro_num'] . '</li>
								                </ul>
								              </dd>
								            </dl>
								            ' . $pcArti . '						            
							          	</li>';
                            }
                        }
                    } else if (15 == $subcatId) {//数码相机
                        $lastLi = '<li rel="productRank_4">套机</li>';
                        $paramArrTmp = array(
                            'limit' => "limit 0,10",
                            'len' => $showLen,
                            'showimgflag' => '1',
                            'pwidth' => '80',
                            'pheight' => '60',
                        );
                        $rows = PageHelper::getCameraExtra($paramArrTmp);
                        if ($rows) {
                            $i = 0;
                            $hotest = 0;
                            foreach ($rows as $row) {
                                $i++;
                                $pcArti = '';
                                $pid = $row['pid'];
                                if (isset($testArticleArr[$pid])) {//先查看以前是否获得过
                                    $artis = $testArticleArr[$pid];
                                } else {
                                    $artis = self::getTechTestArt($array['classId'], $pid, 1, 36); //评测文章
                                    $testArticleArr[$pid] = $artis;
                                }
                                $pcArti = '';
                                if ($artis) {
                                    foreach ($artis as $art) {
                                        $pcArti.= '<span class="pc">[评测] <a href="' . $art['url'] . '" ' . $art['title_tmp'] . '>' . $art['title'] . '</a></span>';
                                    }
                                }
                                $liClass = 1 == $i ? ' class="cur" id="rank_li_one"' : '';
                                $priceStr = $row['price'] ? $row['price'] : '暂无报价';
                                $productList4.='<li' . $liClass . '>
											<em class="clearfix">
											<span class="nu_' . $i . '"></span><a href="' . $row['url'] . '" class="a_hover" ' . $row['titletmp'] . '>' . $row['title'] . '</a>
											</em>
								            <dl class="lidl clearfix">
								              <dt><a href="' . $row['url'] . '"><img src="' . $row['pic'] . '" width="80" height="60" alt="' . $row['ftitle'] . '"></a></dt>
								              <dd>
								                <ul>
								                  <li>商家报价：<em>' . $priceStr . '</em></li>
								                </ul>
								              </dd>
								            </dl>
								            ' . $pcArti . '						            
							          	</li>';
                            }
                        }
                    }
                    if ($productList4 && $lastLi) {
                        $lastLiRel = ' <ul class="rank_ul clearfix" id="productRank_4" style="display:none">
							        ' . $productList4 . '
							    </ul>';
                    }
                    if ($sub_icnt > 1) {
                        if (1 == $sub_i) {
                            $productStr .= '<!--#if expr="$sub_id=' . $subcatId . '" -->';
                        } else if ($sub_icnt == $sub_i) {
                            $productStr .= '<!--#else -->';
                        } else {
                            $productStr .= '<!--#elif expr="$sub_id=' . $subcatId . '" -->';
                        }
                    }
                    $productStr .= '<div class="r_bd mt10 pb10">
						        <div class="tit_5 tit_6"><span>TOP10</span>周热门' . $subName . '排行榜</div>
						        <ul class="tab_ul switch clearfix">
						          <li class="now" rel="productRank_1">同产品</li>
						          <li rel="productRank_2" id="samePriceLi">同价位</li>
						          <li rel="productRank_3">新品</li>
						          ' . $lastLi . '
						        </ul>
						        <ul class="rank_ul clearfix" id="productRank_1">
						        ' . $productList1 . '
						        </ul>
						        <ul class="rank_ul clearfix" id="productRank_2" style="display:none">
						        ' . $productList2 . '
						        </ul>
						        <ul class="rank_ul clearfix" id="productRank_3" style="display:none">
						        ' . $productList3 . '
						        </ul>
						        ' . $lastLiRel . '
						        <a href="http://top.zol.com.cn/compositor/' . $subEName . '.html" class="lok_bd">查看完整榜单>></a>
					       </div>';
                    if ($sub_icnt > 1 && $sub_icnt == $sub_i) {
                        $productStr .= '<!--#endif -->';
                    }
                    $sub_i++;
                }
            }
        }
        return $productStr;
    }

    //-------------------------------------------
    //产品排行榜-2014     右侧区块2(2014)
    //-------------------------------------------
    public static function productRank2014($array) {
    	global $DB_Product_Read, $db_read2;
    	$showCnt = 10; //显示的条数
    	$showLen = 25; //显示的标题长度
    	//配合产品库推广新品，此模板进行独立发布 2011-3-15 wangml
    	$productStr = '';
    	$subcat_id = $array['subcatid'];
    	$relcat_id = $array['relcatid'];
    	//暂时关闭--测试完开启
    	//$productStr = '<!--#if expr="$sub_id > 0" --><!--#include virtual="/dynamic/article_domain_product_${sub_id}_2014.html"--><!--#else --><!--#include virtual="/dynamic/article_domain_product_' . $subcat_id . '_2014.html"--><!--#endif -->';
    	//return $productStr;
    
    
    	$sub_id_str = '';
    	if ($subcat_id) {
    		$sub_id_str .= $subcat_id;
    		if ($relcat_id) {
    			$sub_id_str .= ',' . $relcat_id;
    		}
    	}
    	if ($sub_id_str) {
    		$sub_arr = array_unique(explode(',', $sub_id_str));
    		$sub_i = 1;
    		$sub_icnt = count($sub_arr);
    		foreach ($sub_arr as $subcatId) {
    			//保存评测文章，避免重复取得
    			$testArticleArr = array();
    			if ($subcatId) {
    				//获得产品线英文名
    				$sql = 'select brief from subcategory_extra_info where subcategory_id=' . $subcatId;
    				$subEName = trim($DB_Product_Read->get_var($sql));
    				//获得产品线名称
    				$sql = 'select name from subcategory where id=' . $subcatId;
    				$subName = $DB_Product_Read->get_var($sql);
    				//产品 最热
    				$productList1 = '';
    				$paramArr1 = array(
    						'sub_id' => $subcatId,
    						'showimgFlag' => 1,
    						'titlelen' => $showLen,
    						'showimg' => 1,
    						'pwidth' => 80,
    						'pheight' => 60,
    						'priceFlag' => 1,
    						'priceUrlFlag' => 1,
    						'hudongflag' => 1,
    						'merchantFlag' => 1,
    						'orderby' => 2,
    						'limit' => 'limit 0,' . $showCnt
    				);
    				$rows = PageHelper::getProductList($paramArr1);
    				if ($rows) {
    					$i = 0;
    					$hotest = 0;
    					foreach ($rows as $row) {
    						$i++;
    						$pcArti = '';
    						$pid = $row['pid'];
    						$priceStr = $row['price'];
    						if($i<4){
    							$productList1 .='<li class="special">
									<em class="n1">'.$i.'</em>
									<a href="'.$row['url'].'" class="pic">
										<img width="60" height="48" .src="'.$row['pic'].'" alt="'.$row['ftitle'].'">
									</a>
									<a href="'.$row['url'].'" class="title" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a>
									<span class="price">'.$priceStr.'</span>
									
								</li>';
    						}else{
    							$productList1 .='<li><em class="n2">'.$i.'</em><a href="'.$row['url'].'" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a><span class="price">'.$priceStr.'</span></li>';
    						}
    					}
    				}
                
    				//产品  最新
    				$productList2 = '';
    				$paramArr2 = array(
    						'sub_id' => $subcatId,
    						'showimgFlag' => 1,
    						'titlelen' => $showLen,
    						'showimg' => 1,
    						'pwidth' => 80,
    						'pheight' => 60,
    						'priceFlag' => 1,
    						'priceUrlFlag' => 1,
    						'hudongflag' => 1,
    						'merchantFlag' => 1,
    						'orderby' => 1,
    						'level' => '+0',
    						'limit' => 'limit 0,' . $showCnt
    				);
    				$rows = PageHelper::getProductList($paramArr2);
    				if ($rows) {
    					$i = 0;
    					$hotest = 0;
    					foreach ($rows as $row) {
    						$i++;
    						$pcArti = '';
    						$pid = $row['pid'];
    						$priceStr = '';
    						$priceStr = $row['price'];
    						if($i<4){
    							$productList2 .='<li class="special">
									<em class="n1">'.$i.'</em>
									<a href="'.$row['url'].'" class="pic">
										<img width="60" height="48" .src="'.$row['pic'].'" alt="'.$row['ftitle'].'">
									</a>
									<a href="'.$row['url'].'" class="title" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a>
									<span class="price">'.$priceStr.'</span>
									
								</li>';
    						}else{
    							$productList2 .='<li><em class="n2">'.$i.'</em><a href="'.$row['url'].'" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a><span class="price">'.$priceStr.'</span></li>';
    						}
    					}
    				}
    
    				//第三个切换层 各产品线不同
    				$lastLi = '';
    				$productList4 = '';
    				if (16 == $subcatId || 31 == $subcatId ) {//笔记本、服务器 加系列
    					$lastLi = '<li rel="tab_nb_3">系列</li>';
    					$paramArrTmp = array(
    							'limit' => "limit 0,10",
    							'orderby' => "2",
    							'sub_id' => $subcatId,
    							'len ' => $showLen,
    							'getproflag' => '1',
    							'imgwidth' => '80',
    							'imgheight' => '60',
    							'premanu' => 1
    					);
    					$rows = PageHelper::getProductSeries($paramArrTmp);
    					if ($rows) {
    						$i = 0;
    						foreach ($rows as $row) {
    							$i++;
    							//得到该系列的报价区间
    							$sql = 'select min(price),max(price) from product_search_index where productid in (' . $row['pro_ids'] . ') and price > 0';
    							$priceArr = $DB_Product_Read->get_results($sql);
    							$priceMin = (int) $priceArr[0]['min(price)'];
    							$priceMax = (int) $priceArr[0]['max(price)'];
    							$priceStr = $priceMin;
    							if($i<4){
    								$productList4 .='<li class="special">
										<em class="n1">'.$i.'</em>
										<a href="'.$row['url'].'" class="pic">
											<img width="60" height="48" .src="'.$row['pic_src'].'" alt="'.$row['ftitle'].'">
										</a>
										<a href="'.$row['url'].'" class="title" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a>
										<span class="price">'.$priceStr.'</span>
										
									</li>';
    							}else{
    								$productList4 .='<li><em class="n2">'.$i.'</em><a href="'.$row['url'].'" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a><span class="price">'.$priceStr.'</span></li>';
    							}
    						}
    					}
    				} else if (15 == $subcatId) {//数码相机
    					$lastLi = '<li rel="tab_nb_3">套机</li>';
    					$paramArrTmp = array(
    							'limit' => "limit 0,10",
    							'len' => $showLen,
    							'showimgflag' => '1',
    							'pwidth' => '80',
    							'pheight' => '60',
    					);
    					$rows = PageHelper::getCameraExtra($paramArrTmp);
    					if ($rows) {
    						$i = 0;
    						$hotest = 0;
    						foreach ($rows as $row) {
    							$i++;
    							$pcArti = '';
    							$pid = $row['pid'];
    							$priceStr = $row['price'] ? $row['price'] : '暂无报价';
    							if($i<4){
    								$productList4 .='<li class="special">
										<em class="n1">'.$i.'</em>
										<a href="'.$row['url'].'" class="pic">
											<img width="60" height="48" .src="'.$row['pic'].'" alt="'.$row['ftitle'].'">
										</a>
										<a href="'.$row['url'].'" class="title" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a>
										<span class="price">'.$priceStr.'</span>
										
									</li>';
    							}else{
    								$productList4 .='<li><em class="n2">'.$i.'</em><a href="'.$row['url'].'" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a><span class="price">'.$priceStr.'</span></li>';
    							}
    						}
    					}
    				}
    				if ($productList4 && $lastLi) {
    					$lastLiRel = ' <ul class="intel-ranking" id="tab_nb_3" style="display:none">' . $productList4 . '</ul>';
    				}
    				if ($sub_icnt > 1) {
    					if (1 == $sub_i) {
    						$productStr .= '<!--#if expr="$sub_id=' . $subcatId . '" -->';
    					} else if ($sub_icnt == $sub_i) {
    						$productStr .= '<!--#else -->';
    					} else {
    						$productStr .= '<!--#elif expr="$sub_id=' . $subcatId . '" -->';
    					}
    				}
    				$productStr .= '<div class="module">
                    			<div class="module-header"><h3>周热门'.$subName.'排行榜</h3></div>
						        <ul class="rank-tab switch clearfix">
						          <li class="first current" rel="tab_nb_1">热门</li>
									<li rel="tab_nb_2">新品</li>
						          ' . $lastLi . '
						        </ul>
						        <ul class="intel-ranking" id="tab_nb_1">
						        ' . $productList1 . '
						        </ul>
						        <ul class="intel-ranking" id="tab_nb_2" style="display:none">
						        ' . $productList2 . '
						        </ul>
						        ' . $lastLiRel . '
					       </div>';
    				if ($sub_icnt > 1 && $sub_icnt == $sub_i) {
    					$productStr .= '<!--#endif -->';
    				}
    				$sub_i++;
    			}
    		}
    	}
    	return $productStr;
    }
    
    
    //-------------------------------------------
    //获得评测文章 
    //-------------------------------------------
    public static function getTechTestArt($cid, $pid, $cnt=1, $len=36) {
        $paramArrTmp = array(
            'cid' => $cid,
            'len' => $len,
            'limit' => 'limit 0,' . $cnt,
            'byHardWareFlag' => 1,
            'hardwareids' => $pid,
            'orderby' => 1,
            'docTypeId' => 3 //取评测文章
        );
        $artis = PageHelper::getArticleList($paramArrTmp);
        return $artis;
    }

    //-------------------------------------------
    // 文章排行榜
    //-------------------------------------------	
    public static function articleRank($array) {
        $str = '<!--#include virtual="/include/article_domain_article${subclass_id}.html"-->';
        return $str;
    }

    //论坛内容
    public static function bbsRank($array) {

        //论坛读取新规2011-11-3
        //读资讯手工的频道
        $modulePicArr = array(
            '168' => 16021, //POWER
            '196' => 16851, //GPS
            '145' => 15311, //DCDV
            '74' => 14975, //MOBILE
            '182' => 16024, //DIY
            '62' => 16004, //cpu
            '195' => 16024, //mouse
            '282' => 16024, //cooler
            '63' => 16005, //memory
            '180' => 16022, //vga
            '206' => 16024, //lcd
            '164' => 16024, //sound
            '165' => 16020, //mb
        );
        //读论坛手工的频道
        $bbsModulePicArr = array(
            //'74' =>array('dbname'=>'sjbbs','moduleid'=>14975),
            '210' => array('moduleid' => 16916, 'dbname' => 'nbbbs', 'enname' => 'nbbbs'),
        );
        //频道对应论坛地址
        $channelRelBbs = array(
            '210' => 'http://nbbbs.zol.com.cn/',
            '74' => 'http://sjbbs.zol.com.cn/',
            '145' => 'http://dcbbs.zol.com.cn/',
            '182' => 'http://diybbs.zol.com.cn/', //DIY
            '62' => 'http://diybbs.zol.com.cn/cate_list_2.html', //cpu
            '195' => 'http://diybbs.zol.com.cn/cate_list_17.html', //mouse
            '282' => 'http://diybbs.zol.com.cn/cate_list_15.html', //cooler
            '63' => 'http://diybbs.zol.com.cn/cate_list_11.html', //memory
            '180' => 'http://diybbs.zol.com.cn/cate_list_3.html', //vga
            '206' => 'http://diybbs.zol.com.cn/cate_list_5.html', //lcd
            '164' => 'http://diybbs.zol.com.cn/cate_list_12.html', //sound
            '165' => 'http://diybbs.zol.com.cn/cate_list_4.html', //mb
            '300' => 'http://padbbs.zol.com.cn/', //pad
            '257' => 'http://sjbbs.zol.com.cn/', //3g
            '212' => 'http://nbbbs.zol.com.cn/', //pc
            '230' => 'http://nbbbs.zol.com.cn/', //netbook
            '265' => 'http://nbbbs.zol.com.cn/', //aio
            '289' => 'http://dcbbs.zol.com.cn/', //dv
            '303' => 'http://group.zol.com.cn/subcate_list_223.html', //headphone
            '168' => 'http://diybbs.zol.com.cn/cate_list_13.html', //power
        );
        //各频道论坛帖文字链对应手工ID (有接口数据的读接口数据)
        $moduleArr = array(
            '62' => 16004, //cpu
            '165' => 16020, //mb
            '182' => 16024, //DIY
            '180' => 16022, //vga
            /*
              '303'=>16062,
              '230'=>16037,
              '212'=>16038,
              '265'=>16050,
              '300'=>16059,
              '289'=>16018,
              '194'=>16027,
              '195'=>16028,//mouse
              '282'=>16021,//cooler
              '168'=>16021,//POWER
              '63' =>16005,//memory
              '206'=>16035,//lcd
              '164'=>16019,//sound
             */
        );

        $classId = $array['classId'];
        $subcateId = $array['subcatid'];
        $limit = 'limit 0,3';
        /* 		if($modulePicArr[$classId] || $bbsModulePicArr[$classId]){
          $moduleId = $modulePicArr[$classId]?$modulePicArr[$classId]:$bbsModulePicArr[$classId]['moduleid'];
          $paramArr = array(
          'moduleids'=>$moduleId,
          'len'      =>25,
          'orderby'  =>'order by date desc',
          'limit'    =>$limit,
          'getImageFlag'=>1,
          );
          if($bbsModuleArr[$classId]){
          $paramArr['dbname']= $bbsModulePicArr[$classId]['dbname'];
          $paramArr['enname']= $bbsModulePicArr[$classId]['enname'];
          $rows = PageHelper::getBbsModule($paramArr);
          }else{
          $rows = PageHelper::getModuleArt($paramArr);
          }

          }
          if(!$rows) return; */
        $picStr = '';
        $out = '';
        if ($rows) {
            foreach ($rows as $row) {
                $picStr.= '<li><a href="' . $row['url'] . '" ' . $row['title_tmp'] . '><img width="80" height="60" src="' . $row['pic_src'] . '" alt="' . $row['ftitle'] . '">' . $row['title'] . '</a></li>';
            }
        }
        if ($picStr)
            $picStr = '<ul class="sp_ul clearfix">' . $picStr . '</ul>';

        $swit_arr = array(62, 63, 74, 257, 145, 210, 300, 230, 212, 265, 289, 194, 195, 282, 168, 206, 164, 303);
        if (in_array($classId, $swit_arr)) {
            $i_val = 10;
            if ($classId == 230 || $classId == 212 || $classId == 265) {
                $subcateId = 16;
            }
            if ($classId == 289) {
                $subcateId = 15;
            }
            $jingxuan = file_get_contents('http://groupadmin.zol.com.cn/manage/smallapp/get_jingxuan.php?id=' . $subcateId);
            $jingxuan = unserialize($jingxuan);
            if ($jingxuan) {
                foreach ($jingxuan as $key => $row) {
                    $row_url = cnsubstr($row->title, 40);
                    ${'list_' . $key % $i_val}.= '<li><a href="' . $row->url . '" target="_blank">' . $row_url . '</a></li>';
                }

                for ($k = 0; $k < $i_val; $k++) {
//                    ${'article_list_' . $k} = '<div class="r_bd mt10 pb10">
//                        <div class="tit_5 tit_6"><a href="' . $channelRelBbs[$classId] . '">进入论坛</a>' . str_replace('频道', '', $array['className']) . '论坛精选</div>
//                        <ul class="rank_ul2 rank_dot">' . ${'list_' . $k} . '</ul>
//                    </div>';
                    
                    ${'article_list_' . $k} = '<div class="r_bd mt10 pb10">
                        <div class="tit_5 tit_6"><a href="' . $channelRelBbs[$classId] . '">进入论坛</a>' . str_replace('频道', '', $array['className']) . '网友精品</div>
                        <ul class="tab_ul switch clearfix"><li class="now" rel="bbsRank_1">论坛精选</li><!--#if expr="$hardware_id > 0" --><li rel="askRank_1"><a href="http://ask.zol.com.cn/today/">热门问题</a></li><!--#endif --></ul>
                        <ul id="bbsRank_1" class="rank_ul2 rank_dot" style="border-top:1px solid #AAC5F2;margin-top: -1px;">' . ${'list_' . $k} . '</ul>
                        <!--#if expr="$hardware_id > 0" --><!--#set var="ProRelAsk" value="/productInfo/relAsk/${hardware_path}/artiPage_${hardware_id}.html" --><!--#include virtual="${ProRelAsk}"--><!--#endif -->
                    </div>';
                    $file_name = $array['path'] . '/include/bbsrank_' . $k . '.html';
                    file_put_contents($file_name, ${'article_list_' . $k});
                    //评论最终页用
                    ${'comment_list_' . $k} = '<div class="mod-side">
                    <h3>' . str_replace('频道', '', $array['className']) . '论坛精选</h3>
                        <p class="more"><a target="_blank" href="' . $channelRelBbs[$classId] . '">进入论坛&gt;&gt;</a></p>
                        <ul class="article-rank">' . ${'list_' . $k} . '</ul>
							</div>';
                    $file_name = '/www/article/html/admin/comments/static/bbs' . $classId . '_' . $k . '.html';
                    if ($no_refresh) {
                        ${'comment_list_' . $k} = '';
                    }
                    file_put_contents($file_name, ${'comment_list_' . $k});
                }
                $out = '<!--#if expr="$doc_id_random > 0" --><!--#include virtual="/include/bbsrank_${doc_id_random}.html"--><!--#else --><!--#include virtual="/include/bbsrank_0.html"--><!--#endif -->';
            }
            return $out;
        } else {
            $limit = 'limit 0,100';
            $paramArr = array(
                'moduleids' => $moduleArr[$classId],
                'len' => 40,
                'orderby' => 'order by date desc',
                'limit' => $limit,
                'getImageFlag' => 2,
            );
            $rows = PageHelper::getModuleArt($paramArr);
            $date = $rows[0]['date'];
            $time = strtotime($date);
            $no_refresh = 0;
            if (time() - $time > 1209600) {
                $no_refresh = 1;
            }

            if ($rows) {
                $icnt = count($rows);
                foreach ($rows as $key => $row) {
                    ${'list_' . $key % 10}.= '<li><a href="' . $row["url"] . '" ' . $row["title_tmp"] . '>' . $row["title"] . '</a></li>';
                }
                for ($k = 0; $k < 10; $k++) {
                    ${'article_list_' . $k} = '<div class="r_bd mt10 pb10">
							        <div class="tit_5 tit_6"><a href="' . $channelRelBbs[$classId] . '">进入论坛</a>' . str_replace('频道', '', $array['className']) . '论坛精选</div>
							        <ul class="rank_ul2 rank_dot">' . ${'list_' . $k} . '</ul>
								</div>';
                    $file_name = $array['path'] . '/include/bbsrank_' . $k . '.html';
                    file_put_contents($file_name, ${'article_list_' . $k});
                    //评论最终页用
                    ${'comment_list_' . $k} = '<div class="c_1_1 mt10">' . str_replace('频道', '', $array['className']) . '论坛热帖</div>
						        <div class="c_1_3"><ul>' . ${'list_' . $k} . '</ul><div><a href="' . $channelRelBbs[$classId] . '" target="_blank">进入论坛&gt;&gt;</a></div>
							</div>';
                    $file_name = '/www/article/html/admin/comments/static/bbs' . $classId . '_' . $k . '.html';
                    if ($no_refresh) {
                        ${'comment_list_' . $k} = '';
                    }
                    file_put_contents($file_name, ${'comment_list_' . $k});
                }
                $out = '<!--#if expr="$doc_id_random > 0" --><!--#include virtual="/include/bbsrank_${doc_id_random}.html"--><!--#else --><!--#include virtual="/include/bbsrank_0.html"--><!--#endif -->';
            }
            return $out;
        }

        /* 		
          //各频道论坛帖对应手工ID
          $moduleArr = array(
          '62' =>16004,//cpu
          '210'=>16037,//NB
          '230'=>16037,
          '212'=>16038,
          '265'=>16050,
          '300'=>16059,
          '289'=>16018,
          '145'=>16018,//DCDV
          '74' =>16007,//MOBILE
          '257'=>16007,
          '194'=>16027,
          '195'=>16028,//mouse
          '282'=>16021,//cooler
          '168'=>16021,//POWER
          '63' =>16005,//memory
          '180'=>16022,//vga
          '206'=>16035,//lcd
          '164'=>16019,//sound
          '165'=>16020,//mb
          '182'=>16024,//DIY
          '303'=>16062,
          );
          $out = '';
          $classId = $array['classId'];
          $limit = 'limit 0,50';
          if($moduleId=$moduleArr[$classId]){
          $paramArr = array(
          'moduleids'=>$moduleId,
          'len'      =>38,
          'orderby'  =>'order by date desc',
          'limit'    =>$limit
          );
          $rows = PageHelper::getModuleArt($paramArr);
          if($rows){
          $icnt = count($rows);
          $last = floor(($icnt-1)/5)+1;
          $list_0 = '';
          $list_1 = '';
          $list_2 = '';
          $list_3 = '';
          $list_4 = '';
          foreach($rows as $key=>$row){
          $i = floor($key/5)+1;
          $lastLi = $last==$i?' class="lastli"':'';
          if(0==$key%5)$list_0.= '<li '.$lastLi.'><span class="nu_'.$i.'"></span><a href="'.$row["url"].'" '.$row["title_tmp"].'>'.$row["title"].'</a></li>';
          if(1==$key%5)$list_1.= '<li '.$lastLi.'><span class="nu_'.$i.'"></span><a href="'.$row["url"].'" '.$row["title_tmp"].'>'.$row["title"].'</a></li>';
          if(2==$key%5)$list_2.= '<li '.$lastLi.'><span class="nu_'.$i.'"></span><a href="'.$row["url"].'" '.$row["title_tmp"].'>'.$row["title"].'</a></li>';
          if(3==$key%5)$list_3.= '<li '.$lastLi.'><span class="nu_'.$i.'"></span><a href="'.$row["url"].'" '.$row["title_tmp"].'>'.$row["title"].'</a></li>';
          if(4==$key%5)$list_4.= '<li '.$lastLi.'><span class="nu_'.$i.'"></span><a href="'.$row["url"].'" '.$row["title_tmp"].'>'.$row["title"].'</a></li>';
          }
          for($k=0;$k<5;$k++){
          ${'list_'.$k} = '<div class="r_bd mt10 pb10">
          <div class="tit_5 tit_6"><span>TOP10</span>热门帖子排行榜</div>
          <div class="ph_d"><span class="p_h">排行</span> <span class="b_t">标题</span><span class="p_l"> </span></div>
          <ul class="rank_ul2">'.${'list_'.$k}.'</ul>
          </div>';
          $file_name = $array['path'].'/include/bbsrank_'.$k.'.html';
          file_put_contents($file_name,${'list_'.$k});
          }
          $out = '<!--#if expr="$doc_id = /[0,5]$/ " -->
          <!--#include virtual="/include/bbsrank_0.html"-->
          <!--#elif expr="$doc_id = /[1,6]$/ " -->
          <!--#include virtual="/include/bbsrank_1.html"-->
          <!--#elif expr="$doc_id = /[2,7]$/ " -->
          <!--#include virtual="/include/bbsrank_2.html"-->
          <!--#elif expr="$doc_id = /[3,8]$/ " -->
          <!--#include virtual="/include/bbsrank_3.html"-->
          <!--#elif expr="$doc_id = /[4,9]$/ " -->
          <!--#include virtual="/include/bbsrank_4.html"-->
          <!--#else -->
          <!--#include virtual="/include/bbsrank_0.html"-->
          <!--#endif -->';
          }
          }
          return $out;
         */
    }

    //热门榜单+论坛精选新--进行合并读取---需要获取关联的产品ID---$hardware_id
    public static function bbsRank2014($array) {
        //论坛模块问题临时处理下
        return '';
    	global $DB_Document_Read;
		#获取热门文章榜单
		$paramArr = array('cid' => $array['classId'],'limit' => "limit 0,10",'ispub' => "1",'len' => "30",'orderby' => "2",'hourbtn'=>"168");
		$rows = PageHelper::getArticleListByType($paramArr);
		if($rows){
			$hotDocStr ='<ul class="intel-ranking rank-news" id="tab_list_1">';
			$i=1;
			foreach ($rows as $k=>$row){
				$num = $i<4 ? 'n1' : 'n2';
				$hotDocStr .='<li><em class="'.$num.'">'.$i.'</em><a href="'.$row['url'].'" title="'.$row['ftitle'].'">'.$row['ftitle'].'</a></li>';
				$i++;
			}
			$hotDocStr .='</ul>';
		}
		#获取百科榜单 部分频道不一定有,且必须大于4条数据才会显示出来
		$hotwikiStr = $hotwikiTitle = '';
        $sql = 'select z_title,z_docid,i.class_id from z_document_id_title t left join doc_index i on t.z_docid = i.document_id where t.z_flag = 0 and i.class_id = ' . $array['classId'];
        $rows = $DB_Document_Read->get_results($sql);
        $icnt = count($rows);
        if ($rows && $icnt > 4) {
        	$hotwikiStr = '<ul class="intel-ranking rank-news" id="tab_list_3" style="display:none;">';
        	$hotwikiTitle = '<li rel="tab_list_3">百科</li>';
            shuffle($rows);
            $i = 1;
            foreach ($rows as $row) {
            	$num = $i<4 ? 'n1' : 'n2';
                $title = str_replace('-中关村在线', '', $row['z_title']);
                $url = get_document_url($row['z_docid'], 1, 0, $row['class_id']);
                $hotwikiStr.= '<li><em class="'.$num.'">'.$i.'</em><a href="'.$url.'" title="'.htmlspecialchars($title).'">'.$title.'</a></li>';
                if (10 == $i) {
                    break;
                }
                $i++;
            }
            $hotwikiStr.='</ul>';
        }
        //论坛读取新规2011-11-3
        //读资讯手工的频道---也不一定有
        $modulePicArr = array(
            '168' => 16021, //POWER
            '196' => 16851, //GPS
            '145' => 15311, //DCDV
            '74' => 14975, //MOBILE
            '182' => 16024, //DIY
            '62' => 16004, //cpu
            '195' => 16024, //mouse
            '282' => 16024, //cooler
            '63' => 16005, //memory
            '180' => 16022, //vga
            '206' => 16024, //lcd
            '164' => 16024, //sound
            '165' => 16020, //mb
        );
        //读论坛手工的频道
        $bbsModulePicArr = array(
            '210' => array('moduleid' => 16916, 'dbname' => 'nbbbs', 'enname' => 'nbbbs'),
        );
        //频道对应论坛地址
        $channelRelBbs = array(
            '210' => 'http://nbbbs.zol.com.cn/',
            '74' => 'http://sjbbs.zol.com.cn/',
            '145' => 'http://dcbbs.zol.com.cn/',
            '182' => 'http://diybbs.zol.com.cn/', //DIY
            '62' => 'http://diybbs.zol.com.cn/cate_list_2.html', //cpu
            '195' => 'http://diybbs.zol.com.cn/cate_list_17.html', //mouse
            '282' => 'http://diybbs.zol.com.cn/cate_list_15.html', //cooler
            '63' => 'http://diybbs.zol.com.cn/cate_list_11.html', //memory
            '180' => 'http://diybbs.zol.com.cn/cate_list_3.html', //vga
            '206' => 'http://diybbs.zol.com.cn/cate_list_5.html', //lcd
            '164' => 'http://diybbs.zol.com.cn/cate_list_12.html', //sound
            '165' => 'http://diybbs.zol.com.cn/cate_list_4.html', //mb
            '300' => 'http://padbbs.zol.com.cn/', //pad
            '257' => 'http://sjbbs.zol.com.cn/', //3g
            '212' => 'http://nbbbs.zol.com.cn/', //pc
            '230' => 'http://nbbbs.zol.com.cn/', //netbook
            '265' => 'http://nbbbs.zol.com.cn/', //aio
            '289' => 'http://dcbbs.zol.com.cn/', //dv
            '303' => 'http://group.zol.com.cn/subcate_list_223.html', //headphone
            '168' => 'http://diybbs.zol.com.cn/cate_list_13.html', //power
        );
        //各频道论坛帖文字链对应手工ID (有接口数据的读接口数据)
//        $moduleArr = array(
//            '62' => 16004, //cpu
//            '165' => 16020, //mb
//            '182' => 16024, //DIY
//            '180' => 16022, //vga
//        );
        $moduleArr = array();

        $classId = $array['classId'];
        $subcateId = $array['subcatid'];
        $limit = 'limit 0,3';
        $picStr = '';
        $out = '';
        if ($rows) {
            foreach ($rows as $row) {
                $picStr.= '<li><a href="' . $row['url'] . '" ' . $row['title_tmp'] . '><img width="80" height="60" src="' . $row['pic_src'] . '" alt="' . $row['ftitle'] . '">' . $row['ftitle'] . '</a></li>';
            }
        }
        if ($picStr)
            $picStr = '<ul class="sp_ul clearfix">' . $picStr . '</ul>';

        $swit_arr = array(62, 63, 74, 257, 145, 210, 300, 230, 212, 265, 289, 194, 195, 282, 168, 206, 164, 303);
        if (in_array($classId, $swit_arr)) {
            $i_val = 10;
            if ($classId == 230 || $classId == 212 || $classId == 265) {
                $subcateId = 16;
            }
            if ($classId == 289) {
                $subcateId = 15;
            }
            //精选文章接口拼接
            $jingxuan = file_get_contents('http://groupadmin.zol.com.cn/manage/smallapp/get_jingxuan.php?id=' . $subcateId);
            $jingxuan = unserialize($jingxuan);
            if ($jingxuan) {
            	$jingxuanLi = '';
                foreach ($jingxuan as $key => $row) {
                	if($key<6){//读取6条最新数据
                		$row_tit = cnsubstr($row->title, 40);
                		$row_url = $row->url;
                		$row_pic = $row->picurl;
                		$jingxuanLi .='<li><a href="'.$row_url.'" class="pic"><img .src="'.$row_pic.'" alt="" width="300" height="170"><span class="pic-title">'.$row_tit.'</span></a></li>';
                	}
                }
            } 
            //帖子排行接口
            ##获取热门帖子榜单
            $tidres = file_get_contents('http://groupadmin.zol.com.cn/manage/smallapp/get_booklist.php?id='.$subcateId);
            $tidres = unserialize($tidres);
            if($tidres){
            	$hotTidTitle='<li rel="tab_list_2">帖子</li>';
            	$hottidStr ='<ul class="intel-ranking rank-news" id="tab_list_2" style="display:none;">';
            	$ii = 1;
            	foreach ($tidres as $key=>$row){
            		$class1 = ($key >= 3) ? 'n2' :'n1';
            		$hottidStr .='<li><em class="'.$class1.'">'.$ii.'</em><a href="'.$row->url.'" title="'.htmlspecialchars($row->title).'">'.cnsubstr($row->title, 40).'</a></li>';
            		$ii++;
            	}
				$hottidStr .='</ul>';
            }
			
        } else {
            $limit = 'limit 0,6';
            $paramArr = array(
                'moduleids' => $moduleArr[$classId],
                'len' => 40,
                'orderby' => 'order by date desc',
                'limit' => $limit,
                'getImageFlag' => 2,
            );
            $rows = PageHelper::getModuleArt($paramArr);
            $date = $rows[0]['date'];
            $time = strtotime($date);
            $no_refresh = 0;
            if (time() - $time > 1209600) {
                $no_refresh = 1;
            }
            if ($rows) {
                $icnt = count($rows);
                $jingxuanLi = '';
                foreach ($rows as $key => $row) {
                    $jingxuanLi.= '<li><a href="'.$row["url"].'" class="pic"><img .src="'.$row["pic_src"].'" alt="'.$row["title_tmp"].'" width="300" height="170"><span class="pic-title">'.$row["title"].'</span></a></li>';
                }
            }
        }
        if($jingxuanLi){
        	$jingxuanStr = '<div class="module" id="bbs_jingxuan">
		                    	<div class="module-header"><a href="' . $channelRelBbs[$classId] . '" class="more">更多</a><h3>论坛精选</h3></div>
		                        <div class="bbs-slide">
									<a href="javascript:;" style="display:none" target="_self" class="prev-btn">上一组</a>
									<a href="javascript:;" style="display:none" target="_self" class="next-btn">下一组</a>
									<ul class="bbs-slide-inner clearfix" style="width:1500px;">'.$jingxuanLi.'</ul>
								</div>
							</div>';
        }
          //热门榜单开始组装$rankStr
         $rankStr = '<div class="module"><div class="module-header"><h3>热门榜单</h3></div>
				<ul class="rank-tab switch clearfix">
					<li class="first current" rel="tab_list_1">文章</li>
					'.$hotTidTitle.'
					'.$hotwikiTitle.'<!--#if expr="$hardware_id > 0" --><li rel="tab_list_ask">问答</li><!--#endif --></ul>';	
        $rankStr .=$hotDocStr.$hottidStr.$hotwikiStr.'<!--#if expr="$hardware_id > 0" --><!--#set var="ProRelAsk" value="/productInfo/relAsk/${hardware_path}/artiPage2014_${hardware_id}.html" --><ul class="intel-ranking rank-news" id="tab_list_ask" style="display:none;"><!--#include virtual="${ProRelAsk}"--></ul><!--#endif --></div>';
            
        return $rankStr;
    }
    
    //问答精选
    public static function askRanm($array) {
        
    }


    /*     * *****************各频道相同部分******************* */

    //-------------------------------------------
    // 左侧底部 视觉焦点
    //-------------------------------------------
    public static function visionFocus() {
        $str = '<div class="syzp mt10" style="overflow:hidden;"><div class="tit_7">视觉焦点</div>';
        $str .= '<script type="text/javascript" language="JavaScript">
zpro_n="3";
zpro_width= wide_screen_flag ? "798" : "668";
zpro_height="240";
</script>
<script src="http://sss.zol.com.cn/cgimp/zc.js" type="text/javascript" language="JavaScript"></script>';
        $str .= '</div>';
        return $str;
    }
    //-------------------------------------------
    // 左侧底部 视觉焦点2014
    //-------------------------------------------
    public static function visionFocus2014() {
    	$str  = '<div style="overflow: hidden;"><div class="section-head"><h2>更多精彩内容</h2></div>';
    	$str .= '<script type="text/javascript">zpro_n="41";zpro_width="600";zpro_height="260";</script><script src="http://sss.zol.com.cn/cgimp/zc.js" type="text/javascript"></script>';
    	$str .= '</div>';
    	return $str;
    }
    //右侧 热点推荐
    public static function hotRecom() {
    	return '';  //戴老板需要去掉 20150805
        $str = '<div class="r_bd mt10 pb10"><div style="margin-top: 0pt;" class="tit_5 tit_6">热点推荐</div>';
        $str .= '<script type="text/javascript" language="JavaScript">
zpro_n="1";
zpro_width="298";
zpro_height="355";
</script>
<script src="http://sss.zol.com.cn/cgimp/zc.js" type="text/javascript" language="JavaScript"></script>';
        $str .= '</div>';
        /*        $str = '<div class="r_bd mt10 pb10"><div style="margin-top: 0pt;" class="tit_5 tit_6">热门 FLASH 小游戏</div>';
          $str .= '<script type="text/javascript" language="JavaScript">
          zpro_n="35";
          zpro_width="300";
          zpro_height="350";
          </script>
          <script src="http://sss.zol.com.cn/cgimp/zc.js" type="text/javascript" language="JavaScript"></script>';
          $str .= '</div>'; */
        return $str;
    }
    //右侧 热点推荐2014
    public static function hotRecom2014() {
    	$str = '<div class="module">				
				<div class="module-header"><h3>周边热点推荐</h3></div>
				<script type="text/javascript" language="JavaScript">
				zpro_n="42";
				zpro_width="300";
				zpro_height="389";
				</script>
				<script src="http://sss.zol.com.cn/cgimp/zc.js" type="text/javascript" language="JavaScript"></script>
			</div>';
    	//$str .= '<div class="module"><iframe marginheight="0" marginwidth="0" frameborder="0" scrolling="no" width="300" height="345" src="http://hezuo.xgo.com.cn/chezhan/2014/bjCrop3.html"></iframe></div>';
    	return $str;
    }
    //-------------------------------------------
    // 关联产品
    //-------------------------------------------
    public static function relProduct($array) {
        $str = '<div id="ProductInfo" class="mt10"><!--#if expr="$hardware_id > 0" --><!--#set var="ProductInfoPath" value="/productInfo/relArticle/${hardware_path}/artiPage_${hardware_id}.html" --><!--#include virtual="$ProductInfoPath"--><!--#endif --></div>';
        return $str;
    }

    //步步高配合 add liufu
    public static function bubuGao($array) {
        $paramArr = array(
            'func_name' => "zol_t_getproinfo_bymodule",
            'param_myjson' => "modid:17774,imgwidth:120,imgheight:90",
        );
        $str = '<div class="r_bd mt10"><div class="tit_5 tit_6">vivo智能手机推荐</div><div class="phone-recommend">';
        $rows = zol_t_db_func_exec($paramArr);
        if ($rows) {
            $i = 1;
            $icnt = count($rows);
            foreach ($rows as $row) {
                $paramArr = array(
                    'hardwareids' => $row['id'],
                    'byHardWareFlag' => "1",
                    'subOrManuLen' => "6",
                    'getSubClassOrManuFlag' => "1",
                    'len' => "36",
                    'orderby' => "1",
                    'prop' => "2",
                    'cid' => "74",
                    'sid' => "428",
                    'limit' => "limit 0,2",
                );
                $rowa = PageHelper::getArticleListByType($paramArr);
                $digest = cnsubstr($row['mod_digest'], 80);
                if (false !== strpos($row['price_str'], '.'))
                    $row['price_str'] = substr($row['price_str'], 0, -3);
                $str.='	<dl class="r3_dl clearfix">
										<dt><a href="' . $row['pro_url'] . '"><img height="90" width="120" src="' . $row['pic'] . '" alt="' . $row['title'] . '"></a> </dt>
										<dd><ul>
											<li><a href="' . $row['pro_url'] . '" >' . $row['title'] . '</a></li>
											<li>商家报价：<em>' . $row['price_str'] . '</em></li>
											<li>网友点评（' . $row['u_num'] . '）</li>
											<li><a href="' . $row['pro_url'] . '">查看详细&gt;&gt;</a></li>
										</ul></dd>
									</dl>';
                $str.='<ul class="list2_ul">';
                foreach ($rowa as $article) {
                    $str.='<li><em>[评测]</em>&nbsp;<a href="' . $article["url"] . '"' . $article["title_tmp"] . '>' . $article["title"] . '</a></li>';
                }
            }
            $str.='</div></div>';
            return $str;
        }
    }

    //步步高配合 add weixj
    public static function bubuGao2014($array) {
    	$paramArr = array(
    			'func_name' => "zol_t_getproinfo_bymodule",
    			'param_myjson' => "modid:17774,imgwidth:120,imgheight:90",
    	);
    	$str = '<div class="r_bd mt10"><div class="tit_5 tit_6">vivo智能手机推荐</div><div class="phone-recommend">';
    	$str = '<div class="module vivo-phone-rec">
				<div class="module-header"><h3>vivo智能手机推荐</h3></div>
				<div class="pic-box">';
    	 
    	$rows = zol_t_db_func_exec($paramArr);
    	if ($rows) {
    		$i = 1;
    		$icnt = count($rows);
    		foreach ($rows as $row) {
    			$paramArr = array(
    					'hardwareids' => $row['id'],
    					'byHardWareFlag' => "1",
    					'subOrManuLen' => "6",
    					'getSubClassOrManuFlag' => "1",
    					'len' => "36",
    					'orderby' => "1",
    					'prop' => "2",
    					'cid' => "74",
    					'sid' => "428",
    					'limit' => "limit 0,2",
    			);
    			$rowa = PageHelper::getArticleListByType($paramArr);
    			$digest = cnsubstr($row['mod_digest'], 80);
    			if (false !== strpos($row['price_str'], '.'))
    				$row['price_str'] = substr($row['price_str'], 0, -3);
    			$str.='<ul class="pic-list">
						<li>
							<a href="'.$row['pro_url'].'" class="pic"><img width="120" height="90" src="'.$row['pic'].'" alt="'.$row['title'].'"></a>
							<div class="pic-title"><a href="'. $row['pro_url'].'">' . $row['title'] . '</a></div>
							<p>商家报价：<span class="price">' . $row['price_str'] . '</span></p>
							<p>网友点评（' . $row['u_num'] . '）</p>
							<p><a href="' . $row['pro_url'] . '">查看详细&gt;&gt;</a></p>
						</li>
					</ul>
					<ul class="news-list">';
    			foreach ($rowa as $article) {
    				$str.='<li><a href="' . $article["url"] . '"' . $article["title_tmp"] . '>' . $article["title"] . '</a></li>';
    			}
    			$str.='</ul>';
    		}
    		$str.='</div></div>';
    		return $str;
    	}
    }
    //-------------------------------------------
    //文章页中 ZDC投诉
    //-------------------------------------------
    public static function zdcComplain($array) {
        $str = '<div class="xg_ser zdcts clearfix">
					<a href="http://service.zol.com.cn/complain/complain.php?id=10"><img width="17" height="15" src="http://icon.zol-img.com.cn/article/2011/zdcts.png" border="0">
					数据不准，我要投诉</a><br/>版权说明:该文章由中关村在线ZDC调研中心版权所有，未以书面授权不得转载或摘录。
					<a class="zdc-focus" href="http://weibo.com/zolzdc2003">zdc加关注</a>
				</div>';
        return $str;
    }

    //-------------------------------------------
    //文章页中 ZDC投诉
    //-------------------------------------------
    public static function zdcComplain2014($array) {
    	$str = '<div class="zdcts clearfix">
					<a href="http://service.zol.com.cn/complain/complain.php?id=10"><img width="17" height="15" src="http://icon.zol-img.com.cn/article/2011/zdcts.png" border="0">
					数据不准，我要投诉</a><br/>版权说明:该文章由中关村在线ZDC调研中心版权所有，未以书面授权不得转载或摘录。
					<a class="zdc-focus" href="http://weibo.com/zolzdc2003">zdc加关注</a>
				</div>';
    	return $str;
    }
    
    //-------------------------------------------
    //文章页顶部<head>内部，判断宽窄屏幕的js代码 更新求团购变量
    //-------------------------------------------
    public static function headScreenCheckJs($array) {
    	defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//是否使用ZOL新框架，true为使用
    	require_once('/www/zdata/Api.php'); //引入私有云入口文件
    	$dataArr = ZOL_Api::run("Shop.TuanWant.getRequestTuanProductId" , array(
        	'isOnlyTop'      => 0,               #是否只获取Top数据
        ));
        $requestTuanStr = $dataArr ? 'var z_request_tuan_pro_ids = ['.$dataArr.'];' : '';
        return '<script>
			<!--
			if (screen.availWidth>"1149") {var wide_screen_flag = 1;}
			//-->
			'.$requestTuanStr.'
			</script>';
    }

    //-------------------------------------------
    //文章页顶部<head>内部，rss合作
    //-------------------------------------------
    public static function headRssCooper($array) {
        $rss_file_array = array(
            90 => array('新闻中心', 'http://rss.zol.com.cn/news.xml'),
            76 => array('MP3频道', 'http://rss.zol.com.cn/mp3.xml'),
            63 => array('内存硬盘', 'http://rss.zol.com.cn/memory.xml'),
            164 => array('音频频道', 'http://rss.zol.com.cn/sound.xml'),
            132 => array('服务器频道', 'http://rss.zol.com.cn/server.xml'),
            196 => array('GPS频道', 'http://rss.zol.com.cn/gps.xml'),
            96 => array('全国行情', 'http://rss.zol.com.cn/price.xml'),
            145 => array('数码影像', 'http://rss.zol.com.cn/dcdv.xml'),
            206 => array('显示器频道', 'http://rss.zol.com.cn/lcd.xml'),
            195 => array('键鼠频道', 'http://rss.zol.com.cn/mouse.xml'),
            188 => array('投影机频道', 'http://rss.zol.com.cn/projector.xml'),
            228 => array('游戏频道', 'http://rss.zol.com.cn/game.xml'),
            123 => array('调研中心', 'http://rss.zol.com.cn/zdc.xml'),
            210 => array('笔记本频道', 'http://rss.zol.com.cn/nb.xml'),
            165 => array('主板频道', 'http://rss.zol.com.cn/mb.xml'),
            168 => array('机箱电源', 'http://rss.zol.com.cn/power.xml'),
            129 => array('网络设备', 'http://rss.zol.com.cn/net.xml'),
            300 => array('平板电脑', 'http://rss.zol.com.cn/pad.xml'),
            74 => array('手机频道', 'http://rss.zol.com.cn/mobile.xml'),
            62 => array('CPU频道', 'http://rss.zol.com.cn/cpu.xml'),
            180 => array('显卡频道', 'http://rss.zol.com.cn/vga.xml'),
            194 => array('办公打印', 'http://rss.zol.com.cn/oa.xml'),
            200 => array('家电频道', 'http://rss.zol.com.cn/jd.xml'),
            238 => array('数字电视', 'http://rss.zol.com.cn/tv.xml'),
        );
        $str = '';
        if ($rss_file_array[$array['classId']]) {
            $info = $rss_file_array[$array['classId']];
            $str = '<link rel="alternate" type="application/rss+xml" title="' . $info[0] . '" href="' . $info[1] . '">';
        }
        return $str;
    }

    //-------------------------------------------
    // 分站交换文章热词链接
    //-------------------------------------------
    public static function priceSubsiteKeyWord($array) {

        return '<!--#if expr="$price_site_id > 0" -->
			<!--#include virtual="/include/priceSubsite_${price_site_id}.html"-->
			<!--#endif -->';
    }

    //-------------------------------------------
    // 限时合作项目
    //-------------------------------------------
    public static function cooperProject($array) {
        $endTime = '2013-05-01 01:59:59';
        $height = 344;
        $targetLink = 'http://hezuo.xgo.com.cn/chezhan/2013/zolArticleCrop.html';
        if (time() < strtotime($endTime)) {
            return '<div class="mt10" style="border:medium none">
		<iframe marginheight="0" marginwidth="0" frameborder="0" scrolling="no" width="300" height="' . $height . '" src="' . $targetLink . '"></iframe>
		</div>';
        }
    }

    //-------------------------------------------
    // 文章内容下相关内容
    //-------------------------------------------
    public static function docDownContent($array) {
        $str = '<div class="info_more" id="info_more"></div>';
        return $str;
    }
    
    //-------------------------------------------
    // 文章内容下相关内容
    //-------------------------------------------
    public static function docDownContent2014($array) {
        $str = '<div class="productbox"></div>';
        $str = '';
        return $str;
    }
    
    
    //-------------------------------------------
    // 文章内容下相关内容
    //-------------------------------------------
    public static function commentDown2014($array) {
        $str = '<div class="productbox"></div>';
        return $str;
    }
    
    //-------------------------------------------
    // 文章内容下相关内容
    //-------------------------------------------
    public static function docHotTag2014($array) {
        global $DB_Document_Read;
        $classId = $array['classId'];
        defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//是否使用ZOL新框架，true为使用
    	require_once('/www/zdata/Api.php'); //引入私有云入口文件
		//频道热词
		$tagArr = array();
		$sql = "select module_id from template_module_class where module_name='09文章页--热词' and classid=" . $classId . " and status=0";
		$moduleId = $DB_Document_Read->get_var($sql);
		$moduleArr = ZOL_Api::run("Article.Module.getCmsModule" , array(
				'moduleIds'      => $moduleId,           #手工ID
				'num'			 => 6,				     #数量	
		));
		if($moduleArr){
			foreach ($moduleArr as $val){
				$tagArr[]= array('name' => trim($val['title']), 'url' => $val['url']);
			}
		}
		//拼接字符串
		if(!$tagArr) return '';
			$str = '<div class="hotword-tag"><span>热词：</span>';
			foreach ($tagArr as $key=>$val){
				$str .= '<a href="'.$val['url'].'" title="'.htmlspecialchars($val['name']).'">'.$val['name'].'</a>';
			}
		$str .= '</div>';
		return $str;
    }

    //-------------------------------------------
    // 左侧底部 视觉焦点下模块
    //-------------------------------------------	
    public static function visionFocusDown() {
        //$str = '<div class="clearfix mt10" id="commentTopAd"><!--#include virtual="/include/ouku_cooperation.inc"--></div>';
        return $str;
    }

    //-------------------------------------------
    // 文章页顶部广告内容Js
    //-------------------------------------------	
    public static function headAd($array) {
        $url = $array['url'];
        $name = strtok($url, '.');
        $spec_arr = array('price' => 'market', 'lcd' => 'monitor', 'jd' => 'dhome', 'hd' => 'hdtv', 'netbook' => 'umpc');
        $name = $spec_arr[$name] ? $spec_arr[$name] : $name;
        # 针对“商用频道”(229)做处理，商用频道下显示smb下的广告   by suhy 20151109
        if($array['classId']==229){
        	$name = 'smb';
        }
        $str = '<!--#if expr="$province_id=2 || $class_id=96" --><script language="javascript" src="http://p.zol-img.com.cn/market/detail.js"></script><!--#else --><script language="javascript" src="http://p.zol-img.com.cn/' . $name . '/detail.js"></script><!--#endif -->';
$str .='<script type="text/javascript"> 
if(/AppleWebKit.*Mobile/i.test(navigator.userAgent) || (/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))){
    if(window.location.href.indexOf("?via=")<0){
        try{
            if(/Android|Windows Phone|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)){
                var tmp = location.href.match(/\d+\/(\d+)(_.*)*\.html/i);
                window.location.href="http://m.zol.com.cn/article/"+ tmp["1"]+".html?via=index";
            }
        }catch(e){}
    }
}
</script>
';    
        if($array['classId']==364){
            $str .='<script>var write_ad=function(){}</script>';
        }
        
        return $str;
    }

    //-------------------------------------------
    // 百度分享
    //-------------------------------------------	
    public static function baiduShareOld($array) {
        $str = '<!-- Baidu Button BEGIN -->
<div class="info_more" id="info_more"></div><!-- Baidu Button BEGIN -->
<div class="fixed-share-bar">
	<div id="bdshare" class="bdshare_t bds_tools get-codes-bdshare share-bar-btn">
		<a class="bds_tsina"></a>
		<a class="bds_qzone"></a>
		<a class="bds_tqq"></a>
  	<span class="bds_more share-more"></span>
  </div>
</div>
<script type="text/javascript">
var uid = "14034";if (!window.wbUid) { wbUid = "1747383115";}var bds_config = {"wbUid": wbUid,"render":"false","review":"off","snsKey":{"douban":"038da04052b48ba40aa8e0c0f81da2ec","qzone":"99d207b90670adceaaa416b63528d92c","tsina":"4028615622","tqq":"99d207b90670adceaaa416b63528d92c"}}
</script>
<script type="text/javascript" id="bdshare_js" data="type=tools&amp;uid=14034" ></script>
<script type="text/javascript" id="bdshell_js"></script>
<script type="text/javascript">
    if("undefined"!=typeof(temp_doc_title)) {
        bds_config.bdText = "【"+temp_doc_title+"】";
    }
	document.getElementById("bdshell_js").src = "http://bdimg.share.baidu.com/static/js/shell_v2.js?cdnversion=" + new Date().getHours();
</script>

<!-- Baidu Button END -->';
        return $str;
    }
    
  //-------------------------------------------
    // 百度分享
    //-------------------------------------------	
    public static function baiduShare($array) {
        $str = '<!-- Baidu Button BEGIN -->
<div class="info_more" id="info_more"></div><!-- Baidu Button BEGIN -->
<div class="fixed-share-bar">
	<div id="bdshare" class="bdsharebuttonbox get-codes-bdshare share-bar-btn" data-tag="fixed-share">
	    <a class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
	    <a class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
	    <a class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
	    <a class="bds_weixin" data-cmd="weixin" title="分享到微信"></a>
	    <a class="bds_more share-more" data-cmd="more"></a>
	</div>
</div>
<script type="text/javascript">
	var bdText2 = bdTsinaPic = "";
	if("undefined"!=typeof(temp_doc_title)) {
	    bdText2 = "【"+temp_doc_title+"】";
	}
    try{
		var bdTsinaPic = document.getElementById("content-first-img").src;
	}catch(e){
		var contentBox = document.getElementById("cotent_idd");
		if(contentBox){
			var contentImg = contentBox.getElementsByTagName("img");
			var bdTsinaPic = getFirstImg(contentImg);
		}
	}
    function getFirstImg(obj){
		if(typeof(obj)=="undefined" || !obj) return "";
		var src = "";
		var pattern = /img\.com\.cn/ig;
		for(var i=0; i<obj.length; i++){
			if(typeof(obj[i].src)!="undefined"){
				src = obj[i].src;
				if(pattern.test(src)){
					return src;
				}
			}
		}
		return "";		
	}
	if (!window.wbUid) { wbUid = "1747383115";}  
    window._bd_share_config={
        "common":{
        		"bdSnsKey":{"douban":"038da04052b48ba40aa8e0c0f81da2ec","qzone":"99d207b90670adceaaa416b63528d92c","tsina":"4028615622","tqq":"99d207b90670adceaaa416b63528d92c"},
        		"bdText":bdText2,
        		"bdMini":"2",
        		"bdMiniList":false,
        		"bdStyle":"0",
        		"bdSize":"16", 
        		"bdUrl":temp_doc_root_url,
        		"bdSign":"off",
        		"onBeforeClick":function(cmd,config){if(cmd=="tsina"){ return {bdText: bdText2+\'（分享自 @ZOL中关村在线）\', bdPic:bdTsinaPic}}}},
        "share": [{
			"tag" : "fixed-share",
			"bdSize" : 16
		},{
			"tag" : "bottom-share",
			"bdSize" : 32
		}]
    };
   with(document)0[(getElementsByTagName(\'head\')[0]||body).appendChild(createElement(\'script\')).src=\'http://bdimg.share.baidu.com/static/api/js/share.js?v=89343201.js?cdnversion=\'+~(-new Date()/36e5)];
</script>

<!-- Baidu Button END -->';
        return $str;
    }

    //-------------------------------------------
    // 微博关注
    //-------------------------------------------	
    public static function weiboFocus($array) {
        $str = '<div class="weibo-follow">
	<iframe id="weiboFollowButton" width="125" height="24" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling="no" border="0" .src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&width=125&height=24&uid=1747383115&style=2&btn=red&dpc=1"></iframe>
</div>';
        return $str;
    }

    //-------------------------------------------
    // 微博关注
    //-------------------------------------------	
    public static function weiboFocus_new($array) {
        global $DB_Document_Read;
        if (275 == $classId) {
            $array['url'] = 'http://www.zol.com.cn/help/iphone.html';
        }
        $url = 'http://' . $array['url'] . '/';
        $sql = 'select z_weibo from z_channel_class where z_url="' . $url . '"';
        //	echo $sql;

        $res = $DB_Document_Read->get_results($sql);
        //var_dump($res);
        if ($res) {
            $url = $res[0]['z_weibo'];
            //echo $url;
            preg_match('#http://.*weibo[^/]*/u/(\d{10})#iU', $url, $match);
            //var_dump($match);
            $uid = $match[1];
        }
        if ($array['className']) {
            if ($uid) {
                $array['className'] = str_replace('频道', '', $array['className']);
                $array['className'] = '中关村在线' . $array['className'] . '频道';
            } else {
                $uid = '1747383115';
                $array['className'] = '中关村在线';
                $url = 'http://e.weibo.com/cbsizol';
            }
            $str = '<div class="weibo-follow2">
	<iframe id="weiboFollowButton" width="64" height="24" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling="no" border="0" .src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&width=40&height=24&uid=' . $uid . '&style=1&btn=red&dpc=1"></iframe>
</div>';

            $str.= '<a href="' . $url . '" class="channel-link">' . $array['className'] . '</a>';
        }
        if (96 != $classId) {
            $str = '<!--#if expr="$class_id=96" --><div class="weibo-follow2">
        <iframe id="weiboFollowButton" width="64" height="24" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling
="no" border="0" .src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&width=40&height=24&uid=24499850
95&style=1&btn=red&dpc=1"></iframe>
</div><a href="http://e.weibo.com/u/2449985095" class="channel-link">中关村在线全国行情频道</a><!--#else -->' . $str . '<!--#endif -->';
        }
        return $str;
    }
    //-------------------------------------------
    // 微博关注
    //-------------------------------------------
    public static function weiboFocus2014($array) {
    	global $DB_Document_Read;
    	if (275 == $classId) {
    		$array['url'] = 'http://www.zol.com.cn/help/iphone.html';
    	}
    	$url = 'http://' . $array['url'] . '/';
    	$sql = 'select z_weibo from z_channel_class where z_url="' . $url . '"';
    	//	echo $sql;
    
    	$res = $DB_Document_Read->get_results($sql);
    	//var_dump($res);
    	if ($res) {
    		$url = $res[0]['z_weibo'];
    		//echo $url;
    		preg_match('#http://.*weibo[^/]*/u/(\d{10})#iU', $url, $match);
    		//var_dump($match);
    		$uid = $match[1];
    	}
    	if ($array['className']) {
    		if ($uid) {
    			$array['className'] = str_replace('频道', '', $array['className']);
    			$array['className'] = '中关村在线' . $array['className'] . '频道';
    		} else {
    			$uid = '1747383115';
    			$array['className'] = '中关村在线';
    			$url = 'http://e.weibo.com/cbsizol';
    		}
    		$str = '<div class="weibo-follow">
	<iframe id="weiboFollowButton" width="64" height="24" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling="no" border="0" .src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&width=40&height=24&uid=' . $uid . '&style=1&btn=red&dpc=1"></iframe>
</div>';
    	}
    	if (96 != $classId) {
    		$str = '<!--#if expr="$class_id=96" --><div class="weibo-follow">
        <iframe id="weiboFollowButton" width="64" height="24" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling
="no" border="0" .src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&width=40&height=24&uid=24499850
95&style=1&btn=red&dpc=1"></iframe>
</div><!--#else -->' . $str . '<!--#endif -->';
    	}
    	return $str;
    }
    //-------------------------------------------
    // 超级本
    //-------------------------------------------	
    public static function ultraBook($array) {
        $str = '<div class="ultrabook"><a href="http://ultrabook.zol.com.cn/" class="returnmod">返回超极本首页</a><a href="http://www.intel.com/cn/ultrabook/index.htm?dfaid=1&crtvid=%ecid%21">英特尔超极本</a></div>';
        return $str;
    }

    //-------------------------------------------
    // 频道百科
    //-------------------------------------------	
    public static function baike($array) {
        global $DB_Document_Read;
        $str = '';
        $sql = 'select z_title,z_docid,i.class_id from z_document_id_title t left join doc_index i on t.z_docid = i.document_id where t.z_flag = 0 and i.class_id = ' . $array['classId'];
        $rows = $DB_Document_Read->get_results($sql);
        $icnt = count($rows);
        if ($rows && $icnt > 4) {
            shuffle($rows);
            $str .= '<div class="r_bd mt10 pb10">
							        <div class="tit_5 tit_6">' . str_replace('频道', '', $array['className']) . '百科</div>
							        <ul class="rank_ul2 rank_dot">';
            $i = 1;
            foreach ($rows as $row) {
                $title = str_replace('-中关村在线', '', $row['z_title']);
                $url = get_document_url($row['z_docid'], 1, 0, $row['class_id']);
                $class = ($i == $icnt || 10 == $i) ? ' class="lastli"' : '';
                $str .= '<li' . $class . '><a href="' . $url . '" title="' . htmlspecialchars($title) . '">' . cnsubstr($title, 40) . '</a></li>';
                if (10 == $i) {
                    break;
                }
                $i++;
            }
            $str .= '</ul></div>';
        }
        return $str;
    }

    //文章导航--milj添加产品库微动态
    public static function commentbox($array) {
        $str = '<div class="commentbox" style="display:none">
	<a href="#commentsiframe" target="_self" title="网友评论" class="comment-num" id="comment-num"></a><a target="_self" class="like" title="猜您喜欢"></a><a target="_self" class="backTop" href="#zhead" title="返回顶部">返回顶部</a>
</div>';
        return $str;
    }

    //文章推荐 已经废弃
    public static function mylike($array) {
    	return '';
        $str = '<div class="may-like" style="display:none">
	<!-- 针对ie6 添加一个类名closehover  <i class="close closehover">关闭</i> -->
	<i class="close">关闭</i>
	<!-- 980页面 下面图片时 100*-75 1110px宽页面 下面的图片宽度是120*90 -->
	<ul class="like-list clearfix" id="mylike_list">
	</ul></div>';
        return $str;
    }

    //评论后按钮
    public static function buttonComment($array) {
        $str = '<div class="btns" id="button_comment"></div>';
        return $str;
    }
    //-------------------------------------------
    // 标题下分享
    //-------------------------------------------
	public static function titBottShare2014($array){
		$str = '<a href="javascript:void(0);" target="_self" class="comment-num" id="comment-num-tit">暂无评论</a>                    
				<div class="sharebtnbox">
					<span class="share-label" id="titleShareArea"><i class="share-ico"></i>分享到<i class="arrow"></i></span>
					<div class="bdsharebuttonbox bdshare_t bds_tools get-codes-bdshare box-shadow" id="bdshare" data-tag="arti-tit">
						<a href="javascript:void(0);" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博">新浪微博</a>
						<a href="javascript:void(0);" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间">QQ空间</a>
						<a href="javascript:void(0);" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博">腾讯微博</a>
						<a href="javascript:void(0);" class="bds_renren" data-cmd="renren" title="分享到人人网">人人网</a>
						<a href="javascript:void(0);" class="bds_sqq" data-cmd="sqq" title="分享给QQ好友">QQ好友</a>
						<a href="javascript:void(0);" class="bds_weixin"  data-cmd="weixin"  target="_self" title="微信二维码">微信二维码</a>
					</div>
				</div>';
		return $str;
	}
	//-------------------------------------------
	// 分页下分享    文章分页下信息分享区块(2014)  
	//-------------------------------------------	
	public static function pageBottShare2014($array){
		
		# 企业文章页的特殊处理 by suhy 20150923
		$enterpriseClass = include '/www/article/html/admin/publish/include/doc_enterprise_temp.config.php';
		if(in_array($array['classId'],$enterpriseClass)){
			$str = '<div class="article-operation clearfix">
			<div class="sharebtnbox2">
				<span class="share-label">分享到：</span>
					<div class="bdsharebuttonbox bdshare_t bds_tools get-codes-bdshare" id="bdshare" data-tag="arti-bottom">
						<a href="javascript:void(0);" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
						<a href="javascript:void(0);" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
						<a href="javascript:void(0);" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
						<a href="javascript:void(0);" class="bds_renren" data-cmd="renren" title="分享到人人网"></a>
						<a href="javascript:void(0);" class="bds_sqq" data-cmd="sqq" title="分享给QQ好友"></a>
						<a href="javascript:void(0);" class="bds_weixin"  data-cmd="weixin" target="_self"  title="微信二维码"></a>
					</div>
				</div>
			</div>
			<span style="display:none" id="realCommentNum"></span>
		
			<script src="http://service.zol.com.cn/user/js/login2014/md5.js"></script>';
			
			return $str;
		}
		# 文章页测试 20150929 suhy
		if(in_array($array['classId'],array(364,132))){
			$str = '
				<div class="article-operation clearfix">
		         	<div class="article-qrcode" style="display:none;">
		         		<img  width="80" height="80" alt=""><p>觉得这篇文章不错<br>快分享到朋友圈吧</p>
		         	</div>
		         	<div class="article-share-good">
		         		<a href="#" onclick="return false;" target="_self" class="cai" hidefocus="true"><span>不喜欢(<em id="article_dislike_hits">0</em>)</span></a>
						<a href="#" onclick="return false;" target="_self" class="zan" hidefocus="true"><span>点个赞(<em id="article_like_hits">0</em>)</span></a>
						<div class="sharebtnbox2">
							<span class="share-label">分享到：</span>
							<div class="bdsharebuttonbox bdshare_t bds_tools get-codes-bdshare" id="bdshare" data-tag="arti-bottom">
								<a href="javascript:void(0);" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
								<a href="javascript:void(0);" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
								<a href="javascript:void(0);" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
								<a href="javascript:void(0);" class="bds_renren" data-cmd="renren" title="分享到人人网"></a>
								<a href="javascript:void(0);" class="bds_sqq" data-cmd="sqq" title="分享给QQ好友"></a>
								<a href="javascript:void(0);" class="bds_weixin"  data-cmd="weixin" target="_self"  title="微信二维码"></a>
							</div>
						</div>
					</div>
				</div>
				<script src="http://service.zol.com.cn/user/js/login2014/md5.js"></script>';
			return $str;
		}
		
		
		
		$str = '<div class="article-operation clearfix">
			<div class="sharebtnbox2">
			<span class="share-label">分享到：</span>
			<div class="bdsharebuttonbox bdshare_t bds_tools get-codes-bdshare" id="bdshare" data-tag="arti-bottom">
			<a href="javascript:void(0);" class="bds_tsina" data-cmd="tsina" title="分享到新浪微博"></a>
			<a href="javascript:void(0);" class="bds_qzone" data-cmd="qzone" title="分享到QQ空间"></a>
			<a href="javascript:void(0);" class="bds_tqq" data-cmd="tqq" title="分享到腾讯微博"></a>
			<a href="javascript:void(0);" class="bds_renren" data-cmd="renren" title="分享到人人网"></a>
			<a href="javascript:void(0);" class="bds_sqq" data-cmd="sqq" title="分享给QQ好友"></a>
			<a href="javascript:void(0);" class="bds_weixin"  data-cmd="weixin" target="_self"  title="微信二维码"></a>
			</div>
			</div>
			<a href="#" onclick="return false;" target="_self" class="cai" hidefocus="true"><span>不喜欢(<em id="article_dislike_hits">0</em>)</span></a>
			<a href="#" onclick="return false;" target="_self" class="zan" hidefocus="true"><span>点个赞(<em id="article_like_hits">0</em>)</span></a>
			</div><span style="display:none" id="realCommentNum"></span>
		
		
		<script src="http://service.zol.com.cn/user/js/login2014/md5.js"></script>';
	
		
		return $str;
	}
	//-------------------------------------------
	// 右侧客户端和关注
	//-------------------------------------------
	public static function pageAsideSns2014($array){
		$weiboStr = self::weiboFocus2014($array);
		//$str .=self::getArticleBk($array);
		//$str .=self::getArticleComputex($array);
		$str .= '<div class="module weibox clearfix">'.$weiboStr.'		
		<div class="wb-icobox wb-android">
		<a href="http://www.zol.com.cn/help/index.html" class="android" title="中关村在线Android 客户端">Android 客户端</a>
		<span class="illustration-tips"><i class="trangle"></i>下载中关村在线Android 客户端</span>
		</div>
		<div class="wb-icobox wb-apple">
		<a href="http://www.zol.com.cn/help/index.html" class="apple" title="中关村在线iPhone 客户端">iPhone 客户端</a>
		<span class="illustration-tips"><i class="trangle"></i>下载中关村在线 iPhone 客户端</span>
		</div>
		<div class="wb-icobox wb-win8">
		<a href="http://www.zol.com.cn/help/index.html" class="win8" title="下载中关村在线Windows8客户端">iPhone 客户端</a>
		<span class="illustration-tips"><i class="trangle"></i>下载中关村在线Windows8客户端</span>
		</div>
		<div class="wb-icobox wb-weixin">
		<a href="http://www.zol.com.cn/help/index.html" class="weixin-ico" title="下载中关村在线Windows8客户端">iPhone 客户端</a>
		<div class="weixin-top box-shadow">
			<i class="trangele"></i>
			<i class="close">关闭</i>
			<a href="http://www.zol.com.cn/" class="weixin-pic"><img src="http://icon.zol-img.com.cn/cms/images/weixin.jpg" alt="" width="85" height="85"></a>
			<p>扫一扫</p>
			<p>成为中关村在线微信好友</p>
		</div>
		</div>
		</div>';
		return $str;
	}
	//文章页15周年模块
    public static function getArticleEcma(){
        $ecmaStr = '<div class="module">';
        #15周年大标题
        $paramArr = array(
            'moduleids' => "20898",
            'orderby' => "order by date desc",
            'limit' => "limit 0,1",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) $ecmaStr .= '<div class="ecma-activity-head"><h2><a href="'.$rows[0]['url'].'">'.$rows[0]['title'].'</a></h2></div>';
        #15周年图片部分
        $ecmaStr .= '<div class="ecma-activity-main clearfix">';
        #大图
        $paramArr = array(
            'getImageFlag' => 1,
            'moduleids' => "20893",
            'orderby' => "order by date desc",
            'limit' => "limit 0,1",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) $ecmaStr .= '<div class="fl"><a href="'.$rows[0]['url'].'"><img src="'.$rows[0]['pic_src'].'" width="200" height="150"></a></div>';
        #小图
        $paramArr = array(
            'getImageFlag' => 1,
            'moduleids' => "20894",
            'orderby' => "order by date desc",
            'limit' => "limit 0,3",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) {
            $ecmaStr .= '<div class="fr">';
            foreach ($rows as $row) {
                $ecmaStr .= '<a href="'.$row['url'].'"><img src="'.$row['pic_src'].'" width="96" height="48"></a>';
            }
            $ecmaStr .= '</div>';
        }
        $ecmaStr .= '</div>';
        #文字链
        $paramArr = array(
            'moduleids' => "20895",
            'orderby' => "order by date desc",
            'limit' => "limit 0,2",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) {
            $ecmaStr .= '<ul class="ecma-activity-list">';
            foreach ($rows as $row) {
                $ecmaStr .= '<li><a href="'.$row['other'].'" class="class">'.$row['digest'].'</a><a href="'.$row['url'].'">'.$row['title'].'</a></li>';
            }
            $ecmaStr .= '</ul>';
        }
        #更多文案
        $paramArr = array(
            'moduleids' => "20897",
            'orderby' => "order by date desc",
            'limit' => "limit 0,1",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) $ecmaStr .= '<div class="ecma-activity-tit">'.$rows[0]['title'].'</div>';
        #更多下图片
        $paramArr = array(
            'getImageFlag' => 1,
            'moduleids' => "20896",
            'orderby' => "order by date desc",
            'limit' => "limit 0,3",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) {
            $ecmaStr .= '<div class="ecma-activity-pics clearfix">';
            foreach ($rows as $row) {
                $ecmaStr .= '<a href="'.$row['url'].'"><img src="'.$row['pic_src'].'" width="96" height="48"></a>';
            }
            $ecmaStr .= '</div>';
        }
        $ecmaStr .= '</div>';
        return $ecmaStr;
    }
    
	//文章页Z爆款模块
    public static function getArticleBk(){
        $bkStr = '<div class="module">';
        #Z爆款图片部分
        $bkStr .= '<div class="ecma-baokuan-main">';
        #爆款大图
        $paramArr = array(
            'getImageFlag' => 1,
            'moduleids' => "20890",
            'orderby' => "order by date desc",
            'limit' => "limit 0,1",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) $bkStr .= '<a href="'.$rows[0]['url'].'"><img src="'.$rows[0]['pic_src'].'" width="300" height="150"></a>';
        #小图
        $paramArr = array(
            'getImageFlag' => 1,
            'moduleids' => "20891",
            'orderby' => "order by date desc",
            'limit' => "limit 0,2",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) {
            $bkStr .= '<div class="clearfix">';
            $icnt = 1;
            foreach ($rows as $row) {
                $class = ($icnt==1) ? 'fl' : 'fr';
                $bkStr .= '<a href="'.$row['url'].'" class="'.$class.'"><img src="'.$row['pic_src'].'" width="148" height="100"></a>';
                $icnt++;
            }
            $bkStr .= '</div>';
        }
        $bkStr .= '</div>';
        #文字链
        $paramArr = array(
            'moduleids' => "20892",
            'orderby' => "order by date desc",
            'limit' => "limit 0,1",
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if ($rows) $bkStr .= '<div class="ecma-baokuan-text clearfix"><span>'.$rows[0]['title'].'</span><div class="scroll-box"><ul><li><a href="'.$rows[0]['other'].'">'.$rows[0]['digest'].'</a></li></ul></div><a href="http://bk.zol.com.cn/" class="more">更多商品&gt;&gt;</a></div>';
        $bkStr .= '</div>';
        return $bkStr;
    }
    
    
    //文章页Computex
    public static function getArticleComputex(){
    	
    	$comStr ='<style>
.computex-head { height:28px; /* padding:10px 0 0; */ overflow:hidden;}
.computex-head a, .computex-head img{ display:block;}
.computex-list { border-bottom:0 none;}

.computex-body { padding: 0 0 5px; zoom:1;}
.computex-body-top { padding:10px 0 0; zoom:1;}
.computex-body-top .pic { float:left; width:200px; height:125px; overflow:hidden;}
.computex-body-top .pic img { display:block;}
.computex-body-top .computex-quickMark { float:right; width:90px;}
.computex-body-top .computex-quickMark img { display:block; width:90px; height:90px; overflow:hidden;}
.computex-body-top .computex-quickMark span { display:block; padding:2px 0 0; text-align:center; color:#666; line-height:18px;}

.computex-pro-title { margin:10px 0 0; border-top:1px dotted #ccc;}
.computex-pro-title h3 { position:relative; width:100px; height:24px; margin:0 auto -6px; top:-12px; background-color:#F9F9F9; text-align:center; font-size:14px;}
.computex-pro-list li { float:left; width:90px; overflow:hidden; margin-left:15px; _display:inline;}
.computex-pro-list .first { margin-left:0;}
.computex-pro-list a { color:#333;}
.computex-pro-list img { display:block;}
.computex-pro-list span { display:block; height:22px; line-height:22px; overflow:hidden;}
.computex-pro-list a:hover,.computex-pro-list a:hover span { color:#c00;}
.computex-body-top .pic img { display:block;}
.computex-body-top .pic span { position:relative; display:block; width:190px; height:26px; margin:-26px 0 0; padding:0 5px;background:rgba(0,0,0,0.5); filter:progid:DXImageTransform.Microsoft.gradient(GradientType=1,startColorstr=#80000000,endColorstr=#80000000); color:#fff; text-align:center; line-height:26px; cursor:pointer;}    						
.computex-body-top .pic:hover,.computex-body-top .pic:hover span { color:#fff;}
    			</style>';
    	
    	$comStr .= '<div class="module">';
    	#Computex大标题
    	$paramArr = array(
	    	'moduleids' => "20904",
	    	'orderby' => "order by date desc",
	    	'limit' => "limit 0,1",
    	);
    	$rows = PageHelper::getModuleArt($paramArr);
    	if ($rows){
    		$comStr .='<div class="computex-head"><a href="'.$rows[0]['url'].'"><img src="'.$rows[0]['pic_src'].'" width="300" height="28" alt="'.$rows[0]['title'].'"></a></div>';
    	} 
    	
    	$comStr .='<div class="computex-body">';
    	#Computext图片
    	$comStr .='<div class="computex-body-top clearfix">';
    	#大图
    	$paramArr = array(
    			'moduleids' => "20905",
    			'orderby' => "order by date desc",
    			'limit' => "limit 0,1",
    	);
    	$rows = PageHelper::getModuleArt($paramArr);
    	if($rows){
    		$comStr .= '<a class="pic" href="'.$rows[0]['url'].'" title="'.$rows[0]['title'].'"><img width="200" height="125" src="'.$rows[0]['pic_src'].'" alt="'.$rows[0]['title'].'"><span>'.$rows[0]['title'].'</span></a>';
    	}
    	#二维码
    	$paramArr = array(
    			'moduleids' => "20906",
    			'orderby' => "order by date desc",
    			'limit' => "limit 0,1",
    	);
    	$rows = PageHelper::getModuleArt($paramArr);
    	if($rows){
    		$comStr .= '<span class="computex-quickMark">
							<img width="90" height="90" src="'.$rows[0]['pic_src'].'" alt="'.$rows[0]['title'].'">
							<span>'.$rows[0]['title'].'</span>
						</span>';
    	}
    	$comStr .= '</div>'; 
    	
    	#主题
    	$paramArr = array(
	    	'moduleids' => "20907",
	    	'orderby' => "order by date desc",
	    	'limit' => "limit 0,4",
    	);
    	$rows = PageHelper::getModuleArt($paramArr);
    	if($rows){
    		$comStr .='<ul class="ecma-activity-list computex-list">';
    		foreach ($rows as $row){
    			$comStr .='<li><a href="'.$row['url'].'" class="class">'.$row['title'].'</a><a href="'.$row['other'].'" >'.$row['digest'].'</a></li>';
    		}
    		$comStr .='</ul>';
    	}
    	
    	#新品集中营
    	$comStr .= '<div class="computex-pro"><div class="computex-pro-title"><h3>新品集中营</h3></div>';
    	$paramArr = array(
	    	'moduleids' => "20908",
	    	'orderby' 	=> "order by date desc",
	    	'limit' 	=> "limit 0,3",
    	);
    	$rows = PageHelper::getModuleArt($paramArr);
    	
    	if($rows){
    		$comStr .= '<ul class="computex-pro-list clearfix">';
    		foreach ($rows as $key=>$row){
    			$classFirst = $key==0 ? ' class="first"' : '';
    			$comStr .= '<li'.$classFirst.'>
								<a href="'.$row['url'].'" title="'.$row['title'].'">
									<img width="90" height="55" src="'.$row['pic_src'].'" alt="'.$row['title'].'">
									<span>'.$row['title'].'</span>
								</a>
							</li>';
    		}
    		$comStr .='</ul></div>';
    		
    	}
    	$comStr .= '</div></div>';
    	
    	return $comStr;
    }
    
    
    
	//返回顶部
	public static function goTopbox2014($array) {
		$str = '<div id="pubFeedBack">
	<a href="javascript:;" onclick="return false" id="toComment"><span id="gotop-comnum"></span></a>
	<a href="javascript:;" id="backTop"><i>返回顶部</i></a>
	<a href="javascript:;" id="callSurvey">建议反馈</a>
</div>';
		return $str;
	}
	//长微博
	public static function changWeibo2014($array) {
		$str = '<!-- 公用弹框 --><div class="popbox box-shadow" style="display:none;">
    <i class="pop-close">关闭</i>
    <div class="pop-head">生成长微博<span>(右键另存为下载到本地)</span></div>
    <p class="create-intro">本篇文章已转换为一张图片，您可以分享到新浪微博和腾讯微博</p>
    <div class="createbox"></div>
    <div class="operation"><a href="" class="tqq-btn">腾讯微博</a><a href="" class="sina-btn">新浪微博</a></div>
</div>';
		$str = '';
		return $str;
	}
	//侧边固定导航机快速评论
	public static function sideBtn2014($array) {
		global $temp_doc_kind_arr;
		$classId = $array['classId'];
		$kindid  = 0;
		if(isset($temp_doc_kind_arr[$classId])){
			$kindid = $temp_doc_kind_arr[$classId];
		}
		$str = '';
		$str = '<div class="sideFixed-box">
    <ul class="sideFixed-nav">
        <li class="collect-item" title="收藏"><i class="collect-icon">收藏</i></li>
        <li class="discuss-item" title="快速评论"><i class="discuss-icon">快速评论</i><span class="illustration-tips" style="display:none;"><i class="trangle"></i>快速评论 ^_^</span></li>
        <li class="links-item" title="今日热读"><i class="links-icon">今日热读</i></li>
    </ul>
    <div class="collect-tip" style="display: none;"><span>收藏成功</span></div>
    <div class="sideDiscuss-box">
        <div class="sideDiscuss-header clearfix"><span class="close-link">关闭</span></div>
        <div class="sideDiscuss-content">
            <div class="sideDiscuss-textarea">
                <textarea name="content" id="com-content" class="textarea" onfocus="init_check_img();"  placeholder="我来说两句"></textarea>
            </div>
            <div class="sideDiscuss-toolBar clearfix">
                <span class="submit-button comment-sub-btn" onclick="post(); return false;">发表评论</span>
                <div class="captcha-box">
                    <script>
                    var pp= Math.round((Math.random()) * 100000000);
                    document.write(\'<span id="check_box" style="display:none;"> 验证码：<input type="text" name="door" id="door" class="text_2" tabindex="4"> <img id="check_img" alt="点击图片更换" class="hand"  onclick="con_code();" > <input type=hidden name="imgcode" id="imgcode" value="\' + pp + \'"><a class="hand" onclick="con_code();">换个图片</a></span>\');
                    </script>
                </div>
                <div class="expression-box">
                    <span class="icon expression-trigger" onclick="APP_Emotion.showFace(this,\'com-content\'); return false;">表情</span>
                    <div class="promptbox_comm add_face_box" id="face_main_box" style="display: none;">
					  <div class="prompt">
					    <div class="prompt-head">
					      <h3>添加表情</h3>
					      <a href="javascript:void(0);" target="_self" class="prompt-close" onclick="APP_Emotion.closeFace();return false;" style="cursor:pointer">关闭</a> </div>
					    <div class="add_face" id="add_face" style="height: 216px;">
					      <ul class="face_show" id="show_face_area">
					      </ul>
					    </div>
					  </div>
					</div>                    
                </div>
                
                
            </div>
        </div>
    </div>
</div>
<a class="globalPage-next" target="_self" title="下一页"><i class="icon">下一页</i></a>';
		$str .='<script type="text/javascript">var kindid= '.$kindid.';</script>';
		return $str;
		/*
				<div class="quickReply-box">
                    <span class="icon quickReply-trigger">快速回复</span>
                    <div class="fast-replylist" style="display: none;">
                        <ul class="replylist-items">
                            <li onclick="post(1,1);">你好像说了很多，又好像什么也没说~</li>
                            <li onclick="post(1,2);">简直是业界良心啊，给你点32个赞！</li>
                            <li onclick="post(1,3);">前来围观评论，坐等神回复！</li>
                            <li onclick="post(1,4);">纯属路过打个酱油，各位继续~</li> 
                        </ul>
                    </div>
                </div>
		 * */
	}
	
	//侧边固定导航机快速评论      底部公用（201405）
	public static function sideBtn201405($array) {
		global $temp_doc_kind_arr;
		$classId = $array['classId'];
		$kindid  = 0;
		if(isset($temp_doc_kind_arr[$classId])){
			$kindid = $temp_doc_kind_arr[$classId];
		}
		
		$str = '';
		$str = '
<div class="jd-lyout" id="zol_jd_layout" style="display:none;"><a href="javascript:;" target="_self" class="close-btn">关闭</a><a href="javascript:;" target="_self" class="comment-btn">去评论</a></div>';
		$str .='<script type="text/javascript">var kindid= '.$kindid.';</script>';
		if($classId == 364111){
			$str .= '<!--S 侧边栏 -->
<div class="z-article-side-box">
  <div class="z-article-toolbar" id="toolbarTop">
    <ul class="toolbar-list toolbar-items">
      <li class="toolbar-user"> <span class="caption caption-user"><img width="30" height="30" src="http://icon.zol-img.com.cn/article/201408/default-pic-small.png" alt="" />用户</span>
        <div class="toolbar-content" style="display:none;">
          <div class="toolbar-my-avatar-header">会员登录，体验专属服务</div>
          <div class="toolbar-my-avatar"> <a class="pic" title="" href="javascript:void(0);" target="_self"> <img width="74" height="74" alt="" src="http://icon.zol-img.com.cn/article/201408/default-pic-big.png"></a>
            <h3><a title="pumpe" href="javascript:viod(0);" target="_self" onclick="com_login()" >Hi,您好~</a></h3>
            <p class="no-login-tip">立即登录，签到赢金豆~</p>
          </div>
          <div class="toolbar-loginbox"> <span class="toolbar-global-login-btn" onclick="com_login()">立即登录</span>
            <div class="toolbar-login-option clearfix"> <a href="http://my.zol.com.cn/index.php?c=getPassword" class="toolbar-forgot-pwd">忘记密码？</a> <a href="http://service.zol.com.cn/user/register.php" class="toolbar-register">快速注册</a> <a href="http://service.zol.com.cn/user/api/qq/libs/oauth/redirect_to_login.php?from=210" class="toolbar-qq" title="qq快速登录"></a> <a href="http://service.zol.com.cn/user/api/sina/jump.php?from=210" class="toolbar-sina" title="sina快速登录"></a> </div>
          </div>
          <div class="toolbar-recbox">
            <div class="toolbar-recbox-header">
              <h3>为您推荐</h3>
              <span>登录后，推荐会更符合您的口味~</span></div>
            <ul class="favorites-list">
            </ul>
          </div>
        </div>
      </li>
      <li class="toolbar-favorites"> <span class="caption">收藏</span>
        <div class="toolbar-content"></div>
      </li>
      <li class="toolbar-record"> <span class="caption">记录</span>
        <div class="toolbar-content">
          <div class="toolbar-header">
            <h4>我的记录</h4>
          </div>
          <div class="record-main"> <a class="toolbar-view-next" onclick="loadMoreRecord();" href="#nolink" target="_self">点击查看更多</a> </div>
        </div>
      </li>
      <li class="toolbar-notice"> <span class="caption">通知</span>
        <div class="toolbar-content" style="display: none;">
          <div class="toolbar-header">
            <h4>我的消息通知</h4>
          </div>
          <div class="record-main">
            <div class="no-login-record-nocookies">
              <p class="toolbar-remind">登录后即可查看通知，和作者互动</p>
              <span class="toolbar-global-login-btn" onclick="com_login()">立即登录</span> </div>
          </div>
        </div>
      </li>
      <li class="toolbar-share"> <span class="caption">分享</span>
        <div class="toolbar-share-model"> <i class="arrow-ico"></i>
          <div id="bdshare" class="bdsharebuttonbox bdshare_t bds_tools get-codes-bdshare" data-tag="arti-tit"> <a href="#" target="_self" data-cmd="qzone" class="bds_qzone">QQ空间</a> <a href="#" target="_self" data-cmd="tsina" class="bds_tsina">新浪微博</a> <a href="#" target="_self" data-cmd="tqq" class="bds_tqq">腾讯微博</a> <a href="#" target="_self" data-cmd="weixin" class="bds_weixin">微信</a> <a href="#" target="_self" data-cmd="renren" class="bds_renren">人人</a> </div>
        </div>
      </li>
      <li class="toolbar-comment"> <span class="caption" data-shown="0">评论</span>
        <div class="toolbar-content" style="display: none;">
        <div class="toolbar-header">
          <h4>我的评论</h4>
        </div>
        <div class="reply-main">
          <div class="hd clearfix">
            <div class="num" id="commNumWrap"><a href="#" target="_self">查看全部<em id="toolbarComNum">0</em>条评论</a></div>
            <div class="user" id="commUserWrap"><a href="http://service.zol.com.cn/user/login.php" target="_self" data-role="user-login">登录</a><em>|</em><a href="http://service.zol.com.cn/user/register.php">注册</a><em style="display:inline-block;">|</em><a href="http://service.zol.com.cn/user/api/qq/libs/oauth/redirect_to_login.php" class="qq" style="display:inline-block;">腾讯</a><a href="http://service.zol.com.cn/user/api/sina/jump.php" class="sina" style="display:inline-block;">新浪</a></div>
          </div>
          <div class="text-box textareabox"><span class="commenttip" id="comtip">有话不说，憋着多难受啊。</span>
            <textarea cols="30" rows="10" name="content"  id="com-content" class="textarea" onfocus="init_check_img();init_tip(\'\',1);" onblur="init_tip();"></textarea>
          </div>
          <div class="btn-box clearfix">
            <div class="btn-code clearfix" id="check_box" style="display:none;"> <span>验证码：</span>
              <input type="text" name="door" id="door" tabindex="4">
              <img id="check_img" alt="点击图片更换" title="点击图片更换" class="hand"  width="48" height="20" onclick="con_code();" > </div>
            <a href="javascript:;" class="btn-sent comment-sub-btn" onclick="post(); return false;">发表评论</a> </div>
          <div class="sub-title">
            <h4>最新评论</h4>
          </div>
          <div id="toolbar-comment-list">
            <ul class="reply-list">
            </ul>
          </div>
        </div>
      </li>
      <!-- //end评论 -->
    </ul>
  </div>
  
  <!--S 下半部分-->
  <ul class="toolbar-other toolbar-items" id="toolBarDown">
    <li class="toolbar-top"><span class="caption">顶部</span></li>
    <li class="toolbar-proposal" id="toolBarSurvey"><a href="http://service.zol.com.cn/complain/"><span class="caption">建议</span></a></li>
    <li class="toolbar-packup"><span class="caption toolbar-unfold" id="toolbarSwitch">收起</span></li>
  </ul>
  <!--E 下半部分--> 
  <a style="display:none" href="http://www.zol.com.cn/topic/4956017.html" class="toolbar-draw-pop"><i class="close"></i></a>
</div>
<!--E 侧边栏 -->
<div class="zj-fix-layout" id="zj-fix-layout" style="display:none;">
	<span class="zj-consult-close">关闭</span>   
    <span class="zj-consult-link" id="backHeadBtn"><i></i>专家咨询</span>
</div>';
		return $str;
		}
		//去掉侧边栏 by suhy  
			$str .= '
			<div class="z-article-side-box">
				<ul class="toolbar-other toolbar-items" id="toolBarDown" style="">
					<li class="toolbar-home">
						<a href="/">
							<span class="icon home-icon">频道首页</span>
							<span class="text">频道<br />首页</span>
							<i class="marsk"></i> 
						</a>
					</li>
	
					<li>
						<a href="#commentsiframe" target="_self">
							<span class="icon comment-icon" >网友评论</span>
							<span class="text">网友<br />评论</span>
							<i class="marsk"></i>
						</a>
					</li>
	
					<li>
						<a href="#guessYouLike" target="_self">
							<span class="icon favorite-icon">猜你喜欢</span>
							<span class="text">猜你<br />喜欢</span>
							<i class="marsk"></i>
						</a>
					</li>
	
					<li class="toolbar-top" style="display: block;">
						<span class="icon caption goback-icon">返回顶部</span>
						<span class="text">返回<br />顶部</span>
						<i class="marsk"></i>
					</li>
				</ul>
				<a style="display:none" href="http://www.zol.com.cn/topic/4956017.html" class="toolbar-draw-pop"><i class="close"></i></a>
			</div>
			<div class="zj-fix-layout" id="zj-fix-layout" style="display:none;">
				<span class="zj-consult-close">关闭</span>   
			    <span class="zj-consult-link" id="backHeadBtn"><i></i>专家咨询</span>
			</div>';
			
			return $str;
	}
	
	/* 今日最新文章 */
    public static function guideRead2014($array){
        global $DB_Document_Read;
        $paramArr = array(
            'cid'        => $array['classId'],
            'showimg'    => 1,
            'imgwidth'   => 115,
            'imgheight'  => 75,
            'prop'       => 2,
            'orderby'    => 1,
            'len'        => 30,
            'limit'      => 'limit 0,25'
        );
        $rows = PageHelper::getArticleList($paramArr);
        $artiStr = '<ul class="intel-ranking" id="coordinate">';
        if($rows){
        	$count = count($rows);
        	$icnt  = 1;
            foreach ($rows as $row){
                $class = ($icnt > 3) ? 'n2' : 'n1';
                $artiStr .= '<li><em class="'.$class.'">'.$icnt.'</em><a href="'.$row["url"].'" title="'.$row["ftitle"].'">'.$row["ftitle"].'</a></li>';
                $icnt++;
            }
        }
        $artiStr .= '</ul>';
        return $artiStr;
    }
    
    /* 今日最新文章 2014-9 避免页面加载出现大段列表 改为输出json*/
    public static function guideRead201409($array){
    	global $DB_Document_Read;
    	$paramArr = array(
    			'cid'        => $array['classId'],
    			'showimg'    => 1,
    			'imgwidth'   => 115,
    			'imgheight'  => 75,
    			'prop'       => 2,
    			'orderby'    => 1,
    			'len'        => 30,
    			'limit'      => 'limit 0,25'
    	);
    	$rows = PageHelper::getArticleList($paramArr);
    	$artiStr = '';
    	$jsonArr = array();
    	if($rows){
    		$count = count($rows);
    		$icnt  = 1;
    		foreach ($rows as $row){
    			$class = ($icnt > 3) ? 'n2' : 'n1';
    			$jsonArr[] = array(
    				'url'	   	=>$row['url'],
    				'title'		=>iconv('gbk','utf-8//ignore', $row["ftitle"]),	
    			);
    			$icnt++;
    		}
    	}
    	
    	
    	$artiStr .= '<script type="text/javascript">var guideReadData='.json_encode($jsonArr).'</script>';
    	$artiStr .= '<ul class="intel-ranking" id="coordinate">';
    	$artiStr .= '</ul>';
    	
    	return $artiStr;
    }
    //获取新文章页相关文章（包括LOGO 面包屑 搜索框）      左侧底部区域1(2014)
    public static function getRelDoc2014($array){
    	global $baiduRecommendConf;
    	$classId  = $array['classId'];
    	$classUrl = 'http://'.str_replace('http://','',$array['url']);
    	$className = $array['className'];
    	$className = str_replace('频道', '', $className).'频道';
    	if($classId==96){
    		$className = '全国行情';
    		$classUrl = 'http://price.zol.com.cn/';
    	}else{
    		$classUrl = '/';
    	}
    	if(275==$classId) {
    		$classUrl = 'http://www.zol.com.cn/help/iphone.html';
    	}
    	# 企业文章页的特殊处理 by suhy 20150923
    	//$enterpriseClass = array(129,227,202,301,220,385,386,387,388,229,364,);
    	$enterpriseClass = include '/www/article/html/admin/publish/include/doc_enterprise_temp.config.php';
    	if(in_array($array['classId'],$enterpriseClass)){
    		//var_dump($enterpriseClass); 
    		return '';
    	}
		//判断是否存在推荐方案
		if(array_key_exists($classId,$baiduRecommendConf)){
			$tabCur = '';
			$tabVis = 'none';
			$baiduRecoId = $baiduRecommendConf[$classId];
			$baiduTit = '<li class="current" rel="tab_new_1">今日热读</li>';
			$baiduCon = '<div id="tab_new_1" class="news-about-cont" style="display:block;">';
			$baiduCon .= '<script>document.write(unescape(\'%3Cdiv id="hm_t_'.$baiduRecoId.'"%3E%3C/div%3E%3Cscript charset="utf-8" src="http://crs.baidu.com/t.js?siteId=ae5edc2bc4fc71370807f6187f0a2dd0&planId='.$baiduRecoId.'&async=0&referer=\') + encodeURIComponent(document.referrer) + \'&title=\' + encodeURIComponent(document.title) + \'&rnd=\' + (+new Date) + unescape(\'"%3E%3C/script%3E\'));</script> ';
			$baiduCon .= '</div>';
			
			//后加的
			$tabCur = ' class="current" ';
			$tabVis = 'block';
			$baiduCon = '';
			$baiduTit = '';
		}else{
			$tabCur = ' class="current" ';
			$tabVis = 'block';
			$baiduTit = '';
			$baiduCon = '';
		}  
		$titleStr = '本周必读';
			
    	$str = '<div class="news-about clearfix"><a href="'.$classUrl.'" class="back-main" id="backChannelLink">返回'.$className.'</a><a href="/list.html" id="todayNewLink" style="display:none;" class="back-main">今日最新</a>
       			<a name="guessYouLike" class="page-anchor"></a>
				<ul class="rank-tab switch clearfix">
					'.$baiduTit.'
					<li '.$tabCur.' rel="tab_new_2">猜你喜欢</li>
					<li rel="tab_new_3">'.$titleStr.'</li>
				</ul>'.$baiduCon.'
				<div id="tab_new_2" class="news-about-cont" style="display:'.$tabVis.';"></div>
				<div id="tab_new_3" class="news-about-cont" style="display:none;"></div>
			</div>';
    	
    	return $str;
    }    

    //获取新文章页头部      文章头部2014
    public static function getPubHead2014($array){
    	global $DB_Document_Read;
    	//频道分类
    	$classArr = array(74,210,145,300,182,206,353,200,194,207,231);
    	$resultArr = array();
    	//读取各分类下带图的4篇文章
    	foreach ($classArr as $classId){
    		$paramArr = array(
    				'cid'       => $classId,
    				'showimg'   => 1,
    				'imgwidth'  => 100,             #导读图宽度
    				'imgheight' => 75,              #导读图高度
    				'orderby'   => 1,
    				'len'       => 40,
    				'limit'     => 'limit 0, 4'
    		);
    		$rows = PageHelper::getArticleList($paramArr);
    		if($rows){
    			$resultArr[] = $rows;
    		}else{
    			$resultArr[] = array();
    		}
    	}
    	ob_start();
    	include_once '/www/article/html/template/new/template_c/260.tpl.php';
    	$con = ob_get_contents();
    	ob_end_clean();
    	# 如果是企业站频道，则使用企业站的定制模板 by suhy 20150921
//     	if(in_array($array['classId'],array(364,))){
//     		$con = '
// 	    		<script>
// 					var __publicNavWidth=1000;
// 				</script>
// 				<script src="http://icon.zol-img.com.cn/public/js/enterprise-global-sitenav.js"  type="text/javascript"></script>
// 	    		';
//     	}
    	
    	return $con;
    }
    
    //-------------------------------------------
    // 右侧推荐阅读
    //-------------------------------------------
    public static function pageAsideReco2014($array){
    	$str = '<div class="module clearfix" id="aside-recommend" style="display:none;"></div>';
    	return $str;
    }

    
    //获取logo右侧厂商推广
    public static function logoManu2014($array){
    	$classId = $array['classId'];
    	$str = '';
    	if(74==$classId) {
    		$str .= '<a href="http://www.vivo.com.cn/" class="bbg_logo" title="vivo智能手机"><img src="http://icon.zol-img.com.cn/mobile/bbg/bbg_article_logo_new.png"/></a>';
    	}
    	return $str;
    }
    public function getBkRankHtml($dataArr){
        $detailUrl = 'http://detail.zol.com.cn';
        if (!$dataArr) return false;
        $outHtml = '';
        $i = 1;
        foreach ($dataArr as $key => $value) {
            if ($i == 7) break;
            $outHtml .= '<li><em class="n1">'.$i.'</em><a class="pic" href="'.$detailUrl.$value['urls']['detail'].'"><img width="60" height="45" alt="'.$value['name'].'" src="'.$value['smallPic'].'"></a><a class="title" href="'.$detailUrl.$value['urls']['detail'].'">'.$value['name'].'</a><span class="price">￥'.$value['price'].'</span></li>';
            $i++;
        }
        return $outHtml;
    }
    /*爆款文章页右侧排行榜*/
    public static function getBkRankArea($array){
        $classId = $array['classId'];
        if (74 == $classId) {
            defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//是否使用ZOL新框架，true为使用
        	require_once('/www/zdata/Api.php'); //引入私有云入口文件
            #外观漂亮
            $dataArr = ZOL_Api::run("Pro.Koubei.getGoodBadProRank" , array(
                'subcateId'     => 57,       #子类ID
            	'goodBadId'     => 623095,   #语义ID
            	'goodBad'       => 1,        #1是优点 2是缺点
            ));
            $tabOneHtml = $dataArr ? self::getBkRankHtml($dataArr) : '';
            $tabOneHtml = '<ul class="rank-list" id="tab_top_1">'.$tabOneHtml.'</ul>';
            #屏幕大
            $dataArr = ZOL_Api::run("Pro.Koubei.getGoodBadProRank" , array(
                'subcateId'     => 57,       #子类ID
            	'goodBadId'     => 623089,   #语义ID
            	'goodBad'       => 1,        #1是优点 2是缺点
            ));
            $tabTwoHtml = $dataArr ? self::getBkRankHtml($dataArr) : '';
            $tabTwoHtml = '<ul class="rank-list" id="tab_top_2" style="display:none;">'.$tabTwoHtml.'</ul>';
            #运行流畅速度快
            $dataArr = ZOL_Api::run("Pro.Koubei.getGoodBadProRank" , array(
                'subcateId'     => 57,       #子类ID
            	'goodBadId'     => 623228,   #语义ID
            	'goodBad'       => 1,        #1是优点 2是缺点
            ));
            $tabThreeHtml = $dataArr ? self::getBkRankHtml($dataArr) : '';
            $tabThreeHtml = '<ul class="rank-list" id="tab_top_3" style="display:none;">'.$tabThreeHtml.'</ul>';
            $outHtml = '<ul class="rank-tab-bar switc clearfix"><li class="current" rel="tab_top_1">外观漂亮</li><li rel="tab_top_2">屏幕大</li><li rel="tab_top_3">运行流畅速度快</li></ul>';
            $outHtml = '<div class="side-module bk-rank-box"><div class="hd"><h3>手机排行榜</h3></div>'.$outHtml.$tabOneHtml.$tabTwoHtml.$tabThreeHtml.'</div>';
            return $outHtml;
        } else {
            return '';
        }
    }

    //经销商信息
    public static function getDealer2014($array){
       return '<div class="dealer-box"></div>';  
    }

    //相关问答
    public static function getRelAsk2014($array){
       return '<div class="section relatedQuestions-section" id="relatedQuestions"></div>';  
    }

    // Z神通联盟
    public static function getArticleShentong201410($array){
        
        defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//是否使用ZOL新框架，true为使用
        require_once('/www/zdata/Api.php'); //引入私有云入口文件
        $outArr =array();    
        # 获取大图
        $dataArr = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,1", 
            'moduleids' => "20989",
        ));
        $bigPart = current($dataArr); 
        // 'http://group.zol.com.cn/1/1098_33.html';
        $bookId = preg_replace('@(\S+)_(\d+).html@i','$2',$bigPart['url']);
        
        #获取帖子信息
        $bookArr = ZOL_Api::run("Bbs.Book.getBookInfo" , array(
            'bbsName'        => 'DB_Troop',      #数据库链接名
            'boardId'        => 1098,            #板块ID
            'bookId'         => $bookId,         #帖子ID
        ));
        $bigPart['title'] = $bigPart['title']?$bigPart['title']:$bookArr['baseInfo']['title'];
        $bigPart['date']  = date('m-d',strtotime($bookArr['baseInfo']['wdate']));
        $bigPart['name']  = $bookArr['baseInfo']['nickname'];
        $bigPart['myBbs'] = "http://my.zol.com.cn/mybbs/{$bookArr['baseInfo']['userid']}/"; 

        $bigPartHtml = <<<EOT
            <a href="{$bigPart['url']}" class="big-picnews">
                <img width="300" height="148" .src="{$bigPart['pic_src']}" alt="{$bigPart['title']}">
                <span>{$bigPart['title']}</span>
            </a>
            <div class="opation" style="padding: 0 10px;">
                <span>{$bigPart['date']}</span><a href="{$bigPart['myBbs']}">{$bigPart['name']}</a>
            </div>
EOT;
        #获取小图
        $dataArr = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,2", 
            'moduleids' => "20990",
        ));
        foreach ($dataArr as $key=>$value) {
            $bookId = preg_replace('@(\S+)_(\d+).html@i','$2',$value['url']);
            #获取帖子信息
            $bookArr = ZOL_Api::run("Bbs.Book.getBookInfo" , array(
                'bbsName'        => 'DB_Troop',      #数据库链接名
                'boardId'        => 1098,            #板块ID
                'bookId'         => $bookId,         #帖子ID
            ));
            $value['title'] = $value['title']?$value['title']:$bookArr['baseInfo']['title'];   
            $date  = date('m-d',strtotime($bookArr['baseInfo']['wdate']));
            $name  = $bookArr['baseInfo']['nickname'];
            $myBbs = "http://my.zol.com.cn/mybbs/{$bookArr['baseInfo']['userid']}/";   
            $smallPartHtml .= <<<EOT
            <li>
                <a href="{$value['url']}" class="pic">
                <img width="100" height="75" .src="{$value['pic_src']}" alt="{$value['title']}"></a>
                <div class="pic-title"><a href="{$value['url']}">{$value['title']}</a></div>
                <div class="opation"><span>{$date}</span><a href="{$myBbs}">{$name}</a></div>
            </li>            
EOT;
        }

        #拼接html
        $outHtml = <<<EOD
        <div class="module">
            <div class="media-alliance">
                <a href="http://group.zol.com.cn/subcate_list_1098.html" class="hd">Z神通自媒体联盟</a>
                {$bigPartHtml}
                <ul class="pic-list">
                    {$smallPartHtml}
                </ul>
                <a href="http://group.zol.com.cn/subcate_list_1098.html" class="foot">Z神通联盟 · 期待你的加入！The Zoler · Join us now!</a>
            </div>
        </div>
EOD;
        return $outHtml;        
    }

    // ChinaJoy
    public static function getArticleChinaJoy2014($array){
        #获取大图
        $bigPic = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,1", 
            'moduleids' => "20993",
        ));
        $bigPic = current($bigPic);
        $bigPicHtml = <<<EOT
            <a href="{$bigPic['url']}" title="{$bigPic['title']}" class="pic">
                <img width="196" height="146" src="{$bigPic['pic_src']}" alt="{$bigPic['title']}">
            </a>
EOT;
        #炫酷装备 图
        $onePic = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,1", 
            'moduleids' => "20995",
        ));
        $onePic =current($onePic);   
        $oneDoc = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,3", 
            'moduleids' => "20996",
        ));
        $onePicHtml = <<<EOT
            <a href="{$onePic['url']}" title="{$onePic['title']}" class="pic">
                <img width="96" height="56" src="{$onePic['pic_src']}" alt="{$onePic['title']}">
            </a>
EOT;
        foreach ($oneDoc as $key => $value) {
            $oneDocHtml .= <<<EOT
            <li><a href="{$value['url']}" title="{$value['title']}">{$value['title']}</a></li>
EOT;
        }

        #火辣美女 图
        $twoPic = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,1", 
            'moduleids' => "20997",
        ));
        $twoPic =current($twoPic);   
        $twoDoc = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,3", 
            'moduleids' => "20998",
        ));
        $twoPicHtml = <<<EOT
            <a href="{$twoPic['url']}" title="{$twoPic['title']}" class="pic">
                <img width="96" height="56" src="{$twoPic['pic_src']}" alt="{$twoPic['title']}">
            </a>
EOT;
        foreach ($twoDoc as $key => $value) {
            $twoDocHtml .= <<<EOT
            <li><a href="{$value['url']}" title="{$value['title']}">{$value['title']}</a></li>
EOT;
        }

        #劲爆游戏 图
        $threePic = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,1", 
            'moduleids' => "20999",
        ));
        $threePic =current($threePic);   
        $threeDoc = PageHelper::getModuleArt(array(
            'limit'     => "limit 0,3", 
            'moduleids' => "21000",
        ));
        $threePicHtml = <<<EOT
            <a href="{$threePic['url']}" title="{$threePic['title']}" class="pic">
                <img width="96" height="56" src="{$threePic['pic_src']}" alt="{$threePic['title']}">
            </a>
EOT;
        foreach ($threeDoc as $key => $value) {
            $threeDocHtml .= <<<EOT
            <li><a href="{$value['url']}" title="{$value['title']}">{$value['title']}</a></li>
EOT;
        }

         #拼接html
        $outHtml = <<<EOD
        <div class="module">
            <div class="cj-bcont">
                <div class="hd">中国国际数码互动娱乐展览会</div>
                <div class="cj-first-figure clearfix">
                    {$bigPicHtml}
                    <div class="cj-code">
                        <span class="code-bg"></span>
                        <p>扫一扫</p>
                        <p>手机看游戏展</p>
                    </div>
                </div>
                <div class="cj-newsbox first">
                    <div class="cj-news-hd">
                        <h3>炫酷装备</h3>
                        <a href="http://chinajoy.zol.com.cn/" class="cj-more">more</a>
                    </div>
                    {$onePicHtml}
                    <ul class="cj-news-list">
                       {$oneDocHtml}
                    </ul>
                </div>
                <div class="cj-newsbox">
                    <div class="cj-news-hd">
                        <h3>火辣美女</h3>
                        <a href="http://chinajoy.zol.com.cn/" class="cj-more">more</a>
                    </div>
                    {$twoPicHtml}
                    <ul class="cj-news-list">
                       {$twoDocHtml}
                    </ul>
                </div>
                <div class="cj-newsbox last">
                    <div class="cj-news-hd">
                        <h3>劲爆游戏</h3>
                        <a href="http://chinajoy.zol.com.cn/" class="cj-more">more</a>
                    </div>
                    {$threePicHtml}
                    <ul class="cj-news-list">
                       {$threeDocHtml}
                    </ul>
                </div>
            </div>
        </div>
EOD;
        return $outHtml;
    }

    // 底部调查
    public static function getSurvey2014($array){    
        return '<div id="article_survey"></div>';
    }

    // 合作经销商
    public static function getCooperativeDealer2014($array){    
        return '<div class="pro-dealer" id="cooperative_dealer"></div>';
    } 

    //文章右侧经销商信息
    public static function getDealerRight2014($array){
       return '<div id="show_dealer_section"></div>';  
    }   

    //获取新文章页头部
    public static function getChTopNav2014($array){
    	ob_start();
    	include '/www/article/html/template/new/template_c/302.tpl.php';
    	$con = ob_get_contents(); 
    	ob_end_flush(); 
 		return $con;
    }
    
    //侧边栏额外补充的区块
    public static function getSidebarExtra($array){
        return '<div class="aside-extra" style="display: none;"></div>';
    }

    // Z神通联盟    右侧区块1(2014)
    public static function getArticleShentong2014($array){
        
        defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//是否使用ZOL新框架，true为使用
        require_once('/www/zdata/Api.php'); //引入私有云入口文件
        $outArr =array();    
        # 获取大图
        	$dataArr = PageHelper::getModuleArt(array(
        			'limit'     => "limit 0,2",
        			'moduleids' => "20989",
        	));
        	$dataBBS = '';
        	if($array['classId']){
        		switch ($array['classId']) {
        			case 74:
        				$bbsid =5;
        				break;
        			case 210:
        				$bbsid =3;
        				break;
        			case 300:
        				$bbsid =4;
        				break;
        			case 165:
        				$bbsid =2;
        				break;
        			default:
        				$bbsid =5;
        		}	
        	}else{    
        		$bbsid = 1;
        	}
        	$dataBBS = ZOL_Api::run("Bbsv2.List.getBbsBoardRankings",array(
        			'bbsid' => $bbsid, //论坛id 1摄影论坛  2硬件论坛 3笔记本论坛 4平板论坛 5手机论坛
        			
        	));
        	$bigPartHtml .=<<<EOTB
	        	<div class="alliance-item" id="tab_st_1" style="display: block;">
							<div class="bbs-ranking">
								<table>
									<tbody><tr>
										<th class="cell-1">排名</th>
										<th class="cell-2">板块名</th>
										<th class="cell-3">热度</th>
									</tr>
EOTB;
	if($dataBBS){
        	$num = 1;
        	$classStr='';
        	foreach($dataBBS as $k=>$v){
        		$classStr = $v['type'] == 'up' ? 'class="uarr"' : $v['type'] == 'down' ? 'class="darr"' : '';
        		$bigPartHtml .=<<<EOTB
        		<tr>
					<td class="cell-1">{$num}</td>
					<td class="cell-2"><a href="{$v['url']}" target="_blank">{$v['name']}</td>
					<td class="cell-3"><span {$classStr}>{$v['hot']}</span></td>
				</tr>
EOTB;
        		if($num == 5)break;
        		$num++;
        	}	
     }
									
							
		$bigPartHtml .=<<<EOTB
							</tbody></table>
							</div>
						</div>
EOTB;
        $i=2;       	
        
        foreach ($dataArr as $key => $value) {
            // 'http://group.zol.com.cn/1/1098_33.html';
            $bookId = preg_replace('@(\S+)_(\d+).html@i','$2',$value['url']);
            #获取帖子信息
            $bookArr = ZOL_Api::run("Bbs.Book.getBookInfo" , array(
                'bbsName'        => 'DB_Troop',      #数据库链接名
                'boardId'        => 1098,            #板块ID 
                'bookId'         => $bookId,         #帖子ID 
            ));
            $title = $value['title']?$value['title']:$bookArr['baseInfo']['title'];
            $date  = date('m-d',strtotime($bookArr['baseInfo']['wdate']));
            $name  = $bookArr['baseInfo']['nickname'];
            $myBbs = "http://my.zol.com.cn/mybbs/{$bookArr['baseInfo']['userid']}/"; 
            $display = 'none';
            // change by suhy
            	$value['pic_src'] = str_replace('/g2','/t_s300x150/g2',$value['pic_src']);
            	$bigPartHtml .=<<<EOTB
            <div class="alliance-item" id="tab_st_{$i}" style="display:{$display};">
                <a href="{$value['url']}" title="{$title}" class="big-picnews">
                    <img width="300" height="150" .src="{$value['pic_src']}" alt="{$title}">
                    <span>{$title}</span>
                </a>
                <div class="opation" style="padding: 0 10px;">
                    <span>{$date}</span><a href="{$myBbs}">{$name}</a>
                </div>
            </div>
EOTB;
       
            $i++;
        }
        # 论坛精选
        if($array['subcatid']){
            $bbsArr = ZOL_Api::run("Bbsv2.Get.getListSubcate" , array(
                'subcateId'        => $array['subcatid'],  
                'num'              => 5,//6=>5 by suhy
            ));
        }else{
            $bbsArr1 = ZOL_Api::run("Bbsv2.Get.getListSubcate" , array(
                'subcateId'        => 57,  
                'num'              => 3
            ));
            $bbsArr2 = ZOL_Api::run("Bbsv2.Get.getListSubcate" , array(
                'subcateId'        => 702,  
                'num'              => 2,//3=>2
            ));
            $bbsArr['list'] = array_merge($bbsArr1['list'] , $bbsArr2['list']);
        }
        foreach ($bbsArr['list'] as $key => $value) {
            $bbsListHtml .= "<li><a title='{$value['title']}' href='{$value['url']}'>{$value['title']}</a></li>";
        }
        if($bbsArr['name']&&$array['subcatid']){
            $bbsName = $bbsArr['name'];
            $bbsUrl  = $bbsArr['url'];
        }else{
            $bbsName = '论坛';
            $bbsUrl  = 'http://bbs.zol.com.cn';
        }
        
        $outHtmlExtra = '';
        // 文章页右侧经销商推荐 by suhy $array['classId'] == 364
    	if(true){
             $outHtmlExtra = '
               <div class="alliance-cont article-recomment-merchant" style="display:none;">
                    <div class="alliance-title">
                        <h3 class="section-title">推荐经销商</h3>
                    </div>
                    
                    <div class="merchant-box">
                        <div class="report" id="subId" data="57" manu="171">
                            投诉欺诈商家: <a href="http://service.zol.com.cn/complain/complain.php">400-678-0068</a>
                        </div>
                        <div class="merchant-tabs clearfix">
                            <ul id="J_MerchantTab" class="tab-nav switch clearfix">
                                <li class="current" rel="city-1">北京</li>
                                <li rel="city-2">上海</li> 
                                <li id="addPre" rel="city-3">广州</li>
                            </ul>
                            <div class="city-more">
                                <span id="J_MoreCitys" class="arrow-icon"></span>
                                <div id="J_MoreCitysBox" class="layerbox city-layerbox" style="display: none;">
                                    <div class="layerbox-inner">
                                        <div class="layerbox-header clearfix">
                                            <span id="J_MoreCitysClose" class="close"></span>
                                            <span class="title">请选择省份</span>
                                        </div>
                                        <div class="layerbox-main">
                                            <ul class="win-citylist">
                                                <li data-province="3" data-procity="0">天津</li><li data-province="4" data-procity="539">重庆</li><li data-province="5" data-procity="102">哈尔滨</li><li data-province="6" data-procity="63">沈阳</li><li data-province="7" data-procity="85">长春</li><li data-province="8" data-procity="0">石家庄</li><li data-province="9" data-procity="0">呼和浩特</li><li data-province="10" data-procity="0">西安</li><li data-province="11" data-procity="33">太原</li><li data-province="12" data-procity="0">兰州</li><li data-province="14" data-procity="462">乌鲁木齐</li><li data-province="17" data-procity="386">成都</li><li data-province="18" data-procity="0">昆明</li><li data-province="19" data-procity="0">贵阳</li><li data-province="20" data-procity="0">长沙</li><li data-province="21" data-procity="291">武汉</li><li data-province="22" data-procity="0">郑州</li><li data-province="23" data-procity="0">济南</li><li data-province="23" data-procity="226">青岛</li><li data-province="23" data-procity="231">烟台</li><li data-province="24" data-procity="0">合肥</li><li data-province="25" data-procity="0">南京</li><li data-province="26" data-procity="0">杭州</li><li data-province="30" data-procity="354">东莞</li><li data-province="31" data-procity="0">南宁</li><li data-province="32" data-procity="0">南昌</li><li data-province="33" data-procity="198">福州</li><li data-province="33" data-procity="199">厦门</li><li data-province="30" data-procity="348">深圳</li><li data-province="26" data-procity="155">温州</li><li data-province="30" data-procity="357">佛山</li><li data-province="26" data-procity="154">宁波</li><li data-province="33" data-procity="202">泉州</li><li data-province="30" data-procity="352">惠州</li><li data-province="13" data-procity="459">银川</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--北京-->
                        <div id="city-1" class="merchant-content"> 
                            <ul class="merchant-list">
                             
                            </ul> 
                        </div>
                        <!--上海-->
                        <div id="city-2" class="merchant-content" style="display: none"> 
                            <ul class="merchant-list">
                                
                            </ul> 
                        </div>
                        <!--自定义-->
                        <div id="city-3" class="merchant-content" style="display: none"> 
                            <ul class="merchant-list">
                                <li><div class="merchant-inner">本城市下暂无经销商</div></li>
                            </ul> 
                        </div>
                    </div>
                </div>';
		}

        #拼接html
		// by suhy  
        $outHtml = <<<EODB
        <div class="module">
            <div class="media-alliance">
            <div class="module-header">
	            <h3>论坛排行</h3>
	        </div>
                <div class="alliance-slide">
                    {$bigPartHtml}
                    <div class="alliance-side-index switch">
                        <span rel="tab_st_1" class="current">1</span>
                        <span rel="tab_st_2">2</span>
                        <span rel="tab_st_3">3</span>
                    </div>
                </div>
                {$outHtmlExtra}
                <div class="alliance-cont">
                    <div class="alliance-title">
                        <a class="enter-bbs-link" href="{$bbsUrl}">更多</a>
                        <h3>{$bbsName}精选</h3>
                    </div>
                    <ul class="alliance-news">
                         {$bbsListHtml}
                    </ul>
                </div>
            </div>
        </div>
EODB;

		
                         
      return $outHtml;        
    }
    
    // zol 百分点换量区块       右侧区块3(2014)
    public static function getZolBfdSection($array){
        switch ((int)$array['classId']) {
            case 145:
            	return '';  // by suhy
                $sectionName = "ZOL_BFD";
                break;
            case 196: 
            	return '';  // by suhy 20150916
                $sectionName = "ZOL_XGO";
                break;  
            case 200: 
            	return '';  // by suhy
                $sectionName = "ZOL_EA3W";
                break;         
            default:
                break;
        }
        return '<div class="module" id="'.$sectionName.'"></div>';
    }
    
    /*
     * 获取下载软件排行（软件频道） wuhw 2015-02-11
     */
    public static function getSoftInfo($array){
    	defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//是否使用ZOL新框架，true为使用
    	require_once('/www/zdata/Api.php'); //引入私有云入口文件
    	
    	$weekInfo = ZOL_Api::run("XiaZai.Soft.getSoftInfo" , array(
    			'isPic'          => 1,               #需要LOGO
    			'picSize'        => '32x32',         #图片尺寸
    			'order'          => 5,               #排序方式
    			'limit'     	 => '0,10',  		 #limit条件
    	));
    	
    	$monthInfo = ZOL_Api::run("XiaZai.Soft.getSoftInfo" , array(
    			'isPic'          => 1,               #需要LOGO
    			'picSize'        => '32x32',         #图片尺寸
    			'order'          => 6,               #排序方式
    			'limit'     	 => '0,10',  		 #limit条件
    	));
    	
    	$downInfo = ZOL_Api::run("XiaZai.Soft.getSoftInfo" , array(
    			'isPic'          => 1,               #需要LOGO
    			'picSize'        => '32x32',         #图片尺寸
    			'order'          => 1,               #排序方式
    			'limit'     	 => '0,10',  		 #limit条件
    	));
    	$str = '<div class="module soft-down">
				<div class="module-header"><h3>软件下载排行</h3></div>
				<ul class="rank-tab switch clearfix">
					<li class="first current" rel="tab_soft_1">周排行</li>
					<li rel="tab_soft_2">月排行</li>
					<li rel="tab_soft_3">总排行</li>
				</ul>';
    	if($weekInfo && $weekInfo['base']){
			$str .= '<ul class="intel-ranking" id="tab_soft_1" >';
			$i = 0;
			foreach($weekInfo['base'] as $v){
				$i++;
    			$detailUrl = 'http://xiazai.zol.com.cn'.$v['detailUrl'];
    			if($i<=3){
    				$picUrl = $v['picUrl'];
    				if(!$picUrl){
                    	$picUrl = 'http://icon.zol-img.com.cn/xiazai/new/softpic.jpg';
                    }else{
                    	$isPic = getimagesize("$picUrl");
                    	if(!$isPic){
                        	$picUrl = str_replace('_32x32','',$picUrl);
                        }else if($isPic['width']<30){
                        	$picUrl = str_replace('_32x32','',$picUrl);
                        }
                        $isPic = getimagesize("{$picUrl}");
                        if(!$isPic){
                        	$picUrl = 'http://icon.zol-img.com.cn/xiazai/new/softpic.jpg';
                        }
                    }
	    			if(!$picUrl) $picUrl = 'http://icon.zol-img.com.cn/xiazai/new/softpic.jpg';
	    			$str .= '<li class="special">
							<em class="n1">'.$i.'</em>
	    					<a href="'.$detailUrl.'" class="pic">
								<img width="30" height="30" src="'.$picUrl.'" alt="'.$v['softName'].'" data-bd-imgshare-binded="1">
							</a>
	    					<a href="'.$detailUrl.'" class="title">'.$v['softName'].' '.$v['version'].'</a>
							<a href="'.$detailUrl.'" class="detail-btn">下载</a>
						</li>';
    			}else{
    				$str .= '<li><em class="n2">'.$i.'</em><a href="'.$detailUrl.'">'.$v['softName'].' '.$v['version'].'</a><a href="'.$detailUrl.'" class="detail-btn">下载</a></li>';
    			}
			}
			$str .='</ul>';
    	}
    	if($monthInfo && $monthInfo['base']){
    		$str .= '<ul class="intel-ranking" id="tab_soft_2" style="display:none">';
    		$i = 0;
    		foreach($monthInfo['base'] as $v){
    			$i++;
    			$detailUrl = 'http://xiazai.zol.com.cn'.$v['detailUrl'];
    			if($i<=3){
    				$picUrl = $v['picUrl'];
    				if(!$picUrl){
                    	$picUrl = 'http://icon.zol-img.com.cn/xiazai/new/softpic.jpg';
                    }else{
                    	$isPic = getimagesize("$picUrl");
                    	if(!$isPic){
                        	$picUrl = str_replace('_32x32','',$picUrl);
                        }else if($isPic['width']<30){
                        	$picUrl = str_replace('_32x32','',$picUrl);
                        }
                        $isPic = getimagesize("{$picUrl}");
                        if(!$isPic){
                        	$picUrl = 'http://icon.zol-img.com.cn/xiazai/new/softpic.jpg';
                        }
                    }
	    			$str .= '<li class="special">
							<em class="n1">'.$i.'</em>
	    					<a href="'.$detailUrl.'" class="pic">
								<img width="30" height="30" src="'.$picUrl.'" alt="'.$v['softName'].'" data-bd-imgshare-binded="1">
							</a>
	    					<a href="'.$detailUrl.'" class="title">'.$v['softName'].' '.$v['version'].'</a>
							<a href="'.$detailUrl.'" class="detail-btn">下载</a>
						</li>';
    			}else{
    				$str .= '<li><em class="n2">'.$i.'</em><a href="'.$detailUrl.'">'.$v['softName'].' '.$v['version'].'</a><a href="'.$detailUrl.'" class="detail-btn">下载</a></li>';
    			}
    		}
    		$str .='</ul>';
    	}
    	if($downInfo && $downInfo['base']){
    		$str .= '<ul class="intel-ranking" id="tab_soft_3" style="display:none">';
    		$i = 0;
    		foreach($downInfo['base'] as $v){
    			$i++;
    			$detailUrl = 'http://xiazai.zol.com.cn'.$v['detailUrl'];
    			if($i<=3){
    				$picUrl = $v['picUrl'];
    				if(!$picUrl){
                    	$picUrl = 'http://icon.zol-img.com.cn/xiazai/new/softpic.jpg';
                    }else{
                    	$isPic = getimagesize("$picUrl");
                    	if(!$isPic){
                        	$picUrl = str_replace('_32x32','',$picUrl);
                        }else if($isPic['width']<30){
                        	$picUrl = str_replace('_32x32','',$picUrl);
                        }
                        $isPic = getimagesize("{$picUrl}");
                        if(!$isPic){
                        	$picUrl = 'http://icon.zol-img.com.cn/xiazai/new/softpic.jpg';
                        }
                    }
	    			$str .= '<li class="special">
							<em class="n1">'.$i.'</em>
	    					<a href="'.$detailUrl.'" class="pic">
								<img width="30" height="30" src="'.$picUrl.'" alt="'.$v['softName'].'" data-bd-imgshare-binded="1">
							</a>
	    					<a href="'.$detailUrl.'" class="title">'.$v['softName'].' '.$v['version'].'</a>
							<a href="'.$detailUrl.'" class="detail-btn">下载</a>
						</li>';
    			}else{
    				$str .= '<li><em class="n2">'.$i.'</em><a href="'.$detailUrl.'">'.$v['softName'].' '.$v['version'].'</a><a href="'.$detailUrl.'" class="detail-btn">下载</a></li>';
    			}
    		}
    		$str .='</ul>';
    	}
    	$str .='</div>';
    	return $str;
    	
    }
    
    /*
     * 获取abab开服表信息（软件频道） wuhw 2015-02-11
     * edit 2015-07-15 改为与114游戏合作广告位 游戏频道_软件右侧区块1
     */
    public static function getAbabkf($array){
    	return '<div class="module" style=" padding-top: 0px;"><a href="http://xiaoyouxi.114la.com/zt/dongman/?zgc" target="_blank" rel="nofollow"><img src="http://icon.zol-img.com.cn/soft/114ad_20151028.jpg" /></a></div>';
    	#return '<div class="module" style=" padding-top: 0px;"><iframe height="333" frameborder="0" src="http://kf.abab.com/index.php?c=Corp_Iframe&a=Soft" marginwidth="0" marginheight="0" scrolling="no" class="ababIframe"></iframe></div>';
    }
    
    /**
     * 文章页右侧生成软件装机必备(软件频道) wuhw add 2015-02-25
     * */
    public static function getSoftZhuangji($array){
    	$padStr = '';
    	$mobStr = '';
    	$pcStr  = '';
    	$paramArr=array(
                'moduleids'=>17093,
                'limit'=>'limit 0,12',
                'orderby'=>' and digest="平板软件" order by date desc',
                'getimageflag'=>'1',
                'len'=>'10',
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if($rows){
            foreach ($rows as $row){
                $padStr.= '<li><a href="'.$row['url'].'" '.$row['title_tmp'].'><img src="'.$row['pic_src'].'" width="32" height="32" alt="'.$row['ftitle'].'"><span>'.$row['title'].'</span></a></li>';
            }
        }
        $paramArr=array(
                'moduleids'=>17093,
                'limit'=>'limit 0,12',
                'orderby'=>' and digest="手机软件" order by date desc',
                'getimageflag'=>'1',
                'len'=>'10',
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if($rows){
            foreach ($rows as $row){
                $mobStr.= '<li><a href="'.$row['url'].'" '.$row['title_tmp'].'><img src="'.$row['pic_src'].'" width="32" height="32" alt="'.$row['ftitle'].'"><span>'.$row['title'].'</span></a></li>';
            }
        }
        $paramArr=array(
                'moduleids'=>17093,
                'limit'=>'limit 0,12',
                'orderby'=>' and digest="PC软件" order by date desc',
                'getimageflag'=>'1',
                'len'=>'10',
        );
        $rows = PageHelper::getModuleArt($paramArr);
        if($rows){
            foreach ($rows as $row){
                $pcStr.= '<li><a href="'.$row['url'].'" '.$row['title_tmp'].'><img src="'.$row['pic_src'].'" width="32" height="32" alt="'.$row['ftitle'].'"><span>'.$row['title'].'</span></a></li>';
            }
        }
        $str = '
        		<div class="module mobile-app">
					<div class="module-header"><h3>装机必备</h3></div>
					<ul class="rank-tab switch clearfix">
						<li class="first current" rel="tab_diy_1">PC软件</li>
						<li rel="tab_diy_2">手机软件</li>
						<li rel="tab_diy_3">平板软件</li>
					</ul>
					<ul id="tab_diy_1" class="app-list clearfix">
						'.$pcStr.'
					</ul>
	        		<ul id="tab_diy_2" class="app-list clearfix" style="display:none">
						'.$mobStr.'
					</ul>
        			<ul id="tab_diy_3" class="app-list clearfix" style="display:none">
						'.$padStr.'
					</ul>
					<div class="my-diy-soft"><a href="http://xiazai.zol.com.cn/zj/">晒一晒我的装机软件<span></span></a></div>
				</div>';
        		
        if($array['classId']=='228' || $array['classId']=='372'){
        	$str .= '<div class="weibo-follow">
                <iframe id="weiboFollowButton" width="125" height="24" frameborder="0" allowtransparency="true" marginwidth="0" marginheight="0" scrolling="no" border="0" .src="http://widget.weibo.com/relationship/followbutton.php?language=zh_cn&width=125&height=24&uid=1747383115&style=2&btn=red&dpc=1"></iframe>
            </div>';
        }else{
        	$str .= '<div class="module">
                <div class="module-header"><h3>新浪微博</h3></div>
                <div class="weibo-follow" style="display:none"></div>
                <iframe width="298" height="550" class="share_self" frameborder="0" scrolling="no"
                src="http://widget.weibo.com/weiboshow/index.php?language=&width=280&height=550&fansRow=2&ptype=1&speed=0&skin=-1&isTitle=0&noborder=0&isWeibo=1&isFans=0&uid=1718473917&verifier=8ef712fb&colors=d9e4f3,ffffff,666666,00009c,ecfbfd&dpc=1"></iframe>
            </div>';
        }
		return $str;
	}
	
	/**
	 * 文章页壁纸精选(软件频道) wuhw add 2015-02-25
	 * */
	public static function getDeskInfo($array){
		defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//是否使用ZOL新框架，true为使用
		require_once('/www/zdata/Api.php'); //引入私有云入口文件
		
		$start = rand(0,40);
		#美女壁纸
		$meinv = ZOL_Api::run("Desk.DeskList.getList" , array(
			'pageType'       => 1,               #设备ID
			'cate'           => 3,               #大类ID
			'picSize'        => '120x90',        #壁纸尺寸
			'isDown'         => 1,               #按下载量排序
			'limit'          => $start.',10',    #壁纸个数
		));
		
		#明星壁纸
		$mingxing = ZOL_Api::run("Desk.DeskList.getList" , array(
			'pageType'       => 1,               #设备ID
			'cate'           => 4,               #大类ID
			'picSize'        => '120x90',        #壁纸尺寸
			'isDown'         => 1,               #按下载量排序
			'limit'          => $start.',10',    #壁纸个数
		));
		
		#风景壁纸
		$fengjing = ZOL_Api::run("Desk.DeskList.getList" , array(
			'pageType'       => 1,               #设备ID
			'cate'           => 6,               #大类ID
			'picSize'        => '120x90',        #壁纸尺寸
			'isDown'         => 1,               #按下载量排序
			'limit'          => $start.',10',    #壁纸个数
		));
		
		#动漫壁纸
		$dongman = ZOL_Api::run("Desk.DeskList.getList" , array(
			'pageType'       => 1,               #设备ID
			'cate'           => 14,               #大类ID
			'picSize'        => '120x90',        #壁纸尺寸
			'isDown'         => 1,               #按下载量排序
			'limit'          => $start.',10',    #壁纸个数
		));
		
		#手机壁纸
		$sjBizhi = ZOL_Api::run("Mobile.MobileBiZhiNew.getGroupInfo" , array(
			'g_order'        => 2,               #排序方式
			'z_jqtype'       => 2,               #组图类型
			'g_recommend'    => 1,               #编辑推荐
			'z_showlink'     => 1,               #获取组图链接
			'p_src'          => '120x90',        #图片尺寸
			'z_limit'        => $start.',10',    #壁纸个数
		));
		
		$str = '<div class="module">
				    <div class="module-header"><h3>精美壁纸推荐</h3></div>
				    <ul class="rank-tab switch clearfix">
				        <li class="first current" rel="tab_list_1">美女</li>
				        <li rel="tab_list_2">明星</li>
				        <li rel="tab_list_3">风景</li>
				        <li rel="tab_list_4">动漫</li>
				        <li rel="tab_list_5">手机</li>
				    </ul>
				    <div id="tab_list_1" class="game-rec">';
						if($meinv['list']){
		$str .=       '<ul class="game-rec-list clearfix">';
				        foreach($meinv['list'] as $k=>$v){
				        	if($k>3) break;
		$str .=            '<li>
				                <a href="'.$v['detailUrl'].'">
				                    <img width="120" height="90" alt="" src="'.$v['fileName'].'">
				                    <span>'.$v['name'].'</span>
				                </a>
				            </li>';
				        }
		$str .=        '</ul>
				        <ul class="news-list">';
						foreach($meinv['list'] as $k=>$v){
							if($k<=3) continue;
		$str .=            '<li>
				                <a title="'.$v['name'].'" href="'.$v['detailUrl'].'">'.$v['name'].'</a>
				            </li>';
						}
		$str .=	        '</ul>';
						}
		$str .=     '</div>
				    <div id="tab_list_2" class="game-rec" style="display:none">';
				        if($mingxing['list']){
		$str .=       '<ul class="game-rec-list clearfix">';
				        foreach($mingxing['list'] as $k=>$v){
				        	if($k>3) break;
		$str .=            '<li>
				                <a href="'.$v['detailUrl'].'">
				                    <img width="120" height="90" alt="" src="'.$v['fileName'].'">
				                    <span>'.$v['name'].'</span>
				                </a>
				            </li>';
				        }
		$str .=        '</ul>
				        <ul class="news-list">';
						foreach($mingxing['list'] as $k=>$v){
							if($k<=3) continue;
		$str .=            '<li>
				                <a title="'.$v['name'].'" href="'.$v['detailUrl'].'">'.$v['name'].'</a>
				            </li>';
						}
		$str .=	        '</ul>';
						}
		$str .=		 '</div>
				    <div id="tab_list_3" class="game-rec" style="display:none">';
				        if($fengjing['list']){
		$str .=       '<ul class="game-rec-list clearfix">';
				        foreach($fengjing['list'] as $k=>$v){
				        	if($k>3) break;
		$str .=            '<li>
				                <a href="'.$v['detailUrl'].'">
				                    <img width="120" height="90" alt="" src="'.$v['fileName'].'">
				                    <span>'.$v['name'].'</span>
				                </a>
				            </li>';
				        }
		$str .=        '</ul>
				        <ul class="news-list">';
						foreach($fengjing['list'] as $k=>$v){
							if($k<=3) continue;
		$str .=            '<li>
				                <a title="'.$v['name'].'" href="'.$v['detailUrl'].'">'.$v['name'].'</a>
				            </li>';
						}
		$str .=	        '</ul>';
						}
		$str .=		 '</div>
				    <div id="tab_list_4" class="game-rec" style="display:none">';
				        if($dongman['list']){
		$str .=       '<ul class="game-rec-list clearfix">';
				        foreach($dongman['list'] as $k=>$v){
				        	if($k>3) break;
		$str .=            '<li>
				                <a href="'.$v['detailUrl'].'">
				                    <img width="120" height="90" alt="" src="'.$v['fileName'].'">
				                    <span>'.$v['name'].'</span>
				                </a>
				            </li>';
				        }
		$str .=        '</ul>
				        <ul class="news-list">';
						foreach($dongman['list'] as $k=>$v){
							if($k<=3) continue;
		$str .=            '<li>
				                <a title="'.$v['name'].'" href="'.$v['detailUrl'].'">'.$v['name'].'</a>
				            </li>';
						}
		$str .=	        '</ul>';
						}
		$str .=		 '</div>
					    <div id="tab_list_5" class="game-rec" style="display:none">';
							if($sjBizhi){
		$str .=		        '<ul class="game-rec-list clearfix">';
								foreach($sjBizhi as $k=>$v){
									if($k>3) break;
		$str .=		            '<li>
					                <a href="http://sj.zol.com.cn'.$v['z_grouplink'].'">
					                    <img width="120" height="90" alt="'.$v['g_name'].'" src="'.$v['p_src'].'" >
					                    <span>'.$v['g_name'].'</span>
					                </a>
					            </li>';
								}
		$str .=		        '</ul>
					        <ul class="news-list">';
								foreach($sjBizhi as $k=>$v){
									if($k<=3) continue;
		$str .=		            '<li>
					                <a title="'.$v['g_name'].'" href="http://sj.zol.com.cn'.$v['z_grouplink'].'">'.$v['g_name'].'</a>
					            </li>';
								}
		$str .=		        '</ul>';
							}
		$str .=		    '</div>
					</div>
					<!--end r_bd-->';
		
		if($array['classId']=='228'){
			$str .= "<div class='module'><script>write_ad('doc_new_bottom');</script></div>";
		}
		return $str;
	}
	
	/**
	 * 频道公用底部 
	 */
	public static function channelPubFoot2015($array) {
		$str = '<script>var temp_channe_id ='.(int)$array['classId'].'; </script>';
		return $str;
	}
	
	/**
	 * 百分点
	 */
	public static function Bfd2015($array) {
		$str = '';
	    //$str = '<div id="baifendian" class="module"></div>';
	    return $str;
	}
	/**
	 * zol商城iframe
	 */
	public static function zolMall2015($array) {
		$str = '<div class="module zol-mall-area"></div>';
		return $str;
	}
	
	/**
	 * vivo活动
	 */
	public static function vivo2015($array) {
	    $str = '<style>
                    .vivo-ad-2015-link1, .vivo-btn, .vivo-t, .vivo-info, .vivo-btn2, .vivo-ad-2015-q, .vivo-ad-2015-a span { font-family: "Microsoft Yahei","\5FAE\8F6F\96C5\9ED1"; }
                    #vivo_ad_2015_entrance { cursor: pointer; position: fixed; left: 0; bottom: 60px; width: 130px; height: 200px; background: url(http://icon.zol-img.com.cn/cms/vivo_active/vivo_ad_2015_entrance.png) no-repeat; }
                    #vivo_ad_2015_main { display: none; z-index: 100003; position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); filter: progid:DXImageTransform.Microsoft.gradient(startcolorstr=#99000000,endcolorstr=#99000000,gradientType=1); }
                    #vivo_ad_2015_inner { position: fixed; left: 50%; top: 50%; margin: -220px 0 0 -400px; width: 800px; height: 440px; background: #fed200 url(http://icon.zol-img.com.cn/cms/vivo_active/vivo_ad_2015_main.png) no-repeat; }
                    .vivo-ad-2015-link1 { position: absolute; right: 60px; top: 25px; font-size: 16px; color: #524505; }
                    .vivo-ad-2015-link2 { z-index: 100004; position: absolute; right: 40px; bottom: 38px; width: 238px; height: 188px; }
                    .vivo-ad-2015-close { position: absolute; right: 0; top: 0; width: 32px; height: 32px; cursor: pointer; }
                    .vivo-ad-2015-title { position: absolute; left: 57px; top: 0; width: 680px; height: 120px; text-indent: -999em; }
                    .vivo-ad-2015-section { position: absolute; left: 60px; top: 140px; }
                    .vivo-ad-2015-qa {  }
                    .vivo-ad-2015-error { display: none; }
                    .vivo-ad-2015-right { display: none; }
                    .vivo-ad-2015-sucess { display: none; }
                    .vivo-btn { border: 0; cursor: pointer; margin: 30px 0 0 40px; width: 140px; height: 45px; text-align: center; line-height: 45px; background: #3a7d9e; font-size: 24px; color: #fff; }
                    .vivo-t { height: 60px; line-height: 60px; font-size: 36px; color: #3d738d; }
                    .vivo-info { font-size: 18px; color: #3d738d; }
                    .vivo-btn2 { margin-top: 60px; display: inline-block; height: 45px; line-height: 45px; padding: 0 20px; font-size: 24px; color: #fff; background: #3a7d9e; }
                    .vivo-btn2:hover { color: #fff; text-decoration: none; }
                    .vivo-ad-2015-q { height: 50px; line-height: 50px; font-size: 22px; color: #494947; }
                    .vivo-ad-2015-a { margin: 10px 0 0 40px; }
                    .vivo-ad-2015-a input { margin-right: 10px; }
                    .vivo-ad-2015-a span { height: 38px; line-height: 38px; font-size: 18px; color: #494947; }
                    .vivo-ad-2015-right .vivo-t { font-size: 22px; color: #494947; }
                    .vivo-ad-2015-right .vivo-t span { font-size: 36px; color: #3d738d; }
                    .vivo-ad-2015-right form{ margin-top: 20px; }
                    .vivo-ad-2015-right li{ height: 32px; font-size: 18px; color: #494947; }
                    .vivo-ad-2015-right li span{ margin-top: 6px; display: inline-block; height: 32px; line-height: 32px; }
                    .vivo-ad-2015-right input{ background: none; border: 0; border-bottom: 1px dotted #494947; width: 330px; height: 31px; line-height: 40px; vertical-align: top; }
                    .vivo-ad-2015-right .vivo-btn { margin: 30px 0 0 110px }
	        </style>
	        <div id="vivo_ad_2015_entrance" class="active" style="display: none"></div>
                <div id="vivo_ad_2015_main">
                    <div id="vivo_ad_2015_box">
                        <div id="vivo_ad_2015_inner">
                            <div class="vivo-ad-2015-close"></div>
                            <div class="vivo-ad-2015-title">vivo 遇见小黄人 答题送大礼</div>
                            <a class="vivo-ad-2015-link1" href="http://mobile.zol.com.cn/537/5373403.html" title="">活动详情></a>
                            <a class="vivo-ad-2015-link2" href="http://detail.zol.com.cn/cell_phone/index399281.shtml" title=""></a>
                            <div class="vivo-ad-2015-section vivo-ad-2015-qa">
                                
                            </div>
                            <div class="vivo-ad-2015-section vivo-ad-2015-error">
                                <div class="vivo-t">对不起，您没有答对！</div>
                                <p class="vivo-info"><a href="http://detail.zol.com.cn/cell_phone/index399281.shtml">来看看正确答案吧！</a></p>
                                <a class="vivo-btn2" href="http://detail.zol.com.cn/cell_phone/index399281.shtml" title="">了解 vivo X5Pro</a>
                            </div>
                            <div class="vivo-ad-2015-section vivo-ad-2015-right">
                                
                            </div>
                            <div class="vivo-ad-2015-section vivo-ad-2015-sucess">
                                <div class="vivo-t">提交成功！</div>
                                <p class="vivo-info">跟着小黄人一起了解vivo最美手机X5Pro吧！</p>
                                <a class="vivo-btn2" href="http://detail.zol.com.cn/cell_phone/index399281.shtml" title="">了解 vivo X5Pro</a>
                            </div>
                        </div>
                    </div>
                </div>';
	    return $str;
	}
	/**
	 * 盼达网活动
	 */
	public static function panda2015($array) {
	    if(!in_array( (int)$array['classId'], array(74,367,145,364)) ) return '';
	    $html = '
	        <style>#luo-ad{position:fixed;width:600px;height:300px;overflow:hidden;margin:-150px 0 0 -300px;top:50%;left:50%;background:url(http://icon.zol-img.com.cn/cms/panda_active/lht-ad.gif);z-index:999}#luo-ad #luo-ad-close{position:absolute;width:30px;height:30px;top:23px;right:185px;color:rgba(0,0,0,0);cursor:pointer;overflow:hidden}#luo-ad .luo-ad-btn{position:absolute;width:200px;height:200px;top:95px;left:195px;color:rgba(0,0,0,0);text-align:center;display:block}#luo-ad .luo-ad-btn:hover{color:rgba(0,0,0,0)}#luo-small{position:fixed;left:40px;bottom:20px;z-index:999;display:none}#luo-small-btn {position: absolute;top: 0; right: -10px; width: 17px; height: 17px;background: url(http://icon.zol-img.com.cn/comments/140828/comment-bg.png) -86px -379px no-repeat; _background: url(http://icon.zol-img.com.cn/comments/140828/comment-bg-ie6.png) -86px -379px no-repeat; font: 0/0 arial; cursor: pointer;}</style>
	        <div id="luo-ad" style="display: none">
                	<div id="luo-ad-close"></div>
                	<a href="http://dcdv.zol.com.cn/topic/5430810.html" class="luo-ad-btn"></a>
                </div>
                <div id="luo-small"  style="display: none"><div id="luo-small-btn"></div><a href="http://dcdv.zol.com.cn/topic/5430810.html"><img src="http://icon.zol-img.com.cn/cms/panda_active/lht-small.gif"></a></div>';
	    $array = array(
	        74  => array(925,926,927,102,103,104,109,1010,1011,1016,1017,1018),
	        367 => array(925,928,929,105,106,1012,1013,1019,1020),
	        145 => array(925,930,101,107,108,1014,1015,1021,1022),
	        364 => array(925,930,101,107,108,1014,1015,1021,1022),
	    );
	    $json = json_encode($array);
	    $str = $html."<script>var pandaConf = {$json};</script>";
	    return $str;
	}
	/**
	 * 企业站文章页-顶部导航栏下-广告（左）+文字链（右）
	 */
	public static function enterpriseUnderTopNav($array){
		$str = '
		<div class="ad-topwrap">
			<div class="clearfix">
				<div class="ad-top"> 
					<div class="ad-box" id="top_tl_ad" rel="coordinate">
					      <script>write_ad("new_article_tonglan_ad");</script>
					</div>
				</div>
		        '.self::guideRead201409($array).'
			</div>
		</div>';
		
		return $str;
	}
	/**
	 * 企业站文章页-右侧区块-微信公众号   
	 */
	public static function enterpriseWeChat($array){
		$str = '
			<div class="client-entrance aside-moudle">
				<a href="http://www.zol.com.cn/help/index.html" class="client-con">
					<img src="http://icon.zol-img.com.cn/article/enterprise/enterprise_code.png" alt="" title="" width="70" height="70">
					<span>关注企业中心微信公众号<br/>微信号：ZOL企业</span>
				</a>
			</div>';
		return $str;
	}
	/**
	 * 企业站文章页-右侧区块-读了又读
	 */
	public static function enterpriseReadAndReadMore($array){
		$str = '
			<div class="aside-section">
				<div class="aside-head"><a href="javascript:;" class="change-btn" target="_self">换一换</a><h3>读过此文的人还读过</h3></div>
				<div class="other-article-list">
					<ul class="other-article">
						
					</ul>
				</div>
			</div>';

		$str = '<div class="module showFocusLook clearfix" id="showFocusLookBottom" style="display: none;">
					<div id="look-and-look"> </div>
					<div id="ad-box-under-look"> </div>
				</div>';
		return $str;
	}
	/**
	 * 企业站文章页-右侧区块-周产品排行
	 */
	public static function enterpriseWeekProductRank($array){
		$str = '<!--#include virtual="/include/channel_page_week_rank_area.html" -->';
		
		return $str;
	}
	//企业站文章页-侧边固定导航      底部公用（201405）
	public static function enterpriseSideBtn201509($array) {
		$str = '';
		$enterpriseClassArr = require '/www/article/html/admin/publish/include/doc_enterprise_temp.config.php';
		if(in_array($array['classId'],$enterpriseClassArr)){
			$str .= '	<a href="javascript:void(0)" target="_self" class="gotop"><i class="icon"></i></a>
						<a href="#content-about" target="_self">相关内容</a>
						<a href="#commentsiframe" target="_self">网友评论</a>
						<a href="http://biz.zol.com.cn">返回首页</a>';
		}
		$kindid  = 0;
		if(isset($temp_doc_kind_arr[$classId])){
			$kindid = $temp_doc_kind_arr[$classId];
		}
		$str .='<script type="text/javascript">var kindid= '.$kindid.';</script>';
		
		return $str;
		
		$str = '<a class="consultative-top" href="http://www.smb.zol.com.cn">
					<i class="icon"></i>专家咨询
				</a>
				<span class="close"></span>';
	}
	//企业站文章页-左侧的【专家咨询】      底部公用（201405）
	public static function enterpriseLeftSideBtn201509($array) {
		$str = '';
		$enterpriseClassArr = require '/www/article/html/admin/publish/include/doc_enterprise_temp.config.php';
		if(in_array($array['classId'],$enterpriseClassArr)){
			$str = '<a class="consultative-top" href="javascript:void(0);" id="backHeadBtn" bined="1" >
						<i class="icon"></i>专家咨询
					</a>
					<span class="close" ></span>';
		}
		return $str;
	}
	
	
}

?>