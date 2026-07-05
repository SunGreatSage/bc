-- 新增扩展玩法：2-5连肖、6肖中特、特码大小单双、家禽野兽、合单合双、波色、半波
-- 赔率均为包含本金的赔率。

UPDATE `la_play_method`
SET `name` = '2连肖',
    `odds_default` = 4.96,
    `odds_min` = 4.00,
    `odds_max` = 4.96,
    `prize_config` = '{"select_count":2,"default_odds":4.96,"with_horse_odds":4.00}',
    `is_enabled` = 1,
    `sort` = 20,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'lianxiao2';

UPDATE `la_play_method`
SET `name` = '3连肖',
    `odds_default` = 12.70,
    `odds_min` = 10.00,
    `odds_max` = 12.70,
    `prize_config` = '{"select_count":3,"default_odds":12.70,"with_horse_odds":10.00}',
    `is_enabled` = 1,
    `sort` = 21,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'lianxiao3';

UPDATE `la_play_method`
SET `name` = '4连肖',
    `odds_default` = 36.70,
    `odds_min` = 30.00,
    `odds_max` = 36.70,
    `prize_config` = '{"select_count":4,"default_odds":36.70,"with_horse_odds":30.00}',
    `is_enabled` = 1,
    `sort` = 22,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'lianxiao4';

UPDATE `la_play_method`
SET `name` = '5连肖',
    `odds_default` = 128.00,
    `odds_min` = 97.90,
    `odds_max` = 128.00,
    `prize_config` = '{"select_count":5,"default_odds":128.00,"with_horse_odds":97.90}',
    `is_enabled` = 1,
    `sort` = 23,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'lianxiao5';

UPDATE `la_play_method`
SET `name` = '6肖中特',
    `odds_default` = 1.95,
    `odds_min` = 1.95,
    `odds_max` = 1.95,
    `prize_config` = '{"select_count":6,"default_odds":1.95,"judge_scope":"special","draw_on_49":true}',
    `is_enabled` = 1,
    `sort` = 9,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'liuxiao';

UPDATE `la_play_method`
SET `name` = '特码大小单双',
    `odds_default` = 1.95,
    `odds_min` = 1.95,
    `odds_max` = 1.95,
    `prize_config` = '{"option_odds":{"单":1.95,"双":1.95,"大":1.95,"小":1.95},"draw_on_49":true}',
    `is_enabled` = 1,
    `sort` = 24,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'tema_daxiao_danshuang';

UPDATE `la_play_method`
SET `name` = '家禽野兽',
    `odds_default` = 1.95,
    `odds_min` = 1.95,
    `odds_max` = 1.95,
    `prize_config` = '{"option_odds":{"家禽":1.95,"野兽":1.95},"draw_on_49":true}',
    `is_enabled` = 1,
    `sort` = 25,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'jiaqin_yeshou';

UPDATE `la_play_method`
SET `name` = '合单合双',
    `odds_default` = 1.95,
    `odds_min` = 1.95,
    `odds_max` = 1.95,
    `prize_config` = '{"option_odds":{"合单":1.95,"合双":1.95},"draw_on_49":true}',
    `is_enabled` = 1,
    `sort` = 26,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'hedan_heshuang';

UPDATE `la_play_method`
SET `name` = '波色',
    `odds_default` = 2.60,
    `odds_min` = 2.60,
    `odds_max` = 2.65,
    `prize_config` = '{"option_odds":{"红波":2.60,"蓝波":2.65,"兰波":2.65,"绿波":2.65},"draw_on_49":false}',
    `is_enabled` = 1,
    `sort` = 27,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'bose';

UPDATE `la_play_method`
SET `name` = '半波',
    `odds_default` = 5.00,
    `odds_min` = 5.00,
    `odds_max` = 6.30,
    `prize_config` = '{"option_odds":{"绿双":6.30,"红单":5.40,"蓝单":5.40,"兰单":5.40,"蓝双":5.40,"兰双":5.40,"绿单":5.00,"红双":5.00},"draw_on_49":false}',
    `is_enabled` = 1,
    `sort` = 28,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'banbo';

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '2连肖', 'lianxiao2', 4.96, 4.00, 4.96, '{"select_count":2,"default_odds":4.96,"with_horse_odds":4.00}', 1, 20, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'lianxiao2');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '3连肖', 'lianxiao3', 12.70, 10.00, 12.70, '{"select_count":3,"default_odds":12.70,"with_horse_odds":10.00}', 1, 21, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'lianxiao3');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '4连肖', 'lianxiao4', 36.70, 30.00, 36.70, '{"select_count":4,"default_odds":36.70,"with_horse_odds":30.00}', 1, 22, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'lianxiao4');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '5连肖', 'lianxiao5', 128.00, 97.90, 128.00, '{"select_count":5,"default_odds":128.00,"with_horse_odds":97.90}', 1, 23, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'lianxiao5');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '6肖中特', 'liuxiao', 1.95, 1.95, 1.95, '{"select_count":6,"default_odds":1.95,"judge_scope":"special","draw_on_49":true}', 1, 9, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'liuxiao');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '特码大小单双', 'tema_daxiao_danshuang', 1.95, 1.95, 1.95, '{"option_odds":{"单":1.95,"双":1.95,"大":1.95,"小":1.95},"draw_on_49":true}', 1, 24, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'tema_daxiao_danshuang');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '家禽野兽', 'jiaqin_yeshou', 1.95, 1.95, 1.95, '{"option_odds":{"家禽":1.95,"野兽":1.95},"draw_on_49":true}', 1, 25, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'jiaqin_yeshou');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '合单合双', 'hedan_heshuang', 1.95, 1.95, 1.95, '{"option_odds":{"合单":1.95,"合双":1.95},"draw_on_49":true}', 1, 26, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'hedan_heshuang');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '波色', 'bose', 2.60, 2.60, 2.65, '{"option_odds":{"红波":2.60,"蓝波":2.65,"兰波":2.65,"绿波":2.65},"draw_on_49":false}', 1, 27, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'bose');

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '半波', 'banbo', 5.00, 5.00, 6.30, '{"option_odds":{"绿双":6.30,"红单":5.40,"蓝单":5.40,"兰单":5.40,"蓝双":5.40,"兰双":5.40,"绿单":5.00,"红双":5.00},"draw_on_49":false}', 1, 28, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (SELECT 1 FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'banbo');

SELECT `id`, `name`, `code`, `odds_default`, `prize_config`, `is_enabled`, `sort`
FROM `la_play_method`
WHERE `game_id` = 200
  AND `code` IN ('lianxiao2','lianxiao3','lianxiao4','lianxiao5','liuxiao','tema_daxiao_danshuang','jiaqin_yeshou','hedan_heshuang','bose','banbo')
ORDER BY `sort`, `id`;
