<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'id'             => 1,
                'name'           => 'Alexandre Guyot',
                'email'          => 'a.guyot@astorya.fr',
                'password'       => bcrypt('alex'),
                'remember_token' => null,
                'locale'         => 'fr',
            ],
        ];

        User::insert($users);
    }
}
