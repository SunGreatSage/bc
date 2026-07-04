/*
 Admin menu recovery for vue/admin/apps/web-antd.

 This supplements the recovered dump with menus that exist in the current
 frontend route/API surface but were missing or still pointed at older
 controller permission strings.
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Current frontend uses /plate.user/* for the "盘口管理 > 用户管理" page.
UPDATE `la_system_menu`
SET `perms` = 'plate.user/lists',
    `update_time` = UNIX_TIMESTAMP()
WHERE `id` = 187;

UPDATE `la_system_menu`
SET `name` = '查看',
    `perms` = 'plate.user/lists',
    `update_time` = UNIX_TIMESTAMP()
WHERE `id` = 188;

UPDATE `la_system_menu`
SET `name` = '详情',
    `perms` = 'plate.user/detail',
    `update_time` = UNIX_TIMESTAMP()
WHERE `id` = 189;

UPDATE `la_system_menu`
SET `name` = '新增',
    `perms` = 'plate.user/add',
    `update_time` = UNIX_TIMESTAMP()
WHERE `id` = 190;

UPDATE `la_system_menu`
SET `name` = '编辑',
    `perms` = 'plate.user/edit',
    `update_time` = UNIX_TIMESTAMP()
WHERE `id` = 191;

UPDATE `la_system_menu`
SET `name` = '删除',
    `perms` = 'plate.user/delete',
    `update_time` = UNIX_TIMESTAMP()
WHERE `id` = 192;

UPDATE `la_system_menu`
SET `name` = '状态',
    `perms` = 'plate.user/status',
    `update_time` = UNIX_TIMESTAMP()
WHERE `id` = 193;

UPDATE `la_system_menu`
SET `name` = '调整余额',
    `perms` = 'plate.user/adjustBalance',
    `update_time` = UNIX_TIMESTAMP()
WHERE `id` = 194;

-- Missing control-panel menu group from vue/admin/apps/web-antd/src/router/routes/modules/control-panel.ts.
INSERT INTO `la_system_menu`
  (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
VALUES
  (195, 0, 'M', '控盘管理', 'lucide:bar-chart-4', 960, '', 'control-panel', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (196, 195, 'C', '实时分析', 'lucide:activity', 20, 'best_plan/calculateRealtime', 'analysis', 'control-panel/analysis/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (197, 195, 'C', '历史记录', 'lucide:history', 10, 'best_plan/getHistoryList', 'history', 'control-panel/history/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (198, 196, 'A', '获取盘口', '', 1, 'best_plan/getPlateList', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (199, 196, 'A', '获取期号', '', 2, 'best_plan/getCurrentQishu', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (200, 196, 'A', '实时计算', '', 3, 'best_plan/calculateRealtime', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (201, 196, 'A', '保存分析', '', 4, 'best_plan/analyze', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (202, 196, 'A', '目标利润', '', 5, 'best_plan/findByTargetRate', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (203, 196, 'A', '执行开奖', '', 6, 'best_plan/executeDrawing', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (204, 196, 'A', '创建期号', '', 7, 'best_plan/createNewIssue', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (205, 197, 'A', '历史列表', '', 1, 'best_plan/getHistoryList', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (206, 197, 'A', '详情', '', 2, 'best_plan/getDetail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (207, 197, 'A', '投注汇总', '', 3, 'best_plan/getBetSummary', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (208, 197, 'A', '号码分布', '', 4, 'best_plan/getNumberDistribution', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (209, 187, 'A', '账户流水', '', 8, 'plate.user/accountLogs', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (210, 187, 'A', '开设代理', '', 9, 'plate.user/createAgent', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (211, 187, 'A', '调整信用额度', '', 10, 'plate.user/adjustAgentCredit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (212, 180, 'A', '全部盘口', '', 7, 'plate.plate/all', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE
  `pid` = VALUES(`pid`),
  `type` = VALUES(`type`),
  `name` = VALUES(`name`),
  `icon` = VALUES(`icon`),
  `sort` = VALUES(`sort`),
  `perms` = VALUES(`perms`),
  `paths` = VALUES(`paths`),
  `component` = VALUES(`component`),
  `selected` = VALUES(`selected`),
  `params` = VALUES(`params`),
  `is_cache` = VALUES(`is_cache`),
  `is_show` = VALUES(`is_show`),
  `is_disable` = VALUES(`is_disable`),
  `update_time` = VALUES(`update_time`);

-- Keep the default recovered role in sync with the repaired menu set.
INSERT IGNORE INTO `la_system_role_menu` (`role_id`, `menu_id`)
SELECT 1, `id`
FROM `la_system_menu`
WHERE `id` BETWEEN 195 AND 212
   OR `id` IN (187, 188, 189, 190, 191, 192, 193, 194);

SET FOREIGN_KEY_CHECKS = 1;
