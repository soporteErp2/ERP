<?php
    // header('Content-Type: text/html; charset=UTF-8');

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

    $id_empresa = $_SESSION['EMPRESA'];
    $object = new CRICA($fecha_inicio,$fecha_final,$id_tercero,$tipo_retencion,$id_empresa,$mysql,$IMPRIME_PDF);
    $object->createCert();


    /**
    * @class CRICA Certificado de Retencion ICA
    */
    class CRICA
    {
        private $mysql            = '';
        private $fecha_inicio     = '';
        private $fecha_final      = '';
        private $tipo_retencion      = '';
        private $id_empresa       = '';
        private $id_tercero       = '';
        private $arrayInfoEmpresa = '';
        private $arrayInfotercero = '';
        private $arrayRetenciones = '';
        private $whereCuentas     = '';
        private $arrayCuentas     = '';
        private $IMPRIME_PDF      = '';

        /**
        * @method construct
        * @param srt fecha respectiva del periodo
        * @param int id del tercero
        * @param int id de la empresa
        * @param obj objeto de conexion mysql
        */
        function __construct($fecha_inicio,$fecha_final,$id_tercero,$tipo_retencion,$id_empresa,$mysql,$IMPRIME_PDF)
        {
            $this->fecha_inicio         = $fecha_inicio;
            $this->fecha_final          = $fecha_final;
            $this->id_empresa           = $id_empresa;
            $this->id_tercero           = $id_tercero;
            $this->tipo_retencion       = $tipo_retencion;
            $this->mysql                = $mysql;
            $this->IMPRIME_PDF          = $IMPRIME_PDF;
            $this->nombre_certificado   = ($tipo_retencion=='ReteIva')? "IVA" :"INDUSTRIA Y COMERCIO ICA";
        }

        /**
        * @method getInfoEmpresa Consultar la informacion de la empresa
        */
        public function getInfoEmpresa()
        {
            $sql   = "SELECT nit_completo,razon_social,direccion,ciudad FROM empresas WHERE activo = 1 AND id=".$this->id_empresa;
            $query = $this->mysql->query($sql,$this->mysql->link);

            $this->arrayInfoEmpresa['nit_completo'] = $this->mysql->result($query,0,'nit_completo');
            $this->arrayInfoEmpresa['razon_social'] = $this->mysql->result($query,0,'razon_social');
            $this->arrayInfoEmpresa['direccion']    = $this->mysql->result($query,0,'direccion');
            $this->arrayInfoEmpresa['ciudad']       = $this->mysql->result($query,0,'ciudad');
        }

        /**
        * @method getInfoTercero Consultar la informacion del tercero
        */
        public function getInfoTercero()
        {
            $sql="SELECT nombre, numero_identificacion,direccion,ciudad FROM terceros WHERE activo=1 AND id=".$this->id_tercero;
            $query=$this->mysql->query($sql,$this->mysql->link);

            $this->arrayInfotercero['nombre']                = $this->mysql->result($query,0,'nombre');
            $this->arrayInfotercero['numero_identificacion'] = $this->mysql->result($query,0,'numero_identificacion');
            $this->arrayInfotercero['direccion']             = $this->mysql->result($query,0,'direccion');
            $this->arrayInfotercero['ciudad']                = $this->mysql->result($query,0,'ciudad');
        }

        /**
        * @method getReteciones Consultar las retenciones creadas
        */
        public function getReteciones()
        {
            $sql="SELECT id,retencion,valor,cuenta,ciudad,base FROM retenciones WHERE activo=1 AND id_empresa=$this->id_empresa AND modulo='compra' AND tipo_retencion='$this->tipo_retencion' ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['cuenta']]=array(
                                                'retencion' => $row['retencion'],
                                                'valor'     => $row['valor'],
                                                'cuenta'    => $row['cuenta'],
                                                'ciudad'    => $row['ciudad'],
                                                'base'    => $row['base'],
                                            );
                $whereTemp .= ($whereTemp=='')? 'codigo_cuenta='.$row['cuenta'] : ' OR codigo_cuenta='.$row['cuenta'] ;
            }

            $this->arrayRetenciones = $arrayTemp;
            $this->whereCuentas     = $whereTemp;
        }

        /**
        * @method getAsientos Consultar los asientos con las cuentas de las retenciones
        */
        public function getAsientos()
        {
            $sql="SELECT SUM(debe) AS debito,SUM(haber) AS credito,SUM(haber - debe) AS saldo,codigo_cuenta
                    FROM asientos_colgaap
                    WHERE
                        activo=1
                    AND id_empresa=$this->id_empresa
                    AND id_tercero=$this->id_tercero
                    AND ($this->whereCuentas)
                    AND fecha BETWEEN '$this->fecha_inicio' AND '$this->fecha_final'
                    GROUP BY codigo_cuenta ";
            $query=$this->mysql->query($sql,$this->mysql->link);
            while ($row=$this->mysql->fetch_array($query)) {
                $arrayTemp[$row['codigo_cuenta']]=array(
                                                        'debito'  => $row['debito'],
                                                        'credito' => $row['credito'],
                                                        'saldo'   => $row['saldo'],
                                                        );
            }
            $this->arrayCuentas = $arrayTemp;
        }

        /**
        * @method createCert Crear el formato solicitado por el usuario
        */
        public function createCert()
        {
            $this->getInfoEmpresa();
            $this->getInfoTercero();
            $this->getReteciones();
            $this->getAsientos();

            foreach ($this->arrayCuentas as $codigo_cuenta => $arrayResult) {
                if ($arrayResult['saldo']==0) { continue; }
                $valor_total += $arrayResult['saldo'];
                $body.='<tr>
                            <td style="padding-left:15px;">'.$this->arrayRetenciones[$codigo_cuenta]['retencion'].'</td>
                            <td>'.$this->arrayRetenciones[$codigo_cuenta]['ciudad'].'</td>
                            <td>'.$this->arrayRetenciones[$codigo_cuenta]['valor'].'</td>
                            <td>'.number_format((($arrayResult['saldo']*100)/$this->arrayRetenciones[$codigo_cuenta]['valor']),$_SESSION['DECIMALESMONEDA']).'</td>
                            <td>'.number_format($arrayResult['saldo'],$_SESSION['DECIMALESMONEDA']).'</td>
                        </tr>';
            }

            $formato.='<style>
                            .head{
                                width         : 100%;
                                padding       : 0 0 20px 0;
                                margin        : 0 0 10px 0;
                                border-bottom : 1px solid #CCC;
                            }

                            table{ border-collapse: collapse;}
                            .tableHead td{
                                font-size   : 11px;
                                font-family : Verdana, Geneva, sans-serif;
                                text-align  : center;
                            }

                            .titulo_informe_empresa{
                                float       : left;
                                width       : 100%;
                                font-size   : 16px;
                                font-weight : bold;
                            }

                            .body{
                                width      : 100%;
                                text-align : left;
                            }

                            .tableResponsable{
                                font-size   : 11px;
                                font-family : Verdana, Geneva, sans-serif;
                                margin-bottom : 20px;
                            }

                            .tableDetalle{
                                font-size   : 11px;
                                font-family : Verdana, Geneva, sans-serif;
                                width : 95%;
                                margin-bottom : 30px;
                            }

                            .trHead td {
                                border-top    : 1px solid #999;
                                border-bottom : 1px solid #999;
                                background    : #EEE;
                                color         : #8E8E8E;
                                height        : 25px;
                                font-weight : bold;
                            }

                            .tableTotal{
                                margin-bottom : 30px;
                            }

                            .tableTotal td{
                                padding : 0 20 0 20;
                                font-size : 13px;
                            }

                        </style>
                        <div class="head">
                            <table align="center" class="tableHead">
                                <tr><td class="titulo_informe_empresa" style="text-align:center;">'.$this->arrayInfoEmpresa['razon_social'].'</td></tr>
                                <tr><td ><b>NIT: </b>'.$this->arrayInfoEmpresa['nit_completo'].'</td></tr>
                                <tr><td ><b>Direccion: </b>'.$this->arrayInfoEmpresa['direccion'].'</td></tr>
                                <tr><td ><b>Ciudad: </b>'.$this->arrayInfoEmpresa['ciudad'].'</td></tr>
                                <tr><td>&nbsp;</td></tr>
                                <tr><td style="width:100%; font-weight:bold; font-size:14px; text-align:center;text-transform: uppercase;">Certificado de Retencion de '.$this->nombre_certificado.'</td></tr>
                                <tr style="font-size:12px;"><td>Desde '.$this->fecha_inicio.' hasta '.$this->fecha_final.'<br>Fecha de expedicion '.date('Y-m-d').'</td></tr>
                        </table>
                        </div>
                        <div class="body">
                            <table class="tableResponsable">
                                <tr><td style="width:90px;"><b>Responsable: &nbsp;&nbsp;</b></td><td>'.$this->arrayInfotercero['nombre'].' </td></tr>
                                <tr><td><b>Identificacion: &nbsp;&nbsp;</b></td><td> '.$this->arrayInfotercero['numero_identificacion'].' </td></tr>
                                <tr><td><b>Direccion: &nbsp;&nbsp;</b></td><td>'.$this->arrayInfotercero['direccion'].' </td></tr>
                                <tr><td><b>Ciudad: &nbsp;&nbsp;</b></td><td>'.$this->arrayInfotercero['ciudad'].' </td></tr>
                            </table>

                            <table class="tableDetalle">
                                <tr class="trHead">
                                    <td style="padding-left:15px;">RETENCION</td>
                                    <td>CIUDAD</td>
                                    <td>TASA %</td>
                                    <td>VALOR BASE</td>
                                    <td>VALOR RETENIDO</td>
                                </tr>
                                '.$body.'
                            </table>

                            <table class="tableTotal">
                                <tr class="trHead">
                                    <td>TOTAL</td>
                                    <td style="text-align:right;">$ '.number_format($valor_total,$_SESSION['DECIMALESMONEDA']).'</td>
                                </tr>
                                <tr>
                                    <td colspan="2"><i>'.$this->numToString(ROUND($valor_total,$_SESSION['DECIMALESMONEDA'])).'<i></td>
                                </tr>

                            </table>

                            <table class="tableTotal">
                                <tr>
                                    <td colspan="2"><i>Este documento no requiere para su validez firma autografa de acuerdo con el articulo 10 del Decreto 836 de 1991. recopilado en el articulo 1.6.1.12.12 del DUT 1625 d octubre de 2016, que regula el contenido del certificado de retenciones a titulo de renta.<i></td>
                                </tr>
                            </table>

                        </div>
                        ';

            if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
            if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
            if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
            if(!isset($this->IMPRIME_PDF)){$this->IMPRIME_PDF = 'false';}
            if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=10;}
            if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}
            if($this->IMPRIME_PDF == 'true'){
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
                //       $mpdf->useSubstitutions = true;
                //       $mpdf->simpleTables = true;
                //       $mpdf->packTableData= true;
                $documento = "certificado retenciones ".trim($this->arrayInfoEmpresa['razon_social']);
                $mpdf->SetAutoPageBreak(TRUE, 15);
                $mpdf->SetTitle ( $documento );
                $mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
                $mpdf->SetDisplayMode ( 'fullpage' );
                $mpdf->SetHeader("");
                $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

                $mpdf->WriteHTML(utf8_encode($formato));
                if($PDF_GUARDA=='true'){$mpdf->Output($documento.".pdf",'D');}else{ $mpdf->Output($documento.".pdf",'I');}
                exit;
            }
            else{
                echo $formato;
            }

        }

         /**
        * @method numToString equivalente en letras de un numero
        * @return str valor equivalente en letras
        */
        public function numToString($num, $fem = false, $dec = true)
        {
           $matuni[2]  = "dos";
           $matuni[3]  = "tres";
           $matuni[4]  = "cuatro";
           $matuni[5]  = "cinco";
           $matuni[6]  = "seis";
           $matuni[7]  = "siete";
           $matuni[8]  = "ocho";
           $matuni[9]  = "nueve";
           $matuni[10] = "diez";
           $matuni[11] = "once";
           $matuni[12] = "doce";
           $matuni[13] = "trece";
           $matuni[14] = "catorce";
           $matuni[15] = "quince";
           $matuni[16] = "dieciseis";
           $matuni[17] = "diecisiete";
           $matuni[18] = "dieciocho";
           $matuni[19] = "diecinueve";
           $matuni[20] = "veinte";
           $matunisub[2] = "dos";
           $matunisub[3] = "tres";
           $matunisub[4] = "cuatro";
           $matunisub[5] = "quin";
           $matunisub[6] = "seis";
           $matunisub[7] = "sete";
           $matunisub[8] = "ocho";
           $matunisub[9] = "nove";

           $matdec[2] = "veint";
           $matdec[3] = "treinta";
           $matdec[4] = "cuarenta";
           $matdec[5] = "cincuenta";
           $matdec[6] = "sesenta";
           $matdec[7] = "setenta";
           $matdec[8] = "ochenta";
           $matdec[9] = "noventa";
           $matsub[3]  = 'mill';
           $matsub[5]  = 'bill';
           $matsub[7]  = 'mill';
           $matsub[9]  = 'trill';
           $matsub[11] = 'mill';
           $matsub[13] = 'bill';
           $matsub[15] = 'mill';
           $matmil[4]  = 'millones';
           $matmil[6]  = 'billones';
           $matmil[7]  = 'de billones';
           $matmil[8]  = 'millones de billones';
           $matmil[10] = 'trillones';
           $matmil[11] = 'de trillones';
           $matmil[12] = 'millones de trillones';
           $matmil[13] = 'de trillones';
           $matmil[14] = 'billones de trillones';
           $matmil[15] = 'de billones de trillones';
           $matmil[16] = 'millones de billones de trillones';

           //Zi hack
           $float=explode('.',$num);
           $num=$float[0];

           $num = trim((string)@$num);
           if ($num[0] == '-') {
              $neg = 'menos ';
              $num = substr($num, 1);
           }else
              $neg = '';
           while ($num[0] == '0') $num = substr($num, 1);
           if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num;
           $zeros = true;
           $punt = false;
           $ent = '';
           $fra = '';
           for ($c = 0; $c < strlen($num); $c++) {
              $n = $num[$c];
              if (! (strpos(".,'''", $n) === false)) {
                 if ($punt) break;
                 else{
                    $punt = true;
                    continue;
                 }

              }elseif (! (strpos('0123456789', $n) === false)) {
                 if ($punt) {
                    if ($n != '0') $zeros = false;
                    $fra .= $n;
                 }else

                    $ent .= $n;
              }else

                 break;

           }
           $ent = '     ' . $ent;
           if ($dec and $fra and ! $zeros) {
              $fin = ' coma';
              for ($n = 0; $n < strlen($fra); $n++) {
                 if (($s = $fra[$n]) == '0')
                    $fin .= ' cero';
                 elseif ($s == '1')
                    $fin .= $fem ? ' una' : ' un';
                 else
                    $fin .= ' ' . $matuni[$s];
              }
           }else
              $fin = '';
           if ((int)$ent === 0) return 'Cero ' . $fin;
           $tex = '';
           $sub = 0;
           $mils = 0;
           $neutro = false;
           while ( ($num = substr($ent, -3)) != '   ') {
              $ent = substr($ent, 0, -3);
              if (++$sub < 3 and $fem) {
                 $matuni[1] = 'una';
                 $subcent = 'as';
              }else{
                 $matuni[1] = $neutro ? 'un' : 'uno';
                 $subcent = 'os';
              }
              $t = '';
              $n2 = substr($num, 1);
              if ($n2 == '00') {
              }elseif ($n2 < 21)
                 $t = ' ' . $matuni[(int)$n2];
              elseif ($n2 < 30) {
                 $n3 = $num[2];
                 if ($n3 != 0) $t = 'i' . $matuni[$n3];
                 $n2 = $num[1];
                 $t = ' ' . $matdec[$n2] . $t;
              }else{
                 $n3 = $num[2];
                 if ($n3 != 0) $t = ' y ' . $matuni[$n3];
                 $n2 = $num[1];
                 $t = ' ' . $matdec[$n2] . $t;
              }
              $n = $num[0];
              if ($n == 1) {
                 $t = ' ciento' . $t;
              }elseif ($n == 5){
                 $t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t;
              }elseif ($n != 0){
                 $t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t;
              }
              if ($sub == 1) {
              }elseif (! isset($matsub[$sub])) {
                 if ($num == 1) {
                    $t = ' mil';
                 }elseif ($num > 1){
                    $t .= ' mil';
                 }
              }elseif ($num == 1) {
                 $t .= ' ' . $matsub[$sub] . '�n';
              }elseif ($num > 1){
                 $t .= ' ' . $matsub[$sub] . 'ones';
              }
              if ($num == '000') $mils ++;
              elseif ($mils != 0) {
                 if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub];
                 $mils = 0;
              }
              $neutro = true;
              $tex = $t . $tex;
           }
           $tex = $neg . substr($tex, 1) . $fin;
           //Zi hack --> return ucfirst($tex);
           // $end_num=ucfirst($tex).' pesos '.$float[1].'/100 M.N.';
           $end_num=ucfirst($tex).' '.$_SESSION['DESCRIMONEDA'];
           return $end_num;
        }

    }



?>