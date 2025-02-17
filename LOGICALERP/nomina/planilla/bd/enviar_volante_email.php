<?php

function imprimirEnviaVolante($id_planilla,$id_empleado,$id_contrato,$mail,$mpdf,$link){

	$id_empresa = $_SESSION['EMPRESA'];

	//CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,nit_completo,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular
					FROM empresas
					WHERE id='$id_empresa'
					LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'nit_completo');
	$ciudad                = mysql_result($queryEmpresa,0,'ciudad');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');
	$telefonos             = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
	$actividad_economica   = mysql_result($queryEmpresa,0,'actividad_economica');

	//CONSULTAR LOS DATOS DE LA PLANILLA
	$sql   = "SELECT N.consecutivo,
				N.fecha_inicio,
				N.fecha_final,
				N.estado,
				S.nombre,
				S.departamento,
				S.ciudad
			FROM nomina_planillas AS N LEFT JOIN empresas_sucursales AS S ON( N.id_sucursal = S.id )
			WHERE N.activo=1
				AND N.id_empresa=$id_empresa
				AND N.id=$id_planilla";
	$query = mysql_query($sql,$link);

	$consecutivo  = mysql_result($query,0,'consecutivo');
	$fecha_inicio = mysql_result($query,0,'fecha_inicio');
	$fecha_final  = mysql_result($query,0,'fecha_final');
	$sucursal     = mysql_result($query,0,'nombre');
	$departamento = mysql_result($query,0,'departamento');
	$ciudad       = mysql_result($query,0,'ciudad');

	//CONSULTAR LA INFORMACION DEL EMPLEADO EN LA PLANILLA
	$sql   = "SELECT dias_laborados,terminar_contrato,observaciones
				FROM nomina_planillas_empleados
				WHERE activo=1
					AND id_empresa=$id_empresa
					AND id_planilla=$id_planilla
					AND id_empleado=$id_empleado";
	$query = mysql_query($sql,$link);

	$dias_laborados    = mysql_result($query,0,'dias_laborados');
	$terminar_contrato = mysql_result($query,0,'terminar_contrato');

	$observaciones  = (mysql_result($query,0,'observaciones')<>'')? '<table class="StyleTableObservaciones" width="740" border="0" cellspacing="0" cellpadding="0">
																		<tr>
																			<td width="80"><b>Observaciones: </b>'.mysql_result($query,0,'observaciones').'</td>
																		</tr>
																	</table>' : '' ;

	$finaliza_contrato=($terminar_contrato=='Si')? '<div style="float:left; width:90%; margin:5px 5px 0px 10px;">
														<div style="float:left; width:100%;"><i><b>TERMINACION DE CONTRATO</b></i></div>
													</div>' : '' ;

	//BUSCAR LA INFORMACION DEL CONTRATO Y DEL EMPLEADO
	$sql = "SELECT documento_empleado,nombre_empleado,numero_contrato,grupo_trabajo,salario_basico
			FROM empleados_contratos
			WHERE activo=1
				AND id_empresa=$id_empresa
				AND id_empleado=$id_empleado
				AND id=$id_contrato";
	$query = mysql_query($sql,$link);

	$documento_empleado = mysql_result($query,0,'documento_empleado');
	$nombre_empleado    = mysql_result($query,0,'nombre_empleado');
	$numero_contrato    = mysql_result($query,0,'numero_contrato');
	$salario   		 	= mysql_result($query,0,'salario_basico');
	$grupo_trabajo      = mysql_result($query,0,'grupo_trabajo');

	//CONSULTAR LOS CONCEPTOS DEL EMPLEADO
	$sql = "SELECT concepto,valor_concepto,naturaleza,valor_campo_texto
			FROM nomina_planillas_empleados_conceptos
			WHERE activo=1
				AND id_empleado=$id_empleado
				AND id_planilla=$id_planilla
				AND id_contrato=$id_contrato
				AND imprimir_volante='true'
			ORDER BY naturaleza ASC";
	$query = mysql_query($sql,$link);

	$conceptos     = '';
	$acumDevengo   = 0;
	$acumDeduccion = 0;

	$color = '#F2F2F2;';
	while ($row=mysql_fetch_array($query)) {

		$color = ($color== '')? '#F2F2F2;': '';

		$devengo    = ($row['naturaleza']=='Devengo')? number_format ($row['valor_concepto'],$_SESSION['DECIMALESMONEDA']) : '' ;
		$deduccion  = ($row['naturaleza']=='Deduccion')? number_format ($row['valor_concepto'],$_SESSION['DECIMALESMONEDA']) : '' ;
		$conceptos .= '<tr>
							<td style="background-color:'.$color.'">'.ucwords(strtolower($row['concepto'])).'</td>
							<td style="text-align:center; background-color:'.$color.'">'.$row['valor_campo_texto'].'</td>
							<td style="text-align:right; background-color:'.$color.'">'.$devengo.'</td>
							<td style="text-align:right; background-color:'.$color.'">'.$deduccion.'</td>
						</tr>';

		$acumDevengo   += ($row['naturaleza']=='Devengo')? $row['valor_concepto'] : 0;
		$acumDeduccion += ($row['naturaleza']=='Deduccion')? $row['valor_concepto'] : 0;
	}

	//CONSULTAR EL EMAIL DEL EMPLEADO
	$sql_email     = "SELECT email_personal,email_empresa,email_notificaciones FROM empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_empleado";
	$query_email   = mysql_query($sql_email,$link);
	$email_personal       = mysql_result($query_email,0,'email_personal');
	$email_empresa        = mysql_result($query_email,0,'email_empresa');
	$email_notificaciones = mysql_result($query_email,0,'email_notificaciones');
	$email_envio = ($email_notificaciones=='email_personal')? $email_personal : $email_empresa ;

	if($email_envio != "" || $email_envio != null){
	

		//======================================= ARMAMOS EL DOCUMENTO =============================================//
		$header = '<div id="body_pdf" style="width:100%; font-style:normal;">
						<div class="headEmpresa">
							<div style="font-size:17px;"><b>'.$razon_social.'</b></div>
							<div>'.$tipo_regimen.' '.$tipo_documento_nombre.' '.$documento_empresa.'</div>
							<div>'.$sucursal.' '.$departamento.'-'.$ciudad.'</div>
							<div>'.$direccion_empresa.'</div>
							<div>Tels: '.$telefonos.'</div>
						</div>
						<div style="float:left;width:38%; text-align:center; font-size:16px;">
							<div style="font-size:16px;font-weight:bold;">PLANILLA DE NOMINA<br/>N&deg; '.$consecutivo.'</div>
							<div style="font-size:12px !important;">Periodo '.$fecha_inicio.' / '.$fecha_final.'</div>
							<br/>
						</div>
						<div style="overflow: hidden; width:100%; margin-bottom:15px; margin-top:20px; font-size:12px;">
							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="float:left; width:17%;">Empleado:</div>
								<div style="float:left; width:50%;">'.$nombre_empleado.'</div>
							</div>

							<div style="float:left; width:90%; margin:0px 5px 0px 10px">
								<div style="float:left; width:17%;">Documento:</div>
								<div style="float:left; width:50%;">'.$documento_empleado.'</div>
							</div>

							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="float:left; width:17%;">Contrato N&deg;:</div>
								<div style="float:left; width:50%;">'.$numero_contrato.' </div>
							</div>
							
							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="float:left; width:17%;">Salario B&aacute;sico;:</div>
								<div style="float:left; width:50%;">'.$salario.' </div>
							</div>
							
							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="float:left; width:17%;">Grupo de Trabajo:</div>
								<div style="float:left; width:60%;">'.$grupo_trabajo.' </div>
							</div>

							<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
								<div style="float:left; width:17%;">Dias laborados:</div>
								<div style="float:left; width:60%;">'.$dias_laborados.' </div>
							</div>
							'.$finaliza_contrato.'
						</div>
					</div>';

		$contenido = '<table class="articlesTable">
						<thead>
							<tr>
								<td style="width:50%; text-align:left;">CONCEPTOS</td>
								<td style="width:17%; text-align:left;"><b>CANTIDAD</b></td>
								<td style="width:17%; text-align:right;">DEVENGO</td>
								<td style="width:17%; text-align:right;">DEDUCCION</td>
							</tr>
						</thead>
						<tbody>
							'.$conceptos.'
							<tr style="background-color:#DDD; border-top:1px solid; border-bottom:1px">
								<td>TOTAL</td>
								<td>&nbsp;</td>
								<td style="text-align:right;">'.number_format ($acumDevengo,$_SESSION['DECIMALESMONEDA']).'</td>
								<td style="text-align:right;">'.number_format ($acumDeduccion,$_SESSION['DECIMALESMONEDA']).'</td>
							</tr>
						</tbody>
					</table>
					'.$observaciones.'
					<div style=" float:left; font-style:normal; font-size:12px; margin-left:10px; margin-top:30px;">
						<div style="width:100px; font-weight:bold; float:left;">NETO A PAGAR</div>
						<div style="width:30px; font-weight:bold; float:left;">$</div>
						<div style="text-align:right; width:120px; font-weight:bold; float:left;"> '.number_format ($acumDevengo-$acumDeduccion,$_SESSION['DECIMALESMONEDA']).'</div>
					</div>
					<table style="overflow: hidden; margin:100px 5px 0 0; margin-left:10px; font-size:12px;">
						<tr><td>_______________________________________________________</td></tr>
						<tr><td style="text-align:center;">Firma del Empleado</td></tr>
					</table>

					<style>
						.headEmpresa{
							float       : left;
							width       : 60%;
							margin-left : 10px;
							font-size   : 12px;
							overflow    : hidden;
						}

						.headEmpresa div{
							float      : left;
							text-align : center;
						}

						.articlesTable{
							font-size       : 12px;
							border-collapse : collapse;
							margin-left     : 10px;
							width           : 100%;
						}

						td{ padding : 2px 4px 2px 4px; }

						.articlesTable thead tr{ background-color:#000; }

						.articlesTable thead td{
							font-size  : 12px;
							text-align : center;
							color      : #FFF;
						}

						.articlesTable tbody tr{ border : none; }

						.StyleTableObservaciones{
							font-size		:12px;
							font-family		:"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
							// border			:1px solid #000;
							margin-top: 20px;
						}

					</style>';

		$mpdf->SetProtection(array('print'));
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHTMLHeader(utf8_encode($header));
		// $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($contenido));

		// if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
		// else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA
		$stringPdf = $mpdf->Output("volante_nomina.pdf",'S');		///OUTPUT A VISTA

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
						Se adjunta volante de nomina correspondiente al Periodo de '.$fecha_inicio.' hasta '.$fecha_final.'<br>
						Dias Laborados: '.$dias_laborados.'<br>
						Para visualizar el archivo adjunto, por favor descarguelo y abralo desde su equipo
						<br>
						<br>
						<br>
						<br>
						Esta es una notificacion automatica generada por el software LogicalSoft ERP, por favor no responda este email.
					</font><br>';
		$mail->Body = $body;

		$mail->MsgHTML($body);
		$mail->addStringAttachment($stringPdf,"volante_nomina.pdf");
		$mail->AddAddress($email_envio);
		$mail->IsHTML(true); // send as HTML

		if(!$mail->Send()) { return "false"; }
		else { return "true"; }
		$mail->ClearAddresses();
	}
	else{
		return "null";
	}
}

?>