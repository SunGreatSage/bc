-- ============================================
-- 最佳控盘计划 - 数据库表创建脚本 (新表架构)
-- 日期: 2025-12-12
-- 说明: 新表架构,表名前缀 la_,支持盘口参数
-- ============================================

-- 创建分析历史记录表
CREATE TABLE IF NOT EXISTS `la_best_plan_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `gid` INT(11) NOT NULL COMMENT '游戏ID（100=香港六合彩，200=新澳门，300=澳门六合彩）',
  `qishu` VARCHAR(20) NOT NULL COMMENT '期号（如 2025001）',
  `plate_code` VARCHAR(10) NOT NULL DEFAULT 'A' COMMENT '盘口代码（A/B/C）',
  `analyze_time` DATETIME NOT NULL COMMENT '分析时间',

  -- 汇总数据（便于查询和排序）
  `total_bets` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT '总投注额',
  `total_orders` INT(11) NOT NULL DEFAULT 0 COMMENT '总投注笔数',
  `best_numbers` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '最佳7个号码（逗号分隔，如"1,2,3,4,5,6,7"）',
  `best_profit` DECIMAL(15,2) NOT NULL COMMENT '最高利润额',
  `best_profit_rate` DECIMAL(5,2) NOT NULL COMMENT '最高利润率（%）',
  `worst_number` TINYINT(2) NOT NULL DEFAULT 0 COMMENT '亏损最大号码（预留）',
  `worst_profit` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT '最大亏损额（预留）',
  `worst_profit_rate` DECIMAL(5,2) NOT NULL DEFAULT 0 COMMENT '最大亏损率（预留）',
  `avg_profit` DECIMAL(15,2) NOT NULL COMMENT '平均利润',

  -- 49个号码的详细数据（JSON格式存储）
  `number_details` JSON NOT NULL COMMENT '49个号码的详细盈亏数据（JSON数组）',

  -- 开奖后的验证数据
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '状态（0=未开奖，1=已开奖，2=已验证）',
  `actual_number` TINYINT(2) DEFAULT NULL COMMENT '实际开出号码',
  `actual_profit` DECIMAL(15,2) DEFAULT NULL COMMENT '实际利润',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gid_qishu_plate` (`gid`, `qishu`, `plate_code`),
  KEY `idx_analyze_time` (`analyze_time`),
  KEY `idx_status` (`status`),
  KEY `idx_plate_code` (`plate_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='最佳控盘分析历史记录（新表）';


-- ============================================
-- 与旧表的区别
-- ============================================
-- 1. 表名: x_best_plan_history → la_best_plan_history
-- 2. 新增字段: plate_code (盘口代码)
-- 3. 唯一索引: uk_gid_qishu → uk_gid_qishu_plate (增加盘口维度)
-- 4. 新增索引: idx_plate_code (盘口查询优化)


-- ============================================
-- JSON 字段结构说明 (number_details)
-- ============================================
-- [
--   {
--     "number": 1,
--     "profit": 85000.00,
--     "profit_rate": 85.00,
--     "prize_amount": 15000.00,
--     "bet_count": 12,
--     "risk_level": 0
--   },
--   {
--     "number": 2,
--     "profit": 82300.00,
--     "profit_rate": 82.30,
--     "prize_amount": 17700.00,
--     "bet_count": 15,
--     "risk_level": 0
--   },
--   ... 共49条记录
-- ]
--
-- 字段说明:
-- - number: 号码（1-49）
-- - profit: 平台利润（正=盈利，负=亏损）
-- - profit_rate: 利润率（%）
-- - prize_amount: 该号码开出时的总赔付额
-- - bet_count: 中奖注数
-- - risk_level: 风险等级（0=安全，1=注意，2=危险）


-- ============================================
-- 常用查询示例
-- ============================================

-- 查询最近10期分析记录（按盘口过滤）
-- SELECT id, gid, qishu, plate_code, analyze_time, total_bets, best_number, best_profit, worst_number, worst_profit, status
-- FROM la_best_plan_history
-- WHERE gid = 200 AND plate_code = 'A'
-- ORDER BY analyze_time DESC
-- LIMIT 10;

-- 查询某期某盘口的详细数据
-- SELECT * FROM la_best_plan_history WHERE gid = 200 AND qishu = '2025121102' AND plate_code = 'A';

-- 查询未开奖的分析记录
-- SELECT * FROM la_best_plan_history WHERE status = 0;

-- 从JSON字段中提取特定号码的数据
-- SELECT
--   id, qishu, plate_code,
--   JSON_EXTRACT(number_details, '$[0].number') as first_number,
--   JSON_EXTRACT(number_details, '$[0].profit') as first_profit
-- FROM la_best_plan_history
-- WHERE gid = 200
-- LIMIT 5;

-- 对比不同盘口的分析结果
-- SELECT
--   qishu,
--   plate_code,
--   best_number,
--   best_profit,
--   best_profit_rate,
--   total_bets
-- FROM la_best_plan_history
-- WHERE gid = 200 AND qishu = '2025121102'
-- ORDER BY plate_code;
