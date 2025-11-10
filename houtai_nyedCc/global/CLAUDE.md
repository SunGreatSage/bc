# global 模块 - 全局工具库

[根目录](../CLAUDE.md) > **global**

> 最后更新：2025-11-09 14:50:39

---

## 变更记录 (Changelog)

### 2025-11-09 14:50:39
- 初始化 global 模块文档
- 记录全局工具类与第三方库集成

---

## 模块职责

**global** 模块提供系统级的基础服务，包括：
1. 数据库连接与操作（自定义 DB 类）
2. Session 管理（支持数据库存储）
3. 图片处理（验证码、缩略图、水印）
4. Excel 导入导出（PHPExcel）
5. 邮件发送（集成 SMTP）
6. 文件压缩与解压（ZIP）
7. 表单构建器
8. IP 地址定位
9. 客户端检测（浏览器、操作系统）

---

## 入口与启动

**本模块为工具库**，通过 `include` 引入：

```php
include('../global/db.inc.php');         // 数据库连接类
include('../global/session.class.php');  // Session 管理类
include('../global/PHPExcel.php');       // Excel 处理
include('../global/img.class.php');      // 图片处理
include('../global/forms.php');          // 表单构建器
include('../global/client.php');         // 客户端检测
include('../global/Iplocation_Class.php'); // IP 定位
```

---

## 对外接口

### 核心类与函数

#### 1. 数据库类（db.inc.php / db_mysql.class.php）

**类名**：`DB_Sql`（推测，未完全扫描到）

**主要方法**：
- `query($sql)` - 执行 SQL 查询
- `next_record()` - 移动到下一条记录
- `f($field)` - 获取字段值
- `num_rows()` - 获取结果集行数
- `affected_rows()` - 获取影响行数
- `free()` - 释放结果集

**使用示例**：
```php
global $msql;
$msql->query("SELECT * FROM x_user WHERE userid='$userid'");
$msql->next_record();
$username = $msql->f('username');
```

#### 2. Session 类（session.class.php）

**类名**：`SessionHandler`（推测）

**功能**：
- 支持将 Session 存储到数据库（`x_session` 表）
- Session 有效期：14400 秒（4 小时）
- 自动清理过期 Session

**配置**：
```php
$SESS_LIFE = 14400;  // 定义在 data/config.inc.php
```

#### 3. 图片处理类（img.class.php / img2.class.php / img3.class.php）

**主要功能**：
- 验证码生成
- 图片缩略图
- 图片水印
- 图片裁剪

**使用示例**（推测）：
```php
$img = new ImageClass();
$img->createCaptcha();  // 生成验证码
$img->resize($width, $height);  // 缩略图
```

#### 4. Excel 类（PHPExcel.php）

**第三方库**：PHPExcel（已弃用，建议升级到 PhpSpreadsheet）

**主要功能**：
- 导出 Excel（.xls / .xlsx）
- 导入 Excel
- 单元格样式设置

**使用示例**：
```php
include('../global/PHPExcel.php');
$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', '用户名')
    ->setCellValue('B1', '余额');
// ... 导出逻辑
```

#### 5. 邮件类（ebmail.inc.php）

**功能**：
- SMTP 邮件发送
- 支持附件
- HTML 邮件

**配置**（推测在 `x_config` 表）：
- SMTP 服务器
- 发件人邮箱
- SMTP 密码

#### 6. ZIP 压缩类（pclzip.lib.php / zip.lib.php）

**功能**：
- 文件压缩
- 文件解压
- 数据库备份压缩

#### 7. IP 定位类（Iplocation_Class.php）

**功能**：
- 根据 IP 地址查询地理位置（省份、城市）
- 使用 QQWry.dat IP 数据库

**使用示例**：
```php
include('../global/Iplocation_Class.php');
$ip = new IpLocation();
$addr = $ip->getlocation($_SERVER['REMOTE_ADDR']);
echo $addr['country'];  // 输出：中国
echo $addr['area'];     // 输出：广东省深圳市
```

---

## 关键依赖与配置

### 依赖关系

- **db.inc.php** 依赖 `data/config.inc.php`（数据库配置）
- **session.class.php** 依赖 `data/db.php`（表定义）
- **PHPExcel.php** 依赖 `/global/lib/` 下的子库

### 配置文件

- **数据库配置**：`/data/config.inc.php`
- **Session 表**：`x_session`（需在数据库中创建）

---

## 数据模型

### 关联数据表

| 表名 | 用途 | 关键字段 |
|------|------|---------|
| `x_session` | Session 存储 | `session_id`, `session_data`, `session_time` |
| `x_config` | 系统配置 | `smtp_host`, `smtp_user`, `smtp_pass` |

---

## 测试与质量

### 当前测试覆盖

❌ 无自动化测试

### 推荐测试用例

#### 测试数据库连接

```php
// 测试用例 1：连接成功
$msql->query("SELECT 1");
assert($msql->num_rows() == 1);

// 测试用例 2：查询不存在的表（应抛出错误）
try {
    $msql->query("SELECT * FROM nonexistent_table");
    assert(false);  // 不应该执行到这里
} catch (Exception $e) {
    assert(true);
}
```

#### 测试 Session

```php
// 测试用例 1：设置 Session
$_SESSION['test'] = '123';
assert($_SESSION['test'] == '123');

// 测试用例 2：Session 过期（需等待 4 小时）
// ...
```

### 已知问题

1. **PHPExcel 已弃用**：
   - 官方建议升级到 PhpSpreadsheet

2. **安全隐患**：
   - 图片上传未验证文件类型（可能被上传恶意脚本）

3. **性能问题**：
   - 数据库 Session 存储效率低（建议改用 Redis）

---

## 常见问题 (FAQ)

### Q1：如何切换到 Redis 存储 Session？
A：修改 `session.class.php`，使用 `session_set_save_handler()` 切换到 Redis

### Q2：PHPExcel 导出中文乱码？
A：确保数据库查询结果编码为 UTF-8，并在导出时设置字符集

### Q3：验证码不显示？
A：检查 PHP GD 库是否安装（`php -m | grep gd`）

---

## 相关文件清单

### 核心工具类

- `db.inc.php` - 数据库连接主类
- `db_mysql.class.php` - MySQL 数据库类
- `db1.inc.php` - 备用数据库连接（用于高并发？）
- `session.class.php` - Session 管理类
- `session.class.old.php` - 旧版 Session 类
- `session.class.new.php` - 新版 Session 类

### 图片处理

- `img.class.php` - 图片处理主类
- `img2.class.php` - 图片处理扩展类
- `img3.class.php` - 图片处理扩展类 2
- `imgn.class.php` - 图片处理（新版）
- `imgnf.class.php` - 图片处理（新版 + 滤镜）

### 第三方库

- `PHPExcel.php` - Excel 处理主类
- `php-excel.class.php` - 简易 Excel 类
- `ebmail.inc.php` - SMTP 邮件类
- `pclzip.lib.php` - ZIP 压缩类
- `zip.lib.php` - ZIP 压缩类 2
- `Iplocation_Class.php` - IP 定位类
- `backdata.class.php` - 数据库备份类
- `json.php` - JSON 处理（PHP 5.2 兼容）

### SOAP 服务

- `lib/class.nusoap_base.php` - NuSOAP 基类
- `lib/class.soapclient.php` - SOAP 客户端
- `lib/class.wsdl.php` - WSDL 解析
- `lib/nusoapmime.php` - SOAP MIME 处理

### 辅助工具

- `forms.php` - 表单构建器
- `client.php` - 客户端检测
- `page.class.php` - 分页类

---

## 下一步优化建议

### 高优先级
1. 🔄 PHPExcel 迁移到 PhpSpreadsheet
2. 🚀 Session 改用 Redis 存储
3. 🛡️ 图片上传增加类型验证

### 中优先级
1. 📝 为核心类添加 PHPDoc 注释
2. 🧪 编写单元测试（数据库类、Session 类）
3. 🔧 统一数据库操作类（去除冗余代码）

### 低优先级
1. 🗑️ 删除旧版文件（session.class.old.php）
2. 📦 封装为 Composer 包

---

**模块版本**：v1.0
**文档生成**：Claude AI
**最后更新**：2025-11-09 14:50:39
