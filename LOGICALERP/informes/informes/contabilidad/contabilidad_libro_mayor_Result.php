<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    ob_start();

    if($IMPRIME_XLS=='true'){
      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=libro_mayor.xls");
      header("Pragma: no-cache");
      header("Expires: 0");
    }

    if(!isset($MyInformeFiltroFechaInicio) && !isset($MyInformeFiltroFechaFinal)) {
      $MyInformeFiltroFechaInicio = date('Y').'-01-01';
      $MyInformeFiltroFechaFinal  = date('Y-m-d');
    }

    $id_empresa = $_SESSION['EMPRESA'];

    $wherePuc = '';

    // FILTRO PARA MOSTRAR O NO LAS CUENTAS DE CIERRE
    if($cuentas_cierre == "no"){
      $whereCuentasCierre = " AND tipo_documento != 'NCC'";
    }

    //================================== DEBITO Y CREDITO ACUMULADO DE EL RANGO DE FECHAS ==================================//
    //DEBITO CREDITO FECHA SELECCIONADA
    $sqlAsientos = "SELECT SUM(debe) AS debe,SUM(haber) AS haber,codigo_cuenta
                        FROM asientos_colgaap

                    WHERE activo=1 AND id_empresa = '$id_empresa' $whereCuentasCierre
                        AND fecha BETWEEN ('$MyInformeFiltroFechaInicio') AND ('$MyInformeFiltroFechaFinal')
                        GROUP BY codigo_cuenta";

    $queryAsientos = mysql_query($sqlAsientos, $link);
    $contArrayWherePuc = 0;
    while ($rowAsientos = mysql_fetch_array($queryAsientos)){


        $contArrayWherePuc++;
        // $arrayWherePuc[$contArrayWherePuc]=$rowAsientos['codigo_cuenta'];
        $arrayWherePuc[$contArrayWherePuc]=substr($rowAsientos['codigo_cuenta'], 0, 4);

        $wherePuc .= ($wherePuc != '')? ' OR ' : '' ;

        $wherePuc .= ' cuenta = '.substr($rowAsientos['codigo_cuenta'], 0, 4);               //4 CIFRAS PUC

        //ArrayAsientos EN FECHAS SELECCIONADAS
        $arrayAsientoActual[substr($rowAsientos['codigo_cuenta'], 0, 4)]['debe'] += $rowAsientos['debe'];
        $arrayAsientoActual[substr($rowAsientos['codigo_cuenta'], 0, 4)]['haber']+= $rowAsientos['haber'];

    }

    //================================== SALDO ANTERIOR DEL PERIODO SELECCIONADO =========================================//
    //SUM((SUM(debe)) - (SUM(haber)))
    $sqlAsientosAnterior = "SELECT SUM(debe-haber) AS saldo,codigo_cuenta
                                FROM asientos_colgaap
                            WHERE activo=1 AND id_empresa = '$id_empresa' $whereCuentasCierre
                                AND fecha < '$MyInformeFiltroFechaInicio'
                                GROUP BY codigo_cuenta";
    $queryAsientosAnterior = mysql_query($sqlAsientosAnterior,$link);
    while($rowAsientosAnterior = mysql_fetch_array($queryAsientosAnterior)){


        $wherePuc .= ($wherePuc != '')? ' OR ' : '' ;

        $wherePuc .= ' cuenta = '.substr($rowAsientosAnterior['codigo_cuenta'], 0, 4);               //4 CIFRAS PUC

        $contArrayWherePuc++;
        $arrayWherePuc[$contArrayWherePuc]=substr($rowAsientosAnterior['codigo_cuenta'], 0, 4);

        //ArrayAsientos EN FECHA ANTERIOR
        $arrayAsientoAnterior[substr($rowAsientosAnterior['codigo_cuenta'], 0, 4)] = $rowAsientosAnterior['saldo'];
        $arrayAsientoNewSaldo[substr($rowAsientosAnterior['codigo_cuenta'], 0, 4)] = $arrayAsientoNewSaldo[substr($rowAsientosAnterior['codigo_cuenta'], 0, 4)] + (($rowAsientosAnterior['saldo']+$arrayAsientoActual[substr($rowAsientosAnterior['codigo_cuenta'], 0, 4)]['debe'])-$arrayAsientoActual[substr($rowAsientosAnterior['codigo_cuenta'], 0, 4)]['haber']);

    }

    $arrayWherePuc = array_unique($arrayWherePuc);

    //======================================= CONSULTAR EL PUC PARA LOS NOMBRES DE LAS CUENTAS ============================//
    $sqlCuentas =  "SELECT cuenta,descripcion
                    FROM puc
                    WHERE activo=1 AND id_empresa = '$id_empresa' AND ($wherePuc)
                    GROUP BY cuenta";

    //======================================== ARMAR EL CUERPO DE LAS VENTAS ===============================================//
    $clase=0;
    $arrayClases[1]='ACTIVO';
    $arrayClases[2]='PASIVO';
    $arrayClases[3]='PATRIMONIO';
    $arrayClases[4]='INGRESO';
    $arrayClases[5]='GASTOS';
    $arrayClases[6]='COSTO DE VENTA';
    $arrayClases[7]='COSTO DE PRODUCCION O DE OPERACION';
    $arrayClases[8]='CUENTAS DE ORDEN DEUDORAS';
    $arrayClases[9]='CUENTAS DE ORDEN ACREEDORA';

    //ACUMULADO DE LOS SALDOS
    $acumuladoSaldoAnterior = 0;
    $acumuladoDebe          = 0;
    $acumuladoHaber         = 0;
    $acumuladoSaldoActual   = 0;

    $queryCuentas = mysql_query($sqlCuentas,$link);
    while($rowCuentas = mysql_fetch_array($queryCuentas)){

        if ($clase!=substr($rowCuentas['cuenta'], 0, 1)) {
            if ($clase!=0 ) {
                $bodyTable .=  '<tr >
                                <td colspan="2" style="border-top:1px solid;"><b>TOTAL '.$arrayClases[$clase].'</b></td>
                                <td style="text-align:right;border-top:1px solid;">'.validar_numero_formato($acumuladoSaldoAnterior,$IMPRIME_XLS).'</td>
                                <td style="text-align:right;border-top:1px solid;">'.validar_numero_formato($acumuladoDebe,$IMPRIME_XLS).'</td>
                                <td style="text-align:right;border-top:1px solid;">'.validar_numero_formato($acumuladoHaber,$IMPRIME_XLS).'</td>
                                <td style="text-align:right;border-top:1px solid;">'.validar_numero_formato($acumuladoSaldoActual,$IMPRIME_XLS).'</td>
                                </tr>
                                <tr>
                                <td>&nbsp;</td>
                                </tr>';

                $acumuladoSaldoAnterior = 0;
                $acumuladoDebe          = 0;
                $acumuladoHaber         = 0;
                $acumuladoSaldoActual   = 0;

            }

            $clase=substr($rowCuentas['cuenta'], 0, 1);
            $bodyTable .=  '<tr>
                            <td><b>'.$clase.'</b></td>
                            <td colspan="5"><b>'.$arrayClases[$clase].'</b></td>
                            </tr>';
        }
        $bodyTable .=  '<tr>
                            <td  width="65">'.$rowCuentas['cuenta'].'</td>
                            <td  width="340">'.$rowCuentas['descripcion'].'</td>
                            <td  width="80" style="text-align:right;"> '.validar_numero_formato($arrayAsientoAnterior[$rowCuentas['cuenta']],$IMPRIME_XLS).'</td>
                            <td  width="80" style="text-align:right;"> '.validar_numero_formato($arrayAsientoActual[$rowCuentas['cuenta']]['debe'],$IMPRIME_XLS).'</td>
                            <td  width="80" style="text-align:right;"> '.validar_numero_formato($arrayAsientoActual[$rowCuentas['cuenta']]['haber'],$IMPRIME_XLS).'</td>
                            <td  width="80" style="text-align:right;"> '.validar_numero_formato($arrayAsientoAnterior[$rowCuentas['cuenta']]+($arrayAsientoActual[$rowCuentas['cuenta']]['debe']-$arrayAsientoActual[$rowCuentas['cuenta']]['haber']),$IMPRIME_XLS).'</td>
                        </tr>';

        $acumuladoSaldoAnterior += $arrayAsientoAnterior[$rowCuentas['cuenta']];
        $acumuladoDebe          += $arrayAsientoActual[$rowCuentas['cuenta']]['debe'];
        $acumuladoHaber         += $arrayAsientoActual[$rowCuentas['cuenta']]['haber'];
        $acumuladoSaldoActual   += $arrayAsientoAnterior[$rowCuentas['cuenta']]+($arrayAsientoActual[$rowCuentas['cuenta']]['debe']-$arrayAsientoActual[$rowCuentas['cuenta']]['haber']);
        
    }//FIN WHILE

    if ($clase!=0 ) {
        $bodyTable .=  '<tr >
                        <td colspan="2" style="border-top:1px solid;"><b>TOTAL '.$arrayClases[$clase].'</b></td>
                        <td style="text-align:right;border-top:1px solid;">'.validar_numero_formato($acumuladoSaldoAnterior,$IMPRIME_XLS).'</td>
                        <td style="text-align:right;border-top:1px solid;">'.validar_numero_formato($acumuladoDebe,$IMPRIME_XLS).'</td>
                        <td style="text-align:right;border-top:1px solid;">'.validar_numero_formato($acumuladoHaber,$IMPRIME_XLS).'</td>
                        <td style="text-align:right;border-top:1px solid;">'.validar_numero_formato($acumuladoSaldoActual,$IMPRIME_XLS).'</td>
                        </tr>';
        $acumuladoSaldoAnterior = 0;
        $acumuladoDebe          = 0;
        $acumuladoHaber         = 0;
        $acumuladoSaldoActual   = 0;
    }
?>
<style>
  .my_informe_Contenedor_Titulo_informe{
    float         : left;
    width         : 100%;
    border-bottom : 1px solid #CCC;
    margin        : 0 0 10px 0;
    font-size     : 11px;
    font-family   : Verdana, Geneva, sans-serif
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
  .my_informe_Contenedor_Titulo_informe td{
    padding-left: 2px;
  }
</style>
<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->
<body>
  <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
    <div style="float:left; width:100%">
      <div style="float:left;width:100%; text-align:center">
        <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $_SESSION['NOMBREEMPRESA'];?><br><spam style="font-size:12px;">Nit.<?php echo $_SESSION['NITEMPRESA']; ?></spam></div>
        <div style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></div>
        <div style="margin-bottom:8px;" >Periodo del <?php echo $MyInformeFiltroFechaInicio; ?> al <?php echo $MyInformeFiltroFechaFinal; ?></div>
      </div>
    </div>
  </div>
  <div class="my_informe_Contenedor_Titulo_informe">
    <table class="defaultFont">
      <tr>
        <td width="65"  style="font-size:11px;font-weight:bold;" >CUENTA</td>
        <td width="340" style="font-size:11px;font-weight:bold;" >DESCRIPCION</td>
        <td width="80"  style="text-align:right;font-size:11px;font-weight:bold;"> SALDO ANTERIOR</td>
        <td width="80"  style="text-align:right;font-size:11px;font-weight:bold;"> DEBITO</td>
        <td width="80"  style="text-align:right;font-size:11px;font-weight:bold;"> CREDITO</td>
        <td width="80"  style="text-align:right;font-size:11px;font-weight:bold;"> SALDO ACTUAL</td>
      </tr>
      <?php echo $bodyTable; ?>
    </table>
    <br>
  </div>
</body>
<script>
  <?php echo $script; ?>
</script>
<?php
  $texto = ob_get_contents(); ob_end_clean();

  if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
  if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
  if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
  if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'fal87se';}
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
    $mpdf->useSubstitutions = true;
    $mpdf->packTableData = true;
    $mpdf->SetAutoPageBreak(TRUE,15);
    $mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO'] . " // " . $_SESSION['NOMBREEMPRESA']);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetHeader("");
    $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
    $mpdf->WriteHTML(utf8_encode($texto));
    if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{ $mpdf->Output($documento.".pdf",'I');}
    exit;
  }
  else{
    echo $texto;
  }
?>
