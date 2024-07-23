<?php
	include("../configuracion/conectar.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$facturaWs  = '';

	// CONSULTAR SI TIENE FACTURAS GENERADAS POR EL WEB SERVICE
	$sql = "SELECT COUNT(id) AS cont FROM ventas_facturas
					WHERE activo=1
					AND id_empresa=$id_empresa
					AND estado=1
					AND id_saldo_inicial=0
					AND tipo='Ws'
					AND id NOT IN(SELECT id_factura_venta FROM ventas_facturas_inventario WHERE activo=1)";
	$query = mysql_query($sql,$link);
	$cont  = mysql_result($query,0,'cont');

	if($cont > 0){
		$facturaWs = "{
										closable	  : false,
										autoScroll	: true,
										title		    : 'Factura de Venta Ws',
										iconCls 	  : '',
										disabled    : '$permiso_factura',
										bodyStyle 	: 'background-color:#FFF;',
										handler     : function(){ shortCuts(event); },
										items		    :
										[
											{
												xtype				: 'panel',
												id					: 'contenedor_FvCuentas',
												border		  : false,
												bodyStyle 	: 'background-color: $_SESSION[COLOR_FONDO];',
												autoLoad    :	{
																				url			: 'facturacion_cuentas/factura_ventas_cuentas_bloqueada.php',
																				scripts	: true,
																				nocache	: true,
																				params	: { opcGrillaContable : 'FvCuentas' }
																			}
											}
										],
										tbar		    :
										[
											{
												xtype	  : 'buttongroup',
												height  : 80,
	                    	style   : 'border:none;',
												columns	: 8,
												title	  : 'Opciones',
												items	  :
												[
					                {
				                    xtype       : 'button',
				                    width		    : 60,
														height	  	: 56,
				                    text        : 'Buscar',
				                    scale       : 'large',
				                    iconCls     : 'buscar_doc_new',
				                    iconAlign   : 'top',
				                    handler     : function(){ BloqBtn(this); buscarFvCuentas() }
					                },
					                {
				                    xtype       : 'button',
				                    id 			    : 'Btn_cancelar_FvCuentas',
				                    width		    : 60,
														height	  	: 56,
				                    text        : 'Cancelar',
				                    tooltip		  : 'Cancelar Factura',
				                    scale       : 'large',
				                    iconCls     : 'cancel',
				                    iconAlign   : 'top',
				                    disabled 	  : true,
				                    handler     : function(){ cancelarFvCuentas() }
					                }
						            ]
							        },'-',
							        {
		                    xtype   : 'buttongroup',
		                    height  : 80,
		                    id      : 'BtnGroup_Estado1_FvCuentas',
		                    columns : 4,
		                    title   : 'Documento Generado',
		                    items   :
							          [
									        {
														xtype		  : 'button',
														width		  : 60,
														height	  : 56,
														id			  : 'btnExportar_FvCuentas',
														text		  : 'Imprimir',
														tooltip	  : 'Imprimir en un documento PDF',
														scale		  : 'large',
														iconCls		: 'pdf32_new',
														iconAlign	: 'top',
									          disabled 	: true,
														handler 	: function(){ BloqBtn(this); imprimirFvCuentas(); },
														// menu		:
														// [
														// 	{
														// 		text	: '<b>Imprimir en Excel</b>',
														// 		iconCls	: 'xls16',
														// 		handler	: function(){ BloqBtn(this); imprimirFvCuentasExcel(); }
														// 	}
														// ]
													}
												]
											},'->',
			                {
												xtype : 'tbtext',
												text  : '<div id=\"titleDocumentoFvCuentas\" style=\"text-align:center; font-size:18px; font-weight:bold;\"></div>',
												scale : 'large',
			                }
										]
									},";
	}

	// CONSULTAR SI TIENE FACTURAS ELECTRONICAS GENERADAS
	$sql = "SELECT COUNT(VF.id) as cont
					FROM ventas_facturas AS VF
					LEFT JOIN	ventas_facturas_configuracion AS VFC
					ON VF.id_configuracion_resolucion = VFC.id
					WHERE	VF.activo = 1
					AND VF.id_empresa = $id_empresa
					AND (VF.estado != 0 OR VF.estado != 3)
					AND VFC.tipo = 'FE'
					AND VF.numero_factura_completo != ''";
	$query = $mysql->query($sql,$mysql->link);
	$cont  = $mysql->result($query,0,'cont');

	if($cont > 0){
		$facturaE =  "{
										closable	  : false,
										autoScroll	: true,
										title		    : 'Factura Elect.',
										iconCls 	  : '',
										disabled    : false,
										bodyStyle 	: 'background-color:#FFF;',
										handler     : function(){ shortCuts(event); },
										items       :
										[
											{
												xtype     : 'panel',
												id        : 'contenedor_FacturaElectronica',
												border    : false,
												bodyStyle : 'background-color:<?php echo $_SESSION[COLOR_FONDO] ?>;',
											}
										],
										tbar		    :
										[
											{
												xtype	  : 'buttongroup',
												width   : 360,
												height  : 80,
	                    	style   : 'border:none;',
												columns	: 8,
												title	  : 'Fechas',
												items	  :
												[
													{
														xtype     : 'panel',
														border    : false,
														width     : 220,
														height    : 80,
														bodyStyle : 'background-color:rgba(255,255,255,0)',
														autoLoad  :
														{
															url		  : '../funciones_globales/filtros/filtro_fechas.php',
															scripts	: true,
															nocache	: true,
															params  : {
																					opc : 'FacturaElectronica'
																				}
														},
														handler   : function(){}
													},
												]
											},'-',
											{
												xtype	  : 'buttongroup',
												width   : 100,
												height  : 80,
	                    	style   : 'border:none;',
												columns	: 8,
												title	  : 'Estado',
												items	  :
												[
													{
														xtype     : 'panel',
														border    : false,
														width     : 170,
														height    : 80,
														bodyStyle : 'background-color:rgba(255,255,255,0)',
														html      : '<select class=\"myfield\" style=\"margin-top:16px;\" id=\"estado_FacturaElectronica\"><option value=\"todo\">Todo</option><option value=\"C05F87B3-ABF6-475B-80B4-1520376E4531\">Exitoso</option><option value=\"EBF6D2FE-9AD5-4728-B0BA-4846E3F89147\">Enviado Con Novedad</option><option value=\"C3AF2C4B-2F30-46C1-990B-4A0CEA8A3661\">En Proceso De Validacion</option><option value=\"94533357-4061-4C0E-BD3B-A853652EEF66\">Sin Aprobacion</option></select>',
														handler   : function(){}
													},
												]
											},'-',
											{
												xtype	  : 'buttongroup',
												width   : 100,
												height  : 80,
	                    	style   : 'border:none;',
												columns	: 8,
												title	  : 'Notificaciones',
												items	  :
												[
													{
														xtype     : 'panel',
														border    : false,
														width     : 100,
														height    : 80,
														bodyStyle : 'background-color:rgba(255,255,255,0)',
														html      : '<select class=\"myfield\" style=\"margin-top:16px;\" id=\"notificaciones_FacturaElectronica\"><option value=\"no\">No</option><option value=\"si\">Si</option></select>',
														handler   : function(){}
													},
												]
											},'-',
											{
												xtype	  : 'buttongroup',
												height  : 80,
	                    	style   : 'border:none;',
												columns	: 8,
												title	  : 'Opciones',
												items	  :
												[
													{
				                    xtype       : 'button',
				                    width		    : 60,
														height	  	: 56,
				                    text        : 'Buscar',
				                    scale       : 'large',
				                    iconCls     : 'buscar_doc_new',
				                    iconAlign   : 'top',
				                    handler     : function(){ BloqBtn(this); buscarFVElectronica() }
					                },
													{
				                    xtype       : 'button',
				                    width		    : 60,
														height	  	: 56,
				                    text        : 'Reenviar Dian',
				                    scale       : 'large',
				                    iconCls     : 'envia_doc',
				                    iconAlign   : 'top',
				                    handler     : function(){ BloqBtn(this); reenviarDianFacturaElectronica() }
					                },
													{
				                    xtype       : 'button',
				                    width		    : 60,
														height	  	: 56,
				                    text        : 'Reenviar Cliente',
				                    scale       : 'large',
				                    iconCls     : 'enviar',
				                    iconAlign   : 'top',
				                    handler     : function(){ BloqBtn(this); reenviarClienteFacturaElectronica() }
					                },
						            ]
							        },
			                {
												xtype : 'tbtext',
												text  : '<div id=\"titleDocumentoFvCuentas\" style=\"text-align:center; font-size:18px; font-weight:bold;\"></div>',
												scale : 'large',
			                }
										]
									},";
	}

	$acumScript .= (user_permisos(38,'false') == 'true')? 'Ext.getCmp("Btn_guardar_factura_compra").enable();' : '';

	$permiso_cotizacion  = (user_permisos(5,'false') == 'true')? 'false' : 'true';
	$permiso_pedido      = (user_permisos(10,'false') == 'true')? 'false' : 'true';
	$permiso_remision    = (user_permisos(15,'false') == 'true')? 'false' : 'true';
	$permiso_factura     = (user_permisos(20,'false') == 'true')? 'false' : 'true';
	$permiso_recibo_caja = (user_permisos(26,'false') == 'true')? 'false' : 'true';
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
	var id_cliente_CotizacionVenta = 0
	,	id_cliente_PedidoVenta       = 0
	,	id_cliente_FacturaVenta      = 0
	,	id_cliente_RemisionesVenta   = 0;

	Ext.QuickTips.init();
	Ext.onReady
	(function()
		{
			new Ext.Viewport //TAB PRINCIPAL
			(
				{
				layout : 'border',
				style  : 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
				items  :
					[
						// {
						// 	region			: 'north',
						// 	xtype				: 'panel',
						// 	height			: 33,
						// 	border			: false,
						// 	margins			: '0 0 0 0',
						// 	html				: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
						// 	bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
						// },
						{
							region					: 'center',
							xtype						: 'tabpanel',
							margins					: '0 0 0 0',
							activeTab				: 0,
							deferredRender	: true,
							border					: false,
							bodyStyle 			: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							items						:
							[
								//======================== DASHBOARD==========================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Dashboard',
									iconCls 	: '',
									disabled    : <?php echo $permiso_cotizacion; ?>,
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
								//======================= COTIZACION =========================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Cotizaciones',
									iconCls 	: '',
									disabled    : <?php echo $permiso_cotizacion; ?>,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_CotizacionVenta',
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
															opc           : "CotizacionVenta",
															imprimeVarPhp : 'opcGrillaContable : "CotizacionVenta"',
															renderizaBody : 'true',
															url_render    : 'cotizacion/grillaContable.php',
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_CotizacionVenta',
											height  : 80,
                    						style   : 'border:none;',
											columns	: 1,
											title	: 'Generar',
											items	:
											[
												{
								                    xtype       : 'button',
								                    id     		: 'Btn_guardar_CotizacionVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Guardar',
								                    tooltip		: 'Genera Cotizacion',
								                    scale       : 'large',
								                    disabled 	: true,
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); guardarCotizacionVenta() }
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
													id 			: 'btnNuevaCotizacionVenta',
								                    text        : 'Nuevo',
								                    tooltip		: 'Nueva Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'add_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); nuevaCotizacionVenta() }
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
								                    handler     : function(){ BloqBtn(this); buscarCotizacionVenta() }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_CotizacionVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Eliminar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarCotizacionVenta() }
								                }
								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_CotizacionVenta',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
												{
								                    xtype       : 'button',
								                    id			: 'btnExportarCotizacionVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Imprimir',
								                    tooltip		: 'Imprimir en un documento PDF',
								                    scale       : 'large',
								                    iconCls     : 'pdf32_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); imprimirCotizacionVenta(); },
												    // menu		:
												    // [
													// 	{
													// 		text	: '<b>Imprimir en Excel</b>',
													// 		iconCls	: 'xls16',
													// 		handler	: function(){ BloqBtn(this); imprimirCotizacionVentaExcel(); }
													// 	}
													// ]
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_CotizacionVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoCotizacionVenta(); }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_CotizacionVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarCotizacionVenta(); }
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_enviar_correo_CotizacionVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Enviar',
								                    tooltip		: 'Enviar Cotizacion por email',
								                    scale       : 'large',
								                    iconCls     : 'enviar',
								                    iconAlign   : 'top',
								                    disabled	: false,
								                    handler     : function(){ BloqBtn(this); ventanaEnviarCorreo_CotizacionVenta(); }
								                }
											]
										},'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleDocumentoCotizacionVenta" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								},
								//========================= PEDIDO ===========================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Pedido',
									iconCls 	: '',
									disabled    : <?php echo $permiso_pedido; ?>,
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
                    //	style   : 'border:none;',
										// 	columns	: 1,
										// 	title		: 'Importar',
										// 	items		:
										// 	[
										// 		{
								  	//      xtype       : 'button',
								  	//      id     		  : 'btnCargarPedidoVenta',
								  	//      width			  : 60,
										//			height			: 56,
								  	//      text        : 'Cotizacion',
								  	//      tooltip		  : 'Cargar Cotizacion',
								  	//      scale       : 'large',
								  	//      disabled 	  : false,
								  	//      iconCls     : 'carga_doc',
								  	//      iconAlign   : 'top',
								  	//      handler     : function(){ BloqBtn(this); ventanaBuscarCotizacionPedidoPedidoVenta() }
								  	//    }
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
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_enviar_correo_PedidoVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Enviar',
								                    tooltip		: 'Enviar Pedido por email',
								                    scale       : 'large',
								                    iconCls     : 'enviar',
								                    iconAlign   : 'top',
								                    disabled	: false,
								                    handler     : function(){ BloqBtn(this); ventanaEnviarCorreo_PedidoVenta(); }
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
								//======================= REMISIONES =========================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Remisiones',
									iconCls 	: '',
									disabled    : <?php echo $permiso_remision; ?>,
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
								                },
												{
								                    xtype       : 'button',
								                    id 			: 'Btn_enviar_correo_RemisionesVenta',
								                    width		: 60,
													height		: 56,
								                    text        : 'Enviar',
								                    tooltip		: 'Enviar Remision por email',
								                    scale       : 'large',
								                    iconCls     : 'enviar',
								                    iconAlign   : 'top',
								                    disabled	: false,
								                    handler     : function(){ BloqBtn(this); ventanaEnviarCorreo_RemisionesVenta(); }
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
								//======================== FACTURAS ==========================//
								{
									closable   : false,
									autoScroll : true,
									title      : 'Factura de Venta',
									iconCls    : '',
									disabled   : <?php echo $permiso_factura; ?>,
									bodyStyle  : 'background-color:#FFF;',
									handler    : function(){ shortCuts(event); },
									items      :
									[
										{
											xtype     : "panel",
											id        : 'contenedor_FacturaVenta',
											border    : false,
											bodyStyle : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										}
									],
									tbar			 : [
																	{
																		xtype		: 'buttongroup',
																		columns	: 3,
																		title		: 'Filtro Bodega',
																		items		:
																		[
																			{
																				xtype     : 'panel',
																				border    : false,
																				width     : 160,
																				height    : 56,
																				bodyStyle : 'background-color:rgba(255,255,255,0)',
																				autoLoad  :
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
																				xtype     : 'panel',
																				border    : false,
																				width     : 155,
																				height    : 56,
																				bodyStyle : 'background-color:rgba(255,255,255,0)',
																				autoLoad  :
																				{
																					url     : 'bd/bd.php',
																					scripts : true,
																					nocache : true,
																					params  :
																					{
																						opc               : 'cargarCampoCotizacionPedido',
																						opcGrillaContable : 'FacturaVenta'
																					}
																				}
																			}
																		]
																	},
																	{
																		xtype   : 'buttongroup',
																		id      : 'BtnGroup_Guardar_FacturaVenta',
																		height  : 80,
																		style   : 'border:none;',
																		columns : 1,
																		title   : 'Contabilizar',
																		items   : [
																								{
																									xtype     : 'button',
																									width     : 60,
																									height    : 56,
																									text      : 'Guardar',
																									tooltip   : 'Genera Factura de Venta',
																									id        : 'Btn_guardar_FacturaVenta',
																									scale     : 'large',
																									iconCls   : 'guardar',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); guardarFacturaVenta() }
																                }
															            		]
																	},
																	{
																		xtype   : 'buttongroup',
																		height  : 83,
																		style   : 'border:none;',
																		columns : 4,
																		title   : 'Opciones',
																		items   :	[
																								{
																									xtype     : 'button',
																									width     : 60,
																									height    : 56,
																									text      : 'Nueva',
																									scale     : 'large',
																									iconCls   : 'add_new',
																									id        : 'btnNuevaFacturaVenta',
																									iconAlign : 'top',
																									disabled  : true,
																									handler   : function(){ BloqBtn(this); nuevaFacturaVenta() }
																								},
															                	{
																									xtype     : 'button',
																									width     : 60,
																									height    : 56,
																									text      : 'Buscar',
																									scale     : 'large',
																									iconCls   : 'buscar_doc_new',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); buscarFacturaVenta() }
																                },
															                	{
																									xtype     : 'button',
																									id        : 'Btn_itemsGrupos_FacturaVenta',
																									width     : 60,
																									height    : 60,
																									text      : 'Nuevo Grupo <br>de Items',
																									tooltip   : 'Agregar Grupos de Items',
																									scale     : 'large',
																									iconCls   : 'btnGroups',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); ventanaAgregarAgrupacionItems() }
																                },
															                	{
																									xtype     : 'button',
																									id        : 'Btn_cancelar_FacturaVenta',
																									width     : 60,
																									height    : 56,
																									text      : 'Cancelar',
																									tooltip   : 'Cancelar Factura',
																									scale     : 'large',
																									iconCls   : 'cancel',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); cancelarFacturaVenta() }
															                	}
															      					]
												          },'-',
								                	{
								                    xtype   : 'buttongroup',
								                    height  : 80,
								                    id      : 'BtnGroup_Estado1_FacturaVenta',
								                    columns : 6,
								                    title   : 'Documento Generado',
								                    items   : [
																              	{
																									xtype     : 'splitbutton',
																									width     : 60,
																									height    : 56,
																									id        : 'btnExportarFacturaVenta',
																									text      : 'Imprimir',
																									tooltip   : 'Imprimir en un documento PDF',
																									scale     : 'large',
																									iconCls   : 'pdf32_new',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); imprimirFacturaVenta();},
																									menu      : [
																																{
																																	text    : '<b>XML factura electronica </b>',
																																	iconCls : 'xml16',
																																	handler : function(){ BloqBtn(this); imprimirXmlFacturaVenta(); }
																																}
																															]
																								},
																								{
																									xtype     : 'button',
																									id        : 'Btn_editar_FacturaVenta',
																									width     : 60,
																									height    : 56,
																									text      : 'Editar',
																									tooltip   : 'Editar Factura',
																									scale     : 'large',
																									iconCls   : 'edit',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); modificarDocumentoFacturaVenta(); }
														              			},
																								{
																									xtype     : 'button',
																									id        : 'Btn_restaurar_FacturaVenta',
																									width     : 60,
																									height    : 56,
																									text      : 'Restaurar',
																									tooltip   : 'Restaurar Factura',
																									scale     : 'large',
																									iconCls   : 'restaurar32',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); restaurarFacturaVenta(); }
																              	},
																								{
																									xtype     : 'button',
																									id        : 'Btn_upload_FacturaVenta',
																									width     : 60,
																									height    : 56,
																									text      : 'Anexar',
																									disabled  : true,
																									tooltip   : 'Anexar documento',
																									scale     : 'large',
																									iconCls   : 'upload_file32',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); anexaFacturaVenta(); }
																								},
																								{
																									xtype     : 'button',
																									id        : 'Btn_enviar_correo_FacturaVenta',
																									width     : 60,
																									height    : 56,
																									text      : 'Enviar',
																									tooltip   : 'Enviar Factura de Venta por email',
																									scale     : 'large',
																									iconCls   : 'enviar',
																									iconAlign : 'top',
																									disabled  : false,
																									handler   : function(){ BloqBtn(this); ventanaEnviarCorreo_FacturaVenta(); }
																				        },
																								{
																									xtype     : 'button',
																									id        : 'Btn_enviar_factura_electronica_FacturaVenta',
																									width     : 60,
																									height    : 56,
																									text      : 'Enviar a la DIAN',
																									tooltip   : 'Enviar Factura de Venta a la DIAN',
																									scale     : 'large',
																									iconCls   : 'envia_doc',
																									iconAlign : 'top',
																									disabled  : false,
																									handler   : function(){ BloqBtn(this); enviarDIANFacturaVenta(); }
																	              }
																							]
																	},'->',
							                    {
																		xtype : "tbtext",
																		text  : '<div id="titleDocumentoFacturaVenta" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
																		scale : "large",
							                    },'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleRes" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
																]
								},
								<?php echo $facturaWs; ?>
								<?php echo $facturaE; ?>
								//==================== RECIBOS DE CAJA =======================//
								{
									closable   : false,
									autoScroll : true,
									title      : 'Recibos de Caja',
									iconCls    : '',
									disabled   : <?php echo $permiso_recibo_caja; ?>,
									bodyStyle  : 'background-color:#FFF;',
									items      :  [
																	{
																		xtype     : "panel",
																		id        : 'contenedor_ReciboCaja',
																		border    : false,
																		bodyStyle : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
																		autoLoad  :	{
																									url     : 'recibo_caja/grilla/grillaContable.php',
																									scripts : true,
																									nocache : true,
																									params  : {
																															opcGrillaContable : 'ReciboCaja'
																														}
																								}
																	}
																],
									tbar			 :	[
																	{
																		xtype   : 'buttongroup',
																		id      : 'BtnGroup_Guardar_ReciboCaja',
																		height  : 80,
																		style   : 'border:none;',
																		columns : 1,
																		title   : 'Contabilizar',
																		items   :	[
																								{
																									xtype     : 'button',
																									width     : 60,
																									height    : 56,
																									text      : 'Guardar',
																									tooltip   : 'Genera Recibo de Caja',
																									id        : 'Btn_guardar_ReciboCaja',
																									scale     : 'large',
																									iconCls   : 'guardar',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); guardarReciboCaja() }
																		        		}
																	      			]
																	},
																	{
																		xtype   : 'buttongroup',
																		height  : 80,
																		style   : 'border:none;',
																		columns : 8,
																		title   : 'Opciones',
																		items   :	[
																								{
																									xtype     : 'button',
																									width     : 60,
																									height    : 56,
																									text      : 'Nueva',
																									scale     : 'large',
																									iconCls   : 'add_new',
																									id        : 'btnNuevaReciboCaja',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); nuevaReciboCaja() }
																								},
												                				{
																									xtype     : 'button',
																									width     : 60,
																									height    : 56,
																									text      : 'Buscar',
																									scale     : 'large',
																									iconCls   : 'buscar_doc_new',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); buscarReciboCaja() }
											                					},
												                				{
																									xtype     : 'button',
																									id        : 'Btn_cancelar_ReciboCaja',
																									width     : 60,
																									height    : 56,
																									text      : 'Cancelar',
																									tooltip   : 'Cancelar Recibo',
																									scale     : 'large',
																									iconCls   : 'cancel',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); cancelarReciboCaja() }
												                				}
															      					]
													      	},'-',
											        		{
																		xtype   : 'buttongroup',
																		height  : 80,
																		id      : 'BtnGroup_Estado1_ReciboCaja',
																		columns : 4,
																		title   : 'Documento Generado',
																		items   : [
															                	{
																									xtype     : 'splitbutton',
																									id        : 'btnExportarReciboCaja',
																									width     : 60,
																									height    : 56,
																									text      : 'Imprimir',
																									tooltip   : 'Imprimir en un documento PDF',
																									scale     : 'large',
																									iconCls   : 'pdf32_new',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); imprimirReciboCaja(); },
																									menu      : [
																																{
																																	text		: '<b>Imprimir en Excel</b>',
																																	iconCls	: 'xls16',
																																	handler	: function(){ BloqBtn(this); imprimirReciboCajaExcel(); }
																																}
																															]
															                	},
																								{
																									xtype     : 'button',
																									id        : 'Btn_editar_ReciboCaja',
																									width     : 60,
																									height    : 56,
																									text      : 'Editar',
																									tooltip   : 'Editar Recibo',
																									scale     : 'large',
																									iconCls   : 'edit',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); modificarDocumentoReciboCaja(); }
																			          },
																								{
																									xtype     : 'button',
																									id        : 'Btn_restaurar_ReciboCaja',
																									width     : 60,
																									height    : 56,
																									text      : 'Restaurar',
																									tooltip   : 'Restaurar Recibo',
																									scale     : 'large',
																									iconCls   : 'restaurar32',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); restaurarReciboCaja(); }
																			          },
																								{
																									xtype     : 'button',
																									id        : 'Btn_upload_ReciboCaja',
																									width     : 60,
																									height    : 56,
																									text      : 'Anexar',
																									disabled  : true,
																									tooltip   : 'Anexar documento',
																									scale     : 'large',
																									iconCls   : 'upload_file32',
																									iconAlign : 'top',
																									handler   : function(){ BloqBtn(this); anexaReciboCaja(); }
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

	//======================== NUEVA COTIZACION DE VENTA =======================//
	function nuevaCotizacionVenta(){
		Ext.get("contenedor_CotizacionVenta").load({
			url     : "cotizacion/grillaContable.php",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_bodega     : document.getElementById('filtro_ubicacion_CotizacionVenta').value,
				opcGrillaContable : 'CotizacionVenta',
			}
		});
		Ext.getCmp('btnNuevaCotizacionVenta').disable();
		document.getElementById('titleDocumentoCotizacionVenta').innerHTML=''
	}
	//========================== NUEVO PEDIDO DE VENTA =========================//
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
	//============================= NUEVA REMISION =============================//
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
	//========================= NUEVA FACTURA DE VENTA =========================//
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
	//============================= NUEVA REMISION =============================//
	function nuevaReciboCaja(){
		Ext.get("contenedor_ReciboCaja").load({
			url     : 'recibo_caja/grilla/grillaContable.php',
			scripts : true,
			nocache : true,
			params  : {
									opcGrillaContable : 'ReciboCaja'
								}
		});

		document.getElementById('titleDocumentoReciboCaja').innerHTML='';
		Ext.getCmp('Btn_guardar_ReciboCaja').enable();
	}
	//=================== CARGAR FACTURA DE VENTA AL GUARDAR ===================//
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
	//================== BUSCAR FACTURAS DE VENTAS POR CUENTAS =================//
	function buscarFvCuentas(){

		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_BuscarFvCuentas = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_BuscarFvCuentas',
		    title       : 'Buscar Facturas de Ventas por Cuentas',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		      url     : 'facturacion_cuentas/bd/buscar_factura_cuentas.php',
		      scripts : true,
		      nocache : true,
		      params  : { opcGrillaContable: 'FvCuentas' }
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
                handler     : function(){ BloqBtn(this); Win_Ventana_BuscarFvCuentas.close(id) }
              }
            ]
	        }
		    ]
		}).show();
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
	//================== BUSCAR FACTURAS SINCRONIZADAS DE SIHO =================//
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
		    autoLoad    : {
								        url     : 'bd/buscarFacturasWs.php',
								        scripts : true,
								        nocache : true,
								        params  :
								        {
								          opcGrillaContable : 'FacturaVenta',
								          var2              : 'var2',
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
					                    handler     : function(){ BloqBtn(this); Win_Ventana_ventana_buscar_facturas_ws.close(id) }
						                }
							            ]
		        						}
		    							]
		}).show();
	}
	//========== ACTUALIZAR LA FILA DE LA VENTANA DEL DOCUMENTO CRUCE ==========//
	function actualiza_fila_ventana_busqueda_doc_cruce(id,evento){
	    var div    = '';
	    var divImg = '';
	    var divEvt = '';

	    // MOSTRAR LA FILA DE LA VENTANA DEL DOCUMENTO CRUCE COMO ELIMINADO
	    if (document.getElementById("div_grillaCotizacionFactura_consecutivo_"+id)) {
				div    = document.getElementById('item_grillaCotizacionFactura_'+id);
				divImg = document.getElementById('MuestraToltip_grillaCotizacionFactura_'+id);
				divEvt = document.getElementById('MuestraToltip_General_grillaCotizacionFactura_'+id);
	    } else if(document.getElementById("div_grillaPedidoFactura_consecutivo_"+id)) {
				div    = document.getElementById('item_grillaPedidoFactura_'+id);
				divImg = document.getElementById('MuestraToltip_grillaPedidoFactura_'+id);
				divEvt = document.getElementById('MuestraToltip_General_grillaPedidoFactura_'+id);
	    } else if(document.getElementById("div_grillaRemisionFactura_consecutivo_"+id)) {
				div    = document.getElementById('item_grillaRemisionFactura_'+id);
				divImg = document.getElementById('MuestraToltip_grillaRemisionFactura_'+id);
				divEvt = document.getElementById('MuestraToltip_General_grillaRemisionFactura_'+id);
	    } else if(document.getElementById("div_grillaCotizacionRemision_consecutivo_"+id)) {
				div    = document.getElementById('item_grillaCotizacionRemision_'+id);
				divImg = document.getElementById('MuestraToltip_grillaCotizacionRemision_'+id);
				divEvt = document.getElementById('MuestraToltip_General_grillaCotizacionRemision_'+id);
	    } else if(document.getElementById("div_grillaPedidoRemision_consecutivo_"+id)) {
				div    = document.getElementById('item_grillaPedidoRemision_'+id);
				divImg = document.getElementById('MuestraToltip_grillaPedidoRemision_'+id);
				divEvt = document.getElementById('MuestraToltip_General_grillaPedidoRemision_'+id);
	    } else if(document.getElementById("div_ReciboCaja_nit_"+id)) {
				div    = document.getElementById('item_ReciboCaja_'+id);
				divImg = document.getElementById('MuestraToltip_ReciboCaja_'+id);
				divEvt = document.getElementById('MuestraToltip_General_ReciboCaja_'+id);
	    }

			if (evento=='fail'){
	    	if (div) {
	    		div.setAttribute('style',div.getAttribute('style')+';color:#999 !important;font-style:italic;background-color:#FDDADA !important;');
	      }
	      if (divEvt) {
	      	divEvt.setAttribute('ondblclick','');
	      }
	    	if (divImg) {
	    		divImg.setAttribute('style',divImg.getAttribute('style')+'background-image:url(../nomina/img/false.png);background-repeat: no-repeat;background-position-x: 15px;');
	    	}
	    } else{
	    	if (div) {
	    		div.setAttribute('style',div.getAttribute('style')+';color:#999 !important;font-style:italic;background-color:#e5ffe5 !important;');
	      }
	      if (divEvt) {
	      	divEvt.setAttribute('ondblclick','');
	      }
	    	if (divImg) {
	    		divImg.setAttribute('style',divImg.getAttribute('style')+'background-image:url(../nomina/img/true.png);background-repeat: no-repeat;background-position-x: 15px;');
	    	}
	    }
	}
	// MOSTRAR U OCULTAR LOS INGREDIENTES DE UNA RECETA
	var showHiddenIngredients = (id_elemento) => {
		var grupo = document.getElementById(id_elemento);
		// console.log(grupo.getAttribute('class'));
        if (grupo.getAttribute('class')=="divIngredientes") { grupo.setAttribute('class','divIngredientes showIngredients'); }
        else{ grupo.setAttribute('class','divIngredientes'); }
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
	//========================= BUSCAR FV ELECTRONICAS =========================//
	function buscarFVElectronica(){
		var fecha_inicio = document.getElementById('fecha_inicio_FacturaElectronica').value
		, fecha_fin      = document.getElementById('fecha_fin_FacturaElectronica').value
		, estado         = document.getElementById('estado_FacturaElectronica').value
		, notificaciones = document.getElementById('notificaciones_FacturaElectronica').value;

		Ext.get("contenedor_FacturaElectronica").load({
			url     : 'facturacion_electronica/bd/bd.php',
			scripts : true,
			nocache : true,
			params  : {
									opc            : 'buscarFacturaElectronica',
									fecha_inicio   : fecha_inicio,
									fecha_fin      : fecha_fin,
									estado         : estado,
									notificaciones : notificaciones
								}
		});

		localStorage.fecha_inicio_FacturaElectronica   = fecha_inicio;
		localStorage.fecha_fin_FacturaElectronica      = fecha_fin;
	}
	//======================== REENVIAR FV ELECTRONICAS ========================//
	function reenviarDianFacturaElectronica(){
		cont = 0;
		checkboxes = document.getElementsByClassName('documentosElectronicos');
		documentos = '';

		for(var x = 0; x < checkboxes.length; x++){
		  if(checkboxes[x].checked){
		  	cont = cont + 1;
				documentos += checkboxes[x].value + ",";
		  }
		}

		if(cont == 0){
			alert('No existen documentos seleccionados para reenviar.');
			return;
		}

		if(confirm('\u00BFEstas seguro de reenviar los documentos seleccionados?')){
			cargando_documentos('Enviando Documentos...','');

			Ext.Ajax.request({
				url     : 'facturacion_electronica/bd/bd.php',
				scripts : true,
				nocache : true,
				params  : {
										opc          : 'reenviarDianFacturaElectronica',
										documentos   : documentos
									},
				success : function(result,request){
										alert(result.responseText);
										document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
									},
				failure : function(){
										alert('Error de conexion con el servidor');
										document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
									}
			});
		}
		else{
			return;
		}
	}
	//======================== REENVIAR FV ELECTRONICAS ========================//
	function reenviarClienteFacturaElectronica(){
		cont = 0;
		checkboxes = document.getElementsByClassName('documentosElectronicos');
		documentos = '';

		for(var x = 0; x < checkboxes.length; x++){
		  if(checkboxes[x].checked){
		  	cont = cont + 1;
				documentos += checkboxes[x].value + ",";
		  }
		}

		if(cont == 0){
			alert('No existen documentos seleccionados para reenviar.');
			return;
		}

		if(confirm('\u00BFEstas seguro de reenviar los documentos seleccionados?')){
			cargando_documentos('Enviando Documentos...','');

			Ext.Ajax.request({
				url     : 'facturacion_electronica/bd/bd.php',
				scripts : true,
				nocache : true,
				timeout: 300000,
				params  : {
										opc              : 'reenviarClienteFacturaElectronica',
										numeroDocumentos : documentos
									},
				success : function(result,request){
										alert(result.responseText);
										document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
									},
				failure : function(){
										alert('Error de conexion con el servidor');
										document.getElementById('modal').parentNode.parentNode.removeChild(document.getElementById('modal').parentNode);
									}
			});
		}
		else{
			return;
		}
	}
</script>
