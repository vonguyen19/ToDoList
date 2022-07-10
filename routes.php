<?php
$controllers = array(
  'work' => ['index', 'add', 'store' , 'error'],
);

if (!array_key_exists($controller, $controllers) || !in_array($action, $controllers[$controller])) {
  $controller = 'work';
  $action = 'error';
}

include_once('Controllers/' . $controller . 'Controller.php');
$klass = str_replace('_', '', ucwords($controller, '_')) . 'Controller';
$controller = new $klass;
$controller->$action();