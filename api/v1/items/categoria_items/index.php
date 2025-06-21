<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: *");
	header("Access-Control-Allow-Headers: *");
	error_reporting(E_ERROR | E_PARSE);

	require 'apiCategoriaItems.php';
	// OBJETO DE LA CLASE
	$method = $_SERVER['REQUEST_METHOD'];
	$json   = file_get_contents('php://input');
	$data   = json_decode($json,true);
	$obj    = new apiCategoriaItems();
	switch($method){
		/*
		 * Nota: Por regla el JSON debe estar aramado con comilla doble no simple para los string.
		*/
		case 'GET':

			$result=$obj->show();
			if($result['status']){
				$response['status'] = 200;
				$response['data']   = $result['data'];
			}else{
				$response['status'] = 200;
				$response['data']   = array('failure'=>'No hay informacion para mostrar','detalle'=>$result['detalle']);
			}
			
		break;

		case 'POST':
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
			break;

		case 'PUT':
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
			break;

		case 'DELETE':
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
			break;

		default:
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
			break;
	}



	// print_r($response);
	$obj->apiResponse($response);


?>