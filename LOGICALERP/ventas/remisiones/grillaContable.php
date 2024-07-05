<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../funciones_globales/funciones_php/randomico.php");
    include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php");



    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $id_usuario  = $_SESSION['IDUSUARIO'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');
    $exento_iva  = '';

    $sqlFecha     = "SELECT dias_vencimiento FROM ventas_remisiones_configuracion WHERE activo=1 AND id_empresa='$id_empresa'";
    $queryFecha   = mysql_query($sqlFecha,$link);
    $fechaDefault = mysql_result($queryFecha,0,'dias_vencimiento');
    if ($fechaDefault=='') { $fechaDefault='31'; }

    $sql="SELECT valor FROM ventas_remisiones_configuracion WHERE activo=1 AND id_empresa=$id_empresa ";
    $query=mysql_query($sql,$link);
    $tipo_valor = mysql_result($query,0,'valor');
?>
<script>

    //variables para calcular los valores de los costos y totales de la factura

    var subtotalAcumulado<?php echo $opcGrillaContable; ?>   = 0.00
    ,   descuentoAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   descuento<?php echo $opcGrillaContable; ?>           = 0.00
    ,   acumuladodescuentoArticulo                           = 0.00
    ,   ivaAcumulado<?php echo $opcGrillaContable; ?>        = 0.00
    ,   total<?php echo $opcGrillaContable; ?>               = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>       = 1
    ,   id_cliente_<?php echo $opcGrillaContable;?>          = 0;

    var objDocumentosCruce<?php echo $opcGrillaContable; ?> = [];

    arrayIva<?php echo $opcGrillaContable; ?> = []; // ARRAY CON LOS VALORES DE LOS IVAS
    arrayIva<?php echo $opcGrillaContable; ?>[0] = { nombre:"", valor:"" };

    var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoCliente<?php echo $opcGrillaContable; ?>      = 0
    ,   nitCliente<?php echo $opcGrillaContable; ?>         = 0
    ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
    ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

    //Bloqueo todos los botones
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();

    //variable con la fecha del dia mas treinta dias, para cargar por defecto la fecha de vencimiento
    var fechaVencimientoFactura<?php echo $opcGrillaContable;?>  = new Date();
    fechaVencimientoFactura<?php echo $opcGrillaContable;?>.setDate(fechaVencimientoFactura<?php echo $opcGrillaContable;?>.getDate()+parseInt('<?php echo $fechaDefault; ?>'));

</script>
<?php

    $acumScript .= (user_permisos(16,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    $acumScript .= (user_permisos(18,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';       //cancelar

    //si se recibe la variable idConsecutivoCotizacionPedido, se cargara la factura/pedido apartir de una cotizacion/pedido
    if(isset($idConsecutivoCotizacionPedido)){

        //variable con los valores a insertar
        $columnasInsertTablaPrincipal="id_empresa,random,nit,id_cliente,cod_cliente,cliente,fecha_inicio,id_sucursal,id_bodega,observacion,documento_vendedor,nombre_vendedor";

        //verificamos si se carga una cotizacion o un pedido a una factura mediante los dos primeros condicionales
        if ($opcCargar=="cotizacionRemision") {
            $referencia_consecutivo = "Cotizacion";
            $tablaCarga             = "ventas_cotizaciones";
            $idTablaCargar          = "id_cotizacion_venta";
            $tablaCargaInventario   = "ventas_cotizaciones_inventario";
        }
        else if($opcCargar=="pedidoRemision") {
            $referencia_consecutivo = "Pedido";
            $tablaCarga             = "ventas_pedidos";
            $idTablaCargar          = "id_pedido_venta";
            $tablaCargaInventario   = "ventas_pedidos_inventario";
        }

        //consultamos los datos de la tabla a cargar y los insertamos en la factura
        $sqlConsulOrden   = "SELECT id, nit, id_cliente,cod_cliente,cliente,observacion,documento_vendedor,nombre_vendedor FROM $tablaCarga WHERE consecutivo='$idConsecutivoCotizacionPedido' AND id_sucursal='$id_sucursal' AND id_bodega='$filtro_bodega' AND id_empresa='$id_empresa'";
        $queryConsulOrden = mysql_query($sqlConsulOrden,$link);

        $idDocCruce             = mysql_result($queryConsulOrden,0,'id');
        $nit                    = mysql_result($queryConsulOrden,0,'nit');
        $id_cliente_cotizacion  = mysql_result($queryConsulOrden,0,'id_cliente');
        $cod_cliente_cotizacion = mysql_result($queryConsulOrden,0,'cod_cliente');
        $cliente_cotizacion     = mysql_result($queryConsulOrden,0,'cliente');
        $observacion_cotizacion = mysql_result($queryConsulOrden,0,'observacion');
        $documento_vendedor     = mysql_result($queryConsulOrden,0,'documento_vendedor');
        $nombre_vendedor        = mysql_result($queryConsulOrden,0,'nombre_vendedor');

        $id_cliente=$id_cliente_cotizacion;

        // CREACION DEL ID UNICO, Y LE INSERTAMOS lOS VALORES QUE SE VAN A CARGAR
        $random_factura = responseUnicoRanomico();
        //insertar datos de la tabla principal
        $sqlInsertFactura   ="INSERT INTO $tablaPrincipal($columnasInsertTablaPrincipal)VALUES('$id_empresa','$random_factura','$nit','$id_cliente_cotizacion','$cod_cliente_cotizacion','$cliente_cotizacion','$fecha','$id_sucursal','$filtro_bodega','$observacion_cotizacion','$documento_vendedor','$nombre_vendedor')";
        $queryInsertFactura = mysql_query($sqlInsertFactura,$link);

        //verificamos que se inserto en la tabla
        if (!$queryInsertFactura) { '<script>alert("Error!\nSe produjo un error y no se cargo la cotizacion a la factura");</script>'; }

        $sqlSelectIdFactura   = "SELECT id,exento_iva FROM $tablaPrincipal WHERE random='$random_factura' LIMIT 0,1";
        $querySelectIdFactura = mysql_query($sqlSelectIdFactura,$link);

        $id_factura_venta = mysql_result($querySelectIdFactura,0,'id');
        $exento_iva       = mysql_result($querySelectIdFactura,0,'exento_iva');

        //insertar datos de la tabla de items de la tabla principal
        $sqlConsultaInventario="SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.saldo_cantidad,COI.costo_unitario,
                                    COI.tipo_descuento,COI.descuento,
                                    COI.valor_impuesto,COI.observaciones, COI.nombre_unidad_medida,
                                    COI.cantidad_unidad_medida,CO.id AS id_documento,CO.consecutivo AS consecutivo_documento
                                FROM $tablaCargaInventario AS COI INNER JOIN  $tablaCarga AS CO ON COI.$idTablaCargar=CO.id
                                WHERE CO.consecutivo='$idConsecutivoCotizacionPedido'
                                    AND COI.activo     = 1
                                    AND COI.saldo_cantidad>0
                                    AND CO.id_sucursal ='$id_sucursal'
                                    AND CO.id_bodega   ='$filtro_bodega'
                                    AND CO.id_empresa  ='$id_empresa'";
        $queryConsultaInventario=mysql_query($sqlConsultaInventario,$link);

        $valorImpuestoBd = ($exento_iva == 'Si')? 0: 'NULL';
        while ($rowCotizacion=mysql_fetch_array($queryConsultaInventario)) {

            $cadenaInsert .= "('$id_factura_venta',
                                '".$rowCotizacion['id_inventario']."',
                                '".$rowCotizacion['saldo_cantidad']."',
                                '".$rowCotizacion['saldo_cantidad']."',
                                '".$rowCotizacion['costo_unitario']."',
                                '".$rowCotizacion['tipo_descuento']."',
                                '".$rowCotizacion['descuento']."',
                                ".$valorImpuestoBd.",
                                '".$rowCotizacion['observaciones']."',
                                '".$rowCotizacion['id']."',
                                '".$rowCotizacion['id_documento']."',
                                '".$rowCotizacion['consecutivo_documento']."',
                                '$referencia_consecutivo'),";
        }

        $cadenaInsert         = substr($cadenaInsert,0,-1);
        $sqlInsertArticulos   = "INSERT INTO $tablaInventario (
                                    $idTablaPrincipal,
                                    id_inventario,
                                    cantidad,
                                    saldo_cantidad,
                                    costo_unitario,
                                    tipo_descuento,
                                    descuento,
                                    valor_impuesto,
                                    observaciones,
                                    id_tabla_inventario_referencia,
                                    id_consecutivo_referencia,
                                    consecutivo_referencia,
                                    nombre_consecutivo_referencia)
                                VALUES $cadenaInsert";

        $queryInsertArticulos = mysql_query($sqlInsertArticulos,$link);

        if (!$queryInsertArticulos) {  echo '<script>alert("Error!\nNo se cargaron los articulos al documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }

        $arrayReplaceString = array("\n","\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", $rowCotizacion['observaciones']);

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFinal'.$opcGrillaContable.'",
                            editable   : false,
                            value      : fechaVencimientoFactura'.$opcGrillaContable.',
                            listeners  : { select: function() { UpdateFormaPago'.$opcGrillaContable.'(this.value); } }
                        });';

        if (user_permisos(215) == 'true') {
            $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function() { UpdateFechaRemision'.$opcGrillaContable.'(); } }
                        });';
        }

        $acumScript .=  'document.getElementById("codCliente'.$opcGrillaContable.'").value    = "'.$cod_cliente_cotizacion.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value     = "'.$nit.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value  = "'.$cliente_cotizacion.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha.'";
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$nombre_vendedor.'";
                        observacion'.$opcGrillaContable.'="'.$observacion_cotizacion.'";

                        id_cliente_'.$opcGrillaContable.'   = "'.$id_cliente_cotizacion.'";
                        codigoCliente'.$opcGrillaContable.' = "'.$cod_cliente_cotizacion.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$cliente_cotizacion.'";';

        include("../bd/functions_body_article.php");
        $bodyArticle = cargaArticulosSave($id_factura_venta,$observacion,0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

    }

    //============================================ SI NO EXISTE EL PROCESO SE CREA EL ID UNICO ===================================================
    else if(!isset($id_factura_venta)){

        // CREACION DEL ID UNICO
        $random_factura = responseUnicoRanomico();

        $sqlInsert   = "INSERT INTO $tablaPrincipal (id_empresa,random,fecha_inicio,id_sucursal,id_bodega,documento_vendedor,nombre_vendedor,id_usuario)VALUES('$id_empresa','$random_factura','$fecha','$id_sucursal','$filtro_bodega','".$_SESSION['CEDULAFUNCIONARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."','$id_usuario')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId      = "SELECT id FROM $tablaPrincipal  WHERE random='$random_factura' LIMIT 0,1";
        $id_factura_venta = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFinal'.$opcGrillaContable.'",
                            editable   : false,
                            value      : fechaVencimientoFactura'.$opcGrillaContable.',
                            listeners  : { select: function() { UpdateFormaPago'.$opcGrillaContable.'(this.value); } }
                        });
                        document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";';

        if (user_permisos(216) == 'true') {
            $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function() { UpdateFechaRemision'.$opcGrillaContable.'(); } }
                        });';
        }
    }

    //======================================================= SI EXISTE LA REMISION =================================================================
    else{

        include("../bd/functions_body_article.php");

        $sql   = "SELECT id_cliente,
                        cod_cliente,
                        nit,
                        cliente,
                        date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                        date_format(fecha_finalizacion,'%Y-%m-%d') AS fechaFin,
                        observacion,
                        estado,
                        exento_iva,
                        nombre_vendedor,
                        codigo_centro_costo,
                        centro_costo,
                        id_sucursal_cliente
                    FROM $tablaPrincipal
                    WHERE id='$id_factura_venta' AND activo = 1";
        $query = mysql_query($sql,$link);

        $nit               = mysql_result($query,0,'nit');
        $cliente           = mysql_result($query,0,'cliente');
        $id_cliente        = mysql_result($query,0,'id_cliente');
        $cod_cliente       = mysql_result($query,0,'cod_cliente');
        $fecha             = mysql_result($query,0,'fecha');
        $fechaFin          = mysql_result($query,0,'fechaFin');
        $estado            = mysql_result($query,0,'estado');
        $exento_iva        = mysql_result($query,0,'exento_iva');
        $nombre_vendedor   = mysql_result($query,0,'nombre_vendedor');
        $consecutivo       = mysql_result($query,0,'consecutivo');
        $codigoCcos        = mysql_result($query,0,'codigo_centro_costo');
        $nombreCcos        = mysql_result($query,0,'centro_costo');
        $idSucursalCliente  = mysql_result($query,0,'id_sucursal_cliente');

        $labelCcos = $codigoCcos.' '.$nombreCcos;

        if ($opcGrillaContable=='RemisionesVenta' && $estado=='2') { echo "ESTA REMISION DE VENTA ESTA CERRADA "; exit; }

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFinal'.$opcGrillaContable.'",
                            editable   : false,
                            value      : new Date(),
                            listeners  : { select: function() { UpdateFormaPago'.$opcGrillaContable.'(this.value); } }
                        });';

        if (user_permisos(216) == 'true') {
            $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function() { UpdateFechaRemision'.$opcGrillaContable.'(); } }
                        });';
        }

        $acumScript .=  'document.getElementById("codCliente'.$opcGrillaContable.'").value       = "'.$cod_cliente.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value     = "'.$nit.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value  = "'.$cliente.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha.'";
                        document.getElementById("fechaFinal'.$opcGrillaContable.'").value     = "'.$fechaFin.'";
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$nombre_vendedor.'";
                        observacion'.$opcGrillaContable.'                                     = "'.$observacion.'";

                        id_cliente_'.$opcGrillaContable.'   = "'.$id_cliente.'";
                        codigoCliente'.$opcGrillaContable.' = "'.$cod_cliente.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$cliente.'";';

        $bodyArticle = cargaArticulosSave($id_factura_venta,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
    }

    //======================== DOCUMENTOS AGREGADOS A LA PRESENTE FACTURA ======================//
    $acumDocReferencia  = '';
    $margin_left        = 'margin-left:5px';
    $sqlDocReferencia   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
                            FROM $tablaInventario
                            WHERE id_consecutivo_referencia>0 AND $idTablaPrincipal='$id_factura_venta' AND activo=1
                            ORDER BY id ASC";
    $queryDocReferencia = mysql_query($sqlDocReferencia,$link);

    while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){
        if($rowDocReferencia['doc_referencia'] == 'C'){ $title = 'Eliminar los Articulos de la Cotizacion'; }
        else if($rowDocReferencia['doc_referencia'] == 'P'){ $title = 'Eliminar los Articulos del Pedido'; }
        else if($rowDocReferencia['doc_referencia'] == 'R'){ $title = 'Eliminar los Articulos de la Remision'; }

        $typeDocCruce   = $rowDocReferencia['doc_referencia'];
        $numeroDocCruce = $rowDocReferencia['consecutivo_referencia'];

        $acumDocReferencia .='<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;height: 22px;" id="divDocReferencia'.$opcGrillaContable.'_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                <div class="contenedorInputDocReferenciaFactura">
                                    <input type="text" class="inputDocReferenciaFactura" value="'.$rowDocReferencia['doc_referencia'].' '.$rowDocReferencia['cod_referencia'].'" style="border-bottom: 1px solid #d4d4d4;" readonly/>
                                </div>
                                <div title="'.$title.' # '.$rowDocReferencia['cod_referencia'].' en la presente remision" onclick="eliminaDocReferencia'.$opcGrillaContable.'(\''.$rowDocReferencia['id_referencia'].'\',\''.$rowDocReferencia['doc_referencia'].'\',\''.$id_factura_venta.'\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">
                                    <div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btn'.$opcGrillaContable.'_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                        <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                                    </div>
                                </div>
                            </div>';
    }
    $acumScript .= 'exento_iva_'.$opcGrillaContable.' = "'.$exento_iva.'";';
    $habilita    = ($estado=='1')?  'onclick="javascript: return false;" disabled ': '';

    //============================================== SUCURSALES TERCERO ===================================================//
    $sqlSucursales   = "SELECT id,nombre FROM terceros_direcciones WHERE id_tercero=$id_cliente AND activo=1";
    $querySucursales = mysql_query($sqlSucursales,$link);

    while ($row=mysql_fetch_array($querySucursales)) {
        $sucursalesCliente .= '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
    }
    $acumScript .= 'document.getElementById("sucursalCliente'.$opcGrillaContable.'").value="'.$idSucursalCliente.'";';

    //============================================== SUCURSALES TERCERO ===================================================//
        // $sqlSucursales   = "SELECT id,nombre FROM terceros_direcciones WHERE id_tercero=$id_cliente AND activo=1";
        // $querySucursales = mysql_query($sqlSucursales,$link);

        // while ($row=mysql_fetch_array($querySucursales)) {
        //     $sucursalesCliente .= '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
        // }
        // $acumScript .= 'document.getElementById("sucursalCliente'.$opcGrillaContable.'").value="'.$idSucursalCliente.'";';


?>

<div class="contenedorOrdenCompra" id>

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Vencimiento</div>
                    <div class="campoTop" >
                        <input type="text" id="fechaFinal<?php echo $opcGrillaContable; ?>" />

                        <div id="renderSelectFormaPago<?php echo $opcGrillaContable; ?>" style="float:left;display:none;"></div>
                        <div id="fechaLimitePago<?php echo $opcGrillaContable; ?>" style="float:left; font-size: 11px; text-align:center;  padding-top: 5px;"></div>
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Docs. Cruce</div>
                    <div class="campoTop" style="height:auto;"  id="contenedorDocsReferencia<?php echo $opcGrillaContable; ?>"><?php echo $acumDocReferencia; ?></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Codigo Cliente</div>
                    <div class="campoTop"><input type="text" id="codCliente<?php echo $opcGrillaContable; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" ></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" id="nitCliente<?php echo $opcGrillaContable; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" /></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Cliente</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaCliente<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Cliente">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>

                <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Sucursal Cliente</div>
                    <div id="renderSelectSucursalCliente<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" id="contenedorSucursalCliente<?php echo $opcGrillaContable; ?>">
                        <select id="sucursalCliente<?php echo $opcGrillaContable; ?>" onChange="updateSucursalCliente<?php echo $opcGrillaContable; ?>(this)" style="float:left;"/>
                            <?php echo $sucursalesCliente; ?>
                        </select>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Vendedor</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreVendedor<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaVendedor<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Vendedor">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>

                <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Centro de Costo</div>
                    <div id="renderSelectCcos" style="float:left; margin-left:-22px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop"><input type="text" id="cCos_<?php echo $opcGrillaContable; ?>" value="<?php echo $labelCcos; ?>" Readonly/></div>
                    <div class="iconBuscarProveedor" onclick="ventanaCcos_<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Centro de Costo">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>
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

    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();                   //disable btn imprimir
    document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").focus();         //dar el foco

    UpdateFormaPago<?php echo $opcGrillaContable; ?>(document.getElementById("fechaFinal<?php echo $opcGrillaContable; ?>").value);

    //=========================== UPDATE FORMAS DE PAGO ============================================//
    function UpdateFormaPago<?php echo $opcGrillaContable; ?>(idFormaPago){
        Ext.get('renderSelectFormaPago<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'UpdateFormaPago',
                idFormaPago       : idFormaPago,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    //=========================== UPDATE FORMAS DE PAGO ============================================//
    function UpdateFechaRemision<?php echo $opcGrillaContable; ?>(){
        var fecha = document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value;
        Ext.get('renderSelectFormaPago<?php echo $opcGrillaContable; ?>').load({
            url     : 'remisiones/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'UpdateFechaRemision',
                fecha             : fecha,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    //==================== CAMBIA TIPO DE DESCUENTO POR ARTICULO ===================================//
    function tipoDescuentoArticulo<?php echo $opcGrillaContable; ?> (cont){
        document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
        // Si existe un articulo almacenado muestra el boton deshacer
        if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'block';
        }
        //si esta en signo porcentaje cambia a pesos, y viceversa
        if (document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png') {
            document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("src","img/pesos.png");
            document.getElementById('tipoDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("title","En Pesos");
            document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();
        }
        else{
            document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("src","img/porcentaje.png");
            document.getElementById('tipoDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("title","En Porcentaje");
            document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();
        }
    }

    //==================  GUARDAR LA OBSERVACION DE LA FACTURA ==================================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
        document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13 || tecla == 9){ guardarObservacion<?php echo $opcGrillaContable; ?>(); }

        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = setTimeout(function(){
            guardarObservacion<?php echo $opcGrillaContable; ?>();
        },1500);

    }

    function guardarObservacion<?php echo $opcGrillaContable; ?>(){
        var observacion = document.getElementById('observacion<?php echo $opcGrillaContable; ?>').value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = '';

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  :
            {
                opc            : 'guardarObservacion',
                id             : '<?php echo $id_factura_venta; ?>',
                tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                observacion    : observacion
            },
            success :function (result, request){
                        if(result.responseText != 'true'){
                            // alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
                        else{
                            observacion<?php echo $opcGrillaContable; ?>=observacion;
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardado</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
                    },
            failure : function(){
                        // alert('Error de conexion con el servidor');
                        document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                        document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                    }
        });
    }

    //============================= FILTRO TECLA BUSCAR PROVEEDOR ==============================//
    function buscarCliente<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla   = Input ? event.keyCode : event.which
        ,   inputId = Input.id
        ,   numero  = Input.value;

        if(inputId == "nitCliente<?php echo $opcGrillaContable; ?>" && numero==nitCliente<?php echo $opcGrillaContable; ?>){ return true;}
        else if(inputId == "codCliente<?php echo $opcGrillaContable; ?>" && numero==codigoCliente<?php echo $opcGrillaContable; ?>){ return true;}
        else if(Input.value != '' && id_cliente_<?php echo $opcGrillaContable;?> == 0 && (tecla == 13 )){
            Input.blur();
            ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(Input.value, Input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        else if(id_cliente_<?php echo $opcGrillaContable;?>>0 && contArticulos<?php echo $opcGrillaContable; ?>>1){
            Input.blur();
            if(confirm('Esta seguro de cambiar de cliente y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){
                ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input);
            }
            else{
                document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value    = nitCliente<?php echo $opcGrillaContable; ?>;
                document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value    = codigoCliente<?php echo $opcGrillaContable; ?>;
                document.getElementById("nombreCliente<?php echo $opcGrillaContable;?>").value  = nombreCliente<?php echo $opcGrillaContable; ?>;
            }
        }
        else if(id_cliente_<?php echo $opcGrillaContable;?>>0){
            ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input);
        }
        return true;
    }

    function ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codCliente, inputId){
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCliente',
                codCliente        : codCliente,
                inputId           : inputId,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    function ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor si es una factura
        if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {
            var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
            for(i in checks){ checks[i].checked=false; checks[i].checked=false; }
        }
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cambiaCliente',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    //============================ VENTANA BUSCAR CLIENTE =======================================//
    function buscarVentanaCliente<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND tipo_cliente = \"Si\"';

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaCliente_<?php echo $opcGrillaContable; ?>',
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
                    cargaFuncion  : 'renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id);',
                    nombre_grilla : 'cliente<?php echo $opcGrillaContable; ?>'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id){

        if(id != id_cliente_<?php echo $opcGrillaContable;?> && contArticulos<?php echo $opcGrillaContable; ?>>1){
            if(!confirm('Esta seguro de cambiar de cliente y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }
        }
        else if (id == id_cliente_<?php echo $opcGrillaContable;?>){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }

        ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(document.getElementById("codCliente<?php echo $opcGrillaContable; ?>"));
        id_cliente_<?php echo $opcGrillaContable;?> = id;
        contArticulos<?php echo $opcGrillaContable; ?>  = 1;

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
        ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(id,'idCliente<?php echo $opcGrillaContable; ?>');
    }

    //=========================== VENTANA BUSCAR VENDEDOR ======================================//
    function buscarVentanaVendedor<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaVendedor_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>',
            title       : 'Vendedores',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaVendedor.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : '',
                    cargaFuncion  : 'renderizaResultadoVentanaVendedor<?php echo $opcGrillaContable; ?>(id);',
                    nombre_grilla : 'vendedor<?php echo $opcGrillaContable; ?>'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentanaVendedor<?php echo $opcGrillaContable; ?>(id){
        var documento = document.getElementById('div_vendedor<?php echo $opcGrillaContable; ?>_documento_'+id).innerHTML;
        var nombre = document.getElementById('div_vendedor<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML;
        //mostramos el nombre del vendedor en el campo
        document.getElementById("nombreVendedor<?php echo $opcGrillaContable; ?>").value = nombre;
        ajaxGuardarVendedor<?php echo $opcGrillaContable; ?>(documento,nombre);

        Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>.close();
    }

    //=========================================== AJAX VENDEDOR =============================================================//
    function ajaxGuardarVendedor<?php echo $opcGrillaContable; ?>(documento,nombre){
        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  :
            {
                opc            : 'guardarVendedor',
                id             : '<?php echo $id_factura_venta; ?>',
                tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                documento      : documento,
                nombre         : nombre
            },
            success :function (result, request){
                       if(result.responseText=="false"){
                        alert("Error!\nNo se almaceno el vendedor\nSi el problema persiste comuniquese con el administrador del sistema");
                       }
                    },
            failure : function(){ alert('Error de conexion con el servidor'); }
        });
    }


    //============================== FILTRO TECLA BUSCAR ARTICULO ==========================================================//
    function buscarArticulo<?php echo $opcGrillaContable; ?>(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if (tecla == 13 && numero>0) {
            input.blur();
            ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(input.value, input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }
        if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value               = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value                 = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value            = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value       = "";
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';
        }
        else if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value      = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value     = "";
            document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        }
        return true;
    }

    function ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(valor,input){
        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                whereBodega       : 'AND IT.id_sucursal=<?php echo $id_sucursal; ?>  AND IT.id_ubicacion=<?php echo $filtro_bodega ?>',
                campo             : arrayIdInput[0],
                valorArticulo     : valor,
                idArticulo        : arrayIdInput[1],
                exentoIva         : exento_iva_<?php echo $opcGrillaContable; ?>,
                idProveedor       : id_cliente_<?php echo $opcGrillaContable;?>,
                id                : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    //======================== FUNCION PARA BUSCAR LA COTIZACION-PEDIDO POR SU NUMERO =======================================//
    function buscarCotizacionPedido<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla  = (Input) ? event.keyCode : event.which
        ,   numero = Input.value;

        if(tecla == 13 || tecla == 9){
            var validacion= validarArticulos<?php echo $opcGrillaContable; ?>();
            if (validacion==1) {
                if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
            }
            else if (validacion== 2 || validacion== 0) { ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
            return;
        }

        setTimeout(function(){ Input.value = (Input.value).replace(/[^0-9]/g,''); },10);
    }

    function ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido){

        agregarDocumento<?php echo $opcGrillaContable; ?>(idCotizacionPedido);
        return;


        if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") {  //carga remision
            tablaBuscar = "ventas_cotizaciones";
            opcCargar   = "cotizacionRemision";
        }
        else{                                  //cargar Remision desde un pedido
            tablaBuscar = "ventas_pedidos";
            opcCargar   = "pedidoRemision";
        }

        Ext.get('renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>').load({
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

    //========================= FUNCION PARA AGREGAR UN DOCUMENTO ===================================================//
    function agregarDocumento<?php echo $opcGrillaContable; ?>(codigo){

        if (codigo!='') { var codDocAgregar=codigo; }
        else{ var codDocAgregar = document.getElementById('cotizacionPedido<?php echo $opcGrillaContable; ?>').value; }

        if(isNaN(codDocAgregar) || codDocAgregar==0){ alert('digite el consecutivo del documento que desea cargar.'); return;}
        if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") { typeDoc = "cotizacion"; }
        else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/pedido.png"){ typeDoc = "pedido"; }
        else{ typeDoc = "remision"; }

        Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>").load({
            url     : "bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'agregarDocumento',
                typeDoc           : typeDoc,
                codDocAgregar     : codDocAgregar,
                id_factura        : '<?php echo $id_factura_venta ?>',
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            }
        });
    }

    function eliminaDocReferencia<?php echo $opcGrillaContable; ?>(idDocReferencia,docReferencia,id_factura_venta){
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'eliminaDocReferencia',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id_factura        : '<?php echo $id_factura_venta; ?>',
                id_doc_referencia : idDocReferencia,
                docReferencia     : docReferencia,
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            }
        });
    }

    function ajaxCambia<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="contTopFila" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Cliente y se deshabilitan
        var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
        for(i in checks){ checks[i].checked=false; checks[i].disabled=true; }

        Ext.get("contenedor_facturacion_compras").load({
            url     : "facturacion_compras.php",
            scripts : true,
            nocache : true,
            params  : { filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value }
        });
    }

    //======================== VENTANA BUSCAR PEDIDO - COTIZACION ===========================================================//
    function ventanaBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(){

        if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/pedido.png"){       //CARGAR PEDIDO
            titulo         = "Seleccione el Pedido";
            tablaGrilla    = "ventas_pedidos";
            nameGrillaLoad = "grillaPedidoRemision";
        }
        else{
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


    //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
    function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND id_sucursal=<?php echo $id_sucursal; ?> AND id_ubicacion=<?php echo $filtro_bodega; ?> AND estado_venta="true"';
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
                url     : '../funciones_globales/grillas/BusquedaInventariosVentas.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : sql,
                    nombre_grilla : nombre_grilla,
                    nombreTabla   : 'inventario_totales',
                    cargaFuncion  : 'responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,'+cont+');'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_Ventana_buscar_Articulo_factura.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,cont){
        document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();

        var costoTotal       = 0
        ,   totalDescuento   = 0
        ,   idArticulo       = document.getElementById('ventas_id_item_'+id).innerHTML
        ,   codigo           = document.getElementById('div_'+nombre_grilla+'_codigo_'+id).innerHTML
        ,   costo            = document.getElementById('div_'+nombre_grilla+'_precio_venta_'+id).innerHTML
        ,   costo_inventario = document.getElementById('div_'+nombre_grilla+'_costos_'+id).innerHTML
        ,   unidadMedida     = document.getElementById('unidad_medida_grilla_'+id).innerHTML
        ,   nombreArticulo   = document.getElementById('div_'+nombre_grilla+'_nombre_equipo_'+id).innerHTML
        ,   cantidad         = (document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1
        ,   tipoDescuento    = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0]
        ,   descuento        = (document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1;

        costo=('<?php echo $tipo_valor ?>'=='CI')? costo_inventario : costo ;

        //SI EL TERCERO ESTA EXENTO DE IVA
        if(exento_iva_<?php echo $opcGrillaContable; ?> == 'Si'){
            if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){                    //mostrar la imagen deshacer y actualizar
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
                document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';
            }
            else{ document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = "none"; }

            document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value       = unidadMedida;
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value     = idArticulo;
            document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = codigo;
            document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value  = costo;
            document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value = nombreArticulo;
            document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = 0;

            Win_Ventana_buscar_Articulo_factura.close();
            return;
        }
        else{
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscarImpuestoArticulo',
                    id                : '<?php echo $id_factura_venta; ?>',
                    id_inventario     : idArticulo,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cont              : cont,
                    unidadMedida      : unidadMedida,
                    idArticulo        : idArticulo,
                    codigo            : codigo,
                    costo             : costo,
                    nombreArticulo    : nombreArticulo,

                }
            });
        }
    }

    //============================= FILTRO CAMPO CANTIDAD ARTICULO ==========================================================//
    function cantidadArticulo<?php echo $opcGrillaContable; ?>(cantidad){ }

    //============================= FILTRO TECLA GUARDAR ARTICULO ==========================================================//
    function guardarAuto<?php echo $opcGrillaContable; ?>(event,input,cont){
        var idInsertArticulo  = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tecla  = input? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont);
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        else if (idInsertArticulo>0) {
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

        patron = /[^\d.]/g;
        if(patron.test(value)){ input.value = input.value.replace(patron,''); }
        return true;
    }

    function guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont){

        var idInsertArticulo                                   = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idInventario<?php echo $opcGrillaContable; ?>      = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo<?php echo $opcGrillaContable; ?>      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var descuentoArticulo<?php echo $opcGrillaContable; ?> = document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo<?php echo $opcGrillaContable; ?>     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        var opc       = 'guardarArticulo';
        var divRender = '';
        var accion    = 'agregar';
        var iva       = document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDesc  = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0];

        if (idInventario<?php echo $opcGrillaContable; ?> == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
        else if(cantArticulo<?php echo $opcGrillaContable; ?> <= 0 || cantArticulo<?php echo $opcGrillaContable; ?> == ''){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo Cantidad es obligatorio'); setTimeout(function(){document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }
        else if(costoArticulo<?php echo $opcGrillaContable; ?> <= 0 || costoArticulo<?php echo $opcGrillaContable; ?> == ''){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo costo es obligatorio'); setTimeout(function(){document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }

        if(isNaN(descuentoArticulo<?php echo $opcGrillaContable; ?>)){
            setTimeout(function(){ document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20);
            setTimeout(function(){ alert('El campo descuento debe ser numerico'); },80);
            return;
        }
        else if (idInventario<?php echo $opcGrillaContable; ?> == 0){
            alert('El campo articulo es Obligatorio');
            setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
            return;
        }
        else if(cantArticulo<?php echo $opcGrillaContable; ?> <= 0 || cantArticulo<?php echo $opcGrillaContable; ?> == ''){
            document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur();
            setTimeout(function(){ alert('El campo Cantidad es obligatorio!'); },80);
            //setTimeout(function(){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20);
            return;
        }


        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertArticulo > 0){
            opc       = 'actualizaArticulo';
            divRender = 'renderArticulo<?php echo $opcGrillaContable; ?>_'+cont;
            accion    = 'actualizar';
        }
        else{
            //VALIDAMOS PARA NO REPETIR FILAS DE LAN GRILLA
            contArticulos<?php echo $opcGrillaContable; ?>++;
            divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>;
            var div   = document.createElement('div');
            div.setAttribute('id','bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>);
            div.setAttribute('class','bodyDivArticulos<?php echo $opcGrillaContable; ?>');
            document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').appendChild(div);
        }

        Ext.get(divRender).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : opc,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                consecutivo       : contArticulos<?php echo $opcGrillaContable; ?>,
                cont              : cont,
                idInsertArticulo  : idInsertArticulo,
                idInventario      : idInventario<?php echo $opcGrillaContable; ?>,
                cantArticulo      : cantArticulo<?php echo $opcGrillaContable; ?>,
                tipoDesc          : tipoDesc,
                descuentoArticulo : descuentoArticulo<?php echo $opcGrillaContable; ?>,
                costoArticulo     : costoArticulo<?php echo $opcGrillaContable; ?>,
                exento_iva        : exento_iva_<?php echo $opcGrillaContable; ?>,
                iva               : iva,
                id                : '<?php echo $id_factura_venta; ?>',
            }
        });

        //despues de registrar el primer articulo, habilitamos el boton nuevo
        Ext.getCmp("btnNueva<?php echo $opcGrillaContable; ?>").enable();


        //llamamos la funcion para calcular los totales de la facturan si accion = agregar
        if (accion=="agregar") {
            calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo<?php echo $opcGrillaContable; ?>,descuentoArticulo<?php echo $opcGrillaContable; ?>,costoArticulo<?php echo $opcGrillaContable; ?>,accion,tipoDesc,iva,cont);
        }
    }

    function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){

        var idArticulo<?php echo $opcGrillaContable; ?>        = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo<?php echo $opcGrillaContable; ?>      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var descuentoArticulo<?php echo $opcGrillaContable; ?> = document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo<?php echo $opcGrillaContable; ?>     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        var iva      = document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDesc = '';

        if (document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png') { tipoDesc='porcentaje';}
        else{ tipoDesc='pesos'; }

        if(confirm('Esta Seguro de eliminar este articulo de la factura de compra?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : idArticulo<?php echo $opcGrillaContable; ?>,
                    cont              : cont,
                    id                : '<?php echo $id_factura_venta; ?>'
                }
            });
            calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo<?php echo $opcGrillaContable; ?>,descuentoArticulo<?php echo $opcGrillaContable; ?>,costoArticulo<?php echo $opcGrillaContable; ?>,'eliminar',tipoDesc,iva,cont);
        }
    }

    //======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA ==========================================//
    function ventanaDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont){
        var id = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_descripcion_Articulo_factura = new Ext.Window({
            width       : 330,
            height      : 240,
            id          : 'Win_Ventana_descripcion_Articulo_factura',
            title       : 'Observacion articulo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaDescripcionArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : id,
                    cont              : cont,
                    id                : '<?php echo $id_factura_venta; ?>'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Guardar',
                    scale       : 'large',
                    iconCls     : 'guardar',
                    iconAlign   : 'left',
                    handler     : function(){ btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,id); }
                },
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_Ventana_descripcion_Articulo_factura.close(id) }
                }
            ]
        }).show();
    }

    function btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,idArticulo){
        var observacion = document.getElementById("observacionArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacion<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarDescripcionArticulo',
                idArticulo        : idArticulo,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_factura_venta; ?>',
                observacion       : observacion
            }
        });
    }


    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===================//
    function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
        //capturamos el id que esta asignado en la variable oculta
        id_actual=document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;

        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'retrocederArticulo',
                cont              : cont,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idArticulo        : id_actual,
                id                : '<?php echo $id_factura_venta; ?>',
                exento_iva        : exento_iva_<?php echo $opcGrillaContable; ?>,
            }
        });
    }

    //====================== FINALIZAR 'CERRAR' 'GENERAR' =====================//
    function guardar<?php echo $opcGrillaContable; ?>(){
        if (document.getElementById('sucursalCliente<?php echo $opcGrillaContable; ?>').value==0 || document.getElementById('sucursalCliente<?php echo $opcGrillaContable; ?>').value=='') {
            alert('Seleccione la sucursal del cliente');
            return;
        }
        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();

        if (validacion==0) { alert("No hay articulos por guardar en la presente remision de venta!"); return; }
        else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return; }

        else if (validacion== 2 || validacion== 0) {
            var idBodega    = document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            ,   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;
            observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

            cargando_documentos('Generadno Documento...','');
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'terminarGenerar',
                    opc2              : 'remision',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_factura_venta; ?>',
                    idBodega          : idBodega,
                    observacion       : observacion
                }
            });
        }
    }

    //================================================= BUSCAR ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){

        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();
        if (validacion==1) {
            if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
        }
        else if (validacion== 2 || validacion== 0) { ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
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
                             xtype      : 'button',
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

    //================================== VALIDACION NUMERICA EN CANTIDAD Y DESCUENTO ===================================//
    function validarNumberArticulo<?php echo $opcGrillaContable; ?>(event,input,typeValidate,cont){
        var contIdInput = (input.id).split('_')[1];
        var nombreInput = (input.id).split('_')[0];

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;

        //VALIDACION DE LA CANTIDAD DEL ARTICULO
        if (nombreInput=='cantArticulo<?php echo $opcGrillaContable; ?>') {
            if(tecla == 13 || tecla == 9){
                var id_insert = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value;
                //SI EL OBJETO NO EXISTE, SE CARGO NORMALMENTE
                if (typeof(objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert])=='undefined') {
                    // ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>');
                    // document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
                    ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>');
                    return;
                }
                //SI EL OBJETO EXISTE, Y SUPERA LA CANTIDAD DEL DOCUMENTO CARGADO
                else if((objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].saldo_cantidad * 1)< (numero*1)){
                    input.blur();
                    alert('la cantidad no puede ser mayor a la ya registrada en el documento '+objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].typeDoc);
                    input.value = objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].saldo_cantidad;
                    setTimeout(function(){ input.focus(); },100);
                    return;
                }
                //SI EL OBJETO EXISTE Y NO SUPERA LA CANTIDAD CARGADA
                else{
                    ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>');
                    return;
                }
            }
        }

        if(tecla == 13){
            if(nombreInput == 'cantArticulo<?php echo $opcGrillaContable; ?>'){ document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+contnombreInput).focus(); }
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
        id=document.getElementById("idArticulo"+opc+"_"+cont).value;

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
                        if(parseInt(cantidad) > parseInt(result.responseText)){
                            if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta' || '<?php echo $opcGrillaContable; ?>'=='RemisionesVenta') {
                                alert("Error!\nLa cantidad ingresada es mayor a la existente\nSolo restan "+result.responseText);
                                document.getElementById("cantArticulo"+opc+"_"+cont).value='';
                                setTimeout(function(){document.getElementById("cantArticulo"+opc+"_"+cont).focus();},100);
                            }else{
                                alert("Advertencia!\nLa cantidad ingresada es mayor a la existente\nSolo restan "+result.responseText);
                                document.getElementById("descuentoArticulo"+opc+"_"+cont).focus();
                            }
                        }
                        else if (result.responseText=='false') {
                            alert("Error!\nSe produjo un problema con la validacion\nNo se verifico la cantidad del Articulo\nSi el problema persiste comuniquese con el administrador del sistema");
                        }
                        else{ document.getElementById("descuentoArticulo"+opc+"_"+cont).focus(); }

                    },
            failure : function(){ alert('Error de conexion con el servidor'); }
        });
    }

    //================================== IMPRIMIR EN PDF ==================================================================//

    function imprimir<?php echo $opcGrillaContable; ?> (){
        window.open("bd/imprimirGrillaContable.php?id=<?php echo $id_factura_venta; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    //================================== IMPRIMIR EN EXCEL =================================================================//
    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
       window.open("bd/exportarExcelGrillaContable.php?id=<?php echo $id_factura_venta; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }


    //============================ VALIDAR QUE NO HAYA NINGUN ARTICULO POR GUARDAR O POR ACTULIZAR =======================//
    function validarArticulos<?php echo $opcGrillaContable; ?>(){

        var cont = 0
        ,   contTotal = 0
        ,   contArticulo
        ,   nameArticulo
        ,   divsArticulos<?php echo $opcGrillaContable; ?> = document.querySelectorAll(".bodyDivArticulos<?php echo $opcGrillaContable; ?>");

        for(i in divsArticulos<?php echo $opcGrillaContable; ?>){

            if(typeof(divsArticulos<?php echo $opcGrillaContable; ?>[i].id)!='undefined'){

                contTotal++;

                nameArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[0]
                contArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[1]

                if(     document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).value > 0
                    &&  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                    ||  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
                    &&  document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contArticulo).style.display == 'inline')
                    { cont++; }
            }
        }

        if(contTotal==0 || contTotal==1){ return 0; }      //no hay articulos ni tercero relacionado
        else if(cont > 0){ return 1; }  //articulos pendientes por guardar
        else { return 2; }              //ok
    }

    //============================// CANCELAR UN DOCUMENTO //============================//
    //***********************************************************************************//

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

    function ventanaCcos_<?php echo $opcGrillaContable; ?>(){
        Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 600,
            height      : 450,
            id          : 'Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>',
            title       : 'Seleccione el Centro de Costo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'remisiones/bd/centro_costos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                    impressFunctionScript : 'renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id)'
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
                            handler     : function(){ Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close() }
                        }
                    ]
                }
            ]
        }).show();
    }

    function renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id){

        var nombre = ''
        ,   codigo = '';

        if(id > 0){
            nombre = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML
            codigo = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_codigo_'+id).innerHTML;
        }

        Ext.get('renderSelectCcos').load({
            url     : 'remisiones/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                idCcos     : id,
                nombre     : nombre,
                codigo     : codigo,
                opc        : 'updateCcos',
                id_factura : '<?php echo $id_factura_venta; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });

        Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
    }

    function updateSucursalCliente<?php echo $opcGrillaContable;?>(input){
        if(isNaN(id_cliente_<?php echo $opcGrillaContable;?>) || id_cliente_<?php echo $opcGrillaContable;?> == 0){ alert("Aviso\nSeleccione el cliente antes de continuar!"); }

        var id_scl_cliente     = input.value
        ,   nombre_scl_cliente = input.options[input.selectedIndex].text;

        if(nombre_scl_cliente == 'Seleccione...') nombre_sucursal = '';

        Ext.get('renderSelectSucursalCliente<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                : 'updateSucursalCliente',
                id_factura         : '<?php echo $id_factura_venta; ?>',
                id_cliente         : id_cliente_<?php echo $opcGrillaContable;?>,
                id_scl_cliente     : id_scl_cliente,
                nombre_scl_cliente : nombre_scl_cliente
            }
        });
    }



</script>
