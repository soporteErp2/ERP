<?php

	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	// switch ($_SESSION['PAIS']) {
	// 	case '140':
	// 		include '../../erp_mexico/panel_de_control/puc/puc.php';
	// 		exit;
	// 		break;
	// }

	// switch ($_SESSION['PAIS']) {
	// 	case '140':
		// case '173':
			$optionConfig = '<center><img src="img/config16.png" onclick="configurarCuentasSimultaneas([id])" style="width:15px;height:15px;cursor:pointer;"></center>';
	// 		break;
	// }

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
			$grilla->GrillaName	 		= 'grillaPuc';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'puc';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'CAST(cuenta AS CHAR) ASC';
			$grilla->MySqlLimit			= '0,200';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 360;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 80;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'cuenta,descripcion,sucursal';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('Cuenta','cuenta',100);
			$grilla->AddRow('Descripcion','descripcion',350);
			$grilla->AddRow('Cuenta por Sucursal','sucursal',150);
			$grilla->AddRow('Cuenta Niif','cuenta_niif',100);
			$grilla->AddRow('Departamento','departamento',100);
			$grilla->AddRow('Ciudad','ciudad',100);
			$grilla->AddRow('Centro costo','centro_costo',80);
			$grilla->AddRow('Cuenta cruce','cuenta_cruce',80);
			$grilla->AddRow('Tipo','tipo',90);
            $grilla->AddRowImage('Cta Adicional',$optionConfig,80);

			$grilla->EditLike('cuenta','RIGHT');

			$grilla->AddColStyle('cuenta','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
			$grilla->AddColStyle('cuenta_niif','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

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
			$grilla->VBotonNText		= 'Nuevo Item PUC'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addsucursal';	//IMAGEN CSS DEL BOTON
			$grilla->AddBotton("Exportar","excel32","exportarPuc()");
			// $grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->VAncho		 	= 310;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VAlto		 	= 140;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			// $grilla->VQuitarAncho	= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			// $grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			// $grilla->VBotonEliminar	= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			// $grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

			$grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		$grilla->MenuContextEliminar= 'false';
			$grilla->AddMenuContext('Configuraciones','flechas_cruce16','cambiaConfiguracionCuenta([id])');
			$grilla->AddMenuContext('Ubicacion Departamento/Ciudad','flechas_cruce16','ventanaUbicacion([id])');
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
		//==================================// UBICACION //==================================//
		/*************************************************************************************/
		function ventanaUbicacion(id){

			Win_Ventana_selecciona_ubicacion = new Ext.Window({
			    width       : 300,
			    height      : 180,
			    id          : 'Win_Ventana_selecciona_ubicacion',
			    title       : '',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'puc/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						op : 'ventanaSeleccionarUbicacion',
						id : id
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Guardar',
			                    scale       : 'large',
			                    iconCls     : 'guardar',
			                    iconAlign   : 'top',
			                    handler     : function(){ guardarUbicacionPuc(id) }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_selecciona_ubicacion.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		function guardarUbicacionPuc(id){
			var idDepartamento = document.getElementById('departamento_puc').value
			,	idCiudad       = document.getElementById('ciudad_puc').value;

			if(isNaN(idDepartamento) || idDepartamento==0 || isNaN(idCiudad) || idCiudad==0){
				if(!confirm("Esta seguro de no relacionar departamento y cuidad en la cuenta puc?")){ return; }
			}

			Ext.get('renderSaveUbicacionPuc').load({
				url     : 'puc/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					op             : 'guardarUbicacionPuc',
					id             : id,
					idDepartamento : idDepartamento,
					idCiudad       : idCiudad
				}
			});
		}

		function updateComboCiudadPuc(id, idCiudad){
			Ext.get('combo_ciudad_puc').load({
				url     : 'puc/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					op             : 'updateComboCiudadPuc',
					idDepartamento : id,
					idCiudad       : idCiudad
				}
			});
		}

		//====================================// EDITAR //====================================//
		/**************************************************************************************/
		var globalId     = ''
		,	globalTitle  = ''
		,	globalAction = '';

		function Editar_grillaPuc(id){
			globalId     =  id;
			globalTitle  = 'actualizar';
			globalAction = 'comprobarEditar';

			var myalto      = Ext.getBody().getHeight()
			,	myancho     = Ext.getBody().getWidth()
			,	cuenta      = (document.getElementById('div_grillaPuc_cuenta_'+id).innerHTML)
			,	descripcion = (document.getElementById('div_grillaPuc_descripcion_'+id).innerHTML);

			wiv_ventana_EditarItemPuc = new Ext.Window({
				width       : 580,
				height      : 530,
				border      : true,
				bodyStyle   : 'background-color: #FFF; border-top:3px solid #BBB;',
				id          : 'wiv_ventana_EditarItemPuc',
				title       : cuenta+" "+descripcion,
				modal       : true,
				autoScroll  : true,
				closable    : false,
				autoDestroy : true,
				autoLoad    :
				{
					url		: 'puc/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						op        : 'divCuerpoInsertUpdateItemPuc',
						action    : 'cargar',
						title     : 'cargar',
						id        : id,
						newCodigo : cuenta
					}
				},
				tbar		:
				[
					{
						xtype   : 'buttongroup',
						title   : 'Informacion de Cuenta',
						height  : 105,
						width   : 240,
						columns : 3,
						items   :
						[
							{
								xtype		: 'panel',
								border		: false,
								width		: 240,
								height		: 80,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad    :
								{
									url		: 'puc/bd/bd.php',
									scripts	: true,
									nocache	: true,
									params	:
									{
										op          : 'inputNuevoItemPuc',
										action      : 'editar',
										cuenta      : cuenta,
										descripcion : descripcion
									}
								}
							}
						]
					},
					{
						xtype   : 'buttongroup',
						title   : 'Opciones',
						height  : 105,
						columns : 6,
						items   :
						[
							{
								xtype		: 'button',
								id 			: 'btnComprobarNewCodigoItemPuc',
								width		: 80,
								height		: 80,
								text		: 'Comprobar',
								scale		: 'large',
								iconCls		: 'ok',
								iconAlign	: 'top',
								handler 	: function(){ BloqBtn(this); ComprobarNewCodigoItemGrilla(); }
							},
							{
								xtype		: 'button',
								id 			: 'btnGuardarItemPuc',
								width		: 80,
								height		: 80,
								text		: 'Actualizar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								disabled	: true,
								handler 	: function(){ BloqBtn(this); guardarEditarCuentaPuc('editar',id);}
							},
							{
								xtype		: 'button',
								id 			: 'btnEliminarItemPuc',
								width		: 80,
								height		: 80,
								text		: 'Eliminar',
								scale		: 'large',
								iconCls		: 'no',
								iconAlign	: 'top',
								handler 	: function(){ BloqBtn(this); eliminarItemPuc(id);}
							},
							{
								xtype		: 'button',
								width		: 80,
								height		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ wiv_ventana_EditarItemPuc.close(id); Actualiza_Div_grillaPuc(id) }
							}
						]
					}
				]
			}).show();
		}

		function Agregar_grillaPuc(){
			globalId     = '';
			globalTitle  = 'nuevo';
			globalAction = 'cargar';

			var myalto = Ext.getBody().getHeight()
			,	myancho  = Ext.getBody().getWidth();

			Win_Ventana_AgregarItemPuc = new Ext.Window({
				width       : 770,
				height      : 350,
				bodyStyle   : 'background-color: #FFF; border-top:3px solid #BBB;',
				id          : 'Win_Ventana_AgregarItemPuc',
				title       : 'Nuevo Item PUC',
				modal       : true,
				autoScroll  : true,
				closable    : false,
				autoDestroy : true,
				autoLoad    :	{
												url			: 'puc/bd/bd.php',
												scripts	: true,
												nocache	: true,
												params	:  { op : 'divCuerpoInsertUpdateItemPuc' }
											},
				tbar				:
				[
					{
						xtype		: 'buttongroup',
						title		: 'Nueva Informacion de Cuenta',
						height  : 110,
						width   : 240,
						columns	: 3,
						items		:
						[
							{
								xtype			: 'panel',
								border		: false,
								width			: 490,
								height		: 90,
								bodyStyle : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad  :
								{
									url		: 'puc/bd/bd.php',
									scripts	: true,
									nocache	: true,
									params	:
									{
										op     : 'inputNuevoItemPuc',
										action : 'agregar'
									}
								}
							}
						]
					},
					{
						xtype   : 'buttongroup',
						title   : 'Opciones',
						columns : 6,
						items   :
						[
							{
								xtype		: 'button',
								id 			: 'btnComprobarNewCodigoItemPuc',
								width		: 80,
								height	: 80,
								text		: 'Comprobar',
								scale		: 'large',
								iconCls		: 'ok',
								iconAlign	: 'top',
								handler 	: function(){ BloqBtn(this); ComprobarNewCodigoItemGrilla('cargar'); }
							},
							{
								xtype		: 'button',
								id 			: 'btnGuardarItemPuc',
								width		: 80,
								height		: 80,
								text		: 'Guardar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								disabled	: true,
								handler 	: function(){ BloqBtn(this); guardarEditarCuentaPuc('guardar',''); }
							},
							{
								xtype		: 'button',
								width		: 80,
								height		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ Win_Ventana_AgregarItemPuc.close(id) }
							}
						]
					}
				]
			}).show();
		}

		function puc_colgaap_mayuscula(inputNewNombre){ inputNewNombre.value = inputNewNombre.value.toUpperCase(); }

		//==================================// CONFIGURACION DE LA CUENTA //==================================//
		//****************************************************************************************************//
	    function cambiaConfiguracionCuenta(id) {
	    	var title = (document.getElementById('div_grillaPuc_cuenta_'+id).innerHTML)+' '+(document.getElementById('div_grillaPuc_descripcion_'+id).innerHTML);

	        Win_Ventana_configuracion_cuenta = new Ext.Window({
	            width       : 500,
	            height      : 340,
	            id          : 'Win_Ventana_configuracion_cuenta',
	            title       : title,
	            modal       : true,
	            autoScroll  : false,
	            closable    : true,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : 'puc/bd/bd.php',
	                scripts : true,
	                nocache : true,
	                params  :
	                {
						op : 'configurarCuentaPuc',
						id : id,
	                }
	            },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            style   : 'border-right:none;',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Guardar',
			                    scale       : 'large',
			                    iconCls     : 'guardar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); guardar_configuracion_cuenta(id) }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_configuracion_cuenta.close(id) }
			                }
			            ]
			        }
			    ]
	        }).show();
	    }

	    function guardar_configuracion_cuenta(id){

			var cuentaNiif         = document.getElementById('cuenta_niif').innerHTML
			,	ccosPuc            = document.getElementById('ccos_puc').value
			,	tipo               = document.getElementById('tipo_cuenta_puc').value
			,	cuentaCruce        = document.getElementById('cuenta_cruce_puc').value
			,	cuentaColgaap      = document.getElementById('div_grillaPuc_cuenta_'+id).innerHTML
			,	sincDescripcion    = document.getElementById('sincDescripcion').dataset.sinc
			,	descripcionColgaap = document.getElementById('descripcion_colgaap').value;

			Ext.get('divLoadPucNiif').load({
				url     : 'puc/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					op                 : 'guardarConfiguracionPuc',
					id_cuenta_colgaap  : id,
					cuentaColgaap      : cuentaColgaap,
					cuentaNiif         : cuentaNiif,
					tipo               : tipo,
					ccosPuc            : ccosPuc,
					cuentaCruce        : cuentaCruce,
					sincDescripcion    : sincDescripcion,
					descripcionColgaap : descripcionColgaap,
				}
			});
	    }

	    function sincDescripcion(divSinc){
	    	var sinc = divSinc.dataset.sinc
	    	,	img = document.getElementById('imgSincDescripcion');

	    	if(sinc == 'true'){
				img.src = 'puc/img/false.png';
				divSinc.dataset.sinc = 'false';
	    	}
	    	else{
				img.src = 'puc/img/true.png';
				divSinc.dataset.sinc = 'true';
	    	}
	    }

	    //========================== FUNCION PARA ELIMINAR LA CUENTA NIIF CRUZADA =====================================//
	    function eliminaCuentaNiif(id) {
	    	document.getElementById('cuenta_niif').innerHTML = '';
	    	document.getElementById('descripcion_cuenta_niif').innerHTML = '';
	    	document.getElementById('divEliminaCuenta').style.display = 'none';
	    }

	    //====================================== VENTANA BUSCAR CUENTA  =======================================================//
	    function ventanaBuscarCuentaNiif(opc,id_colgaap){
	        var myalto  = Ext.getBody().getHeight();
	        var myancho = Ext.getBody().getWidth();

	        Win_Ventana_buscar_cuenta_nota = new Ext.Window({
	            width       : myancho-100,
	            height      : myalto-50,
	            id          : 'Win_Ventana_buscar_cuenta_nota',
	            title       : 'Seleccionar Cuenta Niif a cruzar',
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : '../funciones_globales/grillas/buscar_cuenta_puc.php',
	                scripts : true,
	                nocache : true,
	                params  :
	                {
						opc          : opc,
						validaCuenta : 'false',
						opcGrilla    : 'grillasVentanaBuscarCuentaNiif',
						cargaFuncion : 'responseVentanaBuscarCuentaNiif(id,"'+id_colgaap+'")',
					}
	            },
	            tbar        :
	            [
	                {
	                    xtype       : 'button',
	                    text        : 'Regresar',
	                    scale       : 'large',
	                    iconCls     : 'regresar',
	                    iconAlign   : 'left',
	                    handler     : function(){ Win_Ventana_buscar_cuenta_nota.close(id) }
	                },'-'
	            ]
	        }).show();
	    }

	    //===================== FUNCION QUE ABRE LA VENTANA PARA BUSCAR LA CUENTA NIIF A CAMBIAR ============================//
	    function responseVentanaBuscarCuentaNiif(id_niif,id_colgaap) {
			var cuenta_niif      = document.getElementById('div_grillasVentanaBuscarCuentaNiif_cuenta_'+id_niif).innerHTML
			,	descripcion_niif = document.getElementById('div_grillasVentanaBuscarCuentaNiif_descripcion_'+id_niif).innerHTML
			,	cuenta_colgaap   = document.getElementById('div_grillaPuc_cuenta_'+id_colgaap).innerHTML;

	        document.getElementById('cuenta_niif').innerHTML = cuenta_niif;
	        document.getElementById('descripcion_cuenta_niif').innerHTML = descripcion_niif;
	        document.getElementById('divEliminaCuenta').style.display = 'block';

	        Win_Ventana_buscar_cuenta_nota.close();
	    }

		function ComprobarNewCodigoItemGrilla(){
			var newCodigo     = document.getElementById('newCodigoItemPuc').value
			,	newNombre     = document.getElementById('newNombreItemPuc').value
			,	idSucursalPuc = document.getElementById('idSucursalPuc').value;

			if (newCodigo=='' || newNombre==''){ alert('Digite el Codigo y el Nombre del Nuevo item PUC'); return; }
			Ext.get('cuerpoInsertUpdateItemPuc').load({
				url		: 'puc/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op            : 'validarNuevoCodigoItemPuc',
					newCodigo     : newCodigo,
					newNombre     : newNombre,
					action        : globalAction,
					idSucursalPuc : idSucursalPuc,
					title         : globalTitle,
					id            : globalId
				}
			});
		}
		//funcion para guardar el nuevo item puc
		function guardarEditarCuentaPuc(action,id){
			var newCodigo     = document.getElementById('newCodigoItemPuc').value
			,	newNombre     = document.getElementById('newNombreItemPuc').value
			,	idSucursalPuc = document.getElementById('idSucursalPuc').value
			,	crearNiif = (action=='guardar')? document.getElementById('crearNiif').value : ''
			,	validar = (action=='guardar')? document.getElementById('validar').value : '' ;

			if (newCodigo=='' || newNombre==''){ alert('Digite el Codigo y el Nombre del Nuevo item PUC'); return; }
			Ext.get('cuerpoInsertUpdateItemPuc').load({
				url		: 'puc/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op            : 'validarNuevoCodigoItemPuc',
					action        : action,
					idSucursalPuc : idSucursalPuc,
					newNombre     : newNombre,
					newCodigo     : newCodigo,
					crearNiif     : crearNiif,
					validar  	    : validar,
					id            : id
				}
			});
		}

		//funcion para eliminar un item
		function eliminarItemPuc(id){
			var seleccion = confirm("Esta seguro que desea eliminar la presente cuenta PUC?");
			var newCodigo = document.getElementById('newCodigoItemPuc').value;

			if (seleccion==true) {
				Ext.get('cuerpoInsertUpdateItemPuc').load({
					url		: 'puc/bd/bd.php',
					timeout : 180000,
					scripts	: true,
					nocache	: true,
					params	:
					{
						op        : 'eliminarItemPuc',
						id        : id,
						newCodigo : newCodigo,
					}
				});
			};
		}

		//VALIDAR NUMEROS
		function changeCodigoInputItemPuc(e){
			var tecla = (document.all) ? e.keyCode : e.which;

			if (tecla == 0 || tecla==37 || tecla==39) return true;							//TECLA "TAB"
			else if(tecla == 8){ Ext.getCmp('btnGuardarItemPuc').disable(); return true; }	//TECLA " <- RETROCESO"
			else if (tecla==13){
				var newCodigo = document.getElementById('newCodigoItemPuc').value;
				var newNombre = document.getElementById('newNombreItemPuc').value;

				if (newCodigo ==''){ document.getElementById('newCodigoItemPuc').focus(); return; }
				else if (newNombre == ''){ document.getElementById('newNombreItemPuc').focus(); return; }
				else { ComprobarNewCodigoItemGrilla(); BloqBtn(Ext.getCmp('btnComprobarNewCodigoItemPuc')); return; }
			}

			patron    = /[\d\.]/;
			codeTecla = String.fromCharCode(tecla);

			document.getElementById('cuerpoInsertUpdateItemPuc').innerHTML = '';
			Ext.getCmp('btnGuardarItemPuc').disable();
			return patron.test(codeTecla);
		}

		//VALIDAR TEXTO
		function changeNombreInputItemPuc(e){
			var tecla = (document.all) ? e.keyCode : e.which;

			if (tecla == 0 || tecla==37 || tecla==39) return true;							//TECLA "TAB"
			else if(tecla == 8){ Ext.getCmp('btnGuardarItemPuc').disable(); return true; }	//TECLA " <- RETROCESO"
			else if (tecla==13){
				var newCodigo = document.getElementById('newCodigoItemPuc').value;
				var newNombre = document.getElementById('newNombreItemPuc').value;

				if (newNombre == ''){ document.getElementById('newNombreItemPuc').focus(); return; }
				else if (newCodigo ==''){ document.getElementById('newCodigoItemPuc').focus(); return; }
				else { ComprobarNewCodigoItemGrilla(); BloqBtn(Ext.getCmp('btnComprobarNewCodigoItemPuc')); return; }
			}

			patron    = /[\#\<\>\'\"]/;
			codeTecla = String.fromCharCode(tecla);
			Ext.getCmp('btnGuardarItemPuc').disable();

			if(patron.test(codeTecla)==true){ return false; }
			else{ return true; }
		}

		function cambiaSucursalCuentaPuc(valueSelectSucursal, sucursalDb){ Ext.getCmp("btnGuardarItemPuc").disable(); }


		function configurarCuentasSimultaneas(id_cuenta){

			Win_Ventana_CtasSimultaneas = new Ext.Window({
			    width       : 600,
			    height      : 500,
			    id          : 'Win_Ventana_CtasSimultaneas',
			    title       : 'Cuentas a causar simultaneamente',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'puc/cuenta_simultanea.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            id_cuenta : id_cuenta,
			        }
			    }
			}).show();
		}

		function exportarPuc(){
			window.open("puc/exportarPuc.php");
		}

	</script>

<?php } ?>
