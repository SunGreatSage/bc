SET NAMES utf8mb4;

UPDATE `la_play_method`
SET `code` = 'erzhonger', `odds_default` = 60.00, `odds_min` = 1.00, `odds_max` = 999.00, `is_enabled` = 1, `sort` = 10, `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '二中二';

UPDATE `la_play_method`
SET `code` = 'sanzhonger', `odds_default` = 20.00, `odds_min` = 1.00, `odds_max` = 999.00, `is_enabled` = 1, `sort` = 11, `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '三中二';

UPDATE `la_play_method`
SET `code` = 'sanzhongsan', `odds_default` = 500.00, `odds_min` = 1.00, `odds_max` = 999.00, `is_enabled` = 1, `sort` = 12, `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `name` = '三中三';

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '二中二', 'erzhonger', 60.00, 1.00, 999.00, NULL, 1, 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '二中二');

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '三中二', 'sanzhonger', 20.00, 1.00, 999.00, NULL, 1, 11, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '三中二');

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '三中三', 'sanzhongsan', 500.00, 1.00, 999.00, NULL, 1, 12, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `name` = '三中三');

SELECT `id`, `game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `is_enabled`, `sort`
FROM `la_play_method`
WHERE `game_id` = 200
  AND `code` IN ('erzhonger', 'sanzhonger', 'sanzhongsan')
ORDER BY `sort`, `id`;
