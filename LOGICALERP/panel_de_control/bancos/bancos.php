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

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'panelControlBancos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'puc';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo = 1 AND id_empresa = '.$_SESSION['EMPRESA'].' AND cuenta LIKE "111005%" AND cuenta <> 111005';		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 560;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 365;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 80;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto			= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'cuenta,descripcion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Cuenta','cuenta',100);
			$grilla->AddRow('Descripcion','descripcion',250);

			$grilla->EditLike('cuenta','RIGHT');
			$grilla->AddColStyle('cuenta','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 60;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			// $grilla->TituloVentana	= 'Administracion Sucursal'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Banco'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addsucursal';			//IMAGEN CSS DEL BOTON
			// $grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->VAncho		 	= 310;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VAlto		 	= 140;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho	= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			// $grilla->VBotonEliminar	= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			// $grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)


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

		function Editar_panelControlBancos(id){

			var myalto  = Ext.getBody().getHeight()
			,	myancho = Ext.getBody().getWidth()
			,	titulo  = (document.getElementById('div_panelControlBancos_cuenta_'+id).innerHTML)+' '+(document.getElementById('div_panelControlBancos_descripcion_'+id).innerHTML);

			Win_Ventana_Editar_panelControlBancos = new Ext.Window({
				width		: 350,
				height		: 210,
				id			: 'Win_Ventana_Editar_panelControlBancos',
				title		: titulo,
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'bancos/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						op : 'divCuerpoUpdate_PanelControlBancos',
						id : id
					}
				},
				tbar		:
				[
					{
						xtype   : 'buttongroup',
						title   : 'Opciones',
						columns : 6,
						items   :
						[
							{
								xtype		: 'button',
								id 			: 'btnGuardarItemPuc',
								width		: 80,
								text		: 'Actualizar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								// disabled	: true,
								handler 	: function(){ saveInsertUpdateBancoPanelControl(id); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Elimina',
								scale		: 'large',
								iconCls		: 'eliminar',
								iconAlign	: 'top',
								handler 	: function(){ saveEliminaBancoPanelControl(id); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ Win_Ventana_Editar_panelControlBancos.close(); Actualiza_Div_panelControlBancos(id); }
							}
						]
					}
				]
			}).show();
		}



		function Agregar_panelControlBancos(){

			// var myalto  = Ext.getBody().getHeight()
			// ,	myancho = Ext.getBody().getWidth();

			Win_Ventana_Agregar_panelControlBancos = new Ext.Window({
				width		: 350,
				height		: 210,
				id			: 'Win_Ventana_Agregar_panelControlBancos',
				title		: 'Nuevo Banco',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'bancos/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	: { op: 'divCuerpoInsert_PanelControlBancos' }
				},
				tbar		:
				[
					{
						xtype   : 'buttongroup',
						title   : 'Opciones',
						columns : 6,
						items   :
						[
							{
								xtype		: 'button',
								id 			: 'btnGuardarItemPuc',
								width		: 80,
								text		: 'Guardar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								// disabled	: true,
								handler 	: function(){ saveInsertUpdateBancoPanelControl(''); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ Win_Ventana_Agregar_panelControlBancos.close(); }
							}
						]
					}
				]
			}).show();
		}

		function saveInsertUpdateBancoPanelControl(id){
			var newNombre = document.getElementById('newNombreBancoPanelControl').value;
			newNombre     = newNombre.replace(/[\#\<\>\'\"]/g, '');

			if(newNombre == ''){ alert('El campo nombre de cuenta es obligatorio'); return; }
			id > 0 ? op = 'saveUpdateCuentaBancos' : op = 'saveInsertCuentaBancos';

			Ext.get('renderInserUpdateBanco').load({
				url		: 'bancos/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op        : op,
					newNombre : newNombre,
					id        : id
				}
			});
		}

		function saveEliminaBancoPanelControl(id){
			Ext.get('renderInserUpdateBanco').load({
				url		: 'bancos/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op : 'saveEliminaCuentaBancos',
					id : id
				}
			});
		}

    </script>

<?php } ?>