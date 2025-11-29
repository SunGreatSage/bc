<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - 开奖结果控制器
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-11-29
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\common\service\ZodiacYearService;
use think\facade\Db;
use think\response\Json;

/**
 * 开奖结果控制器
 * Class LotteryResultController
 * @package app\api\controller
 */
class LotteryResultController extends BaseApiController
{
    /**
     * 不需要登录的接口
     */
    public array $notNeedLogin = ['getResultList'];

    /**
     * 五行对应表（号码 => 五行）
     * 金: 05,06,19,20,27,28,35,36,49
     * 木: 01,02,09,10,17,18,31,32,39,40,47,48
     * 水: 07,08,15,16,23,24,37,38,45,46
     * 火: 03,04,11,12,25,26,33,34,41,42
     * 土: 13,14,21,22,29,30,43,44
     */
    private const WUXING_MAP = [
        '01' => '木', '02' => '木', '03' => '火', '04' => '火', '05' => '金',
        '06' => '金', '07' => '水', '08' => '水', '09' => '木', '10' => '木',
        '11' => '火', '12' => '火', '13' => '土', '14' => '土', '15' => '水',
        '16' => '水', '17' => '木', '18' => '木', '19' => '金', '20' => '金',
        '21' => '土', '22' => '土', '23' => '水', '24' => '水', '25' => '火',
        '26' => '火', '27' => '金', '28' => '金', '29' => '土', '30' => '土',
        '31' => '木', '32' => '木', '33' => '火', '34' => '火', '35' => '金',
        '36' => '金', '37' => '水', '38' => '水', '39' => '木', '40' => '木',
        '41' => '火', '42' => '火', '43' => '土', '44' => '土', '45' => '水',
        '46' => '水', '47' => '木', '48' => '木', '49' => '金',
    ];


    /**
     * @notes 获取开奖结果列表
     * @return Json
     * @author Claude
     * @date 2025/11/29
     *
     * 请求参数:
     * @param int gid 游戏ID(可选, 默认200=新澳门六合彩)
     * @param int page 页码(可选, 默认1)
     * @param int limit 每页数量(可选, 默认20, 最大100)
     *
     * 响应示例:
     * {
     *   "code": 1,
     *   "msg": "获取成功",
     *   "data": {
     *     "list": [
     *       {
     *         "date": "2025-11-25",
     *         "qishu": "2025332",
     *         "date_display": "2025-11-25 (2025332)",
     *         "numbers": [
     *           {"num": "01", "zodiac": "蛇"},
     *           {"num": "12", "zodiac": "马"},
     *           ...
     *           {"num": "08", "zodiac": "猴", "is_special": true}
     *         ],
     *         "total_score": 145,
     *         "special_num": "08",
     *         "special_zodiac": "猴",
     *         "special_odd_even": "双",
     *         "special_big_small": "小",
     *         "special_hesu": 8,
     *         "special_hesu_odd_even": "双",
     *         "total_odd_even": "单",
     *         "total_big_small": "大",
     *         "one_zodiac": "猴",
     *         "one_zodiac_count": 2,
     *         "tail_count": 5,
     *         "wuxing": "水"
     *       }
     *     ],
     *     "total": 100,
     *     "page": 1,
     *     "limit": 20
     *   }
     * }
     */
    public function getResultList()
    {
        // 获取请求参数
        $gid = $this->request->param('gid/d', 200);
        $page = $this->request->param('page/d', 1);
        $limit = $this->request->param('limit/d', 20);

        // 限制最大条数
        if ($limit > 100) {
            $limit = 100;
        }
        if ($page < 1) {
            $page = 1;
        }

        // 查询已开奖的记录 (js=1 表示已结算)
        $query = Db::table('x_kj')
            ->where('gid', $gid)
            ->where('js', 1)
            ->where('m1', '<>', '')  // m1 不为空表示已开奖
            ->order('dates', 'desc')
            ->order('qishu', 'desc');

        // 获取总数
        $total = $query->count();

        // 分页查询
        $kjList = $query
            ->page($page, $limit)
            ->select()
            ->toArray();

        // 处理每条开奖记录
        $list = [];
        foreach ($kjList as $kj) {
            $list[] = $this->formatKjResult($kj);
        }

        return $this->success('获取成功', [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ]);
    }


    /**
     * 格式化开奖结果
     *
     * @param array $kj 开奖记录
     * @return array 格式化后的结果
     */
    private function formatKjResult(array $kj): array
    {
        // 提取开奖号码 (m1-m7, m7是特码)
        $numbers = [];
        $allNums = [];

        for ($i = 1; $i <= 7; $i++) {
            $num = $kj['m' . $i];
            if (!empty($num)) {
                $allNums[] = $num;
            }
        }

        // 如果没有开奖号码，返回空结果
        if (empty($allNums)) {
            return [
                'date' => $kj['dates'],
                'qishu' => $kj['qishu'],
                'date_display' => $kj['dates'] . ' (' . $kj['qishu'] . ')',
                'numbers' => [],
                'has_result' => false,
            ];
        }

        // 获取年份用于生肖计算
        $year = (int)substr($kj['dates'], 0, 4);

        // 格式化号码和生肖
        foreach ($allNums as $index => $num) {
            $numInt = (int)$num;
            $zodiac = ZodiacYearService::getZodiacByNumberAndYear($numInt, $year);

            $numberItem = [
                'num' => str_pad($num, 2, '0', STR_PAD_LEFT),
                'zodiac' => $zodiac ?? '',
            ];

            // 标记特码（第7个号码）
            if ($index === 6) {
                $numberItem['is_special'] = true;
            }

            $numbers[] = $numberItem;
        }

        // 特码信息 (第7个号码)
        $specialNum = $allNums[6] ?? '';
        $specialNumInt = (int)$specialNum;
        $specialZodiac = ZodiacYearService::getZodiacByNumberAndYear($specialNumInt, $year) ?? '';

        // 计算总分 (所有7个号码之和)
        $totalScore = array_sum(array_map('intval', $allNums));

        // 特码单双
        $specialOddEven = ($specialNumInt % 2 === 0) ? '双' : '单';

        // 特码大小 (1-24小, 25-49大, 49为和)
        if ($specialNumInt === 49) {
            $specialBigSmall = '和';
        } elseif ($specialNumInt <= 24) {
            $specialBigSmall = '小';
        } else {
            $specialBigSmall = '大';
        }

        // 特码合数 (个位+十位)
        $specialHesu = $this->calculateHesu($specialNumInt);

        // 特码合数单双
        $specialHesuOddEven = ($specialHesu % 2 === 0) ? '双' : '单';

        // 总数单双
        $totalOddEven = ($totalScore % 2 === 0) ? '双' : '单';

        // 总数大小 (总分 >= 175 为大, < 175 为小)
        $totalBigSmall = ($totalScore >= 175) ? '大' : '小';

        // 一肖量 (统计7个号码中出现最多的生肖)
        $zodiacCount = $this->countZodiacs($allNums, $year);
        $oneZodiac = $zodiacCount['most_zodiac'];
        $oneZodiacCount = $zodiacCount['max_count'];

        // 尾数量 (统计不同尾数的数量)
        $tailCount = $this->countTails($allNums);

        // 五行 (特码的五行)
        $wuxing = self::WUXING_MAP[str_pad($specialNum, 2, '0', STR_PAD_LEFT)] ?? '';

        return [
            'date' => $kj['dates'],
            'qishu' => (string)$kj['qishu'],
            'date_display' => $kj['dates'] . ' (' . $kj['qishu'] . ')',
            'numbers' => $numbers,
            'has_result' => true,
            'total_score' => $totalScore,
            'special_num' => str_pad($specialNum, 2, '0', STR_PAD_LEFT),
            'special_zodiac' => $specialZodiac,
            'special_odd_even' => $specialOddEven,
            'special_big_small' => $specialBigSmall,
            'special_hesu' => $specialHesu,
            'special_hesu_odd_even' => $specialHesuOddEven,
            'total_odd_even' => $totalOddEven,
            'total_big_small' => $totalBigSmall,
            'one_zodiac' => $oneZodiac,
            'one_zodiac_count' => $oneZodiacCount,
            'tail_count' => $tailCount,
            'wuxing' => $wuxing,
        ];
    }


    /**
     * 计算合数 (个位+十位，如果结果>=10则继续相加直到<10)
     *
     * @param int $num 号码
     * @return int 合数
     */
    private function calculateHesu(int $num): int
    {
        // 先计算十位和个位之和
        $hesu = intval($num / 10) + ($num % 10);

        // 如果合数 >= 10，继续相加
        while ($hesu >= 10) {
            $hesu = intval($hesu / 10) + ($hesu % 10);
        }

        return $hesu;
    }


    /**
     * 统计生肖出现次数
     *
     * @param array $nums 号码数组
     * @param int $year 年份
     * @return array ['most_zodiac' => '出现最多的生肖', 'max_count' => 最大次数]
     */
    private function countZodiacs(array $nums, int $year): array
    {
        $zodiacCounts = [];

        foreach ($nums as $num) {
            $numInt = (int)$num;
            $zodiac = ZodiacYearService::getZodiacByNumberAndYear($numInt, $year);
            if ($zodiac) {
                if (!isset($zodiacCounts[$zodiac])) {
                    $zodiacCounts[$zodiac] = 0;
                }
                $zodiacCounts[$zodiac]++;
            }
        }

        if (empty($zodiacCounts)) {
            return ['most_zodiac' => '', 'max_count' => 0];
        }

        // 找出出现最多的生肖
        arsort($zodiacCounts);
        $mostZodiac = array_key_first($zodiacCounts);
        $maxCount = $zodiacCounts[$mostZodiac];

        return [
            'most_zodiac' => $mostZodiac,
            'max_count' => $maxCount,
        ];
    }


    /**
     * 统计不同尾数的数量
     *
     * @param array $nums 号码数组
     * @return int 不同尾数的数量
     */
    private function countTails(array $nums): int
    {
        $tails = [];

        foreach ($nums as $num) {
            $numInt = (int)$num;
            $tail = $numInt % 10;  // 取个位数
            $tails[$tail] = true;
        }

        return count($tails);
    }
}
