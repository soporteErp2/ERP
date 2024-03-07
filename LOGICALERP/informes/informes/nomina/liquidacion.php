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

	$informe->InformeName			= 'liquidacion';  //NOMBRE DEL INFORME
	$informe->InformeTitle			= 'Liquidacion Empleados'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	= 'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		= 'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->BtnGenera             = 'false';
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
	contVendedores=1;

	function generarPDF_Excel_principal(tipo_documento){

		var MyInformeFiltroFechaFinal        = '' ;
		var MyInformeFiltroFechaInicio       = '' ;
		var sucursal                         = '';
		var idConceptos                      = '';
		var idEmpleados                      = '';
		var discrimina_planillas_liquidacion = '';
		var agrupacion_liquidacion           = '';
		var tipo_contrato                    = '';

		//RECORREMOS EL ARRAY DE LOS CONCEPTOS PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayConceptosLiquidacion.length; i++) {
			if (typeof(arrayConceptosLiquidacion[i])!="undefined" && arrayConceptosLiquidacion[i]!="") {
				idConceptos=(idConceptos=='')? arrayConceptosLiquidacion[i] : idConceptos+','+arrayConceptosLiquidacion[i] ;
			}

		}

		//RECORREMOS EL ARRAY DE LOS EMPLEADOS PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayEmpleadosLiquidacion.length; i++) {
			if (typeof(arrayEmpleadosLiquidacion[i])!="undefined" && arrayEmpleadosLiquidacion[i]!="") {
				idEmpleados=(idEmpleados=='')? arrayEmpleadosLiquidacion[i] : idEmpleados+','+arrayEmpleadosLiquidacion[i] ;
			}

		}

		if (typeof(localStorage.MyInformeFiltroFechaInicioLiquidacion)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalLiquidacion)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioLiquidacion!='' && localStorage.MyInformeFiltroFechaFinalLiquidacion) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalLiquidacion;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioLiquidacion;
			}
		}

		if (typeof(localStorage.sucursal_liquidacion)!="undefined") {
			if (localStorage.sucursal_liquidacion) {
				sucursal=localStorage.sucursal_liquidacion;
			}
		}

		if (typeof(localStorage.discrimina_planillas_liquidacion)!="undefined") {
			if (localStorage.discrimina_planillas_liquidacion) {
				discrimina_planillas_liquidacion=localStorage.discrimina_planillas_liquidacion;
			}
		}

		if (typeof(localStorage.agrupacion_liquidacion)!="undefined") {
			if (localStorage.agrupacion_liquidacion) {
				agrupacion_liquidacion=localStorage.agrupacion_liquidacion;
			}
		}

		if (typeof(localStorage.tipo_contrato_liquidacion)!="undefined") {
			if (localStorage.tipo_contrato_liquidacion) {
				tipo_contrato=localStorage.tipo_contrato_liquidacion;
			}
		}

		var bodyVar = '&nombre_informe=Liquidacion de Empleados'+
						'&sucursal='+sucursal+
						'&MyInformeFiltroFechaFinal='+MyInformeFiltroFechaFinal+
						'&MyInformeFiltroFechaInicio='+MyInformeFiltroFechaInicio+
						'&idConceptos='+idConceptos+
						'&idEmpleados='+idEmpleados+
						'&discrimina_planillas_liquidacion='+discrimina_planillas_liquidacion+
						'&agrupacion_liquidacion='+agrupacion_liquidacion+
						'&tipo_contrato='+tipo_contrato;

		window.open("../informes/informes/nomina/liquidacion_Result.php?"+tipo_documento+"=true"+bodyVar);
	}

	//===================== FUNCIONES DE LA VENTANA QUE CONFIGURA EL INFORME ==================//

	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 700,
		    height      : 560,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/nomina/wizard_informe_liquidacion.php',
		        scripts : true,
		        // text    : 'cargando...	',
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionLiquidacion',

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
                                params  : { opc  : 'liquidacion' }
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
		,	sucursal                   = document.getElementById('filtro_sucursal_liquidacion').value
		,	tipo_contrato              = document.getElementById('tipo_contrato').value
		,	agrupacion_nomina          = document.getElementById('agrupado').value
		,	discrimina_planillas       = document.getElementById('discrimina_planillas').value
		,	i                          = 0
		,	arrayEmpleadosJSON         = Array()
		,	arrayConceptosJSON         = Array()


		arrayEmpleadosLiquidacion.forEach(function(id_tercero) {  arrayEmpleadosJSON[i] = id_tercero; i++; });
        arrayEmpleadosJSON=JSON.stringify(arrayEmpleadosJSON);
		i = 0
        arrayConceptosLiquidacion.forEach(function(id_centro_costo) {  arrayConceptosJSON[i] = id_centro_costo; i++; });
        arrayConceptosJSON=JSON.stringify(arrayConceptosJSON);

		Ext.get('RecibidorInforme_liquidacion').load({
			url     : '../informes/informes/nomina/liquidacion_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe              : 'Liquidacion de Empleados',
				sucursal                    : sucursal,
				MyInformeFiltroFechaFinal   : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio  : MyInformeFiltroFechaInicio,
				arrayEmpleadosJSON          : arrayEmpleadosJSON,
				arrayConceptosJSON          : arrayConceptosJSON,
				discrimina_planillas 		: discrimina_planillas,
				agrupacion_nomina        	: agrupacion_nomina,
				tipo_contrato               : tipo_contrato,
			}
		});

		document.getElementById("RecibidorInforme_liquidacion").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalLiquidacion  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioLiquidacion = MyInformeFiltroFechaInicio;
		localStorage.sucursal_liquidacion                  = sucursal;
		localStorage.agrupacion_liquidacion                = agrupacion_liquidacion;
		localStorage.discrimina_planillas_liquidacion      = discrimina_planillas_liquidacion;
		localStorage.tipo_contrato_liquidacion             = tipo_contrato;

	}

	function generarPDF_Excel(tipo_documento){
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                   = document.getElementById('filtro_sucursal_liquidacion').value
		,	tipo_contrato              = document.getElementById('tipo_contrato').value
		,	agrupacion_nomina          = document.getElementById('agrupado').value
		,	discrimina_planillas       = document.getElementById('discrimina_planillas').value
		,	i                          = 0
		,	arrayEmpleadosJSON         = Array()
		,	arrayConceptosJSON         = Array()


		arrayEmpleadosLiquidacion.forEach(function(id_tercero) {  arrayEmpleadosJSON[i] = id_tercero; i++; });
        arrayEmpleadosJSON=JSON.stringify(arrayEmpleadosJSON);
		i = 0
        arrayConceptosLiquidacion.forEach(function(id_centro_costo) {  arrayConceptosJSON[i] = id_centro_costo; i++; });
        arrayConceptosJSON=JSON.stringify(arrayConceptosJSON);

		var bodyVar = ´&nombre_informe=Liquidacion de Empleados
						&sucursal=${sucursal}
						&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
						&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
						&arrayEmpleadosJSON=${arrayEmpleadosJSON}
						&arrayConceptosJSON=${arrayConceptosJSON}
						&discrimina_planillas=${discrimina_planillas}
						&agrupacion_nomina=${agrupacion_nomina}
						&tipo_contrato=${tipo_contrato}
						´;
		
		window.open("../informes/informes/nomina/liquidacion_Result.php?"+tipo_documento+"=true"+bodyVar);
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
					opcGrillaContable 	 : 'liquidacion',
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
        	arrayEmpleadosConfiguradosLiquidacion[cont]=fila;
        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_empleado_'+cont).innerHTML=fila;
        	contVendedores++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	arrayEmpleadosLiquidacion[cont]=checkbox.value;


		}
		else if (checkbox.checked ==false) {

			delete arrayEmpleadosLiquidacion[cont];
			delete arrayEmpleadosConfiguradosLiquidacion[cont];
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
        	arrayConceptosConfiguradosLiquidacion[cont]=fila;
        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_concepto_'+cont).innerHTML=fila;
        	contTercero++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	arrayConceptosLiquidacion[cont]=checkbox.value;


		}
		else if (checkbox.checked ==false) {

			delete arrayConceptosLiquidacion[cont];
			delete arrayConceptosConfiguradosLiquidacion[cont];
			(document.getElementById("fila_concepto_"+cont)).parentNode.removeChild(document.getElementById("fila_concepto_"+cont));

		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaClienteFV(cont,tabla){
		if (tabla=='empleados') {
			delete arrayEmpleadosLiquidacion[cont];
			delete arrayEmpleadosConfiguradosLiquidacion[cont];
			(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));
		}
		else{
			delete arrayConceptosLiquidacion[cont];
			delete arrayConceptosConfiguradosLiquidacion[cont];
			(document.getElementById("fila_concepto_"+cont)).parentNode.removeChild(document.getElementById("fila_concepto_"+cont));
		}
		// console.log("fila_concepto_"+cont);

	}

</script>