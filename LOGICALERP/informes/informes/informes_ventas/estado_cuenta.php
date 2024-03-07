<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 		INICIALIZACION DE LA CLASE  	  ///**/
	/**/																						/**/
	/**/					$informe = new MyInforme();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa          = $_SESSION['EMPRESA'];
	$id_sucursal_default = $_SESSION['SUCURSAL'];

	$informe->InformeName						=	'estado_cuenta';  	//NOMBRE DEL INFORME
	$informe->InformeTitle					=	'Estado De Cuenta'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; 						//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false'; 						//FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	 					//FILTRO FECHA
	$informe->InformeExportarPDF		= 'false';						//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= 'false';						//SI EXPORTA A XLS
	$informe->BtnGenera             = 'false';						//BOTON PARA GENERAR INFORME
	$informe->InformeTamano 				= "CARTA-HORIZONTAL";	//TAMAÑO DEL INFORME
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Enviar Informe','enviar','enviarInforme()','Btn_enviar');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_cartera');

	// CHANGE CSS
	$informe->DefaultCls            = ''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;

	if($modulo == 'contabilidad'){
		$informe->AreaInformeQuitaAlto = 230;
	}

	/**//////////////////////////////////////////////////////////////**/
	/**///								INICIALIZACION DE LA GRILLA	  				  ///**/
	/**/																														/**/
	/**/			$informe->Link = $link;  			//Conexion a la BD		  /**/
	/**/			$informe->inicializa($_POST);	//variables POST			  /**/
	/**/		  $informe->GeneraInforme(); 		//Inicializa la Grilla  /**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>

	//============================ GENERAR INFORME =============================//
	function generarHtml(){

		var MyInformeFiltroFechaFinalEC = document.getElementById('MyInformeFiltroFechaFinal').value
	 	, clienteEC	  				  				= document.getElementById('id_cliente').value
		, documento_clienteEC 					= document.getElementById('documento_cliente').innerHTML
		, nombre_clienteEC  						= document.getElementById('nombre_cliente').innerHTML
		, sucursalEC        						= document.getElementById('filtro_sucursal_estado_cuenta').value
		, plazo_por_vencerEC  					= document.getElementById('plazo_por_vencer')
		, vencido_1_30EC 	 					  	= document.getElementById('vencido_1_30')
		, vencido_31_60EC 	 						= document.getElementById('vencido_31_60')
		, vencido_61_90EC 	 						= document.getElementById('vencido_61_90')
		, vencido_mas_90EC 	 					  = document.getElementById('vencido_mas_90')
		, estructura_informeEC					= document.getElementsByName('tipo_informe')
		, fecha_corteEC 								= document.getElementsByName('tipo_fecha_informe')
		, generalCheck 									= document.getElementById('check_todas_cuentas_pago_FV').checked
		,	camposCheck  									= document.querySelectorAll('.check_cuentas_pago_FV')
		, tipo_informeEC 								= ''
		, tipo_fecha_informeEC 					= ''
		, cuentaEC   										= ''
		, sqlCheckbox 									= ''

		if(MyInformeFiltroFechaFinalEC == "" || clienteEC == ""){
			alert("Faltan filtros por completar");
			return;
		}

		//SI TODOS LOS CHECKBOX HAN SIDO SELECCIONADOS, NO ENVIAMOS NINGUN PARAMETRO
		if(plazo_por_vencer.checked && vencido_1_30.checked	&& vencido_31_60.checked &&	vencido_61_90.checked	&& vencido_mas_90.checked){
			localStorage.plazo_por_vencer = 'true';
			localStorage.vencido_1_30			= 'true';
			localStorage.vencido_31_60		= 'true';
			localStorage.vencido_61_90		= 'true';
			localStorage.vencido_mas_90		= 'true';
		}
		else{
			//SINO SE HAN SELECIONADO UNOS Y OTROS NO, EN ESE CASO HACEMOS
			if(plazo_por_vencer.checked){ sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=0)' : '' ; localStorage.plazo_por_vencer='true'; }else{ localStorage.plazo_por_vencer='false'; }
			if(vencido_1_30.checked) {    sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<= 30)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND  DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=30)' ; localStorage.vencido_1_30='true';}else{ localStorage.vencido_1_30='false'; }
			if(vencido_31_60.checked){    sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=60)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=60)' ; localStorage.vencido_31_60='true';}else{ localStorage.vencido_31_60='false'; }
			if(vencido_61_90.checked){    sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=90)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=90)' ; localStorage.vencido_61_90='true';}else{ localStorage.vencido_61_90='false'; }
			if(vencido_mas_90.checked){   sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90 )' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90)' ; localStorage.vencido_mas_90='true';}else{ localStorage.vencido_mas_90='false';}
		}

		//RECORRER LAS OPCIONES DE ESTRUCTURA PARA SABER CUAL FUE SELECCIONADA
		for(i = 0; i < estructura_informeEC.length; i++){
			if(estructura_informeEC[i].checked){
				tipo_informeEC = estructura_informeEC[i].value;
			}
		}

		//RECORRER LAS OPCIONES DE FECHA DE CORTE PARA SABER CUAL FUE SELECCIONADA
		for(i = 0; i < fecha_corteEC.length; i++){
			if(fecha_corteEC[i].checked){
				tipo_fecha_informeEC = fecha_corteEC[i].value;
			}
		}

		//OBTENER FECHA INICIAL
		if(tipo_fecha_informeEC == 'corte'){
			MyInformeFiltroFechaInicioEC = '';
		}
		else if(tipo_fecha_informeEC == 'rango_fechas'){
			MyInformeFiltroFechaInicioEC = document.getElementById('MyInformeFiltroFechaInicio').value;
		}

		//OBTENER CUENTAS DE PAGO
		if(generalCheck == false){
			[].forEach.call(camposCheck, function(campo) {
			  if(campo.checked == true){
					cuentaEC += campo.value + ',';
				}
			});
		}

		Ext.get('RecibidorInforme_estado_cuenta').load({
			url     : '../informes/informes/informes_ventas/estado_cuenta_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  : {
									IMPRIME_HTML							 : 'true',
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinalEC,
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicioEC,
									sucursal                   : sucursalEC,
									cliente                 	 : clienteEC,
									tipo_fecha_informe         : tipo_fecha_informeEC,
									tipo_informe               : tipo_informeEC,
									sqlCheckbox                : sqlCheckbox,
									cuenta 					   				 : cuentaEC,
									nombre_informe             : 'Estado De Cuenta'
								}
		});
		
		document.getElementById("RecibidorInforme_estado_cuenta").style.padding = 20;

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.MyInformeFiltroFechaFinalEC  = MyInformeFiltroFechaFinalEC;
		localStorage.MyInformeFiltroFechaInicioEC = MyInformeFiltroFechaInicioEC;
		localStorage.sucursalEC          					= sucursalEC;
		localStorage.clienteEC 										= clienteEC;
		localStorage.documento_clienteEC 					= documento_clienteEC;
		localStorage.nombre_clienteEC 						= nombre_clienteEC;
		localStorage.tipo_informeEC       				= tipo_informeEC;
		localStorage.tipo_fecha_informeEC 				= tipo_fecha_informeEC;
		localStorage.sqlCheckbox									= sqlCheckbox;
		localStorage.cuentaEC											= cuentaEC;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){

		var MyInformeFiltroFechaFinalEC = document.getElementById('MyInformeFiltroFechaFinal').value
	 	, clienteEC	  				  				= document.getElementById('id_cliente').value
		, documento_clienteEC 					= document.getElementById('documento_cliente').innerHTML
		, nombre_clienteEC  						= document.getElementById('nombre_cliente').innerHTML
		, sucursalEC        						= document.getElementById('filtro_sucursal_estado_cuenta').value
		, plazo_por_vencerEC  					= document.getElementById('plazo_por_vencer')
		, vencido_1_30EC 	 					  	= document.getElementById('vencido_1_30')
		, vencido_31_60EC 	 						= document.getElementById('vencido_31_60')
		, vencido_61_90EC 	 						= document.getElementById('vencido_61_90')
		, vencido_mas_90EC 	 					  = document.getElementById('vencido_mas_90')
		, estructura_informeEC					= document.getElementsByName('tipo_informe')
		, fecha_corteEC 								= document.getElementsByName('tipo_fecha_informe')
		, generalCheck 									= document.getElementById('check_todas_cuentas_pago_FV').checked
		,	camposCheck  									= document.querySelectorAll('.check_cuentas_pago_FV')
		, tipo_informeEC 								= ''
		, tipo_fecha_informeEC 					= ''
		, cuentaEC   										= ''
		, sqlCheckbox 									= ''

		if(MyInformeFiltroFechaFinalEC == "" || clienteEC == ""){
			alert("Faltan filtros por completar");
			return;
		}

		//SI TODOS LOS CHECKBOX HAN SIDO SELECCIONADOS, NO ENVIAMOS NINGUN PARAMETRO
		if(plazo_por_vencer.checked && vencido_1_30.checked	&& vencido_31_60.checked &&	vencido_61_90.checked	&& vencido_mas_90.checked){
			localStorage.plazo_por_vencer = 'true';
			localStorage.vencido_1_30			= 'true';
			localStorage.vencido_31_60		= 'true';
			localStorage.vencido_61_90		= 'true';
			localStorage.vencido_mas_90		= 'true';
		}
		else{
			//SINO SE HAN SELECIONADO UNOS Y OTROS NO, EN ESE CASO HACEMOS
			if(plazo_por_vencer.checked){ sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=0)' : '' ; localStorage.plazo_por_vencer='true'; }else{ localStorage.plazo_por_vencer='false'; }
			if(vencido_1_30.checked) {    sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<= 30)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND  DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=30)' ; localStorage.vencido_1_30='true';}else{ localStorage.vencido_1_30='false'; }
			if(vencido_31_60.checked){    sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=60)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=60)' ; localStorage.vencido_31_60='true';}else{ localStorage.vencido_31_60='false'; }
			if(vencido_61_90.checked){    sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=90)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=90)' ; localStorage.vencido_61_90='true';}else{ localStorage.vencido_61_90='false'; }
			if(vencido_mas_90.checked){   sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90 )' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90)' ; localStorage.vencido_mas_90='true';}else{ localStorage.vencido_mas_90='false';}
		}

		//RECORRER LAS OPCIONES DE ESTRUCTURA PARA SABER CUAL FUE SELECCIONADA
		for(i = 0; i < estructura_informeEC.length; i++){
			if(estructura_informeEC[i].checked){
				tipo_informeEC = estructura_informeEC[i].value;
			}
		}

		//RECORRER LAS OPCIONES DE FECHA DE CORTE PARA SABER CUAL FUE SELECCIONADA
		for(i = 0; i < fecha_corteEC.length; i++){
			if(fecha_corteEC[i].checked){
				tipo_fecha_informeEC = fecha_corteEC[i].value;
			}
		}

		//OBTENER FECHA INICIAL
		if(tipo_fecha_informeEC == 'corte'){
			MyInformeFiltroFechaInicioEC = '';
		}
		else if(tipo_fecha_informeEC == 'rango_fechas'){
			MyInformeFiltroFechaInicioEC = document.getElementById('MyInformeFiltroFechaInicio').value;
		}

		//OBTENER CUENTAS DE PAGO
		if(generalCheck == false){
			[].forEach.call(camposCheck, function(campo) {
			  if(campo.checked == true){
					cuentaEC += campo.value + ',';
				}
			});
		}

		var data   = tipo_documento+"=true"
								+ "&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinalEC
								+ "&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicioEC
								+ "&sucursal="+sucursalEC
								+ "&cliente="+clienteEC
								+ "&tipo_fecha_informe="+tipo_fecha_informeEC
								+ "&tipo_informe="+tipo_informeEC
								+ "&sqlCheckbox="+sqlCheckbox
								+ "&cuenta="+cuentaEC;

		window.open("../informes/informes/informes_ventas/estado_cuenta_Result.php?"+data);

		document.getElementById("RecibidorInforme_estado_cuenta").style.padding = 20;

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.MyInformeFiltroFechaFinalEC  = MyInformeFiltroFechaFinalEC;
		localStorage.MyInformeFiltroFechaInicioEC = MyInformeFiltroFechaInicioEC;
		localStorage.sucursalEC          					= sucursalEC;
		localStorage.clienteEC 										= clienteEC;
		localStorage.documento_clienteEC 					= documento_clienteEC;
		localStorage.nombre_clienteEC 						= nombre_clienteEC;
		localStorage.tipo_informeEC       				= tipo_informeEC;
		localStorage.tipo_fecha_informeEC 				= tipo_fecha_informeEC;
		localStorage.sqlCheckbox									= sqlCheckbox;
		localStorage.cuentaEC											= cuentaEC;
	}

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){

		if(localStorage.MyInformeFiltroFechaFinalEC == "" || localStorage.clienteEC == ""){
			alert("Debe generar el informe al menos una vez");
			return;
		}

		var data   = tipo_documento+"=true"
								+"&MyInformeFiltroFechaFinal="+localStorage.MyInformeFiltroFechaFinalEC
								+"&MyInformeFiltroFechaInicio="+localStorage.MyInformeFiltroFechaInicioEC
								+"&sucursal="+localStorage.sucursalEC
								+"&cliente="+localStorage.clienteEC
								+"&tipo_fecha_informe="+localStorage.tipo_fecha_informeEC
								+"&tipo_informe="+localStorage.tipo_informeEC
								+"&sqlCheckbox="+localStorage.sqlCheckbox
								+"&cuenta="+localStorage.cuentaEC;

		window.open("../informes/informes/informes_ventas/estado_cuenta_Result.php?"+data);

		document.getElementById("RecibidorInforme_estado_cuenta").style.padding = 20;
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_estado_cuenta = new Ext.Window({
		    width       : 605,
		    height      : 500,
		    id          : 'Win_Ventana_configurar_estado_cuenta',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    : {
								        url     : '../informes/informes/informes_ventas/wizard_estado_cuenta.php',
								        scripts : true,
								        nocache : true
									    },
		    tbar        : [
								        {
			                    xtype   : 'buttongroup',
			                    columns : 3,
			                    title   : 'Filtro Sucursal',
			                    items   : [
																			{
																				xtype       : 'panel',
																				border      : false,
																				width       : 160,
																				height      : 45,
																				bodyStyle   : 'background-color:rgba(255,255,255,0);',
																				autoLoad    :	{
																												url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
																												scripts : true,
																												nocache : true,
																												params  : { opc  : 'estado_cuenta' }
																											}
																			}
								                    ]
			                	},
				                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Generar<br>Informe',
			                    scale       : 'large',
			                    iconCls     : 'genera_informe',
			                    iconAlign   : 'top',
			                    handler     : function(){ generarHtml() }
				                },'-',
				                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Exportar<br>PDF',
			                    scale       : 'large',
			                    iconCls     : 'genera_pdf',
			                    iconAlign   : 'top',
			                    handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
				                },'-',
												{
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Enviar<br>Informe',
			                    scale       : 'large',
			                    iconCls     : 'enviar',
			                    iconAlign   : 'top',
			                    handler     : function(){ enviarInforme() }
				                },'-',
				                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Reiniciar<br>Filtros',
			                    scale       : 'large',
			                    iconCls     : 'restaurar',
			                    iconAlign   : 'top',
			                    handler     : function(){ resetFiltros() }
				                },'-',
				                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_configurar_estado_cuenta.close() }
				                }
		    							]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){

		localStorage.plazo_por_vencer 						= 'false';
		localStorage.vencido_1_30									= 'false';
		localStorage.vencido_31_60								= 'false';
		localStorage.vencido_61_90								= 'false';
		localStorage.vencido_mas_90								= 'false';
		localStorage.MyInformeFiltroFechaFinalEC  = '';
		localStorage.MyInformeFiltroFechaInicioEC = '';
		localStorage.sucursalEC          					= 'global';
		localStorage.clienteEC 										= '';
		localStorage.documento_clienteEC 					= '';
		localStorage.nombre_clienteEC 						= '';
		localStorage.sqlCheckbox									= '';
		localStorage.cuentaEC											= '';

		Win_Ventana_configurar_estado_cuenta.close();
    ventanaConfigurarInforme();
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
	function ventanaBusquedaTercero(opc){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		if(opc == 'empleados'){
			tabla = 'empleados';
			tercero = 'nombre';
			titulo_ventana = 'Empleados';
		}	else{
			tabla = 'terceros';
			tercero = 'nombre_comercial';
			titulo_ventana = 'Clientes';
		}

	    Win_VentanaCliente_terceros = new Ext.Window({
	      width       : myancho - 100,
	      height      : myalto - 50,
	      id          : 'Win_VentanaCliente_terceros',
	      title       : titulo_ventana,
	      modal       : true,
	      autoScroll  : false,
	      closable    : false,
	      autoDestroy : true,
	      autoLoad    : {
					              url     : '../funciones_globales/grillas/BusquedaTerceros.php',
					              scripts : true,
					              nocache : true,
					              params  : {
																		sql						: '',
																		cargaFuncion  : 'agregarCliente(id)',
																		nombre_grilla : 'estado_cuenta'
										              }
					            },
	      tbar        : [
				                {
			                    xtype       : 'button',
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'left',
			                    handler     : function(){ Win_VentanaCliente_terceros.close(id) }
				                }
					            ]
	    }).show();
	}

	//============================= MOSTRAR CLIENTE ============================//
	function agregarCliente(id){
		var documento 	= document.getElementById('div_estado_cuenta_numero_identificacion_' + id).innerHTML;
		var nombre 			= document.getElementById('div_estado_cuenta_nombre_comercial_' + id).innerHTML;

		document.getElementById('id_cliente').value = id;
		document.getElementById('documento_cliente').innerHTML = documento;
		document.getElementById('nombre_cliente').innerHTML = nombre;
		Win_VentanaCliente_terceros.close();
		return;
	}

	//============================= ENVIAR INFORME =============================//
	function enviarInforme(){

		var myalto  = Ext.getBody().getHeight()
		,   myancho = Ext.getBody().getWidth()

		if(localStorage.MyInformeFiltroFechaFinalEC == "" || localStorage.clienteEC == ""){
			alert("Debe generar el informe al menos una vez.");
			return;
		}

		data = JSON.stringify({
														'IMPRIME_PDF' : 'true',
														'MyInformeFiltroFechaFinal' : localStorage.MyInformeFiltroFechaFinalEC,
														'MyInformeFiltroFechaInicio' : localStorage.MyInformeFiltroFechaInicioEC,
														'sucursal' : localStorage.sucursalEC,
														'cliente' : localStorage.clienteEC,
														'tipo_fecha_informe' : localStorage.tipo_fecha_informeEC,
														'tipo_informe' : localStorage.tipo_informeEC,
														'sqlCheckbox' : localStorage.sqlCheckbox,
														'cuenta' : localStorage.cuentaEC,
														'nombre_informe' : 'Estado De Cuenta',
														'GUARDAR_PDF' : 'true'
													}, null);																				
		

		if(localStorage.MyInformeFiltroFechaFinalEC == "" || localStorage.clienteEC == ""){
			alert("Faltan filtros por completar");
			return;
		}

		Win_Ventana_enviar_informe = new Ext.Window({
			id          : 'Win_Ventana_enviar_informe',
			title       : 'Enviar Informe Estado De Cuenta',
			iconCls     : 'pie2',
			width       : 920,
			height      : myalto - 50,
			modal       : true,
			autoDestroy : true,
			draggable   : false,
			resizable   : true,
			bodyStyle   : 'background-color:#DFE8F6;',
			autoLoad    : {
											url     : "../../LOGICALERP/informes/EnviarInforme.php",
											scripts : true,
											nocache : true,
											params  :	{
																	id_cliente  		: localStorage.clienteEC,
																	nombre_cliente 	: localStorage.nombre_clienteEC,
																	nombre_informe	: '<?php echo $informe->InformeName; ?>',
																	url_result			: 'informes/informes_ventas/estado_cuenta_Result.php',
																	data						: data
																}
										}
		}).show();
	}

	function check_cuentas_pago_FV(estadoCheck){
		var estado    = (estadoCheck == true)? false: true
		,	camposCheck = document.querySelectorAll('.check_cuentas_pago_FV');

		if(estadoCheck == true){ 
			document.getElementById('contenedor_check_cuentas_pago_FV').style.display = 'none'; 
		}
		else{ 
			document.getElementById('contenedor_check_cuentas_pago_FV').style.display = 'block'; 
		}

		[].forEach.call(camposCheck, function(campo) {
		  	campo.checked = false;
		});
	}
</script>
