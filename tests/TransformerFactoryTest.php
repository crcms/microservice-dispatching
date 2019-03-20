<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2019-03-19 23:15
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2019 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Dispatching\Tests;

use CrCms\Microservice\Dispatching\TransformerFactory;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\TestCase;

class TransformerFactoryTest extends TestCase
{
    public function testItem()
    {
        $factory = new TransformerFactory();
        $result = $factory->item(['foo'=>'foo','bar'=>'bar'],TestTransformer::class);
        $this->assertEquals(true,is_array($result));
        $this->assertArrayHasKey('data',$result);
        $this->assertEquals(1,count($result));
    }

    public function testCollection()
    {
        $factory = new TransformerFactory();
        $result = $factory->collection([['foo'=>'foo','bar'=>'bar'],['foo'=>'foo','bar'=>'bar']],TestTransformer::class);
        $this->assertEquals(true,is_array($result));
        $this->assertArrayHasKey('data',$result);
        $this->assertEquals(1,count($result));
    }

    public function testPaginate()
    {
        $factory = new TransformerFactory();

        $paginate = new LengthAwarePaginator(
            [['foo'=>'foo','bar'=>'bar'],['foo'=>'foo','bar'=>'bar']],
            1,
            1
        );

        $result = $factory->paginate($paginate,TestTransformer::class);
        $this->assertEquals(true,is_array($result));
        $this->assertArrayHasKey('data',$result);
        $this->assertArrayHasKey('meta',$result);
        $this->assertEquals(2,count($result));
    }
}
