/*
SQLyog Ultimate v8.32 
MySQL - 5.7.21 : Database - admin
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`admin` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `admin`;

/*Table structure for table `auth_group` */

DROP TABLE IF EXISTS `auth_group`;

CREATE TABLE `auth_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL,
  `nickname` char(20) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` text COMMENT '规则id',
  `home_page` char(80) NOT NULL COMMENT '首页',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户组表';

/*Data for the table `auth_group` */

insert  into `auth_group`(`id`,`title`,`nickname`,`status`,`rules`,`home_page`) values (1,'超管','ADMIN',1,'18,19,15,10,17,1,3,12,4,7,11,6,2,14,9,5,16','index/manageruser/index'),(2,'运营主管','OM',1,'18,19,7,11','index/manageruser/info'),(3,'运营','OPS',1,'18,19,7,11','index/manageruser/info'),(4,'财务','FD',1,'18,19,7,11','index/manageruser/info');

/*Table structure for table `auth_rule` */

DROP TABLE IF EXISTS `auth_rule`;

CREATE TABLE `auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parentId` int(11) unsigned DEFAULT '0' COMMENT '父级id',
  `href` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) NOT NULL COMMENT '规则中文名称',
  `icon` varchar(40) NOT NULL DEFAULT '' COMMENT '图标',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `is_menu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型：1：菜单',
  `sort` int(11) unsigned NOT NULL COMMENT '排序',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='规则表';

/*Data for the table `auth_rule` */

insert  into `auth_rule`(`id`,`parentId`,`href`,`title`,`icon`,`status`,`is_menu`,`sort`,`create_time`,`update_time`) values (1,0,'','权限控制','layui-icon-release',1,1,2,'2020-10-21 09:35:37','2020-10-23 13:45:47'),(2,1,'index/groupuser/index','角色管理','layui-icon-group-smile',1,1,1,'2020-10-21 09:37:24','2020-10-23 14:29:00'),(3,1,'index/authority/index','权限管理','layui-icon-component',1,1,2,'2020-10-21 09:38:36','2020-10-22 13:51:41'),(4,1,'index/manageruser/index','管理员列表','layui-icon-user',1,1,3,'2020-10-21 09:39:00','2020-10-23 15:38:00'),(5,2,'index/groupuser/setgroup','设置角色','',1,0,0,'2020-10-22 13:48:14','2020-10-23 14:28:55'),(6,2,'index/groupuser/groups','角色列表','',1,0,1,'2020-10-23 11:01:56','2020-10-23 14:29:03'),(7,0,'index/index/index','网站入口（必加）','',1,0,1,'2020-10-23 13:46:38','2020-10-23 16:20:46'),(9,3,'index/authority/setrule','设置节点','',1,0,1,'2020-10-23 13:50:14','2020-10-23 15:37:56'),(10,3,'index/authority/delrule','删除节点','',1,0,2,'2020-10-23 13:51:05','2020-10-23 13:51:05'),(11,7,'index/index/getmenu','获取菜单','',1,0,2,'2020-10-23 13:53:01','2020-10-23 13:53:01'),(12,4,'index/manageruser/users','用户列表','',1,0,1,'2020-10-23 15:53:53','2020-10-23 15:53:53'),(14,4,'index/manageruser/setuser','设置用户','',1,0,2,'2020-10-24 10:54:31','2020-10-24 10:54:31'),(15,4,'index/manageruser/deluser','删除用户','',1,0,3,'2020-10-24 14:37:11','2020-10-24 14:37:11'),(16,4,'index/manageruser/resetpsw','重置密码','',1,0,4,'2020-10-24 16:24:11','2020-10-24 16:24:11'),(17,2,'index/groupuser/delgroup','删除角色','',1,0,2,'2020-10-26 09:06:02','2020-10-26 09:06:02'),(18,7,'index/manageruser/upinfo','个人资料','',1,0,3,'2020-10-26 14:00:09','2020-10-26 14:43:41'),(19,7,'index/manageruser/changepsw','修改密码','',1,0,4,'2020-10-26 14:01:43','2020-10-26 15:14:09');

/*Table structure for table `manager_user` */

DROP TABLE IF EXISTS `manager_user`;

CREATE TABLE `manager_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `parent_id` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `group_id` smallint(5) unsigned NOT NULL COMMENT '角色ID',
  `account` char(32) NOT NULL COMMENT '账号',
  `nickname` varchar(30) NOT NULL COMMENT '真实姓名',
  `password` char(32) NOT NULL COMMENT '密码',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-正常 0-离职 离职状态 ',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='管理员账户表';

/*Data for the table `manager_user` */

insert  into `manager_user`(`id`,`parent_id`,`group_id`,`account`,`nickname`,`password`,`status`,`create_time`,`update_time`) values (86,0,1,'admin','阿呆','e10adc3949ba59abbe56e057f20f883e',1,'2020-10-20 11:36:22','2020-10-26 16:12:20'),(87,0,2,'zgan','主管阿牛','745404feaba9fb037e01b4a91c6ddbeb',1,'2020-10-23 15:12:22','2020-10-26 16:12:53'),(88,87,3,'yyal','运营阿龙','e10adc3949ba59abbe56e057f20f883e',1,'2020-10-24 09:23:47','2020-10-26 14:26:52'),(89,0,2,'zgaf','主管阿峰','745404feaba9fb037e01b4a91c6ddbeb',1,'2020-10-24 09:24:25','2020-10-26 16:38:13');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
