<?php
/**
 * Description of authorizations
 *
 * @author javier
 */
class Authorization {
    //put your code here
    static public function is_user_logged_in(){
        return isset($_SESSION['user_logged_in']);
    }

    static public function user_log_in($user){
        unset($_SESSION['user_logged_in']);
        $_SESSION['user_logged_in']=$user;
    }

    static public function user_log_out(){
        unset($_SESSION['user_logged_in']);
    }

    static public function get_logged_user(){
        return ($_SESSION['user_logged_in']);
    }

    static public function has_access($permission){
        if (!Mapper::get_by_id('Permission', $permission)){
            $new = new Permission();
            $new->name = $permission;
            Mapper::save($new);
        }
        if (!self::is_user_logged_in()) return false;
       
        return Mapper::get_by_id('User',$_SESSION['user_logged_in'])->has_permission($permission);
    }
}
?>