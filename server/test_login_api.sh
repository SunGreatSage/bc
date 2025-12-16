#!/bin/bash
# 测试彩票登录API
# 使用方法: bash test_login_api.sh

echo "=========================================="
echo "  彩票登录API测试脚本"
echo "=========================================="
echo ""

# 配置
API_URL="http://localhost:8000/api/lottery_login/login"
USERNAME="test001"
PASSWORD="123456"

echo "📡 测试接口: $API_URL"
echo "👤 用户名: $USERNAME"
echo "🔑 密码: $PASSWORD"
echo ""
echo "⏳ 发送请求中..."
echo ""

# 发送POST请求
response=$(curl -s -X POST "$API_URL" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=$USERNAME&password=$PASSWORD&terminal=1")

echo "📥 响应结果:"
echo "$response" | python -m json.tool 2>/dev/null || echo "$response"
echo ""

# 检查响应
if echo "$response" | grep -q '"code":1'; then
    echo "✅ 登录成功！"

    # 提取token
    token=$(echo "$response" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)
    if [ ! -z "$token" ]; then
        echo "🎫 Token: $token"
        echo ""
        echo "📋 可以用这个token测试投注接口:"
        echo "curl -X POST http://localhost:8000/api/lottery_bet/placeBet \\"
        echo "  -H \"token: $token\" \\"
        echo "  -d \"gid=200&qishu=2025001&pid=21401&bet_content=08&bet_amount=100\""
    fi
else
    echo "❌ 登录失败！"
    echo "请检查:"
    echo "1. PHP后端服务是否启动 (php think run -p 8000)"
    echo "2. 数据库连接是否正确"
    echo "3. 测试用户是否已创建 (运行 test_login_user.sql)"
fi

echo ""
echo "=========================================="
