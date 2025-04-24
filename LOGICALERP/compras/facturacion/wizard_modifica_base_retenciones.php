<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	     $grilla = new MyGrilla();			/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa    = $_SESSION['EMPRESA'];
	$grupo_empresa = $_SESSION['GRUPOEMPRESARIAL'];

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	= 'modifica_base_retencion';  //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName	= 'compras_facturas_retenciones';			  												 				//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere	= "id_factura_compra = $id_factura_compra AND activo = 1"; 				//WHERE DE LA CONSULTA A LA TABLA "$TableName"

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 = 'false';		//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 = 450;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 = 200;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			        = 'true';							//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda				= 'retencion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda   = '' ;								//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Retencion','retencion',200);
			$grilla->AddRow('%','valor',50);
			$grilla->AddRow('Base','base',70);
			$grilla->AddRow('Base modificada','base_modificada',70);
			$grilla->AddColStyle('valor','text-align:right; width:45px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 340;
			$grilla->FColumnaGeneralAncho	= 320;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 160;
			$grilla->FColumnaFieldAncho		= 150;
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'true';							//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Asistente modificar base retenible'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VAutoResize			= 'false';						//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 				= 400;								//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 				= 200;								//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll			= 'true';						//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';							//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';							//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Retencion','retencion',150,'true','false');
			$grilla->AddTextField('% Valor','valor',150,'true','false');
			$grilla->AddTextField('Base retenible','base_modificada',150,'true','false');
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///				     INICIALIZACION DE LA GRILLA	  		        ///**/
	/**/															                              /**/
	/**/		$grilla->Link = $link;  			//Conexion a la BD			  /**/
	/**/		$grilla->inicializa($_POST);	//Variables POST			    /**/
	/**/		$grilla->GeneraGrilla(); 			//Inicializa la Grilla		/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
