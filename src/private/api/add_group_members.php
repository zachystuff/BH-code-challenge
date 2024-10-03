<?php

include_once(__DIR__ . '/../library/maindb.php');

use AS\library\maindb;
use AS\infra\api;


class add_group_members extends api {

    protected function request() {
        if (!$this->validate_request() || empty($_POST)) {
            $this->response['error'] = 'Invalid request.';
            return;
        }
        $this->add_group_member();
    }

    protected function validate_request() {
        if (empty($_POST['group_id']) || isset($_POST['members'])) {
            return false;
        }
        return true;
    }

    protected function add_group_member() {
        $db = new maindb;
        $group_id = $_POST['group_id'];
        $user_id = $_SESSION['user_id'];
        $invited_id = $_POST['user_id'];

        //Make sure the current user is in the group
        $sql = "SELECT 1 FROM group_members WHERE group_id = :group_id AND user_id = :user_id";
        $query = $db->prepare($sql);
        $query->execute(['group_id' => $group_id, 'user_id' => $user_id]);
        $result = $query->fetchColumn();
        if(!empty($result)) {
            $this->response['error'] = 'You are already in this group.';
            return;
        }

        //Make sure the group memeber isn't already in the group
        $query = $db->prepare('
            select 1 
                from group_members
                where group_id = :group_id
                and user_id = :user_id;');
        $query->execute([':group_id' => $group_id, ':user_id' => $invited_id]);
        $results = $query->fetchColumn();

        if(!empty($results)) {
            $this->response['error'] = 'You are already in this group.';
            return;
        }

        $query = $db->prepare('
            insert into group_members (group_id, user_id)
            values (:group_id, :user_id) on duplicate ;');
        $query->execute([':group_id' => $group_id, ':user_id' => $invited_id]);

        $this->response['success'] = 'You have been added to the group.';
        return;
    }
}