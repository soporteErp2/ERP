<?php
	include '../../../misc/ConnectDb/class.ConnectDb.php';

	/**
	 *
	 */
	class apiNotasContables
	{
		private $objConectDB;
		private $mysql;
		private $nit_empresa;
		private $id_ciente;
		private $id_sucursal;
		private $nombre_sucursal;
		private $id_bodega;
		private $nombre_bodega;
		private $id_empresa;
		private $nombre_empresa;
		private $decimales_moneda;
		private $id_usuario;
		private $tipo_doc_usuario;
		private $documento_usuario;
		private $nombre_usuario;
		private $usuarioPermisos;
		private $UsuarioDb    = 'root';
		private $PasswordDb   = 'serverchkdsk';
		private $actionUpdate = false;

		// CONEXION DESARROLLO
		//private $ServidorDb = '192.168.8.2';
		//private $NameDb     = 'erp_bd';

		// CONEXION PRODUCCION
		private $ServidorDb = 'localhost';
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
				$this->apiResponse(array('status' => 401,'data'=> array('failure' => 'Ha ocurrido un error', "detalle"=>'Datos de autenticacion incompletos') ));
			}
			else{
				$arrayExplode      = explode(":", $_SERVER['PHP_AUTH_PW']);
				$token             = $arrayExplode[0];
				$this->nit_empresa = $arrayExplode[1];
				$sql   = "SELECT id,servidor,bd FROM host WHERE activo=1 AND nit=$this->nit_empresa ";
				$query = $this->mysql->query($sql,$this->mysql->link);
				$rows  = $this->mysql->num_rows($query);
				if ($rows==0) {
					$this->apiResponse(array('status' => 401,'data'=> array('failure' => 'Ha ocurrido un error', "detalle"=>'Datos de autenticacion incompletos') ));
				}
				$this->ServidorDb = $this->mysql->result($query,0,'servidor');
				$this->NameDb     = $this->mysql->result($query,0,'bd');

				$this->conexion();

				$sql="SELECT id,nombre,decimales_moneda FROM empresas WHERE activo=1 AND documento=$this->nit_empresa";
				$query=$this->mysql->query($sql,$this->mysql->link);
				$this->id_empresa       = $this->mysql->result($query,0,'id');
				$this->nombre_empresa   = $this->mysql->result($query,0,'nombre');
				$this->decimales_moneda = $this->mysql->result($query,0,'decimales_moneda');

				$sql="SELECT id,tipo_documento_nombre,documento,nombre,token,id_rol FROM empleados WHERE activo=1 AND username='$_SERVER[PHP_AUTH_USER]'";
				$query=$this->mysql->query($sql,$this->mysql->link);
				$rows  = $this->mysql->num_rows($query);
				if ($rows==0) {
					$this->apiResponse(array('status' => 401,'data'=> array('failure' => 'Ha ocurrido un error', "detalle"=>'El usuario no existe en el sistema') ));
				}

				$this->id_usuario        = $this->mysql->result($query,0,'id');
				$this->tipo_doc_usuario  = $this->mysql->result($query,0,'tipo_documento_nombre');
				$this->documento_usuario = $this->mysql->result($query,0,'documento');
				$this->nombre_usuario    = $this->mysql->result($query,0,'nombre');
				$id_rol                  = $this->mysql->result($query,0,'id_rol');

				if ($token<>$this->mysql->result($query,0,'token')){
					$this->apiResponse(array('status' => 401,'data'=> array('failure' => 'Ha ocurrido un error', "detalle"=>'Error, token invalido') ));
				}

				$sql="SELECT id_permiso FROM empleados_roles_permisos WHERE id_rol=$id_rol";
				$query=$this->mysql->query($sql,$this->mysql->link);
				while ($row=$this->mysql->fetch_array($query)) { $this->usuarioPermisos[$row['id_permiso']] = true;  }


			}
		}

		/**
		 * @api {post} /notas_contables/ Crear Nota Contable
		 * @apiVersion 1.0.0
		 * @apiDescription Registrar una nota contable en el sistema
		 * @apiName store_notas_contables
		 * @apiPermission Contable
		 * @apiGroup Notas Contables
		 *
		 * @apiParam {Date} fecha_documento Fecha del documento (Y-M-D)
		 * @apiParam {int} [consecutivo] Consecutivo a insertar para el documento
		 * @apiParam {String} [tipo] Nombre del tipo de documento (Lista de tipos del panel de control)
		 * @apiParam {String} documento_tercero Numero del documento del tercero del documento
		 * @apiParam {Int} id_sucursal Id de la sucursal del documento (Consultar en el panel de control del sistema)
		 * @apiParam {String} [observacion] Observacion general del documento
		 * @apiParam {Object[]} cuentas Lista con las cuentas del documento
		 * @apiParam {String} cuentas.cuenta Cuenta contable a causar en el documento
		 * @apiParam {String} [cuentas.documento_tercero] Tercero de la cuenta a causar (Si no se envia, por defecto toma el tercero principal), si relaciona una factura debe ser el cliente de la factura
		 * @apiParam {Double} cuentas.debito Valor en debito para la cuenta (Si no tiene valor enviar 0)
		 * @apiParam {Double} cuentas.credito Valor en credito para la cuenta (Si no tiene valor enviar 0)
		 * @apiParam {String} [cuentas.observacion] Observacion de la cuenta
		 *
		 * @apiSuccess {200} success  informacion registrada
		 * @apiSuccess {200} consecutivo  Consecutivo del documento
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "informacion registrada",
		 *        "consecutivo": "Consecutivo del documento, Ej 110",
		 *     }
		 * @apiErrorExample Error-Response:
		 * HTTP/1.1 400 Bad Response
		 * {
		 *  "failure": "Ha ocurrido un error",
		 *   "detalle": "detalle del o los errores"
		 * }
		 *
		 * @apiError failure Ha ocurrido un error
		 * @apiError detalle
		 *     HTTP/1.1 400 Bad Response
		 *     {
		 *     	"failure":"Ha ocurrido un error",
		 *     "detalle": "detalle del error"
		 *     }
		 */
		public function store($data=NULL){
			global $json;
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion para registrar'); }
			if ($this->usuarioPermisos[84]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para crear notas contables'); }

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			// CAMPOS OBLIGATORIOS
			// id_sucursal *
			// fecha_documento *			//
			// <> contabilidad * colgaap_niif - colgaap - niif
			// tipo
			// documento_tercero *
			// observacion
			// cuentas => array *
			// 			cuenta *
			// 			documento_tercero
			// 			debito *
			// 			credito *
			// 			observaciones
			// 			cuenta_pago --

			if ($data['fecha_documento']=='' || !isset($data['fecha_documento']) ){ $arrayError[] = "El campo fecha_documento es obligatorio"; }
			if ($data['documento_tercero']=='' || !isset($data['documento_tercero'])){ $arrayError[] = "El campo documento tercero  es obligatorio"; }
			$arrayTercero = $this->getTercero($data['documento_tercero']);
			$this->id_cliente = $arrayTercero['id'];
			if ($arrayTercero==false) {  $arrayError[] = "El tercero no existe en el sistema"; }
			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id sucursal es obligatorio"; }
			$arrayUbicaciones = $this->getSucursales();
			if (!array_key_exists("$data[id_sucursal]",$arrayUbicaciones['sucursales'])) { $arrayError[] = "La sucursal no existe"; }
			$this->id_sucursal = $data['id_sucursal'];
			$arrayPuc = $this->getPuc();
			if (empty($data['cuentas'])){ $arrayError[] = "El objeto con las cuentas es obligatorio"; }
			$acumuladoDebito    = 0;
			$acumuladoCredito   = 0;
			foreach ($data['cuentas'] as $key => $arrayCuentas) {
				// print_r($arrayCuentas);
				// if (gettype($arrayCuentas)=='object') {
				// 	$arrayCuentas=get_object_vars($arrayCuentas);
				// }
				if ($arrayCuentas['cuenta']=='') { $arrayError[] = "El campo cuenta para el documento es obligatorio, posicion $key"; }
				if ($arrayCuentas['debito']=='' || !is_numeric($arrayCuentas['debito']) ) { $arrayError[] = "El campo debito para la cuenta es obligatorio y debe ser numerico, si no tiene valor enviar cero en 0, cuenta $arrayCuentas[cuenta] "; }
				if ($arrayCuentas['credito']=='' || !is_numeric($arrayCuentas['credito']) ) { $arrayError[] = "El campo credito para la cuenta es obligatorio y debe ser numerico, si no tiene valor enviar cero en 0, cuenta $arrayCuentas[cuenta] "; }
				// if ($arrayCuentas['debito']>0 && $arrayCuentas['credito']>0 ) { $arrayError[] = "No se permite que el debito y credito tenga valor en un solo registro, adicione la misma cuenta en otra posicion con el otro valor, cuenta $arrayCuentas[cuenta] "; }
				// if ($arrayCuentas['debito']==0 && $arrayCuentas['credito']==0 ) { $arrayError[] = "No se permite que el debito y credito tenga valor en cero, uno de los dos debe tener un valor mayor a cero, cuenta $arrayCuentas[cuenta] "; }

				if (!array_key_exists("$arrayCuentas[cuenta]",$arrayPuc)) { $arrayError[] = "La cuenta $arrayCuentas[cuenta] no existe en el sistema"; }
				if ($arrayCuentas['documento_tercero']<>'') {
					$arrayTerceroCuentas = $this->getTercero($arrayCuentas['documento_tercero']);
					if ($arrayTerceroCuentas==false) {  $arrayError[] = "El tercero $arrayCuentas[documento_tercero] para la cuenta $arrayCuentas[cuenta] no existe en el sistema"; }
				}
				else{ $id_tercero = $this->id_cliente; }


					$id_documento_cruce      = '';
					$tipo_documento_cruce    = '';
					$prefijo_documento_cruce = '';
					$numero_documento_cruce  = '';

				$acumuladoDebito  += $arrayCuentas['debito'];
				$acumuladoCredito += $arrayCuentas['credito'];
				$arrayCuentas['observacion'] = addslashes($arrayCuentas['observacion']);

				$valueInsertCuentas .= "(
										'id_documento_insert',
										".$arrayPuc[$arrayCuentas['cuenta']]['id'].",
										$arrayCuentas[debito],
										$arrayCuentas[credito],
										'$arrayTerceroCuentas[id]',
										'$tipo_documento_cruce ',
										'$id_documento_cruce',
										'$prefijo_documento_cruce',
										'$numero_documento_cruce',
										'$arrayCuentas[observacion]',
										'$this->id_empresa'
										),";
			}
			// return array('status'=>false,'detalle'=> $arrayError);
			$acumuladoDebito  = ROUND($acumuladoDebito,$this->decimales_moneda);
			$acumuladoCredito = ROUND($acumuladoCredito,$this->decimales_moneda);
			if($acumuladoDebito <> $acumuladoCredito){
				$arrayError[] = "El saldo total debito y credito son diferentes <b>Debito:</b>$acumuladoDebito <b>Credito:</b>$acumuladoCredito <b>Diferncia:</b>".($acumuladoDebito-$acumuladoCredito);
			}

			$arrayTipoNota = $this->getTiposNotas();
			if ($data['tipo']<>'') {
				if (!array_key_exists("$data[tipo]",$arrayTipoNota)) { $arrayError[] = "El tipo de nota <b>$data[tipo]</b> enviado no existe, creelo desde el panel de control"; }
				$id_tipo_nota     = $arrayTipoNota[$data['tipo']]['id'];
				$tipo_nota        = $arrayTipoNota[$data['tipo']]['descripcion'];
				$consecutivo      = $arrayTipoNota[$data['tipo']]['consecutivo'];
				$consecutivo_niif = $arrayTipoNota[$data['tipo']]['consecutivo_niif'];
			}
			else{
				$id_tipo_nota     = $arrayTipoNota['NOTA GENERAL']['id'];
				$tipo_nota        = $arrayTipoNota['NOTA GENERAL']['descripcion'];
				$consecutivo      = $arrayTipoNota['NOTA GENERAL']['consecutivo'];
				$consecutivo_niif = $arrayTipoNota['NOTA GENERAL']['consecutivo_niif'];
			}

			if ($data['consecutivo']>0 && $data['tipo']==''){
				$arrayError[] = "Si envia un consecutivo para el documento debe enviar tambien el tipo";
			}

			if ($data['consecutivo']>0) {
				$validateConsecutivo = $this->validateConsecutivo($data['consecutivo'],$tipo_nota);
				if ($validateConsecutivo['status']==false){
					$arrayError[] = $validateConsecutivo["message"];
				}
				else{
					$consecutivo = $data['consecutivo'];
				}
			}

			if ($consecutivo<=0 || $consecutivo=='') {
				$arrayError[] = "El documento no tiene consecutivo, por tanto no se puede insertar! ";
			}
			// else{
			// 	$consecutivo = $this->getConsecutivo();
			// }

			if ($consecutivo==false) {  $arrayError[] = "Error interno del sistema al consultar el consecutivo de la nota contable"; }

			// if ($data['contabilidad']<>"colgaap_niif" AND $data['contabilidad']<> "colgaap" AND $data['contabilidad']<> "niif") {
			// 	$arrayError[] = "El campo contabilidad tiene un valor invalido";
			// }
			if (!empty($arrayError)) { return array('status'=>false,'detalle'=> $arrayError); }
			$json                = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']);
			$fecha_actual = date("Y-m-d");
			$random = $this->random();
			$sql   = "INSERT INTO nota_contable_general
							(
								random,
								consecutivo,
								consecutivo_niif,
								sinc_nota,
								id_empresa,
								id_sucursal,
								fecha_nota,
								fecha_finalizacion,
								id_tipo_nota,
								tipo_nota,
								id_tercero,
								codigo_tercero,
								numero_identificacion_tercero,
								tipo_identificacion_tercero,
								tercero,
								id_usuario,
								cedula_usuario,
								usuario,
								observacion,
								estado,
								json_api
							)
                        VALUES
                        	(
                        		'$random',
								'$consecutivo',
								'$consecutivo',
								'colgaap_niif',
								'$this->id_empresa',
								'$this->id_sucursal',
								'$data[fecha_documento]',
								'$fecha_actual',
								'$id_tipo_nota',
								'$tipo_nota',
								'$arrayTercero[id]',
								'$arrayTercero[codigo]',
								'$arrayTercero[numero_identificacion]',
								'$arrayTercero[tipo_identificacion]',
								'$arrayTercero[nombre]',
								'$this->id_usuario',
								'$this->documento_usuario',
								'$this->nombre_usuario ',
								'$data[observacion]',
								'1',
								'$json'
                        	)";
        	$query = $this->mysql->query($sql,$this->mysql->link);
        	if ($query){
        		$sql="SELECT id FROM nota_contable_general WHERE activo=1 AND id_empresa=$this->id_empresa AND random='$random' ";
        		$query=$this->mysql->query($sql);
        		$id_documento = $this->mysql->result($query,0,'id');
        		// echo $id_recibo;

				$valueInsertCuentas = substr($valueInsertCuentas, 0, -1);
				$valueInsertCuentas = str_replace("id_documento_insert", $id_documento, $valueInsertCuentas);

        		$sql="INSERT INTO nota_contable_general_cuentas
        				(
							id_nota_general,
							id_puc,
							debe,
							haber,
							id_tercero,
							tipo_documento_cruce,
							id_documento_cruce,
							prefijo_documento_cruce,
							numero_documento_cruce,
							observacion,
							id_empresa
        				)
        				VALUES $valueInsertCuentas";
        		$query=$this->mysql->query($sql,$this->mysql->link);
				// return array('status'=>true,'consecutivo' => $consecutivo, "id_documento" =>$id_documento );
        		// return array('status'=>200,'consecutivo'=>$consecutivo);
        		if (!$query) {
        			$this->rollBack($id_documento,1," activo=0");

        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos';
					$arrayError[1]="Error numero: ".$this->mysql->errno();
	    			$arrayError[2]="Error detalle: ".$this->mysql->error();
	    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
	        		return array('status'=>false,'detalle'=>$arrayError);
        		}
        		else{
					$arrayDatos['consecutivo']     = $consecutivo;
					$arrayDatos['tipo_nota']       = $tipo_nota;
					$arrayDatos['id_tercero']      = $arrayTercero['id'];
					$arrayDatos['tercero']         = $arrayTercero['nombre'];
					$arrayDatos['fecha_documento'] = $data['fecha_documento'];

        			$contabilizacion = $this->setContabilidad($id_documento,$arrayDatos);
        			if ($contabilizacion['status']==false) { return array('status'=>false,'detalle'=>$contabilizacion['detalle']); }

        			// $docsCruce = $this->setSaldosDocumentosCruce($id_documento);
        			// if ($docsCruce['status']==false) { return array('status'=>false,'detalle'=>$docsCruce['detalle']); }

        			$sql="UPDATE tipo_nota_contable SET consecutivo=$consecutivo+1
								WHERE activo   = 1
								AND id_empresa = $this->id_empresa
								AND id         = $id_tipo_nota
								";
        			$query=$this->mysql->query($sql,$this->mysql->link);
        			if (!$query) {
        				$this->rollBack($id_documento,2," activo=0 ");
        				$arrayError[0]='Se produjo un error actualizar ';
						$arrayError[1]="Error numero: ".$this->mysql->errno();
		    			$arrayError[2]="Error detalle: ".$this->mysql->error();
		    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
		        		return array('status'=>false,'detalle'=>$arrayError);
        			}

					return array('status'=>200,'consecutivo'=>$consecutivo);
        		}
        	}
        	else{
    			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

        		return array('status'=>false,'detalle'=>$arrayError);
        	}
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
		    //
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
		    $json_response = json_encode($response['data']);
		    echo $json_response;
		    exit;
		}


		public function getTiposNotas(){
			$sql   = "SELECT
						id,
						codigo,
						descripcion,
						consecutivo,
						consecutivo_niif,
						documento_cruce
					FROM tipo_nota_contable
					WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayReturn[$row['descripcion']] = array(
															'id'               => $row['id'],
															'descripcion'      => $row['descripcion'],
															'consecutivo'      => $row['consecutivo'],
															'consecutivo_niif' => $row['consecutivo_niif'],
														);
			}
			return $arrayReturn;
		}

		/**
		 * validateConsecutivo Validar que el consecutivo recibido no este ya ingresado
		 * @param  Int $consecutivo Consecutivo de la nota a validar
		 * @return Array            Lista conla respuesta de la validacion
		 */
		public function validateConsecutivo($consecutivo,$tipo_nota){
			$sql="SELECT id FROM nota_contable_general
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_sucursal=$this->id_sucursal
					AND consecutivo=$consecutivo
					AND tipo_nota = '$tipo_nota'
					AND (estado=0 OR estado=1 )";
			$query=$this->mysql->query($sql);
			if ($this->mysql->result($query,0,'id')>0){
				return array('status' => false, "message" => "Ya existe una nota contable <b>$tipo_nota</b> con el consecutivo  <b>$consecutivo</b>" );
			}
			else{ return array( 'status' => true ); }
		}

		/**
		 * getTerceros Consultar el cliente del sistema
		 * @param  String $documento Documento del cliente a consultar
		 * @return int Id del cliente a consultar
		 */
		public function getTercero($documento){
			$sql="SELECT id,tipo_identificacion,numero_identificacion,nombre,codigo
					FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa AND numero_identificacion='$documento'";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$datos['id']                    = $this->mysql->result($query,0,'id');
			$datos['codigo']                = $this->mysql->result($query,0,'codigo');
			$datos['tipo_identificacion']   = $this->mysql->result($query,0,'tipo_identificacion');
			$datos['numero_identificacion'] = $this->mysql->result($query,0,'numero_identificacion');
			$datos['nombre']                = $this->mysql->result($query,0,'nombre');

			return ($datos['id']>0)? $datos : false;
		}

		/**
		 * [getSucursales Consultar las bodegas y sucursales de la empresa]
		 * @return [array] [Array con las bodegas y sucursales]
		 */
		public function getSucursales() {
			$sql="SELECT id,nombre,id_sucursal,sucursal FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayTemp['bodegas'][$row['id']]             = $row['nombre'];
				$arrayTemp['sucursales'][$row['id_sucursal']] = $row['sucursal'];
			}
			return $arrayTemp;
		}

		/**
		 * getPuc Consultar las cuentas contables de la empresa
		 * @return Array Array con los items de la sucursal y bodega de la empresa
		 */
		public function getPuc(){
			$sql="SELECT
					id,
					cuenta,
					descripcion,
					cuenta_niif,
					centro_costo,
					cuenta_cruce,
					tipo
				FROM puc
				WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($row=$this->mysql->fetch_assoc($query)){
				$arrayTemp[$row['cuenta']] = array(
												"id"           => $row['id'],
												"cuenta"       => $row['cuenta'],
												"descripcion"  => $row['descripcion'],
												"cuenta_niif"  => $row['cuenta_niif'],
												"centro_costo" => $row['centro_costo'],
												"cuenta_cruce" => $row['cuenta_cruce'],
												"tipo"         => $row['tipo'],
											);
			}
			return $arrayTemp;
		}


		/**
		 * setAsientos Contabilizar la factura en norma local
		 * @param Int $id_recibo        Id de la factura a causar
		 * @param Array $arrayCuentaPago Array con la informacion de la cuenta de pago
		 * @return Array Resultado del proceso {status: true o false},{detalle: en caso de ser error se detallara en este campo}
		 */
		public function setContabilidad($id_documento,$arrayDatos){
			$sql   = "SELECT
						id,
						cuenta_puc,
						cuenta_niif,
						tipo_documento_cruce,
						id_documento_cruce,
						prefijo_documento_cruce,
						numero_documento_cruce,
						id_tercero,
						debe AS debito,
						haber AS credito,
						id_centro_costos,
						observacion
					FROM nota_contable_general_cuentas
					WHERE id_nota_general='$id_documento' AND activo=1 ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$acumuladoDebito     = 0;
			$acumuladoCredito    = 0;
			$valuesInsertNiif    = "";
			$valuesInsertColgaap = "";
			while ($row=$this->mysql->fetch_array($query)) {
				if ($row['cuenta_niif']==0 || $row['cuenta_niif']=='') {
					$this->rollBack($id_documento,1);
					return array('status' => false, 'detalle'=> "La cuenta $row[cuenta_puc] no tiene una cuenta niif configurada" );
				}

				$row['observacion']   = addslashes($data['observacion']);
				$id_documento_cruce     = ($row['id_documento_cruce']=='' || $row['id_documento_cruce']==0)? "$id_documento" : "$row[id_documento_cruce]";
				$tipo_documento_cruce   = ($row['tipo_documento_cruce']=='' || $row['tipo_documento_cruce']==0)? "NCG" : "$row[tipo_documento_cruce]";
				$numero_documento_cruce = ($row['numero_documento_cruce']=='' || $row['numero_documento_cruce']==0)? "$arrayDatos[consecutivo]" : "$row[numero_documento_cruce]";
				$id_tercero             = ($row['id_tercero']=='' || $row['id_tercero']==0)? $arrayDatos['id_tercero'] : $row['id_tercero'] ;

				$acumuladoDebito     += $row['debito'];
				$acumuladoCredito    += $row['credito'];

				$valuesInsertColgaap .= "(
											'$id_documento',
											'$arrayDatos[consecutivo]',
											'NCG',
											'$arrayDatos[tipo_nota]',
											'$id_documento_cruce',
											'$tipo_documento_cruce',
											'$numero_documento_cruce',
											'$row[debito]',
											'$row[credito]',
											'$row[cuenta_puc]',
											'$this->id_sucursal',
											'$this->id_empresa',
											'$id_tercero',
											'$arrayDatos[fecha_documento]',
											'$row[id_centro_costos]',
											'$row[observaciones]'
										),";

				$valuesInsertNiif .= "(
											'$id_documento',
											'$arrayDatos[consecutivo]',
											'NCG',
											'$arrayDatos[tipo_nota]',
											'$id_documento_cruce',
											'$tipo_documento_cruce',
											'$numero_documento_cruce',
											'$row[debito]',
											'$row[credito]',
											'$row[cuenta_niif]',
											'$this->id_sucursal',
											'$this->id_empresa',
											'$id_tercero',
											'$arrayDatos[fecha_documento]',
											'$row[id_centro_costos]',
											'$row[observaciones]'
										),";

			}
			$acumuladoDebito  = ROUND($acumuladoDebito,$this->decimales_moneda);
			$acumuladoCredito = ROUND($acumuladoCredito,$this->decimales_moneda);

			if($acumuladoDebito > $acumuladoCredito){
				$this->rollBack($id_documento,1," activo=0 ");
				return array('status' => false, 'detalle'=> "El saldo debito no puede ser mayor al credito en las cuentas <b>Debito:</b> $acumuladoDebito <b>Credito:</b>$acumuladoCredito" );
			}

			if($valuesInsertColgaap == ""){
				$this->rollBack($id_documento,1," activo=0 ");
				return array('status' => false, 'detalle'=> "No hay informacion de cuentas a guardar" );
			}
			if($valuesInsertNiif == ""){
				$this->rollBack($id_documento,1," activo=0 ");
				return array('status' => false, 'detalle'=> "No hay informacion de cuentas a guardar (Niif)" );
			}

			$valuesInsertNiif    = substr($valuesInsertNiif, 0, -1);
			$valuesInsertColgaap = substr($valuesInsertColgaap, 0, -1);

			//INSERT ASIENTO COLGAAP
			$sql = "INSERT INTO asientos_colgaap (
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido,
						id_documento_cruce,
						tipo_documento_cruce,
						numero_documento_cruce,
						debe,
						haber,
						codigo_cuenta,
						id_sucursal,
						id_empresa,
						id_tercero,
						fecha,
						id_centro_costos,
						observacion)
					VALUES $valuesInsertColgaap";
			$query=$this->mysql->query($sql,$this->mysql->link);
			if (!$query) {
				$arrayError[0]='Se produjo un error al insertar la contabilidad el documento (Cod. Error 600)';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

				$this->rollBack($id_documento,1," activo=0 ");
        		return array('status'=>false,'detalle'=>$arrayError);

			}
			else{
				$sql = "INSERT INTO asientos_niif (
							id_documento,
							consecutivo_documento,
							tipo_documento,
							tipo_documento_extendido,
							id_documento_cruce,
							tipo_documento_cruce,
							numero_documento_cruce,
							debe,
							haber,
							codigo_cuenta,
							id_sucursal,
							id_empresa,
							id_tercero,
							fecha,
							id_centro_costos,
							observacion)
						VALUES $valuesInsertNiif";
				$query=$this->mysql->query($sql,$this->mysql->link);
				if (!$query) {
					$arrayError[0]='Se produjo un error al insertar la contabilidad el documento (Cod. Error 600)';
					$arrayError[1]="Error numero: ".$this->mysql->errno();
	    			$arrayError[2]="Error detalle: ".$this->mysql->error();
	    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

					$this->rollBack($id_documento,1," activo=0 ");
	        		return array('status'=>false,'detalle'=>$arrayError);
				}

				return array('status'=>true);

			}
		}

		/**
		 * rollBack deshacer los cambios realizados
		 * @param  Int $id_recibo Id de la factura a realizar rollback
		 * @param  Int $nivel      Nivel del rollback a realizar
		 */
		public function rollBack($id_documento,$nivel, $sentencia = NULL){
			if ($this->actionUpdate==true) {
				$sentencia = " estado=0 ";
			}
			else if($sentencia==NULL){
				$sentencia = " estado=0,consecutivo='' " ;
			}

			if ($nivel>=1) {
				$sql="UPDATE nota_contable_general SET $sentencia /*,activo=0*/ WHERE id_empresa=$this->id_empresa AND id=$id_documento; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_colgaap WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_documento AND tipo_documento='NCG'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				// $sql="DELETE FROM contabilizacion_compra_venta WHERE activo=1 AND";
				// $query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_niif WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_documento AND tipo_documento='NCG'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				// $sql="DELETE FROM contabilizacion_compra_venta_niif WHERE activo=1 AND";
				// $query=$this->mysql->query($sql,$this->mysql->link);

			}

			if ($nivel>=2) {
				// $sql   = "UPDATE ventas_facturas AS CF,
				// 			(SELECT SUM(debito-credito) AS abono,id_documento_cruce,cuenta
				// 				FROM recibo_caja_cuentas
				// 				WHERE activo=1 AND id_documento_caja='$id_documento' AND tipo_documento_cruce = 'FV'
				// 				GROUP BY id_documento_cruce
				// 			) AS CE
				// 		SET CF.total_factura_sin_abono=CF.total_factura_sin_abono-CE.abono
				// 		WHERE CF.id=CE.id_documento_cruce
				// 			AND CF.cuenta_pago=CE.cuenta
				// 			AND CF.id_empresa=$this->id_empresa";
				// $query = $this->mysql->query($sql,$this->mysql->link);
			}
		}

		/**
		 * random Generar randomico unico por documento
		 * @return String Randomico para el documento
		 */
		public function random(){
			$random1 = mktime();
	        $chars = array(
	                'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
	                'I', 'J', 'K', 'L', 'M', 'N', 'O',
	                'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
	                'X', 'Y', 'Z', '1', '2', '3', '4', '5',
	                '6', '7', '8', '9', '0'
	                );
	        $max_chars = count($chars) - 1;
	        srand((double) microtime()*1000000);
	        $random2 = '';
	        for($i=0; $i < 6; $i++){ $random2 = $random2 . $chars[rand(0, $max_chars)]; }

	    	$randomico = $random1.''.$random2; // ID UNICO
	    	return $randomico;
		}

	}