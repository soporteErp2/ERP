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

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$whereSucursal =( user_permisos(1)=='true')? '' : ' AND id_sucursal='.$id_sucursal;

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'encabezadoFacturasSaldosIniciales';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'facturas_saldos_iniciales';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' $whereSucursal";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= '';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			// $grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->Alto		 		= 465;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 80;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'descripcion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Tipo','tipo_factura',50);
			$grilla->AddRow('Consecutivo','consecutivo',80);
			$grilla->AddRow('Cuenta Pago','cuenta_pago',150);
			// $grilla->AddRow('Cuenta Colgaap','cuenta_colgaap',150);
			// $grilla->AddRow('Cuenta Niif','cuenta_niif',150);
			$grilla->AddRow('Sucursal','sucursal',150);
			$grilla->AddRowImage('','<img src="img/config16.png" style="cursor:pointer" width="16" height="16" title="Agregar Facturas" onclick="ventana_difinicion_tributaria(\'[id]\',\'[tipo_factura]\')"><input type="hidden" id="estado_[id]" value="[estado]"><input type="hidden" id="filtro_sucursal_[id]" value="[id_sucursal]">',16);
			$grilla->AddRowImage('','<img src="img/estado_doc/[estado].png" id="img_saldos_iniciales_[id]" style="cursor:pointer" width="16" height="16">',16);


		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 300;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 80;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Grupos Definicion Tributaria'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Saldo Inicial'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add_new';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Panel_Global.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 340;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 180;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			// $grilla->AddSeparator('Datos Centro De Costos');
			// $grilla->AddValidation('codigo_centro_costos','numero')


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>

	function ventana_difinicion_tributaria(id,tipo_factura){

		var img    = document.getElementById('img_saldos_iniciales_'+id).getAttribute('src');
		var hiden1 = (img == 'img/estado_doc/0.png')? false: true;
		var hiden2 = (img == 'img/estado_doc/1.png')? false: true;

		Win_Ventana_encabezado = new Ext.Window({
			    width       : 890,
			    height      : 600,
			    id          : 'Win_Ventana_encabezado',
			    title       : 'Facturas Saldos Iniciales ',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'facturas_saldos_iniciales/facturas_saldos.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
						id_saldo_inicial  : id,
						tipo_factura      : tipo_factura,
			        }
			    }
			    ,
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            id 		: 'btns_generar_saldos_iniciales',
			            columns : 3,
			            hidden  : hiden1,
			            style   : 'border-right:none;',
			            title   : 'Opciones',
			            items   :
			            [
			                {
								xtype     : 'button',
								id        : 'btn_generar_saldo',
								width     : 60,
								height    : 56,
								text      : 'Generar Documento',
								scale     : 'large',
								iconCls   : 'guardar',
								iconAlign : 'top',
								handler   : function(){ generar_saldo_inicial(); }
			                },
			                {
								xtype     : 'button',
								id        : 'btn_upload_excel_saldo',
								width     : 60,
								height    : 56,
								text      : 'Cargar Excel',
								scale     : 'large',
								iconCls   : 'upload_file32',
								iconAlign : 'top',
								handler   : function(){ windows_upload_excel(); }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_encabezado.close(id) }
			                }
			            ]
			        },
			        {
			            xtype   : 'buttongroup',
			            id 		: 'btns_editar_saldos_iniciales',
			            columns : 3,
			            hidden  : hiden2,
			            style   : 'border-right:none;',
			            title   : 'Opciones',
			            items   :
			            [
			            	{
			                    xtype       : 'button',
			            		id 			: 'btn_editar_saldo_inicial',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Editar Documentos',
			                    scale       : 'large',
			                    iconCls     : 'guardar',
			                    iconAlign   : 'top',
			                    handler     : function(){ editar_saldo_inicial(); }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_encabezado.close(id) }
			                }
			            ]
			        }
			    ]
		}).show();
	}

	//AGREGAR UN NUEVO REGISTRO
	function Agregar_encabezadoFacturasSaldosIniciales(){

		Win_Ventana_agregar_grilla = new Ext.Window({
		    width       : 400,
		    height      : 450,
		    id          : 'Win_Ventana_agregar_grilla',
		    title       : 'Editar documento',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'facturas_saldos_iniciales/bd/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opc  : 'ventanaAgregarEncabezado',
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
		                    handler     : function(){ BloqBtn(this); guardaActualizaEncabezado('',0) }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    handler     : function(){ Win_Ventana_agregar_grilla.close() }
		                }
		            ]
		        }
		    ]
		}).show();

	}

	//FUNCION PARA GUARDAR EL ENVABEZADO DE LOS DOCUMENTOS
	function guardaActualizaEncabezado(accion,id) {
		var tipo_factura                      = document.getElementById('tipo_factura').value
		,	fecha_factura                     = document.getElementById('fecha_factura').value
		,	id_cuenta_pago                    = document.getElementById('id_cuenta_pago').value
		,	contrapartida_cuenta_pago_colgaap = document.getElementById('contrapartida_cuenta_pago_colgaap').value
		,	contrapartida_cuenta_pago_niif    = document.getElementById('contrapartida_cuenta_pago_niif').value
		,	filtro_sucursal                   = document.getElementById('filtro_sucursal').value;

		if (tipo_factura==0) {alert("Aviso\nEl campo tipo factura es obligatorio"); return;}
		if (id_cuenta_pago==0) {alert("Aviso\nEl campo cuenta pago es obligatorio"); return;}
		if (contrapartida_cuenta_pago_colgaap==0) {alert("Aviso\nEl campo cuenta contrapartida colgaap es obligatorio"); return;}
		if (contrapartida_cuenta_pago_niif==0) {alert("Aviso\nEl campo cuenta contrapartida niif es obligatorio"); return;}

		if (accion=='actualiza') { opc='actualizaEncabezado'; }
		else { opc='guardarEncabezado'; }

		Ext.get('divLoad').load({
			url     : 'facturas_saldos_iniciales/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc                               : opc,
				tipo_factura                      : tipo_factura,
				fecha_factura                      : fecha_factura,
				id_cuenta_pago                    : id_cuenta_pago,
				id                                : id,
				contrapartida_cuenta_pago_colgaap : contrapartida_cuenta_pago_colgaap,
				contrapartida_cuenta_pago_niif    : contrapartida_cuenta_pago_niif,
				filtro_sucursal                   : filtro_sucursal,
			}
		});

	}

	//EDITAR UN REGISTRO EXISTENTE
	function Editar_encabezadoFacturasSaldosIniciales (id) {
		var estado = document.getElementById('estado_'+id).value
		,	filtro_sucursal = document.getElementById('filtro_sucursal_'+id).value
		,	tipo_factura = document.getElementById('div_encabezadoFacturasSaldosIniciales_tipo_factura_'+id).innerHTML;

		if (estado==1) {
			alert("Aviso!\nEl Documento esta generado, para modificarlo, edite las facturas relacionadas presionando el engranaje");

			Win_Ventana_editar_grilla = new Ext.Window({
			    width       : 400,
			    height      : 350,
			    id          : 'Win_Ventana_editar_grilla',
			    title       : 'Editar documento',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'facturas_saldos_iniciales/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opc             : 'ventanaEditarEncabezado',
						estado 			: 'bloqueado',
						id              : id,
						tipo_factura    : tipo_factura,
						filtro_sucursal : filtro_sucursal,
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
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_editar_grilla.close(id); }
			                }
			            ]
			        }
			    ]
			}).show();
		}
		else{
			Win_Ventana_editar_grilla = new Ext.Window({
			    width       : 400,
			    height      : 450,
			    id          : 'Win_Ventana_editar_grilla',
			    title       : 'Editar documento',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'facturas_saldos_iniciales/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opc             : 'ventanaEditarEncabezado',
						estado 			: 'libre',
						id              : id,
						tipo_factura    : tipo_factura,
						filtro_sucursal : filtro_sucursal,
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
								xtype     : 'button',
								id        : 'bnt_actualizar',
								width     : 60,
								height    : 56,
								text      : 'Actualizar',
								scale     : 'large',
								iconCls   : 'guardar',
								iconAlign : 'top',
								handler   : function(){ BloqBtn(this); guardaActualizaEncabezado('actualiza',id); }
			                },
			                {
								xtype     : 'button',
								id        : 'bnt_eiminar',
								width     : 60,
								height    : 56,
								text      : 'Eliminar',
								scale     : 'large',
								iconCls   : 'eliminar',
								iconAlign : 'top',
								handler   : function(){ BloqBtn(this); eliminaEncabezado(id,tipo_factura); }
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_editar_grilla.close(id); }
			                }
			            ]
			        }
			    ]
			}).show();
		}
	}

	//ELIMINAR REGISTRO
	function eliminaEncabezado(id,tipo_factura){
		if (!confirm("Realmente desea eliminar el registro?")) { return;}
		Ext.get('divLoad').load({
			url     : 'facturas_saldos_iniciales/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc            : 'eliminaEncabezado',
				tipo_factura   : tipo_factura,
				id             : id,
			}
		});
	}

	//VENTANA PARA BUSCAR EL CUENTA DE PAGO
	function ventanaBusquedaCuentaPago() {
		var tipo_factura = document.getElementById('tipo_factura').value;
		if (tipo_factura==0) {alert("Seleccione el tipo de factura!");return;}

		var sql =(tipo_factura=='FV')? ' AND tipo="Venta" AND estado="Credito" ' : ' AND tipo="Compra" AND estado="Credito"  ';

		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_cuenta_pago = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_buscar_cuenta_pago',
		    title       : 'Buscar Cuenta de Pago',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BusquedaCuentaPago.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					nombre_grilla : 'buscar_cuenta_pago',
					cargaFuncion  : 'responseVentanaBuscarCuentaPago(id);',
					sql           : sql,
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
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_Ventana_buscar_cuenta_pago.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	//RENDERIZA LA VENTA QUE BUSCA LA CUENTA DE PAGO
	function responseVentanaBuscarCuentaPago(id) {
		var cuenta_pago         =document.getElementById('div_buscar_cuenta_pago_nombre_'+id).innerHTML;
		var cuenta_pago_colgaap =document.getElementById('div_buscar_cuenta_pago_cuenta_'+id).innerHTML;
		var cuenta_pago_niif    =document.getElementById('div_buscar_cuenta_pago_cuenta_niif_'+id).innerHTML;

		document.getElementById('id_cuenta_pago').value      = id;
		document.getElementById('cuenta_pago').value         = cuenta_pago;
		document.getElementById('cuenta_pago_colgaap').value = cuenta_pago_colgaap;
		document.getElementById('cuenta_pago_niif').value    = cuenta_pago_niif;


		Win_Ventana_buscar_cuenta_pago.close(id);
	}

	//BUSCAR LA CUENTA DE CONTRAPARTIDA
	function ventanaBuscarCuenta(opc) {
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var title = (opc=='puc')? 'PUC' : 'NIIF' ;

		Win_Ventana_buscar_cuenta = new Ext.Window({
		    width       : 680,
		    height      : 520,
		    id          : 'Win_Ventana_buscar_cuenta',
		    title       : 'Consultar la cuenta '+title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../funciones_globales/grillas/BuscarCuentaPuc.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					nombreGrilla : 'buscarCuentaBalancePrueba',
					cargaFuncion : 'renderizaResultadoVentanaPuc(id,"'+opc+'")',
					opc          : opc,

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
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_Ventana_buscar_cuenta.close() }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	//RENDERIZAR LOS RESULTADOS DE LA VENTANA
	function renderizaResultadoVentanaPuc(id,opc){
		if (opc=='niif') { var campo='contrapartida_cuenta_pago_niif';}
		else{var campo='contrapartida_cuenta_pago_colgaap';}

		input=document.getElementById(campo);
		input.value=document.getElementById('div_buscarCuentaBalancePrueba_cuenta_'+id).innerHTML;
		input.setAttribute("title",document.getElementById('div_buscarCuentaBalancePrueba_descripcion_'+id).innerHTML);
		Win_Ventana_buscar_cuenta.close();

		// input.focus();
	}


	//FUNCION DEL SELECT TIPO FACTURA
	function changeTipoFactura() {
		document.getElementById('id_cuenta_pago').value = "";
		document.getElementById('cuenta_pago').value = "";
		document.getElementById('cuenta_pago_colgaap').value = "";
		document.getElementById('cuenta_pago_niif').value = "";
	}

	//SINCRONIZAR LA CONTRAPARTIDA NIIF DE COLGAAP
	function sincronizarCuentaNiif() {
		var cuenta=document.getElementById('contrapartida_cuenta_pago_colgaap');
		if (cuenta.value==0 || cuenta.value=='') {alert("Debe seleccionar la cuenta colgaap primero"); cuenta.focus(); return;}
		Ext.get('divLoad').load({
			url     : 'facturas_saldos_iniciales/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc    : 'sincronizarCuentaNiif',
				cuenta : cuenta.value,
			}
		});
	}


	</script>

<?php
}
 ?>
