<?php

namespace App\Repositories;

/**
 * Interface AccountRepositoryInterface
 *
 * @package App\Repositories
 */
interface AccountRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Check unique email
     *
     * @param array $data
     * @param string $mode
     * @return mixed
     */
    public function isUniqueEmail(array $data, string $mode);
}
