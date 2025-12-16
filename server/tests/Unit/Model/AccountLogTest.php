<?php

namespace tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use app\common\model\lottery\AccountLog;

/**
 * AccountLog 模型单元测试
 */
class AccountLogTest extends TestCase
{
    /**
     * 测试记录投注流水
     */
    public function testRecordBetting()
    {
        $userId = 1;
        $amount = 100.00;
        $account = [
            'balance' => 1000.00,
            'frozen_amount' => 0.00
        ];
        $relatedSn = 'BET20251209001';
        $remark = '投注: 特码 08';
        $ip = '127.0.0.1';

        // 模拟创建流水记录
        $result = AccountLog::recordBetting($userId, $amount, $account, $relatedSn, $remark, $ip);

        // 断言
        $this->assertNotFalse($result, '投注流水记录创建失败');
    }

    /**
     * 测试记录中奖流水
     */
    public function testRecordWinning()
    {
        $userId = 1;
        $prizeAmount = 500.00;
        $frozenAmount = 100.00;
        $account = [
            'balance' => 1000.00,
            'frozen_amount' => 100.00
        ];
        $relatedSn = 'BET20251209001';
        $remark = '中奖派奖: 特码 08';
        $ip = '127.0.0.1';

        // 模拟创建流水记录
        $result = AccountLog::recordWinning($userId, $prizeAmount, $frozenAmount, $account, $relatedSn, $remark, $ip);

        // 断言
        $this->assertNotFalse($result, '中奖流水记录创建失败');
    }

    /**
     * 测试记录解冻流水
     */
    public function testRecordUnfreeze()
    {
        $userId = 1;
        $frozenAmount = 100.00;
        $account = [
            'balance' => 1000.00,
            'frozen_amount' => 100.00
        ];
        $relatedSn = 'BET20251209001';
        $remark = '未中奖解冻: 特码 08';
        $ip = '127.0.0.1';

        // 模拟创建流水记录
        $result = AccountLog::recordUnfreeze($userId, $frozenAmount, $account, $relatedSn, $remark, $ip);

        // 断言
        $this->assertNotFalse($result, '解冻流水记录创建失败');
    }

    /**
     * 测试记录佣金流水
     */
    public function testRecordCommission()
    {
        $userId = 2;
        $commissionAmount = 10.00;
        $account = [
            'balance' => 500.00
        ];
        $relatedSn = 'BET20251209001';
        $remark = '代理佣金: A盘 2025001期';

        // 模拟创建流水记录
        $result = AccountLog::recordCommission($userId, $commissionAmount, $account, $relatedSn, $remark);

        // 断言
        $this->assertNotFalse($result, '佣金流水记录创建失败');
    }

    /**
     * 测试变动类型文本获取器
     */
    public function testGetChangeTypeText()
    {
        $types = [
            1 => '充值',
            2 => '提现',
            3 => '投注',
            4 => '中奖',
            5 => '退款',
            6 => '佣金',
            7 => '调整',
            8 => '冻结',
            9 => '解冻'
        ];

        foreach ($types as $type => $expectedText) {
            $log = new AccountLog();
            $data = ['change_type' => $type];
            $text = $log->getChangeTypeTextAttr(null, $data);

            $this->assertEquals($expectedText, $text, "变动类型 {$type} 的文本不正确");
        }
    }

    /**
     * 测试流水记录字段完整性
     */
    public function testRecordFieldsIntegrity()
    {
        $userId = 1;
        $amount = 100.00;
        $account = [
            'balance' => 1000.00,
            'frozen_amount' => 0.00
        ];
        $relatedSn = 'BET20251209001';
        $remark = '投注: 特码 08';

        // 创建流水记录
        $log = AccountLog::recordBetting($userId, $amount, $account, $relatedSn, $remark);

        // 验证必填字段
        $this->assertArrayHasKey('sn', $log, '缺少 sn 字段');
        $this->assertArrayHasKey('user_id', $log, '缺少 user_id 字段');
        $this->assertArrayHasKey('change_type', $log, '缺少 change_type 字段');
        $this->assertArrayHasKey('change_amount', $log, '缺少 change_amount 字段');
        $this->assertArrayHasKey('balance_before', $log, '缺少 balance_before 字段');
        $this->assertArrayHasKey('balance_after', $log, '缺少 balance_after 字段');
        $this->assertArrayHasKey('frozen_before', $log, '缺少 frozen_before 字段');
        $this->assertArrayHasKey('frozen_after', $log, '缺少 frozen_after 字段');
        $this->assertArrayHasKey('related_sn', $log, '缺少 related_sn 字段');
        $this->assertArrayHasKey('related_type', $log, '缺少 related_type 字段');
        $this->assertArrayHasKey('remark', $log, '缺少 remark 字段');
        $this->assertArrayHasKey('created_at', $log, '缺少 created_at 字段');
    }

    /**
     * 测试余额计算正确性
     */
    public function testBalanceCalculation()
    {
        $userId = 1;
        $amount = 100.00;
        $account = [
            'balance' => 1000.00,
            'frozen_amount' => 0.00
        ];
        $relatedSn = 'BET20251209001';
        $remark = '投注: 特码 08';

        // 创建投注流水
        $log = AccountLog::recordBetting($userId, $amount, $account, $relatedSn, $remark);

        // 验证余额计算
        $this->assertEquals(1000.00, $log['balance_before'], '投注前余额不正确');
        $this->assertEquals(900.00, $log['balance_after'], '投注后余额不正确');
        $this->assertEquals(0.00, $log['frozen_before'], '投注前冻结金额不正确');
        $this->assertEquals(100.00, $log['frozen_after'], '投注后冻结金额不正确');
        $this->assertEquals(-100.00, $log['change_amount'], '变动金额不正确');
    }
}
