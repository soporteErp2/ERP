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
			$grilla->GrillaName	 		= 'InvGrupo';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'inventario_grupo';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'codigo_grupo ASC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 165;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo_grupo,nombre_grupo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo_grupo',80);
			$grilla->AddRow('Nombre','nombre_grupo',250);
			$grilla->AddRowImage('','<center><div style="float:left; margin: 0 0 0 7px"><img src="../../../temas/clasico/images/BotonesTabs/config16.png?" style="cursor:pointer" width="16" height="16" onclick="VentanaSubGrupo([id]);"></div></center>',30);
			$grilla->AddRowImage('Cuentas','<center title="Configurar cuentas contables por grupo"><img src="../../temas/clasico/images/BotonesTabs/table_gear.png?" style="cursor:pointer" width="16" height="16" onclick="ventanaCuentasGrupos([id],\'[nombre]\');"></center>',50);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 350;
			$grilla->FColumnaGeneralAncho	= 330;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 120;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Grupo'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Grupo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Estructura','sucursal','VentanaEstructura(id);');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 400;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 215;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			$grilla->AddBottonVentana('Subgrupo','admincontactos','VentanaSubGrupo(elid);');

		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'true';

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Datos Grupo');
			$grilla->AddTextField('Codigo Grupo:','codigo_grupo',200,'true','false');
			$grilla->AddValidation('codigo_grupo','numero');
			$grilla->AddValidation('codigo_grupo','unico_global','id_empresa='.$id_empresa);
			$grilla->AddTextField('Nombre:','nombre_grupo',200,'true','false');
			$grilla->AddValidation('nombre_grupo','mayuscula');
			$grilla->AddTextField('Vida Util (en meses):','vida_util',200,'true','false');
			$grilla->AddValidation('vida_util','numero');
			$grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);



	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

	if(!isset($opcion)){?>
	<script>

		function VentanaSubGrupo(id,titulo){
			var titulo  = document.getElementById('div_InvGrupo_nombre_grupo_'+id).innerHTML;
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_Subgrupo = new Ext.Window({
				width		: myancho - 110,
				id			: 'Win_Ventana_subgrupo',
				height		: myalto - 110,
				title		: 'SubGrupos &nbsp;&nbsp;-Grupo Activos Fijos '+titulo,
				modal		: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../../LOGICALERP/panel_de_control/grupo_inventario/inventario_grupo_subgrupo.php',
					scripts	: true,
					nocache	: true,
					params	: { id_grupo : id }
				}
			}).show();
		}


		function VentanaEstructura(id){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			Win_Ventana_Estructura_Inventario_Grupos = new Ext.Window
			(
				{
					width		: 500,
					id			: 'Win_Ventana_Estructura_Inventario_Grupos',
					height		: myalto - 80,
					title		: 'Estructura Grupos De Inventario',
					modal		: true,
					autoScroll	: true,
					closable	: false,
					autoDestroy : true,
					autoLoad	:
					{
						url		: '../../LOGICALERP/panel_de_control/grupo_inventario/estructura.php',
						scripts	: true,
						nocache	: true,
						params	: { opc	: "ver" }
					},
					tbar		:
					[
						{
							xtype		: 'button',
							text		: 'Imprimir',
							scale		: 'large',
							iconCls		: 'genera',
							iconAlign	: 'left',
							handler 	: function(){window.open("../../LOGICALERP/panel_de_control/grupo_inventario/estructura.php?opc=imprimir")}
						},
						{
							xtype		: 'button',
							text		: 'Regresar',
							scale		: 'large',
							iconCls		: 'regresar',
							iconAlign	: 'left',
							handler 	: function(){Win_Ventana_Estructura_Inventario_Grupos.close(id)}
						}
					]
				}
			).show();
		}

		function ventanaCuentasGrupos(id,grupo){

	    	Win_Panel_Global = new Ext.Window({
	            width       : 600,
	            height      : 500,
	            title       : 'Cuentas Contables '+grupo,
	            modal       : true,
	            autoScroll  : false,
	            autoDestroy : true,
	            resizable   : true,
	            // bodyStyle   : color,
	            items       :
				[
					{
						xtype		: 'panel',
						id			: 'contenedor_Win_Panel_Global',
						border		: false,
						// bodyStyle 	: color,
						autoLoad	:
						{
							url		: 'grupo_inventario/configuracion_cuentas.php',
							scripts	: true,
							nocache	: true,
							params	: { id_grupo : id,	}
						}
					}
				]
			}).show();
	    }

    </script>
<?php } ?>