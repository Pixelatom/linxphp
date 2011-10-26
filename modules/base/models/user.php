<?php
/**
 * form:
 *  label: Usuario
 *  value: id
 *  title: username
 */
class User extends Model{
    /**
     * type: integer
     * primary_key: true
     * auto_increment: true
     */
    public $id;

    /**
     * form:
     *  label: Nombre de usuario
     *  rules:
     *   required: El nombre de usuario es requerido
     *  description: Escriba un nombre de usuario.
     */ 
    public $username;

    /**
     * form:
     *  label: Password
     *  type: password
     *  description: Ingrese un valor para cambiar el Password
     *  default:
     *  rules: 
     *   required: El password es requerido.
     */
    public $password;

    /**
     * form:
     *  label: E-mail
     *  rules:
     *   email: El formato del email es incorrecto
     *  description: Ingrese un email válido.
     */
    public $email;

    /**
     * type: Role
     * relationship: 
     *  type: parent
     * form:
     *  label: Rol del usuario
     *  type: select
     */
    public $role;


    public function has_permission($permission){
        // user id = 1 is SUPERADMIN he can do EVERYTHING!! :O
        if ($this->id == 1) return true;
        // no role, no permissions, go cry to the little field.
        if (is_null($this->role)) return false;

        return $this->role->has_permission($permission);
    }

}