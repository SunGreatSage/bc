#!/bin/bash

# 控盘管理系统 - 部署脚本
# API地址: http://156.245.144.78:66

echo "========================================"
echo "  控盘管理系统 - 部署脚本"
echo "========================================"
echo ""

# 颜色定义
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 部署目录（可修改为你的实际部署目录）
DEPLOY_DIR="/usr/share/nginx/html"
NGINX_CONF="/etc/nginx/conf.d/vben-admin.conf"

# 1. 检查 nginx 是否安装
echo -e "${BLUE}[1/5]${NC} 检查 Nginx..."
if ! command -v nginx &> /dev/null; then
    echo -e "${RED}错误: 未安装 Nginx${NC}"
    echo "请先安装 Nginx:"
    echo "  Ubuntu/Debian: sudo apt-get install nginx"
    echo "  CentOS/RHEL: sudo yum install nginx"
    exit 1
fi
echo -e "${GREEN}✓${NC} Nginx 已安装"
echo ""

# 2. 备份现有文件
echo -e "${BLUE}[2/5]${NC} 备份现有文件..."
if [ -d "$DEPLOY_DIR" ]; then
    BACKUP_DIR="${DEPLOY_DIR}_backup_$(date +%Y%m%d_%H%M%S)"
    echo "备份目录: $BACKUP_DIR"
    sudo cp -r "$DEPLOY_DIR" "$BACKUP_DIR"
    echo -e "${GREEN}✓${NC} 备份完成"
else
    echo "目录不存在，跳过备份"
fi
echo ""

# 3. 部署文件
echo -e "${BLUE}[3/5]${NC} 部署应用文件..."
echo "清理部署目录..."
sudo rm -rf ${DEPLOY_DIR}/*

echo "解压文件到部署目录..."
if [ -f "dist.zip" ]; then
    sudo unzip -q dist.zip -d /tmp/vben-admin-temp
    sudo cp -r /tmp/vben-admin-temp/* "$DEPLOY_DIR/"
    sudo rm -rf /tmp/vben-admin-temp
    echo -e "${GREEN}✓${NC} 文件部署完成"
elif [ -d "dist" ]; then
    sudo cp -r dist/* "$DEPLOY_DIR/"
    echo -e "${GREEN}✓${NC} 文件部署完成"
else
    echo -e "${RED}错误: 找不到 dist 目录或 dist.zip 文件${NC}"
    exit 1
fi
echo ""

# 4. 配置 Nginx
echo -e "${BLUE}[4/5]${NC} 配置 Nginx..."
if [ -f "nginx.conf" ]; then
    sudo cp nginx.conf "$NGINX_CONF"
    echo -e "${GREEN}✓${NC} Nginx 配置已更新"
else
    echo -e "${RED}警告: 找不到 nginx.conf 文件${NC}"
fi

# 测试 nginx 配置
echo "测试 Nginx 配置..."
if sudo nginx -t 2>&1 | grep -q "successful"; then
    echo -e "${GREEN}✓${NC} Nginx 配置测试通过"
else
    echo -e "${RED}错误: Nginx 配置测试失败${NC}"
    sudo nginx -t
    exit 1
fi
echo ""

# 5. 重启 Nginx
echo -e "${BLUE}[5/5]${NC} 重启 Nginx..."
if sudo systemctl restart nginx 2>/dev/null || sudo service nginx restart 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Nginx 重启成功"
else
    echo -e "${RED}错误: Nginx 重启失败${NC}"
    exit 1
fi
echo ""

# 完成
echo "========================================"
echo -e "${GREEN}部署完成！${NC}"
echo "========================================"
echo ""
echo "访问地址:"
echo "  本地: http://localhost"
echo "  服务器: http://your-server-ip"
echo ""
echo "API地址: http://156.245.144.78:66"
echo ""
echo "如有问题，请查看日志:"
echo "  sudo tail -f /var/log/nginx/error.log"
echo "  sudo tail -f /var/log/nginx/access.log"
