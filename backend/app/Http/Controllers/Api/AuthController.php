<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\RequestPasswordRequest;
use App\Services\AuthenticationService;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * @var AuthenticationService
     */
    private AuthenticationService $authenticationService;

    /**
     * @param AuthenticationService $authenticationService
     */
    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function login(LoginRequest $request)
    {
        $loginResponse = $this->authenticationService->login($request, 'api_admin');
        if ($loginResponse->getCode() != Response::HTTP_OK) {
            return $this->error([], $loginResponse->getCode(), $loginResponse->getMessage());
        }
        return $this->success(
            [
                'access_token' => $loginResponse->getAccessToken(),
                'profile' => $loginResponse->getProfile(),
            ],
            $loginResponse->getCode(),
            $loginResponse->getMessage()
        );
    }

    public function requestPassword(RequestPasswordRequest $request)
    {
        $requestPasswordResponse = $this->authenticationService->requestPassword($request);
        if ($requestPasswordResponse->getCode() != Response::HTTP_OK) {
            return $this->error([], $requestPasswordResponse->getCode(), $requestPasswordResponse->getMessage());
        }
        return $this->success(
            [],
            $requestPasswordResponse->getCode(),
            $requestPasswordResponse->getMessage()
        );
    }
}
