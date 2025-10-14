<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos/roles del paquete
        app(PermissionRegistrar::class)->forgetCachedPermissions();



        $perms = [
            'vehicles.view','vehicles.create','vehicles.update','vehicles.delete',
            'drivers.view','drivers.create','drivers.update','drivers.delete',
            'routes.view','routes.create','routes.update','routes.delete',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Roles base
        $admin  = Role::firstOrCreate(['name' => 'admin',  'guard_name' => 'web']);
        $gestor = Role::firstOrCreate(['name' => 'gestor', 'guard_name' => 'web']);
        $chofer = Role::firstOrCreate(['name' => 'chofer', 'guard_name' => 'web']);
        $Usuario  = Role::firstOrCreate(['name' => 'Usuario' ,  'guard_name' => 'web']);



        $admin->syncPermissions(Permission::all());
        $gestor->syncPermissions([
            'vehicles.view','vehicles.create','vehicles.update','vehicles.delete',
            'drivers.view','drivers.create','drivers.update','drivers.delete',
            'routes.view','routes.create','routes.update','routes.delete',
        ]);
        $chofer->syncPermissions([
            'routes.view',
        ]);
        $Usuario->syncPermissions([
            'vehicles.view',
            'drivers.view',
            'routes.view',
        ]);


        // Usuarios demo
        $u1 = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin', 'password' => Hash::make('password'),
        ]);
        $u1->assignRole($admin);

        $u2 = User::firstOrCreate(['email' => 'gestor@example.com'], [
            'name' => 'Gestor', 'password' => Hash::make('password'),
        ]);
        $u2->assignRole($gestor);

        $u3 = User::firstOrCreate(['email' => 'chofer@example.com'], [
            'name' => 'Chofer', 'password' => Hash::make('password'),
        ]);
        $u3->assignRole($chofer);

        $u4 = User::firstOrCreate(['email' => 'user@example.com'], [
            'name' => 'Usuario', 'password' => Hash::make('password'),
        ]);
        $u4->assignRole($Usuario);
    }
}
