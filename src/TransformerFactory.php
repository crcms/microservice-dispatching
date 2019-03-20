<?php

namespace CrCms\Microservice\Dispatching;

use CrCms\Foundation\Helpers\InstanceConcern;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Traversable;
use JsonSerializable;
use InvalidArgumentException;
use Illuminate\Support\Collection;
use League\Fractal\Resource;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Manager;

class TransformerFactory
{
    use InstanceConcern;

    /**
     * @var Manager
     */
    protected $fractal;

    /**
     */
    public function __construct()
    {
        $this->fractal = new Manager();
    }

    /**
     * Collection
     *
     * @param Collection|array $collection
     * @param $transformer
     * @param array $fields
     * @param array $includes
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function collection($collection, $transformer, array $fields = [], array $includes = []): array
    {
        $transformer = $this->parseTransformer($transformer, $fields);

        $this->parseIncludes($includes);

        return $this->fractal->createData(new FractalCollection($collection, $transformer))->toArray();
    }

    /**
     * Resource
     *
     * @param $resource
     * @param $transformer
     * @param array $fields
     * @param array $includes
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resource($resource, $transformer, array $fields = [], array $includes = []): array
    {
        $transformer = $this->parseTransformer($transformer, $fields);

        $this->parseIncludes($includes);

        return $this->fractal->createData(new Resource\Item($resource, $transformer))->toArray();
    }

    /**
     * Resource alias
     *
     * @param $resource
     * @param $transformer
     * @param array $fields
     * @param array $includes
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function item($resource, $transformer, array $fields = [], array $includes = []): array
    {
        return $this->resource($resource, $transformer, $fields, $includes);
    }

    /**
     * Paginator
     *
     * @param LengthAwarePaginator $paginator
     * @param string|AbstractTransformer $transformer
     * @param array $fields
     * @param array $includes
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function paginate(LengthAwarePaginator $paginator, $transformer, array $fields = [], array $includes = []): array
    {
        $transformer = $this->parseTransformer($transformer, $fields);

        $resource = (new FractalCollection($paginator->items(), $transformer));
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        $this->parseIncludes($includes);

        return $this->fractal->createData($resource)->toArray();
    }

    /**
     * Compatible with the paging program, the next version is deleted
     *
     * @param LengthAwarePaginator $paginator
     * @param $transformer
     * @param array $fields
     * @param array $includes
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function paginator(LengthAwarePaginator $paginator, $transformer, array $fields = [], array $includes = []): array
    {
        return $this->paginate($paginator, $transformer, $fields, $includes);
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function array(array $array): array
    {
        return $array;
    }

    /**
     * null
     *
     * @return array
     */
    public function null(): array
    {
        return [];
    }

    /**
     * Compatible with the paging program, the next version is deleted
     *
     * @return array
     */
    public function noContent(): array
    {
        return $this->null();
    }

    /**
     * @param array|Collection|\JsonSerializable|\Traversable $data
     * @param string $key
     *
     * @return array
     */
    public function data($data, string $key = 'data'): array
    {
        if (is_array($data)) {
        } elseif ($data instanceof Collection) {
            $data = $data->all();
        } elseif ($data instanceof JsonSerializable) {
            $data = $data->jsonSerialize();
        } elseif ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        } else {
            throw new InvalidArgumentException('Incorrect parameter format');
        }

        return $this->array([$key => $data]);
    }

    /**
     * parse Includes
     *
     * @param string|array $includes
     * @return void
     */
    protected function parseIncludes($includes = []): void
    {
        $this->fractal->parseIncludes($includes);
    }

    /**
     * Parse transformer
     *
     * @param string|AbstractTransformer $transformer
     * @param array $fields
     * @return AbstractTransformer
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function parseTransformer($transformer, array $fields = []): AbstractTransformer
    {
        $transformer = $transformer instanceof AbstractTransformer ? $transformer : $this->app->make($transformer);

        if (isset($fields['only'])) {
            $type = 'only';
            $fields = $fields['only'];
        } elseif (isset($fields['except']) || isset($fields['hide'])) {
            $type = 'except';
            $fields = $fields['except'] ?? $fields['hide'];
        } else {
            $type = 'except';
        }

        return $transformer->$type($fields);
    }
}
