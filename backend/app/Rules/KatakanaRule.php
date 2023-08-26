<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class KatakanaRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return (bool)preg_match("/^[ァ-ヶｦ-ﾟー\s]+$/u", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('half-width.kana');
    }
}
