-- ========================================
-- BC 彩票系统 - 开奖两阶段字段补齐
-- 目标：封盘后写入 planned_result（不公开、不结算），到 draw_time 再发布 result 并结算
-- MySQL: 5.7+
-- 创建时间: 2025-12-16
-- ========================================

ALTER TABLE `la_lottery_issue`
  ADD COLUMN `planned_result` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '封盘后预生成开奖号码(未公开)' AFTER `result`,
  ADD COLUMN `planned_at` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预生成时间' AFTER `planned_result`,
  ADD COLUMN `planned_source` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=auto,1=admin' AFTER `planned_at`,
  ADD COLUMN `planned_operator_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预生成操作员ID(0=系统)' AFTER `planned_source`;

