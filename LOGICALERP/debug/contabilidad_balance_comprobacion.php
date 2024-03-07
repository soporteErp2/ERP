<?php
    include_once('../../configuracion/conectar.php');
    include_once('../../configuracion/define_variables.php');
    ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=balance_de_comprobacion.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa         = $_SESSION['EMPRESA'];
    $desde              = $MyInformeFiltroFechaInicio;
    $hasta              = $MyInformeFiltroFechaFinal;
    $id_sucursal        = $MyInformeFiltro_0;
    $divTitleSucursal   = '';
    $whereSucursal      = '';
    $groupBy            = '';
    $subtitulo_cabecera = '';
    $estilo_td          = '';
    $td_body            = '';
    $td_head            = '';
    $bodyTable          = '';
    $idTercero          = 0;

    //NIVEL DE CUENTAS
    $groupBy     = ($nivel_cuentas > 0)? "LEFT(codigo_cuenta,$nivel_cuentas)": "codigo_cuenta";
    $campoCuenta = ($nivel_cuentas > 0)? "LEFT(codigo_cuenta,$nivel_cuentas) AS codigo_cuenta": "codigo_cuenta";

    if (!isset($MyInformeFiltroFechaInicio) && !isset($MyInformeFiltroFechaFinal)) {
        $MyInformeFiltroFechaInicio = date('Y').'-01-01';
        $MyInformeFiltroFechaFinal  = date('Y-m-d');
        $script = 'localStorage.cuenta_inicialBC = "";
                    localStorage.cuenta_finalBC                                = "";
                    localStorage.MyInformeFiltroFechaFinalBalanceComprobacion  = "";
                    localStorage.MyInformeFiltroFechaInicioBalanceComprobacion = "";
                    localStorage.sucursal_balance_comprobacion                 = "";
                    arraytercerosBC.length                                     = 0;
                    tercerosConfiguradosBC.length                              = 0;
                    checkBoxSelectAllTercerosBC                                = 0;';
    }
    $idTerceros = 'todos';
    if ($idTerceros!='' ) {

        if ($idTerceros!='todos') {
            $idTercerosQuery=explode(",",$idTerceros);
            //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
            foreach ($idTercerosQuery as $indice => $valor) {
                // $whereIdTerceros = ($whereIdTerceros=='')? ' id_tercero='.$valor : $whereIdTerceros.' OR id_tercero='.$valor;
                // $whereClientes   = ($whereIdTerceros!='')? "AND (".$whereIdTerceros.")" : "";
            }
        }

        $groupBy .= ',id_tercero';
        $width_td = '170';
        $td_head  = '<td width="170">NIT</td><td width="170">Digito de Verificacion</td><td width="170">TERCERO</td><td width="170">Direccion</td><td width="170">Ciudad</td>';
        $td_body  = '<td width="170"></td><td width="170"></td>';
    }

    if ($sucursal!='' && $sucursal!='global') {

        $whereSucursal = ' AND id_sucursal='.$sucursal;

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
        $query = mysql_query($sql,$link);
        $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
    }

    // $whereCuentasCierre = ($cuentas_cierre=='true')? '' : "AND tipo_documento<>'NCC' " ;

    //SALDOS
    $totalDebe          = 0;
    $totalHaber         = 0;
    $totalSaldoAnterior = 0;
    $totalSaldoActual   = 0;

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
    // echo $whereRangoCuentas;
	$nombre_empresa	= $_SESSION['NOMBREEMPRESA'];
    $whereRangoCuentas=str_replace('{.}', "%", $whereRangoCuentas);
    //=======================================// SALDO ANTERIOR //=======================================//
    //**************************************************************************************************//
    $sqlAsientosAnterior = "SELECT
                                SUM(debe-haber) AS saldo,
                                cuenta,
                                LEFT(codigo_cuenta,8) AS codigo_cuenta,
                                id_tercero,
                                nit_tercero,
                                tercero
                            FROM asientos_colgaap
                            WHERE activo=1
                            AND id_empresa = '49'
                            AND id_sucursal=47
                            AND fecha < '2015-01-01'
                            GROUP BY LEFT(codigo_cuenta,8),id_tercero
                            ORDER BY CAST(codigo_cuenta as CHAR) ASC, nit_tercero DESC";
    $queryAsientosAnterior = mysql_query($sqlAsientosAnterior,$link);

    while($rowAsientosAnterior = mysql_fetch_array($queryAsientosAnterior)){
        $whereIdTerceros .= ($whereIdTerceros=='')? ' id='.$rowAsientosAnterior['id_tercero']: ' OR id='.$rowAsientosAnterior['id_tercero'];
        //SALDO
        $totalSaldoAnterior += $rowAsientosAnterior['saldo'];

        $cuenta    = $rowAsientosAnterior['codigo_cuenta'];
        if ($idTerceros!='' ) { $idTercero = $rowAsientosAnterior['id_tercero']; }

        $arrayAsiento["$cuenta"][$idTercero] = array(
                                                        'debe_actual'   => 0,
                                                        'haber_actual'  => 0,
                                                        'descripcion'   => $rowAsientosAnterior['cuenta'],
                                                        'nit_tercero'   => $rowAsientosAnterior['nit_tercero'],
                                                        'tercero'       => $rowAsientosAnterior['tercero'],
                                                        'saldoAnterior' => $rowAsientosAnterior['saldo']
                                                    );

    }

    //=======================================// SALDO ACTUAL //=======================================//
    //************************************************************************************************//
    $sqlAsientos   = "SELECT
                        SUM(debe) AS debe,
                        SUM(haber) AS haber,
                        cuenta,
                        LEFT(codigo_cuenta,8) AS codigo_cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero
                    FROM asientos_colgaap
                    WHERE
                        activo=1
                    AND id_empresa = '49'
                    AND id_sucursal=47
                    AND fecha BETWEEN ('2015-01-01') AND ('2015-12-31')
                    GROUP BY LEFT(codigo_cuenta,8),id_tercero
                    ORDER BY CAST(codigo_cuenta as CHAR) ASC, nit_tercero DESC";
    $queryAsientos = mysql_query($sqlAsientos, $link);


    while ($rowAsientos = mysql_fetch_array($queryAsientos)){
        $whereIdTerceros .= ($whereIdTerceros=='')? ' id='.$rowAsientos['id_tercero']: ' OR id='.$rowAsientos['id_tercero'];
        //SALDO
        $totalDebe  += $rowAsientos['debe'];
        $totalHaber += $rowAsientos['haber'];
        $cuenta      = $rowAsientos['codigo_cuenta'];

        if ($idTerceros!='' ) { $idTercero = $rowAsientos['id_tercero']; }

        $arrayAsiento["$cuenta"][$idTercero]['debe_actual']  = $rowAsientos['debe'];
        $arrayAsiento["$cuenta"][$idTercero]['haber_actual'] = $rowAsientos['haber'];
        $arrayAsiento["$cuenta"][$idTercero]['descripcion']  = $rowAsientos['cuenta'];
        $arrayAsiento["$cuenta"][$idTercero]['nit_tercero']  = $rowAsientos['nit_tercero'];
        $arrayAsiento["$cuenta"][$idTercero]['tercero']      = $rowAsientos['tercero'];
    }

    ksort($arrayAsiento);

    $sql="SELECT id,dv,direccion,ciudad FROM terceros WHERE activo=1 AND id_empresa=49 AND ($whereIdTerceros)";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $arrayTerceros[$row['id']] = array('dv' => $row['dv'],'direccion' => $row['direccion'],'ciudad' => $row['ciudad'] );
    }

    $style = 'color:#FFF';
    foreach ($arrayAsiento as $cuenta => $arrayCuenta) {

        // print_r($arrayCuenta);
        foreach ($arrayCuenta as $idTercero => $arrayDatos) {

            $style = ($style!='')? '' : 'background:#f7f7f7;';

            $saldoAnterior = ($arrayDatos['saldoAnterior'] != '')? $arrayDatos['saldoAnterior'] : 0;
            $debe_actual   = $arrayDatos['debe_actual'];
            $haber_actual  = $arrayDatos['haber_actual'];
            $newSaldo      = $saldoAnterior+$debe_actual-$haber_actual;

            $tercero     = $arrayDatos['tercero'];
            $nit_tercero = $arrayDatos['nit_tercero'];
            $descripcion = $arrayDatos['descripcion'];

            if ($idTerceros!='') {

                //RECORRER EL ARRAY DE LOS ID DE LOS TERCEROS Y ARMAR EL CUERPO DEL INFORME
                if ($IMPRIME_XLS=='true') { $tercero=utf8_encode($tercero); }

                $bodyTable .=  '<tr>
                                    <td style="'.$style.'width:65;">'.$cuenta.'</td>
                                    <td style="'.$style.'width:170;">'.$descripcion.'</td>
                                    <td style="'.$style.'width:170;">'.$nit_tercero.'</td>
                                    <td style="'.$style.'width:170;">'.$arrayTerceros[$idTercero]['dv'].'</td>
                                    <td style="'.$style.'width:170;">'.$tercero.'</td>
                                    <td style="'.$style.'width:170;">'.$arrayTerceros[$idTercero]['direccion'].'</td>
                                    <td style="'.$style.'width:170;">'.$arrayTerceros[$idTercero]['ciudad'].'</td>
                                    <td style="'.$style.'width:80;text-align:right;">'.validar_numero_formato($saldoAnterior,$IMPRIME_XLS).'</td>
                                    <td style="'.$style.'width:80;text-align:right;">'.validar_numero_formato($debe_actual,$IMPRIME_XLS).'</td>
                                    <td style="'.$style.'width:80;text-align:right;">'.validar_numero_formato($haber_actual,$IMPRIME_XLS).'</td>
                                    <td style="'.$style.'width:80;text-align:right;">'.validar_numero_formato($newSaldo,$IMPRIME_XLS).'</td>
                                </tr>';
            }
            else{
                $bodyTable .=  '<tr>
                                    <td style="'.$style.'width:65;">'.$cuenta.'</td>
                                    <td style="'.$style.'width:170;">'.$descripcion.'</td>
                                    <td style="'.$style.'width:80;text-align:right;">'.validar_numero_formato($saldoAnterior,$IMPRIME_XLS).'</td>
                                    <td style="'.$style.'width:80;text-align:right;">'.validar_numero_formato($debe_actual,$IMPRIME_XLS).'</td>
                                    <td style="'.$style.'width:80;text-align:right;">'.validar_numero_formato($haber_actual,$IMPRIME_XLS).'</td>
                                    <td style="'.$style.'width:80;text-align:right;">'.validar_numero_formato($newSaldo,$IMPRIME_XLS).'</td>
                                </tr>';
            }
        }
    }

    //SALDO
    $totalSaldoActual = $totalSaldoAnterior+$totalDebe-$totalHaber;

    $totalSaldoAnterior = number_format($totalSaldoAnterior,$_SESSION['DECIMALESMONEDA']);
    $totalDebe          = number_format($totalDebe,$_SESSION['DECIMALESMONEDA']);
    $totalHaber         = number_format($totalHaber,$_SESSION['DECIMALESMONEDA']);
    $totalSaldoActual   = number_format($totalSaldoActual,$_SESSION['DECIMALESMONEDA']);
?>
<style>
	.my_informe_Contenedor_Titulo_informe{
        float         :	left;
        width         :	100%;
        margin        :	0 0 10px 0;
        font-size     :	11px;
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

    .table{
        font-size       : 12px;
        width           : 100%;
        border-collapse : collapse;
    }

    .table thead{ background : #999; }

    .table thead td {
        padding-left : 10px;
        height       : 30px;
        background   : #999;
        color        : #FFF;
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
        color         : #8E8E8E;
    }

</style>

<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body>
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center">
                <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa;?></div>
                <div style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></div>
                <div style="margin-bottom:8px;"><?php echo $subtitulo_cabecera; ?> Periodo del <?php echo $MyInformeFiltroFechaInicio; ?> al <?php echo $MyInformeFiltroFechaFinal; ?></div>
            </div>
        </div>

    </div>

    <div class="my_informe_Contenedor_Titulo_informe">
        <table class="table">
            <thead>
                <tr>
                    <td width="65"><b>CUENTA</b></td>
                    <td width="<?php echo $width_td; ?>"><b>DESCRIPCION</b></td>
                    <?php echo $td_head; ?>
                    <td width="80"><b> SALDO ANTERIOR</b></td>
                    <td width="80"><b> DEBITO</b></td>
                    <td width="80"><b> CREDITO</b></td>
                    <td width="80"><b> SALDO ACTUAL </b></td>
                </tr>
            </thead>
            <?php echo $bodyTable; ?>
            <tr class="total">
                <td align="center" colspan="2"><b>TOTAL</b></td>
                <?php echo $td_body; ?>
                <td style="text-align:right;"><b><?php echo $totalSaldoAnterior; ?></b></td>
                <td style="text-align:right;"><b><?php echo $totalDebe; ?></b></td>
                <td style="text-align:right;"><b><?php echo $totalHaber; ?></b></td>
                <td style="text-align:right;"><b><?php echo $totalSaldoActual; ?></b></td>
            </tr>
        </table>
    </div>
</body>

<script>
    <?php echo $script; ?>
</script>

<?php
// exit;
	// $texto = ob_get_contents(); ob_end_clean();

	// if(isset($TAM)){ $HOJA = $TAM; }
 //    else{ $HOJA = 'LETTER-L'; }

	// if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	// if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	// if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	// if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
 //    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	// if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }
	// if($IMPRIME_PDF == 'true'){
	// 	include("../../../../misc/MPDF54/mpdf.php");
	// 	$mpdf = new mPDF(
	// 				'utf-8',  		// mode - default ''
	// 				$HOJA,			// format - A4, for example, default ''
	// 				12,				// font size - default 0
	// 				'',				// default font family
	// 				$MI,			// margin_left
	// 				$MD,			// margin right
	// 				$MS,			// margin top
	// 				$ML,			// margin bottom
	// 				10,				// margin header
	// 				10,				// margin footer
	// 				$ORIENTACION	// L - landscape, P - portrait
	// 			);
 //        // $mpdf->debug = true;
 //        $mpdf->SetProtection(array('print'));
 //        $mpdf->useSubstitutions = true;
 //        $mpdf->simpleTables     = true;
 //        $mpdf->packTableData    = true;
	// 	$mpdf->SetAutoPageBreak(TRUE, 15);
	// 	//$mpdf->SetTitle ( $documento );
	// 	$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
	// 	$mpdf->SetDisplayMode ( 'fullpage' );
	// 	$mpdf->SetHeader("");
 //        // $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

	// 	$mpdf->WriteHTML(utf8_encode($texto));

	// 	if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
 //        else{ $mpdf->Output($documento.".pdf",'I'); }
	// 	exit;
	// }
 //    else{ echo $texto; }
?>