<?php

use AS\infra\page;
use AS\library\maindb;

class group_settings extends page {
    protected function set_data() {
        $this->login_required = true;
    }

    protected function request() {      
        $this->set_template('group_settings');

        if ($this->is_post) {
            $this->handle_post();
            $this->redirect('group_settings?id=' . $_GET['id']);
        }

        $db = maindb::getInstance();
        $query = $db->prepare('SELECT group_name, is_joinable FROM groups WHERE id = :id');
        $query->execute([':id' => $_GET['id']]);
        $details = $query->fetch();
        $this->data['group_name'] = $details['group_name'];
        $this->data['is_joinable'] = $details['is_joinable'];
        $this->data['group_id'] = $_GET['id'];

        $query = $db->prepare('SELECT username, user_id FROM group_members INNER JOIN users ON users.id = group_members.user_id WHERE group_id = :group_id');
        $query->execute([':group_id' => $_GET['id']]);
        $this->data['group_members'] = $query->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function is_auth() {
        if (empty($_GET['id'])) {
            return false;
        }

        // $this->set_redirect('group?id=' . $_GET['id']);

        $db = maindb::getInstance();
        $query = $db->prepare('SELECT 1 FROM groups WHERE id = :id AND admin_user_id = :admin_user_id');
        $query->execute([':id' => $_GET['id'], ':admin_user_id' => $_SESSION['user_id']]);
        $result = $query->fetchColumn();
        return !empty($result);
    }

    protected function handle_post() {
        switch ($_POST['action']) {
            case 'update_group_details':
                $this->update_group_details();
                break;
            case 'remove_group_member':
                $this->remove_group_members();
                break;
            case 'add_group_member':
                $this->add_group_member_from_username();
                break;
        }
    }

    protected function update_group_details() {
        $db = maindb::getInstance();
        $query = $db->prepare('UPDATE groups SET group_name = :group_name, is_joinable = :is_joinable WHERE id = :id');
        $query->execute([':group_name' => $_POST['group_name'], ':is_joinable' => $_POST['is_joinable']?? 0, ':id' => $_POST['group_id']]);
        $this->redirect('group_settings?id=' . $_GET['id']);
    }

    protected function remove_group_members() {
        $db = maindb::getInstance();
        $query = $db->prepare('DELETE FROM group_members WHERE group_id = :group_id AND user_id = :user_id');
        $query->execute([':group_id' => $_POST['group_id'], ':user_id' => $_POST['group_member']]);
        $this->redirect('group_settings?id=' . $_GET['id']);
    }

    protected function add_group_member_from_username() {
        $db = maindb::getInstance();
        $query = $db->prepare('SELECT id FROM users WHERE username = :username');
        $query->execute([':username' => $_POST['username']]);
        $user_id = $query->fetchColumn();
        if (empty($user_id)) {
            $this->redirect('group_settings?id=' . $_GET['id'] . '&error=1');
        }
        $query = $db->prepare('INSERT INTO group_members (group_id, user_id) VALUES (:group_id, :user_id)');
        $query->execute([':group_id' => $_POST['group_id'], ':user_id' => $user_id]);
        $this->redirect('group_settings?id=' . $_GET['id']);
    }
}