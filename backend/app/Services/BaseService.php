<?php

namespace App\Services;

use App\Http\Parameters\Criteria;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

/**
 * Class BaseCrudService
 * @package App\Services
 *
 * @property Model|Builder $model
 */
interface BaseService
{
    /**
     * Get all model items without pagination
     *
     * @param  Criteria|null  $criteria
     *
     * @return Collection
     */
    public function all(?Criteria $criteria = null): Collection;

    /**
     * Find model(s) by id
     *
     * @param  int|string  $id
     *
     * @return object
     */
    public function find($id): object;

    /**
     * Get list model items by pagination
     *
     * @param  Criteria  $criteria
     *
     * @return LengthAwarePaginator
     */
    public function list(Criteria $criteria): LengthAwarePaginator;

    /**
     * Get lazy collection model items
     *
     * @param  array  $ids
     * @param  array  $relations
     * @param  array  $counts
     *
     * @return Enumerable
     */
    public function getEnumerable(array $ids, $relations = [], $counts = []): Enumerable;

    /**
     * Create new model item
     *
     * @param  array  $data
     *
     * @return Model
     *
     * @throws Exception
     */
    public function create(array $data): Model;

    /**
     * Update model item data by id
     *
     * @param int|string $id
     * @param  array  $data
     *
     * @return Model
     *
     * @throws ModelNotFoundException|Exception
     */
    public function update($id, array $data): Model;

    /**
     * Delete model item by id
     *
     * @param int|string $id
     *
     * @throws ModelNotFoundException|Exception
     */
    public function delete($id): void;

    /**
     * Find model(s) by hash id
     *
     * @param  string  $hashId
     *
     * @return object
     */
    public function findByHashId($hashId): object;

    /**
     * upload file
     * 
     * @param file $file
     * 
     * @return string
     */
    public function uploadFile($file): string;
    

    /**
     * Get all model items without pagination for dropdown
     *
     * @param  Criteria|null  $criteria
     *
     * @return Collection
     */
    public function getDataOptions(?Criteria $criteria = null): Collection;
}
