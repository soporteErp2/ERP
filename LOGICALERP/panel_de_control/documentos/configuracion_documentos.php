<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa = $_SESSION['EMPRESA'];

	/**//////////////////////////////////////////////**/
	/**///		 		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/																						/**/
	/**/					 $grilla = new MyGrilla();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 	= 'Documentos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName		= 'configuracion_documentos_erp';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "id_empresa = '$id_empresa' AND id_sucursal='$filtro_sucursal'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit		= '0,50';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 	= 'false';		//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 510;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 			= 300;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar						= 'true';								//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda			= 'nombre,grupo,tipo';	//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;									//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Documento','nombre',200);
			$grilla->AddRow('','tipo',50);
			$grilla->AddRowImage('Dise&ntilde;ar','<center><img src="../../temas/clasico/images/BotonesTabs/edit16.png" style="cursor:pointer" width="16" height="16" onclick="editor_documento(\'[tipo]\',[id]);"></center>',55);
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'false';						//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Datos Documento'; 	//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'false';						//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo			= 'false';						//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VAutoResize			= 'false';						//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 				= 400;								//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 				= 180;								//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho			= 70;									//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto			= 160;								//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll			= 'false';						//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';							//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'false';						//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///								INICIALIZACION DE LA GRILLA	    			  ///**/
	/**/																														/**/
	/**/		$grilla->Link = $link;  			//Conexion a la BD				/**/
	/**/		$grilla->inicializa($_POST);	//Variables POST					/**/
	/**/		$grilla->GeneraGrilla(); 			//Inicializa la Grilla		/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>

<?php if(!isset($opcion)){?>
	<script>
		function Editar_Documentos(){}
		function editor_documento(tipo,id){
			var tipo_documento = document.getElementById('div_Documentos_nombre_'+id).innerHTML
			,	url            = (tipo == 'CE')? "disenador_formatos/index.php" : "documentos/documento_Editor.php"
			// ,	url            = "documentos/documento_Editor.php"
			,	myanchoVS      = Ext.getBody().getWidth()
			,	myaltoVS       = Ext.getBody().getHeight();

			win_editor = new Ext.Window({
				title				: 'Editor de Documentos',
				id					: 'ventana_edit_documento',
				iconCls			: 'pie2',
				width 			: myanchoVS-25,
				height 			: myaltoVS-35,
				modal				: true,
				autoDestroy : true,
				draggable		: true,
				resizable		: true,
				bodyStyle   : 'background-color:#DFE8F6;',
				autoLoad		:	{
												url			: url,
												scripts	: true,
												nocache	: true,
												params  :	{
																		myalto         : myaltoVS,
																		myancho        : myanchoVS,
																		id_sucursal    : '<?php echo $filtro_sucursal ?>',
																		tipo_documento : tipo_documento,
																		id_documento   : id
																	}
											},
				tbar				:	[
												{
													xtype			: 'button',
													text			: 'Guardar Formato',
													scale			: 'large',
													iconCls		: 'guardar',
													iconAlign	: 'left',
													handler 	: function(){ guardarBodydocumento(); }
												}
											]
			}).show();
		}
  </script>
<?php } ?>
