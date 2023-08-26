<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\AccountRequest;
use App\Services\AccountSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class AccountSettingController extends BaseController
{
    /**
     * AccountSettingController constructor.
     *
     * @param AccountSettingService $service
     * @param Request $request
     */
    public function __construct(AccountSettingService $service, Request $request)
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
        return AccountRequest::class;
    }
}
