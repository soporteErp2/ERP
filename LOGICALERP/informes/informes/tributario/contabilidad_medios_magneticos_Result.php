<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    header('Content-Type: text/html; charset=UTF-8');

    if($IMPRIME_XLS=='true'){
        // header('Content-Encoding: UTF-8');
        header('Content-type: application/vnd.ms-excel;');
        header("Content-Disposition: attachment; filename=Medios_magneticos_".date("Y-m-d").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    }



    $id_empresa = $_SESSION['EMPRESA'];
    $object = new mediosMagneticos($id_formato,$fecha,$id_empresa,$mysql);
    $object->createFormat();


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

        /**
        * @method construct
        * @param int id del formato
        * @param srt fecha respectiva del periodo
        * @param obj objeto de conexion mysql
        */
        function __construct($id_formato,$fecha,$id_empresa,$mysql)
        {
            $this->id_formato = $id_formato;
            $this->fecha      = split('-', $fecha)[0];
            $this->id_empresa = $id_empresa;
            $this->mysql      = $mysql;
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

            }

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

        /**
        * @method setAsientos consultar los asientos contables
        */
        private function setAsientos()
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
                        asientos_colgaap
                    WHERE
                        activo=1
                        AND id_empresa=$this->id_empresa
                        -- AND tipo_documento<>'NCC'
                        AND fecha BETWEEN '$this->fecha-01-01' AND '$this->fecha-12-31'
                        AND nit_tercero = '90009733'
                        $this->whereAsientos
                        GROUP BY id_tercero,codigo_cuenta";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id_tercero']][$row['codigo_cuenta']] = array(
                                                                                'cuenta'      => $row['cuenta'],
                                                                                'id_tercero'  => $row['id_tercero'],
                                                                                'nit_tercero' => $row['nit_tercero'],
                                                                                'tercero'     => $row['tercero'],
                                                                                'debito'      => $row['debito'],
                                                                                'credito'     => $row['credito'],
                                                                            );
                $whereTemp .=($whereTemp=='')? 'id='.$row['id_tercero'] : ' OR id='.$row['id_tercero'] ;

                // RECORRER LA CONFIGURACION DE CUENTAS CONCEPTOS PARA ASIGNARLO AL ARRAY
                foreach ($this->arrayConceptosCuentasFormato as $id_row => $arrayResul){

                    $search_ini = strpos($row['codigo_cuenta'],$arrayResul['cuenta_inicial']);
                    $search_end = strpos($row['codigo_cuenta'],$arrayResul['cuenta_final']);

                    // echo '<br>cuenta :<b>'.$row['codigo_cuenta'].'</b>';
                    // echo '<br>cuenta_inicial :'.$arrayResul['cuenta_inicial'].' - cuenta_final '.$arrayResul['cuenta_final']." - search_ini ".var_dump($search_ini)." - search_end ".var_dump($search_end);

                    // if ($search_ini===0 || $search_end===0) {
                    //     $arrayTemp[$row['id_tercero']][$row['codigo_cuenta']]['concepto'][$id_row] = $this->arrayConceptosCuentasFormato[$id_row];
                    // }

                    if ($row['codigo_cuenta']>=$arrayResul['cuenta_inicial'] && $row['codigo_cuenta']<=$arrayResul['cuenta_final']) {
                        $arrayTemp[$row['id_tercero']][$row['codigo_cuenta']]['concepto'][$id_row] = $this->arrayConceptosCuentasFormato[$id_row];
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
                        AND fecha < '$this->fecha-01-01'
                        -- AND tipo_documento<>'NCC'
                        $this->whereAsientos
                        GROUP BY id_tercero,codigo_cuenta";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id_tercero']][$row['codigo_cuenta']]['saldo_anterior'] = $row['saldo'];
            }

            $this->whereIdTerceros = " AND ($whereTemp)";
            $this->arrayAsientos   = $arrayTemp;
            // print_r($this->arrayAsientos);
        }

        /**
        * @method setTerceros Crear el formato solicitado por el usuario
        */
        private function setTerceros()
        {
            $sql="SELECT
                        id,
                        tipo_identificacion,
                        numero_identificacion,
                        dv,
                        apellido1,
                        apellido2,
                        nombre1,
                        nombre2,
                        nombre AS razon_social,
                        direccion,
                        id_pais,
                        id_departamento,
                        id_ciudad
                    FROM
                        terceros
                    WHERE
                        /*activo=1  */
                        tercero = 1 AND
                        id_empresa=$this->id_empresa
                        $this->whereIdTerceros";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id']] = array(
                                                'tipo_identificacion'   => $row['tipo_identificacion'],
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
            $arrayTemp[444444000]  = array('numero_identificacion' => '444444000','razon_social' => 'OPERACIONES DEL EXTERIOR' );

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
                $arrayCodPais[$row['id']] = $row['codigo'];
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
                $arrayCodDepartamento[$row['id']] = $row['codigo_departamento'];
            }

            $sql="SELECT
                        id,
                        codigo_ciudad
                    FROM
                        ubicacion_ciudad
                    WHERE
                        activo=1
                        AND ($whereIdCiudad)
                        ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayCodCiudad[$row['id']] = $row['codigo_ciudad'];
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
            // echo print_r($this->arrayAsientos);
            // echo html_entity_decode(json_encode($this->arrayAsientos));
            // var_dump(json_encode($this->arrayAsientos));
            foreach ($this->arrayAsientos as $id_tercero => $arrayAsientosResul) {
                foreach ($arrayAsientosResul as $codigo_cuenta => $arrayResul){
                    $saldo_actual = abs( (($arrayResul['saldo_anterior']+$arrayResul['debito'])-$arrayResul['credito']) );

                    foreach ($arrayResul['concepto'] as $default_id => $arrayResulC) {

                        if ($arrayResulC['forma_calculo'] == 'suma_debitos') {
                            $saldo = $arrayResul['debito'];
                        }
                        else if ($arrayResulC['forma_calculo'] == 'suma_creditos') {
                            $saldo = $arrayResul['credito'];
                        }
                        else if ($arrayResulC['forma_calculo'] == 'debito_menos_credito') {
                            $saldo = $arrayResul['debito']-$arrayResul['credito'];
                        }
                        else if ($arrayResulC['forma_calculo'] == 'credito_menos_debito') {
                            $saldo = $arrayResul['credito']-$arrayResul['debito'];
                        }
                        else if ($arrayResulC['forma_calculo'] == 'saldo_actual') {
                            $saldo = $saldo_actual;
                        }
                        else if ($arrayResulC['forma_calculo'] == 'saldo_inicial') {
                            $saldo = $arrayResul['saldo_anterior'];
                        }

                    // echo "$codigo_cuenta = $arrayResul[debito] - $arrayResul[credito] = $saldo <br>";

                        // echo $this->arrayTerceros[$arrayResul['id_tercero']]['numero_identificacion'].' - '.$this->arrayTerceros[$arrayResul['id_tercero']]['razon_social'].' saldo: '.$saldo_actual.'<br>';
                        // if ( isset($arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']]) ) {
                        //     $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['saldo_anterior'] += $arrayResul['saldo_anterior'];
                        //     $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['debito']         += $arrayResul['debito'];
                        //     $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['credito']        += $arrayResul['credito'];
                        //     $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['saldo_actual']   += $saldo_actual;
                        //     $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['saldo']          += $saldo;
                        // }
                        // else{
                        //     $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']] = array(
                        //                                                                                                                                 'saldo_anterior'         => $arrayResul['saldo_anterior'],
                        //                                                                                                                                 'debito'                 => $arrayResul['debito'],
                        //                                                                                                                                 'credito'                => $arrayResul['credito'],
                        //                                                                                                                                 'forma_calculo'          => $arrayResulC['forma_calculo'],
                        //                                                                                                                                 'saldo_actual'           => $saldo_actual,
                        //                                                                                                                                 'saldo'                  => $saldo,
                        //                                                                                                                                 'id_columna_formato'     => $arrayResulC['id_columna_formato'],
                        //                                                                                                                                 'nombre_columna_formato' => $arrayResulC['nombre_columna_formato'],
                        //                                                                                                                                 );
                        // }

                        // 222222222
                        // CUANTIAS MENORES

                        // 444444000
                        // OPERACIONES DEL EXTERIOR

                        if (abs($saldo)<$arrayResulC['tope']) {
                            // echo '->'.$this->arrayTerceros[$arrayResul['id_tercero']]['numero_identificacion'].' - '.$this->arrayTerceros[$arrayResul['id_tercero']]['razon_social'].' saldo: '.$saldo_actual.'<br>';

                            if ($this->arrayTerceros[$arrayResul['id_tercero']]['id_pais']==49) {
                                $arrayTemp[$arrayResulC['id_concepto']][222222222][$arrayResulC['id_columna_formato']]+=$saldo;
                            }
                            else{
                               $arrayTemp[$arrayResulC['id_concepto']][444444000][$arrayResulC['id_columna_formato']]+=$saldo;
                               echo '<script>console.log("'.$arrayResul['id_tercero'].' - '.$saldo.' cuantias menores ");</script>';
                            }

                        }
                        else{
                            // echo '-]'.$this->arrayTerceros[$arrayResul['id_tercero']]['numero_identificacion'].' - '.$this->arrayTerceros[$arrayResul['id_tercero']]['razon_social'].' saldo: '.$saldo_actual.'<br>';
                            $arrayTemp[$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]+=$saldo;
                        }

                    }
                }
            }

            $this->arrayJoined = $arrayTemp;
        }

        /**
        * @method createFormat Crear el formato solicitado por el usuario
        */
        public function createFormat()
        {
            $this->setConceptosFormato();
            $this->setConceptosCuentasFormato();
            $this->setColumnasFormato();
            $this->setAsientos();
            $this->setTerceros();
            $this->joinArrayConceptosCuentas();

            // foreach ($this->arrayAsientos as $id_tercero => $arrayAsientosResul) {
            //     foreach ($arrayAsientosResul as $codigo_cuenta => $arrayResul) {

            //         $saldo_actual = (($arrayResul['saldo_anterior']+$arrayResul['debito'])-$arrayResul['credito']);

            //         foreach ($arrayResul['concepto'] as $default_id => $arrayResulC) {
            //             // $bodyConfig.='
            //             //                 <tr>
            //             //                     <td style="background-color:#c0ddf0;color:#444;font-weight:bold;">Codigo Formato</td>
            //             //                     <td>'.$arrayResulC['codigo_formato'].'</td>
            //             //                     <td style="background-color:#c0ddf0;color:#444;font-weight:bold;">Formato</td>
            //             //                     <td>'.$arrayResulC['nombre_formato'].'</td>
            //             //                     <td style="background-color:#c0ddf0;color:#444;font-weight:bold;">Concepto</td>
            //             //                     <td>'.$arrayResulC['concepto'].'</td>
            //             //                     <td style="background-color:#c0ddf0;color:#444;font-weight:bold;">Descripcion</td>
            //             //                     <td>'.$arrayResulC['descripcion_concepto'].'</td>
            //             //                     <td style="background-color:#c0ddf0;color:#444;font-weight:bold;">forma_calculo</td>
            //             //                     <td>'.$arrayResulC['forma_calculo'].'</td>

            //             //                 </tr>
            //             //             ';

            //             if ($arrayResulC['forma_calculo']=='suma_debitos') {
            //                 $saldo = $arrayResul['debito'];
            //             }
            //             else if ($arrayResulC['forma_calculo']=='suma_creditos') {
            //                 $saldo = $arrayResul['credito'];
            //             }
            //             else if ($arrayResulC['forma_calculo']=='debito_menos_credito') {
            //                 $saldo = $arrayResul['debito']-$arrayResul['credito'];
            //             }
            //             else if ($arrayResulC['forma_calculo']=='saldo_actual') {
            //                 $saldo = $saldo_actual;
            //             }
            //             else if ($arrayResulC['forma_calculo']=='saldo_inicial') {
            //                 $saldo = $arrayResul['saldo_anterior'];
            //             }

            //             if ( isset($arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']]) ) {
            //                 $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['saldo_anterior'] += $arrayResul['saldo_anterior'];
            //                 $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['debito']         += $arrayResul['debito'];
            //                 $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['credito']        += $arrayResul['credito'];
            //                 $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['saldo_actual']   += $saldo_actual;
            //                 $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]['saldo']          += $saldo;
            //             }
            //             else{
            //                 $arrayJoined[$default_id][$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']] = array(
            //                                                                                                                                             'saldo_anterior'         => $arrayResul['saldo_anterior'],
            //                                                                                                                                             'debito'                 => $arrayResul['debito'],
            //                                                                                                                                             'credito'                => $arrayResul['credito'],
            //                                                                                                                                             'forma_calculo'          => $arrayResulC['forma_calculo'],
            //                                                                                                                                             'saldo_actual'           => $saldo_actual,
            //                                                                                                                                             'saldo'                  => $saldo,
            //                                                                                                                                             'id_columna_formato'     => $arrayResulC['id_columna_formato'],
            //                                                                                                                                             'nombre_columna_formato' => $arrayResulC['nombre_columna_formato'],
            //                                                                                                                                             );
            //             }

            //             $arrayJoinedTerceros[$arrayResulC['id_concepto']][$arrayResulC['id_tercero']] = $arrayResulC['id_tercero'];
            //             $arrayJoinedResul[$arrayResulC['id_concepto']][$arrayResul['id_tercero']][$arrayResulC['id_columna_formato']]+=$saldo;

            //         }

            //         // $body.='<tr style="background-color:#2A80B9;color:#FFF;font-weight:bold;">
            //         //             <td>'.$codigo_cuenta.'</td>
            //         //             <td>'.$arrayResul['cuenta'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['tipo_identificacion'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['numero_identificacion'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['dv'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['apellido1'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['apellido2'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['nombre1'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['nombre2'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['razon_social'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['direccion'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['codigo_pais'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['codigo_departamento'].'</td>
            //         //             <td>'.$this->arrayTerceros[$arrayResul['id_tercero']]['codigo_ciudad'].'</td>
            //         //             <!-- <td>'.$arrayResul['nit_tercero'].'</td>
            //         //             <td>'.$arrayResul['tercero'].'</td> -->
            //         //             <td>'.$arrayResul['saldo_anterior'].'</td>
            //         //             <td>'.$arrayResul['debito'].'</td>
            //         //             <td>'.$arrayResul['credito'].'</td>
            //         //             <td>'.$saldo_actual.'</td>
            //         //         </tr>
            //         //         '.$bodyConfig;
            //         // $bodyConfig='';
            //     }
            // }

            // // print_r($arrayJoined);
            // foreach ($arrayJoined as $id_row => $arrayJoined1){
            //     foreach ($arrayJoined1 as $id_concepto => $arrayJoined2) {
            //         foreach ($arrayJoined2 as $id_tercero => $arrayJoined3) {
            //             foreach ($arrayJoined3 as $id_columna_formato => $arrayResul) {
            //                 $body.='<tr style="/*background-color:#2A80B9;*/color:#999;font-weight:bold;">
            //                             <td>'.$this->arrayConceptosFormato[$id_concepto]['concepto'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['tipo_identificacion'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['numero_identificacion'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['dv'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['apellido1'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['apellido2'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['nombre1'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['nombre2'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['razon_social'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['direccion'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['codigo_pais'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['codigo_departamento'].'</td>
            //                             <td>'.$this->arrayTerceros[$id_tercero]['codigo_ciudad'].'</td>
            //                             <td>'.$arrayResul['saldo'].'</td>
            //                             <td>'.$arrayResul['saldo_anterior'].'</td>
            //                             <td>'.$arrayResul['debito'].'</td>
            //                             <td>'.$arrayResul['credito'].'</td>
            //                             <td>'.$arrayResul['saldo_actual'].'</td>
            //                             <td>'.$arrayResul['forma_calculo'].'</td>
            //                         </tr>
            //                         ';


            //                 // foreach ($this->arrayColumnasFormato as $id_col => $arrayResul) {
            //                 //     $body.= '';
            //                 // }

            //                 // $body.= '';

            //             }
            //         }
            //     }
            // }
            // print_r($this->arrayJoined);
            $bodyResul.='<tr style="background-color:#008080;color:#FFF;font-weight:bold;">
                            <td>Concepto</td>
                            <td>Tipo de documento</td>
                            <td>Número identificación del informado</td>
                            <td>DV</td>
                            <td>Primer apellido del informado</td>
                            <td>Segundo apellido del informado</td>
                            <td>Primer nombre del informado</td>
                            <td>Otros nombres del informado</td>
                            <td>Razón social informado</td>
                            <td>Dirección</td>
                            <td>Código dpto</td>
                            <td>Código mcp</td>
                            <td>País de Residencia o domicilio</td>
                            <!--<td>SALDO</td>-->
                        ';

            foreach ($this->arrayColumnasFormato as $id_columna => $columna) {
                $bodyResul.='<td >'.$columna.'</td>';
            }

            $bodyResul.='</tr>';

            foreach ($this->arrayConceptosFormato as $id_concepto => $arrayResulCon) {
                // $bodyResul.='<tr><td>'.$arrayResulCon['concepto'].'</td>';
                foreach ($this->arrayJoined[$id_concepto] as $id_tercero => $arrayResulJoin) {
                    $bodyResul.='<tr>
                                    <td>'.$arrayResulCon['concepto'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['tipo_identificacion'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['numero_identificacion'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['dv'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['apellido1'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['apellido2'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['nombre1'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['nombre2'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['razon_social'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['direccion'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['codigo_departamento'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['codigo_ciudad'].'</td>
                                    <td>'.$this->arrayTerceros[$id_tercero]['codigo_pais'].'</td>
                                    <!--<td>'.$saldo.'</td>-->

                                ';
                    foreach ($this->arrayColumnasFormato as $id_columna => $columna) {
                        $saldo_col = ($arrayResulJoin[$id_columna]>0)? $arrayResulJoin[$id_columna] : 0 ;
                        $bodyResul.='<td >'.$saldo_col.'</td>';
                    }

                }



                $bodyResul.='</tr>';
            }


            $formato="
                <style>

                   .tabla td {solid;font-size:12px;padding:5px;}

                    table {border-collapse: collapse;}

                </style>
                <table class='tabla' border=0>
                $bodyResul
                </table>
                ";

            echo utf8_decode($formato);

        }
    }

?>