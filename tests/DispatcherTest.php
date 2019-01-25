<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2019-01-25 22:11
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2019 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Dispatching\Tests;

use CrCms\Microservice\Dispatching\Dispatcher;
use CrCms\Microservice\Dispatching\Tests\Controllers\TestingController;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase
{

    public function testRegister()
    {
        $app = new Container();
        $dispatch = new \CrCms\Microservice\Dispatching\Dispatcher($app);

        $dispatch->register('abc',TestingController::class.'@foo');


        $dispatch->group(['middleware' => 'test'],function(Dispatcher $dis){
            $dis->register('abcd',TestingController::class.'@bar');
            $dis->middleware('zfc')->register('zar',TestingController::class.'@zar');
        });

        $dispatch->middleware(['ggg','g2'])->register('ggg',TestingController::class.'@ggg');

        dd($dispatch->getCallers());
    }

}