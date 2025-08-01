<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE	      ///**/
	/**/										    /**/
	/**/		 $informe = new MyInforme();	  	/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  										= $_SESSION['EMPRESA'];
	$id_sucursal 										= $_SESSION['SUCURSAL'];
	$informe->BtnGenera             = 'false';
	$informe->InformeName						= 'inventario_consolidado';  									//NOMBRE DEL INFORME
	$informe->InformeTitle					= 'Inventario Consolidado';							 	    //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	= 'false'; 																		//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			= 'false'; 																		//FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= "false";																		//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= "false";																		//SI EXPORTA A XLS
	$informe->InformeTamano 				= "CARTA-HORIZONTAL";
	$informe->DefaultCls            = ''; 																				//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 																				//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	/**//////////////////////////////////////////////////////////////**/
	/**///				  			INICIALIZACION DE LA GRILLA		  	   	  ///**/
	/**/																														/**/
	/**/			$informe->Link = $link;  			//Conexion a la BD			/**/
	/**/			$informe->inicializa($_POST);	//Variables POST				/**/
	/**/			$informe->GeneraInforme(); 	  //Inicializa la Grilla	/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contTercero  = 1;
	contEmpleado = 1;
	arrayItemsIC = [];
	itemsConfiguradosIC = [];

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){
		var MyInformeFiltroFechaFinal  = ''
		,	  MyInformeFiltroFechaInicio = ''
		,	  sucursal                   = ''
		,   bodega										 = ''
		,	  filtro_familia 	  	       = ''
		,	  filtro_grupo 	  	         = ''
		,	  filtro_subgrupo 	  	     = ''
		,	  arrayItemsJSON             = Array()

		i = 0
		arrayItemsIC.forEach(function(id_item){ arrayItemsJSON[i] = id_item; i++; });
		arrayItemsJSON = JSON.stringify(arrayItemsJSON);

		if(typeof(localStorage.MyInformeFiltroFechaInicioIC) != "undefined" && typeof(localStorage.MyInformeFiltroFechaFinalIC) != "undefined"){
			if(localStorage.MyInformeFiltroFechaInicioIC != '' && localStorage.MyInformeFiltroFechaFinalIC != ''){
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalIC;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioIC;
			}
		}
		else{
			alert("Debe generar el informe al menos una vez");
			return;
		}

		if(typeof(localStorage.sucursalIC) != "undefined"){
			if(localStorage.sucursalIC != ''){
				sucursal = localStorage.sucursalIC;
			}
		}

		if(typeof(localStorage.bodegaIC) != "undefined"){
			if(localStorage.bodegaIC != ''){
				bodega = localStorage.bodegaIC;
			}
		}

		if(typeof(localStorage.filtro_familiaIC) != "undefined"){
			if(localStorage.filtro_familiaIC != ''){
				filtro_familia = localStorage.filtro_familiaIC;
			}
		}

		if(typeof(localStorage.filtro_grupoIC) != "undefined"){
			if(localStorage.filtro_grupoIC != ''){
				filtro_grupo = localStorage.filtro_grupoIC;
			}
		}

		if(typeof(localStorage.filtro_subgrupoIC) != "undefined"){
			if(localStorage.filtro_subgrupoIC != ''){
				filtro_subgrupo = localStorage.filtro_subgrupoIC;
			}
		}

		var data =  tipo_documento + `=true
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&sucursal=${sucursal}
								&bodega=${bodega}
								&filtro_familia=${filtro_familia}
								&filtro_grupo=${filtro_grupo}
								&filtro_subgrupo=${filtro_subgrupo}
								&arrayItemsJSON=${arrayItemsJSON}`

		window.open("../informes/informes/inventario/inventario_consolidado_Result.php?" + data);
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_informe_inventario_consolidado = new Ext.Window({
	    width       : 750,
	    height      : 500,
	    id          : 'Win_Ventana_configurar_informe_inventario_consolidado',
	    title       : 'Aplicar Filtros',
	    modal       : true,
	    autoScroll  : false,
	    closable    : true,
	    autoDestroy : true,
	    tbar        : [
					        		{
			                  xtype   : 'buttongroup',
			                  columns : 3,
			                  title   : 'Filtros',
			                  items   : [
							                      {
							                        xtype       : 'panel',
							                        border      : false,
							                        width       : 205,
							                        height      : 65,
							                        bodyStyle   : 'background-color:rgba(255,255,255,0);',
							                        autoLoad    : {
												                              url     : '../funciones_globales/filtros/filtro_sucursal_bodega_informes.php',
												                              scripts : true,
												                              nocache : true,
												                              params  : {
																																	opc : 'inventario_consolidado'
																																}
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
						            handler     : function(){ Win_Ventana_configurar_informe_inventario_consolidado.close()	}
							        }
							    	],
			autoLoad    : {
								      url     : '../informes/informes/inventario/wizard_inventario_consolidado.php',
								      scripts : true,
								      nocache : true,
								      params  : {
													       	opc : 'cuerpoVentanaConfiguracionInventarioConsolidado',
													      }
	    						  }
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalIC  = "";
		localStorage.MyInformeFiltroFechaInicioIC = "";
		localStorage.sucursalIC                   = "global";
		localStorage.bodegaIC                     = "global";
		localStorage.filtro_familiaIC					    = "";
		localStorage.filtro_grupoIC					      = "";
		localStorage.filtro_subgrupoIC     			  = "";
		localStorage.arrayItemsJSONIC             = "";
		arrayItemsIC.length                       = 0;

		Win_Ventana_configurar_informe_inventario_consolidado.close();
    ventanaConfigurarInforme();
	}

	//============================ GENERAR INFORME =============================//
	function generarHtml(){

		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,		MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,		sucursal                   = document.getElementById('filtro_sucursal_inventario_consolidado').value
		,		bodega                     = document.getElementById('filtro_bodega_inventario_consolidado').value
		,   filtro_familia						 = document.getElementById('filtro_familia').value
		,		filtro_grupo			         = document.getElementById('filtro_grupo').value
		,		filtro_subgrupo		  	     = document.getElementById('filtro_subgrupo').value
		,   arrayItemsJSON             = Array()

		i = 0
		arrayItemsIC.forEach(function(id_item){ arrayItemsJSON[i] = id_item; i++; });
		arrayItemsJSON = JSON.stringify(arrayItemsJSON);

		Ext.get('RecibidorInforme_inventario_consolidado').load({
			url     : '../informes/informes/inventario/inventario_consolidado_Result.php',
			text	  : 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  : {
									IMPRIME_HTML							 : 'true',
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									sucursal                   : sucursal,
									bodega										 : bodega,
									filtro_familia						 : filtro_familia,
									filtro_grupo	  					 : filtro_grupo,
									filtro_subgrupo						 : filtro_subgrupo,
									arrayItemsJSON             : arrayItemsJSON
								}
		});

		document.getElementById("RecibidorInforme_inventario_consolidado").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalIC  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioIC = MyInformeFiltroFechaInicio;
		localStorage.sucursalIC                   = sucursal;
		localStorage.bodegaIC                     = bodega;
		localStorage.filtro_familiaIC							= filtro_familia;
		localStorage.filtro_grupoIC  							= filtro_grupo;
		localStorage.filtro_subgrupoIC  					= filtro_subgrupo;
		localStorage.arrayItemsJSONIC							= arrayItemsJSON;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,		MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,		sucursal                   = document.getElementById('filtro_sucursal_inventario_consolidado').value
		,		bodega                     = document.getElementById('filtro_bodega_inventario_consolidado').value
		,   filtro_familia						 = document.getElementById('filtro_familia').value
		,		filtro_grupo			         = document.getElementById('filtro_grupo').value
		,		filtro_subgrupo		  	     = document.getElementById('filtro_subgrupo').value
		,   arrayItemsJSON             = Array()

		i = 0
		arrayItemsIC.forEach(function(id_item){ arrayItemsJSON[i] = id_item; i++; });
		arrayItemsJSON = JSON.stringify(arrayItemsJSON);

		var data =  tipo_documento + `=true
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&sucursal=${sucursal}
								&bodega=${bodega}
								&filtro_familia=${filtro_familia}
								&filtro_grupo=${filtro_grupo}
								&filtro_subgrupo=${filtro_subgrupo}
								&arrayItemsJSON=${arrayItemsJSON}`

		window.open("../informes/informes/inventario/inventario_consolidado_Result.php?" + data);

		localStorage.MyInformeFiltroFechaFinalIC  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioIC = MyInformeFiltroFechaInicio;
		localStorage.sucursalIC                   = sucursal;
		localStorage.bodegaIC                     = bodega;
		localStorage.filtro_familiaIC							= filtro_familia;
		localStorage.filtro_grupoIC  							= filtro_grupo;
		localStorage.filtro_subgrupoIC  					= filtro_subgrupo;
		localStorage.arrayItemsJSONIC							= arrayItemsJSON;
	}

	//===================== VENTANA PARA BUSCAR LOS ITEMS ======================//
	function ventanaBusquedaItemIC(){
		Win_Ventana_buscar_items = new Ext.Window({
	    width       : 400,
	    height      : 450,
	    id          : 'Win_Ventana_buscar_items',
	    title       : 'Seleccione un item',
	    modal       : true,
	    autoScroll  : false,
	    closable    : false,
	    autoDestroy : true,
	    autoLoad    : {
							        url     : '../informes/informes/inventario/grilla_buscar_items.php',
							        scripts : true,
							        nocache : true,
							        params  : {
																	opcGrillaContable : 'inventario_consolidado',
																	funcion           : 'renderizaResultadoVentanaItem(id,codigo,nombre)',
												        }
								    },
	    tbar        : [
							        {
							          xtype       : 'button',
							          width       : 60,
							          height      : 56,
							          text        : 'Regresar',
							          scale       : 'large',
							          iconCls     : 'regresar',
							          iconAlign   : 'top',
							          handler     : function(){ Win_Ventana_buscar_items.close(id) }
							        }
								    ]
		}).show();
	}

	//============================== MOSTRAR ITEMS =============================//
	function renderizaResultadoVentanaItem(id,codigo,nombre){
		if(id != '' && codigo != '' && nombre != ''){
			var cadenaBuscar = '';
			for(i = 0; i < arrayItemsIC.length; i++){
				if(typeof(arrayItemsIC[i]) != "undefined" && arrayItemsIC[i] != ""){
					if(id.indexOf(arrayItemsIC[i]) == 0){
					  alert("Ya se agrego el item");
					  return;
					}
				}
			}

      var div = document.createElement('div');
      div.setAttribute('id','row_item_' + id);
      div.setAttribute('class','row');
      document.getElementById('body_grilla_filtro_item').appendChild(div);

      var fila = `<div class="row" id="row_item_${id}">
                    <div class="cell" data-col="1"></div>
                    <div class="cell" data-col="2">${codigo}</div>
                    <div class="cell" data-col="3" title="${nombre}">${nombre}</div>
                    <div class="cell" data-col="1" data-icon="delete" onclick="eliminaItem(${id})" title="Eliminar Item"></div>
                  </div>`;

      //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
      document.getElementById('row_item_' + id).innerHTML = fila;

      //LLENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
      arrayItemsIC[id] = id;
		}
	}

	//============================== ELIMINAR ITEM =============================//
	function eliminaItem(id){
		delete arrayItemsIC[id];
		(document.getElementById("row_item_" + id)).parentNode.removeChild(document.getElementById("row_item_" + id));
	}
</script>
