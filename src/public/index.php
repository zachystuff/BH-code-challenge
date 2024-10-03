<?php

require_once(__DIR__ . "/../private/library/page.php");
require_once(__DIR__ . "/../private/library/adminpage.php");
require_once(dirname(__FILE__). '/../vendor/autoload.php');

session_start(); //session_destroy(); To logout someone

$is_api = str_starts_with($_SERVER['REQUEST_URI'], '/api/');

$magic_number = $is_api ? 5 : 1;

$request = substr($_SERVER['REQUEST_URI'], $magic_number);
$request = explode('?', $request)[0];
if (empty($request)) {
    $request = 'home';
}

$whitelist = [ //Key is the url and value is the classname of file
    '404' => 'not_found',
    'login' => 'login',
    'shop' => 'shop',
    'signup' => 'signup',
    'profile' => 'profile',
    'logout' => 'logout',
    'my_groups' => 'my_groups',
    'add_group' => 'add_group',
    'home' => 'home',
    'group' => 'group',
    'group_settings' => 'group_settings',
    'find_groups' => 'find_groups',

// Admin Pages
    'admin' => 'admin',
    'admin/user' => 'UserAdminPage',
    'admin/group' => 'GroupAdminPage',
    'admin/post' => 'PostAdminPage',
];

$api_whitelist = [ //Key is the url and value is the classname of file
    'add_group_members' => 'add_group_members',
    'add_group' => 'add_group',
];

$not_found = false;
if (!$is_api && empty($whitelist[$request])) {
    $not_found = true;
}

if ($is_api && empty($api_whitelist[$request])) {
    $not_found = true;
}

if ($is_api) {
    $classname = $api_whitelist[$request]?? 'not_found';
} else {
    $classname = $whitelist[$request]?? 'not_found';
}


// if (!is_subclass_of($classname, 'infra\page')) {
//     $not_found = true;
// }
if (!$not_found) {
    if ($is_api) {
        require_once(__DIR__ . "/../private/api/$request.php");
    } else {
        require_once(__DIR__ . "/../private/pages/$request.php");
    }
   
    $page = new $classname;
    $page->run();
} else {
    if ($is_api) {
        echo json_encode(['error' => 'API endpoint not found.']);
    } else {
        require_once(__DIR__ . "/../private/pages/404.php");
        $page = new not_found; 
        $page->run();
    }
}