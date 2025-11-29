# 彩票投注系统后端服务

> 基于 ThinkPHP 6 框架的六合彩投注系统 API 服务
>
> 完整保留老系统数据库结构,通过服务层封装实现现代化接口

---

## 📋 项目概述

本项目为新澳门六合彩投注系统的后端服务,主要特点:

- ✅ **无侵入式设计**: 完整保留老系统数据库结构,不修改表结构
- ✅ **新老系统融合**: 通过用户ID映射实现新老系统无缝对接
- ✅ **生肖年度自动轮转**: 智能处理每年生肖与号码的对应关系
- ✅ **完整验证流程**: 8段式投注验证,确保数据安全
- ✅ **风控预警机制**: 大额投注自动预警,防范风险
- ✅ **前端友好接口**: 一次请求获取所有投注所需数据

---

## 🚀 快速开始

### 环境要求

- PHP >= 8.0
- MySQL >= 5.7
- Composer
- Redis(可选)

### 安装步骤

```bash
# 1. 安装依赖
composer install

# 2. 配置环境
cp .env.example .env
vi .env  # 修改数据库配置

# 3. 启动服务
php think run -p 8000

# 4. 访问
http://localhost:8000
```

### 快速测试

```bash
# 测试登录
curl -X POST http://localhost:8000/api/lottery_login/login \
  -d "username=test001&password=123456"

# 获取投注数据
curl http://localhost:8000/api/lottery_bet/getBetNumbers?year=2025

# 投注下单(需要token)
curl -X POST http://localhost:8000/api/lottery_bet/placeBet \
  -H "token: YOUR_TOKEN" \
  -d "gid=200&qishu=2025112&pid=21401&bet_content=08&bet_amount=100"
```

---

## 📚 核心功能

### 1. 用户系统

- **彩票登录**: 基于老系统用户表,生成JWT token
- **用户映射**: 新老系统用户ID自动关联
- **余额管理**: 实时查询老系统用户余额

### 2. 投注系统

- **投注下单**: 8段式验证流程,确保投注安全
- **投注记录**: 查询历史投注,支持分页和筛选
- **时间控制**: 开盘/封盘/开奖时间自动控制
- **风控预警**: 大额投注自动记录预警

### 3. 数据查询

- **开奖结果**: 查询历史开奖号码
- **当前期号**: 获取可投注的期号
- **玩法列表**: 查询所有可用玩法及赔率
- **投注数据**: 获取49个号码、12个生肖、完整赔率信息

### 4. 生肖年度轮转

**核心特性**: 自动处理每年生肖与号码的对应关系

```
2025年(蛇年):
  鼠: 6, 18, 30, 42
  蛇: 1, 13, 25, 37, 49  (当年生肖,5个号码)

2026年(马年):
  鼠: 7, 19, 31, 43      (向后偏移1)
  马: 1, 13, 25, 37, 49  (当年生肖,5个号码)
```

**技术实现**: `ZodiacYearService` 服务类

---

## 🏗️ 架构设计

### 目录结构

```
server/
├── app/
│   ├── api/                        # 用户端API
│   │   ├── controller/
│   │   │   ├── LotteryBetController.php      # 投注控制器 ⭐
│   │   │   └── LotteryLoginController.php    # 登录控制器 ⭐
│   │   └── logic/
│   │       ├── LotteryBetLogic.php           # 投注业务逻辑 ⭐
│   │       └── LotteryLoginLogic.php         # 登录业务逻辑 ⭐
│   ├── common/
│   │   └── service/
│   │       ├── ZodiacYearService.php         # 生肖年度服务 ⭐
│   │       ├── ZodiacService.php             # 生肖判奖服务 ⭐
│   │       └── BetLimitService.php           # 投注限额服务 ⭐
│   └── adminapi/                   # 管理端API
├── config/
│   ├── zodiac_base_year.php        # 生肖基准年配置 ⭐
│   └── lottery_bet_limits.php      # 投注限额配置 ⭐
└── docs/                           # 完整文档 ⭐
    ├── 彩票投注系统-完整实现总结.md
    ├── API测试用例集.md
    ├── 获取投注号码序列API文档.md
    ├── 生肖判奖年份获取说明.md
    └── 开奖时间和封盘规则分析.md
```

### 技术栈

| 技术 | 版本 | 用途 |
|------|------|------|
| ThinkPHP | 6.0.2 | PHP框架 |
| PHP | >= 8.0 | 编程语言 |
| MySQL | 5.7+ | 数据库 |
| ThinkORM | 3.0 | ORM |
| JWT | - | Token认证 |
| Redis | (可选) | 缓存 |

---

## 📡 API 接口

### 接口清单

| 接口名 | 路由 | 方法 | 登录 | 说明 |
|--------|------|------|------|------|
| 彩票登录 | `/api/lottery_login/login` | POST | ❌ | 用户登录获取token |
| 投注下单 | `/api/lottery_bet/placeBet` | POST | ✅ | 投注下单 |
| 投注记录 | `/api/lottery_bet/getBetList` | GET | ✅ | 查询投注历史 |
| 开奖结果 | `/api/lottery_bet/getKjResult` | GET | ❌ | 查询开奖号码 |
| 当前期号 | `/api/lottery_bet/getCurrentQishu` | GET | ❌ | 查询当前期号 |
| 玩法列表 | `/api/lottery_bet/getPlayList` | GET | ❌ | 查询玩法列表 |
| 投注数据 | `/api/lottery_bet/getBetNumbers` | GET | ❌ | 获取号码/生肖/赔率 |

### 投注下单示例

**请求**:
```bash
POST /api/lottery_bet/placeBet
Content-Type: application/x-www-form-urlencoded
token: YOUR_JWT_TOKEN

gid=200&qishu=2025112&pid=21401&bet_content=08&bet_amount=100
```

**响应**:
```json
{
  "code": 1,
  "msg": "投注成功",
  "data": {
    "tid": "20251127141530000001",
    "balance": "9900.00",
    "qishu": "2025112",
    "expected_prize": "4200.00"
  }
}
```

### 获取投注数据示例

**请求**:
```bash
GET /api/lottery_bet/getBetNumbers?year=2025
```

**响应**(部分):
```json
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "year": 2025,
    "year_zodiac": "蛇",
    "year_zodiac_numbers": ["01", "13", "25", "37", "49"],
    "numbers": [
      {
        "number": "01",
        "zodiac": "蛇",
        "is_current_year_zodiac": true,
        "odds": {
          "special_number": "47",
          "normal_number": "8"
        }
      }
      // ... 共49个号码
    ],
    "zodiacs": {
      "鼠": {
        "name": "鼠",
        "numbers": ["06", "18", "30", "42"],
        "count": 4,
        "is_current_year": false,
        "odds": {
          "special_zodiac": "12",
          "three_zodiac": "7",
          "four_zodiac": "5",
          "five_zodiac": "4",
          "six_zodiac": "3"
        }
      }
      // ... 共12个生肖
    }
  }
}
```

---

## 🔒 投注验证流程

### 8段式验证

```
┌─────────────┐
│ 1. 参数验证 │ → gid, qishu, pid, bet_content, bet_amount
└─────────────┘
       ↓
┌─────────────┐
│ 2. 金额验证 │ → 最小1元, 必须为正整数
└─────────────┘
       ↓
┌─────────────┐
│ 3. 用户验证 │ → 用户状态, 余额充足
└─────────────┘
       ↓
┌─────────────┐
│ 4. 玩法验证 │ → 玩法存在且开放
└─────────────┘
       ↓
┌─────────────┐
│ 5. 期号验证 │ → 期号存在
└─────────────┘
       ↓
┌─────────────┐
│ 6. 时间验证 │ → 未开奖, 已开盘, 未封盘, 未过开奖时间
└─────────────┘
       ↓
┌─────────────┐
│ 7. 风控验证 │ → 大额投注预警记录
└─────────────┘
       ↓
┌─────────────┐
│ 8. 事务处理 │ → 扣款, 写入投注, 添加流水
└─────────────┘
```

### 时间控制

```
06:00          09:25         09:30              09:50
  |--------------|-------------|------------------|
开盘时间      实际封盘    名义封盘          开奖时间
(opentime)   (提前5分钟) (closetime)      (kjtime)
  |              |             |                  |
  └─ 可投注 ─→   └─停止投注─→  └─等待开奖─→      └─公布结果
```

---

## 🎮 玩法配置

### 赔率表

| 玩法 | 赔率 | 说明 |
|------|------|------|
| 特码(01-48) | 42倍 | 普通号码特码 |
| 特码(49) | 47倍 | 49号特码(特殊) |
| 平码 | 8倍 | 正码1-6 |
| 特肖 | 12倍 | 特码生肖 |
| 三肖 | 7倍 | 选3个生肖 |
| 四肖 | 5倍 | 选4个生肖 |
| 五肖 | 4倍 | 选5个生肖 |
| 六肖 | 3倍 | 选6个生肖 |

### 投注限制

| 配置项 | 值 | 说明 |
|--------|---|------|
| 最小投注 | 1元 | 每笔投注最小金额 |
| 最大投注 | 不限制 | 无最大金额限制 |
| 单笔预警 | 10000元 | 触发风控预警 |
| 单期预警 | 50000元 | 单期总额预警 |

---

## 🗄️ 数据库设计

### 核心表

**使用老系统表,不修改结构**:

| 表名 | 用途 | 关键字段 |
|------|------|---------|
| `x_user` | 老系统用户 | userid, username, money |
| `x_lib` | 投注记录 | tid, userid, qishu, pid, content, je, peilv1, z, dates |
| `x_kj` | 开奖表 | qishu, gid, m1-m8, opentime, closetime, kjtime |
| `x_play` | 玩法表 | pid, name, peilv1, ifok |
| `x_game` | 游戏表 | gid, name, thisqishu, ifok |

**新增表**:

| 表名 | 用途 | 关键字段 |
|------|------|---------|
| `la_user` | 新系统用户 | id, username, mobile, token |
| `user_id_mapping` | 用户映射 | new_user_id, old_user_id |

**重要设计**: 从`dates`字段提取年份,无需新增`year`字段

---

## 📖 完整文档

| 文档 | 说明 |
|------|------|
| [彩票投注系统-完整实现总结](docs/彩票投注系统-完整实现总结.md) | 系统架构、技术实现、接口清单 |
| [API测试用例集](docs/API测试用例集.md) | 完整的接口测试用例和脚本 |
| [获取投注号码序列API文档](docs/获取投注号码序列API文档.md) | 前端数据接口详细说明 |
| [生肖判奖年份获取说明](docs/生肖判奖年份获取说明.md) | 如何从dates字段提取年份 |
| [开奖时间和封盘规则分析](docs/开奖时间和封盘规则分析.md) | 时间字段与封盘逻辑 |
| [CLAUDE.md](CLAUDE.md) | AI开发上下文文档 |

---

## 🧪 测试

### 单元测试

```bash
# 安装PHPUnit
composer require phpunit/phpunit --dev

# 运行测试
./vendor/bin/phpunit
```

### API测试

```bash
# 使用提供的测试脚本
cd docs
./test_lottery_api.sh
```

### 压力测试

```bash
# 使用Apache Bench
ab -n 1000 -c 50 http://localhost:8000/api/lottery_bet/getBetNumbers?year=2025
```

---

## 🔧 配置说明

### 环境变量(.env)

```ini
# 应用配置
APP_DEBUG = true

# 数据库配置
DATABASE_HOST = 127.0.0.1
DATABASE_PORT = 3306
DATABASE_NAME = lottery
DATABASE_USER = root
DATABASE_PASSWORD = secret

# Redis配置(可选)
REDIS_HOST = 127.0.0.1
REDIS_PORT = 6379
```

### 生肖基准年配置

**文件**: `config/zodiac_base_year.php`

```php
return [
    'base_year' => 2025,           // 基准年份
    'base_zodiac' => '蛇',         // 基准生肖
    'base_table' => [...],         // 2025年生肖对应表
    'zodiac_order' => [...],       // 生肖顺序
    'year_offset' => 4,            // 年份偏移量
];
```

### 投注限额配置

**文件**: `config/lottery_bet_limits.php`

```php
return [
    'global' => [
        'min_bet_amount' => 1,     // 最小投注1元
        'max_bet_amount' => null,  // 不限制最大金额
    ],
    'risk_control' => [
        'alert_amount' => 10000,         // 单笔预警阈值
        'alert_period_amount' => 50000,  // 单期预警阈值
    ],
];
```

---

## 🐛 常见问题

### Q1: Token过期怎么办?

**A**: 重新登录获取新token
```bash
curl -X POST http://localhost:8000/api/lottery_login/login \
  -d "username=test001&password=123456"
```

### Q2: 如何测试不同年份的生肖?

**A**: 直接传入year参数
```bash
# 2025年(蛇年)
curl http://localhost:8000/api/lottery_bet/getBetNumbers?year=2025

# 2026年(马年)
curl http://localhost:8000/api/lottery_bet/getBetNumbers?year=2026
```

### Q3: 如何模拟封盘状态?

**A**: 修改数据库x_kj表的closetime字段
```sql
UPDATE x_kj SET closetime = '2025-11-27 08:00:00' WHERE qishu = '2025112';
```

### Q4: 如何查看风控预警?

**A**: 查询risk_alert_log表(如果实现)
```sql
SELECT * FROM risk_alert_log WHERE user_id = 1 ORDER BY id DESC;
```

---

## 📋 待办事项

### 短期

- [ ] 接口自动化测试
- [ ] 添加详细日志记录
- [ ] 实现判奖功能
- [ ] 确认赔率配置
- [ ] 确认开奖时间

### 中期

- [ ] Redis缓存优化
- [ ] 赔率动态化(从x_play表读取)
- [ ] 异步判奖任务
- [ ] 性能优化

### 长期

- [ ] 监控告警系统
- [ ] 数据统计报表
- [ ] Docker容器化
- [ ] CI/CD集成

---

## 👥 开发团队

**架构设计**: Claude AI
**开发时间**: 2025-11-27
**框架版本**: ThinkPHP 6.0.2
**PHP版本**: >= 8.0

---

## 📄 许可证

本项目为商业项目,版权所有。

---

## 🙏 致谢

感谢 ThinkPHP 框架提供强大的技术支持。

---

**最后更新**: 2025-11-27 17:30:00
**文档版本**: v1.0
**项目状态**: ✅ 开发完成,待测试
