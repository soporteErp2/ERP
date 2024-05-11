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



			if($data['fecha_inicio']<>''){ $where .= " AND VP.fecha_documento BETWEEN '$data[fecha_inicio]' AND '$data[fecha_final]' "; }
			if ($data['tipo']<>'' && $data['tipo']<>'Todos') {
				if ($data['tipo']<>'Facturas') {
					$where .= " AND CP.tipo = '$data[tipo]' ";
				}
				else{
					$where .= " AND (CP.tipo <> 'Cheque Cuenta' AND CP.tipo <> 'Cortesia' )";
				}
			}
			// if($data['tipo']<>''){ $where .= " AND CP.tipo='$data[tipo]' "; }

			if($data['id_sucursal']<>''){ $where .= " AND VP.id_sucursal='$data[id_sucursal]' "; }
			if($data['id_seccion']<>''){ $where .= " AND VP.id_seccion='$data[id_seccion]' "; }
			switch ($data['group_by']) {
				case 'ambiente':
					$sqlRows  = "VP.id_seccion,
									VP.seccion,
									(SELECT codigo_transaccion FROM ventas_pos_secciones WHERE id=id_seccion) AS cod_tx,";
					$group_by = " GROUP BY VP.id_seccion ";
					break;

				default:
					$group_by =" GROUP BY VP.id ";
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

			$group_by =" GROUP BY VP.id ";
			// CONSULTAR LOS COD TX DE CADA AMBIENTE
			// $sql="SELECT id,nombre,codigo_transaccion
			// 		FROM ventas_pos_secciones
			// 		WHERE activo=1 ANd id_empresa=$this->id_empresa";
			// $query=$this->mysql->query($sql,$this->mysql->link);
			// while ($row=$this->mysql->fetch_array($query)) {
			// 	$arrayAmb[$row['id']] = array(
			// 									"nombre"             => $row['nombre'],
			// 									"codigo_transaccion" => $row['codigo_transaccion'],
			// 								);
			// }


			$sql="SELECT
						$sqlRows
						(SUM(VPP.valor)-VP.valor_propina) / 1.08 AS subtotal,
 						((SUM(VPP.valor)-VP.valor_propina) / 1.08) * 0.08 AS impuesto,
 						SUM(VPP.valor) AS valor,
						VP.valor_propina AS valor_propina,
						SUM(VP.valor_descuento) AS valor_descuento,
						VP.estado
					FROM
						ventas_pos AS VP
					INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
					INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
					WHERE
						VP.activo = 1
					AND VPP.activo = 1
					AND (VP.estado = 1 OR VP.estado=2)
					$where
					$group_by ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_assoc($query)) {
				if ($data['group_by']=='ambiente') {
					$arrayTemp[$row['id_seccion']]['id_seccion']      = $row['id_seccion'];
					$arrayTemp[$row['id_seccion']]['seccion']         = $row['seccion'];
					$arrayTemp[$row['id_seccion']]['cod_tx']          = $row['cod_tx'];
					$arrayTemp[$row['id_seccion']]['subtotal']        += $row['subtotal'];
					$arrayTemp[$row['id_seccion']]['impuesto']        += $row['impuesto'];
					$arrayTemp[$row['id_seccion']]['valor']           += $row['valor'];
					$arrayTemp[$row['id_seccion']]['valor_propina']   += $row['valor_propina'];
					$arrayTemp[$row['id_seccion']]['valor_descuento'] += $row['valor_descuento'];
				}
				else{
					$arrayTemp[$row['id']]['id']                = $row['id'];
					$arrayTemp[$row['id']]['consecutivo']       = $row['consecutivo'];
					$arrayTemp[$row['id']]['fecha_documento']   = $row['fecha_documento'];
					$arrayTemp[$row['id']]['seccion']           = $row['seccion'];
					$arrayTemp[$row['id']]['mesa']              = $row['mesa'];
					$arrayTemp[$row['id']]['documento_cliente'] = $row['documento_cliente'];
					$arrayTemp[$row['id']]['cliente']           = $row['cliente'];
					$arrayTemp[$row['id']]['usuario']           = $row['usuario'];
					$arrayTemp[$row['id']]['tipo']              = $row['tipo'];
					$arrayTemp[$row['id']]['subtotal']          += $row['subtotal'];
					$arrayTemp[$row['id']]['impuesto']          += $row['impuesto'];
					$arrayTemp[$row['id']]['valor']             += $row['valor'];
					$arrayTemp[$row['id']]['valor_propina']     += $row['valor_propina'];
					$arrayTemp[$row['id']]['valor_descuento']   += $row['valor_descuento'];
				}
			}

			foreach ($arrayTemp as $key => $arrayResult) {
				$result[] = $arrayResult;
			}
				
			// array_pop($result);
			if ($data['debug']=='true') {
				$result['sql']=$sql;
			}


			// exit;
			// print_r($arrayItems);

			return array('status' => true,'data'=> $result);
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