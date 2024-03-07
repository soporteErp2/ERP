<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	include("../../config_var_global.php");
	include("../../../../../misc/MyGrilla/class.MyGrilla.php");

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
			$grilla->GrillaName	 		= $opcGrillaContable;  						 //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tablaPrincipal;							 //NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= " activo = 1 AND id_empresa=$id_empresa
											AND (id in(SELECT $idTablaPrincipal FROM $tablaInventario WHERE activo=1) OR consecutivo>0)"; //WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'id DESC';								 //LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';									 //LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize 		= 'true';
			$grilla->QuitarAncho		= 145;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 195;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" style="cursor:pointer" width="16" height="16" id="imgEstado'.$opcGrillaContable.'_[id]" /></center>','50');
			$grilla->AddRow('Sucursal', 'sucursal',120);
			$grilla->AddRow('Fecha Documento', 'fecha_documento',100);
			$grilla->AddRow('Fecha Diferido', 'fecha_diferidos',100);
			$grilla->AddRow('Consecutivo', 'consecutivo',120);
			$grilla->AddRow('Documento Usuario', 'documento_usuario',120);
			$grilla->AddRow('Usuario', 'nombre_usuario',250);

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
	    //alert("<?php echo $_SERVER['SERVER_NAME']; ?>");
		function Editar_<?php echo $opcGrillaContable; ?>(id){
			var direccionRender = 'amortizaciones/amortizacion/bd/grillaContableBloqueada.php'
			,	estado          = document.getElementById('imgEstado<?php echo $opcGrillaContable; ?>_'+id).getAttribute('src');

        	if(estado == 'img/estado_doc/0.png'){ direccionRender = 'amortizaciones/amortizacion/grillaContable.php'; }

	        Ext.get("contenedor_<?php echo $opcGrillaContable; ?>").load({
				url     : direccionRender,
				scripts : true,
				nocache : true,
				params  :
				{
					id_documento  : id,
					opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
				}
			});

			consecutivoDoc = document.getElementById('div_<?php echo $opcGrillaContable; ?>_consecutivo_'+id).innerHTML;

	 		if(consecutivoDoc != ''){ document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML = 'Amortizacion<br>N. '+consecutivoDoc; }
			else{ document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML='' }

			Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>.close();
		}

	</script>

<?php
} ?>




