<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();
  /**
   * @class InformeDocumentosAuditados
   */
  class InformeDocumentosAuditados{

    public $IMPRIME_HTML               = '';
    public $IMPRIME_XLS                = '';
    public $IMPRIME_PDF                = '';
    public $MyInformeFiltroFechaInicio = '';
    public $MyInformeFiltroFechaFinal  = '';
    public $sucursal                   = '';
    public $tipoDocumento              = '';
    public $arrayEmpleadosJSON         = '';
    public $mysql                      = '';
    public $customWhere                = '';

    /**
     * [__construct]
     * @param str $IMPRIME_HTML                 Generar en HTML
     * @param str $IMPRIME_XLS                  Generar en EXCEL
     * @param str $IMPRIME_PDF                  Generar en PDF
     * @param dat $MyInformeFiltroFechaInicio   Fecha inicial del informe
     * @param dat $MyInformeFiltroFechaFinal    Fecha final del informe
     * @param int $sucursal                     Filtro por sucursal
     * @param str $tipoDocumento                Filtro tipo de documento
     * @param arr $arrayEmpleadosJSON           Filtro por empleados
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$tipoDocumento,$arrayEmpleadosJSON,$mysql){
      $this->IMPRIME_HTML               = $IMPRIME_HTML;
      $this->IMPRIME_XLS                = $IMPRIME_XLS;
      $this->IMPRIME_PDF                = $IMPRIME_PDF;
      $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
      $this->sucursal                   = $sucursal;
      $this->tipoDocumento              = $tipoDocumento;
      $this->arrayEmpleadosJSON         = json_decode($arrayEmpleadosJSON);
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
      else if($this->MyInformeFiltroFechaInicio != '' && $this->MyInformeFiltroFechaFinal != ''){
        $whereFechas = " AND DA.fecha BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'";
      }

      if($this->sucursal != '' && $this->sucursal != 'global'){
        $whereSucursal = " AND DA.id_sucursal = '$this->sucursal'";
      }

      if($this->tipoDocumento != 'Seleccione'){
        $whereTipoDocumento = " AND DA.tipo_documento = '$this->tipoDocumento'";
      }

      if(!empty($this->arrayEmpleadosJSON)){
        foreach($this->arrayEmpleadosJSON as $indice => $id_empleado){
          $empleados .= ($empleados == "")? "E.id = '$id_empleado'" : " OR E.id = '$id_empleado'";
        }
        $whereEmpleados  .= " AND ($empleados)";
      }

      $this->customWhere = $whereFechas.$whereSucursal.$whereTipoDocumento.$whereEmpleados;
    }

    /**
     * @method getDocumentoInfo consultar ls informacion de las requisiciones
     */
    public function getDocumentoInfo(){
      //--------------------- DATOS CABECERA DEL INFORME ---------------------//
      //DOCUMENTOS AUDITADOS
      $sql = "SELECT
                E.tipo_documento_nombre,
                E.documento,
                E.nombre,
                DA.tipo_documento,
                DA.consecutivo,
                DA.fecha,
                DA.hora
              FROM
                documentos_auditados AS DA
              LEFT JOIN
                empleados AS E
              ON
                E.id = DA.id_usuario
              WHERE
                DA.activo = '1'
              AND
                DA.id_empresa = '$this->id_empresa'
                $this->customWhere";
      $query = $this->mysql->query($sql,$this->mysql->link);
      while($row = $this->mysql->fetch_array($query)){
        $this->arrayDoc[] = array(
                                    'tipo_documento_nombre' => $row['tipo_documento_nombre'],
                                    'documento'             => $row['documento'],
                                    'nombre'                => $row['nombre'],
                                    'tipo_documento'        => $row['tipo_documento'],
                                    'consecutivo'           => $row['consecutivo'],
                                    'fecha'                 => $row['fecha'],
                                    'hora'                  => $row['hora']
                                  );
      }
    }

    /**
     * getExcel armar el informe para excel
     * @return str body informe generado
     */
    public function getExcel(){
      $style = "";

      foreach($this->arrayDoc as $indice => $result){
        //CUERPO DEL INFORME
        $bodyTable .=  "<tr style='height:20px; $style'>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[tipo_documento_nombre] $result[documento]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[nombre]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[tipo_documento]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[consecutivo]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[fecha] $result[hora]</td>
                        </tr>";

        if($style == "background-color:#d0c4c4;"){
          $style = "";
        }
        else{
          $style = "background-color:#d0c4c4;";
        }
      }
      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=Informe_Documentos_Auditados_".date("Y_m_d").".xls");
      header("Pragma: no-cache");
      header("Expires: 0");
      ?>
      <table>
        <tr>
          <td style="text-align:center;" colspan="5"><b><?php echo $_SESSION['NOMBREEMPRESA']; ?></b></td>
        </tr>
        <tr>
          <td style="text-align:center;" colspan="5"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td>
        </tr>
        <tr>
          <td style="text-align:center;" colspan="5"><b>Informe Documentos Auditados</td>
        </tr>
        <tr>
          <td style="text-align:center;" colspan="5">Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td>
        </tr>
      </table>
      <table>
        <tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
          <td style='width:70px;text-align:center;'><b>DOCUMENTO USUARIO</b></td>
          <td style='width:70px;text-align:center;'><b>NOMBRE USUARIO</b></td>
          <td style='width:70px;text-align:center;'><b>TIPO DOCUMENTO</b></td>
          <td style='width:70px;text-align:center;'><b>CONSECUTIVO</b></td>
          <td style='width:70px;text-align:center;'><b>FECHA Y HORA</b></td>
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
                        <td style='width:70px;text-align:center;'><b>DOCUMENTO USUARIO</b></td>
                        <td style='width:70px;text-align:center;'><b>NOMBRE USUARIO</b></td>
                        <td style='width:70px;text-align:center;'><b>TIPO DOCUMENTO</b></td>
                        <td style='width:70px;text-align:center;'><b>CONSECUTIVO</b></td>
                        <td style='width:70px;text-align:center;'><b>FECHA Y HORA</b></td>
                      </tr>";

      $style = "";

      foreach($this->arrayDoc as $indice => $result){
        //CUERPO DEL INFORME
        $bodyTable .=  "<tr style='height:20px; $style'>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[tipo_documento_nombre] $result[documento]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[nombre]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[tipo_documento]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[consecutivo]</td>
                          <td style='width:70px; text-align:center; font-size:11px;'>$result[fecha] $result[hora]</td>
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
                <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']; ?></td></tr>
                <tr><td style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                <tr><td style="font-size:13px;"><b>Informe Documentos Auditados</b></td></tr>
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
      $texto = ob_get_contents();
      $documento = "Informe_Documentos_Auditados_" . date('Y_m_d');
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
        $mpdf->SetTitle('Informe Documentos Auditados');
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

  $objectInform = new InformeDocumentosAuditados($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$tipoDocumento,$arrayEmpleadosJSON,$mysql);
  $objectInform->generate();
?>
