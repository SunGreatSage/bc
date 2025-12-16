-- ========================================
-- 盘口管理系统菜单数据
-- 创建时间: 2025-12-12
-- 说明: 添加盘口管理菜单到系统
-- ========================================

-- 1. 插入盘口管理一级菜单 (目录)
INSERT INTO `la_system_menu`
(`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
VALUES
(0, 'M', '盘口管理', 'el-icon-SetUp', 100, '', 'plate', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 获取刚插入的一级菜单ID (假设为 @plate_menu_id)
SET @plate_menu_id = LAST_INSERT_ID();

-- 2. 插入盘口设置子菜单 (菜单)
INSERT INTO `la_system_menu`
(`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
VALUES
(@plate_menu_id, 'C', '盘口设置', 'el-icon-Setting', 10, 'plate.plate/lists', 'settings', 'plate/settings/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 获取盘口设置菜单ID
SET @plate_settings_id = LAST_INSERT_ID();

-- 3. 插入盘口设置的操作按钮
INSERT INTO `la_system_menu`
(`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
VALUES
(@plate_settings_id, 'A', '查看', '', 1, 'plate.plate/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_settings_id, 'A', '详情', '', 2, 'plate.plate/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_settings_id, 'A', '新增', '', 3, 'plate.plate/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_settings_id, 'A', '编辑', '', 4, 'plate.plate/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_settings_id, 'A', '删除', '', 5, 'plate.plate/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_settings_id, 'A', '状态', '', 6, 'plate.plate/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 4. 插入用户管理子菜单 (菜单)
INSERT INTO `la_system_menu`
(`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
VALUES
(@plate_menu_id, 'C', '用户管理', 'el-icon-User', 20, 'plate.user_plate/lists', 'users', 'plate/users/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 获取用户管理菜单ID
SET @plate_users_id = LAST_INSERT_ID();

-- 5. 插入用户管理的操作按钮
INSERT INTO `la_system_menu`
(`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
VALUES
(@plate_users_id, 'A', '查看', '', 1, 'plate.user_plate/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_users_id, 'A', '详情', '', 2, 'plate.user_plate/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_users_id, 'A', '添加', '', 3, 'plate.user_plate/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_users_id, 'A', '编辑', '', 4, 'plate.user_plate/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_users_id, 'A', '删除', '', 5, 'plate.user_plate/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_users_id, 'A', '状态', '', 6, 'plate.user_plate/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@plate_users_id, 'A', '批量分配', '', 7, 'plate.user_plate/batchAssign', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- ========================================
-- 说明:
-- type: M=目录, C=菜单, A=按钮
-- is_cache: 0=不缓存, 1=缓存
-- is_show: 0=隐藏, 1=显示
-- is_disable: 0=启用, 1=禁用
-- ========================================

-- 查询验证 (可选)
SELECT id, pid, type, name, perms, paths, component
FROM la_system_menu
WHERE pid = @plate_menu_id OR id = @plate_menu_id
ORDER BY id ASC;
