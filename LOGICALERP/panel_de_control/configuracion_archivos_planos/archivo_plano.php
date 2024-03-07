<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE  	  ///**/
	/**/											                      /**/
	/**/	         $grilla = new MyGrilla();				/**/
	/**/											                      /**/
	/**//////////////////////////////////////////////**/

	$idEmpresa  = $_SESSION['EMPRESA'];

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'archivos_planos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_archivos_planos';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			  = "activo = 1 AND id_empresa = '$idEmpresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		  = 400;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		    = 320;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			  = 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda	= 'nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Nombre','nombre',200);
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		  = 270;
			$grilla->FColumnaGeneralAncho	= 270;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 100;
			$grilla->FColumnaFieldAncho		= 160;
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		  = 'true';			                //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana	  = 'Archivo Plano';            //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones	  = 'false';			                //SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		  = 'false';			                //SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		  = 'Nuevo<br/>Archivo Plano'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		  = 'add';			                //IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		  = 'false';			              //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		    = 320;				                //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		    = 180;				                //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		  = 70;				                  //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		  = 200;				                //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		  = 'false';			              //SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar	  = 'false';			                //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'false';			                //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Nombre','nombre',150,'true','false');
			$grilla->AddValidation('nombre','mayuscula');
			$grilla->AddComboBox('Tipo Cuenta Bancaria','tipo_cuenta_bancaria',150,'true','false','S:Ahorros,D:Corriente,C:Contable');
			$grilla->AddTextField('Numero Cuenta Bancaria','numero_cuenta_bancaria',150,'true','false');
			$grilla->AddValidation('numero_cuenta_bancaria','numero');
			$grilla->AddTextField('','id_empresa',150,'true','true',$idEmpresa);
			$grilla->AddTextField('','activo',150,'true','true','1');
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///				         INICIALIZACION DE LA GRILLA	  		 	  ///**/
	/**/															                              /**/
	/**/	     $grilla->Link = $link;  	    //Conexion a la BD			/**/
	/**/	     $grilla->inicializa($_POST); //Variables POST			  /**/
	/**/	     $grilla->GeneraGrilla(); 	  //Inicializa la Grilla	/**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>
	<script>
		var styleGrilla = document.getElementById('ContenedorPrincipal_archivos_planos').getAttribute('style');
		document.getElementById('ContenedorPrincipal_archivos_planos').setAttribute('style', styleGrilla+'; margin-top:10px;')
	</script>
<?php
} ?>
