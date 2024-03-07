<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	ob_start();

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	$id_empresa       = $_SESSION['EMPRESA'];
	$labelConsecutivo = 'No.';
	$titulo           = 'EXTRACTO';

	//CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento, pais,ciudad,direccion,razon_social,telefono,celular FROM empresas WHERE id = '$id_empresa' LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$nombre_empresa        	= mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre 	= mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     	= mysql_result($queryEmpresa,0,'documento');
	$ubicacion_empresa     	= mysql_result($queryEmpresa,0,'ciudad') . ' - ' . mysql_result($queryEmpresa,0,'pais');
	$direccion_empresa     	= mysql_result($queryEmpresa,0,'direccion');
	$razon_social          	= mysql_result($queryEmpresa,0,'razon_social');
	$telefonos 			   			= mysql_result($queryEmpresa,0,'telefono') . ' - ' . mysql_result($queryEmpresa,0,'celular');

	//CONSULTAR LA INFORMACION DE LA CABECERA
	$sqlHead    = "SELECT * FROM $tablaPrincipal WHERE id = '$id_documento' AND activo = 1 AND id_empresa = $id_empresa";
	$queryHead  = mysql_query($sqlHead,$link);

	//============= ARMAMOS CABECERA Y PIE DE PAGINA DEL DOCUMENTO =============//
	while($row = mysql_fetch_array($queryHead)){

		if($row['estado'] == 1 || $row['estado'] == 2){
			$estadoDocumento = "Documento Generado";
		}
		else if($row['estado'] == 3){
			$estadoDocumento = "Documento Cancelado";
			$styleCancelado = "color: red;";
		}

		$header =  '<div id="body_pdf" style="width:100%; font-style:normal;">
									<div style="float:left; width:60%; text-align:center; font-size: 17px;">
										<b>'.$razon_social.'</b><br>'.$tipo_documento_nombre.' : <b>'.$documento_empresa.'</b><br>'.$direccion_empresa.'<br><b>Tels:</b>'.$telefonos.'<br>'.$ubicacion_empresa.'<br>
									</div>
									<div style="float:right; width:40%; text-align:center; font-size: 20px;'.$styleCancelado.'">
										<b>'.$titulo.'</b><br>'.$labelConsecutivo.' '.$row['consecutivo'].'<br>
										'.$estadoDocumento.'
									</div>
									<br><br><br><br>

									<div style="float:left; width:50%; font-size:14px;">
										<div style="float:left; width:100%;">
											<div style="float:left; width:40%;"><b>Empresa:</b></div>
											<div style="float:right; width:60%;">'.$row["tercero"].'</div>
										</div>
										<div style="float:left; width:100%;;">
											<div style="float:left; width:40%;"><b>NIT:</b></div>
											<div style="float:right; width:60%;">'.$row["documento_tercero"].'</div>
										</div>
										<div style="float:left; width:100%;;">
											<div style="float:left; width:40%;"><b>Sucursal:</b></div>
											<div style="float:right; width:60%;">'.$row["sucursal"].'</div>
										</div>
									</div>

									<div style="float:right; width:50%; font-size:14px;">
										<div style="float:right; width:100%;">
											<div style="float:left; width:40%;"><b>Elaborado Por:</b></div>
											<div style="float:right; width:60%;">'.$row["nombre_usuario"].'</div>
										</div>
										<div style="float:right; width:100%;">
											<div style="float:left; width:40%;"><b>Fecha Del Extracto:</b></div>
											<div style="float:right; width:60%;">'.$row["fecha_extracto"].'</div>
										</div>
										<div style="float:right; width:100%;">
											<div style="float:left; width:40%;"><b>Cuenta Contable:</b></div>
											<div style="float:right; width:60%;">'.$row["cuenta"].' - '.$row["descripcion_cuenta"].'</div>
										</div>
									</div>
								</div>';

		$footer =  '<div width="50%" style="border: solid 2px #999999;">
									<div width="100%" style="background-color: #999999; color: white;">&nbsp;<b>Observaciones</b></div>
									<div width="100%">'.$row['observacion'].'</div>
								</div>';
	}

	//CONSULTAR LA INFORMACION DE LA CUERPO
	$sqlBody		= "SELECT tipo, numero_documento, fecha, valor FROM extractos_detalle WHERE id_extracto = '$id_documento' AND activo = 1 AND id_empresa = $id_empresa";
	$queryBody	= mysql_query($sqlBody,$link);

	//==================== ARMAMOS EL DETALLE DEL DOCUMENTO ====================//
 	$body =  '<tr class="thead" style="border: 1px solid #999; color: #f7f7f7;">
							<td style="width:70px;text-align:center;"><b>Tipo Documento</b></td>
							<td style="width:70px;text-align:center;"><b>Numero Documento</b></td>
							<td style="width:70px;text-align:center;"><b>Fecha</b></td>
 							<td style="width:70px;text-align:center;"><b>Valor</b></td>
						</tr>';
	while($row = mysql_fetch_array($queryBody)){

		$body .= '<tr>
								<td style="width:70px;text-align:center;">'.$row['tipo'].'</td>
								<td style="width:70px;text-align:center;">'.$row['numero_documento'].'</td>
								<td style="width:70px;text-align:center;">'.$row['fecha'].'</td>
								<td style="width:70px;text-align:right;">'.round($row['valor'],$_SESSION['DECIMALESMONEDA']).'</td>
							</tr>';

		$totalExtracto += $row['valor'];
	}
	$body .= '<tr class="thead" style="border: 1px solid #999; color: #f7f7f7;">
							<td style="width:70px;text-align:center;" colspan="3"><b>Total Extracto</b></td>
							<td style="width:70px;text-align:right;">'.$totalExtracto.'</td>
						</tr>';
?>
<style>
	.tableInforme{
		font-size: 20px;
		width: 100%;
		margin-top: 20px;
		border-collapse: collapse;
	}
	.tableInforme .thead td{
		color:#FFF;
	}
	.tableInforme tbody tr{
		padding-left: 5px;
	}
	.tableInforme .thead{
		height: 25px;
		background: #999;
		padding-left: 10px;
		height: 25px;
		font-size: 20px;
		color: #FFF;
		font-weight: bold;
	}
	.tableInforme .total{
		height: 25px;
		background: #EEE;
		font-weight: bold;
		color: #8E8E8E;
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
	}
	.my_informe_Contenedor_Titulo_informe{
		float         :	left;
		width         :	100%;
		margin        :	0 0 10px 0;
		font-size     :	11px;
	}
	.my_informe_Contenedor_Titulo_informe_label{
		float       : left;
		width       : 130px;
		font-weight : bold;
	}
	.my_informe_Contenedor_Titulo_informe_detalle{
		float         :	left;
		width         :	210px;
		padding       :	0 0 0 10px;
		white-space   : nowrap;
		overflow      : hidden;
		text-overflow : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
		float     : left;
		width     : 100%;
		font-size : 16px;
		font-weight:bold;
	}
	.table{
		font-size       : 20px;
		width           : 100%;
		border-collapse : collapse;
		color : #FFF;
	}
	.table thead{
		background : #999;
	}
	.table thead td {
		padding-left : 10px;
		height       : 30px;
		background   : #999;
		color        : #FFF;
	}
	.total{
		background  : #EEE;
		font-weight : bold;
	}
	.total td{
		border-top    : 1px solid #999;
		border-bottom : 1px solid #999;
		background    : #EEE;
		padding-left  : 10px;
		height        : 25px;
		font-weight   : bold;
		color         : #8E8E8E;
	}
</style>
<body>
	<br>
	<table class="tableInforme" style="width:1015px; border-collapse:collapse;">
		<?php echo $body; ?>
	</table>
	<br>
	<?php echo $footer; ?>
</body>
<?php
	$texto     = ob_get_contents();
	$documento = "Extracto Bancario";

	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}
	if(isset($MARGENES)){
		list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
	}else{
		$MS = 70;$MD = 10;$MI = 15;$ML = 10;
	}
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}

	if($IMPRIME_PDF){
		ob_clean();
		include("../../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   			// mode - default ''
			$HOJA,					// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION		// L - landscape, P - portrait
		);

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHTMLHeader(utf8_encode($header));
		$mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){
			$mpdf->Output($documento.".pdf",'D');   //OUTPUT A ARCHIVO
		}else{
			$mpdf->Output($documento.".pdf",'I');		//OUTPUT A VISTA
		}
		exit;
	}
	else{
		echo $texto;
	}
?>
