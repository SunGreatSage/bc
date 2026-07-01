SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO `la_system_menu`
  (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
VALUES
  (213, 195, 'C', '历史下单', 'lucide:list-ordered', 15, 'best_plan/getOrderHistory', 'order-history', 'control-panel/order-history/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
  (214, 213, 'A', '下单列表', '', 1, 'best_plan/getOrderHistory', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
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

INSERT IGNORE INTO `la_system_role_menu` (`role_id`, `menu_id`)
VALUES
  (1, 213),
  (1, 214);

SET FOREIGN_KEY_CHECKS = 1;
