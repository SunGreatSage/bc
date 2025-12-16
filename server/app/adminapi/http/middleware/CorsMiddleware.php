<?php
// +----------------------------------------------------------------------
// | BC 彩票系统 - CORS 跨域中间件
// +----------------------------------------------------------------------
// | Author: Claude AI
// | Date: 2025-12-11
// +----------------------------------------------------------------------

namespace app\adminapi\http\middleware;

use Closure;
use think\Request;
use think\Response;

/**
 * CORS 跨域请求支持（支持 token 请求头）
 * Class CorsMiddleware
 * @package app\adminapi\http\middleware
 */
class CorsMiddleware
{
    /**
     * @notes 处理 CORS 跨域请求
     * @param Request $request
     * @param Closure $next
     * @return Response
     * @author Claude
     * @date 2025/12/11
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 设置允许的跨域头
        $header = [
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => 1800,
            'Access-Control-Allow-Methods'     => 'GET, POST, PATCH, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-CSRF-TOKEN, X-Requested-With, token, Token',
        ];

        // 获取请求来源，允许所有来源
        $origin = $request->header('origin');
        if ($origin) {
            $header['Access-Control-Allow-Origin'] = $origin;
        } else {
            $header['Access-Control-Allow-Origin'] = '*';
        }

        // 如果是 OPTIONS 请求（预检请求），直接返回 200（不是 204，避免被框架拦截）
        if ($request->method(true) === 'OPTIONS') {
            return Response::create('ok', 'html', 200)->header($header);
        }

        // 继续处理请求，并添加跨域头
        return $next($request)->header($header);
    }
}
