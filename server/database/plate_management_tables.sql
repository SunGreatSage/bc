-- ========================================
-- 盘口管理系统数据库表
-- 创建时间: 2025-12-12
-- 说明: 盘口设置和用户盘口关系管理
-- ========================================

-- 1. 盘口表 (la_plate)
CREATE TABLE IF NOT EXISTS `la_plate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '盘口ID',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT '盘口代码(如A、B、C)',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '盘口名称',
  `game_id` int(11) NOT NULL DEFAULT 200 COMMENT '游戏ID',
  `open_time` varchar(5) NOT NULL DEFAULT '06:00' COMMENT '开盘时间(HH:mm格式)',
  `close_time` varchar(5) NOT NULL DEFAULT '09:30' COMMENT '封盘时间(HH:mm格式)',
  `draw_time` varchar(5) NOT NULL DEFAULT '09:50' COMMENT '开奖时间(HH:mm格式)',
  `close_advance` int(11) NOT NULL DEFAULT 5 COMMENT '提前封盘分钟数',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态(0=禁用, 1=启用)',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序(数字越小越靠前)',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间(软删除)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`,`deleted_at`),
  KEY `idx_game_id` (`game_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='盘口配置表';

-- 插入默认盘口数据
INSERT INTO `la_plate` (`id`, `code`, `name`, `game_id`, `open_time`, `close_time`, `draw_time`, `close_advance`, `status`, `sort`, `remark`, `created_at`, `updated_at`) VALUES
(1, 'A', 'A盘', 200, '06:00', '09:30', '09:50', 5, 1, 1, '默认A盘', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(2, 'B', 'B盘', 200, '06:00', '09:30', '09:50', 5, 1, 2, '默认B盘', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(3, 'C', 'C盘', 200, '06:00', '09:30', '09:50', 5, 1, 3, '默认C盘', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());


-- 2. 用户盘口关系表 (la_user_plate)
CREATE TABLE IF NOT EXISTS `la_user_plate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID(关联la_user表)',
  `plate_id` int(11) NOT NULL COMMENT '盘口ID(关联la_plate表)',
  `plate_code` varchar(10) NOT NULL DEFAULT '' COMMENT '盘口代码(冗余字段,便于查询)',
  `is_agent` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否代理(0=普通会员, 1=代理)',
  `agent_level` tinyint(2) NOT NULL DEFAULT 0 COMMENT '代理等级(0=普通会员, 1=一级代理, 2=二级代理)',
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT '佣金比例(%)',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态(0=禁用, 1=启用)',
  `created_at` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(11) DEFAULT NULL COMMENT '删除时间(软删除)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_plate` (`user_id`, `plate_id`, `deleted_at`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_plate_id` (`plate_id`),
  KEY `idx_plate_code` (`plate_code`),
  KEY `idx_is_agent` (`is_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户盘口关系表';


-- 3. 修改现有的 la_betting_record 表(添加盘口字段,如果还没有)
-- 注意: 这个ALTER语句仅在字段不存在时执行
-- ALTER TABLE `la_betting_record`
-- ADD COLUMN `plate_code` varchar(10) NOT NULL DEFAULT 'A' COMMENT '盘口代码' AFTER `issue`,
-- ADD INDEX `idx_plate_code` (`plate_code`);

-- 4. 修改现有的 la_lottery_issue 表(添加盘口字段,如果还没有)
-- ALTER TABLE `la_lottery_issue`
-- ADD COLUMN `plate_code` varchar(10) NOT NULL DEFAULT 'A' COMMENT '盘口代码' AFTER `issue`,
-- ADD INDEX `idx_plate_code` (`plate_code`);


-- ========================================
-- 查询语句示例
-- ========================================

-- 查询所有启用的盘口
-- SELECT * FROM la_plate WHERE status = 1 AND deleted_at IS NULL ORDER BY sort ASC;

-- 查询用户的所有盘口
-- SELECT p.*, up.is_agent, up.agent_level, up.commission_rate
-- FROM la_user_plate up
-- LEFT JOIN la_plate p ON up.plate_id = p.id
-- WHERE up.user_id = ? AND up.deleted_at IS NULL;

-- 查询盘口下的所有用户
-- SELECT u.*, up.is_agent, up.agent_level, up.commission_rate
-- FROM la_user_plate up
-- LEFT JOIN la_user u ON up.user_id = u.id
-- WHERE up.plate_id = ? AND up.deleted_at IS NULL;
