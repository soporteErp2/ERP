<?php
	include('../../../configuracion/conectar.php');
	include('../../../configuracion/define_variables.php');

	$fecha  = date("Y-m-d");
	$fechai = strtotime ( '-30 day' , strtotime ( $fecha ) );
	$fechai = date("Y-m-d",$fechai);
	$mes    = date("m");
	$ano    = date("Y");


	$sql   = "SELECT id,nombre FROM empresas_sucursales_bodegas WHERE activo=1 AND id_sucursal=".$_SESSION['SUCURSAL'];
	$query = $mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$bodegas .= "{'index':'$row[id]','value':'$row[nombre]'},";
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../misc/Win/css/Win.min.css" />
    <link rel="stylesheet" href="../../../misc/Win/css/Win-theme-blue.min.css" />
    <script type="text/javascript" src="../../..//misc/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="../../../misc/Win/js/Win.min.js"></script>
</head>
<body>

<div id="InformesInventario" style="width:100%; height:100%"></div>

<script>

	$W.Informes({

		id      : 'Informes',
		idApply : 'InformesInventario',
		debug   : true,
		modulos : [
	 		{
				nombre   : 'Kardex',
				id       : 'Bnt1',
				width    : 120,
				icon     : 'sync',				
				informes : [
					{
						text        : "General",
						icon        : "insert_chart",
						file        : "Clases/Kardex_General.php",
						id          : "InfoKardex",
						orientacion : "V",
						items       : [

							{
								xtype        : "textfield",
								label        : "Desde",
								id           : "fechaInicio",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "textfield",
								label        : "Hasta",
								id           : "fechaFin",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "textfield",
								label        : "Cod. item",
								id           : "cod_item",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,

							},
							{
								xtype        : "combobox",
								label        : "Bodega",
								id           : "bodega",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											<?= $bodegas; ?>
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
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_kardex')}
							}
							 //{
							 //	xtype   : "button",
							 //	width   : 65,
							 //	icon    : "picture_as_pdf",
							 //	text    : "Generar PDF",
							 //	handler : function(){$W.HtmlToPdf({
							 //			capa    : "InformeFile_Informes_InfoKardex",
							 //			id      : "Informes_InfoKardex",
							 //			nombre  : "Informe_kardex",
							 //			//target  : 'download',
							 //			path    : "../informes/",
							 //			options :{
							 //				debug:"false"
							 //			}
							 //		})
							 //	}
							 //}
						]
	 				}
	 			]
	 		},
			 {
				nombre   : 'Logs',
				id       : 'Bnt2',
				width    : 120,
				icon     : '\ue8fa',				
				informes : [
					{
						text        : "Por item",
						icon        : "\ue889",
						file        : "Clases/Logs/ByItem.php",
						id          : "LogByItem",
						orientacion : "h",
						items       : [

							{
								xtype        : "textfield",
								label        : "A partir de",
								id           : "fecha",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								value        : "<?php echo $fecha ?>",
								validate 	 : 'date'
							},
							{
								xtype        : "textfield",
								label        : "Cod. item",
								id           : "cod_item",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,

							},
							{
								xtype        : "combobox",
								label        : "Bodega",
								id           : "bodega",
								required     : "true",
								FormMaterial : "false",
								DivAncho     : 200,
								data         : [
											<?= $bodegas; ?>
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
								handler : function(){$W.HtmlTableToExcel('InformeData','Informe_log_por_item')}
							},
							// {
							// 	xtype   : "button",
							// 	width   : 65,
							// 	icon    : "picture_as_pdf",
							// 	text    : "Generar PDF",
							// 	handler : function(){$W.HtmlToPdf({
							// 			capa    : "InformeFile_InformesProductos_InfoKardex",
							// 			id      : "InformesProductos_InfoKardex",
							// 			nombre  : "Informe_kardex",
							// 			//target  : 'download',
							// 			path    : "../informes/",
							// 			options :{
							// 				debug:"false"
							// 			}
							// 		})
							// 	}
							// }
						]
	 				}
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
</body>
</html>
