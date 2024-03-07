<?php

	/**
	* @class depurarDocumentos
	* @param arr arrayQuery array que contiene los querys
	* @param obj mysql objeto de conexion
	* @param int fecha fecha de depuracion
	* @param arr arrayTables tablas a recorrer
	*/
	class depurarDocumentos
	{
		private $arrayQuery;
		private $link;
		private $fecha;
		private $arrayTables;

		/**
		* @method construct
		* @param str fecha fecha depuracion
		* @param arr arrayTables tablas a recorrer
		* @param obj objeto de conexion mysql
		*/
		function __construct($dias,$arrayTables,$link)
		{
			$this->fecha       = date('Y-m-d',strtotime("-$dias day"));
			$this->arrayTables = $arrayTables;
			$this->link       = $link;
		}

		/*
		* @method getQuerys obtener los sql con los datos a eliminar
		*/
		private function getQuerys()
		{
			foreach ($this->arrayTables as $tabla => $arrayResul) {
				$this->LogNotificaciones("Select tabla $tabla ");
				$sql="SELECT id FROM $tabla WHERE (estado=0 OR estado=3) AND $arrayResul[campo_fecha]<='$this->fecha' AND ( $arrayResul[campo_consecutivo]<=0 OR ISNULL($arrayResul[campo_consecutivo]) ); ";
				$query=mysql_query($sql,$this->link);
				$whereId    = '';
				$whereIdDetail = '';
				while ( @$row=mysql_fetch_array($query) ) {
					$whereId    .= ($whereId=='')? "id=$row[id]" : " OR id=$row[id] " ;

					//RECORRER LAS TABLAS DEPENDIENTES
					foreach ($arrayResul['tabla_detalle'] as $key => $arrayResulTablas) {
						// $this->LogNotificaciones("Select Were tabla_detalle $arrayResulTablas[tabla] ");
						@$this->arrayTables[$tabla]['tabla_detalle'][$key]['whereId'] .= ($this->arrayTables[$tabla]['tabla_detalle'][$key]['whereId'] =='')? "$arrayResulTablas[campo_id]=$row[id]" : " OR $arrayResulTablas[campo_id]=$row[id] " ;
					}

					// $whereIdDetail .= ($whereIdDetail=='')? "$arrayResul[campo_id]=$row[id]" : " OR $arrayResul[campo_id]=$row[id] " ;
				}

				// TABLA PRINCIPAL (CABECERA)
				$this->arrayQuery[] = "DELETE FROM $tabla WHERE ($whereId); ";
				// TABLAS DEPENDIENTES
				foreach ($arrayResul['tabla_detalle'] as $key => $arrayResulTablas) {
					$this->LogNotificaciones("tabla_detalle $arrayResulTablas[tabla] ");
					@$this->arrayQuery[] = "DELETE FROM $arrayResulTablas[tabla] WHERE (".($this->arrayTables[$tabla]['tabla_detalle'][$key]['whereId']).");";
				}

			}


		}

		public function  LogNotificaciones($log){
			$nombre_archivo = '/SIIP/ERP/LOGICALERP/notificaciones/log_notificaciones_erp.txt';
			$archivo = fopen($nombre_archivo, "a");
			fwrite($archivo, date("Y-m-d H:i:s")." => ". $log. "\n");
			fclose($archivo);

		}

		/*
		* @method depurar depurar los documentos
		*/
		public function depurar()
		{
			// $nombre_fichero = '/opt/lampp/htdocs/LOGICALSOFTERP/LOGICALERP/notificaciones/log_notificaciones.txt';

			// if (file_exists($nombre_fichero)) {
			//     echo "El fichero $nombre_fichero existe";
			// } else {
			//     echo "El fichero $nombre_fichero no existe";
			// }

			// chmod($nombre_fichero, 777);

			// return;
			$this->LogNotificaciones(' =========== Inicio Clase ===========');
			$this->getQuerys();
			$this->LogNotificaciones('===========Fin arma query ===========');
			$this->LogNotificaciones('===========Inicio Query Delete ===========');
			foreach ($this->arrayQuery as $key => $sql) {
				$this->LogNotificaciones('Sql > '.$sql);
				$query=mysql_query($sql,$this->link);
			}
			$this->LogNotificaciones('=========== Fin Query Delete ===========');
			$this->LogNotificaciones('=========== Fin clase =========== \\n\\n\\n');
		}

	}

	$dias = 2;

	// ARRAY CON LA INFORMACION DE LAS TABLAS
	$arrayTables['compras_requisicion']['campo_fecha']                = 'fecha_registro';
	$arrayTables['compras_requisicion']['campo_consecutivo']          = 'consecutivo';
	$arrayTables['compras_requisicion']['tabla_detalle'][]            = array('tabla' =>  'compras_requisicion_inventario' , 'campo_id' => 'id_requisicion_compra');

	$arrayTables['compras_ordenes']['campo_fecha']                    = 'fecha_registro';
	$arrayTables['compras_ordenes']['campo_consecutivo']              = 'consecutivo';
	$arrayTables['compras_ordenes']['tabla_detalle'][]                = array( 'tabla' =>  'compras_ordenes' , 'campo_id' => 'id_orden_compra');

	$arrayTables['compras_entrada_almacen']['campo_fecha']            = 'fecha_registro';
	$arrayTables['compras_entrada_almacen']['campo_consecutivo']      = 'consecutivo';
	$arrayTables['compras_entrada_almacen']['tabla_detalle'][]        = array('tabla' =>  'compras_entrada_almacen_inventario' , 'campo_id' => 'id_entrada_almacen');

	$arrayTables['compras_facturas']['campo_fecha']                   = 'fecha_registro';
	$arrayTables['compras_facturas']['campo_consecutivo']             = 'consecutivo';
	$arrayTables['compras_facturas']['tabla_detalle'][]               = array('tabla' =>  'compras_facturas_inventario' , 'campo_id' => 'id_factura_compra');

	$arrayTables['comprobante_egreso']['campo_fecha']                 = 'fecha_inicial';
	$arrayTables['comprobante_egreso']['campo_consecutivo']           = 'consecutivo';
	$arrayTables['comprobante_egreso']['tabla_detalle'][]             = array('tabla' =>  'comprobante_egreso_documentos' , 'campo_id' => 'id_comprobante_egreso');

	$arrayTables['ventas_cotizaciones']['campo_fecha']                = 'fecha_registro';
	$arrayTables['ventas_cotizaciones']['campo_consecutivo']          = 'consecutivo';
	$arrayTables['ventas_cotizaciones']['tabla_detalle'][]            = array('tabla' =>  'ventas_cotizaciones_inventario' , 'campo_id' => 'id_cotizacion_venta');

	$arrayTables['ventas_pedidos']['campo_fecha']                     = 'fecha_registro';
	$arrayTables['ventas_pedidos']['campo_consecutivo']               = 'consecutivo';
	$arrayTables['ventas_pedidos']['tabla_detalle'][]                 = array('tabla' =>  'ventas_pedidos_inventario' , 'campo_id' => 'id_pedido_venta');

	$arrayTables['ventas_remisiones']['campo_fecha']                  = 'fecha_registro';
	$arrayTables['ventas_remisiones']['campo_consecutivo']            = 'consecutivo';
	$arrayTables['ventas_remisiones']['tabla_detalle'][]              = array('tabla' =>  'ventas_remisiones_inventario' , 'campo_id' => 'id_remision_venta');

	$arrayTables['ventas_facturas']['campo_fecha']                    = 'fecha_creacion';
	$arrayTables['ventas_facturas']['campo_consecutivo']              = 'numero_factura';
	$arrayTables['ventas_facturas']['tabla_detalle'][]                = array('tabla' =>  'ventas_facturas_inventario' , 'campo_id' => 'id_factura_venta');
	$arrayTables['ventas_facturas']['tabla_detalle'][]                = array('tabla' =>  'ventas_facturas_retenciones' , 'campo_id' => 'id_factura_venta');

	$arrayTables['recibo_caja']['campo_fecha']                        = 'fecha_inicial';
	$arrayTables['recibo_caja']['campo_consecutivo']                  = 'consecutivo';
	$arrayTables['recibo_caja']['tabla_detalle'][]                    = array('tabla' =>  'recibo_caja_cuentas' , 'campo_id' => 'id_recibo_caja');

	$arrayTables['nota_contable_general']['campo_fecha']              = 'fecha_registro';
	$arrayTables['nota_contable_general']['campo_consecutivo']        = 'consecutivo';
	$arrayTables['nota_contable_general']['tabla_detalle'][]          = array('tabla' =>  'nota_contable_general_cuentas' , 'campo_id' => 'id_nota_general');

	// $arrayTables['nomina_planillas']['campo_fecha']                   = 'fecha_creacion';
	// $arrayTables['nomina_planillas']['campo_consecutivo']             = 'consecutivo';
	// $arrayTables['nomina_planillas']['tabla_detalle'][]               = array('tabla' =>  'nomina_planillas_empleados' , 'campo_id' => 'id_planilla');
	// $arrayTables['nomina_planillas']['tabla_detalle'][]               = array('tabla' =>  'nomina_planillas_empleados_conceptos' , 'campo_id' => 'id_planilla');

	// $arrayTables['nomina_planillas_liquidacion']['campo_fecha']       = 'fecha_creacion';
	// $arrayTables['nomina_planillas_liquidacion']['campo_consecutivo'] = 'consecutivo';
	// $arrayTables['nomina_planillas_liquidacion']['tabla_detalle'][]   = array('tabla' =>  'nomina_planillas_liquidacion_empleados' , 'campo_id' => 'id_planilla');
	// $arrayTables['nomina_planillas_liquidacion']['tabla_detalle'][]   = array('tabla' =>  'nomina_planillas_liquidacion_empleados_conceptos' , 'campo_id' => 'id_planilla');
	// $arrayTables['nomina_planillas_liquidacion']['tabla_detalle'][]   = array('tabla' =>  'nomina_vacaciones_empleados' , 'campo_id' => 'id_planilla');

	// $arrayTables['nomina_planillas_ajuste']['campo_fecha']            = 'fecha_creacion';
	// $arrayTables['nomina_planillas_ajuste']['campo_consecutivo']      = 'consecutivo';
	// $arrayTables['nomina_planillas_ajuste']['tabla_detalle'][]        = array('tabla' =>  'nomina_planillas_ajuste_empleados' , 'campo_id' => 'id_planilla');
	// $arrayTables['nomina_planillas_ajuste']['tabla_detalle'][]        = array('tabla' =>  'nomina_planillas_ajuste_empleados_conceptos' , 'campo_id' => 'id_planilla');

	// $arrayTables['']
	// $arrayTables['']

	if ($depurar<>'true') {
		$conexionDB ='127.0.0.1';
		$user       ='root';
		$pass       ='serverchkdsk';
		$bd         = 'erp';

		if(!isset($link)){
			$link = mysql_connect($conexionDB,$user,$pass);
		}
		if(!$link){echo 'Error Conectando a Mysql<br />';};
		mysql_select_db($bd,$link);
		if(!@mysql_select_db($bd,$link)){echo 'Error Conectando a la la base de datos "'.$bd.'" <br />';};

		date_default_timezone_set("America/Bogota");
		$hora_notificacion = date("H:i");
		$hora_activacion1 = '02:50';
		$hora_activacion2 = '03:10';

		if ($hora_notificacion>=$hora_activacion1 && $hora_notificacion<=$hora_activacion2){
			$objeto = new depurarDocumentos($dias,$arrayTables,$link);
			$objeto->depurar();
		}
	}



/*
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//============================================================= DEPURAR DOCUMENTOS DE COMPRAS =======================================================================//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// REQUISICIONES
	$sql="SELECT id FROM compras_requisicion WHERE estado=0 AND fecha_registro<='$fechaDelete' AND ( consecutivo<=0 OR ISNULL(consecutivo) ) ";
	$query=$mysql->query($sql,$mysql->link);
	while ( $row = $mysql->fetch_array($query) ) {
		$whereIdRequisicion    .= ($whereIdRequisicion=='')? "id=$row[id]" : " OR id=$row[id] " ;
		$whereIdRequisicionInv .= ($whereIdRequisicionInv=='')? "id_requisicion=$row[id]" : " OR id_requisicion=$row[id] " ;
	}

	// ELIMINAR LAS REQUISICIONES
	$sql="DELETE FROM compras_requisicion";
	$query=$mysql->query($sql,$mysql->link);
	// ELIMINAR LOS ITEMS DE LAS REQUISICIONES
	$sql="";
	$query=$mysql->query($sql,$mysql->link);

	//========================================================== ORDENES DE COMPRA ========================================================================//
	//CONSULTAMOS TODAS LAS ORDENES DE COMPRA QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulOrdenes="SELECT id FROM compras_ordenes WHERE estado=0 AND fecha_registro<='$fechaDelete' ";
	$queryConsulOrdenes=mysql_query($sqlConsulOrdenes,$link);

	while ($rowOrdenes=mysql_fetch_array($queryConsulOrdenes)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowOrdenes['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_orden_compra = '.$rowOrdenes['id'];

	}

	//ELIMINAR LOS ARTICULOS DE LA ORDEN
	$sqlOrdenesArticulos="DELETE FROM compras_ordenes_inventario WHERE ($whereDI)";
	$queryOrdenesArticulos=mysql_query($sqlOrdenesArticulos,$link);

	//ELIMINAR LAS ORDEN NO GENERADAS
	$sqlOrdenes="DELETE FROM compras_ordenes WHERE ($whereDP)";
	$queryOrdenes=mysql_query($sqlOrdenes,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";


	//========================================================== FACTURAS DE COMPRA ========================================================================//
	//CONSULTAMOS TODAS LAS FACTURAS DE VENTA QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulFacturasCompra="SELECT id FROM compras_facturas WHERE estado=0 AND fecha_registro<='$fechaDelete' ";
	$queryConsulFacturas=mysql_query($sqlConsulFacturasCompra,$link);

	while ($rowFacturas=mysql_fetch_array($queryConsulFacturas)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowFacturas['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_factura_compra = '.$rowFacturas['id'];

	}

	//ELIMINAR LOS ARTICULOS DE LA REMISION
	$sqlFacturasCompraArticulos="DELETE FROM compras_facturas_inventario WHERE ($whereDI)";
	$queryFacturasArticulos=mysql_query($sqlFacturasCompraArticulos,$link);

	//ELIMINAR LAS RETENCIONES
	$sqlFacturasRetenciones="DELETE FROM compras_facturas_retenciones WHERE ($whereDI)";
	$queryFacturasRetenciones=mysql_query($sqlFacturasRetenciones,$link);

	//ELIMINAR LAS FACTURAS NO GENERADAS
	$sqlFacturas="DELETE FROM compras_facturas WHERE ($whereDP)";
	$queryFacturas=mysql_query($sqlFacturas,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//============================================================= DEPURAR DOCUMENTOS DE VENTAS =======================================================================//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//======================================================== COTIZACIONES DE VENTA =====================================================================//
	//CONSULTAMOS TODAS LAS COTIZACIONES DE VENTA QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulCotizaciones="SELECT id FROM ventas_cotizaciones WHERE estado=0 AND fecha_registro<='$fechaDelete' ";
	$queryConsulCotizaciones=mysql_query($sqlConsulCotizaciones,$link);

	while ($rowCotizaciones=mysql_fetch_array($queryConsulCotizaciones)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowCotizaciones['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_cotizacion_venta = '.$rowCotizaciones['id'];

	}

	//ELIMINAR LOS ARTICULOS DE LA COTIZACION
	$sqlCotizacionesArticulos="DELETE FROM ventas_cotizaciones_inventario WHERE ($whereDI)";
	$queryCotizacionesArticulos=mysql_query($sqlCotizacionesArticulos,$link);

	//ELIMINAR LAS COTIZACIONES NO GENERADAS
	$sqlCotizaciones="DELETE FROM ventas_cotizaciones WHERE ($whereDP)";
	$queryCotizaciones=mysql_query($sqlCotizaciones,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";



	//========================================================== PEDIDOS DE VENTA ========================================================================//
	//CONSULTAMOS TODAS LOS PEDIDOS DE VENTA QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulPedidos="SELECT id FROM ventas_pedidos WHERE estado=0 AND fecha_registro<='$fechaDelete' ";
	$queryConsulPedidos=mysql_query($sqlConsulPedidos,$link);

	while ($rowPedidos=mysql_fetch_array($queryConsulPedidos)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowPedidos['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_pedido_venta = '.$rowPedidos['id'];

	}

	//ELIMINAR LOS ARTICULOS DEL PEDIDO
	$sqlPedidosArticulos="DELETE FROM ventas_pedidos_inventario WHERE ($whereDI)";
	$queryPedidosArticulos=mysql_query($sqlPedidosArticulos,$link);

	//ELIMINAR LOS PEDIDOS NO GENERADAS
	$sqlPedidos="DELETE FROM ventas_pedidos WHERE ($whereDP)";
	$queryPedidos=mysql_query($sqlPedidos,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";



	//========================================================== REMISIONES DE VENTA ========================================================================//
	//CONSULTAMOS TODAS LAS REMISIONES DE VENTA QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulRemisiones="SELECT id FROM ventas_remisiones WHERE estado=0 AND fecha_registro<='$fechaDelete' ";
	$queryConsulRemisiones=mysql_query($sqlConsulRemisiones,$link);

	while ($rowRemisiones=mysql_fetch_array($queryConsulRemisiones)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowRemisiones['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_remision_venta = '.$rowRemisiones['id'];

	}

	//ELIMINAR LOS ARTICULOS DE LA REMISION
	$sqlRemisionesArticulos="DELETE FROM ventas_remisiones_inventario WHERE ($whereDI)";
	$queryRemisionesArticulos=mysql_query($sqlRemisionesArticulos,$link);

	//ELIMINAR LAS REMISIONES NO GENERADAS
	$sqlRemisiones="DELETE FROM ventas_remisiones WHERE ($whereDP)";
	$queryRemisiones=mysql_query($sqlRemisiones,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";



	//========================================================== FACTURAS DE VENTA ========================================================================//
	//CONSULTAMOS TODAS LAS FACTURAS DE VENTA QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulFacturas="SELECT id FROM ventas_facturas WHERE estado=0 AND fecha_registro<='$fechaDelete' ";
	$queryConsulFacturas=mysql_query($sqlConsulFacturas,$link);

	while ($rowFacturas=mysql_fetch_array($queryConsulFacturas)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowFacturas['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_factura_venta = '.$rowFacturas['id'];

	}

	//ELIMINAR LOS ARTICULOS DE LA REMISION
	$sqlFacturasArticulos="DELETE FROM ventas_facturas_inventario WHERE ($whereDI)";
	$queryFacturasArticulos=mysql_query($sqlFacturasArticulos,$link);

	//ELIMINAR LAS RETENCIONES
	$sqlFacturasRetenciones="DELETE FROM ventas_facturas_retenciones WHERE ($whereDI)";
	$queryFacturasRetenciones=mysql_query($sqlFacturasRetenciones,$link);

	//ELIMINAR LAS FACTURAS NO GENERADAS
	$sqlFacturas="DELETE FROM ventas_facturas WHERE ($whereDP)";
	$queryFacturas=mysql_query($sqlFacturas,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//================================================================== DEPURAR NOTAS CONTABLES =======================================================================//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//CONSULTAMOS TODAS LAS NOTAS GENERALES QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulNotas="SELECT id FROM nota_contable_general WHERE estado=0 AND fecha_registro<='$fechaDelete' ";
	$queryConsulNotas=mysql_query($sqlConsulNotas,$link);

	while ($rowNotas=mysql_fetch_array($queryConsulNotas)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowNotas['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_nota_general = '.$rowNotas['id'];

	}

	//ELIMINAR EL DETALLE DE SUS CUENTAS
	$sqlCuentasNota="DELETE FROM nota_contable_general_cuentas WHERE ($whereDI)";
	$queryCuentasNota=mysql_query($sqlCuentasNota,$link);

	//ELIMINAR LAS NOTAS NO GENERADAS
	$sqlNotas="DELETE FROM nota_contable_general WHERE ($whereDP)";
	$queryNotas=mysql_query($sqlNotas,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//================================================================== DEPURAR COMPROBANTE DE EGRESO =================================================================//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//CONSULTAMOS TODAS LAS NOTAS GENERALES QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulNotas="SELECT id FROM comprobante_egreso WHERE estado=0 AND fecha_inicial<='$fechaDelete' ";
	$queryConsulNotas=mysql_query($sqlConsulNotas,$link);

	while ($rowNotas=mysql_fetch_array($queryConsulNotas)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowNotas['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_comprobante_egreso = '.$rowNotas['id'];

	}

	//ELIMINAR EL DETALLE DE SUS CUENTAS
	$sqlCuentasNota="DELETE FROM comprobante_egreso_documentos WHERE ($whereDI)";
	$queryCuentasNota=mysql_query($sqlCuentasNota,$link);

	//ELIMINAR LAS NOTAS NO GENERADAS
	$sqlNotas="DELETE FROM comprobante_egreso WHERE ($whereDP)";
	$queryNotas=mysql_query($sqlNotas,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//================================================================== DEPURAR RECIBO DE CAJA ========================================================================//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//CONSULTAMOS TODAS LAS NOTAS GENERALES QUE NO SE HAN GENERADO PARA ELIMINARLAS

	$sqlConsulNotas="SELECT id FROM recibo_caja WHERE estado=0 AND fecha_inicial<='$fechaDelete' ";
	$queryConsulNotas=mysql_query($sqlConsulNotas,$link);

	while ($rowNotas=mysql_fetch_array($queryConsulNotas)) {
		//RECORREMOS LA CONSULTA Y ARMAMOS EL WHERE PARA ELIMINAR LOS REGISTROS
			$whereDP .= ($whereDP != '')? ' OR ' : '' ;
        	$whereDP .= 'id = '.$rowNotas['id'];

        	$whereDI .= ($whereDI != '')? ' OR ' : '' ;
        	$whereDI .= 'id_recibo_caja = '.$rowNotas['id'];

	}

	//ELIMINAR EL DETALLE DE SUS CUENTAS
	$sqlCuentasNota="DELETE FROM recibo_caja_documentos WHERE ($whereDI)";
	$queryCuentasNota=mysql_query($sqlCuentasNota,$link);

	//ELIMINAR LAS NOTAS NO GENERADAS
	$sqlNotas="DELETE FROM recibo_caja WHERE ($whereDP)";
	$queryNotas=mysql_query($sqlNotas,$link);

	//LIMPIAR LAS VARIABLES
	$whereDP="";
	$whereDI="";

*/


?>






