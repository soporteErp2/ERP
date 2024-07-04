<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
?>
<div id="PanelPos"></div>

<script>


	new Ext.Panel({
		style    : 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
		renderTo : 'PanelPos',
		border   : false,
		items    :
		[
			{
				region		: 'north',
				xtype		: 'panel',
				height		: 33,
				border		: false,
				margins		: '0 0 0 0',
				bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);',
			},
			{
				region			: 'center',
				xtype			: 'tabpanel',
				margins			: '0 0 0 0',
				deferredRender	: true,
				border			: false,
				//activeTab		: 0,
				bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
				items			:
				[
					{
						closable	: false,
						autoScroll	: true,
						title		: 'Estado De Resultados',
						iconCls 	: 'informe0',
						bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						items		:
						[
							{
								xtype		: "panel",
								id			: 'contenedor_pos_1',
								border		: false,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad	:
								{
									url		: 'configuracion_informes_niif/configuracion_estado_resultado.php',
									scripts	: true,
									nocache	: true
								}
							}
						]
					},
					{
						closable	: false,
						autoScroll	: true,
						title		: 'Estado de Resultado Integral',
						iconCls 	: 'informe0',
						bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						items		:
						[
							{
								xtype		: "panel",
								id			: 'contenedor_pos_2',
								border		: false,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad	:
								{
									url		: 'configuracion_informes_niif/configuracion_estado_resultado_integral.php',
									scripts	: true,
									nocache	: true
								}
							}
						]
					},
					{
						closable	: false,
						autoScroll	: true,
						title		: 'Estado de Flujo de Efectivo',
						iconCls 	: 'informe0',
						bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						items		:
						[
							{
								xtype		: "panel",
								id			: 'contenedor_pos_3',
								border		: false,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad	:
								{
									url		: 'configuracion_informes_niif/configuracion_estado_flujo_efectivo.php',
									scripts	: true,
									nocache	: true
								}
							}
						]
					}
				]
			}
		]
	});



</script>