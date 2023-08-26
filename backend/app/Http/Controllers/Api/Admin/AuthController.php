<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\RequestPasswordRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Requests\Admin\AccountActivationRequest;
use App\Http\Requests\Admin\ResendConfirmMailRequest;
use App\Http\Controllers\Controller;

use App\Services\AuthenticationService;

use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

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
        $requestPasswordResponse = $this->authenticationService->requestPassword($request, 'admins');
        if ($requestPasswordResponse->getCode() != Response::HTTP_OK) {
            return $this->error([], $requestPasswordResponse->getCode(), $requestPasswordResponse->getMessage());
        }
        return $this->success(
            [],
            $requestPasswordResponse->getCode(),
            $requestPasswordResponse->getMessage()
        );
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $resetPasswordResponse = $this->authenticationService->resetPassword($request, 'admins');
        if ($resetPasswordResponse->getCode() != Response::HTTP_OK) {
            return $this->error([], $resetPasswordResponse->getCode(), $resetPasswordResponse->getMessage());
        }
        return $this->success(
            [],
            $resetPasswordResponse->getCode(),
            $resetPasswordResponse->getMessage()
        );
    }

    public function refreshToken()
    {
        $token = JWTAuth::getToken();
        $refreshTokenResponse = $this->authenticationService->refreshToken($token);
        if ($refreshTokenResponse->getCode() != Response::HTTP_OK) {
            return $this->error([], $refreshTokenResponse->getCode(), $refreshTokenResponse->getMessage());
        }
        return $this->success(
            [
                'access_token' => $refreshTokenResponse->getAccessToken(),
            ],
            $refreshTokenResponse->getCode(),
            $refreshTokenResponse->getMessage()
        );
    }

    public function activateAccount(AccountActivationRequest $request)
    {
        $activationResponse = $this->authenticationService->activateUser($request);
        if ($activationResponse->getCode() != Response::HTTP_OK) {
            return $this->error([], $activationResponse->getCode(), $activationResponse->getMessage());
        }
        return $this->success(
            [],
            $activationResponse->getCode(),
            $activationResponse->getMessage()
        );
    }

    public function resendConfirmEmail(ResendConfirmMailRequest $request)
    {
        $resendMailResponse = $this->authenticationService->resendConfirmEmail($request->all());
        if ($resendMailResponse->getCode() != Response::HTTP_OK) {
            return $this->error([], $resendMailResponse->getCode(), $resendMailResponse->getMessage());
        }
        return $this->success(
            [],
            $resendMailResponse->getCode(),
            $resendMailResponse->getMessage()
        );
    }
}
