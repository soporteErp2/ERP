<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

    if (!isset($_SESSION['EMPRESA'])) { exit; }

    if (isset($IMPRIME_XLS)) {
        if($IMPRIME_XLS=='true'){
            header('Content-type: application/vnd.ms-excel');
            header("Content-Disposition: attachment; filename=recibo_de_caja.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
        }
    }

    ob_start();

    $id_empresa     = $_SESSION['EMPRESA'];
    $nombre_empresa = mysql_result(mysql_query("SELECT nombre FROM empresas WHERE id = $id_empresa",$link),0,"nombre");

    $sqlFv   = "SELECT COUNT(F.id) AS contFactura,
                    F.sucursal,
                    F.id_cliente,
                    F.prefijo,
                    F.numero_factura,
                    F.cod_cliente,
                    F.nit,
                    F.cliente,
                    date_format(F.fecha_inicio,'%Y-%m-%d') AS fecha_ini,
                    date_format(F.fecha_vencimiento,'%Y-%m-%d') AS fecha_fin,
                    F.observacion,
                    F.estado,
                    F.nombre_vendedor,
                    F.prefijo,
                    F.cuenta_pago,
                    F.configuracion_cuenta_pago,
                    F.centro_costo,
                    F.codigo_centro_costo,
                    F.total_factura,
                    T.dv
                FROM ventas_facturas AS F LEFT JOIN terceros AS T ON(
                        T.activo=1
                        AND F.id_cliente=T.id
                    )
                WHERE F.id='$id' AND F.activo = 1";
    $queryFv = mysql_query($sqlFv,$link);

    $dv_tercero    = mysql_result($queryFv,0,'dv');
    $nit_tercero   = mysql_result($queryFv,0,'nit');
    $tercero       = mysql_result($queryFv,0,'cliente');
    $consecutivo   = mysql_result($queryFv,0,'numero_factura');
    $fecha_ini     = mysql_result($queryFv,0,'fecha_ini');
    $fecha_fin     = mysql_result($queryFv,0,'fecha_fin');
    $sucursal      = mysql_result($queryFv,0,'sucursal');
    $codigoCuenta  = mysql_result($queryFv,0,'cuenta');
    $detalleCuenta = mysql_result($queryFv,0,'descripcion_cuenta');
    $estado        = mysql_result($queryFv,0,'estado');
    $observacion   = mysql_result($queryFv,0,'observacion');
    $cuenta_pago   = mysql_result($queryFv,0,'cuenta_pago');
    $total_factura = mysql_result($queryFv,0,'total_factura');

    if($dv_tercero != ""){ $nit_tercero = $nit_tercero.'-'.$dv_tercero; }

    if($dv_tercero != ''){ $nit_tercero = $nit_tercero.'-'.$dv_tercero; }
    if($estado==0){ echo '<center><h2><i>Documento no Generado</i></h2></center>'; exit; }
    else if ($estado==3){ echo "<center><h2><i>Documento Cancelado</i></h2></center>"; exit; }

    $sqlDetalle   = "SELECT cuenta_puc, descripcion_puc, debito, credito, codigo_concepto,concepto
                    FROM ventas_facturas_cuentas
                    WHERE activo=1
                        AND id_empresa=$id_empresa
                        AND id_factura_venta=$id";
    $queryDetalles = mysql_query($sqlDetalle,$link);

    $saldoDebito  = 0;
    $saldoCredito = 0;
    $bodyTable    = '';
    while($rowDetalles = mysql_fetch_array($queryDetalles)){

        $saldoDebito   += $rowDetalles['debito'];
        $saldoCredito  += $rowDetalles['credito'];
        $terceroCuenta  = ($rowDetalles['tercero']=='')? $tercero : $rowDetalles['tercero'];

        $bodyTable.='<tr>
                        <td width="70">'.$rowDetalles['cuenta_puc'].'</td>
                        <td width="250"><b>'.$rowDetalles['descripcion_puc'].'</b></td>
                        <td width="90" style="text-align:right;">'.number_format($rowDetalles['debito'],$_SESSION['DECIMALESMONEDA']).'</td>
                        <td width="90" style="text-align:right;">'.number_format($rowDetalles['credito'],$_SESSION['DECIMALESMONEDA']).'</td>
                        <td width="80" style="text-align:right;"><b>'.$rowDetalles['codigo_concepto'].'</b></td>
                        <td width="180">'.$rowDetalles['concepto'].'</td>
                    </tr>';

        if($rowDetalles['observaciones'] != ''){
            $bodyTable .= '<tr>
                                <td width="235">&nbsp;</td>
                                <td colspan="5" style="font-size:9px;">'.str_replace("\n",'<br>',$rowDetalles['observaciones']).'</td>
                            </tr>';
        }
    }

    //650

    $saldoCuentaCruce = $saldoCredito - $saldoDebito;
    $saldoDebito      = $saldoDebito + $saldoCuentaCruce;

    $bodyTable = $bodyTable.'<tr>
                        <td width="70">'.$cuenta_pago.'</td>
                        <td width="250">&nbsp;</td>
                        <td width="90" style="text-align:right;">'.$total_factura.'</td>
                        <td width="90" style="text-align:right;">0</td>
                        <td width="80"></td>
                        <td width="180">TOTAL FACTURA</td>
                    </tr>';
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
        font-family : "Segoe UI Light","Helvetica Neue Light","Segoe UI","Helvetica Neue","Trebuchet MS",Helvetica,"Droid Sans",Tahoma,Geneva,sans-serif;
    }

</style>

<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body>
    <div style="float:left; width:100%">

        <!-- INFORMACION DE LA EMPRESA -->
        <table style="text-align:center;margin-left: auto; margin-right: auto; font-size:12px;">
            <tr><td style="font-size: 15px;font-weight: bold;"><?php echo $nombre_empresa; ?></td></tr>
            <tr><td>Nit. <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
            <tr><td>Sucursal: <?php echo $sucursal; ?></td></tr>
        </table>
        <br>

        <!-- INFORMACION DEL TERCERO -->
        <div style="float:left;width:45%;">
            <table style="font-size: 12px;">
                <tr>
                    <td>CLIENTE:</td>
                    <td><b><?php echo $tercero; ?></b></td>
                </tr>
                <tr>
                    <td>NIT:</td>
                    <td><?php echo $nit_tercero; ?></td>
                </tr>
            </table>
        </div>

        <!-- NOMBRE DEL DOCUMENTO Y CONSECUTIVO -->
        <div style="float:left;width:48%;">
            <table style="border-collapse: collapse;">
                <tr>
                    <td style="font-size:16px; font-weight:bold;">REGISTRO FACTURA DE VENTA No.</td>
                    <td style="font-size:15px;"><?php echo $consecutivo; ?></td>
                </tr>
                <tr>
                    <td>FECHA DE EMISION: <?php echo fecha_larga($fecha_ini); ?></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>FECHA DE VENCIMIENTO: <?php echo fecha_larga($fecha_fin); ?></td>
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
                <td width="70" style="color:#fff;">CUENTA</td>
                <td width="250" style="color:#fff;">DESCRIPCION</td>
                <td width="90" style="text-align:center; color:#fff;">DEBITO</td>
                <td width="90" style="text-align:center; color:#fff;">CREDITO</td>
                <td width="80" style="text-align:center; color:#fff;">CODIGO</td>
                <td width="180" style="text-align:center; color:#fff;">CONCEPTO</td>
            </tr>
            <?php echo $bodyTable; ?>
        </table>

        <div style="margin-top:25px; overflow:hidden; width:100%;">
            <div style="float:left; width:305px;"><b>TOTAL SUMAS IGUALES</b></div>
            <div style="text-align:right; float:left; width:90px;"><b><?php echo number_format($saldoDebito,$_SESSION['DECIMALESMONEDA']); ?></b></div>
            <div style="text-align:right; float:left; width:90px;"><b><?php echo number_format($saldoCredito,$_SESSION['DECIMALESMONEDA']); ?></b></div>
        </div>

        <div style="border:1px solid #000; margin-top:20px; width:740px; padding:5px;">
            <b>Observacion: </b><?php echo $observacion; ?>
        </div>

    </div>

    <!-- PIE DE PAGINA -->
    <div style="margin-top:180px; overflow:hidden; width:100%;">
        <div style="width:31%; margin:0 1%; float:left; text-align:center; border-top:1px solid; font-size:12px;"><b>Elaboro</b></div>
        <div style="width:31%; margin:0 1%; float:left; text-align:center; border-top:1px solid; font-size:12px;"><b>Aprobo</b></div>
        <div style="width:31%; margin:0 1%; float:left; text-align:center; border-top:1px solid; font-size:12px;"><b>Firma y Sello del Tercero</b></div>
    </div>
</body>

<?php

	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'true'; }

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
        // $mpdf-> debug = true;
        $mpdf->useSubstitutions = true;
        $mpdf->simpleTables = true;
        $mpdf->packTableData = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode ('fullpage');
		$mpdf->SetHeader("");
        $mpdf->SetHtmlFooter('<div style="width:96%; font-size:9px; text-align:right;">Pagina {PAGENO}/{nb}</div>');

		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }

		exit;
    }
    else{ echo $texto; }

?>
