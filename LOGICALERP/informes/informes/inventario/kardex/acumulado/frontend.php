<?php
	include('../../../../../../configuracion/conectar.php');
	include('../../../../../../configuracion/define_variables.php');
	include('../../../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE	    ///**/
	/**/										    /**/
	/**/		 $informe = new MyInforme();	    /**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa                     = $_SESSION['EMPRESA'];
	$id_sucursal                    = $_SESSION['SUCURSAL'];
	$informe->BtnGenera             = 'false';
	$informe->InformeName           = 'kardex_acumulado';  									//NOMBRE DEL INFORME
	$informe->InformeTitle          = 'Kardex acumulado de items';							 	    //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode  = 'false'; 																		//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu      = 'false'; 																		//FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF    = "false";																		//SI EXPORTA A PDF
	$informe->InformeExportarXLS    = "false";																		//SI EXPORTA A XLS
	$informe->InformeTamano         = "CARTA-HORIZONTAL";
	$informe->DefaultCls            = ''; 																				//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 																				//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	// $informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	// $informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar / generar','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	/**//////////////////////////////////////////////////////////////**/
	/**///		     INICIALIZACION DE LA GRILLA		  	   	  ///**/
	/**/			$informe->Link = $link;  			//Conexion a la BD			/**/
	/**/			$informe->inicializa($_POST);	//Variables POST				/**/
	/**/			$informe->GeneraInforme(); 	  //Inicializa la Grilla	/**/
	/**//////////////////////////////////////////////////////////////**/
?>

<script>
	arrayItemsKA = [];

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		wizard_kardex_acumulado = new Ext.Window({
	    width       : 750,
	    height      : 500,
	    id          : 'wizard_kardex_acumulado',
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
																opc : 'kardex_acumulado'
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
			                // {
			                //   xtype       : 'button',
			                //   width       : 60,
			                //   height      : 56,
			                //   text        : 'Exportar<br>PDF',
			                //   scale       : 'large',
			                //   iconCls     : 'genera_pdf',
			                //   iconAlign   : 'top',
			                //   handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
			                // },
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
			                // {
			                //   xtype       : 'button',
			                //   width       : 60,
			                //   height      : 56,
			                //   text        : 'Reiniciar<br>Filtros',
			                //   scale       : 'large',
			                //   iconCls     : 'restaurar',
			                //   iconAlign   : 'top',
			                //   handler     : function(){ resetFiltros() }
			                // },
							// '-',
			                {
						            xtype       : 'button',
						            width       : 60,
						            height      : 56,
						            text        : 'Regresar',
						            scale       : 'large',
						            iconCls     : 'regresar',
						            iconAlign   : 'top',
						            handler     : function(){ wizard_kardex_acumulado.close()	}
							        }
			    	],
			autoLoad : {
							url     : '../informes/informes/inventario/kardex/acumulado/wizard.php',
							scripts : true,
							nocache : true,
							params  : {
										opc : 'cuerpoVentanaConfiguracionInventarioCosto',
									}
							}
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		// localStorage.MyInformeFiltroFechaFinalIC  = "";
		// localStorage.MyInformeFiltroFechaInicioIC = "";
		// localStorage.sucursalIC                   = "global";
		// localStorage.bodegaIC                     = "global";
		// localStorage.filtro_familiaIC					    = "";
		// localStorage.filtro_grupoIC					      = "";
		// localStorage.filtro_subgrupoIC     			  = "";
		// localStorage.itemsKardexAcum             = "";
		// arrayItemsKA.length                       = 0;

		// wizard_inventario_costo.close();
  //   	ventanaConfigurarInforme();
	}

	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		// MyLoading2("on",{"texto":"Generando, esto puede tomar unos minutos"});

		let sucursal           = document.getElementById('filtro_sucursal_kardex_acumulado')
		  , bodega             = document.getElementById('filtro_bodega_kardex_acumulado').value
		  , separador_miles    = document.getElementById('separador_miles_kardex_acumulado').value
		  , separador_decimales = document.getElementById('separador_decimales_kardex_acumulado').value
		  , fecha_inicio       = document.getElementById("fecha_inicio_kardex_acumulado").value
		  , fecha_final        = document.getElementById("fecha_final_kardex_acumulado").value
		  , items              = localStorage.itemsKA
		  , selectedIndex      = sucursal.selectedIndex
		  , selectedOptionText = sucursal.options[selectedIndex].textContent

		Ext.get('RecibidorInforme_kardex_acumulado').load({
			url     : '../informes/informes/inventario/kardex/acumulado/backend.php',
			text	  : 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  : {
						"sucursal" : sucursal.value,
						bodega,
						separador_miles,
						separador_decimales,
						fecha_inicio,
						fecha_final,
						items
					}
		});

		document.getElementById("RecibidorInforme_kardex_acumulado").style.padding = 20;

		localStorage.sucursal_name_ka = selectedOptionText;
		localStorage.sucursalKA       = sucursal.value;
		localStorage.bodegaKA         = bodega;
		localStorage.fecha_inicio_ka  = fecha_inicio;
		localStorage.fecha_final_ka   = fecha_final;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		let sucursal           = document.getElementById('filtro_sucursal_kardex_acumulado')
		  , bodega             = document.getElementById('filtro_bodega_kardex_acumulado').value
		  , separador_miles    = document.getElementById('separador_miles_kardex_acumulado').value
		  , separador_decimales = document.getElementById('separador_decimales_kardex_acumulado').value
		  , fecha_inicio       = document.getElementById("fecha_inicio_kardex_acumulado").value
		  , fecha_final        = document.getElementById("fecha_final_kardex_acumulado").value
		  , items              = localStorage.itemsKA
		  , selectedIndex      = sucursal.selectedIndex
		  , selectedOptionText = sucursal.options[selectedIndex].textContent

		var data =  tipo_documento + `=true
										&sucursal==${sucursal.value}
										&bodega=${bodega}
										&separador_miles=${separador_miles}
										&separador_decimales=${separador_decimales}
										&fecha_inicio=${fecha_inicio}
										&fecha_final=${fecha_final}
										&items=${items}`

		window.open("../informes/informes/inventario/kardex/acumulado/backend.php?" + data);

		localStorage.sucursal_name_ka = selectedOptionText;
		localStorage.sucursalKA       = sucursal.value;
		localStorage.bodegaKA         = bodega;
		localStorage.fecha_inicio_ka  = fecha_inicio;
		localStorage.fecha_final_ka   = fecha_final;
	}

	//===================== VENTANA PARA BUSCAR LOS ITEMS ======================//
	function ventanaBusquedaItemKA(){
		if (localStorage.itemsKA && JSON.parse(localStorage.itemsKA)) {
			saved_items = JSON.parse(localStorage.itemsKA);
			if (saved_items.length>=10) {
				alert("solo se pueden seleccionar un maximo de 10 items");
				return
			}
		}
		
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
									funcion           : 'renderizaItemKardexAcum(id,codigo,nombre)',
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
	function renderizaItemKardexAcum(id,codigo,nombre){
		if(id != '' && codigo != '' && nombre != ''){
			let saved_items = null;
			if (localStorage.itemsKA && JSON.parse(localStorage.itemsKA)) {
				saved_items = JSON.parse(localStorage.itemsKA);
			}

			if (saved_items) {
				if (saved_items.length>=10) {
					alert("solo se pueden seleccionar un maximo de 10 items");
					return
				}

				let search_item = saved_items.find(item => item.id === id);
				if (search_item) {
					alert("Ya se agrego el item");
					return;
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
			arrayItemsKA[id] = id;

			let item_detail = {id , codigo, nombre}
			,	items = [item_detail]

			if (saved_items) {
				saved_items.push(item_detail);
				items = saved_items
			}

			localStorage.itemsKA = JSON.stringify(items)
		}
	}

	//============================== ELIMINAR ITEM =============================//
	function eliminaItem(id){
		let items = JSON.parse(localStorage.itemsKA);
		let filtered_items = items.filter(item => item.id != id);
		localStorage.itemsKA = JSON.stringify(filtered_items);

		(document.getElementById("row_item_" + id)).parentNode.removeChild(document.getElementById("row_item_" + id));
	}
</script>
