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

	$id_empresa    = $_SESSION['EMPRESA'];
	$grupo_empresa = $_SESSION['GRUPOEMPRESARIAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'ItemsFamilia';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'items_familia';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'codigo ASC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 220;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',60);
			$grilla->AddRow('Nombre de la Familia','nombre',250);
			$grilla->AddRowImage('','<center><div style="float:left; margin: 0 0 0 7px"><img src="../../temas/clasico/images/BotonesTabs/config16.png?" style="cursor:pointer" width="16" height="16" onclick="VentanaGrupo([id]);"></div></center>',30);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 280;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Familia Items '.$subtitulo; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Familia'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'cubos_add';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Estructura','sucursal','VentanaEstructura(id);');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 340;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 150;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			$grilla->VSqlBtnEliminar	= "SELECT id FROM items_familia_grupo WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA']." AND id_familia=".$id;	//VALIDA SI EJECUTA BTN ELIMINAR CON UNA CONSULTA SQL
			$grilla->AddBottonVentana('Grupos','cubos_add','VentanaGrupo(elid);');

		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'false';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			//$grilla->AddSeparator('Datos Familia');
			$grilla->AddTextField('Codigo:','codigo',200,'true','false');
			$grilla->AddTextField('Nombre:','nombre',200,'true','false');
			$grilla->AddTextField('','id_empresa',200,'true','true',$id_empresa);
			$grilla->AddTextField('','grupo_empresarial',150,'true','true',$grupo_empresa);

			$grilla->AddValidation('codigo','unico_global','id_empresa='.$id_empresa);



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

		function VentanaGrupo(id){
			var nombreGrupoItems = document.getElementById('div_ItemsFamilia_nombre_'+id).innerHTML;
			var myalto           = Ext.getBody().getHeight();
			var myancho          = Ext.getBody().getWidth();

			Win_Ventana_ItemsGrupo = new Ext.Window({
				width		: myancho - 110,
				id			: 'Win_Ventana_Itemgrupo',
				height		: myalto - 110,
				title		: 'Grupos de la Familia "'+nombreGrupoItems+'"',
				modal		: true,
				autoScroll	: false,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'items/items_grupos.php',
					scripts	: true,
					nocache	: true,
					params	: { id_item_familia : id }
				}
			}).show();
		}

		function VentanaEstructura(id){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			Win_Ventana_Estructura_ItemsGrupos = new Ext.Window({
				width		: 500,
				id			: 'Win_Ventana_Estructura_ItemsGrupos',
				height		: myalto - 80,
				title		: 'Estructura Grupos De Items',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'items/items_estructura.php',
					scripts	: true,
					nocache	: true,
					params	: { opc : "ver" }
				},
				tbar		:
				[
					{
						xtype		: 'button',
						width 		: 60,
						height 		: 56,
						text		: 'Imprimir',
						scale		: 'large',
						iconCls		: 'genera',
						iconAlign	: 'top',
						handler 	: function(){window.open("items/items_estructura.php?opc=imprimir")}
					},
					{
						xtype		: 'button',
						width 		: 60,
						height 		: 56,
						text		: 'Regresar',
						scale		: 'large',
						iconCls		: 'regresar',
						iconAlign	: 'top',
						handler 	: function(){Win_Ventana_Estructura_ItemsGrupos.close(id)}
					},'-'

				]
			}).show();
		}
    </script>
<?php } ?>