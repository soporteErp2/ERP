<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');
  ob_start();

  if($IMPRIME_XLS == 'true'){
    header('Content-type: application/vnd.ms-excel; ');
    header("Content-Disposition: attachment; filename=informe_remisiones_".date("Y_m_d").".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
  }

  $arraytercerosJSON   = json_decode($arraytercerosJSON);
  $arrayVendedoresJSON = json_decode($arrayVendedoresJSON);
  $arrayCcosJSON       = json_decode($arrayCcosJSON);
  $id_empresa          = $_SESSION['EMPRESA'];
  $desde               = $MyInformeFiltroFechaInicio;
  $hasta               = (isset($MyInformeFiltroFechaFinal))? $MyInformeFiltroFechaFinal : date("Y-m-d") ;
  $whereSucursal       = '';
  $subtitulo_cabecera  = '';

  $mystring = $_SERVER['SERVER_NAME'];
  $findme   = 'plataforma';
  $findme1  = 'localhost';

  $pos  = strpos($mystring, $findme);   //PRODUCCION
  $pos1 = strpos($mystring, $findme1);  //LOCAL

  //--------------------------FILTRO POR FECHAS-------------------------------//
  if(isset($MyInformeFiltroFechaFinal) && $MyInformeFiltroFechaFinal != ''){
    $whereFechas = " AND VR.fecha_inicio BETWEEN '".$MyInformeFiltroFechaInicio."' AND '".$MyInformeFiltroFechaFinal."'";
    $datos_informe = 'Desde '.$MyInformeFiltroFechaInicio.' Hasta '.$MyInformeFiltroFechaFinal;
  }
  else{
    $MyInformeFiltroFechaFinal = date("Y-m-d");
    $datos_informe = 'Corte ha '.$MyInformeFiltroFechaFinal;
    $script =  'localStorage.MyInformeFiltroFechaInicioRemisionesVenta  = "";
                localStorage.MyInformeFiltroFechaFinalRemisionesVenta   = "";
                localStorage.sucursal_remisiones = ""
                localStorage.estado_remision = "";
                arraytercerosRV.length              = 0;
                tercerosConfiguradosRV.length       = 0;
                arrayvendedoresRV.length            = 0;
                vendedoresConfiguradosRV.length     = 0;
                checkBoxMostrarArticulos     = ""; ';
  }

  //--------------------------FILTRO POR TERCEROS-----------------------------//
  if(!empty($arraytercerosJSON)){
    foreach($arraytercerosJSON as $indice => $id_tercero){
      $whereClientes .= ($whereClientes == '')? ' VR.id_cliente = '.$id_tercero : ' OR VR.id_cliente = '.$id_tercero;
    }
    $whereClientes   = " AND (".$whereClientes.")";
    $groupBy         = ',VR.id_cliente';
  }

  //-------------------------FILTRO POR VENDEDORES----------------------------//
  if(!empty($arrayVendedoresJSON)){
    foreach($arrayVendedoresJSON as $indice => $id_tercero) {
      $whereVendedores .= ($whereVendedores == '')? ' VR.id_vendedor = '.$id_tercero : ' OR VR.id_vendedor = '.$id_tercero;
    }
    $whereVendedores  = " AND (".$whereVendedores.")";
    $groupBy         .= ',VR.id_vendedor';
  }

  //---------------------FILTRO POR CENTROS DE COSTO--------------------------//
  if(!empty($arrayCcosJSON)){
    foreach($arrayCcosJSON as $indice => $id_ccos){
      $whereidCentroCostos .= ($whereidCentroCostos == '')? ' VR.id_centro_costo='.$id_ccos : ' OR VR.id_centro_costo='.$id_ccos;
    }
    $whereCentroCostos  = " AND (".$whereidCentroCostos.")";
    $groupBy            = ($groupBy <> '')? ',VR.id_centro_costo' : 'VR.id_centro_costo';
  }

  //--------------------------FILTRO POR SUCURSAL-----------------------------//
  if($sucursal != '' && $sucursal != 'global'){
    $whereSucursal = ' AND VR.id_sucursal = '.$sucursal;
    //CONSULTAR EL NOMBRE DE LA SUCURSAL
    $sql = "SELECT nombre FROM empresas_sucursales WHERE  id_empresa = $id_empresa AND id = ".$sucursal;
    $query = mysql_query($sql,$link);
    $subtitulo_cabecera .= '<b>Sucursal</b> ' . mysql_result($query,0,'nombre') . '';
  }

  //---------------------------FILTRO POR ESTADO------------------------------//
  if($estado_remision == '' || !isset($estado_remision) || $estado_remision == 'todas'){
    $whereEstado = ' AND (VR.estado = 1 OR VR.estado = 2 OR VR.estado = 3)';
    $subtitulo_cabecera = ($subtitulo_cabecera != '')? $subtitulo_cabecera . '<br>Todas las Remisiones' : 'Todas las Remisiones';
  }
  else if($estado_remision == 'pendientes'){
    $whereEstado = ' AND (VR.estado = 1 OR VR.pendientes_facturar > 0) AND VR.estado <> 3';
    $subtitulo_cabecera = ($subtitulo_cabecera != '')? $subtitulo_cabecera . '<br>Remisiones Pendientes por Facturar' : 'Remisiones Pendientes por Facturar';
  }
  else if($estado_remision == 'facturadas'){
    $whereEstado = ' AND (VR.estado = 2 AND VR.pendientes_facturar = 0) AND VR.estado <> 3';
    $subtitulo_cabecera = ($subtitulo_cabecera != '')? $subtitulo_cabecera . '<br>Remisiones Facturadas' : 'Remisiones Facturadas';
  }
  else if($estado_remision == 'anuladas'){
    $whereEstado = ' AND (VR.estado = 3)';
    $subtitulo_cabecera = ($subtitulo_cabecera != '')? $subtitulo_cabecera . '<br>Remisiones Anuladas' : 'Remisiones Anuladas';
  }

  //--------------------------FILTRO POR DETALLADO----------------------------//
  if($detallado_items == 'si'){
    $subtitulo_cabecera = ($subtitulo_cabecera != '')? $subtitulo_cabecera . '<br>Detallado Items' : 'Detallado Items';
  }

  //--------------------------CONSULTA PRINCIPAL------------------------------//
  $sql = "SELECT
            VR.id,
            VR.fecha_inicio,
            VR.fecha_finalizacion,
            VR.consecutivo,
            VR.pendientes_facturar,
            VR.nit,
            VR.cliente,
            VR.nombre_vendedor,
            VR.consecutivo_siip,
            VR.centro_costo,
            VR.bodega,
            VR.sucursal,
            VR.estado,
            VR.observacion,
            VF.numero_factura_completo as consecutivo_cruce,
            VF.fecha_inicio as fecha_cruce
          FROM
            ventas_remisiones AS VR
            LEFT JOIN ventas_facturas_inventario AS VFI ON VR.id = VFI.id_consecutivo_referencia
            LEFT JOIN ventas_facturas AS VF ON VFI.id_factura_venta = VF.id
          WHERE
          VR.activo = 1
          AND VFI.nombre_consecutivo_referencia = 'Remision'
          AND VR.id_empresa = $id_empresa
            $whereEstado
            $whereSucursal
            $whereClientes
            $whereVendedores
            $whereFechas
            $whereCentroCostos
          GROUP BY VR.id, VF.id
          ORDER BY
            VR.cliente ASC "; 
  $query = mysql_query($sql,$link);

  //CONSULTAR LAS REMISIONES Y GUARDAR EN UN ARRAY
  while($row = mysql_fetch_array($query)){
    $whereIdRemisiones=($whereIdRemisiones != '')? $whereIdRemisiones . ' OR id_remision_venta=' . $row['id'] : 'id_remision_venta=' . $row['id'];
    $arrayRemisionesVentas[$row['id']] = array('fecha_inicio'        => $row['fecha_inicio'],
                                               'fecha_finalizacion'  => $row['fecha_finalizacion'],
                                               'consecutivo'         => $row['consecutivo'],
                                               'pendientes_facturar' => ($row['estado'] == 3)? 0 : $row['pendientes_facturar'],
                                               'valor_restante'      => $row['valor_restante'],
                                               'nit_cliente'         => $row['nit'],
                                               'cliente'             => $row['cliente'],
                                               'nombre_vendedor'     => $row['nombre_vendedor'],
                                               'consecutivo_siip'    => $row['consecutivo_siip'],
                                               'centro_costo'        => $row['centro_costo'],
                                               'bodega'              => $row['bodega'],
                                               'sucursal'            => $row['sucursal'],
                                               'estado'              => $row['estado'],
                                               'documento_cruce'     => ($arrayRemisionesVentas[$row['id']]['consecutivo_cruce'])?
                                                                          $arrayRemisionesVentas[$row['id']]['consecutivo_cruce']." - ".$row['consecutivo_cruce'] :
                                                                        $row['consecutivo_cruce'],
                                               'fecha_cruce'         => $row['fecha_cruce'],
                                               'observacion'         => $row['observacion']
                                              );
  }

  //SI HAY REMISIONES QUE CONSULTAR
  if($whereIdRemisiones != ''){
    $sql = "SELECT
              id,
              id_remision_venta,
              codigo,
              nombre_unidad_medida,
              cantidad_unidad_medida,
              nombre,
              cantidad,
              saldo_cantidad,
              costo_unitario,
              tipo_descuento,
              descuento,
              valor_impuesto
            FROM
              ventas_remisiones_inventario
            WHERE
              activo = 1
            AND
              ($whereIdRemisiones)";
    $query = mysql_query($sql,$link);

    while($row = mysql_fetch_array($query)){

      // SI ESTADO ES 3 DEJAR VACIOS LOS CAMPOS ACUMULADOS
      if($arrayRemisionesVentas[$row['id_remision_venta']]['estado'] == 3){
        $row['saldo_cantidad'] = 0;
        $row['descuento']      = 0;
        $row['costo_unitario'] = 0;
        $row['valor_impuesto'] = 0;
      }

      $arrayItemsRemision[$row['id_remision_venta']][$row['id']] = array('id_remision_venta'      => $row['id_remision_venta'],
                                                                         'codigo'                 => $row['codigo'],
                                                                         'nombre_unidad_medida'   => $row['nombre_unidad_medida'],
                                                                         'cantidad_unidad_medida' => $row['cantidad_unidad_medida'],
                                                                         'nombre'                 => $row['nombre'],
                                                                         'cantidad'               => $row['cantidad'],
                                                                         'saldo_cantidad'         => $row['saldo_cantidad'],
                                                                         'costo_unitario'         => $row['costo_unitario'],
                                                                         'tipo_descuento'         => $row['tipo_descuento'],
                                                                         'descuento'              => $row['descuento'],
                                                                         'valor_impuesto'         => $row['valor_impuesto'],
                                                                        );

      $total_articulo = 0;
      $iva            = 0;
      $subtotal       = 0;

      //SI EL ARTICULO ESTA PENDIENTE POR FACTURAR
      if ($row['saldo_cantidad'] > 0){
        //SI EL DESCUENTO DEL ARTICULO FUE POR PORCENTAJE
        if($row['descuento'] > 0){
          $total_articulo = ($row['tipo_descuento'] == 'porcentaje')? ($row['saldo_cantidad'] * $row['costo_unitario']) - ((($row['saldo_cantidad'] * $row['costo_unitario']) * $row['descuento']) / 100)
                                                                    : ($row['saldo_cantidad'] * $row['costo_unitario']) - $row['descuento'];
        }
        //SI EL DESCUENTO DEL ARTICULO FUE EN VALOR
        else{
          $total_articulo=$row['saldo_cantidad']*$row['costo_unitario'];
        }
      }

      //SI EL DESCUENTO DEL ARTICULO FUE POR PORCENTAJE
      if($row['descuento'] > 0){
          $total_articulo_total = ($row['tipo_descuento'] == 'porcentaje')? ($row['cantidad'] * $row['costo_unitario']) - ((($row['cantidad'] * $row['costo_unitario']) * $row['descuento']) / 100)
                                                                          : ($row['cantidad'] * $row['costo_unitario']) - $row['descuento'];
      }
      //SI EL DESCUENTO DEL ARTICULO FUE EN VALOR
      else{
        $total_articulo_total=$row['cantidad']*$row['costo_unitario'];
      }

      //CALCULAR LOS VALORES ACUMULADOS
      $iva=($total_articulo_total*$row['valor_impuesto'])/100;
      $subtotal=$total_articulo_total;

      $iva_saldo_restante=($total_articulo*$row['valor_impuesto'])/100;
      $total_articulo+=($row['valor_impuesto']>0)? $iva_saldo_restante : 0 ;


      $arraySubTotalRemision[$row['id_remision_venta']] += $subtotal;
      $arrayIvaRemision[$row['id_remision_venta']]      += $iva;
      $arrayTotalesRemision[$row['id_remision_venta']]  += $total_articulo;
    }
  }
  $remision = 0;

  //VARIABLES ACUMULADAS POR CADA ITEM
  $acumuladoCantidad  = 0;
  $acumuladoPendiente = 0;
  $acumuladoCosto     = 0;
  $acumuladoDescuento = 0;
  $acumuladoIva       = 0;
  $acumuladoTotal     = 0;

  //ARMAR EL CUERPO DEL INFORME
  foreach($arrayRemisionesVentas as $id_remision => $arrayResul){
    $styleDocCancelado = ($arrayResul['estado'] == 3)? 'color:#F00A0A;font-style: italic;font-weight:bold;' : '';

    if($pos !== false || $pos1 !== false){ //SOLO SI ES PLATAFORMA O ES LOCALHOST MUESTRA EL CONSECUTIVO SIIP
      $consecutivo_siip = '<td style="text-align:center;width:75px;'.$styleDocCancelado.'">'.$arrayResul['consecutivo_siip'].'</td>';
      $title = '<td style="width:75px;text-align:center;"><b>CONS. SIIP</b></td>';
      $width = '100px';
    }
    else{
      $consecutivo_siip = '';
      $title = '';
      $width = '175px';
    }

    //SI SE VAN A DISCRIMIAR LOS ARTCULOS
    if($detallado_items == 'si'){
      if($cont > 0){
    $headerTable .=  '<tr class="titulos">
                      <td style="text-align:center;">&nbsp;<b>SUCURSAL</b></td>
                      <td style="text-align:center;"><b>FECHA I.</b></td>
                      <td style="text-align:center;"><b>CONS. ERP</b></td>
                      '.$title.'
                      <td style="text-align:center;"><b>CONS. CRUCE</b></td>
                      <td style="text-align:center;"><b>FECHA CRUCE</b></td>
                      <td style="text-align:center;"><b>CENTRO COSTO</b></td>
                      <td style="text-align:center;"><b>BODEGA</b></td>
                      <td style="text-align:center;"><b>SUBTOTAL</b></td>
                      <td style="text-align:center;"><b>IVA</b></td>
                      <td style="text-align:center;"><b>UND.<br>PENDIENTE(S)</b></td>
                      <td style="text-align:center;"><b>VAL. PENDIENTE</b></td>
                      <td style="text-align:center;"><b>NIT</b></td>
                      <td style="text-align:center;"><b>CLIENTE</b></td>
                      <td style="text-align:center;"><b>VENDEDOR</b></td>
                    </tr>';
      }
      else{
        $cont++;
      }

      $bodyTable .=   $headerTable.'
                      <tr style="border:1px solid #999;border-top:none;border-bottom:none;">
                        <td style="text-align:center;'.$styleDocCancelado.'">&nbsp;'.$arrayResul['sucursal'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['fecha_inicio'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['consecutivo'].'</td>
                        '.$consecutivo_siip.'
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['documento_cruce'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['fecha_cruce'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['centro_costo'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['bodega'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.validar_numero_formato($arraySubTotalRemision[$id_remision],$IMPRIME_XLS).'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.validar_numero_formato($arrayIvaRemision[$id_remision],$IMPRIME_XLS).'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.($arrayResul['pendientes_facturar'] * 1).'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.validar_numero_formato($arrayTotalesRemision[$id_remision],$IMPRIME_XLS).'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['nit_cliente'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['nombre_vendedor'].'</td>
                      </tr>';

      //MOSTRAR LOS ARTICULOS QUE PERTENECEN A LA REMISION
      foreach ($arrayItemsRemision[$id_remision] as $id_registro => $arrayResul2) {
        $simbolo_descuento = ($arrayResul2['tipo_descuento'] == 'porcentaje')? ' %' : ' $';

        //SI TIENE DESCUENTO
        if($arrayResul2['descuento'] > 0){
          $totalArticulo = ($arrayResul2['tipo_descuento'] == 'porcentaje')? ($arrayResul2['cantidad']*$arrayResul2['costo_unitario'])-((($arrayResul2['cantidad']*$arrayResul2['costo_unitario'])*$arrayResul2['descuento'])/100)
                                                                           : ($arrayResul2['cantidad']*$arrayResul2['costo_unitario'])-$arrayResul2['descuento'];
        }
        else{
          $totalArticulo=$arrayResul2['cantidad']*$arrayResul2['costo_unitario'];
        }

        $ivaArticulo = ($arrayResul2['valor_impuesto'] > 0)? ($totalArticulo * $arrayResul2['valor_impuesto']) / 100 : 0;
        $totalArticulo += $ivaArticulo;

        $bodyTableDetail .=  '<tr style="height:25px;border:1px solid #999;border-top:none;border-bottom:none;">
                                <td colspan="2" style="text-align:left">&nbsp;&nbsp;'.$arrayResul2['codigo'].'</td>
                                <td colspan="5" style="text-align:left">'.$arrayResul2['nombre'].'</td>
                                <td colspan="2" style="text-align:left">'.$arrayResul2['nombre_unidad_medida'].' x '.$arrayResul2['cantidad_unidad_medida'].'</td>
                                <td style="text-align:right;">'.($arrayResul2['cantidad'] * 1).'</td>
                                <td style="text-align:right;">'.($arrayResul2['saldo_cantidad'] * 1).'</td>
                                <td style="text-align:right;">'.validar_numero_formato($arrayResul2['costo_unitario'],$IMPRIME_XLS).'</td>
                                <td style="text-align:right;">'.validar_numero_formato($arrayResul2['descuento'],$IMPRIME_XLS).' '.$simbolo_descuento.'</td>
                                <td style="text-align:right;">'.validar_numero_formato($arrayResul2['valor_impuesto'],$IMPRIME_XLS).'</td>
                                <td style="text-align:right;">'.validar_numero_formato($totalArticulo,$IMPRIME_XLS).'&nbsp;&nbsp;</td>
                              </tr>';

        //ACUMULAR LOS VALORES
        $acumuladoCantidad  += $arrayResul2['cantidad'];
        $acumuladoPendiente += $arrayResul2['saldo_cantidad'];
        $acumuladoCosto     += $arrayResul2['costo_unitario'];
        $acumuladoDescuento += $arrayResul2['descuento'];
        $acumuladoIva       += 0;
        $acumuladoTotal     += $totalArticulo;
      }

      //TOTALES DE LA REMISION DISCRIMINANDO LOS ARTICULOS
      if($remision != $id_remision){
        if($id_remision != 0){
          $bodyTableFooter .=  '<tr class="total" style=" border:1px solid #999;border-top:none;">
                                  <td colspan="9" style="border-top:1px solid #999;"><b>&nbsp;&nbsp;TOTALES</b></td>
                                  <td style="border-top:1px solid #999;text-align:right;"><b>'.$acumuladoCantidad.'</b></td>
                                  <td style="border-top:1px solid #999;text-align:right;"><b>'.$acumuladoPendiente.'</b></td>
                                  <td style="border-top:1px solid #999;text-align:right;"><b>'.validar_numero_formato($acumuladoCosto,$IMPRIME_XLS).'</b></td>
                                  <td colspan="2" style="border-top:1px solid #999;"></td>
                                  <td style="border-top:1px solid #999;text-align:right;"><b>'.validar_numero_formato($acumuladoTotal,$IMPRIME_XLS).'&nbsp;&nbsp;</b></td>
                                </tr>
                                <tr class="total" style="border:1px solid #999;border-top:none;">
                                  <td colspan="8" style="border-top:1px solid #999;width:95px;"><b>&nbsp;&nbsp;OBSERVACIONES</b></td>
                                  <td colspan="7" style="border-top:1px solid #999;width:95px;">'.$arrayResul['observacion'].'</td>
                                </tr>';

          //LIMPIAR LAS VARIABLES
          $acumuladoCantidad  = 0;
          $acumuladoPendiente = 0;
          $acumuladoCosto     = 0;
          $acumuladoDescuento = 0;
          $acumuladoIva       = 0;
          $acumuladoTotal     = 0;
        }
        $remision = $id_remision;
      }

      $bodyTable .=  '<tr style="height:30px;border:1px solid #999;border-bottom:none;">
                        <td colspan="2" style="text-align:left;">&nbsp;&nbsp;<b>Codigo</td>
                        <td colspan="5" style="text-align:left;"><b>Nombre</td>
                        <td colspan="2" style="text-align:left;"><b>Unidad</td>
                        <td style="text-align:center;"><b>Cantidad</td>
                        <td style="text-align:center;"><b>Pendientes</td>
                        <td style="text-align:center;"><b>Costo</td>
                        <td style="text-align:center;"><b>Descuento</td>
                        <td style="text-align:center;"><b>Iva(%)</td>
                        <td style="text-align:center;"><b>Total</td>
                      </tr>
                      ' . $bodyTableDetail . $bodyTableFooter;

      $bodyTable .=  '<tr><td>&nbsp;</td></tr>';
    }
    //SI NO SE VAN A DISCRIMINAR LOS ARTICULOS
    else{
      $bodyTable .=   $headerTable.'
                      <tr>
                        <td style="text-align:center;'.$styleDocCancelado.'">&nbsp;'.$arrayResul['sucursal'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['fecha_inicio'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['consecutivo'].'</td>
                        '.$consecutivo_siip.'
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['documento_cruce'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['fecha_cruce'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['centro_costo'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['bodega'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.validar_numero_formato($arraySubTotalRemision[$id_remision],$IMPRIME_XLS).'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.validar_numero_formato($arrayIvaRemision[$id_remision],$IMPRIME_XLS).'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.($arrayResul['pendientes_facturar'] * 1).'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.validar_numero_formato($arrayTotalesRemision[$id_remision],$IMPRIME_XLS).'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['nit_cliente'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['cliente'].'</td>
                        <td style="text-align:center;'.$styleDocCancelado.'">'.$arrayResul['nombre_vendedor'].'</td>
                      </tr>';
    }

    $headerTable     = "";
    $bodyTableDetail = "";
    $bodyTableFooter = "";
  }
  $bodyTable .=  '<table>
                    <tr><td>&nbsp;</td></tr>
                  </table>';

  $style = ($detallado_items == 'si')? 'style=" border:1px solid #000;border-bottom:none;"' : '';
?>
<style>
	.my_informe_Contenedor_Titulo_informe{
    float         :	left;
    width         :	100%;
    margin        :	0 0 10px 0;
    font-size     :	11px;
    font-family   :	Verdana, Geneva, sans-serif;
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
  .defaultFont{
    font-size       : 12px;
    border-collapse : collapse;
    border          : none;
    height          : 25px;
  }
  .labelResult{
    font-weight : bold;
    font-size   : 14px;
  }
  .labelResult2{
    font-weight : bold;
    font-size   : 12px;
    width       : 20%;
  }
  .labelResult3{
    font-weight:bold;
    font-size: 12px;
    text-align: right;
  }
  .titulos{
    background   : #999;
    padding-left : 10px;
  }
  .titulos td{
    height : 35px;
    color  :#FFF;
  }
  .total{
    background  : #EEE;
    font-weight : bold;
  }
  .total td{
    background    : #EEE;
    height        : 25px;
    font-weight   : bold;
  }
</style>
<!--************************* DESARROLLO DEL INFORME ************************-->
<!--*************************************************************************-->
<body>
  <div class="my_informe_Contenedor_Titulo_informe" style="float:left; width:100%">
    <div style="float:left; width:100%">
      <div style="float:left;width:100%;margin-bottom:15px;">
        <table align="center">
          <tr><td colspan="12" class="my_informe_Contenedor_Titulo_informe_Empresa" style="text-align:center;"><?php echo $_SESSION['NOMBREEMPRESA']?></td></tr>
          <tr><td colspan="12" style="text-align:center;font-size:13px;"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td></tr>
          <tr><td colspan="12" style="text-align:center;font-size:13px;"><?php echo $subtitulo_cabecera; ?></td></tr>
          <tr><td colspan="12" style="text-align:center;font-size:11px;"><?php echo $datos_informe; ?><br>&nbsp;</td></tr>
        </table>
        <table class="defaultFont" style="width:1015px;border-collapse:collapse;">
          <tr class="titulos">
            <td style=";text-align:center;">&nbsp;<b>SUCURSAL</b></td>
            <td style="text-align:center;"><b>FECHA I.</b></td>
            <td style="text-align:center;"><b>CONS. ERP</b></td>
            <?php echo $title; ?>
            <td style="text-align:center;"><b>CONS. CRUCE</b></td>
            <td style="text-align:center;"><b>FECHA CRUCE</b></td>
            <td style="text-align:center;"><b>CENTRO COSTO</b></td>
            <td style="text-align:center;"><b>BODEGA</b></td>
            <td style="text-align:center;"><b>SUBTOTAL</b></td>
            <td style="text-align:center;"><b>IVA</b></td>
            <td style="text-align:center;"><b>UND.<br>PENDIENTE(S)</b></td>
            <td style="text-align:center;"><b>VAL. PENDIENTE</b></td>
            <td style="text-align:center;"><b>NIT</b></td>
            <td style="text-align:center;"><b>CLIENTE</b></td>
            <td style="text-align:center;"><b>VENDEDOR</b></td>
          </tr>
          <?php echo $bodyTable; ?>
        </table>
        <table class="defaultFont" style="width:1015px;border-collapse:collapse;border:1px solid #999999;">
          <tr style="border:1px solid #999999;">
            <td colspan="12" style="border-right:1px solid #999999;">
              <table width="100%;">
                <tr><td>&nbsp;</td></tr>
                <tr><td colspan="12" style="text-align:center;font-size:12px;">Nota: Con el recibido y firma de este documento se aceptan y validan la totalidad de las remisiones que aqui se discriminan.</td></tr>
                <tr><td>&nbsp;</td></tr>
              </table>
            </td>
          </tr>
          <tr style="border:1px solid #999999;">
            <td colspan="6" style="border-right:1px solid #999999;">
              <table>
                <tr><td>&nbsp;</td></tr>
                <tr><td style="font-size:12px;">&nbsp;Nombre Funcionario Del Hotel</td></tr>
                <tr><td>&nbsp;</td></tr>
              </table>
            </td>
            <td colspan="6">
              <table>
                <tr><td>&nbsp;</td></tr>
                <tr><td style="font-size:12px;">&nbsp;Sello Hotel</td></tr>
                <tr><td>&nbsp;</td></tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</body>
<script>
  <?php echo $script; ?>
</script>
<?php
	$texto = ob_get_contents(); ob_end_clean();
  $documento = "Informe_Remisiones_De_Venta_" . date('Y_m_d');
	if(isset($TAM)){$HOJA = $TAM;}else{$HOJA = 'LETTER-L';}
	if(!isset($ORIENTACION)){$ORIENTACION = 'P';}
	if(!isset($PDF_GUARDA)){$PDF_GUARDA = 'false';}
	if(!isset($IMPRIME_PDF)){$IMPRIME_PDF = 'false';}
	if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES );}else{$MS=10;$MD=10;$MI=10;$ML=10;}
	if(!isset($TAMANO_ENCA)){$TAMANO_ENCA = 12 ;}
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
            					$ORIENTACION	// L - landscape, P - portrait
            				);
    $mpdf->useSubstitutions = true;
    $mpdf->packTableData = true;
		$mpdf->SetAutoPageBreak(TRUE,15);
		$mpdf->SetAuthor($_SESSION['NOMBREFUNCIONARIO'] . " // " . $_SESSION['NOMBREEMPRESA']);
		$mpdf->SetDisplayMode('fullpage');
		$mpdf->SetHeader("");
    $mpdf->SetTitle('Informe De Remisiones');
    $mpdf->SetFooter('Pagina {PAGENO}/{nb}');
		$mpdf->WriteHTML(utf8_encode($texto));
		if($PDF_GUARDA == 'true'){
      $mpdf->Output($documento.".pdf",'D');
    } else{
      $mpdf->Output($documento.".pdf",'I');
    }
		exit;
	}
  else{
    echo $texto;
  }
?>
