-- 最佳控盘计划 - 数据库表结构
-- 数据库名：lhc_oa（根据 config.inc.php 配置）
-- 执行方式：在 phpMyAdmin 或 Navicat 中执行此 SQL

-- ==========================================
-- 1. 主表：x_best_plan
-- ==========================================
CREATE TABLE IF NOT EXISTS `x_best_plan` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `gid` int(11) NOT NULL DEFAULT '300' COMMENT '游戏ID（300=澳门六合彩）',
  `qishu` varchar(20) NOT NULL COMMENT '期数',
  `total_bet_amount` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '总投注额',
  `max_profit` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '最大盈利',
  `min_profit` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '最小盈利',
  `profit_range` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '盈利差（最大-最小）',
  `best_numbers` varchar(100) DEFAULT NULL COMMENT '最佳号码（逗号分隔，如：12,23,34）',
  `worst_numbers` varchar(100) DEFAULT NULL COMMENT '最差号码（逗号分隔）',
  `analyze_time` datetime DEFAULT NULL COMMENT '分析时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gid_qishu` (`gid`,`qishu`),
  KEY `idx_qishu` (`qishu`),
  KEY `idx_analyze_time` (`analyze_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最佳控盘计划-主表';

-- ==========================================
-- 2. 明细表：x_best_plan_detail
-- ==========================================
CREATE TABLE IF NOT EXISTS `x_best_plan_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `plan_id` int(11) NOT NULL COMMENT '计划ID（关联x_best_plan.id）',
  `qishu` varchar(20) NOT NULL COMMENT '期数',
  `gid` int(11) NOT NULL DEFAULT '300' COMMENT '游戏ID',
  `number` tinyint(4) NOT NULL COMMENT '号码（1-49）',
  `total_prize` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '该号码开出时的总派奖额',
  `profit` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '该号码开出时的平台盈利',
  `win_count` int(11) NOT NULL DEFAULT '0' COMMENT '中奖注单数',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_plan_id` (`plan_id`),
  KEY `idx_qishu` (`qishu`),
  KEY `idx_number` (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最佳控盘计划-明细表（每期49条记录）';

-- ==========================================
-- 3. 配置表：x_best_plan_config
-- ==========================================
CREATE TABLE IF NOT EXISTS `x_best_plan_config` (
  `id` int(11) NOT NULL DEFAULT '1' COMMENT '主键ID（固定为1）',
  `enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用（1=启用，0=禁用）',
  `analyze_time_before` int(11) NOT NULL DEFAULT '5' COMMENT '开奖前多少分钟开始分析',
  `analyze_depth` varchar(20) NOT NULL DEFAULT 'full' COMMENT '分析深度（full=全量，hot=热门）',
  `auto_analyze` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否自动分析（1=开启，0=关闭）',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='最佳控盘计划-配置表';

-- ==========================================
-- 4. 插入默认配置数据
-- ==========================================
-- 先删除可能存在的旧数据
DELETE FROM `x_best_plan_config` WHERE id=1;

-- 插入新数据
INSERT INTO `x_best_plan_config` (`id`, `enabled`, `analyze_time_before`, `analyze_depth`, `auto_analyze`, `created_at`, `updated_at`)
VALUES (1, 1, 5, 'full', 1, NOW(), NOW());

-- ==========================================
-- 执行完成！
-- ==========================================
-- 说明：
-- 1. x_best_plan：存储每期的分析结果（总投注额、最大/最小盈利等）
-- 2. x_best_plan_detail：存储每期49个号码的详细盈利数据
-- 3. x_best_plan_config：存储系统配置（启用状态、分析时间等）
--
-- 使用方法：
-- 1. 登录后台 → 个人管理 → 最佳控盘计划
-- 2. 点击"立即分析"按钮，系统会自动计算当前未开奖期次的最佳开奖号码
-- 3. 或配置定时任务，每分钟自动检查并分析
