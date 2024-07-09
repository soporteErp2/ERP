<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include_once('../../configuracion/conexion.php');

class App {
    public function __construct() {
    }

    public function load_modules(){}
}

header('Content-Type: application/json; charset=utf-8');
$app = new App();

switch ($_GET['method']) {
    case 'load_modules':
        $modules = $app->load_modules();
        echo json_encode($modules);
        break;    
    default:
        echo "Método no válido";
        break;
}