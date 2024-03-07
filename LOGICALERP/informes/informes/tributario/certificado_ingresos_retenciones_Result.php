<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    header('Content-Type: text/html; charset=UTF-8');

    if($IMPRIME_XLS=='true'){
        // header('Content-Encoding: UTF-8');
        header('Content-type: application/vnd.ms-excel;');
        header("Content-Disposition: attachment; filename=CIRE_".date("Y-m-d").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    $id_empresa = $_SESSION['EMPRESA'];
    $object = new certificadoIngresoRetenciones($fecha_inicio,$fecha_final,$id_empleado,$IMPRIME_XLS,$id_empresa,$mysql);
    $object->createFormat();


    /**
    * @class certificadoIngresoRetenciones
    *
    */
    class certificadoIngresoRetenciones
    {
        private $fecha_inicio          = '';
        private $fecha_final           = '';
        private $id_empleado           = '';
        private $mysql                 = '';
        private $IMPRIME_XLS           = '';
        private $id_empresa            = '';
        private $arraySecciones        = '';
        private $arrayFilas            = '';
        private $arrayConceptosFormato = '';
        private $arrayInfoEmpleado     = '';
        private $arrayInfoEmpresa      = '';

        /**
        * @method construct
        * @param int id de la empresa
        * @param obj objeto de conexion mysql
        */
        function __construct($fecha_inicio,$fecha_final,$id_empleado,$IMPRIME_XLS,$id_empresa,$mysql)
        {
            $this->fecha_inicio   = $fecha_inicio;
            $this->fecha_final    = $fecha_final;
            $this->id_empleado     = $id_empleado;
            $this->id_empresa     = $id_empresa;
            $this->IMPRIME_XLS    = $IMPRIME_XLS;
            $this->mysql          = $mysql;
        }

        /**
        * @method getInfoEmpleado consultar los datos del empleado
        */
        public function getInfoEmpleado()
        {
            $sql="SELECT documento,nombre1,nombre2,apellido1,apellido2 FROM empleados WHERE activo=1 AND id_empresa=$this->id_empresa AND id=$this->id_empleado";
            $query=$this->mysql->query($sql,$this->mysql->link);

            $this->arrayInfoEmpleado['documento'] = $this->mysql->result($query, 0,'documento');
            $this->arrayInfoEmpleado['nombre1']   = $this->mysql->result($query, 0,'nombre1');
            $this->arrayInfoEmpleado['nombre2']   = $this->mysql->result($query, 0,'nombre2');
            $this->arrayInfoEmpleado['apellido1'] = $this->mysql->result($query, 0,'apellido1');
            $this->arrayInfoEmpleado['apellido2'] = $this->mysql->result($query, 0,'apellido2');
        }

        /**
        * @method getInfoEmpresa consultar la informacion de la empresa
        */
        public function getInfoEmpresa()
        {
            $sql="SELECT documento,digito_verificacion,razon_social FROM empresas WHERE activo=1 AND id=$this->id_empresa ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            $this->arrayInfoEmpresa['documento']           = $this->mysql->result($query,0,'documento');
            $this->arrayInfoEmpresa['digito_verificacion'] = $this->mysql->result($query,0,'digito_verificacion');
            $this->arrayInfoEmpresa['razon_social']        = $this->mysql->result($query,0,'razon_social');

            $sql="SELECT ciudad FROM empresas_sucursales WHERE activo=1 AND id=".$_SESSION['SUCURSAL'];
            $query=$this->mysql->query($sql,$this->mysql->link);
            $this->arrayInfoEmpresa['ciudad'] = $this->mysql->result($query,0,'ciudad');
        }

        /**
        * @method setSecciones consultar las secciones del informe
        */
        private function setSecciones()
        {
            $sql="SELECT id,nombre,nombre_total,codigo_total FROM certificado_ingreso_retenciones_empleados_secciones WHERE activo=1 AND id_empresa=$this->id_empresa";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row = $this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id']] = array(
                                                'nombre'       => $row['nombre'],
                                                'nombre_total' => $row['nombre_total'],
                                                'codigo_total' => $row['codigo_total'],
                                                );
            }

            $this->arraySecciones = $arrayTemp;
        }

        /**
        * @method setFilas consultar las filas del informe
        */
        private function setFilas()
        {
            $sql="SELECT id,id_seccion,nombre,codigo FROM certificado_ingreso_retenciones_empleados_secciones_filas WHERE activo = 1 AND id_empresa=$this->id_empresa";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row = $this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id_seccion']][$row['id']] = array(
                                                                    'id_seccion' => $row['id_seccion'],
                                                                    'nombre'     => $row['nombre'],
                                                                    'codigo'     => $row['codigo'],
                                                                );
            }
            $this->arrayFilas = $arrayTemp;
        }

        /**
        * @method setConceptos consultar las secciones del informe
        */
        private function setConceptos()
        {
            $sql="SELECT id_seccion,id_fila,id_concepto,codigo_concepto,concepto,naturaleza FROM certificado_ingreso_retenciones_empleados_conceptos WHERE activo=1 AND id_empresa=$this->id_empresa";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row = $this->mysql->fetch_array($query)) {
                $arrayTemp[$row['id_seccion']][$row['id_fila']][$row['id_concepto']] = array(
                                                                                            'codigo_concepto' => $row['codigo_concepto'],
                                                                                            'concepto'        => $row['concepto'],
                                                                                            'naturaleza'      => $row['naturaleza'],
                                                                                            );
                $arrayIdConceptos[$row['id_concepto']] = $row['id_concepto'];
            }

            $this->arrayConceptosFormato = $arrayTemp;
            $this->getConceptosValues($arrayIdConceptos);
        }

        /**
        * @method getConceptosValues consultar los valores de los conceptos de las planillas
        */
        private function getConceptosValues($arrayIdConceptos)
        {
            $sql="SELECT id FROM nomina_planillas WHERE activo=1 AND id_empresa=$this->id_empresa AND (estado=1 OR estado=2) AND fecha_inicio>='$this->fecha_inicio' AND fecha_final<='$this->fecha_final' ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $whereIdPlanillas.=($whereIdPlanillas=='')? "id_planilla=".$row['id'] : " OR id_planilla=".$row['id'] ;
            }

            $sql="SELECT id FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$this->id_empresa AND (estado=1 OR estado=2) AND fecha_inicio>='$this->fecha_inicio' AND fecha_final<='$this->fecha_final' ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $whereIdPlanillasLiquidacion.=($whereIdPlanillasLiquidacion=='')? "id_planilla=".$row['id'] : " OR id_planilla=".$row['id'] ;
            }

            foreach ($arrayIdConceptos as $id_concepto => $value) {
                $whereIdConceptos.=($whereIdConceptos=='')? 'id_concepto='.$id_concepto : ' OR id_concepto='.$id_concepto ;
            }

            $sql="SELECT
                        SUM(valor_concepto) AS saldo,
                        id_concepto,
                        concepto,
                        id_empleado,
                        naturaleza
                    FROM nomina_planillas_empleados_conceptos
                    WHERE
                        activo = 1
                    AND id_empresa=$this->id_empresa
                    AND ($whereIdPlanillas)
                    AND ($whereIdConceptos)
                    AND id_empleado=$this->id_empleado
                    AND id_empresa=$this->id_empresa
                    AND naturaleza<>'Apropiacion'
                    GROUP BY id_empleado, id_concepto;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)){
                $arrayTemp[$row['id_concepto']]['concepto'] = utf8_encode($row['concepto']);

                if ($row['naturaleza']=='Deduccion') {
                    $arrayTemp[$row['id_concepto']]['saldo'] -= $row['saldo'];
                }
                else{
                    $arrayTemp[$row['id_concepto']]['saldo'] += $row['saldo'];
                }

            }

            $sql="SELECT
                        SUM(valor_concepto) AS saldo,
                        SUM(valor_concepto_ajustado) AS saldo_ajustado,
                        id_concepto,
                        concepto,
                        id_empleado,
                        naturaleza
                    FROM nomina_planillas_liquidacion_empleados_conceptos
                    WHERE
                        activo = 1
                    AND id_empleado=$this->id_empleado
                    AND id_empresa=$this->id_empresa
                    AND ($whereIdPlanillasLiquidacion)
                    AND ($whereIdConceptos)
                    GROUP BY id_empleado, id_concepto;";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)){
                $arrayTemp[$row['id_concepto']]['concepto'] = utf8_encode($row['concepto']);

                if ($row['naturaleza']=='Provision') {
                    $arrayTemp[$row['id_concepto']]['saldo'] += $row['saldo_ajustado'];
                }
                else if ($row['naturaleza']=='Deduccion') {
                    $arrayTemp[$row['id_concepto']]['saldo'] -= $row['saldo'];
                }
                else{
                    $arrayTemp[$row['id_concepto']]['saldo'] += $row['saldo'];
                }

            }

            $this->arrayConceptos = $arrayTemp;
            // echo json_encode($this->arrayConceptos);

        }

        private function setConceptosValue()
        {
            $saldo = 0;
            foreach ($this->arrayConceptosFormato as $id_seccion => $arrayFilas) {
                foreach ($arrayFilas as $id_fila => $arrayConceptos) {
                    foreach ($arrayConceptos as $id_concepto => $arrayResult) {
                        $saldo += $this->arrayConceptos[$id_concepto]['saldo'];
                        $this->arrayConceptosFormato[$id_seccion][$id_fila][$id_concepto]['saldo'] = $this->arrayConceptos[$id_concepto]['saldo'];
                    }
                    $this->arrayConceptosFormato[$id_seccion][$id_fila]['saldo'] = abs($saldo);
                    $saldo = 0;
                }
            }
        }

        /**
        * @method createFormat Crear el formato solicitado por el usuario
        */
        public function createFormat()
        {
            $this->getInfoEmpleado();
            $this->getInfoEmpresa();
            $this->setSecciones();
            $this->setFilas();
            $this->setConceptos();
            $this->setConceptosValue();

            if($this->IMPRIME_XLS=='true'){
                $logo_dian      = $_SERVER['DOCUMENT_ROOT'].'/LOGICALERP/informes/img/logo_dian.png';
                $logo_muisca    = $_SERVER['DOCUMENT_ROOT'].'/LOGICALERP/informes/img/logo_muisca.png';
                $numero_formato = $_SERVER['DOCUMENT_ROOT'].'/LOGICALERP/informes/img/numero_formato.png';
            }
            else{
                $logo_dian      = 'img/logo_dian.png';
                $logo_muisca    = 'img/logo_muisca.png';
                $numero_formato = 'img/numero_formato.png';
            }

            $logo_dian      = 'logo_dian.png';
            $logo_muisca    = 'logo_muisca.png';
            $numero_formato = 'numero_formato.png';

            list( $anio_inicio,$mes_inicio, $dia_inicio) = split('-', $this->fecha_inicio);
            list($anio_fin,$mes_fin, $dia_fin, ) = split('-', $this->fecha_final);

            $bodyResul.='
                        <script>
                            // console.log("'.$_SERVER['HTTP_HOST'].' - '.$_SERVER['SERVER_PORT'].' - '.$_SERVER['REQUEST_URI'].' ");
                            console.log("'.$_SERVER['SERVER_NAME'].'");
                        </script>
                        <tr style="height:50px;">
                            <td colspan="6" class="img"><img align=center src="https://bn1303files.storage.live.com/y3mqF8hd4DlnHxczcotzrCm0DpIn2-YZzdwIcO5li83QybnKyoI725SeL7sHSJaWpy8VCdPML9hOm-7X4hgKM1eVpGCLCYwNFKnZrE3Vsvy5lUSblESwXm5taMfU9RV00bLFSkI88TnJY3kJL2Pl8oZaUYHx5NCtTVfjTd7k-K0Pbo/logo_dian.png?psid=1&width=130&height=50"></td>
                            <td colspan="20" class="title">CERTIFICADO  DE  INGRESOS  Y  RETENCIONES PARA PERSONAS NATURALES EMPLEADOS  AÑO GRAVABLE</td>
                            <td colspan="8" class="img"><img align=center src="https://bn1303files.storage.live.com/y3mAsz7dmxbPqDnLog4nAlfvNN3-hg6aVIbJpaM8FnLTMr2KhgUs-kNkhJ-0-yGvj-sGr3FdQpcV_Zw28IbmDsHH82YN4uG3fTQzdYz_Wvos366Sm9CWAMYLUVfNZg-Gcm6_UNqtlpGXaJBandVPxbOrvOpuy3g5U6ZjOWZ1Wmvyy8/logo_muisca.png?psid=1&width=130&height=50"></td>
                            <td colspan="4" class="img"><img align=center src="https://bn1303files.storage.live.com/y3mbxDUXU56HWN91N3QrYvfa7q8xxVhvAyAaA7fbnRH7-3wcQUKEBAaUHCfVjoDal3on-bTxpMu1uaUCquaOBiq1dLf93s8EAtP-nxSJ-0gF0hoJJKtmCGLyKKy29Ci-sLIM4EebIa6tv4e_QMvUkHxzPDE0Zyjt0QfP4O6L54coik/numero_formato.png?psid=1&width=130&height=50"></td>
                        </tr>
                        <tr>
                            <td colspan="21"></td>
                            <td colspan="17">4. Numero de Formulario</td>
                        </tr>
                        <tr>
                            <td rowspan="5" class="verticalTd title">Retenedor</td>
                            <td style="border:none;" colspan="13">5. Numero de Identificacion Tributaria (NIT)</td>
                            <td style="border:none;border-right:0.5px solid;" colspan="2">6. DV</td>
                            <td style="border:none;" colspan="6">7. Primer Apellido</td>
                            <td style="border:none;" colspan="6">8. Segundo Apellido</td>
                            <td style="border:none;" colspan="7">9. Primer Nombre</td>
                            <td style="border:none;" colspan="3">10. Otros Nombres</td>
                        </tr>
                        <tr>
                            <td colspan="15" style="border-top:none;border-bottom:none;"></td>
                            <td colspan="22" style="border:none;"></td>
                        </tr>
                        <tr>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -13,-12).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -12,-11).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -11,-10).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -10,-9).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -9,-8).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -8,-7).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -7,-6).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -6,-5).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -5,-4).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -4,-3).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -3,-2).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -2,-1).'</td>
                            <td style="border-top:none;">'.substr($this->arrayInfoEmpresa['documento'], -1).'</td>
                            <td style="border-top:none;">-</td>
                            <td style="border-top:none;">'.$this->arrayInfoEmpresa['digito_verificacion'].'</td>
                            <td style="border-top:none;" colspan="6"></td>
                            <td style="border-top:none;" colspan="6"></td>
                            <td style="border-top:none;" colspan="7"></td>
                            <td style="border-top:none;" colspan="3"></td>
                        </tr>
                        <tr ><td style="border:none;background-color:#CCFFCC;"colspan="37">11. Razon Social</td></tr>
                        <tr ><td style="border:none;"colspan="37">'.$this->arrayInfoEmpresa['razon_social'].'</td></tr>

                        <tr >
                            <td rowspan="4" class="verticalTd title">Asalariado</td>
                            <td style="border-bottom:none;" colspan="5">24. Cod. tipo de documento</td>
                            <td style="border-bottom:none;" colspan="12">25. Numero de Documento de Identificacion</td>
                            <td style="border-bottom:none;" colspan="20">Apellidos y Nombres</td>
                        </tr>
                        <tr>
                            <td style="border-top:none;border-bottom:none;" colspan="5"></td>
                            <td style="border-top:none;border-bottom:none;" colspan="12">'.$this->arrayInfoEmpleado['documento'].'</td>
                            <td style="border-top:none;border-bottom:none;" colspan="20">
                                '.$this->arrayInfoEmpleado['apellido1'].'
                                '.$this->arrayInfoEmpleado['apellido2'].'
                                '.$this->arrayInfoEmpleado['nombre1'].'
                                '.$this->arrayInfoEmpleado['nombre2'].'
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top:none;border-bottom:none;" colspan="5"></td>
                            <td style="border-top:none;border-bottom:none;" colspan="12"></td>
                            <td style="border-top:none;border-bottom:none;" colspan="20"></td>
                        </tr>
                        <tr>
                            <td style="border-top:none;" colspan="5"></td>
                            <td style="border-top:none;" colspan="12"></td>
                            <td colspan="5">26. Primer Apellido</td>
                            <td colspan="6">27. Segundo Apellido</td>
                            <td colspan="6">28. Primer Nombre</td>
                            <td colspan="3">29. Otros Nombres</td>
                        </tr>
                        <tr >
                            <td colspan="18" style="border-bottom:none;" class="text-center" >Periodo de la certificacion</td>
                            <td colspan="7"  style="border-bottom:none;height:35px;" class="text-center" >32. Fecha de Expedicion</td>
                            <td colspan="8"  style="border-bottom:none;" class="text-center" >33. Lugar Donde Se practico</td>
                            <td colspan="2"  style="border-bottom:none;" class="text-center" >34. Cod. Depto</td>
                            <td colspan="3"  style="border-bottom:none;" class="text-center" >34. 35. Cod. Ciudad/ Municipio</td>
                        </tr>
                        <tr >
                            <td style="border-top:none;text-align:right;"  colspan="4">30. DE:</td>
                            <td style="border:none;border-right:0.5px solid;" colspan="4">'.$anio_inicio.'</td>
                            <td style="border:none;border-right:0.5px solid;" >'.$mes_inicio.'</td>
                            <td style="border:none;border-right:0.5px solid;" >'.$dia_inicio.'</td>
                            <td style="border:none;text-align:center;"  colspan="3">31. A:</td>
                            <td style="border:none;border-right:0.5px solid;"  colspan="2">'.$anio_fin.'</td>
                            <td style="border:none;border-right:0.5px solid;" >'.$mes_fin.'</td>
                            <td style="border:none;" >'.$dia_fin.'</td>
                            <td style="border:none;border-right:0.5px solid;" ></td>
                            <td style="border:none;" ></td>
                            <td style="border:none;"  colspan="3">'.date("Y").'</td>
                            <td style="border:none;" >'.date("m").'</td>
                            <td style="border:none;" >'.date("d").'</td>
                            <td style="border:none;border-right:0.5px solid;" ></td>
                            <td style="border:none;border-right:0.5px solid;text-align:center;"  colspan="8">'.$this->arrayInfoEmpresa['ciudad'].'</td>
                            <td style="border:none;" ></td>
                            <td style="border:none;border-right:0.5px solid;" ></td>
                            <td style="border:none;border-right:0.5px solid;" ></td>
                            <td style="border:none;border-right:0.5px solid;" ></td>
                            <td style="border:none;border-right:0.5px solid;" ></td>
                        </tr>
                        <tr>
                            <td colspan="24">36. Numero de agencias, sucursales, filiales o subsidiarias de la empresa retenedora cuyos montos de retención se consolidan:</td>
                            <td colspan="14"></td>
                        </tr>
                        ';
            $style = '';
            foreach ($this->arrayFilas as $id_seccion => $arrayFilasResul) {
                $bodyResul.='
                            <tr class="title">
                                <td style="background-color:#FEFF8E;" colspan="27">'.$this->arraySecciones[$id_seccion]['nombre'].'</td>
                                <td style="background-color:#FEFF8E;" colspan="11">Valor</td>
                            </tr>
                            ';
                $acumTotal = 0;
                foreach ($arrayFilasResul as $id_fila => $arrayResult) {
                    $bodyResul.='
                                <tr >
                                    <td '.$style.' colspan="27">'.$arrayResult['nombre'].'</td>
                                    <td '.$style.' >'.$arrayResult['codigo'].'</td>
                                    <td '.$style.' colspan="10">'.$this->arrayConceptosFormato[$id_seccion][$id_fila]['saldo'].'</td>
                                </tr>
                                ';
                    $acumTotal += $this->arrayConceptosFormato[$id_seccion][$id_fila]['saldo'];

                    $style=($style=='')? 'style="background-color:#CCFFCC;"' : '' ;

                }

                if ($this->arraySecciones[$id_seccion]['nombre_total']<>'') {
                    $bodyResul.='
                            <tr >
                                <td style="background-color:#006411;color:#FFF;" colspan="27">'.$this->arraySecciones[$id_seccion]['nombre_total'].'</td>
                                <td style="background-color:#006411;color:#FFF;">'.$this->arraySecciones[$id_seccion]['codigo_total'].'</td>
                                <td style="background-color:#006411;color:#FFF;" colspan="10">'.$acumTotal.'</td>
                            </tr>

                            ';
                }

            }

            $bodyResul.='
                        <tr>
                            <td colspan="38">Firma del Retenedor</td>
                        </tr>
                        <tr >
                            <td style="background-color:#FEFF8E;" class="title" colspan="38">Datos a Cargo del Asalariado</td>
                        </tr>
                        <tr >
                            <td style="background-color:#CCFFCC;" colspan="24" >Concepto de otro Ingresos</td>
                            <td style="background-color:#CCFFCC;" colspan="7">Valor Recibido</td>
                            <td style="background-color:#CCFFCC;" colspan="7">Valor Retenido</td>
                        </tr>
                        <tr>
                            <td colspan="24">Arrendamientos</td>
                            <td>47</td>
                            <td colspan="6">-</td>
                            <td>54</td>
                            <td colspan="6">-</td>
                        </tr>
                        <tr >
                            <td style="background-color:#CCFFCC;" colspan="24">Honorarios, comisiones y servicios</td>
                            <td style="background-color:#CCFFCC;" >48</td>
                            <td style="background-color:#CCFFCC;" colspan="6">-</td>
                            <td style="background-color:#CCFFCC;">55</td>
                            <td style="background-color:#CCFFCC;" colspan="6">-</td>
                        </tr>
                        <tr>
                            <td colspan="24">Intereses y rendimientos financieros</td>
                            <td>49</td>
                            <td colspan="6">-</td>
                            <td>56</td>
                            <td colspan="6">-</td>
                        </tr>
                        <tr >
                            <td style="background-color:#CCFFCC;" colspan="24">Enajenacion de activos fijos</td>
                            <td style="background-color:#CCFFCC;">50</td>
                            <td style="background-color:#CCFFCC;" colspan="6">-</td>
                            <td style="background-color:#CCFFCC;">57</td>
                            <td style="background-color:#CCFFCC;" colspan="6">-</td>
                        </tr>
                        <tr>
                            <td colspan="24">Loterias, rifas, apuestas y similares</td>
                            <td>51</td>
                            <td colspan="6">-</td>
                            <td>58</td>
                            <td colspan="6">-</td>
                        </tr>
                        <tr >
                            <td style="background-color:#CCFFCC;" colspan="24">Otros</td>
                            <td style="background-color:#CCFFCC;">52</td>
                            <td style="background-color:#CCFFCC;" colspan="6">-</td>
                            <td style="background-color:#CCFFCC;">59</td>
                            <td style="background-color:#CCFFCC;" colspan="6">-</td>
                        </tr>
                        <tr>
                            <td colspan="24">Totales: (Valor recibido: Sume casillas 47 a 52),  (Valor retenido: Sume casillas 54 a  59)</td>
                            <td>53</td>
                            <td colspan="6">-</td>
                            <td>60</td>
                            <td colspan="6">-</td>
                        </tr>
                        <tr >
                            <td style="background-color:#CCFFCC;" colspan="31">Total retenciones año gravable '.$anio_inicio.'  (Sume casillas 46 + 60)</td>
                            <td style="background-color:#CCFFCC;">61</td>
                            <td style="background-color:#CCFFCC;" colspan="6">-</td>
                        </tr>
                        <tr class="title">
                            <td style="background-color:#FEFF8E;">Item</td>
                            <td style="background-color:#FEFF8E;" colspan="30">62. Identificacion de los bienes poseidos</td>
                            <td style="background-color:#FEFF8E;" colspan="7">63. Valor Patrimonial</td>
                        </tr>
                        <tr >
                            <td style="background-color:#CCFFCC;">1</td>
                            <td style="background-color:#CCFFCC;" colspan="30"></td>
                            <td style="background-color:#CCFFCC;" colspan="7">-</td>
                        </tr>
                        <tr><td>2</td><td colspan="30"></td><td colspan="7">-</td></tr>
                        <tr >
                            <td style="background-color:#CCFFCC;">3</td>
                            <td style="background-color:#CCFFCC;" colspan="30"></td>
                            <td style="background-color:#CCFFCC;" colspan="7">-</td>
                        </tr>
                        <tr><td>4</td><td colspan="30"></td><td colspan="7">-</td></tr>
                        <tr >
                            <td style="background-color:#CCFFCC;">5</td>
                            <td style="background-color:#CCFFCC;" colspan="30"></td>
                            <td style="background-color:#CCFFCC;" colspan="7">-</td>
                        </tr>
                        <tr><td>6</td><td colspan="30"></td><td colspan="7">-</td></tr>
                        <tr >
                            <td style="background-color:#CCFFCC;">7</td>
                            <td style="background-color:#CCFFCC;" colspan="30"></td>
                            <td style="background-color:#CCFFCC;" colspan="7">-</td>
                        </tr>
                        <tr><td>8</td><td colspan="30"></td><td colspan="7">-</td></tr>
                        <tr >
                            <td style="background-color:#CCFFCC;" colspan="31">Deudas vigentes a 31 de Diciembre de  '.$anio_inicio.'  </td>
                            <td style="background-color:#CCFFCC;" colspan="7">64 -</td>
                        </tr>

                        <tr >
                            <td style="background-color:#FEFF8E;" class="title" colspan="38">Identificacion de las personas dependientes de acuerdo al paragrafo 2 del articulo 387 del E.T.</td>
                        </tr>
                        <tr >
                            <td style="background-color:#CCFFCC;" >Item</td>
                            <td style="background-color:#CCFFCC;" colspan="7">65. C.C. o NIT</td>
                            <td style="background-color:#CCFFCC;" colspan="23">66. Apellidos y Nombres</td>
                            <td style="background-color:#CCFFCC;" colspan="7">67. Parentesco</td>
                        </tr>
                        <tr><td>1</td><td colspan="7"></td><td colspan="23"></td><td colspan="7"></td></tr>
                        <tr >
                            <td style="background-color:#CCFFCC;">2</td>
                            <td style="background-color:#CCFFCC;" colspan="7"></td>
                            <td style="background-color:#CCFFCC;" colspan="23"></td>
                            <td style="background-color:#CCFFCC;" colspan="7"></td>
                        </tr>
                        <tr><td>3</td><td colspan="7"></td><td colspan="23"></td><td colspan="7"></td></tr>
                        <tr >
                            <td style="background-color:#CCFFCC;">4</td>
                            <td style="background-color:#CCFFCC;" colspan="7"></td>
                            <td style="background-color:#CCFFCC;" colspan="23"></td>
                            <td style="background-color:#CCFFCC;" colspan="7"></td>
                        </tr>
                        <tr>
                            <td colspan="27">Certifico que durante el año gravable '.$anio_inicio.'  :</td>
                            <td colspan="11">Firma del Asalariado:</td>
                        </tr>
                        ';

            $formato="
                <head>
                    <meta content='charset=UTF-8' />
                </head>
                <style>

                    td{
                      width : 37px !important;
                          font-family: \"Trebuchet MS\", Verdana, Arial, sans-serif, \"Lucida Grande\";
                    }

                   .tabla td {
                        font-size:12px;
                        padding:5px;
                        border:0.5px solid;
                        height : 30px;

                    }

                    .tabla {
                        border-collapse: collapse;
                        border: 1px solid green;
                    }

                    img{
                        width: 130px;
                        height : 50px;
                        text-align : center;
                    }

                    .verticalTd{
                        font-weight       : bold;
                        text-align        : center;
                        vertical-align    : middle;
                        width             : 20px;
                        margin            : 0px;
                        padding           : 0px;
                        padding-left      : 3px;
                        padding-right     : 3px;
                        padding-top       : 10px;
                        white-space       : nowrap;
                        -webkit-transform : rotate(-90deg);
                        -moz-transform    : rotate(-90deg);
                    }

                    .title, .title td{
                        font-weight: bold;
                        text-align: center;
                    }

                    .img{
                        text-align: center;
                    }

                    .text-center{
                        text-align:center;
                    }

                </style>
                <table class='tabla'>
                $bodyResul
                </table>
                ";

            echo $formato;
        }
    }

?>