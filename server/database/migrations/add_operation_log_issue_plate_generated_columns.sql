-- BC 彩票系统 - 操作日志期号/盘口索引列
-- 目标：避免风控日志按期号、盘口筛选时扫描 params JSON 文本
-- MySQL: 5.7+
-- 创建时间: 2026-07-04

SET NAMES utf8mb4;

SET @column_exists := (
  SELECT COUNT(1)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND column_name = 'issue'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `la_operation_log` ADD COLUMN `issue` VARCHAR(32) GENERATED ALWAYS AS (CASE WHEN JSON_VALID(`params`) THEN COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(`params`, ''$.issue'')), ''''), NULLIF(JSON_UNQUOTE(JSON_EXTRACT(`params`, ''$.qishu'')), '''')) ELSE NULL END) STORED COMMENT ''日志参数期号(issue/qishu)'' AFTER `params`',
  'SELECT ''la_operation_log.issue already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @column_exists := (
  SELECT COUNT(1)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND column_name = 'plate_code'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `la_operation_log` ADD COLUMN `plate_code` VARCHAR(16) GENERATED ALWAYS AS (CASE WHEN JSON_VALID(`params`) THEN NULLIF(JSON_UNQUOTE(JSON_EXTRACT(`params`, ''$.plate_code'')), '''') ELSE NULL END) STORED COMMENT ''日志参数盘口'' AFTER `issue`',
  'SELECT ''la_operation_log.plate_code already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND index_name = 'idx_operation_log_issue_action_plate_time'
);

SET @sql := IF(
  @index_exists = 0,
  'ALTER TABLE `la_operation_log` ADD INDEX `idx_operation_log_issue_action_plate_time` (`issue`, `action`, `plate_code`, `create_time`, `id`)',
  'SELECT ''idx_operation_log_issue_action_plate_time already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND index_name = 'idx_operation_log_plate_action_time'
);

SET @sql := IF(
  @index_exists = 0,
  'ALTER TABLE `la_operation_log` ADD INDEX `idx_operation_log_plate_action_time` (`plate_code`, `action`, `create_time`, `id`)',
  'SELECT ''idx_operation_log_plate_action_time already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND index_name = 'idx_operation_log_action_plate_time'
);

SET @sql := IF(
  @index_exists = 0,
  'ALTER TABLE `la_operation_log` ADD INDEX `idx_operation_log_action_plate_time` (`action`, `plate_code`, `create_time`, `id`)',
  'SELECT ''idx_operation_log_action_plate_time already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
