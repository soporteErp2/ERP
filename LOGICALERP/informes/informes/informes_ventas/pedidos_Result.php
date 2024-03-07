<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=informe_pedidos_".date("Y_m_d").".xls");
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
        $whereFechas=" AND TP.fecha_inicio BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";
    }else{
        $MyInformeFiltroFechaFinal=date("Y-m-d");

        $script='localStorage.MyInformeFiltroFechaInicioPedidosVenta  = "";
                 localStorage.MyInformeFiltroFechaFinalPedidosVenta = "";
                 localStorage.sucursal_pedidos_venta ="";
                 arraytercerosPV.length          = 0;
                 tercerosConfiguradosPV.length   = 0;
                 arrayvendedoresPV.length        = 0;
                 vendedoresConfiguradosPV.length = 0;';
    }

    if ($idTerceros!='' ) {

        if ($idTerceros!='todos') {
            $idTercerosQuery=explode(",",$idTerceros);
             //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
             foreach ($idTercerosQuery as $indice => $valor) {
                 $whereidTerceros=($whereidTerceros=='')? ' TP.id_cliente='.$valor : $whereidTerceros.' OR TP.id_cliente='.$valor ;
                 $whereClientes=($whereidTerceros!='')? "AND (".$whereidTerceros.")" : "" ;
            }
        }

        $groupBy  =',TP.id_cliente';
    }

    if ($idVendedores!='' ) {

        $idTercerosQuery=explode(",",$idVendedores);
         //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
         foreach ($idTercerosQuery as $indice => $valor) {
             $whereidVendedores=($whereidVendedores=='')? ' TP.id_vendedor='.$valor : $whereidVendedores.' OR TP.id_vendedor='.$valor ;
             $whereVendedores=($whereidVendedores!='')? "AND (".$whereidVendedores.")" : "" ;
        }


        $groupBy  =($groupBy!='')? ',TP.id_vendedor' : 'TP.id_vendedor';
    }

    if ($sucursal!='' && $sucursal!='global') {

            $whereSucursal = ' AND TP.id_sucursal='.$sucursal;
            //CONSULTAR EL NOMBRE DE LA SUCURSAL
            $sql="SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
            $query=mysql_query($sql,$link);
            $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';

    }
    //TP = tabla principal
    //TI = tabla Inventario
    $sql="SELECT
                    TP.fecha_inicio,
                    TP.consecutivo,
                    TP.cliente,
                    TP.nombre_vendedor,
                    TP.sucursal,
                    SUM(TI.cantidad*TI.costo_unitario) AS subtotal,
                    SUM(((TI.cantidad*TI.costo_unitario)*TI.valor_impuesto)/100) AS iva
                FROM
                    ventas_pedidos AS TP,
                    ventas_pedidos_inventario AS TI
                WHERE
                    TP.activo = 1
                AND TP.id_empresa = $id_empresa
                AND TP.estado = 1
                AND TI.id_pedido_venta=TP.id
                $whereSucursal $whereClientes $whereVendedores $whereFechas
                GROUP BY TP.id
                ORDER BY
                TP.cliente ASC
            ";
    $query=mysql_query($sql,$link);

    $acumuladoSubtotal = 0;
    $acumuladoIva      = 0;
    $acumuladoTotal   = 0;
    $style='color:#FFF';
    while ($row=mysql_fetch_array($query)) {

        $style=($style!='')? '' : 'background:#f7f7f7;' ;
        if ($row['estado']==3) {
            $style.='color:#F00A0A;font-style: italic;font-weight:bold;';
            $row['valor']=0;
        }
        $bodyTable.='<tr>
                        <td style="'.$style.'text-align:center;" >'.$row['sucursal'].'</td>
                        <td style="'.$style.'text-align:center;" >'.$row['fecha_inicio'].'</td>
                        <td style="'.$style.'text-align:center;" >'.$row['consecutivo'].'</td>
                        <td style="'.$style.'padding-left: 10px;">'.$row['cliente'].' </td>
                        <td style="'.$style.'text-align:right;">'.validar_numero_formato($row['subtotal'],$IMPRIME_XLS).'</td>
                        <td style="'.$style.'text-align:right;">'.validar_numero_formato($row['iva'],$IMPRIME_XLS).'</td>
                        <td style="'.$style.'text-align:right;" >'.validar_numero_formato(($row['subtotal']+$row['iva']),$IMPRIME_XLS).'</td>
                    </tr>';
        $acumuladoSubtotal += $row['subtotal'];
        $acumuladoIva      += $row['iva'];
        $acumuladoTotal    += $row['subtotal']+$row['iva'];
    }

?>
<style>
	.my_informe_Contenedor_Titulo_informe{
        float     :	left;
        width     :	100%;
        margin    :	0 0 10px 0;
        font-size :	11px;
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

    .table thead{
        background : #999;
    }
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
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center;margin-bottom:15px;">
                <table align="center" style="text-align:center;" >
                    <tr ><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr  ><td  style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="font-size:13px;"><b>Informe Pedidos</b><br> <?php echo $subtitulo_cabecera; ?><br>&nbsp;</td></tr>
                    <?php echo $datos_informe; ?>
                </table>
               <!--  <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa?></div>
                <div style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></div>
                <div style="margin-bottom:8px;" >A <?php echo $arrayMeses[$mes].' '.$dia.' de '.$anio;?></div> -->
                <table class="table" >
                   <thead>
                        <tr>
                            <td style="width:100px;text-align:center;"><b>SUCURSAL</b></td>
                            <td style="width:70px;text-align:center;"><b>FECHA</b></td>
                            <td style="width:80px;text-align:center;"><b>N. PEDIDO</b></td>
                            <td style="width:200px;padding-left: 10px;"><b>CLIENTE</b></td>
                            <td style="width:80px;text-align:right;"><b>SUBTOTAL</b></td>
                            <td style="width:80px;text-align:right;"><b>IVA</b></td>
                            <td style="width:80px;text-align:right;"><b>TOTAL</b></td>

                        </tr>
                    </thead>
                    <?php echo $bodyTable; ?>
                    <tr><td>&nbsp;</td></tr>
                    <tr class="total">
                        <td style="text-align:center;" colspan="4">TOTAL PEDIDOS</td>
                        <td style="text-align:right;"> <?php echo validar_numero_formato($acumuladoSubtotal,$IMPRIME_XLS); ?></td>
                        <td style="text-align:right;"> <?php echo validar_numero_formato($acumuladoIva,$IMPRIME_XLS); ?></td>
                        <td style="text-align:right;" > <?php echo validar_numero_formato($acumuladoTotal,$IMPRIME_XLS); ?></td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

 <br>
    <?php echo $cuerpoInforme; ?>


</body>
<script>
    <?php echo $script; ?>
</script>
<?php
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'false';}
	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=10;}
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
					$ORIENTACION	// L - landscape, P - portrait
				);
        // $mpdf-> debug = true;
        $mpdf->useSubstitutions = true;
        $mpdf->packTableData= true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		//$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{	$mpdf->Output($documento.".pdf",'I');}
		exit;
	}
    else{ echo $texto; }
?>