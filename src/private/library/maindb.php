<?php

namespace AS\library;
use PDO; 

class maindb extends PDO {

    function __construct() {
        return parent::__construct('pgsql:host=postgres;port=5432;dbname=main;','myuser', 'mypassword');
    }

    static function getInstance() {
        return new maindb;
    }
}