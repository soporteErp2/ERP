<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
	ob_start();

    if($IMPRIME_XLS=='true'){
       header('Content-type: application/vnd.ms-excel');
       header("Content-Disposition: attachment; filename=balance_general.xls");
       header("Pragma: no-cache");
       header("Expires: 0");
    }

    $id_empresa       = $_SESSION['EMPRESA'];
    $desde            = $MyInformeFiltroFechaInicio;
    $hasta            = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;
    // $generar          = $MyInformeFiltro_0;
    $divTitleSucursal = '';
    $whereSucursal    = '';

    // echo$MyInformeFiltroFechaFinal=(isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;

    if (isset($MyInformeFiltroFechaFinal) && isset($generar)) {
        $MyInformeFiltroFechaFinal=$MyInformeFiltroFechaFinal;
        $generar=$generar;
    }else{
        $MyInformeFiltroFechaFinal=date("Y-m-d");
        $generar='Grupos';
        $script='localStorage.MyInformeFiltroFechaInicio="";
                 localStorage.MyInformeFiltroFechaFinal="";
                 localStorage.generar="";
                 localStorage.tipo_balance="";';
    }

    // echo $MyInformeFiltroFechaFinal.' - '.$generar;

    $tipo_balance=(isset($tipo_balance))? $tipo_balance : 'comprobacion' ;

    //NIVELES A GENERAR EL INFORME
    if ($generar=='Grupos'){ $varCortar = 2; }
    else if ($generar=='Cuentas'){ $varCortar = 4; }
    else if ($generar=='Subcuentas'){ $varCortar = 6; }
    else if ($generar=='Auxiliares'){ $varCortar = 8; }


    if ($tipo_balance=='comparativo') {
        $where="AC.fecha <= '$MyInformeFiltroFechaInicio'";
    }else{
        $where="AC.fecha <= '$MyInformeFiltroFechaFinal'";
    }

    //====================== STRING CON EL QUERY DE ACTIVOS ==========================//
    $sqlActivos = "SELECT puc.cuenta,
                            puc.descripcion,
                            SUM(AC.debe-AC.haber) AS saldo
                        FROM puc, asientos_colgaap AS AC
                        WHERE puc.cuenta LIKE '1%'
                            AND LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta
                            AND $where
                            AND AC.id_empresa=$id_empresa
                            AND puc.id_empresa=$id_empresa
                        GROUP BY puc.cuenta";

    //====================== STRING CON EL QUERY DE PASIVOS ==========================//

    $sqlPasivos="SELECT puc.cuenta AS cuenta,
                            puc.descripcion AS descripcion,
                            SUM(AC.debe-AC.haber) AS saldo
                        FROM puc, asientos_colgaap AS AC
                        WHERE puc.cuenta LIKE '2%'
                            AND LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta
                            AND $where
                            AND AC.id_empresa=$id_empresa
                            AND puc.id_empresa=$id_empresa
                        GROUP BY puc.cuenta";

    //====================== STRING CON EL QUERY DE PASIVOS ==========================//
    $cuentaUtilidad     = substr('36050501',0,$varCortar);
    $wherePucPatrimonio = ($varCortar > 2)? "AND descripcion LIKE 'UTILIDAD DEL EJERCICIO%'": "";

    //SQL CUENTA UTILIDAD DEL EJERCICIO TABLE PUC
    $sqlPatrimonio   = "SELECT id,cuenta,descripcion
                        FROM puc
                        WHERE LEFT(cuenta,2)=36
                            $wherePucPatrimonio
                            AND id_empresa=$id_empresa
                        ORDER BY cuenta ASC";
    $queryPatrimonio = mysql_query($sqlPatrimonio,$link);

    while ($rowPatrimonio=mysql_fetch_array($queryPatrimonio)) {
        $indice      = $rowPatrimonio['cuenta'];
        $descripcion = $rowPatrimonio['descripcion'];
        if($cuentaUtilidad == $indice){ break; }
    }
    $cuentaUtilidad = $indice;
    if($indice > 0){ $arrayCuentasPatrimonio[$cuentaUtilidad] = array('valor' => 0, 'descripcion' => $descripcion); }

    $sqlPatrimonio="SELECT puc.cuenta AS cuenta,
                            puc.descripcion AS descripcion,
                            SUM(AC.debe-AC.haber) AS saldo
                        FROM puc,
                            asientos_colgaap AS AC
                        WHERE AC.codigo_cuenta LIKE '3%'
                            AND LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta
                            AND $where
                            AND AC.id_empresa=$id_empresa
                            AND puc.id_empresa=$id_empresa
                        GROUP BY puc.cuenta";

     $date = strtotime($MyInformeFiltroFechaFinal);
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    //$dia=date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));
    // $MyInformeFiltroFechaFinal=$anio.'-'.$mes.'-'.$dia;

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

//=============================== SI EL TIPO DE BALANCE ES IGUAL A COMPROBACION =============================================//

if ($tipo_balance=='comprobacion') {

    $datos_informe='<tr ><td style="font-size:14px;">Balance General</td></tr>
                    <tr ><td style="font-size:13px;">A '.$arrayMeses[$mes].' '.$dia.' de '.$anio.'</td></tr>';

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

            $cuerpoActivosT.='<tr  >
                                    <td>&nbsp;</td>
                                    <td class="defaultFont" >'.$rowActivos['cuenta'].'</td>
                                    <td class="defaultFont" >'.$rowActivos['descripcion'].'</td>
                                    <td class="defaultFont" style="text-align:right;">'.number_format($rowActivos['saldo'], $_SESSION['DECIMALESMONEDA']).'</td>
                              </tr>
                                ';
            $acumActivos+=$rowActivos['saldo'];
        }

    //============================== PASIVOS ====================================//
    //***************************************************************************//

        $queryPasivos=mysql_query($sqlPasivos,$link);

        while ($rowPasivos=mysql_fetch_array($queryPasivos)) {

            $cuerpoPasivosT.='<tr class="defaultFont" >
                                    <td class="defaultFont" >&nbsp;</td>
                                    <td class="defaultFont" >'.$rowPasivos['cuenta'].'</td>
                                    <td class="defaultFont" >'.$rowPasivos['descripcion'].'</td>
                                    <td class="defaultFont" style="text-align:right;">'.number_format($rowPasivos['saldo'], $_SESSION['DECIMALESMONEDA']).'</td>
                              </tr>
                                ';

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

            $arrayCuentasPatrimonio[$indice]['valor']       = ($arrayCuentasPatrimonio[$indice]['valor'] > 0)? $arrayCuentasPatrimonio[$indice]['valor'] + $valor : $valor;
            $arrayCuentasPatrimonio[$indice]['descripcion'] = $descripcion;
        }

        foreach ($arrayCuentasActivo AS $cuenta => $value) { $acumPatrimonio += $value['valor']; }

        $ultimoEjercicio  = ($acumActivos+$acumPasivos-($acumPatrimonio));
        $acumPatrimonio  += $ultimoEjercicio;

        foreach ($arrayCuentasPatrimonio AS $cuenta => $value) {
            $valorCuenta = ($cuenta == $cuentaUtilidad)? $ultimoEjercicio: $value['valor'];

            $cuerpoPatrimonioT.='<tr class="defaultFont" >
                                    <td class="defaultFont" >&nbsp;</td>
                                    <td class="defaultFont" >'.$cuenta.'</td>
                                    <td class="defaultFont" >'.$value['descripcion'].'</td>
                                    <td class="defaultFont" style="text-align:right;">'.number_format($valorCuenta, $_SESSION['DECIMALESMONEDA']).'</td>
                                </tr>
                                ';

        }

    //================================ ARMAR EL CUERPO DEL BALANCE TIPO COMPROBANTE ============================================//

    $cuerpoInforme= '<table  style="width:95%" >
            <tr><td colspan="4" class="labelResult"> ACTIVO</td></tr>
              '.$cuerpoActivosT.'
            <tr><td>&nbsp;</td><td class="labelResult2" >TOTAL ACTIVO</td><td style="width:60%;">&nbsp;</td><td class="labelResult3" >'.number_format($acumActivos, $_SESSION['DECIMALESMONEDA']).'</td></tr>
          </table>
          <br>
          <table  style="width:95%" >
            <tr><td colspan="4" class="labelResult"> PASIVO</td></tr>
              '.$cuerpoPasivosT.'
            <tr><td>&nbsp;</td><td class="labelResult2" >TOTAL PASIVO</td><td style="width:60%;">&nbsp;</td><td class="labelResult3" >'.number_format($acumPasivos, $_SESSION['DECIMALESMONEDA']).'</td></tr>
          </table>

           <br>
          <table  style="width:95%" >
            <tr><td colspan="4" class="labelResult"> PATRIMONIO</td></tr>
              '.$cuerpoPatrimonioT.'
            <tr><td>&nbsp;</td><td class="labelResult2" >TOTAL PATRIMONIO</td><td style="width:60%;">&nbsp;</td><td class="labelResult3" >'.number_format($acumPatrimonio, $_SESSION['DECIMALESMONEDA']).'</td></tr>
          </table>
          <br>
          <table  style="width:95%" >

            <tr><td>&nbsp;</td><td class="labelResult2" style="width:40%;" >SUMA DEL PASIVO Y EL PATRIMONIO</td><td style="width:40%;">&nbsp;</td><td class="labelResult3" >'.number_format(($acumPatrimonio-$acumPasivos), $_SESSION['DECIMALESMONEDA']).'</td></tr>
          </table>

           <br>
    <table align="center" style="text-align:center;width:70%;" >
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
        <tr> <td  class="defaultFont" style="border-top:1px solid;font-weight:bold;" >APROBADO</td><td>&nbsp;</td><td class="defaultFont" style="border-top:1px solid;font-weight:bold;" >REVISADO</td> </tr>
    </table>

    ';

}

//=============================== SI EL TIPO DE BALANCE ES IGUAL A COMPARATIVO =============================================//
else if ($tipo_balance=='comparativo') {
    $datos_informe='<tr ><td style="font-size:14px;" >Balance General Comparativo</td></tr>';
    //========================================= ACTIVOS =============================================================//

    //RECORREMOS EL RESULTADO DEL PRIMER QUERY PARA ARMAR EL ARRAY DEL PRIMER PERIODO
    $queryActivos=mysql_query($sqlActivos,$link);

    while ($rowActivos=mysql_fetch_array($queryActivos)) {

            // echo $rowActivos['cuenta'].' <br> '.$rowActivos['descripcion'].' <br>';
            $arrayCuentasSaldoAnteriorActivos[$rowActivos['cuenta']]=$rowActivos['saldo'];
            $arrayCuentasActivo[$rowActivos['cuenta']]=$rowActivos['descripcion'];

    }

    //CREAMOS MAS QUERYS PARA HACER LA COMPARACION DEL SEGUNDO PERIODO DEL BALANCE
    $sqlActivosActual = "SELECT puc.cuenta,
                            puc.descripcion,
                            SUM(AC.debe-AC.haber) AS saldo
                        FROM puc, asientos_colgaap AS AC
                        WHERE puc.cuenta LIKE '1%'
                            AND LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta
                            AND AC.fecha <= '$MyInformeFiltroFechaFinal' AND AC.fecha >= '$MyInformeFiltroFechaInicio'
                            AND AC.id_empresa=$id_empresa
                            AND puc.id_empresa=$id_empresa
                        GROUP BY puc.cuenta";

    $queryActivosActual=mysql_query($sqlActivosActual,$link);

    while ($rowActivos=mysql_fetch_array($queryActivosActual)) {

            // echo $rowActivos['cuenta'].' <br> '.$rowActivos['descripcion'].' <br>';
            $arrayCuentasSaldoActualActivos[$rowActivos['cuenta']]=$rowActivos['saldo'];
            $arrayCuentasActivo[$rowActivos['cuenta']]=$rowActivos['descripcion'];

        }

    //RECORREMOS EL ARRAY CUENTAS ACTIVOS, Y ARMAMOS EL CUERPO DEL DOCUMENTO
    foreach ($arrayCuentasActivo as $indice => $valor) {
        //PERIODO ANTERIOR
        $acumSaldoAnterior+=$arrayCuentasSaldoAnteriorActivos[$indice];
        //NUEVO PERIODO
        $acumSaldoActual+=$arrayCuentasSaldoActualActivos[$indice];
        //DIFERENCIA
        $diferencia=($arrayCuentasSaldoActualActivos[$indice]-$arrayCuentasSaldoAnteriorActivos[$indice]);
        $acumDiferencia+=$diferencia;
        //PORCENTAJE
        $porcentaje=($diferencia*100)/$arrayCuentasSaldoAnteriorActivos[$indice];
        // echo "(".$diferencia."*100)/ ".$arrayCuentasSaldoActualActivos[$indice]."  ".$porcentaje."<br>";
        $cuerpoActivosT.='<tr class="defaultFont" >
                            <td >&nbsp;</td>
                            <td class="defaultFont" style="width:60px;">'.$indice.'</td>
                            <td class="defaultFont" style="width:300px;">'.$valor.'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($arrayCuentasSaldoAnteriorActivos[$indice], $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($arrayCuentasSaldoActualActivos[$indice], $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($diferencia, $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($porcentaje, $_SESSION['DECIMALESMONEDA']).'</td>
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
    $sqlPasivosActual = "SELECT puc.cuenta AS cuenta,
                            puc.descripcion AS descripcion,
                            SUM(AC.debe-AC.haber) AS saldo
                        FROM puc, asientos_colgaap AS AC
                        WHERE puc.cuenta LIKE '2%'
                            AND LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta
                            AND AC.fecha <= '$MyInformeFiltroFechaFinal' AND AC.fecha >= '$MyInformeFiltroFechaInicio'
                            AND AC.id_empresa=$id_empresa
                            AND puc.id_empresa=$id_empresa
                        GROUP BY puc.cuenta";

    $queryPasivosActual=mysql_query($sqlPasivosActual,$link);
    // echo "-------------------------------------------------------<br>";
    while ($rowPasivos=mysql_fetch_array($queryPasivosActual)) {

            // echo $rowPasivos['cuenta'].' <br> '.$rowPasivos['descripcion'].' <br>'.$rowPasivos['saldo'].' <br>';
            $arrayCuentasSaldoActualPasivos[$rowPasivos['cuenta']]=$rowPasivos['saldo'];
            $arrayCuentasPasivos[$rowPasivos['cuenta']]=$rowPasivos['descripcion'];

        }

    //RECORREMOS EL ARRAY CUENTAS ACTIVOS, Y ARMAMOS EL CUERPO DEL DOCUMENTO
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

        $cuerpoPasivosT.='<tr class="defaultFont" >
                            <td>&nbsp;</td>
                            <td class="defaultFont" style="width:60px;">'.$indice.'</td>
                            <td class="defaultFont" style="width:300px;">'.$valor.'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($arrayCuentasSaldoAnteriorPasivos[$indice], $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($arrayCuentasSaldoActualPasivos[$indice], $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($diferenciaPasivos, $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($porcentajePasivos, $_SESSION['DECIMALESMONEDA']).'</td>
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

    $cuentaUtilidad     = substr('36050501',0,$varCortar);
    $wherePucPatrimonio = ($varCortar > 2)? "AND descripcion LIKE 'UTILIDAD DEL EJERCICIO%'": "";

    //SQL CUENTA UTILIDAD DEL EJERCICIO TABLE PUC
    $sqlPatrimonio   = "SELECT id,cuenta,descripcion
                        FROM puc
                        WHERE LEFT(cuenta,2)=36
                            $wherePucPatrimonio
                            AND id_empresa=$id_empresa
                        ORDER BY cuenta ASC";
    $queryPatrimonio = mysql_query($sqlPatrimonio,$link);

    while ($rowPatrimonio=mysql_fetch_array($queryPatrimonio)) {
        $indice      = $rowPatrimonio['cuenta'];
        $descripcion = $rowPatrimonio['descripcion'];
        if($cuentaUtilidad == $indice){ break; }
    }

    $cuentaUtilidad = $indice;
    if($indice > 0){ $arrayCuentasPatrimonio[$cuentaUtilidad] = array('valor' => 0, 'descripcion' => $descripcion); }

    $sqlPatrimonio="SELECT puc.cuenta AS cuenta,
                            puc.descripcion AS descripcion,
                            SUM(AC.debe-AC.haber) AS saldo
                        FROM puc,
                            asientos_colgaap AS AC
                        WHERE AC.codigo_cuenta LIKE '3%'
                            AND LEFT(AC.codigo_cuenta,$varCortar) = puc.cuenta
                            AND AC.fecha <= '$MyInformeFiltroFechaFinal' AND AC.fecha >= '$MyInformeFiltroFechaInicio'
                            AND AC.id_empresa=$id_empresa
                            AND puc.id_empresa=$id_empresa
                        GROUP BY puc.cuenta";

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

            // $cuerpoPatrimonioT.='<tr class="defaultFont" >
            //                         <td>&nbsp;</td>
            //                         <td>'.$cuenta.'</td>
            //                         <td>'.$value['descripcion'].'</td>
            //                         <td style="text-align:right;">'.number_format($valorCuenta, $_SESSION['DECIMALESMONEDA']).'</td>
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

        $cuerpoPatrimonioT.='<tr class="defaultFont" >
                            <td>&nbsp;</td>
                            <td class="defaultFont" style="width:60px;">'.$indice.'</td>
                            <td class="defaultFont" style="width:300px;">'.$valor.'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($arrayCuentasSaldoAnteriorPatrimonio[$indice], $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($arrayCuentasSaldoActualPatrimonio[$indice], $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($diferenciaPatrimonio, $_SESSION['DECIMALESMONEDA']).'</td>
                            <td class="defaultFont" style="text-align:right;width:100px;">'.number_format($porcentajePatrimonio, $_SESSION['DECIMALESMONEDA']).'</td>
                        </tr>';
    }
    // print_r($arrayCuentasPatrimonio);
    $cuerpoInforme='<table  style="width:95%"   cellspacing="10" >
        <tr>
            <td class="labelResult" colspan="3" >Reporte a Nivel de '.$generar.' </td>
            <td class="defaultFont" style="text-align:center;" width="100"><b>Periodo Anterior</b><br>'.$MyInformeFiltroFechaInicio.'</td>
            <td class="defaultFont" style="text-align:center;" width="100"><b>a la Fecha </b><br>'.$MyInformeFiltroFechaFinal.'</td>
            <td class="defaultFont" style="text-align:center;font-weight:bold;" width="100">Diferencia</td>
            <td class="defaultFont" style="text-align:center;font-weight:bold;" width="100">Porcentaje</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr><td colspan="7" class="labelResult"> ACTIVO</td></tr>

        '.$cuerpoActivosT.'

         <tr>
            <td>&nbsp;</td>
            <td class="labelResult2" style="width:100px;" >TOTAL ACTIVO</td>
            <td >&nbsp;</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoAnterior, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoActual, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumDiferencia, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;">&nbsp;</td>

        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr><td colspan="7" class="labelResult"> PASIVO</td></tr>
        '.$cuerpoPasivosT.'

         <tr>
            <td>&nbsp;</td>
            <td class="labelResult2" style="width:100px;" >TOTAL PASIVO</td>
            <td >&nbsp;</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoAnteriorPasivos, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoActualPasivos, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumDiferenciaPasivos, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;">&nbsp;</td>

        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr><td colspan="7" class="labelResult"> PATRIMONIO</td></tr>
        '.$cuerpoPatrimonioT.'

         <tr>
            <td>&nbsp;</td>
            <td class="labelResult2" style="width:100px;" >TOTAL PATRIMONIO</td>
            <td >&nbsp;</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoAnteriorPatrimonio, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumSaldoActualPatrimonio, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;font-weight:bold;">'.number_format($acumDiferenciaPatrimonio, $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont" style="text-align:right;width:100px;">&nbsp;</td>

        </tr>

        <tr>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td class="labelResult2" style="border:1px; solid;width:100px;" colspan="2"  >TOTAL PASIVO + TOTAL PATRIMONIO</td>

            <td class="defaultFont"  style="text-align:right;width:100px;font-weight:bold;">'.number_format(($acumSaldoAnteriorPasivos+$acumSaldoAnteriorPatrimonio), $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont"  style="text-align:right;width:100px;font-weight:bold;">'.number_format(($acumSaldoActualPasivos+$acumSaldoActualPatrimonio), $_SESSION['DECIMALESMONEDA']).'</td>
            <td class="defaultFont"  style="text-align:right;width:100px;font-weight:bold;">'.number_format(($acumSaldoActualPasivos+$acumSaldoActualPatrimonio)-($acumSaldoAnteriorPasivos+$acumSaldoAnteriorPatrimonio), $_SESSION['DECIMALESMONEDA']).'</td>


        </tr>

    </table>
    <br>
    <table align="center" style="text-align:center;width:70%;" >
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td> </tr>
        <tr> <td  class="defaultFont" style="border-top:1px solid;font-weight:bold;" >APROBADO</td><td>&nbsp;</td><td class="defaultFont" style="border-top:1px solid;font-weight:bold;" >REVISADO</td> </tr>
    </table>
          <br>

    ';
}else{
    exit;
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
    .labelResult{ font-weight:bold;font-size: 14px; }
    .labelResult2{ font-weight:bold;font-size: 12px;  width: 20%;}
    .labelResult3{ font-weight:bold;font-size: 12px; text-align: right;}
</style>


<!--------------------------------   DESARROLLO DEL INFORME  ------------------------------------- -->
<!--***********************************************************************************************-->

<body >
    <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
        <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center">
                <table align="center" style="text-align:center;" >
                    <tr ><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                    <tr  ><td  style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                    <?php echo $datos_informe; ?>
                </table>
               <!--  <div class="my_informe_Contenedor_Titulo_informe_Empresa"><?php echo $nombre_empresa?></div>
                <div style="width:100%; font-weight:bold; font-size:14px; text-align:center;"><?php echo $nombre_informe ?></div>
                <div style="margin-bottom:8px;" >A <?php echo $arrayMeses[$mes].' '.$dia.' de '.$anio;?></div> -->

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