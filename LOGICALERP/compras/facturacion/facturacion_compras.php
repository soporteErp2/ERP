<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../funciones_globales/funciones_php/randomico.php");

    $id_empresa   = $_SESSION['EMPRESA'];
    $id_sucursal  = $_SESSION['SUCURSAL'];
    $id_usuario   = $_SESSION['IDUSUARIO'];
    $bodyArticle  = '';
    $acumScript   = '';
    $estado       = '';
    $fecha_actual = date('Y-m-d');

    $arrayTypeRetenciones = '';

    if(!isset($opcGrillaContable))$opcGrillaContable = 'FacturaCompra';
?>
<script>
    var arrayOrdenesSaldos              = new Array();                  // ARRAY QUE CONTIENE CANTIDADES MAXIMAS CUANDO SE CARGA UNA REMISION
    var arrayTypeRetenciones            = new Array();
    var objectRetenciones_FacturaCompra = new Array();
    var arrayRetencionesFacturaCompra   = new Array();
    var arrayResoluciones               = [];

    var objDocumentosCruceFacturaCompra=[];

    var fecha_inicio               = '<?php echo $fecha_actual; ?>'
    ,   fecha_final                = '<?php echo $fecha_actual; ?>'
    ,   subtotalFacturaCompra      = 0.00
    ,   acumuladodescuentoArticulo = 0.00
    ,   ivaFacturaCompra           = 0.00
    ,   retefuenteFacturaCompra    = 0.00
    ,   totalFacturaCompra         = 0.00
    ,   contArticulosFactura       = 1
    ,   id_proveedor_factura       = 0
    ,   numeroOrdenCompra          = 0
    ,   contabilidad_manual        = ''
    ,   subtotal_manual            = 0
    ,   iva_manual                 = 0
    ,   total_manual               = 0;

    arrayIvaFacturaCompra    = [];
    arrayIvaFacturaCompra[0] = { nombre:"Sin Iva", valor:0, saldo:0 };

    var timeOutObservacionFacturaCompra  = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoProveedorFactura           = 0
    ,   nitProveedorFactura              = 0
    ,   nombreProveedorFactura           = '';

    //Bloqueo todos los botones
    Ext.getCmp("Btn_imprimir_FacturaCompra").disable();
    Ext.getCmp("Btn_guardar_FacturaCompra").disable();
    Ext.getCmp("Btn_editar_FacturaCompra").disable();
    Ext.getCmp("Btn_cancelar_FacturaCompra").disable();
    Ext.getCmp("Btn_restaurar_FacturaCompra").disable();
    Ext.getCmp("BtnGroup_Estado1_FacturaCompra").hide();
    Ext.getCmp("BtnGroup_Guardar_FacturaCompra").show();

</script>
<?php

    $acumScript .= (user_permisos(38,'false') == 'true')? 'Ext.getCmp("Btn_guardar_FacturaCompra").enable();' : '';       //guardar
    $acumScript .= (user_permisos(40,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_FacturaCompra").enable();' : '';      //calcelar
    
       // CHECKBOX RETENCIONES ALMACENADAS
        $sqlRetenciones   = "SELECT id_retencion AS id,retencion,valor,tipo_retencion
                            FROM compras_facturas_retenciones
                            WHERE activo=1
                                AND id_factura_compra='$id_factura_compra'
                            GROUP BY id_retencion";
        $queryRetenciones = mysql_query($sqlRetenciones,$link);

        while ($row=mysql_fetch_array($queryRetenciones)) {
            $whereIdRetenciones .= ($whereIdRetenciones=='')? 'id<>'.$row['id'] : ' OR id<>'.$row['id'] ;
            $row['valor'] = $row['valor']*1;
            $arrayTypeRetenciones .= 'arrayTypeRetenciones['.$row['id'].'] = "'.$row['tipo_retencion'].'";';

            if(gettype($checkboxRetenciones[$row['id']])=='NULL'){
                $checkboxRetenciones[$row['id']] =  '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetencionesFacturaCompra_'.$row['id'].'">
                                                        <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                                        <input type="checkbox" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" onchange="checkboxRetenciones(this); document.getElementById(\'contenedorRetencionesFacturaCompra_'.$row['id'].'\').style.display=\'none\'" checked/>
                                                        <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                                            <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                            <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                        </label>
                                                    </div>';
            }
            else{
                $checkboxRetenciones[$row['id']] =  '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetencionesFacturaCompra_'.$row['id'].'">
                                                        <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                                        <input type="checkbox" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" onchange="checkboxRetenciones(this);" checked/>
                                                        <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                                            <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                            <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                        </label>
                                                    </div>';
            }
        }
    // }

    echo '<script>'.$arrayTypeRetenciones.'</script>';

    // FORMAS DE PAGO
    $formasPago      = '';
    $idFormaPago     = '';
    $arrayFormasPago = 'var idFechaSavePagoFactura = "";
                        var arrayFormaPagoFacutraCompra = new Array();';

    $sqlFormasPago   = "SELECT id,nombre,plazo FROM configuracion_formas_pago WHERE activo=1 AND id_empresa='$id_empresa'";
    $queryFormasPago = mysql_query($sqlFormasPago,$link);

    while ($rowFormasPago=mysql_fetch_array($queryFormasPago)) {
        if ($idFormaPago=='') {
            $idFormaPago     = $rowFormasPago['id'];
            $diasFormaPago   = $rowFormasPago['plazo'];
            $nombreFormaPago = $rowFormasPago['nombre'];
            $arrayFormasPago .= 'idFechaSavePagoFactura = "'.$idFormaPago.'";';
        }
        $formasPago      .= '<option value="'.$rowFormasPago['id'].'" >'.$rowFormasPago['nombre'].'</option>';
        $arrayFormasPago .= 'arrayFormaPagoFacutraCompra['.$rowFormasPago['id'].'] = "'.$rowFormasPago['plazo'].'";';
    }


    if ($formasPago=='') {
        echo'<script>
                alert("Error!\nNo hay ninguna forma de pago configurada\nDirijase al panel de control->formas de pago\nCree una y vuelva a intentarlo");
                Ext.getCmp("Btn_cancelar_FacturaCompra").enable();
            </script>';
        exit;
    }

    // CUENTA DE PAGO
    $cuentasPago      = '<option value="0" >Seleccione...</option>';
    $sqlCuentasPago   = "SELECT id,nombre,cuenta,cuenta_niif
                        FROM configuracion_cuentas_pago
                        WHERE activo=1 AND id_empresa='$id_empresa' AND tipo='Compra' AND (id_sucursal='$id_sucursal' OR id_sucursal=0)";
    $queryCuentasPago = mysql_query($sqlCuentasPago,$link);

    while ($rowCuentasPago=mysql_fetch_array($queryCuentasPago)) {
        if ($idConfigCuentaPago == ''){
            $idConfigCuentaPago = $rowCuentasPago['id'];
            $cuentaPago         = $rowCuentasPago['cuenta'];
            $cuentaPagoNiif     = $rowCuentasPago['cuenta_niif'];
            $configuracionCuentaPago = $rowCuentasPago['nombre'];
        }
        $cuentasPago .= '<option value="'.$rowCuentasPago['id'].'" >'.$rowCuentasPago['nombre'].' '.$rowCuentasPago['cuenta'].'</option>';
    }

    if ($cuentasPago=='') {
        echo'<script>
                alert("Error!\nNo hay ninguna cuenta de pago configurada\nDirijase al panel de control->cuentas de pago\nCree una y vuelva a intentarlo");
                Ext.getCmp("Btn_cancelar_FacturaCompra").enable();
            </script>';
        exit;
    }

    //========================================= SI SE CARGA UNA ORDEN DE COMPRA =========================================//
    //*******************************************************************************************************************//
    if(isset($consecutivoCarga)){
        //IDENTIFICAMOS EL DOCUMENTO A CARGAR CON LA VARIABLE $opcCargar
        if($opcCargar == 'orden_compra'){
          $tablaCarga            = 'compras_ordenes';
          $idTablaCarga          = 'id_orden_compra';
          $documentoCarga        = 'Orden de Compra';
          $tablaCargaInventario  = 'compras_ordenes_inventario';
          $campoSelectValidacion = ",autorizado";
        }
        else if($opcCargar == 'compras_entrada_almacen'){
          $tablaCarga           = 'compras_entrada_almacen';
          $idTablaCarga         = 'id_entrada_almacen';
          $documentoCarga       = 'Entrada de Almacen';
          $tablaCargaInventario = 'compras_entrada_almacen_inventario';
        }

        // QUERY DATOS DE LA ORDEN PARA COPIARLOS EN LA FACTURA
        $sqlConsulOrden   = "SELECT nit,id_proveedor,cod_proveedor,proveedor, observacion$campoSelectValidacion FROM $tablaCarga WHERE consecutivo='$consecutivoCarga' AND activo = 1 AND id_sucursal= '$id_sucursal' AND id_bodega= '$filtro_bodega' AND id_empresa='$id_empresa'";
        $queryConsulOrden         = mysql_query($sqlConsulOrden,$link);
        $nit                      = mysql_result($queryConsulOrden,0,'nit');
        $estado                   = mysql_result($queryConsulOrden,0,'estado');
        $proveedor                = mysql_result($queryConsulOrden,0,'proveedor');
        $id_proveedor             = mysql_result($queryConsulOrden,0,'id_proveedor');
        $cod_proveedor            = mysql_result($queryConsulOrden,0,'cod_proveedor');
        $observacionFacturaCompra = mysql_result($queryConsulOrden,0,'observaciones');

        // VERIFICAR SI ES ORDEN DE COMPRA, QUE ESTE AUTORIZADO SI DA A LUGAR
        if($opcCargar == 'orden_compra'){
            $autorizado = mysql_result($queryConsulOrden,0,'autorizado');

            // CONSULTAR SI TIENE AUTORIZACION POR PRECIO
            $sql="SELECT COUNT(id) AS aut_precio FROM costo_autorizadores_ordenes_compra WHERE activo=1 AND id_empresa=$id_empresa";
            $query = mysql_query($sql,$link);
            $aut_precio = mysql_result($query,0,'aut_precio');

            // CONSULTAR SI TIENE AUTORIZACION POR AREA
            $sql="SELECT COUNT(id) AS aut_area FROM costo_autorizadores_ordenes_compra_area WHERE activo=1 AND id_empresa=$id_empresa";
            $query = mysql_query($sql,$link);
            $aut_area = mysql_result($query,0,'aut_area');

            if ( ( $aut_precio>0 || $aut_area>0 ) && $autorizado=='false' ) {
                echo '<script>
                        alert("La Orden de compra no esta autorizada!");
                        nuevaFacturaCompra();
                    </script>';
                exit;
            }
        }

        // CREACION DEL ID RANDOMICO, Y CONSULTA ID INSERT FACTURA
        $random_factura = responseUnicoRanomico();

        $sqlInsertFactura   = "INSERT INTO compras_facturas(
                                    id_empresa,
                                    random,
                                    nit,
                                    id_proveedor,
                                    cod_proveedor,
                                    proveedor,
                                    id_sucursal,
                                    fecha_final,
                                    id_bodega,
                                    observacion,
                                    id_forma_pago,
                                    dias_pago,
                                    forma_pago,
                                    id_configuracion_cuenta_pago,
                                    configuracion_cuenta_pago,
                                    cuenta_pago,
                                    cuenta_pago_niif,
                                    id_usuario)
                                VALUES(
                                    '$id_empresa',
                                    '$random_factura',
                                    '$nit',
                                    '$id_proveedor',
                                    '$cod_proveedor',
                                    '$proveedor',
                                    '$id_sucursal',
                                    '$fecha_actual',
                                    '$filtro_bodega',
                                    '$observacionFacturaCompra',
                                    '$idFormaPago',
                                    '$diasFormaPago',
                                    '$nombreFormaPago',
                                    '$idConfigCuentaPago',
                                    '$configuracionCuentaPago',
                                    '$cuentaPago',
                                    '$cuentaPagoNiif',
                                    '$_SESSION[IDUSUARIO]')";
        $queryInsertFactura = mysql_query($sqlInsertFactura,$link);

        $sqlSelectIdFactura = "SELECT id FROM compras_facturas WHERE random='$random_factura' LIMIT 0,1";
        $id_factura_compra  = mysql_result(mysql_query($sqlSelectIdFactura,$link),0,'id');

        $arrayReplaceString       = array("\n", "\r");
        $observacionFacturaCompra = str_replace($arrayReplaceString, "\\n", $observacionFacturaCompra );

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fechaFactura",
                            editable   : false,
                            value      : "'.$fecha_actual.'",
                            listeners  : { select: function(combo, value) { guardaFechaFactura(this);  } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fechaFinalFactura",
                            editable   : false,
                            value      : "'.$fecha_actual.'",
                            listeners  : { select: function(combo, value) { guardaFechaFactura(this);  } }
                        });

                        document.getElementById("nitProveedorFactura").value    = "'.$nit.'";
                        document.getElementById("codProveedorFactura").value    = "'.$cod_proveedor.'";
                        document.getElementById("nombreProveedorFactura").value = "'.$proveedor.'";
                        document.getElementById("titleDocuementoFacturaCompra").innerHTML = "";

                        id_proveedor_factura   = "'.$id_proveedor.'";
                        codigoProveedorFactura = "'.$cod_proveedor.'";
                        nitProveedorFactura    = "'.$nit.'";
                        nombreProveedorFactura = "'.$proveedor.'";';

        //RETENCIONES ALMACENADAS O CON CARGA AUTOMATICA
        $sqlRetenciones   = "SELECT
                                R.id,
                                R.tipo_retencion,
                                R.cuenta,
                                R.retencion,
                                R.valor,
                                R.base
                            FROM
                                retenciones AS R
                            RIGHT JOIN terceros_retenciones AS TR ON (
                                (
                                    TR.id_retencion = R.id
                                    AND TR.id_proveedor = '$id_proveedor'
                                    OR R.factura_auto = 'true'
                                )
                                AND TR.activo = 1
                                AND TR.id_empresa = '$id_empresa'
                            )
                            WHERE
                                R.id_empresa = '$id_empresa'
                            AND R.activo = 1
                            AND R.cuenta > 0
                            AND R.modulo = 'Compra'
                            GROUP BY
                                R.id";

        $queryRetenciones = mysql_query($sqlRetenciones,$link);
        while($row = mysql_fetch_array($queryRetenciones)){
            $sqlInsertRetenciones   = "INSERT INTO compras_facturas_retenciones (id_factura_compra,id_retencion) VALUES ('$id_factura_compra','".$row['id']."')";
            $queryInsertRetenciones = mysql_query($sqlInsertRetenciones,$link);

            $row['valor'] = $row['valor']*1;
            if($queryInsertRetenciones){
                if(gettype($checkboxRetenciones[$row['id']])=='NULL'){
                    $checkboxRetenciones[$row['id']]=   '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetencionesFacturaCompra_'.$row['id'].'">
                                                            <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                                            <input type="hidden" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" onchange="checkboxRetenciones(this); document.getElementById(\'contenedorRetencionesFacturaCompra_'.$row['id'].'\').style.display=\'none\'" checked/>
                                                            <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                                                <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                                <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                            </label>
                                                        </div>';
                }
                else{
                    $checkboxRetenciones[$row['id']]=   '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetencionesFacturaCompra_'.$row['id'].'">
                                                            <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                                            <input type="hidden" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" onchange="checkboxRetenciones(this);" checked/>
                                                            <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                                                <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                                <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                            </label>
                                                        </div>';
                }
            }

            $acumScript.='arrayRetencionesFacturaCompra['.$row['id'].']='.$row['id'].';';
            $objectRetenciones[$row['id']]  = 'objectRetenciones_FacturaCompra['.$row['id'].'] = {'
                                                                                        .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                        .'base           : "'.$row['base'].'",'
                                                                                        .'valor          : "'.$row['valor'].'",'
                                                                                    .'}';
        }

        if($tablaCargaInventario == 'compras_entrada_almacen_inventario'){
          $campoSelectCheckOptionContable = "COI.check_opcion_contable,";
          $campoInsertCheckOptionContable = ",check_opcion_contable";
        }

        //CONSULTAMOS LOS ARTICULOS DEL DOCUMENTO A CARGAR PARA INSERTARLOS EN LA FACTURA
        $sql = "SELECT
                  COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.saldo_cantidad AS cantidad,COI.costo_unitario,
                  COI.tipo_descuento,COI.descuento,COI.id_centro_costos,$campoSelectCheckOptionContable
                  COI.id_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
                  CO.id AS id_documento,CO.consecutivo AS consecutivo_documento
                FROM $tablaCargaInventario AS COI
                INNER JOIN $tablaCarga AS CO ON COI.$idTablaCarga = CO.id
                WHERE CO.consecutivo = '$consecutivoCarga'
                AND COI.activo = 1
                AND CO.id_sucursal = '$id_sucursal'
                AND CO.id_bodega = '$filtro_bodega'";
        $query = mysql_query($sql,$link);

        $cadenaInsert = '';
        while($rowArt = mysql_fetch_array($query)){
          if($rowArt['cantidad'] == 0){ continue; }

          $cadenaInsert .= "('$id_factura_compra',
                              '".$rowArt['id']."',
                              '".$rowArt['id_documento']."',
                              '".$rowArt['consecutivo_documento']."',
                              '$documentoCarga',
                              '".$rowArt['id_inventario']."',
                              '".$rowArt['cantidad']."',
                              '".$rowArt['costo_unitario']."',
                              '".$rowArt['tipo_descuento']."',
                              '".$rowArt['descuento']."',
                              '".$rowArt['id_centro_costos']."',
                              '".$rowArt['observaciones']."',
                              '".$rowArt['id_impuesto']."'
                              ".(($tablaCargaInventario=='compras_entrada_almacen_inventario')? ",'".$rowArt['check_opcion_contable']."'" : '') ."
                            ),";
        }
        $cadenaInsert = substr($cadenaInsert,0,-1);
        $sqlInsertArticulo   = "INSERT INTO compras_facturas_inventario (
                                    id_factura_compra,
                                    id_tabla_referencia,
                                    id_consecutivo_referencia,
                                    consecutivo_referencia,
                                    nombre_consecutivo_referencia,
                                    id_inventario,
                                    cantidad,
                                    costo_unitario,
                                    tipo_descuento,
                                    descuento,
                                    id_centro_costos,
                                    observaciones,
                                    id_impuesto
                                    $campoInsertCheckOptionContable)
                                VALUES $cadenaInsert";
        $queryInsertArticulo = mysql_query($sqlInsertArticulo,$link);

        //VERIFICAMOS SI SE EJECUTO EL QUERY CORRECTAMENTE
        if(!$queryInsertArticulo){
          echo '<script>
                  document.getElementById("ordenCompra").blur();
                  alert("Error!\nSe produjo un error al cargar los articulos del documento\nIntentelo nuevamente\nSi el problema persiste comuniquese con el administrador del sistema");
                  setTimeout(function(){ document.getElementById("ordenCompra").focus(); },100);
                </script>';
          exit;
        }

        //POR ULTIMO VISUALIZAMOS EL CUERPO DE LA FACTURA CON LOS ARTICULOS CARGADOS
        include_once("bd/functions_body_article.php");
        $bodyArticle = cargaArticulosFacturaCompraSave($id_factura_compra,'',0,$link);
    }
    //=================================== SI NO EXISTE LA FACTURA SE CREA EL ID UNICO ==========================================//
    //**************************************************************************************************************************//
    else if(!isset($id_factura_compra)){

        // CREACION DEL ID RANDOMICO, Y CONSULTA ID INSERT FACTURA
        $random_factura = responseUnicoRanomico();

        $sqlInsertFactura   = "INSERT INTO compras_facturas(
                                    id_empresa,
                                    random,
                                    id_sucursal,
                                    fecha_final,
                                    id_bodega,
                                    id_forma_pago,
                                    dias_pago,
                                    forma_pago,
                                    id_configuracion_cuenta_pago,
                                    configuracion_cuenta_pago,
                                    cuenta_pago,
                                    cuenta_pago_niif,
                                    id_usuario)
                                VALUES('$id_empresa',
                                    '$random_factura',
                                    '$id_sucursal',
                                    '$fecha_actual',
                                    '$filtro_bodega',
                                    '$idFormaPago',
                                    '$diasFormaPago',
                                    '$nombreFormaPago',
                                    '$idConfigCuentaPago',
                                    '$configuracionCuentaPago',
                                    '$cuentaPago',
                                    '$cuentaPagoNiif',
                                    '$id_usuario')";
        $queryInsertFactura = mysql_query($sqlInsertFactura,$link);

        $sqlSelectIdFactura = "SELECT id FROM compras_facturas WHERE random='$random_factura' LIMIT 0,1";
        $id_factura_compra  = mysql_result(mysql_query($sqlSelectIdFactura,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fechaFactura",
                            editable   : false,
                            value      : "'.$fecha_actual.'",
                            listeners  : { select: function(combo, value) { guardaFechaFactura(this); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fechaFinalFactura",
                            editable   : false,
                            value      : "'.$fecha_actual.'",
                            listeners  : { select: function(combo, value) { guardaFechaFactura(this); } }
                        });
                        document.getElementById("titleDocuementoFacturaCompra").innerHTML="";';
        // carga_retenciones_automaticas($mysql);
    }
    //============================================= SI EXISTE LA FACTURA =======================================================//
    //**************************************************************************************************************************//
    else{

        include("bd/functions_body_article.php");

        $sql   = "SELECT id_proveedor,
                    prefijo_factura,
                    if(numero_factura > 0,numero_factura,'') AS numero_factura,
                    nit,
                    cod_proveedor,
                    proveedor,
                    plantillas_id,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha_inicio,
                    date_format(fecha_final,'%Y-%m-%d') AS fecha_final,
                    observacion,
                    estado,
                    id_forma_pago,
                    id_configuracion_cuenta_pago,
                    usuario_recibe_en_almacen,
                    contabilidad_manual,
                    subtotal_manual,
                    iva_manual,
                    total_manual,
                    id_tipo_factura,
                    id_resolucion,
                    tipo_documento,
                    consecutivo, 
                    response_DS
                FROM compras_facturas
                WHERE id='$id_factura_compra' AND activo = 1";
        $query = mysql_query($sql,$link);

        $nit                       = mysql_result($query,0,'nit');
        $proveedor                 = mysql_result($query,0,'proveedor');
        $id_proveedor              = mysql_result($query,0,'id_proveedor');
        $cod_proveedor             = mysql_result($query,0,'cod_proveedor');
        $idPlantilla               = mysql_result($query,0,'plantillas_id');
        $fecha_inicio              = mysql_result($query,0,'fecha_inicio');
        $fecha_final               = mysql_result($query,0,'fecha_final');
        $estado                    = mysql_result($query,0,'estado');
        $prefijo_factura           = mysql_result($query,0,'prefijo_factura');
        $numero_factura            = mysql_result($query,0,'numero_factura');
        $id_forma_pago             = mysql_result($query,0,'id_forma_pago');
        $idConfigCuentaPago        = mysql_result($query,0,'id_configuracion_cuenta_pago');
        $usuario_recibe_en_almacen = mysql_result($query,0,'usuario_recibe_en_almacen');
        $contabilidad_manual       = mysql_result($query,0,'contabilidad_manual');
        $id_tipo_factura           = mysql_result($query,0,'id_tipo_factura');
        $id_resolucion             = mysql_result($query,0,'id_resolucion');
        $tipo_documento            = mysql_result($query,0,'tipo_documento');
        $consecutivo               = mysql_result($query,0,'consecutivo');
        $response_DS               = mysql_result($query,0,'response_DS');

        if ($contabilidad_manual=='true') {
            $sql = "SELECT subtotal_manual,iva_manual,total_manual
                    FROM compras_facturas_contabilidad_manual
                    WHERE activo=1
                        AND id_empresa=$id_empresa
                        AND id_factura_compra=$id_factura_compra";
            $query = mysql_query($sql,$link);

            $subtotal_manual = mysql_result($query,0,'subtotal_manual');
            $iva_manual      = mysql_result($query,0,'iva_manual');
            $total_manual    = mysql_result($query,0,'total_manual');
        }

        $arrayReplaceString       = array("\n", "\r","<br>");
        $observacionFacturaCompra = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        // NO PERMITE CARGAR DOCUMENTOS CERRADOS
        if ($estado=='1') { echo "ESTA FACTURA DE COMPRA ESTA CERRADA!"; exit; }

        $acumScript .=  'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFactura",
                            editable   : false,
                            value      : "'.$fecha_inicio.'",
                            listeners  : { select: function(combo, value) { guardaFechaFactura(this);  } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFinalFactura",
                            editable   : false,
                            value      : "'.$fecha_final.'",
                            listeners  : { select: function(combo, value) { guardaFechaFactura(this);  } }
                        });

                        document.getElementById("plantilla_compra").value             = "'.$idPlantilla.'";
                        document.getElementById("nitProveedorFactura").value          = "'.$nit.'";
                        document.getElementById("codProveedorFactura").value          = "'.$cod_proveedor.'";
                        document.getElementById("nombreProveedorFactura").value       = "'.$proveedor.'";
                        document.getElementById("prefijoFactura").value               = "'.$prefijo_factura.'";
                        document.getElementById("numeroFactura").value                = "'.$numero_factura.'";
                        document.getElementById("nombreEmpleadoRecibioAlmacen").value = "'.$usuario_recibe_en_almacen.'";
                        document.getElementById("selectFormaPagoCompra").value        = "'.$id_forma_pago.'";
                        document.getElementById("observacionFacturaCompra").value     = "'.$observacionFacturaCompra.'";
                        document.getElementById("invoice_type").value        = "'.$id_tipo_factura.'";

                        fecha_inicio             = "'.$fecha_inicio.'";
                        fecha_final              = "'.$fecha_final.'";
                        id_proveedor_factura     = "'.$id_proveedor.'";
                        observacionFacturaCompra = "'.$observacionFacturaCompra.'";
                        codigoProveedorFactura   = "'.$cod_proveedor.'";
                        nitProveedorFactura      = "'.$nit.'";
                        nombreProveedorFactura   = "'.$proveedor.'";';


        // CHECKBOX RETENCIONES ALMACENADAS
        $sqlRetenciones   = "SELECT id_retencion AS id,retencion,valor,tipo_retencion,base
                            FROM compras_facturas_retenciones
                            WHERE activo=1
                                AND id_factura_compra='$id_factura_compra'
                            GROUP BY id_retencion";
        $queryRetenciones = mysql_query($sqlRetenciones,$link);

        while ($row=mysql_fetch_array($queryRetenciones)) {
            $row['valor'] = $row['valor']*1;
            $arrayTypeRetenciones .= 'arrayTypeRetenciones['.$row['id'].'] = "'.$row['tipo_retencion'].'";';

            if(gettype($checkboxRetenciones[$row['id']])=='NULL'){
                $checkboxRetenciones[$row['id']] =  '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetencionesFacturaCompra_'.$row['id'].'">
                                                        <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                                        <input type="hidden" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" />
                                                        <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                                            <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                            <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                        </label>
                                                    </div>';
            }
            else{
                $checkboxRetenciones[$row['id']] =  '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetencionesFacturaCompra_'.$row['id'].'">
                                                        <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                                        <input type="hidden" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" />
                                                        <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                                            <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                            <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                        </label>
                                                    </div>';
            }

            $acumScript.='arrayRetencionesFacturaCompra['.$row['id'].']='.$row['id'].';';
            $objectRetenciones[$row['id']]  = 'objectRetenciones_FacturaCompra['.$row['id'].'] = {'
                                                                                        .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                        .'base           : "'.$row['base'].'",'
                                                                                        .'valor          : "'.$row['valor'].'",'
                                                                                    .'}';
        }

        if ($contabilidad_manual=='true') {
            if ($subtotal_manual>0 && $total_manual>0) {
                $acumScript.='contabilidad_manual="true";
                                subtotal_manual='.$subtotal_manual.';
                                iva_manual='.$iva_manual.';
                                total_manual='.$total_manual.';
                                //recalculamos los valores de la factura
                                calcularValoresFactura(0,0,0,"","",0);';
            }
        }

        echo '<script>'.$arrayTypeRetenciones.'</script>';
        $bodyArticle = cargaArticulosFacturaCompraSave($id_factura_compra,$observacionFacturaCompra,$estado,$link);
    }
    $acumScript .= 'document.getElementById("selectCuentaPagoCompra").value="'.$idConfigCuentaPago.'"; '.$arrayFormasPago ;

    //CUANTAS ORDENES DE COMPRA
    $margin_left = 'margin-left:5px';
    $acumOrdenesCompra  = '';
    $sqlOrdenesCompra   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1)  AS doc_referencia,nombre_consecutivo_referencia
                            FROM compras_facturas_inventario
                            WHERE id_consecutivo_referencia>0
                                AND id_factura_compra='$id_factura_compra'
                                AND activo=1
                                AND id_empresa='$id_empresa'
                            ORDER BY id_consecutivo_referencia ASC";
    $queryOrdenesCompra = mysql_query($sqlOrdenesCompra,$link);

    while($rowDocReferencia = mysql_fetch_array($queryOrdenesCompra)){


        $acumOrdenesCompra .='<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;height: 22px;" id="divDocReferenciaFactura_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                <div class="contenedorInputDocReferenciaFactura">
                                    <input type="text" class="inputDocReferenciaFactura" value="'.$rowDocReferencia['doc_referencia'].' '.$rowDocReferencia['cod_referencia'].'" readonly style="border-bottom: 1px solid #d4d4d4;" />
                                </div>
                                <div title="'.$title.' # '.$rowDocReferencia['cod_referencia'].' en la presente factura" onclick="eliminaDocReferenciaFacturaCompra(\''.$rowDocReferencia['id_referencia'].'\',\''.$rowDocReferencia['nombre_consecutivo_referencia'].'\',\''.$id_factura_compra.'\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">
                                    <div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btnFacturaCompra_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                        <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                                    </div>
                                </div>
                            </div>';

    }

    $selectPlantilla   = '<option value="0">Seleccione...</option>';
    $sqlPlantilla      = "SELECT id,descripcion FROM plantillas WHERE id_empresa='$id_empresa' AND activo=1 AND referencia='Compra'";
    $querySqlPlantilla = mysql_query($sqlPlantilla,$link);
    while($rowPlantilla = mysql_fetch_array($querySqlPlantilla)){ $selectPlantilla .= '<option value="'.$rowPlantilla['id'].'">'.$rowPlantilla['descripcion'].'</option>'; }

    //IMPRIMIR OBJECT RETENCIONES//
    $plainRetenciones = implode(';', $objectRetenciones).';';
    echo '<script>'.$plainRetenciones.' //console.log(objectRetenciones_FacturaVenta);</script>';


    //=============================// ANTICIPOS //=============================//
    //*************************************************************************//
    $sqlAnticipos   = "SELECT SUM(valor) AS valorAnticipos FROM anticipos WHERE id_documento='$id_factura_compra' AND activo=1 AND tipo_documento='FC' AND id_empresa='$id_empresa'";
    $queryAnticipos = mysql_query($sqlAnticipos,$link);
    $totalAnticipo  = mysql_result($queryAnticipos, 0, 'valorAnticipos');
    $totalAnticipo *= 1;

    function carga_retenciones_automaticas($mysql){
        global $acumScript,$checkboxRetenciones;
        $whereIdRetenciones = ($whereIdRetenciones<>'')? ' AND '.$whereIdRetenciones : '' ;
        $sql="SELECT  id,retencion,valor,tipo_retencion
                FROM retenciones
                WHERE activo=1
                AND factura_auto='true'
                $whereIdRetenciones
                ";
        $query=$mysql->query($sql,$mysql->link);
        while ($row=$mysql->fetch_array($query)) {
            $acumScript.='carga_retenciones_automaticas('.$row['id'].');';

            $row['valor'] = $row['valor']*1;
            $arrayTypeRetenciones .= 'arrayTypeRetenciones['.$row['id'].'] = "'.$row['tipo_retencion'].'";';

            if(gettype($checkboxRetenciones[$row['id']])=='NULL'){
                $checkboxRetenciones[$row['id']] =  '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetencionesFacturaCompra_'.$row['id'].'">
                                                        <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                                        <input type="checkbox" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" onchange="checkboxRetenciones(this); document.getElementById(\'contenedorRetencionesFacturaCompra_'.$row['id'].'\').style.display=\'none\'" checked/>
                                                        <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                                            <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                            <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                        </label>
                                                    </div>';
            }
            else{
                $checkboxRetenciones[$row['id']] =  '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetencionesFacturaCompra_'.$row['id'].'">
                                                        <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                                        <input type="checkbox" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" onchange="checkboxRetenciones(this);" checked/>
                                                        <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                                            <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                            <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                        </label>
                                                    </div>';
            }

        }
    }

    // CONSULTAR LOS TIPOS DE FACTURAS
    $sql   = "SELECT id,nombre FROM compras_facturas_tipos WHERE activo=1 AND id_empresa='$id_empresa'";
    $query = $mysql->query($sql);
    while ($row=$mysql->fetch_array($query)) {
        $tipos .= "<option value='$row[id]' $selected >$row[nombre]</option>";
    }  

    // CONSULTAR LAS RESOLUCIONES DE DOCUMENTO SOPORTE  
    $sql   = "SELECT
                    RS.id_resolucion,
                    RS.numero_resolucion,
                    R.prefijo
                FROM
                    resolucion_documento_soporte_sucursales AS RS,
                    resolucion_documento_soporte AS R
                WHERE
                    RS.activo = 1
                AND RS.id_empresa='$id_empresa'
                AND RS.id_sucursal='$id_sucursal'
                AND R.id = RS.id_resolucion
                AND R.fecha_final_resolucion >= '".date('Y-m-d')."'";
    $query = $mysql->query($sql);
    while ($row=$mysql->fetch_array($query)) {
        ?>
            <script>
            arrayResoluciones[<?= $row["id_resolucion"] ?>] = {
                                                                "numero_resolucion" : "<?= $row["numero_resolucion"] ?>",
                                                                "prefijo"           : "<?= $row["prefijo"] ?>",
                                                            }
            </script>
        <?php
        $selected = ($row['id_resolucion']==$id_resolucion)? "selected" : "";
        $resolucion_doc_soporte .= "<option value='$row[id_resolucion]' $selected >$row[numero_resolucion]</option>";
    }
    $resolucion_doc_soporte = ($resolucion_doc_soporte=='')? '<option disabled>no hay resoluciones asignadas</option>' : $resolucion_doc_soporte ;

    // CONSULTAR LOS METODOS DE PAGINACION
    $sql   = "SELECT id,nombre,codigo_metodo_pago_dian FROM configuracion_metodos_pago WHERE activo=1 AND id_empresa='$id_empresa'";
    $query = $mysql->query($sql);
    while ($row=$mysql->fetch_array($query)) {
        $payMethod .= "<option value='$row[id]' $selected >$row[nombre]</option>";
    } 

    // CONSULTAR LA FORMA DE PAGO
    $sql   = "SELECT id,nombre,codigo_metodo_pago_dian FROM configuracion_metodos_pago WHERE activo=1 AND id_empresa='$id_empresa'";
    $query = $mysql->query($sql);
    while ($row=$mysql->fetch_array($query)) {
        $payMethod .= "<option value='$row[id]' $selected >$row[nombre]</option>";
    }
    

?>
<div class="contenedorFacturaCompra" id="contenedorFacturaCompra">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_factura_compra"></div>
            <div class="contTopFila">
                
                <div class="renglonTop">
                    <div class="labelTop">Tipo documento</div>
                    <div class="campoTop">
                        <select id="tipo_documento" onchange="showHideDocumentType();updateDocumentType(this.value)" <?=($consecutivo>0)? "disabled" : ""?> >
                            <option value="">Factura de compra</option>
                            <option value="05" <?= ($tipo_documento=="05")? "selected" : "" ?> >Documento Soporte</option> <!-- 05 - Documento soporte en adquisiciones efectuadas a sujetos no obligados a expedir factura o documento equivalente -->
                        </select>
                    </div>
                </div>

                <div class="renglonTop" id="content_support_document" >
                    <div class="labelTop">Resolucion</div>
                    <div id="resolutionLoad" style="float:left; margin-left:-22px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <select id="resolucion" onchange="updateSupportDocumentResolution(this.value)" <?=($consecutivo>0)? "disabled" : ""?> >
                            <option value="">seleccione...</option>
                            <?= $resolucion_doc_soporte ?>
                        </select>
                    </div>
                </div>

                <!-- <div class="renglonTop" id="content_support_document_pay_type" >
                    <div class="labelTop">Forma Pago</div>
                    <div id="payTypeLoad" style="float:left; margin-left:-22px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <select id="forma_pago" onchange='updateRows({"render":"payTypeLoad","row":"forma_pago","value":this.value})' <?=($consecutivo>0)? "disabled" : ""?> >
                            <option value="">seleccione...</option>
                            <option value="1">Contado</option>
                            <option value="2">Credito</option>
                        </select>
                    </div>
                </div> -->

                <div class="renglonTop" id="content_support_document_pay_method" >
                    <div class="labelTop">Medio Pago</div>
                    <div id="payMethodLoad" style="float:left; margin-left:-22px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <select id="metodo_pago" onchange='updateRows({"render":"payMethodLoad","row":"id_metodo_pago ","value":this.value})' <?=($response_DS=="Ejemplar recibido exitosamente pasara a verificacion")? "disabled" : ""?> >
                            <option value="">seleccione...</option>
                            <?= $payMethod ?>
                        </select>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Fecha de inicio</div>
                    <div class="campoTop"><input type="text" id="fechaFactura"></div>
                </div>
                <div class="renglonTop" >
                    <div class="labelTop">Fecha de Vencimiento</div>
                    <div class="campoTop"><input type="text" id="fechaFinalFactura"></div>
                </div>
                <div class="renglonTop" style="width:137px;display:none;">
                    <div class="labelTop" style="float:left; width:100%;">Forma de pago</div>
                    <div id="renderSelectFormaPago" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="overflow:hidden;">
                        <select id="selectFormaPagoCompra" onChange="UpdateFormaPagoCompra(this.value)" style="float:left;"/>
                            <?php echo $formasPago; ?>
                        </select>
                    </div>
                </div>
                <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Cuenta de pago</div>
                    <div id="renderSelectCuentaPago" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <select id="selectCuentaPagoCompra" onChange="UpdateCuentaPagoCompra(this.value)" style="float:left;"/>
                            <?php echo $cuentasPago; ?>
                        </select>
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Docs. Cruce</div>
                    <div class="campoTop" style="height:auto;" id="contenedorOrdenCompraFactura"><?php echo $acumOrdenesCompra; ?></div>
                </div>
                <div class="renglonTop" style="width:135px;">
                    <div class="labelTop" style="float:left; width:100%;">Factura #</div>
                    <div id="renderNumeroFactura" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <input type="text" id="prefijoFactura" style="width:30% !important; float:left;" onKeyup="convertirMayusculas(this);" onchange="validarNumeroFactura();">
                        <div style="width:10% !important; float:left;background-color:#F3F3F3; height:100%; text-align:center;">-</div>
                        <input type="text" id="numeroFactura" style="width:60% !important; float:left;" onchange="validarNumeroFactura();">
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Codigo Proveedor</div>
                    <div class="campoTop"><input type="text" id="codProveedorFactura" onKeyup="validarNumero_<?php echo $opcGrillaContable; ?>(event,this);" onchange="updateTerceroHead<?php echo $opcGrillaContable; ?>(this);" /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" id="nitProveedorFactura" onKeyup="validarNumero_<?php echo $opcGrillaContable; ?>(event,this);" onchange="updateTerceroHead<?php echo $opcGrillaContable; ?>(this);" /></div>
                </div>
                <div class="renglonTop">
                    <div id="renderProveedor" style="float:left; /*margin-left:-20px;*/ width:20px; height:19px; overflow:hidden;"></div>
                    <div class="labelTop">Proveedor</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreProveedorFactura" Readonly /></div>
                    <div onclick="buscarVentanaProveedorFactura()" id="imgBuscarProveedor" title="Buscar Proveedor" class="iconBuscarProveedor">
                       <img src="img/buscar20.png">
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Empleado que recibio en el almacen</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreEmpleadoRecibioAlmacen" Readonly /></div>
                    <div onclick="buscarVentanaEmpleadoRecibioAlmacenFactura()" id="imgBuscarProveedor" title="Buscar Empleado" class="iconBuscarProveedor">
                       <img src="img/buscar20.png">
                       <div id="renderEmpleadoRecibe" style="float:left; margin-left:-120px; width:20px; height:100%;"></div>
                    </div>
                </div>
                <div class="renglonTop" id="checksRetencionesFactura">
                    <div class="labelTop" ><div id="renderCheck" style="margin-left:0px;" class="renderCheck" ></div>Retenciones <img src="img/config16.png" style="float:right;cursor:pointer;margin: 0 3 0 0;width: 14;height: 14;" onclick="ventanaConfigurarRetencionesFacturaCompra()" title="Configurar Retenciones" /></div>
                    <div class="contenedorCheckbox"  id="contenedorCheckboxFacturaCompra">
                        <?php foreach ($checkboxRetenciones as $valor) { echo $valor; } ?>
                    </div>
                </div>
                <div class="renglonTop" style="width:137px;display:none">
                    <div class="labelTop" style="float:left; width:100%;">Plantilla</div>
                    <div id="renderSelectPlantilla" style="float:left; margin-left:-22px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <select id="plantilla_compra" onChange="UpdateCuentaPlantilla(this.value)" style="float:left;"/>
                            <?php echo $selectPlantilla; ?>
                        </select>
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Anticipos</div>
                    <div class="campoTop"><input type="text" id="anticipo_<?php echo $opcGrillaContable; ?>" value="<?php echo '$ '.ROUND($totalAnticipo,$_SESSION['DECIMALESMONEDA']); ?>" Readonly/></div>
                    <div class="iconBuscarProveedor" onclick="ventanaAnticipo_<?php echo $opcGrillaContable; ?>()" title="Valor anticipo">
                       <img src="img/config16.png" style="margin: 2px 0 0 2px;"/>
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">clasificacion de factura</div>
                    <div class="campoTop">
                        <select id="invoice_type" onchange="updateInvoceType({invoice_id:'<?= $id_factura_compra?>',select:this})">
                            <option>Seleccione...</option>
                            <?=$tipos?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulosFactura">
        <div class="renderFilasArticulo" id="renderizaNewArticuloFactura"><?php echo $bodyArticle; ?></div>
    </div>
</div>
<script>
    document.getElementById('codProveedorFactura').focus();
    var observacionFacturaCompra = '';
    <?php echo $acumScript; ?>              //CONFIG INPUTS DATE EXTJS

    document.getElementById("numeroFactura").onkeyup  = function(event){ return validarNumeroPrefijoFactura(event,this); };
    document.getElementById("prefijoFactura").onkeyup = function(event){ return validarNumeroPrefijoFactura(event,this); };

    function showHideDocumentType(){
        let type = document.getElementById("tipo_documento").value
        if (type=="") {
            document.getElementById('content_support_document').style.display            ='none';
            // document.getElementById('content_support_document_pay_type').style.display   ='none';
            document.getElementById('content_support_document_pay_method').style.display ='none';         
            document.getElementById('prefijoFactura').disabled=false;
            document.getElementById('prefijoFactura').title="";
            document.getElementById('prefijoFactura').value="";
        }
        else{
            document.getElementById('content_support_document').style.display            ='';
            // document.getElementById('content_support_document_pay_type').style.display   ='';
            document.getElementById('content_support_document_pay_method').style.display ='';
            document.getElementById('prefijoFactura').disabled=true;
            document.getElementById('prefijoFactura').title="se bloquea este campo por que se tomara el prefijo de la resolucion del documento soporte";           

        }
    }

    showHideDocumentType()

    function updateDocumentType(document_type){
        Ext.get('resolutionLoad').load({
        url     : 'facturacion/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc           : 'updateDocumentType',
                    document_type,
                    idFactura     : '<?php echo $id_factura_compra; ?>',
                  }
      });

    }

    function updateSupportDocumentResolution(resolution_id){
        document.getElementById('prefijoFactura').value=(arrayResoluciones[resolution_id])?arrayResoluciones[resolution_id].prefijo:"";
        Ext.get('resolutionLoad').load({
        url     : 'facturacion/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc           : 'updateSupportDocumentResolution',
                    resolution_id : resolution_id,
                    idFactura     : '<?php echo $id_factura_compra; ?>',
                  }
      });

    }

    /* update dinamical rows*/
    function updateRows({render,row,value}){
        Ext.get(render).load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  : {
                        opc       : 'updateRows',
                        row,
                        value,
                        idFactura : '<?php echo $id_factura_compra; ?>',
                      }
        });
    }


    function convertirMayusculas(input){ input.value=input.value.toUpperCase(); }

    function validarNumeroFactura(input){
      if(document.getElementById('nombreProveedorFactura').value == ""){
        alert("Por favor añadir primero un proveedor a la factura.");
        return;
      }

      nitProveedor = document.getElementById('nitProveedorFactura').value;
      prefijo      = document.getElementById('prefijoFactura').value;
      numero       = document.getElementById('numeroFactura').value;

      if(numero == "" || numero == null || numero == "undefined"){
        return;
      }

      Ext.get('renderNumeroFactura').load({
        url     : 'facturacion/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc            : 'validarNumeroFactura',
                    prefijoFactura : prefijo,
                    numeroFactura  : numero,
                    idFactura      : '<?php echo $id_factura_compra; ?>',
                    nitProveedor   : nitProveedor
                  }
      });
    }

    function validarNumeroPrefijoFactura(event,input){
        var tecla   = input ? event.keyCode : event.which
        ,   inputId = input.id
        ,   numero  = input.value;

        if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        if(inputId == 'numeroFactura'){ patron = /[^\d]/; }
        else{ patron = /[^A-Za-z0-9]/; }

        if(patron.test(numero)){ input.value = numero.replace(patron,''); }
        return true;
    }

    document.getElementById('fechaFactura').style.overflow ='hidden !important';

    //============================ UPDATE CHECK RETENCIONES PROVEEDOR ===============================//
    function checkboxRetenciones(Input){
        var action  = 'insertar'
        ,   idInput = (Input.id).split('_')[1];

        if (!Input.checked){ action = 'eliminar'; }

        Ext.get("renderCheck").load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'checkboxRetenciones',
                idFactura   : '<?php echo $id_factura_compra; ?>',
                idRetencion : (Input.id).split('_')[1],
                accion      : action
            }
        });
        //recalculamos los valores de la factura
        calcularValoresFactura(0,0,0,'','',0);
    }

    //=============== CARGAR RETENCIONES AUTOMATICAS ==================//
    function carga_retenciones_automaticas(idRetencion) {
        Ext.get("renderCheck").load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'checkboxRetenciones',
                idFactura   : '<?php echo $id_factura_compra; ?>',
                idRetencion : idRetencion,
                accion      : 'insertar'
            }
        });
        //recalculamos los valores de la factura
        calcularValoresFactura(0,0,0,'','',0);
    }

    //================================== UPDATE FORMAS DE PAGO ====================================//
    function UpdateFormaPagoCompra(idFormaPago){
        Ext.get('renderSelectFormaPago').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'UpdateFormaPago',
                idFormaPago : idFormaPago,
                id          : '<?php echo $id_factura_compra; ?>'
            }
        });
    }

    //================================== UPDATE CUENTAS DE PAGO ====================================//
    function UpdateCuentaPagoCompra(idCuentaPago){
        Ext.get('renderSelectCuentaPago').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc : 'UpdateCuentaPago',
                id  : '<?php echo $id_factura_compra; ?>',
                idCuentaPago : idCuentaPago,
            }
        });
    }

    //================================== UPDATE PLANTILLA ====================================//
    function UpdateCuentaPlantilla(idPlantilla){
        Ext.get('renderSelectPlantilla').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'UpdateIdPlantilla',
                idPlantilla : idPlantilla,
                id          : '<?php echo $id_factura_compra; ?>'
            }
        });
    }

    //=============================== CALC FECHA LARGA DE PAGO ===================================//
    calcFechaLargaPagoFacturaCompra();

    function calcFechaLargaPagoFacturaCompra(){
        var combo      = document.getElementById("selectFormaPagoCompra")
        ,   idFecha    = document.getElementById("selectFormaPagoCompra").value
        ,   arrayDays  = new Array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado")
        ,   arrayMeses = new Array ("Ene.","Feb.","Mar.","Abr.","May.","Jun.","Jul.","Ago.","Sep.","Oct.","Nov.","Dic.");

        idFechaSavePagoFactura = idFecha;

        var fechalimite   = Date.parse(document.getElementById('fechaFactura').value)
        ,   myDate        = new Date(fechalimite)
        ,   diasRestantes = (arrayFormaPagoFacutraCompra[idFecha]*1) + 1;

        myDate.setDate(myDate.getDate()+diasRestantes);

        var month = myDate.getMonth()
        ,   day   = myDate.getDate()
        ,   year  = myDate.getFullYear();

        // document.getElementById('fechaLargaPagoFacturaCompra').innerHTML=arrayDays[myDate.getDay()]+" "+day+" de "+arrayMeses[month]+" del "+year;
        // document.getElementById('fechaLargaPagoFacturaCompra').setAttribute('title',day+" de "+arrayMeses[month]+" del "+year);
        document.getElementById('selectFormaPagoCompra').setAttribute('title',arrayDays[myDate.getDay()]+" "+day+" de "+arrayMeses[month]+" del "+year);
    }

    //==================== CAMBIA TIPO DE DESCUENTO POR ARTICULO ===================================//
    function tipoDescuentoArticulo(cont){
        document.getElementById('divImageSaveFactura_'+cont).style.display = 'inline';
        // Si existe un articulo almacenado muestra el boton deshacer
        if(document.getElementById('idInsertArticuloFactura_'+cont).value > 0){ document.getElementById('divImageDeshacer_'+cont).style.display = 'block'; }

        //si esta en signo porcentaje cambia a pesos, y viceversa
        if (document.getElementById('imgDescuentoArticulo_'+cont).getAttribute('src') == 'img/porcentaje.png') {
            document.getElementById('imgDescuentoArticulo_'+cont).setAttribute("src","img/pesos.png");
            document.getElementById('tipoDescuentoArticulo_'+cont).setAttribute("title","En Pesos");
            document.getElementById('descuentoArticuloFactura_'+cont).focus();
        }
        else{
            document.getElementById('imgDescuentoArticulo_'+cont).setAttribute("src","img/porcentaje.png");
            document.getElementById('tipoDescuentoArticulo_'+cont).setAttribute("title","En Porcentaje");
            document.getElementById('descuentoArticuloFactura_'+cont).focus();
        }
    }

    //==================  GUARDAR LA OBSERVACION DE LA FACTURA ==================================//
    function inputObservacionFacturaCompra(event,input){
        document.getElementById('labelObservacionFactura').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13){ guardarObservacionFacturaOrdenCompra(); }

        clearTimeout(timeOutObservacionFacturaCompra);
        timeOutObservacionFacturaCompra = setTimeout(function(){
            guardarObservacionFacturaOrdenCompra();
        },1500);
    }

    function guardarObservacionFacturaOrdenCompra(){
        var observacion = document.getElementById('observacionFacturaCompra').value;
        observacion     = observacion.replace(/[\#\<\>\'\"]/g, '');

        clearTimeout(timeOutObservacionFacturaCompra);
        timeOutObservacionFacturaCompra = '';

        Ext.Ajax.request({
            url     : 'facturacion/bd/bd.php',
            params  :
            {
                opc         : 'guardarObservacionFacturaCompra',
                idFactura   : '<?php echo $id_factura_compra; ?>',
                observacion : observacion
            },
            success :function (result, request){
                        if(result.responseText != ' true'){
                            // alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById("observacionFacturaCompra").value=observacionFacturaCompra;
                            document.getElementById('labelObservacionFactura').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacionFactura').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
                        else{
                            observacionFacturaCompra = observacion;
                            document.getElementById('labelObservacionFactura').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardada</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacionFactura').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
                    },
            failure : function(){
                // alert('Error de conexion con el servidor'); document.getElementById("observacionFacturaCompra").value=observacionFacturaCompra;
                document.getElementById('labelObservacionFactura').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                setTimeout(function () {
                    document.getElementById('labelObservacionFactura').innerHTML='<b>OBSERVACIONES</b>';
                },1200);
            }
        });
    }

    //========================= FILTRO TECLA BUSCAR TERCERO =========================//
    function validarNumero_<?php echo $opcGrillaContable; ?>(event,input){
        var numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if(tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
    }

    function updateTerceroHead<?php echo $opcGrillaContable; ?>(inputTercero){
        var inputId      = inputTercero.id
        ,   codProveedor = inputTercero.value
        ,   urlRender    = 'renderizaNewArticuloFactura'
        ,   evt          = 'insert'

        if(id_proveedor_factura > 0 && contArticulosFactura > 1){
            urlRender = 'renderProveedor';
            evt       = 'update';
        }

        Ext.get(urlRender).load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc          : 'updateTerceroHead',
                evt          : evt,
                codProveedor : codProveedor,
                idFactura    : '<?php echo $id_factura_compra; ?>',
                inputId      : inputId
            }
        });
    }

    function buscarVentanaProveedorFactura(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND tipo_proveedor = \"Si\"';
        Win_VentanaProveedor_Factura = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaProveedor_Factura',
            title       : 'Proveedores',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : sql,
                    cargaFuncion  : 'renderizaResultadoVentanaFactura(id);',
                    nombre_grilla : 'proveedorFactura'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_VentanaProveedor_Factura.close(id) }
                },'-'
            ]
        }).show();
    }

    function renderizaResultadoVentanaFactura(id){
        //PONEMOS EL VALOR DEL NUMERO DE IDENTIFICACION
        document.getElementById('nitProveedorFactura').value=document.getElementById('div_proveedorFactura_numero_identificacion_'+id).innerHTML;

        updateTerceroHead<?php echo $opcGrillaContable; ?>(document.getElementById("nitProveedorFactura"));
        Win_VentanaProveedor_Factura.close();
    }

    //============================= BUSCAR EL EMPLEADO QUE RECIBIO EN EL ALMACEN ===========================//
    function buscarVentanaEmpleadoRecibioAlmacenFactura(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaProveedor_Factura = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaProveedor_Factura',
            title       : 'Empleados',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaEmpleados.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    cargaFuncion  : 'renderEmpleadoAlmacenFactura(id);',
                    nombre_grilla : 'empleadoFactura'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_VentanaProveedor_Factura.close(id) }
                },'-'
            ]
        }).show();
    }

    function renderEmpleadoAlmacenFactura(id){
        Ext.get('renderEmpleadoRecibe').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc       : 'guardarEmpleadoRecibeAlmacen',
                id        :  id,
                idFactura : '<?php echo $id_factura_compra; ?>'
            }
        });
    }

    //========= FUNCION PARA BUSCAR LA ORDEN DE LA COMPRA POR SU NUMERO ========//
    function buscarOrdenCompraFactura(event,Input){
        var numero = Input.value
        ,   tecla  = (Input) ? event.keyCode : event.which;

        if(numero > 0 && tecla == 13){
            var validacion= validarArticulosFactura();
            if (validacion==1) {
                if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ agregarOrdenCompraFactura('false'); }
            }
            else if (validacion== 2 || validacion== 0) { agregarOrdenCompraFactura('false'); }
            return true;
        }
        patron = /[^\d]/;
        if(patron.test(numero)){ Input.value = numero.replace(patron,''); }
        return true;
    }

    //================ AJAX BUSCAR DOCUMENTO DE REFERENCIA ===================//
    function ajaxBuscarFacturaCompraOrden(idOrdenCompra,confirm){
        var opcCargar = '';

        if (document.getElementById('imgCargarDesdeFacturaCompra').getAttribute('src')=='img/pedido.png'){ opcCargar='ordenCompra'; }               //CARGA UNA ORDEN DE COMPRA
        else if (document.getElementById('imgCargarDesdeFacturaCompra').getAttribute('src')=='img/cotizacion.png'){ opcCargar='entradaAlmacen'; }   //CARGA UNA ENTRADA A ALMACEN

        Ext.get("renderCargarDocumento").load({
            url     : "facturacion/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc           : 'buscarOrdenCompra',
                idOrdenCompra : idOrdenCompra,
                opcCargar     : opcCargar,
                confirm       : confirm,
                filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value
            }
        });
    }

    //============== AJAX DOCUMENTO DE REFENCIA NEW FACTURA ===============//
    function ajaxCambiaFacturaOrden(Input){
        // Reset campos Proveedor
        document.getElementById("nombreProveedorFactura").value   = '';
        document.getElementById("bodyArticulosFactura").innerHTML = '<div class="contTopFila" id="renderizaNewArticuloFactura"></div>';
        if(Input.id != 'codProveedorFactura'){ document.getElementById("codProveedorFactura").value = ''; }
        else if(Input.id != 'nitProveedorFactura'){ document.getElementById("nitProveedorFactura").value = ''; }

        // Reset Checks Proveedor y se deshabilitan
        var checks = document.getElementById('checksRetencionesFactura').getElementsByTagName('input');
        for(i in checks){ checks[i].checked=false; checks[i].checked=false; }

        Ext.get("contenedor_facturacion_compras").load({
            url     : "facturacion/facturacion_compras.php",
            scripts : true,
            nocache : true,
            params  : { filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value }
        });
    }

    //================ AGREGAR DOCUMENTO REFERENCIA ===================//
    function agregarOrdenCompraFactura(confirmacion){

        var opcCargar     = ''
        ,   codDocAgregar = document.getElementById('ordenCompra').value
        ,   arrayInput    = document.getElementById('contenedorOrdenCompraFactura').querySelectorAll('.inputOrdenCompraFactura');

        if(typeof(confirmacion)=='undefined'){ confirmacion = 'false'; }

        if (document.getElementById('imgCargarDesdeFacturaCompra').getAttribute('src')=='img/pedido.png'){ opcCargar='orden_compra'; }                   //CARGA ORDEN DE COMPRA
        else if(document.getElementById('imgCargarDesdeFacturaCompra').getAttribute('src')=='../ventas/img/remisiones.png'){ opcCargar='compras_entrada_almacen'; }        //CARGA ENTRADA DE ALMACEN

        if(isNaN(codDocAgregar) || codDocAgregar==0){ alert('Numero de orden de compra no valido'); return; }
        for(i in arrayInput){

            if(codDocAgregar == arrayInput[i].value){
                document.getElementById("ordenCompra").value='';
                alert("La orden de compra codigo "+codDocAgregar+" ya fue agreagada en la presente factura.");
                document.getElementById("ordenCompra").focus();
                return;
            }
        }

        Ext.get("renderizaNewArticuloFactura").load({
            url     : "facturacion/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc                : 'AgregarOrdenCompra',
                codDocAgregar      : codDocAgregar,
                confirmar          : confirmacion,
                id_factura         : '<?php echo $id_factura_compra; ?>',
                idProveedorFactura : id_proveedor_factura,
                opcCargar          : opcCargar,
                filtro_bodega      : document.getElementById("filtro_ubicacion_facturacion_compras").value
            }
        });
    }

    //======================== VENTANA BUSCAR PEDIDO - COTIZACION ===========================================================//
    function ventanaBuscarDocumentoCruceFacturaCompra(){

        if (document.getElementById("imgCargarDesdeFacturaCompra").getAttribute("src")=="img/pedido.png"){       //CARGAR ORDEN DE COMPRA
        // if (cambioCargaFacturaCompra==0){       //CARGAR ORDEN DE COMPRA
            titulo         = "Seleccione la Orden de Compra";
            tablaGrilla    = "compras_ordenes";
            nameGrillaLoad = "grillaOrdenCompraEntradaAlmacen";
        }
        else{
            titulo         = "Seleccione la Entrada de Almacen";
            tablaGrilla    = "compras_entrada_almacen";
            nameGrillaLoad = "grillaEntradaAlmacen";
        }

        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_documento_cruceFacturaCompra = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_documento_cruceFacturaCompra',
            title       : titulo,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/bd/grillaBuscarDocumentoCruce.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                 : 'buscar_documento_cruce',
                    id_documento        : '<?php echo $id_factura_compra; ?>',
                    opcGrillaContable   : 'FacturaCompra',
                    tablaDocumentoCruce : tablaGrilla,
                    nameGrillaLoad      : nameGrillaLoad,
                    filtro_bodega       : document.getElementById("filtro_ubicacion_facturacion_compras").value
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
                            handler     : function(){ Win_Ventana_buscar_documento_cruceFacturaCompra.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    //======================== FUNCION PARA BUSCAR LA COTIZACION-PEDIDO POR SU NUMERO =======================================//
    function buscarDocumentoCruceFacturaCompra(event,Input){
        var tecla   = (Input) ? event.keyCode : event.which
        ,   numero  = Input.value;

        if(tecla == 13 || tecla == 9){
            var validacion= validarArticulosFactura();
            if (validacion==1) {
                if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ agregarDocumentoFacturaCompra(Input.value); }
            }
            else if (validacion== 2 || validacion== 0) { agregarDocumentoFacturaCompra(Input.value); }
            return;
        }

        setTimeout(function(){ Input.value = (Input.value).replace(/[^0-9]/g,''); },10);
    }

    //========================= FUNCION PARA AGREGAR UN DOCUMENTO ===================================================//
    function agregarDocumentoFacturaCompra(codigo){

        if (codigo!='') { var codDocAgregar=codigo; }
        else{ var codDocAgregar = document.getElementById('cotizacionPedidoFacturaCompra').value; }

        if(isNaN(codDocAgregar) || codDocAgregar==0){ alert('digite el consecutivo del documento que desea cargar.'); return;}
        if (document.getElementById("imgCargarDesdeFacturaCompra").getAttribute("src")=="../ventas/img/remisiones.png") { typeDoc = "compras_entrada_almacen"; }
        else if (document.getElementById("imgCargarDesdeFacturaCompra").getAttribute("src")=="img/pedido.png"){ typeDoc = "orden_compra"; }

        Ext.get("renderCargaCotizacionPedidoFacturaCompra").load({
            url     : "facturacion/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'agregarDocumento',
                typeDoc           : typeDoc,
                codDocAgregar     : codDocAgregar,
                id_factura        : '<?php echo $id_factura_compra ?>',
                opcGrillaContable : "FacturaCompra",
                filtro_bodega     : document.getElementById("filtro_ubicacion_facturacion_compras").value
            }
        });
    }

    function eliminaDocReferenciaFacturaCompra(idDocReferencia,docReferencia,id_factura_venta){
        Ext.get('renderizaNewArticuloFactura').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'eliminaDocReferencia',
                opcGrillaContable : 'FacturaCompra',
                id_factura        : '<?php echo $id_factura_compra; ?>',
                id_doc_referencia : idDocReferencia,
                docReferencia     : docReferencia,
                filtro_bodega     : document.getElementById("filtro_ubicacion_facturacion_compras").value
            }
        });
    }

    //====================== CAMBIA CHECK OPCION CONTABLE =======================//
    function changeCheckOptionContable(cont,campoCheck){
        if(document.getElementById('idInsertArticuloFactura_'+cont).value > 0){
            document.getElementById('divImageDeshacer_'+cont).style.display    = 'block';
            document.getElementById("divImageSaveFactura_"+cont).style.display = 'inline';
        }

        if(campoCheck.checked == true){
            var selectorCheck = document.getElementById('bodyDivArticulosFactura_'+cont).querySelectorAll('.optionCheckContable_'+cont);
            var contSelector  = (selectorCheck.length * 1)-1;
            for(var i=0; i<=contSelector; i++){
                if(selectorCheck[i].id != campoCheck.id){ selectorCheck[i].checked = false; }
            }
        }
    }

    //====================== FILTRO TECLA BUSCAR ARTICULO =======================//
    function buscarArticuloFactura(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if (id_proveedor_factura > 0 && input.value>0 && tecla == 13) {
            input.blur();
            ajaxBuscarArticuloFactura(input.value, input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }
        if(document.getElementById('idInsertArticuloFactura_'+contIdInput).value > 0){
            document.getElementById('idArticuloFactura_'+contIdInput).value                 = 0;
            document.getElementById("unidadesFactura_"+contIdInput).value                   = "";
            document.getElementById("costoArticuloFactura_"+contIdInput).value              = "";
            document.getElementById("nombreArticuloFactura_"+contIdInput).value             = "";
            document.getElementById("costoTotalArticuloFactura_"+contIdInput).value         = "";

            document.getElementById("div_check_factura_activo_fijo_"+contIdInput).innerHTML = "";
            document.getElementById("div_check_factura_gasto_"+contIdInput).innerHTML       = "";
            document.getElementById("div_check_factura_costo_"+contIdInput).innerHTML       = "";

            document.getElementById('divImageDeshacer_'+contIdInput).style.display    = 'block';
            document.getElementById("divImageSaveFactura_"+contIdInput).style.display = 'inline';
        }
        else if(document.getElementById('idArticuloFactura_'+contIdInput).value > 0){
            document.getElementById('idArticuloFactura_'+contIdInput).value         = 0;
            document.getElementById("unidadesFactura_"+contIdInput).value           = "";
            document.getElementById("costoArticuloFactura_"+contIdInput).value      = "";
            document.getElementById("nombreArticuloFactura_"+contIdInput).value     = "";
            document.getElementById("costoTotalArticuloFactura_"+contIdInput).value = "";

            document.getElementById("div_check_factura_activo_fijo_"+contIdInput).innerHTML = "";
            document.getElementById("div_check_factura_gasto_"+contIdInput).innerHTML       = "";
            document.getElementById("div_check_factura_costo_"+contIdInput).innerHTML       = "";
        }
        return true;
    }

    //======================= AJAX TECLA BUSCAR ARTICULO ========================//
    function ajaxBuscarArticuloFactura(valor,input){
        var cont = input.split('_')[1];
        Ext.get('renderArticuloFactura_'+cont).load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc           : 'buscarArticuloFactura',
                valorArticulo : valor,
                contArticulo  : cont,
                idFactura     : '<?php echo $id_factura_compra; ?>',
                idBodega      :document.getElementById("filtro_ubicacion_facturacion_compras").value
            }
        });
    }

    //================= VENTANA BUSCAR ARTICULO POR PORVEEDOR =================//
    function ventanaBuscarArticuloFactura(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();
        var sql     = 'AND estado_compra = "true"';

        Win_Ventana_buscar_Articulo_factura = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_Articulo_factura',
            title       : 'Seleccionar articulo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaInventarios.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : sql,
                    nombre_grilla : 'ventanaBucarArticuloFactura',
                    nombreTabla   : 'items',
                    cargaFuncion  : 'responseVentanaBuscarArticuloFactura(id,'+cont+');'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_buscar_Articulo_factura.close(id) }
                },'-'
            ]
        }).show();
    }

    //=========== RESPONSE VENTANA BUSCAR ARTICULO POR PORVEEDOR ===========//
    function responseVentanaBuscarArticuloFactura(id,cont){
        var  codigo = document.getElementById('div_ventanaBucarArticuloFactura_codigo_'+id).innerHTML;

         if(document.getElementById('idInsertArticuloFactura_'+cont).value > 0){
            document.getElementById('idArticuloFactura_'+cont).value                 = 0;
            document.getElementById("unidadesFactura_"+cont).value                   = "";
            document.getElementById("costoArticuloFactura_"+cont).value              = "";
            document.getElementById("nombreArticuloFactura_"+cont).value             = "";
            document.getElementById("costoTotalArticuloFactura_"+cont).value         = "";
            document.getElementById("div_check_factura_activo_fijo_"+cont).innerHTML = "";
            document.getElementById("div_check_factura_gasto_"+cont).innerHTML       = "";
            document.getElementById("div_check_factura_costo_"+cont).innerHTML       = "";

            document.getElementById('divImageDeshacer_'+cont).style.display    = 'block';
            document.getElementById("divImageSaveFactura_"+cont).style.display = 'inline';
        }

        ajaxBuscarArticuloFactura(codigo,'eanArticuloFactura_'+cont);
        Win_Ventana_buscar_Articulo_factura.close(id)
    }

    //===================================== FILTRO TECLA GUARDAR ARTICULO =============================================//
    function guardarAutoFactura(event,input,cont){
        var tecla = (input) ? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            guardarNewArticuloFactura(cont);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(value)){
            value       = value.replace(patron,'');
            input.value = value;
        }
        else if(isNaN(value)){ input.value = value.substring(0, value.length-1); }
        else {
            document.getElementById('divImageSaveFactura_'+cont).style.display = 'inline';
            if(document.getElementById('idInsertArticuloFactura_'+cont).value > 0){
                document.getElementById('divImageDeshacer_'+cont).style.display = 'block';
            }
        }
        return true;
    }

    function guardarNewArticuloFactura(cont){
        var checkOpcionContable      = '';
        var divRender                = '';
        var accion                   = 'agregar';
        var opc                      = 'guardarArticuloFactura';
        var imgGuardar               = document.getElementById('divImageSaveFactura_'+cont);
        var idInsertArticulo         = document.getElementById('idInsertArticuloFactura_'+cont).value;
        var idInventarioFactura      = document.getElementById('idArticuloFactura_'+cont).value;
        var cantArticuloFactura      = document.getElementById('cantArticuloFactura_'+cont).value;
        var descuentoArticuloFactura = document.getElementById('descuentoArticuloFactura_'+cont).value;
        var costoArticuloFactura     = document.getElementById('costoArticuloFactura_'+cont).value;
        var iva                      = document.getElementById('ivaArticuloFacturaCompra_'+cont).value;
        var tipoDesc                 = ((document.getElementById('imgDescuentoArticulo_'+cont).getAttribute("src")).split('/')[1]).split('.')[0];

        if(imgGuardar.style.display == 'none' ){ return; }
        if(imgGuardar.style.display == 'block' || imgGuardar.style.display == 'inline' || imgGuardar.style.display == '' ) {document.getElementById('divImageSaveFactura_'+cont).style.display='none';}

        //OPCION CONTABLE
        var arrayCheckContable = document.getElementById('bodyDivArticulosFactura_'+cont).querySelectorAll('.optionCheckContable_'+cont);
        var contCheckContable  = (arrayCheckContable.length * 1) - 1;

        for(var i=0; i<= contCheckContable; i++){
            if(arrayCheckContable[i].checked == true){
                checkOpcionContable = (arrayCheckContable[i].id).replace('check_factura_','');
                checkOpcionContable = checkOpcionContable.replace('_'+cont,'');
                break;
            }
        }

        if (idInventarioFactura == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticuloFactura_'+cont).focus(); },20); return; }
        else if(cantArticuloFactura <= 0 || cantArticuloFactura == '' || isNaN(cantArticuloFactura)){
            document.getElementById('cantArticuloFactura_'+cont).blur();
            alert('El campo Cantidad es numerico obligatorio');
            setTimeout(function(){ document.getElementById('cantArticuloFactura_'+cont).focus(); },80);
            return;
        }
        else if(costoArticuloFactura <= 0 || isNaN(costoArticuloFactura)){ alert('El campo costo es obligatorio'); setTimeout(function(){document.getElementById('costoArticuloFactura_'+cont).focus(); },20); return; }

        if(isNaN(descuentoArticuloFactura)){
            alert('El campo descuento debe ser numerico');
            setTimeout(function(){ document.getElementById('descuentoArticuloFactura_'+cont).focus(); },20);
            return;
        }
        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertArticulo > 0){
            opc       = 'actualizaArticuloFactura';
            divRender = 'renderArticuloFactura_'+cont;
            accion    = 'actualizar';
        }
        else{
            contArticulosFactura++;
            divRender = 'bodyDivArticulosFactura_'+contArticulosFactura;
            var div   = document.createElement('div');
            div.setAttribute('id','bodyDivArticulosFactura_'+contArticulosFactura);
            div.setAttribute('class','bodyDivArticulosFactura');
            document.getElementById('DivArticulosFactura').appendChild(div);
        }

        Ext.get(divRender).load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                      : opc,
                checkOpcionContable      : checkOpcionContable,
                consecutivoFactura       : contArticulosFactura,
                contFactura              : cont,
                idInsertArticulo         : idInsertArticulo,
                idInventarioFactura      : idInventarioFactura,
                cantArticuloFactura      : cantArticuloFactura,
                tipoDesc                 : tipoDesc,
                descuentoArticuloFactura : descuentoArticuloFactura,
                costoArticuloFactura     : costoArticuloFactura,
                iva                      : iva,
                idFactura                : '<?php echo $id_factura_compra; ?>',
            }
        });
        calcularValoresFactura(cantArticuloFactura,descuentoArticuloFactura,costoArticuloFactura,accion,tipoDesc,iva,cont);     //FUNCION TOTAL
    }

    function deleteArticuloFactura(cont){
        //antes de eliminar tomamos las variable para enviarlas a la funcion para recalcular los totales
        var idArticuloFactura        = document.getElementById('idInsertArticuloFactura_'+cont).value;
        var cantArticuloFactura      = document.getElementById('cantArticuloFactura_'+cont).value;
        var descuentoArticuloFactura = document.getElementById('descuentoArticuloFactura_'+cont).value;
        var costoArticuloFactura     = document.getElementById('costoArticuloFactura_'+cont).value;
        var iva                      = document.getElementById('ivaArticuloFacturaCompra_'+cont).value;
        var tipoDesc                 = '';

        if (document.getElementById('imgDescuentoArticulo_'+cont).getAttribute('src') == 'img/porcentaje.png') { tipoDesc='porcentaje';}
        else{ tipoDesc='pesos'; }

        if(confirm('Esta Seguro de eliminar este articulo de la factura de compra?')){

            Ext.get('renderArticuloFactura_'+cont).load({
                url     : 'facturacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                  : 'deleteArticuloFactura',
                    idArticuloFactura    : idArticuloFactura,
                    contFactura          : cont,
                    idFactura            : '<?php echo $id_factura_compra; ?>'
                }
            });
            calcularValoresFactura(cantArticuloFactura,descuentoArticuloFactura,costoArticuloFactura,'eliminar',tipoDesc,iva,cont);
        }
    }

    //======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA =============================//
    function ventanaDescripcionArticuloFactura(cont){
        var height      = 270
        ,   title       = 'Editar Observacion'
        ,   idArticulo  = document.getElementById('idArticuloFactura_'+cont).value
        ,   idInsert    = document.getElementById('idInsertArticuloFactura_'+cont).value;


        // if(document.getElementById('check_factura_costo_'+cont)){
        //     if(document.getElementById('check_factura_costo_'+cont).checked && document.getElementById('divImageSaveFactura_'+cont).style.display == 'none'){
        //         height = 300;
        //         title  = 'Editar Configuracion';
        //     }
        // }
        // if(document.getElementById('check_factura_gasto_'+cont)){
        //     if(document.getElementById('check_factura_gasto_'+cont).checked && document.getElementById('divImageSaveFactura_'+cont).style.display == 'none'){
        //         height = 300;
        //         title  = 'Editar Configuracion';
        //     }
        // }

        Win_Ventana_descripcion_Articulo_factura = new Ext.Window({
            width       : 400,
            height      : 350,
            id          : 'Win_Ventana_descripcion_Articulo_factura',
            title       : 'Editar Configuracion',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc           : 'ventanaDescripcionArticulofactura',
                    idArticulo    : idArticulo,
                    idInsert      : idInsert,
                    cont          : cont,
                    idFactura     :'<?php echo $id_factura_compra; ?>',
                    filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Guardar',
                    scale       : 'large',
                    iconCls     : 'guardar',
                    iconAlign   : 'top',
                    handler     : function(){ btnGuardarDescripcionArticuloFactura(cont,idArticulo,idInsert); }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_descripcion_Articulo_factura.close(idArticulo) }
                }
            ]
        }).show();
    }

    function btnGuardarDescripcionArticuloFactura(cont,idArticulo,idInsert){
        var idCentroCostos = document.getElementById("id_ccos_fc").value
        ,   idImpuesto     = document.getElementById("id_impuestoItem_fc").value
        ,   observacion    = document.getElementById("observacionArticuloFactura_"+cont).value;

        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacionFactura_'+cont).load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc            : 'guardarDescripcionArticuloFactura',
                cont           : cont,
                idInventario   : idArticulo,
                idInsert       : idInsert,
                idCentroCostos : idCentroCostos,
                idImpuesto     : idImpuesto,
                observacion    : observacion,
                idFactura      : '<?php echo $id_factura_compra; ?>',
                filtro_bodega  : document.getElementById("filtro_ubicacion_facturacion_compras").value
            }
        });
    }

    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO =====================//
    function retrocederArticuloFactura(cont){
        //capturamos el id que esta asignado en la variable oculta
        id_actual = document.getElementById("idInsertArticuloFactura_"+cont).value;

        Ext.get('renderArticuloFactura_'+cont).load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc        : 'retrocederArticuloFactura',
                cont       : cont,
                idArticulo : id_actual,
                idFactura  : '<?php echo $id_factura_compra; ?>'
            }
        });
    }

    //========================= FUNCION PARA CALCULAR LOS TOTALES DE LA FACTURA DE COMPRA ==============================//
    /*
        ->subtotal      = (suma de (cantidad * costo) de cada uno de los articulos)
        ->iva           = ((suma de ivas de todos los articulos)*(subtotal-descuento))/100
        ->retefuente    = ((suma de las retenciones de la factura)*(subtotal-descuento))/100
        ->total         = (subtotal-descuento) + iva + retefuente
    */
    function calcularValoresFactura(cantidad,descuento,costo,accion,tipoDesc,iva,cont){
      if(!document.getElementById('contenedor_totales_facturas_compras')){ return; }

      var subtotal          = 0
      ,   valor_iva         = 0
      ,   descuentoTotal    = 0
      ,   descuentoMostrar  = 0
      ,   subtotal_anterior = subtotalFacturaCompra
      ,   iva_anterior      = ivaFacturaCompra
      ,   total_anterior    = totalFacturaCompra;

      subtotal = (cantidad * costo);

      if(tipoDesc == 'porcentaje'){ subtotal = subtotal - (subtotal * descuento / 100); } // DESCUENTO POR ARTICULO ITEM
      else if(tipoDesc == 'pesos'){ subtotal = subtotal - descuento; }

      if(iva > 0){
        valor_iva = (parseFloat(arrayIvaFacturaCompra[iva].valor) * parseFloat(subtotal)) / 100; //IVA NETO
      }
      else{
        valor_iva = 0; //IVA NETO
        iva       = 0;
      }

      if(accion == 'agregar'){
        subtotalFacturaCompra = (parseFloat(subtotalFacturaCompra) + parseFloat(subtotal));   // ACUMULADOR SUBTOTAL
        ivaFacturaCompra      = parseFloat(ivaFacturaCompra) + parseFloat(valor_iva);         // ACUMULADOR IVA

        //SI EL OBJETO SALDO EN EL ARRAY DEL IVA NO EXISTE, CREAR EL CAMPO SALDO CON EL PRIMER VALOR
        if(typeof(arrayIvaFacturaCompra[iva].saldo) == 'undefined'){
          arrayIvaFacturaCompra[iva].saldo = valor_iva;
        }
        //SI YA EXISTE EL CAMPO SALDO EN EL OBJETO, ENTONCES ACUMULAR EL VALOR
        else{
          arrayIvaFacturaCompra[iva].saldo = arrayIvaFacturaCompra[iva].saldo + valor_iva;
        }

        document.getElementById("costoTotalArticuloFactura_" + cont).value = subtotal;
      }
      else if(accion == 'eliminar'){
        subtotalFacturaCompra = parseFloat(subtotalFacturaCompra) - parseFloat(subtotal);   // ACUMULADOR SUBTOTAL
        ivaFacturaCompra      = parseFloat(ivaFacturaCompra) - parseFloat(valor_iva);       // ACUMULADOR IVA

        //SI EL OBJETO SALDO EN EL ARRAY DEL IVA EXISTE, RESTAR EL VALOR DEL IVA
        if(typeof(arrayIvaFacturaCompra[iva].saldo) != 'undefined'){
          arrayIvaFacturaCompra[iva].saldo -= valor_iva;
        }
      }

      //RECORRER EL ARRAY DE LOS IVA Y ARMAR ELEMENTOS PARA EL DOM
      var labelIva   = ''
      ,   simboloIva = ''
      ,   valoresIva = '';

      for(var id_iva in arrayIvaFacturaCompra){
        if(typeof(arrayIvaFacturaCompra[id_iva].saldo) != 'undefined'){
          if(arrayIvaFacturaCompra[id_iva].saldo > 0){
            labelIva   += '<div style=\"margin-bottom:5px; overflow:hidden; width:100%; padding-left:3px; font-weight:bold; overflow:hidden;margin-bottom:5px;\"><div class=\"labelNombreRetencion\">'+arrayIvaFacturaCompra[id_iva].nombre+'</div><div class=\"labelValorRetencion\">('+(arrayIvaFacturaCompra[id_iva].valor*1)+'%)</div></div>';
            simboloIva += '<div style=\"margin-bottom:5px\">$</div>';
            valoresIva += '<div style=\"margin-bottom:5px\" title=\"'+formato_numero(arrayIvaFacturaCompra[id_iva].saldo, "<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'\" >'+formato_numero(arrayIvaFacturaCompra[id_iva].saldo, "<?php echo $_SESSION['DECIMALESMONEDA'] ?>", '.', ',')+'</div>';
          }
        }
      }

      //CAMBIAR LOS VALORES AUTOMATICOS POR LOS MANUALES
      if(contabilidad_manual == 'true'){
        subtotalFacturaCompra = subtotal_manual;
        ivaFacturaCompra      = iva_manual;
        totalFacturaCompra    = total_manual;
      }

      // CALCULO DE RETENCIONES
      var contador              = 0
      ,   retenciones           = document.querySelectorAll('.capturarCheckboxAcumuladoFacturaCompra')
      ,   labelRetenciones      = document.querySelectorAll('.capturaLabelAcumuladoFacturaCompra')
      ,   id_retencion          = 0
      ,   valorRetencion        = 0
      ,   listadoRetenciones    = ''
      ,   simboloRetencion      = ''
      ,   valoresRetenciones    = ''
      ,   divValoresRetenciones = '';

      // CIClO PARA RECORRER LOS CHECKBOKS DE RETENCIONES
      for(i in retenciones){
        if(typeof(retenciones[i].id) != 'undefined'){
          id_retencion = (retenciones[i].id).split('_')[1];

          if(objectRetenciones_FacturaCompra[id_retencion].tipo_retencion == 'ReteIva'){
            if(objectRetenciones_FacturaCompra[id_retencion].base > ivaFacturaCompra){
              continue;
            }
            valorRetencion     += (parseFloat(ivaFacturaCompra) * objectRetenciones_FacturaCompra[id_retencion].valor) / 100;
            valoresRetenciones = formato_numero((parseFloat(ivaFacturaCompra) * objectRetenciones_FacturaCompra[id_retencion].valor) / 100,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
          }
          else if(objectRetenciones_FacturaCompra[id_retencion].tipo_retencion == 'AutoRetencion'){
            continue;
          }
          else{
            if (objectRetenciones_FacturaCompra[id_retencion].base>subtotalFacturaCompra) {continue;}
            valorRetencion    += ((parseFloat(subtotalFacturaCompra) * objectRetenciones_FacturaCompra[id_retencion].valor) / 100);
            valoresRetenciones = formato_numero((parseFloat(subtotalFacturaCompra)* objectRetenciones_FacturaCompra[id_retencion].valor)/100,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
          }
          listadoRetenciones    += '<div style="margin-bottom:5px; overflow:hidden; width:100%;">'+labelRetenciones[i].innerHTML+'</div>';
          simboloRetencion      += '<div style="margin-bottom:5px">$</div>';
          divValoresRetenciones += '<div style="margin-bottom:5px">'+valoresRetenciones+'</div>';
        }
      }

      totalFacturaCompra = (parseFloat(subtotalFacturaCompra.toFixed(<?php echo $_SESSION['DECIMALESMONEDA']; ?>)) - parseFloat(valorRetencion.toFixed(<?php echo $_SESSION['DECIMALESMONEDA']; ?>))) + parseFloat(ivaFacturaCompra.toFixed(<?php echo $_SESSION['DECIMALESMONEDA']; ?>));

      //renderizamos los valores en la ventana
      document.getElementById("subtotalFacturaCompra").innerHTML           = formato_numero(subtotalFacturaCompra,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
      document.getElementById("divRetencionesFacturaCompra").style.display = 'inline';
      document.getElementById("idretencionFacturaCompra").innerHTML        = listadoRetenciones;
      document.getElementById("simboloRetencionFacturaCompra").innerHTML   = simboloRetencion;
      document.getElementById("retefuenteFacturaCompra").innerHTML         = divValoresRetenciones;
      document.getElementById('labelIvaFacturaCompra').innerHTML           = labelIva;
      document.getElementById('simboloIvaFacturaCompra').innerHTML         = simboloIva;
      document.getElementById('ivaFacturaCompra').innerHTML                = valoresIva;
      document.getElementById("totalFacturaCompra").innerHTML              = formato_numero(totalFacturaCompra,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');

      if(contabilidad_manual == 'true'){
        if(subtotal_manual > 0 && total_manual > 0){
          document.getElementById("subtotalFacturaCompra").innerHTML = subtotal_manual;
          document.getElementById("labelIvaFacturaCompra").innerHTML = "Iva";
          document.getElementById("ivaFacturaCompra").innerHTML      = iva_manual;
          document.getElementById("totalFacturaCompra").innerHTML    = total_manual;
        }
        subtotalFacturaCompra = subtotal_anterior;
        ivaFacturaCompra      = iva_anterior;
        totalFacturaCompra    = total_anterior;
      }
    }

    function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
        numero = parseFloat(numero);
        if(isNaN(numero)){ return ''; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); }            // Redondeamos

        numero = numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');               // Convertimos el punto en separador_decimal

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles = new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) { numero=numero.replace(miles, '$1' + separador_miles + '$2'); }
        }
        return numero;
    }

    //===================================== FINALIZAR 'CERRAR' LA FACTURA DE LA COMPRA ===================================//
    function guardarFacturaCompra(){
        var validacion     = validarArticulosFactura()
        ,   prefijoFactura = document.getElementById('prefijoFactura').value
        ,   numeroFactura  = document.getElementById('numeroFactura').value
        ,   idPlantilla    = document.getElementById('plantilla_compra').value
        ,   idCuentaPago   = document.getElementById('selectCuentaPagoCompra').value
        ,   nitProveedor   = document.getElementById('nitProveedorFactura').value;

        if (document.getElementById("tipo_documento").value=='05' && document.getElementById("resolucion").value==''){
            alert("se selecciono la factura como documento soporte pero no se selecciono la resolucion"); return;
        }

        if(idCuentaPago == 0 && idPlantilla==0){ alert("Seleccione una cuenta de pago o plantilla en la presente factura de compra!"); return; }
        else if (validacion==0) { alert("No hay articulos por guardar en la presente factura de compra!"); return;}
        else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return;}
        else if (numeroFactura.length==0 || numeroFactura == 0 ) { alert("El campo numero de factura es obligatorio!"); return;}
        else if (validacion== 2 || validacion== 0) {
        cargando_documentos('Generando Factura de Compra...','');
            idBodega    = document.getElementById("filtro_ubicacion_facturacion_compras").value;
            observacion = document.getElementById("observacionFacturaCompra").value;
            observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

            Ext.get('render_btns_factura_compra').load({
                url     : 'facturacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc            : 'terminarFacturaCompra',
                    prefijoFactura : prefijoFactura,
                    numeroFactura  : numeroFactura,
                    idFactura      : '<?php echo $id_factura_compra; ?>',
                    idBodega       : idBodega,
                    observacion    : observacion,
                    idPlantilla    : idPlantilla,
                    idProveedor    : id_proveedor_factura,
                    nitProveedor   : nitProveedor
                }
            });
        }
    }

    //======================================== BUSCAR UNA FACTURA DE COMPRA ================================================//
    function buscarFacturaCompra(){
        var validacion= validarArticulosFactura();
        if (validacion==1) {
            if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")) { ventanaBuscarFacturaCompra(); }
        }
        else if (validacion== 2 || validacion== 0) { ventanaBuscarFacturaCompra(); }
    }

    function ventanaBuscarFacturaCompra(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_factura_compra = new Ext.Window({

            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_factura_compra',
            title       : 'Seleccionar factura de compra',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/grilla_buscar_factura_compra.php',
                scripts : true,
                nocache : true,
                params  : { filtro_bodega   : document.getElementById("filtro_ubicacion_facturacion_compras").value }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_buscar_factura_compra.close(id) }
                },{
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Documentos',
                    style   : 'border-right:none;',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 150,
                            height      : 46,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : 'facturacion/bd/bd.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    opc         : 'filtro_tipo_doc',
                                    filtro_bodega   : document.getElementById("filtro_ubicacion_facturacion_compras").value
                                }
                            }
                        }
                    ]
                }
            ]
        }).show();
    }

    //================================== VALIDACION NUMERICA EN CANTIDAD Y DESCUENTO ===================================//
    function validarNumberArticuloFactura(event,input,typeValidate,cont){
        var valueInput  = input.value
        ,   idInput     = (input.id).split('_')[0]
        ,   contIdInput = (input.id).split('_')[1];

        numero = input.value;
        tecla  = input ? event.keyCode : event.which;

        if(tecla == 13){
            if(idInput == 'cantArticuloFactura'){

                var id_insert = document.getElementById('idInsertArticuloFactura_'+contIdInput).value;
                if (typeof(objDocumentosCruceFacturaCompra[id_insert])!='undefined') {
                    if((objDocumentosCruceFacturaCompra[id_insert].saldo_cantidad * 1)< (numero*1)){
                        input.blur();
                        alert('la cantidad no puede ser mayor a la ya registrada en el documento '+objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].typeDoc);
                        input.value = objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].saldo_cantidad;
                        setTimeout(function(){ input.focus(); },100);
                    }
                }
                // if(arrayOrdenesSaldos[id_insert] < numero){
                //     input.blur();
                //     alert('la cantidad no puede ser mayor a la ya registrada en la Orden de Compra');
                //     input.value = arrayOrdenesSaldos[id_insert];
                //     setTimeout(function(){ input.focus(); },100);
                // }
                else{ document.getElementById('descuentoArticuloFactura_'+contIdInput).focus(); }
            }
            else if(idInput == 'descuentoArticuloFactura'){ document.getElementById('costoArticuloFactura_'+contIdInput).focus(); }
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(valueInput)){
            valueInput  = valueInput.replace(patron,'');
            input.value = valueInput;
        }
        else if(isNaN(valueInput)){ input.value = valueInput.substring(0, valueInput.length-1); }
        else{
            document.getElementById('divImageSaveFactura_'+contIdInput).style.display  = 'inline';
            if(document.getElementById('idInsertArticuloFactura_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer_'+contIdInput).style.display = 'inline';
            }
        }
        return true;
    }

    //================================== VALIDACION NUMERICA EN CANTIDAD Y DESCUENTO ===================================//
    function validarNumberArticulo<?php echo $opcGrillaContable; ?>(event,input,typeValidate,cont){
        var contIdInput = (input.id).split('_')[1];
        var nombreInput = (input.id).split('_')[0];

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;

        if(tecla == 13){
            if(nombreInput == 'cantArticulo<?php echo $opcGrillaContable; ?>'){
                var id_insert = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value;
                //SI EL OBJETO NO EXISTE, SE CARGO NORMALMENTE
                if (typeof(objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert])=='undefined') {
                    ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>');
                    document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
                }
                //SI EL OBJETO EXISTE, Y SUPERA LA CANTIDAD DEL DOCUMENTO CARGADO
                else if((objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].saldo_cantidad * 1)< (numero*1)){
                    input.blur();
                    alert('la cantidad no puede ser mayor a la ya registrada en el documento '+objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].typeDoc);
                    input.value = objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].saldo_cantidad;
                    setTimeout(function(){ input.focus(); },100);
                }
                //SI EL OBJETO EXISTE Y NO SUPERA LA CANTIDAD CARGADA
                else{
                    ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>');
                    document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
                }
            }
            else if(nombreInput == 'descuentoArticulo<?php echo $opcGrillaContable; ?>'){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus(); }
            return true;
        }

        patron = /[^\d.]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
        else{
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display    = 'inline';

            if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
            }
        }
    }

    //=================================== VALIDACION DE LA CANTIDAD DE ARTICULOS EXISTENTES ================================//
    function ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,cantidad,opc){
        var id = document.getElementById("idArticulo"+opc+"_"+cont).value;

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  :
            {
                opc               : 'verificaCantidadArticulo',
                id                : id,
                filtro_bodega     : '<?php echo $filtro_bodega; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            },
            success :function (result, request){
                        var saldoArticulo = result.responseText;
                        if((cantidad *1) > (saldoArticulo*1)){
                            alert("Error!\nLa cantidad ingresada es mayor a la existente\nSolo restan "+saldoArticulo+" unidades");
                            document.getElementById("cantArticulo"+opc+"_"+cont).value='';
                            setTimeout(function(){document.getElementById("cantArticulo"+opc+"_"+cont).focus();},100);
                        }
                        else if((cantidad *1) < saldoArticulo){ document.getElementById("descuentoArticulo"+opc+"_"+cont).focus(); }
                        else if(saldoArticulo=='false'){ alert("Error!\nSe produjo un problema con la validacion\nNo se verifico la cantidad del Articulo\nSi el problema persiste comuniquese con el administrador del sistema"); }
                    },
            failure : function(){ alert('Error de conexion con el servidor'); }
        });
    }

    //========================================= IMPRIMIR FACTURA DE COMPRA ====================================================//
    function imprimirFacturaCompra (){
        window.open("facturacion/bd/imprimir_factura_compra.php?id="+'<?php echo $id_factura_compra; ?>');
    }

    function imprimirFacturaCompraExcel (){
        window.open("facturacion/bd/exportar_excel_factura_compra.php?id="+'<?php echo $id_factura_compra; ?>');
    }

    //============================  VALIDAR QUE NO HAYA NINGUN ARTICULO POR GUARDAR O POR ACTULIZAR ===========================//
    function validarArticulosFactura(){
        var cont = 0
        ,   contTotal = 0
        ,   contArticulo
        ,   nameArticulo
        ,   divsArticulosFactura = document.querySelectorAll(".bodyDivArticulosFactura")
        ,   conArticulos = divsArticulosFactura.length;

        for(i=0;i<conArticulos;i++){
            // if(typeof(divsArticulosFactura[i].id)!='undefined'){

                contTotal++;

                nameArticulo = (divsArticulosFactura[i].id).split('_')[0]
                contArticulo = (divsArticulosFactura[i].id).split('_')[1]
                if(     document.getElementById('idArticuloFactura_'+contArticulo).value > 0
                    &&  document.getElementById('imgSaveFactura_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                    ||  document.getElementById('imgSaveFactura_'+contArticulo).getAttribute('src') == 'img/reload.png'
                    &&  document.getElementById('divImageSaveFactura_'+contArticulo).style.display == 'inline')
                    { cont++; }
            // }
        }
        if (contTotal==1 && document.getElementById('idInsertArticuloFactura_'+contArticulo).value > 0) {contTotal++;}

        if(contTotal==1 || contTotal == 0){ return 0; }
        else if(cont > 0){ return 1; }
        else { return 2; }
    }

    function guardaFechaFactura(inputDate){
        var idInputDate  = inputDate.getEl().id
        ,   valInputDate = inputDate.value;

        Ext.Ajax.request({
            url     : 'facturacion/bd/bd.php',
            params  :
            {
                opc          : 'guardarFechaFactura',
                idInputDate  : idInputDate,
                valInputDate : valInputDate,
                idFactura    : '<?php echo $id_factura_compra; ?>'
            },
            success :function (result, request){
                        if(result.responseText.replace(/ /g, "") == 'true'){
                            if(idInputDate=='fechaFactura'){ fecha_inicio=valInputDate; }
                            else if(idInputDate=='fechaFinalFactura'){ fecha_final=valInputDate; }
                        }
                        else{
                            if(idInputDate=='fechaFactura'){ document.getElementById(idInputDate).value= fecha_inicio; }
                            else if(idInputDate=='fechaFinalFactura'){ document.getElementById(idInputDate).value= fecha_final; }
                            alert(result.responseText+'No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                        }
                    },
            failure : function(){
                        if(idInputDate=='fechaFactura'){ document.getElementById(idInputDate).value= fecha_inicio; }
                        else if(idInputDate=='fechaFinalFactura'){ document.getElementById(idInputDate).value= fecha_final; }
                        alert('Error de conexion con el servidor');
                    }
        });
    }

    //================================ CANCELA LA FACTURA DE COMPRA ====================================//
    function cancelarFacturaCompra(){
        var contArticulos = 0;
        if(!document.getElementById('DivArticulosFactura')){ return; }

        arrayIdsArticulos = document.getElementById('DivArticulosFactura').querySelectorAll('.classInputInsertArticuloFactura');
        for(i in arrayIdsArticulos){
            if(arrayIdsArticulos[i].value > 0){ contArticulos++; }
        }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar la presente Factura de compra y su contenido relacionado')){
                cargando_documentos('Cancelando Factura de Compra...','');
                Ext.get('render_btns_factura_compra').load({
                    url     : 'facturacion/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc       : 'cancelarFacturaCompra',
                        idFactura : '<?php echo $id_factura_compra; ?>',
                        idBodega  : '<?php echo $filtro_bodega ?>'
                    }
                });
            };
        }
    }

    function abrirVentanaUpdateValoresFacturaCompra(){

        if (subtotalFacturaCompra<=0) { alert("No hay elementos agregados en la presente factura!"); return; }

        var iva      = ivaFacturaCompra.toFixed(0)
        ,   total    = totalFacturaCompra.toFixed(0)
        ,   subtotal = subtotalFacturaCompra.toFixed(0);

         Win_Ventana_update_valores_FacturaCompra = new Ext.Window({
            width       : 545,
            height      : 300,
            id          : 'Win_Ventana_update_valores_FacturaCompra',
            title       : 'Cambie los valores de la factura',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc       : 'abrirVentanaUpdateValores',
                    subtotal  : subtotal,
                    iva       : iva,
                    total     : total,
                    idFactura : '<?php echo $id_factura_compra; ?>',
                }
            },
            tbar        :
            [
                {
                    xtype     : 'button',
                    text      : 'Guardar',
                    scale     : 'large',
                    iconCls   : 'guardar',
                    width     : 60,
                    height    : 56,
                    iconAlign : 'top',
                    id        : 'btn_guardar_contabilidad_manual',
                    handler   : function(){ cambiar_valores_factura('') }
                },
                {
                    xtype     : 'button',
                    text      : 'Cancelar',
                    scale     : 'large',
                    iconCls   : 'no',
                    width     : 60,
                    height    : 56,
                    iconAlign : 'top',
                    id        : 'btn_cancelar_contabilidad_manual',
                    hidden    : true,
                    handler   : function(){ cambiar_valores_factura('cancelar') }
                },
                {
                    xtype     : 'button',
                    text      : 'Regresar',
                    scale     : 'large',
                    iconCls   : 'regresar',
                    width     : 60,
                    height    : 56,
                    iconAlign : 'top',
                    id        : 'btn_regresar_contabilidad_manual',
                    handler   : function(){ Win_Ventana_update_valores_FacturaCompra.close(id) }
                }
            ]
        }).show();
    }

    function cambiar_valores_factura(accion){
        var id_cuenta_total      = 0;
        var id_cuenta_niif_total = 0;

        if (accion=='cancelar') {
            var opc                     = 'cancelarValoresFacturaCompra'
            ,   subtotal                = 0
            ,   iva                     = 0
            ,   total                   = 0
            ,   id_centro_costos        = 0
            ,   id_cuenta_subtotal      = 0
            ,   id_cuenta_niif_subtotal = 0
            ,   id_cuenta_iva           = 0
            ,   id_cuenta_niif_iva      = 0;

        }else{
            var opc                     = 'guardarValoresFacturaCompra'
            ,   subtotal                = document.getElementById('subtotalEditado').value
            ,   iva                     = document.getElementById('ivaEditado').value
            ,   total                   = document.getElementById('totalEditado').value
            ,   id_centro_costos        = document.getElementById('id_ccos_fc').value
            ,   id_cuenta_subtotal      = document.getElementById('idCuentaSubtotalEditado').value
            ,   id_cuenta_niif_subtotal = document.getElementById('idCuentaNiifSubtotalEditado').value
            ,   id_cuenta_iva           = document.getElementById('idCuentaIvaEditado').value
            ,   id_cuenta_niif_iva      = document.getElementById('idCuentaNiifIvaEditado').value;
            // ,   id_cuenta_total         = document.getElementById('idCuentaTotalEditado').value
            // ,   id_cuenta_niif_total    = document.getElementById('idCuentaNiifTotalEditado').value;

            if (subtotal=='' || subtotal==0) {alert("El campo valor de subtotal es obligatorio"); return;}
            if (total=='' || total==0) {alert("El campo valor de total es obligatorio"); return;}

            if (id_cuenta_subtotal=='' || id_cuenta_subtotal==0) { alert("El campo cuenta subtotal es obligatorio"); return; }
            if (id_cuenta_niif_subtotal=='' || id_cuenta_niif_subtotal==0) { alert("El campo cuenta niif subtotal  es obligatorio"); return; }
            if (id_cuenta_iva=='' || id_cuenta_iva==0) { alert("El campo cuenta iva es obligatorio"); return; }
            if (id_cuenta_niif_iva=='' || id_cuenta_niif_iva==0) { alert("El campo cuenta niif iva es obligatorio"); return; }
            // if (id_cuenta_total=='' || id_cuenta_total==0) { alert("El campo cuenta total es obligatorio"); return; }
            // if (id_cuenta_niif_total=='' || id_cuenta_niif_total==0) { alert("El campo cuenta niif total es obligatorio"); return; }
        }

        Ext.get('loadValidaUpdatefecha').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                     : opc,
                idFactura               : '<?php echo $id_factura_compra; ?>',
                subtotal                : subtotal,
                iva                     : iva,
                total                   : total,
                id_centro_costos        : id_centro_costos,
                id_cuenta_subtotal      : id_cuenta_subtotal,
                id_cuenta_niif_subtotal : id_cuenta_niif_subtotal,
                id_cuenta_iva           : id_cuenta_iva,
                id_cuenta_niif_iva      : id_cuenta_niif_iva,
                id_cuenta_total         : id_cuenta_total,
                id_cuenta_niif_total    : id_cuenta_niif_total,
            }
        });
    }

    function ventana_centros_costos_fc(){
        Win_Ventana_Ccos_fc = new Ext.Window({
            width       : 600,
            height      : 450,
            id          : 'Win_Ventana_Ccos_fc',
            title       : 'Seleccione el Centro de Costo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/centro_costos.php',
                scripts : true,
                nocache : true,
                params  : { }
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
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_Ccos_fc.close() }
                        }
                    ]
                }
            ]
        }).show();
    }

    //================================== ANTICIPOS DE FACTURAS ==================================//
    function ventanaAnticipo_<?php echo $opcGrillaContable; ?>(){

        if(!document.getElementById("total<?php echo $opcGrillaContable; ?>")){ alert('Aviso,\nSolo se puede agregar el anticipo cuando el total de la factura es superior a cero.'); return; }

        var divTotalFactura = document.getElementById("total<?php echo $opcGrillaContable; ?>").innerHTML;
        divTotalFactura     = divTotalFactura.replace(/[^\d]/g, '');

        if(isNaN(divTotalFactura) || divTotalFactura == 0){ alert('Aviso,\nSolo se puede agregar el anticipo cuando el total de la factura es superior a cero.'); return; }

        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_cuenta_anticipo_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho - 100,
            height      : 400,
            id          : 'Win_Ventana_cuenta_anticipo_<?php echo $opcGrillaContable; ?>',
            title       : 'Configuracion de Anticipos',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
            items       :
            [
                {
                    closable    : false,
                    border      : false,
                    autoScroll  : true,
                    iconCls     : '',
                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                    items       :
                    [

                        {
                            xtype       : "panel",
                            id          : 'contenedor_cuenta_anticipo_<?php echo $opcGrillaContable; ?>',
                            border      : false,
                            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                        }
                    ]
                }
            ],
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Anticipos',
                    style   : 'border-right:none;',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 150,
                            height      : 46,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : 'facturacion/bd/bd.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    opc         : 'filtro_anticipo',
                                    idProveedor : id_proveedor_factura,
                                    opcGrilla   : 'anticipo_<?php echo $opcGrillaContable; ?>',
                                    idFactura   : '<?php echo $id_factura_compra ?>'
                                }
                            }
                        }
                    ]
                },'-',
                {
                    xtype       : 'button',
                    id          : 'Btn_cancelar_anticipo_<?php echo $opcGrillaContable; ?>',
                    width       : 60,
                    height      : 56,
                    text        : 'Eliminar Anticipos',
                    scale       : 'large',
                    iconCls     : 'cancel',
                    iconAlign   : 'top',
                    handler     : function(){ cancelar_anticipo_<?php echo $opcGrillaContable; ?>(); }
                },
                {
                    xtype     : 'button',
                    text      : 'Regresar',
                    scale     : 'large',
                    iconCls   : 'regresar',
                    width     : 60,
                    height    : 56,
                    iconAlign : 'top',
                    handler   : function(){ Win_Ventana_cuenta_anticipo_<?php echo $opcGrillaContable; ?>.close(id); }
                },'->',
                {
                    xtype       : "tbtext",
                    text        : '<div style="margin:10px;">'
                                        +'<div style="font-weight:bold; font-size:15px;">TOTAL ANTICIPO</div>'
                                        +'<div id="total_anticipo_<?php echo $opcGrillaContable; ?>" style="font-weight:bold; font-size:13px; margin-top:5px;"></div>'
                                    +'</div>',
                    scale       : "large",
                }
            ]
        }).show();
    }

    //VENTANA PARA CONFIGURAR LAS RETENCIONES DEL DOCUMENTO
    function ventanaConfigurarRetencionesFacturaCompra(){

        Win_Ventana_configRetenciones_FacturaCompra = new Ext.Window({
            width       : 800,
            height      : 450,
            id          : 'Win_Ventana_configRetenciones_FacturaCompra',
            title       : 'Seleccione las retenciones',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/configuracion_retenciones.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable    : 'FacturaCompra',
                    modulo               : 'Compra',
                    id_documento         : '<?php echo $id_factura_compra; ?>',
                    tabla_retenciones    : 'compras_facturas_retenciones',
                    id_tabla_retenciones : 'id_factura_compra',
                    ejecutaFuncion       : 'checkboxRetenciones',
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
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_configRetenciones_FacturaCompra.close() }
                        },
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Modificar base',
                            scale       : 'large',
                            iconCls     : 'edit',
                            iconAlign   : 'top',
                            handler     : function(){ wizard_modifica_base() }
                        },
                    ]
                }
            ]
        }).show();
    }
    	// VENTANA DE CONFIGURACION DEL ARCHIVO PLANO
	function wizard_modifica_base() {
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_wizard = new Ext.Window({
		   	width       : 480,
			height      : 250,
		    id          : 'Win_Ventana_wizard_modifica_base_retenciones',
		    title       : 'Modificar base retenciones',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'facturacion/wizard_modifica_base_retenciones.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            id_factura_compra : '<?php echo $id_factura_compra ?>'
		        }
		    }
		}).show();
	}
    //BUSCAR LA CUENTA COLGAAP DE LOS VALORES MANUALES
    function ventana_buscar_cuenta(opc,nombreCampoId,nombreCampo) {
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();
        var title   = 'Colgaap';

        if (opc=='niif') { title='Niif'; }
        else{ title='Colgaap'; }

        Win_Ventana_buscar_cuenta_contabilidad_manual = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_cuenta_contabilidad_manual',
            title       : 'Buscar cuenta '+title,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/bd/buscarCuentaPuc.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : opc,
                    cargaFuncion      : 'renderizaResultadoVentanaCuentas("'+opc+'",id,"'+nombreCampoId+'","'+nombreCampo+'");',
                    opcGrillaContable : 'grillaBuscarCuenta_'+opc,
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
                            handler     : function(){ Win_Ventana_buscar_cuenta_contabilidad_manual.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    //PROCESAR LOS DATOS DE LA VENTANA DE BUSQUEDA DE LAS CUENTAS
    function renderizaResultadoVentanaCuentas(opc,id,nombreCampoId,nombreCampo) {
        // console.log("opc "+opc+" id "+id );
        var cuenta = document.getElementById('div_grillaBuscarCuenta_'+opc+'_cuenta_'+id).innerHTML;
        if (cuenta.length<6) { alert("Debe seleccionar una cuenta a partir de 6 digitos"); return; }
        document.getElementById(nombreCampoId).value   = id;
        document.getElementById(nombreCampo).innerHTML = cuenta;

        Win_Ventana_buscar_cuenta_contabilidad_manual.close(id)
    }

    //SINCRONIZAR LA CUENTA NIIF CON LA COLGAAP QUE SELECCIONO EN LA VENTANA DE EDITAR VALORES DE LA FACTURA DE FORMA MANUAL
    function sincronizar_cuenta_niif(campoIdColgaap,campoIdNiif,campoNiif) {
        var id_colgaap = document.getElementById(campoIdColgaap).value;
        if (id_colgaap==0 || id_colgaap=='') { alert("Debe seleccionar primero la cuenta colgaap"); return; }

        Ext.get('loadValidaUpdatefecha').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            text    : 'Buscando Cuenta Niif...',
            params  :
            {
                opc               : 'sincronizarCuentaNiif',
                id_cuenta_colgaap : id_colgaap,
                campoIdNiif       : campoIdNiif,
                campoNiif         : campoNiif,
            }
        });
    }

    function editarContabilidadFacturaCompra(){
        var validacion = validarArticulosFactura();
        if (validacion == 1 ){ if( !confirm("Hay items pendientes por guardar!\nDesea continuar?") ){ return; } }

        Ext.get("contenedor_facturacion_compras").load({
            url     : "facturacion_cuentas/grilla/grillaContable.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opcGrillaContable : 'FacturaCompra',
                id_factura_compra : '<?php echo $id_factura_compra ?>',
                editarFacturaItem : 'true'
            }
        });

        document.getElementById('titleDocuementoFacturaCompra').innerHTML='';
    }

    function ventanaDocumentosCruceFacturaCompra() {

        Win_ventanaDocumentosCruceFacturaCompra = new Ext.Window({
            width       : 550,
            height      : 500,
            id          : 'Win_ventanaDocumentosCruceFacturaCompra',
            title       : '',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/documentos_adjuntos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    id_factura_compra   : '<?php echo $id_factura_compra; ?>',
                }
            },
        }).show();

    }


</script>
