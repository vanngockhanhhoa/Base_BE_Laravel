<?php

namespace App\Services;

use App\Http\Parameters\Criteria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AccountSettingService extends BaseService
{
    /**
     * Create new model
     *
     * @param $data
     * @return mixed
     */
    public function create(array $data): Model;
}
