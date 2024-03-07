Ext.onReady
(
	function()
	{
		var store_camp = new Ext.data.Store
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
						id				    : 'campo',
						fields			   	:	
						[
							'campo', 
							'nombre_campo'
						]
					}
				),
				remoteSort: true
			}
		);
		store_camp.load({params: {op: 'campos', id: apuntador_congreso}});
		
		var nuevo_campp = new Ext.FormPanel
		(
			{
				frame		: true,
				id			: 'form_campp',
				renderTo	: 'formatos3',
				labelAlign	: 'top',
				width		: 238,
				border		: false,
				style		: 'margin:15px;',
				height		: 325,
				items 		:
				[
					{
						xtype 				: "combo",
						fieldLabel 			: "Campo",
						id					: 'cmpp1',
						name 				: "combovalue",
						hiddenName 			: "combovalue",
						inputValue 			: "cbvalue",
						displayField		: 'nombre_campo',
						valueField			: 'campo',
						triggerAction		: 'all',
						mode				: 'local',
						emptyText			: 'Seleccione ...',
						editable			: false,
						forceSelection		: true,
						allowBlank			: true,
						store				: store_camp,
						width				: 220
					},
					{
						xtype 				: "combo",
						fieldLabel 			: "Fuente",
						id					: 'cmpp2',
						name 				: "combovalue",
						hiddenName 			: "combovalue",
						inputValue 			: "cbvalue",
						displayField		: 'tipo_de_fuente',
						valueField			: 'tipo_de_fuente',
						triggerAction		: 'all',
						mode				: 'local',
						emptyText			: 'Seleccione ...',
						value				: 'Helvetica',
						editable			: false,
						forceSelection		: true,
						allowBlank			: false,
						width				: 220,
						store				: new Ext.data.SimpleStore
						(
							{
								fields		: ['tipo_de_fuente'],
								data		: 
								[
									['Helvetica'],
									['Times'],
									['Courier'],
									['dejavusans'],
									['freemono'],
									['freesans'],
									['impact'],
									['segoeprb']
								]
							}
						)
					},
					{
						xtype				: 'textfield',//'spinnerfield',
						fieldLabel 			: "Tama�o de la fuente",
						id					: 'cmpp3',
						name				: 'test',
						actionMode			: 'wrap',
						width				: 220,
						minValue			: 8,
						maxValue			: 50,
						value				: 20,
						incrementValue		: 1,
						maskRe				: /^[0-9]$/,
						accelerate			: true
					},
					{
						xtype 				: "combo",
						fieldLabel 			: "Estilo de Fuente",
						id					: 'cmpp4',
						name 				: "combovalue",
						hiddenName 			: "combovalue",
						inputValue 			: "cbvalue",
						displayField		: 'estilo',
						valueField			: 'valor',
						triggerAction		: 'all',
						mode				: 'local',
						emptyText			: 'Seleccione ...',
						value				: 'normal',
						editable			: false,
						forceSelection		: true,
						allowBlank			: false,
						width				: 220,
						store				: new Ext.data.SimpleStore
						(
							{
								fields		: ['valor','estilo'],
								data		: 
								[
									['normal', 'Normal'],
									['bold','Negrilla']
								]
							}
						)
					},
					{
						xtype 				: "combo",
						fieldLabel 			: "Alineacion Fuente",
						id					: 'cmpp5',
						name 				: "combovalue",
						hiddenName 			: "combovalue",
						inputValue 			: "cbvalue",
						displayField		: 'tipo_de_fuente',
						valueField			: 'valor',
						triggerAction		: 'all',
						mode				: 'local',
						emptyText			: 'Seleccione ...',
						value				: 'center',
						editable			: false,
						forceSelection		: true,
						allowBlank			: false,
						width				: 220,
						store				: new Ext.data.SimpleStore
						(
							{
								fields		: ['valor','tipo_de_fuente'],
								data		: 
								[
									['center','Centrada'],
									['justify','Justificada'],
									['left','Izquierda'],
									['right','Derecha']
								]
							}
						)
					},
					{
						xtype 				: "checkbox",
						fieldLabel 			: "C�digo",
						name 				: "checkbox",
						id					: 'cmpp6',
						inputValue 			: "cbvalue"
					}
				],
				tbar:
				[
					{
						text	: 'Guardar',
						tooltip	: 'Salvar Datos',
						iconCls	: 'guardar',
						handler	: validar
					}
				]
			}
		);
		
		function validar()
		{
			var hh = Ext.getCmp('form_campp').getForm().isValid();
			if(hh==false)
			{
				Ext.Msg.alert('Alerta','Alguno de los campos estan vacios');
			}
			else
			{
				var tam = myDatos=Ext.getCmp("cmpp3").getValue();
				if(tam >= 8)
				{
					if(tam <= 50)
					{
						tomar_id();
					}
					else
					{
						Ext.Msg.alert('Alerta', 'El Tama�o Maximo de la Fuente debe ser 50');
					}
				}
				else
				{
					Ext.Msg.alert('Alerta', 'El Tama�o minimo de la Fuente debe ser 8');
				}
			}
		};
		
		function tomar_id()
		{
			var nombb = Ext.getCmp("nombre").getValue();
			Ext.Ajax.request
			(
				{
					url 		: 'disenador_formatos/bd/bd.php',
					method		: 'GET',
					params		:	
					{
						op		: 'obtener_id',
						id		: apuntador_congreso,
						camp	: nombb
					},
					success		: function (result, request)
					{
						var ind = (result.responseText);
						Salvar_datos(ind);
					}
				}
			);
		};
		
		function Salvar_datos(ind) 
		{
			var tam = 6;
			var myDatos = ind;
			var i=1;
			while(i<=tam)
			{
				myDatos = myDatos+"{"+Ext.getCmp("cmpp"+i).getValue();
				i++;
			}
			
			Ext.Ajax.request
			(
				{
					url 		: 'disenador_formatos/bd/bd.php',
					method		: 'GET',
					params		:	
					{
						op		: 'guardar_elemento',
						datos	: myDatos
					},
					success		: function (result, request)
					{
						var ind = (result.responseText);
						//console.log(ind);
						adicionar_elemento(ind);
						Ext.getCmp("ventana_new_element").close();
					}
				}
			);
		};
		
		function adicionar_elemento(ind)
		{
			var elt1 = Ext.getCmp("cmpp1").getRawValue();
			var elt2 = Ext.getCmp("cmpp2").getValue();
			var elt3 = Ext.getCmp("cmpp3").getValue();
			var elt4 = Ext.getCmp("cmpp4").getValue();
			var elt5 = Ext.getCmp("cmpp5").getValue();
			var elt6 = Ext.getCmp("cmpp6").getValue();

			Ext.getCmp("tool1").setValue(elt2);
			Ext.getCmp("tool2").setValue(elt3);
			Ext.getCmp("tool3").setValue(elt4);
			Ext.getCmp("tool4").setValue(elt5);
			//Ext.getCmp("tool9").setValue(elt6);
			
			var newelemnt = document.createElement('div');
			newelemnt.setAttribute('id', 'elemento'+ind);
			newelemnt.setAttribute('class', 'drsElement drsMoveHandle');
			newelemnt.setAttribute('style', 'left: 75px; top: 122px; width: 178px; height: 37px; font-size:'+elt3+'px; font-weight:'+elt4+'; font-family:'+elt2+'; background-color:#FFFFFF; z-index:2;');
			newelemnt.setAttribute('align', elt5);
			newelemnt.setAttribute('onMouseDown', "myElement = this.id; check = 0;");
			newelemnt.setAttribute('onClick', 'set_tools();');
			var bot = document.getElementById('bankname');
			bot.appendChild(newelemnt);
			document.getElementById('elemento'+ind).innerHTML = elt1;
			
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
			
			var elementos = new Array(ind, elt1, '178', '72', '122', '75', elt2, elt3, elt4, elt5, elt6);
			var i = elementos_cargados.length;
			elementos_cargados[i] = elementos;
			
			if(elt6 == true)
			{
				var barcode = document.createElement('div');
				barcode.setAttribute('id', 'barcode'+ind);
				barcode.setAttribute('style', 'position:absolute; left: 0px; top: 0px; width: 16px; height: 16px; background-image: url(disenador_formatos/imagenes/barcode.png)');
				var botq = document.getElementById('elemento'+ind);
				botq.appendChild(barcode);
			}
			
			Ext.getCmp('tool7').setValue('PosX: 21mm');
			Ext.getCmp('tool8').setValue('PosY: 34mm');
		};		
	}
);