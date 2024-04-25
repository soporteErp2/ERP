<?php

	include '../../../misc/ConnectDb/class.ConnectDb.php';
	/**
	 * @apiDefine Ventas Se requieren permisos de ventas
	 * Para crear, actualizar o eliminar documentos, se requieren los respectivos permisos del modulo de ventas
	 *
	 */
	class ApiRemisiones
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
		private $forzar_ccos = NULL;

		// CONEXION DESARROLLO
		// private $ServidorDb = '192.168.8.2';
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
		 * @api {get} /remisiones/:documento_cliente/:fecha/:fecha_inicio/:fecha_final/:consecutivo/:consecutivo_inicial/:consecutivo_final/:estado Consultar Remisiones
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar remisiones del sistema.
		 * @apiName get_remisiones
		 * @apiGroup Remisiones
		 *
		 * @apiParam {String} documento_cliente Numero de documento del cliente
		 * @apiParam {date} fecha Fecha de la remision
		 * @apiParam {date} fecha_inicio Fecha inicial para filtrar las remisiones
		 * @apiParam {date} fecha_final Fecha final para filtrar las remisiones
		 * @apiParam {String} consecutivo Numero remision completo (Prefijo y consecutivo, Ejemplo: "10")
		 * @apiParam {int} consecutivo_inicial Consecutivo inicial de las remisiones a filtrar, Ejemplo: 1
		 * @apiParam {int} consecutivo_final Consecutivo final de las remisiones a filtrar, Ejemplo: 100
		 * @apiParam {String=" ","pendientes","facturadas"} estado Estado de las remisiones, este campo debe estar vacio si se quiere listar todos los documentos
		 *
		 * @apiSuccess {date} fecha Fecha de la remision en formato "Y-m-d"
		 * @apiSuccess {Int} consecutivo Numero consecutivo de la remision
		 * @apiSuccess {String} consecutivo_completo Consecutivo del documento incluyendo el prefijo
		 * @apiSuccess {String} documento_vendedor Documento del vendedor
		 * @apiSuccess {String} nombre_vendedor Nombre del vendedor
		 * @apiSuccess {String} documento_usuario Documento del usuario que realizo el documento
		 * @apiSuccess {String} usuario Usuario que realizo el documento
		 * @apiSuccess {String} documento_cliente Documento del cliente
		 * @apiSuccess {String} cliente Nombre del cliente
		 * @apiSuccess {String} sucursal_cliente Sucursal del cliente a donde se emite la remision
		 * @apiSuccess {Int} id_sucursal Id de la sucursal del documento
		 * @apiSuccess {String} sucursal Sucursal del documento
		 * @apiSuccess {Int} id_bodega Id de la bodega del documento
		 * @apiSuccess {String} bodega Bodega del documento
		 * @apiSuccess {String} observacion Observacion general del documento
		 * @apiSuccess {String} codigo_centro_costo Codigo del centro de costos
		 * @apiSuccess {String} centro_costo Nombre del centro de costos
		 * @apiSuccess {Double} total_remision Total de toda la remision
		 * @apiSuccess {Object[]} items Listado con los items de la remision
		 * @apiSuccess {String} items.codigo Codigo del item vendido
		 * @apiSuccess {String} items.unidad Unidad de medida del item
		 * @apiSuccess {String} items.nombre Nombre del item vendido
		 * @apiSuccess {Double} items.cantidad Cantidades vendidas del item
		 * @apiSuccess {Double} items.precio Precio por unidad vendida
		 * @apiSuccess {String} items.observaciones Observaciones del item
		 * @apiSuccess {String} items.tipo_descuento Tipo del descuento del item
		 * @apiSuccess {Double} items.descuento Valor del descuento del item
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     [
		 * 	{
		 * 	   "id": "167119",
		 * 	   "fecha": "2018-09-17",
		 * 	   "consecutivo": "980000000",
		 * 	   "documento_vendedor": "11111",
		 * 	   "nombre_vendedor": "VENDEDOR DE PRUEBA",
		 * 	   "documento_usuario": "11111",
		 * 	   "usuario": "USUARIO DE PRUEBA",
		 * 	   "documento_cliente": "900467785",
		 * 	   "cliente": "LOGICALSOFT SAS",
		 * 	   "sucursal_cliente": "CALI",
		 * 	   "id_sucursal": "46",
		 * 	   "sucursal": "Cali(Principal)",
		 * 	   "id_bodega": "141",
		 * 	   "bodega": "Bodega Principal",
		 * 	   "observacion": "",
		 * 	   "codigo_centro_costo" : "",
		 * 	   "centro_costo" : "",
		 * 	   "total_remision": "1545707.07",
		 * 	   "items": [
		 * 	       {
		 * 	           "codigo": "1003010001",
		 * 	           "unidad": "Servicio x 1",
		 * 	           "nombre": "ITEM DE SERVICIO DE PRUEBA",
		 * 	           "cantidad": "1.00",
		 * 	           "precio": "100.00",
		 * 	           "observaciones": "",
		 * 	           "tipo_descuento": "porcentaje",
		 * 	           "descuento": 1,
		 * 	       },
		 * 	       {
		 * 	           "codigo": "2008040001",
		 * 	           "unidad": "Unidad x 1",
		 * 	           "nombre": "ITEM INVENTARIABLE DE PRUEBA",
		 * 	           "cantidad": "21.00",
		 * 	           "precio": "80000.00",
		 * 	           "observaciones": "Item vendido de prueba",
		 * 	           "tipo_descuento": "porcentaje",
		 * 	           "descuento": 0,
		 * 	       }
		 * 	   ]
		 * 	},
		 * 	{
		 * 	   "id": "2040",
		 * 	   "fecha": "2015-01-09",
		 * 	   "consecutivo": "474",
		 * 	   "documento_vendedor": "11111",
		 * 	   "nombre_vendedor": "VENDEDOR DE PRUEBA",
		 * 	   "documento_usuario": "11111",
		 * 	   "usuario": "USUARIO DE PRUEBA",
		 * 	   "documento_cliente": "900467785",
		 * 	   "cliente": "LOGICALSOFT SAS",
		 * 	   "sucursal_cliente": "CALI",
		 * 	   "id_sucursal": "46",
		 * 	   "sucursal": "Cali(Principal)",
		 * 	   "id_bodega": "141",
		 * 	   "bodega": "Bodega Principal",
		 * 	   "observacion": "VALOR CORRESPONDIENTE A 6 CUOTA MES PENDIENTE ",
		 * 	   "codigo_centro_costo" : "0101",
		 * 	   "centro_costo" : "ADMINISTRACION",
		 * 	   "total_remision": "531467",
		 * 	   "items": [
		 * 	       {
		 * 	           "codigo": "6001010001",
		 * 	           "unidad": "Servicio x 1",
		 * 	           "nombre": "ITEM DE SERVICIO DE PRUEBA",
		 * 	           "cantidad": "1.00",
		 * 	           "precio": "458161.00",
		 * 	           "observaciones": "SERVICIO DE PRUEBA PRESTADO",
		 * 	           "tipo_descuento": "porcentaje",
		 * 	           "descuento": 0,
		 * 	       }
		 * 	   ]
		 * 	}
		 *    ]
		 *
		 *
		 *
		 *
		 */
		public function show($data=NULL){
			$count = 0;
			foreach ($data as $campo => $valor) { $count += ($valor<>'')? 1 : 0 ; }
			if ($count<=0){ return array('status'=>false,'detalle'=>'No se envio ningun filtro de busqueda'); }
			if (( $data['fecha_inicio']<>'' || $data['fecha_final']<>'' ) && ( $data['fecha_inicio']=='' || $data['fecha_final']=='' ) ){ return array('status'=>false,'detalle'=>'Para consulta en rango de fecha se debe enviar los dos campos (fecha_inicial y fecha_final)'); }
			if (( $data['consecutivo_inicial']<>'' || $data['consecutivo_final']<>'' ) && ( $data['consecutivo_inicial']=='' || $data['consecutivo_final']=='' ) ){ return array('status'=>false,'detalle'=>'Para consulta en rango de numero de facturas se debe enviar los dos campos (consecutivo_inicial y consecutivo_final)'); }

			$whereRemisiones = '';
			if($data['documento_cliente']<>''){ $whereRemisiones .= " AND nit='$data[documento_cliente]' "; }
			if($data['fecha']<>''){ $whereRemisiones .= " AND fecha_inicio='$data[fecha]' "; }
			if($data['fecha_inicio']<>''){ $whereRemisiones .= " AND fecha_inicio BETWEEN '$data[fecha_inicio]' AND '$data[fecha_final]' "; }
			if($data['consecutivo']<>''){ $whereRemisiones .= " AND consecutivo=$data[consecutivo] "; }
			if($data['consecutivo_inicial']<>''){ $whereRemisiones .= " AND consecutivo BETWEEN $data[consecutivo_inicial] AND $data[consecutivo_final] "; }
			// pendientes - facturadas
			if($data['estado']<>'' && $data['estado']<>' '){ $whereRemisiones .= ($data['estado']=='pendientes')? " AND pendientes_facturar>0 " : " AND pendientes_facturar=0 " ; }

			// total_remision
			$sql="SELECT
					id,
					fecha_inicio AS fecha,
					consecutivo,
					documento_vendedor,
					nombre_vendedor,
					documento_usuario,
					usuario,
					nit AS documento_cliente,
					cliente,
					sucursal_cliente,
					id_sucursal,
					sucursal,
					id_bodega,
					bodega,
					observacion,
					codigo_centro_costo,
					centro_costo
				 FROM ventas_remisiones
				 WHERE activo=1
				 AND id_empresa=$this->id_empresa
				 AND (estado=1 OR estado=2) $whereRemisiones LIMIT 0,2";
			$query=$this->mysql->query($sql,$this->mysql->link);
			// $result['sql'] = $sql;
			while($result[]=$this->mysql->fetch_assoc($query));
			array_pop($result);
			$whereIdRemisiones = '';
			foreach ($result as $key => $arrayResult) {
				$whereIdRemisiones .= ($whereIdRemisiones=="")? " id_remision_venta=$arrayResult[id] " : " OR id_remision_venta=$arrayResult[id] " ;
			}

			$sql="SELECT
						id_remision_venta,
						codigo,
						nombre_unidad_medida,
						cantidad_unidad_medida,
						nombre,
						cantidad,
						costo_unitario,
						IFNULL(observaciones,'') AS observaciones,
						tipo_descuento,
						descuento,
						IFNULL(impuesto,'') AS impuesto,
						IFNULL(valor_impuesto,'') AS valor_impuesto
					FROM ventas_remisiones_inventario WHERE activo=1 AND ($whereIdRemisiones) ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$arrayItems   = array();
			$arrayTotales = array();
			while ($row=$this->mysql->fetch_assoc($query)){
				$descuento = 0;
		    	if ($row['descuento']>0) {
		    		if ($row['tipo_descuento'] == 'porcentaje') {
		    			$descuento = ((($row['cantidad'] * $row['costo_unitario']) * $row['descuento']) / 100);
		    		}
		    		else{
		    			$descuento = $row['descuento'];
		    		}
			    }

				$subtotal = ($row['cantidad'] * $row['costo_unitario']);
				$impuesto = (($subtotal - $descuento) * $row['valor_impuesto']) / 100;

				$arrayTotales[$row['id_remision_venta']]['subtotal']  += $subtotal;
				$arrayTotales[$row['id_remision_venta']]['descuento'] += $descuento;
				$arrayTotales[$row['id_remision_venta']]['iva']       += $impuesto;
				$arrayTotales[$row['id_remision_venta']]['total']     += $subtotal-$descuento;

				$arrayItems[$row['id_remision_venta']][]  = array(
																'codigo'              => $row['codigo'],
																'unidad'              => "$row[nombre_unidad_medida] x $row[cantidad_unidad_medida]",
																'nombre'              => $row['nombre'],
																'cantidad'            => $row['cantidad'],
																'precio'              => $row['costo_unitario'],
																'observaciones'       => $row['observaciones'],
																'tipo_descuento'      => $row['tipo_descuento'],
																'descuento'           => $descuento,
																// 'impuesto'            => $row['impuesto'],
																// 'porcentaje_impuesto' => $row['valor_impuesto'],
																// 'valor_impuesto'      => $impuesto
																);
			}

			foreach ($result as $key => $arrayResult) {
				$result[$key]['total_remision'] = $arrayTotales[$arrayResult['id']]['total'];
				$result[$key]['items']          = $arrayItems[$arrayResult['id']];
			}

			return array('status' => true,'data'=> $result);
		}

		/**
		 * @api {post} /remisiones/ Crear Remision
		 * @apiVersion 1.0.0
		 * @apiDescription Registrar remision en el sistema
		 * @apiName store_remisiones
		 * @apiPermission Ventas
		 * @apiGroup Remisiones
		 *
		 * @apiParam {Date} fecha_documento Fecha de la remision formato (Y-M-D)
		 * @apiParam {Date} fecha_vencimiento Fecha de la remision formato (Y-M-D)
		 * @apiParam {String} documento_cliente Numero del documento del cliente
		 * @apiParam {Int} [id_sucursal_cliente] Id de la sucursal del cliente
		 * @apiParam {String} [documento_vendedor] Numero del documento del vendedor de la remision
		 * @apiParam {Int} id_sucursal Id de la sucursal de la remision (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_bodega Id de la sucursal de la remision (Consultar en el panel de control del sistema)
		 * @apiParam {String} [observacion] Observacion general de la remision
		 * @apiParam {String} [cod_centro_costos] Codigo del centro de costos
		 * @apiParam {Object[]} items Listado de los articulos a remisionar
		 * @apiParam {String} items.codigo Contiene el codigo del item a remisionar
		 * @apiParam {Double} items.cantidad Contiene la cantidad item a remisionar
		 * @apiParam {Double} items.precio Contiene el precio de venta del item a remisionar
		 * @apiParam {Double} [items.observaciones] Contiene la observacion del item a remisionar
		 * @apiParam {String="porcentaje","pesos"} [items.tipo_descuento] Contiene el tipo de descuento a aplicar al item puede ser porcentaje o pesos
		 * @apiParam {Double} [items.descuento] Contiene el valor del descuento a aplicar al item, y se aplica segun el tipo de descuento
		 * @apiParam {Object[]} [items.receta] Contiene los ingredientes que conforman la receta de este item, estos se envia solo si la receta cambio, de no ser asi no es necesario pues el sistema tomara la que se encuentre configurada
		 * @apiParam {String} items.receta.codigo Contiene el codigo del item que forma parte de la receta
		 * @apiParam {Double} items.receta.cantidad Contiene la cantidad item que forma parte de la receta
		 * @apiParam {Double} items.receta.precio Contiene el precio de venta del item que forma parte de la receta
		 * @apiParam {Double} [items.receta.observaciones] Contiene la observacion del item que forma parte de la receta
		 *
		 *
		 * @apiSuccess {200} success  informacion registrada
		 * @apiSuccess {200} consecutivo  Consecutivo de la remision
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "informacion registrada",
		 *        "consecutivo": "Consecutivo de la remision Ej. 105",
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
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[16]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para crear Remisiones'); }

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			// CAMPOS ( x = opcionales )
			// fecha_documento
			// fecha_vencimiento
			// documento_cliente
			// id_sucursal_cliente x
			// documento_vendedor x
			// id_sucursal
			// id_bodega
			// observacion x
			// cod_centro_costos x
			// items =>
			// codigo
			// cantidad
			// precio
			// observaciones x
			// tipo_descuento x
			// descuento x

			if ($data['fecha_documento']=='' || !isset($data['fecha_documento']) ){ $arrayError[] = "El campo fecha_documento es obligatorio"; }
			if ($data['fecha_vencimiento']=='' || !isset($data['fecha_vencimiento']) ){ $arrayError[] = "El campo fecha_vencimiento es obligatorio"; }
			if ($data['documento_cliente']=='' || !isset($data['documento_cliente'])){ $arrayError[] = "El campo documento cliente  es obligatorio"; }
			$this->arrayCliente = $this->getcliente($data['documento_cliente']);
			if ($this->arrayCliente==false) {  $arrayError[] = "El cliente no existe en el sistema"; }
			$arraySucursalCliente =  $this->getSucursalCliente($this->arrayCliente['id'],$data['id_sucursal_cliente']);
			if ($data['id_sucursal_cliente']<>'') {
				if (!array_key_exists("$data[id_sucursal_cliente]",$arraySucursalCliente)) {
					$arrayError[] = "La sucursal del cliente no existe en el sistema";
				}
			}

			if ($data['documento_vendedor']<>'') {
				$arrayVendedor = $this->getEmpleado($data['documento_vendedor']);
				if (!array_key_exists("$data[documento_vendedor]",$arrayVendedor)) { $arrayError[] = "El vendedor no existe en el sistema"; }
			}

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id sucursal es obligatorio"; }
			if ($data['id_bodega']=='' || !isset($data['id_bodega'])){ $arrayError[] = "El campo id bodega es obligatorio"; }
			$arrayUbicaciones = $this->getSucursales();
			if (!array_key_exists("$data[id_sucursal]",$arrayUbicaciones['sucursales'])) { $arrayError[] = "La sucursal no existe"; }
			if (!array_key_exists("$data[id_bodega]",$arrayUbicaciones['bodegas'])) { $arrayError[] = "La bodega no existe"; }

			$this->id_sucursal = $data['id_sucursal'];
			$this->id_bodega   = $data['id_bodega'];

			// establecer si se fuerza el centro de costos
			if ($data['forzar_ccos']<>'') {
				$this->forzar_ccos = $data['forzar_ccos'];
			}

			// foreach ($data['items'] as $key => $arrayItems){
			// 	if (gettype($arrayItems)=='object') {
			// 		$arrayItems=get_object_vars($arrayItems);
			// 	}
			// 	$arrayCodItems[$arrayItems['codigo']] = $arrayItems['codigo'];
			// }
			// print_r($arrayCodItems);
			// print_r(array_keys($data['items'],"codigo"));
			// print_r(array_keys(array_keys($data['items']),"codigo"));

			$arrayItemsBodega = $this->getItems($data['id_sucursal'],$data['id_bodega']);
			if (empty($data)){ $arrayError[] = "El array con items es obligatorio"; }
			$cont = 0;
			foreach ($data['items'] as $key => $arrayItems){
				if (gettype($arrayItems)=='object') {
					$arrayItems=get_object_vars($arrayItems);
				}
				if ($arrayItems['codigo']=='') { $arrayError[] = "El codigo del item es obligatorio, Item $arrayItems[codigo]"; }
				else if (!array_key_exists("$arrayItems[codigo]",$arrayItemsBodega)) { $arrayError[] = "El item con codigo $arrayItems[codigo] no existe en el sistema"; }
				if ($arrayItems['cantidad']=='') { $arrayError[] = "La cantidad del item es obligatorio,  Item $arrayItems[codigo]"; }
				else if(!is_numeric($arrayItems['cantidad'])) { $arrayError[] = "la cantidad del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				if($arrayItemsBodega[$arrayItems['codigo']]['inventariable']=='true' && $arrayItems['cantidad']>$arrayItemsBodega[$arrayItems['codigo']]['cantidad'] && $this->usuarioPermisos[181]<>true){
					$arrayError[] = "la cantidad del item excede la existencia en inventario,  Item $arrayItems[codigo]";
				}
				if ($arrayItems['precio']=='') { $arrayError[] = "El precio del item es obligatorio, Item $arrayItems[codigo]"; }
				else if (!is_numeric($arrayItems['precio'])) { $arrayError[] = "El precio del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }

				if ($arrayItems['tipo_descuento']<>'' && ($arrayItems['tipo_descuento']<>'porcentaje' && $arrayItems['tipo_descuento']<>'pesos')) {  $arrayError[] = "El campo tipo_descuento no es valido solo puede ser (porcentaje, pesos),  Item $arrayItems[codigo]";  }
				if (($arrayItems['descuento']<>'' && $arrayItems['descuento']>0) && $arrayItems['tipo_descuento']=='' ){  $arrayError[] = "Si aplica descuento debe enviar el tipo descuento,  Item $arrayItems[codigo]"; }
				if ($arrayItems['descuento']<>'' && !is_numeric($arrayItems['descuento'])) { $arrayError[] = "El descuento del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				$arrayItems['tipo_descuento'] = ($arrayItems['tipo_descuento']=='')? "porcentaje" : $arrayItems['tipo_descuento'] ;

				// tipo_descuento Puede ser porcentaje,pesos
				// descuento

				$arrayInsertItems[$cont]['item'] = "(
													'id_remision_insert',
													'".$arrayItemsBodega[$arrayItems['codigo']]['id_item']."',
													'$arrayItems[cantidad]',
													'$arrayItems[cantidad]',
													'$arrayItems[precio]',
													'$arrayItems[observaciones]',
													'$arrayItems[tipo_descuento]',
													'$arrayItems[descuento]',
													0
												)";
				// SI EL ITEM TIENE CONFIGURADA UNA RECETA
				if (is_array($arrayItemsBodega[$arrayItems['codigo']]['receta']) ) {
					// SI SE ENVIA UNA RECETA ENTONCES NO TOMAR LA QUE TRAE CONFIGURADA
					if ( is_array($arrayItems['receta']) ) {
						foreach ($arrayItems['receta'] as $key => $arrayIngredientes) {
							if ($arrayIngredientes['codigo']=='') { $arrayError[] = "El codigo del item es obligatorio, Item $arrayIngredientes[codigo]"; }
							else if (!array_key_exists("$arrayIngredientes[codigo]",$arrayItemsBodega)) { $arrayError[] = "El item con codigo $arrayIngredientes[codigo] no existe en el sistema"; }
							if ($arrayIngredientes['cantidad']=='') { $arrayError[] = "La cantidad del item es obligatorio,  Item $arrayIngredientes[codigo]"; }
							else if(!is_numeric($arrayIngredientes['cantidad'])) { $arrayError[] = "la cantidad del item debe ser un valor entero o decimal,  Item $arrayIngredientes[codigo]"; }
							if ($arrayItemsBodega[$arrayIngredientes['codigo']]['inventariable']=='true' && $arrayIngredientes['cantidad']>$arrayIngredientesBodega[$arrayIngredientes['codigo']]['cantidad'] && $this->usuarioPermisos[181]<>true){
								$arrayError[] = "la cantidad del item excede la existencia en inventario,  Item $arrayIngredientes[codigo]";
							}
							if ($arrayIngredientes['precio']=='') { $arrayError[] = "El precio del item es obligatorio, Item $arrayIngredientes[codigo]"; }
							else if (!is_numeric($arrayIngredientes['precio'])) { $arrayError[] = "El precio del item debe ser un valor entero o decimal,  Item $arrayIngredientes[codigo]"; }

							if ($arrayIngredientes['tipo_descuento']<>'' && ($arrayIngredientes['tipo_descuento']<>'porcentaje' && $arrayIngredientes['tipo_descuento']<>'pesos')) {  $arrayError[] = "El campo tipo_descuento no es valido solo puede ser (porcentaje, pesos),  Item $arrayIngredientes[codigo]";  }
							if (($arrayIngredientes['descuento']<>'' && $arrayIngredientes['descuento']>0) && $arrayIngredientes['tipo_descuento']=='' ){  $arrayError[] = "Si aplica descuento debe enviar el tipo descuento,  Item $arrayIngredientes[codigo]"; }
							if ($arrayIngredientes['descuento']<>'' && !is_numeric($arrayIngredientes['descuento'])) { $arrayError[] = "El descuento del item debe ser un valor entero o decimal,  Item $arrayIngredientes[codigo]"; }
							$arrayIngredientes['tipo_descuento'] = ($arrayIngredientes['tipo_descuento']=='')? "porcentaje" : $arrayIngredientes['tipo_descuento'] ;

							$arrayInsertItems[$cont]['receta'] .= "(
																	'id_remision_insert',
																	'".$arrayItemsBodega[$arrayIngredientes['codigo']]['id_item']."',
																	'$arrayIngredientes[cantidad]',
																	'$arrayIngredientes[cantidad]',
																	'$arrayIngredientes[precio]',
																	'$arrayIngredientes[observaciones]',
																	'$arrayIngredientes[tipo_descuento]',
																	'$arrayIngredientes[descuento]',
																	'id_items_receta'
																),";
						}
					}
					// SI NO SE ENVIA UNA RECETA, TOMAR LA CONFIGURADA
					else{
						foreach ($arrayItemsBodega[$arrayItems['codigo']]['receta'] as $key => $arrayIngredientes) {
							$arrayInsertItems[$cont]['receta'] .= "(
																	'id_remision_insert',
																	'$arrayIngredientes[id_item_materia_prima]',
																	'$arrayIngredientes[cantidad_item_materia_prima]',
																	'$arrayIngredientes[cantidad_item_materia_prima]',
																	'".$arrayItemsBodega[$arrayIngredientes['codigo_item_materia_prima']]['costos']."',
																	'',
																	'porcentaje',
																	'0',
																	'id_items_receta'
																),";
						}
					}
				}

				$cont++;
			}
			// print_r($arrayItemsBodega);
        	// return array('status'=>200);
			if ($data['cod_centro_costos']<>''){
				$arrayCcos = $this->getCentroCostos($data['cod_centro_costos']);
				if ($arrayCcos==false) {  $arrayError[] = "El centro de costos relacionados no existe en el sistema"; }
			}
			$consecutivo = $this->getConsecutivo();
			if ($consecutivo<=0) { $arrayError[] = "No se encontro una configuracion de consecutivos valida (Cod. Error 401) "; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }
			$json = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']) ;
			$random = $this->random();
			$sql   = "INSERT INTO ventas_remisiones
							(
								random,
								id_empresa,
								id_sucursal,
								sucursal,
								id_bodega,
								bodega,
								consecutivo,
								fecha_registro,
								fecha_inicio,
								fecha_finalizacion,
								id_vendedor,
								documento_vendedor,
								nombre_vendedor,
								id_usuario,
								documento_usuario,
								usuario,
								id_cliente,
								cod_cliente,
								nit,
								cliente,
								id_sucursal_cliente,
								sucursal_cliente,
								observacion,
								estado,
								id_centro_costo,
								codigo_centro_costo,
								centro_costo,
								json_api
							)
                        VALUES
                        	(
                        		'$random',
								'$this->id_empresa',
								'$this->id_sucursal',
								'".$arrayUbicaciones['sucursales'][$data['id_sucursal']]."',
								'$this->id_bodega',
								'".$arrayUbicaciones['bodegas'][$data['id_bodega']]."',
								'$consecutivo',
								'".date('Y-m-d')."',
								'$data[fecha_documento]',
								'$data[fecha_vencimiento]',
								'".$arrayVendedor[$data['documento_vendedor']]['id']."',
								'".$data['documento_vendedor']."',
								'".$arrayVendedor[$data['documento_vendedor']]['nombre']."',
								'$this->id_usuario',
								'$this->documento_usuario',
								'$this->nombre_usuario',
								'".$this->arrayCliente['id']."',
								'".$this->arrayCliente['codigo']."',
								'".$this->arrayCliente['documento']."',
								'".$this->arrayCliente['nombre']."',
								'$arraySucursalCliente[id]',
								'$arraySucursalCliente[nombre]',
								'$data[observacion]',
								'1',
								'$arrayCcos[id]',
								'$arrayCcos[codigo]',
								'$arrayCcos[centro_costo]',
								'$json'
                        	)";
        	// return array('status'=>200);
        	$query = $this->mysql->query($sql,$this->mysql->link);
        	if ($query){
        		$sql="SELECT id FROM ventas_remisiones WHERE activo=1 AND id_empresa=$this->id_empresa AND random='$random' ";
        		$query=$this->mysql->query($sql,$this->mysql->link);
        		$id_remision = $this->mysql->result($query,0,'id');
        		// echo $id_remision;

        		foreach ($arrayInsertItems as $key => $arrayInsert) {
        			$valueInsertItems = $arrayInsert['item'];
					$valueInsertItems = str_replace("id_remision_insert", $id_remision, $valueInsertItems);

	        		$sql="INSERT INTO ventas_remisiones_inventario
	        				(
								id_remision_venta,
								id_inventario,
								cantidad,
								saldo_cantidad,
								costo_unitario,
								observaciones,
								tipo_descuento,
								descuento,
								id_fila_item_receta
	        				)
	        				VALUES $valueInsertItems";
	        		$query=$this->mysql->query($sql,$this->mysql->link);
	        		if (!$query) {
	        			$this->rollBack($id_remision,1);
	        			$arrayError[0]='Se produjo un error al insertar los items del documento en la base de datos';
						$arrayError[1]="Error numero: ".$this->mysql->errno();
		    			$arrayError[2]="Error detalle: ".$this->mysql->error();
		    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
		        		return array('status'=>false,'detalle'=>$arrayError);
	        		}
	        		else{
	        			if($arrayInsert['receta']<>''){
	        				$id_item = $this->mysql->insert_id();
	        				$valueInsertIngredientes = substr($arrayInsert['receta'], 0, -1);
							$valueInsertIngredientes = str_replace("id_remision_insert", $id_remision, $valueInsertIngredientes);
							$valueInsertIngredientes = str_replace("id_items_receta", $id_item, $valueInsertIngredientes);
							$sql="INSERT INTO ventas_remisiones_inventario
			        				(
										id_remision_venta,
										id_inventario,
										cantidad,
										saldo_cantidad,
										costo_unitario,
										observaciones,
										tipo_descuento,
										descuento,
										id_fila_item_receta
			        				)
			        				VALUES $valueInsertIngredientes";
			        		$query=$this->mysql->query($sql,$this->mysql->link);
			        		if (!$query) {
			        			$this->rollBack($id_remision,1);
			        			$arrayError[0]='Se produjo un error al insertar los ingredientes del documento en la base de datos';
								$arrayError[1]="Error numero: ".$this->mysql->errno();
				    			$arrayError[2]="Error detalle: ".$this->mysql->error();
				    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
				        		return array('status'=>false,'detalle'=>$arrayError);
			        		}
	        			}
	        		}
        		}

        		// return array('status'=>200);
    			$validacionInventario = $this->validateInventario($id_remision);
    			if ($validacionInventario['status']==false) { return array('status'=>false,'detalle'=>$validacionInventario['detalle']); }

    			$contabilizacionLocal = $this->setAsientos($id_remision,$consecutivo);
    			if ($contabilizacionLocal['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionLocal['detalle']); }

    			$contabilizacionNiif = $this->setAsientosNiif($id_remision,$consecutivo);
    			if ($contabilizacionNiif['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionNiif['detalle']); }

    			$updateInventario = $this->updateInventario($id_remision);
    			if ($updateInventario['status']==false) { return array('status'=>false,'detalle'=>$updateInventario['detalle']); }

    			$sql="UPDATE configuracion_consecutivos_documentos SET consecutivo=consecutivo+1
						WHERE activo=1 AND id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND documento='remision' ";
    			$query=$this->mysql->query($sql,$this->mysql->link);
    			if (!$query) {
    				$this->rollBack($id_remision,2);
    				$arrayError[0]='Se produjo un error al actualizar el consecutivo de la remision';
					$arrayError[1]="Error numero: ".$this->mysql->errno();
	    			$arrayError[2]="Error detalle: ".$this->mysql->error();
	    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
	        		return array('status'=>false,'detalle'=>$arrayError);
    			}
				return array('status'=>200,'id'=> $id_remision ,'consecutivo'=>$consecutivo);
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
		 * @api {put} /remisiones/ Modificar remision
		 * @apiVersion 1.0.0
		 * @apiDescription Modificar remision en el sistema
		 * @apiName put_remisiones
		 * @apiPermission Ventas
		 * @apiGroup Remisiones
		 *
		 * @apiParam {Date} fecha_documento Fecha de la remision formato (Y-M-D)
		 * @apiParam {Date} fecha_vencimiento Fecha de la remision formato (Y-M-D)
		 * @apiParam {String} documento_cliente Numero del documento del cliente
		 * @apiParam {String} documento_cliente_nuevo Numero del documento del cliente a actualizar, si no se se modifica, enviar el valor anterior
		 * @apiParam {Int} [id_sucursal_cliente] Id de la sucursal del cliente
		 * @apiParam {String} [documento_vendedor] Numero del documento del vendedor de la remision
		 * @apiParam {Int} id_sucursal Id de la sucursal de la remision (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_bodega Id de la sucursal de la remision (Consultar en el panel de control del sistema)
		 * @apiParam {String} [observacion] Observacion general de la remision
		 * @apiParam {String} [cod_centro_costos] Codigo del centro de costos
		 * @apiParam {Object[]} items Listado de los articulos a remisionar
		 * @apiParam {String} items.codigo Contiene el codigo del item a remisionar
		 * @apiParam {Double} items.cantidad Contiene la cantidad item a remisionar
		 * @apiParam {Double} items.precio Contiene el precio de venta del item a remisionar
		 * @apiParam {Double} [items.observaciones] Contiene la observacion del item a remisionar
		 * @apiParam {String="porcentaje","pesos"} [items.tipo_descuento] Contiene el tipo de descuento a aplicar al item puede ser porcentaje o pesos
		 * @apiParam {Double} [items.descuento] Contiene el valor del descuento a aplicar al item, y se aplica segun el tipo de descuento
		 * @apiParam {Object[]} [items.receta] Contiene los ingredientes que conforman la receta de este item, estos se envia solo si la receta cambio, de no ser asi no es necesario pues el sistema tomara la que se encuentre configurada
		 * @apiParam {String} items.receta.codigo Contiene el codigo del item que forma parte de la receta
		 * @apiParam {Double} items.receta.cantidad Contiene la cantidad item que forma parte de la receta
		 * @apiParam {Double} items.receta.precio Contiene el precio de venta del item que forma parte de la receta
		 * @apiParam {Double} [items.receta.observaciones] Contiene la observacion del item que forma parte de la receta
		 *
		 * @apiSuccess {200} success  informacion registrada
		 * @apiSuccess {200} prefijo  Prefijo asignado a la factura
		 * @apiSuccess {200} numero_factura  Consecutivo de la factura
		 * @apiSuccess {200} numero_factura_completo  Consecutivo completo de la factura, incluye prefijo y numero
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "Documento actualizado"
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
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[17]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para modificar recibos'); }
			// $data = json_decode( json_encode($data), true);

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id_sucursal es obligatorio"; }
			if ($data['id_bodega']=='' || !isset($data['id_bodega'])){ $arrayError[] = "El campo id_bodega es obligatorio"; }
			if ($data['consecutivo']=='' || !isset($data['consecutivo'])){ $arrayError[] = "El campo consecutivo es obligatorio"; }
			if ($data['documento_cliente']=='' || !isset($data['documento_cliente'])){ $arrayError[] = "El campo documento_cliente es obligatorio"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[16]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para crear Remisiones'); }

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			$sql="SELECT
					id,
					consecutivo,
					estado
				FROM ventas_remisiones
				WHERE activo=1
					AND id_empresa  = $this->id_empresa
					AND id_sucursal = $data[id_sucursal]
					AND id_bodega   = $data[id_bodega]
					AND nit         = '$data[documento_cliente]'
					AND consecutivo = '$data[consecutivo]' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			if (!$query) {
    			$arrayError[0]='Se produjo un error al verificar el documento en la base de datos';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
        		return array('status'=>false,'detalle'=>$arrayError);
    		}

			$id_documento      = $this->mysql->result($query,0,'id');
			$consecutivo       = $this->mysql->result($query,0,'consecutivo');
			$estado            = $this->mysql->result($query,0,'estado');
			$this->id_bodega   = $data['id_bodega'];
			$this->id_sucursal = $data['id_sucursal'];

			if ($id_documento=='' || $id_documento==0) { $arrayError[] = "La remision no existe en el sistema"; }
			if ($estado==2) { $arrayError[] = "La remision se encuentra bloqueada"; }
			if ($estado==3) { $arrayError[] = "La remision se encuentra anulada"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if ($estado==1){
				$this->rollBack($id_documento,2 );
			}

			// CAMPOS ( x = opcionales )
			// fecha_documento
			// fecha_vencimiento
			// documento_cliente
			// id_sucursal_cliente x
			// documento_vendedor x
			// id_sucursal
			// id_bodega
			// observacion x
			// cod_centro_costos x
			// items =>
			// codigo
			// cantidad
			// precio
			// observaciones x
			// tipo_descuento x
			// descuento x

			if ($data['fecha_documento']=='' || !isset($data['fecha_documento']) ){ $arrayError[] = "El campo fecha_documento es obligatorio"; }
			if ($data['fecha_vencimiento']=='' || !isset($data['fecha_vencimiento']) ){ $arrayError[] = "El campo fecha_vencimiento es obligatorio"; }
			if ($data['documento_cliente_nuevo']=='' || !isset($data['documento_cliente_nuevo'])){ $arrayError[] = "El campo documento cliente  es obligatorio"; }
			$this->arrayCliente = $this->getcliente($data['documento_cliente_nuevo']);
			if ($this->arrayCliente==false) {  $arrayError[] = "El cliente no existe en el sistema"; }
			$arraySucursalCliente =  $this->getSucursalCliente($this->arrayCliente['id'],$data['id_sucursal_cliente']);
			if ($data['id_sucursal_cliente']<>'') {
				if (!array_key_exists("$data[id_sucursal_cliente]",$arraySucursalCliente)) {
					$arrayError[] = "La sucursal del cliente no existe en el sistema";
				}
			}

			if ($data['documento_vendedor']<>'') {
				$arrayVendedor = $this->getEmpleado($data['documento_vendedor']);
				if (!array_key_exists("$data[documento_vendedor]",$arrayVendedor)) { $arrayError[] = "El vendedor no existe en el sistema"; }
			}

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id sucursal es obligatorio"; }
			if ($data['id_bodega']=='' || !isset($data['id_bodega'])){ $arrayError[] = "El campo id bodega es obligatorio"; }
			$arrayUbicaciones = $this->getSucursales();
			if (!array_key_exists("$data[id_sucursal]",$arrayUbicaciones['sucursales'])) { $arrayError[] = "La sucursal no existe"; }
			if (!array_key_exists("$data[id_bodega]",$arrayUbicaciones['bodegas'])) { $arrayError[] = "La bodega no existe"; }

			$this->id_sucursal = $data['id_sucursal'];
			$this->id_bodega   = $data['id_bodega'];

			// foreach ($data['items'] as $key => $arrayItems){
			// 	if (gettype($arrayItems)=='object') {
			// 		$arrayItems=get_object_vars($arrayItems);
			// 	}
			// 	$arrayCodItems[$arrayItems['codigo']] = $arrayItems['codigo'];
			// }
			// print_r($arrayCodItems);
			// print_r(array_keys($data['items'],"codigo"));
			// print_r(array_keys(array_keys($data['items']),"codigo"));

			$arrayItemsBodega = $this->getItems($data['id_sucursal'],$data['id_bodega']);
			if (empty($data)){ $arrayError[] = "El array con items es obligatorio"; }
			$cont = 0;
			foreach ($data['items'] as $key => $arrayItems){
				if (gettype($arrayItems)=='object') {
					$arrayItems=get_object_vars($arrayItems);
				}
				if ($arrayItems['codigo']=='') { $arrayError[] = "El codigo del item es obligatorio, Item $arrayItems[codigo]"; }
				else if (!array_key_exists("$arrayItems[codigo]",$arrayItemsBodega)) { $arrayError[] = "El item con codigo $arrayItems[codigo] no existe en el sistema"; }
				if ($arrayItems['cantidad']=='') { $arrayError[] = "La cantidad del item es obligatorio,  Item $arrayItems[codigo]"; }
				else if(!is_numeric($arrayItems['cantidad'])) { $arrayError[] = "la cantidad del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				if($arrayItemsBodega[$arrayItems['codigo']]['inventariable']=='true' && $arrayItems['cantidad']>$arrayItemsBodega[$arrayItems['codigo']]['cantidad'] && $this->usuarioPermisos[181]<>true){
					$arrayError[] = "la cantidad del item excede la existencia en inventario,  Item $arrayItems[codigo]";
				}
				if ($arrayItems['precio']=='') { $arrayError[] = "El precio del item es obligatorio, Item $arrayItems[codigo]"; }
				else if (!is_numeric($arrayItems['precio'])) { $arrayError[] = "El precio del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }

				if ($arrayItems['tipo_descuento']<>'' && ($arrayItems['tipo_descuento']<>'porcentaje' && $arrayItems['tipo_descuento']<>'pesos')) {  $arrayError[] = "El campo tipo_descuento no es valido solo puede ser (porcentaje, pesos),  Item $arrayItems[codigo]";  }
				if (($arrayItems['descuento']<>'' && $arrayItems['descuento']>0) && $arrayItems['tipo_descuento']=='' ){  $arrayError[] = "Si aplica descuento debe enviar el tipo descuento,  Item $arrayItems[codigo]"; }
				if ($arrayItems['descuento']<>'' && !is_numeric($arrayItems['descuento'])) { $arrayError[] = "El descuento del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				$arrayItems['tipo_descuento'] = ($arrayItems['tipo_descuento']=='')? "porcentaje" : $arrayItems['tipo_descuento'] ;

				// tipo_descuento Puede ser porcentaje,pesos
				// descuento

				$arrayInsertItems[$cont]['item'] = "(
													'id_remision_insert',
													'".$arrayItemsBodega[$arrayItems['codigo']]['id_item']."',
													'$arrayItems[cantidad]',
													'$arrayItems[cantidad]',
													'$arrayItems[precio]',
													'$arrayItems[observaciones]',
													'$arrayItems[tipo_descuento]',
													'$arrayItems[descuento]',
													0
												)";
				// SI EL ITEM TIENE CONFIGURADA UNA RECETA
				if (is_array($arrayItemsBodega[$arrayItems['codigo']]['receta']) ) {
					// SI SE ENVIA UNA RECETA ENTONCES NO TOMAR LA QUE TRAE CONFIGURADA
					if ( is_array($arrayItems['receta']) ) {
						foreach ($arrayItems['receta'] as $key => $arrayIngredientes) {
							if ($arrayIngredientes['codigo']=='') { $arrayError[] = "El codigo del item es obligatorio, Item $arrayIngredientes[codigo]"; }
							else if (!array_key_exists("$arrayIngredientes[codigo]",$arrayItemsBodega)) { $arrayError[] = "El item con codigo $arrayIngredientes[codigo] no existe en el sistema"; }
							if ($arrayIngredientes['cantidad']=='') { $arrayError[] = "La cantidad del item es obligatorio,  Item $arrayIngredientes[codigo]"; }
							else if(!is_numeric($arrayIngredientes['cantidad'])) { $arrayError[] = "la cantidad del item debe ser un valor entero o decimal,  Item $arrayIngredientes[codigo]"; }
							if ($arrayItemsBodega[$arrayIngredientes['codigo']]['inventariable']=='true' && $arrayIngredientes['cantidad']>$arrayIngredientesBodega[$arrayIngredientes['codigo']]['cantidad'] && $this->usuarioPermisos[181]<>true){
								$arrayError[] = "la cantidad del item excede la existencia en inventario,  Item $arrayIngredientes[codigo]";
							}
							if ($arrayIngredientes['precio']=='') { $arrayError[] = "El precio del item es obligatorio, Item $arrayIngredientes[codigo]"; }
							else if (!is_numeric($arrayIngredientes['precio'])) { $arrayError[] = "El precio del item debe ser un valor entero o decimal,  Item $arrayIngredientes[codigo]"; }

							if ($arrayIngredientes['tipo_descuento']<>'' && ($arrayIngredientes['tipo_descuento']<>'porcentaje' && $arrayIngredientes['tipo_descuento']<>'pesos')) {  $arrayError[] = "El campo tipo_descuento no es valido solo puede ser (porcentaje, pesos),  Item $arrayIngredientes[codigo]";  }
							if (($arrayIngredientes['descuento']<>'' && $arrayIngredientes['descuento']>0) && $arrayIngredientes['tipo_descuento']=='' ){  $arrayError[] = "Si aplica descuento debe enviar el tipo descuento,  Item $arrayIngredientes[codigo]"; }
							if ($arrayIngredientes['descuento']<>'' && !is_numeric($arrayIngredientes['descuento'])) { $arrayError[] = "El descuento del item debe ser un valor entero o decimal,  Item $arrayIngredientes[codigo]"; }
							$arrayIngredientes['tipo_descuento'] = ($arrayIngredientes['tipo_descuento']=='')? "porcentaje" : $arrayIngredientes['tipo_descuento'] ;

							$arrayInsertItems[$cont]['receta'] .= "(
																	'id_remision_insert',
																	'".$arrayItemsBodega[$arrayIngredientes['codigo']]['id_item']."',
																	'$arrayIngredientes[cantidad]',
																	'$arrayIngredientes[cantidad]',
																	'$arrayIngredientes[precio]',
																	'$arrayIngredientes[observaciones]',
																	'$arrayIngredientes[tipo_descuento]',
																	'$arrayIngredientes[descuento]',
																	'id_items_receta'
																),";
						}
					}
					// SI NO SE ENVIA UNA RECETA, TOMAR LA CONFIGURADA
					else{
						foreach ($arrayItemsBodega[$arrayItems['codigo']]['receta'] as $key => $arrayIngredientes) {
							$arrayInsertItems[$cont]['receta'] .= "(
																	'id_remision_insert',
																	'$arrayIngredientes[id_item_materia_prima]',
																	'$arrayIngredientes[cantidad_item_materia_prima]',
																	'$arrayIngredientes[cantidad_item_materia_prima]',
																	'".$arrayItemsBodega[$arrayIngredientes['codigo_item_materia_prima']]['costos']."',
																	'',
																	'porcentaje',
																	'0',
																	'id_items_receta'
																),";
						}
					}
				}

				$cont++;
			}
			// print_r($arrayItemsBodega);
        	// return array('status'=>200);
			if ($data['cod_centro_costos']<>''){
				$arrayCcos = $this->getCentroCostos($data['cod_centro_costos']);
				if ($arrayCcos==false) {  $arrayError[] = "El centro de costos relacionados no existe en el sistema"; }
			}

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }
			$json = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']);

			$updateItemsRemision = $this->updateItemsRemision($id_documento);
			if ($updateItemsRemision['status']==false) { return array('status'=>false,'detalle'=>$updateItemsRemision['detalle']); }

			$sql="UPDATE ventas_remisiones
						SET
							fecha_inicio        = '$data[fecha_documento]',
							fecha_finalizacion  = '$data[fecha_vencimiento]',
							id_vendedor         = '".$arrayVendedor[$data['documento_vendedor']]['id']."',
							documento_vendedor  = '".$data['documento_vendedor']."',
							nombre_vendedor     = '".$arrayVendedor[$data['documento_vendedor']]['nombre']."',
							id_usuario          = '$this->id_usuario',
							documento_usuario   = '$this->documento_usuario',
							usuario             = '$this->nombre_usuario',
							id_cliente          = '".$this->arrayCliente['id']."',
							cod_cliente         = '".$this->arrayCliente['codigo']."',
							nit                 = '".$this->arrayCliente['documento']."',
							cliente             = '".$this->arrayCliente['nombre']."',
							id_sucursal_cliente = '$arraySucursalCliente[id]',
							sucursal_cliente    = '$arraySucursalCliente[nombre]',
							observacion         = '$data[observacion]',
							id_centro_costo     = '$arrayCcos[id]',
							codigo_centro_costo = '$arrayCcos[codigo]',
							centro_costo        = '$arrayCcos[centro_costo]',
							estado              = '1',
							json_api            = '$json'
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_documento";
        	$query = $this->mysql->query($sql,$this->mysql->link);

        	if ($query){

        		foreach ($arrayInsertItems as $key => $arrayInsert) {
        			$valueInsertItems = $arrayInsert['item'];
					$valueInsertItems = str_replace("id_remision_insert", $id_documento, $valueInsertItems);

	        		$sql="INSERT INTO ventas_remisiones_inventario
	        				(
								id_remision_venta,
								id_inventario,
								cantidad,
								saldo_cantidad,
								costo_unitario,
								observaciones,
								tipo_descuento,
								descuento,
								id_fila_item_receta
	        				)
	        				VALUES $valueInsertItems";
	        		$query=$this->mysql->query($sql,$this->mysql->link);
	        		if (!$query) {
	        			$this->rollBack($id_documento,1);
	        			$arrayError[0]='Se produjo un error al insertar los items del documento en la base de datos';
						$arrayError[1]="Error numero: ".$this->mysql->errno();
		    			$arrayError[2]="Error detalle: ".$this->mysql->error();
		    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
		        		return array('status'=>false,'detalle'=>$arrayError);
	        		}
	        		else{
	        			if($arrayInsert['receta']<>''){
	        				$id_item = $this->mysql->insert_id();
	        				$valueInsertIngredientes = substr($arrayInsert['receta'], 0, -1);
							$valueInsertIngredientes = str_replace("id_remision_insert", $id_documento, $valueInsertIngredientes);
							$valueInsertIngredientes = str_replace("id_items_receta", $id_item, $valueInsertIngredientes);
							$sql="INSERT INTO ventas_remisiones_inventario
			        				(
										id_remision_venta,
										id_inventario,
										cantidad,
										saldo_cantidad,
										costo_unitario,
										observaciones,
										tipo_descuento,
										descuento,
										id_fila_item_receta
			        				)
			        				VALUES $valueInsertIngredientes";
			        		$query=$this->mysql->query($sql,$this->mysql->link);
			        		if (!$query) {
			        			$this->rollBack($id_documento,1);
			        			$arrayError[0]='Se produjo un error al insertar los ingredientes del documento en la base de datos';
								$arrayError[1]="Error numero: ".$this->mysql->errno();
				    			$arrayError[2]="Error detalle: ".$this->mysql->error();
				    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
				        		return array('status'=>false,'detalle'=>$arrayError);
			        		}
	        			}
	        		}
        		}

        		// return array('status'=>200);
    			$validacionInventario = $this->validateInventario($id_documento);
    			if ($validacionInventario['status']==false) { return array('status'=>false,'detalle'=>$validacionInventario['detalle']); }

    			$contabilizacionLocal = $this->setAsientos($id_documento,$consecutivo);
    			if ($contabilizacionLocal['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionLocal['detalle']); }

    			$contabilizacionNiif = $this->setAsientosNiif($id_documento,$consecutivo);
    			if ($contabilizacionNiif['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionNiif['detalle']); }

    			$updateInventario = $this->updateInventario($id_documento);
    			if ($updateInventario['status']==false) { return array('status'=>false,'detalle'=>$updateInventario['detalle']); }

				return array('status'=>200);
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
		 * @api {delete} /remisiones/ Anular Remision
		 * @apiVersion 1.0.0
		 * @apiDescription Anular remisiones en el sistema.
		 * @apiName delete_remisiones
		 * @apiPermission Ventas
		 * @apiGroup Remisiones
		 *
		 * @apiParam {String} id_sucursal Id de la sucursal del documento (Consultar en el panel de control del sistema)
		 * @apiParam {String} id_bodega Id de la bodega del documento (Consultar en el panel de control del sistema)
		 * @apiParam {String} consecutivo Consecutivo de la remision Ejemplo: "10"
		 * @apiParam {String} documento_cliente Documento del cliente de la remision
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
			if ($this->usuarioPermisos[18]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para anular remisiones'); }
			// $data = json_decode($data, true);
			// id_sucursal *
			// id_bodega *
			// consecutivo *
			// documento_cliente *
			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id_sucursal es obligatorio"; }
			if ($data['id_bodega']=='' || !isset($data['id_bodega'])){ $arrayError[] = "El campo id_bodega es obligatorio"; }
			if ($data['consecutivo']=='' || !isset($data['consecutivo'])){ $arrayError[] = "El campo consecutivo es obligatorio"; }
			if ($data['documento_cliente']=='' || !isset($data['documento_cliente'])){ $arrayError[] = "El campo documento_cliente es obligatorio"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			$sql="SELECT
					id,
					estado
				FROM ventas_remisiones
				WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_sucursal=$data[id_sucursal]
					AND id_bodega=$data[id_bodega]
					AND nit='$data[documento_cliente]'
					AND consecutivo='$data[consecutivo]' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			if (!$query) {
    			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
        		return array('status'=>false,'detalle'=>$arrayError);
    		}

			$id_documento      = $this->mysql->result($query,0,'id');
			$estado            = $this->mysql->result($query,0,'estado');
			$this->id_bodega   = $data['id_bodega'];
			$this->id_sucursal = $data['id_sucursal'];

			if ($id_documento=='' || $id_documento==0) { $arrayError[] = "La remision no existe en el sistema"; }
			if ($estado==3) { $arrayError[] = "La remision ya esta anulada"; }
			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if ($estado==1){
				$this->rollBack($id_documento,2, " estado=3 " );
			}
			else if ($estado==0) {
				$this->rollBack($id_documento,1, " estado=3 " );
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
		 * setConsecutivo Actualizar el consecutivo de la remision
		 */
		public function setConsecutivo(){
			$sql="UPDATE configuracion_consecutivos_documentos SET consecutivo=consecutivo+1
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND documento='remision' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
		}

		/**
		 * getConsecutivo Consultar el consecutivo para la remision
		 * @return Int consecutivo de la nueva remision
		 */
		public function getConsecutivo(){
			$sql="SELECT
					consecutivo
				FROM configuracion_consecutivos_documentos
				WHERE activo=1 AND id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND documento='remision'";
			$query = $this->mysql->query($sql,$this->mysql->link);
			$consecutivo = $this->mysql->result($query,0,'consecutivo');
			return $consecutivo;
		}

		/**
		 * getTerceros Consultar el cliente del sistema
		 * @param  String $documento Documento del cliente a consultar
		 * @return Array Array con la informacion del vendedor
		 */
		public function getcliente($documento){
			$sql="SELECT id,numero_identificacion,codigo,nombre FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa AND numero_identificacion='$documento'";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$arrayTemp['id']        = $this->mysql->result($query,0,'id');
			$arrayTemp['documento'] = $this->mysql->result($query,0,'numero_identificacion');
			$arrayTemp['codigo']    = $this->mysql->result($query,0,'codigo');
			$arrayTemp['nombre']    = $this->mysql->result($query,0,'nombre');
			return ($arrayTemp['id']>0)? $arrayTemp : false;
		}

		/**
		 * getSucursalCliente Consultar la sucursal del cliente
		 * @param  int $id_cliente Id del cliente
		 * @param  int $id_sucursal Id con la sucursal del cliente
		 * @return Array Array con la informacion de la sucursal
		 */
		public function getSucursalCliente($id_cliente,$id_sucursal){
			if ($id_sucursal>0) {
				$sql="SELECT id,nombre FROM terceros_direcciones WHERE activo=1 AND id_tercero=$id_cliente AND id=$id_sucursal";
			}
			else{
				$sql="SELECT id,nombre FROM terceros_direcciones WHERE activo=1 AND id_tercero=$id_cliente AND direccion_principal=1";
			}

			$query=$this->mysql->query($sql,$this->mysql->link);
			$arrayTemp['id']     = $this->mysql->result($query,0,'id');
			$arrayTemp['nombre'] = $this->mysql->result($query,0,'nombre');

			return $arrayTemp;
		}

		/**
		 * getEmpleados Consultar el empleado
		 * @param String $documento Documento del empleado a consultar
		 * @return Array Array con los datos del empleado o usuario
		 */
		public function getEmpleado($documento){
			$sql="SELECT id,documento,nombre FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND documento='$documento'";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$arrayTemp[$documento] =  $this->mysql->result($query, 'id');
			$arrayTemp[$documento] =  $this->mysql->result($query, 'nombre');
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
		 * getCentroCostos Consultar el centro de costos para la remision
		 * @param  String $cod_centro_costos Codigo del centro de costos a buscar
		 * @return Array Array con la informacion del centro de costos
		 */
		public function getCentroCostos($cod_centro_costos){
			$sql="SELECT id,codigo,nombre
				FROM centro_costos WHERE activo=1 AND id_empresa=$this->id_empresa AND codigo=$cod_centro_costos";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$arrayTemp['id']     = $this->mysql->result($query,0,'id');
			$arrayTemp['codigo'] = $this->mysql->result($query,0,'codigo');
			$arrayTemp['centro_costo'] = $this->mysql->result($query,0,'nombre');
			return ($arrayTemp['id']>0)? $arrayTemp : false;
		}

		/**
		 * getItems Consultar todos los items de la empresa
		 * @param  Int $id_sucursal Id de la sucursal del inventario
		 * @param  Int $id_bodega   Id de la bodega del inventario
		 * @return Array Array con los items de la sucursal y bodega de la empresa
		 */
		public function getItems($id_sucursal,$id_bodega){
			$sql="SELECT
					id,
					id_item,
					codigo,
					code_bar,
					nombre_equipo,
					unidad_medida,
					cantidad_unidades,
					costos,
					precio_venta,
					cantidad,
					inventariable,
					estado_compra,
					estado_venta
				FROM inventario_totales
				WHERE activo=1 AND id_empresa=$this->id_empresa AND id_sucursal=$id_sucursal AND id_ubicacion=$id_bodega";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($row=$this->mysql->fetch_assoc($query)){
				$data[$row['codigo']]=$row;
				$whereItems .= ($whereItems=='')? " id_item=$row[id_item]" : " OR id_item=$row[id_item]" ;
			}

			$sql="SELECT
					id_item,
					codigo_item,
					id_item_materia_prima,
					codigo_item_materia_prima,
					nombre_item_materia_prima,
					cantidad_item_materia_prima
				FROM items_recetas WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereItems)";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($row=$this->mysql->fetch_assoc($query)){
				$data[$row['codigo_item']]['receta'][]=$row;
			}
			return $data;
		}

		/**
		 * validateInventario Validar cantidades en inventario
		 * @param  Int $id_factura       Id de la factura a validar
		 * @return Array Si se produce un error se retorna un array con el error
		 */
		public function validateInventario($id_documento){
			$cantidadPermitida = 0;
			$cantidadMayor     = 0;
			$sumaCantidad      = 0;
			$codigo            = 0;

			$sql = "SELECT
							TI.id_inventario,
							Sum(TI.cantidad) AS suma_cantidad,
							TI.nombre,
							TIT.id_item,
							TIT.cantidad,
							TIT.codigo AS codigo
						FROM
							ventas_remisiones_inventario AS TI,
							inventario_totales AS TIT
						WHERE TI.activo = 1
							AND TI.id_remision_venta = '$id_documento'
							AND TIT.id_item      = TI.id_inventario
							AND TIT.id_sucursal  = '$this->id_sucursal'
							AND TIT.id_ubicacion = '$this->id_bodega'
							AND TIT.inventariable = 'true'
						GROUP BY TI.id_inventario
						HAVING Sum(TI.cantidad) > TIT.cantidad
						LIMIT 0,1 ";
			$query     = $this->mysql->query($sql,$this->mysql->link);

			$sumaCantidad      = $this->mysql->result($query,0,'suma_cantidad');
			$cantidadPermitida = $this->mysql->result($query,0,'cantidad');
			$codigo            = $this->mysql->result($query,0,'codigo');

			if ($codigo > 0 && $this->usuarioPermisos[181]<>true){
				$this->rollBack($id_documento,1);
				return array('status' => false, 'detalle'=> "Hay $sumaCantidad unidades de inventario para el item $codigo, lo maximo permitido en ventas de este inventario es $cantidadPermitida unidades" );
			}
			else{ return array('status' => true); }
		}

		/**
		 * setAsientos Contabilizar la factura en norma local
		 * @param Int $id_factura        Id de la factura a causar
		 * @param Array $arrayCuentaPago Array con la informacion de la cuenta de pago
		 * @return Array Resultado del proceso {status: true o false},{detalle: en caso de ser error se detallara en este campo}
		 */
		public function setAsientos($id_documento,$consecutivo){
			$sql     = "SELECT fecha_inicio,id_cliente,id_centro_costo FROM ventas_remisiones WHERE id=$id_documento AND id_empresa=$this->id_empresa";
			$query   = $this->mysql->query($sql,$this->mysql->link);
			$fecha_inicio    = $this->mysql->result($query,0,'fecha_inicio');
			$id_cliente      = $this->mysql->result($query,0,'id_cliente');
			$id_centro_costo = $this->mysql->result($query,0,'id_centro_costo');
			$decimalesMoneda  = ($this->decimales_moneda >= 0)? $this->decimales_moneda : 0;

			// VALIDACION QUE TODOS LOS ARTICULOS INVENTARIABLES TENGAN CONFIGURADO LA CUENTA INVENTARIO Y COSTOS
			$contNoContabilizacion = 0;
			$consultaCuentas = "SELECT COUNT(VRI.id) AS cont
								FROM ventas_remisiones_inventario AS VRI, items AS I
								WHERE VRI.activo = 1
									AND VRI.id_remision_venta = '$id_documento'
									AND VRI.id_inventario= I.id
									AND I.inventariable= 'true'
									AND id_inventario NOT IN (
											SELECT id_items
											FROM items_cuentas
											WHERE activo=1
												AND id_empresa='$this->id_empresa'
												AND estado='venta'
												AND (descripcion='costo' OR descripcion='contraPartida_costo')
										)
								GROUP BY VRI.activo=1";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$num_cuentas = $this->mysql->result($query,0,'cont');

    		if($num_cuentas>0) {
				$arrayError[]='Hay articulos inventariables que no tiene configuracion contable, contabilidad colgaap';
    			$this->rollback($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}

			//================================ CONTABILIZACION CUENTAS COLGAAP ================================//
			/***************************************************************************************************/
			$sql = "SELECT VRI.id,VRI.id_inventario, IC.id_puc, IC.puc, IC.tipo AS estado, VRI.costo_unitario AS costo, VRI.cantidad, IC.descripcion
					FROM ventas_remisiones_inventario AS VRI, items_cuentas AS IC
					WHERE VRI.activo = 1
					AND VRI.id_remision_venta = '$id_documento'
					AND VRI.id_inventario = IC.id_items
					AND IC.activo         = 1
					AND IC.estado         = 'venta'
					AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
			$query = $this->mysql->query($sql,$this->mysql->link);
			$valueInsertContabilizacion = '';
			while ($row = $this->mysql->fetch_array($query)) {
				$cuenta          = $row['puc'];
				$id_item         = $row['id_inventario'];
				$idDocInventario = $row['id'];
				$id_puc          = $row['id_puc'];
				$estado          = $row['estado'];
				$costo           = $row['costo'] * $row['cantidad'];

				$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

				if(is_nan($arrayAsiento[$cuenta][$estadoAsiento])){ $arrayAsiento[$cuenta][$estadoAsiento] = 0; }
				$arrayAsiento[$cuenta][$estadoAsiento] += $costo;

				$arrayCuenta['debito']  = 0;
				$arrayCuenta['credito'] = 0;

				$valueInsertContabilizacion .= "('$id_item',
												'$id_puc',
												'$cuenta',
												'".$row['estado']."',
												'".$row['descripcion']."',
												'$id_documento',
												'RV',
												'$this->id_empresa',
												'$this->id_sucursal',
												'$this->id_bodega'),";
				$wherePuc .= ($wherePuc=='')? "cuenta='$cuenta'" : " OR cuenta='$cuenta' " ;
			}

			// CONSULTAR SI LAS CUENTAS MUEVEN CENTRO DE COSTOS PARA QUE SE CONTABILICE
			$sql="SELECT cuenta,centro_costo FROM puc WHERE activo=1 AND id_empresa=$this->id_empresa AND ($wherePuc) ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$cuenta       = $row['cuenta'];
				$centro_costo = $row['centro_costo'];
				$arrayPuc[$cuenta] = $centro_costo;
			}

			$contAsientos  = 0;
			$globalDebito  = 0;
			$globalCredito = 0;
			$valueInsertAsientos = '';
			// print_r($arrayAsiento);
			foreach ($arrayAsiento as $cuenta => $arrayCuenta) {
				$contAsientos++;
				$globalDebito  += $arrayCuenta['debe'];
				$globalCredito += $arrayCuenta['haber'];

				

				if ($this->forzar_ccos=='true' && $arrayCuenta['haber']>0) {
					$id_centro_costo = $id_centro_costo;
				}
				else{
					$id_centro_costo = ($arrayPuc[$cuenta]=='Si')? $id_centro_costo : '';
				}
				
				$valueInsertAsientos .= "('$id_documento',
											'$consecutivo',
											'RV',
											'Remision de Venta',
											'$id_documento',
											'$consecutivo',
											'RV',
											'$fecha_inicio',
											'".$arrayCuenta['debe']."',
											'".$arrayCuenta['haber']."',
											'$cuenta',
											'$id_cliente',
											'$id_centro_costo',
											'$this->id_sucursal',
											'$this->id_empresa'
										),";
			}

			$globalDebito  = round($globalDebito,$decimalesMoneda);
			$globalCredito = round($globalCredito,$decimalesMoneda);
			if($contAsientos == 0){
				$arrayError[]='Los articulos no tienen una configuracion contable, contabilidad colgaap';
    			$this->rollback($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}
			else if($globalDebito != $globalCredito) {
				$arrayError[]='No se cumple doble partida, Confirme su configuracion en el modulo panel de control, contabilidad colgaap';
    			$this->rollback($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}

			//INSERT ASIENTOS_COLGAAP Y ASIENTOS_POR_ARTICULO
			$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
			$sql   = "INSERT INTO asientos_colgaap (
										id_documento,
										consecutivo_documento,
										tipo_documento,
										tipo_documento_extendido,
										id_documento_cruce,
										numero_documento_cruce,
										tipo_documento_cruce,
										fecha,
										debe,
										haber,
										codigo_cuenta,
										id_tercero,
										id_centro_costos,
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientos";
			$query = $this->mysql->query($sql,$this->mysql->link);
			if(!$query){
				$arrayError[0]='Se produjo un error al insertar la contabilidad del documento';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

				$this->rollBack($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}

			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
			$sql     = "INSERT INTO contabilizacion_compra_venta
							(id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega)
						VALUES $valueInsertContabilizacion";
			$query   = $this->mysql->query($sql,$this->mysql->link);
			if(!$query){
				$arrayError[0]='Se produjo un error al insertar la contabilidad del documento';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

				$this->rollBack($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}

			return array('status'=>true);
		}

		/**
		 * setAsientosNiif Contabilizar la factura en norma Niif
		 * @param Int $id_factura        Id de la factura a causar
		 * @param Array $arrayCuentaPago Array con la informacion de la cuenta de pago
		 * @return Array Resultado del proceso {status: true o false},{detalle: en caso de ser error se detallara en este campo}
		 */
		public function setAsientosNiif($id_documento,$consecutivo){
			$sql     = "SELECT fecha_inicio,id_cliente,id_centro_costo FROM ventas_remisiones WHERE id=$id_documento AND id_empresa=$this->id_empresa";
			$query   = $this->mysql->query($sql,$this->mysql->link);
			$fecha_inicio    = $this->mysql->result($query,0,'fecha_inicio');
			$id_cliente      = $this->mysql->result($query,0,'id_cliente');
			$id_centro_costo = $this->mysql->result($query,0,'id_centro_costo');
			$decimalesMoneda  = ($this->decimales_moneda >= 0)? $this->decimales_moneda : 0;

			$sql = "SELECT VRI.id,VRI.id_inventario, IC.id_puc, IC.puc, IC.tipo AS estado, VRI.costo_unitario AS costo, VRI.cantidad, IC.descripcion
						FROM ventas_remisiones_inventario AS VRI, items_cuentas_niif AS IC
						WHERE VRI.activo = 1
							AND VRI.id_remision_venta = '$id_documento'
							AND VRI.id_inventario = IC.id_items
							AND IC.activo         = 1
							AND IC.estado         = 'venta'
							AND (IC.descripcion   ='costo' OR IC.descripcion='contraPartida_costo')";
			$query = $this->mysql->query($sql,$this->mysql->link);
			$valueInsertContabilizacion = '';
			$wherePuc = '';
			while ($row = $this->mysql->fetch_array($query)) {
				$cuenta          = $row['puc'];
				$id_item         = $row['id_inventario'];
				$idDocInventario = $row['id'];
				$id_puc          = $row['id_puc'];
				$estado          = $row['estado'];
				$costo           = $row['costo'] * $row['cantidad'];

				$estadoAsiento = ($estado=='debito')? 'debe' : 'haber';

				if(is_nan($arrayAsientoNiif[$cuenta][$estadoAsiento])){ $arrayAsientoNiif[$cuenta][$estadoAsiento] = 0; }
				$arrayAsientoNiif[$cuenta][$estadoAsiento] += $costo;

				$arrayCuenta['debito']  = 0;
				$arrayCuenta['credito'] = 0;

				$valueInsertContabilizacion .= "('$id_item',
												'$id_puc',
												'$cuenta',
												'".$row['estado']."',
												'".$row['descripcion']."',
												'$id_documento',
												'RV',
												'$this->id_empresa',
												'$this->id_sucursal',
												'$this->id_bodega'),";
			$wherePuc .= ($wherePuc=='')? "cuenta='$cuenta'" : " OR cuenta='$cuenta' " ;
			}

			// CONSULTAR SI LAS CUENTAS MUEVEN CENTRO DE COSTOS PARA QUE SE CONTABILICE
			$sql="SELECT cuenta,centro_costo FROM puc_niif WHERE activo=1 AND id_empresa=$this->id_empresa AND ($wherePuc) ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$cuenta       = $row['cuenta'];
				$centro_costo = $row['centro_costo'];
				$arrayPucNiif[$cuenta] = $centro_costo;
			}

			$contAsientos  = 0;
			$globalDebito  = 0;
			$globalCredito = 0;
			$valueInsertAsientos = '';
			foreach ($arrayAsientoNiif as $cuenta => $arrayCuenta) {
				$contAsientos++;
				$globalDebito  += $arrayCuenta['debe'];
				$globalCredito += $arrayCuenta['haber'];
				if ($this->forzar_ccos=='true' && $arrayCuenta['haber']>0) {
					$id_centro_costo = $id_centro_costo;
				}
				else{
					$id_centro_costo = ($arrayPucNiif[$cuenta]=='Si')? $id_centro_costo : '';
				}
				$valueInsertAsientos .= "('$id_documento',
											'$consecutivo',
											'RV',
											'Remision de Venta',
											'$id_documento',
											'$consecutivo',
											'RV',
											'$fecha_inicio',
											'".$arrayCuenta['debe']."',
											'".$arrayCuenta['haber']."',
											'$cuenta',
											'$id_cliente',
											'$id_centro_costo',
											'$this->id_sucursal',
											'$this->id_empresa'
										),";
			}
			$globalDebito  = round($globalDebito,$decimalesMoneda);
			$globalCredito = round($globalCredito,$decimalesMoneda);
			if($contAsientos == 0){
				$arrayError[]='Los articulos no tienen una configuracion contable, contabilidad niif';
    			$this->rollback($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}
			else if($globalDebito != $globalCredito){
				$arrayError[]='No se cumple doble partida, Confirme su configuracion en el modulo panel de control, contabilidad niif';
    			$this->rollback($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}

			//INSERT ASIENTOS_COLGAAP Y ASIENTOS_POR_ARTICULO
			$valueInsertAsientos = substr($valueInsertAsientos, 0, -1);
			$sql   = "INSERT INTO asientos_niif (
										id_documento,
										consecutivo_documento,
										tipo_documento,
										tipo_documento_extendido,
										id_documento_cruce,
										numero_documento_cruce,
										tipo_documento_cruce,
										fecha,
										debe,
										haber,
										codigo_cuenta,
										id_tercero,
										id_centro_costos,
										id_sucursal,
										id_empresa)
									VALUES $valueInsertAsientos";
			$query = $this->mysql->query($sql,$this->mysql->link);
			if(!$query){
				$arrayError[0]='Se produjo un error al insertar la contabilidad niif del documento';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

				$this->rollBack($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}

			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);
			$sql   = "INSERT INTO contabilizacion_compra_venta_niif (id_item,id_puc,codigo_puc,caracter,descripcion,id_documento,tipo_documento,id_empresa,id_sucursal,id_bodega)
						VALUES $valueInsertContabilizacion";
			$query = $this->mysql->query($sql,$this->mysql->link);
			if(!$query){
				$arrayError[0]='Se produjo un error al insertar la configuracion contable niif del documento';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

				$this->rollBack($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}

			return array('status'=>true);
    	}

    	/**
    	 * updateInventario Actualizar las unidades de inventario
    	 * @param  Int $id_factura Id de la factura
    	 * @return Array  Si se genera un error se retorna array con el detalle del error
    	 */
    	public function updateInventario($id_documento){
    		$sql   = "UPDATE inventario_totales AS IT, (
							SELECT SUM(cantidad) AS total_remision_venta, id_inventario AS id_item
							FROM ventas_remisiones_inventario
							WHERE id_remision_venta='$id_documento'
								AND activo=1
								AND inventariable='true'
							GROUP BY id_inventario) AS VFI
						SET IT.cantidad=IT.cantidad-VFI.total_remision_venta
						WHERE IT.id_item=VFI.id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$this->id_bodega'";

			$query = $this->mysql->query($sql,$this->mysql->link);
			if(!$query){
				$arrayError[0]='Se produjo un error al actualizar el inventario ';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
    			$this->rollback($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}
			else{ return array('status' => true); }
    	}

    	/**
    	 * updateItemsRemision Dar de baja items de la factura de venta
    	 * @param  Int $id_factura Id de la factura de venta
    	 * @return Array array con el resultado de la ejecucion
    	 */
    	public function updateItemsRemision($id_documento){
    		$sql="UPDATE ventas_remisiones_inventario SET activo=0 WHERE id_remision_venta=$id_documento ";
    		$query=$this->mysql->query($sql,$this->mysql->link);
    		if(!$query){
				$arrayError[0]='Se produjo un error actualizar los items';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
    			$this->rollback($id_documento,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}
			else{ return array('status' => true); }
    	}

		/**
		 * rollBack deshacer los cambios realizados
		 * @param  Int $id_factura Id de la factura a realizar rollback
		 * @param  Int $nivel      Nivel del rollback a realizar
		 */
		public function rollBack($id_remision,$nivel, $sentencia = NULL){
			if ($this->actionUpdate==true) {
				$sentencia = " estado=0 ";
			}
			else if($sentencia==NULL){
				$sentencia = " estado=0,consecutivo=0 " ;
			}

			if ($nivel>=1){
				$sql="UPDATE ventas_remisiones SET $sentencia WHERE id_empresa=$this->id_empresa AND id=$id_remision; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_colgaap WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_remision AND tipo_documento='RV'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_niif WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_remision AND tipo_documento='RV'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);
			}
			if ($nivel>=2){
				$sql   = "UPDATE inventario_totales AS IT,
								(
									SELECT SUM(cantidad) AS total_remision_venta, id_inventario AS id_item
									FROM ventas_remisiones_inventario
									WHERE id_remision_venta='$id_remision'
										AND activo=1
										AND inventariable='true'
									GROUP BY id_inventario
								) AS VFI
						SET IT.cantidad=IT.cantidad+VFI.total_remision_venta
						WHERE IT.id_item=VFI.id_item
	 						AND IT.activo = 1
	 						AND IT.id_ubicacion = '$this->id_bodega'; ";
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