<?php

include_once(__DIR__ . '/../library/maindb.php');

use AS\library\maindb;
use AS\infra\page;

class find_groups extends page {
    protected function set_data() {
        $this->login_required = true;
    }

    protected function request() {  
        $this->set_template('find_groups');
        $this->data['pageTitle'] = 'Find Groups';

        if ($this->is_post) {
            $this->handle_post();
        }

        $db = new maindb;
        $query = $db->prepare('
        select groups.group_name, groups.id
            from groups
            WHERE groups.id NOT IN (SELECT group_id FROM group_members WHERE user_id = :user_id)
            AND is_joinable
        ');
        $query->execute([':user_id' => $_SESSION['user_id']]);
        $results = $query->fetchAll();
        
        $this->data['groups'] = $results;
    }

    protected function handle_post() {
        switch ($_POST['action']) {
            case 'join_group':
                $this->join_group();
                break;
        }
    }

    protected function join_group() {
        $db = new maindb;
        $query = $db->prepare('
        insert into group_members (group_id, user_id)
            values (:group_id, :user_id)
        ');
        $query->execute([
            ':group_id' => $_POST['group_id'],
            ':user_id' => $_SESSION['user_id'],
        ]);
        $this->redirect('group?id=' . $_POST['group_id']);
    }
}