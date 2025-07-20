<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['nama_role' => 'admin']);
        Role::firstOrCreate(['nama_role' => 'verifikator']);
        Role::firstOrCreate(['nama_role' => 'anggota']);
    }
}
