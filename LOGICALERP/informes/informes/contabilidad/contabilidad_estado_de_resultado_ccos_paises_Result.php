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

  $id_empresa             = $_SESSION['EMPRESA'];
  $divTitleSucursal       = '';
  $whereSucursal          = '';
  $whereFecha             = '';
  $groupBy                = '';
  $whereIdCentroCostos    = '';

  //SALDOS
  $acumuladoDebe          = 0;
  $acumuladoHaber         = 0;
  $acumuladoSaldoAnterior = 0;
  $acumuladoSaldoActual   = 0;
  $script                 = '';

    if(!isset($MyInformeFiltroFechaFinal) || $MyInformeFiltroFechaFinal == ''){
      $tipo_balance_EstadoResultado = 'Resumido';
      $MyInformeFiltroFechaFinal = date("Y-m-d");
      $MyInformeFiltroFechaInicio = date("Y-m").'-01';
      $script =  'localStorage.MyInformeFiltroFechaFinalEstadoResultado  = "";
                  localStorage.MyInformeFiltroFechaInicioEstadoResultado = "";
                  localStorage.tipo_balance_EstadoResultado              = "";
                  localStorage.nivel_cuentas_EstadoResultado             = "";
                  localStorage.sucursales_estado_resultado               = "";
                  arrayCentroCostos.length                               = 0;
                  arrayCodigosCentroCostos.length                        = 0;
                  centroCostosConfigurados.length                        = 0;';

      $whereFecha    = 'AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
      $whereNiveles  = ' AND AC.codigo_cuenta = puc.cuenta ';
      $groupByOpcion = 'GROUP BY puc.cuenta'.$groupBy;
    }
    else{
        //NIVELES A GENERAR EL INFORME
        $sql = "SELECT digitos FROM puc_configuracion WHERE activo = 1 AND id_empresa = $_SESSION[EMPRESA] AND nombre = '$generar'";
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
            //EN PROCESO
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

        // FILTRO POR CENTRO DE COSTO
        if (isset($centro_costos) && $centro_costos<>'[]'){
            $tipo_informe.='<br>Por Centros de Costos';
            $groupByOpcion = 'GROUP BY puc.cuenta'.$groupBy.',AC.codigo_centro_costos';

            // TODOS LOS CENTROS DE COSTOS
            if ($centro_costos=='todos') {
                $sql="SELECT id FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa";
                $query=$mysql->query($sql,$mysql->link);
                while ($row=$mysql->fetch_array($query)) {
                    $whereIdCentroCostos.=($whereIdCentroCostos!='')? " OR AC.id_centro_costos = '$row[id]%' " : " AND ( AC.id_centro_costos = '$row[id]%' " ;
                }
            }
            // CENTROS DE COSTOS SELECCIONADOS POR EL USUARIO
            else{
                $centro_costos = json_decode($centro_costos);
                foreach ($centro_costos as $indice => $id_centro_costo) {
                    $whereIdCentroCostos.=($whereIdCentroCostos!='')? " OR AC.id_centro_costos = '$id_centro_costo%' " : " AND ( AC.id_centro_costos = '$id_centro_costo%' " ;
                }
            }

            $whereIdCentroCostos .= ")";
        }
        else{
            echo "<b>NO SELECCIONO NINGUN CENTRO DE COSTOS</b>";
            exit;
        }

        // FILTRO POR TERCEROS
        if (isset($terceros) && $terceros<>'[]' ) {
            $tipo_informe    .='<br>Por Terceros';
            $groupByOpcion   .= ',AC.id_tercero';
            $mostrar_tercero = 'true';

            // FILTRO POR TODOS LOS TERCEROS
            if ($terceros=='todos') {
               $sql="SELECT id FROM terceros WHERE activo=1 AND id_empresa=$id_empresa";
               $query=$mysql->query($sql,$mysql->link);
               while ($row=$mysql->fetch_array($query)) {
                    $whereIdTerceros.=($whereIdTerceros!='')? " OR AC.id_tercero = '$row[id]' " : " AND ( AC.id_tercero = '$row[id]' " ;
                }
            }
            // TERCEROS SELCCIONADOS POR EL USUARIO
            else{
                $terceros = json_decode($terceros);
                foreach ($terceros as $indice => $id_tercero) {
                    $whereIdTerceros.=($whereIdTerceros!='')? " OR AC.id_tercero = $id_tercero " : " AND ( AC.id_tercero = $id_tercero" ;
                }
            }

            $whereIdTerceros .= ")";
        }

    }

    //FILTRO POR SUCURSAL
    if ($sucursal!="") {
        if ($sucursal!='global') {
            $whereSucursal = ' AND AC.id_sucursal='.$sucursal;
            $subtitulo_cabecera .= '<b>Sucursal</b> '.$_SESSION['NOMBRESUCURSAL'].'<br>';
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
    $colspan = ($mostrar_tercero=='true')? '5' : '3';

    // CONSULTAR LA CUENTA DE CIERRE
    $sql="SELECT cuenta FROM puc WHERE activo=1 AND id_empresa=$id_empresa AND tipo='cuenta_cierre' ";
    $query=$mysql->query($sql,$mysql->link);
    $whereCuentaCierre = ($mysql->result($query,0,'cuenta')<>'')? " AND codigo_cuenta<> '".$mysql->result($query,0,'cuenta')."' " : '' ;

    //CADENA CON LA CONSULTA
    echo $sql   = "SELECT
                SUM(AC.debe - AC.haber) AS saldo,
                puc.cuenta,
                puc.descripcion,
                AC.centro_costos,
                AC.codigo_centro_costos,
                AC.id_tercero,
                AC.nit_tercero,
                AC.tercero
                $campos_select
            FROM
                asientos_colgaap AS AC,puc
            WHERE
                AC.activo=1
                AND puc.activo=1
                AND AC.id_empresa=$id_empresa
                AND AC.tipo_documento<>'NCC'
                AND puc.id_empresa=$id_empresa
                AND  ( AC.codigo_cuenta LIKE '6%'
                    OR AC.codigo_cuenta LIKE '7%'
                    OR AC.codigo_cuenta LIKE '41%'
                    OR AC.codigo_cuenta LIKE '51%'
                    OR AC.codigo_cuenta LIKE '52%'
                    OR AC.codigo_cuenta LIKE '53%'
                    OR AC.codigo_cuenta LIKE '42%'
                    OR AC.codigo_cuenta LIKE '54%'
                    OR AC.codigo_cuenta LIKE '59%')
                $whereCuentaCierre
                $whereNiveles
                $whereFecha
                $whereIdCentroCostos
                $whereIdTerceros
                $whereSucursal
                $groupByOpcion
                ORDER BY
                    CAST(AC.codigo_cuenta AS CHAR) ASC";

    if ($tipo_balance_EstadoResultado=='Resumido') {
        $subtitulo_cabecera.='Mensual<br>de '.$MyInformeFiltroFechaInicio.' a '.$MyInformeFiltroFechaFinal.' <br>Resumido';
        $query = mysql_query($sql,$link);

        while ($row=mysql_fetch_array($query)) {
            $cuenta        = $row['cuenta'];
            $saldo         = $row['saldo'];
            $centro_costos = $row['codigo_centro_costos'];
            $id_tercero    = $row['id_tercero'];

            //SI ES UN COSTO DE VENTA
            if (substr($cuenta, 0,1)=='6' || substr($cuenta, 0,1)=='7') { $arraySaldoCV2[$centro_costos] += $saldo; $arrayTerceroSaldoCV2[$centro_costos][$id_tercero] += $saldo; $saldoCV2 += $saldo;}
            // if (substr($cuenta, 0,1)=='7') { $saldoCV2[$centro_costos] += $saldo; }

            //VENTAS DEL PERIODO
            if (substr($cuenta, 0,2)=='41') {
                if (substr($cuenta, 0,4)=='4175') { $devolucionIO += $saldo; }                //FILTRAMOS LA CUENTA DE DEVOLUCION QUE ES LA 4175
                else{ $arraySaldoIO2[$centro_costos] += $saldo; $arrayTerceroSaldoIO2[$centro_costos][$id_tercero]  += $saldo; $saldoIO2+= $saldo;}                //SI NO ES LA CUENTA DE DEVOLUCION
            }

            //SI ES UN GASTO DE ADMINISTRACION
            if (substr($cuenta, 0,2)=='51') { $arraySaldoGOA2[$centro_costos] += $saldo; $arrayTerceroSaldoGOA2[$centro_costos][$id_tercero]  += $saldo; $saldoGOA2+= $saldo; }
            //SI ES UN GASTO DE VENTA
            if (substr($cuenta, 0,2)=='52') { $arraySaldoGOV2[$centro_costos] += $saldo; $arrayTerceroSaldoGOV2[$centro_costos][$id_tercero] += $saldo; $saldoGOV2+= $saldo; }
            //SI ES UN GASTO FINANCIERO
            if (substr($cuenta, 0,2)=='53') { $arraySaldoIGNOGF2[$centro_costos] += $saldo; $arrayTerceroSaldoIGNOGF2[$centro_costos][$id_tercero] += $saldo; $saldoIGNOGF2 += $saldo; }
            //SI ES OTROS INGRESOS
            if (substr($cuenta, 0,2)=='42') { $arraySaldoIGNOOI2[$centro_costos] += $saldo; $arrayTerceroSaldoIGNOOI2[$centro_costos][$id_tercero] += $saldo; $saldoIGNOOI2 += $saldo; }
            //SI ES IMPUESTO SOBRE LA RENTA
            if (substr($cuenta, 0,2)=='54' || substr($cuenta, 0,2)=='59') { $arraySaldoIR2[$centro_costos] += $saldo; $arraySaldoIR2[$centro_costos][$id_tercero] += $saldo; $saldoIR2 += $saldo; }

        }

        //utilidad bruta en ventas
        $saldoIO2 += $devolucionIO;
        $utilidadBrutaVentas2 = $saldoIO2 + $saldoCV2;

        //gastos operacionales
        $gastosOperacionales2 = $saldoGOA2 + $saldoGOV2;

        //utilidad operacional
        $utilidadOperacional2 = $utilidadBrutaVentas2 + $gastosOperacionales2;



        $cuerpoInforme='<table style="width:80%;font-size:11px;">
                        <tr>
                            <td style="width:50%;"><b> INGRESOS OPERACIONALES </b></td> <td style="width:30%;text-align:right;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:50%;"> INGRESOS OPERACIONALES </td> <td style="width:30%;text-align:right;">'.validar_numero_formato($saldoIO2, $IMPRIME_XLS).'&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:50%;">  -COSTO DE VENTAS</td><td style="width:30%;text-align:right;">'.validar_numero_formato($saldoCV2, $IMPRIME_XLS).'&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:50%;"><b>UTILIDAD BRUTA EN VENTAS</b></td><td style="width:30%;text-align:right;"><b>'.validar_numero_formato($utilidadBrutaVentas2, $IMPRIME_XLS).'&nbsp;</b></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td style="width:50%;"><b>-GASTOS OPERACIONALES </b></td><td style="width:30%;text-align:right;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:50%;">ADMINISTRACION</td><td style="width:30%;text-align:right;">'.validar_numero_formato($saldoGOA2, $IMPRIME_XLS).'&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:50%;">VENTAS</td><td style="width:30%;text-align:right;">'.validar_numero_formato($saldoGOV2, $IMPRIME_XLS).'&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:50%;"> GASTOS OPERACIONALES &nbsp;</td><td style="width:30%;text-align:right;"> '.validar_numero_formato($gastosOperacionales2, $IMPRIME_XLS).'&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:50%;"><b>UTILIDAD OPERACIONAL</b></td><td style="width:30%;text-align:right;"><b>'.validar_numero_formato($utilidadOperacional2, $IMPRIME_XLS).'&nbsp;</b></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td style="width:50%;"><b>INGRESOS Y GASTOS NO OPERACIONALES </b></td><td style="width:30%;text-align:right;"></td>
                        </tr>
                        <tr>
                            <td style="width:50%;">-GASTOS FINANCIEROS</td><td style="width:30%;text-align:right;"> '.validar_numero_formato($saldoIGNOGF2, $IMPRIME_XLS).'&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:50%;">+OTROS INGRESOS </td><td style="width:30%;text-align:right;"> '.validar_numero_formato($saldoIGNOOI2, $IMPRIME_XLS).'&nbsp;</td>
                        </tr> ';


            $utilidadOperacional  += $saldoIGNOGF;
            $utilidadOperacional  += $saldoIGNOOI;

            $utilidadOperacional2 += $saldoIGNOGF2;
            $utilidadOperacional2 += $saldoIGNOOI2;

            $cuerpoInforme .= '<tr>
                                    <td style="width:50%;"><b> UTILIDAD ANTES DE IMPUESTO</b></td><td style="width:30%;text-align:right;"><b>'.validar_numero_formato($utilidadOperacional2, $IMPRIME_XLS).'&nbsp;</b></td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr>
                                    <td style="width:50%;">IMPUESTO SOBRE LA RENTA</td><td style="width:30%;text-align:right;">'.validar_numero_formato($saldoIR2, $IMPRIME_XLS).'&nbsp;</td>
                                </tr>';

            $utilidadOperacional  += $saldoIR;
            $utilidadOperacional2 += $saldoIR2;

            $cuerpoInforme .= '<tr>
                                    <td style="width:50%;">UTILIDAD NETA</td><td style="width:30%;text-align:right;">'.validar_numero_formato($utilidadOperacional2, $IMPRIME_XLS).'&nbsp;</td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr>
                                    <td style="width:50%;"><b>UTILIDAD O PERDIDA</b></td><td style="width:30%;text-align:right;"><b>'.validar_numero_formato($utilidadOperacional2, $IMPRIME_XLS).'&nbsp;</b></td>
                                </tr>
                            </table>';
    }
    else if($tipo_balance_EstadoResultado=='comparativo_mensual' || $tipo_balance_EstadoResultado=='comparativo_anual'){
        $subtitulo_cabecera.=$tipo_informe.'<br>A nivel de '.$generar;
        $query = mysql_query($sql,$link);

        // $colspan = '3';
        $cuenta_head = '';
        $desc_cuenta_head = '';
        $anio_head   = '';
        $mes_head    = '';
        //RECORREMOS EL RESULTADO DE LA CONSULTA  Y LLENAMOS EL ARRAY CON EL VALOR DEL SALDO Y COMO INDICE
        while ($row=mysql_fetch_array($query)) {
            $anio          = $row['anio'];
            $mes           = $row['mes'];
            $cuenta        = $row['cuenta'];
            $saldo         = $row['saldo'];
            $centro_costos = $row['codigo_centro_costos'];
            $id_tercero    = $row['id_tercero'];




            if ($tipo_balance_EstadoResultado=='comparativo_mensual') {

                // if ($cuenta_head!=$cuenta ) {
                //     if ($cuenta_head!="") {
                //         $arrayDatosCuentas[$cuenta_head.' '][' '][$anio_head][$mes_head] = $acumCuenta[$cuenta_head][$anio_head][$mes_head];
                //         $arrayCuentas[$cuenta_head.' '] = array('descripcion' => $desc_cuenta_head,'class'=>'class="subtotal"');
                //         // echo $cuenta.' - '.$anio_head.' - '.$mes_head.' = '.$acumCuenta[$cuenta][$anio_head][$mes_head]." <br>";
                //         echo "cuenta_head :$cuenta_head - cuenta: $cuenta - acumCuenta: ".$acumCuenta[$cuenta_head][$anio_head][$mes_head]." - anio $anio_head - mes $mes_head <br>";
                //         // print_r($acumCuenta);
                //         // $acumCuenta  = 0;
                //     }
                //     $cuenta_head      = $cuenta ;
                //     $desc_cuenta_head = $row['descripcion'] ;
                //     $anio_head        = $anio;
                //     $mes_head         = $mes;
                // }

                $arrayDatosCuentas[$cuenta][$centro_costos][$anio][$mes]= $row['saldo'];
                // $arrayTerceroDatosCuentas[$cuenta][$centro_costos][$anio][$mes][$id_tercero]= $row['saldo'];
            }
            else{
                // if ($cuenta_head!=$cuenta ) {
                //     if ($cuenta_head!="") {
                //         $arrayDatosCuentas[$cuenta_head.' '][' '][$anio_head] = $acumCuentaAnio[$cuenta_head][$anio_head];
                //         $arrayCuentas[$cuenta_head.' '] = array('descripcion' => $desc_cuenta_head,'class'=>'class="subtotal"');
                //         // $acumCuenta  = 0;
                //     }
                //     $cuenta_head = $cuenta ;
                //     $desc_cuenta_head = $row['descripcion'] ;
                //     $anio_head        = $anio;
                // }

                $arrayDatosCuentas[$cuenta][$centro_costos][$anio] = $row['saldo'];
                // $arrayTerceroDatosCuentas[$cuenta][$centro_costos][$anio][$id_tercero] = $row['saldo'];
            }



            // $acumCuenta[$cuenta][$anio][$mes] += $row['saldo'];
            // $acumCuentaAnio[$cuenta][$anio]   += $row['saldo'];

            $arrayCuentas[$cuenta] = array('descripcion' => $row['descripcion']);
        }

        $arrayCuentas[$cuenta.' '] = array('descripcion' => $row['descripcion'],'class'=>'class="subtotal"');


        //FECHAS DE LOS MESES PARA LA COMPARACION
        $fechaAnterior = explode('-', $MyInformeFiltroFechaInicio);
        $fechaActual   = explode('-', $MyInformeFiltroFechaFinal);

        $anioAnterior = $fechaAnterior[0];
        $mesAnterior  = $fechaAnterior[1]*1;

        $anioActual   = $fechaActual[0];
        $mesActual    = $fechaActual[1]*1;

        //SI EL INFORME ES POR CENTROS DE COSTO
        // if ($centro_costos!='') {
            foreach ($arrayDatosCuentas as $cuenta => $arrayCentroCostos) {
                foreach ($arrayCentroCostos as $centro_costo => $saldo) {

                    //COSTO DE VENTA
                    if (substr($cuenta, 0,1)=='6' || substr($cuenta, 0,1)=='7') {

                        if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                            $costoVentaAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior][$mesAnterior] : 0;
                            $costoVentaActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual][$mesActual] : 0;

                            $costoVentaAnteriorUnico =$saldo[$anioAnterior][$mesAnterior];
                            $costoVentaActualUnico   =$saldo[$anioActual][$mesActual];
                        }
                        else{
                            $costoVentaAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior] : 0;
                            $costoVentaActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual] : 0;

                            $costoVentaAnteriorUnico =$saldo[$anioAnterior];
                            $costoVentaActualUnico   =$saldo[$anioActual];
                        }

                        $diferencia = $costoVentaActualUnico - $costoVentaAnteriorUnico;
                        $porcentaje = ($costoVentaActualUnico/$costoVentaAnteriorUnico)-1;

                        $costoVentaCuerpo.='<tr '.$arrayCuentas[$cuenta]['class'].'>
                                                <td >'.$cuenta.'</td>
                                                <td >'.$arrayCuentas[$cuenta]['descripcion'].' &nbsp;</td>
                                                <td style="text-align:center;">'.$centro_costo.'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($costoVentaAnteriorUnico, $IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($costoVentaActualUnico, $IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($diferencia,$IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($porcentaje,$IMPRIME_XLS).'</td>
                                            </tr>';


                    }

                    //VENTAS DEL PERIODO
                    if (substr($cuenta, 0,2)=='41') {

                        if (substr($cuenta, 0,4)=='4175') {
                            if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                $devolucionAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior][$mesAnterior] : 0;
                                $devolucionActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual][$mesActual] : 0;

                                $devolucionAnteriorUnico =$saldo[$anioAnterior][$mesAnterior];
                                $devolucionActualUnico   =$saldo[$anioActual][$mesActual];
                            }
                            else{
                                $devolucionAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior] : 0;
                                $devolucionActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual] : 0;

                                $devolucionAnteriorUnico =$saldo[$anioAnterior];
                                $devolucionActualUnico   =$saldo[$anioActual];
                            }


                            $diferencia = $devolucionActualUnico - $devolucionAnteriorUnico;
                            $porcentaje = ($devolucionActualUnico/$devolucionAnteriorUnico)-1;

                            $devolucionCuerpo.='<tr '.$arrayCuentas[$cuenta]['class'].'>
                                                <td >'.$cuenta.'</td>
                                                <td >'.$arrayCuentas[$cuenta]['descripcion'].' &nbsp;</td>
                                                <td style="text-align:center;">'.$centro_costo.'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($devolucionAnteriorUnico, $IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($devolucionActualUnico, $IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($diferencia,$IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($porcentaje, $IMPRIME_XLS).'</td>
                                            </tr>';

                         }
                         //SI NO ES LA CUENTA DE DEVOLUCION
                         else{
                            if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                $ventasAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior][$mesAnterior] : 0;
                                $ventasActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual][$mesActual] : 0;

                                $ventasAnteriorUnico =$saldo[$anioAnterior][$mesAnterior];
                                $ventasActualUnico   =$saldo[$anioActual][$mesActual];
                            }
                            else{
                                $ventasAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior] : 0;
                                $ventasActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual] : 0;

                                $ventasAnteriorUnico =$saldo[$anioAnterior];
                                $ventasActualUnico   =$saldo[$anioActual];
                            }

                            $diferencia = $ventasActualUnico - $ventasAnteriorUnico;
                            $porcentaje = ($ventasActualUnico/$ventasAnteriorUnico)-1;

                            $ventasCuerpo  .= '<tr '.$arrayCuentas[$cuenta]['class'].'>
                                                    <td >'.$cuenta.'</td>
                                                    <td style="width:'.$width.';overflow:hidden;" >'.$arrayCuentas[$cuenta]['descripcion'].' &nbsp;</td>
                                                    <td style="text-align:center;">'.$centro_costo.'</td>
                                                    <td style="text-align:right;">'.validar_numero_formato($ventasAnteriorUnico, $IMPRIME_XLS).'</td>
                                                    <td style="text-align:right;">'.validar_numero_formato($ventasActualUnico, $IMPRIME_XLS).'</td>
                                                    <td style="text-align:right;">'.validar_numero_formato($diferencia,$IMPRIME_XLS).'</td>
                                                    <td style="text-align:right;">'.validar_numero_formato($porcentaje, $IMPRIME_XLS).'</td>
                                                </tr>';
                        }
                    }

                    //SI ES UN GASTO DE ADMINISTRACION
                    if (substr($cuenta, 0,2)=='51') {
                        if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                            $gastosAdministracionAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior][$mesAnterior] : 0;
                            $gastosAdministracionActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual][$mesActual] : 0;

                            $gastosAdministracionAnteriorUnico =$saldo[$anioAnterior][$mesAnterior];
                            $gastosAdministracionActualUnico   =$saldo[$anioActual][$mesActual];
                        }
                        else{
                            $gastosAdministracionAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior] : 0;
                            $gastosAdministracionActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual] : 0;

                            $gastosAdministracionAnteriorUnico =$saldo[$anioAnterior];
                            $gastosAdministracionActualUnico   =$saldo[$anioActual];
                        }

                        $diferencia = $gastosAdministracionActualUnico - $gastosAdministracionAnteriorUnico;
                        $porcentaje = ($gastosAdministracionActualUnico/$gastosAdministracionAnteriorUnico)-1;

                        $gastosAdministracionCuerpo .= '<tr '.$arrayCuentas[$cuenta]['class'].'>
                                                            <td >'.$cuenta.'</td>
                                                            <td >'.$arrayCuentas[$cuenta]['descripcion'].' &nbsp;</td>
                                                            <td style="text-align:center;">'.$centro_costo.'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($gastosAdministracionAnteriorUnico, $IMPRIME_XLS).'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($gastosAdministracionActualUnico, $IMPRIME_XLS).'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($diferencia,$IMPRIME_XLS).'</td>
                                                            <td style="text-align:right;">'.validar_numero_formato($porcentaje, $IMPRIME_XLS).'</td>
                                                        </tr>';
                    }

                    //SI ES UN GASTO DE VENTA
                    if (substr($cuenta, 0,2)=='52') {
                        if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                            $gastosVentaAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior][$mesAnterior] : 0;
                            $gastosVentaActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual][$mesActual] : 0;

                            $gastosVentaAnteriorUnico =$saldo[$anioAnterior][$mesAnterior];
                            $gastosVentaActualUnico   =$saldo[$anioActual][$mesActual];
                        }
                        else{
                            $gastosVentaAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior] : 0;
                            $gastosVentaActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual] : 0;

                            $gastosVentaAnteriorUnico =$saldo[$anioAnterior];
                            $gastosVentaActualUnico   =$saldo[$anioActual];
                        }

                        $diferencia = $gastosVentaActualUnico - $gastosVentaAnteriorUnico;
                        $porcentaje = ($gastosVentaActualUnico/$gastosVentaAnteriorUnico)-1;

                        $gastosVentaCuerpo.='<tr '.$arrayCuentas[$cuenta]['class'].'>
                                                <td >'.$cuenta.'</td>
                                                <td >'.$arrayCuentas[$cuenta]['descripcion'].' &nbsp;</td>
                                                <td style="text-align:center;">'.$centro_costo.'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($gastosVentaAnteriorUnico, $IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($gastosVentaActualUnico, $IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($diferencia,$IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($porcentaje, $IMPRIME_XLS).'</td>
                                            </tr>';
                    }

                    //SI ES UN GASTO NO OPERACIONAL
                    if (substr($cuenta, 0,2)=='53') {
                        if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                            $gastosOperacionalAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior][$mesAnterior] : 0;
                            $gastosOperacionalActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual][$mesActual] : 0;

                            $gastosOperacionalAnteriorUnico =$saldo[$anioAnterior][$mesAnterior];
                            $gastosOperacionalActualUnico   =$saldo[$anioActual][$mesActual];
                        }
                        else{
                            $gastosOperacionalAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior] : 0;
                            $gastosOperacionalActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual] : 0;

                            $gastosOperacionalAnteriorUnico =$saldo[$anioAnterior];
                            $gastosOperacionalActualUnico   =$saldo[$anioActual];
                        }

                        $diferencia = $gastosOperacionalActualUnico - $gastosOperacionalAnteriorUnico;
                        $porcentaje = ($gastosOperacionalActualUnico/$gastosOperacionalAnteriorUnico)-1;

                        $gastosOperacionalCuerpo .= '<tr '.$arrayCuentas[$cuenta]['class'].'>
                                                        <td >'.$cuenta.'</td>
                                                        <td >'.$arrayCuentas[$cuenta]['descripcion'].' &nbsp;</td>
                                                        <td style="text-align:center;">'.$centro_costo.'</td>
                                                        <td style="text-align:right;">'.validar_numero_formato($gastosOperacionalAnteriorUnico, $IMPRIME_XLS).'</td>
                                                        <td style="text-align:right;">'.validar_numero_formato($gastosOperacionalActualUnico, $IMPRIME_XLS).'</td>
                                                        <td style="text-align:right;">'.validar_numero_formato($diferencia,$IMPRIME_XLS).'</td>
                                                        <td style="text-align:right;">'.validar_numero_formato($porcentaje, $IMPRIME_XLS).'</td>
                                                    </tr>';
                    }

                    //SI ES OTROS INGRESOS
                    if (substr($cuenta, 0,2)=='42') {
                        if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                            $otrosIngresosAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior][$mesAnterior] : 0;
                            $otrosIngresosActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual][$mesActual] : 0;

                            $otrosIngresosAnteriorUnico =$saldo[$anioAnterior][$mesAnterior];
                            $otrosIngresosActualUnico   =$saldo[$anioActual][$mesActual];
                        }
                        else{
                            $otrosIngresosAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior] : 0;
                            $otrosIngresosActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual] : 0;

                            $otrosIngresosAnteriorUnico =$saldo[$anioAnterior];
                            $otrosIngresosActualUnico   =$saldo[$anioActual];
                        }

                        $diferencia = $otrosIngresosActualUnico - $otrosIngresosAnteriorUnico;
                        $porcentaje = ($otrosIngresosActualUnico/$otrosIngresosAnteriorUnico)-1;

                        $otrosIngresosCuerpo.='<tr '.$arrayCuentas[$cuenta]['class'].'>
                                                <td >'.$cuenta.'</td>
                                                <td >'.$arrayCuentas[$cuenta]['descripcion'].' &nbsp;</td>
                                                <td style="text-align:center;">'.$centro_costo.'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($otrosIngresosAnteriorUnico, $IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($otrosIngresosActualUnico, $IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($diferencia,$IMPRIME_XLS).'</td>
                                                <td style="text-align:right;">'.validar_numero_formato($porcentaje, $IMPRIME_XLS).'</td>
                                            </tr>';
                    }

                    //OTROS EGRESOS
                    if (substr($cuenta, 0,2)=='54' || substr($cuenta, 0,2)=='59') {
                        if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                            $otrosEgresosAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior][$mesAnterior] : 0;
                            $otrosEgresosActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual][$mesActual] : 0;

                            $otrosEgresosAnteriorUnico =$saldo[$anioAnterior][$mesAnterior];
                            $otrosEgresosActualUnico   =$saldo[$anioActual][$mesActual];
                        }
                        else{
                            $otrosEgresosAnterior +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioAnterior] : 0;
                            $otrosEgresosActual   +=($arrayCuentas[$cuenta]['class']=='')? $saldo[$anioActual] : 0;

                            $otrosEgresosAnteriorUnico =$saldo[$anioAnterior];
                            $otrosEgresosActualUnico   =$saldo[$anioActual];
                        }

                        $diferencia = $otrosEgresosActualUnico - $otrosEgresosAnteriorUnico;
                        $porcentaje = ($otrosEgresosActualUnico/$otrosEgresosAnteriorUnico)-1;

                        $otrosEgresosCuerpo  .= '<tr '.$arrayCuentas[$cuenta]['class'].'>
                                                    <td >'.$cuenta.'</td>
                                                    <td >'.$arrayCuentas[$cuenta]['descripcion'].' &nbsp;</td>
                                                    <td style="text-align:center;">'.$centro_costo.'</td>
                                                    <td style="text-align:right;">'.validar_numero_formato($otrosEgresosAnteriorUnico, $IMPRIME_XLS).'</td>
                                                    <td style="text-align:right;">'.validar_numero_formato($otrosEgresosActualUnico, $IMPRIME_XLS).'</td>
                                                    <td style="text-align:right;">'.validar_numero_formato($diferencia,$IMPRIME_XLS).'</td>
                                                    <td style="text-align:right;">'.validar_numero_formato($porcentaje, $IMPRIME_XLS).'</td>
                                                </tr>';
                    }

                }//FOREACH 2
            }//FOREACH 1

        // }

        //ACUMULADOS DE LA EL PERIODO ANTERIOR
        $ventasNetasAnterior            = $ventasAnterior + $devolucionAnterior;
        $margenBrutoAnterior            = $ventasNetasAnterior + $costoVentaAnterior;
        $utilidadNetaAnterior           = $margenBrutoAnterior + $gastosAdministracionAnterior + $gastosVentaAnterior + $gastosOperacionalAnterior;
        $utilidadOperdidaAnterior       = $utilidadNetaAnterior + $otrosIngresosAnterior + $otrosEgresosAnterior;
        //ACUMULADOS DEL PERIODO ACTUAL
        $ventasNetasActual              =$ventasActual + $devolucionActual;
        $margenBrutoActual              =$ventasNetasActual + $costoVentaActual;
        $utilidadNetaActual             =$margenBrutoActual + $gastosAdministracionActual + $gastosVentaActual + $gastosOperacionalActual;
        $utilidadOperdidaActual         =($utilidadNetaActual + $otrosIngresosActual) + $otrosEgresosActual;
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

        $cuerpoInforme.='<table style="width:97%;font-size:11px;" class="table" >
                            <thead>
                                <tr class="total">
                                    <td >Cuenta</td>
                                    <td >Descripcion</td>
                                    <td style="text-align:center;">Centro de <br>Costo</td>
                                    <td style="text-align:right;"><b>Periodo Ante.</b><br><label style="font-size:10px;">'.$MyInformeFiltroFechaInicio.'<br>a<br>'.$fechaFinalAnterior.'</label></td>
                                    <td style="text-align:right;"><b>A la fecha</b><br>'.$fechaInicialActual.'<br>a<br>'.$MyInformeFiltroFechaFinal.'</td>
                                    <td style="text-align:right;"><b>Diferencia</b></td>
                                    <td style="text-align:right;"><b>Porcentaje</b></td>
                                </tr>
                            </thead>
                            <tr class="total">
                                <td colspan="7"><b>VENTAS DEL PERIODO</b></td>
                            </tr>

                            '.$ventasCuerpo.'
                            <tr class="total">
                                <td colspan="3"><b>TOTAL VENTAS DEL PERIODO </b></td>
                                <td style="text-align:right;border-top:1px solid;"><b>'.validar_numero_formato($ventasAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($ventasActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($ventasDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($ventasPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="7" ><b>DEVOLUCIONES EN VENTA</b></td>
                                </tr>
                            </thead>
                                '.$devolucionCuerpo.'
                            <tr class="total">
                                <td colspan="3"><b>-TOTAL DEVOLUCIONES EN VENTA</b> </td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($devolucionAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($devolucionActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($devolucionDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($devolucionPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr class="total">
                                <td colspan="3"><b>VENTAS NETAS</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($ventasNetasAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($ventasNetasActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($ventasNetasDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($ventasNetasPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="7"><b>COSTOS DE VENTAS</b></td>
                                </tr>
                            </thead>
                                '.$costoVentaCuerpo.'
                            <tr class="total">
                                <td colspan="3"><b>-TOTAL COSTOS DE VENTAS<b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($costoVentaAnterior, $IMPRIME_XLS).'<b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($costoVentaActual, $IMPRIME_XLS).'<b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($costoVentaDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($costoVentaPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr class="total">
                                <td colspan="3"><b>MARGEN BRUTO</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($margenBrutoAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($margenBrutoActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($margenBrutoDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($margenBrutoPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="7"><b>GASTOS DE ADMINISTRACION</b></td>
                                </tr>
                            </thead>
                                '.$gastosAdministracionCuerpo.'
                            <tr class="total">
                                <td colspan="3"><b>-TOTAL GASTOS DE ADMINISTRACION</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosAdministracionAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosAdministracionActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosAdministracionDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosAdministracionPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="7"><b>GASTOS DE VENTAS</b></td>
                                </tr>
                            </thead>
                                '.$gastosVentaCuerpo.'
                            <tr class="total">
                                <td colspan="3"><b>-TOTAL GASTOS DE VENTAS</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosVentaAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosVentaActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosVentaDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosVentaPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="7"><b>GASTOS NO OPERACIONALES</b></td>
                                </tr>
                            </thead>
                                '.$gastosOperacionalCuerpo.'
                            <tr class="total">
                                <td colspan="3"><b>-TOTAL GASTOS NO OPERACIONALES</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosOperacionalAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosOperacionalActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosOperacionalDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($gastosOperacionalPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr class="total">
                                <td colspan="3" ><b>UTILIDAD NETA </b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($utilidadNetaAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($utilidadNetaActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($utilidadNetaDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($utilidadNetaPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="7"><b>+ OTROS INGRESOS</b></td>
                                </tr>
                            </thead>
                            '.$otrosIngresosCuerpo.'
                            <tr class="total">
                                <td colspan="3"><b>TOTAL OTROS INGRESOS</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($otrosIngresosAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($otrosIngresosActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($otrosIngresosDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($otrosIngresosPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="7"><b>-OTROS EGRESOS</b></td>
                                </tr>
                            </thead>
                                '.$otrosEgresosCuerpo.'
                            <tr class="total">
                                <td colspan="3"><b>TOTAL OTROS EGRESOS</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($otrosEgresosAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($otrosEgresosActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($otrosEgresosDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($otrosEgresosPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr class="total">
                                <td colspan="3"><b>UTILIDAD O PERDIDA</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($utilidadOperdidaAnterior, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($utilidadOperdidaActual, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($utilidadOperdidaDiferencia, $IMPRIME_XLS).'</b></td>
                                <td style="width:90px;text-align:right;"><b>'.validar_numero_formato($utilidadOperdidaPorcentaje, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                        </table>';
    }
    // INFORME MENSUAL
    else{
        $cuenta_head = '';
        $cuenta_grupo = '';

        $subtitulo_cabecera.=$tipo_informe.'<br>A nivel de '.$generar;
        $query  =$mysql->query($sql,$mysql->link);
        while ($row=$mysql->fetch_array($query)){
            $cuenta = $row['cuenta'];

            //SUBTOTAL POR CUENTA
            if ($cuenta_head!=$cuenta ) {
                if ($cuenta_head!="") {
                    $arrayAsiento[] = array(
                                                'saldo'                => $acumCuenta,
                                                'cuenta'               => $cuenta_head,
                                                'descripcion'          => '',
                                                'centro_costos'        => '',
                                                'codigo_centro_costos' => '',
                                                'id_tercero'           => '',
                                                'nit_tercero'          => '',
                                                'tercero'              => '',
                                                'total'                => 'true',
                                                'class'                => "class='subtotal'",
                                            );
                    $acumCuenta  = 0;
                }
                $cuenta_head = $cuenta ;
            }
            //SUBTOTAL POR GRUPO
            if ($cuenta_grupo!=substr($cuenta,0,2 ) ) {
                if ($cuenta_grupo!="") {
                    $arrayAsiento[] = array(
                                                'saldo'                => $acumGrupo,
                                                'cuenta'               => $cuenta_grupo,
                                                'descripcion'          => '',
                                                'centro_costos'        => '',
                                                'codigo_centro_costos' => '',
                                                'id_tercero'           => '',
                                                'nit_tercero'          => '',
                                                'tercero'              => '',
                                                'total'                => 'true',
                                                'class'                => "class='subtotal'",
                                            );
                    $acumGrupo  = 0;
                }
                $cuenta_grupo = substr($cuenta,0,2 ) ;
            }

            $arrayAsiento[] = array(
                                            'saldo'                => $row['saldo'],
                                            'cuenta'               => $cuenta,
                                            'descripcion'          => $row['descripcion'],
                                            'centro_costos'        => $row['centro_costos'],
                                            'codigo_centro_costos' => $row['codigo_centro_costos'],
                                            'id_tercero'           => $row['id_tercero'],
                                            'nit_tercero'          => $row['nit_tercero'],
                                            'tercero'              => $row['tercero'],
                                            );
            // $arrayTotales[$row['cuenta']]+=$row['saldo'];
            $acumCuenta += $row['saldo'];
            $acumGrupo  += $row['saldo'];
            // echo " cuenta_head $cuenta_head - cuenta $cuenta <br>";



        }

        $arrayAsiento[] = array(
                                                'saldo'                => $acumCuenta,
                                                'cuenta'               => $cuenta_head,
                                                'descripcion'          => '',
                                                'centro_costos'        => '',
                                                'codigo_centro_costos' => '',
                                                'id_tercero'           => '',
                                                'nit_tercero'          => '',
                                                'tercero'              => '',
                                                'total'                => 'true',
                                                'class'                => "class='subtotal'",
                                            );
        // json_decode( $arrayAsiento);
        // arsort($arrayAsiento);
        // print_r($arrayAsiento);
        //RECORREMOS EL RESULTADO DE LA CONSULTA  Y LLENAMOS EL ARRAY CON EL VALOR DEL SALDO Y COMO INDICE
        foreach ($arrayAsiento as $key => $row) {
            // echo " cuenta_head $key $row[cuenta] $row[descripcion] <br>";
            // while ($row=$mysql->fetch_array($query)) {
            $cuenta             = $row['cuenta'];
            $descripcion_cuenta = $row['descripcion'];
            $saldo              = $row['saldo'];
            $centro_costos      = $row['codigo_centro_costos'];
            $id_tercero         = $row['id_tercero'];
            $nit_tercero        = $row['nit_tercero'];
            $tercero            = $row['tercero'];
            // if ($centro_costos=='') { continue;  }

            if ($mostrar_tercero=='true'){
                $tdTercero = "  <td>$nit_tercero</td>
                                <td>$tercero</td>
                            ";
                $tdTerceroSubtotal = "<td>&nbsp;</td>
                                        <td>&nbsp;</td>";
            }


            //COSTO DE VENTA
            if (substr($cuenta , 0,1)=='6' || substr($cuenta , 0,1)=='7') {
                $costoVenta       +=($row['class']=='')? $saldo : 0 ;
                $costoVentaCuerpo .= "<tr $row[class]>
                                            <td >$cuenta</td>
                                            <td >$descripcion_cuenta &nbsp;</td>
                                            $tdTercero
                                            <td >$centro_costos</td>
                                            <td style='text-align:right;'>".validar_numero_formato($saldo, $IMPRIME_XLS)."</td>
                                        </tr>";
            }

            //VENTAS DEL PERIODO
            //SI EL SALDO ES NEGATIVO SE MULTIPLICA POR -1 PARA CONVERTIRLO A POSITIVO Y QUE SE HAGA MAS ENTENDIBLE
            if (substr($cuenta , 0,2)=='41') {


                if (substr($cuenta , 0,4)=='4175') {
                    $devolucion       +=($row['class']=='')? $saldo : 0 ;
                    $devolucionCuerpo .= "<tr $row[class]>
                                                <td >$cuenta</td>
                                                <td >$descripcion_cuenta &nbsp;</td>
                                                $tdTercero
                                                <td >$centro_costos</td>
                                                <td style='text-align:right;'>".validar_numero_formato($saldo, $IMPRIME_XLS)."</td>
                                            </tr>";

                 }
                 //SI NO ES LA CUENTA DE DEVOLUCION
                 else{
                    $ventas       +=($row['class']=='')? $saldo : 0 ;
                    $ventasCuerpo .= "<tr $row[class]>
                                            <td >$cuenta</td>
                                            <td >$descripcion_cuenta &nbsp;</td>
                                            $tdTercero
                                            <td >$centro_costos</td>
                                            <td style='text-align:right;'>".validar_numero_formato($saldo, $IMPRIME_XLS)."</td>
                                        </tr>";

                 }

                //  if ($cuenta_head!=$cuenta ) {
                //     if ($cuenta_head!="") {
                //         $ventasCuerpo.= "<tr class='total'>
                //                                 <td >Subtotal $cuenta</td>
                //                                 <td >&nbsp;</td>
                //                                 $tdTerceroSubtotal
                //                                 <td ></td>
                //                                 <td style='text-align:right;'>".validar_numero_formato( $arrayTotales[$cuenta], $IMPRIME_XLS)."</td>
                //                             </tr>";
                //     }

                //     $cuenta_head = $cuenta ;
                // }
            }

            //SI ES UN GASTO DE ADMINISTRACION
            if (substr($cuenta , 0,2)=='51') {

                // $td = ($id_centro_costos!='')? '<td style="width:30%;font-style: italic;">'.$row['centro_costos'].' &nbsp;</td>': '';

                $gastosAdministracion       +=($row['class']=='')? $saldo : 0 ;
                $gastosAdministracionCuerpo .= "<tr $row[class]>
                                                    <td >$cuenta </td>
                                                    <td >$descripcion_cuenta &nbsp;</td>
                                                    $tdTercero
                                                    <td >$centro_costos </td>
                                                    <td style='text-align:right;'>".validar_numero_formato($saldo, $IMPRIME_XLS)."</td>
                                                </tr>";

            }
            //SI ES UN GASTO DE VENTA
            if (substr($cuenta , 0,2)=='52') {
                if ($id_centro_costos!='') {
                    $width = '35%';
                    // $td    = '<td style="width:30%;font-style: italic;">'.$row['centro_costos'].' &nbsp;</td>';
                }

                $gastosVenta       +=($row['class']=='')? $saldo : 0 ;
                $gastosVentaCuerpo .= "<tr $row[class]>
                                            <td >$cuenta </td>
                                            <td >$descripcion_cuenta &nbsp;</td>
                                            $tdTercero
                                            <td >$centro_costos </td>
                                            <td style='text-align:right;'>".validar_numero_formato($saldo, $IMPRIME_XLS)."</td>
                                        </tr>";

            }
            //SI ES UN GASTO NO OPERACIONAL
            if (substr($cuenta , 0,2)=='53') {
                if ($id_centro_costos!='') {
                    $width = '35%';
                    // $td    = '<td style="width:30%;font-style: italic;">'.$row['centro_costos'].' &nbsp;</td>';
                }

                $gastosOperacional       +=($row['class']=='')? $saldo : 0 ;
                $gastosOperacionalCuerpo .= "<tr $row[class]>
                                                <td >$cuenta </td>
                                                <td >$descripcion_cuenta &nbsp;</td>
                                                $tdTercero
                                                <td >$centro_costos </td>
                                                <td style='text-align:right;'>".validar_numero_formato($saldo, $IMPRIME_XLS)."</td>
                                            </tr>";

            }
            //SI ES OTROS INGRESOS
            if (substr($cuenta , 0,2)=='42') {

                $otrosIngresos       +=($row['class']=='')? $saldo : 0 ;
                $otrosIngresosCuerpo .= "<tr $row[class]>
                                            <td >$cuenta </td>
                                            <td >$descripcion_cuenta &nbsp;</td>
                                            $tdTercero
                                            <td >$centro_costos </td>
                                            <td style='text-align:right;'>".validar_numero_formato($saldo, $IMPRIME_XLS)."</td>
                                        </tr>";
            }
            //OTROS EGRESOS
            if (substr($cuenta , 0,2)=='54' || substr($cuenta , 0,2)=='59') {

                $otrosEgresos       +=($row['class']=='')? $saldo : 0 ;
                $otrosEgresosCuerpo .= "<tr $row[class]>
                                            <td >$cuenta </td>
                                            <td >$descripcion_cuenta &nbsp;</td>
                                            $tdTercero
                                            <td >$centro_costos </td>
                                            <td style='text-align:right;'>".validar_numero_formato($saldo, $IMPRIME_XLS)."</td>
                                        </tr>";

            }



        }

        $ventasNetas      = $ventas + $devolucion;
        $margenBruto      = $ventasNetas + $costoVenta;
        $utilidadNeta     = $margenBruto + $gastosAdministracion + $gastosVenta + $gastosOperacional;
        $utilidadOperdida = ($utilidadNeta + $otrosIngresos) + $otrosEgresos;
        $tdTitleTercero   =($mostrar_tercero=='true')? "<td>NIT</td><td>TERCERO</td>" : "";

        $cuerpoInforme.='<table style="width:90%;font-size:11px;" class="table" >

                            <thead>
                                <tr>
                                    <td><b>CUENTA</b></td>
                                    <td><b>DESCRIPCION</b></td>
                                    '.$tdTitleTercero.'
                                    <td><b>CENTRO DE COSTO</b></td>
                                    <td style="text-align:right;"><b>SALDO</b></td>
                                </tr>
                            </thead>
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>VENTAS DEL PERIODO</b></td>
                                <td></td>
                            </tr>
                            '.$ventasCuerpo.'
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>TOTAL VENTAS DEL PERIODO </b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($ventas, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="'.$colspan.'" ><b>DEVOLUCIONES EN VENTA</b></td>
                                    <td></td>
                                </tr>
                            </thead>
                                '.$devolucionCuerpo.'
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>-TOTAL DEVOLUCIONES EN VENTA</b> </td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($devolucion, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>VENTAS NETAS</b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($ventasNetas, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="'.$colspan.'"><b>COSTOS DE VENTAS</b></td>
                                    <td></td>
                                </tr>
                            </thead>
                                '.$costoVentaCuerpo.'
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>-TOTAL COSTOS DE VENTAS<b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($costoVenta, $IMPRIME_XLS).'<b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr class="total" >
                                <td colspan="'.$colspan.'"><b>MARGEN BRUTO</b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($margenBruto, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="'.$colspan.'"><b>GASTOS DE ADMINISTRACION</b></td>
                                    <td></td>
                                </tr>
                            </thead>
                                '.$gastosAdministracionCuerpo.'
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>-TOTAL GASTOS DE ADMINISTRACION</b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($gastosAdministracion, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="'.$colspan.'"><b>GASTOS DE VENTAS</b></td>
                                    <td></td>
                                </tr>
                            </thead>
                                '.$gastosVentaCuerpo.'
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>-TOTAL GASTOS DE VENTAS</b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($gastosVenta, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="'.$colspan.'"><b>GASTOS NO OPERACIONALES</b></td>
                                    <td></td>
                                </tr>
                            </thead>
                                '.$gastosOperacionalCuerpo.'
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>-TOTAL GASTOS NO OPERACIONALES</b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($gastosOperacional, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr class="total">
                                <td colspan="'.$colspan.'" ><b>UTILIDAD NETA </b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($utilidadNeta, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="'.$colspan.'"><b>+ OTROS INGRESOS</b></td>
                                    <td></td>
                                </tr>
                            </thead>
                            '.$otrosIngresosCuerpo.'
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>TOTAL OTROS INGRESOS</b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($otrosIngresos, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>

                            <thead>
                                <tr>
                                    <td colspan="'.$colspan.'"><b>-OTROS EGRESOS</b></td>
                                    <td></td>
                                </tr>
                            </thead>
                                '.$otrosEgresosCuerpo.'
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>TOTAL OTROS EGRESOS</b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($otrosEgresos, $IMPRIME_XLS).'</b></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr class="total">
                                <td colspan="'.$colspan.'"><b>UTILIDAD O PERDIDA</b></td>
                                <td style="text-align:right;"><b>'.validar_numero_formato($utilidadOperdida, $IMPRIME_XLS).'</b></td>
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
  .titulo_informe_empresa{
    float     : left;
    width     : 100%;
    font-size : 16px;
    font-weight:bold;
  }
  .contenedor_informe, .contenedor_titulo_informe{
    width     : 100%;
    margin    : 0 0 15px 0;
    font-size : 11px;
    float     : left;
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
  .table thead td{
    padding-left : 10px;
    height       : 30px;
    background   : #999;
    color        : #FFF;
  }
  .total{
    background  : #EEE;
    font-weight : bold;
  }
  .subtotal{
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
  .subtotal td{
    border-top    : 1px solid #999;
    border-bottom : 1px solid #999;
    padding-left  : 10px;
    height        : 25px;
    font-weight   : bold;
    color         : #8E8E8E;
  }
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

	if(!isset($ORIENTACION)){ $ORIENTACION = 'L'; }
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
  else{ $MS = 10; $MD = 2; $MI = 5; $ML = 10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12 ; }
	if($IMPRIME_PDF == 'true'){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
    					'utf-8',     // mode - default ''
    					$HOJA,		   // format - A4, for example, default ''
    					12,				   // font size - default 0
    					'',				   // default font family
    					$MI,			   // margin_left
    					$MD,			   // margin right
    					$MS,			   // margin top
    					$ML,			   // margin bottom
    					10,				   // margin header
    					10,				   // margin footer
    					$ORIENTACION // L - landscape, P - portrait
    				);
    $mpdf->SetProtection(array('print'));
    $mpdf->useSubstitutions = true;
    $mpdf->simpleTables = true;
    $mpdf->packTableData = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHeader("");
    $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($texto));

		if($PDF_GUARDA == 'true'){ $mpdf->Output($documento.".pdf",'D'); }
    else{ $mpdf->Output($documento.".pdf",'I'); }
		exit;
	}
  else{
    echo $texto;
  }
?>
