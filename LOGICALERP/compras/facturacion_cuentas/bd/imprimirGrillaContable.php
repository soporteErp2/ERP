<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../bd/functions_imprimir.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	$id_empresa       = $_SESSION['EMPRESA'];
	$labelConsecutivo = 'Consecutivo No.';
	$titulo           = 'FACTURA DE COMPRA';
	$estilo           = 'background-color: #EEE;';
	$cuentasMostrar   = '<br>&nbsp;';

	//CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento, pais,ciudad,direccion,razon_social,telefono,celular,tipo_regimen FROM empresas WHERE id='$id_empresa' LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$tipo_documento_nombre = mysql_result($queryEmpresa,0,'tipo_documento_nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$ubicacion_empresa     = mysql_result($queryEmpresa,0,'ciudad').' - '.mysql_result($queryEmpresa,0,'pais');
	$direccion_empresa     = mysql_result($queryEmpresa,0,'direccion');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');
	$tipo_regimen          = mysql_result($queryEmpresa,0,'tipo_regimen');
	$telefonos 			   = mysql_result($queryEmpresa,0,'telefono').' - '.mysql_result($queryEmpresa,0,'celular');

	$marcaAgua   = 'false';
	$acumDebito  = 0;
	$acumCredito = 0;

	//==================================// QUERY FC //==================================//
	//**********************************************************************************//
	$sqlFc   = "SELECT F.*,
					T.direccion,
					T.telefono1,
					T.departamento,
					T.ciudad,
					T.tercero_tributario,
					IF(T.dv > 0, CONCAT(T.numero_identificacion,' - ',T.dv), T.numero_identificacion) AS nit_tercero
				FROM $tablaPrincipal AS F LEFT JOIN terceros AS T ON(
						F.id_proveedor = T.id
					)
				WHERE F.id='$id'
					AND F.activo=1
					AND F.id_empresa=$id_empresa
				LIMIT 0,1";
	$queryFc = mysql_query($sqlFc,$link);

	if (!$queryFc){ die('Informe no valido '.mysql_error()); exit; }

	$estado      = mysql_result($queryFc, 0, 'estado');
	$consecutivo = mysql_result($queryFc, 0, 'consecutivo');
	$proveedor   = mysql_result($queryFc, 0,'proveedor');
	$cuenta_pago = mysql_result($queryFc, 0, 'cuenta_pago');
	$observacion = mysql_result($queryFc, 0, 'observacion');
	$id_sucursal = mysql_result($queryFc, 0, 'id_sucursal');

	$tercero_dpto      = mysql_result($queryFc, 0,'departamento');
	$tercero_ciudad    = mysql_result($queryFc, 0,'ciudad');
	$tercero_regimen   = mysql_result($queryFc, 0,'tercero_tributario');

	$tercero_nit       = mysql_result($queryFc, 0,'nit_tercero');
	$tercero_telefono  = mysql_result($queryFc, 0,'telefono1');
	$tercero_direccion = mysql_result($queryFc, 0,'direccion');

	if ($estado==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }
	else if($estado==3){ $marcaAgua = 'true'; }


	//SI SE MUESTRAN LAS CUENTAS COLGAAP O NIIF
	if ($cuentas=='niif') {
		$tablaCuentas   = "asientos_niif";
		$cuentasMostrar = '<br>CUENTAS NIIF';
	}
	else{
		$tablaCuentas = "asientos_colgaap";
	}

	//=======================// ITEMS (CUENTAS) //=======================//
	//*******************************************************************//
	$sqlCuentas   = "SELECT id_documento,
							tipo_documento,
							id_documento_cruce,
							tipo_documento_cruce,
							numero_documento_cruce,
							codigo_cuenta,
							cuenta,
							debe,
							haber,
							tercero
						FROM $tablaCuentas
						WHERE activo=1
							AND id_empresa='$id_empresa'
							AND tipo_documento='FC'
							AND id_documento='$id'";
	$queryCuentas = mysql_query($sqlCuentas,$link);

	$items = '';
	while ($array= mysql_fetch_array($queryCuentas)) {
		// $tercero = '';
		$tercero = $array["tercero"];
		$estilo  = ($estilo!="")? '': 'background-color: #EEE;';

		$acumDebito  += $array["debe"];
		$acumCredito += $array["haber"];

		$array["debe"]  = number_format($array["debe"],$_SESSION['DECIMALESMONEDA']);
		$array["haber"] = number_format($array["haber"],$_SESSION['DECIMALESMONEDA']);

		$tipo_documento_cruce = '';

		if($array["tipo_documento_cruce"]!=$array["tipo_documento"] || $array["id_documento_cruce"]!=$array["id_documento"]){
			$tercero = $array["tercero"];
			$tipo_documento_cruce = $array["tipo_documento_cruce"].' '.$array["numero_documento_cruce"];
		}

		$items .= '<tr style="'.$estilo.'">
						<td width="55">'.$array["codigo_cuenta"].'</td>
						<td width="210" style="text-align:left; padding-left:5px;">'.$array["cuenta"].'</td>
						<td width="200" style="text-align:left; padding-left:5px;">'.$tercero.'&nbsp;</td>
						<td width="40" style="text-align:center;">'.$tipo_documento_cruce.' </td>
						<td width="80" style="text-align:right;">'.$array["debe"].'</td>
					 	<td width="80" style="text-align:right;">'.$array["haber"].'</td>
					</tr>';
	}

	// AGREGAR LOS TOTALES
	$items .= '<tr style="border-top:1px solid;">
					<td style="width:55px;" colspan="4">TOTAL</td>
					<td style="width:80px; text-align:right;">'.number_format($acumDebito,$_SESSION['DECIMALESMONEDA']).'</td>
				 	<td style="width:80px; text-align:right;">'.number_format($acumCredito,$_SESSION['DECIMALESMONEDA']).'</td>
				</tr>';

	$arrayReplaceString = array("\n", "\r");
	$observacion = str_replace($arrayReplaceString, "<br/>", $observacion );

	//==================================// FACTURA DE COMPRA //==================================//
	//*******************************************************************************************//
	$contenido = '<div id="body_pdf" style="width:100%; font-style:normal; font-size:11px;">
					<div style="overflow: hidden; width:100%; margin-top:20px;">
						<div style="float:left; width:90%; margin:0px 5px 0px 10px">
							<div style="float:left; width:15%;"><b>Proveedor:</b></div>
							<div style="float:left; width:45%;">'.$proveedor.'</div>
							<div style="float:left; width:15%;"><b>Departamento:</b></div>
							<div style="float:left; width:25%;">'.$tercero_dpto.'</div>
						</div>

						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:15%;"><b>Nit:</b></div>
							<div style="float:left; width:45%;">'.$tercero_nit.'</div>
							<div style="float:left; width:15%;"><b>Ciudad:</b></div>
							<div style="float:left; width:25%;">'.$tercero_ciudad.'</div>
						</div>

						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:15%;"><b>Direccion:</b></div>
							<div style="float:left; width:45%;">'.$tercero_direccion.'</div>
							<div style="float:left; width:15%;"><b>Regimen:</b></div>
							<div style="float:left; width:25%;">'.$tercero_regimen.'</div>
						</div>

						<div style="float:left; width:90%; margin:0px 5px 0px 10px;">
							<div style="float:left; width:15%;"><b>Telefono:</b></div>
							<div style="float:left; width:45%;">'.$tercero_telefono.'</div>
						</div>
					</div>
				</div>
				<div style="width:100%; font-style:normal; font-size:11px; margin:0px; pdding-top:30px;">
					<table class="articlesTable">
						<thead>
							<tr>
								<td>CUENTA</td>
								<td>DESCRIPCION</td>
								<td>TERCERO</td>
								<td>DOC. CRUCE</td>
								<td>DEBITO</td>
								<td>CREDITO</td>
							</tr>
						</thead>
						<tbody>'.$items.'</tbody>
					</table>
					<br/>
				</div>

				<div style=" float:left">
					<br>
					<div style="overflow: hidden; width:100%; font-size:12px;">
						<div style="float:left; width:90%; margin:5px 5px 0px 0px;">Observaciones</div>
						<div style="float:left; width:100%; margin:3px 200px 5px 0px; padding:5px 10px 5px 10px; border: 1px solid; height:40px;">'.$observacion.'</div>
					</div>
				</div>

			<style>
				.articlesTable{
					font-size       : 10px;
					border-collapse : collapse;
					width           : 100%;
				}

				.articlesTable td{ border-collapse : collapse; }
				.articlesTable tbody tr{ border : none; }

				.articlesTable thead td{
					text-align       : center;
					font-size        : 11px;
					font-family      : "Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
					background-color : #000;
					color            : #FFF;
				}

			</style>';


	$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND id_sucursal = '$id_sucursal' AND tipo='FC' AND activo=1";
	$queryConfig = mysql_query($sqlConfig,$link);
	$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

	if($contConfig == 0){

		$sqlConfig   = "SELECT COUNT(id) AS contConfig, id FROM configuracion_documentos_erp WHERE id_empresa = '$id_empresa' AND tipo='FC' AND activo=1";
		$queryConfig = mysql_query($sqlConfig,$link);
		$contConfig  = mysql_result($queryConfig, 0, 'contConfig');

		if($contConfig > 0){

			$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal) VALUES ('Factura de Compra','FC','$id_empresa','$id_sucursal')";
			$queryInsert = mysql_query($sqlInsert,$link);
		}
		else{

			$sqlConfig   = "SELECT COUNT(id) AS contConfig, id, texto FROM configuracion_documentos_erp WHERE tipo='FC' AND activo=1";
			$queryConfig = mysql_query($sqlConfig,$link);
			$contConfig  = mysql_result($queryConfig, 0, 'contConfig');
			$textoConfig = mysql_result($queryConfig, 0, 'texto');

			if ($contConfig > 0) {
				$sqlInsert   = "INSERT INTO configuracion_documentos_erp (nombre,tipo,id_empresa,id_sucursal,texto) VALUES ('Factura de Compra','FC','$id_empresa','$id_sucursal','$textoConfig')";
				$queryInsert = mysql_query($sqlInsert,$link);
			}
		}
	}

	$documento      = "Factura_de_Compra";
	$textoPlantilla = cargaFormatoDocumento($id_empresa,$id_sucursal,'FC');
	$texto          = reemplazarVariables($textoPlantilla,$contenido,$id_empresa,$id_sucursal,$id);

	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS = 50; $MD = 10; $MI = 10; $ML = 10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

	if($IMPRIME_PDF){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',   					// mode - default ''
			$HOJA,						// format - A4, for example, default ''
			12,							// font size - default 0
			'',							// default font family
			$MI,						// margin_left
			$MD,						// margin right
			$MS,						// margin top
			$ML,						// margin bottom
			2,							// margin header
			2,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);

		$mpdf->SetProtection(array('print'));
		if($marcaAgua=='true'){
            $mpdf->SetWatermarkText('ANULADO');
            $mpdf->showWatermarkText = true;
        }
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ($documento);
		$mpdf->SetAuthor ($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ('fullpage');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	///OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I'); }		///OUTPUT A VISTA

		exit;
	}
	else{ echo $texto; }

?>