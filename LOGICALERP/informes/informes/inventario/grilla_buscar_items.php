<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///			 	INICIALIZACION DE LA CLASE  	  ///**/
	/**/																						/**/
	/**/					 $grilla = new MyGrilla();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName = $opcGrillaContable;  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName  = 'items';																        //NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere    = "activo = 1 AND id_empresa = $id_empresa";			//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy    = 'CAST(codigo AS  CHAR) ASC';										//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit = '0,100';																				//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize = 'true';
			$grilla->Ancho      = 370;																						//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto       = 340;																						//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
		//TOOLBAR Y CAMPO DE BUSQUEDA Y FILTROS
			$grilla->Gtoolbar = 'true';																					//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda	= 'codigo,nombre_equipo';
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',80);
			$grilla->AddRow('Nombre','nombre_equipo',170);
			$grilla->AddColStyle('codigo','text-align:right; width:75px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto     = 'false';										//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana   = 'Buscar Item'; 							//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones   = 'false';										//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo     = 'false';										//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText     = 'Nueva Reunion';  					//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage    = 'addcontactos';							//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize     = 'true';											//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho          = 400;												//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto           = 200;												//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho    = 70;													//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto     = 160;												//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll     = 'true';											//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar  = 'true';											//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar = 'true';											//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		/**//////////////////////////////////////////////////////////////**/
		/**///				       INICIALIZACION DE LA GRILLA 	  	/////**/
		/**/															/**/
		/**/	    $grilla->Link = $link;  	   //Conexion a la BD   /**/
		/**/	    $grilla->inicializa($_POST); //Variables POST		/**/
		/**/	    $grilla->GeneraGrilla(); 	  //Inicializa la Grilla/**/
		/**/															/**/
		/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>

	<script>
		function Editar_<?php echo $opcGrillaContable; ?>(id){
			var codigo = document.getElementById('div_<?php echo $opcGrillaContable; ?>_codigo_' + id).innerHTML;
			var nombre = document.getElementById('div_<?php echo $opcGrillaContable; ?>_nombre_equipo_' + id).innerHTML;
			<?php echo $funcion; ?>
		}
	</script>

<?php
} ?>
