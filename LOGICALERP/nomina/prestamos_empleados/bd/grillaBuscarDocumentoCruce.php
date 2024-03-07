<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa=$_SESSION['EMPRESA'];

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	if ($tipo_documento=='RC') {
		$tabla_buscar='recibo_caja';
	}
	else if ($tipo_documento='NC') {
		$tabla_buscar='nota_contable_general';
	}

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'buscar_documento_cruce';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= $tabla_buscar;			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = $id_empresa  AND estado=1 AND id_sucursal=$filtro_sucursal ";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA


		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 470;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 350;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 80;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto			= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'consecutivo,tercero';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			if ($tipo_documento=='RC') {
				$grilla->AddRow('Fecha','fecha_recibo',80);
				$grilla->AddRow('Consecutivo','consecutivo',70);
				$grilla->AddRow('tercero','tercero',200);
			}
			else if ($tipo_documento='NC') {
				$grilla->AddRow('Fecha','fecha_nota',80);
				$grilla->AddRow('Consecutivo','consecutivo',70);
				$grilla->AddRow('tercero','tercero',200);
			}


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 60;
			$grilla->FColumnaFieldAncho		= 200;
			//

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
		$grilla->VentanaAuto        = 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
		// $grilla->TituloVentana   = 'Administracion Sucursal'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
		$grilla->VBarraBotones      = 'false';			//SI HAY O NO BARRA DE BOTONES
		$grilla->VBotonNuevo        = 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
		// $grilla->VBotonNText     = 'Seleccionar Todos'; //TEXTO DEL BOTON DE NUEVO REGISTRO
		// $grilla->VBotonNImage    = 'reunionadd';			//IMAGEN CSS DEL BOTON
		// $grilla->AddBotton('Regresar','regresar','Win_Ventana_buscar_documento_cruce.close();');
		// $grilla->VAutoResize     = 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
		// $grilla->VAncho          = 310;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
		// $grilla->VAlto           = 140;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
		// $grilla->VQuitarAncho    = 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
		// $grilla->VQuitarAlto     = 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
		// $grilla->VAutoScroll     = 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
		// $grilla->VBotonEliminar  = 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
		// $grilla->VComporEliminar = 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)



	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>

	<script>

		function Editar_buscar_documento_cruce(id){
			document.getElementById('consecutivo_documento').value=document.getElementById('div_buscar_documento_cruce_consecutivo_'+id).innerHTML;
			document.getElementById('id_documento').value=id;
			Win_Ventana_buscar_documento_cruce.close(id);
		}

    </script>

<?php } ?>