<?php

include("../configuracion/conectar.php");

$permiso_conceptos_x_cargo            = (user_permisos(93,'false') == 'true')? 'false' : 'true';
$permiso_conceptos_x_empleado         = (user_permisos(94,'false') == 'true')? 'false' : 'true';
$permiso_liquidacion_nomina           = (user_permisos(95,'false') == 'true')? 'false' : 'true';
$permiso_liquidacion_nomina_guardar   = (user_permisos(96,'false') == 'true')? 'false' : 'true';

$permiso_liquidacion_empleado         = (user_permisos(158,'false') == 'true')? 'false' : 'true';
$permiso_liquidacion_empleado_guardar = (user_permisos(159,'false') == 'true')? 'false' : 'true';

$permiso_planilla_ajuste         = (user_permisos(163,'false') == 'true')? 'false' : 'true';
$permiso_planilla_ajuste_guardar = (user_permisos(164,'false') == 'true')? 'false' : 'true';

$permiso_liquidacion_provision        = (user_permisos(100,'false') == 'true')? 'false' : 'true';
$permiso_informe                      = (user_permisos(168,'false') == 'true')? 'false' : 'true';

$fechaf = date("Y-m-d");
$fechai = date('Y-m-d', strtotime('-40 day')) ;

 ?>
<script>
//////////////////////////////////   VARIABLES PARA LOS INFORMES  /////////////////////////////////////////
var my_fecha_desde      = '<?php $fecha = date("Y-m-d"); echo date("Y-m-d", strtotime("$fecha -5 day")); ?>'
,	my_fecha_hasta      = '<?php $fecha = date("Y-m-d"); echo $fecha; ?>'
// ,	Tam                 = parent.TamVentana()
// ,	myancho             = Tam[0]
// ,	myalto              = Tam[1];
apuntador_este_gridraro = 2;

// Ext.QuickTips.init();
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

var id_activo
,	filtro_empresa
,	filtro_sucursal
,	filtro_ubicacion
, 	id_inventario_proceso
, 	empresa_inventario_proceso
,	sucursal_crear_inventario_proceso
,	ubicacion_crear_inventario_proceso;

// ARRAYS PARA EL INFORME DE NOMINA
arrayEmpleados = new Array();
arrayEmpleadosNomina = new Array();

arrayConceptos = new Array();
arrayConceptosNomina = new Array();

arrayterceros        = new Array();
tercerosConfigurados = new Array();

// ARRAYS PARA EL INFORME DE LIQUIDACION
arrayEmpleadosLiquidacion = new Array();
arrayEmpleadosConfiguradosLiquidacion = new Array();

arrayConceptosLiquidacion = new Array();
arrayConceptosConfiguradosLiquidacion = new Array();

arraytercerosLiquidacion        = new Array();
tercerosConfiguradosLiquidacion = new Array();

// ARRAYS PARA EL INFORME DE AJUSTE
arrayEmpleadosPlanillaAjuste = new Array();
arrayEmpleadosConfiguradosPlanillaAjuste = new Array();

arrayConceptosPlanillaAjuste = new Array();
arrayConceptosConfiguradosPlanillaAjuste = new Array();

arraytercerosPlanillaAjuste        = new Array();
tercerosConfiguradosPlanillaAjuste = new Array();

arrayEmpleadosVacaciones             = new Array();
arrayEmpleadosConfiguradosVacaciones = new Array();

// INFORME CONSOLIDADO DE NOMINA
arrayEmpleadosConsolidado = new Array();
arrayEmpleadosConsolidadoNomina = new Array();


Ext.onReady
(function()
	{


		new Ext.Viewport //TAB PRINCIPAL
		(
			{
			layout		: 'border',
			style 		: 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
			items:
				[
					// {
					// 	region		: 'north',
					// 	xtype		: 'panel',
					// 	height		: 33,
					// 	border		: false,
					// 	margins		: '0 0 0 0',
					// 	html		: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
					// 	bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
					// },
					{
						region			: 'center',
						xtype			: 'tabpanel',
						margins			: '0 0 0 0',
						deferredRender	: true,
						border			: false,
						activeTab		: 0,
						bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						enableTabScroll : true,
						items			:
						[
							//======================== DASHBOARD==========================//
							{
								closable	: false,
								autoScroll	: true,
								title		: 'Dashboard',
								iconCls 	: '',
								// disabled    : <?php echo $permiso_cotizacion; ?>,
								bodyStyle 	: 'background-color:#FFF;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_DashboardNomina',
										border		: false,
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
									}
								],
								tbar		:
								[
									{
										xtype	: 'buttongroup',
										columns	: 3,
										title	: 'Filtro Sucursal',
										items	:
										[
											{
												xtype		: 'panel',
												border		: false,
												width		: 170,
												height		: 56,
												bodyStyle 	: 'background-color:rgba(255,255,255,0)',
												autoLoad    :
												{
													url		: '../funciones_globales/filtros/filtro_unico_sucursal_consulta_documentos.php',
													scripts	: true,
													nocache	: true,
													params	:
													{
														opc              : "DashboardNomina",
														imprimeVarPhp    : 'opcGrillaContable : "DashboardNomina",fechai: "<?php echo $fechai; ?>",fechaf: "<?php echo $fechaf; ?>",',
														imprimeScriptPhp : 'if ( $("#fechai").length ){ $("#fechai").val("<?php echo $fechai; ?>"); } if ( $("#fechai").length ){ $("#fechaf").val("<?php echo $fechaf; ?>"); }',
														renderizaBody    : 'true',
														url_render       : 'dashboard/dashboard.php',
														contenedor       : 'contenedor_DashboardNomina',
														// url_filtro_bodega : '../funciones_globales/filtros/filtro_bodega_todos.php',

													}
												}
											}
										]
									},
									{
										xtype	: 'buttongroup',
										columns	: 3,
										title	: 'Filtro Fechas',
										items	:
										[
											{
												xtype		: 'panel',
												border		: false,
												width		: 190,
												height		: 56,
												bodyStyle 	: 'background-color:rgba(255,255,255,0)',
												autoLoad    :
												{
													url		: 'bd/bd.php',
													scripts	: true,
													nocache	: true,
													params	:
													{
														opc           : "filtro_fecha_dashboard",
														// imprimeVarPhp : 'opcGrillaContable : "DashboardNomina"',
														// renderizaBody : 'true',
														// url_render    : 'dashboard/dashboard.php',
														// contenedor : 'contenedor_DashboardNomina',
														// url_filtro_bodega : '../funciones_globales/filtros/filtro_bodega_todos.php',

													}
												}
											}
										]
									},'->',
								    // {
								    //     xtype       : 'button',
								    //     width       : 65,
								    //     height      : 65,
								    //     //text        : 'Regresar',
								    //     scale       : 'large',
								    //     iconCls     : 'regresar',
								    //     iconAlign   : 'top',
								    //     hidden      : false,
								    //     handler     : function(){ cambiarPeriodoAtras();}
								    // }
								    // ,'-',
								    {
								        xtype       : "tbtext",
								        text        : '<div class="divIndicador" id="divIndicador" onClick="cambiarPeriodoAdelante()">'
								        					+'<div id="id_periodo" style="font-size:30px; text-align:center;"></div>'
								        					+'<div id="id_rango" style="font-size:11px; text-align:center;"></div>'
								        				+'</div>',
					                    scale       : "large",
								    }
								    // ,'-',
								    // {
								    //     xtype       : 'button',
								    //     width       : 65,
								    //     height      : 65,
								    //     //text        : 'Regresar',
								    //     scale       : 'large',
								    //     iconCls     : 'regresar2',
								    //     iconAlign   : 'top',
								    //     hidden      : false,
								    //     handler     : function(){ cambiarPeriodoAdelante();}
								    // }
								 ]
							},
							{
								closable   : false,
								autoScroll : false,
								title      : 'Documentos',
								id         : 'panel_documentos_nomina',
								xtype      : 'panel',
								iconCls    : 'folder16',
								bodyStyle  : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',

								tbar		:
											[

												{
													xtype		: 'panel',
													title		: '',
													bodyStyle   : 'border:none;background-color:#DFE8F6;',
													items		:
																[
																	{
																		xtype       : "tbtext",
																		text        : "<div style=\"text-align:center; margin: 0 5px 0 5px; padding: 0 15px 0 0;  font-weight:normal;\"><span style=\"font-size:12px; font-weight:bold;display:none;\">Ubicacion Actual</span><br /><span style=\"text-shadow: 1px 1px 1px #333; color:#333; font-size:24px; font-weight:bold;\">DOCUMENTOS DE NOMINA</span></div>",
																		scale       : "large",
																		minWidth	: 200,
																	},

																]
												},
											],
									items:
											[
												{
													xtype   : 'tabpanel',
													border 	: false,
													//style	: 'margin:5px 0 0 0;',
													items   :
													[
														// PLANILLA DE NOMINA
														{
															closable	: false,
															id 			: "PanelNomina",
															autoScroll	: false,
															border		: false,
															title		: 'Planilla de Nomina',
															bodyStyle 	: 'background-color:<?php echo $_SESSION["COLOR_CONTRASTE"] ?>;',
															iconCls 	: 'docedit16',
															disabled    : <?php echo $permiso_liquidacion_nomina; ?>,
															items		:
																		[
																			{
																				xtype		: "panel",
																				id			: 'contenedor_nomina',
																				border		: false,
																				bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'

																			}
																		],
															autoLoad	:
																		{
																			url		: 'planilla/planilla.php',
																			scripts	: true,
																			nocache	: true,
																			params	:
																				{
																					params	: { opc	: 'planilla' }
																				}
																		}
														},
														// PLANILLA DE LIQUIDACION
														{
															closable	: false,
															id 			: "PanelLiquidacion",
															autoScroll	: false,
															border		: false,
															title		: 'Planilla de Liquidacion',
															bodyStyle 	: 'background-color:<?php echo $_SESSION["COLOR_CONTRASTE"] ?>;',
															iconCls 	: 'ventas16',
															disabled    : <?php echo $permiso_liquidacion_empleado; ?>,
															items		:
																		[
																			{
																				xtype		: "panel",
																				id			: 'contenedor_liquidacion',
																				border		: false,
																				bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'

																			}
																		],
															autoLoad	:
																		{
																			url		: 'liquidacion/planilla.php',
																			scripts	: true,
																			nocache	: true,
																			params	:
																				{
																					params	: { opc	: 'liquidacion' }
																				}
																		}
														},
														// NOMINA ELECTRONICA
														{
															closable	: false,
															id 			: "NominaElectronica",
															autoScroll	: false,
															border		: false,
															title		: 'Nomina Electronica',
															bodyStyle 	: 'background-color:<?php echo $_SESSION["COLOR_CONTRASTE"] ?>;',
															iconCls 	: 'terceros16',
															// disabled    : <?php echo $permiso_liquidacion_empleado; ?>,
															items		:
																		[
																			{
																				xtype		: "panel",
																				id			: 'contenedor_nomina_electronica',
																				border		: false,
																				bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'

																			}
																		],
															autoLoad	:
																		{
																			url		: 'nomina_electronica/planilla.php',
																			scripts	: true,
																			nocache	: true,
																			params	:
																				{
																					params	: { opc	: 'nomina_electronica' }
																				}
																		}
														},
														// PLANILLA DE  AJUSTE
														{
															closable	: false,
															id 			: "PanelAjuste",
															autoScroll	: false,
															border		: false,
															title		: 'Ajustes-Consolidacion',
															bodyStyle 	: 'background-color:<?php echo $_SESSION["COLOR_CONTRASTE"] ?>;',
															iconCls 	: 'table_gear',
															disabled    : <?php echo $permiso_planilla_ajuste; ?>,
															items		:
																		[
																			{
																				xtype		: "panel",
																				id			: 'contenedor_ajuste_nomina',
																				border		: false,
																				bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'

																			}
																		],
															autoLoad	:
																		{
																			url		: 'ajuste_nomina/planilla.php',
																			scripts	: true,
																			nocache	: true,
																			params	:
																				{
																					params	: { opc	: 'ajuste_nomina' }
																				}
																		}
														},
														// CONSOLIDACION ANUAL DE PROVISIONES
														{
															closable	: false,
															id 			: "PanelConcolidacion",
															autoScroll	: false,
															border		: false,
															title		: 'Consolidacion Provisiones',
															bodyStyle 	: 'background-color:<?php echo $_SESSION["COLOR_CONTRASTE"] ?>;',
															iconCls 	: 'libro',
															disabled    : <?php echo $permiso_planilla_ajuste; ?>,
															// disabled    : 'true',
															items		:
																		[
																			{
																				xtype		: "panel",
																				id			: 'contenedor_consolidacion_provision',
																				border		: false,
																				bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'

																			}
																		],
															autoLoad	:
																		{
																			url		: 'consolidacion_provision/planilla.php',
																			scripts	: true,
																			nocache	: true,
																			params	:
																				{
																					params	: { opc	: 'consolidacion_provision' }
																				}
																		}
														},

													]
												}
											]
							},
							// --> INSERTE AQUI NUEVA PESTAÑA
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Conceptos por Cargo',
								iconCls 	: 'page_gear',
								disabled    : <?php echo $permiso_conceptos_x_cargo; ?>,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_tributario',
										border		: false,
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'
									}
								],
								autoLoad :
								{
									url		: 'tributario/panel_principal.php',
									scripts	: true,
									nocache	: true,
									params	: { opc	: '' }
								}
							},
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Conceptos por Empleados',
								iconCls 	: 'contactos16',
								disabled    : <?php echo $permiso_conceptos_x_empleado; ?>,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_tributario',
										border		: false,
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'

									}
								],
								autoLoad :
								{
									url		: 'conceptos_empleados/panel_principal.php',
									scripts	: true,
									nocache	: true,
									params	: { opc	: '' }
								}
							},
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Informes',
								iconCls 	: 'doc16',
								disabled    : <?php echo $permiso_informe; ?>,
								bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png)',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_informes_nomina',
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_CONTRASTE'] ?>;',
										border		: false,
									}
								],
								tbar		:
									[
										{
											xtype	: 'buttongroup',
											columns	: 10,
											title	: 'Seleccione el Informe',
											items	:
											[
												{
													text		: 'Nomina',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('nomina.php');}
												},
												{
													text		: 'Nomina <br>(Consolidado)',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('nomina_consolidado.php');}
												},
												{
													text		: 'Liquidacion',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('liquidacion.php');}
												},{
													text		: 'Planilla de ajuste',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('planilla_ajuste.php');}
												},{
													text		: 'Vacaciones',
													scale		: 'small',
													iconCls		: 'doc',
													iconAlign	: 'top',
													handler		: function(){informe('vacaciones.php');}
												},
											]
										},
										// {
										// 	xtype	: 'buttongroup',
										// 	columns	: 4,
										// 	title	: 'P.I.L.A',
										// 	items	:
										// 	[
										// 		{
										// 			text		: 'Exportar Archivo Plano',
										// 			scale		: 'small',
										// 			iconCls		: 'doc',
										// 			iconAlign	: 'top',
										// 			handler		: function(){wizard_plain_file();}
										// 		}
										// 	]
										// }
									]
							}
						]
					}
				]
			}
		);
	}
);

	function informe(cual){

		Ext.getCmp('contenedor_informes_nomina').load(
			{
				url 	:	'../informes/informes/nomina/'+cual,
				scripts	:	true,
				nocache	:	true,
				params	:	{modulo:'nomina'}
			}
		);

	}

	function resizeHeadMyGrilla(divScroll, idHead){

		if (!divScroll.querySelector(".bodyDivNominaPlanilla") || !document.getElementById(idHead)) {
			return;
		}

		var idDivScroll  = divScroll.id
		,	widthBody    = (divScroll.querySelector(".bodyDivNominaPlanilla").offsetWidth)*1
		,	divHead      = document.getElementById(idHead)
		,	widthHead    = (divHead.offsetWidth)*1;

		var saldo = widthBody - widthHead;
		var styleHead = divHead.getAttribute('style');

		if(isNaN(widthBody) || widthBody == 0 || saldo >= -1 && saldo <=1){  return; }
		else if(widthBody > widthHead){ divHead.setAttribute('style','width: calc(100% - 1px);border-bottom:none;'); }
		else if(widthBody < widthHead){ divHead.setAttribute('style','width: calc(100% - 18px);border-bottom:none;'); }
	}

	function nuevaLiquidacionProvision (){

		Ext.get("contenedor_LiquidacionProvision").load({
			url     : 'liquidacion_provision/grilla/grillaContable.php',
			scripts : true,
			nocache : true,
			params  : {  opcGrillaContable : 'LiquidacionProvision',filtro_sucursal:document.getElementById('filtro_sucursal_liquidacion_provision').value, }
		});

		document.getElementById('titleDocumentoLiquidacionProvision').innerHTML=''
	}

	function cargando_documentos(texto,opc) {

		var contenido='<div id="experiment">'+
				            '<div id="cube">'+
				                    '<div class="face one">'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                    '</div>'+
				                    '<div class="face two">'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                    '</div>'+
				                    '<div class="face three">'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el2"></div>'+
				                    '</div>'+
				                    '<div class="face four">'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el2"></div>  '+
				                    '</div>'+
				                    '<div class="face five">'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el3"></div> '+
				                        '<div id="cuadro" class="el1"></div>'+
				                    '</div>'+
				                    '<div class="face six">'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                    '</div>'+

				                    '<div class="face seven">'+
				                    '</div>'+
				            '</div>'+
				            '<div id="LabelCargando">'+texto+'</div>'+
				    '</div>';
		parentModal = document.createElement("div");
		parentModal.innerHTML = '<div id="modal">'+contenido+'</div>';
		parentModal.setAttribute("id", "divPadreModal");
		document.body.appendChild(parentModal);
		document.getElementById("divPadreModal").className = "fondo_modal";

		document.getElementById('experiment').style.top="calc(50% - 100px)";
		document.getElementById('experiment').style.left="calc(50% - 100px)";

	}

	habilitar_empleados_en_vacaciones();
	function habilitar_empleados_en_vacaciones(){
		Ext.Ajax.request({
		    url     : 'bd/bd.php',
		    params  :
		    {
				opc : 'habilitar_empleados_en_vacaciones',
		    },
		    success :function (result, request){
		    		// console.log(result.responseText);
		                if(result.responseText != 'true'){ console.log("false"); }
		                else{ console.log("true"); }
		            },
		    failure : function(){ console.log("fail"); }
		});
	}

	// VENTANA DE CONFIGURACION DEL ARCHIVO PLANO
	function wizard_plain_file() {
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_wizard = new Ext.Window({
		   	width       : 550,
			height      : 210,
		    id          : 'Win_Ventana_wizard',
		    title       : 'Asistente',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'exportar_archivo_plano/wizard.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            var1 : 'var1',
		            var2 : 'var2',
		        }
		    }
		}).show();
	}

</script>