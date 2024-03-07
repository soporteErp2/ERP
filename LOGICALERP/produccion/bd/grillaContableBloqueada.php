<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../funciones_globales/funciones_php/randomico.php");
    include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fechaActual = date('Y-m-d');
    $divImagen   = '';

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
    else if ($opcGrillaContable == 'FacturaVenta'){ $arrayPermisos = array(21, 22, 23, 24); }

    list($permisoGuardar, $permisoEditar, $permisoEliminar, $permisoRestaurar) = $arrayPermisos;

    $user_permiso_editar    = (user_permisos($permisoEditar,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos($permisoEliminar,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos($permisoRestaurar,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';     //restaurar

    //el campo numero de factura solo esta en la tabla de ventas_facturas, si que verificamos que opcGrillaContable sea ventas facturas para pasar la cadena sql con ese campo, sino se pasa si ese campo
    $div_sucursal_cliente = '';
    if ($opcGrillaContable=='FacturaVenta') {
        $sql   = "SELECT id_cliente,
                    prefijo,
                    numero_factura,
                    cod_cliente,
                    nit,cliente,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                    date_format(fecha_vencimiento,'%Y-%m-%d') AS fechaFin,
                    DATE_ADD(fecha_inicio, INTERVAL 5 MONTH) AS fecha_bloqueo,
                    observacion,
                    estado,
                    nombre_vendedor,
                    prefijo,
                    cuenta_pago,
                    configuracion_cuenta_pago,
                    centro_costo,
                    codigo_centro_costo,
                    sucursal_cliente
                FROM $tablaPrincipal  WHERE id='$id_factura_venta' AND activo = 1";
    }
    else if($opcGrillaContable=='RemisionesVenta'){
        $sql   = "SELECT id_cliente,
                    cod_cliente,
                    nit,
                    cliente,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                    date_format(fecha_finalizacion,'%Y-%m-%d') AS fechaFin,
                    DATE_ADD(fecha_inicio, INTERVAL 5 MONTH) AS fecha_bloqueo,
                    observacion,
                    estado,
                    nombre_vendedor,
                    centro_costo,
                    codigo_centro_costo,
                    sucursal_cliente
                FROM $tablaPrincipal  WHERE id='$id_factura_venta' AND activo = 1";
    }
    else{
        $sql   = "SELECT id_cliente,
                    cod_cliente,
                    nit,
                    cliente,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                    date_format(fecha_finalizacion,'%Y-%m-%d') AS fechaFin,
                    DATE_ADD(fecha_inicio, INTERVAL 5 MONTH) AS fecha_bloqueo,
                    observacion,
                    estado,
                    nombre_vendedor
                FROM $tablaPrincipal  WHERE id='$id_factura_venta' AND activo = 1";
    }

    $query = mysql_query($sql,$link);

    $nit               = mysql_result($query,0,'nit');
    $cliente           = mysql_result($query,0,'cliente');
    $id_cliente        = mysql_result($query,0,'id_cliente');
    $fecha             = mysql_result($query,0,'fecha');
    $fechaFin          = mysql_result($query,0,'fechaFin');
    $fechaBloqueo      = mysql_result($query,0,'fecha_bloqueo');
    $codigo_cliente    = mysql_result($query,0,'cod_cliente');
    $estado            = mysql_result($query,0,'estado');
    $nombre_vendedor   = mysql_result($query,0,'nombre_vendedor');
    $numero_factura    = mysql_result($query,0,'numero_factura');
    $prefijo           = mysql_result($query,0,'prefijo');
    $codigoCcos        = mysql_result($query,0,'codigo_centro_costo');
    $nombreCcos        = mysql_result($query,0,'centro_costo');
    $cuentaPago        = mysql_result($query,0,'cuenta_pago');
    $configuracionPago = mysql_result($query,0,'configuracion_cuenta_pago');
    $sucursal_cliente  = mysql_result($query,0,'sucursal_cliente');

    if($sucursal_cliente != ''){
        $div_sucursal_cliente = '<div class="renglonTop">
                                    <div class="labelTop">Sucursal Cliente</div>
                                    <div class="campoTop"><input type="text" style="width:100%" value="'.$sucursal_cliente.'" Readonly /></div>
                                </div>';
    }

    $labelCcos = $codigoCcos.' '.$nombreCcos;

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
                                    setTimeout(function(){  calculaPlazo'.$opcGrillaContable.'(); },10);
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
        $plazoFormaPago  = mysql_result($queryFormasPago,0,'plazo');

        $acumScript.='document.getElementById("plazo'.$opcGrillaContable.'").value= "'.$nombreFormaPago.'";';
        $acumScript.='document.getElementById("titleDocumentoFacturaVenta").innerHTML="Factura de Venta<br>N. '.$numero_factura.'";';
    }

    if (strtotime($fechaBloqueo) <= strtotime($fechaActual)){ $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento de venta Vencido">'; }
    else{
        if ($estado == '1'){ $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }
        else if($estado == '2'){  $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento de venta Cruzado">'; }     //documento cruzado con otro
        else if ($estado == '3') { $acumScript .= $user_permiso_restaurar; }
        else{ $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento de venta Cruzado">'; }
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
            $cruce .= '<div title="'.$row['cruce'].' #'.$row['numero_cruce'].'" style="float:left; border-right:1px solid #d4d4d4; padding: 3px; height:100%;" ondblclick="colorFila_'.$opcGrillaContable.'('.$row['id'].')">'.$row['string_cruce'].' '.$row['numero_cruce'].'</div>';
        }

        if($contCruce > 0){
            $divRelacionado = '<div class="renglonTop">
                                    <div class="labelTop">Documentos Relacionados</div>
                                    <div class="campoTop">'.$cruce.'</div>
                                </div>';
        }
    }

    //=============================// CDOCUMENTOS CRUCE //=============================//
    //*********************************************************************************//
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

?>

<div class="contenedorDocumentoVentaBloqueado">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <?php echo $divImagen ; ?>
                <div class="renglonTop">
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" Readonly /></div>
                </div>
                <div class="renglonTop" >
                     <div class="labelTop">Vencimiento</div>
                    <div class="campoTop"  id="fechaLimitePago<?php echo $opcGrillaContable; ?>"><input type="text" value="<?php echo $fechaFin; ?>" Readonly /></div>
                </div>
                <div class="renglonTop" id="divFormaPago<?php echo $opcGrillaContable; ?>" style="display:none;">
                    <div class="labelTop"> Forma de Pago</div>
                    <div class="campoTop" id="selectFormaPago<?php echo $opcGrillaContable; ?>">
                         <input type="text" id="plazo<?php echo $opcGrillaContable; ?>" Readonly />
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

    document.getElementById('fecha<?php echo $opcGrillaContable; ?>').style.overflow='hidden !important';

    //========= FUNCION PARA CALCULAR LOS DIAS RESTANTES DE PLAZO DE PAGO DE LA FACTURA ==============//
    function calculaPlazo<?php echo $opcGrillaContable; ?>(){

        var meses     = new Array ("Ene","Feb","Mar","Abril","Mayo","Jun","Jul","Ago","Sep","Oct","Nov","Dic");
        var arrayDays = new Array("Domingo","Lunes","Martes","Mi&eacute;rcoles","Jueves","Viernes","S&aacute;bado");

        var fechalimite   = Date.parse(document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value);
        var myDate        = new Date(fechalimite);
        var diasRestantes = parseInt('<?php echo $plazoFormaPago; ?>')+ parseInt(1);
        myDate.setDate(myDate.getDate()+diasRestantes);

        // Display the month, day, and year. getMonth() returns a 0-based number.
        var month = myDate.getMonth();
        var day   = myDate.getDate();
        var day1  = myDate.getDay();
        var year  = myDate.getFullYear();
    }

   //======================== FUNCION PARA BUSCAR LA COTIZACION-PEDIDO POR SU NUMERO =======================================//
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
    //======================== VENTANA BUSCAR PEDIDO - COTIZACION ===========================================================//

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

    //======================================== BUSCAR UNA  ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }

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


    //================================== IMPRIMIR  ===========================================================//

    function imprimir<?php echo $opcGrillaContable; ?> (){
        var url = ('<?php echo $opcGrillaContable; ?>' == 'FacturaVenta')? 'facturacion/bd/imprimir_factura_venta.php': 'bd/imprimirGrillaContable.php';
        window.open(url+"?id=<?php echo $id_factura_venta; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
        window.open("bd/exportar_excel_factura_compra.php?id="+'<?php echo $id_factura_venta; ?>');
    }
     //============================ CANCELAR UN DOCUMENTO =========================================================================//

    function cancelar<?php echo $opcGrillaContable; ?>(){
        var contArticulos = 0;

        if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }

        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');

        for(i in arrayIdsArticulos){
            if(arrayIdsArticulos[i].innerHTML != '' ){ contArticulos++; }
        }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
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

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento<?php echo $opcGrillaContable ?>(){
        var texto = '';
        if ('<?php echo $opcGrillaContable; ?>' == 'RemisionesVenta' || '<?php echo $opcGrillaContable; ?>' == 'FacturaVenta'){ texto = "\nSi lo hace se eliminara el movimiento contable del mismo y\nRegresaran Los articulos al Inventario"; }

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?"+texto)) {
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    idDocumento       : '<?php echo $id_factura_venta; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_bodega         : '<?php echo $filtro_bodega; ?>',
                }
            });
        }
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar<?php echo $opcGrillaContable ?>(){
        Ext.get('render_btns_<?php echo $opcGrillaContable ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                idDocumento       : '<?php echo $id_factura_venta; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idBodega          : '<?php echo $filtro_bodega; ?>'
            }
        });
    }

    function colorFila_<?php echo $opcGrillaContable ?>(idItem){
        console.log(idItem);
    }

</script>
