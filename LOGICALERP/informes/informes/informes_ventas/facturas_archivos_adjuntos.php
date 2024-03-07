<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE   	  ///**/
	/**/																						/**/
	/**/				 $informe = new MyInforme(); 				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa          						= $_SESSION['EMPRESA'];					//ID EMPRESA
	$id_sucursal_default 						= $_SESSION['SUCURSAL'];				//ID SUCURSAL
	$informe->BtnGenera             = 'false';											//BOTON GENERAR
	$informe->InformeName						=	'facturas_archivos_adjuntos'; //NOMBRE DEL INFORME
	$informe->InformeTitle					=	'Facturas Archivos Adjuntos'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; 											//FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false'; 											//FILTRO EMPRESA, SUCURSAL
	$informe->InformeExportarPDF		= 'false'; 											//SI EXPORTA A PDF
	$informe->InformeExportarXLS		= 'false'; 											//SI EXPORTA A XLS
	$informe->InformeTamano 				= 'CARTA-HORIZONTAL';						//TAMAÑO Y ORIENTACION
	$informe->DefaultCls            = ''; 													//RESET STYLE CSS
	$informe->HeightToolbar         = 80; 													//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar');

	/**//////////////////////////////////////////////////////////////**/
	/**///             	INICIALIZACION DE LA GRILLA             	///**/
	/**/																														/**/
	/**/		$informe->Link = $link;  			//Conexion a la BD				/**/
	/**/		$informe->inicializa($_POST); //Variables POST					/**/
	/**/		$informe->GeneraInforme(); 		//Inicializa la Grilla		/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contTercero = 1;

	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		var	sucursal                 = document.getElementById('filtro_sucursal_facturas_archivos_adjuntos').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	contenido                  = document.getElementById('contenido').value
		, arraytercerosJSON          = Array()
		,	i                          = 0

		arraytercerosFAA.forEach(function(id_tercero){  arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON = JSON.stringify(arraytercerosJSON);

		Ext.get('RecibidorInforme_facturas_archivos_adjuntos').load({
			url     : '../informes/informes/informes_ventas/facturas_archivos_adjuntos_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :	{
									IMPRIME_HTML							 : 'true',
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									sucursal                   : sucursal,
									arraytercerosJSON          : arraytercerosJSON,
									contenido                  : contenido
								}
		});

		localStorage.MyInformeFiltroFechaFinalFAA  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioFAA = MyInformeFiltroFechaInicio;
		localStorage.sucursalFAA               		 = sucursal;
		localStorage.contenidoFAA              		 = contenido;

		document.getElementById("RecibidorInforme_facturas_archivos_adjuntos").style.padding = 20;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		var sucursal                 = document.getElementById('filtro_sucursal_facturas_archivos_adjuntos').value
		,	MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		, arraytercerosJSON          = Array()
		,	contenido                  = document.getElementById('contenido').value
		,	i = 0

		arraytercerosFAA.forEach(function(id_tercero) { arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON = JSON.stringify(arraytercerosJSON);

		var data = tipo_documento+"=true"
							+"&nombre_informe=Facturas_Archivos_Adjuntos"
							+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
							+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
							+"&sucursal="+sucursal
							+"&arraytercerosJSON="+arraytercerosJSON
							+"&contenido="+contenido

		window.open("../informes/informes/informes_ventas/facturas_archivos_adjuntos_Result.php?"+data);
	}

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){
		if(localStorage.MyInformeFiltroFechaInicioFAA == "" || localStorage.MyInformeFiltroFechaFinalFAA == ""){
			alert("Debe generar el informe al menos una vez");
			return;
		}

		var sucursal                 = localStorage.sucursalFAA
		,	MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioFAA
		,	MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalFAA
		, arraytercerosJSON          = Array()
		,	contenido                  = localStorage.contenidoFAA
		,	i = 0

		arraytercerosFAA.forEach(function(id_tercero) { arraytercerosJSON[i] = id_tercero; i++; });
    arraytercerosJSON = JSON.stringify(arraytercerosJSON);

		var data = tipo_documento+"=true"
							+"&nombre_informe=Facturas_Archivos_Adjuntos"
							+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
							+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
							+"&sucursal="+sucursal
							+"&arraytercerosJSON="+arraytercerosJSON
							+"&contenido="+contenido

		window.open("../informes/informes/informes_ventas/facturas_archivos_adjuntos_Result.php?"+data);
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_facturas_archivos_adjuntos = new Ext.Window({
	    width       : 693,
	    height      : 366,
	    id          : 'Win_Ventana_configurar_facturas_archivos_adjuntos',
	    title       : 'Aplicar Filtros',
	    modal       : true,
	    autoScroll  : false,
	    closable    : true,
	    autoDestroy : true,
	    autoLoad    : {
							        url     : '../informes/informes/informes_ventas/wizard_facturas_archivos_adjuntos.php',
							        scripts : true,
							        nocache : true,
							        params  : {
											            opc : 'cuerpoVentanaConfiguracionFacturasArchivosAdjuntos',
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
											                                params  : { opc  : 'facturas_archivos_adjuntos' }
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
		                    handler     : function(){ Win_Ventana_configurar_facturas_archivos_adjuntos.close() }
		                	}
	    							]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.MyInformeFiltroFechaFinalFAA  = "";
		localStorage.MyInformeFiltroFechaInicioFAA = "";
		localStorage.sucursalFAA                	 = "global";
		localStorage.contenidoFAA               	 = "todos";
		arraytercerosFAA.length                    =  0;
		Win_Ventana_configurar_facturas_archivos_adjuntos.close();
		ventanaConfigurarInforme();
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
	function ventanaBusquedaTercero(){
    var myalto  = Ext.getBody().getHeight();
    var myancho = Ext.getBody().getWidth();

    Win_VentanaCliente_ = new Ext.Window({
      width       : 650,
      height      : 608,
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
																	tabla                : 'terceros',
																	id_tercero           : 'id',
																	tercero              : 'nombre_comercial',
																	opcGrillaContable 	 : 'facturas_archivos_adjuntos',
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
					              handler     : function(){ Win_VentanaCliente_.close(id) }
						          }
						        ]
    }).show();
	}

	//============================= AGREGAR CLIENTE ============================//
	function checkGrilla(checkbox,cont){
		if(checkbox.checked == true){

			var div = document.createElement('div');
			div.setAttribute('id','row_tercero_'+cont);
      div.setAttribute('class','row');
      document.getElementById('body_grilla_filtro').appendChild(div);

      //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
      var nit   	= document.getElementById('nit_'+cont).innerHTML
      var tercero = document.getElementById('tercero_'+cont).innerHTML;

      var fila = `<div class="row" id="row_tercero_${cont}">
                    <div class="cell" data-col="1">${contTercero}</div>
                    <div class="cell" data-col="2">${nit}</div>
                    <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                    <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont})" title="Eliminar Cliente"></div>
                  </div>`;

      //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
      tercerosConfiguradosFAA[cont]=fila;
      //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
      document.getElementById('row_tercero_'+cont).innerHTML = fila;
      contTercero++;
      //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
      arraytercerosFAA[cont] = checkbox.value;
		}
		else if(checkbox.checked == false){
			delete arraytercerosFAA[cont];
			delete tercerosConfiguradosFAA[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	//============================= ELIMINAR CLIENTE ============================//
	function eliminaCliente(cont){
		delete arraytercerosFAA[cont];
		delete tercerosConfiguradosFAA[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}
</script>
