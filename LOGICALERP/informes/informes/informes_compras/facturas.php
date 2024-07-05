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

	$informe->InformeName			=	'facturas_compra';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Facturas de Compra'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	$informe->InformeTamano = "CARTA-HORIZONTAL";

	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

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

	// CONSULTA PARA SELECT TIPO FACTURAS --  discriminar_tipo_factura
	
?>

<script>
	contTercero    = 1;
	contVendedores = 1;
	contCcos       = 1;

	function generarPDF_Excel_principal(tipo_documento){

		var MyInformeFiltroFechaFinal  = ''
		,	MyInformeFiltroFechaInicio = ''
		,	sucursal                   = ''
		,	discriminar_items          = ''
		,	i                          = 0
		,	arraytercerosJSON          = Array()
		,	arrayVendedoresJSON        = Array()
		,	arrayCcosJSON              = Array()

		arraytercerosFC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

        i = 0
        arrayvendedoresFC.forEach(function(id_vendedor) {  arrayVendedoresJSON[i] = id_vendedor; i++; });
        arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

        i = 0
        arrayCentroCostosFC.forEach(function(id_vendedor) {  arrayCcosJSON[i] = id_vendedor; i++; });
        arrayCcosJSON=JSON.stringify(arrayCcosJSON);

		if (typeof(localStorage.MyInformeFiltroFechaInicioFacturasCompra)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalFacturasCompra)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioFacturasCompra!='' && localStorage.MyInformeFiltroFechaFinalFacturasCompra) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalFacturasCompra;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioFacturasCompra;
			}
		}

		if (typeof(localStorage.sucursal_facturas_compra)!="undefined") {
			if (localStorage.sucursal_facturas_compra) {
				sucursal=localStorage.sucursal_facturas_compra;
			}
		}

		if (typeof(localStorage.discriminar_items_facturas_compra)!="undefined") {
			if (localStorage.discriminar_items_facturas_compra!='') {
				discriminar_items=localStorage.discriminar_items_facturas_compra;
			}
		}



		var data = tipo_documento+"=true"
					+"&sucursal="+sucursal
					+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
					+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
					+"&arraytercerosJSON="+arraytercerosJSON
					+"&arrayVendedoresJSON="+arrayVendedoresJSON
					+"&arrayCcosJSON="+arrayCcosJSON
					+"&discriminar_items="+discriminar_items

		window.open("../informes/informes/informes_compras/facturas_Result.php?"+data);

	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

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
		        url     : '../informes/informes/informes_compras/wizard_factura_compra.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionFacturasCompra',
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
                                params  : { opc  : 'facturas_compra' }
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

		localStorage.MyInformeFiltroFechaFinalFacturasCompra  = "";
		localStorage.MyInformeFiltroFechaInicioFacturasCompra = "";
		localStorage.sucursal_facturas_compra                 = "";
		arraytercerosFC.length                                = 0;
		arrayvendedoresFC.length                              = 0;

		Win_Ventana_configurar_informe_facturas.close();
        ventanaConfigurarInforme();

	}

	function generarHtml(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                   = document.getElementById('filtro_sucursal_facturas_compra').value
		,	discriminar_items          = document.getElementById('discriminar_items_facturas_compra').value
		,	i                          = 0
		,	arraytercerosJSON          = Array()
		,	arrayVendedoresJSON        = Array()
		,	arrayCcosJSON              = Array()

		arraytercerosFC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

        i = 0
        arrayvendedoresFC.forEach(function(id_vendedor) {  arrayVendedoresJSON[i] = id_vendedor; i++; });
        arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

        i = 0
        arrayCentroCostosFC.forEach(function(id_vendedor) {  arrayCcosJSON[i] = id_vendedor; i++; });
        arrayCcosJSON=JSON.stringify(arrayCcosJSON);

		Ext.get('RecibidorInforme_facturas_compra').load({
			url     : '../informes/informes/informes_compras/facturas_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Cotizaciones',
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				arraytercerosJSON          : arraytercerosJSON,
				arrayVendedoresJSON        : arrayVendedoresJSON,
				arrayCcosJSON              : arrayCcosJSON,
				discriminar_items          : discriminar_items,
			}
		});

		document.getElementById("RecibidorInforme_facturas_compra").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalFacturasCompra  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioFacturasCompra = MyInformeFiltroFechaInicio;
		localStorage.sucursal_facturas_compra                 = sucursal;
		localStorage.discriminar_items_facturas_compra        = discriminar_items;

	}

	function generarPDF_Excel(tipo_documento){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                   = document.getElementById('filtro_sucursal_facturas_compra').value
		,	discriminar_items          = document.getElementById('discriminar_items_facturas_compra').value
		,	i                          = 0
		,	arraytercerosJSON          = Array()
		,	arrayVendedoresJSON        = Array()
		,	arrayCcosJSON              = Array()

		arraytercerosFC.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++; });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

        i = 0
        arrayvendedoresFC.forEach(function(id_vendedor) {  arrayVendedoresJSON[i] = id_vendedor; i++; });
        arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

        i = 0
        arrayCentroCostosFC.forEach(function(id_vendedor) {  arrayCcosJSON[i] = id_vendedor; i++; });
        arrayCcosJSON=JSON.stringify(arrayCcosJSON);

        var data = tipo_documento+"=true"
					+"&sucursal="+sucursal
					+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
					+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
					+"&arraytercerosJSON="+arraytercerosJSON
					+"&arrayVendedoresJSON="+arrayVendedoresJSON
					+"&arrayCcosJSON="+arrayCcosJSON
					+"&discriminar_items="+discriminar_items

		window.open("../informes/informes/informes_compras/facturas_Result.php?"+data);

		// window.open("../informes/informes/informes_compras/facturas_Result.php?"+tipo_documento+"=true&sucursal="+sucursal+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&idTerceros="+idTerceros+"&idVendedores="+idVendedores+"&idCentroCostos="+idCentroCostos+"&discriminar_items="+discriminar_items);

	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTerceroFV(opc){
		if (opc=='vendedores') {
			tabla='empleados';
			tercero='nombre';
			titulo_ventana='Empleados';
		}
		else{
			tabla='terceros';
			tercero='nombre_comercial';
			titulo_ventana='Proveedores';
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
                url     : '../informes/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
					tabla                : tabla,
					id_tercero           : 'id',
					tercero              : tercero,
					opcGrillaContable 	 : 'facturas_compra',
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

	//FUNCION DE LA VENTANA DE BUSQUDA DE CLIENTES Y VENDEDORES
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
	                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont},'${tabla}')" title="Eliminar Vendedor"></div>
	                        </div>`;

	            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
	            vendedoresConfiguradosFC[cont]=fila;
	            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	            document.getElementById('row_vendedor_'+cont).innerHTML=fila;
	            contVendedores++;

	            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
	            arrayvendedoresFC[cont]=checkbox.value;
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
	                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont},'${tabla}')" title="Eliminar Proveedor"></div>
	                        </div>`;

	            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
	            tercerosConfiguradosFC[cont]=fila;
	            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	            document.getElementById('row_tercero_'+cont).innerHTML=fila;
	            contTercero++;

	            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
	            arraytercerosFC[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked ==false) {
			if (tabla=='empleados') {
				delete arrayvendedoresFC[cont];
				delete vendedoresConfiguradosFC[cont];
				(document.getElementById("row_vendedor_"+cont)).parentNode.removeChild(document.getElementById("row_vendedor_"+cont));
			}
			else{
				delete arraytercerosFC[cont];
				delete tercerosConfiguradosFC[cont];
				(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
			}


		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente(cont,tabla){
		if (tabla=='empleados') {
			delete arrayvendedoresFC[cont];
			delete vendedoresConfiguradosFC[cont];
			(document.getElementById("row_vendedor_"+cont)).parentNode.removeChild(document.getElementById("row_vendedor_"+cont));
		}
		else{
			delete arraytercerosFC[cont];
			delete tercerosConfiguradosFC[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	// ======================== VENTANA PARA BUSCAR LOS CENTROS DE COSTOS ========================//
	function ventanaBusquedaCentroCostosFC(){
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
					opcGrillaContable : 'facturas_compras',
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
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostosFC(${cont})" title="Eliminar Centro Costos"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            CentroCostosConfiguradosFC[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_ccos_'+cont).innerHTML=fila;
            contCcos++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayCentroCostosFC[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arrayCentroCostosFC[cont];
			delete CentroCostosConfiguradosFC[cont];
			(document.getElementById("row_ccos_"+cont)).parentNode.removeChild(document.getElementById("row_ccos_"+cont));
		}
	}

	function eliminaCentroCostosFC(cont,tabla){
		delete arrayCentroCostosFC[cont];
		delete CentroCostosConfiguradosFC[cont];
		(document.getElementById("row_ccos_"+cont)).parentNode.removeChild(document.getElementById("row_ccos_"+cont));
	}

</script>