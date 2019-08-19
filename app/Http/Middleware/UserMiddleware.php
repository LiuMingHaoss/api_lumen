<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $data=$request->input();
        $key='token:uid:'.$data['uid'];
        $token=Redis::get($key);
        if(!$token){
            $response=[
                'errno'=>60002,
                'msg'=>'参数已过期，请重新登录',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        if(empty($data['token'])||empty($data['uid'])){
            $response=[
                'errno'=>60003,
                'msg'=>'参数不全，请重新登录',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }else if($token!=$data['token']){
            $response=[
                'errno'=>60001,
                'msg'=>'参数不对，请重新登录',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }

        return $next($request);
    }
}
