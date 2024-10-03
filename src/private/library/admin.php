<?php

namespace AS\admin;

class Admin {
    public static function new_column($name, $mysql_column, $sortable = false) {
        $result = array(
            'name' => $name,
            'mysql_column' => $mysql_column,
            'sortable' => $sortable);

        return $result;
    }
}