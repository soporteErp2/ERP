<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($op) {

		case 'guardarDiasFactura':
			guardarDiasFactura($dias,$id_empresa,$link);
			break;

		case 'guardarNumeroFactura':
			guardarNumeroFactura($idEmpresa,$idSucursal,$resolucionDian,$fechaResolucion,$indiceFactura,$numeroInicial,$numeroFinal,$link);
			break;

		case 'guardarConsecutivosDocumentos':
			guardarConsecutivosDocumentos($jsonData,$filtro_sucursal,$id_empresa,$link);
			break;

		case 'configurar_resolucion':
			configurar_resolucion($id_resolucion,$id_empresa,$mysql);
			break;
	}

	function guardarDiasFactura($dias,$id_empresa,$link){

		//consultamos para determibnar si ya se habia insertado un valor, si se inserto entonces se actualiza y si no se inserta
		$sql="SELECT id FROM ventas_facturas_configuracion WHERE activo=1 AND estado=1 AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($sql,$link);
		$id    = mysql_result($query,0,'id');

		if ($id>0) {
			$sql="UPDATE ventas_facturas_configuracion SET dias_vencimiento = '$dias' WHERE activo=1 AND estado=1 AND id_empresa= '$id_empresa'";
			$query =mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizo la informacion");</script>
				<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!">';
			}
			else{
				echo ' <img src="../../../../temas/clasico/images/BotonesTabs/saved.png" style="margin-top: 3px;" title="Guardado">';
			}
		}
		else
		{
			$sql   = "INSERT INTO ventas_facturas_configuracion (dias_vencimiento,id_empresa) VALUES ('$dias','$id_empresa')";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se guardo la informacion");</script>
				<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!">';
			}
			else{ echo ' <img src="../../../../temas/clasico/images/BotonesTabs/saved.png" style="margin-top: 3px;" title="Guardado">'; }
		}
	}

	function guardarDiasOrdenes($dias,$opc,$id_empresa,$link){

		//consultamos para determibnar si ya se habia insertado un valor, si se inserto entonces se actualiza y si no se inserta
		$sql   = "SELECT id FROM configuracion_vencimiento_documentos WHERE activo=1 AND documento='$opc' AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($sql,$link);
		$id    = mysql_result($query,0,'id');

		if ($id>0) {
			$sql   = "UPDATE configuracion_vencimiento_documentos SET dias_vencimiento = '$dias' WHERE activo=1  AND documento='$opc' AND id_empresa='$id_empresa'";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizo la informacion");</script>
				<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!">';
			}
			else{
				echo ' <img src="../../../../temas/clasico/images/BotonesTabs/ok16.png" style="margin-top: 3px;" title="Guardado">';
			}
		}
		else
		{
			$sql   = "INSERT INTO configuracion_vencimiento_documentos (dias_vencimiento,documento,id_empresa) VALUES ('$dias','$opc','$id_empresa') ";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizo la informacion");</script>
				<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!">';
			}
			else{ echo ' <img src="../../../../temas/clasico/images/BotonesTabs/ok16.png" style="margin-top: 3px;" title="Guardado">'; }
		}
	}

	function guardarNumeroFactura($idEmpresa,$idSucursal,$resolucionDian,$fechaResolucion,$indiceFactura,$numeroInicial,$numeroFinal,$link){
		$idUsuario          = $_SESSION['IDUSUARIO'];
		$nombreUsuario      = $_SESSION['NOMBREUSUARIO'];
		$sqlConfiguracion   = "INSERT INTO ventas_facturas_configuracion (
									consecutivo_resolucion,
									prefijo,
									fecha_resolucion,
									numero_inicial_resolucion,
									numero_final_resolucion,
									id_empresa,
									id_sucursal,
									id_usuario,
									usuario)
								VALUES (
									'$resolucionDian',
									'$indiceFactura',
									'$fechaResolucion',
									'$numeroInicial',
									'$numeroFinal',
									'$idEmpresa',
									'$idSucursal',
									'$idUsuario',
									'$nombreUsuario')";
		$queryConfiguracion = mysql_query($sqlConfiguracion,$link);
		if (!$queryConfiguracion) {
			echo '<script>alert("Error!\nNo se guardo la informacion");</script>
			<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!">';
		}
		else{
			echo'<img src="../../../../temas/clasico/images/BotonesTabs/saved.png" style="margin-top: 3px;" title="Guardado">
				<script>Win_Panel_Sucursal.close();</script>';
		}
	}

	function guardarConsecutivosDocumentos($jsonData,$filtro_sucursal,$id_empresa,$link){

		$array = json_decode($jsonData,true);
		$errores = 0;

		foreach ($array as $documento => $arrayValue ) {

		   $consecutivo=$arrayValue["value"];
		   $digitos=$arrayValue["digitos"];

		   $sqlUpdate  = "UPDATE configuracion_consecutivos_documentos
		   				SET consecutivo = '$consecutivo',
		   					digitos = '$digitos'
		  				WHERE activo=1
		  					AND id_empresa = '$id_empresa'
		  					AND id_sucursal = '$filtro_sucursal'
		  					AND documento = '$documento'
		  					AND modulo = 'venta'";
		   $queryUpdate = mysql_query($sqlUpdate,$link);

		   if(!$queryUpdate){ $errores++; $msgError .= '\n'.$documento; }

	   }
	   if($errores > 0){
			$msgError = ($contError > 1)? 'Error,\nLos siguientes consecutivos no se han almacenado:\n'.$msgError : 'Error,\nEl siguiente consecutivo no se han almacenado:\n'.$msgError;
			echo '<script>alert("'.$msgError.'");</script>'; exit;
		}

		echo '<script>Win_Panel_Sucursal.close();</script>';
	}

	function configurar_resolucion($id_resolucion,$id_empresa,$mysql){
		//CONSULTAR RESOLUCION DE FACTURAS
		$sql = "SELECT 
							VFC.prefijo,
							VFC.consecutivo_resolucion,
							VFC.fecha_resolucion,
							VFC.fecha_final_resolucion,
							VFC.numero_inicial_resolucion,
							VFC.numero_final_resolucion,
							VFC.llave_tecnica,
							E.token
						FROM
							ventas_facturas_configuracion AS VFC
						LEFT JOIN
							empresas AS E ON E.id = VFC.id_empresa
						WHERE
							VFC.activo = 1
						AND
							VFC.id_empresa = $id_empresa
						AND
							VFC.id = $id_resolucion";
		$query = $mysql->query($sql,$mysql->link);
		$prefijo_factura           = $mysql->result($query,0,'prefijo');	
		$consecutivo_resolucion    = $mysql->result($query,0,'consecutivo_resolucion');	
		$fecha_resolucion          = $mysql->result($query,0,'fecha_resolucion');	
		$fecha_final_resolucion    = $mysql->result($query,0,'fecha_final_resolucion');	
		$numero_inicial_resolucion = $mysql->result($query,0,'numero_inicial_resolucion');	
		$numero_final_resolucion   = $mysql->result($query,0,'numero_final_resolucion');
		$llave_tecnica             = $mysql->result($query,0,'llave_tecnica');
		$token                     = $mysql->result($query,0,'token');

		//JSON RESOLUCION FACTURA
		$data = array(
			"type_document_id" => 1,
			"prefix" => "$prefijo_factura",
			"resolution" => "$consecutivo_resolucion",
			"resolution_date" => "2001-01-01",
			"technical_key" => "$llave_tecnica",
			"from" => (int) $numero_inicial_resolucion,
			"to" => (int) $numero_final_resolucion,
			"generate_to_date" => 0,
			"date_from" => "$fecha_resolucion",
			"date_to" => "$fecha_final_resolucion",
		);

		$data = json_encode($data,JSON_PRETTY_PRINT);

		$params                   = [];
		$params['request_url']    = "http://192.168.8.2/apidian2020/public/api/ubl2.1/config/resolution";
		$params['request_method'] = "PUT";
		$params['Authorization']  = "Authorization: Bearer $token";
		$params['data']           = $data;

		$respuesta = curlApi($params);
		$respuesta = json_decode($respuesta,true);

		if(strpos($respuesta["message"], "creada/actualizada con") !== false) {
		  $respuestaAPI = "Resolucion de facturas configurada. \n";
		}

		// CONSULTAR SUCURSALES
		$sql   = "SELECT id FROM empresas_sucursales WHERE activo = 1 AND id_empresa = $id_empresa";
		$query = $mysql->query($sql,$mysql->link);

		while($row = $mysql->fetch_array($query)){
			if(strlen($row['id']) == 1){
        $prefijo_resolucion = "DV0" . $row['id'];
      }
      else{
        $prefijo_resolucion = "DV" . $row['id'];
      }

			//JSON RESOLUCION DEVOLUCION
			$data = array(
				"type_document_id" => 4,
				"from" => 1,
				"to" => 99999999,
				"prefix" => "$prefijo_resolucion"
			);

			$data = json_encode($data,JSON_PRETTY_PRINT);

			$params                   = [];
			$params['request_url']    = "http://192.168.8.2/apidian2020/public/api/ubl2.1/config/resolution";
			$params['request_method'] = "PUT";
			$params['Authorization']  = "Authorization: Bearer $token";
			$params['data']           = $data;

			$respuesta = curlApi($params);
			$respuesta = json_decode($respuesta,true);

			if(strpos($respuesta["message"], "creada/actualizada con") !== false) {
		  	$respuestaAPI .= "Resolucion de notas configurada. \n";
			}
		}

		echo $respuestaAPI;
	}

	function curlApi($params){
		$client = curl_init();
		$options = array(
											CURLOPT_HTTPHEADER     => array('Content-Type: application/json',"$params[Authorization]"),
											CURLOPT_URL            => "$params[request_url]",
											CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
											CURLOPT_RETURNTRANSFER => true,
											CURLOPT_POSTFIELDS     => $params['data'],
											CURLOPT_SSL_VERIFYPEER => false
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

	function quitarTildes($cadena){
		$caracterEspecial = array("\t","\r","\n",chr(160));
		$originales  = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ°ª&º/';
    $modificadas = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyoayo-';
    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
		$cadena = str_replace($caracterEspecial,"",$cadena);
    return utf8_encode($cadena);
	}
?>