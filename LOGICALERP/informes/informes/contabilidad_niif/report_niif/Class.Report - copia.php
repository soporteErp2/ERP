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
        // public $id_empresa               = '';
        // public $arrayFilasFormato        = '';
        // public $arraySaldoFila           = '';
        // public $arrayCuentasFilasFormato = '';
        // public $whereAsientos            = '';
        // public $whereIdTerceros          = '';
        // public $arraySeccionesFormato    = '';
        // public $arrayFormatoInfo         = '';
        // public $arrayAsientos            = '';

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
        function __construct($fecha_inicio,$fecha_final,$arrayCentroCostosJSON,$id_empresa,$mysql)
        {
            $this->fecha_inicio        = $fecha_inicio;
            $this->fecha_final         = $fecha_final;
            $this->id_empresa          = $id_empresa;
            $this->ccosFiltro          = json_decode($arrayCentroCostosJSON);
            $this->mysql               = $mysql;
        }

        /**
         * @method setFormatoInfo consultar la informacion del formato
         * @param [int] $id_formato id del formato a consultar
         */
        public function setFormatoInfo($id_formato){
            $sql   = "SELECT
                        codigo,
                        nombre,
                        filtro_terceros,
                        filtro_ccos,
                        filtro_corte_anual,
                        filtro_corte_mensual,
                        filtro_rango_fechas,
                        filtro_cuentas
                    FROM informes_niif_formatos WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            $arrayFormatoInfo['codigo']               = $this->mysql->result($query,0,'codigo');
            $arrayFormatoInfo['nombre']               = $this->mysql->result($query,0,'nombre');

            $filtro_terceros      = $this->mysql->result($query,0,'filtro_terceros');
            $filtro_ccos          = $this->mysql->result($query,0,'filtro_ccos');
            $filtro_corte_anual   = $this->mysql->result($query,0,'filtro_corte_anual');
            $filtro_corte_mensual = $this->mysql->result($query,0,'filtro_corte_mensual');
            $filtro_rango_fechas  = $this->mysql->result($query,0,'filtro_rango_fechas');
            $filtro_cuentas       = $this->mysql->result($query,0,'filtro_cuentas');

            $arrayFechaInicio = split('-', $this->fecha_inicio);
            $arrayFechaFinal  = split('-', $this->fecha_final);
            // OPCIONES DE FILTROS
            if ($filtro_terceros == 'Si') {
                $arrayFormatoInfo['title']              .= "";
                $arrayFormatoInfo['whereSaldoAnterior'] .= "";
                $arrayFormatoInfo['whereSaldo']         .= "";
            }
            if ($filtro_ccos == 'Si') {
                $arrayFormatoInfo['title']              .= "";
                $arrayFormatoInfo['whereSaldoAnterior'] .= "";
                $arrayFormatoInfo['whereSaldo']         .= "";
            }
            if ($filtro_corte_anual == 'Si') {
                $arrayFormatoInfo['title']              .= "</tr><td style='font-size:11px;text-align:center;'> $arrayFechaFinal[0]-01-01 al $this->fecha_final</td></tr>";
                $arrayFormatoInfo['whereSaldoAnterior'] .= " AND fecha < '$arrayFechaFinal[0]-01-01' ";
                $arrayFormatoInfo['whereSaldo']         .= " AND fecha BETWEEN '$arrayFechaFinal[0]-01-01' AND '$this->fecha_final' ";
            }
            if ($filtro_corte_mensual == 'Si') {
                $arrayFormatoInfo['title']              .= "</tr><td style='font-size:11px;text-align:center;'> $arrayFechaFinal[0]-$arrayFechaFinal[1]-01 al $this->fecha_final</td></tr>";
                $arrayFormatoInfo['whereSaldoAnterior'] .= " AND fecha < '$arrayFechaFinal[0]-$arrayFechaFinal[1]-01' ";
                $arrayFormatoInfo['whereSaldo']         .= " AND fecha BETWEEN '$arrayFechaFinal[0]-$arrayFechaFinal[1]-01' AND '$this->fecha_final' ";
            }
            if ($filtro_rango_fechas == 'Si') {
                $arrayFormatoInfo['title']              .= "</tr><td style='font-size:11px;text-align:center;'> $this->fecha_inicio al $this->fecha_final</td></tr>";
                $arrayFormatoInfo['whereSaldoAnterior'] .= " AND fecha < '$this->fecha_inicio' ";
                $arrayFormatoInfo['whereSaldo']         .= " AND fecha BETWEEN '$this->fecha_inicio' AND '$this->fecha_final' ";
            }
            if ($filtro_cuentas == 'Si') {
                $arrayFormatoInfo['title']              .= "";
                $arrayFormatoInfo['whereSaldoAnterior'] .= "";
                $arrayFormatoInfo['whereSaldo']         .= "";
            }

            return $arrayFormatoInfo;

        }

        /**
         * @method setSeccionesFormato consultar la informacion de las secciones del formato
         * @param [int] $id_formato id del formato a consultar
         */
        public function setSeccionesFormato($id_formato,$setFormatosRequeridos=false)
        {
            $sql   = "SELECT
                        codigo_seccion,
                        codigo_seccion_padre,
                        orden,
                        nombre,
                        tipo,
                        descripcion_tipo,
                        totalizado,
                        label_total,
                        formula_total
                         FROM informes_niif_formatos_secciones WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$id_formato ORDER BY orden ASC";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $codigo_seccion       = $row['codigo_seccion'];
                $codigo_seccion_padre = $row['codigo_seccion_padre'];
                $arraySeccionesFormato['child'][$codigo_seccion] = array(
                                                                        'id'                   =>$row['id'],
                                                                        'codigo_seccion'       =>$codigo_seccion,
                                                                        'id_formato'           =>$row['id_formato'],
                                                                        'orden'                =>$row['orden'],
                                                                        'nombre'               =>$row['nombre'],
                                                                        'tipo'                 =>$row['tipo'],
                                                                        'descripcion_tipo'     =>$row['descripcion_tipo'],
                                                                        'totalizado'           =>$row['totalizado'],
                                                                        'label_total'          =>$row['label_total'],
                                                                        'formula_total'        =>$row['formula_total'],
                                                                        'codigo_seccion_padre' =>$row['codigo_seccion_padre'],
                                                                        );

                $arraySeccionesFormato['parent'][$codigo_seccion_padre][] = $codigo_seccion;

                // SI LA FORMULA DE LA SECCION SE CALCULA CON OTROS INFORMES
                $findFormat = strpos($row['formula_total'], '>');
                // VERIFICAR SI ES DE UNA FILA O SECCION
                $signoVariable = strpos($row['formula_total'], '[');

                if ($findFormat !== false) {
                    $codigosFormatos = explode('>', $row['formula_total']);
                    $codigosFormatos[0] = substr($codigosFormatos[0],1,-1);
                    $codigosFormatos[1] = substr($codigosFormatos[1],0,-1);

                    // echo $codigosFormatos[0].' - '.$codigosFormatos[1].' => '.substr($codigosFormatos[0],1,-1).'  '.substr($codigosFormatos[1],0,-1)."<br>";

                    $tipoVariable = ($signoVariable!=='[')? "fila" : "seccion" ;
                    if ($setFormatosRequeridos==true){
                        $this->formatosRequeridos[$codigosFormatos[0]][$tipoVariable][$codigosFormatos[1]] = $row['formula_total'];
                    }
                }
            }
            // $this->arraySeccionesFormato = $arraySeccionesFormato;
            return $arraySeccionesFormato;
        }

        /**
        * @method setFilasFormato conceptos del formato
        * @param [int] $id_formato id del formato a consultar
        */
        public function setFilasFormato($id_formato,$setFormatosRequeridos=false)
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
                    FROM informes_niif_formatos_secciones_filas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayFilasFormato[$row['id']] = array(
                                                        'id'         => $row['id'],
                                                        'id_seccion' => $row['id_seccion'],
                                                        'codigo'      => $row['codigo'],
                                                        'orden'      => $row['orden'],
                                                        'nombre'     => $row['nombre'],
                                                        'naturaleza' => $row['naturaleza'],
                                                        'formula'    => $row['formula'],
                                                        );

                // SI LA FORMULA DE LA SECCION SE CALCULA CON OTROS INFORMES
                $findFormat = strpos($row['formula'], '>');
                // VERIFICAR SI ES DE UNA FILA O SECCION
                $signoVariable = strpos($row['formula'], '[');

                if ($findFormat !== false) {
                    $codigosFormatos = explode('>', $row['formula']);
                    $codigosFormatos[0] = substr($codigosFormatos[0],1,10);
                    $codigosFormatos[1] = substr($codigosFormatos[1],0,-1);
                    $tipoVariable = ($signoVariable!=='[')? "fila" : "seccion" ;
                    if ($setFormatosRequeridos==true){
                        $this->formatosRequeridos[$codigosFormatos[0]][$tipoVariable][$codigosFormatos[1]] = $row['formula'];
                    }
                }

            }


            // $this->arrayFilasFormato = $arrayTemp;
            return $arrayFilasFormato;
        }

        /**
        * @method setCuentasFilasFormato cuentas de los conceptos del formato
        * @param [int] $id_formato id del formato a consultar
        */
        public function setCuentasFilasFormato($id_formato)
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
                            AND id_formato=$id_formato";

            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayCuentasFilasFormato[$row['id']] = array(
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

            // $this->arrayCuentasFilasFormato = $arrayTemp;
            // $this->whereAsientos            = " AND ($whereTemp)";
            $arrayCuentasFilasFormato['whereAsientos'] = " AND ($whereTemp)";
            return $arrayCuentasFilasFormato;
        }

       /**
        * @method setAsientos consultar los asientos contables
        * @param [str] $whereAsientos            Condicion de las cuentas para la consulta de los asientos
        * @param [str] $whereSaldoAnterior       Condicion para la consulta de los asientos en saldo anterior
        * @param [str] $whereSaldo               Condicion para la consulta de los asientos en saldo corriente
        * @param [arr] $arrayCuentasFilasFormato array con las filas y sus cuentas
        */
        public function setAsientos($whereAsientos,$whereSaldoAnterior,$whereSaldo,$arrayCuentasFilasFormato,$arrayFilasFormato)
        {
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
                        $whereAsientos
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $whereSaldo
                        GROUP BY codigo_cuenta";
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

                // RECORRER LA CONFIGURACION DE CUENTAS CONCEPTOS PARA ASIGNARLO AL ARRAY
                foreach ($arrayCuentasFilasFormato as $id_row => $arrayResul){

                    $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                    $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                    if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']){
                        // $arrayAsientos[$row['id_tercero']][$row['codigo_cuenta']]['concepto'][$id_row] = $this->arrayConceptosCuentasFormato[$id_row];
                        $arrayAsientos[$row['codigo_cuenta']]['fila'][$arrayResul['id_fila']] = $arrayCuentasFilasFormato[$id_row];
                        $arrayAsientos[$row['codigo_cuenta']]['fila']['seccion'] = $arrayCuentasFilasFormato[$id_row]['id_seccion'];
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
                        $whereAsientos
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $whereSaldoAnterior
                        GROUP BY id_tercero,codigo_cuenta";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayAsientos[$row['codigo_cuenta']]['saldo_anterior'] = $row['saldo'];
            }
            // $this->whereIdTerceros = " AND ($whereTemp)";
            // ASIGNAR EL TOTAL POR FILA
            foreach ($arrayAsientos as $codigo_cuenta => $arrayResul){

                $saldo_actual = abs( (($arrayResul['saldo_anterior']+$arrayResul['debito'])-$arrayResul['credito']) );

                foreach ($arrayResul['fila'] as $id_fila => $arrayResulF) {

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

                    // echo $id_fila." => ".$saldo." - ".$arrayResulF['forma_calculo']." D: ".$arrayResul['debito']." C: ".$arrayResul['credito']." S.A: ".$arrayResul['saldo_anterior']."<br>";
                    $arrayFilasFormato[$id_fila]['total'] += $saldo;
                    // $this->arraySaldoFila[$id_fila] += $saldo;
                }
            }

            return $arrayFilasFormato;
            // ASIGNAR EL TOTAL DE LA SECCION CON LOS VALORES DE LA FILA
            // foreach ($this->arrayFilasFormato[$id_seccion] as $key => $arrayResult){
            //     $this->arraySeccionesFormato['child'][$codigo_seccion]['total']+=$this->arraySaldoFila[$arrayResult['id']];
            // }
        }

        /**
         * @method calculaFormula
         * @param  [arr] $arrayFilas filas del informe con los valores de cuentas
         */
        public function calculaFormulaFilas($id_formato,$arrayFilasFormato)
        {
            // IDENTIFICAR LOS CONCEPTOS CON FORMULA
            foreach ($arrayFilasFormato as $id_fila => $arrayResult) {
                if ($arrayResult['formula']<>''){
                        // echo $arrayResult['formula']."<br>";
                    // RECORRER DE NUEVO LAS FILAS PARA REEMPLAZAR LOS VALORES
                    foreach ($arrayFilasFormato as $id_fila_replace => $arrayFila) {
                        // echo $arrayFila['codigo']." - ".$arrayFila['total']."<br>";
                        // echo $arrayFilasFormato[$id_fila]['formula']."<br>";
                        $arrayFilasFormato[$id_fila]['formula']=str_replace("[".$arrayFila['codigo']."]", $arrayFila['total'], $arrayFilasFormato[$id_fila]['formula']);
                    }
                }
            }

            // EJECUTAR LA FORMULA PARA HALLAR EL VALOR DE LOS CONCEPTOS
            foreach ($arrayFilasFormato as $id_fila => $arrayResult) {
                if ($arrayResult['formula']<>''){
                    // echo $arrayFilasFormato[$id_fila]['formula']."<br>";

                    $formula=$arrayFilasFormato[$id_fila]['formula'];
                    // eval('$total='.$formula.';');
                    $arrayFilasFormato[$id_fila]['total'] = $total;
                    // echo "Formula= $formula => total = $total<br>";
                    // echo  eval($arrayFilasFormato[$id_fila]['formula']).'- <br>';
                }
            }

            // print_r($arrayFilasFormato);

            return $arrayFilasFormato;
        }

        /**
         * @method setValorSecciones establecer el totalizado de secciones a partir de las filas de la misma
         * @param [arr] $arrayFilasFormato filas de las secciones
         * @param [arr] $arraySeccionesFormato secciones del formato
         */
        public function setValorSecciones($arrayFilasFormato,$arraySeccionesFormato)
        {
            foreach ($arrayFilasFormato as $id_fila => $arrayResult) {
                foreach ($arraySeccionesFormato['child'] as $codigo_seccion => $arrayResultSecciones) {
                    if ($arrayResult['id_seccion']==$codigo_seccion) {
                        $arraySeccionesFormato['child'][$codigo_seccion]['total'] += $arrayResult['total'];
                    }
                }
            }
            return $arraySeccionesFormato;
        }

    }

?>