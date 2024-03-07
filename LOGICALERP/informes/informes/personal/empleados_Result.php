<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();
  /**
   * @class InformeEmpleados
   */
  class InformeEmpleados{

    public $IMPRIME_HTML  = '';
    public $IMPRIME_XLS   = '';
    public $IMPRIME_PDF   = '';
    public $acceso        = '';
    public $cargo         = '';
    public $rol           = '';
    public $sucursal      = '';
    public $mysql         = '';
    public $id_empresa    = '';
    public $customWhere   = '';
    public $arrayDoc      = array();

    /**
     * [__construct]
     * @param str $IMPRIME_HTML   Generar en HTML
     * @param str $IMPRIME_XLS    Generar en EXCEL
     * @param str $IMPRIME_PDF    Generar en PDF
     * @param dat $acceso         Filtro de acceso al sistema
     * @param dat $cargo          Filtro para los cargos del empleado
     * @param dat $rol            Filtro para los roles del empleado
     * @param int $sucursal       Filtro por sucursal
     * @param obj $mysql          Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$acceso,$cargo,$rol,$sucursal,$mysql){
      $this->IMPRIME_HTML   = $IMPRIME_HTML;
      $this->IMPRIME_XLS    = $IMPRIME_XLS;
      $this->IMPRIME_PDF    = $IMPRIME_PDF;
      $this->acceso         = $acceso;
      $this->cargo          = $cargo;
      $this->rol            = $rol;
      $this->sucursal       = $sucursal;
      $this->mysql          = $mysql;
      $this->id_empresa     = $_SESSION['EMPRESA'];
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
      if($this->sucursal != '' && $this->sucursal != 'global'){
        $whereSucursal = " AND E.id_sucursal = '$this->sucursal'";
      }

      if($this->acceso != '' && $this->acceso != 'global'){
        if($this->acceso == 'con_acceso'){
          $whereAcceso = " AND E.acceso_sistema = 'true'";
        }
        else{
          $whereAcceso = " AND E.acceso_sistema = 'false'";
        }
      }

      if($this->cargo != '' && $this->cargo != 'global'){
        $whereCargo = " AND E.id_cargo = '$this->cargo'";
      }

      if($this->rol != '' && $this->rol != 'global'){
        $whereRol = " AND E.id_rol = '$this->rol'";
      }

      $this->customWhere = $whereSucursal.$whereAcceso.$whereCargo.$whereRol;
    }

    /**
     * @method getDocumentoInfo consultar ls informacion de las requisiciones
     */
    public function getDocumentoInfo(){
      //----------------------- DATOS DE LOS EMPLEADOS -----------------------//
      $sql = "SELECT
                E.tipo_documento_nombre,
              	E.documento,
                E.nombre,
                E.cargo,
                E.rol,
              	E.sucursal,
                E.username
              FROM
              	empleados AS E
              WHERE
              	E.activo = 1
              AND
                E.id_empresa = $this->id_empresa
                $this->customWhere
              ORDER BY
                E.cargo ASC";
      $query = $this->mysql->query($sql,$this->mysql->link);
      while($row = $this->mysql->fetch_array($query)){
        $this->arrayDoc[] = array(
                                    'tipo_documento_nombre' => $row['tipo_documento_nombre'],
                                    'documento'             => $row['documento'],
                                    'nombre'                => $row['nombre'],
                                    'cargo'                 => $row['cargo'],
                                    'rol'                   => $row['rol'],
                                    'sucursal'              => $row['sucursal'],
                                    'username'              => $row['username']
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
      foreach($this->arrayDoc as $empleado => $result){
        if($enableStyle == "true"){
          $style = "style='background-color: #ffffff;'";
        }
        else{
          $style = "style='background-color: #d7d7d7;'";
        }

        $bodyTable .=  "<tr $style>
                          <td style='text-align:center; font-size:11px; padding-left: 4px;'>$result[tipo_documento_nombre]</td>
                          <td style='text-align:center; font-size:11px;'>$result[documento]</td>
                          <td style='text-align:center; font-size:11px;'>$result[nombre]</td>
                          <td style='text-align:center; font-size:11px;'>$result[cargo]</td>
                          <td style='text-align:center; font-size:11px;'>$result[rol]</td>
                          <td style='text-align:center; font-size:11px;'>$result[username]</td>
                          <td style='text-align:center; font-size:11px; padding-right: 4px;'>$result[sucursal]</td>
                        </tr>";

        if($enableStyle == "true"){
          $enableStyle = "false";
        }
        else{
          $enableStyle = "true";
        }
      }

      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=Informe_Empleados_".date("Y_m_d").".xls");
      header("Pragma: no-cache");
      header("Expires: 0");

      ?>
      <table>
        <tr>
          <td colspan="6" style="text-align:center;"><b><?php echo $_SESSION['NOMBREEMPRESA']; ?></b></td>
        </tr>
        <tr>
          <td colspan="6" style="text-align:center;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td>
        </tr>
        <tr>
          <td colspan="6" style="text-align:center;"><b>Informe Empleados</td>
        </tr>
        <tr>
          <td colspan="6" style="text-align:center;"><b>Fecha: <?php echo date('Y-m-d'); ?></td>
        </tr>
      </table>
      <table>
        <tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
          <td style='text-align:center;'><b>TIPO DOCUMENTO</b></td>
          <td style='text-align:center;'><b>DOCUMENTO</b></td>
          <td style='text-align:center;'><b>NOMBRE</b></td>
          <td style='text-align:center;'><b>CARGO</b></td>
          <td style='text-align:center;'><b>ROL</b></td>
          <td style='text-align:center;'><b>USUARIO</b></td>
          <td style='text-align:center;'><b>SUCURSAL</b></td>
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
                        <td style='text-align:center;'><b>TIPO DOCUMENTO</b></td>
                        <td style='text-align:center;'><b>DOCUMENTO</b></td>
                        <td style='text-align:center;'><b>NOMBRE</b></td>
                        <td style='text-align:center;'><b>CARGO</b></td>
                        <td style='text-align:center;'><b>ROL</b></td>
                        <td style='text-align:center;'><b>USUARIO</b></td>
                        <td style='text-align:center;'><b>SUCURSAL</b></td>
                      </tr>";

      //CUERPO DEL INFORME
      $enableStyle = "true";
      foreach($this->arrayDoc as $empleado => $result){
        if($enableStyle == "true"){
          $style = "style='background-color: #ffffff;'";
        } else{
          $style = "style='background-color: #d7d7d7;'";
        }

        $bodyTable .=  "<tr $style>
                          <td style='text-align:center; font-size:11px; padding-left: 4px;'>$result[tipo_documento_nombre]</td>
                          <td style='text-align:center; font-size:11px;'>$result[documento]</td>
                          <td style='text-align:center; font-size:11px;'>$result[nombre]</td>
                          <td style='text-align:center; font-size:11px;'>$result[cargo]</td>
                          <td style='text-align:center; font-size:11px;'>$result[rol]</td>
                          <td style='text-align:center; font-size:11px;'>$result[username]</td>
                          <td style='text-align:center; font-size:11px; padding-right: 4px;'>$result[sucursal]</td>
                        </tr>";

        if($enableStyle == "true"){
          $enableStyle = "false";
        } else{
          $enableStyle = "true";
        }
      }

      //DETALLE DE ROLES Y PERMISOS
      foreach($this->arrayDetalle as $rol => $permiso){
        echo $rol ."<br>";
        foreach($permiso as $key){
          for($i = 0; $i <= $key['nivel']; $i++){
            $tabuladores .= "&nbsp;&nbsp;&nbsp;";
          }
          echo $tabuladores . $key['nombre_permiso'] . "<br>";
          $tabuladores = "";
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
          font-weight   : bold;
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
                <tr><td style="font-size:13px;"><b>Informe Empleados</b></td></tr>
                <tr><td style="font-size:13px;text-align:center;"><b>Fecha: <?php echo date('Y-m-d'); ?></td></tr>
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
        $documento = "Informe_Empleados_" . date('Y_m_d');
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
        $mpdf->SetTitle('Informe Empleados');
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

  $objectInform = new InformeEmpleados($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$acceso,$cargo,$rol,$sucursal,$mysql);
  $objectInform->generate();
?>
