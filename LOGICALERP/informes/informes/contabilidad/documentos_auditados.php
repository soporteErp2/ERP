<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE   	  ///**/
	/**/																						/**/
	/**/					$informe = new MyInforme();				/**/
	/**/																						/**/
	/**//////////////////////////////////////////////**/

	$id_empresa          						= $_SESSION['EMPRESA'];
	$id_sucursal_default 						= $_SESSION['SUCURSAL'];
	$informe->InformeName						=	'documentos_auditados';  //NOMBRE DEL INFORME
	$informe->InformeTitle					=	'Documentos Auditados';  //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; 								 //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu			=	'false'; 								 //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false'; 								 //FILTRO FECHA
	$informe->DefaultCls            = ''; 		 								 //RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		 								 //HEIGHT TOOLBAR
	$informe->InformeExportarPDF		= "false"; 								 //SI EXPORTA A PDF
	$informe->InformeExportarXLS		= "false"; 								 //SI EXPORTA A XLS
	$informe->InformeTamano 				= "CARTA-HORIZONTAL";
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	$informe->BtnGenera             = 'false';

	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_documentos_auditados');

	/**//////////////////////////////////////////////////////////////**/
	/**///	        			INICIALIZACION DE LA GRILLA         	  ///**/
	/**/																														/**/
	/**/  	$informe->Link = $link;  			//Conexion a la BD			  /**/
	/**/  	$informe->inicializa($_POST); //Variables POST				  /**/
	/**/  	$informe->GeneraInforme(); 		//Inicializa la Grilla	  /**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contEmpleados    = 1;
	arrayEmpleadosDA = new Array();

	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	  MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	  sucursal                   = document.getElementById('filtro_sucursal_sucursales_documentos_auditados').value
		,	  tipoDocumento              = document.getElementById('tipoDocumento').value
		,	  arrayEmpleadosJSON         = Array()

		i = 0
		arrayEmpleadosDA.forEach(function(id_empleado){ arrayEmpleadosJSON[i] = id_empleado; i++; });
    arrayEmpleadosJSON = JSON.stringify(arrayEmpleadosJSON);

		Ext.get('RecibidorInforme_documentos_auditados').load({
			url     : '../informes/informes/contabilidad/documentos_auditados_Result.php',
			text		: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :	{
									IMPRIME_HTML               : 'true',
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									sucursal                   : sucursal,
									tipoDocumento     			   : tipoDocumento,
									arrayEmpleadosJSON         : arrayEmpleadosJSON
								}
		});

		localStorage.MyInformeFiltroFechaInicioDA = MyInformeFiltroFechaInicio;
		localStorage.MyInformeFiltroFechaFinalDA  = MyInformeFiltroFechaFinal;
		localStorage.sucursalDA                   = sucursal;
		localStorage.tipoDocumentoDA              = tipoDocumento;
		localStorage.arrayEmpleadosJSONDA					= arrayEmpleadosJSON;

		document.getElementById("RecibidorInforme_documentos_auditados").style.padding = 20;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
		,	  MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
		,	  sucursal                   = document.getElementById('filtro_sucursal_sucursales_documentos_auditados').value
		,	  tipoDocumento              = document.getElementById('tipoDocumento').value
		,	  arrayEmpleadosJSON         = Array()

		i = 0
		arrayEmpleadosDA.forEach(function(id_empleado){ arrayEmpleadosJSON[i] = id_empleado; i++; });
    arrayEmpleadosJSON = JSON.stringify(arrayEmpleadosJSON);

		var data =  tipo_documento + `=true
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&sucursal=${sucursal}
								&tipoDocumento=${tipoDocumento}
								&arrayEmpleadosJSON=${arrayEmpleadosJSON}`

		window.open("../informes/informes/contabilidad/documentos_auditados_Result.php?"+data);
	}

	//================ GENERAR ARCHIVO DESDE LA VISTA PRINCIPAL ================//
	function generarPDF_Excel_principal(tipo_documento){
		var MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalDA
		,	  MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioDA
		,	  sucursal                   = localStorage.sucursalDA
		,	  tipoDocumento              = localStorage.tipoDocumentoDA
		,	  arrayEmpleadosJSON         = localStorage.arrayEmpleadosJSONDA

		if(localStorage.MyInformeFiltroFechaInicioDA == '' || localStorage.MyInformeFiltroFechaFinalDA == ''){
			alert('Debe generar el informe al menos una vez.');
			return;
		}

		var data =  tipo_documento + `=true
								&MyInformeFiltroFechaFinal=${MyInformeFiltroFechaFinal}
								&MyInformeFiltroFechaInicio=${MyInformeFiltroFechaInicio}
								&sucursal=${sucursal}
								&tipoDocumento=${tipoDocumento}
								&arrayEmpleadosJSON=${arrayEmpleadosJSON}`

		window.open("../informes/informes/contabilidad/documentos_auditados_Result.php?" + data);
	}

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		z = Array();
		z.length = 0;
		if(typeof(localStorage.arrayEmpleadosJSONDA) != "undefined" && localStorage.tipoDocumentoDA != ""){
			x = JSON.parse(localStorage.arrayEmpleadosJSONDA);

			var y = arrayEmpleadosDA.filter(function(element){
				if(element != '') return element;
			});

			y.forEach(function(valor,indice,array){
				z[indice] = y[indice].toString();
			});

			if(JSON.stringify(x) != JSON.stringify(z)){
				arrayEmpleadosDA = x;
			}
		}

		Win_Ventana_configurar_documentos_auditados = new Ext.Window({
	    width       : 750,
	    height      : 450,
	    id          : 'Win_Ventana_configurar_documentos_auditados',
	    title       : 'Aplicar Filtros',
	    modal       : true,
	    autoScroll  : false,
	    closable    : true,
	    autoDestroy : true,
	    autoLoad    : {
							        url     : '../informes/informes/contabilidad/wizard_documentos_auditados.php',
							        scripts : true,
							        nocache : true,
							        params  : {
											            opc : 'ventanadocumentosAuditados'
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
																																	opc  : 'sucursales_documentos_auditados'
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
				                handler     : function(){ Win_Ventana_configurar_documentos_auditados.close(); }
				              }
					    			]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){
		localStorage.MyInformeFiltroFechaInicioDA = "";
		localStorage.MyInformeFiltroFechaFinalDA  = "";
		localStorage.sucursalDA                   = "";
		localStorage.tipoDocumentoDA              = "";
		localStorage.arrayEmpleadosJSONDA					= "";
		arrayEmpleadosDA.length                   = 0;
		Win_Ventana_configurar_documentos_auditados.close();
		ventanaConfigurarInforme();
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
			autoLoad    :	{
											url     : '../informes/BusquedaTerceros.php',
											scripts : true,
											nocache : true,
											params  : {
																	tabla             : 'empleados',
																	id_tercero        : 'id',
																	tercero           : 'nombre',
																	opcGrillaContable : 'documentos_auditados',
																	cargaFuncion      : '',
																	nombre_grilla     : '',
																}
									  },
			tbar        :	[
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
			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
			var div = document.createElement('div');
      div.setAttribute('id','row_empleado_'+cont);
      div.setAttribute('class','filaBoleta');
      document.getElementById('body_grilla_filtro_empleados').appendChild(div);

      //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			nit     = document.getElementById('nit_'+cont).innerHTML;
			tercero = document.getElementById('tercero_'+cont).innerHTML;

			var fila = `<div class="row" id="row_empleado_${cont}">
                    <div class="cell" data-col="1">${contEmpleados}</div>
                    <div class="cell" data-col="2">${nit}</div>
                    <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                    <div class="cell" data-col="1" data-icon="delete" onclick="eliminaEmpleado(${cont})" title="Eliminar Empleado"></div>
                  </div>`;

      //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
      document.getElementById('row_empleado_'+cont).innerHTML = fila;
      contEmpleados++;

      //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
      arrayEmpleadosDA[cont] = checkbox.value;
		}
		else if(checkbox.checked == false){
			delete arrayEmpleadosDA[cont];
			(document.getElementById("row_empleado_"+cont)).parentNode.removeChild(document.getElementById("row_empleado_"+cont));
		}
	}

	//============================ ELIMINAR TERCERO ============================//
	function eliminaEmpleado(cont){
		console.log(arrayEmpleadosDA);
		delete arrayEmpleadosDA[cont];
		console.log(arrayEmpleadosDA);
		(document.getElementById("row_empleado_"+cont)).parentNode.removeChild(document.getElementById("row_empleado_"+cont));
	}
</script>
