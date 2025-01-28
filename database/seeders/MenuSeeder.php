<?php

namespace Database\Seeders;

use App\Models\Menu; // Correct import
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            $this->createMenu($value);
        }
    }

    private function createMenu($data, $parent_id = null)
    {
        $menu = new Menu([
            'name' => $data['name'],
            'icon' => $data['icon'],
            'route' => $data['route'],
            'description' => $data['description'],
            'sorting' => $data['sorting'],
            'parent_id' => $parent_id,
            'permission_name' => $data['permission_name'],
            'status' => $data['status'],
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $menu->save();

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->createMenu($child, $menu->id);
            }
        }
    }

    private function datas()
    {
        return [
            [
                'name' => 'Dashboard',
                'icon' => 'home',
                'route' => 'backend.dashboard',
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'dashboard',
                'status' => 'Active',
            ],
            // [
            //     'name' => 'Module Make',
            //     'icon' => 'slack',
            //     'route' => 'backend.moduleMaker',
            //     'description' => null,
            //     'sorting' => 1,
            //     'permission_name' => 'module maker',
            //     'status' => 'Active',
            // ],
            [
                'name' => 'User Manage',
                'icon' => 'user',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'user-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'User Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.admin.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'Admin-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'User List',
                        'icon' => 'list',
                        'route' => 'backend.admin.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'Admin-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Permission Manage',
                'icon' => 'unlock',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'permission-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Permission Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.permission.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'permission-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Permission List',
                        'icon' => 'list',
                        'route' => 'backend.permission.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'permission-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Role Manage',
                'icon' => 'layers',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'role-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Role Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.role.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'role-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Role List',
                        'icon' => 'list',
                        'route' => 'backend.role.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'role-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Category Manage',
                'icon' => 'aperture',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'category-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Category Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.Category.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'category-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Category List',
                        'icon' => 'list',
                        'route' => 'backend.Category.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'category-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Size Manage',
                'icon' => 'aperture',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'Size-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Size Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.Size.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'Size-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Size List',
                        'icon' => 'list',
                        'route' => 'backend.Size.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'Size-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Color Manage',
                'icon' => 'aperture',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'color-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Color Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.Color.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'color-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Color List',
                        'icon' => 'list',
                        'route' => 'backend.Color.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'color-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Product Manage',
                'icon' => 'package',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'product-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Product Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.product.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'product-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Product List',
                        'icon' => 'list',
                        'route' => 'backend.product.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'product-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Inventory Manage',
                'icon' => 'shopping-cart',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'inventory-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Inventory Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.inventory.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'inventory-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Inventory List',
                        'icon' => 'list',
                        'route' => 'backend.inventory.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'inventory-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Invoice Manage',
                'icon' => 'dollar-sign',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'invoice-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Invoice Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.invoice.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'invoice-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Invoice List',
                        'icon' => 'list',
                        'route' => 'backend.invoice.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'invoice-list',
                        'status' => 'Active',
                    ],
                ],
            ],

            //don't remove this comment from menu seeder
        ];
    }
}
