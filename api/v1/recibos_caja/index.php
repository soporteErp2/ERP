<?php
	// error_reporting(E_ERROR | E_WARNING | E_PARSE);
	header('Access-Control-Allow-Origin: *');
	// header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	// header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	// header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	error_reporting(E_ERROR | E_PARSE);
	require 'apiRecibosCaja.php';
	// OBJETO DE LA CLASE
	$obj    = new apiRecibosCaja();
	$method = $_SERVER['REQUEST_METHOD'];
	$json   = file_get_contents('php://input');
	$data   = json_decode($json);
	// $data   = (array) $data;
	// echo $_SERVER['REQUEST_METHOD'];
	switch($method){
		/*
		 * Nota: Por regla el JSON debe estar aramado con comilla doble no simple para los string.
		*/

		case 'GET':
			if(empty($_SERVER['QUERY_STRING'])){
				$response['data']   = array('failure'=>'No se recibieron filtros para la consulta');
				$response['status'] = 404;
			}else{
				$result=$obj->show($_GET);
				if($result['status']){
					$response['status'] = 202;
					$response['data']   = $result['data'];
				}else{
					$response['status'] = 400;
					$response['data']   = array('failure'=>'No hay informacion para mostrar','detalle'=>$result['detalle']);
				}
			}
		break;

		case 'POST':
				$result =$obj->store($data);
				if($result['status']){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Informacion registrada','consecutivo'=>$result['consecutivo']);
				}else{
					$response['status'] = 400;
        			$response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
				}
			break;

		case 'PUT':
				$result =$obj->update($data);
				if($result['status']){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Documento actualizado');
				}else{
					$response['status'] = 400;
        			$response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
				}
			break;

		case 'DELETE':
				$result =$obj->delete($data);
				if($result['status']){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Documento anulado');
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