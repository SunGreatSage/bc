<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 期号自动创建服务
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-12-12
// +----------------------------------------------------------------------

namespace app\common\service;

use think\facade\Db;

/**
 * 期号自动创建服务
 * Class LotteryIssueService
 * @package app\common\service
 */
class LotteryIssueService
{
    /**
     * 【只读模式】获取当前可投注的期号(不创建新期号)
     *
     * ⚠️ 前端API专用: 只查询已存在的期号,不会创建新期号
     * ⚠️ 安全原则: 只有开奖接口才能创建新期号
     *
     * @param int $gameId 游戏ID(默认200=新澳门六合彩)
     * @param string $plateCode 盘口代码(默认A)
     * @return array|null 返回期号信息,如果没有可用期号返回null
     */
    public static function getCurrentIssueReadOnly(int $gameId = 200, string $plateCode = 'A'): ?array
    {
        $now = time();
        trace("🔍 [只读] 查询期号: gameId=$gameId, plateCode=$plateCode", 'info');

        // 1. 查询待开盘(status=0)或投注中(status=1)的期号
        $currentIssue = Db::table('la_lottery_issue')
            ->where('game_id', $gameId)
            ->where('plate_code', $plateCode)
            ->whereIn('status', [0, 1])
            ->order('draw_time', 'asc')
            ->find();

        if ($currentIssue) {
            trace("📌 [只读] 找到期号: " . $currentIssue['issue'] . ", status=" . $currentIssue['status'], 'info');

            // 更新状态(如果需要)
            $needUpdate = false;
            $newStatus = $currentIssue['status'];

            if ($now >= $currentIssue['open_time'] && $now < $currentIssue['close_time'] && $currentIssue['status'] != 1) {
                $newStatus = 1;
                $needUpdate = true;
            } elseif ($now >= $currentIssue['close_time'] && $currentIssue['status'] != 2) {
                $newStatus = 2;
                $needUpdate = true;
            }

            if ($needUpdate) {
                Db::table('la_lottery_issue')
                    ->where('id', $currentIssue['id'])
                    ->update(['status' => $newStatus]);
                $currentIssue['status'] = $newStatus;
            }

            return $currentIssue;
        }

        // 2. 查询已封盘(status=2)的期号
        $pendingIssue = Db::table('la_lottery_issue')
            ->where('game_id', $gameId)
            ->where('plate_code', $plateCode)
            ->where('status', 2)
            ->order('draw_time', 'desc')
            ->find();

        if ($pendingIssue) {
            trace("⏳ [只读] 找到等待开奖的期号: " . $pendingIssue['issue'], 'info');
            return $pendingIssue;
        }

        // 3. 没有找到任何可用期号
        trace("❌ [只读] 没有找到可用期号,需要等待开奖后自动创建", 'warning');
        return null;
    }


    /**
     * 获取或创建当前可投注的期号
     *
     * ⚠️ 仅供开奖接口使用: 开奖后自动创建下一期
     * ⚠️ 核心规则: 必须等上一期开奖完毕(status=3且有开奖结果),才能创建下一期
     *
     * @param int $gameId 游戏ID(默认200=新澳门六合彩)
     * @param string $plateCode 盘口代码(默认A)
     * @return array|null 返回期号信息,失败返回null
     */
    public static function getOrCreateCurrentIssue(int $gameId = 200, string $plateCode = 'A', string $strategy = 'plate_config'): ?array
    {
        $now = time();
        trace("🔍 开始获取期号: gameId=$gameId, plateCode=$plateCode", 'info');

        // 1. 先查询是否存在待开盘(status=0)或投注中(status=1)的期号
        $currentIssue = Db::table('la_lottery_issue')
            ->where('game_id', $gameId)
            ->where('plate_code', $plateCode)
            ->whereIn('status', [0, 1])  // 0=待开盘, 1=投注中
            ->order('draw_time', 'asc')
            ->find();

        if ($currentIssue) {
            trace("📌 找到待开盘或投注中的期号: " . $currentIssue['issue'], 'info');
        }

        // 2. 如果找到了待开盘或投注中的期号,检查是否需要更新状态
        if ($currentIssue) {
            $needUpdate = false;
            $newStatus = $currentIssue['status'];

            if ($now >= $currentIssue['open_time'] && $now < $currentIssue['close_time'] && $currentIssue['status'] != 1) {
                $newStatus = 1;  // 投注中
                $needUpdate = true;
            } elseif ($now >= $currentIssue['close_time'] && $now < $currentIssue['draw_time'] && $currentIssue['status'] != 2) {
                $newStatus = 2;  // 已封盘,等待开奖
                $needUpdate = true;
            } elseif ($now >= $currentIssue['draw_time'] && $currentIssue['status'] != 3) {
                // 注意: 到达开奖时间时,不自动设置为已开奖,需要手动开奖后才变为status=3
                // 这里只更新状态为"已封盘",等待管理员开奖
                if ($currentIssue['status'] != 2) {
                    $newStatus = 2;
                    $needUpdate = true;
                }
            }

            if ($needUpdate) {
                Db::table('la_lottery_issue')
                    ->where('id', $currentIssue['id'])
                    ->update(['status' => $newStatus]);
                $currentIssue['status'] = $newStatus;
            }

            return $currentIssue;
        }

        // 3. 查询是否有已封盘(status=2)的期号
        $pendingIssue = Db::table('la_lottery_issue')
            ->where('game_id', $gameId)
            ->where('plate_code', $plateCode)
            ->where('status', 2)  // 2=已封盘,等待开奖
            ->order('draw_time', 'desc')
            ->find();

        // 4. 如果有等待开奖的期号,直接返回(不创建新期号)
        // 前端会显示"等待开奖中"
        if ($pendingIssue) {
            trace("⏳ 找到等待开奖的期号: " . $pendingIssue['issue'], 'info');
            return $pendingIssue;
        }

        // 5. 查询最新的已开奖期号
        $latestIssue = Db::table('la_lottery_issue')
            ->where('game_id', $gameId)
            ->where('plate_code', $plateCode)
            ->order('draw_time', 'desc')
            ->find();

        // 6. 判断是否可以创建新期号
        if ($latestIssue) {
            trace("📋 找到最新期号: " . $latestIssue['issue'] . ", status=" . $latestIssue['status'] . ", result=" . ($latestIssue['result'] ?: '空'), 'info');

            // 必须满足: status=3(已开奖)、有开奖结果且已结算，才能创建下一期。
            if ($latestIssue['status'] == 3 && !empty($latestIssue['result']) && !empty($latestIssue['is_settled'])) {
                trace("✨ 上一期已开奖,准备创建新期号", 'info');
                return self::autoCreateNextIssue($gameId, $plateCode, $latestIssue, $strategy);
            } else {
                trace("⏳ 上一期还未开奖完毕,返回最新期号", 'info');
                return $latestIssue;
            }
        }

        // 7. 如果没有任何期号,创建第一个
        trace("🆕 没有任何期号,创建第一期", 'info');
        return self::autoCreateNextIssue($gameId, $plateCode, null, $strategy);
    }

    /**
     * 预览下一期期号，不写入数据库
     *
     * @param int $gameId 游戏ID
     * @param string $plateCode 盘口代码
     * @param string $strategy 创建策略: plate_config=按盘口配置, immediate=立即开盘, continuous=连续开奖
     * @return array|null
     */
    public static function previewNextIssue(int $gameId = 200, string $plateCode = 'A', string $strategy = 'plate_config'): ?array
    {
        try {
            $latestIssue = Db::table('la_lottery_issue')
                ->where('game_id', $gameId)
                ->where('plate_code', $plateCode)
                ->order('id', 'desc')
                ->find();

            return self::buildNextIssueData($gameId, $plateCode, $latestIssue, $strategy);
        } catch (\Exception $e) {
            trace("❌ 预览期号异常: " . $e->getMessage(), 'error');
            return null;
        }
    }

    public static function previewNextIssueWithBase(
        int $gameId = 200,
        string $plateCode = 'A',
        string $strategy = 'plate_config',
        ?array $latestIssue = null
    ): ?array {
        try {
            return self::buildNextIssueData($gameId, $plateCode, $latestIssue, $strategy);
        } catch (\Exception $e) {
            trace("❌ 预览指定基准期号异常: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * 手动创建下一期期号。
     * 调用方必须先校验当前业务期号是否允许创建；这里仅负责按策略落库。
     */
    public static function forceCreateNextIssue(
        int $gameId = 200,
        string $plateCode = 'A',
        string $strategy = 'plate_config',
        ?array $latestIssue = null
    ): ?array
    {
        try {
            return self::autoCreateNextIssue($gameId, $plateCode, $latestIssue, $strategy);
        } catch (\Exception $e) {
            trace("❌ 手动创建期号异常: " . $e->getMessage(), 'error');
            return null;
        }
    }

    /**
     * 自动创建下一期期号
     *
     * @param int $gameId 游戏ID
     * @param string $plateCode 盘口代码
     * @param array|null $latestIssue 最新期号信息(用于生成期号序列)
     * @return array|null 创建成功返回期号信息,失败返回null
     */
    private static function autoCreateNextIssue(int $gameId, string $plateCode, ?array $latestIssue, string $strategy = 'plate_config'): ?array
    {
        try {
            $now = time();
            $issueData = self::buildNextIssueData($gameId, $plateCode, $latestIssue, $strategy);

            if (!$issueData) {
                trace("❌ 创建期号失败: 无法生成期号数据", 'error');
                return null;
            }

            $insertData = [
                'game_id' => $issueData['game_id'],
                'plate_code' => $issueData['plate_code'],
                'issue' => $issueData['issue'],
                'open_time' => $issueData['open_time'],
                'close_time' => $issueData['close_time'],
                'draw_time' => $issueData['draw_time'],
                'status' => $issueData['status'],
                'result' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            try {
                $insertId = Db::table('la_lottery_issue')->insertGetId($insertData);

                if ($insertId) {
                    // 返回新创建的期号信息
                    $newIssue = Db::table('la_lottery_issue')
                        ->where('id', $insertId)
                        ->find();

                    trace("✅ 成功创建新期号: " . $newIssue['issue'] . ", ID: " . $insertId, 'info');
                    return $newIssue;
                }

                trace("❌ 创建期号失败: insertGetId返回空", 'error');
                return null;

            } catch (\think\db\exception\DbException $dbEx) {
                $message = $dbEx->getMessage();

                // 命中唯一键：说明期号已存在(并发/重复调用)，直接返回已有记录
                if (stripos($message, 'Duplicate entry') !== false) {
                    $existing = Db::table('la_lottery_issue')
                        ->where('game_id', $insertData['game_id'])
                        ->where('plate_code', $insertData['plate_code'])
                        ->where('issue', $insertData['issue'])
                        ->find();

                    if ($existing) {
                        trace("⚠️ 期号已存在(uk_game_plate_issue)，返回已有期号: " . $existing['issue'] . ", ID: " . $existing['id'], 'warning');
                        return $existing;
                    }
                }
                // 数据库异常 - 输出详细信息
                $errorMsg = "数据库错误: " . $dbEx->getMessage();
                $errorData = $dbEx->getData();
                trace("❌ 插入期号时数据库异常: $errorMsg", 'error');
                trace("SQL错误详情: " . json_encode($errorData), 'error');
                trace("插入数据: " . json_encode($insertData), 'error');
                return null;
            }

        } catch (\Exception $e) {
            // 记录日志
            trace("❌ 自动创建期号异常: " . $e->getMessage(), 'error');
            trace("异常类型: " . get_class($e), 'error');
            trace("详细错误: " . $e->getTraceAsString(), 'error');
            return null;
        }
    }

    /**
     * 构造下一期期号数据
     */
    private static function buildNextIssueData(int $gameId, string $plateCode, ?array $latestIssue, string $strategy = 'plate_config'): ?array
    {
        $now = time();
        $strategy = self::normalizeCreationStrategy($strategy);
        $plateConfig = self::getPlateConfig($gameId, $plateCode);

        if ($strategy === 'continuous') {
            return self::buildContinuousIssueData($gameId, $plateCode, $latestIssue, $now);
        }

        if ($strategy === 'immediate') {
            return self::buildImmediateIssueData($gameId, $plateCode, $latestIssue, $now);
        }

        $issueDate = date('Ymd', $now);
        [$openTime, $closeTime, $drawTime] = self::buildConfiguredTimes($issueDate, $plateConfig);
        $sourceText = '按盘口配置时间创建';

        if ($openTime <= $now) {
            $issueDate = date('Ymd', strtotime('+1 day', $now));
            [$openTime, $closeTime, $drawTime] = self::buildConfiguredTimes($issueDate, $plateConfig);
            $sourceText = '盘口配置开盘时间已过，顺延到下一天创建';
        }

        return self::packIssueData($gameId, $plateCode, $latestIssue, $issueDate, $openTime, $closeTime, $drawTime, $strategy, $sourceText, $now);
    }

    private static function buildContinuousIssueData(int $gameId, string $plateCode, ?array $latestIssue, int $now): array
    {
        $today = date('Ymd', $now);

        if ($latestIssue && substr((string)$latestIssue['issue'], 0, 8) === $today && !empty($latestIssue['draw_time'])) {
            $openTime = (int)$latestIssue['draw_time'] + 300;

            if ($openTime > $now) {
                $closeTime = $openTime + 900;
                $drawTime = $closeTime + 300;
                return self::packIssueData($gameId, $plateCode, $latestIssue, $today, $openTime, $closeTime, $drawTime, 'continuous', '按上一期开奖时间后5分钟连续创建', $now);
            }
        }

        return self::buildImmediateIssueData($gameId, $plateCode, $latestIssue, $now, 'continuous', '上一期连续时间已过，改为立即开盘');
    }

    private static function buildImmediateIssueData(int $gameId, string $plateCode, ?array $latestIssue, int $now, string $strategy = 'immediate', string $sourceText = '立即开盘创建'): array
    {
        $openTime = $now;
        $closeTime = $now + 900;
        $drawTime = $closeTime + 300;
        $issueDate = date('Ymd', $now);

        return self::packIssueData($gameId, $plateCode, $latestIssue, $issueDate, $openTime, $closeTime, $drawTime, $strategy, $sourceText, $now);
    }

    private static function packIssueData(
        int $gameId,
        string $plateCode,
        ?array $latestIssue,
        string $issueDate,
        int $openTime,
        int $closeTime,
        int $drawTime,
        string $strategy,
        string $sourceText,
        int $now
    ): array {
        return [
            'game_id' => $gameId,
            'plate_code' => $plateCode,
            'issue' => self::makeNextIssue($latestIssue, $issueDate),
            'open_time' => $openTime,
            'close_time' => $closeTime,
            'draw_time' => $drawTime,
            'status' => self::resolveIssueStatus($now, $openTime, $closeTime, $drawTime),
            'strategy' => $strategy,
            'source_text' => $sourceText,
        ];
    }

    private static function getPlateConfig(int $gameId, string $plateCode): array
    {
        $plateConfig = Db::table('la_plate')
            ->where('game_id', $gameId)
            ->where('code', $plateCode)
            ->where('status', 1)
            ->find();

        if (!$plateConfig) {
            trace("❌ 未找到盘口配置: gameId=$gameId, plateCode=$plateCode", 'error');
            return [
                'open_time' => '06:00',
                'close_time' => '09:30',
                'draw_time' => '09:50',
            ];
        }

        return $plateConfig;
    }

    private static function buildConfiguredTimes(string $issueDate, array $plateConfig): array
    {
        $dateObj = \DateTime::createFromFormat('Ymd', $issueDate);
        $date = $dateObj ? $dateObj->format('Y-m-d') : date('Y-m-d');
        $openTime = strtotime("$date {$plateConfig['open_time']}:00");
        $closeTime = strtotime("$date {$plateConfig['close_time']}:00");
        $drawTime = strtotime("$date {$plateConfig['draw_time']}:00");

        if ($closeTime <= $openTime) {
            $closeTime += 86400;
        }
        if ($drawTime <= $openTime) {
            $drawTime += 86400;
        }

        return [$openTime, $closeTime, $drawTime];
    }

    private static function makeNextIssue(?array $latestIssue, string $issueDate): string
    {
        if ($latestIssue && substr((string)$latestIssue['issue'], 0, 8) === $issueDate) {
            $issueNumber = (int)substr((string)$latestIssue['issue'], 8) + 1;
            return $issueDate . str_pad((string)$issueNumber, 2, '0', STR_PAD_LEFT);
        }

        return $issueDate . '01';
    }

    private static function resolveIssueStatus(int $now, int $openTime, int $closeTime, int $drawTime): int
    {
        if ($now < $openTime) {
            return 0;
        }
        if ($now < $closeTime) {
            return 1;
        }
        return 2;
    }

    private static function normalizeCreationStrategy(string $strategy): string
    {
        return in_array($strategy, ['plate_config', 'immediate', 'continuous'], true) ? $strategy : 'plate_config';
    }


    /**
     * 获取期号的详细信息(包含倒计时)
     *
     * @param array $issue 期号数据
     * @return array 包含倒计时的期号信息
     */
    public static function getIssueWithCountdown(array $issue): array
    {
        $now = time();
        $openTime = $issue['open_time'];
        $closeTime = $issue['close_time'];
        $drawTime = $issue['draw_time'];

        $result = [
            'issue' => $issue['issue'],
            'game_id' => $issue['game_id'],
            'plate_code' => $issue['plate_code'],
            'open_time' => date('Y-m-d H:i:s', $openTime),
            'close_time' => date('Y-m-d H:i:s', $closeTime),
            'draw_time' => date('Y-m-d H:i:s', $drawTime),
            'seconds_to_open' => $openTime - $now,
            'seconds_to_close' => $closeTime - $now,
            'seconds_to_draw' => $drawTime - $now,
            'status' => self::getStatusText($issue['status']),
            'status_code' => $issue['status'],
            'result' => $issue['result'] ?? '',
        ];

        // 添加调试日志
        trace("🔍 [倒计时] plate_code={$issue['plate_code']}, issue={$issue['issue']}", 'info');
        trace("🔍 [倒计时] openTime=$openTime (" . date('Y-m-d H:i:s', $openTime) . "), now=$now (" . date('Y-m-d H:i:s', $now) . ")", 'info');
        trace("🔍 [倒计时] closeTime=$closeTime (" . date('Y-m-d H:i:s', $closeTime) . ")", 'info');
        trace("🔍 [倒计时] drawTime=$drawTime (" . date('Y-m-d H:i:s', $drawTime) . ")", 'info');
        trace("🔍 [倒计时] seconds_to_open=" . $result['seconds_to_open'], 'info');
        trace("🔍 [倒计时] seconds_to_close=" . $result['seconds_to_close'], 'info');
        trace("🔍 [倒计时] seconds_to_draw=" . $result['seconds_to_draw'], 'info');

        return $result;
    }


    /**
     * 获取状态文本
     *
     * @param int $statusCode 状态码
     * @return string 状态文本
     */
    private static function getStatusText(int $statusCode): string
    {
        $statusMap = [
            0 => 'before_open',  // 待开盘
            1 => 'betting',      // 投注中
            2 => 'closed',       // 已封盘
            3 => 'settled',      // 已开奖
        ];

        return $statusMap[$statusCode] ?? 'unknown';
    }
}
