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
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class TransformerFactoryTest extends TestCase
{
    use ApplicationTrait;

    public function testItem()
    {
        $factory = new TransformerFactory();
        $result = $factory->collection(Collection::make([[]]),TestTransformer::class);
        dd($result);
    }
}