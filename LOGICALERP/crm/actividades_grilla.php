<?php 
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");
	
	/**//////////////////////////////////////////////**/
	/**////		 INICIALIZACION DE LA CLASE  	 ////**/
	/**///										  ///**/
	/**/	      $grilla = new MyGrilla();			/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
 			$grilla->GrillaName	 		= 'Actividades';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
  		//QUERY
			$grilla->TableName			= 'crm_objetivos_actividades';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS 
 			$grilla->MyWhere			= 'activo = 1 AND id_objetivo = 0 AND id_cliente = '.$id_cliente;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA	 
		//TAMANO DE LA GRILLA 
			//$grilla->Ancho		 	= ;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false' 
			//$grilla->Alto		 		= ;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false' 
			$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true' 
			$grilla->QuitarAlto			= 270;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true' 
		//TOOLBAR Y CAMPO DE BUSQUEDA 
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES 
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
 			$grilla->VBotonNText		= 'Nueva Actividad'; //TEXTO DEL BOTON DE NUEVO REGISTRO 
			$grilla->VBotonNImage		= 'actividad';			//IMAGEN CSS DEL BOTON 
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'objetivo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
 			/*$grilla->AddBotton('Nueva Tarea'	,'tareas','');
			$grilla->AddBotton('Nueva Llamada'	,'llamadas','');
			$grilla->AddBotton('Nueva Cita'		,'citas','');*/
		//BOTONES ADICIONALES EN EL TOOLBAR PRINCIPAL DE LA GRILLA
 		//COLUMNAS DE LA GRILLA
			$grilla->AddRowImage('Tipo','<div style="float:left; width:16px; height:16px; margin: 0 5px 0 0;"><img src="../calendario/images/t[icono]B.png" style="cursor:pointer" width="16" height="16" onclick=""></div><div style="float:left; width:40px">[tipo_nombre]</div>','180','');
			$grilla->AddRow('Tema','tema','250','');
			$grilla->AddRowImage('Estado','<center><img src="../crm/images/[estado].png" style="cursor:pointer" width="16" height="16" onclick=""></center>','40','');
			$grilla->AddRowImage('Acciones','<center>[acciones]</center>','55',''); 			
			$grilla->AddRow('Creado por','usuario','250','');
			$grilla->AddRow('Asignado a','asignado','250','');
			$grilla->AddRow('Fecha de Creacion','fecha','130','');			
			$grilla->AddRow('Fecha de Vencimiento','fecha_actividad','130','');
			//$grilla->AddRowImage('','<input id="tipo_[id]" type="hidden" value="[tipo]" />','0.00001','');
		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';  		// BOTON ELIMINAR EN MENU CONTEXTUAL 
		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
	 		//$grilla->AddMenuContext('Actividades','tareas16','CRM("personalizado","[id]")');
 			$grilla->AddMenuContext('Finalizar Actividad','ok16','FinalizaActividad([id],"Actividades");');
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
 			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
   			$grilla->TituloVentana		= 'Ventana de Prueba'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
 			$grilla->VAncho		 		= 300;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false' 
			$grilla->VAlto		 		= 200;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
 			//$grilla->VQuitarAncho		= 50;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
 			//$grilla->VQuitarAlto		= 50;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
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
 
 
 if(!isset($opcion)){//FUNCIONES PARA LA GRILLA
 ?>

 	<script>
		
					
 		function Agregar_Actividades(){
			VentanaActi1 = 1; VentanaActi2 = 0;
 			Agregar_ActividadesTareas(0);	
 		}

 		function Editar_Actividades(id){
			VentanaActi1 = 1; VentanaActi2 = 0;
 			Editar_ActividadesTareas(id);	
 		}

 	</script>

 <?php } ?>