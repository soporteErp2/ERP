<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
?>
<div id="PanelItem"></div>

<script>


	new Ext.Panel//TAB PRINCIPAL
	(
		{

		style 		: 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
		renderTo	: 'PanelItem',
		border		: false,
		items:
			[
				{
					region		: 'north',
					xtype		: 'panel',
					height		: 33,
					border		: false,
					margins		: '0 0 0 0',
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
						{
							closable	: false,
							autoScroll	: true,
							title		: 'Cartera Clientes',
							iconCls 	: 'doc',
							bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							items		:
							[
								{
									xtype		: "panel",
									id			: 'contenedor_item_1',
									border		: false,
									bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
									autoLoad	:
									{
										url		: 'cuentas_deterioro_cartera_niif/cuentas_deterioro_cartera_clientes.php',
										scripts	: true,
										nocache	: true
									}
								}
							]
						},
						{
							closable	: false,
							autoScroll	: true,
							title		: 'Cartera Proveedores',
							iconCls 	: 'doc',
							bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
							items		:
							[
								{
									xtype		: "panel",
									id			: 'contenedor_item_2',
									border		: false,
									bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
									autoLoad	:
									{
										url		: 'cuentas_deterioro_cartera_niif/cuentas_deterioro_cartera_proveedores.php',
										scripts	: true,
										nocache	: true
									}
								}
							]
						}
					]
				}
			]
		}
	);



</script>