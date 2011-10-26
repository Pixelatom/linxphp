<?php
/**
 * form:
 *  label: Role
 *  value: id
 *  title: name
 */
abstract class AppRole extends Model{
    /**
     * type: integer
     * primary_key: true
     * auto_increment: true
     */
    public $id;

    /**
     * form:
     *  label: Name
     *  rules:
     *   required: You must specify a name for the Role.
     */
    public $name;

    /**
     * form: false
     */
    public $permissions;
    
    public function has_permission($permission){
        $array = explode(',', $this->permissions);
        return in_array($permission, $array);
    }
    public function add_permission($permission){
        if (!$this->has_permission($permission)){
            $this->permissions .= ','.$permission;           
        }
    }
    public function assign_permissions($permissions){
            $this->permissions = implode(',',$permissions);
    }
    public function remove_permission($permission){
        $array = explode(',', $this->permissions);
        if (($key=array_search($permission, $array))!==false){
            unset($array[$key]);
            $this->permissions = implode(',', $array);            
            return true;
        }
        return false;
    }
}