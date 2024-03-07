<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		   INICIALIZACION DE LA CLASE  	    ///**/
	/**/																						/**/
	/**/	        $grilla = new MyGrilla();					/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 	= 'TiposIdentificacion'; 	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)

		//QUERY
			$grilla->TableName		= 'tipo_documento';														//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa'";	//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit		= '0,50';																			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize		= 'false';	//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 587;			//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		  = 260;			//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar		        	= 'false';						//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		    = 'id,nombre,tipo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda   = '';									//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Documento','nombre',80);
			$grilla->AddRow('Descripcion','detalle',200);
			$grilla->AddRow('Tipo','tipo',100);
			$grilla->AddRow('Codigo Tributario','codigo_tributario',100);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho	  	= 270;
			$grilla->FColumnaGeneralAncho	= 270;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 100;
			$grilla->FColumnaFieldAncho		= 160;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		  = 'false';									 	//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Tipos de Identificacion'; 	//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';										//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		  = 'false';										//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		  = 'Nuevo'; 										//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		  = 'add';											//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		  = 'false';										//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		    = 300;												//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		    = 230;												//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		  = 70;													//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		  = 200;												//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		  = 'false';										//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'false';										//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'false';										//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///								INICIALIZACION DE LA GRILLA	  			  	///**/
	/**/																														/**/
	/**/				$grilla->Link = $link;  	//Conexion a la BD				/**/
	/**/				$grilla->inicializa($_POST);//variables POST				/**/
	/**/				$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>
	<script>
		var styleGrilla = document.getElementById('ContenedorPrincipal_TiposIdentificacion').getAttribute('style');
		document.getElementById('ContenedorPrincipal_TiposIdentificacion').setAttribute('style', styleGrilla+'; margin-top:10px;')
	</script>
<?php
} ?>
