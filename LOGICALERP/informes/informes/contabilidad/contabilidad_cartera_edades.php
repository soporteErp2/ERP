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

	$informe->InformeName			=	'contabilidad_cartera_edades';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Cartera detallada de un Cliente'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	 //FILTRO FECHA

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_cartera');

	$informe->InformeTamano = "CARTA-HORIZONTAL";

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

	contCliente   = 1;
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
					+"&sqlCheckbox="+sqlCheckbox
					+"&sucursal="+sucursal
					+"&tipo_informe="+tipo_informe
					+"&cuenta=";

		window.open("../informes/informes/contabilidad/contabilidad_cartera_edades_Result.php?"+data);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_cartera_edades = new Ext.Window({
		    width       : 720,
		    height      : 520,
		    id          : 'Win_Ventana_configurar_cartera_edades',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/bd.php',
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
		sqlCheckbox = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayClientes.length; i++) {
			if (typeof(arrayClientes[i])!="undefined" && arrayClientes[i]!="") {
				idClientes=(idClientes=='')? arrayClientes[i] : idClientes+','+arrayClientes[i] ;
			}
		}

		//RECORREMOS LOS CHECKBOX PARA SABER CUALES FUERON SELECCIONADOS Y ENVIARLOS A LA CONSULTA
		plazo_por_vencer = document.getElementById('plazo_por_vencer');
		vencido_1_30	 = document.getElementById('vencido_1_30');
		vencido_31_60	 = document.getElementById('vencido_31_60');
		vencido_61_90	 = document.getElementById('vencido_61_90');
		vencido_mas_90	 = document.getElementById('vencido_mas_90');

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

		var sucursal       = document.getElementById('filtro_sucursal_cartera_edades').value;
		var elementos      = document.getElementsByName("tipo_fecha_informe");
		var elementos_tipo = document.getElementsByName('tipo_informe');
		var tipo_informe   = '';

		for(var i=0; i<elementos_tipo.length; i++) {
			if (elementos_tipo[i].checked) {tipo_informe=elementos_tipo[i].value;}
		}

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_fecha_informe=elementos[i].value;}
		}

		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_fecha_informe=='corte') { MyInformeFiltroFechaInicio=''; }
		else if (tipo_fecha_informe=='rango_fechas') { MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value; }
		else{ return; }

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.tipo_fecha_informe                = tipo_fecha_informe;
		localStorage.tipo_informe_cartera_edades       = tipo_informe;
		localStorage.MyInformeFiltroFechaFinalCartera  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioCartera = MyInformeFiltroFechaInicio;
		localStorage.sucursal_cartera_edades           = sucursal;

		//==================// CUENTAS DE PAGO //==================//
		//*********************************************************//
		var cuenta       = ""
		, 	generalCheck = document.getElementById('check_todas_cuentas_pago_FV').checked
		,	camposCheck  = document.querySelectorAll('.check_cuentas_pago_FV');

		if(generalCheck == false){
			[].forEach.call(camposCheck, function(campo) {
			  	if(campo.checked == true){ cuenta += campo.value+','; };
			});
		}

		Ext.get('RecibidorInforme_contabilidad_cartera_edades').load({
			url     : '../informes/informes/contabilidad/contabilidad_cartera_edades_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				nombre_informe             : 'Cartera detallada de un Cliente',
				idClientes                 : idClientes,
				tipo_fecha_informe         : tipo_fecha_informe,
				tipo_informe               : tipo_informe,
				sqlCheckbox                : sqlCheckbox,
				sucursal                   : sucursal,
				cuenta 					   : cuenta
			}
		});

		document.getElementById("RecibidorInforme_contabilidad_cartera_edades").style.padding = 20;
	}

	function generarPDF_Excel(tipo_documento){

		idClientes  = '';
		sqlCheckbox = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayClientes.length; i++) {
			if (typeof(arrayClientes[i])!="undefined" && arrayClientes[i]!="") {
				idClientes=(idClientes=='')? arrayClientes[i] : idClientes+','+arrayClientes[i] ;
			}
		}

		//RECORREMOS LOS CHECKBOX PARA SABER CUALES FUERON SELECCIONADOS Y ENVIARLOS A LA CONSULTA
		plazo_por_vencer = document.getElementById('plazo_por_vencer');
		vencido_1_30	 = document.getElementById('vencido_1_30');
		vencido_31_60	 = document.getElementById('vencido_31_60');
		vencido_61_90	 = document.getElementById('vencido_61_90');
		vencido_mas_90	 = document.getElementById('vencido_mas_90');

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
		}
		else{
			//SINO SE HAN SELECIONADO UNOS Y OTROS NO, EN ESE CASO HACEMOS
			if (plazo_por_vencer.checked) { sqlCheckbox =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=0)' : '' ;}
			if (vencido_1_30.checked) { sqlCheckbox     =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<= 30)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>0 AND  DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <= 30)';}
			if (vencido_31_60.checked) { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=60)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=60)' ; }
			if (vencido_61_90.checked) { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)<=90)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento) <=90)' ; }
			if (vencido_mas_90.checked) { sqlCheckbox   =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90 )' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",VF.fecha_vencimiento)>90)' ; }
		}

		var sucursal       = document.getElementById('filtro_sucursal_cartera_edades').value;
		var elementos_tipo = document.getElementsByName('tipo_informe');
		var tipo_informe   = '';

		for(var i=0; i<elementos_tipo.length; i++) {
			if (elementos_tipo[i].checked) {tipo_informe=elementos_tipo[i].value;}
		}

		var elementos = document.getElementsByName("tipo_fecha_informe");

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_fecha_informe=elementos[i].value;}
		}

		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_fecha_informe=='corte') { MyInformeFiltroFechaInicio = ''; }
		else if (tipo_fecha_informe=='rango_fechas') { MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value; }
		else{ return; }

		//==================// CUENTAS DE PAGO //==================//
		//*********************************************************//
		var cuenta       = ""
		, 	generalCheck = document.getElementById('check_todas_cuentas_pago_FV').checked
		,	camposCheck  = document.querySelectorAll('.check_cuentas_pago_FV');

		if(generalCheck == false){
			[].forEach.call(camposCheck, function(campo) {
			  	if(campo.checked == true){ cuenta += campo.value+','; };
			});
		}

		var data   = tipo_documento+"=true"
					+"&nombre_informe=Cartera por Edades"
					+"&tipo_fecha_informe="+tipo_fecha_informe
					+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
					+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
					+"&idClientes="+idClientes
					+"&sqlCheckbox="+sqlCheckbox
					+"&sucursal="+sucursal
					+"&tipo_informe="+tipo_informe
					+"&cuenta="+cuenta;

		window.open("../informes/informes/contabilidad/contabilidad_cartera_edades_Result.php?"+data);

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

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
            var div   = document.createElement('div');
            div.setAttribute('id','fila_cartera_cliente_'+cont);
            div.setAttribute('class','filaBoleta');
            document.getElementById('bodyTablaConfiguracion').appendChild(div);

            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			nit     = document.getElementById('nit_'+cont).innerHTML;
			tercero = document.getElementById('tercero_'+cont).innerHTML;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            clientesConfigurados[cont]='<div class="campo0">'+contCliente+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;" onclick="eliminaCliente('+cont+')" title="Eliminar Tercero"></div>';
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('fila_cartera_cliente_'+cont).innerHTML=clientesConfigurados[cont];
            contCliente++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayClientes[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arrayClientes[cont];
			delete clientesConfigurados[cont];
			(document.getElementById("fila_cartera_cliente_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_cliente_"+cont));
		}

	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente(cont){
		delete arrayClientes[cont];
		delete clientesConfigurados[cont];
		(document.getElementById("fila_cartera_cliente_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_cliente_"+cont));
	}

	//===========================// HABILITA/DESHABILITA CUENTAS DE PAGO //===========================//
	//************************************************************************************************//
	function check_cuentas_pago_FV(estadoCheck){
		var estado      = (estadoCheck==true)? false: true
		,	camposCheck = document.querySelectorAll('.check_cuentas_pago_FV');

		if(estadoCheck == true){ document.getElementById('contenedor_check_cuentas_pago_FV').style.display = 'none'; }
		else{ document.getElementById('contenedor_check_cuentas_pago_FV').style.display = 'block'; }

		[].forEach.call(camposCheck, function(campo) {
		  	campo.checked = false;
		});
	}

</script>