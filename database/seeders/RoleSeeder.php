<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            $role = Role::create($value);
            $permissions = Permission::all();
            $role->syncPermissions($permissions);

        }
    }

    private function datas()
    {
        return [
            [
                'name' => 'Admin',
                'guard_name' => 'admin',
                'created_at' => now(),
            ],
            [
                'name' => 'User',
                'guard_name' => 'admin',
                'created_at' => now(),
            ],

        ];
    }
}
