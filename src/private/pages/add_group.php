<?php

include_once(__DIR__ . '/../library/maindb.php');

use AS\library\maindb;
use AS\infra\page;

class add_group extends page {

    protected function request() {  
        $this->set_template('add_group');
        $this->data['pageTitle'] = 'New Group';
        
        if ($this->is_post) {
            if (empty($_POST['new-group-name'])) {
                $this->data['error'] = 'Error: Name is required';
                return;
            }
            if (!empty($_POST['new-group-code']) && $_POST['new-group-code'] !== '1234') {
                $this->data['error'] = 'Error: Code is incorrect';
                return;
            }

            $db = new maindb;
            $query = $db->prepare('
            insert into groups (group_name, admin_user_id)
            values (:group_name, :admin_user_id)
            returning id
            ');
            $query->execute([':group_name' => $_POST['new-group-name'], ':admin_user_id' => $_SESSION['user_id']]);
            $results = $query->fetch(PDO::FETCH_ASSOC);
            $groupId = $results['id'];
    
            $query2 = $db->prepare('
            insert into group_members (group_id, user_id)
            values(:groupid, :userid)
            ');
            $query2->execute([':groupid' => $groupId, ':userid' => $_SESSION['user_id']]);
            
            $this->redirect('my_groups');
        }
    }

    protected function is_auth() {
        return true;
    }
}