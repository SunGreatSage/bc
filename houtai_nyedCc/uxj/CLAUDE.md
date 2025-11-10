# uxj 模块 - PC 用户前台

[根目录](../CLAUDE.md) > **uxj**

> 最后更新：2025-11-09 14:50:39

---

## 变更记录 (Changelog)

### 2025-11-09 14:50:39
- 初始化 uxj 模块文档
- 记录 PC 端用户前台核心功能

---

## 模块职责

**uxj** 模块是 PC 端用户前台界面，负责：
1. 用户注册与登录
2. 投注下单（选号、快速投注、龙虎斗等）
3. 投注记录查询
4. 开奖结果查看
5. 账户管理（修改密码、查看余额）
6. 资金操作（充值、提款）
7. 个人中心（用户信息、交易记录）
8. 玩法规则说明

---

## 入口与启动

### 主要入口文件

- **`index.php`** - 重定向到登录页
- **`login.php`** - 用户登录
- **`reg.php`** - 用户注册
- **`checklogin.php`** - 登录鉴权中间件
- **`top.php`** - 顶部导航
- **`long.php`** - 投注下单（龙虎斗等）
- **`bao.php`** - 投注下单（包号）
- **`lib.php`** - 投注记录查询

### 启动流程

1. 访问 `http://domain/uxj/`
2. `index.php` 重定向到 `login.php`
3. 验证 Session 中的 `$_SESSION['uuid']`（用户 ID）
4. 未登录 → 显示登录表单
5. 已登录 → 跳转到投注页面（`long.php` 或 `bao.php`）

---

## 对外接口

### 核心功能页面

| 功能模块 | 文件路径 | 描述 |
|---------|---------|------|
| 用户登录 | `login.php` | 登录表单 + 验证 |
| 用户注册 | `reg.php` | 注册表单（如果开放注册） |
| 投注下单（龙虎） | `long.php` | 龙虎斗、时时彩等玩法 |
| 投注下单（包号） | `bao.php` | 快速包号投注 |
| 投注记录 | `lib.php` | 查询个人投注记录 |
| 开奖结果 | `xy.php` | 查看历史开奖 |
| 个人中心 | `member.php` | 用户信息、余额 |
| 修改密码 | `changepass2.php` | 修改登录密码 |
| 账户报表 | `report.php` | 盈亏统计 |
| 玩法规则 | `rule.php` | 各游戏玩法说明 |
| 在线客服 | `时间.php` | 客服信息展示 |

### API 接口

- **POST `/uxj/check.php`** - AJAX 下单处理
- **POST `/uxj/getlogin.php`** - 获取登录状态
- **GET `/uxj/time.php`** - 获取服务器时间（用于倒计时）

---

## 关键依赖与配置

### 依赖文件

```php
include('../data/comm.inc.php');      // 公共配置（数据库、Session）
include('../data/uservar.php');       // 用户变量定义
include('../func/func.php');          // 通用函数库
include('../func/userfunc.php');      // 用户专用函数
include('../func/csfunc.php');        // 彩票业务函数
include('../include.php');            // Smarty 模板引擎初始化
include('./checklogin.php');          // 登录鉴权
```

### 配置文件

- **`/data/uservar.php`** - 用户变量（如 `$skin`、`$smartytpl` 等）
- **Smarty 模板目录**：`/templates/default/uxj/`

### 权限控制

- Session 变量：`$_SESSION['uuid']` - 用户 ID（8 位数字）
- Session 变量：`$_SESSION['wid']` - 站点 ID
- Session 变量：`$_SESSION['mobi']` - 是否为移动端（1=是，0=否）

---

## 数据模型

### 核心数据表

| 表名 | 用途 | 关键字段 |
|------|------|---------|
| `x_user` | 用户账号 | `userid`, `username`, `password`, `points`, `agentid` |
| `x_user_login` | 登录日志 | `userid`, `logintime`, `ip` |
| `x_lib` | 投注记录 | `tid`, `userid`, `qishu`, `points`, `prize`, `z` |
| `x_kj` | 开奖记录 | `qishu`, `kj1-kj8`, `dates`, `gid` |
| `x_points` | 积分调整 | `userid`, `points`, `time`, `remark` |
| `x_money_log` | 资金流水 | `userid`, `amount`, `mtype`, `status` |
| `x_bank` | 银行卡绑定 | `userid`, `bankname`, `banknumber` |

### 投注流程

1. 用户选择游戏 → 选择玩法 → 输入号码/金额
2. 提交到 `check.php` 进行验证：
   - 检查用户余额
   - 检查是否封盘
   - 检查投注金额是否在限额内
   - 检查赔率是否正常
3. 验证通过 → 扣除余额 → 写入 `x_lib` 表
4. 返回投注成功消息

---

## 测试与质量

### 当前测试覆盖

❌ 无自动化测试

### 手动测试检查清单

- [ ] 用户注册（用户名重复、密码强度）
- [ ] 用户登录（正确密码、错误密码、验证码）
- [ ] 投注下单（正常投注、余额不足、封盘时投注）
- [ ] 投注记录查询（按日期、按游戏筛选）
- [ ] 开奖查看（历史开奖）
- [ ] 修改密码（旧密码验证）
- [ ] 充值/提款（金额验证）

### 已知问题

1. **安全隐患**：
   - 密码使用 MD5 存储（建议升级到 bcrypt）
   - 部分表单缺少 CSRF Token

2. **用户体验**：
   - 投注界面刷新较慢（建议使用 AJAX 异步提交）
   - 倒计时不准确（依赖客户端时间）

3. **代码质量**：
   - JavaScript 代码混乱（缺少模块化）
   - 部分页面存在重复代码

---

## 常见问题 (FAQ)

### Q1：如何重置用户密码？
A：管理员在后台 `hide/suser.php` 中修改，或用户通过邮箱找回（如果配置了邮件功能）

### Q2：投注失败怎么办？
A：检查以下几点：
   - 账户余额是否足够
   - 当前期号是否已封盘
   - 投注金额是否超过限额
   - 玩法是否已关闭

### Q3：开奖时间不准确？
A：检查服务器时间是否正确（`time.php` 返回的时间）

---

## 相关文件清单

### PHP 核心文件（按功能分类）

**登录与注册**：
- `login.php` - 登录页面
- `reg.php` - 注册页面
- `checklogin.php` - 登录验证中间件
- `getlogin.php` - 获取登录状态（AJAX）

**投注下单**：
- `long.php` - 龙虎斗等玩法投注
- `bao.php` - 包号投注
- `makeloto.php` - 生成投注单
- `makelib.php` - 写入投注记录

**查询与统计**：
- `lib.php` - 投注记录查询
- `xy.php` - 开奖结果查看
- `report.php` - 盈亏报表

**账户管理**：
- `member.php` - 个人中心
- `changepass.php` - 修改密码（旧版）
- `changepass2.php` - 修改密码（新版）
- `userinfo.php` - 用户信息

**其他**：
- `top.php` - 顶部导航
- `longs.php` - 龙虎斗（简化版）
- `time.php` - 服务器时间
- `rule.php` - 玩法规则

### Smarty 模板文件

位于 `/templates/default/uxj/`：
- `login.html` - 登录页
- `longloto.html` - 龙虎斗投注界面
- `libloto.html` - 投注记录
- `kj.html` - 开奖结果
- `mem_uinfo.html` - 用户信息
- `changepassword2.html` - 修改密码

### JavaScript 文件

- `/js/default/jsuxj/` - 用户前台 JS 脚本

---

## 下一步优化建议

### 高优先级
1. 🔒 密码加密升级（MD5 → bcrypt）
2. 🛡️ 添加 CSRF Token 防护
3. 🚀 投注改用 AJAX 异步提交

### 中优先级
1. 📝 优化 JavaScript 代码（模块化、压缩）
2. 🧪 编写关键业务的单元测试
3. 🎨 UI 现代化（Bootstrap 5）

### 低优先级
1. 📱 移动端适配（响应式布局）
2. 🔔 添加实时通知（WebSocket）

---

**模块版本**：v1.0
**文档生成**：Claude AI
**最后更新**：2025-11-09 14:50:39
