<?php

	include '../../../misc/ConnectDb/class.ConnectDb.php';
	/**
	 * @apiDefine Ventas Se requieren permisos de ventas
	 * Para crear, actualizar o eliminar documentos, se requieren los respectivos permisos del modulo de ventas
	 *
	 */
	class apiFacturacion
	{


		/**
		 * @api {get} /factura_electronica/:documento_cliente/:fecha/:fecha_inicio/:fecha_final/:numero_factura/:numero_factura_inicial/:numero_factura_final/:estado Consultar Facturas
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar facturas del sistema.
		 * @apiName get_facturas
		 * @apiGroup Factura Electronica
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
			if($data['numero_factura']<>''){ $whereFacturas .= " AND numero_factura_completo=$data[numero_factura] "; }
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
					UUID
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
		 * @api {post} /factura_electronica/ Crear factura
		 * @apiVersion 1.0.0
		 * @apiDescription Registrar facturas en el sistema
		 * @apiName store_facturas
		 * @apiPermission Ventas
		 * @apiGroup Factura Electronica
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
		 * @apiParam {Double} valor_abono Valor de abono o anticipo u otro concepto aplicado al total de la factura, es obligatorio si el saldo de la factura y el total de la factura son diferentes
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
			$url_api = "http://api.facsep.com/api/Comunicacion/Comprobante";

			// Cambiamos la url de validacion por la del envio
			$params                   = [];
			$params['request_url']    = $url_api;
			$params['request_method'] = "POST";
			$params['Authorization']  = "";
			$params['data']           = '';

			// Consumimos el API y obtenemos sus resultados
			$respuesta = $this->curlApi($params);
			$respuesta = json_decode($respuesta,true);

			$respuestaFinal['comprobante'] = "Se ejecuto el envio en desarrollo";

			// return $respuestaFinal;
			return array('status' => true,'data'=> $respuesta);

		}

		public function curlApi($params){
			$client = curl_init();
			$options = array(
								CURLOPT_HTTPHEADER     => array('Content-Type: application/json',"$params[Authorization]"),
							    CURLOPT_URL            => "$params[request_url]",
							    CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
							    CURLOPT_RETURNTRANSFER => true,
							    CURLOPT_POSTFIELDS     => $params['data'],
							);
			curl_setopt_array($client,$options);
			$response    = curl_exec($client);
			$curl_errors = curl_error($client);

			if(!empty($curl_errors)){
				$response['status']               = 'failed';
				$response['errors'][0]['titulo']  = curl_getinfo($client);
				$response['errors'][0]['detalle'] = curl_error($client);
			}

			$httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
			curl_close($client);
			return $response;
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