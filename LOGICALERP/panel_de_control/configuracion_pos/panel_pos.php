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
						title		: 'Configuracion Principal',
						iconCls 	: 'config16',
						bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>; height:100%;',
						items		:
						[
							{
								xtype		: "panel",
								id			: 'contenedor_pos_1',
								border		: false,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad	:
								{
									url		: 'configuracion_pos/pos.php',
									scripts	: true,
									nocache	: true,
									params  : { filtro_sucursal : '<?php echo $filtro_sucursal; ?>' }
								}
							}
						],
						// tbar        :
					 //    [
			   //              {
			   //                  xtype       : 'button',
			   //                  width       : 60,
			   //                  height      : 56,
			   //                  text        : 'Guardar',
			   //                  scale       : 'large',
			   //                  iconCls     : 'guardar',
			   //                  iconAlign   : 'top',
			   //                  hidden      : false,
			   //                  handler     : function(){ guardarconfiguracionPos(this); }
			   //              },'-'
					 //    ]
					},
					{
						closable	: false,
						autoScroll	: true,
						title		: 'Configuracion Cajas',
						iconCls 	: 'pos16',
						bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>; height:100%;',
						items		:
						[
							{
								xtype		: "panel",
								id			: 'contenedor_pos_2',
								border		: false,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								autoLoad	:
								{
									url		: 'configuracion_pos/grilla_cajas.php',
									scripts	: true,
									nocache	: true,
									params  : { filtro_sucursal : '<?php echo $filtro_sucursal; ?>' }
								}
							}
						],
						tbar        :
					    [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Consecutivos',
			                    scale       : 'large',
			                    iconCls     : 'opciones',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ ventanaConfigurarNumero(this); }
			                },'-'
					    ]
					}
				]
			}
		]
	});


</script>