<?php

namespace tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use app\common\model\lottery\WinningRecord;

/**
 * WinningRecord 模型单元测试
 */
class WinningRecordTest extends TestCase
{
    /**
     * 测试创建中奖记录
     */
    public function testRecordWin()
    {
        $betting = [
            'id' => 1,
            'user_id' => 1,
            'game_id' => 200,
            'plate_id' => 1,
            'plate_code' => 'A',
            'issue_id' => 1,
            'issue' => '2025001',
            'method_name' => '特码',
            'total_amount' => 100.00,
            'odds' => 5.0
        ];
        $prizeAmount = 500.00;

        // 模拟创建中奖记录
        $result = WinningRecord::recordWin($betting, $prizeAmount);

        // 断言
        $this->assertNotFalse($result, '中奖记录创建失败');
    }

    /**
     * 测试中奖记录字段完整性
     */
    public function testRecordFieldsIntegrity()
    {
        $betting = [
            'id' => 1,
            'user_id' => 1,
            'game_id' => 200,
            'plate_id' => 1,
            'plate_code' => 'A',
            'issue_id' => 1,
            'issue' => '2025001',
            'method_name' => '特码',
            'total_amount' => 100.00,
            'odds' => 5.0
        ];
        $prizeAmount = 500.00;

        // 创建中奖记录
        $record = WinningRecord::recordWin($betting, $prizeAmount);

        // 验证必填字段
        $this->assertArrayHasKey('sn', $record, '缺少 sn 字段');
        $this->assertArrayHasKey('betting_id', $record, '缺少 betting_id 字段');
        $this->assertArrayHasKey('user_id', $record, '缺少 user_id 字段');
        $this->assertArrayHasKey('game_id', $record, '缺少 game_id 字段');
        $this->assertArrayHasKey('plate_id', $record, '缺少 plate_id 字段');
        $this->assertArrayHasKey('plate_code', $record, '缺少 plate_code 字段');
        $this->assertArrayHasKey('issue_id', $record, '缺少 issue_id 字段');
        $this->assertArrayHasKey('issue', $record, '缺少 issue 字段');
        $this->assertArrayHasKey('method_name', $record, '缺少 method_name 字段');
        $this->assertArrayHasKey('bet_amount', $record, '缺少 bet_amount 字段');
        $this->assertArrayHasKey('odds', $record, '缺少 odds 字段');
        $this->assertArrayHasKey('prize_amount', $record, '缺少 prize_amount 字段');
        $this->assertArrayHasKey('net_profit', $record, '缺少 net_profit 字段');
        $this->assertArrayHasKey('is_paid', $record, '缺少 is_paid 字段');
        $this->assertArrayHasKey('paid_at', $record, '缺少 paid_at 字段');
        $this->assertArrayHasKey('created_at', $record, '缺少 created_at 字段');
    }

    /**
     * 测试净利润计算正确性
     */
    public function testNetProfitCalculation()
    {
        $betting = [
            'id' => 1,
            'user_id' => 1,
            'game_id' => 200,
            'plate_id' => 1,
            'plate_code' => 'A',
            'issue_id' => 1,
            'issue' => '2025001',
            'method_name' => '特码',
            'total_amount' => 100.00,
            'odds' => 5.0
        ];
        $prizeAmount = 500.00;

        // 创建中奖记录
        $record = WinningRecord::recordWin($betting, $prizeAmount);

        // 验证净利润计算
        $expectedNetProfit = $prizeAmount - $betting['total_amount'];
        $this->assertEquals($expectedNetProfit, $record['net_profit'], '净利润计算不正确');
        $this->assertEquals(400.00, $record['net_profit'], '净利润应该是 400.00');
    }

    /**
     * 测试派奖状态
     */
    public function testPaidStatus()
    {
        $betting = [
            'id' => 1,
            'user_id' => 1,
            'game_id' => 200,
            'plate_id' => 1,
            'plate_code' => 'A',
            'issue_id' => 1,
            'issue' => '2025001',
            'method_name' => '特码',
            'total_amount' => 100.00,
            'odds' => 5.0
        ];
        $prizeAmount = 500.00;

        // 创建中奖记录
        $record = WinningRecord::recordWin($betting, $prizeAmount);

        // 验证派奖状态
        $this->assertEquals(1, $record['is_paid'], '派奖状态应该是 1');
        $this->assertNotEmpty($record['paid_at'], '派奖时间不应该为空');
        $this->assertIsInt($record['paid_at'], '派奖时间应该是整数时间戳');
    }

    /**
     * 测试序列号生成
     */
    public function testSnGeneration()
    {
        $betting = [
            'id' => 1,
            'user_id' => 1,
            'game_id' => 200,
            'plate_id' => 1,
            'plate_code' => 'A',
            'issue_id' => 1,
            'issue' => '2025001',
            'method_name' => '特码',
            'total_amount' => 100.00,
            'odds' => 5.0
        ];
        $prizeAmount = 500.00;

        // 创建中奖记录
        $record = WinningRecord::recordWin($betting, $prizeAmount);

        // 验证序列号格式
        $this->assertStringStartsWith('WIN', $record['sn'], '序列号应该以 WIN 开头');
        $this->assertMatchesRegularExpression('/^WIN\d{14}\d{4}$/', $record['sn'], '序列号格式不正确');
    }

    /**
     * 测试字段值正确性
     */
    public function testFieldValues()
    {
        $betting = [
            'id' => 123,
            'user_id' => 456,
            'game_id' => 200,
            'plate_id' => 1,
            'plate_code' => 'A',
            'issue_id' => 789,
            'issue' => '2025001',
            'method_name' => '特码',
            'total_amount' => 100.00,
            'odds' => 5.0
        ];
        $prizeAmount = 500.00;

        // 创建中奖记录
        $record = WinningRecord::recordWin($betting, $prizeAmount);

        // 验证字段值
        $this->assertEquals(123, $record['betting_id'], 'betting_id 不正确');
        $this->assertEquals(456, $record['user_id'], 'user_id 不正确');
        $this->assertEquals(200, $record['game_id'], 'game_id 不正确');
        $this->assertEquals(1, $record['plate_id'], 'plate_id 不正确');
        $this->assertEquals('A', $record['plate_code'], 'plate_code 不正确');
        $this->assertEquals(789, $record['issue_id'], 'issue_id 不正确');
        $this->assertEquals('2025001', $record['issue'], 'issue 不正确');
        $this->assertEquals('特码', $record['method_name'], 'method_name 不正确');
        $this->assertEquals(100.00, $record['bet_amount'], 'bet_amount 不正确');
        $this->assertEquals(5.0, $record['odds'], 'odds 不正确');
        $this->assertEquals(500.00, $record['prize_amount'], 'prize_amount 不正确');
    }
}
