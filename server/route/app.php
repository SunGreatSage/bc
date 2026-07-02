<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Console;
use think\facade\Route;

// 管理后台
Route::rule('admin/:any', function () {
    return view(app()->getRootPath() . 'public/admin/index.html');
})->pattern(['any' => '\w+']);

// 手机端
Route::rule('mobile/:any', function () {
    return view(app()->getRootPath() . 'public/mobile/index.html');
})->pattern(['any' => '\w+']);

// PC端
Route::rule('pc/:any', function () {
    return view(app()->getRootPath() . 'public/pc/index.html');
})->pattern(['any' => '\w+']);

//定时任务
Route::rule('crontab', function () {
    $startedAt = microtime(true);

    try {
        $output = Console::call('crontab');
        $content = trim($output->fetch());

        return json([
            'code' => 1,
            'show' => 0,
            'msg' => '定时任务执行完成',
            'data' => [
                'time' => date('Y-m-d H:i:s'),
                'duration' => round(microtime(true) - $startedAt, 3),
                'output' => $content,
                'lines' => $content === '' ? [] : preg_split('/\r\n|\r|\n/', $content),
            ],
        ]);
    } catch (\Throwable $e) {
        return json([
            'code' => 0,
            'show' => 1,
            'msg' => '定时任务执行失败：' . $e->getMessage(),
            'data' => [
                'time' => date('Y-m-d H:i:s'),
                'duration' => round(microtime(true) - $startedAt, 3),
            ],
        ], 500);
    }
});
