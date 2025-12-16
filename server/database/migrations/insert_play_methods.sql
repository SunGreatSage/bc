-- 插入玩法配置数据
-- 执行时间: 2025-12-11

-- 清空旧数据
TRUNCATE TABLE `la_play_method`;

-- 插入玩法配置（游戏ID=200，新澳门六合彩）
INSERT INTO `la_play_method` (`game_id`, `name`, `code`, `odds_default`, `odds_min`, `odds_max`, `is_enabled`, `sort`, `created_at`) VALUES
-- 特码（01-49）
(200, '特码', 'tema', 47.00, 40.00, 50.00, 1, 1, UNIX_TIMESTAMP()),

-- 正码/平码（01-49）
(200, '正码', 'zhengma', 2.10, 1.80, 2.50, 1, 2, UNIX_TIMESTAMP()),
(200, '平码', 'pingma', 2.10, 1.80, 2.50, 1, 3, UNIX_TIMESTAMP()),

-- 特肖（一肖）
(200, '特肖', 'texiao', 11.75, 10.00, 13.00, 1, 4, UNIX_TIMESTAMP()),
(200, '一肖', 'yixiao', 11.75, 10.00, 13.00, 1, 5, UNIX_TIMESTAMP()),

-- 连肖玩法（仅保留三肖、四肖、五肖、六肖）
(200, '三肖', 'sanxiao', 3.60, 3.00, 4.00, 1, 6, UNIX_TIMESTAMP()),
(200, '四肖', 'sixiao', 2.80, 2.50, 3.20, 1, 7, UNIX_TIMESTAMP()),
(200, '五肖', 'wuxiao', 2.00, 1.80, 2.30, 1, 8, UNIX_TIMESTAMP()),
(200, '六肖', 'liuxiao', 1.90, 1.70, 2.10, 1, 9, UNIX_TIMESTAMP());
