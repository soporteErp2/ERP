<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=Estado_de_resultados_niif.xls");
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

    function valorFinal($valor){
        if ($valor<=0) {
            return '(Disminuci&oacute;n)';
        }else{
            return '(Aumento)';
        }
    }

    if (!isset($MyInformeFiltroFechaFinal) || $MyInformeFiltroFechaFinal=='') {
        $tipo_balance_EstadoResultado='';
        $MyInformeFiltroFechaFinal=date("Y-m-d");
        $MyInformeFiltroFechaInicio=date("Y-m").'-01';
        // $script='localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif="";
        //         localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif="";
        //         localStorage.tipo_balance_EstadoResultadoNiif="";
        //         localStorage.nivel_cuentas_EstadoResultadoNiif="";
        //         arrayCentroCostos.length=0;';
        $whereFecha='AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
        $whereNiveles=' AND AC.codigo_cuenta=puc_niif.cuenta ';
        $groupByOpcion = 'GROUP BY AC.codigo_cuenta';
        $tipo_informe = 'Mensual <br>de '.$MyInformeFiltroFechaInicio.' hasta '.$MyInformeFiltroFechaFinal;

    }else{
        //NIVELES A GENERAR EL INFORME
        if ($generar=='Grupos'){ $varCortar=2;  }
        else if ($generar=='Cuentas'){ $varCortar = 4; }
        else if ($generar=='Subcuentas'){ $varCortar = 6; }
        else if ($generar=='Auxiliares'){ $varCortar = 8; }

        if ($varCortar>0) {
            $whereNiveles=' AND LEFT(AC.codigo_cuenta,'.$varCortar.')= puc_niif.cuenta ';
        }
        else{
            $whereNiveles=' AND AC.codigo_cuenta=puc_niif.cuenta ';
        }


        switch ($tipo_balance_EstadoResultado) {
            case 'mensual':
                $tipo_informe='Mensual';
                $fecha_informe=explode('-', $MyInformeFiltroFechaFinal);
                $MyInformeFiltroFechaInicio=$fecha_informe[0].'-'.$fecha_informe[1].'-01';
                $whereFecha='AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
                $tipo_informe='Mensual <br>de '.$MyInformeFiltroFechaInicio.' a '.$MyInformeFiltroFechaFinal;;
            break;
            case 'mensual_acumulado':

                $fecha_informe=explode('-', $MyInformeFiltroFechaFinal);
                $MyInformeFiltroFechaInicio=$fecha_informe[0].'-01-01';
                $whereFecha='AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
                $tipo_informe='Mensual Acumulado<br>de '.$MyInformeFiltroFechaInicio.' a '.$MyInformeFiltroFechaFinal;
            break;
            case 'comparativo_mensual':
                $tipo_informe='Comparativo Mensual';
                $groupBy=',YEAR(AC.fecha) , MONTH(AC.fecha)';
                $campos_select=',YEAR(AC.fecha) as anio, MONTH(AC.fecha) as mes, DAY(AC.fecha) as dia';
                $mesanterior =  date("Y-m-d", strtotime($MyInformeFiltroFechaFinal.' - 1 month'));
                $fecha_informe=explode('-', $mesanterior);
                $MyInformeFiltroFechaInicio=$fecha_informe[0].'-'.$fecha_informe[1].'-01';
                $whereFecha='AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';

            break;
            case 'rango_fechas':
                //EN PROCESO
            break;
            case 'comparativo_anual':
                $tipo_informe='Comparativo Anual';
                $groupBy=',YEAR(AC.fecha)';
                $campos_select=',YEAR(AC.fecha) as anio, MONTH(AC.fecha) as mes, DAY(AC.fecha) as dia';
                $anioAnterior =  date("Y-m-d", strtotime($MyInformeFiltroFechaFinal.' - 1 year'));
                $fecha_informe=explode('-', $anioAnterior);
                $MyInformeFiltroFechaInicio=$fecha_informe[0].'-01-01';
                $whereFecha='AND AC.fecha BETWEEN \''.$MyInformeFiltroFechaInicio.'\' AND \''.$MyInformeFiltroFechaFinal.'\'';
            break;

        }
        $groupByOpcion          = 'GROUP BY puc_niif.cuenta'.$groupBy;
         $id_centro_costos;
        if ($id_centro_costos!='') {
            $tipo_informe.='<br>Por Centros de Costos';
            $groupByOpcion = 'GROUP BY puc_niif.cuenta'.$groupBy.',id_centro_costos';
            $arrayId = explode(",", $id_centro_costos);
            foreach ($arrayId as $indice => $valor_array) {
                if ($valor_array!='') {
                   $whereIdCentroCostos.=($whereIdCentroCostos!='')? ' OR AC.id_centro_costos='.$valor_array : ' AND ( AC.id_centro_costos='.$valor_array ;
                }

            }
            $whereIdCentroCostos.=' OR AC.id_centro_costos=0 OR AC.id_centro_costos="" OR ISNULL(AC.id_centro_costos))';
        }
    }

    // echo $mesanterior =  date('Y-m-d', strtotime('now - 1 month'));
    // echo $MyInformeFiltroFechaInicio;

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
    $whereCuentas  = '';

    $cuerpoInforme = '';

    //CONSULTAR LAS CUENTAS CONFIGURADAS PARA EL INFORME
    $sql="SELECT cuenta,descripcion,clasificacion FROM configuracion_informe_estado_flujo_efectivo WHERE id_empresa='$id_empresa' AND contabilidad='niif' AND activo=1";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $arrayClasificacionCuentas[$row['cuenta']]=array('clasificacion'=>$row['clasificacion'],'descripcion'=>$row['descripcion']);
        // echo $row['cuenta_niif'].' - '.$row['descripcion_cuenta_niif'].' - '.$row['clasificacion'].' - '.$row['informe']."<br>";
        $whereCuentas.=($whereCuentas=='')? " AC.codigo_cuenta LIKE '".$row['cuenta']."%'" : " OR AC.codigo_cuenta LIKE '".$row['cuenta']."%'";
    }
    // print_r($arrayClasificacionCuentas);
    //CADENA CON LA CONSULTA
  $sql="SELECT
            SUM(AC.haber-AC.debe) AS saldo,puc_niif.cuenta,puc_niif.descripcion,AC.centro_costos $campos_select
            FROM
                asientos_niif AS AC,puc_niif
            WHERE
             AC.id_empresa=$id_empresa
             AND puc_niif.id_empresa=$id_empresa
             AND  ( $whereCuentas)
            $whereNiveles
            $whereFecha
            $whereIdCentroCostos
            $groupByOpcion";

    if($tipo_balance_EstadoResultado=='comparativo_mensual' || $tipo_balance_EstadoResultado=='comparativo_anual'){

        //FECHAS DE LOS MESES PARA LA COMPARACION
        $fechaAnterior = explode('-', $MyInformeFiltroFechaInicio);
        $fechaActual   = explode('-', $MyInformeFiltroFechaFinal);

        $anioAnterior = $fechaAnterior[0];
        $mesAnterior  = $fechaAnterior[1]*1;

        $anioActual   = $fechaActual[0];
        $mesActual    = $fechaActual[1]*1;

        $subtitulo_cabecera=$tipo_informe.'<br>A nivel de '.$generar;
        $query=mysql_query($sql,$link);

        $colspan=($mostrar_cuentas_niif=='true')? ' colspan="2" ' : '' ;

        //RECORREMOS EL RESULTADO DE LA CONSULTA  Y LLENAMOS EL ARRAY CON EL VALOR DEL SALDO Y COMO INDICE
        while ($row=mysql_fetch_array($query)) {
            //SI ES COMPARATIVO MENSUAL
            if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                //SI ES POR CENTRO DE COSTOS Y CON CAPA DEL MES
                if ($id_centro_costos!='') {
                    $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)][$row['anio']][$row['mes']]['saldo']+=$row['saldo'];
                    $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)]['subcuenta'][$row['cuenta']][$row['centro_costos']][$row['anio']][$row['mes']]=array('descripcion' => $row['descripcion'],'saldo' => $row['saldo']);

                    $arrayDatosCuentas[$row['cuenta']][$row['centro_costos']][$row['anio']][$row['mes']]= $row['saldo'] ;
                }
                //SIN CAPA DE CENTRO DE COSTOS, CON CAPA DE MES
                else{
                    $arrayDatosCuentas[$row['cuenta']][$row['anio']][$row['mes']] = $row['saldo'] ;
                    $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)][$row['anio']][$row['mes']]['saldo']+=$row['saldo'];
                    $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)]['subcuenta'][$row['cuenta']][$row['anio']][$row['mes']]=array('descripcion' => $row['descripcion'],'saldo' => $row['saldo']);

                }

            }
            //SI ES COMPARATIVO ANUAL
            else{
                //AGREGAR LA CAPA DE CENTRO DE COSTOS, SIN CAPA MES
                if ($id_centro_costos!='') {
                    $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)][$row['anio']]['saldo']+=$row['saldo'];
                    $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)]['subcuenta'][$row['cuenta']][$row['centro_costos']][$row['anio']]=array('descripcion' => $row['descripcion'],'saldo' => $row['saldo']);

                    $arrayDatosCuentas[$row['cuenta']][$row['centro_costos']][$row['anio']] = $row['saldo'] ;
                }else{
                    //SIN CAPA DE CENTRO DE COSTOS NI MES
                    $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)][$row['anio']]['saldo']+=$row['saldo'];
                    $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)]['subcuenta'][$row['cuenta']][$row['anio']]=array('descripcion' => $row['descripcion'],'saldo' => $row['saldo']);

                    $arrayDatosCuentas[$row['cuenta']][$row['anio']] = $row['saldo'] ;
                }
            }

            $arrayCuentas[$row['cuenta']] = $row['descripcion'];
        }



        if($tipo_balance_EstadoResultado=='comparativo_mensual' ){
            $diaFinalAnterior= date("d",(mktime(0,0,0,$fechaAnterior[1]+1,1,$fechaAnterior[0])-1));
            $fechaFinalAnterior=$fechaAnterior[0].'-'.$fechaAnterior[1].'-'.$diaFinalAnterior;
            $fechaInicialActual=$fechaActual[0].'-'.$fechaActual[1].'-01';
        }
        else{
            $fechaFinalAnterior=$fechaAnterior[0].'-12-31';
            $fechaInicialActual=$fechaActual[0].'-'.$fechaActual[1].'-01';
        }

        $cabecera_tabla='<tr><td '.$colspan.'></td><td ></td><td style="text-align:right;"><b>PERIODO ANTERIOR</b><br><label style="font-size:10px;">'.$MyInformeFiltroFechaInicio.'<br>a<br>'.$fechaFinalAnterior.'</label></td><td style="text-align:right;"><b>PERIODO ACTUAL</b><br>'.$fechaInicialActual.'<br>a<br>'.$MyInformeFiltroFechaFinal.'<br></td></tr>';
        if ($id_centro_costos!='' && $generar!='' && $generar!='Grupos') {

            $cabecera_tabla='<tr><td '.$colspan.' ><b>CUENTAS</b></td><td ><b>CENTROS DE COSTOS</b></td><td style="text-align:right;"><b>PERIODO ANTERIOR</b><br><label style="font-size:10px;">'.$MyInformeFiltroFechaInicio.'<br>a<br>'.$fechaFinalAnterior.'</label></td><td style="text-align:right;"><b>PERIODO ACTUAL</b><br>'.$fechaInicialActual.'<br>a<br>'.$MyInformeFiltroFechaFinal.'<br></td></tr>';
        }

        //INGRESOS
        foreach ($arrayClasificacionCuentas as $cuenta => $arrayValores) {
            // print_r($arrayValores);
            // echo $arrayValores['saldo']."<br>";
            //SALDO DEPENDIENDO DE EL TIPO DE INFORME
            if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                $saldoAnteriorValidar = $arrayValores[$anioAnterior][$mesAnterior]['saldo'];
                $saldoActualValidar   = $arrayValores[$anioActual][$mesActual]['saldo'];

                $saldoAnterior = valorFinal($arrayValores[$anioAnterior][$mesAnterior]['saldo']);
                $saldoAnterior = ($generar!='' && $generar!='Grupos')? '' : $saldoAnterior ;

                $saldoActual = valorFinal($arrayValores[$anioActual][$mesActual]['saldo']);
                $saldoActual   = ($generar!='' && $generar!='Grupos')? '' : $saldoActual ;

                $descripcion_cuenta_niif = $arrayValores['descripcion'] ;

                //CAMBIAR EL ARRAY
                $arrayTemporal=$arrayValores[$anioAnterior][$mesAnterior];
            }
            else{

                $saldoAnteriorValidar = $arrayValores[$anioAnterior]['saldo'];
                $saldoActualValidar   = $arrayValores[$anioActual]['saldo'];

                $saldoAnterior = valorFinal($arrayValores[$anioAnterior]['saldo']);
                $saldoAnterior = ($generar!='' && $generar!='Grupos')? '' : $saldoAnterior ;

                $saldoActual =  valorFinal($arrayValores[$anioActual]['saldo']);
                $saldoActual   = ($generar!='' && $generar!='Grupos')? '' : $saldoActual;

                $descripcion_cuenta_niif = $arrayValores['descripcion'] ;

                //CAMBIAR EL ARRAY
                $arrayTemporal=$arrayValores[$anioAnterior];
            }

            //SI TIENE SALDO Y PERTENECE A SU CLASIFICACION
            if (($saldoActualValidar!='' || $saldoAnteriorValidar!='') && $arrayValores['clasificacion']=='ingresos'){

                $totalEstadoResultadoAnterior+=$saldoAnteriorValidar;
                $totalEstadoResultadoActual+=$saldoActualValidar;

                // $totalEstadoResultado+=$arrayValores['saldo'];

                $cuenta = ($mostrar_cuentas_niif=='true')? '<td>'.$cuenta.'&nbsp;&nbsp;</td>' : '' ;

                $cuerpoIngresos.='<tr>
                                    '.$cuenta.'
                                    <td>'.$descripcion_cuenta_niif.'</td>
                                    <td></td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoAnterior.'</td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoActual.'</td>
                                </tr>';

                //INFORMACION DETALLADA DE LA CUENTA
                if ($generar!='' && $generar!='Grupos') {
                    //FOR EACH DE LAS SUBCUENTAS
                    foreach ($arrayValores['subcuenta'] as $subcuenta => $arrayResulIni) {
                        //SI SE MUESTRA O NO LA CUENTA
                        $subcuenta=($mostrar_cuentas_niif=='true')? '<td>'.$subcuenta.'&nbsp;&nbsp;</td>' : '' ;
                        //SI TIENE LA CAPA DE LOS CENTROS DE COSTO
                        if ($id_centro_costos!='') {
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $centro_costos => $arrayResul) {

                                // echo $arrayResul[$anioAnterior]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;

                                }

                                $cuerpoIngresos.='<tr>
                                                        '.$subcuenta.'
                                                        <td>'.$descripcion.'</td>
                                                        <td>'.$centro_costos.'</td>
                                                        <td style="text-align:right;" class="campo_total" >'.$saldoAnterior_cuenta.'</td>
                                                        <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                    </tr>';

                            }
                        }
                        //SI EL ARRAY NO TIENE LA CAPA DE LOS CENTROS DE COSTOS
                        else{
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $anio_array => $arrayResul) {

                                // echo $arrayResulIni[$anioActual][$mesActual]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior]['descripcion'] ;
                                }

                                $cuerpoIngresos.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;">'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }

                    }//FOR EACH DE LAS CUENTAS DETALLADAS

                }// FIN IF CUENTAS DETALLADAS

            }// FIN IF DE INGRESOS

            //SI TIENE SALDO Y PERTENECE A SU CLASIFICACION
            if (($saldoActualValidar!='' || $saldoAnteriorValidar!='') && $arrayValores['clasificacion']=='gastos'){
                $totalEstadoResultadoAnterior+=$saldoAnteriorValidar;
                $totalEstadoResultadoActual+=$saldoActualValidar;
                // $totalEstadoResultado+=$arrayValores['saldo'];

                $cuenta = ($mostrar_cuentas_niif=='true')? '<td>'.$cuenta.'&nbsp;&nbsp;</td>' : '' ;

                $cuerpoGastos.='<tr>
                                    '.$cuenta.'
                                    <td>'.$descripcion_cuenta_niif.'</td>
                                    <td></td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoAnterior.'</td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoActual.'</td>
                                </tr>';

                //INFORMACION DETALLADA DE LA CUENTA
                if ($generar!='' && $generar!='Grupos') {
                    //FOR EACH DE LAS SUBCUENTAS
                    foreach ($arrayValores['subcuenta'] as $subcuenta => $arrayResulIni) {
                        //SI SE MUESTRA O NO LA CUENTA
                        $subcuenta=($mostrar_cuentas_niif=='true')? '<td>'.$subcuenta.'&nbsp;&nbsp;</td>' : '' ;
                        //SI TIENE LA CAPA DE LOS CENTROS DE COSTO
                        if ($id_centro_costos!='') {
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $centro_costos => $arrayResul) {

                                // echo $arrayResul[$anioAnterior]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;

                                }

                                $cuerpoGastos.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;" class="campo_total" >'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }
                        //SI EL ARRAY NO TIENE LA CAPA DE LOS CENTROS DE COSTOS
                        else{
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $anio_array => $arrayResul) {

                                // echo $arrayResulIni[$anioActual][$mesActual]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior]['descripcion'] ;
                                }

                                $cuerpoGastos.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;">'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }

                    }//FOR EACH DE LAS CUENTAS DETALLADAS

                }// FIN IF CUENTAS DETALLADAS

            }// FIN IF DE INGRESOS

            //SI TIENE SALDO Y PERTENECE A SU CLASIFICACION
            if (($saldoActualValidar!='' || $saldoAnteriorValidar!='') && $arrayValores['clasificacion']=='costos'){
                $totalEstadoResultadoAnterior+=$saldoAnteriorValidar;
                $totalEstadoResultadoActual+=$saldoActualValidar;
                // $totalEstadoResultado+=$arrayValores['saldo'];

                $cuenta = ($mostrar_cuentas_niif=='true')? '<td>'.$cuenta.'&nbsp;&nbsp;</td>' : '' ;

                $cuerpoCostos.='<tr>
                                    '.$cuenta.'
                                    <td>'.$descripcion_cuenta_niif.'</td>
                                    <td></td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoAnterior.'</td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoActual.'</td>
                                </tr>';

                //INFORMACION DETALLADA DE LA CUENTA
                if ($generar!='' && $generar!='Grupos') {
                    //FOR EACH DE LAS SUBCUENTAS
                    foreach ($arrayValores['subcuenta'] as $subcuenta => $arrayResulIni) {
                        //SI SE MUESTRA O NO LA CUENTA
                        $subcuenta=($mostrar_cuentas_niif=='true')? '<td>'.$subcuenta.'&nbsp;&nbsp;</td>' : '' ;
                        //SI TIENE LA CAPA DE LOS CENTROS DE COSTO
                        if ($id_centro_costos!='') {
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $centro_costos => $arrayResul) {

                                // echo $arrayResul[$anioAnterior]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;

                                }

                                $cuerpoCostos.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;" class="campo_total" >'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }
                        //SI EL ARRAY NO TIENE LA CAPA DE LOS CENTROS DE COSTOS
                        else{
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $anio_array => $arrayResul) {

                                // echo $arrayResulIni[$anioActual][$mesActual]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior]['descripcion'] ;
                                }

                                $cuerpoCostos.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;">'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }

                    }//FOR EACH DE LAS CUENTAS DETALLADAS

                }// FIN IF CUENTAS DETALLADAS

            }// FIN IF DE INGRESOS

            //SI TIENE SALDO Y PERTENECE A SU CLASIFICACION
            if (($saldoActualValidar!='' || $saldoAnteriorValidar!='') && $arrayValores['clasificacion']=='impuestos'){
                $totalEstadoResultadoImpuestoAnterior+=$saldoAnteriorValidar;
                $totalEstadoResultadoImpuestoActual+=$saldoActualValidar;
                // $totalEstadoResultado+=$arrayValores['saldo'];

                $cuenta = ($mostrar_cuentas_niif=='true')? '<td>'.$cuenta.'&nbsp;&nbsp;</td>' : '' ;

                $cuerpoImpuestos.='<tr>
                                    '.$cuenta.'
                                    <td>'.$descripcion_cuenta_niif.'</td>
                                    <td></td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoAnterior.'</td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoActual.'</td>
                                </tr>';

                //INFORMACION DETALLADA DE LA CUENTA
                if ($generar!='' && $generar!='Grupos') {
                    //FOR EACH DE LAS SUBCUENTAS
                    foreach ($arrayValores['subcuenta'] as $subcuenta => $arrayResulIni) {
                        //SI SE MUESTRA O NO LA CUENTA
                        $subcuenta=($mostrar_cuentas_niif=='true')? '<td>'.$subcuenta.'&nbsp;&nbsp;</td>' : '' ;
                        //SI TIENE LA CAPA DE LOS CENTROS DE COSTO
                        if ($id_centro_costos!='') {
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $centro_costos => $arrayResul) {

                                // echo $arrayResul[$anioAnterior]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;

                                }

                                $cuerpoImpuestos.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;" class="campo_total" >'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }
                        //SI EL ARRAY NO TIENE LA CAPA DE LOS CENTROS DE COSTOS
                        else{
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $anio_array => $arrayResul) {

                                // echo $arrayResulIni[$anioActual][$mesActual]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior]['descripcion'] ;
                                }

                                $cuerpoImpuestos.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;">'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }

                    }//FOR EACH DE LAS CUENTAS DETALLADAS

                }// FIN IF CUENTAS DETALLADAS

            }// FIN IF DE INGRESOS

            //SI TIENE SALDO Y PERTENECE A SU CLASIFICACION
            if (($saldoActualValidar!='' || $saldoAnteriorValidar!='') && $arrayValores['clasificacion']=='cuentas'){
                $totalOtroEstadoResultadoAnterior=$saldoAnteriorValidar;
                $totalOtroEstadoResultadoActual=$saldoActualValidar;

                // $totalEstadoResultado+=$arrayValores['saldo'];

                $cuenta = ($mostrar_cuentas_niif=='true')? '<td>'.$cuenta.'&nbsp;&nbsp;</td>' : '' ;

                $cuerpoOtroEstadoIntegral.='<tr>
                                    '.$cuenta.'
                                    <td>'.$descripcion_cuenta_niif.'</td>
                                    <td></td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoAnterior.'</td>
                                    <td style="text-align:right;" class="campo_total">'.$saldoActual.'</td>
                                </tr>';

                //INFORMACION DETALLADA DE LA CUENTA
                if ($generar!='' && $generar!='Grupos') {
                    //FOR EACH DE LAS SUBCUENTAS
                    foreach ($arrayValores['subcuenta'] as $subcuenta => $arrayResulIni) {
                        //SI SE MUESTRA O NO LA CUENTA
                        $subcuenta=($mostrar_cuentas_niif=='true')? '<td>'.$subcuenta.'&nbsp;&nbsp;</td>' : '' ;
                        //SI TIENE LA CAPA DE LOS CENTROS DE COSTO
                        if ($id_centro_costos!='') {
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $centro_costos => $arrayResul) {

                                // echo $arrayResul[$anioAnterior]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResul[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResul[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResul[$anioAnterior]['descripcion']=='')?
                                                        $arrayResul[$anioActual]['descripcion']
                                                      : $arrayResul[$anioAnterior][$mesAnterior]['descripcion'] ;

                                }

                                $cuerpoOtroEstadoIntegral.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;" class="campo_total" >'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }
                        //SI EL ARRAY NO TIENE LA CAPA DE LOS CENTROS DE COSTOS
                        else{
                            //FOR EACH DE LOS CENTROS DE COSTO
                            foreach ($arrayResulIni as $anio_array => $arrayResul) {

                                // echo $arrayResulIni[$anioActual][$mesActual]['saldo']."<br>";

                                //SI ES COMPARATIVO MENSUAL
                                if ($tipo_balance_EstadoResultado=='comparativo_mensual') {
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior][$mesAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual][$mesActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior][$mesAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual][$mesActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior][$mesAnterior]['descripcion'] ;
                                }
                                //SI ES COMPARATIVO ANUAL
                                else{
                                    $saldoAnterior_cuenta = valorFinal($arrayResulIni[$anioAnterior]['saldo']);
                                    $saldoAnterior_cuenta = ($generar!='' && $generar!='Grupos')? $saldoAnterior_cuenta : '' ;

                                    $saldoActual_cuenta = valorFinal($arrayResulIni[$anioActual]['saldo']);
                                    $saldoActual_cuenta = ($generar!='' && $generar!='Grupos')? $saldoActual_cuenta : '' ;


                                    $descripcion = ($arrayResulIni[$anioAnterior]['descripcion']=='')?
                                                        $arrayResulIni[$anioActual]['descripcion']
                                                      : $arrayResulIni[$anioAnterior]['descripcion'] ;
                                }

                                $cuerpoOtroEstadoIntegral.='<tr>
                                                                    '.$subcuenta.'
                                                                    <td>'.$descripcion.'</td>
                                                                    <td>'.$centro_costos.'</td>
                                                                    <td style="text-align:right;">'.$saldoAnterior_cuenta.'</td>
                                                                    <td style="text-align:right;" class="campo_total">'.$saldoActual_cuenta.'</td>
                                                                </tr>';

                            }
                        }

                    }//FOR EACH DE LAS CUENTAS DETALLADAS

                }// FIN IF CUENTAS DETALLADAS

            }// FIN IF DE INGRESOS

        }

        //MOSTRAR CADA INFORME
        $totalEstadoResultadoIntegralAnterior=$totalEstadoResultadoAnterior+$totalOtroEstadoResultadoAnterior;
        $totalEstadoResultadoIntegralActual=$totalEstadoResultadoActual+$totalOtroEstadoResultadoActual;

        $totalEstadoResultadoIntegralAnterior=valorFinal($totalEstadoResultadoIntegralAnterior);
        $totalEstadoResultadoIntegralActual=valorFinal($totalEstadoResultadoIntegralActual);

        $cuerpoInformeEstadoResultado.=$cuerpoIngresos.$cuerpoGastos.$cuerpoCostos;
        $cuerpoInformeEstadoResultado.='<tr>
                                           <td '.$colspan.' ><b>GANANCIA ANTES DE IMPUESTO</b></td>
                                           <td></td>
                                           <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoAnterior).'</b><br>&nbsp;</td>
                                           <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoActual).'</b><br>&nbsp;</td>
                                        </tr>';
        $cuerpoInformeEstadoResultado.=$cuerpoImpuestos;
        $cuerpoInformeEstadoResultado.='<tr>
                                           <td '.$colspan.'><b>UTILIDAD DEL EJERCICIO</b></td>
                                           <td></td>
                                           <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoAnterior+$totalEstadoResultadoImpuestoAnterior).'</b><br>&nbsp;</td>
                                           <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoActual+$totalEstadoResultadoImpuestoActual).'</b><br>&nbsp;</td>
                                        </tr>';

        $cuerpoInformeIntegral.=$cuerpoOtroEstadoIntegral;

        //MOSTRAR SOLO EL RESULTADO DE ESTADO
        if ($estado_resultado=='estado_resultado') {
            $cuerpoInforme.=$cuerpoInformeEstadoResultado;
        }
        //MOSTRAR SOLO EL ESTADO DE RESULTADO INTEGRAL
        else if ($estado_resultado=='otro_estado_resultado') {
            if ($cuerpoInformeIntegral!='') {
                $cuerpoInforme.='<tr>
                                   <td '.$colspan.' ><b>OTRO RESULTADO INTEGRAL</b></td>
                                   <td ></td>
                                   <td ></td>
                                </tr>
                                <tr>
                                   <td '.$colspan.' ><b>RESULTADO</b></td>
                                   <td></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoAnterior+$totalEstadoResultadoImpuestoAnterior).'</b><br>&nbsp;</td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoActual+$totalEstadoResultadoImpuestoActual).'</b><br>&nbsp;</td>
                                </tr>';
                $cuerpoInforme.=$cuerpoInformeIntegral;
                $cuerpoInforme.='<tr>
                                   <td '.$colspan.'><b>TOTAL DE OTRO RESULTADO INTEGRAL</b></td>
                                   <td></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalOtroEstadoResultadoAnterior).'</b></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalOtroEstadoResultadoActual).'</b></td>
                                </tr>
                                <tr>
                                   <td '.$colspan.'><br><b>RESULTADO INTEGRAL TOTAL</b></td>
                                   <td></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoIntegralAnterior).'</b></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoIntegralActual).'</b></td>
                                </tr>';
            }else{
                $cuerpoInforme.='<tr><td>NO HAY REGISTROS QUE MOSTRAR</td></tr>';
            }

        }
        //MOSTRAR LOS DOS ESTADOS DE RESULTADO
        else{
            $cuerpoInforme.=$cuerpoInformeEstadoResultado;
            if ($cuerpoInformeIntegral!='') {
                $cuerpoInforme.='<tr>
                                   <td '.$colspan.'><b>OTRO RESULTADO INTEGRAL</b></td>
                                   <td ></td>
                                   <td ><br>&nbsp;</td>
                                </tr>';
                $cuerpoInforme.=$cuerpoInformeIntegral;
                $cuerpoInforme.='<tr>
                                   <td '.$colspan.' ><br><b>TOTAL DE OTRO RESULTADO INTEGRAL </b></td>
                                   <td></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoIntegralAnterior).'</b></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalEstadoResultadoIntegralActual).'</b></td>
                                </tr>
                                <tr>
                                   <td '.$colspan.' ><br><b>RESULTADO INTEGRAL TOTAL</b></td>
                                   <td></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalOtroEstadoResultadoAnterior).'</b></td>
                                   <td style="text-align:right;"><b>'.valorFinal($totalOtroEstadoResultadoActual).'</b></td>
                                </tr>';
            }
        }

        $cuerpoInforme='<table style="width:95%;font-size:11px;">
                            '.$cabecera_tabla.'
                            '.$cuerpoInforme.'
                        </table>';



    }
    else{
        // $subtitulo_cabecera=$tipo_informe.'<br>A nivel de '.$generar;

        $query=mysql_query($sql,$link);

        //RECORREMOS EL RESULTADO DE LA CONSULTA  Y LLENAMOS EL ARRAY CON EL VALOR DEL SALDO Y COMO INDICE
        while ($row=mysql_fetch_array($query)) {

            $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)]['saldo']+=$row['saldo'];
            $arrayClasificacionCuentas[substr($row['cuenta'], 0,2)]['subcuenta'][$row['cuenta']]=array('descripcion' => $row['descripcion'],'saldo' => $row['saldo'],'centro_costos'=>$row['centro_costos']);

        }

        // print_r($arrayClasificacionCuentas);

        $acumulado_actividades_operacionales = 0;
        $acumulado_actividades_inversiones   = 0;
        $acumulado_actividades_financieras   = 0;

        $cuerpo_actividades_operacionales    = '';
        $cuerpo_actividades_inversiones      = '';
        $cuerpo_actividades_financieras      = '';

        //RECORRER EL ARRAY PARA ARMAR EL INFORME
        foreach ($arrayClasificacionCuentas as $cuenta => $arrayResul) {

            //ACTIVIDADES OPERACIONALES
            if ($arrayResul['clasificacion']=='actividades_operacionales') {
                $acumulado_actividades_operacionales+=$arrayResul['saldo'];
                $cuerpo_actividades_operacionales.='<tr>
                                                        <td>'.valorFinal($arrayResul['saldo']).'</td>
                                                        <td>'.$arrayResul['descripcion'].'</td>
                                                        <td>'.$arrayResul['saldo'].'</td>
                                                    </tr>';
            }
            //ACTIVIDADES DE INVERSION
            else if ($arrayResul['clasificacion']=='actividades_inversiones') {
                $acumulado_actividades_inversiones+=$arrayResul['saldo'];
                $cuerpo_actividades_inversiones.='<tr>
                                                    <td>'.valorFinal($arrayResul['saldo']).'</td>
                                                    <td>'.$arrayResul['descripcion'].'</td>
                                                    <td>'.$arrayResul['saldo'].'</td>
                                                </tr>';
            }
            //ACTIVIDADES FINANCIERAS
            else if ($arrayResul['clasificacion']=='actividades_financieras') {
                $acumulado_actividades_financieras+=$arrayResul['saldo'];
                $cuerpo_actividades_financieras.='<tr>
                                                    <td>'.valorFinal($arrayResul['saldo']).'</td>
                                                    <td>'.$arrayResul['descripcion'].'</td>
                                                    <td>'.$arrayResul['saldo'].'</td>
                                                </tr>';
            }

        }




       $cuerpoInforme='<table style="width:95%;font-size:11px;" >
                            <tr>
                                <td colspan="3" class="titulos">ACTIVIDADES OPERACIONALES</td>
                            </tr>
                            '.$cuerpo_actividades_operacionales.'
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="2"><b>TOTAL ACTIVIDADES OPERACIONALES</td>
                                <td>'.$acumulado_actividades_operacionales.'</td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="3" class="titulos">ACTIVIDADES DE INVERSIONES</td>
                            </tr>
                            '.$cuerpo_actividades_inversiones.'
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="2"><b>TOTAL ACTIVIDADES DE INVERSIONES</td>
                                <td>'.$acumulado_actividades_inversiones.'</td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="3" class="titulos">ACTIVIDADES FINANCIERAS</td>
                            </tr>
                            '.$cuerpo_actividades_financieras.'
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td colspan="2"><b>TOTAL ACTIVIDADES FINANCIERAS</td>
                                <td>'.$acumulado_actividades_financieras.'</td>
                            </tr>
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

    .my_informe_Contenedor_informe {
        border-bottom : 1px solid #CCC;

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
    .labelResult{font-weight:bold;font-size: 11px;}
    .labelResult2{font-weight:bold;font-size: 10px;}

    .cuenta,.campo_total{width: 100px;}

    #divContenedorInforme{
        width         : 100%;
        margin-top    : 5px;
        margin-left   : 10px;
        margin-bottom : 25px;
    }

    .titulos{
        font-weight: bold;
        background-color: #F3F3F3;
        border-bottom: 1px solid;
        height: 25px;
    }

</style>

<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body >
    <div class="my_informe_Contenedor_Titulo_informe" style=" width:100%">
        <div style=" width:100%">
            <div style="width:100%; text-align:center">
                <table align="center" style="text-align:center;" >
                    <tr><td class="titulo_informe_empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr><td  style="font-size:13px;text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></td></tr>
                    <tr><td style="font-size:11px; text-align:center;" ><?php echo $tipo_informe; ?> </td></tr>
                </table>

            </div>
        </div>
    </div>

    <div class="my_informe_Contenedor_informe"  >

        <div  id="divContenedorInforme">
        <?php   echo $cuerpoInforme; ?>

           <!--  -->
        </div>

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
	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=2;$MI=5;$ML=10;}
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
        // $mpdf->useSubstitutions = true;
        // $mpdf->simpleTables = true;
        // $mpdf->packTableData= true;
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