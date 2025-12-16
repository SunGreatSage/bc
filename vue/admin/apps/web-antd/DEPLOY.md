# Nginx 部署指南

## 一、构建项目

已配置生产环境API地址：http://156.245.144.78:66/api

构建命令已执行，生成的文件在 `dist` 目录。

## 二、部署到 Nginx

### 方式1：直接部署（推荐）

1. 将 `dist` 目录下的所有文件上传到服务器的 nginx 根目录（如：`/usr/share/nginx/html`）

2. 复制 nginx 配置：
```bash
# 复制配置文件到 nginx 配置目录
cp nginx.conf /etc/nginx/conf.d/vben-admin.conf

# 测试配置
nginx -t

# 重启 nginx
systemctl restart nginx
# 或
nginx -s reload
```

### 方式2：使用 Docker 部署

1. 创建 Dockerfile：
```dockerfile
FROM nginx:alpine

# 复制构建文件
COPY dist /usr/share/nginx/html

# 复制nginx配置
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

2. 构建并运行：
```bash
# 构建镜像
docker build -t vben-admin .

# 运行容器
docker run -d -p 80:80 --name vben-admin vben-admin
```

## 三、配置说明

### nginx.conf 配置要点：

1. **根目录**：`root /usr/share/nginx/html;`
   - 修改为你的实际部署路径

2. **端口**：`listen 80;`
   - 可修改为其他端口，如 8080

3. **域名**：`server_name localhost;`
   - 修改为你的实际域名，如 `admin.example.com`

4. **前端路由支持**：
   - `try_files $uri $uri/ /index.html;` 确保前端路由正常工作

5. **静态资源缓存**：
   - JS/CSS/图片等资源缓存1年，提升访问速度

### 生产环境配置

当前生产环境配置（`.env.production`）：
- API地址：`http://156.245.144.78:66/api`
- 路由模式：`hash`（使用 # 号路由，更兼容）
- Base路径：`/`

## 四、访问地址

部署完成后，通过以下地址访问：
- 本地：http://localhost
- 域名：http://你的域名

默认登录信息请查看后端接口文档。

## 五、常见问题

### 1. 404 错误
- 检查 nginx 配置中的 `try_files` 指令是否正确
- 确认 `root` 路径指向 dist 目录

### 2. 接口跨域问题
如果遇到跨域，可以在 nginx 配置中添加：
```nginx
location /api {
    proxy_pass http://156.245.144.78:66/api;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;

    # CORS 头
    add_header Access-Control-Allow-Origin *;
    add_header Access-Control-Allow-Methods 'GET, POST, OPTIONS, PUT, DELETE';
    add_header Access-Control-Allow-Headers 'DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Authorization';
}
```

### 3. 静态资源加载失败
- 检查 VITE_BASE 配置是否正确
- 确认 nginx root 路径配置正确

## 六、性能优化建议

1. **启用 gzip 压缩**（已在配置中）
2. **配置 CDN**：将静态资源上传到 CDN
3. **启用 HTTP/2**：提升加载速度
4. **配置 SSL/TLS**：使用 HTTPS（生产环境必须）

## 七、SSL 配置（HTTPS）

如需启用 HTTPS，修改 nginx.conf：
```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    # 其他配置保持不变...
}

# HTTP 自动跳转 HTTPS
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```
