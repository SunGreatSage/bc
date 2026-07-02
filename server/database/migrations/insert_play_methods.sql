-- 插入玩法配置数据
-- 执行时间: 2025-12-11

-- 清空旧数据
TRUNCATE TABLE `la_play_method`;

-- 插入玩法配置（游戏ID=200，新澳门六合彩）
INSERT INTO `la_play_method` (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `is_enabled`, `sort`, `created_at`) VALUES
-- 特码（01-49）
(200, '特码', 'tema', 47.00, 40.00, 50.00, 1, 1, UNIX_TIMESTAMP()),

-- 平码（01-49，按前6个开奖号码判奖）
(200, '平码', 'pingma', 7.00, 1.00, 999.00, 1, 2, UNIX_TIMESTAMP()),

-- 特肖（一肖）
(200, '特肖', 'texiao', 11.75, 10.00, 13.00, 1, 4, UNIX_TIMESTAMP()),
(200, '一肖', 'yixiao', 11.75, 10.00, 13.00, 1, 5, UNIX_TIMESTAMP()),

-- 连肖玩法（仅保留三肖、四肖、五肖、六肖）
(200, '三肖', 'sanxiao', 3.60, 3.00, 4.00, 1, 6, UNIX_TIMESTAMP()),
(200, '四肖', 'sixiao', 2.80, 2.50, 3.20, 1, 7, UNIX_TIMESTAMP()),
(200, '五肖', 'wuxiao', 2.00, 1.80, 2.30, 1, 8, UNIX_TIMESTAMP()),
(200, '六肖', 'liuxiao', 1.90, 1.70, 2.10, 1, 9, UNIX_TIMESTAMP()),

-- 数字连码
(200, '二中二', 'erzhonger', 60.00, 1.00, 999.00, 1, 10, UNIX_TIMESTAMP()),
(200, '三中二', 'sanzhonger', 20.00, 1.00, 999.00, 1, 11, UNIX_TIMESTAMP()),
(200, '三中三', 'sanzhongsan', 500.00, 1.00, 999.00, 1, 12, UNIX_TIMESTAMP()),

-- 平肖 / 数字不中
(200, '平肖', 'pingxiao', 2.00, 1.00, 999.00, 1, 13, UNIX_TIMESTAMP()),
(200, '五不中', 'wubuzhong', 2.00, 1.00, 999.00, 1, 14, UNIX_TIMESTAMP()),
(200, '六不中', 'liubuzhong', 2.50, 1.00, 999.00, 1, 15, UNIX_TIMESTAMP()),
(200, '七不中', 'qibuzhong', 3.00, 1.00, 999.00, 1, 16, UNIX_TIMESTAMP()),
(200, '八不中', 'babuzhong', 3.50, 1.00, 999.00, 1, 17, UNIX_TIMESTAMP()),
(200, '九不中', 'jiubuzhong', 4.00, 1.00, 999.00, 1, 18, UNIX_TIMESTAMP()),
(200, '十不中', 'shibuzhong', 5.00, 1.00, 999.00, 1, 19, UNIX_TIMESTAMP());

UPDATE `la_play_method`
SET `prize_config` = '{"default_odds":2.00,"option_odds":{"马":1.80}}',
    `updated_at` = UNIX_TIMESTAMP()
WHERE `game_id` = 200 AND `code` = 'pingxiao';
