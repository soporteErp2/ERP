<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=informe_pos.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa       = $_SESSION['EMPRESA'];
    $desde            = $MyInformeFiltroFechaInicio;
    $hasta            = $MyInformeFiltroFechaFinal;
    $id_sucursal      = $MyInformeFiltro_0;
    $divTitleSucursal = '';
    $whereSucursal    = '';

    //SALDOS
    $acumuladoDebe          = 0;
    $acumuladoHaber         = 0;
    $acumuladoSaldoAnterior = 0;
    $acumuladoSaldoActual   = 0;

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
    $hoy=date("Y-m-d");

    $wherePuc = '';

$tercero="";
$acumuladoporVencer           = 0;
$acumuladounoAtreinta         = 0;
$acumuladotreintayunoAsesenta = 0;
$acumuladosesentayunoAnoventa = 0;
$acumuladomasDenoventa        = 0;

//SI NO EXISTE LA VARIABLE DE FECHA FINAL, QUIERE DECIR QUE SE ESTA GENERANDO EL INFORME DESDE LA INTERFAZ PRINCIPAL Y NO DE LA VENTANA DE CONFIGURACION
//ENTONCES MOSTRAMOS TODAS LAS FACTURAS HASTA HOY Y CON TODOS LOS CLIENTES
if (!isset($MyInformeFiltroFechaFinal) || $MyInformeFiltroFechaFinal=='') {
    $MyInformeFiltroFechaFinal=date("Y-m-d");
    $where="fecha_generado <= '$MyInformeFiltroFechaFinal' ";
    $script='   localStorage.MyInformeFiltroFechaFinalPos="";
                localStorage.MyInformeFiltroFechaInicioPos="";
                localStorage.tipo_fecha_informe_pos="";

                //VACIAR LOS ARRAY DE LOS DATOS DEL CLIENTE
                arrayConsecutivos.length=0;
                consecutivosConfigurados.length=0;
            ';
    $info='Reporte hasta '.$MyInformeFiltroFechaFinal;
}else{

    //SI SE ESTA ENVIANDO DESDE LA VENTANA DE CONFIGURAR
    if ($tipo_fecha_informe=='corte') {
        $where="fecha_generado <= '$MyInformeFiltroFechaFinal' ";
        $info='Reporte hasta '.$MyInformeFiltroFechaFinal;
    }else if($tipo_fecha_informe=='rango_fechas'){
        $where="fecha_generado BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal' ";
        $info='Reporte de '.$MyInformeFiltroFechaInicio.' Hasta '.$MyInformeFiltroFechaFinal;
    }

    //SI ES FRILTRADO POR CAJAS, CREAMOS EL WHERE PARA PASARLO AL QUERY
    $whereStringCaja='';
    $whereCaja='';

    if ($caja!='') {
        $cajaQuery=explode(",",$caja);
        //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
        foreach ($cajaQuery as $indice => $valor) {
            $whereStringCaja=($whereStringCaja=='')? 'caja='.$valor : $whereStringCaja.' OR caja='.$valor ;
        }

         $whereCaja=($whereStringCaja!='')? "AND (".$whereStringCaja.")" : "" ;
    }
}

$sqlFacturas="SELECT caja,usuario,SUM(total_pos) AS total_pos
                FROM ventas_pos
                WHERE activo=1 AND estado=1 AND id_empresa=$id_empresa
                AND $where
                $whereCaja
                GROUP BY caja,id_usuario";

$queryFacturas=mysql_query($sqlFacturas,$link);
$caja='';
$style='color:#FFF';
while ($rowFacturas=mysql_fetch_array($queryFacturas)) {
    $style=($style!='')? '' : 'background:#f7f7f7;' ;
    if ($caja!=$rowFacturas['caja']) {
        if ($caja!=0) {
            $bodyTable.='<tr class="total">
                            <td style="width:100px;"  >TOTAL CAJA  </td>
                            <td style="width:120px;text-align:right;padding-right:15px;">'.validar_numero_formato($acumuladoCaja,$IMPRIME_XLS).' </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>';
        }

        $caja=$rowFacturas['caja'];

        $bodyTable.='
                    <tr class="total">
                        <td width="80" colspan="2"><b>CAJA '.$rowFacturas['caja'].'</b></td>
                    </tr>
                    <tr>
                        <td style="'.$style.'width:100px;padding-left:15px;">'.$rowFacturas['usuario'].'</td>
                        <td style="'.$style.'width:120px;text-align:right;padding-right:15px;">'.validar_numero_formato($rowFacturas['total_pos'],$IMPRIME_XLS).' </td>
                    </tr>';
        $acumuladoCaja=0;
    }
    else{

       $bodyTable.='<tr>
                        <td style="'.$style.'width:100px;padding-left:15px;">'.$rowFacturas['usuario'].'</td>
                        <td style="'.$style.'width:120px;text-align:right;padding-right:15px;">'.validar_numero_formato($rowFacturas['total_pos'],$IMPRIME_XLS).' </td>
                    </tr>';
    }

    $acumuladoCaja+=$rowFacturas['total_pos'];
}

if ($caja!=0) {
        $bodyTable.='<tr class="total">
                        <td style="width:100px;"  >TOTAL CAJA  </td>
                        <td style="width:120px;text-align:right;padding-right:15px;">'.validar_numero_formato($acumuladoCaja,$IMPRIME_XLS).' </td>
                    </tr>';

}


?>
<style>
	.contenedor_informe, .contenedor_titulo_informe{
        width         :	100%;
        /*border-bottom :	1px solid #CCC; */
        margin        :	0 0 10px 0;
        font-size     :	11px;
        font-family   :	Verdana, Geneva, sans-serif
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
        float     :	left;
        width     :	100%;
        font-size :	16px;
        font-weight:bold;
	}

    /*.table{ font-size : 11px; width: 100%; }*/
    /*.table thead td {border-bottom: 1px solid; border-top: 1px solid;}*/

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
    <div class="contenedor_titulo_informe">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center;margin-bottom:10px;">
                <table align="center" style="text-align:center;" >
                    <tr ><td class="titulo_informe_empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr  ><td  style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></td></tr>
                    <tr><td style="font-size:11px; text-align:center;" ><?php echo $info; ?></td></tr>
                    <tr><td style="font-size:11px; text-align:center;" >Impreso: <?php echo fecha_larga_hora_m(date('Y-m-d H:i:s')); ?></td></tr>
                </table>

            </div>
        </div>

    </div>


        <table class="table"  cellspacing="0" >
            <thead>
                <tr>
                    <td style="font-weight:bold;width:100px;padding-left:15px;">USUARIO</td>
                    <td style="font-weight:bold;width:120px;text-align:right;padding-right:15px;">TOTAL VENDIDO </td>
                </tr>
            </thead>
             <?php echo $bodyTable; ?>
        </table>

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
  //       $mpdf->useSubstitutions = true;
  //       $mpdf->simpleTables = true;
  //       $mpdf->packTableData= true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetTitle ( $documento );
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
        $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{	$mpdf->Output($documento.".pdf",'I');}
		exit;
	}
    else{ echo $texto; }


    function calculaDias($fecha_inicio,$fecha_vencimiento){

        // $diferencia  = (strtotime($fecha_inicio)-strtotime($fecha_vencimiento))/86400;
        // $diferencia   = floor($diferencia);
        // return $diferencia;
        // $diferencia   = abs($diferencia); $diferencia = floor($diferencia);


        $diferencia  = (strtotime($fecha_vencimiento)-strtotime($fecha_inicio))/86400;
        $diferencia   = abs($diferencia); $diferencia = floor($diferencia);
        return $diferencia;
        // return $dias;


    }


?>

