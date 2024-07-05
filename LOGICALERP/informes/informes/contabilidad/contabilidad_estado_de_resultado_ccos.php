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

	$informe->InformeName			=	'contabilidad_estado_de_resultado_ccos';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Estado de Resultado Por centro de costos'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	 //FILTRO FECHA

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	// CHANGE CSS
	$informe->DefaultCls               = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar            = 	80; 		//HEIGHT TOOLBAR
	$informe->BtnGenera             = 'false';
	$informe->InformeTamano 		= "CARTA-HORIZONTAL";

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_cartera');

	$array= '["Resumido","Resumido"],["Cuentas","Cuentas"],["Subcuentas","Subcuentas"]';

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

	contCentroCostos = 1;
	contTercero      = 1;

	//==========================// PDF Y EXCEL PRINCIPAL //==========================//
	//*******************************************************************************//

	function generarPDF_Excel_principal(tipo_documento){
		var	arrayCentroCostosJSON        = Array()
		,	arrayTercerosJSON        	 = Array()
		,	i                            = 0
		,	bodyVars                     = ''

		arrayCentroCostosERC.forEach(function(centro_costos) {  arrayCentroCostosJSON[i] = centro_costos; i++; });
        arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

        i = 0;

        arraytercerosERC.forEach(function(id_tercero) {  arrayTercerosJSON[i] = id_tercero; i++; });
        arrayTercerosJSON=JSON.stringify(arrayTercerosJSON);

		if (checkBoxSelectAllERC=='true') {arrayCentroCostosJSON='todos';}
		if (checkBoxSelectAllTercerosERC=='true') {arrayTercerosJSON='todos';}

		MyInformeFiltroFechaFinal    = (typeof(localStorage.MyInformeFiltroFechaFinalEstadoResultadoC)!='undefined')? localStorage.MyInformeFiltroFechaFinalEstadoResultadoC : '' ;
		MyInformeFiltroFechaInicio   = (typeof(localStorage.MyInformeFiltroFechaInicioEstadoResultadoC)!='undefined')? localStorage.MyInformeFiltroFechaInicioEstadoResultadoC : '' ;
		tipo_balance_EstadoResultado = (typeof(localStorage.tipo_balance_EstadoResultadoC)!='undefined')? localStorage.tipo_balance_EstadoResultadoC : 'mensual' ;
		nivel_cuenta                 = (typeof(localStorage.nivel_cuentas_EstadoResultadoC)!='undefined')? localStorage.nivel_cuentas_EstadoResultadoC : 'Resumido' ;
		sucursal                 	 = (typeof(localStorage.sucursales_estado_resultadoC)!='undefined')? localStorage.sucursales_estado_resultadoC : 'Resumido' ;

		// window.open("../informes/informes/contabilidad/contabilidad_estado_de_resultado_Result.php?"+tipo_documento+"=true&nombre_informe=Estado de Resultados&tipo_balance_EstadoResultado="+tipo_balance_EstadoResultado+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&generar="+nivel_cuenta+"&centro_costos="+centro_costos);

		bodyVars = '&nombre_informe=Estado de Resultados por centro de costos'+
					'&tipo_balance_EstadoResultado='+tipo_balance_EstadoResultado+
					'&MyInformeFiltroFechaFinal='+MyInformeFiltroFechaFinal+
					'&MyInformeFiltroFechaInicio='+MyInformeFiltroFechaInicio+
					'&generar='+nivel_cuenta+
					'&centro_costos='+arrayCentroCostosJSON+
					'&terceros='+arrayTercerosJSON+
					'&sucursal='+sucursal;

		window.open("../informes/informes/contabilidad/contabilidad_estado_de_resultado_ccos_Result.php?"+tipo_documento+"=true"+bodyVars);

	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_cartera_edades = new Ext.Window({
		    width       : 680,
		    height      : 500,
		    id          : 'Win_Ventana_configurar_cartera_edades',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/wizard_estado_de_resultado_ccos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ventana_configuracion_PyG',
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
                                params  : { opc  : 'sucursales_estado_resultado' }
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


		localStorage.nivel_cuentas_EstadoResultado             = "";
		localStorage.tipo_balance_EstadoResultado              = "";
		localStorage.MyInformeFiltroFechaFinalEstadoResultado  = "";
		localStorage.MyInformeFiltroFechaInicioEstadoResultado = "";
		localStorage.sucursales_estado_resultado               = "";
		arrayCentroCostos.length                               =0;
		Win_Ventana_configurar_cartera_edades.close();
		ventanaConfigurarInforme();
	}

	function generarHtml(){
		var nivel_cuenta                 = document.getElementById('nivel_cuenta').value
		,	centro_costos                =''
		,	tipo_balance_EstadoResultado = document.getElementById('tipo_informe').value
		,	arrayCentroCostosJSON        = Array()
		,	arrayTercerosJSON            = Array()
		,	i                            = 0


		var sucursal=document.getElementById('filtro_sucursal_sucursales_estado_resultado').value;
		var MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_balance_EstadoResultado!='rango_fechas') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_balance_EstadoResultado=='rango_fechas') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{ return; }

		arrayCentroCostosERC.forEach(function(centro_costos) {  arrayCentroCostosJSON[i] = centro_costos; i++; });
        arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

        i = 0;

        arraytercerosERC.forEach(function(id_tercero) {  arrayTercerosJSON[i] = id_tercero; i++; });
        arrayTercerosJSON=JSON.stringify(arrayTercerosJSON);

		if (checkBoxSelectAllERC=='true') {arrayCentroCostosJSON='todos';}
		if (checkBoxSelectAllTercerosERC=='true') {arrayTercerosJSON='todos';}


		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.nivel_cuentas_EstadoResultadoC             = nivel_cuenta;
		localStorage.tipo_balance_EstadoResultadoC              = tipo_balance_EstadoResultado;
		localStorage.MyInformeFiltroFechaFinalEstadoResultadoC  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioEstadoResultadoC = MyInformeFiltroFechaInicio;
		localStorage.sucursales_estado_resultadoC               = sucursal;

		Ext.get('RecibidorInforme_contabilidad_estado_de_resultado_ccos').load({
			url     : '../informes/informes/contabilidad/contabilidad_estado_de_resultado_ccos_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe               : 'Estado de Resultados',
				tipo_balance_EstadoResultado : tipo_balance_EstadoResultado,
				MyInformeFiltroFechaFinal    : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio   : MyInformeFiltroFechaInicio,
				generar                      : nivel_cuenta,
				centro_costos                : arrayCentroCostosJSON,
				terceros                     : arrayTercerosJSON,
				sucursal                     : sucursal,
			}
		});

		document.getElementById("RecibidorInforme_contabilidad_estado_de_resultado_ccos").style.padding = 20;
	}

	function generarPDF_Excel(tipo_documento){
		var nivel_cuenta                 = document.getElementById('nivel_cuenta').value
		,	tipo_balance_EstadoResultado = document.getElementById('tipo_informe').value
		,	arrayCentroCostosJSON        = Array()
		,	arrayTercerosJSON        	 = Array()
		,	sucursal                     = document.getElementById('filtro_sucursal_sucursales_estado_resultado').value
		,	i                            = 0
		,	bodyVars                     = ''

		arrayCentroCostosERC.forEach(function(centro_costos) {  arrayCentroCostosJSON[i] = centro_costos; i++; });
        arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

        i = 0;

        arraytercerosERC.forEach(function(id_tercero) {  arrayTercerosJSON[i] = id_tercero; i++; });
        arrayTercerosJSON=JSON.stringify(arrayTercerosJSON);

		if (checkBoxSelectAllERC=='true') {arrayCentroCostosJSON='todos';}
		if (checkBoxSelectAllTercerosERC=='true') {arrayTercerosJSON='todos';}

		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_balance_EstadoResultado!='rango_fechas') {
			MyInformeFiltroFechaInicio='';
		}
		else if (tipo_balance_EstadoResultado=='rango_fechas') {
			MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value;
		}
		else{
			return;
		}

		bodyVars = '&nombre_informe=Estado de Resultados por centro de costos'+
					'&tipo_balance_EstadoResultado='+tipo_balance_EstadoResultado+
					'&MyInformeFiltroFechaFinal='+MyInformeFiltroFechaFinal+
					'&MyInformeFiltroFechaInicio='+MyInformeFiltroFechaInicio+
					'&generar='+nivel_cuenta+
					'&centro_costos='+arrayCentroCostosJSON+
					'&terceros='+arrayTercerosJSON+
					'&sucursal='+sucursal;

		window.open("../informes/informes/contabilidad/contabilidad_estado_de_resultado_ccos_Result.php?"+tipo_documento+"=true"+bodyVars);
	}

	function buscarCentroCostos(event,input) {
		tecla   = (input) ? event.keyCode : event.which;
        numero  = input.value;

        if (tecla==13 && numero!="") {
        	Ext.Ajax.request({
        	    url     : '../informes/informes/contabilidad/bd.php',
        	    params  :
        	    {
        			opc  : 'buscarCentroCostos',
        			codigo : numero,
        	    },
        	    success :function (result, request){
        	    			if(result.responseText=='false'){ alert('Error\nNo existe el centro de costos');  return;}
        	                else if(result.responseText != 'true'){
    	                				var arrayBD=result.responseText;
                                      	var obj=JSON.parse(arrayBD);

        	                			console.log('id: '+obj.id+' nombre: '+obj.nombre);

        	                			renderizaResultadoVentanaCentroCosto(obj.id,numero,obj.nombre);
        	            			}
        	            },
        	    failure : function(){ console.log("fail"); }
        	});
        }
	}

	//=====================// VENTANA CENTROS DE COSTOS //=====================//
	//*************************************************************************//
	function ventanaBusquedaCcos() {

		Win_Ventana_buscar_centro_cotos = new Ext.Window({
		    width       : 400,
		    height      : 450,
		    id          : 'Win_Ventana_buscar_centro_cotos',
		    title       : 'Seleccione un Centro de costo',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/grilla_buscar_centro_costos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opcGrillaContable : 'grillaCentroCostos',
					funcion           : 'renderizaResultadoVentanaCentroCosto(id,codigo,nombre)',
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
                    handler     : function(){ Win_Ventana_buscar_centro_cotos.close(id) }
                }
		    ]
		}).show();
	}

	//================== FUNCION PARA RENDERIZAR LOS RESULTADOS DE LA VENTANA DE CENTROS DE COSTOS =======================//
	function renderizaResultadoVentanaCentroCosto(id,codigo,nombre) {
		if (id!='' && codigo!='' && nombre!='') {
			//VALIDAR QUE LAS CUENTAS NO ESTEN YA AGREGADAS
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			var cadenaBuscar='';
			for ( i = 0; i < arrayCentroCostosERC.length; i++) {
				if (typeof(arrayCentroCostosERC[i])!="undefined" && arrayCentroCostosERC[i]!="") {
					// console.log(codigo.indexOf(arrayCentroCostosERC[i])+' - '+arrayCentroCostosERC[i]+' - '+id);

					if (id.indexOf(arrayCentroCostosERC[i])==0) {

					  alert("Ya se agrego el Centro de Costos, o el padre del centro de costos");
					  return;
					}
				}
			}

            var div   = document.createElement('div');
            div.setAttribute('id','row_centro_costo_'+id);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro_ccos').appendChild(div);

            var fila = `<div class="row" id="row_centro_costo_${id}">
                           <div class="cell" data-col="1"></div>
                           <div class="cell" data-col="2">${codigo}</div>
                           <div class="cell" data-col="3" title="${nombre}">${nombre}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostos(${id})" title="Eliminar Centro Costos"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            centroCostosConfiguradosERC[id]=fila;

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_centro_costo_'+id).innerHTML=fila;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayCentroCostosERC[id]=id;
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCentroCostos(id){

		delete arrayCentroCostosERC[id];
		delete centroCostosConfiguradosERC[id];
		(document.getElementById("row_centro_costo_"+id)).parentNode.removeChild(document.getElementById("row_centro_costo_"+id));
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
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
					tabla                : 'terceros',
					id_tercero           : 'id',
					tercero              : 'nombre_comercial',
					opcGrillaContable 	 : 'estado_resultados_ccos',
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
                    handler     : function(){ Win_VentanaCliente_.close(id) }
                }
            ]
        }).show();
	}

	function checkGrilla(checkbox,cont){

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
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont})" title="Eliminar Cliente"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            tercerosConfiguradosERC[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraytercerosERC[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arraytercerosERC[cont];
			delete tercerosConfiguradosERC[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente(cont){

		delete arraytercerosERC[cont];

		delete tercerosConfiguradosERC[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}

</script>
