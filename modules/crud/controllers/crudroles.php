<?php

abstract class CrudRolesController extends CrudController{
	public function __construct(){
		
		$this->modelname = 'Role';
		$this->columns = array(
			'id',
			'name',
		);

		parent::__construct();
	}

	/**
     * CRUD overwritten method
     * validates form submit
     * @return boolean
     */
    protected function validate($form, $object=null){
    	$return = true;

        if ($return = parent::validate($form, $object)){

        	if (empty($object->id)){
	        	$name = $this->view->form->widget('name')->value;
	        	if (Mapper::count('Role','name = "'.addslashes($name).'"')>0){
	        		$this->view->form->widget('name')->error = "Este nombre de rol ya existe.";
	        		$return = false;
	        	}
        	}
        }

        return $return;
    }
	
	/**
	 * CRUD overwritten method
	 * changes the form template
	 */
	protected function form($object=null){
        $model = (is_null($object))? $this->modelname : $object;
        $form =  new ModelForm($model);
        
        // changes the form template when editing
        if (is_object($object)){
        	$form->set_default_template('users/roles/role.form');
        	$form->permissions = Mapper::get('permission','','name');
        	$form->role = $object;
		}
                
        return $form;
    }

    /**
     * CRUD overwritten method
     * adds logic to process the permissions array
     */
    protected function submit($object){		

        $this->view->form->submit($object);

        if(isset($_POST['permissions'])){
	       $object->assign_permissions($_POST['permissions']);
		}
    }


    function permissions() {

    	$this->view->breadcrumb[(string) Application::$request_url] = 'Permisos';
		
		$this->view->current_action = 'permissions';

		if (!Authorization::has_access('permissions_admin')){
            $this->show_message("No tienes permisos para acceder a esta página.", self::ERROR_MSG);
            Application::route('index', true);
        }
		
		if(isset($_POST['permissions']))
		{
		   foreach($_POST['permissions'] as $role => $permissions)
		   {
		       $role_model = Mapper::get_by_id('role', $role);		       
		       $role_model->assign_permissions($permissions);
		       Mapper::save($role_model);
		   }
		}		
		
		$permissions = Mapper::get('permission','','name');
		$roles = Mapper::get('role');
		$this->view->content = new Template('users/roles/permissions');
		$this->view->permissions = $permissions;
		$this->view->roles = $roles;
		$this->show();
    }

}