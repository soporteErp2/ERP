<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');

  /**
   * @class InformeRequisicion
   */
  class InformeRequisicion{

    public $IMPRIME_XLS                = '';
    public $IMPRIME_PDF                = '';
    public $arraySolicitanteJSON       = '';
    public $arrayCentroCostosJSON      = '';
    public $MyInformeFiltroFechaInicio = '';
    public $MyInformeFiltroFechaFinal  = '';
    public $tipo_requisicion           = '';
    public $discrimina_items           = '';
    public $tipo_cruce                 = '';
    public $mysql                      = '';
    public $sucursal                   = '';
    public $bodega                     = '';
    public $id_empresa                 = '';
    public $customWhere                = '';
    public $arrayDoc                   = array();
    public $arrayDocItems              = array();

    /**
     * [__construct]
     * @param str $IMPRIME_XLS                  Generar en EXCEL
     * @param str $IMPRIME_PDF                  Generar en PDF
     * @param arr $arraySolicitanteJSON         Filtro por solicitante
     * @param arr $arrayCentroCostosJSON        Filtro por centro de costos
     * @param dat $MyInformeFiltroFechaInicio   Fecha inicial del informe
     * @param dat $MyInformeFiltroFechaFinal    Fecha final del informe
     * @param srt $tipo_requisicion             Filtro por tipo de documento
     * @param srt $discrimina_items             Filtro para discriminar items del documento
     * @param int $sucursal                     Filtro por sucursal
     * @param int $bodega                       Filtro por bodega
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_XLS,$IMPRIME_PDF,$arraySolicitanteJSON,$arrayCentroCostosJSON,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$tipo_requisicion,$discrimina_items,$tipo_cruce,$autorizado,$sucursal,$bodega,$mysql){
      $this->IMPRIME_XLS                = $IMPRIME_XLS;
      $this->IMPRIME_PDF                = $IMPRIME_PDF;
      $this->arraySolicitanteJSON       = json_decode($arraySolicitanteJSON);
      $this->arrayCentroCostosJSON      = json_decode($arrayCentroCostosJSON);
      $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
      $this->tipo_requisicion           = $tipo_requisicion;
      $this->discrimina_items           = $discrimina_items;
      $this->tipo_cruce                 = $tipo_cruce;
      $this->autorizado                 = $autorizado;
      $this->mysql                      = $mysql;
      $this->sucursal                   = $sucursal;
      $this->bodega                     = $bodega;
      $this->id_empresa                 = $_SESSION['EMPRESA'];
    }

    /**
     * @method showError Mostrar alerta si se presenta un error
     * @param  str $mensaje Mensaje de error a mostrar
     */
    public function showError($mensaje){
      echo '<script>alert("Error\n' . $mensaje . '");</script>' . $mensaje;
      exit;
    }

    /**
     * @method getCustomFiltres armar los filtros a aplicar al informe
     */
    public function getCustomFiltres(){
      if($this->MyInformeFiltroFechaFinal == '' || $this->MyInformeFiltroFechaInicio == ''){
        $this->showError('Debe Seleccionar las fechas del informe');
      }
      $this->customWhere = "AND CR.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal' ";

      if(!empty($this->arraySolicitanteJSON)){
        foreach($this->arraySolicitanteJSON as $indice => $id_solicitante){
          $whereSolicitante .= ($whereSolicitante == '')? ' CR.id_solicitante = ' . $id_solicitante : ' OR CR.id_solicitante = ' . $id_solicitante;
        }
        $whereSolicitante = " AND (".$whereSolicitante.")";
      }

      if(!empty($this->arrayCentroCostosJSON)){
        foreach($this->arrayCentroCostosJSON as $indice => $codigo_centro_costo){
          $whereCcos .= ($whereCcos == '')? ' CRI.id_centro_costos =' . $codigo_centro_costo : ' OR CRI.id_centro_costos = ' . $codigo_centro_costo;
        }
        $whereCcos = " AND (".$whereCcos.")";
      }

      if($this->tipo_cruce <> 'Todas'){
        $this->customWhere .= ($this->tipo_cruce == 'cruzadas')? " AND CR.estado = 2 " : " AND CR.estado = 1 ";
      }
      else{
        $this->customWhere .= " AND (CR.estado = 1 OR CR.estado = 2 OR CR.estado = 3)";
      }

      if($this->autorizado <> 'Todas'){
        $this->title = "<tr><td style='font-size:11px;'><b>Filtrados por Autorizaciones:</b>$this->autorizado</td></tr>";
      }

      $this->customWhere .= ($this->tipo_requisicion != '' && $this->tipo_requisicion != 'Todos')? " AND CR.tipo = '$this->tipo_requisicion' " : "";
      $this->customWhere .= ($this->sucursal != '' && $this->sucursal != 'global')? " AND CR.id_sucursal = $this->sucursal " : "";
      $this->customWhere .= ($this->bodega != '' && $this->bodega != 'global')? " AND CR.id_bodega = $this->bodega " : "";
      $this->customWhere .= $whereSolicitante . $whereCcos;
    }

    /**
     * @method getDocumentoInfo consultar ls informacion de las requisiciones
     */
    public function getDocumentoInfo(){
      $sql = "SELECT
                CR.id,
                CR.sucursal,
                CR.bodega,
                CR.consecutivo,
                CR.pendientes_facturar,
                CR.fecha_inicio,
                CR.fecha_vencimiento,
                CR.tipo_nombre,
                CR.nombre_solicitante,
                CR.autorizado,
                CR.estado,
                CRI.id AS id_detalle,
                CRI.id_requisicion_compra,
                CRI.codigo,
                CRI.id_unidad_medida,
                CRI.nombre_unidad_medida,
                CRI.cantidad_unidad_medida,
                CRI.nombre,
                COI.cantidad,
                CRI.saldo_cantidad,
                CRI.id_centro_costos,
                CRI.codigo_centro_costo,
                CRI.centro_costo,
                CRI.observaciones,
                CO.consecutivo AS consecutivo_oc,
	              COI.id AS id_coi
              FROM
                compras_requisicion AS CR
              INNER JOIN
                compras_requisicion_inventario AS CRI
              ON
                CRI.id_requisicion_compra = CR.id
              LEFT JOIN
                compras_ordenes_inventario AS COI
              ON
                COI.id_tabla_inventario_referencia = CRI.id
              LEFT JOIN
                compras_ordenes AS CO
              ON
                CO.id = COI.id_orden_compra
              WHERE
                CR.activo = 1
              AND
                CR.id_empresa = $this->id_empresa
                $this->customWhere";

      $query = $this->mysql->query($sql,$this->mysql->link);
      while($row = $this->mysql->fetch_array($query)){
        $id_doc = $row['id'];
        $whereIdDocs .= ($whereIdDocs == '')? "id_requisicion = $id_doc" : " OR id_requisicion = $id_doc";
        $this->arrayDoc[$id_doc] = array(
                                          'id'                  => $id_doc,
                                          'sucursal'            => $row['sucursal'],
                                          'bodega'              => $row['bodega'],
                                          'consecutivo'         => $row['consecutivo'],
                                          'pendientes_facturar' => $row['pendientes_facturar'],
                                          'fecha_inicio'        => $row['fecha_inicio'],
                                          'fecha_vencimiento'   => $row['fecha_vencimiento'],
                                          'codigo_centro_costo' => $row['codigo_centro_costo'],
                                          'tipo_nombre'         => $row['tipo_nombre'],
                                          'nombre_solicitante'  => $row['nombre_solicitante'],
                                          'estado'              => $row['estado'],
                                        );

        $id = $row['id_coi'];
        $this->arrayDocItems[$id_doc][$id] = array(
                                                    'codigo'                 => $row['codigo'],
                                                    'id_unidad_medida'       => $row['id_unidad_medida'],
                                                    'nombre_unidad_medida'   => $row['nombre_unidad_medida'],
                                                    'cantidad_unidad_medida' => $row['cantidad_unidad_medida'],
                                                    'nombre'                 => $row['nombre'],
                                                    'cantidad'               => $row['cantidad'],
                                                    'saldo_cantidad'         => $row['saldo_cantidad'],
                                                    'id_centro_costos'       => $row['id_centro_costos'],
                                                    'codigo_centro_costo'    => $row['codigo_centro_costo'],
                                                    'centro_costo'           => $row['centro_costo'],
                                                    'observaciones'          => $row['observaciones'],
                                                    'consecutivo_oc'         => $row['consecutivo_oc']
                                                  );
      }

      if($this->autorizado <> 'Todas'){
        //CONSULTAR LAS AUTORIZACIONES DEL DOCUMENTO
        $sql = "SELECT id_requisicion,tipo_autorizacion FROM autorizacion_requisicion WHERE activo = 1 AND id_empresa = $this->id_empresa AND ($whereIdDocs)";
        $query = $this->mysql->query($sql,$this->mysql->link);
        while($row = $this->mysql->fetch_array($query)){
          $id_requisicion = $row['id_requisicion'];
          $autorizacion   = $row['tipo_autorizacion'];

          $this->arrayDoc[$id_requisicion]['autorizacion'] = $autorizacion;
        }

        foreach($this->arrayDoc as $id_documento => $arrayResult){
          if($this->autorizado == 'Autorizada' && $arrayResult['autorizacion'] <> 'Autorizada'){
            unset($this->arrayDoc[$id_documento]);
            unset($this->arrayDocItems[$id_documento]);
          }
          else if($this->autorizado == 'Aplazada' && $arrayResult['autorizacion'] <> 'Aplazada'){
            unset($this->arrayDoc[$id_documento]);
            unset($this->arrayDocItems[$id_documento]);
          }
          else if($this->autorizado == 'Rechazada' && $arrayResult['autorizacion'] <> 'Rechazada'){
            unset($this->arrayDoc[$id_documento]);
            unset($this->arrayDocItems[$id_documento]);
          }
        }
      }
    }

    /**
     * getExcel armar el informe para excel
     * @return str body informe generado
     */
    public function getExcel(){
      foreach($this->arrayDoc as $id_rq => $result){
        foreach($this->arrayDocItems[$id_rq] as $id_row => $resultItems){
          $bodyInform .= "<tr>
                            <td>$result[sucursal]</td>
                            <td>$result[bodega]</td>
                            <td>$result[consecutivo]</td>
                            <td>$result[fecha_inicio]</td>
                            <td>$result[fecha_vencimiento]</td>
                            <td>$result[nombre_solicitante]</td>
                            <td>$result[tipo_nombre]</td>
                            <td>$result[autorizado]</td>
                            ".(($this->discrimina_items == 'Si')? "
                            <td>$resultItems[codigo]</td>
                            <td>$resultItems[nombre]</td>
                            <td>$resultItems[nombre_unidad_medida] x $resultItems[cantidad_unidad_medida]</td>
                            <td>$resultItems[cantidad]</td>
                            <td>$resultItems[saldo_cantidad]</td>
                            <td>$resultItems[codigo_centro_costo] - $resultItems[centro_costo]</td>
                            <td>$resultItems[observaciones]</td>
                            <td>$resultItems[consecutivo_oc]</td>" : "" )."
                          </tr>";
        }
      }

      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=informe_requisiciones_compra_".date("Y_m_d").".xls");
      header("Pragma: no-cache");
      header("Expires: 0");

      ?>
      <table>
        <tr>
          <td><b><?php echo $_SESSION['NOMBREEMPRESA']; ?></b></td>
        </tr>
        <tr>
          <td><b>NIT</b><?php echo $_SESSION['NITEMPRESA']; ?></td>
        </tr>
        <tr>
          <td><b>Informe Requisiciones de Compra</td>
        </tr>
        <tr>
          <td>Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td>
        </tr>
        <?php echo $this->title; ?>
      </table>
      <table>
        <tr style="background:#999;padding-left:10px;height:25px;color:#FFF;font-weight:bold;">
          <td>SUCURSAL</td>
          <td>BODEGA</td>
          <td>CONSECUTIVO</td>
          <td>FECHA</td>
          <td>VENCIMIENTO</td>
          <td>SOLICITANTE</td>
          <td>TIPO</td>
          <td>AUTORIZACION</td>
          <?php if($this->discrimina_items == 'Si'){ ?>
          <td>CODIGO</td>
          <td>ARTICULO</td>
          <td>UNIDAD</td>
          <td>CANTIDAD</td>
          <td>CANTIDAD PENDIENTE</td>
          <td>CENTRO DE COSTOS</td>
          <td>OBSERVACIONES ITEM</td>
          <td>CONSECUTIVO OC</td>
          <?php } ?>
        </tr>
        <?php echo $bodyInform; ?>
      </table>
      <?php
    }

    /**
     * getHtmlPdf armar el informe para la vista en la app y pdf
     * @return str body informe generado
     */
    public function getHtmlPdf(){
      foreach($this->arrayDoc as $id_rq => $result){
        $styleCancel = ($result['estado'] == 3)? "color:red;" : "";

        $bodyTable .=  "<tr>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[sucursal]</td>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[bodega]</td>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[consecutivo]</td>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[pendientes_facturar]</td>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[fecha_inicio]</td>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[fecha_vencimiento]</td>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[nombre_solicitante]</td>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[tipo_nombre]</td>
                          <td style='width:70px;text-align:center;font-size:11px;$styleCancel'>$result[autorizado]</td>
                        </tr>";

        if($this->discrimina_items == 'Si'){
          $bodyTable .=  "<tr>
                            <td colspan='9' style='border-bottom: 1px solid #999;'>
                              <table class='tableInforme' style='margin-top:0px;'>
                                <tr class='total'>
                                  <td style='text-align:center;'>&nbsp;&nbsp;&nbsp;Codigo</td>
                                  <td style='text-align:center;'>Articulo</td>
                                  <td style='text-align:center;'>Unidad</td>
                                  <td style='text-align:center;'>Cantidad</td>
                                  <td style='text-align:center;'>Cantidad Pendiente</td>
                                  <td style='text-align:center;'>Centro de Costos</td>
                                  <td style='text-align:center;'>Consecutivo OC</td>
                                </tr>";

          foreach($this->arrayDocItems[$id_rq] as $id => $resultItems){
            $bodyTable .=  "<tr>
                              <td style='font-size:11px;text-align:center;'>&nbsp;&nbsp;&nbsp;$resultItems[codigo]</td>
                              <td style='font-size:11px;text-align:center;'>$resultItems[nombre]</td>
                              <td style='font-size:11px;text-align:center;'>$resultItems[nombre_unidad_medida] x $resultItems[cantidad_unidad_medida]</td>
                              <td style='font-size:11px;text-align:center;'>$resultItems[cantidad]</td>
                              <td style='font-size:11px;text-align:center;'>$resultItems[saldo_cantidad]</td>
                              <td style='font-size:11px;text-align:center;'>$resultItems[codigo_centro_costo] - $resultItems[centro_costo]</td>
                              <td style='font-size:11px;text-align:center;'>$resultItems[consecutivo_oc]</td>
                            </tr>";
          }
          $end_arr = end($this->arrayDoc);
          $addHead = ($end_arr['id'] == $id_rq)? "" :  "<tr class='thead'>
                                                          <td style='width:70px;text-align:center;'><b>SUCURSAL</b></td>
                                                          <td style='width:70px;text-align:center;'><b>BODEGA</b></td>
                                                          <td style='width:70px;text-align:center;'><b>CONSECUTIVO</b></td>
                                                          <td style='width:70px;text-align:center;'><b>UNIDADES PENDIENTES</b></td>
                                                          <td style='width:70px;text-align:center;'><b>FECHA</b></td>
                                                          <td style='width:70px;text-align:center;'><b>VENCIMIENTO</b></td>
                                                          <td style='width:80px;text-align:center;'><b>SOLICITANTE</b></td>
                                                          <td style='width:70px;text-align:center;'><b>TIPO</b></td>
                                                          <td style='width:70px;text-align:center;'><b>AUTORIZACION</b></td>
                                                        </tr>" ;

          $bodyTable.= "</table>
                          </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        $addHead";
        }
      }

      ob_start();
      ?>
      <style>
        .tableInforme{
          font-size       : 12px;
          width           : 100%;
          margin-top      : 20px;
          border-collapse : collapse;
        }

        .tableInforme td{
          padding-left    : 0px;
        }

        .tableInforme .thead{
          height          : 25px;
          background      : #999;
          padding-left    : 10px;
          height          : 25px;
          font-size       : 12px;
          color           : #FFF;
          font-weight     : bold;
        }

        .tableInforme .total{
          height          : 25px;
          background      : #EEE;
          font-weight     : bold;
          color           : #8E8E8E;
        }

        .tableInforme .total td{
          border-top      : 1px solid #999;
          border-bottom   : 1px solid #999;
        }
      </style>
      <body>
        <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
          <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center;margin-bottom:15px;">
              <table align="center" style="text-align:center;" >
                <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                <tr><td style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                <tr><td style="font-size:13px;"><b>Informe Requisiciones de Compra</b><br> <?php echo $subtitulo_cabecera; ?></td></tr>
                <tr><td style="font-size:11px;">Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td></tr>
                <?php echo $this->title; ?>
              </table>
              <table class="tableInforme" style="width:1015px;border-collapse:collapse;">
                <tr class="thead">
                  <td style="width:70px;text-align:center;"><b>SUCURSAL</b></td>
                  <td style="width:70px;text-align:center;"><b>BODEGA</b></td>
                  <td style="width:70px;text-align:center;"><b>CONSECUTIVO</b></td>
                  <td style='width:70px;text-align:center;'><b>UNIDADES PENDIENTES</b></td>
                  <td style="width:70px;text-align:center;"><b>FECHA</b></td>
                  <td style="width:70px;text-align:center;"><b>VENCIMIENTO</b></td>
                  <td style="width:80px;text-align:center;"><b>SOLICITANTE</b></td>
                  <td style="width:70px;text-align:center;"><b>TIPO</b></td>
                  <td style="width:70px;text-align:center;"><b>AUTORIZACION</b></td>
                </tr>
                <?php echo $bodyTable; ?>
              </table>
            </div>
          </div>
        </div>
        <br>
        <?php echo $cuerpoInforme; ?>
      </body>
      <?php
        $texto = ob_get_contents(); ob_end_clean();

        if(isset($TAM)){ $HOJA = $TAM; }
        else{ $HOJA = 'LETTER-L'; }

        if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
        if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
        if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

        if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
        else{ $MS=10; $MD=10; $MI=10; $ML=10; }

        if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12; }
        if($this->IMPRIME_PDF == 'true'){
          include("../../../../misc/MPDF54/mpdf.php");
          $mpdf = new mPDF(
                            'utf-8',        // mode - default ''
                            $HOJA,          // format - A4, for example, default ''
                            12,             // font size - default 0
                            '',             // default font family
                            $MI,            // margin left
                            $MD,            // margin right
                            $MS,            // margin top
                            $ML,            // margin bottom
                            10,             // margin header
                            10,             // margin footer
                            $ORIENTACION    // L - landscape, P - portrait
                          );
          $mpdf->SetProtection(array('print'));
          $mpdf->SetTitle("Informe Requisiciones");
          $mpdf->SetDisplayMode ('fullpage');
          $mpdf->SetHeader("");
          $mpdf->SetHTMLFooter('<div style="text-align:right; font-size:12px; font-weight:bold;">Pagina {PAGENO}/{nb}</div>');
          $mpdf->WriteHTML(utf8_encode($texto));
          $mpdf->Output("requisicion_compra_".date("Y_m_d").".pdf","I");
        }
        else{
          echo $texto;
        }
    }

    /**
     * @method generate Generar el informe
     */
    public function generate(){
      $this->getCustomFiltres();
      $this->getDocumentoInfo();
      if($this->IMPRIME_XLS == 'true'){
        $this->getExcel();
      }
      else{
        $this->getHtmlPdf();
      }
    }
  }

  $objectInform = new InformeRequisicion($IMPRIME_XLS,$IMPRIME_PDF,$arraySolicitanteJSON,$arrayCentroCostosJSON,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$tipo_requisicion,$discrimina_items,$tipo_cruce,$autorizado,$sucursal,$bodega,$mysql);
  $objectInform->generate();
?>
