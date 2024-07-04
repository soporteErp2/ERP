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

    $id_empresa = $_SESSION['EMPRESA'];
    $desde            = $MyInformeFiltroFechaInicio;
    $hasta            = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;
    // $generar          = $MyInformeFiltro_0;
    $divTitleSucursal   = '';
    $whereSucursal      = '';
    $subtitulo_cabecera = '';
    $whereVendedores    = '';

    // echo$MyInformeFiltroFechaFinal=(isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;

    if (isset($MyInformeFiltroFechaFinal) && $MyInformeFiltroFechaFinal!='') {
        $whereFechas=" AND VF.fecha_inicio BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";
    }
    else{
        $MyInformeFiltroFechaFinal=date("Y-m-d");

        $script = 'localStorage.MyInformeFiltroFechaInicioFacturas  = "";
                localStorage.MyInformeFiltroFechaFinalFacturas = "";
                localStorage.sucursal_facturas  = "";
                arraytercerosFV.length          = 0;
                tercerosConfiguradosFV.length   = 0;
                arrayvendedoresFV.length        = 0;
                vendedoresConfiguradosFV.length = 0;';
    }

    if ($idTerceros!='' ) {

        if ($idTerceros!='todos') {
            $idTercerosQuery=explode(",",$idTerceros);
            //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
            foreach ($idTercerosQuery as $indice => $valor) {
                $whereidTerceros=($whereidTerceros=='')? ' VF.id_cliente='.$valor : $whereidTerceros.' OR VF.id_cliente='.$valor ;
                $whereClientes=($whereidTerceros!='')? "AND (".$whereidTerceros.")" : "" ;
            }
        }

        $groupBy = ',VF.id_cliente';
    }

    if ($idVendedores!='' ) {

        $idTercerosQuery=explode(",",$idVendedores);
         //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
         foreach ($idTercerosQuery as $indice => $valor) {
             $whereidVendedores=($whereidVendedores=='')? ' VF.id_vendedor='.$valor : $whereidVendedores.' OR VF.id_vendedor='.$valor ;
             $whereVendedores=($whereidVendedores!='')? "AND (".$whereidVendedores.")" : "" ;
        }

        $groupBy  =($groupBy!='')? ',id_vendedor' : 'id_vendedor';
    }

    if ($idCentroCostos!='' ) {



        $idCentroCostosQuery=explode(",",$idCentroCostos);
        //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
        foreach ($idCentroCostosQuery as $indice => $valor) {
            $whereidCentroCostos=($whereidCentroCostos=='')? ' id = '.$valor : $whereidCentroCostos.' OR id ='.$valor;
        }

        $whereidCentroCostos=($whereidCentroCostos!='')? "AND (".$whereidCentroCostos.")" : "" ;

        $sql   = "SELECT codigo FROM centro_costos WHERE activo = 1 $whereidCentroCostos AND id_empresa = '$id_empresa'";
        $query = mysql_query($sql,$link);

        $whereConceptos = '';

        while ($row=mysql_fetch_array($query)) {
            $whereConceptos=($whereConceptos=='')? ' codigo_concepto = '.$row['codigo'] : $whereConceptos.' OR codigo_concepto ='.$row['codigo'];
        }

        $whereConceptos=($whereConceptos!='')? "AND (".$whereConceptos.")" : "" ;

        $groupBy  =($groupBy!='')? ',id_vendedor' : 'id_vendedor';
    }

    if ($sucursal!='' && $sucursal!='global') {

        $whereSucursal = ' AND VF.id_sucursal='.$sucursal;

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
        $query = mysql_query($sql,$link);
        $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
    }


    $sql= "SELECT
                VF.id,
                VF.fecha_inicio,
                VF.fecha_vencimiento,
                VF.prefijo,
                VF.numero_factura,
                VF.nit,
                VF.cliente,
                VF.total_factura,
                VF.nombre_vendedor,
                VF.codigo_centro_costo,
                VF.centro_costo,
                VF.sucursal,
                VF.sucursal_cliente,
                VF.estado
            FROM
                ventas_facturas AS VF
            WHERE
                VF.activo = 1
                AND VF.id_empresa = $id_empresa
                AND VF.estado = 1
                AND VF.id_saldo_inicial=0
                AND VF.tipo = 'Ws'
                $whereSucursal $whereClientes $whereVendedores $whereFechas
            GROUP BY
                VF.id
            ORDER BY
                VF.numero_factura,
                VF.fecha_inicio DESC";

    $query = mysql_query($sql,$link);

    $acumuladoSubtotal = 0;
    $acumuladoIva      = 0;
    $acumuladoTotal    = 0;

    $whereId        = '';
    $filaCabecera   = '';
    $arrayConceptos = array();
    while ($row=mysql_fetch_array($query)) {

        $filaCabecera = '';

        $rowsCuentas = 0;

        $sql2 = "SELECT
                    cuenta_puc,
                    descripcion_puc,
                    credito - debito AS valor,
                    codigo_concepto,
                    concepto
                FROM
                    ventas_facturas_cuentas
                WHERE
                    activo = 1
                $whereConceptos

                AND id_factura_venta = '$row[id]'";

        $query2 = mysql_query($sql2,$link);

        $rowsCuentas = mysql_num_rows($query2);

        $filasCuentas = '';

        while ($row2=mysql_fetch_array($query2)){
            $valor       = $row2['valor'];
            $codConcepto = $row2['codigo_concepto'];

            //SE ACUMULA EN UN ARRAY LOS TOTALES POR CONCEPTOS

            if($arrayConceptos[$codConcepto]['valor'] > 0 || $arrayConceptos[$codConcepto]['valor'] < 0){ $arrayConceptos[$codConcepto]['valor'] += $valor; }
            else{
                $arrayConceptos[$codConcepto]['valor']  = $valor;
                $arrayConceptos[$codConcepto]['nombre'] = $row2['concepto'];
            }

            $filasCuentas .=   '<tr>
                                    <td style ="padding-left:50px;text-align:left;width:80px">'.$row2['cuenta_puc'].'</td>
                                    <td style ="padding-left:30px;text-align:left;width:80px">'.$row2['codigo_concepto'].'</td>
                                    <td style ="text-align:left;width:320px">'.$row2['concepto'].'</td>
                                    <td style ="text-align:right;padding-right:10px;width:100px">'.validar_numero_formato(($valor*1),$IMPRIME_XLS).'</td>
                                </tr>';

            //print_r(-120000-139000);

        }

        if((!isset($checkCabecera) || $checkCabecera == 'false') && $rowsCuentas > 0){

            $filaCabecera =    '<tr>
                                    <td style ="padding-left:20px;" colspan="3"><br />FV. #'.$row['numero_factura'].' '.$row['cliente'].' '.$row['fecha_inicio'].' '.$row['fecha_vencimiento'].'</td>
                                    <td style ="text-align:right; padding-right:10px;font-weight:bold;" ><br />$'.validar_numero_formato($row['total_factura'],$IMPRIME_XLS).'</td>
                               </tr>';

        }

        $bodyTable     .= '<table class="defaultFont" style="width:550px;border-collapse: collapse;">
                                '.$filaCabecera.''.$filasCuentas.'
                           </table>';

        $rowsCuentas = 0;
        $acumuladoTotal += $row['total_factura'];

    }

    //print_r($arrayConceptos);


    $width = '550px';
    $widthConcepto = '350px';

    if($IMPRIME_PDF == true || $IMPRIME_XLS == true){
        $width = '600px';
        $widthConcepto = '400px';
    }

    $bodyConceptos = '';

    if($checkConceptos == 'true'){//MOSTRAR EL TOTAL POR CONCEPTOS

        $bodyConceptos .= '<br>
                          <div style="width:100%;font-size:13px;font-weight:bold;text-align:center;padding-bottom:15px;">Total por Conceptos</div>
                          <table class="defaultFont" style="width:'.$width.';border-collapse: collapse;" >
                                <tr class="titulos">
                                    <td style="padding-left:50px;width:100px"><b>CODIGO</b></td>
                                    <td style="padding-left:10px;'.$widthConcepto.'"><b>CONCEPTO</b></td>
                                    <td style="text-align:center;width:100px"><b>TOTAL</b></td>
                                </tr>
                          </table>';

        foreach ($arrayConceptos as $codConcepto => $arrayValor) {

            $bodyConceptos .= '<table class="defaultFont" style="width:'.$width.';border-collapse: collapse;" >
                                      <tr>
                                          <td style ="padding-left:50px;text-align:left;width:100px">'.$codConcepto.'</td>
                                          <td style ="padding-left:10px;text-align:left;width:'.$widthConcepto.'">'.$arrayValor['nombre'].'</td>
                                          <td style ="text-align:right;padding-right:10px;width:100px">'.validar_numero_formato($arrayValor['valor'],$IMPRIME_XLS).'</td>
                                      </tr>
                                </table>';
            $acumConceptos += $arrayValor['valor'];
        }

        $bodyConceptos .= '<table class="defaultFont" style="width:'.$width.';border-collapse: collapse;" >
                                <tr><td colspan="2">&nbsp;</td></tr>
                                <tr class="total">
                                    <td style="width:450px;text-align:center;">TOTAL CONCEPTOS</td>
                                    <td style="text-align:right;padding-right:10px;">$ '.validar_numero_formato($acumConceptos,$IMPRIME_XLS).'</td>
                                </tr>
                           </table>';

    }

?>
<style>
	.my_informe_Contenedor_Titulo_informe{
        float         :	left;
        width         :	100%;
        /*border-bottom :	1px solid #CCC;*/
        margin        :	0 0 10px 0;
        font-size     :	11px;
        font-family   :	Verdana, Geneva, sans-serif
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
        float     : left;
        width     : 100%;
        font-size : 16px;
        font-weight:bold;
	}
    .defaultFont{ font-size : 11px; }
    .labelResult{ font-weight:bold;font-size: 14px; }
    .labelResult2{ font-weight:bold;font-size: 12px;  width: 20%;}
    .labelResult3{ font-weight:bold;font-size: 12px; text-align: right;}



    .titulos{
        background   : #999;
        padding-left : 10px;
        /*font-size    : 11px;*/
    }

    .titulos td{
        height : 35px;
        color  :#FFF;
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

</style>


<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body >
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center;margin-bottom:15px;">
                <table align="center" style="text-align:center;" >
                    <tr ><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr  ><td  style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="font-size:13px;"><b>Informe Facturas por Cuentas</b><br> <?php echo $subtitulo_cabecera; ?><br>&nbsp;</td></tr>
                    <?php echo $datos_informe; ?>
                </table>

                <table class="defaultFont" style="width:550px;border-collapse: collapse;" >
                    <tr class="titulos">
                        <td style="padding-left:50px;width:40px"><b>CUENTA</b></td>
                        <td style="padding-left:50px;width:40px"><b>CODIGO</b></td>
                        <td style="padding-left:50px;width:320px"><b>CONCEPTO</b></td>
                        <td style="text-align:center;width:100px"><b>VALOR</b></td>
                    </tr>
                </table>

                    <?php echo $bodyTable; ?>
<?php
    if(!isset($checkCabecera) || $checkCabecera == 'false'){
?>
                <table class="defaultFont" style="width:<?php echo $width;?>;border-collapse: collapse;" >
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr class="total">
                        <td style="width:450px;text-align:center;">TOTAL VENTAS</td>
                        <td style="text-align:right;padding-right:10px;"> <?php echo '$ '.validar_numero_formato($acumuladoTotal,$IMPRIME_XLS); ?></td>
                    </tr>
                </table>

<?php
    }
?>

                <?php echo $bodyConceptos; ?>

                <div class="defaultFont" style="width:550px;border-collapse: collapse;">

                </div>
            </div>
        </div>
    </div>
    <br>
    <?php echo $cuerpoInforme.'<script>'.$script.'</script>'; ?>
</body>
<?php
    $footer='<div style="text-align:right;font-weight:bold;font-size:12px;">Pagina {PAGENO}/{nb}</div>';
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
    // if(!isset($ORIENTACION)){$ORIENTACION = 'L';}
	$ORIENTACION = 'p';
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'false';}

	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}
    else{$MS=10;$MD=10;$MI=10;$ML=10;}

	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}
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
					'P'	// L - landscape, P - portrait
				);
        // $mpdf-> debug = true;
        $mpdf->useSubstitutions = true;
        $mpdf->packTableData= true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
       // $mpdf->simpleTables = true;
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetHtmlFooter($footer);
        // $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{	$mpdf->Output($documento.".pdf",'I');}
		exit;
	}
    else{ echo $texto; }
?>