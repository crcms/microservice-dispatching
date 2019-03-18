<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2019-03-18 23:36
 *
 * @link http://crcms.cn/
 *
 * @copyright Copyright &copy; 2019 Rights Reserved CRCMS
 */

namespace CrCms\Microservice\Dispatching;

use CrCms\Foundation\Resources\Concerns\FieldConcern;
use League\Fractal\TransformerAbstract;

abstract class AbstractTransform extends TransformerAbstract
{
    use FieldConcern;

//    public function transform($item)
//    {
//        if (method_exists($this,'itemTransform')) {
//            $result = $this->toArray($item);
//        }
//    }
}