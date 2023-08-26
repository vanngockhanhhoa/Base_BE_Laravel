<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AccountRequest extends BaseRequest
{
    /**
     * Define rules for store function
     *
     * @return array
     */
    public function rulesPost()
    {
        return [
            'name' => V_VARCHAR_REQUIRED,
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:accounts,email,NULL,id,deleted_at,NULL',
            ],
            'status' => 'required',
        ];
    }

    /**
     * Define rules for update function
     *
     * @return array
     */
    public function rulesPut()
    {
        $id = $this->id;
        return [
            'name' => V_VARCHAR_REQUIRED,
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('accounts')
                    ->whereNot('id', $this->request->get('id'))
                    ->where('email', $this->request->get('email'))
                    ->whereNull('deleted_at'),
            ],
            'status' => 'required',
        ];
    }

    /**
     * Bind attributes into message
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => __('field.account.name'),
            'email' => __('field.account.email'),
            'status' => __('field.account.status'),
        ];
    }
}
