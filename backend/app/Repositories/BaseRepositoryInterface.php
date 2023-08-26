<?php

namespace App\Repositories;

use App\Http\Parameters\Criteria;
use App\Models\BaseModel;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;

/**
 * Interface BaseRepositoryInterface
 * @package App\Repositories
 */
interface BaseRepositoryInterface
{
    /**
     * Get all model items without pagination
     *
     * @param Criteria|null $criteria
     *
     * @return Collection
     */
    public function all(?Criteria $criteria = null): Collection;

    /**
     * Find model by id
     *
     * @param int|string $id
     *
     * @return Model|Collection
     */
    public function find($id): object;

    /**
     * Get list model items with pagination
     *
     * @param Criteria $criteria
     *
     * @return LengthAwarePaginator
     */
    public function list(Criteria $criteria): LengthAwarePaginator;

    /**
     * Get lazy collection model items
     *
     * @param array $ids
     * @param array $relations
     * @param array $counts
     *
     * @return Enumerable
     */
    public function getEnumerable(array $ids, $relations = [], $counts = []): Enumerable;

    /**
     * Create new model item
     *
     * @param array $data
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
     * @param array $data
     *
     * @return Model
     *
     * @throws ModelNotFoundException|Exception
     */
    public function update($id, array $data): Model;

    /**
     * Update model item data by id
     *
     * @param array $attributes
     * @param array $data
     *
     * @return Model
     *
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function upCreate(array $attributes, array $data): Model;

    /**
     * Update model item data by id
     *
     * @param array $attributes
     * @param array $data
     *
     */
    public function updateConditions(array $attributes, array $data): int;

    /**
     * Delete model item by id
     *
     * @param int|string $id
     *
     * @throws ModelNotFoundException|Exception
     */
    public function delete($id): void;

    /**
     * Create new query of model
     *
     * @return Builder|Model
     */
    public function newQuery(): Builder;

    /**
     * Load scopes of model
     *
     * @param $scopes
     *
     * @return array
     */
    public function loadScopes($scopes): array;

    /**
     * Apply sort order
     *
     * @param Builder $query
     * @param $orderBys
     *
     * @return Builder
     */
    public function applyOrderBy(Builder $query, $orderBys): Builder;

    /**
     * Get query from criteria
     * @param Criteria $criteria
     * @return Builder
     */
    public function createQuery(Criteria $criteria): Builder;

    /**
     * Execute the query and get the first result or throw an exception.
     * @param int|string $id
     * @param Criteria $criteria
     * @return Builder|Model|BaseModel
     */
    public function findOrFail($id, Criteria $criteria): Model;

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable(): array;

    /**
     * Find model by hash id
     *
     * @param string $hashId
     *
     * @return Model|Collection
     */
    public function findByHashId(string $hashId): object;

    /**
     * Get all attribute of model
     *
     * @param array $attributes
     * @return array
     */
    public function getAllAttributes(array $attributes): array;

    /**
     * Get all model items without pagination for dropdown
     *
     * @param  Criteria|null  $criteria
     *
     * @return Collection
     */
    public function getDataOptions(?Criteria $criteria = null): Collection;

    /**
     * Update only updated_at field
     * @return Model|Collection
     */
    public function updateOnlyUpdatedAtField($id): Model;
}
