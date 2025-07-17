<?php

include_once('../../../../configuracion/conectar.php');
include_once('../../../../configuracion/define_variables.php');
ob_start();

if($IMPRIME_XLS=='true'){
  header('Content-type: application/vnd.ms-excel;');
  header("Content-Disposition: attachment; filename=informe_facturas_".date("Y_m_d").".xls");
  header("Pragma: no-cache");
  header("Expires: 0");
}

$arraytercerosJSON   = json_decode($arraytercerosJSON);
$arrayVendedoresJSON = json_decode($arrayVendedoresJSON);
$arrayCcosJSON       = json_decode($arrayCcosJSON);

$id_empresa = $_SESSION['EMPRESA'];
$desde      = $MyInformeFiltroFechaInicio;
$hasta      = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;

$divTitleSucursal   = '';
$whereSucursal      = '';
$subtitulo_cabecera = '';
$whereVendedores    = '';

//---------------------------FILTRO POR SUCURSAL------------------------------//

if($sucursal!='' && $sucursal!='global'){
  $whereSucursal = " AND VF.id_sucursal = $sucursal";
  $whereSucursalDV = " AND DV.id_sucursal = $sucursal";

  //CONSULTAR EL NOMBRE DE LA SUCURSAL
  $sql   = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa=$id_empresa AND id=".$sucursal;
  $query = mysql_query($sql,$link);
  $subtitulo_cabecera .= '<b>Sucursal</b> '. mysql_result($query,0,'nombre').'<br>';
}

//---------------------------FILTRO POR TERCEROS------------------------------//

if(!empty($arraytercerosJSON)){
  foreach($arraytercerosJSON as $indice => $id_tercero) {
    $whereClientes .= ($whereClientes == '')? ' VF.id_cliente='.$id_tercero : ' OR VF.id_cliente='.$id_tercero;
  }
  $whereClientes   = " AND (".$whereClientes.")";
  $groupBy         = ',VF.id_cliente';
}

//---------------------------FILTRO POR FECHAS--------------------------------//

if(isset($MyInformeFiltroFechaFinal) && $MyInformeFiltroFechaFinal != ''){
  $whereFechas = " AND VF.fecha_inicio BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";
} else{
  $MyInformeFiltroFechaFinal = date("Y-m-d");
  $whereFechas = " AND VF.fecha_inicio = '".$MyInformeFiltroFechaInicio."'";
}

//--------------------------FILTRO POR VENDEDORES-----------------------------//

if(!empty($arrayVendedoresJSON)){
  foreach ($arrayVendedoresJSON as $indice => $id_vendedor) {
    $whereidVendedores .= ($whereidVendedores == '')? ' VF.id_vendedor='.$id_vendedor : ' OR VF.id_vendedor='.$id_vendedor;
  }
  $whereVendedores = " AND (".$whereidVendedores.")";
  $groupBy         = ($groupBy <> '')? ',VF.id_vendedor' : 'VF.id_vendedor';
}

//----------------------FILTRO POR CENTROS DE COSTO---------------------------//

if(!empty($arrayCcosJSON)){
  foreach($arrayCcosJSON as $indice => $id_ccos){
    $whereidCentroCostos .= ($whereidCentroCostos == '')? ' VF.id_centro_costo='.$id_ccos : ' OR VF.id_centro_costo='.$id_ccos;
  }
  $whereCentroCostos  = " AND (".$whereidCentroCostos.")";
  $groupBy            = ($groupBy <> '')? ',VF.id_centro_costo' : 'VF.id_centro_costo';
}

//---------------------FILTRO POR FACTURACION ELECTRONICA---------------------//

if($facturacion_electronica == 'Todas'){
  $subtitulo_cabecera .= "Todas Las Facturas";
} else if($facturacion_electronica == 'Si'){
  $subtitulo_cabecera .= "Facturas Electronicas Enviadas";
  $whereFE = " AND VF.response_FE LIKE '%Ejemplar recibido exitosamente pasara a verificacion%'";
} else{
  $subtitulo_cabecera .= "Facturas Electronicas No Enviadas";
  $whereFE = " AND (VF.response_FE IS NULL OR VF.response_FE NOT LIKE '%Ejemplar recibido exitosamente pasara a verificacion%')";
}


//---------------------------CONSULTA PRINCIPAL-------------------------------//

$sql = "SELECT
          VF.id,
          VF.fecha_inicio,
          VF.fecha_vencimiento,
          VF.prefijo,
          VF.numero_factura,
          VF.nit,
          VF.cliente,
          VF.nombre_vendedor,
          VF.codigo_centro_costo,
          VF.centro_costo,
          VF.sucursal,
          VF.bodega,
          VF.sucursal_cliente,
          VF.estado,
          VF.exento_iva,
          VF.observacion,
          VF.nombre_usuario_FE,
          VF.cedula_usuario_FE,
          VF.fecha_FE,
          VF.hora_FE,
          VFI.consecutivo_referencia,
          VFI.nombre_consecutivo_referencia
        FROM
          ventas_facturas AS VF
        LEFT JOIN ventas_facturas_inventario AS VFI ON VFI.id_factura_venta = VF.id 
        WHERE
          VF.activo = 1
        AND
          VF.id_empresa = $id_empresa
        AND
          (VF.estado = 1 OR VF.estado=2 OR VF.estado = 3)
        AND
          VF.id_saldo_inicial = 0
          $whereSucursal
          $whereClientes
          $whereVendedores
          $whereFechas
          $whereCentroCostos
          $whereFE
        GROUP BY
        VFI.id_consecutivo_referencia,
	      VF.id 
        ORDER BY
          VF.numero_factura,
          VF.fecha_inicio DESC";

$query = mysql_query($sql,$link);

$acumuladoSubtotal   = 0;
$acumuladoDescuento  = 0;
$acumuladoIva        = 0;
$acumuladoTotal      = 0;
$whereId             = '';

while($row = mysql_fetch_array($query)){
  $whereId .= ($whereId == '')? 'id_factura_venta='.$row['id'] : ' OR id_factura_venta='.$row['id'] ;

  if($row['estado'] == 3){
    $row['subtotal'] = 0;
    $row['iva']      = 0;
  }
  $tipoDoc = "";
  $docCruce ="";
  if($row['nombre_consecutivo_referencia']){
    $tipoDoc = ($row['nombre_consecutivo_referencia']=="Remision")? "RV" : "CT";
    $docCruce = $tipoDoc.$row['consecutivo_referencia'];
  }

  $numero_factura = ($row['prefijo'] != "")? $row['prefijo'].' '.$row['numero_factura'] : $row['numero_factura'] ;
  $arrayFacturas[$row['id']] = array(
                                      'fecha_inicio'             => $row['fecha_inicio'],
                                      'fecha_vencimiento'        => $row['fecha_vencimiento'],
                                      'numero_factura'           => $numero_factura,
                                      'nit'                      => $row['nit'],
                                      'cliente'                  => $row['cliente'],
                                      'nombre_vendedor'          => $row['nombre_vendedor'],
                                      'codigo_centro_costo'      => $row['codigo_centro_costo'],
                                      'centro_costo'             => $row['centro_costo'],
                                      'sucursal'                 => $row['sucursal'],
                                      'bodega'                   => $row['bodega'],
                                      'sucursal_cliente'         => $row['sucursal_cliente'],
                                      'subtotal'                 => 0,
                                      'iva'                      => 0,
                                      'ReteFuente'               => '',
                                      'ReteIva'                  => '',
                                      'ReteIca'                  => '',
                                      'estado'                   => $row['estado'],
                                      'exento_iva'               => $row['exento_iva'],
                                      'observacion'              => $row['observacion'],
                                      'nombre_usuario_FE'        => $row['nombre_usuario_FE'],
                                      'cedula_usuario_FE'        => $row['cedula_usuario_FE'],
                                      'fecha_FE'                 => $row['fecha_FE'],
                                      'hora_FE'                  => $row['hora_FE'],
                                      'docCruce'                 => ($arrayFacturas[$row['id']]['docCruce'])?
                                                                    $arrayFacturas[$row['id']]['docCruce']." - ".$docCruce
                                                                    : $docCruce
                                    );
}

// SI SE DETALLA POR DEVOLUCION
if($detallado_documentos == 'devolucion'){
  // CONSULTAR LA CABECERA DE LA NOTA DE DEVOLUCION
  $sql = "SELECT
            DV.id,
            DV.consecutivo,
            DV.fecha_registro,
            DV.numero_documento_venta,
            DV.sucursal,
            DV.bodega,
            DV.nit,
            DV.cliente,
            FV.codigo_centro_costo,
            FV.centro_costo,
            FV.id AS id_factura_venta,
            DV.nombre_usuario_DE,
            DV.cedula_usuario_DE,
            DV.fecha_DE,
            DV.hora_DE,
            DV.observacion,
            FV.exento_iva
          FROM
            devoluciones_venta AS DV
          LEFT JOIN
            ventas_facturas AS FV ON FV.id = DV.id_documento_venta
          WHERE
            DV.activo = 1
          AND
            DV.estado = 1
          AND
            DV.id_empresa = $id_empresa
          AND
            DV.documento_venta = 'Factura'
            $whereSucursalDV
          AND
            DV.fecha_registro
          BETWEEN
            '$MyInformeFiltroFechaInicio' AND '$MyInformeFiltroFechaFinal'";

  $query = $mysql->query($sql,$mysql->link);

  while($row = $mysql->fetch_array($query)){
      $whereIdDev .= ($whereIdDev == '')? "id_devolucion_venta=".$row['id'] : " OR id_devolucion_venta=".$row['id'] ;

      $arrayDevoluciones[$row['id']] = array(
                                              'consecutivo'            => $row['consecutivo'],
                                              'fecha_registro'         => $row['fecha_registro'],
                                              'numero_documento_venta' => $row['numero_documento_venta'],
                                              'sucursal'               => $row['sucursal'],
                                              'bodega'                 => $row['bodega'],
                                              'nit'                    => $row['nit'],
                                              'cliente'                => $row['cliente'],
                                              'codigo_centro_costo'    => $row['codigo_centro_costo'],
                                              'centro_costo'           => $row['centro_costo'],
                                              'nombre_usuario_DE'      => $row['nombre_usuario_DE'],
                                              'cedula_usuario_DE'      => $row['cedula_usuario_DE'],
                                              'fecha_DE'               => $row['fecha_DE'],
                                              'hora_DE'                => $row['hora_DE'],
                                              'observaciones'          => $row['observacion'],
                                              'id_factura_venta'       => $row['id_factura_venta'],
                                              'exento_iva'             => $row['exento_iva']
                                            );
  }

  // CONSULTAR EL CUERPO DE LA NOTA DE DEVOLUCION
  $sql = "SELECT
            id,
            id_devolucion_venta,
            codigo,
            nombre_unidad_medida,
            cantidad_unidad_medida,
            nombre,
            cantidad,
            costo_unitario,
            tipo_descuento,
            descuento,
            valor_impuesto,
            observaciones
          FROM
            devoluciones_venta_inventario
          WHERE
            activo = 1
          AND ($whereIdDev)";

  $query = $mysql->query($sql,$mysql->link);

  while($row = $mysql->fetch_array($query)){
    // SI ESTADO ES 3 DEJAR VACIOS LOS CAMPOS ACUMULADOS
    if ($arrayDevoluciones[$row['id_devolucion_venta']]['estado'] == 3) {
      $row['saldo_cantidad']   = 0;
      $row['descuento']        = 0;
      $row['costo_unitario']   = 0;
      $row['costo_inventario'] = 0;
      $row['valor_impuesto']   = 0;
    }

    $total_articulo = 0;
    $iva            = 0;
    $subtotal       = 0;
    $descuento      = 0;

    //SI EL DESCUENTO DEL ARTICULO FUE POR PORCENTAJE
    if ($row['descuento']>0) {
        $descuento = ($row['tipo_descuento'] == 'porcentaje')?
                                                            ((($row['cantidad'] * $row['costo_unitario']) * $row['descuento']) / 100)
                                                            :
                                                            $row['descuento']
                                                            ;
    }

    $subtotal = ($row['cantidad'] * $row['costo_unitario']);

    //CALCULAR LOS VALORES ACUMULADOS
    if($arrayDevoluciones[$row['id_devolucion_venta']]['exento_iva'] <> 'Si') {
      $iva = (($subtotal - $descuento) * $row['valor_impuesto']) / 100;
    }
    $acumuladoSubtotalDV  += $subtotal;
    $acumuladoDescuentoDV += $descuento;
    $acumuladoIvaDV       += $iva;

    $arrayDevoluciones[$row['id_devolucion_venta']]['costo']     += ($row['cantidad'] * $row['costo_unitario']);
    $arrayDevoluciones[$row['id_devolucion_venta']]['subtotal']  += $subtotal;
    $arrayDevoluciones[$row['id_devolucion_venta']]['descuento'] += $descuento;
    $arrayDevoluciones[$row['id_devolucion_venta']]['iva']       += $iva;
    $arrayItemsDevoluciones[$row['id_devolucion_venta']][$row['id']] = array(
                                                                              'id_devolucion_venta'    => $row['id_devolucion_venta'],
                                                                              'codigo'                 => $row['codigo'],
                                                                              'nombre_unidad_medida'   => $row['nombre_unidad_medida'],
                                                                              'cantidad_unidad_medida' => $row['cantidad_unidad_medida'],
                                                                              'nombre'                 => $row['nombre'],
                                                                              'cantidad'               => $row['cantidad'],
                                                                              'costo_unitario'         => $row['costo_unitario'],
                                                                              'tipo_descuento'         => $row['tipo_descuento'],
                                                                              'descuento'              => $row['descuento'],
                                                                              'valor_impuesto'         => $row['valor_impuesto'],
                                                                              'observaciones'          => $row['observaciones']
                                                                            );
  }
  //echo json_encode($arrayDevoluciones);
}

// CONSULTAR LAS RETENCIONES Y ITEMS
if($whereId != ''){

    $sql = "SELECT
              id,
              id_factura_venta,
              consecutivo_referencia,
              nombre_consecutivo_referencia,
              codigo,
              nombre_unidad_medida,
              cantidad_unidad_medida,
              nombre,
              cantidad,
              saldo_cantidad,
              costo_unitario,
              costo_inventario,
              tipo_descuento,
              descuento,
              valor_impuesto,
              observaciones
            FROM
              ventas_facturas_inventario
            WHERE
              activo = 1
            AND ($whereId)";

    $query = mysql_query($sql,$link);

    while ($row = mysql_fetch_array($query)) {

        // SI ESTADO ES 3 DEJAR VACIOS LOS CAMPOS ACUMULADOS
        if($arrayFacturas[$row['id_factura_venta']]['estado'] == 3){
          $row['saldo_cantidad']   = 0;
          $row['descuento']        = 0;
          $row['costo_unitario']   = 0;
          $row['costo_inventario'] = 0;
          $row['valor_impuesto']   = 0;
        }

        $total_articulo = 0;
        $iva            = 0;
        $subtotal       = 0;
        $descuento      = 0;

        //SI EL DESCUENTO DEL ARTICULO FUE POR PORCENTAJE
        if ($row['descuento']>0) {
            $descuento =($row['tipo_descuento']=='porcentaje')?
                                                                ((($row['cantidad']*$row['costo_unitario'])*$row['descuento'])/100)
                                                                :
                                                                $row['descuento']
                                                                ;
        }

        $subtotal =($row['cantidad']*$row['costo_unitario']);

        //CALCULAR LOS VALORES ACUMULADOS
        if ($arrayFacturas[$row['id_factura_venta']]['exento_iva'] <> 'Si') {
            $iva = (($subtotal-$descuento) * $row['valor_impuesto']) / 100;
        }

        $acumuladoSubtotal  += $subtotal;
        $acumuladoDescuento += $descuento;
        $acumuladoIva       += $iva;

        $arrayFacturas[$row['id_factura_venta']]['costo']     += ($row['cantidad'] * $row['costo_inventario']);
        $arrayFacturas[$row['id_factura_venta']]['subtotal']  += $subtotal;
        $arrayFacturas[$row['id_factura_venta']]['descuento'] += $descuento;
        $arrayFacturas[$row['id_factura_venta']]['iva']       += $iva;

        $tipoDoc = ($row['nombre_consecutivo_referencia']=="Remision")? "RV" : "CT";
        $arrayItemsFactura[$row['id_factura_venta']][$row['id']] = array(
                                                                          'id_factura_venta'       => $row['id_factura_venta'],
                                                                          'codigo'                 => $row['codigo'],
                                                                          'docCruce'               => $tipoDoc.$row['consecutivo_referencia'],
                                                                          'nombre_unidad_medida'   => $row['nombre_unidad_medida'],
                                                                          'cantidad_unidad_medida' => $row['cantidad_unidad_medida'],
                                                                          'nombre'                 => $row['nombre'],
                                                                          'cantidad'               => $row['cantidad'],
                                                                          'saldo_cantidad'         => $row['saldo_cantidad'],
                                                                          'costo_unitario'         => $row['costo_unitario'],
                                                                          'costo_inventario'       => $row['costo_inventario'],
                                                                          'tipo_descuento'         => $row['tipo_descuento'],
                                                                          'descuento'              => $row['descuento'],
                                                                          'valor_impuesto'         => ($arrayFacturas[$row['id_factura_venta']]['exento_iva'] != 'Si')? $row['valor_impuesto'] : '0',
                                                                          'observaciones'          => $row['observaciones']
                                                                        );
    }

    //CONSULTAR RETENCIONES
    $sql   = "SELECT
                id_factura_venta,
                tipo_retencion,
                retencion,
                valor,
                base
              FROM
                ventas_facturas_retenciones
              WHERE
                activo = 1
              AND ($whereId)";
              // echo $sql;
    $queryRetenciones = mysql_query($sql,$link);

    while($row = mysql_fetch_array($queryRetenciones)){

      // AutoRetencion
      if($row['tipo_retencion']=='ReteFuente'){
          $arrayFacturas [$row['id_factura_venta']]['ReteFuente'][] = array(
                                                                              'retencion' => $row['retencion'],
                                                                              'valor'     => $row['valor'],
                                                                              'base'      => $row['base'],
                                                                            );
      } else if($row['tipo_retencion'] == 'ReteIva'){
          $arrayFacturas [$row['id_factura_venta']]['ReteIva'][] = array(
                                                                          'retencion' => $row['retencion'],
                                                                          'valor'     => $row['valor'],
                                                                          'base'      => $row['base'],
                                                                        );
      } else if($row['tipo_retencion'] == 'ReteIca'){
          $arrayFacturas [$row['id_factura_venta']]['ReteIca'][] = array(
                                                                          'retencion' => $row['retencion'],
                                                                          'valor'     => $row['valor'],
                                                                          'base'      => $row['base'],
                                                                        );
      }
    }
}

$cont    = 0;
$factura = 0;

$titulosTipoDoc = ($IMPRIME_XLS == 'true')? '<td style="width:200px; text-align:center"><b>NIT</b></td>
                                             <td style="width:200px; text-align:center"><b>CLIENTE</b></td>
                                             <td style="width:200px; text-align:center"><b>VENDEDOR</b></td>
                                             <td style="width:100px; text-align:center"><b>SUCURSAL CLIENTE</b></td>
                                             <td style="width:100px; text-align:center"><b>CODIGO CENTRO COSTO</b></td>'
                                             :
                                            '<td style="width:180px; text-align:center"><b>CLIENTE</b></td>
                                             <td style="width:100px; text-align:center"><b>SUCURSAL CLIENTE</b></td>' ;

$colspanTotal = ($IMPRIME_XLS == 'true')? 10 : 7 ;
if($mostrarDocCruce == "Si"){$colspanTotal++;}


//LIMPIAR LAS VARIABLES
$acumuladoCantidad   = 0;
$acumuladoPendiente  = 0;
$acumuladoCosto      = 0;
$acumuladoTotal      = 0;
$acumuladoCantidad   = 0;
$acumuladoPendiente  = 0;
$acumuladoIva        = 0;
$acumuladoUtilidad   = 0;
$acumuladoTotalItems = 0;

// RECORRER EL ARRAY ARA ARMAR EL CUERPO DEL INFORME
foreach($arrayFacturas as $id_factura_venta => $arrayResul){
    // RECORRER LAS RETENCIONES PARA GENERAR EL CALCULO
    $ReteFuente = 0;
    $ReteIva    = 0;
    $ReteIca    = 0;

    foreach ($arrayResul['ReteFuente'] as $key => $arrayResulRetencion) {
      if ($arrayResulRetencion['base']<$arrayResul['subtotal']) {
        $ReteFuente += (($arrayResul['subtotal']-$arrayResul['descuento'])*$arrayResulRetencion['valor'])/100;
      }
    }
    foreach ($arrayResul['ReteIva'] as $key => $arrayResulRetencion) {
      if ($arrayResulRetencion['base']<$arrayResul['iva']) {
        $ReteIva += ($arrayResul['iva']*$arrayResulRetencion['valor'])/100;
      }
    }
    foreach ($arrayResul['ReteIca'] as $key => $arrayResulRetencion) {
      if ($arrayResulRetencion['base']<($arrayResul['subtotal']-$arrayResul['descuento'])) {
        $ReteIca += (($arrayResul['subtotal']-$arrayResul['descuento'])*$arrayResulRetencion['valor'])/100;
      }
    }

    $ivaTotal = $arrayResul['iva'] - $ReteIva;
    $total    = (($arrayResul['subtotal']-$arrayResul['descuento'])+$ivaTotal)-$ReteFuente-$ReteIca;
    $utilidad = $arrayResul['subtotal']-$arrayResul['descuento']-$arrayResul['costo'];

    $acumuladoReteFuente    += $ReteFuente;
    $acumuladoReteIva       += $ReteIva;
    $acumuladoIva += $arrayResul['iva'];
    $acumuladoReteIca       += $ReteIca;
    $acumuladoTotalUtilidad += $utilidad;
    $acumuladoTotal         += $total;
    $style                   =($style!='')? '' : 'background:#f7f7f7;' ;
    $styleDocCancelado       =($arrayResul['estado'] == 3)? 'color:#F00A0A;font-style: italic;font-weight:bold;' : '' ;

    if($facturacion_electronica == "Si" || $facturacion_electronica == "Todas"){
      $columnasFE =  '<td style="width:80px; text-align:center;"><b>USUARIO FE</b></td>
                      <td style="width:80px; text-align:center;"><b>CEDULA FE</b></td>
                      <td style="width:80px; text-align:center;"><b>FECHA FE</b></td>
                      <td style="width:80px; text-align:center;"><b>HORA FE</b></td>';

      $resultadoFE = '<td style="width:80px;">'.$arrayResul['nombre_usuario_FE'].'</td>
                      <td style="width:80px;">'.$arrayResul['cedula_usuario_FE'].'</td>
                      <td style="width:80px;">'.$arrayResul['fecha_FE'].'</td>
                      <td style="width:80px;">'.$arrayResul['hora_FE'].'</td>';
    }

    // SI SE DISCRIMINAN LOS ITEMS DE LAS FACTURAS
    if ($detallado_items == 'si') {
        // SI SE EXPORTA DETALLADO Y EN EXCEL
        if ($IMPRIME_XLS == 'true') {
            //MOSTRAR LOS ARTICULOS QUE PERTENECEN A LA FACTURA
            foreach($arrayItemsFactura[$id_factura_venta] as $id_registro => $arrayResul2){
                $simbolo_descuento = ($arrayResul2['tipo_descuento'] == 'porcentaje')? ' %' : ' $' ;
                $descuento         = 0;
                $subtotalArticulo  = 0;
                $ivaArticulo       = 0;
                $totalArticulo     = 0;
                $totalSinIva       = 0;
                $costoArticulo     = 0;
                $totalArticulo     = 0;

                //SI TIENE DESCUENTO
                if ($arrayResul2['descuento'] > 0) {
                  $descuento = ($arrayResul2['tipo_descuento'] == 'porcentaje')?
                                                                                ((($arrayResul2['cantidad'] * $arrayResul2['costo_unitario']) * $arrayResul2['descuento']) / 100)
                                                                                :
                                                                                $arrayResul2['descuento'];
                }

                $subtotalArticulo = ($arrayResul2['cantidad'] * $arrayResul2['costo_unitario']) - $descuento;
                $ivaArticulo      = ($arrayResul2['valor_impuesto'] > 0)? ($subtotalArticulo*$arrayResul2['valor_impuesto']) / 100 : 0 ;
                $totalArticulo    = $subtotalArticulo + $ivaArticulo;
                $totalSinIva      = $totalArticulo;
                $costoArticulo    = $arrayResul2['cantidad'] * $arrayResul2['costo_inventario'];

                $bodyTable.= '<tr>
                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['sucursal'].' </td>
                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['fecha_inicio'].'</td>
                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['fecha_vencimiento'].'</td>
                                <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['numero_factura'].'</td>
                                <td style="padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['nit'].'</td>
                                <td style="padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>
                                <td style="padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['nombre_vendedor'].'</td>
                                <td style="padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['sucursal_cliente'].'</td>
                                <td style="text-align:left;'.$styleDocCancelado.'">'.$arrayResul['codigo_centro_costo'].'</td>
                                <td style="text-align:left;'.$styleDocCancelado.'">'.$arrayResul['centro_costo'].'</td>
                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['subtotal'],$IMPRIME_XLS).'</td>
                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['descuento'],$IMPRIME_XLS).'</td>
                                <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['iva'],$IMPRIME_XLS).'</td>
                                <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteFuente,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteFuente,$IMPRIME_XLS)).'</td>
                                <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteIca,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteIca,$IMPRIME_XLS)).'</td>
                                <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteIva,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteIva,$IMPRIME_XLS)).'</td>
                                '.(($detalla_utilidad=='Si')? '<td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($utilidad,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($utilidad,$IMPRIME_XLS)).'</td>' : '' ).'
                                <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($total,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($total,$IMPRIME_XLS)).'</td>
                                <td style="padding-left: 10px;'.$styleDocCancelado.''.$styleDocCancelado.'">'.$arrayResul['observacion'].'</td>
                                <td style="width: 95px;">&nbsp;&nbsp;'.$arrayResul2['codigo'].'</td>
                                <td style="width: 340px;">'.$arrayResul2['nombre'].'</td>
                                <td style="width: 80px;">'.$arrayResul2['docCruce'].'</td>
                                <td style="width: 80px;">'.$arrayResul2['nombre_unidad_medida'].' x '.$arrayResul2['cantidad_unidad_medida'].'</td>
                                <td style="width: 70px;text-align:right;">'.($arrayResul2['cantidad'] * 1).'</td>
                                <td style="width: 120px;text-align:right;">'.validar_numero_formato($arrayResul2['costo_unitario'],$IMPRIME_XLS).'</td>
                                <td style="width: 70px;text-align:right;">'.validar_numero_formato($descuento,$IMPRIME_XLS).' </td>
                                <td style="width: 120px;text-align:right;">'.validar_numero_formato($subtotalArticulo,$IMPRIME_XLS).'</td>
                                <td style="width: 90px;text-align:right;">'.validar_numero_formato($ivaArticulo,$IMPRIME_XLS).' ('.validar_numero_formato($arrayResul2['valor_impuesto'],$IMPRIME_XLS).'%)</td>
                                '.(($detalla_utilidad=='Si')? '<td style="width: 90px;text-align:right;">'.validar_numero_formato(($subtotalArticulo-$costoArticulo),$IMPRIME_XLS).'</td>' : '' ).'
                                <td style="width: 120px;text-align:right;">'.validar_numero_formato($totalArticulo,$IMPRIME_XLS).'&nbsp;&nbsp;</td>
                                <td style="width: 80px;">'.$arrayResul2['observaciones'].'</td>
                                '.$resultadoFE.'
                              </tr>';

                //ACUMULAR LOS VALORES
                $acumuladoCantidad   += $arrayResul2['cantidad'];
                $acumuladoPendiente  += $arrayResul2['saldo_cantidad'];
                $acumuladoCosto      += $arrayResul2['costo_unitario'];
                $acumuladoDescuento  += $arrayResul2['descuento'];
                $acumuladoIva        += $ivaArticulo;
                $acumuladoUtilidad   += $totalSinIva-$costoArticulo;
                $acumuladoTotalItems += $totalArticulo;

            }
        }
        else{ // SI ES DETALLADO PERO NO EN EXCEL
            $noAnexar = true;
            $bodyTable .= '<table class="defaultFont" style="width:100%;border-collapse: collapse;border:1px solid #999;border-top:none;border-bottom:none;">
                            <tr class="titulos">
                              <td style="width:70px; text-align:center;"><b>SUCURSAL</b></td>
                              <td style="width:70px; text-align:center;"><b>FECHA</b></td>
                              <td style="width:70px; text-align:center;"><b>FECHA VENCIMIENTO</b></td>
                              <td style="width:70px; text-align:center;"><b>N. FACTURA</b></td>
                              '.$titulosTipoDoc.'
                              <td style="width:80px; text-align:center"><b>CENTRO COSTOS</b></td>
                              <td style="width:80px; text-align:center;"><b>SUBTOTAL</b></td>
                              <td style="width:80px; text-align:center;"><b>DESCUENTO</b></td>
                              <td style="width:80px; text-align:center;"><b>IMPUESTO</b></td>
                              <td style="width:80px; text-align:center;"><b>RETE. FUENTE</b></td>
                              <td style="width:80px; text-align:center;"><b>RETE. ICA</b></td>
                              <td style="width:80px; text-align:center;"><b>RETE. IVA</b></td>
                              '.(($detalla_utilidad=='Si')? '<td style="width:80px; text-align:center;"><b>UTILIDAD</b></td>' : '' ).'
                              <td style="width:80px; text-align:center;"><b>TOTAL</b></td>
                              ' . (($facturacion_electronica == 'Si')? $columnasFE : '') . '
                            </tr>';

            $camposTipoDoc = ($IMPRIME_XLS == 'true')? '<td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['nit'].'</td>
                                                        <td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>
                                                        <td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['nombre_vendedor'].'</td>
                                                        <td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['sucursal_cliente'].'</td>
                                                        <td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['codigo_centro_costo'].'</td>
                                                        <td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['centro_costo'].'</td>'
                                                         :
                                                       '<td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>
                                                        <td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['sucursal_cliente'].'</td>
                                                        <td style="padding-left:10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['centro_costo'].'</td>' ;

            $bodyTable.='
                        <tr style="'.$style.' ">
                            <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['sucursal'].' </td>
                            <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['fecha_inicio'].'</td>
                            <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['fecha_vencimiento'].'</td>
                            <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['numero_factura'].'</td>
                            '.$camposTipoDoc.'

                            <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['subtotal'],$IMPRIME_XLS).'</td>
                            <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['descuento'],$IMPRIME_XLS).'</td>
                            <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['iva'],$IMPRIME_XLS).'</td>

                            <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteFuente,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteFuente,$IMPRIME_XLS)).'</td>
                            <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteIca,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteIca,$IMPRIME_XLS)).'</td>
                            <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteIva,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteIva,$IMPRIME_XLS)).'</td>
                            '.(($detalla_utilidad=='Si')? '<td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($utilidad,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($utilidad,$IMPRIME_XLS)).'</td>' : '' ).'
                            <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($total,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($total,$IMPRIME_XLS)).'</td>
                            ' . (($facturacion_electronica == 'Si')? $resultadoFE : '') . '
                        </tr>
                        </table>';

            $bodyTable.= '<table class="defaultFont" style="width:100%; border-collapse:collapse;border:1px solid #999;border-top:none;border-bottom:none;" >
                            <tr style=" border:1px solid #999;border-top:none;border-bottom:none;">
                              <td style="width:95px;">&nbsp;&nbsp;<b>Codigo</b></td>
                              <td style="width:340px;"><b>Nombre</b></td>
                              <td style="width:80px;"><b>DOC. CRUCE</b></td>
                              <td style="width:80px;"><b>Unidad</b></td>
                              <td style="text-align:right;width:70px;"><b>Cantidad</b></td>
                              <td style="text-align:right;width:120px;"><b>Precio</b></td>
                              <td style="text-align:right;width:70px;"><b>Descuento</b></td>
                              <td style="text-align:right;width:90px;"><b>Iva</b></td>
                              '.(($detalla_utilidad == 'Si')? '<td style="text-align:right;width:90px;"><b>utilidad</b></td>' : '' ).'
                              <td style="text-align:right;width:120px;"><b>Total&nbsp;&nbsp;</b></td>
                            </tr>
                          </table>';

            //MOSTRAR LOS ARTICULOS QUE PERTENECEN A LA FACTURA
            foreach($arrayItemsFactura[$id_factura_venta] as $id_registro => $arrayResul2){
                $simbolo_descuento = ($arrayResul2['tipo_descuento'] == 'porcentaje')? ' %' : ' $' ;

                //SI TIENE DESCUENTO
                if($arrayResul2['descuento'] > 0){
                $totalArticulo = ($arrayResul2['tipo_descuento'] == 'porcentaje')?
                                                                                ($arrayResul2['cantidad'] * $arrayResul2['costo_unitario'])-((($arrayResul2['cantidad'] * $arrayResul2['costo_unitario']) * $arrayResul2['descuento'])/100)
                                                                                 :
                                                                                ($arrayResul2['cantidad'] * $arrayResul2['costo_unitario'])-$arrayResul2['descuento']
                                                                                 ;
                } else{
                  $totalArticulo = $arrayResul2['cantidad'] * $arrayResul2['costo_unitario'];
                }

                $ivaArticulo      = ($arrayResul2['valor_impuesto'] > 0)? ($totalArticulo * $arrayResul2['valor_impuesto']) / 100 : 0 ;
                $totalSinIva      = $totalArticulo;
                $costoArticulo    = $arrayResul2['cantidad'] * $arrayResul2['costo_inventario'];
                $totalArticulo   += $ivaArticulo;

                $bodyTable .='<table class="defaultFont" style="width:100%; border-collapse:collapse;border:1px solid #999;border-top:none;border-bottom:none;" >
                                <tr>
                                  <td style="width: 95px;">&nbsp;&nbsp;'.$arrayResul2['codigo'].'</td>
                                  <td style="width: 340px;">'.$arrayResul2['nombre'].'</td>
                                  <td style="width: 80px;">'.$arrayResul2['docCruce'].'</td>
                                  <td style="width: 80px;">'.$arrayResul2['nombre_unidad_medida'].' x '.$arrayResul2['cantidad_unidad_medida'].'</td>
                                  <td style="width: 70px;text-align:right;">'.($arrayResul2['cantidad'] * 1).'</td>
                                  <td style="width: 120px;text-align:right;">'.validar_numero_formato($arrayResul2['costo_unitario'],$IMPRIME_XLS).'</td>
                                  <td style="width: 70px;text-align:right;">'.validar_numero_formato($arrayResul2['descuento'],$IMPRIME_XLS).' '.$simbolo_descuento.'</td>
                                  <td style="width: 90px;text-align:right;">'.validar_numero_formato($ivaArticulo,$IMPRIME_XLS).' ('.validar_numero_formato($arrayResul2['valor_impuesto'],$IMPRIME_XLS).'%)</td>
                                  '.(($detalla_utilidad=='Si')? '<td style="width: 90px;text-align:right;">'.validar_numero_formato(($totalSinIva-$costoArticulo),$IMPRIME_XLS).'</td>' : '' ).'
                                  <td style="width: 120px;text-align:right;">'.validar_numero_formato($totalArticulo,$IMPRIME_XLS).'&nbsp;&nbsp;</td>
                                </tr>
                              </table>';

                //ACUMULAR LOS VALORES
                $acumuladoCantidad   += $arrayResul2['cantidad'];
                $acumuladoPendiente  += $arrayResul2['saldo_cantidad'];
                $acumuladoCosto      += $arrayResul2['costo_unitario'];
                $acumuladoDescuento  += $arrayResul2['descuento'];
                $acumuladoIva        += $ivaArticulo;
                $acumuladoUtilidad   += $totalSinIva-$costoArticulo;
                $acumuladoTotalItems += $totalArticulo;
            }

            //TOTALES DE LA FACTURA DISCRIMINANDO LOS ARTICULOS
            if($factura != $id_factura_venta){
              if($id_factura_venta != 0){
                  $bodyTable .='<table class="defaultFont" style="width:100%; border-collapse:collapse;border:1px solid #999;border-top:none;" >
                                  <tr class="total" style=" border:1px solid #999;border-top:none;" >
                                      <td style="width: 95px;"><b>&nbsp;&nbsp;TOTALES</b></td>
                                      <td style="width: 340px;"><div style="width:300px;height:12px"></div></td>
                                      <td style="width: 80px;"><div style="width:50px;height:12px"></div></td>
                                      <td style="width: 70px;text-align:right;"><b>'.$acumuladoCantidad.'</b></td>
                                      <td style="width: 120px;text-align:right;"><b>'.validar_numero_formato($acumuladoCosto,$IMPRIME_XLS).'</b></td>
                                      <td style="width: 70px;"><div style="width:50px;height:12px"></div></td>
                                      <td style="width: 90px;text-align:right;">'.validar_numero_formato($acumuladoIva,$IMPRIME_XLS).'</td>
                                      '.(($detalla_utilidad=='Si')? '<td style="width: 90px;text-align:right;">'.validar_numero_formato($acumuladoUtilidad,$IMPRIME_XLS).'</td>' : '').'
                                      <td style="width: 130px;text-align:right;"><b>'.validar_numero_formato($acumuladoTotalItems,$IMPRIME_XLS).'&nbsp;&nbsp;</b></td>
                                  </tr>
                                </table>';

                  //LIMPIAR LAS VARIABLES
                  $acumuladoCantidad   = 0;
                  $acumuladoPendiente  = 0;
                  $acumuladoCosto      = 0;
                  $acumuladoDescuento  = 0;
                  $acumuladoIva        = 0;
                  $acumuladoUtilidad   = 0;
                  $acumuladoTotalItems = 0;
              }
                $factura=$id_factura_venta;
            }
            $bodyTable.='</table></td></tr><tr><td>&nbsp;</td></tr>';
        }// FIN SI NO ES DETALLADO POR ITEMS Y A EXCEL
    }
    // SI NO SE DISCRIMINANA LOS ITEMS DE LAS FACTURAS
    else{

      $camposTipoDoc=($IMPRIME_XLS=='true')? '<td style="padding-left: 10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['nit'].'</td>
                                              <td style="padding-left: 10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>
                                              <td style="padding-left: 10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['nombre_vendedor'].'</td>
                                              <td style="padding-left: 10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['sucursal_cliente'].'</td>
                                              <td style="padding-left: 10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['codigo_centro_costo'].'</td>
                                              <td style="padding-left: 10px; text-align:center;'.$styleDocCancelado.'">'.$arrayResul['centro_costo'].'</td>'
                                              :
                                             '<td style="padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>
                                              <td style="padding-left: 10px;'.$styleDocCancelado.'">'.$arrayResul['sucursal_cliente'].'</td>
                                              <td style="padding-left: 10px;'.$styleDocCancelado.''.$styleDocCancelado.'">'.$arrayResul['centro_costo'].'</td>' ;
      
      $cotizacion = ($mostrarDocCruce == "Si")? 
                    '<td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['docCruce'].'</td>'
                  : '';
      $bodyTable .='<tr style="'.$style.'">
                      <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['sucursal'].' </td>
                      <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['fecha_inicio'].'</td>
                      <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['fecha_vencimiento'].'</td>
                      <td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['numero_factura'].'</td>'
                      . $cotizacion . $camposTipoDoc .'
                      <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['subtotal'],$IMPRIME_XLS).'</td>
                      <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['descuento'],$IMPRIME_XLS).'</td>
                      <td style="text-align:right;'.$styleDocCancelado.'">'.validar_numero_formato($arrayResul['iva'],$IMPRIME_XLS).'</td>

                      <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteFuente,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteFuente,$IMPRIME_XLS)).'</td>
                      <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteIca,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteIca,$IMPRIME_XLS)).'</td>
                      <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($ReteIva,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($ReteIva,$IMPRIME_XLS)).'</td>

                      '.(($detalla_utilidad=='Si')? '<td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($utilidad,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($utilidad,$IMPRIME_XLS)).'</td>' : '' ).'
                      <td style="text-align:right;'.$styleDocCancelado.'">'.(($IMPRIME_XLS=='true')? round(validar_numero_formato($total,$IMPRIME_XLS),$_SESSION['DECIMALESMONEDA']) : validar_numero_formato($total,$IMPRIME_XLS)).'</td>
                      '.(($IMPRIME_XLS == 'true')? '<td style="text-align:center;'.$styleDocCancelado.'" >'.$arrayResul['observacion'].'</td>' : '') .
                      (($facturacion_electronica == 'Si' && $IMPRIME_XLS == 'true')? $resultadoFE : '') .
                      (($facturacion_electronica == 'Si' && $IMPRIME_XLS != 'true')? $resultadoFE : '') .
                      (($IMPRIME_XLS == 'true' && $facturacion_electronica == 'Todas')? $resultadoFE : '') .'
                    </tr>';
    }
}

//--------------CONSTRUCCION DEL INFORME GENERADO EN PANTALLA-----------------//

//SI NO SE DETALLA POR ITEM
if ($detallado_items == 'no') {
  $cotizaciones = ($mostrarDocCruce == 'Si')? '<td style="width:70px; text-align:center;"><b>DOC. CRUCE</b></td>' : '';
  $table = '<table class="defaultFont" style="width:99%;border-collapse: collapse;">
              <tr class="titulos">
                <td style="width:100px; text-align:center;"><b>SUCURSAL</b></td>
                <td style="width:100px; text-align:center;"><b>FECHA</b></td>
                <td style="width:70px; text-align:center;"><b>FECHA VENCIMIENTO</b></td>
                <td style="width:70px; text-align:center;"><b>N. FACTURA</b></td>
                '.$cotizaciones.'
                '.$titulosTipoDoc.'
                <td style="width:70px; text-align:center;"><b>CENTRO COSTOS</b></td>
                <td style="width:80px; text-align:center;"><b>SUBTOTAL</b></td>
                <td style="width:80px; text-align:center;"><b>DESCUENTO</b></td>
                <td style="width:80px; text-align:center;"><b>IMPUESTO</b></td>
                <td style="width:80px; text-align:center;"><b>RETE. FUENTE</b></td>
                <td style="width:80px; text-align:center;"><b>RETE. ICA</b></td>
                <td style="width:80px; text-align:center;"><b>RETE. IVA</b></td>
                '.(($detalla_utilidad == 'Si')? '<td style="width:80px; text-align:center;"><b>UTILIDAD</b></td>' : '' ).'
                <td style="width:80px; text-align:center;"><b>TOTAL</b></td>
                '.(($IMPRIME_XLS == 'true')? '<td style="width:70px; text-align:center;"><b>OBSERVACIONES GENERALES</b></td>' : '').
                (($facturacion_electronica == 'Si' || $IMPRIME_XLS == 'true')? $columnasFE : '') .
              '</tr>
              '.$bodyTable.'
              <tr class="total">
                <td style="text-align:center;" colspan="'.$colspanTotal.'">TOTAL VENTAS</td>
                <td style="text-align:right;">'.validar_numero_formato($acumuladoSubtotal,$IMPRIME_XLS).'</td>
                <td style="text-align:right;">'.validar_numero_formato($acumuladoDescuento,$IMPRIME_XLS).'</td>
                <td style="text-align:right;">'.validar_numero_formato($acumuladoIva,$IMPRIME_XLS).'</td>
                <td style="text-align:right;">'.validar_numero_formato($acumuladoReteFuente,$IMPRIME_XLS).'</td>
                <td style="text-align:right;">'.validar_numero_formato($acumuladoReteIca,$IMPRIME_XLS).'</td>
                <td style="text-align:right;">'.validar_numero_formato($acumuladoReteIva,$IMPRIME_XLS).'</td>
                '.(($detalla_utilidad=='Si')? '<td style="text-align:right;">'.validar_numero_formato($acumuladoTotalUtilidad,$IMPRIME_XLS).'</td>' : '').'
                <td style="text-align:right;">'.validar_numero_formato($acumuladoTotal,$IMPRIME_XLS).'</td>'.
                (($facturacion_electronica == 'Si' || $IMPRIME_XLS == 'true' && $facturacion_electronica != 'No')? '<td></td><td></td><td></td><td></td>' : '' ) .
                (($IMPRIME_XLS == 'true')? '<td></td>' : '') .'
              </tr>
            </table>';
}
//SI SE DETALLA POR ITEM
else{
  // SI ES DETALLADO POR EXCEL
  if ($IMPRIME_XLS == 'true') {
    $table = '<table class="defaultFont" style="width:100%;border-collapse: collapse;border:1px solid #999;border-top:none;border-bottom:none;">
                <tr class="titulos">
                  <td style="width:100px; text-align:center;"><b>SUCURSAL</b></td>
                  <td style="width:70px; text-align:center;"><b>FECHA</b></td>
                  <td style="width:70px; text-align:center;"><b>FECHA VENCIMIENTO</b></td>
                  <td style="width:70px; text-align:center;"><b>N. FACTURA</b></td>
                  <td style="width:200px; padding-left: 10px;"><b>NIT</b></td>
                  <td style="width:200px; padding-left: 10px;"><b>CLIENTE</b></td>
                  <td style="width:200px; padding-left: 10px;"><b>VENDEDOR</b></td>
                  <td style="width:100px; padding-left: 10px;"><b>SUCURSAL CLIENTE</b></td>
                  <td><b>CODIGO CENTRO COSTO</b></td>
                  <td><b>CENTRO COSTO</b></td>
                  <td style="width:80px; text-align:center;"><b>SUBTOTAL</b></td>
                  <td style="width:80px; text-align:center;"><b>DESCUENTO</b></td>
                  <td style="width:80px; text-align:center;"><b>IMPUESTO</b></td>
                  <td style="width:80px; text-align:center;"><b>RETE. FUENTE</b></td>
                  <td style="width:80px; text-align:center;"><b>RETE. ICA</b></td>
                  <td style="width:80px; text-align:center;"><b>RETE. IVA</b></td>
                  '.(($detalla_utilidad=='Si')? '<td style="width:80px; text-align:center;"><b>UTILIDAD</b></td>' : '' ).'
                  <td style="width:80px; text-align:right;"><b>TOTAL</b></td>
                  <td style="width:80px; text-align:center;"><b>OBSERVACIONES GENERALaES</b></td>
                  <td style="width:95px;">&nbsp;&nbsp;<b>CODIGO</b></td>
                  <td style="width:340px;"><b>NOMBRE</b></td>
                  <td style="width:80px;"><b>DOC. CRUCE</b></td>
                  <td style="width:80px;"><b>UNIDAD</b></td>
                  <td style="text-align:center;width:70px;"><b>CANTIDAD</b></td>
                  <td style="text-align:center;width:120px;"><b>COSTO UNITARIO</b></td>
                  <td style="text-align:center;width:70px;"><b>DESCUENTO</b></td>
                  <td style="text-align:center;width:90px;"><b>SUBTOTAL</b></td>
                  <td style="text-align:center;width:90px;"><b>IVA</b></td>
                  '.(($detalla_utilidad=='Si')? '<td style="text-align:right;width:90px;"><b>UTILIDAD</b></td>' : '' ).'
                  <td style="text-align:right;width:120px;"><b>TOTAL&nbsp;&nbsp;</b></td>
                  <td style="width:80px; text-align:center;"><b>OBSERVACIONES ITEM</b></td>
                  ' . $columnasFE . '
                </tr>
                '.$bodyTable.'
                <tr class="total" style=" border:1px solid #999;border-top:none;" >
                  <td style="width: 95px;" colspan="22"><b>&nbsp;&nbsp;TOTALES</b></td>
                  <td style="width: 70px;text-align:right;"><b>'.$acumuladoCantidad.'</b></td>
                  <td style="width: 120px;text-align:right;"><b>'.validar_numero_formato($acumuladoCosto,$IMPRIME_XLS).'</b></td>
                  <td style="width: 70px;"><div style="width:50px;height:12px"></div></td>
                  <td style="width: 90px;text-align:right;">'.validar_numero_formato($acumuladoIva,$IMPRIME_XLS).'</td>
                  '.(($detalla_utilidad=='Si')? '<td style="width: 90px;text-align:right;">'.validar_numero_formato($acumuladoUtilidad,$IMPRIME_XLS).'</td>' : '').'
                  <td style="width: 130px;text-align:right;"><b>'.validar_numero_formato($acumuladoTotalItems,$IMPRIME_XLS).'&nbsp;&nbsp;</b></td>
                </tr>
              </table>';
  }
  //SI ES DETALLADO POR PDF
  else if($IMPRIME_PDF == 'true'){
    $table .= (($IMPRIME_PDF == 'true')? $bodyTable : '');
  }
  // SI NO ES DETALLADO POR EXCEL NI PDF
  else{
    if($noAnexar != true){
      $table .=  '<table class="defaultFont" style="width:99%;border-collapse: collapse;">
                    <tr class="titulos">
                      <td style="width:100px; text-align:center;"><b>SUCURSAL</b></td>
                      <td style="width:100px; text-align:center;"><b>FECHA</b></td>
                      <td style="width:70px; text-align:center;"><b>FECHA VENCIMIENTO</b></td>
                      <td style="width:70px; text-align:center;"><b>N. FACTURA</b></td>
                      '.$titulosTipoDoc.'
                      <td style="width:70px; text-align:center;"><b>CENTRO COSTOS</b></td>
                      <td style="width:80px; text-align:center;"><b>SUBTOTAL</b></td>
                      <td style="width:80px; text-align:center;"><b>DESCUENTO</b></td>
                      <td style="width:80px; text-align:center;"><b>IMPUESTO</b></td>
                      <td style="width:80px; text-align:center;"><b>RETE. FUENTE</b></td>
                      <td style="width:80px; text-align:center;"><b>RETE. ICA</b></td>
                      <td style="width:80px; text-align:center;"><b>RETE. IVA</b></td>
                      '.(($detalla_utilidad == 'Si')? '<td style="width:80px; text-align:center;"><b>UTILIDAD</b></td>' : '' ).'
                      <td style="width:80px; text-align:center;"><b>TOTAL</b></td>
                      '.(($IMPRIME_XLS == 'true')? '<td style="width:70px; text-align:center;"><b>OBSERVACIONES GENERALES</b></td>' : '').
                      $columnasFE .
                      (($IMPRIME_PDF == 'true')? $bodyTable : '') . '
                    </tr>
                    <tr class="total">
                      <td style="text-align:center;"  colspan="'.$colspanTotal.'>TOTAL VENTAS</td>
                      <td style="text-align:right;">'.validar_numero_formato($acumuladoSubtotal,$IMPRIME_XLS).'</td>
                      <td style="text-align:right;">'.validar_numero_formato($acumuladoDescuento,$IMPRIME_XLS).'</td>
                      <td style="text-align:right;">'.validar_numero_formato($acumuladoIva,$IMPRIME_XLS).'</td>
                      <td style="text-align:right;">'.validar_numero_formato($acumuladoReteFuente,$IMPRIME_XLS).'</td>
                      <td style="text-align:right;">'.validar_numero_formato($acumuladoReteIca,$IMPRIME_XLS).'</td>
                      <td style="text-align:right;">'.validar_numero_formato($acumuladoReteIva,$IMPRIME_XLS).'</td>
                      '.(($detalla_utilidad=='Si')? '<td style="text-align:right;">'.validar_numero_formato($acumuladoTotalUtilidad,$IMPRIME_XLS).'</td>' : '').'
                      <td style="text-align:right;">'.validar_numero_formato($acumuladoTotal,$IMPRIME_XLS).'</td>
                    </tr>
                  </table>';
    }
    $table .= (($IMPRIME_PDF != 'true')? $bodyTable : '');
  }
}

// SI SE DETALLA POR DOCUMENTO
if ($detallado_documentos == 'devolucion') {
    $bodyTable          = '';
    $acumuladoSubTotal  = 0;
    $acumuladoDescuento = 0;
    $acumuladoIva       = 0;
    $acumuladoTotal     = 0;

    // RECORRER LAS DEVOLUCIONES
    foreach ($arrayDevoluciones as $id_devolucion => $arrayResul) {

        $totalDev = ($arrayResul['subtotal'] - $arrayResul['descuento']) + $arrayResul['iva'];
        $acumuladoSubTotal  += $arrayResul['subtotal'];
        $acumuladoDescuento += $arrayResul['descuento'];
        $acumuladoIva       += $arrayResul['iva'];
        $acumuladoTotal     += $totalDev;

        if ($detallado_items=='si') {

            if ($IMPRIME_XLS<>'true') {
                $bodyTable .= "<table class='defaultFont' style='width:99%;border-collapse: collapse;'>
                                <tr class='titulos'>
                                    <td style='padding-left:10px;width:100px;'>SUCURSAL</td>
                                    <td style='padding-left:10px;width:120px;'>BODEGA</td>
                                    <td style='padding-left:10px;width:70px;'>CODIGO CENTRO COSTO</td>
                                    <td style='padding-left:10px;width:70px;'>CENTRO COSTO</td>
                                    <td style='padding-left:10px;width:70px;'>FECHA</td>
                                    <td style='padding-left:10px;width:70px;'>N. FACTURA</td>
                                    <td style='padding-left:10px;width:70px;'>CONSECUTIVO</td>
                                    <td style='padding-left:10px;width:100px;'>NIT</td>
                                    <td style='padding-left:10px;width:200px;'>CLIENTE</td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'>SUBTOTAL</td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'>DESCUENTO</td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'>IVA</td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'>TOTAL</td>
                                </tr>
                                <tr>
                                    <td>$arrayResul[sucursal]</td>
                                    <td>$arrayResul[bodega]</td>
                                    <td>$arrayResul[codigo_centro_costo]</td>
                                    <td>$arrayResul[centro_costo]</td>
                                    <td>$arrayResul[fecha_registro]</td>
                                    <td>$arrayResul[numero_documento_venta]</td>
                                    <td>$arrayResul[consecutivo]</td>
                                    <td>$arrayResul[nit]</td>
                                    <td>$arrayResul[cliente]</td>
                                    <td style='text-align:right;'>".validar_numero_formato($arrayResul['subtotal'],$IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato($arrayResul['descuento'],$IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato($arrayResul['iva'],$IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato(-$totalDev,$IMPRIME_XLS)."</td>
                                </tr>
                                </table>
                                <table class='defaultFont' style='width:99%;border-collapse: collapse;'>
                                    <tr style='border-top: 1px solid #999;'>
                                        <td style='padding:5px;'><b>CODIGO</b></td>
                                        <td style='padding:5px;'><b>NOMBRE</b></td>
                                        <td style='padding:5px;'><b>UNIDAD MEDIDAD</b></td>
                                        <td style='padding:5px;text-align:right;' ><b>CANTIDAD</b></td>
                                        <td style='padding:5px;text-align:right;' ><b>COSTO UNITARIO</b></td>
                                        <td style='padding:5px;text-align:right;' ><b>SUBTOTAL </b></td>
                                        <td style='padding:5px;text-align:right;' ><b>DESCUENTO</b></td>
                                        <td style='padding:5px;text-align:right;' ><b>IVA</b></td>
                                        <td style='padding:5px;text-align:right;' ><b>TOTAL</b></td>
                                    </tr>
                                ";
            }

            $subtotalArticuloAcum  = 0;
            $descuentoArticuloAcum = 0;
            $ivaArticuloAcum       = 0;
            $totalArticuloAcum     = 0;

            // SI SE DISCRIMINAN LOS ITEMS
            foreach ($arrayItemsDevoluciones[$id_devolucion] as $key => $arrayResulItems) {

                $subtotalArticulo  = $arrayResulItems['cantidad']*$arrayResulItems['costo_unitario'];
                $descuentoArticulo = ($arrayResulItems['tipo_descuento']=='porcentaje')? ($subtotalArticulo*$arrayResulItems['descuento']) /100 : $arrayResulItems['descuento'] ;
                $ivaArticulo       = ($subtotalArticulo-$descuentoArticulo)*$arrayResulItems['valor_impuesto']/100;
                $totalArticulo     = ($subtotalArticulo-$descuentoArticulo)+$ivaArticulo;

                $subtotalArticuloAcum  += $subtotalArticulo;
                $descuentoArticuloAcum += $descuentoArticulo;
                $ivaArticuloAcum       += $ivaArticulo;
                $totalArticuloAcum     += $totalArticulo;

                // SI SE IMPRIME EN EXCEL EL FORMATO CAMBIA
                if ($IMPRIME_XLS=='true') {
                    $bodyTable .= "<tr>
                                    <td style='text-align:center;'>$arrayResul[sucursal]</td>
                                    <td style='text-align:center;'>$arrayResul[fecha_registro]</td>
                                    <td style='text-align:center;'>$arrayResul[consecutivo]</td>
                                    <td style='text-align:center;'>$arrayResul[numero_documento_venta]</td>
                                    <td style='text-align:center;'>$arrayResul[nit]</td>
                                    <td style='text-align:left;'>$arrayResul[cliente]</td>
                                    <td style='text-align:left;'>$arrayResul[bodega]</td>
                                    <td style='text-align:left;'>$arrayResul[sucursal_cliente]</td>
                                    <td style='text-align:left;'>$arrayResul[codigo_centro_costo]</td>
                                    <td style='text-align:left;'>$arrayResul[centro_costo]</td>
                                    <td style='text-align:right;'>".validar_numero_formato(-$arrayResul['subtotal'],$IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato(-$arrayResul['descuento'],$IMPRIME_XLS)."</td>
                                    <td style='text-align:right;'>".validar_numero_formato(-$arrayResul['iva'],$IMPRIME_XLS)."</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td style='text-align:right;'>".validar_numero_formato(-$totalDev,$IMPRIME_XLS)."</td>
                                    <td>$arrayResul[observaciones]</td>
                                    <td>$arrayResulItems[codigo]</td>
                                    <td>$arrayResulItems[nombre]</td>
                                    <td>$arrayResulItems[nombre_unidad_medida] x $arrayResulItems[cantidad_unidad_medida] </td>
                                    <td>".validar_numero_formato(-$arrayResulItems['cantidad'])."</td>
                                    <td>".validar_numero_formato(-$arrayResulItems['costo_unitario'])."</td>
                                    <td>".validar_numero_formato(-$subtotalArticulo,$IMPRIME_XLS)."</td>
                                    <td>".validar_numero_formato(-$descuentoArticulo,$IMPRIME_XLS)."</td>
                                    <td>".validar_numero_formato(-$ivaArticulo,$IMPRIME_XLS)."</td>
                                    <td>".validar_numero_formato(-$totalArticulo,$IMPRIME_XLS)."</td>
                                    <td style='text-align:left;'>$arrayResulItems[observaciones]</td>
                                    <td style='text-align:left;'>$arrayResul[nombre_usuario_DE]</td>
                                    <td style='text-align:left;'>$arrayResul[cedula_usuario_DE]</td>
                                    <td style='text-align:left;'>$arrayResul[fecha_DE]</td>
                                    <td style='text-align:left;'>$arrayResul[hora_DE]</td>
                                  </tr>";
                }
                // SI NO SE IMPRIME EN EXCEL, EL FORMATO ES NORMAL
                else{
                    $bodyTable .= "<tr>
                                    <td>$arrayResulItems[codigo]</td>
                                    <td>$arrayResulItems[nombre]</td>
                                    <td>$arrayResulItems[nombre_unidad_medida] * $arrayResulItems[cantidad_unidad_medida] </td>
                                    <td>$arrayResulItems[cantidad]</td>
                                    <td>$arrayResulItems[costo_unitario]</td>
                                    <td style='padding-right:10px;text-align:right;'>".validar_numero_formato($subtotalArticulo,$IMPRIME_XLS)."</td>
                                    <td style='padding-right:10px;text-align:right;'>".validar_numero_formato($descuentoArticulo,$IMPRIME_XLS)."</td>
                                    <td style='padding-right:10px;text-align:right;'>".validar_numero_formato($ivaArticulo,$IMPRIME_XLS)."</td>
                                    <td style='padding-right:10px;text-align:right;'>".validar_numero_formato($totalArticulo,$IMPRIME_XLS)."</td>
                                  </tr>";
                }
            }

            if ($IMPRIME_XLS<>'true') {
                $bodyTable .= "<tr class='total'>
                                    <td colspan='5' style='padding-right:10px;'>TOTALES</td>
                                    <td style='padding-right:10px;text-align:right;' >".validar_numero_formato($subtotalArticuloAcum ,$IMPRIME_XLS)."</td>
                                    <td style='padding-right:10px;text-align:right;' >".validar_numero_formato($descuentoArticuloAcum,$IMPRIME_XLS)."</td>
                                    <td style='padding-right:10px;text-align:right;' >".validar_numero_formato($ivaArticuloAcum ,$IMPRIME_XLS)."</td>
                                    <td style='padding-right:10px;text-align:right;' >".validar_numero_formato($totalArticuloAcum,$IMPRIME_XLS)."</td>
                                </tr>
                            </table>&nbsp;";
            }

        }
        // SI NO ES DETALLADO POR ITEMS
        else{
          $bodyTable .=  "<tr>
                            <td style='text-align:center;'>$arrayResul[sucursal]</td>
                            <td style='text-align:center;'>$arrayResul[fecha_registro]</td>
                            <td style='text-align:center;'>$arrayResul[consecutivo]</td>
                            <td style='text-align:center;'>$arrayResul[numero_documento_venta]</td>
                            <td style='text-align:center;'>$arrayResul[nit]</td>
                            <td style='text-align:center;'>$arrayResul[cliente]</td>
                            <td style='text-align:center;'>$arrayResul[bodega]</td>
                            ".(($detallado_items == 'no')? "<td></td>" : "")."
                            <td style='text-align:center;'>$arrayResul[codigo_centro_costo]</td>
                            <td style='text-align:center;'>$arrayResul[centro_costo]</td>
                            <td style='text-align:right;'>".validar_numero_formato(-$arrayResul['subtotal'],$IMPRIME_XLS)."</td>
                            <td style='text-align:right;'>".validar_numero_formato(-$arrayResul['descuento'],$IMPRIME_XLS)."</td>
                            <td style='text-align:right;'>".validar_numero_formato(-$arrayResul['iva'],$IMPRIME_XLS)."</td>
                            ".(($IMPRIME_XLS == 'true')? "<td></td><td></td><td></td>" : "") .
                            "<td style='text-align:right;'>".validar_numero_formato(-$totalDev,$IMPRIME_XLS)."</td>".
                            (($IMPRIME_XLS == 'true')? "<td>$arrayResul[observaciones]</td>
                                                        <td style='text-align:left;'>$arrayResul[nombre_usuario_DE]</td>
                                                        <td style='text-align:left;'>$arrayResul[cedula_usuario_DE]</td>
                                                        <td style='text-align:left;'>$arrayResul[fecha_DE]</td>
                                                        <td style='text-align:left;'>$arrayResul[hora_DE]</td>" : "") .
                          "</tr>";
        }

    }

    if ($detallado_items=='si') {
        if ($IMPRIME_XLS=='true') {
            $table .= "<table style='width:99%;'>
                            <tr><td>&nbsp;</td></tr>
                            <tr class='total'><td style='text-align: center;''>DEVOLUCIONES</td></tr>
                        </table>
                        <table class='defaultFont' style='width:99%;border-collapse: collapse;'>
                                <tr class='titulos'>
                                    <td style='padding-left:10px;text-align:center;width:100px;'>SUCURSAL</td>
                                    <td style='padding-left:10px;text-align:center;width:70px;'>FECHA</td>
                                    <td style='padding-left:10px;text-align:center;width:70px;'>CONSECUTIVO</td>
                                    <td style='padding-left:10px;text-align:center;width:70px;'>N. FACTURA</td>
                                    <td style='padding-left:10px;text-align:center;width:100px;'>NIT</td>
                                    <td style='padding-left:10px;text-align:center;width:200px;'>CLIENTE</td>
                                    <td style='padding-left:10px;text-align:center;width:120px;'>BODEGA</td>
                                    ".(($IMPRIME_XLS == 'true')? "<td></td>" : "")."
                                    <td style='padding-left:10px;text-align:center;width:70px;'>CODIGO CENTRO COSTO</td>
                                    <td style='padding-left:10px;text-align:center;width:70px;'>CENTRO COSTO</td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'>SUBTOTAL</td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'>DESCUENTO</td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'>IMPUESTO</td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'></td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'></td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'></td>
                                    <td style='padding-right:10px;text-align:right;width:80px;'>TOTAL</td>
                                    <td style='padding-right:10px;width:80px;'>OBSERVACIONES GENERALES</td>
                                    <td style='padding-right:10px;'><b>CODIGO</b></td>
                                    <td style='padding-right:10px;'><b>NOMBRE</b></td>
                                    <td style='padding-right:10px;'><b>UNIDAD MEDIDAD</b></td>
                                    <td style='padding-right:10px;text-align:right;'><b>CANTIDAD</b></td>
                                    <td style='padding-right:10px;text-align:right;'><b>COSTO UNITARIO</b></td>
                                    <td style='padding-right:10px;text-align:right;'><b>SUBTOTAL </b></td>
                                    <td style='padding-right:10px;text-align:right;'><b>DESCUENTO</b></td>
                                    <td style='padding-right:10px;text-align:right;'><b>IVA</b></td>
                                    <td style='padding-right:10px;text-align:right;'><b>TOTAL</b></td>
                                    <td style='padding-right:10px;text-align:center;'><b>OBSERVACIONES ITEM</b></td>
                                    <td style='padding-right:10px;text-align:left;'><b>USUARIO D.E</b></td>
                                    <td style='padding-right:10px;text-align:left;'><b>CEDULA D.E</b></td>
                                    <td style='padding-right:10px;text-align:left;'><b>FECHA D.E</b></td>
                                    <td style='padding-right:10px;text-align:left;'><b>HORA D.E</b></td>
                                </tr>
                                $bodyTable
                                <tr class='total'>
                                  <td colspan='23'>TOTALES</td>
                                  <td style='text-align:right;'>".validar_numero_formato($acumuladoSubTotal,$IMPRIME_XLS)."</td>
                                  <td style='text-align:right;'>".validar_numero_formato($acumuladoDescuento,$IMPRIME_XLS)."</td>
                                  <td style='text-align:right;'>".validar_numero_formato($acumuladoIva,$IMPRIME_XLS)."</td>
                                  <td style='text-align:right;'>".validar_numero_formato(-$acumuladoTotal,$IMPRIME_XLS)."</td>
                                  <td colspan='5'></td>
                                </tr>";
        }
        else{
            $table .= "<table style='width:99%;'>
                            <tr><td>&nbsp;</td></tr>
                            <tr class='total'><td style='text-align: center;'>DEVOLUCIONES</td></tr>
                        </table>".$bodyTable;
        }
    }
    //SI NO ES DETALLADO POR ITEM
    else{
        $table .=  "<table style='width:99%;'>
                        <tr><td>&nbsp;</td></tr>
                        <tr class='total'><td style='text-align: center;'>DEVOLUCIONES</td></tr>
                    </table>
                    <table class='defaultFont' style='width:99%;border-collapse: collapse;'>
                      <tr class='titulos'>
                        <td style='padding-left:10px;text-align:center;width:100px;'>SUCURSAL</td>
                        <td style='padding-left:10px;text-align:center;width:70px;'>FECHA</td>
                        <td style='padding-left:10px;text-align:center;width:70px;'>CONSECUTIVO</td>
                        <td style='padding-left:10px;text-align:center;width:70px;'>N. FACTURA</td>
                        <td style='padding-left:10px;text-align:center;width:100px;'>NIT</td>
                        <td style='padding-left:10px;text-align:center;width:200px;'>CLIENTE</td>
                        <td style='padding-left:10px;text-align:center;width:120px;'>BODEGA</td>
                        ".(($detallado_items == 'no')? "<td></td>" : "")."
                        <td style='padding-left:10px;text-align:center;width:70px;'>CODIGO CENTRO COSTOOO</td>
                        <td style='padding-left:10px;text-align:center;width:70px;'>CENTRO COSTO</td>
                        <td style='padding-right:10px;text-align:right;width:80px;'>SUBTOTAL</td>
                        <td style='padding-right:10px;text-align:right;width:80px;'>DESCUENTO</td>
                        <td style='padding-right:10px;text-align:right;width:80px;'>IMPUESTO</td>
                        ".(($IMPRIME_XLS == 'true')? "<td style='padding-right:10px;text-align:right;width:80px;'></td>
                        <td style='padding-right:10px;text-align:right;width:80px;'></td>
                        <td style='padding-right:10px;text-align:right;width:80px;'></td>" : "") .
                        "<td style='padding-right:10px;text-align:right;width:80px;'>TOTAL</td>
                        ".(($IMPRIME_XLS == 'true')? "<td style='padding-right:10px;width:80px;'>OBSERVACIONES GENERALES</td>
                                                  <td style='padding-right:10px;text-align:left;'><b>USUARIO D.E</b></td>
                                                  <td style='padding-right:10px;text-align:left;'><b>CEDULA D.E</b></td>
                                                  <td style='padding-right:10px;text-align:left;'><b>FECHA D.E</b></td>
                                                  <td style='padding-right:10px;text-align:left;'><b>HORA D.E</b></td>" : "") .
                      "</tr>
                      $bodyTable
                      <tr class='total'>
                        " . (($IMPRIME_XLS == 'true')? "<td colspan='10'>TOTALES</td>" : "<td colspan='10'>TOTALES</td>") . "
                        <td style='text-align:right;'>".validar_numero_formato(-$acumuladoSubTotal,$IMPRIME_XLS)."</td>
                        <td style='text-align:right;'>".validar_numero_formato(-$acumuladoDescuento,$IMPRIME_XLS)."</td>
                        <td style='text-align:right;'>".validar_numero_formato(-$acumuladoIva,$IMPRIME_XLS)."</td>".
                        (($IMPRIME_XLS == 'true')? "<td></td><td></td><td></td>" : "") . "
                        <td style='text-align:right;'>".validar_numero_formato(-$acumuladoTotal,$IMPRIME_XLS)."</td>
                         " . (($IMPRIME_XLS == 'true')? "<td colspan='5'></td>" : "") . "
                      </tr>
                  </table>";
            $acumuladoIva = 0;
    }
}

switch ($detallado) {
  case 'items':
      $subtitulo_cabecera .= '<br>Detallado Items';
      break;
  case 'utilidad':
      $subtitulo_cabecera .= '<br>Detallado utilidad';
      break;
  case 'utilidad_item':
      $subtitulo_cabecera .= '<br>Utilidad por item';
      break;
  case 'devolucion':
      $subtitulo_cabecera .= '<br>Devoluciones';
      break;
}

?>
<style>
	.my_informe_Contenedor_Titulo_informe{
    float       :	left;
    width       :	100%;
    margin      :	0 0 10px 0;
    font-size   :	11px;
    font-family :	Verdana, Geneva, sans-serif
	}

	.my_informe_Contenedor_Titulo_informe_label{
    float       : left;
    width       : 130px;
    font-weight : bold;
	}

	.my_informe_Contenedor_Titulo_informe_detalle{
    float         :	left;
    width         :	210px;
    padding       :	0 0 0 5px;
    white-space   : nowrap;
    overflow      : hidden;
    text-overflow : ellipsis;
	}

	.my_informe_Contenedor_Titulo_informe_Empresa{
    float       : left;
    width       : 100%;
    font-size   : 16px;
    font-weight : bold;
	}

  .defaultFont, td{
    font-size : 11px;
  }

  .labelResult{
    font-weight:bold; font-size: 14px;
  }

  .labelResult2{
    font-weight:bold; font-size: 12px; width: 20%;
  }

  .labelResult3{
    font-weight:bold; font-size: 12px; text-align: right;
  }

  .titulos{
    background   : #999;
    padding-left : 10px;
    border: 1px solid #999;
  }

  .titulos td{
    height : 35px;
    color  : #FFF;
  }

  .total{
    background  : #EEE;
    font-weight : bold;
  }

  .total td{
    border-top    : 1px solid #999;
    border-bottom : 1px solid #999;
    background    : #EEE;
    padding-left  : 10px;
    height        : 25px;
    font-weight   : bold;
  }
</style>

<!-- ************************ DESARROLLO DEL INFORME *********************** -->
<!-- *********************************************************************** -->

<body >
  <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
    <div style="float:left; width:100%">
      <div style="float:left;width:100%; text-align:center;margin-bottom:15px;">
        <table align="center" style="text-align:center;" >
          <tr><td class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
          <tr><td style="font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
          <tr><td style="font-size:13px;"><b>Informe Facturas</b><br><?php echo $subtitulo_cabecera; ?></td></tr>
          <tr><td style="font-size:11px;">De <?php echo $MyInformeFiltroFechaInicio ?> Hasta <?php echo $MyInformeFiltroFechaFinal ?><br>&nbsp;</td></tr>
          <?php echo $datos_informe; ?>
        </table>
        <?php echo $table; ?>
      </div>
    </div>
  </div>
  <br>
  <?php echo $cuerpoInforme.'<script>'.$script.'</script>'; ?>
</body>
<?php
  $footer = '<div style="text-align:right;font-weight:bold;font-size:12px;">Pagina {PAGENO}/{nb}</div>';
  $texto  = ob_get_contents(); ob_end_clean();

	if(isset($TAM)){ $HOJA = $TAM; }
  else{ $HOJA = 'LETTER-L'; }

	$ORIENTACION = 'p';
	if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
	if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

	if(isset($MARGENES)){ list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
  else{ $MS=10; $MD=10; $MI=10; $ML=10; }

	if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12 ; }
	if($IMPRIME_PDF == 'true'){
		include("../../../../misc/MPDF54/mpdf.php");
		$mpdf = new mPDF(
            					'utf-8',  // mode - default ''
            					$HOJA,		// format - A4, for example, default ''
            					12,				// font size - default 0
            					'',				// default font family
            					$MI,			// margin_left
            					$MD,			// margin right
            					$MS,			// margin top
            					$ML,			// margin bottom
            					10,				// margin header
            					10,				// margin footer
            					'L'	      // L - landscape, P - portrait
				            );
    $mpdf-> debug = true;
		$mpdf->SetAutoPageBreak(TRUE, 15);
		$mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
		$mpdf->SetDisplayMode ( 'fullpage' );
		$mpdf->SetHeader("");
    $mpdf->SetHtmlFooter($footer);
		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA=='true'){ $mpdf->Output("facturas_de_venta_".(date('Y-m-d')).".pdf",'D'); }else{	$mpdf->Output("facturas_de_venta_".(date('Y-m-d')).".pdf",'I'); }
		exit;
	}
    else{ echo $texto; }
?>
