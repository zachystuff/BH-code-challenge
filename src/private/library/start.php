<?php

require_once(dirname(__FILE__). '/../../vendor/autoload.php');

ini_set('display_errors', '1');
ini_set('error_log', '/var/log/php_error_log');
error_reporting(E_ALL &~ E_STRICT);

function connect_twig()
{
    

    $options = array(
        'path' => '../public/twig',
        'cache' => '/tmp/',
        'auto_reload' => true,
    );

    $loader = new \Twig\Loader\FilesystemLoader($options['path']);
    $twig = new \Twig\Environment($loader, [
        'cache' => $options['cache'],
        'auto_reload' => $options['auto_reload'],
        'autoescape' => 'html',
    ]);

    return $twig;
}

function start_twig(string $template_name, array $data, array $response = []){
    $full = array_merge(['data' => $data, '_SESSION' => $_SESSION], $response);
    $twig = connect_twig();
    $template = $twig->load($template_name);
    echo $template->render($full);
}