<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\common\command;

use app\common\enum\CrontabEnum;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use Cron\CronExpression;
use think\facade\Console;
use app\common\model\Crontab as CrontabModel;

/**
 * 定时任务
 * Class Crontab
 * @package app\command
 */
class Crontab extends Command
{
    protected function configure()
    {
        $this->setName('crontab')
            ->setDescription('定时任务');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('Start crontab runner...');

        $lists = CrontabModel::where('status', CrontabEnum::START)->select()->toArray();
        if (empty($lists)) {
            $output->writeln('No active crontab tasks');
            return;
        }

        $time =  time();
        $initialized = 0;
        $skipped = 0;
        $executed = 0;
        $failed = 0;

        foreach ($lists as $item) {
            if (empty($item['last_time'])) {
                $lastTime = (new CronExpression($item['expression']))
                    ->getNextRunDate()
                    ->getTimestamp();
                CrontabModel::where('id', $item['id'])->update([
                    'last_time' => $lastTime,
                ]);
                $initialized++;
                $output->writeln(sprintf(
                    'Init task #%s %s command=%s next_run=%s',
                    $item['id'],
                    $item['name'] ?? '',
                    $item['command'] ?? '',
                    date('Y-m-d H:i:s', $lastTime)
                ));
                continue;
            }

            $nextTime = (new CronExpression($item['expression']))
                ->getNextRunDate($item['last_time'])
                ->getTimestamp();
            if ($nextTime > $time) {
                // 未到时间，不执行
                $skipped++;
                $output->writeln(sprintf(
                    'Skip task #%s %s command=%s next_run=%s',
                    $item['id'],
                    $item['name'] ?? '',
                    $item['command'] ?? '',
                    date('Y-m-d H:i:s', $nextTime)
                ));
                continue;
            }
            // 开始执行
            $result = self::start($item);
            $executed++;
            if (empty($result['success'])) {
                $failed++;
            }

            $output->writeln(sprintf(
                'Run task #%s %s command=%s status=%s duration=%ss',
                $item['id'],
                $item['name'] ?? '',
                $item['command'] ?? '',
                empty($result['success']) ? 'failed' : 'success',
                $result['duration'] ?? 0
            ));

            if (!empty($result['output'])) {
                $output->writeln('Command output:');
                foreach (preg_split('/\r\n|\r|\n/', trim($result['output'])) as $line) {
                    $output->writeln('  ' . $line);
                }
            }

            if (!empty($result['error'])) {
                $output->writeln('Command error: ' . $result['error']);
            }
        }

        $output->writeln(sprintf(
            'Crontab runner completed: active=%d initialized=%d skipped=%d executed=%d failed=%d',
            count($lists),
            $initialized,
            $skipped,
            $executed,
            $failed
        ));
    }

    public static function start($item)
    {
        // 开始执行
        $startTime = microtime(true);
        $result = [
            'success' => true,
            'output' => '',
            'error' => '',
            'duration' => 0,
        ];

        try {
            $params = [];
            if (!empty($item['params'])) {
                $params = preg_split('/\s+/', trim($item['params'])) ?: [];
            }

            if (!empty($params)) {
                $commandOutput = Console::call($item['command'], $params);
            } else {
                $commandOutput = Console::call($item['command']);
            }

            $result['output'] = trim($commandOutput->fetch());

            // 清除错误信息
            CrontabModel::where('id', $item['id'])->update(['error' => '']);
        } catch (\Throwable $e) {
            $result['success'] = false;
            $result['error'] = $e->getMessage();

            // 记录错误信息
            CrontabModel::where('id', $item['id'])->update([
                'error' => $e->getMessage(),
                'status' => CrontabEnum::ERROR
            ]);
        } finally {
            $endTime = microtime(true);
            // 本次执行时间
            $useTime = round(($endTime - $startTime), 2);
            // 最大执行时间
            $maxTime = max($useTime, $item['max_time']);
            // 更新最后执行时间
            CrontabModel::where('id', $item['id'])->update([
                'last_time' => time(),
                'time' => $useTime,
                'max_time' => $maxTime
            ]);

            $result['duration'] = $useTime;
        }

        return $result;
    }
}
