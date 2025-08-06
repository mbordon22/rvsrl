<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'users' => [
                'actions' => [
                    'index' => 'user.index',
                    'create'  => 'user.create',
                    'edit'    => 'user.edit',
                    'trash' => 'user.destroy',
                    'restore' => 'user.restore',
                    'delete' => 'user.forceDelete'
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'trash', 'restore', 'destroy'],
                ]
            ],
            'roles' => [
                'actions' => [
                    'index'   => 'role.index',
                    'create'  => 'role.create',
                    'edit'    => 'role.edit',
                    'delete'  => 'role.destroy'
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'delete'],
                ],
            ],
            'attachments' => [
                'actions' => [
                    'index'   => 'attachment.index',
                    'create'  => 'attachment.create',
                    'delete'  => 'attachment.destroy'
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'delete'],
                    RoleEnum::STAFF => ['index', 'create', 'delete'],
                    RoleEnum::AUTHOR => ['index', 'create', 'delete'],
                    RoleEnum::MEMBER => ['index', 'create', 'delete'],
                    RoleEnum::CREATOR => ['index', 'create', 'delete'],
                ],
            ],
            'categories' => [
                'actions' => [
                    'index'   => 'category.index',
                    'create'  => 'category.create',
                    'edit'    => 'category.edit',
                    'delete'  => 'category.destroy'
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'delete'],
                    RoleEnum::USER => ['index', 'create', 'edit', 'destroy'],
                    RoleEnum::STAFF => ['index', 'create', 'edit', 'destroy'],
                    RoleEnum::AUTHOR => ['index', 'create', 'edit', 'destroy'],
                    RoleEnum::MEMBER => ['index', 'create', 'edit', 'destroy'],
                    RoleEnum::CREATOR => ['index', 'create', 'edit', 'destroy'],
                ]
            ],
            'tags' => [
                'actions' => [
                    'index'   => 'tag.index',
                    'create'  => 'tag.create',
                    'edit'    => 'tag.edit',
                    'trash'   => 'tag.destroy',
                    'restore' => 'tag.restore',
                    'delete'  => 'tag.forceDelete'
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'trash', 'restore', 'delete'],
                    RoleEnum::USER => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::STAFF => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::AUTHOR => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::MEMBER => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::CREATOR => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                ]
            ],
            'blogs' => [
                'actions' => [
                    'index'   => 'blog.index',
                    'create'  => 'blog.create',
                    'edit'    => 'blog.edit',
                    'trash'   => 'blog.destroy',
                    'restore' => 'blog.restore',
                    'delete'  => 'blog.forceDelete'
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'trash', 'restore', 'delete'],
                    RoleEnum::USER => ['index', 'create', 'edit', 'forceDelete'],
                    RoleEnum::STAFF => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::AUTHOR => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::MEMBER => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::CREATOR => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                ]
            ],
            'pages' => [
                'actions' => [
                    'index'   => 'page.index',
                    'create'  => 'page.create',
                    'edit'    => 'page.edit',
                    'trash'   => 'page.destroy',
                    'restore' => 'page.restore',
                    'delete'  => 'page.forceDelete'
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'edit', 'trash', 'restore', 'delete'],
                    RoleEnum::USER => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::STAFF => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::AUTHOR => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::MEMBER => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                    RoleEnum::CREATOR => ['index', 'create', 'edit', 'destroy', 'restore', 'forceDelete'],
                ]
            ],
            'vehiculos' => [
                'actions' => [
                    'index'   => 'vehiculo.index',
                    'create'  => 'vehiculo.create',
                    'update'  => 'vehiculo.update',
                    'destroy' => 'vehiculo.destroy',
                ],
                'roles' => [
                    RoleEnum::ADMIN => ['index', 'create', 'update', 'destroy'],
                    RoleEnum::STAFF => ['index', 'create', 'update'],
                ],
            ],
        ];

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $userpermision = [];
        $staffpermision = [];
        $authorpermision = [];
        $memberpermision = [];
        $creatorpermision = [];

        foreach ($modules as $key => $value) {
            Module::updateOrCreate(['name' => $key], ['name' => $key, 'actions' => $value['actions']]);
            foreach ($value['actions'] as $key => $permission) {
                if (!Permission::where('name', $permission)->first()) {
                    $permission = Permission::create(['name' => $permission]);
                }
                if (isset($value['roles'])) {
                    foreach ($value['roles'] as $role => $allowed_actions) {
                        if ($role == RoleEnum::USER) {
                            if (in_array($key, $allowed_actions)) {
                                $userpermision[] = $permission;
                            }
                        }
                        if ($role == RoleEnum::STAFF) {
                            if (in_array($key, $allowed_actions)) {
                                $staffpermision[] = $permission;
                            }
                        }
                        if ($role == RoleEnum::AUTHOR) {
                            if (in_array($key, $allowed_actions)) {
                                $authorpermision[] = $permission;
                            }
                        }
                        if ($role == RoleEnum::MEMBER) {
                            if (in_array($key, $allowed_actions)) {
                                $memberpermision[] = $permission;
                            }
                        }
                        if ($role == RoleEnum::CREATOR) {
                            if (in_array($key, $allowed_actions)) {
                                $creatorpermision[] = $permission;
                            }
                        }
                    }
                }
            }
        }


        $admin = Role::create([
            'name' => RoleEnum::ADMIN,
            'system_reserve' => true
        ]);
        $admin->givePermissionTo(Permission::all());
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('123456789'),
            'gender' => 'male',
            'dob' => '01/01/2000',
            'location' => 'Rome',
            'country_code' => '39',
            'phone' => '612345678',
            'skills' => 'Developer',
            'about_me' => 'Administrator',
            'first_name' => 'john',
            'last_name' => 'deo',
            'postal_code' => '00100',
            'address' => 'Rome',
            'country_id' => 380,
            'state_id' => 1771,
            'bio' => 'Developer',
            'system_reserve' => true,
        ]);
        $user->assignRole($admin);

        $userRole = Role::create([
            'name' => RoleEnum::USER,
            'system_reserve' => false
        ]);

        $userRole->givePermissionTo($userpermision);
        $user = User::factory()->create([
            'first_name' => 'Maximiliano',
            'last_name' => 'Rivadeneira Bordon',
            'email' => 'mrivadeneirabordon@gmail.com',
            'password' => Hash::make('Racing14'),
            'country_code' => '33',
            'phone' => '0123456789',
            'dob' => '08/11/2024',
            'gender' => 'male',
            'status' => '1',
            'country_id' => '250',
            'state_id' => '1201',
            'location' => 'Paris',
            'postal_code' => '75001',
            'about_me' => 'I like using platforms that make life easier. As a user, I value efficiency and creativity, and I am always on the lookout for exciting features and content. I appreciate how easy it is to navigate this space and find what I am looking for.',
            'bio' => 'Quinn Mcdowell is a regular user who enjoys exploring the platform and utilizing its features. He is a curious individual who values simplicity and ease of access in everything he does. Quinn frequently seeks out new content and loves discovering innovative ideas.',
            'system_reserve' => false,
        ]);
        $user->assignRole($admin);
        $image = public_path('/assets/images/user-images/1.png');
        if (File::exists($image)) {
            $user->addMedia($image)->toMediaCollection('image');
        }
    }
}
