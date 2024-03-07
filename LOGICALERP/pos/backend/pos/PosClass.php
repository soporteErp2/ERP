<?php
	error_reporting(E_ERROR | E_PARSE);
	date_default_timezone_set($_SESSION['TIMEZONE']);
	/**
	 * Class Pos clase backend para el pos
	 */
	class Pos extends PosFunctions
	{
		public $id_sucursal;
		public $id_empresa;
		public $mysql;

		function __construct($id_sucursal,$id_empresa,$id_host,$mysql){
			$this->id_sucursal = $id_sucursal;
			$this->id_empresa  = $id_empresa;
			$this->id_host     = $id_host;
			$this->mysql       = $mysql;

			parent::__construct($id_sucursal,$id_empresa,$id_host,$mysql);
		}

		public function getPosHuesped($url,$nit,$like){
			$params['request_url']    = $this->apiHotels['url']."getPosHuesped/$nit/$like"; // ESTA VARIABLE ES HEREADA DE LA CLASE DE FUNCIONES GLOBALES
			$params['request_method'] = "GET";
			echo $this->curlApi($params);
		}

		public function validatePin($pin){
			$sql   = "SELECT pin,token_pos FROM empleados WHERE pin=$pin ";
			$query = $this->mysql->query($sql);
			$pinBd = $this->mysql->result($query,0,'pin');
			$token = $this->mysql->result($query,0,'token_pos');

			if ($pinBd>0) {
				$arrayReturn = array( 'status' => 'success','token'=>$token );
			}
			else{
				$arrayReturn = array( 'status' => 'failed', 'message'=>'El pin digitado no existe en el sistema, intente con otro nuevamente', 'debug'=>$sql);
			}
			echo json_encode($arrayReturn);
		}

		/**
		 * validateToken Validar el token del POS contra el del empleado
		 * @param  String $token token del que envia el pos para validarlo en el sistema
		 * @return Array Json la validacion del token y los datos de usuario
		 */
		public function validateToken($token,$id_sucursal){
			$sql="SELECT
						id,
						tipo_documento_nombre,
						documento,
						nombre1,
						nombre2,
						apellido1,
						apellido2,
						nombre,
						username,
						email_empresa,
						email_personal,
						acceso_sistema,
						id_rol,
						id_empresa,
						rol,
						token,
						token_pos
					FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND token_pos='$token'";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_rol = $row['id_rol'];
				$id_empresa = $row['id_empresa'];
				$sqlPermisos="SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol=$id_rol";
				$queryPermisos=$this->mysql->query($sqlPermisos);
			 	// AND id_permiso=244
				$acceso_caja       = false;
				$permiso_descuento = false;

				while ($rowP=$this->mysql->fetch_array($queryPermisos)) {
					switch ($rowP['id_permiso']) {
						case 244:
							$acceso_caja = true;
							break;
						case 254:
							$permiso_descuento = true;
							break;
					}
				}
				// $rows = $this->mysql->num_rows($queryPermisos);
				// while ($rowPermisos=$this->mysql->fetch_array($queryPermisos)) {
				// 	$arrayPermisos[$rowPermisos['id_permiso']]=true;
				// }
				$datosEmpresa = $this->getInfoEmpresa();
				$arrayRest[] = array(
										'id_host'           => $this->id_host,
										'nitEmpresa'        => $datosEmpresa['documento'],
										'id_sucursal'       => $id_sucursal,
										'id_empleado'       => $row['id'],
										'tipo_documento'    => utf8_encode($row['tipo_documento_nombre']),
										'documento'         => utf8_encode($row['documento']),
										'nombre1'           => utf8_encode($row['nombre1']),
										'nombre2'           => utf8_encode($row['nombre2']),
										'apellido1'         => utf8_encode($row['apellido1']),
										'apellido2'         => utf8_encode($row['apellido2']),
										'nombre'            => utf8_encode($row['nombre']),
										'username'          => utf8_encode($row['username']),
										'email_empresa'     => utf8_encode($row['email_empresa']),
										'email_personal'    => utf8_encode($row['email_personal']),
										'acceso_sistema'    => utf8_encode($row['acceso_sistema']),
										'id_rol'            => $row['id_rol'],
										'rol'               => $row['rol'],
										'acceso_caja'       => $acceso_caja,
										'permiso_descuento' => $permiso_descuento,
										'acceso_caja'       => false,
										'token'             => $row['token'],
										'token_pos'         => $row['token_pos'],
										'sql'               => $sqlPermisos
									);
			}

			$sql="SELECT
						id,
						tipo,
						nombre
						FROM configuracion_cuentas_pago_pos WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {

					if($row['tipo']=='Cheque Cuenta'){
						$tipo='CHEQUE CUENTA';
					}else if($row['tipo']=='Cortesia'){
						$tipo='CORTESIA';
					}else{
						$tipo='GENERAL';
					}

					$arrayMetodos[$tipo][] = array(
										'id'     => utf8_encode($row['id']),
										'tipo'   => utf8_encode($row['tipo']),
										'nombre' => utf8_encode($row['nombre'])
									);
			}

			$sql="SELECT
						id,
						porcentaje,
						nombre,
						requiere_permiso
						FROM configuracion_descuentos_pos WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {

					$arrayDescuentos[] = array(
										'id'         => utf8_encode($row['id']),
										'nombre'     => utf8_encode($row['nombre']),
										'porcentaje' => utf8_encode($row['porcentaje']),
										'permiso'    => utf8_encode($row['requiere_permiso'])
									);
			}

			$sql="SELECT
						id,
						porcentaje,
						nombre
						FROM configuracion_propinas_pos WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {

					$arrayPropinas[] = array(
										'id'         => utf8_encode($row['id']),
										'nombre'     => utf8_encode($row['nombre']),
										'porcentaje' => utf8_encode($row['porcentaje'])
									);
			}

			if (count($arrayRest)>0) {
				// $sql = "UPDATE empleados SET token='$token' WHERE id=".$arrayRest[0]['id_empleado'];
				// $query=$this->mysql->query($sql);
				$arrayResult = array('status' => 'success','metodos_pago'=>$arrayMetodos,'propinas'=>$arrayPropinas,'descuentos'=>$arrayDescuentos, 'user_info'=> $arrayRest);
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>'No existe ningun empleado con ese token' );
			}
			echo json_encode($arrayResult);
		}

		/**
		 * getRestaurantes Consultar restaurantes para visualizar en el pos
		 * @return Array Json con los restaurantes o con mensaje indicando que no hay configurados
		 */
		public function getRestaurantes(){
			$sql = "SELECT id,nombre,id_sucursal,id_bodega,cambia_precio_items
					FROM ventas_pos_secciones
					WHERE activo=1
					AND restaurante = 'Si'
					AND id_sucursal = $this->id_sucursal
					AND id_empresa  = $this->id_empresa ";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayRest[] = array(
										'id'                  => utf8_encode($row['id']),
										'nombre'              => utf8_encode($row['nombre']),
										'id_bodega'           => utf8_encode($row['id_bodega']),
										'cambia_precio_items' => utf8_encode($row['cambia_precio_items'])
									);
			}
			if (count(@$arrayRest)>0) {
				$arrayResult = array('status' => 'success', 'restaurants'=> $arrayRest);
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>'No hay restaurantes configurados','debug'=>$sql);
			}
			echo json_encode($arrayResult);
		}	

		public function getFormasPago()
		{
			$sql   = "SELECT FP.id_forma_pago,FP.valor,CFP.tipo
			 			FROM ventas_pos_formas_pago AS FP
						INNER JOIN configuracion_cuentas_pago_pos AS CFP ON CFP.id = FP.id_forma_pago
						WHERE FP.id_pos= ".$this->id_documento;
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_assoc($query)) {
				$retVal[]=$row;
			}
			return $retVal;
		}

		/**
		 * generateTiquet Generar tiquet de venta
		 * @param  Array $params parametros necesarios para generar la venta
		 * @param  Int 	 $params.id Id del tiquet a generar
		 * @return Array         Array con el estado y el consecutivo del tiquet generado
		 */
		public function generateTiquet($params){

			$arrayResolucion = $this->validateResolucion();
			if ($arrayResolucion['status']==false) {
				$arrayResult = array('status' => 'failed', 'message'=>$arrayResolucion['message'], "debug" =>$arrayResolucion['debug'] );
				echo json_encode($arrayResult);
				return;
		 	}

			$this->documentInfo($params['id'],$arrayResolucion);

			$cuentas_pago = $this->getFormasPago();
			$acum_cheque_cuenta = 0;
			$acum_cortesia = 0;
			// $init = [$acum_cheque_cuenta,$acum_cortesia];
			foreach ($cuentas_pago as $array_val) {
				if ($array_val['tipo']=='Cheque Cuenta') {
					$acum_cheque_cuenta += $array_val['valor'];
					// $retval['Cheque Cuenta'][] = $array_val;
				}
				if ($array_val['tipo']=='Cortesia') {
					$acum_cortesia += $array_val['valor'];
					// $retval['Cortesia'][] = $array_val;
				}
			}
			// $set = [$acum_cheque_cuenta,$acum_cortesia];

			

			// validar si se supera el tope para que entonces se genere una factura electronica
			$tope = $this->get_tope_facturacion(true);
			// if ($this->datosEmpresa['documento'] == "2002") {
			// 	$arrayResult = array('status' => 'failed', 
			// 						'message'=>" tope: $tope subtotal: $this->subtotal_pos acum_cheque_cuenta : $acum_cheque_cuenta acum_cortesia:$acum_cortesia",
			// 						"cuentas_pago"=>$cuentas_pago,
			// 						"vals" => [$init,$set],
			// 						"retval" => $retval	
			// 						);
			// 	echo json_encode($arrayResult);
			// 	return;
			// }
			if ($tope > 0 && (($this->subtotal_pos-$acum_cheque_cuenta)-$acum_cortesia) > $tope) {
				// si no se ha generado la factura electronica entonces crearla y despues la remision
				if (!$this->id_factura>0) {
					$this->generate_electronic_billing();
				}
				else if (!$this->id_entrada_almacen>0) {
					$this->generate_remission();
				}
				return;
			}

			// echo "tope $tope  && subtotal ".$params["subtotalt"];
			
			$this->setAsientos();
			$this->sendCharges();

			// include '../../../funciones_globales/Clases/ClassInventory.php';
			// global $mysql;
			// $objectInventory = new ClassInventory($mysql);
			
			// $sql  = "SELECT SUM(cantidad) AS cantidad_total,
			// 			(
			// 				SELECT costos
			// 				FROM inventario_totales
			// 				WHERE activo=1
			// 				AND id_ubicacion=".$this->id_bodega_ambiente."
			// 				AND id_item=id_item 
			// 				LIMIT 0,1
			// 			) AS costo_unitario,
			// 			SUM(
			// 					(
			// 						(
			// 							SELECT costos
			// 							FROM inventario_totales
			// 							WHERE activo=1
			// 							AND id_ubicacion=".$this->id_bodega_ambiente."
			// 							AND id_item=id_item 
			// 							LIMIT 0,1
			// 						) *	cantidad
			// 					) 
			// 				) AS costo_total,
			// 			id_item
			// 		FROM ventas_pos_inventario_receta
			// 		WHERE id_pos = '".$params['id']."'
			// 		AND activo = 1
			// 		GROUP BY id_item";

			// $params['sqlItems']              = $sql;
			// $params['id_bodega']             = $this->id_bodega_ambiente;
			// $params['event']                 = 'subtract';
			// $params['id_documento']          = $params['id'];
			// $params['nombre_documento']      = "POS";
			// $params['consecutivo_documento'] = $this->consecutivo;
			// $objectInventory->updateInventory($params);

			// echo json_encode(array('status' => true,"consecutivo"=>$this->consecutivo,"cuentas"=>$this->arrayCuentas) );
			$this->updateInventario();
		}

		/**
		 * si se supera el tope de facturacion entonces se debe generar una factura electronica en lugar del pos
		 */
		public function generate_electronic_billing()
		{
			global $SERVER; // from configuracion/configuration
			$query_auth = base64_encode($this->username.":".$this->token.":".$this->datosEmpresa['documento'] );
			
			

			$params["fecha_documento"] 			= $this->fecha_documento;
			$params["fecha_vencimiento"] 		= $this->fecha_documento;
			$params["documento_cliente"] 		= $this->documento_cliente;
			$params["cuenta_pago"] 				= $this->cuenta_pago;
			$params["cuenta_ingreso"]	 		= $this->cuenta_ingreso_colgaap;
			$params["cod_metodo_pago"]			= $this->codigo_metodo_pago_dian;
			$params["id_sucursal"] 				= $this->id_sucursal;
			$params["id_bodega"] 				= $this->id_bodega_ambiente;
			$params["total_factura"] 			= $this->total_pos;
			$params["saldo_restante_factura"] 	= 0;
			$params["items"]		 			= $this->get_items();
			// validar propina
			if($this->valor_propina>0){
				// si tiene propina entonces consultar el item configurado en la propina
				$sql="SELECT
						id,
						porcentaje,
						nombre,
						cod_item_fe
						FROM configuracion_propinas_pos WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_propina";
				$query=$this->mysql->query($sql);
				$item_propina        = $this->mysql->result($query,0,'cod_item_fe');

				// si se tiene ite configurado, se agrega al listado de items del pos
				array_push($params["items"],[
					"codigo" => $item_propina,
					"cantidad" => 1,
					"precio" => $this->valor_propina,
					"causacion_normal" => "true"
				]);
			}
			
			// Cambiamos la url de validacion por la del envio
			$curl_params                   = [];
			$curl_params['request_url']    = "$SERVER/api/v1/facturas/";
			$curl_params['request_method'] = "POST";
			$curl_params['Authorization']  = "Authorization: Basic ".$query_auth;
			$curl_params['data']           = json_encode($params);

			// Consumimos el API y obtenemos sus resultados
			$respuesta = $this->curlApi($curl_params);
			$respuesta = json_decode($respuesta,true);
			//si se creo la factura electronica, entonces crear la remision de venta para la salida de la receta
			if ($respuesta["success"]) {
				$sql = "UPDATE ventas_pos
						SET 
							id_factura = '".$respuesta["id"]."',
							prefijo_factura = '".$respuesta["prefijo"]."',
							numero_factura = '".$respuesta["numero_factura"]."',
							numero_factura_completo = '".$respuesta["numero_factura_completo"]."'
						WHERE id = ".$this->id_documento;
				$query=$this->mysql->query($sql);
				$this->generate_remission();
			}
			else{
				$params["nivel"]          = 1;
				$params["estado"]         = 500;
				$params["detalle_estado"] = "Error en factura electronica: ".json_encode($respuesta['detalle']);
				$this->rollback($params);
			}

		}

		public function generate_remission()
		{
			global $SERVER; // from configuracion/configuration
			$query_auth = base64_encode($this->username.":".$this->token.":".$this->datosEmpresa['documento'] );
			 
			$params["fecha_documento"] 			= $this->fecha_documento;
			$params["fecha_vencimiento"] 		= $this->fecha_documento;
			$params["documento_cliente"] 		= $this->documento_cliente;
			$params["id_sucursal"] 				= $this->id_sucursal;
			$params["id_bodega"] 				= $this->id_bodega_ambiente;
			$params["cod_centro_costos"] 	    = $this->codigoCcos;
			$params["forzar_ccos"] 	    		= "true";			
			$params["items"]		 			= $this->get_recipie();
			
			// Cambiamos la url de validacion por la del envio
			$curl_params                   = [];
			$curl_params['request_url']    = "$SERVER/api/v1/remisiones/";
			$curl_params['request_method'] = "POST";
			$curl_params['Authorization']  = "Authorization: Basic ".$query_auth;
			$curl_params['data']           = json_encode($params);

			// Consumimos el API y obtenemos sus resultados
			$respuesta = $this->curlApi($curl_params);
			$respuesta = json_decode($respuesta,true);			
			if ($respuesta["success"]) {
				$sql = "UPDATE ventas_pos
						SET 
							id_entrada_almacen = '".$respuesta["id"]."',
							consecutivo_entrada_almacen =  '".$respuesta["consecutivo"]."'
						WHERE id = ".$this->id_documento;
				$query=$this->mysql->query($sql);

				$this->set_accounts();
				echo json_encode(array('status' => true,"consecutivo"=>$this->consecutivo,"cuentas"=>$this->arrayCuentas) );
			}
			else{
				$params["nivel"]          = 1;
				$params["estado"]         = 500;
				$params["detalle_estado"] = "Error en remision de venta: ".json_encode($respuesta['detalle']);
				$this->rollback($params);
			}
		}

		public function get_items()
		{
			$sql = "SELECT 
						id, 
						codigo, 
						cantidad, 
						precio_venta AS total, 
						IF(valor_impuesto <> 0, (precio_venta / (1 + (valor_impuesto / 100))), precio_venta) AS subtotal, 
						precio_venta * (valor_impuesto / 100) AS valor_impuesto
					FROM ventas_pos_inventario
					WHERE activo=1
					AND id_pos = '$this->id_documento'
					AND id_sucursal = $this->id_sucursal
					AND id_empresa  = $this->id_empresa ";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$retval[] = [
					"codigo" => $row["codigo"],
					"cantidad" => $row["cantidad"],
					"precio" => $row["subtotal"],
				];
			}
			return $retval;
		}

		public function get_recipie()
		{
			$sql = "SELECT 
						R.codigo, 
						R.cantidad, 
						I.costos
					FROM ventas_pos_inventario_receta AS R 
					LEFT JOIN inventario_totales AS I ON I.id_item = R.id_item
					WHERE R.activo=1
					AND R.id_pos = '$this->id_documento'
					AND R.id_empresa  = $this->id_empresa 
					AND I.id_ubicacion = $this->id_bodega_ambiente";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$retval[] = [
					"codigo" => $row["codigo"],
					"cantidad" => $row["cantidad"],
					"precio" => $row["costos"],
				];
			}
			return $retval;
		}

		public function set_accounts()
		{
			// payment methods and accounts
			$payments_accounts = $this->getCuentasPago();			
			$insertString = NULL;
			foreach ($payments_accounts['colgapp'] as $cuenta => $details) {
				$insertString .= "(
									'".$this->id_documento."',
									'".$this->consecutivo."',
									'POS',
									'Tiquet de venta POS',
									'$this->id_documento',
									'POS',
									'".$this->consecutivo."',
									'".$this->fecha_documento."',
									'".$details['valor']."',
									'0',
									'$cuenta',
									'".$this->id_cliente."',
									'".$this->documento_cliente."',
									'".$this->cliente."',
									'',
									'".$this->id_sucursal."',
									'".$this->id_empresa."'
								),";
				$acum_total += $details['valor'];
			}

			$insertString .= "(
								'".$this->id_documento."',
								'".$this->consecutivo."',
								'POS',
								'Tiquet de venta POS',
								'$this->id_documento',
								'POS',
								'".$this->consecutivo."',
								'".$this->fecha_documento."',
								'0',
								'$acum_total',
								'".$this->cuenta_pago."',
								'".$this->id_cliente."',
								'".$this->documento_cliente."',
								'".$this->cliente."',
								'',
								'".$this->id_sucursal."',
								'".$this->id_empresa."'
							)";
			$sql   = "INSERT INTO asientos_colgaap
							(
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								id_documento_cruce,
								tipo_documento_cruce,
								numero_documento_cruce,
								fecha,
								debe,
								haber,
								codigo_cuenta,
								id_tercero,
								nit_tercero,
								tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa
							)
						VALUES $insertString";
			$query = $this->mysql->query($sql);

			$insertString = NULL;
			$acum_total = NULL;
			foreach ($payments_accounts['niif'] as $cuenta => $details) {
				$insertString .= "(
									'".$this->id_documento."',
									'".$this->consecutivo."',
									'POS',
									'Tiquet de venta POS',
									'$this->id_documento',
									'POS',
									'".$this->consecutivo."',
									'".$this->fecha_documento."',
									'".$details['valor']."',
									'0',
									'$cuenta',
									'".$this->id_cliente."',
									'".$this->documento_cliente."',
									'".$this->cliente."',
									'',
									'".$this->id_sucursal."',
									'".$this->id_empresa."'
								),";
				$acum_total += $details['valor'];
			}


			$insertString .= "(
								'".$this->id_documento."',
								'".$this->consecutivo."',
								'POS',
								'Tiquet de venta POS',
								'$this->id_documento',
								'POS',
								'".$this->consecutivo."',
								'".$this->fecha_documento."',
								'0',
								'$acum_total',
								'".$this->cuenta_pago."',
								'".$this->id_cliente."',
								'".$this->documento_cliente."',
								'".$this->cliente."',
								'',
								'".$this->id_sucursal."',
								'".$this->id_empresa."'
							)";
			$sql   = "INSERT INTO asientos_niif
							(
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								id_documento_cruce,
								tipo_documento_cruce,
								numero_documento_cruce,
								fecha,
								debe,
								haber,
								codigo_cuenta,
								id_tercero,
								nit_tercero,
								tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa
							)
						VALUES $insertString";
			$query = $this->mysql->query($sql);

		}

		public function curlApi($params){
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

		/* consultar el tope de facturacion configurada en erp */
		public function get_tope_facturacion($return=false){
			$sql="SELECT tope FROM ventas_pos_tope_facturacion WHERE id_empresa='$this->id_empresa' LIMIT 0,1";
    		$query=$this->mysql->query($sql);
			
			if ($return) {
				return $this->mysql->result($query,0,'tope');
			}
			echo json_encode(["tope"=>$this->mysql->result($query,0,'tope')]);
		}

		public function get_clients($value)
		{
			$sql="SELECT 
					id,numero_identificacion,nombre 
				FROM terceros 
				WHERE activo = 1 AND id_empresa='$this->id_empresa' AND (numero_identificacion LIKE '%$value%' OR nombre LIKE '%$value%') ";
    		$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$row["numero_identificacion"] = utf8_encode($row["numero_identificacion"]);
				$row["nombre"] = utf8_encode($row["nombre"]);
				// var_dump($row["nombre"]);
				$rows[] = [
							"id" => $row["id"],
							"documento" => utf8_encode($row["numero_identificacion"]),
							"nombre" => utf8_encode($row["nombre"]),
						];
			}
			
			$rows = $rows ? $rows : ["sin resultados"];
			$json = json_encode($rows);
			if (json_last_error()) {
				echo json_encode(["error json"=>"error en caracteres de json"]);
				return;
			}
			// echo json_encode($sql);
			echo $json;
		}

		public function savePayPos($params){
			// IDENTIFICAR SI SE REALIZARA UNA SOLA FACTURA O FACTURA Y CHEQUE CUENTA
			$general      = 0;
			$chequeCuenta = 0;
			$cortesia     = 0;
			foreach ($params["metodoPago"] as  $metodosPago) {
				$general      += ($metodosPago['categoria']=='GENERAL')? 1 : 0 ;
				$chequeCuenta += ($metodosPago['categoria']=='CHEQUE CUENTA')? 1 : 0 ;
				$cortesia     += ($metodosPago['categoria']=='CORTESIA')? 1 : 0 ;
			}

			$sql = "SELECT
						ci.id,
						ci.id_cuenta,
						ci.cantidad,
						ci.cantidad_pendiente,
						ci.id_item,
						ci.id_comanda
					FROM
						ventas_pos_mesas_cuenta_items ci
					WHERE
						ci.id_cuenta = ".$params["mesa"]["id_cuenta"]."
					AND ci.cantidad  > 0
					AND (ci.cantidad > ci.cantidad_pendiente OR ci.cantidad_pendiente IS NULL)";
			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {

				$arrayCuentasItems[$row['id']] = array(
										'id'    => $row['id'],
										'id_cuenta'    => $row['id_cuenta'],
										'cantidad'=> $row['cantidad'],
										'id_comanda'=> $row['id_comanda'],
										'id_item'=> $row['id_item'],
										'cantidad_pendiente'=> $row['cantidad_pendiente']
									);
			}

			$id_reserva        = '';
			$numero_reserva    = '';
			$habitacion        = '';
			$id_cliente        = '0';
			$documento_cliente = '';
			$cliente           = '';

			if(count($params['huespedesSelect'])>0){
				$id_reserva  		= $params['huespedesSelect'][0]['id_reserva'];
				$numero_reserva 	= $params['huespedesSelect'][0]['numero_reserva'];
				$habitacion 		= $params['huespedesSelect'][0]['numero_habitacion'];
				$id_huesped 		= $params['huespedesSelect'][0]['id_comensal'];
				// $id_cliente 		= $params['huespedesSelect'][0]['id'];
				$documento_cliente  = $params['huespedesSelect'][0]['documento_comensal'];
				$cliente			= $params['huespedesSelect'][0]['comensal'];
			}
			else if(count($params['clienteErp'])>0){
				$id_cliente 		= $params['clienteErp'];
				$documento_cliente  = $params['documento_cliente_erp'];
				$cliente			= $params['nombre_cliente_erp'];
			}

			$randomico = $this->randomico();

			$sql="INSERT INTO ventas_pos
					(
						randomico,
						fecha_creacion,
						fecha_documento,
						hora_documento,
						id_caja,
						caja,
						id_mesa,
						codigo_mesa,
						mesa,
						id_seccion,
						seccion,
						id_reserva,
						numero_reserva,
						habitacion,
						id_huesped,
						id_cliente,
						documento_cliente,
						cliente,
						documento_usuario,
						id_usuario,
						usuario,
						id_empresa,
						id_sucursal,
						tipo,
						monto_recibido,
						subtotal_pos,
						total_pos,
						id_descuento,
						nombre_descuento,
						porcentaje_descuento,
						valor_descuento,
						id_propina,
						nombre_propina,
						porcentaje_propina,
						valor_propina
					)
					VALUES
					(
						'".$randomico."',
						'".date("Y-m-d")."',
						'".date("Y-m-d")."',
						'".date("H:i:s")."',
						'".$params["caja"]["id"]."',
						'".$params["caja"]["nombre"]."',
						'".$params["mesa"]["id"]."',
						'".$params["mesa"]["codigo"]."',
						'".$params["mesa"]["nombre"]."',
						'".$params["restaurant"]["id"]."',
						'".$params["restaurant"]["nombre"]."',
						'".$id_reserva."',
						'".$numero_reserva."',
						'".$habitacion."',
						'".$id_huesped."',
						'$id_cliente',
						'".$documento_cliente."',
						'".$cliente."',
						'".$params["user"]["documento"]."',
						'".$params["user"]["id_empleado"]."',
						'".$params["user"]["nombre"]."',
						'$this->id_empresa',
						'$this->id_sucursal',
						'restaurantes',
						'".$params["totalMetodos"]."',
						'".$params["subtotalt"]."',
						'".$params["totalt"]."',
						'".$params["descuentoData"]["id"]."',
						'".$params["descuentoData"]["nombre"]."',
						'".$params["descuentoData"]["porcentaje"]."',
						'".$params["descuentost"]."',
						'".$params["propinaData"]["id"]."',
						'".$params["propinaData"]["nombre"]."',
						'".$params["propinaData"]["porcentaje"]."',
						'".$params["propinat"]."'
					) ";

			$query=$this->mysql->query($sql);
			$id_pos = $this->mysql->insert_id();

			$contMe=0;
			foreach ($params["metodoPago"] as  $metodosPago) {
				if((int)$metodosPago["valor"]>0){
					$contMe++;
					$valor = $metodosPago["valor"];

					if($contMe==1 & (int)$params["totalMetodos"]>(int)$params["totalt"]){
						$valor = (int)$metodosPago["valor"] - ((int)$params["totalMetodos"] - (int)$params["totalt"]);
					//	$valor = ROUND($valor,$this->decimalesMoneda);
					}


					$sql="INSERT INTO ventas_pos_formas_pago
						(
							id_pos,
							id_forma_pago,
							forma_pago,
							valor,
							n_tarjeta,
							n_aprobacion,
							activo
						)
						VALUES
						(
							'".$id_pos."',
							'".$metodosPago["id"]."',
							'".$metodosPago["nombre"]."',
							'".$valor."',
							'".$metodosPago["n_tarjeta"]."',
							'".$metodosPago["n_aprobacion"]."',
							1
						) ";

					$query=$this->mysql->query($sql);
				}
			}

			$validaItems=0;


			foreach ($params["itemsSelect"] as $key => $value) {

				$sql="INSERT INTO ventas_pos_inventario
					(
						id_pos,
						id_cuenta,
						id_cuenta_item,
						id_item,
						codigo,
						nombre,
						cantidad,
						saldo_cantidad,
						precio_venta,
						id_impuesto,
						impuesto,
						id_empresa,
						id_sucursal,
						activo
					)
					VALUES
					(
						'".$id_pos."',
						'".$value["id_cuenta"]."',
						'".$value["id"]."',
						'".$value["id_item"]."',
						'".$value["codigo_item"]."',
						'".$value["nombre_item"]."',
						1,
						'".$value["precio"]."',
						'".$value["precio"]."',
						'".$value["id_impuesto"]."',
						'".$value["nombre_impuesto"]."',
						'$this->id_empresa',
						'$this->id_sucursal',
						1
					) ";

				$query=$this->mysql->query($sql);
				$id_row = $this->mysql->insert_id();

				$cantidad 			= (int)$arrayCuentasItems[$value["id"]]["cantidad"];
				$cantidad_pendiente = (int)$arrayCuentasItems[$value["id"]]["cantidad_pendiente"];
				$cantidad_pendiente++;
				$arrayCuentasItems[$value["id"]]["cantidad_pendiente"]=$cantidad_pendiente;
				if($cantidad<=$cantidad_pendiente){
					$validaItems++;
				}

				$sql="UPDATE ventas_pos_mesas_cuenta_items
					SET
						cantidad_pendiente  = '".$cantidad_pendiente."'
					WHERE id=".$arrayCuentasItems[$value["id"]]['id'];
				$query=$this->mysql->query($sql);

				$itemValidate[] =  array('sdsd'=>$arrayCuentasItems[$value["id"]],'sq' => $sql, 'cantidad'=>$cantidad_pendiente);

				foreach ($value['receta'] as $key => $receta) {
					$sql="INSERT INTO ventas_pos_inventario_receta
								(
									id_pos,
									id_cuenta,
									id_cuenta_item,
									id_item_producto,
									id_item,
									codigo,
									cantidad_unidad_medida,
									nombre,
									cantidad,
									id_empresa,
									activo
								)
								VALUES
								(
									'".$id_pos."',
									'".$receta['id_cuenta']."',
									'".$receta['id_cuenta_item']."',
									'".$id_row."',
									'".$receta["id"]."',
									'".$receta["codigo"]."',
									'".$receta["cantidad"]."',
									'".$receta["nombre"]."',
									'".$receta["cantidad"]."',
									'$this->id_empresa',
									1
								) ";
					$query=$this->mysql->query($sql);
					$sqlRecipies .= $sql;
				}
			}

			// <------------------------------ validacion comanda ------------------------------------>
			$sql = "SELECT
						ci.id,
							SUM(ci.cantidad) as cantidad,
							SUM(ci.cantidad_pendiente) as cantidad_pendiente,
							ci.id_comanda
						FROM
							ventas_pos_mesas_cuenta_items ci
						WHERE
							ci.id_cuenta = ".$params["mesa"]["id_cuenta"]."
						GROUP BY ci.id_comanda
						HAVING cantidad<=cantidad_pendiente";

			$query=$this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {

				$update="UPDATE ventas_pos_comanda
					SET
						estado  = 2
					WHERE id=".$row['id_comanda'];
				$queryUpdate=$this->mysql->query($update);
			}

			// <------------------------------ validacion conabda ------------------------------------>
			if(count($arrayCuentasItems)==$validaItems & $validaItems>0){
				$sql="UPDATE ventas_pos_mesas_cuenta
					SET
						 estado   = 'Cerrada'
					WHERE id=".$params["mesa"]["id_cuenta"];
				$query=$this->mysql->query($sql);
			}

			$result = array('status' => 'success', 'message'=>'','idPos'=>$id_pos , 'sd'=>$itemValidate,"debug"=>$sqlRecipies );

			echo json_encode($result);
			return ;
		}


		/**
		 * printTiquet Imprimir la comanda
		 * @param  Int $id_documento Id del tiquet a imprimir
		 */
		public function printTiquet($id_documento,$debug=''){
			if(file_exists("../../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_factura.php")){
				include("../../../../ARCHIVOS_PROPIOS/empresa_".$_SESSION['ID_HOST']."/panel_de_control/formato_documentos/formato_factura.php");
			}
			else{
				$sql="SELECT
						prefijo,
						consecutivo,
						id_configuracion_resolucion,
						fecha_creacion,
						fecha_generado,
						hora_generado,
						fecha_documento,
						hora_documento,
						caja,
						codigo_mesa,
						mesa,
						seccion,
						numero_reserva,
						habitacion,
						documento_cliente,
						cliente,
						documento_usuario,
						usuario,
						monto_recibido,
						total_pos,
						id_descuento,
						nombre_descuento,
						porcentaje_descuento,
						valor_descuento,
						id_propina,
						nombre_propina,
						porcentaje_propina,
						valor_propina,
						estado,
						tipo
					FROM ventas_pos WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_documento";
				$query=$this->mysql->query($sql);
				$id_configuracion_resolucion = $this->mysql->result($query,0,'id_configuracion_resolucion');

				$prefijo           = $this->mysql->result($query,0,'prefijo');
				$consecutivo       = $this->mysql->result($query,0,'consecutivo');
				$fecha_documento   = $this->mysql->result($query,0,'fecha_documento');
				$hora_documento    = $this->mysql->result($query,0,'hora_documento');
				$caja              = $this->mysql->result($query,0,'caja');
				$numero_reserva    = $this->mysql->result($query,0,'numero_reserva');
				$habitacion        = $this->mysql->result($query,0,'habitacion');
				$documento_cliente = $this->mysql->result($query,0,'documento_cliente');
				$cliente           = utf8_encode($this->mysql->result($query,0,'cliente'));
				$mesa              = $this->mysql->result($query,0,'mesa');
				$seccion           = $this->mysql->result($query,0,'seccion');
				$usuario           = utf8_encode($this->mysql->result($query,0,'usuario'));
				$valor_descuento   = $this->mysql->result($query,0,'valor_descuento');
				$valor_propina     = $this->mysql->result($query,0,'valor_propina');
				$estado            = $this->mysql->result($query,0,'estado');

				$sql="SELECT
						id,
						id_item,
						codigo,
						codigo_barras,
						id_unidad_medida,
						nombre_unidad_medida,
						cantidad_unidad_medida,
						nombre,
						cantidad,
						saldo_cantidad,
						precio_venta,
						costo_inventario,
						id_impuesto,
						impuesto,
						valor_impuesto,
						inventariable
						FROM
							ventas_pos_inventario
						WHERE
							activo = 1
						AND id_empresa = $this->id_empresa
						AND id_pos     = $id_documento";
				$query=$this->mysql->query($sql);
				$contItems = 0;
				while ($row=$this->mysql->fetch_array($query)) {
					$arrayItems[]  = array(
											'id'           => $row['id_item'],
											'nombre'       => utf8_encode($row['nombre']),
											'cantidad'     => ROUND($row['cantidad'],$this->decimalesMoneda),
											'precio_venta' => ROUND($row['precio_venta'],$this->decimalesMoneda),
											'id_impuesto'  => $row['id_impuesto'],
											'impuesto'     => $row['impuesto'],
										);
    				$arrayIdImpuestos[$row['id_impuesto']] = $row['impuesto'];
					$contItems++;
				}

				$whereIdImpuestos   = "id='".implode("' OR id='", array_keys($arrayIdImpuestos))."'";
				$sql="SELECT id,valor,cuenta_venta,cuenta_venta_niif
    					FROM impuestos WHERE activo=1 AND venta='Si' AND id_empresa=$this->id_empresa AND ($whereIdImpuestos)";
	    		$query=$this->mysql->query($sql);
	    		while ($row=$this->mysql->fetch_array($query)) {
	    			$arrayImpuestos[$row['id']] = array(
														'valor'             => $row['valor'],
														'cuenta_venta'      => $row['cuenta_venta'],
														'cuenta_venta_niif' => $row['cuenta_venta_niif'],
	    												);
	    		}

				foreach ($arrayItems as $key => $arrayResult) {

					$subtotal = $arrayResult['precio_venta']*$arrayResult['cantidad'];
					// if ($valor_descuento>0) {
					// 	$subtotal = $subtotal - ($valor_descuento/$contItems);
					// }
					$acumCantidad += $arrayResult['cantidad'];
					// $acumTotal    += ($valor_descuento>0)? $subtotal - ($valor_descuento/$contItems) : $subtotal;
					$acumTotal    += $subtotal;
					$labelSubtotal = number_format($subtotal,$this->decimalesMoneda,",",".");



					// $precio = $arrayItems[$key]['precio'];
    				if ($valor_descuento>0) {
    					$subtotal = $subtotal-($valor_descuento/$contItems);
    				}
					$taxPercent   = ( $arrayImpuestos[$arrayResult['id_impuesto']]['valor'] * 0.01 )+1;
					$neto         = ROUND($subtotal/$taxPercent);
					$acumNeto     += $neto;
					$acumImpuesto += ROUND(($neto*$arrayImpuestos[$arrayResult['id_impuesto']]['valor'])/100);
					if (is_array($arrayItemsPrint[$arrayResult['id']][$arrayResult['precio_venta']])) {
						$arrayItemsPrint[$arrayResult['id']][$arrayResult['precio_venta']]['cantidad'] += $arrayResult['cantidad'];
					}
					else{
						$arrayItemsPrint[$arrayResult['id']][$arrayResult['precio_venta']] = array(
																								'nombre'        => utf8_encode($arrayResult[nombre]),
																								'cantidad'      => $arrayResult[cantidad],
																								'precio_venta'  => $arrayResult['precio_venta'],
																								'labelSubtotal' => $labelSubtotal,
																							);
					}
				}

				foreach ($arrayItemsPrint as $id_item => $arrayItemsPrintResult) {
					foreach ($arrayItemsPrintResult as $precio => $arrayResult) {
						$bodyTable .= "<tr>
											<td>$arrayResult[nombre]</td>
											<td>$arrayResult[cantidad] </td>
											<td>$ ".number_format($arrayResult['precio_venta'],$this->decimalesMoneda,",",".")."</td>
											<td>$ ".number_format($arrayResult['precio_venta']*$arrayResult['cantidad'],$this->decimalesMoneda,",",".")."</td>
										</tr>";
					}
				}

				$sql   = "SELECT
							CP.id,
							VP.id_forma_pago,
							CP.cuenta,
							CP.cuenta_niif,
							VP.valor,
							CP.tipo,
							CP.consecutivo,
							CP.nombre,
							IF(CP.tipo='Cheque Cuenta','1','2') AS tipo_ch
						FROM
							ventas_pos_formas_pago AS VP
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.id_pos = $id_documento
						ORDER BY tipo_ch ASC ";
				$query = $this->mysql->query($sql);
				$tipo = "";
				while ($row=$this->mysql->fetch_array($query)) {
					$formasPago .= "<tr>
										<td>".utf8_encode($row[nombre])."</td>
										<td>$ ".number_format($row[valor],$this->decimalesMoneda,",",".")."</td>
									</tr>";
					switch ($row['tipo']) {
						case 'Cortesia':
							$tipo = ($tipo =="" )? 'Cortesia' : $tipo ;
							break;
						case 'Cheque Cuenta':
							$tipo = ($tipo =="" )? 'Cheque Cuenta' : $tipo ;
							break;
						default:
							$tipo = ($tipo =="" )? "FV" : $tipo ;
							break;
					}
					// if ($row['tipo']=='Cortesia' || $row['tipo']=='Cheque Cuenta') { $tipo=$row['tipo']; }
				}

				if ($tipo=='Cortesia') {
					$head = "<div class='title4' >
								No valido como factura
							</div>
							<div class='title5' >
								Cortesia: $consecutivo
							</div>
							<table class='head'>
								<thead>
									<tr>
										<td><b>Fecha</b></td>
										<td>$fecha_documento $hora_documento</td>
									</tr>
									<tr>
										<td><b>Nit</b></td>
										<td>$documento_cliente</td>
									</tr>
									<tr>
										<td><b>Cliente</b></td>
										<td>$cliente</td>
									</tr>
									<tr>
										<td><b>Mesa</b></td>
										<td>$mesa</td>
									</tr>
									<tr>
										<td><b>Ambiente</b></td>
										<td>$seccion</td>
									</tr>
									<tr>
										<td><b>Mesero</b></td>
										<td>$usuario</td>
									</tr>
									<tr>
										<td colspan='2' class='separator' >&nbsp;</td>
									</tr>
								</thead>
							</table>";
				}
				else if ($tipo=='Cheque Cuenta') {
					$head = "<div class='title4' >
								No valido como factura
							</div>
							<div class='title5' >
								Cheque Cuenta: $consecutivo
							</div>
							<table class='head'>
								<thead>
									<tr>
										<td><b>Fecha</b></td>
										<td>$fecha_documento $hora_documento</td>
									</tr>
									<tr>
										<td><b>Reserva</b></td>
										<td>$numero_reserva</td>
									</tr>
									<tr>
										<td><b>Habitación</b></td>
										<td>$habitacion</td>
									</tr>
									<tr>
										<td><b>Nit</b></td>
										<td>$documento_cliente</td>
									</tr>
									<tr>
										<td><b>Cliente</b></td>
										<td>$cliente</td>
									</tr>
									<tr>
										<td><b>Mesa</b></td>
										<td>$mesa</td>
									</tr>
									<tr>
										<td><b>Ambiente</b></td>
										<td>$seccion</td>
									</tr>
									<tr>
										<td><b>Mesero</b></td>
										<td>$usuario</td>
									</tr>
									<tr>
										<td colspan='2' class='separator' >&nbsp;</td>
									</tr>
								</thead>
							</table>";

					$bodyTotales = "<table class='totales'>
										<tr>
											<td>NETO:</td>
											<td><b>$ ".number_format($acumNeto,0 ,',', '.')."</b></td>
										</tr>
										<tr>
											<td>IPC:</td>
											<td><b>$ ".number_format($acumImpuesto,0,',', '.')." </b></td>
										</tr>
										<tr>
											<td>DESCUENTO:</td>
											<td><b>$ ".number_format($valor_descuento,0 ,',', '.')."</b></td>
										</tr>
										<tr>
											<td>PROPINA:</td>
											<td><b>$ ".number_format($valor_propina,0 ,',', '.')."</b></td>
										</tr>
										<tr>
											<td>TOTAL:</td>
											<td><b>$ ".number_format(($acumTotal+$valor_propina-$valor_descuento),0 ,',', '.')."</b></td>
										</tr>
									</table>
									";

					$bodyFormaPago = "<table class='items'>	
										<thead>
											<tr>
												<td>FORMA PAGO</td>
												<td>VALOR</td>
											</tr>
										</thead>
										$formasPago
										<tr><td>&nbsp;</td></tr>
									</table>";
				}
				else{
					// CONSULTAR LA RESOLUCION DE FACTURACION
					$sql="SELECT
        					numero_resolucion_dian,
							fecha_resolucion_dian,
							consecutivo_inicial,
							consecutivo_final,
							vigencia,
							grandes_contribuyentes
						FROM ventas_pos_configuracion
						WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_configuracion_resolucion";
					$query=$this->mysql->query($sql);
					$numero_resolucion   = $this->mysql->result($query,0,'numero_resolucion_dian');
					$fecha_resolucion    = $this->mysql->result($query,0,'fecha_resolucion_dian');
					$consecutivo_inicial = $this->mysql->result($query,0,'consecutivo_inicial');
					$consecutivo_final   = $this->mysql->result($query,0,'consecutivo_final');
					$vigencia   		 = $this->mysql->result($query,0,'vigencia');
					$grandes_contribuyentes   		 = $this->mysql->result($query,0,'grandes_contribuyentes');
					$head = "<div class='title4' >
								No. Resolucion $numero_resolucion fecha: $fecha_resolucion autorizado desde:  $consecutivo_inicial hasta: $consecutivo_final <br>vigencia: $vigencia meses
							</div>
							<div class='title5' >
								Factura de Venta: $prefijo $consecutivo
							</div>
							<table class='head'>
								<thead>
									<tr>
										<td><b>Fecha</b></td>
										<td>$fecha_documento $hora_documento</td>
									</tr>
									<tr>
										<td><b>Nit</b></td>
										<td>$documento_cliente</td>
									</tr>
									<tr>
										<td><b>Cliente</b></td>
										<td>$cliente</td>
									</tr>
									<tr>
										<td><b>Mesa</b></td>
										<td>$mesa</td>
									</tr>
									<tr>
										<td><b>Ambiente</b></td>
										<td>$seccion</td>
									</tr>
									<tr>
										<td><b>Mesero</b></td>
										<td>$usuario</td>
									</tr>
									<tr>
										<td colspan='2' class='separator' >&nbsp;</td>
									</tr>
								</thead>
							</table>";

					$bodyTotales = "<table class='totales'>
										<tr>
											<td>NETO:</td>
											<td><b>$ ".number_format($acumNeto,0 ,',', '.')."</b></td>
										</tr>
										<tr>
											<td>IPC:</td>
											<td><b>$ ".number_format($acumImpuesto,0,',', '.')." </b></td>
										</tr>
										<tr>
											<td>DESCUENTO:</td>
											<td><b>$ ".number_format($valor_descuento,0 ,',', '.')."</b></td>
										</tr>
										<tr>
											<td>PROPINA:</td>
											<td><b>$ ".number_format($valor_propina,0 ,',', '.')."</b></td>
										</tr>
										<tr>
											<td>TOTAL:</td>
											<td><b>$ ".number_format(($acumTotal+$valor_propina-$valor_descuento),0 ,',', '.')."</b></td>
										</tr>
									</table>
									";

					$bodyFormaPago = "<table class='items'>
										<thead>
											<tr>
												<td>FORMA PAGO</td>
												<td>VALOR</td>
											</tr>
										</thead>
										$formasPago
										<tr><td>&nbsp;</td></tr>
									</table>";

				}

				// print_r($arrayItems); exit;

				$contenido = "<style>
									body{
										font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,sans-serif;
									}
									.title, .title2, title3{
										font-weight   : bold;
										font-size     : 12px;
										width         : 100%;
										text-align    : center;
										margin-bottom : 5px;
									}
									.title2{
										font-weight   : none;
									}
									.title3{
										font-size     : 10px;
									}
									.title4{
										font-size     : 9px;
										font-weight   : none;
									}
									.title5{
										font-size     : 10px;
										text-align    : left;
										padding-top    : 5px;
										padding-bottom : 5px;
									}
									table {
										font-size : 11px;
										width     : 100%;
									}
									.separator{
										border-top     : 1px solid #CCC;
									}
									.footer {
										font-weight   : none;
										width       : 100%;
										font-size   : 10px;
										padding-top : 10px;
										text-align  : left;
									}
									.items{
										border-collapse:collapse;
										font-size     : 10px;
									}
									.items thead tr td{
										font-weight    : bold;
										border-top     : 1px solid #000;
										border-bottom  : 1px solid #000;
										padding-top    : 5px;
										padding-bottom : 5px;
									}
									.head{
										font-size: 10px;
									}
									.firmas{
										margin-top: 20px;
										/*margin-bottom: 20px*/
									}
									.firmas td{
										padding-bottom: 25px;
									}
								</style>
								<div>
									".(($tipo=="FV")?
										"<div class='title' >
											FACTURA DE VENTA POS
										</div>"
										: ""
									)."
									<div class='title' >
										$tipo ".$this->datosEmpresa['nombre']."
									</div>
									<div class='title2' >
										".$this->datosEmpresa['razon_social']."<br/>
										".$this->datosEmpresa['nit']."<br/>
										".(($tipo=="FV")?
											(($grandes_contribuyentes=="si")? "Somos grandes Contribuyentes - Responsables de IVA"
											: "No somos grandes contribuyentes - Responsables de IVA")
										: "")."<br/>
										".$this->datosEmpresa['direccion']."<br/>
									</div>
									<div class='title' >
										*** GRACIAS POR SU VISITA ***
									</di>
									$head
									<div class='title3' >
										LISTADO DE PRODUCTOS CONSUMIDOS
									</di>
									<table class='items'>
										<thead>
											<tr>
												<td>PRODUCTO</td>
												<td>CANT</td>
												<td>V/UNIT</td>
												<td>VALOR</td>
											</tr>
										</thead>
										$bodyTable
										<thead>
											<tr>
												<td>CANT TOTAL</td>
												<td>$acumCantidad</td>
												<td>VALOR</td>
												<td>$ ".number_format($acumTotal,$this->decimalesMoneda,",",".")."</td>
											</tr>
										</thead>
									</table>
									$bodyTotales

									<table class='firmas' >
										<tr>
											<td>Nombre:</td>
											<td>_________________________________________</td>
										</tr>
										<tr>
											<td>Firma:</td>
											<td>_________________________________________</td>
										</tr>
									</table>

									$bodyFormaPago

									<div class='title' >
											GRACIAS POR SU COMPRA
									</di>
									<div class='footer' >
										Se informa a los consumidores que este establecimiento
										de comercio sugiere una propina correspondiente al 10%
										del valor de la cuenta, el cual podra ser aceptado,
										rechazado o modificado por usted, de acuerdo a su
										valoración del servicio prestado.
										Al momento de solicitud la cuenta, indíque a la persona
										que lo atiende que dicho valor sea o no incluido en la
										factura o indíque el valor que quiere dar como propina.
										En caso que tenga algún inconveniente con el cobro podra
										comunicarce con la Línea de Atención al Ciudadano de la
										Superintencia de Industria y Comercio : 592 0404 en
										Bogotá, Para el resto del país, lìnea gratuita nacional:
										018000-910165, para que radique su queja.
									</div>
								</div>";
								// ".($this->fecha_larga($fecha))."
				// if ($debug=='true') {
					// echo $contenido; exit;
				// }
				include("../misc/MPDF54/mpdf.php");

				// echo $id_documento;
				$mpdf = new mPDF(
					"utf-8",  						// mode - default "
					'A6.5',	// format - A4, for example, default "
					12,								// font size - default 0
					"",								// default font family
					'10', // margin_left
					'10', // margin right
					'10', // margin top
					'10', // margin bottom
					3,								// margin header
					10,								// margin footer
				    $orientacion    				// orientacion
				);
				if ($estado==3) {
					$mpdf->SetWatermarkText('ANULADA');
					$mpdf->showWatermarkText = true;
				}
				$mpdf->SetAutoPageBreak(TRUE, 15);
				$mpdf->SetTitle ("Tiquet POS");
				$mpdf->SetAuthor ( "LOGICALSOFT-POS" );
				$mpdf->SetDisplayMode ( "fullpage" );
				$mpdf->SetHeader("");
				$mpdf->SetFooter("By Plataforma USA CORP. EIN: 35-2651281");
				// $mpdf->WriteHTML(utf8_encode($contenido));
				$mpdf->WriteHTML($contenido);
				$mpdf->Output("tiquet_$id_documento.pdf","I");
			}
		}

		/**
		 * logOutToken Actualizar el token para cerrar sesion
		 * @param  Array $params parametros necesarios para actualizar el token
		 * @return Array         Json con la respuesta de la peticion
		 */
		public function logOutToken($params){
			$token = password_hash(date("Y-m-d H:i:s")."PASSWORD_DEFAULT", PASSWORD_DEFAULT );
			$sql   = "UPDATE empleados SET token_pos='$token' WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$params[id_usuario]";
			$query = $this->mysql->query($sql);
			if ($query) {
				$arrayResult = array('status' => 'success', 'message'=>'Se actualizo el token de seguridad' );
			}
			else{
				$arrayResult = array('status' => 'failed', 'message'=>'No se actualizo el token para cerrar la sesion', "debug" =>$sql );
			}

			echo json_encode($arrayResult);
		}


	}

?>