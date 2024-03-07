<?php
	include('../../../../../../configuracion/conectar.php');
	include('../../../../../../configuracion/define_variables.php');

	$fecha  = date("Y-m-d");
	$fechai = strtotime ( '-30 day' , strtotime ( $fecha ) );
	$fechai = date("Y-m-d",$fechai);
	$mes    = date("m");
	$ano    = date("Y");


	$sql   = "SELECT id,nombre FROM ventas_pos_secciones WHERE activo=1 AND restaurante='Si' ";
	$query = $mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$dataAmbientes .= "{'index':'$row[id]','value':'$row[nombre]'},";
	}

?>
<style>
	/*Ocultar el panel de campos de informe de productos*/
	#PanelCampos_InformesProductos_InfoProductos{ width: 0px !important; }
	/*#PanelCampos_InformesProductos_InfoProductos{ width: 0px !important; }*/
</style>
<div id="InformesPro" style="width:100%; height:100%"></div>

<script>

	$W.Informes({

		id      : 'InformesProductos',
		idApply : 'InformesPro',
		debug   : true,
		modulos : [
	 		{
				nombre   : 'Informe Productos',
				id       : 'Bnt1',
				width    : 120,
				icon     : 'insert_chart',
				informes : [
					{
						text        : "Informe Diario Productos",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoDiarioProductos",
						id          : "InfoDiarioProductos",
						items       : [

							{
								xtype        : "textfield",
								label        : "Fecha",
								id           : "fecha",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "combobox",
								label        : "Ambiente",
								id           : "ambiente",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"Todos","value":"Todos"},
											<?= $dataAmbientes; ?>
								]
							},
							{
								xtype : "button",
								width : 65,
								icon  : "flash_on",
								text  : "Generar Informe",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_diario_productos')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesProductos_InfoDiarioProductos",
										id      : "InformesProductos_InfoDiarioProductos",
										nombre  : "Informe_diario_productos",
										//target  : 'download',
										path    : "../../../backend/pos_admin/informes/",
										options :{
											debug:"false"
										}
									})
								}
							}
						]
	 				},
	 				{
						text        : "Informe Popularidad Productos",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoPopularidadProductos",
						id          : "InfoPopularidad",
						items       : [
							{
								xtype        : "textfield",
								label        : "Fecha inicio",
								id           : "fechaInicio",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fechai ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "combobox",
								label        : "Ambiente",
								id           : "ambiente",
								required     : "false",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"Todos","value":"Todos"},
											<?= $dataAmbientes; ?>
								]
							},
							{
								xtype        : "textfield",
								xtype        : "textfield",
								label        : "Fecha fin",
								id           : "fechaFin",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "combobox",
								label        : "Orden",
								id           : "orden",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"DESC","value":"Mas vendido"},
											{"index":"ASC","value":"Menos vendido"}
								]
							},
							{
								xtype : "button",
								width : 65,
								icon  : "flash_on",
								text  : "Generar Informe",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_popularidad_productos')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesProductos_InfoPopularidad",
										id      : "InformesProductos_InfoPopularidad",
										nombre  : "Informe_popularidad",
										path    : "../../../backend/pos_admin/informes/",
										options :{
											debug:"false"
										}
									})
								}
							}
						]
	 				},
	 				{
						text        : "Informe Productos",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoProductos",
						id          : "InfoProductos",
						items       : [
							// {
							// 	xtype        : "textfield",
							// 	label        : "Fecha inicio",
							// 	id           : "desde",
							// 	required     : "true",
							// 	FormMaterial : "false",
							// 	DivAncho     : 200,
							// 	value        : "<?php echo $fechai ?>",
							// 	validate 	 : 'date'
							// },
							// {
							// 	xtype        : "combobox",
							// 	label        : "Ambiente",
							// 	id           : "ambiente",
							// 	required     : "false",
							// 	FormMaterial : "false",
							// 	DivAncho     : 200,
							// 	data         : [
							// 				{"index":"Todos","value":"Todos"},
							// 				<?= $dataAmbientes; ?>
							// 	]
							// },
							// {
							// 	xtype        : "textfield",
							// 	xtype        : "textfield",
							// 	label        : "Fecha fin",
							// 	id           : "hasta",
							// 	required     : "true",
							// 	FormMaterial : "false",
							// 	DivAncho     : 200,
							// 	value        : "<?php echo $fecha ?>",
							// 	validate 	 : 'date'
							// },
							// {
							// 	xtype        : "textfield",
							// 	xtype        : "textfield",
							// 	label        : "Cod. Item",
							// 	id           : "cod_item",
							// 	required     : "false",
							// 	FormMaterial : "false",
							// 	DivAncho     : 200,
							// 	value        : "",
							// },
							{
								xtype : "button",
								width : 65,
								icon  : "flash_on",
								text  : "Generar Informe",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_productos')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesProductos_InfoProductos",
										id      : "InformesProductos_InfoProductos",
										nombre  : "Informe_productos",
										//target  : 'download',
										path    : "../../../backend/pos_admin/informes/",
										options :{
											debug:"false"
										}
									})
								}
							}
						]
	 				},
	 				{
						text        : "Informe materia prima",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoIngredientes",
						id          : "InfoIngredientes",
						items       : [
							{
								xtype        : "textfield",
								label        : "Fecha inicio",
								id           : "desde",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fechai ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "combobox",
								label        : "Ambiente",
								id           : "ambiente",
								required     : "false",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"Todos","value":"Todos"},
											<?= $dataAmbientes; ?>
								]
							},
							{
								xtype        : "textfield",
								xtype        : "textfield",
								label        : "Fecha fin",
								id           : "hasta",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "textfield",
								xtype        : "textfield",
								label        : "Cod. Item",
								id           : "cod_item",
								required     : "false",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "",
							},
							{
								xtype : "button",
								width : 65,
								icon  : "flash_on",
								text  : "Generar Informe",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_ingredientes')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesProductos_InfoIngredientes",
										id      : "InformesProductos_InfoIngredientes",
										nombre  : "Informe_ingredientes",
										//target  : 'download',
										path    : "../../../backend/pos_admin/informes/",
										options :{
											debug:"false"
										}
									})
								}
							}

						]
	 				},
	 				{
						text        : "Informe cubiertos",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoCubiertos",
						id          : "InfoCubiertos",
						items       : [
							{
								xtype        : "textfield",
								label        : "Fecha inicio",
								id           : "fechaInicio",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fechai ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "combobox",
								label        : "Ambiente",
								id           : "ambiente",
								required     : "false",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"Todos","value":"Todos"},
											<?= $dataAmbientes; ?>
								]
							},
							{
								xtype        : "textfield",
								xtype        : "textfield",
								label        : "Fecha fin",
								id           : "fechaFin",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype : "button",
								width : 65,
								icon  : "flash_on",
								text  : "Generar Informe",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_cubiertos')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesProductos_InfoCubiertos",
										id      : "InformesProductos_InfoCubiertos",
										nombre  : "Informe_cubiertos",
										path    : "../../../backend/pos_admin/informes/",
										options :{
											debug:"false"
										}
									})
								}
							}
						]
	 				},
	 			]
	 		},

	 		// {
				// nombre   : 'Ventas Productos Finales',
				// id       : 'Bnt2',
				// width    : 120,
				// icon     : 'insert_chart',
	 		// },

	 	]
	})



</script>