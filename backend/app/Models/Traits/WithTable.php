<?php

namespace App\Models\Traits;

trait WithTable
{
    /**
     * @return mixed
     */
    public static function getTableName()
    {
        return with(new static())->getTable();
    }
}
