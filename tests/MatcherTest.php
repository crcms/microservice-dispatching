<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2019-03-03 11:04
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2019 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Dispatching\Tests;

use CrCms\Microservice\Dispatching\Dispatcher;
use CrCms\Microservice\Dispatching\Matcher;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    use ApplicationTrait;

    /**
     * @var Matcher
     */
    public $matcher;

    public function setUp()
    {
        // TODO: Change the autogenerated stub
        parent::setUp();

        $this->matcher = $matcher = new Matcher(new Dispatcher(static::$app),['uses' => 'test','middleware' => ['m1' => 'm1']]);
    }

    public function testGetCallerUses()
    {
        $this->assertEquals('test',$this->matcher->getCallerUses());
        $matcher = new Matcher(new Dispatcher(static::$app),['uses' => function(){},'middleware' => ['m1' => 'm1']]);
        $this->assertTrue($matcher->getCallerUses() instanceof \Closure);
    }

    public function testGetCallerMiddleware()
    {
        $this->assertTrue(is_array($this->matcher->getCallerMiddleware()));
    }
}