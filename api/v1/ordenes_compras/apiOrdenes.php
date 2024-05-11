<?php

	include_once '../Clases/ApiFunctions.php';
	/**
	 * @apiDefine Compras Se requieren permisos de compras
	 * Para crear, actualizar o eliminar documentos, se requieren los respectivos permisos del modulo de compras
	 *
	 */
	class ApiOrdenes extends ApiFunctions
	{	

		/**
		 * @api {get} /ordenes_compras/?documento_proveedor&fecha_inicio&fecha_final&consecutivo&consecutivo_inicial&consecutivo_final Consultar Ordenes
		 * @apiVersion 1.0.0
		 * @apiDescription Consultar ordenes de compra del sistema (se debe enviar al menos un parametro para realizar la consulta).
		 * @apiName get_ordenes
		 * @apiGroup Ordenes_compras
		 *
		 * @apiParam {String} [documento_proveedor] Numero de documento del proveedor a consultar
		 * @apiParam {date} [fecha_inicio] Fecha inicial para filtrar las ordenes
		 * @apiParam {date} [fecha_final] Fecha final para filtrar las ordenes
		 * @apiParam {Number} [consecutivo] consecutivo del documento
		 * @apiParam {Number} [consecutivo_inicial] Consecutivo inicial de las ordenes a filtrar, Ejemplo: 1
		 * @apiParam {Number} [consecutivo_final] Consecutivo final de las ordenes a filtrar, Ejemplo: 100
		 * @apiParam {Number} id_sucursal id de la sucursal del documento
		 * 
		 * @apiError Unauthorized datos incorrectos de autenticacion
		 * @apiError failure No se recibieron parametros para la consulta
		 * @apiErrorExample {json} Error-Response:
 		 *     HTTP/1.1 404 Not Found
 		 *     {
 		 *       "failure": "No se recibieron parametros para la consulta"
 		 *     }
		 *
		 * @apiSuccess {Number} id Id interno del documento
		 * @apiSuccess {Number} pendientes_facturar Cantidad de items pendientes por facturar
		 * @apiSuccess {String} sucursal Sucursal del documento
		 * @apiSuccess {String} bodega Bodega del documento
		 * @apiSuccess {Number} consecutivo Consecutivo del documento
		 * @apiSuccess {date} fecha_registro Fecha en que se registro el documento
		 * @apiSuccess {date} fecha Fecha del documento
		 * @apiSuccess {date} fecha_vencimiento Fecha de vencimiento del documento
		 * @apiSuccess {String} forma_pago Forma de pago del documento
		 * @apiSuccess {String} documento_proveedor Numero de documento del proveedor
		 * @apiSuccess {String} proveedor Nombre del proveedor
		 * @apiSuccess {String} documento_usuario Documento del usuario que creo el documento
		 * @apiSuccess {String} usuario Nombre del usuario que creo el documento
		 * @apiSuccess {String} usuario_recibe_en_almacen Usuario que recibira el pedido en el almacen
		 * @apiSuccess {String} codigo_area_solicitante Codigo del area que solicita los items del documento
		 * @apiSuccess {String} area_solicitante Area que solicita los items del documento
		 * @apiSuccess {String} referencia Referencia del documento
		 * @apiSuccess {String} observacion Observacion general del documento
		 * @apiSuccess {String} validacion Retorna tru si el documento esta validado si no retorna false
		 * @apiSuccess {String} usuario_validacion
		 * @apiSuccess {Number} estado Estado del documento 1= generado 2= cruzado
		 * @apiSuccess {String} autorizado autorizacion del documento true=autorizado o false=no autorizado
		 * @apiSuccess {Object[]} items Listado de los items del documento
		 * @apiSuccess {String} items.codigo Codigo del item
		 * @apiSuccess {String} items.unidad Unidad de medida del item
		 * @apiSuccess {String} items.nombre Nombre del item 
		 * @apiSuccess {Double} items.cantidad Cantidades del item
		 * @apiSuccess {Double} items.precio Precio por unidad 
		 * @apiSuccess {String} items.observaciones Observaciones del item
		 * @apiSuccess {String} items.tipo_descuento Tipo del descuento del item
		 * @apiSuccess {Double} items.descuento Valor del descuento del item
		 * @apiSuccess {String} items.impuesto Nombre del impuesto del item
		 * @apiSuccess {Double} items.porcentaje_impuesto Porcentaje del impuesto del item
		 * @apiSuccess {Double} items.valor_impuesto Valor neto del impuesto del item
		 *
		 * @apiSuccessExample Success-Response:
		 * 
		 * [
		 * 	{
		 * 		"id": "17",
		 * 		"pendientes_facturar": "2000.00",
		 * 		"sucursal": "Sucursal Principal",
		 * 		"bodega": "Bodega Principal",
		 * 		"consecutivo": "1",
		 * 		"fecha_registro": "2017-03-02",
		 * 		"fecha": "2017-03-02",
		 * 		"fecha_vencimiento": "2017-03-02",
		 * 		"forma_pago": "Mes",
		 * 		"documento_proveedor": "12544096",
		 * 		"proveedor": "proveedor ",
		 * 		"documento_usuario": "26759920",
		 * 		"usuario": "usuario",
		 * 		"usuario_recibe_en_almacen": "",
		 * 		"codigo_area_solicitante": "",
		 * 		"area_solicitante": "",
		 * 		"referencia": "",
		 * 		"observacion": "observacion Orden",
		 * 		"validacion": "false",
		 * 		"id_usuario_validacion": "",
		 * 		"usuario_validacion": "",
		 * 		"estado": "1",
		 * 		"items": [
		 * 			{
		 * 				"codigo": "001",
		 * 				"unidad": "Unidad x 1",
		 * 				"nombre": "Papeleria",
		 * 				"cantidad": "10",
		 * 				"precio": "200.00",
		 * 				"observaciones": "",
		 * 				"tipo_descuento": "porcentaje",
		 * 				"descuento": 0,
		 * 				"impuesto": "",
		 * 				"porcentaje_impuesto": "",
		 * 				"valor_impuesto": 0
		 * 			}
		 * 		]
		 * 	}
		 * ]
		 *
		 *
		 *
		 *
		 */
		public function show($data=NULL){
			$count = 0;
			foreach ($data as $campo => $valor) { $count += ($valor<>'')? 1 : 0 ; }
			if ($count<=0){ return array('status'=>false,'detalle'=>'No se envio ningun parametro de busqueda'); }
			if (( $data['fecha_inicio']<>'' || $data['fecha_final']<>'' ) && ( $data['fecha_inicio']=='' || $data['fecha_final']=='' ) ){ return array('status'=>false,'detalle'=>'Para consulta en rango de fecha se debe enviar los dos campos (fecha_inicial y fecha_final)'); }

			$whereDocumento = '';
			if($data['documento_proveedor']<>''){ $whereDocumento .= " AND nit='$data[documento_proveedor]' "; }
			if($data['fecha']<>''){ $whereDocumento .= " AND fecha_inicio='$data[fecha]' "; }
			if($data['fecha_inicio']<>''){ $whereDocumento .= " AND fecha_inicio BETWEEN '$data[fecha_inicio]' AND '$data[fecha_final]' "; }
			if($data['consecutivo']<>''){ $whereDocumento .= " AND consecutivo=$data[consecutivo] "; }
			if($data['id_sucursal']<>''){ $whereDocumento .= " AND id_sucursal=$data[id_sucursal] "; }
			if($data['consecutivo_inicial']<>'' || $data['consecutivo_final']){ $whereDocumento .= " AND consecutivo IN ($data[consecutivo_inicial], $data[consecutivo_final]) "; }
			if($data['autorizado']<>''){ $whereDocumento .= " AND autorizado='$data[autorizado]' "; }

			$sql="SELECT
					id,
					pendientes_facturar,
					sucursal,
					bodega,
					consecutivo,
					fecha_registro,
					fecha_inicio AS fecha,
					fecha_vencimiento,
					forma_pago,
					nit AS documento_proveedor,
					proveedor,
					documento_usuario,
					usuario,
					usuario_recibe_en_almacen,
					codigo_area_solicitante,
					area_solicitante,
					referencia,
					observacion,
					validacion,
					usuario_validacion,
					autorizado,
					estado
				 FROM compras_ordenes 
				 WHERE activo=1 AND id_empresa=$this->id_empresa AND (estado=1 OR estado=2) $whereDocumento";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while($result[]=$this->mysql->fetch_assoc($query));
			array_pop($result);

			$sql="SELECT
						id_orden_compra,
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
					FROM compras_ordenes_inventario WHERE activo=1 AND id_orden_compra IN (".implode(",",array_column ($result,"id")).")";
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

				$arrayTotales[$row['id_orden_compra']]['subtotal']  += $subtotal;
				$arrayTotales[$row['id_orden_compra']]['descuento'] += $descuento;
				$arrayTotales[$row['id_orden_compra']]['iva']       += $impuesto;

				$arrayItems[$row['id_orden_compra']][]  = array(
																'codigo'              => $row['codigo'],
																'unidad'              => "$row[nombre_unidad_medida] x $row[cantidad_unidad_medida]",
																'nombre'              => utf8_encode($row['nombre']),
																'cantidad'            => $row['cantidad'],
																'precio'              => $row['costo_unitario'],
																'observaciones'       => utf8_encode($row['observaciones']),
																'tipo_descuento'      => $row['tipo_descuento'],
																'descuento'           => $descuento,
																'impuesto'            => $row['impuesto'],
																'porcentaje_impuesto' => $row['valor_impuesto'],
																'valor_impuesto'      => $impuesto
																);
			}

			$result = array_map(function($element) use ($arrayItems){
				$element["items"]=$arrayItems[$element['id']];
				return $element;
			},$result);
			
			$response = array('status' => true,'data'=> $result);
			return $response;
		}

		

	}