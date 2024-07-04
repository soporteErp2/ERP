<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel;');
       header("Content-Disposition: attachment; filename=cartera_por_edades_de_cliente_".date('Y_m_d').".xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa       = $_SESSION['EMPRESA'];
    $desde            = $MyInformeFiltroFechaInicio;
    $hasta            = $MyInformeFiltroFechaFinal;
    $divTitleSucursal = '';
    $whereFecha       = '';
    $whereSucursal    = '';

    //SALDOS
    $acumuladoDebe          = 0;
    $acumuladoHaber         = 0;
    $acumuladoSaldoAnterior = 0;
    $acumuladoSaldoActual   = 0;

    $split1 = explode('-', $desde);
    $split2 = explode('-', $hasta);

    $year1 = $split1[0];
    $year2 = $split2[0];
    $mes1  = $split1[1];
    $mes2  = $split2[1];
    $dia1  = $split1[2];
    $dia2  = $split2[2];
    $hoy   = date("Y-m-d");

    $wherePuc = '';
    $tercero  = '';

    $acumuladoporVencer           = 0;
    $acumuladounoAtreinta         = 0;
    $acumuladotreintayunoAsesenta = 0;
    $acumuladosesentayunoAnoventa = 0;
    $acumuladomasDenoventa        = 0;

    $subtitulo = '';
    if ($sucursal!='' && $sucursal!='global') {
        $whereSucursal = ' AND VF.id_sucursal='.$sucursal;

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
        $query = mysql_query($sql,$link);
        $subtitulo .= '<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
    }

    //SI NO EXISTE LA VARIABLE DE FECHA FINAL, QUIERE DECIR QUE SE ESTA GENERANDO EL INFORME DESDE LA INTERFAZ PRINCIPAL Y NO DE LA VENTANA DE CONFIGURACION
    //ENTONCES MOSTRAMOS TODAS LAS FACTURAS HASTA HOY Y CON TODOS LOS CLIENTES
    if (!isset($MyInformeFiltroFechaFinal) || $MyInformeFiltroFechaFinal=='') {
        $MyInformeFiltroFechaFinal=date("Y-m-d");
        $whereFecha = "AND A.fecha <= '$MyInformeFiltroFechaFinal' ";
        $script = 'localStorage.plazo_por_vencer                   = "";
                    localStorage.vencido_1_30                      = "";
                    localStorage.vencido_31_60                     = "";
                    localStorage.vencido_61_90                     = "";
                    localStorage.vencido_mas_90                    = "";
                    localStorage.MyInformeFiltroFechaFinalCartera  = "";
                    localStorage.MyInformeFiltroFechaInicioCartera = "";
                    localStorage.tipo_fecha_informe                = "";
                    localStorage.tipo_informe_cartera_edades       = "";
                    localStorage.sucursal_cartera_edades           = "";

                    //VACIAR LOS ARRAY DE LOS DATOS DEL CLIENTE
                    arrayClientes.length        = 0;
                    clientesConfigurados.length = 0;';

        $subtitulo.='Corte a '.$MyInformeFiltroFechaFinal;
    }
    else{

        //SI SE ESTA ENVIANDO DESDE LA VENTANA DE CONFIGURAR
        if ($tipo_fecha_informe=='corte') {
            $whereFecha  = "AND A.fecha <= '$MyInformeFiltroFechaFinal' ";
            $subtitulo  .= 'Corte a '.$MyInformeFiltroFechaFinal;
        }
        else if($tipo_fecha_informe=='rango_fechas'){
            $whereFecha  = "AND A.fecha BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal' ";
            $subtitulo  .= 'Desde '.$MyInformeFiltroFechaInicio.' Hasta '.$MyInformeFiltroFechaFinal;
        }

        //SI ES FRILTRADO POR CLIENTES, CREAMOS EL WHERE PARA PASARLO AL QUERY
        $whereIdClientes = '';
        $whereClientes   = '';

        if ($idClientes!='') {
            $idClientesQuery = explode(",",$idClientes);

            //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
            foreach ($idClientesQuery as $indice => $valor) {
                $whereIdClientes = ($whereIdClientes=='')? 'VF.id_cliente='.$valor : $whereIdClientes.' OR VF.id_cliente='.$valor;
            }

            $whereClientes=($whereIdClientes!='')? "AND (".$whereIdClientes.")" : "";
        }

        //SI ES FILTRADO POR LOS CHECKBOX DE PLAZOS DE LA FECHA
        $wherePlazos = '';
        if ($sqlCheckbox!='') { $wherePlazos="AND ($sqlCheckbox) "; }
    }

    //CONSUTA LAS CUENTAS DE PAGO CREDITO
    if($cuenta != ''){
        $cuenta = substr($cuenta, 0, -1);
        $arrayCuenta = explode(",", $cuenta);
        foreach ($arrayCuenta as $key => $cuenta_pago) {
            $whereCuentas .= "A.codigo_cuenta=$cuenta_pago OR ";
        }

        $whereCuentas = substr($whereCuentas, 0, -3);
    }
    else{
        $whereCuentas     = "";
        $sqlCuentasPago   = "SELECT cuenta FROM configuracion_cuentas_pago WHERE id_empresa=$id_empresa AND activo=1 AND tipo='Venta' AND estado='Credito'";
        $queryCuentasPago = mysql_query($sqlCuentasPago,$link);
        while ($rowCuenta = mysql_fetch_assoc($queryCuentasPago)) { $whereCuentas .= " OR A.codigo_cuenta=$rowCuenta[cuenta]"; }

        $whereCuentas = substr($whereCuentas, 3);
    }

    if($hasta == ''){ $hasta = $hoy; }

    $sqlFacturas = "SELECT
                        T.telefono1,
                        T.celular1,
                        VF.cuenta_pago AS codigo_cuenta,
                        DATEDIFF('$hasta',VF.fecha_vencimiento) AS dias,
                        VF.id,
                        VF.id_cliente,
                        VF.nit,
                        VF.cliente,
                        VF.fecha_inicio,
                        VF.fecha_vencimiento,
                        VF.numero_factura_completo,
                        VF.codigo_centro_costo AS codigo_ccos,
                        VF.centro_costo AS nombre_ccos,
                        VF.sucursal,
                        SUM(A.debe - A.haber) AS saldo
                    FROM
                        asientos_colgaap AS A
                    INNER JOIN ventas_facturas AS VF ON(
                        A.id_documento_cruce = VF.id
                        AND A.codigo_cuenta = VF.cuenta_pago
                        AND VF.activo = 1
                        AND VF.estado = 1
                        AND VF.id_empresa = '$id_empresa'
                        $whereSucursal
                        $whereClientes
                        $wherePlazos
                    )
                    LEFT JOIN terceros AS T ON(
                        VF.id_cliente = T.id
                        AND T.id_empresa = $id_empresa
                    )
                    WHERE
                        A.activo=1
                        $whereFecha
                        AND ($whereCuentas)
                        AND A.tipo_documento_cruce='FV'
                        AND A.id_empresa=$id_empresa
                    GROUP BY A.id_documento_cruce
                    HAVING saldo > 0
                    ORDER BY
                        VF.cliente,VF.fecha_vencimiento ASC";
    // echo $sqlFacturas; exit;

    $queryFacturas = mysql_query($sqlFacturas,$link);

    if ($tipo_informe=='' || !isset($tipo_informe)) { $tipo_informe='detallado'; }

    // tipo_informe
    // detallado
    // totalizado_terceros
    // totalizado_edades

    //VARIABLES DE ACUMULADOS TOTALES
    $acumuladoporVencerTotal           = 0;
    $acumuladounoAtreintaTotal         = 0;
    $acumuladotreintayunoAsesentaTotal = 0;
    $acumuladosesentayunoAnoventaTotal = 0;
    $acumuladomasDenoventaTotal        = 0;

    $colspanCliente = ($IMPRIME_XLS=='true')? 'colspan="10"' : 'colspan="6"';
    $colspanCartera = ($IMPRIME_XLS=='true')? 'colspan="11"' : 'colspan="7"';

    $style = 'color:#FFF';
    while ($rowFacturas=mysql_fetch_array($queryFacturas)) {

        $dias      = $rowFacturas['dias'];
        $telefono  = ' Telefono: '.$rowFacturas['telefono1'];
        $telefono .= ($rowFacturas['telefono1']!='' && $rowFacturas['celular1']!='')? ' - Celular: '.$rowFacturas['celular1'] : $rowFacturas['celular1'];

        $rowFacturas['saldo'] = $rowFacturas['saldo'];
        $porVencer            = ($dias<1)? $rowFacturas['saldo']: '&nbsp;';
        $unoAtreinta          = ($dias>0 && $dias<=30)? $rowFacturas['saldo'] : '&nbsp;';
        $treintayunoAsesenta  = ($dias>30 && $dias<=60)? $rowFacturas['saldo'] : '&nbsp;';
        $sesentayunoAnoventa  = ($dias>60 && $dias<=90)? $rowFacturas['saldo'] : '&nbsp;';
        $masDenoventa         = ($dias>90)? $rowFacturas['saldo'] : '&nbsp;';

       $style  = ($style!='')? '' : 'background:#f7f7f7;';
       $campos = ($IMPRIME_XLS=='true')? '<td>'.$rowFacturas['sucursal'].'</td>
                                        <td>'.$rowFacturas['codigo_ccos'].'</td>
                                        <td>'.$rowFacturas['nombre_ccos'].'</td>
                                        <td>'.$rowFacturas['nit'].'</td>
                                        <td>'.$rowFacturas['cliente'].'</td>'
                                        : '<td>'.$rowFacturas['sucursal'].'</td>';

       if ($tercero!=$rowFacturas['id_cliente']) {
            if ($tercero!=0) {
                $bodyTable.=($tipo_informe!='totalizado_edades')? '<tr class="total">
                                                                        <td '.$colspanCliente.'>TOTAL CLIENTE</td>
                                                                        <td style="text-align:right;">'.validar_numero_formato($acumuladoSaldo,$IMPRIME_XLS).'</td>
                                                                        <td style="text-align:right;">'.validar_numero_formato($acumuladoporVencer,$IMPRIME_XLS).'</td>
                                                                        <td style="text-align:right;">'.validar_numero_formato($acumuladounoAtreinta,$IMPRIME_XLS).'</td>
                                                                        <td style="text-align:right;">'.validar_numero_formato($acumuladotreintayunoAsesenta,$IMPRIME_XLS).'</td>
                                                                        <td style="text-align:right;">'.validar_numero_formato($acumuladosesentayunoAnoventa,$IMPRIME_XLS).'</td>
                                                                        <td style="text-align:right;">'.validar_numero_formato($acumuladomasDenoventa,$IMPRIME_XLS).'</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>&nbsp;</td>
                                                                    </tr>': '';

                $acumuladoSaldo               = 0;
                $acumuladoporVencer           = 0;
                $acumuladounoAtreinta         = 0;
                $acumuladotreintayunoAsesenta = 0;
                $acumuladosesentayunoAnoventa = 0;
                $acumuladomasDenoventa        = 0;
            }

            $tercero    = $rowFacturas['id_cliente'];
            $bodyTable .= ($tipo_informe!='totalizado_edades')? '<tr class="total">
                                                                    <td colspan="16"><b>'.$rowFacturas['cliente'].' '.$telefono.'</b></td>
                                                                </tr>' : '';
            $bodyTable .= ($tipo_informe=='detallado')? '<tr>
                                                            '.$campos.'
                                                            <td width="80" style="'.$style.'padding-left:15px;">'.$rowFacturas['codigo_cuenta'].'</td>
                                                            <td width="120" style="'.$style.'">'.$rowFacturas['numero_factura_completo'].'</td>
                                                            <td width="80" style="'.$style.'">'.$rowFacturas['fecha_inicio'].'</td>
                                                            <td width="80" style="'.$style.'">'.$rowFacturas['fecha_vencimiento'].'</td>
                                                            <td width="80" style="'.$style.' text-align:center;">'.$dias.'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($rowFacturas['saldo'],$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($porVencer,$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($unoAtreinta,$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($treintayunoAsesenta,$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($sesentayunoAnoventa,$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($masDenoventa,$IMPRIME_XLS).'</td>
                                                        </tr>' : '';
        }
        else{

            $bodyTable .= ($tipo_informe=='detallado')? '<tr>
                                                            '.$campos.'
                                                            <td width="80" style="'.$style.'padding-left:15px;">'.$rowFacturas['codigo_cuenta'].'</td>
                                                            <td width="120" style="'.$style.'">'.$rowFacturas['numero_factura_completo'].'</td>
                                                            <td width="80" style="'.$style.'">'.$rowFacturas['fecha_inicio'].'</td>
                                                            <td width="80" style="'.$style.'">'.$rowFacturas['fecha_vencimiento'].'</td>
                                                            <td width="80" style="'.$style.' text-align:center;">'.$dias.'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($rowFacturas['saldo'],$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($porVencer,$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($unoAtreinta,$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($treintayunoAsesenta,$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($sesentayunoAnoventa,$IMPRIME_XLS).'</td>
                                                            <td width="80" style="'.$style.' text-align:right;">'.validar_numero_formato($masDenoventa,$IMPRIME_XLS).'</td>
                                                        </tr>': '';

        }

        $acumuladoSaldo+=$rowFacturas['saldo'];
        $acumuladoporVencer           += ($dias<1)? $rowFacturas['saldo']: '';
        $acumuladounoAtreinta         += ($dias>0 && $dias<=30)? $rowFacturas['saldo'] : 0;
        $acumuladotreintayunoAsesenta += ($dias>30 && $dias<=60)? $rowFacturas['saldo'] : 0;
        $acumuladosesentayunoAnoventa += ($dias>60 && $dias<=90)? $rowFacturas['saldo'] : 0;
        $acumuladomasDenoventa        += ($dias>90)? $rowFacturas['saldo'] : 0;

        $acumuladoporVencerTotal           += ($dias<1)? $rowFacturas['saldo']: '';
        $acumuladounoAtreintaTotal         += ($dias>0 && $dias<=30)? $rowFacturas['saldo'] : 0;
        $acumuladotreintayunoAsesentaTotal += ($dias>30 && $dias<=60)? $rowFacturas['saldo'] : 0;
        $acumuladosesentayunoAnoventaTotal += ($dias>60 && $dias<=90)? $rowFacturas['saldo'] : 0;
        $acumuladomasDenoventaTotal        += ($dias>90)? $rowFacturas['saldo'] : 0;
    }
    if ($tercero!=0) {
        $bodyTable.=($tipo_informe!='totalizado_edades')?'<tr class="total">
                                                            <td '.$colspanCliente.'>TOTAL CLIENTE</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($acumuladoSaldo,$IMPRIME_XLS).'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($acumuladoporVencer,$IMPRIME_XLS).'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($acumuladounoAtreinta,$IMPRIME_XLS).'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($acumuladotreintayunoAsesenta,$IMPRIME_XLS).'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($acumuladosesentayunoAnoventa,$IMPRIME_XLS).'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($acumuladomasDenoventa,$IMPRIME_XLS).'</td>
                                                        </tr>' : '';

    }

    //TOTALES DEL INFORME
    $bodyTable .= '<tr><td>&nbsp;</td></tr>
                    <tr class="total">
                        <td '.$colspanCartera.'><b>TOTAL CARTERA</b></td>
                        <td style="text-align:right;"><b>'.validar_numero_formato($acumuladoporVencerTotal,$IMPRIME_XLS).'</b></td>
                        <td style="text-align:right;"><b>'.validar_numero_formato($acumuladounoAtreintaTotal,$IMPRIME_XLS).'</b></td>
                        <td style="text-align:right;"><b>'.validar_numero_formato($acumuladotreintayunoAsesentaTotal,$IMPRIME_XLS).'</b></td>
                        <td style="text-align:right;"><b>'.validar_numero_formato($acumuladosesentayunoAnoventaTotal,$IMPRIME_XLS).'</b></td>
                        <td style="text-align:right;"><b>'.validar_numero_formato($acumuladomasDenoventaTotal,$IMPRIME_XLS).'</b></td>
                    </tr>
                    <tr class="total">
                        <td colspan="11"><b>TOTAL CONSOLIDADO</b></td>
                        <td style="text-align:right;"><b>'.validar_numero_formato(($acumuladoporVencerTotal+$acumuladounoAtreintaTotal+$acumuladotreintayunoAsesentaTotal+$acumuladosesentayunoAnoventaTotal+$acumuladomasDenoventaTotal),$IMPRIME_XLS).'</b></td>
                    </tr>
                    ';

    $tituloHead=($IMPRIME_XLS=='true')? '<td>SUCURSAL</td>
                                        <td>CODIGO CENTRO DE COSTO</td>
                                        <td>CENTRO DE COSTO</td>
                                        <td width="90" style="font-weight:bold; border-left:1px solid;">NIT</td>
                                        <td width="90" style="font-weight:bold; border-left:1px solid;">CLIENTE</td>
                                        <td width="90" style="font-weight:bold; border-left:1px solid;">CUENTA</td>'
                                        : '<td>SUCURSAL</td>
                                        <td width="90" style="font-weight:bold;">CLIENTE</td>';
?>
<style>
	.contenedor_informe, .contenedor_titulo_informe{
        width     :	100%;
        margin    :	0 0 15px 0;
        font-size :	11px;
        float     : left;
	}

	.titulo_informe_label{
        float       : left;
        width       : 130px;
        font-weight : bold;
	}

	.titulo_informe_detalle{
        float         :	left;
        width         :	210px;
        padding       :	0 0 0 5px;
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

<body >
    <div class="contenedor_titulo_informe">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center">
                <table align="center" style="text-align:center;margin-bottom:10px;">
                    <tr><td class="titulo_informe_empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr><td style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></td></tr>
                    <tr><td style="font-size:11px;text-align:center;"><?php echo $subtitulo; ?></td></tr>
                </table>

                <table class="table" cellspacing="0">
                    <thead>
                        <tr>
                            <?php echo $tituloHead; ?>
                            <td width="120" style="font-weight:bold;">NUMERO</td>
                            <td width="80" style="font-weight:bold; ">FECHA</td>
                            <td width="80" style="font-weight:bold; ">VENCIMIENTO</td>
                            <td width="80" style="font-weight:bold; text-align:center;">DIAS</td>
                            <td width="80" style="font-weight:bold; text-align:right;">SALDO</td>
                            <td width="80" style="font-weight:bold; text-align:right;">POR VENCER</td>
                            <td width="80" style="font-weight:bold; text-align:right;">1-30</td>
                            <td width="80" style="font-weight:bold; text-align:right;">31-60</td>
                            <td width="80" style="font-weight:bold; text-align:right;">61-90</td>
                            <td width="80" style="font-weight:bold; text-align:right;">MAS 90</td>
                        </tr>
                    </thead>
                <?php echo $bodyTable; ?>
                </table>
            </div>
        </div>
    </div>
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
        // $mpdf->useSubstitutions = true;
        $mpdf->simpleTables = true;
        $mpdf->packTableData= true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle($documento);
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA=='true'){ $mpdf->Output("cartera_por_edades_de_clientes.pdf",'D'); }
        else{ $mpdf->Output("cartera_por_edades_de_clientes.pdf",'I'); }
		exit;
	}
    else{ echo $texto; }

    function calculaDias($fecha_inicio,$fecha_vencimiento){
        $diferencia = (strtotime($fecha_vencimiento)-strtotime($fecha_inicio))/86400;
        $diferencia = abs($diferencia);
        $diferencia = floor($diferencia);
        return $diferencia;
    }

?>

