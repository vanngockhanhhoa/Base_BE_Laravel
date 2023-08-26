<?php

namespace App\Repositories\Impl;

use App\Models\PasswordReset;
use App\Repositories\PasswordResetRepositoryInterface;

/**
 * Class PasswordResetRepository
 *
 * @package App\Repositories\Impl
 *
 * @property PasswordReset $model
 */
class PasswordResetRepository extends BaseRepository implements PasswordResetRepositoryInterface
{
    /**
     * CustomerRepository constructor.
     *
     * @param PasswordReset $model
     */
    public function __construct(PasswordReset $model)
    {
        parent::__construct($model);
    }

    public function deleteByEmail(string $email)
    {
        $this->model->where('email', $email)->delete();
    }
}
