<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa          = $_SESSION['EMPRESA'];
	$id_sucursal_default = $_SESSION['SUCURSAL'];

	$informe->InformeName			= 'contabilidad_cartera_edades';  //NOMBRE DEL INFORME
	$informe->InformeTitle			= 'Cartera detallada de un Cliente'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	= 'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		= 'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	= 'false';	 //FILTRO FECHA

	$informe->InformeExportarPDF	= "false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= "false";	//SI EXPORTA A XLS
	$informe->BtnGenera             = 'false';
	$informe->InformeTamano 		= "CARTA-HORIZONTAL";

	// $informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	// $informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_cartera');

	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='contabilidad'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/
?>

<script>

	contCliente    = 1;
	contCuentaPago = 1;
	arrayClientes = new Array();

	//==========================// PDF Y EXCEL PRINCIPAL //==========================//
	//*******************************************************************************//

	function generarPDF_Excel_principal(tipo_documento){

		idClientes  = '';
		sqlCheckbox = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayClientes.length; i++) {
			if (typeof(arrayClientes[i])!="undefined" && arrayClientes[i]!="") {
				idClientes=(idClientes=='')? arrayClientes[i] : idClientes+','+arrayClientes[i] ;
			}
		}

		//SINO SE HAN SELECIONADO UNOS Y OTROS NO, EN ESE CASO HACEMOS
		if (typeof(localStorage.plazo_por_vencer)=='checked') { sqlCheckbox =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=0)' : '' ;}
		if (typeof(localStorage.vencido_1_30)=='checked') { sqlCheckbox     =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<= 30)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND  DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <= 30)';}
		if (typeof(localStorage.vencido_31_60)=='checked') { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=60)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=60)' ; }
		if (typeof(localStorage.vencido_61_90)=='checked') { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=90)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=90)' ; }
		if (typeof(localStorage.vencido_mas_90)=='checked') { sqlCheckbox   =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90 )' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90)' ; }

		var tipo_informe = (typeof(localStorage.tipo_informe_cartera_edades)!="undefined")? localStorage.tipo_informe_cartera_edades : '' ;
		var sucursal     = (typeof(localStorage.sucursal_cartera_edades)!="undefined")? localStorage.sucursal_cartera_edades : '' ;

		MyInformeFiltroFechaFinal  = (typeof(localStorage.MyInformeFiltroFechaFinalCartera)!='undefined')? localStorage.MyInformeFiltroFechaFinalCartera : '' ;
		MyInformeFiltroFechaInicio = (typeof(localStorage.MyInformeFiltroFechaInicioCartera)!='undefined')? localStorage.MyInformeFiltroFechaInicioCartera : '' ;
		tipo_fecha_informe         = (typeof(localStorage.tipo_fecha_informe)!='undefined')? localStorage.tipo_fecha_informe : '' ;

		var data = tipo_documento+"=true"
					+"&nombre_informe=Cartera por Edades"
					+"&tipo_fecha_informe="+tipo_fecha_informe
					+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
					+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
					+"&idClientes="+idClientes
					+"&sqlFechas="+sqlCheckbox
					+"&sucursal="+sucursal
					+"&tipo_informe="+tipo_informe
					+"&cuenta=";

		window.open("../informes/informes/informes_ventas/cartera_edades_Result.php?"+data);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_cartera_edades = new Ext.Window({
		    width       : 720,
		    height      : 550,
		    id          : 'Win_Ventana_configurar_cartera_edades',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_ventas/wizard_cartera.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionCartera',
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
                                params  : { opc  : 'cartera_edades' }
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
                    handler     : function(){ Win_Ventana_configurar_cartera_edades.close() }
                }
		    ]
		}).show();
	}

	function resetFiltros(){


		localStorage.plazo_por_vencer                  = "";
		localStorage.vencido_1_30                      = "";
		localStorage.vencido_31_60                     = "";
		localStorage.vencido_61_90                     = "";
		localStorage.vencido_mas_90                    = "";
		localStorage.tipo_fecha_informe                = "";
		localStorage.tipo_informe_cartera_edades       = "";
		localStorage.MyInformeFiltroFechaFinalCartera  = "";
		localStorage.MyInformeFiltroFechaInicioCartera = "";
		localStorage.sucursal_cartera_edades           = "";
		arrayClientes.length                           = 0;

		Win_Ventana_configurar_cartera_edades.close();
        ventanaConfigurarInforme();
	}

	function generarHtml(){
		idClientes  = '';

		//RECORREMOS LOS CHECKBOX PARA SABER CUALES FUERON SELECCIONADOS Y ENVIARLOS A LA CONSULTA
		var	plazo_por_vencer           = document.getElementById('plazo_por_vencer')
		,	vencido_1_30               = document.getElementById('vencido_1_30')
		,	vencido_31_60              = document.getElementById('vencido_31_60')
		,	vencido_61_90              = document.getElementById('vencido_61_90')
		,	vencido_mas_90             = document.getElementById('vencido_mas_90')
		,	sucursal                   = document.getElementById('filtro_sucursal_cartera_edades').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	agrupacion                 = document.getElementById("agrupacion").value
		,	tipo_informe               = document.getElementById("tipo_informe").value
		,	tipo_fecha_informe         = document.getElementById("tipo_fecha_informe").value
		,	order                      = document.getElementById('order').value
		,	by                         = document.getElementById('by').value
		,	separador_miles            = document.getElementById('separador_miles').value
		,	separador_decimales        = document.getElementById('separador_decimales').value
		,	arrayClientesJSON          = Array()
		,	arrayCuentasPagoJSON       = Array()
		,	i                          = 0

		if (tipo_fecha_informe=='corte') { MyInformeFiltroFechaInicio=''; }

		arrayClientes.forEach(function(id_cliente) {  arrayClientesJSON[i] = id_cliente; i++;  });
    	arrayClientesJSON=JSON.stringify(arrayClientesJSON);

    	i = 0;
    	arraycuentasPagoVenta.forEach(function(id_cliente) {  arrayCuentasPagoJSON[i] = id_cliente; i++;  });
    	arrayCuentasPagoJSON=JSON.stringify(arrayCuentasPagoJSON);


		const hoy = new Date().toISOString().split('T')[0]; // "YYYY-MM-DD"
		let condiciones = [];
		
		if (plazo_por_vencer.checked) {
		    condiciones.push("VF.dias < 0");
		}
		if (vencido_1_30.checked) {
		    condiciones.push("VF.dias BETWEEN 1 AND  30");
		}
		if (vencido_31_60.checked) {
		    condiciones.push("VF.dias BETWEEN 31 AND  60");
		}
		if (vencido_61_90.checked) {
		    condiciones.push("VF.dias BETWEEN 61 AND  90");
		}
		if (vencido_mas_90.checked) {
		    condiciones.push("VF.dias > 90");
		}
		let sqlCheckbox = '';
		if (condiciones.length > 0) {
		    sqlCheckbox = ` (${condiciones.join(' OR ')}) `;
		}

		// if (sqlCheckbox=='') { alert("Debe seleccionar la o las edades a consultar"); return; }
		if (tipo_fecha_informe=='corte' && MyInformeFiltroFechaFinal=='') { alert("Debe seleccionar la fecha final"); return; }
		else if (tipo_fecha_informe=='rango_fechas' && (MyInformeFiltroFechaInicio=='' || MyInformeFiltroFechaFinal=='') ) { alert("Debe seleccionar las fechas para el informe"); return; }

		Ext.get('RecibidorInforme_contabilidad_cartera_edades').load({
			url     : '../informes/informes/informes_ventas/cartera_edades_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				nombre_informe             : 'Cartera detallada de un Cliente',
				arrayClientesJSON          : arrayClientesJSON,
				arrayCuentasPagoJSON       : arrayCuentasPagoJSON,
				agrupacion                 : agrupacion,
				tipo_fecha_informe         : tipo_fecha_informe,
				tipo_informe               : tipo_informe,
				sqlFechas                : sqlCheckbox,
				sucursal                   : sucursal,
				order_by                   : order+" "+by,
				separador_miles 		   : separador_miles,
				separador_decimales 	   : separador_decimales,
			}
		});

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.agrupacion_cartera_cliente          = agrupacion;
		localStorage.tipo_fecha_informe                  = tipo_fecha_informe;
		localStorage.tipo_informe_cartera_edades         = tipo_informe;
		localStorage.MyInformeFiltroFechaFinalCartera    = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioCartera   = MyInformeFiltroFechaInicio;
		localStorage.sucursal_cartera_edades             = sucursal;
		localStorage.order_cartera_cliente               = order;
		localStorage.by_cartera_cliente                  = by;
		localStorage.separador_miles_cartera_cliente     = separador_miles;
		localStorage.separador_decimales_cartera_cliente = separador_decimales;

		document.getElementById("RecibidorInforme_contabilidad_cartera_edades").style.padding = 20;
	}

	function generarPDF_Excel(tipo_documento){

		idClientes  = '';

		//RECORREMOS LOS CHECKBOX PARA SABER CUALES FUERON SELECCIONADOS Y ENVIARLOS A LA CONSULTA
		var	plazo_por_vencer           = document.getElementById('plazo_por_vencer')
		,	vencido_1_30               = document.getElementById('vencido_1_30')
		,	vencido_31_60              = document.getElementById('vencido_31_60')
		,	vencido_61_90              = document.getElementById('vencido_61_90')
		,	vencido_mas_90             = document.getElementById('vencido_mas_90')
		,	sucursal                   = document.getElementById('filtro_sucursal_cartera_edades').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	agrupacion                 = document.getElementById("agrupacion").value
		,	tipo_informe               = document.getElementById("tipo_informe").value
		,	tipo_fecha_informe         = document.getElementById("tipo_fecha_informe").value
		,	order                      = document.getElementById('order').value
		,	by                         = document.getElementById('by').value
		,	separador_miles            = document.getElementById('separador_miles').value
		,	separador_decimales        = document.getElementById('separador_decimales').value
		,	arrayClientesJSON          = Array()
		,	arrayCuentasPagoJSON       = Array()
		,	i                          = 0

		if (tipo_fecha_informe=='corte') { MyInformeFiltroFechaInicio=''; }

		arrayClientes.forEach(function(id_cliente) {  arrayClientesJSON[i] = id_cliente; i++;  });
    	arrayClientesJSON=JSON.stringify(arrayClientesJSON);

    	i = 0;
    	arraycuentasPagoVenta.forEach(function(id_cliente) {  arrayCuentasPagoJSON[i] = id_cliente; i++;  });
    	arrayCuentasPagoJSON=JSON.stringify(arrayCuentasPagoJSON);


		const hoy = new Date().toISOString().split('T')[0]; // "YYYY-MM-DD"
		let condiciones = [];
		
		if (plazo_por_vencer.checked) {
		    condiciones.push("VF.dias < 0");
		}
		if (vencido_1_30.checked) {
		    condiciones.push("VF.dias BETWEEN 1 AND  30");
		}
		if (vencido_31_60.checked) {
		    condiciones.push("VF.dias BETWEEN 31 AND  60");
		}
		if (vencido_61_90.checked) {
		    condiciones.push("VF.dias BETWEEN 61 AND  90");
		}
		if (vencido_mas_90.checked) {
		    condiciones.push("VF.dias > 90");
		}
		let sqlCheckbox = '';
		if (condiciones.length > 0) {
		    sqlCheckbox = ` (${condiciones.join(' OR ')}) `;
		}

		// if (sqlCheckbox=='') { alert("Debe seleccionar la o las edades a consultar"); return; }
		if (tipo_fecha_informe=='corte' && MyInformeFiltroFechaFinal=='') { alert("Debe seleccionar la fecha final"); return; }
		else if (tipo_fecha_informe=='rango_fechas' && (MyInformeFiltroFechaInicio=='' || MyInformeFiltroFechaFinal=='') ) { alert("Debe seleccionar las fechas para el informe"); return; }

		// Ext.get('RecibidorInforme_contabilidad_cartera_edades').load({
		// 	url     : '../informes/informes/informes_ventas/cartera_edades_Result.php',
		// 	text	: 'Generando Informe...',
		// 	scripts : true,
		// 	nocache : true,
		// 	params  :
		// 	{
		// 		MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
		// 		MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
		// 		nombre_informe             : 'Cartera detallada de un Cliente',
		// 		arrayClientesJSON          : arrayClientesJSON,
		// 		arrayCuentasPagoJSON       : arrayCuentasPagoJSON,
		// 		agrupacion                 : agrupacion,
		// 		tipo_fecha_informe         : tipo_fecha_informe,
		// 		tipo_informe               : tipo_informe,
		// 		sqlCheckbox                : sqlCheckbox,
		// 		sucursal                   : sucursal,
		// 		order_by                   : order+" "+by,
		// 		separador_miles 		   : separador_miles,
		// 		separador_decimales 	   : separador_decimales,
		// 	}
		// });

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		// localStorage.agrupacion_cartera_cliente          = agrupacion;
		// localStorage.tipo_fecha_informe                  = tipo_fecha_informe;
		// localStorage.tipo_informe_cartera_edades         = tipo_informe;
		// localStorage.MyInformeFiltroFechaFinalCartera    = MyInformeFiltroFechaFinal;
		// localStorage.MyInformeFiltroFechaInicioCartera   = MyInformeFiltroFechaInicio;
		// localStorage.sucursal_cartera_edades             = sucursal;
		// localStorage.order_cartera_cliente               = order;
		// localStorage.by_cartera_cliente                  = by;
		// localStorage.separador_miles_cartera_cliente     = separador_miles;
		// localStorage.separador_decimales_cartera_cliente = separador_decimales;

		var data   = tipo_documento+`=true&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}&arrayClientesJSON=${arrayClientesJSON}&arrayCuentasPagoJSON=${arrayCuentasPagoJSON}&agrupacion=${agrupacion}&tipo_fecha_informe=${tipo_fecha_informe}&tipo_informe=${tipo_informe}&sqlFechas=${sqlCheckbox}&sucursal=${sucursal}&order_by=${order} ${by}&separador_miles=${separador_miles}&separador_decimales=${separador_decimales}`

		window.open("../informes/informes/informes_ventas/cartera_edades_Result.php?"+data);
	}

	//==========================// VENTANA PARA BUSCAR LOS TERCEROS //==========================//
	function ventanaBusquedaTercero(){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_<?php echo $opcGrillaContable; ?>',
            title       : 'Clientes',
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
					tabla             : 'ventas_facturas',
					id_tercero        : 'id_cliente',
					tercero           : 'cliente',
					opcGrillaContable : 'cartera_edades',
					cargaFuncion      : '',
					nombre_grilla     : ''
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
                    handler     : function(){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
	}

	function checkGrilla(checkbox,cont){

		if (checkbox.checked ==true) {

			var div   = document.createElement('div');
            div.setAttribute('id','row_cliente_'+cont);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro').appendChild(div);


            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            var nit     = document.getElementById('nit_'+cont).innerHTML
            ,   tercero = document.getElementById('tercero_'+cont).innerHTML;

            var fila = `<div class="row" id="row_cliente_${cont}">
                           <div class="cell" data-col="1">${contCliente}</div>
                           <div class="cell" data-col="2">${nit}</div>
                           <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont})" title="Eliminar Cliente"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            clientesConfigurados[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_cliente_'+cont).innerHTML=fila;
            contCliente++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayClientes[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arrayClientes[cont];
			delete clientesConfigurados[cont];
			(document.getElementById("row_cliente_"+cont)).parentNode.removeChild(document.getElementById("row_cliente_"+cont));
		}
	}

	/**
	 * ventanaBusquedaCuentasPago Buscar por cuenta de pagos
	 */
	function ventanaBusquedaCuentasPago(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();
		whereSql = "";

		arraycuentasPagoVenta.forEach(function(id_cuenta_pago) {  whereSql += (whereSql=='')? ` id<>${id_cuenta_pago}`: ` AND id<>${id_cuenta_pago}`; i++;  });
		whereSql=(whereSql=='')? "" : ` AND (${whereSql}) ` ;
		Win_Ventana_buscarCuentaPago = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_buscarCuentaPago',
		    title       : 'Seleccionar cuenta pago',
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
					nombre_grilla : 'cuentaPagoVentas',
					sql           : ' AND tipo="Venta" '+whereSql,
					cargaFuncion  : 'renderCuentaPago(id)',
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
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_buscarCuentaPago.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	/**
	 * renderCuentaPago Evento doble click al buscar la cuenta de pago
	 * @param  int id id de la cuenta de pago
	 */
	function renderCuentaPago(id) {

		var div   = document.createElement('div');
        div.setAttribute('id','row_cuenta_pago_'+id);
        div.setAttribute('class','row');
        document.getElementById('body_grilla_filtro_cuenta_pago').appendChild(div);


        //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
        var cuenta     = document.getElementById('div_cuentaPagoVentas_cuenta_'+id).innerHTML
        ,   nombre = document.getElementById('div_cuentaPagoVentas_nombre_'+id).innerHTML;

        var fila = `<div class="row" id="row_cuenta_pago_${id}">
                       <div class="cell" data-col="1">${contCuentaPago}</div>
                       <div class="cell" data-col="2">${cuenta}</div>
                       <div class="cell" data-col="3" title="${nombre}">${nombre}</div>
                       <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCuentaPago(${contCuentaPago})" title="Eliminar Cliente"></div>
                    </div>`;

        //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
        cuentasPagoVentaConfigurados[contCuentaPago]=fila;
        //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        document.getElementById('row_cuenta_pago_'+id).innerHTML=fila;
        contCuentaPago++;

        //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        arraycuentasPagoVenta[contCuentaPago]=cuenta;

		Win_Ventana_buscarCuentaPago.close(id);
	}

	function eliminaCuentaPago(cont) {
		delete cuentasPagoVentaConfigurados[cont];
		delete arraycuentasPagoVenta[cont];
		(document.getElementById("row_cuenta_pago_"+cont)).parentNode.removeChild(document.getElementById("row_cuenta_pago_"+cont));
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente(cont){
		delete arrayClientes[cont];
		delete clientesConfigurados[cont];
		(document.getElementById("row_cliente_"+cont)).parentNode.removeChild(document.getElementById("row_cliente_"+cont));
	}


</script>
