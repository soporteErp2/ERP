<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");


?>
<div id="PanelItemGrupos"></div>

<script>


	new Ext.Panel({						//TAB PRINCIPAL
		style 		: 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
		renderTo	: 'PanelItemGrupos',
		border		: false,
        bodyStyle   : 'background-color:#FFF;',
		items:
		[
			{
				region		: 'north',
				xtype		: 'panel',
				height		: 33,
				border		: false,
				margins		: '0 0 0 0',
				bodyStyle 	: 'background-image:url(../../../../temas/clasico/images/fondo_cabecera.png);'
			},
			{
				region			: 'center',
				xtype			: 'tabpanel',
				margins			: '0 0 0 0',
				deferredRender	: true,
				border			: false,
				activeTab		: 0,
				// bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
        		bodyStyle   : 'background-color:#FFF;',
				items			:
				[
					{
						closable	: false,
						autoScroll	: true,
						title		: 'Cuentas Colgaap',
						iconCls 	: 'cotizacion16',
	            		bodyStyle   : 'background-color:#FFF;',
						items		:
						[
							{
								xtype		: "panel",
								id			: 'contenedor_cuentas_default_items',
								border		: false,
								autoLoad	:
								{
									url		: 'items/cuentas_default_grupos/cuentas_default_items.php',
									scripts	: true,
									nocache	: true,
									params : { id_grupo : '<?php echo $id_grupo; ?>',}
								}
							}
						]
					},
					{
						closable	: false,
						autoScroll	: true,
						title		: 'Cuentas Niif',
						iconCls 	: 'cotizacion16',
						items		:
						[
							{
								xtype		: "panel",
								id			: 'contenedor_cuentas_niif_default_items',
								border		: false,
								autoLoad	:
								{
									url		: 'items/cuentas_default_grupos/cuentas_niif_default_items.php',
									scripts	: true,
									nocache	: true,
									params : { id_grupo : '<?php echo $id_grupo; ?>',}
								}
							}
						]
					}
				]
			}
		]
	});



</script>

<style type="text/css">

	.contenedor_items_cuentas{
		margin   : 0 5px 5px 5px;
		width    : calc(100% - 10px);
		overflow : auto;
	}

	.contenedor_items_cuentas input{ width : 100%; }

	.item_cuenta_left{
		margin-right : 6px;
		padding-top  : 20px;
	}

	.item_cuenta_right{
		margin-left : 6px;
		padding-top : 40px;
	}

	.titleItemsCuenta{
		font-size     : 13px;
		margin-bottom : 35px;
		text-align    : center;
	}

	.btnItemsCuentas{
		margin           : 1px 0 0 4px;
		height           : 16px;
		width            : 18px;
		float            : left;
		cursor           : pointer;
		border-radius    : 3px;
		border           : 1px solid #999;
		background-color : #FFF;
		box-shadow       : 1px 1px 3px #999;
		text-align       : center;
		font-weight      : bold;
		color            : #0842a5;
	}

	.btnItemsCuentasEstado{
		margin           : 1px 0 0 -20px;
		height           : 18px;
		width            : 18px;
		float            : left;
		cursor           : pointer;
		border-left      : 1px solid #999;
		background-color : #FFF;
		box-shadow       : 1px 1px 3px #999;
		text-align       : center;
		font-weight      : bold;
		color            : #0842a5;
	}

	.contenedorBtns{ float : left; }

	.cuentaPuc{
		float         : left;
		width         : 260px;
		overflow      : hidden;
		margin-left   : 10px;
		text-overflow : ellipsis;
		white-space   : nowrap;
	}

	.filaCuentasItems{
		overflow : hidden;
		margin   : 15px 5px;
		height   : 20px;
	}

	.filaCuentasItems input{ cursor : pointer; }

</style>
