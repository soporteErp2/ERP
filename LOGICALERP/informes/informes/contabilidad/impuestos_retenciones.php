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

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$informe->InformeName			=	'impuestos_retenciones';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Impuestos y Retenciones'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	// $informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal_CIR("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal_CIR("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme_CIR()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	$informe->InformeTamano = "CARTA-HORIZONTAL";

	// CHANGE CSS
	$informe->DefaultCls    = 	''; //RESET STYLE CSS
	$informe->HeightToolbar = 	80; //HEIGHT TOOLBAR

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='ventas'){ $informe->AreaInformeQuitaAlto = 230; }

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
	contTercero = 1;

	function generarPDF_Excel_principal_CIR(tipo_documento){

		var fechaFinal_CIR  = '' ;
		var fechaInicio_CIR = '' ;
		var sucursal        = '';
		var idTerceros      = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_CIR.length; i++) {
			if (typeof(arrayTerceros_CIR[i])!="undefined" && arrayTerceros_CIR[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_CIR[i] : idTerceros+','+arrayTerceros_CIR[i] ;
			}
		}

		if (typeof(localStorage.fechaInicio_CIR)!="undefined" && typeof(localStorage.fechaFinal_CIR)!="undefined") {
			if (localStorage.fechaInicio_CIR!='' && localStorage.fechaFinal_CIR) {
				fechaFinal_CIR  = localStorage.fechaFinal_CIR;
				fechaInicio_CIR = localStorage.fechaInicio_CIR;
			}
		}

		if (typeof(localStorage.sucursal_CIR)!="undefined") {
			if (localStorage.sucursal_CIR) {
				sucursal=localStorage.sucursal_CIR;
			}
		}

		window.open("../informes/informes/contabilidad/impuestos_retenciones_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&fechaFinal_CIR="+fechaFinal_CIR+"&fechaInicio_CIR="+fechaInicio_CIR+"&idTerceros="+idTerceros);
	}

	//=====================// VENTANA CONFIGURACION //=====================//
	//*********************************************************************//

	function ventanaConfigurarInforme_CIR(){

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 750,
		    height      : 570,
		    id          : 'Win_Ventana_configurar_informe_facturas',
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
		            opc : 'ventanaConfiguracion_CIR',
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
                                params  : { opc  : 'CIR' }
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
                    handler     : function(){ generarHtml_CIR() }
                },
                // {
                //     xtype       : 'button',
                //     width       : 60,
                //     height      : 56,
                //     text        : 'Exportar<br>PDF',
                //     scale       : 'large',
                //     iconCls     : 'genera_pdf',
                //     iconAlign   : 'top',
                //     handler     : function(){ generarPDF_Excel_CIR('IMPRIME_PDF') }
                // },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel_CIR('IMPRIME_XLS') }
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
		            handler     : function(){ Win_Ventana_configurar_informe_facturas.close() }
		        }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.fechaFinal_CIR                         = "";
		localStorage.fechaInicio_CIR                        = "";
		localStorage.sucursal_CIR                           = "";
		arrayTerceros_CIR.length                            =0;
		Win_Ventana_configurar_informe_facturas.close();
		ventanaConfigurarInforme_CIR();
	}

	//BTN GENERAR - VENTANA CONFIGURACION
	function generarHtml_CIR(){

		var fechaFinal_CIR  = document.getElementById('fechaFinal_CIR').value;
		var fechaInicio_CIR = document.getElementById('fechaInicio_CIR').value;
		var sucursal        = document.getElementById('filtro_sucursal_CIR').value;
		var idTerceros      = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_CIR.length; i++) {
			if (typeof(arrayTerceros_CIR[i])!="undefined" && arrayTerceros_CIR[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_CIR[i] : idTerceros+','+arrayTerceros_CIR[i] ;
			}
		}

		//==================// IMPUESTOS Y RETENCIONES //==================//
		//*****************************************************************//
		var impuestos           = ""
		,	checkImpuestos      = document.querySelectorAll('.check_impuestos_CIR')
		, 	checkTodosImpuestos = document.getElementById('check_todos_impuestos_CIR').checked;

		if(checkTodosImpuestos == false){
			[].forEach.call(checkImpuestos, function(campo) {
			  	if(campo.checked == true){ impuestos += campo.value+','; };
			});
		}

		//====================// TIPOS DE DOCUMENTOS //====================//
		//*****************************************************************//
		var documentos           = ""
		,	checkDocumentos      = document.querySelectorAll('.check_documentos_CIR')
		, 	checkTodosDocumentos = document.getElementById('check_todos_documentos_CIR').checked;

		if(checkTodosDocumentos == false){
			[].forEach.call(checkDocumentos, function(campo) {
			  	if(campo.checked == true){ documentos += campo.value+','; };
			});
		}

		//CHECK AGRUPACION DE TERCEROS
		var agrupar_terceros = (document.getElementById('check_agrupar_terceros_CIR').checked)? 'true': 'false';

		//CHECK AGRUPACION TIPOS DOCUMENTOS
		var agrupar_tipo_documento = (document.getElementById('check_agrupar_documentos_CIR').checked)? 'true': 'false';

		Ext.get('RecibidorInforme_impuestos_retenciones').load({
			url     : '../informes/informes/contabilidad/impuestos_retenciones_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe         : 'impuestos_retenciones',
				sucursal               : sucursal,
				fechaFinal_CIR         : fechaFinal_CIR,
				fechaInicio_CIR        : fechaInicio_CIR,
				idTerceros             : idTerceros,
				impuestos              : impuestos,
				documentos             : documentos,
				agrupar_terceros       : agrupar_terceros,
				agrupar_tipo_documento : agrupar_tipo_documento
			}
		});

		document.getElementById("RecibidorInforme_impuestos_retenciones").style.padding = 20;

		localStorage.fechaFinal_CIR  = fechaFinal_CIR;
		localStorage.fechaInicio_CIR = fechaInicio_CIR;
		localStorage.sucursal_CIR    = sucursal;
	}

	//BTN GENERAR - VENTANA CONFIGURACION
	function generarPDF_Excel_CIR(tipo_documento){

		var fechaFinal_CIR  = document.getElementById('fechaFinal_CIR').value;
		var fechaInicio_CIR = document.getElementById('fechaInicio_CIR').value;
		var sucursal        = document.getElementById('filtro_sucursal_CIR').value;
		var idTerceros      = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayTerceros_CIR.length; i++) {
			if (typeof(arrayTerceros_CIR[i])!="undefined" && arrayTerceros_CIR[i]!="") {
				idTerceros=(idTerceros=='')? arrayTerceros_CIR[i] : idTerceros+','+arrayTerceros_CIR[i] ;
			}
		}

		//==================// IMPUESTOS Y RETENCIONES //==================//
		//*****************************************************************//
		var impuestos           = ""
		,	checkImpuestos      = document.querySelectorAll('.check_impuestos_CIR')
		, 	checkTodosImpuestos = document.getElementById('check_todos_impuestos_CIR').checked;

		if(checkTodosImpuestos == false){
			[].forEach.call(checkImpuestos, function(campo) {
			  	if(campo.checked == true){ impuestos += campo.value+','; };
			});
		}

		//======================// TIPOS DOCUMENTOS //=====================//
		//*****************************************************************//
		var documentos           = ""
		,	checkDocumentos      = document.querySelectorAll('.check_documentos_CIR')
		, 	checkTodosDocumentos = document.getElementById('check_todos_documentos_CIR').checked;

		if(checkTodosDocumentos == false){
			[].forEach.call(checkDocumentos, function(campo) {
			  	if(campo.checked == true){ documentos += campo.value+','; };
			});
		}

		//CHECK AGRUPACION DE TERCEROS
		var agrupar_terceros = (document.getElementById('check_agrupar_terceros_CIR').checked)? 'true': 'false';

		//CHECK AGRUPACION TIPOS DOCUMENTOS
		var agrupar_tipo_documento = (document.getElementById('check_agrupar_documentos_CIR').checked)? 'true': 'false';

		var data = tipo_documento+"=true"
					+"&sucursal="+sucursal
					+"&fechaFinal_CIR="+fechaFinal_CIR
					+"&fechaInicio_CIR="+fechaInicio_CIR
					+"&idTerceros="+idTerceros
					+"&impuestos="+impuestos
					+"&documentos="+documentos
					+"&agrupar_terceros="+agrupar_terceros
					+"&agrupar_tipo_documento="+agrupar_tipo_documento;

		window.open("../informes/informes/contabilidad/impuestos_retenciones_Result.php?"+data);
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTercero_CIR(opc){

        Win_VentanaCliente_terceros_CIR = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_terceros_CIR',
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
					tabla                : 'terceros',
					id_tercero           : 'id',
					tercero              : 'nombre',
					opcGrillaContable 	 : 'impuestos_retenciones',
					cargaFuncion         : '',
					nombre_grilla        : '',
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
                    handler     : function(){ Win_VentanaCliente_terceros_CIR.close(id) }
                }
            ]
        }).show();
	}


	function checkGrilla(checkbox,cont,tabla){

		if (checkbox.checked ==true) {

        	var div = document.createElement('div');

        	div.setAttribute('id','fila_cartera_tercero_'+cont);
        	div.setAttribute('class','filaBoleta');
        	document.getElementById('bodyTablaConfiguracion').appendChild(div);

        	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			var nit     = document.getElementById('nit_'+cont).innerHTML;
			var tercero = document.getElementById('tercero_'+cont).innerHTML;

        	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
        	var fila='<div class="campo0">'+contTercero+'</div><div class="campo1" id="nits_'+cont+'">'+nit+'</div><div class="campo1" id="terceros_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCliente_CIR('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
        	tercerosConfigurados_CIR[cont]=fila;
        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_cartera_tercero_'+cont).innerHTML=fila;
        	contTercero++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	arrayTerceros_CIR[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {

			delete arrayTerceros_CIR[cont];
			delete tercerosConfigurados_CIR[cont];
			(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
		}
	}

	//========================// FUNCION PARA ELIMINAR LOS TERCEROS AGREGADOS //========================//
	//**************************************************************************************************//
	function eliminaCliente_CIR(cont,tabla){

		delete arrayTerceros_CIR[cont];
		delete tercerosConfigurados_CIR[cont];
		(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
	}

	//===========================// HABILITA/DESHABILITA TIPOS DE IMPUESTO //===========================//
	//**************************************************************************************************//
	function check_impuestos_CIR(estadoCheck){
		var estado      = (estadoCheck==true)? false: true
		,	camposCheck = document.querySelectorAll('.check_impuestos_CIR');

		if(estadoCheck == true){ document.getElementById('contenedor_check_impuestos_CIR').style.display = 'none'; }
		else{ document.getElementById('contenedor_check_impuestos_CIR').style.display = 'block'; }

		[].forEach.call(camposCheck, function(campo) {
		  	campo.checked = false;
		});
	}

	//===========================// HABILITA/DESHABILITA TIPOS DOCUMENTOS //===========================//
	//*************************************************************************************************//
	function check_documentos_CIR(estadoCheck){
		var estado      = (estadoCheck==true)? false: true
		,	camposCheck = document.querySelectorAll('.check_documentos_CIR');

		if(estadoCheck == true){ document.getElementById('contenedor_check_documentos_CIR').style.display = 'none'; }
		else{ document.getElementById('contenedor_check_documentos_CIR').style.display = 'block'; }

		[].forEach.call(camposCheck, function(campo) {
		  	campo.checked = false;
		});
	}

</script>