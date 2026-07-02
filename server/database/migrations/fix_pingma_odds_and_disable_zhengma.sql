SET NAMES utf8mb4;

UPDATE `la_play_method`
SET `odds_default` = 7.00,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `is_enabled` = 0,
    `sort` = 98,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'zhengma';

UPDATE `la_play_method`
SET `name` = '平码',
    `code` = 'pingma',
    `odds_default` = 7.00,
    `odds_min` = 1.00,
    `odds_max` = 999.00,
    `prize_config` = NULL,
    `is_enabled` = 1,
    `sort` = 2,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'pingma';

INSERT INTO `la_play_method`
  (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '平码', 'pingma', 7.00, 1.00, 999.00, NULL, 1, 2, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'pingma');

SELECT `id`, `game_id`, `name`, `code`, `odds_default`, `is_enabled`, `sort`
FROM `la_play_method`
WHERE `game_id` = 200 AND `code` IN ('zhengma', 'pingma')
ORDER BY `sort`, `id`;
