<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Parameters\Criteria;
use App\Services\TransmissionLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransmissionLogController extends BaseController
{
    /**
     * ProductReturnCustomerController constructor.
     *
     * @param TransmissionLogService $service
     * @param Request $request
     */
    public function __construct(TransmissionLogService $service, Request $request
    )
    {
        parent::__construct($service, $request);
    }

    /**
     * Get FormRequest validation
     *
     * @return string
     */
    public function getRules(): string
    {
        return '';
    }
}
