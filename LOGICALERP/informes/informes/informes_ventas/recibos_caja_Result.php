<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    ob_start();

    if($IMPRIME_XLS=='true'){
      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=recibo_caja_".date("Y_m_d").".xls");
      header("Pragma: no-cache");
      header("Expires: 0");
    }

    $id_empresa         = $_SESSION['EMPRESA'];
    $desde              = $MyInformeFiltroFechaInicio;
    $hasta              = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;
    $divTitleSucursal   = '';
    $whereSucursal      = '';
    $subtitulo_cabecera = '';
    $arraytercerosJSON  = json_decode($arraytercerosJSON);

    if (isset($MyInformeFiltroFechaFinal) && $MyInformeFiltroFechaFinal!='') {
      $whereFechas=" AND RC.fecha_recibo BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";
    }else{
      $MyInformeFiltroFechaFinal=date("Y-m-d");
    }

    if (!empty($arraytercerosJSON)) {
      foreach ($arraytercerosJSON as $indice => $id_tercero) {
        $whereTerceros .= ($whereTerceros=='')? ' RC.id_tercero='.$id_tercero : ' OR RC.id_tercero='.$id_tercero;
      }
      $whereTerceros   = " AND (".$whereTerceros.")";
      $groupBy  =',RC.id_tercero';
    }

    if ($software<>'global') {
      $whereTipo = ($software=='SIHO')? ' AND RC.tipo="Ws" ' : '  AND RC.tipo="" ' ;
      $subtitulo_cabecera = ($software=='SIHO')? ' Sincronizados de SIHO <br> ' : ' Realizados en ERP <br> ' ;
    }

    if($sucursal != '' && $sucursal != 'global'){
      $whereSucursal = ' AND RC.id_sucursal='.$sucursal;
      $sql="SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
      $query=mysql_query($sql,$link);
      $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
    }

    $sql = "SELECT
              RC.fecha_recibo,
              RC.nit_tercero,
              RC.consecutivo,
              RC.tercero,
              RC.sucursal,
              RC.estado,
              SUM(AC.debe) AS valor
            FROM
              recibo_caja AS RC
            LEFT JOIN
              asientos_colgaap AS AC ON (AC.id_documento=RC.id AND AC.tipo_documento='RC')
            WHERE
              RC.activo = 1
            AND
              (RC.estado = 1 OR RC.estado=3)
            AND
              RC.id_empresa = $id_empresa
            $whereSucursal
            $whereTerceros
            $whereFechas
            $whereTipo
            GROUP BY
              RC.id
            ORDER BY
              RC.consecutivo DESC";

    $query=mysql_query($sql,$link);

    $style='color:#FFF';

    while ($row=mysql_fetch_array($query)) {
      $acumulado+=$row['valor'];

      $style=($style!='')? '' : 'background:#f7f7f7;' ;

      if ($row['estado'] == 3) {
        $style .= 'color:#F00A0A;font-style: italic;font-weight:bold;';
        $row['valor'] = 0;
      }

      $bodyTable .='<tr>
                      <td style="'.$style.'text-align:center;" >'.$row['sucursal'].'</td>
                      <td style="'.$style.'text-align:center;" >'.$row['fecha_recibo'].'</td>
                      <td style="'.$style.'text-align:center;" >'.$row['consecutivo'].'</td>
                      <td style="'.$style.'text-align:center;" >'.$row['nit_tercero'].'</td>
                      <td style="'.$style.'padding-left: 10px;">'.$row['tercero'].'</td>
                      <td style="'.$style.'text-align:right;padding-right:8px;" >'.validar_numero_formato($row['valor'],$IMPRIME_XLS).'</td>
                    </tr>';
    }

?>
<style>
    .my_informe_Contenedor_Titulo_informe{
      float         : left;
      width         : 100%;
      margin        : 0 0 10px 0;
      font-size     : 11px;
    }
    .my_informe_Contenedor_Titulo_informe_label{
      float       : left;
      width       : 130px;
      font-weight : bold;
    }
    .my_informe_Contenedor_Titulo_informe_detalle{
      float         : left;
      width         : 210px;
      padding       : 0 0 0 5px;
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
    .defaultFont{
      font-size : 11px;
    }
    .labelResult{
      font-weight : bold;
      font-size   : 14px;
    }
    .labelResult2{
      font-weight :bold;
      font-size   : 12px;
      width       : 20%;
    }
    .labelResult3{
      font-weight :bold;
      font-size   : 12px;
      text-align  : right;
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

<!-- *************************** DESARROLLO DEL INFORME ****************************************** -->
<!-- ********************************************************************************************* -->

<body >
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center;margin-bottom:15px;">
                <table align="center" style="text-align:center;" >
                    <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr><td style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="font-size:13px;"><b>Recibos de Caja</b><br> <?php echo $subtitulo_cabecera; ?><br>&nbsp;</td></tr>
                    <?php echo $datos_informe; ?>
                </table>
               <!--  <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa?></div>
                <div style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></div>
                <div style="margin-bottom:8px;" >A <?php echo $arrayMeses[$mes].' '.$dia.' de '.$anio;?></div> -->
                <table class="table">
                  <thead>
                    <tr>
                      <td style="width:100px;text-align:center;"><b>SUCURSAL</b></td>
                      <td style="width:70px;text-align:center;"><b>FECHA</b></td>
                      <td style="width:40px;text-align:center;"><b>CONSECUTIVO</b></td>
                      <td style="width:90px;text-align:center;"><b>NIT</b></td>
                      <td style="padding-left: 10px;"><b>TERCERO</b></td>
                      <td style="width:90px;text-align:right;padding-right:8px;"><b>VALOR</b></td>
                    </tr>
                  </thead>
                  <?php echo $bodyTable; ?>
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr class="total">
                    <td colspan="5" style="text-align:center;"><b>TOTAL RECIBOS DE CAJA:</b></td>
                    <td style="text-align:right;padding-right:8px;"><?php echo validar_numero_formato($acumulado,$IMPRIME_XLS) ?></td>
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
                    'utf-8',        // mode - default ''
                    $HOJA,          // format - A4, for example, default ''
                    12,             // font size - default 0
                    '',             // default font family
                    $MI,            // margin_left
                    $MD,            // margin right
                    $MS,            // margin top
                    $ML,            // margin bottom
                    10,             // margin header
                    10,             // margin footer
                    $ORIENTACION    // L - landscape, P - portrait
                );
        // $mpdf-> debug = true;
        $mpdf->useSubstitutions = true;
        $mpdf->packTableData= true;
        $mpdf->SetAutoPageBreak(TRUE, 15);
        $mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
        $mpdf->SetDisplayMode ( 'fullpage' );
        $mpdf->SetHeader("");
        $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
        $mpdf->WriteHTML(utf8_encode($texto));
        if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{ $mpdf->Output($documento.".pdf",'I');}
        exit;
    }
    else{ echo $texto; }
?>
