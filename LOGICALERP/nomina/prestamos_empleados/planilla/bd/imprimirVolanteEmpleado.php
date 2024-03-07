<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../../misc/MPDF54/mpdf.php");

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
	$estado       = mysql_result($query,0,'estado');
	$sucursal     = mysql_result($query,0,'nombre');
	$departamento = mysql_result($query,0,'departamento');
	$ciudad       = mysql_result($query,0,'ciudad');

	if ($estado==0) { echo "<center><b><i>El documento no esta generado</i></b></center>"; exit; }
	if ($estado==3) { echo "<center><b><i>El documento se encuentra cancelado</i></b></center>"; exit; }

	//CONSULTAR LA INFORMACION DEL EMPLEADO EN LA PLANILLA
	$sql = "SELECT id_empleado,id_contrato,dias_laborados,terminar_contrato
			FROM nomina_planillas_empleados
			WHERE activo=1
				AND id_empresa  = $id_empresa
				AND id_planilla = $id_planilla
				AND id_empleado = $id_empleado";
	$query = mysql_query($sql,$link);

	$dias_laborados = mysql_result($query,0,'dias_laborados');
	$id_contrato    = mysql_result($query,0,'id_contrato');

	//BUSCAR LA INFORMACION DEL CONTRATO Y DEL EMPLEADO
	$sql = "SELECT id,id_empleado,documento_empleado,nombre_empleado,numero_contrato,grupo_trabajo
			FROM empleados_contratos
			WHERE activo=1
				AND id = $id_contrato
				AND id_empresa  = $id_empresa
				AND id_empleado = $id_empleado";
	$query = mysql_query($sql,$link);

	while ($row=mysql_fetch_array($query)) {
		$documento_empleado = $row['documento_empleado'];
		$nombre_empleado    = $row['nombre_empleado'];
		$numero_contrato    = $row['numero_contrato'];
		$grupo_trabajo      = $row['grupo_trabajo'];
	}

	//CONSULTAR LOS CONCEPTOS DEL EMPLEADO
	$sql = "SELECT id_empleado,id_contrato,id_concepto,concepto,valor_concepto,naturaleza
			FROM nomina_planillas_empleados_conceptos
			WHERE activo=1
				AND id_planilla=$id_planilla
				AND imprimir_volante='true'
				AND id_empleado=$id_empleado
				AND id_contrato=$id_contrato
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
							<td style="text-align:right; background-color:'.$color.'">'.$devengo.'</td>
							<td style="text-align:right; background-color:'.$color.'">'.$deduccion.'</td>
						</tr>';

		$acumDevengo   += ($row['naturaleza']=='Devengo')? $row['valor_concepto'] : 0 ;
		$acumDeduccion += ($row['naturaleza']=='Deduccion')? $row['valor_concepto'] : 0 ;
	}

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
							<div style="float:left; width:17%;">Grupo de Trabajo:</div>
							<div style="float:left; width:60%;">'.$grupo_trabajo.' </div>
						</div>

						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:17%;">Dias laborados:</div>
							<div style="float:left; width:60%;">'.$dias_laborados.' </div>
						</div>
					</div>
				</div>';

	$contenido = '<table class="articlesTable">
					<thead>
						<tr>
							<td style="width:50%; text-align:left;"><b>CONCEPTOS</b></td>
							<td style="width:25%; text-align:right;"><b>DEVENGO</b></td>
							<td style="width:25%; text-align:right;"><b>DEDUCCION</b></td>
						</tr>
					</thead>
					<tbody>
						'.$conceptos.'
						<tr style="background-color:#DDD; border-top:1px solid; border-bottom:1px">
							<td>TOTAL</td>
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
				</style>';

	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS= 65 ; $MD = 10;$MI = 10;$ML = 10; }		//con imagen ms=86 sin imagen ms=71
	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

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

	//===================// MARCA DE AGUA //===================//
	// $mpdf->SetWatermarkText('COPIA');
	// $mpdf->watermark_font = 'DejaVuSansCondensed';
	// $mpdf->showWatermarkText = true;

	// $mpdf->SetAutoPageBreak(TRUE, 15);
	// $mpdf->SetTitle ( $documento );
	// $mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
	$mpdf->SetProtection(array('print'));
	$mpdf->SetDisplayMode ('fullpage');
	$mpdf->SetHTMLHeader(utf8_encode($header));
	// $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
	$mpdf->WriteHTML(utf8_encode($contenido));

	// if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
	// else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA
	$mpdf->Output("volante_nomina.pdf",'I');		///OUTPUT A VISTA

?>