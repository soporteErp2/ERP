<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");


	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_sucursal = $_SESSION['SUCURSAL'];
	$id_empresa  = $_SESSION['EMPRESA'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		// CONSULTAR SOLO LOS DOCUMENTOS QUE NO ESTES AGREGADOS
		// if ($opcGrillaContable=='OrdenCompra') {
		// 	$tablaValidar='compras_ordenes_inventario';
		// 	$idTablaValidar='id_orden_compra';
		// }

		// $sql="SELECT id_consecutivo_referencia,consecutivo_referencia,nombre_consecutivo_referencia
		// 		FROM $tablaValidar WHERE activo=1 AND $idTablaValidar=$id_documento";
		// $query=mysql_query($sql,$link);

		// while ($row=mysql_fetch_array($query)) {
		// 	if ($row['nombre_consecutivo_referencia']=='Requisicion') {
		// 		$whereIdDoc.=($whereIdDoc=='')? ' AND id<>'.$row['id_consecutivo_referencia'] : ' AND id<>'.$row['id_consecutivo_referencia'] ;
		// 	}
		// }

		//VERIFICAMOS, SI ES UNA COTIZACION NO MOSTRAMOS LAS QUE ESTEN VENCIDAS POR TIEMPO

		// if ($tablaDocumentoCruce=='ventas_cotizaciones') {
		// 	$sql="activo = 1 AND cliente <> '' AND (estado = 1 OR estado = 2) AND id_sucursal = $id_sucursal AND id_bodega = $filtro_bodega AND id_empresa='$id_empresa' AND ('".date('Y-m-d')."' BETWEEN date_format(fecha_inicio,'%Y-%m-%d') AND date_format(fecha_finalizacion,'%Y-%m-%d') ) $whereIdCotizacion";
		// }
		// else if($tablaDocumentoCruce=='ventas_remisiones'){
		// 	$sql="activo = 1 AND cliente <> '' AND (estado = 1 OR estado = 2) AND id_sucursal = $id_sucursal AND id_bodega = $filtro_bodega AND id_empresa='$id_empresa' AND pendientes_facturar>0 $whereIdRemision";

		// 	if($opcGrillaContable == 'FacturaVenta'){
		// 		$sqlCcos   = "SELECT id_centro_costo, estado FROM ventas_facturas WHERE id='$id_documento' AND id_empresa='$id_empresa'";
		// 		$queryCcos = mysql_query($sqlCcos,$link);
		// 		$idCcos    = mysql_result($queryCcos, 0, 'id_centro_costo');
		// 		$estado    = mysql_result($queryCcos, 0, 'estado');

		// 		$whereCcos = "";
		// 		if($idCcos > 0 && $estado == 0){ $whereCcos = "AND id_centro_costo='$idCcos'"; }
		// 		else if($estado == 0) { $whereCcos = ""; }
		// 		$sql="activo = 1 AND cliente <> '' AND (estado = 1 OR estado = 2) AND id_sucursal = $id_sucursal  AND id_empresa='$id_empresa' AND id_bodega = $filtro_bodega AND pendientes_facturar>0 $whereCcos $whereIdRemision";
		// 	}
		// }
		// else{
		// 	$sql="activo = 1 AND cliente <> '' AND (estado = 1 OR estado = 2) AND id_sucursal = $id_sucursal AND id_bodega = $filtro_bodega AND id_empresa='$id_empresa' AND unidades_pendientes>0 $whereIdPedido";
		// 	// $sql="activo = 1 AND cliente <> '' AND (estado = 1 OR estado = 2) AND id_sucursal = $id_sucursal AND id_empresa='$id_empresa' AND unidades_pendientes>0";
		// }
		// echo $sql;

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $nameGrillaLoad;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaDocumentoCruce;	//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= " activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal AND id_bodega=$filtro_bodega AND (estado=1 OR estado = 2)";
			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 		= 560;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 410;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 195;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'fecha_inicio,consecutivo,nit,proveedor,bodega';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
			//$grilla->Gfilters			= 'false';
			//$grilla->GfiltersAutoOpen	= 'false';
	 		//$grilla->AddFilter('Estado de la Factura','estado','estado');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('estado','estado',45);
			$grilla->AddRow(utf8_decode('Doc N°'),'consecutivo',50);
			// if($tablaDocumentoCruce!='compras_requisicion'){
			// 	$grilla->AddRow('Nit','nit',120);
			// 	$grilla->AddRow('Proveedor','proveedor',200);
		 //    }
			$grilla->AddRow('Documento','documento_solicitante',90);
			$grilla->AddRow('Solicitante','nombre_solicitante',200);
			$grilla->AddRow('Area','area_solicitante',100);
			$grilla->AddRow('Bodega','bodega',100);
			$grilla->AddRow('Fecha','fecha_inicio',170,'fecha');

			// if($tablaDocumentoCruce=='ventas_pedidos'){ $grilla->AddRow('Unidades Pendientes','unidades_pendientes',120); }
			// else if($tablaDocumentoCruce=='ventas_remisiones'){
			// 	$grilla->AddRow('Centro de Costo','centro_costo',150);
			// 	$grilla->AddRow('Unidades Por Facturar','pendientes_facturar',150);
			// }

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Reuniones Coope'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Reunion'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';	//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 400;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 200;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

 		//CONFIGURACION DEL MENU CONTEXTUAL
			$grilla->MenuContext		= 'false';		//MENU CONTEXTUAL
			$grilla->MenuContextEliminar= 'false';

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
			$grilla->AddMenuContext('Cargar en Nuevo Documento','doc16','Editar_'.$nameGrillaLoad.'([id])');
			$grilla->AddMenuContext('Agregar al Documento Actual','docAdd16','agregarDocumento'.$opcGrillaContable.'([consecutivo])');

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


		/**//////////////////////////////////////////////////////////////**/
		/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
		/**/															/**/
		/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
		/**/	$grilla->inicializa($_POST);//variables POST			/**/
		/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
		/**/															/**/
		/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>

		function Editar_<?php echo $nameGrillaLoad; ?>(id){
			var consecutivo = document.getElementById('div_<?php echo $nameGrillaLoad; ?>_consecutivo_'+id).innerHTML;
			ajaxBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>(consecutivo,'false');
			// Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>.close();
		}
	</script>

<?php
} ?>




