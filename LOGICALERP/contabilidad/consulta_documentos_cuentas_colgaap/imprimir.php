<?php

	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$whereTipoCuenta    = "";
	$whereTipoDocumento = ($tipo_documento != '')? "AND tipo_documento='$tipo_documento'": "";
	$whereSucursal      = ($filtro_sucursal > 0)? "AND id_sucursal='$filtro_sucursal'": "";

	if($tipo_cuenta != ''){
		$sqlCuenpaPago   = "SELECT cuenta FROM configuracion_cuentas_pago WHERE id_empresa='$id_empresa' AND activo=1";
		$queryCuentaPago = mysql_query($sqlCuenpaPago);

		while ($rowCuentaPago = mysql_fetch_assoc($queryCuentaPago)) {
			$whereTipoCuenta .= "codigo_cuenta='$rowCuentaPago[cuenta]' OR ";
		}

		$whereTipoCuenta = "AND(".substr($whereTipoCuenta, 0, -3).")";
	}


	//SI SE VAN A CONSULTAR TODOS LOS DOCUMENTOS DENTRO O FUERA DE UN RANGO DE FECHAS
	if ($consulta=='principal') {
		$whereFecha = "";

		if($fecha_inicial != ''){
			$divFiltroFecha .= "<div>Fecha inicial: $fecha_inicial</div>";
			$whereFecha     .= "AND fecha >= '$fecha_inicial'";
		}
		if($fecha_final != ''){
			$divFiltroFecha .= "<div>Fecha Final: $fecha_final</div>";
			$whereFecha     .= "AND fecha <= '$fecha_final'";
		}

		$sql  = "SELECT *,
					SUM(debe) AS debito,
					SUM(haber) AS credito
				FROM asientos_colgaap
				WHERE activo = 1
					AND id_empresa='$id_empresa'
					AND tipo_documento<>'POS'
					$whereFecha
					$whereSucursal
					$whereTipoCuenta
					$whereTipoDocumento
				GROUP BY tipo_documento,id_documento
				ORDER BY fecha DESC";
		$tabla_asiento = 'asientos_colgaap';
	}
	else{
		$sql = "SELECT *,
					SUM(debe) AS debito,
					SUM(haber) AS credito
				FROM $tabla_asiento
				WHERE id_documento='$id_documento'
					AND tipo_documento='$type_document'
					AND id_empresa='$id_empresa'
				GROUP BY codigo_cuenta";
	}

	$consul    = mysql_query($sql,$link);
	$documento = "MOVIMIENTO ".$titulo;

	$info              = ($consulta=='principal')? 'LISTADO DE DOCUMENTOS' : 'ASIENTO CONTABLE';
	$tipo_contabilidad = ($tabla_asiento=='asientos_colgaap')? 'Contabilidad Colgaap' : 'Contabilidad Niif';

	$contenido = '<div id="body_pdf" style="width:100%; font-style:normal; font-size:14px;">
					<br>
					<div style="overflow: hidden; width:100%; margin-bottom:15px;">
						<div style="float:left; width:49%; margin:0px 5px 0px 0px;">
							<div style="float:left; width:100%; font-size:16px;"><b>'.$_SESSION['NOMBREEMPRESA'].'</b></div>
							<div style="float:left; width:100%; font-size:12px;">NIT. '.$_SESSION['NITEMPRESA'].'</div>
							<div style="float:left; width:100%; font-size:12px;">'.$sucursal.'</div>
						</div>
						<div style="float:right; width:49%; font-size:12px;"  >
							<div><b>'.$info.'</b></div>
							<div>'.$tipo_contabilidad.'</div>
							<div>Impreso '.fecha_larga_hora_m(date('Y-m-d H:i:s')).'</div>
							'.$divFiltroFecha.'
						</div>
					</div>
				</div>';
	if ($consulta=='principal') {
		$contenido .= '<div style="width:100%; font-style:normal; font-size:11px; float:left;">
							<div style="float:left;width:50%; margin-bottom:3px; font-size:12;"><b>LISTADO DE DOCUMENTOS</b></div>
							<div style="float:left;width:50%; margin-bottom:3px; font-size:14;text-align:right;">'.$labelSaldoAnterior.'</div>
						</div>

						<table id="consulta_cuentas_documentos">
							<thead>
								<tr>
									<td width="58">FECHA</td>
									<td width="70">NIT</td>
									<td width="230">TERCERO</td>
									<td width="20">&nbsp;</td>
									<td width="110">DOCUMENTO</td>
									<td width="71">NUMERO</td>
									<td width="95">DEBITO/CREDITO</td>
									<td width="120">SUCURSAL</td>
								</tr>
							</thead>';
	}
	else{
		$contenido .= '<table id="consulta_cuentas_documentos">
							<thead>
								<tr>
									<td width="150"><b>Documento: </b>'.$tipo_documento_extendido.' ('.$type_document.') No. '.$numero_documento.'  </td>
									<td width="140">FECHA</td>
									<td width="70">CUENTA</td>
									<td width="220">DESCRIPCION</td>
									<td width="100" style="text-align:right;">DEBITO</td>
									<td width="100" style="text-align:right;">CREDITO</td>
									<td width="100" style="text-align:right;">SALDO</td>
								</tr>
							</thead>';
	}

	$estilo = 'background-color: #EEE;';
	while ($row=mysql_fetch_array($consul)) {

		$saldo  = $row['debito']-$row['credito'];
		$estilo = ($estilo!='')? '': 'background-color: #EEE;';

		$acumuladoDebito  += ($row['debito'] >0)? $row['debito'] : 0;
		$acumuladoCredito += ($row['credito']>0)? $row['credito']: 0;

		if ($consulta=='principal') {

			if($whereTipoCuenta == ""){ $saldo = $row['debito']; }

			$contenido .= '<tr>
								<td style="'.$estilo.'" width="58">'.$row['fecha'].'</td>
								<td style="'.$estilo.'" width="70">'.$row['nit_tercero'].'</td>
								<td style="'.$estilo.'" width="230">'.$row['tercero'].'</td>
								<td style="'.$estilo.'" width="20">'.$row['tipo_documento'].'</td>
								<td style="'.$estilo.'" width="110">'.$row['tipo_documento_extendido'].'</td>
								<td style="'.$estilo.'" width="71">'.$row['consecutivo_documento'].'</td>
								<td style="'.$estilo.' text-align:right;" width="95">'.number_format($saldo,$_SESSION['moneda_cero']).'</td>
								<td style="'.$estilo.'" width="120">'.$row['sucursal'].'</td>
							</tr>';
		}
		else{
			$contenido .= '<tr>
								<td style="'.$estilo.'" width="140">&nbsp;</td>
								<td style="'.$estilo.'" width="140">'.fecha_larga($row['fecha']).'</td>
								<td style="'.$estilo.'" width="70">'.$row['codigo_cuenta'].'</td>
								<td style="'.$estilo.'" width="220">'.$row['cuenta'].'</td>
								<td style="'.$estilo.' text-align:right;" width="100">'.number_format($row['debito'],$_SESSION['moneda_cero']).'</td>
								<td style="'.$estilo.' text-align:right;" width="100">'.number_format($row['credito'],$_SESSION['moneda_cero']).'</td>
								<td style="'.$estilo.' text-align:right;" width="100">'.number_format($saldo,$_SESSION['moneda_cero']).'</td>
							</tr>';
		}
	}

	//======================// LIBERA MEMORIA //======================//
	//****************************************************************//
	mysql_free_result($consul);

	if ($consulta=='principal') {
		$contenido .= '		<tr style="border-top:1px solid #000;">
								<td style="border-top:1px solid #000; width:100px; text-align:right;" colspan="6">TOTAL</td>
								<td style="border-top:1px solid #000; width:100px; text-align:right;">'.number_format($acumuladoDebito,$_SESSION['moneda_cero']).'</td>
								<td style="border-top:1px solid #000; width:145px;"></td>
							</tr>
						</table>';
	}
	else{
		$contenido .= '		<tr style="border-top:1px solid #000;">
								<td colspan="3">TOTAL</td>
								<td style="text-align:right;">'.number_format($acumuladoDebito,$_SESSION['moneda_cero']).'</td>
								<td style="text-align:right;">'.number_format($acumuladoCredito,$_SESSION['moneda_cero']).'</td>
								<td style="text-align:right;">'.number_format((($acumuladoDebito-$acumuladoCredito)+$saldo_anterior),$_SESSION['moneda_cero']) .'</td>
							</tr>
						</table>';
	}

	$contenido .=  '<style>
						#consulta_cuentas_documentos{ font-size: 11px; border-collapse:collapse; }
						#consulta_cuentas_documentos thead td{ background-color: #000; color:#fff; }
					</style>';



	if (!$consul){die('no valido informe '.mysql_error()); }

	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS =10 ; $MD = 7;$MI = 5;$ML = 7; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

	if($IMPRIME_PDF){
		include("../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			10,							// margin header
			10,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);

		// $mpdf->SetProtection(array('print'));
		$mpdf->useSubstitutions = true;
        $mpdf->simpleTables     = true;
        $mpdf->packTableData    = true;

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');
		$mpdf->WriteHTML(utf8_encode($contenido));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA
		exit;
	}
	else{ echo $contenido; }


?>