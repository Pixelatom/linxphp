<?php

abstract class CrudUsersController extends CrudController{
	public function __construct(){
		
		$this->modelname = 'User';
		$this->columns = array(
			'id',
			'username',			
			'email',
		);

		parent::__construct();
	}

	/**
     * custom form generation depending on permissions
     */
    protected function form($object=null){
    	// build default form from parent class
        $form = parent::form($object);

        if (!Authorization::has_access('role_assign'))
        $form->remove_field('role');

        return $form;
    }

	/**
	 * in case to be editting own user we'll change the permission to check
	 */
	protected function access($action, $id = null){
		if ($action == 'edit' and $id == Authorization::get_logged_user()){
			return strtolower($this->modelname).'_'.$action.'_own';
		}
		else{		
        	return strtolower($this->modelname).'_'.$action;
        }
    }

	/**
	 * password field validation on change
	 */
	protected function validate($form, $object=null){

		if ($this->action == 'edit'){
			// we won't make password required on edition
			unset($this->view->form->widget('password')->rules['required']);
		}
        
        if ($valid =  $this->view->form->is_valid()){
        	// check username duplicity
			$username = $this->view->form->widget('username');

			$condition = "username = '".addslashes($username->value)."'";
			if (is_object($object)){
				$condition .= " and id <> " . $object->id;
			}

			if (Mapper::count('User',$condition)>0 ){
				$username->error = "El nombre de usuario ya está siendo usado por otro.";
				$valid = false;
			}

        }

        return $valid;
    }

    /**
     * custom submit for password
     */
	protected function submit($object){
		
		if ($this->view->current_action == 'edit'){
			// ignore password field
			$this->view->form->widget('password')->ignore_submit = true;			
		}
		
		// fill model properties with the form values
	    $this->view->form->submit($object);
		
	    if ($this->view->current_action == 'edit'){
	        // if the password field is not empty we'll assign the value
	        if (!empty($this->view->form->widget('password')->value)){
				$object->password = md5($this->view->form->widget('password')->value);
			}
		}
    }

}