<?php

namespace App\Models\Traits;

trait HasFillable
{
    /**
     * Check model has field
     * @param $key
     * @return bool
     */
    public function hasField($key): bool
    {
        return in_array($key, $this->getFillable()) || property_exists($this, $key);
    }
}
