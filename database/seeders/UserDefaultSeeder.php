<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;

class UserDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = array(
            [
                'id' => 1,
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' =>  bcrypt('admin'),
                'admin' => '1'
            ],
        );

        foreach ($items as $item) :
            User::updateOrCreate(
                [
                    'name' => $item['name'],
                    'email' => $item['email'],
                ],
                [
                    'password' => $item['password'],
                    'admin' => $item['admin'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );
        endforeach;
    }
}
