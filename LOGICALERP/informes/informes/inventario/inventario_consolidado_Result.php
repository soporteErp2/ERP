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
     * @param dat $MyInformeFiltroFechaInicio   Fecha inicial del informe
     * @param dat $MyInformeFiltroFechaFinal    Fecha final del informe
     * @param int $sucursal                     Filtro por sucursal
     * @param int $bodega                       Filtro por bodega
     * @param int $filtro_familia               Filtro por familia
     * @param int $filtro_grupo                 Filtro por grupo
     * @param int $filtro_subgrupo              Filtro por subgrupo
     * @param arr $arrayItemsJSON               Filtro por items
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$bodega,$filtro_familia,$filtro_grupo,$filtro_subgrupo,$arrayItemsJSON,$mysql){
      $this->IMPRIME_HTML               = $IMPRIME_HTML;
      $this->IMPRIME_XLS                = $IMPRIME_XLS;
      $this->IMPRIME_PDF                = $IMPRIME_PDF;
      $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
      $this->sucursal                   = $sucursal;
      $this->bodega                     = $bodega;
      $this->filtro_familia             = $filtro_familia;
      $this->filtro_grupo               = $filtro_grupo;
      $this->filtro_subgrupo            = $filtro_subgrupo;
      $this->arrayItemsJSON             = json_decode($arrayItemsJSON);
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
        $fechaInicio = date('Y-m-d',strtotime($this->MyInformeFiltroFechaInicio."- 1 month"));
        $fechaFin = $this->MyInformeFiltroFechaInicio;

        $this->whereFechas = " WHERE fecha_inventario BETWEEN '$fechaInicio 00:00:00' AND '$fechaFin 23:59:59'";
      }

      if($this->sucursal != '' && $this->sucursal != 'global'){
        $whereSucursal = " AND ITH.id_sucursal = '$this->sucursal'";
      }

      if($this->bodega != '' && $this->bodega != 'global'){
        $whereBodega = " AND ITH.id_bodega = '$this->bodega'";
      }

      if($this->filtro_familia != ''){
        $whereFamilia = " AND I.id_familia = '$this->filtro_familia'";
      }

      if($this->filtro_familia != '' && $this->filtro_grupo != ''){
        $whereGrupo = " AND I.id_grupo = '$this->filtro_grupo'";
      }

      if($this->filtro_familia != '' && $this->filtro_grupo != '' && $this->filtro_subgrupo != ''){
        $whereSubgrupo = " AND I.id_subgrupo = '$this->filtro_subgrupo'";
      }

      if(!empty($this->arrayItemsJSON)){
        foreach($this->arrayItemsJSON as $indice => $id_item){
          $itemsIn .= ($itemsIn == "")? "I.id = '$id_item'" : " OR I.id = '$id_item'";
        }
        $whereItemsIn  .= " AND ($itemsIn)";
      }

      $this->customWhereIn = $whereSucursal.$whereBodega.$whereFamilia.$whereGrupo.$whereSubgrupo.$whereItemsIn;
    }

    /**
     * @method getDocumentoInfo consultar ls informacion de las requisiciones
     */
    public function getDocumentoInfo(){
      //--------------------- DATOS CABECERA DEL INFORME ---------------------//
      //BUSCAMOS LOS DATOS PRINCIPALES DEL INVENTARIO

      //INVENTARIO DENTRO DEL RANGO DE FECHAS
      $sql = "SELECT
                ITH.id,
                ITH.id_item,
                ITH.id_sucursal,
                ITH.sucursal,
                ITH.id_bodega,
                ITH.bodega,
                ITH.fecha_inventario,
                ITH.codigo,
                ITH.nombre_equipo,
                I.unidad_medida,
                ITH.cantidad
              FROM
                inventario_totales_historico AS ITH
              LEFT JOIN
                items AS I
              ON
                I.id = ITH.id_item
              INNER JOIN(
            							SELECT MAX(fecha_inventario)fecha_maxima, id_item
            							FROM inventario_totales_historico
                          $this->whereFechas
            							GROUP BY id_bodega,id_item
              					) ITHM
              ON
                ITHM.id_item = ITH.id_item AND ITHM.fecha_maxima = ITH.fecha_inventario
              WHERE
                ITH.activo = 1
              AND
                ITH.id_empresa = $this->id_empresa
                $this->customWhereIn
              GROUP BY ITH.id";
      $query = $this->mysql->query($sql,$this->mysql->link);
      while($row = $this->mysql->fetch_array($query)){
        $itemfuera .= ($itemsfuera == "")? "ITH.id_item = '$id_item'" : " OR ITH.id_item = '$id_item'";

        $this->arrayDoc[$row['id_sucursal']][$row['id_bodega']][$row['id_item']] = array(
                                                                                          'sucursal'         => $row['sucursal'],
                                                                                          'bodega'           => $row['bodega'],
                                                                                          'fecha_inventario' => $row['fecha_inventario'],
                                                                                          'codigo'           => $row['codigo'],
                                                                                          'nombre_equipo'    => $row['nombre_equipo'],
                                                                                          'unidad_medida'    => $row['unidad_medida'],
                                                                                          'cantidad'         => $row['cantidad']
                                                                                        );
      }
    }

    /**
     * getExcel armar el informe para excel
     * @return str body informe generado
     */
    public function getExcel(){
      $style         = "";
      $totalSalidas  = 0;
      $totalEntradas = 0;

      foreach($this->arrayDoc as $id_sucursal => $arraySucursal){
        foreach($arraySucursal as $id_bodega => $arrayBodega){
          foreach($arrayBodega as $id_item => $result){
            //BUSCAR FV
            $sqlVentas = "SELECT
              							SUM(VFI.cantidad) as cantidad
                					FROM
                            ventas_facturas_inventario AS VFI,
                						ventas_facturas AS VF
                					WHERE VF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
              						AND VF.id_empresa = '$this->id_empresa'
              						AND VF.id_sucursal = '$id_sucursal'
              						AND VF.id_bodega = '$id_bodega'
              						AND (VF.estado  =  1 OR VF.estado = 4)
              						AND VF.id = VFI.id_factura_venta
              						AND VF.activo = 1
              						AND VFI.id_inventario = '$id_item'
              						AND VFI.activo = 1
                					GROUP BY VFI.id_factura_venta, VFI.costo_inventario
                					ORDER BY VF.fecha_inicio ASC";
            $queryVentas = $this->mysql->query($sqlVentas,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryVentas)){
              $totalSalidas += $row['cantidad'];
            }

            //BUSCAR RM
            $sqlVentas = "SELECT
              							SUM(VFI.cantidad) as cantidad
                					FROM
                            ventas_remisiones_inventario AS VFI,
                						ventas_remisiones AS VF
                					WHERE VF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
              						AND VF.id_empresa = '$this->id_empresa'
              						AND VF.id_sucursal = '$id_sucursal'
              						AND VF.id_bodega = '$id_bodega'
              						AND (VF.estado = 1 OR VF.estado = 2 OR VF.estado = 4)
              						AND VF.id = VFI.id_remision_venta
              						AND VF.activo = 1
              						AND VFI.id_inventario = '$id_item'
              						AND VFI.activo = 1
                					GROUP BY VFI.id_remision_venta, VFI.costo_inventario
                					ORDER BY VF.fecha_inicio ASC";
            $queryVentas = $this->mysql->query($sqlVentas,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryVentas)){
              $totalSalidas += $row['cantidad'];
            }

            //BUSCAR FC
            $sqlCompras =  "SELECT
                							SUM(CFI.cantidad) as cantidad
                  					FROM
                              compras_facturas_inventario AS CFI,
                  						compras_facturas AS CF
                  					WHERE CF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                						AND CF.id_empresa = '$this->id_empresa'
                						AND CF.id_sucursal = '$id_sucursal'
                						AND CF.id_bodega = '$id_bodega'
                						AND (CF.estado = 1 OR CF.estado = 4)
                						AND CF.id = CFI.id_factura_compra
                						AND CF.activo = 1
                						AND CFI.id_inventario = '$id_item'
                						AND CFI.check_opcion_contable = ''
                						AND CFI.activo = 1
                  					GROUP BY CFI.id_factura_compra, CFI.costo_unitario
                  					ORDER BY CF.fecha_inicio ASC";
            $queryCompras = $this->mysql->query($sqlCompras,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryCompras)){
              $whereFC .= ($whereFC == '')? "id_factura_compra = " . $rowCompras['id'] : " OR id_factura_compra = " . $rowCompras['id'];
              $totalEntradas += $row['cantidad'];
            }

            //CONSULTAR LAS ENTRADAS DE ALMACEN QUE NO SE DEBEN MOSTRAR POR QUE SE FACTURARON
            $whereEA = ($whereEA <> '')? " AND $whereEA " : "";
          	$sql = "SELECT id_consecutivo_referencia FROM compras_facturas_inventario WHERE activo = 1 AND ($whereFC) AND id_consecutivo_referencia > 0";
          	$query = $this->mysql->query($sql,$this->mysql->link);
          	while($row = $this->mysql->fetch_array($query)){
          		$whereEA .= ($whereEA == '')? "CF.id <> " . $row['id_consecutivo_referencia'] : " AND CF.id <> " . $row['id_consecutivo_referencia'];
          	}

            //BUSCAR EA
            $sqlCompras =  "SELECT
                							SUM(CFI.cantidad) as cantidad
                  					FROM
                              compras_entrada_almacen_inventario AS CFI,
                  						compras_entrada_almacen AS CF
                  					WHERE CF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                						AND CF.id_empresa = '$this->id_empresa'
                						AND CF.id_sucursal = '$id_sucursal'
                						AND CF.id_bodega = '$id_bodega'
                						AND (CF.estado = 1 OR CF.estado = 2 OR CF.estado = 4)
                						AND CF.id = CFI.id_entrada_almacen
                						AND CF.activo = 1
                						AND CFI.id_inventario = '$id_item'
                						$whereEA
                						AND CFI.activo = 1
                  					GROUP BY CFI.id_entrada_almacen, CFI.costo_unitario
                  					ORDER BY CF.fecha_registro ASC";
          	$queryCompras = $this->mysql->query($sqlCompras,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryCompras)){
              $totalEntradas += $row['cantidad'];
            }

            //BUSCAR DV
            $sqlDevVentas =  "SELECT
                  							SUM(DVI.cantidad) as cantidad
                    					FROM
                                devoluciones_venta_inventario AS DVI,
                    						devoluciones_venta AS DV
                    					WHERE DV.fecha_finalizacion BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                  						AND DV.id_empresa = '$this->id_empresa'
                  						AND DV.id_sucursal = '$id_sucursal'
                  						AND DV.id_bodega = '$id_bodega'
                  						AND (DV.estado = 1 OR DV.estado = 4)
                  						AND DV.id = DVI.id_devolucion_venta
                  						AND DV.activo = 1
                  						AND DVI.id_inventario = '$id_item'
                  						AND DVI.activo = 1
                    					GROUP BY DVI.id_devolucion_venta, DVI.costo_inventario
                    					ORDER BY DV.fecha_finalizacion ASC";
            $queryDevVentas = $this->mysql->query($sqlDevVentas,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryDevVentas)){
              $totalEntradas += $row['cantidad'];
            }

            //BUSCAR DC
            $sqlDevCompras = "SELECT
                  							SUM(DCI.cantidad) as cantidad
                    					FROM
                                devoluciones_compra_inventario AS DCI,
                    						devoluciones_compra AS DC
                    					WHERE DC.fecha_finalizacion BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                  						AND DC.id_empresa = '$this->id_empresa'
                  						AND DC.id_sucursal = '$id_sucursal'
                  						AND DC.id_bodega = '$filtro_bodega'
                  						AND (DC.estado = 1 OR DC.estado = 4)
                  						AND DC.id = DCI.id_devolucion_compra
                  						AND DC.activo = 1
                  						AND DCI.id_inventario = '$id_item'
                  						AND DCI.activo = 1
                    					GROUP BY DCI.id_devolucion_compra, DCI.costo_unitario
                    					ORDER BY DC.fecha_finalizacion ASC";
            $queryDevCompras = $this->mysql->query($sqlDevCompras,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryDevCompras)){
              $totalSalidas += $row['cantidad'];
            }

            //BUSCAR TI
            $sqlInventario = "SELECT
                        				SUM(ITU.cantidad) AS cantidad
                        			FROM inventario_traslados AS IT
                              INNER JOIN inventario_traslados_unidades AS ITU ON ITU.id_traslado = IT.id
                        			WHERE IT.activo = 1
                        			AND IT.id_empresa = $this->id_empresa
                        			AND IT.id_sucursal = $id_sucursal
                        			AND IT.id_bodega = $id_bodega
                        			AND IT.fecha_documento BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                        			AND IT.estado = 1
                        			AND ITU.id_inventario = $id_item
                        			GROUP BY IT.id,ITU.costo_unitario
                        			ORDER BY IT.fecha_documento,IT.consecutivo DESC";
            $queryInventario = $this->mysql->query($sqlInventario,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryInventario)){
              $totalSalidas += $row['cantidad'];
            }

            $sqlInventario = "SELECT
                        				SUM(ITU.cantidad) AS cantidad
                        			FROM inventario_traslados AS IT
                              INNER JOIN inventario_traslados_unidades AS ITU ON ITU.id_traslado = IT.id
                        			WHERE IT.activo = 1
                        			AND IT.id_empresa = $this->id_empresa
                        			AND IT.id_sucursal_traslado = $id_sucursal
                        			AND IT.id_bodega_traslado = $id_bodega
                        			AND IT.fecha_documento BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                        			AND IT.estado = 1
                        			AND ITU.id_inventario = $id_item
                        			GROUP BY IT.id,ITU.costo_unitario
                        			ORDER BY IT.fecha_documento,IT.consecutivo DESC";
            $queryInventario = $this->mysql->query($sqlInventario,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryInventario)){
              $totalEntradas += $row['cantidad'];
            }

            //CUERPO DEL INFORME
            $bodyTable .=  "<tr style='height:20px; $style'>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[sucursal]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[bodega]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[codigo]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[nombre_equipo]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[unidad_medida]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[cantidad]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$totalEntradas</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$totalSalidas</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>".(($result['cantidad'] + $totalEntradas) - $totalSalidas)."</td>
                            </tr>";

            if($style == "background-color:#d0c4c4;"){
              $style = "";
            }
            else{
              $style = "background-color:#d0c4c4;";
            }

            $totalSalidas  = 0;
            $totalEntradas = 0;
            $whereFC       = "";
            $whereEA       = "";
          }
        }
      }
      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=Informe_Inventario_Consolidado_".date("Y_m_d").".xls");
      header("Pragma: no-cache");
      header("Expires: 0");
      ?>
      <table>
        <tr colspan="9">
          <td><b><?php echo $_SESSION['NOMBREEMPRESA']; ?></b></td>
        </tr>
        <tr colspan="9">
          <td><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td>
        </tr>
        <tr colspan="9">
          <td><b>Informe Inventario Consolidado</td>
        </tr>
        <tr colspan="9">
          <td>Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td>
        </tr>
      </table>
      <table>
        <tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
          <td style='width:70px;text-align:center;'><b>SUCURSAL</b></td>
          <td style='width:70px;text-align:center;'><b>BODEGA</b></td>
          <td style='width:70px;text-align:center;'><b>CODIGO ITEM</b></td>
          <td style='width:70px;text-align:center;'><b>NOMBRE ITEM</b></td>
          <td style='width:70px;text-align:center;'><b>UNIDAD DE MEDIDA</b></td>
          <td style='width:70px;text-align:center;'><b>CANTIDAD</b></td>
          <td style='width:70px;text-align:center;'><b>COMPRAS</b></td>
          <td style='width:70px;text-align:center;'><b>SALIDAS</b></td>
          <td style='width:70px;text-align:center;'><b>INVENTARIO FINAL</b></td>
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
                        <td style='width:70px;text-align:center;'><b>BODEGA</b></td>
                        <td style='width:70px;text-align:center;'><b>CODIGO ITEM</b></td>
                        <td style='width:70px;text-align:center;'><b>NOMBRE ITEM</b></td>
                        <td style='width:70px;text-align:center;'><b>UNIDAD DE MEDIDA</b></td>
                        <td style='width:70px;text-align:center;'><b>CANTIDAD</b></td>
                        <td style='width:70px;text-align:center;'><b>COMPRAS</b></td>
                        <td style='width:70px;text-align:center;'><b>SALIDAS</b></td>
                        <td style='width:70px;text-align:center;'><b>INVENTARIO FINAL</b></td>
                      </tr>";

      $style         = "";
      $totalSalidas  = 0;
      $totalEntradas = 0;

      foreach($this->arrayDoc as $id_sucursal => $arraySucursal){
        foreach($arraySucursal as $id_bodega => $arrayBodega){
          foreach($arrayBodega as $id_item => $result){
            //BUSCAR FV
            $sqlVentas = "SELECT
              							SUM(VFI.cantidad) as cantidad
                					FROM
                            ventas_facturas_inventario AS VFI,
                						ventas_facturas AS VF
                					WHERE VF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
              						AND VF.id_empresa = '$this->id_empresa'
              						AND VF.id_sucursal = '$id_sucursal'
              						AND VF.id_bodega = '$id_bodega'
              						AND (VF.estado  =  1 OR VF.estado = 4)
              						AND VF.id = VFI.id_factura_venta
              						AND VF.activo = 1
              						AND VFI.id_inventario = '$id_item'
              						AND VFI.activo = 1
                					GROUP BY VFI.id_factura_venta, VFI.costo_inventario
                					ORDER BY VF.fecha_inicio ASC";
            $queryVentas = $this->mysql->query($sqlVentas,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryVentas)){
              $totalSalidas += $row['cantidad'];
            }

            //BUSCAR RM
            $sqlVentas = "SELECT
              							SUM(VFI.cantidad) as cantidad
                					FROM
                            ventas_remisiones_inventario AS VFI,
                						ventas_remisiones AS VF
                					WHERE VF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
              						AND VF.id_empresa = '$this->id_empresa'
              						AND VF.id_sucursal = '$id_sucursal'
              						AND VF.id_bodega = '$id_bodega'
              						AND (VF.estado = 1 OR VF.estado = 2 OR VF.estado = 4)
              						AND VF.id = VFI.id_remision_venta
              						AND VF.activo = 1
              						AND VFI.id_inventario = '$id_item'
              						AND VFI.activo = 1
                					GROUP BY VFI.id_remision_venta, VFI.costo_inventario
                					ORDER BY VF.fecha_inicio ASC";
            $queryVentas = $this->mysql->query($sqlVentas,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryVentas)){
              $totalSalidas += $row['cantidad'];
            }

            //BUSCAR FC
            $sqlCompras =  "SELECT
                							SUM(CFI.cantidad) as cantidad
                  					FROM
                              compras_facturas_inventario AS CFI,
                  						compras_facturas AS CF
                  					WHERE CF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                						AND CF.id_empresa = '$this->id_empresa'
                						AND CF.id_sucursal = '$id_sucursal'
                						AND CF.id_bodega = '$id_bodega'
                						AND (CF.estado = 1 OR CF.estado = 4)
                						AND CF.id = CFI.id_factura_compra
                						AND CF.activo = 1
                						AND CFI.id_inventario = '$id_item'
                						AND CFI.check_opcion_contable = ''
                						AND CFI.activo = 1
                  					GROUP BY CFI.id_factura_compra, CFI.costo_unitario
                  					ORDER BY CF.fecha_inicio ASC";
            $queryCompras = $this->mysql->query($sqlCompras,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryCompras)){
              $whereFC .= ($whereFC == '')? "id_factura_compra = " . $rowCompras['id'] : " OR id_factura_compra = " . $rowCompras['id'];
              $totalEntradas += $row['cantidad'];
            }

            //CONSULTAR LAS ENTRADAS DE ALMACEN QUE NO SE DEBEN MOSTRAR POR QUE SE FACTURARON
            $whereEA = ($whereEA <> '')? " AND $whereEA " : "";
          	$sql = "SELECT id_consecutivo_referencia FROM compras_facturas_inventario WHERE activo = 1 AND ($whereFC) AND id_consecutivo_referencia > 0";
          	$query = $this->mysql->query($sql,$this->mysql->link);
          	while($row = $this->mysql->fetch_array($query)){
          		$whereEA .= ($whereEA == '')? "CF.id <> " . $row['id_consecutivo_referencia'] : " AND CF.id <> " . $row['id_consecutivo_referencia'];
          	}

            //BUSCAR EA
            $sqlCompras =  "SELECT
                							SUM(CFI.cantidad) as cantidad
                  					FROM
                              compras_entrada_almacen_inventario AS CFI,
                  						compras_entrada_almacen AS CF
                  					WHERE CF.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                						AND CF.id_empresa = '$this->id_empresa'
                						AND CF.id_sucursal = '$id_sucursal'
                						AND CF.id_bodega = '$id_bodega'
                						AND (CF.estado = 1 OR CF.estado = 2 OR CF.estado = 4)
                						AND CF.id = CFI.id_entrada_almacen
                						AND CF.activo = 1
                						AND CFI.id_inventario = '$id_item'
                						$whereEA
                						AND CFI.activo = 1
                  					GROUP BY CFI.id_entrada_almacen, CFI.costo_unitario
                  					ORDER BY CF.fecha_registro ASC";
          	$queryCompras = $this->mysql->query($sqlCompras,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryCompras)){
              $totalEntradas += $row['cantidad'];
            }

            //BUSCAR DV
            $sqlDevVentas =  "SELECT
                  							SUM(DVI.cantidad) as cantidad
                    					FROM
                                devoluciones_venta_inventario AS DVI,
                    						devoluciones_venta AS DV
                    					WHERE DV.fecha_finalizacion BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                  						AND DV.id_empresa = '$this->id_empresa'
                  						AND DV.id_sucursal = '$id_sucursal'
                  						AND DV.id_bodega = '$id_bodega'
                  						AND (DV.estado = 1 OR DV.estado = 4)
                  						AND DV.id = DVI.id_devolucion_venta
                  						AND DV.activo = 1
                  						AND DVI.id_inventario = '$id_item'
                  						AND DVI.activo = 1
                    					GROUP BY DVI.id_devolucion_venta, DVI.costo_inventario
                    					ORDER BY DV.fecha_finalizacion ASC";
            $queryDevVentas = $this->mysql->query($sqlDevVentas,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryDevVentas)){
              $totalEntradas += $row['cantidad'];
            }

            //BUSCAR DC
            $sqlDevCompras = "SELECT
                  							SUM(DCI.cantidad) as cantidad
                    					FROM
                                devoluciones_compra_inventario AS DCI,
                    						devoluciones_compra AS DC
                    					WHERE DC.fecha_finalizacion BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                  						AND DC.id_empresa = '$this->id_empresa'
                  						AND DC.id_sucursal = '$id_sucursal'
                  						AND DC.id_bodega = '$filtro_bodega'
                  						AND (DC.estado = 1 OR DC.estado = 4)
                  						AND DC.id = DCI.id_devolucion_compra
                  						AND DC.activo = 1
                  						AND DCI.id_inventario = '$id_item'
                  						AND DCI.activo = 1
                    					GROUP BY DCI.id_devolucion_compra, DCI.costo_unitario
                    					ORDER BY DC.fecha_finalizacion ASC";
            $queryDevCompras = $this->mysql->query($sqlDevCompras,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryDevCompras)){
              $totalSalidas += $row['cantidad'];
            }

            //BUSCAR TI
            $sqlInventario = "SELECT
                        				SUM(ITU.cantidad) AS cantidad
                        			FROM inventario_traslados AS IT
                              INNER JOIN inventario_traslados_unidades AS ITU ON ITU.id_traslado = IT.id
                        			WHERE IT.activo = 1
                        			AND IT.id_empresa = $this->id_empresa
                        			AND IT.id_sucursal = $id_sucursal
                        			AND IT.id_bodega = $id_bodega
                        			AND IT.fecha_documento BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                        			AND IT.estado = 1
                        			AND ITU.id_inventario = $id_item
                        			GROUP BY IT.id,ITU.costo_unitario
                        			ORDER BY IT.fecha_documento,IT.consecutivo DESC";
            $queryInventario = $this->mysql->query($sqlInventario,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryInventario)){
              $totalSalidas += $row['cantidad'];
            }

            $sqlInventario = "SELECT
                        				SUM(ITU.cantidad) AS cantidad
                        			FROM inventario_traslados AS IT
                              INNER JOIN inventario_traslados_unidades AS ITU ON ITU.id_traslado = IT.id
                        			WHERE IT.activo = 1
                        			AND IT.id_empresa = $this->id_empresa
                        			AND IT.id_sucursal_traslado = $id_sucursal
                        			AND IT.id_bodega_traslado = $id_bodega
                        			AND IT.fecha_documento BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'
                        			AND IT.estado = 1
                        			AND ITU.id_inventario = $id_item
                        			GROUP BY IT.id,ITU.costo_unitario
                        			ORDER BY IT.fecha_documento,IT.consecutivo DESC";
            $queryInventario = $this->mysql->query($sqlInventario,$this->mysql->link);
            while($row = $this->mysql->fetch_array($queryInventario)){
              $totalEntradas += $row['cantidad'];
            }

            //CUERPO DEL INFORME
            $bodyTable .=  "<tr style='height:20px; $style'>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[sucursal]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[bodega]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[codigo]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[nombre_equipo]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[unidad_medida]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$result[cantidad]</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$totalEntradas</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>$totalSalidas</td>
                              <td style='width:70px; text-align:center; font-size:11px;'>".(($result['cantidad'] + $totalEntradas) - $totalSalidas)."</td>
                            </tr>";

            if($style == "background-color:#d0c4c4;"){
              $style = "";
            }
            else{
              $style = "background-color:#d0c4c4;";
            }

            $totalSalidas  = 0;
            $totalEntradas = 0;
            $whereFC       = "";
            $whereEA       = "";
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
                <tr><td style="font-size:13px;"><b>Informe Inventario Consolidado</b><br> <?php echo $subtitulo_cabecera; ?></td></tr>
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
      $documento = "Informe_Inventario_Consolidado_" . date('Y_m_d');
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

  $objectInform = new InformeInventarioConsolidado($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$bodega,$filtro_familia,$filtro_grupo,$filtro_subgrupo,$arrayItemsJSON,$mysql);
  $objectInform->generate();
?>
