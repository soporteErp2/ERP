<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=informe_planilla_ajuste_".date("Y-m-d").".xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa       = $_SESSION['EMPRESA'];
    $desde            = $MyInformeFiltroFechaInicio;
    $hasta            = $MyInformeFiltroFechaFinal;
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

if ($sucursal!='' && $sucursal!='global') {
    $whereSucursal= ' AND id_sucursal='.$sucursal;
    //CONSULTAR EL NOMBRE DE LA SUCURSAL
    $sql="SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
    $query=mysql_query($sql,$link);
    $subtitulo_cabecera.='<b>Sucursal</b> '.mysql_result($query,0,'nombre').'<br>';


}

//SI NO EXISTE LA VARIABLE DE FECHA FINAL, QUIERE DECIR QUE SE ESTA GENERANDO EL INFORME DESDE LA INTERFAZ PRINCIPAL Y NO DE LA VENTANA DE CONFIGURACION
//ENTONCES MOSTRAMOS TODAS LAS FACTURAS HASTA HOY Y CON TODOS LOS CLIENTES
if ((!isset($MyInformeFiltroFechaFinal) || $MyInformeFiltroFechaFinal=='') && (!isset($MyInformeFiltroFechaInicio) || $MyInformeFiltroFechaInicio=='')) {
    $MyInformeFiltroFechaFinal = date('Y-m-d');
    $nuevafecha = strtotime ( '-30 day' , strtotime ( $MyInformeFiltroFechaFinal ) ) ;
    $MyInformeFiltroFechaInicio = date ( 'Y-m-d' , $nuevafecha );

    $whereFecha=" AND fecha_inicio >= '".date('Y-m-d')."' AND fecha_final <= '$MyInformeFiltroFechaFinal' ";
    $script='localStorage.MyInformeFiltroFechaFinalPlanillaAjuste  = "";
             localStorage.MyInformeFiltroFechaInicioPlanillaAjuste = "";
             localStorage.sucursal_PlanillaAjuste                  = "";
             localStorage.agrupacion_PlanillaAjuste                = "";
             localStorage.discrimina_planillas_PlanillaAjuste      = "";

             //VACIAR LOS ARRAY DE LOS DATOS DEL CLIENTE
             arrayEmpleadosPlanillaAjuste.length=0;
             arrayEmpleadosPlanillaAjuste.length=0;
             arrayConceptosPlanillaAjuste.length=0;
            ';
    $agrupacion_PlanillaAjuste = 'empleados';




}else{
    // SI SE VA A DETALLAR CADA PLANILLA
    // discrimina_planillas

    $whereFecha=" AND fecha_inicio >= '$MyInformeFiltroFechaInicio' AND fecha_final <= '$MyInformeFiltroFechaFinal' ";

    //SI ES FRILTRADO POR CLIENTES, CREAMOS EL WHERE PARA PASARLO AL QUERY
    $whereIdClientes='';
    $whereClientes='';

    // idConceptos
    // idEmpleados

    if ($idConceptos!='') {
        $idConceptosExplode = explode(",", $idConceptos);
        // ARMAR EL WHERE DE LA CONSULTA
        foreach ($idConceptosExplode as $indice => $valor) {
            $whereIdConceptos.=($whereIdConceptos=='')? ' id_concepto='.$valor : ' OR id_concepto='.$valor ;
        }

        $whereIdConceptos=($whereIdConceptos!='')? " AND (".$whereIdConceptos.") " : "" ;
    }

    if ($idEmpleados!='') {
        $idEmpleadosExplode = explode(",", $idEmpleados);
        $whereIdEmpleados='';
        // ARMAR EL WHERE DE LA CONSULTA
        foreach ($idEmpleadosExplode as $indice => $valor) {
            $whereIdEmpleados.=($whereIdEmpleados=='')? ' id_empleado='.$valor : ' OR id_empleado='.$valor ;
        }

        $whereIdEmpleados=($whereIdEmpleados!='')? " AND (".$whereIdEmpleados.") " : "" ;
    }

}



    // CONSULTAR LAS PLANILLAS
    $sql="SELECT id,consecutivo,sucursal FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 $whereSucursal $whereFecha";
    $query=mysql_query($sql,$link);
    $wherePlanilla='';
    // RECORRER EL RESULTADO DE LA CONSULTA PARA CAPTURAR LOS ID DE LAS PLANILLAS
    while ($row=mysql_fetch_array($query)) {
        $wherePlanilla.=($wherePlanilla=='')? ' id_planilla='.$row['id'] : " OR id_planilla=".$row['id'];
        $arrayPlanillas[$row['id']]  = array('consecutivo' => $row['consecutivo'],
                                            'sucursal' => $row['sucursal'] );
    }

    // CONSULTAR LOS EMPLEADOS DE LAS PLANILLAS
    $sql="SELECT id_planilla,id_empleado,id_contrato,tipo_documento,documento_empleado,nombre_empleado,dias_laborados,id_contrato,terminar_contrato
            FROM nomina_planillas_ajuste_empleados WHERE activo=1 AND id_empresa=$id_empresa AND ($wherePlanilla) $whereIdEmpleados";
    $query=mysql_query($sql,$link);

    while ($row=mysql_fetch_array($query)) {
        $arrayPlanillasEmpleados[$row['id_empleado']] = array(
                                                                'id_planilla'        => $row['id_planilla'],
                                                                'tipo_documento'     => $row['tipo_documento'],
                                                                'documento_empleado' => $row['documento_empleado'],
                                                                'nombre_empleado'    => $row['nombre_empleado'],
                                                            );
        $arrayEmpleados[$row['id_empleado']] = array(
                                                        'tipo_documento'     => $row['tipo_documento'],
                                                        'documento_empleado' => $row['documento_empleado'],
                                                        'nombre_empleado'    => $row['nombre_empleado'],
                                                    );

        $whereIdEmpleadosQuery.=($whereIdEmpleadosQuery=='')? ' id_empleado='.$row['id_empleado'] : ' OR id_empleado='.$row['id_empleado'] ;
    }

    // CONSULTAR LAS ENTIDADES DE LOS EMPLEADOS
    $sql="SELECT id_empleado,id_entidad,id_concepto FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=$id_empresa";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $arrayIdEntidades[$row['id_empleado']][$row['id_concepto']] = array('id_entidad' => $row['id_entidad'], );
        $whereEntidades.=($whereEntidades=='')? 'id='.$row['id_entidad'] : ' OR id='.$row['id_entidad'] ;
    }

    $sql="SELECT id,numero_identificacion,nombre FROM terceros WHERE activo=1 AND id_empresa=$id_empresa AND ($whereEntidades)";
    $query=mysql_query($sql,$link);
    while ( $row=mysql_fetch_array($query)) {
        $arrayEntidades[$row['id']] = array('numero_identificacion' => $row['numero_identificacion'],
                                            'nombre' => $row['nombre'] );
    }

    // CONSULTAR LOS CONCEPTOS DE LOS EMPLEADOS DE LA PLANILLA
    $sql="SELECT id,id_planilla,id_empleado,id_contrato,id_concepto,codigo_concepto,concepto,naturaleza,valor_concepto,valor_concepto_ajustado,valor_campo_texto
            FROM nomina_planillas_ajuste_empleados_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND ($wherePlanilla) $whereIdConceptos ORDER BY naturaleza";
    $query=mysql_query($sql,$link);

    while ($row=mysql_fetch_array($query)) {

        $acumApropiacionTotal       += ($row['naturaleza']=='Apropiacion')? $row['valor_concepto']:0;
        $acumApropiacionAjusteTotal += ($row['naturaleza']=='Apropiacion')? $row['valor_concepto_ajustado']:0;

        $acumDeduccionTotal         += ($row['naturaleza']=='Deduccion')? $row['valor_concepto']:0;
        $acumDeduccionAjusteTotal   += ($row['naturaleza']=='Deduccion')? $row['valor_concepto_ajustado']:0;

        // VERIFICAR SI SE ESTAN PIDIENDO EL INFORME POR EMPLEADO
        if ($agrupacion_PlanillaAjuste=='empleados') {
            // SI YA EXISTE ESE CONCEPTO, ENTONCES LE INCREMENTAMOS EL VALOR DEL CONCEPTO PARA NO SOBREESCRIBIRLO
            if (isset($arrayPlanillasEmpleadosConceptos[$row['id_empleado']][$row['id_concepto']]['valor_concepto'])) {
                $arrayPlanillasEmpleadosConceptos[$row['id_empleado']][$row['id_concepto']]['valor_concepto']          += $row['valor_concepto'];
                $arrayPlanillasEmpleadosConceptos[$row['id_empleado']][$row['id_concepto']]['valor_concepto_ajustado'] += $row['valor_concepto_ajustado'];
                $arrayPlanillasEmpleadosConceptos[$row['id_empleado']][$row['id_concepto']]['valor_campo_texto']       += $row['valor_campo_texto'];
            }
            // SINO EXISTE, SE CREA EL CONCEPTO EN EL ARRAY
            else{
            $arrayPlanillasEmpleadosConceptos[$row['id_empleado']][$row['id_concepto']]  = array(
                                                                                                'id_planilla'             => $row['id_planilla'],
                                                                                                'codigo_concepto'         => $row['codigo_concepto'],
                                                                                                'concepto'                => $row['concepto'],
                                                                                                'naturaleza'              => $row['naturaleza'],
                                                                                                'valor_concepto'          => $row['valor_concepto'],
                                                                                                'valor_concepto_ajustado' => $row['valor_concepto_ajustado'],
                                                                                                'valor_campo_texto'       => $row['valor_campo_texto'],
                                                                                            );
            }

            // ARRAY PARA EL INFORME DISCRIMINANDO PLANILLAS
            // SI YA EXISTE ESE CONCEPTO, ENTONCES LE INCREMENTAMOS EL VALOR DEL CONCEPTO PARA NO SOBREESCRIBIRLO
            if (isset($arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_empleado']][$row['id_planilla']][$row['id_concepto']]['valor_concepto'])) {
                $arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_empleado']][$row['id_planilla']][$row['id_concepto']]['valor_concepto']+=$row['valor_concepto'];
                $arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_empleado']][$row['id_planilla']][$row['id_concepto']]['valor_campo_texto']+=$row['valor_campo_texto'];
            }
            // SINO EXISTE, SE CREA EL CONCEPTO EN EL ARRAY
            else{
            $arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_empleado']][$row['id_planilla']][$row['id_concepto']]  = array(
                                                                                                                                'id_planilla'             => $row['id_planilla'],
                                                                                                                                'codigo_concepto'         => $row['codigo_concepto'],
                                                                                                                                'concepto'                => $row['concepto'],
                                                                                                                                'naturaleza'              => $row['naturaleza'],
                                                                                                                                'valor_concepto'          => $row['valor_concepto'],
                                                                                                                                'valor_concepto_ajustado' => $row['valor_concepto_ajustado'],
                                                                                                                                'valor_campo_texto'       => $row['valor_campo_texto'],
                                                                                                                            );
            }

        }
        // INFORME POR CONCEPTO
        else{
            // ARRAY CON LA INFO DEL CONCEPTO
            $arrayConceptos[$row['id_concepto']] = array(
                                                            'codigo_concepto' => $row['codigo_concepto'],
                                                            'concepto'        => $row['concepto'],
                                                            'naturaleza'      => $row['naturaleza'],
                                                        );

           // SI YA EXISTE ESE CONCEPTO, ENTONCES LE INCREMENTAMOS EL VALOR DEL CONCEPTO PARA NO SOBREESCRIBIRLO
            if (isset($arrayPlanillasEmpleadosConcepto[$row['id_concepto']][$row['id_empleado']]['valor_concepto'])) {
                $arrayPlanillasEmpleadosConcepto[$row['id_concepto']][$row['id_empleado']]['valor_concepto']          +=$row['valor_concepto'];
                $arrayPlanillasEmpleadosConcepto[$row['id_concepto']][$row['id_empleado']]['valor_concepto_ajustado'] +=$row['valor_concepto_ajustado'];
                $arrayPlanillasEmpleadosConcepto[$row['id_concepto']][$row['id_empleado']]['valor_campo_texto']       +=$row['valor_campo_texto'];
            }
            // SINO EXISTE, SE CREA EL CONCEPTO EN EL ARRAY
            else{
            $arrayPlanillasEmpleadosConcepto[$row['id_concepto']][$row['id_empleado']]  = array(
                                                                                                'id_planilla'             => $row['id_planilla'],
                                                                                                'codigo_concepto'         => $row['codigo_concepto'],
                                                                                                'concepto'                => $row['concepto'],
                                                                                                'naturaleza'              => $row['naturaleza'],
                                                                                                'valor_concepto'          => $row['valor_concepto'],
                                                                                                'valor_concepto_ajustado' => $row['valor_concepto_ajustado'],
                                                                                                'valor_campo_texto'       => $row['valor_campo_texto'],
                                                                                            );
            }

            // SI YA EXISTE ESE CONCEPTO, ENTONCES LE INCREMENTAMOS EL VALOR DEL CONCEPTO PARA NO SOBREESCRIBIRLO
            if (isset($arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_concepto']][$row['id_planilla']][$row['id_empleado']]['valor_concepto'])) {
                $arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_concepto']][$row['id_planilla']][$row['id_empleado']]['valor_concepto']          +=$row['valor_concepto'];
                $arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_concepto']][$row['id_planilla']][$row['id_empleado']]['valor_concepto_ajustado'] +=$row['valor_concepto_ajustado'];
                $arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_concepto']][$row['id_planilla']][$row['id_empleado']]['valor_campo_texto']       +=$row['valor_campo_texto'];
            }
            // SINO EXISTE, SE CREA EL CONCEPTO EN EL ARRAY
            else{
            $arrayPlanillasEmpleadosConceptosDiscriminados[$row['id_concepto']][$row['id_planilla']][$row['id_empleado']]  = array(
                                                                                                'id_planilla'             => $row['id_planilla'],
                                                                                                'codigo_concepto'         => $row['codigo_concepto'],
                                                                                                'concepto'                => $row['concepto'],
                                                                                                'naturaleza'              => $row['naturaleza'],
                                                                                                'valor_concepto'          => $row['valor_concepto'],
                                                                                                'valor_concepto_ajustado' => $row['valor_concepto_ajustado'],
                                                                                                'valor_campo_texto'       => $row['valor_campo_texto'],
                                                                                            );
            }

        }


    }
    // print_r($arrayPlanillasEmpleadosConceptos);
    $headEmpleado=0;
    // VERIFICAR EL TIPO DE INFORME SI ES EMPLEADO
    if ($agrupacion_PlanillaAjuste=='empleados') {
        $id_empleado_old=0;
        foreach ($arrayPlanillasEmpleados as $id_empleado => $arrayPlanillasEmpleadosResul) {
            if (empty($arrayPlanillasEmpleadosConceptosDiscriminados[$id_empleado])) {continue;}

            // RENDERIZAR EL EMPLEADO
            $campoHead=($discrimina_planillas_PlanillaAjuste=='true')? '<td style="text-align:center;">PLANILLA</td>' : '' ;
            $colspanEmpleado=($discrimina_planillas_PlanillaAjuste=='true')? 9 : 8 ;
            $campoTotal=($discrimina_planillas_PlanillaAjuste=='true')? '<td>&nbsp;</td>' : '' ;
            $bodyTable .= '<tr>
                                <td colspan="'.$colspanEmpleado.'" class="empleado_nomina"><b>'.$arrayPlanillasEmpleadosResul['nombre_empleado'].' - '.$arrayPlanillasEmpleadosResul['tipo_documento'].' '.$arrayPlanillasEmpleadosResul['documento_empleado'].'</b></td>
                            </tr>
                            <tr class="titulos_conceptos_empleados">
                                '.$campoHead.'
                                <td style="">CONCEPTO</td>
                                <td style="text-align:center;">CANTIDAD</td>
                                <td style="text-align:right;">DEVENGO(+)</td>
                                <td style="text-align:right;">DEDUCCION(-)</td>
                                <td style="text-align:right;">APROPIACION</td>
                                <td style="text-align:right;">PROVISION</td>
                                <td style="text-align:right;">VALOR AJUSTADO</td>
                            '.(($IMPRIME_XLS=='true')? '<td>DOCUMENTO EMPLEADO</td>
                                                        <td>NOMBRE EMPLEADO</td>
                                                        <td>DOCUMENTO ENTIDAD</td>
                                                        <td>NOMBRE ENTIDAD</td>
                                                        <td>SUCURSAL</td> </tr>' : '</tr>');
            $style='style="background:#EEE;"';
            // SI SE DISCRIMINAN LAS PLANILLAS
            if ($discrimina_planillas_PlanillaAjuste=='true') {
                 // RECORRER LOS CONCEPTOS DEL EMPLEADO
                foreach ($arrayPlanillasEmpleadosConceptosDiscriminados[$id_empleado] as $id_planilla => $arrayResul) {
                    foreach ($arrayResul as $id_concepto => $arrayPlanillasEmpleadosConceptosResul) {
                        $valor_campo_texto = ($arrayPlanillasEmpleadosConceptosResul['valor_campo_texto']>0)? $arrayPlanillasEmpleadosConceptosResul['valor_campo_texto'] : '' ;

                        // ACUMULADOS
                        $acumDevengo     += ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Devengo')?     $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                        $acumDeduccion   += ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Deduccion')?   $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                        $acumApropiacion += ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Apropiacion')? $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                        $acumProvision   += ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Provision')?   $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                        $acumAjuste      += $arrayPlanillasEmpleadosConceptosResul['valor_concepto_ajustado'];

                        // VARIABLES
                        $devengo     = ($arrayPlanillasEmpleadosConceptosResul['naturaleza']== 'Devengo')?     $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                        $deduccion   = ($arrayPlanillasEmpleadosConceptosResul['naturaleza']== 'Deduccion')?   $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                        $apropiacion = ($arrayPlanillasEmpleadosConceptosResul['naturaleza']== 'Apropiacion')? $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                        $provision   = ($arrayPlanillasEmpleadosConceptosResul['naturaleza']== 'Provision')?   $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                        $valor_ajustado = $arrayPlanillasEmpleadosConceptosResul['valor_concepto_ajustado'];

                        $style=($style!='')? '' : 'style="background:#f7f7f7;"' ;
                        $bodyTable.='<tr class="filaConcepto" '.$style.'>
                                        <td style="padding-left: 10px;text-align:center;">'.$arrayPlanillas[$id_planilla]['consecutivo'].'</td>
                                        <td style="padding-left: 10px;">'.$arrayPlanillasEmpleadosConceptosResul['concepto'].'</td>
                                        <td style="text-align:center;">'.$valor_campo_texto.'</td>
                                        <td style="text-align:right;">'.$devengo.'</td>
                                        <td style="text-align:right;">'.$deduccion.'</td>
                                        <td style="text-align:right;">'.$apropiacion.'</td>
                                        <td style="text-align:right;">'.$provision.'</td>
                                        <td style="text-align:right;">'.$valor_ajustado.'</td>
                                    '.(($IMPRIME_XLS=='true')? '<td>'.$arrayPlanillasEmpleados[$id_empleado]['documento_empleado'].'</td>
                                                                <td>'.$arrayPlanillasEmpleados[$id_empleado]['nombre_empleado'].'</td>
                                                                <td>'.$arrayEntidades[$arrayIdEntidades[$id_empleado][$id_concepto]['id_entidad']]['numero_identificacion'].'</td>
                                                                <td>'.$arrayEntidades[$arrayIdEntidades[$id_empleado][$id_concepto]['id_entidad']]['nombre'].'</td>
                                                                <td>'.$arrayPlanillas[$id_planilla]['sucursal'].'</td> </tr>' : '</tr>') ;
                    }

                }
            }
            // SI NO SE DISCRIMINAN LAS PLANILLAS
            else{
                 // RECORRER LOS CONCEPTOS DEL EMPLEADO
                foreach ($arrayPlanillasEmpleadosConceptos[$id_empleado] as $id_concepto => $arrayPlanillasEmpleadosConceptosResul) {
                    $valor_campo_texto = ($arrayPlanillasEmpleadosConceptosResul['valor_campo_texto']>0)? $arrayPlanillasEmpleadosConceptosResul['valor_campo_texto'] : '' ;

                    // ACUMULADOS
                    $acumDevengo     += ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Devengo')?     $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                    $acumDeduccion   += ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Deduccion')?   $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                    $acumApropiacion += ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Apropiacion')? $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                    $acumProvision   += ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Provision')?   $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                    $acumAjuste      += $arrayPlanillasEmpleadosConceptosResul['valor_concepto_ajustado'];

                    // VARIABLES
                    $devengo     = ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Devengo')?     $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                    $deduccion   = ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Deduccion')?   $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                    $apropiacion = ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Apropiacion')? $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                    $provision   = ($arrayPlanillasEmpleadosConceptosResul['naturaleza']=='Provision')?   $arrayPlanillasEmpleadosConceptosResul['valor_concepto'] : '' ;
                    $valor_ajustado = $arrayPlanillasEmpleadosConceptosResul['valor_concepto_ajustado'];

                    $style=($style!='')? '' : 'style="background:#f7f7f7;"' ;
                    $bodyTable.='<tr class="filaConcepto" '.$style.'>
                                    <td style="padding-left: 10px;">'.$arrayPlanillasEmpleadosConceptosResul['concepto'].'</td>
                                    <td style="text-align:center;">'.$valor_campo_texto.'</td>
                                    <td style="text-align:right;">'.$devengo.'</td>
                                    <td style="text-align:right;">'.$deduccion.'</td>
                                    <td style="text-align:right;">'.$apropiacion.'</td>
                                    <td style="text-align:right;">'.$provision.'</td>
                                    <td style="text-align:right;">'.$valor_ajustado.'</td>
                                '.(($IMPRIME_XLS=='true')? '<td>'.$arrayPlanillasEmpleados[$id_empleado]['documento_empleado'].'</td>
                                                            <td>'.$arrayPlanillasEmpleados[$id_empleado]['nombre_empleado'].'</td>
                                                            <td>'.$arrayEntidades[$arrayIdEntidades[$id_empleado][$id_concepto]['id_entidad']]['numero_identificacion'].'</td>
                                                            <td>'.$arrayEntidades[$arrayIdEntidades[$id_empleado][$id_concepto]['id_entidad']]['nombre'].'</td>
                                                            <td>'.$arrayPlanillas[$id_planilla]['sucursal'].'</td> </tr>' : '</tr>');
                }
            }


            // AGREGAR LOS TOTALES
            $bodyTable.='<tr class="titulos_totales_empleados">
                            <td >TOTALES</td>
                            <td style="text-align:right;">&nbsp;</td>
                            '.$campoTotal.'
                            <td style="text-align:right;">'.$acumDevengo.'</td>
                            <td style="text-align:right;">'.$acumDeduccion.'</td>
                            <td style="text-align:right;">'.$acumApropiacion.'</td>
                            <td style="text-align:right;">'.$acumProvision.'</td>
                            <td style="text-align:right;">'.$acumAjuste.'</td>
                        </tr>';

            $bodyTable.='<tr><td>&nbsp;</td></tr>';

            $acumDevengo     = 0;
            $acumDeduccion   = 0;
            $acumApropiacion = 0;
            $acumProvision   = 0;

        }// FIN FOR EACH DEL BODY


    }
    // SI EL INFORME ES POR CONCEPTO
    else{

        // RECORRER EL ARRAY PRINCIPAL DE LOS CONCEPTOS
        foreach ($arrayConceptos as $id_concepto => $arrayConceptosResul) {
            // RENDERIZAR EL CONCEPTO
            $campoHead=($discrimina_planillas_PlanillaAjuste=='true')? '<td style="text-align:center;">PLANILLA</td>' : '' ;
            $colspanEmpleado=($discrimina_planillas_PlanillaAjuste=='true')? 7 : 6 ;
            $campoTotal=($discrimina_planillas_PlanillaAjuste=='true')? '<td>&nbsp;</td>' : '' ;
            $bodyTable .= '<tr>
                                <td colspan="'.$colspanEmpleado.'" class="empleado_nomina"><b>'.$arrayConceptosResul['codigo_concepto'].' - '.$arrayConceptosResul['concepto'].' ('.$arrayConceptosResul['naturaleza'].') </b></td>
                            </tr>
                            <tr class="titulos_conceptos_empleados">
                                '.$campoHead.'
                                <td style="text-align:left;">DOCUMENTO</td>
                                <td style="text-align:left;">EMPLEADO</td>
                                <td style="text-align:center;">CANTIDAD</td>
                                <td style="text-align:right;">VALOR</td>
                            </tr>';
            $style='style="background:#EEE;"';
            // SI SE DISCRIMINAN LAS PLANILLAS
            if ($discrimina_planillas_PlanillaAjuste=='true') {

                foreach ($arrayPlanillasEmpleadosConceptosDiscriminados[$id_concepto] as $id_planilla => $arrayPlanillasEmpleadosConceptoResul) {
                    foreach ($arrayPlanillasEmpleadosConceptoResul as $id_empleado => $arrayResul) {
                        $total_concepto+=$arrayResul['valor_concepto'];
                        $style=($style!='')? '' : 'style="background:#f7f7f7;"' ;
                        $bodyTable.='<tr class="filaConcepto" '.$style.'>
                                        <td style="padding-left: 10px;text-align:center;">'.$arrayPlanillas[$id_planilla]['consecutivo'].'</td>
                                        <td style="">'.$arrayEmpleados[$id_empleado]['documento_empleado'].'</td>
                                        <td style="text-align:left;">'.$arrayEmpleados[$id_empleado]['nombre_empleado'].'</td>
                                        <td style="text-align:center;">'.$arrayResul['valor_campo_texto'].'</td>
                                        <td style="text-align:right;">'.$arrayResul['valor_concepto'].'</td>
                                    </tr>';
                    }
                }

                 // AGREGAR LOS TOTALES
                $bodyTable.='<tr class="titulos_totales_empleados">
                                <td colspan="'.($colspanEmpleado-1).'" >TOTAL</td>
                                <td style="text-align:right;">'.$total_concepto.'</td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>';
                $total_concepto=0;

            }
            else{
                foreach ($arrayPlanillasEmpleadosConcepto[$id_concepto] as $id_empleado => $arrayResul) {
                    $total_concepto+=$arrayResul['valor_concepto'];
                    $style=($style!='')? '' : 'style="background:#f7f7f7;"' ;
                    $bodyTable.='<tr class="filaConcepto" '.$style.'>
                                    <td style="padding-left: 10px;">'.$arrayEmpleados[$id_empleado]['documento_empleado'].'</td>
                                    <td style="text-align:left;">'.$arrayEmpleados[$id_empleado]['nombre_empleado'].'</td>
                                    <td style="text-align:center;">'.$arrayResul['valor_campo_texto'].'</td>
                                    <td style="text-align:right;">'.$arrayResul['valor_concepto'].'</td>
                                </tr>';
                }

                 // AGREGAR LOS TOTALES
                $bodyTable.='<tr class="titulos_totales_empleados">
                                <td colspan="'.($colspanEmpleado-1).'" >TOTAL</td>
                                <td style="text-align:right;">'.$total_concepto.'</td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>';
                $total_concepto=0;
            }
        }

    }


$subtitulo_cabecera.='Desde '.$MyInformeFiltroFechaInicio.' Hasta '.$MyInformeFiltroFechaFinal;

?>
<style>
	.contenedor_informe, .contenedor_titulo_informe{
        width         :	100%;
        /*border-bottom :	1px solid #CCC;*/
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
        font-weight: bold;
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
        border: 1px solid #EEE;
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
                    <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;text-transform: uppercase;"><?php echo $nombre_informe ?></td></tr>
                    <tr><td  style="font-size:11px;text-align:center;"><?php echo $subtitulo_cabecera; ?></td></tr>
                    <!--<tr><td style="font-size:11px; text-align:center;" >Impreso: <?php echo fecha_larga_hora_m(date('Y-m-d H:i:s')); ?></td></tr>-->
                </table>

            </div>
        </div>

    </div>


        <table class="table"  cellspacing="0">
             <?php echo $bodyTable; ?>
        </table>

        <table class="table">
            <tr class="empleado_nomina">
                <td colspan="7" style="text-align:center;">TOTALES</td>
            </tr>
            <tr class="titulos_conceptos_empleados">
                <td>DEDUCCION PROVISIONADA</td>
                <td>APROPIACION PROVISIONADA</td>
                <td>DEDUCCION+APROPIACION</td>

                <td>DEDUCCION AJUSTADA</td>
                <td>APROPIACION AJUSTADA</td>
                <td>DEDUCCION+APROPIACION</td>
            </tr>
            <tr class="filaConcepto">
                <td style="text-align:center;"><?php echo $acumDeduccionTotal; ?></td>
                <td style="text-align:center;"><?php echo $acumApropiacionTotal; ?></td>
                <td style="text-align:center;"><?php echo ($acumDeduccionTotal+$acumApropiacionTotal) ?></td>
                <td style="text-align:center;"><?php echo $acumDeduccionAjusteTotal; ?></td>
                <td style="text-align:center;"><?php echo $acumApropiacionAjusteTotal; ?></td>
                <td style="text-align:center;"><?php echo ($acumDeduccionAjusteTotal+$acumApropiacionAjusteTotal) ?></td>
            </tr>
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

