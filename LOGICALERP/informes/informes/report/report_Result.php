<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    header('Content-Type: text/html; charset=UTF-8');

    if (!isset($arrayCentroCostosJSON) && $arrayCentroCostosJSON=='[]') { $arrayCentroCostosJSON=''; }


    $id_empresa = $_SESSION['EMPRESA'];
    $object = new ErpReport($id_formato,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$separador_miles,$separador_decimales,$arrayCentroCostosJSON,$id_empresa,$IMPRIME_XLS,$mysql);
    $object->createFormat();


    /**
    * @class ErpReport
    *
    */
    class ErpReport
    {
        private $mysql                       = '';
        private $id_formato                  = '';
        private $fechaInicio                 = '';
        private $fechaFinal                  = '';
        private $separador_miles             = '';
        private $separador_decimales         = '';
        private $id_empresa                  = '';
        private $ccosFiltro                  = '';
        private $xlsPrint                    = '';
        private $arrayfilasFormato           = '';
        private $arrayFilasCuentasFormato    = '';
        private $whereAsientos               = '';
        private $whereIdTerceros             = '';
        private $arrayColumnasFormato        = '';
        private $arrayTerceros               = '';
        private $arrayJoined                 = '';
        private $arrayMeses                  = '';
        private $arrayFilasCcosFormato       = '';
        private $arrayFilasDocumentosFormato = '';
        private $arrayFilasTercerosFormato   = '';


        /**
        * @method construct
        * @param int id del formato
        * @param srt fecha respectiva del periodo
        * @param obj objeto de conexion mysql
        */
        function __construct($id_formato,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$separador_miles,$separador_decimales,$arrayCentroCostosJSON,$id_empresa,$IMPRIME_XLS,$mysql)
        {
            $this->id_formato          = $id_formato;
            $this->fechaInicio         = $MyInformeFiltroFechaInicio;
            $this->fechaFinal          = $MyInformeFiltroFechaFinal;
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
         * setCentroCostosFilas centro de costos de una fila
         */
        public function setCentroCostosFilas()
        {
            $sql="SELECT
                        id_formato,
                        codigo_formato,
                        nombre_formato,
                        id_seccion,
                        seccion,
                        id_columna,
                        columna,
                        id_fila,
                        fila,
                        id_centro_costos,
                        codigo_centro_costos,
                        centro_costos
                    FROM informes_formatos_secciones_filas_centro_costos
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND id_formato=$this->id_formato ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            $arrayTemp = '';
            while ($row=$this->mysql->fetch_assoc($query)) {
                $id_seccion = $row['id_seccion'];
                $id_columna = $row['id_columna'];
                $id_fila    = $row['id_fila'];
                $cod_ccos   = $row['codigo_centro_costos'];
                $arrayTemp[$id_seccion][$id_columna][$id_fila][$cod_ccos] = $row;
            }

            $this->arrayFilasCcosFormato = $arrayTemp;
            // print_r($this->arrayFilasCcosFormato);
        }

        /**
         * setDocumentosFilas documentos especifico por fila
         */
        public function setDocumentosFilas()
        {
            $sql="SELECT
                        id_formato,
                        codigo_formato,
                        nombre_formato,
                        id_seccion,
                        seccion,
                        id_columna,
                        columna,
                        id_fila,
                        fila,
                        documento
                    FROM informes_formatos_secciones_filas_documentos
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND id_formato=$this->id_formato";
            $query=$this->mysql->query($sql,$this->mysql->link);
            $arrayTemp = '';
            while ($row=$this->mysql->fetch_assoc($query)) {
                $id_seccion = $row['id_seccion'];
                $id_columna = $row['id_columna'];
                $id_fila    = $row['id_fila'];
                $documento  = $row['documento'];
                $arrayTemp[$id_seccion][$id_columna][$id_fila][$documento] = $row;
            }
            $this->arrayFilasDocumentosFormato = $arrayTemp;
            // print_r($this->arrayFilasDocumentosFormato);
        }

        /**
         * setTercerosFilas terceros especifico por fila
         */
        public function setTercerosFilas()
        {
            $sql="SELECT
                        id_formato,
                        codigo_formato,
                        nombre_formato,
                        id_seccion,
                        seccion,
                        id_columna,
                        columna,
                        id_fila,
                        fila,
                        id_tercero,
                        documento_tercero,
                        tercero
                    FROM informes_formatos_secciones_filas_terceros
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND id_formato=$this->id_formato";
            $query=$this->mysql->query($sql,$this->mysql->link);
            $arrayTemp = '';
            while ($row=$this->mysql->fetch_assoc($query)) {
                $id_seccion = $row['id_seccion'];
                $id_columna = $row['id_columna'];
                $id_fila    = $row['id_fila'];
                $id_tercero = $row['id_tercero'];
                $arrayTemp[$id_seccion][$id_columna][$id_fila][$id_tercero] = $row;
            }
            $this->arrayFilasTercerosFormato = $arrayTemp;
            // print_r($this->arrayFilasTercerosFormato);
        }

        /**
        * @method setColumnasFormato columnas del formato
        */
        private function setColumnasFormato()
        {
            $sql   = "SELECT id,id_seccion,orden,nombre,titulo FROM informes_formatos_secciones_columnas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$this->id_formato ORDER BY orden ASC";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id_seccion']][$row['id']]  = array('nombre' =>  $row['nombre'],'titulo' =>  $row['titulo'], 'orden' => $row['orden'] );

            }
            $this->arrayColumnasFormato = $arrayTemp;
        }

        /**
        * @method setAsientos consultar los asientos contables
        */
        private function setAsientos()
        {

            if ($this->fechaFinal<>'') {
                $whereFechaCol1    = " AND fecha BETWEEN '$this->fechaInicio' AND '$this->fechaFinal' ";
                $whereFechaAntCol1 = " AND fecha<'$this->fechaInicio' ";

                $this->title ="<tr><td style='font-size:11px;text-align:center;'>$_SESSION[NOMBRESUCURSAL]<br>$this->fechaInicio a $this->fechaFinal </td></tr>";

                $fechaIni = date( 'Y-m-d' ,strtotime ( '-1 month' , strtotime ( $this->fechaInicio ) ) );
                $fechaFin  = date( 'Y-m-d' ,strtotime ( '-1 month' , strtotime ( $this->fechaFinal  ) ) );
                $whereFechaCol2    = " AND fecha BETWEEN '$fechaIni' AND '$fechaFin' ";
                $whereFechaAntCol2 = " AND fecha<'$fechaIni' ";

            }else{

                $fecha_inicial = split('-', $this->fechaInicio)[0].'-'.split('-', $this->fechaInicio)[1];

                $mes  = split('-', $this->fechaInicio)[1];
                $mes = ( ($mes-1)<=0 )? 12 : $mes-1;
                $anio = ( $mes==12 )? split('-', $this->fechaInicio)[0]-1 : split('-', $this->fechaInicio)[0];

                $fecha_final = $anio.'-'.$mes;

                $whereFechaCol1    = " AND fecha BETWEEN '$fecha_inicial-01' AND '$this->fechaInicio' ";
                $whereFechaAntCol1 = " AND fecha<'$fecha_inicial-01' ";
                $whereFechaCol2    = " AND fecha BETWEEN '$fecha_final-01' AND '$fecha_final-31'";
                $whereFechaAntCol2 = " AND fecha < '$fecha_final-01'";

                $this->title ="<tr><td style='font-size:11px;text-align:center;'>$_SESSION[NOMBRESUCURSAL]<br>$fecha_inicial-01 a $this->fechaInicio </td></tr>";
            }

            foreach ($this->ccosFiltro as $indice => $id_centro_costos) {
                $whereCcos .= ($whereCcos=='')? ' id_centro_costos='.$id_centro_costos : ' OR id_centro_costos='.$id_centro_costos;
            }
            $whereCcos = ($whereCcos<>'')? " AND ( $whereCcos )" : "" ;

            // $arrayFilasCuentasFormato[$id_seccion][$id_columna][$id_fila]

            // COLUMNA 1
              $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe) AS debito,
                        SUM(haber) AS credito,
                        codigo_centro_costos,
                        tipo_documento
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $whereFechaCol1
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta,codigo_centro_costos,tipo_documento;";

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

                                // VALIDAR EL TERCERO DE LA FILA
                                if (is_array($this->arrayFilasTercerosFormato[$id_seccion][$id_columna][$id_fila])) {
                                    // SI TIENE CONFIGURACION DE TERCEROS, VALIDAR QUE SEA EL QUE ESTA CONFIGURADO
                                    if (!is_array($this->arrayFilasTercerosFormato[$id_seccion][$id_columna][$id_fila][$row['id_tercero']])) {
                                        continue;
                                    }
                                }


                                $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                                $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                                if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                                    $this->arrayAsientos['MES 1'][$row['codigo_cuenta']]['debito'] += $row['debito'];
                                    $this->arrayAsientos['MES 1'][$row['codigo_cuenta']]['credito'] += $row['credito'];

                                    // $this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$cod_ccos];
                                    // $this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$documento];

                                    // SI LA FILA ES POR CENTRO DE COSTOS O FILTRA POR DOCUMENTO
                                    if (
                                            is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila]) ||
                                            is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila])
                                        )
                                    {
                                        // SI TIENE CENTRO DE COSTOS Y FILTRO DE DOCUMENTOS
                                        if( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                            // SI EL DOCUMENTO COINCIDE CON EL FILTRO
                                            if (is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$row['tipo_documento']] )) {
                                                // SI MUEVE CENTRO DE COSTOS
                                                if( is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                                    if ( is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$row['codigo_centro_costos']]) ){
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                                    }
                                                }
                                                // SI NO MUEVE CENTRO DE COSTOS ENTONCES COINCIDE EL DOCUMENTO, AGREGAR EL REGISTRO
                                                else{
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                                }
                                            }
                                        }
                                        else if(is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila])) {
                                            // SI EL DOCUMENTO COINCIDE CON EL FILTRO
                                            if (is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$row['codigo_centro_costos']] )) {
                                                // SI MUEVE CENTRO DE COSTOS
                                                if( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                                    if ( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$row['tipo_documento']]) ){
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                                    }
                                                }
                                                // SI NO MUEVE EL FILTRO DEL DOCUMENTO, AGREGAR EL REGISTRO
                                                else{
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                                }
                                            }
                                        }
                                    }
                                    else{
                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                    }

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
                        SUM(debe-haber) AS saldo,
                        codigo_centro_costos,
                        tipo_documento
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $whereFechaAntCol1
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta,codigo_centro_costos,tipo_documento;";
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

                                // VALIDAR EL TERCERO DE LA FILA
                                if (is_array($this->arrayFilasTercerosFormato[$id_seccion][$id_columna][$id_fila])) {
                                    // SI TIENE CONFIGURACION DE TERCEROS, VALIDAR QUE SEA EL QUE ESTA CONFIGURADO
                                    if (!is_array($this->arrayFilasTercerosFormato[$id_seccion][$id_columna][$id_fila][$row['id_tercero']])) {
                                        continue;
                                    }
                                }

                                $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                                $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                                if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                                    $this->arrayAsientos['MES 1'][$row['codigo_cuenta']]['saldo_anterior'] += $row['saldo'];

                                     // SI LA FILA ES POR CENTRO DE COSTOS O FILTRA POR DOCUMENTO
                                    if (
                                            is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila]) ||
                                            is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila])
                                        )
                                    {
                                        // SI TIENE CENTRO DE COSTOS Y FILTRO DE DOCUMENTOS
                                        if( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                            // SI EL DOCUMENTO COINCIDE CON EL FILTRO
                                            if (is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$row['tipo_documento']] )) {
                                                // SI MUEVE CENTRO DE COSTOS
                                                if( is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                                    if ( is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$row['codigo_centro_costos']]) ){
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                                    }
                                                }
                                                // SI NO MUEVE CENTRO DE COSTOS ENTONCES COINCIDE EL DOCUMENTO, AGREGAR EL REGISTRO
                                                else{
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                                }
                                            }
                                        }
                                        else if(is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila])) {
                                            // SI EL DOCUMENTO COINCIDE CON EL FILTRO
                                            if (is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$row['codigo_centro_costos']] )) {
                                                // SI MUEVE CENTRO DE COSTOS
                                                if( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                                    if ( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$row['tipo_documento']]) ){
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                                    }
                                                }
                                                // SI NO MUEVE EL FILTRO DEL DOCUMENTO, AGREGAR EL REGISTRO
                                                else{
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                                }
                                            }
                                        }
                                    }
                                    else{
                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                    }

                                }

                            }
                        }
                    }
                }

            }

            // print_r($this->arrayAsientos);
            // COLUMNA 2
            $sql="SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe) AS debito,
                        SUM(haber) AS credito,
                        codigo_centro_costos,
                        tipo_documento
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $whereFechaCol2
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta,codigo_centro_costos,tipo_documento;";
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
                        if ($this->arrayColumnasFormato[$id_seccion][$id_columna]['orden']<>2){ continue; }

                        foreach ($colResul as $id_fila => $filaResul){
                            foreach ($filaResul as $default_id => $arrayResul){
                                // echo 'Tercero: '.$this->arrayfilasFormato[$id_fila]['tercero_unico'].' id tercero:'.$this->arrayfilasFormato[$id_fila]['id_tercero'].' tercero asientos: '.$row['id_tercero'].'<br>';
                                // SI LA FILA TIENE UN FILTRO QUE SEA UNICO POR TERCERO, Y EL ASIENTO NO ES DE ESE TERCERO CONFIGURADO, ENTONCES SE VACIA ESE VALOR PARA QUE NO SUME
                                if ($this->arrayfilasFormato[$id_seccion][$id_fila]['tercero_unico']=='Si' && $this->arrayfilasFormato[$id_seccion][$id_fila]['id_tercero']<>$row['id_tercero'] ){
                                    continue;
                                    // $row['debito']  = 0;
                                    // $row['credito'] = 0;
                                }
                                // VALIDAR EL TERCERO DE LA FILA
                                if (is_array($this->arrayFilasTercerosFormato[$id_seccion][$id_columna][$id_fila])) {
                                    // SI TIENE CONFIGURACION DE TERCEROS, VALIDAR QUE SEA EL QUE ESTA CONFIGURADO
                                    if (!is_array($this->arrayFilasTercerosFormato[$id_seccion][$id_columna][$id_fila][$row['id_tercero']])) {
                                        continue;
                                    }
                                }
                                $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                                $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                                if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                                    $this->arrayAsientos['MES 2'][$row['codigo_cuenta']]['debito'] += $row['debito'];
                                    $this->arrayAsientos['MES 2'][$row['codigo_cuenta']]['credito'] += $row['credito'];

                                    // SI LA FILA ES POR CENTRO DE COSTOS O FILTRA POR DOCUMENTO
                                    if (
                                            is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila]) ||
                                            is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila])
                                        )
                                    {
                                        // SI TIENE CENTRO DE COSTOS Y FILTRO DE DOCUMENTOS
                                        if( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                            // SI EL DOCUMENTO COINCIDE CON EL FILTRO
                                            if (is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$row['tipo_documento']] )) {
                                                // SI MUEVE CENTRO DE COSTOS
                                                if( is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                                    if ( is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$row['codigo_centro_costos']]) ){
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                                    }
                                                }
                                                // SI NO MUEVE CENTRO DE COSTOS ENTONCES COINCIDE EL DOCUMENTO, AGREGAR EL REGISTRO
                                                else{
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                                }
                                            }
                                        }
                                        else if(is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila])) {
                                            // SI EL DOCUMENTO COINCIDE CON EL FILTRO
                                            if (is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$row['codigo_centro_costos']] )) {
                                                // SI MUEVE CENTRO DE COSTOS
                                                if( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                                    if ( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$row['tipo_documento']]) ){
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                                    }
                                                }
                                                // SI NO MUEVE EL FILTRO DEL DOCUMENTO, AGREGAR EL REGISTRO
                                                else{
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                                    $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                                }
                                            }
                                        }
                                    }
                                    else{
                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                        $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
                                    }


                                    // $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['debito'] += $row['debito'];
                                    // $this->arrayFilasCuentasFormato [$id_seccion][$id_columna][$id_fila][$default_id]['credito'] += $row['credito'];
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
                        SUM(debe-haber) AS saldo,
                        codigo_centro_costos,
                        tipo_documento
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND tipo_documento<>'NCC'
                        $whereFechaAntCol2
                        $this->whereAsientos
                        $whereCcos
                        GROUP BY id_tercero,codigo_cuenta,codigo_centro_costos,tipo_documento;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {

                // RECORRER LA CONFIGURACION DE CUENTAS FILAS PARA ASIGNARLO AL ARRAY
                foreach ($this->arrayFilasCuentasFormato as $id_seccion => $seccionResul){
                    foreach ($seccionResul as $id_columna => $colResul){
                        // echo $arrayColumnasFormato[$id_columna]['orden']."<br>";
                        if ($this->arrayColumnasFormato[$id_seccion][$id_columna]['orden']<>2){ continue; }
                        foreach ($colResul as $id_fila => $filaResul){
                            foreach ($filaResul as $default_id => $arrayResul){
                                // SI LA FILA TIENE UN FILTRO QUE SEA UNICO POR TERCERO, Y EL ASIENTO NO ES DE ESE TERCERO CONFIGURADO, ENTONCES SE VACIA ESE VALOR PARA QUE NO SUME
                                if ($this->arrayfilasFormato[$id_seccion][$id_fila]['tercero_unico']=='Si' && $this->arrayfilasFormato[$id_seccion][$id_fila]['id_tercero']<>$row['id_tercero'] ){
                                    continue;
                                    // $row['saldo']  = 0;
                                }
                                // VALIDAR EL TERCERO DE LA FILA
                                if (is_array($this->arrayFilasTercerosFormato[$id_seccion][$id_columna][$id_fila])) {
                                    // SI TIENE CONFIGURACION DE TERCEROS, VALIDAR QUE SEA EL QUE ESTA CONFIGURADO
                                    if (!is_array($this->arrayFilasTercerosFormato[$id_seccion][$id_columna][$id_fila][$row['id_tercero']])) {
                                        continue;
                                    }
                                }
                                $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                                $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                                if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                                    $this->arrayAsientos['MES 2'][$row['codigo_cuenta']]['saldo_anterior'] += $row['saldo'];
                                    // SI LA FILA ES POR CENTRO DE COSTOS O FILTRA POR DOCUMENTO
                                    if (
                                            is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila]) ||
                                            is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila])
                                        )
                                    {
                                        // SI TIENE CENTRO DE COSTOS Y FILTRO DE DOCUMENTOS
                                        if( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                            // SI EL DOCUMENTO COINCIDE CON EL FILTRO
                                            if (is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$row['tipo_documento']] )) {
                                                // SI MUEVE CENTRO DE COSTOS
                                                if( is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                                    if ( is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$row['codigo_centro_costos']]) ){
                                                        $this->arrayFilasCuentasFormato[$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                                    }
                                                }
                                                // SI NO MUEVE CENTRO DE COSTOS ENTONCES COINCIDE EL DOCUMENTO, AGREGAR EL REGISTRO
                                                else{
                                                    $this->arrayFilasCuentasFormato[$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                                }
                                            }
                                        }
                                        else if(is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila])) {
                                            // SI EL DOCUMENTO COINCIDE CON EL FILTRO
                                            if (is_array($this->arrayFilasCcosFormato[$id_seccion][$id_columna][$id_fila][$row['codigo_centro_costos']] )) {
                                                // SI MUEVE CENTRO DE COSTOS
                                                if( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila]) ) {
                                                    if ( is_array($this->arrayFilasDocumentosFormato[$id_seccion][$id_columna][$id_fila][$row['tipo_documento']]) ){
                                                        $this->arrayFilasCuentasFormato[$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                                    }
                                                }
                                                // SI NO MUEVE EL FILTRO DEL DOCUMENTO, AGREGAR EL REGISTRO
                                                else{
                                                    $this->arrayFilasCuentasFormato[$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                                }
                                            }
                                        }
                                    }
                                    else{
                                        $this->arrayFilasCuentasFormato[$id_seccion][$id_columna][$id_fila][$default_id]['saldo_anterior'] += $row['saldo'];
                                    }

                                }

                            }
                        }
                    }
                }

            }

            // print_r($this->arrayAsientos);
            // print_r($this->arrayFilasCuentasFormato);
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
            $this->setCentroCostosFilas();
            $this->setDocumentosFilas();
            $this->setTercerosFilas();
            $this->setAsientos();

            // print_r($this->arrayFilasCuentasFormato);

            // RECORRER LA SECCION
            foreach ($this->arraySeccionesFormato as $id_seccion => $seccionResult) {
                // RECORRER LAS COLUMNAS
                $seccionColumnas='';
                $col = 0;
                $mes1 = split('-', $this->fechaInicio)[1]*1;
                $mes2 = (($mes1-1) == 0)? 12: $mes1-1 ;
                $colspan=2;
                foreach ($this->arrayColumnasFormato[$id_seccion] as $id_columna => $columna) {
                    $colspan++;
                    // TITULOS DE LAS COLUMNAS DE LAS SECCIONES
                    switch ($col) {
                        case 0: $label_col = $this->arrayMeses[$mes1]; break;
                        case 1: $label_col = $this->arrayMeses[$mes2]; break;
                        case 2: $label_col = "Costos por ambiente"; break;
                        case 3: $label_col = "%"; break;
                    }
                    // $label_col =($col==0)? $this->arrayMeses[$mes1] : $this->arrayMeses[$mes2] ;
                    $seccionColumnas .="<td>$label_col </td>";
                    $col ++;
                    if ($col==2) {
                        $seccionColumnas .="<td>Diferencia</td>";
                    }

                    //SI HAY COLUMNA COSTOS POR AMBIENTE MOSTRAR COLUMNA %
                    if ($col==3) {
                        $seccionColumnas .="<td>%</td>";
                    }
                }


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
                        $arrayTotalesCol[$id_fila][$contSaldo] = $saldo;
                        // FILAS COLUMNA DIFERENCIA
                        if ($contSaldo==2) {
                            $seccionFilas .="<td>".number_format ( $acumFila ,2,$this->separador_decimales,$this->separador_miles )."</td>";
                        }

                        // SI TIENE LA COLUMNA COSTOS POR AMBIENTE, MOSTRAR LA COLUMNA %
                        if ($contSaldo==3) {
                            $colspan++;
                            $seccionFilas .="<td>".number_format ( (($arrayTotalesCol[$id_fila][3]/$arrayTotalesCol[$id_fila][1])*100) ,2,$this->separador_decimales,$this->separador_miles )."</td>";
                        }

                        if ($contSaldo>2) {
                           $seccionFilas .= "</tr>";
                        }
                    }

                    // $seccionFilas .="<td>".number_format ( $acumFila ,2,$this->separador_decimales,$this->separador_miles )."final</td>
                                    // </tr>";
                }
                // print_r($arrayTotalesCol);
                // print_r($this->arrayFilasCuentasFormato);
                $bodyResul .="<tr class='thead'>
                                <td colspan='$colspan' >$seccionResult[titulo]</td>
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

                        // TOTAL COLUMNA DIFERENCIA
                        if ($contSaldo==2) {
                            $bodyResul .= "<td>".number_format ( $acumFila ,2,$this->separador_decimales,$this->separador_miles)."</td>";
                        }
                        // SI TIENE LA COLUMNA COSTOS POR AMBIENTE AGREGAR LA COLUMNA DE %
                        // if ($contSaldo==3) {
                        //     $bodyResul .= "<td>".number_format ( '0' ,2,$this->separador_decimales,$this->separador_miles)."</td>";
                        // }
                        if ($contSaldo>2) {
                           $seccionFilas .= "</tr>";
                        }
                    }
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
                        $this->title
                    </table>
                    <table class='tableInforme' >
                        $bodyResul
                    </table>
                    ";

            echo utf8_decode($formato);
        }
    }

?>