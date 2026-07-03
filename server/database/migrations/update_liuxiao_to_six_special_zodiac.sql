-- 将原“六肖/6肖”升级为“6肖中特”。
-- 规则：选择6个生肖，只按特码判断；特码生肖命中任一所选生肖即中奖；特码49打和退本金。
-- 赔率为包含本金的 1.95。

UPDATE `la_play_method`
SET `name` = '6肖中特',
    `odds_default` = 1.95,
    `odds_min` = 1.95,
    `odds_max` = 1.95,
    `prize_config` = '{"select_count":6,"default_odds":1.95,"judge_scope":"special","draw_on_49":true}',
    `is_enabled` = 1,
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200
  AND (`code` = 'liuxiao' OR `name` IN ('六肖', '6肖', '6肖中特', '六肖中特'));

INSERT INTO `la_play_method`
(`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`, `sort`, `created_at`, `updated_at`)
SELECT 200, '6肖中特', 'liuxiao', 1.95, 1.95, 1.95, '{"select_count":6,"default_odds":1.95,"judge_scope":"special","draw_on_49":true}', 1, 9, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
WHERE NOT EXISTS (
    SELECT 1
    FROM `la_play_method`
    WHERE `game_id` = 200
      AND `code` = 'liuxiao'
);

SELECT `id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `prize_config`, `is_enabled`
FROM `la_play_method`
WHERE `game_id` = 200
  AND `code` = 'liuxiao';
