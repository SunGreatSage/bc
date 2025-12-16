-- ========================================
-- 盘口管理菜单权限分配(修正版)
-- 数据库: newbc
-- 直接使用角色ID=1(假设为管理员)
-- ========================================

USE newbc;

-- 设置角色ID(通常ID=1是管理员)
SET @role_id = 1;

-- 删除旧的关联(如果存在)
DELETE FROM la_system_role_menu
WHERE role_id = @role_id AND menu_id BETWEEN 179 AND 194;

-- 批量插入菜单权限
INSERT INTO la_system_role_menu (role_id, menu_id, create_time)
SELECT @role_id, id, UNIX_TIMESTAMP()
FROM la_system_menu
WHERE id BETWEEN 179 AND 194;

-- 验证结果
SELECT
    '✅ 分配完成' as status,
    @role_id as role_id,
    COUNT(*) as menu_count
FROM la_system_role_menu
WHERE role_id = @role_id AND menu_id BETWEEN 179 AND 194;

-- 显示分配的菜单详情
SELECT
    sm.id,
    sm.pid as parent_id,
    sm.type,
    sm.name as menu_name,
    sm.perms,
    '✅ 已分配' as status
FROM la_system_menu sm
INNER JOIN la_system_role_menu srm ON sm.id = srm.menu_id
WHERE srm.role_id = @role_id AND sm.id BETWEEN 179 AND 194
ORDER BY sm.id;
