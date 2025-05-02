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
            [
                'id'             => 2,
                'name'           => 'Laurent Aubineau',
                'email'          => 'l.aubineau@astorya.fr',
                'password'       => bcrypt('laurent'),
                'remember_token' => null,
                'locale'         => 'fr',
            ],

            [
                'id'             => 3,
                'name'           => 'Louise Neau',
                'email'          => 'l.neau@astorya.fr',
                'password'       => bcrypt('louise'),
                'remember_token' => null,
                'locale'         => 'fr',
            ],
        ];

        User::insert($users);
    }
}
