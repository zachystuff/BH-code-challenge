<?php

use AS\infra\AdminPage;
use AS\admin\Admin;

class UserAdminPage extends AdminPage
{

    protected function set_data() {
        $this->title = 'User';
        $this->table_name = 'users';
    }

    protected function set_sorts() {
        $this->sorts = [
            Admin::new_column('ID', 'id', true),
            Admin::new_column('Email', 'email', true),
            Admin::new_column('Username', 'username', true),
            Admin::new_column('Password', 'password', true),
            Admin::new_column('Is Admin', 'is_admin', false),
        ];
    }

    protected function set_select_query() {
        $this->sql_select_query = "
            SELECT
                id,
                email,
                username,
                password,
                is_admin
            FROM
                users
        ";
    }
}
