<?php
	header('Access-Control-Allow-Origin: *');
	// header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
	// header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	// header("Allow: GET, POST, OPTIONS, PUT, DELETE");
	error_reporting(E_ERROR | E_PARSE);
	require 'apiTerceros.php';
	// OBJETO DE LA CLASE
	$obj    = new apiTerceros();
	$method = $_SERVER['REQUEST_METHOD'];
	$json   = file_get_contents('php://input');
	$data   = json_decode($json,true);
	// $data   = (array) $data;
	switch($method){
		/*
		 * Nota: Por regla el JSON debe estar aramado con comilla doble no simple para los string.
		*/

		case 'GET':
			if(empty($_SERVER['QUERY_STRING'])){
				$result=$obj->index();
				if(count($result)>0){
        			$response['status'] = 202;
        			$response['data']=$result;
				}else{
					$response['status'] = 404;
        			$response['data']=array('failure'=>'No hay informacion para mostrar');
				}
			}else{
				$result=$obj->show($_GET);
				if(count($result)>0){
        			$response['status'] = 202;
        			$response['data']=$result;
				}else{
					$response['status'] = 404;
        			$response['data']=array('failure'=>'No hay informacion para mostrar');
				}
			}
		break;

		case 'POST':
				$result =$obj->store($data);
				if($result['status']){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Informacion registrada');
				}else{
					$response['status'] = 400;
        			$response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
				}
			break;

		case 'PUT':
				$result =$obj->update($data);
				if($result){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Informacion actualizada');
				}else{
					$response['status'] = 500;
        			$response['data']=array('failure'=>'Ha ocurrido un error. La informacion no ha podido ser actualizada.');
				}
			break;

		case 'DELETE':
				$result =$obj->delete($data);
				if($result){
        			$response['status'] = 202;
        			$response['data']=array('success'=>'Informacion eliminada');
				}else{
					$response['status'] = 500;
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