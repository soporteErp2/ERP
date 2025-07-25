<?php

	include '../../../misc/ConnectDb/class.ConnectDb.php';
	/**
	 * @apiDefine Tercero Se requieren permisos de terceros
	 * Para crear, actualizar o eliminar terceros, se requiere permiso al modulo de terceros en el sistema
	 *
	 */
	class ApiTerceros
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
		private $arrayCamposNombres = array('nombre1',
											'nombre',
											'nombre_comercial',
											'nombre2',
											'apellido1',
											'apellido2');

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

		public function index(){
			$sql="SELECT
						id,
						tipo_identificacion AS tipo_documento,
						dv AS digito_verificacion,
						numero_identificacion AS documento,
						nombre,
						nombre_comercial,
						direccion,
						telefono1 AS telefono,
						departamento,
						ciudad,
						email,
						tercero_tributario AS regimen,
						nombre1 AS primer_nombre,
						nombre2 AS segundo_nombre,
						apellido1 AS primer_apellido,
						apellido2 AS segundo_apellido
					FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa LIMIT 0,1";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($data[]=$this->mysql->fetch_assoc($query));
			array_pop($data);
			return $data;
		}

		/**
		 * @api {get} /terceros/:documento Consultar un tercero
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar el tercero del sistema.
		 * @apiName get_terceros
		 * @apiGroup Terceros
		 *
		 * @apiParam {String} documento Documento Unico del Tercero
		 *
		 * @apiSuccess {String} tipo_documento Tipo de documento
		 * @apiSuccess {Int} digito_verificacion Digito de verificacion (NIT)
		 * @apiSuccess {String} documento Numero de documento
		 * @apiSuccess {String} nombre Nombre del tercero
		 * @apiSuccess {String} nombre_comercial Nombre comercial
		 * @apiSuccess {String} direccion Direccion
		 * @apiSuccess {Int} telefono Telefono fijo
		 * @apiSuccess {String} departamento Departamento
		 * @apiSuccess {String} ciudad Ciudad
		 * @apiSuccess {String} email Correo electronico
		 * @apiSuccess {String} regimen Regimen tributario
		 * @apiSuccess {String} primer_nombre Primer nombre
		 * @apiSuccess {String} segundo_nombre Segundo Nombre
		 * @apiSuccess {String} primer_apellido Primer apellido
		 * @apiSuccess {String} segundo_apellido Segundo apellido
		 *
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *	"tipo_documento"      : "C.C.",
		 *	"digito_verificacion" : "",
		 *	"documento"           : "123456",
		 *	"nombre"              : "Jose Luis Rodriquez",
		 *	"nombre_comercial"    : "Jose Luis Rodriquez",
		 *	"direccion"           : "Avenida 123",
		 *	"telefono"            : "3952455",
		 *	"departamento"        : "Valle",
		 *	"ciudad"              : "Cali",
		 *	"email"               : "jose@correo.com",
		 *	"regimen"             : "Simplificado",
		 *	"primer_nombre"       : "Jose",
		 *	"segundo_nombre"      : "Luis",
		 *	"primer_apellido"     : "Rodriguez",
		 *	"segundo_apellido"    : "",
		 *     }
		 *
		 */
		public function show($data=NULL){
			$query  ="SELECT
						tipo_identificacion AS tipo_documento,
						dv AS digito_verificacion,
						numero_identificacion AS documento,
						nombre,
						nombre_comercial,
						direccion,
						telefono1 AS telefono,
						departamento,
						ciudad,
						email,
						tercero_tributario AS regimen,
						nombre1 AS primer_nombre,
						nombre2 AS segundo_nombre,
						apellido1 AS primer_apellido,
						apellido2 AS segundo_apellido
					FROM terceros WHERE numero_identificacion= '$data[documento]' AND id_empresa=$this->id_empresa";
			$result =$this->mysql->query($query, $this->link);
			$data   =array();
			if($this->mysql->num_rows($result)>0){
				while($data[]=$this->mysql->fetch_assoc($result));
				array_pop($data);
			}
			return $data;
		}

		/**
		 * @api {post} /terceros/ Crear Terceros
		 * @apiVersion 1.0.0
		 * @apiDescription Registrar terceros en el sistema
		 * @apiName store_terceros
		 * @apiPermission Tercero
		 * @apiGroup Terceros
		 *
		 * @apiParam (Codigos Identificacion Dian) {Int} 11 Registro civil
		 * @apiParam (Codigos Identificacion Dian) {Int} 12 Tarjeta de identidad
		 * @apiParam (Codigos Identificacion Dian) {Int} 13 Cedula de ciudadania
		 * @apiParam (Codigos Identificacion Dian) {Int} 21 Tarjeta  de extranjeria
		 * @apiParam (Codigos Identificacion Dian) {Int} 22 Cedula de extranjeria
		 * @apiParam (Codigos Identificacion Dian) {Int} 31 Nit
		 * @apiParam (Codigos Identificacion Dian) {Int} 41 Pasaporte
		 * @apiParam (Codigos Identificacion Dian) {Int} 42 Documento de identificacion extranjero
		 * @apiParam (Codigos Identificacion Dian) {Int} 91 NUIP *
		 *
		 * @apiParam {Int{1}="1","2"} [tipo_persona] Tipo de persona 1 si es juridica, 2 si es natural
		 * @apiParam {Int{2}="11","12","13","21","22","31","41","42","91"} cod_documento_dian Codigo Dian del tipo de documento (Ver tabla <b>Codigos Identificacion Dian</b> )
		 * @apiParam {Int} digito_verificacion Digito de verificacion, si aplica
		 * @apiParam {String} documento Numero de documento
		 * @apiParam {String} nombre Nombre
		 * @apiParam {String} nombre_comercial Nombre Comercial
		 * @apiParam {String} [direccion] Direccion
		 * @apiParam {Int} [telefono1] Primer telefono fijo de contacto
		 * @apiParam {Int} [telefono2] Segundo Telefono fijo de contacto
		 * @apiParam {String} [celular1] Primer celular de contacto
		 * @apiParam {String} [celular2] Segundo celular de contacto
		 * @apiParam {Int} id_pais Id del Pais (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_departamento Id del departamento (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_ciudad Id de la ciudad (Consultar en el panel de control del sistema)
		 * @apiParam {String} [pagina_web] Pagina web
		 * @apiParam {String} [email] Correo electronico
		 * @apiParam {String} [representante_legal] Representante legal
		 * @apiParam {Int{2}="11","12","13","21","22","31","41","42","91"} [cod_documento_dian_rl] Codigo Dian del tipo de documento del representante (Ver tabla <b>Codigos Identificacion Dian</b> )
		 * @apiParam {String} [documento_representante] Numero de documento del representante
		 * @apiParam {string{2}="Si","No"} cliente Si es cliente
		 * @apiParam {string{2}="Si","No"} proveedor Si es proveedor
		 * @apiParam {String{2}="Si","No"} [exento_iva] Si no se le aplica iva
		 * @apiParam {String} primer_nombre Primer nombre, opcional si no aplica
		 * @apiParam {String} segundo_nombre Segundo Nombre, opcional si no aplica
		 * @apiParam {String} primer_apellido Primer Apellido, opcional si no aplica
		 * @apiParam {String} segundo_apellido Segundo Apellido, opcional si no aplica
		 *
		 * @apiSuccess {200} success  informacion registrada
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "informacion registrada",
		 *     }
		 * @apiErrorExample Error-Response:
		 * HTTP/1.1 400 Bad Response
		 * {
		 *  "failure": "Ha ocurrido un error",
		 *   "detalle":
		 *    {
		 *     "1234":
		 *      [
		 *       "Detalle del error (Documento repetido, validacion de campos obligatorios, etc.)"
		 *      ],
		 *     "123456789":
		 *      [
		 *       "Detalle del error (Documento repetido, validacion de campos obligatorios, etc.)"
		 *      ],
		 *      ...
		 *    }
		 * }
		 *
		 * @apiError failure Ha ocurrido un error
		 * @apiError detalle
		 *     HTTP/1.1 400 Bad Response
		 *     {
		 *       "documento": "detalle del error",
		 *       "documento": "detalle del error",
		 *       ...
		 *     }
		 */
		public function store($data=NULL){
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[47]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para administrar terceros en el sistema'); }

			$arrayTipoDoc     = $this->getTipoDocumento();
			$arrayTerceros    = $this->getTerceros();
			$arrayUbicaciones = $this->getUbicaciones();
			$arrayError       = array();
			$valueInsert      = '';


			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

    		if (array_key_exists("$data[documento]",$arrayTerceros) && $data['documento']<>'') { $arrayError[$data['documento']][] = "El tercero $data[documento] ya existe";  }
    		if (!array_key_exists("$data[cod_documento_dian]",$arrayTipoDoc)) { $arrayError[$data['documento']][] = "tipo identificacion no valido";  }
    		if ($data['documento']=='') { $arrayError[$data['documento']][] = "Campo No. documento obligatorio";  }
    		if ($data['nombre']=='') { $arrayError[$data['documento']][] = "Campo Nombre obligatorio";  }
    		if ($data['nombre_comercial']=='') { $arrayError[$data['documento']][] = "Campo Nombre Comercial obligatorio";  }
    		if ($data['cliente']=='' || ($data['cliente']<>'No' && $data['cliente']<>'Si') ){ $arrayError[$data['documento']][] = "Campo Cliente invalido";  }
    		if ($data['proveedor']=='' || ($data['proveedor']<>'No' && $data['proveedor']<>'Si') ){ $arrayError[$data['documento']][] = "Campo Proveedor invalido";  }
    		if ($data['id_pais']<>'' && !array_key_exists("$data[id_pais]", $arrayUbicaciones['pais'])) { $arrayError[$data['documento']][] = "Campo pais invalido";  }
    		if ($data['id_departamento']<>'' && !array_key_exists("$data[id_departamento]", $arrayUbicaciones['departamento'][$data['id_pais']])) { $arrayError[$data['documento']][] = "Campo departamento invalido";  }
    		if ($data['id_ciudad']<>'' && !array_key_exists("$data[id_ciudad]", $arrayUbicaciones['ciudad'][$data['id_pais']][$data["id_departamento"]] ) ) { $arrayError[$data['documento']][] = "Campo ciudad invalido";  }
    		if (!empty($arrayError)) {
				return array('status'=>false,'detalle'=>$arrayError);
			}

			$valueInsert .= "(
								'$this->id_empresa',
								'".$arrayTipoDoc[$data['cod_documento_dian']]['id']."',
								'".$arrayTipoDoc[$data['cod_documento_dian']]['nombre']."',
								'$data[digito_verificacion]',
								'$data[documento]',
								'$data[tipo_persona]',
								'".$this->limpiarTexto($data['nombre'])."',
								'".$this->limpiarTexto($data['nombre_comercial'])."',
								'$data[direccion]',
								'$data[telefono1]',
								'$data[telefono2]',
								'$data[celular1]',
								'$data[celular2]',
								'$data[id_pais]',
								'$data[id_departamento]',
								'$data[id_ciudad]',
								'$data[pagina_web]',
								'$data[email]',
								'$data[representante_legal]',
								'".$arrayTipoDoc[$data['cod_documento_dian_rl']]['id']."',
								'".$arrayTipoDoc[$data['cod_documento_dian_rl']]['nombre']."',
								'$data[documento_representante]',
								'$data[cliente]',
								'$data[proveedor]',
								'$data[exento_iva]',
								'".$this->limpiarTexto($data['primer_nombre'])."',
								'".$this->limpiarTexto($data['segundo_nombre'])."',
								'".$this->limpiarTexto($data['primer_apellido'])."',
								'".$this->limpiarTexto($data['segundo_apellido'])."'
							),";

			$valueInsert = substr($valueInsert, 0, -1);
			$sql = "INSERT INTO terceros (
											id_empresa,
											id_tipo_identificacion,
											tipo_identificacion,
											dv,
											numero_identificacion,
											id_tipo_persona_dian,
											nombre,
											nombre_comercial,
											direccion,
											telefono1,
											telefono2,
											celular1,
											celular2,
											id_pais,
											id_departamento,
											id_ciudad,
											web,
											email,
											representante_legal,
											id_tipo_identificacion_representante,
											tipo_identificacion_representante,
											identificacion_representante,
											tipo_cliente,
											tipo_proveedor,
											exento_iva,
											nombre1,
											nombre2,
											apellido1,
											apellido2
										)
						VALUES $valueInsert ";
			$query =$this->mysql->query($sql, $this->link);
			if($query){
				if (empty($arrayError)) {
					return array('status'=>true, 'id_tercero'=>$this->mysql->insert_id());
				}
				else{
					return array('status'=>false,'detalle'=>$arrayError);
				}
			}else{
				if (empty($arrayError)) {
					return array('status'=>false,'detalle'=>'Error al guardar la informacion');
				}
				else{
					return array('status'=>false,'detalle'=>$arrayError);
				}
				// return array('status'=>false);
			}
		}

		/**
		 * @api {put} /terceros/ Modificar Terceros
		 * @apiVersion 1.0.0
		 * @apiDescription Modificar terceros en el sistema.
		 * @apiName update_terceros
		 * @apiPermission Tercero
		 * @apiGroup Terceros
		 *
		 * @apiParam (Codigos Identificacion Dian) {Int} 11 Registro civil
		 * @apiParam (Codigos Identificacion Dian) {Int} 12 Tarjeta de identidad
		 * @apiParam (Codigos Identificacion Dian) {Int} 13 Cedula de ciudadania
		 * @apiParam (Codigos Identificacion Dian) {Int} 21 Tarjeta  de extranjeria
		 * @apiParam (Codigos Identificacion Dian) {Int} 22 Cedula de extranjeria
		 * @apiParam (Codigos Identificacion Dian) {Int} 31 Nit
		 * @apiParam (Codigos Identificacion Dian) {Int} 41 Pasaporte
		 * @apiParam (Codigos Identificacion Dian) {Int} 42 Documento de identificacion extranjero
		 * @apiParam (Codigos Identificacion Dian) {Int} 91 NUIP *
		 *
		 * @apiParam {Int{2}="11","12","13","21","22","31","41","42","91"} cod_documento_dian Codigo Dian del tipo de documento (Ver tabla <b>Codigos Identificacion Dian</b> )
		 * @apiParam {Int} digito_verificacion Digito de verificacion
		 * @apiParam {String} documento Numero de documento
		 * @apiParam {String} nombre Nombre
		 * @apiParam {String} nombre_comercial Nombre Comercial
		 * @apiParam {String} [direccion] Direccion
		 * @apiParam {Int} [telefono1] Primer telefono fijo de contacto
		 * @apiParam {Int} [telefono2] Segundo Telefono fijo de contacto
		 * @apiParam {String} [celular1] Primer celular de contacto
		 * @apiParam {String} [celular2] Segundo celular de contacto
		 * @apiParam {Int} id_pais Id del Pais (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_departamento Id del departamento (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_ciudad Id de la ciudad (Consultar en el panel de control del sistema)
		 * @apiParam {String} [pagina_web] Pagina web
		 * @apiParam {String} [email] Correo electronico
		 * @apiParam {String} [representante_legal] Representante legal
		 * @apiParam {Int{2}="11","12","13","21","22","31","41","42","91"} [cod_documento_dian_rl] Codigo Dian del tipo de documento del representante (Ver tabla <b>Codigos Identificacion Dian</b> )
		 * @apiParam {String} [documento_representante] Numero de documento del representante
		 * @apiParam {string{2}="Si","No"} cliente Si es cliente
		 * @apiParam {string{2}="Si","No"} proveedor Si es proveedor
		 * @apiParam {String{2}="Si","No"} [exento_iva] Si no se le aplica iva
		 * @apiParam {String} primer_nombre Primer nombre, opcional si no aplica
		 * @apiParam {String} segundo_nombre Segundo Nombre, opcional si no aplica
		 * @apiParam {String} primer_apellido Primer Apellido, opcional si no aplica
		 * @apiParam {String} segundo_apellido Segundo Apellido, opcional si no aplica
		 *
		 * @apiSuccess {200} success  informacion registrada
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "informacion registrada",
		 *     }
		 * @apiErrorExample Error-Response:
		 * HTTP/1.1 400 Bad Response
		 * {
		 *  "failure": "Ha ocurrido un error",
		 *   "detalle":
		 *    {
		 *     "1234":
		 *      [
		 *       "Detalle del error (Documento repetido, validacion de campos obligatorios, etc.)"
		 *      ],
		 *     "123456789":
		 *      [
		 *       "Detalle del error (Documento repetido, validacion de campos obligatorios, etc.)"
		 *      ],
		 *      ...
		 *    }
		 * }
		 *
		 * @apiError failure Ha ocurrido un error
		 * @apiError detalle
		 *     HTTP/1.1 400 Bad Response
		 *     {
		 *       "documento": "detalle del error",
		 *       "documento": "detalle del error",
		 *       ...
		 *     }
		 */
		public function update($data=NULL){
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[47]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para administrar terceros en el sistema'); }
			$data = json_decode( json_encode($data), true);
			$arrayTipoDoc     = $this->getTipoDocumento();
			$arrayTerceros    = $this->getTerceros();
			$arrayUbicaciones = $this->getUbicaciones();
			// print_r($data);
			$camposUpdate     = '';

			foreach ($this->arrayCampos as $campoApi => $campoBd) {
				if ($data[$campoApi]<>'') {
					$valorInsert = (in_array($campoBd,$this->arrayCamposNombres))?
										$this->limpiarTexto($data[$campoApi]):
										$data[$campoApi];

					$camposUpdate .= "$campoBd = '".$valorInsert."',";
				}
			}

			$camposUpdate = substr($camposUpdate, 0, -1);

			$sql="UPDATE terceros SET $camposUpdate
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND numero_identificacion='".$data['documento']."';";
			$query=$this->mysql->query($sql,$this->mysql->link);
    		if(!$query) { $arrayError[$data['documento']] = "Error al actualizar  el tercero ".$data['documento']; }

			if (empty($arrayError)) {
					return array('status'=>true);
			}
			else{
				return array('status'=>false,'detalle'=>$arrayError);
			}
		}

		/**
		 * @api {delete} /terceros/ Eliminar Terceros
		 * @apiVersion 1.0.0
		 * @apiDescription Eliminar terceros en el sistema.
		 * @apiName delete_terceros
		 * @apiPermission Tercero
		 * @apiGroup Terceros
		 *
		 * @apiParam {String} documento Documento Unico del Tercero
		 *
		 * @apiSuccess {200} success  Informacion eliminada
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "Informacion eliminada",
		 *     }
		 * @apiErrorExample Error-Response:
		 * HTTP/1.1 400 Bad Response
		 * {
		 *  "failure": "Ha ocurrido un error",
		 *   "detalle":
		 *    {
		 *     "1234":
		 *      [
		 *       "Detalle del error (tercero cruzado, documento invalido, etc.)"
		 *      ],
		 *     "123456789":
		 *      [
		 *       "Detalle del error (tercero cruzado, documento invalido, etc.)"
		 *      ],
		 *      ...
		 *    }
		 * }
		 *
		 * @apiError failure Ha ocurrido un error
		 * @apiError detalle
		 *     HTTP/1.1 400 Bad Response
		 *     {
		 *       "documento": "detalle del error",
		 *       "documento": "detalle del error",
		 *       ...
		 *     }
		 */
		public function delete($data=NULL){
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[47]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para administrar terceros en el sistema'); }
			$data = json_decode( json_encode($data), true);

			$whereDelete = substr($whereDelete, 0, -1);
			$sql="UPDATE terceros SET activo=0
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND numero_identificacion='$data[documento]' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
    		if(!$query) { $arrayError[$data['documento']] = "Error al actualizar  el tercero ".$data['documento'];  }

			if (empty($arrayError)) {
					return array('status'=>true);
			}
			else{
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
		 * getTipoDocumento Consultar los tipos de documentos configurados en el sistema
		 * @return Array
		 */
		public function getTipoDocumento(){
			$sql="SELECT id,nombre,detalle, codigo_tipo_documento_dian FROM tipo_documento WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayTemp[$row['codigo_tipo_documento_dian']] = array('id' => $row['id'],'nombre'=>$row['nombre'], 'detalle'=> $row['detalle'] );
			}
			return $arrayTemp;
		}

		public function getTerceros(){
			$sql="SELECT id,numero_identificacion FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row = $this->mysql->fetch_array($query)) {
				$arrayTemp[$row['numero_identificacion']] = $row['id'];
			}
			return $arrayTemp;
		}

		public function getUbicaciones(){
			$sql   ="SELECT id,ciudad,id_pais,pais,id_departamento,departamento FROM ubicacion_ciudad WHERE activo=1";
			$query =$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_pais         = $row['id_pais'];
				$pais            = $row['pais'];
				$id_departamento = $row['id_departamento'];
				$departamento    = $row['departamento'];
				$id_ciudad       = $row['id'];
				$ciudad          = $row['ciudad'];

				$arrayTemp['pais'][$id_pais]                                 = $pais;
				$arrayTemp['departamento'][$id_pais][$id_departamento]       = $departamento;
				$arrayTemp['ciudad'][$id_pais][$id_departamento][$id_ciudad] = $ciudad;
			}
			return $arrayTemp;
		}

		public function limpiarTexto($valor) {
		    // Asegurar que el texto esté en UTF-8 antes de manipularlo
		    $valor = mb_convert_encoding(
		        $valor,
		        'UTF-8',
		        mb_detect_encoding($valor, 'UTF-8, ISO-8859-1, ISO-8859-15', true)
		    );
		
		    // Reemplazar vocales acentuadas y ñ por su versión sin tilde ni diacríticos
		    $buscar  = array(
		        'á','é','í','ó','ú','ä','ë','ï','ö','ü','à','è','ì','ò','ù','ñ',
		        'Á','É','Í','Ó','Ú','Ä','Ë','Ï','Ö','Ü','À','È','Ì','Ò','Ù','Ñ'
		    );
		    $reempl = array(
		        'a','e','i','o','u','a','e','i','o','u','a','e','i','o','u','n',
		        'A','E','I','O','U','A','E','I','O','U','A','E','I','O','U','N'
		    );
		    $valor = str_replace($buscar, $reempl, $valor);
		
		    // Eliminar signos de puntuación y caracteres especiales
		    $valor = str_replace(
		        array(
		            ',', ';', ':', '!', '?', '¿', '¡', '"', "'", '“', '”',
		            '(', ')', '[', ']', '{', '}', '/', '\\', '-', '_', '°', '@', '#', '$',
		            '%', '*', '+', '=', '<', '>', 'º'
		        ),
		        '',
		        $valor
		    );
		
		    // Reemplazar saltos de línea, retorno de carro y tabulaciones por espacio
		    $valor = str_replace(array("\r", "\n", "\t"), ' ', $valor);
		
		    // Convertir todo el texto a mayúsculas 
		    $valor = strtoupper($valor);
		
		    // Unificar múltiples espacios consecutivos en uno solo
		    $valor = preg_replace('/\s+/', ' ', $valor);
		
		    // Eliminar espacios al principio y al final
		    $valor = trim($valor);
		
		    return $valor;
		}

	}