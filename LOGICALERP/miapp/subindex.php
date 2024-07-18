<script>
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
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Mis Opciones',
								iconCls 	: 'opciones16',
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										border		: false,
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										autoLoad	:
											{
												url		:	'misopciones.php',
												scripts	: 	true,
												nocache	:	true
											}
									}
								]
							},
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Carpeta del Empleado',
								iconCls 	: 'carpeta_personal16',
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										border		: false,
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										autoLoad	:
											{
												url		:	'carpeta_empleado.php',
												scripts	: 	true,
												nocache	:	true
											}
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


</script>