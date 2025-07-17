<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../config_var_global.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$whereFactura = "";
	if ($opcGrillaContable=='FacturaVenta') {
		$whereFactura = " OR numero_factura_completo<>''";
	}
	else{
		$whereFactura = " OR consecutivo<>''";
	}

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $opcGrillaContable;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaPrincipal;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND (cliente!='' $whereFactura) AND id_sucursal=$id_sucursal AND id_bodega=$filtro_bodega AND id_empresa=$id_empresa
											AND (id in(SELECT $idTablaPrincipal FROM $tablaInventario WHERE activo=1) $whereFactura)";						//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	= $CualAncho;		//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 	= $CualAlto;		//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 195;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstado'.$opcGrillaContable.'_[id]" /></center>','40');
	 		//$grilla->AddRow('N. Orden','consecutivo_orden_compra',90);
 			//$grilla->AddRow('Id','id',90);

	 		if ($opcGrillaContable=='FacturaVenta') {
	 			$grilla->CamposBusqueda		= 'fecha_inicio,numero_factura,nit,cliente,prefijo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
	 			$grilla->AddRow('Prefijo','prefijo',60);
	 			$grilla->AddRow('N. Factura','numero_factura',80);
				$grilla->AddRowImage('Enviado','<center><img src="img/estado_doc/[response_FE].png" style="cursor:pointer" width="16" height="16" id="imgEnvioFacturaCompra_[id]" /></center>','60','EnvioDian','response_FE');
	 		}
	 		else if ($opcGrillaContable=='CotizacionVenta'){
	 			$grilla->CamposBusqueda		= 'fecha_inicio,consecutivo,nit,cliente';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

	 			$grilla->AddRow('Consecutivo','consecutivo',90);
	 		}
	 		else if ($opcGrillaContable=='RemisionesVenta'){
	 			$grilla->CamposBusqueda		= 'fecha_inicio,consecutivo,nit,cliente,consecutivo_siip';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

	 			$grilla->AddRow('Consecutivo','consecutivo',90);

				$mystring = $_SERVER['SERVER_NAME'];
				$findme   = 'plataforma';
				$findme1  = 'localhost';

				$pos  = strpos($mystring, $findme);//PRODUCCION
				$pos1 = strpos($mystring, $findme1);//LOCAL

				if ($pos !== false || $pos1 !== false) {//SOLO SI ES PLATAFORMA O ES LOCALHOST MUESTRA EL CONSECUTIVO SIIP
	 				$grilla->AddRow('Consecutivo SIIP','consecutivo_siip',100);
	 			}

	 			$grilla->AddRow('Unidades por Facturar','pendientes_facturar',140);
	 		}
	 		else{
	 			$grilla->CamposBusqueda		= 'fecha_inicio,consecutivo,nit,cliente';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
				$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

	 			$grilla->AddRow('Consecutivo','consecutivo',90);
	 			// $grilla->AddRow('Cotizacion Cruce','consecutivo_carga',110);
	 		}

			$grilla->AddRow('Nit','nit',100);
			$grilla->AddRow('Cliente','cliente',200);

	 		if($opcGrillaContable == 'FacturaVenta'){
				$grilla->AddRow('Sucursal Cliente','sucursal_cliente',150);
				$grilla->AddRow('Total','total_factura',150);
				$grilla->AddRow('Saldo Restante','total_factura_sin_abono',150);
				$grilla->AddRow('Fecha Vencimiento','fecha_vencimiento',150);
			}

			if($opcGrillaContable=='PedidoVenta'){ $grilla->AddRow('Unidades Pendientes','unidades_pendientes',120); }
			else if($opcGrillaContable=='CotizacionVenta'){ $grilla->AddRow('Total Unidades','total_unidades',120); }
			else{ $grilla->AddRow('Centro de Costo','centro_costo',100); }

			$grilla->AddRow('Fecha','fecha_inicio',170,'fecha');

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana	= 'Ventana Reuniones Coope'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones	= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Reunion';  //TEXTO DEL BOTON DE NUEVO REGISTRO
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
			// $grilla->MenuContext         = 'true';		//MENU CONTEXTUAL
			// $grilla->MenuContextEliminar = 'true';

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
	    //alert("<?php echo $_SERVER['SERVER_NAME']; ?>");
		function Editar_<?php echo $opcGrillaContable; ?>(id){
			var direccionRender = 'bd/grillaContableBloqueada.php'
			,	estado          = document.getElementById('imgEstado<?php echo $opcGrillaContable; ?>_'+id).getAttribute('src');

			if ('<?php echo $opcGrillaContable; ?>'=='CotizacionVenta') {
	            titulo='Cotizacion de Venta';
	            if(estado == 'img/estado_doc/0.png'){ direccionRender = 'cotizacion/grillaContable.php'; }
	        }
	        else if ('<?php echo $opcGrillaContable; ?>'=='PedidoVenta') {
	            titulo='Pedido de Venta';
	            if(estado == 'img/estado_doc/0.png' ){ direccionRender = 'pedido/grillaContable.php'; }
	        }
	        else if('<?php echo $opcGrillaContable; ?>'=='RemisionesVenta') {
	        	titulo='Remision de Venta';
	        	if(estado == 'img/estado_doc/0.png'){ direccionRender = 'remisiones/grillaContable.php'; }
	        }
	        else if('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {
	        	titulo='Factura de Venta';
	        	if(estado == 'img/estado_doc/0.png'){ direccionRender = 'facturacion/grillaContable.php'; }
	        }

	        Ext.get("contenedor_<?php echo $opcGrillaContable; ?>").load({
				url     : direccionRender,
				scripts : true,
				nocache : true,
				params  :
				{
					id_factura_venta  : id,
					opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
					filtro_bodega     : '<?php echo $filtro_bodega; ?>',
				}
			});

			//si opcgrillacontable no es facturaVenta, entonces se va a cargar unm pedido o cotizacion, tomamos entonces el consecutivo y lo ponemos para el titulo
			if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {
				prefijoDoc=document.getElementById('div_<?php echo $opcGrillaContable; ?>_prefijo_'+id).innerHTML;
				consecutivoDoc = document.getElementById('div_<?php echo $opcGrillaContable; ?>_numero_factura_'+id).innerHTML;
				consecutivoDoc = (consecutivoDoc!=0 )? prefijoDoc +" "+consecutivoDoc: '';
			}
			else{
				consecutivoDoc = document.getElementById('div_<?php echo $opcGrillaContable; ?>_consecutivo_'+id).innerHTML;
			}

	 		if(consecutivoDoc != ''){ document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML = titulo+'<br>N. '+consecutivoDoc; }
			else{ document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML='' }

			Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>.close();
		}

	</script>

<?php
} ?>
