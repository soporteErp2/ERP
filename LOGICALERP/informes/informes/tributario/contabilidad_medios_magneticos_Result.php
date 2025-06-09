<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    header('Content-Type: text/html; charset=UTF-8');


    $id_empresa = $_SESSION['EMPRESA'];
    $object = new mediosMagneticos($id_formato,$fecha,$id_empresa,$mysql);

    if($IMPRIME_XLS=='true'){
        // header('Content-Encoding: UTF-8');
        header('Content-type: application/vnd.ms-excel;');
        header("Content-Disposition: attachment; filename=Medios_magneticos_".date("Y-m-d").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $object->createFormat();

    }else{
        header('Content-Type: application/json; charset=utf-8');
        $object->debug();

    }


    /**
    * @class mediosMagneticos
    *
    */
    class mediosMagneticos
    {
        private $mysql                        = '';
        private $id_formato                   = '';
        private $fecha                        = '';
        private $id_empresa                   = '';
        private $arrayConceptosFormato        = '';
        private $arrayConceptosCuentasFormato = '';
        private $whereAsientos                = '';
        private $whereIdTerceros              = '';
        private $arrayColumnasFormato         = '';
        private $arrayTerceros                = '';
        private $arrayJoined                  = '';
        private $sqldebug                     = [];
        private $codigo_formato               = '';
        private $camposComoTexto              = [];
        private $headers                      = [];

        /**
        * @method construct
        * @param int id del formato
        * @param srt fecha respectiva del periodo
        * @param obj objeto de conexion mysql
        */
        function __construct($id_formato,$fecha,$id_empresa,$mysql)
        {
            $this->id_formato       = $id_formato;
            $this->fecha            = split('-', $fecha)[0];
            $this->id_empresa       = $id_empresa;
            $this->mysql            = $mysql;
            $this->camposComoTexto  = ['Número identificación del informado','Código dpto', 'Código mcp', 'País'];
            $this->headers = [
                'Concepto', 'Tipo de documento', 'Número identificación del informado', 'DV',
                'Primer apellido del informado', 'Segundo apellido del informado',
                'Primer nombre del informado', 'Otros nombres del informado',
                'Razón social informado', 'Dirección', 'Código dpto', 'Código mcp', 'País'
            ];

        }

        /**
        * @method setConceptosFormato conceptos del formato
        */
        private function setConceptosFormato()
        {
            $sql   = "SELECT id,concepto,descripcion FROM medios_magneticos_formatos_conceptos WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$this->id_formato";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id']] = array(
                                                'concepto'    => $row['concepto'],
                                                'descripcion' => $row['descripcion'],
                                                );
            }

            $this->arrayConceptosFormato = $arrayTemp;
        }

        /**
        * @method setConceptosCuentasFormato cuentas de los conceptos del formato
        */
        private function setConceptosCuentasFormato()
        {
            $sql   = "SELECT
                            id,
                            id_formato,
                            codigo_formato,
                            nombre_formato,
                            id_concepto,
                            concepto,
                            descripcion_concepto,
                            id_cuenta_inicial,
                            cuenta_inicial,
                            descripcion_cuenta_inicial,
                            id_cuenta_final,
                            cuenta_final,
                            descripcion_cuenta_final,
                            forma_calculo,
                            id_columna_formato,
                            nombre_columna_formato,
                            tope
                        FROM
                            medios_magneticos_formatos_conceptos_cuentas
                        WHERE
                            activo=1
                            AND id_empresa=$this->id_empresa
                            AND id_formato=$this->id_formato";

            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id']] = array(
                                                'id_formato'                 => $row['id_formato'],
                                                'codigo_formato'             => $row['codigo_formato'],
                                                'nombre_formato'             => $row['nombre_formato'],
                                                'id_concepto'                => $row['id_concepto'],
                                                'concepto'                   => $row['concepto'],
                                                'descripcion_concepto'       => $row['descripcion_concepto'],
                                                'id_cuenta_inicial'          => $row['id_cuenta_inicial'],
                                                'cuenta_inicial'             => $row['cuenta_inicial'],
                                                'descripcion_cuenta_inicial' => $row['descripcion_cuenta_inicial'],
                                                'id_cuenta_final'            => $row['id_cuenta_final'],
                                                'cuenta_final'               => $row['cuenta_final'],
                                                'descripcion_cuenta_final'   => $row['descripcion_cuenta_final'],
                                                'forma_calculo'              => $row['forma_calculo'],
                                                'id_columna_formato'         => $row['id_columna_formato'],
                                                'nombre_columna_formato'     => $row['nombre_columna_formato'],
                                                'tope'                       => $row['tope'],
                                                );

                $whereTemp.=($whereTemp=='')? "CAST(codigo_cuenta AS CHAR) >='$row[cuenta_inicial]' AND CAST(codigo_cuenta AS CHAR) <= '$row[cuenta_final]' " : " OR CAST(codigo_cuenta AS CHAR) >='$row[cuenta_inicial]' AND CAST(codigo_cuenta AS CHAR) <= '$row[cuenta_final]'" ;
                $codigoFormato = $row['codigo_formato'];
            }
            $this->codigo_formato = $codigoFormato;
            $this->arrayConceptosCuentasFormato = $arrayTemp;
            $this->whereAsientos = " AND ($whereTemp)";
        }

        /**
        * @method setColumnasFormato columnas del formato
        */
        private function setColumnasFormato()
        {
            $sql   = "SELECT id,nombre FROM medios_magneticos_formatos_columnas WHERE activo=1 AND id_empresa=$this->id_empresa AND id_formato=$this->id_formato ORDER BY orden ASC";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id']] = $row['nombre'];

            }
            $this->arrayColumnasFormato = $arrayTemp;
        }

        private function setAsientos()
        {
            $arrayTemp = [];
        
            // Primera consulta (debe, haber por año actual)
            $sql1 = "SELECT
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
                        AND fecha BETWEEN '$this->fecha-01-01' AND '$this->fecha-12-31'
                        $this->whereAsientos
                    GROUP BY id_tercero, codigo_cuenta";

            $this->sqldebug[] = $sql1;
            $query1 = $this->mysql->query($sql1, $this->mysql->link);
            while ($row = $this->mysql->fetch_array($query1)) {
                $this->procesarFilaAsiento($row, $arrayTemp, false);
            }
        
            // Segunda consulta (saldo anterior, fecha menor al año actual)
            $sql2 = "SELECT
                        codigo_cuenta,
                        cuenta,
                        id_tercero,
                        nit_tercero,
                        tercero,
                        SUM(debe) as debito,
                        SUM(haber) as credito
                    FROM
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        AND fecha < '$this->fecha-01-01'
                        $this->whereAsientos
                    GROUP BY id_tercero, codigo_cuenta";
            $this->sqldebug[] = $sql2;

            $query2 = $this->mysql->query($sql2, $this->mysql->link);
            while ($row = $this->mysql->fetch_array($query2)) {
                $this->procesarFilaAsiento($row, $arrayTemp, true);
            }
        
            $this->whereIdTerceros = $this->construirWhereIdTerceros();
        
            $this->arrayAsientos = $arrayTemp;
        }

        /**
         * Procesa una fila del asiento y actualiza el array temporal y lista de terceros.
         * 
         * @param array $row Fila de resultados SQL
         * @param array &$arrayTemp Array temporal con la info de asientos
         * @param bool $esSaldoAnterior Indica si la fila corresponde a saldo anterior
         */
        private function procesarFilaAsiento($row, &$arrayTemp, $esSaldoAnterior = false)
        {
            $idTercero = $row['nit_tercero'];
            $codigoCuenta = $row['codigo_cuenta'];
        
            // Si la cuenta no existe aún, inicializarla
            if (!isset($arrayTemp[$idTercero][$codigoCuenta]['cuenta'])) {
                $arrayTemp[$idTercero][$codigoCuenta] = [
                    'cuenta'      => $row['cuenta'],
                    'id_tercero'  => $idTercero,
                    'nit_tercero' => $row['nit_tercero'],
                    'tercero'     => $row['tercero'],
                    'debito'      => $esSaldoAnterior ? 0 : $row['debito'],
                    'credito'     => $esSaldoAnterior ? 0 : $row['credito'],
                ];
            } else {
                // Si no es saldo anterior
                if (!$esSaldoAnterior) {
                    $arrayTemp[$idTercero][$codigoCuenta]['debito'] = $row['debito'];
                    $arrayTemp[$idTercero][$codigoCuenta]['credito'] = $row['credito'];
                }
            }
        
            // Si es saldo anterior, agregamos el saldo al array
            if ($esSaldoAnterior) {
                $arrayTemp[$idTercero][$codigoCuenta]['saldo_anterior'] = $row['debito'] - $row['credito'];
            }
        
            // Asignar conceptos segun cuentas
            $this->asignarConceptos($arrayTemp, $row);
        }

        /**
         * Asigna conceptos al array temporal según el rango de cuentas definido
         * 
         * @param array &$arrayTemp
         * @param array $row
         */
        private function asignarConceptos(&$arrayTemp, $row)
        {
            $idTercero = $row['nit_tercero'];
            $codigoCuenta = $row['codigo_cuenta'];
        
            foreach ($this->arrayConceptosCuentasFormato as $id_row => $arrayResul) {
                if ($codigoCuenta >= $arrayResul['cuenta_inicial'] && $codigoCuenta <= $arrayResul['cuenta_final']) {
                    $arrayTemp[$idTercero][$codigoCuenta]['concepto'][$id_row] = $arrayResul;
                }
            }
        }

        private function construirWhereIdTerceros()
        {
            // Construye la subconsulta para filtrar terceros con movimientos en asientos_colgaap
            $subconsulta = "
                SELECT DISTINCT nit_tercero
                FROM asientos_colgaap
                WHERE activo = 1
                  AND id_empresa = $this->id_empresa
                  AND fecha < '$this->fecha-12-31'
                  $this->whereAsientos
            ";
        
            return " AND T.numero_identificacion IN ($subconsulta)";
        }

        /**
        * @method setTerceros Crear el formato solicitado por el usuario
        */
        private function setTerceros()
        {
            $sqlTerceros="SELECT
                        T.id,
                        T.numero_identificacion,
                        T.dv,
                        T.apellido1,
                        T.apellido2,
                        T.nombre1,
                        T.nombre2,
                        T.nombre AS razon_social,
                        T.direccion,
                        T.id_pais,
                        T.id_departamento,
                        T.id_ciudad,
                        TD.codigo_tipo_documento_dian
                    FROM
                        terceros AS T
                        LEFT JOIN tipo_documento AS TD ON T.id_tipo_identificacion = TD.id
                    WHERE
                        /*activo=1  */
                        T.tercero = 1 AND
                        T.id_empresa=$this->id_empresa
                        $this->whereIdTerceros";
            $this->sqldebug[] = $sqlTerceros;
            $query=$this->mysql->query($sqlTerceros,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['numero_identificacion']] = array(
                                                'tipo_identificacion'   => $row['codigo_tipo_documento_dian'],
                                                'numero_identificacion' => $row['numero_identificacion'],
                                                'dv'                    => $row['dv'],
                                                'apellido1'             => $row['apellido1'],
                                                'apellido2'             => $row['apellido2'],
                                                'nombre1'               => $row['nombre1'],
                                                'nombre2'               => $row['nombre2'],
                                                'razon_social'          => $row['razon_social'],
                                                'direccion'             => $row['direccion'],
                                                'id_pais'               => $row['id_pais'],
                                                'id_departamento'       => $row['id_departamento'],
                                                'id_ciudad'             => $row['id_ciudad'],
                                                );

                $whereIdPais         .= ($whereIdPais=='')? "id='".$row['id_pais']."'" : " OR id='".$row['id_pais']."'" ;
                $whereIdDepartamento .= ($whereIdDepartamento=='')? "id='".$row['id_departamento']."'" : " OR id='".$row['id_departamento']."'" ;
                $whereIdCiudad       .= ($whereIdCiudad=='')? "id='".$row['id_ciudad']."'" : " OR id='".$row['id_ciudad']."'" ;
            }

            $arrayTemp[222222222]  = array('tipo_identificacion'=>43,'numero_identificacion' => '222222222','razon_social' => 'CUANTIAS MENORES' );
            $arrayTemp[444444000]  = array('tipo_identificacion'=>43,'numero_identificacion' => '444444000','razon_social' => 'OPERACIONES DEL EXTERIOR' );

            $this->arrayTerceros = $arrayTemp;
            $this->setCodigosDane($whereIdPais,$whereIdDepartamento,$whereIdCiudad);
        }

        /**
        * @method setCodigosDane establecer codigos del Dane segun ubicacion del tercero
        */
        private function setCodigosDane($whereIdPais,$whereIdDepartamento,$whereIdCiudad)
        {

            $sql="SELECT
                        id,
                        codigo
                    FROM
                        ubicacion_pais
                    WHERE
                        activo=1
                        AND ($whereIdPais)
                        ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayCodPais[$row['id']] = str_pad($row['codigo'], 3, '0', STR_PAD_LEFT);
            }

            $sql="SELECT
                        id,
                        codigo_departamento
                    FROM
                        ubicacion_departamento
                    WHERE
                        activo=1
                        AND ($whereIdDepartamento)
                        ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayCodDepartamento[$row['id']] = str_pad($row['codigo_departamento'], 2, '0', STR_PAD_LEFT);
                
            }

            $sql="SELECT
                        id,
                        codigo_ciudad,
                        id_departamento
                    FROM
                        ubicacion_ciudad
                    WHERE
                        activo=1
                        AND ($whereIdCiudad)
                        ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayCodCiudad[$row['id']] = $arrayCodDepartamento[$row['id_departamento']].str_pad($row['codigo_ciudad'], 3, '0', STR_PAD_LEFT);
            }

            $this->joinArrayTercero($arrayCodPais,$arrayCodDepartamento,$arrayCodCiudad);

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
            // 1. Acumular por concepto-tercero-columna
            $tmpConceptos = []; // acumulador

            foreach ($this->arrayAsientos as $id_tercero => $arrayAsientosResul) {
                foreach ($arrayAsientosResul as $codigo_cuenta => $arrayResul) {
                    $saldo_actual = abs($arrayResul['saldo_anterior'] + $arrayResul['debito'] - $arrayResul['credito']);
                
                    foreach ($arrayResul['concepto'] as $default_id => $arrayResulC) {
                        $codigo_cuenta = str_pad($codigo_cuenta, 8, '0', STR_PAD_RIGHT);
                        $cuenta_ini    = str_pad($arrayResulC['cuenta_inicial'], 8, '0', STR_PAD_RIGHT);
                        $cuenta_fin    = str_pad($arrayResulC['cuenta_final'], 8, '0', STR_PAD_RIGHT);
                    
                        if ($codigo_cuenta < $cuenta_ini || $codigo_cuenta > $cuenta_fin) {
                            continue;
                        }
                    
                        switch ($arrayResulC['forma_calculo']) {
                            case 'suma_debitos':
                                $saldo = $arrayResul['debito'];
                                break;
                            case 'suma_creditos':
                                $saldo = $arrayResul['credito'];
                                break;
                            case 'debito_menos_credito':
                                $saldo = $arrayResul['debito'] - $arrayResul['credito'];
                                break;
                            case 'credito_menos_debito':
                                $saldo = $arrayResul['credito'] - $arrayResul['debito'];
                                break;
                            case 'saldo_actual':
                                $saldo = $saldo_actual;
                                break;
                            case 'saldo_inicial':
                                $saldo = $arrayResul['saldo_anterior'];
                                break;
                            default:
                                $saldo = 0;
                        }
                    
                        $id_concepto = $arrayResulC['id_concepto'];
                        $id_columna = $arrayResulC['id_columna_formato'];
                        $tope = $arrayResulC['tope'];
                    
                        $tmpConceptos[$id_concepto][$id_tercero][$id_columna]['saldo'] += $saldo;
                        $tmpConceptos[$id_concepto][$id_tercero][$id_columna]['tope'] = $tope;
                    }
                }
            }

            // 2. Clasificar por cuantías menores o normales
            $arrayTemp = [];

            foreach ($tmpConceptos as $id_concepto => $terceros) {
                foreach ($terceros as $id_tercero => $columnas) {
                    foreach ($columnas as $id_columna => $datos) {
                        $saldo = $datos['saldo'];
                        $tope = $datos['tope'];
                    
                        if (abs($saldo) < $tope) {
                            $key = ($this->arrayTerceros[$id_tercero]['id_pais'] == 49) ? 222222222 : 444444000;
                            $arrayTemp[$id_concepto][$key][$id_columna] += $saldo;
                        } else {
                            $arrayTemp[$id_concepto][$id_tercero][$id_columna] += $saldo;
                        }
                    }
                }
            }

            $this->arrayJoined = $arrayTemp;
        }


        /**
        * @method createFormat Crear el formato solicitado por el usuario
        */
        public function createFormat() {
            include_once('../../../../misc/excel/Classes/PHPExcel.php');
        
            $this->setConceptosFormato();
            $this->setConceptosCuentasFormato();
            $this->setColumnasFormato();
            $this->setAsientos();
            $this->setTerceros();
            $this->joinArrayConceptosCuentas();
        
            $objPHPExcel = new PHPExcel();
            $sheet = $objPHPExcel->setActiveSheetIndex(0);
        
            // Títulos
            $this->headers = array_merge($this->headers, $this->arrayColumnasFormato);
        
            $col = 0;
            foreach ($this->headers as $header) {
                $headerUtf8 = mb_convert_encoding($header, 'UTF-8', 'auto');
                $sheet->setCellValueExplicitByColumnAndRow($col++, 1, $headerUtf8, PHPExcel_Cell_DataType::TYPE_STRING);
            }
        
            // Cuerpo
            $row = 2;
            foreach ($this->arrayConceptosFormato as $id_concepto => $arrayResulCon) {
                foreach ($this->arrayJoined[$id_concepto] as $id_tercero => $arrayResulJoin) {
                
                    $dataRow = [
                        $arrayResulCon['concepto'],
                        $this->arrayTerceros[$id_tercero]['tipo_identificacion'],
                        $this->arrayTerceros[$id_tercero]['numero_identificacion'],
                        $this->arrayTerceros[$id_tercero]['dv'],
                        $this->arrayTerceros[$id_tercero]['apellido1'],
                        $this->arrayTerceros[$id_tercero]['apellido2'],
                        $this->arrayTerceros[$id_tercero]['nombre1'],
                        $this->arrayTerceros[$id_tercero]['nombre2'],
                        $this->arrayTerceros[$id_tercero]['razon_social'],
                        $this->arrayTerceros[$id_tercero]['direccion'],
                        $this->arrayTerceros[$id_tercero]['codigo_departamento'],
                        $this->arrayTerceros[$id_tercero]['codigo_ciudad'],
                        $this->arrayTerceros[$id_tercero]['codigo_pais']
                    ];
                
                    foreach ($this->arrayColumnasFormato as $id_columna => $columna) {
                        $saldo_col = ($arrayResulJoin[$id_columna] > 0) ? $arrayResulJoin[$id_columna] : 0;
                        $dataRow[] = $saldo_col;
                    }
                
                    $col = 0;
                    foreach ($dataRow as $key => $value) {
                        if (in_array($this->headers[$key], $this->camposComoTexto)) {
                            $sheet->setCellValueExplicitByColumnAndRow($col++, $row, $value, PHPExcel_Cell_DataType::TYPE_STRING);
                        } else {
                            $sheet->setCellValueByColumnAndRow($col++, $row, $value);
                        }
                    }
                    $row++;
                }
            }
        
            // Descargar el archivo
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="formato '. $this->codigo_formato . " " . date("Y-m-d") . '.xlsx"');
            header('Cache-Control: max-age=0');
        
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }


        public function debug()
        {
            $tiempos = [];
        
            $inicio = microtime(true);
            $this->setConceptosFormato();
            $tiempos['setConceptosFormato'] = microtime(true) - $inicio;
        
            $inicio = microtime(true);
            $this->setConceptosCuentasFormato();
            $tiempos['setConceptosCuentasFormato'] = microtime(true) - $inicio;
        
            $inicio = microtime(true);
            $this->setColumnasFormato();
            $tiempos['setColumnasFormato'] = microtime(true) - $inicio;
        
            $inicio = microtime(true);
            $this->setAsientos();
            $tiempos['setAsientos'] = microtime(true) - $inicio;
        
            $inicio = microtime(true);
            $this->setTerceros();
            $tiempos['setTerceros'] = microtime(true) - $inicio;
        
            $inicio = microtime(true);
            $this->joinArrayConceptosCuentas();
            $tiempos['joinArrayConceptosCuentas'] = microtime(true) - $inicio;
        
        
            echo json_encode(array(
                                "tiempos" => $tiempos,
                                "consultas" => array(
                                    "sql1" => $this->sqldebug1,
                                    "sql2" => $this->sqldebug2
                                ),
                                "arrayAsientos"=>$this->utf8ize($this->arrayAsientos['15367'])
                            ));

            //echo json_encode($this->utf8ize($this->arrayAsientos['15367']));

            //$csv = '';
            //$array = $this->arrayTerceros;
            //if (!empty($array)) {
            //    // Encabezados
            //    $csv .= implode(',', array_keys($array[0])) . "\n";
            //
            //    // Filas
            //    foreach ($array as $fila) {
            //        // Escapar comillas dobles y encerrar cada campo en comillas
            //        $escaped = array_map(function($valor) {
            //            $valor = str_replace('"', '""', $valor);
            //            return '"' . $valor . '"';
            //        }, $fila);
            //    
            //        $csv .= implode(',', $escaped) . "\n";
            //    }
            //}
            //
            //echo $csv;

        }

        public function utf8ize($mixed) {
            if (is_array($mixed)) {
                $result = array();
                foreach ($mixed as $key => $value) {
                    $key = is_string($key) && !mb_detect_encoding($key, 'UTF-8', true) ? utf8_encode($key) : $key;
                    $result[$key] = $this->utf8ize($value); // llamada con $this
                }
                return $result;
            } elseif (is_string($mixed)) {
                return !mb_detect_encoding($mixed, 'UTF-8', true) ? utf8_encode($mixed) : $mixed;
            }
            return $mixed;
        }
    }

?>