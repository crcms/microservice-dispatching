<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2019-03-19 23:16
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2019 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Dispatching\Tests;

use CrCms\Microservice\Dispatching\AbstractTransformer;
use Illuminate\Support\Collection;

class TestTransformer extends AbstractTransformer
{

    public function toArray($item): array
    {
        if ($item instanceof Collection) {
            $item = $item->toArray();
        }
        return array_merge($item,[
            'z' => 1,
            'x' => 2,
        ]);
    }

}
