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

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'Contrato';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'empleados_contratos';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo = 1 AND id_sucursal='.$filtro_sucursal.' AND id_empresa='.$filtro_empresa;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 600;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 265;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 220;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto			= 198;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,id';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('Nombre del Contrato','nombre',370);
			$grilla->AddRowImage('Formato','<center><img src="../../temas/clasico/images/BotonesTabs/doc16.png" style="cursor:pointer" width="16" height="16" onclick="editor_contrato([id]);"></center>',55);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 330;
			$grilla->FColumnaGeneralAncho	= 330;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 110;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Administracion de Contratos'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Agregar Contrato'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontrato';			//IMAGEN CSS DEL BOTON
			// $grilla->AddBotton('Importar Contratos','importcontrato','importar_Contratos()');
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 370;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 140;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'false';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Nombre del Contrato','nombre',200,'true','false');
			$grilla->AddTextField('','id_empresa',200,'true','true',$_SESSION['EMPRESA']);
			$grilla->AddTextField('','id_sucursal',200,'true','true',$_SESSION['SUCURSAL']);


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

<?php
if(!isset($opcion)){
?>

	<script>
		function importar_Contratos(){

			myanchoVS = Ext.getBody().getWidth();
			myaltoVS  = Ext.getBody().getHeight();
			win_import_contratos = new Ext.Window
			(
				{
					title		: 'Importar de Documentos',
					iconCls		: 'pie2',
					width 		: myanchoVS-200,
					height 		: myaltoVS-50,
					modal		: true,
					autoDestroy : true,
					draggable	: false,
					resizable	: false,
					autoLoad:
					{
						url		:  'importar_contrato.php',
						scripts	: true,
						nocache	: true,
						params	:
								{
									id_empresa		: 	'<?php echo $filtro_empresa ?>',
									id_sucursal		: 	'<?php echo $filtro_sucursal ?>'
								}
					}
				}
			).show();
		}


		function editor_contrato(id){

			myanchoVS = Ext.getBody().getWidth();
			myaltoVS  = Ext.getBody().getHeight();
			win_editor = new Ext.Window
			(
				{
					title		: 'Editor de Documentos',
					id			: 'ventana_edit_docuemnt',
					iconCls		: 'pie2',
					width 		: myanchoVS-25,
					height 		: myaltoVS-35,
					modal		: true,
					autoDestroy : true,
					draggable	: false,
					resizable	: false,
					closable	: false,
					bodyStyle   : 'background-color:#DFE8F6;',
					autoLoad:
					{
						url		:  'contrato_Editor.php?myalto='+myaltoVS+'&myancho='+myanchoVS+'&id_contrato='+id,
						scripts	: true,
						nocache	: true
					},
					tbar		:
					[
						{
								xtype		: 'button',
								text		: 'Guardar Formato',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'left',
								handler 	: function(){guardarBodyContrato()}
						},'-',
						{
								xtype		: 'button',
								text		: 'Cerrar Editor',
								scale		: 'large',
								iconCls		: 'eliminar',
								iconAlign	: 'left',
								handler 	: function(){cerrarBodyContrato()}
						}
					]
				}
			).show();
		}
    </script>

<?php } ?>