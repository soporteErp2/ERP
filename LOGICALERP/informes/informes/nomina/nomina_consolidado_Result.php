<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

    /**
     * consolidatePayroll clase para el informe de nomina consolidado
     */
    class consolidatePayroll
    {

        private $sucursal;
        private $MyInformeFiltroFechaFinal;
        private $MyInformeFiltroFechaInicio;
        private $arrayEmpleadosJSON;
        private $arrayConceptosJSON;
        private $IMPRIME_XLS;
        private $separador_miles;
        private $separador_decimales;
        private $id_empresa;
        private $filters;
        private $mysql;
        function __construct($sucursal,$MyInformeFiltroFechaFinal,$MyInformeFiltroFechaInicio,$arrayEmpleadosJSON,$arrayConceptosJSON,$IMPRIME_XLS,$separador_miles,$separador_decimales,$mysql)
        {
            $this->sucursal                   = $sucursal;
            $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
            $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
            $this->arrayEmpleadosJSON         = json_decode($arrayEmpleadosJSON);
            $this->arrayConceptosJSON         = $arrayConceptosJSON;
            $this->mysql                      = $mysql;
            $this->id_empresa                 = $_SESSION['EMPRESA'];
            $this->IMPRIME_XLS                = $IMPRIME_XLS;
            $this->separador_miles            = $separador_miles;
            $this->separador_decimales        = $separador_decimales;
        }

        /**
         * setFilters Establecer los filtros de la consulta del informe
         * @return String Filtros de la consulta del informe
         */
        public function setFilters()
        {
            $this->filters = " AND NP.fecha_inicio >= '$this->MyInformeFiltroFechaInicio' AND NP.fecha_final <= '$this->MyInformeFiltroFechaFinal' ";
            foreach ($this->arrayEmpleadosJSON as $indice => $id_empleado) {
                $whereEmpleados .= ($whereEmpleados=='')? ' NPE.id_empleado='.$id_empleado : ' OR NPE.id_empleado='.$id_empleado;
            }
            $this->filters .= ($whereEmpleados<>'')? " AND ($whereEmpleados) " : "" ;
        }

        public function getInfo()
        {
            $sql = "SELECT
                            NP.fecha_inicio,
                            NP.consecutivo,
                            NPE.documento_empleado,
                            NPE.nombre_empleado,
                            SUM(NPE.dias_laborados_empleado) AS dias_laborados,
                            NPEC.codigo_concepto,
                            NPEC.concepto,
                            NPEC.naturaleza,
                            SUM(NPEC.valor_concepto) AS valor_concepto,
                            NPEC.id_contrato
                        FROM  nomina_planillas AS NP
                        INNER JOIN nomina_planillas_empleados AS NPE ON NPE.id_planilla=NP.id
                        INNER JOIN nomina_planillas_empleados_conceptos AS NPEC ON NPEC.id_planilla=NP.id
                        WHERE NP.activo=1
                        AND (NP.estado = 1 OR NP.estado = 2)
                        AND NPE.activo=1
                        AND NPEC.activo=1
                        AND NPEC.id_empleado=NPE.id_empleado
                        AND (NPEC.naturaleza = 'Devengo' OR NPEC.naturaleza = 'Deduccion' )
                        AND NP.id_empresa = $this->id_empresa
                        $this->filters
                        GROUP BY NPE.documento_empleado, NPEC.codigo_concepto
                        ORDER BY NPE.nombre_empleado,NPEC.codigo_concepto ASC
                    ";
            $query = $this->mysql->query($sql);
            while ($row=$this->mysql->fetch_array($query)){
              $documento  = $row['documento_empleado'];
              $naturaleza = $row['naturaleza'];
              $arrayColumnas[$row['naturaleza']][$row['codigo_concepto']] = $row['concepto'];
              $arrayWhereContrato[$row['id_contrato']] = $row['id_contrato'];

              if(!isset($arrayInfo[$documento])){
                  $arrayInfo[$documento] = array(
                                                  'nombre_empleado' => $row['nombre_empleado'],
                                                  'dias_laborados'  => $row['dias_laborados'],
                                                  'id_contrato'     => $row['id_contrato'],
                                                  $naturaleza => array(
                                                                          $row['codigo_concepto'] => array(
                                                                                                      'codigo_concepto' => $row['codigo_concepto'],
                                                                                                      'concepto'        => $row['concepto'],
                                                                                                      'valor_concepto'  => $row['valor_concepto'],
                                                                                                  )
                                                                      )
                                                  );
              }
              else{
                $arrayInfo[$documento][$naturaleza][$row['codigo_concepto']] = array(
                                                                                      'codigo_concepto' => $row['codigo_concepto'],
                                                                                      'concepto'        => $row['concepto'],
                                                                                      'valor_concepto'  => $row['valor_concepto'],
                                                                                    );
              }

              if($row['codigo_concepto'] == "SB"){
                $arrayInfo[$documento]['dias_laborados'] = $row['dias_laborados'];
              }
            }

            $whereContratos = implode(" OR id=", array_keys($arrayWhereContrato));
            $sql="SELECT id,salario_basico FROM empleados_contratos WHERE activo=1 AND (id= $whereContratos) ";
            $query = $this->mysql->query($sql);
            while ($row=$this->mysql->fetch_array($query)){
                $arrayContratos[$row['id']] = $row['salario_basico'];
            }
            foreach ($arrayInfo as $documento => $arrayInforResult) {
                $arrayInfo[$documento]['salario_contrato']=$arrayContratos[$arrayInforResult['id_contrato']];
            }

            return array("filas"=>$arrayInfo,"columnas"=>$arrayColumnas);
        }

        /**
         * render Renderizar la informacion del informe
         * @param Array reporInfo Array con la informacion del informe a renderizar
         */
        public function render($reporInfo)
        {
        //   echo "<pre>";
        //   print_r($reporInfo);
        //   echo "</pre>";
            if($this->IMPRIME_XLS=='true'){
               header('Content-type: application/vnd.ms-excel');
               header("Content-Disposition: attachment; filename=informe_nomina_consolidado_".date("Y_m_d").".xls");
               header("Pragma: no-cache");
               header("Expires: 0");
            }

            $numColsDev = count($reporInfo['columnas']['Devengo']);
            $numColsDec = count($reporInfo['columnas']['Deduccion']);
            foreach ($reporInfo['columnas']['Devengo'] as $codigo_concepto => $concepto) {
                $bodyTableSubtitle .= "<td>$concepto</td>";
            }

            foreach ($reporInfo['columnas']['Deduccion'] as $codigo_concepto => $concepto) {
                $bodyTableSubtitle .= "<td>$concepto</td>";
            }

            foreach ($reporInfo['filas'] as $documento => $arrayEmpleado) {
                $bodyTable .= "
                                <tr>
                                    <td style='text-align:center;'>$documento</td>
                                    <td>$arrayEmpleado[nombre_empleado]</td>
                                    <td style='text-align:right;'>".number_format($arrayEmpleado['salario_contrato'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales, $this->separador_miles)."</td>
                                    <td style='text-align:center;'>$arrayEmpleado[dias_laborados]</td>
                            ";
                $totalEmpleado=0;
                $bodyTable1 = "";
                $bodyTable2 = "";
                foreach ($reporInfo['columnas'] as $naturaleza => $arrayConceptos) {
                    foreach ($arrayConceptos as $codigo_concepto => $concepto) {

                      if($naturaleza == "Devengo"){
                        $valor_concepto =  $reporInfo['filas'][$documento][$naturaleza][$codigo_concepto]['valor_concepto'];
                        $conceptoFila   = $reporInfo['filas'][$documento][$naturaleza][$codigo_concepto]['concepto'];

                        // var_dump($conceptoFila); echo " - "; var_dump($concepto); echo "<br>";

                          $bodyTable1 .= "<td style='text-align:right;' >".number_format($valor_concepto,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales, $this->separador_miles)."</td>";


                        $totalEmpleado = ($naturaleza=='Devengo')? $totalEmpleado+$valor_concepto : $totalEmpleado-$valor_concepto ;

                      }
                      else{
                        $valor_concepto =  $reporInfo['filas'][$documento][$naturaleza][$codigo_concepto]['valor_concepto'];
                        $conceptoFila   = $reporInfo['filas'][$documento][$naturaleza][$codigo_concepto]['concepto'];

                        // var_dump($conceptoFila); echo " - "; var_dump($concepto); echo "<br>";

                          $bodyTable2 .= "<td style='text-align:right;' >".number_format($valor_concepto,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales, $this->separador_miles)."</td>";


                        $totalEmpleado = ($naturaleza=='Devengo')? $totalEmpleado+$valor_concepto : $totalEmpleado-$valor_concepto ;
                      }
                    }
                }
                $bodyTable .= $bodyTable1.$bodyTable2;
                $bodyTable .= "<td style='text-align:right;'>".number_format($totalEmpleado,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales, $this->separador_miles)."</td>
                                </tr>";
            }
            $colspanTitle = $numColsDev+$numColsDec+5;
            ?>
                <style>
                    .tableReport {
                        font-family     : -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
                        border-collapse : collapse;
                        font-size       : 12px;
                    }

                    .reportTitle {
                        font-size   : 16px;
                        font-weight : bold;
                        text-align  : center;
                    }

                    .title{
                        color            : #FFF;
                        background-color : #999;
                        padding          : 10px;
                        text-align       : center;
                    }

                    .title td {
                        font-weight      : bold;
                        font-size        : 14px;
                        padding          : 10px;
                    }

                    .subTitle{
                        color            : #8E8E8E;
                        background-color : #EEE;
                        text-align       : center;
                    }

                    .subTitle td {
                        font-weight      : bold;
                        padding          : 10px;
                    }
                </style>
                <table class="tableReport">
                    <tr class="reportTitle">
                        <td colspan="<?php echo $colspanTitle ?>">Informe consolidado de Nomina</td>
                    </tr>
                    <tr class="reportTitle">
                        <td colspan="<?php echo $colspanTitle ?>">De <?php echo $this->MyInformeFiltroFechaInicio ?> a <?php echo $this->MyInformeFiltroFechaFinal ?></td>
                    </tr>

                    <tr class="title">
                        <td colspan="4">INFORMACION EMPLEADO</td>
                        <td colspan="<?php echo $numColsDev; ?>" >TOTAL DEVENGADOS</td>
                        <td colspan="<?php echo $numColsDec; ?>" >TOTAL DEDUCIDOS</td>
                        <td>TOTAL</td>
                    </tr>
                    <tr class="subTitle">
                        <td>DOCUMENTO</td>
                        <td>EMPLEADO</td>
                        <td>SALARIO CONTRATO</td>
                        <td>DIAS</td>
                        <?php echo $bodyTableSubtitle; ?>
                        <td>TOTAL A PAGAR</td>
                    </tr>
                    <?php echo $bodyTable; ?>
                </table>
            <?php
        }

        /**
         * generate Generar el informe
         */
        public function generate()
        {
            $this->setFilters();
            $reporInfo = $this->getInfo();
            $this->render($reporInfo);
        }

    }

    $objectRepor = new consolidatePayroll($sucursal,$MyInformeFiltroFechaFinal,$MyInformeFiltroFechaInicio,$arrayEmpleadosConsolidadoJSON,'',$IMPRIME_XLS,$separador_miles,$separador_decimales,$mysql);
    $objectRepor->generate();
?>
