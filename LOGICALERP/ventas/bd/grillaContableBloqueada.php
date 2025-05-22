<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../funciones_globales/funciones_php/randomico.php");
    include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php");

    $id_empresa           = $_SESSION['EMPRESA'];
    $id_sucursal          = $_SESSION['SUCURSAL'];
    $bodyArticle          = '';
    $acumScript           = '';
    $estado               = '';
    $fechaActual          = date('Y-m-d');
    $divImagen            = '';
    $divAnticipo          = '';
    $divOrdenCompra       = '';
    $arrayTypeRetenciones = '';
?>
<script>

    var arrayTypeRetenciones  = new Array();// ARRAY QUE CONTIENE LAS RETENCIONES QUE NO SON DESCONTADAS DEL TOTAL

    //variables para calcular los valores de los costos y totales de la factura
    var subtotalAcumulado<?php echo $opcGrillaContable; ?> = 0.00
    ,   ivaAcumulado<?php echo $opcGrillaContable; ?>      = 0.00
    ,   totalAcumulado<?php echo $opcGrillaContable; ?>    = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>     = 1
    ,   id_cliente_<?php echo $opcGrillaContable; ?>       = 0;

    var objectRetenciones_<?php echo $opcGrillaContable; ?>=[];

    //Bloqueo todos los botones

    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").enable();

</script>
<?php
    include("functions_body_article.php");

    if ($opcGrillaContable == 'CotizacionVenta'){ $titulo = 'Cotizacion de Venta'; $arrayPermisos = array(6, 7, 8, 9); }
    else if ($opcGrillaContable == 'PedidoVenta'){ $titulo = 'Pedido de Venta'; $arrayPermisos = array(11, 12, 13, 14); }
    else if ($opcGrillaContable == 'RemisionesVenta'){ $arrayPermisos = array(16, 17, 18, 19); }
    else if ($opcGrillaContable == 'FacturaVenta'){ $arrayPermisos = array(21, 22, 23, 24); $acumScript .="Ext.getCmp('Btn_itemsGrupos_$opcGrillaContable').disable();"; $user_permiso_anexar    = 'Ext.getCmp("Btn_upload_'.$opcGrillaContable.'").enable();'; }

    list($permisoGuardar, $permisoEditar, $permisoEliminar, $permisoRestaurar) = $arrayPermisos;

    $user_permiso_editar    = (user_permisos($permisoEditar,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos($permisoEliminar,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos($permisoRestaurar,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';     //restaurar


    //el campo numero de factura solo esta en la tabla de ventas_facturas, si que verificamos que opcGrillaContable sea ventas facturas para pasar la cadena sql con ese campo, sino se pasa si ese campo
    $div_sucursal_cliente = '';
    if($opcGrillaContable == 'FacturaVenta'){
        $sql = "SELECT
                  id_cliente,
                  id_configuracion_resolucion,
                  prefijo,
                  numero_factura,
                  cod_cliente,
                  nit,cliente,
                  date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                  date_format(fecha_vencimiento,'%Y-%m-%d') AS fechaFin,
                  DATE_ADD(fecha_inicio, INTERVAL 24 MONTH) AS fecha_bloqueo,
                  REPLACE(observacion,'\"','') AS observacion,
                  estado,
                  nombre_vendedor,
                  prefijo,
                  cuenta_pago,
                  configuracion_cuenta_pago,
                  metodo_pago,
                  centro_costo,
                  codigo_centro_costo,
                  sucursal_cliente,
                  orden_compra,
                  response_FE,
                  UUID
                FROM
                  $tablaPrincipal
                WHERE
                  id = '$id_factura_venta'
                AND
                  activo = 1";
    }
    else if($opcGrillaContable == 'RemisionesVenta'){
        $sql = "SELECT id_cliente,
                  cod_cliente,
                  nit,
                  cliente,
                  date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                  date_format(fecha_finalizacion,'%Y-%m-%d') AS fechaFin,
                  DATE_ADD(fecha_inicio, INTERVAL 24 MONTH) AS fecha_bloqueo,
                  REPLACE(observacion,'\"','') AS observacion,
                  estado,
                  nombre_vendedor,
                  centro_costo,
                  codigo_centro_costo,
                  sucursal_cliente,
                  consecutivo
                FROM
                  $tablaPrincipal
                WHERE
                  id = '$id_factura_venta'
                AND
                  activo = 1";
    }
    else{
        $sql = "SELECT id_cliente,
                  cod_cliente,
                  nit,
                  cliente,
                  date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                  date_format(fecha_finalizacion,'%Y-%m-%d') AS fechaFin,
                  DATE_ADD(fecha_inicio, INTERVAL 24 MONTH) AS fecha_bloqueo,
                  REPLACE(observacion,'\"','') AS observacion,
                  estado,
                  nombre_vendedor,
                  consecutivo
                FROM
                  $tablaPrincipal
                WHERE
                  id = '$id_factura_venta'
                AND
                  activo = 1";
    }

    $query = mysql_query($sql,$link);

    $nit                = mysql_result($query,0,'nit');
    $cliente            = mysql_result($query,0,'cliente');
    $id_cliente         = mysql_result($query,0,'id_cliente');
    $fecha              = mysql_result($query,0,'fecha');
    $fechaFin           = mysql_result($query,0,'fechaFin');
    $fechaBloqueo       = mysql_result($query,0,'fecha_bloqueo');
    $codigo_cliente     = mysql_result($query,0,'cod_cliente');
    $estado             = mysql_result($query,0,'estado');
    $nombre_vendedor    = mysql_result($query,0,'nombre_vendedor');
    $consecutivo        = mysql_result($query,0,'consecutivo');
    $numero_factura     = mysql_result($query,0,'numero_factura');
    $prefijo            = mysql_result($query,0,'prefijo');
    $codigoCcos         = mysql_result($query,0,'codigo_centro_costo');
    $nombreCcos         = mysql_result($query,0,'centro_costo');
    $cuentaPago         = mysql_result($query,0,'cuenta_pago');
    $configuracionPago  = mysql_result($query,0,'configuracion_cuenta_pago');
    $metodo_pago        = mysql_result($query,0,'metodo_pago');
    $sucursal_cliente   = mysql_result($query,0,'sucursal_cliente');
    $orden_compra       = mysql_result($query,0,'orden_compra');
    $response_FE        = mysql_result($query,0,'response_FE');
    $UUID               = mysql_result($query,0,'UUID');
    $id_resolucion      = mysql_result($query,0,'id_configuracion_resolucion');

    if($sucursal_cliente != ''){
        $div_sucursal_cliente = '<div class="renglonTop">
                                    <div class="labelTop">Sucursal Cliente</div>
                                    <div class="campoTop"><input type="text" style="width:100%" value="'.$sucursal_cliente.'" Readonly /></div>
                                </div>';
    }

    $labelCcos = $codigoCcos.' '.$nombreCcos;

    if($orden_compra != ""){
      $divOrdenCompra =  "<div class='renglonTop'>
                            <div class='labelTop'>OC</div>
                            <div class='campoTop'><input type='text' style='width:100%' value='".$orden_compra."' Readonly /></div>
                          </div>";
    }

    $divCuentaPago = ($cuentaPago > 0)? '<div class="renglonTop">
                                            <div class="labelTop">Cuenta de cobro</div>
                                            <div class="campoTop" title="'.$cuentaPago.'"><input type="text" id="cuentas_pago" value="'.$configuracionPago.'" Readonly="Readonly" /></div>
                                        </div>' : '';

    if($prefijo != ''){ $numero_factura = $prefijo.' '.$numero_factura; }
    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .= 'id_cliente_'.$opcGrillaContable.'
                    observacion'.$opcGrillaContable.' = "'.$observacion.'";';

    $bodyArticle = cargaArticulosSave($id_factura_venta,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

    if ($opcGrillaContable=='FacturaVenta') {

      if("$estado" == "1" && "$opcGrillaContable" == "FacturaVenta"){
        $acumScript .= "Ext.getCmp('Btn_enviar_factura_electronica_$opcGrillaContable').enable();";
      } else if("$estado" == "3" && "$opcGrillaContable" == "FacturaVenta"){
        $acumScript .= "Ext.getCmp('Btn_enviar_factura_electronica_$opcGrillaContable').disable();";
      }

      if("$response_FE" == "Ejemplar recibido exitosamente pasara a verificacion" && ($UUID != "" || $UUID != null)){
        $acumScript .= "Ext.getCmp('Btn_enviar_factura_electronica_$opcGrillaContable').disable();";
      } else{
        $acumScript .= "Ext.getCmp('Btn_enviar_factura_electronica_$opcGrillaContable').enable();";
      }

      // DIV RESOLUCION DE LA FACTURA
      $sql="SELECT prefijo,consecutivo_resolucion FROM ventas_facturas_configuracion WHERE activo=1 AND id=$id_resolucion";
      $query=$mysql->query($sql,$mysql->link);
      $prefijo        = $mysql->result($query,0,'prefijo');
      $num_resolucion = $mysql->result($query,0,'consecutivo_resolucion');
      $prefijo = ($prefijo=='')? '' : "$prefijo - " ;
      $divResolucion = "<div class='renglonTop'>
                          <div class='labelTop'>Resolucion</div>
                          <div class='campoTop'><input type='text' value='$prefijo $num_resolucion'  Readonly /></div>
                      </div>";
        //============================================== CHECKBOX RETENCIONES ===================================================//
        $checkboxRetenciones = '';
        $sqlRetenciones      = "SELECT id_retencion AS id, retencion,valor,tipo_retencion,base
                                FROM $tablaRetenciones
                                WHERE activo=1
                                AND $idTablaPrincipal='$id_factura_venta'";

        $queryRetenciones    = mysql_query($sqlRetenciones,$link);
        $checkboxRetenciones.= '<div class="renglonTop" id="checksRetenciones<?php echo $opcGrillaContable; ?>">
                                    <div class="labelTop">Retenciones</div>
                                    <div class="contenedorCheckbox">';

        while ($row=mysql_fetch_array($queryRetenciones)) {
            $row['valor'] = $row['valor']*1;
            $arrayTypeRetenciones .= 'arrayTypeRetenciones['.$row['id'].'] = "'.$row['tipo_retencion'].'";';

            $checkboxRetenciones .= '   <div class="campoCheck" title="'.$row['retencion'].'">
                                            <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                            <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" />
                                            <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                            </label>
                                        </div>';

            $objectRetenciones[$row['id']]  = 'objectRetenciones_'.$opcGrillaContable.'['.$row['id'].'] = {'
                                                                                        .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                        .'base           : "'.$row['base'].'",'
                                                                                        .'valor          : "'.$row['valor'].'",'
                                                                                        .'cuenta         : "'.$row['cuenta_venta'].'",'
                                                                                        .'estado         : "0"'
                                                                                    .'}';
        }

        $checkboxRetenciones .= '   </div>
                                </div>
                                <script>
                                    '.$arrayTypeRetenciones.'
                                    document.getElementById("divFormaPago'.$opcGrillaContable.'").style.display="inline";
                                </script>
                                <div class="renglonTop" style="display:none">
                                    <div class="labelTop">Numero de Factura</div>
                                    <div class="campoTop" ><input Readonly type="text" value="'.$numero_factura.'"/></div>
                                </div>';

        //=================================================== FORMAS DE PAGO ====================================================//
        $sqlFormasPago = "SELECT CFP.nombre,CFP.plazo
                          FROM $tablaPrincipal AS TP, configuracion_formas_pago AS CFP
                          WHERE CFP.id=TP.id_forma_pago AND TP.activo=1 AND TP.id='$id_factura_venta' AND TP.id_sucursal='$id_sucursal' AND TP.id_bodega='$filtro_bodega' AND TP.id_empresa='$id_empresa' LIMIT 0,1";
        $queryFormasPago = mysql_query($sqlFormasPago,$link);

        $nombreFormaPago = mysql_result($queryFormasPago,0,'nombre');
        $plazoFormaPago = mysql_result($queryFormasPago,0,'plazo');

        $acumScript.='document.getElementById("formaPago'.$opcGrillaContable.'").value= "'.$nombreFormaPago.'";';
        $texto_title=($estado=='3')? "<span style='color:red;text-align: center;font-size: 18px;font-weight: bold;'>Factura de Venta<br>N. ".$numero_factura."</span>"  : 'Factura de Venta<br>N. '.$numero_factura ;
        $acumScript .= 'document.getElementById("titleDocumentoFacturaVenta").innerHTML="'.$texto_title.'";';

        //=============================// ANTICIPOS //=============================//
        //*************************************************************************//
        $sqlAnticipos   = "SELECT SUM(valor) AS valorAnticipos FROM anticipos WHERE id_documento='$id_factura_venta' AND activo=1 AND tipo_documento='FV' AND id_empresa='$id_empresa'";
        $queryAnticipos = mysql_query($sqlAnticipos,$link);
        $totalAnticipo  = mysql_result($queryAnticipos, 0, 'valorAnticipos');

        if($totalAnticipo > 0){
            $totalAnticipo *= 1;

            $divAnticipo = '<div class="renglonTop">
                                <div class="labelTop">Anticipos</div>
                                <div class="campoTop"><input type="text" value="$ '.number_format($totalAnticipo).'" Readonly/></div>
                            </div>';
        }
    }

    if (strtotime($fechaBloqueo) <= strtotime($fechaActual)){ $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento de venta Vencido">'; }
    else{
        if($estado == '1'){
          $acumScript .= $user_permiso_editar.$user_permiso_cancelar.$user_permiso_anexar;
        } else if($estado == '2'){
          $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento de venta Cruzado">';
        } else if($estado == '3'){
          $acumScript .= $user_permiso_restaurar;
        } else{
          $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento de venta Cruzado">';
        }
    }

    //IMPRIMIR OBJECT RETENCIONES//
    $plainRetenciones = implode(';', $objectRetenciones).';';
    echo '<script>'.$plainRetenciones.'</script>';

    //=========================// CONSECUTIVOS DE REFERENCIA //=========================//
    //**********************************************************************************//
    $divRelacionado = '';
    if ($opcGrillaContable != 'CotizacionVenta'){


        $sqlCruce = "SELECT id,consecutivo_referencia AS numero_cruce, LEFT(nombre_consecutivo_referencia,1) AS string_cruce, nombre_consecutivo_referencia AS cruce
                    FROM $tablaInventario
                    WHERE $idTablaPrincipal=$id_factura_venta
                        AND activo=1
                        AND id_consecutivo_referencia > 0
                    GROUP BY consecutivo_referencia,nombre_consecutivo_referencia";
        $queryCruce = mysql_query($sqlCruce,$link);

        $cruce     = '';
        $contCruce = 0;
        while ($row = mysql_fetch_assoc($queryCruce)) {
            $contCruce++;
            $cruce .= '<div class="campoDocumentoCruzado"  id="btn'.$opcGrillaContable.'_'.$row['string_cruce'].'_'.$row['numero_cruce'].'" title="'.$row['string_cruce'].' '.$row['numero_cruce'].'" style="display:none"></div>
                       <div id="label'.$opcGrillaContable.'_'.$row['string_cruce'].'_'.$row['numero_cruce'].'" style="float:left; border-right:1px solid #d4d4d4; padding: 3px; height:100%;" ondblclick="colorFila_'.$opcGrillaContable.'('.$row['id'].')">'.$row['string_cruce'].' '.$row['numero_cruce'].'</div>';
        }

        if($contCruce > 0){
            $divRelacionado = '<div class="renglonTop">
                                    <div class="labelTop">Documentos Relacionados</div>
                                    <div class="campoTop">'.$cruce.'</div>
                                </div>';

        }
    }

    //=============================// DOCUMENTOS CRUCE //=============================//
    //********************************************************************************//
    $divCruce  = '';
    $cruce     = '';
    $contCruce = 0;
    if ($opcGrillaContable == 'FacturaVenta'){

        $sqlCruce = "SELECT tipo_documento,consecutivo_documento
                    FROM asientos_colgaap
                    WHERE id_documento_cruce = $id_factura_venta
                        AND tipo_documento_cruce = 'FV'
                        AND id_documento <> $id_factura_venta
                        AND tipo_documento <> 'FV'
                        AND activo = 1
                        AND id_empresa = $id_empresa
                    GROUP BY id_documento,tipo_documento";
        $queryCruce = mysql_query($sqlCruce,$link);


        while ($row = mysql_fetch_assoc($queryCruce)) {
            $contCruce++;
            $cruce .= '<div title="'.$row['tipo_documento'].' #'.$row['consecutivo_documento'].'" style="float:left; border-right:1px solid #d4d4d4; padding: 3px; height:100%;">'.$row['tipo_documento'].' '.$row['consecutivo_documento'].'</div>';
        }

        if($contCruce > 0){
            $divCruce = '<div class="renglonTop">
                            <div class="labelTop">Cruzado con:</div>
                            <div class="campoTop">'.$cruce.'</div>
                        </div>';
        }
        if($estado!==0 || $numero_factura !== 0 || $numero_factura !== '' ){
            echo "<script>if(document.querySelector('#titleRes') !== null){document.querySelector('#titleRes').innerHTML='';}</script>";
        }
    }

    if($opcGrillaContable=='RemisionesVenta'){
        $sqlCruce = "SELECT V.numero_factura_completo FROM ventas_facturas AS V, ventas_facturas_inventario AS I
                    WHERE I.id_consecutivo_referencia='$id_factura_venta'
                        AND I.nombre_consecutivo_referencia='Remision'
                        AND I.activo=1
                        AND I.id_factura_venta=V.id
                        AND V.estado=1
                    GROUP BY I.id_factura_venta";
        $queryCruce = mysql_query($sqlCruce,$link);
        while ($rowCruce = mysql_fetch_assoc($queryCruce)) {
            $contCruce++;
            $cruce .= '<div title="FV #'.$rowCruce['numero_factura_completo'].'" style="float:left; border-right:1px solid #d4d4d4; padding: 3px; height:100%;"><span style="color:blue; font-weight:bold;">FV</span> '.$rowCruce['numero_factura_completo'].'</div>';
        }

        $sqlCruce   = "SELECT consecutivo FROM devoluciones_venta WHERE id_documento_venta='$id_factura_venta' AND activo=1 AND estado=1 AND documento_venta='Remision'";
        $queryCruce = mysql_query($sqlCruce,$link);
        while ($rowCruce = mysql_fetch_assoc($queryCruce)) {
            $contCruce++;
            $cruce .= '<div title="NDRV #'.$rowCruce['consecutivo'].'" style="float:left; border-right:1px solid #d4d4d4; padding: 3px; height:100%;"><span style="color:blue; font-weight:bold;">NDRV</span> '.$rowCruce['consecutivo'].'</div>';
        }

        if($contCruce > 0){
            $divCruce = '<div class="renglonTop">
                            <div class="labelTop">Documentos Cruce:</div>
                            <div class="campoTop">'.$cruce.'</div>
                        </div>';
        }
    }

    if ($estado==3) {
        if ($opcGrillaContable == 'CotizacionVenta'){
            $texto_title="<span style='color:red;text-align: center;font-size: 18px;font-weight: bold;'>Cotizacion de Venta<br>N. ".$consecutivo."</span>" ;
        }
        if ($opcGrillaContable == 'PedidoVenta'){
            $texto_title="<span style='color:red;text-align: center;font-size: 18px;font-weight: bold;'>Pedido de Venta<br>N. ".$consecutivo."</span>" ;
        }
        if ($opcGrillaContable == 'RemisionesVenta'){
            $texto_title="<span style='color:red;text-align: center;font-size: 18px;font-weight: bold;'>Remision de Venta<br>N. ".$consecutivo."</span>" ;
        }
        if ($opcGrillaContable == 'FacturaVenta'){
            $texto_title="<span style='color:red;text-align: center;font-size: 18px;font-weight: bold;'>Factura de Venta<br>N. ".$numero_factura."</span>" ;
        }
        
        $acumScript.='document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$texto_title.'";';

    }
?>
<div class="contenedorDocumentoVentaBloqueado">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <?php echo $divImagen ; ?>
                <?php echo $divResolucion; ?>
                <div class="renglonTop">
                  <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                  <div class="labelTop">Fecha</div>
                  <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly/></div>
                  <div class="iconBuscarProveedor hideFE" onclick="abrirVentanaUpdateFechaBloqueada<?php echo $opcGrillaContable; ?>(this)" title="Editar Fecha">
                    <img src="img/config16.png" style="margin: 2px 0 0 2px;"/>
                  </div>
                </div>
                <div class="renglonTop">
                     <div class="labelTop">Vencimiento</div>
                    <div class="campoTop"  id="fechaLimitePago<?php echo $opcGrillaContable; ?>"><input type="text" value="<?php echo $fechaFin; ?>" Readonly /></div>
                </div>
                <div class="renglonTop" id="divFormaPago<?php echo $opcGrillaContable; ?>" style="display:none;">
                    <div class="labelTop">Forma de Pago</div>
                    <div class="campoTop" id="selectFormaPago<?php echo $opcGrillaContable; ?>">
                        <input type="text" id="formaPago<?php echo $opcGrillaContable; ?>" Readonly />
                    </div>
                </div>
                <div class="renglonTop" id="divMetodoPago<?php echo $opcGrillaContable; ?>">
                    <div class="labelTop">Metodo de Pago</div>
                    <div class="campoTop" id="selectMetodoPago<?php echo $opcGrillaContable; ?>">
                        <input type="text" id="metodoPago<?php echo $opcGrillaContable; ?>" value="<?php echo $metodo_pago; ?>" Readonly />
                    </div>
                </div>
                <?php echo $divCuentaPago; ?>
                <div class="renglonTop">
                    <div class="labelTop">Codigo</div>
                    <div class="campoTop"><input type="text" value="<?php echo $codigo_cliente; ?>" Readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" value="<?php echo $nit; ?>" Readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Cliente</div>
                    <div class="campoTop" style="width:277px;"><input type="text" style="width:100%" value="<?php echo $cliente ?>" Readonly /></div>
                </div>
                <?php echo $div_sucursal_cliente; ?>
                <div class="renglonTop">
                    <div class="labelTop">Vendedor</div>
                    <div class="campoTop" style="width:277px;"><input type="text" style="width:100%" value="<?php echo $nombre_vendedor; ?>" Readonly /></div>
                </div>
                <?php echo $checkboxRetenciones; ?>

                <?php
                    if ($opcGrillaContable=='FacturaVenta' || $opcGrillaContable=='RemisionesVenta'){ ?>
                        <div class="renglonTop" style="width:137px;">
                            <div class="labelTop" style="float:left; width:100%;">Centro de Costo</div>
                            <div class="campoTop"><input type="text" id="cCos_<?php echo $opcGrillaContable; ?>" value="<?php echo $labelCcos; ?>" Readonly/></div>
                        </div>
                <?php
                } ?>
                <?php echo $divRelacionado; ?>
                <?php echo $divCruce; ?>
                <?php echo $divAnticipo; ?>
                <?php echo $divOrdenCompra; ?>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>
<script>
  var observacion<?php echo $opcGrillaContable; ?> = '';
  <?php echo $acumScript; ?>

  var timeOutFlete<?php echo $opcGrillaContable; ?>       = ''       // var time out autoguardado onkeydows campo flete
  ,   timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
  ,   codigoProveedor<?php echo $opcGrillaContable; ?>    = 0
  ,   nitCliente<?php echo $opcGrillaContable; ?>         = 0
  ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
  ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

  document.getElementById('fecha<?php echo $opcGrillaContable; ?>').style.overflow = 'hidden !important';

  //=============== HABILITAR O DESHABILITAR ENVIO A LA DIAN ===============//
  // if("<?php echo $estado; ?>" == "1" && "<?php echo $opcGrillaContable; ?>" == "FacturaVenta"){
  //   Ext.getCmp("Btn_enviar_factura_electronica_<?php echo $opcGrillaContable; ?>").enable();
  // } else if("<?php echo $estado; ?>" == "3" && "<?php echo $opcGrillaContable; ?>" == "FacturaVenta"){
  //   Ext.getCmp("Btn_enviar_factura_electronica_<?php echo $opcGrillaContable; ?>").disable();
  // }
  //
  // if("<?php echo $response_FE; ?>" == "Ejemplar recibido exitosamente pasara a verificacion"){
  //   Ext.getCmp("Btn_enviar_factura_electronica_<?php echo $opcGrillaContable; ?>").disable();
  // } else{
  //   Ext.getCmp("Btn_enviar_factura_electronica_<?php echo $opcGrillaContable; ?>").enable();
  // }

  //============= ANEXAR ARCHIVOS ADJUNTOS A LA FACTURA DE VENTA =============//
	function anexa<?php echo $opcGrillaContable; ?>(){
		Win_Ventana_Documentos = new Ext.Window({
	    width       	: 546,
	    height      	: 500,
	    id          	: 'Win_Ventana_Documentos',
	    title       	: 'Formulario subir documentos',
	    modal       	: true,
	    autoScroll  	: false,
	    closable    	: false,
	    autoDestroy 	: true,
	    autoLoad    	: {
								        url  		   	: 'facturacion/ventana_documentos_factura_venta.php',
								        scripts 		: true,
								        nocache 		: true,
								        params  		: {
															          id_documento : <?php echo $id_factura_venta; ?>
															        }
									    },
	    tbar       		:	[
								        {
							            xtype   : 'buttongroup',
							            columns : 3,
							            title   : 'Opciones',
							            style   : 'border-right:none;',
							            items   : [
											                {
											                  xtype       : 'button',
											                  width       : 60,
											                  height      : 56,
											                  text        : 'Regresar',
											                  scale       : 'large',
											                  iconCls     : 'regresar',
											                  iconAlign   : 'top',
											                  hidden      : false,
											                  handler     : function(){ BloqBtn(this); Win_Ventana_Documentos.close(id) }
											                },
											                {
											                  xtype       : 'button',
											                  width       : 60,
											                  height      : 56,
											                  text        : 'Anexar',
											                  scale       : 'large',
											                  iconCls     : 'upload_file32',
											                  iconAlign   : 'top',
											                  hidden      : false,
											                  handler     : function(){ BloqBtn(this); windows_upload_file(); }
											                }
												            ]
								        }
								    	]
		}).show();
	}

  //==================== ABRIR MODAL PARA ANEXAR ARCHIVOS ====================//
	function windows_upload_file(){
		document.getElementById('divPadreModalUploadFile').setAttribute('style','display:block;');
	}
    
    function abrirVentanaUpdateFechaBloqueada<?php echo $opcGrillaContable; ?>(inputFechaFactura){

        Win_Ventana_update_fecha_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 275,
            height      : 235,
            id          : 'Win_Ventana_update_fecha_<?php echo $opcGrillaContable; ?>',
            title       : 'Validacion de usuario',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  : { opc : 'ventanaUpdateFecha' }
            },
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Opciones',
                    items   :
                    [
                        {
                            xtype     : 'button',
                            text      : 'Guardar',
                            scale     : 'large',
                            iconCls   : 'guardar',
                            width     : 60,
                            height    : 56,
                            iconAlign : 'top',
                            handler   : function(){ cambiar_update_fecha_bloqueada_<?php echo $opcGrillaContable; ?>() }
                        },
                        {
                            xtype     : 'button',
                            text      : 'Regresar',
                            scale     : 'large',
                            iconCls   : 'regresar',
                            width     : 60,
                            height    : 56,
                            iconAlign : 'top',
                            handler   : function(){ Win_Ventana_update_fecha_<?php echo $opcGrillaContable; ?>.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    function cambiar_update_fecha_bloqueada_<?php echo $opcGrillaContable; ?>(){
        var fecha    = document.getElementById('fecha_updateFechafactura').value
        ,   usuario  = document.getElementById('usuario_updateFechafactura').value
        ,   password = document.getElementById('password_updateFechafactura').value;

        usuario  = usuario.replace(/[\#\<\>\'\"]/g, '');
        password = password.replace(/[\#\<\>\'\"]/g, '');

        if(usuario.length <= 5){ alert("Aviso,\nInserte un usuario valido"); return; }
        else if(password.length <= 1){ alert("Aviso,\nInserte su codigo password valido"); return; }

        Ext.get('loadValidaUpdatefecha').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'QuickFechaUpdate',
                fecha             : fecha,
                usuario           : usuario,
                password          : password,
                idFacturaVenta    : '<?php echo $id_factura_venta; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            }
        });
    }
  //==================== CERRAR MODAL PARA ANEXAR ARCHIVOS ===================//
	function close_ventana_upload_file(){
		document.getElementById('divPadreModalUploadFile').setAttribute('style','');
	}

  //================ BUSCAR LA COTIZACION-PEDIDO POR SU NUMERO ===============//
  function buscarCotizacionPedido<?php echo $opcGrillaContable; ?>(event,Input){
      var tecla  = (Input) ? event.keyCode : event.which
      ,   numero = Input.value;

      if(tecla == 13 ){ ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
      patron = /[^\d]/;
      if(patron.test(numero)){ Input.value = numero.replace(patron,''); }

      return true;
  }

  function ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido){

      if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {                  //FACTURAS DESDE
          if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") {                    //COTIZACION
              tablaBuscar = "ventas_cotizaciones";
              opcCargar   = "cotizacion";
          }
          else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/pedido.png"){                    //PEDIDO
              opcCargar   = "pedido";
              tablaBuscar = "ventas_pedidos";
          }
          else{                                               //REMISION
              opcCargar   = "remision";
              tablaBuscar = "ventas_remisiones";
          }
          divRender=Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>");
      }
      else if('<?php echo $opcGrillaContable; ?>'=='RemisionesVenta') {                //REMISIONAR DESDE
          if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") {                 //COTIZACION
              tablaBuscar ="ventas_cotizaciones";
              opcCargar   = "cotizacionRemision";
          }
          else{                                           //PEDIDO
              tablaBuscar ="ventas_pedidos";
              opcCargar   = "pedidoRemision";
          }
          divRender=Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>");
      }
      else{                           //PEDIDO DESDE COTIZACION
          tablaBuscar = "ventas_cotizaciones";
          titulo      = "Seleccione la Cotizacion";
          tablaGrilla = "ventas_cotizaciones";
          divRender   = 'render_btns_<?php echo $opcGrillaContable; ?>';
          opcCargar   = ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta')? "cotizacion" : "cotizacionApedido";
          nameGrillaLoad = "grillaCotizacionFactura";
      }

      Ext.get(divRender).load({
          url     : "bd/bd.php",
          scripts : true,
          nocache : true,
          params  :
          {
              opc               : 'buscarCotizacionPedido',
              opcCargar         : opcCargar,
              tablaBuscar       : tablaBuscar,
              id                : idCotizacionPedido,
              opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
              filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
          }
      });
  }

  //======================= BUSCAR PEDIDO - COTIZACION =====================//
  function ventanaBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(){

      if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {              //FACTURA

          if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") {
              //cargar factura desde una cotizacion
              titulo         = "Seleccione la Cotizacion";
              tablaGrilla    = "ventas_cotizaciones";
              nameGrillaLoad = "grillaCotizacionFactura";
          }
          else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/pedido.png"){
              //cargar factura desde un pedido
              titulo         = "Seleccione el Pedido";
              tablaGrilla    = "ventas_pedidos";
              nameGrillaLoad = "grillaPedidoFactura";
          }
          else{
              titulo         = "Seleccione la Remision";
              tablaGrilla    = "ventas_remisiones";
              nameGrillaLoad = "grillaRemisionFactura";
          }
      }
      else if('<?php echo $opcGrillaContable; ?>'=='RemisionesVenta'){        //REMISION

          if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") {
              //cargar Remision desde una cotizacion
              titulo         = "Seleccione la Cotizacion";
              tablaGrilla    = "ventas_cotizaciones";
              nameGrillaLoad = "grillaCotizacionRemision";
          }
          else{
              //cargar Remision desde un pedido
              titulo         = "Seleccione el Pedido";
              tablaGrilla    = "ventas_pedidos";
              nameGrillaLoad = "grillaPedidoRemision";
          }
      }
      else{                                                                   //COTIZACION
          titulo         = "Seleccione la Cotizacion";
          tablaGrilla    = "ventas_cotizaciones";
          nameGrillaLoad = "grillaCotizacionFactura";
      }

      var myalto  = Ext.getBody().getHeight();
      var myancho = Ext.getBody().getWidth();

      Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?> = new Ext.Window({
          width       : myancho-100,
          height      : myalto-50,
          id          : 'Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>',
          title       : titulo,
          modal       : true,
          autoScroll  : false,
          closable    : false,
          autoDestroy : true,
          autoLoad    :
          {
              url     : 'bd/grillaBuscarCotizacionPedido.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opc                   : 'buscar_cotizacionPedido',
                  id_documento          : '<?php echo $id_factura_venta; ?>',
                  opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                  tablaCotizacionPedido : tablaGrilla,
                  nameGrillaLoad        : nameGrillaLoad,
                  filtro_bodega         : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
              }
          },
          tbar        :
          [
              {
                  xtype   : 'buttongroup',
                  columns : 3,
                  title   : 'Opciones',
                  items   :
                  [
                      {
                          xtype       : 'button',
                          width       : 60,
                          height      : 56,
                          text        : 'Regresar',
                          scale       : 'large',
                          iconCls     : 'regresar',
                          iconAlign   : 'left',
                          handler     : function(){ Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>.close(id) }
                      }
                  ]
              }
          ]
      }).show();
  }

  //=========================== BUSCAR UNA FACTURA ===========================//
  function buscar<?php echo $opcGrillaContable; ?>(){
    ventanaBuscar<?php echo $opcGrillaContable; ?>();
  }

  function ventanaBuscar<?php echo $opcGrillaContable; ?>(){
      var myalto  = Ext.getBody().getHeight();
      var myancho = Ext.getBody().getWidth();

      Win_Ventana_buscar_<?php echo $opcGrillaContable; ?> = new Ext.Window({
          width       : myancho-100,
          height      : myalto-50,
          id          : 'Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>',
          title       : 'Seleccionar ',
          modal       : true,
          autoScroll  : false,
          closable    : false,
          autoDestroy : true,
          autoLoad    :
          {
              url     : 'bd/buscarGrillaContable.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
              }
          },
          tbar        :
          [
              {
                  xtype   : 'buttongroup',
                  columns : 3,
                  title   : 'Opciones',
                  items   :
                  [
                      {
                          xtype       : 'button',
                          text        : 'Regresar',
                          scale       : 'large',
                          iconCls     : 'regresar',
                          height      : 56,
                          iconAlign   : 'top',
                          handler     : function(){ Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>.close(id) }
                      }
                  ]
              }
          ]
      }).show();
  }

  //================================ IMPRIMIR ================================//
  function imprimir<?php echo $opcGrillaContable; ?> (){
      var url = ('<?php echo $opcGrillaContable; ?>' == 'FacturaVenta')? 'facturacion/bd/imprimir_factura_venta.php': 'bd/imprimirGrillaContable.php';
      window.open(url+"?id=<?php echo $id_factura_venta; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
  }

  function imprimir<?php echo $opcGrillaContable; ?>Excel (){
      window.open("bd/exportar_excel_factura_compra.php?id="+'<?php echo $id_factura_venta; ?>');
  }

  function imprimirXml<?php echo $opcGrillaContable; ?>() {
    window.open("facturacion/bd/imprimirXMLFACSE.php?id=<?php echo $id_factura_venta; ?>");
  }

  //========================== CANCELAR UN DOCUMENTO =========================//
  function cancelar<?php echo $opcGrillaContable; ?>(){
      var contArticulos = 0;

      if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }

      arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');

      for(i in arrayIdsArticulos){
          if(arrayIdsArticulos[i].innerHTML != '' ){ contArticulos++; }
      }

      if(contArticulos > 0){
          if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
              cargando_documentos('Cancelando Documento...','');
              Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                  url  : 'bd/bd.php',
                  scripts : true,
                  nocache : true,
                  params  :
                  {
                      opc               : 'cancelarDocumento',
                      id                : '<?php echo $id_factura_venta; ?>',
                      opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                      idBodega          : '<?php echo $filtro_bodega; ?>'
                  }
              });
          };
      }
  }

  //=========================== EDITAR UN DOCUMENTO ==========================//
  function modificarDocumento<?php echo $opcGrillaContable ?>(){
      var texto = '';
      if ('<?php echo $opcGrillaContable; ?>' == 'RemisionesVenta' || '<?php echo $opcGrillaContable; ?>' == 'FacturaVenta'){ texto = "\nSi lo hace se eliminara el movimiento contable del mismo y\nRegresaran Los articulos al Inventario"; }

      if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?"+texto)) {
          cargando_documentos('Editando Documento...','');
          Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
              url     : 'bd/bd.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opc               : 'modificarDocumentoGenerado',
                  id       : '<?php echo $id_factura_venta; ?>',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  id_bodega         : '<?php echo $filtro_bodega; ?>',
              }
          });
      }
  }

  //========================= RESTAURAR UN DOCUMENTO =========================//
  function restaurar<?php echo $opcGrillaContable ?>(){
    cargando_documentos('Restaurando Documento...','');
    Ext.get('render_btns_<?php echo $opcGrillaContable ?>').load({
        url     : 'bd/bd.php',
        scripts : true,
        nocache : true,
        params  :
        {
            opc               : 'restaurarDocumento',
            id                : '<?php echo $id_factura_venta; ?>',
            opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            idBodega          : '<?php echo $filtro_bodega; ?>'
        }
    });
  }

  function colorFila_<?php echo $opcGrillaContable ?>(idItem){
      console.log(idItem);
  }

  //==================== CARGAR VENTANA DE ENVIO DE EMAIL ====================//
  function ventanaEnviarCorreo_<?php echo $opcGrillaContable ?>(){

      var myalto  = Ext.getBody().getHeight();
      var myancho = Ext.getBody().getWidth();

      var documento = document.getElementById('titleDocumento<?php echo $opcGrillaContable ?>').innerHTML;
      documento     = documento.split('<br>')[0];

      ventana_email = new Ext.Window({
          id          : 'Win_Ventana_EnviarOrden',
          title       : 'Enviar '+documento,
          iconCls     : 'pie2',
          width       : 950,
          height      : 530,
          modal       : true,
          autoDestroy : true,
          draggable   : false,
          resizable   : false,
          bodyStyle   : 'background-color:#DFE8F6;',
          autoLoad    :
          {
              url     : "bd/mail_documentos_ventas.php",
              scripts : true,
              nocache : true,
              params  :
              {
                  id  : '<?php echo $id_factura_venta; ?>',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
              }
          }
      }).show();

  }

  //======================= ENVIAR FACTURA ELECTRONICA =======================//
  function enviarDIAN<?php echo $opcGrillaContable; ?>(){
    var opcion = confirm('\u00BFEstas seguro de enviar la factura electronica?');
    if(opcion == true){
      cargando_documentos('Enviando Documento...','');
      Ext.get('render_btns_<?php echo $opcGrillaContable ?>').load({
        url     : 'bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc               : 'enviarFacturaDIAN',
                    id_factura        : '<?php echo $id_factura_venta; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                  }
      });
    }
  }

    function ventanaConfigurarInformeRemisionVenta(){

		Win_Ventana_configurar_separador_remision_venta = new Ext.Window({
		    width       : 500,
		    height      : 250,
		    id          : 'Win_Ventana_configurar_separador_remision_venta',
		    title       : 'Aplicar separador',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../ventas/bd/wizard_remision_venta_xls.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionRemisionVenta',
		        }
		    }
		}).show();
	}

    function imprimirRVXls (){
        const selectDecimales	  = document.getElementById('separadorDecimalesRV').value,
			  selectMiles 		  = document.getElementById('separadorMilesRV').value
        window.open("../ventas/bd/imprimirRemisionVentaXls.php?id=<?php echo $id_factura_venta; ?>&separador_decimales="+selectDecimales+
                                                                        "&separador_miles="+selectMiles);
    }
    
	function validarSelectRV(separadorId){
		const selectDecimales	  = document.getElementById('separadorDecimalesRV'),
			  selectMiles 		  = document.getElementById('separadorMilesRV')
        
		if(selectDecimales.value === selectMiles.value && separadorId === 'decimales'){
			selectMiles.value = (selectMiles.value === ',')? "." : ",";
		}
		else if(selectDecimales.value === selectMiles.value){
			selectDecimales.value = (selectDecimales.value === ',')? "." : ",";
		}
	}

</script>
