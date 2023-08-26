<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Account::create([
            'name' => 'admin',
            'email' => 'admin@thefirstone.jp',
            'password' => 'Password@1234',
            'role' => ROLE_ADMIN,
            'status' => ACCOUNT_STATUS['ACTIVE']
        ]);
        Account::create([
            'name' => 'admin1',
            'email' => 'admin@cnctor.jp',
            'password' => 'Password@1234',
            'role' => ROLE_ADMIN,
            'status' => ACCOUNT_STATUS['ACTIVE']
        ]);
        Account::create([
            'name' => 'admin2',
            'email' => 'admin2@thefirstone.jp',
            'password' => 'Password@1234',
            'role' => ROLE_ADMIN,
            'status' => ACCOUNT_STATUS['INACTIVE']
        ]);
    }
}
