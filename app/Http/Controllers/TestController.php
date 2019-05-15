<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\UsersModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class TestController extends Controller
{
    //对称加密
    public function decrypt(){
        //echo __METHOD__;
        $data=file_get_contents('php://input');
        $method='AES-256-CBC';
        $key='bruno';
        $option=OPENSSL_RAW_DATA;
        $iv='0123456789abcdef';
        //解密
        $base64=base64_decode($data);
        $ope=openssl_decrypt($base64,$method,$key,$option,$iv);
        echo $ope;
    }
    //非对称加密
    public function rsaDecrypt(){
        //echo __METHOD__;
        $data=file_get_contents('php://input');
        $base64=base64_decode($data);
        $key=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($base64,$enc,$key);
        echo $enc;
    }
    //非对称加密签名
    public function firma(){
        //echo __METHOD__;
        $data=file_get_contents('php://input');
        echo $data;
        //接收验证签名
        $firma=$_GET['firma'];
        $b64=base64_decode($firma);
        //echo $b64;
        $key=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        $ope=openssl_verify($data,$b64,$key);
        if($ope!=1){
            die('验签错误');

        }else{
            echo 'ok';
        }
    }
    //注册
    public function register(){
        //echo __METHOD__;
        $data=file_get_contents('php://input');
        $base64=base64_decode($data);
        $key=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($base64,$enc,$key);
        $json=json_decode($enc,true);
        $add=DB::table('p_user')->insert($json);
        if($add){
            die('注册成功');
        }else{
            die("注册失败");
        }
    }
    //redis测试
    public function aa(){
        $k=1;
        $v=1;
        Redis::set($k,$v);
        echo Redis::get($k);
    }
    //登录信息
    public function login(){
        //echo __METHOD__;
        $data=file_get_contents('php://input');
        $base64=base64_decode($data);
        $key=openssl_get_publickey('file://'.storage_path('app/keys/public.pem'));
        openssl_public_decrypt($base64,$enc,$key);
        $json=json_decode($enc,true);
        $info=DB::table('p_user')->where(['name'=>$json['name']])->first();
        //$info=json_decode(json_encode($info),true);
//        print_r($json['pwd']);echo "<br>";
//        print_r($info->pwd);die;
        if($info){
            if(password_verify($json['pwd'],$info->pwd)){
                $token=substr(sha1($info->uid.time().str::random(10)),5,15);
                $key='uid_token'.$_SERVER['REMOTE_ADDR'].$info->uid;
                $re=Redis::get($key);
                if($re){

                }else{
                    Redis::set($key,$token);
                    Redis::expire($key,604800);
                }
            }else{
                die('登录失败');
            }
        }else{
            die('信息不正确');
        }
    }
    //跨域
    public function a(){
        header('Access-Control-Allow-Origin:http://client.1809a.com');
        $name=$_POST['name'];
        $pwd=$_POST['pwd'];
        if($name&&$pwd){
            echo 1;
        }else{
            echo 2;
        }
    }
    //app注册
    public function reg()
    {
        $name=$_POST['name'];
        $pwd=$_POST['pwd'];
        $pwd=password_hash($pwd,PASSWORD_DEFAULT);
        $e=DB::table('p_user')->where(['name'=>$name])->first();
        if($e){
            $response=[
                'errorno'=>50002,
                'msg'=>'名字存在'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        $data=[
            'name'=>$name,
            'pwd'=>$pwd,
        ];
        $add=DB::table('p_user')->insert($data);
        if($add){
            $response=[
                'errorno'=>0,
                'msg'=>'注册成功'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }else{
            $response=[
                'errorno'=>1,
                'msg'=>'注册失败'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
    }
    //app登录
    public function log()
    {
        $name=$_POST['name'];
        $pwd=$_POST['pwd'];
        $e=DB::table('p_user')->where(['name'=>$name])->first();
        if($e){
            if(password_verify($pwd,$e->pwd)){
                $token=substr(sha1($e->uid.time().str::random(10)),5,15);
                $key='token_uid'.'-'.$_SERVER['REMOTE_ADDR'].'-'.$e->uid;
                $re=Redis::get($key);
                if($re){
                    $response=[
                        'errorno'=>0,
                        'msg'=>'登录成功',
                        'token'=>$token,
                        'uid'=>$e->uid
                    ];
                    die(json_encode($response,JSON_UNESCAPED_UNICODE));
                }else{
                    Redis::set($key,$token);
                    Redis::expire($key,604800);
                    $response=[
                        'errorno'=>0,
                        'msg'=>'登录成功',
                        'token'=>$token,
                        'uid'=>$e->uid
                    ];
                    die(json_encode($response,JSON_UNESCAPED_UNICODE));
                }
            }else{
                $response=[
                    'errorno'=>"50003",
                    'msg'=>'密码不正确'
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }
        }else{
            $response=[
                'errorno'=>"50004",
                'msg'=>'用户名或密码不正确'
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
    }
    //个人中心
//    public function centro(){
//        $uid=$_GET['uid'];
//        $token=$_GET['token'];
//        $e=DB::table('p_user')->where(['uid'=>$uid])->first();
//        if(empty($uid)||empty($token)){
//            $response=[
//                'errorno'=>50001,
//                'msg'=>'参数不对',
//            ];
//            die(json_encode($response,JSON_UNESCAPED_UNICODE));
//        }
//        $key='token_uid'.'-'.$_SERVER['REMOTE_ADDR'].'-'.$uid;
//        $local_token=Redis::get($key);
//        if($token){
//            if($token==$local_token){
//                $response=[
//                    'errorno'=>0,
//                    'msg'=>'欢迎登录',
//                ];
//                die(json_encode($response,JSON_UNESCAPED_UNICODE));
//            }else{
//                $response=[
//                    'errorno'=>50002,
//                    'msg'=>'请登录',
//                ];
//                die(json_encode($response,JSON_UNESCAPED_UNICODE));
//            }
//        }
//    }
    public function centro()
    {
        $uid=$_GET['uid'];
        $e=DB::table('p_user')->where(['uid'=>$uid])->first();
        if($e){
            $response=[
                'errorno'=>0,
                'msg'=>'欢迎登录',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }else{
            $response=[
                'errorno'=>50001,
                'msg'=>'登录失败',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
    }
}
