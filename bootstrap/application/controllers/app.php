<?php

abstract class AppController extends BaseController {

    function __construct() {
        parent::__construct();
        // application settings
        $this->view->script = array();
        $this->view->css = array();
        
        $this->view->site_name = 'Boostrap';
        
        $this->view->navigation = array();
        if (Authorization::is_user_logged_in())
        $this->view->navigation['logout'] = 'Logout';
        
        if (Authorization::has_access('admin_access')){
            $this->insertMenuItem('admin',array(
                'Admin',
                array(
                    'admin/users'=>'Users',
                    'admin/roles'=>'Roles',
                    'admin/roles/permissions'=>'Permissions',
                    ),
            ),'logout');
        }

        
        
        $this->view->script[] = 'http://code.jquery.com/jquery-1.7.2.min.js';
        $this->view->script[] = 'js/bootstrap-dropdown.js';
    }

    protected function insertMenuItem($route, $menuItem, $beforeRoute=null) {
        if (!empty($beforeRoute)){
            // find the position of the key
            $internalPosition = 0;
            $found = false;
            foreach ($this->view->navigation as $key => $value) {
                if ($key == $beforeRoute) {
                    $found = true;
                    break;
                }
                $internalPosition++;
            }

            if (!$found) {
                throw new Exception('Couldn\'t find');
            }

            // use that position to split array into two halves
            $firstPart = array_slice($this->view->navigation, 0, $internalPosition);
            $secondPart = array_slice($this->view->navigation, $internalPosition);

            // reconstruct array
            $this->view->navigation = $firstPart;
            $this->view->navigation[$route] = $menuItem; // new data
            foreach ($secondPart as $key => $value) {
                $this->view->navigation[$key] = $value;
            }
            
        }
        else{
            $arr = array_reverse($this->view->navigation, true);
            $arr[$route] = $menuItem;
            $this->view->navigation = array_reverse($arr, true);
        }       
        
    }

}