<?php

class InstallController extends Controller {

    public function index() {

        $admin = new Role();
        $admin->id = 1; // force id value
        $admin->name = 'admin';
        $admin->permissions = 'admin_access';
        Mapper::save($admin);

        $user = new User();
        $user->id = 1;
        $user->username = 'admin';
        $user->email = 'contact@pixelatom.com';
        $user->password = md5('admin');
        $user->role = $admin;

        Mapper::save($user);
        echo 'Admin user created.';
    }

}