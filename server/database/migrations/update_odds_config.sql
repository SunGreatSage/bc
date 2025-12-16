-- 更新newbc数据库赔率配置
-- 创建时间: 2025-12-09
-- 说明: 7种玩法赔率配置(包本金转换为不含本金)

USE newbc;

-- 1. 特码 47倍(包本金) = 46倍(不含本金)
UPDATE la_play_method SET odds = 46.0000 WHERE game_id = 200 AND method_name LIKE '特码%';

-- 2. 正码 2.1倍(包本金) = 1.1倍(不含本金)
UPDATE la_play_method SET odds = 1.1000 WHERE game_id = 200 AND method_name LIKE '正码%';

-- 3. 特肖 11.75倍(包本金) = 10.75倍(不含本金)
UPDATE la_play_method SET odds = 10.7500 WHERE game_id = 200 AND method_name = '特肖';

-- 4. 六肖 1.9倍(包本金) = 0.9倍(不含本金)
UPDATE la_play_method SET odds = 0.9000 WHERE game_id = 200 AND method_name = '六肖';

-- 5. 五肖 2倍(包本金) = 1.0倍(不含本金)
UPDATE la_play_method SET odds = 1.0000 WHERE game_id = 200 AND method_name = '五肖';

-- 6. 四肖 2.8倍(包本金) = 1.8倍(不含本金)
UPDATE la_play_method SET odds = 1.8000 WHERE game_id = 200 AND method_name = '四肖';

-- 7. 三肖 3.6倍(包本金) = 2.6倍(不含本金)
UPDATE la_play_method SET odds = 2.6000 WHERE game_id = 200 AND method_name = '三肖';

-- 查看更新结果
SELECT method_id, method_name, odds, status FROM la_play_method WHERE game_id = 200 ORDER BY method_id;
