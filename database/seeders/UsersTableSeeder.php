<?php

namespace Database\Seeders;

use App\Models\User\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class UsersTableSeeder extends Seeder
{
    public function run()
    {

        User::firstOrCreate([
            'email'                         => 'p.schaer@7industry.ch',
        ],
            [
                'first_name'                => 'Peter',
                'last_name'                 => 'Schär',
                'email'                     => 'p.schaer@7industry.ch',
                'email_verified_at'         => now(),
                'password'                  => '$2y$12$hAgzGyUue8mcYb4KgRGhiunaCNo/fmDBk45pDB4Fi73PJJQYvsF9y', //Default
                'remember_token'            => Str::random(10),
                'created_at'                => Carbon::now(),
                'updated_at'                => Carbon::now()
            ]
        );
    }
}
