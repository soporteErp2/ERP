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
			$grilla->GrillaName	 		= 'panelControlPlantillas';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'plantillas';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 560;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 365;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 80;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'descripcion,referencia';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Descripcion','descripcion',250);
			$grilla->AddRow('Modulo','referencia',80);
			$grilla->AddRowImage('','<center><div style="float:left; margin: 0 0 0 7px"><img src="../../../temas/clasico/images/BotonesTabs/config16.png?" style="cursor:pointer" width="16" height="16" onclick="configPlantilla([id]);"></div></center>',30);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho     = 300;
			$grilla->FColumnaGeneralAncho = 290;
			$grilla->FColumnaGeneralAlto  = 25;
			$grilla->FColumnaLabelAncho   = 60;
			$grilla->FColumnaFieldAncho   = 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto     = 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana   = 'Plantilla'; 	//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones   = 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo     = 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText     = 'Nueva Plantilla'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage    = 'add';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize     = 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho          = 310;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto           = 160;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho    = 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto     = 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll     = 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar  = 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar = 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('','id_empresa',200,'true','true',$id_empresa);
			$grilla->AddTextField('Descripcion','descripcion',200,'true','false');
			$grilla->AddComboBox('Referencia','referencia',200,'true','false','Venta:Venta,Compra:Compra');


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

		function configPlantilla(id){
			var estadoCuenta = document.getElementById('div_panelControlPlantillas_referencia_'+id).innerHTML;
			var myalto  = Ext.getBody().getHeight()
			,	myancho = Ext.getBody().getWidth()
			,	titulo  = 'Plantilla '+document.getElementById('div_panelControlPlantillas_referencia_'+id).innerHTML+' '+document.getElementById('div_panelControlPlantillas_descripcion_'+id).innerHTML;

			Win_Ventana_configPlantillas = new Ext.Window({
				width		: 700,
				height		: 445,
				id			: 'Win_Ventana_configPlantillas',
				title		: titulo,
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'plantillas/config_plantilla.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						idPlantilla  : id,
						estadoCuenta : estadoCuenta
					}
				}
			}).show();
		}
    </script>

<?php } ?>