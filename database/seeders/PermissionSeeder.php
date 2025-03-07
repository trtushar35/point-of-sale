<?php
namespace Database\Seeders;

use App\Models\Menu;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed permissions from predefined data
        //foreach ($this->datas() as $value) {
        //    $this->createPermission($value);
        //}

        // Seed permissions from the menu
        $this->menuPermission();
    }



    private function menuPermission()
    {
        foreach (Menu::with('childrens')->whereNull('parent_id')->get() as $menu) {
            $this->createPermissionFromMenu($menu);
        }
    }

    private function createPermissionFromMenu($menu, $parent_id = null)
    {
        $permission = new Permission([
            'name' => $menu->permission_name,
            'guard_name' => 'admin',
            'parent_id' => $parent_id,
            'created_at' => now(),
        ]);

        $permission->save();

        foreach ($menu->childrens as $child) {
            $this->createPermissionFromMenu($child, $permission->id);
        }
    }

   
    
}
