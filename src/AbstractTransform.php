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

    /**
     * Base transform
     *
     * @param $item
     * @return array
     */
    public function transform($item): array
    {
        if (method_exists($this, 'toArray')) {
            return $this->filterFields($this->toArray($item));
        }

        return (array)$item;
    }
}