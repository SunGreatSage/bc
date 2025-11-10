# mxj 模块 - 移动用户前台

[根目录](../CLAUDE.md) > **mxj**

> 最后更新：2025-11-09 14:50:39

---

## 变更记录 (Changelog)

### 2025-11-09 14:50:39
- 初始化 mxj 模块文档
- 记录移动端用户前台核心功能

---

## 模块职责

**mxj** 模块是移动端（手机/平板）用户前台界面，负责：
1. 移动端用户注册与登录
2. 响应式投注界面（触摸优化）
3. 移动端投注记录查询
4. 移动端开奖结果查看
5. 移动端账户管理
6. 移动端资金操作
7. 移动端个人中心
8. 移动端玩法规则说明

**与 uxj 模块的区别**：
- UI 适配移动端（Bootstrap Mobile / Ionic）
- 交互优化（触摸、滑动）
- 页面加载速度优化（精简资源）

---

## 入口与启动

### 主要入口文件

- **`index.php`** - 重定向到登录页（带参数 `com`）
- **`login.php`** - 移动端登录
- **`reg.php`** - 移动端注册
- **`checklogin.php`** - 登录鉴权中间件
- **`nav.php`** - 移动端导航菜单
- **`long.php`** - 移动端投注下单
- **`bao.php`** - 移动端包号投注
- **`lib.php`** - 移动端投注记录

### 启动流程

1. 访问 `http://domain/mxj/`
2. `index.php` 重定向到 `login.php?com=随机数`
3. 检测设备类型（通过 User-Agent）
4. 验证 Session 中的 `$_SESSION['uuid']`
5. 未登录 → 显示移动端登录表单
6. 已登录 → 跳转到投注页面

---

## 对外接口

### 核心功能页面

| 功能模块 | 文件路径 | 描述 |
|---------|---------|------|
| 移动登录 | `login.php` | 触摸优化的登录界面 |
| 移动注册 | `reg.php` | 移动端注册表单 |
| 投注下单 | `long.php` | 龙虎斗等玩法（触摸版） |
| 包号投注 | `bao.php` | 快速包号（触摸版） |
| 投注记录 | `lib.php` | 移动端记录查询 |
| 开奖结果 | `xy.php` | 移动端开奖查看 |
| 个人中心 | `member.php` | 移动端用户中心 |
| 修改密码 | `changepass2.php` | 移动端密码修改 |
| 账户报表 | `report.php` | 移动端盈亏统计 |
| 玩法规则 | `rule.php` | 移动端规则说明 |
| 导航菜单 | `nav.php` | 底部导航栏 |
| 公告通知 | `notices.php` | 移动端公告 |

### API 接口

- **POST `/mxj/check.php`** - AJAX 下单处理（移动端）
- **POST `/mxj/getlogin.php`** - 获取登录状态
- **GET `/mxj/time.php`** - 获取服务器时间

---

## 关键依赖与配置

### 依赖文件

```php
include('../data/comm.inc.php');      // 公共配置（数据库、Session）
include('../data/mobivar.php');       // 移动端变量定义
include('../func/func.php');          // 通用函数库
include('../func/userfunc.php');      // 用户专用函数
include('../func/csfunc.php');        // 彩票业务函数
include('../include.php');            // Smarty 模板引擎初始化
include('./checklogin.php');          // 登录鉴权
```

### 配置文件

- **`/data/mobivar.php`** - 移动端变量（如 `$skin`、`$smartytpl` 等）
- **Smarty 模板目录**：`/templates/default/mxj/`

### 权限控制

- Session 变量：`$_SESSION['uuid']` - 用户 ID（8 位数字）
- Session 变量：`$_SESSION['mobi']` - 移动端标识（值为 1）

---

## 数据模型

### 核心数据表

**与 uxj 模块共用相同的数据表**：
- `x_user` - 用户账号
- `x_lib` - 投注记录
- `x_kj` - 开奖记录
- `x_points` - 积分调整
- `x_money_log` - 资金流水

### 移动端特殊处理

1. **登录检测**：
   - 根据 `$_SESSION['mobi']` 判断是否为移动端
   - 如果 PC 端用户访问 mxj，自动跳转到 uxj

2. **投注界面**：
   - 按钮更大（触摸友好）
   - 减少页面元素（加载速度优化）

---

## 测试与质量

### 当前测试覆盖

❌ 无自动化测试

### 手动测试检查清单

- [ ] 移动端登录（各种浏览器：Safari、Chrome Mobile、微信内置浏览器）
- [ ] 触摸投注（滑动选号、点击下单）
- [ ] 横屏/竖屏适配
- [ ] 网络慢速模拟（3G/4G）
- [ ] 移动端支付（如果集成支付宝/微信）

### 已知问题

1. **兼容性问题**：
   - 部分旧版 Android 浏览器不兼容
   - iOS Safari 可能存在 Session 问题

2. **性能问题**：
   - 图片未压缩（加载慢）
   - JavaScript 文件未合并压缩

3. **用户体验**：
   - 部分按钮太小（难以点击）
   - 缺少下拉刷新功能

---

## 常见问题 (FAQ)

### Q1：移动端登录后自动退出？
A：可能是 Safari 的隐私设置问题，建议引导用户使用 App 或微信内置浏览器

### Q2：移动端投注失败？
A：检查网络状态，建议添加网络状态检测提示

### Q3：如何区分移动端和 PC 端？
A：通过 `$_SESSION['mobi']` 判断，或使用 `ismobi()` 函数（定义在 `index.php`）

---

## 相关文件清单

### PHP 核心文件（按功能分类）

**登录与注册**：
- `login.php` - 移动端登录
- `reg.php` - 移动端注册
- `checklogin.php` - 登录验证
- `getlogin.php` - 获取登录状态（AJAX）

**投注下单**：
- `long.php` - 龙虎斗等玩法（移动版）
- `bao.php` - 包号投注（移动版）
- `makeloto.php` - 生成投注单
- `makelib.php` - 写入投注记录

**查询与统计**：
- `lib.php` - 投注记录查询（移动版）
- `xy.php` - 开奖结果查看（移动版）
- `report.php` - 盈亏报表（移动版）

**账户管理**：
- `member.php` - 个人中心（移动版）
- `changepass2.php` - 修改密码（移动版）
- `uinfo.php` - 用户信息（移动版）

**其他**：
- `nav.php` - 底部导航栏
- `notices.php` - 公告通知
- `time.php` - 服务器时间
- `rule.php` - 玩法规则（移动版）
- `home.php` - 移动端首页

### Smarty 模板文件

位于 `/templates/default/mxj/`：
- `login.html` - 移动端登录页
- `longloto.html` - 移动端投注界面
- `libloto.html` - 移动端投注记录
- `mem_ucenter.html` - 移动端个人中心
- `changepass2.html` - 移动端修改密码
- `sidebar.html` - 移动端侧边栏
- `nav.html` - 移动端导航

### CSS 文件

- `/css/mobile/` - 移动端样式（Bootstrap Mobile、Ionic 等）
- `/css/mo/` - 移动端主题

### JavaScript 文件

- `/js/vue/` - Vue.js（移动端可能使用）
- `/js/node_modules/angular/` - AngularJS（移动端可能使用）

---

## 下一步优化建议

### 高优先级
1. 📱 PWA 改造（支持离线访问、添加到主屏幕）
2. 🚀 性能优化（图片压缩、懒加载、代码压缩）
3. 🔒 Session 稳定性优化（解决 iOS Safari 问题）

### 中优先级
1. 🎨 UI 现代化（Material Design 或 Ant Design Mobile）
2. 🧪 移动端自动化测试（Appium 或 Selenium）
3. 🔔 添加推送通知（Service Worker）

### 低优先级
1. 📦 封装为混合 App（Cordova / React Native）
2. 🌐 多语言支持（国际化）

---

**模块版本**：v1.0
**文档生成**：Claude AI
**最后更新**：2025-11-09 14:50:39
