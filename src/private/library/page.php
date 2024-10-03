<?php

namespace AS\infra;

include_once('start.php');

abstract class page {
    protected $data = [];
    protected $conf = [];
    private string $template_name;
    private string $auth_redirect = '404';
    protected bool $is_post = false;
    protected bool $login_required = false;

    static $instance;
    
    public function __construct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->is_post = true;
        }
    }
    
    public static function getInstance() {
        if (static::$instance instanceof static) {
            return static::$instance;
        }
        
        return static::$instance = new static();
    }

    public function run() {
        $this->set_data();
        if ($this->login_required) {
            $this->redirect_if_not_logged_in();
        }
        if (!$this->is_auth()) {
            $this->redirect($this->auth_redirect);
        }
        $this->request();
        $template_name = $this->template_name;
        start_twig("$template_name.twig", $this->data);
    }

    protected function set_data(){}

    protected function is_auth() {
        return true;
    }

    static function is_logged_in(): bool {
        return !empty($_SESSION['user_id']);
    }

    protected function redirect_if_not_logged_in() {
        // Get the current page and base64 encode it so we can redirect back to it after login
        $current_page = base64_encode($_SERVER['REQUEST_URI']);
        if (!self::is_logged_in()) {
            $this->redirect('login?redirect=' . $current_page);
        }
    }

    protected function redirect_if_logged_in() {
        if (self::is_logged_in()) {
            $this->redirect('home');
        }
    }

    protected function request() {  
    }

    protected function redirect($page = null) {
        if (empty($page)) {
            $page = $this->auth_redirect;
        }
        header("Location: $page");
        exit;
    }

    protected function set_template($template_name) {
        $this->template_name = $template_name;
    }

    protected function set_redirect($auth_redirect) {
        $this->auth_redirect = $auth_redirect;
    }
}