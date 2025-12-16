-- ========================================
-- 盘口管理菜单权限分配脚本
-- 功能: 将盘口管理菜单(ID:179-194)分配给超级管理员角色
-- 创建时间: 2025-12-12
-- ========================================

-- 1. 查询超级管理员角色ID
SELECT @admin_role_id := id FROM la_system_role WHERE is_super = 1 LIMIT 1;

-- 2. 显示角色信息
SELECT
    @admin_role_id as '角色ID',
    name as '角色名称',
    is_super as '是否超管'
FROM la_system_role
WHERE id = @admin_role_id;

-- 3. 检查盘口菜单是否已存在
SELECT
    COUNT(*) as '已分配菜单数',
    GROUP_CONCAT(menu_id) as '已分配菜单ID'
FROM la_system_role_menu
WHERE role_id = @admin_role_id
AND menu_id BETWEEN 179 AND 194;

-- 4. 删除旧的关联(如果存在)
DELETE FROM la_system_role_menu
WHERE role_id = @admin_role_id
AND menu_id BETWEEN 179 AND 194;

-- 5. 批量插入菜单权限(ID: 179-194)
INSERT INTO la_system_role_menu (role_id, menu_id, create_time)
SELECT
    @admin_role_id,
    id,
    UNIX_TIMESTAMP()
FROM la_system_menu
WHERE id BETWEEN 179 AND 194;

-- 6. 验证分配结果
SELECT
    srm.role_id as '角色ID',
    sr.name as '角色名称',
    COUNT(srm.menu_id) as '分配的菜单数',
    GROUP_CONCAT(sm.name ORDER BY sm.id) as '菜单列表'
FROM la_system_role_menu srm
LEFT JOIN la_system_role sr ON srm.role_id = sr.id
LEFT JOIN la_system_menu sm ON srm.menu_id = sm.id
WHERE srm.role_id = @admin_role_id
AND srm.menu_id BETWEEN 179 AND 194
GROUP BY srm.role_id;

-- 7. 详细菜单清单
SELECT
    sm.id as 'ID',
    sm.pid as '父ID',
    sm.type as '类型',
    sm.name as '菜单名',
    sm.perms as '权限标识',
    sm.paths as '路由',
    CASE
        WHEN srm.id IS NULL THEN '❌ 未分配'
        ELSE '✅ 已分配'
    END as '分配状态'
FROM la_system_menu sm
LEFT JOIN la_system_role_menu srm ON sm.id = srm.menu_id AND srm.role_id = @admin_role_id
WHERE sm.id BETWEEN 179 AND 194
ORDER BY sm.id;

-- ========================================
-- 执行说明:
-- 1. 此脚本会自动找到超级管理员角色
-- 2. 删除旧的菜单关联(防止重复)
-- 3. 批量插入盘口管理的16个菜单项
-- 4. 验证分配结果
--
-- 执行后需要:
-- - 用户重新登录
-- - 清除前端缓存
-- - 刷新浏览器
-- ========================================
