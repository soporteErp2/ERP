<?php
  include_once('../../../../../../configuracion/conectar.php');
  include_once('../../../../../../configuracion/define_variables.php');
  /**
   * @class InformeInventarioConsolidado
   */
  class InformeInventarioConsolidado{

    public $IMPRIME_HTML               = '';
    public $IMPRIME_XLS                = '';
    public $MyInformeFiltroFechaInicio = '';
    public $MyInformeFiltroFechaFinal  = '';
    public $sucursal                   = '';
    public $bodega                     = '';
    public $items             = '';
    public $mysql                      = '';
    public $customWhere                = '';

    /**
     * [__construct]
     * @param str $IMPRIME_HTML                 Generar en HTML
     * @param str $IMPRIME_XLS                  Generar en EXCEL
     * @param str $IMPRIME_PDF                  Generar en PDF
     * @param int $sucursal                     Filtro por sucursal
     * @param int $bodega                       Filtro por bodega
     * @param int $filtro_familia               Filtro por familia
     * @param int $filtro_grupo                 Filtro por grupo
     * @param int $filtro_subgrupo              Filtro por subgrupo
     * @param arr $items               Filtro por items
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct(
              $IMPRIME_XLS,
              $sucursal,
              $bodega,
              $separador_miles,
              $separador_decimale,
              $items,
              $fecha_inicio,
              $fecha_final,
              $mysql){

      if ($bodega=="global" || $sucursal=="global") {
        $this->showError("debe seleccionar una sucursal y bodega, no se permiten todas");
      }

      if ($fecha_inicio=="" || $fecha_final=="") {
        $this->showError("las fechas son obligatorias");
      }

      if ($items=="") {
        $this->showError("debe seleccionar al menos un item");
      }

      if (
        (explode('-',$fecha_inicio)[0] <> explode('-',$fecha_final)[0]) ||
        ((explode('-',$fecha_final)[0] - explode('-',$fecha_inicio)[0]) * 12 + (explode('-',$fecha_final)[1] - explode('-',$fecha_inicio)[1]) > 2)
    ) {
          $this->showError("Solo se puede consultar 3 meses");
      }

      $this->IMPRIME_XLS           = $IMPRIME_XLS;
      $this->sucursal              = $sucursal;
      $this->bodega                = $bodega;
      $this->separador_miles       = $separador_miles;
      $this->separador_decimales    = $separador_decimales;
      $this->items                 = json_decode($items,true);
      $this->fecha_inicio          = $fecha_inicio;
      $this->fecha_final           = $fecha_final;
      $this->mysql                 = $mysql;
      $this->id_empresa            = $_SESSION['EMPRESA'];
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
        $this->customWhereUbication .= " AND id_sucursal = '$this->sucursal'";
      }

      if($this->bodega != '' && $this->bodega != 'global'){
        $this->customWhereUbication .= " AND id_ubicacion = '$this->bodega'";
      }

      // if(!empty($this->items)){
      //   foreach($this->items as $indice => $id_item){
      //     $itemsIn .= ($itemsIn == "")? "id = '$id_item'" : " OR id = '$id_item'";
      //   }
      //   $this->customWhereIn .= ($itemsIn<>'')? " AND ($itemsIn)" : "" ;
      // }

    }

    /**
     * @method getReportInfo consultar ls informacion de las requisiciones
     */
    public function getReportInfo(){
      // var_dump($this->items);

      $items = array_map(function($item){
        return $item["id"];
      },$this->items);

      // consultar los movimientos de entradas y salidas
      $sql = "SELECT
                    id,
                    id_documento,
                    tipo_documento,
                    consecutivo_documento,
                    fecha_documento,
                    fecha_movimiento,
                    hora_movimiento,
                    accion_documento,
                    accion_inventario,
                    id_item,
                    codigo,
                    item,
                    unidad_medida,
                    cantidad_unidades,
                    costo,
                    cantidad,
                    costo_anterior,
                    costo_nuevo,
                    cantidad_anterior,
                    cantidad_nueva,
                    bodega,
                    usuario
                  FROM
                    logs_inventario 
                  WHERE activo = 1
                  AND id_item IN (".implode(",",$items).")
                  AND id_bodega=$this->bodega 
                  AND fecha_documento BETWEEN  '$this->fecha_inicio' AND '$this->fecha_final'
                  ORDER BY fecha_documento ASC ";
      
      $query = $this->mysql->query($sql);
      while ($row=$this->mysql->fetch_array($query)) {
        $this->bodega_name = ($this->bodega_name)? $this->bodega_name : $row['bodega'];
        $items_info[$row['id_item']]=$row;
        $dates[$row['fecha_documento']]=$row['fecha_documento'];

        switch ($row['tipo_documento']) {
          case 'FC':
          case 'EA':
          case 'TDI':
            // aumentar cuando se genera el documento
            if ($row['accion_documento']=='Generar') {
              // condicion solo para el traslado de inventario TDI
              if ($row['accion_inventario']=='traslado salida') {
                $data[$row['id_item']][$row['fecha_documento']]['salidas'] += $row['cantidad'];
              }
              else {
                $data[$row['id_item']][$row['fecha_documento']]['entradas'] += $row['cantidad'];
              }
            }
            // disminuir cuando se edita o cancela
            else{
              if ($row['accion_inventario']=='traslado salida') {
                $data[$row['id_item']][$row['fecha_documento']]['salidas'] -= $row['cantidad'];
              }
              else {
                $data[$row['id_item']][$row['fecha_documento']]['entradas'] -= $row['cantidad'];
              }
            }
            break;
          case 'FV':
          case 'RV':
          case 'POS':
            if ($row['accion_documento']=='Generar') {
              $data[$row['id_item']][$row['fecha_documento']]['salidas'] += $row['cantidad'];
            }
            else{
              $data[$row['id_item']][$row['fecha_documento']]['salidas'] -= $row['cantidad'];
            }
            break;
        }

        // if (!isset($data[$row['id_item']][$row['fecha_documento']]['saldo_inicial'])) {
        //   $data[$row['id_item']][$row['fecha_documento']]['saldo_inicial'] = $row['cantidad_anterior'];
        // }
        // if (!isset($data[$row['id_item']][$row['fecha_documento']]['saldo_final'])) {
        //   $data[$row['id_item']][$row['fecha_documento']]['saldo_final'] = $row['cantidad_nueva'];
        // }


      }

      //consultar los saldos (saldo inicial)
      $sql = "SELECT
                    id,
                    fecha_movimiento,
                    fecha_documento,
                    cantidad_anterior,
                    cantidad_nueva,
                    id_item
                  FROM
                    logs_inventario 
                  WHERE activo = 1
                  AND id_item IN (".implode(",",$items).")
                  AND id_bodega=$this->bodega 
                  AND fecha_documento BETWEEN  '$this->fecha_inicio' AND '$this->fecha_final'
                  ORDER BY id ASC;";
      
      $query = $this->mysql->query($sql);
      while ($row=$this->mysql->fetch_array($query)) {
        if (!isset($data[$row['id_item']][$row['fecha_documento']]['saldo_inicial'])) {
          $data[$row['id_item']][$row['fecha_documento']]['saldo_inicial'] = $row['cantidad_anterior'];
        }
      }

      $sql = "SELECT
                    id,
                    fecha_movimiento,
                    fecha_documento,
                    cantidad_anterior,
                    cantidad_nueva,
                    id_item
                  FROM
                    logs_inventario 
                  WHERE activo = 1
                  AND id_item IN (".implode(",",$items).")
                  AND id_bodega=$this->bodega 
                  AND fecha_documento BETWEEN  '$this->fecha_inicio' AND '$this->fecha_final'
                  ORDER BY id DESC ";
      
      $query = $this->mysql->query($sql);
      while ($row=$this->mysql->fetch_array($query)) {
        if (!isset($data[$row['id_item']][$row['fecha_documento']]['saldo_final'])) {
          $data[$row['id_item']][$row['fecha_documento']]['saldo_final'] = $row['cantidad_nueva'];
        }

      }


      // consultar los saldos iniciales y finales por dia por item
      // $sql = "SELECT
      //               MAX(id),
      //               fecha_movimiento,
      //               fecha_documento,
      //               MAX(cantidad_anterior) AS cantidad_anterior,
      //               MIN(cantidad_nueva) AS cantidad_nueva,
      //               hora_movimiento,
      //               accion_documento,
      //               accion_inventario,
      //               id_item,
      //               codigo,
      //               cantidad,
      //               costo_nuevo
      //             FROM
      //               logs_inventario 
      //             WHERE activo = 1
      //             AND id_item IN (".implode(",",$items).")
      //             AND id_bodega=$this->bodega 
      //             AND fecha_documento BETWEEN  '$this->fecha_inicio' AND '$this->fecha_final'
      //             GROUP BY id_item,fecha_documento 
      //             ORDER BY fecha_documento ASC";
      
      // $query = $this->mysql->query($sql);
      // while ($row=$this->mysql->fetch_array($query)) {
      //   // $dates[$row['fecha_documento']]=$row['fecha_documento'];
      //   // $data[$row['id_item']][$row['fecha_documento']]['saldo_inicial'] = $row['cantidad_anterior'];
      //   // $data[$row['id_item']][$row['fecha_documento']]['saldo_final'] = $row['cantidad_nueva'];

      // }

      $ret_val = ["items" => $items_info, "dates" => $dates,"rows"=>$data];
      // echo "<br><pre>";
      // echo json_encode($ret_val,JSON_PRETTY_PRINT);
      //   // var_dump($items_data);
      // echo "<pre>";

      return $ret_val;

    }

    /**
     * getReportContent armar el informe para la vista en la app y pdf
     * @return str body informe generado
     */
    public function getReportContent(){
      if($this->IMPRIME_XLS == "true"){      
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=kardex_acumulado".date("Y_m_d H_i_s").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
      }

      $data = $this->getReportInfo();

      // var_dump($data);

      ?>
      <style>
        #RecibidorInforme_kardex_acumulado{
            min-width: 1015px;
            width: auto !important;
          }
        .tableInforme{
          font-size       : 12px;
          width           : 100%;
          margin-top      : 20px;
          border-collapse : collapse;
        }
        .kardex-acumulado td{
          padding: 8px;
        }
        .tableInforme .thead td{
          background     : #999;
          height         : 25px;
          font-size      : 14px;
          color          : #FFF;
          font-weight    : bold;
          text-transform :capitalize;
          text-align : center;
        }

        .tableInforme .tbody td{
          border-top: 1px solid #EEE;
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

        .kardex-acumulado .row {
            background-color : #EEE;
        }

      </style>
      <body>
        <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
          <div style="float:left; width:100%">
            <div style="float:left;width:100%; text-align:center;margin-bottom:15px;">
              <table align="center" style="text-align:center;" >
                <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
                <tr><td style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
                <tr><td style="font-size:13px;"> <?php echo $this->bodega_name; ?></td></tr>                
                <tr><td style="font-size:13px;"><b>Kardex Acumulado</b><br> <?php echo $subtitulo_cabecera; ?></td></tr>
              </table>
              <table class="tableInforme kardex-acumulado" style="width:1015px;border-collapse:collapse;">
                  <tr class="thead">
                    <td rowspan="2">Codigo</td>
                    <td rowspan="2">Nombre</td>
                    <td rowspan="2">Unidad Medida</td>
                    <?php
                      foreach ($data["dates"] as $date) {
                        echo "<td colspan='4'>$date</td>";
                      }
                    ?>
                  </tr>
                  <tr class="thead">
                    <?php 
                      foreach ($data["dates"] as $date) {
                        ?>
                          <td>INICIAL</td>
                          <td>ENTRADA</td>
                          <td>SALIDA</td>
                          <td>FINAL</td>
                          
                          <?php
                      }
                    ?>
                  </tr>
                  <?php
                    $row_class = true;
                    foreach ($data["items"] as $id_item =>  $item) {
                      $row_class = !$row_class;
                    ?>
                      <tr class="<?= $row_class ? "row" : "" ?>" >
                        <td ><?= $item["codigo"]; ?></td>
                        <td ><?= $item["item"]; ?></td>
                        <td ><?= $item["unidad_medida"]; ?></td>
                    <?php
                      foreach ($data["dates"] as $date) {
                        $saldo_inicial = $data["rows"][$id_item][$date]["saldo_inicial"] ? $data["rows"][$id_item][$date]["saldo_inicial"] : 0;
                        $entradas      = $data["rows"][$id_item][$date]["entradas"] ? $data["rows"][$id_item][$date]["entradas"] : 0;
                        $salidas       = $data["rows"][$id_item][$date]["salidas"] ? $data["rows"][$id_item][$date]["salidas"] : 0;
                        $saldo_final   = $data["rows"][$id_item][$date]["saldo_final"]? $data["rows"][$id_item][$date]["saldo_final"] : 0;
                        ?>
                          <td style="text-align: center;" ><?= number_format($saldo_inicial,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles); ?></td>
                          <td style="text-align: center;" ><?= number_format($entradas,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles); ?></td>
                          <td style="text-align: center;" ><?= number_format($salidas,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles); ?></td>
                          <td style="text-align: center;" ><?= number_format($saldo_final,$_SESSION['DECIMALESMONEDA'],$this->separador_decimales,$this->separador_miles); ?></td>
                        <?php
                      }
                    echo "</tr>";
                    }
                  ?>
                  <!-- <tr class='thead'> -->
              </table>
            </div>
          </div>
        </div>
        <br>
      </body>
      <script>
        MyLoading2("off",{"texto":"reporte generado"});
      </script>
      <?php     
    }

    /**
     * @method generate Generar el informe
     */
    public function generate(){
      $this->getCustomFiltres();
      $this->getReportContent();
      
    }
  }

  $objectInform = new InformeInventarioConsolidado(
                      $IMPRIME_XLS,
                      $sucursal,
                      $bodega,
                      $separador_miles,
                      $separador_decimale,
                      $items,
                      $fecha_inicio,
                      $fecha_final,
                      $mysql);
  $objectInform->generate();
?>
