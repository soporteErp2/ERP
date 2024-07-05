<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    header('Content-Type: text/html; charset=UTF-8');

    if (!isset($arrayCentroCostosJSON) && $arrayCentroCostosJSON=='[]') { $arrayCentroCostosJSON=''; }


    $id_empresa = $_SESSION['EMPRESA'];
    $object = new ErpReport($id_formato,$MyInformeFiltroFechaInicio,$separador_miles,$separador_decimales,$arrayCentroCostosJSON,$id_empresa,$IMPRIME_XLS,$mysql);
    $object->createFormat();


    /**
    * @class ErpReport
    *
    */
    class ErpReport
    {
        private $mysql                    = '';
        private $id_formato               = '';
        private $fecha                    = '';
        private $separador_miles          = '';
        private $separador_decimales      = '';
        private $id_empresa               = '';
        private $ccosFiltro               = '';
        private $xlsPrint                 = '';
        private $arrayfilasFormato        = '';
        private $arrayFilasCuentasFormato = '';
        private $whereAsientos            = '';
        private $whereIdTerceros          = '';
        private $arrayColumnasFormato     = '';
        private $arrayTerceros            = '';
        private $arrayJoined              = '';
        private $arrayMeses               = '';

        /**
        * @method construct
        * @param int id del formato
        * @param srt fecha respectiva del periodo
        * @param obj objeto de conexion mysql
        */
        function __construct($id_formato,$fecha,$separador_miles,$separador_decimales,$arrayCentroCostosJSON,$id_empresa,$IMPRIME_XLS,$mysql)
        {
            $this->id_formato          = $id_formato;
            $this->fecha               = $fecha;
            $this->separador_miles     = $separador_miles;
            $this->separador_decimales = $separador_decimales;
            $this->id_empresa          = $id_empresa;
            $this->ccosFiltro          = json_decode($arrayCentroCostosJSON);
            $this->xlsPrint            = $IMPRIME_XLS;
            $this->mysql               = $mysql;

            $this->arrayMeses[1]  = 'Enero';
            $this->arrayMeses[2]  = 'Febrero';
            $this->arrayMeses[3]  = 'Marzo';
            $this->arrayMeses[4]  = 'Abril';
            $this->arrayMeses[5]  = 'Mayo';
            $this->arrayMeses[6]  = 'Junio';
            $this->arrayMeses[7]  = 'Julio';
            $this->arrayMeses[8]  = 'Agosto';
            $this->arrayMeses[9]  = 'Septiembre';
            $this->arrayMeses[10] = 'Octubre';
            $this->arrayMeses[11] = 'Noviembre';
            $this->arrayMeses[12] = 'Diciembre';

        }

        /**
        * @method setSeccionesFormato secciones del formato
        */
        private function setSeccionesFormato()
        {
            $sql   = "SELECT id,nombre,titulo,totalizado FROM informes_formatos_secciones WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$this->id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id']]  = array(
                                                    'nombre'     => $row['nombre'],
                                                    'titulo'     => $row['titulo'],
                                                    'totalizado' => $row['totalizado'],
                                                );
            }

            $this->arraySeccionesFormato = $arrayTemp;
        }

        /**
        * @method setFilasFormato conceptos del formato
        */
        private function setFilasFormato()
        {
            $sql   = "SELECT id,id_seccion,nombre,tercero_unico,id_tercero,documento_tercero,tercero FROM informes_formatos_secciones_filas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$this->id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id_seccion']][$row['id']] = array(
                                                                    'nombre'            => $row['nombre'],
                                                                    'tercero_unico'     => $row['tercero_unico'],
                                                                    'id_tercero'        => $row['id_tercero'],
                                                                    'documento_tercero' => $row['documento_tercero'],
                                                                    'tercero'           => $row['tercero'],
                                                                );

            }

            $this->arrayfilasFormato = $arrayTemp;
            // print_r($this->arrayfilasFormato);
        }

        /**
        * @method setFilasCuentasFormato cuentas de los conceptos del formato
        */
        private function setFilasCuentasFormato()
        {
            $sql   = "SELECT
                            id,
                            id_formato,
                            codigo_formato,
                            nombre_formato,
                            id_seccion,
                            seccion,
                            id_columna,
                            columna,
                            id_fila,
                            fila,
                            id_cuenta_inicial,
                            cuenta_inicial,
                            descripcion_cuenta_inicial,
                            id_cuenta_final,
                            cuenta_final,
                            descripcion_cuenta_final,
                            forma_calculo
                        FROM
                            informes_formatos_secciones_filas_cuentas
                        WHERE
                            activo=1
                            AND id_empresa=$this->id_empresa
                            AND id_formato=$this->id_formato";

            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $id_seccion = $row['id_seccion'];
                $id_columna = $row['id_columna'];
                $id_fila    = $row['id_fila'];

                $arrayTemp[$id_seccion][$id_columna][$id_fila][$row['id']] = array(
                                                                                'id_formato'                 => $row['id_formato'],
                                                                                'codigo_formato'             => $row['codigo_formato'],
                                                                                'nombre_formato'             => $row['nombre_formato'],
                                                                                'id_seccion'                 => $row['id_seccion'],
                                                                                'seccion'                    => $row['seccion'],
                                                                                'id_columna'                 => $row['id_columna'],
                                                                                'columna'                    => $row['columna'],
                                                                                'id_fila'                    => $row['id_fila'],
                                                                                'fila'                       => $row['fila'],
                                                                                'id_cuenta_inicial'          => $row['id_cuenta_inicial'],
                                                                                'cuenta_inicial'             => $row['cuenta_inicial'],
                                                                                'descripcion_cuenta_inicial' => $row['descripcion_cuenta_inicial'],
                                                                                'id_cuenta_final'            => $row['id_cuenta_final'],
                                                                                'cuenta_final'               => $row['cuenta_final'],
                                                                                'descripcion_cuenta_final'   => $row['descripcion_cuenta_final'],
                                                                                'forma_calculo'              => $row['forma_calculo'],
                                                                                );

                $whereTemp.=($whereTemp=='')? "CAST(codigo_cuenta AS CHAR) >='$row[cuenta_inicial]' AND CAST(codigo_cuenta AS CHAR) <= '$row[cuenta_final]' " : " OR CAST(codigo_cuenta AS CHAR) >='$row[cuenta_inicial]' AND CAST(codigo_cuenta AS CHAR) <= '$row[cuenta_final]'" ;

            }

            $this->arrayFilasCuentasFormato = $arrayTemp;
            $this->whereAsientos = " AND ($whereTemp)";
        }

        /**
        * @method setColumnasFormato columnas del formato
        */
        private function setColumnasFormato()
        {
            $sql   = "SELECT id,id_seccion,orden,nombre FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$this->id_formato ORDER BY orden ASC";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id_seccion']][$row['id']]  = array('nombre' =>  $row['nombre'], 'orden' => $row['orden'] );

            }
            $this->arrayColumnasFormato = $arrayTemp;
        }

        /**
        * @method setAsientos consultar los asientos contables
        */
        private function setAsientos()
        {
            $fecha_inicial = split('-', $this->fecha)[0].'-'.split('-', $this->fecha)[1];

            $mes  = split('-', $this->fecha)[1];
            $mes = ( ($mes-1)<=0 )? 12 : $mes-1;
            $anio = ( $mes==12 )? split('-', $this->fecha)[0]-1 : split('-', $this->fecha)[0];
            // $anio = split('-', $this->fecha)[0];

            // $dia  = split('-', $this->fecha)[2];

            $fecha_final = $anio.'-'.$mes;


            foreach ($this->ccosFiltro as $indice => $id_centro_costos) {
                $whereCcos .= ($whereCcos=='')? ' id_centro_costos='.$id_centro_costos : ' OR id_centro_costos='.$id_centro_costos;
            }
            $whereCcos = ($whereCcos<>'')? " AND ( $whereCcos )" : "" ;

            // COLUMNA 1
            $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe) AS debito,
                        SUM(haber) AS credito
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        AND tipo_documento<>'EA'
                        AND fecha BETWEEN '$fecha_inicial-01' AND '$this->fecha'
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['codigo_cuenta']] = array(
                                                            'cuenta'      => $row['cuenta'],
                                                            'id_tercero'  => $row['id_tercero'],
                                                            'nit_tercero' => $row['nit_tercero'],
                                                            'tercero'     => $row['tercero'],
                                                            'debito'      => $row['debito'],
                                                            'credito'     => $row['credito'],
                                                        );
                $whereTemp .=($whereTemp=='')? 'id='.$row['id_tercero'] : ' OR id='.$row['id_tercero'] ;

                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY

                foreach ($this->arrayFilasCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $id_columna => $colResul){
                        // echo $this->arrayColumnasFormato[$id_seccion][$id_columna]['orden']."<br>";
                        if ($this->arrayColumnasFormato[$id_seccion][$id_columna]['orden']==2){ continue; }
                        foreach ($colResul as $id_fila => $filaResul){
                            foreach ($filaResul as $default_id => $arrayResul){

                                // SI LA FILA TIENE UN FILTRO QUE SEA UNICO POR TERCERO, Y EL ASIENTO NO ES DE ESE TERCERO CONFIGURADO, ENTONCES SE VACIA ESE VALOR PARA QUE NO SUME
                                if ($this->arrayfilasFormato[$id_seccion][$id_fila]['tercero_unico']=='Si' && $this->arrayfilasFormato[$id_seccion][$id_fila]['id_tercero']<>$row['id_tercero'] ){
                                    continue;
                                    // $row['debito']  = 0;
                                    // $row['credito'] = 0;
                                }

                                $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                                $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                                if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {

                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                }

                            }
                        }
                    }
                }

            }

            // $proof = strpos('52050301','52050302');
            // var_dump($proof);

            $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe-haber) AS saldo
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND fecha < '$fecha_inicial-01'
                        AND tipo_documento<>'NCC'
                        AND tipo_documento<>'EA'
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {

                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY
                foreach ($this->arrayFilasCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $id_columna => $colResul){
                        if ($this->arrayColumnasFormato[$id_seccion][$id_columna]['orden']==2){ continue; }
                        foreach ($colResul as $id_fila => $filaResul){
                            foreach ($filaResul as $default_id => $arrayResul){
                                // SI LA FILA TIENE UN FILTRO QUE SEA UNICO POR TERCERO, Y EL ASIENTO NO ES DE ESE TERCERO CONFIGURADO, ENTONCES SE VACIA ESE VALOR PARA QUE NO SUME
                                if ($this->arrayfilasFormato[$id_seccion][$id_fila]['tercero_unico']=='Si' && $this->arrayfilasFormato[$id_seccion][$id_fila]['id_tercero']<>$row['id_tercero'] ){
                                    // $row['saldo']  = 0;
                                    continue;
                                }

                                $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                                $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                                if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                }

                            }
                        }
                    }
                }

            }

            // COLUMNA 2
            $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe) AS debito,
                        SUM(haber) AS credito
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        AND tipo_documento<>'EA'
                        AND fecha BETWEEN '$fecha_final-01' AND '$fecha_final-31'
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['codigo_cuenta']] = array(
                                                            'cuenta'      => $row['cuenta'],
                                                            'id_tercero'  => $row['id_tercero'],
                                                            'nit_tercero' => $row['nit_tercero'],
                                                            'tercero'     => $row['tercero'],
                                                            'debito'      => $row['debito'],
                                                            'credito'     => $row['credito'],
                                                        );
                $whereTemp .=($whereTemp=='')? 'id='.$row['id_tercero'] : ' OR id='.$row['id_tercero'] ;

                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY

                foreach ($this->arrayFilasCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $id_columna => $colResul){
                        if ($this->arrayColumnasFormato[$id_seccion][$id_columna]['orden']==1){ continue; }
                        foreach ($colResul as $id_fila => $filaResul){
                            foreach ($filaResul as $default_id => $arrayResul){
                                // echo 'Tercero: '.$this->arrayfilasFormato[$id_fila]['tercero_unico'].' id tercero:'.$this->arrayfilasFormato[$id_fila]['id_tercero'].' tercero asientos: '.$row['id_tercero'].'<br>';
                                // SI LA FILA TIENE UN FILTRO QUE SEA UNICO POR TERCERO, Y EL ASIENTO NO ES DE ESE TERCERO CONFIGURADO, ENTONCES SE VACIA ESE VALOR PARA QUE NO SUME
                                if ($this->arrayfilasFormato[$id_seccion][$id_fila]['tercero_unico']=='Si' && $this->arrayfilasFormato[$id_seccion][$id_fila]['id_tercero']<>$row['id_tercero'] ){
                                    continue;
                                    // $row['debito']  = 0;
                                    // $row['credito'] = 0;
                                }

                                $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                                $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                                if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {

                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                }

                            }
                        }
                    }
                }

            }

            $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe-haber) AS saldo
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND fecha < '$fecha_final-01'
                        AND tipo_documento<>'NCC'
                        AND tipo_documento<>'EA'
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {

                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY
                foreach ($this->arrayFilasCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $id_columna => $colResul){
                        // echo $arrayColumnasFormato[$id_columna]['orden']."<br>";
                        if ($this->arrayColumnasFormato[$id_seccion][$id_columna]['orden']==1){ continue; }
                        foreach ($colResul as $id_fila => $filaResul){
                            foreach ($filaResul as $default_id => $arrayResul){
                                // SI LA FILA TIENE UN FILTRO QUE SEA UNICO POR TERCERO, Y EL ASIENTO NO ES DE ESE TERCERO CONFIGURADO, ENTONCES SE VACIA ESE VALOR PARA QUE NO SUME
                                if ($this->arrayfilasFormato[$id_seccion][$id_fila]['tercero_unico']=='Si' && $this->arrayfilasFormato[$id_seccion][$id_fila]['id_tercero']<>$row['id_tercero'] ){
                                    continue;
                                    // $row['saldo']  = 0;
                                }

                                $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                                $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                                if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                                    $this->arrayFilasCuentasFormato[$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                }

                            }
                        }
                    }
                }

            }
            // print_r($this->arrayFilasCuentasFormato);
        }

        /**
        * @method joinArrayTercero crear unir array de los terceros con los codigos dane de su ubicacion
        */
        private function joinArrayTercero($arrayCodPais,$arrayCodDepartamento,$arrayCodCiudad)
        {
            foreach ($this->arrayTerceros as $id_tercero => $arrayResul) {
                $this->arrayTerceros[$id_tercero]['codigo_pais']         = $arrayCodPais[$arrayResul['id_pais']];
                $this->arrayTerceros[$id_tercero]['codigo_departamento'] = $arrayCodDepartamento[$arrayResul['id_departamento']];
                $this->arrayTerceros[$id_tercero]['codigo_ciudad']       = $arrayCodCiudad[$arrayResul['id_ciudad']];
            }
        }

        /**
        * @method joinArrayConceptosCuentas unir arrays para el informe final
        */
        private function joinArrayConceptosCuentas()
        {
            // echo print_r($this->arrayAsientos);
            // echo html_entity_decode(json_encode($this->arrayAsientos));
            // var_dump(json_encode($this->arrayAsientos));
            // foreach ($this->arrayAsientos as $id_tercero => $arrayAsientosResul) {

            foreach ($this->arrayAsientos as $codigo_cuenta => $arrayResul){

                $saldo_actual = abs( (($arrayResul['saldo_anterior']+$arrayResul['debito'])-$arrayResul['credito']) );

                foreach ($arrayResul['fila'] as $id_fila => $arrayResulC) {

                    if ($arrayResulC['forma_calculo'] == 'suma_debitos') {
                        $saldo = $arrayResul['debito'];
                    }
                    else if ($arrayResulC['forma_calculo'] == 'suma_creditos') {
                        $saldo = $arrayResul['credito'];
                    }
                    else if ($arrayResulC['forma_calculo'] == 'debito_menos_credito') {
                        $saldo = $arrayResul['debito']-$arrayResul['credito'];
                    }
                    else if ($arrayResulC['forma_calculo'] == 'saldo_actual') {
                        $saldo = $saldo_actual;
                    }
                    else if ($arrayResulC['forma_calculo'] == 'saldo_inicial') {
                        $saldo = $arrayResul['saldo_anterior'];
                    }

                    // 222222222
                    // CUANTIAS MENORES

                    // 444444000
                    // OPERACIONES DEL EXTERIOR

                    // if (abs($saldo)<$arrayResulC['tope']) {
                    //     // echo '->'.$this->arrayTerceros[$arrayResul['id_tercero']]['numero_identificacion'].' - '.$this->arrayTerceros[$arrayResul['id_tercero']]['razon_social'].' saldo: '.$saldo_actual.'<br>';

                    //     if ($this->arrayTerceros[$arrayResul['id_tercero']]['id_pais']==49) {
                    //         $arrayTemp[$arrayResulC['id_concepto']][222222222][$arrayResulC['id_columna_formato']]+=$saldo;
                    //     }
                    //     else{
                    //        $arrayTemp[$arrayResulC['id_concepto']][444444000][$arrayResulC['id_columna_formato']]+=$saldo;
                    //        echo '<script>console.log("'.$arrayResul['id_tercero'].' - '.$saldo.' cuantias menores ");</script>';
                    //     }

                    // }
                    // else{
                        // echo '-]'.$this->arrayTerceros[$arrayResul['id_tercero']]['numero_identificacion'].' - '.$this->arrayTerceros[$arrayResul['id_tercero']]['razon_social'].' saldo: '.$saldo_actual.'<br>';

                    $arrayTemp[$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]+=$saldo;

                    // }

                }
            }

            // }

            $this->arrayJoined = $arrayTemp;
        }

        /**
        * @method createFormat Crear el formato solicitado por el usuario
        */
        public function createFormat()
        {
            $this->setSeccionesFormato();
            $this->setFilasFormato();
            $this->setFilasCuentasFormato();
            $this->setColumnasFormato();
            $this->setAsientos();

            // print_r($this->arrayFilasCuentasFormato);

            // RECORRER LA SECCION
            foreach ($this->arraySeccionesFormato as $id_seccion => $seccionResult) {
                // RECORRER LAS COLUMNAS
                $seccionColumnas='';
                $col = 0;
                $mes1 = split('-', $this->fecha)[1]*1;
                $mes2 = (($mes1-1) == 0)? 12: $mes1-1 ;

                foreach ($this->arrayColumnasFormato[$id_seccion] as $id_columna => $columna) {
                    $label_col =($col==0)? $this->arrayMeses[$mes1] : $this->arrayMeses[$mes2] ;
                    $seccionColumnas .="<td>$label_col </td>";
                    $col ++;
                }

                $seccionColumnas .="<td>Diferencia</td>";

                $seccionFilas = '';
                foreach ($this->arrayfilasFormato[$id_seccion] as $id_fila => $fila) {
                    $seccionFilas.="<tr>
                                        <td>$fila[nombre]</td>
                                  ";

                    $contSaldo = 0;
                    $acumFila  = 0;

                    foreach ($this->arrayColumnasFormato[$id_seccion] as $id_columna => $columna) {

                        $saldo=0;
                        foreach ($this->arrayFilasCuentasFormato[$id_seccion][$id_columna][$id_fila] as $default_id => $arrayResul) {
                            // echo $arrayResul['forma_calculo'].' - '.$arrayResul['debito'].' - '.$arrayResul['credito'].' - '.$arrayResul['credito'].'<br>';
                            if ($arrayResul['forma_calculo'] == 'suma_debitos') {
                                $saldo+= $arrayResul['debito'];
                            }
                            else if ($arrayResul['forma_calculo'] == 'suma_creditos') {
                                $saldo+= $arrayResul['credito'];
                            }
                            else if ($arrayResul['forma_calculo'] == 'debito_menos_credito') {
                                $saldo+= $arrayResul['debito']-$arrayResul['credito'];
                            }
                            else if ($arrayResul['forma_calculo'] == 'saldo_actual') {

                                $saldo += abs( (($arrayResul['saldo_anterior']+$arrayResul['debito'])-$arrayResul['credito']) );;
                            }
                            else if ($arrayResul['forma_calculo'] == 'saldo_inicial') {
                                $saldo += $arrayResul['saldo_anterior'];
                            }

                        }

                        $totalColumnas[$id_columna] += $saldo;
                        $acumFila =($contSaldo==0)? $saldo : $acumFila-$saldo;
                        // echo $contSaldo.'  '.$saldo.' -- '.$acumFila.'<br>';
                        $contSaldo++;
                        // $acumFila += $acumFila;
                        $seccionFilas .= "<td>".number_format ( $saldo ,2,$this->separador_decimales,$this->separador_miles )."</td>";
                    }

                    $seccionFilas .="<td>".number_format ( $acumFila ,2,$this->separador_decimales,$this->separador_miles )."</td>
                                    </tr>";
                }

                $bodyResul .="<tr class='thead'>
                                <td colspan='4' >$seccionResult[titulo]</td>
                            </tr>
                            <tr class='total'>
                                <td>&nbsp;</td>
                                $seccionColumnas
                            </tr>
                            $seccionFilas";

                // $bodyResul .="</tr>";

                // SI SE DEBE MOSTRAR EL TOTALIZADO
                if ($seccionResult['totalizado']=='Si') {
                    $bodyResul .= "<tr class='total'>
                                    <td>TOTAL $seccionResult[titulo]</td>
                                ";
                    $contSaldo = 0;
                    $acumFila  = 0;
                    foreach ($totalColumnas as $id_columna => $total) {
                        $bodyResul .= "<td>".number_format ( $total ,2,$this->separador_decimales,$this->separador_miles )."</td>";
                        $acumFila =($contSaldo==0)? $total : $acumFila-$total;
                        $contSaldo++;
                    }
                    $bodyResul .= "<td>".number_format ( $acumFila ,2,$this->separador_decimales,$this->separador_miles)."</td></tr>";
                }

                $bodyResul .= "<tr>
                                    <td>&nbsp;</td>
                                </tr>";

                $totalColumnas = '';

            }

            $bodyResul.='</tr>';

            $sql   = "SELECT nombre FROM informes_formatos WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            $nombre_formato = $this->mysql->result($query,0,'nombre');

            $fecha_inicial = split('-', $this->fecha)[0].'-'.split('-', $this->fecha)[1].'-01';

            if ($this->xlsPrint=='true') {
                header('Content-type: application/vnd.ms-excel');
                header("Content-Disposition: attachment; filename=$nombre_formato.xls");
                header("Pragma: no-cache");
                header("Expires: 0");

                echo "<style>
                        .tableInforme {
                            font-size       : 12px;
                            width           : 100%;
                            margin-top      : 20px;
                            border-collapse : collapse;
                        }

                        .tableInforme td{
                            padding-left: 5px;
                        }

                        .tableInforme .thead {
                            height       : 25px;
                            background   : #999;
                            padding-left : 10px;
                            height       : 25px;
                            font-size    : 12px;
                            color        : #FFF;
                            font-weight : bold;
                        }

                        .tableInforme .total {
                            height      : 25px;
                            background  : #EEE;
                            font-weight : bold;
                            color       : #8E8E8E;
                        }

                        .tableInforme .total td{
                            border-top    : 1px solid #999;
                            border-bottom : 1px solid #999;
                        }
                    </style>";
            }

            $formato="
                    <table align='center' style='text-align:center;'>
                        <tr><td class='titulo_informe_empresa' style='text-align:center;'>$_SESSION[NOMBREEMPRESA]</td></tr>
                        <tr><td style='font-size:13px;text-align:center;'><b>NIT</b> $_SESSION[NITEMPRESA]</td></tr>
                        <tr><td style='width:100%; font-weight:bold; font-size:14px; text-align:center;text-transform: uppercase;'>$nombre_formato</td></tr>
                        <tr><td style='font-size:11px;text-align:center;'>$_SESSION[NOMBRESUCURSAL]<br>$fecha_inicial a $this->fecha </td></tr>
                    </table>
                    <table class='tableInforme' >
                        $bodyResul
                    </table>
                    ";

            echo utf8_decode($formato);

        }
    }

?>