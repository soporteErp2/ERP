<?php

    /**
    * @class ClassReport
    *
    */
    class ClassReport
    {
        // public $mysql                    = '';
        // public $ccosFiltro               = '';
        // public $id_formato               = '';
        // public $fecha_inicio             = '';
        // public $fecha_final              = '';
        public $id_sucursal                 = '';
        public $id_empresa                  = '';
        // public $arrayFilasFormato        = '';
        // public $arraySaldoFila           = '';
        // public $arrayCuentasFilasFormato = '';
        // public $whereCuentas            = '';
        // public $whereIdTerceros          = '';
        // public $arraySeccionesFormato    = '';
        // public $arrayFormatoInfo         = '';
        // public $arrayAsientos            = '';
        public $arrayFilasFormato           = '';
        public $formatosRequeridos          = '';
        public $arrayCuentasFilasFormato    = '';
        public $arraySeccionesFormato       = '';
        public $arrayFormatoInfo            = '';

        /**
        * @method construct
        * @param int id del formato
        * @param srt fecha_inicio respectiva del periodo
        * @param srt fecha_final respectiva del periodo
        * @param str signo de separacion de miles
        * @param str signo de separacion de decimales
        * @param str Json con el centro de costos
        * @param int id de la empresa
        * @param str imprimir en excel
        * @param obj objeto de conexion mysql
        */
        function __construct($id_formato,$fecha_inicio,$fecha_final,$separador_miles,$separador_decimales,$arrayCentroCostosJSON,$id_sucursal,$id_empresa,$mysql)
        {
            $this->id_formato          = $id_formato;
            $this->fecha_inicio        = $fecha_inicio;
            $this->fecha_final         = $fecha_final;
            $this->id_sucursal         = $id_sucursal;
            $this->id_empresa          = $id_empresa;
            $this->ccosFiltro          = json_decode($arrayCentroCostosJSON);
            $this->separador_miles     = $separador_miles;
            $this->separador_decimales = $separador_decimales;
            $this->mysql               = $mysql;
        }

        /**
         * @method setFormatoInfo consultar la informacion del formato
         * @param [int] $id_formato id del formato a consultar
         */
        public function setFormatoInfo(){
            $sql   = "SELECT
                        codigo,
                        nombre,
                        filtro_terceros,
                        filtro_ccos,
                        filtro_corte_anual,
                        filtro_corte_mensual,
                        filtro_rango_fechas,
                        filtro_cuentas
                    FROM informes_niif_formatos WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            $this->arrayFormatoInfo['codigo']               = $this->mysql->result($query,0,'codigo');
            $this->arrayFormatoInfo['nombre']               = $this->mysql->result($query,0,'nombre');

            $filtro_terceros      = $this->mysql->result($query,0,'filtro_terceros');
            $filtro_ccos          = $this->mysql->result($query,0,'filtro_ccos');
            $filtro_corte_anual   = $this->mysql->result($query,0,'filtro_corte_anual');
            $filtro_corte_mensual = $this->mysql->result($query,0,'filtro_corte_mensual');
            $filtro_rango_fechas  = $this->mysql->result($query,0,'filtro_rango_fechas');
            $filtro_cuentas       = $this->mysql->result($query,0,'filtro_cuentas');

            $arrayFechaInicio = split('-', $this->fecha_inicio);
            $arrayFechaFinal  = split('-', $this->fecha_final);

            if ($this->id_sucursal<>'global') {
                $sql   = "SELECT nombre FROM empresas_sucursales WHERE id=$this->id_sucursal ";
                $query = $this->mysql->query($sql,$this->mysql->link);
                $nombre_sucursal = $this->mysql->result($query,0,'nombre');
                $this->arrayFormatoInfo['title'] .= "<tr><td style='font-size:12px;text-align:center;'>Sucursal: $nombre_sucursal</td></tr>";
            }

            // OPCIONES DE FILTROS
            if ($filtro_terceros == 'Si') {
                $this->arrayFormatoInfo['title']              .= "";
                $this->arrayFormatoInfo['whereSaldoAnterior'] .= "";
                $this->arrayFormatoInfo['whereSaldo']         .= "";
            }
            if ($filtro_ccos == 'Si') {
                $this->arrayFormatoInfo['title']              .= "";
                $this->arrayFormatoInfo['whereSaldoAnterior'] .= "";
                $this->arrayFormatoInfo['whereSaldo']         .= "";
            }
            if ($filtro_corte_anual == 'Si') {
                $this->arrayFormatoInfo['title']              .= "<tr><td style='font-size:11px;text-align:center;'> $arrayFechaFinal[0]-01-01 al $this->fecha_final</td></tr>";
                $this->arrayFormatoInfo['whereSaldoAnterior'] .= " AND fecha < '$arrayFechaFinal[0]-01-01' ";
                $this->arrayFormatoInfo['whereSaldo']         .= " AND fecha BETWEEN '$arrayFechaFinal[0]-01-01' AND '$this->fecha_final' ";
            }
            if ($filtro_corte_mensual == 'Si') {
                $this->arrayFormatoInfo['title']              .= "<tr><td style='font-size:11px;text-align:center;'> $arrayFechaFinal[0]-$arrayFechaFinal[1]-01 al $this->fecha_final</td></tr>";
                $this->arrayFormatoInfo['whereSaldoAnterior'] .= " AND fecha < '$arrayFechaFinal[0]-$arrayFechaFinal[1]-01' ";
                $this->arrayFormatoInfo['whereSaldo']         .= " AND fecha BETWEEN '$arrayFechaFinal[0]-$arrayFechaFinal[1]-01' AND '$this->fecha_final' ";
            }
            if ($filtro_rango_fechas == 'Si') {
                $this->arrayFormatoInfo['title']              .= "<tr><td style='font-size:11px;text-align:center;'> $this->fecha_inicio al $this->fecha_final</td></tr>";
                $this->arrayFormatoInfo['whereSaldoAnterior'] .= " AND fecha < '$this->fecha_inicio' ";
                $this->arrayFormatoInfo['whereSaldo']         .= " AND fecha BETWEEN '$this->fecha_inicio' AND '$this->fecha_final' ";
            }
            if ($filtro_cuentas == 'Si') {
                $this->arrayFormatoInfo['title']              .= "";
                $this->arrayFormatoInfo['whereSaldoAnterior'] .= "";
                $this->arrayFormatoInfo['whereSaldo']         .= "";
            }



            // return $arrayFormatoInfo;

        }

        /**
         * @method setSeccionesFormato consultar la informacion de las secciones del formato
         * @param [int] $id_formato id del formato a consultar
         */
        public function setSeccionesFormato()
        {
            $sql   = "SELECT
                        id,
                        codigo_seccion,
                        codigo_seccion_padre,
                        orden,
                        nombre,
                        tipo,
                        descripcion_tipo,
                        totalizado,
                        label_totalizado,
                        formula_totalizado,
                        padding
                         FROM informes_niif_formatos_secciones WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$this->id_formato ORDER BY orden ASC";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $codigo_seccion       = $row['codigo_seccion'];
                $codigo_seccion_padre = $row['codigo_seccion_padre'];
                $this->arraySeccionesFormato['child'][$codigo_seccion] = array(
                                                                        'id'                   =>$row['id'],
                                                                        'codigo_seccion'       =>$codigo_seccion,
                                                                        'id_formato'           =>$row['id_formato'],
                                                                        'orden'                =>$row['orden'],
                                                                        'nombre'               =>$row['nombre'],
                                                                        'tipo'                 =>$row['tipo'],
                                                                        'descripcion_tipo'     =>$row['descripcion_tipo'],
                                                                        'totalizado'           =>$row['totalizado'],
                                                                        'label_totalizado'          =>$row['label_totalizado'],
                                                                        'formula_totalizado'        =>$row['formula_totalizado'],
                                                                        'codigo_seccion_padre' =>$row['codigo_seccion_padre'],
                                                                        'padding'              =>$row['padding'],
                                                                        );

                $this->arraySeccionesFormato['parent'][$codigo_seccion_padre][] = $codigo_seccion;

            }

        }

        /**
        * @method setCuentasFilasFormato cuentas de los conceptos del formato
        * @param [int] $id_formato id del formato a consultar
        */
        public function setCuentasSeccionesFormato()
        {
            $sql   = "SELECT
                            id,
                            id_formato,
                            id_seccion,
                            id_cuenta_inicial,
                            cuenta_inicial,
                            descripcion_cuenta_inicial,
                            id_cuenta_final,
                            cuenta_final,
                            descripcion_cuenta_final,
                            forma_calculo
                        FROM
                            informes_niif_formatos_secciones_cuentas
                        WHERE
                            activo=1
                            AND id_empresa=$this->id_empresa
                            AND id_formato=$this->id_formato";

            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $this->arrayCuentasSeccionesFormato[$row['id']] = array(
                                                            'id_formato'                 => $row['id_formato'],
                                                            'id_seccion'                 => $row['id_seccion'],
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

            $this->arrayCuentasSeccionesFormato['whereCuentas'] = ($whereTemp<>'')? "$whereTemp" : "" ;

        }

        /**
        * @method setFilasFormato conceptos del formato
        * @param [int] $id_formato id del formato a consultar
        */
        public function setFilasFormato()
        {
            $sql   = "SELECT
                        id,
                        id_formato,
                        id_seccion,
                        codigo,
                        orden,
                        nombre,
                        naturaleza,
                        formula
                    FROM informes_niif_formatos_secciones_filas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$this->id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $this->arrayFilasFormato[$row['id']] = array(
                                                        'id'         => $row['id'],
                                                        'id_seccion' => $row['id_seccion'],
                                                        'codigo'      => $row['codigo'],
                                                        'orden'      => $row['orden'],
                                                        'nombre'     => $row['nombre'],
                                                        'naturaleza' => $row['naturaleza'],
                                                        'formula'    => $row['formula'],
                                                        );

            }

        }

        /**
        * @method setCuentasFilasFormato cuentas de los conceptos del formato
        * @param [int] $id_formato id del formato a consultar
        */
        public function setCuentasFilasFormato()
        {
            $sql   = "SELECT
                            id,
                            id_formato,
                            id_seccion,
                            id_fila,
                            id_cuenta_inicial,
                            cuenta_inicial,
                            descripcion_cuenta_inicial,
                            id_cuenta_final,
                            cuenta_final,
                            descripcion_cuenta_final,
                            forma_calculo
                        FROM
                            informes_niif_formatos_secciones_filas_cuentas
                        WHERE
                            activo=1
                            AND id_empresa=$this->id_empresa
                            AND id_formato=$this->id_formato";

            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $this->arrayCuentasFilasFormato[$row['id']] = array(
                                                            'id_formato'                 => $row['id_formato'],
                                                            'id_seccion'                 => $row['id_seccion'],
                                                            'id_fila'                    => $row['id_fila'],
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

            $this->arrayCuentasFilasFormato['whereCuentas'] = ($whereTemp<>'')? "$whereTemp" : "" ;

        }

       /**
        * @method setAsientos consultar los asientos contables
        */
        public function setAsientos()
        {

            if ($this->arrayCuentasFilasFormato['whereCuentas']=='' && $this->arrayCuentasSeccionesFormato['whereCuentas']=='') { return; }
            $whereSucursal = ($this->id_sucursal=='global')? "" : " AND id_sucursal=$this->id_sucursal " ;
            $whereCuentas =($this->arrayCuentasSeccionesFormato['whereCuentas']<>'')?
                            " AND (".$this->arrayCuentasFilasFormato['whereCuentas']." OR ".$this->arrayCuentasSeccionesFormato['whereCuentas']." ) " :
                            " AND (".$this->arrayCuentasFilasFormato['whereCuentas']." ) ";

            set_time_limit(0);
            ini_set("memory_limit", "1024M");

            $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe) AS debito,
                        SUM(haber) AS credito
                    FROM
                        asientos_niif
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        ".$whereCuentas.
                        $this->arrayFormatoInfo['whereSaldo']."
                        GROUP BY codigo_cuenta;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {

                $arrayAsientos[$row['codigo_cuenta']] = array(
                                                                'cuenta'      => $row['cuenta'],
                                                                'id_tercero'  => $row['id_tercero'],
                                                                'nit_tercero' => $row['nit_tercero'],
                                                                'tercero'     => $row['tercero'],
                                                                'debito'      => $row['debito'],
                                                                'credito'     => $row['credito'],
                                                            );
                $whereTemp .=($whereTemp=='')? 'id='.$row['id_tercero'] : ' OR id='.$row['id_tercero'] ;

                // RECORRER LA CONFIGURACION DE LAS FILAS PARA ASIGNARLO AL ARRAY
                foreach ($this->arrayCuentasFilasFormato as $id_row => $arrayResul){

                    $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                    $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                    if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']){
                        // $arrayAsientos[$row['id_tercero']][$row['codigo_cuenta']]['concepto'][$id_row] = $this->arrayConceptosCuentasFormato[$id_row];
                        $arrayAsientos[$row['codigo_cuenta']]['fila'][$arrayResul['id_fila']] = $this->arrayCuentasFilasFormato[$id_row];
                        $arrayAsientos[$row['codigo_cuenta']]['fila']['seccion'] = $this->arrayCuentasFilasFormato[$id_row]['id_seccion'];
                    }
                }

                // RECORRER LA CONFIGURACION DE LAS SECCIONES PARA ASIGNARLO AL ARRAY
                foreach ($this->arrayCuentasSeccionesFormato as $id_row => $arrayResul){

                    $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                    $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                    if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']){
                        // $arrayAsientos[$row['id_tercero']][$row['codigo_cuenta']]['concepto'][$id_row] = $this->arrayConceptosCuentasFormato[$id_row];
                        $arrayAsientos[$row['codigo_cuenta']]['seccion'][$arrayResul['id_seccion']] = $this->arrayCuentasSeccionesFormato[$id_row];
                        // $arrayAsientos[$row['codigo_cuenta']]['seccion']['seccion'] = $this->arrayCuentasSeccionesFormato[$id_row]['id_seccion'];
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
                        asientos_niif
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        ".$whereCuentas.
                        $this->arrayFormatoInfo['whereSaldoAnterior']."
                        GROUP BY codigo_cuenta;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayAsientos[$row['codigo_cuenta']]['saldo_anterior'] = $row['saldo'];

                // RECORRER LA CONFIGURACION DE LAS FILAS PARA ASIGNARLO AL ARRAY
                foreach ($this->arrayCuentasFilasFormato as $id_row => $arrayResul){

                    $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                    $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                    if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']){
                        // $arrayAsientos[$row['id_tercero']][$row['codigo_cuenta']]['concepto'][$id_row] = $this->arrayConceptosCuentasFormato[$id_row];
                        $arrayAsientos[$row['codigo_cuenta']]['fila'][$arrayResul['id_fila']]= $this->arrayCuentasFilasFormato[$id_row];;
                        // $arrayAsientos[$row['codigo_cuenta']]['fila']['seccion'] = $this->arrayCuentasFilasFormato[$id_row]['id_seccion'];
                    }
                }

                // RECORRER LA CONFIGURACION DE LAS SECCIONES PARA ASIGNARLO AL ARRAY
                foreach ($this->arrayCuentasSeccionesFormato as $id_row => $arrayResul){

                    $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                    $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                    if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']){
                        // $arrayAsientos[$row['id_tercero']][$row['codigo_cuenta']]['concepto'][$id_row] = $this->arrayConceptosCuentasFormato[$id_row];
                        $arrayAsientos[$row['codigo_cuenta']]['seccion'][$arrayResul['id_seccion']] = $this->arrayCuentasSeccionesFormato[$id_row];
                        // $arrayAsientos[$row['codigo_cuenta']]['seccion']['seccion'] = $this->arrayCuentasSeccionesFormato[$id_row]['id_seccion'];
                    }
                }

            }
            // $this->whereIdTerceros = " AND ($whereTemp)";
            // ASIGNAR EL TOTAL POR FILA
            foreach ($arrayAsientos as $codigo_cuenta => $arrayResul){


                foreach ($arrayResul['fila'] as $id_fila => $arrayResulF) {
                    $saldo_actual = ($arrayResul['saldo_anterior']+$arrayResul['debito'])-$arrayResul['credito'];

                    if ($arrayResulF['forma_calculo'] == 'suma_debitos') {
                        $saldo = $arrayResul['debito'];
                    }
                    else if ($arrayResulF['forma_calculo'] == 'suma_creditos') {
                        $saldo = $arrayResul['credito'];
                    }
                    else if ($arrayResulF['forma_calculo'] == 'debito_menos_credito') {
                        $saldo = $arrayResul['debito']-$arrayResul['credito'];
                    }
                    else if ($arrayResulF['forma_calculo'] == 'saldo_actual') {
                        $saldo = $saldo_actual;
                    }
                    else if ($arrayResulF['forma_calculo'] == 'saldo_inicial') {
                        $saldo = $arrayResulF['saldo_anterior'];
                    }
                    $body .= "<tr><td>$codigo_cuenta</td> <td>$arrayResul[debito]</td><td>$arrayResul[credito]</td><td>$arrayResulF[saldo_anterior]</td><td>$saldo_actual</td> </tr>";
                    // echo $id_fila." => ".$saldo." - ".$arrayResulF['forma_calculo']." D: ".$arrayResul['debito']." C: ".$arrayResul['credito']." S.A: ".$arrayResul['saldo_anterior']."<br>";
                    $this->arrayFilasFormato[$id_fila]['total'] += $saldo;
                    // $this->arraySaldoFila[$id_fila] += $saldo;
                }

                foreach ($arrayResul['seccion'] as $id_seccion => $arrayResulF) {
                    $saldo_actual = ($arrayResulF['saldo_anterior']+$arrayResulF['debito'])-$arrayResulF['credito'];

                    if ($arrayResulF['forma_calculo'] == 'suma_debitos') {
                        $saldo = $arrayResul['debito'];
                    }
                    else if ($arrayResulF['forma_calculo'] == 'suma_creditos') {
                        $saldo = $arrayResul['credito'];
                    }
                    else if ($arrayResulF['forma_calculo'] == 'debito_menos_credito') {
                        $saldo = $arrayResul['debito']-$arrayResul['credito'];
                    }
                    else if ($arrayResulF['forma_calculo'] == 'saldo_actual') {
                        $saldo = $saldo_actual;
                    }
                    else if ($arrayResulF['forma_calculo'] == 'saldo_inicial') {
                        $saldo = $arrayResul['saldo_anterior'];
                    }

                    // echo "<script>console.log(' $id_seccion - debito: $arrayResul[debito] Credito: $arrayResul[credito] - saldo anterior: $arrayResul[saldo_anterior] - saldo_actual: $saldo_actual ');</script>";
                    // echo $id_fila." => ".$saldo." - ".$arrayResulF['forma_calculo']." D: ".$arrayResul['debito']." C: ".$arrayResul['credito']." S.A: ".$arrayResul['saldo_anterior']."<br>";
                    $this->arraySeccionesFormato['child'][$id_seccion]['total_cuentas'] += $saldo;
                    // print_r($this->arraySeccionesFormato[child][$id_seccion]);
                    // $this->arraySeccionesFormato['child'][$id_seccion]['total'] += 10;
                    // $this->arraySaldoFila[$id_fila] += $saldo;
                }

            }

            // echo "<table>$body</table>";
            // print_r($arrayAsientos);
            // print_r($this->arrayFilasFormato);
            // return $arrayFilasFormato;
            // ASIGNAR EL TOTAL DE LA SECCION CON LOS VALORES DE LA FILA
            // foreach ($this->arrayFilasFormato[$id_seccion] as $key => $arrayResult){
            //     $this->arraySeccionesFormato['child'][$codigo_seccion]['total']+=$this->arraySaldoFila[$arrayResult['id']];
            // }
        }

        /**
         * @method calculaFormula
         * @param  [arr] $arrayFilas filas del informe con los valores de cuentas
         */
        public function calculaFormulaFilas()
        {
            // REEMPLAZAR LA FORMULA CON LOS VALORES DE LAS FILAS Y SECCIONES QUE ESTAN EN LA FORMULADE LA MISMA
            foreach ($this->arrayFilasFormato as $id_fila => $arrayResult) {
                if ($arrayResult['formula']<>''){
                    // FILAS DE LA FORMULA
                    foreach ($this->arrayFilasFormato as $id_fila_replace => $arrayFila) {
                        $arrayFila['total'] =($arrayFila['total']=='')? 0 : $arrayFila['total'] ;
                        $this->arrayFilasFormato[$id_fila]['formula']=str_replace("[".$arrayFila['codigo']."]", $arrayFila['total'], $this->arrayFilasFormato[$id_fila]['formula']);
                    }
                    // SECCIONES DE LA FORMULA
                    foreach ($this->arraySeccionesFormato['child'] as $id_seccion_replace => $arraySeccion) {
                        // echo $arraySeccion['codigo'].' - '.$arraySeccion['nombre']." : ".$arraySeccion['total'].'<br>';
                        $arraySeccion['total'] =($arraySeccion['total']=='')? 0 : $arraySeccion['total'] ;
                        $this->arrayFilasFormato[$id_fila]['formula']=str_replace("{".$arraySeccion['codigo_seccion']."}", $arraySeccion['total'], $this->arrayFilasFormato[$id_fila]['formula']);
                    }

                    $this->arrayFilasFormato[$id_fila]['total'] = $this->calculaFormula($this->arrayFilasFormato[$id_fila]['formula']);

                }
            }


            // EJECUTAR LA FORMULA PARA HALLAR EL VALOR DE LOS CONCEPTOS
            // foreach ($this->arrayFilasFormato as $id_fila => $arrayResult) {
            //     if ($arrayResult['formula']<>''){
            //         // echo $arrayFilasFormato[$id_fila]['formula']."<br>";

            //         $formula=$this->arrayFilasFormato[$id_fila]['formula'];
            //         $this->arrayFilasFormato[$id_fila]['total'] = $this->calculaFormula($formula);
            //     }
            // }
        }



        /**
         * @method setValorSecciones establecer el totalizado de secciones a partir de las filas de la misma
         * @param [arr] $arrayFilasFormato filas de las secciones
         * @param [arr] $arraySeccionesFormato secciones del formato
         */
        public function setValorSecciones()
        {
            // ASIGNAR LOS TOTALES DE LAS SECCIONES CON LAS FILAS
            foreach ($this->arrayFilasFormato as $id_fila => $arrayResult) {
                foreach ($this->arraySeccionesFormato['child'] as $codigo_seccion => $arrayResultSecciones) {
                // echo "<script>console.log('$codigo_seccion - $arrayResult[total]');</script>";
                    if ($arrayResult['id_seccion']==$codigo_seccion && $arrayResultSecciones['calcula']<>'false') {
                        $this->arraySeccionesFormato['child'][$codigo_seccion]['total'] += $arrayResult['total'];
                    }
                }
            }

            // ASIGNAR LOS TOTALES DE LAS SECCIONES CON OTRAS SECCIONES
            foreach ($this->arraySeccionesFormato['child'] as $codigo_seccion_padre => $arrayResult){
                if ($arrayResult['totalizado']=='false' || $arrayResult['calcula']=='false') { continue; }
                foreach ($this->arraySeccionesFormato['child'] as $codigo_seccion_child => $arrayResultChild) {
                    if ($arrayResultChild['codigo_seccion_padre']==$codigo_seccion_padre) {
                        // echo "<script>console.log('$codigo_seccion_padre () -> $codigo_seccion_child = $arrayResultChild[total]');</script>";
                        $this->arraySeccionesFormato['child'][$codigo_seccion_padre]['total']+=$arrayResultChild['total'];
                    }
                }
            }

            // SI LA SECCION TIENE YA UN VALOR, ENTONCES QUE NO SE RECALCULE CUANDO SE LLAME DE NUEVO LA FUNCION
            foreach ($this->arraySeccionesFormato['child'] as $codigo_seccion => $arrayResult){
                if ($arrayResult['total']<>0) {
                    $this->arraySeccionesFormato['child'][$codigo_seccion]['calcula'] = 'false';
                }
            }
        }

        /**
         * @method calculaFormulaSecciones
         * @param  [arr] $arrayFilas filas del informe con los valores de cuentas
         */
        public function calculaFormulaSecciones()
        {
            // IDENTIFICAR LOS CONCEPTOS CON FORMULA
            foreach ($this->arraySeccionesFormato['child'] as $codigo_seccion => $arrayResult) {
                if ($arrayResult['formula_totalizado']<>''){
                    // echo "<script>console.log('-----------------------------------------------');</script>";
                    // echo "<script>console.log('$codigo_seccion = $arrayResult[formula_totalizado]');</script>";
                    // RECORRER DE NUEVO LAS FILAS PARA REEMPLAZAR LOS VALORES
                    foreach ($this->arrayFilasFormato as $id_fila_replace => $arrayFila) {
                        $this->arraySeccionesFormato['child'][$codigo_seccion]['formula_totalizado']=str_replace("[".$arrayFila['codigo']."]", $arrayFila['total'], $this->arraySeccionesFormato['child'][$codigo_seccion]['formula_totalizado']);
                    }
                    // SECCIONES DE LA FORMULA
                    foreach ($this->arraySeccionesFormato['child'] as $id_seccion_replace => $arraySeccion) {
                        // echo "<script>console.log('$id_seccion_replace = $arraySeccion[total] $arraySeccion[formula_totalizado]');</script>";
                        // echo $arraySeccion['codigo'].' - '.$arraySeccion['nombre']." : ".$arraySeccion['total'].'<br>';
                        $this->arraySeccionesFormato['child'][$codigo_seccion]['formula_totalizado']=str_replace("{".$arraySeccion['codigo_seccion']."}", $arraySeccion['total'], $this->arraySeccionesFormato['child'][$codigo_seccion]['formula_totalizado']);
                    }
                }
            }

            // EJECUTAR LA FORMULA PARA HALLAR EL VALOR DE LOS CONCEPTOS
            foreach ($this->arraySeccionesFormato['child'] as $codigo_seccion => $arrayResult) {
                if ($arrayResult['formula_totalizado']<>''){
                    $formula=$this->arraySeccionesFormato['child'][$codigo_seccion]['formula_totalizado'];
                    $this->arraySeccionesFormato['child'][$codigo_seccion]['total'] = $this->calculaFormula($formula);
                }
            }
        }

        //FUNCION PARA CALCULAR LA FORMULA DEL CONCEPTO
        public function calculaFormula($equation){
            if ($equation==''){ return round(0,$_SESSION['DECIMALESMONEDA']); }

            // Remove whitespaces
            $equation = preg_replace('/\s+/', '', $equation);
            // echo "$equation\n=";
            // echo 'alert("'.$equation.'"=)';

            $number    = '((?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?|pi|π)'; // What is a number
            $functions = '(?:sinh?|cosh?|tanh?|acosh?|asinh?|atanh?|exp|log(10)?|deg2rad|rad2deg|sqrt|pow|abs|intval|ceil|floor|round|(mt_)?rand|gmp_fact)'; // Allowed PHP functions
            $operators = '[\/*\^\+-,]'; // Allowed math operators
            $regexp    = '/^([+-]?('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns

            if (preg_match($regexp, $equation)){
                $equation = preg_replace('!pi|π!', 'pi()', $equation); // Replace pi with pi function
                eval('$result = '.$equation.';');
            }
            else{ $result = false; }

            return round($result,$_SESSION['DECIMALESMONEDA']);
            // return $result;
        }

        public function createTreeView($codigo_seccion_padre){
            $body        = "";
            $label_totalizado = "";
            // echo $this->arraySeccionesFormato['parent'][$codigo_seccion_padre];
            // SI EXISTE LA SECCION PADRE
            if (isset($this->arraySeccionesFormato['parent'][$codigo_seccion_padre]) ) {
                // $body .= "<div class='tree'>";
                foreach ($this->arraySeccionesFormato['parent'][$codigo_seccion_padre] as $codigo_seccion) {
                    $label_totalizado = "";

                    if(!isset($this->arraySeccionesFormato['child'][$codigo_seccion])) {
                        // $body .= "<div >".$this->arraySeccionesFormato['child'][$codigo_seccion]['nombre']."</div>";
                        $total_cuentas = ($this->arraySeccionesFormato['child'][$codigo_seccion]['total_cuentas']<>0)? "<td>".number_format($this->arraySeccionesFormato['child'][$codigo_seccion]['total_cuentas'],2,$this->separador_decimales,$this->separador_miles)."</td>" : "" ;
                        $body .= "<tr><td style='padding-left:".$this->arraySeccionesFormato['child'][$codigo_seccion]['padding'].";' >".$this->arraySeccionesFormato['child'][$codigo_seccion]['nombre']."</td>$total_cuentas</tr>";
                    }

                    if(isset($this->arraySeccionesFormato['child'][$codigo_seccion])) {

                        $padding_fila =$this->arraySeccionesFormato['child'][$codigo_seccion]['padding']+10;
                        // $body .= "<div ><b>".$this->arraySeccionesFormato['child'][$codigo_seccion]['nombre']."</b></div>".$this->setFilasSecciones($codigo_seccion);
                        $total_cuentas = ($this->arraySeccionesFormato['child'][$codigo_seccion]['total_cuentas']<>0)? "<td>".number_format($this->arraySeccionesFormato['child'][$codigo_seccion]['total_cuentas'],2,$this->separador_decimales,$this->separador_miles)."</td>" : "" ;
                        $body .= "<tr><td style='padding-left:".$this->arraySeccionesFormato['child'][$codigo_seccion]['padding'].";' ><b>".$this->arraySeccionesFormato['child'][$codigo_seccion]['nombre']."</b></td>$total_cuentas</tr>".$this->setFilasSecciones($codigo_seccion,$padding_fila);
                        $body .= $this->createTreeView($codigo_seccion, $padding);
                        if ($this->arraySeccionesFormato['child'][$codigo_seccion]['totalizado']=='true' && $codigo_seccion_padre<>$codigo_seccion ) {
                            $label_totalizado = ($this->arraySeccionesFormato['child'][$codigo_seccion]['label_totalizado']<>'')? $this->arraySeccionesFormato['child'][$codigo_seccion]['label_totalizado'] : 'Total '.$this->arraySeccionesFormato['child'][$codigo_seccion]['nombre'] ;
                            // $body .= "<div style='width:calc(100% - 230px) !important;' ><b>$label_totalizado</b></div>
                             //           <div style='width: 200px !important;' ><b>".number_format($this->arraySeccionesFormato['child'][$codigo_seccion]['total'],2,$this->separador_decimales,$this->separador_miles)."</b></div>";
                            $body .= "<tr><td style='padding-left:".$this->arraySeccionesFormato['child'][$codigo_seccion]['padding'].";'><b>$label_totalizado</b></td>
                                        <td><b>".number_format($this->arraySeccionesFormato['child'][$codigo_seccion]['total'],2,$this->separador_decimales,$this->separador_miles)." </b></td></tr>";
                        }
                    }
                }
                // $body .= "</div>";
            }
            // echo $body;
            return $body;
        }

        /**
        * @method setFilasSecciones
        * @param int id de la seccion a listar la fila
        */
        public function setFilasSecciones($id_seccion,$padding){
            $filas = "";

            foreach ($this->arrayFilasFormato as $id_fila => $arrayResult) {
                if ($arrayResult['id_seccion']<>$id_seccion) { continue; }
                 // $filas .= "<div style='margin-left: 30px; !important;width:calc(100% - 245px) !important;'>$arrayResult[nombre]</div>
                            // <div style='width: 200px !important;'>".number_format($arrayResult['total'],2,$this->separador_decimales,$this->separador_miles)."</div>";
                // $padding +=5;
                $filas .= "<tr><td style='padding-left:$padding;'>$arrayResult[nombre]</td><td>".number_format($arrayResult['total'],2,$this->separador_decimales,$this->separador_miles)."</td></tr>";
            }
            // foreach ($this->arrayFilasFormato[$id_seccion] as $id_fila => $arrayResult) {
            //     $filas .= "<div style='margin-left: 30px; !important;width:calc(100% - 245px) !important;'>$arrayResult[nombre]</div>
            //                 <div style='width: 200px !important;'>".number_format($this->arraySaldoFila[$arrayResult['id']],2,$this->separador_decimales,$this->separador_miles)."</div>";
            //     // $this->arraySeccionesFormato['child'][$id_seccion]['total']+=$this->arraySaldoFila[$arrayResult['id']];
            // }

            return $filas;
        }

        /**
        * @method createFormat Crear el formato solicitado por el usuario
        */
        public function createFormat()
        {
            $this->setFormatoInfo();
            $this->setFilasFormato();
            $this->setSeccionesFormato();
            $this->setCuentasFilasFormato();
            $this->setCuentasSeccionesFormato();
            $this->setAsientos();
            $this->setValorSecciones();
            $this->calculaFormulaFilas();
            // print_r($this->arrayFilasFormato);
            $this->setValorSecciones();
            $this->calculaFormulaSecciones();


            // print_r( $this->arraySeccionesFormato['child'][5]);
            // print_r($this->arraySeccionesFormato);
        }

        public function getHtmlPdf($IMPRIME_PDF)
        {
            $this->createFormat();
            $bodyResul = $this->createTreeView(0);
            // print_r($this->arraySeccionesFormato['child']);

            $stylePdf =($IMPRIME_PDF == 'true')?
                         "
                        .table-form{
                            font-family : arial,sans-serif;
                            margin-top  : 20px;
                            font-size   : 12px;
                            float       : left;
                            margin-left : 10px;
                        }

                        .table-form .thead{
                            background-color : #2A80B9;
                            color            : #fff;
                        }

                        .table-form .thead td{
                            padding   : 5px;
                            font-size : 14px;
                        }

                        .table-form td{
                            padding: 2px 2px 2px 15px;
                        }

                        .table-form input, .table-form textarea, .table-form select{
                            line-height      : 1.42857143;
                            color            : #555;
                            background-color : #fff;
                            border           : 1px solid #ccc;
                            height           : 30px;
                            width            : 200px;
                            padding-left     : 5px;
                        }

                        .table-form textarea{
                            height: 50px;
                        }

                        " : "" ;

            $formato = "<style>

                            .tree{
                                margin-left: 10px;
                            }

                            .tree > div{
                                margin-left : 15px;
                                padding     : 2px 0px 2px 0px;
                                width       : 100%;
                                float       : left;
                            }

                            .total_seccion{
                                margin-left: 0px !important;
                            }
                            .filas{
                                float:left;
                            }

                            $stylePdf

                        </style>
                        <div class='content' >
                        <table align='center' style='text-align:center;'>
                            <tr>
                                <td class='titulo_informe_empresa' style='text-align:center;'><b>$_SESSION[NOMBREEMPRESA]</b></td>
                            </tr>
                            <tr>
                                <td style='font-size:13px;text-align:center;'><b>NIT</b> $_SESSION[NITEMPRESA]</td>
                                ".$this->arrayFormatoInfo['title']."
                        </table>
                            <table class='table-form' style='width:95%' >
                                <tbody>
                                    <tr class='thead' >
                                        <td colspan='2' style='color:#FFF;'>".$this->arrayFormatoInfo['codigo']." - ".utf8_encode($this->arrayFormatoInfo['nombre'])."</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!--<div class='table-form' style='padding-bottom: 20px;'>-->
                                <table class='table-form' style='width:95%;' >
                                    $bodyResul
                                </table>
                             <!-- </div>-->
                        </div>";
            // echo utf8_decode($formato);
            // exit;

            if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=2;$MI=5;$ML=10;}
            if($IMPRIME_PDF == 'true'){
                include("../../../../../misc/MPDF54/mpdf.php");
                $mpdf = new mPDF(
                            'utf-8',        // mode - default ''
                            'LETTER-L',          // format - A4, for example, default ''
                            12,             // font size - default 0
                            '',             // default font family
                            $MI,            // margin_left
                            $MD,            // margin right
                            $MS,            // margin top
                            $ML,            // margin bottom
                            10,             // margin header
                            10,             // margin footer
                            'P'    // L - landscape, P - portrait
                        );
                // $mpdf-> debug = true;
                // $mpdf->useSubstitutions = true;
                // $mpdf->simpleTables = true;
                // $mpdf->packTableData= true;
                $mpdf->SetAutoPageBreak(TRUE, 15);
                //$mpdf->SetTitle ( $documento );
                // $mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
                $mpdf->SetDisplayMode ( 'fullpage' );
                $mpdf->SetHeader("");
                $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

                $mpdf->WriteHTML(utf8_encode($formato));
                $mpdf->Output($this->arrayFormatoInfo['codigo']."_".utf8_encode($this->arrayFormatoInfo['nombre']).".pdf",'I');
            }
            else{ echo utf8_decode($formato); }
        }

        public function getExcel()
        {
            $this->createFormat();
            $bodyResul = $this->createTreeView(0);

            header('Content-type: application/vnd.ms-excel');
            header("Content-Disposition: attachment; filename=".$this->arrayFormatoInfo['codigo']."_".date("Y_m_d").".xls");
            header("Pragma: no-cache");
            header("Expires: 0");

            $formato = "<style>
                            .table-form{
                                font-family : arial,sans-serif;
                                margin-top  : 20px;
                                font-size   : 12px;
                                float       : left;
                                margin-left : 10px;
                            }

                            .thead{
                                background-color : #2A80B9;
                                color            : #fff;
                                padding          : 5px;
                                font-size        : 14px;
                            }

                            .table-form td{
                                padding: 2px 2px 2px 15px;
                            }

                        </style>
                        <div class='content' >
                        <table align='center' style='text-align:center;'>
                            <tr>
                                <td class='titulo_informe_empresa' style='text-align:center;'><b>$_SESSION[NOMBREEMPRESA]</b></td>
                            </tr>
                            <tr>
                                <td style='font-size:13px;text-align:center;'><b>NIT</b> $_SESSION[NITEMPRESA]</td>
                                ".$this->arrayFormatoInfo['title']."
                        </table>
                            <table class='table-form' style='width:calc(100% - 10px);' >
                                <tbody>
                                    <tr class='thead'>
                                        <td colspan='2'>".$this->arrayFormatoInfo['codigo']." - ".utf8_encode($this->arrayFormatoInfo['nombre'])." </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class='table-form' style='padding-bottom: 20px;'>
                                <table class='table-form' style='width:calc(100% - 10px);' >
                                    $bodyResul
                                </table>
                             </div>
                        </div>";
            echo utf8_decode($formato);
        }

    }

?>