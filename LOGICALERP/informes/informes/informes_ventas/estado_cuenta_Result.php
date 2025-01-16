<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  include_once('../../ClassFuncionesInforme.php');

  /**
   * @class InformeEstadoCuenta
   */
  class InformeEstadoCuenta extends FuncionesInforme{
    public $IMPRIME_HTML                = '';
    public $IMPRIME_PDF                 = '';
    public $MyInformeFiltroFechaInicio  = '';
    public $MyInformeFiltroFechaFinal   = '';
    public $sucursal                    = '';
    public $cliente                     = '';
    public $tipo_fecha_informe          = '';
    public $tipo_informe                = '';
    public $sqlCheckbox                 = '';
    public $cuenta                      = '';
    public $GUARDAR_PDF                 = '';
    public $mysql                       = '';
    public $id_empresa                  = '';
    public $customWhere                 = '';
    public $arrayDoc                    = array();

    /**
     * [__construct]
     * @param str $IMPRIME_HTML                 Generar en HTML
     * @param str $IMPRIME_PDF                  Generar en PDF
     * @param dat $MyInformeFiltroFechaInicio   Fecha inicial del informe
     * @param dat $MyInformeFiltroFechaFinal    Fecha final del informe
     * @param int $sucursal                     Filtro por sucursal
     * @param int $cliente                      Filtro por cliente
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$cliente,$tipo_fecha_informe,$tipo_informe,$sqlCheckbox,$cuenta,$GUARDAR_PDF,$mysql){
      $this->IMPRIME_HTML               = $IMPRIME_HTML;
      $this->IMPRIME_PDF                = $IMPRIME_PDF;
      $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
      $this->sucursal                   = $sucursal;
      $this->cliente                    = $cliente;
      $this->tipo_fecha_informe         = $tipo_fecha_informe;
      $this->tipo_informe               = $tipo_informe;
      $this->sqlCheckbox                = $sqlCheckbox;
      $this->cuenta                     = $cuenta;
      $this->GUARDAR_PDF                = $GUARDAR_PDF;
      $this->mysql                      = $mysql;
      $this->id_empresa                 = $_SESSION['EMPRESA'];
      $this->id_sucursal                = $_SESSION['SUCURSAL'];
    }

    /**
     * @method showError Mostrar alerta si se presenta un error
     * @param  str $mensaje Mensaje de error a mostrar
     */
    public function showError($mensaje){
      echo "<script>alert('Error\n $mensaje');</script>" . $mensaje;
      exit;
    }

    /**
     * @method getCustomFiltres armar los filtros a aplicar al informe
     */
    public function getCustomFiltres(){

      //FILTRO SUCURSAL
      if($this->sucursal != "" && $this->sucursal != "global"){
        $this->whereSucursal = " AND VF.id_sucursal = '$this->sucursal'";
      }

      //FILTRO FECHAS
      if($this->tipo_fecha_informe == 'corte'){
        $this->whereFecha  = "AND A.fecha <= '$this->MyInformeFiltroFechaFinal' ";
      }
      else if($this->tipo_fecha_informe == 'rango_fechas'){
        $this->whereFecha  = "AND A.fecha BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'";
      }

      //FILTRO CLIENTE
      if($this->cliente != ''){
        $this->whereTercero = "AND VF.id_cliente = $this->cliente";
      }

      //FILTRO VENCIMIENTO POR EDADES DE VENCIMIENTO
      if($this->sqlCheckbox != ''){
        $this->wherePlazos = "AND ($this->sqlCheckbox)";
      }

      //FILTRO CUENTAS
      if($this->cuenta != ''){
        $this->cuenta = substr($this->cuenta, 0, -1);
        $arrayCuenta  = explode(",", $this->cuenta);
        foreach($arrayCuenta as $key => $cuenta_pago){
          $this->whereCuentas .= "A.codigo_cuenta = $cuenta_pago OR ";
        }

        $this->whereCuentas = substr($this->whereCuentas, 0, -3);
      }
      else{
        $sqlCuentasPago   = "SELECT cuenta FROM configuracion_cuentas_pago WHERE id_empresa = $this->id_empresa AND activo = 1 AND tipo = 'Venta' AND estado = 'Credito'";
        $queryCuentasPago = $this->mysql->query($sqlCuentasPago,$this->mysql->link);
        while($rowCuenta = $this->mysql->fetch_array($queryCuentasPago)){
          $this->whereCuentas .= " OR A.codigo_cuenta = $rowCuenta[cuenta]";
        }

        $this->whereCuentas = substr($this->whereCuentas, 3);
      }
    }

    /**
     * @method getDocumentoInfo consultar la informacion de las facturas de compra
     */
    public function getDocumentoInfo(){

      //--------------------- DATOS CABECERA DE LA FACTURA -------------------//
      $sqlEstadoCuenta = "SELECT
                            T.telefono1,
                            T.celular1,
                            VF.cuenta_pago AS codigo_cuenta,
                            DATEDIFF('$this->MyInformeFiltroFechaFinal',VF.fecha_vencimiento) AS dias,
                            VF.id,
                            VF.id_cliente,
                            VF.nit,
                            VF.cliente,
                            VF.fecha_inicio,
                            VF.fecha_vencimiento,
                            VF.numero_factura_completo,
                            VF.codigo_centro_costo AS codigo_ccos,
                            VF.centro_costo AS nombre_ccos,
                            VF.sucursal,
                            SUM(A.debe - A.haber) AS saldo
                          FROM
                            asientos_colgaap AS A
                          INNER JOIN ventas_facturas AS VF ON(
                            A.id_documento_cruce = VF.id
                            AND VF.activo = 1
                            AND VF.estado = 1
                            AND VF.id_empresa = '$this->id_empresa'
                            $this->whereSucursal
                            $this->whereTercero
                            $this->wherePlazos
                          )
                          LEFT JOIN terceros AS T ON(
                            VF.id_cliente = T.id
                            AND T.id_empresa = $this->id_empresa
                          )
                          WHERE
                            A.activo = 1
                            $this->whereFecha
                            AND ($this->whereCuentas)
                            AND A.tipo_documento_cruce = 'FV'
                            AND A.id_empresa = $this->id_empresa
                          GROUP BY
                            A.id_documento_cruce
                          HAVING
                            saldo > 0
                          ORDER BY
                            VF.cliente,VF.fecha_vencimiento ASC";
      $this->query_estado_cuenta = $this->mysql->query($sqlEstadoCuenta,$this->mysql->link);
    }

    /**
     * getHtmlPdf armar el informe para la vista en la app y pdf
     * @return str body informe generado
     */
    public function getHtmlPdf(){
      if($this->mysql->num_rows($this->query_estado_cuenta) > 0){
        $style   = 'color:#FFF';
        $tercero = 0;

        //CONSTRUCCION DEL INFORME
        while($rowFacturas = mysql_fetch_array($this->query_estado_cuenta)){
          $dias      = $rowFacturas['dias'];
          $telefono  = ' Telefono: ' . $rowFacturas['telefono1'];
          $telefono .= ($rowFacturas['telefono1'] != '' && $rowFacturas['celular1'] != '')? ' - Celular: ' . $rowFacturas['celular1'] : $rowFacturas['celular1'];

          $rowFacturas['saldo'] = $rowFacturas['saldo'];
          $porVencer            = ($dias < 1)? $rowFacturas['saldo'] : '&nbsp;';
          $unoAtreinta          = ($dias > 0 && $dias <= 30)? $rowFacturas['saldo'] : '&nbsp;';
          $treintayunoAsesenta  = ($dias > 30 && $dias <= 60)? $rowFacturas['saldo'] : '&nbsp;';
          $sesentayunoAnoventa  = ($dias > 60 && $dias <= 90)? $rowFacturas['saldo'] : '&nbsp;';
          $masDenoventa         = ($dias > 90)? $rowFacturas['saldo'] : '&nbsp;';

          $style  = ($style != '')? '' : 'background:#f7f7f7;';

          if($tercero == 0){
            if($this->tipo_informe != 'totalizado_edades'){
              $headTable = '<tr class="total" style="border:1px solid #999;">
                              <td style="text-align: center;" colspan="12"><b>' . $rowFacturas['cliente'] . ' ' . $telefono . '</b></td>
                            </tr>';
            }
          }

          $tercero++;

          $bodyTable .= ($this->tipo_informe == 'detallado')?  '<tr class="detail" style="color: #0a0318">
                                                                  <td style="'.$style.' border-left:1px solid #999;text-align:center;">'.$rowFacturas['sucursal'].'</td>
                                                                  <td style="'.$style.' text-align:center;">'.$rowFacturas['codigo_cuenta'].'</td>
                                                                  <td style="'.$style.' text-align:center;">'.$rowFacturas['numero_factura_completo'].'</td>
                                                                  <td style="'.$style.' text-align:center;">'.$rowFacturas['fecha_inicio'].'</td>
                                                                  <td style="'.$style.' text-align:center;">'.$rowFacturas['fecha_vencimiento'].'</td>
                                                                  <td style="'.$style.' text-align:center;">'.$dias.'</td>
                                                                  <td style="'.$style.' text-align:right;">'.number_format($rowFacturas['saldo'], $_SESSION['DECIMALESMONEDA'], ".", ",").'</td>
                                                                  <td style="'.$style.' text-align:right;">'.number_format($porVencer, $_SESSION['DECIMALESMONEDA'], ".", ",").'</td>
                                                                  <td style="'.$style.' text-align:right;">'.number_format($unoAtreinta, $_SESSION['DECIMALESMONEDA'], ".", ",").'</td>
                                                                  <td style="'.$style.' text-align:right;">'.number_format($treintayunoAsesenta, $_SESSION['DECIMALESMONEDA'], ".", ",").'</td>
                                                                  <td style="'.$style.' text-align:right;">'.number_format($sesentayunoAnoventa, $_SESSION['DECIMALESMONEDA'], ".", ",").'</td>
                                                                  <td style="'.$style.' border-right:1px solid #999;text-align:right;">'.number_format($masDenoventa, $_SESSION['DECIMALESMONEDA'], ".", ",").'</td>
                                                                </tr>' : '';

          $acumuladoSaldo                    += $rowFacturas['saldo'];
          $acumuladoporVencer                += ($dias < 1)? $rowFacturas['saldo'] : '';
          $acumuladounoAtreinta              += ($dias > 0 && $dias <= 30)? $rowFacturas['saldo'] : 0;
          $acumuladotreintayunoAsesenta      += ($dias > 30 && $dias <= 60)? $rowFacturas['saldo'] : 0;
          $acumuladosesentayunoAnoventa      += ($dias > 60 && $dias <= 90)? $rowFacturas['saldo'] : 0;
          $acumuladomasDenoventa             += ($dias > 90)? $rowFacturas['saldo'] : 0;
          $acumuladoporVencerTotal           += ($dias < 1)? $rowFacturas['saldo'] : '';
          $acumuladounoAtreintaTotal         += ($dias > 0 && $dias <= 30)? $rowFacturas['saldo'] : 0;
          $acumuladotreintayunoAsesentaTotal += ($dias > 30 && $dias <= 60)? $rowFacturas['saldo'] : 0;
          $acumuladosesentayunoAnoventaTotal += ($dias > 60 && $dias <= 90)? $rowFacturas['saldo'] : 0;
          $acumuladomasDenoventaTotal        += ($dias > 90)? $rowFacturas['saldo'] : 0;
        }

        //CABECERA DEL INFORME
        $headTable .=  '<tr class="thead" style="border: 1px solid #999; color: #f7f7f7;">
                          <td style="text-align:center;">SUCURSAL</td>
                          <td style="text-align:center;">CUENTA</td>
                          <td style="text-align:center;">NUMERO</td>
                          <td style="text-align:center;">FECHA</td>
                          <td style="text-align:center;">VENCIMIENTO</td>
                          <td style="text-align:center;">DIAS</td>
                          <td style="text-align:center;">SALDO</td>
                          <td style="text-align:center;">POR VENCER</td>
                          <td style="text-align:center;">1-30</td>
                          <td style="text-align:center;">31-60</td>
                          <td style="text-align:center;">61-90</td>
                          <td style="text-align:center;">MAS 90</td>
                        </tr>';

        //PIE DE PAGINA DEL INFORME
        $bodyTable .=  '<tr class="total" style="border:1px solid #999;">
                          <td colspan="7"><b>TOTALES</b></td>
                          <td style="text-align:right;"><b>'.number_format($acumuladoporVencerTotal, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                          <td style="text-align:right;"><b>'.number_format($acumuladounoAtreintaTotal, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                          <td style="text-align:right;"><b>'.number_format($acumuladotreintayunoAsesentaTotal, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                          <td style="text-align:right;"><b>'.number_format($acumuladosesentayunoAnoventaTotal, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                          <td style="text-align:right;"><b>'.number_format($acumuladomasDenoventaTotal, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                        </tr>';

        //TOTAL CARTERA
        $totalCartera = $acumuladoporVencerTotal + $acumuladounoAtreintaTotal + $acumuladotreintayunoAsesentaTotal + $acumuladosesentayunoAnoventaTotal + $acumuladomasDenoventaTotal;

        $bodyTable .=  '<tr>
                          <td colspan="12">&nbsp;</td>
                        </tr>
                        <tr class="total" style="border:1px solid #999;">
                          <td colspan="6" style="text-align:center; border:1px solid #999;"><b>TOTAL CARTERA</b></td>
                          <td colspan="6" style="text-align:center; border:1px solid #999;"><b>'.number_format($totalCartera, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                        </tr>';

        $texto = '<style>
                    @page {
                      margin-top    : 1cm;
                      margin-bottom : 2cm;
                      margin-left   : 0.1cm;
                      margin-right  : 0.1cm;
                    }
                    .tableInforme{
                      font-size       : 12px;
                      width           : 100%;
                      margin-top      : 20px;
                      border-collapse : collapse;
                    }
                    .tableInforme .thead{
                      height        : 25px;
                      background    : #EEE;
                      color         : #8E8E8E;
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      font-weight   : bold;
                      font-family   : arial,helvetica;
                    }
                    .tableInforme .total{
                      height        : 25px;
                      background    : #EEE;
                      color         : #8E8E8E;
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      font-weight   : bold;
                      font-family   : arial,helvetica;
                    }
                    .my_informe_Contenedor_Titulo_informe{
                      float         : left;
                      width         : 100%;
                      margin        : 0 0 10px 0;
                      font-size     : 11px;
                    }
                    .table{
                      font-size       : 12px;
                      width           : 100%;
                      border-collapse : collapse;
                      color           : #FFF;
                    }
                    .thead{
                      background  : #999;
                      font-weight : bold;
                      font-family : arial,helvetica;
                    }
                    .thead td {
                      height        : 30px;
                      background    : #999;
                      height        : 25px;
                      font-weight   : bold;
                      font-family   :arial,helvetica;
                      color         : #FFF;
                    }
                    .detail{
                      background  : #FFF;
                      font-family : arial,helvetica;
                    }
                    .detail td{
                      background    : #FFF;
                      height        : 25px;
                      font-family   :arial,helvetica;
                      color         : #000000;
                    }
                    .total{
                      background  : #EEE;
                      font-weight : bold;
                      font-family : arial,helvetica;
                    }
                    .total td{
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      background    : #EEE;
                      height        : 25px;
                      font-weight   : bold;
                      font-family   :arial,helvetica;
                      color         : #8E8E8E;
                    }
                  </style>
                  <div class="my_informe_Contenedor_Titulo_informe" style="float:left;">
                    <table class="tableInforme" style="width:100%; border-collapse:collapse;">
                      ' . $headTable . $bodyTable . '
                    </table>
                  </div>';

        $formato    = $this->cargaFormatoDocumento('EC',$this->id_empresa,$this->id_sucursal);
        $textoFinal = $this->reemplazarVariables($formato,$texto,$this->id_empresa,$this->id_sucursal,$this->cliente,'','','');
        $documento  = "Estado De Cuenta";

        if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
        if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
        if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
        if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=50;}
        if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}

        if($this->IMPRIME_PDF == 'true'){
          include_once("../../../../misc/MPDF54/mpdf.php");
          $mpdf = new mPDF(
                            'utf-8',      // mode - default ''
                            $HOJA,        // format - A4, for example, default ''
                            12,           // font size - default 0
                            '',           // default font family
                            $MI,          // margin left
                            $MD,          // margin right
                            $MS,          // margin top
                            $ML,          // margin bottom
                            10,           // margin header
                            50,           // margin footer
                            $ORIENTACION  // L - landscape, P - portrait
                          );
          $mpdf->useSubstitutions = false;
          $mpdf->packTableData = false;
          $mpdf->SetAutoPageBreak(true);
          $mpdf->SetTitle($documento);
          $mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
          $mpdf->SetDisplayMode( 'fullpage' );
          $mpdf->SetHeader("");
          $mpdf->WriteHTML(utf8_encode($textoFinal));

          //OUTPUT A ARCHIVO
          if($this->GUARDAR_PDF == 'true'){
            $serv = $_SERVER['DOCUMENT_ROOT'] . "/";
            $url  = $serv . 'ARCHIVOS_PROPIOS/empresa_' . $_SESSION['ID_HOST'] . '/archivos_temporales/';
            if(!file_exists($url)){
              mkdir($url);
            }

            $mpdf->Output($url . "Estado_Cuenta.pdf",'F');
          }
          //OUTPUT A VISTA
          else{
            $mpdf->Output($documento.".pdf",'I');
          }
        }
        if($this->IMPRIME_HTML == 'true'){
          echo $textoFinal;
        }
      } else{
                //CABECERA DEL INFORME
        $headTable .=  '<tr class="thead" style="border: 1px solid #999; color: #f7f7f7;">
                          <td style="text-align:center;">SUCURSAL</td>
                          <td style="text-align:center;">CUENTA</td>
                          <td style="text-align:center;">NUMERO</td>
                          <td style="text-align:center;">FECHA</td>
                          <td style="text-align:center;">VENCIMIENTO</td>
                          <td style="text-align:center;">DIAS</td>
                          <td style="text-align:center;">SALDO</td>
                          <td style="text-align:center;">POR VENCER</td>
                          <td style="text-align:center;">1-30</td>
                          <td style="text-align:center;">31-60</td>
                          <td style="text-align:center;">61-90</td>
                          <td style="text-align:center;">MAS 90</td>
                        </tr>';

        //PIE DE PAGINA DEL INFORME
        $bodyTable .=  '<tr class="total" style="border:1px solid #999;">
                          <td colspan="7"><b>TOTALES</b></td>
                          <td style="text-align:right;"><b>'.number_format(0, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                          <td style="text-align:right;"><b>'.number_format(0, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                          <td style="text-align:right;"><b>'.number_format(0, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                          <td style="text-align:right;"><b>'.number_format(0, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                          <td style="text-align:right;"><b>'.number_format(0, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                        </tr>';

        $bodyTable .=  '<tr>
                          <td colspan="12">&nbsp;</td>
                        </tr>
                        <tr class="total" style="border:1px solid #999;">
                          <td colspan="6" style="text-align:center; border:1px solid #999;"><b>TOTAL CARTERA</b></td>
                          <td colspan="6" style="text-align:center; border:1px solid #999;"><b>'.number_format(0, $_SESSION['DECIMALESMONEDA'], ".", ",").'</b></td>
                        </tr>';

        $texto = '<style>
                    @page {
                      margin-top    : 1cm;
                      margin-bottom : 2cm;
                      margin-left   : 0.1cm;
                      margin-right  : 0.1cm;
                    }
                    .tableInforme{
                      font-size       : 12px;
                      width           : 100%;
                      margin-top      : 20px;
                      border-collapse : collapse;
                    }
                    .tableInforme .thead{
                      height        : 25px;
                      background    : #EEE;
                      color         : #8E8E8E;
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      font-weight   : bold;
                      font-family   : arial,helvetica;
                    }
                    .tableInforme .total{
                      height        : 25px;
                      background    : #EEE;
                      color         : #8E8E8E;
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      font-weight   : bold;
                      font-family   : arial,helvetica;
                    }
                    .my_informe_Contenedor_Titulo_informe{
                      float         : left;
                      width         : 100%;
                      margin        : 0 0 10px 0;
                      font-size     : 11px;
                    }
                    .table{
                      font-size       : 12px;
                      width           : 100%;
                      border-collapse : collapse;
                      color           : #FFF;
                    }
                    .thead{
                      background  : #999;
                      font-weight : bold;
                      font-family : arial,helvetica;
                    }
                    .thead td {
                      height        : 30px;
                      background    : #999;
                      height        : 25px;
                      font-weight   : bold;
                      font-family   :arial,helvetica;
                      color         : #FFF;
                    }
                    .detail{
                      background  : #FFF;
                      font-family : arial,helvetica;
                    }
                    .detail td{
                      background    : #FFF;
                      height        : 25px;
                      font-family   :arial,helvetica;
                      color         : #000000;
                    }
                    .total{
                      background  : #EEE;
                      font-weight : bold;
                      font-family : arial,helvetica;
                    }
                    .total td{
                      border-top    : 1px solid #999;
                      border-bottom : 1px solid #999;
                      background    : #EEE;
                      height        : 25px;
                      font-weight   : bold;
                      font-family   :arial,helvetica;
                      color         : #8E8E8E;
                    }
                  </style>
                  <div class="my_informe_Contenedor_Titulo_informe" style="float:left;">
                    <table class="tableInforme" style="width:100%; border-collapse:collapse;">
                      ' . $headTable . $bodyTable . '
                    </table>
                  </div>';

                $formato    = $this->cargaFormatoDocumento('PYSEC',$this->id_empresa,$this->id_sucursal);
                $textoFinal = $this->reemplazarVariables($formato,$texto,$this->id_empresa,$this->id_sucursal,$this->cliente,'','','');
                $documento  = "Estado De Cuenta";
                
                if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER';}
                if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
                if(!isset($PDF_GUARDA)){$PDF_GUARDA = false;}
                if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=50;}
                if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}

                if($this->IMPRIME_PDF == 'true'){
                  include_once("../../../../misc/MPDF54/mpdf.php");
                  $mpdf = new mPDF(
                                    'utf-8',      // mode - default ''
                                    $HOJA,        // format - A4, for example, default ''
                                    12,           // font size - default 0
                                    '',           // default font family
                                    $MI,          // margin left
                                    $MD,          // margin right
                                    $MS,          // margin top
                                    $ML,          // margin bottom
                                    10,           // margin header
                                    50,           // margin footer
                                    $ORIENTACION  // L - landscape, P - portrait
                                  );
                  $mpdf->useSubstitutions = false;
                  $mpdf->packTableData = false;
                  $mpdf->SetAutoPageBreak(true);
                  $mpdf->SetTitle($documento);
                  $mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
                  $mpdf->SetDisplayMode( 'fullpage' );
                  $mpdf->SetHeader("");
                  $mpdf->WriteHTML(utf8_encode($textoFinal));
        
                  //OUTPUT A ARCHIVO
                  if($this->GUARDAR_PDF == 'true'){
                    $serv = $_SERVER['DOCUMENT_ROOT'] . "/";
                    $url  = $serv . 'ARCHIVOS_PROPIOS/empresa_' . $_SESSION['ID_HOST'] . '/archivos_temporales/';
                    if(!file_exists($url)){
                      mkdir($url);
                    }
        
                    $mpdf->Output($url . "Estado_Cuenta.pdf",'F');
                  }
                  //OUTPUT A VISTA
                  else{
                    $mpdf->Output($documento.".pdf",'I');
                  }
                }
            if($this->IMPRIME_HTML == 'true'){
              echo $textoFinal;
            } 
      }
    }
    /**
     * @method generate Generar el informe
     */
    public function generate(){
      $this->getCustomFiltres();
      $this->getDocumentoInfo();
      $this->getHtmlPdf();
    }
  }

  $objectInform = new InformeEstadoCuenta($IMPRIME_HTML,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$cliente,$tipo_fecha_informe,$tipo_informe,$sqlCheckbox,$cuenta,$GUARDAR_PDF,$mysql);
  $objectInform->generate();
?>
