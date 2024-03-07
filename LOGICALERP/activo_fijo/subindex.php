<?php
	include("../configuracion/conectar.php");
	$permiso_administracion_activos = (user_permisos(106,'false') == 'true')? 'false' : 'true';
	$permiso_depreciaciones         = (user_permisos(107,'false') == 'true')? 'false' : 'true';
	$permiso_deterioro              = (user_permisos(204,'false') == 'true')? 'false' : 'true';
	$permiso_traslado               = (user_permisos(226,'false') == 'true')? 'false' : 'true';
	$permiso_baja                   = (user_permisos(231,'false') == 'true')? 'false' : 'true';
	$permiso_crear_activo						= (user_permisos(236,'false') == 'true')? 'false' : 'true';
	$permiso_editar_activo					= (user_permisos(237,'false') == 'true')? 'false' : 'true';
?>
<script>
	//======================= VARIABLES PARA LOS INFORMES ======================//
	var my_fecha_desde   		 = '<?php $fecha = date("Y-m-d"); echo date("Y-m-d", strtotime("$fecha -5 day")); ?>'
	,	my_fecha_hasta     		 = '<?php $fecha = date("Y-m-d"); echo $fecha; ?>'
	,	Tam                		 = parent.TamVentana()
	,	myancho            		 = Tam[0]
	,	myalto             		 = Tam[1];
	apuntador_este_gridraro  = 2;

	Ext.QuickTips.init();

	var id_activo
	,	filtro_empresa
	,	filtro_sucursal
	,	filtro_ubicacion
	, id_inventario_proceso
	, empresa_inventario_proceso
	,	sucursal_crear_inventario_proceso
	,	ubicacion_crear_inventario_proceso;

	url_render_colgaap        = 'depreciaciones/grilla/grillaContable.php';
	url_render_niif           = 'depreciaciones_niif/grilla/grillaContable.php';
	opcGrillaContable_colgaap = 'Depreciaciones';
	opcGrillaContable_niif    = 'DepreciacionesNiif';

	var load_select = 0;

	Ext.onReady
	(function()
		{
			new Ext.Viewport //TAB PRINCIPAL
			(
				{
					layout	: 'border',
					style 	: 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
					items		:	[
											{
												region			: 'north',
												xtype				: 'panel',
												height			: 33,
												border			: false,
												margins			: '0 0 0 0',
												html				: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
												bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
											},
											{
												region					: 'center',
												xtype						: 'tabpanel',
												margins					: '0 0 0 0',
												deferredRender	: true,
												border					: false,
												activeTab				: 0,
												bodyStyle 			: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
												items						:	[
																						//===== ADMINISTRACION DE ACTIVOS FIJOS =====//
																						{
																							closable		: false,
																							autoScroll	: false,
																							title				: 'Activos Fijos',
																							iconCls 		: 'inventario16',
																							disabled    : <?php echo $permiso_administracion_activos; ?>,
																							bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
																							items				:	[
																															{
																																xtype			: "panel",
																																id				: 'contenedor_inventario',
																																border		: false,
																																bodyStyle : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'

																															}
																														],
																							tbar				:	[
																															{
																																xtype		: 'buttongroup',
																																columns	: 6,
																																items		:	[
																																						{
																																							xtype		: 'buttongroup',
																																							columns	: 3,
																																							title		: 'Filtros',
																																							items		: [
																																													{
																																														xtype				: 'panel',
																																														border			: false,
																																														width				: 260,
																																														height			: 55,
																																														bodyStyle 	: 'background-color:rgba(255,255,255,0)',
																																														autoLoad    :	{
																																																						url			: 'filtros/filtros_sucursal.php',
																																																						scripts	: true,
																																																						nocache	: true,
																																																						params	: { opc	: 'inventario' }
																																																					}
																																													},
																																												]
																																						},
																																						{
																																							xtype		: 'buttongroup',
																																							columns	: 3,
																																							title		: 'Herramientas',
																																							items		: [
																																													{
																																														text			: '<br>Nuevo Activo Fijo',
																																														scale			: 'small',
																																														iconCls		: 'addequipo',
																																														iconAlign	: 'top',
																																														disabled  : <?php echo $permiso_crear_activo; ?>,
																																														handler		: function(){Agregar_ActivosFijos();}
																																													},
																																												]
																																						},
																																						{
																																							xtype		: 'buttongroup',
																																							columns	: 3,
																																							title		: 'Actas',
																																							items		: [
																																													{
																																														text			: 'Impresion en Lote</br /> Codigo de Barras',
																																														scale			: 'small',
																																														iconCls		: 'barcode16',
																																														iconAlign	: 'top',
																																														handler		: function(){nueva_ventana_Grupo_codigo_barras("varios_codigos_barras");}
																																													},
																																													{
																																														text			: '<br>Acta Activos Fijos',
																																														scale			: 'small',
																																														iconCls		: 'genera',
																																														iconAlign	: 'top',
																																														handler		: function(){window.open("imprimir_acta_inventario.php?filtro_empresa=<?php echo $id_empresa; ?>"+"&opc=imprimirActaParcial")}
																																													},
																																													{
																																														text			: '<br>Acta Parcial',
																																														scale			: 'small',
																																														iconCls		: 'genera',
																																														iconAlign	: 'top',
																																														handler		: function(){ver_acta_parcial_inventario();}
																																													}
																																												]
																																						},
																																						{
																																							text			: '<br>Cargar activos fijos',
																																							scale			: 'small',
																																							iconCls		: 'upload_file32',
																																							iconAlign	: 'top',
																																							handler		: function(){VentanaSubirExcel();}
																																						},
																																					]
																															}
																														]
																						},
																						{
																							closable		: false,
																							autoScroll	: false,
																							title				: 'Documentos',
																							iconCls 		: 'folder16',
																							bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
																							tbar				: [
																															{
																																xtype			: 'panel',
																																title			: '',
																																bodyStyle : 'border:none;background-color:#DFE8F6;',
																																items			:	[
																																							{
																																								xtype       : "tbtext",
																																								text        : "<div style=\"text-align:center; margin: 0 5px 0 5px; padding: 0 15px 0 0;  font-weight:normal;\"><span style=\"font-size:12px; font-weight:bold;display:none;\">Ubicacion Actual</span><br /><span style=\"text-shadow: 1px 1px 1px #333; color:#333; font-size:24px; font-weight:bold;\">DOCUMENTOS DEL MODULO</span></div>",
																																								scale       : "large",
																																								minWidth	: 200,
																																							},
																																						]
																															},
																														],
																							items				:	[
																															{
																																xtype     : "tabpanel",
																																border    : false,
																																bodyStyle : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
																																items     : [
																																							//DEPRECIACIONES
																																							{
																																								closable		: false,
																																								autoScroll	: true,
																																								title				: 'Depreciaciones',
																																								iconCls 		: '',
																																								disabled    : <?php echo $permiso_depreciaciones; ?>,
																																								bodyStyle 	: 'background-color:#FFF;height:100%;',
																																								items				:	[
																																																{
																																																	xtype			: "panel",
																																																	id				: 'contenedor_Depreciaciones',
																																																	border		: false,
																																																	bodyStyle : 'background-color:#FFF;',
																																																}
																																															],
																																								tbar				:	[
																																																{
																																			                            xtype   : 'buttongroup',
																																			                            columns : 3,
																																			                            title   : 'Filtro Contabilidad',
																																			                            items   : [
																																							                                {
																																						                                    xtype     : 'panel',
																																						                                    border    : false,
																																						                                    width     : 160,
																																						                                    height    : 56,
																																						                                    bodyStyle : 'background-color:rgba(255,255,255,0)',
																																						                                    autoLoad  : {
																																											                                        url     : '../funciones_globales/filtros/filtro_tipo_contabilidad.php',
																																											                                        scripts : true,
																																											                                        nocache : true,
																																											                                        params  : {
																																																																					opc                       : "Depreciaciones",
																																																																					// contenedor             : 'contenedor_'+opcGrillaContable,
																																																																					imprimeScriptPhp          : 'if(load_select<1){load_select++; return;}', //NO HACE LOAD LA PRIMERA VEZ QUE ENRTRA
																																																																					renderizaBody             : 'true',
																																																																					url_render                : 'depreciaciones/grilla/grillaContable.php',
																																																																					opcGrillaContable         : "Depreciaciones",
																																																																					url_render_colgaap        : url_render_colgaap,
																																																																					url_render_niif           : url_render_niif,
																																																																					opcGrillaContable_colgaap : opcGrillaContable_colgaap,
																																																																					opcGrillaContable_niif    : opcGrillaContable_niif,
																																																																					inmprimeVarphp            : 'filtro_sucursal : document.getElementById("filtro_sucursal_Depreciaciones").value,',
																																	                                        															}
																																	                                    											}
																																	                                						}
																																	                            							]
																																                        				},
																																																{
																																																	xtype   : 'buttongroup',
																																																	columns : 3,
																																																	title   : 'Filtro Sucursal',
																																																	id      : 'panel_filtro_sucursal_depreciaciones',
																																																	items   : [
																																																							{
																																																								xtype			: 'panel',
																																																								border		: false,
																																																								width			: 210,
																																																								height		: 56,
																																																								bodyStyle : 'background-color:rgba(255,255,255,0)',
																																																								autoLoad  :	{
																																																															url			: '../funciones_globales/filtros/filtro_unico_sucursal.php',
																																																															scripts	: true,
																																																															nocache	: true,
																																																															params	: {
																																																																					opc           : "Depreciaciones",
																																																																					contenedor    : 'contenedor_Depreciaciones',
																																																																					imprimeVarPhp : 'opcGrillaContable : "Depreciaciones",'+'sinc_nota:(document.getElementById("filtro_tipo_contabilidad_Depreciaciones"))?document.getElementById("filtro_tipo_contabilidad_Depreciaciones").value : "colgaap" ,',
																																																																					renderizaBody : 'true',
																																																																					url_render    : 'depreciaciones/grilla/grillaContable.php',
																																																																				}
																																																														}
																																																							}
																																																						]
																																																},
																																																{
																																																	xtype		: 'buttongroup',
																																																	id      : 'BtnGroup_Guardar_Depreciaciones',
																																																	height  : 80,
																																						                    	style   : 'border:none;',
																																																	columns	: 1,
																																																	title		: 'Generar',
																																																	items		:	[
																																																							{
																																														                    xtype     : 'button',
																																														                    id     		: 'Btn_guardar_Depreciaciones',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Guardar',
																																														                    tooltip		: 'Genera Cotizacion',
																																														                    scale     : 'large',
																																														                    disabled 	: true,
																																														                    iconCls   : 'guardar',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); terminarGenerarDepreciaciones() }
																																														                	}
																																														            		]
																																														    },
																																																{
																																																	xtype		: 'buttongroup',
																																																	height  : 80,
																																						                    	style   : 'border:none;',
																																																	columns	: 7,
																																																	title		: 'Opciones',
																																																	items		:	[
																																																							{
																																														                    xtype     : 'button',
																																														                    width			: 60,
																																																								height		: 56,
																																																								id 				: 'btnNuevaDepreciaciones',
																																														                    text      : 'Nuevo',
																																														                    tooltip		: 'Nueva Cotizacion',
																																														                    scale     : 'large',
																																														                    iconCls   : 'add_new',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); nuevaDepreciaciones() }
																																															                },
																																															                {
																																																								xtype     : 'button',
																																																								width     : 60,
																																																								height    : 56,
																																																								id        : 'Btn_buscar_Depreciaciones',
																																																								text      : 'Buscar',
																																																								tooltip   : 'Buscar Cotizacion',
																																																								scale     : 'large',
																																																								iconCls   : 'buscar_doc_new',
																																																								iconAlign : 'top',
																																																								handler   : function(){ BloqBtn(this); buscarDepreciaciones() }
																																															                },
																																														                	{
																																														                    xtype     : 'button',
																																														                    id 				: 'Btn_cancelar_Depreciaciones',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Cancelar',
																																														                    tooltip		: 'Eliminar Cotizacion',
																																														                    scale     : 'large',
																																														                    iconCls   : 'cancel',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); cancelarDepreciaciones() }
																																														                	}
																																														            		]
																																								                },
																																								                {
																																																	xtype		: 'buttongroup',
																																																	height  : 80,
																																	                    						style   : 'border:none;',
																																							                    id      : 'BtnGroup_carga_activos_Depreciaciones',
																																																	columns	: 7,
																																																	title		: '&nbsp;',
																																																	items		:	[
																																																								{
																																															                    xtype     : 'button',
																																															                    width			: 60,
																																																									height		: 56,
																																																									id 				: 'btnCargarActivos',
																																															                    text      : 'Cargar Activos',
																																															                    tooltip		: 'Cargar Todos los activos',
																																															                    scale     : 'large',
																																															                    iconCls   : 'carga_doc',
																																															                    iconAlign : 'top',
																																															                    handler   : function(){ BloqBtn(this); cargarActivosFijosSucursal() }
																																															                },
																																															            	]
																																												        },'-',
																																							                	{
																																							                    xtype   : 'buttongroup',
																																							                    height  : 80,
																																							                    id      : 'BtnGroup_Estado1_Depreciaciones',
																																							                    columns : 4,
																																							                    title   : 'Documento Generado',
																																							                    items   : [
																																																							{
																																														                    xtype     : 'button',
																																														                    id				: 'btnExportarDepreciaciones',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Imprimir',
																																														                    tooltip		: 'Imprimir en un documento PDF',
																																														                    scale     : 'large',
																																														                    iconCls   : 'pdf32_new',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); imprimirDepreciaciones(); },
																																														                	},
																																																							{
																																														                    xtype     : 'button',
																																														                    id 				: 'Btn_editar_Depreciaciones',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Editar',
																																														                    tooltip		: 'Editar Cotizacion',
																																														                    scale     : 'large',
																																														                    iconCls   : 'edit',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); modificarDocumentoDepreciaciones(); }
																																															                },
																																															                {
																																														                    xtype     : 'button',
																																														                    id 				: 'Btn_restaurar_Depreciaciones',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Restaurar',
																																														                    tooltip		: 'Restaurar Cotizacion',
																																														                    scale     : 'large',
																																														                    iconCls   : 'restaurar32',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); restaurarDepreciaciones(); }
																																														                	}
																																																						]
																																																},
																																																{
																																			                            xtype   : 'buttongroup',
																																			                            id      : 'GroupBtnSync',
																																			                            height  : 93,
																																			                            style   : 'border:none;',
																																			                            columns : 1,
																																			                            title   : 'Contabilizar',
																																			                            items   : [
																																						                                	{
																																						                                    xtype       : 'button',
																																						                                    width       : 60,
																																						                                    height      : 56,
																																						                                    text        : 'Mover<br>Cuentas Niif',
																																						                                    tooltip     : 'Generar Nota con asientos niif automaticamente',
																																						                                    id          : 'BtnSync',
																																						                                    scale       : 'large',
																																						                                    iconCls     : 'sync',
																																						                                    iconAlign   : 'top',
																																						                                    handler     : function(){ BloqBtn(this); cambiaSyncNota('colgaap_niif') }
																																						                                	}
																																						                            		]
																																				                        },
																																				                        {
																																			                            xtype   : 'buttongroup',
																																			                            id      : 'GroupBtnNoSync',
																																			                            height  : 93,
																																			                            style   : 'border:none;',
																																			                            columns : 1,
																																			                            title   : 'Contabilizar',
																																			                            items   : [
																																						                                	{
																																						                                    xtype       : 'button',
																																						                                    width       : 60,
																																						                                    height      : 56,
																																						                                    text        : 'No Mover <br>Cuentas Niif',
																																						                                    tooltip     : 'Generar Nota solo con asientos colgaap',
																																						                                    id          : 'BtnNoSync',
																																						                                    scale       : 'large',
																																						                                    iconCls     : 'no_sync',
																																						                                    iconAlign   : 'top',
																																						                                    handler     : function(){ BloqBtn(this); cambiaSyncNota('colgaap') }
																																							                                }
																																							                            	]
																																				                        },
																																																'->',
																																								                {
																																																	xtype : "tbtext",
																																																	text  : '<div id="titleDocumentoDepreciaciones" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
																																																	scale : "large",
																																								                }
																																															]
																																							},
																																							//DETERIORO
																																							{
																																								closable		: false,
																																								autoScroll	: true,
																																								title				: 'Deterioro',
																																								iconCls 		: '',
																																								disabled    : <?php echo $permiso_deterioro; ?>,
																																								bodyStyle 	: 'background-color:#FFF;height:100%;',
																																								items				:	[
																																																{
																																																	xtype			: "panel",
																																																	id				: 'contenedor_Deterioro',
																																																	border		: false,
																																																	bodyStyle : 'background-color:#FFF;',
																																																}
																																															],
																																								tbar				:	[
																																																{
																																																	xtype   : 'buttongroup',
																																																	columns : 3,
																																																	title   : 'Filtro Sucursal',
																																																	id      : 'panel_filtro_sucursal_Deterioro',
																																																	items   :	[
																																																							{
																																																								xtype			: 'panel',
																																																								border		: false,
																																																								width			: 210,
																																																								height		: 56,
																																																								bodyStyle : 'background-color:rgba(255,255,255,0)',
																																																								autoLoad  :	{
																																																															url			: '../funciones_globales/filtros/filtro_unico_sucursal.php',
																																																															scripts	: true,
																																																															nocache	: true,
																																																															params	:	{
																																																																					opc           : "Deterioro",
																																																																					contenedor    : 'contenedor_Deterioro',
																																																																					imprimeVarPhp : 'opcGrillaContable : "Deterioro"',
																																																																					renderizaBody : 'true',
																																																																					url_render    : 'deterioro/grilla/grillaContable.php',
																																																																				}
																																																														}
																																																							}
																																																						]
																																																},
																																																{
																																																	xtype		: 'buttongroup',
																																																	id      : 'BtnGroup_Guardar_Deterioro',
																																																	height  : 80,
																																						                    	style   : 'border:none;',
																																																	columns	: 1,
																																																	title		: 'Generar',
																																																	items		: [
																																																							{
																																														                    xtype       : 'button',
																																														                    id     			: 'Btn_guardar_Deterioro',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Guardar',
																																														                    tooltip			: 'Genera Cotizacion',
																																														                    scale       : 'large',
																																														                    disabled 		: true,
																																														                    iconCls     : 'guardar',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); guardarDeterioro() }
																																														                	}
																																														            		]
																																														    },
																																																{
																																																	xtype		: 'buttongroup',
																																																	height  : 80,
																																						                    	style   : 'border:none;',
																																																	columns	: 7,
																																																	title		: 'Opciones',
																																																	items		:	[
																																																							{
																																														                    xtype       : 'button',
																																														                    width				: 60,
																																																								height			: 56,
																																																								id 					: 'btnNuevaDeterioro',
																																														                    text        : 'Nuevo',
																																														                    tooltip			: 'Nueva Cotizacion',
																																														                    scale       : 'large',
																																														                    iconCls     : 'add_new',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); nuevaDeterioro() }
																																														                	},
																																															                {
																																																								xtype     : 'button',
																																																								width     : 60,
																																																								height    : 56,
																																																								id        : 'Btn_buscar_Deterioro',
																																																								text      : 'Buscar',
																																																								tooltip   : 'Buscar Cotizacion',
																																																								scale     : 'large',
																																																								iconCls   : 'buscar_doc_new',
																																																								iconAlign : 'top',
																																																								handler   : function(){ BloqBtn(this); buscarDeterioro() }
																																															                },
																																														                	{
																																														                    xtype       : 'button',
																																														                    id 					: 'Btn_cancelar_Deterioro',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Cancelar',
																																														                    tooltip			: 'Eliminar Cotizacion',
																																														                    scale       : 'large',
																																														                    iconCls     : 'cancel',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); cancelarDeterioro() }
																																														                	}
																																														            		]
																																												        },
																																												        {
																																																	xtype		: 'buttongroup',
																																																	height  : 80,
																																	                    						style   : 'border:none;',
																																							                    id      : 'BtnGroup_carga_activos_Deterioro',
																																																	columns	: 7,
																																																	title		: '&nbsp;',
																																																	items		: [
																																																							{
																																														                    xtype       : 'button',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Cargar Activos',
																																														                    tooltip			: 'Cargar Todos los activos',
																																														                    scale       : 'large',
																																														                    iconCls     : 'carga_doc',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); cargarActivosFijosSucursal() }
																																														                	},
																																														            		]
																																												        },'-',
																																								                {
																																							                    xtype   : 'buttongroup',
																																							                    height  : 80,
																																							                    id      : 'BtnGroup_Estado1_Deterioro',
																																							                    columns : 4,
																																							                    title   : 'Documento Generado',
																																							                    items   : [
																																																							{
																																														                    xtype       : 'button',
																																														                    id					: 'btnExportarDeterioro',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Imprimir',
																																														                    tooltip			: 'Imprimir en un documento PDF',
																																														                    scale       : 'large',
																																														                    iconCls     : 'pdf32_new',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); imprimirDeterioro(); },
																																														                	},
																																																							{
																																														                    xtype       : 'button',
																																														                    id 					: 'Btn_editar_Deterioro',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Editar',
																																														                    tooltip			: 'Editar Cotizacion',
																																														                    scale       : 'large',
																																														                    iconCls     : 'edit',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); modificarDocumentoDeterioro(); }
																																															                },
																																															                {
																																														                    xtype       : 'button',
																																														                    id 					: 'Btn_restaurar_Deterioro',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Restaurar',
																																														                    tooltip			: 'Restaurar Cotizacion',
																																														                    scale       : 'large',
																																														                    iconCls     : 'restaurar32',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); restaurarDeterioro(); }
																																															                }
																																																						]
																																																},
																																																'->',
																																                                {
																																																	xtype : "tbtext",
																																																	text  : '<div id="titleDocumentoDeterioro" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
																																																	scale : "large",
																																                                }
																																															]
																																							},
																																							//TRASLADOS
																																							{
																																								closable		: false,
																																								autoScroll	: true,
																																								title				: 'Traslados',
																																								iconCls 		: '',
																																								disabled    : <?php echo $permiso_traslado; ?>,
																																								bodyStyle 	: 'background-color:#FFF;height:100%;',
																																								items				:	[
																																																{
																																																	xtype			: "panel",
																																																	id				: 'contenedor_Traslados',
																																																	border		: false,
																																																	bodyStyle : 'background-color:#FFF;',
																																																}
																																															],
																																								tbar				:	[
																																																{
																																																	xtype   : 'buttongroup',
																																																	columns : 3,
																																																	title   : 'Ubicacion De Origen',
																																																	id      : 'panel_filtro_sucursal_Traslados',
																																																	items   : [
																																																							{
																																																								xtype			: 'panel',
																																																								border		: false,
																																																								width			: 260,
																																																								height		: 55,
																																																								bodyStyle : 'background-color:rgba(255,255,255,0)',
																																																								autoLoad  :	{
																																																															url			: 'filtros/filtros_sucursal.php',
																																																															scripts	: true,
																																																															nocache	: true,
																																																															params	:	{
																																																																					opc           : 'Traslados',
																																																																					imprimeVarPhp : 'opcGrillaContable:"Traslados",',
																																																																					url_render    : 'traslados/grilla/grillaContable'
																																																																				}
																																																														}
																																																							}
																																																						]
																																																},
																																																{
																																																	xtype		: 'buttongroup',
																																																	id      : 'BtnGroup_Guardar_Traslados',
																																																	height  : 80,
																																						                    	style   : 'border:none;',
																																																	columns	: 1,
																																																	title		: 'Generar',
																																																	items		:	[
																																																							{
																																														                    xtype     : 'button',
																																														                    id     		: 'Btn_guardar_Traslados',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Guardar',
																																														                    tooltip		: 'Genera Traslado',
																																														                    scale     : 'large',
																																														                    disabled 	: true,
																																														                    iconCls   : 'guardar',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); validarTraslados() }
																																														                	}
																																																			      ]
																																														    },
																																																{
																																																	xtype		: 'buttongroup',
																																																	height  : 80,
																																						                    	style   : 'border:none;',
																																																	columns	: 7,
																																																	title		: 'Opciones',
																																																	items		:	[
																																																							{
																																														                    xtype     : 'button',
																																														                    width			: 60,
																																																								height		: 56,
																																																								id 				: 'btnNuevaTraslados',
																																														                    text      : 'Nuevo',
																																														                    tooltip		: 'Nuevo Traslado',
																																														                    scale     : 'large',
																																														                    iconCls   : 'add_new',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); nuevaTraslados() }
																																														                	},
																																															                {
																																																								xtype     : 'button',
																																																								width     : 60,
																																																								height    : 56,
																																																								id        : 'Btn_buscar_Traslados',
																																																								text      : 'Buscar',
																																																								tooltip   : 'Buscar Traslado',
																																																								scale     : 'large',
																																																								iconCls   : 'buscar_doc_new',
																																																								iconAlign : 'top',
																																																								handler   : function(){ BloqBtn(this); buscarTraslados() }
																																															                },
																																															                {
																																														                    xtype     : 'button',
																																														                    id 				: 'Btn_cancelar_Traslados',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Cancelar',
																																														                    tooltip		: 'Eliminar Traslado',
																																														                    scale     : 'large',
																																														                    iconCls   : 'cancel',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); cancelarTraslados() }
																																															                }
																																														            		]
																																												        },
																																																'-',
																																								                {
																																							                    xtype   : 'buttongroup',
																																							                    height  : 80,
																																							                    id      : 'BtnGroup_Estado1_Traslados',
																																							                    columns : 4,
																																							                    title   : 'Documento Generado',
																																							                    items   : [
																																																							{
																																														                    xtype     	: 'button',
																																														                    id					: 'btnExportarTraslados',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text      	: 'Imprimir',
																																														                    tooltip			: 'Imprimir en un documento PDF',
																																														                    scale      	: 'large',
																																														                    iconCls     : 'pdf32_new',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); imprimirTraslados(); },
																																															                },
																																																							{
																																														                    xtype       : 'button',
																																														                    id 					: 'Btn_editar_Traslados',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Editar',
																																														                    tooltip			: 'Editar Traslado',
																																														                    scale       : 'large',
																																														                    iconCls     : 'edit',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); modificarDocumentoTraslados(); }
																																															                },
																																															                {
																																														                    xtype       : 'button',
																																														                    id 					: 'Btn_restaurar_Traslados',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Restaurar',
																																														                    tooltip			: 'Restaurar Traslado',
																																														                    scale       : 'large',
																																														                    iconCls     : 'restaurar32',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); restaurarTraslados(); }
																																															                }
																																																						]
																																																},
																																																'->',
																																                                {
																																																	xtype : "tbtext",
																																																	text  : '<div id="titleDocumentoTraslados" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
																																																	scale : "large",
																																                                }
																																															]
																																							},
																																							//DAR DE BAJA
																																							{
																																								closable		: false,
																																								autoScroll	: true,
																																								title				: 'Dar De Baja',
																																								iconCls 		: '',
																																								disabled    : <?php echo $permiso_baja; ?>,
																																								bodyStyle 	: 'background-color:#FFF;height:100%;',
																																								items				:	[
																																																{
																																																	xtype			: "panel",
																																																	id				: 'contenedor_Baja',
																																																	border		: false,
																																																	bodyStyle : 'background-color:#FFF;',
																																																}
																																															],
																																								tbar				:	[
																																																{
																																																	xtype   : 'buttongroup',
																																																	columns : 3,
																																																	title   : 'Ubicacion Del Activo',
																																																	id      : 'panel_filtro_sucursal_Baja',
																																																	items   : [
																																																							{
																																																								xtype			: 'panel',
																																																								border		: false,
																																																								width			: 260,
																																																								height		: 55,
																																																								bodyStyle : 'background-color:rgba(255,255,255,0)',
																																																								autoLoad  :	{
																																																															url			: 'filtros/filtros_sucursal.php',
																																																															scripts	: true,
																																																															nocache	: true,
																																																															params	:	{
																																																																					opc           : 'Baja',
																																																																					imprimeVarPhp : 'opcGrillaContable:"Baja",',
																																																																					url_render    : 'baja/grilla/grillaContable'
																																																																				}
																																																														}
																																																							}
																																																						]
																																																},
																																																{
																																																	xtype		: 'buttongroup',
																																																	id      : 'BtnGroup_Guardar_Baja',
																																																	height  : 80,
																																						                    	style   : 'border:none;',
																																																	columns	: 1,
																																																	title		: 'Generar',
																																																	items		:	[
																																																							{
																																														                    xtype     : 'button',
																																														                    id     		: 'Btn_guardar_Baja',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Guardar',
																																														                    tooltip		: 'Genera Baja',
																																														                    scale     : 'large',
																																														                    disabled 	: true,
																																														                    iconCls   : 'guardar',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); validarBaja() }
																																														                	}
																																																			      ]
																																														    },
																																																{
																																																	xtype		: 'buttongroup',
																																																	height  : 80,
																																						                    	style   : 'border:none;',
																																																	columns	: 7,
																																																	title		: 'Opciones',
																																																	items		:	[
																																																							{
																																														                    xtype     : 'button',
																																														                    width			: 60,
																																																								height		: 56,
																																																								id 				: 'btnNuevaBaja',
																																														                    text      : 'Nuevo',
																																														                    tooltip		: 'Nuevo Baja',
																																														                    scale     : 'large',
																																														                    iconCls   : 'add_new',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); nuevaBaja() }
																																														                	},
																																															                {
																																																								xtype     : 'button',
																																																								width     : 60,
																																																								height    : 56,
																																																								id        : 'Btn_buscar_Baja',
																																																								text      : 'Buscar',
																																																								tooltip   : 'Buscar Baja',
																																																								scale     : 'large',
																																																								iconCls   : 'buscar_doc_new',
																																																								iconAlign : 'top',
																																																								handler   : function(){ BloqBtn(this); buscarBaja() }
																																															                },
																																															                {
																																														                    xtype     : 'button',
																																														                    id 				: 'Btn_cancelar_Baja',
																																														                    width			: 60,
																																																								height		: 56,
																																														                    text      : 'Cancelar',
																																														                    tooltip		: 'Eliminar Baja',
																																														                    scale     : 'large',
																																														                    iconCls   : 'cancel',
																																														                    iconAlign : 'top',
																																														                    handler   : function(){ BloqBtn(this); cancelarBaja() }
																																															                }
																																														            		]
																																												        },
																																																'-',
																																								                {
																																							                    xtype   : 'buttongroup',
																																							                    height  : 80,
																																							                    id      : 'BtnGroup_Estado1_Baja',
																																							                    columns : 4,
																																							                    title   : 'Documento Generado',
																																							                    items   : [
																																																							{
																																														                    xtype     	: 'button',
																																														                    id					: 'btnExportarBaja',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text      	: 'Imprimir',
																																														                    tooltip			: 'Imprimir en un documento PDF',
																																														                    scale      	: 'large',
																																														                    iconCls     : 'pdf32_new',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); imprimirBaja(); },
																																															                },
																																																							{
																																														                    xtype       : 'button',
																																														                    id 					: 'Btn_editar_Baja',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Editar',
																																														                    tooltip			: 'Editar Baja',
																																														                    scale       : 'large',
																																														                    iconCls     : 'edit',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); modificarDocumentoBaja(); }
																																															                },
																																															                {
																																														                    xtype       : 'button',
																																														                    id 					: 'Btn_restaurar_Baja',
																																														                    width				: 60,
																																																								height			: 56,
																																														                    text        : 'Restaurar',
																																														                    tooltip			: 'Restaurar Baja',
																																														                    scale       : 'large',
																																														                    iconCls     : 'restaurar32',
																																														                    iconAlign   : 'top',
																																														                    handler     : function(){ BloqBtn(this); restaurarBaja(); }
																																															                }
																																																						]
																																																},
																																																'->',
																																                                {
																																																	xtype : "tbtext",
																																																	text  : '<div id="titleDocumentoBaja" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
																																																	scale : "large",
																																                                }
																																															]
																																							}
																																						]
																															},
																						                 	{
																																xtype : "tbtext",
																																text  : '<div id="titleDocumentoDepreciaciones" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
																																scale : "large",
																						                  }
																														],
																						}
																						//INSERTE UNA NUEVA PESTAÑA
																					]
											}
										]
				}
			);
		}
	);

	function resizeHeadMyGrilla(divScroll, idHead){
		if (!divScroll.querySelector(".body"+divScroll.id)) {
			return;
		}
		var idDivScroll  = divScroll.id
		,	widthBody    = (divScroll.querySelector(".body"+idDivScroll).offsetWidth)*1
		,	divHead      = document.getElementById(idHead)
		,	widthHead    = (divHead.offsetWidth)*1;

		if((widthBody + 1) == widthHead){
			divHead.setAttribute('style','width: calc(100% - 18px);');
		}
		else if(isNaN(widthBody) || widthBody == 0 || widthBody == widthHead){
			return;
		}
		else if(widthBody > widthHead){
			divHead.setAttribute('style','width: calc(100% - 35px);');
		}
		else if(widthBody < widthHead){
			divHead.setAttribute('style','width: calc(100% - 35px);');
		}
	}

	function nuevaDepreciaciones() {
		var contabilidad = document.getElementById('filtro_tipo_contabilidad_Depreciaciones').value;
		var url='';
		var opcGrillaContable='';
		if (contabilidad=='colgaap') {
			url='depreciaciones/grilla/grillaContable.php';
			opcGrillaContable='Depreciaciones';
		}
		else{
			url='depreciaciones_niif/grilla/grillaContable.php';
			opcGrillaContable='DepreciacionesNiif';
		}

		Ext.get('contenedor_Depreciaciones').load({
			url     : url,
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_sucursal   : document.getElementById('filtro_sucursal_Depreciaciones').value,
				opcGrillaContable : 'Depreciaciones',
			}
		});
	}

	function nuevaDeterioro() {
		Ext.get('contenedor_Deterioro').load({
			url     : 'deterioro/grilla/grillaContable.php',
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_sucursal   : document.getElementById('filtro_sucursal_Deterioro').value,
				opcGrillaContable : 'Deterioro',
			}
		});
	}

	function nuevaTraslados() {
		Ext.get('contenedor_Traslados').load({
			url     : 'traslados/grilla/grillaContable.php',
			scripts : true,
			nocache : true,
			params  :	{
									filtro_sucursal   : document.getElementById('filtro_sucursal_Traslados').value,
									filtro_ubicacion  : document.getElementById('filtro_ubicacion_Traslados').value,
									opcGrillaContable : 'Traslados',
								}
		});
	}

	function nuevaBaja() {
		Ext.get('contenedor_Baja').load({
			url     : 'baja/grilla/grillaContable.php',
			scripts : true,
			nocache : true,
			params  :	{
									filtro_sucursal   : document.getElementById('filtro_sucursal_Baja').value,
									filtro_ubicacion  : document.getElementById('filtro_ubicacion_Baja').value,
									opcGrillaContable : 'Baja',
								}
		});
	}

	var totalDepreciaciones = 0.00;
	var totalDeterioro      = 0.00;

  function calculaValorTotalesDocumento(accion,valor) {
        if (document.getElementById('subtotalAcumuladoDeterioro')) {
        	if (accion=='agregar') {
	            totalDeterioro+=(valor<=0)? 0 : valor;
	        }
	        else if (accion=='eliminar') {
	            totalDeterioro-=Math.abs(valor);
	        }

	        if (totalDeterioro<0) {totalDeterioro=0}
			document.getElementById('subtotalAcumuladoDeterioro').innerHTML = formato_numero(totalDeterioro,<?php echo $_SESSION['DECIMALESMONEDA']; ?>, '.', ',');
			document.getElementById('totalAcumuladoDeterioro').innerHTML    = formato_numero(totalDeterioro,<?php echo $_SESSION['DECIMALESMONEDA']; ?>, '.', ',');
        }
        else{
        	if (accion=='agregar') {
	            totalDepreciaciones+=valor;
	        }
	        else if (accion=='eliminar') {
	            totalDepreciaciones-=valor;
	        }

			document.getElementById('subtotalAcumuladoDepreciaciones').innerHTML = formato_numero(totalDepreciaciones,<?php echo $_SESSION['DECIMALESMONEDA']; ?>, '.', ',');
			document.getElementById('totalAcumuladoDepreciaciones').innerHTML    = formato_numero(totalDepreciaciones,<?php echo $_SESSION['DECIMALESMONEDA']; ?>, '.', ',');
        }

    }

  function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06

        numero=parseFloat(numero);
        if(isNaN(numero)){ return ''; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); }  // Redondeamos

        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) { numero=numero.replace(miles, '$1' + separador_miles + '$2'); }
        }

        return numero;
    }

  function cargando_documentos(texto,opc){
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
</script>
