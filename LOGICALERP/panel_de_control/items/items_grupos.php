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
			$grilla->GrillaName	 		= 'ItemsGrupo';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'items_familia_grupo';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_familia='$id_item_familia' AND id_empresa='$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'codigo ASC';			//LIMITE DE LA CONSULTA
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 250;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,nombre';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',60);
			$grilla->AddRow('Nombre del Grupo','nombre',250);
			$grilla->AddRowImage('','<center><div style="float:left; margin: 0 0 0 7px"><img src="../../temas/clasico/images/BotonesTabs/config16.png?" style="cursor:pointer" width="16" height="16" onclick="VentanaSubGrupo([id]);"></div></center>',30);
			$grilla->AddRowImage('Cuentas','<center title="Configurar cuentas contables por grupo"><img src="../../temas/clasico/images/BotonesTabs/table_gear.png?" style="cursor:pointer" width="16" height="16" onclick="ventanaCuentasGrupos([id],\'[nombre]\');"></center>',50);
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 280;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Grupo Items '.$subtitulo; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Grupo '; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'cubos_add';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_ItemsGrupo.close();Actualiza_Div_ItemsFamilia('.$id_item_familia.');');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 340;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 150;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VSqlBtnEliminar	= "SELECT id FROM items_familia_grupo_subgrupo WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA']." AND id_grupo=".$id;	//VALIDA SI EJECUTA BTN ELIMINAR CON UNA CONSULTA SQL
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			$grilla->AddBottonVentana('Subgrupos','cubos_add','VentanaSubGrupo(elid);');

		//CONFIGURACION DEL MENU CONTEXTUAL
 			$grilla->MenuContext		= 'false';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			//$grilla->AddSeparator('Datos Grupo');
			$grilla->AddTextField('Codigo:','codigo',200,'true','false');
			$grilla->AddTextField('Nombre:','nombre',200,'true','false');
            $grilla->AddTextField('id_familia','id_familia',180,'true','true',$id_item_familia);
            $grilla->AddTextField('','id_empresa',200,'true','hidden',$id_empresa);
			$grilla->AddTextField('','grupo_empresarial',150,'true','true',$grupo_empresa);

			$grilla->AddValidation('codigo','unico_global','id_empresa='.$id_empresa.' AND id_familia='.$id_item_familia);


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

		function VentanaSubGrupo(id){
			var nombreGrupoItems = document.getElementById('div_ItemsGrupo_nombre_'+id).innerHTML;
			var myalto           = Ext.getBody().getHeight();
			var myancho          = Ext.getBody().getWidth();

			Win_Ventana_ItemsSubgrupo = new Ext.Window({
				width		: myancho - 200,
				id			: 'Win_Ventana_ItemsSubgrupo',
				height		: myalto - 200,
				title		: 'SubGrupos del Grupo "'+nombreGrupoItems+'"',
				modal		: true,
				autoScroll	: false,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'items/items_grupo_subgrupo.php',
					scripts	: true,
					nocache	: true,
					params	: { id_item_grupo : id, id_item_familia: "<?php echo $id_item_familia ?>" }
				}
			}).show();
		}


		function VentanaEstructura(id){
			var myalto  = Ext.getBody().getHeight();
			var myancho  = Ext.getBody().getWidth();

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
						xtype     : 'button',
						text      : 'Imprimir',
						scale     : 'large',
						iconCls   : 'genera',
						iconAlign : 'left',
						handler   : function(){ window.open("items/items_estructura.php?opc=imprimir") }
					},
					{
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'left',
						handler   : function(){ Win_Ventana_Estructura_ItemsGrupos.close(id) }
					}
				]
			}).show();
		}
	    function ventanaCuentasGrupos(id,grupo){

	    	Win_Panel_Global = new Ext.Window({
	            width       : 650,
	            height      : 500,
	            title       : 'Cuentas Contables predefinidas grupo '+grupo,
	            modal       : true,
	            autoScroll  : false,
	            autoDestroy : true,
	            resizable   : true,
	            bodyStyle   : 'background-color:#FFF;',
	            items       :
				[
					{
						xtype		: 'panel',
						id			: 'contenedor_Win_Panel_Global',
						border		: false,
	            		bodyStyle   : 'background-color:#FFF;',
						autoLoad	:
						{
							url		: 'items/cuentas_default_grupos/panel_cuentas_default.php',
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