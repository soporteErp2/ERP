<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE   	  ///**/
	/**/																						/**/
	/**/					$informe = new MyInforme();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa          						= $_SESSION['EMPRESA'];
	$id_sucursal_default 						= $_SESSION['SUCURSAL'];
	$informe->InformeName						=	'contabilidad_balance_comprobacion';  //NOMBRE DEL INFORME
	$informe->InformeTitle					=	'Balance de Comprobacion'; 						//TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; 															//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false'; 															//FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false'; 															//FILTRO FECHA
	$informe->DefaultCls            = ''; 		 															//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		 															//HEIGHT TOOLBAR
	$informe->InformeExportarPDF		= "false"; 															//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= "false"; 															//SI EXPORTA A XLS
	$informe->InformeTamano 				= "CARTA-HORIZONTAL";
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;

	if($modulo == 'contabilidad'){
		$informe->AreaInformeQuitaAlto = 230;
	}

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_balance_prueba');

	/**//////////////////////////////////////////////////////////////**/
	/**///	        			INICIALIZACION DE LA GRILLA         	  ///**/
	/**/																														/**/
	/**/  	$informe->Link = $link;  			//Conexion a la BD			  /**/
	/**/  	$informe->inicializa($_POST); //Variables POST				  /**/
	/**/  	$informe->GeneraInforme(); 		//Inicializa la Grilla	  /**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contTercero     = 1;
	arraytercerosBC = new Array();

	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		var idTerceros               = ''
		,	whereRangoCuentas          = ''
		,	nivel_cuentas              = document.getElementById('nivel_cuentas_BC').value
		,	cuenta_inicial             = document.getElementById('cuenta_inicial')
		,	cuenta_final               = document.getElementById('cuenta_final')
		,	sucursal                   = document.getElementById('filtro_sucursal_sucursales_balance_comprobacion').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	totalizadoBC               = document.getElementById('totalizadoBC').value
		,	separador_miles            = document.getElementById('separador_milesBC').value
		,	separador_decimales        = document.getElementById('separador_decimalesBC').value
		,	arraytercerosJSON          = Array()
		,	i                          = 0

		if(cuenta_inicial.value != "" && cuenta_final.value != ""){
			whereRangoCuentas = ' AND (CAST(codigo_cuenta AS CHAR) >= "'+cuenta_inicial.value+'" AND CAST(codigo_cuenta AS CHAR) <= "'+cuenta_final.value+'" OR codigo_cuenta LIKE "'+cuenta_inicial.value+'{.}" OR codigo_cuenta LIKE "'+cuenta_final.value+'{.}")';
		}
		else if(cuenta_inicial.value != "" || cuenta_final.value != ""){
			alert("\u00A1Error!\nDigite las dos cuentas para la consulta por rango de cuentas");
			return;
		}

		arraytercerosBC.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON = JSON.stringify(arraytercerosJSON);

		if(checkBoxSelectAllTercerosBC == 'true'){
			arraytercerosJSON = 'todos';
		}

		if(separador_decimales == separador_miles){
			alert("\u00A1Error!\nEl separador de miles y decimales no puede ser igual.");
			return;
		}

		Ext.get('RecibidorInforme_contabilidad_balance_comprobacion').load({
			url     : '../informes/informes/contabilidad/contabilidad_balance_comprobacion_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			timeout : 120000,
			params  :	{
									nivel_cuentas              : nivel_cuentas,
									nombre_informe             : 'Balance de Comprobacion',
									arraytercerosJSON          : arraytercerosJSON,
									whereRangoCuentas          : whereRangoCuentas,
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									sucursal                   : sucursal,
									totalizadoBC			 			   : totalizadoBC,
									separador_miles 		   		 : separador_miles,
									separador_decimales 	   	 : separador_decimales,
									cuentas_cierre             : document.getElementById('incluir_cuentas_cierre_BC').value,
								}
		});

		localStorage.cuenta_inicialBC                              = cuenta_inicial.value;
		localStorage.cuenta_finalBC                                = cuenta_final.value;
		localStorage.nivel_cuentasBC                               = nivel_cuentas;
		localStorage.MyInformeFiltroFechaFinalBalanceComprobacion  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioBalanceComprobacion = MyInformeFiltroFechaInicio;
		localStorage.sucursal_balance_comprobacion                 = sucursal;
		localStorage.totalizadoBC                                  = totalizadoBC;
		localStorage.separador_milesBC                             = separador_miles;
		localStorage.separador_decimalesBC                         = separador_decimales;

		document.getElementById("RecibidorInforme_contabilidad_balance_comprobacion").style.padding = 20;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		var whereRangoCuentas        = ''
		,	nivel_cuentas              = document.getElementById('nivel_cuentas_BC').value
		,	cuenta_inicial             = document.getElementById('cuenta_inicial')
		,	cuenta_final               = document.getElementById('cuenta_final')
		,	sucursal                   = document.getElementById('filtro_sucursal_sucursales_balance_comprobacion').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	totalizadoBC               = document.getElementById('totalizadoBC').value
		,	separador_miles            = document.getElementById('separador_milesBC').value
		,	separador_decimales        = document.getElementById('separador_decimalesBC').value
		,	arraytercerosJSON 				 = Array()
		,	i                 				 = 0

		if(cuenta_inicial.value != "" && cuenta_final.value != ""){
			whereRangoCuentas = ' AND (CAST(codigo_cuenta AS CHAR) >= "'+cuenta_inicial.value+'" AND CAST(codigo_cuenta AS CHAR) <= "'+cuenta_final.value+'" OR codigo_cuenta LIKE "'+cuenta_inicial.value+'{.}" OR codigo_cuenta LIKE "'+cuenta_final.value+'{.}")';
		}
		else if(cuenta_inicial.value != "" || cuenta_final.value != ""){
			alert("\u00A1Error!\nDigite las dos cuentas para la consulta por rango de cuentas");
			return;
		}

		arraytercerosBC.forEach(function(id_tercero){  arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON = JSON.stringify(arraytercerosJSON);

		if(checkBoxSelectAllTercerosBC == 'true'){ arraytercerosJSON = 'todos'; }

		if(separador_decimales == separador_miles){
			alert("\u00A1Error!\nEl separador de miles y decimales no puede ser igual.");
			return;
		}

		var data = tipo_documento+"=true"
							+"&nombre_informe=Balance de Comprobacion"
							+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
							+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
							+"&arraytercerosJSON="+arraytercerosJSON
							+"&whereRangoCuentas="+whereRangoCuentas
							+"&sucursal="+sucursal
							+"&nivel_cuentas="+nivel_cuentas
							+"&totalizadoBC="+totalizadoBC
							+"&separador_miles="+separador_miles
							+"&separador_decimales="+separador_decimales

		window.open("../informes/informes/contabilidad/contabilidad_balance_comprobacion_Result.php?"+data);
	}

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){
		var fecha           = new Date()
		,	dia               = fecha.getDate()
		,	mes               = fecha.getMonth()+1
		,	anio              = fecha.getFullYear()
		,	nivel_cuentas     = ''
		,	sucursal          = ''
		,	arraytercerosJSON = Array()
		,	i                 = 0
		,	totalizadoBC      = ''
 		, whereRangoCuentas = ''

		if(typeof(localStorage.cuenta_inicialBC) != "undefined" && typeof(localStorage.cuenta_finalBC) != "undefined" ){
			if(localStorage.cuenta_inicialBC != "" && localStorage.cuenta_finalBC != ""){
				whereRangoCuentas = ' AND ( CAST(codigo_cuenta AS CHAR) >= "'+localStorage.cuenta_inicialBC +'" AND CAST(codigo_cuenta AS CHAR) <= "'+localStorage.cuenta_finalBC +'" OR codigo_cuenta LIKE "'+localStorage.cuenta_inicialBC +'{.}" OR codigo_cuenta LIKE "'+localStorage.cuenta_finalBC +'{.}")';
			}
		}

		if(typeof(localStorage.MyInformeFiltroFechaInicioBalanceComprobacion) != "undefined" && typeof(localStorage.MyInformeFiltroFechaFinalBalanceComprobacion) != "undefined"){
			if(localStorage.MyInformeFiltroFechaInicioBalanceComprobacion != "" && localStorage.MyInformeFiltroFechaFinalBalanceComprobacion != ""){
				//VARIABLES CON EL RANGO DE FECHAS
				var MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioBalanceComprobacion;
				var MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalBalanceComprobacion;
			}else{
				//VARIABLES CON EL RANGO DE FECHAS
				var MyInformeFiltroFechaInicio = anio + "-01-01";
				var MyInformeFiltroFechaFinal  = anio + "-" + mes + "-" + dia;
			}
		}
		else{
			//VARIABLES CON EL RANGO DE FECHAS
			var MyInformeFiltroFechaInicio = anio + "-01-01";
			var MyInformeFiltroFechaFinal  = anio + "-" + mes + "-" + dia;
		}

		//VARIABLE CON LOS ID DE LOS TERCEROS
		idTerceros='';

		arraytercerosBC.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON = JSON.stringify(arraytercerosJSON);

		if(checkBoxSelectAllTercerosBC == 'true'){ arraytercerosJSON = 'todos'; }

		if(typeof(localStorage.sucursal_balance_comprobacion) != "undefined")
			if(localStorage.sucursal_balance_comprobacion != '')
				sucursal = localStorage.sucursal_balance_comprobacion;

		if(typeof(localStorage.nivel_cuentasBC))
			if(localStorage.nivel_cuentasBC != "")
				nivel_cuentas = localStorage.nivel_cuentasBC

		if(typeof(localStorage.totalizadoBC))
			if(localStorage.totalizadoBC != "")
				totalizadoBC = localStorage.totalizadoBC

		if(typeof(localStorage.separador_milesBC))
			if(localStorage.separador_milesBC != "")
				separador_miles = localStorage.separador_milesBC

		if(typeof(localStorage.separador_decimalesBC))
			if(localStorage.separador_decimalesBC != "")
				separador_decimales = localStorage.separador_decimalesBC

		var data = tipo_documento+"=true"
							+"&nombre_informe=Balance de Comprobacion"
							+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
							+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
							+"&arraytercerosJSON="+arraytercerosJSON
							+"&whereRangoCuentas="+whereRangoCuentas
							+"&nivel_cuentas="+nivel_cuentas
							+"&sucursal="+sucursal
							+"&totalizadoBC="+totalizadoBC
							+"&separador_miles="+separador_miles
							+"&separador_decimales="+separador_decimales

		window.open("../informes/informes/contabilidad/contabilidad_balance_comprobacion_Result.php?" + data);
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_balance_prueba = new Ext.Window({
		    width       : 750,
		    height      : 450,
		    id          : 'Win_Ventana_configurar_balance_prueba',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/wizard_balance_comprobacion.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ventanaBalanceComprobacion',
		        }
		    },
		    tbar        :
		    [

		        {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Sucursal',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 160,
                            height      : 45,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
                                scripts : true,
                                nocache : true,
                                params  : { opc  : 'sucursales_balance_comprobacion' }
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
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel('IMPRIME_XLS') }
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
                    handler     : function(){ Win_Ventana_configurar_balance_prueba.close() }
                }
		    ]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.cuenta_inicialBC                              = "";
		localStorage.cuenta_finalBC                                = "";
		localStorage.separador_milesBC                             = "";
		localStorage.separador_decimalesBC                         = "";
		localStorage.MyInformeFiltroFechaFinalBalanceComprobacion  = "";
		localStorage.MyInformeFiltroFechaInicioBalanceComprobacion = "";
		localStorage.sucursal_balance_comprobacion                 = "";
		arraytercerosBC.length                                     = 0;
		Win_Ventana_configurar_balance_prueba.close();
		ventanaConfigurarInforme();
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
	function ventanaBusquedaTercero(){
		var myalto  = Ext.getBody().getHeight();
				var myancho = Ext.getBody().getWidth();

				Win_VentanaCliente_ = new Ext.Window({
						width       : 650,
						height      : 605,
						id          : 'Win_VentanaCliente_',
						title       : 'Terceros',
						modal       : true,
						autoScroll  : false,
						closable    : false,
						autoDestroy : true,
						autoLoad    :
						{
								url     : '../informes/BusquedaTerceros.php',
								scripts : true,
								nocache : true,
								params  :
								{
					tabla             : 'terceros',
					id_tercero        : 'id',
					tercero           : 'nombre_comercial',
					opcGrillaContable : 'balance_comprobacion',
					cargaFuncion      : '',
					nombre_grilla     : '',
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
										handler     : function(){ Win_VentanaCliente_.close(id) }
								}
						]
				}).show();
	}

	//============================ MOSTRAR TERCEROS ============================//
	function checkGrilla(checkbox,cont){

		if (checkbox.checked ==true) {

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
			var div = document.createElement('div');

            div.setAttribute('id','row_tercero_'+cont);
            div.setAttribute('class','filaBoleta');
            document.getElementById('body_grilla_filtro').appendChild(div);

            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			nit     = document.getElementById('nit_'+cont).innerHTML;
			tercero = document.getElementById('tercero_'+cont).innerHTML;

			var fila = `<div class="row" id="row_tercero_${cont}">
                           <div class="cell" data-col="1">${contTercero}</div>
                           <div class="cell" data-col="2">${nit}</div>
                           <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont})" title="Eliminar Tercero"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            tercerosConfiguradosBC[cont]=fila;

            // tercerosConfiguradosBC[cont]='<div class="campo0">'+contTercero+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCliente('+cont+')" title="Eliminar Cliente"></div>';

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_'+cont).innerHTML=tercerosConfiguradosBC[cont];
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraytercerosBC[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arraytercerosBC[cont];
			delete tercerosConfiguradosBC[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	//============================ ELIMINAR TERCERO ============================//
	function eliminaCliente(cont){
		delete arraytercerosBC[cont];
		delete tercerosConfiguradosBC[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}

	//===================== VENTANA PARA BUSCAR LAS CUENTAS ====================//
	function ventanaBuscarCuentaPuc(campo){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_cuenta_puc_balance_prueba = new Ext.Window({
		    width       : 680,
		    height      : 500,
		    id          : 'Win_Ventana_buscar_cuenta_puc_balance_prueba',
		    title       : 'Consultar la cuenta del Puc',
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
					cargaFuncion : 'renderizaResultadoVentanaPuc(id,"'+campo+'")',
					tabla_puc    : 'puc',
		        }
		    },
		    tbar        :
		    [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_buscar_cuenta_puc_balance_prueba.close() }
                }
		    ]
		}).show();
	}

	//============================= MOSTRAR CUENTAS ============================//
	function renderizaResultadoVentanaPuc(id,campo){
		input       = document.getElementById(campo);
		input.value = document.getElementById('div_buscarCuentaBalancePrueba_cuenta_'+id).innerHTML;

		input.setAttribute("title",document.getElementById('div_buscarCuentaBalancePrueba_descripcion_'+id).innerHTML);
		Win_Ventana_buscar_cuenta_puc_balance_prueba.close();

		input.focus();

	}

	//============================= VALIDAR CUENTAS ============================//
	function validaCuentaPuc(event,input){
		tecla  = input ? event.keyCode : event.which;
		patron = /[^\d]/g;
    if(patron.test(input.value)){ input.value = (input.value).replace(/[^0-9]/g,''); }
	}
</script>
