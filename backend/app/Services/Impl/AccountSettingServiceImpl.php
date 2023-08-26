<?php

namespace App\Services\Impl;

use App\Repositories\Impl\AccountRepository;
use App\Services\AccountSettingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

/**
 * Class AccountSettingServiceImpl
 *
 * @package App\Services\Impl
 *
 * @property AccountRepository $accountRepository
 */
class AccountSettingServiceImpl extends BaseServiceImpl implements AccountSettingService
{
    /**
     * AccountSettingServiceImpl constructor.
     * @param AccountRepository $accountRepository
     */
    public function __construct(AccountRepository $accountRepository)
    {
        parent::__construct($accountRepository);
    }

    /**
     * @Override
     */
    public function create(array $data): Model
    {
        try {
            DB::beginTransaction();
            $account = parent::create($data);
            Password::sendResetLink(['email' => $account->email], function ($account, $token) {
                $account->sendNewPasswordNotification($token);
            });
            DB::commit();
            return $account;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new ModelNotFoundException($e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception(__('messages.create_fail'));
        }
    }
}
