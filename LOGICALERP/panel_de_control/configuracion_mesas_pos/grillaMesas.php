<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$id_pais    = $_SESSION['PAIS'];

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'ventas_pos_mesas';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'ventas_pos_mesas';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= " activo = 1 AND id_empresa = '$id_empresa' AND id_seccion=$id_seccion";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 	= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 410;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 280;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 750;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 210;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			// $grilla->AddRow('id','id',50);
			$grilla->AddRow('Codigo','codigo',50);
			$grilla->AddRow('Nombre','nombre',150);
			$grilla->AddRow('Estado','estado',100);
			// $grilla->AddRowImage('Bodegas','<div style="float:left; margin:0 0 0 10px; cursor:pointer" onClick="ventana_sucursal_bodega([id])"><div style="float:left"><img src="../../temas/clasico/images/BotonesTabs/sucursales16.png" ></div><div style="float:left">&nbsp;([bodegas])</div></div>',65)	;

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 75;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Administracion mesas'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			// $grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			// $grilla->VBotonNuevo		= $btnNuevo;			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			// $grilla->VBotonNText		= 'Nueva Sucursal'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			// $grilla->VBotonNImage		= 'addsucursal';			//IMAGEN CSS DEL BOTON
			// // $grilla->AddBotton('Estructura','sucursal','VentanaEstructuraEmpresa();');
			// $grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 300;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 190;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			// $grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			// $grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Codigo','codigo',180,'true','false');
			$grilla->AddTextField('Nombre','nombre',180,'true','false');
			$grilla->AddComboBox('Estado','estado',180,'false','false','Acitva:Activa,Inactiva:Inactiva');

			$grilla->AddTextField('','id_empresa',200,'false','hidden', $id_empresa);
			$grilla->AddTextField('','id_seccion',200,'false','hidden', $id_seccion);
			$grilla->AddTextField('','seccion',200,'false','hidden', $seccion);
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

	</script>
<?php
}
else if($opcion =='Vupdate' || $opcion == 'Vagregar'){  ?>
	<script>

    </script>

<?php } ?>