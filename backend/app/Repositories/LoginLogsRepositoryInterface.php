<?php

namespace App\Repositories;

interface LoginLogsRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get log login by email
     *
     * @param $email
     * @return mixed
     */
    public function findByEmail($email);
}
