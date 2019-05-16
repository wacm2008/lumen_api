<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class CheckTokenMiddleware
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
        $uid=$_GET['uid'];
        $token=$_GET['token'];
        //echo $token.'<br>';
        if(empty($uid)||empty($token)){
            $response=[
                'errorno'=>40001,
                'msg'=>'参数不全',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $key='token'.'-'.$uid;
        $local_token=Redis::get($key);
        //var_dump($local_token);die;
        if($local_token){
            if($token!=$local_token){
                $response=[
                    'errorno'=>40002,
                    'msg'=>'token无效 请登录',
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }
        }
        return $next($request);
    }
}
