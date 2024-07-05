<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();
  /**
   * @class InformeFacturasArchivosAdjuntos
   */
  class InformeFacturasArchivosAdjuntos{

    public $IMPRIME_HTML               = '';
    public $IMPRIME_XLS                = '';
    public $IMPRIME_PDF                = '';
    public $MyInformeFiltroFechaInicio = '';
    public $MyInformeFiltroFechaFinal  = '';
    public $sucursal                   = '';
    public $contenido                  = '';
    public $arraytercerosJSON          = '';
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
     * @param str $contenido          Filtro por mes y año
     * @param arr $arraytercerosJSON            Filtro por terceros
     * @param arr $arrayccosJSON                Filtro por centro de costos
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$contenido,$arraytercerosJSON,$mysql){
      $this->IMPRIME_HTML               = $IMPRIME_HTML;
      $this->IMPRIME_XLS                = $IMPRIME_XLS;
      $this->IMPRIME_PDF                = $IMPRIME_PDF;
      $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
      $this->sucursal                   = $sucursal;
      $this->contenido                  = $contenido;
      $this->arraytercerosJSON          = json_decode($arraytercerosJSON);
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
        $this->showError('Debe seleccionar el rango de fechas para el informe');
      }
      else if($this->MyInformeFiltroFechaFinal != '' && $this->MyInformeFiltroFechaInicio != ''){
        $whereFechas = " AND VF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'";
      }

      if($this->contenido == "conArchivos"){
        $whereContenido = " AND VFD.nombre IS NOT NULL";
      }elseif ($this->contenido == "sinArchivos"){
        $whereContenido = " AND VFD.nombre IS NULL";
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

      $this->customWhere = $whereFechas.$whereSucursal.$whereTerceros.$whereContenido;
    }

    /**
     * @method getDocumentoInfo consultar ls informacion de las requisiciones
     */
    public function getDocumentoInfo(){
      //-------------------- DATOS CABECERA DE LA FACTURA --------------------//
      $sql = "SELECT
                VF.id,
                VF.sucursal,
                VF.numero_factura_completo,
                VF.fecha_inicio,
                VF.nit,
                VF.cliente,
                CONCAT(VFD.nombre,'.',VFD.ext) AS archivo
              FROM
                ventas_facturas AS VF
              LEFT JOIN
                ventas_facturas_documentos AS VFD
              ON
                VF.id = VFD.id_factura_venta
              WHERE
                VF.activo = 1
              AND
                VF.estado = 1
              AND
                VF.id_empresa = $this->id_empresa
                $this->customWhere
              ORDER BY
                VF.fecha_inicio,VF.id ASC";

      $query = $this->mysql->query($sql,$this->mysql->link);

      while($row = $this->mysql->fetch_array($query)){
        $this->arrayDoc[] = array(
                                              'sucursal'                => $row['sucursal'],
                                              'numero_factura_completo' => $row['numero_factura_completo'],
                                              'fecha_inicio'            => $row['fecha_inicio'],
                                              'nit'                     => $row['nit'],
                                              'cliente'                 => $row['cliente'],
                                              'archivo'                 => $row['archivo']
                                            );
      }
    }

    /**
     * getExcel armar el informe para excel
     * @return str body informe generado
     */
    public function getExcel(){
      //CUERPO DEL INFORME
      $enableStyle = "true";
      foreach($this->arrayDoc as $id_factura => $result){
        if($enableStyle == "true"){
          $style = "style='background-color: #ffffff;'";
        } else{
          $style = "style='background-color: #d7d7d7;'";
        }

        $bodyTable .=  "<tr $style>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:10%;'>$result[sucursal]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:20%;'>$result[numero_factura_completo]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:10%;'>$result[fecha_inicio]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:20%;'>$result[nit]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:20%;'>$result[cliente]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:20%;'>$result[archivo]</td>
                        </tr>";

        if($enableStyle == "true"){
          $enableStyle = "false";
        } else{
          $enableStyle = "true";
        }
      }

      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=Informe_Facturas_De_Venta_Archivos_Adjuntos_" . date("Y_m_d").".xls");
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
          <td><b>Informe Facturas De Venta Archivos Adjuntos</td>
        </tr>
        <tr>
          <td>Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td>
        </tr>
      </table>
      <table>
        <tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
          <td style='text-align:center; padding-left:4px; width:10%;'><b>SUCURSAL</b></td>
          <td style='text-align:center; padding-left:4px; width:20%;'><b>NUMERO FACTURA</b></td>
          <td style='text-align:center; padding-left:4px; width:10%;'><b>FECHA</b></td>
          <td style='text-align:center; padding-left:4px; width:20%;'><b>NIT</b></td>
          <td style='text-align:center; padding-left:4px; width:20%;'><b>CLIENTE</b></td>
          <td style='text-align:center; padding-left:4px; width:20%;'><b>ARCHIVO</b></td>
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
      //CABECERA DEL INFORME
      $headTable .=  "<tr class='thead' style='color: #f7f7f7;'>
                        <td style='text-align:center; padding-left:4px; width:10%;'><b>SUCURSAL</b></td>
                        <td style='text-align:center; padding-left:4px; width:20%;'><b>NUMERO FACTURA</b></td>
                        <td style='text-align:center; padding-left:4px; width:10%;'><b>FECHA</b></td>
                        <td style='text-align:center; padding-left:4px; width:20%;'><b>NIT</b></td>
                        <td style='text-align:center; padding-left:4px; width:20%;'><b>CLIENTE</b></td>
                        <td style='text-align:center; padding-left:4px; width:20%;'><b>ARCHIVO</b></td>
                      </tr>";

      //CUERPO DEL INFORME
      $enableStyle = "true";
      foreach($this->arrayDoc as $id_factura => $result){
        if($enableStyle == "true"){
          $style = "style='background-color: #ffffff;'";
        } else{
          $style = "style='background-color: #d7d7d7;'";
        }

        $bodyTable .=  "<tr $style>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:10%;'>$result[sucursal]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:20%;'>$result[numero_factura_completo]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:10%;'>$result[fecha_inicio]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:20%;'>$result[nit]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:20%;'>$result[cliente]</td>
                          <td style='text-align:center; font-size:11px; padding-left:4px; width:20%;'>$result[archivo]</td>
                        </tr>";

        if($enableStyle == "true"){
          $enableStyle = "false";
        } else{
          $enableStyle = "true";
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
                <tr><td style="font-size:13px;"><b>Informe Facturas De Ventas Archivos Adjuntos</b><br> <?php echo $subtitulo_cabecera; ?></td></tr>
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
        $documento = "Informe_Facturas_De_Venta_Archivos_Adjuntos_" . date('Y_m_d');
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
        $mpdf->SetTitle('Informe Facturas Archivos Adjuntos');
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

  $objectInform = new InformeFacturasArchivosAdjuntos($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$contenido,$arraytercerosJSON,$mysql);
  $objectInform->generate();
?>
