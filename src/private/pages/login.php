<?php

include_once(__DIR__ . '/../library/maindb.php');
include_once(__DIR__ . '/../library/userauth.php');

use AS\library\maindb;
use AS\library\userauth;
use AS\infra\page;

class login extends page {

    protected function request() {  
        $this->set_template('login');
        if ($this->is_post) {
            $results = userauth::login( $_POST['password'], $_POST['username']);
            if ($results['result'] == 'success') {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $results['user_id'];
                $this->redirect();
            } else {
                $this->data['error'] = true;
            }
        }
    }

    protected function is_auth() {
        if (!empty($_GET['redirect'])) {
            $redirect = base64_decode($_GET['redirect']);
            $this->set_redirect($redirect);
        } else {
            $this->set_redirect('profile');
        }
        return empty($_SESSION['logged_in']);
    }
}