<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

error_reporting(E_ERROR | E_PARSE);

require_once 'Api_Controller.php';
$obj    = new Api_Controller();
$method = $_SERVER['REQUEST_METHOD'];
$json   = file_get_contents('php://input');
$data   = json_decode($json,true);

switch ($_GET['method']) {
    case 'verify_db':
        $obj->verify_db($data);
        break;
    
    default:
        # code...
        break;
}