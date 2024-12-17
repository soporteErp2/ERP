<?php

	include '../../../../misc/ConnectDb/class.ConnectDb.php';
	/**
	 * @apiDefine Consultar los tipos de documentos aceptables para facturacion electronica
	 *
	 */
	class apiTipoDocumentos
	{
		private $objConectDB;
		private $mysql;
		private $nit_empresa;
		private $id_sucursal;
		private $nombre_sucursal;
		private $id_bodega;
		private $nombre_bodega;
		private $id_empresa;
		private $nombre_empresa;
		private $id_usuario;
		private $tipo_doc_usuario;
		private $documento_usuario;
		private $usuarioPermisos;
		private $nombre_usuario;
		private $UsuarioDb  = 'root';
		private $PasswordDb = 'serverchkdsk';
		private $arrayCampos = array(
										'digito_verificacion' => 'dv',
										'nombre'              => 'nombre',
										'nombre_comercial'    => 'nombre_comercial',
										'direccion'           => 'direccion',
										'telefono1'           => 'telefono1',
										'telefono2'           => 'telefono2',
										'celular1'            => 'celular1',
										'celular2'            => 'celular2',
										'id_pais'             => 'id_pais',
										'id_departamento'     => 'id_departamento',
										'id_ciudad'           => 'id_ciudad',
										'pagina_web'          => 'web',
										'email'               => 'email',
										'cliente'             => 'tipo_cliente',
										'proveedor'           => 'tipo_proveedor',
										'exento_iva'          => 'exento_iva',
										'primer_nombre'       => 'nombre1',
										'segundo_nombre'      => 'nombre2',
										'primer_apellido'     => 'apellido1',
										'segundo_apellido'    => 'apellido2',
									);

		// CONEXION DESARROLLO
		// private $ServidorDb = '192.168.8.202';
		// private $NameDb     = 'erp_bd';

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
		}

		public function authentication(){
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

				$sql="SELECT id,nombre FROM empresas WHERE activo=1 AND documento=$this->nit_empresa";
				$query=$this->mysql->query($sql,$this->mysql->link);
				$this->id_empresa     = $this->mysql->result($query,0,'id');
				$this->nombre_empresa = $this->mysql->result($query,0,'nombre');

				// $sql="SELECT id,nombre,id_sucursal,sucursal FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$this->id_empresa";
				// $query=$mysql->query($sql,$mysql->link);
				// $nombre_bodega   = $this->mysql->result($query,0,'nombre');
				// $nombre_sucursal = $this->mysql->result($query,0,'sucursal');
				// if ($id_sucursal <> $this->mysql->result($query,0,'id_sucursal') ) {
				// 	$this->apiResponse(array('status' => 401,'data'=> 'La sucursal no existe en el sistema'));
				// }
				// if ($id_bodega <> $this->mysql->result($query,0,'id') ) {
				// 	$this->apiResponse(array('status' => 401,'data'=> 'La bodega no existe en el sistema'));
				// }

				$sql="SELECT id,tipo_documento_nombre,documento,nombre,token,id_rol FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND username='$_SERVER[PHP_AUTH_USER]'";
				$query=$this->mysql->query($sql,$this->mysql->link);
				$rows  = $this->mysql->num_rows($query);
				if ($rows==0) {
					$this->apiResponse(array('status' => 401,'data'=> 'El usuario '.$_SERVER[PHP_AUTH_USER].' no existe en el sistema'));
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
		 * @api {get} /terceros/tipo_documentos Consultar tipos de documento
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar los tipos de documentos aceptables para facturacion electronica
		 * @apiName get_tipo_documentos
		 * @apiGroup Terceros
		 * 
		 * @apiSuccess {Object[]} TiposDocumento Listado con los tipos de documentos
		 * @apiSuccess {String} TiposDocumento.id Codigo DIAN del documento
		 * @apiSuccess {String} TiposDocumento.nombre Abreviacion del tipo de documento
		 * @apiSuccess {String} TiposDocumento.detalle Nombre del tipo de documento
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *     	"status":true,
		 *      "data": [
         *					{
         *					    "id": "13",
         *					    "nombre": "C.C",
         *					    "detalle": "Cedula de Ciudadania"
         *					},
         *					{
         *					    "id": "12",
         *					    "nombre": "T.I",
         *					    "detalle": "Tarjeta de identidad"
         *					}
		 *				]
		 *     }
		 */
		public function index(){
			$sql="SELECT id,nombre,detalle, codigo_tipo_documento_dian 
				  FROM tipo_documento 
				  WHERE activo=1 
				  AND id_empresa=$this->id_empresa
				  AND (codigo_tipo_documento_dian IS NOT NULL OR codigo_tipo_documento_dian = '')";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayTemp[] = array('id' => $row['codigo_tipo_documento_dian'],'nombre'=>$row['nombre'], 'detalle'=> $row['detalle'] );
			}
			return array('status'=>true,'data'=>$arrayTemp);
		}
		public function utf8_encode_recursive($mixed) {
			if (is_array($mixed)) {
				foreach ($mixed as &$valor) {
					$valor = $this->utf8_encode_recursive($valor);
				}
			} elseif (is_string($mixed)) {
				$mixed = utf8_encode($mixed);
			}
			return $mixed;
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
	    	header('Content-Type: application/json; charset=utf-8');
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
			// print_r($response);
			
		    $json_response = json_encode($this->utf8_encode_recursive($response['data']));
		    echo $json_response;
		    exit;
		}

	}