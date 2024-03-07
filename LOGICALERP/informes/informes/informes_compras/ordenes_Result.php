<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();
  /**
   * @class InformeOrdenesCompra
   */
  class InformeOrdenesCompra{

    public $IMPRIME_HTML               = '';
    public $IMPRIME_XLS                = '';
    public $IMPRIME_PDF                = '';
    public $MyInformeFiltroFechaInicio = '';
    public $MyInformeFiltroFechaFinal  = '';
    public $sucursal                   = '';
    public $bodega                     = '';
    public $tipo_orden_compra          = '';
    public $estado                     = '';
    public $item                       = '';
    public $autorizado                 = '';
    public $arraytercerosJSON          = '';
    public $arrayempleadosJSON         = '';
    public $arrayccosJSON              = '';
    public $mysql                      = '';
    public $id_empresa                 = '';
    public $customWhere                = '';
    public $arrayDoc                   = array();
    public $arrayDocItems              = array();

    /**
     * [__construct]
     * @param str $IMPRIME_HTML                 Generar en HTML
     * @param str $IMPRIME_XLS                  Generar en EXCEL
     * @param str $IMPRIME_PDF                  Generar en PDF
     * @param dat $MyInformeFiltroFechaInicio   Fecha inicial del informe
     * @param dat $MyInformeFiltroFechaFinal    Fecha final del informe
     * @param int $sucursal                     Filtro por sucursal
     * @param int $bodega                       Filtro por bodega
     * @param srt $tipo_orden_compra            Filtro por tipo de documento
     * @param int $estado                       Filtro por estado
     * @param srt $item                         Filtro para discriminar items del documento
     * @param srt $autorizado                   Filtro por autorizaciones
     * @param arr $arraytercerosJSON            Filtro por terceros
     * @param arr $arrayempleadosJSON           Filtro por empleados
     * @param arr $arrayccosJSON                Filtro por centro de costos
     * @param obj $mysql                        Objeto de conexion a la base de datos
     */
    function __construct($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$bodega,$tipo_orden_compra,$estado,$item,$autorizado,$arraytercerosJSON,$arrayempleadosJSON,$arrayccosJSON,$mysql){
      $this->IMPRIME_HTML               = $IMPRIME_HTML;
      $this->IMPRIME_XLS                = $IMPRIME_XLS;
      $this->IMPRIME_PDF                = $IMPRIME_PDF;
      $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
      $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
      $this->sucursal                   = $sucursal;
      $this->bodega                     = $bodega;
      $this->tipo_orden_compra          = $tipo_orden_compra;
      $this->estado                     = $estado;
      $this->item                       = $item;
      $this->autorizado                 = $autorizado;
      $this->arraytercerosJSON          = json_decode($arraytercerosJSON);
      $this->arrayempleadosJSON         = json_decode($arrayempleadosJSON);
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
        $this->showError('Debe Seleccionar las fechas del informe');
      }
      else if($this->MyInformeFiltroFechaFinal != '' && $this->MyInformeFiltroFechaInicio != ''){
        $whereFechas = " AND CO.fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'";
      }

      if($this->sucursal != '' && $this->sucursal != 'global'){
        $whereSucursal = " AND CO.id_sucursal = '$this->sucursal'";
      }

      if($this->bodega != '' && $this->bodega != 'global'){
        $whereBodega = " AND CO.id_bodega = '$this->bodega'";
      }

      if($this->tipo_orden_compra != 'todo'){
        $whereTipoOrden = " AND CO.id_tipo = '$this->tipo_orden_compra'";
      }

      if($this->estado == 'facturado'){
        $whereEstado = " AND CO.pendientes_facturar = 0";
      }
      else if($this->estado == 'pendientefacturado'){
        $whereEstado = " AND CO.pendientes_facturar > 0";
      }

      if($this->autorizado == 'autorizadas'){
        $this->title = "<tr><td style='font-size:11px;'><b>Ordenes Autorizadas</b></td></tr>";
      }
      else if($this->autorizado == 'noautorizadas'){
        $this->title = "<tr><td style='font-size:11px;'><b>Ordenes No Autorizadas</b></td></tr>";
      }

      if(!empty($this->arraytercerosJSON)){
        foreach ($this->arraytercerosJSON as $indice => $id_tercero){
          $terceros .= ($terceros == "")? "CO.id_proveedor = '$id_tercero'" : " OR CO.id_proveedor = '$id_tercero'";
        }
        $whereTerceros .= " AND ($terceros)";
      }

      if(!empty($this->arrayempleadosJSON)){
        foreach($this->arrayempleadosJSON as $indice => $id_empleado){
          $empleados .= ($empleados == '')? "CO.id_usuario = '$id_empleado'" : " OR CO.id_usuario = '$id_empleado'";
        }
        $whereEmpleados .= " AND ($empleados)";
      }

      if(!empty($this->arrayccosJSON)){
        foreach($this->arrayccosJSON as $indice => $codigo_centro_costo){
          $ccos .= ($ccos == "")? "CC.id = '$codigo_centro_costo'" : " OR CC.id = '$codigo_centro_costo'";
        }
        $this->whereCcos .= "AND ($ccos)";
      }

      $this->customWhere = $whereFechas.$whereSucursal.$whereBodega.$whereTipoOrden.$whereEstado.$whereTerceros.$whereEmpleados;
    }

    /**
     * @method getDocumentoInfo consultar ls informacion de las requisiciones
     */
    public function getDocumentoInfo(){
      //-------------------- DATOS CABECERA DE LA FACTURA --------------------//
      //BUSCAMOS LOS DATOS PRINCIPALES DE LAS ORDENES
      $sql = "SELECT
                CO.id,
                CO.sucursal,
                CO.bodega,
                CO.fecha_inicio,
                CO.fecha_vencimiento,
                CO.consecutivo,
                CO.pendientes_facturar,
                CO.proveedor,
                CO.usuario,
                CO.tipo_nombre,
                CO.observacion,
                CO.estado
              FROM
                compras_ordenes AS CO
              WHERE
                CO.activo = 1
              AND
                CO.id_empresa = $this->id_empresa
                $this->customWhere
              GROUP BY
                CO.id";
      $query = $this->mysql->query($sql,$this->mysql->link);
      while($row = $this->mysql->fetch_array($query)){
        $id_doc = $row['id'];

        $whereIdDocs .= ($whereIdDocs == '')? "COI.id_orden_compra = '$id_doc'" : " OR COI.id_orden_compra = '$id_doc'";

        $this->arrayDoc[$id_doc] = array(
                                          'sucursal'            => $row['sucursal'],
                                          'bodega'              => $row['bodega'],
                                          'fecha_inicio'        => $row['fecha_inicio'],
                                          'fecha_vencimiento'   => $row['fecha_vencimiento'],
                                          'consecutivo'         => $row['consecutivo'],
                                          'pendientes_facturar' => $row['pendientes_facturar'],
                                          'proveedor'           => $row['proveedor'],
                                          'usuario'             => $row['usuario'],
                                          'tipo_nombre'         => $row['tipo_nombre'],
                                          'observacion'         => $row['observacion'],
                                          'estado'              => $row['estado']
                                        );
      }

      //--------------------- DATOS CUERPO DE LA FACTURA ---------------------//
      //SI EXISTE FILTRO POR CCOS
      if($this->arrayccosJSON != ""){
        $sql = "SELECT
                  COI.id_orden_compra
                FROM
                  compras_ordenes_inventario AS COI
                LEFT JOIN
                  centro_costos AS CC
                ON
                  CC.id = COI.id_centro_costos
                WHERE
                  COI.activo = 1
                AND
                  ($whereIdDocs)
                  $this->whereCcos
                GROUP BY
                  COI.id_orden_compra";
        $query = $this->mysql->query($sql,$this->mysql->link);
        while($row = $this->mysql->fetch_array($query)){
          $whereOrdenes .= ($whereOrdenes == "")? "COI.id_orden_compra = '$row[id_orden_compra]'" : " OR COI.id_orden_compra = '$row[id_orden_compra]'";
        }
        $whereFinal .= " AND ($whereOrdenes)";
      }
      //SI NO EXISTO FILTRO POR CCOS
      else{
        $whereFinal = "AND ($whereIdDocs)";
      }

      //BUSCAMOS LOS ITEMS DE LAS ORDENES
      $sql = "SELECT
                COI.id,
                COI.id_orden_compra,
                COI.codigo,
                COI.id_unidad_medida,
                COI.nombre_unidad_medida,
                COI.cantidad_unidad_medida,
                COI.nombre,
                COI.cantidad,
                COI.saldo_cantidad,
                COI.costo_unitario,
                COI.tipo_descuento,
                COI.descuento,
                COI.valor_impuesto,
                COI.observaciones,
                CC.codigo AS ccos_codigo,
                CC.nombre AS ccos_nombre,
                CR.consecutivo AS consecutivo_requisicion,
                CR.fecha_inicio AS fecha_requisicion
              FROM
                compras_ordenes_inventario AS COI
              LEFT JOIN
                centro_costos AS CC
              ON
                CC.id = COI.id_centro_costos
              LEFT JOIN
                compras_requisicion_doc_cruce AS CRDC
              ON
                CRDC.id_documento_cruce = COI.id_orden_compra
              LEFT JOIN
                compras_requisicion AS CR
              ON
                CR.id = CRDC.id_requisicion
              WHERE
                COI.activo = 1
                $whereFinal";
      $query = $this->mysql->query($sql,$this->mysql->link);
      while($row = $this->mysql->fetch_array($query)){
        $id    = $row['id'];
        $id_oc = $row['id_orden_compra'];
        $this->arrayDocItems[$id_oc][$id] = array(
                                                    'codigo'                  => $row['codigo'],
                                                    'id_unidad_medida'        => $row['id_unidad_medida'],
                                                    'nombre_unidad_medida'    => $row['nombre_unidad_medida'],
                                                    'cantidad_unidad_medida'  => $row['cantidad_unidad_medida'],
                                                    'nombre'                  => $row['nombre'],
                                                    'cantidad'                => $row['cantidad'],
                                                    'saldo_cantidad'          => $row['saldo_cantidad'],
                                                    'costo_unitario'          => $row['costo_unitario'],
                                                    'tipo_descuento'          => $row['tipo_descuento'],
                                                    'descuento'               => $row['descuento'],
                                                    'valor_impuesto'          => $row['valor_impuesto'],
                                                    'observaciones'           => $row['observaciones'],
                                                    'ccos_codigo'             => $row['ccos_codigo'],
                                                    'ccos_nombre'             => $row['ccos_nombre'],
                                                    'consecutivo_requisicion' => $row['consecutivo_requisicion'],
                                                    'fecha_requisicion'       => $row['fecha_requisicion']
                                                 );
      }

      //SI EXISTE FILTRO POR CCOS ELIMINAMOS ORDENES QUE NO TENGAN ITEMS CON CCOS RELACIONADOS
      foreach($this->arrayDoc as $id_oc => $result){
        if(!array_key_exists($id_oc,$this->arrayDocItems)){
          unset($this->arrayDoc[$id_oc]);
        }
      }

      // CONSULTAR LAS AUTORIZACIONES DEL DOCUMENTO
      $sql = "SELECT
                COI.id_orden_compra,
                COI.tipo_autorizacion
              FROM
                autorizacion_ordenes_compra_area AS COI
              WHERE
                COI.activo = 1
              AND
                COI.id_empresa = $this->id_empresa
                $whereFinal";

      $query = $this->mysql->query($sql,$this->mysql->link);

      while($row = $this->mysql->fetch_array($query)){
        $id_orden_compra   = $row['id_orden_compra'];
        $tipo_autorizacion = ($row['tipo_autorizacion'] != "")? $row['tipo_autorizacion'] : "Por Autorizar";

        $this->arrayDoc[$id_orden_compra]['autorizacion'] = $tipo_autorizacion;
      }

      //SI EXISTE FILTRO POR AUTORIZACIONES ELIMINAMOS ORDENES QUE NO CUMPLAN CON LA CONDICION
      if($this->autorizado != 'todo'){
        foreach($this->arrayDoc as $id_documento => $arrayResult){
          if($this->autorizado == 'autorizadas' && $arrayResult['autorizacion'] != 'Autorizada'){
            unset($this->arrayDoc[$id_documento]);
            unset($this->arrayDocItems[$id_documento]);
          }
          else if($this->autorizado == 'aplazadas' && $arrayResult['autorizacion'] != 'Aplazada'){
            unset($this->arrayDoc[$id_documento]);
            unset($this->arrayDocItems[$id_documento]);
          }
          else if($this->autorizado == 'rechazadas' && $arrayResult['autorizacion'] != 'Rechazada'){
            unset($this->arrayDoc[$id_documento]);
            unset($this->arrayDocItems[$id_documento]);
          }
          else if($this->autorizado == 'porautorizar' && $arrayResult['autorizacion'] != 'Por Autorizar'){
            unset($this->arrayDoc[$id_documento]);
            unset($this->arrayDocItems[$id_documento]);
          }
        }
      }

      //RECORREMOS ORDENES
      foreach($this->arrayDoc as $id_oc => $result){
        //RECORREMOS ITEMS PARA OBTENER SUBTOTALES, DESCUENTOS E IMPUESTOS
        foreach($this->arrayDocItems[$id_oc] as $id_row => $resultItems){
          if($result['estado'] == 3){
            $this->subtotalOrden[$id_oc][$id_row]  = 0;
            $this->descuentoOrden[$id_oc][$id_row] = 0;
            $this->impuestoOrden[$id_oc][$id_row]  = 0;
            $resultItems['saldo_cantidad'] = 0;
          }
          else{
            if($resultItems['tipo_descuento'] == "porcentaje"){
              $this->subtotalOrden[$id_oc][$id_row]   = ($resultItems['costo_unitario'] * $resultItems['cantidad']) - ((($resultItems['costo_unitario'] * $resultItems['cantidad']) * $resultItems['descuento']) / 100);
              $this->descuentoOrden[$id_oc][$id_row]  = (($resultItems['costo_unitario'] * $resultItems['cantidad']) * $resultItems['descuento']) / 100;
            } else{
              $this->subtotalOrden[$id_oc][$id_row]   = ($resultItems['costo_unitario'] * $resultItems['cantidad']) - $resultItems['descuento'];
              $this->descuentoOrden[$id_oc][$id_row]  = $resultItems['descuento'];
            }
            $this->impuestoOrden[$id_oc][$id_row] = ($this->subtotalOrden[$id_oc][$id_row] * $resultItems['valor_impuesto']) / 100;
          }

          if($resultItems['saldo_cantidad'] > 0){
            $this->totalUnidadPendienteOrden[$id_oc] += $resultItems['saldo_cantidad'];
            $this->totalValorPendienteOrden[$id_oc]  += $this->subtotalOrden[$id_oc][$id_row] + $this->impuestoOrden[$id_oc][$id_row];
          }

          //TOTALES DEL PIE DE TABLA
          $this->totalSubtotalOrden[$id_oc]   += $this->subtotalOrden[$id_oc][$id_row];
          $this->totalDescuentoOrden[$id_oc]  += $this->descuentoOrden[$id_oc][$id_row];
          $this->totalImpuestoOrden[$id_oc]   += $this->impuestoOrden[$id_oc][$id_row];
          $this->totalOrden[$id_oc]           += ($this->subtotalOrden[$id_oc][$id_row] + $this->impuestoOrden[$id_oc][$id_row]);
        }

        $this->arrayDoc[$id_oc] += array(
                                          'subtotal'          => $this->totalSubtotalOrden[$id_oc],
                                          'impuesto'          => $this->totalImpuestoOrden[$id_oc],
                                          'total'             => $this->totalOrden[$id_oc],
                                          'valor_pendientes'  => $this->totalValorPendienteOrden[$id_oc],
                                          'unidad_pendientes' => $this->totalUnidadPendienteOrden[$id_oc]
                                        );
      }
    }

    /**
     * getExcel armar el informe para excel
     * @return str body informe generado
     */
    public function getExcel(){
      foreach ($this->arrayDoc as $id_oc => $result){
        if($this->item == 'no'){
          $styleCancel = ($result['estado'] == 3)? "color:red;" : "" ;

          $bodyTable .=  "<tr>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[sucursal]</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[bodega]</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[fecha_inicio]</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[fecha_vencimiento]</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[consecutivo]</td>
                            <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['subtotal'],$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['impuesto'],$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['total'],$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['unidad_pendientes'],$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['valor_pendientes'],$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[proveedor]</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[usuario]</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[tipo_nombre]</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[observacion]</td>
                            <td style='text-align:center; font-size:11px; $styleCancel'>$result[autorizacion]</td>
                          </tr>";

          if($result == end($this->arrayDoc)){
            $bodyTable .= "<tr style='background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;'>
                              <td style='text-align:left;' colspan='5'>TOTALES ORDENES DE COMPRAS</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalSubtotalOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalImpuestoOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalUnidadPendienteOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalValorPendienteOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;' colspan='5'></td>
                            </tr>";
          }
        }
        elseif($this->item == 'si'){
          foreach($this->arrayDocItems[$id_oc] as $id_row => $resultItems){
            $bodyTable .=  "<tr>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$result[sucursal]</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$result[bodega]</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$result[fecha_inicio]</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$result[fecha_vencimiento]</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$result[consecutivo]</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['subtotal'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['impuesto'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['total'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['unidad_pendientes'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round($result['valor_pendientes'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$result[proveedor]</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$result[usuario]</td>
                              <td style=text-align:center;  font-size:11px; $styleCancel'>$result[tipo_nombre]</td>
                              <td style=text-align:center;  font-size:11px; $styleCancel'>$result[observacion]</td>
                              <td style=text-align:center;  font-size:11px; $styleCancel'>$result[autorizacion]</td>
                              <td style='text-aling:center; font-size:11px; $styleCancel'>$resultItems[codigo]</td>
                              <td style='text-align:left;   font-size:11px; $styleCancel'>$resultItems[nombre]</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$resultItems[nombre_unidad_medida] x $resultItems[cantidad_unidad_medida]</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$resultItems[ccos_codigo]</td>
                              <td style='text-align:center; font-size:11px; $styleCancel'>$resultItems[ccos_nombre]</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>$resultItems[consecutivo_requisicion]</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>$resultItems[fecha_requisicion]</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>$resultItems[cantidad]</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round($resultItems['costo_unitario'],$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round($resultItems['descuento'],$_SESSION['DECIMALESMONEDA']) . (($resultItems['tipo_descuento'] == "porcentaje")? "%" : "$") ."</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round($this->impuestoOrden[$id_oc][$id_row],$_SESSION['DECIMALESMONEDA']) ."($resultItems[valor_impuesto]%)</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>".round(($this->subtotalOrden[$id_oc][$id_row] + $this->impuestoOrden[$id_oc][$id_row]),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;  font-size:11px; $styleCancel'>$resultItems[observaciones]</td>
                            </tr>";

            $totalCantidadItems       += $resultItems['cantidad'];
            $totalCostoUnitarioItems  += $resultItems['costo_unitario'];
            $totalDescuentoItems      += $this->descuentoOrden[$id_oc][$id_row];
            $totalImpuestoItems       += $this->impuestoOrden[$id_oc][$id_row];
            $totalItems               += ($this->subtotalOrden[$id_oc][$id_row] + $this->impuestoOrden[$id_oc][$id_row]);
          }

          if($result == end($this->arrayDoc)){
            $bodyTable .=  "<tr style='background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;'>
                              <td style='text-align:left;' colspan='5'>TOTALES ORDENES DE COMPRAS</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalSubtotalOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalImpuestoOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalUnidadPendienteOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round(array_sum($this->totalValorPendienteOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;' colspan='12'></td>
                              <td style='text-align:right;'>".round($totalCantidadItems,$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round($totalCostoUnitarioItems,$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round($totalDescuentoItems,$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round($totalImpuestoItems,$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'>".round($totalItems,$_SESSION['DECIMALESMONEDA'])."</td>
                              <td style='text-align:right;'></td>
                            </tr>";
          }
        }
      }
      header('Content-type: application/vnd.ms-excel');
      header("Content-Disposition: attachment; filename=Informe_Ordenes_De_Compra_".date("Y_m_d").".xls");
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
          <td><b>Informe Ordenes de Compra</td>
        </tr>
        <tr>
          <td>Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td>
        </tr>
      </table>
      <table>
        <tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
          <td>SUCURSAL</td>
          <td>BODEGA</td>
          <td>FECHA GENERACION</td>
          <td>FECHA VENCIMIENTO</td>
          <td>CONSECUTIVO</td>
          <td>SUBTOTAL</td>
          <td>IVA</td>
          <td>TOTAL</td>
          <td>UND. PENDIENTE(S)</td>
          <td>VALOR PENDIENTE(S)</td>
          <td>PROVEEDOR</td>
          <td>USUARIO</td>
          <td>TIPO</td>
          <td>OBSERVACION</td>
          <td>ESTADO</td>
          <?php if($this->item == "si"){ ?>
          <td>CODIGO</td>
          <td>NOMBRE</td>
          <td>UNIDAD</td>
          <td>COD. CENTRO COSTO</td>
          <td>CENTRO COSTO</td>
          <td>CONSECUTIVO REQUISICION</td>
          <td>FECHA REQUISICION</td>
          <td>CANTIDAD</td>
          <td>PRECIO</td>
          <td>DESCUENTO</td>
          <td>IVA</td>
          <td>TOTAL</td>
          <td>OBSERVACION</td>
          <?php } ?>
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

      foreach($this->arrayDoc as $id_oc => $result){
        $styleCancel = ($result['estado'] == 3)? "color:red;" : "" ;

        //CABECERA DEL INFORME SIN DRISCRIMINAR
        if($result == reset($this->arrayDoc)){
          $headTable .=  "<tr class='thead' style='border: 1px solid #999; color: #f7f7f7;'>
                            <td style='width:70px;text-align:center;'><b>SUCURSAL</b></td>
                            <td style='width:70px;text-align:center;'><b>BODEGA</b></td>
                            <td style='width:70px;text-align:center;'><b>FECHA GENERACION</b></td>
                            <td style='width:70px;text-align:center;'><b>FECHA VENCIMIENTO</b></td>
                            <td style='width:70px;text-align:center;'><b>CONSECUTIVO</b></td>
                            <td style='width:70px;text-align:center;'><b>SUBTOTAL</b></td>
                            <td style='width:70px;text-align:center;'><b>IVA</b></td>
                            <td style='width:70px;text-align:center;'><b>UND. PENDIENTE(S)</b></td>
                            <td style='width:70px;text-align:center;'><b>VALOR PENDIENTE(S)</b></td>
                            <td style='width:70px;text-align:center;'><b>PROVEEDOR</b></td>
                            <td style='width:70px;text-align:center;'><b>USUARIO</b></td>
                          </tr>";
        }

        //CUERPO DEL INFORME SIN DISCRIMINAR
        $bodyTable .=  "<tr>
                          <td style='width:70px; text-align:center; font-size:11px; $styleCancel'>$result[sucursal]</td>
                          <td style='width:70px; text-align:center; font-size:11px; $styleCancel'>$result[bodega]</td>
                          <td style='width:70px; text-align:center; font-size:11px; $styleCancel'>$result[fecha_inicio]</td>
                          <td style='width:70px; text-align:center; font-size:11px; $styleCancel'>$result[fecha_vencimiento]</td>
                          <td style='width:70px; text-align:center; font-size:11px; $styleCancel'>$result[consecutivo]</td>
                          <td style='width:70px; text-align:right;  font-size:11px; $styleCancel'>".round($result['subtotal'],$_SESSION['DECIMALESMONEDA'])."</td>
                          <td style='width:70px; text-align:right;  font-size:11px; $styleCancel'>".round($result['impuesto'],$_SESSION['DECIMALESMONEDA'])."</td>
                          <td style='width:70px; text-align:right;  font-size:11px; $styleCancel'>".round($result['unidad_pendientes'],$_SESSION['DECIMALESMONEDA'])."</td>
                          <td style='width:70px; text-align:right;  font-size:11px; $styleCancel'>".round($result['valor_pendientes'],$_SESSION['DECIMALESMONEDA'])."</td>
                          <td style='width:70px; text-align:center; font-size:11px; $styleCancel'>$result[proveedor]</td>
                          <td style='width:70px; text-align:center; font-size:11px; $styleCancel'>$result[usuario]</td>
                        </tr>";

        //PIE DE PAGINA DEL INFORME SIN DRISCRIMINAR
        if($result == end($this->arrayDoc) && $this->item == "no"){
          $footerTable = "<tr class='total' style='border:1px solid #999;'>
                            <td style='text-align:left;' colspan='5'>TOTALES ORDENES DE COMPRAS</td>
                            <td style='text-align:right;'>".round(array_sum($this->totalSubtotalOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:right;'>".round(array_sum($this->totalImpuestoOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:right;'>".round(array_sum($this->totalUnidadPendienteOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:right;'>".round(array_sum($this->totalValorPendienteOrden),$_SESSION['DECIMALESMONEDA'])."</td>
                            <td style='text-align:right;'></td>
                            <td style='text-align:right;'></td>
                          </tr>";
        }

        if($this->item == "si"){
          $totalCantidadItems       = 0;
          $totalCostoUnitarioItems  = 0;
          $totalDescuentoItems      = 0;
          $totalImpuestoItems       = 0;
          $totalItems               = 0;
          $bodyTableItems           = "";
          $footerTableItems         = "";

          foreach($this->arrayDocItems[$id_oc] as $id_row => $resultItems){
            $bodyTableItems .= "<tr>
                                  <td style='text-aling:center; font-size:11px;'>$resultItems[codigo]</td>
                                  <td style='text-align:left;   font-size:11px;'>$resultItems[nombre]</td>
                                  <td style='text-align:center; font-size:11px;'>$resultItems[nombre_unidad_medida] x $resultItems[cantidad_unidad_medida]</td>
                                  <td style='text-align:center; font-size:11px;'>$resultItems[ccos_codigo]</td>
                                  <td style='text-align:center; font-size:11px;'>$resultItems[ccos_nombre]</td>
                                  <td style='text-align:right;  font-size:11px;'>$resultItems[cantidad]</td>
                                  <td style='text-align:right;  font-size:11px;'>".round($resultItems['costo_unitario'],$_SESSION['DECIMALESMONEDA'])."</td>
                                  <td style='text-align:right;  font-size:11px;'>".round($resultItems['descuento'],$_SESSION['DECIMALESMONEDA']) . (($resultItems['tipo_descuento'] == "porcentaje")? "%" : "$") ."</td>
                                  <td style='text-align:right;  font-size:11px;'>".round($this->impuestoOrden[$id_oc][$id_row],$_SESSION['DECIMALESMONEDA']) ."($resultItems[valor_impuesto]%)</td>
                                  <td style='text-align:right;  font-size:11px;'>".round(($this->subtotalOrden[$id_oc][$id_row] + $this->impuestoOrden[$id_oc][$id_row]),$_SESSION['DECIMALESMONEDA'])."</td>
                                </tr>";

            $totalCantidadItems       += $resultItems['cantidad'];
            $totalCostoUnitarioItems  += $resultItems['costo_unitario'];
            $totalDescuentoItems      += $this->descuentoOrden[$id_oc][$id_row];
            $totalImpuestoItems       += $this->impuestoOrden[$id_oc][$id_row];
            $totalItems               += ($this->subtotalOrden[$id_oc][$id_row] + $this->impuestoOrden[$id_oc][$id_row]);
          }

          $footerTableItems =  "<tr class='total'>
                                  <td colspan='5'>TOTALES ORDENES DE COMPRAS</td>
                                  <td style='text-align:right;'>".round($totalCantidadItems,$_SESSION['DECIMALESMONEDA'])."</td>
                                  <td style='text-align:right;'>".round($totalCostoUnitarioItems,$_SESSION['DECIMALESMONEDA'])."</td>
                                  <td style='text-align:right;'>".round($totalDescuentoItems,$_SESSION['DECIMALESMONEDA'])."</td>
                                  <td style='text-align:right;'>".round($totalImpuestoItems,$_SESSION['DECIMALESMONEDA'])."</td>
                                  <td style='text-align:right;'>".round($totalItems,$_SESSION['DECIMALESMONEDA'])."</td>
                                </tr>";

          $bodyTable .=  "<tr style='border: 1px solid #999;'>
                            <td colspan='11' style='padding:0px;'>
                              <table class='tableInforme' style='margin-top: -1px;margin-bottom:-1px;'>
                                <tr class='total'>
                                  <td style='text-align:center;'>CODIGO</td>
                                  <td style='text-align:center;'>NOMBRE</td>
                                  <td style='text-align:center;'>UNIDAD</td>
                                  <td style='text-align:center;'>COD. CENTRO COSTO</td>
                                  <td style='text-align:center;'>CENTRO COSTO</td>
                                  <td style='text-align:center;'>CANTIDAD</td>
                                  <td style='text-align:center;'>PRECIO</td>
                                  <td style='text-align:center;'>DESCUENTO</td>
                                  <td style='text-align:center;'>IVA</td>
                                  <td style='text-align:center;'>TOTAL</td>
                                </tr>
                                ".$bodyTableItems.$footerTableItems."
                              </table>
                            </td>
                          </tr>";
        }

        if($result != end($this->arrayDoc) && $this->item != "no"){
          $bodyTable .= "<tr><td>&nbsp;</td></tr>".$headTable;
        }
        else{
          $bodyTable .= "";
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
                <tr><td style="font-size:13px;"><b>Informe Ordenes de Compra</b><br> <?php echo $subtitulo_cabecera; ?></td></tr>
                <tr><td style="font-size:11px;">Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td></tr>
              </table>
              <table class="tableInforme" style="width:1015px;border-collapse:collapse;">
                <?php echo $headTable.$bodyTable.$footerTable; ?>
              </table>
            </div>
          </div>
        </div>
        <br>
        <?php echo $cuerpoInforme; ?>
      </body>

      <?php
      $texto = ob_get_contents();
      $documento = "Informe_Ordenes_De_Compra_" . date('Y_m_d');
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
        $mpdf->SetDisplayMode ( 'fullpage' );
        $mpdf->SetHeader("");
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
     * @mtthod generate Generar el informe
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

  $objectInform = new InformeOrdenesCompra($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$sucursal,$bodega,$tipo_orden_compra,$estado,$item,$autorizado,$arraytercerosJSON,$arrayempleadosJSON,$arrayccosJSON,$mysql);
  $objectInform->generate();
?>
