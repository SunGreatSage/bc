# /adminapi/best_plan/calculateRealtime 返回 10%-100% 利润率列表方案

## 目标
- 接口一次返回 10%~100% 的利润率分档列表。
- 按利润率从高到低排序：100%、90%、80%...10%。
- 忽略请求中的 `target_rate` / `tolerance` 参数。
- 每个 10% 分档至少返回 10 组号码（7 个号码一组）。

## 输入（保持不变）
- 路由：`POST /adminapi/best_plan/calculateRealtime`
- 现有参数继续接收，但 `rate_buckets` 忽略 `target_rate` / `tolerance` 的影响；其它字段保持现有逻辑。

## 输出（新增字段，兼容原结构）
建议在原有响应基础上新增 `rate_buckets` 字段，不改变现有字段以保证兼容。
当前接口已有返回字段需保留（部分为可选/可能为空）：
- `summary`（无投注时可能包含 `has_bets: false`）
- `best_solution`
- `top_solutions`
- `risk_assessment`
- `recommendations`
- `strategy_used`
- `target_rate_config`（传入 `target_rate` 时）
- `message`（无投注时）

```json
{
  "code": 1,
  "msg": "计算完成",
  "data": {
    "summary": { /* 现有字段 */ },
    "best_solution": { /* 现有字段 */ },
    "top_solutions": [ /* 现有字段 */ ],
    "risk_assessment": { /* 现有字段，可能为 null */ },
    "recommendations": [ /* 现有字段 */ ],
    "strategy_used": "balanced",
    "target_rate_config": { /* 现有字段，传入 target_rate 时 */ },
    "message": "无投注时的提示信息",
    "rate_buckets": [
      {
        "rate": 100,
        "range": "=100.00",
        "effective_range": "=100.00",
        "in_range_count": 10,
        "count": 10,
        "filled": false,
        "solutions": [
          {
            "numbers": [1,2,3,4,5,6,7],
            "m1_m6": [1,2,3,4,5,6],
            "m7": 7,
            "profit_rate": 100.0,
            "total_profit": 85000,
            "total_prize": 15000,
            "bet_amount": 100000
          }
        ]
      }
    ]
  }
}
```

## 分档规则（考虑 100% 上限）
- 先将 `profit_rate` 按 2 位小数四舍五入得到 `profit_rate_rounded`。
- 分档固定为：`[100, 90, 80, 70, 60, 50, 40, 30, 20, 10]`。
- 档位范围：
  - 100：`profit_rate_rounded == 100.00`（全吃，上限）
  - 90：`90.00 <= profit_rate_rounded < 100.00`
  - 80：`80.00 <= profit_rate_rounded < 90.00`
  - ...
  - 10：`10.00 <= profit_rate_rounded < 20.00`
- 说明：利润率理论上不超过 100%，因此 100 档是最大值。

## 排序规则
- `rate_buckets` 按 `rate` 从高到低排列。
- 每个 bucket 内的 `solutions` 按 `profit_rate` 降序，再按 `total_profit` 降序。

## 号码规则（m1-m6）
- m1-m6 号码必须唯一，不允许重复数字。
- 前 6 个号码允许出现相同生肖（不要求全部不同）。
- 允许生肖重复，但同一生肖在 m1-m6 中最多出现 2 次。
  - 例：同生肖号码如 1/13/25/37/49，m1-m6 中最多只取其中 2 个。
- 组合多样性：在满足利润率筛选的前提下，避免明显的连续序列（如 1,2,3,4,5,6），优先选择分布更均衡的组合（奇偶/大小/区间更均衡）。

## 补齐策略（针对 100% 占比高）
当某档不足 10 组时，使用“真实优先 + 渐进扩容 + 就近补齐”的策略：
1. 严格命中：先按档位范围归档，记录 `in_range_count`。
2. 区间补齐：优先用利润率范围搜索补齐（例如：按档位 min/max 调用范围搜索获取更多解）。
3. 渐进扩容：仍不足时，以 2% 为步长向两侧扩展范围，直到满 10 组或触达上下界（10~100）。
   - 扩展后的范围写入 `effective_range`，并设置 `filled: true`。
4. 就近补齐：若仍不足，或无法精确筛选出档位范围内的解（如 10% 档不够准确），从全量解中按“利润率距离该档中心值最近”补齐，标记 `fill_reason: "nearest_profit_rate"`。
5. 不足兜底：若全量解总数 < 100，允许跨档复用号码组，标记 `duplicate: true`，仍保证每档 >= 10。

## 无投注或极端场景
- 若总投注为 0 或利润率高度集中在 100 档：
  - 100 档正常填充；其余档位通过补齐生成，并标记 `filled: true`。
  - `in_range_count` 用于体现真实命中数量，避免误解为真实分布。

## 忽略 target_rate / tolerance
- `rate_buckets` 的分档与补齐不受 `target_rate` / `tolerance` 影响。
- 其它字段（如 `best_solution`/`top_solutions`/`strategy_used`/`target_rate_config`）保持现有逻辑，可能仍受影响。
- 结果始终返回 10 档固定列表（100 → 10）。

## 验收标准
- 响应包含 10 个固定档位，按 100→10 顺序。
- 每个档位 `solutions.length >= 10`。
- 当发生补齐或复用时，必须带上 `filled`/`fill_reason`/`duplicate` 标记。
- `target_rate` / `tolerance` 不改变输出。

