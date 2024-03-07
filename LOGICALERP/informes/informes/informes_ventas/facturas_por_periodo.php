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

	$id_empresa  										= $_SESSION['EMPRESA'];
	$id_sucursal 										= $_SESSION['SUCURSAL'];
	$informe->InformeName			    	=	'facturas_por_periodo';   //NOMBRE DEL INFORME
	$informe->InformeTitle			  	=	'Facturas Por Periodo';   //TITULO DEL INFORME
	$informe->BtnGenera							= 'false';							 		//BOTON PARA GENERAR INFORME
	$informe->InformeEmpreSucuBode	=	'false';     					 		//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false';		 					 		//FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= 'false';	   					 		//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= 'false';	   					 		//SI EXPORTA A XLS
	$informe->InformeTamano         = 'CARTA-HORIZONTAL';    		//TAMAÑO DEL INFORME
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	//CHANGE CSS
	$informe->DefaultCls            = ''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo == 'ventas'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				     INICIALIZACION DE LA GRILLA	          	  ///**/
	/**/															                              /**/
	/**/		$informe->Link = $link;  	    //Conexion a la BD				/**/
	/**/		$informe->inicializa($_POST); //Variables POST					/**/
	/**/		$informe->GeneraInforme(); 	  //Inicializa la Grilla	  /**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contTercero    = 1;

	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		var MyInformeFiltroFechaFinal  = document.getElementById('endYear').value
		,	MyInformeFiltroFechaInicio 	 = document.getElementById('beginYear').value
		,	sucursal                   	 = document.getElementById('filtro_sucursal_facturas_por_periodo').value
		,	detallado_principal 	  	   = document.getElementById('detallado_principal').value
		,	arraytercerosJSON            = Array()
		,	arrayccosJSON                = Array()
		,	i                            = 0

		arraytercerosFP.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
  	arraytercerosJSON = JSON.stringify(arraytercerosJSON);

  	i = 0;
  	arrayCentroCostosFP.forEach(function(id_vendedor){ arrayccosJSON[i] = id_vendedor; i++; });
  	arrayccosJSON = JSON.stringify(arrayccosJSON);

		Ext.get('RecibidorInforme_facturas_por_periodo').load({
			url     : '../informes/informes/informes_ventas/facturas_por_periodo_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  : {
									IMPRIME_HTML							 : 'true',
									sucursal                   : sucursal,
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									arraytercerosJSON        	 : arraytercerosJSON,
									arrayccosJSON              : arrayccosJSON,
									detallado_principal        : detallado_principal
								}
		});

		document.getElementById("RecibidorInforme_facturas_por_periodo").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalFP  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioFP = MyInformeFiltroFechaInicio;
		localStorage.sucursalFP     	            = sucursal;
		localStorage.detallado_principalFP        = detallado_principal;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		var MyInformeFiltroFechaFinal  = document.getElementById('endYear').value
		,	MyInformeFiltroFechaInicio 	 = document.getElementById('beginYear').value
		,	sucursal                   	 = document.getElementById('filtro_sucursal_facturas_por_periodo').value
		,	detallado_principal 	  	   = document.getElementById('detallado_principal').value
		,	arraytercerosJSON            = Array()
		,	arrayccosJSON                = Array()
		,	i                            = 0

		arraytercerosFP.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
  	arraytercerosJSON = JSON.stringify(arraytercerosJSON);

  	i = 0;
  	arrayCentroCostosFP.forEach(function(id_vendedor){ arrayccosJSON[i] = id_vendedor; i++; });
  	arrayccosJSON = JSON.stringify(arrayccosJSON);

		var data =  tipo_documento + `=true
								&sucursal=${sucursal}
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&detallado_principal=${detallado_principal}
								&arraytercerosJSON=${arraytercerosJSON}
								&arrayccosJSON=${arrayccosJSON}`

		window.open("../informes/informes/informes_ventas/facturas_por_periodo_Result.php?" + data);

		localStorage.MyInformeFiltroFechaFinalFP  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioFP = MyInformeFiltroFechaInicio;
		localStorage.sucursalFP     	            = sucursal;
		localStorage.detallado_principalFP        = detallado_principal;
	}

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){
		var MyInformeFiltroFechaFinal  = ''
		,	MyInformeFiltroFechaInicio 	 = ''
		,	sucursal                   	 = ''
		,	detallado_principal 	  	   = ''
		,	arraytercerosJSON            = Array()
		,	arrayccosJSON                = Array()
		,	i                            = 0

		arraytercerosFP.forEach(function(id_tercero){ arraytercerosJSON[i] = id_tercero; i++; });
  	arraytercerosJSON = JSON.stringify(arraytercerosJSON);

  	i = 0;
  	arrayCentroCostosFP.forEach(function(id_vendedor){ arrayccosJSON[i] = id_vendedor; i++; });
  	arrayccosJSON = JSON.stringify(arrayccosJSON);

		if (typeof(localStorage.MyInformeFiltroFechaInicioFP) != "undefined" && typeof(localStorage.MyInformeFiltroFechaFinalFP) != "undefined") {
			if (localStorage.MyInformeFiltroFechaInicioFP != '' && localStorage.MyInformeFiltroFechaFinalFP) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalFP;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioFP;
			}
		}

		if (typeof(localStorage.sucursalFP) != "undefined") {
			if (localStorage.sucursalFP != '') {
				sucursal = localStorage.sucursalFP;
			}
		}

		if (typeof(localStorage.detallado_principalFP) != "undefined") {
			if (localStorage.detallado_principalFP != '') {
				detallado_principal = localStorage.detallado_principalFP;
			}
		}

		var data =  tipo_documento + `=true
								&sucursal=${sucursal}
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&detallado_principal=${detallado_principal}
								&arraytercerosJSON=${arraytercerosJSON}
								&arrayccosJSON=${arrayccosJSON}`

		window.open("../informes/informes/informes_ventas/facturas_por_periodo_Result.php?" + data);
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_informe_facturas_por_periodo = new Ext.Window({
	    width       : 700,
	    height      : 514,
	    id          : 'Win_Ventana_configurar_informe_facturas_por_periodo',
	    title       : 'Aplicar Filtros',
	    modal       : true,
	    autoScroll  : false,
	    closable    : true,
	    autoDestroy : true,
	    autoLoad    : {
							        url     : '../informes/informes/informes_ventas/wizard_facturas_por_periodo.php',
							        scripts : true,
							        nocache : true,
							        params  : {
											            opc : 'cuerpoVentanaConfiguracionFacturasPorPeriodo',
												        }
								    },
	    tbar        : [
					      			{
					              xtype   : 'buttongroup',
					              columns : 3,
					              title   : 'Filtro Sucursal',
					              items   : [
					                        	{
					                            xtype       : 'panel',
					                            border      : false,
					                            width       : 160,
					                            height      : 45,
					                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
					                            autoLoad    : {
												                              url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
												                              scripts : true,
												                              nocache : true,
												                              params  : { opc  : 'facturas_por_periodo' }
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
						            handler     : function(){ Win_Ventana_configurar_informe_facturas_por_periodo.close() }
							        }
								    ]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalFP  = "";
		localStorage.MyInformeFiltroFechaInicioFP = "";
		localStorage.sucursalFP                   = "";
		tercerosConfiguradosFP.length             = 0;
		centroCostosConfiguradosFP.length         = 0;
		Win_Ventana_configurar_informe_facturas_por_periodo.close();
		ventanaConfigurarInforme();
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
	function ventanaBusquedaTercero(opc){
		if(opc == 'vendedores'){
			tabla = 'empleados';
			tercero = 'nombre';
			titulo_ventana = 'Empleados';
		}
		else{
			tabla = 'terceros';
			tercero = 'nombre_comercial';
			titulo_ventana = 'Clientes';
		}

    Win_VentanaCliente_tercerosFP = new Ext.Window({
      width       : 650,
      height      : 605,
      id          : 'Win_VentanaCliente_tercerosFP',
      title       : titulo_ventana,
      modal       : true,
      autoScroll  : false,
      closable    : false,
      autoDestroy : true,
      autoLoad    : {
			                url     : '../informes/BusquedaTerceros.php',
			                scripts : true,
			                nocache : true,
			              	params  : {
																	tabla                : tabla,
																	id_tercero           : 'id',
																	tercero              : tercero,
																	opcGrillaContable 	 : 'facturas',
																	cargaFuncion         : '',
																	nombre_grilla        : '',
												        }
						        },
			tbar        : [
			                {
		                    xtype       : 'button',
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'left',
		                    handler     : function(){ Win_VentanaCliente_tercerosFP.close(id) }
			                }
				            ]
    }).show();
	}

	//============================= MOSTRAR TERCERO ============================//
	function checkGrilla(checkbox,cont,tabla){
		if(checkbox.checked == true){
			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
      if(tabla == 'terceros'){
      	var div = document.createElement('div');
        div.setAttribute('id','row_tercero_' + cont);
        div.setAttribute('class','row');
        document.getElementById('body_grilla_filtro').appendChild(div);

        //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
        var nit   = document.getElementById('nit_' + cont).innerHTML
        , tercero = document.getElementById('tercero_' + cont).innerHTML;

        var fila = `<div class="row" id="row_tercero_${cont}">
                      <div class="cell" data-col="1">${contTercero}</div>
                      <div class="cell" data-col="2">${nit}</div>
                      <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                      <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont},'${tabla}')" title="Eliminar Cliente"></div>
                    </div>`;

        //LLENAMOS EL ARRAY CON EL CLIENTE CREADO
        tercerosConfiguradosFP[cont] = fila;

        //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        document.getElementById('row_tercero_'+cont).innerHTML = fila;
        contTercero++;

        //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        arraytercerosFP[cont] = checkbox.value;
  	  }
		}
		else if(checkbox.checked == false){
			if(tabla == 'terceros'){
				delete arraytercerosFP[cont];
				delete tercerosConfiguradosFP[cont];
				(document.getElementById("row_tercero_" + cont)).parentNode.removeChild(document.getElementById("row_tercero_" + cont));
			}
		}
	}

	//============================ ELIMINAR TERCERO ============================//
	function eliminaCliente(cont,tabla){
		if(tabla == 'terceros'){
			delete arraytercerosFP[cont];
			delete tercerosConfiguradosFP[cont];
			(document.getElementById("row_tercero_" + cont)).parentNode.removeChild(document.getElementById("row_tercero_" + cont));
		}
	}

	//================ VENTANA PARA BUSCAR LOS CENTROS DE COSTO ================//
	function ventanaBusquedaCentroCostos(){
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
																	opcGrillaContable : 'facturas',
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

	//======================== MOSTRAR CENTRO DE COSTO =========================//
	function checkGrillaCentroCostos(checkbox,cont){
		if(checkbox.checked == true){
  		var div = document.createElement('div');
      div.setAttribute('id','row_ccos_'+cont);
      div.setAttribute('class','row');
      document.getElementById('body_grilla_filtro_ccos').appendChild(div);

      //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			var codigo = document.getElementById('codigo_'+cont).innerHTML
			,   nombre = document.getElementById('nombre_'+cont).innerHTML;

      var fila = `<div class="row" id="row_ccos_${cont}">
                    <div class="cell" data-col="2">${codigo}</div>
                    <div class="cell" data-col="2" title="${nombre}">${nombre}</div>
                    <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostos(${cont})" title="Eliminar Centro De Costo"></div>
                  </div>`;

      //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
      centroCostosConfiguradosFP[cont] = fila;

      //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
      document.getElementById('row_ccos_' + cont).innerHTML = fila;

      //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
      arrayCentroCostosFP[cont] = checkbox.value;
		}
		else if(checkbox.checked == false){
			delete arrayCentroCostosFP[cont];
			delete centroCostosConfiguradosFP[cont];
			(document.getElementById("row_ccos_" + cont)).parentNode.removeChild(document.getElementById("row_ccos_" + cont));
		}
	}

	//======================== ELIMINAR CENTRO DE COSTO ========================//
	function eliminaCentroCostos(cont,tabla){
		delete arrayCentroCostosFP[cont];
		delete centroCostosConfiguradosFP[cont];
		(document.getElementById("row_ccos_" + cont)).parentNode.removeChild(document.getElementById("row_ccos_" + cont));
	}
</script>
