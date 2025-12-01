-- ============================================
-- 最佳控盘计划 - 数据库表创建脚本
-- 日期: 2025-12-01
-- 说明: 仅需创建 1 个表（x_best_plan_history）
-- ============================================

-- 创建分析历史记录表
CREATE TABLE IF NOT EXISTS `x_best_plan_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `gid` INT(11) NOT NULL COMMENT '游戏ID（100=香港六合彩，200=新澳门，300=澳门六合彩）',
  `qishu` VARCHAR(20) NOT NULL COMMENT '期号（如 2025001）',
  `analyze_time` DATETIME NOT NULL COMMENT '分析时间',

  -- 汇总数据（便于查询和排序）
  `total_bets` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT '总投注额',
  `total_orders` INT(11) NOT NULL DEFAULT 0 COMMENT '总投注笔数',
  `best_number` TINYINT(2) NOT NULL COMMENT '利润最高号码（1-49）',
  `best_profit` DECIMAL(15,2) NOT NULL COMMENT '最高利润额',
  `best_profit_rate` DECIMAL(5,2) NOT NULL COMMENT '最高利润率（%）',
  `worst_number` TINYINT(2) NOT NULL COMMENT '亏损最大号码',
  `worst_profit` DECIMAL(15,2) NOT NULL COMMENT '最大亏损额（负数）',
  `worst_profit_rate` DECIMAL(5,2) NOT NULL COMMENT '最大亏损率（%）',
  `avg_profit` DECIMAL(15,2) NOT NULL COMMENT '平均利润',

  -- 49个号码的详细数据（JSON格式存储）
  `number_details` JSON NOT NULL COMMENT '49个号码的详细盈亏数据（JSON数组）',

  -- 开奖后的验证数据
  `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '状态（0=未开奖，1=已开奖，2=已验证）',
  `actual_number` TINYINT(2) DEFAULT NULL COMMENT '实际开出号码',
  `actual_profit` DECIMAL(15,2) DEFAULT NULL COMMENT '实际利润',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gid_qishu` (`gid`, `qishu`),
  KEY `idx_analyze_time` (`analyze_time`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='最佳控盘分析历史记录';


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
-- 测试数据（可选）
-- ============================================
-- INSERT INTO x_best_plan_history (
--   gid, qishu, analyze_time, total_bets, total_orders,
--   best_number, best_profit, best_profit_rate,
--   worst_number, worst_profit, worst_profit_rate,
--   avg_profit, number_details, status
-- ) VALUES (
--   200, '2025334', NOW(), 100000.00, 500,
--   49, 85000.00, 85.00,
--   7, -50000.00, -50.00,
--   20000.00, '[{"number":1,"profit":80000,"profit_rate":80,"prize_amount":20000,"bet_count":10,"risk_level":0}]', 0
-- );


-- ============================================
-- 常用查询示例
-- ============================================

-- 查询最近10期分析记录
-- SELECT id, gid, qishu, analyze_time, total_bets, best_number, best_profit, worst_number, worst_profit, status
-- FROM x_best_plan_history
-- WHERE gid = 200
-- ORDER BY analyze_time DESC
-- LIMIT 10;

-- 查询某期的详细数据
-- SELECT * FROM x_best_plan_history WHERE gid = 200 AND qishu = '2025334';

-- 查询未开奖的分析记录
-- SELECT * FROM x_best_plan_history WHERE status = 0;

-- 从JSON字段中提取特定号码的数据
-- SELECT
--   id, qishu,
--   JSON_EXTRACT(number_details, '$[0].number') as first_number,
--   JSON_EXTRACT(number_details, '$[0].profit') as first_profit
-- FROM x_best_plan_history
-- WHERE gid = 200
-- LIMIT 5;
