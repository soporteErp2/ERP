<?php
	include '../../../misc/ConnectDb/class.ConnectDb.php';
	include '../global/MainClass.php';
	/**
	 *
	 */
	class ApiPos extends MainClass
	{
		public $objConectDB;
		public $mysql;
		public $nit_empresa;
		public $id_ciente;
		public $id_sucursal;
		public $nombre_sucursal;
		public $id_bodega;
		public $nombre_bodega;
		public $id_empresa;
		public $nombre_empresa;
		public $decimales_moneda;
		public $id_usuario;
		public $tipo_doc_usuario;
		public $documento_usuario;
		public $nombre_usuario;
		public $usuarioPermisos;
		public $UsuarioDb    = 'root';
		public $PasswordDb   = 'serverchkdsk';
		public $actionUpdate = false;

		// CONEXION DESARROLLO
		// public $ServidorDb = '192.168.8.2';
		// public $NameDb     = 'erp_bd';

		// CONEXION PRODUCCION
		private $ServidorDb = 'localhost';
		// private $UsuarioDb  = 'root';
		// private $PasswordDb = 'serverchkdsk';
		private $NameDb     = 'erp_acceso';

		function __construct(){
			$this->conexion();
			$this->authentication();
		}

		public function conexion(){
			$this->objConectDB = new ConnectDb(
			                       'MySql',
			                       $this->ServidorDb,
			                       $this->UsuarioDb,
			                       $this->PasswordDb,
			                       $this->NameDb
			                   );
			$this->mysql = $this->objConectDB->getApi();
			$this->link  = $this->mysql->conectar();
			// INICIAR EL CONSTRUCCTOR DE LA CLASE PADRE
			parent::__construct($this->mysql);
		}

		public function authentication(){
			// header('Cache-Control: no-cache, must-revalidate, max-age=0');
			if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']=='' || $_SERVER['PHP_AUTH_PW']=='') {
				$this->apiResponse(array('status' => 401,'data'=> 'Datos de autenticacion incompletos'));
			}
			else{
				$arrayExplode      = explode(":", $_SERVER['PHP_AUTH_PW']);
				$token             = $arrayExplode[0];
				$this->nit_empresa = $arrayExplode[1];
				$sql   = "SELECT id,servidor,bd FROM host WHERE activo=1 AND nit=$this->nit_empresa ";
				$query = $this->mysql->query($sql,$this->mysql->link);
				$rows  = $this->mysql->num_rows($query);
				if ($rows==0) {
					$this->apiResponse(array('status' => 401,'data'=> 'La empresa no existe en el sistema'));
				}
				$this->ServidorDb = $this->mysql->result($query,0,'servidor');
				$this->NameDb     = $this->mysql->result($query,0,'bd');

				$this->conexion();

				$sql="SELECT id,nombre,decimales_moneda FROM empresas WHERE activo=1 AND documento=$this->nit_empresa";
				$query=$this->mysql->query($sql,$this->mysql->link);
				$this->id_empresa       = $this->mysql->result($query,0,'id');
				$this->nombre_empresa   = $this->mysql->result($query,0,'nombre');
				$this->decimales_moneda = $this->mysql->result($query,0,'decimales_moneda');

				$sql="SELECT id,tipo_documento_nombre,documento,nombre,token,id_rol FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND username='$_SERVER[PHP_AUTH_USER]'";
				$query=$this->mysql->query($sql,$this->mysql->link);
				$rows  = $this->mysql->num_rows($query);
				if ($rows==0) {
					$this->apiResponse(array('status' => 401,'data'=> 'El usuario no existe en el sistema'));
				}

				$this->id_usuario        = $this->mysql->result($query,0,'id');
				$this->tipo_doc_usuario  = $this->mysql->result($query,0,'tipo_documento_nombre');
				$this->documento_usuario = $this->mysql->result($query,0,'documento');
				$this->nombre_usuario    = $this->mysql->result($query,0,'nombre');
				$id_rol                  = $this->mysql->result($query,0,'id_rol');

				if ($token<>$this->mysql->result($query,0,'token')){
					$this->apiResponse(array('status' => 401,'data'=> 'Error, token invalido'));
				}

				$sql="SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol=$id_rol";
				$query=$this->mysql->query($sql,$this->mysql->link);
				while ($row=$this->mysql->fetch_array($query)) { $this->usuarioPermisos[$row['id_permiso']] = true;  }


			}
		}

		/**
		 * @api {get} /pos/:fecha_inicio/:fecha_final/:tipo/:id_sucursal/:id_seccion/:group_by Consultar ventas POS
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar las ventas POS del sistema.
		 * @apiName get_pos
		 * @apiGroup POS
		 *
		 *
		 * @apiParam {date} fecha_inicio Fecha inicial para filtrar los documentos
		 * @apiParam {date} fecha_final Fecha final para filtrar los documentos
		 * @apiParam {String= "Cheque Cuenta","Cortesia","Facturas"} tipo Tipo de documento a consultar (No enviar ningun valor si se quiere consultar todo)
		 * @apiParam {Int} id_sucursal Id de la sucursal del documento
		 * @apiParam {Int} id_seccion Id de la seccion o ambiente del documento
		 * @apiParam {String={"ambiente"}} group_by Agrupacion a retonar el Json, vacio para detalle y ambiente para no detallar
		 *
		 *
		 * @apiSuccess {String} consecutivo Consecutivo del documento
		 * @apiSuccess {date} fecha_documento fecha del documento
		 * @apiSuccess {String} seccion Seccion o ambiente donde se realizo la venta
		 * @apiSuccess {String} mesa Mesa del restaurante donde se vendio
		 * @apiSuccess {String} documento_cliente Documento del cliente
		 * @apiSuccess {String} cliente Cliente a quien se realizo la venta
		 * @apiSuccess {String} usuario Usuario que realizo la venta
		 * @apiSuccess {String} tipo Tipo de documento
		 * @apiSuccess {double} subtotal Subtotal sin impuesto del documento
		 * @apiSuccess {double} impuesto Impuesto del documento
		 * @apiSuccess {double} valor Valor total con impuesto del documento
		 * @apiSuccess {double} valor_propina Valor de la propina del documento
		 * @apiSuccess {double} valor_descuento Valor del descuent aplicado al documento
		 * @apiSuccess {String} estado Estado del documento
		 *
		 *
		 * @apiSuccessExample Success-Response:
		 *  HTTP/1.1 200 OK Detallado
		 *  [
		 *
		 *   {
		 *       "consecutivo": "230",
		 *       "fecha_documento": "2019-11-01",
		 *       "seccion": "EVENTOS",
		 *       "mesa": "SALON WYNDHAM",
		 *       "documento_cliente": "1144132739",
		 *       "cliente": "NATALIA  ZAMBRANO VANEGAS",
		 *       "usuario": "MARYANELLYS  MALDONADO VILLARREAL",
		 *       "tipo": "Transferencia Cuentas",
		 *       "subtotal": "510000.000000",
		 *       "impuesto": "40800.000000",
		 *       "valor": "550800.00",
		 *       "valor_propina": "0",
		 *       "valor_descuento": "0",
		 *       "estado": "2"
		 *   },
		 *   {
		 *       "consecutivo": "231",
		 *       "fecha_documento": "2019-11-01",
		 *       "seccion": "RUKUTU PERU",
		 *       "mesa": "MESA 2",
		 *       "documento_cliente": "483800245",
		 *       "cliente": "CARLOS JULIO ZAVALA ",
		 *       "usuario": "MARYANELLYS  MALDONADO VILLARREAL",
		 *       "tipo": "Transferencia Cuentas",
		 *       "subtotal": "23148.148148",
		 *       "impuesto": "1851.851852",
		 *       "valor": "25000.00",
		 *       "valor_propina": "0",
		 *       "valor_descuento": "0",
		 *       "estado": "2"
		 *   }
		 *   ...
		 *  ]
		 *
		 *  HTTP/1.1 200 OK ambiente
		 *  [
		 *   {
		 *   "id_seccion": "1",
		 *   "seccion": "RESTAURANTE PARIS",
		 *   "cod_tx": "2011",
		 *   "subtotal": "848842.592593",
		 *   "impuesto": "67907.407407",
		 *   "valor": "916750.00",
		 *   "valor_propina": "0",
		 *   "valor_descuento": "0",
		 *   "estado": "2"
		 *   },
		 *   ...
		 *  ]
		 *
		 *
		 */
		public function show($data=NULL){
			// fecha_inicio
			// fecha_final
			// tipo
			// id_sucursal
			// id_seccion
			// group_by
				$count = 0;
				foreach ($data as $campo => $valor) { $count += ($valor<>'')? 1 : 0 ; }
				if ($count<=0){ return array('status'=>false,'detalle'=>'No se envio ningun filtro de busqueda'); }
				if (( $data['fecha_inicio']<>'' || $data['fecha_final']<>'' ) && ( $data['fecha_inicio']=='' || $data['fecha_final']=='' ) ){ return array('status'=>false,'detalle'=>'Para consulta en rango de fecha se debe enviar los dos campos (fecha_inicial y fecha_final)'); }

				if($data['fecha_inicio']<>''){ 
					$where .= " AND VP.fecha_documento BETWEEN '$data[fecha_inicio]' AND '$data[fecha_final]' ";
					$whereTempo = " AND VP.fecha_documento BETWEEN '$data[fecha_inicio]' AND '$data[fecha_final]' ";
				}

				if($data['tipo']<>'' && $data['tipo']<>'Todos'){
					if($data['tipo']<>'Facturas'){
						$where .= " AND CP.tipo = '$data[tipo]' ";
					}
					else{
						$whereTempo .= " AND (CP.tipo <> 'Cheque Cuenta' AND CP.tipo <> 'Cortesia' )";
					}
				}

				if($data['id_sucursal']<>''){ $where .= " AND VP.id_sucursal='$data[id_sucursal]' "; }
				if($data['id_seccion']<>''){ $where .= " AND VP.id_seccion='$data[id_seccion]' "; }
				switch ($data['group_by']) {
					case 'ambiente':
						if($data['version'] == 'beta'){
							return array('status' => true,'data'=> $this->getReportBySection($data));
						}

						$sqlRows  = "VP.id_seccion,
										VP.seccion,
										VP.id,
										(SELECT codigo_transaccion FROM ventas_pos_secciones WHERE id=id_seccion) AS cod_tx,";
						break;

					default:
						$sqlRows = "VP.consecutivo,
									VP.id,
									VP.fecha_documento,
									VP.seccion,
									VP.mesa,
									VP.documento_cliente,
									VP.cliente,
									VP.usuario,
									IF(CP.tipo='Cheque Cuenta','Transferencia Cuentas',IF(CP.tipo='Cortesia','Cortesias','Facturas')) AS tipo,";
						break;
				}

				$nombreTempo = "tableroEstadistico$_SESSION[ID_HOST]";

				$sqlTempoTable = "CREATE TEMPORARY TABLE $nombreTempo SELECT
									VPFP.id_pos,
									VPFP.id_forma_pago,
									VPFP.valor AS valor
								FROM
									ventas_pos_formas_pago AS VPFP
								INNER JOIN ventas_pos AS VP ON VP.id = VPFP.id_pos
								INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPFP.id_forma_pago
								WHERE
									VPFP.activo = 1
								AND VP.activo = 1
								AND (VP.estado = 1 OR VP.estado = 2)
								$whereTempo";

				$queryTempoTable = $this->mysql->query($sqlTempoTable,$this->mysql->link);

				$sqlVentasPos = "SELECT SQL_NO_CACHE
													$sqlRows
													SUM(TT.valor) as valor,
													VP.valor_propina,
													VP.valor_descuento,
													VPI.id_item,
													VPI.cantidad,
													VPI.precio_venta,
													VPI.impuesto,
													VPI.valor_impuesto
												FROM
													ventas_pos AS VP
												INNER JOIN $nombreTempo AS TT ON TT.id_pos = VP.id
												INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = TT.id_forma_pago
												INNER JOIN ventas_pos_inventario AS VPI ON VPI.id_pos = VP.id
												WHERE VPI.activo = 1
												AND VP.activo = 1
												AND (VP.estado = 1 OR VP.estado = 2)
												$where
												GROUP BY VPI.id";

				$queryVentasPos = $this->mysql->query($sqlVentasPos,$this->mysql->link);

				$sqlTempoTableDrop = "DROP TEMPORARY TABLE $nombreTempo";
				$queryTempoTable = $this->mysql->query($sqlTempoTableDrop,$this->mysql->link);
				$arrayPos = [];
				while($row=$this->mysql->fetch_array($queryVentasPos)){
					$arrayPos[$row['id']]['contItems']++;
					$arrayPos[$row['id']]['valor_metodo'] 		= $row['valor'];
					$arrayPos[$row['id']]['valor_descuento'] 	= $row['valor_descuento'];
					$arrayPos[$row['id']]['valor_propina'] 		= $row['valor_propina'];
					$arrayPos[$row['id']]['id_seccion'] 		= $row['id_seccion'];
					$arrayPos[$row['id']]['cod_tx'] 			= $row['cod_tx'];
					$arrayPos[$row['id']]['id_pos'] 			= $row['id'];
					$arrayPos[$row['id']]['consecutivo'] 		= $row['consecutivo'];
					$arrayPos[$row['id']]['fecha_documento'] 	= $row['fecha_documento'];
					$arrayPos[$row['id']]['seccion'] 			= $row['seccion'];
					$arrayPos[$row['id']]['mesa'] 				= $row['mesa'];
					$arrayPos[$row['id']]['documento_cliente'] 	= $row['documento_cliente'];
					$arrayPos[$row['id']]['cliente'] 			= $row['cliente'];
					$arrayPos[$row['id']]['usuario'] 			= $row['usuario'];
					$arrayPos[$row['id']]['tipo'] 				= $row['tipo'];

					$arrayPos[$row['id']]['items'][] = array(
																	'id_pos'           	=> $row['id'],
																	'id_item'      		=> $row['id_item'],
																	'cantidad'     		=> $row['cantidad'],
																	'precio_venta' 		=> $row['precio_venta'],
																	'impuesto'     		=> $row['valor_impuesto'],
																);
					$arrTaxes[$row['id_item']] = $row['valor_impuesto'];
				}
							
				foreach ($arrayPos as $id_pos => $arrayResult){
					$acumNeto     = 0;
					$acumImpuesto = 0;
					$acumExento   = 0;
					
					foreach ($arrayResult['items'] as $key => $arrayResultItems){
						$taxPercent = 0;
						$neto       = 0;
						$subtotal = $arrayResultItems['precio_venta']*$arrayResultItems['cantidad'];
						$acumCantidad += $arrayResultItems['cantidad'];
						$acumTotal    += $subtotal;
						if ($arrayResult['valor_descuento']>0) {
							$subtotal = $subtotal-($arrayResult['valor_descuento']/$arrayResult['contItems']);							
						}
						$taxPercent   = ( $arrTaxes[$arrayResultItems['id_item']] * 0.01 )+1;
						$neto         = ROUND($subtotal/$taxPercent);
						$acumNeto     += $neto;
						$acumImpuesto += ROUND(($neto*$arrTaxes[$arrayResultItems['id_item']])/100);
	
						$acumExento += ($arrTaxes[$arrayResultItems['id_item']]==0 || $arrTaxes[$arrayResultItems['id_item']]==null)?  $arrayResultItems['precio_venta']*$arrayResultItems['cantidad'] : 0;
					}

					if ($arrayResult['valor_metodo']<> round($acumNeto+$acumImpuesto+$arrayResult['valor_propina']) && $acumImpuesto>0){
						$acumNeto = ($arrayResult['valor_metodo']-$acumExento-$arrayResult['valor_propina'])/1.08;
						$acumImpuesto = $acumNeto * 0.08;
						$acumNeto += $acumExento;
					}
					
					if($data['group_by'] == 'ambiente'){
						$arrayTemp[$arrayResult['id_seccion']]['id_seccion']      = $arrayResult['id_seccion'];
						$arrayTemp[$arrayResult['id_seccion']]['seccion']         = $arrayResult['seccion'];
						$arrayTemp[$arrayResult['id_seccion']]['cod_tx']          = $arrayResult['cod_tx'];
						$arrayTemp[$arrayResult['id_seccion']]['subtotal']        += $acumNeto;
						$arrayTemp[$arrayResult['id_seccion']]['impuesto']        += $acumImpuesto;
						$arrayTemp[$arrayResult['id_seccion']]['valor_propina']   += $arrayResult['valor_propina'];
						$arrayTemp[$arrayResult['id_seccion']]['valor_descuento'] += $arrayResult['valor_descuento'];
					}
					else{
						$arrayTemp[$arrayResult['id_pos']]['id']                = $arrayResult['id_pos'];
						$arrayTemp[$arrayResult['id_pos']]['consecutivo']       = $arrayResult['consecutivo'];
						$arrayTemp[$arrayResult['id_pos']]['fecha_documento']   = $arrayResult['fecha_documento'];
						$arrayTemp[$arrayResult['id_pos']]['seccion']           = $arrayResult['seccion'];
						$arrayTemp[$arrayResult['id_pos']]['mesa']              = $arrayResult['mesa'];
						$arrayTemp[$arrayResult['id_pos']]['documento_cliente'] = $arrayResult['documento_cliente'];
						$arrayTemp[$arrayResult['id_pos']]['cliente']           = $arrayResult['cliente'];
						$arrayTemp[$arrayResult['id_pos']]['usuario']           = $arrayResult['usuario'];
						$arrayTemp[$arrayResult['id_pos']]['tipo']              = $arrayResult['tipo'];
						$arrayTemp[$arrayResult['id_pos']]['subtotal']          += $acumNeto;
						$arrayTemp[$arrayResult['id_pos']]['impuesto']          += $acumImpuesto;
						$arrayTemp[$arrayResult['id_pos']]['valor_propina']     += $arrayResult['valor_propina'];
						$arrayTemp[$arrayResult['id_pos']]['valor_descuento']   += $arrayResult['valor_descuento'];
					}
					
				}

				foreach($arrayTemp as $key => $value){
					$arrayTemp[$key]['subtotal']        = round($value['subtotal']);
					$arrayTemp[$key]['impuesto']        = round($value['impuesto']);
					$arrayTemp[$key]['valor_propina']   = round($value['valor_propina']);
					$arrayTemp[$key]['valor_descuento'] = round($value['valor_descuento']);
					$arrayTemp[$key]['valor']           = (round($value['subtotal']) + round($value['impuesto']) + round($value['valor_propina']));
				}

				foreach($arrayTemp as $key => $arrayResult){
					$result[] = $arrayResult;
				}

			 return array('status' => true,'data'=> $result);

			}


		/**
		 * getReportBySection reporte por seccion para los hoteles
		 * @param  [type] $data [description]
		 * @return [type]       [description]
		 */
		public function getReportBySection($data)
		{
			
			$sections = $this->getSections();

			if($data['tipo']<>'' && $data['tipo']<>'Todos'){
				if($data['tipo']<>'Facturas'){
					$where .= " AND CP.tipo = '$data[tipo]' ";
				}
				else{
					$where .= " AND (CP.tipo <> 'Cheque Cuenta' AND CP.tipo <> 'Cortesia' )";
				}
			}

			$sql   = "SELECT
							VP.id,
							VP.consecutivo,
							VP.fecha_documento,
							VP.id_seccion,
							VP.mesa,
							VP.documento_cliente,
							VP.cliente,
							VP.usuario,
							IF(CP.tipo='Cheque Cuenta','Transferencia Cuentas',IF(CP.tipo='Cortesia','Cortesias','Facturas')) AS tipo,
							VPP.forma_pago,
							SUM(VPP.valor) AS valor,
							VP.valor_propina,
							VP.valor_descuento,
							VP.estado
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.fecha_documento >= '$data[fecha_inicio]'
						AND VP.fecha_documento <= '$data[fecha_final]'
						AND (VP.estado = 1 OR VP.estado = 2)
						AND VPP.activo = 1
						$where
						GROUP BY VP.id";
			$query = $this->mysql->query($sql);

			while ($row=$this->mysql->fetch_assoc($query)){
				$arrayPos[$row['id']] = array(
											'fecha_documento'   => $row['fecha_documento'],
											'id_seccion'        => $row['id_seccion'],
											'tipo'              => $row['tipo'],
											'consecutivo'       => $row['consecutivo'],
											'documento_cliente' => $row['documento_cliente'],
											'cliente'           => htmlentities($row['cliente']),
											'valor_metodo'      => $row['valor'],
											'valor_propina'     => $row['valor_propina'],
											'valor_descuento'   => $row['valor_descuento'],
											'estado'            => $estado,
										);
			}
			$wherePos = "id_pos='".implode("' OR id_pos='", array_keys($arrayPos))."'";
			$sql = "SELECT
						id_pos,
						id_item,
						cantidad,
						precio_venta,
						valor_impuesto
					FROM ventas_pos_inventario
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($wherePos)";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayPos[$row['id_pos']]['contItems']++;
				$arrayPos[$row['id_pos']]['items'][] = array(
																'id_pos'       => $row['id_pos'],
																'id_item'      => $row['id_item'],
																'cantidad'     => $row['cantidad'],
																'precio_venta' => $row['precio_venta'],
															);
				$arrayItems[$row['id_item']] = $row['valor_impuesto'];
			}
			// var_dump($arrayItems);
			foreach ($arrayPos as $id_pos => $arrayResult){
				$acumNeto     = 0;
				$acumImpuesto = 0;

				foreach ($arrayResult['items'] as $key => $arrayResultItems){
					$subtotal = $arrayResultItems['precio_venta']*$arrayResultItems['cantidad'];
					$acumCantidad += $arrayResultItems['cantidad'];
					$acumTotal    += $subtotal;
					$labelSubtotal = number_format($subtotal,$this->decimalesMoneda,",",".");
    				if ($arrayResult['valor_descuento']>0) {
    					$subtotal = $subtotal-($arrayResult['valor_descuento']/$arrayResult['contItems']);
    				}
					$taxPercent   = ( $arrayItems[$arrayResultItems['id_item']] * 0.01 )+1;
					$neto         = ROUND($subtotal/$taxPercent);
					$acumNeto     += $neto;
					$acumImpuesto += ROUND(($neto*$arrayItems[$arrayResultItems['id_item']])/100);
				}

				if ($arrayResult['valor_metodo']<> round($acumNeto+$acumImpuesto+$arrayResult['valor_propina']) && $acumImpuesto>0){
					$acumNeto = ($arrayResult['valor_metodo']-$arrayResult['valor_propina'])/1.08;
					$acumImpuesto = $acumNeto * 0.08;
				}

				$arrayTemp[$arrayResult['id_seccion']]['id_seccion']      = $arrayResult['id_seccion'];
				$arrayTemp[$arrayResult['id_seccion']]['seccion']         = $sections[$arrayResult['id_seccion']]['nombre'];
				$arrayTemp[$arrayResult['id_seccion']]['cod_tx']          = $sections[$arrayResult['id_seccion']]['codigo_transaccion'];
				$arrayTemp[$arrayResult['id_seccion']]['subtotal']        += $acumNeto;
				$arrayTemp[$arrayResult['id_seccion']]['impuesto']        += $acumImpuesto;
				$arrayTemp[$arrayResult['id_seccion']]['valor_propina']   += $arrayResult['valor_propina'];
				$arrayTemp[$arrayResult['id_seccion']]['valor_descuento'] += $arrayResult['valor_descuento'];

			}
			
			foreach($arrayTemp as $key => $arrayResult){
				$result[] = $arrayResult;
			}

			return $arrayResult;
		}

		/**
		 * getSections consultar todas las secciones (ambientes del pos)
		 * @return [Array] Array con las secciones del pos configuradas en el sistema
		 */
		public function getSections()
		{
			$sql   = "SELECT id,nombre,codigo_transaccion FROM ventas_pos_secciones WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_assoc($query)){
				$retVal[$row['id']] = $row;
			}
			return $retVal;
		}

		public function apiResponse($response){
		    $http_response_code = array(
		        100 => 'Continue',
		        101 => 'Switching Protocols',
		        200 => 'OK',
		        201 => 'Created',
		        202 => 'Accepted',
		        203 => 'Non-Authoritative Information',
		        204 => 'No Content',
		        205 => 'Reset Content',
		        206 => 'Partial Content',
		        300 => 'Multiple Choices',
		        301 => 'Moved Permanently',
		        302 => 'Found',
		        303 => 'See Other',
		        304 => 'Not Modified',
		        305 => 'Use Proxy',
		        306 => '(Unused)',
		        307 => 'Temporary Redirect',
		        400 => 'Bad Request',
		        401 => 'Unauthorized',
		        402 => 'Payment Required',
		        403 => 'Forbidden',
		        404 => 'Not Found',
		        405 => 'Method Not Allowed',
		        406 => 'Not Acceptable',
		        407 => 'Proxy Authentication Required',
		        408 => 'Request Timeout',
		        409 => 'Conflict',
		        410 => 'Gone',
		        411 => 'Length Required',
		        412 => 'Precondition Failed',
		        413 => 'Request Entity Too Large',
		        414 => 'Request-URI Too Long',
		        415 => 'Unsupported Media Type',
		        416 => 'Requested Range Not Satisfiable',
		        417 => 'Expectation Failed',
		        500 => 'Internal Server Error',
		        501 => 'Not Implemented',
		        502 => 'Bad Gateway',
		        503 => 'Service Unavailable',
		        504 => 'Gateway Timeout',
		        505 => 'HTTP Version Not Supported',
		    );

			// header('HTTP/1.1 ' . $response['status'] . ' ' . $http_response_code[$response['status']]);
	    	// header('Content-Type: application/json; charset=utf-8');
			if (is_array($response['data'])) {
				foreach ($response['data'] as $key => $arrayResult) {
					if (is_array($arrayResult)) {
						foreach ($arrayResult as $campo => $valor) {
							if (!is_array($valor)) {
								$response['data'][$key][$campo]=utf8_encode($valor);
							}

						}
					}
				}
			}
			// print_r($_SERVER);
		    $json_response = json_encode($response['data']);
		    echo $json_response;
		    exit;
		}


	} 
