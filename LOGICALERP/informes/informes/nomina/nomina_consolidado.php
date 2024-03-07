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

	$informe->InformeName			=	'nomina_consolidado';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Nomina Consolidado'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	$informe->BtnGenera             = 'false';
	// $informe->AddFiltroFechaInicioFin('false','true');
	// $informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	// $informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS
	$informe->InformeTamano 		= "CARTA-HORIZONTAL";

	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	// if($modulo=='ventas'){
	$informe->AreaInformeQuitaAlto = 230;
	// }

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
	contVendedores=1;

	//===================== FUNCIONES DE LA VENTANA QUE CONFIGURA EL INFORME ==================//
	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 700,
		    height      : 450,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/nomina/wizard_nomina_consolidado.php',
		        scripts : true,
		        nocache : true,
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
                                params  : { opc  : 'nomina' }
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
		                // {
		                //     xtype       : 'button',
		                //     width       : 60,
		                //     height      : 56,
		                //     text        : 'Exportar a PDF',
		                //     scale       : 'large',
		                //     iconCls     : 'genera_pdf',
		                //     iconAlign   : 'top',
		                //     handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
		                // },
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

		var MyInformeFiltroFechaFinal     = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio    = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                      = document.getElementById('filtro_sucursal_nomina').value
		,	separador_miles               = document.getElementById('separador_miles').value
		,	separador_decimales           = document.getElementById('separador_decimales').value
		,	arrayEmpleadosConsolidadoJSON = Array()
		,	i                             = 0
		// ,	tipo_contrato              = document.getElementById('tipo_contrato').value
		// ,	agrupacion_nomina          = document.getElementById('agrupado').value
		// ,	discrimina_planillas       = document.getElementById('discrimina_planillas').value
		// ,	arrayConceptosJSON         = Array()

		arrayEmpleadosConsolidado.forEach(function(id_tercero) {  arrayEmpleadosConsolidadoJSON[i] = id_tercero; i++; });
        arrayEmpleadosConsolidadoJSON=JSON.stringify(arrayEmpleadosConsolidadoJSON);
			// i = 0
	  //       arrayConceptos.forEach(function(id_centro_costo) {  arrayConceptosJSON[i] = id_centro_costo; i++; });
	  //       arrayConceptosJSON=JSON.stringify(arrayConceptosJSON);

		Ext.get('RecibidorInforme_nomina_consolidado').load({
			url     : '../informes/informes/nomina/nomina_consolidado_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				sucursal                      : sucursal,
				MyInformeFiltroFechaFinal     : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio    : MyInformeFiltroFechaInicio,
				separador_miles               : separador_miles,
				separador_decimales           : separador_decimales,
				arrayEmpleadosConsolidadoJSON : arrayEmpleadosConsolidadoJSON,
				// arrayConceptosJSON        : arrayConceptosJSON,
				// discrimina_planillas      : discrimina_planillas,
				// agrupacion_nomina         : agrupacion_nomina,
				// tipo_contrato             : tipo_contrato,
			}
		});

		document.getElementById("RecibidorInforme_nomina_consolidado").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalNominaConsolidado  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioNominaConsolidado = MyInformeFiltroFechaInicio;
		localStorage.sucursal_nominaConsolidado                  = sucursal;
		localStorage.separador_milesNominaConsolidado            = separador_miles;
		localStorage.separador_decimalesNominaConsolidado        = separador_decimales;
		// localStorage.agrupacion_nomina                = agrupacion_nomina;
		// localStorage.discrimina_planillas             = discrimina_planillas;
		// localStorage.tipo_contrato                    = tipo_contrato;

	}

	function generarPDF_Excel(tipo_documento){
		var MyInformeFiltroFechaFinal     = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio    = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                      = document.getElementById('filtro_sucursal_nomina').value
		,	separador_miles               = document.getElementById('separador_miles').value
		,	separador_decimales           = document.getElementById('separador_decimales').value
		,	arrayEmpleadosConsolidadoJSON = Array()
		,	i                             = 0
		// ,	tipo_contrato              = document.getElementById('tipo_contrato').value
		// ,	agrupacion_nomina          = document.getElementById('agrupado').value
		// ,	discrimina_planillas       = document.getElementById('discrimina_planillas').value
		// ,	arrayConceptosJSON         = Array()

		arrayEmpleadosConsolidado.forEach(function(id_tercero) {  arrayEmpleadosConsolidadoJSON[i] = id_tercero; i++; });
        arrayEmpleadosConsolidadoJSON = JSON.stringify(arrayEmpleadosConsolidadoJSON);
				// i = 0
		  //       arrayConceptos.forEach(function(id_centro_costo) {  arrayConceptosJSON[i] = id_centro_costo; i++; });
		  //       arrayConceptosJSON=JSON.stringify(arrayConceptosJSON);

		var bodyVar = `&sucursal=${sucursal}
						&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
						&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
						&separador_miles=${separador_miles}
						&separador_decimales=${separador_decimales}
						&arrayEmpleadosConsolidadoJSON=${arrayEmpleadosConsolidadoJSON}`;

		window.open("../informes/informes/nomina/nomina_consolidado_Result.php?"+tipo_documento+"=true"+bodyVar);

	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaGrillas(opc){
		if (opc=='empleados') {
			var tabla='empleados';
			var tercero='nombre';
			var titulo_ventana='Empleados';
			var url = '../informes/BusquedaTerceros.php';
		}
		else{
			var tabla='';
			var tercero='';
			var titulo_ventana='Seleccionar Conceptos';
			var url = '../informes/informes/nomina/buscar_concepto.php';
		}

        Win_VentanaCliente_terceros = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_terceros',
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
					opcGrillaContable 	 : 'nomina',
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
                    handler     : function(){ Win_VentanaCliente_terceros.close(id) }
                }
            ]
        }).show();
	}

	//FUNCION DE LA VENTANA DE BUSQUDA DE CLIENTES Y VENDEDORES
	function checkGrilla(checkbox,cont,tabla){

		if (checkbox.checked ==true) {

			var div   = document.createElement('div');
            div.setAttribute('id','row_empleados_'+cont);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro').appendChild(div);


            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            var nit     = document.getElementById('nit_'+cont).innerHTML
            ,   tercero = document.getElementById('tercero_'+cont).innerHTML;

            var fila = `<div class="row" id="row_empleados_${cont}">
                           <div class="cell" data-col="1">${contVendedores}</div>
                           <div class="cell" data-col="2">${nit}</div>
                           <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaRegistroGrilla(${cont},'${tabla}')" title="Eliminar Empleado"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            arrayEmpleadosConsolidadoNomina[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_empleados_'+cont).innerHTML=fila;
            contVendedores++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayEmpleadosConsolidado[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arrayEmpleadosConsolidado[cont];
			delete arrayEmpleadosConsolidadoNomina[cont];
			(document.getElementById("row_empleados_"+cont)).parentNode.removeChild(document.getElementById("row_empleados_"+cont));
		}
	}

	function checkGrillaConceptos(checkbox,cont){

		if (checkbox.checked ==true) {

			var div   = document.createElement('div');
            div.setAttribute('id','row_conceptos_'+cont);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro_conceptos').appendChild(div);


            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            var codigo     = document.getElementById('codigo_concepto_'+cont).innerHTML
            ,   nombre = document.getElementById('descripcion_concepto_'+cont).innerHTML;

            var fila = `<div class="row" id="row_conceptos_${cont}">
                           <div class="cell" data-col="1">${contTercero}</div>
                           <div class="cell" data-col="2">${codigo}</div>
                           <div class="cell" data-col="3" title="${nombre}">${nombre}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaRegistroGrilla(${cont},'Conceptos')" title="Eliminar Concepto"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            arrayConceptosNomina[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_conceptos_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayConceptos[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arrayConceptos[cont];
			delete arrayConceptosNomina[cont];
			(document.getElementById("row_conceptos_"+cont)).parentNode.removeChild(document.getElementById("row_conceptos_"+cont));
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaRegistroGrilla(cont,tabla){
		if (tabla=='empleados') {
			delete arrayEmpleadosConsolidado[cont];
			delete arrayEmpleadosConsolidadoNomina[cont];
			(document.getElementById("row_empleados_"+cont)).parentNode.removeChild(document.getElementById("row_empleados_"+cont));
		}
		else{
			delete arrayConceptos[cont];
			delete arrayConceptosNomina[cont];
			(document.getElementById("row_conceptos_"+cont)).parentNode.removeChild(document.getElementById("row_conceptos_"+cont));
		}
	}

</script>