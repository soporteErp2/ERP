<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MPDF54/mpdf.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	// CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento,actividad_economica,pais,ciudad,direccion,razon_social,tipo_regimen,telefono,celular FROM empresas WHERE id='$idEmpresa' LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$ciudad     = mysql_result($queryEmpresa,0,'ciudad');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');
	$telefonos 			   = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');
	$actividad_economica   = mysql_result($queryEmpresa,0,'actividad_economica');


	// CONSULTAR LOS DATOS DE LA PLANILLA
	$sql="SELECT consecutivo,fecha_inicio,fecha_final,estado FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$idEmpresa AND id=$id_planilla";
	$query=mysql_query($sql,$link);

	$consecutivo  = mysql_result($query,0,'consecutivo');
	$fecha_inicio = mysql_result($query,0,'fecha_inicio');
	$fecha_final  = mysql_result($query,0,'fecha_final');
	$estado  = mysql_result($query,0,'estado');

	if ($estado==0) {
		echo "<center><b><i>El documento no esta generado</i></b></center>";
		exit;
	}
	if ($estado==3) {
		echo "<center><b><i>El documento se encuentra cancelado</i></b></center>";
		exit;
	}

	// CONSULTAR LA INFORMACION DEL EMPLEADO EN LA PLANILLA
	$sql="SELECT id_empleado,id_contrato,dias_laborados,terminar_contrato,motivo_fin_contrato
			FROM nomina_planillas_liquidacion_empleados
			WHERE
			activo=1
			AND id_empresa=$idEmpresa
			AND id_planilla=$id_planilla
			AND id_empleado=$id_empleado";
	$query=mysql_query($sql,$link);

	$dias_laborados      = mysql_result($query,0,'dias_laborados');
	$terminar_contrato   = mysql_result($query,0,'terminar_contrato');
	$id_contrato         = mysql_result($query,0,'id_contrato');
	$motivo_fin_contrato = mysql_result($query,0,'motivo_fin_contrato');

	//BUSCAR LA INFORMACION DEL CONTRATO Y DEL EMPLEADO
	$sql="SELECT id,id_empleado,documento_empleado,nombre_empleado,numero_contrato,grupo_trabajo,fecha_inicio_contrato,tipo_contrato,fecha_cancelacion
			FROM empleados_contratos
			WHERE
			activo=1
			AND id_empresa=$idEmpresa
			AND id_empleado=$id_empleado
			AND id=$id_contrato";
	$query=mysql_query($sql,$link);

	$documento_empleado    = mysql_result($query,0,'documento_empleado');
	$nombre_empleado       = mysql_result($query,0,'nombre_empleado');
	$numero_contrato       = mysql_result($query,0,'numero_contrato');
	$grupo_trabajo         = mysql_result($query,0,'grupo_trabajo');
	$fecha_inicio_contrato = mysql_result($query,0,'fecha_inicio_contrato');
	$fecha_cancelacion    = mysql_result($query,0,'fecha_cancelacion');
	$tipo_contrato         = mysql_result($query,0,'tipo_contrato');


		$finaliza_contrato =($terminar_contrato=='Si')?'<div style="float:right;width:40%;">
														<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
															<div style="float:left; width:100%;">
																<table style="font-size:12px;">
																	<tr><td colspan="3"><i><b>TERMINACION DE CONTRATO</b></i></td></tr>
																	<tr><td><b>Motivo: </b></td><td>'.$motivo_fin_contrato.'</td></tr>
																	<tr><td><b>Fecha Ingreso: </b></td><td>'.$fecha_inicio_contrato.'</td></tr>
																	<tr><td><b>Fecha Retiro: </b></td><td>'.$fecha_cancelacion.'</td></tr>
																</table>
															</div>
														</div>
													</div>'
													: '<div style="float:right;width:40%;height:100px;"></div>' ;


	// $finaliza_contrato=($terminar_contrato=='Si')? '<div style="float:left; width:90%; margin:5px 5px 0px 10px;">
	// 													<div style="float:left; width:100%;"><i><b>TERMINACION DE CONTRATO'.(($motivo_fin_contrato!='')? ' <br>MOTIVO : '.$motivo_fin_contrato : '' ).'</b></i></div>
	// 												</div>' : '' ;

	//CONSULTAR LOS CONCEPTOS DEL EMPLEADO
	$sql="SELECT id_empleado,id_contrato,id_concepto,codigo_concepto,concepto,valor_concepto,valor_concepto_ajustado,naturaleza,dias_laborados,base,dias_adicionales
			FROM nomina_planillas_liquidacion_empleados_conceptos
			WHERE
			activo=1
			AND id_planilla=$id_planilla /*AND imprimir_volante='true'*/
			AND id_empleado=$id_empleado
			AND id_contrato=$id_contrato
			ORDER BY naturaleza ASC";
	$query=mysql_query($sql,$link);
	$conceptos     ='';
	$acumDevengo   =0;
	$acumDeduccion =0;
	$style='color:#FFF';
	while ($row=mysql_fetch_array($query)) {
		$arrayConceptos[$row['codigo_concepto']] = array(
																				'concepto'                => $row['concepto'],
																				'valor_concepto'          => $row['valor_concepto'],
																				'valor_concepto_ajustado' => $row['valor_concepto_ajustado'],
																				'naturaleza'              => $row['naturaleza'],
																				'dias_laborados'          => $row['dias_laborados'],
																				'dias_adicionales'        => $row['dias_adicionales'],
																				'base'                    => ($row['base']/$row['dias_laborados'])*30
																				);
		// <td style="'.$style.'text-align:right;">'.number_format ((($arrayResul['base']/$arrayResul['dias_laborados'])*30),$_SESSION['DECIMALESMONEDA']).'</td>

	}

	foreach ($arrayConceptos as $codigo_concepto => $arrayResul) {
		if ($codigo_concepto=='ISC') {
			$arrayResul['base']=$arrayConceptos['CS']['valor_concepto_ajustado'];
		}

		$style=($style!='')? '' : 'background:#F7F7F7;' ;
		$valor_concepto = ($arrayResul['valor_concepto_ajustado']>0)? $arrayResul['valor_concepto_ajustado'] : $arrayResul['valor_concepto'] ;

		$devengo   =($arrayResul['naturaleza']=='Devengo' || $arrayResul['naturaleza']=='Provision')? number_format ($valor_concepto,$_SESSION['DECIMALESMONEDA']) : '' ;
		$deduccion =($arrayResul['naturaleza']=='Deduccion')? number_format ($valor_concepto,$_SESSION['DECIMALESMONEDA']) : '' ;
		$conceptos.='<tr>
						<td style="'.$style.'">'.$arrayResul['concepto'].'</td>
						<td style="'.$style.'text-align:right;">'.($arrayResul['dias_laborados']+$arrayResul['dias_adicionales']).'</td>
						<td style="'.$style.'text-align:right;">'.$devengo.'</td>
						<td style="'.$style.'text-align:right;">'.$deduccion.'</td>
					</tr>';

		$acumDevengo   +=($arrayResul['naturaleza']=='Devengo' || $arrayResul['naturaleza']=='Provision')? $valor_concepto : 0 ;
		$acumDeduccion +=($arrayResul['naturaleza']=='Deduccion')? $valor_concepto : 0 ;
	}


	$contenido='';
	$id_empleado_OLD=0;


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
						<div style="font-size:16px;font-weight:bold;">PLANILLA DE LIQUIDACION<br/>N&deg; '.$consecutivo.'</div>
						<div style="font-size:12px !important;">Periodo '.$fecha_inicio.' / '.$fecha_final.'</div>
						<br/>
					</div>

				</div>';



	$contenido.= '
					<br>
					<div style="overflow: hidden; width:100%; margin-bottom:15px;margin-top:20px;font-size:12px;">
					<div style="width:59%;float:left;">

						<div style="float:left; width:100%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:40%;"><b>Empleado:</b></div>
							<div style="float:left; width:50%;">'.$nombre_empleado.'</div>
						</div>

						<div style="float:left; width:100%; margin:0px 5px 0px 10px">
							<div style="float:left; width:40%;border:"><b>Documento Empleado:</b></div>
							<div style="float:left; width:50%;border:">'.$documento_empleado.'</div>
						</div>

						<div style="float:left; width:100%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:40%;"><b>Grupo de Trabajo:</b></div>
							<div style="float:left; width:50%;">'.$grupo_trabajo.' </div>
						</div>

						<div style="float:left; width:100%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:40%;"><b>N. Contrato:</b></div>
							<div style="float:left; width:50%;">'.$numero_contrato.' </div>
						</div>

						<div style="float:left; width:100%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:40%;"><b>Tipo de Contrato:</b></div>
							<div style="float:left; width:50%;">'.$tipo_contrato.' </div>
						</div>

					</div>

					'.$finaliza_contrato.'

					<br/><br/>
					<table class="articlesTable">
					<thead>
						<tr>
							<td style="width:50%;">CONCEPTOS</td>
							<td style="text-align:right;">DIAS</td>
							<td style="text-align:right;">DEVENGO</td>
							<td style="text-align:right;">DEDUCCION</td>
						</tr>
					</thead>
					<tbody>
						'.$conceptos.'
						<tr class="total" style="">
							<td colspan="2">TOTALES</td>
							<td style="text-align:right;">'.number_format ($acumDevengo,$_SESSION['DECIMALESMONEDA']).'</td>
							<td style="text-align:right;">'.number_format ($acumDeduccion,$_SESSION['DECIMALESMONEDA']).'</td>
						</tr>
					</tbody>
				</table>

				<div style=" float:left; font-style:normal; font-size:12px; margin-left:10px; margin-top:30px;">
					<div style="width:100px; font-weight:bold; float:left;">NETO A PAGAR</div>
					<div style="width:30px; font-weight:bold; float:left;">$</div>
					<div style="text-align:right; width:120px; font-weight:bold; float:left;"> '.number_format ($acumDevengo-$acumDeduccion,$_SESSION['DECIMALESMONEDA']).'</div>
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
						border          : 1px solid #999;
						border-collapse : collapse;
						margin-left     : 10px;
						width           : 100%;
					}

					.articlesTable td{
						padding         : 2px;
						/*border          : 1px solid #000;*/
						border-collapse : collapse;
					}

					.articlesTable thead td{
						padding-left : 10px;
						height       : 30px;
						background   : #999;
						color        : #FFF;
						font-weight  : bold;
					}
					.total {
					  background: #EEE;
					  font-weight: bold;
					}

					.total td {
					  border-top: 1px solid #999;
					  border-bottom: 1px solid #999;
					  background: #EEE;
					  padding-left: 10px;
					  height: 25px;
					  font-weight: bold;
					  color: #8E8E8E;
					}
				</style>';

	// $contenido='jgjkvbkjnlj';

				// echo $contenido;
				// exit;


	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS=30 ; $MD = 10;$MI = 10;$ML = 10; }		//con imagen ms=86 sin imagen ms=71
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12;}




	// echo "string";
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
// echo 'in';
	/*/////// MARCA DE AGUA
	$mpdf->SetWatermarkText('COPIA');
	$mpdf->watermark_font = 'DejaVuSansCondensed';
	$mpdf->showWatermarkText = true;
	*/
	$documento='volante_nomina';
	$mpdf->SetProtection(array('print'));
	// $mpdf->SetAutoPageBreak(TRUE, 15);
	// $mpdf->SetTitle ( $documento );
	// $mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
	$mpdf->SetDisplayMode ( 'fullpage' );
	$mpdf->SetHTMLHeader(utf8_encode($header));
	// $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
	$mpdf->WriteHTML(utf8_encode($contenido));

	// if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
	// else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA
	$mpdf->Output($documento.".pdf",'I');		///OUTPUT A VISTA



?>