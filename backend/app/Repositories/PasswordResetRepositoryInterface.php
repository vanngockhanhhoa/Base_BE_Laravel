<?php

namespace App\Repositories;

/**
 * Interface AdminRepositoryInterface
 *
 * @package App\Repositories
 */
interface PasswordResetRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * Delete record by email
     *
     * @param string $email
     * @return void
     */
    public function deleteByEmail(string $email);
}
