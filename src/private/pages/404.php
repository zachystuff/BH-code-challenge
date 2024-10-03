<?php

use AS\infra\page;

class not_found extends page {

    protected function request() {      
        $this->set_template('404');
    }
}