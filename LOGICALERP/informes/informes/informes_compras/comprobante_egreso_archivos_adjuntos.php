<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 	 INICIALIZACION DE LA CLASE	  	  ///**/
	/**/																						/**/
	/**/				 $informe = new MyInforme();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa          						= $_SESSION['EMPRESA'];
	$id_sucursal_default 						= $_SESSION['SUCURSAL'];
	$informe->BtnGenera             = 'false';
	$informe->InformeName						= 'comprobante_egreso_archivos_adjuntos';  		//NOMBRE DEL INFORME
	$informe->InformeTitle					= 'Comprobantes De Egreso Archivos Adjuntos'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	= 'false'; 																		//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			= 'false'; 																		//FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= 'false'; 																		//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= 'false'; 																		//SI EXPORTA A XLS
	$informe->InformeTamano 				= 'CARTA-HORIZONTAL';
	$informe->DefaultCls            = ''; 		 																		//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		 																		//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe');

	/**//////////////////////////////////////////////////////////////**/
	/**///								INICIALIZACION DE LA GRILLA	  	  		  ///**/
	/**/																														/**/
	/**/		$informe->Link = $link;  			//Conexion a la BD				/**/
	/**/		$informe->inicializa($_POST);	//Variables POST					/**/
	/**/		$informe->GeneraInforme(); 		//Inicializa la Grilla		/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	arrayTercerosCEAA = [];
	contTercero 			= 1;

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
    var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_comprobante_egreso_archivos_adjuntos = new Ext.Window({
	    width       : myancho - (myancho * 35 / 100),
	    height      : myalto - (myalto * 30 / 100),
	    id          : 'Win_Ventana_configurar_comprobante_egreso_archivos_adjuntos',
	    title       : 'Aplicar Filtros',
	    modal       : true,
	    autoScroll  : false,
	    closable    : true,
	    autoDestroy : true,
	    autoLoad    : {
							        url     : '../informes/informes/informes_compras/wizard_comprobante_egreso_archivos_adjuntos.php',
							        scripts : true,
							        nocache : true,
							        params  : {
											            opc : 'cuerpoVentanaConfiguracionComprobanteEgresoArchivosAdjuntos',
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
												                              params  : {
																																	opc  : 'sucursales_comprobante_egreso_archivos_adjuntos'
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
			                  handler     : function(){ Win_Ventana_configurar_comprobante_egreso_archivos_adjuntos.close() }
			                }
	    							]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalCEAA  = "";
		localStorage.MyInformeFiltroFechaInicioCEAA = "";
		localStorage.sucursalCEAA                   = "";
		localStorage.contenidoCEAA                  = "";
		localStorage.arrayTercerosJSONCEAA          = "";
		arrayTercerosCEAA.length                    = 0;
		Win_Ventana_configurar_comprobante_egreso_archivos_adjuntos.close();
		ventanaConfigurarInforme();
	}

	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		var	sucursal                 	= document.getElementById('filtro_sucursal_sucursales_comprobante_egreso_archivos_adjuntos').value
		, MyInformeFiltroFechaInicio 	= document.getElementById('MyInformeFiltroFechaInicio').value
		, MyInformeFiltroFechaFinal	  = document.getElementById('MyInformeFiltroFechaFinal').value
		, contenido                	  = document.getElementById('contenido').value
		, arrayTercerosJSON          	= Array()

		i	= 0
		arrayTercerosCEAA.forEach(function(id_tercero){ arrayTercerosJSON[i] = id_tercero; i++; });
    arrayTercerosJSON = JSON.stringify(arrayTercerosJSON);

		Ext.get('RecibidorInforme_comprobante_egreso_archivos_adjuntos').load({
			url     : '../informes/informes/informes_compras/comprobante_egreso_archivos_adjuntos_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :	{
									IMPRIME_HTML               : 'true',
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									sucursal                   : sucursal,
									contenido                  : contenido,
									arrayTercerosJSON          : arrayTercerosJSON
								}
		});

		localStorage.MyInformeFiltroFechaFinalCEAA  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioCEAA = MyInformeFiltroFechaInicio;
		localStorage.sucursalCEAA                   = sucursal;
		localStorage.contenidoCEAA                  = contenido;
		localStorage.arrayTercerosJSONCEAA					= arrayTercerosJSON;

		document.getElementById("RecibidorInforme_comprobante_egreso_archivos_adjuntos").style.padding = 20;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		var	sucursal                 	= document.getElementById('filtro_sucursal_sucursales_comprobante_egreso_archivos_adjuntos').value
		, MyInformeFiltroFechaInicio 	= document.getElementById('MyInformeFiltroFechaInicio').value
		, MyInformeFiltroFechaFinal	  = document.getElementById('MyInformeFiltroFechaFinal').value
		, contenido                	  = document.getElementById('contenido').value
		, arrayTercerosJSON          	= Array()

		i	= 0
		arrayTercerosCEAA.forEach(function(id_tercero){ arrayTercerosJSON[i] = id_tercero; i++; });
    arrayTercerosJSON = JSON.stringify(arrayTercerosJSON);

		var data =  tipo_documento + `=true
								&sucursal=${sucursal}
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&contenido=${contenido}
								&arrayTercerosJSON=${arrayTercerosJSON}`

		window.open("../informes/informes/informes_compras/comprobante_egreso_archivos_adjuntos_Result.php?" + data);

		localStorage.MyInformeFiltroFechaFinalCEAA  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioCEAA = MyInformeFiltroFechaInicio;
		localStorage.sucursalCEAA                   = sucursal;
		localStorage.contenidoCEAA                  = contenido;
		localStorage.arrayTercerosJSONCEAA					= arrayTercerosJSON;
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
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
      autoLoad    : {
			                url     : '../informes/BusquedaTerceros.php',
			                scripts : true,
			                nocache : true,
			                params  : {
																	tabla             : 'terceros',
																	id_tercero        : 'id',
																	tercero           : 'nombre_comercial',
																	opcGrillaContable	: 'comprobante_egreso_archivos_adjuntos',
																	cargaFuncion      : '',
																	nombre_grilla     : '',
								                }
			            	},
      tbar        : [
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

	//============================ MOSTRAR TERCEROS ============================//
	function checkGrilla(checkbox,cont){
		if(checkbox.checked == true){
			var div = document.createElement('div');
      div.setAttribute('id','row_tercero_' + cont);
      div.setAttribute('class','row');
      document.getElementById('body_grilla_filtro_tercero').appendChild(div);

      //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
      var nit     = document.getElementById('nit_' + cont).innerHTML
      ,   tercero = document.getElementById('tercero_' + cont).innerHTML;

      var fila = `<div class="row" id="row_tercero_${cont}">
                    <div class="cell" data-col="1"></div>
                    <div class="cell" data-col="2">${nit}</div>
                    <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                    <div class="cell" data-col="1" data-icon="delete" onclick="eliminaTercero(${cont})" title="Eliminar Cliente"></div>
                  </div>`;

      //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
      document.getElementById('row_tercero_' + cont).innerHTML = fila;
      contTercero++;

      //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
      arrayTercerosCEAA[checkbox.value] = checkbox.value;
		}
		else if(checkbox.checked == false){
			delete arrayTercerosCEAA[cont];
			(document.getElementById("row_tercero_" + cont)).parentNode.removeChild(document.getElementById("row_tercero_" + cont));
		}
	}

	//============================ ELIMINAR TERCEROS ===========================//
	function eliminaTercero(cont){
		delete arrayTercerosCEAA[cont];
		(document.getElementById("row_tercero_" + cont)).parentNode.removeChild(document.getElementById("row_tercero_" + cont));
	}
</script>
