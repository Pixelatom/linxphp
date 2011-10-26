<?php

abstract class CrudController extends AppController {

    protected $modelname;
    protected $controllername;
    protected $columns = array();
    protected $conditions = '';
    protected $action = 'list';

    function __construct() {
        parent::__construct();
        $this->view->page_title = null;
        $this->controllername = Application::get_controller();
        
        $modeldescription = ModelDescriptor::describe($this->modelname);

        // if there are not defined columns for this model, we'll use all of them :P
        if (empty($this->columns)){
            // define columns properties to be displayed on the table
            
       
            foreach($modeldescription['properties'] as $property=>$attributes){
                // for now only properties that aren't relations to other models
                if (!$modeldescription['properties'][$property]['attributes']['is_relationship']){
                    $this->columns[] = $property;
                }
            }    
        }

        // template configuration
        if (isset($modeldescription["attributes"]["form"]["label"])) {
            $label = $modeldescription["attributes"]["form"]["label"];            
            $this->view->label = $label;
        }
        else {
            $this->view->label = $this->modelname;            
        }

        $this->view->breadcrumb["{$this->controllername}/index"] = $this->view->label .'s';
    }

    /**
     * default controler action, can be inherited in case it needs to do something different
     */
    public function index() {
        // default action: list items
        Application::route("{$this->controllername}/listitems");
    }

    /**
     * access control for each method
     * returns the name of the permission to check to access to a CRUD page
     * or perform a CRUD action
     * possible actions:
     *  - list: list all the items
     *  - edit: edit one item
     *  - add: add a new item
     *  - remove: remove an item.
     */
    protected function access($action, $id = null){
        return strtolower($this->modelname).'_'.$action;
    }

    /**
     * controller path: list items in a table
     */
    public function listitems() {
        $this->action = 'list';
                
        // permission & access control
        if (!Authorization::has_access($this->access($this->action))){
            $this->show_message('Sorry, you don\'t have access to this page',self::WARNING_MSG);
            Application::route('index',true);
        }

        $this->view->current_action = 'list';

        if (!empty($_POST['list'])) {
            //array_shift($_POST['list']);
            $remove = $_POST['list'];

            $this->view->page_title = "Confirm";
            $this->view->ids = $remove;
            $this->view->action = UrlFactory::set_param('route', "{$this->controllername}/remove");
            $this->view->back = UrlFactory::set_param('route', "{$this->controllername}/listitems");
            $this->view->content = new Template('crud/confirm');
            $this->show();
            die();
        }

        $conditions = '';
        // a wild search query appears!
        if (!empty($_GET['q'])) {
            // time to biuld the $conditions string
            $string = addslashes($_GET['q']);

            $modeldescription = ModelDescriptor::describe($this->modelname);

            $table = strtolower($this->modelname);

            if (isset($modeldescription["attributes"]["table"])) {
                $table = $modeldescription["attributes"]["table"];
            }

            foreach ($this->columns as $property) {
                $field = "`$table`.`$property`";
                if (isset($modeldescription['properties'][$property]['attributes']['form'])) {
                    if (isset($modeldescription['properties'][$property]['attributes']['form']['display_property'])) {
                        $type = $modeldescription['properties'][$property]['attributes']['type'];
                        $field = "`$property`.`{$modeldescription['properties'][$property]['attributes']['form']['display_property']}`";
                    }
                }

                if (!empty($conditions))
                    $conditions .= ' OR ';

                $conditions .= "$field like '%$string%'";
            }
        }
        if (!empty($this->conditions) and !empty($conditions)) {
            $conditions = $this->conditions . " AND ( $conditions ) ";
        } elseif (!empty($this->conditions) and empty($conditions)) {
            $conditions = $this->conditions;
        }
        // paginator configuration
        if (!isset($_SESSION['pagesize']))
            $_SESSION['pagesize'] = 10;
        $page = (int) (isset($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
        $pagesize = (isset($_REQUEST['pagesize'])) ? $_REQUEST['pagesize'] : $_SESSION['pagesize'];
        $_SESSION['pagesize'] = $pagesize;

        $pagecount = ceil(Mapper::count($this->modelname, $conditions) / $pagesize);
        $items = array();
        if ($pagecount > 0) {

            if ($page < 1) {
                $page = 1;
                Application::$request_url->set_param('page', $page);
                Application::route(null, true);
            }
            if ($page > $pagecount) {
                $page = $pagecount;
                Application::$request_url->set_param('page', $page);
                Application::route(null, true);
            }

            // DB query of all items to be displayed
            $offset = ($page - 1) * $pagesize;
            $items = Mapper::get($this->modelname, $conditions, $order_by = null, $pagesize, $offset);
        }
        // define columns properties to be displayed on the table
        $modeldescription = ModelDescriptor::describe($this->modelname);
        $values = array();
        $headers = array();
        
        // define table headers values
        foreach ($this->columns as $property) {
            $headers[$property] = $property;
            if (isset($modeldescription['properties'][$property]['attributes']['form'])) {
                if (isset($modeldescription['properties'][$property]['attributes']['form']['label']))
                    $headers[$property] = $modeldescription['properties'][$property]['attributes']['form']['label'];
            }
        }
        $id_property = $modeldescription['primary_key'][0];
        foreach ($items as $row) {
            $item = array();
            foreach ($this->columns as $property) {

                if (isset($modeldescription['properties'][$property]['attributes']['form'])) {
                    // tiene la propiedad form, tenemos que ver como hay que presentar el valor
                    if (isset($modeldescription['properties'][$property]['attributes']['form']['display_property'])) {
                        // una relacion con otro objeto
                        $property = $property . '->' . $modeldescription['properties'][$property]['attributes']['form']['display_property'];

                        eval('$item[] = $row->' . $property . ';');
                    } elseif (isset($modeldescription['properties'][$property]['attributes']['form']['type']) and $modeldescription['properties'][$property]['attributes']['form']['type'] == 'select'
                            and isset($modeldescription['properties'][$property]['attributes']['form']['options'])) {
                        // tipo select
                        $item[] = $modeldescription['properties'][$property]['attributes']['form']['options'][$row->$property];
                    } else {
                        $item[] = $row->$property;
                    }
                } else {
                    $item[] = $row->$property;
                }
            }

            $values[$row->$id_property] = $item;
        }

        // template configuration
        
        if (empty($this->view->page_title))
            $this->view->page_title = $this->view->label .'s';
        

        // table porperties
        $this->view->columns = $this->columns;

        $this->view->headers = $headers;
        $this->view->id_property = $modeldescription['primary_key'][0];
        $this->view->items = $items;
        $this->view->values = $values;
        $this->view->page = $page;
        $this->view->pagesize = $pagesize;
        $this->view->pagecount = $pagecount;

        
        $this->view->controllername = $this->controllername;
        $this->view->modelname = $this->modelname;

        if (empty($this->view->page_description))
            $this->view->page_description = '';
        if (!$this->view->key_exists('addbutton'))
            $this->view->addbutton = true;
        $this->view->content = new Template('crud/list');

        // shows
        $this->show();
    }

    /**
     * returns a template with the form for the model
     * @param object $model
     * @return Template
     */
    protected function form($object=null){
        $model = (is_null($object))? $this->modelname : $object;
        $form =  new ModelForm($model);        
        return $form;
    }

    /**
     * checks if the form is submited
     * @return boolean
     */
    protected function is_submited(){
        return $this->view->form->is_submitted();
    }

    /**
     * validates form submit
     * @return boolean
     */
    protected function validate($form, $object=null){
        return $this->view->form->is_valid();
    }

    /**
     * fill object properties with form values
     * @param <type> $object
     */
    protected function submit($object){
        $this->view->form->submit($object);
    }

    public function edit($id) {
        
        $this->action = 'edit';

        // permission & access control
        if (!Authorization::has_access($this->access($this->action,$id))){
            $this->show_message('Sorry, you don\'t have access to this page',self::WARNING_MSG);
            Application::route('index',true);
        }


        $this->view->current_action = 'edit';
        $modeldescription = ModelDescriptor::describe($this->modelname);
        
        // set page title is case it's empty
        if (empty($this->view->page_title)) {
            $this->view->page_title = 'Editar ' . ucfirst($this->modelname);
            if (isset($modeldescription["attributes"]["form"]["label"])) {
                $label = $modeldescription["attributes"]["form"]["label"];

                $this->view->page_title = 'Editar ' . $label;
                $this->view->label = $label;
            }
        }

        // get the object to be edited
        $object = Mapper::get_by_id($this->modelname, $id);
        $this->view->object = $object;

        // creates form
        $form = $this->form($object);
        $this->view->form = $form;

        if ($this->is_submited()) {

            if ($this->validate($form, $object)) {

                $this->submit($object);
                
                try{
                    if (Mapper::save($object));
                    $this->show_message('changes saved successfully', self::SUCCESS_MSG);
                }
                catch(Exception $e){
                    $this->show_message($e->getMessage(), self::ERROR_MSG);
                }
                Application::route(null, true);
            } else {
                $this->show_message('Form data is no valid', self::ERROR_MSG);
            }
        }

        $this->view->modelname = $this->modelname;
        $this->view->controllername = $this->controllername;        
        $this->view->content = new Template('crud/edit');

        $this->view->breadcrumb[(string) Application::$request_url] = $this->view->page_title;

        $this->show();
    }

    /**
     * controller path: add a new model object form
     */
    public function add() {
        
        $this->action = 'add';

        // permission & access control
       if (!Authorization::has_access($this->access($this->action))){
            $this->show_message('Sorry, you don\'t have access to perform this action',self::NOTICE_MSG);
            Application::route('index',true);
        }

        $this->view->current_action = 'add';
        $modeldescription = ModelDescriptor::describe($this->modelname);

        // set page title..
        if (empty($this->view->page_title)) {
            $this->view->page_title = 'Agregar ' . ucfirst($this->modelname);

            if (isset($modeldescription["attributes"]["form"]["label"])) {
                $label = $modeldescription["attributes"]["form"]["label"];

                $this->view->page_title = 'Agregar ' . $label;
                $this->view->label = $label;
            }
        }

        // creates form
        $form = $this->form();
        $this->view->form = $form;

        if ($this->is_submited()) {
            if ($this->validate($form)) {
                // creates new object
                $class_name = $this->modelname;
                $object = new $class_name();
                // fill values
                $this->submit($object);

                try{
                    if (Mapper::save($object)>0) {
                        $this->show_message(ucfirst($this->modelname) . ' was created successfully', self::SUCCESS_MSG);

                        $route = "{$this->controllername}/edit/" . $object->id;
                        Application::route($route, true);
                    } else {
                        $this->show_message('an error occurred, please try again', CrudController::ERROR_MSG);
                    }
                }
                catch(Exception $e){
                    $this->show_message($e->getMessage(), self::ERROR_MSG);
                }
            } else {
                $this->show_message('Form data is no valid', self::ERROR_MSG);
            }
        }
        $this->view->modelname = $this->modelname;
        $this->view->controllername = $this->controllername;
        $this->view->form = $form;
        $this->view->content = new Template('crud/edit');
        $this->view->breadcrumb[(string) Application::$request_url] = $this->view->page_title;
        $this->show();
    }

    /**
     * controller path: remove an object
     */
    public function remove() {
        $this->action = 'delete';

        // permission & access control
        if (!Authorization::has_access($this->access($this->action, $_POST['list']))){
            $this->show_message('Sorry, you don\'t have access to perform this action',self::NOTICE_MSG);
            Application::route('index',true);
        }

        $this->view->current_action = 'remove';

        if (empty($_POST))
            Application::route('index', true);

        $error = false;
        $classname = $this->modelname;
        foreach ($_POST['list'] as $id) {
            $object = Mapper::get_by_id($classname, $id);
            if (!Mapper::delete($object, true)) {

                $error = true;
            }
        }

        if ($error != true) {
            $this->show_message('Items removed', self::SUCCESS_MSG);
        } else {
            $this->show_message('an error occurred, please try again', self::ERROR_MSG);
        }

        // redirects to the default cotroller action
        Application::route("{$this->controllername}", true);
    }

    public function delete($id) {
        $this->view->current_action = 'remove';

        $remove = array($id);

        $this->view->page_title = "Confirm";
        $this->view->ids = $remove;
        $this->view->action = UrlFactory::set_param('route', "{$this->controllername}/remove");
        $this->view->back = UrlFactory::set_param('route', "{$this->controllername}/listitems");
        $this->view->content = new Template('crud/confirm');
        $this->view->breadcrumb[(string) Application::$request_url] = $this->view->page_title;
        $this->show();
        
    }

    

}