@echo off
chcp 65001 >nul
REM 测试彩票登录API (Windows版本)
REM 使用方法: 双击运行 test_login_api.bat

echo ==========================================
echo   彩票登录API测试脚本
echo ==========================================
echo.

REM 配置
set API_URL=http://localhost:8000/api/lottery_login/login
set USERNAME=test001
set PASSWORD=123456

echo 📡 测试接口: %API_URL%
echo 👤 用户名: %USERNAME%
echo 🔑 密码: %PASSWORD%
echo.
echo ⏳ 发送请求中...
echo.

REM 发送POST请求
curl -s -X POST "%API_URL%" -H "Content-Type: application/x-www-form-urlencoded" -d "username=%USERNAME%&password=%PASSWORD%&terminal=1"

echo.
echo.
echo ==========================================
echo 如果看到 "code":1 表示登录成功！
echo 如果看到错误信息，请检查：
echo 1. PHP后端服务是否启动
echo 2. 数据库连接是否正确
echo 3. 测试用户是否已创建
echo ==========================================
echo.

pause
