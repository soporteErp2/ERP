<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();
  /**
   * @class InformeInventarioConsolidado
   */
  class InformeInventarioConsolidado{

    public $IMPRIME_HTML               = '';
    public $IMPRIME_XLS                = '';
    public $IMPRIME_PDF                = '';
    public $MyInformeFiltroFechaInicio = '';
    public $MyInformeFiltroFechaFinal  = '';
    public $sucursal                   = '';
    public $bodega                     = '';
    public $filtro_familia             = '';
    public $filtro_grupo               = '';
    public $filtro_subgrupo            = '';
    public $arrayItemsJSON             = '';
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
     * @param arr $arrayItemsJSON               Filtro por items
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct(
              $IMPRIME_XLS,
              $IMPRIME_PDF,
              $sucursal,
              $bodega,
              $separador_miles,
              $separador_decimale,
              $filtro_receta,
              $filtro_receta_detalle,
              $filtro_familia,
              $filtro_grupo,
              $filtro_subgrupo,
              $arrayItemsJSON,
              $mysql){
      $this->IMPRIME_XLS           = $IMPRIME_XLS;
      $this->IMPRIME_PDF           = $IMPRIME_PDF;
      $this->sucursal              = $sucursal;
      $this->bodega                = $bodega;
      $this->separador_miles       = $separador_miles;
      $this->separador_decimale    = $separador_decimale;
      $this->filtro_receta         = $filtro_receta;
      $this->filtro_receta_detalle = $filtro_receta_detalle;
      $this->filtro_familia        = $filtro_familia;
      $this->filtro_grupo          = $filtro_grupo;
      $this->filtro_subgrupo       = $filtro_subgrupo;
      $this->arrayItemsJSON        = json_decode($arrayItemsJSON);
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

      if($this->filtro_familia != ''){
        $this->customWhereIn .= " AND id_familia = '$this->filtro_familia'";
      }

      if($this->filtro_familia != '' && $this->filtro_grupo != ''){
        $this->customWhereIn .= " AND id_grupo = '$this->filtro_grupo'";
      }

      if($this->filtro_familia != '' && $this->filtro_grupo != '' && $this->filtro_subgrupo != ''){
        $this->customWhereIn .= " AND id_subgrupo = '$this->filtro_subgrupo'";
      }

      if(!empty($this->arrayItemsJSON)){
        foreach($this->arrayItemsJSON as $indice => $id_item){
          $itemsIn .= ($itemsIn == "")? "id = '$id_item'" : " OR id = '$id_item'";
        }
        $this->customWhereIn .= ($itemsIn<>'')? " AND ($itemsIn)" : "" ;
      }

    }

    /**
     * @method getReportInfo consultar ls informacion de las requisiciones
     */
    public function getReportInfo(){

      $sql = "SELECT
                id,
                codigo,
                familia,
                grupo,
                subgrupo,
                nombre_equipo AS nombre,
                unidad_medida AS unidad,
                IF(inventariable='true','Si','No') AS inventariable
              FROM
                items 
              WHERE
                activo = 1
              AND id_empresa = $this->id_empresa
                $this->customWhereIn ";
      $query = $this->mysql->query($sql);
      while ($row=$this->mysql->fetch_assoc($query)) {
        $items[$row['id']] = $row;
      }

      $whereItems = "id_item='".implode("' OR id_item='", array_keys($items))."'";

      $sql = "SELECT 
                    id_item,
                    codigo_item,
                    nombre_item,
                    id_item_materia_prima,
                    codigo_item_materia_prima,
                    code_bar_item_materia_prima,
                    nombre_item_materia_prima,
                    cantidad_item_materia_prima AS cantidad
                  FROM 
                    items_recetas
                  WHERE ($whereItems)
                  ";
      $query = $this->mysql->query($sql);
      while ($row=$this->mysql->fetch_assoc($query)) {
        $whereItems .= ($whereItems=='')? " id_item='$row[id_item_materia_prima]'" : " OR id_item='$row[id_item_materia_prima]'" ;
        if ($this->filtro_receta=='con_receta' || $this->filtro_receta=='') {
          $items[$row['id_item']]['receta'][]=$row;
        }
        else{
          unset($items[$row['id_item']]);
        }

      }
      
      $sql = "SELECT 
                  id_item,
                  costos,
                  cantidad,
                  precio_venta,
                  id_sucursal,
                  sucursal,
                  id_ubicacion,
                  ubicacion
                FROM 
                  inventario_totales
                WHERE activo=1 
                AND id_empresa=$this->id_empresa 
                AND ($whereItems)
                $this->customWhereUbication
                ";
      $query = $this->mysql->query($sql);
      while ($row=$this->mysql->fetch_assoc($query)) {
        $this->sucursalInfo[$row['id_sucursal']] =   $row['sucursal'];
        $inventory[$row['id_item']][$row['id_sucursal']][$row['id_ubicacion']]=$row;
      }
      
      foreach ($items as $itemId => $itemsValues) {  

        if (isset($itemsValues['receta'])) {
              foreach ($itemsValues['receta'] as $key => $recipeValues) {
                foreach ($inventory[$recipeValues['id_item_materia_prima']] as $id_sucursal_r => $arrBod_r) {
                  foreach ($arrBod_r as $id_bodega_r => $arrayResult_r) {
                      $this->sucursalInfo[$id_sucursal] =   $arrayResult_r['sucursal'];
                      $items[$itemId]['ubicacion'][$id_sucursal_r][$id_bodega_r]["sucursal"] = $arrayResult_r['sucursal'];
                      $items[$itemId]['ubicacion'][$id_sucursal_r][$id_bodega_r]["bodega"] = $arrayResult_r['ubicacion'];
                      $items[$itemId]['ubicacion'][$id_sucursal_r][$id_bodega_r]["cantidad"] = $arrayResult['cantidad'];
                      $items[$itemId]['ubicacion'][$id_sucursal_r][$id_bodega_r]["costo"] += ($arrayResult_r['costos']*$recipeValues['cantidad']);
                  }
                }

              }
            }
            else{
                foreach ($inventory[$itemId] as $id_sucursal => $arrBod) {
                  foreach ($arrBod as $id_bodega => $arrayResult) {
                      $this->sucursalInfo[$id_sucursal] =  $arrBod['sucursal'];
                                 
                      $items[$itemId]['ubicacion'][$id_sucursal][$id_bodega] = 
                                                                                  [
                                                                                    "sucursal" => $arrayResult['sucursal'],
                                                                                    "bodega"   => $arrayResult['ubicacion'],
                                                                                    "costo"    => $arrayResult['costos'],
                                                                                    "cantidad" => $arrayResult['cantidad']
                                                                                  ]; 

                  }  // END FOREACH   
                }  // END FOREACH   
            } // END ELSE

      }

      $this->reportData = $items;

    }

    /**
     * getReportContent armar el informe para la vista en la app y pdf
     * @return str body informe generado
     */
    public function getReportContent(){
      if($this->IMPRIME_XLS == "true"){      
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=Informe_Inventario_Costo_".date("Y_m_d").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
      }

      foreach ($this->reportData as $itemId => $itemsValues) {
          $tbody .= "<tr class='tbody' >";
          $trRecipe = '';
        foreach ($itemsValues as $row => $value) {
          if (($row=="receta" && $this->filtro_receta_detalle=='No') || $row=="sucursal" ) { continue; }

          if ($row=="ubicacion") {
            foreach ($value as $id_sucursal => $arrBod) {
              
              $tbody .= "<td>". $this->sucursalInfo[$id_sucursal]."</td>";
              foreach ($arrBod as $id_bodega => $arrayResult) {
                $header["Sucursal $id_sucursal"] = 'Sucursal';
                $header["Bodega $id_bodega"]     = 'Bodega';
                $header["Costo $id_bodega"]      = 'Costo';
                $header["Cantidad $id_bodega"]   = 'Cantidad';

                $tbody .= "<td>$arrayResult[bodega]</td>
                            <td>".number_format($arrayResult[costo],0,$this->separador_decimales,$this->separador_miles)."</td>
                            <td>$arrayResult[cantidad]</td>";

              }
            }
          }
          else if($row=="receta" && $this->filtro_receta_detalle=='Si'){
              // $tbody .= "</tr>";
              foreach ($value as $key => $recipie) {
                $trRecipe .= "<tr>
                              <td>$recipie[codigo_item_materia_prima]</td>
                              <td>$recipie[nombre_item_materia_prima]</td>
                              <td>$recipie[cantidad]</td>
                            </tr>";
              }
          } 
          else{
            $header[$row] = $row;
            $tbody .= "<td>$value</td>";
          }
        }
        $tbody .= ($trRecipe<>'')? "</tr>
                                    <tr>
                                      <td><b>Receta</td>
                                    </tr>
                                    <tr>
                                      <td><b>Codigo</td>
                                      <td><b>Nombre</td>
                                      <td><b>Cantidad</td>
                                    </tr>
                                    $trRecipe" : "</tr>";
      }
      foreach ($header as $colName => $value) {
        $header .="<td>$value</td>";
      }
      ?>
      <style>
        #RecibidorInforme_inventario_costo{
            min-width: 1015px;
            width: auto !important;
          }
        .tableInforme{
          font-size       : 12px;
          width           : 100%;
          margin-top      : 20px;
          border-collapse : collapse;
        }

        .tableInforme .thead td{
          background     : #999;
          height         : 25px;
          font-size      : 14px;
          color          : #FFF;
          font-weight    : bold;
          text-transform :capitalize;
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
                <tr><td style="font-size:13px;"><b>Inventario detallado de costos</b><br> <?php echo $subtitulo_cabecera; ?></td></tr>
              </table>
              <table class="tableInforme" style="width:1015px;border-collapse:collapse;">
                  <tr class="thead">
                    <?= $header ?>
                  </tr>
                  <?= $tbody ?>
              </table>
            </div>
          </div>
        </div>
        <br>
      </body>
      <?php     

      if($this->IMPRIME_PDF == 'true'){
        // exit;
        $texto = ob_get_contents();
        $documento = "Informe_Inventario_Consolidado_" . date('Y_m_d');
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
        $mpdf->SetTitle('Informe Inventario Consolidado');
        $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
        $mpdf->WriteHTML(utf8_encode($texto));

        if($PDF_GUARDA == "true"){
          $mpdf->Output($documento.".pdf",'D');
        } else{
          $mpdf->Output($documento.".pdf",'I');
        }
        exit;
      }
    }

    /**
     * @method generate Generar el informe
     */
    public function generate(){
      $this->getCustomFiltres();
      $this->getReportInfo();
      $this->getReportContent();
      
    }
  }

  $objectInform = new InformeInventarioConsolidado(
                      $IMPRIME_XLS,
                      $IMPRIME_PDF,
                      $sucursal,
                      $bodega,
                      $separador_miles,
                      $separador_decimale,
                      $filtro_receta,
                      $filtro_receta_detalle,
                      $filtro_familia,
                      $filtro_grupo,
                      $filtro_subgrupo,
                      $arrayItemsJSON,
                      $mysql);
  $objectInform->generate();
?>
