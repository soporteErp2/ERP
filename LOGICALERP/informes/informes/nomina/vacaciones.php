<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	include('../../../../misc/MyInforme/class.MyInforme.php');
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$informe->InformeName          = 'vacaciones';  //NOMBRE DEL INFORME
	$informe->InformeTitle         = 'Vacaciones'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode = 'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu     = 'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->BtnGenera            = 'false';
	$informe->InformeExportarPDF   =  "false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS   =  "false";	//SI EXPORTA A XLS
	$informe->InformeTamano 		= "CARTA-HORIZONTAL";
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');


	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	$informe->AreaInformeQuitaAlto = 230;

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
	contTercero=1;

	function generarPDF_Excel_principal(tipo_documento){

		var MyInformeFiltroFechaFinal  = ''
		,	MyInformeFiltroFechaInicio = ''
		,	detalle                    = ''
		,	sucursal                   = ''
		,	arrayEmpleadosJSON         = Array()
		,	i                          = 0


		if (typeof(localStorage.MyInformeFiltroFechaInicioVacaciones)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalVacaciones)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioVacaciones!='' && localStorage.MyInformeFiltroFechaFinalVacaciones) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalVacaciones;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioVacaciones;
			}
		}

		arrayEmpleadosVacaciones.forEach(function(id_empleado) {  arrayEmpleadosJSON[i] = id_empleado; i++; });
        arrayEmpleadosJSON=JSON.stringify(arrayEmpleadosJSON);

		if (typeof(localStorage.sucursal_nomina)!="undefined") {
			if (localStorage.sucursal_nomina) {
				sucursal=localStorage.sucursal_nomina;
			}
		}

		if (typeof(localStorage.detalleVacaciones)!="undefined") {
			if (localStorage.detalleVacaciones) {
				detalle=localStorage.detalleVacaciones;
			}
		}

		var bodyVar = '&nombre_informe=vacaciones'+
						'&sucursal='+sucursal+
						'&MyInformeFiltroFechaFinal='+MyInformeFiltroFechaFinal+
						'&MyInformeFiltroFechaInicio='+MyInformeFiltroFechaInicio+
						'&detalle='+detalle+
						'&arrayEmpleadosJSON='+arrayEmpleadosJSON;
		window.open("../informes/informes/nomina/vacaciones_Result.php?"+tipo_documento+"=true"+bodyVar);
	}

	//===================== FUNCIONES DE LA VENTANA QUE CONFIGURA EL INFORME ==================//
	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 700,
		    height      : 400,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/nomina/wizard_informe_vacaciones.php',
		        scripts : true,
		        // text    : 'cargando...	',
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionNomina',

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
                            height      : 56,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
                                scripts : true,
                                nocache : true,
                                params  : { opc  : 'vacaciones' }
                            }
                        }
                    ]
                },
		        {
		            xtype   : 'buttongroup',
		            columns : 4,
		            title   : 'Generacion de Informe',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Generar Informe',
		                    scale       : 'large',
		                    iconCls     : 'genera_informe',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarHtml() }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Exportar a PDF',
		                    scale       : 'large',
		                    iconCls     : 'genera_pdf',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Exportar a Excel',
		                    scale       : 'large',
		                    iconCls     : 'excel32',
		                    iconAlign   : 'top',
		                    handler     : function(){ generarPDF_Excel('IMPRIME_XLS') }
		                },
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
		        }
		    ]
		}).show();
	}

	function generarHtml(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	detalle                    = document.getElementById('detalle').value
		,	sucursal                   = document.getElementById('filtro_sucursal_vacaciones').value
		,	arrayEmpleadosJSON         = Array()
		,	i                          = 0

		arrayEmpleadosVacaciones.forEach(function(id_empleado) {  arrayEmpleadosJSON[i] = id_empleado; i++; });
        arrayEmpleadosJSON=JSON.stringify(arrayEmpleadosJSON);

		Ext.get('RecibidorInforme_vacaciones').load({
			url     : '../informes/informes/nomina/vacaciones_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				detalle                    : detalle,
				sucursal                   : sucursal,
				arrayEmpleadosJSON         : arrayEmpleadosJSON,
			}
		});

		localStorage.MyInformeFiltroFechaFinalVacaciones  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioVacaciones = MyInformeFiltroFechaInicio;
		localStorage.detalleVacaciones                    = detalle;
		localStorage.arrayEmpleadosJSONVacaciones         = arrayEmpleadosJSON;

		document.getElementById("RecibidorInforme_vacaciones").style.padding = 20;
	}

	function generarPDF_Excel(tipo_documento){
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	detalle                    = document.getElementById('detalle').value
		,	sucursal                   = document.getElementById('filtro_sucursal_vacaciones').value
		,	arrayEmpleadosJSON         = Array()
		,	i                          = 0

		arrayEmpleadosVacaciones.forEach(function(id_empleado) {  arrayEmpleadosJSON[i] = id_empleado; i++; });
        arrayEmpleadosJSON=JSON.stringify(arrayEmpleadosJSON);

		var bodyVar = '&nombre_informe=vacaciones'+
						'&sucursal='+sucursal+
						'&MyInformeFiltroFechaFinal='+MyInformeFiltroFechaFinal+
						'&MyInformeFiltroFechaInicio='+MyInformeFiltroFechaInicio+
						'&detalle='+detalle+
						'&arrayEmpleadosJSON='+arrayEmpleadosJSON;

		window.open("../informes/informes/nomina/vacaciones_Result.php?"+tipo_documento+"=true"+bodyVar);

	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaGrillas(){
		var tabla          = 'empleados'
		,	tercero        = 'nombre'
		,	titulo_ventana = 'Empleados'
		,	url            = '../informes/BusquedaTerceros.php'

        Win_VentanaEmpleados = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaEmpleados',
            title       : titulo_ventana,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : url,
                scripts : true,
                nocache : true,
                params  :
                {
					tabla                : tabla,
					id_tercero           : 'id',
					tercero              : tercero,
					opcGrillaContable 	 : 'vacaciones',
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
                    iconAlign   : 'top',
                    handler     : function(){ Win_VentanaEmpleados.close(id) }
                }
            ]
        }).show();
	}

	//FUNCION DE LA VENTANA DE BUSQUDA DE CLIENTES Y VENDEDORES
	function checkGrilla(checkbox,cont,tabla){

		if (checkbox.checked ==true) {

        	var div   = document.createElement('div');
            div.setAttribute('id','row_tercero_'+cont);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro').appendChild(div);

            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            var nit     = document.getElementById('nit_'+cont).innerHTML
            ,   tercero = document.getElementById('tercero_'+cont).innerHTML;

            var fila = `<div class="row" id="row_tercero_${cont}">
                           <div class="cell" data-col="1">${contTercero}</div>
                           <div class="cell" data-col="2">${nit}</div>
                           <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaEmpleadoVacaciones(${cont})" title="Eliminar Cliente"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            arrayEmpleadosConfiguradosVacaciones[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayEmpleadosVacaciones[cont]=checkbox.value;


		}
		else if (checkbox.checked ==false) {

			delete arrayEmpleadosVacaciones[cont];
			delete arrayEmpleadosConfiguradosVacaciones[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));

		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaEmpleadoVacaciones(cont){
		delete arrayEmpleadosVacaciones[cont];
		delete arrayEmpleadosConfiguradosVacaciones[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}

</script>