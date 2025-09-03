DROP TABLE IF EXISTS `met_admin_column`;
CREATE TABLE `met_admin_column` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) DEFAULT '',
    `url` varchar(255) DEFAULT '',
    `bigclass` int(11) DEFAULT '0',
    `field` int(11) DEFAULT '0',
    `type` int(11) DEFAULT '0' COMMENT '0:系统功能 ，1:左侧菜单，2:顶部菜单',
    `list_order` int(11) DEFAULT '0',
    `icon` varchar(255) DEFAULT '',
    `info` text,
    `display` int(11) DEFAULT '1' COMMENT '0:不显示 1：显示',
    `menu_lang` varchar(255) DEFAULT NULL COMMENT '菜单名称语言变量、',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_admin_has_permissions`;
CREATE TABLE `met_admin_has_permissions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `role_id` int(11) DEFAULT NULL COMMENT '角色id',
    `uid` int(11) DEFAULT NULL COMMENT '成员id',
    `aid` varchar(50) NOT NULL COMMENT '权限关联id或权限',
    `type` varchar(50) DEFAULT NULL COMMENT '权限类型  s:系统功能模块, f:系统功能 m:系统菜单 c:栏目 a:应用 l:语言',
    `access` varchar(255) DEFAULT NULL COMMENT '操作权限  0:无权限 1:查看 2:操作',
    `info` text COMMENT '说明',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_admin_roles`;
CREATE TABLE `met_admin_roles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(255) DEFAULT NULL,
    `name` varchar(255) DEFAULT NULL,
    `sort` int(11) DEFAULT NULL,
    `info` text,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_admin_menus`;
CREATE TABLE `met_admin_menus` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `pid` int(11) DEFAULT '0' COMMENT 'pid',
    `name` varchar(100) DEFAULT '' COMMENT 'name',
    `url` varchar(255) DEFAULT '' COMMENT 'url',
    `aid` int(11) DEFAULT '0' COMMENT '权限id',
    `sid` int(11) DEFAULT '0' COMMENT '关联模块权限',
    `type` int(11) DEFAULT '0' COMMENT '1:左侧菜单，2:顶部菜单，3:可视化菜单',
    `sort` int(11) DEFAULT '0' COMMENT '排序',
    `icon` varchar(255) DEFAULT '' COMMENT '图标',
    `info` text COMMENT '描述信息',
    `display` int(11) DEFAULT '1' COMMENT '0:不显示 1：显示',
    `menu_lang` varchar(255) DEFAULT NULL COMMENT '菜单名称语言变量、',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_admin_permissions`;
CREATE TABLE `met_admin_permissions` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `pid` int(11) DEFAULT '0' COMMENT '父级id',
 `code` varchar(100) DEFAULT '',
 `module` varchar(255) DEFAULT NULL COMMENT '模块',
 `aid` int(11) DEFAULT '0' COMMENT '权限ID',
 `type` varchar(50) DEFAULT '0' COMMENT 's系统功能模块, f系统功能',
 `sort` int(11) DEFAULT '0' COMMENT '排序',
 `name` varchar(255) DEFAULT NULL,
 `display` int(11) DEFAULT '1' COMMENT '0:title 1：name',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_admin_logs`;
CREATE TABLE `met_admin_logs` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `module` varchar(255) DEFAULT '',
  `current_url` varchar(255) DEFAULT '',
  `brower` varchar(255) DEFAULT '',
  `result` varchar(255) DEFAULT '',
  `ip` varchar(50) DEFAULT '',
  `client` varchar(50) DEFAULT '',
  `time` int(11) DEFAULT '0',
  `user_agent` varchar(255) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_admin_table`;
CREATE TABLE `met_admin_table` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `pid` int(11) DEFAULT '0',
  `role_id` int(11) DEFAULT '0',
  `admin_type` text,
  `admin_id` varchar(20) NOT NULL DEFAULT '',
  `admin_pass` varchar(64) NOT NULL DEFAULT '',
  `admin_name` varchar(30) NOT NULL DEFAULT '',
  `admin_mobile` varchar(20) DEFAULT '',
  `admin_email` varchar(150) DEFAULT '',
  `admin_introduction` text,
  `admin_login` int(11) DEFAULT '0',
  `admin_modify_ip` varchar(20) DEFAULT '',
  `admin_modify_date` datetime,
  `admin_register_date` datetime,
  `admin_approval_date` datetime,
  `admin_ok` int(11) DEFAULT '0',
  `admin_op` varchar(30) DEFAULT 'metinfo',
  `admin_group` int(11) DEFAULT '0',
  `content_type` INT(11) DEFAULT '0',
  `cookie` text,
  `lang` varchar(50) DEFAULT '',
  `langok` varchar(255) DEFAULT 'metinfo',
  `admin_login_lang` varchar(50) DEFAULT '' COMMENT '登录默认语言',
  `admin_issueok` int(11) DEFAULT '0',
  `admin_check` int(11) DEFAULT '0' COMMENT '发布信息需要审核才能正常显示',
  `openid` varchar(255) DEFAULT '',
  `access_token` varchar(255) DEFAULT '',
  `expires_in` int(11) DEFAULT '0',
  `other_login` int(11) DEFAULT '0',
  PRIMARY KEY  (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_app_config`;
CREATE TABLE `met_app_config` (
  `id` int(11) NOT NULL auto_increment,
  `appno` int(20) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `value` text,
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_applist`;
CREATE TABLE IF NOT EXISTS `met_applist` (
  `id` int(11) NOT NULL auto_increment,
  `no` int(11) DEFAULT '0',
  `ver` varchar(50) DEFAULT '',
  `m_name` varchar(50) DEFAULT '',
  `m_class` varchar(50) DEFAULT '',
  `m_action` varchar(50) DEFAULT '',
  `appname` varchar(50) DEFAULT '',
  `info` text,
  `addtime` int(11) DEFAULT '0',
  `updatetime` int(11) DEFAULT '0',
  `target` int(11) DEFAULT '0',
  `display` int(11) DEFAULT '1',
  `depend`  varchar(100) NULL,
  `mlangok`  int(1) NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_app_plugin`;
CREATE TABLE IF NOT EXISTS `met_app_plugin` (
  `id` int(11) NOT NULL auto_increment,
  `no_order` int(11) DEFAULT '0',
  `no` int(11) DEFAULT '0',
  `m_name` varchar(255) DEFAULT '',
  `m_action` varchar(255) DEFAULT '',
  `effect` tinyint(1) DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_column`;
CREATE TABLE `met_column` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) DEFAULT '',
  `foldername` varchar(50) DEFAULT '',
  `filename` varchar(50) DEFAULT '',
  `bigclass` int(11) DEFAULT '0',
  `samefile` int(11) DEFAULT '0',
  `module` int(11) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `wap_ok` int(1) DEFAULT '0',
  `wap_nav_ok` int( 11 ) DEFAULT '0',
  `if_in` int(1) DEFAULT '0',
  `nav` int(1) DEFAULT '0',
  `ctitle` varchar(200) DEFAULT '',
  `keywords` varchar(200) DEFAULT '',
  `content` longtext,
  `description` text,
  `other_info` text,
  `custom_info` text,
  `list_order` int(11) DEFAULT '0',
  `new_windows` varchar(50) DEFAULT '',
  `classtype` int(11) DEFAULT '1',
  `out_url` varchar(200) DEFAULT '',
  `index_num` int(11) DEFAULT '0',
  `access` text,
  `indeximg` varchar(255) DEFAULT '',
  `columnimg` varchar(255) DEFAULT '',
  `isshow` int(11) DEFAULT '1',
  `lang` varchar(50) DEFAULT '',
  `namemark` varchar(255) DEFAULT '',
  `releclass` int(11) DEFAULT '0',
  `display` int(11) DEFAULT '0',
  `icon` varchar(100) DEFAULT '',
  `nofollow` int(1) DEFAULT '0',
  `text_size` int(11) DEFAULT '0',
  `text_color` varchar(100) DEFAULT '',
  `thumb_list` varchar (50) DEFAULT '',
  `thumb_detail` varchar(50) DEFAULT '',
  `list_length` int(11) DEFAULT '0',
  `tab_num` int(11) DEFAULT '0',
  `tab_name` varchar(255) DEFAULT '',
  `style_type` varchar(255) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_config`;
CREATE TABLE `met_config` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) DEFAULT '',
  `value` text ,
  `mobile_value` text,
  `columnid` int(11) DEFAULT '0',
  `flashid` int(11) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_cv`;
CREATE TABLE `met_cv` (
  `id` int(11) NOT NULL auto_increment,
  `addtime` datetime,
  `readok` int(11) DEFAULT '0',
  `customerid` varchar(50) DEFAULT '0',
  `jobid` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  `ip` varchar(255) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_download`;
CREATE TABLE `met_download` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) DEFAULT '',
  `ctitle` varchar(200) DEFAULT '',
  `keywords` varchar(200) DEFAULT '',
  `description` text,
  `content` longtext,
  `class1` int(11) DEFAULT '0',
  `class2` int(11) DEFAULT '0',
  `class3` int(11) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `new_ok` int(1) DEFAULT '0',
  `wap_ok` int(1) DEFAULT '0',
  `imgurl` varchar(255) DEFAULT '',
  `downloadurl` varchar(255) DEFAULT '',
  `filesize` varchar(100) DEFAULT '',
  `com_ok` int(1) DEFAULT '0',
  `hits` int(11) DEFAULT '0',
  `updatetime` datetime,
  `addtime` datetime,
  `issue` varchar(100) DEFAULT '',
  `access` text,
  `top_ok` int(1) DEFAULT '0',
  `downloadaccess` text,
  `filename` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `recycle` int(11) NOT NULL DEFAULT '0',
  `displaytype` int(11) NOT NULL DEFAULT '1',
  `tag` text,
  `links` varchar(200) DEFAULT '',
  `text_size` int(11)  DEFAULT '0',
  `text_color` varchar(100) DEFAULT '',
  `other_info` text,
  `custom_info` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_feedback`;
CREATE TABLE `met_feedback` (
  `id` int(11) NOT NULL auto_increment,
  `class1` int(11) DEFAULT '0',
  `fdtitle` varchar(255) DEFAULT '',
  `fromurl` varchar(255) DEFAULT '',
  `ip` varchar(255) DEFAULT '',
  `addtime` datetime,
  `readok` int(11) DEFAULT '0',
  `useinfo` text,
  `customerid` varchar(30) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_flash`;
CREATE TABLE `met_flash` (
  `id` int(11) NOT NULL auto_increment,
  `module` text,
  `img_title` varchar(255) DEFAULT '',
  `img_path` varchar(255) DEFAULT '',
  `img_link` varchar(255) DEFAULT '',
  `flash_path` varchar(255) DEFAULT '',
  `flash_back` varchar(255) DEFAULT '',
  `no_order` int(11) DEFAULT '0',
  `width` int(11) DEFAULT '0',
  `height` int(11) DEFAULT '0',
  `wap_ok` int(11) DEFAULT '0',
  `img_title_color` varchar(100) DEFAULT '',
  `img_des` varchar(255) DEFAULT '',
  `img_des_color` varchar(100) DEFAULT '',
  `img_text_position` varchar(100) DEFAULT '4',
  `img_title_fontsize` int(11) DEFAULT '0',
  `img_des_fontsize` int(11) DEFAULT '0',
  `height_m` int(11) DEFAULT '0',
  `height_t` int(11) DEFAULT '0',
  `mobile_img_path` varchar(255) DEFAULT '',
  `img_title_mobile` varchar(255) DEFAULT '',
  `img_title_color_mobile` varchar(100) DEFAULT '',
  `img_text_position_mobile` varchar(100) DEFAULT '4',
  `img_title_fontsize_mobile` int(11) DEFAULT '0',
  `img_des_mobile` varchar(255) DEFAULT '',
  `img_des_color_mobile` varchar(100) DEFAULT '',
  `img_des_fontsize_mobile` int(11) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  `target` int(11) DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_flash_button`;
CREATE TABLE `met_flash_button` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flash_id` int(11) NOT NULL DEFAULT '0',
  `but_text` varchar(255) DEFAULT '',
  `but_url` varchar(255) DEFAULT '',
  `but_text_size` int(11) DEFAULT '0',
  `but_text_color` varchar(100) DEFAULT '',
  `but_text_hover_color` varchar(100) DEFAULT '',
  `but_color` varchar(100) DEFAULT '',
  `but_hover_color` varchar(100) DEFAULT '',
  `but_size` varchar(100) DEFAULT '',
  `is_mobile` int(11) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `target` int(11) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_flist`;
CREATE TABLE `met_flist` (
  `id` int(11) NOT NULL auto_increment,
  `listid` int(11) DEFAULT '0',
  `paraid` int(11) DEFAULT '0',
  `info` text,
  `lang` varchar(50) DEFAULT '',
  `imgname` varchar(255) DEFAULT '',
  `module` int(11) DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_ifcolumn`;
CREATE TABLE IF NOT EXISTS `met_ifcolumn` (
  `id` int(11) NOT NULL auto_increment,
  `no` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT '',
  `appname` varchar(50) DEFAULT '' COMMENT '应用名称',
  `addfile` tinyint(1) DEFAULT '1',
  `memberleft` tinyint(1) DEFAULT '0',
  `uniqueness` tinyint(1) DEFAULT '0',
  `fixed_name` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_ifcolumn_addfile`;
CREATE TABLE IF NOT EXISTS `met_ifcolumn_addfile` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `no` int(11) DEFAULT '0',
  `filename` varchar(255) DEFAULT '',
  `m_name` varchar(255) DEFAULT '',
  `m_module` varchar(255) DEFAULT '',
  `m_class` varchar(255) DEFAULT '',
  `m_action` varchar(255) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_ifmember_left`;
CREATE TABLE IF NOT EXISTS `met_ifmember_left` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `no` int(11) DEFAULT '0',
  `columnid` int(11) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `foldername` varchar(255) DEFAULT '',
  `filename` varchar(255) DEFAULT '',
  `target` int(11) DEFAULT '0',
  `own_order` varchar(11) DEFAULT '',
  `effect` int(1) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_img`;
CREATE TABLE `met_img` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) DEFAULT '',
  `ctitle` varchar(200) DEFAULT '',
  `keywords` varchar(200) DEFAULT '',
  `description` text,
  `content` longtext,
  `class1` int(11) DEFAULT '0',
  `class2` int(11) DEFAULT '0',
  `class3` int(11) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `wap_ok` int(1) DEFAULT '0',
  `new_ok` int(1) DEFAULT '0',
  `imgurl` varchar(255) DEFAULT '',
  `imgurls` varchar(255) DEFAULT '',
  `displayimg` text,
  `com_ok` int(1) DEFAULT '0',
  `hits` int(11) DEFAULT '0',
  `updatetime` datetime,
  `addtime` datetime,
  `issue` varchar(100) DEFAULT '',
  `access` text,
  `top_ok` int(1) DEFAULT '0',
  `filename` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `content1` text,
  `content2` text,
  `content3` text,
  `content4` text,
  `contentinfo` varchar(255) DEFAULT '',
  `contentinfo1` varchar(255) DEFAULT '',
  `contentinfo2` varchar(255) DEFAULT '',
  `contentinfo3` varchar(255) DEFAULT '',
  `contentinfo4` varchar(255) DEFAULT '',
  `recycle` int(11) DEFAULT '0',
  `displaytype` int(11) DEFAULT '1',
  `tag` text,
  `links` varchar(200) DEFAULT '',
  `imgsize` varchar(200) DEFAULT '',
  `text_size` int(11)  DEFAULT '0',
  `text_color` varchar(100) DEFAULT '',
  `other_info` text,
  `custom_info` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_infoprompt`;
CREATE TABLE IF NOT EXISTS `met_infoprompt` (
  `id` int(11) NOT NULL auto_increment,
  `news_id` int(11) DEFAULT '0',
  `newstitle` varchar(120) DEFAULT '',
  `content` text,
  `url` varchar(200) DEFAULT '',
  `member` varchar(50) DEFAULT '',
  `type` varchar(35) DEFAULT '',
  `time` int(11) DEFAULT '0',
  `see_ok` int(11) DEFAULT '0',
  `lang` varchar(10) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_job`;
CREATE TABLE `met_job` (
  `id` int(11) NOT NULL auto_increment,
  `position` varchar(200) DEFAULT '',
  `count` int(11) DEFAULT '0',
  `place` varchar(200) DEFAULT '',
  `deal` varchar(200) DEFAULT '',
  `addtime` date,
  `updatetime` date,
  `useful_life` int(11) DEFAULT '0',
  `content` longtext,
  `access` text,
  `class1` int(11) DEFAULT '0',
  `class2` int(11) DEFAULT '0',
  `class3` int(11) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `wap_ok` int(1) DEFAULT '0',
  `top_ok` int(1) DEFAULT '0',
  `email` varchar(255) DEFAULT '',
  `filename` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `displaytype` int(11) DEFAULT '1',
  `text_size` int(11)  DEFAULT '0',
  `text_color` varchar(100) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_label`;
CREATE TABLE `met_label` (
  `id` int(11) NOT NULL auto_increment,
  `oldwords` varchar(255) DEFAULT '',
  `newwords` varchar(255) DEFAULT '',
  `newtitle` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `num` int(11) DEFAULT '99',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_lang`;
CREATE TABLE `met_lang` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) DEFAULT '',
  `useok` int(1) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `mark` varchar(50) DEFAULT '',
  `synchronous` varchar(50) DEFAULT '',
  `flag` varchar(100) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `newwindows` int(1) DEFAULT '0',
  `metconfig_webhtm` int(1) DEFAULT '0',
  `metconfig_htmtype` varchar(50) DEFAULT '',
  `metconfig_weburl` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_lang_admin`;
CREATE TABLE `met_lang_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '' COMMENT '语言名称',
  `useok` int(1) DEFAULT '1' COMMENT '语言是否开启，1开启，0不开启',
  `no_order` int(11) DEFAULT '0' COMMENT '排序',
  `mark` varchar(50) DEFAULT '' COMMENT '语言标识（唯一）',
  `synchronous` varchar(50) DEFAULT '' COMMENT '同步官方语言标识',
  `link` varchar(255) DEFAULT '' COMMENT '语言外部链接',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_language`;
CREATE TABLE `met_language` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) DEFAULT '',
  `value` text,
  `site` tinyint(1) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `array` int(11) DEFAULT '0',
  `app` int(11) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_link`;
CREATE TABLE `met_link` (
  `id` int(11) NOT NULL auto_increment,
  `webname` varchar(255) DEFAULT '',
  `module` text,
  `weburl` varchar(255) DEFAULT '',
  `weblogo` varchar(255) DEFAULT '',
  `link_type` int(11) DEFAULT '0',
  `info` varchar(255) DEFAULT '',
  `contact` varchar(255) DEFAULT '',
  `orderno` int(11) DEFAULT '0',
  `com_ok` int(11) DEFAULT '0',
  `show_ok` int(11) DEFAULT '0',
  `addtime` datetime,
  `lang` varchar(50) DEFAULT '',
  `ip` varchar(255) DEFAULT '',
  `nofollow` tinyint(1) DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_menu`;
CREATE TABLE `met_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT '',
  `type` int(11) DEFAULT '0',
  `text_color` varchar(100) DEFAULT '',
  `but_color` varchar(100) DEFAULT '',
  `target` int(11) DEFAULT '0' ,
  `enabled` int(11) DEFAULT '1',
  `no_order` int(11) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_message`;
CREATE TABLE `met_message` (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(255) DEFAULT '',
  `addtime` datetime,
  `readok` int(11) DEFAULT '0',
  `useinfo` text,
  `lang` varchar(50) DEFAULT '',
  `access` text,
  `customerid` varchar(30) DEFAULT '0',
  `checkok` int(11) DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_mlist`;
CREATE TABLE `met_mlist` (
  `id` int(11) NOT NULL auto_increment,
  `listid` int(11) DEFAULT '0',
  `paraid` int(11) DEFAULT '0',
  `info` text,
  `lang` varchar(50) DEFAULT '',
  `imgname` varchar(255) DEFAULT '',
  `module` int(11) DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_news`;
CREATE TABLE `met_news` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) DEFAULT '',
  `ctitle` varchar(200) DEFAULT '',
  `keywords` varchar(200) DEFAULT '',
  `description` text,
  `content` longtext,
  `class1` int(11) DEFAULT '0',
  `class2` int(11) DEFAULT '0',
  `class3` int(11) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `wap_ok` int(1) DEFAULT '0',
  `img_ok` int(1) DEFAULT '0',
  `imgurl` varchar(255) DEFAULT '',
  `imgurls` varchar(255) DEFAULT '',
  `com_ok` int(1) DEFAULT '0',
  `issue` varchar(100) DEFAULT '',
  `hits` int(11) DEFAULT '0',
  `updatetime` datetime,
  `addtime` datetime,
  `access` text,
  `top_ok` int(1) DEFAULT '0',
  `filename` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `recycle` int(11) DEFAULT '0',
  `displaytype` int(11) DEFAULT '1',
  `tag` text,
  `links` varchar(200) DEFAULT '',
  `publisher` varchar(50) DEFAULT '',
  `text_size` int(11)  DEFAULT '0',
  `text_color` varchar(100) DEFAULT '',
  `other_info` text,
  `custom_info` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_online`;
CREATE TABLE `met_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_order` int(11) DEFAULT '0',
  `name` varchar(255) DEFAULT '',
  `value` varchar(255) DEFAULT '',
  `icon` varchar(255) DEFAULT '',
  `type` int(11) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_para`;
CREATE TABLE `met_para` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT '0',
  `value` varchar(255) DEFAULT '',
  `module` int(10) DEFAULT '0',
  `order` int(10) DEFAULT '0',
  `lang` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_parameter`;
CREATE TABLE `met_parameter` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) DEFAULT '',
  `options` text,
  `description` text,
  `no_order` int(2) DEFAULT '0',
  `type` int(2) DEFAULT '0',
  `access` text,
  `wr_ok` int(2) DEFAULT '0',
  `class1` int(11) DEFAULT '0',
  `class2` int(11) DEFAULT '0',
  `class3` int(11) DEFAULT '0',
  `module` int(2) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  `wr_oks` int(2) DEFAULT '0',
  `related` varchar(50) DEFAULT '',
  `edit_ok` int(2) DEFAULT '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_plist`;
CREATE TABLE `met_plist` (
  `id` int(11) NOT NULL auto_increment,
  `listid` int(11) DEFAULT '0',
  `paraid` int(11) DEFAULT '0',
  `info` text,
  `lang` varchar(50) DEFAULT '',
  `imgname` varchar(255) DEFAULT '',
  `module` int(11) DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_product`;
CREATE TABLE `met_product` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(200) DEFAULT '',
  `ctitle` varchar(200) DEFAULT '',
  `keywords` varchar(200) DEFAULT '',
  `description` text,
  `content` longtext,
  `class1` int(11) DEFAULT '0',
  `class2` int(11) DEFAULT '0',
  `class3` int(11) DEFAULT '0',
  `classother` text NOT NULL,
  `no_order` int(11) DEFAULT '0',
  `wap_ok` int(1) DEFAULT '0',
  `new_ok` int(1) DEFAULT '0',
  `imgurl` varchar(255) DEFAULT '',
  `imgurls` varchar(255) DEFAULT '',
  `displayimg` text,
  `com_ok` int(1) DEFAULT '0',
  `hits` int(11) DEFAULT '0',
  `updatetime` datetime,
  `addtime` datetime,
  `issue` varchar(100) DEFAULT '',
  `access` text,
  `top_ok` int(1) DEFAULT '0',
  `filename` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `video` text,
  `content1` mediumtext,
  `content2` mediumtext,
  `content3` mediumtext,
  `content4` mediumtext,
  `contentinfo` varchar(255) DEFAULT '',
  `contentinfo1` varchar(255) DEFAULT '',
  `contentinfo2` varchar(255) DEFAULT '',
  `contentinfo3` varchar(255) DEFAULT '',
  `contentinfo4` varchar(255) DEFAULT '',
  `recycle` int(11) DEFAULT '0',
  `displaytype` int(11) DEFAULT '1',
  `tag` text,
  `links` varchar(200) DEFAULT '',
  `imgsize` varchar(200) DEFAULT '',
  `text_size` int(11)  DEFAULT '0',
  `text_color` varchar(100) DEFAULT '',
  `other_info` text,
  `custom_info` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_relation`;
CREATE TABLE `met_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT NULL COMMENT '内容id',
  `module` int(11) DEFAULT NULL,
  `relation_id` int(11) DEFAULT NULL COMMENT '关联内容id',
  `relation_module` int(11) DEFAULT NULL,
  `lang` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_skin_table`;
CREATE TABLE `met_skin_table` (
  `id` int(11) NOT NULL auto_increment,
  `skin_name` varchar(200) DEFAULT '',
  `skin_file` varchar(20) DEFAULT '',
  `skin_info` text,
  `devices` int(11) DEFAULT '0',
  `ver` varchar(10) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_tags`;
CREATE TABLE `met_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(255)  DEFAULT '',
  `tag_pinyin` varchar(255)  DEFAULT '',
  `module` int(11) DEFAULT 0,
  `cid` int(11) DEFAULT 0,
  `list_id` varchar(255)  DEFAULT '',
  `title` varchar(255)  DEFAULT '',
  `keywords` varchar(255)  DEFAULT '',
  `description` varchar(255)  DEFAULT '',
  `tag_color` varchar(255)  DEFAULT '',
  `tag_size` int(10) DEFAULT '0',
  `sort` int(10) DEFAULT 0,
  `lang` varchar(100) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_templates`;
CREATE TABLE IF NOT EXISTS `met_templates` (
  `id` int(11) NOT NULL auto_increment,
  `no` varchar(20) DEFAULT '0',
  `pos` int(11) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `type` int(11) DEFAULT '0',
  `style` int(11) DEFAULT '0',
  `selectd` varchar(500) DEFAULT '',
  `name` varchar(50) DEFAULT '',
  `value` text,
  `defaultvalue` text,
  `valueinfo` varchar(100) DEFAULT '',
  `tips` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `bigclass` int(11) DEFAULT 0,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_ui_list`;
CREATE TABLE `met_ui_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `installid` int(10) DEFAULT '0',
  `parent_name` varchar(100) DEFAULT '',
  `ui_name` varchar(100) DEFAULT '',
  `skin_name` varchar(100) DEFAULT '',
  `ui_page` varchar(200) DEFAULT '',
  `ui_title` varchar(100) DEFAULT '',
  `ui_description` varchar(500) DEFAULT '',
  `ui_order` int(10) DEFAULT '0',
  `ui_version` varchar(100) DEFAULT '',
  `ui_installtime` int(10) DEFAULT '0',
  `ui_edittime` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_ui_config`;
CREATE TABLE `met_ui_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT '0',
  `parent_name` varchar(100) DEFAULT '',
  `ui_name` varchar(100) DEFAULT '',
  `skin_name` varchar(100) DEFAULT '',
  `uip_type` int(10) DEFAULT '0',
  `uip_style` tinyint(1) DEFAULT '0',
  `uip_select` varchar(500) DEFAULT '1',
  `uip_name` varchar(100) DEFAULT '',
  `uip_key` varchar(100) DEFAULT '',
  `uip_value` text,
  `uip_default` varchar(255) DEFAULT '',
  `uip_title` varchar(100) DEFAULT '',
  `uip_description` varchar(255) DEFAULT '',
  `uip_order` int(10)  DEFAULT '0',
  `lang` varchar(100) DEFAULT '',
  `uip_hidden` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_user`;
CREATE TABLE IF NOT EXISTS `met_user` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(30) DEFAULT '',
  `password` varchar(32) DEFAULT '',
  `head` varchar(100) DEFAULT '',
  `email` varchar(50) DEFAULT '',
  `tel` varchar(20) DEFAULT '',
  `groupid` int(11) DEFAULT '0',
  `register_time` int(11) DEFAULT '0',
  `register_ip` varchar(15) DEFAULT '',
  `login_time` int(11) DEFAULT '0',
  `login_count` int(11) DEFAULT '0',
  `login_ip` varchar(15) DEFAULT '',
  `valid` int(1) DEFAULT '0',
  `source` varchar(20) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `idvalid` int(1) DEFAULT '0' COMMENT '实名认证状态',
  `reidinfo` varchar(100) DEFAULT '' COMMENT '实名信息  姓名|身份证|手机号',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `met_user_group`;
CREATE TABLE IF NOT EXISTS `met_user_group` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) DEFAULT '',
  `access` int(11) DEFAULT '0',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_user_group_pay`;
CREATE TABLE IF NOT EXISTS`met_user_group_pay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(11) DEFAULT '0' COMMENT '会员组ID',
  `price` double(10,2) DEFAULT '0' COMMENT '购买价格',
  `recharge_price` double(10,2) DEFAULT '0'  COMMENT '充值价格',
  `buyok` int(1) DEFAULT '0' COMMENT '付费会员',
  `rechargeok` int(50) DEFAULT '0' COMMENT '充值会员',
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_user_list`;
CREATE TABLE IF NOT EXISTS `met_user_list` (
  `id` int(11) NOT NULL auto_increment,
  `listid` int(11) DEFAULT '0',
  `paraid` int(11) DEFAULT '0',
  `info` text,
  `lang` varchar(50) DEFAULT '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_user_other`;
CREATE TABLE IF NOT EXISTS `met_user_other` (
  `id` int(11) NOT NULL auto_increment,
  `metconfig_uid` int(11) DEFAULT '0',
  `openid` varchar(100) DEFAULT '',
  `unionid` varchar(100) DEFAULT '',
  `access_token` varchar(255) DEFAULT '',
  `expires_in` int(11) DEFAULT '0',
  `type` varchar(10) DEFAULT '',
  PRIMARY KEY  (`id`),
  KEY `openid` (`openid`),
  KEY `metconfig_uid` (`metconfig_uid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `met_weixin_reply_log`;
CREATE TABLE IF NOT EXISTS `met_weixin_reply_log` (
  `id` int(11) NOT NULL auto_increment,
  `FromUserName` varchar(255) DEFAULT '',
  `Content` text,
  `rid` int(11) DEFAULT NULL,
  `CreateTime` int(10) DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `met_files`;
CREATE TABLE `met_files`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(511) NOT NULL COMMENT '文件路径',
  `md5` varchar(50) NOT NULL COMMENT '文件md5',
  `size` int(11) NOT NULL COMMENT '文件大小 KB',
  `extension` varchar(50) NOT NULL COMMENT '扩展名',
  `type` varchar(50) NOT NULL COMMENT '类型 img|video',
  `folder` varchar(255) NOT NULL COMMENT '文件夹',
  `publisher` varchar(50) NOT NULL COMMENT '发布者',
  `create_at` datetime NOT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `met_history`;
CREATE TABLE `met_history`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` varchar(511) NOT NULL COMMENT '内容id',
  `module` int(11) NOT NULL COMMENT '模块编号',
  `title` varchar(200) DEFAULT '',
  `ctitle` varchar(200) DEFAULT '',
  `keywords` varchar(200) DEFAULT '',
  `description` text,
  `content` longtext,
  `content1` mediumtext,
  `content2` mediumtext,
  `content3` mediumtext,
  `content4` mediumtext,
  `contentinfo` varchar(255) DEFAULT '',
  `contentinfo1` varchar(255) DEFAULT '',
  `contentinfo2` varchar(255) DEFAULT '',
  `contentinfo3` varchar(255) DEFAULT '',
  `contentinfo4` varchar(255) DEFAULT '',
  `class1` int(11) DEFAULT '0',
  `class2` int(11) DEFAULT '0',
  `class3` int(11) DEFAULT '0',
  `no_order` int(11) DEFAULT '0',
  `wap_ok` int(1) DEFAULT '0',
  `img_ok` int(1) DEFAULT '0',
  `imgurl` varchar(255) DEFAULT '',
  `imgurls` varchar(255) DEFAULT '',
  `displayimg` text,
  `video` text,
  `com_ok` int(1) DEFAULT '0',
  `issue` varchar(100) DEFAULT '',
  `hits` int(11) DEFAULT '0',
  `updatetime` datetime,
  `addtime` datetime,
  `access` text,
  `top_ok` int(1) DEFAULT '0',
  `filename` varchar(255) DEFAULT '',
  `lang` varchar(50) DEFAULT '',
  `recycle` int(11) DEFAULT '0',
  `displaytype` int(11) DEFAULT '0',
  `tag` text,
  `links` varchar(200) DEFAULT '',
  `publisher` varchar(50) DEFAULT '',
  `text_size` int(11)  DEFAULT '0',
  `text_color` varchar(255) DEFAULT '',
  `other_info` text,
  `custom_info` text,
  `imgsize` varchar(255) DEFAULT '',
  `downloadurl` varchar(255) DEFAULT '',
  `filesize` varchar(100) DEFAULT '',
  `position` varchar(200) DEFAULT '',
  `count` int(11) DEFAULT '0',
  `place` varchar(200) DEFAULT '',
  `deal` varchar(200) DEFAULT '',
  `useful_life` int(11) DEFAULT '0',
  `email` varchar(255) DEFAULT '',
  `record_time` datetime,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

DROP TABLE IF EXISTS `met_history_plist`;
CREATE TABLE `met_history_plist` (
 `id` int(11) NOT NULL auto_increment,
 `hid` int(11) DEFAULT '0',
 `listid` int(11) DEFAULT '0',
 `paraid` int(11) DEFAULT '0',
 `info` text,
 `lang` varchar(50) DEFAULT '',
 `imgname` varchar(255) DEFAULT '',
 `module` int(11) DEFAULT '0',
 PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `met_history_relation`;
CREATE TABLE `met_history_relation` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `hid` int(11) DEFAULT '0',
 `aid` int(11) DEFAULT NULL COMMENT '内容id',
 `module` int(11) DEFAULT NULL,
 `relation_id` int(11) DEFAULT NULL COMMENT '关联内容id',
 `relation_module` int(11) DEFAULT NULL,
 `lang` varchar(50) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


#系统全局配置
INSERT INTO met_config VALUES (null,'metcms_v','8.0','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_ch_lang','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_lang_mark','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_admin_type_ok','0','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_admin_type','cn','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_index_type','cn','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_host','api.metinfo.cn','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_host_new','app.metinfo.cn','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_api', 'https://u.mituo.cn/api/client', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES (null,'metconfig_tablename','admin_column|admin_has_permissions|admin_logs|admin_menus|admin_permissions|admin_roles|admin_table|app_config|app_plugin|applist|column|config|cv|download|feedback|files|flash|flash_button|flist|history|history_plist|history_relation|ifcolumn|ifcolumn_addfile|ifmember_left|img|infoprompt|job|label|lang|lang_admin|language|link|menu|message|mlist|news|online|para|parameter|plist|product|relation|skin_table|tags|templates|ui_config|ui_list|user|user_group|user_group_pay|user_list|user_other|weixin_reply_log','','0','0','metinfo');
#其他配置
INSERT INTO met_config VALUES (null,'metconfig_safe_prompt', '0', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES (null,'metconfig_uiset_guide', '1', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES (null,'metconfig_301jump', '', '', 0, 0, 'metinfo');
INSERT INTO met_config VALUES (null,'metconfig_https', '', '', 0, 0, 'metinfo');
INSERT INTO met_config VALUES (null,'disable_cssjs', 0, '', 0, 0, 'metinfo');
INSERT INTO met_config VALUES (null,'metconfig_secret_key','','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_member_force','byuqujz','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_editor', 'ueditor', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES (null, 'metconfig_text_fonts', '../public/third-party/fonts/Roboto/Roboto-Regular.ttf', '','0','0', 'metinfo');
INSERT INTO met_config VALUES (null,'metconfig_smsprice','0.1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_sms_token', '', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES (null,'metconfig_sms_url', 'https://u.mituo.cn/api/sms', '', '0', '0', 'metinfo');
#SEO-siteMap
INSERT INTO met_config VALUES (null,'metconfig_sitemap_lang','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_sitemap_not2','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_sitemap_not1','0','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_sitemap_txt','0','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_sitemap_xml','1','','0','0','metinfo');
#版权控制配置
INSERT INTO met_config VALUES (null,'metconfig_agents_logo_login','../public/images/login-logo.png','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_logo_index','../public/images/logo.png','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_img','../public/images/metinfo.gif','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_copyright_foot','Powered by <b><a href=https://www.metinfo.cn target=_blank title=CMS>MetInfo $metcms_v</a></b> &copy;2008-$m_now_year &nbsp;<a href=https://www.mituo.cn target=_blank title=米拓建站>mituo.cn</a>','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_copyright_foot1','本站基于 <b><a href=https://www.metinfo.cn target=_blank title=米拓建站>米拓企业建站系统 $metcms_v</a></b> 搭建','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_copyright_foot2','技术支持：<b><a href=https://www.mituo.cn target=_blank title=米拓建站>米拓建站 $metcms_v</a></b> ','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_copyright_nofollow', '0', '', '0', '0', 'metinfo');
INSERT INTO met_config VALUES (null,'metconfig_copyright_type','1','','0','0','metinfo');
#版权控制
INSERT INTO met_config VALUES (null,'metconfig_agents_type','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_linkurl','https://www.mituo.cn','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_pageset_logo','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_update','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_code','','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_backup','metinfo','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_sms','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_app','1','','0','0','metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_metmsg', '1', '', '0', '0', 'metinfo');
#代理信息
INSERT INTO met_config VALUES (null,'metconfig_agents_thanks','感谢使用 Metinfo','','0','0','cn-metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_name','MetInfo|米拓企业建站系统','','0','0','cn-metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_copyright','长沙米拓信息技术有限公司（MetInfo Inc.）','','0','0','cn-metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_depict_login','MetInfo','','0','0','cn-metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_thanks','thanks use Metinfo','','0','0','en-metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_name','Metinfo CMS','','0','0','en-metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_copyright','China Changsha MetInfo Information Co., Ltd.','','0','0','en-metinfo');
INSERT INTO met_config VALUES (null,'metconfig_agents_depict_login','Metinfo Build marketing value corporate website','','0','0','en-metinfo');

#后台栏目

#后台菜单
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (1, 0, 'content_manage', 'manage', 1, 1301, 1, 0, 'manage', '内容管理', 1, 'lang_administration');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (2, 0, 'column_manage', 'column', 2, 1201, 1, 1, 'column', '栏目管理', 1, 'lang_htmColumn');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (3, 0, 'feedback_interaction', '', 3, 1202, 1, 2, 'feedback-interaction', '反馈互动', 1, 'lang_feedback_interaction');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (4, 0, 'seo_settings', 'seo', 4, 1404, 1, 3, 'seo', 'SEO设置', 1, 'lang_seo_set_v6');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (5, 0, 'site_template', 'app/metconfig_template', 5, 1405, 1, 4, 'template', '网站模板', 1, 'lang_appearance');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (6, 0, 'application', 'myapp', 6, 1505, 1, 5, 'application', '应用插件', 1, 'lang_myapp');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (7, 0, 'user_manage', '', 7, 1506, 1, 6, 'user', '用户管理', 1, 'lang_the_user');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (8, 0, 'security_setting', '', 8, 1200, 1, 7, 'safety', '安全设置', 1, 'lang_safety');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (9, 0, 'multilingual', 'language', 9, 1002, 1, 8, 'multilingualism', '多语言', 1, 'lang_multilingual');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (10, 0, 'basic_settings', '', 10, 1100, 1, 9, 'setting', '基本设置', 1, 'lang_unitytxt_39');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (11, 0, 'enterprise_market', 'partner', 11, 1508, 1, 10, 'partner', '企业超市', 1, 'lang_cooperation_platform');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (21, 3, 'feedback_system', 'feed_feedback_8', 21, 1509, 1, 0, 'feedback', '反馈系统', 1, 'lang_mod8');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (22, 3, 'message_system', 'feed_message_7', 22, 1510, 1, 1, 'message', '留言系统', 1, 'lang_mod7');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (23, 3, 'recruitment_system', 'feed_job_6', 23, 1511, 1, 2, 'recruit', '招聘系统', 1, 'lang_mod6');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (24, 3, 'online_settings', 'online', 24, 1106, 1, 3, 'online', '客服设置', 1, 'lang_customerService');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (26, 7, 'members', 'user', 26, 1601, 1, 0, 'member', '会员', 1, 'lang_member');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (27, 7, 'admins', 'permission', 27, 1603, 1, 1, 'administrator', '管理员', 1, 'lang_managertyp2');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (28, 8, 'safety_efficiency', 'safe', 28, 1004, 1, 0, 'safe', '安全与效率', 1, 'lang_safety_efficiency');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (29, 8, 'backup_Recovery', 'databack', 29, 1005, 1, 1, 'databack', '备份与恢复', 1, 'lang_data_processing');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (30, 10, 'basic_info', 'webset', 30, 1007, 1, 0, 'information', '基本信息', 1, 'lang_upfiletips7');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (31, 10, 'watermark', 'imgmanage', 31, 1003, 1, 1, 'picture', '图片水印', 1, 'lang_indexpic');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (32, 10, 'banner_manage', 'banner', 32, 1604, 1, 2, 'banner', 'Banner管理', 1, 'lang_banner_manage');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (33, 10, 'mobile_menus', 'menu', 33, 1605, 1, 3, 'bottom-menu', '手机菜单', 1, 'lang_the_menu');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (34, 0, 'updates', 'update', 34, 1104, 2, 2, 'update', '检测更新', 1, 'lang_checkupdate');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (37, 8, 'file_manage', 'myfiles', 37, 1302, 1, 0, 'fa-file-o', '文件管理', 0, 'lang_myfiles');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (38, 0, 'cache_clear', 'clear_cache', 38, 1901, 2, 1, 'clear_cache', '清空缓存', 1, 'lang_clearCache');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (39, 0, 'func_collection', 'function_complete', 39, 1902, 2, 3, 'function_complete', '功能大全', 1, 'lang_funcCollection');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (40, 0, 'env_detection', 'environmental_test', 40, 1903, 2, 4, 'environmental_test', '环境检测', 1, 'lang_environmental_test');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (41, 0, 'style', '', 41, 1900, 3, 0, '', '风格', 1, 'lang_skinstyle');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (42, 41, 'style_settings', 'style_settings', 42, 1905, 3, 0, 'style_settings', '风格设置', 1, 'lang_style_settings');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (44, 0, 'content_manage', 'manage', 44, 1301, 3, 2, '', '内容', 1, 'lang_content');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (45, 0, 'column_manage', 'column', 45, 1201, 3, 1, '', '栏目', 1, 'lang_banner_column_v6');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (46, 0, 'seo_settings', 'seo', 46, 1404, 3, 3, '', 'SEO设置', 1, 'lang_seo_set_v6');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (47, 0, 'multilingual', 'language', 47, 1002, 3, 4, '', '多语言', 1, 'lang_multilingual');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (48, 0, 'application', 'myapp', 48, 1505, 3, 5, '', '应用插件', 1, 'lang_myapp');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (49, 0, 'cache_clear', 'clear_cache', 49, 1901, 3, 6, '', '清空缓存', 1, 'lang_clearCache');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (50, 51, 'updates', 'update', 50, 1104, 3, 9, '', '检测更新', 1, 'lang_checkupdate');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (51, 0, 'more_func', 'more_func', 51, 1904, 3, 8, '', '更多', 1, 'lang_columnmore');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (52, 41, 'banner_manage', 'banner', 52, 1604, 3, 2, '', 'Banner管理', 1, 'lang_banner_manage');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (53, 41, 'watermark', 'imgmanage', 53, 1003, 3, 1, '', '图片水印', 1, 'lang_indexpic');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (54, 41, 'mobile_menus', 'menu', 54, 1605, 3, 3, '', '手机菜单', 1, 'lang_the_menu');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (55, 51, 'basic_info', 'webset', 55, 1007, 3, 0, 'information', '基本信息', 1, 'lang_upfiletips7');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (56, 51, 'online_settings', 'online', 56, 1106, 3, 1, 'online', '客服设置', 1, 'lang_customerService');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (57, 51, 'safety_efficiency', 'safe', 57, 1004, 3, 2, 'safe', '安全与效率', 1, 'lang_safety_efficiency');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (58, 51, 'backup_Recovery', 'databack', 58, 1005, 3, 3, 'databack', '备份与恢复', 1, 'lang_data_processing');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (59, 51, 'members', 'user', 59, 1601, 3, 4, 'member', '会员', 1, 'lang_memberManage');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (60, 51, 'admins', 'permission', 60, 1603, 3, 5, 'administrator', '管理员', 1, 'lang_indexadminname');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (61, 51, 'func_collection', 'function_complete', 61, 1902, 3, 6, 'function_complete', '功能大全', 1, 'lang_funcCollection');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (62, 51, 'enterprise_market', 'partner', 62, 1508, 3, 7, 'partner', '企业超市', 1, 'lang_cooperation_platform');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (63, 51, 'env_detection', 'environmental_test', 63, 1903, 3, 8, 'environmental_test', '环境检测', 1, 'lang_environmental_test');
INSERT INTO `met_admin_menus` (`id`, `pid`, `name`, `url`, `aid`, `sid`, `type`, `sort`, `icon`, `info`, `display`, `menu_lang`) VALUES (64, 41, 'site_template', 'app/metconfig_template', 64, 1405, 3, 0, 'template', '网站模板', 1, 'lang_appearance');

#系统权限
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (1, 0, 'content_manage', 'manage', 1301, 's', 0, '内容管理', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (2, 0, 'column_manage', 'column', 1201, 's', 1, '栏目管理', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (3, 0, 'feedback_interaction', '', 1202, 's', 2, '反馈互动', 0);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (4, 0, 'seo_settings', 'seo', 1404, 's', 3, 'SEO设置', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (5, 0, 'site_template', 'template', 1405, 's', 4, '网站模板', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (6, 0, 'application', 'myapp', 1505, 's', 5, '应用插件', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (7, 0, 'user_manage', '', 1506, 's', 6, '用户管理', 0);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (8, 0, 'security_setting', '', 1200, 's', 7, '安全设置', 0);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (9, 0, 'multilingual', 'language', 1002, 's', 8, '多语言', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (10, 0, 'basic_settings', '', 0, 's', 9, '基本设置', 0);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (11, 0, 'enterprise_market', 'partner', 1508, 's', 10, '企业超市', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (12, 3, 'feedback_system', 'feedback', 1509, 's', 0, '反馈系统', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (13, 3, 'message_system', 'message', 1510, 's', 1, '留言系统', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (14, 3, 'recruitment_system', 'job', 1511, 's', 2, '招聘系统', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (15, 3, 'online_settings', 'online', 1106, 's', 3, '客服设置', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (16, 7, 'members', 'user', 1601, 's', 0, '会员', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (17, 7, 'admins', 'permission', 1603, 's', 1, '管理员', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (18, 8, 'safety_efficiency', 'safe', 1004, 's', 0, '安全与效率', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (19, 8, 'backup_Recovery', 'databack', 1005, 's', 1, '备份与恢复', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (20, 10, 'basic_info', 'webset', 1007, 's', 0, '基本信息', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (21, 10, 'watermark', 'imgmanage', 1003, 's', 1, '图片水印', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (22, 10, 'banner_manage', 'banner', 1604, 's', 2, 'Banner管理', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (23, 10, 'mobile_menus', 'menu', 1605, 's', 3, '手机菜单', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (24, 0, 'admin_pop', '', 8888, 'f', 0, '可视化', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (25, 6, 'app_install', '', 1800, 's', 0, '安装应用', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (26, 6, 'app_uninstall', '', 1801, 's', 0, '卸载应用', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (27, 1, 'add', '', 1802, 's', 0, '添加内容', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (28, 1, 'edit', '', 1803, 's', 0, '编辑内容', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (29, 1, 'delete', '', 1804, 's', 0, '删除内容', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (30, 2, 'column_add', '', 9999, 's', 0, '新增栏目', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (31, 2, 'column_edit', '', 9888, 's', 0, '编辑栏目', 1);
INSERT INTO `met_admin_permissions` (`id`, `pid`, `code`, `module`, `aid`, `type`, `sort`, `name`, `display`) VALUES (32, 2, 'column_del', '', 9777, 's', 0, '删除栏目', 1);

#角色
INSERT INTO `met_admin_roles` (`id`, `code`, `name`, `sort`, `info`) VALUES (1, 'root', '超级管理员', 1, '超级管理员');
INSERT INTO `met_admin_roles` (`id`, `code`, `name`, `sort`, `info`) VALUES (2, 'sys_admin_1', '管理员', 2, '系统管理员');
INSERT INTO `met_admin_roles` (`id`, `code`, `name`, `sort`, `info`) VALUES (3, 'sys_admin_2', '内容管理员', 3, '内容管理员');

#默认角色权限
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (1, 2, 0, '1', 'l', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (2, 2, 0, '2', 'l', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (3, 2, 0, '8888', 'f', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (4, 2, 0, '1301', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (5, 2, 0, '1802', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (6, 2, 0, '1803', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (7, 2, 0, '1804', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (8, 2, 0, '126', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (9, 2, 0, '132', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (10, 2, 0, '133', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (11, 2, 0, '134', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (12, 2, 0, '77', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (13, 2, 0, '127', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (14, 2, 0, '136', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (15, 2, 0, '137', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (16, 2, 0, '139', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (17, 2, 0, '128', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (18, 2, 0, '140', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (19, 2, 0, '141', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (20, 2, 0, '142', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (21, 2, 0, '129', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (22, 2, 0, '130', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (23, 2, 0, '131', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (24, 2, 0, '76', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (25, 2, 0, '73', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (26, 2, 0, '74', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (27, 2, 0, '2', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (28, 2, 0, '1', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (29, 2, 0, '143', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (30, 2, 0, '144', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (31, 2, 0, '145', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (32, 2, 0, '146', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (33, 2, 0, '163', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (34, 2, 0, '148', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (35, 2, 0, '149', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (36, 2, 0, '150', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (37, 2, 0, '151', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (38, 2, 0, '152', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (39, 2, 0, '153', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (40, 2, 0, '154', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (41, 2, 0, '155', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (42, 2, 0, '156', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (43, 2, 0, '157', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (44, 2, 0, '158', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (45, 2, 0, '159', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (46, 2, 0, '160', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (47, 2, 0, '161', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (48, 2, 0, '162', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (49, 2, 0, '164', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (50, 2, 0, '1201', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (51, 2, 0, '9999', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (52, 2, 0, '9888', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (53, 2, 0, '9777', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (54, 2, 0, '1202', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (55, 2, 0, '1509', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (56, 2, 0, '1510', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (57, 2, 0, '1511', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (58, 2, 0, '1106', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (59, 2, 0, '1404', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (60, 2, 0, '1405', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (61, 2, 0, '1505', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (62, 2, 0, '1800', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (63, 2, 0, '1801', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (64, 2, 0, '10070', 'a', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (65, 2, 0, '50002', 'a', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (66, 2, 0, '1506', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (67, 2, 0, '1601', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (68, 2, 0, '1603', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (69, 2, 0, '1200', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (70, 2, 0, '1004', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (71, 2, 0, '1005', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (72, 2, 0, '1002', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (73, 2, 0, '0', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (74, 2, 0, '1007', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (75, 2, 0, '1003', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (76, 2, 0, '1604', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (77, 2, 0, '1605', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (78, 2, 0, '1508', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (79, 2, 0, '1', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (80, 2, 0, '2', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (81, 2, 0, '3', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (82, 2, 0, '21', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (83, 2, 0, '22', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (84, 2, 0, '23', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (85, 2, 0, '24', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (86, 2, 0, '4', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (87, 2, 0, '5', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (88, 2, 0, '6', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (89, 2, 0, '7', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (90, 2, 0, '26', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (91, 2, 0, '27', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (92, 2, 0, '8', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (93, 2, 0, '28', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (94, 2, 0, '29', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (95, 2, 0, '9', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (96, 2, 0, '10', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (97, 2, 0, '30', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (98, 2, 0, '31', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (99, 2, 0, '32', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (100, 2, 0, '33', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (101, 2, 0, '11', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (102, 2, 0, '38', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (103, 2, 0, '34', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (104, 2, 0, '39', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (105, 2, 0, '40', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (106, 2, 0, '41', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (107, 2, 0, '42', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (108, 2, 0, '64', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (109, 2, 0, '53', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (110, 2, 0, '52', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (111, 2, 0, '54', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (112, 2, 0, '45', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (113, 2, 0, '44', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (114, 2, 0, '46', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (115, 2, 0, '47', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (116, 2, 0, '48', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (117, 2, 0, '49', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (118, 2, 0, '51', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (119, 2, 0, '55', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (120, 2, 0, '56', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (121, 2, 0, '57', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (122, 2, 0, '58', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (123, 2, 0, '59', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (124, 2, 0, '60', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (125, 2, 0, '61', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (126, 2, 0, '62', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (127, 2, 0, '63', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (128, 2, 0, '50', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (129, 3, 0, '1', 'l', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (130, 3, 0, '2', 'l', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (131, 3, 0, '8888', 'f', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (132, 3, 0, '1301', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (133, 3, 0, '1802', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (134, 3, 0, '1803', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (135, 3, 0, '1804', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (136, 3, 0, '126', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (137, 3, 0, '132', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (138, 3, 0, '133', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (139, 3, 0, '134', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (140, 3, 0, '77', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (141, 3, 0, '127', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (142, 3, 0, '136', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (143, 3, 0, '137', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (144, 3, 0, '139', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (145, 3, 0, '128', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (146, 3, 0, '140', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (147, 3, 0, '141', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (148, 3, 0, '142', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (149, 3, 0, '129', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (150, 3, 0, '130', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (151, 3, 0, '131', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (152, 3, 0, '76', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (153, 3, 0, '73', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (154, 3, 0, '74', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (155, 3, 0, '2', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (156, 3, 0, '1', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (157, 3, 0, '143', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (158, 3, 0, '144', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (159, 3, 0, '145', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (160, 3, 0, '146', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (161, 3, 0, '163', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (162, 3, 0, '148', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (163, 3, 0, '149', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (164, 3, 0, '150', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (165, 3, 0, '151', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (166, 3, 0, '152', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (167, 3, 0, '153', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (168, 3, 0, '154', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (169, 3, 0, '155', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (170, 3, 0, '156', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (171, 3, 0, '157', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (172, 3, 0, '158', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (173, 3, 0, '159', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (174, 3, 0, '160', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (175, 3, 0, '161', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (176, 3, 0, '162', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (177, 3, 0, '164', 'c', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (178, 3, 0, '1201', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (179, 3, 0, '9999', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (180, 3, 0, '9888', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (181, 3, 0, '9777', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (182, 3, 0, '1202', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (183, 3, 0, '1509', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (184, 3, 0, '1510', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (185, 3, 0, '1511', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (186, 3, 0, '1106', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (187, 3, 0, '1404', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (188, 3, 0, '1405', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (189, 3, 0, '1505', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (190, 3, 0, '1800', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (191, 3, 0, '1801', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (192, 3, 0, '10070', 'a', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (193, 3, 0, '50002', 'a', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (194, 3, 0, '1506', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (195, 3, 0, '1601', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (196, 3, 0, '1603', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (197, 3, 0, '1200', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (198, 3, 0, '1004', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (199, 3, 0, '1005', 's', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (200, 3, 0, '1002', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (201, 3, 0, '0', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (202, 3, 0, '1007', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (203, 3, 0, '1003', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (204, 3, 0, '1604', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (205, 3, 0, '1605', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (206, 3, 0, '1508', 's', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (207, 3, 0, '1', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (208, 3, 0, '2', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (209, 3, 0, '3', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (210, 3, 0, '21', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (211, 3, 0, '22', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (212, 3, 0, '23', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (213, 3, 0, '24', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (214, 3, 0, '4', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (215, 3, 0, '5', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (216, 3, 0, '6', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (217, 3, 0, '7', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (218, 3, 0, '26', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (219, 3, 0, '27', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (220, 3, 0, '8', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (221, 3, 0, '28', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (222, 3, 0, '29', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (223, 3, 0, '9', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (224, 3, 0, '10', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (225, 3, 0, '30', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (226, 3, 0, '31', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (227, 3, 0, '32', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (228, 3, 0, '33', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (229, 3, 0, '11', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (230, 3, 0, '38', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (231, 3, 0, '34', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (232, 3, 0, '39', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (233, 3, 0, '40', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (234, 3, 0, '41', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (235, 3, 0, '42', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (236, 3, 0, '64', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (237, 3, 0, '53', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (238, 3, 0, '52', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (239, 3, 0, '54', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (240, 3, 0, '45', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (241, 3, 0, '44', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (242, 3, 0, '46', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (243, 3, 0, '47', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (244, 3, 0, '48', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (245, 3, 0, '49', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (246, 3, 0, '51', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (247, 3, 0, '55', 'm', '1', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (248, 3, 0, '56', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (249, 3, 0, '57', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (250, 3, 0, '58', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (251, 3, 0, '59', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (252, 3, 0, '60', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (253, 3, 0, '61', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (254, 3, 0, '62', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (255, 3, 0, '63', 'm', '0', '');
INSERT INTO `met_admin_has_permissions` (`id`, `role_id`, `uid`, `aid`, `type`, `access`, `info`) VALUES (256, 3, 0, '50', 'm', '0', '');

#后台语言
INSERT INTO met_lang_admin VALUES (null, '简体中文', '1', '1', 'cn', 'cn', '', 'cn');
INSERT INTO met_lang_admin VALUES (null, 'English', '1', '2', 'en', 'en', '', 'en');

#默认插件
INSERT INTO met_applist VALUES (null, '0', '1.0', 'ueditor', 'index', 'doindex', '百度编辑器', '编辑器', '0', '0', '0','0','','0');
INSERT INTO met_applist VALUES (null,'10070','1.6', 'metconfig_sms', 'index', 'doindex', '短信功能', '短信接口', '0', '0', '0','1','','0');
INSERT INTO met_applist VALUES (null,'50002','1.0', 'metconfig_template', 'temtool', 'dotemlist', '官方模板管理工具', '官方商业模板请在此进行管理操作', '0', '0', '0','1','','0');

#模板
INSERT INTO met_skin_table VALUES (null,'metv75','metv75','MetInfo v7.5.0正式版新推出一套全新精致免费模板！','0','');
