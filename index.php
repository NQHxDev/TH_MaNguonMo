<?php

require_once 'App/Config/Database.php';
require_once 'App/Models/CategoryModel.php';
require_once 'App/Models/ProductModel.php';

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

$controllerName = isset($url[0]) && $url[0] !== '' ? ucfirst($url[0]) . 'Controller' : 'DefaultController';
$action = isset($url[1]) && $url[1] !== '' ? $url[1] : 'index';

if (!file_exists('App/Controllers/' . $controllerName . '.php')) {
   die('Controller Not Found');
}

require_once 'App/Controllers/' . $controllerName . '.php';

$controller = new $controllerName();


if (!method_exists($controller, $action)) {
   die('Action Not Found');
}

call_user_func_array([$controller, $action], array_slice($url, 2));
