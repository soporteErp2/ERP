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
 			$grilla->GrillaName	 		= 'Objetivos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
  		//QUERY
			$grilla->TableName			= 'crm_objetivos';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS 
 			$grilla->MyWhere			= 'activo = 1 AND id_cliente = '.$id_cliente;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA	 
		//TAMANO DE LA GRILLA 
			//$grilla->Ancho		 	= ;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false' 
			//$grilla->Alto		 		= ;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false' 
			$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true' 
			$grilla->QuitarAlto			= 270;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true' 
		//TOOLBAR Y CAMPO DE BUSQUEDA 
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES 
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
 			$grilla->VBotonNText		= 'Nuevo Proyecto'; //TEXTO DEL BOTON DE NUEVO REGISTRO 
			$grilla->VBotonNImage		= 'proyecto';			//IMAGEN CSS DEL BOTON 
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'objetivo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->Gfilters			= 'true';
			$grilla->GfiltersAutoOpen	= 'true';
			$grilla->AddFilter('Prioridad','prioridad','prioridad');
			$grilla->AddFilter('Fase','id_estado','estado_proyecto');
			$grilla->AddFilter('Probabilidad de Exito','probabilidad_exito','probabilidad_exito');			

 			/*$grilla->AddBotton('Nueva Tarea'	,'tareas','');
			$grilla->AddBotton('Nueva Llamada'	,'llamadas','');
			$grilla->AddBotton('Nueva Cita'		,'citas','');*/
		//BOTONES ADICIONALES EN EL TOOLBAR PRINCIPAL DE LA GRILLA
 		//COLUMNAS DE LA GRILLA
			$grilla->AddRowImage('','<center><img src="../crm/images/prioridades/prioridad_[prioridad].png" style="" width="16" height="16" onclick=""></center>','32','');
			$grilla->AddRowImage('estado','<center><img src="../crm/images/[estado].png" style="cursor:pointer" width="16" height="16" onclick=""></center>','45','');
			$grilla->AddRow('N. Proyecto','id','70','');
			$grilla->AddRow('Tipo','tipo_proyecto','150','');
			$grilla->AddRow('Linea','linea_negocio','150','');	
			$grilla->AddRow('Fase','estado_proyecto','100','');				
			$grilla->AddRowImage('exito','<center><img src="../crm/images/probabilidad_[probabilidad_exito].png" style="" width="16" height="16" onclick=""></center>','32','');
 			$grilla->AddRowImage('Actividades','<center>[acciones]</center>','75','');
			$grilla->AddRowImage('Acciones','<center>[acciones]</center>','55','');
			$grilla->AddRow('Proyecto','objetivo','300','');
			$grilla->AddRow('Valor','valor','90','moneda');
			$grilla->AddRow('Creado por','usuario','200','');
			$grilla->AddRow('Fecha Creacion','fecha_creacion','110','');
			$grilla->AddRow('Actualizacion','fecha_actualizacion','110','');
			$grilla->AddRow('Vencimiento','vencimiento','110','');
			$grilla->AddRow('Observaciones','observacion','250','');
			//$grilla->AddRowImage('Docs','<center><img src="../crm/images/doc.png" style="cursor:pointer" title ="Subir documentos" width="16" height="16" onclick="documentosProyectos([id]);"></center>','35','');
			//$grilla->AddRowImage('','<center><img src="../crm/images/edit.png" style="cursor:pointer" width="16" title ="Editar proyecto" height="16" onclick="modificarProyecto([id]);"></center>','35','');
		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';  		// BOTON ELIMINAR EN MENU CONTEXTUAL 
		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			$grilla->AddMenuContext('Modificar Objetivo o Proyecto','edit16','modificarProyecto([id])');	 		
	 		$grilla->AddMenuContext('Actividades','tareas16','CRM("personalizado","[id]")');
 			$grilla->AddMenuContext('Finalizar Objetivo o Proyecto','ok16','FinalizaObjetivo([id])');
 			$grilla->AddMenuContext('Gestionar Documentos','doc','documentosProyectos([id])'); 			
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
 		function Agregar_Objetivos(){

 			var myalto  = Ext.getBody().getHeight();
			var myancho  = Ext.getBody().getWidth();

		 	Win_Agrega_Objetivos = new Ext.Window({
				id			: 'Win_Agrega_Objetivos',
				width		: 625,// myancho-100,
				height		: 380,
				//boxMaxHeight: 350,
				//title		: 'Agrega Objetivo' ,
				//iconCls 	: 'proyecto16',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				border		: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'../crm/objetivos_agregar.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
								id_cliente  : 	'<?php echo $id_cliente ?>'
							}
				}
			}).show();
 		}

 		function Editar_Objetivos(id){

 			CRM('personalizado',id); 			
 			
 		}

 		function modificarProyecto(id){
 			var myalto  = Ext.getBody().getHeight();
			var myancho  = Ext.getBody().getWidth();

		 	Win_Agrega_Objetivos = new Ext.Window({
				id			: 'Win_Agrega_Objetivos',
				width		: 625,// myancho-100,
				height		: 380,
				//boxMaxHeight: 350,
				//title		: 'Agrega Objetivo' ,
				//iconCls 	: 'proyecto16',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				border		: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'../crm/objetivos_agregar.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
								id_cliente  : 	'<?php echo $id_cliente ?>',
								id_objetivo :   id
							}
				}
			}).show();
 		}

 		function documentosProyectos(id){
 			var myalto  = Ext.getBody().getHeight();
 			var myancho = Ext.getBody().getWidth();
 			
 			Win_ventanaDocumentosProyectos = new Ext.Window({
 			    width       : 500,
 			    height      : 300,
 			    id          : 'Win_ventanaDocumentosProyectos',
 			    title       : '',
 			    modal       : true,
 			    autoScroll  : false,
 			    closable    : false,
 			    autoDestroy : true,
 			    autoLoad    :
 			    {
 			        url     : 'upload_files/documentos_adjuntos.php',
 			        scripts : true,
 			        nocache : true,
 			        params  :
 			        {
 			            id  : id,
 			            opc : 'Proyectos' 			            
 			        }
 			    }, 			    
 			}).show();
 		}

 	</script>

 <?php } ?>