# hide 模块 - 系统管理员后台

[根目录](../CLAUDE.md) > **hide**

> 最后更新：2025-11-09 14:50:39

---

## 变更记录 (Changelog)

### 2025-11-09 14:50:39
- 初始化 hide 模块文档
- 记录管理员后台核心功能与入口

---

## 模块职责

**hide** 模块是系统最高权限的管理员后台，负责：
1. 用户管理（查询、新增、编辑、删除、积分调整）
2. 代理商管理（层级结构、权限分配）
3. 订单管理（投注记录、审核、撤单）
4. 开奖管理（手动开奖、自动开奖、历史开奖）
5. 游戏管理（玩法配置、赔率设置、盘口控制）
6. 系统配置（站点设置、支付通道、邮件配置）
7. 报表统计（盈亏报表、资金流水、在线用户）
8. 安全管理（登录日志、异常监控、IP 黑名单）

---

## 入口与启动

### 主要入口文件

- **`index.php`** - 重定向到登录页
- **`login.php`** - 管理员登录
- **`checklogin.php`** - 登录鉴权中间件
- **`top.php`** - 后台顶部导航
- **`left.php`** - 后台左侧菜单

### 启动流程

1. 访问 `http://domain/hide/`
2. `index.php` 包含 `login.php`
3. 验证 Session 中的 `$_SESSION['adminid']`
4. 未登录 → 显示登录表单
5. 已登录 → 加载 `top.php` + `left.php` + 内容页（frameset 结构）

---

## 对外接口

### 核心功能页面

| 功能模块 | 文件路径 | 描述 |
|---------|---------|------|
| 用户管理 | `suser.php` | 用户列表、搜索、编辑、删除 |
| 用户详情 | `user.php` | 查看用户详细信息 |
| 积分调整 | `points.php` | 手动给用户加/减积分 |
| 代理管理 | `admins.php` | 代理商列表、层级关系 |
| 订单管理 | `lib.php` | 投注记录查询 |
| 开奖管理 | `kj.php` | 手动录入开奖结果 |
| 游戏配置 | `game.php` | 玩法开关、赔率设置 |
| 盘口管理 | `caopan.php` | 实时控盘（限注、封盘） |
| 系统配置 | `sysconfig.php` | 全局参数设置 |
| 站点配置 | `webconfig.php` | 多站点管理 |
| 报表统计 | `data.php` | 盈亏报表、投注统计 |
| 在线用户 | `online.php` | 实时在线用户列表 |
| 消息管理 | `message.php` | 站内消息、公告 |
| 邮箱管理 | `mailbox.php` | 邮件发送记录 |

### API 接口

- **POST `/hide/checklogin.php`** - 登录验证
- **POST `/hide/check.php`** - AJAX 表单提交处理
- **GET `/hide/fly.php`** - 飞单管理（未中奖订单自动撤单）

---

## 关键依赖与配置

### 依赖文件

```php
include('../data/comm.inc.php');      // 公共配置（数据库、Session）
include('../data/myadminvar.php');    // 管理员变量定义
include('../func/func.php');          // 通用函数库
include('../func/adminfunc.php');     // 管理员专用函数
include('../include.php');            // Smarty 模板引擎初始化
include('./checklogin.php');          // 登录鉴权
```

### 配置文件

- **`/data/myadminvar.php`** - 管理员权限变量（如 `$skin`、`$smartytpl` 等）
- **Smarty 模板目录**：`/templates/default/hide/`

### 权限控制

- Session 变量：`$_SESSION['adminid']` - 管理员 ID
- Session 变量：`$_SESSION['level']` - 管理员权限级别（1=超级管理员，2=普通管理员）

---

## 数据模型

### 核心数据表

| 表名 | 变量名 | 描述 |
|------|--------|------|
| `x_admins` | `$tb_admins` | 管理员账号表 |
| `x_admins_login` | `$tb_admins_login` | 管理员登录日志 |
| `x_admins_page` | `$tb_admins_page` | 管理员操作日志 |
| `x_user` | `$tb_user` | 用户账号表 |
| `x_order` | `$tb_order` | 订单表（已弃用？） |
| `x_lib` | `$tb_lib` | 投注记录表 |
| `x_kj` | `$tb_kj` | 开奖记录表 |
| `x_game` | `$tb_game` | 游戏配置表 |
| `x_config` | `$tb_config` | 系统配置表 |
| `x_points` | `$tb_points` | 用户积分表 |
| `x_money_log` | `$tb_money_log` | 资金流水表 |

### 关键字段示例（x_user 表）

- `userid` - 用户 ID（8 位数字）
- `username` - 用户名
- `password` - 密码（MD5 加密）
- `points` - 账户余额
- `agentid` - 上级代理 ID
- `status` - 账户状态（0=正常，1=冻结）

---

## 测试与质量

### 当前测试覆盖

❌ 无自动化测试

### 手动测试检查清单

- [ ] 管理员登录（正确密码、错误密码）
- [ ] 用户列表分页
- [ ] 用户积分调整（加/减）
- [ ] 开奖录入（手动开奖）
- [ ] 赔率修改
- [ ] 订单查询（按日期、用户、游戏筛选）
- [ ] 报表统计（盈亏计算）

### 已知问题

1. **安全隐患**：
   - 使用 MD5 存储密码（建议升级到 bcrypt）
   - 部分页面缺少 CSRF Token

2. **性能问题**：
   - 大数据量查询缺少索引（如 `x_lib` 表按日期查询）
   - 未使用缓存（每次查询都访问数据库）

3. **代码质量**：
   - 部分函数过长（超过 100 行）
   - 缺少注释

---

## 常见问题 (FAQ)

### Q1：如何重置管理员密码？
A：直接修改数据库 `x_admins` 表的 `password` 字段，使用 MD5 加密。
   例如密码 `123456` 的 MD5 为 `e10adc3949ba59abbe56e057f20f883e`

### Q2：如何开启/关闭某个游戏？
A：访问 `game.php`，找到对应游戏，修改 `status` 字段（0=关闭，1=开启）

### Q3：报表统计不准确怎么办？
A：检查 `x_lib` 表的 `prize` 字段（中奖金额）和 `points` 字段（投注金额）是否正确

### Q4：如何备份数据？
A：使用 `backdata.class.php` 类，或使用 phpMyAdmin 导出 SQL

---

## 相关文件清单

### PHP 核心文件（按功能分类）

**登录与鉴权**：
- `login.php` - 登录页面
- `checklogin.php` - 登录验证中间件
- `check.php` - AJAX 处理

**用户管理**：
- `suser.php` - 用户列表
- `user.php` - 用户详情
- `points.php` - 积分调整

**订单与开奖**：
- `lib.php` - 订单查询
- `kj.php` - 开奖管理
- `caopan.php` - 盘口控制

**系统配置**：
- `sysconfig.php` - 系统设置
- `webconfig.php` - 站点设置
- `game.php` - 游戏配置

**报表与统计**：
- `data.php` - 数据报表
- `online.php` - 在线用户
- `money.php` - 资金统计

### Smarty 模板文件

位于 `/templates/default/hide/`：
- `login.html` - 登录页
- `suser.html` - 用户列表
- `kj.html` - 开奖管理
- `game.html` - 游戏配置
- `sysconfig.html` - 系统设置

---

## 下一步优化建议

### 高优先级
1. 🔒 密码加密升级（MD5 → bcrypt）
2. 🛡️ 添加 CSRF Token 防护
3. 📊 数据库索引优化（x_lib 表按日期查询）

### 中优先级
1. 📝 为核心函数添加注释
2. 🧪 编写关键业务的单元测试
3. 🚀 引入 Redis 缓存（用户信息、配置缓存）

### 低优先级
1. 🎨 UI 现代化（Bootstrap 5）
2. 📱 移动端适配（响应式布局）

---

**模块版本**：v1.0
**文档生成**：Claude AI
**最后更新**：2025-11-09 14:50:39
