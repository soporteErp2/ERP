<?php
	error_reporting(E_ERROR | E_PARSE);
	session_start();
	include("../../../configuracion/define_variables.php");
?>

<data-table 
	endpoint='{
				"url" : "consulta_documentos_cuentas_colgaap/bd/Api.php",
				"method" : "GET",
				"params" : [
							{"id_empresa":"<?=$_SESSION['EMPRESA']?>"},
							{"id_sucursal":"<?=$filtro_sucursal?>"},
							{"contabilidad":"<?=$contabilidad?>"},
							{"tipo_documento":"<?=$tipo_documento?>"},
							{"fecha_inicial":"<?=$fecha_inicial?>"},
							{"fecha_final":"<?=$fecha_final?>"}
						]
			}' 
	columns='[
				{"field":"Fecha","class":"","type":"","options":"","callback":"document_details"},
				{"field":"Tipo","class":"","type":"","options":"","callback":"document_details"},
				{"field":"Consecutivo","class":"","type":"","options":"","callback":"document_details"},
				{"field":"Documento","class":"","type":"","options":"","callback":"document_details"},
				{"field":"Nit","class":"","type":"","options":"","callback":"document_details"},
				{"field":"Tercero","class":"","type":"","options":"","callback":"document_details"},
				{"field":"Sucursal","class":"","type":"","options":"","callback":"document_details"}
			]'
></data-table>
<script>

		function document_details(params){
			var myalto                   = Ext.getBody().getHeight()
			,	myancho                  = Ext.getBody().getWidth()
			,	sucursal                 = document.getElementById("div_consultarDocumentos_sucursal_"+id).innerHTML
			,	id_sucursal              = document.getElementById("filtro_sucursal_panel_filtro_sucursal").value
			,	fecha_documento          = document.getElementById("fecha_documento_consultarDocumentos_"+id).innerHTML
			,	tipo_documento           = document.getElementById('tipo_documento_consultarDocumentos_'+id).innerHTML
			,	id_documento             = document.getElementById('id_documento_consultarDocumentos_'+id).innerHTML
			,	consecutivo              = document.getElementById('consecutivo_consultarDocumentos_'+id).innerHTML
			,	id_tercero               = document.getElementById('id_tercero_consultarDocumentos_'+id).innerHTML
			,	tipo_documento_extendido = document.getElementById("div_consultarDocumentos_tipo_documento_extendido_"+id).innerHTML
			,   numero_documento         = document.getElementById("div_consultarDocumentos_consecutivo_documento_"+id).innerHTML
			,	title                    = tipo_documento_extendido+" No. "+numero_documento;

			var imprimeVarPhp = 'sucursal 					: "'+sucursal+'"'
								+',id_sucursal              : "'+id_sucursal+'"'
								+',fecha_documento          : "'+fecha_documento+'"'
								+',id_documento             : "'+id_documento+'"'
								+',consecutivo_documento    : "'+consecutivo+'"'
								+',tipo_documento           : "'+tipo_documento+'"'
								+',tipo_documento_extendido : "'+tipo_documento_extendido+'"'
								+',numero_documento         : "'+numero_documento+'"'
								+',id_tercero               : "'+id_tercero+'"';

			var hidenBoton = (tipo_documento == 'FV' || tipo_documento == 'FC' || tipo_documento == 'NDFV' || tipo_documento == 'NDFC')? false: true;

			Win_Ventana_Consultar_cuentas_colgaap = new Ext.Window({
				height		: myalto - 80,
				width		: myancho - 70,
				id			: 'Win_Ventana_Consultar_cuentas_colgaap',
				title		: title,
				modal		: true,
				autoScroll	: true,
				closable	: false,
				autoDestroy : true,
				bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
				items		:
				[
					{
	                    closable    : false,
	                    border      : false,
	                    autoScroll  : true,
	                    iconCls     : '',
	                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
	                    items       :
	                    [

							{
								xtype		: "panel",
								id			: 'contenedor_consultarCuentasColgaap',
								border		: false,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							}
						],
						tbar		:
						[
							{
								xtype	: 'buttongroup',
								columns	: 3,
								height 	: 70,
								title	: 'Filtro Contabilidad',
								items	:
								[
									{
										xtype		: 'panel',
										border		: false,
										width		: 150,
										height		: 56,
										bodyStyle 	: 'background-color:rgba(255,255,255,0);',
										autoLoad    :
										{
											url		: '../funciones_globales/filtros/filtro_niif.php',
											scripts	: true,
											nocache	: true,
											params	:
											{
												opc           : "consultarCuentasColgaap",
												tabla         : 'asientos_colgaap',
												imprimeVarPhp : imprimeVarPhp,
												renderizaBody : 'true',
												newUrlRender  : 'consulta_documentos_cuentas_colgaap/consulta_cuentas_colgaap.php',
											}
										}
									}
								]
							},
							{
		                        xtype       : 'button',
		                        width       : 60,
		                        height      : 56,
		                        id 			: 'btnEditarContabilizacion',
		                        text        : 'Editar',
		                        scale       : 'large',
		                        iconCls     : 'edit',
		                        iconAlign   : 'top',
		                        hiden    	: true,
		                        handler     : function(){ editarCuentasDocumento() }
		                    },
							{
		                        xtype       : 'button',
		                        width       : 60,
		                        height      : 56,
		                        text        : 'Imprimir',
		                        scale       : 'large',
		                        iconCls     : 'genera_pdf',
		                        iconAlign   : 'top',
		                        handler     : function(){ imprimirBusqueda() }
		                    },
							{
								xtype		: 'button',
								width 		: 60,
								height 		: 56,
								text		: 'Regresar',
								scale		: 'large',
								iconCls		: 'regresar',
								iconAlign	: 'top',
								handler 	: function(){Win_Ventana_Consultar_cuentas_colgaap.close()}
							},'->',
		                    {
		                        xtype       : "tbtext",
		                        text        : 	'<div class="contenedorSaldos">'
			                        				+'<div id="saldoConsultaCuenta_debito"></div>'
			                        				+'<div id="saldoConsultaCuenta_credito"></div>'
			                        				+'<div id="saldoConsultaCuenta"></div>'
		                        				+'<div>',
		                        scale       : "large",
		                    }
						]
					}
				]
			}).show();
		}

		function imprimirBusquedaPricipal(){
			// var tipo_cuenta  = document.getElementById('filtro_tipo_cuenta').value;
			var tipo_documento  = document.getElementById('filtro_documento').value;
			var fecha_inicial   = document.getElementById('filtroFechaInicial').value;
			var fecha_final     = document.getElementById('filtroFechaFinal').value;
			var fecha_final     = document.getElementById('filtroFechaFinal').value;
			var filtro_sucursal = document.getElementById('filtro_sucursal_panel_filtro_sucursal').value;


			var varImprimir = "fecha_inicial="+fecha_inicial
								+"&fecha_final="+fecha_final
								+"&consulta=principal&filtro_sucursal="+filtro_sucursal
								+"&filtro_busqueda="+filtroBusqueda
								+"&tipo_documento="+tipo_documento;
								// +"&tipo_cuenta="+tipo_cuenta


			window.open("consulta_documentos_cuentas_colgaap/imprimir.php?"+varImprimir);
		}

    </script>
