<?php

use Illuminate\Database\Seeder;
use App\Role;


class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin = new Role();
        $role_admin->name = 'Super admin';
        $role_admin->description = 'Also know as the Master. A Super Admin can do everything';
        $role_admin->save();


        $role_admin = new Role();
        $role_admin->name = 'Client admin';
        $role_admin->description = 'A client admin can view all sites';
        $role_admin->save();

        
        $role_admin = new Role();
        $role_admin->name = 'BU admin';
        $role_admin->description = 'A BU admin can create edit and delete UT admin. Allocate sites to UT. 
                                    View sites allocated to him';
        $role_admin->save();

        $role_admin = new Role();
        $role_admin->name = 'UT admin';
        $role_admin->description = 'A UT admin can view sites allocated to him by BU';
        $role_admin->save();


        $role_admin = new Role();
        $role_admin->name = 'SiteUser admin';
        $role_admin->description = 'A Site admin can only view site';
        $role_admin->save();

    }
}
