<?php
    include("../../../../../configuracion/conectar.php");
    include("../../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../../funciones_globales/funciones_php/randomico.php");
    include("../../../../funciones_globales/funciones_javascript/totalesCompraVenta.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');

    $arrayTypeRetenciones = '';
?>
<script>

    var arrayTypeRetenciones  = new Array();                  // ARRAY QUE CONTIENE LAS RETENCIONES QUE NO SON DESCONTADAS DEL TOTAL

    var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   descuentoAcumulado<?php echo $opcGrillaContable; ?> = 0.00
    ,   descuento<?php echo $opcGrillaContable; ?>          = 0.00
    ,   acumuladodescuentoArticulo                          = 0.00
    ,   ivaAcumulado<?php echo $opcGrillaContable; ?>       = 0.00
    ,   total<?php echo $opcGrillaContable; ?>              = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
    ,   id_cliente_<?php echo $opcGrillaContable;?>         = 0;

    var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoCliente<?php echo $opcGrillaContable; ?>      =  0
    ,   nitCliente<?php echo $opcGrillaContable; ?>         =  0
    ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
    ,   nombre_grilla                                       = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>'//nombre de la grilla cunado se busca un articulo
    ,   nombreTabla                                         = ''; //nombre de la tabla para la grilla de buscar articulos para agregar a la nota
    //variable con la fecha del dia mas treinta dias, para cargar por defecto la fecha de vencimiento
    var fechaVencimientoFactura<?php echo $opcGrillaContable;?>  = new Date();

    arrayIva<?php echo $opcGrillaContable;?> = [];
    var objectRetenciones_<?php echo $opcGrillaContable; ?>=[];


    //ESTE ARRAY CONTENDRA LAS CANTIDADES DEL ARTICULO CARGADO ORIGINALMENTE, PARA REALIZAR LA VALIDACION DE QUE SOLO PUEDE USAR UNA CATIDAD MENOR DE ARTICULOS DEL DOCUMENTO Y NO MAYOR
    var cantidadesArticulos<?php echo $opcGrillaContable; ?> = new Array();

    Ext.getCmp("Btn_exportar_<?php echo $opcGrillaContable; ?>").enable();
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();

</script>
<?php

    //PERMISOS BOTONES
    $user_permiso_editar    = 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();';        //editar
    $user_permiso_cancelar  = 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';      //calcelar
    $user_permiso_restaurar = 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").disable();';     //restaurar

    include("../bd/functions_body_article.php");

    if ($opcGrillaContable == 'DevolucionCompra'){
        $camposSelect         = "id_proveedor,cod_proveedor,numero_documento_compra,nit,proveedor,date_format(fecha_registro,'%Y-%m-%d') AS fecha, observacion, estado,consecutivo,id_documento_compra";
        $tercero_carga        = 'proveedor';
        $id_tercero_carga     = 'id_proveedor';
        $cod_tercero_carga    = 'cod_proveedor';
        $id_documento_cargado = 'id_documento_compra';

        //PARA LA TABLA DE LAS RETENCIONES
        $idTablaCargar          = "id_factura_compra";
        $tablaCargaRetenciones  = "compras_facturas_retenciones";
        $numero_documento_cruce = 'numero_documento_compra';
    }
    else{
      $camposSelect         = "id_documento_venta,documento_venta,id_cliente,cod_cliente,nit,cliente,date_format(fecha_registro,'%Y-%m-%d') AS fecha, observacion, estado,consecutivo,numero_documento_venta,descripcion_motivo_dian,metodo_pago,response_DE";
      $tercero_carga        = 'cliente';
      $id_tercero_carga     = 'id_cliente';
      $cod_tercero_carga    = 'cod_cliente';
      $id_documento_cargado = 'id_documento_venta';

      //PARA LA TABLA DE LAS RETENCIONES
      $idTablaCargar          = "id_factura_venta";
      $tablaCargaRetenciones  = "ventas_facturas_retenciones";
      $numero_documento_cruce = 'numero_documento_venta';
    }

    $sql   = "SELECT $camposSelect FROM $tablaPrincipal  WHERE id='$id_nota' AND activo = 1";
    $query = mysql_query($sql,$link);

    $nit                      = mysql_result($query,0,'nit');
    $tercero                  = mysql_result($query,0,$tercero_carga);
    $id_tercero               = mysql_result($query,0,$id_tercero_carga);
    $cod_tercero              = mysql_result($query,0,$cod_tercero_carga);
    $numero_documento_cruce   = mysql_result($query,0,$numero_documento_cruce);
    $fecha                    = mysql_result($query,0,'fecha');
    $estado                   = mysql_result($query,0,'estado');
    $id_documento_carga       = mysql_result($query,0,$id_documento_cargado);
    $documento_cargado        = mysql_result($query,0,'documento_venta');
    $descripcion_motivo_dian  = mysql_result($query,0,'descripcion_motivo_dian');
    $metodo_pago              = mysql_result($query,0,'metodo_pago');
    $response_DE              = mysql_result($query,0,'response_DE');
    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    //VERIFICAMOS SI EL TERCERO ES EXENTO DE IVA
    $sql = "SELECT exento_iva FROM terceros WHERE id = $id_tercero AND id_empresa = $id_empresa";
    $query = mysql_query($sql,$link);

    $exento_iva  = mysql_result($query,0,'exento_iva');
    echo "<script> exento_iva_DevolucionVenta = '$exento_iva';</script>";

    if ($estado == 0) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }         //documento por editar
    if ($estado == 1) { $acumScript .='Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").enable();'.$user_permiso_editar.$user_permiso_cancelar; }         //documento generado
    else if($estado == 3){ $acumScript .= $user_permiso_restaurar.'Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").enable();'; }      //documento cancelado

    if ($documento_cargado == 'Remision') {
        $acumScript.='  document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/remisiones.png");
                        document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
                        document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");
                        document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Remision</b>";
                        document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Click para cargar una Factura");
                        document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
                        Ext.getCmp("Btn_enviar_devolucion_electronica_'.$opcGrillaContable.'").disable();';
    }
    elseif ($documento_cargado == 'Factura') {

        $acumScript .= 'document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("src","img/factura.png");
                        document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("width","20px");
                        document.getElementById("imgCargarDesde'.$opcGrillaContable.'").setAttribute("height","20px");
                        document.getElementById("textoFacturardesde'.$opcGrillaContable.'").innerHTML="<b>Factura</b>";
                        document.getElementById("divContenedorCargarDesde'.$opcGrillaContable.'").setAttribute("title","Click para cargar una Remision");
                        document.getElementById("cotizacionPedido'.$opcGrillaContable.'").focus();
                        document.getElementById("descripcionMotivoDian'.$opcGrillaContable.'").value  = "'.$descripcion_motivo_dian.'";
                        document.getElementById("metodoPagoCliente'.$opcGrillaContable.'").value      = "'.$metodo_pago.'";';

        if($response_DE == "Ejemplar recibido exitosamente pasara a verificacion"){
          $acumScript .= 'Ext.getCmp("Btn_enviar_devolucion_electronica_'.$opcGrillaContable.'").disable();';
        } else{
          $acumScript .= 'Ext.getCmp("Btn_enviar_devolucion_electronica_'.$opcGrillaContable.'").enable();';
        }
    }

    $acumScript .= 'document.getElementById("codCliente'.$opcGrillaContable.'").value             = "'.$cod_tercero.'";
                    document.getElementById("nitCliente'.$opcGrillaContable.'").value             = "'.$nit.'";
                    document.getElementById("nombreCliente'.$opcGrillaContable.'").value          = "'.$tercero.'";
                    document.getElementById("fecha'.$opcGrillaContable.'").value                  = "'.$fecha.'";

                    id_cliente_'.$opcGrillaContable.'   = "'.$id_tercero.'";
                    codigoCliente'.$opcGrillaContable.' = "'.$cod_tercero.'";
                    nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                    nombreCliente'.$opcGrillaContable.' = "'.$tercero.'";';

    $bodyArticle = cargaArticulosSave($id_nota,$observacion,$estado,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$tablaCarga,$idTablaCarga,$link);
    if ($documento_cargado=='Factura') {
        $acumScript.=cargaDivsGruposItems($id_nota,$id_documento_carga,'devoluciones_venta_grupos',$opcGrillaContable,$estado,$id_empresa,$link);
    }

    //CONSULTAMOS LAS RETENCIONES, SI LAS HAY, Y LAS MOSTRAMOS EN LA NOTA
    $sqlRetenciones   = "SELECT id,retencion,valor,tipo_retencion,base FROM $tablaCargaRetenciones WHERE $idTablaCargar='$id_documento_carga' AND activo=1";
    $queryRetenciones = mysql_query($sqlRetenciones,$link);
    while ($row = mysql_fetch_array($queryRetenciones)) {
        $arrayTypeRetenciones .= 'arrayTypeRetenciones['.$row['id'].'] = "'.$row['tipo_retencion'].'";';
        $checkboxRetenciones[$row['id']] = '<div class="campoCheck" title="'.$row['retencion'].'">
                                                <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" name="checkbox'.$opcGrillaContable.'" checked value="'.$row['valor'].'" onchange="this.checked=true" />
                                                <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                    <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                    <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                </label>
                                            </div>';

        $objectRetenciones[$row['id']]  = 'objectRetenciones_'.$opcGrillaContable.'['.$row['id'].'] = {'
                                                                                .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                .'base           : "'.$row['base'].'",'
                                                                                .'valor          : "'.$row['valor'].'",'
                                                                                .'estado         : "0"'
                                                                            .'}';
    }
    $plainRetenciones = implode(';', $objectRetenciones).';';
    echo '<script>
            exento_iva_'.$opcGrillaContable.'="'.$exento_iva.'";
            '.$plainRetenciones.' '.$arrayTypeRetenciones.'
        </script>';

    $terceroMostrar='Cliente';
    if($documento_cargado == ''){ $documento_cruce = 'FC'; $terceroMostrar='Proveedor'; }
    else if($documento_cargado == 'Factura'){ $documento_cruce = 'FV'; }
    else{ $documento_cruce = 'RV'; }
?>
<div class="contenedorOrdenCompra" style="width:100% !important">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <?php echo $imgBloqueo; ?>
                <div class="renglonTop">
                    <div class="labelTop">Documento Cruce</div>
                    <div class="campoTop campoDocCruce">
                        <div class="labelTypeDocCruce" title="<?php echo $title_cruce; ?>"><?php echo $documento_cruce; ?></div>
                        <div class="numeroTypeDocCruce" ><?php echo $numero_documento_cruce; ?></div>
                    </div>
                </div>
                <div id="renderRestaurar<?php echo $opcGrillaContable; ?>" style="width:20px; overflow:hidden; float:right;"></div>
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Codigo</div>
                    <div class="campoTop"><input type="text" id="codCliente<?php echo $opcGrillaContable; ?>" readonly ></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" id="nitCliente<?php echo $opcGrillaContable; ?>" readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop"><?php echo $terceroMostrar; ?></div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
                </div>
                <?php if($documento_cargado == 'Factura'){ ?>
                        <div class="renglonTop">
                          <div class="labelTop">Metodo de Pago</div>
                          <div class="campoTop">
                            <input type="text" id="metodoPagoCliente<?php echo $opcGrillaContable; ?>" readonly />
                          </div>
                        </div>
                <?php } ?>
                <div class="renglonTop" id="checksRetenciones<?php echo $opcGrillaContable; ?>">
                    <div class="labelTop">Retenciones</div>
                    <div class="contenedorCheckbox">
                        <?php foreach ($checkboxRetenciones as $valor) { echo  $valor; } ?>
                    </div>
                </div>
                <?php if($documento_cargado == 'Factura'){ ?>
                        <div class="renglonTop">
                          <div class="labelTop">Motivo Devolucion</div>
                          <div class="campoTop" style="width:277px;">
                            <input type="text" id="descripcionMotivoDian<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/>
                          </div>
                        </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>
<script>
    <?php
        echo $acumScript;
        echo "var observacion".$opcGrillaContable." = '';";
        if($documento_cargado == 'Remision'){ echo 'document.getElementById("checksRetenciones'.$opcGrillaContable.'").style.display="none";'; }    //CONDICION PARA OCULTAR EL CAMPO RETENCIONES SI ES UNA REMISION
    ?>

    //======================== FUNCION PARA BUSCAR LA COTIZACION-PEDIDO POR SU NUMERO =======================================//
    function buscarCotizacionPedido<?php echo $opcGrillaContable; ?>(event,Input){
        tecla  = (Input) ? event.keyCode : event.which;
        numero = Input.value;

        if(tecla == 13){ ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
    }

    function ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido,idDocumento){

        if ('<?php echo $opcGrillaContable; ?>'=='DevolucionVenta') {

            if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {
                tablaBuscar = "ventas_remisiones";
                opcCargar   = "remisionVenta";
            }
            else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/factura.png"){
                opcCargar   = "facturaVenta";
                tablaBuscar = "ventas_facturas";
            }
        }
        else{
            titulo      = "Seleccione la Factura de Compra";
            opcCargar   = "facturaCompra";
            tablaBuscar = "";
        }

        divRender = Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>");

        Ext.get(divRender).load({
            url     : "notas_inventario/notas_devolucion/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCotizacionPedido',
                opcCargar         : opcCargar,
                tablaBuscar       : tablaBuscar,
                id                : idCotizacionPedido,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value,
                idDocumento       : idDocumento
            }
        });
    }

    function ajaxCambia<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="contTopFila" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor y se deshabilitan
        var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
        for(i in checks){ checks[i].checked=false; checks[i].disabled=true; }

        Ext.get("contenedor_facturacion_compras").load({
            url     : "facturacion_compras.php",
            scripts : true,
            nocache : true,
            params  : { filtro_bodega   : document.getElementById("filtro_ubicacion_facturacion_compras").value }
        });
    }

    //======================== VENTANA BUSCAR PEDIDO - COTIZACION ===========================================================//
    function ventanaBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(){

        if ('<?php echo $opcGrillaContable; ?>'=='DevolucionVenta') {

            if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {
                id_tabla_carga         = "id_remision_venta";
                tabla_inventario_carga = "ventas_remisiones_inventario";

                titulo      = "Seleccione la remision";
                opcCargar   = "remisionVenta";
                tablaGrilla = "ventas_remisiones";
                nombreGrillaCotizacionPedido = "grillaNotaRemision";

            }
            else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/factura.png"){
                id_tabla_carga         = "id_factura_venta";
                tabla_inventario_carga = "ventas_facturas_inventario";

                opcCargar   = "facturaVenta";
                titulo      = "Seleccione la factura";
                tablaGrilla = "ventas_facturas";
                nombreGrillaCotizacionPedido = "grillaNotaFactura";
            }
        }
        else{
            id_tabla_carga         = "id_factura_compra";
            tabla_inventario_carga = "compras_facturas_inventario";

            opcCargar   = 'facturaCompra';
            titulo      = "Seleccione la factura";
            tablaGrilla = "compras_facturas";
            nombreGrillaCotizacionPedido ="grillaNotaFactura";
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
                url     : 'notas_inventario/notas_devolucion/bd/grillaBuscarCotizacionPedido.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                          : 'buscar_cotizacionPedido',
                    opcGrillaContable            : '<?php echo $opcGrillaContable; ?>',
                    opcCargar                    : opcCargar,
                    tablaCotizacionPedido        : tablaGrilla,
                    id_tabla_carga               : id_tabla_carga,
                    tabla_inventario_carga       : tabla_inventario_carga,
                    nombreGrillaCotizacionPedido : nombreGrillaCotizacionPedido,
                    filtro_bodega                : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
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
    function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){
        //antes de eliminar tomamos las variable para enviarlas a la funcion para recalcular los totales
        var idArticulo<?php echo $opcGrillaContable; ?> = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo<?php echo $opcGrillaContable; ?>      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var descuentoArticulo<?php echo $opcGrillaContable; ?> = document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo<?php echo $opcGrillaContable; ?>     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var iva=document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDesc='';

        if (document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png') { tipoDesc='porcentaje';}
        else{ tipoDesc='pesos'; }

        if(confirm('Esta seguro de eliminar este articulo de la nota de devolucion?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'notas_inventario/notas_devolucion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : idArticulo<?php echo $opcGrillaContable; ?>,
                    cont              : cont,
                    id                : '<?php echo $id_nota; ?>'
                }
            });
            calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo<?php echo $opcGrillaContable; ?>,descuentoArticulo<?php echo $opcGrillaContable; ?>,costoArticulo<?php echo $opcGrillaContable; ?>,'eliminar',tipoDesc,iva,cont);
        }
    }

    //================================================= BUSCAR   ================================================//
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
                url     : 'notas_inventario/notas_devolucion/bd/buscarGrillaContable.php',
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

    //================================== IMPRIMIR EN PDF ==================================================================//
    function imprimir<?php echo $opcGrillaContable; ?> (){
        if ('<?php echo $opcGrillaContable; ?>'=='DevolucionVenta') {
                if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {
                    opcCargar  = "remisionVenta";
                }else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/factura.png"){
                    opcCargar    = "facturaVenta";
                }
        }
        else{
            opcCargar="facturaCompra";
        }

        window.open("notas_inventario/notas_devolucion/bd/imprimirGrillaContable.php?id=<?php echo $id_nota; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&opcCargar="+opcCargar);
    }

    //================================== IMPRIMIR EN EXCEL =================================================================//
    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
       window.open("notas_inventario/notas_devolucion/bd/exportarExcelGrillaContable.php?id=<?php echo $id_nota; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    //============================ CANCELAR UN DOCUMENTO =========================================================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){

        if ('<?php echo $opcGrillaContable; ?>'=='DevolucionVenta') {
                if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {
                    opcCargar  = "remisionVenta";
                }else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/factura.png"){
                    opcCargar    = "facturaVenta";
                }
        }
        else{
            opcCargar="facturaCompra";
        }

        if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado\nSe eliminara toda la contabilidad creada por el y se actualizara el inventario\nDesea continuar?')){
            cargando_documentos('Cancelando Documento');
            Ext.get("terminar<?php echo $opcGrillaContable; ?>").load({
                url  : 'notas_inventario/notas_devolucion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'cancelarDocumento',
                    id                : '<?php echo $id_nota; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idBodega          : '<?php echo $filtro_bodega; ?>',
                    opcCargar         : opcCargar
                }
            });
        };

    }

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento<?php echo $opcGrillaContable ?>(){

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo y\nSe moveran Los articulos del Inventario")) {

            if ('<?php echo $opcGrillaContable; ?>'=='DevolucionVenta') {
                //SI VAMOS A CARGAR A UNA FACTURA
                if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {
                    //cargar factura desde una cotizacion
                    opcCargar  = "remisionVenta";
                    }else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/factura.png"){
                        //cargar factura desde un pedido
                        opcCargar    = "facturaVenta";
                    }
                }
                else{
                    opcCargar="facturaCompra";
                }
            cargando_documentos('Editando Documento');
            Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
                url     : 'notas_inventario/notas_devolucion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id                : '<?php echo $id_nota; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_bodega         : '<?php echo $filtro_bodega; ?>',
                    opcCargar         : opcCargar,
                    idDocumentoCarga  : '<?php echo $id_documento_carga; ?>'
                }
            });
        }
    }

    //============================ LIMPIAR LA GRILLA SI SE MUEVE LA OPCION DE CARGAR DOCUMENTO ==============================//
    function limpiarGrillaContable(opc,filtro_bodega){


        if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {

            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("src","img/factura.png");
            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("width","20px");
            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("height","20px");

            document.getElementById("textoFacturardesde<?php echo $opcGrillaContable; ?>").innerHTML="<b>Factura</b>";
            document.getElementById("divContenedorCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("title","Click para cargar una Remision");
            document.getElementById("cotizacionPedido<?php echo $opcGrillaContable; ?>").focus();

        }else{

            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("src","img/remisiones.png");
            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("width","20px");
            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("height","20px");
            document.getElementById("textoFacturardesde<?php echo $opcGrillaContable; ?>").innerHTML="<b>Remision</b>";
            document.getElementById("divContenedorCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("title","Click para cargar una Factura");

            document.getElementById("cotizacionPedido<?php echo $opcGrillaContable; ?>").focus();

        }

        Ext.get("contenedor_"+opc).load({
                        url     : "notas_inventario/notas_devolucion/default.php",
                        scripts : true,
                        nocache : true,
                        params  :
                        {
                            filtro_bodega : filtro_bodega,
                            opcGrillaContable: opc
                        }
                    });
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar<?php echo $opcGrillaContable ?>(){
        Ext.get('renderRestaurar<?php echo $opcGrillaContable ?>').load({
            url     : 'notas_inventario/notas_devolucion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id                : '<?php echo $id_nota; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idBodega          : '<?php echo $filtro_bodega; ?>'
            }
        });
    }

    //===================== ENVIAR DEVOLUCION ELECTRONICA ====================//
    function enviarDIAN<?php echo $opcGrillaContable; ?>(){
      var opcion = confirm('\u00BFEstas seguro de enviar la devolucion electronica?');
      if(opcion == true){
        cargando_documentos('Enviando Documento...','');
        Ext.get('terminar<?php echo $opcGrillaContable ?>').load({
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          scripts : true,
          nocache : true,
          params  : {
                      opc               : 'enviarDevolucionDIAN',
                      id_devolucion     : '<?php echo $id_nota; ?>',
                      opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                    }
        });
      }
    }

</script>
