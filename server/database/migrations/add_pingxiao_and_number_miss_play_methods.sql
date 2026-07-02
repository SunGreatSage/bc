SET NAMES utf8mb4;

UPDATE `la_play_method`
SET `code` = 'pingxiao',
    `odds_default` = 2.00,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `prize_config` = '{"default_odds":2.00,"option_odds":{"马":1.80}}',
    `is_enabled` = 1,
    `sort` = 13,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '平肖';

UPDATE `la_play_method`
SET `code` = 'wubuzhong',
    `odds_default` = 2.00,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `prize_config` = NULL,
    `is_enabled` = 1,
    `sort` = 14,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '五不中';

UPDATE `la_play_method`
SET `code` = 'liubuzhong',
    `odds_default` = 2.50,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `prize_config` = NULL,
    `is_enabled` = 1,
    `sort` = 15,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '六不中';

UPDATE `la_play_method`
SET `code` = 'qibuzhong',
    `odds_default` = 3.00,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `prize_config` = NULL,
    `is_enabled` = 1,
    `sort` = 16,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '七不中';

UPDATE `la_play_method`
SET `code` = 'babuzhong',
    `odds_default` = 3.50,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `prize_config` = NULL,
    `is_enabled` = 1,
    `sort` = 17,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '八不中';

UPDATE `la_play_method`
SET `code` = 'jiubuzhong',
    `odds_default` = 4.00,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `prize_config` = NULL,
    `is_enabled` = 1,
    `sort` = 18,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '九不中';

UPDATE `la_play_method`
SET `code` = 'shibuzhong',
    `odds_default` = 5.00,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `prize_config` = NULL,
    `is_enabled` = 1,
    `sort` = 19,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '十不中';

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '平肖', 'pingxiao', 2.00, 1.00, 999.00, '{"default_odds":2.00,"option_odds":{"马":1.80}}', 1, 13, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '平肖');

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '五不中', 'wubuzhong', 2.00, 1.00, 999.00, NULL, 1, 14, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '五不中');

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '六不中', 'liubuzhong', 2.50, 1.00, 999.00, NULL, 1, 15, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '六不中');

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '七不中', 'qibuzhong', 3.00, 1.00, 999.00, NULL, 1, 16, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '七不中');

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '八不中', 'babuzhong', 3.50, 1.00, 999.00, NULL, 1, 17, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '八不中');

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '九不中', 'jiubuzhong', 4.00, 1.00, 999.00, NULL, 1, 18, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '九不中');

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '十不中', 'shibuzhong', 5.00, 1.00, 999.00, NULL, 1, 19, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '十不中');

SELECT `id`, `game_id`, `name`, `code`, `odds_default`, `prize_config`, `is_enabled`, `sort`
FROM `la_play_method`
WHERE `game_id` = 200
  AND `code` IN ('pingxiao', 'wubuzhong', 'liubuzhong', 'qibuzhong', 'babuzhong', 'jiubuzhong', 'shibuzhong')
ORDER BY `sort`, `id`;
