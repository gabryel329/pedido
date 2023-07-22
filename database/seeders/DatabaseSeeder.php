<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //USER
        $user = new User();
        $user->name = 'admin';
        $user->email = 'admin@sualoja.com';
        $user->password = bcrypt('12345678');
        $user->save();

        //STATUS
        DB::table('status')->insert([
            'nome' => 'FEITO',
        ]);

        DB::table('status')->insert([
            'nome' => 'DELETADO',
        ]);

        DB::table('status')->insert([
            'nome' => 'EM ANDAMENTO',
        ]);

        //FORMA PAGAMENTO
        DB::table('forma_pagamento')->insert([
            'nome' => 'PIX',
        ]);

        DB::table('forma_pagamento')->insert([
            'nome' => 'DEBITO',
        ]);

        DB::table('forma_pagamento')->insert([
            'nome' => 'CREDITO',
        ]);

        DB::table('forma_pagamento')->insert([
            'nome' => 'DINHEIRO',
        ]);
    }
}
