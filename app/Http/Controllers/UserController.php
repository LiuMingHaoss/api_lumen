<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
class UserController extends BaseController
{
    public function login(Request $request)
    {
        //接收登录数据
        $username=$request->input('username');      //用户名
        $pwd=$request->input('pwd');                //用户密码
        $email=$request->input('email');            //用户邮箱

        $userInfo=DB::table('api_user')->where('email',$email)->first();    //根据邮箱查询用户数据

        if($userInfo){      //用户存在
            if($pwd==$userInfo->pwd){   //密码正确
                $key="token:uid:".$userInfo->id;
                $token=Redis::get($key);
                if(!$token){                //token过期
                    $token=Str::random(16);
                    Redis::set($key,$token);
                    Redis::expire($key,3600);
                }

                $response=[
                    'token'=>$token,
                    'msg'=>'ok'
                ];
            }else{                      //密码不正确
                $response=[
                    'errno'=>20001,
                    'msg'=>'用户名或密码不正确'
                ];
            }
        }else{              //用户不存在
            $response=[
                'errno'=>20003,
                'msg'=>'用户名或密码不正确'
            ];
        }

        return json_encode($response,JSON_UNESCAPED_UNICODE);
    }
}
