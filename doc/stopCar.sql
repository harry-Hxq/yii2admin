

--
-- 表的结构 `yii2_user`
--
CREATE TABLE IF NOT EXISTS `yii2_user` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '用户名',
  `password` char(60) NOT NULL COMMENT '密码',
  `salt` char(32) NOT NULL COMMENT '密码干扰字符',
  `email` char(32) DEFAULT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `tuid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '推荐人uid',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '头像路径',
  `score` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '当前积分',
  `score_all` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '总积分',
  `allowance` int(5) NOT NULL COMMENT 'api接口调用速率限制',
  `allowance_updated_at` int(10) NOT NULL COMMENT 'api接口调用速率限制',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态 1正常 0禁用'
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='用户表';

ALTER TABLE yii2_user ADD COLUMN plate_num VARCHAR(50) NOT NULL DEFAULT '' COMMENT '车牌号';
ALTER TABLE yii2_user ADD COLUMN openid VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'openid';
ALTER TABLE yii2_user ADD COLUMN subsribe TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否关注 1关注 2未关注';
ALTER TABLE yii2_user ADD COLUMN stopCarStatus TINYINT(1) NOT NULL DEFAULT 1 COMMENT '用户停车状态 1未停车 2停车中';

--
-- 行程表的结构 `yii2_route`
--
CREATE TABLE IF NOT EXISTS `yii2_route` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` char(32) DEFAULT NULL COMMENT '标题',
  `start_time` INT(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` INT(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `latitude` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置纬度',
  `longitude` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置经度',
  `precision` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置精度',
  `remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='行程表';

--
-- 用户停车记录 `yii2_user_stop_log`
--
CREATE TABLE IF NOT EXISTS `yii2_user_stop_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `latitude` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置纬度',
  `longitude` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置经度',
  `precision` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置精度',
  `remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='用户停车记录';
ALTER TABLE yii2_user_stop_log ADD COLUMN is_tip TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否提醒过用户 1未提醒 2已提醒';

--
-- 提醒用户记录 `yii2_user_tip`
--
CREATE TABLE IF NOT EXISTS `yii2_user_tip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `route_id` int(10) DEFAULT NULL COMMENT '路线id',
  `remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='提醒用户记录';


--
-- 摩托车管制点 `yii2_moto`
--
CREATE TABLE IF NOT EXISTS `yii2_moto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` char(32) DEFAULT NULL COMMENT '标题',
  `start_time` INT(11) NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` INT(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  `latitude` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置纬度',
  `longitude` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置经度',
  `precision` DECIMAL(10,6) NOT NULL DEFAULT 0.000000 COMMENT '地理位置精度',
  `remark` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='行程表';


--
-- 用户充值记录 `yii2_user_recharge`
--
CREATE TABLE IF NOT EXISTS `yii2_user_recharge` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `openid` VARCHAR(255) DEFAULT NULL DEFAULT '' COMMENT 'openid',
  `wx_order_id` VARCHAR(255) DEFAULT NULL DEFAULT '' COMMENT '微信orderId',
  `order_id` VARCHAR(255) DEFAULT NULL DEFAULT '' COMMENT '本地orderId',
  `out_trade_no` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '商户订单号',
  `wx_order_info_prepare` VARCHAR(500) NOT NULL DEFAULT '' COMMENT '订单信息',
  `total_fee` INT(11) NOT NULL DEFAULT 0 COMMENT '总金额',
  `spbill_create_ip` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '终端IP',
  `time_start` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '交易起始时间',
  `time_expire` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '交易结束时间',
  `notify_url` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '通知地址',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `uid_index`(`uid`),
  KEY `uid_openid`(`openid`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='用户充值记录';
ALTER TABLE yii2_user_recharge add COLUMN order_id VARCHAR(255) DEFAULT 0 NOT NULL COMMENT '本地orderid';
ALTER TABLE yii2_user_recharge add COLUMN wx_order_id VARCHAR(255) DEFAULT 0 NOT NULL COMMENT '微信orderid';
ALTER TABLE yii2_user_recharge add COLUMN wx_order_info_prepare VARCHAR(500) DEFAULT 0 NOT NULL COMMENT '订单信息';