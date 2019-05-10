<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
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
}
