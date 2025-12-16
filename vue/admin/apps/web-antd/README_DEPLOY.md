# 控盘管理系统 - 部署文档

## 📦 项目信息

- **项目名称**: 控盘管理系统
- **API地址**: http://156.245.144.78:66
- **构建状态**: ✅ 已成功编译
- **构建文件**:
  - `dist/` - 构建产物目录
  - `dist.zip` - 压缩包（1.2MB）

## 🚀 快速部署

### 方式一：使用 Shell 脚本（推荐）

```bash
# 1. 上传文件到服务器
# 上传 dist.zip, nginx.conf, deploy.sh 到服务器

# 2. 赋予执行权限
chmod +x deploy.sh

# 3. 执行部署脚本
sudo ./deploy.sh
```

### 方式二：手动部署

```bash
# 1. 解压文件
unzip dist.zip -d /usr/share/nginx/html/

# 2. 配置 nginx
cp nginx.conf /etc/nginx/conf.d/vben-admin.conf

# 3. 测试配置
nginx -t

# 4. 重启 nginx
systemctl restart nginx
```

### 方式三：Docker 部署

```bash
# 1. 构建镜像
docker build -t vben-admin:latest .

# 2. 运行容器
docker run -d -p 80:80 --name vben-admin vben-admin:latest

# 或使用 docker-compose
docker-compose up -d
```

## 📋 部署文件清单

```
apps/web-antd/
├── dist/                  # 构建产物目录
│   ├── index.html        # 入口HTML
│   ├── js/               # JavaScript文件
│   ├── css/              # 样式文件
│   └── ...
├── dist.zip              # 压缩包（推荐传输）
├── nginx.conf            # Nginx配置文件
├── Dockerfile            # Docker镜像配置
├── docker-compose.yml    # Docker Compose配置
├── deploy.sh             # 自动部署脚本
├── DEPLOY.md             # 详细部署文档
└── README_DEPLOY.md      # 本文件
```

## ⚙️ 配置说明

### API 地址配置

已在构建时配置：
```
VITE_GLOB_API_URL=http://156.245.144.78:66/api
```

### Nginx 配置要点

1. **端口**: 默认 80
2. **根目录**: `/usr/share/nginx/html`
3. **路由模式**: Hash 模式（兼容性好）
4. **Gzip**: 已启用
5. **缓存策略**: 静态资源缓存1年

### 修改端口

编辑 `nginx.conf`：
```nginx
server {
    listen 8080;  # 修改为你想要的端口
    # ...
}
```

## 🌐 访问地址

部署完成后通过以下地址访问：

- **本地访问**: http://localhost
- **服务器访问**: http://你的服务器IP
- **域名访问**: http://你的域名（需配置DNS）

## 🔒 HTTPS 配置（可选）

### 使用 Let's Encrypt 免费证书

```bash
# 1. 安装 certbot
apt-get install certbot python3-certbot-nginx  # Ubuntu/Debian
yum install certbot python3-certbot-nginx      # CentOS

# 2. 获取证书
certbot --nginx -d yourdomain.com

# 3. 自动续期
certbot renew --dry-run
```

### 手动配置 SSL

编辑 `nginx.conf` 添加：
```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /path/to/fullchain.pem;
    ssl_certificate_key /path/to/privkey.pem;

    # 其他配置...
}

# HTTP 跳转 HTTPS
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

## 🐛 故障排查

### 1. 页面 404 错误

**原因**: Nginx 配置问题

**解决**:
```bash
# 检查 nginx 配置
nginx -t

# 检查文件权限
ls -la /usr/share/nginx/html

# 查看错误日志
tail -f /var/log/nginx/error.log
```

### 2. 接口跨域问题

**原因**: API 服务器未配置 CORS

**解决**: 在 `nginx.conf` 中添加代理：
```nginx
location /api {
    proxy_pass http://156.245.144.78:66/api;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;

    # CORS 头
    add_header Access-Control-Allow-Origin *;
    add_header Access-Control-Allow-Methods 'GET, POST, OPTIONS, PUT, DELETE';
    add_header Access-Control-Allow-Headers 'Authorization, Content-Type';
}
```

### 3. 静态资源加载失败

**检查项**:
- 确认 dist 目录完整上传
- 检查文件权限（建议 755）
- 查看浏览器控制台错误
- 检查 Nginx root 路径配置

### 4. Docker 容器无法启动

```bash
# 查看日志
docker logs vben-admin

# 检查端口占用
netstat -tuln | grep 80

# 重新构建
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## 📊 性能优化

### 1. 启用 HTTP/2

```nginx
listen 443 ssl http2;
```

### 2. 配置 CDN

将静态资源上传到 CDN，修改资源引用路径。

### 3. 配置缓存

已在 `nginx.conf` 中配置：
```nginx
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

### 4. 启用 Brotli 压缩

```nginx
# 安装 nginx-module-brotli
apt-get install libnginx-mod-http-brotli

# 在 nginx.conf 中添加
brotli on;
brotli_comp_level 6;
brotli_types text/plain text/css application/json application/javascript;
```

## 🔐 安全建议

1. **使用 HTTPS**: 生产环境必须使用 HTTPS
2. **配置防火墙**: 只开放必要端口
3. **定期更新**: 及时更新 Nginx 和系统补丁
4. **访问控制**: 配置 IP 白名单（如需要）
5. **请求限制**: 防止 DDoS 攻击

```nginx
# 限制请求速率
limit_req_zone $binary_remote_addr zone=mylimit:10m rate=10r/s;
location / {
    limit_req zone=mylimit burst=20;
}
```

## 📱 监控和日志

### 查看日志

```bash
# 访问日志
tail -f /var/log/nginx/access.log

# 错误日志
tail -f /var/log/nginx/error.log

# Docker 日志
docker logs -f vben-admin
```

### 监控状态

```bash
# Nginx 状态
systemctl status nginx

# 容器状态
docker ps
docker stats vben-admin
```

## 🔄 更新部署

### 更新流程

1. **拉取新版本**
```bash
# 获取新的 dist.zip
```

2. **备份当前版本**
```bash
cp -r /usr/share/nginx/html /usr/share/nginx/html.backup
```

3. **部署新版本**
```bash
unzip -o dist.zip -d /usr/share/nginx/html/
```

4. **重启服务**
```bash
systemctl reload nginx
```

### 回滚

```bash
# 恢复备份
rm -rf /usr/share/nginx/html
mv /usr/share/nginx/html.backup /usr/share/nginx/html
systemctl reload nginx
```

## 📞 技术支持

如遇到问题，请检查：

1. ✅ Nginx 配置是否正确
2. ✅ 文件权限是否正确
3. ✅ 端口是否被占用
4. ✅ 防火墙是否放行
5. ✅ API 接口是否可访问

## 📝 版本信息

- **构建日期**: 2025-12-01
- **Node版本**: >= 20.12.0
- **Nginx版本**: >= 1.18.0
- **浏览器支持**: Chrome, Firefox, Safari, Edge (最新版本)

---

**注意事项**:
- 生产环境建议使用 HTTPS
- 定期备份数据和配置
- 监控服务器资源使用情况
- 保持系统和依赖包更新
