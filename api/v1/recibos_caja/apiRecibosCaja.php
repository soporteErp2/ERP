<?php
	include '../../../misc/ConnectDb/class.ConnectDb.php';

	/**
	 * @apiDefine Ventas Se requieren permisos de ventas
	 * Para crear, actualizar o eliminar documentos, se requieren los respectivos permisos del modulo de ventas
	 *
	 */
	class apiRecibosCaja
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
		 * @api {get} /recibos_caja/:fecha/:documento_cliente/:consecutivo/:consecutivo_inicial/:consecutivo_final/:cuenta_pago Consultar recibos de caja
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar recibos de caja del sistema.
		 * @apiName get_recibos
		 * @apiGroup Ingresos
		 *
		 * @apiParam {date} fecha Fecha del recibo a filtrar
		 * @apiParam {String} documento_cliente Documento unico del cliente a consultar
		 * @apiParam {date} consecutivo Consecutivo a filtrar
		 * @apiParam {int} consecutivo_inicial Consecutivo inicial de los recibos a filtrar, Ejemplo: 1
		 * @apiParam {int} consecutivo_final Consecutivo final de los recibos a filtrar, Ejemplo: 100
		 * @apiParam {String} cuenta_pago Cuenta de pago de la cabecera del documento
		 *
		 * @apiParam {String} documento_tercero Documento del tercero del recibo
		 * @apiParam {String} tercero Tercero del recibo de caja
		 * @apiParam {Date} fecha_recibo Fecha del documento
		 * @apiParam {Int} consecutivo Numero consecutivo del documento
		 * @apiParam {String} cuenta Cuenta de cobro relacionada en el recibo
		 * @apiParam {String} descripcion_cuenta Descripcion de la cuenta de cobro
		 * @apiParam {String} cuenta_niif Cuenta Niif de cobro del documento
		 * @apiParam {String} sucursal Sucursal en donde se elaboro el documento
		 * @apiParam {String} observacion Observacion global de todo el documento
		 * @apiParam {String} usuario Usuario que elaboro el documento
		 * @apiParam {Object[]} cuentas Lista de las cuentas causada en el recibo de caja
		 * @apiParam {String} cuentas.cuenta Cuenta contable del documento
		 * @apiParam {String} cuentas.descripcion Descripcion de la cuenta del documento
		 * @apiParam {String} cuentas.cuenta_niif Cuenta niif del documento
		 * @apiParam {Double} cuentas.debito Valor en debito de la cuenta del documento
		 * @apiParam {Double} cuentas.credito Valor en credito de la cuenta del documento
		 * @apiParam {String} cuentas.documento_tercero Documento cruzado en la cuenta
		 * @apiParam {String} cuentas.tercero Tercero cruzado en la cuenta
		 * @apiParam {String} cuentas.tipo_documento_cruce Tipo del documento cruzado en el recibo (tendra valor si se realiza el cruce de facturas)
		 * @apiParam {String} cuentas.prefijo_documento_cruce Prefijo del documento cruzado en el recibo (tendra valor si se realiza el cruce de facturas)
		 * @apiParam {String} cuentas.numero_documento_cruce Numero del documento cruzado en el recibo (tendra valor si se realiza el cruce de facturas)
		 * @apiParam {String} cuentas.observaciones Observacion especifica de la cuenta
		 * @apiParam {Int} cuentas.codigo_centro_costos Codigo del centro de costos de la cuenta (Si tiene)
		 * @apiParam {String} cuentas.centro_costos Centro de costos relacionado a la cuenta (Si tiene)
		 *
		 * @apiSuccessExample Success-Response:
		 *  HTTP/1.1 200 OK
		 *  [
		 *     {
		 *         "documento_tercero": "900467785",
		 *         "tercero": "TERCERO DE PRUEBA",
		 *         "fecha_recibo": "2018-01-01",
		 *         "consecutivo": "1",
		 *         "cuenta_cobro": "11100502",
		 *         "descripcion_cuenta": "BANCO PRUEBA CUENTA CORRIENTE 111111",
		 *         "cuenta_niif": "11100502",
		 *         "sucursal": "Principal",
		 *         "observacion": "Observacion general de prueb",
		 *         "usuario": "USUARIO DE PRUEBA",
		 *         "cuentas": [
		 *             {
		 *                 "cuenta": "13050501",
		 *                 "descripcion": "CLIENTES NACIONALES",
		 *                 "cuenta_niif": "13050501",
		 *                 "debito": "0.00",
		 *                 "credito": "10000.00",
		 *                 "documento_tercero": "",
		 *                 "tercero": "",
		 *                 "tipo_documento_cruce": "FV",
		 *                 "prefijo_documento_cruce": "",
		 *                 "numero_documento_cruce": "825",
		 *                 "observaciones": "",
		 *                 "codigo_centro_costos": "",
		 *                 "centro_costos": ""
		 *             },
		 *             {
		 *                 "cuenta": "13551502",
		 *                 "descripcion": "RETEFUENTE A FAVOR SERVICIOS",
		 *                 "cuenta_niif": "13551502",
		 *                 "debito": "1000.00",
		 *                 "credito": "0.00",
		 *                 "documento_tercero": "",
		 *                 "tercero": "",
		 *                 "tipo_documento_cruce": "",
		 *                 "prefijo_documento_cruce": "",
		 *                 "numero_documento_cruce": "0",
		 *                 "observaciones": "",
		 *                 "codigo_centro_costos": "",
		 *                 "centro_costos": ""
		 *             }
		 *         ]
		 *     },
		 *     {
		 *         "documento_tercero": "900467785",
		 *         "tercero": "TERCERO DE PRUEBA",
		 *         "fecha_recibo": "2018-03-01",
		 *         "consecutivo": "2",
		 *         "cuenta_cobro": "11050501",
		 *         "descripcion_cuenta": "CAJA GENERAL",
		 *         "cuenta_niif": "11050501",
		 *         "sucursal": "Principal",
		 *         "observacion": "Observacion de prueba",
		 *         "usuario": "USUARIO DE PRUEBA",
		 *         "cuentas": [
		 *             {
		 *                 "cuenta": "13050501",
		 *                 "descripcion": "CLIENTES NACIONALES",
		 *                 "cuenta_niif": "13050501",
		 *                 "debito": "0.00",
		 *                 "credito": "35000.00",
		 *                 "documento_tercero": "",
		 *                 "tercero": "",
		 *                 "tipo_documento_cruce": "FV",
		 *                 "prefijo_documento_cruce": "",
		 *                 "numero_documento_cruce": "800",
		 *                 "observaciones": "",
		 *                 "codigo_centro_costos": "",
		 *                 "centro_costos": ""
		 *             }
		 *         ]
		 *     }
		 *  ]
		 */
		public function show($data=NULL){
			$count = 0;
			foreach ($data as $campo => $valor) { $count += ($valor<>'')? 1 : 0 ; }
			if ($count<=0){ return array('status'=>false,'detalle'=>'No se envio ningun filtro de busqueda, debe enviar almenos 1 filtro'); }

			if ($data['fecha']<>'' || $data['fecha']<>''){ return array('status'=>false,'detalle'=>'Para consulta en rango de fecha se debe enviar los dos campos (fecha_inicial y fecha_final)'); }
			if (( $data['consecutivo_inicial']<>'' || $data['consecutivo_final']<>'' ) && ( $data['consecutivo_inicial']=='' || $data['consecutivo_final']=='' ) ){ return array('status'=>false,'detalle'=>'Para consulta en rango de consecutivos se debe enviar los dos campos (consecutivo_inicial y consecutivo_final)'); }

			$whereRecibos = '';
			if($data['documento_cliente']<>''){ $whereRecibos .= " AND nit_tercero='$data[documento_cliente]' "; }
			if($data['fecha']<>''){ $whereRecibos .= " AND fecha_recibo='$data[fecha]' "; }
			if($data['consecutivo']<>''){ $whereRecibos .= " AND consecutivo=$data[consecutivo] "; }
			if($data['consecutivo_inicial']<>''){ $whereRecibos .= " AND consecutivo BETWEEN $data[consecutivo_inicial] AND $data[consecutivo_final] "; }
			if($data['cuenta_pago']<>''){ $whereRecibos .= " AND cuenta = '$data[cuenta_pago]' "; }

			$sql="SELECT
					id,
					nit_tercero AS documento_tercero,
					tercero,
					fecha_recibo,
					consecutivo,
					cuenta AS cuenta_cobro,
					descripcion_cuenta,
					cuenta_niif,
					sucursal,
					observacion,
					usuario
				 FROM recibo_caja WHERE activo=1 AND id_empresa=$this->id_empresa AND (estado=1 OR estado=2) $whereRecibos /*LIMIT 0,2*/ ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($result[]=$this->mysql->fetch_assoc($query));
			array_pop($result);
			$whereIdRecibos = '';
			foreach ($result as $key => $arrayResult){
				$whereIdRecibos .= ($whereIdRecibos=="")? " id_recibo_caja=$arrayResult[id] " : " OR id_recibo_caja=$arrayResult[id] " ;
			}

			$sql="SELECT
						id_recibo_caja,
						id_puc,
						cuenta,
						descripcion,
						cuenta_niif,
						debito,
						credito,
						saldo_pendiente,
						id_tercero,
						IFNULL(codigo_tercero,'') AS codigo_tercero,
						IFNULL(nit_tercero,'') AS documento_tercero,
						IFNULL(tercero,'') AS tercero,
						id_documento_cruce,
						tipo_documento_cruce,
						prefijo_documento_cruce,
						numero_documento_cruce,
						IFNULL(observaciones,'') AS observaciones,
						IFNULL(codigo_centro_costos,'') AS codigo_centro_costos,
						IFNULL(centro_costos,'') AS centro_costos
					FROM recibo_caja_cuentas WHERE activo=1 AND ($whereIdRecibos) ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_assoc($query)){
				$arrayCuentas[$row['id_recibo_caja']][]  = array(
																"cuenta"                  => $row['cuenta'],
																"descripcion"             => $row['descripcion'],
																"cuenta_niif"             => $row['cuenta_niif'],
																"debito"                  => $row['debito'],
																"credito"                 => $row['credito'],
																"documento_tercero"       => $row['documento_tercero'],
																"tercero"                 => $row['tercero'],
																"tipo_documento_cruce"    => $row['tipo_documento_cruce'],
																"prefijo_documento_cruce" => $row['prefijo_documento_cruce'],
																"numero_documento_cruce"  => $row['numero_documento_cruce'],
																"observaciones"           => $row['observaciones'],
																"codigo_centro_costos"    => $row['codigo_centro_costos'],
																"centro_costos"           => $row['centro_costos'],
																);
			}
			foreach ($result as $key => $arrayResult) {
				$result[$key]['cuentas']=$arrayCuentas[$arrayResult['id']];
				unset($result[$key]['id']);
			}

			// exit;
			// print_r($arrayItems);

			return array('status' => true,'data'=> $result);
		}

		/**
		 * @api {post} /recibos_caja/ Crear Recibo de caja
		 * @apiVersion 1.0.0
		 * @apiDescription Registrar recibo de caja en el sistema
		 * @apiName store_recibos
		 * @apiPermission Ventas
		 * @apiGroup Ingresos
		 *
		 * @apiParam {Date} fecha_documento Fecha del recibo de caja (Y-M-D)
		 * @apiParam {String} documento_tercero Numero del documento del tercero del recibo
		 * @apiParam {Int} cuenta_pago Cuenta contable del recaudo del recibo
		 * @apiParam {Int} id_sucursal Id de la sucursal del recibo (Consultar en el panel de control del sistema)
		 * @apiParam {String} [observacion] Observacion general del documento
		 * @apiParam {Object[]} cuentas Lista con las cuentas del recibo, no se debe enviar la cuenta de pago en este listado, el sistema la tomara como contrapartida contable de forma automatica
		 * @apiParam {String} cuentas.cuenta Cuenta contable a causar en el recibo, si tiene una factura relacionada, debe ser la cuenta de pago de la factura
		 * @apiParam {String} [cuentas.documento_tercero] Tercero de la cuenta a causar en el recibo (Si no se envia, por defecto toma el tercero principal), si relaciona una factura debe ser el cliente de la factura
		 * @apiParam {String} [cuentas.prefijo_documento_cruce] Prefijo (si tiene) de una factura de venta a realizar el pago
		 * @apiParam {String} [cuentas.numero_documento_cruce] Numero de una factura de venta a realizar el pago
		 * @apiParam {Double} cuentas.debito Valor en debito para la cuenta (Si no tiene valor enviar 0)
		 * @apiParam {Double} cuentas.credito Valor en credito para la cuenta (Si no tiene valor enviar 0)
		 * @apiParam {String} [cuentas.observaciones] Observacion de la cuenta
		 *
		 * @apiSuccess {200} success  informacion registrada
		 * @apiSuccess {200} consecutivo  Consecutivo del recibo de caja
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "informacion registrada",
		 *        "consecutivo": "Consecutivo del recibo, Ej 110",
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
			if ($this->usuarioPermisos[27]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para crear recibos de caja'); }

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			// CAMPOS OBLIGATORIOS
			// fecha_documento *
			// documento_tercero *
			// cuenta_pago *
			// id_sucursal *
			// observacion
			// cuentas => array *
			// 			cuenta *
			// 			documento_tercero
			// 			documento_cruce (No aplica solo de info, siempre es FV)
			// 			prefijo_documento_cruce
			// 			numero_documento_cruce
			// 			debito *
			// 			credito *
			// 			observaciones

			if ($data['fecha_documento']=='' || !isset($data['fecha_documento']) ){ $arrayError[] = "El campo fecha_documento es obligatorio"; }
			if ($data['documento_tercero']=='' || !isset($data['documento_tercero'])){ $arrayError[] = "El campo documento cliente  es obligatorio"; }
			$arrayTercero = $this->getcliente($data['documento_tercero']);
			$this->id_cliente = $arrayTercero['id'];

			if ($arrayTercero==false) {  $arrayError[] = "El cliente no existe en el sistema"; }
			if ($data['cuenta_pago']=='' || !isset($data['cuenta_pago'])){ $arrayError[] = "El campo cuenta pago es obligatorio"; }
			$arrayCuentaPago = $this->getCuentaPago($data['cuenta_pago']);
			if (!array_key_exists("$data[cuenta_pago]",$arrayCuentaPago)){ $arrayError[] = "La cuenta de pago no existe en el sistema"; }
			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id sucursal es obligatorio"; }
			$arrayUbicaciones = $this->getSucursales();
			if (!array_key_exists("$data[id_sucursal]",$arrayUbicaciones['sucursales'])) { $arrayError[] = "La sucursal no existe"; }
			$this->id_sucursal = $data['id_sucursal'];
			$arrayPuc = $this->getPuc();
			if (empty($data['cuentas'])){ $arrayError[] = "El objeto con las cuentas es obligatorio"; }
			$acumuladoDebito    = 0;
			$acumuladoCredito   = 0;
			// $contDocumentoCruce = 0;
			// print_r($arrayPuc);
			foreach ($data['cuentas'] as $key => $arrayCuentas) {
				if (gettype($arrayCuentas)=='object') {
					$arrayCuentas=get_object_vars($arrayCuentas);
				}
				$arrayValidateDocCruce[$arrayCuentas['prefijo_documento_cruce']."-".$arrayCuentas['numero_documento_cruce']]++;
				// cuenta *
				// documento_tercero
				// prefijo_documento_cruce
				// numero_documento_cruce
				// debito *
				// credito *
				// observaciones

				if ($arrayCuentas['cuenta']==='') { $arrayError[] = "El campo cuenta para el documento es obligatorio, posicion $key"; }
				if ($arrayCuentas['debito']==='' || !is_numeric($arrayCuentas['debito']) ) { $arrayError[] = "El campo debito para la cuenta es obligatorio y debe ser numerico, si no tiene valor enviar cero en 0, cuenta $arrayCuentas[cuenta] "; }
				if ($arrayCuentas['credito']==='' || !is_numeric($arrayCuentas['credito']) ) { $arrayError[] = "El campo credito para la cuenta es obligatorio y debe ser numerico, si no tiene valor enviar cero en 0, cuenta $arrayCuentas[cuenta] "; }
				if ($arrayCuentas['debito']>0 && $arrayCuentas['credito']>0 ) { $arrayError[] = "No se permite que el debito y credito tenga valor en un solo registro, adicione la misma cuenta en otra posicion con el otro valor, cuenta $arrayCuentas[cuenta] "; }
				if ($arrayCuentas['debito']==0 && $arrayCuentas['credito']==0 ) { $arrayError[] = "No se permite que el debito y credito tenga valor en cero, uno de los dos debe tener un valor mayor a cero, cuenta $arrayCuentas[cuenta] "; }

				if (!array_key_exists("$arrayCuentas[cuenta]",$arrayPuc)) { $arrayError[] = "La cuenta $arrayCuentas[cuenta] no existe en el sistema"; }
				if ($arrayCuentas['documento_tercero']<>'') {
					$arrayTerceroCuentas = $this->getcliente($arrayCuentas['documento_tercero']);
					if ($arrayTerceroCuentas==false) {  $arrayError[] = "El cliente relacionado para la cuenta $arrayCuentas[cuenta] no existe en el sistema"; }
				}else{ $id_tercero = $this->id_cliente; }
				if ($arrayCuentas['numero_documento_cruce']<>'') {
					if ($arrayCuentas['credito']=='' || !is_numeric($arrayCuentas['credito']) ) { $arrayError[] = "El campo credito es obligatorio cuando se inserta un documento cruce, cuenta $arrayCuentas[cuenta] "; }
					$consecutivo_completo = ($arrayCuentas['prefijo_documento_cruce']<>'')? "$arrayCuentas[prefijo_documento_cruce] $arrayCuentas[numero_documento_cruce]" : "$arrayCuentas[numero_documento_cruce]" ;

					$arrayDocCruce = $this->getDocumentocruce($consecutivo_completo,"FV",$arrayCuentas['credito']);
        			if ($arrayDocCruce['status']==false) { $arrayError[] = "$arrayDocCruce[detalle]"; }
					if($arrayDocCruce==false){ $arrayError[] = "El documento cruce relacionado para la cuenta $arrayCuentas[cuenta] no existe en el sistema"; }
					else{ $id_documento_cruce=$arrayDocCruce['id_documento']; }

					if ($arrayDocsCruce[$consecutivo_completo]>0) { $arrayError[] = "El documento cruce $consecutivo_completo esta repetido, solo se permite una vez el mismo documento cruce en el recibo"; }
					else{ $arrayDocsCruce[$consecutivo_completo]=1; }

					$tipo_documento_cruce    = 'FV';
					$prefijo_documento_cruce = $arrayCuentas['prefijo_documento_cruce'];
					$numero_documento_cruce  = $arrayCuentas['numero_documento_cruce'];

				}
				else{
					$id_documento_cruce      = '';
					$tipo_documento_cruce    = '';
					$prefijo_documento_cruce = '';
					$numero_documento_cruce  = '';
				}

				$acumuladoDebito  += $arrayCuentas['debito'];
				$acumuladoCredito += $arrayCuentas['credito'];
				$arrayCuentas['observaciones'] = addslashes($arrayCuentas['observaciones']);

				$valueInsertCuentas .= "(
										'id_recibo_caja_insert',
										".$arrayPuc[$arrayCuentas['cuenta']]['id'].",
										$arrayCuentas[debito],
										$arrayCuentas[credito],
										$arrayCuentas[credito],
										'$arrayTerceroCuentas[id]',
										'$id_documento_cruce',
										'$tipo_documento_cruce ',
										'$prefijo_documento_cruce',
										'$numero_documento_cruce',
										'$arrayCuentas[observaciones]'
										),";
			}
			if($acumuladoDebito > $acumuladoCredito){  $arrayError[] = "El saldo total debito no puede ser mayor al saldo total credito"; }

			$consecutivo = $this->getConsecutivo();
			if ($consecutivo==false) {  $arrayError[] = "Error interno del sistema al consultar el consecutivo del recibo de caja"; }

			foreach ($arrayValidateDocCruce as $prefijo => $arrayValidateDocCruce2) {
				foreach ($arrayValidateDocCruce2 as $consecutivoDocCruce => $cont) {
					if ($cont>1) { $arrayError[] = "Solo se permite documentos cruce una vez, el documento $prefijo $consecutivoDocCruce esta repetido"; }
				}
			}

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }
			// return array('status'=>true,'consecutivo'=>'prueba');
			$json                = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']);
			$random = $this->random();
			$sql   = "INSERT INTO recibo_caja
							(
								random,
								id_tercero,
								codigo_tercero,
								nit_tercero,
								tercero,
								fecha_recibo,
								consecutivo,
								id_configuracion_cuenta,
								configuracion_cuenta,
								cuenta,
								descripcion_cuenta,
								cuenta_niif,
								estado,
								id_sucursal,
								id_empresa,
								observacion,
								id_usuario,
								usuario,
								json_api
							)
                        VALUES
                        	(
                        		'$random',
								'$arrayTercero[id]',
								'$arrayTercero[codigo]',
								'$arrayTercero[numero_identificacion]',
								'$arrayTercero[nombre]',
								'$data[fecha_documento]',
								'$consecutivo',
								'".$arrayCuentaPago[$data['cuenta_pago']]['id']."',
								'".$arrayCuentaPago[$data['cuenta_pago']]['nombre']."',
								'".$arrayCuentaPago[$data['cuenta_pago']]['cuenta']."',
								'".$arrayCuentaPago[$data['cuenta_pago']]['nombre_cuenta']."',
								'".$arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif']."',
								'1',
								'$this->id_sucursal',
								'$this->id_empresa',
								'$data[observacion]',
								'$this->id_usuario',
								'$this->nombre_usuario',
								'$json'
                        	)";
        	$query = $this->mysql->query($sql,$this->mysql->link);
        	if ($query){
        		$sql="SELECT id FROM recibo_caja WHERE activo=1 AND id_empresa=$this->id_empresa AND random='$random' ";
        		$query=$this->mysql->query($sql,$this->mysql->link);
        		$id_recibo = $this->mysql->result($query,0,'id');
        		// echo $id_recibo;

				$valueInsertCuentas = substr($valueInsertCuentas, 0, -1);
				$valueInsertCuentas = str_replace("id_recibo_caja_insert", $id_recibo, $valueInsertCuentas);

        		$sql="INSERT INTO recibo_caja_cuentas
        				(
			        		id_recibo_caja,
							id_puc,
							debito,
							credito,
							saldo_pendiente,
							id_tercero,
							id_documento_cruce,
							tipo_documento_cruce,
							prefijo_documento_cruce,
							numero_documento_cruce,
							observaciones
        				)
        				VALUES $valueInsertCuentas";
        		$query=$this->mysql->query($sql,$this->mysql->link);
        		// return array('status'=>200,'consecutivo'=>$consecutivo);
        		if (!$query) {
        			$this->rollBack($id_recibo,1);

        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos';
					$arrayError[1]="Error numero: ".$this->mysql->errno();
	    			$arrayError[2]="Error detalle: ".$this->mysql->error();
	    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
	        		return array('status'=>false,'detalle'=>$arrayError);
        		}
        		else{
					$arrayDatos['consecutivo']  = $consecutivo;
					$arrayDatos['id_tercero']   = $arrayTercero['id'];
					$arrayDatos['tercero']      = $arrayTercero['nombre'];
					$arrayDatos['cuenta']       = $arrayCuentaPago[$data['cuenta_pago']]['cuenta'];
					$arrayDatos['cuenta_niif']  = $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'];
					$arrayDatos['fecha_recibo'] = $data['fecha_documento'];

        			$contabilizacion = $this->setContabilidad($id_recibo,$arrayDatos);
        			if ($contabilizacion['status']==false) { return array('status'=>false,'detalle'=>$contabilizacion['detalle']); }

        			$docsCruce = $this->setSaldosDocumentosCruce($id_recibo);
        			if ($docsCruce['status']==false) { return array('status'=>false,'detalle'=>$docsCruce['detalle']); }

        			$sql="UPDATE configuracion_consecutivos_documentos SET consecutivo=consecutivo+1
								WHERE activo=1
								AND id_empresa=$this->id_empresa
								AND id_sucursal=$this->id_sucursal
								AND modulo='venta'
								AND documento='recibo_de_caja' ";
        			$query=$this->mysql->query($sql,$this->mysql->link);
        			if (!$query) {
        				$this->rollBack($id_recibo,2);
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

		/**
		 * @api {put} /recibos_caja/ Modificar Recibo de caja
		 * @apiVersion 1.0.0
		 * @apiDescription Modificar un recibo de caja en el sistema
		 * @apiName update_recibos
		 * @apiPermission Ventas
		 * @apiGroup Ingresos
		 *
		 * @apiParam {String} consecutivo Consecutivo del recibo de caja
		 * @apiParam {Date} fecha_documento Fecha del recibo de caja (Y-M-D)
		 * @apiParam {String} documento_tercero Numero del documento del tercero del recibo
		 * @apiParam {Int} cuenta_pago Cuenta contable del recaudo del recibo
		 * @apiParam {Int} id_sucursal Id de la sucursal del recibo (Consultar en el panel de control del sistema)
		 * @apiParam {String} [observacion] Observacion general del documento
		 * @apiParam {Object[]} cuentas Lista con las cuentas del recibo, no se debe enviar la cuenta de pago en este listado, el sistema la tomara como contrapartida contable de forma automatica
		 * @apiParam {String} cuentas.cuenta Cuenta contable a causar en el recibo, si tiene una factura relacionada, debe ser la cuenta de pago de la factura
		 * @apiParam {String} [cuentas.documento_tercero] Tercero de la cuenta a causar en el recibo (Si no se envia, por defecto toma el tercero principal), si relaciona una factura debe ser el cliente de la factura
		 * @apiParam {String} [cuentas.prefijo_documento_cruce] Prefijo (si tiene) de una factura de venta a realizar el pago
		 * @apiParam {String} [cuentas.numero_documento_cruce] Numero de una factura de venta a realizar el pago
		 * @apiParam {Double} cuentas.debito Valor en debito para la cuenta (Si no tiene valor enviar 0)
		 * @apiParam {Double} cuentas.credito Valor en credito para la cuenta (Si no tiene valor enviar 0)
		 * @apiParam {String} [cuentas.observaciones] Observacion de la cuenta
		 *
		 * @apiSuccess {200} success  Documento actualizado
		 * @apiSuccess {200} consecutivo  Consecutivo del recibo de caja
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "Documento actualizado",
		 *        "consecutivo": "Consecutivo del recibo, Ej 110",
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
		public function update($data=NULL){
			$this->actionUpdate = true;
			global $json;
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion para registrar'); }
			if ($this->usuarioPermisos[28]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para editar recibos de caja'); }

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id sucursal es obligatorio"; }
			if ($data['documento_tercero']=='' || !isset($data['documento_tercero'])){ $arrayError[] = "El campo documento cliente  es obligatorio"; }
			if ($data['consecutivo']=='' || !isset($data['consecutivo'])){ $arrayError[] = "El campo consecutivo es obligatorio"; }
			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			$sql="SELECT
					id,
					estado
				FROM recibo_caja
				WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_sucursal=$data[id_sucursal]
					AND nit_tercero='$data[documento_tercero]'
					AND consecutivo='$data[consecutivo]' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			if (!$query) {
    			$arrayError[0]='Se produjo un error al verificar el documento en la base de datos';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
        		return array('status'=>false,'detalle'=>$arrayError);
    		}
			$id_recibo_caja    = $this->mysql->result($query,0,'id');
			$estado            = $this->mysql->result($query,0,'estado');
			$this->id_bodega   = $data['id_bodega'];
			$this->id_sucursal = $data['id_sucursal'];

			if ($id_recibo_caja=='' || $id_recibo_caja==0 || is_null($id_recibo_caja)) { $arrayError[] = "El documento no existe en el sistema"; }
			if ($estado==2) { $arrayError[] = "El documento se encuentra bloqueado"; }
			if ($estado==3) { $arrayError[] = "El documento se encuentra anulada"; }

			if ($data['fecha_documento']=='' || !isset($data['fecha_documento']) ){ $arrayError[] = "El campo fecha_documento es obligatorio"; }
			$arrayTercero = $this->getcliente($data['documento_tercero']);
			$this->id_cliente = $arrayTercero['id'];

			if ($arrayTercero==false) {  $arrayError[] = "El cliente no existe en el sistema"; }
			if ($data['cuenta_pago']=='' || !isset($data['cuenta_pago'])){ $arrayError[] = "El campo cuenta pago es obligatorio"; }
			$arrayCuentaPago = $this->getCuentaPago($data['cuenta_pago']);
			if (!array_key_exists("$data[cuenta_pago]",$arrayCuentaPago)){ $arrayError[] = "La cuenta de pago no existe en el sistema"; }
			$arrayUbicaciones = $this->getSucursales();
			if (!array_key_exists("$data[id_sucursal]",$arrayUbicaciones['sucursales'])) { $arrayError[] = "La sucursal no existe"; }
			$this->id_sucursal = $data['id_sucursal'];
			$arrayPuc = $this->getPuc();
			if (empty($data['cuentas'])){ $arrayError[] = "El objeto con las cuentas es obligatorio"; }
			$acumuladoDebito  = 0;
			$acumuladoCredito = 0;
			// print_r($arrayPuc);
			foreach ($data['cuentas'] as $key => $arrayCuentas) {
				if (gettype($arrayCuentas)=='object') {
					$arrayCuentas=get_object_vars($arrayCuentas);
				}

				$arrayValidateDocCruce[$arrayCuentas['prefijo_documento_cruce']."-".$arrayCuentas['numero_documento_cruce']]++;
				// cuenta *
				// documento_tercero
				// prefijo_documento_cruce
				// numero_documento_cruce
				// debito *
				// credito *
				// observaciones

				if ($arrayCuentas['cuenta']=='') { $arrayError[] = "El campo cuenta para el documento es obligatorio, posicion $key"; }
				if ($arrayCuentas['debito']=='' || !is_numeric($arrayCuentas['debito']) ) { $arrayError[] = "El campo debito para la cuenta es obligatorio y debe ser numerico, si no tiene valor enviar cero en 0, cuenta $arrayCuentas[cuenta] "; }
				if ($arrayCuentas['credito']=='' || !is_numeric($arrayCuentas['credito']) ) { $arrayError[] = "El campo credito para la cuenta es obligatorio y debe ser numerico, si no tiene valor enviar cero en 0, cuenta $arrayCuentas[cuenta] "; }
				if ($arrayCuentas['debito']>0 && $arrayCuentas['credito']>0 ) { $arrayError[] = "No se permite que el debito y credito tenga valor en un solo registro, adicione la misma cuenta en otra posicion con el otro valor, cuenta $arrayCuentas[cuenta] "; }
				if ($arrayCuentas['debito']==0 && $arrayCuentas['credito']==0 ) { $arrayError[] = "No se permite que el debito y credito tenga valor en cero, uno de los dos debe tener un valor mayor a cero, cuenta $arrayCuentas[cuenta] "; }

				if (!array_key_exists("$arrayCuentas[cuenta]",$arrayPuc)) { $arrayError[] = "La cuenta $arrayCuentas[cuenta] no existe en el sistema"; }
				if ($arrayCuentas['documento_tercero']<>'') {
					$arrayTerceroCuentas = $this->getcliente($arrayCuentas['documento_tercero']);
					if ($arrayTerceroCuentas==false) {  $arrayError[] = "El cliente relacionado para la cuenta $arrayCuentas[cuenta] no existe en el sistema"; }
				}else{ $id_tercero = $this->id_cliente; }
				if ($arrayCuentas['numero_documento_cruce']<>'') {
					if ($arrayCuentas['credito']=='' || !is_numeric($arrayCuentas['credito']) ) { $arrayError[] = "El campo credito es obligatorio cuando se inserta un documento cruce, cuenta $arrayCuentas[cuenta] "; }
					$consecutivo_completo = ($arrayCuentas['prefijo_documento_cruce']<>'')? "$arrayCuentas[prefijo_documento_cruce] $arrayCuentas[numero_documento_cruce]" : "$arrayCuentas[numero_documento_cruce]" ;

					$arrayDocCruce = $this->getDocumentocruce($consecutivo_completo,"FV",$arrayCuentas['credito']);
        			if ($arrayDocCruce['status']==false) { $arrayError[] = "$arrayDocCruce[detalle]"; }
					if($arrayDocCruce==false){ $arrayError[] = "El documento cruce relacionado para la cuenta $arrayCuentas[cuenta] no existe en el sistema"; }
					else{ $id_documento_cruce=$arrayDocCruce['id_documento']; }

					if ($arrayDocsCruce[$consecutivo_completo]>0) { $arrayError[] = "El documento cruce $consecutivo_completo esta repetido, solo se permite una vez el mismo documento cruce en el recibo"; }
					else{ $arrayDocsCruce[$consecutivo_completo]=1; }

					$tipo_documento_cruce    = 'FV';
					$prefijo_documento_cruce = $arrayCuentas['prefijo_documento_cruce'];
					$numero_documento_cruce  = $arrayCuentas['numero_documento_cruce'];

				}
				else{
					$id_documento_cruce      = '';
					$tipo_documento_cruce    = '';
					$prefijo_documento_cruce = '';
					$numero_documento_cruce  = '';
				}

				$acumuladoDebito  += $arrayCuentas['debito'];
				$acumuladoCredito += $arrayCuentas['credito'];
				$arrayCuentas['observaciones'] = addslashes($arrayCuentas['observaciones']);

				$valueInsertCuentas .= "(
										'$id_recibo_caja',
										".$arrayPuc[$arrayCuentas['cuenta']]['id'].",
										$arrayCuentas[debito],
										$arrayCuentas[credito],
										$arrayCuentas[credito],
										'$arrayTerceroCuentas[id]',
										'$id_documento_cruce',
										'$tipo_documento_cruce ',
										'$prefijo_documento_cruce',
										'$numero_documento_cruce',
										'$arrayCuentas[observaciones]'
										),";
			}
			if($acumuladoDebito > $acumuladoCredito){  $arrayError[] = "El saldo total debito no puede ser mayor al saldo total credito"; }

			foreach ($arrayValidateDocCruce as $prefijo => $arrayValidateDocCruce2) {
				foreach ($arrayValidateDocCruce2 as $consecutivoDocCruce => $cont) {
					if ($cont>1) { $arrayError[] = "Solo se permite documentos cruce una vez, el documento $prefijo $consecutivoDocCruce esta repetido"; }
				}
			}

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if ($estado==1){
				$this->rollBack($id_recibo_caja,2 );
			}

			$json                = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']);
			$random = $this->random();
			$sql   = "UPDATE recibo_caja
						SET
							id_tercero              = '$arrayTercero[id]',
							codigo_tercero          = '$arrayTercero[codigo]',
							nit_tercero             = '$arrayTercero[numero_identificacion]',
							tercero                 = '$arrayTercero[nombre]',
							fecha_recibo            = '$data[fecha_documento]',
							id_configuracion_cuenta = '".$arrayCuentaPago[$data['cuenta_pago']]['id']."',
							configuracion_cuenta    = '".$arrayCuentaPago[$data['cuenta_pago']]['nombre']."',
							cuenta                  = '".$arrayCuentaPago[$data['cuenta_pago']]['cuenta']."',
							descripcion_cuenta      = '".$arrayCuentaPago[$data['cuenta_pago']]['nombre_cuenta']."',
							cuenta_niif             = '".$arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif']."',
							estado                  = '1',
							id_sucursal             = '$this->id_sucursal',
							id_empresa              = '$this->id_empresa',
							observacion             = '$data[observacion]',
							id_usuario              = '$this->id_usuario',
							usuario                 = '$this->nombre_usuario',
							json_api                = '$json'
                        	WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_recibo_caja";
        	$query = $this->mysql->query($sql,$this->mysql->link);
        	if ($query){
        		// $sql="SELECT id FROM recibo_caja WHERE activo=1 AND id_empresa=$this->id_empresa AND random='$random' ";
        		// $query=$this->mysql->query($sql,$this->mysql->link);
        		// $id_recibo = $this->mysql->result($query,0,'id');
        		// echo $id_recibo;

				$valueInsertCuentas = substr($valueInsertCuentas, 0, -1);
				// $valueInsertCuentas = str_replace("id_recibo_caja_insert", $id_recibo, $valueInsertCuentas);
				$sql="DELETE FROM recibo_caja_cuentas WHERE id_recibo_caja=$id_recibo_caja";
				$query=$this->mysql->query($sql,$this->mysql->link);

        		$sql="INSERT INTO recibo_caja_cuentas
        				(
			        		id_recibo_caja,
							id_puc,
							debito,
							credito,
							saldo_pendiente,
							id_tercero,
							id_documento_cruce,
							tipo_documento_cruce,
							prefijo_documento_cruce,
							numero_documento_cruce,
							observaciones
        				)
        				VALUES $valueInsertCuentas";
        		$query=$this->mysql->query($sql,$this->mysql->link);
        		// return array('status'=>200,'consecutivo'=>$consecutivo);
        		if (!$query) {
        			$this->rollBack($id_recibo_caja,1);

        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos';
					$arrayError[1]="Error numero: ".$this->mysql->errno();
	    			$arrayError[2]="Error detalle: ".$this->mysql->error();
	    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
	        		return array('status'=>false,'detalle'=>$arrayError);
        		}
        		else{
					$arrayDatos['consecutivo']  = $data['consecutivo'];
					$arrayDatos['id_tercero']   = $arrayTercero['id'];
					$arrayDatos['tercero']      = $arrayTercero['nombre'];
					$arrayDatos['cuenta']       = $arrayCuentaPago[$data['cuenta_pago']]['cuenta'];
					$arrayDatos['cuenta_niif']  = $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'];
					$arrayDatos['fecha_recibo'] = $data['fecha_documento'];

        			$contabilizacion = $this->setContabilidad($id_recibo_caja,$arrayDatos);
        			if ($contabilizacion['status']==false) { return array('status'=>false,'detalle'=>$contabilizacion['detalle']); }

        			$docsCruce = $this->setSaldosDocumentosCruce($id_recibo_caja);
        			if ($docsCruce['status']==false) { return array('status'=>false,'detalle'=>$docsCruce['detalle']); }

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

		/**
		 * @api {delete} /recibos_caja/ Anular recibo de caja
		 * @apiVersion 1.0.0
		 * @apiDescription Anular recibo de caja en el sistema.
		 * @apiName delete_recibos
		 * @apiPermission Ventas
		 * @apiGroup Ingresos
		 *
		 * @apiParam {String} id_sucursal Id de la sucursal del documento (Consultar en el panel de control del sistema)
		 * @apiParam {String} consecutivo Consecutivo del recibo de caja
		 * @apiParam {String} documento_tercero Documento del cliente del recibo
		 *
		 * @apiSuccess {200} success  Documento Anulado
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "Documento anulado",
		 *     }
		 *
		 * @apiErrorExample Error-Response:
		 * HTTP/1.1 400 Bad Response
		 * {
		 *  "failure": "Ha ocurrido un error",
		 *   "detalle": "detalle del error"
		 * }
		 *
		 * @apiError failure Ha ocurrido un error
		 * @apiError detalle
		 *     HTTP/1.1 400 Bad Response
		 *     {
		 *       "failure": "Ha ocurrido un error",
		 *       "detalle": "detalle del error"
		 *     }
		 */
		public function delete($data=NULL){
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[29]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para anular recibos de caja'); }
			$data = json_decode( json_encode($data), true);
			// id_sucursal *
			// id_bodega *
			// consecutivo *
			// documento_tercero *

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id_sucursal es obligatorio"; }
			if ($data['consecutivo']=='' || !isset($data['consecutivo'])){ $arrayError[] = "El campo consecutivo es obligatorio"; }
			if ($data['documento_tercero']=='' || !isset($data['documento_tercero'])){ $arrayError[] = "El campo documento_tercero es obligatorio"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			$sql="SELECT
					id,
					estado
				FROM recibo_caja
				WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_sucursal=$data[id_sucursal]
					AND nit_tercero='$data[documento_tercero]'
					AND consecutivo='$data[consecutivo]' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			if (!$query) {
    			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
        		return array('status'=>false,'detalle'=>$arrayError);
    		}
			$id_recibo         = $this->mysql->result($query,0,'id');
			$estado            = $this->mysql->result($query,0,'estado');
			$this->id_sucursal = $data['id_sucursal'];

			if ($id_recibo=='' || $id_recibo==0) { $arrayError[] = "El recibo de caja no existe en el sistema"; }
			if ($estado==3) { $arrayError[] = "El recibo de caja ya esta anulada"; }
			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if ($estado==1){
				$this->rollBack($id_recibo,2, " estado=3 " );
			}
			else if ($estado==0) {
				$this->rollBack($id_recibo,1, " estado=3 " );
			}

			return array('status'=>200);
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
		    $json_response = json_encode($response['data']);
		    echo $json_response;
		    exit;
		}

		/**
		 * getConsecutivo Consultar el consecutivo del documento
		 * @return Int Consecutivo del documento
		 */
		public function getConsecutivo(){
			$sql="SELECT
					consecutivo
					FROM configuracion_consecutivos_documentos
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND modulo='venta' AND documento='recibo_de_caja' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$consecutivo = $this->mysql->result($query,0,'consecutivo');
			return ($consecutivo>0)? $consecutivo : false;
		}

		/**
		 * getTerceros Consultar el cliente del sistema
		 * @param  String $documento Documento del cliente a consultar
		 * @return int Id del cliente a consultar
		 */
		public function getcliente($documento){
			$sql="SELECT id,numero_identificacion,nombre,codigo FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa AND numero_identificacion='$documento'";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$datos['id']                    = $this->mysql->result($query,0,'id');
			$datos['codigo']                = $this->mysql->result($query,0,'codigo');
			$datos['numero_identificacion'] = $this->mysql->result($query,0,'numero_identificacion');
			$datos['nombre']                = $this->mysql->result($query,0,'nombre');

			return ($datos['id']>0)? $datos : false;
		}

		/**
		 * getCuentaPago Consultar la cuenta de pago de la factura
		 * @param  string $cuenta_pago cuenta de pago de la factura
		 * @return array              Array con las cuentas de pago
		 */
		public function getCuentaPago($cuenta_pago){
			$sql="SELECT id,nombre,id_cuenta,cuenta,nombre_cuenta,cuenta_niif,estado
					FROM configuracion_cuentas_pago WHERE activo=1 AND tipo='Venta' AND id_empresa=$this->id_empresa AND cuenta='$cuenta_pago' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayTemp[$row['cuenta']] = array(
												'id'            => $row['id'],
												'nombre'        => $row['nombre'],
												'id_cuenta'     => $row['id_cuenta'],
												'cuenta'        => $row['cuenta'],
												'nombre_cuenta' => $row['nombre_cuenta'],
												'cuenta_niif'   => $row['cuenta_niif'],
												'estado'        => $row['estado'],
												);
			}

			return $arrayTemp;
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
		 * getDocumentocruce Consultar el documento cruce del recibo de caja
		 * @param  Int $consecutivo_completo Numero completo de la factura a consultar
		 * @param  String $tipo_documento_cruce Tipo del documento a consultar, por ahora siempre sera FV = Factura de venta
		 * @return Int Id del documento o false en caso de error
		 */
		public function getDocumentocruce($consecutivo_completo,$tipo_documento_cruce,$credito){
			if ($tipo_documento_cruce=='FV'){
				$sql="SELECT id,total_factura_sin_abono AS saldo  FROM ventas_facturas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND numero_factura_completo='$consecutivo_completo' ";
				$query        =$this->mysql->query($sql,$this->mysql->link);
				$id_documento = $this->mysql->result($query,0,'id');
				$saldo        = $this->mysql->result($query,0,'saldo');

				if($id_documento<=0 || $id_documento=='') { return array('status'=>false,'detalle'=>"El documento $consecutivo_completo no existe en el sistema"); }
				if($credito<=0){ return array('status'=>false,'detalle'=>"El campo credito para el documento cruce $consecutivo_completo no puede estar en cero, por que no se pagaria ningun valor a la factura"); }
				if($saldo < $credito ){ return array('status'=>false,'detalle'=>"El abono es superior al saldo del documento $consecutivo_completo, saldo restante: $saldo"); }
				return array('status'=>true,'id_documento'=>$id_documento);
			}
		}

		/**
		 * setAsientos Contabilizar la factura en norma local
		 * @param Int $id_recibo        Id de la factura a causar
		 * @param Array $arrayCuentaPago Array con la informacion de la cuenta de pago
		 * @return Array Resultado del proceso {status: true o false},{detalle: en caso de ser error se detallara en este campo}
		 */
		public function setContabilidad($id_recibo,$arrayDatos){
			$sql   = "SELECT
						id,
						debito,
						credito,
						cuenta AS cuenta_colgaap,
		 				cuenta_niif,
						id_tercero,
						id_documento_cruce,
						tipo_documento_cruce,
						prefijo_documento_cruce,
						numero_documento_cruce,
						id_centro_costos,
						observaciones
					FROM recibo_caja_cuentas
					WHERE id_recibo_caja='$id_recibo' AND activo=1 ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$acumuladoDebito     = 0;
			$acumuladoCredito    = 0;
			$valuesInsertNiif    = "";
			$valuesInsertColgaap = "";
			while ($row=$this->mysql->fetch_array($query)) {
				if ($row['cuenta_niif']==0 || $row['cuenta_niif']=='') {
					$this->rollBack($id_recibo,1);
					return array('status' => false, 'detalle'=> "La cuenta $row[cuenta_colgaap] no tiene una cuenta niif configurada" );
				}

				$row['observaciones']   = addslashes($data['observaciones']);
				$id_documento_cruce     = ($row['id_documento_cruce']=='')? "$id_recibo" : "$row[id_documento_cruce]";
				$tipo_documento_cruce   = ($row['tipo_documento_cruce']=='')? "RC" : "$row[tipo_documento_cruce]";
				$numero_documento_cruce = ($row['numero_documento_cruce']=='')? "$arrayDatos[consecutivo]" : "$row[numero_documento_cruce]";
				$id_tercero             = ($row['id_tercero']=='')? $arrayDatos['id_tercero'] : $row['id_tercero'] ;

				$acumuladoDebito     += $row['debito'];
				$acumuladoCredito    += $row['credito'];

				$valuesInsertColgaap .= "(
											'$id_recibo',
											'$arrayDatos[consecutivo]',
											'RC',
											'Recibo de Caja',
											'$id_documento_cruce',
											'$tipo_documento_cruce',
											'$numero_documento_cruce',
											'$row[debito]',
											'$row[credito]',
											'$row[cuenta_colgaap]',
											'$this->id_sucursal',
											'$this->id_empresa',
											'$id_tercero',
											'$arrayDatos[fecha_recibo]',
											'$row[id_centro_costos]',
											'$row[observaciones]'
										),";

				$valuesInsertNiif .= "(
											'$id_recibo',
											'$arrayDatos[consecutivo]',
											'RC',
											'Recibo de Caja',
											'$id_documento_cruce',
											'$tipo_documento_cruce',
											'$numero_documento_cruce',
											'$row[debito]',
											'$row[credito]',
											'$row[cuenta_niif]',
											'$this->id_sucursal',
											'$this->id_empresa',
											'$id_tercero',
											'$arrayDatos[fecha_recibo]',
											'$row[id_centro_costos]',
											'$row[observaciones]'
										),";

			}

			if($acumuladoDebito > $acumuladoCredito){
				$this->rollBack($id_recibo,1);
				return array('status' => false, 'detalle'=> "El saldo debito no puede ser mayor al credito en las cuentas" );
			}

			$saldoCuentaCruce = $acumuladoCredito - $acumuladoDebito;
			if ($saldoCuentaCruce>0) {
				$valuesInsertColgaap .= "(
											'$id_recibo',
											'$arrayDatos[consecutivo]',
											'RC',
											'Recibo de Caja',
											'$id_recibo',
											'RC',
											'$arrayDatos[consecutivo]',
											'$saldoCuentaCruce',
											'0',
											'$arrayDatos[cuenta]',
											'$this->id_sucursal',
											'$this->id_empresa',
											'$arrayDatos[id_tercero]',
											'$arrayDatos[fecha_recibo]',
											'',
											''
										),";
				$valuesInsertNiif    .= "(
											'$id_recibo',
											'$arrayDatos[consecutivo]',
											'RC',
											'Recibo de Caja',
											'$id_recibo',
											'RC',
											'$arrayDatos[consecutivo]',
											'$saldoCuentaCruce',
											'0',
											'$arrayDatos[cuenta]',
											'$this->id_sucursal',
											'$this->id_empresa',
											'$arrayDatos[id_tercero]',
											'$arrayDatos[fecha_recibo]',
											'',
											''
										),";

			}

			if($valuesInsertColgaap == ""){
				$this->rollBack($id_recibo,1);
				return array('status' => false, 'detalle'=> "No hay informacion de cuentas a guardar" );
			}
			if($valuesInsertNiif == ""){
				$this->rollBack($id_recibo,1);
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

				$this->rollBack($id_recibo,1);
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

					$this->rollBack($id_recibo,1);
	        		return array('status'=>false,'detalle'=>$arrayError);
				}

				return array('status'=>true);

			}
		}

		/**
		 * setSaldosDocumentosCruce Actualizar el saldo de los documentos cruzados en la factura
		 * @param Array Respuesta del proceso de ser correcta o no
		 */
		function setSaldosDocumentosCruce($id_recibo){
		  	$sql = "UPDATE ventas_facturas AS CF,
						(SELECT SUM(debito-credito) AS abono,id_documento_cruce,cuenta
							FROM recibo_caja_cuentas
							WHERE activo=1 AND id_recibo_caja='$id_recibo' AND tipo_documento_cruce = 'FV'
							GROUP BY id_documento_cruce
						) AS CE
					SET CF.total_factura_sin_abono=CF.total_factura_sin_abono+CE.abono
					WHERE CF.id=CE.id_documento_cruce
						AND CF.cuenta_pago=CE.cuenta
						AND CF.id_empresa=$this->id_empresa; ";
			$query = $this->mysql->query($sql,$this->mysql->link);
			if (!$query) {
				$this->rollBack($id_recibo,1);
				return array('status' => false, 'detalle'=> "No se descontaron los valores del(os) Documento cruce");
			}
			else{ return array('status' => true); }
		}

		/**
		 * rollBack deshacer los cambios realizados
		 * @param  Int $id_recibo Id de la factura a realizar rollback
		 * @param  Int $nivel      Nivel del rollback a realizar
		 */
		public function rollBack($id_recibo,$nivel, $sentencia = NULL){
			if ($this->actionUpdate==true) {
				$sentencia = " estado=0 ";
			}
			else if($sentencia==NULL){
				$sentencia = " estado=0,consecutivo='' " ;
			}

			if ($nivel>=1) {
				$sql="UPDATE recibo_caja SET $sentencia /*,activo=0*/ WHERE id_empresa=$this->id_empresa AND id=$id_recibo; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_colgaap WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_recibo AND tipo_documento='RC'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				// $sql="DELETE FROM contabilizacion_compra_venta WHERE activo=1 AND";
				// $query=$this->mysql->query($sql,$this->mysql->link);


				$sql="DELETE FROM asientos_niif WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_recibo AND tipo_documento='RC'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				// $sql="DELETE FROM contabilizacion_compra_venta_niif WHERE activo=1 AND";
				// $query=$this->mysql->query($sql,$this->mysql->link);

			}

			if ($nivel>=2) {
				$sql   = "UPDATE ventas_facturas AS CF,
							(SELECT SUM(debito-credito) AS abono,id_documento_cruce,cuenta
								FROM recibo_caja_cuentas
								WHERE activo=1 AND id_recibo_caja='$id_recibo' AND tipo_documento_cruce = 'FV'
								GROUP BY id_documento_cruce
							) AS CE
						SET CF.total_factura_sin_abono=CF.total_factura_sin_abono-CE.abono
						WHERE CF.id=CE.id_documento_cruce
							AND CF.cuenta_pago=CE.cuenta
							AND CF.id_empresa=$this->id_empresa";
				$query = $this->mysql->query($sql,$this->mysql->link);
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