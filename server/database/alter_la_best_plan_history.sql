-- ============================================
-- 修复 la_best_plan_history 表结构
-- 日期: 2025-12-12
-- 说明: 修复 best_number 字段类型错误
-- ============================================

-- 修改表结构
ALTER TABLE `la_best_plan_history`
  -- 修改 best_number → best_numbers (VARCHAR 类型存储7个号码)
  CHANGE COLUMN `best_number` `best_numbers` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '最佳7个号码（逗号分隔，如"1,2,3,4,5,6,7"）',

  -- 修改 worst_* 字段添加默认值
  MODIFY COLUMN `worst_number` TINYINT(2) NOT NULL DEFAULT 0 COMMENT '亏损最大号码（预留）',
  MODIFY COLUMN `worst_profit` DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT '最大亏损额（预留）',
  MODIFY COLUMN `worst_profit_rate` DECIMAL(5,2) NOT NULL DEFAULT 0 COMMENT '最大亏损率（预留）';

-- ============================================
-- 修复说明
-- ============================================
--
-- 问题：
-- 原字段 best_number 为 TINYINT(2)，只能存储 1-99 的整数
-- 代码中使用 implode(',', [1,2,3,4,5,6,7]) 生成字符串 "1,2,3,4,5,6,7"
-- 导致数据截断错误: "Data truncated for column 'best_number'"
--
-- 解决方案：
-- 将 best_number 改为 best_numbers VARCHAR(50)
-- 支持存储逗号分隔的7个号码字符串
--
-- 代码已同步修改：
-- - server/app/api/logic/BestPlanLogic.php (line 63)
-- - server/database/la_best_plan_history.sql (line 18)
-- ============================================
