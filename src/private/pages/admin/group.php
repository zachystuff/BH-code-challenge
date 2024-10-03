<?php

use AS\infra\AdminPage;
use AS\admin\Admin;

class GroupAdminPage extends AdminPage
{
    protected function set_data() {
        $this->title = 'Groups';
        $this->table_name = 'groups';
    }

    protected function set_sorts() {
        $this->sorts = [
            Admin::new_column('ID', 'id', true),
            Admin::new_column('Group Name', 'group_name', true),
            Admin::new_column('Admin User ID', 'admin_user_id', true),
            Admin::new_column('Is Active', 'is_active', false),
        ];
    }

    protected function set_select_query() {
        $this->sql_select_query = "
            SELECT
                id,
                group_name,
                admin_user_id,
                is_active
            FROM
                groups
        ";
    }
}
