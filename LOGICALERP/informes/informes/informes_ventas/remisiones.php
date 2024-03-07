<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///			  INICIALIZACION DE LA CLASE  	  ///**/
	/**/																						/**/
	/**/					$informe = new MyInforme();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$informe->InformeName          = 'remisiones';  //NOMBRE DEL INFORME
	$informe->InformeTitle         = 'Informe Remisiones'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode = 'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu     = 'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->BtnGenera            = 'false';
	$informe->InformeExportarPDF   = "false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS   = "false";	//SI EXPORTA A XLS
	$informe->InformeTamano        = "CARTA-HORIZONTAL";

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	//CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo == 'ventas'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///								INICIALIZACION DE LA GRILLA	  				  ///**/
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

		var MyInformeFiltroFechaFinal  = ""
		,	MyInformeFiltroFechaInicio = ""
		,	sucursal                   = ""
		,	estado_remision            = ""
		,	detallado_items            = ""
		,	arraytercerosJSON          = Array()
		,	arrayVendedoresJSON        = Array()
		,	arrayCcosJSON              = Array()
		,	i                          = 0

		arraytercerosRV.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON=JSON.stringify(arraytercerosJSON);

  	i = 0;
  	arrayvendedoresRV.forEach(function(id_vendedor){ arrayVendedoresJSON[i] = id_vendedor; i++; });
  	arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

		i = 0;
    arrayCentroCostosRV.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
    arrayCcosJSON=JSON.stringify(arrayCcosJSON);

    if(typeof(localStorage.MyInformeFiltroFechaInicioRemisionesVenta) != "undefined"){
			if(localStorage.MyInformeFiltroFechaInicioRemisionesVenta != ''){
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioRemisionesVenta;
			}
		}

    if(typeof(localStorage.MyInformeFiltroFechaFinalRemisionesVenta) != "undefined"){
			if(localStorage.MyInformeFiltroFechaFinalRemisionesVenta != ''){
				MyInformeFiltroFechaFinal = localStorage.MyInformeFiltroFechaFinalRemisionesVenta;
			}
		}

		if(typeof(localStorage.sucursal_remisiones) != "undefined"){
			if(localStorage.sucursal_remisiones != ''){
				sucursal = localStorage.sucursal_remisiones;
			}
		}

		if(typeof(localStorage.estado_remision) != "undefined"){
			if(localStorage.estado_remision != ''){
				estado_remision = localStorage.estado_remision;
			}
		}

		if(typeof(localStorage.detallado_itemsRV) != "undefined"){
			if(localStorage.detallado_itemsRV != ''){
				detallado_items = localStorage.detallado_itemsRV;
			}
		}

		var data =  tipo_documento+`=true
								&sucursal=${sucursal}
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&arraytercerosJSON=${arraytercerosJSON}
								&arrayVendedoresJSON=${arrayVendedoresJSON}
								&arrayCcosJSON=${arrayCcosJSON}
								&estado_remision=${estado_remision}
								&detallado_items=${detallado_items}`;

		window.open("../informes/informes/informes_ventas/remisiones_Result.php?" + data);
	}

	function ventanaConfigurarInforme(){

		Win_Ventana_configurar_remisiones = new Ext.Window({
		    width       : 700,
		    height      : 560,
		    id          : 'Win_Ventana_configurar_remisiones',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_ventas/wizard_remisiones.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionResmisionesVenta',
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
                                params  : { opc  : 'remisiones' }
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
                    handler     : function(){ generarHtmlRV() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelRV('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_ExcelRV('IMPRIME_XLS') }
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
		            handler     : function(){ Win_Ventana_configurar_remisiones.close() }
		        }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalRemisionesVenta  = "";
		localStorage.MyInformeFiltroFechaInicioRemisionesVenta = "";
		localStorage.sucursal_remisiones                       = "";
		localStorage.estado_remision                           = "";
		arraytercerosRV.length                                 = 0;
		arrayvendedoresRV.length                               = 0;
		arrayCentroCostosRV.length                      			 = 0;
		Win_Ventana_configurar_remisiones.close();
    ventanaConfigurarInforme();
	}

	function generarHtmlRV(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                   = document.getElementById('filtro_sucursal_remisiones').value
		,	estado_remision            = document.getElementById('estado_remision').value
		,	detallado_items            = document.getElementById('detallado_items').value
		,	arraytercerosJSON          = Array()
		,	arrayVendedoresJSON        = Array()
		,	arrayCcosJSON              = Array()
		,	i                          = 0

		arraytercerosRV.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON=JSON.stringify(arraytercerosJSON);

  	i = 0;
  	arrayvendedoresRV.forEach(function(id_vendedor){ arrayVendedoresJSON[i] = id_vendedor; i++; });
  	arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

		i = 0;
		arrayCentroCostosRV.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
		arrayCcosJSON=JSON.stringify(arrayCcosJSON);

		Ext.get('RecibidorInforme_remisiones').load({
			url     : '../informes/informes/informes_ventas/remisiones_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Remisiones',
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				arraytercerosJSON          : arraytercerosJSON,
				arrayVendedoresJSON        : arrayVendedoresJSON,
				arrayCcosJSON              : arrayCcosJSON,
				estado_remision            : estado_remision,
				detallado_items            : detallado_items,

			}
		});

		document.getElementById("RecibidorInforme_remisiones").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalRemisionesVenta  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioRemisionesVenta = MyInformeFiltroFechaInicio;
		localStorage.sucursal_remisiones                       = sucursal;
		localStorage.estado_remision                           = estado_remision;
		localStorage.detallado_itemsRV                         = detallado_items;

	}

	function generarPDF_ExcelRV(tipo_documento){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	MyInformeFiltroFechaInicio   = document.getElementById('MyInformeFiltroFechaInicio').value
		,	sucursal                     = document.getElementById('filtro_sucursal_remisiones').value
		,	estado_remision              = document.getElementById('estado_remision').value
		,	detallado_items              = document.getElementById('detallado_items').value
		,	arraytercerosJSON            = Array()
		,	arrayVendedoresJSON          = Array()
		,	arrayCcosJSON              	 = Array()
		,	i                            = 0

		arraytercerosRV.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
  	arraytercerosJSON=JSON.stringify(arraytercerosJSON);

  	i = 0;
  	arrayvendedoresRV.forEach(function(id_vendedor){ arrayVendedoresJSON[i] = id_vendedor; i++; });
  	arrayVendedoresJSON=JSON.stringify(arrayVendedoresJSON);

		i = 0;
		arrayCentroCostosRV.forEach(function(id_vendedor){ arrayCcosJSON[i] = id_vendedor; i++; });
		arrayCcosJSON=JSON.stringify(arrayCcosJSON);

  	var data = tipo_documento+`=true
								&sucursal=${sucursal}
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&arraytercerosJSON=${arraytercerosJSON}
								&arrayVendedoresJSON=${arrayVendedoresJSON}
								&estado_remision=${estado_remision}
								&detallado_items=${detallado_items}
								&arrayCcosJSON=${arrayCcosJSON}`;

		window.open("../informes/informes/informes_ventas/remisiones_Result.php?"+data);

	}

	function ventanaBusquedaTerceroRV(opc){
		if (opc=='vendedores') {
			tabla   = 'empleados';
			tercero = 'nombre';
			titulo_ventana = 'Empleados';
		}
		else{
			tabla   = 'terceros';
			tercero = 'nombre_comercial';
			titulo_ventana = 'Clientes';
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
					tabla             : tabla,
					id_tercero        : 'id',
					tercero           : tercero,
					opcGrillaContable : 'remisiones',
					cargaFuncion      : '',
					nombre_grilla     : '',
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

	function checkGrilla(checkbox,cont,tabla){
		//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR

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
	                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaClienteRV(${cont},'${tabla}')" title="Eliminar Vendedor"></div>
	                        </div>`;

	            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
	            vendedoresConfiguradosRV[cont]=fila;

	            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	            document.getElementById('row_vendedor_'+cont).innerHTML=fila;
	            contVendedores++;

	            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
	            arrayvendedoresRV[cont]=checkbox.value;
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
	                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaClienteRV(${cont},'${tabla}')" title="Eliminar Proveedor"></div>
	                        </div>`;

	            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
	            tercerosConfiguradosRV[cont]=fila;

	            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	            document.getElementById('row_tercero_'+cont).innerHTML=fila;
	            contTercero++;

	            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
	            arraytercerosRV[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked == false) {
			if (tabla == 'empleados') {
				delete arrayvendedoresRV[cont];
				delete vendedoresConfiguradosRV[cont];
				(document.getElementById("row_vendedor_"+cont)).parentNode.removeChild(document.getElementById("row_vendedor_"+cont));
			}
			else{
				delete arraytercerosRV[cont];
				delete tercerosConfiguradosRV[cont];
				(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
			}

		}

	}

	function eliminaClienteRV(cont,tabla){
		if (tabla == 'empleados') {
			delete arrayvendedoresRV[cont];
			delete vendedoresConfiguradosRV[cont];
			(document.getElementById("row_vendedor_"+cont)).parentNode.removeChild(document.getElementById("row_vendedor_"+cont));
		}
		else{
			delete arraytercerosRV[cont];
			delete tercerosConfiguradosRV[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	function ventanaBusquedaCentroCostosRV(){
		Win_Ventana_buscar_centro_costos = new Ext.Window({
      width       : 450,
      height      : 410,
      id          : 'Win_Ventana_buscar_centro_costos',
      title       : 'Buscar Centro de Costos',
      modal       : true,
      autoScroll  : false,
      closable    : false,
      autoDestroy : true,
      autoLoad    : {
					            url     : '../informes/grillaBuscarCentroCostos.php',
					            scripts : true,
					            nocache : true,
					            params  : {
																	opcGrillaContable : 'remisiones',
										            }
						        },
      tbar        : [
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
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostosRV(${cont})" title="Eliminar Centro Costos"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            CentroCostosConfiguradosRV[cont]=fila;

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_ccos_'+cont).innerHTML=fila;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayCentroCostosRV[cont]=checkbox.value;
		}
		else if (checkbox.checked == false) {
			delete arrayCentroCostosRV[cont];
			delete CentroCostosConfiguradosRV[cont];
			(document.getElementById("row_ccos_"+cont)).parentNode.removeChild(document.getElementById("row_ccos_"+cont));
		}
	}

	function eliminaCentroCostosRV(cont,tabla){
		delete arrayCentroCostosRV[cont];
		delete CentroCostosConfiguradosRV[cont];
		(document.getElementById("row_ccos_"+cont)).parentNode.removeChild(document.getElementById("row_ccos_"+cont));
	}
</script>
