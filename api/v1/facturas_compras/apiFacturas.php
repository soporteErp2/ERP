<?php

	include '../../../misc/ConnectDb/class.ConnectDb.php';
	/**
	 * @apiDefine Compras Se requieren permisos de compras
	 * Para crear, actualizar o eliminar documentos, se requieren los respectivos permisos del modulo de compras
	 *
	 */
	class ApiFacturas
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
		private $ServidorDb = 'localhost';
		private $NameDb     = 'erp_bd';

		// CONEXION PRODUCCION
		// private $ServidorDb = 'localhost';
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
		 * @api {get} /facturas_compras/:documento_proveedor/:fecha_inicio/:fecha_final/:numero_factura/:numero_factura_inicial/:numero_factura_final/:estado Consultar Facturas
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar facturas del sistema.
		 * @apiName get_facturas
		 * @apiGroup Facturas_compras
		 *
		 * @apiParam {String} documento_proveedor Numero de documento del cliente
		 * @apiParam {date} fecha_inicio Fecha inicial para filtrar las facturas
		 * @apiParam {date} fecha_final Fecha final para filtrar las facturas
		 * @apiParam {String} numero_factura Numero factura del proveedor, sin prefijo
		 * @apiParam {int} consecutivo_inicial Consecutivo inicial de las facturas a filtrar, Ejemplo: 1
		 * @apiParam {int} consecutivo_final Consecutivo final de las facturas a filtrar, Ejemplo: 100
		 * @apiParam {String} estado Estado de las facturas, campo vacio para listar todo, ("pagadas","pendientes")
		 *
		 * @apiSuccess {date} fecha Fecha del documento
		 * @apiSuccess {date} fecha_final Fecha de vencimiento del documento
		 * @apiSuccess {String} prefijo_factura Prefijo de la factura del proveedor
		 * @apiSuccess {Int} numero_factura Numero de la factura del proveedor
		 * @apiSuccess {Int} consecutivo Consecutivo asignado por el sistema al documento
		 * @apiSuccess {String} opcion_cobro Cuenta de cobro
		 * @apiSuccess {String} documento_proveedor Documento del proveedor de la factura
		 * @apiSuccess {String} proveedor Nombre del proveedor de la factura
		 * @apiSuccess {Int} documento_usuario Documento del usuario que creo la factura
		 * @apiSuccess {String} usuario Nombre del usuario que creo el usuario
		 * @apiSuccess {Int} id_sucursal Id de la sucursal del documento
		 * @apiSuccess {String} sucursal Sucursal del documento
		 * @apiSuccess {Int} id_bodega Id de la bodega del documento
		 * @apiSuccess {String} bodega Bodega del documento
		 * @apiSuccess {String} observacion Observacion de la factura de compra
		 * @apiSuccess {Double} total_factura Valor total de la factura de compra
		 * @apiSuccess {Double} saldo_pendiente Saldo restante de la factura
		 * @apiSuccess {Object[]} retenciones Listado con las retenciones
		 * @apiSuccess {String} retenciones.tipo_retencion Tipo de retencion del documento
		 * @apiSuccess {String} retenciones.retencion Retencion Aplicada al documento
		 * @apiSuccess {String} retenciones.porcentaje Porcentaje de la retencion
		 * @apiSuccess {String} retenciones.base Base de aplicacion de la retencion
		 * @apiSuccess {String} retenciones.valor Valor de la retencion
		 * @apiSuccess {Object[]} items Listado con los items de la factura
		 * @apiSuccess {String} items.codigo Codigo del item vendido
		 * @apiSuccess {String} items.unidad Unidad de medida del item
		 * @apiSuccess {String} items.nombre Nombre del item vendido
		 * @apiSuccess {Double} items.cantidad Cantidades vendidas del item
		 * @apiSuccess {Double} items.precio Precio por unidad vendida
		 * @apiSuccess {String} items.observaciones Observaciones del item
		 * @apiSuccess {String} items.tipo_descuento Tipo del descuento del item
		 * @apiSuccess {Double} items.descuento Valor del descuento del item
		 * @apiSuccess {String} items.impuesto Nombre del impuesto del item
		 * @apiSuccess {Double} items.porcentaje Porcentaje del impuesto del item
		 * @apiSuccess {Double} items.valor Valor neto del impuesto del item
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     [
		 *  {
		 *      "id": "2",
		 *      "fecha": "2020-04-27",
		 *      "fecha_final": "2020-05-01",
		 *      "prefijo_factura": "FC",
		 *      "numero_factura": "123",
		 *      "consecutivo": "8184",
		 *      "opcion_cobro": "PROVEEDORES",
		 *      "documento_proveedor": "900467785",
		 *      "proveedor": "LOGICALSOFT  S.A.S",
		 *      "documento_usuario": "55301192",
		 *      "usuario": "KATTYA  ROMERO DAVID",
		 *      "estado": "1",
		 *      "id_sucursal": "2",
		 *      "sucursal": "Sucursal Principal",
		 *      "id_bodega": "1",
		 *      "bodega": "Bodega Principal",
		 *      "observacion": "obs de prueba",
		 *      "total_factura": "100.00",
		 *      "saldo_pendiente": "100.00",
		 *      "retenciones": [
		 *          {
		 *              "tipo_retencion": "ReteFuente",
		 *              "retencion": "RETEFUENTE POR COMPRAS 3.5% (NO DECLARANTES)",
		 *              "porcentaje": "3.500",
		 *              "base": "925000",
		 *              "valor": 0
		 *          }
		 *      ],
		 *      "items": [
		 *          {
		 *              "codigo": "01010101",
		 *              "unidad": "Mililitros x 1",
		 *              "nombre": "ACEITE PIMPINA",
		 *              "cantidad": "49.00",
		 *              "precio": "1.00",
		 *              "observaciones": "",
		 *              "tipo_descuento": "porcentaje",
		 *              "descuento": 0.49,
		 *              "impuesto": "IVA DESC COMPRAS 19%",
		 *              "porcentaje_impuesto": "19.00",
		 *              "valor_impuesto": 9.2169
		 *          },
		 *          {
		 *              "codigo": "010101031",
		 *              "unidad": "Gramos x 1",
		 *              "nombre": "ACEITUNAS MORADAS",
		 *              "cantidad": "12.00",
		 *              "precio": "250.00",
		 *              "observaciones": "obs item proof",
		 *              "tipo_descuento": "porcentaje",
		 *              "descuento": 0,
		 *              "impuesto": "",
		 *              "porcentaje_impuesto": "",
		 *              "valor_impuesto": 0
		 *          }
		 *      ]
		 *  },
		 *  {
		 *      "id": "4",
		 *      "fecha": "2020-04-25",
		 *      "fecha_final": "2020-05-01",
		 *      "prefijo_factura": "FC",
		 *      "numero_factura": "1234",
		 *      "consecutivo": "8185",
		 *      "opcion_cobro": "PROVEEDORES",
		 *      "documento_proveedor": "1143148632",
		 *      "proveedor": "ROMARIO  GARRIDO ",
		 *      "documento_usuario": "55301192",
		 *      "usuario": "KATTYA  ROMERO DAVID",
		 *      "estado": "1",
		 *      "id_sucursal": "2",
		 *      "sucursal": "Sucursal Principal",
		 *      "id_bodega": "1",
		 *      "bodega": "Bodega Principal",
		 *      "observacion": "obs de prueba",
		 *      "total_factura": "100.00",
		 *      "saldo_pendiente": "100.00",
		 *      "retenciones": [
		 *          {
		 *              "tipo_retencion": "ReteFuente",
		 *              "retencion": "RETEFUENTE ARR BIEN MUEBLE",
		 *              "porcentaje": "4.000",
		 *              "base": "1",
		 *              "valor": 120.0396
		 *          }
		 *      ],
		 *      "items": [
		 *          {
		 *              "codigo": "01010101",
		 *              "unidad": "Mililitros x 1",
		 *              "nombre": "ACEITE PIMPINA",
		 *              "cantidad": "1.00",
		 *              "precio": "1.00",
		 *              "observaciones": "",
		 *              "tipo_descuento": "porcentaje",
		 *              "descuento": 0.01,
		 *              "impuesto": "IVA DESC COMPRAS 19%",
		 *              "porcentaje_impuesto": "19.00",
		 *              "valor_impuesto": 0.1881
		 *          },
		 *          {
		 *              "codigo": "010101031",
		 *              "unidad": "Gramos x 1",
		 *              "nombre": "ACEITUNAS MORADAS",
		 *              "cantidad": "12.00",
		 *              "precio": "250.00",
		 *              "observaciones": "obs item proof",
		 *              "tipo_descuento": "porcentaje",
		 *              "descuento": 0,
		 *              "impuesto": "",
		 *              "porcentaje_impuesto": "",
		 *              "valor_impuesto": 0
		 *          }
		 *      ]
		 *  }
		 *	]
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

			$whereFacturas = '';
			if($data['documento_proveedor']<>''){ $whereFacturas .= " AND nit='$data[documento_proveedor]' "; }
			if($data['fecha']<>''){ $whereFacturas .= " AND fecha_inicio='$data[fecha]' "; }
			if($data['fecha_inicio']<>''){ $whereFacturas .= " AND fecha_inicio BETWEEN $data[fecha_inicio] AND $data[fecha_final] "; }
			if($data['numero_factura']<>''){ $whereFacturas .= " AND numero_factura=$data[numero_factura] "; }
			if($data['consecutivo_inicial']<>'' || $data['consecutivo_final']){ $whereFacturas .= " AND consecutivo BETWEEN $data[consecutivo_inicial] AND $data[consecutivo_final] "; }
			// pagadas - pendientes
			if($data['estado']<>''){ $whereFacturas .= ($data['estado']=='pagadas')? " AND total_factura_sin_abono=0 " : " AND total_factura_sin_abono>0 " ; }

			$sql="SELECT
					id,
					fecha_inicio AS fecha,
					fecha_final,
					prefijo_factura,
					numero_factura,
					consecutivo,
					configuracion_cuenta_pago AS opcion_cobro,
					nit AS documento_proveedor,
					proveedor,
					documento_usuario,
					usuario,
					id_sucursal,
					sucursal,
					id_bodega,
					bodega,
					observacion,
					total_factura,
					total_factura_sin_abono AS saldo_pendiente
				 FROM compras_facturas 
				 WHERE activo=1 AND id_empresa=$this->id_empresa AND (estado=1 OR estado=2) $whereFacturas
				 LIMIT 100";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($result[]=$this->mysql->fetch_assoc($query));
			array_pop($result);
			$whereid_facturas = '';
			foreach ($result as $key => $arrayResult) {
				$whereid_facturas .= ($whereid_facturas=="")? " id_factura_compra=$arrayResult[id] " : " OR id_factura_compra=$arrayResult[id] " ;
			}

			$sql="SELECT
						id_factura_compra,
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
					FROM compras_facturas_inventario WHERE activo=1 AND ($whereid_facturas) ";
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

				$arrayTotales[$row['id_factura_compra']]['subtotal']  += $subtotal;
				$arrayTotales[$row['id_factura_compra']]['descuento'] += $descuento;
				$arrayTotales[$row['id_factura_compra']]['iva']       += $impuesto;

				$arrayItems[$row['id_factura_compra']][]  = array(
																'codigo'              => $row['codigo'],
																'unidad'              => "$row[nombre_unidad_medida] x $row[cantidad_unidad_medida]",
																'nombre'              => $row['nombre'],
																'cantidad'            => $row['cantidad'],
																'precio'              => $row['costo_unitario'],
																'observaciones'       => $row['observaciones'],
																'tipo_descuento'      => $row['tipo_descuento'],
																'descuento'           => $descuento,
																'impuesto'            => $row['impuesto'],
																'porcentaje_impuesto' => $row['valor_impuesto'],
																'valor_impuesto'      => $impuesto
																);
			}


			$sql="SELECT id_factura_compra,tipo_retencion,retencion,valor,base FROM compras_facturas_retenciones WHERE activo=1 AND ($whereid_facturas)";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_assoc($query)) {
						    $valor=0;
			    if ($row['tipo_retencion']=='ReteIva') {
			      	if ($row['base']<$arrayTotales[$row['id_factura_compra']]['iva']) {
			        	$valor = ($arrayTotales[$row['id_factura_compra']]['iva']*$row['valor'])/100;
			    	}
			    }
			    else{
			    	if ($row['base']<$arrayTotales[$row['id_factura_compra']]['subtotal']){
			        	$valor = (($arrayTotales[$row['id_factura_compra']]['subtotal']-$arrayTotales[$row['id_factura_compra']]['descuento'])*$row['valor'])/100;
			    	}
			    }

				$arrayRetenciones[$row['id_factura_compra']][] = array(
																		"tipo_retencion" => $row['tipo_retencion'],
																		"retencion"      => $row['retencion'],
																		"porcentaje"     => $row['valor'],
																		"base"           => $row['base'],
																		"valor"          => $valor
																	);
			}

			foreach ($result as $key => $arrayResult) {
				$result[$key]['retenciones']=$arrayRetenciones[$arrayResult['id']];
				$result[$key]['items']=$arrayItems[$arrayResult['id']];
				// unset($result[$key]['id']);
			}

			// exit;
			// print_r($arrayItems);

			return array('status' => true,'data'=> $result);
		}

		/**
		 * @api {post} /facturas_compras/ Crear factura
		 * @apiVersion 1.0.0
		 * @apiDescription Registrar facturas en el sistema
		 * @apiName store_facturas
		 * @apiPermission Compras
		 * @apiGroup Facturas_compras
		 *
		 *
		 * @apiParam {Date} fecha_documento Fecha de la factura formato (Y-M-D)
		 * @apiParam {Date} fecha_vencimiento Fecha de la factura formato (Y-M-D)
		 * @apiParam {String} documento_proveedor Numero del documento del cliente
		 * @apiParam {String} [prefijo_factura] Prefijo de la factura del proveedor
		 * @apiParam {Int} numero_factura Numero de la factura del proveedor
		 * @apiParam {Int} cuenta_pago Cuenta contable de pago de la factura
		 * @apiParam {Int} id_sucursal Id de la sucursal de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_bodega Id de la sucursal de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {String} [observacion] Observacion general de la factura
		 * @apiParam {Double} total_factura Valor completo de la factura
		 * @apiParam {Double} saldo_restante_factura Valor restante de la factura, corresponde al total o si se han hecho pagos al saldo restante
		 * @apiParam {Double} valor_abono Valor de abono o anticipo u otro concepto aplicado al total de la factura, es obligatorio si el saldo de la factura y el total de la factura son diferentes
		 * @apiParam {Object[]} [retenciones] Lista con las retenciones de la factura
		 * @apiParam {Int} retenciones.id Id de la retencion (Consultar en el panel de control del sistema)
		 * @apiParam {String} items.codigo Contiene el codigo del item a facturar
		 * @apiParam {Double} items.cantidad Contiene la cantidad item a facturar
		 * @apiParam {Double} items.precio Contiene el precio de venta del item a facturar
		 * @apiParam {Double} [items.observaciones] Contiene la observacion del item a facturar
		 * @apiParam {String="porcentaje","pesos"} [items.tipo_descuento] Contiene el tipo de descuento a aplicar al item puede ser porcentaje o pesos
		 * @apiParam {Double} [items.descuento] Contiene el valor del descuento a aplicar al item, y se aplica segun el tipo de descuento
		 *
		 * @apiSuccess {200} success  informacion registrada
		 * @apiSuccess {200} consecutivo  Consecutivo automatico asignado por el sistema
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "informacion registrada",
		 *        "consecutivo": "consecutivo de la factura Ej. 2",
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
			if ($this->usuarioPermisos[38]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para crear facturas'); }

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			// CAMPOS OBLIGATORIOS
			// fecha_documento *
			// fecha_vencimiento *
			// documento_proveedor *
			// prefijo_factura *
			// numero_factura *
			// cuenta_pago *
			// id_sucursal *
			// id_bodega *
			// observacion
			// total_factura *
			// saldo_restante_factura *
			// valor_abono *
			// items => array *
			// 			codigo *
			// 			cantidad *
			// 			precio *
			// 			observaciones
			// 			tipo_descuento Puede ser porcentaje,pesos
			// 			descuento
			// retenciones
			// 			id * id de la retencion

			if ($data['fecha_documento']=='' || !isset($data['fecha_documento']) ){ $arrayError[] = "El campo fecha_documento es obligatorio"; }
			if ($data['fecha_vencimiento']=='' || !isset($data['fecha_vencimiento']) ){ $arrayError[] = "El campo fecha_vencimiento es obligatorio"; }
			if ($data['documento_proveedor']=='' || !isset($data['documento_proveedor'])){ $arrayError[] = "El campo documento proveedor  es obligatorio"; }
			$this->id_proveedor = $this->getProveedor($data['documento_proveedor']);
			if ($this->id_proveedor==false) {  $arrayError[] = "El proveedor no existe en el sistema"; }


			if ($data['numero_factura']=='' || !isset($data['numero_factura'])){ $arrayError[] = "El campo numero factura es obligatorio"; }
			$numero_factura_completo = (!empty($data['prefijo_factura']) && $data['prefijo_factura']<>' ')? "$data[prefijo_factura] $numero_factura" : $numero_factura ;

			if ($data['cuenta_pago']=='' || !isset($data['cuenta_pago'])){ $arrayError[] = "El campo cuenta pago es obligatorio"; }
			$arrayCuentaPago = $this->getCuentaPago($data['cuenta_pago']);
			if (!array_key_exists("$data[cuenta_pago]",$arrayCuentaPago)) {
				$arrayError[] = "La cuenta de pago no existe en el sistema";
			}

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id sucursal es obligatorio"; }
			if ($data['id_bodega']=='' || !isset($data['id_bodega'])){ $arrayError[] = "El campo id bodega es obligatorio"; }
			$arrayUbicaciones = $this->getSucursales();
			if (!array_key_exists("$data[id_sucursal]",$arrayUbicaciones['sucursales'])) { $arrayError[] = "La sucursal no existe"; }
			if (!array_key_exists("$data[id_bodega]",$arrayUbicaciones['bodegas'])) { $arrayError[] = "La bodega no existe"; }
			$this->id_sucursal = $data['id_sucursal'];
			$this->id_bodega   = $data['id_bodega'];
			if (!empty($data['retenciones'])){
				$arrayRetenciones=$this->getRetenciones();
				foreach ($data['retenciones'] as $key => $arrayResult) {
					if (gettype($arrayResult)=='object') {
						$arrayResult=get_object_vars($arrayResult);
					}
					if (!array_key_exists("$arrayResult[id]",$arrayRetenciones)) { $arrayError[] = "la retencion con id  $arrayResult[id] no existe en el sistema o no esta disponible en compras"; }
					$valueInsertRetenciones .= "(id_factura_compra_insert,$arrayResult[id]),";
				}
			}
			if (!empty($data['anticipos'])){
				$arrayAnticipos = $this->setAnticipos($data['anticipos']);
				if ($arrayAnticipos['status']==false) { $arrayError[] = $arrayAnticipos['detalle']; }
				else{$valueInsertAnticipos = $arrayAnticipos['sql'];}
			}


			$arrayItemsBodega = $this->getItems($data['id_sucursal'],$data['id_bodega']);
			if (empty($data['items'])){ $arrayError[] = "El array con items es obligatorio"; }
			$arrayCcos = $this->getCcos();

			foreach ($data['items'] as $key => $arrayItems) {
				if (gettype($arrayItems)=='object') {
					$arrayItems=get_object_vars($arrayItems);
				}
				if ($arrayItems['codigo']=='') { $arrayError[] = "El codigo del item es obligatorio, Item $arrayItems[codigo]"; }
				else if (!array_key_exists("$arrayItems[codigo]",$arrayItemsBodega)) { $arrayError[] = "El item con codigo $arrayItems[codigo] no existe en el sistema"; }
				if ($arrayItems['cantidad']==='') { $arrayError[] = "La cantidad del item es obligatorio,  Item $arrayItems[codigo]"; }
				else if(!is_numeric($arrayItems['cantidad'])) { $arrayError[] = "la cantidad del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				
				if ($arrayItems['precio']==='') { $arrayError[] = "El precio del item es obligatorio, Item $arrayItems[codigo]"; }
				else if (!is_numeric($arrayItems['precio'])) { $arrayError[] = "El precio del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }

				if ($arrayItems['tipo_descuento']<>'' && ($arrayItems['tipo_descuento']<>'porcentaje' && $arrayItems['tipo_descuento']<>'pesos')) {  $arrayError[] = "El campo tipo_descuento no es valido solo puede ser (porcentaje, pesos),  Item $arrayItems[codigo]";  }
				if (($arrayItems['descuento']<>'' && $arrayItems['descuento']>0) && $arrayItems['tipo_descuento']=='' ){  $arrayError[] = "Si aplica descuento debe enviar el tipo descuento,  Item $arrayItems[codigo]"; }
				if ($arrayItems['descuento']<>'' && !is_numeric($arrayItems['descuento'])) { $arrayError[] = "El descuento del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				$arrayItems['tipo_descuento'] = ($arrayItems['tipo_descuento']=='')? "porcentaje" : $arrayItems['tipo_descuento'] ;

				if (!array_key_exists("$arrayItems[ccos]",$arrayCcos) && $arrayItems['ccos']<>'') { $arrayError[] = "El item con codigo $arrayItems[codigo] envia el centro de costos $arrayItems[ccos] que no existe en el sistema"; }

				// tipo_descuento Puede ser porcentaje,pesos
				// descuento

				$valueInsertItems .= "(
									'id_factura_compra_insert',
									'".$arrayItemsBodega[$arrayItems['codigo']]['id_item']."',
									'$arrayItems[cantidad]',
									'$arrayItems[precio]',
									'$arrayItems[observaciones]',
									'$arrayItems[tipo_descuento]',
									'$arrayItems[descuento]',
									'".$arrayCcos[$arrayItems['ccos']]['id']."'
									),";

			}


			if ($data['total_factura']==='' /*|| !is_numeric($data['total_factura'])*/ ) { $arrayError[] = "El campo total factura debe tener un valor y debe ser numerico"; }
			if ($data['saldo_restante_factura']==='' /*|| !is_numeric($data['saldo_restante_factura']*1)*/ ) { $arrayError[] = "El campo saldo restante de la factura debe tener un valor y debe ser numerico"; }
			if ($data['total_factura']<>$data['saldo_restante_factura'] && ($data['valor_abono']=='' /*|| !is_numeric($data['valor_abono']*1)*/ ) ) { $arrayError[] = "El campo valor abono debe tener un valor y debe ser numerico pues el saldo de la factura es diferente al total"; }

			$this->arrayConsecutivo = $this->getConsecutivo();
			if (!$this->arrayConsecutivo) { $arrayError[] = "Se produjo un error interno al consultar el consecutivo del documento"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }
			// return array('status'=>false,'detalle'=>'pass');
			$json = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']);
			$random = $this->random();
			$sql   = "INSERT INTO compras_facturas
							(
								id_empresa,
								random,
								fecha_registro,
								fecha_inicio,
								fecha_final,
								fecha_generacion,
								hora_generacion,
								id_configuracion_cuenta_pago,
								configuracion_cuenta_pago,
								id_cuenta_pago,
								cuenta_pago,
								cuenta_pago_niif,
								prefijo_factura,
								numero_factura,
								consecutivo,
								id_proveedor,
								id_usuario,
								estado,
								id_sucursal,
								id_bodega,
								observacion,
								total_factura,
								total_factura_sin_abono,
								json_api
							)
                        VALUES
                        	(
                        		'$this->id_empresa',
								'$random',
								'".date("Y-m-d")."',
								'$data[fecha_documento]',
								'$data[fecha_vencimiento]',
								'".date("Y-m-d")."',
								'".date("H:i:s")."',
								'".$arrayCuentaPago[$data["cuenta_pago"]]["id"]."',
								'".$arrayCuentaPago[$data["cuenta_pago"]]["nombre"]."',
								'".$arrayCuentaPago[$data["cuenta_pago"]]["id_cuenta"]."',
								'".$arrayCuentaPago[$data["cuenta_pago"]]["cuenta"]."',
								'".$arrayCuentaPago[$data["cuenta_pago"]]["cuenta_niif"]."',
								'$data[prefijo_factura]',
								'$data[numero_factura]',
								'".$this->arrayConsecutivo['consecutivo']."',
								'$this->id_proveedor',
								'$this->id_usuario',
								'1',
								'$data[id_sucursal]',
								'$data[id_bodega]',
								'$data[observacion]',
								'$data[total_factura]',
								'$data[saldo_restante_factura]',
								'$json'
                        	)";
        	// return array('status'=>200);
        	$query = $this->mysql->query($sql);
        	if ($query){
        		$sql="SELECT id FROM compras_facturas WHERE activo=1 AND id_empresa=$this->id_empresa AND random='$random' ";
        		$query=$this->mysql->query($sql);
        		$id_factura = $this->mysql->result($query,0,'id');
        		// echo $id_factura;

				$valueInsertItems = substr($valueInsertItems, 0, -1);
				$valueInsertItems = str_replace("id_factura_compra_insert", $id_factura, $valueInsertItems);

        		$sql="INSERT INTO compras_facturas_inventario
        				(
			        		id_factura_compra,
							id_inventario,
							cantidad,
							costo_unitario,
							observaciones,
							tipo_descuento,
							descuento,
							id_centro_costos
        				)
        				VALUES $valueInsertItems";
        		$query=$this->mysql->query($sql,$this->mysql->link);
        		if (!$query) {
        			$this->rollBack($id_factura,1);

        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Items Error)';
					$arrayError[1]="Error numero: ".$this->mysql->errno();
	    			$arrayError[2]="Error detalle: ".$this->mysql->error();
	    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
	        		return array('status'=>false,'detalle'=>$arrayError);
        		}
        		else{
        			// INSERTAR ANTICIPOS DE LA FC
        			if($valueInsertAnticipos<>''){
						$valueInsertAnticipos = str_replace("id_factura_compra_insert", $id_factura, $valueInsertAnticipos);
        				$sql="INSERT INTO anticipos
								(
									id_documento,
									tipo_documento,
									id_documento_anticipo,
									tipo_documento_anticipo,
									consecutivo_documento_anticipo,
									id_cuenta_anticipo,
									cuenta_colgaap,
									cuenta_niif,
									id_tercero,
									nit_tercero,
									tercero,
									valor,
									id_empresa
								) VALUES $valueInsertAnticipos ";
						$query=$this->mysql->query($sql);
						if (!$query) {
		        			$this->rollBack($id_factura,1);
		        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Anticipos Error)';
							$arrayError[1]="Error numero: ".$this->mysql->errno();
			    			$arrayError[2]="Error detalle: ".$this->mysql->error();
			    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
			        		return array('status'=>false,'detalle'=>$arrayError);
	        			}
        			}
        			$valueInsertRetenciones = substr($valueInsertRetenciones, 0, -1);
					$valueInsertRetenciones = str_replace("id_factura_compra_insert", $id_factura, $valueInsertRetenciones);
    				$sql="INSERT INTO compras_facturas_retenciones (id_factura_compra,id_retencion) VALUES $valueInsertRetenciones ";
    				$query=$this->mysql->query($sql,$this->mysql->link);
    				if (!$query && $valueInsertRetenciones<>"") {
	        			$this->rollBack($id_factura,1);
	        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Retenciones Error)';
						$arrayError[1]="Error numero: ".$this->mysql->errno();
		    			$arrayError[2]="Error detalle: ".$this->mysql->error();
		    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
		        		return array('status'=>false,'detalle'=>$arrayError);
	        		}
	        		else{

	        			$arrayAnticipo =$this->getAnticipos($id_factura);
	        			// $this->rollBack($id_factura,1);
		        		// return array('status'=>false,'detalle'=>$arrayAnticipo);

	        			$contabilizacionLocal = $this->setAsientos($id_factura,$numero_factura_completo,array('cuentaColgaap' => $data['cuenta_pago'], 'cuentaNiif' => $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'], 'estado' => $arrayCuentaPago[$data['cuenta_pago']]['estado']),$arrayAnticipo);
	        			if ($contabilizacionLocal['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionLocal['detalle']); }

	        			$contabilizacionNiif = $this->setAsientosNiif($id_factura,$numero_factura_completo,array('cuentaColgaap' => $data['cuenta_pago'], 'cuentaNiif' => $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'], 'estado' => $arrayCuentaPago[$data['cuenta_pago']]['estado']),$arrayAnticipo);
	        			if ($contabilizacionNiif['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionNiif['detalle']); }

	        			$updateInventario = $this->updateInventario($id_factura,"agregar");
	        			if ($updateInventario['status']==false) { return array('status'=>false,'detalle'=>$updateInventario['detalle']); }

	        			if ($this->setConsecutivo($this->arrayConsecutivo['consecutivo'])==false) { return array('status'=>false,'detalle'=>"Error al actualizar consecutivo del documento "); }
	        			

	        		}

					return array(
									'status'      => 200,
									'consecutivo' => $this->arrayConsecutivo['consecutivo'],
								);
        		}
        	}
        	else{
    			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Cabecera Error)';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

        		return array('status'=>false,'detalle'=>$arrayError);
        	}
		}

		/**
		 * @api {put} /facturas_compras/ Modificar factura
		 * @apiVersion 1.0.0
		 * @apiDescription Modificar factura en el sistema
		 * @apiName post_facturas
		 * @apiPermission Ventas
		 * @apiGroup Facturas_compras
		 *
		 * @apiParam {Int} consecutivo Numero consecutivo de la factura a modificar
		 * @apiParam {Date} fecha_documento Fecha de la factura formato (Y-M-D)
		 * @apiParam {Date} fecha_vencimiento Fecha de la factura formato (Y-M-D)
		 * @apiParam {String} documento_proveedor Numero del documento del cliente
		 * @apiParam {String} [prefijo_factura] Prefijo de la factura del proveedor
		 * @apiParam {Int} numero_factura Numero de la factura del proveedor
		 * @apiParam {Int} cuenta_pago Cuenta contable de pago de la factura
		 * @apiParam {Int} id_sucursal Id de la sucursal de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_bodega Id de la sucursal de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {String} [observacion] Observacion general de la factura
		 * @apiParam {Double} total_factura Valor completo de la factura
		 * @apiParam {Double} saldo_restante_factura Valor restante de la factura, corresponde al total o si se han hecho pagos al saldo restante
		 * @apiParam {Double} valor_abono Valor de abono o anticipo u otro concepto aplicado al total de la factura, es obligatorio si el saldo de la factura y el total de la factura son diferentes
		 * @apiParam {Object[]} [retenciones] Lista con las retenciones de la factura
		 * @apiParam {Int} retenciones.id Id de la retencion (Consultar en el panel de control del sistema)
		 * @apiParam {String} items.codigo Contiene el codigo del item a facturar
		 * @apiParam {Double} items.cantidad Contiene la cantidad item a facturar
		 * @apiParam {Double} items.precio Contiene el precio de venta del item a facturar
		 * @apiParam {Double} [items.observaciones] Contiene la observacion del item a facturar
		 * @apiParam {String="porcentaje","pesos"} [items.tipo_descuento] Contiene el tipo de descuento a aplicar al item puede ser porcentaje o pesos
		 * @apiParam {Double} [items.descuento] Contiene el valor del descuento a aplicar al item, y se aplica segun el tipo de descuento
		 *
		 * @apiSuccess {200} success  informacion registrada
		 * @apiSuccess {200} consecutivo  Consecutivo automatico asignado por el sistema
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
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[39]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para modificar facturas'); }
			// $data = json_decode( json_encode($data), true);

			$arrayError   = array();
			$arrayTercero = array();

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id_sucursal es obligatorio"; }
			if ($data['consecutivo']=='' || !isset($data['consecutivo'])){ $arrayError[] = "El campo consecutivo es obligatorio"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			$sql="SELECT
					id,
					estado
				FROM compras_facturas
				WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_sucursal=$data[id_sucursal]
					AND consecutivo='$data[consecutivo]' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			if (!$query) {
    			$arrayError[0]='Se produjo un error al verificar el documento en la base de datos';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
        		return array('status'=>false,'detalle'=>$arrayError);
    		}
			$id_factura        = $this->mysql->result($query,0,'id');
			$estado            = $this->mysql->result($query,0,'estado');
			$this->id_bodega   = $data['id_bodega'];
			$this->id_sucursal = $data['id_sucursal'];

			if ($id_factura=='' || $id_factura==0) { $arrayError[] = "La factura no existe en el sistema"; }
			if ($estado==2) { $arrayError[] = "La factura se encuentra bloqueada"; }
			if ($estado==3) { $arrayError[] = "La factura se encuentra anulada"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }			

			$updateRetenciones = $this->updateRetenciones($id_factura);
			if ($updateRetenciones['status']==false) { return array('status'=>false,'detalle'=>$updateRetenciones['detalle']); }
			$updateItemsFactura = $this->updateItemsFactura($id_factura);
			if ($updateItemsFactura['status']==false) { return array('status'=>false,'detalle'=>$updateItemsFactura['detalle']); }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if ($data['numero_factura']=='' || !isset($data['numero_factura'])){ $arrayError[] = "El campo numero factura es obligatorio"; }
			$numero_factura_completo = (!empty($data['prefijo_factura']) && $data['prefijo_factura']<>' ')? "$data[prefijo_factura] $numero_factura" : $numero_factura ;

			if ($data['fecha_documento']=='' || !isset($data['fecha_documento']) ){ $arrayError[] = "El campo fecha_documento es obligatorio"; }
			if ($data['fecha_vencimiento']=='' || !isset($data['fecha_vencimiento']) ){ $arrayError[] = "El campo fecha_vencimiento es obligatorio"; }
			if ($data['documento_proveedor']=='' || !isset($data['documento_proveedor'])){ $arrayError[] = "El campo documento proveedor es obligatorio"; }
			$this->id_proveedor = $this->getProveedor($data['documento_proveedor']);
			if ($this->id_proveedor==false) {  $arrayError[] = "El proveedor no existe en el sistema"; }

			if ($data['cuenta_pago']=='' || !isset($data['cuenta_pago'])){ $arrayError[] = "El campo cuenta pago es obligatorio"; }
			$arrayCuentaPago = $this->getCuentaPago($data['cuenta_pago']);
			if (!array_key_exists("$data[cuenta_pago]",$arrayCuentaPago)) {
				$arrayError[] = "La cuenta de pago no existe en el sistema";
			}

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id sucursal es obligatorio"; }
			if ($data['id_bodega']=='' || !isset($data['id_bodega'])){ $arrayError[] = "El campo id bodega es obligatorio"; }
			$arrayUbicaciones = $this->getSucursales();
			if (!array_key_exists("$data[id_sucursal]",$arrayUbicaciones['sucursales'])) { $arrayError[] = "La sucursal no existe"; }
			if (!array_key_exists("$data[id_bodega]",$arrayUbicaciones['bodegas'])) { $arrayError[] = "La bodega no existe"; }
			$this->id_sucursal = $data['id_sucursal'];
			$this->id_bodega   = $data['id_bodega'];
			if (!empty($data['retenciones'])){
				$arrayRetenciones=$this->getRetenciones();
				foreach ($data['retenciones'] as $key => $arrayResult) {
					if (gettype($arrayResult)=='object') {
						$arrayResult=get_object_vars($arrayResult);
					}
					if (!array_key_exists("$arrayResult[id]",$arrayRetenciones)) { $arrayError[] = "la retencion con id  $arrayResult[id] no existe en el sistema o no esta creada"; }
					$valueInsertRetenciones .= "($id_factura,$arrayResult[id]),";
				}
			}
			if (!empty($data['anticipos'])){
				$arrayAnticipos = $this->setAnticipos($data['anticipos']);
				if ($arrayAnticipos['status']==false) { $arrayError[] = $arrayAnticipos['detalle']; }
				else{$valueInsertAnticipos = $arrayAnticipos['sql'];}
			}


			$arrayItemsBodega = $this->getItems($data['id_sucursal'],$data['id_bodega']);
			if (empty($data['items'])){ $arrayError[] = "El array con items es obligatorio"; }
			foreach ($data['items'] as $key => $arrayItems) {
				if (gettype($arrayItems)=='object') {
					$arrayItems=get_object_vars($arrayItems);
				}
				if ($arrayItems['codigo']=='') { $arrayError[] = "El codigo del item es obligatorio, Item $arrayItems[codigo]"; }
				else if (!array_key_exists("$arrayItems[codigo]",$arrayItemsBodega)) { $arrayError[] = "El item con codigo $arrayItems[codigo] no existe en el sistema"; }
				if ($arrayItems['cantidad']=='') { $arrayError[] = "La cantidad del item es obligatorio,  Item $arrayItems[codigo]"; }
				
				if ($arrayItems['precio']=='') { $arrayError[] = "El precio del item es obligatorio, Item $arrayItems[codigo]"; }
				// else if (!is_numeric($arrayItems['precio'])) { $arrayError[] = "El precio del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }

				if ($arrayItems['tipo_descuento']<>'' && ($arrayItems['tipo_descuento']<>'porcentaje' && $arrayItems['tipo_descuento']<>'pesos')) {  $arrayError[] = "El campo tipo_descuento no es valido solo puede ser (porcentaje, pesos),  Item $arrayItems[codigo]";  }
				if (($arrayItems['descuento']<>'' && $arrayItems['descuento']>0) && $arrayItems['tipo_descuento']=='' ){  $arrayError[] = "Si aplica descuento debe enviar el tipo descuento,  Item $arrayItems[codigo]"; }
				if ($arrayItems['descuento']<>'' && !is_numeric($arrayItems['descuento'])) { $arrayError[] = "El descuento del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				$arrayItems['tipo_descuento'] = ($arrayItems['tipo_descuento']=='')? "porcentaje" : $arrayItems['tipo_descuento'] ;

				// tipo_descuento Puede ser porcentaje,pesos
				// descuento

				$valueInsertItems .= "(
									'$id_factura',
									'".$arrayItemsBodega[$arrayItems['codigo']]['id_item']."',
									'$arrayItems[cantidad]',
									'$arrayItems[precio]',
									'$arrayItems[observaciones]',
									'$arrayItems[tipo_descuento]',
									'$arrayItems[descuento]'
									),";

			}


			if ($data['total_factura']==='' /*|| !is_numeric($data['total_factura'])*/ ) { $arrayError[] = "El campo total factura debe tener un valor y debe ser numerico"; }
			if ($data['saldo_restante_factura']==='' /*|| !is_numeric($data['saldo_restante_factura'])*/ ) { $arrayError[] = "El campo saldo restante de la factura debe tener un valor y debe ser numerico"; }
			if ($data['total_factura']<>$data['saldo_restante_factura'] && ($data['valor_abono']==='' /*|| $data['valor_abono']*/ ) ) { $arrayError[] = "El campo valor abono debe tener un valor y debe ser numerico pues el saldo de la factura es diferente al total"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			$json                = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']);
			
			if ($estado==1){
				$this->rollBack($id_factura,2 );
			}

			$sql="UPDATE compras_facturas
						SET
							fecha_inicio                 = '$data[fecha_documento]',
							fecha_final                  = '$data[fecha_vencimiento]',
							id_configuracion_cuenta_pago = '".$arrayCuentaPago[$data["cuenta_pago"]]["id"]."',
							configuracion_cuenta_pago    = '".$arrayCuentaPago[$data["cuenta_pago"]]["nombre"]."',
							id_cuenta_pago               = '".$arrayCuentaPago[$data["cuenta_pago"]]["id_cuenta"]."',
							cuenta_pago                  = '".$arrayCuentaPago[$data["cuenta_pago"]]["cuenta"]."',
							cuenta_pago_niif             = '".$arrayCuentaPago[$data["cuenta_pago"]]["cuenta_niif"]."',
							prefijo_factura              = '$data[prefijo_factura]',
							numero_factura               = '$data[numero_factura]',
							id_proveedor                 = $this->id_proveedor,
							id_usuario                   = $this->id_usuario,
							estado                       = 1,
							id_sucursal                  = $this->id_sucursal,
							id_bodega                    = $this->id_bodega,
							observacion                  = '$data[observacion]',
							total_factura                = $data[total_factura],
							total_factura_sin_abono      = $data[saldo_restante_factura],
							json_api                     = '$json'
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_factura";
        	$query = $this->mysql->query($sql,$this->mysql->link);
        	if ($query){
    			$valueInsertItems = substr($valueInsertItems, 0, -1);
        		$sql="INSERT INTO compras_facturas_inventario
        				(
			        		id_factura_compra,
							id_inventario,
							cantidad,
							costo_unitario,
							observaciones,
							tipo_descuento,
							descuento
        				)
        				VALUES $valueInsertItems";
        		$query=$this->mysql->query($sql,$this->mysql->link);
        		if (!$query) {
        			$this->rollBack($id_factura,1);

        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Items Error)';
					$arrayError[1]="Error numero: ".$this->mysql->errno();
	    			$arrayError[2]="Error detalle: ".$this->mysql->error();
	    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
	        		return array('status'=>false,'detalle'=>$arrayError);
        		}
        		else{
        			// INSERTAR ANTICIPOS DE LA FC
        			if($valueInsertAnticipos<>''){
						$valueInsertAnticipos = str_replace("id_factura_compra_insert", $id_factura, $valueInsertAnticipos);
        				$sql="INSERT INTO anticipos
								(
									id_documento,
									tipo_documento,
									id_documento_anticipo,
									tipo_documento_anticipo,
									consecutivo_documento_anticipo,
									id_cuenta_anticipo,
									cuenta_colgaap,
									cuenta_niif,
									id_tercero,
									nit_tercero,
									tercero,
									valor,
									id_empresa
								) VALUES $valueInsertAnticipos ";
						$query=$this->mysql->query($sql);
						if (!$query) {
		        			$this->rollBack($id_factura,1);
		        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Anticipos Error)';
							$arrayError[1]="Error numero: ".$this->mysql->errno();
			    			$arrayError[2]="Error detalle: ".$this->mysql->error();
			    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
			        		return array('status'=>false,'detalle'=>$arrayError);
	        			}
        			}
        			$valueInsertRetenciones = substr($valueInsertRetenciones, 0, -1);
    				$sql="INSERT INTO compras_facturas_retenciones (id_factura_compra,id_retencion) VALUES $valueInsertRetenciones ";
    				$query=$this->mysql->query($sql,$this->mysql->link);
    				if (!$query && $valueInsertRetenciones<>"") {
	        			$this->rollBack($id_factura,1);
	        			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Retenciones Error)';
						$arrayError[1]="Error numero: ".$this->mysql->errno();
		    			$arrayError[2]="Error detalle: ".$this->mysql->error();
		    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
		        		return array('status'=>false,'detalle'=>$arrayError);
	        		}
	        		else{
	        			$arrayAnticipo =$this->getAnticipos($id_factura);

	        			$contabilizacionLocal = $this->setAsientos($id_factura,$data['consecutivo'],array('cuentaColgaap' => $data['cuenta_pago'], 'cuentaNiif' => $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'], 'estado' => $arrayCuentaPago[$data['cuenta_pago']]['estado']),$arrayAnticipo);
	        			if ($contabilizacionLocal['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionLocal['detalle']); }

	        			$contabilizacionNiif = $this->setAsientosNiif($id_factura,$data['consecutivo'],array('cuentaColgaap' => $data['cuenta_pago'], 'cuentaNiif' => $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'], 'estado' => $arrayCuentaPago[$data['cuenta_pago']]['estado']),$arrayAnticipo);
	        			if ($contabilizacionNiif['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionNiif['detalle']); }

	        			// $updateInventario = $this->updateInventario($id_factura, "agregar");
	        			// if ($updateInventario['status']==false) { return array('status'=>false,'detalle'=>$updateInventario['detalle']); }

	        			$updateInventario = $this->updateInventario($id_factura,"agregar");
	        			if ($updateInventario['status']==false) { return array('status'=>false,'detalle'=>$updateInventario['detalle']); }

	        		}
					return array('status'=>200);
        		}
        	}
        	else{
    			$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Cabecera Error )';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
        		return array('status'=>false, 'detalle'=>$arrayError);
        	}
		}

		/**
		 * @api {delete} /facturas_compras/ Anular factura
		 * @apiVersion 1.0.0
		 * @apiDescription Anular factura en el sistema.
		 * @apiName delete_factura
		 * @apiPermission Compras
		 * @apiGroup Facturas_compras
		 *
		 * @apiParam {String} id_sucursal Id de la sucursal del documento (Consultar en el panel de control del sistema)
		 * @apiParam {String} id_bodega Id de la bodega del documento (Consultar en el panel de control del sistema)
		 * @apiParam {String} consecutivo Consecutivo de la factura Ejemplo: "1010"
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
			if ($this->usuarioPermisos[40]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para anular facturas'); }
			$data = json_decode( json_encode($data), true);
			// id_sucursal *
			// id_bodega *
			// consecutivo *
			// documento_cliente *

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id_sucursal es obligatorio"; }
			if ($data['id_bodega']=='' || !isset($data['id_bodega'])){ $arrayError[] = "El campo id_bodega es obligatorio"; }
			if ($data['consecutivo']=='' || !isset($data['consecutivo'])){ $arrayError[] = "El campo consecutivo es obligatorio"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			$sql="SELECT
					id,
					estado
				FROM compras_facturas
				WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_sucursal=$data[id_sucursal]
					AND id_bodega=$data[id_bodega]
					AND consecutivo='$data[consecutivo]' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			if (!$query) {
    			$arrayError[0]='Se produjo un error al eliminar el documento en la base de datos';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
        		return array('status'=>false,'detalle'=>$arrayError);
    		}
			$id_factura        = $this->mysql->result($query,0,'id');
			$estado            = $this->mysql->result($query,0,'estado');
			$this->id_bodega   = $data['id_bodega'];
			$this->id_sucursal = $data['id_sucursal'];

			if ($id_factura=='' || $id_factura==0) { $arrayError[] = "La factura no existe en el sistema"; }
			if ($estado==3) { $arrayError[] = "La factura ya esta anulada"; }
			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if ($estado==1){
				$this->rollBack($id_factura,2, " estado=3 " );
			}
			else if ($estado==0) {
				$this->rollBack($id_factura,1, " estado=3 " );
			}

			return array('status'=>200);
		}

		/**
		 * getConsecutivo Consultar el consecutivo que se le asignara al documento
		 */
		public function getConsecutivo(){
			$sql = "SELECT id,consecutivo FROM configuracion_consecutivos_documentos WHERE activo=1 AND modulo='compra' AND documento='factura' AND id_sucursal=$this->id_sucursal";
			$query = $this->mysql->query($sql);
			$arrayReturn  = array( 
								'id'          => $this->mysql->result($query,0,'id'), 
								'consecutivo' => $this->mysql->result($query,0,'consecutivo'), 
							);
			return (!empty($arrayReturn))? $arrayReturn : false ;
		}

		/**
		 * setConsecutivo Actualizar el consecutivo asignado
		 */
		public function setConsecutivo($consecutivo){
			$sql = "UPDATE configuracion_consecutivos_documentos SET consecutivo=$consecutivo+1 WHERE  activo=1 AND modulo='compra' AND documento='factura' AND id_sucursal=$this->id_sucursal ";
			$query = $this->mysql->query($sql);
			return (!$query)? false : true ;
		}

		/**
		 * getTerceros Consultar el cliente del sistema
		 * @param  String $documento Documento del cliente a consultar
		 * @return int Id del cliente a consultar
		 */
		public function getProveedor($documento){
			$sql="SELECT id,numero_identificacion,nombre FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa AND numero_identificacion='$documento'";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$id_temp = $this->mysql->result($query,0,'id');
			return ($id_temp>1)? $id_temp : false;
		}


		/**
		 * getCuentaPago Consultar la cuenta de pago de la factura
		 * @param  string $cuenta_pago cuenta de pago de la factura
		 * @return array              Array con las cuentas de pago
		 */
		public function getCuentaPago($cuenta_pago){
			$sql="SELECT id,nombre,id_cuenta,cuenta,cuenta_niif,estado FROM configuracion_cuentas_pago WHERE activo=1 AND tipo='Compra' AND id_empresa=$this->id_empresa AND cuenta='$cuenta_pago' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayTemp[$row['cuenta']] = array(
												'id'          => $row['id'],
												'nombre'      => $row['nombre'],
												'id_cuenta'   => $row['id_cuenta'],
												'cuenta'      => $row['cuenta'],
												'cuenta_niif' => $row['cuenta_niif'],
												'estado'      => $row['estado'],
												);
			}

			return $arrayTemp;
		}


		/**
		 * setAnticipos validar y guardar los acticipos, abonos, depositos a aplicar a la factura
		 * @param Array $params Parametros necesarios para el proceso : id_sucursal, consecutivo, cuenta, valor
		 */
		public function setAnticipos($params){
			foreach ($params as $key => $result){
				if (gettype($result)=='object') {
					$result=get_object_vars($result);
				}
				$arrayDocs[$result['consecutivo']][$result['cuenta']]['valor'] += $result['valor'];
				$arrayDocs[$result['consecutivo']][$result['cuenta']]['status'] = false; // PARA VALIDAR SI EL ABONO EXISTE EN BD
				$whereConsecutivo .= ($whereConsecutivo=="")? " R.consecutivo=$result[consecutivo] " : " OR R.consecutivo=$result[consecutivo] " ;
			}

			$sql = "SELECT
						R.id,
						R.consecutivo,
						RC.id AS id_row_cuenta,
						RC.cuenta,
						RC.cuenta_niif,
						RC.debito,
						RC.credito,
						RC.saldo_pendiente,
						R.id_tercero,
						R.nit_tercero,
						R.tercero,
						RC.id_tercero AS id_tercero_cuenta,
						RC.nit_tercero AS nit_tercero_cuenta,
						RC.tercero AS tercero_cuenta
					FROM
						recibo_caja AS R
					INNER JOIN recibo_caja_cuentas AS RC ON RC.id_recibo_caja = R.id
					WHERE
						R.id_sucursal = $this->id_sucursal
					AND ($whereConsecutivo)
					AND R.activo = 1
					AND R.estado = 1
					AND R.tipo <> 'Ws'";
			$query = $this->mysql->query($sql);
			while ($row = $this->mysql->fetch_array($query)) {
				if (is_array($arrayDocs[$row['consecutivo']][$row['cuenta']])) {

					$id_tercero  = ($row['id_tercero_cuenta']>0)?   $row['id_tercero_cuenta'] : $row['id_tercero'] ;
					$nit_tercero = ($row['nit_tercero_cuenta']<>'')? $row['nit_tercero_cuenta'] : $row['nit_tercero'] ;
					$tercero     = ($row['tercero_cuenta']<>'')? $row['tercero_cuenta'] : $row['tercero'] ;

					$arrayDocs[$row['consecutivo']][$row['cuenta']]['status']        = true;
					$arrayDocs[$row['consecutivo']][$row['cuenta']]['id']            = $row['id'];
					$arrayDocs[$row['consecutivo']][$row['cuenta']]['id_row_cuenta'] = $row['id_row_cuenta'];
					$arrayDocs[$row['consecutivo']][$row['cuenta']]['cuenta_niif']   = $row['cuenta_niif'];
					$arrayDocs[$row['consecutivo']][$row['cuenta']]['id_tercero']    = $id_tercero;
					$arrayDocs[$row['consecutivo']][$row['cuenta']]['nit_tercero']   = $nit_tercero;
					$arrayDocs[$row['consecutivo']][$row['cuenta']]['tercero']       = $tercero;

					$arrayDocs[$row['consecutivo']][$row['cuenta']]['saldo']         += $row['saldo_pendiente'];
				}
			}

			foreach ($arrayDocs as $consecutivo => $arrayDocsR) {
				foreach ($arrayDocsR as $cuenta => $result) {
					if ($result['status']==false) { $arrayError[] = "El anticipo en el documento $consecutivo con cuenta $cuenta no existe "; continue;}
					// if ($result['valor']>$result['saldo']) { $arrayError[] = "El anticipo en el documento $consecutivo con cuenta $cuenta excede el saldo del anticipo por ".($result['valor']-$result['saldo']); continue;}

					$valueInsert .= "(
									'id_factura_compra_insert',
									'FC',
									'$result[id]',
									'RC',
									'$consecutivo',
									'$result[id_row_cuenta]',
									'$cuenta',
									'$result[cuenta_niif]',
									'$result[id_tercero]',
									'$result[nit_tercero]',
									'$result[tercero]',
									'$result[valor]',
									'$this->id_empresa'
								),";
				}


			}
			$valueInsert = substr($valueInsert, 0, -1);
			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }
			else{ return array('status'=>true,'sql'=>$valueInsert); }
		}

		public function getAnticipos($id_factura){
			$arrayAnticipo = array('total'=>0);
			$sql   = "SELECT
									id,
									id_cuenta_anticipo,
									id_documento_anticipo,
									tipo_documento_anticipo,
									consecutivo_documento_anticipo,
									cuenta_colgaap,
									cuenta_niif,
									id_tercero,
									nit_tercero,
									tercero,
									valor
								FROM anticipos
								WHERE id_documento='$id_factura'
									AND tipo_documento='FC'
									AND id_empresa='$this->id_empresa'
									AND activo=1
									AND valor>0";
			$query=$this->mysql->query($sql);
			$arrayAnticipo['sql']=$sql;
			while ($rowAnticipo = $this->mysql->fetch_assoc($query)) {

				$idAnticipo  = $rowAnticipo['id'];

				$arrayAnticipo['total'] += $rowAnticipo['valor']*1;

				$arrayAnticipo['anticipos'][$idAnticipo]['valor']          = $rowAnticipo['valor'];
				$arrayAnticipo['anticipos'][$idAnticipo]['id_tercero']     = $rowAnticipo['id_tercero'];
				$arrayAnticipo['anticipos'][$idAnticipo]['cuenta_niif']    = $rowAnticipo['cuenta_niif'];
				$arrayAnticipo['anticipos'][$idAnticipo]['cuenta_colgaap'] = $rowAnticipo['cuenta_colgaap'];
				$arrayAnticipo['anticipos'][$idAnticipo]['consecutivo']    = $rowAnticipo['consecutivo_documento_anticipo'];
				$arrayAnticipo['anticipos'][$idAnticipo]['id_anticipo']    = $rowAnticipo['id_documento_anticipo'];
				$arrayAnticipo['anticipos'][$idAnticipo]['tipo_documento'] = $rowAnticipo['tipo_documento_anticipo'];
			}

			return $arrayAnticipo;
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
		 * getRetenciones Consultar las retenciones del sistema
		 * @return Array Array con las retenciones de venta creadas en el sistema
		 */
		public function getRetenciones(){
			$sql="SELECT
					id,
					retencion,
					tipo_retencion,
					valor,
					base,
					cuenta,
					cuenta_niif,
					cuenta_autoretencion,
					cuenta_autoretencion_niif,
					modulo
 				FROM retenciones WHERE activo=1 AND id_empresa=$this->id_empresa AND modulo='Compra' ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($row=$this->mysql->fetch_assoc($query)){$data[$row['id']]=$row;}
			return $data;
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
			while($row=$this->mysql->fetch_assoc($query)){$data[$row['codigo']]=$row;}
			return $data;
		}

		/**
		 * getCcos Consultar los centros de costos
		 * @return Array Array con los centros de costos
		 */
		public function getCcos(){
			$sql="SELECT
					id,
					codigo,
					nombre
				FROM centro_costos
				WHERE activo=1 AND id_empresa=$this->id_empresa ";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($row=$this->mysql->fetch_assoc($query)){$data[$row['codigo']]= $row;}
			return $data;
		}


		/**
		 * setAsientos Contabilizar la factura en norma local
		 * @param Int $id_factura        Id de la factura a causar
		 * @param Array $arrayCuentaPago Array con la informacion de la cuenta de pago
		 * @return Array Resultado del proceso {status: true o false},{detalle: en caso de ser error se detallara en este campo}
		 */
		public function setAsientos($id_factura,$consecutivo,$arrayCuentaPago,$arrayAnticipo){
			global $saldoGlobalFactura, $saldoGlobalFacturaSinAbono;
			$decimalesMoneda  = ($this->decimales_moneda >= 0)? $this->decimales_moneda : 0;
			$cuentaPago       = $arrayCuentaPago['cuentaColgaap'];
			$estadoCuentaPago = $arrayCuentaPago['estado'];

			//============================= QUERY CUENTAS ============================//
			$ivaAcumulado      = 0;
			$precioAcumulado   = 0;

			$whereIdItemsCuentas = '';

			$sql   = "SELECT fecha_inicio,prefijo_factura,numero_factura FROM compras_facturas WHERE activo=1 AND id=$id_factura";
			$query = $this->mysql->query($sql,$this->mysql->link);
			$fechaFactura = $this->mysql->result($query,0,'fecha_inicio');
			$numeroFactura = ($this->mysql->result($query,0,'prefijo_factura')<>'')? $this->mysql->result($query,0,'prefijo_factura')." ".$this->mysql->result($query,0,'numero_factura') : $this->mysql->result($query,0,'numero_factura');


			$sql  = "SELECT
						CFI.id,
						CFI.id_inventario AS id_item,
						CFI.codigo,
						CFI.nombre,
						CFI.cantidad,
						CFI.costo_unitario AS precio,
						CFI.descuento,
						CFI.tipo_descuento,
						CFI.id_impuesto,
						CFI.valor_impuesto,
						CFI.cuenta_impuesto,
						I.cruzar_costo_activo_fijo,
						I.cuenta_compra,
						CFI.inventariable,
						CFI.check_opcion_contable,
						CFI.opcion_gasto,
						CFI.opcion_costo,
						CFI.opcion_activo_fijo,
						CFI.id_centro_costos,
						CFI.id_consecutivo_referencia AS id_referencia,
						CFI.nombre_consecutivo_referencia AS nombre_referencia
					FROM
						compras_facturas_inventario AS CFI
					LEFT JOIN
						impuestos AS I
					ON
						CFI.id_impuesto = I.id
					WHERE
						CFI.id_factura_compra = '$id_factura'
					AND
						CFI.activo = 1";
			$queryDoc = $this->mysql->query($sql);
			while($rowDoc = $this->mysql->fetch_array($queryDoc)){

				//CALCULO DEL PRECIO
				$impuesto = 0;
				$precio   = $rowDoc['precio'] * $rowDoc['cantidad'];
				$costo    = $rowDoc['costo'] * $rowDoc['cantidad'];

				if($rowDoc['descuento'] > 0){ $precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio - ROUND(($rowDoc['descuento'] * $precio) / 100, $decimalesMoneda) : $precio - $rowDoc['descuento']; }
				if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0){ $impuesto = $precio * $rowDoc['valor_impuesto'] / 100; }

				$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR id_items = '.$rowDoc['id_item']: 'id_items = '.$rowDoc['id_item'];

				$arrayInfoItems[$rowDoc['id_item']] = array(
															'codigo'                => $rowDoc['codigo'],
															'nombre_item'           => $rowDoc['nombre'],
															'check_opcion_contable' => $rowDoc['check_opcion_contable'],
															'opcion_gasto'          => $rowDoc['opcion_gasto'],
															'opcion_costo'          => $rowDoc['opcion_costo'],
															'opcion_activo_fijo'    => $rowDoc['opcion_activo_fijo'],
														);

				$arrayInventarioFactura[$rowDoc['id']] = array(
																'id_factura_inventario'     => $rowDoc['id'],
																'codigo'                		=> $rowDoc['codigo'],
																'nombre_item'           		=> $rowDoc['nombre'],
																'impuesto'              		=> $impuesto,
																'cuenta_impuesto'       		=> $rowDoc['cuenta_impuesto'],
																'cruzar_costo_activo_fijo'	=> $rowDoc['cruzar_costo_activo_fijo'],
																'cuenta_compra'							=> $rowDoc['cuenta_compra'],
																'precio'                		=> $precio,
																'id_items'              		=> $rowDoc['id_item'],
																'inventariable'         		=> $rowDoc['inventariable'],
																'cantidad'              		=> $rowDoc['cantidad'],
																'id_centro_costos'      		=> $rowDoc['id_centro_costos'],
																'check_opcion_contable' 		=> $rowDoc['check_opcion_contable'],
																'id_referencia'         		=> $rowDoc['id_referencia'],
																'nombre_referencia'     		=> $rowDoc['nombre_referencia'],
															);
			}

			$sqlItemsCuentas = "SELECT id,id_items,descripcion,id_puc,puc,tipo,estado
								FROM items_cuentas
								WHERE activo = 1
								AND id_empresa = '$this->id_empresa'
								AND estado = 'compra'
								AND ($whereIdItemsCuentas)
								GROUP BY id_items,descripcion
								ORDER BY id_items ASC";
			$queryItemsCuentas = $this->mysql->query($sqlItemsCuentas);

			$whereCuentaCcos = "";
			while($rowCuentasItems = $this->mysql->fetch_array($queryItemsCuentas)){
				if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; $rowCuentasItems['tipo'] = 'credito'; }
				if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['tipo'] = 'debito'; }

				// VALIDAR QUE EL ITEM TENGA LAS CUENTAS CONFIGURADAS
				switch ($rowCuentasItems['descripcion']){
					case 'costo':
							if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_costo']=='true'){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo ".$arrayInfoItems[$rowCuentasItems['id_items']]['codigo']." No se ha configurado en la cuenta de $rowCuentasItems[descripcion] en $rowCuentasItems[estado]");
							}
						break;
					case 'gasto':
							if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_gasto']=='true'){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo ".$arrayInfoItems[$rowCuentasItems['id_items']]['codigo']." No se ha configurado en la cuenta de $rowCuentasItems[descripcion] en $rowCuentasItems[estado]");
							}
						break;
					case 'precio':
							if ($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo ".$arrayInfoItems[$rowCuentasItems['id_items']]['codigo']." No se ha configurado en la cuenta de $rowCuentasItems[descripcion] en $rowCuentasItems[estado]");
							}
						break;
					case 'activo_fijo':
						if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_activo_fijo']=='true'){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "Aviso.\nEl item Codigo ".$arrayInfoItems[$rowCuentasItems['id_items']]['codigo']." No se ha configurado en la cuenta de $rowCuentasItems[descripcion] en $rowCuentasItems[estado]");
							}
						break;
				}

				$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']] = array('estado' => $rowCuentasItems['tipo'], 'cuenta' => $rowCuentasItems['puc']);

				$valueInsertContabilizacion .=  "('".$rowCuentasItems['id_items']."',
													'".$rowCuentasItems['puc']."',
													'".$rowCuentasItems['tipo']."',
													'".$rowCuentasItems['descripcion']."',
													'$id_factura',
													'FC',
													'$this->id_empresa',
													'$this->id_sucursal',
													'$this->id_bodega'),";

				$cuenta      = $rowCuentasItems['puc'];
				$descripcion = $rowCuentasItems['descripcion'];

				if($descripcion == 'precio' || $descripcion == 'gasto' || $descripcion == 'costo' || $descripcion == 'activo_fijo'){ $whereCuentaCcos .= "OR cuenta='$cuenta' "; }
			}

			// VALIDAR LA CUENTA DE PAGO
			foreach($arrayCuentasItems as $id_item => $arrayResult){
				if(!array_key_exists('contraPartida_precio', $arrayResult)){
					$arrayCuentasItems[$id_item]['contraPartida_precio'] = array(
																					'estado' => 'credito',
																					'cuenta' => $cuentaPago
																				);
					$valueInsertContabilizacion .= "('".$arrayResult['id_items']."',
														 '".$cuentaPago."',
														 'credito',
														 'contraPartida_precio',
														 '$id_factura',
														 'FC',
														 '$this->id_empresa',
														 '$this->id_sucursal',
														 '$this->id_bodega'),";
				}
			}

			$whereCuentaCcos = substr($whereCuentaCcos, 3, -1);
			$sqlCcos   = "SELECT cuenta,centro_costo FROM puc WHERE id_empresa = '$this->id_empresa' AND activo = 1 AND ($whereCuentaCcos)";
			$queryCcos = $this->mysql->query($sqlCcos);

			while($row = $this->mysql->fetch_assoc($queryCcos)){
				$cuenta = $row['cuenta'];
				$cCos   = $row['centro_costo'];

				$arrayCuentaCcos[$cuenta] = $cCos;
			}

			// CONSULTAR LAS CUENTAS DE TRANSITO DE LAS ENTRADAS DE ALMACEN
			$sql = "SELECT
						id_cuenta_colgaap_debito,
						cuenta_colgaap_debito,
						id_cuenta_colgaap_credito,
						cuenta_colgaap_credito
					FROM
						costo_cuentas_transito
					WHERE
						activo = 1
					AND
						id_empresa = $this->id_empresa";
			$query = $this->mysql->query($sql);

			$arrayCuentasTransito['id_cuenta_colgaap_debito']  = $this->mysql->result($query,0,'id_cuenta_colgaap_debito');
			$arrayCuentasTransito['cuenta_colgaap_debito']     = $this->mysql->result($query,0,'cuenta_colgaap_debito');
			$arrayCuentasTransito['id_cuenta_colgaap_credito'] = $this->mysql->result($query,0,'id_cuenta_colgaap_credito');
			$arrayCuentasTransito['cuenta_colgaap_credito']    = $this->mysql->result($query,0,'cuenta_colgaap_credito');

			$arrayGlobalEstado['debito']  = 0;
			$arrayGlobalEstado['credito'] = 0;

			$arrayItemEstado['debito']  = 0;
			$arrayItemEstado['credito'] = 0;

			$acumSubtotal = 0;
			$acumImpuesto = 0;

			$msjErrorCcosto = '';

			// print_r($arrayInventarioFactura);
			foreach($arrayInventarioFactura AS $valArrayInventario){
				// print_r($valArrayInventario);

				$totalContabilizacionItem   = 0;
				$arrayItemEstado['debito']  = 0;
				$arrayItemEstado['credito'] = 0;

				$idItemArray       = $valArrayInventario['id_items'];										//ID ITEM
				$descripcionCuenta = $valArrayInventario['check_opcion_contable'];							//GASTO, COSTO, ACTIVO FIJO
				$descripcionCuenta = (strlen($descripcionCuenta) > 4)? $descripcionCuenta : 'precio';

				$cuentaOpcional    = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['cuenta'];		//CUENTA OPCION CONTABILIZACION

				$cuentaPrecio   = ($descripcionCuenta != 'precio')? $cuentaOpcional: $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
				$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
				$cuentaImpuesto = ($valArrayInventario['cuenta_impuesto'] > 0)? $valArrayInventario['cuenta_impuesto']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

				//========================= CONTABILIZACION MERCANCIA PARA LA VENTA, COSTO, GASTO =========================//
				//*********************************************************************************************************//
				//CONDICIONAL SI TIENE CCOS
				$cCosPrecio = 0;
				if($arrayCuentaCcos[$cuentaPrecio] == 'Si'){
					if($valArrayInventario['id_centro_costos'] > 0){ $cCosPrecio = $valArrayInventario['id_centro_costos']; }
					else{ $msjErrorCcosto = '\n'.$valArrayInventario['codigo'].' '.$valArrayInventario['nombre_item']; }
				}

				if($descripcionCuenta != 'activo_fijo'){

					//======================================= CALC PRECIO =====================================//
					if($cuentaPrecio > 0){
						$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] += $valArrayInventario['precio']; }
						else{ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] = $valArrayInventario['precio']; }

						$arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
						$arrayItemEstado[$estado]   += $valArrayInventario['precio'];
						$acumSubtotal               += $valArrayInventario['precio'];

						//===================================== CALC IMPUESTO ========================================//
						if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){

							$estado = 'debito';

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[0][$cuentaImpuesto][$estado] > 0){ $arrayAsiento[0][$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
							else{ $arrayAsiento[0][$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }

							//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
							if($valArrayInventario === end($arrayInventarioFactura)) {
				        $arrayAsiento[0][$cuentaImpuesto][$estado] = $arrayAsiento[0][$cuentaImpuesto][$estado];
				    	}

							$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
							$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
							$acumImpuesto               += $valArrayInventario['impuesto'];
						}

						//============================== CALC CONTRA PARTIDA PRECIO =================================//
						if($contraPrecio > 0){
							$arrayAsiento[0][$contraPrecio]['type'] = 'cuentaPago';
							$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

							$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
											: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[0][$contraPrecio][$estado] > 0){ $arrayAsiento[0][$contraPrecio][$estado] += $contraSaldo; }
							else{ $arrayAsiento[0][$contraPrecio][$estado] = $contraSaldo; }

							$arrayGlobalEstado[$estado] += $contraSaldo;
							$arrayItemEstado[$estado]   += $contraSaldo;

							$acumCuentaClientes   = $contraPrecio;
							$estadoCuentaClientes = $estado;
						}

						//============================== SI PERTENECE A UNA ENTRADA DE ALMACEN CERRAR CUENTAS TRANSITO =================================//
						if ($valArrayInventario['nombre_referencia']=='Entrada de Almacen') {
							if($arrayCuentasTransito['id_cuenta_colgaap_debito'] =='' || $arrayCuentasTransito['id_cuenta_colgaap_credito'] ==''){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] pertenece a una entrada de Almacen pero no hay cuentas de transito configiradas en el panel de control" );
							}

							$arrayAsiento[0][$arrayCuentasTransito['cuenta_colgaap_debito']]['credito'] += $valArrayInventario['precio'];
							$arrayAsiento[0][$arrayCuentasTransito['cuenta_colgaap_credito']]['debito'] += $valArrayInventario['precio'];
						}

					}
					else if($valArrayInventario['inventariable'] == 'false'){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "Aviso.\nEl item Codigo $valArrayInventario[codigo] No se ha configurado en la contabilizacion");
					}

					if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No establece doble partida por favor revise la configuracion de contabilizacion" );
					}
					else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
						// print_r($arrayItemEstado);
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion");
					}
				}
				//======================================= CONTABILIZACION ACTIVO FIJO ======================================//
				//**********************************************************************************************************//
				else{
					if($cuentaPrecio == ''){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] no tiene asignado la cuenta de Activo Fijo en configuracion items");
					}

					//======================================= CALC PRECIO =====================================//
					if($cuentaPrecio > 0){
						$totalContabilizacionItem += $valArrayInventario['precio'];

						//======================================= CALC IMPUESTO ========================================//
						if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
							$estado                   = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
							$acumImpuesto             += $valArrayInventario['impuesto'];
							$totalContabilizacionItem += $valArrayInventario['impuesto'];
						}

						//============================== CALC CONTRA PRECIO ACTIVO FIJO ================================//
						if($contraPrecio > 0){
							$arrayAsiento[0][$contraPrecio]['type'] = 'cuentaPago';
							$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

							$arrayGlobalEstado[$estado] += $totalContabilizacionItem;
							$arrayItemEstado[$estado]   += $totalContabilizacionItem;

							if($arrayAsiento[0][$contraPrecio][$estado] > 0){ $arrayAsiento[0][$contraPrecio][$estado] += $totalContabilizacionItem; }
							else{ $arrayAsiento[0][$contraPrecio][$estado] = $totalContabilizacionItem; }

							//ARRAY ASIENTO CONTABLE PRECIO
							$estadoPrecio                     = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];
							$arrayGlobalEstado[$estadoPrecio] += $totalContabilizacionItem;
							$arrayItemEstado[$estadoPrecio]   += $totalContabilizacionItem;
							$acumSubtotal                     += $valArrayInventario['precio'];

							//ARRAY ASIENTO CONTABLE ACTIVO FIJO O GASTO
							if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] > 0){
								$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] += $totalContabilizacionItem;
							}
							else{
								$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] = $totalContabilizacionItem;
							}

							//ARRAY ASIENTO CONTABLE IMPUESTO
							if($valArrayInventario['cuenta_compra'] != "" && $valArrayInventario['cruzar_costo_activo_fijo'] == "false" && $valArrayInventario['check_opcion_contable'] == "activo_fijo"){
								$estadoImpuesto = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
								$cuentaImpuesto = $valArrayInventario['cuenta_compra'];

								//VERIFICAMOS SI EL COSTO DLE ACTIVO FIJO SE CRUZA CON EL COSTO DEL IMPUESTO
								$arrayAsiento[0][$cuentaPrecio][$estadoPrecio] = $arrayAsiento[0][$cuentaPrecio][$estadoPrecio] - $valArrayInventario['impuesto'];
								$arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto] += $valArrayInventario['impuesto'];

								//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
								if($valArrayInventario === end($arrayInventarioFactura)) {
					        $arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto] = ROUND($arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto],$decimalesMoneda);
					    	}
							}

							$acumCuentaClientes   = $contraPrecio;
							$estadoCuentaClientes = $estado;
						}

						//============================== SI PERTENECE A UNA ENTRADA DE ALMACEN CERRAR CUENTAS TRANSITO =================================//
						if ($valArrayInventario['nombre_referencia']=='Entrada de Almacen') {
							if($arrayCuentasTransito['id_cuenta_colgaap_debito'] =='' || $arrayCuentasTransito['id_cuenta_colgaap_credito'] ==''){

								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "Aviso.\nEl item Codigo $valArrayInventario[codigo] pertenece a una entrada de Almacen pero no hay cuentas de transito configiradas en el panel de control");
							}

							$arrayAsiento[0][$arrayCuentasTransito['cuenta_colgaap_debito']]['credito'] += $valArrayInventario['precio'];
							$arrayAsiento[0][$arrayCuentasTransito['cuenta_colgaap_credito']]['debito'] += $valArrayInventario['precio'];
						}

					}
					else if($valArrayInventario['inventariable'] == 'false'){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado en la contabilizacion");
					}

					if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No establece doble partida por favor revise la configuracion de contabilizacion");

					}
					else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "Aviso.\nEl item Codigo $valArrayInventario[codigo] Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion");
					}
				}
				if($msjErrorCcosto != ''){
					$this->rollBack($id_factura,1);
					return array('status' => false, 'detalle'=> "nLos siguientes items no tienen centro de costo \n'.$msjErrorCcosto");
				}
			}

			$arrayGlobalEstado['debito']  = round($arrayGlobalEstado['debito'],$_SESSION['DECIMALESMONEDA']);
			$arrayGlobalEstado['credito'] = round($arrayGlobalEstado['credito'],$_SESSION['DECIMALESMONEDA']);

			if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "Ha ocurrido un problema de contabilizacion, favor revise la configuracion de contabilizacion. Si el problema persiste consulte con soporte tecnico");
			}
			else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0 || $arrayGlobalEstado['debito'] =='' || $arrayGlobalEstado['credito'] ==''){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "Contabilizacion en saldo 0. Si el problema persiste consulte con soporte tecnico");
			}

			$acumImpuesto = ROUND($acumImpuesto,$decimalesMoneda);

			//=========================== QUERY RETENCIONES ==========================//
			$acumRetenciones  = 0;
			$contRetencion    = 0;
			$estadoRetencion  = $estadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
			$sqlRetenciones   = "SELECT 
									valor,
									codigo_cuenta,
									tipo_retencion,
									cuenta_autoretencion,
									base 
								FROM compras_facturas_retenciones WHERE id_factura_compra='$id_factura' AND activo=1";
			$queryRetenciones = $this->mysql->query($sqlRetenciones);

			while($rowRetenciones = $this->mysql->fetch_array($queryRetenciones)){
				$valorBase           = $rowRetenciones['base'];
				$valorRetencion      = $rowRetenciones['valor'];
				$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
				$tipoRetencion       = $rowRetenciones['tipo_retencion'];
				$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

				if(is_nan($arrayAsiento[0][$codigoRetencion][$estadoRetencion])){ $arrayAsiento[0][$codigoRetencion][$estadoRetencion] = 0; }

				//CALCULO RETEIVA
				if($tipoRetencion == "ReteIva"){ 																		      //CALCULO RETEIVA
					if($acumImpuesto < $valorBase){ continue; }															//BASE RETENCION

					$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				}
				else{ 																									//CALCULO RETE Y RETEICA
					if($acumSubtotal<$valorBase) { continue; }															//BASE RETENCION

					$acumRetenciones += ROUND($acumSubtotal * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal * $valorRetencion/100, $decimalesMoneda);
				}

				if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

					if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "No se ha configurado la cuenta Colgaap Autorretencion.");
					}

					if(is_nan($arrayAsiento[0][$cuentaAutoretencion][$estadoCuentaClientes])){ $arrayAsiento[0][$cuentaAutoretencion][$estadoCuentaClientes] = 0; }
					$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
					$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[0][$codigoRetencion][$estadoRetencion];
				}
			}

			$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] -= $acumRetenciones;
			$saldoGlobalFactura = $arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes];						//VARIABLE GLOBAL TOTAL FACTURA

			//============================ SALDO ANTICIPO ============================//
	    	$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] = $arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes];
			$saldoClientes = $arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes];
			$saldoAnticipo = $arrayAnticipo['total'];

			if(round($saldoAnticipo,$_SESSION['DECIMALESMONEDA']) > 0 && round($saldoAnticipo,$_SESSION['DECIMALESMONEDA']) > round($saldoClientes,$_SESSION['DECIMALESMONEDA'])){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "Los anticipos no pueden ser mayores a la factura de compra");
			}
			else{
				$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] -= $saldoAnticipo;
			}

			foreach($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo){
				$arrayCampo['debito']  = 0;
				$arrayCampo['credito'] = 0;

				$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
			}

			$totalDebito  = 0;
			$totalCredito = 0;

			//============================ CONTABILIZACION ===========================//
			$contAnticipos = 0;
			// print_r($arraya)
			foreach($arrayAsiento AS $idCcos => $arrayCuenta){
				foreach($arrayCuenta AS $cuenta => $arrayCampo){
					if(is_nan($cuenta) || $cuenta == 0){ continue; }
					$cuenta = $cuenta * 1;

					$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
					$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

					if($arrayCampo['debito'] > 0 || $arrayCampo['credito'] > 0){

						// SALDO POR PAGAR EN FACTURACION
						if($estadoCuentaPago == 'Credito' && $arrayCampo['type']=='cuentaPago'){
							$saldoGlobalFacturaSinAbono += ($arrayCampo['debito'] > $arrayCampo['credito'])? $arrayCampo['debito'] : $arrayCampo['credito'];
						}

						// BODY INSERT
						$valueInsertAsientos .= "('$id_factura',
												'$numeroFactura',
												'FC',
												'Factura de Compra',
												'$id_factura',
												'FC',
												'".$this->arrayConsecutivo['consecutivo']."',
												'$fechaFactura',
												'".$arrayCampo['debito']."',
												'".$arrayCampo['credito']."',
												'$cuenta',
												'$this->id_proveedor',
												'$this->id_sucursal',
												'$this->id_empresa',
												'$idCcos'),";

					}

					//============================= ANTICIPOS ============================//
					if($acumCuentaClientes == $cuenta){
						foreach($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo){
							$contAnticipos++;
							$arrayCampo['debito']  = 0;
							$arrayCampo['credito'] = 0;

							$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

							$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
							$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

							if(is_nan($datosAnticipo['cuenta_colgaap']) || $datosAnticipo['cuenta_colgaap']==0){ continue; }
							$datosAnticipo['cuenta_colgaap'] = $datosAnticipo['cuenta_colgaap'] * 1;

							$valueInsertAsientos .= "('$id_factura',
													'$numeroFactura',
													'FC',
													'Factura de Compra',
													'$datosAnticipo[id_documento]',
													'$datosAnticipo[tipo_documento]',
													'',
													'$fechaFactura',
													'$arrayCampo[debito]',
													'$arrayCampo[credito]',
													'$datosAnticipo[cuenta_colgaap]',
													'$datosAnticipo[id_tercero]',
													'$this->id_sucursal',
													'$this->id_empresa',
													'$idCcos'),";

						}
					}
				}
			}

			$totalDebito  = round($totalDebito,$_SESSION['DECIMALESMONEDA']);
			$totalCredito = round($totalCredito,$_SESSION['DECIMALESMONEDA']);

			if($totalDebito != $totalCredito){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "La contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
			}

			if($contAnticipos > 0){
				$sqlAnticipo = "UPDATE comprobante_egreso_cuentas AS C, anticipos AS A
												SET C.saldo_pendiente=C.saldo_pendiente-A.valor
												WHERE C.id=A.id_cuenta_anticipo
												AND C.activo=1
												AND A.activo=1
												AND A.id_documento='$id_factura'
												AND A.tipo_documento='FC'";
				$queryAnticipo = $this->mysql->query($sqlAnticipo);
			}

			$valueInsertAsientos        = substr($valueInsertAsientos, 0, -1);
			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);

			$sqlContabilizar = "INSERT INTO
										contabilizacion_compra_venta(
											id_item,
											codigo_puc,
											caracter,
											descripcion,
											id_documento,
											tipo_documento,
											id_empresa,
											id_sucursal,
											id_bodega
										)
									VALUES
										$valueInsertContabilizacion";
			$queryContabilizar = $this->mysql->query($sqlContabilizar);

			if(!$queryContabilizar){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "Error al insertar la configuracion contable del documento");
			}

			//CUENTAS NIIF
			$sql = "INSERT INTO
													asientos_colgaap(
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
														id_sucursal,
														id_empresa,
														id_centro_costos
													)
												VALUES
													$valueInsertAsientos";
			$query = $this->mysql->query($sql);
			if ($query) {
					return array('status'=>true);
			}
			else{
				$arrayError[0]='Se produjo un error al insertar la contabilidad el documento (Cod. Error 601)';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
    			$this->rollback($id_factura,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}

		}

		/**
		 * setAsientosNiif Contabilizar la factura en norma Niif
		 * @param Int $id_factura        Id de la factura a causar
		 * @param Array $arrayCuentaPago Array con la informacion de la cuenta de pago
		 * @return Array Resultado del proceso {status: true o false},{detalle: en caso de ser error se detallara en este campo}
		 */
		public function setAsientosNiif($id_factura,$consecutivo,$arrayCuentaPago,$arrayAnticipo){
			$decimalesMoneda  = ($this->decimales_moneda >= 0)? $this->decimales_moneda : 0;
			$cuentaPago       = $arrayCuentaPago['cuentaNiif'];
			$estadoCuentaPago = $arrayCuentaPago['estado'];

			//============================= QUERY CUENTAS ============================//
			$ivaAcumulado      = 0;
			$precioAcumulado   = 0;

			$whereIdItemsCuentas = '';

			$sql   = "SELECT fecha_inicio,prefijo_factura,numero_factura FROM compras_facturas WHERE activo=1 AND id=$id_factura";
			$query = $this->mysql->query($sql,$this->mysql->link);
			$fechaFactura = $this->mysql->result($query,0,'fecha_inicio');
			$numeroFactura = ($this->mysql->result($query,0,'prefijo_factura')<>'')? $this->mysql->result($query,0,'prefijo_factura')." ".$this->mysql->result($query,0,'numero_factura') : $this->mysql->result($query,0,'numero_factura');


			$sqlDoc = "SELECT
									CFI.id,
									CFI.id_inventario AS id_item,
									CFI.codigo,
									CFI.nombre AS nombre_equipo,
									CFI.cantidad,
									CFI.costo_unitario AS precio,
									CFI.descuento,
									CFI.tipo_descuento,
									CFI.id_impuesto,
									CFI.valor_impuesto,
									CFI.cuenta_impuesto_niif AS cuenta_impuesto,
									I.cruzar_costo_activo_fijo,
									I.cuenta_compra_niif AS cuenta_compra,
									CFI.inventariable,
									CFI.check_opcion_contable,
									CFI.opcion_gasto,
									CFI.opcion_costo,
									CFI.opcion_activo_fijo,
									CFI.id_centro_costos,
									CFI.id_consecutivo_referencia AS id_referencia,
									CFI.nombre_consecutivo_referencia AS nombre_referencia
								FROM
									compras_facturas_inventario AS CFI
								LEFT JOIN
									impuestos AS I
								ON
									CFI.id_impuesto = I.id
								WHERE
									CFI.id_factura_compra = '$id_factura'
								AND
									CFI.activo = 1";
			$queryDoc = $this->mysql->query($sqlDoc);
			while($rowDoc = $this->mysql->fetch_array($queryDoc)){

				//CALCULO DEL PRECIO
				$impuesto = 0;
				$precio   = $rowDoc['precio'] * $rowDoc['cantidad'];
				$costo    = $rowDoc['costo'] * $rowDoc['cantidad'];

				if($rowDoc['descuento'] > 0){ $precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio - ROUND(($rowDoc['descuento'] * $precio) / 100, $decimalesMoneda) : $precio - $rowDoc['descuento']; }
				if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0){ $impuesto = $precio * $rowDoc['valor_impuesto'] / 100; }

				$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR id_items = '.$rowDoc['id_item']: 'id_items = '.$rowDoc['id_item'];

				$arrayInfoItems[$rowDoc['id_item']] = array(
															'codigo'                => $rowDoc['codigo'],
															'nombre_item'           => $rowDoc['nombre'],
															'check_opcion_contable' => $rowDoc['check_opcion_contable'],
															'opcion_gasto'          => $rowDoc['opcion_gasto'],
															'opcion_costo'          => $rowDoc['opcion_costo'],
															'opcion_activo_fijo'    => $rowDoc['opcion_activo_fijo'],
														);

				$arrayInventarioFactura[$rowDoc['id']] = array(
																'id_factura_inventario' 		=> $rowDoc['id'],
																'codigo'                		=> $rowDoc['codigo'],
																'nombre_equipo'         		=> $rowDoc['nombre_equipo'],
																'impuesto'              		=> $impuesto,
																'cuenta_impuesto'       		=> $rowDoc['cuenta_impuesto'],
																'cruzar_costo_activo_fijo'	=> $rowDoc['cruzar_costo_activo_fijo'],
																'cuenta_compra'							=> $rowDoc['cuenta_compra'],
																'precio'                    => $precio,
																'id_items'                  => $rowDoc['id_item'],
																'inventariable'             => $rowDoc['inventariable'],
																'cantidad'                  => $rowDoc['cantidad'],
																'id_centro_costos'          => $rowDoc['id_centro_costos'],
																'check_opcion_contable'     => $rowDoc['check_opcion_contable'],
																'id_referencia'             => $rowDoc['id_referencia'],
																'nombre_referencia'         => $rowDoc['nombre_referencia']
															);
			}

			$sqlItemsCuentas = "SELECT id,id_items,descripcion,id_puc,puc,tipo,estado
								FROM items_cuentas_niif
								WHERE activo = 1
								AND id_empresa = '$this->id_empresa'
								AND estado = 'compra'
								AND ($whereIdItemsCuentas)
								GROUP BY id_items,descripcion
								ORDER BY id_items ASC";
			$queryItemsCuentas = $this->mysql->query($sqlItemsCuentas);

			$whereCuentaCcos = "";
			while($rowCuentasItems = $this->mysql->fetch_array($queryItemsCuentas)){
				if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; $rowCuentasItems['tipo'] = 'credito'; }
				if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['tipo'] = 'debito'; }

				// VALIDAR QUE EL ITEM TENGA LAS CUENTAS CONFIGURADAS
				switch ($rowCuentasItems['descripcion']){
					case 'costo':
							if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_costo']=='true'){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo ".$arrayInfoItems[$rowCuentasItems['id_items']]['codigo']." No se ha configurado en la cuenta de $rowCuentasItems[descripcion] en $rowCuentasItems[estado]");
							}
						break;
					case 'gasto':
							if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_gasto']=='true'){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo ".$arrayInfoItems[$rowCuentasItems['id_items']]['codigo']." No se ha configurado en la cuenta de $rowCuentasItems[descripcion] en $rowCuentasItems[estado]");
							}
						break;
					case 'precio':
							if ($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo ".$arrayInfoItems[$rowCuentasItems['id_items']]['codigo']." No se ha configurado en la cuenta de $rowCuentasItems[descripcion] en $rowCuentasItems[estado]");
							}
						break;
					case 'activo_fijo':
							if (($rowCuentasItems['id_puc']=='' || $rowCuentasItems['id_puc']==0) && $arrayInfoItems[$rowCuentasItems['id_items']]['opcion_activo_fijo']=='true'){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo ".$arrayInfoItems[$rowCuentasItems['id_items']]['codigo']." No se ha configurado en la cuenta de $rowCuentasItems[descripcion] en $rowCuentasItems[estado]");
							}
						break;
				}

				$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']] = array('estado' => $rowCuentasItems['tipo'], 'cuenta' => $rowCuentasItems['puc']);

				$valueInsertContabilizacion .=  "('".$rowCuentasItems['id_items']."',
																					'".$rowCuentasItems['puc']."',
																					'".$rowCuentasItems['tipo']."',
																					'".$rowCuentasItems['descripcion']."',
																					'$id_factura',
																					'FC',
																					'$this->id_empresa',
																					'$this->id_sucursal',
																					'$this->id_bodega'),";

				$cuenta      = $rowCuentasItems['puc'];
				$descripcion = $rowCuentasItems['descripcion'];

				if($descripcion == 'precio' || $descripcion == 'gasto' || $descripcion == 'costo' || $descripcion == 'activo_fijo'){ $whereCuentaCcos .= "OR cuenta='$cuenta' "; }
			}

			// VALIDAR LA CUENTA DE PAGO
			foreach($arrayCuentasItems as $id_item => $arrayResult){
				if(!array_key_exists('contraPartida_precio', $arrayResult)){
					$arrayCuentasItems[$id_item]['contraPartida_precio'] = array(
																																				'estado' => 'credito',
																																				'cuenta' => $cuentaPago
																																			);
					$valueInsertContabilizacion .= "('".$arrayResult['id_items']."',
																					 '".$cuentaPago."',
																					 'credito',
																					 'contraPartida_precio',
																					 '$id_factura',
																					 'FC',
																					 '$this->id_empresa',
																					 '$this->id_sucursal',
																					 '$idBodega'),";
				}
			}

			$whereCuentaCcos = substr($whereCuentaCcos, 3, -1);
			$sqlCcos   = "SELECT cuenta,centro_costo FROM puc_niif WHERE id_empresa = '$idEmpresa' AND activo = 1 AND ($whereCuentaCcos)";
			$queryCcos = $this->mysql->query($sqlCcos);

			while($row = $this->mysql->fetch_assoc($queryCcos)){
				$cuenta = $row['cuenta'];
				$cCos   = $row['centro_costo'];

				$arrayCuentaCcos[$cuenta] = $cCos;
			}

			// CONSULTAR LAS CUENTAS DE TRANSITO DE LAS ENTRADAS DE ALMACEN
			$sql = "SELECT
								id_cuenta_niif_debito,
								cuenta_niif_debito,
								id_cuenta_niif_credito,
								cuenta_niif_credito
							FROM
								costo_cuentas_transito
							WHERE
							  activo = 1
							AND
								id_empresa = $this->id_empresa";
			$query = $this->mysql->query($sql);

			$arrayCuentasTransito['id_cuenta_niif_debito']  = $this->mysql->result($query,0,'id_cuenta_niif_debito');
			$arrayCuentasTransito['cuenta_niif_debito']     = $this->mysql->result($query,0,'cuenta_niif_debito');
			$arrayCuentasTransito['id_cuenta_niif_credito'] = $this->mysql->result($query,0,'id_cuenta_niif_credito');
			$arrayCuentasTransito['cuenta_niif_credito']    = $this->mysql->result($query,0,'cuenta_niif_credito');

			$arrayGlobalEstado['debito']  = 0;
			$arrayGlobalEstado['credito'] = 0;

			$arrayItemEstado['debito']  = 0;
			$arrayItemEstado['credito'] = 0;

			$acumSubtotal = 0;
			$acumImpuesto = 0;

			$msjErrorCcosto = '';

			foreach($arrayInventarioFactura AS $valArrayInventario){

				$totalContabilizacionItem   = 0;
				$arrayItemEstado['debito']  = 0;
				$arrayItemEstado['credito'] = 0;

				$idItemArray       = $valArrayInventario['id_items'];										//ID ITEM
				$descripcionCuenta = $valArrayInventario['check_opcion_contable'];							//GASTO, COSTO, ACTIVO FIJO
				$descripcionCuenta = (strlen($descripcionCuenta) > 4)? $descripcionCuenta : 'precio';

				$cuentaOpcional    = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['cuenta'];		//CUENTA OPCION CONTABILIZACION

				$cuentaPrecio   = ($descripcionCuenta != 'precio')? $cuentaOpcional: $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
				$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
				$cuentaImpuesto = ($valArrayInventario['cuenta_impuesto'] > 0)? $valArrayInventario['cuenta_impuesto']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

				//========================= CONTABILIZACION MERCANCIA PARA LA VENTA, COSTO, GASTO =========================//
				//*********************************************************************************************************//
				//CONDICIONAL SI TIENE CCOS
				$cCosPrecio = 0;
				if($arrayCuentaCcos[$cuentaPrecio] == 'Si'){
					if($valArrayInventario['id_centro_costos'] > 0){ $cCosPrecio = $valArrayInventario['id_centro_costos']; }
					else{ $msjErrorCcosto = '\n'.$valArrayInventario['codigo'].' '.$valArrayInventario['nombre_item']; }
				}

				if($descripcionCuenta != 'activo_fijo'){

					//======================================= CALC PRECIO =====================================//
					if($cuentaPrecio > 0){
						$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] += $valArrayInventario['precio']; }
						else{ $arrayAsiento[$cCosPrecio][$cuentaPrecio][$estado] = $valArrayInventario['precio']; }

						$arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
						$arrayItemEstado[$estado]   += $valArrayInventario['precio'];
						$acumSubtotal               += $valArrayInventario['precio'];

						//===================================== CALC IMPUESTO ========================================//
						if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){

							$estado = 'debito';

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[0][$cuentaImpuesto][$estado] > 0){ $arrayAsiento[0][$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
							else{ $arrayAsiento[0][$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }

							//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
							if($valArrayInventario === end($arrayInventarioFactura)) {
				        $arrayAsiento[0][$cuentaImpuesto][$estado] = $arrayAsiento[0][$cuentaImpuesto][$estado];
				    	}

							$arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
							$arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
							$acumImpuesto               += $valArrayInventario['impuesto'];
						}

						//============================== CALC CONTRA PARTIDA PRECIO =================================//
						if($contraPrecio > 0){
							$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

							$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
											: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

							//ARRAY ASIENTO CONTABLE
							if($arrayAsiento[0][$contraPrecio][$estado] > 0){ $arrayAsiento[0][$contraPrecio][$estado] += $contraSaldo; }
							else{ $arrayAsiento[0][$contraPrecio][$estado] = $contraSaldo; }

							$arrayGlobalEstado[$estado] += $contraSaldo;
							$arrayItemEstado[$estado]   += $contraSaldo;

							$acumCuentaClientes   = $contraPrecio;
							$estadoCuentaClientes = $estado;
						}

						//============================== SI PERTENECE A UNA ENTRADA DE ALMACEN CERRAR CUENTAS TRANSITO =================================//
						if ($valArrayInventario['nombre_referencia']=='Entrada de Almacen') {
							if($arrayCuentasTransito['id_cuenta_niif_debito'] =='' || $arrayCuentasTransito['id_cuenta_niif_credito'] ==''){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] pertenece a una entrada de Almacen pero no hay cuentas de transito niif configiradas en el panel de control");
							}

							$arrayAsiento[0][$arrayCuentasTransito['cuenta_niif_debito']]['credito'] += $valArrayInventario['precio'];
							$arrayAsiento[0][$arrayCuentasTransito['cuenta_niif_credito']]['debito'] += $valArrayInventario['precio'];
						}

					}
					else if($valArrayInventario['inventariable'] == 'false'){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado en la contabilizacion");
					}

					if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No establece doble partida por favor revise la configuracion de contabilizacion");
					}
					else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion");
					}
				}
				//======================================= CONTABILIZACION ACTIVO FIJO ======================================//
				//**********************************************************************************************************//
				else{
					if($cuentaPrecio == ''){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "Aviso.\nEl item Codigo $valArrayInventario[codigo] no tiene asignado la cuenta de Activo Fijo Niif en configuracion items");
					}

					//======================================= CALC PRECIO =====================================//
					if($cuentaPrecio > 0){
						$totalContabilizacionItem += $valArrayInventario['precio'];

						//======================================= CALC IMPUESTO =======================================//
						if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
							$estado                   = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
							$acumImpuesto             += $valArrayInventario['impuesto'];
							$totalContabilizacionItem += $valArrayInventario['impuesto'];
						}

						//============================= CALC CONTRA PRECIO ACTIVO FIJO ================================//
						if($contraPrecio > 0){
							$arrayAsiento[0][$contraPrecio]['type'] = 'cuentaPago';
							$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

							$arrayGlobalEstado[$estado] += $totalContabilizacionItem;
							$arrayItemEstado[$estado]   += $totalContabilizacionItem;

							if($arrayAsiento[0][$contraPrecio][$estado] > 0){ $arrayAsiento[0][$contraPrecio][$estado] += $totalContabilizacionItem; }
							else{ $arrayAsiento[0][$contraPrecio][$estado] = $totalContabilizacionItem; }

							//ARRAY ASIENTO CONTABLE PRECIO
							$estadoPrecio                      = $arrayCuentasItems[$idItemArray][$descripcionCuenta]['estado'];
							$arrayGlobalEstado[$estadoPrecio] += $totalContabilizacionItem;
							$arrayItemEstado[$estadoPrecio]   += $totalContabilizacionItem;
							$acumSubtotal                     += $valArrayInventario['precio'];

							//ARRAY ASIENTO CONTABLE ACTIVO FIJO O GASTO
							if($arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] > 0){
								$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] += $totalContabilizacionItem;
							}
							else{
								$arrayAsiento[$cCosPrecio][$cuentaPrecio][$estadoPrecio] = $totalContabilizacionItem;
							}

							//ARRAY ASIENTO CONTABLE IMPUESTO
							if($valArrayInventario['cuenta_compra'] != "" && $valArrayInventario['cruzar_costo_activo_fijo'] == "false" && $valArrayInventario['check_opcion_contable'] == "activo_fijo"){
								$estadoImpuesto = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];
								$cuentaImpuesto = $valArrayInventario['cuenta_compra'];

								//VERIFICAMOS SI EL COSTO DLE ACTIVO FIJO SE CRUZA CON EL COSTO DEL IMPUESTO
								$arrayAsiento[0][$cuentaPrecio][$estadoPrecio] = $arrayAsiento[0][$cuentaPrecio][$estadoPrecio] - $valArrayInventario['impuesto'];
								$arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto] += $valArrayInventario['impuesto'];

								//REDONDEAMOS EL VALOR TOTAL DEL IMPUESTO DEL DOCUMENTO AL FINAL DEL CICLO
								if($valArrayInventario === end($arrayInventarioFactura)) {
					        $arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto] = ROUND($arrayAsiento[0][$cuentaImpuesto][$estadoImpuesto],$decimalesMoneda);
					    	}
							}

							$acumCuentaClientes   = $contraPrecio;
							$estadoCuentaClientes = $estado;
						}

						//============================== SI PERTENECE A UNA ENTRADA DE ALMACEN CERRAR CUENTAS TRANSITO =================================//
						if ($valArrayInventario['nombre_referencia']=='Entrada de Almacen') {
							if($arrayCuentasTransito['id_cuenta_niif_debito'] =='' || $arrayCuentasTransito['id_cuenta_niif_credito'] ==''){
								$this->rollBack($id_factura,1);
								return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] pertenece a una entrada de Almacen pero no hay cuentas de transito niif configiradas en el panel de control");
							}

							$arrayAsiento[0][$arrayCuentasTransito['cuenta_niif_debito']]['credito'] += $valArrayInventario['precio'];
							$arrayAsiento[0][$arrayCuentasTransito['cuenta_niif_credito']]['debito'] += $valArrayInventario['precio'];
						}

					}
					else if($valArrayInventario['inventariable'] == 'false'){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado en la contabilizacion");
					}

					if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No establece doble partida por favor revise la configuracion de contabilizacion");
					}
					else if($arrayItemEstado['debito'] == 0 || $arrayItemEstado['credito'] == 0){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion");
					}
				}
				if($msjErrorCcosto != ''){
					$this->rollBack($id_factura,1);
					return array('status' => false, 'detalle'=> "Los siguientes items no tienen centro de costo $msjErrorCcosto");
				}
			}

			$arrayGlobalEstado['debito']  = round($arrayGlobalEstado['debito'],$_SESSION['DECIMALESMONEDA']);
			$arrayGlobalEstado['credito'] = round($arrayGlobalEstado['credito'],$_SESSION['DECIMALESMONEDA']);

			if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "Ha ocurrido un problema de contabilizacion niif, favor revise la configuracion de contabilizacion Si el problema persiste consulte a soporte");
			}
			else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0 || $arrayGlobalEstado['debito'] =='' || $arrayGlobalEstado['credito'] ==''){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "Contabilizacion en saldo 0 en Niif Si el problema persiste consulte con soporte tecnico");
			}

			$acumImpuesto = ROUND($acumImpuesto,$decimalesMoneda);

			//=========================== QUERY RETENCIONES ==========================//
			$acumRetenciones  = 0;
			$contRetencion    = 0;
			$estadoRetencion  = $estadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
			$sqlRetenciones   = "SELECT valor,codigo_cuenta_niif,tipo_retencion,cuenta_autoretencion_niif,base 
								FROM compras_facturas_retenciones WHERE id_factura_compra='$id_factura' AND activo=1";
			$queryRetenciones = $this->mysql->query($sqlRetenciones);

			while($rowRetenciones = $this->mysql->fetch_array($queryRetenciones)){
				$valorBase           = $rowRetenciones['base'];
				$valorRetencion      = $rowRetenciones['valor'];
				$codigoRetencion     = $rowRetenciones['codigo_cuenta_niif'];
				$tipoRetencion       = $rowRetenciones['tipo_retencion'];
				$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion_niif'];

				if(is_nan($arrayAsiento[0][$codigoRetencion][$estadoRetencion])){ $arrayAsiento[0][$codigoRetencion][$estadoRetencion] = 0; }

				//CALCULO RETEIVA
				if($tipoRetencion == "ReteIva"){ 																		      //CALCULO RETEIVA
					if($acumImpuesto < $valorBase){ continue; }															//BASE RETENCION

					$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				}
				else{ 																									//CALCULO RETE Y RETEICA
					if($acumSubtotal<$valorBase) { continue; }															//BASE RETENCION

					$acumRetenciones += ROUND($acumSubtotal * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$codigoRetencion][$estadoRetencion] += ROUND($acumSubtotal * $valorRetencion/100, $decimalesMoneda);
				}

				if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

					if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
						$this->rollBack($id_factura,1);
						return array('status' => false, 'detalle'=> "No se ha configurado la cuenta niif de la AutoRetencion");
					}

					if(is_nan($arrayAsiento[0][$cuentaAutoretencion][$estadoCuentaClientes])){ $arrayAsiento[0][$cuentaAutoretencion][$estadoCuentaClientes] = 0; }
					$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
					$acumRetenciones -= ROUND($acumSubtotal*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[0][$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[0][$codigoRetencion][$estadoRetencion];
				}
			}

			//TOTAL CUENTA PAGO (CLIENTES CAJA Y CUENTA ANTICIPO)
			$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] -= $acumRetenciones;

			//============================ SALDO ANTICIPO ============================//
			$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] = $arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes];
			$saldoClientes = $arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes];
			$saldoAnticipo = $arrayAnticipo['total'];

			if(round($saldoAnticipo,$_SESSION['DECIMALESMONEDA']) > 0 && round($saldoAnticipo,$_SESSION['DECIMALESMONEDA']) > round($saldoClientes,$_SESSION['DECIMALESMONEDA'])){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "Los anticipos no pueden ser mayores al valor de la factura");
			}
			else{
				$arrayAsiento[0][$acumCuentaClientes][$estadoCuentaClientes] -= $saldoAnticipo;
			}

			foreach($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo){
				$arrayCampo['debito']  = 0;
				$arrayCampo['credito'] = 0;

				$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
			}

			$totalDebito  = 0;
			$totalCredito = 0;
			
			//============================ CONTABILIZACION ===========================//
			foreach($arrayAsiento AS $idCcos => $arrayCuenta){
				foreach($arrayCuenta AS $cuenta => $arrayCampo){
					if(is_nan($cuenta) || $cuenta == 0){ continue; }
					$cuenta = $cuenta * 1;

					$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
					$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

					if($arrayCampo['debito'] > 0 || $arrayCampo['credito'] > 0){
						// fechaFactura
						// numeroFactura
						// BODY INSERT
						$valueInsertAsientos .= "('$id_factura',
													'".$this->arrayConsecutivo['consecutivo']."',
													'FC',
													'Factura de Compra',
													'$id_factura',
													'FC',
													'$numeroFactura',
													'$fechaFactura',
													'".$arrayCampo['debito']."',
													'".$arrayCampo['credito']."',
													'$cuenta',
													'$this->id_proveedor',
													'$this->id_sucursal',
													'$this->id_empresa',
													'$idCcos'),";

						
					}

					//============================= ANTICIPOS ============================//
					if($acumCuentaClientes == $cuenta){
						foreach($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo){

							$arrayCampo['debito']  = 0;
							$arrayCampo['credito'] = 0;

							$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

							$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
							$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

							if(is_nan($datosAnticipo['cuenta_niif']) || $datosAnticipo['cuenta_niif']==0){ continue; }
							$datosAnticipo['cuenta_niif'] = $datosAnticipo['cuenta_niif'] * 1;

							$valueInsertAsientos .= "('$id_factura',
													'".$this->arrayConsecutivo['consecutivo']."',
													'FC',
													'Factura de Compra',
													'$datosAnticipo[id_documento]',
													'$datosAnticipo[tipo_documento]',
													'',
													'$fechaFactura',
													'$arrayCampo[debito]',
													'$arrayCampo[credito]',
													'$datosAnticipo[cuenta_niif]',
													'$datosAnticipo[id_tercero]',
													'$this->id_sucursal',
													'$this->id_empresa',
													'$idCcos'),";

							
						}
					}
				}
			}

			$totalDebito  = round($totalDebito,$_SESSION['DECIMALESMONEDA']);
			$totalCredito = round($totalCredito,$_SESSION['DECIMALESMONEDA']);

			if($totalDebito != $totalCredito){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "La contabilizacion ingresada no cumple doble partida en niif, debito: $totalDebito , credito: $totalCredito");
			}

			$valueInsertAsientos        = substr($valueInsertAsientos, 0, -1);
			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);

			$sqlContabilizar = "INSERT INTO
														contabilizacion_compra_venta_niif(
															id_item,
															codigo_puc,
															caracter,
															descripcion,
															id_documento,
															tipo_documento,
															id_empresa,
															id_sucursal,
															id_bodega
														)
													VALUES
														$valueInsertContabilizacion";
			$queryContabilizar = $this->mysql->query($sqlContabilizar);

			if(!$queryContabilizar){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "No se inserto la configuracion contables niif" );
			}

			//CUENTAS NIIF
			$sql =  "INSERT INTO
												asientos_niif(
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
													id_sucursal,
													id_empresa,
													id_centro_costos
												)
											VALUES
												$valueInsertAsientos";
			$query = $this->mysql->query($sql);
			if ($query) {
					return array('status'=>true);
			}
			else{
				$arrayError[0]='Se produjo un error al insertar la contabilidad niif el documento (Cod. Error 601)';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
    			$this->rollback($id_factura,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}
    	}

    	/**
    	 * updateInventario Actualizar las unidades de inventario
    	 * @param  Int $id_factura Id de la factura
    	 * @return Array  Si se genera un error se retorna array con el detalle del error
    	 */
    	public function updateInventario($id_factura,$opc){
    		$arrayDatos = array(
								"campo_fecha"             => "fecha_inicio",
								"tablaPrincipal"          => "compras_facturas",
								"id_documento"            => "$id_factura",
								"campos_tabla_inventario" => " id_inventario AS id_item ",
								"tablaInventario"         => "compras_facturas_inventario",
								"idTablaPrincipal"        => "id_factura_compra",
								"documento"               => "FC",
								"descripcion_documento"   => "Factura de compra",
								);

			$sql   = "SELECT SUM(cantidad) AS cantidad_total,
							costo_unitario,
							IF(descuento>0,
									IF(tipo_descuento='porcentaje',
										SUM(cantidad * costo_unitario)-SUM(cantidad * costo_unitario * descuento)/100 ,
										SUM( (cantidad * costo_unitario) - descuento)
										),
							SUM(cantidad * costo_unitario) ) AS costo_total,
							id_inventario AS id_item
						FROM compras_facturas_inventario
						WHERE id_factura_compra='$id_factura'
							AND activo=1
							AND inventariable='true'
							AND check_opcion_contable=''
							AND nombre_consecutivo_referencia<>'Entrada de Almacen'
						GROUP BY id_inventario,tipo_descuento";

	
			// echo "in";
			// GENERAR EL MOVIMIENTO DE INVENTARIO
			include_once '../../../LOGICALERP/funciones_globales/Clases/ClassInventory.php';
			$objectInventory = new ClassInventory($this->mysql);

			$params['sqlItems']              = $sql;
			$params['id_bodega']             = $this->id_bodega;
			$params['event']                 = ($opc=='agregar')? 'add' : 'remove' ;
			$params['id_documento']          = $id_factura;
			$params['nombre_documento']      = "Factura de compra";
			$params['consecutivo_documento'] = '';
			$objectInventory->updateInventory($params);
			// if(!$query){
			// 	$arrayError[0]='Se produjo un error generar el movimiento de inventario (Cod. Error 601)';
			// 	$arrayError[1]="Error numero: ".$this->mysql->errno();
			   //  			$arrayError[2]="Error detalle: ".$this->mysql->error();
			   //  			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
			   //  			$this->rollback($id_factura,1);
			   //      		return array('status'=>false,'detalle'=>$arrayError);
			// }
			// else{ 
			return array('status' => true); 
			// }
    	}

    	/**
    	 * updateRetenciones Dar de baja las retenciones de la factura de venta
    	 * @param  Int $id_factura Id de la factura de venta
    	 * @return Array array con el resultado de la ejecucion
    	 */
    	public function updateRetenciones($id_factura){
    		$sql="UPDATE compras_facturas_retenciones SET activo=0 WHERE id_factura_compra=$id_factura ";
    		$query=$this->mysql->query($sql,$this->mysql->link);
    		if(!$query){
				$arrayError[0]='Se produjo un error actualizar las retenciones';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
    			$this->rollback($id_factura,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}
			else{ return array('status' => true); }
    	}

    	/**
    	 * updateItemsFactura Dar de baja items de la factura de venta
    	 * @param  Int $id_factura Id de la factura de venta
    	 * @return Array array con el resultado de la ejecucion
    	 */
    	public function updateItemsFactura($id_factura){
    		$sql="UPDATE compras_facturas_inventario SET activo=0 WHERE id_factura_compra=$id_factura ";
    		$query=$this->mysql->query($sql,$this->mysql->link);
    		if(!$query){
				$arrayError[0]='Se produjo un error actualizar los items';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
    			$this->rollback($id_factura,1);
        		return array('status'=>false,'detalle'=>$arrayError);
			}
			else{ return array('status' => true); }
    	}

		/**
		 * rollBack deshacer los cambios realizados
		 * @param  Int $id_factura Id de la factura a realizar rollback
		 * @param  Int $nivel      Nivel del rollback a realizar
		 */
		public function rollBack($id_factura,$nivel, $sentencia = NULL){
			if ($this->actionUpdate==true) {
				$sentencia = " estado=0 ";
			}
			else if($sentencia==NULL){
				$sentencia = " estado=0,prefijo_factura='',numero_factura='',consecutivo='' " ;
			}

			if ($nivel>=1){
				$sql="UPDATE compras_facturas SET $sentencia WHERE id_empresa=$this->id_empresa AND id=$id_factura; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM anticipos WHERE id_empresa=$this->id_empresa AND id_documento=$id_factura; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_colgaap WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_factura AND tipo_documento='FC'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_niif WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_factura AND tipo_documento='FC'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);
			}
			if ($nivel>=2){
				$this->updateInventario($id_factura,"remove");
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

		    // $httpStatusCode = 521;
			// $httpStatusMsg  = 'Web server is down';
			$phpSapiName    = substr(php_sapi_name(), 0, 3);
			// $_SERVER['SERVER_PROTOCOL'];
			// HTTP/1.1
		    // header('HTTP/1.0 ' . $response['status'] . ' ' . $http_response_code[$response['status']]);
			if ($phpSapiName == 'cgi' || $phpSapiName == 'fpm') {
			    // header('Status: '.$http_response_code[$response['status']].' '.$response['status']);
			} else {
			    $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
			    // header($protocol.' '.$http_response_code[$response['status']].' '.$response['status']);
			}
			// print_r($response);
			// print_r($_SERVER);
			// echo 'HTTP/1.1 ' . $response['status'] . ' ' . $http_response_code[$response['status']];
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
			// print_r($_SERVER);
		    $json_response = json_encode($response['data']);
		    echo $json_response;
		    exit;
		}

	}