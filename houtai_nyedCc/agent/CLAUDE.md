# agent 模块 - 代理商后台

[根目录](../CLAUDE.md) > **agent**

> 最后更新：2025-11-09 14:50:39

---

## 变更记录 (Changelog)

### 2025-11-09 14:50:39
- 初始化 agent 模块文档
- 记录代理商后台核心功能

---

## 模块职责

**agent** 模块是代理商后台管理系统，负责：
1. 下级用户管理（查询、新增、编辑）
2. 下级投注记录查看
3. 代理盈亏报表（统计下级输赢）
4. 代理佣金计算
5. 下级用户积分调整（加/减积分）
6. 代理账户管理（修改密码、查看余额）
7. 下级充值/提款审核（如果有权限）

**权限说明**：
- 代理只能查看和管理自己的下级用户
- 不能查看其他代理的用户
- 不能修改系统配置和赔率

---

## 入口与启动

### 主要入口文件

- **`index.php`** - 重定向到登录页
- **`login.php`** - 代理登录
- **`checklogin.php`** - 登录鉴权中间件
- **`top.php`** - 顶部导航
- **`suser.php`** - 下级用户管理
- **`report.php`** - 盈亏报表
- **`account.php`** - 账户管理

### 启动流程

1. 访问 `http://domain/agent/`
2. `index.php` 重定向到 `login.php`
3. 验证 Session 中的 `$_SESSION['agentid']`（代理 ID）
4. 未登录 → 显示登录表单
5. 已登录 → 加载代理后台界面

---

## 对外接口

### 核心功能页面

| 功能模块 | 文件路径 | 描述 |
|---------|---------|------|
| 代理登录 | `login.php` | 代理登录表单 |
| 用户管理 | `suser.php` | 下级用户列表 |
| 用户详情 | `userinfo.php` | 查看下级用户详细信息 |
| 投注记录 | `slib.php` | 下级投注记录查询 |
| 盈亏报表 | `report.php` | 代理盈亏统计 |
| 账户管理 | `account.php` | 代理账户信息 |
| 修改密码 | `changepass2.php` | 修改登录密码 |
| 在线用户 | `online.php` | 下级在线用户列表 |
| 玩法规则 | `rule.php` | 玩法说明（供下级查看） |

### API 接口

- **POST `/agent/check.php`** - AJAX 处理下级用户编辑
- **POST `/agent/baox.php`** - 代理下单（如果代理可以代下注）

---

## 关键依赖与配置

### 依赖文件

```php
include('../data/comm.inc.php');      // 公共配置（数据库、Session）
include('../data/agentvar.php');      // 代理变量定义
include('../func/func.php');          // 通用函数库
include('../func/agentfunc.php');     // 代理专用函数
include('../func/csfunc.php');        // 彩票业务函数
include('../include.php');            // Smarty 模板引擎初始化
include('./checklogin.php');          // 登录鉴权
```

### 配置文件

- **`/data/agentvar.php`** - 代理变量（如 `$skin`、`$smartytpl` 等）
- **Smarty 模板目录**：`/templates/default/agent/`

### 权限控制

- Session 变量：`$_SESSION['agentid']` - 代理 ID（8 位数字）
- Session 变量：`$_SESSION['level']` - 代理级别（1=一级代理，2=二级代理，等）

---

## 数据模型

### 核心数据表

| 表名 | 用途 | 关键字段 |
|------|------|---------|
| `x_admins` | 代理账号（复用管理员表） | `adminid`, `username`, `password`, `level` |
| `x_user` | 下级用户 | `userid`, `agentid`, `points` |
| `x_lib` | 下级投注记录 | `tid`, `userid`, `points`, `prize` |
| `x_points` | 下级积分调整 | `userid`, `points`, `remark` |
| `x_money_log` | 下级资金流水 | `userid`, `amount`, `mtype` |

### 代理层级关系

```
超级管理员（hide）
 └── 一级代理（agent，level=1）
      └── 二级代理（agent，level=2）
           └── 用户（uxj/mxj）
```

**代理佣金计算逻辑**（推测）：
1. 计算下级用户总输赢
2. 按照设定的佣金比例计算代理收入
3. 写入代理账户余额

---

## 测试与质量

### 当前测试覆盖

❌ 无自动化测试

### 手动测试检查清单

- [ ] 代理登录（正确密码、错误密码）
- [ ] 查看下级用户列表
- [ ] 编辑下级用户信息
- [ ] 查看下级投注记录
- [ ] 查看盈亏报表（计算准确性）
- [ ] 修改密码
- [ ] 权限隔离（不能查看其他代理的用户）

### 已知问题

1. **安全隐患**：
   - 密码使用 MD5 存储（建议升级到 bcrypt）
   - 部分页面缺少 CSRF Token

2. **业务逻辑**：
   - 佣金计算逻辑不清晰（需要详细文档）
   - 代理层级深度未限制

3. **代码质量**：
   - 部分功能与 hide 模块重复（应该共用代码）

---

## 常见问题 (FAQ)

### Q1：如何新增代理？
A：超级管理员在 `hide/admins.php` 中添加，设置 `level` 字段区分代理级别

### Q2：代理佣金如何结算？
A：（根据代码推测）需要手动在 `hide` 后台执行佣金结算脚本，或定时任务自动结算

### Q3：代理可以修改赔率吗？
A：不能，赔率由超级管理员统一设置

---

## 相关文件清单

### PHP 核心文件（按功能分类）

**登录与鉴权**：
- `login.php` - 代理登录
- `checklogin.php` - 登录验证中间件
- `check.php` - AJAX 处理

**用户管理**：
- `suser.php` - 下级用户列表
- `userinfo.php` - 用户详情

**投注管理**：
- `slib.php` - 下级投注记录
- `baox.php` - 代理代下注（如果开放）

**报表统计**：
- `report.php` - 盈亏报表
- `account.php` - 账户管理

**其他**：
- `top.php` - 顶部导航
- `changepass2.php` - 修改密码
- `online.php` - 在线用户
- `rule.php` - 玩法规则

### Smarty 模板文件

位于 `/templates/default/agent/`：
- `login.html` - 代理登录页
- `suser.html` - 用户列表
- `report.html` - 盈亏报表
- `rule_*.html` - 各游戏规则

---

## 下一步优化建议

### 高优先级
1. 🔒 密码加密升级（MD5 → bcrypt）
2. 📝 明确佣金计算逻辑并文档化
3. 🛡️ 添加 CSRF Token 防护

### 中优先级
1. 🧪 为佣金计算编写单元测试
2. 🔧 重构与 hide 模块的重复代码
3. 📊 增加代理数据可视化（图表）

### 低优先级
1. 🎨 UI 现代化（Bootstrap 5）
2. 📱 移动端适配（响应式布局）

---

**模块版本**：v1.0
**文档生成**：Claude AI
**最后更新**：2025-11-09 14:50:39
