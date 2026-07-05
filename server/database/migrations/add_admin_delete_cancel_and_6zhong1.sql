SET NAMES utf8mb4;

SET @schema_name = DATABASE();

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_betting_record` ADD COLUMN `admin_deleted_at` INT UNSIGNED NULL DEFAULT NULL COMMENT ''后台删除时间'' AFTER `updated_at`',
    'SELECT ''la_betting_record.admin_deleted_at exists'''
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_betting_record'
    AND COLUMN_NAME = 'admin_deleted_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_betting_record` ADD COLUMN `admin_deleted_by` INT UNSIGNED NULL DEFAULT NULL COMMENT ''后台删除管理员ID'' AFTER `admin_deleted_at`',
    'SELECT ''la_betting_record.admin_deleted_by exists'''
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_betting_record'
    AND COLUMN_NAME = 'admin_deleted_by'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_betting_record` ADD KEY `idx_admin_deleted_at` (`admin_deleted_at`)',
    'SELECT ''la_betting_record.idx_admin_deleted_at exists'''
  )
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_betting_record'
    AND INDEX_NAME = 'idx_admin_deleted_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_best_plan_history` ADD COLUMN `admin_deleted_at` INT UNSIGNED NULL DEFAULT NULL COMMENT ''后台删除时间'' AFTER `actual_profit`',
    'SELECT ''la_best_plan_history.admin_deleted_at exists'''
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_best_plan_history'
    AND COLUMN_NAME = 'admin_deleted_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_best_plan_history` ADD COLUMN `admin_deleted_by` INT UNSIGNED NULL DEFAULT NULL COMMENT ''后台删除管理员ID'' AFTER `admin_deleted_at`',
    'SELECT ''la_best_plan_history.admin_deleted_by exists'''
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_best_plan_history'
    AND COLUMN_NAME = 'admin_deleted_by'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_best_plan_history` ADD KEY `idx_admin_deleted_at` (`admin_deleted_at`)',
    'SELECT ''la_best_plan_history.idx_admin_deleted_at exists'''
  )
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_best_plan_history'
    AND INDEX_NAME = 'idx_admin_deleted_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_lottery_issue` ADD COLUMN `admin_hidden_at` INT UNSIGNED NULL DEFAULT NULL COMMENT ''后台隐藏时间'' AFTER `updated_at`',
    'SELECT ''la_lottery_issue.admin_hidden_at exists'''
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_lottery_issue'
    AND COLUMN_NAME = 'admin_hidden_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_lottery_issue` ADD COLUMN `admin_hidden_by` INT UNSIGNED NULL DEFAULT NULL COMMENT ''后台隐藏管理员ID'' AFTER `admin_hidden_at`',
    'SELECT ''la_lottery_issue.admin_hidden_by exists'''
  )
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_lottery_issue'
    AND COLUMN_NAME = 'admin_hidden_by'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE `la_lottery_issue` ADD KEY `idx_admin_hidden_at` (`admin_hidden_at`)',
    'SELECT ''la_lottery_issue.idx_admin_hidden_at exists'''
  )
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = @schema_name
    AND TABLE_NAME = 'la_lottery_issue'
    AND INDEX_NAME = 'idx_admin_hidden_at'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `la_play_method`
SET `code` = 'liuzhongyi',
    `odds_default` = IF(`odds_default` > 0, `odds_default`, 2.11),
    `odds_min` = IF(`odds_min` > 0, `odds_min`, 1.00),
    `odds_max` = IF(`odds_max` > 0, `odds_max`, 999.00),
    `is_enabled` = 1,
    `sort` = 13,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '6中1';

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '6中1', 'liuzhongyi', 2.11, 1.00, 999.00, NULL, 1, 13, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (
  SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '6中1'
);

SELECT `id`, `game_id`, `name`, `code`, `odds_default`, `is_enabled`, `sort`
FROM `la_play_method`
WHERE `game_id` = 200
  AND (`name` = '6中1' OR `code` = 'liuzhongyi');
