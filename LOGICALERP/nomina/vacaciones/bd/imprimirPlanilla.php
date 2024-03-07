<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MPDF54/mpdf.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	// //CONSULTAR LA INFORMACION DE LA EMPRESA
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

	// $id_planilla=1;
	// $id_empleado=979;
	// $id_contrato=6;

	//CONSULTAR LOS DATOS DE LA PLANILLA
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

	//CONSULTAR LA INFORMACION DEL EMPLEADO EN LA PLANILLA
	$sql="SELECT id_empleado,id_contrato,dias_laborados,terminar_contrato FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$idEmpresa AND id_planilla=$id_planilla ";
	$query=mysql_query($sql,$link);
	$whereId='';
	while ($row=mysql_fetch_array($query)) {
		$whereId.=($whereId=='')? '( id_empleado = '.$row['id_empleado'].' AND id='.$row['id_contrato'].')' : ' OR ( id_empleado = '.$row['id_empleado'].' AND id='.$row['id_contrato'].')' ;
		$whereIdConceptos.=($whereIdConceptos=='')? '( id_empleado = '.$row['id_empleado'].' AND id_contrato='.$row['id_contrato'].')' : ' OR ( id_empleado = '.$row['id_empleado'].' AND id_contrato='.$row['id_contrato'].')' ;
		$arrayInfoEmpleados[$row['id_empleado']][$row['id_contrato']]= array(	'dias_laborados' => $row['dias_laborados'],
																				'terminar_contrato' => $row['terminar_contrato'] );
	}

	// $dias_laborados    = mysql_result($query,0,'dias_laborados');
	// $terminar_contrato = mysql_result($query,0,'terminar_contrato');

	// $finaliza_contrato=($terminar_contrato=='Si')? '<div style="float:left; width:90%; margin:5px 5px 0px 10px;">
	// 													<div style="float:left; width:100%;"><i><b>TERMINACION DE CONTRATO</b></i></div>
	// 												</div>' : '' ;

	//BUSCAR LA INFORMACION DEL CONTRATO Y DEL EMPLEADO
	$sql="SELECT id,id_empleado,documento_empleado,nombre_empleado,numero_contrato,grupo_trabajo,numero_cuenta_bancaria FROM empleados_contratos WHERE activo=1 AND id_empresa=$idEmpresa AND ($whereId)";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {

		$arrayEmpleadosContratos[$row['id_empleado']][$row['id']]= array('documento_empleado' 	 => $row['documento_empleado'],
																		'nombre_empleado'        => $row['nombre_empleado'],
																		'numero_contrato'        => $row['numero_contrato'],
																		'grupo_trabajo'          => $row['grupo_trabajo'],
																		'numero_cuenta_bancaria' => $row['numero_cuenta_bancaria']);

	}

	//CONSULTAR LOS CONCEPTOS DEL EMPLEADO
	$sql="SELECT id_empleado,id_contrato,id_concepto,concepto,valor_concepto,naturaleza FROM nomina_planillas_liquidacion_empleados_conceptos WHERE activo=1  AND id_planilla=$id_planilla AND ($whereIdConceptos) ORDER BY naturaleza ASC";
	$query=mysql_query($sql,$link);
	$conceptos     ='';
	$acumDevengo   =0;
	$acumDeduccion =0;
	while ($row=mysql_fetch_array($query)) {
		$arrayConceptosEmpleados[$row['id_empleado']][$row['id_contrato']][$row['id_concepto']]=array(	'concepto' => $row['concepto'],
																										'valor_concepto' => $row['valor_concepto'],
																										'naturaleza' => $row['naturaleza'] );

	}

	$contenido='';
	$id_empleado_OLD=0;
	//RECORRER LOS ARRAY Y CREAR EL CONTENIDO DEL PDF
	foreach ($arrayEmpleadosContratos as $id_empleado => $arrayResul) {

		foreach ($arrayResul as $id_contrato => $resul) {
			// TOTALES DE CADA EMPLEADO
			if ($id_empleado_OLD!=$id_empleado) {
					if ($id_empleado_OLD!=0) {
						$id_empleado_OLD=$id_empleado;
						$contenido.='<tr style="border-top:1px solid;">
									<td>TOTALES</td>
									<td style="text-align:right;">'.number_format ($acumDevengo,$_SESSION['DECIMALESMONEDA']).'</td>
									<td style="text-align:right;">'.number_format ($acumDeduccion,$_SESSION['DECIMALESMONEDA']).'</td>
								</tr>';
						$contenido.='<tr style="border:1px solid;">
									<td>NETO EMPLEADO</td>
									<td colspan="2" style="text-align:center;">'.number_format (($acumDevengo-$acumDeduccion),$_SESSION['DECIMALESMONEDA']).'</td>
								</tr>';
						$contenido.='</tbody>
									</table>';
						$acumDevengo   = 0;
						$acumDeduccion = 0;
					}
					$id_empleado_OLD=$id_empleado;
				}

			$finaliza_contrato=($arrayInfoEmpleados[$id_empleado][$id_contrato]['terminar_contrato']=='Si')? '<div style="float:left; width:90%; margin:5px 5px 0px 10px;">
																												<div style="float:left; width:100%;"><i><b>TERMINACION DE CONTRATO</b></i></div>
																											</div>' : '' ;
			//CABECERA DE LOS EMPLEADOS
			$contenido.='<div style="overflow: hidden; width:100%; margin-bottom:15px;margin-top:20px;font-size:12px;">
						<div style="float:left; width:90%; margin:0px 5px 0px 10px">
							<div style="float:left; width:30%;"><b>Documento Empleado:</b></div>
							<div style="float:left; width:50%;">'.$resul['documento_empleado'].'</div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:30%;"><b>Empleado:</b></div>
							<div style="float:left; width:50%;">'.$resul['nombre_empleado'].'</div>
						</div>

						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:30%;"><b>N. Contrato:</b></div>
							<div style="float:left; width:50%;">'.$resul['numero_contrato'].' </div>
						</div>

						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:30%;"><b>Grupo de Trabajo:</b></div>
							<div style="float:left; width:60%;">'.$resul['grupo_trabajo'].' </div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:30%;"><b>Dias Laborados:</b></div>
							<div style="float:left; width:60%;">'.$arrayInfoEmpleados[$id_empleado][$id_contrato]['dias_laborados'].' </div>
						</div>
						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:30%;"><b>Cuenta Bancaria:</b></div>
							<div style="float:left; width:60%;">'.$resul['numero_cuenta_bancaria'].' </div>
						</div>

						'.$finaliza_contrato.'

					</div>';
			// CONCEPTOS DEL EMPLEADO
			$contenido.='<table class="articlesTable">
					<thead>
						<tr>
							<td style="width:50%;">CONCEPTOS</td>
							<td style="width:25%;">DEVENGO</td>
							<td style="width:25%">DEDUCCION</td>
						</tr>
					</thead>
					<tbody>

					';

			foreach ($arrayConceptosEmpleados[$id_empleado][$id_contrato] as $id_concepto => $resulConcept) {
				$devengo   =($resulConcept['naturaleza']=='Devengo' || $resulConcept['naturaleza']=='Provision')? number_format ($resulConcept['valor_concepto'],$_SESSION['DECIMALESMONEDA']) : '' ;
				$deduccion =($resulConcept['naturaleza']=='Deduccion')? number_format ($resulConcept['valor_concepto'],$_SESSION['DECIMALESMONEDA']) : '' ;
				$contenido.='<tr>
								<td>'.$resulConcept['concepto'].'</td>
								<td style="text-align:right;">'.$devengo.'</td>
								<td style="text-align:right;">'.$deduccion.'</td>
							</tr>';
				$acumDevengo   +=($resulConcept['naturaleza']=='Devengo' || $resulConcept['naturaleza']=='Provision')? $resulConcept['valor_concepto'] : 0 ;
				$acumDeduccion +=($resulConcept['naturaleza']=='Deduccion')? $resulConcept['valor_concepto'] : 0 ;

				$acumDevengoTotal   +=($resulConcept['naturaleza']=='Devengo' || $resulConcept['naturaleza']=='Provision')? $resulConcept['valor_concepto'] : 0 ;
				$acumDeduccionTotal +=($resulConcept['naturaleza']=='Deduccion')? $resulConcept['valor_concepto'] : 0 ;

			}

		}
	}

	// TOTALES DE CADA EMPLEADO
	if ($id_empleado_OLD!=0) {
		$id_empleado_OLD=$id_empleado;
		$contenido.='<tr style="border-top:1px solid;">
					<td>TOTALES</td>
					<td style="text-align:right;">'.number_format ($acumDevengo,$_SESSION['DECIMALESMONEDA']).'</td>
					<td style="text-align:right;">'.number_format ($acumDeduccion,$_SESSION['DECIMALESMONEDA']).'</td>
				</tr>';

		$contenido.='<tr style="border:1px solid;">
						<td>NETO EMPLEADO</td>
						<td colspan="2" style="text-align:center;">'.number_format (($acumDevengo-$acumDeduccion),$_SESSION['DECIMALESMONEDA']).'</td>
					</tr>';

		$contenido.='</tbody>
					</table>';
		$acumDevengo   = 0;
		$acumDeduccion = 0;
	}

	//======================================= ARMAMOS EL DOCUMENTO =============================================//
	$header = '<div id="body_pdf" style="width:100%; font-style:normal;">
					<div style="float:left; width:445px; margin-left:10px;">
						<table style="font-size:12px;">
							<tr><td><b>'.$razon_social.'</b></td><td></td></tr>
							<tr><td>'.$tipo_regimen.'</td><td></td></tr>
							<tr><td><b>'.$tipo_documento_nombre.':</b></td><td>'.$documento_empresa.'</td></tr>
							<tr><td><b>Direccion: </b></td><td>'.$direccion_empresa.'</td></tr>
							<tr><td><b>Tels:</b></td><td>'.$telefonos.'</td></tr>
							<tr><td>'.$ciudad.'</td><td></td></tr>
						</table>
					</div>
					<div style="float:left;width:30%;text-align:center;font-size:16px;">
						<b>PLANILLA DE NOMINA<br> N. '.$consecutivo.'</b><br>
						<div style="font-size:12px !important;">Periodo '.$fecha_inicio.' / '.$fecha_final.'</div><br/>
					</div>
					<br>

				</div>';



	$contenido.= '
				<table class="articlesTable" style="font-size:12px;margin-top:30px;">
					<thead>
						<tr>
							<td style="width:25%;">TOTAL DEVENGO</td>
							<td style="width:25%;">TOTAL DEDUCCION</td>
							<td style="width:50%">NETO A PAGAR</td>
						</tr>
					</thead>
					<tbody>
						<tr >
							<td style="text-align:center;">'.number_format ($acumDevengoTotal,$_SESSION['DECIMALESMONEDA']).'</td>
							<td style="text-align:center;">'.number_format ($acumDeduccionTotal,$_SESSION['DECIMALESMONEDA']).'</td>
							<td style="text-align:center;">'.number_format ($acumDevengoTotal-$acumDeduccionTotal,$_SESSION['DECIMALESMONEDA']).'</td>
						</tr>
					</tbody>
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

	// echo $contenido;exit;
	if(isset($TAM)){$HOJA = $TAM;}else{	$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'true';}

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS= 45 ; $MD = 10;$MI = 15;$ML = 10; }		//con imagen ms=86 sin imagen ms=71
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