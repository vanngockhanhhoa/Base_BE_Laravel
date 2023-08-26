<?php

namespace App\Repositories\Impl;

use App\Models\LoginLogs;
use App\Repositories\LoginLogsRepositoryInterface;
use Illuminate\Support\Str;

class LoginLogsRepository extends BaseRepository implements LoginLogsRepositoryInterface
{

    /**
     * LoginLogsRepository constructor.
     * @param LoginLogs $model
     */
    public function __construct(LoginLogs $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}
