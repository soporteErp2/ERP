<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
	ob_start();

  if($IMPRIME_XLS == 'true'){
    header('Content-type: application/vnd.ms-excel');
    header("Content-Disposition: attachment; filename=Estado_de_resultados.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
  }

  $id_empresa            = $_SESSION['EMPRESA'];
  $divTitleSucursal      = '';
  $whereSucursal         = '';
  $whereFecha            = '';
  $groupBy               = '';
  $whereIdCentroCostos   = '';
  $separador_decimales   = ($separador_decimales == '')? "." : $separador_decimales;
  $separador_miles       = ($separador_miles == '')? "," : $separador_miles;
  $arrayCentroCostosJSON = json_decode($arrayCentroCostosJSON);

  //SALDOS
  $acumuladoDebe          = 0;
  $acumuladoHaber         = 0;
  $acumuladoSaldoAnterior = 0;
  $acumuladoSaldoActual   = 0;
  $script                 = '';

  if(!isset($MyInformeFiltroFechaFinal) || $MyInformeFiltroFechaFinal == ''){
    $tipo_balance_EstadoResultado='Resumido';
    $MyInformeFiltroFechaFinal=date("Y-m-d");
    $MyInformeFiltroFechaInicio=date("Y-m").'-01';

    $whereFecha    = 'AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
    $whereNiveles  = ' AND AC.codigo_cuenta=puc.cuenta ';
    $groupByOpcion = 'GROUP BY puc.cuenta'.$groupBy;
  }
  else{
    //NIVELES A GENERAR EL INFORME
    $sql = "SELECT digitos FROM puc_configuracion WHERE activo = 1 AND id_empresa = $_SESSION[EMPRESA] AND nombre = '$nivel_cuentas'";
    $query = $mysql->query($sql,$mysql->link);

    while($row = $mysql->fetch_array($query)){
      $arrayCuentas[] = $row['digitos'];
    }

    $whereNiveles = "";
    foreach($arrayCuentas as $rowx){
      $varCortar = $rowx;

      if($whereNiveles == ""){
        $whereNiveles .= "LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta";
      }
      else{
        $whereNiveles .= " OR LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta";
      }
    }

    $whereNiveles = "AND ($whereNiveles)";

    switch($tipo_balance_EstadoResultado){
          case 'mensual':
              $fecha_informe = explode('-', $MyInformeFiltroFechaFinal);
              $MyInformeFiltroFechaInicio = $fecha_informe[0].'-'.$fecha_informe[1].'-01';

              $whereFecha   = 'AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
              $tipo_informe = 'Mensual <br>de '.$MyInformeFiltroFechaInicio.' a '.$MyInformeFiltroFechaFinal;
              break;

          case 'mensual_acumulado':

              $fecha_informe = explode('-', $MyInformeFiltroFechaFinal);
              $MyInformeFiltroFechaInicio = $fecha_informe[0].'-01-01';

              $whereFecha   = 'AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
              $tipo_informe = 'Mensual Acumulado<br>de '.$MyInformeFiltroFechaInicio.' a '.$MyInformeFiltroFechaFinal;

              break;

          case 'comparativo_mensual':
              $tipo_informe  = 'Comparativo Mensual';
              $groupBy       = ',YEAR(AC.fecha) , MONTH(AC.fecha)';
              $campos_select = ',YEAR(AC.fecha) as anio, MONTH(AC.fecha) as mes, DAY(AC.fecha) as dia';
              $mesanterior   = date("Y-m-d", strtotime($MyInformeFiltroFechaFinal.' - 1 month'));
              $cantidadDias  = split('-', $MyInformeFiltroFechaFinal)[2];
              if ($cantidadDias>30) {
                  $mesanterior   = date("Y-m-d", strtotime($mesanterior.' - 1 day'));
              }
              $fecha_informe = explode('-', $mesanterior);
              $MyInformeFiltroFechaInicio = $fecha_informe[0].'-'.$fecha_informe[1].'-01';
              $whereFecha = 'AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';

              break;

          case 'rango_fechas':
              // $fecha_informe = explode('-', $MyInformeFiltroFechaFinal);
              // $MyInformeFiltroFechaInicio = $fecha_informe[0].'-'.$fecha_informe[1].'-01';

              $whereFecha   = 'AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
              $tipo_informe = 'Rango de Fechas <br>de '.$MyInformeFiltroFechaInicio.' a '.$MyInformeFiltroFechaFinal;
              $tipo_balance_EstadoResultado = 'mensual';
              break;

          case 'comparativo_anual':
              $tipo_informe  = 'Comparativo Anual';
              $groupBy       = ',YEAR(AC.fecha)';
              $campos_select = ',YEAR(AC.fecha) as anio, MONTH(AC.fecha) as mes, DAY(AC.fecha) as dia';
              $anioAnterior  =  date("Y-m-d", strtotime($MyInformeFiltroFechaFinal.' - 1 year'));
              $fecha_informe = explode('-', $anioAnterior);

              $MyInformeFiltroFechaInicio = $fecha_informe[0].'-01-01';
              $whereFecha = 'AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
              break;

    }
    $groupByOpcion = 'GROUP BY puc.cuenta'.$groupBy;

    if (!empty($arrayCentroCostosJSON)) {
      if ($id_centro_costos=='todos') {
          $sql="SELECT id FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa";
          $query=mysql_query($sql,$link);
          while ($row=mysql_fetch_array($query)) {
              $whereIdCentroCostos.=($whereIdCentroCostos!='')? ' OR AC.codigo_centro_costos LIKE "'.$row['id'].'%"' : ' AND ( AC.codigo_centro_costos LIKE "'.$row['id'].'%"' ;
          }

          $whereIdCentroCostos.=' OR AC.codigo_centro_costos=0 OR AC.codigo_centro_costos="" OR ISNULL(AC.codigo_centro_costos))';
      }
      else{
        foreach ($arrayCentroCostosJSON as $indice => $id_centro_costos) {
          $whereCcos .= ($whereCcos=='')? ' id_centro_costos='.$id_centro_costos : $whereCcos.' OR id_centro_costos='.$id_centro_costos;
        }
        $whereCcos   = " AND (".$whereCcos.")";
      }

      $tipo_informe .= '<br>Por Centros de Costos';
      $groupByOpcion = 'GROUP BY puc.cuenta'.$groupBy.',codigo_centro_costos';
      }
    }

    //FILTRO POR SUCURSAL
    if($sucursal != ""){
      if($sucursal != 'global'){
        $whereSucursal = ' AND AC.id_sucursal = '.$sucursal;

        //CONSULTAR EL NOMBRE DE LA SUCURSAL
        $sqlSucursal        = "SELECT nombre FROM empresas_sucursales WHERE id_empresa = $id_empresa AND id = $sucursal";
        $querySucursal      = mysql_query($sqlSucursal,$link);
        $subtitulo_cabecera .= '<b>Sucursal</b> '.mysql_result($querySucursal,0,'nombre').'<br>';
      }
    }

    $desde = $MyInformeFiltroFechaInicio;
    $hasta = $MyInformeFiltroFechaFinal;
    $fec1  = $desde;
    $fec2  = $hasta;

    $split1 = explode('-', $desde);
    $split2 = explode('-', $hasta);

    $year1 = $split1[0];
    $year2 = $split2[0];
    $mes1  = $split1[1];
    $mes2  = $split2[1];
    $dia1  = $split1[2];
    $dia2  = $split2[2];

    $wherePuc      = '';
    $cuerpoInforme = '';

    // CONSULTAR LA CUENTA DE CIERRE
    $sql = "SELECT cuenta FROM puc WHERE activo = 1 AND id_empresa = $id_empresa AND tipo = 'cuenta_cierre' ";
    $query = $mysql->query($sql,$mysql->link);
    $whereCuentaCierre = ($mysql->result($query,0,'cuenta')<>'')? " AND codigo_cuenta<> '".$mysql->result($query,0,'cuenta')."' " : '' ;

    //CADENA CON LA CONSULTA
    $sql = "SELECT
              SUM(AC.debe - AC.haber) AS saldo,
              puc.cuenta,
              puc.descripcion,
              AC.centro_costos,
              AC.codigo_centro_costos
              $campos_select
            FROM
              asientos_colgaap AS AC,puc
            WHERE
              AC.activo = 1
              /*AND puc.activo=1*/
              AND AC.id_empresa = $id_empresa
              AND AC.tipo_documento <> 'NCC'
              AND puc.id_empresa = $id_empresa
              AND (
                    AC.codigo_cuenta LIKE '6%'
                    OR AC.codigo_cuenta LIKE '7%'
                    OR AC.codigo_cuenta LIKE '41%'
                    OR AC.codigo_cuenta LIKE '51%'
                    OR AC.codigo_cuenta LIKE '52%'
                    OR AC.codigo_cuenta LIKE '53%'
                    OR AC.codigo_cuenta LIKE '42%'
                    OR AC.codigo_cuenta LIKE '54%'
                    OR AC.codigo_cuenta LIKE '59%'
                  )
              $whereCuentaCierre
              $whereNiveles
              $whereFecha
              $whereIdCentroCostos
              $whereSucursal
              $groupByOpcion";

    if($tipo_balance_EstadoResultado == 'Resumido'){
      $subtitulo_cabecera .= 'Mensual<br>de '.$MyInformeFiltroFechaInicio.' a '.$MyInformeFiltroFechaFinal.' <br>Resumido';
      $query = mysql_query($sql,$link);

      while($row = mysql_fetch_array($query)){
        //SI ES UN COSTO DE VENTA
        if (substr($row['cuenta'], 0,1) == '6') { $saldoCV2 += $row['saldo']; }
        if (substr($row['cuenta'], 0,1) == '7') { $saldoCV2 += $row['saldo']; }

        //VENTAS DEL PERIODO
        if (substr($row['cuenta'], 0,2) == '41'){
          if(substr($row['cuenta'], 0,4) == '4175'){ $devolucionIO += $row['saldo']; }                //FILTRAMOS LA CUENTA DE DEVOLUCION QUE ES LA 4175
          else{ $saldoIO2 += $row['saldo']; }                //SI NO ES LA CUENTA DE DEVOLUCION
        }

        //SI ES UN GASTO DE ADMINISTRACION
        if (substr($row['cuenta'], 0,2)=='51') { $saldoGOA2 += $row['saldo']; }
        //SI ES UN GASTO DE VENTA
        if (substr($row['cuenta'], 0,2)=='52') { $saldoGOV2 += $row['saldo']; }
        //SI ES UN GASTO FINANCIERO
        if (substr($row['cuenta'], 0,2)=='53') { $saldoIGNOGF2 += $row['saldo']; }
        //SI ES OTROS INGRESOS
        if (substr($row['cuenta'], 0,2)=='42') { $saldoIGNOOI2 += $row['saldo']; }
        //SI ES IMPUESTO SOBRE LA RENTA
        if (substr($row['cuenta'], 0,2)=='54' || substr($row['cuenta'], 0,2)=='59') { $saldoIR2 += $row['saldo']; }
      }

      //utilidad bruta en ventas
      $saldoIO2 += $devolucionIO;
      $utilidadBrutaVentas2 = $saldoIO2 + $saldoCV2;

      //gastos operacionales
      $gastosOperacionales2 = $saldoGOA2 + $saldoGOV2;

      //utilidad operacional
      $utilidadOperacional2 = $utilidadBrutaVentas2 + $gastosOperacionales2;

      $cuerpoInforme = '<table style="width:80%;font-size:11px;">
                          <tr>
                            <td style="width:50%;"><b> INGRESOS OPERACIONALES </b></td> <td style="width:30%;text-align:right;">&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;"> INGRESOS OPERACIONALES </td> <td style="width:30%;text-align:right;">'.number_format($saldoIO2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;">  -COSTO DE VENTAS</td><td style="width:30%;text-align:right;">'.number_format($saldoCV2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;"><b>UTILIDAD BRUTA EN VENTAS</b></td><td style="width:30%;text-align:right;"><b>'.number_format($utilidadBrutaVentas2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                            <td style="width:50%;"><b>-GASTOS OPERACIONALES </b></td><td style="width:30%;text-align:right;">&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;">ADMINISTRACION</td><td style="width:30%;text-align:right;">'.number_format($saldoGOA2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;">VENTAS</td><td style="width:30%;text-align:right;">'.number_format($saldoGOV2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;"> GASTOS OPERACIONALES &nbsp;</td><td style="width:30%;text-align:right;"> '.number_format($gastosOperacionales2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;"><b>UTILIDAD OPERACIONAL</b></td><td style="width:30%;text-align:right;"><b>'.number_format($utilidadOperacional2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</b></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;"><b>INGRESOS Y GASTOS NO OPERACIONALES </b></td><td style="width:30%;text-align:right;"></td>
                          </tr>
                          <tr>
                            <td style="width:50%;">-GASTOS FINANCIEROS</td><td style="width:30%;text-align:right;"> '.number_format($saldoIGNOGF2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;">+OTROS INGRESOS </td><td style="width:30%;text-align:right;"> '.number_format($saldoIGNOOI2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>';

      $utilidadOperacional  += $saldoIGNOGF;
      $utilidadOperacional  += $saldoIGNOOI;

      $utilidadOperacional2 += $saldoIGNOGF2;
      $utilidadOperacional2 += $saldoIGNOOI2;

      $cuerpoInforme .=  '<tr>
                            <td style="width:50%;"><b> UTILIDAD ANTES DE IMPUESTO</b></td><td style="width:30%;text-align:right;"><b>'.number_format($utilidadOperacional2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                            <td style="width:50%;">IMPUESTO SOBRE LA RENTA</td><td style="width:30%;text-align:right;">'.number_format($saldoIR2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>';

      $utilidadOperacional  += $saldoIR;
      $utilidadOperacional2 += $saldoIR2;

      $cuerpoInforme .=  '<tr>
                            <td style="width:50%;">UTILIDAD NETA</td><td style="width:30%;text-align:right;">'.number_format($utilidadOperacional2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                          </tr>
                          <tr>
                            <td style="width:50%;"><b>UTILIDAD O PERDIDA</b></td><td style="width:30%;text-align:right;"><b>'.number_format($utilidadOperacional2, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'&nbsp;</b></td>
                          </tr>
                      </table>';
    }
    else if($tipo_balance_EstadoResultado == 'comparativo_mensual' || $tipo_balance_EstadoResultado == 'comparativo_anual'){

      $subtitulo_cabecera .= $tipo_informe . '<br>A nivel de ' . $nivel_cuentas;
      $query = mysql_query($sql,$link);

      //SI ES POR CENTRO DE COSTOS
      if($id_centro_costos != ''){
        $width   = '160px';
        $td      = '<td style="width:90px;">&nbsp;</td>';
        $colspan = '3';
        $tdHead  = '<td style="width:90px; text-align:center;"><b>Centro Costos</b></td>';
      }
      else{
        $width   = '250px';
        $td      = '';
        $colspan = '2';
        $tdHead  = '';
      }

      //RECORREMOS EL RESULTADO DE LA CONSULTA  Y LLENAMOS EL ARRAY CON EL VALOR DEL SALDO Y COMO INDICE
      while($row = mysql_fetch_array($query)){
        if($tipo_balance_EstadoResultado == 'comparativo_mensual'){
          //SI ES POR CENTRO DE COSTOS
          if($id_centro_costos != ''){ $arrayDatosCuentas[$row['cuenta']][$row['centro_costos']][$row['anio']][$row['mes']]= $row['saldo']; }
          else{ $arrayDatosCuentas[$row['cuenta']][$row['anio']][$row['mes']] = $row['saldo']; }
        }
        else{
          if($id_centro_costos != ''){ $arrayDatosCuentas[$row['cuenta']][$row['centro_costos']][$row['anio']] = $row['saldo']; }
          else{ $arrayDatosCuentas[$row['cuenta']][$row['anio']] = $row['saldo']; }
        }

        $arrayCuentas[$row['cuenta']] = $row['descripcion'];
      }

      //FECHAS DE LOS MESES PARA LA COMPARACION
      $fechaAnterior = explode('-', $MyInformeFiltroFechaInicio);
      $fechaActual   = explode('-', $MyInformeFiltroFechaFinal);

      $anioAnterior = $fechaAnterior[0];
      $mesAnterior  = $fechaAnterior[1]*1;

      $anioActual   = $fechaActual[0];
      $mesActual    = $fechaActual[1]*1;

      //SI EL INFORME ES POR CENTROS DE COSTO
      if ($id_centro_costos!='') {
          foreach ($arrayDatosCuentas as $cuenta => $arrayCentroCostos) {
              foreach ($arrayCentroCostos as $centro_costos => $saldo) {
                $td = '';

                //COSTO DE VENTA
                if(substr($cuenta, 0,1) == '6' || substr($cuenta, 0,1) == '7'){
                    if($tipo_balance_EstadoResultado == 'comparativo_mensual'){
                      $costoVentaAnterior += $saldo[$anioAnterior][$mesAnterior];
                      $costoVentaActual   += $saldo[$anioActual][$mesActual];

                      $costoVentaAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                      $costoVentaActualUnico   = $saldo[$anioActual][$mesActual];
                    }
                    else{
                      $costoVentaAnterior += $saldo[$anioAnterior];
                      $costoVentaActual   += $saldo[$anioActual];

                      $costoVentaAnteriorUnico = $saldo[$anioAnterior];
                      $costoVentaActualUnico   = $saldo[$anioActual];
                    }

                    $diferencia = $costoVentaActualUnico - $costoVentaAnteriorUnico;
                    $porcentaje = ($costoVentaActualUnico/$costoVentaAnteriorUnico)-1;
                    $td         = '<td style="width:90px;font-style: italic;text-align:center;">'.$centro_costos.' &nbsp;</td>';

                    $costoVentaCuerpo .= '<tr>
                                            <td style="width:60px;">'.$cuenta.'</td>
                                            <td style="width:'.$width.';">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                            '.$td.'
                                            <td style="width:90px;text-align:right;">'.number_format($costoVentaAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                            <td style="width:90px;text-align:right;">'.number_format($costoVentaActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                            <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                            <td style="width:90px;text-align:right;">'.number_format($porcentaje,$IMPRIME_XLS).'</td>
                                          </tr>';
                }

                  //VENTAS DEL PERIODO
                  if (substr($cuenta, 0,2)=='41') {

                      if (substr($cuenta, 0,4)=='4175') {
                          if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                              $devolucionAnterior += $saldo[$anioAnterior][$mesAnterior];
                              $devolucionActual   += $saldo[$anioActual][$mesActual];

                              $devolucionAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                              $devolucionActualUnico   = $saldo[$anioActual][$mesActual];
                          }
                          else{
                              $devolucionAnterior += $saldo[$anioAnterior];
                              $devolucionActual   += $saldo[$anioActual];

                              $devolucionAnteriorUnico = $saldo[$anioAnterior];
                              $devolucionActualUnico   = $saldo[$anioActual];
                          }


                          $diferencia = $devolucionActualUnico - $devolucionAnteriorUnico;
                          $porcentaje = ($devolucionActualUnico/$devolucionAnteriorUnico)-1;

                          $devolucionCuerpo.='<tr>
                                              <td style="width:60px;">'.$cuenta.'</td>
                                              <td style="width:'.$width.';">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                              <td style="width:90px;">&nbsp;</td><!--cuando se pide por centro_costos -->
                                              <td style="width:90px;text-align:right;">'.number_format($devolucionAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($devolucionActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                          </tr>';

                      }
                      //SI NO ES LA CUENTA DE DEVOLUCION
                      else{
                          if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                              $ventasAnterior += $saldo[$anioAnterior][$mesAnterior];
                              $ventasActual   += $saldo[$anioActual][$mesActual];

                              $ventasAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                              $ventasActualUnico   = $saldo[$anioActual][$mesActual];
                          }
                          else{
                              $ventasAnterior += $saldo[$anioAnterior];
                              $ventasActual   += $saldo[$anioActual];

                              $ventasAnteriorUnico = $saldo[$anioAnterior];
                              $ventasActualUnico   = $saldo[$anioActual];
                          }

                          $diferencia = $ventasActualUnico - $ventasAnteriorUnico;
                          $porcentaje = ($ventasActualUnico/$ventasAnteriorUnico)-1;

                          $ventasCuerpo  .= '<tr>
                                                  <td style="width:60px;">'.$cuenta.'</td>
                                                  <td style="width:'.$width.';overflow:hidden;" >'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                                  <td style="width:90px;">&nbsp;</td><!--cuando se pide por centro_costos -->
                                                  <td style="width:90px;text-align:right;">'.number_format($ventasAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($ventasActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              </tr>';
                      }
                  }

                  //SI ES UN GASTO DE ADMINISTRACION
                  if (substr($cuenta, 0,2)=='51') {
                      if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                          $gastosAdministracionAnterior += $saldo[$anioAnterior][$mesAnterior];
                          $gastosAdministracionActual   += $saldo[$anioActual][$mesActual];

                          $gastosAdministracionAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                          $gastosAdministracionActualUnico   = $saldo[$anioActual][$mesActual];
                      }
                      else{
                          $gastosAdministracionAnterior += $saldo[$anioAnterior];
                          $gastosAdministracionActual   += $saldo[$anioActual];

                          $gastosAdministracionAnteriorUnico = $saldo[$anioAnterior];
                          $gastosAdministracionActualUnico   = $saldo[$anioActual];
                      }

                      $diferencia = $gastosAdministracionActualUnico - $gastosAdministracionAnteriorUnico;
                      $porcentaje = ($gastosAdministracionActualUnico/$gastosAdministracionAnteriorUnico)-1;

                      $td='<td style="width:90px;font-style: italic;text-align:center;">'.$centro_costos.' &nbsp;</td>';

                      $gastosAdministracionCuerpo .= '<tr>
                                                          <td style="width:60px;">'.$cuenta.'</td>
                                                          <td style="width:'.$width.';">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                                          '.$td.'
                                                          <td style="width:90px;text-align:right;">'.number_format($gastosAdministracionAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                          <td style="width:90px;text-align:right;">'.number_format($gastosAdministracionActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                          <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                                          <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                      </tr>';
                  }

                  //SI ES UN GASTO DE VENTA
                  if (substr($cuenta, 0,2)=='52') {
                      if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                          $gastosVentaAnterior += $saldo[$anioAnterior][$mesAnterior];
                          $gastosVentaActual   += $saldo[$anioActual][$mesActual];

                          $gastosVentaAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                          $gastosVentaActualUnico   = $saldo[$anioActual][$mesActual];
                      }
                      else{
                          $gastosVentaAnterior += $saldo[$anioAnterior];
                          $gastosVentaActual   += $saldo[$anioActual];

                          $gastosVentaAnteriorUnico = $saldo[$anioAnterior];
                          $gastosVentaActualUnico   = $saldo[$anioActual];
                      }

                      $diferencia = $gastosVentaActualUnico - $gastosVentaAnteriorUnico;
                      $porcentaje = ($gastosVentaActualUnico/$gastosVentaAnteriorUnico)-1;

                      $td = '<td style="width:90px;font-style: italic;text-align:center;">'.$centro_costos.' &nbsp;</td>';

                      $gastosVentaCuerpo.='<tr>
                                              <td style="width:60px;">'.$cuenta.'</td>
                                              <td style="width:'.$width.';">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                              '.$td.'
                                              <td style="width:90px;text-align:right;">'.number_format($gastosVentaAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($gastosVentaActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                          </tr>';
                  }

                  //SI ES UN GASTO NO OPERACIONAL
                  if (substr($cuenta, 0,2)=='53') {
                      if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                          $gastosOperacionalAnterior += $saldo[$anioAnterior][$mesAnterior];
                          $gastosOperacionalActual   += $saldo[$anioActual][$mesActual];

                          $gastosOperacionalAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                          $gastosOperacionalActualUnico   = $saldo[$anioActual][$mesActual];
                      }
                      else{
                          $gastosOperacionalAnterior += $saldo[$anioAnterior];
                          $gastosOperacionalActual   += $saldo[$anioActual];

                          $gastosOperacionalAnteriorUnico = $saldo[$anioAnterior];
                          $gastosOperacionalActualUnico   = $saldo[$anioActual];
                      }

                      $diferencia = $gastosOperacionalActualUnico - $gastosOperacionalAnteriorUnico;
                      $porcentaje = ($gastosOperacionalActualUnico/$gastosOperacionalAnteriorUnico)-1;

                      $td = '<td style="width:90px;font-style: italic;text-align:center;">'.$centro_costos.' &nbsp;</td>';

                      $gastosOperacionalCuerpo .= '<tr>
                                                      <td style="width:60px;">'.$cuenta.'</td>
                                                      <td style="width:'.$width.';">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                                      '.$td.'
                                                      <td style="width:90px;text-align:right;">'.number_format($gastosOperacionalAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                      <td style="width:90px;text-align:right;">'.number_format($gastosOperacionalActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                      <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                                      <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                  </tr>';
                  }

                  //SI ES OTROS INGRESOS
                  if (substr($cuenta, 0,2)=='42') {
                      if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                          $otrosIngresosAnterior += $saldo[$anioAnterior][$mesAnterior];
                          $otrosIngresosActual   += $saldo[$anioActual][$mesActual];

                          $otrosIngresosAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                          $otrosIngresosActualUnico   = $saldo[$anioActual][$mesActual];
                      }
                      else{
                          $otrosIngresosAnterior += $saldo[$anioAnterior];
                          $otrosIngresosActual   += $saldo[$anioActual];

                          $otrosIngresosAnteriorUnico = $saldo[$anioAnterior];
                          $otrosIngresosActualUnico   = $saldo[$anioActual];
                      }

                      $diferencia = $otrosIngresosActualUnico - $otrosIngresosAnteriorUnico;
                      $porcentaje = ($otrosIngresosActualUnico/$otrosIngresosAnteriorUnico)-1;

                      $otrosIngresosCuerpo.='<tr>
                                              <td style="width:60px;">'.$cuenta.'</td>
                                              <td style="width:'.$width.';">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                              <td style="width:90px;">&nbsp;</td><!--cuando se pide por centro_costos -->
                                              <td style="width:90px;text-align:right;">'.number_format($otrosIngresosAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($otrosIngresosActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                          </tr>';
                  }

                  //OTROS EGRESOS
                  if (substr($cuenta, 0,2)=='54' || substr($cuenta, 0,2)=='59') {
                      if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                          $otrosEgresosAnterior += $saldo[$anioAnterior][$mesAnterior];
                          $otrosEgresosActual   += $saldo[$anioActual][$mesActual];

                          $otrosEgresosAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                          $otrosEgresosActualUnico   = $saldo[$anioActual][$mesActual];
                      }
                      else{
                          $otrosEgresosAnterior += $saldo[$anioAnterior];
                          $otrosEgresosActual   += $saldo[$anioActual];

                          $otrosEgresosAnteriorUnico = $saldo[$anioAnterior];
                          $otrosEgresosActualUnico   = $saldo[$anioActual];
                      }

                      $diferencia = $otrosEgresosActualUnico - $otrosEgresosAnteriorUnico;
                      $porcentaje = ($otrosEgresosActualUnico/$otrosEgresosAnteriorUnico)-1;

                      $td = '<td style="width:90px;font-style: italic;text-align:center;">'.$centro_costos.' &nbsp;</td>';

                      $otrosEgresosCuerpo  .= '<tr>
                                                  <td style="width:60px;">'.$cuenta.'</td>
                                                  <td style="width:'.$width.';">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                                  '.$td.'
                                                  <td style="width:90px;text-align:right;">'.number_format($otrosEgresosAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($otrosEgresosActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              </tr>';
                  }

              }//FOREACH 2
          }//FOREACH 1

      }
      else{
        foreach ($arrayDatosCuentas as $cuenta => $saldo) {

          //COSTO DE VENTA
          if (substr($cuenta, 0,1)=='6' || substr($cuenta, 0,1)=='7') {

              if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                  $costoVentaAnterior += $saldo[$anioAnterior][$mesAnterior];
                  $costoVentaActual   += $saldo[$anioActual][$mesActual];

                  $costoVentaAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                  $costoVentaActualUnico   = $saldo[$anioActual][$mesActual];
              }
              else{
                  $costoVentaAnterior += $saldo[$anioAnterior];
                  $costoVentaActual   += $saldo[$anioActual];

                  $costoVentaAnteriorUnico = $saldo[$anioAnterior];
                  $costoVentaActualUnico   = $saldo[$anioActual];
              }


              $diferencia = $costoVentaActualUnico - $costoVentaAnteriorUnico;
              $porcentaje = ($costoVentaActualUnico/$costoVentaAnteriorUnico)-1;

              $costoVentaCuerpo.='<tr>
                                      <td style="width:60px;">'.$cuenta.'</td>
                                      <td style="width:250px;">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                      <td style="width:90px;text-align:right;">'.number_format($costoVentaAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($costoVentaActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($porcentaje,$IMPRIME_XLS).'</td>
                                  </tr>';
          }

          //VENTAS DEL PERIODO
          //SI EL SALDO ES NEGATIVO SE MULTIPLICA POR -1 PARA CONVERTIRLO A POSITIVO Y QUE SE HAGA MAS ENTENDIBLE
          if (substr($cuenta, 0,2)=='41') {

              if (substr($cuenta, 0,4)=='4175') {
                  if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                      $devolucionAnterior += $saldo[$anioAnterior][$mesAnterior];
                      $devolucionActual   += $saldo[$anioActual][$mesActual];

                      $devolucionAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                      $devolucionActualUnico   = $saldo[$anioActual][$mesActual];
                  }
                  else{
                      $devolucionAnterior += $saldo[$anioAnterior];
                      $devolucionActual   += $saldo[$anioActual];

                      $devolucionAnteriorUnico = $saldo[$anioAnterior];
                      $devolucionActualUnico   = $saldo[$anioActual];
                  }

                  $diferencia = $devolucionActualUnico - $devolucionAnteriorUnico;
                  $porcentaje = ($devolucionActualUnico/$devolucionAnteriorUnico)-1;

                  $devolucionCuerpo   .= '<tr>
                                              <td style="width:60px;">'.$cuenta.'</td>
                                              <td style="width:250px;">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                              <td style="width:90px;text-align:right;">'.number_format($devolucionAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($devolucionActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                          </tr>';

               }
               //SI NO ES LA CUENTA DE DEVOLUCION
               else{
                  if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                      $ventasAnterior += $saldo[$anioAnterior][$mesAnterior];
                      $ventasActual   += $saldo[$anioActual][$mesActual];

                      $ventasAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                      $ventasActualUnico   = $saldo[$anioActual][$mesActual];
                  }
                  else{
                      $ventasAnterior += $saldo[$anioAnterior];
                      $ventasActual   += $saldo[$anioActual];

                      $ventasAnteriorUnico = $saldo[$anioAnterior];
                      $ventasActualUnico   = $saldo[$anioActual];
                  }

                  $diferencia = $ventasActualUnico - $ventasAnteriorUnico;
                  $porcentaje = ($ventasActualUnico/$ventasAnteriorUnico)-1;

                  $ventasCuerpo.='<tr>
                                      <td style="width:60px;">'.$cuenta.'</td>
                                      <td style="width:250px;overflow:hidden;" >'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                      <td style="width:90px;text-align:right;">'.number_format($ventasAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($ventasActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                  </tr>';

               }
          }

          //SI ES UN GASTO DE ADMINISTRACION
          if (substr($cuenta, 0,2)=='51') {
              if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                  $gastosAdministracionAnterior += $saldo[$anioAnterior][$mesAnterior];
                  $gastosAdministracionActual   += $saldo[$anioActual][$mesActual];

                  $gastosAdministracionAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                  $gastosAdministracionActualUnico   = $saldo[$anioActual][$mesActual];

              }else{
                  $gastosAdministracionAnterior += $saldo[$anioAnterior];
                  $gastosAdministracionActual   += $saldo[$anioActual];

                  $gastosAdministracionAnteriorUnico = $saldo[$anioAnterior];
                  $gastosAdministracionActualUnico   = $saldo[$anioActual];
              }

              $diferencia = $gastosAdministracionActualUnico - $gastosAdministracionAnteriorUnico;
              $porcentaje = ($gastosAdministracionActualUnico/$gastosAdministracionAnteriorUnico)-1;

              $gastosAdministracionCuerpo  .= '<tr>
                                                  <td style="width:60px;">'.$cuenta.'</td>
                                                  <td style="width:250px;">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($gastosAdministracionAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($gastosAdministracionActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                                  <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              </tr>';
          }

          //GASTO DE VENTA
          if (substr($cuenta, 0,2)=='52') {
              if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                  $gastosVentaAnterior += $saldo[$anioAnterior][$mesAnterior];
                  $gastosVentaActual   += $saldo[$anioActual][$mesActual];

                  $gastosVentaAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                  $gastosVentaActualUnico   = $saldo[$anioActual][$mesActual];
              }
              else{
                  $gastosVentaAnterior += $saldo[$anioAnterior];
                  $gastosVentaActual   += $saldo[$anioActual];

                  $gastosVentaAnteriorUnico = $saldo[$anioAnterior];
                  $gastosVentaActualUnico   = $saldo[$anioActual];
              }

              $diferencia = $gastosVentaActualUnico - $gastosVentaAnteriorUnico;
              $porcentaje = ($gastosVentaActualUnico/$gastosVentaAnteriorUnico)-1;


              $gastosVentaCuerpo.='<tr>
                                      <td style="width:60px;">'.$cuenta.'</td>
                                      <td style="width:250px;">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                      <td style="width:90px;text-align:right;">'.number_format($gastosVentaAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($gastosVentaActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                  </tr>';
          }

          //SI ES UN GASTO NO OPERACIONAL
          if (substr($cuenta, 0,2)=='53') {
              if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                  $gastosOperacionalAnterior += $saldo[$anioAnterior][$mesAnterior];
                  $gastosOperacionalActual   += $saldo[$anioActual][$mesActual];

                  $gastosOperacionalAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                  $gastosOperacionalActualUnico   = $saldo[$anioActual][$mesActual];
              }
              else{
                  $gastosOperacionalAnterior += $saldo[$anioAnterior];
                  $gastosOperacionalActual   += $saldo[$anioActual];

                  $gastosOperacionalAnteriorUnico = $saldo[$anioAnterior];
                  $gastosOperacionalActualUnico   = $saldo[$anioActual];
              }

              $diferencia = $gastosOperacionalActualUnico - $gastosOperacionalAnteriorUnico;
              $porcentaje = ($gastosOperacionalActualUnico/$gastosOperacionalAnteriorUnico)-1;

              $gastosOperacionalCuerpo .= '<tr>
                                              <td style="width:60px;">'.$cuenta.'</td>
                                              <td style="width:250px;">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                              <td style="width:90px;text-align:right;">'.number_format($gastosOperacionalAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($gastosOperacionalActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                              <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                          </tr>';
          }

          //SI ES OTROS INGRESOS
          if (substr($cuenta, 0,2)=='42') {
              if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                  $otrosIngresosAnterior += $saldo[$anioAnterior][$mesAnterior];
                  $otrosIngresosActual   += $saldo[$anioActual][$mesActual];

                  $otrosIngresosAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                  $otrosIngresosActualUnico   = $saldo[$anioActual][$mesActual];
              }
              else{
                  $otrosIngresosAnterior += $saldo[$anioAnterior];
                  $otrosIngresosActual   += $saldo[$anioActual];

                  $otrosIngresosAnteriorUnico = $saldo[$anioAnterior];
                  $otrosIngresosActualUnico   = $saldo[$anioActual];
              }

              $diferencia = $otrosIngresosActualUnico - $otrosIngresosAnteriorUnico;
              $porcentaje = ($otrosIngresosActualUnico/$otrosIngresosAnteriorUnico)-1;

              $otrosIngresosCuerpo .= '<tr>
                                          <td style="width:60px;">'.$cuenta.'</td>
                                          <td style="width:250px;">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                          <td style="width:90px;text-align:right;">'.number_format($otrosIngresosAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                          <td style="width:90px;text-align:right;">'.number_format($otrosIngresosActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                          <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                          <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      </tr>';
          }

          //OTROS EGRESOS
          if (substr($cuenta, 0,2)=='54' || substr($cuenta, 0,2)=='59') {
              if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                  $otrosEgresosAnterior += $saldo[$anioAnterior][$mesAnterior];
                  $otrosEgresosActual   += $saldo[$anioActual][$mesActual];

                  $otrosEgresosAnteriorUnico = $saldo[$anioAnterior][$mesAnterior];
                  $otrosEgresosActualUnico   = $saldo[$anioActual][$mesActual];
              }
              else{
                  $otrosEgresosAnterior += $saldo[$anioAnterior];
                  $otrosEgresosActual   += $saldo[$anioActual];

                  $otrosEgresosAnteriorUnico = $saldo[$anioAnterior];
                  $otrosEgresosActualUnico   = $saldo[$anioActual];
              }

              $diferencia = $otrosEgresosActualUnico - $otrosEgresosAnteriorUnico;
              $porcentaje = ($otrosEgresosActualUnico/$otrosEgresosAnteriorUnico)-1;

              $otrosEgresosCuerpo.='<tr>
                                      <td style="width:60px;">'.$cuenta.'</td>
                                      <td style="width:250px;">'.$arrayCuentas[$cuenta].' &nbsp;</td>
                                      <td style="width:90px;text-align:right;">'.number_format($otrosEgresosAnteriorUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($otrosEgresosActualUnico, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($diferencia,$IMPRIME_XLS).'</td>
                                      <td style="width:90px;text-align:right;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                  </tr>';
          }
        }
      }

      //ACUMULADOS DE LA EL PERIODO ANTERIOR
      $ventasNetasAnterior      = $ventasAnterior + $devolucionAnterior;
      $margenBrutoAnterior      = $ventasNetasAnterior + $costoVentaAnterior;
      $utilidadNetaAnterior     = $margenBrutoAnterior + $gastosAdministracionAnterior + $gastosVentaAnterior + $gastosOperacionalAnterior;
      $utilidadOperdidaAnterior = $utilidadNetaAnterior + $otrosIngresosAnterior + $otrosEgresosAnterior;
      //ACUMULADOS DEL PERIODO ACTUAL
      $ventasNetasActual      =$ventasActual + $devolucionActual;
      $margenBrutoActual      =$ventasNetasActual + $costoVentaActual;
      $utilidadNetaActual     =$margenBrutoActual + $gastosAdministracionActual + $gastosVentaActual + $gastosOperacionalActual;
      $utilidadOperdidaActual =($utilidadNetaActual + $otrosIngresosActual) + $otrosEgresosActual;
      //DIFERENCIAS
      $ventasDiferencia               = $ventasActual - $ventasAnterior;
      $devolucionDiferencia           = $devolucionActual - $devolucionAnterior;
      $ventasNetasDiferencia          = $ventasNetasActual - $ventasNetasAnterior;
      $costoVentaDiferencia           = $costoVentaActual - $costoVentaAnterior;
      $margenBrutoDiferencia          = $margenBrutoActual - $margenBrutoAnterior;
      $gastosAdministracionDiferencia = $gastosAdministracionActual - $gastosAdministracionAnterior;
      $gastosVentaDiferencia          = $gastosVentaActual - $gastosVentaAnterior;
      $gastosOperacionalDiferencia    = $gastosOperacionalActual - $gastosOperacionalAnterior;
      $utilidadNetaDiferencia         = $utilidadNetaActual - $utilidadNetaAnterior;
      $otrosIngresosDiferencia        = $otrosIngresosActual - $otrosIngresosAnterior;
      $otrosEgresosDiferencia         = $otrosEgresosActual - $otrosEgresosAnterior;
      $utilidadOperdidaDiferencia     = $utilidadOperdidaActual - $utilidadOperdidaAnterior;

      //PORCENTAJE
      $ventasPorcentaje               = ($ventasDiferencia / $ventasAnterior)*100;
      $devolucionPorcentaje           = ($devolucionDiferencia / $devolucionActua)*100;
      $ventasNetasPorcentaje          = ($ventasNetasDiferencia / $ventasNetasAnterior)*100;
      $costoVentaPorcentaje           = ($costoVentaDiferencia / $costoVentaAnterior)*100;
      $margenBrutoPorcentaje          = ($margenBrutoDiferencia / $margenBrutoAnterior)*100;
      $gastosAdministracionPorcentaje = ($gastosAdministracionDiferencia / $gastosAdministracionAnterior)*100;
      $gastosVentaPorcentaje          = ($gastosVentaDiferencia / $gastosVentaAnterior)*100;
      $gastosOperacionalPorcentaje    = ($gastosOperacionalDiferencia / $gastosOperacionalAnterior)*100;
      $utilidadNetaPorcentaje         = ($utilidadNetaDiferencia / $utilidadNetaAnterior)*100;
      $otrosIngresosPorcentaje        = ($otrosIngresosDiferencia / $otrosIngresosAnterior)*100;
      $otrosEgresosPorcentaje         = ($otrosEgresosDiferencia / $otrosEgresosAnterior)*100;
      $utilidadOperdidaPorcentaje     = ($utilidadOperdidaDiferencia / $utilidadOperdidaAnterior)*100;

      // $ventasNetas=$ventas - $devolucion;
      // $margenBruto=$ventasNetas - $costoVenta;
      // $utilidadNeta=$margenBruto - $gastosAdministracion - $gastosVenta - $gastosOperacional;
      // $utilidadOperdida=($utilidadNeta + $otrosIngresos) - $otrosEgresos;

      if($tipo_balance_EstadoResultado=='comparativo_mensual' ){
        $diaFinalAnterior   = date("d",(mktime(0,0,0,$fechaAnterior[1]+1,1,$fechaAnterior[0])-1));
        $fechaFinalAnterior = $fechaAnterior[0].'-'.$fechaAnterior[1].'-'.$diaFinalAnterior;
        $fechaInicialActual = $fechaActual[0].'-'.$fechaActual[1].'-01';
      }
      else{
        $fechaFinalAnterior = $fechaAnterior[0].'-12-31';
        $fechaInicialActual = $fechaActual[0].'-'.$fechaActual[1].'-01';
      }

      $cuerpoInforme.='<table style="width:97%;font-size:11px;"  >
                          <tr>
                              <td style="width:60px;">&nbsp;</td>
                              <td style="width:200px;">&nbsp;</td>
                              '.$tdHead.'
                              <td style="width:90px; text-align:right;"><b>Periodo Ante.</b><br><label style="font-size:10px;">'.$MyInformeFiltroFechaInicio.'<br>a<br>'.$fechaFinalAnterior.'</label></td>
                              <td style="width:90px; text-align:right;"><b>A la fecha</b><br>'.$fechaInicialActual.'<br>a<br>'.$MyInformeFiltroFechaFinal.'</td>
                              <td style="width:90px; text-align:right;"><b>Diferencia</b></td>
                              <td style="width:90px; text-align:right;"><b>Porcentaje</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="6"><b>VENTAS DEL PERIODO</b></td>
                          </tr>
                          '.$ventasCuerpo.'
                          <tr>
                              <td colspan="'.$colspan.'"><b>TOTAL VENTAS DEL PERIODO </b></td>
                              <td style="text-align:right;border-top:1px solid;"><b>'.number_format($ventasAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($ventasActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($ventasDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($ventasPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="6" ><b>DEVOLUCIONES EN VENTA</b></td>
                          </tr>
                              '.$devolucionCuerpo.'
                          <tr>
                              <td colspan="'.$colspan.'"><b>-TOTAL DEVOLUCIONES EN VENTA</b> </td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($devolucionAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($devolucionActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($devolucionDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($devolucionPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="'.$colspan.'"><b>VENTAS NETAS</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($ventasNetasAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($ventasNetasActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($ventasNetasDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($ventasNetasPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="6"><b>COSTOS DE VENTAS</b></td>
                          </tr>
                              '.$costoVentaCuerpo.'
                          <tr>
                              <td colspan="'.$colspan.'"><b>-TOTAL COSTOS DE VENTAS<b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($costoVentaAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'<b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($costoVentaActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'<b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($costoVentaDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($costoVentaPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="'.$colspan.'"><b>MARGEN BRUTO</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($margenBrutoAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($margenBrutoActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($margenBrutoDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($margenBrutoPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="6"><b>GASTOS DE ADMINISTRACION</b></td>
                          </tr>
                              '.$gastosAdministracionCuerpo.'
                          <tr>
                              <td colspan="'.$colspan.'"><b>-TOTAL GASTOS DE ADMINISTRACION</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosAdministracionAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosAdministracionActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosAdministracionDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosAdministracionPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="6"><b>GASTOS DE VENTAS</b></td>
                          </tr>
                              '.$gastosVentaCuerpo.'
                          <tr>
                              <td colspan="'.$colspan.'"><b>-TOTAL GASTOS DE VENTAS</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosVentaAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosVentaActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosVentaDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosVentaPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="6"><b>GASTOS NO OPERACIONALES</b></td>
                          </tr>
                              '.$gastosOperacionalCuerpo.'
                          <tr>
                              <td colspan="'.$colspan.'"><b>-TOTAL GASTOS NO OPERACIONALES</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosOperacionalAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosOperacionalActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosOperacionalDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($gastosOperacionalPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="'.$colspan.'" ><b>UTILIDAD NETA </b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($utilidadNetaAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($utilidadNetaActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($utilidadNetaDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($utilidadNetaPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="6"><b>+ OTROS INGRESOS</b></td>
                          </tr>
                          '.$otrosIngresosCuerpo.'
                          <tr>
                              <td colspan="'.$colspan.'"><b>TOTAL OTROS INGRESOS</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($otrosIngresosAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($otrosIngresosActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($otrosIngresosDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($otrosIngresosPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="6"><b>-OTROS EGRESOS</b></td>
                          </tr>
                              '.$otrosEgresosCuerpo.'
                          <tr>
                              <td colspan="'.$colspan.'"><b>TOTAL OTROS EGRESOS</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($otrosEgresosAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($otrosEgresosActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($otrosEgresosDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($otrosEgresosPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                          <tr>
                              <td colspan="'.$colspan.'"><b>UTILIDAD O PERDIDA</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($utilidadOperdidaAnterior, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($utilidadOperdidaActual, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($utilidadOperdidaDiferencia, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                              <td style="width:90px;text-align:right;border-top:1px solid;"><b>'.number_format($utilidadOperdidaPorcentaje, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                          </tr>
                          <tr><td>&nbsp;</td></tr>
                      </table>';
    }
    else{
        $subtitulo_cabecera.=$tipo_informe.'<br>A nivel de '.$nivel_cuentas;
        $query  =mysql_query($sql,$link);

        if ($id_centro_costos!='') {
            $width   = '35%';
            $td      = '<td style="width:30%;">&nbsp;</td>';
            $colspan = '3';
        }
        else{
            $width   = '65%';
            $td      = '';
            $colspan = '2';
        }

        //RECORREMOS EL RESULTADO DE LA CONSULTA  Y LLENAMOS EL ARRAY CON EL VALOR DEL SALDO Y COMO INDICE
        while ($row=mysql_fetch_array($query)) {
            $td = ($td!='')? '<td style="width:30%;">&nbsp;</td>' : '' ;

            //COSTO DE VENTA
            if (substr($row['cuenta'], 0,1)=='6' || substr($row['cuenta'], 0,1)=='7') {
                $costoVenta       += $row['saldo'];
                $costoVentaCuerpo .= '<tr>
                                            <td style="width:15%;">'.$row['cuenta'].'</td>
                                            <td style="width:'.$width.';">'.$row['descripcion'].' &nbsp;</td>
                                            '.$td.'
                                            <td style="width:20%;text-align:right;">'.number_format($row['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                        </tr>';
            }

            //VENTAS DEL PERIODO
            //SI EL SALDO ES NEGATIVO SE MULTIPLICA POR -1 PARA CONVERTIRLO A POSITIVO Y QUE SE HAGA MAS ENTENDIBLE
            if (substr($row['cuenta'], 0,2)=='41') {

                if (substr($row['cuenta'], 0,4)=='4175') {
                    $devolucion       += $row['saldo'];
                    $devolucionCuerpo .= '<tr>
                                                <td style="width:15%;">'.$row['cuenta'].'</td>
                                                <td style="width:'.$width.';">'.$row['descripcion'].' &nbsp;</td>
                                                '.$td.'
                                                <td style="width:20%;text-align:right;">'.number_format($row['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                            </tr>';

                 }
                 //SI NO ES LA CUENTA DE DEVOLUCION
                 else{
                    $ventas       += $row['saldo'];
                    $ventasCuerpo .= '<tr>
                                            <td style="width:15%;">'.$row['cuenta'].'</td>
                                            <td style="width:'.$width.';">'.$row['descripcion'].' &nbsp;</td>
                                            '.$td.'
                                            <td style="width:20%;text-align:right;">'.number_format($row['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                        </tr>';

                 }
            }

            //SI ES UN GASTO DE ADMINISTRACION
            if (substr($row['cuenta'], 0,2)=='51') {

                $td = ($id_centro_costos!='')? '<td style="width:30%;font-style: italic;">'.$row['centro_costos'].' &nbsp;</td>': '';

                $gastosAdministracion       += $row['saldo'];
                $gastosAdministracionCuerpo .= '<tr>
                                                    <td style="width:15%;">'.$row['cuenta'].'</td>
                                                    <td style="width:'.$width.';">'.$row['descripcion'].' &nbsp;</td>
                                                    '.$td.'
                                                    <td style="width:20%;text-align:right;">'.number_format($row['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                                </tr>';

            }
            //SI ES UN GASTO DE VENTA
            if (substr($row['cuenta'], 0,2)=='52') {
                if ($id_centro_costos!='') {
                    $width = '35%';
                    $td    = '<td style="width:30%;font-style: italic;">'.$row['centro_costos'].' &nbsp;</td>';
                }

                $gastosVenta       += $row['saldo'];
                $gastosVentaCuerpo .= '<tr>
                                            <td style="width:15%;">'.$row['cuenta'].'</td>
                                            <td style="width:'.$width.';">'.$row['descripcion'].' &nbsp;</td>
                                            '.$td.'
                                            <td style="width:20%;text-align:right;">'.number_format($row['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                        </tr>';

            }
            //SI ES UN GASTO NO OPERACIONAL
            if (substr($row['cuenta'], 0,2)=='53') {
                if ($id_centro_costos!='') {
                    $width = '35%';
                    $td    = '<td style="width:30%;font-style: italic;">'.$row['centro_costos'].' &nbsp;</td>';
                }

                $gastosOperacional       += $row['saldo'];
                $gastosOperacionalCuerpo .= '<tr>
                                                <td style="width:15%;">'.$row['cuenta'].'</td>
                                                <td style="width:'.$width.';">'.$row['descripcion'].' &nbsp;</td>
                                                '.$td.'
                                                <td style="width:20%;text-align:right;">'.number_format($row['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                            </tr>';

            }
            //SI ES OTROS INGRESOS
            if (substr($row['cuenta'], 0,2)=='42') {

                $otrosIngresos       += $row['saldo'];
                $otrosIngresosCuerpo .= '<tr>
                                            <td style="width:15%;">'.$row['cuenta'].'</td>
                                            <td style="width:'.$width.';">'.$row['descripcion'].' &nbsp;</td>
                                            '.$td.'
                                            <td style="width:20%;text-align:right;">'.number_format($row['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                        </tr>';
            }
            //OTROS EGRESOS
            if (substr($row['cuenta'], 0,2)=='54' || substr($row['cuenta'], 0,2)=='59') {

                $otrosEgresos       += $row['saldo'];
                $otrosEgresosCuerpo .= '<tr>
                                            <td style="width:15%;">'.$row['cuenta'].'</td>
                                            <td style="width:'.$width.';">'.$row['descripcion'].' &nbsp;</td>
                                            '.$td.'
                                            <td style="width:20%;text-align:right;">'.number_format($row['saldo'], $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</td>
                                        </tr>';

            }

        }

        $ventasNetas      = $ventas + $devolucion;
        $margenBruto      = $ventasNetas + $costoVenta;
        $utilidadNeta     = $margenBruto + $gastosAdministracion + $gastosVenta + $gastosOperacional;
        $utilidadOperdida = ($utilidadNeta + $otrosIngresos) + $otrosEgresos;

        $cuerpoInforme.='<table style="width:90%;font-size:11px;" >
                            <tr>
                                <td colspan="'.$colspan.'"><b>VENTAS DEL PERIODO</b></td>
                            </tr>
                            '.$ventasCuerpo.'
                            <tr>
                                <td colspan="'.$colspan.'"><b>TOTAL VENTAS DEL PERIODO </b></td>
                                <td style="text-align:right;"><b>'.number_format($ventas, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'" ><b>DEVOLUCIONES EN VENTA</b></td>
                            </tr>
                                '.$devolucionCuerpo.'
                            <tr>
                                <td colspan="'.$colspan.'"><b>-TOTAL DEVOLUCIONES EN VENTA</b> </td>
                                <td style="text-align:right;"><b>'.number_format($devolucion, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>VENTAS NETAS</b></td>
                                <td style="text-align:right;"><b>'.number_format($ventasNetas, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>COSTOS DE VENTAS</b></td>
                            </tr>
                                '.$costoVentaCuerpo.'
                            <tr>
                                <td colspan="'.$colspan.'"><b>-TOTAL COSTOS DE VENTAS<b></td>
                                <td style="text-align:right;"><b>'.number_format($costoVenta, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'<b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>MARGEN BRUTO</b></td>
                                <td style="text-align:right;"><b>'.number_format($margenBruto, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>GASTOS DE ADMINISTRACION</b></td>
                            </tr>
                                '.$gastosAdministracionCuerpo.'
                            <tr>
                                <td colspan="'.$colspan.'"><b>-TOTAL GASTOS DE ADMINISTRACION</b></td>
                                <td style="text-align:right;"><b>'.number_format($gastosAdministracion, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>GASTOS DE VENTAS</b></td>
                            </tr>
                                '.$gastosVentaCuerpo.'
                            <tr>
                                <td colspan="'.$colspan.'"><b>-TOTAL GASTOS DE VENTAS</b></td>
                                <td style="text-align:right;"><b>'.number_format($gastosVenta, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>GASTOS NO OPERACIONALES</b></td>
                            </tr>
                                '.$gastosOperacionalCuerpo.'
                            <tr>
                                <td colspan="'.$colspan.'"><b>-TOTAL GASTOS NO OPERACIONALES</b></td>
                                <td style="text-align:right;"><b>'.number_format($gastosOperacional, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'" ><b>UTILIDAD NETA </b></td>
                                <td style="text-align:right;"><b>'.number_format($utilidadNeta, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>+ OTROS INGRESOS</b></td>
                            </tr>
                            '.$otrosIngresosCuerpo.'
                            <tr>
                                <td colspan="'.$colspan.'"><b>TOTAL OTROS INGRESOS</b></td>
                                <td style="text-align:right;"><b>'.number_format($otrosIngresos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>-OTROS EGRESOS</b></td>
                            </tr>
                                '.$otrosEgresosCuerpo.'
                            <tr>
                                <td colspan="'.$colspan.'"><b>TOTAL OTROS EGRESOS</b></td>
                                <td style="text-align:right;"><b>'.number_format($otrosEgresos, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="'.$colspan.'"><b>UTILIDAD O PERDIDA</b></td>
                                <td style="text-align:right;"><b>'.number_format($utilidadOperdida, $_SESSION['DECIMALESMONEDA'],$separador_decimales,$separador_miles).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                        </table>';

    }

?>
<style>
	.my_informe_Contenedor_Titulo_informe{
    float         :	left;
    width         :	100%;
    border-bottom :	1px solid #CCC;
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
    width         :	220px;
    padding       :	0 0 0 5px;
    white-space   : nowrap;
    overflow      : hidden;
    text-overflow : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
    float     :	left;
    width     :	100%;
    font-size :	16px;
    font-weight:bold;
	}
  .titulo_informe_empresa{
    float     : left;
    width     : 100%;
    font-size : 16px;
    font-weight:bold;
  }
  .defaultFont{ font-size : 11px; }
  .labelResult{font-weight:bold;font-size: 11px; }
  .labelResult2{font-weight:bold;font-size: 10px; }
</style>
<body>
  <div class="my_informe_Contenedor_Titulo_informe" style=" width:100%">
    <div style=" width:100%">
      <div style="width:100%; text-align:center">
        <table align="center" style="text-align:center;" >
          <tr><td class="titulo_informe_empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
          <tr><td  style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
          <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></td></tr>
          <tr><td style="font-size:11px; text-align:center;" ><?php echo $subtitulo_cabecera; ?> </td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="my_informe_Contenedor_Titulo_informe">
    <div style="width:100%;margin-top:5px;margin-left:10px;">
      <?php echo $cuerpoInforme; ?>
    </div>
  </div>
</body>
<script><?php echo $script; ?></script>

<?php
	$texto = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
  else{ $HOJA = 'LETTER'; }

	if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
  else{ $MS=10; $MD=2; $MI=5; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12 ; }
	if($IMPRIME_PDF == 'true'){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
			'utf-8',  		// mode - default ''
			$HOJA,		    // format - A4, for example, default ''
			12,				    // font size - default 0
			'',				    // default font family
			$MI,			    // margin_left
			$MD,			    // margin right
			$MS,			    // margin top
			$ML,			    // margin bottom
			10,				    // margin header
			10,				    // margin footer
			$ORIENTACION	// L - landscape, P - portrait
		);

    $mpdf->SetProtection(array('print'));
    $mpdf->useSubstitutions = true;
    $mpdf->simpleTables     = true;
    $mpdf->packTableData    = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHeader("");
    $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA == 'true'){$mpdf->Output($documento.".pdf",'D'); }
    else{ $mpdf->Output($documento.".pdf",'I'); }
		exit;
	}
  else{
    echo $texto;
  }
?>
