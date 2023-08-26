<?php

namespace App\Services\Impl;

use App\Helpers\LogHelperService;
use App\Http\Parameters\Criteria;
use App\Repositories\BaseRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Storage;

/**
 * Class BaseService
 * @package App\Services
 *
 * @property Model|Builder $model
 */
abstract class BaseServiceImpl implements BaseService
{
    /** @var BaseRepositoryInterface $repository */
    public BaseRepositoryInterface $repository;

    /**
     * @var LogHelperService
     */
    protected LogHelperService $logger;

    /**
     * @var string
     */
    protected string $uploadPath;

    /**
     * Contractor of BaseServiceImpl
     *
     * @param BaseRepositoryInterface $repository
     */
    public function __construct(
        BaseRepositoryInterface $repository
    )
    {
        $this->repository = $repository;
        $this->logger = app(LogHelperService::class);
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
        return $this->repository->all($criteria);
    }

    /**
     * Find model(s) by id
     *
     * @param int|string $id
     *
     * @return object
     * @throws Exception
     */
    public function find($id): object
    {
        return $this->repository->find($id);
    }

    /**
     * Get list model items by pagination
     *
     * @param Criteria $criteria
     *
     * @return LengthAwarePaginator
     */
    public function list(Criteria $criteria): LengthAwarePaginator
    {
        return $this->repository->list($criteria);
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
        return $this->repository->getEnumerable($ids, $relations, $counts);
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
        return $this->repository->create($data);
    }

    /**
     * Update model item data by hash id
     *
     * @param string $id
     * @param array $data
     *
     * @return Model
     *
     * @throws ModelNotFoundException|Exception
     */
    public function update($id, array $data): Model
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete model item by hash id
     *
     * @param string $id
     *
     * @throws ModelNotFoundException|Exception
     */
    public function delete($id): void
    {
        $this->repository->delete($id);
    }

    /**
     * Get data of model by hash id
     *
     * @param string $hashId
     *
     * @return Model
     *
     * @throws ModelNotFoundException
     */
    public function findByHashId($hashId): Model
    {
        return $this->repository->findByHashId($hashId);
    }

    /**
     * upload file
     *
     * @param file $file
     *
     * @return string
     */
    public function uploadFile($file): string
    {
        if (!$file) return '';
        $fileName = time() . '.' . $file->extension();
        return $file->storeAs('uploads/' . $this->uploadPath, $fileName);
    }
    /**
     * upload base64 file
     *
     * @param file $file
     *
     * @return string
     */
    public function uploadBase64($base64): string
    {
        if (!$base64) return '';
        $img = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $type = explode(';', $base64)[0];
        $type = explode('/', $type)[1];
        $fileName = time() . '.' . $type;
        $storage = Storage::disk('public');
        if ($storage->put('uploads/' . $this->uploadPath . '/' . $fileName, base64_decode($img))) {
            return 'uploads/' . $this->uploadPath . '/' . $fileName;
        }
        return '';
    }
    /**
     * @inheritDoc
     */
    public function getDataOptions(?Criteria $criteria = null): Collection
    {
        return $this->repository->getDataOptions($criteria);
    }
}
