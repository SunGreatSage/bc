# API 测试用例集

> 彩票投注系统完整API测试用例
>
> 日期: 2025-11-29

---

## 环境准备

### 测试环境

```bash
# 启动开发服务器
cd /Users/mac/Project/Php/bc/server
php think run -p 8000

# 测试地址
BASE_URL=http://localhost:8000
```

### 测试工具

**推荐工具**:
- Postman
- Apifox
- curl命令行
- VS Code REST Client插件

---

## 测试用例

### 1. 用户登录

**目的**: 获取token用于后续测试

```bash
# 请求
curl -X POST ${BASE_URL}/api/lottery_login/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=test001&password=123456"

# 预期响应
{
  "code": 1,
  "msg": "登录成功",
  "data": {
    "userInfo": {
      "id": 1,
      "username": "test001",
      "nickname": "测试用户"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
}

# 保存token供后续使用
TOKEN="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

**测试点**:
- [x] 正确的用户名和密码能登录成功
- [x] 返回有效的token
- [x] 返回用户信息

---

### 2. 获取用户账户信息

**目的**: 获取用户余额及当前期号时间信息

```bash
# 请求
curl -X GET "${BASE_URL}/api/user_info/getUserInfo?gid=200" \
  -H "token: ${TOKEN}"

# 预期响应
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "credit_limit": "100.00",
    "available_balance": "4.43",
    "remaining_credit": "82.00",
    "sy": "-13.57",
    "frozen_money": "0.00",
    "time_info": {
      "current_qishu": "2025334",
      "open_time": "2025-11-29 08:00:00",
      "close_time": "2025-11-29 21:25:00",
      "kj_time": "2025-11-29 21:30:00",
      "seconds_to_open": -32440,
      "seconds_to_close": 15860,
      "seconds_to_kj": 16160,
      "status": "betting"
    }
  }
}
```

**响应字段说明**:

| 字段 | 类型 | 说明 |
|------|------|------|
| `credit_limit` | string | 信用额度上限 (kmaxmoney) |
| `available_balance` | string | **可用余额** (kmoney, 用于投注判断) |
| `remaining_credit` | string | 剩余可用额度 (计算值) |
| `sy` | string | 上水/返点 |
| `frozen_money` | string | 冻结金额 |
| `time_info.current_qishu` | string | 当前期号 |
| `time_info.open_time` | string | 开盘时间 |
| `time_info.close_time` | string | 封盘时间 |
| `time_info.kj_time` | string | 开奖时间 |
| `time_info.seconds_to_open` | int | 距离开盘秒数 (负数=已开盘) |
| `time_info.seconds_to_close` | int | 距离封盘秒数 |
| `time_info.seconds_to_kj` | int | 距离开奖秒数 |
| `time_info.status` | string | 状态: before_open/betting/closed/settled |

**测试点**:
- [x] 需要登录token验证
- [x] `available_balance` 与投注接口余额判断一致
- [x] 返回开盘、封盘、开奖时间
- [x] 返回倒计时秒数和当前状态

---

### 3. 获取投注号码序列

**目的**: 获取前端投注界面所需的号码/生肖/赔率数据

```bash
# 请求(获取2025年数据)
curl -X GET "${BASE_URL}/api/lottery_bet/getBetNumbers?play_name=特碼&gid=200&year=2025"

# 预期响应(号码类玩法)
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "play_name": "特码",
    "play_type": "number",
    "year": 2025,
    "total_options": 49,
    "options": [
      {
        "value": "01",
        "label": "01",
        "odds": "42.0000",
        "zodiac": "蛇"
      },
      {
        "value": "02",
        "label": "02",
        "odds": "42.0000",
        "zodiac": "龙"
      }
      // ... 共49个号码
    ]
  }
}
```

```bash
# 请求(生肖类玩法)
curl -X GET "${BASE_URL}/api/lottery_bet/getBetNumbers?play_name=六肖&gid=200&year=2025"

# 预期响应(生肖类玩法)
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "play_name": "六肖",
    "play_type": "zodiac",
    "year": 2025,
    "year_zodiac": "蛇",
    "total_options": 12,
    "options": [
      {
        "value": "鼠",
        "label": "鼠",
        "odds": "1.9700",
        "odds_win": "0.0000",
        "odds_not_win": "1.9680",
        "numbers": ["06", "18", "30", "42"],
        "count": 4,
        "is_current_year": false,
        "category": "wild",
        "category_label": "野兽"
      }
      // ... 共12个生肖
    ],
    "category_groups": [
      {
        "type": "domestic",
        "label": "家禽",
        "zodiacs": ["牛", "马", "羊", "鸡", "狗", "猪"],
        "numbers": ["03", "04", "09", ...],
        "total_numbers": 25
      },
      {
        "type": "wild",
        "label": "野兽",
        "zodiacs": ["鼠", "虎", "兔", "龙", "蛇", "猴"],
        "numbers": ["01", "02", "05", ...],
        "total_numbers": 24
      }
    ],
    "odds_types": [
      {"type": "normal", "label": "普通", "odds": "1.9700"},
      {"type": "win", "label": "中", "odds": "0.0000"},
      {"type": "not_win", "label": "不中", "odds": "1.9680"}
    ]
  }
}
```

**支持的玩法名称**: 特碼、特码、正碼、正码、平码、平碼、特肖、三肖、四肖、五肖、六肖

**测试点**:
- [x] 号码类玩法返回49个号码
- [x] 生肖类玩法返回12个生肖
- [x] 返回家禽/野兽分类
- [x] 当年生肖标记 `is_current_year: true`
- [x] 赔率从数据库动态读取

---

### 4. 查询当前期号

**目的**: 获取可投注的期号

```bash
# 请求
curl -X GET "${BASE_URL}/api/lottery_bet/getCurrentQishu?gid=200"

# 预期响应
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "qishu": "2025334",
    "game_name": "新澳门六合彩"
  }
}
```

---

### 5. 查询玩法列表

**目的**: 获取所有可用玩法

```bash
# 请求
curl -X GET "${BASE_URL}/api/lottery_bet/getPlayList?gid=200"

# 预期响应
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "list": [
      {
        "id": "bclass_24926",
        "name": "特碼",
        "type": "bclass"
      },
      {
        "id": "bclass_24927",
        "name": "正碼",
        "type": "bclass"
      },
      {
        "id": "play_97000135",
        "name": "4肖",
        "type": "play",
        "peilv1": "11.0000"
      }
    ]
  }
}
```

**ID格式说明**:
- `bclass_xxx`: 大类玩法，投注时需配合 `bet_content` 指定具体号码/生肖
- `play_xxx`: 具体玩法，直接投注

---

### 6. 投注下单 (批量)

**目的**: 测试批量投注功能

```bash
# 请求 (JSON格式)
curl -X POST ${BASE_URL}/api/lottery_bet/placeBet \
  -H "Content-Type: application/json" \
  -H "token: ${TOKEN}" \
  -d '{
    "gid": 200,
    "qishu": "2025334",
    "orders": [
      {"pid": "bclass_24927", "bet_content": "26", "bet_amount": 1},
      {"pid": "bclass_24927", "bet_content": "08", "bet_amount": 2}
    ]
  }'

# 预期响应 (成功)
{
  "code": 1,
  "msg": "投注成功",
  "data": {
    "success_count": 2,
    "fail_count": 0,
    "total_amount": "3.00",
    "balance": "1.43",
    "qishu": "2025334",
    "results": [
      {
        "index": 0,
        "status": "success",
        "tid": 20000001,
        "bet_content": "26",
        "bet_amount": "1.00",
        "play_name": "26",
        "peilv": "42.0000",
        "expected_prize": "42.00"
      },
      {
        "index": 1,
        "status": "success",
        "tid": 20000002,
        "bet_content": "08",
        "bet_amount": "2.00",
        "play_name": "08",
        "peilv": "42.0000",
        "expected_prize": "84.00"
      }
    ]
  }
}
```

**请求参数说明**:

| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| `gid` | int | 是 | 游戏ID (200=新澳门六合彩) |
| `qishu` | string | 是 | 期号 |
| `orders` | array | 是 | 订单数组 (最多100注) |
| `orders[].pid` | string | 是 | 玩法ID (支持 bclass_xxx / play_xxx / 纯数字) |
| `orders[].bet_content` | string | 是 | 投注内容 (号码如"08"，生肖如"鼠") |
| `orders[].bet_amount` | number | 是 | 投注金额 (正整数) |

**响应字段说明**:

| 字段 | 类型 | 说明 |
|------|------|------|
| `success_count` | int | 成功数量 |
| `fail_count` | int | 失败数量 |
| `total_amount` | string | 总投注金额 |
| `balance` | string | 投注后余额 |
| `results` | array | 每注的结果详情 |
| `results[].status` | string | success / fail |
| `results[].tid` | int | 订单号 (成功时) |
| `results[].error` | string | 错误信息 (失败时) |

---

### 7. 投注下单 - 部分失败

```bash
# 请求 (第二注余额不足)
curl -X POST ${BASE_URL}/api/lottery_bet/placeBet \
  -H "Content-Type: application/json" \
  -H "token: ${TOKEN}" \
  -d '{
    "gid": 200,
    "qishu": "2025334",
    "orders": [
      {"pid": "bclass_24927", "bet_content": "26", "bet_amount": 1},
      {"pid": "bclass_24927", "bet_content": "08", "bet_amount": 9999}
    ]
  }'

# 预期响应 (部分成功)
{
  "code": 1,
  "msg": "投注成功",
  "data": {
    "success_count": 1,
    "fail_count": 1,
    "total_amount": "1.00",
    "balance": "3.43",
    "qishu": "2025334",
    "results": [
      {
        "index": 0,
        "status": "success",
        "tid": 20000003,
        "bet_content": "26",
        "bet_amount": "1.00"
      },
      {
        "index": 1,
        "status": "fail",
        "error": "余额不足 (可用: 3.43, 投注: 9999)",
        "bet_content": "08",
        "bet_amount": "9999.00"
      }
    ]
  }
}
```

---

### 8. 投注下单 - 错误场景

#### 8.1 金额非整数

```bash
curl -X POST ${BASE_URL}/api/lottery_bet/placeBet \
  -H "Content-Type: application/json" \
  -H "token: ${TOKEN}" \
  -d '{
    "gid": 200,
    "qishu": "2025334",
    "orders": [
      {"pid": "bclass_24927", "bet_content": "08", "bet_amount": 1.5}
    ]
  }'

# 预期: 投注金额必须为整数
```

#### 8.2 期号已封盘

```bash
# 预期响应
{
  "code": 0,
  "msg": "投注失败",
  "data": {
    "success_count": 0,
    "fail_count": 1,
    "results": [
      {
        "index": 0,
        "status": "fail",
        "error": "已封盘"
      }
    ]
  }
}
```

#### 8.3 玩法不存在

```bash
curl -X POST ${BASE_URL}/api/lottery_bet/placeBet \
  -H "Content-Type: application/json" \
  -H "token: ${TOKEN}" \
  -d '{
    "gid": 200,
    "qishu": "2025334",
    "orders": [
      {"pid": "bclass_99999", "bet_content": "08", "bet_amount": 1}
    ]
  }'

# 预期: 玩法大类不存在
```

---

### 9. 查询投注记录

```bash
# 请求
curl -X GET "${BASE_URL}/api/lottery_bet/getBetList?page=1&limit=20" \
  -H "token: ${TOKEN}"

# 预期响应
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "list": [
      {
        "tid": "20000001",
        "qishu": "2025334",
        "gid": 200,
        "content": "26",
        "je": "1.00",
        "peilv1": "42.0000",
        "z": 9,
        "status_text": "未开奖",
        "prize": "0.00",
        "time": "2025-11-29 16:30:00"
      }
    ],
    "total": 1,
    "page": 1,
    "limit": 20
  }
}
```

**筛选参数**:
- `qishu`: 按期号筛选
- `gid`: 按游戏筛选
- `z`: 按状态筛选 (9=未开奖, 1=中奖, 0=未中)

---

### 10. 查询开奖结果

```bash
# 请求
curl -X GET "${BASE_URL}/api/lottery_bet/getKjResult?gid=200&qishu=2025333"

# 预期响应 (已开奖)
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "qishu": "2025333",
    "numbers": ["01", "12", "23", "34", "35", "46", "08"],
    "kj_time": "2025-11-28 21:30:00",
    "status": 1
  }
}
```

---

### 11. 获取开奖结果列表

```bash
# 请求
curl -X GET "${BASE_URL}/api/lottery_result/getResultList?gid=200&page=1&limit=10"

# 预期响应
{
  "code": 1,
  "msg": "获取成功",
  "data": {
    "list": [
      {
        "date": "2025-11-28",
        "qishu": "2025333",
        "date_display": "2025-11-28 (2025333)",
        "numbers": [
          {"num": "01", "zodiac": "蛇"},
          {"num": "12", "zodiac": "马"},
          {"num": "23", "zodiac": "羊"},
          {"num": "34", "zodiac": "猴"},
          {"num": "35", "zodiac": "羊"},
          {"num": "46", "zodiac": "狗"},
          {"num": "08", "zodiac": "猴", "is_special": true}
        ],
        "has_result": true,
        "total_score": 159,
        "special_num": "08",
        "special_zodiac": "猴",
        "special_odd_even": "双",
        "special_big_small": "小",
        "special_hesu": 8,
        "special_hesu_odd_even": "双",
        "total_odd_even": "单",
        "total_big_small": "小",
        "one_zodiac": "羊",
        "one_zodiac_count": 2,
        "tail_count": 6,
        "wuxing": "水"
      }
    ],
    "total": 100,
    "page": 1,
    "limit": 10
  }
}
```

---

## 批量测试脚本

```bash
#!/bin/bash

BASE_URL="http://localhost:8000"
TOKEN=""

echo "=== 1. 测试登录 ==="
response=$(curl -s -X POST ${BASE_URL}/api/lottery_login/login \
  -d "username=test001&password=123456")
echo $response | jq '.'
TOKEN=$(echo $response | jq -r '.data.token')
echo ""

echo "=== 2. 获取用户信息 ==="
curl -s "${BASE_URL}/api/user_info/getUserInfo?gid=200" \
  -H "token: ${TOKEN}" | jq '.data'
echo ""

echo "=== 3. 获取当前期号 ==="
response=$(curl -s "${BASE_URL}/api/lottery_bet/getCurrentQishu?gid=200")
QISHU=$(echo $response | jq -r '.data.qishu')
echo "当前期号: $QISHU"
echo ""

echo "=== 4. 获取玩法列表 ==="
curl -s "${BASE_URL}/api/lottery_bet/getPlayList?gid=200" | jq '.data.list[:3]'
echo ""

echo "=== 5. 批量投注 ==="
curl -s -X POST ${BASE_URL}/api/lottery_bet/placeBet \
  -H "Content-Type: application/json" \
  -H "token: ${TOKEN}" \
  -d "{
    \"gid\": 200,
    \"qishu\": \"${QISHU}\",
    \"orders\": [
      {\"pid\": \"bclass_24927\", \"bet_content\": \"26\", \"bet_amount\": 1},
      {\"pid\": \"bclass_24927\", \"bet_content\": \"08\", \"bet_amount\": 1}
    ]
  }" | jq '.'
echo ""

echo "=== 6. 查询投注记录 ==="
curl -s "${BASE_URL}/api/lottery_bet/getBetList?limit=5" \
  -H "token: ${TOKEN}" | jq '.data'
echo ""

echo "=== 测试完成 ==="
```

---

## 测试检查清单

### 功能测试

- [x] 用户登录成功，返回token
- [x] 获取用户信息，余额与投注判断一致
- [x] 获取投注号码数据完整
- [x] 年度轮转正确 (2025蛇年)
- [x] 赔率从数据库动态读取
- [x] 查询当前期号成功
- [x] 查询玩法列表成功
- [x] 批量投注成功
- [x] 部分投注失败不影响其他
- [x] 投注后余额正确扣减
- [x] 投注记录查询正确
- [x] 开奖结果查询正确

### 参数验证

- [x] 金额小于1元时拒绝
- [x] 金额非整数时拒绝
- [x] 余额不足时拒绝
- [x] 期号不存在时拒绝
- [x] 玩法不存在时拒绝
- [x] 单次最多100注限制

### 时间控制

- [x] 未到开盘时间拒绝投注
- [x] 已封盘时拒绝投注
- [x] 已开奖时拒绝投注

### 数据一致性

- [x] 投注后 x_user.kmoney 正确扣减
- [x] 投注记录写入 x_lib 表
- [x] 资金流水写入 x_user_money_log 表
- [x] tid 使用递增方式生成 (int类型)

---

## 常见问题

### Q1: Token过期怎么办?

重新登录获取新token。

### Q2: 如何创建新期号?

```sql
INSERT INTO x_kj (gid, dates, qishu, bml, opentime, closetime, kjtime, baostatus, js)
VALUES (200, '2025-11-29', 2025334, '乙巳',
        '2025-11-29 08:00:00', '2025-11-29 21:25:00', '2025-11-29 21:30:00', 0, 0);

UPDATE x_game SET thisqishu = 2025334 WHERE gid = 200;
```

### Q3: 如何增加用户余额?

```sql
UPDATE x_user SET kmoney = kmoney + 100 WHERE userid = 用户ID;
```

### Q4: pid 格式说明?

- `bclass_24927`: 大类玩法 (正碼)，需要 bet_content 指定号码
- `play_97000135`: 具体玩法 (四肖)，直接投注
- `24927`: 纯数字，默认当作 play 类型

---

**测试文档版本**: v2.0
**最后更新**: 2025-11-29
**状态**: 已更新
