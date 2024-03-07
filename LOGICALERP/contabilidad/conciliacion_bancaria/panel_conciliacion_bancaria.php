<div class="ContenedorGrupoPanelControl" style="margin-top:20px;padding-left:50px;">
	<div class="IconoPanelControl" onClick="AbreVentanaExtracto()">
		<div class="IconoPanelControlimg"><img src="img/extracto.png" width="44" height="44"></div>
		<div class="IconoPanelControltxt">Extractos</div>
	</div>
	<div class="IconoPanelControl" onClick="AbreVentanaConciliacion();">
		<div class="IconoPanelControlimg"><img src="img/conciliacion.png" width="44" height="44"></div>
		<div class="IconoPanelControltxt">Conciliacion</div>
	</div>
</div>

<script>

	function AbreVentanaExtracto() {
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_panel_conciliacion = new Ext.Window({
		    width       : 750,
		    height      : 550,
		    id          : 'Win_Ventana_panel_conciliacion',
		    title       : 'Extractos',
		    modal       : true,
		    autoScroll  : true,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'conciliacion_bancaria/extracto/grillaContable.php',
		        scripts : true,
		        nocache : true,
		        params  :
						{
		        	opcGrillaContable : 'Extractos',
		        }
		    },
		    bodyStyle 	: 'background-color:#FFF;',
				items				:
				[
					{
						xtype		: "panel",
						id			: 'contenedor_Extractos',
						border	: false,
					}
				],
				tbar				:
				[
					{
						xtype		: 'buttongroup',
						id      : 'BtnGroup_Guardar_Extractos',
						height  : 80,
						style   : 'border:none;',
						columns	: 1,
						title		: 'Generar',
						items		:
						[
							{
                xtype       : 'button',
                id     			: 'Btn_guardar_Extractos',
                width				: 60,
								height			: 56,
                text        : 'Guardar',
                tooltip			: 'Genera Extracto',
                scale       : 'large',
                disabled 		: true,
                iconCls     : 'guardar',
                iconAlign   : 'top',
                handler     : function(){ BloqBtn(this); guardarExtractos() }
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
                width				: 60,
								height			: 56,
								id 					: 'btnNuevaExtractos',
                text        : 'Nuevo',
                tooltip			: 'Nuevo Extracto',
                scale       : 'large',
                iconCls     : 'add_new',
                iconAlign   : 'top',
                handler     : function(){ BloqBtn(this); nuevaExtractos() }
            	},
		          {
                xtype     	: 'button',
                width				: 60,
								height			: 56,
                text      	: 'Buscar',
                tooltip			: 'Buscar Extracto',
                scale     	: 'large',
                iconCls   	: 'buscar_doc_new',
                iconAlign 	: 'top',
                handler   	: function(){ BloqBtn(this); buscarExtractos() }
              },
              {
                xtype       : 'button',
                id 					: 'Btn_cancelar_Extractos',
                width				: 60,
								height			: 56,
                text        : 'Cancelar',
                tooltip			: 'Eliminar Extracto',
                scale       : 'large',
                iconCls     : 'cancel',
                iconAlign   : 'top',
                handler     : function(){ BloqBtn(this); cancelarExtractos() }
              }
		        ]
          },'-',
          {
            xtype   : 'buttongroup',
            height  : 80,
            id      : 'BtnGroup_Estado1_Extractos',
            columns : 4,
            title   : 'Documento Generado',
            items   :
            [
							{
                xtype       : 'button',
                id					: 'btnExportarExtractos',
                width				: 60,
								height			: 56,
                text        : 'Imprimir',
                tooltip			: 'Imprimir en un documento PDF',
                scale       : 'large',
                iconCls     : 'pdf32_new',
                iconAlign   : 'top',
                handler     : function(){ BloqBtn(this); imprimirExtractos(); },
		          },
							{
                xtype       : 'button',
                id 					: 'Btn_editar_Extractos',
                width				: 60,
								height			: 56,
                text        : 'Editar',
                tooltip			: 'Editar Extracto',
                scale       : 'large',
                iconCls     : 'edit',
                iconAlign   : 'top',
                handler     : function(){ BloqBtn(this); modificarDocumentoExtractos(); }
              },
              {
                xtype       : 'button',
                id 					: 'Btn_restaurar_Extractos',
                width				: 60,
								height			: 56,
                text        : 'Restaurar',
                tooltip			: 'Restaurar Extracto',
                scale       : 'large',
                iconCls     : 'restaurar32',
                iconAlign   : 'top',
                handler     : function(){ BloqBtn(this); restaurarExtractos(); }
              }
						]
					},'->',
          {
						xtype : "tbtext",
						text  : '<div id="titleDocumentoExtractos" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
						scale : "large",
          }
			]
		}).show();
	}

	function AbreVentanaConciliacion() {

		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_panel_conciliacion = new Ext.Window({
		    width       : 1500,
		    height      : 650,
		    id          : 'Win_Ventana_panel_conciliacion',
		    title       : 'Conciliacion',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'conciliacion_bancaria/conciliacion/grillaContable.php',
		        scripts : true,
		        nocache : true,
		        params  : {
		        			opcGrillaContable : 'conciliaciones',
		        }
		    },
		    bodyStyle 	: 'background-color:#FFF;',
			items		:
			[
				{
					xtype		: "panel",
					id			: 'contenedor_conciliaciones',
					border		: false,
				}
			],
			tbar		:
			[
				{
					xtype	: 'buttongroup',
					id      : 'BtnGroup_Guardar_conciliaciones',
					height  : 80,
					style   : 'border:none;',
					columns	: 1,
					title	: 'Generar',
					items	:
					[
						{
		                    xtype       : 'button',
		                    id     		: 'Btn_guardar_conciliaciones',
		                    width		: 60,
							height		: 56,
		                    text        : 'Guardar',
		                    tooltip		: 'Genera Cotizacion',
		                    scale       : 'large',
		                    disabled 	: true,
		                    iconCls     : 'guardar',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); guardarconciliaciones() }
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
							id 			: 'btnNuevaconciliaciones',
		                    text        : 'Nuevo',
		                    tooltip		: 'Nueva Cotizacion',
		                    scale       : 'large',
		                    iconCls     : 'add_new',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); nuevaconciliaciones() }
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
		                    handler     : function(){ BloqBtn(this); buscarconciliaciones() }
		                },
		                {
		                    xtype       : 'button',
		                    id 			: 'Btn_cancelar_conciliaciones',
		                    width		: 60,
							height		: 56,
		                    text        : 'Cancelar',
		                    tooltip		: 'Eliminar Cotizacion',
		                    scale       : 'large',
		                    iconCls     : 'cancel',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); cancelarconciliaciones() }
		                }
		            ]
                },'-',
                {
                    xtype   : 'buttongroup',
                    height  : 80,
                    id      : 'BtnGroup_Estado1_conciliaciones',
                    columns : 4,
                    title   : 'Documento Generado',
                    items   :
                    [
						{
		                    xtype       : 'button',
		                    id			: 'btnExportarconciliaciones',
		                    width		: 60,
							height		: 56,
		                    text        : 'Imprimir',
		                    tooltip		: 'Imprimir en un documento PDF',
		                    scale       : 'large',
		                    iconCls     : 'pdf32_new',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); imprimirconciliaciones(); },
		                },
						{
		                    xtype       : 'button',
		                    id 			: 'Btn_editar_conciliaciones',
		                    width		: 60,
							height		: 56,
		                    text        : 'Editar',
		                    tooltip		: 'Editar Cotizacion',
		                    scale       : 'large',
		                    iconCls     : 'edit',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); modificarDocumentoconciliaciones(); }
		                },
		                {
		                    xtype       : 'button',
		                    id 			: 'Btn_restaurar_conciliaciones',
		                    width		: 60,
							height		: 56,
		                    text        : 'Restaurar',
		                    tooltip		: 'Restaurar Cotizacion',
		                    scale       : 'large',
		                    iconCls     : 'restaurar32',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); restaurarconciliaciones(); }
		                }
					]
				},'->',
                {
					xtype : "tbtext",
					text  : '<div id="titleDocumentoconciliaciones" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
					scale : "large",
                }
			]
		}).show();
	}

</script>
