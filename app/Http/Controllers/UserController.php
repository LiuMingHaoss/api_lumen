<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
class UserController extends BaseController
{
    /**
     * @param Request $request
     * @return false|string
     * 用户登录
     */
    public function login(Request $request)
    {
        //接收登录数据
        $data=file_get_contents('php://input');;
        $data=json_decode($data,JSON_UNESCAPED_UNICODE);

        $userInfo=DB::table('api_user')->where('email',$data['email'])->first();    //根据邮箱查询用户数据

        if($userInfo){      //用户存在
            if($data['pwd']==$userInfo->pwd){   //密码正确
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

    /**
     * 个人中心
     */
    public function my(Request $request)
    {
        $uid=$request->input('uid');
        $userInfo=DB::table('api_user')->where('id',$uid)->first();
        echo "<pre>";print_r($userInfo);echo"</pre>";
    }
}
