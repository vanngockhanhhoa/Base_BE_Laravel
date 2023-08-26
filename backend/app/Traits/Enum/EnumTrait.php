<?php

namespace App\Traits\Enum;

use Illuminate\Support\Str;
use BenSampo\Enum\Enum;

/**
 * Trait EnumTrait
 * @package App\Traits\Enum
 * @property Enum static
 */
trait EnumTrait
{
    public static function getLists(): array
    {
        $values = static::getValues();

        $result = [];
        foreach ($values as $index => $value) {
            $type = static::fromValue($value);
            $result[] = [
                'id' => $type->value,
                'value' => Str::lower($type->key),
                'description' => $type->description
            ];
        }

        return $result;
    }

    public static function getKeyName($value): string
    {
        $type = static::fromValue($value);
        return strtolower($type->key);
    }
}
