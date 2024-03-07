<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= $nombre_grilla;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'empleados';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= "activo = 1 AND id_empresa=".$_SESSION['EMPRESA']." $sql"; //.$condicional;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 300;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 200;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'documento, nombre, direccion, pais, departamento, ciudad';

            $grilla->Gfilters			= 'false';
			$grilla->GfiltersAutoOpen	= 'false';
			$grilla->AddFilter('Pais','id_pais','pais');
			$grilla->AddFilter('Tipo de Tercero','tipo','tipo');
			$grilla->AddFilter('Tipo de Documento','id_tipo_identificacion','tipo_identificacion');

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Identificacion','documento',100); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('Nombre','nombre',300);
			$grilla->AddRowImage('Cargo','<span id="cargo_[id]">[cargo]</span><span style="display:none;" id="id_cargo_[id]">[id_cargo]</span><span style="display:none;" id="email_[id]">[email_empresa]</span>',150);
			$grilla->AddRow('Cargo','cargo',150);
			$grilla->AddRow('Sucursal','sucursal',150);
			$grilla->AddRow('id','idv',0);


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 760;
			$grilla->FColumnaGeneralAncho	= 380;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 150;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana			= 'Clientes'; 		//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar	= 'false';
			$grilla->VBarraBotones			= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo			= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			//$grilla->VBotonNText		= 'Agregar Cliente'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			//$grilla->VBotonNImage		= 'addcliente';		//IMAGEN CSS DEL BOTON
			//$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->VAncho		 		= 800;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VAlto		 		= 570;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			//$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			//$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			//$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			//$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DEL MENU CONTEXTUAL
 			//$grilla->MenuContext		= 'true';
	 		//$grilla->MenuContextEliminar= 'true';

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			//$grilla->AddMenuContext('Eliminar','delete','Eliminar_inventario_empleado([id])');

 		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

   // echo '<script>alert("'.$grilla->MyWhere.'")</script>';

if(!isset($opcion)) {?>

	<script>

		function Editar_<?php echo $nombre_grilla; ?>(id){
			<?php echo $cargaFuncion; ?>
		}

	</script>


<?php
} ?>