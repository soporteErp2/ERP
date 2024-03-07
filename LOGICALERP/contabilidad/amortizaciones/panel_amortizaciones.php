<?php
	include("../../../configuracion/conectar.php");

	$permiso_diferidos    = (user_permisos(210,'false') == 'true')? 'false' : 'true';
	$permiso_amortizacion = (user_permisos(211,'false') == 'true')? 'false' : 'true';

 ?>
<div class="ContenedorGrupoPanelControl" style="margin-top:20px;padding-left:50px;">
	<?php
		if($permiso_diferidos =='false'){ ?>
	<div class="IconoPanelControl" onClick="AbreVentanaDiferido()">
		<div class="IconoPanelControlimg"><img src="img/agregar.png" width="44" height="44"></div>
		<div class="IconoPanelControltxt">Registrar Diferidos</div>
	</div>
	<?php }
		if($permiso_amortizacion =='false'){ ?>
	<div class="IconoPanelControl" onClick="AbreVentanaAmortizacion();">
		<div class="IconoPanelControlimg"><img src="img/procesar.png" width="44" height="44"></div>
		<div class="IconoPanelControltxt">Amortizacion</div>
	</div>
	<?php } ?>
</div>
<script>

	function AbreVentanaDiferido() {
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_panel_amortizaciones = new Ext.Window({
		    width       : 800,
		    height      : 700,
		    id          : 'Win_Ventana_panel_amortizaciones',
		    title       : 'Diferidos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'amortizaciones/diferidos/diferidos.php',
		        scripts : true,
		        nocache : true,
		        params  : {
		        	opcGrillaContable : 'Extractos',
		        }
		    },
		    bodyStyle 	: 'background-color:#FFF;',
			items		:
			[
				{
					xtype		: "panel",
					id			: 'contenedor_Extractos',
					border		: false,
				}
			],
		}).show();
	}

	function AbreVentanaAmortizacion() {
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_panel_amortizaciones = new Ext.Window({
		    width       : 800,
		    height      : 700,
		    id          : 'Win_Ventana_panel_amortizaciones',
		    title       : 'Amortizacion',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'amortizaciones/amortizacion/grillaContable.php',
		        scripts : true,
		        nocache : true,
		        params  : {
		        			opcGrillaContable : 'Amortizacion',
		        }
		    },
		    bodyStyle 	: 'background-color:#FFF;',
			items		:
			[
				{
					xtype		: "panel",
					id			: 'contenedor_Amortizacion',
					border		: false,
				}
			],
			tbar		:
			[
				{
					xtype	: 'buttongroup',
					id      : 'BtnGroup_Guardar_Amortizacion',
					height  : 80,
					style   : 'border:none;',
					columns	: 1,
					title	: 'Generar',
					items	:
					[
						{
		                    xtype       : 'button',
		                    id     		: 'Btn_guardar_Amortizacion',
		                    width		: 60,
							height		: 56,
		                    text        : 'Guardar',
		                    tooltip		: 'Genera Amortizacion',
		                    scale       : 'large',
		                    disabled 	: true,
		                    iconCls     : 'guardar',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); guardarAmortizacion() }
		                }
		            ]
		        },
				{
					xtype	: 'buttongroup',
					height  : 80,
					style   : 'border:none;',
					columns	: 3,
					title	: 'Opciones',
					items	:
					[
						{
		                    xtype       : 'button',
		                    width		: 60,
							height		: 56,
							id 			: 'btnNuevaAmortizacion',
		                    text        : 'Nuevo',
		                    tooltip		: 'Nueva Amortizacion',
		                    scale       : 'large',
		                    iconCls     : 'add_new',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); nuevaAmortizacion() }
		                },
		                {
		                    xtype       : 'button',
		                    width		: 60,
							height		: 56,
		                    text        : 'Buscar',
		                    tooltip		: 'Buscar Amortizacion',
		                    scale       : 'large',
		                    iconCls     : 'buscar_doc_new',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); buscarAmortizacion() }
		                },
		                {
		                    xtype       : 'button',
		                    id 			: 'Btn_cancelar_Amortizacion',
		                    width		: 60,
							height		: 56,
		                    text        : 'Cancelar',
		                    tooltip		: 'Eliminar Amortizacion',
		                    scale       : 'large',
		                    iconCls     : 'cancel',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); cancelarAmortizacion() }
		                }
		            ]
                },'-',
				{
					xtype   : 'buttongroup',
					height  : 80,
					style   : 'border:none;',
					columns : 1,
					title   : 'Diferidos',
					id      : 'BtnGroup_cargar_diferidos',
					items   :
					[
						{
		                    xtype       : 'button',
		                    width		: 60,
							height		: 56,
							id 			: '',
		                    text        : 'Cargar Diferidos',
		                    tooltip		: 'Cargar Diferidos',
		                    scale       : 'large',
		                    iconCls     : 'carga_doc',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); cargarDiferidos() }
		                },
		            ]
                },'-',
                {
                    xtype   : 'buttongroup',
                    height  : 80,
                    id      : 'BtnGroup_Estado1_Amortizacion',
                    columns : 3,
                    title   : 'Documento Generado',
                    items   :
                    [
						{
		                    xtype       : 'button',
		                    id			: 'btnImprimirAmortizacion',
		                    width		: 60,
							height		: 56,
		                    text        : 'Imprimir',
		                    tooltip		: 'Imprimir en un documento PDF',
		                    scale       : 'large',
		                    iconCls     : 'pdf32_new',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); imprimirAmortizacion(); },
		                },
						{
		                    xtype       : 'button',
		                    id 			: 'Btn_editar_Amortizacion',
		                    width		: 60,
							height		: 56,
		                    text        : 'Editar',
		                    tooltip		: 'Editar Amortizacion',
		                    scale       : 'large',
		                    iconCls     : 'edit',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); modificarDocumentoAmortizacion(); }
		                },
		                {
		                    xtype       : 'button',
		                    id 			: 'Btn_restaurar_Amortizacion',
		                    width		: 60,
							height		: 56,
		                    text        : 'Restaurar',
		                    tooltip		: 'Restaurar Amortizacion',
		                    scale       : 'large',
		                    iconCls     : 'restaurar32',
		                    iconAlign   : 'top',
		                    handler     : function(){ BloqBtn(this); restaurarAmortizacion(); }
		                }
					]
				},'->',
                {
					xtype : "tbtext",
					text  : '<div id="titleDocumentoAmortizacion" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
					scale : "large",
                }
			]
		}).show();
	}

</script>