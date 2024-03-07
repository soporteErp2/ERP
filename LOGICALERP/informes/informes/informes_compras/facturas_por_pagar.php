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

	$informe->InformeName			= 'facturas_por_pagar';  //NOMBRE DEL INFORME
	$informe->InformeTitle			= 'Cartera de proveedores'; //TITULO DEL INFORME
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
	arrayProveedores = new Array();
	cuentasPagoCompraConfigurados  = new Array();


	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_cartera_proveedor = new Ext.Window({
		    width       : 720,
		    height      : 550,
		    id          : 'Win_Ventana_configurar_cartera_proveedor',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_compras/wizard_facturas_por_pagar.php',
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
                    handler     : function(){ Win_Ventana_configurar_cartera_proveedor.close() }
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
		arrayProveedores.length                           = 0;

		Win_Ventana_configurar_cartera_proveedor.close();
        ventanaConfigurarInforme();
	}

	function generarHtml(){
		idClientes  = '';
		sqlCheckbox = '';

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
		,	arrayProveedoresJSON          = Array()
		,	arrayCuentasPagoJSON       = Array()
		,	i                          = 0

		if (tipo_fecha_informe=='corte') { MyInformeFiltroFechaInicio=''; }

		arrayProveedores.forEach(function(id_cliente) {  arrayProveedoresJSON[i] = id_cliente; i++;  });
    	arrayProveedoresJSON=JSON.stringify(arrayProveedoresJSON);

    	i = 0;
    	arraycuentasPagoVenta.forEach(function(id_cliente) {  arrayCuentasPagoJSON[i] = id_cliente; i++;  });
    	arrayCuentasPagoJSON=JSON.stringify(arrayCuentasPagoJSON);

		//SI TODO ESTA CHECKED, NO ENVIAMOS NINGUN PARAMETRO
		if (plazo_por_vencer.checked &&
			vencido_1_30.checked	 &&
			vencido_31_60.checked	 &&
			vencido_61_90.checked	 &&
			vencido_mas_90.checked	) {

			localStorage.plazo_por_vencer_facturas_pagar ='true';
			localStorage.vencido_1_30_facturas_pagar     ='true';
			localStorage.vencido_31_60_facturas_pagar    ='true';
			localStorage.vencido_61_90_facturas_pagar    ='true';
			localStorage.vencido_mas_90_facturas_pagar   ='true';

		}else{
			//SINO SE HAN SELECIONADO UNOS Y OTROS NO, EN ESE CASO HACEMOS
			if (plazo_por_vencer.checked) { sqlCheckbox =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=0)' : '' ; localStorage.plazo_por_vencer_facturas_pagar='true'; }else{ localStorage.plazo_por_vencer_facturas_pagar='false'; }
			if (vencido_1_30.checked) { sqlCheckbox     =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<= 30)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND  DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <= 30)' ; localStorage.vencido_1_30_facturas_pagar='true';}else{ localStorage.vencido_1_30_facturas_pagar='false'; }
			if (vencido_31_60.checked) { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=60)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=60)' ; localStorage.vencido_31_60_facturas_pagar='true';}else{ localStorage.vencido_31_60_facturas_pagar='false'; }
			if (vencido_61_90.checked) { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=90)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=90)' ; localStorage.vencido_61_90_facturas_pagar='true';}else{ localStorage.vencido_61_90_facturas_pagar='false'; }
			if (vencido_mas_90.checked) { sqlCheckbox   =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90 )' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90)' ; localStorage.vencido_mas_90_facturas_pagar='true';}else{ localStorage.vencido_mas_90_facturas_pagar='false';}
		}

		// if (sqlCheckbox=='') { alert("Debe seleccionar la o las edades a consultar"); return; }
		if (tipo_fecha_informe=='corte' && MyInformeFiltroFechaFinal=='') { alert("Debe seleccionar la fecha final"); return; }
		else if (tipo_fecha_informe=='rango_fechas' && (MyInformeFiltroFechaInicio=='' || MyInformeFiltroFechaFinal=='') ) { alert("Debe seleccionar las fechas para el informe"); return; }

		Ext.get('RecibidorInforme_facturas_por_pagar').load({
			url     : '../informes/informes/informes_compras/facturas_por_pagar_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				arrayProveedoresJSON       : arrayProveedoresJSON,
				arrayCuentasPagoJSON       : arrayCuentasPagoJSON,
				agrupacion                 : agrupacion,
				tipo_fecha_informe         : tipo_fecha_informe,
				tipo_informe               : tipo_informe,
				sqlCheckbox                : sqlCheckbox,
				sucursal                   : sucursal,
				order_by                   : order+" "+by,
				separador_miles            : separador_miles,
				separador_decimales        : separador_decimales,
			}
		});

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.agrupacion_facturas_pagar                 = agrupacion;
		localStorage.tipo_fecha_informe_facturas_pagar         = tipo_fecha_informe;
		localStorage.tipo_informe_facturas_pagar               = tipo_informe;
		localStorage.MyInformeFiltroFechaFinal_facturas_pagar  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicio_facturas_pagar = MyInformeFiltroFechaInicio;
		localStorage.sucursal_facturas_pagar                   = sucursal;
		localStorage.order_facturas_pagar                      = order;
		localStorage.by_facturas_pagar                         = by;
		localStorage.separador_miles_facturas_pagar            = separador_miles;
		localStorage.separador_decimales_facturas_pagar        = separador_decimales;

		document.getElementById("RecibidorInforme_facturas_por_pagar").style.padding = 20;
	}

	function generarPDF_Excel(tipo_documento){

		idClientes  = '';
		sqlCheckbox = '';

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
		,	arrayProveedoresJSON          = Array()
		,	arrayCuentasPagoJSON       = Array()
		,	i                          = 0

		if (tipo_fecha_informe=='corte') { MyInformeFiltroFechaInicio=''; }

		arrayProveedores.forEach(function(id_cliente) {  arrayProveedoresJSON[i] = id_cliente; i++;  });
    	arrayProveedoresJSON=JSON.stringify(arrayProveedoresJSON);

    	i = 0;
    	arraycuentasPagoVenta.forEach(function(id_cliente) {  arrayCuentasPagoJSON[i] = id_cliente; i++;  });
    	arrayCuentasPagoJSON=JSON.stringify(arrayCuentasPagoJSON);

		//SI TODO ESTA CHECKED, NO ENVIAMOS NINGUN PARAMETRO
		if (plazo_por_vencer.checked &&
			vencido_1_30.checked	 &&
			vencido_31_60.checked	 &&
			vencido_61_90.checked	 &&
			vencido_mas_90.checked	) {

			localStorage.plazo_por_vencer='true';
			localStorage.vencido_1_30='true';
			localStorage.vencido_31_60='true';
			localStorage.vencido_61_90='true';
			localStorage.vencido_mas_90='true';

		}else{
			//SINO SE HAN SELECIONADO UNOS Y OTROS NO, EN ESE CASO HACEMOS
			if (plazo_por_vencer.checked) { sqlCheckbox =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=0)' : '' ; localStorage.plazo_por_vencer='true'; }else{ localStorage.plazo_por_vencer='false'; }
			if (vencido_1_30.checked) { sqlCheckbox     =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<= 30)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND  DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <= 30)' ; localStorage.vencido_1_30='true';}else{ localStorage.vencido_1_30='false'; }
			if (vencido_31_60.checked) { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=60)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=60)' ; localStorage.vencido_31_60='true';}else{ localStorage.vencido_31_60='false'; }
			if (vencido_61_90.checked) { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=90)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=90)' ; localStorage.vencido_61_90='true';}else{ localStorage.vencido_61_90='false'; }
			if (vencido_mas_90.checked) { sqlCheckbox   =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90 )' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90)' ; localStorage.vencido_mas_90='true';}else{ localStorage.vencido_mas_90='false';}
		}

		// if (sqlCheckbox=='') { alert("Debe seleccionar la o las edades a consultar"); return; }
		if (tipo_fecha_informe=='corte' && MyInformeFiltroFechaFinal=='') { alert("Debe seleccionar la fecha final"); return; }
		else if (tipo_fecha_informe=='rango_fechas' && (MyInformeFiltroFechaInicio=='' || MyInformeFiltroFechaFinal=='') ) { alert("Debe seleccionar las fechas para el informe"); return; }

		// Ext.get('RecibidorInforme_facturas_por_pagar').load({
		// 	url     : '../informes/informes/informes_ventas/cartera_edades_Result.php',
		// 	text	: 'Generando Informe...',
		// 	scripts : true,
		// 	nocache : true,
		// 	params  :
		// 	{
		// 		MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
		// 		MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
		// 		nombre_informe             : 'Cartera detallada de un Cliente',
		// 		arrayProveedoresJSON          : arrayProveedoresJSON,
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

		var data   = tipo_documento+`=true&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}&arrayProveedoresJSON=${arrayProveedoresJSON}&arrayCuentasPagoJSON=${arrayCuentasPagoJSON}&agrupacion=${agrupacion}&tipo_fecha_informe=${tipo_fecha_informe}&tipo_informe=${tipo_informe}&sqlCheckbox=${sqlCheckbox}&sucursal=${sucursal}&order_by=${order} ${by}&separador_miles=${separador_miles}&separador_decimales=${separador_decimales}`

		console.log("../informes/informes/informes_ventas/cartera_edades_Result.php?"+data);
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
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaProveedor(${cont})" title="Eliminar Cliente"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            clientesConfigurados[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_cliente_'+cont).innerHTML=fila;
            contCliente++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayProveedores[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arrayProveedores[cont];
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
	function eliminaProveedor(cont){
		delete arrayProveedores[cont];
		delete clientesConfigurados[cont];
		(document.getElementById("row_cliente_"+cont)).parentNode.removeChild(document.getElementById("row_cliente_"+cont));
	}


</script>