<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'read-student-data', 'description' => 'Can view student records'],
            ['name' => 'read-employee-data', 'description' => 'Can view employee records'],
            ['name' => 'read-directory-data', 'description' => 'Can view employee directory records'],
            ['name' => 'write-transaction-data', 'description' => 'Can create and update transaction records'],
        ];
        
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
