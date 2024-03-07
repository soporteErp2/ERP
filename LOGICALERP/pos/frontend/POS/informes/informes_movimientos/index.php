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

	// CONSULTAR LOS CAJEROS
	$sql = "SELECT id,nombre FROM empleados WHERE activo=1 AND token_pos<>''";
	$query = $mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$dataCajeros .= "{'index':'$row[id]','value':'$row[nombre]'},";
	}

?>
<style>
	/*Ocultar el panel de campos de informe de facturas*/
	#PanelCampos_InformesMovimientos_InfoFacturas{ width: 0px !important; }
	#PanelCampos_InformesMovimientos_InfoFacturasAnuladas{ width: 0px !important; }
</style>
<div id="InformesMov" style="width:100%; height:100%"></div>

<script>

	var wizardInfoFacturas = () => {
		WWizard(
					{
						title  : 'Wizard Informe Facturas',
						width  : '450px',
						height : '250px',
						url    : 'informes_movimientos/wizardInfoFacturas.php'
					}
				);
	}

	$W.Informes({
		id      : 'InformesMovimientos',
		idApply : 'InformesMov',
		debug   : true,
		modulos : [
	 		{
				nombre   : 'Informe Documentos',
				id       : 'Bnt1',
				width    : 120,
				icon     : 'insert_chart',
				informes : [
	 				{
						text        : "Informe Cheque Cuenta",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoChequeCuenta",
						id          : "InfoChequeCuenta",
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
								required     : "true",
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
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_cheque_cuenta')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoChequeCuenta",
										id      : "InformesMovimientos_InfoChequeCuenta",
										nombre  : "Informe_cheque_cuenta",
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
						text  : "Informe Facturas",
						icon  : "insert_chart",
						file  : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoFacturaVenta",
						width : 10,
						id    : "InfoFacturas",
						wizard : [
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
								xtype        : "combobox",
								label        : "Tipo",
								id           : "tipo",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"Todos","value":"Todos"},
											{"index":"Facturas","value":"Facturas"},
											{"index":"Cheque Cuenta","value":"Transferencia Cuentas"},
											{"index":"Cortesia","value":"Cortesias"}
								]
							},
							{
								xtype        : "textfield",
								label        : "Num. Factura",
								id           : "numFactura",
								required     : "false",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "",
							},
						],
						items : [
							{
								xtype   : "button",
								width   : 65,
								icon    : "flash_on",
								text    : "Generar",
								// handler : function(){$W.HtmlTableToExcel('InformeData','Informe_tiempos_priorizacion')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_facturas')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoFacturas",
										id      : "InformesMovimientos_InfoFacturas",
										nombre  : "Informe_facturas",
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
						text        : "Informe Comandas",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoComandas",
						id          : "InfoComandas",
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
								required     : "true",
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
								xtype        : "combobox",
								label        : "Estado",
								id           : "estado",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":" ","value":"Todos"},
											{"index":"1","value":"Abiertas"},
											{"index":"2","value":"Cerradas"},
											{"index":"3","value":"Anuladas"}
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
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_comandas')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoComandas",
										id      : "InformesMovimientos_InfoComandas",
										nombre  : "Informe_comandas",
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
						text   : "Informe Anulacion",
						icon   : "insert_chart",
						file   : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoFacturasAnuladas",
						id     : "InfoAnulacion",
						id     : "InfoFacturasAnuladas",
						wizard : [
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
								xtype        : "combobox",
								label        : "Tipo",
								id           : "tipo",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"Todos","value":"Todos"},
											{"index":"Facturas","value":"Facturas"},
											{"index":"Cheque Cuenta","value":"Transferencia Cuentas"},
											{"index":"Cortesia","value":"Cortesias"}
								]
							},
							{
								xtype        : "textfield",
								label        : "Num. Factura",
								id           : "numFactura",
								required     : "false",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "",
							},
						],
						items : [
							{
								xtype   : "button",
								width   : 65,
								icon    : "flash_on",
								text    : "Generar",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_facturas_anuladas')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoFacturasAnuladas",
										id      : "InformesMovimientos_InfoFacturasAnuladas",
										nombre  : "Informe_facturas_anuladas",
										//target  : 'download',
										path    : "../../../backend/pos_admin/informes/",
										options :{
											debug:"false"
										}
									})
								}
							}
						]
	 				}
	 			]
	 		},
	 		{
				nombre   : 'Informes de Caja',
				id       : 'Bnt2',
				width    : 120,
				icon     : 'insert_chart',
				informes : [

	 				{
						text        : "Informe Caja",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoCajas",
						id          : "InfoCaja",
						items       : [
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
								label        : "Cajero",
								id           : "cajero",
								required     : "false",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"Todos","value":"Todos"},
											<?= $dataCajeros; ?>
								]
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "flash_on",
								text    : "Generar",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_cajas')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoCaja",
										id      : "InformesMovimientos_InfoCaja",
										nombre  : "Informe_cajas",
										//target  : "download",
										path    : "../../../backend/pos_admin/informes/",
										options : {
											debug:"false"
										}
									})
								}

							}
						]

	 				},
	 				{
						text        : "Informe Caja detallado",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoCajasDetallado",
						id          : "InfoCaja",
						items       : [
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
								xtype        : "textfield",
								label        : "Fecha Inicio",
								id           : "fecha_inicio",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "combobox",
								label        : "Cajero",
								id           : "cajero",
								required     : "false",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											{"index":"Todos","value":"Todos"},
											<?= $dataCajeros; ?>
								]
							},
							{
								xtype        : "textfield",
								label        : "Fecha Final",
								id           : "fecha_final",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "flash_on",
								text    : "Generar",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_cajas')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoCaja",
										id      : "InformesMovimientos_InfoCaja",
										nombre  : "Informe_cajas",
										//target  : "download",
										path    : "../../../backend/pos_admin/informes/",
										options : {
											debug:"false"
										}
									})
								}

							}
						]

	 				},

	 			]
	 		},
	 		{
				nombre   : 'Informe Movimientos',
				id       : 'Bnt3',
				width    : 120,
				icon     : 'insert_chart',
				informes : [
	 				{
						text        : "Comprobante diario",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoComprobanteDiario",
						id          : "InfoComprobanteDiario",
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
								xtype   : "button",
								width   : 65,
								icon    : "flash_on",
								text    : "Generar",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoComprobanteDiario",
										id      : "InformesMovimientos_InfoComprobanteDiario",
										nombre  : "Informe_comprobante_diario",
										//target  : "download",
										path    : "../../../backend/pos_admin/informes/",
										options : {
											debug:"false"
										}
									})
								}

							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "desktop_windows",
								text    : "Nombre Equipo",
								handler : function(){setPcName();}
							},
						]
	 				},
	 				{
						text        : "Informe Propinas",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoPropinas",
						id          : "InfoPropina",
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
								xtype   : "button",
								width   : 65,
								icon    : "flash_on",
								text    : "Generar",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_propinas')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoPropina",
										id      : "InformesMovimientos_InfoPropina",
										nombre  : "Informe_propina",
										//target  : "download",
										path    : "../../../backend/pos_admin/informes/",
										options : {
											debug:"false"
										}
									})
								}

							}
						]
	 				},
	 				{
						text        : "Informe Descuentos",
						icon        : "insert_chart",
						file        : "../../../backend/pos_admin/informes/Controller.php?method=ClassInfoDescuentos",
						id          : "InfoDescuentos",
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
								xtype   : "button",
								width   : 65,
								icon    : "flash_on",
								text    : "Generar",
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "arrow_downward",
								text    : "Generar Excel",
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_descuentos')}
							},
							{
								xtype   : "button",
								width   : 65,
								icon    : "picture_as_pdf",
								text    : "Generar PDF",
								handler : function(){$W.HtmlToPdf({
										capa    : "InformeFile_InformesMovimientos_InfoDescuentos",
										id      : "InformesMovimientos_InfoDescuentos",
										nombre  : "Informe_descuentos",
										//target  : "download",
										path    : "../../../backend/pos_admin/informes/",
										options : {
											debug:"false"
										}
									})
								}

							}
						]
	 				},

	 			]
	 		},
	 	]
 	})

	var setPcName = ()=>{
		$W.Prompt({
			title : "Nombre de este equipo",
			text  : "Por favor ingrese en nombre exacto de este equipo, el cual aparecera en este informe",
			success : function (evt,value) {
				localStorage.pcName = value;
			},
			cancel : function () {
				console.log(false);
			},
		})
	}

</script>