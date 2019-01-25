<?php
/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2019-01-25 21:55
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2019 Rights Reserved CRCMS
 */


$dispatch = new \CrCms\Microservice\Dispatching\Dispatcher();

$dispatch->register();


Route::namespace('CrCms\Passport\Controllers')->group(function () {
    Route::register('auth', 'AuthController', ['only' => ['login', 'check', 'refresh', 'user', 'register']]);
    Route::register('user', 'UserController', ['only' => ['index', 'store', 'update', 'destroy']]);
});
