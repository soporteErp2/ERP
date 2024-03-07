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

	if ($opc=='principal') {
		$campos_bd=',SUM(debe) AS debito,SUM(haber) AS credito,SUM(debe)-SUM(haber) AS saldo ';
		$where="activo = 1 AND id_empresa=".$_SESSION['EMPRESA']." AND id_sucursal=".$_SESSION['SUCURSAL']."  $whereDetalleFecha AND id_cuenta<>'' GROUP BY codigo_cuenta";
	}
	else{
		$where="activo = 1 AND id_empresa=".$_SESSION['EMPRESA']." AND id_sucursal=".$_SESSION['SUCURSAL']." AND codigo_cuenta=$codigo_cuenta $whereDetalleFecha AND id_cuenta<>''";
	}


	$_SESSION['DECIMALES_MONEDA']=2;

	$saldo_anterior=0;
	$labelSaldoAnterior='';

	$acumuladoDebito=0;
	$acumuladoCredito=0;

	//CONDICIONES EN LAS QUE SE CONSULTA EL SALDO ANTERIOR DE LA CUENTA
	if ($fecha_inicial!='') {
		$sqlSaldo="SELECT SUM(debe-haber) AS saldo FROM $tabla_asiento WHERE codigo_cuenta='$codigo_cuenta' AND fecha< '$fecha_inicial' ";
		$querySaldo=mysql_query($sqlSaldo,$link);

		$saldo_anterior=mysql_result($querySaldo,0,'saldo');

		$labelSaldoAnterior=($saldo_anterior!='')? 'SALDO ANTERIOR: '.number_format($saldo_anterior,$_SESSION['DECIMALES_MONEDA']) : 'SALDO ANTERIOR: 0';

	}

	$sql    = "SELECT * $campos_bd FROM $tabla_asiento WHERE  $where ";
	$consul = mysql_query($sql,$link);

	$numero=strlen($codigo_cuenta);
	$titulo='';

	if ($numero==1) {
		$titulo="CLASE";
	}else if ($numero==2) {
		$titulo="GRUPO";
	}else if ($numero==4) {
		$titulo="CUENTA";
	}else if ($numero==6) {
		$titulo="SUBCUENTA";
	}else if ($numero==8) {
		$titulo="CUENTA AUXILIAR";
	}

	// '.$sql.'
	//$fecha_inicial,$fecha_final,$nombre_cuenta

	$documento = "MOVIMIENTO ".$titulo;

	$tipo_contabilidad=($tabla_asiento=='asientos_colgaap')? 'Contabilidad Colgaap' : 'Contabilidad Niif' ;

	$header = '<div id="body_pdf" style="width:100%; font-style:normal; font-size:14px;" >
					<br>
					<div style="overflow: hidden; width:100%; margin-bottom:15px;margin-top:20px;">
						<div style="float:left; width:49%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:100%;font-size:16px;"><b>'.$_SESSION['NOMBREEMPRESA'].'</b></div>
							<div style="float:left; width:100%;font-size:12px;">NIT. '.$_SESSION['NITEMPRESA'].'</div>
						</div>
						<div style="float: right; width:49%; position: fixed;"  >
							<div style="float:left; width:100%;"><b>MOVIMIENTO '.$titulo.'</b></div>
							<div style="float:left; width:100%;">'.$tipo_contabilidad.'</div>
							<div style="float:left; width:100%;font-size:12px;">Impreso '.fecha_larga_hora_m(date('Y-m-d H:i:s')).'</div>
							'.$divFiltroFecha.'


						</div>


					</div>

				</div>';

	if ($opc=='principal') {
		$header.='<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; /*background-color: #CDCDCD;*/">

						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;" >CUENTA</div>
						<div style="float:left; width:300px; padding-left:3px; padding-top:5px;" >DESCRIPCION</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;" >DEBITO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;">CREDITO</div>
						<div style="float:left; width:100px; padding-left:3px; padding-top:5px;">SALDO</div>

					</div>';
	}
	else{
		$header.='<div style="width:100%; font-style:normal; font-size:11px; margin-left:10px; border-bottom:1px solid; float:left; /*background-color: #CDCDCD;*/">
						<div style="float:left;width:50%;margin-bottom:3px;font-size:12;"><b>Cuenta:</b> '.$codigo_cuenta.' '.$nombre_cuenta.'   </div>
						<div style="float:left;width:50%;margin-bottom:3px;font-size:14;text-align:right;">'.$labelSaldoAnterior.'</div>

						<div style="float:left; width:58px; padding-left:3px; padding-top:5px;" >FECHA</div>
						<div style="float:left; width:70px; padding-left:3px; padding-top:5px;" >NIT</div>
						<div style="float:left; width:190px; padding-left:3px; padding-top:5px;" >TERCERO</div>
						<div style="float:left; width:125px; padding-left:3px; padding-top:5px;">TIPO DOCUMENTO</div>
						<div style="float:left; width:71px; padding-left:3px; padding-top:5px;">NUMERO</div>
						<div style="float:left; width:71px; padding-left:3px; padding-top:5px;text-align:right;" >DEBITO</div>
						<div style="float:left; width:71px; padding-left:3px; padding-top:5px;text-align:right;" >CREDITO</div>
						<div style="float:left; width:71px; padding-left:3px; padding-top:5px;text-align:right;" >SALDO</div>

					</div>';
	}

	while ($row=mysql_fetch_array($consul)) {
		$saldo=$row['debe']-$row['haber'];

		$acumuladoDebito  +=($row['debe'] >0)? $row['debe'] : 0;
		$acumuladoCredito +=($row['haber']>0)? $row['haber']: 0;

		if ($opc=='principal') {
			$cuentas.='<div style="width:100%;">
			<div style="float:left; width:100px; padding-left:3px; padding-top:5px;" >'.$row['codigo_cuenta'].'</div>
							<div style="float:left; width:300px; padding-left:3px; padding-top:5px;" >'.$row['cuenta'].'</div>
							<div style="float:left; width:100px; padding-left:3px; padding-top:5px;" >'.$row['debito'].'</div>
							<div style="float:left; width:100px; padding-left:3px; padding-top:5px;">'.$row['credito'].'</div>
							<div style="float:left; width:100px; padding-left:3px; padding-top:5px;">'.$row['saldo'].'</div>

						</div>';
		}
		else{

			$cuentas.='<div style="width:100%;">
							<div style="float:left; width:58px; padding-left:4px; padding-top:5px;" >'.$row['fecha'].'</div>
							<div style="float:left; width:70px; padding-left:4px; padding-top:5px;">'.$row['nit_tercero'].'</div>
							<div style="float:left; width:190px; padding-left:4px; padding-top:5px;">'.$row['tercero'].'</div>
							<div style="float:left; width:125px; padding-left:4px; padding-top:5px;">'.$row['tipo_documento_extendido'].'</div>
							<div style="float:left; width:71px; padding-left:4px; padding-top:5px;" >'.$row['consecutivo_documento'].'</div>
							<div style="float:left; width:71px; padding-left:4px; padding-top:5px;text-align:right;" >'.number_format($row['debe'],$_SESSION['DECIMALES_MONEDA']).'</div>
							<div style="float:left; width:71px; padding-left:4px; padding-top:5px;text-align:right;" >'.number_format($row['haber'],$_SESSION['DECIMALES_MONEDA']).'</div>
							<div style="float:left; width:71px; padding-left:4px; padding-top:5px;text-align:right;"> '.number_format($saldo,$_SESSION['DECIMALES_MONEDA']).'</div>

						</div>';
		}

	}
	if ($opc=='principal') {
		$contenido='<div style="width:100%; font-style:normal;font-family:arial; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000; pdding-top:30px; margin-top:30px;">
				 '.$cuentas.'
				<br/>
				</div>
			';
	}
	else{
		$contenido='<div style="width:100%; font-style:normal;font-family:arial; font-size:11px; margin:0px 0px 0px 10px; border-bottom:1px solid #000; pdding-top:30px; margin-top:30px;">
				 '.$cuentas.'
				<br/>
				</div>

				<div style=" float:left;">
				<table  style="width:100%; font-style:normal; font-size:11px; margin:10px 0px 0px 10px; border-collapse:collapse;" >
					<tr style="border: 1px solid;">
						<td style="width:550px;text-align:left;font-size:14;">TOTALES </td>
						<td style="width:80px;text-align:right;" >'.number_format($acumuladoDebito,$_SESSION['DECIMALES_MONEDA']) .'</td>
						<td style="width:80px;text-align:right;" >'.number_format($acumuladoCredito,$_SESSION['DECIMALES_MONEDA']) .'</td>
						<td style="width:80px;text-align:right;" >'.number_format((($acumuladoDebito-$acumuladoCredito)+$saldo_anterior),$_SESSION['DECIMALES_MONEDA']) .'</td>
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
		if ($opc=='principal') {
			$MS =50 ;
		}else{
			$MS =59 ;
		}
		 $MD = 7;$MI = 5;$ML = 7;
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