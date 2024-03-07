var id_conten_format;
Ext.onReady
(
	function()
	{
		var nuevo_format = new Ext.FormPanel
		(
			{
				frame		: true,
				id			: 'form_format',
				border		: false,
				renderTo	: 'formatos2',
				labelAlign	: 'top',
				style		: 'margin:15px;',
				width		: 235,
				height		: 290,
				items 		:
				[
					{
						xtype 				: "textfield",
						fieldLabel 			: "Nombre",
						id					: 'frms1',
						labelStyle 			: "width:60;font-size:11px",
						name 				: "textvalue",
						allowBlank			: false,
						width				: 220
					},
					{
						xtype 				: "combo",
						fieldLabel 			: "Tipo de Campo",
						labelStyle 	        : "width:60;font-size:11px",
						id					: 'frms2',
						name 				: "combovalue",
						hiddenName 			: "combovalue",
						inputValue 			: "cbvalue",
						displayField		: 'tipo_de_campo',
						valueField			: 'valor',
						triggerAction		: 'all',
						mode				: 'local',
						emptyText			: 'Seleccione ...',
						editable			: false,
						forceSelection		: true,
						allowBlank			: false,
						width				: 220,
						store				: new Ext.data.SimpleStore
						(
							{
								fields		: ['valor','tipo_de_campo'],
								data		:
								[
									['escarapela','Escarapela'],
									['certificado','Cerftificado']
								]
							}
						)
					},
					{
						xtype 				: "textfield",
						id					: 'frms3',
						fieldLabel 			: "Alto (mm)",
						labelStyle 			: "width:60;font-size:11px",
						name		 		: "textvalue",
						allowBlank			: false,
						maskRe				: /^[0-9]$/,
						value				: "120",
						width				: 220
					},
					{
						xtype 				: "textfield",
						id					: 'frms4',
						fieldLabel 			: "Ancho (mm)",
						labelStyle 			: "width:60;font-size:11px",
						maskRe				: /^[0-9]$/,
						allowBlank			: false,
						name 				: "textvalue",
						value				: "100",
						width				: 220
					},
					{
						xtype 				: "combo",
						fieldLabel 			: "Plan",
						id					: 'frms5',
						name 				: "combovalue",
						hiddenName 			: "combovalue",
						inputValue 			: "cbvalue",
						displayField		: 'nombre',
						valueField			: 'id',
						triggerAction		: 'all',
						mode				: 'local',
						emptyText			: 'Seleccione ...',
						editable			: false,
						forceSelection		: true,
						allowBlank			: false,
						store				: storeun,
						width				: 220
					}
				],
				tbar:
				[
					{
						text	: 'Guardar',
						tooltip	: 'Salvar Datos',
						iconCls	: 'guardar16',
						handler	: validar
					}
				]
			}
		);

		function validar()
		{
			var hh = Ext.getCmp('form_format').getForm().isValid();
			if(hh==false)
			{
				Ext.Msg.alert('Alerta','Alguno de los campos estan vacios');
			}
			else
			{
				var al = myDatos=Ext.getCmp("frms3").getValue();
				var an = myDatos=Ext.getCmp("frms4").getValue();
				if(an >= 50 && al >= 50)
				{
					validar_existe();
				}
				else
				{
					Ext.Msg.alert('Alerta', 'El Ancho y el Alto deben ser como minimo 50mm');
				}
			}
		};

		function validar_existe()
		{
			var datin = Ext.getCmp('frms1').getValue();
			Ext.Ajax.request
			(
				{
					url : 'disenador_formatos/bd/bd.php',
					method: 'GET',
					params:
					{
						op	 : 'validar_existe',
						id	 : apuntador_congreso,
						camp : datin
					},
					success: function (result, request)
					{
						var indic = (result.responseText);
						//console.log(indic);
						if(indic != 0)
						{
							Ext.Msg.alert('Alerta', 'El nombre de Este Formato ya Existe,<br>Cambielo y vuelva a Intentarlo')
						}
						else
						{
							Salvar_datos();
						}
					}
				}
			);
		};

		function Salvar_datos()
		{
			var tam = 5;
			var myDatos='';
			var i=1;
			while(i<=tam)
			{
				if(i==1)
				{
					myDatos = Ext.getCmp("frms"+i).getValue();
				}
				else if(i== 3)
				{
					var altt = Ext.getCmp("frms"+i).getValue();
					altt = parseInt((altt/0.28));
					myDatos = myDatos + "{" + altt;
				}
				else if(i == 4)
				{
					var anch = Ext.getCmp("frms"+i).getValue();
					anch = parseInt((anch/0.28));
					myDatos = myDatos + "{" + anch;
				}
				else
				{
					myDatos = myDatos+"{"+Ext.getCmp("frms"+i).getValue();
				}
				i++;
			}

			Ext.Ajax.request
			(
				{
					url 		: 'disenador_formatos/bd/bd.php',
					method		: 'GET',
					params		:
					{
						op		: 'guardar_datos',
						id		: apuntador_congreso,
						datos	: myDatos
					},
					success		: function (result, request)
					{
						var ind = (result.responseText);
						//console.log(ind);
						//id_conten_format();
						Ext.getCmp("escarap").store.load({params: {op: 'formatos', id: apuntador_congreso}});
						Ext.getCmp("altura").setValue(Ext.getCmp("frms3").getValue());
						Ext.getCmp("anchura").setValue(Ext.getCmp("frms4").getValue());
						Ext.getCmp("nombre").setValue(Ext.getCmp("frms1").getValue());
						Ext.getCmp("tip_format").setValue(Ext.getCmp("frms2").getValue());
						Ext.getCmp("tip_plan").setValue(Ext.getCmp("frms5").getValue());
						Ext.getCmp("ventana_new_format").close();
						adicionar_formato();
					}
				}
			);
		};

		function adicionar_formato()
		{
			var anc = Ext.getCmp("anchura").getValue();
			Ext.getCmp("anchura").setValue(anc + ' mm');
			anc = parseInt((anc/0.28)+1);
			var alt = Ext.getCmp("altura").getValue();
			Ext.getCmp("altura").setValue(alt + ' mm');
			alt = parseInt((alt/0.28)+1);

			//alert(anc+';'+alt)
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

			edicion = 1;
		};
	}
);
