var elementos_presentes ='';
var elementos_cargados = new Array();
var myElement='';
var storeun;
var union=0;
var lemn_union = '';
var edicion = 0;
var id_actual='';
var check =0;
var id_anterior='';
var val_spinner;
var ind_spinner = 0;
var myalto = Ext.getBody().getHeight();
var myancho = Ext.getBody().getWidth();

Ext.onReady
(
	function()
	{

		Ext.QuickTips.init();

		storeun = new Ext.data.Store
		(
			{
				proxy: new Ext.data.HttpProxy
				(
					{
						url				    : 'disenador_formatos/bd/bd.php',
						root			    : 'datos',
						totalProperty		: 'total'
					}
				),
				reader: new Ext.data.JsonReader
				(
					{
						root				: 'datos',
						totalProperty		: 'total',
						id				    : 'id',
						fields			   	:
						[
							'id',
							'nombre'
						]
					}
				),
				remoteSort: true
			}
		);
		storeun.load({params: {op: 'planes', /*id: apuntador_congreso*/}});

		var storedos = new Ext.data.Store
		(
			{
				proxy: new Ext.data.HttpProxy
				(
					{
						url				    : 'disenador_formatos/bd/bd.php',
						root			    : 'datos',
						totalProperty		: 'total'
					}
				),
				reader: new Ext.data.JsonReader
				(
					{
						root				: 'datos',
						totalProperty		: 'total',
						id				    : 'id',
						fields			   	:
						[
							'id',
							'nombre',
							'tipo',
							'deletes'
						]
					}
				),
				remoteSort: true
			}
		);
		storedos.load({params: {op: 'formatos', /*id: apuntador_congreso*/}});

		var contenido = new Ext.Panel
		(
			{
				renderTo	: 'formatos',
				layout		: 'border',
				height  	: myalto - 51,
				width			: myancho - 33,
				items			:	[
											{
												region			: 'west',
												height  		: 350,
												width				: 250,
												layout 			: "form",
												labelAlign	: 'top',
												bodyStyle		: 'padding:10px',
												margins			: '5 5 5 5',
												items 			:	[
																			{
																				xtype 			: "textfield",
																				fieldLabel 	: "Nombre",
																				id					: 'nombre',
																				labelStyle 	: "width:60;font-size:11px",
																				name 				: "textvalue",
																				readOnly 		: true,
																				width				: 220
																			},
																			{
																				xtype 					: "combo",
																				fieldLabel 			: "Tipo de Formato",
																				labelStyle 	    : "width:60;font-size:11px",
																				id							: 'tip_format',
																				name 						: "combovalue",
																				hiddenName 			: "combovalue",
																				inputValue 			: "cbvalue",
																				displayField		: 'tipo_de_campo',
																				valueField			: 'valor',
																				triggerAction		: 'all',
																				mode						: 'local',
																				emptyText				: 'Seleccione ...',
																				editable				: false,
																				forceSelection	: true,
																				allowBlank			: true,
																				width						: 220,
																				store						: new Ext.data.SimpleStore
																				(
																					{
																						fields	: ['valor','tipo_de_campo'],
																						data		:	[
																												['escarapela','Escarapela'],
																												['certificado','Cerftificado']
																											]
																					}
																				)
																			},
																			{
																				xtype 					: "textfield",
																				id							: "altura",
																				fieldLabel 			: "Alto (mm)",
																				labelStyle 			: "width:60;font-size:11px",
																				name 						: "textvalue",
																				readOnly 				: true,
																				width						: 220
																			},
																			{
																				xtype 					: "textfield",
																				id							: "anchura",
																				fieldLabel 			: "Ancho (mm)",
																				labelStyle 			: "width:60;font-size:11px",
																				name 						: "textvalue",
																				readOnly 				: true,
																				width						: 220
																			},
																			{
																				xtype 					: "combo",
																				fieldLabel 			: "Plan",
																				id							: 'tip_plan',
																				name 						: "combovalue",
																				hiddenName 			: "combovalue",
																				inputValue 			: "cbvalue",
																				displayField		: 'nombre',
																				valueField			: 'id',
																				triggerAction		: 'all',
																				mode						: 'local',
																				emptyText				: 'Seleccione ...',
																				editable				: false,
																				forceSelection	: true,
																				allowBlank			: true,
																				store						: storeun,
																				width						: 220
																			},
																			{
																				xtype 							: "grid",
																				id									: 'escarap',
																				width								: 220,
																				height							: 290,
																				store								: storedos,
																				enableCtxMenu				: false,
																				enableColLock				: false,
																				enableColumnMove		: false,
																				enableColumnHide		: false,
																				enableColumnResize	: false,
																				enableHdMenu				: false,
																				enableColLock				: false,
																				frame								: false,
																				viewConfig					: { forceFit:true},
																				selModel						: new Ext.grid.RowSelectionModel
																				(
																					{
																						singleSelect:true
																					}
																				),
																				cm : new Ext.grid.ColumnModel
																				(
																					[
																						{
																							id							: 'nombre',
																							header					: "Nombre",
																							dataIndex				: 'nombre',
																							sortable				: false,
																							resizable				: false,
																							defaultSortable	: false,
																							menuDisabled		: true,
																							renderer				: changes,
																							width						: 120
																						},
																						{
																							header					: "Tipo",
																							dataIndex				: 'tipo',
																							sortable				: false,
																							resizable				: false,
																							defaultSortable	: false,
																							menuDisabled		: true,
																							width						: 100
																						},
																						{
																							header					: "Borrar",
																							dataIndex				: 'deletes',
																							sortable				: false,
																							resizable				: false,
																							defaultSortable	: false,
																							menuDisabled		: true,
																							renderer				: borr,
																							width						: 50
																						}
																					]
																				)
																			}
																		]
											},
											{
												region			: 'center',
												height  		: 350,
												width				: myancho - 220,
												margins			: '5 5 5 0',
												autoScroll  : true,
												baseCls			: 'x-plain',
												layout 			: "absolute",
												tbar				:	[
																				'-',
																				{
																					text			: 'Nuevo',
																					tooltip		: 'Nuevo Formato',
																					iconCls		: 'page',
																					handler		: nuevo_format
																				},'-',
																				{
																					text			: 'Guardar',
																					tooltip		: 'Guardar Formato',
																					iconCls		: 'guardar16',
																					handler		: guardar_datos_presentes2
																				},'-',
																				{
																					text			: 'Adicionar',
																					tooltip		: 'Adicionar Campo',
																					iconCls		: 'fadd',
																					handler		: nuevo_element
																				},'-',
																				{
																					text			: 'Eliminar',
																					tooltip		: 'Eliminar Campo',
																					iconCls		: 'fdelete',
																					handler		: eliminar_elemento
																				},'-',
																				{
																					text			: 'Unir',
																					tooltip		: 'Unir Campos',
																					iconCls		: 'link',
																					handler		: validar_unir_campo
																				},'-',
																				{
																					xtype 			: "checkbox",
																					id					: 'tool9',
																					boxLabel 		: "Codigo de Barras",
																					name 				: "checkbox",
																					inputValue 	: "cbvalue",
																					listeners		:	{
																													check  	: function()
																													{
																														if(myElement != '')
																														{
																															if(typeof document.getElementById(myElement) !== "undefined")
																															{
																																if(check != 0)
																																{
																																	var acti = Ext.getCmp("tool9").getValue();
																																	var inf = myElement.replace(/elemento/g, "");
																																	for(ind=0; ind<elementos_cargados.length; ind++)
																																	{
																																		if (elementos_cargados[ind][0] == inf)
																																		{
																																			if(acti == true)
																																			{
																																				elementos_cargados[ind][10] = 'true';
																																			}
																																			else
																																			{
																																				elementos_cargados[ind][10] = 'false';
																																			}
																																			break;
																																		}
																																	}
																																	if(acti == true)
																																	{
																																		var barcode = document.createElement('div');
																																		barcode.setAttribute('id', 'barcode'+inf);
																																		barcode.setAttribute('style', 'position:absolute; left: 0px; top: 0px; width: 16px; height: 16px; background-image: url(disenador_formatos/imagenes/barcode.png)');
																																		var botq = document.getElementById(myElement);
																																		botq.appendChild(barcode);
																																	}
																																	else
																																	{
																																		var docBody = document.getElementById(myElement);
																																		var node = document.getElementById('barcode'+inf);
																																		docBody.removeChild(node);
																																	}
																																	check = 0;
																																	myElement = '';
																																}
																															}
																															else
																															{
																																Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Editarlo');
																															}
																														}
																														else
																														{
																															Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Editarlo');
																														}
																													}
																												}
																				},'-'
																			],
												items				:	[
																				{
																					xtype		: 'box',
																					id			: 'xbox1',
																					autoEl	: {tag: 'img', src: 'disenador_formatos/imagenes/regla1.png'},
																					width		: 2200,
																					height	: 40,
																				},
																				{
																					xtype		: 'box',
																					id			: 'xbox2',
																					autoEl	: {tag: 'img', src: 'disenador_formatos/imagenes/regla2.png'},
																					width		: 40,
																					height	: 1200,
																					y				: 40
																				},
																				{
																					id	 			: 'contened',
																					layout		: 'absolute',
																					border		: false,
																					html			: '<div id="contenedor" style="width:2159px; height:1200px; background-image:url(disenador_formatos/imagenes/Fondo.png);"></div>',
																					width			: 2159,
																					height		: 1200,
																					x					: 41,
																					y					: 41
																				}
																			],
												bbar				:	[
																				{
																					xtype 					: "combo",
																					id							: 'tool1',
																					name 						: "combovalue",
																					hiddenName 			: "combovalue",
																					inputValue 			: "cbvalue",
																					displayField		: 'tipo_de_fuente',
																					valueField			: 'tipo_de_fuente',
																					triggerAction		: 'all',
																					mode						: 'local',
																					editable				: false,
																					forceSelection	: true,
																					allowBlank			: true,
																					width						: 80,
																					store						: new Ext.data.SimpleStore
																					(
																						{
																							fields	: ['tipo_de_fuente'],
																							data		:	[
																													['Arial'],
																													['Times New Roman'],
																													['Courier New'],
																													['Trebuchet MS'],
																													['Comic Sans MS'],
																													['Franklin Gothic Book'],
																													['Calibri'],
																													['Old English Text MT'],
																													['French Script MT'],
																													['Brush Script MT'],
																													['Lucida Handwriting']
																												]
																						}
																					),
																					listeners:
																					{
																						select:
																						{
																							fn:function(combo, record, index)
																							{
																								if(myElement != '')
																								{
																									if(typeof document.getElementById(myElement) !== "undefined")
																									{
																										var obj= document.getElementById(myElement);
																										obj.style.fontFamily = this.value;
																										var inf = myElement.replace(/elemento/g, "");
																										for(ind=0; ind<elementos_cargados.length; ind++)
																										{
																											if (elementos_cargados[ind][0] == inf)
																											{
																												elementos_cargados[ind][6] = this.value;
																												break;
																											}
																										}
																									}
																									else
																									{
																										Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Editarlo');
																									}
																								}
																								else
																								{
																									Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Editarlo');
																								}
																							}
																						}
																					}
																				},'-',
																				{
																					xtype						: 'textfield', //'spinnerfield',
																					id							: 'tool2',
																					name						: 'test',
																					actionMode			: 'wrap',
																					width						: 80,
																					minValue				: 8,
																					maxValue				: 50,
																					incrementValue	: 1,
																					maskRe					: /^[0-9]$/,
																					accelerate			: true,
																					listeners 			:
																					{
																						'blur' :
																						{
																							fn: function(e)
																							{
																								tame = Ext.getCmp('tool2').getValue();

																								if(myElement != '')
																								{
																									if(typeof document.getElementById(myElement) !== "undefined")
																									{
																										var obj= document.getElementById(myElement);
																										obj.style.fontSize = tame+'px';
																										var inf = myElement.replace(/elemento/g, "");
																										for(ind=0; ind<elementos_cargados.length; ind++)
																										{
																											if (elementos_cargados[ind][0] == inf)
																											{
																												elementos_cargados[ind][7] = tame;
																												break;
																											}
																										}
																									}
																									else
																									{
																										Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Editarlo');
																									}
																								}
																								else
																								{
																									Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Editarlo');
																								}
																							}
																						}
																					}
																				},'-',
																				{
																					xtype 					: "combo",
																					id							: 'tool3',
																					name 						: "combovalue",
																					hiddenName 			: "combovalue",
																					inputValue 			: "cbvalue",
																					displayField		: 'estilo',
																					valueField			: 'valor',
																					triggerAction		: 'all',
																					mode						: 'local',
																					editable				: false,
																					forceSelection	: true,
																					allowBlank			: true,
																					width						: 80,
																					store						: new Ext.data.SimpleStore
																					(
																						{
																							fields	: ['valor','estilo'],
																							data		:	[
																													['Normal', 'Normal'],
																													['Bold','Negrilla']
																												]
																						}
																					),
																					listeners:
																					{
																						select:
																						{
																							fn:function(combo, record, index)
																							{
																								if(myElement != '')
																								{
																									if(typeof document.getElementById(myElement) !== "undefined")
																									{
																										var obj= document.getElementById(myElement);
																										obj.style.fontWeight = this.value;
																										var inf = myElement.replace(/elemento/g, "");
																										for(ind=0; ind<elementos_cargados.length; ind++)
																										{
																											if (elementos_cargados[ind][0] == inf)
																											{
																												elementos_cargados[ind][8] = this.value;
																												break;
																											}
																										}
																									}
																									else
																									{
																										Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Editarlo');
																									}
																								}
																								else
																								{
																									Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Editarlo');
																								}
																							}
																						}
																					}
																				},'-',
																				{
																					xtype 					: "combo",
																					id							: 'tool4',
																					name 						: "combovalue",
																					hiddenName 			: "combovalue",
																					inputValue 			: "cbvalue",
																					displayField		: 'tipo_de_fuente',
																					valueField			: 'valor',
																					triggerAction		: 'all',
																					mode						: 'local',
																					editable				: false,
																					forceSelection	: true,
																					allowBlank			: true,
																					width						: 80,
																					store						: new Ext.data.SimpleStore
																					(
																						{
																							fields	: ['valor','tipo_de_fuente'],
																							data		:	[
																													['center','Centrada'],
																													['justify','Justificada'],
																													['left','Izquierda'],
																													['right','Drecha']
																												]
																						}
																					),
																					listeners:
																					{
																						select:
																						{
																							fn:function(combo, record, index)
																							{
																								if(myElement != '')
																								{
																									if(typeof document.getElementById(myElement) !== "undefined")
																									{
																										var obj= document.getElementById(myElement);
																										obj.align = this.value;
																										var inf = myElement.replace(/elemento/g, "");
																										for(ind=0; ind<elementos_cargados.length; ind++)
																										{
																											if (elementos_cargados[ind][0] == inf)
																											{
																												elementos_cargados[ind][9] = this.value;
																												break;
																											}
																										}
																									}
																									else
																									{
																										Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Editarlo');
																									}
																								}
																								else
																								{
																									Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Editarlo');
																								}
																							}
																						}
																					}
																				},'-',
																				{
																					xtype 			: "textfield",
																					name		 		: "textvalue",
																					id					: 'tool5',
																					value				: 'Ancho:',
																					readOnly 		: true,
																					frame				: true,
																					width				: 80
																				},'-',
																				{
																					xtype 			: "textfield",
																					name		 		: "textvalue",
																					id					: 'tool6',
																					value				: 'Alto:',
																					readOnly 		: true,
																					frame				: true,
																					width				: 80
																				},'-',
																				{
																					xtype 			: "textfield",
																					name		 		: "textvalue",
																					id					: 'tool7',
																					value				: 'PosX:',
																					readOnly 		: true,
																					frame				: true,
																					width				: 80
																				},'-',
																				{
																					xtype 			: "textfield",
																					id					: 'tool8',
																					name		 		: "textvalue",
																					value				: 'PosY:',
																					readOnly 		: true,
																					frame				: true,
																					width				: 80
																				},'-'
																			]
											}
										]
			}
		);

		function borr(val)
		{
		    if(val == '')
			{
		        return '<div style="cursor: pointer; cursor: hand; width:16px; height:16px; background-image:url(disenador_formatos/imagenes/delete.png);" onclick="borar_format();"></div>';
		    }
		    return val;
		};

		function cambiar_down(e)
		{
			//console.log(myElement);
			if(myElement != '')
			{
				if(typeof document.getElementById(myElement) !== "undefined")
				{
					var obj= document.getElementById(myElement);
					obj.style.fontSize = e+'px';
					var inf = myElement.replace(/elemento/g, "");
					for(ind=0; ind<elementos_cargados.length; ind++)
					{
						if (elementos_cargados[ind][0] == inf)
						{
							elementos_cargados[ind][7] = e;
							break;
						}
					}
				}
				else
				{
					Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Editarlo');
				}
			}
			else
			{
				Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Editarlo');
			}
		};

		function cambiar_up(e)
		{
			//console.log(myElement);
			if(myElement != '')
			{
				if(typeof document.getElementById(myElement) !== "undefined")
				{
					var obj= document.getElementById(myElement);
					obj.style.fontSize = e+'px';
					var inf = myElement.replace(/elemento/g, "");
					for(ind=0; ind<elementos_cargados.length; ind++)
					{
						if (elementos_cargados[ind][0] == inf)
						{
							elementos_cargados[ind][7] = e;
							break;
						}
					}
				}
				else
				{
					Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Editarlo');
				}
			}
			else
			{
				Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Editarlo');
			}
		};

		function nuevo_element()
		{
			if(edicion == 1)
			{
				var win_new_element = new Ext.Window
				(
					{
						title		: 'Nuevo Elemento',
						id			: 'ventana_new_element',
						iconCls		: 'permi',
						closable	: true,
						draggable	: false,
						autoScroll  : false,
						autoDestroy : true,
						animate		: false,
						frame		: true,
						border		: false,
						resizable	: false,
						modal 		: true,
						width 		: 280,
						height 		: 380,
						autoLoad:
						{
							url		: 'disenador_formatos/formatos3.php',
							scripts	: true
						}
					}
				);

				win_new_element.show();
			}
			else
			{
				Ext.Msg.alert('Alerta', 'Debe Crear o Cargar un Formato para Poder Adicionar algun Elemento');
			}
		};

		//========================== BOTON NUEVO FORMATO =========================//
		function nuevo_format()
		{
			if(edicion == 0)
			{
				var win_new_format = new Ext.Window
				(
					{
						title				: 'Nuevo Formato',
						id					: 'ventana_new_format',
						iconCls			: 'page',
						closable		: true,
						draggable		: false,
						autoScroll  : false,
						autoDestroy : true,
						animate			: false,
						frame				: true,
						border			: false,
						resizable		: false,
						modal 			: true,
						width 			: 280,
						height 			: 380,
						autoLoad:
						{
							url		: 'disenador_formatos/formatos2.php',
							scripts	: true
						}
					}
				);

				win_new_format.show();
			}
			else
			{
				Ext.MessageBox.confirm('Alerta', 'Desea Guardar los Cambios Realizados ?<br>Recuerde que va a Generar un Nuevo Formato y todos los campos Actuales seran Borrados ', cargar_formato);
			}
		};

		function changes(val)
		{
		    return '<div style="width:80px; height:20px;">'+val+'</div>'
		};

		function cargar_formato(btn)
		{
			if(btn=='yes')
			{
				guardar_datos_presentes();
				var docBody = document.getElementById('bankname-rzwrap').parentNode;
				var node = document.getElementById('bankname-rzwrap');
				docBody.removeChild(node);
				edicion = 0;
				nuevo_format();
			}
			else
			{
				var docBody = document.getElementById('bankname-rzwrap').parentNode;
				var node = document.getElementById('bankname-rzwrap');
				docBody.removeChild(node);
				edicion = 0;
				nuevo_format();
			}
		};

		function viejo_format()
		{
			if(edicion == 0)
			{
				edicion = 1;
				var grid = Ext.getCmp('escarap');
				var id = grid.getSelectionModel().getSelected();

				if(id_actual != '')
				{
					id_anterior = id_actual;
					id_actual = id.id;
				}
				else
				{
					id_anterior = id.id;
					id_actual = id.id;
				}

				Ext.Ajax.request
				(
					{
						url 		: 'disenador_formatos/bd/bd.php',
						method		: 'GET',
						params		:
						{
							op		: 'obtener_datos_old',
							id		: id.id
						},
						success		: function (result, request)
						{
							var ind = (result.responseText);
							d   = ind.split('{');
							Ext.getCmp('nombre').setValue(d[0]);
							Ext.getCmp('tip_format').setValue(d[1]);
							Ext.getCmp('anchura').setValue(parseInt((d[2]*0.28)+1));
							Ext.getCmp('altura').setValue(parseInt((d[3]*0.28)+1));
							Ext.getCmp('tip_plan').setValue(d[4]);
							verificar();
							elementos_presentes.length = 0;
							array_elementos();
						}
					}
				);

			}
			else
			{
				Ext.MessageBox.confirm('Alerta', 'Desea Guardar los Cambios Realizados ?<br>Recuerde que va a Cargar un Formato Existente<br>y todos los campos Actuales seran Borrados ', cargar_viejo_format);
			}
		};

		function cargar_viejo_format(btn)
		{
			if(btn=='yes')
			{
				var docBody = document.getElementById('bankname-rzwrap').parentNode;
				var node = document.getElementById('bankname-rzwrap');
				docBody.removeChild(node);
				var grid = Ext.getCmp('escarap');
				var id = grid.getSelectionModel().getSelected();
				id_anterior = id_actual;
				id_actual = id.id;
				guardar_datos_presentes();

				Ext.Ajax.request
				(
					{
						url 		: 'disenador_formatos/bd/bd.php',
						method		: 'GET',
						params		:
						{
							op		: 'obtener_datos_old',
							id		: id.id
						},
						success		: function (result, request)
						{
							var ind = (result.responseText);
							d   = ind.split('{');
							Ext.getCmp('nombre').setValue(d[0]);
							Ext.getCmp('tip_format').setValue(d[1]);
							Ext.getCmp('anchura').setValue(parseInt((d[2]*0.28)+1));
							Ext.getCmp('altura').setValue(parseInt((d[3]*0.28)+1));
							Ext.getCmp('tip_plan').setValue(d[4]);
							verificar();
							elementos_presentes.length = 0;
							array_elementos();
						}
					}
				);
				/*array_elementos();
				setTimeout(adicionar_formato, 300);
				setTimeout(adicionar_elementos, 300);*/
			}
			else
			{
				var docBody = document.getElementById('bankname-rzwrap').parentNode;
				var node = document.getElementById('bankname-rzwrap');
				docBody.removeChild(node);
				var grid = Ext.getCmp('escarap');
				var id = grid.getSelectionModel().getSelected();
				id_anterior = id_actual;
				id_actual = id.id;

				Ext.Ajax.request
				(
					{
						url 		: 'disenador_formatos/bd/bd.php',
						method		: 'GET',
						params		:
						{
							op		: 'obtener_datos_old',
							id		: id.id
						},
						success		: function (result, request)
						{
							var ind = (result.responseText);
							d   = ind.split('{');
							Ext.getCmp('nombre').setValue(d[0]);
							Ext.getCmp('tip_format').setValue(d[1]);
							Ext.getCmp('anchura').setValue(parseInt((d[2]*0.28)+1));
							Ext.getCmp('altura').setValue(parseInt((d[3]*0.28)+1));
							Ext.getCmp('tip_plan').setValue(d[4]);
							verificar();
							elementos_presentes.length = 0;
							array_elementos();
						}
					}
				);
				/*array_elementos();
				setTimeout(adicionar_formato, 300);
				setTimeout(adicionar_elementos, 300);*/
			}
		};

		function Esperar()
		{
			setTimeout(verificar,250)
		};


		function adicionar_formato()
		{
			var anc = Ext.getCmp("anchura").getValue();
			Ext.getCmp("anchura").setValue(anc + ' mm');
			anc = parseInt((anc/0.28)+1);
			var alt = Ext.getCmp("altura").getValue();
			Ext.getCmp("altura").setValue(alt + ' mm');
			alt = parseInt((alt/0.28)+1);

			var newformat = document.createElement('div');
			newformat.setAttribute('id', 'bankname');
			newformat.setAttribute('style', 'position:obsolute; left: 0px; top: 0px; width: '+anc+'px; height: '+alt+'px; background-color:#FFFFFF;');
			var bot = document.getElementById('contenedor');
			bot.appendChild(newformat);

			var basic = new Ext.Resizable
			(
				'bankname',
				{
					wrap	 : true,
					dynamic	 : true,
					minWidth : 178,
					minHeight: 178
				}
			);

			basic.on
			(
				"resize",
				function(r, width, height, event)
				{
					Ext.getCmp('altura').setValue(parseInt(height*0.28)+1+' mm');
					Ext.getCmp('anchura').setValue(parseInt(width*0.28)+1+' mm');
					if (basic.layout)
					{
						basic.doLayout();
					}
				}
			);
		};

		function verificar()
		{
			if(document.getElementById("anchura") != null && document.getElementById("altura") != null && document.getElementById("contenedor") != null )
			{
				adicionar_formato();
			}
			else
			{
				Esperar();
			}
		};

		function guardar_datos_presentes()
		{
			var frm1 = Ext.getCmp("tip_format").getValue();
			var frm2 = Ext.getCmp("tip_plan").getValue();
			var frm3a = Ext.getCmp("anchura").getValue();
			var frm3b = parseInt(frm3a.replace(/ mm/g, ""));
			var frm3 = parseInt((frm3b/0.28)+1);
			var frm4a = Ext.getCmp("altura").getValue();
			var frm4b = parseInt(frm4a.replace(/ mm/g, ""));
			var frm4 = parseInt((frm4b/0.28)+1);
			var elem = Ext.util.JSON.encode(elementos_cargados);

			Ext.Ajax.request
			(
				{
					url 			: 'disenador_formatos/bd/bd.php',
					method			: 'POST',
					params			:
					{
						op			: 'guardar_datos_presentes',
						id			: id_anterior,
						formato		: frm1,
						plan		: frm2,
						ancho		: frm3,
						alto		: frm4,
						elementos	: elem
					},
					success		: function (result, request){}
				}
			);
		};

		function guardar_datos_presentes2()
		{
			var frm1 = Ext.getCmp("tip_format").getValue();
			var frm2 = Ext.getCmp("tip_plan").getValue();
			var frm3a = Ext.getCmp("anchura").getValue();
			var frm3b = parseInt(frm3a.replace(/ mm/g, ""));
			var frm3 = parseInt((frm3b/0.28)+1);
			var frm4a = Ext.getCmp("altura").getValue();
			var frm4b = parseInt(frm4a.replace(/ mm/g, ""));
			var frm4 = parseInt((frm4b/0.28)+1);
			var elem = Ext.util.JSON.encode(elementos_cargados);
			Ext.Ajax.request
			(
				{
					url 			: 'disenador_formatos/bd/bd.php',
					method			: 'POST',
					params			:
					{
						op			: 'guardar_datos_presentes',
						id			: id_actual,
						formato		: frm1,
						plan		: frm2,
						ancho		: frm3,
						alto		: frm4,
						elementos	: elem
					},
					success		: function (result, request){}
				}
			);
		};

		function eliminar_elemento()
		{
			if(myElement != '')
			{
				if(typeof document.getElementById(myElement) !== "undefined")
				{
					var inf = myElement.replace(/elemento/g, "");
					Ext.Ajax.request
					(
						{
							url 		: 'disenador_formatos/bd/bd.php',
							method		: 'GET',
							params		:
							{
								op		: 'Eliminar_elementos',
								id		: inf
							},
							success		: function (result, request){}
						}
					);
					var docBody = document.getElementById(myElement).parentNode;
					var node = document.getElementById(myElement);
					docBody.removeChild(node);
					for(ind=0; ind<elementos_cargados.length; ind++)
				    {
						if (elementos_cargados[ind][0] == inf)
						{
							elementos_cargados.splice(ind,1);
							break;
						}
					}
					myElement='';
				}
				else
				{
					Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Borrar');
				}
			}
			else
			{
				Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Borrar');
			}
		};

		function array_elementos()
		{
			var grid = Ext.getCmp('escarap');
			var id = grid.getSelectionModel().getSelected();

			Ext.Ajax.request
			(
				{
					url 		: 'disenador_formatos/bd/bd.php',
					method		: 'POST',
					params		:
					{
						op		: 'obtener_datos_elem_presentes',
						id		: id.id,
						// idc		: apuntador_congreso
					},
					success		: function (result, request)
					{
						var llegados = (result.responseText);
						if(elementos_presentes.length == 0)
						{
							elementos_presentes = llegados;
						}
						else
						{
							elementos_presentes.length = 0
							elementos_presentes = llegados;
						}
						//console.log(elementos_presentes)
						//setTimeout(adicionar_elementos, 300);
						adicionar_elementos();
					}
				}
			);

		};

		function adicionar_elementos()
		{
			myElement = '';
			elementos_cargados.length = 0;
			if(elementos_presentes.length !=0)
			{

				var elme = elementos_presentes.split(';');
				for(i = 0; i < elme.length; i++)
				{
					var dat = elme[i].split(',')
					var id = dat[0];
					var nomb = dat[1];
					var anch = dat[2];
					var ancho = parseInt(anch);
					var alt = dat[3];
					var alto = parseInt(alt);
					var posi = dat[4];
					var posy = parseInt(posi);
					var poss = dat[5];
					var posx = parseInt(poss);
					var font = dat[6];
					var siz = dat[7];
					var size = parseInt(siz);
					var form = dat[8];
					var aling = dat[9];
					var barcode = dat[10];

					Ext.getCmp('tool5').setValue('Alto: ' + parseInt((alto*0.28))+'mm');
					Ext.getCmp('tool6').setValue('Ancho: ' + parseInt((ancho*0.28))+'mm');
					Ext.getCmp('tool7').setValue('PosX: ' + parseInt((posx*0.28))+'mm');
					Ext.getCmp('tool8').setValue('PosY: ' + parseInt((posy*0.28))+'mm');

					var newelemnt = document.createElement('div');
					newelemnt.setAttribute('id', 'elemento'+id);
					newelemnt.setAttribute('class', 'drsElement drsMoveHandle');
					newelemnt.setAttribute('style', 'left: '+posx+'px; top: '+posy+'px; width: '+ancho+'px; height: '+alto+'px; font-size:'+size+'px; font-weight:'+form+'; font-family:'+font+'; background-color:#FFFFFF; z-index:2;');
					newelemnt.setAttribute('align', aling);
					newelemnt.setAttribute('onMouseDown', 'myElement = this.id; check = 0;');
					newelemnt.setAttribute('onClick', 'set_tools();');

						var bot2 = document.getElementById('bankname');
						bot2.appendChild(newelemnt);
						document.getElementById('elemento'+id).innerHTML = nomb;

					var dragresize = new DragResize
					(
						'dragresize',
						{
							minWidth	: 72,
							minHeight	: 18,
							minLeft		: 0,
							minTop		: 0,
							maxLeft		: 1200,
							maxTop		: 1159
						}
					);

					dragresize.isElement = function(elm)
					{
						if (elm.className && elm.className.indexOf('drsElement') > -1)
						{
							return true;
						}
					};

					dragresize.isHandle = function(elm)
					{
						if (elm.className && elm.className.indexOf('drsMoveHandle') > -1)
						{
							return true;
						}
					};

					dragresize.ondragfocus = function(){};

					dragresize.ondragstart = function(isResize)
					{
						var d = document.getElementById(myElement);
						var ff = getPosition(d);
						var ffpos = ff.split(',');
						var ffx = parseInt(ffpos[0]);
						var ffy = parseInt(ffpos[1]);
						Ext.getCmp('tool7').setValue('PosX: ' + parseInt((ffx*0.28))+'mm');
						Ext.getCmp('tool8').setValue('PosY: ' + parseInt((ffy*0.28))+'mm');

						var ww1 = document.getElementById(myElement).style.width;
						var hh1 = document.getElementById(myElement).style.height;
						var ww = ww1.replace(/px/g, "");
						var hh = hh1.replace(/px/g, "");
						Ext.getCmp('tool5').setValue('Alto: ' + parseInt((hh*0.28))+'mm');
						Ext.getCmp('tool6').setValue('Ancho: ' + parseInt((ww*0.28))+'mm');

						var inf = myElement.replace(/elemento/g, "");
						for(ind=0; ind<elementos_cargados.length; ind++)
						{
							if (elementos_cargados[ind][0] == inf)
							{
								elementos_cargados[ind][2] = ww;
								elementos_cargados[ind][3] = hh;
								elementos_cargados[ind][4] = ffy;
								elementos_cargados[ind][5] = ffx;
								break;
							}
						}
					};

					dragresize.ondragmove = function(isResize)
					{
						var d = document.getElementById(myElement);
						var ff = getPosition(d);
						var ffpos = ff.split(',');
						var ffx = parseInt(ffpos[0]);
						var ffy = parseInt(ffpos[1]);
						//Ext.getCmp('anchura').setValue(ff);
						Ext.getCmp('tool7').setValue('PosX: ' + parseInt((ffx*0.28))+'mm');
						Ext.getCmp('tool8').setValue('PosY: ' + parseInt((ffy*0.28))+'mm');

						var ww1 = document.getElementById(myElement).style.width;
						var hh1 = document.getElementById(myElement).style.height;
						var ww = ww1.replace(/px/g, "");
						var hh = hh1.replace(/px/g, "");
						Ext.getCmp('tool5').setValue('Alto: ' + parseInt((hh*0.28))+'mm');
						Ext.getCmp('tool6').setValue('Ancho: ' + parseInt((ww*0.28))+'mm');
						var inf = myElement.replace(/elemento/g, "");
						for(ind=0; ind<elementos_cargados.length; ind++)
						{
							if (elementos_cargados[ind][0] == inf)
							{
								elementos_cargados[ind][2] = ww;
								elementos_cargados[ind][3] = hh;
								elementos_cargados[ind][4] = ffy;
								elementos_cargados[ind][5] = ffx;
								break;
							}
						}
					};

					dragresize.ondragend = function(isResize){};

					dragresize.ondragblur = function(){};

					dragresize.apply(document);

					var elementos = new Array(id, nomb, ancho, alto, posi, poss, font, siz, form, aling, barcode);
					elementos_cargados[i] = elementos;

					if(barcode == 'true')
					{
						var barcodes = document.createElement('div');
						barcodes.setAttribute('id', 'barcode'+id);
						barcodes.setAttribute('style', 'position:absolute; left: 0px; top: 0px; width: 16px; height: 16px; background-image: url(disenador_formatos/imagenes/barcode.png)');
						var botq = document.getElementById('elemento'+id);
						botq.appendChild(barcodes);
					}
				}
			}
		};

		function validar_unir_campo()
		{
			if(myElement != '')
			{
				if(typeof document.getElementById(myElement) !== "undefined")
				{
					var inf = myElement.replace(/elemento/g, "");
					for(ind=0; ind<elementos_cargados.length; ind++)
					{
						if (elementos_cargados[ind][0] == inf)
						{
							var notm = elementos_cargados[ind][1];
							break;
						}
					}
					Ext.MessageBox.confirm('Precauci�n ', 'Desea Unir el Elemento '+notm+' a otro Elemento de este Formato ?<br> si al respuesta es Afirmativa, pulse el boton Si y Esoja el Formato que desea Unir', validar_campo);
				}
				else
				{
					Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento dentro del Formato para Unirlo');
				}
			}
			else
			{
				Ext.Msg.alert('Alerta', 'Debe cargar un Formato y luego Escoger un Elemento para Unirlo');
			}
		};

		function validar_campo(btn)
		{
			if(btn=='yes')
			{
				union = 1;
				lemn_union = myElement;
			}
			else
			{
				union = 0;
			}
		};

		var grid = Ext.getCmp('escarap');
		grid.on('dblclick', viejo_format);

	}

);

function getPosition(obj)
{
    var topValue= 0,leftValue= 0;
	leftValue+= obj.offsetLeft;
	topValue+= obj.offsetTop;
    finalvalue = leftValue + "," + topValue;
    return finalvalue;
};

function set_tools()
{
	var inf = myElement.replace(/elemento/g, "");
	for(ind=0; ind<elementos_cargados.length; ind++)
	{
		if (elementos_cargados[ind][0] == inf)
		{
			Ext.getCmp('tool1').setValue(elementos_cargados[ind][6]);
			Ext.getCmp('tool2').setValue(elementos_cargados[ind][7]);
			Ext.getCmp('tool3').setValue(elementos_cargados[ind][8]);
			Ext.getCmp('tool4').setValue(elementos_cargados[ind][9]);

			if(elementos_cargados[ind][10] == 'true')
			{
				Ext.getCmp('tool9').setValue(true);
			}
			else
			{
				Ext.getCmp('tool9').setValue(false);
			}
			break;
		}
	}

	if(union == 1)
	{
		union = 0;
		//alert(myElement+'='+lemn_union);
		if(myElement != lemn_union)
		{
			var inf = myElement.replace(/elemento/g, "");

			for(ind=0; ind<elementos_cargados.length; ind++)
			{
				if (elementos_cargados[ind][0] == inf)
				{
					var nom_nuev = elementos_cargados[ind][1];
					break;
				}
			}

			var inf2 = lemn_union.replace(/elemento/g, "");

			for(ind=0; ind<elementos_cargados.length; ind++)
			{
				if (elementos_cargados[ind][0] == inf2)
				{
					var nom_ante = elementos_cargados[ind][1];
					elementos_cargados[ind][1] = nom_ante+'<>'+nom_nuev;
					Ext.Ajax.request
					(
						{
							url 			: 'disenador_formatos/bd/bd.php',
							method			: 'POST',
							params			:
							{
								op			:  'unir_elementos',
								id			:  inf2,
								nombre		:  nom_ante+','+nom_nuev,
								// id_congreso	:  apuntador_congreso
							},
							success		: function (result, request){}
						}
					);
					document.getElementById(lemn_union).innerHTML = nom_ante+'<>'+nom_nuev;
					elim_element();
					break;
				}
			}
		}
		else
		{
			Ext.Msg.alert('Alerta', 'Debe Escoger un Elemento Diferente a el mismo para poder Unirlo');
		}
	}
	check = 1;
};

function elim_element()
{
	var inf = myElement.replace(/elemento/g, "");
	Ext.Ajax.request
	(
		{
			url 		: 'disenador_formatos/bd/bd.php',
			method		: 'GET',
			params		:
			{
				op		: 'Eliminar_elementos',
				id		: inf
			},
			success		: function (result, request){}
		}
	);
	var docBody = document.getElementById(myElement).parentNode;
	var node = document.getElementById(myElement);
	docBody.removeChild(node);
	for(ind=0; ind<elementos_cargados.length; ind++)
	{
		if (elementos_cargados[ind][0] == inf)
		{
			elementos_cargados.splice(ind,1);
			break;
		}
	}
	myElement='';
};

function borar_format()
{
	var grid = Ext.getCmp('escarap');
	if(grid.selModel.getCount() == 1)
	{
		Ext.MessageBox.confirm('Alerta', 'Esta seguro que Desea Borrar Este Formato', Borrarn);
	}
	else
	{
		Ext.MessageBox.alert('Alerta', 'Debe escoger un Formato para Borrar');
	}

	function Borrarn(btn)
	{
		if(btn=='yes')
		{
			var grid = Ext.getCmp('escarap');
			var selections = grid.selModel.getSelections();
			var prez;
			for(i = 0; i< grid.selModel.getCount(); i++)
			{
				prez = (selections[i].json.id);
			}
			var id = prez;
			Ext.Ajax.request
			(
				{
					url 		: 'disenador_formatos/bd/bd.php',
					method		: 'GET',
					params		:
					{
						op		: 'Eliminar_formatos',
						id		: id
					},
					success		: function (result, request)
					{
						Ext.getCmp("escarap").store.load({params: {op: 'formatos', /*id: apuntador_congreso*/}});
					}
				}
			);
		}
	};
};
