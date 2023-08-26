<?php

namespace App\Services;

use App\Http\Responses\BaseResponse as ServiceResponse;
use App\Http\Responses\LoginResponse;
use App\Models\Account;
use App\Repositories\Impl\AccountRepository;
use App\Repositories\Impl\PasswordResetRepository;
use App\Repositories\Impl\LoginLogsRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Exceptions\LoginFailedException;

class AuthenticationService
{

    public function __construct(
        private LoginResponse              $loginResponse,
        private ServiceResponse            $serviceResponse,
        private AccountRepository          $accountRepository,
        private PasswordResetRepository    $passwordResetRepository,
        public  LoginLogsRepository        $logLoginRepository,
    )
    {
    }

    public function login($request, $guardRequest = null)
    {
        $credentials = $request->only('email', 'password');
        $credentials['status'] = ACCOUNT_STATUS['ACTIVE'];
        switch($guardRequest){
            case 'api_admin' : { // need fix if want to split router for warehouse
                $guard = 'api_admin';
                $role = ROLE_ADMIN;
                $token = Auth::guard($guard)->attempt($credentials);
                break;
            }
            default : {
                $tokenAdmin = Auth::guard('api_admin')->attempt($credentials);
                if ($tokenAdmin) {
                    $token = $tokenAdmin;
                    $guard = 'api_admin';
                    $role = ROLE_ADMIN;
                }
            }
        }
        $modelLogLogin = $this->logLoginRepository->findByEmail($request->email);
        $currentTime = Carbon::now();
        if (!$token) {
            if ($modelLogLogin) {
                if ($modelLogLogin['login_fail_times'] === MAX_LOGIN_FAIL) {
                    throw new LoginFailedException(__('messages.login_fail_many_time', ['attribute' => $modelLogLogin['unlock_time']]));
                } else {
                    $modelLogLogin['login_fail_times'] = $modelLogLogin['login_fail_times'] + 1;
                    if ($modelLogLogin['login_fail_times'] === MAX_LOGIN_FAIL) {
                        $modelLogLogin['unlock_time'] = Carbon::now()->addHours(TIME_LOCK_LOGIN);
                    }
                    $this->logLoginRepository->update($modelLogLogin['id'], $modelLogLogin->toArray());
                }
            } else {
                $this->logLoginRepository->create(
                    [
                        'email' => $request->email,
                        'login_fail_times' => 1,
                    ]
                );
            }
            $this->loginResponse->setCode(Response::HTTP_UNAUTHORIZED);
            $this->loginResponse->setMessage(__('messages.invalid_password'));
        } else {
            if ($modelLogLogin) {
                // Account is still lock
                if ($currentTime <= $modelLogLogin['unlock_time']) {
                    throw new LoginFailedException(__('messages.login_fail_many_time', ['attribute' => $modelLogLogin['unlock_time']]));
                }
                // Login success, reset value in table log_login
                $modelLogLogin['login_fail_times'] = 0;
                $modelLogLogin['unlock_time'] = null;
                $this->logLoginRepository->update($modelLogLogin['id'], $modelLogLogin->toArray());
            }
            $this->loginResponse->setAccessToken($token);
            $user = Auth::guard($guard)->user();
            $user->save();
            $user->role = $role;
            $this->loginResponse->setProfile($user);
            $this->loginResponse->setMessage(__('messages.login_success'));
        }
        return $this->loginResponse;
    }

    /**
     * Request send email to reset password
     *
     * @param $request
     * @param $broker
     * @return ServiceResponse
     */
    public function requestPassword($request, $broker = null)
    {
        $email = $request->only('email');
        $this->passwordResetRepository->deleteByEmail($email['email']);
        if ($broker) {
            $status = Password::broker($broker)->sendResetLink($email);
        } else {
            // When reset PW in the request password common
            $statusAdmins = Password::broker('admins')->sendResetLink($email);
            $statusUsers = Password::broker('users')->sendResetLink($email);
            $status = $statusAdmins === Password::RESET_LINK_SENT ? $statusAdmins : $statusUsers;
        }

        if ($status !== Password::RESET_LINK_SENT) {
            $this->serviceResponse->setCode(Response::HTTP_BAD_REQUEST);
        }
        $this->serviceResponse->setMessage(__($status));
        return $this->serviceResponse;
    }

    public function resetPassword($request, $broker = null)
    {
        $status = Password::broker($broker)->reset(
            $request->only('password', 'password_confirmation', 'token', 'email'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password
                ]);
                $user->save();
                event(new PasswordReset($user));
            }
        );
        if ($status !== Password::PASSWORD_RESET) {
            $this->serviceResponse->setCode(Response::HTTP_BAD_REQUEST);
        }
        $this->serviceResponse->setMessage(__($status));
        return $this->serviceResponse;
    }

    public function refreshToken($token)
    {
        if (!$token) {
            $this->loginResponse->setCode(Response::HTTP_UNAUTHORIZED);
            $this->loginResponse->setMessage(trans('errors.invalid_token'));
        }
        try {
            $token = JWTAuth::refresh();
            $this->loginResponse->setAccessToken($token);
        } catch (Exception $e) {
            $this->loginResponse->setCode(Response::HTTP_UNAUTHORIZED);
            $this->loginResponse->setMessage(trans('errors.invalid_token'));
        }
        return $this->loginResponse;
    }

    public function activateUser($request, $type = 'admin')
    {
        $email = $request->get('email');
        $token = $request->get('token');
        if ($this->validateResetToken($email, $token)) {
            switch ($type) {
                case 'admin':
                    $user = $this->accountRepository->getByEmail($email);
                    break;
            }
            $user->status = ACCOUNT_STATUS['ACTIVE'];;
            $user->save();
        } else {
            $this->serviceResponse->setCode(Response::HTTP_BAD_REQUEST);
            $this->serviceResponse->setMessage(trans('errors.invalid_token'));
        }
        return $this->serviceResponse;
    }

    public function resendConfirmEmail($data) {
        try {
            $this->passwordResetRepository->deleteByEmail($data['email']);
            $broker = 'admins';
            if (isset($data) && $data['role'] == 'user') {
                if ($data['type'] != User::WAREHOUSE_TYPE) {
                    $broker = 'users';
                }
            }
            Password::broker($broker)->sendResetLink(['email' => $data['email']], function ($admin, $token) {
                $admin->sendNewPasswordNotification($token);
            });
        } catch (Exception $exception) {
            $this->serviceResponse->setCode(Response::HTTP_BAD_REQUEST);
            $this->serviceResponse->setMessage(trans('errors.something_error'));
        }
        return $this->serviceResponse;
    }

    private function validateResetToken($email, $token)
    {
        $password_resets = DB::table('password_resets')
            ->where('email', $email)->first();

        if ($password_resets && Hash::check($token, $password_resets->token)) {
            $createdAt = Carbon::parse($password_resets->created_at);
            if (!Carbon::now()->greaterThan($createdAt->addMinutes(config('auth.passwords.email.expire')))) {
                return true;
            }
        }

        return false;
    }
}
