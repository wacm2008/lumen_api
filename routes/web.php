<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
//对称加密
$router->post('/test/opcode','TestController@decrypt');
//非对称加密
$router->post('/test/rsa','TestController@rsaDecrypt');
//非对称加密签名
$router->post('/test/firma','TestController@firma');
//注册练习
$router->post('/test/register','TestController@register');
//用户登录练习
$router->post('/test/login','TestController@login');
//跨域
$router->post('/test/a','TestController@a');
//redis测试
$router->get('/test/aa','TestController@aa');
//app注册
$router->post('/test/reg','TestController@reg');
//app登录
$router->post('/test/log','TestController@log');