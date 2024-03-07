<?php

	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	$idEmpresa  = $_SESSION['EMPRESA'];
	$idSucursal = $_SESSION['SUCURSAL'];

	//SI SE VAN A CONSULTAR TODOS LOS DOCUMENTOS DENTRO O FUERA DE UN RANGO DE FECHAS
	if ($consulta=='principal') {
		$whereFecha = "";
		if($fecha_inicial != '' && $fecha_final != ''){ $whereFecha = "AND fecha BETWEEN  '$fecha_inicial' AND '$fecha_final'"; }
		else if($fecha_inicial != ''){ $whereFecha = "AND fecha >=  '$fecha_inicial'"; }
		else if($fecha_final != ''){ $whereFecha = "AND fecha <=  '$fecha_final'"; }

		$where = $filtro_sucursal > 0 ? "AND id_sucursal='$filtro_sucursal'": "";

		$sql           = "SELECT *,SUM(debe) AS debito,SUM(haber) AS credito FROM asientos_colgaap WHERE activo = 1 AND id_empresa='$idEmpresa' $where AND  tipo_documento='POS' $whereFecha GROUP BY tipo_documento,id_documento";
		$tabla_asiento = 'asientos_colgaap';
	}
	else{ $sql="SELECT *,SUM(debe) AS debito,SUM(haber) AS credito FROM $tabla_asiento WHERE id_documento='$id_documento' AND tipo_documento='POS' AND id_empresa='$idEmpresa' GROUP BY codigo_cuenta"; }

	$consul    = mysql_query($sql,$link);
	$documento = "MOVIMIENTO ".$titulo;

	$info              = ($consulta=='principal')? 'LISTADO DE DOCUMENTOS' : 'ASIENTO CONTABLE';
	$tipo_contabilidad = ($tabla_asiento=='asientos_colgaap')? 'Contabilidad Colgaap' : 'Contabilidad Niif';

	$header = '<div id="body_pdf" style="width:100%; font-style:normal; font-size:14px;">
					<br>
					<div style="overflow: hidden; width:100%; margin-bottom:15px;margin-top:20px;">
						<div style="float:left; width:49%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:100%;font-size:16px;"><b>'.$_SESSION['NOMBREEMPRESA'].'</b></div>
							<div style="float:left; width:100%;font-size:12px;">NIT. '.$_SESSION['NITEMPRESA'].'</div>
						</div>
						<div style="float: right; width:49%; position: fixed;"  >
							<div style="float:left; width:100%;"><b>'.$info.'</b></div>
							<div style="float:left; width:100%;">'.$tipo_contabilidad.'</div>
							<div style="float:left; width:100%;font-size:12px;">Impreso '.fecha_larga_hora_m(date('Y-m-d H:i:s')).'</div>
							'.$divFiltroFecha.'
						</div>
					</div>

				</div>';
	if ($consulta=='principal') {
		$header .= '<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left;">
						<div style="float:left;width:50%;margin-bottom:3px;font-size:12;"><b>LISTADO DE DOCUMENTOS</b></div>
						<div style="float:left;width:50%;margin-bottom:3px;font-size:14;text-align:right;">'.$labelSaldoAnterior.'</div>
						<div style="float:left; width:158px; padding-left:5px; padding-top:5px;">FECHA</div>
						<div style="float:left; width:175px; padding-left:5px; padding-top:5px;">PUNTO DE VENTA</div>
						<div style="float:left; width:100px; padding-left:5px; padding-top:5px;">DEBITO/CREDITO</div>
						<div style="float:left; width:240px; padding-left:10px; padding-top:5px;">SUCURSAL</div>
					</div>';
	}
	else{
		$header .= '<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left;">
						<div style="float:left;width:50%;margin-bottom:3px;font-size:14;width:100%;"><b>Ticket No. '.$numero_documento.'</b> '.$tipo_documento_extendido.' ('.$type_document.') </div>
						<div style="float:left; width:140px; padding-left:5px; padding-top:5px;">FECHA</div>
						<div style="float:left; width:70px; padding-left:5px; padding-top:5px;">CUENTA</div>
						<div style="float:left; width:220px; padding-left:5px; padding-top:5px;">DESCRIPCION</div>
						<div style="float:left; width:100px; padding-left:5px; padding-top:5px;text-align:right;">DEBITO</div>
						<div style="float:left; width:100px; padding-left:5px; padding-top:5px;text-align:right;">CREDITO</div>
						<div style="float:left; width:100px; padding-left:5px; padding-top:5px;text-align:right;">SALDO</div>
					</div>';
	}

	while ($row=mysql_fetch_array($consul)) {
		$saldo = $row['debito']-$row['credito'];

		$acumuladoDebito  +=($row['debito'] >0)? $row['debito'] : 0;
		$acumuladoCredito +=($row['credito']>0)? $row['credito']: 0;

		if ($consulta=='principal') {
			$cuentas.='<div style="width:100%;">
						<div style="float:left; width:158px; padding-left:5px; padding-top:5px;">'.fecha_larga($row['fecha']).'</div>
						<div style="float:left; width:50px; padding-left:5px; padding-top:5px;">'.$row['consecutivo_documento'].'</div>
						<div style="float:left; width:125px; padding-left:5px; padding-top:5px;">'.$row['tipo_documento_extendido'].'</div>
						<div style="float:left; width:100px; padding-left:5px; padding-top:5px; text-align:right;">'.number_format($row['debito'], $_SESSION['moneda_cero']).'</div>
						<div style="float:left; width:240px; padding-left:10px; padding-top:5px;">'.$row['sucursal'].'</div>
					</div>';
		}
		else{
			$cuentas.='<div style="width:100%;">
							<div style="float:left; width:140px; padding-left:5px; padding-top:5px;">'.fecha_larga($row['fecha']).'</div>
							<div style="float:left; width:70px; padding-left:5px; padding-top:5px;">'.$row['codigo_cuenta'].'</div>
							<div style="float:left; width:220px; padding-left:5px; padding-top:5px;">'.$row['cuenta'].'</div>
							<div style="float:left; width:100px; padding-left:5px; padding-top:5px; text-align:right;">'.number_format($row['debito'],$_SESSION['moneda_cero']).'</div>
							<div style="float:left; width:100px; padding-left:5px; padding-top:5px; text-align:right;">'.number_format($row['credito'],$_SESSION['moneda_cero']).'</div>
							<div style="float:left; width:100px; padding-left:5px; padding-top:5px; text-align:right;">'.number_format($saldo,$_SESSION['moneda_cero']).'</div>
						</div>';
		}
	}
	if ($consulta=='principal') {
		$contenido='<div style="width:100%; font-style:normal;font-family:arial; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000; pdding-top:30px; margin-top:30px;">
						'.$cuentas.'<br/>
					</div>
					<div style="float:left;">
						<table style="width:100%; font-style:normal; font-size:11px; margin:10px 0px 0px 10px;">
							<tr>
								<td style="width:300px; text-align:left; font-size:14;">TOTAL</td>
								<td style="width:80px; text-align:right;">'.number_format($acumuladoDebito,$_SESSION['moneda_cero']).'</td>
								<td style="width:305px;"></td>
							</tr>
						</table>
					</div>';
	}
	else{
		$contenido='<div style="width:100%; font-style:normal;font-family:arial; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000; pdding-top:30px; margin-top:30px;">
						'.$cuentas.'<br/>
					</div><br>
					<div style="float:left; margin-left:10px; font-size:12;">
						<div style="float:left; width:440px; padding-left:5px; text-align:left;">TOTAL</div>
						<div style="float:left; width:100px; padding-left:5px; text-align:right;">'.number_format($acumuladoDebito,$_SESSION['moneda_cero']).'</div>
						<div style="float:left; width:100px; padding-left:5px; text-align:right;">'.number_format($acumuladoCredito,$_SESSION['moneda_cero']).'</div>
						<div style="float:left; width:100px; padding-left:5px; text-align:right;">'.number_format((($acumuladoDebito-$acumuladoCredito)+$saldo_anterior),$_SESSION['moneda_cero']) .'</div>
					</div>';
	}

	if (!$consul){die('no valido informe '.mysql_error());}

	$texto = $contenido;

	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS =53 ; $MD = 7;$MI = 5;$ML = 7; }		//con imagen ms=86 sin imagen ms=71

	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}

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

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHTMLHeader(utf8_encode($header));
		$mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA
		exit;
	}
	else{ echo $texto; }


?>