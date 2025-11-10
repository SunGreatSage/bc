<?php
/**
 * 用户退出登录（PC端）
 * 清除Session并跳转到登录页面
 */

// 启动Session
session_start();

// 清除所有Session变量
$_SESSION = array();

// 如果使用了Cookie来保存Session ID，也删除Cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// 销毁Session
session_destroy();

// 跳转到登录页面
header("Location: login.php");
exit;
?>
