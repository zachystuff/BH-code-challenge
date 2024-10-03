<?php

include_once(__DIR__ . '/../library/maindb.php');
include_once(__DIR__ . '/../library/userauth.php');

use AS\library\maindb;
use AS\library\userauth;
use AS\infra\page;

class signup extends page {

    protected function request() {      
        $this->set_template('signup');
        if ($this->is_post) {
            $results = userauth::signup($_POST['email'], $_POST['password'], $_POST['username']);
            if ($results['result'] == 'success') {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $results['user_id'];
                $this->redirect("profile");
            } else {
                $this->data['error'] = 'Something went wrong';
            }
        }
    }

    protected function is_auth() {
        $this->set_redirect('profile');
        return empty($_SESSION['logged_in']);
    }
}