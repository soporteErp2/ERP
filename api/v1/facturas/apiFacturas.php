<?php

	include '../../../misc/ConnectDb/class.ConnectDb.php';
	/**
	 * @apiDefine Ventas Se requieren permisos de ventas
	 * Para crear, actualizar o eliminar documentos, se requieren los respectivos permisos del modulo de ventas
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
		private $cuenta_ingreso = NULL;
		private $consecuivo_factura = NULL;
		private $fecha_documento = NULL;
		private $invoiceValidationStrings = array(
			"Procesado Correctamente",
			"Documento no enviado, Ya cuenta con env",
			"procesado anteriormente",
			"ha sido autorizada"
		);
		// CONEXION DESARROLLO
		// private $ServidorDb = 'localhost';
		// private $NameDb     = 'erp_bd';

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
			// header('Cache-Control: no-cache, must-revalidate, max-age=0');
			if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER']=='' || $_SERVER['PHP_AUTH_PW']=='') {
				$this->apiResponse(array('status' => 401,'data'=> "Datos de autenticacion incompletos"));
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
		 * @api {get} /facturas/:documento_cliente/:fecha/:fecha_inicio/:fecha_final/:numero_factura/:numero_factura_inicial/:numero_factura_final/:estado Consultar Facturas
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar facturas del sistema.
		 * @apiName get_facturas
		 * @apiGroup Facturas
		 *
		 * @apiParam {String} documento_cliente Numero de documento del cliente
		 * @apiParam {date} fecha Fecha de la factura
		 * @apiParam {date} fecha_inicio Fecha inicial para filtrar las facturas
		 * @apiParam {date} fecha_final Fecha final para filtrar las facturas
		 * @apiParam {String} numero_factura Numero factura completo (Prefijo y consecutivo, Ejemplo: "FV 100")
		 * @apiParam {int} numero_factura_inicial Consecutivo inicial de las facturas a filtrar, Ejemplo: 1
		 * @apiParam {int} numero_factura_final Consecutivo final de las facturas a filtrar, Ejemplo: 100
		 * @apiParam {String} estado Estado de las facturas, campo vacio para listar todo, ("pagadas","pendientes")
		 *
		 * @apiSuccess {date} fecha Fecha de la factura en formato "Y-m-d"
		 * @apiSuccess {String} prefijo Prefijo de la factura (Si tiene)
		 * @apiSuccess {Int} numero_factura Numero consecutivo de la factura
		 * @apiSuccess {String} numero_factura_completo Consecutivo del documento incluyendo el prefijo
		 * @apiSuccess {String} documento_vendedor Documento del vendedor
		 * @apiSuccess {String} nombre_vendedor Nombre del vendedor
		 * @apiSuccess {String} documento_usuario Documento del usuario que realizo el documento
		 * @apiSuccess {String} usuario Usuario que realizo el documento
		 * @apiSuccess {String} documento_cliente Documento del cliente
		 * @apiSuccess {String} cliente Nombre del cliente
		 * @apiSuccess {String} sucursal_cliente Sucursal del cliente a donde se emite la factura
		 * @apiSuccess {String} exento_iva Si la factura no se le aplica IVA
		 * @apiSuccess {String} opcion_cobro Cuenta de pago seleccionada en al documento
		 * @apiSuccess {String} cuenta_pago Cuenta contable (Norma local)
		 * @apiSuccess {String} cuenta_pago_niif Cuenta Contable (Norma Niif)
		 * @apiSuccess {String} metodo_pago Metodo de Pago (Metodos validos para la Dian)
		 * @apiSuccess {Int} dias_pago Dias acordados para el pago
		 * @apiSuccess {String} forma_pago Forma de pago en relacion a los dias
		 * @apiSuccess {Int} id_sucursal Id de la sucursal del documento
		 * @apiSuccess {String} sucursal Sucursal del documento
		 * @apiSuccess {Int} id_bodega Id de la bodega del documento
		 * @apiSuccess {String} bodega Bodega del documento
		 * @apiSuccess {String} observacion Observacion general del documento
		 * @apiSuccess {String} orden_compra Orden de compra digitada
		 * @apiSuccess {Double} total_factura Total de toda la factura
		 * @apiSuccess {Double} saldo_pendiente Valor pendiente de pago de la factura
		 * @apiSuccess {Double} valor_anticipo Valor de anticipo realizado a la factura
		 * @apiSuccess {String} UUID Indentificador unico que retorna la dian cuando se envia la factura electronica
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
		 * 	{
		 * 	   "id": "167119",
		 * 	   "fecha": "2018-09-17",
		 * 	   "prefijo": "PRUE",
		 * 	   "numero_factura": "980000000",
		 * 	   "numero_factura_completo": "PRUE 980000000",
		 * 	   "documento_vendedor": "11111",
		 * 	   "nombre_vendedor": "VENDEDOR DE PRUEBA",
		 * 	   "documento_usuario": "11111",
		 * 	   "usuario": "USUARIO DE PRUEBA",
		 * 	   "documento_cliente": "900467785",
		 * 	   "cliente": "LOGICALSOFT SAS",
		 * 	   "sucursal_cliente": "CALI",
		 * 	   "exento_iva": "No",
		 * 	   "opcion_cobro": "CLIENTES",
		 * 	   "cuenta_pago": "13050501",
		 * 	   "cuenta_pago_niif": "13050501",
		 * 	   "metodo_pago": "Consigancion Bancaria",
		 * 	   "dias_pago": "30",
		 * 	   "forma_pago": "30 DIAS",
		 * 	   "id_sucursal": "46",
		 * 	   "sucursal": "Cali(Principal)",
		 * 	   "id_bodega": "141",
		 * 	   "bodega": "Bodega Principal",
		 * 	   "observacion": "",
		 * 	   "orden_compra": "",
		 * 	   "total_factura": "1545707.07",
		 * 	   "saldo_pendiente": "1545707.07",
		 * 	   "valor_anticipo": "0.00",
		 * 	   "UUID" : "369cf96-644a-43be-93f3-ffc0535ab07e",
		 * 	   "retenciones": [
		 * 	       {
		 * 	           "tipo_retencion": "AutoRetencion",
		 * 	           "retencion": "AUTORRETENCION DE CREE 0.80%",
		 * 	           "porcentaje": "0.800",
		 * 	           "base": "0",
		 * 	           "valor": 13440.792
		 * 	       },
		 * 	       {
		 * 	           "tipo_retencion": "ReteIva",
		 * 	           "retencion": "IMPOVENTAS RETENIDO A FAVOR (ALQ-HON-FIN)",
		 * 	           "porcentaje": "15.000",
		 * 	           "base": "0",
		 * 	           "valor": 2.8215
		 * 	       },
		 * 	       {
		 * 	           "tipo_retencion": "ReteIca",
		 * 	           "retencion": "RETENCION ICA - CALI",
		 * 	           "porcentaje": "1.000",
		 * 	           "base": "82000",
		 * 	           "valor": 16800.99
		 * 	       },
		 * 	       {
		 * 	           "tipo_retencion": "ReteFuente",
		 * 	           "retencion": "RETEFUENTE A FAVOR INTERESES",
		 * 	           "porcentaje": "7.000",
		 * 	           "base": "0",
		 * 	           "valor": 117606.93
		 * 	       }
		 * 	   ],
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
		 * 	           "impuesto": "IVA 19%",
		 * 	           "porcentaje_impuesto": "19.00",
		 * 	           "valor_impuesto": 18.81
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
		 * 	           "impuesto": "",
		 * 	           "porcentaje_impuesto": "",
		 * 	           "valor_impuesto": 0
		 * 	       }
		 * 	   ]
		 * 	},
		 * 	{
		 * 	   "id": "2040",
		 * 	   "fecha": "2015-01-09",
		 * 	   "prefijo": "",
		 * 	   "numero_factura": "474",
		 * 	   "numero_factura_completo": "474",
		 * 	   "documento_vendedor": "11111",
		 * 	   "nombre_vendedor": "VENDEDOR DE PRUEBA",
		 * 	   "documento_usuario": "11111",
		 * 	   "usuario": "USUARIO DE PRUEBA",
		 * 	   "documento_cliente": "900467785",
		 * 	   "cliente": "LOGICALSOFT SAS",
		 * 	   "sucursal_cliente": "CALI",
		 * 	   "exento_iva": "No",
		 * 	   "opcion_cobro": "CLIENTES",
		 * 	   "cuenta_pago": "13050501",
		 * 	   "cuenta_pago_niif": "13050501",
		 * 	   "metodo_pago": "",
		 * 	   "dias_pago": "1",
		 * 	   "forma_pago": "Contado",
		 * 	   "id_sucursal": "46",
		 * 	   "sucursal": "Cali(Principal)",
		 * 	   "id_bodega": "141",
		 * 	   "bodega": "Bodega Principal",
		 * 	   "observacion": "VALOR CORRESPONDIENTE A 6 CUOTA MES PENDIENTE ",
		 * 	   "orden_compra": "",
		 * 	   "total_factura": "531467",
		 * 	   "saldo_pendiente": "531467",
		 * 	   "valor_anticipo": "0.00",
		 * 	   "UUID" : "",
		 * 	   "retenciones": "",
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
		 * 	           "impuesto": "IVA 16%",
		 * 	           "porcentaje_impuesto": "16.00",
		 * 	           "valor_impuesto": 73305.76
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
			if (( $data['numero_factura_inicial']<>'' || $data['numero_factura_final']<>'' ) && ( $data['numero_factura_inicial']=='' || $data['numero_factura_final']=='' ) ){ return array('status'=>false,'detalle'=>'Para consulta en rango de numero de facturas se debe enviar los dos campos (numero_factura_inicial y numero_factura_final)'); }

			$whereFacturas = '';
			if($data['documento_cliente']<>''){ $whereFacturas .= " AND nit='$data[documento_cliente]' "; }
			if($data['fecha']<>''){ $whereFacturas .= " AND fecha_inicio='$data[fecha]' "; }
			if($data['fecha_inicio']<>''){ $whereFacturas .= " AND fecha_inicio BETWEEN $data[fecha_inicio] AND $data[fecha_final] "; }
			if($data['numero_factura']<>''){
				$data['numero_factura'] = str_replace("_"," ",$data['numero_factura']);
				$whereFacturas .= " AND numero_factura_completo='$data[numero_factura]' "; 
			}
			if($data['numero_factura_inicial']<>''){ $whereFacturas .= " AND numero_factura BETWEEN $data[numero_factura_inicial] AND $data[numero_factura_final] "; }
			// pagadas - pendientes
			if($data['estado']<>''){ $whereFacturas .= ($data['estado']=='pagadas')? " AND total_factura_sin_abono=0 " : " AND total_factura_sin_abono>0 " ; }

			$sql="SELECT
					id,
					fecha_inicio AS fecha,
					prefijo,
					numero_factura,
					numero_factura_completo,
					documento_vendedor,
					nombre_vendedor,
					documento_usuario,
					usuario,
					nit AS documento_cliente,
					cliente,
					sucursal_cliente,
					exento_iva,
					configuracion_cuenta_pago AS opcion_cobro,
					cuenta_pago,
					cuenta_pago_niif,
					metodo_pago,
					dias_pago,
					forma_pago,
					id_sucursal,
					sucursal,
					id_bodega,
					bodega,
					observacion,
					orden_compra,
					total_factura,
					total_factura_sin_abono AS saldo_pendiente,
					valor_anticipo,
					UUID,
					cufe
				 FROM ventas_facturas WHERE activo=1 AND id_empresa=$this->id_empresa AND (estado=1 OR estado=2) $whereFacturas LIMIT 0,2";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($result[]=$this->mysql->fetch_assoc($query));
			array_pop($result);
			$whereid_facturas = '';
			foreach ($result as $key => $arrayResult) {
				$whereid_facturas .= ($whereid_facturas=="")? " id_factura_venta=$arrayResult[id] " : " OR id_factura_venta=$arrayResult[id] " ;
			}

			$sql="SELECT
						id_factura_venta,
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
					FROM ventas_facturas_inventario WHERE activo=1 AND ($whereid_facturas) ";
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

				$arrayTotales[$row['id_factura_venta']]['subtotal']  += $subtotal;
				$arrayTotales[$row['id_factura_venta']]['descuento'] += $descuento;
				$arrayTotales[$row['id_factura_venta']]['iva']       += $impuesto;

				$arrayItems[$row['id_factura_venta']][]  = array(
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


			$sql="SELECT id_factura_venta,tipo_retencion,retencion,valor,base FROM ventas_facturas_retenciones WHERE activo=1 AND ($whereid_facturas)";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_assoc($query)) {
						    $valor=0;
			    if ($row['tipo_retencion']=='ReteIva') {
			      	if ($row['base']<$arrayTotales[$row['id_factura_venta']]['iva']) {
			        	$valor = ($arrayTotales[$row['id_factura_venta']]['iva']*$row['valor'])/100;
			    	}
			    }
			    else{
			    	if ($row['base']<$arrayTotales[$row['id_factura_venta']]['subtotal']){
			        	$valor = (($arrayTotales[$row['id_factura_venta']]['subtotal']-$arrayTotales[$row['id_factura_venta']]['descuento'])*$row['valor'])/100;
			    	}
			    }

				$arrayRetenciones[$row['id_factura_venta']][] = array(
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
		 * @api {post} /facturas/ Crear factura
		 * @apiVersion 1.0.0
		 * @apiDescription Registrar facturas en el sistema
		 * @apiName store_facturas
		 * @apiPermission Ventas
		 * @apiGroup Facturas
		 *
		 * @apiParam (Metodos de pago Dian) {Int} 25 Efectivo
		 * @apiParam (Metodos de pago Dian) {Int} 26 Cheque
		 * @apiParam (Metodos de pago Dian) {Int} 27 Transferencia Bancaria
		 * @apiParam (Metodos de pago Dian) {Int} 28 Consigancion Bancaria
		 *
		 * @apiParam {Date} fecha_documento Fecha de la factura formato (Y-M-D)
		 * @apiParam {Date} fecha_vencimiento Fecha de la factura formato (Y-M-D)
		 * @apiParam {String} documento_cliente Numero del documento del cliente
		 * @apiParam {Int} [id_sucursal_cliente] Id de la sucursal del cliente
		 * @apiParam {String} [numero_resolucion] Numero de la resolucion de facturacion, si no se envia se toma por defecto el configurado en el sistema
		 * @apiParam {String} [documento_vendedor] Numero del documento del vendedor de la factura
		 * @apiParam {Int} cuenta_pago Cuenta contable de pago de la factura
		 * @apiParam {Int{2}="25","26","27","28"} cod_metodo_pago Codigo DIAN para los metodos de pago (Ver tabla <b>Metodos de pago Dian</b>)
		 * @apiParam {Int} [id_forma_pago] Id de la forma de pago de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_sucursal Id de la sucursal de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_bodega Id de la sucursal de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {String} [orden_compra] Texto con el codigo de la orden de compra del cliente
		 * @apiParam {String} [observacion] Observacion general de la factura
		 * @apiParam {Double} total_factura Valor completo de la factura
		 * @apiParam {Double} saldo_restante_factura Valor restante de la factura, corresponde al total o si se han hecho pagos al saldo restante
		 * @apiParam {Double} valor_abono Valor de abono o anticipo u otro concepto aplicado al total de la factura, es obligatorio si el saldo de la factura el total de la factura son diferentes
		 * @apiParam {String} email_fe Correos electronicos hacia los cuales se van a enviar las facturas electronicas
 		 * @apiParam {String} info_reserva Contiene un json con los valores de la reserva en Logical Hotels
		 * @apiParam {Object[]} [retenciones] Lista con las retenciones de la factura
		 * @apiParam {Int} retenciones.id Id de la retencion (Consultar en el panel de control del sistema)
		 * @apiParam {Object[]} [anticipos] Lista con los abonos, anticipos, depositos a aplicar a la factura (deben estar en la misma sucursal)
		 * @apiParam {Int} anticipos.consecutivo Consecutivo del documento donde esta el anticipo
		 * @apiParam {Int} anticipos.cuenta Cuenta contable del anticipo a aplicar que pertenece al documento donde se realizo el anticipo
		 * @apiParam {Int} anticipos.valor Valor del anticipo a aplicar, no puede exceder el valor de la factura ni el valor del anticipo mismo
		 * @apiParam {Object[]} items Listado de los articulos a facturar
		 * @apiParam {String} items.codigo Contiene el codigo del item a facturar
		 * @apiParam {Double} items.cantidad Contiene la cantidad item a facturar
		 * @apiParam {Double} items.precio Contiene el precio de venta del item a facturar
		 * @apiParam {Double} [items.observaciones] Contiene la observacion del item a facturar
		 * @apiParam {String="porcentaje","pesos"} [items.tipo_descuento] Contiene el tipo de descuento a aplicar al item puede ser porcentaje o pesos
		 * @apiParam {Double} [items.descuento] Contiene el valor del descuento a aplicar al item, y se aplica segun el tipo de descuento
		 *
		 * @apiSuccess {200} success  informacion registrada
		 * @apiSuccess {200} prefijo  Prefijo asignado a la factura
		 * @apiSuccess {200} numero_factura  Consecutivo de la factura
		 * @apiSuccess {200} numero_factura_completo  Consecutivo completo de la factura, incluye prefijo y numero
		 * @apiSuccess {200} num_resolucion  Numero de la resolucion de facturacion Dian
		 * @apiSuccess {200} fecha_resolucion  fecha de la resolucion de facturacion Dian
		 * @apiSuccess {200} num_inicial  numero inicial de la resolucion de facturacion Dian
		 * @apiSuccess {200} num_final  numero final de la resolucion de facturacion Dian
		 *
		 * @apiSuccessExample Success-Response:
		 *     HTTP/1.1 200 OK
		 *     {
		 *        "success": "informacion registrada",
		 *        "prefijo": "Prefijo de la factura Ej. FV",
		 *        "numero_factura": "Consecutivo de la factura Ej. 105",
		 *        "numero_factura_completo": "Consecutivo completo de la factura Ej. FV 105",
		 *        "num_resolucion": "Numero de la resolucion de facturacion Dian Ej. 12881684 ",
		 *        "fecha_resolucion": "fecha de la resolucion de facturacion Dian Ej. 2019-01-01 ",
		 *        "num_inicial": "numero inicial de la resolucion de facturacion Dian Ej. 1 ",
		 *        "num_final": "numero final de la resolucion de facturacion Dian Ej. 1000 ",
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
			if ($this->usuarioPermisos[21]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para crear facturas'); }

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			// CAMPOS OBLIGATORIOS
			// fecha_documento *
			// fecha_vencimiento *
			// documento_cliente *
			// id_sucursal_cliente
			// numero_resolucion
			// documento_vendedor
			// cuenta_pago *
			// cod_metodo_pago*   Corresponde al codigo de metodo de pago dado por la Dian (25,Efectivo; 26,Cheque;27,Transferencia Bancaria; 28,Consigancion Bancaria)
			// id_forma_pago
			// id_sucursal *
			// id_bodega *
			// orden_compra
			// observacion
			// total_factura *
			// saldo_restante_factura *
			// valor_abono *
			// email_fe
      		// info_reserva
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
			$this->fecha_documento = $data['fecha_documento'];
			if ($data['fecha_vencimiento']=='' || !isset($data['fecha_vencimiento']) ){ $arrayError[] = "El campo fecha_vencimiento es obligatorio"; }
			if ($data['documento_cliente']=='' || !isset($data['documento_cliente'])){ $arrayError[] = "El campo documento cliente  es obligatorio"; }
			$this->id_cliente = $this->getcliente($data['documento_cliente']);
			if ($this->id_cliente==false) {  $arrayError[] = "El cliente no existe en el sistema"; }
			$arraySucursalCliente =  $this->getSucursalCliente($this->id_cliente,$data['id_sucursal_cliente']);
			if ($data['id_sucursal_cliente']<>'') {
				if (!array_key_exists("$data[id_sucursal_cliente]",$arraySucursalCliente)) {
					$arrayError[] = "La sucursal del cliente no existe en el sistema";
				}
			}
			$arrayResoluciones = $this->getResoluciones();
			if ($data['numero_resolucion']<>'' && !array_key_exists("$data[numero_resolucion]",$arrayResoluciones['resoluciones']) ) { $arrayError[] = "La resolucion Numero $data[numero_resolucion] no existe en el sistema"; }
			//else if(!array_key_exists("$data[id_sucursal]",$arrayResoluciones['configuracion'][$data['numero_resolucion']]) ){ $arrayError[] = "La resolucion Numero $data[numero_resolucion] no esta configurada para esa sucursal"; }
			else if(empty($arrayResoluciones['resoluciones'])){ $arrayError[] = "No existe ninguna resolucion configurada en el sistema"; }
			//else if (empty($arrayResoluciones['configuracion'])) { $arrayError[] = "No existe ninguna resolucion configurada para la sucursal en el sistema"; }
			//print_r($arrayResoluciones['configuracion'][$data['id_sucursal']]);
			$id_configuracion_resolucion = '';
			if (empty($data['numero_resolucion'])) {
				foreach ($arrayResoluciones['configuracion'][$data['id_sucursal']] as $numero_resolucion => $arrayResult){
					$id_configuracion_resolucion = ($id_configuracion_resolucion=='' && $arrayResult['predeterminada']=='Si')? $arrayResult['id_resolucion'] : $id_configuracion_resolucion ;
				}
			}
			else{ $id_configuracion_resolucion = $arrayResoluciones['configuracion'][$data['id_sucursal']][$data['numero_resolucion']]['id_resolucion'] ; }
			if ($id_configuracion_resolucion=='' || $id_configuracion_resolucion<=0) { $arrayError[] = "No se ha configurado ninguna resolucion para la sucursal en el sistema"; }

			$prefijo                 = $arrayResoluciones['resolucion'][$id_configuracion_resolucion]['prefijo'];
			$numero_factura          = $arrayResoluciones['resolucion'][$id_configuracion_resolucion]['consecutivo_factura'];

			//VERIFICAMOS QUE EL CONSECUTIVO NO EXISTA EN EL ERP
			$sqlVerificacionConsecutivo =  "SELECT numero_factura FROM ventas_facturas 
											WHERE numero_factura = '$numero_factura' AND id_empresa = $this->id_empresa AND id_configuracion_resolucion = $id_configuracion_resolucion AND estado != 3 LIMIT 0,1";
			$queryVerificacionConsecutivo = $this->mysql->query($sqlVerificacionConsecutivo,$this->mysql->link);
			$verificacionConsecutivo = $this->mysql->result($queryVerificacionConsecutivo,0,'numero_factura');
			if($verificacionConsecutivo != ""){
				$arrayError[] = "El consecutivo $numero_factura ya existe en el ERP intentelo nuevamente, si el problema persiste revise la configuracion de la resolucion.";
			}

			$numero_factura_completo = (!empty($prefijo) && $prefijo<>' ')? "$prefijo $numero_factura" : $numero_factura ;
			$this->consecuivo_factura = $numero_factura_completo;

			if ($numero_factura=='' || $numero_factura<=0) { $arrayError[] = "No se encontro una configuracion de consecutivos valida (Cod. Error 401) "; }

			/* set the gain account */
			$this->cuenta_ingreso = $data['cuenta_ingreso'];

			if ($data['documento_vendedor']<>'') {
				$arrayVendedor = $this->getEmpleado($data['documento_vendedor']);
				if (!array_key_exists("$data[documento_vendedor]",$arrayVendedor)) { $arrayError[] = "El vendedor no existe en el sistema"; }
			}

			if ($data['cuenta_pago']=='' || !isset($data['cuenta_pago'])){ $arrayError[] = "El campo cuenta pago es obligatorio"; }
			$arrayCuentaPago = $this->getCuentaPago($data['cuenta_pago']);
			if (!array_key_exists("$data[cuenta_pago]",$arrayCuentaPago)) {
				$arrayError[] = "La cuenta de pago no existe en el sistema $data[cuenta_pago]";
			}

			$arrayMetodosPago = $this->getMetodosPago();
			if ($data['cod_metodo_pago']=='' || $data['cod_metodo_pago']==0) { $arrayError[] = "El campo cod_metodo_pago es obligatorio"; }
			if (!array_key_exists("$data[cod_metodo_pago]",$arrayMetodosPago) ) {  $arrayError[] = "El metodo de pago no existe en el sistema"; }

			$arrayFormasPago = $this->getFormasPago();
			if ($data['id_forma_pago']<>'') {
				if (!array_key_exists("$data[id_forma_pago]",$arrayFormasPago) ) {  $arrayError[] = "La forma de pago no existe en el sistema"; }
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
					if (!array_key_exists("$arrayResult[id]",$arrayRetenciones)) { $arrayError[] = "la retencion con id  $arrayResult[id] no existe en el sistema o no esta disponible en ventas"; }
					$valueInsertRetenciones .= "(id_factura_venta_insert,$arrayResult[id]),";
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
			$this->items = $data['items'];
			foreach ($data['items'] as $key => $arrayItems) {
				if (gettype($arrayItems)=='object') {
					$arrayItems=get_object_vars($arrayItems);
				}
				if ($arrayItems['codigo']=='') { $arrayError[] = "El codigo del item es obligatorio, Item $arrayItems[codigo]"; }
				else if (!array_key_exists("$arrayItems[codigo]",$arrayItemsBodega)) { $arrayError[] = "El item con codigo $arrayItems[codigo] no existe en el sistema"; }
				if ($arrayItems['cantidad']==='') { $arrayError[] = "La cantidad del item es obligatorio,  Item $arrayItems[codigo]"; }
				else if(!is_numeric($arrayItems['cantidad'])) { $arrayError[] = "la cantidad del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				if ($arrayItemsBodega[$arrayItems['codigo']]['inventariable']=='true' && $arrayItems['cantidad']>$arrayItemsBodega[$arrayItems['codigo']]['cantidad'] && $this->usuarioPermisos[181]<>true){
					$arrayError[] = "la cantidad del item excede la existencia en inventario,  Item $arrayItems[codigo]";
				}
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
									'id_factura_venta_insert',
									'".$arrayItemsBodega[$arrayItems['codigo']]['id_item']."',
									'$arrayItems[cantidad]',
									'$arrayItems[cantidad]',
									'$arrayItems[precio]',
									'$arrayItems[observaciones]',
									'$arrayItems[tipo_descuento]',
									'$arrayItems[descuento]',
									'".$arrayCcos[$arrayItems['ccos']]['id']."',
									'$arrayItems[ccos]',
									'".$arrayCcos[$arrayItems['ccos']]['nombre']."'
									),";

			}


			if ($data['total_factura']==='' /*|| !is_numeric($data['total_factura'])*/ ) { $arrayError[] = "El campo total factura debe tener un valor y debe ser numerico"; }
			if ($data['saldo_restante_factura']==='' /*|| !is_numeric($data['saldo_restante_factura']*1)*/ ) { $arrayError[] = "El campo saldo restante de la factura debe tener un valor y debe ser numerico"; }
			// if ($data['total_factura']<>$data['saldo_restante_factura'] && ($data['valor_abono']=='' /*|| !is_numeric($data['valor_abono']*1)*/ ) ) { $arrayError[] = "El campo valor abono debe tener un valor y debe ser numerico pues el saldo de la factura es diferente al total"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }
			// return array('status'=>false,'detalle'=>'pass');
			$json = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']);

			if(empty($data['email_fe'])){
				$data['email_fe'] = "";
			}

			if(empty($data['info_reserva'])){
			$data['info_reserva'] = "";
			}

			$random = $this->random();
			$sql   = "INSERT INTO ventas_facturas
							(
								random,
								fecha_inicio,
								fecha_vencimiento,
								id_configuracion_resolucion,
								prefijo,
								numero_factura,
								numero_factura_completo,
								id_vendedor,
								documento_vendedor,
								nombre_vendedor,
								id_usuario,
								documento_usuario,
								usuario,
								id_cliente,
								id_sucursal_cliente,
								sucursal_cliente,
								estado,
								id_configuracion_cuenta_pago,
								configuracion_cuenta_pago,
								id_cuenta_pago,
								cuenta_pago,
								cuenta_pago_niif,
								id_metodo_pago,
								metodo_pago,
								id_forma_pago,
								dias_pago,
								forma_pago,
								id_empresa,
								id_sucursal,
								sucursal,
								id_bodega,
								bodega,
								observacion,
								orden_compra,
								total_factura,
								total_factura_sin_abono,
								valor_anticipo,
								email_fe,
                info_reserva,
								json_api
							)
                        VALUES
                        	(
                        		'$random',
								'$data[fecha_documento]',
								'$data[fecha_vencimiento]',
								'$id_configuracion_resolucion',
								'$prefijo',
								'$numero_factura',
								'$numero_factura_completo',
								'".$arrayVendedor[$data['documento_vendedor']]['id']."',
								'".$data['documento_vendedor']."',
								'".$arrayVendedor[$data['documento_vendedor']]['nombre']."',
								'$this->id_usuario',
								'$this->documento_usuario',
								'$this->nombre_usuario',
								'$this->id_cliente',
								'$arraySucursalCliente[id]',
								'$arraySucursalCliente[nombre]',
								'1',
								'".$arrayCuentaPago[$data['cuenta_pago']]['id']."',
								'".$arrayCuentaPago[$data['cuenta_pago']]['nombre']."',
								'".$arrayCuentaPago[$data['cuenta_pago']]['id_cuenta']."',
								'".$arrayCuentaPago[$data['cuenta_pago']]['cuenta']."',
								'".$arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif']."',
								'".$arrayMetodosPago[$data['cod_metodo_pago']]['id']."',
								'".$arrayMetodosPago[$data['cod_metodo_pago']]['nombre']."',
								'".$arrayFormasPago[$data['id_forma_pago']]['id']."',
								'".$arrayFormasPago[$data['id_forma_pago']]['plazo']."',
								'".$arrayFormasPago[$data['id_forma_pago']]['nombre']."',
								'$this->id_empresa',
								'$data[id_sucursal]',
								'".$arrayUbicaciones['sucursales'][$data['id_sucursal']]."',
								'$data[id_bodega]',
								'".$arrayUbicaciones['bodegas'][$data['id_bodega']]."',
								'$data[observacion]',
								'$data[orden_compra]',
								'$data[total_factura]',
								'$data[saldo_restante_factura]',
								'$data[valor_abono]',
                '$data[email_fe]',
								'$data[info_reserva]',
								'$json'
                        	)";
        	// return array('status'=>200);
        	$query = $this->mysql->query($sql,$this->mysql->link);
        	if ($query){
        		$sql="SELECT id FROM ventas_facturas WHERE activo=1 AND id_empresa=$this->id_empresa AND random='$random' ";
        		$query=$this->mysql->query($sql,$this->mysql->link);
        		$id_factura = $this->mysql->result($query,0,'id');
        		// echo $id_factura;

				$valueInsertItems = substr($valueInsertItems, 0, -1);
				$valueInsertItems = str_replace("id_factura_venta_insert", $id_factura, $valueInsertItems);

        		$sql="INSERT INTO ventas_facturas_inventario
        				(
			        		id_factura_venta,
							id_inventario,
							cantidad,
							saldo_cantidad,
							costo_unitario,
							observaciones,
							tipo_descuento,
							descuento,
							id_centro_costos,
							codigo_centro_costos,
							centro_costos
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
        			// INSERTAR ANTICIPOS DE LA FV
        			if($valueInsertAnticipos<>''){
						$valueInsertAnticipos = str_replace("id_factura_venta_insert", $id_factura, $valueInsertAnticipos);
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
					$valueInsertRetenciones = str_replace("id_factura_venta_insert", $id_factura, $valueInsertRetenciones);
    				$sql="INSERT INTO ventas_facturas_retenciones (id_factura_venta,id_retencion) VALUES $valueInsertRetenciones ";
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
	        			$validacionInventario = $this->validateInventario($id_factura);
	        			if ($validacionInventario['status']==false) { return array('status'=>false,'detalle'=>$validacionInventario['detalle']); }

	        			$arrayAnticipo =$this->getAnticipos($id_factura);
	        			// $this->rollBack($id_factura,1);
		        		// return array('status'=>false,'detalle'=>$arrayAnticipo);

	        			$contabilizacionLocal = $this->setAsientos($id_factura,$numero_factura_completo,array('cuentaColgaap' => $data['cuenta_pago'], 'cuentaNiif' => $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'], 'estado' => $arrayCuentaPago[$data['cuenta_pago']]['estado']),$arrayAnticipo);
	        			if ($contabilizacionLocal['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionLocal['detalle']); }

	        			$contabilizacionNiif = $this->setAsientosNiif($id_factura,$numero_factura_completo,array('cuentaColgaap' => $data['cuenta_pago'], 'cuentaNiif' => $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'], 'estado' => $arrayCuentaPago[$data['cuenta_pago']]['estado']),$arrayAnticipo);
	        			if ($contabilizacionNiif['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionNiif['detalle']); }

						$updateInventario = $this->updateInventario($id_factura,'salida',"Generar");
	        			if ($updateInventario['status']==false) { return array('status'=>false,'detalle'=>$updateInventario['detalle']); }

	        			$sql="UPDATE ventas_facturas_configuracion SET consecutivo_factura=consecutivo_factura+1
								WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_configuracion_resolucion";
	        			$query=$this->mysql->query($sql,$this->mysql->link);
	        			if (!$query) {
	        				$this->rollBack($id_factura,2);
	        				$arrayError[0]='Se produjo un error al insertar el documento en la base de datos (Resolucion Error)';
							$arrayError[1]="Error numero: ".$this->mysql->errno();
			    			$arrayError[2]="Error detalle: ".$this->mysql->error();
			    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
			        		return array('status'=>false,'detalle'=>$arrayError);
	        			}
	        		}
					$resultadoEnvio = "Envio manual";
					if($this->isAutomaticallySent()){
						$resultadoEnvio = $this->sendInvoice($id_factura);
					}
					
					// Factura almacenada con exito
					return array(
									'status'                  => 200,
									'id'                	  => $id_factura,
									'prefijo'                 => $prefijo,
									'numero_factura'          => $numero_factura,
									'numero_factura_completo' => $numero_factura_completo,
									'num_resolucion'          => $arrayResoluciones['resolucion'][$id_configuracion_resolucion]['consecutivo_resolucion'],
									'fecha_resolucion'        => $arrayResoluciones['resolucion'][$id_configuracion_resolucion]['fecha_resolucion'],
									'num_inicial'             => $arrayResoluciones['resolucion'][$id_configuracion_resolucion]['numero_inicial_resolucion'],
									'num_final'               => $arrayResoluciones['resolucion'][$id_configuracion_resolucion]['numero_final_resolucion'],
									'envioDIAN'				  => $resultadoEnvio,
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
		public function isAutomaticallySent(){
			$sqlAutomaticSending = "SELECT data 
									FROM
										configuracion_general
									WHERE 
									id_empresa =$this->id_empresa
									AND descripcion = 'envio automatico de FV electronica'";
			
			$queryAutomaticSending = $this->mysql->query($sqlAutomaticSending,$this->mysql->link);
			$dataSting  = $this->mysql->result($queryAutomaticSending,0,'data');
			$data  =  json_decode($dataSting,true);
			$isActive = $data[0]['is_active'];

			return ($isActive === "true");
		}

		public function sendInvoice($idFactura){
			include("../../../LOGICALERP/ventas/facturacion/bd/ClassFacturaJSON_V2.php");

			$facturaJSON = new ClassFacturaJSON_V2($this->mysql); //Objeto classFacturaJson
			$facturaJSON->obtenerDatos($idFactura,$this->id_empresa); //Obtener todos los datos de la factura
		  	$facturaJSON->construirJSON(); //Armar JSON de envio
			$result = $facturaJSON->enviarJSON(); //Enviar a la DIAN
			
			$result['validar'] = "Procesado Correctamente"; //Quitar backslashes de la respuesta de FACSE
			
			/*
			Para comprobar si la factura fue enviada
			Filtramos $invoiceValidationStrings que contiene
			los posibles valores de una factura envida con éxito
			comparando sus valores con el obtenido en este envío $result['validar']
			*/
			$found = array_filter($this->invoiceValidationStrings,  //Array a filtrar
				function($str) use ($result) {	//Callback -> usamos use para permitir que la función anónima acceda a la variable $result fuera de su scope.
					return strpos($result['validar'], $str) !== false;
				}
			);
			
			/*
			$found estará vacío si ninún elemento de 
			$invoiceValidationStrings coincide con $result['validar']
			Por lo tanto la factura no fue enviada
			*/
			if (empty($found)) {
				$response_FE = $result['validar'];
				$detalleEnvio = "Factura no enviada";
			}
			else{//Si $found no está vacío, la factura fue enviada con éxito
				$response_FE = "Ejemplar recibido exitosamente pasara a verificacion";
				$result['validar'] = str_replace("'", "-", $result['validar']);

				//Si no tenemos el UUID ($result["id_factura"]) hay un error en el PDF pero la factura fue enviada
				$detalleEnvio = ($result["id_factura"] == "" || $result["id_factura"] == null)? 
				"No fue posible generar el pdf":
				"Factura Enviada";
			}
			date_default_timezone_set("America/Bogota");
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//Se guardan los datos de la respuesta del web web_service
			$sqlEnviarFacturaDIAN =  "UPDATE
									  	ventas_facturas
									  SET
									  	fecha_FE = '$fecha_actual',
									  	hora_FE = '$hora_actual',
									  	response_FE = '$response_FE',
									  	UUID = '$result[id_factura]',
									  	cufe = '$result[cufe]',
									  	id_usuario_FE = '".$this->id_usuario."',
									  	nombre_usuario_FE = '".$this->nombre_usuario."',
									  	cedula_usuario_FE = '".$this->documento_usuario."'
									  WHERE
									  	id = $idFactura";

			$queryEnviarFacturaDIAN = $this->mysql->query($sqlEnviarFacturaDIAN,$link);

			return $detalleEnvio;
		}
		/**
		 * @api {put} /facturas/ Modificar factura
		 * @apiVersion 1.0.0
		 * @apiDescription Modificar factura en el sistema
		 * @apiName post_facturas
		 * @apiPermission Ventas
		 * @apiGroup Facturas
		 *
		 * @apiParam (Metodos de pago Dian) {Int} 25 Efectivo
		 * @apiParam (Metodos de pago Dian) {Int} 26 Cheque
		 * @apiParam (Metodos de pago Dian) {Int} 27 Transferencia Bancaria
		 * @apiParam (Metodos de pago Dian) {Int} 28 Consigancion Bancaria
		 *
		 * @apiParam {Date} fecha_documento Fecha de la factura formato (Y-M-D)
		 * @apiParam {Date} fecha_vencimiento Fecha de la factura formato (Y-M-D)
		 * @apiParam {String} consecutivo Consecutivo completo de la factura incluye prefijo si tiene
		 * @apiParam {String} documento_cliente Numero del documento del cliente
		 * @apiParam {String} documento_cliente_nuevo Numero del documento del cliente a actualizar, si no se se modifica, enviar el valor anterior
		 * @apiParam {Int} [id_sucursal_cliente] Id de la sucursal del cliente (Consultar en el modulo de terceros)
		 * @apiParam {String} [numero_resolucion] Numero de la resolucion de facturacion, si no se envia se toma por defecto el configurado en el sistema
		 * @apiParam {String} [documento_vendedor] Numero del documento del vendedor de la factura
		 * @apiParam {Int} cuenta_pago Cuenta contable de pago de la factura
		 * @apiParam {Int{2}="25","26","27","28"} cod_metodo_pago Codigo DIAN para los metodos de pago (Ver tabla <b>Metodos de pago Dian</b>)
		 * @apiParam {Int} [id_forma_pago] Id de la forma de pago de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_sucursal Id de la sucursal de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {Int} id_bodega Id de la sucursal de la factura (Consultar en el panel de control del sistema)
		 * @apiParam {String} [orden_compra] Texto con el codigo de la orden de compra del cliente
		 * @apiParam {String} [observacion] Observacion general de la factura
		 * @apiParam {Double} total_factura Valor completo de la factura
		 * @apiParam {Double} saldo_restante_factura Valor restante de la factura, corresponde al total o si se han hecho pagos al saldo restante
		 * @apiParam {Double} valor_abono Valor de abono o anticipo u otro concepto aplicado al total de la factura, es obligatorio si el saldo de la factura y el total de la factura son diferentes
     	 * @apiParam {String} email_fe Correos electronicos hacia los cuales se van a enviar las facturas electronicas
		 * @apiParam {String} info_reserva Contiene un json con los valores de las reserva en Logical Hotels
		 * @apiParam {Object[]} [retenciones] Lista con las retenciones de la factura
		 * @apiParam {Int} retenciones.id Id de la retencion (Consultar en el panel de control del sistema)
		 * @apiParam {Object[]} items Listado de los articulos a facturar
		 * @apiParam {String} items.codigo Contiene el codigo del item a facturar
		 * @apiParam {Double} items.cantidad Contiene la cantidad item a facturar
		 * @apiParam {Double} items.precio Contiene el precio de venta del item a facturar
		 * @apiParam {Double} [items.observaciones] Contiene la observacion del item a facturar
		 * @apiParam {String="porcentaje","pesos"} [items.tipo_descuento] Contiene el tipo de descuento a aplicar al item puede ser porcentaje o pesos
		 * @apiParam {Double} [items.descuento] Contiene el valor del descuento a aplicar al item, y se aplica segun el tipo de descuento
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
			global $json;
			$this->actionUpdate = true;
			if (count($data)<=0){ return array('status'=>false,'detalle'=>'No se recibio informacion'); }
			if ($this->usuarioPermisos[22]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para modificar facturas'); }
			$data = json_decode( json_encode($data), true);

			$arrayError   = array();
			$arrayTercero = array();
			if (gettype($data)=='object') {
				$data=get_object_vars($data);
			}

			if ($data['id_sucursal']=='' || !isset($data['id_sucursal'])){ $arrayError[] = "El campo id_sucursal es obligatorio"; }
			if ($data['id_bodega']=='' || !isset($data['id_bodega'])){ $arrayError[] = "El campo id_bodega es obligatorio"; }
			if ($data['consecutivo']=='' || !isset($data['consecutivo'])){ $arrayError[] = "El campo consecutivo es obligatorio"; }
			if ($data['documento_cliente']=='' || !isset($data['documento_cliente'])){ $arrayError[] = "El campo documento_cliente es obligatorio"; }
			/* set the gain account */
			$this->cuenta_ingreso = $data['cuenta_ingreso'];

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			$sql="SELECT
					id,
					estado,
					fecha_inicio
				FROM ventas_facturas
				WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_sucursal=$data[id_sucursal]
					AND id_bodega=$data[id_bodega]
					AND nit='$data[documento_cliente]'
					AND numero_factura_completo='$data[consecutivo]' ";
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

			$this->fecha_documento = $this->mysql->result($query,0,'fecha_inicio');
			$this->consecuivo_factura = $data['consecutivo'];
			$this->id_bodega   = $data['id_bodega'];
			$this->id_sucursal = $data['id_sucursal'];

			if ($id_factura=='' || $id_factura==0) { $arrayError[] = "La factura no existe en el sistema"; }
			if ($estado==2) { $arrayError[] = "La factura se encuentra bloqueada"; }
			if ($estado==3) { $arrayError[] = "La factura se encuentra anulada"; }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if ($estado==1){
				$this->rollBack($id_factura,2 );
			}

			$updateRetenciones = $this->updateRetenciones($id_factura);
			if ($updateRetenciones['status']==false) { return array('status'=>false,'detalle'=>$updateRetenciones['detalle']); }
			$updateItemsFactura = $this->updateItemsFactura($id_factura);
			if ($updateItemsFactura['status']==false) { return array('status'=>false,'detalle'=>$updateItemsFactura['detalle']); }

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			if ($data['fecha_documento']=='' || !isset($data['fecha_documento']) ){ $arrayError[] = "El campo fecha_documento es obligatorio"; }
			if ($data['fecha_vencimiento']=='' || !isset($data['fecha_vencimiento']) ){ $arrayError[] = "El campo fecha_vencimiento es obligatorio"; }
			if ($data['documento_cliente_nuevo']=='' || !isset($data['documento_cliente_nuevo'])){ $arrayError[] = "El campo documento cliente nuevo es obligatorio"; }
			$this->id_cliente = $this->getcliente($data['documento_cliente_nuevo']);
			if ($this->id_cliente==false) {  $arrayError[] = "El cliente no existe en el sistema"; }
			$arraySucursalCliente =  $this->getSucursalCliente($this->id_cliente,$data['id_sucursal_cliente']);
			if ($data['id_sucursal_cliente']<>'') {
				if (!array_key_exists("$data[id_sucursal_cliente]",$arraySucursalCliente)) {
					$arrayError[] = "La sucursal del cliente no existe en el sistema";
				}
			}
			$arrayResoluciones = $this->getResoluciones();
			if ($data['numero_resolucion']<>'' && !array_key_exists("$data[numero_resolucion]",$arrayResoluciones['resoluciones']) ) { $arrayError[] = "La resolucion Numero $data[numero_resolucion] no existe en el sistema"; }
			//else if(!array_key_exists("$data[id_sucursal]",$arrayResoluciones['configuracion'][$data['numero_resolucion']]) ){ $arrayError[] = "La resolucion Numero $data[numero_resolucion] no esta configurada para esa sucursal"; }
			else if(empty($arrayResoluciones['resoluciones'])){ $arrayError[] = "No existe ninguna resolucion configurada en el sistema"; }

			if ($data['documento_vendedor']<>'') {
				$arrayVendedor = $this->getEmpleado($data['documento_vendedor']);
				if (!array_key_exists("$data[documento_vendedor]",$arrayVendedor)) { $arrayError[] = "El vendedor no existe en el sistema"; }
			}

			if ($data['cuenta_pago']=='' || !isset($data['cuenta_pago'])){ $arrayError[] = "El campo cuenta pago es obligatorio"; }
			$arrayCuentaPago = $this->getCuentaPago($data['cuenta_pago']);
			if (!array_key_exists("$data[cuenta_pago]",$arrayCuentaPago)) {
				$arrayError[] = "La cuenta de pago no existe en el sistema";
			}

			$arrayMetodosPago = $this->getMetodosPago();
			if ($data['cod_metodo_pago']=='' || $data['cod_metodo_pago']==0) { $arrayError[] = "El campo cod_metodo_pago es obligatorio"; }
			if (!array_key_exists("$data[cod_metodo_pago]",$arrayMetodosPago) ) {  $arrayError[] = "El metodo de pago no existe en el sistema"; }

			$arrayFormasPago = $this->getFormasPago();
			if ($data['id_forma_pago']<>'') {
				if (!array_key_exists("$data[id_forma_pago]",$arrayFormasPago) ) {  $arrayError[] = "La forma de pago no existe en el sistema"; }
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
				// else if(!is_numeric($arrayItems['cantidad'])) { $arrayError[] = "la cantidad del item debe ser un valor entero o decimal,  Item $arrayItems[codigo]"; }
				if ($arrayItemsBodega[$arrayItems['codigo']]['inventariable']=='true' && $arrayItems['cantidad']>$arrayItemsBodega[$arrayItems['codigo']]['cantidad'] && $this->usuarioPermisos[181]<>true){
					$arrayError[] = "la cantidad del item excede la existencia en inventario,  Item $arrayItems[codigo]";
				}
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
									'$arrayItems[cantidad]',
									'$arrayItems[precio]',
									'$arrayItems[observaciones]',
									'$arrayItems[tipo_descuento]',
									'$arrayItems[descuento]'
									),";

			}

			if ($data['total_factura']==='' /*|| !is_numeric($data['total_factura'])*/ ) { $arrayError[] = "El campo total factura debe tener un valor y debe ser numerico"; }
			if ($data['saldo_restante_factura']==='' /*|| !is_numeric($data['saldo_restante_factura'])*/ ) { $arrayError[] = "El campo saldo restante de la factura debe tener un valor y debe ser numerico"; }
			// if ($data['total_factura']<>$data['saldo_restante_factura'] && ($data['valor_abono']==='' /*|| $data['valor_abono']*/ ) ) { $arrayError[] = "El campo valor abono debe tener un valor y debe ser numerico pues el saldo de la factura es diferente al total"; }

			if($data['email_fe'] == ''){
				$data['email_fe'] = "";
			}

			if($data['info_reserva'] == ''){
				$data['info_reserva'] = "";
			}

			if (!empty($arrayError)) { return array('status'=>false,'detalle'=>$arrayError); }

			$json                = addslashes($json);
			$data['observacion'] = addslashes($data['observacion']);

			$sql="UPDATE ventas_facturas
						SET
							fecha_inicio                 = '$data[fecha_documento]',
							fecha_vencimiento            = '$data[fecha_vencimiento]',
							id_vendedor                  = '".$arrayVendedor[$data['documento_vendedor']]['id']."',
							documento_vendedor           = '".$data['documento_vendedor']."',
							nombre_vendedor              = '".$arrayVendedor[$data['documento_vendedor']]['nombre']."',
							id_usuario                   = '$this->id_usuario',
							documento_usuario            = '$this->documento_usuario',
							usuario                      = '$this->nombre_usuario',
							id_cliente                   = '$this->id_cliente',
							id_sucursal_cliente          = '$arraySucursalCliente[id]',
							sucursal_cliente             = '$arraySucursalCliente[nombre]',
							estado                       = '1',
							id_configuracion_cuenta_pago = '".$arrayCuentaPago[$data['cuenta_pago']]['id']."',
							configuracion_cuenta_pago    = '".$arrayCuentaPago[$data['cuenta_pago']]['nombre']."',
							id_cuenta_pago               = '".$arrayCuentaPago[$data['cuenta_pago']]['id_cuenta']."',
							cuenta_pago                  = '".$arrayCuentaPago[$data['cuenta_pago']]['cuenta']."',
							cuenta_pago_niif             = '".$arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif']."',
							id_metodo_pago               = '".$arrayMetodosPago[$data['cod_metodo_pago']]['id']."',
							metodo_pago                  = '".$arrayMetodosPago[$data['cod_metodo_pago']]['nombre']."',
							id_forma_pago                = '".$arrayFormasPago[$data['id_forma_pago']]['id']."',
							dias_pago                    = '".$arrayFormasPago[$data['id_forma_pago']]['plazo']."',
							forma_pago                   = '".$arrayFormasPago[$data['id_forma_pago']]['nombre']."',
							id_empresa                   = '$this->id_empresa',
							id_sucursal                  = '$data[id_sucursal]',
							sucursal                     = '".$arrayUbicaciones['sucursales'][$data['id_sucursal']]."',
							id_bodega                    = '$data[id_bodega]',
							bodega                       = '".$arrayUbicaciones['bodegas'][$data['id_bodega']]."',
							observacion                  = '$data[observacion]',
							orden_compra                 = '$data[orden_compra]',
							total_factura                = '$data[total_factura]',
							total_factura_sin_abono      = '$data[saldo_restante_factura]',
							valor_anticipo               = '$data[valor_abono]',
              email_fe                     = '$data[email_fe]',
							info_reserva                 = '$data[info_reserva]',
							json_api                     = '$json'
					WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_factura";
        	$query = $this->mysql->query($sql,$this->mysql->link);
        	if ($query){
    			$valueInsertItems = substr($valueInsertItems, 0, -1);
        		$sql="INSERT INTO ventas_facturas_inventario
        				(
			        		id_factura_venta,
							id_inventario,
							cantidad,
							saldo_cantidad,
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
        			// INSERTAR ANTICIPOS DE LA FV
        			if($valueInsertAnticipos<>''){
						$valueInsertAnticipos = str_replace("id_factura_venta_insert", $id_factura, $valueInsertAnticipos);
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
    				$sql="INSERT INTO ventas_facturas_retenciones (id_factura_venta,id_retencion) VALUES $valueInsertRetenciones ";
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
	        			$validacionInventario = $this->validateInventario($id_factura);
	        			if ($validacionInventario['status']==false) { return array('status'=>false,'detalle'=>$validacionInventario['detalle']); }

	        			$arrayAnticipo =$this->getAnticipos($id_factura);

	        			$contabilizacionLocal = $this->setAsientos($id_factura,$data['consecutivo'],array('cuentaColgaap' => $data['cuenta_pago'], 'cuentaNiif' => $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'], 'estado' => $arrayCuentaPago[$data['cuenta_pago']]['estado']),$arrayAnticipo);
	        			if ($contabilizacionLocal['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionLocal['detalle']); }

	        			$contabilizacionNiif = $this->setAsientosNiif($id_factura,$data['consecutivo'],array('cuentaColgaap' => $data['cuenta_pago'], 'cuentaNiif' => $arrayCuentaPago[$data['cuenta_pago']]['cuenta_niif'], 'estado' => $arrayCuentaPago[$data['cuenta_pago']]['estado']),$arrayAnticipo);
	        			if ($contabilizacionNiif['status']==false) { return array('status'=>false,'detalle'=>$contabilizacionNiif['detalle']); }

	        			$updateInventario = $this->updateInventario($id_factura,'salida',"Generar");
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
		 * @api {delete} /facturas/ Anular factura
		 * @apiVersion 1.0.0
		 * @apiDescription Anular factura en el sistema.
		 * @apiName delete_factura
		 * @apiPermission Ventas
		 * @apiGroup Facturas
		 *
		 * @apiParam {String} id_sucursal Id de la sucursal del documento (Consultar en el panel de control del sistema)
		 * @apiParam {String} id_bodega Id de la bodega del documento (Consultar en el panel de control del sistema)
		 * @apiParam {String} consecutivo Consecutivo completo de la factura incluye prefijo si tiene Ejemplo: "FV 1010"
		 * @apiParam {String} documento_cliente Documento del cliente de la factura
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
			if ($this->usuarioPermisos[23]<>true){ return array('status'=>false,'detalle'=>'No tiene permisos para anular facturas'); }
			$data = json_decode( json_encode($data), true);
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
				FROM ventas_facturas
				WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND id_sucursal=$data[id_sucursal]
					AND id_bodega=$data[id_bodega]
					AND nit='$data[documento_cliente]'
					AND numero_factura_completo='$data[consecutivo]' ";
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
		 * setNumeroResolucion Actualizar el consecutivo de la resolucion
		 * @param Int $id_resolucion Id de la resolucion a actualizar
		 */
		public function setNumeroResolucion($id_resolucion){
			$sql="UPDATE ventas_facturas_configuracion SET consecutivo_factura=consecutivo_factura+1 WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_resolucion ";
			$query=$this->mysql->query($sql,$this->mysql->link);
		}

		/**
		 * getTerceros Consultar el cliente del sistema
		 * @param  String $documento Documento del cliente a consultar
		 * @return int Id del cliente a consultar
		 */
		public function getcliente($documento){
			$sql="SELECT id,numero_identificacion,nombre FROM terceros WHERE activo=1 AND id_empresa=$this->id_empresa AND numero_identificacion='$documento'";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$id_temp = $this->mysql->result($query,0,'id');
			return ($id_temp>1)? $id_temp : false;
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
			$arrayTemp[$documento] =  array('id'		=>	$this->mysql->result($query, 'id'), 
											'nombre'	=>	$this->mysql->result($query, 'nombre')
										);
			return $arrayTemp;
		}

		/**
		 * getCuentaPago Consultar la cuenta de pago de la factura
		 * @param  string $cuenta_pago cuenta de pago de la factura
		 * @return array              Array con las cuentas de pago
		 */
		public function getCuentaPago($cuenta_pago){
			$sql="SELECT id,nombre,id_cuenta,cuenta,cuenta_niif,estado FROM configuracion_cuentas_pago WHERE activo=1 AND tipo='Venta' AND id_empresa=$this->id_empresa AND cuenta='$cuenta_pago' ";
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
		 * getMetodosPago Consultar los metodos de pago para envio a la dian
		 * @return Array Array con los metodos de pago exigidos por la DIAN
		 */
		public function getMetodosPago(){
			$sql="SELECT id,nombre,codigo_metodo_pago_dian FROM configuracion_metodos_pago WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($row=$this->mysql->fetch_assoc($query)){$data[$row['codigo_metodo_pago_dian']]=$row;}
			return $data;
		}

		/**
		 * getFormasPago Consultar las formas de pago
		 * @return Array Array con las formas de pago
		 */
		public function getFormasPago(){
			$sql="SELECT id,nombre,plazo FROM configuracion_formas_pago WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($row=$this->mysql->fetch_assoc($query)){$data[$row['id']]=$row;}
			return $data;
		}

		/**
		 * getResoluciones Consultar las resoluciones y su configuracion
		 * @return Array Array con las resoluciones  y su configuracion
		 */
		public function getResoluciones(){
			$sql="SELECT
					id,
					dias_vencimiento,
					prefijo,
					consecutivo_resolucion,
					fecha_resolucion,
					numero_inicial_resolucion,
					numero_final_resolucion,
					tipo,
					consecutivo_factura,
					digitos
					FROM ventas_facturas_configuracion WHERE activo=1 AND id_empresa=$this->id_empresa AND consecutivo_factura<=numero_final_resolucion";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$whereIdResolucion = '';
			while($row=$this->mysql->fetch_assoc($query)){
				$data['resoluciones'][$row['consecutivo_resolucion']]=$row;
				$data['resolucion'][$row['id']]=$row;
				$whereIdResolucion .= ($whereIdResolucion=='')? "id_resolucion= $row[id]" : " OR id_resolucion= $row[id]" ;
			}

			$whereIdResolucion = ($whereIdResolucion<>'')? " AND ($whereIdResolucion) " : "" ;
			$sql="SELECT
						id_resolucion,
						numero_resolucion,
						id_sucursal,
						sucursal,
						predeterminada
			 		FROM ventas_facturas_configuracion_sucursales WHERE activo=1 AND id_empresa=$this->id_empresa $whereIdResolucion";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($row=$this->mysql->fetch_assoc($query)){$data['configuracion'][$row['id_sucursal']][$row['numero_resolucion']]=$row;}
			return $data;
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
									'id_factura_venta_insert',
									'FV',
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
									AND tipo_documento='FV'
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
 				FROM retenciones WHERE activo=1 AND id_empresa=$this->id_empresa AND modulo='Venta' ";
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
		 * validateInventario Validar cantidades en inventario
		 * @param  Int $id_factura       Id de la factura a validar
		 * @return Array Si se produce un error se retorna un array con el error
		 */
		public function validateInventario($id_factura){
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
							ventas_facturas_inventario AS TI,
							inventario_totales AS TIT
						WHERE TI.activo = 1
							AND TI.id_factura_venta = '$id_factura'
							AND TI.nombre_consecutivo_referencia<>'Remision'
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
				$this->rollBack($id_factura,1);
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
		public function setAsientos($id_factura,$consecutivo,$arrayCuentaPago,$arrayAnticipo){
			global $saldoGlobalfactura, $saldoGlobalFacturaSinAbono;
			$decimalesMoneda  = ($this->decimales_moneda >= 0)? $this->decimales_moneda : 0;
			$cuentaPago       = $arrayCuentaPago['cuentaColgaap'];
			$estadoCuentaPago = $arrayCuentaPago['estado'];

			//===================== QUERY CUENTAS ====================//
	    	/**********************************************************/
			$ivaAcumulado      = 0;
			$costoAcumulado    = 0;
			$precioAcumulado   = 0;
			$impuestoAcumulado = 0;

			$arrayRemisiones   = '';
			$contRemisiones    = 0;
			$acumIdRemisiones  = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

			$whereIdItemsCuentas = '';

			$sql="SELECT fecha_inicio FROM ventas_facturas WHERE activo=1 AND id=$id_factura";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$fechaFactura = $this->mysql->result($query,0,'fecha_inicio');

			$sqlDoc = "SELECT VF.id,
							VF.id_consecutivo_referencia AS id_referencia,
							VF.nombre_consecutivo_referencia AS nombre_referencia,
							VF.id_inventario AS id_item,
							VF.codigo,
							VF.cantidad,
							VF.costo_unitario AS precio,
							VF.costo_inventario AS costo,
							VF.descuento,
							VF.tipo_descuento,
							VF.id_impuesto,
							VF.valor_impuesto,
							VF.inventariable,
							VF.id_centro_costos,
							VF.codigo_centro_costos,
							VF.centro_costos,
							I.cuenta_venta AS cuenta_iva,
							I.codigo_impuesto_dian as tipo_impuesto
						FROM ventas_facturas_inventario AS VF LEFT JOIN impuestos AS I ON(
								I.activo=1
								AND I.id=VF.id_impuesto
							)
						WHERE VF.id_factura_venta='$id_factura' AND VF.activo=1";
			$queryDoc = $this->mysql->query($sqlDoc,$this->mysql->link);

			while($rowDoc = $this->mysql->fetch_array($queryDoc)){
				$typeDocumento = 'FV';
				$impuesto      = 0;

				//CALCULO DEL PRECIO
				$precio = $rowDoc['precio'] * $rowDoc['cantidad'];
				$costo  = ABS($rowDoc['costo'] * $rowDoc['cantidad']);
				if($rowDoc['descuento'] > 0){
					$precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio-ROUND(($rowDoc['descuento']*$precio)/100, $decimalesMoneda) : $precio-$rowDoc['descuento'];
				}

				if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0 && $exento_iva!='Si'){  $impuesto = ROUND($precio*$rowDoc['valor_impuesto']/100, $decimalesMoneda); }

				$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR ': ' ';
				$whereIdItemsCuentas .='id_items = '.$rowDoc['id_item'];

				$arrayInventarioFactura[$rowDoc['id']] = array('id_factura_inventario' =>$rowDoc['id'],
																'precio'                               => $precio,
																'codigo'                               => $rowDoc['codigo'],
																'id_referencia'                        => $rowDoc['id_referencia'],
																'nombre_referencia'                    => $rowDoc['nombre_referencia'],
																'tipo_impuesto'                        => $rowDoc['tipo_impuesto'],
																'impuesto'                             => $impuesto,
																'costo'                                => $costo,
																'id_items'                             => $rowDoc['id_item'],
																'inventariable'                        => $rowDoc['inventariable'],
																'cuenta_iva'                           => $rowDoc['cuenta_iva'],
																'id_centro_costos'                     => $rowDoc['id_centro_costos'],
																'codigo_centro_costos'                 => $rowDoc['codigo_centro_costos'],
																'centro_costos'                        => $rowDoc['centro_costos'],

															);
			}


			$sqlItemsCuentas = "SELECT id, id_items,descripcion, puc, tipo
								FROM items_cuentas
								WHERE activo=1
									AND id_empresa='$this->id_empresa'
									AND estado='venta'
									AND ($whereIdItemsCuentas)
								GROUP BY id_items,descripcion
								ORDER BY id_items ASC";
			$queryItemsCuentas = $this->mysql->query($sqlItemsCuentas,$this->mysql->link);

			while ($rowCuentasItems = $this->mysql->fetch_array($queryItemsCuentas)) {

				if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPago; $rowCuentasItems['tipo'] = 'debito'; }
				if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['tipo'] = 'credito'; }

				$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']]= array('estado' => $rowCuentasItems['tipo'], 'cuenta'=> $rowCuentasItems['puc']);

				$valueInsertContabilizacion .= "('".$rowCuentasItems['id_items']."',
												'".$rowCuentasItems['puc']."',
												'".$rowCuentasItems['tipo']."',
												'".$rowCuentasItems['descripcion']."',
												'$id_factura',
												'FV',
												'$this->id_empresa',
												'$this->id_sucursal',
												'$this->id_bodega'),";
			}

			$arrayGlobalEstado['debito']  = 0;
			$arrayGlobalEstado['credito'] = 0;

			$arrayItemEstado['debito']    = 0;
			$arrayItemEstado['credito']   = 0;

			$acumSubtotal = 0;
			$acumImpuesto = 0;
			$acumBaseRetenciones = 0;
			// print_r($arrayInventarioFactura);
			foreach ($arrayInventarioFactura AS $valArrayInventario) {

				$arrayItemEstado['debito']  = 0;
				$arrayItemEstado['credito'] = 0;

				// validar si el item tiene causacion normal
				$array_key = array_search($valArrayInventario['codigo'], array_column($this->items, 'codigo'));
				$causacion_normal_item = null;
				if ($array_key !== false) {
					$causacion_normal_item = $this->items[$array_key]["causacion_normal"]; 
				}

				$idItemArray    = $valArrayInventario['id_items'];
				$cuentaPrecio   = $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
				if ($causacion_normal_item != "true" && $this->cuenta_ingreso>0) {
					$cuentaPrecio   =  $this->cuenta_ingreso;
				}
				
				$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
				$cuentaImpuesto = ($valArrayInventario['cuenta_iva'] > 0)? $valArrayInventario['cuenta_iva']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

				$cuentaCosto = $arrayCuentasItems[$idItemArray]['costo']['cuenta'];
				$contraCosto = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['cuenta'];

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cuentaPrecio][$estado] += ROUND($valArrayInventario['precio'],$decimalesMoneda); }
					else{ $arrayAsiento[$cuentaPrecio][$estado] = ROUND($valArrayInventario['precio'],$decimalesMoneda); }
					$arrayAsiento[$cuentaPrecio]['idCcos'] = $valArrayInventario['id_centro_costos'];
					$CadenaCuentaPrecio = strval($cuentaPrecio);

					$arrayGlobalEstado[$estado] += ROUND($valArrayInventario['precio'],$decimalesMoneda);
					$arrayItemEstado[$estado]   += ROUND($valArrayInventario['precio'],$decimalesMoneda);
					$acumSubtotal               += ROUND($valArrayInventario['precio'],$decimalesMoneda);
					$acumBaseRetenciones        += ($CadenaCuentaPrecio[0]=="4")? ROUND($valArrayInventario['precio'],$decimalesMoneda) : 0;

					// $arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
					// $arrayItemEstado[$estado]   += $valArrayInventario['precio'];
					// $acumSubtotal               += $valArrayInventario['precio'];

					//===================================== CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
						$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
						else{ $arrayAsiento[$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }
						$arrayAsiento[$cuentaImpuesto]['idCcos'] = 0;

						$arrayGlobalEstado[$estado] += ROUND($valArrayInventario['impuesto'],$decimalesMoneda);
						$arrayItemEstado[$estado]   += ROUND($valArrayInventario['impuesto'],$decimalesMoneda);
						$acumImpuesto               += ($valArrayInventario["tipo_impuesto"]=="01")? ROUND($valArrayInventario['impuesto'],$decimalesMoneda) : 0;

						// $arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
						// $arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
						// $acumImpuesto               += $valArrayInventario['impuesto'];

					}

					//============================== CALC CONTRA PARTIDA PRECIO =================================//
					if($contraPrecio > 0){
						$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];



						$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
										:  $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
						else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }
						$arrayAsiento[$contraPrecio]['idCcos'] = 0;

						$arrayGlobalEstado[$estado] += $contraSaldo;
						$arrayItemEstado[$estado]   += $contraSaldo;

						$acumCuentaClientes   = $contraPrecio;
						$estadoCuentaClientes = $estado;
					}
				}
				else if($valArrayInventario['inventariable'] == 'false'){
					$this->rollBack($id_factura,1);
					return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado en la contabilizacion" );
					// echo '<script>
					// 		alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion");
					// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// 	</script>';
					// exit;
				}
				//======================================= CALC COSTO ===========================================//
				if( $cuentaCosto > 0 && $contraCosto > 0 && $valArrayInventario['nombre_referencia'] != 'Remision' ){

					$estado = $arrayCuentasItems[$idItemArray]['costo']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaCosto][$estado] > 0){ $arrayAsiento[$cuentaCosto][$estado] += $valArrayInventario['costo']; }
					else{ $arrayAsiento[$cuentaCosto][$estado] = $valArrayInventario['costo']; }
					$arrayAsiento[$cuentaCosto]['idCcos'] = 0;

					$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
					$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

					//ARRAY ASIENTO CONTABLE
					$estado = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['estado'];
					if($arrayAsiento[$contraCosto][$estado] > 0){ $arrayAsiento[$contraCosto][$estado] += $valArrayInventario['costo']; }
					else{ $arrayAsiento[$contraCosto][$estado] = $valArrayInventario['costo']; }
					$arrayAsiento[$contraCosto]['idCcos'] = $valArrayInventario['id_centro_costos'];;

					$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
					$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

				}
				else if($valArrayInventario['inventariable'] == 'true' && $valArrayInventario['nombre_referencia'] != 'Remision'){
					$this->rollBack($id_factura,1);
					return  array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado el manejo del costo en la contabilizacion" );
					// echo '<script>
					// 		alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado el manejo del costo en la contabilizacion");
					// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// 	</script>';
					// exit;
				}

			}


			$arrayGlobalEstado['debito']  = ROUND($arrayGlobalEstado['debito'],$decimalesMoneda);
			$arrayGlobalEstado['credito'] = ROUND($arrayGlobalEstado['credito'],$decimalesMoneda);

			// print_r($arrayAsiento);
			if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
				$this->rollBack($id_factura,1);
				return  array('status' => false, 'detalle'=> "El saldo debito es diferente al credito, favor revise la configuracion de contabilizacion" );
				// echo '<script>
				// 		alert("Aviso.\nEl saldo debito es diferente al credito, favor revise la configuracion de contabilizacion!");
				// 		console.log("'.$arrayGlobalEstado['debito'].' - '.$arrayGlobalEstado['credito'].'");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}
			else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0){
				$this->rollBack($id_factura,1);
				return  array('status' => false, 'detalle'=> "El saldo debito y credito en norma colgaap debe ser mayor a 0" );
				// echo '<script>
				// 		alert("Aviso,\nEl saldo debito y credito en norma colgaap debe ser mayor a 0!");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}

			//==================== QUERY RETENCIONES =================//
	    	/**********************************************************/
			$acumRetenciones  = 0;
			$contRetencion    = 0;
			$estadoRetencion  = $estadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
			$sqlRetenciones   = "SELECT valor,codigo_cuenta,tipo_retencion,cuenta_autoretencion,base FROM ventas_facturas_retenciones WHERE id_factura_venta='$id_factura' AND activo=1";
			$queryRetenciones = $this->mysql->query($sqlRetenciones,$this->mysql->link);
			while($rowRetenciones = $this->mysql->fetch_array($queryRetenciones)){
				$valorBase           = $rowRetenciones['base'];
				$valorRetencion      = $rowRetenciones['valor'];
				$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
				$tipoRetencion       = $rowRetenciones['tipo_retencion'];
				$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion'];

				if(is_nan($arrayAsiento[$codigoRetencion][$estadoRetencion])){ $arrayAsiento[$codigoRetencion][$estadoRetencion] = 0; }

				//CALCULO RETEIVA
				if($tipoRetencion == "ReteIva" ){
					// if ($exento_iva=='Si' || $acumImpuesto<$valorBase) { continue; }
					if ($exento_iva=='Si' || $acumImpuesto<$valorBase) { continue; }

					$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				}
				else{ 																									//CALCULO RETE, RETECREE Y RETEICA
					if ($acumBaseRetenciones<$valorBase) { continue; }

					$acumRetenciones += ROUND($acumBaseRetenciones*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumBaseRetenciones*$valorRetencion/100, $decimalesMoneda);
				}

				if($tipoRetencion == "AutoRetencion"){ 																	//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES

					if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
						$this->rollBack($id_factura,1);
						return  array('status' => false, 'detalle'=> "No se ha configurado la cuenta Colgaap Autorretencion" );
						// echo '<script>
						// 		alert("Aviso.\nNo se ha configurado la cuenta Colgaap Autorretencion.");
						// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						// 	</script>';
						// exit;
					}

					if(is_nan($arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes])){ $arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes] = 0; }
					$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
					$acumRetenciones -= ROUND($acumBaseRetenciones*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[$codigoRetencion][$estadoRetencion];
				}
			}

			$arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes] -= $acumRetenciones;
			$saldoGlobalfactura = $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes];												//VARIABLE GLOBAL TOTAL FACTURA

			//=========================// SALDO ANTICIPO //=========================//
			//**********************************************************************//
			$saldoClientes = ROUND($arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes]);
			$saldoAnticipo = $arrayAnticipo['total'];
			$diferenciaSaldos = ABS($saldoAnticipo - $saldoClientes);
			if($saldoAnticipo > 0 && $saldoAnticipo > $saldoClientes && $diferenciaSaldos > 1 ){
				$this->rollBack($id_factura,1);
				return  array('status' => false, 'detalle'=> "Los anticipos no pueden ser mayores a la factura de venta. anticipo: $saldoAnticipo saldo:$saldoClientes" );
				// echo'<script>
				// 		alert("Aviso.\nLos anticipos no pueden ser mayores a la factura de venta!");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}
			else{ $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes] -= $saldoAnticipo; }

			foreach ($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo) {
				$arrayCampo['debito']  = 0;
				$arrayCampo['credito'] = 0;

				$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
			}

			$totalDebito  = 0;
			$totalCredito = 0;
			$tablaDebug   = '<div style="float:left; width:80px;">Id</div>
							<div style="float:left; width:80px;">Documento</div>
							<div style="float:left; width:80px;">Debito</div>
							<div style="float:left; width:80px;">Credito</div>
							<div style="float:left; width:80px;">PUC</div>
							<div style="float:left; width:150px;">Id Centro Costos</div><br>';

			//=========================// CONTABILIZACION //=========================//
			//***********************************************************************//
			// print_r($arrayAsiento);
			$contAnticipos = 0;
			foreach ($arrayAsiento AS $cuenta => $arrayCampo) {

				if(is_nan($cuenta) || $cuenta==0){ continue; }
				$cuenta = $cuenta * 1;

				$arrayCampo['debito'] = round($arrayCampo['debito'],$decimalesMoneda);
				$arrayCampo['credito'] = round($arrayCampo['credito'],$decimalesMoneda);

				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

				if($arrayCampo['debito'] > 0 || $arrayCampo['credito'] > 0){

					// SALDO POR PAGAR EN FACTURACION
					if($estadoCuentaPago == 'Credito' && $arrayCampo['type']=='cuentaPago'){ $saldoGlobalFacturaSinAbono += ($arrayCampo['debito'] > $arrayCampo['credito'])? $arrayCampo['debito']: $arrayCampo['credito']; } //ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO

					// BODY INSERT
					$valueInsertAsientos .= "('$id_factura',
											'$consecutivo',
											'FV',
											'Factura de Venta',
											'$id_factura',
											'FV',
											'$consecutivo',
											'$fechaFactura',
											'".$arrayCampo['debito']."',
											'".$arrayCampo['credito']."',
											'$cuenta',
											'$this->id_cliente',
											'".$arrayCampo['idCcos']."',
											'$this->id_sucursal',
											'$this->id_empresa'),";

					$tablaDebug  .='<div style="overflow:hidden;">
										<div style="float:left; width:80px;">-'.$id_factura.'</div>
										<div style="float:left; width:80px;">FV</div>
										<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
										<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
										<div style="float:left; width:80px;">-'.$cuenta.'</div>
									</div><br>';
				}

				//=======================// ANTICIPOS //=======================//
				if($acumCuentaClientes == $cuenta){

					foreach ($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo) {
						$contAnticipos++;

						$arrayCampo['debito']  = 0;
						$arrayCampo['credito'] = 0;

						$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

						$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
						$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

						if(is_nan($datosAnticipo['cuenta_niif']) || $datosAnticipo['cuenta_niif']==0){ continue; }
						$datosAnticipo['cuenta_niif'] = $datosAnticipo['cuenta_niif'] * 1;

						$valueInsertAsientos .= "('$id_factura',
												'$consecutivo',
												'FV',
												'Factura de Venta',
												'$datosAnticipo[id_anticipo]',
												'$datosAnticipo[tipo_documento]',
												'$datosAnticipo[consecutivo]',
												'$fechaFactura',
												'$arrayCampo[debito]',
												'$arrayCampo[credito]',
												'$datosAnticipo[cuenta_niif]',
												'$datosAnticipo[id_tercero]',
												'$idCcos',
												'$this->id_sucursal',
												'$this->id_empresa'),";

						$tablaDebug .= '<div style="overflow:hidden;">
											<div style="float:left; width:80px;">-'.$idAnticipo.'</div>
											<div style="float:left; width:80px;">RC</div>
											<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
											<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
											<div style="float:left; width:80px;">-'.$datosAnticipo['cuenta_niif'].'</div>
										<div><br>';
					}
				}
			}

			$tablaDebug .= '<div style="overflow:hidden;">
								<div style="float:left; width:80px; border-top:1px solid">-</div>
								<div style="float:left; width:80px; border-top:1px solid">-</div>
								<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div>
								<div style="float:left; width:80px; border-top:1px solid">-'.$totalCredito.'</div>
							</div><br>';

			// echo $tablaDebug;
			 // exit;
			$saldoGlobalFacturaSinAbono = ROUND($saldoGlobalFacturaSinAbono,$decimalesMoneda);
			$totalDebito  = ROUND($totalDebito,$decimalesMoneda);
			$totalCredito = ROUND($totalCredito,$decimalesMoneda);
			if($totalDebito != $totalCredito && ABS($totalDebito-$totalCredito)>10){
				$this->rollBack($id_factura,1);
				return array('status' => false, 'detalle'=> "La contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control. debitos: $totalDebito creditos: $totalCredito" );
				// echo '<script>
				// 		alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
				// 		console.log("totalDebito: '.$totalDebito.' totalCredito: '.$totalCredito.'");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}

			if($contAnticipos>0){
				$sqlAnticipo = "UPDATE recibo_caja_cuentas AS C, anticipos AS A
								SET C.saldo_pendiente=C.saldo_pendiente-A.valor
								WHERE C.id=A.id_cuenta_anticipo
									AND C.activo=1
									AND A.activo=1
									AND A.id_documento='$id_factura'
									AND A.tipo_documento='FV'";
				$queryAnticipo = $this->mysql->query($sqlAnticipo,$this->mysql->link);
			}

			$valueInsertAsientos        = substr($valueInsertAsientos, 0, -1);
			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);

			$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta (
									id_item,
									codigo_puc,
									caracter,
									descripcion,
									id_documento,
									tipo_documento,
									id_empresa,
									id_sucursal,
									id_bodega)
								VALUES $valueInsertContabilizacion";
			$queryContabilizar = $this->mysql->query($sqlContabilizar,$this->mysql->link);
			if(!$queryContabilizar){
				$arrayError[0]='Se produjo un error al insertar la configuracion de contabilidad el documento (Cod. Error 600)';
				$arrayError[1]="Error numero: ".$this->mysql->errno();
    			$arrayError[2]="Error detalle: ".$this->mysql->error();
    			$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";

				$this->rollBack($id_factura,1);
        		return array('status'=>false,'detalle'=>$arrayError);

				// return = array('status' => false, 'detalle'=> "Sin conexion con la base de datos, intente de nuevo si el problema persiste consulte al administrador del sistema" );
				// echo'<script>
				// 		alert("Aviso.\nSin conexion con la base de datos, intente de nuevo si el problema persiste consulte al administrador del sistema");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}

			if($valueInsertAsientos != ''){
				$sql   = "INSERT INTO asientos_colgaap (
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
															id_centro_costos,
															id_sucursal,
															id_empresa
														)
								VALUES $valueInsertAsientos";
				$query = $this->mysql->query($sql,$this->mysql->link);
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
				// contabilizacionSimultanea($id_factura,'FV',$this->id_sucursal,$this->id_empresa,$link);
			}
			else{
    			$this->rollback($id_factura,1);
				return array('status' => false, 'detalle'=> "No hay asientos contables a registrar" );

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
			$cuentaPagoNiif   = $arrayCuentaPago['cuentaNiif'];
			$estadoCuentaPago = $arrayCuentaPago['estado'];

			//===================== QUERY CUENTAS ====================//
	    	/**********************************************************/
			$ivaAcumulado       = 0;
			$costoAcumulado     = 0;
			$precioAcumulado    = 0;
			$impuestoAcumulado  = 0;

			$arrayRemisiones    = '';
			$contRemisiones     = 0;
			$acumIdRemisiones   = '';		//CONDICIONAL GLOBAL WHERE SQL IDS REMISIONES

			$whereIdItemsCuentas = '';
			$sql="SELECT fecha_inicio FROM ventas_facturas WHERE activo=1 AND id=$id_factura";
			$query=$this->mysql->query($sql,$this->mysql->link);
			$fechaFactura = $this->mysql->result($query,0,'fecha_inicio');

			$sqlDoc = "SELECT VF.id,
							VF.id_consecutivo_referencia AS id_referencia,
							VF.nombre_consecutivo_referencia AS nombre_referencia,
							VF.id_inventario AS id_item,
							VF.codigo,
							VF.cantidad,
							VF.costo_unitario AS precio,
							VF.costo_inventario AS costo,
							VF.descuento,
							VF.tipo_descuento,
							VF.id_impuesto,
							VF.valor_impuesto,
							VF.id_centro_costos,
							VF.codigo_centro_costos,
							VF.centro_costos,
							VF.inventariable,
							I.cuenta_venta_niif AS cuenta_iva,
							I.valor as tipo_impuesto
						FROM ventas_facturas_inventario AS VF LEFT JOIN impuestos AS I ON(
								I.activo=1
								AND I.id=VF.id_impuesto
							)
						WHERE VF.id_factura_venta='$id_factura' AND VF.activo=1";
			$queryDoc = $this->mysql->query($sqlDoc,$this->mysql->link);

			while($rowDoc = $this->mysql->fetch_array($queryDoc)){
				$typeDocumento = 'FV';
				$impuesto = 0;

				//CALCULO DEL PRECIO
				$precio = $rowDoc['precio'] * $rowDoc['cantidad'];
				$costo  = ABS($rowDoc['costo'] * $rowDoc['cantidad']);
				if($rowDoc['descuento'] > 0){
					$precio = ($rowDoc['tipo_descuento'] == 'porcentaje')? $precio-ROUND(($rowDoc['descuento']*$precio)/100, $decimalesMoneda) : $precio-$rowDoc['descuento'];
				}

				if($rowDoc['id_impuesto'] > 0 && $rowDoc['valor_impuesto'] > 0 && $exento_iva!='Si'){ $impuesto = ROUND($precio*$rowDoc['valor_impuesto']/100, $decimalesMoneda); }

				$whereIdItemsCuentas .= ($whereIdItemsCuentas != '')? ' OR ': ' ';
				$whereIdItemsCuentas .= 'id_items = '.$rowDoc['id_item'];

				$arrayInventarioFactura[$rowDoc['id']]  = Array('id_factura_inventario' =>$rowDoc['id'],
																'precio'               =>$precio,
																'codigo'               =>$rowDoc['codigo'],
																'id_referencia'        =>$rowDoc['id_referencia'],
																'nombre_referencia'    =>$rowDoc['nombre_referencia'],
																'tipo_impuesto'        => $rowDoc['tipo_impuesto'],
																'impuesto'             =>$impuesto,
																'costo'                =>$costo,
																'id_items'             =>$rowDoc['id_item'],
																'inventariable'        =>$rowDoc['inventariable'],
																'cuenta_iva'           =>$rowDoc['cuenta_iva'],
																'id_centro_costos'     =>$rowDoc['id_centro_costos'],
																'codigo_centro_costos' =>$rowDoc['codigo_centro_costos'],
																'centro_costos'        =>$rowDoc['centro_costos']);
			}

			$sqlItemsCuentas = "SELECT id, id_items,descripcion, puc, tipo
								FROM items_cuentas_niif
								WHERE activo=1
									AND id_empresa='$this->id_empresa'
									AND estado='venta'
									AND ($whereIdItemsCuentas)
								GROUP BY id_items,descripcion
								ORDER BY id_items ASC";
			$queryItemsCuentas = $this->mysql->query($sqlItemsCuentas,$this->mysql->link);

			while ($rowCuentasItems = $this->mysql->fetch_array($queryItemsCuentas)) {

				if($rowCuentasItems['descripcion'] == 'contraPartida_precio'){ $rowCuentasItems['puc'] = $cuentaPagoNiif; $rowCuentasItems['tipo'] = 'debito'; }
				if($rowCuentasItems['descripcion'] == 'impuesto'){ $rowCuentasItems['tipo'] = 'credito'; }

				$arrayCuentasItems[$rowCuentasItems['id_items']][$rowCuentasItems['descripcion']]= array('estado' => $rowCuentasItems['tipo'], 'cuenta'=> $rowCuentasItems['puc']);

				$valueInsertContabilizacion .= "('".$rowCuentasItems['id_items']."',
												'".$rowCuentasItems['puc']."',
												'".$rowCuentasItems['tipo']."',
												'".$rowCuentasItems['descripcion']."',
												'$id_factura',
												'FV',
												'$this->id_empresa',
												'$this->id_sucursal',
												'$this->id_bodega'),";
			}

			$arrayGlobalEstado['debito']  = 0;
			$arrayGlobalEstado['credito'] = 0;

			$arrayItemEstado['debito']    = 0;
			$arrayItemEstado['credito']   = 0;

			$acumSubtotal = 0;
			$acumImpuesto = 0;
			$acumBaseRetenciones = 0;

			foreach ($arrayInventarioFactura AS $valArrayInventario) {

				$arrayItemEstado['debito']  = 0;
				$arrayItemEstado['credito'] = 0;

				$idItemArray    = $valArrayInventario['id_items'];
				$cuentaPrecio   = ($this->cuenta_ingreso>0)? $this->cuenta_ingreso : $arrayCuentasItems[$idItemArray]['precio']['cuenta'];
				$contraPrecio   = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['cuenta'];
				$cuentaImpuesto = ($valArrayInventario['cuenta_iva'] > 0)? $valArrayInventario['cuenta_iva']: $arrayCuentasItems[$idItemArray]['impuesto']['cuenta'];

				$cuentaCosto = $arrayCuentasItems[$idItemArray]['costo']['cuenta'];
				$contraCosto = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['cuenta'];

				//======================================= CALC PRECIO =====================================//
				if($cuentaPrecio > 0){
					$estado = $arrayCuentasItems[$idItemArray]['precio']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaPrecio][$estado] > 0){ $arrayAsiento[$cuentaPrecio][$estado] += ROUND($valArrayInventario['precio'],$decimalesMoneda); }
					else{ $arrayAsiento[$cuentaPrecio][$estado] = ROUND($valArrayInventario['precio'],$decimalesMoneda); }
					$arrayAsiento[$cuentaPrecio]['idCcos'] = $valArrayInventario['id_centro_costos'];
					$CadenaCuentaPrecio = strval($cuentaPrecio);

					$arrayGlobalEstado[$estado] += ROUND($valArrayInventario['precio'],$decimalesMoneda);
					$arrayItemEstado[$estado]   += ROUND($valArrayInventario['precio'],$decimalesMoneda);
					$acumSubtotal               += ROUND($valArrayInventario['precio'],$decimalesMoneda);
					$acumBaseRetenciones        += ($CadenaCuentaPrecio[0]=="4")? ROUND($valArrayInventario['precio'],$decimalesMoneda) : 0;

					// $arrayGlobalEstado[$estado] += $valArrayInventario['precio'];
					// $arrayItemEstado[$estado]   += $valArrayInventario['precio'];
					// $acumSubtotal               += $valArrayInventario['precio'];

					//===================================== CALC IMPUESTO ========================================//
					if($cuentaImpuesto > 0 && $valArrayInventario['impuesto'] > 0){
						$estado = $arrayCuentasItems[$idItemArray]['impuesto']['estado'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$cuentaImpuesto][$estado] > 0){ $arrayAsiento[$cuentaImpuesto][$estado] += $valArrayInventario['impuesto']; }
						else{ $arrayAsiento[$cuentaImpuesto][$estado] = $valArrayInventario['impuesto']; }
						$arrayAsiento[$cuentaImpuesto]['idCcos'] = 0;

						$arrayGlobalEstado[$estado] += ROUND($valArrayInventario['impuesto'],$decimalesMoneda);
						$arrayItemEstado[$estado]   += ROUND($valArrayInventario['impuesto'],$decimalesMoneda);
						$acumImpuesto               += ($valArrayInventario["tipo_impuesto"]=="01")? ROUND($valArrayInventario['impuesto'],$decimalesMoneda) : 0;

						// $arrayGlobalEstado[$estado] += $valArrayInventario['impuesto'];
						// $arrayItemEstado[$estado]   += $valArrayInventario['impuesto'];
						// $acumImpuesto               += $valArrayInventario['impuesto'];
					}

					//============================== CALC CONTRA PARTIDA PRECIO =================================//
					if($contraPrecio > 0){
						$arrayAsiento[$contraPrecio]['type'] = 'cuentaPago';
						$estado = $arrayCuentasItems[$idItemArray]['contraPartida_precio']['estado'];

						$contraSaldo = ($arrayItemEstado['debito'] > $arrayItemEstado['credito'])? $arrayItemEstado['debito'] - $arrayItemEstado['credito']
										: $arrayItemEstado['credito'] - $arrayItemEstado['debito'];

						//ARRAY ASIENTO CONTABLE
						if($arrayAsiento[$contraPrecio][$estado] > 0){ $arrayAsiento[$contraPrecio][$estado] += $contraSaldo; }
						else{ $arrayAsiento[$contraPrecio][$estado] = $contraSaldo; }
						$arrayAsiento[$contraPrecio]['idCcos'] = 0;

						$arrayGlobalEstado[$estado] += $contraSaldo;
						$arrayItemEstado[$estado]   += $contraSaldo;

						$acumCuentaClientes 		 = $contraPrecio;
						$estadoCuentaClientes	 = $estado;
					}
				}
				else if($valArrayInventario['inventariable'] == 'false'){
	    			$this->rollback($id_factura,1);
					return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado en la contabilizacion (Niif)" );
					// echo '<script>
					// 		alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado en la contabilizacion");
					// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// 	</script>';
					// exit;
				}

				//======================================= CALC COSTO ===========================================//
				if( $cuentaCosto > 0 && $contraCosto > 0 && $valArrayInventario['nombre_referencia'] != 'Remision' ){

					$estado = $arrayCuentasItems[$idItemArray]['costo']['estado'];

					//ARRAY ASIENTO CONTABLE
					if($arrayAsiento[$cuentaCosto][$estado] > 0){ $arrayAsiento[$cuentaCosto][$estado] += $valArrayInventario['costo']; }
					else{ $arrayAsiento[$cuentaCosto][$estado] = $valArrayInventario['costo']; }
					$arrayAsiento[$cuentaCosto]['idCcos'] = 0;

					$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
					$arrayItemEstado[$estado]   += $valArrayInventario['costo'];

					//ARRAY ASIENTO CONTABLE
					$estado = $arrayCuentasItems[$idItemArray]['contraPartida_costo']['estado'];
					if($arrayAsiento[$contraCosto][$estado] > 0){ $arrayAsiento[$contraCosto][$estado] += $valArrayInventario['costo']; }
					else{ $arrayAsiento[$contraCosto][$estado] = $valArrayInventario['costo']; }
					$arrayAsiento[$contraCosto]['idCcos'] = $valArrayInventario['id_centro_costos'];;

					$arrayGlobalEstado[$estado] += $valArrayInventario['costo'];
					$arrayItemEstado[$estado]   += $valArrayInventario['costo'];
				}
				else if($valArrayInventario['inventariable'] == 'true' && $valArrayInventario['nombre_referencia'] != 'Remision'){
	    			$this->rollback($id_factura,1);
					return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No se ha configurado el manejo del costo en la contabilizacion (Niif)" );
					// echo '<script>
					// 		alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No se ha configurado el manejo del costo en la contabilizacion");
					// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// 	</script>';
					// exit;
				}

				if($arrayItemEstado['debito'] != $arrayItemEstado['credito']){
	    			$this->rollback($id_factura,1);
					return array('status' => false, 'detalle'=> "El item Codigo $valArrayInventario[codigo] No establece doble partida por favor revise la configuracion de contabilizacion (Niif)" );
					// echo '<script>
					// 		alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' No establece doble partida por favor revise la configuracion de contabilizacion");
					// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					// 	</script>';
					// exit;
				}
				// else if($arrayItemEstado['debito'] == 0 && $_SERVER['SERVER_NAME'] != 'erp.plataforma.co' && $_SERVER['SERVER_NAME'] != 'logicalerp.localhost'){
				// 	echo '<script>alert("Aviso.\nEl item Codigo '.$valArrayInventario['codigo'].' Establece doble partida con valor 0, por favor revise la configuracion de contabilizacion")</script>'; exit;
				// }
			}
			$arrayGlobalEstado['debito'] = ROUND($arrayGlobalEstado['debito'],$decimalesMoneda);
			$arrayGlobalEstado['credito'] = ROUND($arrayGlobalEstado['credito'],$decimalesMoneda);

			if($arrayGlobalEstado['debito'] != $arrayGlobalEstado['credito']){
    			$this->rollback($id_factura,1);
				return array('status' => false, 'detalle'=> "El saldo debito es diferente al credito, favor revise la configuracion de contabilizacion (Niif)" );
				// echo '<script>
				// 		alert("alert("Aviso.\nEl saldo debito es diferente al credito, favor revise la configuracion de contabilizacion!");
				// 			document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}
			else if($arrayGlobalEstado['debito'] == 0 || $arrayGlobalEstado['credito'] == 0){
    			$this->rollback($id_factura,1);
				return array('status' => false, 'detalle'=> "El saldo debito y credito en norma niif debe ser mayor a 0 (Niif)" );
				// echo '<script>
				// 		alert("Aviso,\nEl saldo debito y credito en norma niif debe ser mayor a 0!");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}

			//==================== QUERY RETENCIONES =================//
	    	/**********************************************************/
			$acumRetenciones  = 0;
			$contRetencion    = 0;
			$estadoRetencion  = $estadoCuentaClientes; 													//ESTADO CONTRARIO DE LAS RETENCIONES A LA CUENTA CLIENTES
			$sqlRetenciones   = "SELECT valor, codigo_cuenta_niif AS codigo_cuenta, tipo_retencion, cuenta_autoretencion_niif, base FROM ventas_facturas_retenciones WHERE id_factura_venta='$id_factura' AND activo=1";
			$queryRetenciones = $this->mysql->query($sqlRetenciones,$this->mysql->link);

			while($rowRetenciones = $this->mysql->fetch_array($queryRetenciones)){
				$valorBase           = $rowRetenciones['base'];
				$valorRetencion      = $rowRetenciones['valor'];
				$codigoRetencion     = $rowRetenciones['codigo_cuenta'];
				$tipoRetencion       = $rowRetenciones['tipo_retencion'];
				$cuentaAutoretencion = $rowRetenciones['cuenta_autoretencion_niif'];

				if(is_nan($arrayAsiento[$codigoRetencion][$estadoRetencion])){ $arrayAsiento[$codigoRetencion][$estadoRetencion] = 0; }

				if($tipoRetencion == "ReteIva"){																		//CALCULO RETEIVA
					if ($exento_iva=='Si' || $acumImpuesto<$valorBase) { continue; }									//EXCENTO IVA O NO CUMPLE BASE

					$acumRetenciones += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumImpuesto * $valorRetencion/100, $decimalesMoneda);
				}
				else{ 																									//CALCULO RETE, RETECREE Y RETEICA
					if ($acumBaseRetenciones<$valorBase) { continue; }

					$acumRetenciones += ROUND($acumBaseRetenciones*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$codigoRetencion][$estadoRetencion] += ROUND($acumBaseRetenciones*$valorRetencion/100, $decimalesMoneda);
				}

				if($tipoRetencion == "AutoRetencion"){ 																		//SI ES AUTORETENCION NO SE DESCUENTA A CLIENTES
					if(is_nan($cuentaAutoretencion)|| $cuentaAutoretencion < 10000){
						return array('status' => false, 'detalle'=> "No se ha configurado la cuenta Niif Autorretencion (Niif)" );
						// echo '<script>
						// 		alert("Aviso.\nNo se ha configurado la cuenta Niif Autorretencion.");
						// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
						// 	</script>';
						// exit;
					}

					if(is_nan($arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes])){ $arrayAsiento[$cuentaAutoretencion][$estadoCuentaClientes] = 0; }
					$estadoReteCree  = ($estadoRetencion != 'debito')? 'debito' : 'credito';
					$acumRetenciones -= ROUND($acumBaseRetenciones*$valorRetencion/100, $decimalesMoneda);
					$arrayAsiento[$cuentaAutoretencion][$estadoReteCree] += $arrayAsiento[$codigoRetencion][$estadoRetencion];
				}
			}

			$arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes] -= $acumRetenciones;

			//=========================// SALDO ANTICIPO //=========================//
			//**********************************************************************//

			$saldoClientes = $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes];
			$saldoAnticipo = $arrayAnticipo['total'];

			if($saldoAnticipo > 0 && $saldoAnticipo > $saldoClientes && ABS($saldoAnticipo-$saldoClientes) > 1){
    			$this->rollback($id_factura,1);
				return array('status' => false, 'detalle'=> "Los anticipos no pueden ser mayores a la factura de venta (Niif)" );
				// echo'<script>
				// 		alert("Aviso.\nLos anticipos no pueden ser mayores a la factura de venta!");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}
			else{ $arrayAsiento[$acumCuentaClientes][$estadoCuentaClientes] -= $saldoAnticipo; }

			foreach ($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo) {
				$arrayCampo['debito']  = 0;
				$arrayCampo['credito'] = 0;

				$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;
			}


			$totalDebito  = 0;
			$totalCredito = 0;
			$tablaDebug   = '<div style="float:left; width:80px;">Id</div>
							<div style="float:left; width:80px;">Documento</div>
							<div style="float:left; width:80px;">Debito</div>
							<div style="float:left; width:80px;">Credito</div>
							<div style="float:left; width:80px;">PUC</div>
							<div style="float:left; width:150px;">Id Centro Costos</div><br>';

			//=========================// CONTABILIZACION //=========================//
			//***********************************************************************//
			$contAnticipos = 0;
			foreach ($arrayAsiento AS $cuenta => $arrayCampo) {

				if(is_nan($cuenta) || $cuenta==0){ continue; }
				$cuenta = $cuenta * 1;

				$arrayCampo['debito'] = round($arrayCampo['debito'],$decimalesMoneda);
				$arrayCampo['credito'] = round($arrayCampo['credito'],$decimalesMoneda);


				$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
				$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

				if($arrayCampo['debito'] > 0 || $arrayCampo['credito'] > 0){
					$valueInsertAsientos .= "('$id_factura',
											'$consecutivo',
											'FV',
											'Factura de Venta',
											'$id_factura',
											'$consecutivo',
											'FV',
											'$fechaFactura',
											'".$arrayCampo['debito']."',
											'".$arrayCampo['credito']."',
											'$cuenta',
											'$this->id_cliente',
											'".$arrayCampo['idCcos']."',
											'$this->id_sucursal',
											'$this->id_empresa'),";

					$tablaDebug  .='<div style="overflow:hidden;">
										<div style="float:left; width:80px;">-'.$id_factura.'</div>
										<div style="float:left; width:80px;">FC</div>
										<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
										<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
										<div style="float:left; width:80px;">-'.$cuenta.'</div>
									</div><br>';
				}

				//=======================// ANTICIPOS //=======================//
				if($acumCuentaClientes == $cuenta){

					foreach ($arrayAnticipo['anticipos'] as $idAnticipo => $datosAnticipo) {
						$contAnticipos++;

						$arrayCampo['debito']  = 0;
						$arrayCampo['credito'] = 0;

						$arrayCampo[$estadoCuentaClientes] = $datosAnticipo['valor'];

						$totalDebito  += ($arrayCampo['debito'] > 0)? $arrayCampo['debito'] : 0;
						$totalCredito += ($arrayCampo['credito'] > 0)? $arrayCampo['credito'] : 0;

						if(is_nan($datosAnticipo['cuenta_colgaap']) || $datosAnticipo['cuenta_colgaap']==0){ continue; }
						$datosAnticipo['cuenta_colgaap'] = $datosAnticipo['cuenta_colgaap'] * 1;

						$valueInsertAsientos .= "('$id_factura',
												'$consecutivo',
												'FV',
												'Factura de Venta',
												'$datosAnticipo[id_anticipo]',
												'$datosAnticipo[consecutivo]',
												'$datosAnticipo[tipo_documento]',
												'$fechaFactura',
												'$arrayCampo[debito]',
												'$arrayCampo[credito]',
												'$datosAnticipo[cuenta_colgaap]',
												'$datosAnticipo[id_tercero]',
												'$idCcos',
												'$this->id_sucursal',
												'$this->id_empresa'),";

						$tablaDebug .= '<div style="overflow:hidden;">
											<div style="float:left; width:80px;">-'.$idAnticipo.'</div>
											<div style="float:left; width:80px;">RC</div>
											<div style="float:left; width:80px;">-'.$arrayCampo['debito'].'</div>
											<div style="float:left; width:80px;">-'.$arrayCampo['credito'].'</div>
											<div style="float:left; width:80px;">-'.$datosAnticipo['cuenta_colgaap'].'</div>
										<div><br>';
					}
				}

				// 			//MANEJOS DE ANTICIPOS VERSION BETA NO EJERCE
				// if($arrayAnticipo['valor'] > 0 && $cuenta == $cuentaPagoNiif){				//ACUMULADOR VARIABLE GLOBAL TOTAL FACTURA SIN ABONO MENOS ANTICIPO
				// 	$totalFactura        = ($arrayCampo['debito'] >= $arrayCampo['credito'])? $arrayCampo['debito']: $arrayCampo['credito'];
				// 	$partidaCuenta       = ($arrayCampo['debito'] >= $arrayCampo['credito'])? 'debito': 'credito';
				// 	$contraPartidaCuenta = ($partidaCuenta == 'debito')? 'credito': 'debito';

				// 	if($arrayAnticipo['valor'] > $totalFactura){
				// 		echo '<script>
				// 				alert("Aviso.\nEl valor del anticipo no puede ser superior al valor de la factura");
				// 				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 			</script>';
				// 		exit;
				// 	}		//SI EL ANTICIPO ES MAYOR AL VALOR DE LA FACTURA

				// 	$cuentaAnticipo[$partidaCuenta]       = $arrayAnticipo['valor'];
				// 	$cuentaAnticipo[$contraPartidaCuenta] = 0;

				// 	$valueInsertAsientos .= "('$id_factura',
				// 							'$consecutivo',
				// 							'FV',
				// 							'Factura de Venta',
				// 							'$fechaFactura',
				// 							'".$cuentaAnticipo['debito']."',
				// 							'".$cuentaAnticipo['credito']."',
				// 							'".$arrayAnticipo['cuenta']."',
				// 							'$this->id_cliente',
				// 							'$this->id_sucursal',
				// 							'$this->id_empresa'),";

				// 	$saldoSinAnticipo = $totalFactura - $arrayAnticipo['valor'];

				// 	$arrayCampo[$partidaCuenta] = $saldoSinAnticipo;
				// 	$tablaDebug  .= '<div style="float:left; width:80px;">-'.$cuentaAnticipo['debito'].'</div><div style="float:left; width:80px;">-'.$cuentaAnticipo['credito'].'</div><div style="float:left; width:80px;">-'.$cuenta.'</div><br>';

				// 	if($saldoSinAnticipo == 0){ continue; }
				// }


			}

			$tablaDebug .= '<div style="float:left; width:80px; border-top:1px solid">-'.$totalDebito.'</div><div style="float:left; width:80px; border-top:1px solid">'.$totalCredito.'</div><br>';
			// echo $tablaDebug;
			// exit;

			$totalDebito  = ROUND($totalDebito,$decimalesMoneda);
			$totalCredito = ROUND($totalCredito,$decimalesMoneda);
			if($totalDebito != $totalCredito && ABS($totalDebito-$totalCredito)>10){
    			$this->rollback($id_factura,1);
				return array('status' => false, 'detalle'=> "La contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control (Niif) debitos: $totalDebito creditos: $totalCredito" );
				// echo '<script>
				// 		alert("Aviso.\nLa contabilizacion ingresada no cumple doble partida, Confirme su configuracion en el modulo panel de control.");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// exit;
			}

			$valueInsertAsientos        = substr($valueInsertAsientos, 0, -1);
			$valueInsertContabilizacion = substr($valueInsertContabilizacion, 0, -1);

			$sqlContabilizar   = "INSERT INTO contabilizacion_compra_venta_niif (
									id_item,
									codigo_puc,
									caracter,
									descripcion,
									id_documento,
									tipo_documento,
									id_empresa,
									id_sucursal,
									id_bodega)
								VALUES $valueInsertContabilizacion";
			$queryContabilizar = $this->mysql->query($sqlContabilizar,$this->mysql->link);
			if(!$queryContabilizar){
    			$this->rollback($id_factura,1);
				return array('status' => false, 'detalle'=> "Sin conexion con la base de datos, intente de nuevo si el problema persiste consulte al administrador del sistema (Niif)" );
				// echo'<script>
				// 		alert("Aviso.\nSin conexion con la base de datos, intente de nuevo si el problema persiste consulte al administrador del sistema");
				// 		document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
				// 	</script>';
				// 	exit;
			}

			if($valueInsertAsientos != ''){
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
				// contabilizacionSimultanea($id_factura,'FV',$this->id_sucursal,$this->id_empresa,$link);
			}
			else{
    			$this->rollback($id_factura,1);
				return array('status' => false, 'detalle'=> "No hay asientos contables a registrar (Niif)" );

			}
    	}

    	/**
    	 * updateInventario Actualizar las unidades de inventario
    	 * @param  Int $id_factura Id de la factura
    	 * @return Array  Si se genera un error se retorna array con el detalle del error
    	 */
    	public function updateInventario($id_factura,$accion_inventario,$accion_documento){
			$arrayUbicaciones = $this->getSucursales();
			// actualizarCantidadArticulos($id,'salida',"Generar","remision");		//ACTUALIZAR LA CANTIDAD DE ARTICULOS

			// consultar los items de ese documento pero solo los que generan movimiento de inventario
			$sql = "SELECT 
							id_inventario AS id,
							codigo,
							nombre,
							nombre_unidad_medida AS unidad_medida,
							cantidad_unidad_medida AS cantidad_unidades,
							costo_unitario AS costo,
							cantidad
						FROM ventas_facturas_inventario 
						WHERE id_factura_venta='$id_factura'
						AND activo=1 
						AND inventariable='true' 
						AND (nombre_consecutivo_referencia <> 'Remision' OR ISNULL(nombre_consecutivo_referencia) )";
			$query = $this->mysql->query($sql);
			$index = 0;
			$items = array();
			while ($row = $this->mysql->fetch_assoc($query)) {
				$items[$index]                = $row;
				$items[$index]["empresa_id"]  = $this->id_empresa;
				$items[$index]["empresa"]     = NULL;
				$items[$index]["sucursal_id"] = $this->id_sucursal;
				$items[$index]["sucursal"]    = $arrayUbicaciones['sucursales'][$this->id_sucursal];
				$items[$index]["bodega_id"]   = $this->id_bodega;
				$items[$index]["bodega"]      = $arrayUbicaciones['bodegas'][$this->id_bodega];
				
				$index++;
			}
			
			// GENERAR EL MOVIMIENTO DE INVENTARIO
			include '../../../LOGICALERP/inventario/Clases/Inventory.php';

			$params = [ 
				"documento_id"          => $id_factura,
				"documento_tipo"        => "FV",
				"documento_consecutivo" => $this->consecuivo_factura,
				"fecha"                 => $this->fecha_documento,
				"accion_inventario"     => $accion_inventario,
				"accion_documento"      => $accion_documento,    // accion del documento, generar, editar, etc
				"items"                 => $items,
				"mysql"                 => $this->mysql
			];
			$obj = new Inventario_pp();
			$process = $obj->UpdateInventory($params);

			return array('status' => true);

    		// $sql   = "UPDATE inventario_totales AS IT, (
			// 				SELECT SUM(cantidad) AS total_factura_venta, id_inventario AS id_item
			// 				FROM ventas_facturas_inventario
			// 				WHERE id_factura_venta='$id_factura'
			// 					AND activo=1
			// 					AND inventariable='true'
			// 					AND (nombre_consecutivo_referencia <> 'Remision' OR ISNULL(nombre_consecutivo_referencia) )
			// 				GROUP BY id_inventario) AS VFI
			// 			SET IT.cantidad=IT.cantidad-VFI.total_factura_venta,
			// 				IT.id_documento_update          = '$id_factura',
			// 				IT.tipo_documento_update        = 'Factura Venta (API)',
			// 				IT.consecutivo_documento_update = ''
			// 			WHERE IT.id_item=VFI.id_item
	 		// 				AND IT.activo = 1
	 		// 				AND IT.id_ubicacion = '$this->id_bodega'";

			// $query = $this->mysql->query($sql,$this->mysql->link);
			// if(!$query){
			// 	$arrayError[0]='Se produjo un error al insertar la contabilidad el documento (Cod. Error 601)';
			// 	$arrayError[1]="Error numero: ".$this->mysql->errno();
    		// 	$arrayError[2]="Error detalle: ".$this->mysql->error();
    		// 	$arrayError[3]="Por favor envienos el numero de error y el detalle atravez del modulo de soporte ";
    		// 	$this->rollback($id_factura,1);
        	// 	return array('status'=>false,'detalle'=>$arrayError);
			// }
			// else{ return array('status' => true); }
    	}

    	/**
    	 * updateRetenciones Dar de baja las retenciones de la factura de venta
    	 * @param  Int $id_factura Id de la factura de venta
    	 * @return Array array con el resultado de la ejecucion
    	 */
    	public function updateRetenciones($id_factura){
    		$sql="UPDATE ventas_facturas_retenciones SET activo=0 WHERE id_factura_venta=$id_factura ";
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
    		$sql="UPDATE ventas_facturas_inventario SET activo=0 WHERE id_factura_venta=$id_factura ";
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
				$sentencia = " estado=0,id_configuracion_resolucion='',prefijo='',numero_factura='',numero_factura_completo='' " ;
			}

			if ($nivel>=1){
				$sql="UPDATE ventas_facturas SET $sentencia WHERE id_empresa=$this->id_empresa AND id=$id_factura; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM anticipos WHERE id_empresa=$this->id_empresa AND id_documento=$id_factura; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_colgaap WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_factura AND tipo_documento='FV'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);

				$sql="DELETE FROM asientos_niif WHERE id_empresa=$this->id_empresa AND id_sucursal=$this->id_sucursal AND id_documento=$id_factura AND tipo_documento='FV'; ";
				$query=$this->mysql->query($sql,$this->mysql->link);
			}
			if ($nivel>=2){
				$updateInventario = $this->updateInventario($id_factura,'"reversar salida',"Editar");
				// $sql   = "UPDATE inventario_totales AS IT,
				// 				(
				// 					SELECT SUM(cantidad) AS total_factura_venta, id_inventario AS id_item
				// 					FROM ventas_facturas_inventario
				// 					WHERE id_factura_venta='$id_factura'
				// 						AND activo=1
				// 						AND inventariable='true'
				// 						AND (nombre_consecutivo_referencia <> 'Remision' OR ISNULL(nombre_consecutivo_referencia) )
				// 					GROUP BY id_inventario
				// 				) AS VFI
				// 		SET IT.cantidad=IT.cantidad+VFI.total_factura_venta
				// 		WHERE IT.id_item=VFI.id_item
	 			// 			AND IT.activo = 1
	 			// 			AND IT.id_ubicacion = '$this->id_bodega'; ";
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
