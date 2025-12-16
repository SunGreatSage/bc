<?php
/**
 * 执行菜单权限分配脚本
 * 用途: 将盘口管理菜单分配给超级管理员角色
 */

// 数据库配置
$host = '127.0.0.1';
$port = 3306;
$dbname = 'lhc_oa';
$user = 'lhc_oa';
$pass = 'JH4ctk4mJBNxmhw5';

try {
    // 连接数据库
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "✅ 数据库连接成功\n\n";

    // 1. 查询超级管理员角色
    $stmt = $pdo->query("SELECT id, name, is_super FROM la_system_role WHERE is_super = 1 LIMIT 1");
    $adminRole = $stmt->fetch();

    if (!$adminRole) {
        die("❌ 错误: 未找到超级管理员角色\n");
    }

    $roleId = $adminRole['id'];
    $roleName = $adminRole['name'];

    echo "📋 角色信息:\n";
    echo "  - ID: {$roleId}\n";
    echo "  - 名称: {$roleName}\n";
    echo "  - 超管: " . ($adminRole['is_super'] ? '是' : '否') . "\n\n";

    // 2. 检查已存在的菜单关联
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count
        FROM la_system_role_menu
        WHERE role_id = ? AND menu_id BETWEEN 179 AND 194
    ");
    $stmt->execute([$roleId]);
    $existCount = $stmt->fetch()['count'];

    echo "🔍 检查现有关联: {$existCount} 个菜单已分配\n";

    if ($existCount > 0) {
        echo "🗑️ 删除旧的关联...\n";
        $stmt = $pdo->prepare("
            DELETE FROM la_system_role_menu
            WHERE role_id = ? AND menu_id BETWEEN 179 AND 194
        ");
        $stmt->execute([$roleId]);
        echo "   已删除 {$existCount} 条记录\n\n";
    }

    // 3. 批量插入新的菜单权限
    echo "➕ 分配盘口管理菜单权限...\n";

    $stmt = $pdo->prepare("
        INSERT INTO la_system_role_menu (role_id, menu_id, create_time)
        SELECT ?, id, UNIX_TIMESTAMP()
        FROM la_system_menu
        WHERE id BETWEEN 179 AND 194
    ");
    $stmt->execute([$roleId]);
    $insertCount = $stmt->rowCount();

    echo "   成功分配 {$insertCount} 个菜单\n\n";

    // 4. 验证分配结果
    echo "✅ 分配结果验证:\n";
    $stmt = $pdo->prepare("
        SELECT
            sm.id,
            sm.pid,
            sm.type,
            sm.name,
            sm.perms,
            sm.paths,
            CASE WHEN srm.id IS NULL THEN '❌' ELSE '✅' END as status
        FROM la_system_menu sm
        LEFT JOIN la_system_role_menu srm ON sm.id = srm.menu_id AND srm.role_id = ?
        WHERE sm.id BETWEEN 179 AND 194
        ORDER BY sm.id
    ");
    $stmt->execute([$roleId]);
    $menus = $stmt->fetchAll();

    echo "\n";
    echo "ID  | PID | 类型 | 菜单名称        | 权限标识                    | 路由        | 状态\n";
    echo str_repeat("-", 100) . "\n";

    foreach ($menus as $menu) {
        printf(
            "%-3s | %-3s | %-4s | %-15s | %-27s | %-11s | %s\n",
            $menu['id'],
            $menu['pid'],
            $menu['type'],
            mb_substr($menu['name'], 0, 15),
            mb_substr($menu['perms'], 0, 27),
            mb_substr($menu['paths'], 0, 11),
            $menu['status']
        );
    }

    echo "\n";
    echo "🎉 菜单权限分配完成!\n\n";
    echo "📌 下一步操作:\n";
    echo "   1. 退出管理后台\n";
    echo "   2. 清除浏览器缓存\n";
    echo "   3. 重新登录\n";
    echo "   4. 查看左侧菜单是否出现 '盘口管理'\n";

} catch (PDOException $e) {
    echo "❌ 数据库错误: " . $e->getMessage() . "\n";
    exit(1);
}
