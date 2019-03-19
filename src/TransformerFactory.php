<?php

namespace CrCms\Microservice\Dispatching;

use CrCms\Foundation\Helpers\InstanceConcern;
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
     *
     *
     * @param $paginator
     * @param $transformer
     * @param array $fields
     * @param array $includes
     * @return array
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function paginator($paginator, $transformer, array $fields = [], array $includes = []): array
    {
//        $paginator = Book::paginate();
//        $books = $paginator->getCollection();
//
//        $resource = new Collection($books, new BookTransformer());
//        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

//        $transformer = $this->parseTransformer($transformer, $fields);
//
//        $this->parseIncludes($includes);
//
//        $paginator = new Collection($paginator, $transformer);
//        $paginator->setPaginator(new IlluminatePaginatorAdapter($paginator));
//
//        return $this->fractal->createData()->toArray();
//
//        return $this->collection($paginator, $transformer, $fields, $includes);
        return [];
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
