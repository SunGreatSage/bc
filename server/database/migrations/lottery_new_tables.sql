-- ========================================
-- BC 彩票系统 - 新表结构 (支持多盘)
-- 创建时间: 2025-12-09
-- 说明: 支持A/B/C盘,每个盘独立开奖时间
-- ========================================

-- ========================================
-- 1. 彩票盘口配置表 (la_lottery_plate)
-- ========================================
DROP TABLE IF EXISTS `la_lottery_plate`;
CREATE TABLE `la_lottery_plate` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '盘口ID',
  `plate_code` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '盘口代码: A, B, C',
  `plate_name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '盘口名称: A盘, B盘, C盘',
  `game_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏ID (关联游戏类型)',
  `draw_time_offset` INT(11) NOT NULL DEFAULT 0 COMMENT '开奖时间偏移(分钟) 相对基准时间',
  `close_time_offset` INT(11) NOT NULL DEFAULT -5 COMMENT '封盘时间偏移(分钟) 相对开奖时间',
  `status` TINYINT(1) UNSIGNED DEFAULT 1 COMMENT '状态: 0=停用, 1=启用',
  `sort` INT(11) UNSIGNED DEFAULT 0 COMMENT '排序',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `updated_at` INT(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_plate_game` (`plate_code`, `game_id`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='彩票盘口配置表';

-- 示例数据: A盘09:50开奖, B盘10:20开奖, C盘10:50开奖
INSERT INTO `la_lottery_plate` (`plate_code`, `plate_name`, `game_id`, `draw_time_offset`, `close_time_offset`, `status`, `sort`, `created_at`) VALUES
('A', 'A盘', 200, 0, -5, 1, 1, UNIX_TIMESTAMP()),
('B', 'B盘', 200, 30, -5, 1, 2, UNIX_TIMESTAMP()),
('C', 'C盘', 200, 60, -5, 1, 3, UNIX_TIMESTAMP());

-- ========================================
-- 2. 用户账户表 (la_user_account)
-- ========================================
DROP TABLE IF EXISTS `la_user_account`;
CREATE TABLE `la_user_account` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '账户ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `balance` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '账户余额',
  `frozen_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '冻结金额',
  `total_recharge` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '累计充值',
  `total_withdraw` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '累计提现',
  `total_bet` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '累计投注',
  `total_prize` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '累计中奖',
  `total_commission` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '累计佣金',
  `status` TINYINT(1) UNSIGNED DEFAULT 1 COMMENT '状态: 0=冻结, 1=正常',
  `version` INT(11) UNSIGNED DEFAULT 0 COMMENT '版本号(乐观锁)',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `updated_at` INT(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_user` (`user_id`) USING BTREE,
  KEY `idx_balance` (`balance`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户账户表';

-- ========================================
-- 3. 账户流水表 (la_account_log)
-- ========================================
DROP TABLE IF EXISTS `la_account_log`;
CREATE TABLE `la_account_log` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '流水ID',
  `sn` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '流水单号',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `change_type` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0 COMMENT '变动类型: 1=充值, 2=提现, 3=投注, 4=中奖, 5=退款, 6=佣金, 7=调整, 8=冻结, 9=解冻',
  `change_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '变动金额',
  `balance_before` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '变动前余额',
  `balance_after` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '变动后余额',
  `frozen_before` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '变动前冻结',
  `frozen_after` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '变动后冻结',
  `related_sn` VARCHAR(64) DEFAULT '' COMMENT '关联单号',
  `related_type` TINYINT(2) UNSIGNED DEFAULT 0 COMMENT '关联类型: 1=投注, 2=充值, 3=提现, 4=佣金',
  `remark` VARCHAR(255) DEFAULT '' COMMENT '备注',
  `operator_id` INT(11) UNSIGNED DEFAULT 0 COMMENT '操作人ID',
  `ip` VARCHAR(39) DEFAULT '' COMMENT '操作IP',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_sn` (`sn`) USING BTREE,
  KEY `idx_user_created` (`user_id`, `created_at`) USING BTREE,
  KEY `idx_change_type` (`change_type`) USING BTREE,
  KEY `idx_related_sn` (`related_sn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='账户流水表';

-- ========================================
-- 4. 用户扩展表 (la_user_extend)
-- ========================================
DROP TABLE IF EXISTS `la_user_extend`;
CREATE TABLE `la_user_extend` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '扩展ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `parent_id` INT(11) UNSIGNED DEFAULT 0 COMMENT '上级ID',
  `ancestor_ids` VARCHAR(255) DEFAULT '' COMMENT '祖先ID链',
  `level` TINYINT(4) UNSIGNED DEFAULT 0 COMMENT '层级',
  `is_agent` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '是否代理',
  `agent_rate` DECIMAL(5, 2) DEFAULT 0.00 COMMENT '佣金比例(%)',
  `total_downlines` INT(11) UNSIGNED DEFAULT 0 COMMENT '下级总数',
  `direct_downlines` INT(11) UNSIGNED DEFAULT 0 COMMENT '直属下级',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `updated_at` INT(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_user` (`user_id`) USING BTREE,
  KEY `idx_parent` (`parent_id`) USING BTREE,
  KEY `idx_is_agent` (`is_agent`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户扩展表';

-- ========================================
-- 5. 开奖期次表 (la_lottery_issue) - 支持多盘
-- ========================================
DROP TABLE IF EXISTS `la_lottery_issue`;
CREATE TABLE `la_lottery_issue` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '期次ID',
  `game_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏ID',
  `plate_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '盘口ID',
  `plate_code` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '盘口代码',
  `issue` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '期号',
  `result` VARCHAR(255) DEFAULT '' COMMENT '开奖结果(已公开)',
  `planned_result` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '封盘后预生成开奖号码(未公开)',
  `planned_at` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预生成时间',
  `planned_source` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=auto,1=admin',
  `planned_operator_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预生成操作员ID(0=系统)',
  `status` TINYINT(2) UNSIGNED DEFAULT 1 COMMENT '状态: 1=待开盘, 2=投注中, 3=已封盘, 4=已开奖, 5=已结算',
  `open_time` INT(10) UNSIGNED DEFAULT 0 COMMENT '开盘时间',
  `close_time` INT(10) UNSIGNED DEFAULT 0 COMMENT '封盘时间',
  `draw_time` INT(10) UNSIGNED DEFAULT 0 COMMENT '开奖时间',
  `is_settled` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '是否已结算',
  `settled_at` INT(10) UNSIGNED DEFAULT 0 COMMENT '结算时间',
  `total_bet_amount` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '总投注额',
  `total_prize_amount` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '总派奖额',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `updated_at` INT(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_game_plate_issue` (`game_id`, `plate_code`, `issue`) USING BTREE,
  KEY `idx_plate_status` (`plate_id`, `status`) USING BTREE,
  KEY `idx_draw_time` (`draw_time`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='开奖期次表(支持多盘)';

-- ========================================
-- 6. 投注记录表 (la_betting_record) - 支持多盘
-- ========================================
DROP TABLE IF EXISTS `la_betting_record`;
CREATE TABLE `la_betting_record` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '投注ID',
  `sn` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '投注单号',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `game_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏ID',
  `plate_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '盘口ID',
  `plate_code` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '盘口代码',
  `issue_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '期次ID',
  `issue` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '期号',
  `method_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '玩法ID',
  `method_name` VARCHAR(50) DEFAULT '' COMMENT '玩法名称',
  `bet_content` VARCHAR(500) DEFAULT '' COMMENT '投注内容',
  `bet_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '单注金额',
  `bet_multiple` INT(11) UNSIGNED DEFAULT 1 COMMENT '投注倍数',
  `total_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '总金额',
  `odds` DECIMAL(10, 2) DEFAULT 0.00 COMMENT '赔率',
  `status` TINYINT(2) UNSIGNED DEFAULT 0 COMMENT '状态: 0=待开奖, 1=已中奖, 2=未中奖, 3=已撤单',
  `prize_amount` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '中奖金额',
  `is_settled` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '是否已结算',
  `settled_at` INT(10) UNSIGNED DEFAULT 0 COMMENT '结算时间',
  `parent_id` INT(11) UNSIGNED DEFAULT 0 COMMENT '上级ID',
  `ancestor_ids` VARCHAR(255) DEFAULT '' COMMENT '祖先ID链',
  `ip` VARCHAR(39) DEFAULT '' COMMENT 'IP地址',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `updated_at` INT(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_sn` (`sn`) USING BTREE,
  KEY `idx_user_created` (`user_id`, `created_at`) USING BTREE,
  KEY `idx_issue_status` (`issue_id`, `status`) USING BTREE,
  KEY `idx_plate_issue` (`plate_id`, `issue`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='投注记录表(支持多盘)';

-- ========================================
-- 7. 中奖记录表 (la_winning_record)
-- ========================================
DROP TABLE IF EXISTS `la_winning_record`;
CREATE TABLE `la_winning_record` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '中奖ID',
  `betting_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '投注ID',
  `sn` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '投注单号',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `game_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏ID',
  `plate_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '盘口ID',
  `plate_code` VARCHAR(10) NOT NULL DEFAULT '' COMMENT '盘口代码',
  `issue_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '期次ID',
  `issue` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '期号',
  `method_name` VARCHAR(50) DEFAULT '' COMMENT '玩法名称',
  `bet_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '投注金额',
  `odds` DECIMAL(10, 2) DEFAULT 0.00 COMMENT '赔率',
  `prize_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '中奖金额',
  `net_profit` DECIMAL(15, 2) DEFAULT 0.00 COMMENT '净盈利',
  `is_paid` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '是否已派奖',
  `paid_at` INT(10) UNSIGNED DEFAULT 0 COMMENT '派奖时间',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_betting` (`betting_id`) USING BTREE,
  KEY `idx_user_created` (`user_id`, `created_at`) USING BTREE,
  KEY `idx_issue` (`issue_id`) USING BTREE,
  KEY `idx_plate_issue` (`plate_id`, `issue`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中奖记录表';

-- ========================================
-- 8. 代理佣金表 (la_agent_commission)
-- ========================================
DROP TABLE IF EXISTS `la_agent_commission`;
CREATE TABLE `la_agent_commission` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '佣金ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '代理ID',
  `downline_user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '下级用户ID',
  `betting_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '投注ID',
  `game_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏ID',
  `plate_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '盘口ID',
  `issue_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '期次ID',
  `issue` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '期号',
  `bet_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '投注金额',
  `commission_rate` DECIMAL(5, 2) NOT NULL DEFAULT 0.00 COMMENT '佣金比例',
  `commission_amount` DECIMAL(15, 2) NOT NULL DEFAULT 0.00 COMMENT '佣金金额',
  `commission_type` TINYINT(2) UNSIGNED DEFAULT 1 COMMENT '佣金类型: 1=投注佣金',
  `status` TINYINT(1) UNSIGNED DEFAULT 0 COMMENT '状态: 0=待发放, 1=已发放',
  `settled_at` INT(10) UNSIGNED DEFAULT 0 COMMENT '结算时间',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_user_created` (`user_id`, `created_at`) USING BTREE,
  KEY `idx_betting` (`betting_id`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='代理佣金表';

-- ========================================
-- 9. 玩法配置表 (la_play_method)
-- ========================================
DROP TABLE IF EXISTS `la_play_method`;
CREATE TABLE `la_play_method` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '玩法ID',
  `game_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '游戏ID',
  `name` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '玩法名称',
  `code` VARCHAR(50) DEFAULT '' COMMENT '玩法代码',
  `odds_default` DECIMAL(10, 2) DEFAULT 0.00 COMMENT '默认赔率',
  `odds_min` DECIMAL(10, 2) DEFAULT 0.00 COMMENT '最小赔率',
  `odds_max` DECIMAL(10, 2) DEFAULT 0.00 COMMENT '最大赔率',
  `prize_config` TEXT COMMENT '中奖规则配置(JSON)',
  `is_enabled` TINYINT(1) UNSIGNED DEFAULT 1 COMMENT '是否启用',
  `sort` INT(11) UNSIGNED DEFAULT 0 COMMENT '排序',
  `created_at` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `updated_at` INT(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_game_enabled` (`game_id`, `is_enabled`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='玩法配置表';

-- ========================================
-- 10. 用户基础表 (la_user)
-- ========================================
DROP TABLE IF EXISTS `la_user`;
CREATE TABLE `la_user` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` VARCHAR(64) NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` VARCHAR(32) DEFAULT '' COMMENT '昵称',
  `avatar` VARCHAR(255) DEFAULT '' COMMENT '头像',
  `mobile` VARCHAR(20) DEFAULT '' COMMENT '手机号',
  `status` TINYINT(1) UNSIGNED DEFAULT 1 COMMENT '状态: 0=禁用, 1=正常',
  `create_time` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
  `update_time` INT(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `uk_username` (`username`) USING BTREE,
  KEY `idx_mobile` (`mobile`) USING BTREE,
  KEY `idx_status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户基础表';

-- ========================================
-- 测试数据
-- ========================================

-- 1. 测试用户
INSERT INTO `la_user` (`username`, `password`, `nickname`, `status`, `create_time`) VALUES
('test001', 'e10adc3949ba59abbe56e057f20f883e', '测试用户1', 1, UNIX_TIMESTAMP()),
('test002', 'e10adc3949ba59abbe56e057f20f883e', '测试用户2', 1, UNIX_TIMESTAMP()),
('agent001', 'e10adc3949ba59abbe56e057f20f883e', '代理1', 1, UNIX_TIMESTAMP());

-- 2. 用户账户
INSERT INTO `la_user_account` (`user_id`, `balance`, `status`, `created_at`, `updated_at`) VALUES
(1, 10000.00, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(2, 5000.00, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(3, 20000.00, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 3. 用户扩展(代理关系)
INSERT INTO `la_user_extend` (`user_id`, `parent_id`, `ancestor_ids`, `level`, `is_agent`, `agent_rate`, `created_at`) VALUES
(1, 3, '3', 1, 0, 0.00, UNIX_TIMESTAMP()),
(2, 3, '3', 1, 0, 0.00, UNIX_TIMESTAMP()),
(3, 0, '', 0, 1, 5.00, UNIX_TIMESTAMP());

-- 4. 测试期次(A盘 - 当天)
INSERT INTO `la_lottery_issue` (`game_id`, `plate_id`, `plate_code`, `issue`, `status`, `open_time`, `close_time`, `draw_time`, `created_at`) VALUES
(200, 1, 'A', CONCAT(DATE_FORMAT(NOW(), '%Y%m%d'), '01'), 2,
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 06:00:00')),
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 09:25:00')),
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 09:30:00')),
 UNIX_TIMESTAMP());

-- 5. 测试期次(B盘 - 当天)
INSERT INTO `la_lottery_issue` (`game_id`, `plate_id`, `plate_code`, `issue`, `status`, `open_time`, `close_time`, `draw_time`, `created_at`) VALUES
(200, 2, 'B', CONCAT(DATE_FORMAT(NOW(), '%Y%m%d'), '01'), 2,
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 06:00:00')),
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 09:55:00')),
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 10:00:00')),
 UNIX_TIMESTAMP());

-- 6. 测试期次(C盘 - 当天)
INSERT INTO `la_lottery_issue` (`game_id`, `plate_id`, `plate_code`, `issue`, `status`, `open_time`, `close_time`, `draw_time`, `created_at`) VALUES
(200, 3, 'C', CONCAT(DATE_FORMAT(NOW(), '%Y%m%d'), '01'), 2,
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 06:00:00')),
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 10:25:00')),
 UNIX_TIMESTAMP(CONCAT(CURDATE(), ' 10:30:00')),
 UNIX_TIMESTAMP());

-- ========================================
-- 索引优化建议
-- ========================================
-- 高频查询索引已在建表时创建
-- 如需额外优化,可根据实际查询情况添加复合索引
