<?php
	include("../configuracion/conectar.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$acumScript .= (user_permisos(38,'false') == 'true')? 'Ext.getCmp("Btn_guardar_factura_compra").enable();' : '';

	$permiso_cotizacion  = (user_permisos(5,'false') == 'true')? 'false' : 'true';
	$permiso_pedido      = (user_permisos(10,'false') == 'true')? 'false' : 'true';
	$permiso_remision    = (user_permisos(15,'false') == 'true')? 'false' : 'true';
	$permiso_factura     = (user_permisos(20,'false') == 'true')? 'false' : 'true';
	$permiso_recibo_caja = (user_permisos(23,'false') == 'true')? 'false' : 'true';
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
	var id_cliente_Estandar = 0
	,	id_cliente_PedidoVenta     = 0
	,	id_cliente_FacturaVenta    = 0
	,	id_cliente_RemisionesVenta = 0;


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
							xtype		: 'panel',
							height		: 33,
							border		: false,
							margins		: '0 0 0 0',
							html		: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
							bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
						},
						{
							region			: 'center',
							xtype			: 'tabpanel',
							margins			: '0 0 0 0',
							activeTab		: 0,
							deferredRender	: true,
							border			: false,
							//activeTab		: 0,
							bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							items			:
							[

//==============================================// DASHBOARD //==============================================//
//***********************************************************************************************************//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Dashboard',
									iconCls 	: '',
									disabled    : false,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_DashboardVenta',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										}
									],
									tbar		:
									[
										{
											xtype	: 'buttongroup',
											columns	: 3,
											title	: 'Filtro',
											items	:
											[
												{
													xtype		: 'panel',
													border		: false,
													width		: 260,
													height		: 56,
													bodyStyle 	: 'background-color:rgba(255,255,255,0)',
													autoLoad    :
													{
														url		: '../funciones_globales/filtros/filtro_sucursal_todos.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc           : "DashboardVenta",
															imprimeVarPhp : 'opcGrillaContable : "DashboardVenta"',
															renderizaBody : 'true',
															url_render    : 'dashboard/dashboard.php',
															url_filtro_bodega    : '../funciones_globales/filtros/filtro_bodega_todos.php',
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
									        handler     : function(){ cambiarPeriodoAdelante(); }
									    }
									]
								},
//==============================================// ESTANDAR //==============================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Estandar',
									iconCls 	: '',
									disabled    : false,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_Estandar',
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
															opc           : "Estandar",
															imprimeVarPhp : 'opcGrillaContable : "Estandar"',
															renderizaBody : 'true',
															url_render    : 'estandar/grilla_contable.php',
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_Estandar',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 1,
											title	: 'Generar',
											items	:
											[
												{
								                    xtype       : 'button',
								                    id     		: 'Btn_guardar_Estandar',
								                    width		: 60,
													height		: 56,
								                    text        : 'Guardar',
								                    tooltip		: 'Genera Cotizacion',
								                    scale       : 'large',
								                    disabled 	: true,
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); guardarEstandar() }
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
													id 			: 'btnNuevaEstandar',
								                    text        : 'Nuevo',
								                    tooltip		: 'Nueva Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); nuevaEstandar() }
								                },
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    tooltip		: 'Buscar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarEstandar() }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_Estandar',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Eliminar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarEstandar() }
								                }
								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_Estandar',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
												{
								                    xtype       : 'button',
								                    id			: 'btnExportarEstandar',
								                    width		: 60,
													height		: 56,
								                    text        : 'Imprimir',
								                    tooltip		: 'Imprimir en un documento PDF',
								                    scale       : 'large',
								                    iconCls     : 'pdf32_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); imprimirEstandar(); },
												    // menu		:
												    // [
													// 	{
													// 		text	: '<b>Imprimir en Excel</b>',
													// 		iconCls	: 'xls16',
													// 		handler	: function(){ BloqBtn(this); imprimirEstandarExcel(); }
													// 	}
													// ]
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_Estandar',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoEstandar(); }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_Estandar',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarEstandar(); }
								                }
											]
										},'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleDocumentoEstandar" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								},
//============================================== PEDIDO ==========================================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Pedido',
									iconCls 	: '',
									disabled    : false,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_PedidoVenta',
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
															opc           : 'PedidoVenta',
															imprimeVarPhp : 'opcGrillaContable : "PedidoVenta"',
															renderizaBody : 'true',
															url_render    : 'pedido/grillaContable.php',
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											columns	: 3,
											title	: 'Importar Cotizacion',
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
															opcGrillaContable : 'PedidoVenta'
														}
													}
												}
											]
										},
										// {
										// 	xtype	: 'buttongroup',
										// 	height  : 80,
          //           						style   : 'border:none;',
										// 	columns	: 1,
										// 	title	: 'Importar',
										// 	items	:
										// 	[
										// 		{
								  //                   xtype       : 'button',
								  //                   id     		: 'btnCargarPedidoVenta',
								  //                   width		: 60,
										// 			height		: 56,
								  //                   text        : 'Cotizacion',
								  //                   tooltip		: 'Cargar Cotizacion',
								  //                   scale       : 'large',
								  //                   disabled 	: false,
								  //                   iconCls     : 'carga_doc',
								  //                   iconAlign   : 'top',
								  //                   handler     : function(){ BloqBtn(this); ventanaBuscarCotizacionPedidoPedidoVenta() }
								  //               }
										// 	]
										// },'-',
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_PedidoVenta',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 1,
											title	: 'Generar',
											items	:
											[
												{
								                    xtype       : 'button',
								                    id     		: 'Btn_guardar_PedidoVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Guardar',
								                    tooltip		: 'Genera Pedido',
								                    scale       : 'large',
								                    disabled 	: true,
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); guardarPedidoVenta() }
								                }
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
								                    text        : 'Nuevo',
								                    id 			: 'btnNuevaPedidoVenta',
								                    tooltip		: 'Nuevo Pedido',
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); nuevaPedidoVenta() }
								                },
												{
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    tooltip		: 'Buscar Pedido',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarPedidoVenta() }
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_PedidoVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Eliminar Pedido',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarPedidoVenta() }
								                }
								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_PedidoVenta',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
												{
								                    xtype       : 'button',
								                    id			: 'btnExportarPedidoVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Imprimir',
								                    tooltip		: 'Imprimir en un documento PDF',
								                    scale       : 'large',
								                    iconCls     : 'pdf32_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); imprimirPedidoVenta(); },
												    // menu		:
												    // [
													// 	{
													// 		text	: '<b>Imprimir en Excel</b>',
													// 		iconCls	: 'xls16',
													// 		handler	: function(){ BloqBtn(this); imprimirPedidoVentaExcel(); }
													// 	}
													// ]
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_PedidoVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Pedido',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoPedidoVenta(); }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_PedidoVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Pedido',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarPedidoVenta(); }
								                }
											]
										},'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleDocumentoPedidoVenta" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								},
//============================================== REMISIONES ======================================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Remisiones',
									iconCls 	: '',
									disabled    : false,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_RemisionesVenta',
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
															opc           : 'RemisionesVenta',
															imprimeVarPhp : 'opcGrillaContable : "RemisionesVenta"',
															renderizaBody : 'true',
															url_render    : 'remisiones/grillaContable.php',
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
															opcGrillaContable : 'RemisionesVenta'
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_RemisionesVenta',
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
								                    tooltip		: 'Genera Remision',
								                    id     		: 'Btn_guardar_RemisionesVenta',
								                    scale       : 'large',
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    disabled	: false,
								                    handler     : function(){ BloqBtn(this); guardarRemisionesVenta() }
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
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    id 			: 'btnNuevaRemisionesVenta',
								                    iconAlign   : 'top',
								                    disabled 	: true,
								                    handler     : function(){ BloqBtn(this); nuevaRemisionesVenta() }
												},
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarRemisionesVenta() }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_RemisionesVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Cancelar Remision',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarRemisionesVenta() }
								                }
								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_RemisionesVenta',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
								                {
								                    xtype       : 'button',
								                    id			: 'btnExportarRemisionesVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Imprimir',
								                    tooltip		: 'Imprimir en un documento PDF',
								                    scale       : 'large',
								                    iconCls     : 'pdf32_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); imprimirRemisionesVenta(); },
												    // menu		:
												    // [
													// 	{
													// 		text	: '<b>Imprimir en Excel</b>',
													// 		iconCls	: 'xls16',
													// 		handler	: function(){ BloqBtn(this); imprimirRemisionesVentaExcel(); }
													// 	}
													// ]
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_RemisionesVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoRemisionesVenta(); }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_RemisionesVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarRemisionesVenta(); }
								                }
											]
										},'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleDocumentoRemisionesVenta" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								},
//============================================== FACTURAS ========================================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Factura de Venta',
									iconCls 	: '',
									disabled    : false,
									bodyStyle 	: 'background-color:#FFF;',
									handler     : function(){ shortCuts(event); },
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_FacturaVenta',
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
															opc           : 'FacturaVenta',
															imprimeVarPhp : 'opcGrillaContable : "FacturaVenta"',
															renderizaBody : 'true',
															url_render    : 'facturacion/grillaContable.php',
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
													bodyStyle 	: 'background-color:rgba(255,255,255,0)',
													autoLoad    :
													{
														url		: 'bd/bd.php',
														scripts	: true,
														nocache	: true,
														params	:
														{
															opc               : 'cargarCampoCotizacionPedido',
															opcGrillaContable : 'FacturaVenta'
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_FacturaVenta',
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
								                    tooltip		: 'Genera Factura de Venta',
								                    id     		: 'Btn_guardar_FacturaVenta',
								                    scale       : 'large',
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); guardarFacturaVenta() }
								                }
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
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    id 			: 'btnNuevaFacturaVenta',
								                    iconAlign   : 'top',
								                    disabled 	: true,
								                    handler     : function(){ BloqBtn(this); nuevaFacturaVenta() }
												},
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarFacturaVenta() }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_FacturaVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Cancelar Factura',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarFacturaVenta() }
								                }
								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_FacturaVenta',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
								                {
													xtype		: 'button',
													width		: 60,
													height		: 56,
													id			: 'btnExportarFacturaVenta',
													text		: 'Imprimir',
													tooltip		: 'Imprimir en un documento PDF',
													scale		: 'large',
													iconCls		: 'pdf32_new',
													iconAlign	: 'top',
													handler 	: function(){ BloqBtn(this); imprimirFacturaVenta(); },
													// menu		:
													// [
													// 	{
													// 		text	: '<b>Imprimir en Excel</b>',
													// 		iconCls	: 'xls16',
													// 		handler	: function(){ BloqBtn(this); imprimirFacturaVentaExcel(); }
													// 	}
													// ]
												},
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_FacturaVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoFacturaVenta(); }
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_FacturaVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarFacturaVenta(); }
								                }
											]
										}
										<?php echo $btnFacturaWs; ?>
										,'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleDocumentoFacturaVenta" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								},
//===========================================RECIBOS DE CAJA =======================================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Recibos de Caja',
									iconCls 	: '',
									disabled    : false,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_ReciboCaja',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
											autoLoad    :
											{
												url		: 'recibo_caja/grilla/grillaContable.php',
												scripts	: true,
												nocache	: true,
												params : { opcGrillaContable : 'ReciboCaja' }
											}
										}
									],
									tbar		:
									[
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_ReciboCaja',
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
								                    tooltip		: 'Genera Recibo de Caja',
								                    id     		: 'Btn_guardar_ReciboCaja',
								                    scale       : 'large',
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); guardarReciboCaja() }
								                }
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
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    id 			: 'btnNuevaReciboCaja',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); nuevaReciboCaja() }
												},
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarReciboCaja() }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_ReciboCaja',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Cancelar Recibo',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarReciboCaja() }
								                }
								            ]
						                }/*,
						                {
											xtype	: 'buttongroup',
											height  : 80,
						                    id      : 'Btn_addDocumento_ReciboCaja',
                    						style   : 'border:none;',
											columns	: 8,
											title	: 'Adjuntar',
											items	:
											[
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Documentos',
								                    scale       : 'large',
								                    iconCls     : 'add',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); adjuntarDocumentoReciboCaja() }
								                }
								            ]
						                }*/,'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_ReciboCaja',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
								                {
								                    xtype       : 'splitbutton',
								                    id			: 'btnExportarReciboCaja',
								                    width		: 60,
													height		: 56,
								                    text        : 'Imprimir',
								                    tooltip		: 'Imprimir en un documento PDF',
								                    scale       : 'large',
								                    iconCls     : 'pdf32_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); imprimirReciboCaja(); },
								                     menu		:
								                    [
														{
															text	: '<b>Imprimir en Excel</b>',
															iconCls	: 'xls16',
															handler	: function(){ BloqBtn(this); imprimirReciboCajaExcel(); }
														}
													]
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_ReciboCaja',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Recibo',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoReciboCaja(); }
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_ReciboCaja',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Recibo',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarReciboCaja(); }
								                }
											]
										},'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleDocumentoReciboCaja" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								}
							]
						}
					]
				}
			);
		}
	);


	//================================= NUEVA COTIZACION DE VENTA ==================================//
	function nuevaEsdtandar(){
		Ext.get("contenedor_Esdtandar").load({
			url     : "cotizacion/grillaContable.php",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_bodega     : document.getElementById('filtro_ubicacion_Esdtandar').value,
				opcGrillaContable : 'Esdtandar',
			}
		});
		Ext.getCmp('btnNuevaEsdtandar').disable();
		document.getElementById('titleDocumentoEsdtandar').innerHTML=''
	}
	//=================================== NUEVO PEDIDO DE VENTA ====================================//
	function nuevaPedidoVenta(){
		Ext.get("contenedor_PedidoVenta").load({
			url     : "pedido/grillaContable.php",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_bodega     : document.getElementById('filtro_ubicacion_PedidoVenta').value,
				opcGrillaContable : 'PedidoVenta',
			}
		});
		Ext.getCmp('btnNuevaPedidoVenta').disable();
		document.getElementById('titleDocumentoPedidoVenta').innerHTML=''
	}
	//==================================== NUEVA REMISION =========================================//
	function nuevaRemisionesVenta(){
		Ext.get("contenedor_RemisionesVenta").load({
			url     : "remisiones/grillaContable.php",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_bodega     : document.getElementById('filtro_ubicacion_RemisionesVenta').value,
				opcGrillaContable : 'RemisionesVenta',
			}
		});
		Ext.getCmp('btnNuevaRemisionesVenta').disable();
		document.getElementById('titleDocumentoRemisionesVenta').innerHTML='';
	}


	//================================ NUEVA FACTURA DE VENTA ======================================//
	function nuevaFacturaVenta(){
		Ext.get("contenedor_FacturaVenta").load({
			url     : "facturacion/grillaContable.php",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_bodega     : document.getElementById('filtro_ubicacion_FacturaVenta').value,
				opcGrillaContable : 'FacturaVenta',
			}
		});
		Ext.getCmp('btnNuevaFacturaVenta').disable();
		document.getElementById('cotizacionPedidoFacturaVenta').innerHTML ='';
		document.getElementById('titleDocumentoFacturaVenta').innerHTML   ='';
	}

	//==================================== NUEVA REMISION =========================================//
	function nuevaReciboCaja(){
		Ext.get("contenedor_ReciboCaja").load({
			url     : 'recibo_caja/grilla/grillaContable.php',
			scripts : true,
			nocache : true,
			params  : { opcGrillaContable : 'ReciboCaja' }
		});

		document.getElementById('titleDocumentoReciboCaja').innerHTML='';
		Ext.getCmp('Btn_guardar_ReciboCaja').enable();
	}


	//============================== CARGAR FACTURA DE VENTA AL GUARDAR ============================//
	function cargarFacturaVentaGuardada(idFactura){
		Ext.get("contenedor_FacturaVenta").load({
			url     : "bd/grillaContableBloqueada.php",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_bodega     : document.getElementById('filtro_ubicacion_FacturaVenta').value,
				opcGrillaContable : 'FacturaVenta',
				id_factura_venta  : idFactura
			}
		});
		Ext.getCmp('btnNuevaFacturaVenta').enable();
	}

	function resizeHeadMyGrilla(divScroll, idHead){
		if (!divScroll.querySelector(".body"+divScroll.id)) {
			return;
		}
		var idDivScroll  = divScroll.id
		,	widthBody    = (divScroll.querySelector(".body"+idDivScroll).offsetWidth)*1
		,	divHead      = document.getElementById(idHead)
		,	widthHead    = (divHead.offsetWidth)*1;

		if(isNaN(widthBody) || widthBody == 0 || widthBody == widthHead){ return; }
		else if(widthBody > widthHead){ divHead.setAttribute('style','width: calc(100% - 1px);'); }
		else if(widthBody < widthHead){ divHead.setAttribute('style','width: calc(100% - 18px);'); }
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
		            opcGrillaContable : 'FacturaVenta',
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
        if (document.getElementById("div_grillaCotizacionFactura_consecutivo_"+id)) {
			div    = document.getElementById('item_grillaCotizacionFactura_'+id);
			divImg = document.getElementById('MuestraToltip_grillaCotizacionFactura_'+id);
			divEvt = document.getElementById('MuestraToltip_General_grillaCotizacionFactura_'+id);
        }
        else if (document.getElementById("div_grillaPedidoFactura_consecutivo_"+id)) {
			div    = document.getElementById('item_grillaPedidoFactura_'+id);
			divImg = document.getElementById('MuestraToltip_grillaPedidoFactura_'+id);
			divEvt = document.getElementById('MuestraToltip_General_grillaPedidoFactura_'+id);
        }
        else if (document.getElementById("div_grillaRemisionFactura_consecutivo_"+id)) {
			div    = document.getElementById('item_grillaRemisionFactura_'+id);
			divImg = document.getElementById('MuestraToltip_grillaRemisionFactura_'+id);
			divEvt = document.getElementById('MuestraToltip_General_grillaRemisionFactura_'+id);
        }
        else if (document.getElementById("div_grillaCotizacionRemision_consecutivo_"+id)) {
			div    = document.getElementById('item_grillaCotizacionRemision_'+id);
			divImg = document.getElementById('MuestraToltip_grillaCotizacionRemision_'+id);
			divEvt = document.getElementById('MuestraToltip_General_grillaCotizacionRemision_'+id);
        }
        else if (document.getElementById("div_grillaPedidoRemision_consecutivo_"+id)) {
			div    = document.getElementById('item_grillaPedidoRemision_'+id);
			divImg = document.getElementById('MuestraToltip_grillaPedidoRemision_'+id);
			divEvt = document.getElementById('MuestraToltip_General_grillaPedidoRemision_'+id);
        }

        if (div) {
        	div.setAttribute('style',div.getAttribute('style')+';color:#999 !important;font-style:italic;background-color:#e5ffe5 !important;');
        }
        if (divEvt) {
        	divEvt.setAttribute('ondblclick','');
        }
    	if (divImg) {
    		divImg.setAttribute('style',divImg.getAttribute('style')+'background-image:url(../../misc/MyGrilla/MyGrillaFondoOk.png);');
    	}


    }

	// alert("Aviso,\nEl periodo de prueba del software ERP correspondiente al 2014 ha concluido.\nSe recuerda que unicamente se deben registrar las facturas pertenecientes al 2015");

</script>