<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Methods: *");
	header("Access-Control-Allow-Headers: *");
	error_reporting(E_ERROR | E_PARSE);

	require 'Pedido_Controller.php';

	
	// OBJETO DE LA CLASE
	$method = $_SERVER['REQUEST_METHOD'];
	$json   = file_get_contents('php://input');
	$data   = json_decode($json,true);
	$obj    = new Pedido_Controller();
	switch($method){
		/*
		 * Nota: Por regla el JSON debe estar aramado con comilla doble no simple para los string.
		*/
		case 'GET':
			if(empty($_SERVER['QUERY_STRING'])){
				$response['data']   = array('failure'=>'No se recibieron parametros para la consulta');
				$response['status'] = 405;
			}else{
				$result=$obj->show($_GET);
				if($result['status']){
					$response['status'] = 200;
					$response['data']   = $result['data'];
				}else{
					$response['status'] = 200;
					$response['data']   = array('failure'=>'No hay informacion para mostrar','detalle'=>$result['detalle']);
				}
			}
		break;

		case 'POST':
			// $response['status'] = 405;
        	// $response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
			$result =$obj->store($data);
			if($result['status']){
				$response['status'] = 202;
				$response['data']=array('success'=>'Informacion registrada', 'pedido' => $result['id_pedido']);
			}else{
				$response['status'] = 400;
				$response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
			}
			break;

		case 'PUT':
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
				// $result =$obj->update($data);
				// if($result['status']){
        		// 	$response['status'] = 202;
        		// 	$response['data']=array('success'=>'Documento actualizado');
				// }else{
				// 	$response['status'] = 400;
        		// 	$response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
				// }
			break;

		case 'DELETE':
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
				// $result =$obj->delete($data);
				// if($result['status']){
        		// 	$response['status'] = 202;
        		// 	$response['data']=array('success'=>'Documento anulado');
				// }else{
				// 	$response['status'] = 400;
        		// 	$response['data']=array('failure'=>'Ha ocurrido un error','detalle'=>$result['detalle']);
				// }
			break;

		default:
			$response['status'] = 405;
        	$response['data']=array('failure'=>'Metodo HTTP no configurado para respuesta.');
			break;
	}



	// print_r($response);
	$obj->apiResponse($response);


?>