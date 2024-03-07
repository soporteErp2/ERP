<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();
  /**
   * @class InformeComprobanteEgresoArchivosAdjuntos
   */
  class InformeComprobanteEgresoArchivosAdjuntos{

    public $IMPRIME_HTML               = '';
    public $IMPRIME_XLS                = '';
    public $IMPRIME_PDF                = '';
    public $MyInformeFiltroFechaInicio = '';
    public $MyInformeFiltroFechaFinal  = '';
    public $sucursal                   = '';
    public $contenido                  = '';
    public $arrayTercerosJSON          = '';
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
     * @param str $contenido                    Filtro por contenido
     * @param arr $arrayTercerosJSON            Filtro por terceros
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$contenido,$arrayTercerosJSON,$mysql){
      $this->IMPRIME_HTML               = $IMPRIME_HTML;
      $this->IMPRIME_XLS                = $IMPRIME_XLS;
      $this->IMPRIME_PDF                = $IMPRIME_PDF;
      $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
      $this->sucursal                   = $sucursal;
      $this->contenido                  = $contenido;
      $this->arrayTercerosJSON          = json_decode($arrayTercerosJSON);
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
        $this->showError('Debe Seleccionar las fechas del informe');
      }
      else if($this->MyInformeFiltroFechaFinal != '' && $this->MyInformeFiltroFechaInicio != ''){
        $whereFechas = " AND CE.fecha_inicial BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'";
      }

      if($this->sucursal != '' && $this->sucursal != 'global'){
        $whereSucursal = " AND CE.id_sucursal = '$this->sucursal'";
      }

      if($this->contenido != ''){
        if($this->contenido == "conArchivos"){
          $whereContenido = " AND CEAA.nombre_archivo IS NOT NULL";
        }
        else if($this->contenido == "sinArchivos"){
          $whereContenido = " AND CEAA.nombre_archivo IS NULL";
        }
      }

      if(!empty($this->arrayTercerosJSON)){
        foreach($this->arrayTercerosJSON as $indice => $id_tercero){
          $terceros .= ($terceros == "")? "CE.id_tercero = '$id_tercero'" : " OR CE.id_tercero = '$id_tercero'";
        }
        $whereTerceros .= " AND ($terceros)";
      }

      $this->customWhere = $whereFechas.$whereSucursal.$whereContenido.$whereTerceros;
    }

    /**
     * @method getDocumentoInfo consultar ls informacion de las requisiciones
     */
    public function getDocumentoInfo(){
      //--------------------- DATOS CABECERA DEL INFORME ---------------------//

      $sql = "SELECT
                CE.sucursal,
                CE.fecha_inicial,
                CE.nit_tercero,
                CE.tercero,
                CONCAT(CEAA.nombre_archivo,'.',CEAA.ext) AS archivo,
                CEAA.fecha_creacion,
                CEAA.nombre_tercero,
                CE.consecutivo
              FROM
                comprobante_egreso AS CE
              LEFT JOIN
                comprobante_egreso_archivos_adjuntos AS CEAA
              ON
                CE.id = CEAA.id_comprobante_egreso
              WHERE
                CE.activo = 1
              AND
                CE.estado = 1
              AND
                CE.id_empresa = $this->id_empresa
              $this->customWhere
              ORDER BY
                CE.id DESC";
      $query = $this->mysql->query($sql,$this->mysql->link);
      while($row = $this->mysql->fetch_array($query)){
        $this->arrayDoc[] = array(
                                              'sucursal'         => $row['sucursal'],
                                              'consecutivo'      => $row['consecutivo'],
                                              'fecha_inicial'    => $row['fecha_inicial'],
                                              'nit_tercero'      => $row['nit_tercero'],
                                              'tercero'          => $row['tercero'],
                                              'archivo'          => $row['archivo'],
                                              'fecha_creacion'   => $row['fecha_creacion']
                                            );
      }
    }

    /**
     * getExcel armar el informe para excel
     * @return str body informe generado
     */
    public function getExcel(){
      $style = "";

      foreach($this->arrayDoc as $fila => $result){
        //CUERPO DEL INFORME
        $bodyTable .=  "<tr style='height:20px; $style'>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[sucursal]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[consecutivo]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[fecha_inicial]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[nit_tercero]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[tercero]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[archivo]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[fecha_creacion]</td>
                        </tr>";

        if($style == "background-color:#d0c4c4;"){
          $style = "";
        }
        else{
          $style = "background-color:#d0c4c4;";
        }
      }
      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=Informe_Comprobante_Egreso_Archivos_Adjuntos_".date("Y_m_d").".xls");
      header("Pragma: no-cache");
      header("Expires: 0");
      ?>
      <table>
        <tr>
          <td style="text-align:center;" colspan="7"><b><?php echo $_SESSION['NOMBREEMPRESA']; ?></b></td>
        </tr>
        <tr>
          <td style="text-align:center;" colspan="7"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td>
        </tr>
        <tr>
          <td style="text-align:center;" colspan="7"><b>Informe Comprobante Egreso Archivos Adjuntos</td>
        </tr>
        <tr>
          <td style="text-align:center;" colspan="7">Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td>
        </tr>
      </table>
      <table>
        <tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
          <td style='width:70px;text-align:center;'><b>SUCURSAL</b></td>
          <td style='width:70px;text-align:center;'><b>CONSECUTIVO</b></td>
          <td style='width:70px;text-align:center;'><b>FECHA</b></td>
          <td style='width:70px;text-align:center;'><b>NIT</b></td>
          <td style='width:70px;text-align:center;'><b>PROVEEDOR</b></td>
          <td style='width:70px;text-align:center;'><b>ARCHIVO</b></td>
          <td style='width:70px;text-align:center;'><b>FECHA CREACION</b></td>
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
      $headTable .=  "<tr class='thead' style='border: 1px solid #999; color: #f7f7f7;'>
                        <td style='width:70px;text-align:center;'><b>SUCURSAL</b></td>
                        <td style='width:70px;text-align:center;'><b>CONSECUTIVO</b></td>
                        <td style='width:70px;text-align:center;'><b>FECHA</b></td>
                        <td style='width:70px;text-align:center;'><b>NIT</b></td>
                        <td style='width:70px;text-align:center;'><b>PROVEEDOR</b></td>
                        <td style='width:70px;text-align:center;'><b>ARCHIVO</b></td>
                        <td style='width:70px;text-align:center;'><b>FECHA CREACION</b></td>
                      </tr>";

      $style = "";

      foreach($this->arrayDoc as $fila => $result){
        //CUERPO DEL INFORME
        $archivo = substr($result['archivo'],0,40);
        $bodyTable .=  "<tr style='height:20px; $style'>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[sucursal]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[consecutivo]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[fecha_inicial]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[nit_tercero]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[tercero]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$archivo</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[fecha_creacion]</td>
                        </tr>";

        if($style == "background-color:#d0c4c4;"){
          $style = "";
        }
        else{
          $style = "background-color:#d0c4c4;";
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
                <tr><td style="font-size:13px;"><b>Informe Inventario Consolidado</b><br> <?php echo $subtitulo_cabecera; ?></td></tr>
                <tr><td style="font-size:11px;">Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td></tr>
              </table>
              <table class="tableInforme" style="width:1015px;border-collapse:collapse;">
                <?php echo $headTable.$bodyTable; ?>
              </table>
            </div>
          </div>
        </div>
      </body>
      <?php
      $texto = ob_get_contents();
      $documento = "Informe_Comprobante_Egreso_Archivos_Adjuntos_" . date('Y_m_d');
      if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER-L';}
      if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
      if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
      if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=10;}
      if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }

      if($this->IMPRIME_PDF == 'true'){
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
        $mpdf->SetTitle('Informe Comprobante Egreso Archivos Adjuntos');
        $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
        $mpdf->WriteHTML(utf8_encode($texto));

        if($PDF_GUARDA == "true"){
          $mpdf->Output($documento.".pdf",'D');
        } else{
          $mpdf->Output($documento.".pdf",'I');
        }
        exit;
      }
      else if($IMPRIME_HTML == "true"){
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

  $objectInform = new InformeComprobanteEgresoArchivosAdjuntos($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$contenido,$arrayTercerosJSON,$mysql);
  $objectInform->generate();
?>
