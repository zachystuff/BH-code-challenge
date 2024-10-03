<?php

include_once(__DIR__ . '/../library/maindb.php');

use AS\library\maindb;
use AS\infra\page;

class my_groups extends page {
    protected function set_data() {
        $this->login_required = true;
    }

    protected function request() {  
        $this->set_template('my_groups');
        $this->data['pageTitle'] = 'My Groups';

        $db = new maindb;
        $query = $db->prepare('
        select groups.group_name, groups.id
            from groups
            left join group_members on groups.id=group_members.group_id
            where group_members.user_id = :id;
        ');
        $query->execute([':id' => $_SESSION['user_id']]);
        $results = $query->fetchAll();
        
        $this->data['groups'] = $results;
    }
}