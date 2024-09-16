<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	       //**/
	/**/																					  /**/
	/**/			 $informe = new MyInforme();				  /**/
	/**/																					  /**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$informe->InformeName			    	=	'facturas';  //NOMBRE DEL INFORME
	$informe->InformeTitle			  	=	'Facturas';  //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false';     //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false';		 //FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= 'false';	   //SI EXPORTA A PDF
	$informe->InformeExportarXLS		= 'false';	   //SI EXPORTA A XLS
	$informe->InformeTamano         = 'CARTA-HORIZONTAL';
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principalFV("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principalFV("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInformeFV()','Btn_configurar_informe_clientes');

	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo == 'ventas'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  		          	  ///**/
	/**/															                              /**/
	/**/		$informe->Link = $link;  	    //Conexion a la BD				/**/
	/**/		$informe->inicializa($_POST); //variables POST					/**/
	/**/		$informe->GeneraInforme(); 	  // Inicializa la Grilla	  /**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/

?>

<script>
	contTercero    = 1;
	contVendedores = 1;

	function generarPDF_Excel_principalFV(tipo_documento){

		var MyInformeFiltroFechaFinal  = ''
			,	MyInformeFiltroFechaInicio = ''
			,	sucursal                   = ''
			,	detallado_items            = ''
			, detallado_documentos       = ''
			,	utilidad                   = ''
			, facturacion_electronica		 = ''
			,	i                          = 0
			,	arraytercerosJSON          = Array()
			,	arrayVendedoresJSON        = Array()
			,	arrayCcosJSON              = Array()

		arraytercerosFV.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON=JSON.stringify(arraytercerosJSON);

    i = 0;
    arrayvendedoresFV.forEach(function(id_vendedor){ arrayVendedoresJSON[i] = id_vendedor; i++; });
    arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

		i = 0;
    arrayCentroCostosFV.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
    arrayCcosJSON=JSON.stringify(arrayCcosJSON);


		if (typeof(localStorage.MyInformeFiltroFechaInicioFacturas) != "undefined" && typeof(localStorage.MyInformeFiltroFechaFinalFacturas) != "undefined") {
			if (localStorage.MyInformeFiltroFechaInicioFacturas != '' && localStorage.MyInformeFiltroFechaFinalFacturas) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalFacturas;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioFacturas;
			}
		}

		if (typeof(localStorage.sucursal_facturas) != "undefined") {
			if (localStorage.sucursal_facturas!='') {
				sucursal = localStorage.sucursal_facturas;
			}
		}

		if (typeof(localStorage.detallado_itemsFV) != "undefined") {
			if (localStorage.detallado_itemsFV != '') {
				detallado_items = localStorage.detallado_itemsFV;
			}
		}

		if (typeof(localStorage.detallado_documentosFV)!="undefined") {
			if (localStorage.detallado_documentosFV != '') {
				detallado_documentos = localStorage.detallado_documentosFV;
			}
		}

		if (typeof(localStorage.mostrarDocCruce)!="undefined") {
			if (localStorage.mostrarDocCruce != '') {
				detallado_documentos = localStorage.mostrarDocCruce;
			}
		}

		if (typeof(localStorage.utilidadFV) != "undefined") {
			if (localStorage.utilidadFV != '') {
				utilidad = localStorage.utilidadFV;
			}
		}

		if (typeof(localStorage.facturacion_electronica) != "undefined") {
			if (localStorage.facturacion_electronica != '') {
				facturacion_electronica = localStorage.facturacion_electronica;
			}
		}

		var data = tipo_documento+`=true
									&sucursal=${sucursal}
									&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
									&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
									&detallado_items=${detallado_items}
									&detallado_documentos=${detallado_documentos}
									&detalla_utilidad=${utilidad}
									&facturacion_electronica=${facturacion_electronica}
									&arraytercerosJSON=${arraytercerosJSON}
									&arrayVendedoresJSON=${arrayVendedoresJSON}
									&arrayCcosJSON=${arrayCcosJSON}`

		window.open("../informes/informes/informes_ventas/facturas_Result.php?"+data);
	}

	//==================== VENTANA CONFIGURACION DE INFORME ====================//

	function ventanaConfigurarInformeFV(){

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 670,
		    height      : 560,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_ventas/wizard_factura_venta.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionFacturas',
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
                                params  : { opc  : 'facturas' }
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
                    handler     : function(){ generarHtmlFV() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelFV('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelFV('IMPRIME_XLS') }
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
				            handler     : function(){ Win_Ventana_configurar_informe_facturas.close()
								}
		        }
		    ]
		}).show();
	}

	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalFacturas  = "";
		localStorage.MyInformeFiltroFechaInicioFacturas = "";
		localStorage.sucursal_facturas                  = "";
		arraytercerosFV.length                          = 0;
		arrayvendedoresFV.length                        = 0;
		arrayCentroCostosFV.length                      = 0;
		Win_Ventana_configurar_informe_facturas.close();
		ventanaConfigurarInformeFV();
	}

	function generarHtmlFV(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                   = document.getElementById('filtro_sucursal_facturas').value
		,	detallado_items            = document.getElementById('detallado_items').value
		,	detallado_documentos       = document.getElementById('detallado_documentos').value
		,	utilidad                   = document.getElementById('utilidad').value
		,	arraytercerosJSON          = Array()
		,	arrayVendedoresJSON        = Array()
		,	arrayCcosJSON              = Array()
		,	i                          = 0
		,	facturacion_electronica    = document.getElementById('facturacion_electronica').value
		,	mostrarDocCruce	       	   = document.getElementById('mostrarDocCruce').value

		arraytercerosFV.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
    	arraytercerosJSON=JSON.stringify(arraytercerosJSON);

    	i = 0;
    	arrayvendedoresFV.forEach(function(id_vendedor){ arrayVendedoresJSON[i] = id_vendedor; i++; });
    	arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

    	i = 0;
    	arrayCentroCostosFV.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
    	arrayCcosJSON=JSON.stringify(arrayCcosJSON);

		Ext.get('RecibidorInforme_facturas').load({
			url     : '../informes/informes/informes_ventas/facturas_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				arraytercerosJSON          : arraytercerosJSON,
				arrayVendedoresJSON        : arrayVendedoresJSON,
				arrayCcosJSON              : arrayCcosJSON,
				detallado_items            : detallado_items,
				detallado_documentos       : detallado_documentos,
				detalla_utilidad           : utilidad,
				facturacion_electronica    : facturacion_electronica,
				mostrarDocCruce   	   	   : mostrarDocCruce,
			}
		});

		document.getElementById("RecibidorInforme_facturas").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalFacturas  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioFacturas = MyInformeFiltroFechaInicio;
		localStorage.sucursal_facturas                  = sucursal;
		localStorage.detallado_itemsFV                  = detallado_items;
		localStorage.detallado_documentosFV             = detallado_documentos;
		localStorage.utilidadFV                         = utilidad;
		localStorage.facturacion_electronica            = facturacion_electronica;
		localStorage.mostrarDocCruce           			= mostrarDocCruce;
	}

	function generarPDF_ExcelFV(tipo_documento){

		var	sucursal                   = document.getElementById('filtro_sucursal_facturas').value
		, 	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	detallado_items            = document.getElementById('detallado_items').value
		,	detallado_documentos       = document.getElementById('detallado_documentos').value
		,	utilidad                   = document.getElementById('utilidad').value
		, 	facturacion_electronica    = document.getElementById('facturacion_electronica').value
		, 	mostrarDocCruce   	   	   = document.getElementById('mostrarDocCruce').value
		,	i                          = 0
		,	arraytercerosJSON          = Array()
		,	arrayVendedoresJSON        = Array()
		,	arrayCcosJSON              = Array()

		arraytercerosFV.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
    	arraytercerosJSON=JSON.stringify(arraytercerosJSON);

    	i = 0;
    	arrayvendedoresFV.forEach(function(id_vendedor){ arrayVendedoresJSON[i] = id_vendedor; i++; });
    	arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

    	i = 0;
    	arrayCentroCostosFV.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
    	arrayCcosJSON=JSON.stringify(arrayCcosJSON);

		var data =  tipo_documento+`=true
								&sucursal=${sucursal}
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&detallado_items=${detallado_items}
								&detallado_documentos=${detallado_documentos}
								&detalla_utilidad=${utilidad}
								&facturacion_electronica=${facturacion_electronica}
								&mostrarDocCruce=${mostrarDocCruce}
								&arraytercerosJSON=${arraytercerosJSON}
								&arrayVendedoresJSON=${arrayVendedoresJSON}
								&arrayCcosJSON=${arrayCcosJSON}`

		window.open("../informes/informes/informes_ventas/facturas_Result.php?"+data);
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
	function ventanaBusquedaTerceroFV(opc){
		if (opc=='vendedores') {
			tabla='empleados';
			tercero='nombre';
			titulo_ventana='Empleados';
		}
		else{
			tabla='terceros';
			tercero='nombre_comercial';
			titulo_ventana='Clientes';
		}

        Win_VentanaCliente_tercerosPF = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_tercerosPF',
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
					id_tercero           : 'id',
					tercero              : tercero,
					opcGrillaContable 	 : 'facturas',
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
                    handler     : function(){ Win_VentanaCliente_tercerosPF.close(id) }
                }
            ]
        }).show();
	}

	//================ VENTANA PARA BUSCAR LOS CENTROS DE COSTOS ===============//
	function ventanaBusquedaCentroCostosFV(){
		Win_Ventana_buscar_centro_costos = new Ext.Window({
            width       : 450,
            height      : 410,
            id          : 'Win_Ventana_buscar_centro_costos',
            title       : 'Buscar Centro de Costos',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/grillaBuscarCentroCostos.php',
                scripts : true,
                nocache : true,
                params  :
                {
									opcGrillaContable : 'facturas',
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
                    handler     : function(){ Win_Ventana_buscar_centro_costos.close(id) }
                }
            ]
        }).show();
	}

	function checkGrillaCentroCostos(checkbox,cont){
		if (checkbox.checked ==true) {

        	var div   = document.createElement('div');
            div.setAttribute('id','row_ccos_'+cont);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro_ccos').appendChild(div);


            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
						var codigo = document.getElementById('codigo_'+cont).innerHTML
						,   nombre = document.getElementById('nombre_'+cont).innerHTML;

            var fila = `<div class="row" id="row_ccos_${cont}">
                           <!--<div class="cell" data-col="1">${contTercero}</div>-->
                           <div class="cell" data-col="2">${codigo}</div>
                           <div class="cell" data-col="2" title="${nombre}">${nombre}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostosFV(${cont})" title="Eliminar Centro Costos"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            CentroCostosConfiguradosFV[cont]=fila;

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_ccos_'+cont).innerHTML=fila;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayCentroCostosFV[cont]=checkbox.value;
		}
		else if (checkbox.checked == false) {
			delete arrayCentroCostosFV[cont];
			delete CentroCostosConfiguradosFV[cont];
			(document.getElementById("row_ccos_"+cont)).parentNode.removeChild(document.getElementById("row_ccos_"+cont));
		}
	}

	function eliminaCentroCostosFV(cont,tabla){
		delete arrayCentroCostosFV[cont];
		delete tercerosConfiguradosFV[cont];
		(document.getElementById("row_ccos_"+cont)).parentNode.removeChild(document.getElementById("row_ccos_"+cont));
	}

	function checkGrilla(checkbox,cont,tabla){

		if (checkbox.checked ==true) {

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
            if (tabla=='empleados') {
            	var div   = document.createElement('div');
	            div.setAttribute('id','row_vendedor_'+cont);
	            div.setAttribute('class','row');
	            document.getElementById('body_grilla_filtro_usuarios').appendChild(div);


	            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
	            var nit     = document.getElementById('nit_'+cont).innerHTML
	            ,   tercero = document.getElementById('tercero_'+cont).innerHTML;

	            var fila = `<div class="row" id="row_vendedor_${cont}">
	                           <div class="cell" data-col="1">${contVendedores}</div>
	                           <div class="cell" data-col="2">${nit}</div>
	                           <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
	                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaClienteFV(${cont},'${tabla}')" title="Eliminar Vendedor"></div>
	                        </div>`;

	            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
	            vendedoresConfiguradosFV[cont]=fila;

	            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	            document.getElementById('row_vendedor_'+cont).innerHTML=fila;
	            contVendedores++;

	            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
	            arrayvendedoresFV[cont]=checkbox.value;
            }
            else{

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
	                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaClienteFV(${cont},'${tabla}')" title="Eliminar Proveedor"></div>
	                        </div>`;

	            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
	            tercerosConfiguradosFV[cont]=fila;

	            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	            document.getElementById('row_tercero_'+cont).innerHTML=fila;
	            contTercero++;

	            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
	            arraytercerosFV[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked == false) {
			if (tabla == 'empleados') {
				delete arrayvendedoresFV[cont];
				delete vendedoresConfiguradosFV[cont];
				(document.getElementById("row_vendedor_"+cont)).parentNode.removeChild(document.getElementById("row_vendedor_"+cont));
			}
			else{
				delete arraytercerosFV[cont];
				delete tercerosConfiguradosFV[cont];
				(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
			}
		}
	}

	//======================= FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS ======================//
	function eliminaClienteFV(cont,tabla){
		if (tabla == 'empleados') {
			delete arrayvendedoresFV[cont];
			delete vendedoresConfiguradosFV[cont];
			(document.getElementById("row_vendedor_"+cont)).parentNode.removeChild(document.getElementById("row_vendedor_"+cont));
		}
		else{
			delete arraytercerosFV[cont];
			delete tercerosConfiguradosFV[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

</script>
