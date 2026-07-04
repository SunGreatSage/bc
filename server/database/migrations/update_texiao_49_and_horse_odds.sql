-- 特肖规则调整:
-- 1. 特肖开出49按当年生肖正常判奖,不再作为和局.
-- 2. 特肖投注"马"的单项赔率调整为9.40,其他生肖继续使用当前默认赔率.

UPDATE `la_play_method`
SET
    `prize_config` = JSON_SET(
        JSON_SET(
            IF(JSON_VALID(`prize_config`), `prize_config`, JSON_OBJECT()),
            '$.option_odds',
            JSON_MERGE_PATCH(
                IF(
                    JSON_TYPE(JSON_EXTRACT(IF(JSON_VALID(`prize_config`), `prize_config`, JSON_OBJECT()), '$.option_odds')) = 'OBJECT',
                    JSON_EXTRACT(IF(JSON_VALID(`prize_config`), `prize_config`, JSON_OBJECT()), '$.option_odds'),
                    JSON_OBJECT()
                ),
                JSON_OBJECT('马', 9.40)
            )
        ),
        '$.default_odds',
        CAST(IFNULL(
            JSON_UNQUOTE(JSON_EXTRACT(IF(JSON_VALID(`prize_config`), `prize_config`, JSON_OBJECT()), '$.default_odds')),
            `odds_default`
        ) AS DECIMAL(10,2)),
        '$.rule_49',
        'normal_zodiac'
    ),
    `odds_min` = LEAST(IFNULL(`odds_min`, 9.40), 9.40),
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200
  AND (`code` = 'texiao' OR `name` = '特肖');
