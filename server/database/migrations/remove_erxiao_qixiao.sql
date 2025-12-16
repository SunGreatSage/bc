-- 删除二肖和七肖玩法
-- 执行时间: 2025-12-11

-- 删除二肖
DELETE FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'erxiao';

-- 删除七肖
DELETE FROM `la_play_method` WHERE `game_id` = 200 AND `code` = 'qixiao';

-- 验证删除结果
SELECT id, name, code, odds_default, is_enabled
FROM `la_play_method`
WHERE `game_id` = 200
ORDER BY sort;
