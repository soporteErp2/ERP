<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: *");
	header("Access-Control-Allow-Headers: *");
	error_reporting(E_ERROR | E_PARSE);
	// print_r($_FILES);
	// return;
	require 'ApiArchivosAdjuntos.php';
	$method = $_SERVER['REQUEST_METHOD'];
	$obj    = new ApiArchivosAdjuntos();
    switch($method){

		case 'POST':
            $result =$obj->store($data);
            if($result['status']){
                $response['status'] = 200;
                $response['data']=array('success'=>'Archivo adjunto almacenado '.$result['detalle']);
            }else{
                $response['status'] = 400;
                $response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
            }
			break;

		default:
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
			break;
	}



	// print_r($response);
	$obj->apiResponse($response);


?>