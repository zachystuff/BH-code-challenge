<?php

use AS\infra\AdminPage;
use AS\admin\Admin;

class PostAdminPage extends AdminPage
{
    protected function set_data() {
        $this->title = 'Post';
        $this->table_name = 'posts';
    }

    protected function set_sorts() {
        $this->sorts = [
            Admin::new_column('ID', 'id', true),
            Admin::new_column('User ID', 'user_id'),
            Admin::new_column('Title', 'title'),
            Admin::new_column('Body', 'body'),
            Admin::new_column('Group ID', 'groups_id'),
            Admin::new_column('Created At', 'created_at'),
            Admin::new_column('Is Active', 'is_active'),
        ];
    }

    protected function set_select_query() {
        $this->sql_select_query = "
            SELECT
                id,
                user_id,
                title,
                body,
                groups_id,
                created_at,
                is_active
            FROM
                posts
        ";
    }
}
