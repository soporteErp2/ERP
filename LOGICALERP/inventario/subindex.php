<?php
	include("../configuracion/conectar.php");
	$permiso_inventario        = (user_permisos(90,'false') == 'true')? 'false' : 'true';
	$permiso_ajuste_inventario = (user_permisos(219,'false') == 'true')? 'false' : 'true';
	$permiso_traslado          = (user_permisos(239,'false') == 'true')? 'false' : 'true';

 ?>

 <div id="divPadreModalUploadFile" class="fondo_modal_upload_file">
    <div>
        <div>
            <div>
                <div id="div_upload_file">
                    <div>Arrastre el archivo excel o csv.</div>
                </div>
                <div class="btn_div_upload_file2" style="margin-left:350px;" onclick="imagenAyudaModal()">?</div>
                <div class="btn_div_upload_file2" onclick="close_ventana_upload_file()">X</div>
            </div>
        </div>
    </div>
</div>

<script>
	//////////////////////////////////   VARIABLES PARA LOS INFORMES  /////////////////////////////////////////
	var my_fecha_desde      = '<?php $fecha = date("Y-m-d"); echo date("Y-m-d", strtotime("$fecha -5 day")); ?>'
	,	my_fecha_hasta      = '<?php $fecha = date("Y-m-d"); echo $fecha; ?>'
	// ,	Tam                 = parent.TamVentana()
	// ,	myancho             = Tam[0]
	// ,	myalto              = Tam[1];
	apuntador_este_gridraro = 2;

	Ext.QuickTips.init();
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////

	var id_activo
	,	filtro_empresa
	,	filtro_sucursal
	,	filtro_ubicacion;

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
							deferredRender	: true,
							border			: false,
							activeTab		: 0,
							bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							items			:
							[
		//============================================= PESTAÑA INVENTARIO =============================================//
								{
									closable   : false,
									autoScroll : false,
									title      : 'Inventario',
									iconCls    : 'inventario16',
									disabled   : <?php echo $permiso_inventario; ?>,
									bodyStyle  : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
									items      :
									[
										{
											xtype		: "panel",
											id			: 'contenedor_inventario',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;'

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
															opc           : 'inventario',
															renderizaBody : 'true',
															url_render    : 'inventario_unidades/inventario.php',
														}
													}
												}
											]
										},
										{
											xtype   : 'buttongroup',
											columns : 3,
											title   : 'Herramientas',
											items   :
											[
												{
													text		: 'Impresion en Lote</br /> Codigo de Barras',
													scale		: 'small',
													iconCls		: 'barcode16',
													iconAlign	: 'top',
													handler		: function(){nueva_ventana_Grupo_codigo_barras("varios_codigos_barras");}
												}
											]
										},
										{
											xtype   : 'buttongroup',
											columns : 3,
											title   : 'Informes',
											items   :
											[
												{
													text		: '<br>Exportar Inventario',
													scale		: 'small',
													iconCls		: 'xls32',
													iconAlign	: 'top',
													handler		: function(){ventanaConfigurarInforme();}
												}
											]
										},'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleInventario" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								},
		
		//============================================= PESTAÑA TRASLADOS =============================================//
								{
									closable   : false,
									autoScroll : true,
									title      : 'Traslados',
									iconCls    : '',
									disabled   : <?php echo $permiso_traslado; ?>,
									bodyStyle  : 'background-color:#FFF;height:100%;',
									items      :	[
														{
															xtype     : "panel",
															id        : 'contenedor_Traslados',
															border    : false,
															bodyStyle : 'background-color:#FFF;',
														}
																],
									tbar 		:	[
														{
															xtype   : 'buttongroup',
															columns : 3,
															title   : 'Ubicacion De Origen',
															id      : 'panel_filtro_sucursal_Traslados',
															items   : [
																			{
																				xtype     : 'panel',
																				border    : false,
																				width     : 260,
																				height    : 55,
																				bodyStyle : 'background-color:rgba(255,255,255,0)',
																				autoLoad  :	{
																								url     : 'filtros/filtros_sucursal.php',
																								scripts : true,
																								nocache : true,
																								params  :
																										{
																											opc           : 'Traslados',
																											imprimeVarPhp : 'opcGrillaContable:"Traslados",',
																											url_render    : 'traslados/grillaContable'
																										}
																							}
																			}
																		]
														},

														{
															xtype   : 'buttongroup',
															id      : 'BtnGroup_Guardar_Traslados',
															height  : 80,
															style   : 'border:none;',
															columns : 1,
															title   : 'Generar',
															items   :	[
																			{
																				xtype     : 'button',
																				id        : 'Btn_guardar_Traslados',
																				width     : 60,
																				height    : 56,
																				text      : 'Guardar',
																				tooltip   : 'Genera Traslado',
																				scale     : 'large',
																				disabled  : true,
																				iconCls   : 'guardar',
																				iconAlign : 'top',
																				handler   : function(){ BloqBtn(this); guardarTraslados(); }
														                	}
																      	]
												    	},
														{
															xtype   : 'buttongroup',
															height  : 80,
															style   : 'border:none;',
															columns : 7,
															title   : 'Opciones',
															items   :	[
																			{
																				xtype     : 'button',
																				width     : 60,
																				height    : 56,
																				id        : 'btnNuevaTraslados',
																				text      : 'Nuevo',
																				tooltip   : 'Nuevo Traslado',
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
																				id        : 'Btn_cancelar_Traslados',
																				width     : 60,
																				height    : 56,
																				text      : 'Cancelar',
																				tooltip   : 'Eliminar Traslado',
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
																			xtype     : 'button',
																			id        : 'btnExportarTraslados',
																			width     : 60,
																			height    : 56,
																			text      : 'Imprimir',
																			tooltip   : 'Imprimir en un documento PDF',
																			scale     : 'large',
																			iconCls   : 'pdf32_new',
																			iconAlign : 'top',
																			handler   : function(){ BloqBtn(this); imprimirTraslados(); },
														                },
																		{
																			xtype     : 'button',
																			id        : 'Btn_editar_Traslados',
																			width     : 60,
																			height    : 56,
																			text      : 'Editar',
																			tooltip   : 'Editar Traslado',
																			scale     : 'large',
																			iconCls   : 'edit',
																			iconAlign : 'top',
																			handler   : function(){ BloqBtn(this); modificarTraslados(); }
														                },
														                {
																			xtype     : 'button',
																			id        : 'Btn_restaurar_Traslados',
																			width     : 60,
																			height    : 56,
																			text      : 'Restaurar',
																			tooltip   : 'Restaurar Traslado',
																			scale     : 'large',
																			iconCls   : 'restaurar32',
																			iconAlign : 'top',
																			handler   : function(){ BloqBtn(this); restaurarTraslados(); }
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
		//============================================= PESTAÑA AJUSTE DE INVENTARIO =============================================//
								{
									closable	: false,
									autoScroll	: true,
									title		: 'Ajuste de Inventario',
									iconCls 	: '',
									disabled    : <?php echo $permiso_ajuste_inventario; ?>,
									bodyStyle 	: 'background-color:#FFF;',
									items		:
									[
										{
											xtype		: "panel",
											id			: 'contenedor_AjusteInventario',
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
															opc           : 'AjusteInventario',
															imprimeVarPhp : 'opcGrillaContable : "AjusteInventario"',
															renderizaBody : 'true',
															url_render    : 'ajuste_inventario/grillaContable.php',
														}
													}
												}
											]
										},
										{
											xtype	: 'buttongroup',
											id      : 'BtnGroup_Guardar_AjusteInventario',
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
								                    tooltip		: 'Genera Ajuste',
								                    id     		: 'Btn_guardar_AjusteInventario',
								                    scale       : 'large',
								                    iconCls     : 'guardar',
								                    iconAlign   : 'top',
								                    disabled	: false,
								                    handler     : function(){ BloqBtn(this); guardarAjusteInventario() }
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
								                    id 			: 'btnNuevaAjusteInventario',
								                    iconAlign   : 'top',
								                    disabled 	: true,
								                    handler     : function(){ BloqBtn(this); nuevaAjusteInventario() }
												},
								                {
								                    xtype       : 'button',
								                    width		: 60,
													height		: 56,
								                    text        : 'Buscar',
								                    scale       : 'large',
								                    iconCls     : 'buscar_doc_new',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); buscarAjusteInventario() }
								                },
								                {
													xtype       : 'button',
													width       : 60,
													height      : 56,
													id          : 'Btn_load_excel_body_nota',
													text        : 'Cargar Excel',
													tooltip     : 'Cargar Excel',
													scale       : 'large',
													iconCls     : 'upload_file32',
													iconAlign   : 'top',
													handler     : function(){ BloqBtn(this); windows_upload_excel(); }
												},
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_cancelar_AjusteInventario',
								                    width		: 60,
													height		: 56,
								                    text        : 'Cancelar',
								                    tooltip		: 'Cancelar Ajuste',
								                    scale       : 'large',
								                    iconCls     : 'cancel',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); cancelarAjusteInventario() }
								                }
								            ]
						                },'-',
						                {
						                    xtype   : 'buttongroup',
						                    height  : 80,
						                    id      : 'BtnGroup_Estado1_AjusteInventario',
						                    columns : 4,
						                    title   : 'Documento Generado',
						                    items   :
						                    [
								                {
													xtype     : "splitbutton",
													id        : 'btnExportarAjusteInventario',
													tooltip   : 'Imprimir a un documento PDF',
													iconCls   : "pdf32_new",
													scale     : "large",
													iconAlign : 'top',
													text      : 'Imprimir',
													handler   : function(){ BloqBtn(this); imprimirAjusteInventario(); },
										            menu:
										            [
									            		{
															text    : "Imprimir Ajuste entradas",
															iconCls : "pdf16",
															handler : function(){ BloqBtn(this); imprimirEntradasAjusteInventario(); }
									            		},
									            		{
															text    : "Imprimir Ajuste salidas",
															iconCls : "pdf16",
															handler : function(){ BloqBtn(this); imprimirSalidasAjusteInventario(); }
									            		}
										          	]
										        },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_editar_AjusteInventario',
								                    width		: 60,
													height		: 56,
								                    text        : 'Editar',
								                    tooltip		: 'Editar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'edit',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); modificarDocumentoAjusteInventario(); }
								                },
								                {
								                    xtype       : 'button',
								                    id 			: 'Btn_restaurar_AjusteInventario',
								                    width		: 60,
													height		: 56,
								                    text        : 'Restaurar',
								                    tooltip		: 'Restaurar Cotizacion',
								                    scale       : 'large',
								                    iconCls     : 'restaurar32',
								                    iconAlign   : 'top',
								                    handler     : function(){ BloqBtn(this); restaurarAjusteInventario(); }
								                }
											]
										},'->',
		                                {
											xtype : "tbtext",
											text  : '<div id="titleDocumentoAjusteInventario" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
											scale : "large",
		                                }
									]
								},
			//============================================= PESTAÑA INFORMES =============================================//
								{
									closable   : false,
									autoScroll : false,
									title      : 'Informes',
									iconCls    : 'book_open',
									// disabled   : <?php echo $permiso_inventario; ?>,
									bodyStyle  : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
									items      :
									[
										{
											xtype		: "panel",
											id			: 'contenedor_informes',
											border		: false,
											bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
											autoLoad    :
													{
														url		: 'informes/index.php',
														scripts	: true,
														nocache	: true,
														// params	:
														// {
														// 	opc           : 'kardex',
														// 	renderizaBody : 'true',
														// 	url_render    : 'kardex/kardex.php',
														// }
													}
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

	function nuevaAjusteInventario(){
		Ext.get("contenedor_AjusteInventario").load({
			url     : "ajuste_inventario/grillaContable.php",
			scripts : true,
			nocache : true,
			params  :
			{
				filtro_bodega     : document.getElementById('filtro_ubicacion_AjusteInventario').value,
				opcGrillaContable : 'AjusteInventario',
			}
		});
		// Ext.getCmp('btnNuevaAjusteInventario').disable();
		document.getElementById('titleDocumentoAjusteInventario').innerHTML='';
	}

	function nuevaTraslados(){
		Ext.get("contenedor_Traslados").load({
			url     : "traslados/grillaContable.php",
			scripts : true,
			nocache : true,
			params  :
			{
				// filtro_bodega     : document.getElementById('filtro_ubicacion_AjusteInventario').value,
				opcGrillaContable : 'Traslados',
				filtro_sucursal   : document.getElementById('filtro_sucursal_Traslados').value,
				filtro_bodega     : document.getElementById('filtro_ubicacion_Traslados').value
			}
		});
		// Ext.getCmp('btnNuevaAjusteInventario').disable();
		document.getElementById('titleDocumentoTraslados').innerHTML='';
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

	function windows_upload_excel(){
		if(globalNameFileUpload != ''){ alert('Elimine el archivo anterior antes de subir uno nuevo!'); return; }
		document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;');
	}

	function close_ventana_upload_file(){ document.getElementById('divPadreModalUploadFile').setAttribute('style',''); }

	function calcTotalDocAjusteInventario(cantidadInventario,cantidad,costo,accion,tipo) {
		var ajuste   = 0
		,	subtotal = 0;

		ajuste   = Math.abs(cantidadInventario - cantidad)
		subtotal = Math.abs(ajuste)*costo

		if (cantidadInventario == cantidad) { subtotal=0; }
		// SALIDAS
		else if (cantidad<cantidadInventario || ajuste==0 ) {
			if (accion=='agregar') { subtotalSalidaAjusteInventario += subtotal; }
			else if (accion=='eliminar') { subtotalSalidaAjusteInventario -= subtotal; }
		}
		// ENTRADAS
		else if (cantidad>cantidadInventario) {
			if (accion=='agregar') { subtotalIngresoAjusteInventario += subtotal}
			else if (accion=='eliminar') { subtotalIngresoAjusteInventario -= subtotal}
			// console.log(subtotal+' '+tipo);
		}

		totalAjusteInventario = ('agregar')? totalAjusteInventario+ subtotal : totalAjusteInventario-subtotal;

		document.getElementById('subtotalAcumuladoIngresosAjusteInventario').innerHTML = formato_numero(subtotalIngresoAjusteInventario, "<?php echo $_SESSION['DECIMALESMONEDA'] ?>", '.', ',');
		document.getElementById('subtotalAcumuladoSalidasAjusteInventario').innerHTML  = formato_numero(subtotalSalidaAjusteInventario, "<?php echo $_SESSION['DECIMALESMONEDA'] ?>", '.', ',');
		document.getElementById('totalAcumuladoAjusteInventario').innerHTML            = formato_numero(totalAjusteInventario, "<?php echo $_SESSION['DECIMALESMONEDA'] ?>", '.', ',');
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

    //VENTANA MODAL CON LA IMAGEN DE AYUDA PARA CARGAR EL EXCEL
    function imagenAyudaModal() {

       	var contenido = '<div style="margin: 0px auto;width:778px;" >'+
	       					'<img src="img/formato_items.png"><br>'+
	       					'<spam style="color:#FFF;font-weight:bold;font-size:9px;">HAGA CLICK PARA CERRAR</spam>'+
       					'</div>';

      	parentModal = document.createElement("div");
        parentModal.innerHTML = '<div id="modal">'+contenido+'</div>';
        parentModal.setAttribute("id", "divPadreModal");
        parentModal.setAttribute("onclick", "cerrarVentanaModal()");
        document.body.appendChild(parentModal);
        document.getElementById("divPadreModal").className = "fondoImgHelp";

    }

    function cerrarVentanaModal() {
    	document.getElementById('divPadreModal').parentNode.removeChild(document.getElementById('divPadreModal'));
    }

</script>