<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 		INICIALIZACION DE LA CLASE  	  ///**/
	/**/																						/**/
	/**/					 $grilla = new MyGrilla();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	= 'Terceros_Direcciones_Email';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)

		//QUERY
			$grilla->TableName	= 'terceros_direcciones_email';							//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere		= 'activo = 1 AND id_direccion = ' . $elid;	//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit	= '0,100';																	//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	= 'true';		//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 	= 482;			//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 160;			//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar							= 'false';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda				= 'nombre,id';	//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda 	= '' ;					//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Contacto','contacto',170);
			$grilla->AddRow('Cuenta de e-mail','email',205);

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'true';																	//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Administracion de Cuentas de Correo'; 	//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';																	//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo			= 'true';																	//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText			= 'Agregar e-mail'; 											//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage			= 'addmail';															//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_ConfiguracionClientes_Direcciones_email.close();Actualiza_Div_TercerosDirecciones('.$elid.');');
			$grilla->VAutoResize			= 'false';																//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 				= 370;																		//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 				= 140;																		//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho			= 70;																			//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto			= 160;																		//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll			= 'false';																//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';																	//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';																	//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho			= 370;
			$grilla->FColumnaGeneralAncho	= 370;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 100;
			$grilla->FColumnaFieldAncho		= 220;

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('id_direccion','id_direccion',100,'true','true',$elid);
			$grilla->AddTextField('Contacto','contacto',220,'false','false');
			$grilla->AddTextField('Cuenta de e-mail','email',220,'true','false');
			$grilla->AddValidation('email','email');

		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext					= 'true';
	 		$grilla->MenuContextEliminar	= 'true';

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///							INICIALIZACION DE LA GRILLA	 			 			  ///**/
	/**/																														/**/
	/**/		$grilla->Link = $link;  			//Conexion a la BD				/**/
	/**/		$grilla->inicializa($_POST);	//variables POST					/**/
	/**/		$grilla->GeneraGrilla(); 			// Inicializa la Grilla		/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
