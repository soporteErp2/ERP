<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

    /**
     * Class carteraEdades Informe de la cartera de clientes
     */
    class CarteraEdades
    {
        private $MyInformeFiltroFechaInicio;
        private $MyInformeFiltroFechaFinal;
        private $arrayClientesJSON;
        private $arrayCuentasPagoJSON;
        private $tipo_fecha_informe;
        private $agrupacion;
        private $tipo_informe;
        private $sqlCheckbox;
        private $id_empresa;
        private $sucursal;
        private $IMPRIME_PDF;
        private $IMPRIME_XLS;
        private $separador_miles;
        private $separador_decimales;
        private $mysql;
        private $whereLeftJoin;
        private $whereInnerJoin;

        /**
         * __construct Inicializacion y asignacion de variables
         * @param date $MyInformeFiltroFechaInicio Fecha de inicio del informe de cartera
         * @param date $MyInformeFiltroFechaFinal Fecha de final del informe de cartera
         * @param json $arrayClientesJSON Array json con los clientes a filtrar
         * @param json $arrayCuentasPagoJSON Array json con las cuentas de pago a filtrar
         * @param String $tipo_fecha_informe Tipo fecha (corte, rango de fechas)
         * @param String $agrupacion Agrupacion para visualizar el informe (Clientes, Facturas)
         * @param String $tipo_informe Tipo de informe a generar (Detallado,Totalizado por Terceros,Totalizado por Edades)
         * @param String $sqlCheckbox Condicion con las fechas a filtrar el informe
         * @param Int $id_empres Id de la empres (Variable de sesion)
         * @param String $sucursal Consultar una sucursal o todas
         * @param String $order_by Sentencia SQL con el orden del reporte
         * @param String $IMPRIME_PDF Opcion a imprimir PDF
         * @param String $IMPRIME_XLS Opcion a imprimir EXCEL
         * @param Objeto $mysql Objeto de conexion mysql
         */
        function __construct($MyInformeFiltroFechaFinal,$MyInformeFiltroFechaInicio,$arrayClientesJSON,$arrayCuentasPagoJSON,$tipo_fecha_informe,$agrupacion,$tipo_informe,$sqlCheckbox,$sucursal,$order_by,$IMPRIME_PDF=null,$IMPRIME_XLS=null,$separador_miles,$separador_decimales,$mysql){
            $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
            $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
            $this->arrayClientesJSON          = json_decode($arrayClientesJSON);
            $this->arrayCuentasPagoJSON       = json_decode($arrayCuentasPagoJSON);
            $this->tipo_fecha_informe         = $tipo_fecha_informe;
            $this->agrupacion                 = $agrupacion;
            $this->tipo_informe               = $tipo_informe;
            $this->sqlCheckbox                = $sqlCheckbox;
            $this->id_empresa                 = $_SESSION['EMPRESA'];
            $this->sucursal                   = $sucursal;
            $this->order_by                   = $order_by;
            $this->IMPRIME_PDF                = $IMPRIME_PDF;
            $this->IMPRIME_XLS                = $IMPRIME_XLS;
            $this->separador_miles            = $separador_miles;
            $this->separador_decimales        = $separador_decimales;
            $this->mysql                      = $mysql;
        }

        /**
         * setFilters Filtros del usuario para la consulta del informe
         */
        public function setFilters(){
            $this->whereLeftJoin = ($this->tipo_fecha_informe=='corte')? " AND fecha <= '$this->MyInformeFiltroFechaFinal' " : " AND fecha BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal' ";
            if (!empty($this->arrayClientesJSON)) {
                foreach ($this->arrayClientesJSON as $indice => $id_cliente) {
                    $whereClientes .= ($whereClientes=='')? ' VF.id_cliente='.$id_cliente : ' OR VF.id_cliente='.$id_cliente;
                }
                    $whereClientes   = " AND (".$whereClientes.")";
            }
            $this->whereInnerJoin .= $whereClientes;
            if (!empty($this->arrayCuentasPagoJSON)) {
                foreach ($this->arrayCuentasPagoJSON as $indice => $cuenta_pago) {
                    $whereCuentas .= ($whereCuentas=='')? ' codigo_cuenta='.$cuenta_pago : ' OR codigo_cuenta='.$cuenta_pago;
                }
                $whereCuentas   = " AND (".$whereCuentas.")";
            }
            else{ $whereCuentas = $this->getCuentasPago(); }
            $this->whereLeftJoin .= $whereCuentas;
            $this->whereInnerJoin .= ($this->sqlCheckbox<>'')? " AND $this->sqlCheckbox" : "" ;
            $this->whereInnerJoin .= ($this->sucursal!='' && $this->sucursal!='global')? " AND VF.id_sucursal=$this->sucursal " : "" ;

        }

        /**
         * getInfoSucursal Consultar la informacion de la sucursal consultada
         * @return String nombre Nombre de la sucursal consultada
         */
        public function getInfoSucursal(){
            $sql="SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$this->id_empresa AND id=$this->sucursal";
            $query=$this->mysql->query($sql,$this->mysql->link);
            $nombre = $this->mysql->result($query,0,'nombre');
        }

        /**
         * getCuentasPago Consultar las cuentas de pago
         * @return String whereTemp Where a aplicar en la consulta con las cuentas de pago
         */
        public function getCuentasPago(){
            $whereTemp = "";
            $sql   = "SELECT cuenta FROM configuracion_cuentas_pago WHERE id_empresa=$this->id_empresa AND activo=1 AND tipo='Venta' AND estado='Credito'";
            $query = $this->mysql->query($sql,$this->mysql->link);
            while ($row = $this->mysql->fetch_assoc($query)) { $whereTemp .= " OR codigo_cuenta=$row[cuenta]"; }
            $whereTemp = substr($whereTemp, 3);

            return " AND ($whereTemp) ";
        }

        /**
         * getInformData Consultar las facturas
         * @return array Array con la informacion de todas las facturas
         */
        public function getInformData(){
            $nombreTempo = "asientosTempo$_SESSION[ID_HOST]";
            $sqlTempoTable = "CREATE TEMPORARY TABLE $nombreTempo
                              SELECT SUM(debe - haber) AS saldo,id_documento_cruce,codigo_cuenta,activo,fecha,tipo_documento_cruce,id_empresa
                              FROM asientos_colgaap
                              WHERE activo = 1
                              $this->whereLeftJoin
                              AND tipo_documento_cruce = 'FV'
                              AND id_empresa = '$this->id_empresa'
                              GROUP BY id_documento_cruce
                              HAVING saldo > 0";
            $queryTempoTable = $this->mysql->query($sqlTempoTable,$this->mysql->link);
            $whereSaldo = ($_SESSION['NITEMPRESA']==900474556)? "AND  A.saldo>0" : 'AND  VF.total_factura_sin_abono>1';
            $sql = "SELECT
                        T.telefono1,
                        T.celular1,
                        VF.cuenta_pago AS codigo_cuenta,
                        DATEDIFF('$this->MyInformeFiltroFechaFinal',VF.fecha_vencimiento) AS dias,
                        VF.id,
                        VF.id_cliente,
                        VF.nit,
                        REPLACE(VF.cliente, ' ', '') AS cliente,
                        VF.cliente AS cliente_real,
                        VF.fecha_inicio,
                        VF.fecha_vencimiento,
                        VF.numero_factura_completo,
                        VF.codigo_centro_costo AS codigo_ccos,
                        VF.centro_costo AS nombre_ccos,
                        VF.sucursal,
                        A.saldo
                    FROM
                        $nombreTempo AS A
                    INNER JOIN ventas_facturas AS VF ON(
                        A.id_documento_cruce = VF.id
                        AND A.codigo_cuenta = VF.cuenta_pago
                        AND VF.activo = 1
                        AND VF.estado = 1
                        $whereSaldo
                        AND VF.id_empresa = '$this->id_empresa'
                        $this->whereInnerJoin
                    )
                    LEFT JOIN terceros AS T ON(
                        VF.id_cliente = T.id
                        AND T.id_empresa = '$this->id_empresa'
                    )
                    ORDER BY
                        $this->order_by, fecha_inicio ASC";
            $query=$this->mysql->query($sql,$this->mysql->link);

            $sqlTempoTable = "DROP TEMPORARY TABLE $nombreTempo";
            $queryTempoTable = $this->mysql->query($sqlTempoTable,$this->mysql->link);

            while ($row=$this->mysql->fetch_array($query)) {
                $porVencer            = ($row['dias']<1)? $row['saldo']: '&nbsp;';
                $unoAtreinta          = ($row['dias']>0 && $row['dias']<=30)? $row['saldo'] : '&nbsp;';
                $treintayunoAsesenta  = ($row['dias']>30 && $row['dias']<=60)? $row['saldo'] : '&nbsp;';
                $sesentayunoAnoventa  = ($row['dias']>60 && $row['dias']<=90)? $row['saldo'] : '&nbsp;';
                $masDenoventa         = ($row['dias']>90)? $row['saldo'] : '&nbsp;';

                $arrayTemp[$row['id']] = array(
                                                "telefono1"               => $row['telefono1'],
                                                "celular1"                => $row['celular1'],
                                                "codigo_cuenta"           => $row['codigo_cuenta'],
                                                "dias"                    => $row['dias'],
                                                "nit"                     => $row['nit'],
                                                "cliente"                 => $row['cliente_real'],
                                                "fecha_inicio"            => $row['fecha_inicio'],
                                                "fecha_vencimiento"       => $row['fecha_vencimiento'],
                                                "numero_factura_completo" => $row['numero_factura_completo'],
                                                "codigo_ccos"             => $row['codigo_ccos'],
                                                "nombre_ccos"             => $row['nombre_ccos'],
                                                "sucursal"                => $row['sucursal'],
                                                "saldo"                   => $row['saldo'],
                                                "porVencer"               => $porVencer,
                                                "unoAtreinta"             => $unoAtreinta,
                                                "treintayunoAsesenta"     => $treintayunoAsesenta,
                                                "sesentayunoAnoventa"     => $sesentayunoAnoventa,
                                                "masDenoventa"            => $masDenoventa,
                                                );
            }
            return $arrayTemp;

        }

        /**
         * setOrderData Organizar el array con la informacion a listar en el informe
         */
        public function setOrderData(){
            # code...
        }

        public function getStyle(){
            $style = "<style>
                        .contenedor_informe, .contenedor_titulo_informe{
                            width     : 100%;
                            margin    : 0 0 15px 0;
                            font-size : 11px;
                            float     : left;
                        }

                        .titulo_informe_label{
                            float       : left;
                            width       : 130px;
                            font-weight : bold;
                        }

                        .titulo_informe_detalle{
                            float         : left;
                            width         : 210px;
                            padding       : 0 0 0 5px;
                            white-space   : nowrap;
                            overflow      : hidden;
                            text-overflow : ellipsis;
                        }

                        .titulo_informe_empresa{
                            float       : left;
                            width       : 100%;
                            font-size   : 16px;
                            font-weight : bold;
                        }

                        .table{
                            font-size       : 12px;
                            width           : 100%;
                            border-collapse : collapse;
                        }

                        .table thead{ background : #999; }

                        .table thead td {
                            padding-left : 10px;
                            height       : 30px;
                            background   : #999;
                            color        : #FFF;
                        }

                        .total{
                            background  : #EEE;
                            font-weight : bold;
                        }

                        .total td{
                            border-top    : 1px solid #999;
                            border-bottom : 1px solid #999;
                            background    : #EEE;
                            padding-left  : 10px;
                            height        : 25px;
                            font-weight   : bold;
                            color         : #8E8E8E;
                        }

                    </style>";
            return $style;
        }

        public function getTitle(){
            $title = "<table align='center' style='text-align:center;margin-bottom:10px;'>
                        <tr><td class='titulo_informe_empresa' style='text-align:center;'> $_SESSION[NOMBREEMPRESA]</td></tr>
                        <tr><td style='font-size:13px;text-align:center;'><b>NIT</b> $_SESSION[NITEMPRESA]</td></tr>
                        <tr><td style='width:100%; font-weight:bold; font-size:14px; text-align:center;'>Cartera de Clientes</td></tr>
                    </table>";
            return $title;
        }

        public function getExcel(){
            if ($this->agrupacion == 'Clientes'){
                foreach ($arrayFacturas as $id => $arrayResult){
                    $arrayFacturasClientes[$arrayResult['nit']][] = $id;
                    $arrayClientes[$arrayResult['nit']]['nombre']    = $arrayResult['cliente'];
                    $arrayClientes[$arrayResult['nit']]['telefono1'] = $arrayResult['telefono1'];
                    $arrayClientes[$arrayResult['nit']]['celular1']  = $arrayResult['celular1'];
                }

                $acumuladoporVencerTotal           = 0;
                $acumuladounoAtreintaTotal         = 0;
                $acumuladotreintayunoAsesentaTotal = 0;
                $acumuladosesentayunoAnoventaTotal = 0;
                $acumuladomasDenoventaTotal        = 0;
                foreach ($arrayFacturasClientes as $nit => $values) {
                    $acumuladoSaldo               = 0;
                    $acumuladoporVencer           = 0;
                    $acumuladounoAtreinta         = 0;
                    $acumuladotreintayunoAsesenta = 0;
                    $acumuladosesentayunoAnoventa = 0;
                    $acumuladomasDenoventa        = 0;

                    $bodyTable .= "<tr class='total'>
                                        <td colspan='14'><b>DOCUMENTO: $nit CLIENTE: ".$arrayClientes[$nit]['nombre']." Tel: ".$arrayClientes[$nit]['telefono1']." Cel: ".$arrayClientes[$nit]['celular1']." </b></td>
                                    </tr>";

                    foreach ($values as $key => $id_factura) {
                        $acumuladoporVencerTotal           += $arrayFacturas[$id_factura]['porVencer'];;
                        $acumuladounoAtreintaTotal         += $arrayFacturas[$id_factura]['unoAtreinta'];;
                        $acumuladotreintayunoAsesentaTotal += $arrayFacturas[$id_factura]['treintayunoAsesenta'];;
                        $acumuladosesentayunoAnoventaTotal += $arrayFacturas[$id_factura]['sesentayunoAnoventa'];;
                        $acumuladomasDenoventaTotal        += $arrayFacturas[$id_factura]['masDenoventa'];;

                        $acumuladoSaldo               += $arrayFacturas[$id_factura]['saldo'];
                        $acumuladoporVencer           += $arrayFacturas[$id_factura]['porVencer'];
                        $acumuladounoAtreinta         += $arrayFacturas[$id_factura]['unoAtreinta'];
                        $acumuladotreintayunoAsesenta += $arrayFacturas[$id_factura]['treintayunoAsesenta'];
                        $acumuladosesentayunoAnoventa += $arrayFacturas[$id_factura]['sesentayunoAnoventa'];
                        $acumuladomasDenoventa        += $arrayFacturas[$id_factura]['masDenoventa'];


                        if ($this->tipo_informe=='detallado') {
                            $bodyTable .= "<tr>
                                                <td>".$arrayFacturas[$id_factura]['sucursal']."</td>
                                                <td width='80' style='$style padding-left:15px;'>".$arrayFacturas[$id_factura]['codigo_cuenta']."</td>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['numero_factura_completo']."</td>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['fecha_inicio']."</td>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['fecha_vencimiento']."</td>
                                                <td width='80' style='$style text-align:center;'>".$arrayFacturas[$id_factura]['dias']."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['saldo'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['porVencer'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['unoAtreinta'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['treintayunoAsesenta'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['sesentayunoAnoventa'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['masDenoventa'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            </tr>";
                        }

                    }
                    // if ($this->tipo_informe=='totalizado_terceros') {
                    if ($this->tipo_informe<>'totalizado_edades') {
                        $bodyTable .= "<tr class='total'>
                                            <td colspan='6'>TOTAL CLIENTE</td>
                                            <td style='text-align:right;'>".number_format($acumuladoSaldo,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladoporVencer,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladounoAtreinta,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladotreintayunoAsesenta,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladosesentayunoAnoventa,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladomasDenoventa,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        </tr>";
                    }

                    // if ($this->tipo_informe=='totalizado_edades') {
                    //     # code...
                    // }
                }

                $bodyTable .= "<tr><td>&nbsp;</td></tr>
                                <tr class='total'>
                                    <td colspan='7'><b>TOTAL CARTERA</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladoporVencerTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladounoAtreintaTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladotreintayunoAsesentaTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladosesentayunoAnoventaTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladomasDenoventaTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                </tr>
                                <tr class='total'>
                                    <td colspan='11'><b>TOTAL CONSOLIDADO</b></td>
                                    <td style='text-align:right;'><b>".number_format(($acumuladoporVencerTotal+$acumuladounoAtreintaTotal+$acumuladotreintayunoAsesentaTotal+$acumuladosesentayunoAnoventaTotal+$acumuladomasDenoventaTotal),$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                </tr>";


                echo $this->getStyle();
                echo $this->getTitle();
                ?>
                    <table class="table" cellspacing="0">
                        <thead>
                            <tr>
                                <td>SUCURSAL</td>
                                <td width="80" style="font-weight:bold;">CUENTA</td>
                                <td width="80" style="font-weight:bold;">NUMERO</td>
                                <td width="80" style="font-weight:bold; ">FECHA</td>
                                <td width="80" style="font-weight:bold; ">VENCIMIENTO</td>
                                <td width="80" style="font-weight:bold; text-align:center;">DIAS</td>
                                <td width="80" style="font-weight:bold; text-align:right;">SALDO</td>
                                <td width="80" style="font-weight:bold; text-align:right;">POR VENCER</td>
                                <td width="80" style="font-weight:bold; text-align:right;">1-30</td>
                                <td width="80" style="font-weight:bold; text-align:right;">31-60</td>
                                <td width="80" style="font-weight:bold; text-align:right;">61-90</td>
                                <td width="80" style="font-weight:bold; text-align:right;">MAS 90</td>
                            </tr>
                        </thead>
                        <?php echo $bodyTable; ?>
                    </table>
                <?php
            }
            else{
                foreach ($arrayFacturas as $id => $arrayResult){
                    $bodyTable .= "<tr>
                                        <td>$arrayResult[sucursal]</td>
                                        <td>$arrayResult[nit]</td>
                                        <td>$arrayResult[cliente]</td>
                                        <td>$arrayResult[numero_factura_completo]</td>
                                        <td>$arrayResult[fecha_inicio]</td>
                                        <td>$arrayResult[fecha_vencimiento]</td>
                                        <td>$arrayResult[dias]</td>
                                        <td>".number_format($arrayResult['saldo'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['porVencer'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['unoAtreinta'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['treintayunoAsesenta'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['sesentayunoAnoventa'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['masDenoventa'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                    </tr>";
                }

                echo $this->getStyle();
                echo $this->getTitle();
                ?>

                    <table class="table">
                        <thead>
                        <tr>
                            <td>SUCURSAL</td>
                            <td style="font-weight:bold;">NIT</td>
                            <td style="font-weight:bold;">CLIENTE</td>
                            <td style="font-weight:bold;">NUMERO</td>
                            <td style="font-weight:bold; ">FECHA</td>
                            <td style="font-weight:bold; ">VENCIMIENTO</td>
                            <td style="font-weight:bold; text-align:center;">DIAS</td>
                            <td style="font-weight:bold; text-align:right;">SALDO</td>
                            <td style="font-weight:bold; text-align:right;">POR VENCER</td>
                            <td style="font-weight:bold; text-align:right;">1-30</td>
                            <td style="font-weight:bold; text-align:right;">31-60</td>
                            <td style="font-weight:bold; text-align:right;">61-90</td>
                            <td style="font-weight:bold; text-align:right;">MAS 90</td>
                        </tr>
                    </thead>
                    <?php echo $bodyTable ?>
                    </table>
                <?php
            }
        }

        public function getContent($arrayFacturas){
            if($this->IMPRIME_XLS=='true'){
               header('Content-type: application/vnd.ms-excel;');
               header("Content-Disposition: attachment; filename=cartera_por_edades_de_cliente_".date('Y_m_d').".xls");
               header("Pragma: no-cache");
               header("Expires: 0");
            }

            if($this->IMPRIME_PDF=='true'){
                ob_start();
            }

            // print_r($arrayFacturas);
            if ($this->agrupacion == 'Clientes'){
                foreach ($arrayFacturas as $id => $arrayResult){
                    $arrayFacturasClientes[$arrayResult['nit']][] = $id;
                    $arrayClientes[$arrayResult['nit']]['nombre']    = $arrayResult['cliente'];
                    $arrayClientes[$arrayResult['nit']]['telefono1'] = $arrayResult['telefono1'];
                    $arrayClientes[$arrayResult['nit']]['celular1']  = $arrayResult['celular1'];
                }

                $acumuladoporVencerTotal           = 0;
                $acumuladounoAtreintaTotal         = 0;
                $acumuladotreintayunoAsesentaTotal = 0;
                $acumuladosesentayunoAnoventaTotal = 0;
                $acumuladomasDenoventaTotal        = 0;
                foreach ($arrayFacturasClientes as $nit => $values) {
                    $acumuladoSaldo               = 0;
                    $acumuladoporVencer           = 0;
                    $acumuladounoAtreinta         = 0;
                    $acumuladotreintayunoAsesenta = 0;
                    $acumuladosesentayunoAnoventa = 0;
                    $acumuladomasDenoventa        = 0;

                    $bodyTable .= "<tr class='total'>
                                        <td colspan='14'><b>DOCUMENTO: $nit CLIENTE: ".$arrayClientes[$nit]['nombre']." Tel: ".$arrayClientes[$nit]['telefono1']." Cel: ".$arrayClientes[$nit]['celular1']."</b></td>
                                    </tr>";

                    foreach ($values as $key => $id_factura) {
                        $acumuladoporVencerTotal           += $arrayFacturas[$id_factura]['porVencer'];;
                        $acumuladounoAtreintaTotal         += $arrayFacturas[$id_factura]['unoAtreinta'];;
                        $acumuladotreintayunoAsesentaTotal += $arrayFacturas[$id_factura]['treintayunoAsesenta'];;
                        $acumuladosesentayunoAnoventaTotal += $arrayFacturas[$id_factura]['sesentayunoAnoventa'];;
                        $acumuladomasDenoventaTotal        += $arrayFacturas[$id_factura]['masDenoventa'];;

                        $acumuladoSaldo               += $arrayFacturas[$id_factura]['saldo'];
                        $acumuladoporVencer           += $arrayFacturas[$id_factura]['porVencer'];
                        $acumuladounoAtreinta         += $arrayFacturas[$id_factura]['unoAtreinta'];
                        $acumuladotreintayunoAsesenta += $arrayFacturas[$id_factura]['treintayunoAsesenta'];
                        $acumuladosesentayunoAnoventa += $arrayFacturas[$id_factura]['sesentayunoAnoventa'];
                        $acumuladomasDenoventa        += $arrayFacturas[$id_factura]['masDenoventa'];


                        if ($this->tipo_informe=='detallado') {
                            $bodyTable .= "<tr>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['sucursal']."</td>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['codigo_ccos']."</td>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['nombre_ccos']."</td>
                                                <td width='80' style='$style padding-left:15px;'>".$arrayFacturas[$id_factura]['codigo_cuenta']."</td>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['numero_factura_completo']."</td>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['fecha_inicio']."</td>
                                                <td width='80' style='$style'>".$arrayFacturas[$id_factura]['fecha_vencimiento']."</td>
                                                <td width='80' style='$style text-align:center;'>".$arrayFacturas[$id_factura]['dias']."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['saldo'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['porVencer'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['unoAtreinta'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['treintayunoAsesenta'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['sesentayunoAnoventa'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                                <td width='80' style='$style text-align:right;'>".number_format($arrayFacturas[$id_factura]['masDenoventa'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            </tr>";
                        }

                    }
                    // if ($this->tipo_informe=='totalizado_terceros') {
                    if ($this->tipo_informe<>'totalizado_edades') {
                        $bodyTable .= "<tr class='total'>
                                            <td colspan='8'>TOTAL CLIENTE</td>
                                            <td style='text-align:right;'>".number_format($acumuladoSaldo,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladoporVencer,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladounoAtreinta,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladotreintayunoAsesenta,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladosesentayunoAnoventa,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                            <td style='text-align:right;'>".number_format($acumuladomasDenoventa,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        </tr><tr><td>&nbsp;</td></tr>";
                    }

                    // if ($this->tipo_informe=='totalizado_edades') {
                    //     # code...
                    // }
                }

                $bodyTable .= "<tr><td>&nbsp;</td></tr>
                                <tr class='total'>
                                    <td colspan='9'><b>TOTAL CARTERA</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladoporVencerTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladounoAtreintaTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladotreintayunoAsesentaTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladosesentayunoAnoventaTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                    <td style='text-align:right;'><b>".number_format($acumuladomasDenoventaTotal,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                </tr>
                                <tr class='total'>
                                    <td colspan='13'><b>TOTAL CONSOLIDADO</b></td>
                                    <td style='text-align:right;'><b>".number_format(($acumuladoporVencerTotal+$acumuladounoAtreintaTotal+$acumuladotreintayunoAsesentaTotal+$acumuladosesentayunoAnoventaTotal+$acumuladomasDenoventaTotal),$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</b></td>
                                </tr>";


                echo $this->getStyle();
                echo $this->getTitle();
                ?>
                    <table class="table" cellspacing="0">
                        <thead>
                            <tr>
                                <td width="80" style="font-weight:bold;">SUCURSAL</td>
                                <td width="80" style="font-weight:bold;">CODIGO CCOS</td>
                                <td width="80" style="font-weight:bold;">NOMBRE CCOS</td>
                                <td width="80" style="font-weight:bold;">CUENTA</td>
                                <td width="80" style="font-weight:bold;">NUMERO</td>
                                <td width="80" style="font-weight:bold;">FECHA</td>
                                <td width="80" style="font-weight:bold;">VENCIMIENTO</td>
                                <td width="80" style="font-weight:bold; text-align:center;">DIAS</td>
                                <td width="80" style="font-weight:bold; text-align:right;">SALDO</td>
                                <td width="80" style="font-weight:bold; text-align:right;">POR VENCER</td>
                                <td width="80" style="font-weight:bold; text-align:right;">1-30</td>
                                <td width="80" style="font-weight:bold; text-align:right;">31-60</td>
                                <td width="80" style="font-weight:bold; text-align:right;">61-90</td>
                                <td width="80" style="font-weight:bold; text-align:right;">MAS 90</td>
                            </tr>
                        </thead>
                        <?php echo $bodyTable; ?>
                    </table>
                <?php
            }
            else{
                foreach ($arrayFacturas as $id => $arrayResult){
                    $bodyTable .= "<tr>
                                        <td>$arrayResult[sucursal]</td>
                                        <td>$arrayResult[codigo_ccos]</td>
                                        <td>$arrayResult[nombre_ccos]</td>
                                        <td>$arrayResult[nit]</td>
                                        <td>$arrayResult[cliente]</td>
                                        <td>$arrayResult[codigo_cuenta]</td>
                                        <td>$arrayResult[numero_factura_completo]</td>
                                        <td>$arrayResult[fecha_inicio]</td>
                                        <td>$arrayResult[fecha_vencimiento]</td>
                                        <td>$arrayResult[dias]</td>
                                        <td>".number_format($arrayResult['saldo'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['porVencer'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['unoAtreinta'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['treintayunoAsesenta'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['sesentayunoAnoventa'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                        <td>".number_format($arrayResult['masDenoventa'],$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                    </tr>";

                    $saldo               += $arrayResult['saldo'];
                    $porVencer           += $arrayResult['porVencer'];
                    $unoAtreinta         += $arrayResult['unoAtreinta'];
                    $treintayunoAsesenta += $arrayResult['treintayunoAsesenta'];
                    $sesentayunoAnoventa += $arrayResult['sesentayunoAnoventa'];
                    $masDenoventa        += $arrayResult['masDenoventa'];
                }

                $bodyTable .= "<tr class='total'>
                                    <td>TOTALES</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>".number_format($saldo,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                    <td>".number_format($porVencer,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                    <td>".number_format($unoAtreinta,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                    <td>".number_format($treintayunoAsesenta,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                    <td>".number_format($sesentayunoAnoventa,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                    <td>".number_format($masDenoventa,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                </tr>
                                <tr class='total'>
                                    <td>TOTALES</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>".number_format($porVencer+$unoAtreinta+$treintayunoAsesenta+$sesentayunoAnoventa+$masDenoventa,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles)."</td>
                                </tr>";

                echo $this->getStyle();
                echo $this->getTitle();
                ?>

                    <table class="table">
                        <thead>
                        <tr>
                            <td style="font-weight:bold;">SUCURSAL</td>
                            <td style="font-weight:bold;">CODIGO CCOS</td>
                            <td style="font-weight:bold;">NOMBRE CCOS</td>
                            <td style="font-weight:bold;">NIT</td>
                            <td style="font-weight:bold;">CLIENTE</td>
                            <td style="font-weight:bold;">CUENTA</td>
                            <td style="font-weight:bold;">NUMERO</td>
                            <td style="font-weight:bold;">FECHA</td>
                            <td style="font-weight:bold;">VENCIMIENTO</td>
                            <td style="font-weight:bold; text-align:center;">DIAS</td>
                            <td style="font-weight:bold; text-align:right;">SALDO</td>
                            <td style="font-weight:bold; text-align:right;">POR VENCER</td>
                            <td style="font-weight:bold; text-align:right;">1-30</td>
                            <td style="font-weight:bold; text-align:right;">31-60</td>
                            <td style="font-weight:bold; text-align:right;">61-90</td>
                            <td style="font-weight:bold; text-align:right;">MAS 90</td>
                        </tr>
                    </thead>
                    <?php echo $bodyTable ?>
                    </table>
                <?php
            }

            if($this->IMPRIME_PDF=='true'){
                $content  = ob_get_contents(); ob_end_clean();

                if(isset($TAM)){ $HOJA = $TAM; }
                else{ $HOJA = 'LETTER-L'; }

                if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
                if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
                if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

                if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
                else{ $MS=10; $MD=10; $MI=10; $ML=10; }

                if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

                include("../../../../misc/MPDF54/mpdf.php");
                $mpdf = new mPDF(
                            'utf-8',        // mode - default ''
                            $HOJA,          // format - A4, for example, default ''
                            12,             // font size - default 0
                            '',             // default font family
                            $MI,            // margin_left
                            $MD,            // margin right
                            $MS,            // margin top
                            $ML,            // margin bottom
                            10,             // margin header
                            10,             // margin footer
                            $ORIENTACION    // L - landscape, P - portrait
                        );
                // $mpdf-> debug = true;
                // $mpdf->useSubstitutions = true;
                $mpdf->simpleTables = true;
                $mpdf->packTableData= true;
                $mpdf->SetAutoPageBreak(TRUE, 15);
                $mpdf->SetTitle($documento);
                $mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
                $mpdf->SetDisplayMode( 'fullpage' );
                $mpdf->SetHeader("");
                $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');
                $mpdf->WriteHTML(utf8_encode($content));

                if($PDF_GUARDA=='true'){ $mpdf->Output("cartera_por_edades_de_clientes.pdf",'D'); }
                else{ $mpdf->Output("cartera_por_edades_de_clientes.pdf",'I'); }
            }

        }

        /**
         * generate Generar el informe
         */
        public function generate(){
            $this->setFilters();
            $arrayFacturas=$this->getInformData();
            // if ($this->IMPRIME_XLS=='true') {
            //     $this->getExcel($arrayFacturas);
            // }
            // else{
                $this->getContent($arrayFacturas);
            // }
        }
    }

    $informe = new CarteraEdades($MyInformeFiltroFechaFinal,$MyInformeFiltroFechaInicio,$arrayClientesJSON,$arrayCuentasPagoJSON,$tipo_fecha_informe,$agrupacion,$tipo_informe,$sqlCheckbox,$sucursal,$order_by,$IMPRIME_PDF,$IMPRIME_XLS,$separador_miles,$separador_decimales,$mysql);
    $informe->generate();

?>
