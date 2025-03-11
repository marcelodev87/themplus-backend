<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TransferUserSeeder extends Seeder
{

    public function run()
    {
        $filePath = public_path('storage/imports/lista_usuarios.xlsx');


        (new FastExcel)->import($filePath, function ($row) {

            $enterprise = DB::table('enterprises')
                ->where('name', $row['nome_igreja'])
                ->first();

            $enterprise = User::create([
                'name' => $row['nome'],
                'email' => $row['email'],
                'position' => 'admin',
                'enterprise_id' => $enterprise->id,
                'view_enterprise_id' => $enterprise->id,
                'password' => Hash::make($row['email'])
            ]);
        });
    }
}
