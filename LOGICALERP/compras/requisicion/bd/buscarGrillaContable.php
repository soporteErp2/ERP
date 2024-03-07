<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../config_var_global.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $opcGrillaContable;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaPrincipal;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_sucursal=$id_sucursal AND id_bodega=$filtro_bodega AND id_empresa=$id_empresa
											AND (id in(SELECT $idTablaPrincipal FROM $tablaInventario WHERE activo=1) )";//WHERE DE LA CONSULTA A LA TABLA "$TableName" SI SE REQUIERE EL PROVEEDOR AÑADIR ND proveedor!=''
			$grilla->OrderBy			= 'id DESC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			// $grilla->Ancho		 	= $CualAncho;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 	= $CualAlto;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 195;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'fecha_inicio,consecutivo,documento_solicitante,nombre_solicitante,area_solicitante';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstado'.$opcGrillaContable.'_[id]" /></center>','40');
			$grilla->AddRow('Consecutivo','consecutivo',70);
 			$grilla->AddRow('Solicitante','nombre_solicitante',200);
 			$grilla->AddRow('Area Solicitante','area_solicitante',200);
 			$grilla->AddRow('Unidades','pendientes_facturar',55);
			$grilla->AddRowImage('Autorizada','<center><img src="../personal/images/[autorizado].png"><center>',70);
			$grilla->AddRow('Fecha','fecha_inicio',170,'fecha');

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
		function Editar_<?php echo $opcGrillaContable; ?>(id){
			var direccionRender = 'requisicion/grillaContableBloqueada.php'
			,	estado          = document.getElementById('imgEstado<?php echo $opcGrillaContable; ?>_'+id).getAttribute('src');

        	titulo='Requisicion de compra';
        	if(estado == 'img/estado_doc/0.png'){ direccionRender = 'requisicion/grillaContable.php'; }

	        Ext.get("contenedor_<?php echo $opcGrillaContable; ?>").load({
				url     : direccionRender,
				scripts : true,
				nocache : true,
				params  :
				{
					id_documento      : id,
					opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
					filtro_bodega     : '<?php echo $filtro_bodega; ?>',
				}
			});

			//si opcgrillacontable no es facturaVenta, entonces se va a cargar unm pedido o cotizacion, tomamos entonces el consecutivo y lo ponemos para el titulo



		    consecutivoDoc = document.getElementById('div_<?php echo $opcGrillaContable; ?>_consecutivo_'+id).innerHTML;

		    if(consecutivoDoc != '' && estado != 'img/estado_doc/3.png'){ document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML = titulo+'<br>N. '+consecutivoDoc; }
			else if(consecutivoDoc != '' && estado == 'img/estado_doc/3.png'){  document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML='<span style="color:red;font-size:18px;font-weight: bold;}">'+titulo+'<br>N. '+consecutivoDoc+'</span>';}
			else{ document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML=''; }

	 		Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>.close();
		}

	</script>

<?php
} ?>




