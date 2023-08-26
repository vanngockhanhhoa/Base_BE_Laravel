<?php

namespace App\Repositories\Impl;

use App\Http\Parameters\Criteria;
use App\Models\BaseModel;
use App\Repositories\BaseRepositoryInterface;
use App\Utils\MessageCommon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Str;
use Helper\Common;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class BaseRepository implements BaseRepositoryInterface
{
    /** @var Model|BaseModel $model */
    public Model $model;

    /** @var array $relations */
    public static array $relations = [];

    /** @var array $counts */
    public array $counts = [];

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all model items without pagination
     *
     * @param Criteria|null $criteria
     *
     * @return Collection
     */
    public function all(?Criteria $criteria = null): Collection
    {
        if (!$criteria) {
            $criteria = new Criteria();
        }
        $query = $this->newQuery()->scopes($this->loadScopes($criteria->getFilters()));
        if (!empty($criteria->getSelect())) {
            $query->select($criteria->getSelect());
        }

        return $this->applyOrderBy($query, $criteria->getSorts())
            ->with($this->getRelations($criteria))
            ->withCount($this->getCountRelations($criteria))
            ->get();
    }

    /**
     * Find model by id
     *
     * @param int|string $id
     *
     * @return Model|Collection
     */
    public function find($id): object
    {
        $query = $this->newQuery();
        return $query->findOrFail($id);
    }

    /**
     * Get list model items with pagination
     *
     * @param Criteria $criteria
     *
     * @return LengthAwarePaginator
     */
    public function list(Criteria $criteria): LengthAwarePaginator
    {
        $query = $this->newQuery()->scopes($this->loadScopes($criteria->getFilters()));
        if (!empty($criteria->getSelect())) {
            $query->select($criteria->getSelect());
        }

        return $this->applyOrderBy($query, $criteria->getSorts())
            ->with($this->getRelations($criteria))
            ->withCount($this->getCountRelations($criteria))
            ->paginate($criteria->getLimit(), ['*'], config('pagination.page_name'), $criteria->getPage());
    }

    /**
     * Get lazy collection model items
     *
     * @param array $ids
     * @param array $relations
     * @param array $counts
     *
     * @return Enumerable
     */
    public function getEnumerable(array $ids, $relations = [], $counts = []): Enumerable
    {
        $hashIDs = [];
        $normalIDs = [];
        foreach ($ids as $id) {
            if (Str::isUuid($id) && $this->hasHashId()) {
                $hashIDs[] = $id;
                continue;
            }
            $normalIDs[] = $id;
        }
        $query = $this->newQuery();
        if (empty($ids)) {
            return $query->whereIn('id', $ids)->cursor();
        }

        $query = $query->where(function ($subQuery) use ($hashIDs, $normalIDs) {
            if (!empty($hashIDs)) {
                $subQuery->orWhereIn('hash_id', $hashIDs);
            }
            if (!empty($normalIDs)) {
                $subQuery->orWhereIn('id', $normalIDs);
            }
            return $subQuery;
        });

        if ($relations) {
            $query->with($relations);
        }
        if ($counts) {
            $query->withCount($counts);
        }

        return $query->cursor();
    }

    /**
     * Create new model item
     *
     * @param array $data
     *
     * @return Model
     *
     * @throws Exception
     */
    public function create(array $data): Model
    {
        return tap(
            $this->newQuery()->create($data),
            function ($instance) {
                if (!$instance) {
                    throw new Exception(MessageCommon::MS02_004);
                }
            }
        )->fresh(static::$relations);
    }

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
    public function update($id, array $data): Model
    {
        $model = tap(
            $this->find($id),
            function ($instance) use ($data) {
                if (!$instance->update($data)) {
                    throw new Exception(MessageCommon::MS02_005);
                }
            }
        );

        return $model->load(static::$relations);
    }

    /**
     * Update or create model item data by id
     *
     * @param array $attributes
     * @param array $data
     * @return Builder|Model
     */
    public function upCreate(array $attributes, array $data): Model
    {
        return $this->newQuery()->updateOrCreate($attributes, $data);
    }

    /**
     * Update model item data by conditions
     *
     * @param array $attributes
     * @param array $data
     *
     * @return int
     */
    public function updateConditions(array $attributes, array $data): int
    {
        return $this->model->where($attributes)->update($data);
    }

    /**
     * Update many by field and conditions
     *
     * @param array $attributes
     * @param array $data
     *
     * @return int
     */
    public function updateManyWithConditions(string $field = 'id', array $conditions, array $data): int
    {
        return $this->model->whereIn($field , $conditions)->update($data);
    }

    /**

	 * Update model item data by arrays ids
     *
     * @param array $attributes
     * @param array $data
     *
     * @return int
     */
    public function updateManyByIds(array $ids, array $data): int
    {
        return $this->model->whereIn('id', $ids)->update($data);
    }

    /**
     * Delete model item by id
     *
     * @param int|string $id
     *
     * @throws ModelNotFoundException|Exception
     */
    public function delete($id): void
    {
        $model = $this->find($id);
        try {
            $model->delete();
        } catch (Throwable $e) {
            throw new Exception(MessageCommon::MS02_006);
        }
    }

    /**
     * Create new query of model
     *
     * @return Builder|Model
     */
    public function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Load scopes of model
     *
     * @param $scopes
     *
     * @return array
     */
    public function loadScopes($scopes): array
    {
        $namedScoped = [];
        foreach ($scopes as $name => $args) {
            $scopeName = Str::camel($name);
            if (!$this->model->hasNamedScope($scopeName) || is_null($args)) {
                continue;
            }
            $namedScoped[$scopeName] = $args;
        }
        return $namedScoped;
    }

    /**
     * Apply sort order
     *
     * @param Builder $query
     * @param $orderBys
     *
     * @return Builder
     */
    public function applyOrderBy(Builder $query, $orderBys): Builder
    {
        $orderByScopes = [];
        $isOrderedBy = false;
        foreach ($orderBys as $column => $direction) {
            if (!in_array($direction, ['asc', 'desc'], true)) {
                continue;
            }
            $scopeName = 'OrderBy' . Str::camel($column);
            if ($this->model->hasNamedScope($scopeName)) {
                $orderByScopes[$scopeName] = $direction;
            } else {
                $query->orderBy($column, $direction);
            }
            $isOrderedBy = true;
        }

        if ($isOrderedBy) {
            $query->withoutGlobalScope(BaseModel::DEFAULT_ORDER_SCOPE);
        }

        return $query->scopes($orderByScopes);
    }

    /**
     * Get array relationships
     *
     * @param Criteria $criteria
     *
     * @return array
     */
    public function getRelations(Criteria $criteria): array
    {
        return array_merge_recursive(static::$relations, $criteria->getRelations());
    }

    /**
     * Get array count relationship
     *
     * @param Criteria $criteria
     *
     * @return array
     */
    public function getCountRelations(Criteria $criteria): array
    {
        return array_merge_recursive($this->counts, $criteria->getCountRelations());
    }

    /**
     * Get query from criteria
     * @param Criteria $criteria
     * @return Builder
     */
    public function createQuery(Criteria $criteria): Builder
    {
        $query = $this->newQuery()->scopes($this->loadScopes($criteria->getFilters()));
        if (!empty($criteria->getSelect())) {
            $query->select($criteria->getSelect());
        }

        return $this->applyOrderBy($query, $criteria->getSorts())
            ->with($this->getRelations($criteria))
            ->withCount($this->getCountRelations($criteria));
    }

    /**
     * Check repository has use HashId
     * @return bool
     */
    public function hasHashId(): bool
    {
        return $this->model->hasField('hash_id');
    }

    /**
     * Execute the query and get the first result or throw an exception.
     * @param $id
     * @param Criteria $criteria
     * @return Builder|Model|BaseModel
     */
    public function findOrFail($id, Criteria $criteria): Model
    {
        $query = $this->createQuery($criteria);
        return $query->findOrFail($id);
    }

    /**
     * Get the fillable attributes for the model.
     *
     * @return array
     */
    public function getFillable(): array
    {
        return $this->model->getFillable();
    }

    /**
     * Find model hash id
     *
     * @param string $hashId
     *
     * @return Model|Collection
     */
    public function findByHashId(string $hashId): object
    {
        return $this->newQuery()->where('hash_id', $hashId)->firstOrFail();
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function getAllAttributes(array $attributes): array
    {
        $columns = [];
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->getFillable())) {
                $columns[$key] = $value;
            }
        }
        return $columns;
    }


    /**
     * @inheritDoc
     */
    public function getDataOptions(?Criteria $criteria = null): Collection
    {
        $criteria->setSelect(['id', 'name']);
        // $criteria->setSorts(['id' => 'desc']);
        if (!$criteria) {
            $criteria = new Criteria();
        }
        $query = $this->newQuery()->scopes($this->loadScopes($criteria->getFiltersDropdown()));
        if (!empty($criteria->getSelect())) {
            $query->select($criteria->getSelect());
        }

        return $this->applyOrderBy($query, $criteria->getSorts())
            ->with($this->getRelations($criteria))
            ->withCount($this->getCountRelations($criteria))
            ->get();
    }

    /**
     * Retrieve model with specific relation data
     *
     * @param int|string $id
     * @param array $relation
     *
     * @return Model
     */
    public function findWithRelations($id, $relations): Model
    {
        return $this->model->with($relations)->findOrFail($id);
    }

    public function findBy($conditionArray): Model
    {
        $query = $this->model;
        foreach($conditionArray as $field => $value) {
            $query = $query->where($field, '=', $value);
        }
        return $query->firstOrFail();
    }

    /**
     * Update only updated_at field
     * @return Model|Collection
     */
    public function updateOnlyUpdatedAtField($id): Model
    {
        $model = tap(
            $this->find($id),
            function ($instance) {
                if (!$instance->touch()) {
                    throw new Exception(MessageCommon::MS02_005);
                }
            }
        );

        return $model->load(static::$relations);
    }
}
