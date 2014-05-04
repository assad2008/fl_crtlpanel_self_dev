SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `pm`
--

DELIMITER $$
--
-- 存储过程
--
$$

$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_adminmsg`
--

CREATE TABLE IF NOT EXISTS `iosadm_adminmsg` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `adduser` varchar(30) NOT NULL,
  `addtime` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_admin_action`
--

CREATE TABLE IF NOT EXISTS `iosadm_admin_action` (
  `action_id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(4) unsigned NOT NULL DEFAULT '0',
  `action_code` tinytext NOT NULL,
  `action_name` varchar(30) NOT NULL,
  `relevance` varchar(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `sort` int(11) NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`action_id`),
  KEY `parent_id` (`parent_id`),
  KEY `parent_id_2` (`parent_id`,`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_admin_user`
--

CREATE TABLE IF NOT EXISTS `iosadm_admin_user` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `truename` varchar(20) DEFAULT NULL,
  `bossname` varchar(30) DEFAULT NULL,
  `email` varchar(60) DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `salt` varchar(8) DEFAULT NULL,
  `level` int(2) DEFAULT '2',
  `is_super` int(1) DEFAULT '0',
  `add_time` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) DEFAULT NULL,
  `action_list` text,
  `rights` text,
  `nav_list` text,
  `lang_type` varchar(50) DEFAULT NULL,
  `agency_id` smallint(5) unsigned DEFAULT NULL,
  `suppliers_id` smallint(5) unsigned DEFAULT '0',
  `todolist` longtext,
  `role_id` smallint(5) DEFAULT NULL,
  `remark` text,
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `iosadm_admin_user`
--

INSERT INTO `iosadm_admin_user` (`user_id`, `user_name`, `truename`, `bossname`, `email`, `password`, `salt`, `level`, `is_super`, `add_time`, `last_login`, `last_ip`, `action_list`, `rights`, `nav_list`, `lang_type`, `agency_id`, `suppliers_id`, `todolist`, `role_id`, `remark`) VALUES
(1, 'wangjiang', '王江', 'wangjiang', 'wangjiang@feiliu.com', '', NULL, 2, 0, 1399175329, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_config`
--

CREATE TABLE IF NOT EXISTS `iosadm_config` (
  `fl_name` varchar(30) NOT NULL DEFAULT '',
  `fl_value` text NOT NULL,
  PRIMARY KEY (`fl_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_loginhistory`
--

CREATE TABLE IF NOT EXISTS `iosadm_loginhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `logintime` int(10) DEFAULT '0',
  `loginok` tinyint(1) DEFAULT NULL,
  `errmsg` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `iosadm_loginhistory`
--

INSERT INTO `iosadm_loginhistory` (`id`, `username`, `ip`, `logintime`, `loginok`, `errmsg`) VALUES
(1, 'wangjiang', '110.96.32.160', 1399175329, 1, ''),
(2, 'wangjiang', '110.96.32.160', 1399175392, 1, ''),
(3, 'wangjiang', '110.96.32.160', 1399177783, 1, ''),
(4, 'wangjiang', '110.96.32.160', 1399180005, 1, ''),
(5, 'wangjiang', '110.96.32.160', 1399181101, 1, ''),
(6, 'wangjiang', '110.96.32.160', 1399195605, 1, ''),
(7, 'wangjiang', '110.96.32.160', 1399195662, 1, ''),
(8, 'wangjiang', '110.96.32.160', 1399196174, 1, ''),
(9, 'wangjiang', '110.96.32.160', 1399196300, 1, ''),
(10, 'wangjiang', '110.96.32.160', 1399196709, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_menu`
--

CREATE TABLE IF NOT EXISTS `iosadm_menu` (
  `menu_id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '3',
  `menu_name` varchar(30) DEFAULT NULL,
  `act_url` varchar(50) NOT NULL,
  `actioncode` varchar(50) DEFAULT NULL,
  `is_show` tinyint(1) DEFAULT '1',
  `addtime` int(10) DEFAULT '0',
  `adduser` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `sort` int(3) NOT NULL DEFAULT '255',
  PRIMARY KEY (`menu_id`),
  KEY `is_show` (`is_show`),
  KEY `parent_id` (`parent_id`,`level`,`is_show`,`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=126 ;

--
-- 转存表中的数据 `iosadm_menu`
--

INSERT INTO `iosadm_menu` (`menu_id`, `parent_id`, `level`, `menu_name`, `act_url`, `actioncode`, `is_show`, `addtime`, `adduser`, `status`, `sort`) VALUES
(23, 20, 3, '权限管理', '?c=menu&a=rightlist', 'menu_rightlist', 1, 1374743005, 'wangyuanyuan', 1, 10),
(22, 20, 3, '菜单管理', '?c=menu&a=menulist', 'menu_menulist', 1, 1374742924, 'wangyuanyuan', 1, 254),
(21, 20, 3, '会员管理', '?c=member&a=member_list', 'member_member_list', 1, 1374663437, 'wangyuanyuan', 1, 255),
(20, 0, 1, '系统管理', '', '', 1, 1374663345, 'wangyuanyuan', 1, 255);

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_oplog`
--

CREATE TABLE IF NOT EXISTS `iosadm_oplog` (
  `oplid` int(10) NOT NULL AUTO_INCREMENT,
  `aday` varchar(8) DEFAULT NULL,
  `username` varchar(40) DEFAULT NULL,
  `ctrl` varchar(20) DEFAULT NULL,
  `act` varchar(20) DEFAULT NULL,
  `query` varchar(100) DEFAULT NULL,
  `timestamp` int(10) DEFAULT '0',
  PRIMARY KEY (`oplid`),
  KEY `aday` (`aday`,`username`),
  KEY `aday_2` (`aday`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_role`
--

CREATE TABLE IF NOT EXISTS `iosadm_role` (
  `role_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(60) NOT NULL DEFAULT '',
  `action_list` text NOT NULL,
  `role_describe` text,
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`role_id`),
  KEY `user_name` (`role_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- 表的结构 `iosadm_usermsg`
--

CREATE TABLE IF NOT EXISTS `iosadm_usermsg` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) NOT NULL,
  `touser` varchar(30) NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `addtime` int(10) DEFAULT '0',
  `status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `touser` (`touser`,`is_read`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
