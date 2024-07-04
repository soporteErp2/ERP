<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    $documento = 'comprobante_egreso';
    if (!isset($_SESSION['EMPRESA'])) { exit; }

    if (isset($IMPRIME_XLS)) {
        if($IMPRIME_XLS=='true'){
            header('Content-type: application/vnd.ms-excel');
            header("Content-Disposition: attachment; filename=Comprobante_de_egreso.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
        }
    }

    ob_start();

    $id_empresa     = $_SESSION['EMPRESA'];
    $nombre_empresa = mysql_result(mysql_query("SELECT nombre FROM empresas WHERE id = $id_empresa",$link),0,"nombre");

    //DEBITO CREDITO FECHA SELECCIONADA
    $sqlcomprobante   = "SELECT CE.nit_tercero,
                            CE.tercero,
                            CE.consecutivo,
                            CE.fecha_comprobante,
                            CE.sucursal,
                            CE.numero_cheque,
                            CE.cuenta,
                            CE.descripcion_cuenta,
                            CE.estado,
                            CE.observacion,
                            T.dv
                        FROM comprobante_egreso AS CE LEFT JOIN terceros AS T ON(
                                T.activo=1
                                AND T.tercero = 1
                                AND CE.id_tercero=T.id
                            )
                        WHERE CE.id=$id
                            AND CE.activo=1
                            AND CE.id_empresa=$id_empresa";
    $queryComprobante = mysql_query($sqlcomprobante,$link);


    $dv_tercero        = mysql_result($queryComprobante,0,'dv');
    $nit_tercero       = mysql_result($queryComprobante,0,'nit_tercero');
    $tercero           = mysql_result($queryComprobante,0,'tercero');
    $consecutivo       = mysql_result($queryComprobante,0,'consecutivo');
    $fecha_comprobante = mysql_result($queryComprobante,0,'fecha_comprobante');
    $sucursal          = mysql_result($queryComprobante,0,'sucursal');
    $codigoCuenta      = mysql_result($queryComprobante,0,'cuenta');
    $detalleCuenta     = mysql_result($queryComprobante,0,'descripcion_cuenta');
    $numero_cheque     = (mysql_result($queryComprobante,0,'numero_cheque')!='')? '<tr><td>No Cheque: '.mysql_result($queryComprobante,0,'numero_cheque').' </td><td>&nbsp;</td></tr>' : '' ;
    $estado            = mysql_result($queryComprobante,0,'estado');
    $observacionCE     = mysql_result($queryComprobante,0,'observacion');

    if($dv_tercero != ''){ $nit_tercero = $nit_tercero.'-'.$dv_tercero; }
    if ($estado==0) { echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }
    $marcaAgua = 'false';
    if($estado==3){
        $marcaAgua = 'true';
    }

    $sqlDetalle = "SELECT
                        cuenta,
                        descripcion,
                        tercero,
                        debito,
                        credito,
                        tipo_documento_cruce,
                        prefijo_documento_cruce,
                        numero_documento_cruce,
                        observaciones
                    FROM comprobante_egreso_cuentas
                    WHERE activo = 1
                        AND id_comprobante_egreso = $id
                    GROUP BY id";
    $queryDetalles = mysql_query($sqlDetalle,$link);

    $saldoDebito  = 0;
    $saldoCredito = 0;
    $bodega       = '';
    $bodyTable    = '';
    while($rowDetalles = mysql_fetch_array($queryDetalles)){

        $saldoDebito   += $rowDetalles['debito'];
        $saldoCredito  += $rowDetalles['credito'];
        $terceroCuenta  = ($rowDetalles['tercero']=='')? $tercero : $rowDetalles['tercero'] ;
        $documentoCruce = ($rowDetalles['prefijo_documento_cruce'] != '')? $rowDetalles['prefijo_documento_cruce'].'-'.$rowDetalles['numero_documento_cruce']: $rowDetalles['numero_documento_cruce'];

        $bodyTable .= '<tr>
                            <td width="235">'.$terceroCuenta.'</td>
                            <td width="100"><b>'.$rowDetalles['tipo_documento_cruce'].' '.$documentoCruce.'</b></td>
                            <td width="235"><b>'.$rowDetalles['cuenta'].'</b> '.$rowDetalles['descripcion'].'</td>
                            <td width="90" style="text-align:right;">'.number_format($rowDetalles['debito'],$_SESSION['DECIMALESMONEDA']).'</td>
                            <td width="90" style="text-align:right;">'.number_format($rowDetalles['credito'],$_SESSION['DECIMALESMONEDA']).'</td>
                        </tr>';

        if($rowDetalles['observaciones'] != ''){
            $bodyTable .= '<tr>
                                <td width="235">&nbsp;</td>
                                <td colspan="4" style="font-size:9px;">'.str_replace("\n",'<br>',$rowDetalles['observaciones']).'</td>
                            </tr>';
        }
    }

    $saldoCuentaCruce = $saldoDebito - $saldoCredito;
    $saldoCredito     = $saldoCredito + $saldoCuentaCruce;

    $bodyTable = '<tr>
                        <td width="235">'.$tercero.'</td>
                        <td width="100">&nbsp;</td>
                        <td width="235"><b>'.$codigoCuenta.'</b> '.$detalleCuenta.'</td>
                        <td width="90" style="text-align:right;">0</td>
                        <td width="90" style="text-align:right;">'.number_format($saldoCuentaCruce,$_SESSION['DECIMALESMONEDA']).'</td>
                    </tr>'.$bodyTable;
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
    .defaultFont{ font-size : 11px; }
    .my_informe_Contenedor_Titulo_informe td{ padding-left : 2px; }
    .tablaPiePagina td{ padding-left : 2px; }

    td{
        font-size   : 11px;
        font-family :"Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
    }
</style>

<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body >
    <div style="float:left; width:100%">

        <!-- INFORMACION DE LA EMPRESA -->
        <table  style="text-align:center;margin-left: auto; margin-right: auto; font-size:12px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $nombre_empresa; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
            <tr><td>Sucursal: <?php echo $sucursal; ?></td></tr>
        </table>
        <br>

        <!-- INFORMACION DEL TERCERO -->
        <div style="float:left;width:50%;">
            <table style="font-size: 12px;">
                <tr>
                    <td>CANCELADO A:</td>
                    <td><b><?php echo $tercero; ?></b></td>
                </tr>
                <tr>
                    <td>NIT:</td>
                    <td><?php echo $nit_tercero; ?></td>
                </tr>
            </table>
        </div>

        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left;width:40%;">
            <table>
                <tr>
                    <td style="font-size: 15px;font-weight: bold;">COMPROBANTE DE EGRESO No.</td>
                    <td style="font-size: 15px;"><?php echo $consecutivo; ?></td>
                </tr>
                <?php echo $numero_cheque; ?>
                <tr>
                    <td>FECHA: <?php echo fecha_larga($fecha_comprobante); ?></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <br>

    </div>

    <div class="my_informe_Contenedor_Titulo_informe">

        <!-- CUERPO DEL INFORME -->
        <table class="defaultFont" style="border-collapse: collapse;">
            <tr style="background-color:#000;">
                <td width="235" style="color:#fff;">TERCERO</td>
                <td width="100" style="color:#fff;">DOC. CRUCE</td>
                <td width="235" style="color:#fff;">CUENTA</td>
                <td width="90" style="text-align:center; color:#fff;">DEBITO</td>
				<td width="90" style="text-align:center; color:#fff;">CREDITO</td>
            </tr>
            <?php echo $bodyTable; ?>
        </table>

        <div style="margin-top:25px; overflow:hidden; width:100%;">
            <div style="float:left; width:560px;"><b>TOTAL SUMAS IGUALES</b></div>
            <div style="text-align:right; float:left; width:90px;"><b><?php echo number_format($saldoDebito,$_SESSION['DECIMALESMONEDA']); ?></b></div>
            <div style="text-align:right; float:left; width:90px;"><b><?php echo number_format($saldoCredito,$_SESSION['DECIMALESMONEDA']); ?></b></div>
        </div>

        <div style="border:1px solid #000; margin-top:20px; width:740px; padding:5px;">
            <b>Observaciones: </b><?php echo $observacionCE; ?>
        </div>

    </div>

    <!-- PIE DE PAGINA -->
    <div style="margin-top:150px; overflow:hidden; width:100%;">
        <div style="width:31%; margin:0 1%; float:left; text-align:center; border-top:1px solid #000; font-size:12px;"><b>Elaboro</b></div>
        <div style="width:31%; margin:0 1%; float:left; text-align:center; border-top:1px solid #000; font-size:12px;"><b>Aprobo</b></div>
        <div style="width:31%; margin:0 1%; float:left; text-align:center; border-top:1px solid #000; font-size:12px;"><b>Firma y Sello del Tercero</b></div>
    </div>
</body>

<?php

	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	// if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12 ; }

	if ($IMPRIME_PDF=='true') {

		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
					'utf-8',  		// mode - default ''
					$HOJA,			// format - A4, for example, default ''
					12,				// font size - default 0
					'',				// default font family
					$MI,			// margin_left
					$MD,			// margin right
					$MS,			// margin top
					$ML,			// margin bottom
					10,				// margin header
					10,				// margin footer
					$ORIENTACION	// L - landscape, P - portrait
				);

        $mpdf->SetProtection(array('print'));
        if($marcaAgua=='true'){
            $mpdf->SetWatermarkText('ANULADO');
            $mpdf->showWatermarkText = true;
        }
        // $mpdf-> debug = true;
        $mpdf->useSubstitutions = true;
        $mpdf->simpleTables = true;
        $mpdf->packTableData = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode ('fullpage');
		$mpdf->SetHeader("");
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');

		$mpdf->WriteHTML(utf8_encode($texto));

		if( $PDF_GUARDA=='F'){
            $serv = $_SERVER['DOCUMENT_ROOT']."/";
            $url  = $serv.'ARCHIVOS_PROPIOS/adjuntos_compras/comprobante_egreso/';
            if(!file_exists($url)){ mkdir ($url); }

            $url = $url.'empresa_'.$_SESSION['ID_HOST'].'/';
            if(!file_exists($url)){ mkdir ($url); }

            $mpdf->Output($url.$documento.'_'.$id.".pdf",'F');      ///OUTPUT A ARCHIVO

            // $mpdf->Output($documento.".pdf",'D');
        }
        else{ $mpdf->Output($documento.".pdf",'I'); }

        exit;
    }
    else{ echo $texto; }

?>