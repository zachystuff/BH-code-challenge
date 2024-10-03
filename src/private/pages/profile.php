<?php

include_once(__DIR__ . '/../library/maindb.php');
include_once(__DIR__ . '/../library/userauth.php');

use AS\library\maindb;
use AS\infra\page;
use AS\library\userauth;

class profile extends page {
    protected function set_data() {
        $this->login_required = true;
    }

    protected function request() {  
        $this->set_template('profile');
        $this->data['pageTitle'] = 'My Profile';

        $db = new maindb;

        $this->data['profile'] = $this->get_profile();

        if ($this->is_post) {
            if (!empty($_POST['username']) && $this->data['profile']['username'] != $_POST['username']){
                if (userauth::is_username_used($_POST['username'])) {
                    $this->data['error'] = 'Error';
                    return;
                }
            }
            if (!empty($_POST['email']) && $this->data['profile']['email'] != $_POST['email']) {
                if (userauth::is_email_used($_POST['email'])) {
                    $this->data['error'] = 'Error';
                    return;
                }
            }
            $query = $db->prepare('UPDATE users SET email = :email, username = :username WHERE id = :id');
            $query->execute([
                ':email' => $_POST['email'],
                ':username' => $_POST['username'],
                ':id' => $_SESSION['user_id']
            ]);
            $this->data['success'] = 'Success';
            $this->data['profile'] = $_POST;
        }
    }

    protected function get_profile() {
        $db = new maindb;
        $query = $db->prepare('SELECT email, username FROM users WHERE id = :id');
        $query->execute([':id' => $_SESSION['user_id']]);
        return $query->fetch();
    }
}