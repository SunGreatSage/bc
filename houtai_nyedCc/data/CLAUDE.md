# data 模块 - 配置与数据层

[根目录](../CLAUDE.md) > **data**

> 最后更新：2025-11-09 14:50:39

---

## 变更记录 (Changelog)

### 2025-11-09 14:50:39
- 初始化 data 模块文档
- 记录数据库配置与表定义

---

## 模块职责

**data** 模块是系统的配置中心和数据定义层，负责：
1. 数据库连接配置（主机、端口、用户名、密码）
2. 数据库表名定义（全局变量）
3. SQL 注入防护函数
4. 公共变量定义（用户、管理员、代理）
5. Session 配置
6. 盘口配置（开奖间隔、封盘时间等）

---

## 入口与启动

**本模块为配置文件**，通过 `include` 引入：

```php
include('../data/config.inc.php');  // 数据库配置
include('../data/db.php');          // 表定义与公共函数
include('../data/comm.inc.php');    // 公共配置（整合 config + db）
include('../data/uservar.php');     // 用户变量
include('../data/agentvar.php');    // 代理变量
include('../data/myadminvar.php');  // 管理员变量
include('../data/pan.inc.php');     // 盘口配置
```

---

## 对外接口

### 核心配置文件

#### 1. config.inc.php - 数据库配置

**主要变量**：
```php
$dbHost = "127.0.0.1";           // 数据库主机
$dbPort = '55667';               // 数据库端口
$dbName = "lhc_oa";              // 数据库名
$dbUser = "lhc_oa";              // 数据库用户
$dbPass = "JH4ctk4mJBNxmhw5";   // 数据库密码
$globalpath = "/";               // 全局路径
$SESS_LIFE = 14400;              // Session 有效期（4 小时）
```

**特殊配置**：
```php
$jkarr = array(22116244, 22116241, ...);  // 开奖控制数组
$poarr = array(0);                         // 端口数组
$ipa = [                                   // IP 白名单（动态生成）
    'i22116244' => '117.24.125.1' . (date("d")+1),
    ...
];
$changeorder = 1;  // 订单修改开关（1=开启，0=关闭）
```

#### 2. db.php - 表定义与公共函数

**表名定义（全局变量）**：
```php
$tb_admins       = "x_admins";        // 管理员表
$tb_user         = "x_user";          // 用户表
$tb_order        = "x_order";         // 订单表
$tb_kj           = "x_kj";            // 开奖记录表
$tb_lib          = "x_lib";           // 投注记录表
$tb_points       = "x_points";        // 积分表
$tb_money_log    = "x_money_log";     // 资金流水表
$tb_config       = "x_config";        // 系统配置表
$tb_session      = "x_session";       // Session 表
// ... 共 40+ 张表
```

**SQL 注入防护函数**：
```php
function addslashes_array($a);         // 递归转义数组
function acreplace($a);                 // 过滤危险关键字
function daddslashes($string);          // Discuz 风格转义
function do_query_safe($sql, $diyunsafe); // SQL 安全检查
```

**触发器定义**：
```php
$tupdatestr = "DROP TRIGGER if exists `tupdatelib`";
$tupdatecc  = "CREATE TRIGGER `tupdatelib` BEFORE UPDATE ON `x_lib_total` ...";
$tdeletestr = "DROP TRIGGER if exists `tdeletelib`";
$tdeletecc  = "CREATE TRIGGER `tdeletelib` AFTER DELETE ON `x_lib_total` ...";
```

#### 3. comm.inc.php - 公共配置（整合）

**引入依赖**：
```php
include("../data/config.inc.php");
include("../data/db.php");
include("../global/db.inc.php");
include("../global/session.class.php");
include("../data/pan.inc.php");
```

#### 4. uservar.php / agentvar.php / myadminvar.php - 角色变量

**用户变量（uservar.php）**：
```php
$skin = 'default';         // 皮肤名称
$smartytpl = 'uxj';        // Smarty 模板目录
```

**代理变量（agentvar.php）**：
```php
$skin = 'default';
$smartytpl = 'agent';
```

**管理员变量（myadminvar.php）**：
```php
$skin = 'default';
$smartytpl = 'hide';
```

#### 5. pan.inc.php - 盘口配置

（根据代码推测）
- 开奖间隔时间
- 封盘时间
- 停售时间

---

## 关键依赖与配置

### 依赖关系

- **config.inc.php** 是基础配置，被所有模块依赖
- **db.php** 依赖 `config.inc.php`
- **comm.inc.php** 整合了 `config.inc.php` + `db.php` + `global/`

### 时区配置

```php
date_default_timezone_set("Asia/Shanghai");  // 定义在 config.inc.php
```

### 错误报告配置

```php
error_reporting(0);  // 关闭错误显示（生产环境）
```

---

## 数据模型

### 核心数据表清单（40+ 张表）

| 表名 | 用途 | 关键字段 |
|------|------|---------|
| `x_admins` | 管理员账号 | `adminid`, `username`, `password`, `level` |
| `x_user` | 用户账号 | `userid`, `username`, `password`, `points`, `agentid` |
| `x_order` | 订单（旧） | `orderid`, `userid`, `amount`, `status` |
| `x_lib` | 投注记录 | `tid`, `userid`, `qishu`, `points`, `prize`, `z` |
| `x_kj` | 开奖记录 | `qishu`, `kj1-kj8`, `dates`, `gid` |
| `x_points` | 积分调整记录 | `userid`, `points`, `time`, `remark` |
| `x_money_log` | 资金流水 | `userid`, `amount`, `mtype`, `status`, `time` |
| `x_config` | 系统配置 | `logincode`, `loginfs`, `smtp_host`, ... |
| `x_session` | Session 存储 | `session_id`, `session_data`, `session_time` |
| `x_game` | 游戏配置 | `gid`, `gname`, `status`, `sort` |
| `x_bclass` | 大盘分类 | `bid`, `bname`, `gid` |
| `x_class` | 子盘分类 | `sid`, `sname`, `bid` |
| `x_play` | 玩法赔率 | `pid`, `pname`, `peilv1`, `peilv2` |
| `x_fly` | 飞单配置 | `flyid`, `userid`, `flytype` |
| `x_warn` | 预警配置 | `warnid`, `class`, `warnpoint` |
| `x_bank` | 银行卡配置 | `bankid`, `bankname`, `banknumber` |
| `x_notices` | 公告 | `noticeid`, `title`, `content`, `time` |
| `x_mailbox` | 站内信 | `mailid`, `userid`, `title`, `content` |

### 触发器机制

**UPDATE 触发器（tupdatelib）**：
- 当 `x_lib_total` 表的订单被更新时，如果 `kk != 1`，则将旧数据备份到 `x_lib_err` 表
- 防止非法修改订单（篡改投注金额、赔率等）

**DELETE 触发器（tdeletelib）**：
- 当 `x_lib_total` 表的订单被删除时，如果 `kk != 2`，则将删除记录备份到 `x_lib_err` 表
- 防止非法删除订单

---

## 测试与质量

### 当前测试覆盖

❌ 无自动化测试

### 推荐测试用例

#### 测试 SQL 注入防护

```php
// 测试用例 1：过滤 SELECT 关键字
$input = "1' OR '1'='1";
$result = acreplace($input);
assert(strpos($result, 'select') === false);

// 测试用例 2：过滤 UNION 关键字
$input = "1 UNION SELECT * FROM x_admins";
$result = acreplace($input);
assert(strpos($result, 'union') === false);
```

#### 测试触发器

```php
// 测试用例 1：尝试修改订单（应被拦截）
$msql->query("UPDATE x_lib_total SET points=9999 WHERE tid='12345678'");
// 检查 x_lib_err 表是否有备份记录
```

### 已知问题

1. **SQL 注入防护不完善**：
   - `acreplace()` 仅过滤部分关键字，可能被绕过

2. **配置硬编码**：
   - 数据库密码明文存储（应使用环境变量）

3. **表名定义混乱**：
   - 部分表重复定义（如 `$tb_user` 出现两次）

---

## 常见问题 (FAQ)

### Q1：如何修改数据库密码？
A：编辑 `data/config.inc.php`，修改 `$dbPass` 变量

### Q2：如何新增数据表？
A：
1. 在数据库中执行 CREATE TABLE 语句
2. 在 `data/db.php` 中添加表名定义（如 `$tb_newtable = "x_newtable";`）

### Q3：Session 有效期太短怎么办？
A：修改 `data/config.inc.php` 中的 `$SESS_LIFE` 变量（单位：秒）

---

## 相关文件清单

### 配置文件

- `config.inc.php` - 数据库配置（约 34 行）
- `db.php` - 表定义与公共函数（约 214 行）
- `comm.inc.php` - 公共配置整合（7 行）
- `uservar.php` - 用户变量
- `agentvar.php` - 代理变量
- `myadminvar.php` - 管理员变量
- `mobivar.php` - 移动端变量
- `pan.inc.php` - 盘口配置
- `var.php` - 其他变量

### 辅助文件

- `session.php` - Session 初始化脚本
- `cuncu.php` - 临时存储（用途不明）
- `FileCache.php` - 文件缓存类

---

## 下一步优化建议

### 高优先级
1. 🔒 数据库密码改用环境变量（.env 文件）
2. 🛡️ 完善 SQL 注入防护（使用 PDO 预处理）
3. 📝 清理重复的表名定义

### 中优先级
1. 📊 将盘口配置移到数据库（`x_config` 表）
2. 🧪 为防护函数编写单元测试
3. 🔧 统一配置文件格式（使用 JSON 或 YAML）

### 低优先级
1. 🗑️ 删除未使用的变量（如 `$jkarr`、`$ipa` 等）
2. 📦 封装为配置类（面向对象）

---

**模块版本**：v1.0
**文档生成**：Claude AI
**最后更新**：2025-11-09 14:50:39
