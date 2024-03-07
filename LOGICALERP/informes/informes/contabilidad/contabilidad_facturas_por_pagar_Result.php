<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();

  if($IMPRIME_XLS == 'true'){
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Facturas_Por_Pagar.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
  }

  $id_empresa                   = $_SESSION['EMPRESA'];
  $hasta                        = $MyInformeFiltroFechaFinal;
  $whereFecha                   = '';
  $whereSucursal                = '';
  $hoy                          = date("Y-m-d");
  $tercero                      = "";
  $acumuladoporVencer           = 0;
  $acumuladounoAtreinta         = 0;
  $acumuladotreintayunoAsesenta = 0;
  $acumuladosesentayunoAnoventa = 0;
  $acumuladomasDenoventa        = 0;

  if($sucursal != '' && $sucursal != 'global'){
    $whereSucursal = ' AND CF.id_sucursal =' . $sucursal;

    //CONSULTAR EL NOMBRE DE LA SUCURSAL
    $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa = $id_empresa AND id = ".$sucursal;
    $query = mysql_query($sql,$link);
    $subtitulo .= '<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';
  }

  //SI NO EXISTE LA VARIABLE DE FECHA FINAL, QUIERE DECIR QUE SE ESTA GENERANDO EL INFORME DESDE LA INTERFAZ PRINCIPAL Y NO DE LA VENTANA DE CONFIGURACION
  //ENTONCES MOSTRAMOS TODAS LAS FACTURAS HASTA HOY Y CON TODOS LOS CLIENTES
  if(!isset($MyInformeFiltroFechaFinal) || $MyInformeFiltroFechaFinal == ''){
    $MyInformeFiltroFechaFinal = date("Y-m-d");
    $whereFecha = "AND A.fecha <= '$MyInformeFiltroFechaFinal' ";
    $script = ' localStorage.plazo_por_vencer_facturas_pagar           = "";
                localStorage.vencido_1_30_facturas_pagar               = "";
                localStorage.vencido_31_60_facturas_pagar              = "";
                localStorage.vencido_61_90_facturas_pagar              = "";
                localStorage.vencido_mas_90_facturas_pagar             = "";
                localStorage.MyInformeFiltroFechaFinal_facturas_pagar  = "";
                localStorage.MyInformeFiltroFechaInicio_facturas_pagar = "";
                localStorage.tipo_fecha_informe_facturas_pagar         = "";
                localStorage.tipo_informe_facturas_por_pagar           = "";
                localStorage.sucursal_facturas_por_pagar               = "";

                //VACIAR LOS ARRAY DE LOS DATOS DEL CLIENTE
                arrayProveedores.length = 0;
                proveedoresConfigurados.length = 0;';

    $subtitulo.='Corte a '.$MyInformeFiltroFechaFinal;
  }
  else{
    //SI SE ESTA ENVIANDO DESDE LA VENTANA DE CONFIGURAR
    if($tipo_fecha_informe == 'corte'){
      $whereFecha  = "AND A.fecha <= '$MyInformeFiltroFechaFinal' ";
      $subtitulo  .= 'Corte a '.$MyInformeFiltroFechaFinal;
    }
    else if($tipo_fecha_informe == 'rango_fechas'){
      $whereFecha  = "AND A.fecha BETWEEN '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal' ";
      $subtitulo  .= 'Desde '.$MyInformeFiltroFechaInicio.' Hasta '.$MyInformeFiltroFechaFinal;
    }

    //SI ES FRILTRADO POR CLIENTES, CREAMOS EL WHERE PARA PASARLO AL QUERY
    $whereIdClientes = '';
    $whereClientes   = '';

    if($idClientes != ''){
      $idClientesQuery = explode(",",$idClientes);

      //RECORREMOS EL ARRAY CON LOS ID PARA ARMAR EL WHERE
      foreach($idClientesQuery as $indice => $valor){
        $whereIdClientes = ($whereIdClientes == '')? 'CF.id_proveedor='.$valor : $whereIdClientes.' OR CF.id_proveedor='.$valor;
      }

      $whereClientes = ($whereIdClientes != '')? "AND (".$whereIdClientes.")" : "";
    }

    //SI ES FILTRADO POR LOS CHECKBOX DE PLAZOS DE LA FECHA
    $wherePlazos = '';
    if($sqlCheckbox != ''){
      $wherePlazos = "AND ($sqlCheckbox) ";
    }
  }

  //CONSUTA LAS CUENTAS DE PAGO CREDITO
  if($cuenta != ''){
    $cuenta      = substr($cuenta, 0, -1);
    $arrayCuenta = explode(",", $cuenta);

    foreach($arrayCuenta as $key => $cuenta_pago){
      $whereCuentas .= "A.codigo_cuenta=$cuenta_pago OR ";
    }

    $whereCuentas = substr($whereCuentas, 0, -3);
  }
  else{
    $whereCuentas    = "";
    $sqlCuentasPago  = "SELECT cuenta FROM configuracion_cuentas_pago
                        WHERE id_empresa = $id_empresa
                        AND activo = 1
                        AND tipo = 'Compra'
                        AND estado = 'Credito'";
    $queryCuentasPago = mysql_query($sqlCuentasPago,$link);
    while($rowCuenta = mysql_fetch_assoc($queryCuentasPago)){
      $whereCuentas .= " OR A.codigo_cuenta=$rowCuenta[cuenta]";
    }

    $whereCuentas = substr($whereCuentas, 3);
  }

  if($hasta == ''){
    $hasta = $hoy;
  }

  //FILTRO ORDENAMIENTO POR CAMPO
  if($ordenCampo == "consecutivo"){
    $orderBy = ", CF.consecutivo";
  }
  else if($ordenCampo == "facturaProveedor"){
    $orderBy = ", CONCAT(CF.prefijo_factura,CF.numero_factura)";
  }
  else if($ordenCampo == "fecha"){
    $orderBy = ", CF.fecha_final";
  }

  //FILTRO ORDENAMIENTO POR FLUJO
  if($ordenFlujo == "ascendente"){
    $orderBy .= " ASC";
  }
  else if($ordenFlujo == "descendente"){
    $orderBy .= " DESC";
  }

  $nombreTempo = "asientosTempoCompra$_SESSION[ID_HOST]";

  $sqlTempoTable = "CREATE TEMPORARY TABLE $nombreTempo
                    SELECT SUM(A.haber - A.debe) AS saldo,A.id_documento_cruce,A.codigo_cuenta,A.activo,A.fecha,A.tipo_documento_cruce,A.id_empresa
                    FROM asientos_colgaap AS A
                    INNER JOIN compras_facturas AS CF ON (
                      A.id_documento_cruce = CF.id
                      AND A.codigo_cuenta = CF.cuenta_pago
                    )
                    WHERE A.activo = 1
                    $whereFecha
                    $whereSucursal
                    $whereClientes
                    $wherePlazos
                    AND ($whereCuentas)
                    AND A.tipo_documento_cruce = 'FC'
                    AND A.id_empresa = $id_empresa
                    GROUP BY A.id_documento_cruce
                    HAVING saldo > 0";
  $queryTempoTable = mysql_query($sqlTempoTable,$link);

  $sqlFacturas = "SELECT
                    T.telefono1,
                    T.celular1,
                    CF.cuenta_pago AS codigo_cuenta,
                    DATEDIFF('$hasta',CF.fecha_final) AS dias,
                    CF.id,
                    CF.id_proveedor,
                    CF.nit,
                    CF.proveedor,
                    CF.fecha_inicio,
                    CF.fecha_final,
                    CF.consecutivo,
                    CF.prefijo_factura,
                    CF.numero_factura,
                    CF.sucursal,
                    A.saldo
                  FROM
                    $nombreTempo AS A
                  INNER JOIN compras_facturas AS CF ON A.id_documento_cruce = CF.id
                  LEFT JOIN terceros AS T ON CF.id_proveedor = T.id
                  ORDER BY
                    CF.id_proveedor $orderBy";

  $queryFacturas = mysql_query($sqlFacturas,$link);

  $sqlTempoTable = "DROP TEMPORARY TABLE $nombreTempo";
  $queryTempoTable = mysql_query($sqlTempoTable,$link);

  if($tipo_informe == '' || !isset($tipo_informe)){
    $tipo_informe = 'detallado';
  }

  $acumuladoporVencerTotal           = 0;
  $acumuladounoAtreintaTotal         = 0;
  $acumuladotreintayunoAsesentaTotal = 0;
  $acumuladosesentayunoAnoventaTotal = 0;
  $acumuladomasDenoventaTotal        = 0;

  $colspanCliente = ($IMPRIME_XLS == 'true')? 'colspan="9"' : 'colspan="7"';
  $colspanCartera = ($IMPRIME_XLS == 'true')? 'colspan="10"' : 'colspan="8"';

  $style = 'color:#FFF';
  while($rowFacturas = mysql_fetch_array($queryFacturas)){
    $dias      = $rowFacturas['dias'];
    $telefono  = ' Telefono: '.$rowFacturas['telefono1'];
    $telefono .= ($rowFacturas['telefono1']!='' && $rowFacturas['celular1'] != '')? ' - Celular: ' . $rowFacturas['celular1'] : $rowFacturas['celular1'];

    $porVencer           = ($dias < 1)? $rowFacturas['saldo']: '&nbsp;';
    $unoAtreinta         = ($dias > 0 && $dias <= 30)? $rowFacturas['saldo'] : '&nbsp;';
    $treintayunoAsesenta = ($dias > 30 && $dias <= 60)? $rowFacturas['saldo'] : '&nbsp;';
    $sesentayunoAnoventa = ($dias > 60 && $dias <= 90)? $rowFacturas['saldo'] : '&nbsp;';
    $masDenoventa        = ($dias > 90)? $rowFacturas['saldo'] : '&nbsp;';

    $style  = ($style != '')? '' : 'background:#f7f7f7;';
    $campos = ($IMPRIME_XLS == 'true')?  '<td>'.$rowFacturas['sucursal'].'</td>
                                          <td>"'.$rowFacturas['nit'].'"</td>
                                          <td>'.$rowFacturas['proveedor'].'</td>'
                                          : '<td>'.$rowFacturas['sucursal'].'</td>';

    if($tercero != $rowFacturas['id_proveedor']){
      if($tercero != 0){
        $bodyTable .= ($tipo_informe != 'totalizado_edades')?  '<tr class="total">
                                                                  <td '.$colspanCliente.'>TOTAL PROVEEDOR</td>
                                                                  <td style="text-align:right;">'.number_format($acumuladoSaldo,2,',','.').'</td>
                                                                  <td style="text-align:right;">'.number_format($acumuladoporVencer,2,',','.').'</td>
                                                                  <td style="text-align:right;">'.number_format($acumuladounoAtreinta,2,',','.').'</td>
                                                                  <td style="text-align:right;">'.number_format($acumuladotreintayunoAsesenta,2,',','.').'</td>
                                                                  <td style="text-align:right;">'.number_format($acumuladosesentayunoAnoventa,2,',','.').'</td>
                                                                  <td style="text-align:right;">'.number_format($acumuladomasDenoventa,2,',','.').'</td>
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

      $tercero    = $rowFacturas['id_proveedor'];
      $bodyTable .= ($tipo_informe != 'totalizado_edades')?  '<tr class="total">
                                                                <td width="80" colspan="15"><b>'.$rowFacturas['proveedor'].'</b></td>
                                                              </tr>': '';
      $bodyTable .= ($tipo_informe == 'detallado')?  '<tr>
                                                        '.$campos.'
                                                        <td style="'.$style.'width:80px;" >'.$rowFacturas['codigo_cuenta'].'</td>
                                                        <td style="'.$style.'width:60px;text-align:right;">'.$rowFacturas['consecutivo'].'</td>
                                                        <td style="'.$style.'width:100px;text-align:right;">'.$rowFacturas['prefijo_factura'].' '.$rowFacturas['numero_factura'].' </td>
                                                        <td style="'.$style.'width:100px;text-align:right;">'.$rowFacturas['fecha_inicio'].' </td>
                                                        <td style="'.$style.'width:80px;text-align:right;">'.$rowFacturas['fecha_final'].'</td>
                                                        <td style="'.$style.'width:80px;text-align:center;"> '.$dias.'</td>
                                                        <td style="'.$style.'width:80px;text-align:right;"> '.number_format($rowFacturas['saldo'],2,',','.').'</td>
                                                        <td style="'.$style.'width:80px;text-align:right;">  '.number_format($porVencer,2,',','.').'</td>
                                                        <td style="'.$style.'width:80px;text-align:right;" > '.number_format($unoAtreinta,2,',','.').'</td>
                                                        <td style="'.$style.'width:80px;text-align:right;" > '.number_format($treintayunoAsesenta,2,',','.').'</td>
                                                        <td style="'.$style.'width:80px;text-align:right;" > '.number_format($sesentayunoAnoventa,2,',','.').'</td>
                                                        <td style="'.$style.'width:80px;text-align:right;" > '.number_format($masDenoventa,2,',','.').'</td>
                                                      </tr>' : '';
    }
    else{
        $bodyTable .= ($tipo_informe == 'detallado')?  '<tr>
                                                          '.$campos.'
                                                          <td style="'.$style.'width:80px;">'.$rowFacturas['codigo_cuenta'].'</td>
                                                          <td style="'.$style.'width:60px;text-align:right;">'.$rowFacturas['consecutivo'].'</td>
                                                          <td style="'.$style.'width:100;text-align:right;">'.$rowFacturas['prefijo_factura'].' '.$rowFacturas['numero_factura'].' </td>
                                                          <td style="'.$style.'width:100px;text-align:right;">'.$rowFacturas['fecha_inicio'].' </td>
                                                          <td style="'.$style.'width:80px;text-align:right;">'.$rowFacturas['fecha_final'].'</td>
                                                          <td style="'.$style.'width:80px;text-align:center;"> '.$dias.'</td>
                                                          <td style="'.$style.'width:80px;text-align:right;"> '.number_format($rowFacturas['saldo'],2,',','.').'</td>
                                                          <td style="'.$style.'width:80px;text-align:right;" >'.number_format($porVencer,2,',','.').'</td>
                                                          <td style="'.$style.'width:80px;text-align:right;"> '.number_format($unoAtreinta,2,',','.').'</td>
                                                          <td style="'.$style.'width:80px;text-align:right;"> '.number_format($treintayunoAsesenta,2,',','.').'</td>
                                                          <td style="'.$style.'width:80px;text-align:right;"> '.number_format($sesentayunoAnoventa,2,',','.').'</td>
                                                          <td style="'.$style.'width:80px;text-align:right;"> '.number_format($masDenoventa,2,',','.').'</td>
                                                        </tr>' : '';
    }

    //TOTALES ACUMULADOS
    $acumuladoSaldo+=$rowFacturas['saldo'];
    $acumuladoporVencerTotal            += ($dias < 1)? $rowFacturas['saldo']: '';
    $acumuladounoAtreintaTotal          += ($dias > 0 && $dias <= 30)? $rowFacturas['saldo'] : 0;
    $acumuladotreintayunoAsesentaTotal  += ($dias > 30 && $dias <= 60)? $rowFacturas['saldo'] : 0;
    $acumuladosesentayunoAnoventaTotal  += ($dias > 60 && $dias <= 90)? $rowFacturas['saldo'] : 0;
    $acumuladomasDenoventaTotal         += ($dias > 90)? $rowFacturas['saldo'] : 0;

    //TOTALES INDIVIDUALES
    $acumuladoporVencer            += ($dias < 1)? $rowFacturas['saldo']: '';
    $acumuladounoAtreinta          += ($dias > 0 && $dias <= 30)? $rowFacturas['saldo'] : 0;
    $acumuladotreintayunoAsesenta  += ($dias > 30 && $dias <= 60)? $rowFacturas['saldo'] : 0;
    $acumuladosesentayunoAnoventa  += ($dias > 60 && $dias <= 90)? $rowFacturas['saldo'] : 0;
    $acumuladomasDenoventa         += ($dias > 90)? $rowFacturas['saldo'] : 0;
  }

  if($tercero != 0){
    $bodyTable .= ($tipo_informe != 'totalizado_edades')?'<tr class="total">
                                                          <td '.$colspanCliente.'  >TOTAL PROVEEDOR</td>
                                                          <td style="text-align:right;">'.number_format($acumuladoSaldo,2,',','.').'</td>
                                                          <td style="text-align:right;">'.number_format($acumuladoporVencer,2,',','.').'</td>
                                                          <td style="text-align:right;">'.number_format($acumuladounoAtreinta,2,',','.').'</td>
                                                          <td style="text-align:right;">'.number_format($acumuladotreintayunoAsesenta,2,',','.').'</td>
                                                          <td style="text-align:right;">'.number_format($acumuladosesentayunoAnoventa,2,',','.').'</td>
                                                          <td style="text-align:right;">'.number_format($acumuladomasDenoventa,2,',','.').'</td></tr>' : '';
  }

  //TOTALES DEL INFORME
  $bodyTable .=  '<tr>
                    <td>&nbsp;</td>
                  </tr>
                  <tr class="total">
                    <td '.$colspanCartera.'><b>TOTAL FACTURAS POR PAGAR</b></td>
                    <td style="text-align:right;"><b>'.number_format($acumuladoporVencerTotal,2,',','.').'</b></td>
                    <td style="text-align:right;"><b>'.number_format($acumuladounoAtreintaTotal,2,',','.').'</b></td>
                    <td style="text-align:right;"><b>'.number_format($acumuladotreintayunoAsesentaTotal,2,',','.').'</b></td>
                    <td style="text-align:right;"><b>'.number_format($acumuladosesentayunoAnoventaTotal,2,',','.').'</b></td>
                    <td style="text-align:right;"><b>'.number_format($acumuladomasDenoventaTotal,2,',','.').'</b></td>
                  </tr>';

  $tituloHead = ($IMPRIME_XLS == 'true')?  '<td>SUCURSAL</td>
                                            <td style="font-weight:bold; width:90px;border-left:1px solid;">NIT</td>
                                            <td style="font-weight:bold; width:90px;border-left:1px solid;">PROVEEDOR</td>
                                            <td style="font-weight:bold; width:90px;border-left:1px solid;">CUENTA</td>'
                                            : '<td>SUCURSAL</td>
                                            <td style="font-weight:bold; width:90px;">PROVEEDOR</td>';
?>
<style>
  .contenedor_informe, .contenedor_titulo_informe{
    width     : 100%;
    margin    : 0 0 10px 0;
    font-size : 11px;
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
<body>
  <div class="contenedor_titulo_informe">
    <div style="float:left; width:100%">
      <div style="float:left;width:100%; text-align:center">
        <table align="center" style="text-align:center;margin-bottom:15px;" >
          <tr><td class="titulo_informe_empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
          <tr><td style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
          <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></td></tr>
          <tr><td style="font-size:11px;text-align:center;"><?php echo $subtitulo; ?></td></tr>
        </table>
        <table class="table"  cellspacing="0" border="0">
          <thead>
            <tr>
              <?php echo $tituloHead; ?>
              <td style="font-weight:bold; width:60px;">CONSEC.</td>
              <td style="font-weight:bold; width:100px;">NUMERO</td>
              <td style="font-weight:bold; width:100px;">FECHA</td>
              <td style="font-weight:bold; width:80px;">VENCIMIENTO</td>
              <td style="font-weight:bold; width:80px;text-align:center;">DIAS</td>
              <td style="font-weight:bold; width:80px;text-align:right;">SALDO</td>
              <td style="font-weight:bold; width:80px;text-align:right;">POR VENCER</td>
              <td style="font-weight:bold; width:80px;text-align:right;">1-30</td>
              <td style="font-weight:bold; width:80px;text-align:right;">31-60</td>
              <td style="font-weight:bold; width:80px;text-align:right;">61-90</td>
              <td style="font-weight:bold; width:80px;text-align:right;" >MAS 90</td>
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
  $documento = "Facturas_Por_Pagar";

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
    $mpdf->SetAutoPageBreak(TRUE, 15);
    $mpdf->SetTitle($documento);
    $mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetHeader("");
    $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
    $mpdf->WriteHTML(utf8_encode($texto));
    if($PDF_GUARDA == 'true'){
      $mpdf->Output($documento.".pdf",'D');
    }
    else{
      $mpdf->Output($documento.".pdf",'I');
    }
    exit;
  }
  else{
    echo $texto;
  }
?>
