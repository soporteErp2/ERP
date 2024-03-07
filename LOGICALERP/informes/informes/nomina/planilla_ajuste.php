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

	$informe->InformeName			=	'planilla_ajuste';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Planilla de Ajuste'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

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

	function generarPDF_Excel_principal(tipo_documento){

		var MyInformeFiltroFechaFinal  = '' ;
		var MyInformeFiltroFechaInicio = '' ;
		var sucursal   = '';
		var idConceptos = '';
		var idEmpleados = '';
		var discrimina_planillas_PlanillaAjuste = '';
		var agrupacion_PlanillaAjuste = '';

		//RECORREMOS EL ARRAY DE LOS CONCEPTOS PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayConceptosPlanillaAjuste.length; i++) {
			if (typeof(arrayConceptosPlanillaAjuste[i])!="undefined" && arrayConceptosPlanillaAjuste[i]!="") {
				idConceptos=(idConceptos=='')? arrayConceptosPlanillaAjuste[i] : idConceptos+','+arrayConceptosPlanillaAjuste[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS EMPLEADOS PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayEmpleadosPlanillaAjuste.length; i++) {
			if (typeof(arrayEmpleadosPlanillaAjuste[i])!="undefined" && arrayEmpleadosPlanillaAjuste[i]!="") {
				idEmpleados=(idEmpleados=='')? arrayEmpleadosPlanillaAjuste[i] : idEmpleados+','+arrayEmpleadosPlanillaAjuste[i] ;
			}

		}

		if (typeof(localStorage.MyInformeFiltroFechaInicioPlanillaAjuste)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalPlanillaAjuste)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioPlanillaAjuste!='' && localStorage.MyInformeFiltroFechaFinalPlanillaAjuste) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalPlanillaAjuste;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioPlanillaAjuste;
			}
		}

		if (typeof(localStorage.sucursal_PlanillaAjuste)!="undefined") {
			if (localStorage.sucursal_PlanillaAjuste) {
				sucursal=localStorage.sucursal_PlanillaAjuste;
			}
		}

		if (typeof(localStorage.discrimina_planillas_PlanillaAjuste)!="undefined") {
			if (localStorage.discrimina_planillas_PlanillaAjuste) {
				discrimina_planillas_PlanillaAjuste=localStorage.discrimina_planillas_PlanillaAjuste;
			}
		}

		if (typeof(localStorage.agrupacion_PlanillaAjuste)!="undefined") {
			if (localStorage.agrupacion_PlanillaAjuste) {
				agrupacion_PlanillaAjuste=localStorage.agrupacion_PlanillaAjuste;
			}
		}

		var bodyVar = '&nombre_informe=Planilla de Ajuste'+
						'&sucursal='+sucursal+
						'&MyInformeFiltroFechaFinal='+MyInformeFiltroFechaFinal+
						'&MyInformeFiltroFechaInicio='+MyInformeFiltroFechaInicio+
						'&idConceptos='+idConceptos+
						'&idEmpleados='+idEmpleados+
						'&discrimina_planillas_PlanillaAjuste='+discrimina_planillas_PlanillaAjuste+
						'&agrupacion_PlanillaAjuste='+agrupacion_PlanillaAjuste;

		window.open("../informes/informes/nomina/planilla_ajuste_Result.php?"+tipo_documento+"=true"+bodyVar);
	}

	//===================== FUNCIONES DE LA VENTANA QUE CONFIGURA EL INFORME ==================//

	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 560,
		    height      : 560,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/nomina/bd.php',
		        scripts : true,
		        // text    : 'cargando...	',
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionPlanillaAjuste',

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
                                params  : { opc  : 'PlanillaAjuste' }
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

		var MyInformeFiltroFechaFinal  =document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal   = document.getElementById('filtro_sucursal_PlanillaAjuste').value;
		var idConceptos = '';
		var idEmpleados = '';

		//RECORREMOS EL ARRAY DE LOS CONCEPTOS PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayConceptosPlanillaAjuste.length; i++) {
			if (typeof(arrayConceptosPlanillaAjuste[i])!="undefined" && arrayConceptosPlanillaAjuste[i]!="") {
				idConceptos=(idConceptos=='')? arrayConceptosPlanillaAjuste[i] : idConceptos+','+arrayConceptosPlanillaAjuste[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS EMPLEADOS PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayEmpleadosPlanillaAjuste.length; i++) {
			if (typeof(arrayEmpleadosPlanillaAjuste[i])!="undefined" && arrayEmpleadosPlanillaAjuste[i]!="") {
				idEmpleados=(idEmpleados=='')? arrayEmpleadosPlanillaAjuste[i] : idEmpleados+','+arrayEmpleadosPlanillaAjuste[i] ;
			}

		}

		// RECORRER LA AGRUPACION DEL INFORME
		var elementos = document.getElementsByName("agrupado");

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {agrupacion_PlanillaAjuste=elementos[i].value;}
		}

		//
		var discrimina_planillas_PlanillaAjuste = (document.getElementById('discrimina_planillas_PlanillaAjuste').checked)? true : false ;

		Ext.get('RecibidorInforme_planilla_ajuste').load({
			url     : '../informes/informes/nomina/planilla_ajuste_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe                      : 'Planilla de Ajuste',
				sucursal                            : sucursal,
				MyInformeFiltroFechaFinal           : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio          : MyInformeFiltroFechaInicio,
				idConceptos                         : idConceptos,
				idEmpleados                         : idEmpleados,
				discrimina_planillas_PlanillaAjuste : discrimina_planillas_PlanillaAjuste,
				agrupacion_PlanillaAjuste           : agrupacion_PlanillaAjuste,
			}
		});

		document.getElementById("RecibidorInforme_planilla_ajuste").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalPlanillaAjuste  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioPlanillaAjuste = MyInformeFiltroFechaInicio;
		localStorage.sucursal_PlanillaAjuste                  = sucursal;
		localStorage.agrupacion_PlanillaAjuste                = agrupacion_PlanillaAjuste;
		localStorage.discrimina_planillas_PlanillaAjuste             = discrimina_planillas_PlanillaAjuste;

	}

	function generarPDF_Excel(tipo_documento){
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;
		var sucursal                   = document.getElementById('filtro_sucursal_PlanillaAjuste').value;
		var idConceptos                = '';
		var idEmpleados                = '';

		//RECORREMOS EL ARRAY DE LOS CONCEPTOS PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayConceptosPlanillaAjuste.length; i++) {
			if (typeof(arrayConceptosPlanillaAjuste[i])!="undefined" && arrayConceptosPlanillaAjuste[i]!="") {
				idConceptos=(idConceptos=='')? arrayConceptosPlanillaAjuste[i] : idConceptos+','+arrayConceptosPlanillaAjuste[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS EMPLEADOS PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayEmpleadosPlanillaAjuste.length; i++) {
			if (typeof(arrayEmpleadosPlanillaAjuste[i])!="undefined" && arrayEmpleadosPlanillaAjuste[i]!="") {
				idEmpleados=(idEmpleados=='')? arrayEmpleadosPlanillaAjuste[i] : idEmpleados+','+arrayEmpleadosPlanillaAjuste[i] ;
			}

		}

		// RECORRER LA AGRUPACION DEL INFORME
		var elementos = document.getElementsByName("agrupado");

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {agrupacion_PlanillaAjuste=elementos[i].value;}
		}

		var discrimina_planillas_PlanillaAjuste = (document.getElementById('discrimina_planillas_PlanillaAjuste').checked)? true : false ;

		var bodyVar = '&nombre_informe=Planilla de Ajuste'+
						'&sucursal='+sucursal+
						'&MyInformeFiltroFechaFinal='+MyInformeFiltroFechaFinal+
						'&MyInformeFiltroFechaInicio='+MyInformeFiltroFechaInicio+
						'&idConceptos='+idConceptos+
						'&idEmpleados='+idEmpleados+
						'&discrimina_planillas_PlanillaAjuste='+discrimina_planillas_PlanillaAjuste+
						'&agrupacion_PlanillaAjuste='+agrupacion_PlanillaAjuste;


		window.open("../informes/informes/nomina/planilla_ajuste_Result.php?"+tipo_documento+"=true"+bodyVar);

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
					opcGrillaContable 	 : 'planilla_ajuste',
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

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
        	var div   = document.createElement('div');
        	div.setAttribute('id','fila_empleado_'+cont);
        	div.setAttribute('class','filaBoleta');
        	document.getElementById('bodyTablaConfiguracion').appendChild(div);

        	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
        	var nit=document.getElementById('nit_'+cont).innerHTML;
        	var tercero=document.getElementById('tercero_'+cont).innerHTML;
        	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
        	var fila='<div class="campoInforme0">'+contVendedores+'</div><div class="campoInforme1" id="nit_'+cont+'">'+nit+'</div><div class="campoInforme2" style="width:150px;" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campoInforme4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteFV('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
        	arrayEmpleadosConfiguradosPlanillaAjuste[cont]=fila;
        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_empleado_'+cont).innerHTML=fila;
        	contVendedores++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	arrayEmpleadosPlanillaAjuste[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {

			delete arrayEmpleadosPlanillaAjuste[cont];
			delete arrayEmpleadosConfiguradosPlanillaAjuste[cont];
			(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));

		}
	}

	function checkGrillaConceptos(checkbox,cont){
		if (checkbox.checked ==true) {

        	var div   = document.createElement('div');
        	div.setAttribute('id','fila_concepto_'+cont);
        	div.setAttribute('class','filaBoleta');
        	document.getElementById('bodyTablaConfiguracionVendedores').appendChild(div);

        	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
        	var descripcion=document.getElementById('descripcion_concepto_'+cont).innerHTML;
        	var naturaleza=document.getElementById('naturaleza_concepto_'+cont).innerHTML;
        	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
        	var fila='<div class="campoInforme0">'+contTercero+'</div><div class="campoInforme1" id="nits_'+cont+'" style="width:220px;">'+descripcion+'</div><div class="campoInforme2" style="width:30px;" id="terceros_'+cont+'" title="'+naturaleza+'"><img src="img/'+naturaleza+'.png"></div><div class="campoInforme4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteFV('+cont+',\'Conceptos\')" title="Eliminar Cliente"></div>';
        	arrayConceptosPlanillaAjusteNomina[cont]=fila;
        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_concepto_'+cont).innerHTML=fila;
        	contTercero++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	arrayConceptosPlanillaAjuste[cont]=checkbox.value;


		}
		else if (checkbox.checked ==false) {

			delete arrayConceptosPlanillaAjuste[cont];
			delete arrayConceptosPlanillaAjusteNomina[cont];
			(document.getElementById("fila_concepto_"+cont)).parentNode.removeChild(document.getElementById("fila_concepto_"+cont));

		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaClienteFV(cont,tabla){
		if (tabla=='empleados') {
			delete arrayEmpleadosPlanillaAjuste[cont];
			delete arrayEmpleadosConfiguradosPlanillaAjuste[cont];
			(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));
		}
		else{
			delete arrayConceptosPlanillaAjuste[cont];
			delete arrayConceptosPlanillaAjusteNomina[cont];
			(document.getElementById("fila_concepto_"+cont)).parentNode.removeChild(document.getElementById("fila_concepto_"+cont));
		}
		// console.log("fila_concepto_"+cont);
	}

</script>