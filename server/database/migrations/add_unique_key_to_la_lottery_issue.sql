-- ========================================
-- BC 彩票系统 - 期号唯一性约束
-- 目标：防止同一 (game_id, plate_code, issue) 重复写入导致重复开奖/结算
-- MySQL: 5.7+
-- 创建时间: 2025-12-16
-- ========================================

ALTER TABLE `la_lottery_issue`
  ADD UNIQUE KEY `uk_game_plate_issue` (`game_id`, `plate_code`, `issue`) USING BTREE;

