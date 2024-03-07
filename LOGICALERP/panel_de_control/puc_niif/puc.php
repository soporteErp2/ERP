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
			$grilla->GrillaName	 		= 'grillaPucNiif';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'puc_niif';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
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
			$grilla->CamposBusqueda		= 'cuenta,descripcion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Cuenta','cuenta',100);
			$grilla->AddRow('Descripcion','descripcion',350);
			$grilla->AddRow('Cuenta por Sucursal','sucursal',150);
			$grilla->AddRow('Departamento','departamento',100);
			$grilla->AddRow('Ciudad','ciudad',100);
			$grilla->AddRow('Centro costo','centro_costo',80);
			$grilla->AddRow('Cuenta cruce','cuenta_cruce',80);

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
			$grilla->AddMenuContext('Configuracion','table_gear','cambiaConfiguracionCuenta([id])');
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
			        url     : 'puc_niif/bd/bd.php',
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
				url     : 'puc_niif/bd/bd.php',
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
				url     : 'puc_niif/bd/bd.php',
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

		//====================================// EDITAR //===================================//
		/*************************************************************************************/
		var globalId     = ''
		,	globalTitle  = ''
		,	globalAction = '';

		function Editar_grillaPucNiif(id){
			globalId     =  id;
			globalTitle  = 'actualizar';
			globalAction = 'comprobarEditar';

			var myalto      = Ext.getBody().getHeight()
			,	myancho     = Ext.getBody().getWidth()
			,	cuenta      = (document.getElementById('div_grillaPucNiif_cuenta_'+id).innerHTML)
			,	descripcion = (document.getElementById('div_grillaPucNiif_descripcion_'+id).innerHTML);

			wiv_ventana_EditarItemPucNiif = new Ext.Window({
				width       : 580,
				height      : 500,
				border      : true,
				bodyStyle   : 'background-color: #FFF; border-top:3px solid #BBB;',
				id          : 'wiv_ventana_EditarItemPucNiif',
				title       : cuenta+" "+descripcion,
				modal       : true,
				autoScroll  : true,
				closable    : false,
				autoDestroy : true,
				autoLoad    :
				{
					url		: 'puc_niif/bd/bd.php',
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
						xtype	: 'buttongroup',
						title	: 'Informacion de Cuenta Niif',
						height  : 105,
						columns	: 3,
						items	:
						[
							{
								xtype		: 'panel',
								border		: false,
								width		: 230,
								height		: 80,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad    :
								{
									url		: 'puc_niif/bd/bd.php',
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
								id 			: 'btnComprobarNewCodigoItemPucNiif',
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
								id 			: 'btnGuardarItemPucNiif',
								width		: 80,
								height		: 80,
								text		: 'Actualizar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								disabled	: true,
								handler 	: function(){ BloqBtn(this); guardarEditarItemPucNiif('editar',id);}
							},
							{
								xtype		: 'button',
								id 			: 'btnEliminarItemPucNiif',
								width		: 80,
								height		: 80,
								text		: 'Eliminar',
								scale		: 'large',
								iconCls		: 'no',
								iconAlign	: 'top',
								handler 	: function(){ BloqBtn(this); eliminarItemPucNiif(id);}
							},
							{
								xtype		: 'button',
								width		: 80,
								height		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ wiv_ventana_EditarItemPucNiif.close(id); Actualiza_Div_grillaPucNiif(id) }
							}
						]
					}
				]
			}).show();
		}

		function Agregar_grillaPucNiif(){
			globalId     = '';
			globalTitle  = 'nuevo';
			globalAction = 'cargar';

			var myalto  = Ext.getBody().getHeight()
			,	myancho = Ext.getBody().getWidth();

			Win_Ventana_AgregarItemPuc = new Ext.Window({
				width       : 530,
				height      : 350,
				bodyStyle   : 'background-color: #FFF; border-top:3px solid #BBB;',
				id          : 'Win_Ventana_AgregarItemPuc',
				title       : 'Nuevo Item PUC',
				modal       : true,
				autoScroll  : true,
				closable    : false,

				autoDestroy : true,
				autoLoad    :
				{
					url		: 'puc_niif/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:  {  op : 'divCuerpoInsertUpdateItemPuc' }
				},
				tbar		:
				[
					{
						xtype	: 'buttongroup',
						title	: 'Nueva Informacion de Cuenta',
						height  : 105,
						columns	: 3,
						items	:
						[
							{
								xtype		: 'panel',
								border		: false,
								width		: 230,
								height		: 80,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad    :
								{
									url		: 'puc_niif/bd/bd.php',
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
								id 			: 'btnComprobarNewCodigoItemPucNiif',
								width		: 80,
								height		: 80,
								text		: 'Comprobar',
								scale		: 'large',
								iconCls		: 'ok',
								iconAlign	: 'top',
								handler 	: function(){ BloqBtn(this); ComprobarNewCodigoItemGrilla('cargar'); }
							},
							{
								xtype		: 'button',
								id 			: 'btnGuardarItemPucNiif',
								width		: 80,
								height		: 80,
								text		: 'Guardar',
								scale		: 'large',
								iconCls		: 'guardar',
								iconAlign	: 'top',
								disabled	: true,
								handler 	: function(){ BloqBtn(this); guardarEditarItemPucNiif('guardar',''); }
							},
							{
								xtype		: 'button',
								width		: 80,
								height		: 80,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){ Win_Ventana_AgregarItemPuc.close() }
							}
						]
					}
				]
			}).show();
		}

	function ventanaUpdateCuentaNiif(id){
		Win_Ventana_new_nombre_cuenta = new Ext.Window({
		    width       : 300,
		    height      : 150,
		    id          : 'Win_Ventana_new_nombre_cuenta',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'puc_niif/bd/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					op       : 'ventanaUpdateNombreCuenta',
					idCuenta : id
		        }
		    },
		    tbar        :
		    [
                {
					xtype   : 'buttongroup',
					title   : 'Opciones',
					height  : 80,
					columns : 6,
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
		                    handler     : function(){ updateNombreCuentaNiif(id); }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    handler     : function(){ Win_Ventana_new_nombre_cuenta.close(id); }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function cambiaConfiguracionCuenta(id) {
    	var title = (document.getElementById('div_grillaPucNiif_cuenta_'+id).innerHTML)+' '+(document.getElementById('div_grillaPucNiif_descripcion_'+id).innerHTML);

        Win_Ventana_configuracion_cuenta = new Ext.Window({
            width       : 400,
            height      : 300,
            id          : 'Win_Ventana_configuracion_cuenta',
            title       : title,
            modal       : true,
            autoScroll  : false,
            closable    : true,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'puc_niif/bd/bd.php',
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
    	MyLoading2('on');
		var ccosPuc         = document.getElementById('centro_costo').value
		,	tipo            = document.getElementById('tipo_cuenta_puc').value
		,	descripcionNiif = document.getElementById('descripcion').value;

		Ext.get('divLoadPucNiif').load({
			url     : 'puc_niif/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op              : 'guardarConfiguracionPuc',
				id_cuenta       : id,
				tipo            : tipo,
				ccosPuc         : ccosPuc,
				descripcionNiif : descripcionNiif,
			}
		});
    }

	function updateNombreCuentaNiif(id){

		var newNombre = document.getElementById('new_nombre_cuenta').value;
		if(newNombre == '' || newNombre > 0){ alert("Aviso\nNombre no valido para una cuenta!"); }

		Ext.get('renderUpdateNombreCuenta').load({
			url     : 'puc_niif/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op        : 'updateNombreCuenta',
				idCuenta  : id,
				newNombre : newNombre
			}
		});
	}

	function puc_Niif_mayuscula(inputNewNombre){ inputNewNombre.value = inputNewNombre.value.toUpperCase(); }


		//FUNCION COMPROBAR LA CUENTA
		function ComprobarNewCodigoItemGrilla(){
			var newCodigo     = document.getElementById('newCodigoItemPuc').value
			,	newNombre     = document.getElementById('newNombreItemPuc').value
			,	idSucursalPuc = document.getElementById('idSucursalPuc').value;

			if (newCodigo=='' || newNombre==''){ alert('Digite el Codigo y el Nombre del Nuevo item PUC'); return; }
			Ext.get('cuerpoInsertUpdateItemPuc').load({
				url		: 'puc_niif/bd/bd.php',
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
		function guardarEditarItemPucNiif(action,id){

			var newCodigo     = document.getElementById('newCodigoItemPuc').value
			,	newNombre     = document.getElementById('newNombreItemPuc').value
			,	idSucursalPuc = document.getElementById('idSucursalPuc').value;

			if (newCodigo=='' || newNombre==''){ alert('Digite el Codigo y el Nombre del Nuevo item PUC'); return; }
			Ext.get('cuerpoInsertUpdateItemPuc').load({
				url		: 'puc_niif/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					op            : 'validarNuevoCodigoItemPuc',
					action        : action,
					newNombre     : newNombre,
					newCodigo     : newCodigo,
					idSucursalPuc : idSucursalPuc,
					id            : id
				}
			});
		}

		//funcion para eliminar un item
		function eliminarItemPucNiif(id){
			var seleccion = confirm("Esta seguro que desea eliminar este item?")
			,	newCodigo = document.getElementById('newCodigoItemPuc').value;

			if (seleccion==true) {
				Ext.get('cuerpoInsertUpdateItemPuc').load({
					url		: 'puc_niif/bd/bd.php',
					timeout : 180000,
					scripts	: true,
					nocache	: true,
					params	:
					{
						op        : 'eliminarItemPuc',
						newCodigo : newCodigo,
						id        : id
					}
				});
			};
		}

		//VALIDAR NUMEROS
		function changeCodigoInputItemPuc(e){
			var tecla = (document.all) ? e.keyCode : e.which;

			if (tecla == 0 || tecla==37 || tecla==39) return true;							//TECLA "TAB"
			else if(tecla == 8){ Ext.getCmp('btnGuardarItemPucNiif').disable(); return true; }	//TECLA " <- RETROCESO"
			else if (tecla==13){
				var newCodigo = document.getElementById('newCodigoItemPuc').value;
				var newNombre = document.getElementById('newNombreItemPuc').value;

				if (newCodigo ==''){ document.getElementById('newCodigoItemPuc').focus(); return; }
				else if (newNombre == ''){ document.getElementById('newNombreItemPuc').focus(); return; }
				else { ComprobarNewCodigoItemGrilla(); BloqBtn(Ext.getCmp('btnComprobarNewCodigoItemPucNiif')); return; }
			}

			patron    = /[\d]/;
			codeTecla = String.fromCharCode(tecla);

			document.getElementById('cuerpoInsertUpdateItemPuc').innerHTML = '';
			Ext.getCmp('btnGuardarItemPucNiif').disable();
			return patron.test(codeTecla);
		}

		//VALIDAR NUMEROS
		function changeNombreInputItemPuc(e){
			var tecla = (document.all) ? e.keyCode : e.which;

			if (tecla == 0 || tecla==37 || tecla==39) return true;							//TECLA "TAB"
			else if(tecla == 8){ Ext.getCmp('btnGuardarItemPucNiif').disable(); return true; }	//TECLA " <- RETROCESO"
			else if (tecla==13){
				var newCodigo = document.getElementById('newCodigoItemPuc').value;
				var newNombre = document.getElementById('newNombreItemPuc').value;

				if (newNombre == ''){ document.getElementById('newNombreItemPuc').focus(); return; }
				else if (newCodigo ==''){ document.getElementById('newCodigoItemPuc').focus(); return; }
				else { ComprobarNewCodigoItemGrilla(); BloqBtn(Ext.getCmp('btnComprobarNewCodigoItemPucNiif')); return; }
			}

			patron    = /[\#\<\>\'\"]/;
			codeTecla = String.fromCharCode(tecla);
			Ext.getCmp('btnGuardarItemPucNiif').enable();

			if(patron.test(codeTecla)==true){ return false; }
			else{ return true; }
		}

		function cambiaSucursalCuentaPuc(valueSelectSucursal, sucursalDb){ Ext.getCmp("btnGuardarItemPucNiif").disable(); }

		function exportarPuc(){
			window.open("puc_niif/exportarPuc.php");
		}

    </script>

<?php } ?>