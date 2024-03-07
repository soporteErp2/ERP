<?php

	include("../configuracion/conectar.php");

	$btnFacturaWs = '';
	$id_empresa   = $_SESSION['EMPRESA'];

	// CONSULTAR SI TIENE FACTURAS GENERADAS POR EL WEB SERVICE
	$sql   = "SELECT COUNT(id) AS cont
				FROM compras_facturas
				WHERE activo=1
					AND id_empresa=$id_empresa
					AND estado=1
					AND id_saldo_inicial=0
					AND tipo='Ws'
					AND id NOT IN(SELECT id_factura_compra FROM compras_facturas_inventario WHERE activo=1)";
	$query = mysql_query($sql,$link);
	$cont  = mysql_result($query, 0,'cont');

	if ($cont>0) {
		$btnFacturaWs = ",{
												xtype	: 'buttongroup',
												id      : '',
												height  : 80,
												columns	: 1,
												style   : 'border:none;',
												title	: 'Facturas Webservice',
												items	:
												[
													{
				                    xtype       : 'button',
				                    id     		  : '',
				                    width				: 60,
														height			: 56,
				                    text        : 'Consultar Facturas',
				                    tooltip		  : 'Facturas generadas con web-service',
				                    scale       : 'large',
				                    iconCls     : 'doc_sinc',
				                    iconAlign   : 'top',
				                    handler     : function(){ BloqBtn(this); ventanaBuscarFacturasWs();}
				                	}
				            		]
				        			}";

	}

$acumScript .= (user_permisos(38,'false') == 'true')? 'Ext.getCmp("Btn_guardar_FacturaCompra").enable();' : '';

$permiso_orden_compra           = (user_permisos(32,'false') == 'true')? 'false' : 'true';
$permiso_factura_compra         = (user_permisos(37,'false') == 'true')? 'false' : 'true';
$permiso_factura_compra_cuentas = (user_permisos(153,'false') == 'true')? 'false' : 'true';
$permiso_comprobante_egreso     = (user_permisos(42,'false') == 'true')? 'false' : 'true';
$permiso_requisicion            = (user_permisos(170,'false') == 'true')? 'false' : 'true';
$permiso_entrada_almacen        = (user_permisos(175,'false') == 'true')? 'false' : 'true';


?>
<style>
	.divIndicador{
		width  : 100%;
		height : 60px;
		margin : 15px 0 0 0;
		float  : left;
		cursor : pointer;
	}
</style>
<script>

	var id_proveedor_orden_compra = 0
		,	id_proveedor_factura      = 0;

	Ext.QuickTips.init();
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
						{
							region		: 'north',
							xtype			: 'panel',
							height		: 33,
							border		: false,
							margins		: '0 0 0 0',
							html			: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
							bodyStyle : 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
						},
						{
							region					: 'center',
							xtype						: 'tabpanel',
							margins					: '0 0 0 0',
							deferredRender	: true,
							border					: false,
							activeTab				: 0,
							bodyStyle 			: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							items						:
							[

                                {
									closable		: false,
									autoScroll	: true,
									title				: 'Dashboard',
									iconCls 		: '',
									disabled    : false,
									// bodyStyle 	: 'background:url(../../temas/clasico/images/Fondo.png)',
									items				:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_DashboardCompra',
											border	: false,
										}
									],
									tbar		:
									[
										{
											xtype		: 'buttongroup',
											columns	: 3,
											title		: 'Filtro',
											items		:
											[
												{
													xtype				: 'panel',
													border			: false,
													width				: 260,
													height			: 56,
													bodyStyle 	: 'background-color:rgba(255,255,255,0)',
													autoLoad    :
													{
														url			: '../funciones_globales/filtros/filtro_sucursal_todos.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc           		: "DashboardCompra",
															imprimeVarPhp 		: 'opcGrillaContable : "DashboardCompra"',
															renderizaBody 		: 'true',
															url_render    		: 'dashboard/dashboard.php',
															url_filtro_bodega : '../funciones_globales/filtros/filtro_bodega_todos.php',

														}
													}
												}
											]
										},'->',
									    {
									        xtype       : 'button',
									        width       : 65,
									        height      : 65,
									        //text        : 'Regresar',
									        scale       : 'large',
									        iconCls     : 'regresar',
									        iconAlign   : 'top',
									        hidden      : false,
									        handler     : function(){ cambiarPeriodoAtras();}
									    },'-',
									    {
									        xtype       : "tbtext",
									        text        : '<div class="divIndicador" id="divIndicador" onClick="cambiarPeriodoAdelante()">'
									        					+'<div id="id_periodo" style="font-size:30px; text-align:center;"></div>'
									        					+'<div id="id_rango" style="font-size:11px; text-align:center;"></div>'
									        				+'</div>',
						                    scale       : "large",
									    },'-',
									    {
									        xtype       : 'button',
									        width       : 65,
									        height      : 65,
									        //text        : 'Regresar',
									        scale       : 'large',
									        iconCls     : 'regresar2',
									        iconAlign   : 'top',
									        hidden      : false,
									        handler     : function(){ cambiarPeriodoAdelante();}
									    }

									]
								},
//=========================================== REQUISICION DE COMPRA ===========================================//
								{
									closable		: false,
									autoScroll	: true,
									title				: '<span title="Requisicion - Solicitud de Compra">Requisicion</span>',
									iconCls 		: '',
									disabled    :  <?php echo $permiso_requisicion; ?>,
									bodyStyle 	: 'background-color:#FFF;',
									items				:
									[
										{
											xtype				: "panel",
											id					: 'contenedor_RequisicionCompra',
											border			: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										}
									],
									tbar				:
									[
										{
											xtype		: 'buttongroup',
											columns	: 3,
											title		: 'Filtro Bodega',
											items		:
											[
												{
													xtype			: 'panel',
													border		: false,
													width			: 160,
													height		: 56,
													bodyStyle : 'background-color:rgba(255,255,255,0)',
													autoLoad  :
													{
														url			: '../funciones_globales/filtros/filtro_unico_bodega.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc           : 'RequisicionCompra',
															renderizaBody : 'true',
															url_render    : 'requisicion/grillaContable.php',
															imprimeVarPhp : 'opcGrillaContable : "RequisicionCompra"',
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_RequisicionCompra',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 1,
											title	: 'Generar',
											items	:
											[
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_guardar_RequisicionCompra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Guardar',
								                    tooltip		: 'Genera Requisicion de compra',
								                    scale       : 'large',
								                    disabled 	: false,
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); guardarRequisicionCompra(); }
										        }
									      ]
										},
										{
											xtype		: 'buttongroup',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 7,
											title		: 'Opciones',
											items		:
											[
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_nueva_RequisicionCompra',
								                    width		: 60,
																		height		: 56,
								                    text        : 'Nuevo',
								                    tooltip		: 'Nueva Requisicion de compra',
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); nuevaRequisicionCompra(); }
										        },
												{
								                    xtype       : 'button',
								                    width		: 60,
																		height		: 56,
								                    text        : 'Buscar',
								                    tooltip		: 'Buscar Requisicion de compra',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarRequisicionCompra(); }
										        },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_RequisicionCompra',
								                    width		: 60,
																		height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Eliminar Requisicion de compra',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); btnDeleteRequisicionCompra(); }
								        		}
							      			]
						          		},'-',
					          			{
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_RequisicionCompra',
						                    columns : 5,
						                    title   : 'Documento Generado',
						                    items   : [
		                    							{
															xtype     : "splitbutton",
															id        : 'btnExportarRequisicionCompra',
															tooltip   : 'Imprimir a un documento PDF',
															iconCls   : "pdf32_new",
															scale     : "large",
															iconAlign : 'top',
															text      : 'Imprimir',
															handler   : function(){ BloqBtn(this); imprimirRequisicionCompra('pdf'); },
												            menu:
												            [
											            		{
																	text    : "Exportar a Excel",
																	iconCls : "xls16",
																	handler : function(){ BloqBtn(this); imprimirRequisicionCompra('xls'); }
											            		}
												          	]
												        },
														{
															xtype     : 'button',
															id        : 'Btn_editar_RequisicionCompra',
															width     : 60,
															height    : 56,
															text      : 'Editar',
															tooltip   : 'Editar Requisicion de compra',
															scale     : 'large',
															iconCls   : 'edit',
															iconAlign : 'top',
															handler   : function(){ BloqBtn(this); modificarRequisicionCompra(); }
				              							},
														{
															xtype     : 'button',
															id        : 'Btn_restaurar_RequisicionCompra',
															width     : 60,
															height    : 56,
															text      : 'Restaurar',
															tooltip   : 'Restaurar Requisicion de compra',
															scale     : 'large',
															iconCls   : 'restaurar32',
															iconAlign : 'top',
															handler   : function(){ BloqBtn(this); restaurarRequisicionCompra(); }
								                		},
								                		{
															xtype     : 'button',
															id        : 'Btn_autorizar_RequisicionCompra',
															width     : 60,
															height    : 56,
															text      : 'Autorizar',
															tooltip   : 'Autorizar Requisicion',
															scale     : 'large',
															iconCls   : 'verifica_doc',
															iconAlign : 'top',
															handler   : function(){ BloqBtn(this); ventanaAutorizarRequisicionCompra(); }
								                		},
														{
															xtype     : 'button',
															id        : 'Btn_upload_RequisicionCompra',
															width     : 60,
															height    : 56,
															text      : 'Anexar',
															disabled  : true,
															tooltip   : 'Anexar documento',
															scale     : 'large',
															iconCls   : 'upload_file32',
															iconAlign : 'top',
															handler   : function(){ BloqBtn(this); btnAnexaRequisicionCompra(); }
										                },
													]
											},'->',
						                  	{
					                      		xtype       : "tbtext",
							                    text        : '<div id="titleDocumentoRequisicionCompra" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
							                    scale       : "large",
						                  	}
										]
									},

//=========================================== ORDENES DE COMPRA ===========================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Ordenes de Compra',
									iconCls 	: '',
									disabled    : <?php echo $permiso_orden_compra; ?>,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_ordenes_compra',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										}
									],
									tbar		:
									[
										{
											xtype	: 'buttongroup',
											columns	: 3,
											title	: 'Filtro Bodega',
											items	:
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 160,
													height		: 56,
													bodyStyle 	: 'background-color:rgba(255,255,255,0)',
													autoLoad    :
													{
														url		: '../funciones_globales/filtros/filtro_unico_bodega.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc           : 'ordenes_compra',
															renderizaBody : 'true',
															url_render    : 'ordenes_compra/ordenes_compra.php',
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											columns	: 3,
											title	: 'Importar',
											items	:
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 155,
													height		: 56,
													bodyStyle 	: 'background-color:rgba(255,255,255,0);',
													autoLoad    :
													{
														url		: 'bd/bd.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc               : 'cargarCampoCotizacionPedido',
															opcGrillaContable : 'OrdenCompra'
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_orden_compra',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 1,
											title	: 'Generar',
											items	:
											[
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_guardar_orden_compra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Guardar',
								                    tooltip		: 'Genera Orden de compra',
								                    scale       : 'large',
								                    disabled 	: false,
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); guardarOrdenCompra(); }
								                }
								            ]
										},
										{
											xtype	: 'buttongroup',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 7,
											title	: 'Opciones',
											items	:
											[
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_nueva_orden_compra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Nuevo',
								                    tooltip		: 'Nueva Orden de compra',
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); nuevaOrdenCompra(); }
								                },
												{
								                    xtype       : 'button',
								                    width       : 60,
																		height			: 56,
								                    text        : 'Buscar',
								                    tooltip		: 'Buscar Orden de compra',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarOrdenCompra(); }
								                },
												{
													xtype     : 'button',
													id        : 'Btn_cancelar_orden_compra',
													width     : 60,
													height    : 56,
													text      : 'Cancelar',
													tooltip   : 'Eliminar Orden de compra',
													scale     : 'large',
													iconCls   : 'cancel',
													iconAlign : 'top',
													handler   : function(){ BloqBtn(this); btnDeleteOrdenCompra(); }
								                }

								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_orden_compra',
						                    columns : 6,
						                    title   : 'Documento Generado',
						                    items   :
						                    [

					                            {
													xtype     : "splitbutton",
													id        : 'Btn_imprimir_orden_compra',
													tooltip   : 'Imprimir a un documento PDF',
													iconCls   : "pdf32_new",
													scale     : "large",
													iconAlign : 'top',
													text      : 'Imprimir',
													handler   : function(){ BloqBtn(this); imprimirOrdenCompra(); },
										            menu:
										            [
									            		{
															text    : "Imprimir normal",
															iconCls : "pdf16",
															handler : function(){ BloqBtn(this); imprimirOrdenCompra(); }
									            		},
									            		{
															text    : "Imprimir en otra moneda",
															iconCls : "pdf16",
															handler : function(){ BloqBtn(this); win_Select_moneda(); }
									            		}
										          	]
										        },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_orden_compra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Orden de compra',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarOrdenCompra(); }
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_orden_compra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Orden de compra',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarOrdenCompra(); }
								                },
												{
													xtype     : 'button',
													id        : 'Btn_validar_orden_compra',
													width     : 60,
													height    : 56,
													text      : 'Validar',
													tooltip   : 'Validar Orden de compra',
													scale     : 'large',
													iconCls   : 'verifica_doc',
													iconAlign : 'top',
													handler   : function(){ ventanaAutorizarOrdenCompra(); }
								                },
								                {
													xtype     : 'button',
													id        : 'Btn_upload_orden_compra',
													width     : 60,
													height    : 56,
													text      : 'Anexar',
													disabled  : true,
													tooltip   : 'Anexar documento',
													scale     : 'large',
													iconCls   : 'upload_file32',
													iconAlign : 'top',
													handler   : function(){ BloqBtn(this); btnAnexaOrdenCompra(); }
								                },
												{
													xtype     : 'button',
													id        : 'Btn_enviar_correo_OrdenCompra',
													width     : 60,
													height    : 56,
													text      : 'Enviar',
													tooltip   : 'Enviar Orden de compra por email',
													scale     : 'large',
													iconCls   : 'enviar',
													iconAlign : 'top',
													disabled  : false,
													handler   : function(){ BloqBtn(this); ventanaEnviarCorreo_OrdenCompra(); }
								                }
											]
										},'->',
		                                {
		                                    xtype       : "tbtext",
		                                    text        : '<div id="titleDocuementoOrdenCompra" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
		                                    scale       : "large",
		                                }
									]
								},

//=========================================== ENTRADAS DE ALMACEN ===========================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Entrada de Almacen',
									iconCls 	: '',
									disabled    : <?php echo $permiso_entrada_almacen; ?>,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_EntradaAlmacen',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										}
									],
									tbar		:
									[
										{
											xtype	: 'buttongroup',
											columns	: 3,
											title	: 'Filtro Bodega',
											items	:
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 160,
													height		: 56,
													bodyStyle 	: 'background-color:rgba(255,255,255,0);',
													autoLoad    :
													{
														url		: '../funciones_globales/filtros/filtro_unico_bodega.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc           : 'EntradaAlmacen',
															imprimeVarPhp : 'opcGrillaContable : "EntradaAlmacen"',
															renderizaBody : 'true',
															url_render    : 'entrada_almacen/grillaContable.php',
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											columns	: 3,
											title	: 'Importar',
											items	:
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 155,
													height		: 56,
													bodyStyle 	: 'background-color:rgba(255,255,255,0);',
													autoLoad    :
													{
														url		: 'bd/bd.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc               : 'cargarCampoCotizacionPedido',
															opcGrillaContable : 'EntradaAlmacen'
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_EntradaAlmacen',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 1,
											title	: 'Contabilizar',
											items	:
											[
												{
								                    xtype       : 'button',
								                    width		: 60,
													          height		: 56,
								                    text        : 'Guardar',
								                    tooltip		: 'Genera Documento',
								                    id     		: 'Btn_guardar_EntradaAlmacen',
								                    scale       : 'large',
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    disabled	: false,
								                    handler     : function(){ BloqBtn(this); guardarEntradaAlmacen() }
								                },
								            ]
										},
										{
											xtype	: 'buttongroup',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 8,
											title	: 'Opciones',
											items	:
											[
												{
													xtype       : 'button',
													width		: 60,
													height		: 56,
								                    text        : 'Nueva',
								                    tooltip		: 'Nuevo Documento',
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    id 			: 'btnNuevaEntradaAlmacen',
								                    iconAlign   : 'top',
								                    disabled 	: true,
								                    handler     : function(){ BloqBtn(this); nuevaEntradaAlmacen() }
												},
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    tooltip		: 'Buscar Documento',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarEntradaAlmacen() }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_EntradaAlmacen',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Cancelar Documento',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarEntradaAlmacen() }
								                }
								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_EntradaAlmacen',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
								                {
								                    xtype       : 'button',
								                    id			: 'btnExportarEntradaAlmacen',
								                    width		: 60,
													height		: 56,
								                    text        : 'Imprimir',
								                    tooltip		: 'Imprimir en un documento PDF',
								                    scale       : 'large',
								                    iconCls     : 'pdf32_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); imprimirEntradaAlmacen(); },
												    // menu		:
												    // [
													// 	{
													// 		text	: '<b>Imprimir en Excel</b>',
													// 		iconCls	: 'xls16',
													// 		handler	: function(){ BloqBtn(this); imprimirEntradaAlmacenExcel(); }
													// 	}
													// ]
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_EntradaAlmacen',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Documento',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoEntradaAlmacen(); }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_EntradaAlmacen',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Documento',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarEntradaAlmacen(); }
								                }
											]
										},'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleDocumentoEntradaAlmacen" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								},

//=========================================== FACTURAS ==================================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Factura de Compra',
									iconCls 	: '',
									disabled    : <?php echo $permiso_factura_compra; ?>,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_facturacion_compras',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										}
									],
									tbar		:
									[
										{
											xtype	: 'buttongroup',
											columns	: 3,
											title	: 'Filtro Bodega',
											items	:
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 160,
													height		: 56,
													bodyStyle 	: 'background-color:rgba(255,255,255,0)',
													autoLoad    :
													{
														url		: '../funciones_globales/filtros/filtro_unico_bodega.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc           : 'facturacion_compras',
															renderizaBody : 'true',
															url_render    : 'facturacion/facturacion_compras.php',
															imprimeVarPhp : 'opcGrillaContable : "FacturaCompra"',
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											columns	: 3,
											title	: 'Importar',
											items	:
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 160,
													height		: 56,
													bodyStyle 	: 'background-color:rgba(255,255,255,0)',
													autoLoad    :
													{
														url		: 'facturacion/bd/bd.php',
														scripts	: true,
														nocache	: true,
														params	: { opc	: 'cargarCampoOrdenCompra' }
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_FacturaCompra',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 1,
											title	: 'Contabilizar',
											items	:
											[
												{
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Guardar',
								                    tooltip		: 'Cerrar Factura de compra',
								                    id     		: 'Btn_guardar_FacturaCompra',
								                    scale       : 'large',
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    disabled	: false,
								                    handler     : function(){ BloqBtn(this); guardarFacturaCompra() }
								                }
								            ]
										},
										{
											xtype	: 'buttongroup',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 7,
											title	: 'Opciones',
											items	:
											[
								                {
													xtype       : 'button',
													width		: 60,
													height		: 56,
								                    text        : 'Nueva',
								                    tooltip		: 'Nueva Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    id 			: 'Btn_nueva_FacturaCompra',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); nuevaFacturaCompra() }
												},
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    tooltip		: 'Buscar Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarFacturaCompra() }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_FacturaCompra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                   	tooltip		: 'Cancelar Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarFacturaCompra(); }
								                },
								     //            {
								     //                xtype       : 'button',
								     //                id 			: 'Btn_editar_contabilidad_factura_compra',
								     //                width		: 60,
													// height		: 56,
								     //                text        : 'Buscar',
								     //                tooltip		: 'Edicion Contable',
								     //                scale       : 'large',
								     //                iconCls     : 'buscar_doc_new',
								     //                iconAlign   : 'top',
								     //                handler     : function(){ BloqBtn(this); editarContabilidadFacturaCompra(); }
								     //            },
								     //            {
								     //                xtype       : 'button',
								     //                id 			: 'Btn_cancelar_editar_contabilidad_factura_compra',
								     //                width		: 60,
													// height		: 56,
								     //                text        : 'Cancelar',
								     //               	tooltip		: 'Cancelar Edicion Contable',
								     //                scale       : 'large',
								     //                iconCls     : 'cancel',
								     //                iconAlign   : 'top',
								     //                handler     : function(){ BloqBtn(this); cancelarEditarContabilidadFacturaCompra(); }
								     //            }
								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_FacturaCompra',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
								                {
													xtype		: 'button',
													width		: 60,
													height		: 56,
													id			: 'Btn_imprimir_FacturaCompra',
													text		: 'Imprimir',
													tooltip		: 'Imprimir a un documento PDF',
													scale		: 'large',
													iconCls		: 'pdf32_new',
													iconAlign	: 'top',
													handler 	: function(){ BloqBtn(this); imprimirFacturaCompra(); },
													// menu		:
													// [
													// 	{
													// 		text	: '<b>Exportar a Excel</b>',
													// 		iconCls	: 'xls16',
													// 		handler	: function(){ BloqBtn(this); imprimirFacturaCompraExcel(); }
													// 	}
													// ]
												},
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_FacturaCompra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumento(); }
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_FacturaCompra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarFacturaCompra(); }
								                },
								                {
													xtype     : 'button',
													id        : 'Btn_enviar_factura_electronica_FacturaCompra',
													width     : 60,
													height    : 56,
													text      : 'Enviar a la DIAN',
													tooltip   : 'Enviar documento soporte',
													scale     : 'large',
													iconCls   : 'envia_doc',
													iconAlign : 'top',
													disabled  : false,
													handler   : function(){ BloqBtn(this); enviarDIANFacturaCompra(); }
								              	}
											]
										}
										,
										{
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : '',
						                    columns : 1,
						                    title   : 'Adjuntos',
						                    items   :
						                    [

												{
								                    xtype       : 'button',
								                    id 			: 'Btn_adjuntar_FacturaCompra',
								                    width		: 60,
													height		: 56,
								                    text        : 'Archivos Adjuntos',
								                    tooltip		: 'Adjuntar archivos',
								                    scale       : 'large',
								                    iconCls     : 'adjunto',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); ventanaDocumentosCruceFacturaCompra(); }
								                }
											]
										}
										<?php echo $btnFacturaWs; ?>
										,'->',
		                                {
		                                    xtype       : "tbtext",
		                                    text        : '<div id="titleDocuementoFacturaCompra" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
		                                    scale       : "large",
		                                }
									]
								},

//=========================================== FACTURAS POR CUENTAS ==================================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Factura x Cuentas',
									iconCls 	: '',
									disabled    : <?php echo $permiso_factura_compra_cuentas; ?>,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_FacturaCompraCuentas',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
											autoLoad    :
											{
												url		: 'facturacion_cuentas/grilla/grillaContable.php',
												scripts	: true,
												nocache	: true,
												params	:
												{
													opcGrillaContable               : 'FacturaCompraCuentas',
													// renderizaBody     : 'true',
													// url_render        : 'facturacion_cuentas/grilla/grillaContable.php',
												}
											}
										}
									],
									tbar		:
									[
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_FacturaCompraCuentas',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 1,
											title	: 'Contabilizar',
											items	:
											[
												{
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Guardar',
								                    tooltip		: 'Cerrar Factura de compra',
								                    id     		: 'Btn_guardar_FacturaCompraCuentas',
								                    scale       : 'large',
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    disabled	: false,
								                    handler     : function(){ BloqBtn(this); guardarFacturaCompraCuentas() }
								                }
								            ]
										},
										{
											xtype	: 'buttongroup',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 7,
											title	: 'Opciones',
											items	:
											[
								                {
													xtype       : 'button',
													width		: 60,
													height		: 56,
								                    text        : 'Nueva',
								                    tooltip		: 'Nueva Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    id 			: 'Btn_nueva_FacturaCompraCuentas',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); nuevaFacturaCompraCuentas() }
												},
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    tooltip		: 'Buscar Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarFacturaCompraCuentas() }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_FacturaCompraCuentas',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                   	tooltip		: 'Cancelar Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarFacturaCompraCuentas(); }
								                }

								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_FacturaCompraCuentas',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
								                {
													xtype		: 'button',
													width		: 60,
													height		: 56,
													id			: 'Btn_exportar_FacturaCompraCuentas',
													text		: 'Imprimir',
													tooltip		: 'Imprimir a un documento PDF',
													scale		: 'large',
													iconCls		: 'pdf32_new',
													iconAlign	: 'top',
													handler 	: function(){ BloqBtn(this); imprimirFacturaCompraCuentas(); },
												},
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_FacturaCompraCuentas',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoFacturaCompraCuentas(); }
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_FacturaCompraCuentas',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Factura de compra',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarFacturaCompraCuentas(); }
								                }
											]
										},
										{
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : '',
						                    columns : 1,
						                    title   : 'Adjuntos',
						                    items   :
						                    [

												{
								                    xtype       : 'button',
								                    id 			: 'Btn_adjuntar_FacturaCompraCuentas',
								                    width		: 60,
													height		: 56,
								                    text        : 'Archivos Adjuntos',
								                    tooltip		: 'Adjuntar archivos',
								                    scale       : 'large',
								                    iconCls     : 'adjunto',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); ventanaDocumentosCruce(); }
								                }
											]
										}

										,'->',
		                                {
		                                    xtype       : "tbtext",
		                                    text        : '<div id="titleDocumentoFacturaCompraCuentas" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
		                                    scale       : "large",
		                                }
									]
								},

//========================================= COMPROBANTE DE EGRESO ==========================================//
								{
									closable	  : false,
									autoScroll	: true,
									title		    : 'Comprobante de Egreso',
									iconCls 	  : '',
									disabled    : <?php echo $permiso_comprobante_egreso; ?>,
									bodyStyle 	: 'background-color:#FFF;',
									items		    :	[
																	{
																		xtype	 	  : "panel",
																		id			  : 'contenedor_ComprobanteEgreso',
																		border		: false,
																		bodyStyle : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
																		autoLoad  :	{
																									url		  : 'comprobante_egreso/grilla/grillaContable.php',
																									scripts	: true,
																									nocache	: true,
																									params	: { opcGrillaContable : "ComprobanteEgreso" }
																								}
																	}
																],
									tbar		    :	[
																	{
																		xtype	  : 'buttongroup',
																		id      : 'BtnGroup_Guardar_comprobante_egreso',
																		height  : 80,
							                    	style   : 'border:none;',
																		columns	: 1,
																		title	  : 'Contabilizar',
																		items	  :	[
																								{
															                    xtype     : 'button',
															                    id     		: 'Btn_guardar_comprobante_egreso',
															                    width		  : 60,
																				          height		: 56,
															                    text      : 'Guardar',
															                    tooltip		: 'Contabilizar',
															                    scale     : 'large',
															                    disabled 	: false,
															                    iconCls   : 'guardar',
															                    iconAlign : 'top',
															                    handler   : function(){ BloqBtn(this); guardarComprobanteEgreso() }
																                }
																            	]
															    },
																	{
																		xtype	  : 'buttongroup',
																		height  : 80,
							                    	style   : 'border:none;',
																		columns	: 7,
																		title	  : 'Opciones',
																		items	  :	[
																								{
															                    xtype     : 'button',
															                    width		  : 60,
																				          height		: 56,
																				          id 			  : 'btnNuevaComprobanteEgreso',
															                    text      : 'Nuevo',
															                    tooltip		: 'Nueva Comprobante Egreso',
															                    scale     : 'large',
															                    iconCls   : 'add_new',
															                    iconAlign : 'top',
															                    handler   : function(){ BloqBtn(this); nuevaComprobanteEgreso() }
																                },
																								{
															                    xtype     : 'button',
															                    width		  : 60,
																									height		: 56,
															                    text      : 'Buscar',
															                    tooltip		: 'Buscar Comprobante Egreso',
															                    scale     : 'large',
															                    iconCls   : 'buscar_doc_new',
															                    iconAlign : 'top',
															                    handler   : function(){ BloqBtn(this); buscarComprobanteEgreso() }
															                  },
																			          {
															                    xtype     : 'button',
															                    id 			  : 'Btn_cancelar_comprobante_egreso',
															                    width		  : 60,
																				          height		: 56,
															                    text      : 'Cancelar',
															                    tooltip		: 'Eliminar Comprobante Egreso',
															                    scale     : 'large',
															                    iconCls   : 'cancel',
															                    iconAlign : 'top',
															                    handler   : function(){ BloqBtn(this); cancelarComprobanteEgreso() }
															                  }
															                ]
													        },'-',
						                			{
								                    xtype   : 'buttongroup',
								                    height  : 80,
								                    id      : 'BtnGroup_Estado1_comprobante_egreso',
								                    columns : 5,
								                    title   : 'Documento Generado',
								                    items   :	[
								                  							{
															                    xtype     : 'splitbutton',
															                    id			  : 'btnExportarComprobanteEgreso',
															                    width		  : 60,
																				          height	  : 56,
															                    text      : 'Imprimir',
															                    tooltip	  : 'Imprimir en un documento PDF',
															                    scale     : 'large',
															                    iconCls   : 'pdf32_new',
															                    iconAlign : 'top',
															                    handler   : function(){ BloqBtn(this); imprimirComprobanteEgreso(); },
															                    menu		  : [
																																{
																																	text	: '<b>Exportar a Excel</b>',
																																	iconCls	: 'xls16',
																																	handler	: function(){ BloqBtn(this); imprimirComprobanteEgresoExcel(); }
																																}
																															]
								                                },
												                        {
															                    xtype     : 'button',
															                    id 			  : 'Btn_editar_comprobante_egreso',
															                    width		  : 60,
																									height		: 56,
															                    text      : 'Editar',
															                    tooltip		: 'Editar Comprobante Egreso',
															                    scale     : 'large',
															                    iconCls   : 'edit',
															                    iconAlign : 'top',
															                    handler   : function(){ BloqBtn(this); modificarDocumentoComprobanteEgreso(); }
																                },
																								{
															                    xtype     : 'button',
															                    id 			  : 'Btn_restaurar_comprobante_egreso',
															                    width		  : 60,
																									height		: 56,
															                    text      : 'Restaurar',
															                    tooltip		: 'Restaurar Comprobante Egreso',
															                    scale     : 'large',
															                    iconCls   : 'restaurar32',
															                    iconAlign : 'top',
															                    handler   : function(){ BloqBtn(this); restaurarComprobanteEgreso(); }
																                },
																								{
															                    xtype     : 'button',
															                    id 			  : 'Btn_enviar_correo_ComprobanteEgreso',
															                    width		  : 60,
																									height		: 56,
															                    text      : 'Enviar',
															                    tooltip		: 'Enviar Comprobante por email',
															                    scale     : 'large',
															                    iconCls   : 'enviar',
															                    iconAlign : 'top',
															                    disabled	: false,
															                    handler   : function(){ BloqBtn(this); ventanaEnviarCorreo_ComprobanteEgreso(); }
																                }
											                        ]
										              },
																	{
																		xtype   : 'buttongroup',
																		height  : 80,
																		id      : '',
																		columns : 1,
																		title   : 'Adjuntos',
																		items   :	[
																								{
																									xtype     : 'button',
																									id 			  : 'Btn_adjuntar_ComprobanteEgreso',
																									width		  : 60,
																									height		: 56,
																									text      : 'Archivos Adjuntos',
																									tooltip		: 'Adjuntar archivos',
																									scale     : 'large',
																									iconCls   : 'adjunto',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); ventanaDocumentosCruceComprobanteEgreso(); }
																								}
																							]
																	},'->',
	                                {
                                    xtype       : "tbtext",
                                    text        : '<div id="titleDocuementoComprobanteEgreso" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
                                    scale       : "large",
	                                }
									]
								},
							]
						}
					]
				}
			);
		}
	);

	function nuevaRequisicionCompra(){
		var validacion = validarArticulosRequisicionCompra();
		if (validacion==1){ if(!confirm("Hay articulos pendientes por guardar!\nDesea continuar?")){ return; } }

		Ext.get("contenedor_RequisicionCompra").load({
			url     : "requisicion/grillaContable.php",
			scripts : true,
			nocache : true,
			params  : { filtro_bodega : document.getElementById('filtro_ubicacion_RequisicionCompra').value, opcGrillaContable : "RequisicionCompra"}
		});

		document.getElementById('titleDocumentoRequisicionCompra').innerHTML='';
		// Ext.getCmp('Btn_validar_orden_compra').enable();

	}

	function btnAnexaRequisicionCompra(){

		var requisicion_compra = document.getElementById('titleDocumentoRequisicionCompra').innerHTML,
	 	    id_requisicion_compra = requisicion_compra.replace(/[^\d]/g, '');

		Win_Ventana_Documentos = new Ext.Window({
		    width       : 546,
		    height      : 500,
		    id          : 'Win_Ventana_Documentos',
		    title       : 'Formulario subir documentos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    : {
								        url     : 'requisicion/ventana_documentos_requisicion_compra.php',
								        scripts : true,
								        nocache : true,
								        params  :	{
		             										consecutivo : id_requisicion_compra
		        											}
		    							},
		    tbar        : [
		        						{
						            xtype   : 'buttongroup',
						            columns : 3,
						            title   : 'Opciones',
						            style   : 'border-right:none;',
						            items   :
						            [
						              {
				                    xtype       : 'button',
				                    width       : 60,
				                    height      : 56,
				                    text        : 'Regresar',
				                    scale       : 'large',
				                    iconCls     : 'regresar',
				                    iconAlign   : 'top',
				                    hidden      : false,
				                    handler     : function(){ BloqBtn(this); Win_Ventana_Documentos.close(id) }
					                },
					                {
				                    xtype       : 'button',
				                    width       : 60,
				                    height      : 56,
				                    text        : 'Anexar',
				                    scale       : 'large',
				                    iconCls     : 'upload_file32',
				                    iconAlign   : 'top',
				                    hidden      : false,
				                    handler     : function(){ BloqBtn(this); windows_upload_file(); }
					                }
						            ]
		        					}
		    						]
		}).show();
	}

	function nuevaOrdenCompra(){
		var validacion = validarArticulosOrdenCompra();
		if (validacion==1){ if(!confirm("Hay articulos pendientes por guardar!\nDesea continuar?")){ return; } }

		Ext.get("contenedor_ordenes_compra").load({
			url     : "ordenes_compra/ordenes_compra.php",
			scripts : true,
			nocache : true,
			params  : { filtro_bodega : document.getElementById('filtro_ubicacion_ordenes_compra').value }
		});

		document.getElementById('titleDocuementoOrdenCompra').innerHTML='';
		Ext.getCmp('Btn_validar_orden_compra').enable();

	}

	function nuevaEntradaAlmacen(){
		var validacion = validarArticulosEntradaAlmacen();
		if (validacion==1){ if(!confirm("Hay articulos pendientes por guardar!\nDesea continuar?")){ return; } }

		Ext.get("contenedor_EntradaAlmacen").load({
			url     : "entrada_almacen/grillaContable.php",
			scripts : true,
			nocache : true,
			params  : { filtro_bodega : document.getElementById('filtro_ubicacion_EntradaAlmacen').value, opcGrillaContable : "EntradaAlmacen"}
		});

		document.getElementById('titleDocumentoEntradaAlmacen').innerHTML='';
		// Ext.getCmp('Btn_validar_orden_compra').enable();

	}

	function nuevaFacturaCompra(){
		var validacion = validarArticulosFactura();
		if (validacion==1){ if(!confirm("Hay articulos pendientes por guardar!\nDesea continuar?")){ return; } }

		document.getElementById("ordenCompra").value="";
		Ext.get("contenedor_facturacion_compras").load({
			url     : "facturacion/facturacion_compras.php",
			scripts : true,
			nocache : true,
			params  :
			{
				opcGrillaContable : 'FacturaCompra',
				filtro_bodega     : document.getElementById('filtro_ubicacion_facturacion_compras').value
			}
		});

		document.getElementById('titleDocuementoFacturaCompra').innerHTML='';
	}

	function nuevaFacturaCompraCuentas(){
		var validacion = validarCuentasFacturaCompraCuentas();
		if (validacion==1){ if(!confirm("Hay cuentas pendientes por guardar!\nDesea continuar?")){ return; } }

		Ext.get("contenedor_FacturaCompraCuentas").load({
			url     : "facturacion_cuentas/grilla/grillaContable.php",
			scripts : true,
			nocache : true,
			params  : { opcGrillaContable:'FacturaCompraCuentas' }
		});

		document.getElementById('titleDocumentoFacturaCompraCuentas').innerHTML='';
	}

	function nuevaComprobanteEgreso(){

		Ext.get("contenedor_ComprobanteEgreso").load({
			url     : 'comprobante_egreso/grilla/grillaContable.php',
			scripts : true,
			nocache : true,
			params  : {  opcGrillaContable : 'ComprobanteEgreso' }
		});

		document.getElementById('titleDocuementoComprobanteEgreso').innerHTML=''
	}

	function resizeHeadMyGrilla(divScroll, idHead){

		if (!divScroll.querySelector(".body"+divScroll.id)) { return; }

		var idDivScroll  = divScroll.id
		,	widthBody    = (divScroll.querySelector(".body"+idDivScroll).offsetWidth)*1;

		var divHead      = document.getElementById(idHead);
		var widthHead    = (divHead.offsetWidth)*1;


		if(isNaN(widthBody) || widthBody == 0 || widthBody == widthHead){ return; }
		else if(widthBody > widthHead){ console.log('2'); divHead.setAttribute('style','width: calc(100% - 1px);'); }
		else if(widthBody < widthHead){ console.log('3'); divHead.setAttribute('style','width: calc(100% - 18px);'); }
	}

	function ventanaBuscarFacturasWs(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_ventana_buscar_facturas_ws = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_ventana_buscar_facturas_ws',
		    title       : '',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'bd/buscarFacturasWs.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opcGrillaContable : 'FacturaCompra',
		            var2 : 'var2',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            style   : 'border-right:none;',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_ventana_buscar_facturas_ws.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
		}

		// FUNCION PARA ACTUALIZAR LA FILA DE LA VENTANA DEL DOCUMENTO CRUCE

	function actualiza_fila_ventana_busqueda_doc_cruce(id) {
        var div    = '';
        var divImg = '';
        var divEvt = '';

        // MOSTRAR LA FILA DE LA VENTANA DEL DOCUMENTO CRUCE COMO ELIMINADO
        if (document.getElementById("div_nomina_planillas_empleados_contabilizacion_cuenta_colgaap_"+id)) {
			div    = document.getElementById('item_nomina_planillas_empleados_contabilizacion_'+id);
			divImg = document.getElementById('MuestraToltip_nomina_planillas_empleados_contabilizacion_'+id);
			divEvt = document.getElementById('MuestraToltip_General_nomina_planillas_empleados_contabilizacion_'+id);
        }
        else if (document.getElementById("div_grillaPedidoFactura_consecutivo_"+id)) {
			div    = document.getElementById('item_grillaPedidoFactura_'+id);
			divImg = document.getElementById('MuestraToltip_grillaPedidoFactura_'+id);
			divEvt = document.getElementById('MuestraToltip_General_grillaPedidoFactura_'+id);
        }

        if (div) {
        	div.setAttribute('style',div.getAttribute('style')+';color:#999 !important;font-style:italic;background-color:#e5ffe5 !important;');
        }

        if (divEvt) {
        	divEvt.setAttribute('ondblclick','');
        }

    	if (divImg) {
    		divImg.setAttribute('style',divImg.getAttribute('style')+';background-image:url(../../misc/MyGrilla/MyGrillaFondoOk.png);');
    	}


    }

	function RecargaDashboard(){

	    var id_bodega = document.getElementById('filtro_ubicacion_DashboardCompra').value,
		  id_sucursal = document.getElementById('filtro_sucursal_DashboardCompra').value;

		  Ext.getCmp('contenedor_DashboardCompra').load(
			{
				url     : 'dashboard/dashboard.php',
				scripts : true,
				nocache : true,
				params  :
				{
					filtro_bodega   : id_bodega,
					filtro_sucursal : id_sucursal
				}
			}
		);
	}

	function validarOrdenCompra(){
		MyLoading2('on');
		var orden_compra = document.getElementById('titleDocuementoOrdenCompra').innerHTML;
		var id_orden_compra = orden_compra.replace(/[^\d]/g, '');

		//alert(id_orden_compra);
		Ext.getCmp("Btn_validar_orden_compra").disable();
        Ext.Ajax.request({
            url     : 'ordenes_compra/bd/bd.php',
            params  :
            {
            	opc  : 'validarOrdenCompra',
        		consecutivo : id_orden_compra

            },
            success :function (result, request){
            	MyLoading2('off');
                        if(result.responseText != 'true'){ console.log("true"); }
                        else{ console.log("false"); }
                    },
            failure : function(){ console.log("fail"); MyLoading2('off'); }
        });

    	//       setTimeout(function(){
				//    bloquearBoton();
		  // }, 1000);


	}

	function btnAnexaOrdenCompra(){

		//funcionalidad del boton para subir documentos

		var orden_compra = document.getElementById('titleDocuementoOrdenCompra').innerHTML,
	 	    id_orden_compra = orden_compra.replace(/[^\d]/g, '');

		//var myalto  = Ext.getBody().getHeight();
		//var myancho = Ext.getBody().getWidth();

		Win_Ventana_Documentos = new Ext.Window({
		    width       : 546,
		    height      : 500,
		    id          : 'Win_Ventana_Documentos',
		    title       : 'Formulario subir documentos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'ordenes_compra/ventana_documentos_orden_compra.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		             consecutivo : id_orden_compra

		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            style   : 'border-right:none;',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_Documentos.close(id) }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Anexar',
		                    scale       : 'large',
		                    iconCls     : 'upload_file32',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); windows_upload_file(); }
		                }
		            ]
		        }
		    ]
		}).show();


	}

	function windows_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;'); }

	function close_ventana_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style',''); }

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

	// ACTUALIZAR TIPO DE FACTURA DE COMPRA
	const updateInvoceType = params =>{
		// console.log(params)
		Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  :
            {
				opc        : 'updateInvoceType',
				invoice_id : params.invoice_id,
				type       : params.select.value
            },
            success :function (result, request){
                        // console.log(result)
                    },
            failure : function(error){
                // console.log(error)
            }
        });
	}

</script>
