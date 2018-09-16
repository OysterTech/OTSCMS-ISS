CREATE DATABASE IF NOT EXISTS `swim` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `swim`;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `user_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `level` int(1) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` varchar(19) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `admin` (`id`, `user_name`, `password`, `salt`, `level`, `create_time`, `last_login`) VALUES
(1, 'super', 'ddf6b9e6274760d9b30db4b4fe7c0ae243b4dfa8', 'wFNxYdnE', 1, '2018-08-12 09:29:22', '2018-09-16 15:54:52');

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0000-00-00',
  `end_date` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0000-00-00',
  `praise` int(11) NOT NULL DEFAULT '0',
  `extra_json` text COLLATE utf8_unicode_ci NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `item` (
  `id` int(11) NOT NULL,
  `games_id` int(11) NOT NULL COMMENT '比赛ID',
  `scene` int(11) NOT NULL COMMENT '场次',
  `order_index` int(11) NOT NULL COMMENT '项次',
  `sex` varchar(2) COLLATE utf8_unicode_ci NOT NULL COMMENT '性别',
  `group_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '组别名称',
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '项目名称',
  `total_group` int(11) NOT NULL COMMENT '总组数',
  `total_ath` int(11) NOT NULL COMMENT '总人数',
  `is_allround` int(1) NOT NULL DEFAULT '0' COMMENT '是否为全能项目',
  `is_calling` int(1) NOT NULL DEFAULT '0' COMMENT '是否正在检录',
  `is_delete` int(1) NOT NULL DEFAULT '0',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='项目表';

CREATE TABLE `score` (
  `id` int(11) NOT NULL,
  `item_id` int(2) NOT NULL COMMENT '项目ID',
  `run_group` int(2) NOT NULL COMMENT '组次',
  `runway` int(1) NOT NULL COMMENT '道次',
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL COMMENT '运动员姓名',
  `team_id` int(11) NOT NULL COMMENT '队伍ID',
  `score` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '比赛成绩',
  `point` decimal(5,2) DEFAULT NULL COMMENT '得分',
  `allround_point` decimal(5,2) DEFAULT NULL COMMENT '全能成绩分',
  `rank` int(2) DEFAULT NULL COMMENT '总排名',
  `remark` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '备注',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `create_user` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '管理员',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='分组成绩表';

CREATE TABLE `team` (
  `id` int(11) NOT NULL,
  `games_id` int(11) NOT NULL COMMENT '比赛ID',
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '队伍全称',
  `short_name` varchar(6) COLLATE utf8_unicode_ci NOT NULL COMMENT '队伍简称',
  `bouns` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '加分',
  `deduction` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '扣分',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='队伍表';

ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `item`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `score`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `team`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
