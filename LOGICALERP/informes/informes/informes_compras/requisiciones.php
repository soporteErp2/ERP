<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 		INICIALIZACION DE LA CLASE  	  ///**/
	/**/																						/**/
	/**/				 $informe = new MyInforme();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$informe->InformeName						=	'requisiciones_compra';  		//NOMBRE DEL INFORME
	$informe->InformeTitle					=	'Requisiciones'; 	//TITULO DEL INFORME
	$informe->BtnGenera           	= 'false';										//BOTON PARA GENERAR INFORME
	$informe->InformeEmpreSucuBode	=	'false'; 										//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false'; 										//FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= 'false';										//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= 'false';										//SI EXPORTA A XLS
	$informe->InformeTamano 				= 'CARTA-HORIZONTAL';					//TAMAÑO DEL INFORME
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo == 'ventas'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///							 INICIALIZACION DE LA GRILLA	    			  ///**/
	/**/																														/**/
	/**/			$informe->Link = $link;  			//Conexion a la BD			/**/
	/**/			$informe->inicializa($_POST);	//Variables POST				/**/
	/**/			$informe->GeneraInforme(); 		//Inicializa la Grilla	/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contTercero    = 1;
	contVendedores = 1;

	function generarPDF_Excel_principal(tipo_documento){

		var MyInformeFiltroFechaFinal  = ''
		,	MyInformeFiltroFechaInicio = ''
		,	sucursal                   = ''
		,	bodega                     = ''
		,	tipo_requisicion           = ''
		,	discrimina_items           = ''
		,	tipo_cruce                 = ''
		,	autorizado                 = ''
		,	arraySolicitanteJSON       = Array()
		,	arrayCentroCostosJSON      = Array()
		,	i                          = 0

		arraySolicitante.forEach(solicitanteConfigurado=> {  arraySolicitanteJSON[i] = solicitanteConfigurado; i++; });
        arraySolicitanteJSON=JSON.stringify(arraySolicitanteJSON);

        i=0;
        arrayCentroCostosRQ.forEach(centroCostosConfiguradosRQ=> {  arrayCentroCostosJSON[i] = centroCostosConfiguradosRQ; i++; });
        arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

		if (typeof(localStorage.MyInformeFiltroFechaFinalRQ)!="undefined" && typeof(localStorage.MyInformeFiltroFechaInicioRQ)!="undefined") {
			if (localStorage.MyInformeFiltroFechaFinalRQ!='' && localStorage.MyInformeFiltroFechaInicioRQ) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalRQ;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioRQ;
			}
		}

		if (typeof(localStorage.sucursal_requisiciones_compra)!="undefined") {
			if (localStorage.sucursal_requisiciones_compra) {
				sucursal=localStorage.sucursal_requisiciones_compra;
			}
		}

		if (typeof(localStorage.bodega_RQ)!="undefined") {
			if (localStorage.bodega_RQ) {
				bodega=localStorage.bodega_RQ;
			}
		}

		if (typeof(localStorage.tipo_requisicion)!="undefined") {
			if (localStorage.tipo_requisicion) {
				tipo_requisicion=localStorage.tipo_requisicion;
			}
		}

		if (typeof(localStorage.discrimina_items_RQ)!="undefined") {
			if (localStorage.discrimina_items_RQ) {
				discrimina_items=localStorage.discrimina_items_RQ;
			}
		}

		if (typeof(localStorage.tipo_cruce_RQ)!="undefined") {
			if (localStorage.tipo_cruce_RQ) {
				tipo_cruce=localStorage.tipo_cruce_RQ;
			}
		}

		if (typeof(localStorage.autorizado_RQ)!="undefined") {
			if (localStorage.autorizado_RQ) {
				autorizado=localStorage.autorizado_RQ;
			}
		}

		bodyVar = `${tipo_documento}=true&sucursal=${sucursal}
					&bodega=${bodega}
					&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
					&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
					&tipo_requisicion=${tipo_requisicion}
					&discrimina_items=${discrimina_items}
					&tipo_cruce=${tipo_cruce}
					&autorizado=${autorizado}
					&arraySolicitanteJSON=${arraySolicitanteJSON}
					&arrayCentroCostosJSON=${arrayCentroCostosJSON}
    				`;

		window.open("../informes/informes/informes_compras/requisiciones_Result.php?"+bodyVar);
	}

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 750,
		    height      : 560,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_compras/wizard_Requisiciones.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionRequisicionesCompra',
		        }
		    },
		    tbar        :
		    [

		        {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 205,
                            height      : 65,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                // url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
                                url     : '../funciones_globales/filtros/filtro_sucursal_bodega_informes.php',
                                scripts : true,
                                nocache : true,
                                params  : { opc  : 'requisiciones_compra' }
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
		            handler     : function(){ Win_Ventana_configurar_informe_facturas.close() }
		        }
		    ]
		}).show();
	}

	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalRQ   = "";
		localStorage.MyInformeFiltroFechaInicioRQ  = "";
		localStorage.tipo_requisicion              = "";
		localStorage.sucursal_requisiciones_compra = "";
		arraySolicitante.length                    = 0;
		arrayCentroCostosRQ.length                 = 0;
		Win_Ventana_configurar_informe_facturas.close();
		ventanaConfigurarInforme();
	}

	function generarHtml(){
		var MyInformeFiltroFechaFinal = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio 	= document.getElementById('MyInformeFiltroFechaInicio').value
		,	bodega                     	= document.getElementById('filtro_bodega_requisiciones_compra').value
		,	sucursal                   	= document.getElementById('filtro_sucursal_requisiciones_compra').value
		,	tipo_requisicion           	= document.getElementById('tipo_requisicion').value
		,	discrimina_items           	= document.getElementById('discrimina_items').value
		,	tipo_cruce                 	= document.getElementById('tipo_cruce').value
		,	autorizado                 	= document.getElementById('autorizado').value
		,	arraySolicitanteJSON       	= Array()
		,	arrayCentroCostosJSON      	= Array()
		,	i                          	= 0;

		arraySolicitante.forEach(solicitanteConfigurado => {  arraySolicitanteJSON[i] = solicitanteConfigurado; i++; });
    arraySolicitanteJSON = JSON.stringify(arraySolicitanteJSON);

		i = 0;

    arrayCentroCostosRQ.forEach(centroCostosConfiguradosRQ => {  arrayCentroCostosJSON[i] = centroCostosConfiguradosRQ; i++; });
    arrayCentroCostosJSON = JSON.stringify(arrayCentroCostosJSON);

		Ext.get('RecibidorInforme_requisiciones_compra').load({
			url     : '../informes/informes/informes_compras/requisiciones_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :	{
									nombre_informe             : 'Requisiciones',
									bodega                     : bodega,
									sucursal                   : sucursal,
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									arraySolicitanteJSON       : arraySolicitanteJSON,
									arrayCentroCostosJSON      : arrayCentroCostosJSON,
									tipo_requisicion           : tipo_requisicion,
									discrimina_items           : discrimina_items,
									tipo_cruce                 : tipo_cruce,
									autorizado                 : autorizado
								}
		});

		document.getElementById("RecibidorInforme_requisiciones_compra").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalRQ   = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioRQ  = MyInformeFiltroFechaInicio;
		localStorage.sucursal_requisiciones_compra = sucursal;
		localStorage.bodega_RQ                     = bodega;
		localStorage.tipo_requisicion              = tipo_requisicion;
		localStorage.discrimina_items_RQ           = discrimina_items;
		localStorage.tipo_cruce_RQ                 = tipo_cruce;
		localStorage.autorizado_RQ                 = autorizado;
	}

	function generarPDF_Excel(tipo_documento){


		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	bodega                     = document.getElementById('filtro_bodega_requisiciones_compra').value
		,	sucursal                   = document.getElementById('filtro_sucursal_requisiciones_compra').value
		,	tipo_requisicion           = document.getElementById('tipo_requisicion').value
		,	discrimina_items           = document.getElementById('discrimina_items').value
		,	tipo_cruce                 = document.getElementById('tipo_cruce').value
		,	autorizado                 = document.getElementById('autorizado').value
		,	arraySolicitanteJSON       = Array()
		,	arrayCentroCostosJSON      = Array()
		,	bodyVar                    = ''
		,	i                          = 0

		arraySolicitante.forEach(solicitanteConfigurado => {  arraySolicitanteJSON[i] = solicitanteConfigurado; i++; });
        arraySolicitanteJSON=JSON.stringify(arraySolicitanteJSON);

        i = 0;
        arrayCentroCostosRQ.forEach(centroCostosConfiguradosRQ=> {  arrayCentroCostosJSON[i] = centroCostosConfiguradosRQ; i++; });
        arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

        bodyVar = `${tipo_documento}=true&sucursal=${sucursal}
					&bodega=${bodega}
					&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
					&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
					&tipo_requisicion=${tipo_requisicion}
					&discrimina_items=${discrimina_items}
					&tipo_cruce=${tipo_cruce}
					&autorizado=${autorizado}
					&arraySolicitanteJSON=${arraySolicitanteJSON}
					&arrayCentroCostosJSON=${arrayCentroCostosJSON}
    				`;

		window.open("../informes/informes/informes_compras/requisiciones_Result.php?"+bodyVar);

	}

	function ventanaBusquedaTercero(){

		tabla='empleados';
		tercero='nombre';
		titulo_ventana='Empleados';

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
                url     : '../informes/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
					tabla                : tabla,
					tercero              : tercero,
					id_tercero			 : "id",
					opcGrillaContable 	 : 'requisiciones_compra',
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
                    handler     : function(){ Win_VentanaCliente_terceros.close(id) }
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
            solicitanteConfigurado[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraySolicitante[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arraySolicitante[cont];
			delete solicitanteConfigurado[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	function eliminaCliente(cont,tabla){
		if (tabla=='empleados') {
			delete arraySolicitante[cont];
			delete solicitanteConfigurado
			[cont];
			(document.getElementById("fila_empleado_"+cont)).parentNode.removeChild(document.getElementById("fila_empleado_"+cont));
		}
		else{
			delete arraySolicitante[cont];
			delete solicitanteConfigurado[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
		// console.log("fila_cartera_tercero_"+cont);

	}

	function ventanaBusquedaCcos(){

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
					opcGrillaContable : 'requisicionesCentroC_compra',
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

	function renderizaResultadoVentanaCentroCosto(id,codigo,nombre){
		if (id!='' && codigo!='' && nombre!='') {
			//VALIDAR QUE LAS CUENTAS NO ESTEN YA AGREGADAS
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			var cadenaBuscar='';
			for ( i = 0; i < arrayCentroCostosRQ.length; i++) {
				if (typeof(arrayCentroCostosRQ[i])!="undefined" && arrayCentroCostosRQ[i]!="") {
					// console.log(codigo.indexOf(arrayCentroCostosLA[i])+' - '+arrayCentroCostosLA[i]+' - '+id);

					if (id.indexOf(arrayCentroCostosRQ[i])==0) {

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
            centroCostosConfiguradosRQ[id]=fila;

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_centro_costo_'+id).innerHTML=fila;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayCentroCostosRQ[id]=id;
		}
	}

	function eliminaCentroCostos(id){
		delete arrayCentroCostosRQ[id];
		delete centroCostosConfiguradosRQ[id];
		(document.getElementById("row_centro_costo_"+id)).parentNode.removeChild(document.getElementById("row_centro_costo_"+id));
	}
</script>
