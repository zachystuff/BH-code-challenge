<?php

include_once(__DIR__ . '/../library/userauth.php');

use AS\library\userauth;
use AS\infra\page;

class logout extends page {

    protected function request() {  
        userauth::logout();
        $this->redirect("login");
    }
}