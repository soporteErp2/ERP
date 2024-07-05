<?php
// Your code here!
// API para enviar el JSON a la DIAN
				
				
				function curlApi($params){
        			$client = curl_init();
        			$options = array(
						CURLOPT_HTTPHEADER     => array('Content-Type: application/json',"$params[Authorization]"),
						CURLOPT_URL            => "$params[request_url]",
						CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_POSTFIELDS     => $params['data'],

        			);
        			curl_setopt_array($client,$options);

        			//$certificate_location = '/opt/lampp/etc/ssl.crt/5bc79829f350d71c.pem';
        			//curl_setopt($client, CURLOPT_SSL_VERIFYHOST, $certificate_location);
			        //curl_setopt($client, CURLOPT_SSL_VERIFYPEER, $certificate_location);

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
        		
        		$url_api = "https://web.facse.net:444/api/Comunicacion/Comprobante";

				// Creamos los parametros para consumir la API
				$params                   = [];
				$params['request_url']    = $url_api;
				$params['request_method'] = "POST";
				$params['Authorization']  = "";
				$params['data']           = "hola";
        		
        		$hola = curlApi($params);
        		var_dump($hola);
        		
?>
