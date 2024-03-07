<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=libro_auxiliar_".date("Y_m_d").".xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }
    $arraytercerosJSON = json_decode($arraytercerosJSON);
    $arrayCentroCostosJSON = json_decode($arrayCentroCostosJSON);

    $id_empresa         = $_SESSION['EMPRESA'];
    $desde              = $MyInformeFiltroFechaInicio;
    $hasta              = $MyInformeFiltroFechaFinal;
    $id_sucursal        = $MyInformeFiltro_0;
    $whereSucursal      = '';
    $subtitulo_cabecera = '';
    $bodyTable          = '';


    $whereRangoCuentas = "";
    if($cuentaInicial!='' && $cuentaFinal!=''){
        $whereRangoCuentas = "AND (CAST(codigo_cuenta AS CHAR) BETWEEN '$cuentaInicial' AND '$cuentaFinal' OR codigo_cuenta LIKE '$cuentaInicial%' OR codigo_cuenta LIKE '$cuentaFinal%')";
    }

    if (!isset($MyInformeFiltroFechaInicio) && !isset($MyInformeFiltroFechaFinal)) {
        $MyInformeFiltroFechaInicio = date('Y').'-01-01';
        $MyInformeFiltroFechaFinal  = date('Y-m-d');

        $script  = 'localStorage.cuenta_inicialLA = "";
                    localStorage.cuenta_finalLA                          = "";
                    localStorage.MyInformeFiltroFechaFinalLibroAuxiliar  = "";
                    localStorage.MyInformeFiltroFechaInicioLibroAuxiliar = "";
                    localStorage.sucursal_libro_auxiliar                 = "";
                    arraytercerosLA.length                               = 0;
                    tercerosConfiguradosLA.length                        = 0;';
    }
    $whereClientes = '';
    $id_tercero = '';
    if (!empty($arraytercerosJSON)) {
        foreach ($arraytercerosJSON as $indice => $id_tercero) {
            $whereClientes .= ($whereClientes=='')? ' id_tercero='.$id_tercero : ' OR id_tercero='.$id_tercero;
        }
        $whereClientes   = " AND (".$whereClientes.")";
    }

    if (!empty($arrayCentroCostosJSON)) {
        foreach ($arrayCentroCostosJSON as $indice => $id_centro_costos) {
            $whereCcos .= ($whereCcos=='')? ' id_centro_costos='.$id_centro_costos : $whereCcos.' OR id_centro_costos='.$id_centro_costos;
        }
        $whereCcos   = " AND (".$whereCcos.")";
    }

    if ($sucursal!='' && $sucursal!='global') {

        $whereSucursal = ' AND id_sucursal='.$sucursal;

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
        $query = mysql_query($sql,$link);
        $subtitulo_cabecera .= '<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
    }

    //SALDOS
    $acumuladoDebe          = 0;
    $acumuladoHaber         = 0;
    $acumuladoSaldoAnterior = 0;

    $fec1 = $desde;
    $fec2 = $hasta;

    $split1 = explode('-', $desde);
    $split2 = explode('-', $hasta);

    $year1 = $split1[0];
    $year2 = $split2[0];
    $mes1  = $split1[1];
    $mes2  = $split2[1];
    $dia1  = $split1[2];
    $dia2  = $split2[2];

	$nombre_empresa	 = $_SESSION['NOMBREEMPRESA'];

    $order_by =($order_by<>'')? $order_by : "fecha DESC";

    //DEBITO CREDITO FECHA SELECCIONADA
    $sqlAsientos = "SELECT
                        SUM(debe) AS debe,
                        SUM(haber) AS haber,
                        SUM(debe-haber) AS saldo,
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        id_documento,
                        consecutivo_documento,
                        tipo_documento,
                        tipo_documento_cruce,
                        numero_documento_cruce,
                        fecha,
                        sucursal,
                        codigo_centro_costos,
                        centro_costos,
                        observacion
                    FROM asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa = '$id_empresa'
                            $whereSucursal
                            $whereClientes
                            $whereCcos
                            $whereRangoCuentas
                        AND fecha BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'
                    GROUP BY
                        codigo_cuenta,
                        id_documento,
                        tipo_documento,
                        id_documento_cruce,
                        id_tercero,
                        id
                        $mostrar_observacion

                    ORDER BY
                        $order_by,
                        CAST(codigo_cuenta AS CHAR) ASC,
                        tipo_documento,
                        consecutivo_documento ASC";

    $queryAsientos = mysql_query($sqlAsientos, $link);

    while ($rowAsientos = mysql_fetch_array($queryAsientos)){
        //se cambia id_tercero por nit_tercero debido a que los hoteles manejan mucho terceros con id cero (0).
        $id_tercero         = $rowAsientos['nit_tercero'];
        $saldo              = $rowAsientos['saldo'] * 1;
        $cuenta             = $rowAsientos['codigo_cuenta'];
        $descripcion_cuenta = $rowAsientos['cuenta'];

        //SALDO
        $acumuladoDebe  += $rowAsientos['debe'];
        $acumuladoHaber += $rowAsientos['haber'];

        $campo_agrupacion = ($totalizado=='cuentas')? $cuenta : $id_tercero ;
        $arrayAsiento[$campo_agrupacion][] = array(
                                                'cuenta'                 => $cuenta,
                                                'debe'                   => $rowAsientos['debe'],
                                                'haber'                  => $rowAsientos['haber'],
                                                'consecutivo_documento'  => $rowAsientos['consecutivo_documento'],
                                                'fecha'                  => $rowAsientos['fecha'],
                                                'nit_tercero'            => $rowAsientos['nit_tercero'],
                                                'tercero'                => $rowAsientos['tercero'],
                                                'tipo_documento'         => $rowAsientos['tipo_documento'],
                                                'descripcion_cuenta'     => $rowAsientos['cuenta'],
                                                'tipo_documento_cruce'   => $rowAsientos['tipo_documento_cruce'],
                                                'numero_documento_cruce' => $rowAsientos['numero_documento_cruce'],
                                                'sucursal'               => $rowAsientos['sucursal'],
                                                'codigo_centro_costos'   => $rowAsientos['codigo_centro_costos'],
                                                'centro_costos'          => $rowAsientos['centro_costos'],
                                                'observacion'            => ($mostrar_observacion<>'')? $rowAsientos['observacion'] : ''
                                            );

        $arrayAgrupacionDescripcion[$campo_agrupacion] = ($totalizado=='cuentas')? '<b>CUENTA:</b> '.$cuenta.'&nbsp;&nbsp; '.$descripcion_cuenta :
                                                            '<b>TERCERO:</b> '.$rowAsientos['nit_tercero'].'&nbsp;&nbsp; '.$rowAsientos['tercero'] ;

        $arraySaldoActual[$campo_agrupacion] += $saldo;

    }

    $sqlAsientosAnterior = "SELECT SUM(debe-haber) AS saldo,codigo_cuenta,id_tercero,nit_tercero,tercero,consecutivo_documento,tipo_documento,fecha
                                FROM asientos_colgaap
                            WHERE activo=1
                                AND id_empresa = '$id_empresa'
                                    $whereSucursal
                                    $whereClientes
                                    $whereCcos
                                    $whereRangoCuentas
                                AND fecha < '$MyInformeFiltroFechaInicio'
                            GROUP BY codigo_cuenta,id_tercero
                            ORDER BY
                                CAST(codigo_cuenta AS CHAR),
                                tipo_documento,
                                consecutivo_documento ASC";
    $queryAsientosAnterior = mysql_query($sqlAsientosAnterior,$link);
    while($rowAsientosAnterior = mysql_fetch_array($queryAsientosAnterior)){
        $saldo      = $rowAsientosAnterior['saldo'] * 1;
        //se cambia id_tercero por nit_tercero debido a que los hoteles manejan mucho terceros con id cero (0).
        $id_tercero = $rowAsientosAnterior['nit_tercero'];
        $cuenta     = $rowAsientosAnterior['codigo_cuenta'];

        //SALDO
        $acumuladoSaldoAnterior += $rowAsientosAnterior['saldo'];

        $campo_agrupacion = ($totalizado=='cuentas')? $cuenta : $id_tercero ;
        $arraySaldoAnterior[$campo_agrupacion] += $saldo;
        $arraySaldoActual[$campo_agrupacion]   += $saldo;
    }

    $acumuladoDebeCuenta  = 0;
    $acumuladoHaberCuenta = 0;
    $bodyTable            = '';
    $cuenta_head          = '';
    $subtotal_cuenta      = '';

    foreach ($arrayAsiento as $campo_agrupacion => $arrayAsientoArray) {
        if ($cuenta_head != $campo_agrupacion) {
            // SUBTOTALES EN LAS CUENTAS
            if ($cuenta_head != "") {
                $subtotal_cuenta = '<tr class="titulos_totales_empleados">
                                        <td colspan="9">SUBTOTALES</td>
                                        <td style="text-align:right;">'.$acumuladoDebeCuenta.'</td>
                                        <td style="text-align:right;">'.$acumuladoHaberCuenta.'</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </table>';
                $acumuladoDebeCuenta  = 0;
                $acumuladoHaberCuenta = 0;
            }

            $cuenta_head = $campo_agrupacion;
        }

        $campoAdd     = ($IMPRIME_XLS=='true')? '<td><b>NIT EMPRESA</b></td>' : '';
        $campoAddFila = ($IMPRIME_XLS=='true')? '<td>'.$_SESSION['NITEMPRESA'].'</td>' : '';


        $bodyTable .= $subtotal_cuenta.
                        '<table class="table" style="border-collapse: collapse;" >
                        <tr class="empleado_nomina">
                            <td colspan="9">'.$arrayAgrupacionDescripcion[$campo_agrupacion].' </td>
                            <td colspan="2"><b>SALDO ANTERIOR:</b> '.$arraySaldoAnterior[$campo_agrupacion].' </td>
                            <td colspan="2"><b>SALDO ACTUAL:</b> '.$arraySaldoActual[$campo_agrupacion].' </td>
                        </tr>
                        <tr class="titulos_conceptos_empleados">
                            <td><b>CUENTA</b></td>
                            <td style="width:80px;" ><b>FECHA</b></td>
                            <td style="width:30px;"><b>DOC</b></td>
                            <td><b>N. DOC</b></td>
                            <td><b>DOC CRUCE</b></td>
                            <td><b>N. DOC CRUCE</b></td>
                            <td><b>SUCURSAL</b></td>
                            <td><b>NIT</b></td>
                            <td><b>TERCERO </b></td>
                            <td style="text-align:right;width:80px;"><b>DEBITO</b></td>
                            <td style="text-align:right;width:80px;"><b>CREDITO</b></td>
                            <td style="text-align:right;width:80px;"><b>CODIGO CENTRO COSTOS</b></td>
                            <td style="text-align:right;width:80px;"><b>CENTRO COSTOS</b></td>
                            '.$campoAdd.'
                        </tr>';

        foreach ($arrayAsientoArray as $key => $arrayAsientoResul) {
            $style = ($style != '')? '' : 'style="background:#f7f7f7;"';
            $bodyTable .=  '<tr class="filaConcepto " '.$style.'>
                                <td>'.$arrayAsientoResul['cuenta'].'</td>
                                <td>'.$arrayAsientoResul['fecha'].'</td>
                                <td>'.$arrayAsientoResul['tipo_documento'].'</td>
                                <td>'.$arrayAsientoResul['consecutivo_documento'].'</td>

                                <td>'.$arrayAsientoResul['tipo_documento_cruce'].'</td>
                                <td>'.$arrayAsientoResul['numero_documento_cruce'].'</td>
                                <td>'.$arrayAsientoResul['sucursal'].'</td>

                                <td>'.$arrayAsientoResul['nit_tercero'].'</td>
                                <td>'.$arrayAsientoResul['tercero'].'</td>
                                <td style="text-align:right;">'.$arrayAsientoResul['debe'].'</td>
                                <td style="text-align:right;">'.$arrayAsientoResul['haber'].'</td>
                                <td style="text-align:right;">'.$arrayAsientoResul['codigo_centro_costos'].'</td>
                                <td style="text-align:right;">'.$arrayAsientoResul['centro_costos'].'</td>
                                '.$campoAddFila.'
                            </tr>';
            if ($mostrar_observacion<>'' && $arrayAsientoResul['observacion']<>'') {
                $bodyTable .="<tr>
                                <td>&nbsp;</td>
                                <td colspan='10' style='padding-bottom: 10px;'>$arrayAsientoResul[observacion]</td>
                            </tr>";
            }
            $acumuladoDebeCuenta  +=$arrayAsientoResul['debe'];
            $acumuladoHaberCuenta +=$arrayAsientoResul['haber'];
        }
    }

    $bodyTable .= '<tr class="titulos_totales_empleados">
                       <td colspan="9">SUBTOTALES</td>
                       <td style="text-align:right;">'.$acumuladoDebeCuenta.'</td>
                       <td style="text-align:right;">'.$acumuladoHaberCuenta.'</td>
                       <td></td>
                       <td></td>
                    </tr>
                </table>';

?>
<style>
    .contenedor_informe, .contenedor_titulo_informe{
        width         : 100%;
        border-bottom : 1px solid #CCC;
        margin        : 0 0 10px 0;
        font-size     : 11px;
        font-family   : Verdana, Geneva, sans-serif
    }

    .titulo_informe_label{
        float       : left;
        width       : 130px;
        font-weight : bold;
    }

    .titulo_informe_detalle{
        float         : left;
        width         : 210px;
        padding       : 0 0 0 5px;
        white-space   : nowrap;
        overflow      : hidden;
        text-overflow : ellipsis;
    }

    .titulo_informe_empresa{
        float       : left;
        width       : 100%;
        font-size   : 16px;
        font-weight : bold;
    }

    .table{ font-size : 11px; width: 100%; margin-top: 20px; }
    .table thead td { border-bottom: 1px solid; border-top: 1px solid; }
    .table td{ padding-right: 10px; }

    .empleado_nomina{
        height       : 25px;
        background   : #999;
        padding-left : 10px;
        height       : 25px;
        font-size    : 12px;
    }

    .empleado_nomina td{
        padding-left : 10px;
        color        : #FFF;
        height       : 25px;
    }

    .titulos_conceptos_empleados{
        height      : 25px;
        background  : #EEE;
        font-weight : bold;
    }

    .titulos_conceptos_empleados td{
        color  : #8E8E8E;
        height : 25px;
    }

    .titulos_conceptos_empleados td,.titulos_totales_empleados td{
        border-top    : 1px solid #999;
        border-bottom : 1px solid #999;
        background    : #EEE;
        padding-left  : 10px;
    }

    .titulos_totales_empleados{
        height      : 25px;
        font-weight : bold;
        font-size   : 12px;
        color       : #8E8E8E;
        font-weight : bold;
    }

    .titulos_totales_empleados td{
        color       : #8E8E8E;
        height      : 25px;
        font-weight : bold;
    }

    .filaConcepto{ height: 25px; }
    .filaConcepto td{ padding-left: 10px; }
    .table{ font-size : 11px; width: 100%; margin-top: 20px; }

</style>

<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body>

    <div class="contenedor_titulo_informe">
        <div style=" width:100%">
            <div style="width:100%; text-align:center">
                <table align="center" style="text-align:center;" >
                    <tr><td class="titulo_informe_empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr><td style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;text-transform: uppercase;">LIBRO AUXILIAR</td></tr>
                    <tr><td style="font-size:11px;text-align:center;"><?php echo $subtitulo_cabecera; ?> Periodo del <?php echo $MyInformeFiltroFechaInicio; ?> al <?php echo $MyInformeFiltroFechaFinal; ?></td></tr>
                    <!--<tr><td style="font-size:11px; text-align:center;" >Impreso: <?php echo fecha_larga_hora_m(date('Y-m-d H:i:s')); ?></td></tr>-->
                </table>
            </div>
        </div>
    </div>

    <?php echo $bodyTable; ?>

    <table class="table" style="border-collapse:collapse; width:50%;">
        <tr class="empleado_nomina" style="font-size:13px; font-weight:bold;">
            <td><b>SALDOS TOTALES</b></td>
            <td>&nbsp;</td>
        <tr>
        <tr class="titulos_totales_empleados">
            <td style="font-size:11px;" class="titulos_totales_empleados">SALDO ANTERIOR</td>
            <td style="text-align:right;"><?php echo round($acumuladoSaldoAnterior,$_SESSION['DECIMALESMONEDA']); ?></td>
        </tr>
        <tr class="titulos_totales_empleados">
            <td style="font-size:11px;" >DEBITO</td>
            <td style="text-align:right;"><?php echo $acumuladoDebe; ?></td>
        </tr>
        <tr class="titulos_totales_empleados">
            <td style="font-size:11px;" >CREDITO</td>
            <td style="text-align:right;"><?php echo $acumuladoHaber; ?></td>
        </tr>
        <tr class="titulos_totales_empleados">
            <td style="font-size:11px;" >SALDO ACTUAL</td>
            <td style="text-align:right;"><?php echo round($acumuladoSaldoAnterior+$acumuladoDebe-$acumuladoHaber,$_SESSION['DECIMALESMONEDA']); ?></td>
        </tr>
        </tr>
    </table>
</body>

<script>
    <?php echo $script; ?>
</script>

<?php

	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER-L'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }
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
					$ORIENTACION	// L - landscape, P - portrait
				);
        // $mpdf-> debug = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->useSubstitutions = true;
        $mpdf->simpleTables     = true;
        $mpdf->packTableData    = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');

		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'F'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }
		exit;
	}
    else{ echo $texto; }

?>