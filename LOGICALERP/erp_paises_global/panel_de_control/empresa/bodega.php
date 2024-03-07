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
			$grilla->GrillaName	 		= 'Bodega';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'empresas_sucursales_bodegas';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo = 1 AND id_empresa = '.$filtro_empresa.' AND id_sucursal = '.$filtro_sucursal;		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 450;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 265;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 900;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto			= 300;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,id';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('id','id',50);
			$grilla->AddRow('Bodega','nombre',250);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 60;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Administracion Bodega'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Bodega'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addsucursal';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_Bodega.close(); Actualiza_Div_Sucursal('.$filtro_sucursal.');');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->VAncho		 		= 310;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VAlto		 		= 140;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('','id_empresa',200,'false','hidden', $filtro_empresa);
			$grilla->AddTextField('','id_sucursal',200,'false','hidden', $filtro_sucursal);
			$grilla->AddTextField('Nombre','nombre',200,'true','false');


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
	//============== VENTANA EDITAR UNA BODEGA ============//
		function Editar_Bodega(id){

			Win_Ventana_Aactualizar_panelControlBodega = new Ext.Window({
				width		: 310,
				height		: 180,
				id			: 'Win_Ventana_Agregar_Bodega',
				title		: 'Nueva Bodega',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'sucursales/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc : 'cargarCampoBodegaUpdate',
						id  : id
					 }
				},
				tbar		:
				[
					{
						xtype   : 'buttongroup',
						title   : 'Opciones',
						columns : 3,
						items   :
						[
							{
								xtype		: 'button',
								id 			: 'btnActualizarBodega',
								width		: 80,
								text		: 'Actualizar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								// disabled	: true,
								handler 	: function(){ guardarActualizarBodega(id); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Eliminar',
								scale		: 'large',
								iconCls		: 'eliminar',
								iconAlign	: 'top',
								handler 	: function(){ eliminarBodega(id); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ Win_Ventana_Aactualizar_panelControlBodega.close(); }
							}
						]
					}
				]
			}).show();
		}
	//============== VENTANA GUARDAR UNA BODEGA ===========//
		function Agregar_Bodega(){

			// var myalto  = Ext.getBody().getHeight()
			// ,	myancho = Ext.getBody().getWidth();

			Win_Ventana_Agregar_panelControlBodega = new Ext.Window({
				width		: 310,
				height		: 180,
				id			: 'Win_Ventana_Agregar_Bodega',
				title		: 'Nueva Bodega',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'sucursales/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	: { opc: 'cargarCampoBodega' }
				},
				tbar		:
				[
					{
						xtype   : 'buttongroup',
						title   : 'Opciones',
						columns : 2,
						items   :
						[
							{
								xtype		: 'button',
								id 			: 'btnGuardarBodega',
								width		: 80,
								text		: 'Guardar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								// disabled	: true,
								handler 	: function(){ guardarActualizarBodega(''); }
							},
							{
								xtype		: 'button',
								width		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ Win_Ventana_Agregar_panelControlBodega.close(); }
							}
						]
					}
				]
			}).show();
		}
	//============= FUNCION PARA GUARDAR O ACTULIZAR UNA BODEGA =============//
	function guardarActualizarBodega(id){
		var bodega=document.getElementById("newNombreBodegaPanelControl").value;
		if (bodega=='') {alert("El campo bodega es obligatorio!"); return;}
		id > 0 ? opc = 'actualizarBodega' : opc = 'guardarBodega';

		Ext.get('renderInserUpdateBodega').load({
				url		: 'sucursales/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc        : opc,
					id         : id,
					bodega 	   : bodega,
					idSucursal : '<?php echo $filtro_sucursal ?>',
					idEmpresa  : '<?php echo $filtro_empresa; ?>'
				}
			});

	}
	//============= FUNCION PARA ELIMINAR UNA BODEGA =================//
	function eliminarBodega(id){
		Ext.get('renderInserUpdateBodega').load({
				url		: 'sucursales/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc        : 'eliminarBodega',
					id         : id,
					idSucursal : '<?php echo $filtro_sucursal ?>',
					idEmpresa  : '<?php echo $filtro_empresa; ?>'
				}
			});
	}


    </script>

<?php } ?>
