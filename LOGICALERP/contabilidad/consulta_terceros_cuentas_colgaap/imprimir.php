<?php

	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }
	$divFiltroFecha='<br>';
	$whereDetalleFecha = "";
	if($fecha_inicial != '' && $fecha_final != ''){ $whereDetalleFecha = "AND fecha BETWEEN  '$fecha_inicial' AND '$fecha_final'"; $divFiltroFecha='<div style="float:left; width:100%;font-size:12px;">Desde: '.$fecha_inicial.' Hasta: '.$fecha_final.'</div>';}
	else if($fecha_inicial != ''){ $whereDetalleFecha = "AND fecha >=  '$fecha_inicial'"; $divFiltroFecha='<div style="float:left; width:100%;font-size:12px;">Desde: '.$fecha_inicial.' </div>';}
	else if($fecha_final != ''){ $whereDetalleFecha = "AND fecha <=  '$fecha_final'"; $divFiltroFecha='<div style="float:left; width:100%;font-size:12px;"> Hasta: '.$fecha_final.'</div>';}
	$campos_bd='';
	$subtitulo='';
	if ($consulta=='principal') {
		$where="activo = 1 AND id_empresa=".$_SESSION['EMPRESA']." AND id_sucursal=".$_SESSION['SUCURSAL']." AND id_tercero > 0 $whereDetalleFecha GROUP BY id_tercero";
		$tabla_asiento='asientos_colgaap';
		$campos_bd=',SUM(debe) AS debito,SUM(haber) AS credito,SUM(debe)-SUM(haber) AS saldo ';
		$titulo='TERCEROS';
	}
	else{
		$titulo='MOVIMIENTO CUENTAS TERCERO';
		$where="activo = 1 AND id_empresa=".$_SESSION['EMPRESA']." AND id_sucursal=".$_SESSION['SUCURSAL']." AND id_tercero=$id_tercero $whereDetalleFecha";
	}


	$acumuladoDebito=0;
	$acumuladoCredito=0;

	$sql    = "SELECT *$campos_bd FROM $tabla_asiento WHERE  $where ";
	$consul = mysql_query($sql,$link);


	$documento = " ".$titulo;

	$tipo_contabilidad=($tabla_asiento=='asientos_colgaap')? 'Contabilidad Colgaap' : 'Contabilidad Niif' ;

	$header = '<div id="body_pdf" style="width:100%; font-style:normal; font-size:14px;" >
					<br>
					<div style="overflow: hidden; width:100%; margin-bottom:15px;margin-top:20px;">
						<div style="float:left; width:49%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:100%;font-size:16px;"><b>'.$_SESSION['NOMBREEMPRESA'].'</b></div>
							<div style="float:left; width:100%;font-size:12px;">NIT. '.$_SESSION['NITEMPRESA'].'</div>
						</div>
						<div style="float: right; width:49%; position: fixed;"  >
							<div style="float:left; width:100%;"><b>'.$titulo.'</b></div>
							<div style="float:left; width:100%;">'.$tipo_contabilidad.'</div>
							<div style="float:left; width:100%;font-size:12px;">Impreso '.fecha_larga_hora_m(date('Y-m-d H:i:s')).'</div>
							'.$divFiltroFecha.'
						</div>


					</div>

				</div>';

	if ($consulta=='principal') {
		$header.='<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; /*background-color: #CDCDCD;*/">
						<div style="float:left;width:100%;margin-bottom:3px;font-size:12;"></div>

						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;" >NIT</div>
						<div style="float:left; width:250px; padding-left:3px; padding-top:5px;" >TERCERO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;text-align:right;" >DEBITO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;text-align:right;" >CREDITO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;text-align:right;" >SALDO</div>

					</div>';
	}
	else{
			$header.='<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; /*background-color: #CDCDCD;*/">
						<div style="float:left;width:100%;margin-bottom:3px;font-size:12;"><b> Tercero:</b> '.$tercero.' <b>Nit</b> '.$nit.'  </div>

						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;" >FECHA</div>
						<div style="float:left; width:200px; padding-left:3px; padding-top:5px;">TIPO DOCUMENTO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;">NUMERO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;text-align:right;" >DEBITO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;text-align:right;" >CREDITO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;text-align:right;" >SALDO</div>

					</div>';
	}


	while ($row=mysql_fetch_array($consul)) {
		$saldo=$row['debe']-$row['haber'];

		$acumuladoDebito  +=($row['debe'] >0)? $row['debe'] : 0;
		$acumuladoCredito +=($row['haber']>0)? $row['haber']: 0;
		if ($consulta=='principal') {
			$cuentas.='<div style="width:100%;">
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;"> '.$row['nit_tercero'].'</div>
						<div style="float:left; width:250px; padding-left:4px; padding-top:5px;">'.$row['tercero'].'</div>
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;text-align:right;" >'.number_format($row['debito'],$_SESSION['DECIMALES_MONEDA']).'</div>
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;text-align:right;" >'.number_format($row['credito'],$_SESSION['DECIMALES_MONEDA']).'</div>
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;text-align:right;"> '.number_format($row['saldo'],$_SESSION['DECIMALES_MONEDA']).'</div>

					</div>';
		}
		else{
			$cuentas.='<div style="width:100%;">
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;" >'.$row['fecha'].'</div>
						<div style="float:left; width:200px; padding-left:4px; padding-top:5px;">'.$row['tipo_documento_extendido'].'</div>
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;" >'.$row['consecutivo_documento'].'</div>
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;text-align:right;" >'.number_format($row['debe'],$_SESSION['DECIMALES_MONEDA']).'</div>
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;text-align:right;" >'.number_format($row['haber'],$_SESSION['DECIMALES_MONEDA']).'</div>
						<div style="float:left; width:100px; padding-left:4px; padding-top:5px;text-align:right;"> '.number_format($saldo,$_SESSION['DECIMALES_MONEDA']).'</div>

					</div>';
		}


	}

	$contenido='<div style="width:100%; font-style:normal;font-family:arial; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000; pdding-top:30px; margin-top:30px;">
				 '.$cuentas.'
				<br/>
				</div>';
		if ($consulta!='principal') {

			$contenido.='<div style=" float:left;">
				<table  style="width:100%; font-style:normal; font-size:11px; margin:10px 0px 0px 10px; border-collapse:collapse;" >
					<tr style="border: 1px solid;">
						<td style="width:550px;text-align:left;font-size:14;">TOTALES </td>
						<td style="width:80px;text-align:right;" >'.number_format($acumuladoDebito,$_SESSION['moneda_cero']) .'</td>
						<td style="width:80px;text-align:right;" >'.number_format($acumuladoCredito,$_SESSION['moneda_cero']) .'</td>
						<td style="width:80px;text-align:right;" >'.number_format((($acumuladoDebito-$acumuladoCredito)+$saldo_anterior),$_SESSION['moneda_cero']) .'</td>
					</tr>

				</table>
			</div>
			';
		}

	if (!$consul){die('no valido informe '.mysql_error());}

	$texto= $contenido;


	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}
	if(isset($MARGENES)){
		list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );
	}else{
		//con imagen ms=86 sin imagen ms=71
		$MS =53 ; $MD = 7;$MI = 5;$ML = 7;
	}
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

		if($PDF_GUARDA){
			$mpdf->Output($documento.".pdf",'D');   	///OUTPUT A ARCHIVO
		}else{
			$mpdf->Output($documento.".pdf",'I');		///OUTPUT A VISTA
		}
		exit;
	}else{
		echo $texto;
	}


?>