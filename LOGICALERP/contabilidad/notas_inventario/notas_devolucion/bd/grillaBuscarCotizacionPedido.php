<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	include("../../../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if($opcCargar == "facturaCompra"){
		$rowGroupAuxquery = "id_factura_compra";
	}
	else if($opcCargar == "facturaVenta"){
		$rowGroupAuxquery = "id_factura_venta";
	}
	else if($opcCargar == "remisionVenta"){
		$rowGroupAuxquery = "id_remision_venta";
	}

	//QUERY AUX
	$sqlAux = "SELECT $id_tabla_carga FROM $tabla_inventario_carga WHERE saldo_cantidad > 0 AND activo = 1 AND id_bodega=$filtro_bodega AND id_sucursal=$id_sucursal AND id_empresa=$id_empresa GROUP BY $rowGroupAuxquery";
	$queryAux = mysql_query($sqlAux,$mysql->link);
	while( $row = mysql_fetch_assoc($queryAux) ){
		$whereId .= " $row[$id_tabla_carga] ,";
	}

	$whereId = substr($whereId,0,-1);

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/
	
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $nombreGrillaCotizacionPedido;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaCotizacionPedido;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere		= 'activo=1 AND (estado>0 AND estado<3) AND id_bodega='.$filtro_bodega.' AND id_sucursal='.$id_sucursal.' AND id_empresa='.$id_empresa." AND id IN ($whereId)";
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
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
			//$grilla->Gfilters			= 'false';
			//$grilla->GfiltersAutoOpen	= 'false';
	 		//$grilla->AddFilter('Estado de la Factura','estado','estado');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstado'.$opcGrillaContable.'_[id]" /></center>','50');
			// $grilla->AddRow('estado','estado',45);

			if ($opcCargar=='facturaCompra') {
				$grilla->CamposBusqueda = 'numero_factura,nit,proveedor';			//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->AddRow('prefijo Factura','prefijo_factura',120);
				$grilla->AddRow('Numero Factura','numero_factura',120);
				$grilla->AddRow('Nit','nit',120);
				$grilla->AddRow('Proveedor','proveedor',200);
			}
			else{
				if ($opcCargar=='remisionVenta') {
					$grilla->CamposBusqueda	= 'consecutivo,nit,cliente';				//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
					$grilla->AddRow('Consecutivo','consecutivo',100);
				}
				else if ($opcCargar=='facturaVenta') {
					$grilla->CamposBusqueda	= 'prefijo,numero_factura,nit,cliente';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
					$grilla->AddRow('Prefijo','prefijo',90);
					$grilla->AddRow('Numero','numero_factura',100);
				}
				$grilla->AddRow('Nit','nit',100);
				$grilla->AddRow('Cliente','cliente',200);
				$grilla->AddRow('Fecha','fecha_inicio',250,'fecha');
			}

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho     = 760;
			$grilla->FColumnaGeneralAncho = 380;
			$grilla->FColumnaGeneralAlto  = 25;
			$grilla->FColumnaLabelAncho   = 130;
			$grilla->FColumnaFieldAncho   = 150;

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
 			// $grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		// $grilla->MenuContextEliminar= 'true';

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
		function Editar_<?php echo $nombreGrillaCotizacionPedido; ?>(id){
			if ('<?php echo $opcGrillaContable; ?>'=='DevolucionCompra') {
				var prefijo          = document.getElementById('div_<?php echo $nombreGrillaCotizacionPedido; ?>_prefijo_factura_'+id).innerHTML
				,	numero_documento = document.getElementById('div_<?php echo $nombreGrillaCotizacionPedido; ?>_numero_factura_'+id).innerHTML;

				if(prefijo != ''){ numero_documento = prefijo+' '+numero_documento; }
			}
			else if ('<?php echo $opcGrillaContable; ?>'=='DevolucionVenta'){

				if ('<?php echo $opcCargar; ?>'=='remisionVenta') { numero_documento = document.getElementById('div_<?php echo $nombreGrillaCotizacionPedido; ?>_consecutivo_'+id).innerHTML; }
				else if ('<?php echo $opcCargar; ?>'=='facturaVenta') {
					prefijo          = document.getElementById('div_<?php echo $nombreGrillaCotizacionPedido; ?>_prefijo_'+id).innerHTML;
					numero_documento = (prefijo!='')? prefijo+' '+document.getElementById('div_<?php echo $nombreGrillaCotizacionPedido; ?>_numero_factura_'+id).innerHTML
										: document.getElementById('div_<?php echo $nombreGrillaCotizacionPedido; ?>_numero_factura_'+id).innerHTML;
				}
			}

			document.getElementById("cotizacionPedido<?php echo $opcGrillaContable; ?>").value = numero_documento;
			ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(numero_documento,id)
			Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>.close();
		}

	</script>

<?php
} ?>




