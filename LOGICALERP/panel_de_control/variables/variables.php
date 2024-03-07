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

	$id_empresa = $_SESSION['EMPRESA'];
	
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName		= 'Variables';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName		= 'variables';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere		= "id_grupo='$id_grupo'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit		= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 	= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 	= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 	= 360;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho	= 210;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto		= 340;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar       = 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda = 'nombre,detalle,campo,tabla,grupo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
		
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Variable','nombre',200);
			$grilla->AddRow('Detalle','detalle',200);
			$grilla->AddRow('Tabla','tabla',150);
			$grilla->AddRow('Campo','campo',150);
			
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 100;
			$grilla->FColumnaFieldAncho		= 180;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto	= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana	= ''; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones	= 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VAutoResize	= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 	= 330;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 	= 140;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho	= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto	= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll	= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar	= 'false';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar= 'false';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION


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
	<script type="text/javascript">

		function Agregar_Variables(){
			var myalto2  = Ext.getBody().getHeight();
			var myancho2 = Ext.getBody().getWidth();
			Win_Editar_Variable = new Ext.Window
			(
				{
					width		: 390,
					id			: 'Win_Editar_Variable',
					height		: 250,
					title		: 'Edicion de Variables',
					modal		: true,
					autoScroll	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'variables/agregar_variable.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							id_grupo : '<?php echo $id_grupo ?>'
						}
					},
					tbar		:
					[
						{
							xtype		: 'button',
							text		: 'Guardar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'left',
							handler 	: function(){guardaVariable()}
						}
					]
				}
			).show();
		}

		function Editar_Variables(elid){
			var myalto2  = Ext.getBody().getHeight();
			var myancho2 = Ext.getBody().getWidth();
			Win_Editar_Variable = new Ext.Window
			(
				{
					width		: 390,
					id			: 'Win_Editar_Variable',
					height		: 250,
					title		: 'Edicion de Variables',
					modal		: true,
					autoScroll	: true,
					autoDestroy : true,
					autoLoad	:
					{
						url		: 'variables/agregar_variable.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							opcion : 'actualizar',
							id     : elid
						}
					},
					tbar		:
					[
						{
							xtype		: 'button',
							text		: 'Guardar',
							scale		: 'large',
							iconCls		: 'guardar',
							iconAlign	: 'left',
							handler 	: function(){guardaVariable()}
						},
						{
							xtype		: 'button',
							//id			: 'btn2',
							text		: 'Eliminar',
							scale		: 'large',
							iconCls		: 'eliminar',
							iconAlign	: 'left',
							handler 	: function(){eliminaVariable()}
						}
					]
				}
			).show();
		}
</script>
<?php } ?>