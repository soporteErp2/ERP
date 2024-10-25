<?php
    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    ob_start();
    /**
     * @class InformeRequisicion
     */
    class InformeFacturaCompra{

      public $IMPRIME_HTML                = '';
      public $IMPRIME_XLS                 = '';
      public $IMPRIME_PDF                 = '';
      public $arraytercerosJSON           = '';
      public $arrayVendedoresJSON         = '';
      public $arrayCcosJSON               = '';
      public $MyInformeFiltroFechaInicio  = '';
      public $MyInformeFiltroFechaFinal   = '';
      public $discriminar_items           = '';
      public $sucursal                    = '';
      public $mysql                       = '';
      public $id_empresa                  = '';
      public $customWhere                 = '';
      public $tipoDoc                     = '';
      public $estadoDoc                   = '';
      public $arrayDoc                    = array();
      public $arrayDocItems               = array();

      /**
       * [__construct]
       * @param str $IMPRIME_HTML                 Generar en HTML
       * @param str $IMPRIME_XLS                  Generar en EXCEL
       * @param str $IMPRIME_PDF                  Generar en PDF
       * @param arr $arrayTercerosJSON            Filtro por terceros
       * @param arr $arrayVendedoresJSON          Filtro por usuarios
       * @param arr $arrayCcosJSON                Filtro por centro de costos
       * @param dat $MyInformeFiltroFechaInicio   Fecha inicial del informe
       * @param dat $MyInformeFiltroFechaFinal    Fecha final del informe
       * @param str $detallado                    Filtro por detalle
       * @param int $sucursal                     Filtro por sucursal
       * @param obj $mysql                        Objeto de conexion a la base de datos
       */
      function __construct($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$sucursal,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$arraytercerosJSON,$arrayVendedoresJSON,$arrayCcosJSON,$discriminar_items,$tipo_doc,$estadoDoc,$mysql){
        $this->IMPRIME_HTML               = $IMPRIME_HTML;
        $this->IMPRIME_XLS                = $IMPRIME_XLS;
        $this->IMPRIME_PDF                = $IMPRIME_PDF;
        $this->arraytercerosJSON          = json_decode($arraytercerosJSON);
        $this->arrayVendedoresJSON        = json_decode($arrayVendedoresJSON);
        $this->arrayCcosJSON              = json_decode($arrayCcosJSON);
        $this->MyInformeFiltroFechaInicio = $MyInformeFiltroFechaInicio;
        $this->MyInformeFiltroFechaFinal  = $MyInformeFiltroFechaFinal;
        $this->discriminar_items          = $discriminar_items;
        $this->mysql                      = $mysql;
        $this->sucursal                   = $sucursal;
        $this->tipoDoc                    = $tipo_doc;
        $this->estadoDoc                  = $estadoDoc;
        $this->id_empresa                 = $_SESSION['EMPRESA'];
      }

      /**
       * @method showError Mostrar alerta si se presenta un error
       * @param  str $mensaje Mensaje de error a mostrar
       */
      public function showError($mensaje){
        echo "<script>alert('Error\n $mensaje');</script>" . $mensaje;
        exit;
      }

      /**
       * @method getCustomFiltres armar los filtros a aplicar al informe
       */
      public function getCustomFiltres(){
        if($this->MyInformeFiltroFechaFinal == "" || $this->MyInformeFiltroFechaInicio == ""){
          $this->showError("Debe Seleccionar las fechas del informe");
        }

        $this->customWhere = " AND fecha_inicio BETWEEN '$this->MyInformeFiltroFechaInicio' AND '$this->MyInformeFiltroFechaFinal'";

        if(!empty($this->arraytercerosJSON)){
          foreach ($this->arraytercerosJSON as $indice => $id_tercero){
            $whereTerceros .= ($whereTerceros == "")? " AND TP.id_proveedor = '$id_tercero'" : " OR TP.id_proveedor = '$id_tercero'";
          }
        }

        if(!empty($this->arrayVendedoresJSON)){
          foreach($this->arrayVendedoresJSON as $indice => $id_vendedor){
            $whereVendedores .= ($whereVendedores == "")? " AND TP.id_usuario = '$id_vendedor'" : " OR TP.id_usuario = '$id_vendedor'";
          }
        }

        if(!empty($this->arrayCcosJSON)){
          foreach($this->arrayCcosJSON as $indice => $codigo_centro_costo){
            $whereCcos .= ($whereCcos == "")? " AND TI.id_centro_costos = '$codigo_centro_costo'" : " OR TI.id_centro_costos = '$codigo_centro_costo'";
          }
        }

        if(!empty($this->tipoDoc)){
          if ($this->tipoDoc == "FC"){
             $whereTipoDoc = " AND TP.tipo_documento IS NULL"; 
          }
          elseif ($this->tipoDoc == "DSE"){
             $whereTipoDoc = " AND TP.tipo_documento = '05'"; 
          }
        }

        if(!empty($this->estadoDoc)){
          if ($this->estadoDoc == "ENVIADO"){
             $whereEstadoDoc = " AND TP.response_DS like '%recibido exitosamente%' "; 
          }
          elseif ($this->estadoDoc == "NO_ENVIADO"){
            $whereEstadoDoc = " AND (TP.response_DS IS NULL OR TP.response_DS = '' OR TP.response_DS NOT like '%recibido exitosamente%' )"; 
          }
        }

        $this->customWhere .= ($this->sucursal != "" && $this->sucursal != "global")? " AND TP.id_sucursal = '$this->sucursal'" : "";
        $this->customWhere .= $whereTerceros.$whereVendedores.$whereCcos.$whereTipoDoc.$whereEstadoDoc;
      }

      /**
       * @method getDocumentoInfo consultar la informacion de las facturas de compra
       */
      public function getDocumentoInfo(){

        //------------------- DATOS CABECERA DE LA FACTURA -------------------//
       $sql = "SELECT
                  TP.id,
                  TP.fecha_inicio,
                  TP.fecha_final,
                  TP.prefijo_factura,
                  TP.numero_factura,
                  TP.consecutivo,
                  TP.proveedor,
                  TP.usuario,
                  TP.sucursal,
                  TP.bodega,
                  TP.estado,
                  TP.nit,
                  TP.fecha_generacion,
                  TP.total_factura,
                  TP.factura_por_cuentas
                FROM
                  compras_facturas AS TP
                LEFT JOIN
                  compras_facturas_inventario AS TI
                ON
                  TI.id_factura_compra = TP.id
                WHERE
                  TP.activo = 1
                AND
                  (TP.estado = 1 OR TP.estado = 2 OR TP.estado = 3)
                AND
                  TP.id_empresa = $this->id_empresa
                  $this->customWhere
                GROUP BY
                  TP.id";

        $query = $this->mysql->query($sql,$this->mysql->link);

        while($row = $this->mysql->fetch_array($query)){
          $id_doc = $row['id'];

          $whereIdDocs .= ($whereIdDocs == "")? " AND CFI.id_factura_compra = '$id_doc'" : " OR CFI.id_factura_compra = '$id_doc'";
          $whereIdComprobantes .= ($whereIdComprobantes == "")? "CF.id = '$id_doc'" : "OR CF.id = '$id_doc'";

          $this->arrayDoc[$id_doc] = array(
                                            'fecha_inicio'        => $row['fecha_inicio'],
                                            'fecha_final'         => $row['fecha_final'],
                                            'prefijo_factura'     => ($row['prefijo_factura'] != "")? $row['prefijo_factura'] : "",
                                            'numero_factura'      => $row['numero_factura'],
                                            'consecutivo'         => $row['consecutivo'],
                                            'proveedor'           => $row['proveedor'],
                                            'usuario'             => $row['usuario'],
                                            'sucursal'            => $row['sucursal'],
                                            'bodega'              => $row['bodega'],
                                            'estado'              => $row['estado'],
                                            'nit'                 => $row['nit'],
                                            'fecha_generacion'    => $row['fecha_generacion'],
                                            'total_factura'       => $row['total_factura'],
                                            'por_cuentas'         => $row['factura_por_cuentas']
                                          );

          $this->totalFactura += $row['total_factura'];
        }

        //-------------------- DATOS CUERPO DE LA FACTURA --------------------//
        $sql = "SELECT
                  CFI.id,
                  CFI.id_factura_compra,
                  CFI.codigo,
                  CFI.nombre_unidad_medida,
                  CFI.cantidad_unidad_medida,
                  CFI.nombre,
                  CFI.cantidad,
                  CFI.saldo_cantidad,
                  CFI.costo_unitario,
                  CFI.tipo_descuento,
                  CFI.descuento,
                  CFI.valor_impuesto,
                  CFI.nombre_consecutivo_referencia,
                  CFI.consecutivo_referencia,
                  CC.codigo AS ccos_codigo,
                  CC.nombre AS ccos_nombre
                FROM
                  compras_facturas_inventario AS CFI
                LEFT JOIN
                  centro_costos AS CC
                ON
                  CC.id = CFI.id_centro_costos
                WHERE
                  CFI.activo = 1
                  $whereIdDocs";

        $query = $this->mysql->query($sql,$this->mysql->link);

        while($row = $this->mysql->fetch_array($query)){
          $id    = $row['id'];
          $id_fc = $row['id_factura_compra'];
          $this->arrayDocItems[$id_fc][$id] = array(
                                                      'codigo'                          => $row['codigo'],
                                                      'nombre_unidad_medida'            => $row['nombre_unidad_medida'],
                                                      'cantidad_unidad_medida'          => $row['cantidad_unidad_medida'],
                                                      'nombre'                          => $row['nombre'],
                                                      'cantidad'                        => $row['cantidad'],
                                                      'saldo_cantidad'                  => $row['saldo_cantidad'],
                                                      'costo_unitario'                  => $row['costo_unitario'],
                                                      'tipo_descuento'                  => $row['tipo_descuento'],
                                                      'descuento'                       => $row['descuento'],
                                                      'valor_impuesto'                  => ($row['valor_impuesto'] != null)? $row['valor_impuesto'] : 0,
                                                      'nombre_consecutivo_referencia'   => $row['nombre_consecutivo_referencia'],
                                                      'consecutivo_referencia'          => $row['consecutivo_referencia'],
                                                      'ccos_codigo'                     => $row['ccos_codigo'],
                                                      'ccos_nombre'                     => $row['ccos_nombre']
                                                    );
        }

        //----------------- DATOS RETENCIONES DE LA FACTURA ------------------//
        $sql = "SELECT
                  CFI.id,
                  CFI.id_factura_compra,
                  CFI.tipo_retencion,
                  CFI.valor,
                  CFI.base
                FROM
                  compras_facturas_retenciones AS CFI
                WHERE
                  CFI.activo = 1
                  $whereIdDocs";

        $query = $this->mysql->query($sql,$this->mysql->link);

        while($row = $this->mysql->fetch_array($query)){
          $id = $row['id'];
          $id_fc = $row['id_factura_compra'];
          $this->arrayDocRetenciones[$id_fc][$id] = array(
                                                            'tipo_retencion'    => $row['tipo_retencion'],
                                                            'valor'             => $row['valor'],
                                                            'base'              => $row['base']
                                                          );
        }

        //----------------- DATOS COMPROBANTES DE LA FACTURA -----------------//
        $sql = "SELECT
                  AC.id_documento,
                  AC.consecutivo_documento,
                  AC.tipo_documento,
                  AC.debe,
                  AC.id_documento_cruce
                FROM
                  asientos_colgaap AS AC
                LEFT JOIN
                  compras_facturas AS CF
                ON
                  CF.id = AC.id_documento_cruce
                WHERE
                  AC.activo = 1
                AND
                  AC.id_empresa = $this->id_empresa
                AND
                  AC.tipo_documento = 'CE'
                AND
                  AC.tipo_documento_cruce = 'FC'
                AND
                  ($whereIdComprobantes)";

        $query = $this->mysql->query($sql,$this->mysql->link);

        while($row = $this->mysql->fetch_array($query)){
          $id_fc = $row['id_documento_cruce'];
          $id_comprobante = $row['id_documento'];
          $this->arrayDocComprobantes[$id_fc][$id_comprobante] = array(
                                                                        'consecutivo_documento' => $row['consecutivo_documento'],
                                                                        'debe'                  => $row['debe']
                                                                      );
        }

        foreach($this->arrayDoc as $id_fc => $result){
          if($result['por_cuentas']=='true'){
            $this->arrayDoc[$id_fc] += array(
              'subtotal'    => $result['total_factura'],
              'descuento'   => 0,
              'impuesto'    => 0,
              'reteIca'     => 0,
              'reteIva'     => 0,
              'reteFuente'  => 0
            );
            continue;} 
          //RECORRER ITEMS PARA OBTENER SUBTOTALES, DESCUENTOS E IMPUESTOS
          foreach($this->arrayDocItems[$id_fc] as $id_row => $resultItems){
            if($result['estado'] == 3){
              $this->subtotalFactura[$id_fc][$id_row]  = 0;
              $this->descuentoFactura[$id_fc][$id_row] = 0;
              $this->impuestoFactura[$id_fc][$id_row]  = 0;
            } else{
                $this->subtotalFactura[$id_fc][$id_row]   = ($resultItems['costo_unitario'] * $resultItems['cantidad']);
              if($resultItems['tipo_descuento'] == "porcentaje"){
                $this->descuentoFactura[$id_fc][$id_row]  = (($resultItems['costo_unitario'] * $resultItems['cantidad']) * $resultItems['descuento']) / 100;
              } else{
                $this->descuentoFactura[$id_fc][$id_row]  = $resultItems['descuento'];
              }
              $this->impuestoFactura[$id_fc][$id_row] = ($this->subtotalFactura[$id_fc][$id_row] * $resultItems['valor_impuesto']) / 100;
            }

            $this->totalSubtotalFactura[$id_fc]   += $this->subtotalFactura[$id_fc][$id_row];
            $this->totalDescuentoFactura[$id_fc]  += $this->descuentoFactura[$id_fc][$id_row];
            $this->totalImpuestoFactura[$id_fc]   += $this->impuestoFactura[$id_fc][$id_row];
          }

          //RECORRER RETENCIONES
          foreach($this->arrayDocRetenciones[$id_fc] as $id_row => $resultRetenciones){
            if($this->totalSubtotalFactura[$id_fc] > $resultRetenciones['base']){
              if($resultRetenciones['tipo_retencion'] == 'ReteIca'){
                $this->totalReteIcaFactura[$id_fc] += $this->totalSubtotalFactura[$id_fc] * $resultRetenciones['valor'] / 100;
              } elseif($resultRetenciones['tipo_retencion'] == 'ReteIva'){
                $this->totalReteIvaFactura[$id_fc] += $this->totalImpuestoFactura[$id_fc] * $resultRetenciones['valor'] / 100;
              } elseif($resultRetenciones['tipo_retencion'] == 'ReteFuente'){
                $this->totalReteFuenteFactura[$id_fc] += $this->totalSubtotalFactura[$id_fc] * $resultRetenciones['valor'] / 100;
              };
            }
          }

          $this->arrayDoc[$id_fc] += array(
                                            'subtotal'    => $this->totalSubtotalFactura[$id_fc],
                                            'descuento'   => $this->totalDescuentoFactura[$id_fc],
                                            'impuesto'    => $this->totalImpuestoFactura[$id_fc],
                                            'reteIca'     => ($this->totalReteIcaFactura[$id_fc] != null)? $this->totalReteIcaFactura[$id_fc] : 0,
                                            'reteIva'     => ($this->totalReteIvaFactura[$id_fc] != null)? $this->totalReteIvaFactura[$id_fc] : 0,
                                            'reteFuente'  => ($this->totalReteFuenteFactura[$id_fc] != null)? $this->totalReteFuenteFactura[$id_fc] : 0
                                          );

        }

      }

      /**
       * getExcel armar el informe para excel
       * @return str body informe generado
       */
      public function getExcel(){
        foreach($this->arrayDoc as $id_fc => $result){
          if($this->discriminar_items == 'no'){
            $styleCancel = ($result['estado'] == 3)? "color:red;" : "" ;

            $bodyTable .=  "<tr>
                              <td style='text-align:center;font-size: 11px; $styleCancel'>$result[sucursal]</td>
                              <td style='text-align:center;font-size: 11px; $styleCancel'>$result[bodega]</td>
                              <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_inicio]</td>
                              <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_final]</td>
                              <td style='text-align:center;font-size: 11px; $styleCancel'>$result[prefijo_factura] $result[numero_factura]</td>
                              <td style='text-align:center;font-size: 11px; $styleCancel'>$result[consecutivo]</td>
                              <td style='text-align:center;font-size: 11px; $styleCancel'>$result[proveedor]</td>
                              <td style='text-align:center;font-size: 11px; $styleCancel'>$result[usuario]</td>
                              <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['subtotal'],$this->IMPRIME_XLS)."</td>
                              <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['descuento'],$this->IMPRIME_XLS)."</td>
                              <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['impuesto'],$this->IMPRIME_XLS)."</td>
                              <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteFuente'],$this->IMPRIME_XLS)."</td>
                              <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIca'],$this->IMPRIME_XLS)."</td>
                              <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIva'],$this->IMPRIME_XLS)."</td>
                              <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['total_factura'],$this->IMPRIME_XLS)."</td>
                            </tr>";

            if($result == end($this->arrayDoc)){
              $bodyTable .=  "<tr style='background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;'>
                                <td style='text-align:left;' colspan='8'>TOTALES FACTURAS DE COMPRAS</td>
                                <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalSubtotalFactura),$this->IMPRIME_XLS)   . "</td>
                                <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalDescuentoFactura),$this->IMPRIME_XLS)  . "</td>
                                <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalImpuestoFactura),$this->IMPRIME_XLS)   . "</td>
                                <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalReteFuenteFactura),$this->IMPRIME_XLS) . "</td>
                                <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalReteIcaFactura),$this->IMPRIME_XLS)    . "</td>
                                <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalReteIvaFactura),$this->IMPRIME_XLS)    . "</td>
                                <td style='text-align:right;'>" . validar_numero_formato($this->totalFactura,$this->IMPRIME_XLS)                      . "</td>
                              </tr>";
            }
          }
          elseif($this->discriminar_items == 'items'){
            foreach($this->arrayDocItems[$id_fc] as $id_row => $resultItems){
              $docCruce = ($resultItems['nombre_consecutivo_referencia']=="Orden de Compra")? "OC ".$resultItems['consecutivo_referencia'] : "";
              $bodyTable .=  "<tr>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[sucursal]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[bodega]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_inicio]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_final]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[prefijo_factura] $result[numero_factura]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[consecutivo]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[proveedor]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[usuario]</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['subtotal'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['descuento'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['impuesto'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteFuente'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIca'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIva'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['total_factura'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$docCruce</td>
                                <td style='text-aling:center;font-size: 11px; $styleCancel'>$resultItems[codigo]</td>
                                <td style='text-align:left;  font-size: 11px; $styleCancel'>$resultItems[nombre]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$resultItems[nombre_unidad_medida] x $resultItems[cantidad_unidad_medida]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$resultItems[ccos_codigo]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$resultItems[ccos_nombre]</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>$resultItems[cantidad]</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($resultItems['costo_unitario'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($resultItems['descuento'],$this->IMPRIME_XLS) . (($resultItems['tipo_descuento'] == "porcentaje")? "%" : "$") ."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($this->impuestoFactura[$id_fc][$id_row],$this->IMPRIME_XLS) ."($resultItems[valor_impuesto]%)</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato(($this->subtotalFactura[$id_fc][$id_row] + $this->impuestoFactura[$id_fc][$id_row]),$this->IMPRIME_XLS)."</td>
                              </tr>";

              $totalCantidadItems       += $resultItems['cantidad'];
              $totalCostoUnitarioItems  += $resultItems['costo_unitario'];
              $totalDescuentoItems      += $this->descuentoFactura[$id_fc][$id_row];
              $totalImpuestoItems       += $this->impuestoFactura[$id_fc][$id_row];
              $totalItems               += ($this->subtotalFactura[$id_fc][$id_row] + $this->impuestoFactura[$id_fc][$id_row]);
            }

            if($result == end($this->arrayDoc)){
              $bodyTable .=  "<tr style='background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;'>
                                <td colspan='20'>TOTALES</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalCantidadItems,$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalCostoUnitarioItems,$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalDescuentoItems,$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalImpuestoItems,$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalItems,$this->IMPRIME_XLS)."</td>
                              </tr>";
            }
          }
          elseif($this->discriminar_items == 'ordenes'){

            foreach($this->arrayDocItems[$id_fc] as $id_row => $resultOrdenes){
              if($resultOrdenes['consecutivo_referencia'] != null){

                if($resultOrdenes['tipo_descuento'] == "porcentaje"){
                  $descuentoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']] += ($resultOrdenes['costo_unitario'] * $resultOrdenes['cantidad']) * $resultOrdenes['descuento'] / 100;
                } else{
                  $descuentoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']] += $resultOrdenes['descuento'];
                }
                $subtotalOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']]  += ($resultOrdenes['cantidad'] * $resultOrdenes['costo_unitario']) - $descuentoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']];
                $ivaOrdenes[$resultOrdenes['consecutivo_referencia']]               += (($resultOrdenes['cantidad'] * $resultOrdenes['costo_unitario']) - $descuentoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']]) * $resultOrdenes['valor_impuesto'] / 100;

              }

            }

            if(!empty($subtotalOrdenes[$id_fc])){
              foreach($subtotalOrdenes[$id_fc] as $id_row => $resultOrdenes){
                $bodyTable .=  "<tr>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[sucursal]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[bodega]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_inicio]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_final]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[prefijo_factura] $result[numero_factura]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[consecutivo]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[proveedor]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[usuario]</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['subtotal'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['descuento'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['impuesto'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteFuente'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIca'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIva'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['total_factura'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>".$id_row."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($subtotalOrdenes[$id_fc][$id_row],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($descuentoOrdenes[$id_fc][$id_row],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($ivaOrdenes[$id_row],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato(($subtotalOrdenes[$id_fc][$id_row] + $ivaOrdenes[$id_row]),$this->IMPRIME_XLS)."</td>
                                </tr>";

                $totalSubtotalOrdenes   += $subtotalOrdenes[$id_fc][$id_row];
                $totalDescuentoOrdenes  += $descuentoOrdenes[$id_fc][$id_row];
                $totalIvaOrdenes        += $ivaOrdenes[$id_row];
                $totalOrdenes           += ($subtotalOrdenes[$id_fc][$id_row] + $ivaOrdenes[$id_row]);
              }
            }
            else{
              $bodyTable .=  "<tr>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[sucursal]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[bodega]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_inicio]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_final]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[prefijo_factura] $result[numero_factura]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[consecutivo]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[proveedor]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[usuario]</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['subtotal'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['descuento'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['impuesto'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteFuente'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIca'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIva'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['total_factura'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'></td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>0</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>0</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>0</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>0</td>
                              </tr>";
            }

            if($result == end($this->arrayDoc)){
              $bodyTable .=  "<tr style='background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;'>
                                <td colspan='16'>TOTALES</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalSubtotalOrdenes,$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalDescuentoOrdenes,$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalIvaOrdenes,$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right;'>".validar_numero_formato($totalOrdenes,$this->IMPRIME_XLS)."</td>
                              </tr>";
            }
          }
          elseif($this->discriminar_items == 'comprobantes'){

            if(!empty($this->arrayDocComprobantes[$id_fc])){
              foreach($this->arrayDocComprobantes[$id_fc] as $id_row => $resultComprobantes){
                $bodyTable .=  "<tr>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[sucursal]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[bodega]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_inicio]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_final]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[prefijo_factura] $result[numero_factura]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[consecutivo]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[proveedor]</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>$result[usuario]</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['subtotal'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['descuento'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['impuesto'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteFuente'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIca'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIva'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['total_factura'],$this->IMPRIME_XLS)."</td>
                                  <td style='text-align:center;font-size: 11px; $styleCancel'>".$resultComprobantes['consecutivo_documento']."</td>
                                  <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($resultComprobantes['debe'],$this->IMPRIME_XLS)."</td>
                                </tr>";

                $totalValorComprobantes += $resultComprobantes['debe'];
              }
            }
            else{
              $bodyTable .=  "<tr>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[sucursal]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[bodega]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_inicio]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[fecha_final]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[prefijo_factura] $result[numero_factura]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[consecutivo]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[proveedor]</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'>$result[usuario]</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['subtotal'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['descuento'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['impuesto'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteFuente'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIca'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIva'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>".validar_numero_formato($result['total_factura'],$this->IMPRIME_XLS)."</td>
                                <td style='text-align:center;font-size: 11px; $styleCancel'></td>
                                <td style='text-align:right; font-size: 11px; $styleCancel'>0</td>
                              </tr>";
            }

            if($result == end($this->arrayDoc)){
              $bodyTable .=  "<tr style='background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;'>
                                <td colspan='16'>TOTALES</td>
                                <td style='text-align:right'>".validar_numero_formato($totalValorComprobantes,$this->IMPRIME_XLS)."</td>
                              </tr>";
            }
          }
        }
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=informe_facturas_compra_".date("Y_m_d").".xls");
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
            <td><b>Informe Facturas de Compra</td>
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
            <td>NUMERO FACTURA</td>
            <td>CONSECUTIVO</td>
            <td>PROVEEDOR</td>
            <td>USUARIO</td>
            <td>SUBTOTAL</td>
            <td>DESCUENTO</td>
            <td>IMPUESTO</td>
            <td>RETE. FTE</td>
            <td>RETE. ICA</td>
            <td>RETE. IVA</td>
            <td>TOTAL</td>
            <?php if($this->discriminar_items == "items"){ ?>
              <td>DOC CRUCE</td>
              <td>CODIGO</td>
              <td>NOMBRE</td>
              <td>UNIDAD</td>
              <td>COD. CENTRO COSTO</td>
              <td>CENTRO COSTO</td>
              <td>CANTIDAD</td>
              <td>PRECIO</td>
              <td>DESCUENTO</td>
              <td>IVA</td>
              <td>TOTAL</td>
            <?php } elseif($this->discriminar_items == "ordenes"){ ?>
              <td>CONSECUTIVO ORDEN DE COMPRA</td>
              <td>SUBTOTAL</td>
              <td>DESCUENTO</td>
              <td>IVA</td>
              <td>TOTAL</td>
            <?php } elseif($this->discriminar_items == "comprobantes"){ ?>
              <td>CONSECUTIVO COMPROBANTE DE EGRESO</td>
              <td>VALOR</td>
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

        foreach($this->arrayDoc as $id_fc => $result){
          $styleCancel    = ($result['estado'] == 3)? "color:red;" : "";

          //CABECERA DEL INFORME SIN DRISCRIMINAR
          if($result == reset($this->arrayDoc)){
            $headTable .=  "<tr class='thead' style='border: 1px solid #999; color: #f7f7f7;'>
                              <td style='width:70px;text-align:center;'><b>SUCURSAL</b></td>
                              <td style='width:70px;text-align:center;'><b>BODEGA</b></td>
                              <td style='width:70px;text-align:center;'><b>FECHA GENERACION</b></td>
                              <td style='width:70px;text-align:center;'><b>FECHA VENCIMIENTO</b></td>
                              <td style='width:70px;text-align:center;'><b>NUMERO FACTURA</b></td>
                              <td style='width:70px;text-align:center;'><b>CONSECUTIVO</b></td>
                              <td style='width:70px;text-align:center;'><b>PROVEEDOR</b></td>
                              <td style='width:70px;text-align:center;'><b>USUARIO</b></td>
                              <td style='width:70px;text-align:center;'><b>SUBTOTAL</b></td>
                              <td style='width:70px;text-align:center;'><b>DESCUENTO</b></td>
                              <td style='width:70px;text-align:center;'><b>IMPUESTO</b></td>
                              <td style='width:70px;text-align:center;'><b>RETE. FTE</b></td>
                              <td style='width:70px;text-align:center;'><b>RETE. ICA</b></td>
                              <td style='width:70px;text-align:center;'><b>RETE. IVA</b></td>
                              <td style='width:70px;text-align:center;'><b>TOTAL</b></td>
                            </tr>";
          }

          //CUERPO DEL INFORME SIN DISCRIMINAR
          $bodyTable .=  "<tr>
                            <td style='border-left:1px solid #999;width:70px;text-align:center;font-size: 11px; $styleCancel'>$result[sucursal]</td>
                            <td style='width:70px;text-align:center;font-size: 11px; $styleCancel'>$result[bodega]</td>
                            <td style='width:70px;text-align:center;font-size: 11px; $styleCancel'>$result[fecha_inicio]</td>
                            <td style='width:70px;text-align:center;font-size: 11px; $styleCancel'>$result[fecha_final]</td>
                            <td style='width:70px;text-align:center;font-size: 11px; $styleCancel'>$result[prefijo_factura] $result[numero_factura]</td>
                            <td style='width:70px;text-align:center;font-size: 11px; $styleCancel'>$result[consecutivo]</td>
                            <td style='width:70px;text-align:center;font-size: 11px; $styleCancel'>$result[proveedor]</td>
                            <td style='width:70px;text-align:center;font-size: 11px; $styleCancel'>$result[usuario]</td>
                            <td style='width:70px;text-align:right;font-size: 11px; $styleCancel'>".validar_numero_formato($result['subtotal'],$this->IMPRIME_XLS)."</td>
                            <td style='width:70px;text-align:right;font-size: 11px; $styleCancel'>".validar_numero_formato($result['descuento'],$this->IMPRIME_XLS)."</td>
                            <td style='width:70px;text-align:right;font-size: 11px; $styleCancel'>".validar_numero_formato($result['impuesto'],$this->IMPRIME_XLS)."</td>
                            <td style='width:70px;text-align:right;font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteFuente'],$this->IMPRIME_XLS)."</td>
                            <td style='width:70px;text-align:right;font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIca'],$this->IMPRIME_XLS)."</td>
                            <td style='width:70px;text-align:right;font-size: 11px; $styleCancel'>".validar_numero_formato($result['reteIva'],$this->IMPRIME_XLS)."</td>
                            <td style='border-right:1px solid #999;width:70px;text-align:right;font-size: 11px; $styleCancel'>".validar_numero_formato($result['total_factura'],$this->IMPRIME_XLS)."</td>
                          </tr>";

          //PIE DE PAGINA DEL INFORME SIN DRISCRIMINAR
          if($result == end($this->arrayDoc) && $this->discriminar_items == "no"){
            $footerTable = "<tr class='total' style='border:1px solid #999;'>
                              <td style='text-align:left;' colspan='8'>TOTALES FACTURAS DE COMPRAS</td>
                              <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalSubtotalFactura),$this->IMPRIME_XLS) . "</td>
                              <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalDescuentoFactura),$this->IMPRIME_XLS) . "</td>
                              <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalImpuestoFactura),$this->IMPRIME_XLS) . "</td>
                              <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalReteFuenteFactura),$this->IMPRIME_XLS) . "</td>
                              <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalReteIcaFactura),$this->IMPRIME_XLS) . "</td>
                              <td style='text-align:right;'>" . validar_numero_formato(array_sum($this->totalReteIvaFactura),$this->IMPRIME_XLS) . "</td>
                              <td style='text-align:right;'>" . validar_numero_formato($this->totalFactura,$this->IMPRIME_XLS) . "</td>
                            </tr>";
          }

          //SI SE DISCRIMINA POR ITEMS
          if($this->discriminar_items == "items"){

            $totalCantidadItems       = 0;
            $totalCostoUnitarioItems  = 0;
            $totalDescuentoItems      = 0;
            $totalImpuestoItems       = 0;
            $totalItems               = 0;
            $bodyTableItems           = "";
            $footerTableItems         = "";

            foreach($this->arrayDocItems[$id_fc] as $id_row => $resultItems){
              $bodyTableItems .= "<tr>
                                    <td style='text-aling:center;font-size: 11px;'>$resultItems[codigo]</td>
                                    <td style='text-align:left;  font-size: 11px;'>$resultItems[nombre]</td>
                                    <td style='text-align:center;font-size: 11px;'>$resultItems[nombre_unidad_medida] x $resultItems[cantidad_unidad_medida]</td>
                                    <td style='text-align:center;font-size: 11px;'>$resultItems[ccos_codigo]</td>
                                    <td style='text-align:center;font-size: 11px;'>$resultItems[ccos_nombre]</td>
                                    <td style='text-align:right; font-size: 11px;'>$resultItems[cantidad]</td>
                                    <td style='text-align:right; font-size: 11px;'>".validar_numero_formato($resultItems['costo_unitario'],$this->IMPRIME_XLS)."</td>
                                    <td style='text-align:right; font-size: 11px;'>".validar_numero_formato($resultItems['descuento'],$this->IMPRIME_XLS) . (($resultItems['tipo_descuento'] == "porcentaje")? "%" : "$") ."</td>
                                    <td style='text-align:right; font-size: 11px;'>".validar_numero_formato($this->impuestoFactura[$id_fc][$id_row],$this->IMPRIME_XLS) ."($resultItems[valor_impuesto]%)</td>
                                    <td style='text-align:right; font-size: 11px;'>".validar_numero_formato(($this->subtotalFactura[$id_fc][$id_row] + $this->impuestoFactura[$id_fc][$id_row]),$this->IMPRIME_XLS)."</td>
                                  </tr>";

              $totalCantidadItems       += $resultItems['cantidad'];
              $totalCostoUnitarioItems  += $resultItems['costo_unitario'];
              $totalDescuentoItems      += $this->descuentoFactura[$id_fc][$id_row];
              $totalImpuestoItems       += $this->impuestoFactura[$id_fc][$id_row];
              $totalItems               += ($this->subtotalFactura[$id_fc][$id_row] + $this->impuestoFactura[$id_fc][$id_row]);
            }

            $footerTableItems =  "<tr class='total'>
                                    <td colspan='5'>TOTALES FACTURAS DE COMPRAS</td>
                                    <td style='text-align:right;'>".validar_numero_formato($totalCantidadItems,$this->IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato($totalCostoUnitarioItems,$this->IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato($totalDescuentoItems,$this->IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato($totalImpuestoItems,$this->IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato($totalItems,$this->IMPRIME_XLS)."</td>
                                  </tr>";

            $bodyTable .=  "<tr style='border: 1px solid #999;'>
                              <td colspan='15' style='padding:0px;'>
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
          //SI SE DISCRIMINA POR ORDENES DE COMPRA
          elseif($this->discriminar_items == "ordenes"){

            $totalSubtotalOrdenes = 0;
            $totalDescuentoItems  = 0;
            $totalIvaOrdenes      = 0;
            $totalIvaOrdenes      = 0;
            $bodyTableOrdenes     = "";
            $footerTableOrdenes   = "";

            foreach($this->arrayDocItems[$id_fc] as $id_row => $resultOrdenes){
              if($resultOrdenes['consecutivo_referencia'] != null){
                $consecutivoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']] = $resultOrdenes['consecutivo_referencia'];
                if($resultOrdenes['tipo_descuento'] == "porcentaje"){
                  $descuentoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']] += ($resultOrdenes['costo_unitario'] * $resultOrdenes['cantidad']) * $resultOrdenes['descuento'] / 100;
                } else{
                  $descuentoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']] += $resultOrdenes['descuento'];
                }
                $subtotalOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']]  += ($resultOrdenes['cantidad'] * $resultOrdenes['costo_unitario']) - $descuentoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']];
                $ivaOrdenes[$resultOrdenes['consecutivo_referencia']]               += (($resultOrdenes['cantidad'] * $resultOrdenes['costo_unitario']) - $descuentoOrdenes[$id_fc][$resultOrdenes['consecutivo_referencia']]) * $resultOrdenes['valor_impuesto'] / 100;
              }
            }

            foreach($consecutivoOrdenes[$id_fc] as $id_row => $resultOrdenes){
              $bodyTableOrdenes .= "<tr>
                                      <td style='text-align:center;font-size:11px;'>".$id_row."</td>
                                      <td style='text-align:right;font-size:11px;'>".validar_numero_formato($subtotalOrdenes[$id_fc][$id_row],$this->IMPRIME_XLS)."</td>
                                      <td style='text-align:right;font-size:11px;'>".validar_numero_formato($descuentoOrdenes[$id_fc][$id_row],$this->IMPRIME_XLS)."</td>
                                      <td style='text-align:right;font-size:11px;'>".validar_numero_formato($ivaOrdenes[$id_row],$this->IMPRIME_XLS)."</td>
                                      <td style='text-align:right;font-size:11px;'>".validar_numero_formato(($subtotalOrdenes[$id_fc][$id_row] + $ivaOrdenes[$id_row]),$this->IMPRIME_XLS)."</td>
                                    </tr>";

              $totalSubtotalOrdenes   += $subtotalOrdenes[$id_fc][$id_row];
              $totalDescuentoOrdenes  += $descuentoOrdenes[$id_fc][$id_row];
              $totalIvaOrdenes        += $ivaOrdenes[$id_row];
              $totalOrdenes           += ($subtotalOrdenes[$id_fc][$id_row] + $ivaOrdenes[$id_row]);
            }

            $footerTableOrdenes =  "<tr class='total'>
                                      <td>TOTALES ORDENES DE COMPRAS</td>
                                      <td style='text-align:right;'>".validar_numero_formato($totalSubtotalOrdenes,$this->IMPRIME_XLS)."</td>
                                      <td style='text-align:right;'>".validar_numero_formato($totalDescuentoOrdenes,$this->IMPRIME_XLS)."</td>
                                      <td style='text-align:right;'>".validar_numero_formato($totalIvaOrdenes,$this->IMPRIME_XLS)."</td>
                                      <td style='text-align:right;'>".validar_numero_formato($totalOrdenes,$this->IMPRIME_XLS)."</td>
                                    </tr>";

            $bodyTable .=  "<tr style='border-left: 1px solid #999;border-right: 1px solid #999'>
                              <td colspan='15' style='padding-left:0px;'>
                                <table class='tableInforme' style='margin-top: 0px'>
                                  <tr class='total'>
                                    <td style='text-align:center;'>CONSECUTIVO ORDEN DE COMPRA</td>
                                    <td style='text-align:center;'>SUBTOTAL</td>
                                    <td style='text-align:center;'>DESCUENTO</td>
                                    <td style='text-align:center;'>IVA</td>
                                    <td style='text-align:center;'>TOTAL</td>
                                  </tr>
                                  ".$bodyTableOrdenes.$footerTableOrdenes."
                                </table>
                              </td>
                            </tr>";

          }
          //SI SE DISCRIMINA POR COMPROBANTES DE EGRESO
          elseif($this->discriminar_items == "comprobantes"){

            $totalValorComprobantes   = 0;
            $bodyTableComprobantes    = "";
            $footerTableComprobantes  = "";

            foreach($this->arrayDocComprobantes[$id_fc] as $id_row => $resultComprobantes){
              $bodyTableComprobantes .=  "<tr>
                                            <td style='text-align:center;font-size:11px'>".$resultComprobantes['consecutivo_documento']."</td>
                                            <td style='text-align:right;font-size:11px'>".validar_numero_formato($resultComprobantes['debe'],$this->IMPRIME_XLS)."</td>
                                          </tr>";

              $totalValorComprobantes += $resultComprobantes['debe'];
            }

            $footerTableComprobantes = "<tr class='total'>
                                          <td>TOTALES COMPROBANTES DE EGRESO</td>
                                          <td style='text-align:right'>".validar_numero_formato($totalValorComprobantes,$this->IMPRIME_XLS)."</td>
                                        </tr>";

            $bodyTable .=  "<tr style='border-left: 1px solid #999;border-right: 1px solid #999'>
                              <td colspan='15' style='padding-left:0px;'>
                                <table class='tableInforme' style='margin-top: 0px'>
                                  <tr class='total'>
                                    <td style='text-align:center'>CONSECUTIVO COMPROBANTE DE EGRESO</td>
                                    <td style='text-align:center'>VALOR</td>
                                  </tr>
                                  ".$bodyTableComprobantes.$footerTableComprobantes."
                                </table>
                              </td>
                            </tr>";

          }

          if($result != end($this->arrayDoc) && $this->discriminar_items != "no"){
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
                  <tr><td style="font-size:13px;"><b>Informe Facturas de Compra</b><br><?php echo $subtitulo_cabecera; ?></td></tr>
                  <tr><td style="font-size:11px;">Desde <?php echo $this->MyInformeFiltroFechaInicio; ?> a <?php echo $this->MyInformeFiltroFechaFinal; ?></td></tr>
                </table>
                <table class="tableInforme" style="width:1015px; border-collapse:collapse;">
                  <?php echo $headTable.$bodyTable.$footerTable; ?>
                </table>
              </div>
            </div>
          </div>
          <br>
        </body>
        <?php

        $texto = ob_get_contents();
      	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER-L';}
      	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
      	if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
      	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=10;}
      	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}

      	if($this->IMPRIME_PDF == 'true'){
          ob_clean();
      		include_once("../../../../misc/MPDF54/mpdf.php");
      		$mpdf = new mPDF(
                  					'utf-8',  		// mode - default ''
                  					$HOJA,			  // format - A4, for example, default ''
                  					12,				    // font size - default 0
                  					'',				    // default font family
                  					$MI,			    // margin_left
                  					$MD,			    // margin right
                  					$MS,			    // margin top
                  					$ML,			    // margin bottom
                  					10,				    // margin header
                  					10,				    // margin footer
                  					$ORIENTACION	// L - landscape, P - portrait
                  				);
          $mpdf->useSubstitutions = false;
          $mpdf->packTableData = true;
      		$mpdf->SetAutoPageBreak(TRUE, 15);
      		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA']);
      		$mpdf->SetDisplayMode( 'fullpage' );
      		$mpdf->SetHeader("");
          $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
      		$mpdf->WriteHTML(utf8_encode($texto));

      		if($PDF_GUARDA == "true"){
            $mpdf->Output($documento.".pdf",'D');
          } else{
            $mpdf->Output($documento.".pdf",'I');
          }
      		exit;
      	} elseif($IMPRIME_HTML == 'true'){
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
        } else{
          $this->getHtmlPdf();
        }
      }
    }

    $objectInform = new InformeFacturaCompra($IMPRIME_HTML,$IMPRIME_XLS,$IMPRIME_PDF,$sucursal,$MyInformeFiltroFechaInicio,$MyInformeFiltroFechaFinal,$arraytercerosJSON,$arrayVendedoresJSON,$arrayCcosJSON,$discriminar_items,$tipo_doc,$estadoDoc,$mysql);
    $objectInform->generate();
?>
