<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../misc/MyGrilla/class.MyGrilla.php");
	
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/	
	
	//echo $ID."--";
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'EmpleadoInventario';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'inventarios';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE 
			$grilla->MyWhere			= 'activo = 1  AND id_usuario_encargado='.$id_empleado_adjuntar_equipo;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA	
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->Ancho		 	= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 250;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo, nombre_equipo, empresa, sucursal, ubicacion';			//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA    
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',100);
			$grilla->AddRow('Equipo','nombre_equipo',200);
			$grilla->AddRow('Empresa','empresa',180);
			$grilla->AddRow('Sucursal','sucursal',180);
			$grilla->AddRow('Bodega','ubicacion',180);

			
			//$grilla->AddRowImage('Inventariado','<center><div style="float:left; margin: 0 0 0 7px"><img src="images/2/[inventariado].png?v1.1" style="cursor:pointer" width="16" height="16" onclick=""></div></center>',80);
			
			$grilla->AddBotton('Asignar Inventario','addequipo','asignar_inventario()');
			$grilla->AddBotton('Ver Acta','genera','ver_acta_asignacion(id)');
			//$grilla->AddBotton('Regresar','regresar','Win_Ventana_inventario_proceso_realizado .close(id)');

		
			$grilla->FContenedorAncho		= 500;
			$grilla->FColumnaGeneralAncho	= 250;	
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 250;
			$grilla->FColumnaFieldAncho		= 25;			
			
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL  
			$grilla->TituloVentana		= 'Ventana Inventario'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Inventario'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addequipo';	//IMAGEN CSS DEL BOTON
			//$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 560;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VAlto		 	= 570;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho		= 540;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 20;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'false';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			//$grilla->AddBotton('Inventario Prestados','doc_nuevo','alert("En desarrollo")');
			//$grilla->AddBotton('Inventario por Devolver','doc_nuevo','alert("En desarrollo")');
		
		
		//BOTONES ADICIONALES EN EL TOOLBAR DE LA VENTANA DE INSERT DELETE Y UPDATE
 			//$grilla->AddBottonVentana('Eliminar','eliminar','ventana_eliminar_campo_inventario()','false','true');	
		
		
		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false'; 		
		
		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			$grilla->AddMenuContext('Eliminar','delete','Eliminar_inventario_empleado([id])');

			


			
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/	
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


?>
<div id="eliminar_inventario_empleado"></div>
<script>
	
	function ver_acta_asignacion(){

		datos='id_empleado_inventario='+id_empleado_adjuntar_equipo;

		window.open('../informes/informes/personal/personal_adjuntar_equipo.php?'+datos);	
	}

	function asignar_inventario(){
		var myalto  = Ext.getBody().getHeight();
		var myancho  = Ext.getBody().getWidth();
		Win_Ventana_nueva_asignacion_equipo_empleado = new Ext.Window
		(
			{
				width		: myancho-500,
				id			: 'Win_Ventana_nueva_asignacion_equipo_empleado ',
				height		: myalto - 100,
				title		: 'Asignar inventarios',
				bodyStyle	: 'backgraund:#ffffff',
				modal		: true,
				autoScroll	: false,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'agregar_equipo_empleado.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
							id_empleado_adjuntar_equipo	: id_empleado_adjuntar_equipo
							}
				}
			}
		).show();	
		
	}

	function Editar_EmpleadoInventario(){


	}

	function Eliminar_inventario_empleado(id){
		Ext.get("eliminar_inventario_empleado").load
		(
			{
				url		:	"bd/bd.php",
				scripts	:	true,
				nocache	:	true,
				params	:
					{	
						id_empleado		:0,
						id_inventario	:id,
						op 				:"actualizar_inventario_empleado"
					}
			}
		);	
	}


</script>							
							
