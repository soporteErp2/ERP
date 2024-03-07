<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=libro_diario_".date("Y_m_d").".xls");
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

    $groupBy     = ($nivel_cuentas > 0)? "LEFT(codigo_cuenta,$nivel_cuentas)": "codigo_cuenta";
    $campoCuenta = ($nivel_cuentas > 0)? "LEFT(codigo_cuenta,$nivel_cuentas) AS codigo_cuenta": "codigo_cuenta";
    $whereRangoCuentas=str_replace('{.}', "%", $whereRangoCuentas);

    if (!isset($MyInformeFiltroFechaInicio) && !isset($MyInformeFiltroFechaFinal)){
        $MyInformeFiltroFechaInicio = date('Y').'-01-01';
        $MyInformeFiltroFechaFinal  = date('Y-m-d');
        $script = '
                    localStorage.MyInformeFiltroFechaFinalLibroDiario  = "";
                    localStorage.MyInformeFiltroFechaInicioLibroDiario = "";
                    localStorage.sucursal_libro_diario                 = "";
                    localStorage.clase_cuenta_libro_diario             = "";
                    ';
    }

    if ($clase_cuenta<>'' && $clase_cuenta<>'global') {
        $whereClase = " AND codigo_cuenta LIKE '$clase_cuenta%' ";
    }

    if ($sucursal!='' && $sucursal!='global') {

        $whereSucursal = ' AND id_sucursal='.$sucursal;

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
        $query = mysql_query($sql,$link);
        $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
    }

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
	$nombre_empresa	= $_SESSION['NOMBREEMPRESA'];


    //=======================================// SALDO ACTUAL //=======================================//
    //************************************************************************************************//
    $sqlAsientos   = "SELECT
                            debe,
                            haber,
                            $campoCuenta,
                            cuenta,
                            tipo_documento,
                            consecutivo_documento,
                            fecha,
                            tipo_documento_extendido
                        FROM asientos_colgaap
                        WHERE activo=1
                            AND id_empresa = '$id_empresa'
                            $whereClase
                            $whereSucursal
                            $whereRangoCuentas
                            AND tipo_documento<>'NCC'
                            AND fecha BETWEEN ('$MyInformeFiltroFechaInicio') AND ('$MyInformeFiltroFechaFinal')
                        ORDER BY fecha DESC,CAST(codigo_cuenta AS char) ASC";
    $queryAsientos = mysql_query($sqlAsientos, $link);
    $fecha  = 0;
    $cuenta = 0;
    while ($row = mysql_fetch_array($queryAsientos)){
        $style = ($style!='')? '' : 'background:#F7F7F7;';

        if ($cuenta<>$row['codigo_cuenta']) {
            if ($cuenta<>0) {
                $bodyTable .= '<tr class="total">
                                    <td align="center" colspan="4"><b>TOTAL CUENTA</b></td>
                                    <td style="text-align:left;"><b>'.validar_numero_formato($acumDebe,$IMPRIME_XLS).'</b></td>
                                    <td style="text-align:left;"><b>'.validar_numero_formato($acumhaber,$IMPRIME_XLS).'</b></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>';
                $acumDebe  = 0;
                $acumhaber = 0;
            }

            $bodyTable .= '<tr class="total">
                                <td ><b>'.$row['codigo_cuenta'].'</b></td>
                                <td colspan="2"><b>'.$row['cuenta'].'</b></td>
                                <td colspan="3"><b>'.$row['fecha'].'</b></td>
                            </tr>';
            $cuenta = $row['codigo_cuenta'];
        }

        $bodyTable .=  '<tr class="fila">
                            <td style="'.$style.'" >&nbsp;</td>
                            <td style="'.$style.'" >'.$row['tipo_documento_extendido'].' ('.$row['tipo_documento'].')</td>
                            <td style="'.$style.'" >'.$row['consecutivo_documento'].'</td>
                            <td style="'.$style.'" >'.number_format($row['debe'], $_SESSION['DECIMALESMONEDA'], ',', '.').'</td>
                            <td style="'.$style.'" >'.number_format($row['haber'], $_SESSION['DECIMALESMONEDA'], ',', '.').'</td>
                        </tr>';

        $acumDebe  += $row['debe'];
        $acumhaber += $row['haber'];
    }

    $bodyTable .= '<tr class="total">
                        <td align="center" colspan="4"><b>TOTAL CUENTA</b></td>
                        <td style="text-align:left;"><b>'.validar_numero_formato($acumDebe,$IMPRIME_XLS).'</b></td>
                        <td style="text-align:left;"><b>'.validar_numero_formato($acumhaber,$IMPRIME_XLS).'</b></td>
                    </tr>';

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
        /*padding-left  : 10px;*/
        height        : 25px;
        font-weight   : bold;
        color         : #8E8E8E;
    }

    .fila{
        height: 25px;
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
                    <td width="80px"><b>CUENTA</b></td>
                    <td width="80px"><b>COMPROBANTE</b></td>
                    <td width="80px"><b>CONSECUTIVO</b></td>
                    <td width="80px"><b>FECHA</b></td>
                    <td width="80px"><b>DEBITO</b></td>
                    <td width="80px"><b>CREDITO</b></td>
                </tr>
            </thead>
            <?php echo $bodyTable; ?>

        </table>
    </div>
</body>

<script>
    <?php echo $script; ?>
</script>

<?php
// exit;
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
        // $mpdf->debug = true;
        $mpdf->SetProtection(array('print'));
        $mpdf->useSubstitutions = true;
        $mpdf->simpleTables     = true;
        $mpdf->packTableData    = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        // $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }
		exit;
	}
    else{ echo $texto; }
?>
