<?php

namespace App\Repositories\Impl;

use App\Models\Account;
use App\Repositories\AccountRepositoryInterface;

/**
 * Class AdminRepository
 *
 * @package App\Repositories\Impl
 *
 * @property Account $model
 */
class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    /**
     * AdminRepository constructor.
     *
     * @param Account $model
     */
    public function __construct(Account $model)
    {
        parent::__construct($model);
    }

    /**
     * Get model by email
     *
     * @param string $email
     */
    public function getByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @inheritDoc
     */
    public function isUniqueEmail(array $data, string $mode): bool
    {
        if ($mode === 'edit') {
            return $this->model
                ->where('email', $data['email'])
                ->whereNull('deleted_at')
                ->whereNot('id', $data['id'])
                ->exists();
        }
        return $this->model
            ->where('email', $data['email'])
            ->whereNull('deleted_at')
            ->exists();
    }
}
