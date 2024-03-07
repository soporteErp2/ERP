<?php

    include_once('../../../../../configuracion/conectar.php');
    include_once('../../../../../configuracion/define_variables.php');
    include('Class.Report.php');

    if($IMPRIME_XLS=='true'){
        header('Content-Encoding: UTF-8');
        header('Content-type: application/vnd.ms-excel;');
        header("Content-Disposition: attachment; filename=Medios_magneticos_".date("Y-m-d").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    }

    $id_empresa = $_SESSION['EMPRESA'];

    // var_dump($object);
    // $object->setFormatoInfo();

    /**
    * @class ReportNiif
    *
    */
    class ReportNiif extends ClassReport
    {
        public $arrayFilasFormato        = '';
        public $formatosRequeridos       = '';
        public $arrayCuentasFilasFormato = '';
        public $arraySeccionesFormato    = '';
        public $arrayFormatoInfo         = '';

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
        function __construct($id_formato,$fecha_inicio,$fecha_final,$separador_miles,$separador_decimales,$arrayCentroCostosJSON,$id_empresa,$mysql)
        {
            // parent::__construct();

            // INICIALIZAR EL CONSTRUCTOR DE LA CLASE PADRE
            parent::__construct($fecha_inicio,$fecha_final,$arrayCentroCostosJSON,$id_empresa,$mysql);

            $this->id_formato          = $id_formato;
            // $this->fecha_inicio        = $fecha_inicio;
            // $this->fecha_final         = $fecha_final;
            $this->separador_miles     = $separador_miles;
            $this->separador_decimales = $separador_decimales;
            $this->id_empresa          = $id_empresa;
            // $this->ccosFiltro          = json_decode($arrayCentroCostosJSON);
            // $this->xlsPrint            = $IMPRIME_XLS;
            // $this->mysql               = $mysql;

        }

        /*
        * @method createTreeView
         */
        public function createTreeView($codigo_seccion_padre){
            $body        = "";
            $label_total = "";
            // echo $this->arraySeccionesFormato['parent'][$codigo_seccion_padre];
            // SI EXISTE LA SECCION PADRE
            if (isset($this->arraySeccionesFormato['parent'][$codigo_seccion_padre]) ) {
                $body .= "<div class='tree'>";
                foreach ($this->arraySeccionesFormato['parent'][$codigo_seccion_padre] as $codigo_seccion) {
                    $label_total = "";
                    if(!isset($this->arraySeccionesFormato['child'][$codigo_seccion])) {
                        $body .= "<div >".$this->arraySeccionesFormato['child'][$codigo_seccion]['nombre']."</div>";
                    }

                    if(isset($this->arraySeccionesFormato['child'][$codigo_seccion])) {
                        $body .= "<div ><b>".$this->arraySeccionesFormato['child'][$codigo_seccion]['nombre']."</b></div>".$this->setFilasSecciones($codigo_seccion);
                        $body .= $this->createTreeView($codigo_seccion, $this->arraySeccionesFormato);
                        if ($this->arraySeccionesFormato['child'][$codigo_seccion]['totalizado']=='true' && $codigo_seccion_padre<>$codigo_seccion ) {
                            $label_total = ($this->arraySeccionesFormato['child'][$codigo_seccion]['label_total']<>'')? $this->arraySeccionesFormato['child'][$codigo_seccion]['label_total'] : 'Total '.$this->arraySeccionesFormato['child'][$codigo_seccion]['nombre'] ;
                            $body .= "<div style='width:calc(100% - 230px) !important;' ><b>$label_total</b></div>
                                        <div style='width: 200px !important;' ><b>".number_format($this->arraySeccionesFormato['child'][$codigo_seccion]['total'],2,$this->separador_decimales,$this->separador_miles)."</b></div>";
                        }
                    }
                }
                $body .= "</div>";
            }
            // echo $body;
            return $body;
        }

        /**
        * @method setFilasSecciones
        * @param int id de la seccion a listar la fila
        */
        public function setFilasSecciones($id_seccion){
            $filas = "";

            foreach ($this->arrayFilasFormato as $id_fila => $arrayResult) {
                if ($arrayResult['id_seccion']<>$id_seccion) { continue; }
                 $filas .= "<div style='margin-left: 30px; !important;width:calc(100% - 245px) !important;'>$arrayResult[nombre]</div>
                            <div style='width: 200px !important;'>".number_format($arrayResult['total'],2,$this->separador_decimales,$this->separador_miles)."</div>";
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
            $this->arrayFormatoInfo         = $this->setFormatoInfo($this->id_formato);
            $this->arrayFilasFormato        = $this->setFilasFormato($this->id_formato,true);
            $this->arrayCuentasFilasFormato = $this->setCuentasFilasFormato($this->id_formato);
            $this->arraySeccionesFormato    = $this->setSeccionesFormato($this->id_formato,true);
            $this->arrayFilasFormato        = $this->setAsientos($this->arrayCuentasFilasFormato['whereAsientos'],$this->arrayFormatoInfo['whereSaldoAnterior'],$this->arrayFormatoInfo['whereSaldo'],$this->arrayCuentasFilasFormato,$this->arrayFilasFormato);
            $this->arrayFilasFormato        = $this->calculaFormulaFilas($id_formato,$this->arrayFilasFormato);
            // print_r($this->arraySeccionesFormato);
            $this->arraySeccionesFormato    = $this->setValorSecciones($this->arrayFilasFormato,$this->arraySeccionesFormato);
            $bodyResul = $this->createTreeView(0);
            print_r($this->formatosRequeridos);
            // print_r($this->arrayAsientos);
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
                                $bodyResul
                             </div>
                        </div>";
            echo utf8_decode($formato);
        }
    }

    $object = new ReportNiif($id_formato,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$separador_miles,$separador_decimales,$arrayCentroCostosJSON,$id_empresa,$mysql);
    $object->createFormat();
?>