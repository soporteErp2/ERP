<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	if (!isset($_SESSION['EMPRESA']) || $_SESSION['EMPRESA']==''){ exit; }

	ob_start();

	$id_empresa       = $_SESSION['EMPRESA'];
	$documento        = "Nota General";
	$estilo           = 'background-color: #DFDFDF;';
	$cuentasMostrar   = '<br>&nbsp;';

	//CONSULTAR LA INFORMACION DE LA EMPRESA
	$sqlEmpresa   = "SELECT nombre,tipo_documento_nombre,documento, pais,ciudad,direccion,razon_social,telefono,celular,tipo_regimen FROM empresas WHERE id='$id_empresa' LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);

	$nombre_empresa        = mysql_result($queryEmpresa,0,'nombre');
	$documento_empresa     = mysql_result($queryEmpresa,0,'documento');
	$razon_social          = mysql_result($queryEmpresa,0,'razon_social');

	$acumDebito  = 0;
	$acumCredito = 0;

	$sqlNota   = "SELECT N.estado,
						N.consecutivo_niif,
						N.observacion,
						N.tipo_nota,
						N.fecha_registro,
						N.tercero,
						N.sucursal,
						N.usuario,
						N.fecha_nota,
						IF(T.tipo='Empresa' && T.dv>0, CONCAT(T.numero_identificacion,'-',T.dv),T.numero_identificacion) AS nit
					FROM $tablaPrincipal AS N LEFT JOIN terceros AS T ON(
						N.id_tercero = T.id
						)
					WHERE N.id='$id'
						AND N.activo=1
						AND N.id_empresa=$id_empresa";
	$queryNota = mysql_query($sqlNota,$link);
	if (!$queryNota){ die('No se cargo el documento'.mysql_error()); }

	$estado         = mysql_result($queryNota, 0, 'estado');
	$consecutivo    = mysql_result($queryNota, 0, 'consecutivo_niif');
	$observacion    = mysql_result($queryNota, 0, 'observacion');
	$tipo_nota      = mysql_result($queryNota, 0, 'tipo_nota');
	$fecha_registro = mysql_result($queryNota, 0, 'fecha_registro');
	$tercero        = mysql_result($queryNota, 0, 'tercero');
	$nit_tercero    = mysql_result($queryNota, 0, 'nit');
	$sucursal       = mysql_result($queryNota, 0, 'sucursal');
	$usuario        = mysql_result($queryNota, 0, 'usuario');
	$fecha_nota     = mysql_result($queryNota, 0, 'fecha_nota');


	if ($estado==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }
	else if ($estado==3) { echo "<center><h2><i>Documento Cancelado</i></h2></center>"; exit; }

	//SI SE MUESTRAN LAS CUENTAS COLGAAP O NIIF
	if ($cuentas=='niif') {
		$camposBd       = 'cuenta_niif AS cuenta_puc,descripcion_niif AS descripcion_puc,';
		$cuentasMostrar = '<br>CUENTAS NIIF';
	}
	else{ $camposBd='cuenta_puc,descripcion_puc,'; }

	//CONSULTAR SI LA NOTA TIENE CRUCE DE DOCUMENTOS
	$sqlCruce   = "SELECT id_documento_cruce FROM $tablaCuentasNota WHERE $idTablaPrincipal='$id' AND id_documento_cruce>0 AND activo=1 LIMIT 0,1";
	$queryCruce = mysql_query($sqlCruce,$link);
	$documento_cruce = (mysql_result($queryCruce,0,'id_documento_cruce') > 0)? 'Si': 'No';

	//CUENTAS DE LAS NOTAS
	$sqlCuentas   = "SELECT $camposBd
						tipo_documento_cruce,
						id_documento_cruce,
						prefijo_documento_cruce,
						numero_documento_cruce,
						debe,
						haber,
						nit_tercero,
						tercero
					FROM $tablaCuentasNota
					WHERE $idTablaPrincipal='$id'
						AND activo=1";
	$queryCuentas = mysql_query($sqlCuentas,$link);

	while ($array= mysql_fetch_array($queryCuentas)) {
		$estilo = ($estilo!='')? '': 'background-color: #DFDFDF;';

		$acumDebito  += $array["debe"];
		$acumCredito += $array["haber"];

		$array["debe"]  = number_format($array["debe"] * 1,$_SESSION['DECIMALESMONEDA']);
		$array["haber"] = number_format($array["haber"] * 1,$_SESSION['DECIMALESMONEDA']);

		if ($documento_cruce == 'Si') {
			$numero_documento_cruce = ($array["prefijo_documento_cruce"]!="")? $array["prefijo_documento_cruce"].' '.$array["numero_documento_cruce"] : $array["numero_documento_cruce"] ;
			$numero_documento_cruce = ($array["tipo_documento_cruce"] == "")? '&nbsp;': $numero_documento_cruce;
			$tipo_documento_cruce   = ($array["tipo_documento_cruce"] == "")? '&nbsp;': $array["tipo_documento_cruce"];

			$tbody .= '<tr style="'.$estilo.'">
							<td style="width:50px;">'.$array["cuenta_puc"].'</td>
							<td style="width:168px; text-align:left; padding-left:5px;">'.$array["descripcion_puc"].'</td>
							<td style="width:68px; text-align:left; padding-left:5px;">'.$array["nit_tercero"].'&nbsp;</td>
							<td style="width:170px; text-align:left; padding-left:5px;">'.$array["tercero"].'&nbsp;</td>
							<td style="width:30px; text-align:left; padding-left:5px;">'.$tipo_documento_cruce.'</td>
							<td style="width:60px; text-align:left; padding-left:5px;">'.$numero_documento_cruce.'</td>
							<td style="width:70px; text-align:right; padding-left:5px;">'.$array["debe"].'</td>
							<td style="width:70px; text-align:right;">'.$array["haber"].'</td>
						</tr>';
		}
		else{
			$tbody .= '<tr style="'.$estilo.'">
							<td style="width:50px;">'.$array["cuenta_puc"].'</td>
							<td style="width:240px; text-align:left; padding-left:5px;">'.$array["descripcion_puc"].'</td>
							<td style="width:70px; text-align:left; padding-left:5px;">'.$array["nit_tercero"].'&nbsp;</td>
							<td style="width:170px; text-align:left; padding-left:5px;">'.$array["tercero"].'&nbsp;</td>
							<td style="width:70px; text-align:right; padding-left:5px;">'.$array["debe"].'</td>
							<td style="width:70px; text-align:right;">'.$array["haber"].'</td>
						</tr>';
		}
	}

	if ($documento_cruce == 'Si') {
		$thead = '<tr style="background-color:#000;">
						<td style="width:50px; color:#fff; padding-left:2px;">CUENTA</td>
						<td style="width:170px; color:#fff; padding-left:2px;">DESCRIPCION</td>
						<td style="width:70px; color:#fff; padding-left:2px;">NIT</td>
						<td style="width:170px; color:#fff; padding-left:2px;">TERCERO</td>
						<td colspan="2" style="width:90px; text-align:left; color:#fff; padding-left:5px;">DOC. CRUCE</td>
						<td style="width:70px; color:#fff; padding-left:2px;text-align:right;">DEBITO</td>
						<td style="width:70px; color:#fff; padding-left:2px;text-align:right;">CREDITO</td>
					</tr>';

		$tfooter = '<tr style="background-color:#000;">
						<td colspan="6" style="color:#fff; padding-left:2px;">TOTAL</td>
						<td style="color:#fff; padding-left:2px;text-align:right;">'.number_format($acumDebito * 1,$_SESSION['DECIMALESMONEDA']).'</td>
						<td style="color:#fff; padding-left:2px;text-align:right;">'.number_format($acumCredito * 1,$_SESSION['DECIMALESMONEDA']).'</td>
					</tr>';
	}
	else{

		$thead = '<tr style="background-color:#000;">
						<td style="width:50px; color:#fff; padding-left:2px;">CUENTA</td>
						<td style="width:240px; color:#fff; padding-left:2px;">DESCRIPCION</td>
						<td style="width:70px; color:#fff; padding-left:2px;">NIT</td>
						<td style="width:170px; color:#fff; padding-left:2px;">TERCERO</td>
						<td style="width:70px; color:#fff; padding-left:2px;text-align:right;">DEBITO</td>
						<td style="width:70px; color:#fff; padding-left:2px;text-align:right;">CREDITO</td>
					</tr>';

		$tfooter = '<tr style="background-color:#000;">
						<td colspan="4" style="color:#fff; padding-left:2px;">TOTAL</td>
						<td style="color:#fff; padding-left:2px;text-align:right;">'.number_format($acumDebito * 1,$_SESSION['DECIMALESMONEDA']).'</td>
						<td style="color:#fff; padding-left:2px;text-align:right;">'.number_format($acumCredito * 1,$_SESSION['DECIMALESMONEDA']).'</td>
					</tr>';
	}

	$arrayReplaceString = array("\n", "\r");
	$observacion = str_replace($arrayReplaceString, "<br/>", $observacion );

?>

<style>
	.my_informe_Contenedor_Titulo_informe{
        float       : left;
        width       : 100%;
        margin      : 0 0 10px 0;
        font-size   : 11px;
        font-family : "Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
	}
	.my_informe_Contenedor_Titulo_informe_label{
        float       : left;
        width       : 130px;
        font-weight : bold;
	}
	.my_informe_Contenedor_Titulo_informe_detalle{
        float         :	left;
        width         :	210px;
        padding       :	0 0 0 5px;
        white-space   : nowrap;
        overflow      : hidden;
        text-overflow : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
        float     :	left;
        width     :	100%;
        font-size : 16px;
	}
    .my_informe_Contenedor_Titulo_informe td{ padding-left : 2px; }
    .tablaPiePagina td{ padding-left : 2px; }

    td{
        font-size   : 11px;
        font-family :"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
    }

</style>

<body>
	<div style="float:left; width:100%">

        <!-- INFORMACION DE LA EMPRESA -->
        <table style="text-align:center; margin-left:auto; margin-right:auto; font-size:12px; float:left; width:100%; margin-bottom:20px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $razon_social; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
            <tr><td>Sucursal: <?php echo $sucursal; ?></td></tr>
        </table>

        <!-- INFORMACION DEL TERCERO -->
        <div style="float:left; width:48%; font-size: 11px;">
            <div style="float:left; width:100%;">
                <div style="float:left; width:25%;">TERCERO:</div>
                <div style="float:left; width:75%;"><b><?php echo $tercero; ?></b></div>
            </div>
            <div style="float:left; width:100%;">
                <div style="float:left; width:25%;">NIT:</div>
                <div style="float:left; width:75%;"><?php echo $nit_tercero; ?></div>
            </div>
        </div>

        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left; width:50%;">
                <div style="float:left; width:100%; font-size:15px;"><b><?php echo $tipo_nota; ?></b> <?php echo $consecutivo; ?></div>
                <div style="float:left; width:100%; font-size:12px;"><b>NOTA NIIF</b></div>
                <div style="float:left; width:100%; font-size:11px;">FECHA: <?php echo fecha_larga($fecha_nota); ?></div>
                <div style="float:left; width:100%; font-size:11px;">ELABORADO POR: <?php echo $usuario ?></div>
            </table>
        </div>
        <br>
	</div>
	<div class="my_informe_Contenedor_Titulo_informe">

        <!-- CUERPO DEL INFORME -->
        <table style="border-collapse: collapse; width:100%;">
        	<thead><?php echo $thead ?></thead>
			<tbody><?php echo $tbody.$tfooter ?></tbody>
		</table>
		<div style=" float:left">
			<br>
			<div style="overflow: hidden; width:100%; margin:5px 5px 20px 0px; padding:0px 7px 0px 0px; font-size:12px;">
		        <div style="border:1px solid #000; margin-top:20px; width:740px; padding:5px;">
		            <b>Observaciones: </b><?php echo $observacion; ?>
		        </div>
	    	</div>
	    </div>
    </div>

</body>

<?php
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
	else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = false; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
	else{ $MS=10; $MD=10; $MI=10; $ML=10; }

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
			10,							// margin header
			10,							// margin footer
			$ORIENTACION				// L - landscape, P - portrait
		);
		$mpdf->SetProtection(array('print'));
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA){ $mpdf->Output($documento.".pdf",'D'); }   	// OUTPUT A ARCHIVO
		else{ $mpdf->Output($documento.".pdf",'I'); }		// OUTPUT A VISTA

		exit;
	}
	else{ echo $texto; }

?>