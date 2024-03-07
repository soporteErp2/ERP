<?php

	function curlApi($params){
		$client = curl_init();
		$options = array(
							CURLOPT_HTTPHEADER     => array('Content-Type: application/json',"$params[Authorization]"),
						    CURLOPT_URL            => "$params[request_url]",
						    CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
						    CURLOPT_RETURNTRANSFER => true,
						    CURLOPT_SSL_VERIFYPEER => false,
						    CURLOPT_POSTFIELDS     => $params['data'],
						);
		curl_setopt_array($client,$options);
		$response    = curl_exec($client);
		$curl_errors = curl_error($client);

		if(!empty($curl_errors)){
			$response['status']               = 'failed';
			$response['errors'][0]['titulo']  = curl_getinfo($client);
			$response['errors'][0]['detalle'] = curl_error($client);
		}

		$httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
		curl_close($client);
		return $response;
	}


	$url_api = "https://erp.plataforma.co/api/v1/facturacion_electronica/";
	$params                   = [];
	$params['request_url']    = $url_api;
	$params['request_method'] = "POST";
	$params['Authorization']  = "";
	$params['data']           = '{ "fecha" : "fecha" }';

	$respuesta = curlApi($params);
	var_dump($respuesta);

	// $respuesta = json_decode($respuesta,true);

	// $respuestaFinal['comprobante'] = "Se ejecuto el envio en desarrollo";

	// return $respuestaFinal;
	// echo json_encode( array('status' => true,'data'=> $respuesta) );

	// phpinfo();

?>