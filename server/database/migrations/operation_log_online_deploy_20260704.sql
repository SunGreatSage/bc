-- BC 彩票系统 - 操作日志风控性能与安全上线汇总脚本
-- 包含：
-- 1. params/result 扩容为 MEDIUMTEXT
-- 2. action/create_time 风控日志查询索引
-- 3. issue/plate_code 生成列与组合索引
-- MySQL: 5.7+
-- 创建时间: 2026-07-04
-- 说明：本脚本可重复执行，已存在的字段/索引会自动跳过

SET NAMES utf8mb4;

-- 1. 扩容操作日志请求参数与响应结果，避免大响应写日志失败
SET @params_column_type := (
  SELECT LOWER(COLUMN_TYPE)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND column_name = 'params'
);

SET @sql := IF(
  @params_column_type IS NULL,
  'SELECT ''la_operation_log.params column not found'' AS message',
  IF(
    @params_column_type <> 'mediumtext',
    'ALTER TABLE `la_operation_log` MODIFY COLUMN `params` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT ''请求数据''',
    'SELECT ''la_operation_log.params already mediumtext'' AS message'
  )
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @result_column_type := (
  SELECT LOWER(COLUMN_TYPE)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND column_name = 'result'
);

SET @sql := IF(
  @result_column_type IS NULL,
  'SELECT ''la_operation_log.result column not found'' AS message',
  IF(
    @result_column_type <> 'mediumtext',
    'ALTER TABLE `la_operation_log` MODIFY COLUMN `result` MEDIUMTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT ''请求结果''',
    'SELECT ''la_operation_log.result already mediumtext'' AS message'
  )
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. action/create_time 基础索引
SET @index_exists := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND index_name = 'idx_operation_log_action_create_time'
);

SET @sql := IF(
  @index_exists = 0,
  'ALTER TABLE `la_operation_log` ADD INDEX `idx_operation_log_action_create_time` (`action`, `create_time`)',
  'SELECT ''idx_operation_log_action_create_time already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists := (
  SELECT COUNT(1)
  FROM information_schema.statistics
  WHERE table_schema = DATABASE()
    AND table_name = 'la_operation_log'
    AND index_name = 'idx_operation_log_create_time'
);

SET @sql := IF(
  @index_exists = 0,
  'ALTER TABLE `la_operation_log` ADD INDEX `idx_operation_log_create_time` (`create_time`)',
  'SELECT ''idx_operation_log_create_time already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. 从 params JSON 中提取 issue/qishu 与 plate_code 为可索引生成列
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
