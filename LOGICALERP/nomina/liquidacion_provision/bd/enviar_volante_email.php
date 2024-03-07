<?php

function imprimirEnviaVolante($id_tercero,$consecutivo,$fecha_inicio,$fecha_final,$concepto,$saldo,$mail,$mpdf,$link){

	$idEmpresa = $_SESSION['EMPRESA'];

	// //CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular FROM empresas WHERE id='$idEmpresa' LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$ciudad                = mysql_result($queryEmpresa,0,'ciudad');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');
	$telefonos             = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
	$actividad_economica   = mysql_result($queryEmpresa,0,'actividad_economica');

	//CONSULTAR LOS DATOS DEL EMPLEADO
	$sql="SELECT id,documento,nombre,email_empresa FROM empleados WHERE activo=1 AND id_empresa=$idEmpresa AND id_tercero=$id_tercero ";
	$query=mysql_query($sql,$link);
	$id_empleado        = mysql_result($query,0,'id');
	$documento_empleado = mysql_result($query,0,'documento');
	$nombre_empleado    = mysql_result($query,0,'nombre');
	$email_empresa      =mysql_result($query,0,'email_empresa');

	//CONSULTAR LOS DATOS DE LA PLANILLA
	$sql="SELECT consecutivo,fecha_inicio,fecha_final FROM nomina_planillas WHERE activo=1 AND id_empresa=$idEmpresa AND id=$id_planilla";
	$query=mysql_query($sql,$link);

	//BUSCAR LA INFORMACION DEL CONTRATO Y DEL EMPLEADO
	$sql="SELECT documento_empleado,nombre_empleado,numero_contrato,grupo_trabajo FROM empleados_contratos WHERE activo=1 AND estado=0 AND id_empresa=$idEmpresa AND id_empleado=$id_empleado LIMIT 0,1";
	$query=mysql_query($sql,$link);
	$numero_contrato    = mysql_result($query,0,'numero_contrato');
	$grupo_trabajo      = mysql_result($query,0,'grupo_trabajo');

	//CONSULTAR LOS CONCEPTOS DEL EMPLEADO
	// $sql="SELECT concepto,valor_concepto,naturaleza FROM nomina_planillas_empleados_conceptos WHERE activo=1 AND id_empleado=$id_empleado AND id_planilla=$id_planilla AND id_contrato=$id_contrato AND imprimir_volante='true' ORDER BY naturaleza ASC";
	// $query=mysql_query($sql,$link);
	// $conceptos     ='';
	// $acumDevengo   =0;
	// $acumDeduccion =0;
	// while ($row=mysql_fetch_array($query)) {
	// 	$devengo   =($row['naturaleza']=='Devengo')? number_format ($row['valor_concepto'],$_SESSION['DECIMALESMONEDA']) : '' ;
	// 	$deduccion =($row['naturaleza']=='Deduccion')? number_format ($row['valor_concepto'],$_SESSION['DECIMALESMONEDA']) : '' ;
	// 	$conceptos.='<tr>
	// 					<td>'.$row['concepto'].'</td>
	// 					<td style="text-align:right;">'.$devengo.'</td>
	// 					<td style="text-align:right;">'.$deduccion.'</td>
	// 				</tr>';

	// 	$acumDevengo   +=($row['naturaleza']=='Devengo')? $row['valor_concepto'] : 0 ;
	// 	$acumDeduccion +=($row['naturaleza']=='Deduccion')? $row['valor_concepto'] : 0 ;

	// }

	$conceptos='<tr>
					<td>'.$concepto.'</td>
					<td style="text-align:right;">'.number_format ($saldo,$_SESSION['DECIMALESMONEDA']).'</td>
				</tr>';

	//======================================= ARMAMOS EL DOCUMENTO =============================================//
	$header = '<div id="body_pdf" style="width:100%; font-style:normal; font-size:11px;">
					<div style="float:left; width:445px; margin-left:10px;">
						<table style="font-size:10px;">
							<tr><td><b>'.$razon_social.'</b></td><td></td></tr>
							<tr><td>'.$tipo_regimen.'</td><td></td></tr>
							<tr><td><b>'.$tipo_documento_nombre.':</b></td><td>'.$documento_empresa.'</td></tr>
							<tr><td><b>Direccion: </b></td><td>'.$direccion_empresa.'</td></tr>
							<tr><td><b>Tels:</b></td><td>'.$telefonos.'</td></tr>
							<tr><td>'.$ciudad.'</td><td></td></tr>
						</table>
					</div>
					<div style="float:left;width:35%;text-align:center;">
						<b>LIQUIDACION '.$concepto.'<br>DOCUMENTO N. '.$consecutivo.'</b><br>
						Periodo '.$fecha_inicio.' / '.$fecha_final.'
					</div>
					<br>
					<div style="overflow: hidden; width:100%; margin-bottom:15px;margin-top:20px;">
						<div style="float:left; width:90%; margin:0px 5px 0px 10px">
							<div style="float:left; width:20%;"><b>Documento Empleado:</b></div>
							<div style="float:left; width:50%;">'.$documento_empleado.'</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:20%;"><b>Empleado:</b></div>
							<div style="float:left; width:50%;">'.$nombre_empleado.'</div>
						</div>

						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:20%;"><b>N. Contrato:</b></div>
							<div style="float:left; width:50%;">'.$numero_contrato.' </div>
						</div>

						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:20%;"><b>Grupo de Trabajo:</b></div>
							<div style="float:left; width:60%;">'.$grupo_trabajo.' </div>
						</div>

						'.$finaliza_contrato.'

					</div>
				</div>';

	$contenido = '<table class="articlesTable">
					<thead>
						<tr>
							<td style="width:50%;">CONCEPTOS</td>
							<td style="width:25%;">SALDO</td>
						</tr>
					</thead>
					<tbody>
						'.$conceptos.'
						<tr style="border:1px solid;">
							<td>TOTALES</td>
							<td style="text-align:right;">'.number_format ($saldo,$_SESSION['DECIMALESMONEDA']).'</td>
						</tr>
					</tbody>
				</table>

				<div style=" float:left; margin:auto; width:50%;">
					<table style="1px font-style:normal; font-size:11px; margin-left:5px;margin-top:50px;">
						<tr>
							<td style="width:100px; font-weight:bold;">NETO A PAGAR </td>
							<td style="width:10px; font-weight:bold;">$</td>
							<td style="text-align:right; width:120px; font-weight:bold;"> '.number_format ($saldo,$_SESSION['DECIMALESMONEDA']).'</td>
						</tr>
					</table>
				</div>
				<br>
				<table style="overflow: hidden; width:100%; margin:50px 5px 100px 0px; padding:0px 7px 0px 0px; font-size:12px;">
					<tr style="width:100%;">
						<td style="width:100%;">
							<table >
								<tr>
									<td style="40%;border-top: 1px solid;"><br><br><br>_______________________________________________________</td>
									<td style="10%;border-top: 1px solid;"><br><br><br>&nbsp;</td>
								</tr>

								<tr>
									<td style="40%;border-top: 1px solid;">Firma del Empleado</td>
									<td style="10%;border-top: 1px solid;">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<style>
					.articlesTable{
						font-size       : 12px;
						border          : 1px solid #000;
						border-collapse : collapse;
						margin-left     : 10px;
						width           : 100%;
					}

					.articlesTable td{
						padding         : 2px;
						border          : 1px solid #000;
						border-collapse : collapse;
					}

					.articlesTable thead td{
						font-size   : 10px;
						font-weight : bold;
						text-align  : center;
					}

					.articlesTable tbody tr{
						border : none;
					}
				</style>';

	// $contenido='jgjkvbkjnlj';



// echo 'in';
	/*/////// MARCA DE AGUA
	$mpdf->SetWatermarkText('COPIA');
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->showWatermarkText = true;
	*/
	$documento='volante_nomina';
	// $mpdf->SetAutoPageBreak(TRUE, 15);
	// $mpdf->SetTitle ( $documento );
	// $mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
	$mpdf->SetDisplayMode ( 'fullpage' );
	$mpdf->SetHTMLHeader(utf8_encode($header));
	// $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
	$mpdf->WriteHTML(utf8_encode($contenido));

	// if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
	// else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA
	$mpdf->Output($documento.".pdf",'F');		///OUTPUT A VISTA


	//======================================= ENVIO DEL EMAIL ======================================//

	$body  = '<font color="black">
				<br>
				<b>'.$razon_social.'</b><br>
				<b>'.$tipo_regimen.'</b><br>
				<b>'.$tipo_documento_nombre.': </b>'.$documento_empresa.'<br>
				<b>Direccion: </b>'.$direccion_empresa.'<br>
				<b>Telefono: </b>'.$telefonos.'<br>
				<b>'.$ciudad.' </b><br>

				<br>
				<b>Documento empleado: </b> '.$documento_empleado.' <br>
				<b>Nombre Empleado:</b>'.$nombre_empleado.'<br><br>
				Se adjunta volante de liquidacion correspondiente al Periodo de '.$fecha_inicio.' hasta '.$fecha_final.'<br>
				';

	$body .= '<br><br><br>Esta es una notificacion automatica generada por un robot, por favor no responda este email.</font>'.'<br>';
	$mail->Body = $body;

	$mail->MsgHTML($body);
	$mail->AddAttachment($documento.".pdf");
	$mail->AddAddress($email_empresa);
	$mail->IsHTML(true); // send as HTML

	if(!$mail->Send()) { return "false"; }
	else { return "true"; }
	$mail->ClearAddresses();
}

?>