<?php

/*
 * Linx PHP Framework
 * Author: Javier Arias. *
 * Licensed under MIT License.
 */
/**
 * @package template system
 */

/**
 * Template is the main View class of MVC pattern .
 * 
 * Views are files that contain the display information for your application. This is most commonly HTML, CSS and JavaScript but can be anything you require such as XML or Json for AJAX output. The purpose of views is to keep this information separate from your application logic for easy reusability and cleaner code.
 * While this is true, views themselves can contain code used for displaying the data you pass into them. For example, looping through an array of product information and display each one on a new table row. Views are still PHP files so you can use any code you normally would. Views are executed in the Controller namespace so you have access to all resources you have loaded into $this->
 * When this view is rendered it is executed just as any PHP script would and the output from it is returned (or sent to the browser if you so wish).
 *
 */
class Template extends BaseTemplate {

    protected $_default_template;
    protected $_custom_path = false;

    /**
     * This method is static. Parameters are the same as creating a new instance.
     * It creates a View instance and immediately returns it so method chaining is possible.
     */
    static public function factory($default_template=null, $custom_path=null) {
        $class = get_class();
        return new $class($default_template, $custom_path);
    }

    function __construct($default_template=null, $custom_path=null) {
        $this->set_default_template($default_template);

        $this->set_custom_path($custom_path);
    }

    /**
     * change the path where the class will search for the file it has to show
     */
    public function set_custom_path($custom_path) {
        $this->_custom_path = $custom_path;
        return $this;
    }

    /**
     * set a default file or object to render when the method show is called without parameters
     */
    public function set_default_template($default_template) {
        $this->_default_template = $default_template;
        return $this;
    }

    /**
     * renders the output of the View.
     *
     * @param unknown_type $name: (opcional) si no es especificado
     * muestra el template default del objeto,
     * si es un strig, busca el archivo .php con el mismo nombre y lo usa
     * de template.
     * si es otro objeto template, lo muestra agregandole las variables
     * que tiene seteadas este objeto.
     *
     */
    function show($name=null) {
        Event::run('template.show_call', $this, $name);

        # sumamos a las variables seteadas con los metodos comunes, las variables seteadas dinamicamente.
        $vars = array_merge(get_object_vars($this), $this->_vars);

        $onbuffer = false;
        $onbuffer = ob_start();
        try {
            // si $name se trata de un objeto
            if (!empty($name) and is_object($name) and (get_class($name) == 'Template' or is_subclass_of($name, 'BaseTemplate'))) {
                /* @var $name Template */
                $name = clone $name;

                # copiamos todas las variables que tenemos en este template al template que se paso por parametros
                foreach ($vars as $key => &$value) {
                    if (!isset($name->_vars[$key])) {
                        $name->_vars[$key] = &$value;
                    }
                }

                $name->show();
            } else {

                #buscamos entre todas las variables que tenemos asignadas por un objeto template
                foreach ($vars as $key => &$value) {
                    # si es un template le asigna las variables que este template tiene
                    if (!empty($value) and is_object($value) and (get_class($value) == 'Template' or is_subclass_of($value, 'BaseTemplate'))) {
                        $value = clone $value;

                        foreach ($vars as $key1 => &$value1) {
                            if (!isset($value->_vars[$key1]) and $key != $key1) {
                                $value->_vars[$key1] = &$value1;
                            }
                        }
                    }
                }

                $this->include_template($name, $vars);
            }
        } catch (Exception $e) {
            if ($onbuffer) {
                ob_end_flush();
            }
            throw $e;
        }

        if ($onbuffer) {
            $output = ob_get_contents();
            ob_end_clean();


            Event::run('template.show', $output, $this, $name);
            echo $output;
        }
        return $this;
    }

    protected function include_template($name, &$vars) {        
        # va a mostrar template default
        if (empty($name)) {
            if (empty($this->_default_template))
                throw new Exception("Empty template");
            $name = $this->_default_template;
        }

        if (empty($this->_custom_path))
            $path = Application::get_site_path() . Configuration::get('paths', 'templates') . '/' . $name . '.php';
        else
            $path = realpath($this->_custom_path) . '/' . $name . '.php';

        if (!file_exists($path))
            throw new Exception('Template `' . $name . '` does not exists');


        $this->clousure($path, $vars);
    }

    protected function clousure($path, &$vars) {
        extract($vars, EXTR_REFS);
        include($path);
    }

}
?>