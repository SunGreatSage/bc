-- BC 彩票系统 - 操作日志大字段扩容
-- 目标：避免分析接口返回过大时写入 la_operation_log.result/params 失败
-- MySQL: 5.7+
-- 创建时间: 2026-07-04

SET NAMES utf8mb4;

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
