<?php

use AS\infra\page;
use AS\library\maindb;

class group extends page {
    protected function set_data() {
        $this->login_required = true;
    }

    protected function request() {      
        
        $this->set_template('group');

        if ($this->is_post) {
            $db = maindb::getInstance();
            $query = $db->prepare('INSERT INTO posts (user_id, title, body, groups_id, created_at) VALUES (:user_id, :title, :body, :groups_id, :created_at)');
            $query->execute([
                ':user_id' => $_POST['user_id'],
                ':title' => $_POST['title'],
                ':body' => $_POST['body'],
                ':groups_id' => $_GET['id'],
                ':created_at' => date('Y-m-d H:i:s'),
            ]);
            header('Location: /group?id=' . $_GET['id']);
            exit;
        }

        if (empty($_GET['id'])) {
            return false;
        }

        $db = maindb::getInstance();
        $query = $db->prepare('SELECT group_name FROM groups WHERE id = :id');
        $query->execute([':id' => $_GET['id']]);
        $this->data['group_name'] = $query->fetchColumn();
        $this->data['group_id'] = $_GET['id'];

        $query = $db->prepare('SELECT posts.id, posts.title, posts.body, posts.created_at, users.username FROM posts LEFT JOIN users ON users.id = posts.user_id WHERE groups_id = :id AND posts.is_active = 1 ORDER BY created_at DESC');
        $query->execute([':id' => $_GET['id']]);
        $this->data['posts'] = $query->fetchAll();

        $this->data['is_admin'] = $this->is_admin();
    }

    protected function is_auth() {
        $db = maindb::getInstance();
        $query = $db->prepare('SELECT 1 FROM group_members WHERE group_id = :group_id AND user_id = :user_id');
        $query->execute([':group_id' => $_GET['id'], ':user_id' => $_SESSION['user_id']]);
        $result = $query->fetchColumn();
        return !empty($result);
    }

    protected function is_admin() {
        $db = maindb::getInstance();
        $query = $db->prepare('SELECT 1 FROM groups WHERE id = :id AND admin_user_id = :admin_user_id');
        $query->execute([':id' => $_GET['id'], ':admin_user_id' => $_SESSION['user_id']]);
        $result = $query->fetchColumn();
        return !empty($result);
    }
}