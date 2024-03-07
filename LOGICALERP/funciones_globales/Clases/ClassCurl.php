<?php

/**
 * ClassCurl Clase para el manejo de apis externas a ERP
 */
class ClassCurl
{

	/**
	 * curl Funcion para consumo de api con curl
	 * @param  Array $params Array con los parametros necesarios para el consumo del api
	 * @param  String 		$params.Authorization Si la peticion lleva un header de autorizacion entonces se envia la cabcera completa
	 * @param  String 		$params.request_url Url del api a consumir
	 * @param  String 		$params.request_method Metodo a usar en el consumo del API (GET,POST,PUT,DELETE)
	 * @param  String 		$params.data Datos a enviar al Api
	 * @return Array 		Lista con la respuesta del consumo del api
	 */
	public function curl($params){
		$client = curl_init();
		$options = array(
							CURLOPT_HTTPHEADER     => array(
														'Content-Type: application/json',
														"$params[Authorization]"),
						    CURLOPT_URL            => "$params[request_url]",
						    CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
						    CURLOPT_RETURNTRANSFER => true,
						    CURLOPT_POSTFIELDS     => $params['data'],
						    CURLOPT_SSL_VERIFYPEER => false,
						);
		curl_setopt_array($client, $options);
		$response = curl_exec($client);
		$curl_errors=curl_error($client);
		if(!empty($curl_errors)){
			$response['status']               = 'failed';
			$response['errors'][0]['titulo']  = curl_getinfo($client) ;
			$response['errors'][0]['detalle'] = curl_error($client);
			// return;
		}
		$httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
		curl_close($client);
		return $response;
	}


}

 ?>