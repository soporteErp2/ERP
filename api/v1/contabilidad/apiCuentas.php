<?php
	include '../../../misc/ConnectDb/class.ConnectDb.php';
	/**
	 * @apiDefine Contable Se requieren permisos para el modulo de contabilidad
	 * Para consultar, crear, actualizar o eliminar documentos, se requieren los respectivos permisos del modulo de contabilidad
	 *
	 */
	class ApiCuentas
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
		public $ServidorDb = '192.168.8.2';
		public $NameDb     = 'erp_bd';

		// CONEXION PRODUCCION
		// private $ServidorDb = 'localhost';
		// private $UsuarioDb  = 'root';
		// private $PasswordDb = 'serverchkdsk';
		// private $NameDb     = 'erp_acceso';

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
			// parent::__construct($this->mysql);
		}

		public function authentication(){
			// header('Cache-Control: no-cache, must-revalidate, max-age=0');
			if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']=='' || $_SERVER['PHP_AUTH_PW']=='') {
				$this->apiResponse(array('status' => 'failed','detalle'=> 'Datos de autenticacion incompletos'));
			}
			else{
				$arrayExplode      = explode(":", $_SERVER['PHP_AUTH_PW']);
				$token             = $arrayExplode[0];
				$this->nit_empresa = $arrayExplode[1];
				$sql   = "SELECT id,servidor,bd FROM host WHERE activo=1 AND nit=$this->nit_empresa ";
				$query = $this->mysql->query($sql,$this->mysql->link);
				$rows  = $this->mysql->num_rows($query);
				if ($rows==0) {
					$this->apiResponse(array('status' => 'failed','detalle'=> 'La empresa no existe en el sistema'));
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
					$this->apiResponse(array('status' => 'failed','detalle'=> 'El usuario no existe en el sistema'));
				}

				$this->id_usuario        = $this->mysql->result($query,0,'id');
				$this->tipo_doc_usuario  = $this->mysql->result($query,0,'tipo_documento_nombre');
				$this->documento_usuario = $this->mysql->result($query,0,'documento');
				$this->nombre_usuario    = $this->mysql->result($query,0,'nombre');
				$id_rol                  = $this->mysql->result($query,0,'id_rol');

				if ($token<>$this->mysql->result($query,0,'token')){
					$this->apiResponse(array('status' => 'failed','detalle'=> 'Error, token invalido'));
				}

				$sql="SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol=$id_rol";
				$query=$this->mysql->query($sql,$this->mysql->link);
				while ($row=$this->mysql->fetch_array($query)) { $this->usuarioPermisos[$row['id_permiso']] = true;  }


			}
		}

		/**
		 * @api {get} /contabilidad/:fecha_inicio/:fecha_final/:id_sucursal/:group_by:/centro_costos Consultar asientos
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar las cuentas contables del sistema.
		 * @apiName get_asientos
		 * @apiPermission Contable
		 * @apiGroup Contabilidad
		 *
		 *
		 * @apiParam {date} fecha_inicio Fecha inicial para filtrar la contabilidad
		 * @apiParam {date} fecha_final Fecha final para filtrar la contabilidad
		 * @apiParam {Int} [id_sucursal] Id de la sucursal del documento
		 * @apiParam {Int} [group_by] Group by a aplicar a la consulta, (no esta habilitado para esta version)
		 * @apiParam {Object[]} [centro_costos] Lista con los centros de costos a filtrar de la contabilidad
		 * @apiParam {String} centro_costos.codigo Codigo del centro de costos a aplicar la busqueda
		 *
		 * @apiSuccess {String} consecutivo_documento Consecutivo del documento
		 * @apiSuccess {String} tipo_documento Tipo de documento que genero esa cuenta (FV,FC,RC, ETC.)
		 * @apiSuccess {String} tipo_documento_extendido Nombre detallado del documento que genero esa cuenta
		 * @apiSuccess {String} tipo_documento_cruce Tipo de documento cruzado en esa cuenta
		 * @apiSuccess {String} numero_documento_cruce Consecutivo del documento cruzado en esa cuenta
		 * @apiSuccess {date} fecha Fecha del asiento de contable
		 * @apiSuccess {double} debito Valor en debito para esa cuenta contable
		 * @apiSuccess {double} credito Valor en credito para esa cuenta contable
		 * @apiSuccess {String} codigo_cuenta Codigo de la cuenta contabilizada
		 * @apiSuccess {String} cuenta Nombre de esa cuenta contabilizada
		 * @apiSuccess {int} id_tercero Id del tercero contabilizado
		 * @apiSuccess {String} nit_tercero Documento del tercero contabilizado
		 * @apiSuccess {String} tercero Nombre del tercero contabilizado
		 * @apiSuccess {int} id_sucursal Id de la sucursal donde se genero el movimiento
		 * @apiSuccess {String} sucursal Nombre de la sucursal donde se genero el movimiento
		 * @apiSuccess {String} codigo_centro_costos Codigo del centro de costos que se contabilizo
		 * @apiSuccess {String} centro_costos Nombre del centro de costos que se contabilizo
		 *
		 *
		 * @apiSuccessExample Success-Response:
		 *  HTTP/1.1 200 OK Detallado
		 *  [
		 *
		 *	 {
    	 * 	     "consecutivo_documento": "IR 9759",
    	 * 	     "tipo_documento": "POS",
    	 * 	     "tipo_documento_extendido": "Tiquet de venta POS",
    	 * 	     "tipo_documento_cruce": "POS",
    	 * 	     "numero_documento_cruce": "IR 9759",
    	 * 	     "fecha": "2019-11-01",
    	 * 	     "debito": "517000.00",
    	 * 	     "credito": "0.00",
    	 * 	     "codigo_cuenta": "11050501",
    	 * 	     "cuenta": "CAJA GENERAL",
    	 * 	     "id_tercero": "0",
    	 * 	     "nit_tercero": "",
    	 * 	     "tercero": "",
    	 * 	     "id_sucursal": "2",
    	 * 	     "sucursal": "Sucursal Principal",
    	 * 	     "codigo_centro_costos": "",
    	 * 	     "centro_costos": ""
    	 * 	  },
    	 * 	  {
    	 * 	      "consecutivo_documento": "IR 9762",
    	 * 	      "tipo_documento": "POS",
    	 * 	      "tipo_documento_extendido": "Tiquet de venta POS",
    	 * 	      "tipo_documento_cruce": "POS",
    	 * 	      "numero_documento_cruce": "IR 9762",
    	 * 	      "fecha": "2019-11-01",
    	 * 	      "debito": "443587.00",
    	 * 	      "credito": "0.00",
    	 * 	      "codigo_cuenta": "11100501",
    	 * 	      "cuenta": "BANCOLOMBIA 77113085851",
    	 * 	      "id_tercero": "0",
    	 * 	      "nit_tercero": "45534674",
    	 * 	      "tercero": "MADELEINE DEL CARMEN GONZALEZ MORELOS",
    	 * 	      "id_sucursal": "2",
    	 * 	      "sucursal": "Sucursal Principal",
    	 * 	      "codigo_centro_costos": "",
    	 * 	      "centro_costos": ""
    	 * 	  },
		 *   ...
		 *  ]
		 *
		 *
		 */
		public function show($data=NULL){
			// fecha_inicio
			// fecha_final
			// id_sucursal
			// group_by
			// centro_costos
			// var_dump($data);
			$count = 0;
			foreach ($data as $campo => $valor) { $count += ($valor<>'')? 1 : 0 ; }
			if ($count<=0){ return array('status'=>false,'detalle'=>'No se envio ningun filtro de busqueda'); }
			if (( $data['fecha_inicio']<>'' || $data['fecha_final']<>'' ) && ( $data['fecha_inicio']=='' || $data['fecha_final']=='' ) ){ return array('status'=>false,'detalle'=>'Para consulta en rango de fecha se debe enviar los dos campos (fecha_inicial y fecha_final)'); }

			if($data['fecha_inicio']<>''){ $where .= " AND fecha BETWEEN '$data[fecha_inicio]' AND '$data[fecha_final]' "; }
			if($data['id_sucursal']<>''){ $where .= " AND id_sucursal='$data[id_sucursal]' "; }
			if($data['custonWhere']<>''){ $where .= base64_decode($data['custonWhere']); }
			// if($data['group_by']<>''){ $group_by = " GROUP BY $data[group_by] "; }
			// else{
				$group_by = " GROUP BY id_tercero,codigo_cuenta,codigo_centro_costos,tipo_documento";
			// }

			if (!empty($data['centro_costos'])) {
				foreach ($data['centro_costos'] as $key => $arrayResult) {
					$whereCcos .= ($whereCcos == '')? " codigo_centro_costos=$arrayResult[codigo] " : " OR codigo_centro_costos=$arrayResult[codigo] " ;
				}

				$where .= " AND ($whereCcos)";
			}

			if ($data['asientos']<>'' && ($data['asientos']<> 'Local' && $data['asientos']<> 'Niif') ) {
				return array('status'=>false,'detalle'=>'Valor invalido para el campo asientos');
			}
			else if ($data['asientos']<>'') {
				$tabla_asientos = ($data['asientos']=='Local')? "asientos_colgaap" : "asientos_niif" ;
			}
			else{
				$tabla_asientos = "asientos_colgaap";
			}
			// echo $data['custonWhere']." ---- <br><br>";
			// echo base64_decode($data['custonWhere'])." ---- <br><br>";
			$sql="SELECT
						consecutivo_documento,
                        tipo_documento,
                        tipo_documento_extendido,
                        tipo_documento_cruce,
                        numero_documento_cruce,
                        fecha,
                        SUM(debe) AS debito,
                        SUM(haber) AS credito,
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        id_sucursal,
                        sucursal,
                        codigo_centro_costos,
                        centro_costos
                    FROM
                        $tabla_asientos
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $where
                        $group_by";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($result[]=$this->mysql->fetch_assoc($query));
			array_pop($result);


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
		    $json_response = json_encode($response);
		    echo $json_response;
		    exit;
		}


	}