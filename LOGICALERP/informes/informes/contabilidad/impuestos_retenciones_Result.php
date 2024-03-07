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

    $whereFechas        = "";
    $id_empresa         = $_SESSION['EMPRESA'];
    $divTitleFecha      = "";
    $subtitulo_cabecera = "";


    $desde = $fechaInicio_CIR;
    $hasta = (isset($fechaFinal_CIR))? $fechaFinal_CIR : date("Y-m-d");

    if ($fechaFinal_CIR!='' && $fechaFinal_CIR!='') {
        $divTitleFecha = "$fechaInicio_CIR - $fechaFinal_CIR";
        $whereFechas = " AND fecha BETWEEN '".$fechaInicio_CIR."' AND '".$fechaFinal_CIR."'";
    }
    else{
        $script = 'localStorage.fechaInicio_CIR     = "";
                    localStorage.fechaFinal_CIR     = "";
                    localStorage.sucursal_CIR       = ""
                    arrayTerceros_CIR.length        = 0;
                    tercerosConfigurados_CIR.length = 0;';
    }

    // WHERE TERCEROS
    $whereIdTerceros = "";
    if ($idTerceros!='' ) {

        if ($idTerceros!='todos') {
            $arrayidTerceros=explode(",",$idTerceros);
            foreach ($arrayidTerceros as $indice => $valor) { $whereIdTerceros .= " OR id_tercero=$valor"; }
        }
        $whereIdTerceros = "AND (".substr($whereIdTerceros, 4).")";
    }

    // WHERE SUCURSAL
    $whereSucursal = "";
    if ($sucursal!='' && $sucursal!='global') {

        $whereSucursal = " AND id_sucursal=$sucursal";

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sqlSucursal   = "SELECT nombre FROM empresas_sucursales WHERE id_empresa=$id_empresa AND id=".$sucursal;
        $querySucursal = mysql_query($sqlSucursal,$link);
        $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($querySucursal,0,'nombre');
    }

    $colColor    = "#FFF";
    $whereCuenta = "";
    $arrayTotal  = array();
    $arrayFila   = array();
    $arrayHead   = array();


    //GROUP BY
    $groupBy  = "codigo_cuenta";
    $groupBy .= ($agrupar_terceros == 'true')? ",id_tercero": ",id_documento";
    // $groupBy .= ($agrupar_tipo_documento == 'true')? ",tipo_documento": "";
    // $groupBy .= ($agrupar_terceros <> 'true' && $agrupar_tipo_documento <> 'true')? ",id_documento": "";

    //ORDER BY
    $orderBy = ($agrupar_terceros == 'true')? "id_tercero DESC": "fecha DESC, id_documento DESC, tipo_documento ASC";
    // $orderBy .= ($agrupar_tipo_documento == 'true')? ",tipo_documento": "";

    //===================// WHERE DOCUMENTO //===================//
    $whereDocumento = "";
    if($documentos != ''){
        $documentos    = substr($documentos, 0, -1);
        $arrayDocumento = explode(",", $documentos);

        foreach ($arrayDocumento as $documento) { $whereDocumento .= "tipo_documento='$documento' OR "; }
        $whereDocumento = "AND (".substr($whereDocumento, 0, -3).")";
    }

    //===================// WHERE IMPUESTO //===================//
    $whereRetencion = "";
    $whereImpuesto  = "";
    if($impuestos != ''){
        $impuestos    = substr($impuestos, 0, -1);
        $arrayImpuesto = explode(",", $impuestos);

        foreach ($arrayImpuesto as $impuesto) {

            list($tipo,$id) = explode("_", $impuesto);

            if($tipo == "R"){ $whereRetencion .= "id=$id OR "; }
            else if($tipo == "I"){ $whereImpuesto .= "id=$id OR "; }
            else { continue; }
        }

        if($whereRetencion != "") { $whereRetencion = "AND (".substr($whereRetencion, 0, -3).")"; }
        else { $whereRetencion = "false"; }

        if($whereImpuesto != "") { $whereImpuesto  = "AND (".substr($whereImpuesto, 0, -3).")"; }
        else  { $whereImpuesto = "false"; }

        if($whereRetencion == "false" &&  $whereImpuesto == "false"){ echo "SELECCIONE UN IMPUESTO O RETENCION PARA GENERAR EL INFORME"; exit; }
    }

    if($whereImpuesto != "false"){

        //====================================// IMPUESTOS VENTA //====================================//
        //*********************************************************************************************//
        $sqlImpuestos   = "SELECT impuesto,valor,cuenta_venta AS cuenta,cuenta_venta_devolucion AS cuenta_devolucion, COUNT(cuenta_venta) AS contImpuestos
                            FROM impuestos
                            WHERE id_empresa='$id_empresa'
                                AND activo=1
                                AND venta='Si'
                                AND cuenta_venta>0
                                $whereImpuesto
                            GROUP BY cuenta_venta";
        $queryImpuestos = mysql_query($sqlImpuestos,$link);
        while ($rowImpuestos = mysql_fetch_assoc($queryImpuestos)) {
            $cuenta            = $rowImpuestos['cuenta'];
            $cuenta_devolucion = $rowImpuestos['cuenta_devolucion'];

            //VALIDACION QUE SE HA MOVIDO LA CUENTA EN LOS ASIENTOS
            $sqlAsientos   = "SELECT id
                            FROM asientos_colgaap
                            WHERE id_empresa='$id_empresa'
                                AND activo=1
                                AND (codigo_cuenta='$cuenta' OR codigo_cuenta='$cuenta_devolucion')
                                $whereIdTerceros
                                $whereSucursal
                                $whereFechas
                            LIMIT 0,1;";
            $queryAsientos = mysql_query($sqlAsientos,$link);
            $contAsiento   = mysql_result($queryAsientos, 0, 'id');
            if($contAsiento == 0){ continue; }

            //ASIENTOS
            $colColor = ($colColor == "#FFF")? "#F1F1F1": "#FFF";

            if($rowImpuestos["contImpuestos"] > 1){
                $rowImpuestos["impuesto"] = "Impuestos Varios";
                $rowImpuestos["valor"]    = "-";
            }

            $arrayTotal["$cuenta"] = array('valor'=>0, 'base'=>0);
            $arrayFila["$cuenta"]  = '<td style="background-color:'.$colColor.';"></td><td style="background-color:'.$colColor.';"></td>';
            $arrayHead["$cuenta"]  = array("nombre"=>strtoupper($rowImpuestos["impuesto"]), "valor"=>$rowImpuestos["valor"], "color"=>$colColor);
            $whereCuenta .= " OR codigo_cuenta=".$rowImpuestos['cuenta'];

            $arrayTotal["$cuenta_devolucion"] = array('valor'=>0, 'base'=>0);
            $arrayFila["$cuenta_devolucion"]  = '<td style="background-color:'.$colColor.';"></td><td style="background-color:'.$colColor.';"></td>';
            $arrayHead["$cuenta_devolucion"]  = array("nombre"=>strtoupper($rowImpuestos["impuesto"]), "valor"=>$rowImpuestos["valor"], "color"=>$colColor);
            $whereCuenta .= " OR codigo_cuenta=".$cuenta_devolucion;
        }


        //====================================// IMPUESTOS COMPRA //===================================//
        //*********************************************************************************************//
        $sqlImpuestos   = "SELECT impuesto,valor,cuenta_compra AS cuenta,cuenta_compra_devolucion AS cuenta_devolucion, COUNT(cuenta_compra) AS contImpuestos
                            FROM impuestos
                            WHERE id_empresa='$id_empresa'
                                AND activo=1
                                AND compra='Si'
                                AND cuenta_compra>0
                                $whereImpuesto
                            GROUP BY cuenta_compra";
        $queryImpuestos = mysql_query($sqlImpuestos,$link);
        while ($rowImpuestos = mysql_fetch_assoc($queryImpuestos)) {
            $cuenta            = $rowImpuestos['cuenta'];
            $cuenta_devolucion = $rowImpuestos['cuenta_devolucion'];

            //VALIDACION QUE SE HA MOVIDO LA CUENTA EN LOS ASIENTOS
            $sqlAsientos   = "SELECT id
                            FROM asientos_colgaap
                            WHERE id_empresa='$id_empresa'
                                AND activo=1
                                AND (codigo_cuenta='$cuenta' OR codigo_cuenta='cuenta_devolucion')
                                $whereIdTerceros
                                $whereSucursal
                                $whereFechas
                            LIMIT 0,1;";
            $queryAsientos = mysql_query($sqlAsientos,$link);
            $contAsiento   = mysql_result($queryAsientos, 0, 'id');
            if($contAsiento == 0){ continue; }

            //ASIENTOS
            $colColor = ($colColor == "#FFF")? "#F1F1F1": "#FFF";

            if($rowImpuestos["contImpuestos"] > 1){
                $rowImpuestos["impuesto"] = "Impuestos Varios";
                $rowImpuestos["valor"]    = "-";
            }

            $arrayTotal["$cuenta"] = array('valor'=>0, 'base'=>0);
            $arrayFila["$cuenta"]  = '<td style="background-color:'.$colColor.';"></td><td style="background-color:'.$colColor.';"></td>';
            $arrayHead["$cuenta"]  = array("nombre"=>strtoupper($rowImpuestos["impuesto"]), "valor"=>$rowImpuestos["valor"], "color"=>$colColor);
            $whereCuenta .= " OR codigo_cuenta=".$rowImpuestos['cuenta'];

            $arrayTotal["$cuenta_devolucion"] = array('valor'=>0, 'base'=>0);
            $arrayFila["$cuenta_devolucion"]  = '<td style="background-color:'.$colColor.';"></td><td style="background-color:'.$colColor.';"></td>';
            $arrayHead["$cuenta_devolucion"]  = array("nombre"=>strtoupper($rowImpuestos["impuesto"]), "valor"=>$rowImpuestos["valor"], "color"=>$colColor);
            $whereCuenta .= " OR codigo_cuenta=".$cuenta_devolucion;
        }
    }

    //===================================// RETENCIONES //===================================//
    //***************************************************************************************//
    if($whereRetencion != "false"){
        $sqlRetenciones   = "SELECT retencion, tipo_retencion, valor, base, cuenta, COUNT(cuenta) AS contRetenciones
                                FROM retenciones
                                WHERE id_empresa='$id_empresa'
                                    AND activo=1
                                    $whereRetencion
                                GROUP BY cuenta";
        $queryRetenciones = mysql_query($sqlRetenciones,$link);
        while ($rowRetenciones = mysql_fetch_assoc($queryRetenciones)) {
            $cuenta = $rowRetenciones['cuenta'];

            // //VALIDACION QUE SE HA MOVIDO LA CUENTA EN LOS ASIENTOS
            $sqlAsientos   = "SELECT id
                            FROM asientos_colgaap
                            WHERE id_empresa='$id_empresa'
                                AND activo=1
                                AND codigo_cuenta='$cuenta'
                                $whereIdTerceros
                                $whereSucursal
                                $whereFechas
                            LIMIT 0,1;";
            $queryAsientos = mysql_query($sqlAsientos,$link);
            $contAsiento   = mysql_result($queryAsientos, 0, 'id');
            if($contAsiento == 0){ continue; }

            //ASIENTOS
            $colColor = ($colColor == "#FFF")? "#F1F1F1": "#FFF";

            $rowRetenciones["valor"] = ($rowRetenciones["valor"]*1).'%';
            if($rowRetenciones["contRetenciones"] > 1){
                $rowRetenciones["retencion"] = $rowRetenciones["tipo_retencion"];
                $rowRetenciones["valor"]     = "-";
            }

            $arrayTotal["$cuenta"] = array('valor'=>0, 'base'=>0);
            $arrayFila["$cuenta"]  = '<td style="background-color:'.$colColor.';"></td><td style="background-color:'.$colColor.';"></td>';
            $arrayHead["$cuenta"]  = array("nombre"=>strtoupper($rowRetenciones["retencion"]), "valor"=>$rowRetenciones["valor"], "color"=>$colColor);
            $whereCuenta .= " OR codigo_cuenta=".$rowRetenciones['cuenta'];
        }
    }

    $whereCuenta = substr($whereCuenta, 3);
    if($whereCuenta != ""){ $whereCuenta = "AND($whereCuenta)"; }
    else { $whereCuenta = "AND id=0"; }

    //=====================================// CUENTAS //=====================================//
    //***************************************************************************************//
    $sql   = "SELECT id_tercero,
                    id_documento_cruce,
                    codigo_cuenta AS cuenta,
                    consecutivo_documento,
                    fecha,
                    SUM(debe - haber) AS saldo,
                    sucursal,
                    nit_tercero,
                    tercero,
                    IF('$agrupar_terceros'<>'true', tipo_documento,'') AS tipo,
                    IF('$agrupar_terceros'='true' OR '$agrupar_tipo_documento'='true','', consecutivo_documento) AS consecutivo
                FROM asientos_colgaap
                WHERE id_empresa='$id_empresa'
                    AND (debe <> 0 OR haber <> 0)
                    AND activo=1
                    $whereCuenta
                    $whereIdTerceros
                    $whereSucursal
                    $whereFechas
                    $whereDocumento
                GROUP BY $groupBy
                ORDER BY $orderBy";

    $query = mysql_query($sql,$link);

    $acumuladoSubtotal = 0;
    $acumuladoIva      = 0;
    $acumuladoTotal    = 0;

    $idFila    = 0;
    $acumFila  = '';
    $arrayAcum = $arrayFila;
    $acumBody  = '';

    while ($row = mysql_fetch_assoc($query)) {
        $base   = "";
        $cuenta = $row['cuenta'];
        $saldo  = $row['saldo'] * 1;
        $color  = $arrayHead["$cuenta"]['color'];

        if($agrupar_terceros != 'true' && $idFila!=$row['id_documento_cruce'] || $agrupar_terceros == 'true' && $idFila!=$row['id_tercero']){
            if($idFila > 0){
                $acumBody .= '<tr>'.$acumFila.implode("", $arrayAcum).'</tr>';
                $arrayAcum = $arrayFila;
            }

            $idFila = ($agrupar_terceros == 'true')? $row['id_tercero']: $row['id_documento_cruce'];
        }

        $acumFila = '<td>'.$row['sucursal'].'</td>
                    <td>'.$row['nit_tercero'].'</td>
                    <td style="width:200px;">'.$row['tercero'].'</td>
                    <td style="text-align:right;">'.$row['tipo'].'</td>
                    <td>'.$row['consecutivo'].'</td>
                    <td>'.$row['fecha'].'</td>';


        if($saldo > 0 || $saldo < 0){

            $base = ROUND($saldo * 100/ $arrayHead["$cuenta"]['valor'],0);
            $arrayTotal["$cuenta"]["base"] += $base;

            $base = ABS($saldo * 100/ $arrayHead["$cuenta"]['valor']);

        }
        $arrayTotal["$cuenta"]["valor"] += $saldo;

        if($saldo < 0){ $saldo = '('.ABS($saldo).')'; }


        $arrayAcum["$cuenta"] = '<td style="text-align:right; background-color:'.$color.';">'.round($base,$_SESSION['DECIMALESMONEDA']).'</td><td style="text-align:right; background-color:'.$color.';">'.$saldo.'</td>';
    }

    if($idFila > 0){
        $acumBody .= '<tr>'.$acumFila.implode("", $arrayAcum).'</tr>';
    }

    $anchoFila = 540;
    $tableBody = '<div style="width:100%; height:calc(100% - 170px) !important; overflow-x:auto;">
                    <table id="tabla_impuestos_retenciones" class="thead">
                        <tr class="thead">
                            <td rowspan="2">SUCURSAL</td>
                            <td rowspan="2">NIT</td>
                            <td rowspan="2" style="width:200px;">TERCERO</td>
                            <td rowspan="2" style="width:80px;">TIPO DOCUMENTO</td>
                            <td rowspan="2" style="width:80px;">CONSECUTIVO</td>
                            <td rowspan="2" style="width:80px;">FECHA</td>';

    //CICLO TITULOS DE LA TABLA
    $tableHead1 = "";
    $tableHead2 = "";
    foreach($arrayHead AS $cuenta=>$arrayTitle){
        $anchoFila += 100;
        $tableHead1 .= '<td colspan="2" style="background-color:'.$arrayTitle['color'].';">'.$cuenta.'<br/>'.$arrayTitle['nombre'].'</td>';
        $tableHead2 .= '<td style="background-color:'.$arrayTitle['color'].';">BASE</td><td style="background-color:'.$arrayTitle['color'].';">'.$arrayTitle['valor'].'</td>';
    }

    //CUERPO DE LA TABLA
    $tableBody .= $tableHead1.'</tr><tr class="thead">'.$tableHead2.'</tr>';
    $tableBody .=       $acumBody.'
                        <tr class="filaTotal">
                            <td colspan="6">TOTAL IMPUESTOS Y RETENCIONES</td>';

    //CICLO PARA EL TOTAL
    foreach ($arrayTotal as $cuenta => $arrayAcum) {
        $valor = $arrayAcum['valor'];
        $base  = $arrayAcum['base'];
        $color = $arrayHead["$cuenta"]["color"];

        if($valor < 0){ $valor = '('.ABS($valor).')'; }
        if($base < 0){ $base = '('.ABS($base).')'; }

        $tableBody .= '<td style="background-color:'.$color.';">'.round($base,$_SESSION['DECIMALESMONEDA']).'</td><td style="background-color:'.$color.';">'.$valor.'</td>';
    }

    $tableBody .=' </table>
                </div>';
?>
<style>
    #tabla_impuestos_retenciones .thead td{ border:1px solid #000; }
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
                    <tr><td style="font-size:13px;"><b>Informe Impuestos y Retenciones</b><br> <?php echo $subtitulo_cabecera; ?><br><?php echo $divTitleFecha; ?></td></tr>
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
        $mpdf->SetProtection(array('print'));
        $mpdf->useSubstitutions = true;
        $mpdf->simpleTables = true;
        $mpdf->packTableData = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
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