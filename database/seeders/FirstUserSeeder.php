<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FirstUserSeeder extends Seeder {
    
    public function run(): void {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'uuid'      => Str::uuid(),
                'name'      => 'Administrador',
                'cpfcnpj'   => '00000000000',
                'password'  => bcrypt('123456'),
                'type'      => 'master'
            ]
        );
    }
}
