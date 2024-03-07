<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel;');
       header("Content-Disposition: attachment; filename=informe_facturas_".date("Y_m_d").".xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $desde              = $MyInformeFiltroFechaInicio;
    $hasta              = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;
    $id_empresa         = $_SESSION['EMPRESA'];
    $divTitleSucursal   = '';
    $subtitulo_cabecera = '';

    // echo$MyInformeFiltroFechaFinal=(isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;

    if ($MyInformeFiltroFechaFinal!='' && $MyInformeFiltroFechaFinal!='') {
        $whereFechas = " AND A.fecha BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";
    }
    else{
        $MyInformeFiltroFechaFinal=date("Y-m-d");

        $script = 'localStorage.MyInformeFiltroFechaInicioFacturas  = "";
                localStorage.MyInformeFiltroFechaFinalFacturas = "";
                localStorage.sucursal_facturas  = ""
                arrayTerceros_FVIR.length          = 0;
                tercerosConfigurados_FVIR.length   = 0;
                arrayVendedores_FVIR.length        = 0;
                vendedoresConfigurados_FVIR.length = 0;';
    }

    $whereIdTerceros = "";
    if ($idTerceros!='' ) {

        if ($idTerceros!='todos') {
            $arrayidTerceros=explode(",",$idTerceros);
            foreach ($arrayidTerceros as $indice => $valor) { $whereIdTerceros .= " OR A.id_tercero=$valor"; }
        }
        $whereIdTerceros = "AND (".substr($whereIdTerceros, 4).")";
    }

    if ($idVendedores!='' ) {

        $arrayIdVendedores=explode(",",$idVendedores);
        //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
        foreach ($arrayIdVendedores as $indice => $valor) {
            $whereidVendedores = ($whereidVendedores=='')? ' VF.id_vendedor='.$valor : $whereidVendedores.' OR VF.id_vendedor='.$valor ;
            $whereVendedores   = ($whereidVendedores!='')? "AND (".$whereidVendedores.")" : "" ;
        }
    }

    $whereIdCCos = "";
    if ($idCentroCostos!='' ) {

        $arrayCcos = explode(",",$idCentroCostos);
        foreach ($arrayCcos as $indice => $valor) { $whereIdCCos .= " OR A.id_centro_costo=$valor"; }
        $whereIdCCos = substr($whereIdCCos, 4);
    }

    $whereSucursal = "";
    if ($sucursal!='' && $sucursal!='global') {

        $whereSucursal = " AND A.id_sucursal=$sucursal";

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sqlSucursal   = "SELECT nombre FROM empresas_sucursales WHERE id_empresa=$id_empresa AND id=".$sucursal;
        $querySucursal = mysql_query($sqlSucursal,$link);
        $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($querySucursal,0,'nombre').'<br>';
    }

    $whereCuenta = "";
    $arrayTotal  = array();
    $arrayFila   = array();
    $arrayHead   = array();

    //====================================// IMPUESTOS //====================================//
    //***************************************************************************************//
    $sqlImpuestos =  "SELECT impuesto,valor,cuenta_venta, COUNT(cuenta_venta) AS contImpuestos
                      FROM impuestos
                      WHERE id_empresa='$id_empresa'
                      AND venta='Si'			
                      GROUP BY cuenta_venta";
    $queryImpuestos = mysql_query($sqlImpuestos,$link);
    while ($rowImpuestos = mysql_fetch_assoc($queryImpuestos)) {
        $cuenta = $rowImpuestos['cuenta_venta'];

        if($rowImpuestos["contImpuestos"] > 1){
            $rowImpuestos["impuesto"] = "Impuestos Varios";
            $rowImpuestos["valor"]    = "-";
        }

        $arrayTotal["$cuenta"] = 0;
        $arrayFila["$cuenta"]  = '<td></td>';
        $arrayHead["$cuenta"]  = array("nombre"=>strtoupper($rowImpuestos["impuesto"]), "valor"=>$rowImpuestos["valor"]);
        $whereCuenta .= " OR A.codigo_cuenta=".$rowImpuestos['cuenta_venta'];
    }

    //===================================// RETENCIONES //===================================//
    //***************************************************************************************//
    $sqlRetenciones  = "SELECT retencion, tipo_retencion, valor, base, cuenta, COUNT(cuenta) AS contRetenciones
                        FROM retenciones
                        WHERE id_empresa='$id_empresa'
                        AND modulo='Venta'
                        GROUP BY cuenta";
    $queryRetenciones = mysql_query($sqlRetenciones,$link);
    while ($rowRetenciones = mysql_fetch_assoc($queryRetenciones)) {
        $cuenta = $rowRetenciones['cuenta'];

        $sqlAsientos   = "SELECT A.id
                        FROM asientos_colgaap AS A
                        WHERE A.id_empresa='$id_empresa'
                            AND A.activo=1
                            AND A.codigo_cuenta='$cuenta'
                            AND A.tipo_documento_cruce='FV'
                            $whereIdTerceros
                            $whereSucursal
                            $whereFechas
                        LIMIT 0,1;";
        $queryAsientos = mysql_query($sqlAsientos,$link);
        $contAsiento   = mysql_result($queryAsientos, 0, 'id');

        if($contAsiento > 0){
            $rowRetenciones["valor"] = ($rowRetenciones["valor"]*1).'%';
            if($rowRetenciones["contRetenciones"] > 1){
                $rowRetenciones["retencion"] = $rowRetenciones["tipo_retencion"];
                $rowRetenciones["valor"]     = "-";
            }

            $arrayTotal["$cuenta"] = 0;
            $arrayFila["$cuenta"]  = '<td></td>';
            $arrayHead["$cuenta"]  = array("nombre"=>strtoupper($rowRetenciones["retencion"]), "valor"=>$rowRetenciones["valor"]);
            $whereCuenta .= " OR A.codigo_cuenta=".$rowRetenciones['cuenta'];
        }
    }

    $whereCuenta = substr($whereCuenta, 3);
    if($whereCuenta != ""){ $whereCuenta = "AND($whereCuenta)"; }

    //=====================================// CUENTAS //=====================================//
    //***************************************************************************************//
    $sql  = "SELECT A.id_documento_cruce,
                    A.codigo_cuenta AS cuenta,
                    A.consecutivo_documento,
                    A.fecha,
                    SUM(A.debe - A.haber) AS saldo,
                    V.sucursal,
                    V.nit,
                    V.cliente,
                    V.fecha_inicio AS fecha,
                    V.total_factura,
                    V.numero_factura_completo AS factura
                FROM asientos_colgaap AS A LEFT JOIN ventas_facturas AS V ON(
                    V.id=A.id_documento_cruce
                    )
                WHERE A.tipo_documento_cruce = 'FV'
                    AND A.id_empresa = '$id_empresa'
                    AND A.activo = 1
                    AND V.estado = 1
                    $whereCuenta
                    $whereIdTerceros
                    $whereSucursal
                    $whereFechas
                GROUP BY A.id_documento_cruce, A.codigo_cuenta
                ORDER BY A.id_documento_cruce DESC";

    $query = mysql_query($sql,$link);

    $acumuladoSubtotal = 0;
    $acumuladoIva      = 0;
    $acumuladoTotal    = 0;

    $idFila    = 0;
    $acumFila  = '';
    $arrayAcum = $arrayFila;
    $acumBody  = '';

    // print_r($arrayFila);
    $totalFacturas = 0;
    while ($row = mysql_fetch_assoc($query)) {
        $cuenta = $row['cuenta'];
        $saldo  = $row['saldo'] * 1;
        $valorFactura = $row['total_factura'] * 1;

        if($idFila!=$row['id_documento_cruce']){
            if($idFila > 0){
                $acumBody .= '<tr>'.$acumFila.implode("", $arrayAcum).'</tr>';
                $arrayAcum = $arrayFila;
            }

            $totalFacturas += $valorFactura;
            $idFila = $row['id_documento_cruce'];
        }



        $acumFila = '<td>'.$row['sucursal'].'</td>
                    <td>'.$row['nit'].'</td>
                    <td style="width:200px;">'.$row['cliente'].'</td>
                    <td>'.$row['factura'].'</td>
                    <td>'.$row['fecha'].'</td>
                    <td style="text-align:right;">'.$valorFactura.'</td>';


        $arrayTotal["$cuenta"] += $saldo;

        if($saldo < 0){ $saldo = '('.ABS($saldo).')'; }
        $arrayAcum["$cuenta"] = '<td style="text-align:right;">'.$saldo.'</td>';
    }
    if($idFila > 0){
        $acumBody .= '<tr>'.$acumFila.implode("", $arrayAcum).'</tr>';
    }

    $anchoFila = 540;
    $tableBody = '<!-- <div style="width:100%; height:calc(100% - 170px) !important; overflow-x:auto;"> -->
                    <table id="tabla_impuestos_retenciones">
                        <tr>
                            <td>SUCURSAL</td>
                            <td>NIT</td>
                            <td style="width:200px;">CLIENTE</td>
                            <td style="width:80px;">FACTURA</td>
                            <td style="width:80px;">FECHA</td>
                            <td style="width:80px;">TOTAL FACTURA</td>';

    //CICLO PARA LOS REGISTROS
    foreach($arrayHead AS $cuenta=>$arrayTitle){
        $anchoFila += 100;
        $tableBody .= '<td>'.$arrayTitle['nombre'].'<br/>'.$arrayTitle['valor'].'</td>';
    }

    $tableBody .= '     </tr>
                        '.$acumBody.'
                        <tr class="filaTotal">
                            <td colspan="4">TOTAL IMPUESTOS Y RETENCIONES</td>
                            <td>'.$totalFacturas.'</td>';

    //CICLO PARA EL TOTAL
    foreach ($arrayTotal as $cuenta => $total) {
        if($total < 0){ $total = '('.ABS($total).')'; }
        $tableBody .= '<td>'.$total.'</td>';
    }

     $tableBody .=' </table>
                <!-- </div> -->';
?>
<style>
    #tabla_impuestos_retenciones .filaTotal td{ text-align : right; }
    #tabla_impuestos_retenciones .filaTotal{ border-top : 1px solid; }
    #tabla_impuestos_retenciones{ border-collapse: collapse; width: <?php echo $anchoFila; ?>px; }

	.formato_impuestos_retenciones{
        float       : left;
        width       : 100%;
        margin      : 0;
        font-size   : 11px;
        font-family : Verdana, Geneva, sans-serif;
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
        float       : left;
        width       : 100%;
        font-size   : 16px;
        font-weight : bold;
	}

    .defaultFont{ font-size : 11px; }
    .labelResult{ font-weight:bold; font-size: 14px; }
    .labelResult2{ font-weight:bold; font-size: 12px; width: 20%; }
    .labelResult3{ font-weight:bold; font-size: 12px; text-align: right; }

    .titulos{
        background   : #999;
        padding-left : 10px;
    }

    .titulos td{
        height : 35px;
        color  : #FFF;
    }

    .total{
        background  : #EEE;
        font-weight : bold;
    }

    .total td{
        border-top    : 1px solid #999;
        border-bottom : 1px solid #999;
        background    : #EEE;
        padding-left  : 10px;
        height        : 25px;
        font-weight   : bold;
    }

    #tabla_impuestos_retenciones td{
        font-size      : 10px;
        vertical-align : top;
        padding        : 5px;
        text-align     : center;
    }
</style>


<!-- ------------------------------ DESARROLLO DEL INFORME -------------------------------------- -->
<!-- ******************************************************************************************** -->

<body>
    <div id="formato_impuestos_retenciones" class="formato_impuestos_retenciones" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left; width:100%; text-align:center;">
                <table align="center" style="text-align:center;" >
                    <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr><td  style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="font-size:13px;"><b>Informe Impuestos y Retenciones</b><br> <?php echo $subtitulo_cabecera; ?><br>&nbsp;</td></tr>
                    <?php echo $datos_informe; ?>
                </table>

                <?php echo $tableBody; ?>
            </div>
        </div>
    </div>
    <br>
    <?php echo '<script>'.$script.'</script>'; ?>
</body>
<?php
    $footer = '<div style="text-align:right;font-weight:bold;font-size:12px;">Pagina {PAGENO}/{nb}</div>';
    $texto  = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER-L'; }

    // if(!isset($ORIENTACION)){ $ORIENTACION = 'L'; }
	$ORIENTACION = 'p';
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12 ; }
	if($IMPRIME_PDF == 'true'){
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
					'L'	// L - landscape, P - portrait
				);
        // $mpdf-> debug = true;
        $mpdf->useSubstitutions = true;
        $mpdf->simpleTables = true;
        $mpdf->packTableData = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetHtmlFooter($footer);
        // $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }

		exit;
	}
    else{ echo $texto; }
?>