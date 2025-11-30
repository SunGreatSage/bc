<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 彩票投注逻辑层(基于 x_user 和 x_lib 表)
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-27
// +----------------------------------------------------------------------

namespace app\api\logic;

use think\facade\Db;
use think\Exception;
use app\common\service\BetLimitService;
use app\common\service\ZodiacYearService;

/**
 * 彩票投注逻辑
 * Class LotteryBetLogic
 * @package app\api\logic
 */
class LotteryBetLogic
{
    /**
     * 错误信息
     */
    private static $error = '';

    /**
     * 获取错误信息
     * @return string
     */
    public static function getError(): string
    {
        return self::$error;
    }

    /**
     * 设置错误信息
     * @param string $error
     */
    private static function setError(string $error): void
    {
        self::$error = $error;
    }


    /**
     * @notes 下单投注(完整事务流程,参考老系统makelib.php)
     * @param array $params 投注参数
     * @return array|false
     * @author Claude
     * @date 2025/11/27
     *
     * 参数说明:
     * @param int params.legacy_userid 老系统用户ID(从x_user表)
     * @param int params.gid 游戏ID(200=新澳门六合彩)
     * @param string params.qishu 期号(如2025112)
     * @param int params.pid 玩法ID(如21401=特码)
     * @param string params.bet_content 投注内容(如"08"表示号码08)
     * @param float params.bet_amount 投注金额
     * @param string params.ip 用户IP地址
     *
     * 流程(参考makelib.php):
     * 1. 验证用户状态(status=1, yingdeny=0)
     * 2. 检查余额(kmoney >= bet_amount)
     * 3. 开启事务
     * 4. 查询玩法配置(x_play表)
     * 5. 插入投注记录(x_lib表)
     * 6. 扣除余额(x_user.kmoney)
     * 7. 记录资金流水(x_user_money_log表)
     * 8. 提交事务
     *
     * 返回:
     * {
     *   "tid": "订单号",
     *   "balance": 剩余余额,
     *   "qishu": "期号",
     *   "prize": 预计中奖金额
     * }
     */
    public static function placeBet(array $params)
    {
        // 参数提取
        $legacyUserId = $params['legacy_userid'];
        $gid = $params['gid'];
        $qishu = $params['qishu'];
        $pid = $params['pid'];
        $betContent = $params['bet_content'];
        $betAmount = $params['bet_amount'];
        $ip = $params['ip'] ?? request()->ip();

        // 参数验证
        if (empty($legacyUserId) || empty($gid) || empty($qishu) || empty($pid)) {
            self::setError('参数缺失');
            return false;
        }

        if ($betAmount <= 0) {
            self::setError('投注金额必须大于0');
            return false;
        }

        // 金额必须为整数(参考makelib.php:208)
        if ($betAmount != floor($betAmount)) {
            self::setError('投注金额必须为整数');
            return false;
        }

        // ========== 新增: 投注限额验证 ==========
        $limitCheck = BetLimitService::validateBetAmount($betAmount);
        if (!$limitCheck['valid']) {
            self::setError($limitCheck['error']);
            return false;
        }

        try {
            // ========== 阶段1: 验证用户状态与余额 ==========
            // 参考makelib.php:112-175

            $user = Db::table('x_user')
                ->where('userid', $legacyUserId)
                ->field('kmoney,status,yingdeny,fudong,pan')
                ->lock(true)  // 行级锁,防止并发
                ->find();

            if (!$user) {
                self::setError('用户不存在');
                return false;
            }

            // 检查用户状态
            if ($user['status'] != 1) {
                self::setError('账户已被停用');
                return false;
            }

            // 检查是否禁止盈利
            if ($user['yingdeny'] == 1) {
                self::setError('账户暂不可用');
                return false;
            }

            // 检查余额
            if ($user['kmoney'] < $betAmount) {
                self::setError('余额不足 (可用: ' . $user['kmoney'] . ', 投注: ' . $betAmount . ')');
                return false;
            }


            // ========== 阶段2: 查询玩法配置 ==========
            // 参考makelib.php:225-238
            //
            // 玩法层级结构:
            // x_bclass (大类: 特碼/正碼/連碼等) -> bid
            // x_class (子类) -> cid
            // x_play (具体玩法: 号码/生肖等) -> pid
            //
            // 支持两种投注方式:
            // 1. pid_type='play': 直接通过 x_play.pid 投注具体玩法
            // 2. pid_type='bclass': 通过 x_bclass.id 投注大类,根据bet_content查找具体pid

            $pidType = $params['pid_type'] ?? 'play';
            $play = null;

            if ($pidType === 'bclass') {
                // bclass模式: 根据大类ID和投注内容查找具体玩法
                // 例如: bclass_24926 (特碼大类) + bet_content="08" -> 找到号码08的pid
                $bclass = Db::table('x_bclass')
                    ->where('id', $pid)
                    ->where('gid', $gid)
                    ->where('ifok', 1)
                    ->field('bid,name')
                    ->find();

                if (!$bclass) {
                    self::setError('玩法大类不存在');
                    return false;
                }

                // 根据大类和投注内容查找具体玩法
                // bet_content 可能是号码(如"08")或生肖(如"虎")
                $play = Db::table('x_play')
                    ->where('gid', $gid)
                    ->where('bid', $bclass['bid'])
                    ->where('name', $betContent)
                    ->where('ifok', 1)
                    ->field('pid,bid,sid,cid,peilv1,peilv2,ifok,name,ztype,znum1,znum2')
                    ->find();

                if (!$play) {
                    self::setError('投注内容无效: ' . $betContent . ' (大类: ' . $bclass['name'] . ')');
                    return false;
                }

                // 更新pid为实际的玩法pid
                $pid = $play['pid'];
            } else {
                // play模式: 直接通过pid查询
                $play = Db::table('x_play')
                    ->where('gid', $gid)
                    ->where('pid', $pid)
                    ->field('pid,bid,sid,cid,peilv1,peilv2,ifok,name,ztype,znum1,znum2')
                    ->find();

                if (!$play) {
                    self::setError('玩法不存在 (pid=' . $pid . ')');
                    return false;
                }
            }

            // 检查玩法是否开放
            if ($play['ifok'] != 1) {
                self::setError('玩法已关闭');
                return false;
            }

            // 检查期号是否有效(当前期号)
            $currentQishu = self::getCurrentQishu($gid);
            if ($qishu != $currentQishu) {
                self::setError('期号已过期,当前期号:' . $currentQishu);
                return false;
            }

            // ========== 新增: 完整的开盘/封盘/开奖检查 ==========
            $periodCheck = self::checkPeriodStatus($gid, $qishu);
            if (!$periodCheck['can_bet']) {
                self::setError($periodCheck['message']);
                return false;
            }


            // ========== 阶段3: 开启事务 ==========
            // 参考makelib.php:204

            Db::startTrans();

            try {
                // ========== 新增: 风控预警检查 ==========
                // 查询用户本期已投注总额
                $periodTotalBet = Db::table('x_lib')
                    ->where('userid', $legacyUserId)
                    ->where('qishu', $qishu)
                    ->where('bs', 1)  // 有效投注
                    ->sum('je');

                $riskAlert = BetLimitService::checkRiskAlert($betAmount, $periodTotalBet);
                if ($riskAlert['alert']) {
                    // 记录风控日志(不阻止投注,仅记录)
                    \think\facade\Log::warning('投注风控预警', [
                        'userid' => $legacyUserId,
                        'qishu' => $qishu,
                        'bet_amount' => $betAmount,
                        'period_total' => $periodTotalBet,
                        'alert_type' => $riskAlert['type'],
                        'message' => $riskAlert['message'],
                    ]);
                }

                // ========== 阶段4: 生成订单号 ==========
                // 参考老系统 csfunc.php setuptid() 函数
                // tid 是 int(11) 类型,最大值约21亿,所以用8位数字递增
                $maxTid = Db::table('x_lib')
                    ->where('userid', $legacyUserId)
                    ->max('tid');

                if (empty($maxTid)) {
                    $tid = 20000000;  // 初始值
                } else {
                    $tid = $maxTid + rand(1, 3);  // 递增 1-3
                }


                // ========== 阶段5: 插入投注记录 ==========
                // 参考makelib.php:557-641

                $insertData = [
                    'tid' => $tid,
                    'userid' => $legacyUserId,
                    'dates' => date('Y-m-d'),  // 日期字段(判奖时从这里提取年份)
                    'qishu' => $qishu,
                    'gid' => $gid,
                    'bid' => $play['bid'],
                    'sid' => $play['sid'],
                    'cid' => $play['cid'],
                    'pid' => $pid,
                    'content' => $betContent,
                    'je' => $betAmount,
                    'peilv1' => $play['peilv1'],
                    'peilv2' => $play['peilv2'] ?? 0,
                    'xtype' => 0,  // 0=正常投注
                    'z' => 9,      // 9=未开奖
                    'bs' => 1,     // 倍数=1
                    'time' => date('Y-m-d H:i:s'),
                    'ip' => $ip,
                ];

                $insertResult = Db::table('x_lib')->insert($insertData);

                if (!$insertResult) {
                    throw new Exception('插入投注记录失败');
                }


                // ========== 阶段6: 扣除余额 ==========
                // 参考makelib.php:653-661

                $updateResult = Db::table('x_user')
                    ->where('userid', $legacyUserId)
                    ->dec('kmoney', $betAmount)
                    ->update();

                if (!$updateResult) {
                    throw new Exception('扣除余额失败');
                }

                // 计算扣款后余额
                $newBalance = $user['kmoney'] - $betAmount;


                // ========== 阶段7: 记录资金流水 ==========
                // 参考makelib.php:659行的usermoneylog函数
                // 表名: x_money_log (不是 x_user_money_log)

                $logData = [
                    'userid' => $legacyUserId,
                    'modiuser' => $legacyUserId,  // 操作人
                    'modisonuser' => 0,           // 子账号(无)
                    'money' => -$betAmount,       // 负数表示扣除
                    'usermoney' => $newBalance,   // 变动后余额
                    'type' => 1,                  // 1=成功
                    'ip' => $ip,
                    'time' => date('Y-m-d H:i:s'),
                    'bz' => '投注',               // 备注
                ];

                $logResult = Db::table('x_money_log')->insert($logData);

                if (!$logResult) {
                    throw new Exception('记录资金流水失败');
                }


                // ========== 阶段8: 提交事务 ==========
                Db::commit();


                // ========== 返回成功数据 ==========
                return [
                    'tid' => $tid,
                    'balance' => number_format($newBalance, 2, '.', ''),
                    'qishu' => $qishu,
                    'bet_amount' => number_format($betAmount, 2, '.', ''),
                    'bet_content' => $betContent,
                    'play_name' => $play['name'],
                    'peilv' => $play['peilv1'],
                    'expected_prize' => number_format($betAmount * $play['peilv1'], 2, '.', ''),  // 预计中奖金额
                ];

            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                self::setError('投注失败: ' . $e->getMessage());
                return false;
            }

        } catch (\Exception $e) {
            self::setError('系统错误: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * @notes 批量下单投注（原子性操作：全部成功或全部失败）
     * @param array $orders 订单数组
     * @return array|false
     * @author Claude
     * @date 2025/11/30
     *
     * 参数说明:
     * @param array orders 订单数组，每个订单包含:
     *   - index: 订单序号
     *   - legacy_userid: 老系统用户ID
     *   - gid: 游戏ID
     *   - qishu: 期号
     *   - pid: 玩法ID
     *   - pid_type: 玩法类型(play/bclass)
     *   - bet_content: 投注内容
     *   - bet_amount: 投注金额
     *   - ip: IP地址
     *
     * 返回:
     * {
     *   "success_count": 3,
     *   "total_amount": "8.00",
     *   "balance": "92.00",
     *   "qishu": "2025334",
     *   "results": [...]
     * }
     */
    public static function placeBetBatch(array $orders)
    {
        if (empty($orders)) {
            self::setError('订单列表不能为空');
            return false;
        }

        // 获取第一个订单的基础信息（所有订单应该是同一用户、同一游戏、同一期号）
        $firstOrder = $orders[0];
        $legacyUserId = $firstOrder['legacy_userid'];
        $gid = $firstOrder['gid'];
        $qishu = $firstOrder['qishu'];

        // 计算总投注金额
        $totalBetAmount = 0;
        foreach ($orders as $order) {
            $totalBetAmount += (float)$order['bet_amount'];
        }

        try {
            // ========== 阶段1: 事务外预验证（快速失败） ==========

            // 1.1 验证用户状态
            $user = Db::table('x_user')
                ->where('userid', $legacyUserId)
                ->field('kmoney,status,yingdeny,fudong,pan')
                ->find();

            if (!$user) {
                self::setError('用户不存在');
                return false;
            }

            if ($user['status'] != 1) {
                self::setError('账户已被停用');
                return false;
            }

            if ($user['yingdeny'] == 1) {
                self::setError('账户暂不可用');
                return false;
            }

            // 1.2 检查总余额是否足够
            if ($user['kmoney'] < $totalBetAmount) {
                self::setError('余额不足 (可用: ' . $user['kmoney'] . ', 需要: ' . $totalBetAmount . ')');
                return false;
            }

            // 1.3 验证期号
            $currentQishu = self::getCurrentQishu($gid);
            if ($qishu != $currentQishu) {
                self::setError('期号已过期,当前期号:' . $currentQishu);
                return false;
            }

            // 1.4 检查开盘/封盘状态
            $periodCheck = self::checkPeriodStatus($gid, $qishu);
            if (!$periodCheck['can_bet']) {
                self::setError($periodCheck['message']);
                return false;
            }

            // 1.5 预验证所有订单的玩法
            $validatedOrders = [];
            foreach ($orders as $order) {
                $index = $order['index'];
                $pid = $order['pid'];
                $pidType = $order['pid_type'] ?? 'play';
                $betContent = $order['bet_content'];
                $betAmount = (float)$order['bet_amount'];

                // 金额验证
                if ($betAmount != floor($betAmount)) {
                    self::setError('第' . ($index + 1) . '注投注失败: 投注金额必须为整数');
                    return false;
                }

                $limitCheck = BetLimitService::validateBetAmount($betAmount);
                if (!$limitCheck['valid']) {
                    self::setError('第' . ($index + 1) . '注投注失败: ' . $limitCheck['error']);
                    return false;
                }

                // 查询玩法配置
                $play = null;
                if ($pidType === 'bclass') {
                    $bclass = Db::table('x_bclass')
                        ->where('id', $pid)
                        ->where('gid', $gid)
                        ->where('ifok', 1)
                        ->field('bid,name')
                        ->find();

                    if (!$bclass) {
                        self::setError('第' . ($index + 1) . '注投注失败: 玩法大类不存在');
                        return false;
                    }

                    $play = Db::table('x_play')
                        ->where('gid', $gid)
                        ->where('bid', $bclass['bid'])
                        ->where('name', $betContent)
                        ->where('ifok', 1)
                        ->field('pid,bid,sid,cid,peilv1,peilv2,ifok,name,ztype,znum1,znum2')
                        ->find();

                    if (!$play) {
                        self::setError('第' . ($index + 1) . '注投注失败: 投注内容无效 (' . $betContent . ')');
                        return false;
                    }

                    $pid = $play['pid'];
                } else {
                    $play = Db::table('x_play')
                        ->where('gid', $gid)
                        ->where('pid', $pid)
                        ->field('pid,bid,sid,cid,peilv1,peilv2,ifok,name,ztype,znum1,znum2')
                        ->find();

                    if (!$play) {
                        self::setError('第' . ($index + 1) . '注投注失败: 玩法不存在 (pid=' . $pid . ')');
                        return false;
                    }
                }

                if ($play['ifok'] != 1) {
                    self::setError('第' . ($index + 1) . '注投注失败: 玩法已关闭');
                    return false;
                }

                // 保存验证通过的订单数据
                $validatedOrders[] = [
                    'index' => $index,
                    'pid' => $pid,
                    'play' => $play,
                    'bet_content' => $betContent,
                    'bet_amount' => $betAmount,
                    'ip' => $order['ip'],
                ];
            }

            // ========== 阶段2: 开启事务执行所有投注 ==========
            Db::startTrans();

            try {
                // 重新获取用户余额（带锁）
                $user = Db::table('x_user')
                    ->where('userid', $legacyUserId)
                    ->field('kmoney')
                    ->lock(true)
                    ->find();

                // 再次检查余额
                if ($user['kmoney'] < $totalBetAmount) {
                    throw new Exception('余额不足 (可用: ' . $user['kmoney'] . ', 需要: ' . $totalBetAmount . ')');
                }

                $results = [];
                $currentBalance = (float)$user['kmoney'];

                // 获取当前最大tid
                $maxTid = Db::table('x_lib')
                    ->where('userid', $legacyUserId)
                    ->max('tid');

                if (empty($maxTid)) {
                    $tid = 20000000;
                } else {
                    $tid = $maxTid;
                }

                // 逐个处理订单
                foreach ($validatedOrders as $orderData) {
                    $index = $orderData['index'];
                    $pid = $orderData['pid'];
                    $play = $orderData['play'];
                    $betContent = $orderData['bet_content'];
                    $betAmount = $orderData['bet_amount'];
                    $ip = $orderData['ip'];

                    // 生成订单号
                    $tid = $tid + rand(1, 3);

                    // 插入投注记录
                    $insertData = [
                        'tid' => $tid,
                        'userid' => $legacyUserId,
                        'dates' => date('Y-m-d'),
                        'qishu' => $qishu,
                        'gid' => $gid,
                        'bid' => $play['bid'],
                        'sid' => $play['sid'],
                        'cid' => $play['cid'],
                        'pid' => $pid,
                        'content' => $betContent,
                        'je' => $betAmount,
                        'peilv1' => $play['peilv1'],
                        'peilv2' => $play['peilv2'] ?? 0,
                        'xtype' => 0,
                        'z' => 9,
                        'bs' => 1,
                        'time' => date('Y-m-d H:i:s'),
                        'ip' => $ip,
                    ];

                    $insertResult = Db::table('x_lib')->insert($insertData);

                    if (!$insertResult) {
                        throw new Exception('第' . ($index + 1) . '注投注失败: 插入记录失败');
                    }

                    // 更新余额
                    $currentBalance -= $betAmount;

                    // 记录结果
                    $results[] = [
                        'index' => $index,
                        'tid' => $tid,
                        'bet_content' => $betContent,
                        'bet_amount' => number_format($betAmount, 2, '.', ''),
                        'play_name' => $play['name'],
                        'peilv' => $play['peilv1'],
                        'expected_prize' => number_format($betAmount * $play['peilv1'], 2, '.', ''),
                    ];
                }

                // 一次性扣除总余额
                $updateResult = Db::table('x_user')
                    ->where('userid', $legacyUserId)
                    ->dec('kmoney', $totalBetAmount)
                    ->update();

                if (!$updateResult) {
                    throw new Exception('扣除余额失败');
                }

                // 记录资金流水（一条汇总记录）
                $logData = [
                    'userid' => $legacyUserId,
                    'modiuser' => $legacyUserId,
                    'modisonuser' => 0,
                    'money' => -$totalBetAmount,
                    'usermoney' => $currentBalance,
                    'type' => 1,
                    'ip' => $firstOrder['ip'],
                    'time' => date('Y-m-d H:i:s'),
                    'bz' => '批量投注(' . count($orders) . '注)',
                ];

                $logResult = Db::table('x_money_log')->insert($logData);

                if (!$logResult) {
                    throw new Exception('记录资金流水失败');
                }

                // 提交事务
                Db::commit();

                return [
                    'success_count' => count($results),
                    'total_amount' => number_format($totalBetAmount, 2, '.', ''),
                    'balance' => number_format($currentBalance, 2, '.', ''),
                    'qishu' => $qishu,
                    'results' => $results,
                ];

            } catch (\Exception $e) {
                Db::rollback();
                self::setError($e->getMessage());
                return false;
            }

        } catch (\Exception $e) {
            self::setError('系统错误: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * @notes 获取当前期号
     * @param int $gid 游戏ID
     * @return string
     * @author Claude
     * @date 2025/11/27
     */
    private static function getCurrentQishu(int $gid): string
    {
        // 查询x_game表获取当前期号
        $game = Db::table('x_game')
            ->where('gid', $gid)
            ->field('thisqishu')
            ->find();

        return $game['thisqishu'] ?? '';
    }


    /**
     * @notes 检查期号状态(开盘/封盘/开奖)
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @return array{can_bet: bool, message: string, countdown: int, period_info: array}
     * @author Claude
     * @date 2025/11/27
     *
     * 返回:
     * {
     *   "can_bet": 是否可以投注,
     *   "message": 提示信息,
     *   "countdown": 倒计时(秒),
     *   "period_info": 期号详细信息
     * }
     */
    private static function checkPeriodStatus(int $gid, string $qishu): array
    {
        // 查询期号信息(参考makelib.php:182-184, 731-742)
        $period = Db::table('x_kj')
            ->where('gid', $gid)
            ->where('qishu', $qishu)
            ->field('opentime, closetime, kjtime, m1, m2, m3, m4, m5, m6, m7, m8')
            ->find();

        if (!$period) {
            return [
                'can_bet' => false,
                'message' => '期号不存在',
                'countdown' => 0,
                'period_info' => [],
            ];
        }

        $now = time();
        $opentime = strtotime($period['opentime']);
        $closetime = strtotime($period['closetime']);
        $kjtime = strtotime($period['kjtime']);

        // 检查1: 是否已开奖
        // m1不为空表示已开奖
        if (!empty($period['m1'])) {
            return [
                'can_bet' => false,
                'message' => '该期已开奖',
                'countdown' => 0,
                'period_info' => $period,
            ];
        }

        // 检查2: 是否到开盘时间
        if ($now < $opentime) {
            return [
                'can_bet' => false,
                'message' => '未到开盘时间',
                'countdown' => $opentime - $now,
                'period_info' => $period,
            ];
        }

        // 检查3: 是否已封盘
        // 参考makelib.php:734-736, 提前封盘时间(默认5分钟=300秒)
        $advanceCloseSeconds = 300;  // 可以从配置读取

        $actualClosetime = $closetime - $advanceCloseSeconds;

        if ($now >= $actualClosetime) {
            return [
                'can_bet' => false,
                'message' => '已封盘',
                'countdown' => 0,
                'period_info' => $period,
            ];
        }

        // 检查4: 是否已过开奖时间(理论上不应该出现)
        if ($now >= $kjtime) {
            return [
                'can_bet' => false,
                'message' => '已过开奖时间',
                'countdown' => 0,
                'period_info' => $period,
            ];
        }

        // 可以投注
        return [
            'can_bet' => true,
            'message' => '可以投注',
            'countdown' => $actualClosetime - $now,
            'period_info' => $period,
        ];
    }


    /**
     * @notes 查询投注记录
     * @param int $legacyUserId 老系统用户ID
     * @param array $params 查询参数
     * @return array
     * @author Claude
     * @date 2025/11/27
     *
     * 参数说明:
     * @param int params.page 页码(默认1)
     * @param int params.limit 每页数量(默认20)
     * @param string params.qishu 期号(可选)
     * @param int params.gid 游戏ID(可选)
     * @param int params.z 中奖状态(可选: 9=未开奖, 1=中奖, 0=未中)
     *
     * 返回:
     * {
     *   "list": [...],
     *   "total": 总记录数,
     *   "page": 当前页,
     *   "limit": 每页数量
     * }
     */
    public static function getBetList(int $legacyUserId, array $params): array
    {
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 20;

        // 构建查询
        $query = Db::table('x_lib')
            ->where('userid', $legacyUserId)
            ->order('time', 'desc');

        // 筛选条件
        if (!empty($params['qishu'])) {
            $query->where('qishu', $params['qishu']);
        }

        if (!empty($params['gid'])) {
            $query->where('gid', $params['gid']);
        }

        if (isset($params['z']) && $params['z'] !== '') {
            $query->where('z', $params['z']);
        }

        // 查询总数
        $total = $query->count();

        // 分页查询 - 包含pid和bid用于关联查询
        $list = $query->page($page, $limit)
            ->field('tid,qishu,gid,bid,pid,content,je,peilv1,z,prize,time')
            ->select()
            ->toArray();

        // 缓存游戏名称和玩法大类名称,避免重复查询
        $gameCache = [];
        $bclassCache = [];
        $playCache = [];

        // 关联查询玩法名称
        foreach ($list as &$item) {
            // 1. 查询游戏名称
            $gid = $item['gid'];
            if (!isset($gameCache[$gid])) {
                $game = Db::table('x_game')
                    ->where('gid', $gid)
                    ->field('gname')
                    ->find();
                $gameCache[$gid] = $game['gname'] ?? '';
            }
            $item['game_name'] = $gameCache[$gid];

            // 2. 查询玩法大类名称(x_bclass)
            $bid = $item['bid'];
            if (!isset($bclassCache[$bid])) {
                $bclass = Db::table('x_bclass')
                    ->where('bid', $bid)
                    ->field('name')
                    ->find();
                $bclassCache[$bid] = $bclass['name'] ?? '';
            }
            $item['bclass_name'] = $bclassCache[$bid];

            // 3. 查询具体玩法名称(x_play)
            $pid = $item['pid'];
            if (!isset($playCache[$pid])) {
                $play = Db::table('x_play')
                    ->where('pid', $pid)
                    ->field('name')
                    ->find();
                $playCache[$pid] = $play['name'] ?? '';
            }
            $item['play_name'] = $playCache[$pid];

            // 4. 组合显示名称: 玩法大类 + 具体玩法
            // 例如: "特碼 - 08" 或 "六肖 - 鼠,牛,虎,兔,龙,蛇"
            if (!empty($item['bclass_name']) && !empty($item['play_name'])) {
                // 如果play_name和content相同(如号码),只显示大类
                if ($item['play_name'] === $item['content']) {
                    $item['play_display'] = $item['bclass_name'];
                } else {
                    $item['play_display'] = $item['bclass_name'] . ' - ' . $item['play_name'];
                }
            } elseif (!empty($item['bclass_name'])) {
                $item['play_display'] = $item['bclass_name'];
            } elseif (!empty($item['play_name'])) {
                $item['play_display'] = $item['play_name'];
            } else {
                $item['play_display'] = '未知玩法';
            }

            // 5. 格式化中奖状态
            $item['status_text'] = self::getStatusText($item['z']);

            // 6. 格式化金额
            $item['je'] = number_format((float)$item['je'], 2, '.', '');
            $item['prize'] = number_format((float)$item['prize'], 2, '.', '');
            $item['peilv1'] = number_format((float)$item['peilv1'], 4, '.', '');

            // 7. 预期奖金
            $item['expected_prize'] = number_format((float)$item['je'] * (float)$item['peilv1'], 2, '.', '');
        }

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }


    /**
     * @notes 获取中奖状态文本
     * @param int $z 中奖状态
     * @return string
     */
    private static function getStatusText(int $z): string
    {
        $statusMap = [
            9 => '未开奖',
            1 => '已中奖',
            0 => '未中奖',
        ];

        return $statusMap[$z] ?? '未知';
    }


    /**
     * @notes 查询开奖结果
     * @param int $gid 游戏ID
     * @param string $qishu 期号
     * @return array|false
     * @author Claude
     * @date 2025/11/27
     */
    public static function getKjResult(int $gid, string $qishu)
    {
        $kj = Db::table('x_kj')
            ->where('gid', $gid)
            ->where('qishu', $qishu)
            ->find();

        if (!$kj) {
            self::setError('开奖记录不存在');
            return false;
        }

        // 解析开奖号码(kj1-kj8字段)
        $numbers = [];
        for ($i = 1; $i <= 8; $i++) {
            $key = 'kj' . $i;
            if (isset($kj[$key]) && $kj[$key] > 0) {
                $numbers[] = str_pad($kj[$key], 2, '0', STR_PAD_LEFT);
            }
        }

        return [
            'qishu' => $kj['qishu'],
            'numbers' => $numbers,
            'kj_time' => $kj['dates'] ?? '',
            'status' => count($numbers) > 0 ? 1 : 0,  // 1=已开奖, 0=未开奖
        ];
    }


    /**
     * @notes 获取可投注的号码序列(含赔率、生肖信息)
     * @param int $year 年份
     * @return array|false
     * @author Claude
     * @date 2025/11/27
     *
     * 返回数据结构:
     * {
     *   "year": 2025,
     *   "year_zodiac": "蛇",
     *   "numbers": [
     *     {
     *       "number": "01",
     *       "zodiac": "蛇",
     *       "is_current_year_zodiac": true,
     *       "odds": {"special_number": "47", "normal_number": "8"}
     *     },
     *     ...
     *   ],
     *   "zodiacs": {
     *     "鼠": {
     *       "numbers": ["06", "18", "30", "42"],
     *       "count": 4,
     *       "odds": {
     *         "special_zodiac": "12",
     *         "three_zodiac": "7",
     *         "four_zodiac": "5",
     *         "five_zodiac": "4",
     *         "six_zodiac": "3"
     *       }
     *     },
     *     ...
     *   },
     *   "summary": {
     *     "total_numbers": 49,
     *     "total_zodiacs": 12,
     *     "special_number_49_zodiac": "蛇"
     *   }
     * }
     */
    public static function getBetNumbers(int $year)
    {
        try {
            // 1. 获取当年生肖
            $yearZodiac = ZodiacYearService::getYearZodiac($year);

            // 2. 获取当年的生肖对应表
            $zodiacTable = ZodiacYearService::getZodiacTableByYear($year);
            $numberMap = ZodiacYearService::getNumberMapByYear($year);

            // 3. 从x_play表查询赔率配置
            $odds = self::getOddsFromDatabase();

            // 4. 构建号码数据(01-49)
            $numbers = [];
            for ($i = 1; $i <= 49; $i++) {
                $numberStr = str_pad($i, 2, '0', STR_PAD_LEFT);
                $zodiac = $numberMap[$i] ?? '';
                $isCurrentYearZodiac = ($zodiac === $yearZodiac);

                // 特殊处理49号的赔率
                $specialNumberOdds = ($i === 49) ? $odds['special_number_49'] : $odds['special_number_normal'];

                $numbers[] = [
                    'number' => $numberStr,
                    'number_int' => $i,
                    'zodiac' => $zodiac,
                    'is_current_year_zodiac' => $isCurrentYearZodiac,
                    'odds' => [
                        'special_number' => (string)$specialNumberOdds,  // 特码赔率
                        'normal_number' => (string)$odds['normal_number'],  // 平码赔率
                    ],
                ];
            }

            // 5. 构建生肖数据
            $zodiacs = [];
            foreach ($zodiacTable as $zodiac => $zodiacNumbers) {
                $numbersFormatted = array_map(function($num) {
                    return str_pad($num, 2, '0', STR_PAD_LEFT);
                }, $zodiacNumbers);

                $zodiacs[$zodiac] = [
                    'name' => $zodiac,
                    'numbers' => $numbersFormatted,
                    'count' => count($zodiacNumbers),
                    'is_current_year' => ($zodiac === $yearZodiac),
                    'odds' => [
                        'special_zodiac' => (string)$odds['special_zodiac'],   // 特肖赔率
                        'three_zodiac' => (string)$odds['three_zodiac'],       // 三肖赔率
                        'four_zodiac' => (string)$odds['four_zodiac'],         // 四肖赔率
                        'five_zodiac' => (string)$odds['five_zodiac'],         // 五肖赔率
                        'six_zodiac' => (string)$odds['six_zodiac'],           // 六肖赔率
                    ],
                ];
            }

            // 6. 返回完整数据
            return [
                'year' => $year,
                'year_zodiac' => $yearZodiac,
                'year_zodiac_numbers' => array_map(function($num) {
                    return str_pad($num, 2, '0', STR_PAD_LEFT);
                }, $zodiacTable[$yearZodiac] ?? []),
                'numbers' => $numbers,
                'zodiacs' => $zodiacs,
                'summary' => [
                    'total_numbers' => 49,
                    'total_zodiacs' => 12,
                    'special_number_49_zodiac' => $numberMap[49] ?? '',
                    'special_number_49_odds' => (string)$odds['special_number_49'],
                ],
                'odds_config' => $odds,  // 返回完整赔率配置供前端参考
            ];

        } catch (\Exception $e) {
            self::setError('获取号码数据失败: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * @notes 根据玩法名称获取可投注选项(号码或生肖)及赔率
     * @param string $playName 玩法名称(特碼/正碼/特肖/三肖/四肖/五肖/六肖)
     * @param int $gid 游戏ID
     * @param int $year 年份(用于生肖轮转计算)
     * @return array|false
     * @author Claude
     * @date 2025/11/28
     */
    public static function getBetOptions(string $playName, int $gid = 200, int $year = 0)
    {
        try {
            if (empty($year)) {
                $year = (int)date('Y');
            }

            // 规范化玩法名称(处理繁简体和别名)
            $normalizedName = self::normalizePlayName($playName);

            // 判断玩法类型
            if (in_array($normalizedName, ['特码', '平码'])) {
                // 号码类玩法
                return self::getNumberOptions($normalizedName, $gid, $year);
            } elseif (in_array($normalizedName, ['特肖', '三肖', '四肖', '五肖', '六肖'])) {
                // 生肖类玩法
                return self::getZodiacOptions($normalizedName, $gid, $year);
            } else {
                self::setError('不支持的玩法类型: ' . $playName);
                return false;
            }

        } catch (\Exception $e) {
            self::setError('获取投注选项失败: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * @notes 规范化玩法名称(繁简体转换、别名处理)
     * @param string $playName 原始玩法名称
     * @return string 规范化后的名称
     */
    private static function normalizePlayName(string $playName): string
    {
        $mapping = [
            '特碼' => '特码',
            '正碼' => '平码',
            '正码' => '平码',
            '平碼' => '平码',
        ];

        return $mapping[$playName] ?? $playName;
    }


    /**
     * @notes 获取号码类玩法的选项(01-49号码)
     * @param string $playName 玩法名称(特码/平码)
     * @param int $gid 游戏ID
     * @param int $year 年份
     * @return array
     */
    private static function getNumberOptions(string $playName, int $gid, int $year): array
    {
        // 获取生肖映射表
        $numberMap = ZodiacYearService::getNumberMapByYear($year);

        // 构建49个号码选项
        $options = [];
        for ($i = 1; $i <= 49; $i++) {
            $numberStr = str_pad($i, 2, '0', STR_PAD_LEFT);

            // 从x_play表查询该号码的赔率
            $odds = Db::table('x_play')
                ->where('gid', $gid)
                ->where('name', $numberStr)
                ->where('ifok', 1)
                ->value('peilv1');

            // 如果数据库没有赔率,使用默认值
            if (!$odds) {
                $odds = ($i == 49) ? 47.0 : 42.0;  // 49号特殊赔率
            }

            $options[] = [
                'value' => $numberStr,
                'label' => $numberStr,
                'odds' => number_format((float)$odds, 4, '.', ''),
                'zodiac' => $numberMap[$i] ?? '',
            ];
        }

        return [
            'play_name' => $playName,
            'play_type' => 'number',
            'year' => $year,
            'total_options' => 49,
            'options' => $options,
        ];
    }


    /**
     * @notes 获取生肖类玩法的选项(12生肖)
     * @param string $playName 玩法名称(特肖/三肖/四肖/五肖/六肖)
     * @param int $gid 游戏ID
     * @param int $year 年份
     * @return array
     */
    private static function getZodiacOptions(string $playName, int $gid, int $year): array
    {
        // 获取当年生肖表
        $zodiacTable = ZodiacYearService::getZodiacTableByYear($year);
        $yearZodiac = ZodiacYearService::getYearZodiac($year);

        // 家禽/野兽分类定义(固定的生肖分类,来自老系统 malhc.php)
        // 注意:使用简体字,与ZodiacYearService返回的数据保持一致
        $domesticZodiacs = ['牛', '马', '羊', '鸡', '狗', '猪'];  // 家禽(简体)
        $wildZodiacs = ['鼠', '虎', '兔', '龙', '蛇', '猴'];      // 野兽(简体)

        // 查询赔率(普通玩法和中/不中玩法)
        $oddsData = self::getZodiacOddsFromDatabase($playName, $gid);

        // 构建12生肖选项
        $options = [];
        foreach ($zodiacTable as $zodiac => $numbers) {
            $numbersFormatted = array_map(function($num) {
                return str_pad($num, 2, '0', STR_PAD_LEFT);
            }, $numbers);

            // 判断是家禽还是野兽
            $category = in_array($zodiac, $domesticZodiacs) ? 'domestic' : 'wild';
            $categoryLabel = in_array($zodiac, $domesticZodiacs) ? '家禽' : '野兽';

            $options[] = [
                'value' => $zodiac,
                'label' => $zodiac,
                'odds' => $oddsData['normal_odds'],
                'odds_win' => $oddsData['win_odds'],      // "中"的赔率
                'odds_not_win' => $oddsData['not_win_odds'],  // "不中"的赔率
                'numbers' => $numbersFormatted,
                'count' => count($numbers),
                'is_current_year' => ($zodiac === $yearZodiac),
                'category' => $category,           // domestic(家禽) 或 wild(野兽)
                'category_label' => $categoryLabel, // "家禽" 或 "野兽"
            ];
        }

        // 根据当年生肖表,动态计算家禽/野兽的号码
        $domesticNumbers = [];
        $wildNumbers = [];

        foreach ($zodiacTable as $zodiac => $numbers) {
            $numbersFormatted = array_map(function($num) {
                return str_pad($num, 2, '0', STR_PAD_LEFT);
            }, $numbers);

            if (in_array($zodiac, $domesticZodiacs)) {
                $domesticNumbers = array_merge($domesticNumbers, $numbersFormatted);
            } else {
                $wildNumbers = array_merge($wildNumbers, $numbersFormatted);
            }
        }

        // 构建家禽/野兽分类汇总
        $categoryGroups = [
            [
                'type' => 'domestic',
                'label' => '家禽',
                'zodiacs' => $domesticZodiacs,
                'numbers' => $domesticNumbers,
                'total_numbers' => count($domesticNumbers),
                'description' => '牛、马、羊、鸡、狗、猪',
            ],
            [
                'type' => 'wild',
                'label' => '野兽',
                'zodiacs' => $wildZodiacs,
                'numbers' => $wildNumbers,
                'total_numbers' => count($wildNumbers),
                'description' => '鼠、虎、兔、龙、蛇、猴',
            ],
        ];

        return [
            'play_name' => $playName,
            'play_type' => 'zodiac',
            'year' => $year,
            'year_zodiac' => $yearZodiac,
            'total_options' => 12,
            'options' => $options,
            'category_groups' => $categoryGroups,  // 新增:家禽/野兽分类汇总
            'odds_types' => [                       // 新增:可用的赔率类型
                [
                    'type' => 'normal',
                    'label' => '普通',
                    'odds' => $oddsData['normal_odds'],
                ],
                [
                    'type' => 'win',
                    'label' => '中',
                    'odds' => $oddsData['win_odds'],
                ],
                [
                    'type' => 'not_win',
                    'label' => '不中',
                    'odds' => $oddsData['not_win_odds'],
                ],
            ],
            'special_rules' => [
                'rule_49' => '开出49号视为和局,投注金额退还',
            ],
        ];
    }


    /**
     * @notes 从数据库查询生肖玩法的赔率(包括普通/中/不中)
     * @param string $playName 玩法名称
     * @param int $gid 游戏ID
     * @return array
     */
    private static function getZodiacOddsFromDatabase(string $playName, int $gid): array
    {
        // 默认赔率配置
        $defaultOdds = [
            '特肖' => ['normal' => 12.0, 'win' => 0, 'not_win' => 0],
            '三肖' => ['normal' => 88.0, 'win' => 88.488, 'not_win' => 78.948],
            '四肖' => ['normal' => 11.0, 'win' => 11.088, 'not_win' => 10.428],
            '五肖' => ['normal' => 2.09, 'win' => 2.088, 'not_win' => 2.028],
            '六肖' => ['normal' => 1.97, 'win' => 0, 'not_win' => 1.968],
        ];

        // 查询普通玩法赔率
        $normalOdds = Db::table('x_play')
            ->where('gid', $gid)
            ->where('name', $playName)
            ->where('ifok', 1)
            ->value('peilv1');

        // 查询"中"玩法赔率(如"5肖連(中)")
        $winPlayName = self::getPlayNameVariant($playName, 'win');
        $winOdds = 0;
        if ($winPlayName) {
            $winOddsValue = Db::table('x_play')
                ->where('gid', $gid)
                ->where('name', $winPlayName)
                ->where('ifok', 1)
                ->value('peilv1');

            // 如果peilv1为0,尝试从pl字段解析
            if ($winOddsValue == 0) {
                $plData = Db::table('x_play')
                    ->where('gid', $gid)
                    ->where('name', $winPlayName)
                    ->where('ifok', 1)
                    ->value('pl');
                if ($plData) {
                    $plArray = json_decode($plData, true);
                    if (is_array($plArray) && isset($plArray[0][0])) {
                        $winOdds = (float)$plArray[0][0];
                    }
                }
            } else {
                $winOdds = (float)$winOddsValue;
            }
        }

        // 查询"不中"玩法赔率(如"6肖連(不中)")
        $notWinPlayName = self::getPlayNameVariant($playName, 'not_win');
        $notWinOdds = 0;
        if ($notWinPlayName) {
            $notWinOddsValue = Db::table('x_play')
                ->where('gid', $gid)
                ->where('name', $notWinPlayName)
                ->where('ifok', 1)
                ->value('peilv1');

            // 如果peilv1为0,尝试从pl字段解析
            if ($notWinOddsValue == 0) {
                $plData = Db::table('x_play')
                    ->where('gid', $gid)
                    ->where('name', $notWinPlayName)
                    ->where('ifok', 1)
                    ->value('pl');
                if ($plData) {
                    $plArray = json_decode($plData, true);
                    if (is_array($plArray) && isset($plArray[0][0])) {
                        $notWinOdds = (float)$plArray[0][0];
                    }
                }
            } else {
                $notWinOdds = (float)$notWinOddsValue;
            }
        }

        // 使用查询结果或默认值
        $defaults = $defaultOdds[$playName] ?? ['normal' => 1.0, 'win' => 0, 'not_win' => 0];

        return [
            'normal_odds' => number_format($normalOdds ?: $defaults['normal'], 4, '.', ''),
            'win_odds' => number_format($winOdds ?: $defaults['win'], 4, '.', ''),
            'not_win_odds' => number_format($notWinOdds ?: $defaults['not_win'], 4, '.', ''),
        ];
    }


    /**
     * @notes 获取玩法名称的变种(中/不中)
     * @param string $playName 原始玩法名称
     * @param string $type win(中) 或 not_win(不中)
     * @return string|null
     */
    private static function getPlayNameVariant(string $playName, string $type): ?string
    {
        // 玩法名称映射表
        $mapping = [
            '三肖' => ['win' => '3肖連(中)', 'not_win' => '3肖連(不中)'],
            '四肖' => ['win' => '4肖連(中)', 'not_win' => '4肖連(不中)'],
            '五肖' => ['win' => '5肖連(中)', 'not_win' => '5肖連(不中)'],
            '六肖' => ['win' => '6肖連(中)', 'not_win' => '6肖連(不中)'],
        ];

        return $mapping[$playName][$type] ?? null;
    }


    /**
     * @notes 从x_play表查询赔率配置
     * @return array 赔率配置数组
     * @author Claude
     * @date 2025/11/28
     *
     * 查询逻辑:
     * 1. 特码赔率: 查询pid对应号码01-49的peilv1
     * 2. 平码赔率: 查询平码玩法的peilv1
     * 3. 生肖赔率: 查询特肖、三肖、四肖、五肖、六肖的peilv1
     *
     * 表结构参考:
     * - x_play.pid: 玩法ID
     * - x_play.name: 玩法名称(如"01","特肖","三肖")
     * - x_play.peilv1: 普通用户赔率
     * - x_play.gid: 游戏ID (200=香港六合彩)
     * - x_play.ifok: 是否启用 (1=启用)
     */
    private static function getOddsFromDatabase(): array
    {
        // 默认赔率(兜底值)
        $defaultOdds = [
            'special_number_normal' => 42.0,
            'special_number_49' => 47.0,
            'normal_number' => 8.0,
            'special_zodiac' => 12.0,
            'three_zodiac' => 7.0,
            'four_zodiac' => 5.0,
            'five_zodiac' => 4.0,
            'six_zodiac' => 3.0,
        ];

        try {
            // 查询特码赔率(号码01的赔率,通用赔率)
            $specialNumberNormal = Db::table('x_play')
                ->where('gid', 200)
                ->where('name', '01')
                ->where('ifok', 1)
                ->value('peilv1');

            // 查询49号特码赔率
            $specialNumber49 = Db::table('x_play')
                ->where('gid', 200)
                ->where('name', '49')
                ->where('ifok', 1)
                ->value('peilv1');

            // 查询平码赔率(查询平码玩法)
            $normalNumber = Db::table('x_play')
                ->where('gid', 200)
                ->where('name', 'like', '%平码%')
                ->where('ifok', 1)
                ->value('peilv1');

            // 查询特肖赔率
            $specialZodiac = Db::table('x_play')
                ->where('gid', 200)
                ->where('name', 'like', '%特肖%')
                ->where('ifok', 1)
                ->value('peilv1');

            // 查询三肖赔率
            $threeZodiac = Db::table('x_play')
                ->where('gid', 200)
                ->where('name', 'like', '%三肖%')
                ->where('name', 'not like', '%不中%')
                ->where('ifok', 1)
                ->value('peilv1');

            // 查询四肖赔率
            $fourZodiac = Db::table('x_play')
                ->where('gid', 200)
                ->where('name', 'like', '%四肖%')
                ->where('name', 'not like', '%不中%')
                ->where('ifok', 1)
                ->value('peilv1');

            // 查询五肖赔率
            $fiveZodiac = Db::table('x_play')
                ->where('gid', 200)
                ->where('name', 'like', '%五肖%')
                ->where('name', 'not like', '%不中%')
                ->where('ifok', 1)
                ->value('peilv1');

            // 查询六肖赔率
            $sixZodiac = Db::table('x_play')
                ->where('gid', 200)
                ->where('name', 'like', '%六肖%')
                ->where('name', 'not like', '%不中%')
                ->where('ifok', 1)
                ->value('peilv1');

            // 组装赔率数组(使用数据库值,如果查询失败则使用默认值)
            return [
                'special_number_normal' => $specialNumberNormal ?: $defaultOdds['special_number_normal'],
                'special_number_49' => $specialNumber49 ?: $defaultOdds['special_number_49'],
                'normal_number' => $normalNumber ?: $defaultOdds['normal_number'],
                'special_zodiac' => $specialZodiac ?: $defaultOdds['special_zodiac'],
                'three_zodiac' => $threeZodiac ?: $defaultOdds['three_zodiac'],
                'four_zodiac' => $fourZodiac ?: $defaultOdds['four_zodiac'],
                'five_zodiac' => $fiveZodiac ?: $defaultOdds['five_zodiac'],
                'six_zodiac' => $sixZodiac ?: $defaultOdds['six_zodiac'],
            ];

        } catch (\Exception $e) {
            // 查询失败时返回默认赔率
            return $defaultOdds;
        }
    }
}
