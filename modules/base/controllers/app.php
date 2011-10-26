<?php
/**
 * Abstract Controller to be extended by all the app controllers
 * provides controll access, unified message setting and default show function
 */
abstract class AppController extends Controller {
    
    // default template
    protected $view;

    public function __construct() {
        // check the user is logged in
        if (!Authorization::is_user_logged_in() and Application::$request_url->get_param('route') != 'login') {
            Application::route('login'); //executes login page without redirecting
            die();
        }

        // load default template
        $this->view = new Template('layout');
        $this->view->logged_user = Mapper::get_by_id('User', Authorization::get_logged_user());
        
        $this->view->breadcrumb = array('index' => 'Dashboard');
    }

    /**
     * shows the default template and clear messages queue
     */
    protected function show() {
        $this->view->messages = (!empty($_SESSION['_messages'])) ? $_SESSION['_messages'] : array();
        $_SESSION['_messages'] = array();        
        $this->view->show();
    }

    /**
     * Messages types
     */
    const WARNING_MSG = "warning";
    const ERROR_MSG = "error";
    const SUCCESS_MSG = "success";
    const INFO_MSG = "info";

    /**
     * Adds a message to be shown on next template render
     * @param <type> $message
     * @param <type> $type
     */
    protected function show_message($message, $type = self::INFO_MSG) {
        $_SESSION['_messages'][] = array('type' => $type, 'message' => $message);
    }

}