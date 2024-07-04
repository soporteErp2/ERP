<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=informe_vacaciones_".date("Y-m-d_h-i-s").".xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa = $_SESSION['EMPRESA'];
    $arrayEmpleadosJSON = json_decode($arrayEmpleadosJSON);

    // FILTRO POR EMPLEADOS
    if (!empty($arrayEmpleadosJSON)) {
        foreach ($arrayEmpleadosJSON as $indice => $id_empleado) {
            $whereEmpleados .= ($whereEmpleados=='')? ' VC.id_empleado='.$id_empleado : ' OR VC.id_empleado='.$id_empleado;
        }
        $whereEmpleados   = " AND (".$whereEmpleados.")";
    }

    if ($detalle<>'todos') {
        $whereFecha = " AND VC.fecha_inicio_periodo_vacaciones='$MyInformeFiltroFechaInicio' VC.fecha_final_periodo_vacaciones='$MyInformeFiltroFechaFinal' ";
    }

    $whereSucursal = ($sucursal<>'global')? " AND EC.id_sucursal=$sucursal " : "" ;

    // CONSULTAR LAS VACACIONES
    $sql="SELECT
                VC.documento_empleado,
                VC.nombre_empleado,
                VC.fecha_inicio_periodo_vacaciones,
                VC.fecha_final_periodo_vacaciones,
                VC.fecha_inicio_vacaciones_disfrutadas,
                VC.fecha_fin_vacaciones_disfrutadas,
                VC.dias_vacaciones_disfrutadas,
                VC.tipo_base,
                VC.base,
                VC.valor_vacaciones_disfrutadas,
                VC.dias_vacaciones_compensadas,
                VC.valor_vacaciones_compensadas,
                VC.fecha_inicio_labores,
                EC.numero_contrato,
                EC.tipo_contrato
            FROM nomina_vacaciones_empleados AS VC LEFT JOIN empleados_contratos AS EC ON EC.id=VC.id_contrato
            WHERE VC.activo=1 AND VC.id_empresa=$id_empresa AND VC.estado=1 $whereFecha $whereEmpleados $whereSucursal";
    $query=$mysql->query($sql,$mysql->link);
    $style='background:#EEE;"';
    while($row=$mysql->fetch_array($query)) {
        $style=($style!='')? '' : 'background:#F7F7F7;' ;
        $bodyTable .="<tr class='filaConcepto'>
                        <td style='$style ' >$row[documento_empleado] </td>
                        <td style='$style ' >$row[nombre_empleado] </td>
                        <td style='$style text-align:center'>$row[numero_contrato] </td>
                        <td style='$style text-align:center'>$row[fecha_inicio_periodo_vacaciones] </td>
                        <td style='$style text-align:center'>$row[fecha_final_periodo_vacaciones] </td>
                        <td style='$style text-align:center'>$row[fecha_inicio_vacaciones_disfrutadas] </td>
                        <td style='$style text-align:center'>$row[fecha_fin_vacaciones_disfrutadas] </td>
                        <td style='$style text-align:center'>$row[fecha_inicio_labores] </td>
                        <td style='$style text-align: right;' >$row[dias_vacaciones_disfrutadas] </td>
                        <td style='$style text-align: right;' >$row[dias_vacaciones_compensadas] </td>
                    </tr>";
        $dias_disfrute    += $row['dias_vacaciones_disfrutadas'];
        $dias_compensadas += $row['dias_vacaciones_compensadas'];
    }

?>

<style>
	.contenedor_informe, .contenedor_titulo_informe{
        width         :	100%;
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

    .table{ font-size : 11px; width: 100%; margin-top: 20px;border-collapse: collapse;}
    /*.table thead td {border-bottom: 1px solid; border-top: 1px solid;}*/
    .table td{padding-right: 10px;}

    .empleado_nomina{
        background: #999;
        color: #FFF;
        padding-left: 10px;
        height: 25px;
        font-size : 12px;
    }

    .titulos_conceptos_empleados{
        height: 25px;
        background: #EEE;
        /*font-weight: bold;*/
        color: #8E8E8E;
    }

    .titulos_conceptos_empleados td,.titulos_totales_empleados td{
        border-top: 1px solid #999;
        border-bottom: 1px solid #999;
        background: #EEE;
        padding-left: 10px;
    }

    .titulos_totales_empleados{
        height: 25px;
        font-weight: bold;
        font-size: 12px;
        color: #8E8E8E;
    }

    .filaConcepto{
        /*border: 1px solid #EEE;*/
        height: 22px;
    }
</style>
<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->
<body>
    <div class="contenedor_titulo_informe">
        <div style=" width:100%">
            <div style="width:100%; text-align:center">
                <table align="center" style="text-align:center;" >
                    <tr ><td class="titulo_informe_empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr  ><td  style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;text-transform: uppercase;">Informe de vacaciones</td></tr>
                    <tr><td  style="font-size:11px;text-align:center;"><?php echo $subtitulo_cabecera; ?></td></tr>
                    <!--<tr><td style="font-size:11px; text-align:center;" >Impreso: <?php echo fecha_larga_hora_m(date('Y-m-d H:i:s')); ?></td></tr>-->
                </table>

            </div>
        </div>
    </div>
        <table class="table"  cellspacing="0">
            <tr class="titulos_conceptos_empleados">
                <td>DOCUMENTO</td>
                <td>EMPLEADO</td>
                <td>N. CONTRATO</td>
                <td colspan ="2">PERIODO VACACIONES</td>
                <td colspan ="2">PERIODO DISFRUTADO</td>
                <td>INICIO LABORES</td>
                <td>DIAS DESCANSO</td>
                <td>DIAS COMPENSADOS</td>
            </tr>
             <?php echo $bodyTable; ?>
             <tr class="titulos_conceptos_empleados">
                <td colspan="8">TOTALES</td>
                <td style='text-align: right;'><?php echo $dias_disfrute ?></td>
                <td style='text-align: right;'><?php echo $dias_compensadas ?></td>
            </tr>
        </table>
</body>
<script>
    <?php echo $script; ?>
</script>
<?php
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER-L';}
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


?>

