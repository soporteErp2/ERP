<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
?>
	<div id="PanelItem"></div>
	<div id="divPadreModalUploadFile" class="fondo_modal_upload_file">
		<div>
			<div>
				<div>
					<div id="div_upload_file">
						<div>Arrastre el archivo excel o csv.</div>
					</div>
					<div class="btn_div_upload_file2" style="margin-left:350px;" onclick="window.open('items/bd/formato_items.xls')">&darr;</div>
					<div class="btn_div_upload_file2" onclick="close_ventana_upload_file()">X</div>
				</div>
			</div>
		</div>
	</div>
	<div id="loadForm" style="display:none;"></div>
<script>
	var globalNameFileUpload = '';

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
					//activeTab		: 0,
					bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
					items			:
					[
						{
							closable	: false,
							autoScroll	: true,
							title		: 'Familia y Grupo Items',
							iconCls 	: 'cubos16',
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
										url		: 'items/items_familia.php',
										scripts	: true,
										nocache	: true
									}
								}
							]
						},
						{
							closable	: false,
							autoScroll	: true,
							title		: 'Items',
							iconCls 	: 'cubos16',
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
										url		: 'items/items.php',
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