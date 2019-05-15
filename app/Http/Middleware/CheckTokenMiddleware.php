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
        if(empty($uid)||empty($token)){
            $response=[
                'errorno'=>50001,
                'msg'=>'参数不对',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $key='token_uid'.'-'.$_SERVER['REMOTE_ADDR'].'-'.$uid;
        $local_token=Redis::get($key);
        if($token){
            if($token==$local_token){

            }else{
                $response=[
                    'errorno'=>50002,
                    'msg'=>'请登录',
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }
        }
        return $next($request);
    }
}
