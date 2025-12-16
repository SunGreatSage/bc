-- ========================================
-- 盘口管理菜单权限分配(简化版)
-- 数据库: newbc
-- ========================================

USE newbc;

-- 1. 查询并设置超级管理员角色ID
SET @role_id = (SELECT id FROM la_system_role WHERE is_super = 1 LIMIT 1);

-- 2. 删除旧的关联
DELETE FROM la_system_role_menu
WHERE role_id = @role_id AND menu_id BETWEEN 179 AND 194;

-- 3. 批量插入菜单权限
INSERT INTO la_system_role_menu (role_id, menu_id, create_time)
SELECT @role_id, id, UNIX_TIMESTAMP()
FROM la_system_menu
WHERE id BETWEEN 179 AND 194;

-- 4. 验证结果
SELECT
    '分配完成' as status,
    @role_id as role_id,
    COUNT(*) as menu_count
FROM la_system_role_menu
WHERE role_id = @role_id AND menu_id BETWEEN 179 AND 194;
