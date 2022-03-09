<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'username' => 'admin',
            'phone_number' => '0123456789',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'buyer_days' => '0',
            'seller_days' => '0',
            'commission' => '0',
        ]);
    }
}
