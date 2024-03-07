<?php 
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**////		 INICIALIZACION DE LA CLASE  	 ////**/
	/**///										  ///**/
	/**/	      $grilla = new MyGrilla();			/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
 			$grilla->GrillaName	 		= 'GrillaAcciones';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
  		//QUERY
			$grilla->TableName			= 'crm_objetivos_actividades_acciones';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS 
 			$grilla->MyWhere			= 'activo = 1 AND id_objetivo = '.$id_objetivo.' AND id_actividad = '.$id_actividad;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA	 
		//TAMANO DE LA GRILLA 
			$grilla->Ancho		 		= 570;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false' 
			$grilla->Alto		 		= 150;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false' 
			//$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true' 
			//$grilla->QuitarAlto			= 270;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true' 
		//TOOLBAR Y CAMPO DE BUSQUEDA 
			$grilla->VBarraBotones		= 'false';			//SI HAY O NO BARRA DE BOTONES 
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
 			$grilla->VBotonNText		= 'Nueva Tarea'; //TEXTO DEL BOTON DE NUEVO REGISTRO 
			$grilla->VBotonNImage		= 'tareas';			//IMAGEN CSS DEL BOTON 
			$grilla->Gtoolbar			= 'false';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'id,nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
 		//BOTONES ADICIONALES EN EL TOOLBAR PRINCIPAL DE LA GRILLA
 		//COLUMNAS DE LA GRILLA
 			$grilla->AddRow('Accion','accion','200','');
 			$grilla->AddRow('Usuario','usuario','140','');
			$grilla->AddRow('Fecha','fecha','110','');
		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';  		// BOTON ELIMINAR EN MENU CONTEXTUAL 
		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			$grilla->AddMenuContext('Ver Detalles de la Accion Realizada','informe0','Editar_GrillaAcciones([id])');
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
 			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
   			$grilla->TituloVentana		= 'Ventana de Prueba'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
 			//$grilla->VAncho		 		= ;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false' 
			//$grilla->VAlto		 		= ;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
 			$grilla->VQuitarAncho		= 50;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
 			$grilla->VQuitarAlto		= 50;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
 			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
 			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
 			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
 		//BOTONES ADICIONALES EN EL TOOLBAR DE LA VENTANA DE INSERT DELETE Y UPDATE
 
 	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
 	/**//////////////////////////////////////////////////////////////**/
 	/**////				INICIALIZACION DE LA GRILLA	  			 ////**/
 	/**///														  ///**/
 	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
 	/**/	$grilla->inicializa($_POST);//variables POST			/**/
 	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
 	/**/															/**/
 	/**//////////////////////////////////////////////////////////////**/

 ?>

 <script>
	 function Editar_GrillaAcciones(id){
		 	Win_Muestra_Accion = new Ext.Window({
				id			: 'Win_Muestra_Accion',
				width		: 300,
				height		: 150,
				title		: 'Informacion de la Accion Realizada' ,
				iconCls 	: '1',
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'../crm/acciones/accion_info.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
								id : id
							}
				}
			}).show();	

	 }
 </script>