<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$sql1 = "	SELECT nombre,documento,cargo,email_empresa,celular_empresa,empresa,sucursal
				FROM empleados
				WHERE id_empresa =".$_SESSION['EMPRESA']."
				AND id_sucursal like '$sucursal'
				AND activo=1
				AND (documento like '%$busca_funcionario%' OR nombre like '%$busca_funcionario%')
				ORDER BY id_empresa,id_sucursal,apellido1";
	$del = mysql_query($sql1 ,$link);
	//echo $sql1;
	$print="";
	while($cow = mysql_fetch_array($del)){
		$print=$print.'	<div class="RedondeadoSombra" style="float:left; vertical-align:middle; margin:10px; border:1px solid #888; width:352px; height:135px;">
							<div style="float:left; width:215px; margin: 10 0 0 10;">
								<div style="font-size:14px; text-align: left;"><b>'.$cow['nombre'].'</b></div>
								<div style="font-size:11px; text-align: left;" >'.$cow['empresa'].'<br>'.$cow['cargo'].'</div>
								<div style="font-size:11px; text-align: left;" >Telefono: '.$cow['celular_empresa'].'&nbsp;</div>
								<div style="font-size:11px; text-align: left;" >Email: '.$cow['email_empresa'].'&nbsp;</div>
							</div>
							<div style="float:left; margin: 5 0 0 0;">
								<img  id="imagen_qr" src="../personal/empleados_QRvisor.php?documento='.$cow['documento'].'" alt="" width="122" height="122" />
							</div>
						</div>';
	}
	if (!isset($pdf)){
		echo $print;
	}else{
		$print = '<div style="float:left; width:740px;">'.$print.'</div>';
		$print 			= utf8_encode($print);
		$HOJA 			= 'LETTER';
		$ORIENTACION 	= 'P';
		$MS 			= 10;
		$MD 			= 10;
		$MI 			= 10;
		$ML 			= 10;
		include("../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF('utf-8',   // mode - default ''
		$HOJA,						// format - A4, for example, default ''
		12,							// font size - default 0
		'',							// default font family
		$MI,						// margin_left
		$MD,						// margin right
		$MS,						// margin top
		$ML,						// margin bottom
		10,							// margin header
		10,							// margin footer
		$ORIENTACION);				// L - landscape, P - portrait

		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( "DIRECTORIO DE FUNCIONARIOS" );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
		$mpdf->WriteHTML($print);
		$mpdf->Output($documento.".pdf",'I');					///OUTPUT A VISTA
	}