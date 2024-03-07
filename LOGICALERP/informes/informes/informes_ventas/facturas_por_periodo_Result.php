<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();
  /**
   * @class InformeFacturasPorPeriodo
   */
  class InformeFacturasPorPeriodo{

    public $IMPRIME_HTML               = '';
    public $IMPRIME_XLS                = '';
    public $IMPRIME_PDF                = '';
    public $MyInformeFiltroFechaInicio = '';
    public $MyInformeFiltroFechaFinal  = '';
    public $sucursal                   = '';
    public $detallado_principal        = '';
    public $arraytercerosJSON          = '';
    public $arrayccosJSON              = '';
    public $mysql                      = '';
    public $id_empresa                 = '';
    public $customWhere                = '';
    public $arrayDoc                   = array();

    /**
     * [__construct]
     * @param str $IMPRIME_HTML                 Generar en HTML
     * @param str $IMPRIME_XLS                  Generar en EXCEL
     * @param str $IMPRIME_PDF                  Generar en PDF
     * @param dat $MyInformeFiltroFechaInicio   Fecha inicial del informe
     * @param dat $MyInformeFiltroFechaFinal    Fecha final del informe
     * @param int $sucursal                     Filtro por sucursal
     * @param str $detallado_principal          Filtro por mes y año
     * @param arr $arraytercerosJSON            Filtro por terceros
     * @param arr $arrayccosJSON                Filtro por centro de costos
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$detallado_principal,$arraytercerosJSON,$arrayccosJSON,$mysql){
      $this->IMPRIME_HTML               = $IMPRIME_HTML;
      $this->IMPRIME_XLS                = $IMPRIME_XLS;
      $this->IMPRIME_PDF                = $IMPRIME_PDF;
      $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
      $this->sucursal                   = $sucursal;
      $this->detallado_principal        = $detallado_principal;
      $this->arraytercerosJSON          = json_decode($arraytercerosJSON);
      $this->arrayccosJSON              = json_decode($arrayccosJSON);
      $this->mysql                      = $mysql;
      $this->id_empresa                 = $_SESSION['EMPRESA'];
    }

    /**
     * @method showError Mostrar alerta si se presenta un error
     * @param  str $mensaje Mensaje de error a mostrar
     */
    public function showError($mensaje){
      echo '<script>alert("Error\n'.$mensaje.'");</script>'.$mensaje;
      exit;
    }

    /**
     * @method getCustomFiltres armar los filtros a aplicar al informe
     */
    public function getCustomFiltres(){
      if($this->MyInformeFiltroFechaFinal == '' || $this->MyInformeFiltroFechaInicio == ''){
        $this->showError('Debe seleccionar el rango de años para el informe');
      }
      else if($this->MyInformeFiltroFechaFinal != '' && $this->MyInformeFiltroFechaInicio != ''){
        $whereFechas = " AND YEAR(VF.fecha_inicio) BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'";
      }

      if($this->sucursal != '' && $this->sucursal != 'global'){
        $whereSucursal = " AND VF.id_sucursal = '$this->sucursal'";
      }

      if(!empty($this->arraytercerosJSON)){
        foreach ($this->arraytercerosJSON as $indice => $id_tercero){
          $terceros .= ($terceros == "")? "VF.id_cliente = '$id_tercero'" : " OR VF.id_cliente = '$id_tercero'";
        }
        $whereTerceros .= " AND ($terceros)";
      }

      if(!empty($this->arrayccosJSON)){
        foreach($this->arrayccosJSON as $indice => $codigo_centro_costo){
          $ccos .= ($ccos == "")? "VF.id_centro_costo = '$codigo_centro_costo'" : " OR VF.id_centro_costo = '$codigo_centro_costo'";
        }
        $whereCcos .= "AND ($ccos)";
      }

      $this->customWhere = $whereFechas.$whereSucursal.$whereTerceros.$whereCcos;
    }

    /**
     * @method getDocumentoInfo consultar ls informacion de las requisiciones
     */
    public function getDocumentoInfo(){
      //-------------------- DATOS CABECERA DE LA FACTURA --------------------//
      $sql = "SELECT
                VF.id_cliente,
              	VF.cliente,
                VF.id_centro_costo,
              	VF.centro_costo,
              	MONTH(VF.fecha_inicio) AS month,
                YEAR(VF.fecha_inicio) AS year,
                SUM(VF.total_factura) AS total_factura
              FROM
              	ventas_facturas AS VF
              WHERE
              	VF.activo = 1
              AND
                (VF.estado = 1 OR VF.estado=2 OR VF.estado = 3)
              AND
                VF.id_saldo_inicial = 0
              AND
                VF.id_empresa = $this->id_empresa
                $this->customWhere
              GROUP BY
	              VF.centro_costo,MONTH(VF.fecha_inicio),YEAR(VF.fecha_inicio)
              ORDER BY
                VF.fecha_inicio ASC";
      $query = $this->mysql->query($sql,$this->mysql->link);

      while($row = $this->mysql->fetch_array($query)){
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['total_factura'] += $row['total_factura'];
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']] += array(
                                                                                              'cliente'       => $row['cliente'],
                                                                                              'centro_costo'  => $row['centro_costo']
                                                                                           );
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['enero']      += ($row['month'] == '1')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['febrero']    += ($row['month'] == '2')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['marzo']      += ($row['month'] == '3')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['abril']      += ($row['month'] == '4')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['mayo']       += ($row['month'] == '5')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['junio']      += ($row['month'] == '6')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['julio']      += ($row['month'] == '7')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['agosto']     += ($row['month'] == '8')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['septiembre'] += ($row['month'] == '9')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['octubre']    += ($row['month'] == '10')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['noviembre']  += ($row['month'] == '11')? $row['total_factura'] : '0';
        $this->arrayDoc[$row['id_cliente']][$row['id_centro_costo']][$row['year']]['diciembre']  += ($row['month'] == '12')? $row['total_factura'] : '0';
      }
    }

    /**
     * getExcel armar el informe para excel
     * @return str body informe generado
     */
    public function getExcel(){
      if($this->detallado_principal == "year"){
        $width = "width:25%;";
      }

      //CUERPO DEL INFORME
      $enableStyle = "true";
      foreach($this->arrayDoc as $id_cliente => $arrayCliente){
        foreach($arrayCliente as $id_ccos => $arrayCcos){
          foreach($arrayCcos as $year => $result){
            if($enableStyle == "true"){
              $style = "style='background-color: #ffffff;'";
            } else{
              $style = "style='background-color: #d7d7d7;'";
            }

            $bodyTable .=  "<tr $style>
                              <td style='text-align:center; font-size:11px; $width padding-left: 4px;'>$result[cliente]</td>
                              <td style='text-align:center; font-size:11px; $width'>$result[centro_costo]</td>";

            if($this->detallado_principal == "monthYear"){
              $bodyTable .=  "<td style='text-align:right;  font-size:11px;'>".round($result['enero'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['febrero'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['marzo'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['abril'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['mayo'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['junio'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['julio'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['agosto'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['septiembre'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['octubre'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['noviembre'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['diciembre'],$_SESSION['DECIMALESMONEDA'])."</td>";
            }

            $bodyTable .=    "<td style='text-align:right;  font-size:11px; $width'>".round($result['total_factura'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px; $width padding-right: 4px;'>$year</td>
                            </tr>";
          }

          if($enableStyle == "true"){
            $enableStyle = "false";
          } else{
            $enableStyle = "true";
          }
        }
      }

      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=Informe_Facturas_De_Venta_Por_Periodo_".date("Y_m_d").".xls");
      header("Pragma: no-cache");
      header("Expires: 0");

      ?>
      <table>
        <tr>
          <td><b><?php echo $_SESSION['NOMBREEMPRESA']; ?></b></td>
        </tr>
        <tr>
          <td><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td>
        </tr>
        <tr>
          <td><b>Informe Facturas De Venta Por Periodo</td>
        </tr>
        <tr>
          <td>Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td>
        </tr>
      </table>
      <table>
        <tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
          <td style='text-align:center; <?php echo $width; ?>'><b>CLIENTE</b></td>
          <td style='text-align:center; <?php echo $width; ?>'><b>CENTRO DE COSTOS</b></td>
          <?php if($this->detallado_principal == "monthYear"){ ?>
            <td style='text-align:center;'><b>ENERO</b></td>
            <td style='text-align:center;'><b>FEBRERO</b></td>
            <td style='text-align:center;'><b>MARZO</b></td>
            <td style='text-align:center;'><b>ABRIL</b></td>
            <td style='text-align:center;'><b>MAYO</b></td>
            <td style='text-align:center;'><b>JUNIO</b></td>
            <td style='text-align:center;'><b>JULIO</b></td>
            <td style='text-align:center;'><b>AGOSTO</b></td>
            <td style='text-align:center;'><b>SEPTIEMBRE</b></td>
            <td style='text-align:center;'><b>OCTUBRE</b></td>
            <td style='text-align:center;'><b>NOVIEMBRE</b></td>
            <td style='text-align:center;'><b>DICIEMBRE</b></td>
          <?php } ?>
          <td style='text-align:center; <?php echo $width; ?>'><b>TOTAL VENTA</b></td>
          <td style='text-align:center; <?php echo $width; ?>'><b>A&Ntilde;O</b></td>
        </tr>
        <?php echo $bodyTable; ?>
      </table>
      <?php
    }

    /**
     * getHtmlPdf armar el informe para la vista en la app y pdf
     * @return str body informe generado
     */
    public function getHtmlPdf(){
      if($this->detallado_principal == "year"){
        $width = "width:25%;";
      }

      //CABECERA DEL INFORME
      $headTable .=  "<tr class='thead' style='color: #f7f7f7;'>
                        <td style='text-align:center; $width'><b>CLIENTE</b></td>
                        <td style='text-align:center; $width'><b>CENTRO DE COSTOS</b></td>";

      if($this->detallado_principal == "monthYear"){
        $headTable .=  "<td style='text-align:center;'><b>ENERO</b></td>
                        <td style='text-align:center;'><b>FEBRERO</b></td>
                        <td style='text-align:center;'><b>MARZO</b></td>
                        <td style='text-align:center;'><b>ABRIL</b></td>
                        <td style='text-align:center;'><b>MAYO</b></td>
                        <td style='text-align:center;'><b>JUNIO</b></td>
                        <td style='text-align:center;'><b>JULIO</b></td>
                        <td style='text-align:center;'><b>AGOSTO</b></td>
                        <td style='text-align:center;'><b>SEPTIEMBRE</b></td>
                        <td style='text-align:center;'><b>OCTUBRE</b></td>
                        <td style='text-align:center;'><b>NOVIEMBRE</b></td>
                        <td style='text-align:center;'><b>DICIEMBRE</b></td>";
      }

      $headTable .=    "<td style='text-align:center; $width'><b>TOTAL VENTA</b></td>
                        <td style='text-align:center; $width'><b>A&Ntilde;O</b></td>
                      </tr>";

      //CUERPO DEL INFORME
      $enableStyle = "true";
      foreach($this->arrayDoc as $id_cliente => $arrayCliente){
        foreach($arrayCliente as $id_ccos => $arrayCcos){
          foreach($arrayCcos as $year => $result){
            if($enableStyle == "true"){
              $style = "style='background-color: #ffffff;'";
            } else{
              $style = "style='background-color: #d7d7d7;'";
            }

            $bodyTable .=  "<tr $style>
                              <td style='text-align:center; font-size:11px; $width padding-left: 4px;'>$result[cliente]</td>
                              <td style='text-align:center; font-size:11px; $width'>$result[centro_costo]</td>";

            if($this->detallado_principal == "monthYear"){
              $bodyTable .=  "<td style='text-align:right;  font-size:11px;'>".round($result['enero'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['febrero'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['marzo'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['abril'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['mayo'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['junio'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['julio'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['agosto'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['septiembre'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['octubre'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['noviembre'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px;'>".round($result['diciembre'],$_SESSION['DECIMALESMONEDA'])."</td>";
            }

            $bodyTable .=    "<td style='text-align:right;  font-size:11px; $width'>".round($result['total_factura'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px; $width padding-right: 4px;'>$year</td>
                            </tr>";
          }

          if($enableStyle == "true"){
            $enableStyle = "false";
          } else{
            $enableStyle = "true";
          }
        }
      }
      ?>
      <style>
        .tableInforme{
          font-size       : 12px;
          width           : 100%;
          margin-top      : 20px;
          border-collapse : collapse;
        }
        .tableInforme .thead td{
          color : #FFF;
        }
        .tableInforme .thead{
          height      : 25px;
          background  : #999;
          height      : 25px;
          font-size   : 12px;
          color       : #FFF;
          font-weight : bold;
        }
        .tableInforme .total{
          height        : 25px;
          background    : #EEE;
          font-weight   : bold;
          color         : #8E8E8E;
          border-top    : 1px solid #999;
          border-bottom : 1px solid #999;
        }
        .my_informe_Contenedor_Titulo_informe{
          float         :	left;
          width         :	100%;
          margin        :	0 0 10px 0;
          font-size     :	11px;
        }
        .my_informe_Contenedor_Titulo_informe_label{
          float       : left;
          width       : 130px;
          font-weight : bold;
        }
        .my_informe_Contenedor_Titulo_informe_detalle{
          float         :	left;
          width         :	210px;
          white-space   : nowrap;
          overflow      : hidden;
          text-overflow : ellipsis;
        }
        .my_informe_Contenedor_Titulo_informe_Empresa{
          float         : left;
          width         : 100%;
          font-size     : 16px;
          font-weight   :bold;
        }
        .table{
          font-size       : 12px;
          width           : 100%;
          border-collapse : collapse;
          color           : #FFF;
        }
        .table thead{
          background : #999;
        }
        .table thead td {
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
          height        : 25px;
          font-weight   : bold;
          color         : #8E8E8E;
        }
      </style>
      <body>
        <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
          <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center;margin-bottom:15px;">
              <table align="center" style="text-align:center;" >
                <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                <tr><td style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                <tr><td style="font-size:13px;"><b>Informe Facturas De Ventas Por Periodo</b><br> <?php echo $subtitulo_cabecera; ?></td></tr>
                <tr><td style="font-size:11px;">Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td></tr>
              </table>
              <table class="tableInforme" style="width:1015px;border-collapse:collapse;">
                <?php echo $headTable.$bodyTable; ?>
              </table>
            </div>
          </div>
        </div>
        <br>
      </body>
      <?php
      $texto = ob_get_contents(); ob_end_clean();

      if($this->IMPRIME_PDF == 'true'){
        $documento = "Informe_Facturas_De_Venta_Por_Periodo_" . date('Y_m_d');
        if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER-L';}
        if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
        if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
        if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=10;}
        if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

        ob_clean();
        include("../../../../misc/MPDF54/mpdf.php");
        $mpdf = new mPDF(
                          'utf-8',      // mode - default ''
                          $HOJA,        // format - A4, for example, default ''
                          12,           // font size - default 0
                          '',           // default font family
                          $MI,          // margin_left
                          $MD,          // margin right
                          $MS,          // margin top
                          $ML,          // margin bottom
                          10,           // margin header
                          10,           // margin footer
                          $ORIENTACION  // L - landscape, P - portrait
                        );
        $mpdf->useSubstitutions = false;
        $mpdf->packTableData    = true;
        $mpdf->SetAutoPageBreak(TRUE, 15);
        $mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->SetTitle('Informe Facturas Por Periodo');
        $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
        $mpdf->WriteHTML(utf8_encode($texto));

        if($PDF_GUARDA == "true"){
          $mpdf->Output($documento.".pdf",'D');
        } else{
          $mpdf->Output($documento.".pdf",'I');
        }
      }
      else if($this->IMPRIME_HTML == 'true'){
        echo $texto;
      }
    }

    /**
     * @method generate Generar el informe
     */
    public function generate(){
      $this->getCustomFiltres();
      $this->getDocumentoInfo();
      if($this->IMPRIME_XLS == "true"){
        $this->getExcel();
      }
      else{
        $this->getHtmlPdf();
      }
    }
  }

  $objectInform = new InformeFacturasPorPeriodo($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$detallado_principal,$arraytercerosJSON,$arrayccosJSON,$mysql);
  $objectInform->generate();
?>
