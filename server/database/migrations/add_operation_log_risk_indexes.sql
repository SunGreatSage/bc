-- ========================================
-- BC 彩票系统 - 操作日志风控查询索引
-- 目标：加速风控日志按 action、create_time 查询
-- MySQL: 5.7+
-- 创建时间: 2026-07-04
-- ========================================

SET NAMES utf8mb4;

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
