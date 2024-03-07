<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=estado_situacion_financiera.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa       = $_SESSION['EMPRESA'];
    $desde            = $MyInformeNiifFechaInicio;
    $hasta            = (isset($MyInformeNiifFechaFinal))? $MyInformeNiifFechaFinal : date("Y-m-d") ;
    $divTitleSucursal = '';
    $whereSucursal    = '';

    if (isset($MyInformeNiifFechaFinal) && isset($generar)) {
        $MyInformeNiifFechaFinal=$MyInformeNiifFechaFinal;
        $generar=$generar;
    }
    else{
        $MyInformeNiifFechaFinal=date("Y-m-d");
        $generar = 'Grupos';
        $script  = 'localStorage.MyInformeNiifFechaInicio="";
                    localStorage.MyInformeNiifFechaFinal="";
                    localStorage.generar_estado_situacion_financiera="";
                    localStorage.tipo_balance_estado_situacion_financiera="";
                    localStorage.mostrar_cuentas_estado_situacion_financiera=""';
    }

    $tipo_balance=(isset($tipo_balance))? $tipo_balance : 'comprobacion' ;

    //NIVELES A GENERAR EL INFORME
    if ($generar=='Grupos'){ $varCortar = 2; }
    else if ($generar=='Cuentas'){ $varCortar = 4; }
    else if ($generar=='Subcuentas'){ $varCortar = 6; }
    else if ($generar=='Auxiliares'){ $varCortar = 8; }


    if ($tipo_balance=='comparativo') {
        $where="AN.fecha <= '$MyInformeNiifFechaInicio'";
    }else{
        $where="AN.fecha <= '$MyInformeNiifFechaFinal'";
    }

    //VARIABLES TIPO CUENTAS
    $id_activo='1';
    $id_pasivo='2';
    $id_patrimonio='3';

    //====================== STRING CON EL QUERY DE ACTIVOS ==========================//
    $sqlActivos = "SELECT puc_niif.cuenta,
                            puc_niif.descripcion,
                            SUM(AN.debe-AN.haber) AS saldo
                        FROM puc_niif, asientos_niif AS AN
                        WHERE puc_niif.cuenta LIKE '$id_activo%'
                            AND LEFT(AN.codigo_cuenta,$varCortar) = puc_niif.cuenta
                            AND $where
                            AND AN.id_empresa=$id_empresa
                            AND puc_niif.id_empresa=$id_empresa
                        GROUP BY puc_niif.cuenta";

    //====================== STRING CON EL QUERY DE PASIVOS ==========================//

    $sqlPasivos="SELECT puc_niif.cuenta AS cuenta,
                            puc_niif.descripcion AS descripcion,
                            SUM(AN.debe-AN.haber) AS saldo
                        FROM puc_niif, asientos_niif AS AN
                        WHERE puc_niif.cuenta LIKE '$id_pasivo%'
                            AND LEFT(AN.codigo_cuenta,$varCortar) = puc_niif.cuenta
                            AND $where
                            AND AN.id_empresa=$id_empresa
                            AND puc_niif.id_empresa=$id_empresa
                        GROUP BY puc_niif.cuenta";

    //====================== STRING CON EL QUERY DE PASIVOS ==========================//
    // $cuentaUtilidad     = substr('36050501',0,$varCortar);
    // $wherePucPatrimonio = ($varCortar > 2)? "AND descripcion LIKE 'UTILIDAD DEL EJERCICIO%'": "";

    //SQL CUENTA UTILIDAD DEL EJERCICIO TABLE PUC
    // $sqlPatrimonio   = "SELECT id,cuenta,descripcion
    //                     FROM puc_niif
    //                     WHERE LEFT(cuenta,2)=36
    //                         $wherePucPatrimonio
    //                         AND id_empresa=$id_empresa
    //                     ORDER BY cuenta ASC";
    // $queryPatrimonio = mysql_query($sqlPatrimonio,$link);

    // while ($rowPatrimonio=mysql_fetch_array($queryPatrimonio)) {
    //     $indice      = $rowPatrimonio['cuenta'];
    //     $descripcion = $rowPatrimonio['descripcion'];
    //     if($cuentaUtilidad == $indice){ break; }
    // }

    // $cuentaUtilidad = $indice;
    // if($indice > 0){ $arrayCuentasPatrimonio[$cuentaUtilidad] = array('valor' => 0, 'descripcion' => $descripcion); }

    $sqlPatrimonio="SELECT puc_niif.cuenta AS cuenta,
                            puc_niif.descripcion AS descripcion,
                            SUM(AN.debe-AN.haber) AS saldo
                        FROM puc_niif,
                            asientos_niif AS AN
                        WHERE AN.codigo_cuenta LIKE '$id_patrimonio%'
                            AND LEFT(AN.codigo_cuenta,$varCortar) = puc_niif.cuenta
                            AND $where
                            AND AN.id_empresa=$id_empresa
                            AND puc_niif.id_empresa=$id_empresa
                        GROUP BY puc_niif.cuenta";

    $date = strtotime($MyInformeNiifFechaFinal);
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    //$dia=date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));
    // $MyInformeNiifFechaFinal=$anio.'-'.$mes.'-'.$dia;

    $arrayMeses['01']='Enero';
    $arrayMeses['02']='Febrero';
    $arrayMeses['03']='Marzo';
    $arrayMeses['04']='Abril';
    $arrayMeses['05']='Mayo';
    $arrayMeses['06']='Junio';
    $arrayMeses['07']='Julio';
    $arrayMeses['08']='Agosto';
    $arrayMeses['09']='Septiembre';
    $arrayMeses['10']='Octubre';
    $arrayMeses['11']='Noviembre';
    $arrayMeses['12']='Diciembre';


    //================ CONSULTAR EL TOTAL DE EL ESTADO DE RESULTADO  ======================================//

    //CONSULTAR LAS CUENTAS CONFIGURADAS PARA EL INFORME
    $sql="SELECT cuenta_niif,descripcion_cuenta_niif,clasificacion,informe FROM configuracion_informe_estado_resultado_niif WHERE id_empresa='$id_empresa' AND activo=1 AND clasificacion<>'cuentas' ";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $arrayClasificacionCuentas[$row['cuenta_niif']]=array('informe' => $row['informe'],'clasificacion'=>$row['clasificacion'],'descripcion'=>$row['descripcion_cuenta_niif']);

        $whereCuentas.=($whereCuentas=='')? " AN.codigo_cuenta LIKE '".$row['cuenta_niif']."%'" : " OR AN.codigo_cuenta LIKE '".$row['cuenta_niif']."%'";
    }

    //CADENA DE LA CONSULTA
    $sql="SELECT
            SUM(AN.haber-AN.debe) AS saldo,puc_niif.cuenta,puc_niif.descripcion,AN.centro_costos
            FROM
                asientos_niif AS AN,puc_niif
            WHERE
             AN.id_empresa=$id_empresa
             AND puc_niif.id_empresa=$id_empresa
             AND  ( $whereCuentas)
            AND AN.codigo_cuenta=puc_niif.cuenta
            AND $where";
    $query=mysql_query($sql,$link);

    $resultado_ultimo_ejercicio=mysql_result($query,0,'saldo');


//=============================== SI EL TIPO DE BALANCE ES IGUAL A COMPROBACION =============================================//

if ($tipo_balance=='comprobacion') {

    $datos_informe='<tr><td style="font-size:14px;">Estado de Situacion Financiera</td></tr>
                    <tr><td style="font-size:13px;">A '.$arrayMeses[$mes].' '.$dia.' de '.$anio.'</td></tr>';

    # code...
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

    //***************************************************************************************************************************

    $acumActivos    = 0;
    $acumPasivos    = 0;
    $acumPatrimonio = 0;

    //============================== ACTIVOS ====================================//
    //***************************************************************************//


        $queryActivos=mysql_query($sqlActivos,$link);
        while ($rowActivos=mysql_fetch_array($queryActivos)) {
            $cuenta=($mostrar_cuenta_niif=='true')? '<td class="defaultFont" >'.$rowActivos['cuenta'].'&nbsp;&nbsp;</td>' : '' ;
            $cuerpoActivosT .= '<tr>
                                    '.$cuenta.'
                                    <td class="defaultFont">'.$rowActivos['descripcion'].'</td>
                                    <td class="defaultFont" style="text-align:right;">'.validar_numero_formato($rowActivos['saldo'], $IMPRIME_XLS).'</td>
                                </tr>';
            $acumActivos+=$rowActivos['saldo'];
        }

    //============================== PASIVOS ====================================//
    //***************************************************************************//

        $queryPasivos=mysql_query($sqlPasivos,$link);

        while ($rowPasivos=mysql_fetch_array($queryPasivos)) {
            $cuenta=($mostrar_cuenta_niif=='true')? '<td class="defaultFont">'.$rowPasivos['cuenta'].'&nbsp;&nbsp;</td>' : '' ;
            $cuerpoPasivosT .= '<tr class="defaultFont">
                                    '.$cuenta.'
                                    <td class="defaultFont">'.$rowPasivos['descripcion'].'</td>
                                    <td class="defaultFont" style="text-align:right;">'.validar_numero_formato($rowPasivos['saldo'], $IMPRIME_XLS).'</td>
                                </tr>';

            $acumPasivos += $rowPasivos['saldo'];

        }

    //============================== PATRIMONIO ===================================//
    //*****************************************************************************//


        //SQL CUENTAS PATRIMONIO ASIENTOS COLGAAP


        $queryPatrimonio=mysql_query($sqlPatrimonio,$link);

        while ($rowPatrimonio=mysql_fetch_array($queryPatrimonio)) {
            $indice      = $rowPatrimonio['cuenta'];
            $valor       = $rowPatrimonio['saldo'];
            $descripcion = $rowPatrimonio['descripcion'];

            // echo $valor."<br>";

            $arrayCuentasPatrimonio[$indice]['valor']       = ($arrayCuentasPatrimonio[$indice]['valor'] > 0)? $arrayCuentasPatrimonio[$indice]['valor'] + $valor : $valor;
            $arrayCuentasPatrimonio[$indice]['descripcion'] = $descripcion;
        }

        foreach ($arrayCuentasPatrimonio AS $cuenta => $value) { $acumPatrimonio += $value['valor']; }

        // $ultimoEjercicio  = ($acumActivos+$acumPasivos-($acumPatrimonio));

        $acumPatrimonio  += $resultado_ultimo_ejercicio;

        //PASAR EL ACUMULADO A POSITIVO
        // if ($acumPatrimonio<0) {
        //     $acumPatrimonio=$acumPatrimonio*-1;
        // }

        foreach ($arrayCuentasPatrimonio AS $cuenta => $value) {
            $valorCuenta = ($cuenta == $cuentaUtilidad)? $resultado_ultimo_ejercicio : $value['valor'];
            $cuenta=($mostrar_cuenta_niif=='true')? '<td class="defaultFont">'.$cuenta.' &nbsp;&nbsp;</td>' : '' ;
            $cuerpoPatrimonioT .= '<tr class="defaultFont">
                                        '.$cuenta.'
                                        <td class="defaultFont">'.$value['descripcion'].'</td>
                                        <td class="defaultFont" style="text-align:right;">'.validar_numero_formato($valorCuenta, $IMPRIME_XLS).'</td>
                                    </tr>';

        }

        $colspan=($mostrar_cuenta_niif=='true')? 'colspan="2"' : '' ;
        $colspanHeaD=($mostrar_cuenta_niif=='true')? 'colspan="3"' : 'colspan="2"' ;
    //================================ ARMAR EL CUERPO DEL BALANCE TIPO COMPROBANTE ============================================//

    $cuerpoInforme = '<table style="width:95%" >
                        <tr><td '.$colspanHeaD.' class="labelResultNiif"> ACTIVO</td></tr>
                        '.$cuerpoActivosT.'
                        <tr>
                            <td class="labelResultNiif2" '.$colspan.'>TOTAL ACTIVO</td>
                            <td class="labelResultNiif3">'.validar_numero_formato($acumActivos, $IMPRIME_XLS).'</td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td '.$colspanHeaD.' class="labelResultNiif"> PASIVO</td></tr>
                        '.$cuerpoPasivosT.'
                        <tr>
                            <td class="labelResultNiif2" '.$colspan.'>TOTAL PASIVO</td>
                            <td class="labelResultNiif3">'.validar_numero_formato($acumPasivos, $IMPRIME_XLS).'</td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td '.$colspanHeaD.' class="labelResultNiif"> PATRIMONIO</td></tr>
                        <tr class="defaultFont">
                            <td class="defaultFont" '.$colspan.'><b>GANANCIAS ACUMULADAS</b></td>
                            <td class="defaultFont" style="text-align:right;">'.validar_numero_formato($resultado_ultimo_ejercicio, $IMPRIME_XLS).'</td>
                        </tr>
                        '.$cuerpoPatrimonioT.'
                        <tr>
                            <td class="labelResultNiif2" '.$colspan.' >TOTAL PATRIMONIO</td>
                            <td class="labelResultNiif3">'.validar_numero_formato($acumPatrimonio, $IMPRIME_XLS).'</td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td class="labelResultNiif2"  '.$colspan.'>SUMA DEL PASIVO Y EL PATRIMONIO</td>
                            <td class="labelResultNiif3">'.validar_numero_formato(($acumPatrimonio+$acumPasivos), $IMPRIME_XLS).'</td>
                        </tr>
                    </table><br>

                    <table align="center" style="text-align:center;width:70%;">
                        <tr><td><br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</td></tr>
                        <tr><td  class="defaultFont" style="border-top:1px solid;font-weight:bold;">APROBADO</td><td>&nbsp;</td><td class="defaultFont" style="border-top:1px solid;font-weight:bold;">REVISADO</td></tr>
                    </table>';

}

//=============================== SI EL TIPO DE BALANCE ES IGUAL A COMPARATIVO =============================================//
else if ($tipo_balance=='comparativo') {
    $datos_informe='<tr><td style="font-size:14px;">Estado de Situacion Financiera Comparativo</td></tr>';

    $colspan=($mostrar_cuenta_niif=='true')? ' colspan="2" ' : '' ;

    //========================================= ACTIVOS =============================================================//

    //RECORREMOS EL RESULTADO DEL PRIMER QUERY PARA ARMAR EL ARRAY DEL PRIMER PERIODO
    $queryActivos=mysql_query($sqlActivos,$link);

    while ($rowActivos=mysql_fetch_array($queryActivos)) {

        $arrayCuentasSaldoAnteriorActivos[$rowActivos['cuenta']]=$rowActivos['saldo'];
        $arrayCuentasActivo[$rowActivos['cuenta']]=$rowActivos['descripcion'];
    }

    //CREAMOS MAS QUERYS PARA HACER LA COMPARACION DEL SEGUNDO PERIODO DEL BALANCE
    $sqlActivosActual = "SELECT puc_niif.cuenta,
                            puc_niif.descripcion,
                            SUM(AN.debe-AN.haber) AS saldo
                        FROM puc_niif, asientos_niif AS AN
                        WHERE puc_niif.cuenta LIKE '1%'
                            AND LEFT(AN.codigo_cuenta,$varCortar) = puc_niif.cuenta
                            AND AN.fecha <= '$MyInformeNiifFechaFinal'
                            AND AN.id_empresa=$id_empresa
                            AND puc_niif.id_empresa=$id_empresa
                        GROUP BY puc_niif.cuenta";

    $queryActivosActual=mysql_query($sqlActivosActual,$link);

    while ($rowActivos=mysql_fetch_array($queryActivosActual)) {

        // echo $rowActivos['cuenta'].' <br> '.$rowActivos['descripcion'].' <br>';
        $arrayCuentasSaldoActualActivos[$rowActivos['cuenta']]=$rowActivos['saldo'];
        $arrayCuentasActivo[$rowActivos['cuenta']]=$rowActivos['descripcion'];
    }

    //RECORREMOS EL ARRAY CUENTAS ANTIVOS, Y ARMAMOS EL CUERPO DEL DOCUMENTO
    foreach ($arrayCuentasActivo as $indice => $valor) {
        //PERIODO ANTERIOR
        $acumSaldoAnterior+=$arrayCuentasSaldoAnteriorActivos[$indice];
        //NUEVO PERIODO
        $acumSaldoActual+=$arrayCuentasSaldoActualActivos[$indice];
        //DIFERENCIA
        $diferencia     =($arrayCuentasSaldoActualActivos[$indice]-$arrayCuentasSaldoAnteriorActivos[$indice]);
        $acumDiferencia +=$diferencia;
        //PORCENTAJE
        $porcentaje=($diferencia*100)/$arrayCuentasSaldoAnteriorActivos[$indice];
        // echo "(".$diferencia."*100)/ ".$arrayCuentasSaldoActualActivos[$indice]."  ".$porcentaje."<br>";

        $cuenta=($mostrar_cuenta_niif=='true')? '<td>'.$indice.'</td>' : '' ;

        $cuerpoActivosT .= '<tr class="defaultFont">
                                '.$cuenta.'
                                <td class="defaultFont" style="width:300px;">'.$valor.'</td>
                                <td class="defaultFont" style="text-align:right;width:100px;">'.validar_numero_formato($arrayCuentasSaldoAnteriorActivos[$indice], $IMPRIME_XLS).'</td>
                                <td class="defaultFont" style="text-align:right;width:100px;">'.validar_numero_formato($arrayCuentasSaldoActualActivos[$indice], $IMPRIME_XLS).'</td>
                            </tr>';
    }

    //======================================================= PASIVOS ====================================================================//

    $queryPasivos=mysql_query($sqlPasivos,$link);

    while ($rowPasivos=mysql_fetch_array($queryPasivos)) {

        // echo $rowPasivos['cuenta'].' <br> '.$rowPasivos['descripcion'].' <br>';
        $arrayCuentasSaldoAnteriorPasivos[$rowPasivos['cuenta']]=$rowPasivos['saldo'];
        $arrayCuentasPasivos[$rowPasivos['cuenta']]=$rowPasivos['descripcion'];
    }

    //CREAMOS MAS QUERYS PARA HACER LA COMPARACION DEL SEGUNDO PERIODO DEL BALANCE
    $sqlPasivosActual = "SELECT puc_niif.cuenta AS cuenta,
                            puc_niif.descripcion AS descripcion,
                            SUM(AN.debe-AN.haber) AS saldo
                        FROM puc_niif, asientos_niif AS AN
                        WHERE puc_niif.cuenta LIKE '2%'
                            AND LEFT(AN.codigo_cuenta,$varCortar) = puc_niif.cuenta
                            AND AN.fecha <= '$MyInformeNiifFechaFinal'
                            AND AN.id_empresa=$id_empresa
                            AND puc_niif.id_empresa=$id_empresa
                        GROUP BY puc_niif.cuenta";

    $queryPasivosActual=mysql_query($sqlPasivosActual,$link);
    // echo "-------------------------------------------------------<br>";
    while ($rowPasivos=mysql_fetch_array($queryPasivosActual)) {

        // echo $rowPasivos['cuenta'].' <br> '.$rowPasivos['descripcion'].' <br>'.$rowPasivos['saldo'].' <br>';
        $arrayCuentasSaldoActualPasivos[$rowPasivos['cuenta']]=$rowPasivos['saldo'];
        $arrayCuentasPasivos[$rowPasivos['cuenta']]=$rowPasivos['descripcion'];
    }

    //RECORREMOS EL ARRAY CUENTAS ANTIVOS, Y ARMAMOS EL CUERPO DEL DOCUMENTO
    foreach ($arrayCuentasPasivos as $indice => $valor) {
        //PERIODO ANTERIOR
        $acumSaldoAnteriorPasivos+=$arrayCuentasSaldoAnteriorPasivos[$indice];
        //NUEVO PERIODO
        $acumSaldoActualPasivos+=$arrayCuentasSaldoActualPasivos[$indice];
        //DIFERENCIA
        $diferenciaPasivos=$arrayCuentasSaldoActualPasivos[$indice]-$arrayCuentasSaldoAnteriorPasivos[$indice];
        $acumDiferenciaPasivos+=$diferenciaPasivos;
        //PORCENTAJE
        $porcentajePasivos=($diferenciaPasivos*100)/$arrayCuentasSaldoAnteriorPasivos[$indice];

        $cuenta=($mostrar_cuenta_niif=='true')? '<td>'.$indice.'</td>' : '' ;

        $cuerpoPasivosT .= '<tr class="defaultFont">
                                '.$cuenta.'
                                <td class="defaultFont" style="width:300px;">'.$valor.'</td>
                                <td class="defaultFont" style="text-align:right;width:100px;">'.validar_numero_formato($arrayCuentasSaldoAnteriorPasivos[$indice], $IMPRIME_XLS).'</td>
                                <td class="defaultFont" style="text-align:right;width:100px;">'.validar_numero_formato($arrayCuentasSaldoActualPasivos[$indice], $IMPRIME_XLS).'</td>
                            </tr>';
    }

    //======================================== PATRIMONIO ============================================================//
    //------------------------------ SALDO ANTERIOR AL PERIODO SELECCIONADO
    $queryPatrimonio=mysql_query($sqlPatrimonio,$link);

    while ($rowPatrimonio=mysql_fetch_array($queryPatrimonio)) {
        $indice      = $rowPatrimonio['cuenta'];
        $valor       = $rowPatrimonio['saldo'];
        $descripcion = $rowPatrimonio['descripcion'];

        $arrayCuentasPatrimonio[$indice]['valor']       = ($arrayCuentasPatrimonio[$indice]['valor'] > 0)? $arrayCuentasPatrimonio[$indice]['valor'] + $valor : $valor;
        $arrayCuentasPatrimonio[$indice]['descripcion'] = $descripcion;
    }

    foreach ($arrayCuentasActivo AS $cuenta => $value) { $acumPatrimonio += $value['valor']; }

    $ultimoEjercicio  = ($acumSaldoAnterior+$acumSaldoAnteriorPasivos-($acumPatrimonio));
    $acumPatrimonio  += $ultimoEjercicio;

    foreach ($arrayCuentasPatrimonio AS $cuenta => $value) {
        $valorCuenta = ($cuenta == $cuentaUtilidad)? $ultimoEjercicio: $value['valor'];

        $arrayCuentasSaldoAnteriorPatrimonio[$cuenta]=$valorCuenta;
        $arrayCuentasPatrimonios[$cuenta]=$value['descripcion'];
    }

    //--------------------------- SALDO ACTUAL (ENTRE EL RANGO DE FECHAS)

    // $cuentaUtilidad     = substr('36050501',0,$varCortar);
    // $wherePucPatrimonio = ($varCortar > 2)? "AND descripcion LIKE 'UTILIDAD DEL EJERCICIO%'": "";

    // //SQL CUENTA UTILIDAD DEL EJERCICIO TABLE PUC
    // $sqlPatrimonio   = "SELECT id,cuenta,descripcion
    //                     FROM puc_niif
    //                     WHERE LEFT(cuenta,2)=36
    //                         $wherePucPatrimonio
    //                         AND id_empresa=$id_empresa
    //                     ORDER BY cuenta ASC";
    // $queryPatrimonio = mysql_query($sqlPatrimonio,$link);

    // while ($rowPatrimonio=mysql_fetch_array($queryPatrimonio)) {
    //     $indice      = $rowPatrimonio['cuenta'];
    //     $descripcion = $rowPatrimonio['descripcion'];
    //     if($cuentaUtilidad == $indice){ break; }
    // }

    // $cuentaUtilidad = $indice;
    // if($indice > 0){ $arrayCuentasPatrimonio[$cuentaUtilidad] = array('valor' => 0, 'descripcion' => $descripcion); }

    $sqlPatrimonio="SELECT puc_niif.cuenta AS cuenta,
                            puc_niif.descripcion AS descripcion,
                            SUM(AN.debe-AN.haber) AS saldo
                        FROM puc_niif,
                            asientos_niif AS AN
                        WHERE AN.codigo_cuenta LIKE '3%'
                            AND LEFT(AN.codigo_cuenta,$varCortar) = puc_niif.cuenta
                            AND AN.fecha <= '$MyInformeNiifFechaFinal'
                            AND AN.id_empresa=$id_empresa
                            AND puc_niif.id_empresa=$id_empresa
                        GROUP BY puc_niif.cuenta";

    $queryPatrimonio=mysql_query($sqlPatrimonio,$link);

    while ($rowPatrimonio=mysql_fetch_array($queryPatrimonio)) {
        $indice      = $rowPatrimonio['cuenta'];
        $valor       = $rowPatrimonio['saldo'];
        $descripcion = $rowPatrimonio['descripcion'];

        $arrayCuentasPatrimonio[$indice]['valor']       = ($arrayCuentasPatrimonio[$indice]['valor'] > 0)? $arrayCuentasPatrimonio[$indice]['valor'] + $valor : $valor;
        $arrayCuentasPatrimonio[$indice]['descripcion'] = $descripcion;
    }

    foreach ($arrayCuentasActivo AS $cuenta => $value) { $acumPatrimonio += $value['valor']; }
    $acumPatrimonio=0;
    $ultimoEjercicio  = ($acumSaldoActual+$acumSaldoActualPasivos-($acumPatrimonio));
    $acumPatrimonio  += $ultimoEjercicio;

    foreach ($arrayCuentasPatrimonio AS $cuenta => $value) {
        $valorCuenta = ($cuenta == $cuentaUtilidad)? $ultimoEjercicio: $value['valor'];

        $arrayCuentasSaldoActualPatrimonio[$cuenta]=$valorCuenta;
        $arrayCuentasPatrimonios[$cuenta]=$value['descripcion'];

        // $cuerpoPatrimonioT.='<tr class="defaultFont">
        //                         <td>&nbsp;</td>
        //                         <td>'.$cuenta.'</td>
        //                         <td>'.$value['descripcion'].'</td>
        //                         <td style="text-align:right;">'.validar_numero_formato($valorCuenta, $IMPRIME_XLS).'</td>
        //                     </tr>
        //                     ';
    }

    //RECORRER LOS ARRAY Y ARMAR EL CUERPO DEL PATRIMONIO
    foreach ($arrayCuentasPatrimonios as $indice => $valor) {
        //PERIODO ANTERIOR
        $acumSaldoAnteriorPatrimonio+=$arrayCuentasSaldoAnteriorPatrimonio[$indice];
        //NUEVO PERIODO
        $acumSaldoActualPatrimonio+=$arrayCuentasSaldoActualPatrimonio[$indice];
        //DIFERENCIA
        $diferenciaPatrimonio=$arrayCuentasSaldoActualPatrimonio[$indice]-$arrayCuentasSaldoAnteriorPatrimonio[$indice];
        $acumDiferenciaPatrimonio+=$diferenciaPatrimonio;
        //PORCENTAJE
        $porcentajePatrimonio=($diferenciaPatrimonio*100)/$arrayCuentasSaldoAnteriorPatrimonio[$indice];

        $cuenta=($mostrar_cuenta_niif=='true')? '<td>'.$indice.'</td>' : '' ;

        $cuerpoPatrimonioT.='<tr class="defaultFont">
                                '.$cuenta.'
                                <td class="defaultFont" style="width:300px;">'.$valor.'</td>
                                <td class="defaultFont" style="text-align:right;width:100px;">'.validar_numero_formato($arrayCuentasSaldoAnteriorPatrimonio[$indice], $IMPRIME_XLS).'</td>
                                <td class="defaultFont" style="text-align:right;width:100px;">'.validar_numero_formato($arrayCuentasSaldoActualPatrimonio[$indice], $IMPRIME_XLS).'</td>
                            </tr>';
    }

    //============================= RESULTADO DE EL ULTIMO EJERCICIO ================================================//

    //CONSULTAR LAS CUENTAS CONFIGURADAS PARA EL INFORME
    $sql="SELECT cuenta_niif,descripcion_cuenta_niif,clasificacion,informe FROM configuracion_informe_estado_resultado_niif WHERE id_empresa='$id_empresa' AND activo=1 AND clasificacion<>'cuentas' ";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        $arrayClasificacionCuentas[$row['cuenta_niif']]=array('informe' => $row['informe'],'clasificacion'=>$row['clasificacion'],'descripcion'=>$row['descripcion_cuenta_niif']);

        $whereCuentas.=($whereCuentas=='')? " AN.codigo_cuenta LIKE '".$row['cuenta_niif']."%'" : " OR AN.codigo_cuenta LIKE '".$row['cuenta_niif']."%'";

    }

    //RESULTADO DEL EJERCICIO ANTERIOR
    //CADENA DE LA CONSULTA
    $sql="SELECT
            SUM(AN.haber-AN.debe) AS saldo
            FROM
                asientos_niif AS AN,puc_niif
            WHERE
             AN.id_empresa=$id_empresa
             AND puc_niif.id_empresa=$id_empresa
             AND  ( $whereCuentas)
            AND AN.codigo_cuenta=puc_niif.cuenta
            AND AN.fecha<='$MyInformeNiifFechaInicio' ";
    $query=mysql_query($sql,$link);

    $resultado_ultimo_ejercicio_anterior=mysql_result($query,0,'saldo');

    //RESULTADO DEL EJERCICIO ACTUAL
    //CADENA DE LA CONSULTA
    $sql="SELECT
            SUM(AN.haber-AN.debe) AS saldo
            FROM
                asientos_niif AS AN,puc_niif
            WHERE
             AN.id_empresa=$id_empresa
             AND puc_niif.id_empresa=$id_empresa
             AND  ( $whereCuentas)
            AND AN.codigo_cuenta=puc_niif.cuenta
            AND AN.fecha<='$MyInformeNiifFechaFinal'";
    $query=mysql_query($sql,$link);

    $resultado_ultimo_ejercicio_actual=mysql_result($query,0,'saldo');

    $diferencia_ganancia_acumulada=$resultado_ultimo_ejercicio_actual-$resultado_ultimo_ejercicio_anterior;
    $porcentaje_ganancia_acumulada=($diferencia_ganancia_acumulada*100)/$resultado_ultimo_ejercicio_anterior;

    $acumSaldoAnteriorPatrimonio += $resultado_ultimo_ejercicio_anterior;
    $acumSaldoActualPatrimonio   += $resultado_ultimo_ejercicio_actual;
    $acumDiferenciaPatrimonio    += $diferencia_ganancia_acumulada;

    // print_r($arrayCuentasPatrimonio);
    $cuerpoInforme='<table style="width:95%" cellspacing="10"  >
        <tr>
            <td class="labelResultNiif" '.$colspan.' >Reporte a Nivel de '.$generar.' </td>
            <td class="defaultFont" style="text-align:right;" width="100"><b>Periodo Anterior</b><br>'.$MyInformeNiifFechaInicio.'</td>
            <td class="defaultFont" style="text-align:right;" width="100"><b>a la Fecha </b><br>'.$MyInformeNiifFechaFinal.'</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr><td colspan="5" class="labelResultNiif"> ACTIVO</td></tr>

        '.$cuerpoActivosT.'

         <tr>
            <td class="labelResultNiif2" style="width:100px;" '.$colspan.' >TOTAL ACTIVO</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato($acumSaldoAnterior, $IMPRIME_XLS).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato($acumSaldoActual, $IMPRIME_XLS).'</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr><td colspan="5" class="labelResultNiif"> PASIVO</td></tr>
        '.$cuerpoPasivosT.'

         <tr>
            <td class="labelResultNiif2" style="width:100px;" '.$colspan.'>TOTAL PASIVO</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato($acumSaldoAnteriorPasivos, $IMPRIME_XLS).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato($acumSaldoActualPasivos, $IMPRIME_XLS).'</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr><td colspan="5" class="labelResultNiif"> PATRIMONIO</td></tr>
        <tr>
            <td class="labelResultNiif2" style="width:100px;" '.$colspan.' ><b>GANANCIAS ACUMULADAS</b></td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato($resultado_ultimo_ejercicio_anterior, $IMPRIME_XLS).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato($resultado_ultimo_ejercicio_actual, $IMPRIME_XLS).'</td>
        </tr>

        '.$cuerpoPatrimonioT.'

        <tr>
            <td class="labelResultNiif2" style="width:100px;" '.$colspan.' >TOTAL PATRIMONIO</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato($acumSaldoAnteriorPatrimonio, $IMPRIME_XLS).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato($acumSaldoActualPatrimonio, $IMPRIME_XLS).'</td>
        </tr>

        <tr>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td class="labelResultNiif2" style="border:1px; solid;width:100px;" '.$colspan.'>TOTAL PASIVO + TOTAL PATRIMONIO</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato(($acumSaldoAnteriorPasivos+$acumSaldoAnteriorPatrimonio), $IMPRIME_XLS).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.validar_numero_formato(($acumSaldoActualPasivos+$acumSaldoActualPatrimonio), $IMPRIME_XLS).'</td>
        </tr>
    </table>
    <br>
    <table align="center" style="text-align:center;width:70%;">
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <tr><td  class="defaultFont" style="border-top:1px solid;font-weight:bold;">APROBADO</td><td>&nbsp;</td><td class="defaultFont" style="border-top:1px solid;font-weight:bold;">REVISADO</td></tr>
    </table><br>';
}
else{ exit; }


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
        width         :	210px;
        padding       :	0 0 0 5px;
        white-space   : nowrap;
        overflow      : hidden;
        text-overflow : ellipsis;
	}
	.my_informe_Contenedor_Titulo_informe_Empresa{
        float     : left;
        width     : 100%;
        font-size : 16px;
        font-weight:bold;
	}
    .defaultFont{ font-size : 11px; }
    .labelResultNiif{ font-weight:bold;font-size: 14px; }
    .labelResultNiif2{ font-weight:bold;font-size: 12px;  width: 60%;}
    .labelResultNiif3{ font-weight:bold;font-size: 12px; text-align: right;}
</style>


<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body >
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center">
                <table align="center" style="text-align:center;" >
                    <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr><td  style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <?php echo $datos_informe; ?>
                </table>
               <!--  <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa?></div>
                <div style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></div>
                <div style="margin-bottom:8px;">A <?php echo $arrayMeses[$mes].' '.$dia.' de '.$anio;?></div> -->

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