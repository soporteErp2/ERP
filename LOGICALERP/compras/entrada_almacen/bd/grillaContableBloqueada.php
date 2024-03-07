<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../../config_var_global.php");
    include("../../../funciones_globales/funciones_javascript/totalesCompraVenta.php");

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
    include("../bd/functions_body_article.php");

    if ($opcGrillaContable == 'CotizacionVenta'){ $titulo = 'Cotizacion de Venta'; $arrayPermisos = array(6, 7, 8, 9); }
    else if ($opcGrillaContable == 'PedidoVenta'){ $titulo = 'Pedido de Venta'; $arrayPermisos = array(11, 12, 13, 14); }
    else if ($opcGrillaContable == 'RemisionesVenta'){ $arrayPermisos = array(16, 17, 18, 19); }
    else if ($opcGrillaContable == 'FacturaVenta'){ $arrayPermisos = array(21, 22, 23, 24); }
    else if ($opcGrillaContable == 'EntradaAlmacen'){ $arrayPermisos = array(176, 177,178, 179); }

    list($permisoGuardar, $permisoEditar, $permisoEliminar, $permisoRestaurar) = $arrayPermisos;

    $user_permiso_editar    = (user_permisos($permisoEditar,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos($permisoEliminar,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos($permisoRestaurar,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';     //restaurar

    /*$user_permiso_editar    = 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();';
    $user_permiso_cancelar  = 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';
    $user_permiso_restaurar = 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();';*/

    //el campo numero de factura solo esta en la tabla de ventas_facturas, si que verificamos que opcGrillaContable sea ventas facturas para pasar la cadena sql con ese campo, sino se pasa si ese campo
    $div_sucursal_cliente = '';
    $titulo='Entrada de Almacen';
    $sql  = "SELECT id_proveedor,
                    cod_proveedor,
                    nit,
                    proveedor,
                    consecutivo,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                    date_format(fecha_finalizacion,'%Y-%m-%d') AS fechaFin,
                    observacion,
                    estado,
                    id_usuario,
                    usuario,
                    tipo_entrada,
                    codigo_centro_costo,
                    centro_costo
            FROM $tablaPrincipal  WHERE id='$id_documento' AND activo = 1";

    $query = mysql_query($sql,$link);

    $nit            = mysql_result($query,0,'nit');
    $cliente        = mysql_result($query,0,'proveedor');
    $id_cliente     = mysql_result($query,0,'id_proveedor');
    $cod_cliente    = mysql_result($query,0,'cod_proveedor');
    $fechaInicio    = mysql_result($query,0,'fecha');
    $fechaFin       = mysql_result($query,0,'fechaFin');
    $estado         = mysql_result($query,0,'estado');
    $nombre_usuario = mysql_result($query,0,'usuario');
    $consecutivo    = mysql_result($query,0,'consecutivo');
    $idUsuario      = mysql_result($query,0,'id_usuario');
    $tipo_entrada   = mysql_result($query,0,'tipo_entrada');
    $labelCcos      = mysql_result($query,0,'codigo_centro_costo').' - '.mysql_result($query,0,'centro_costo');

    if($prefijo != ''){ $numero_factura = $prefijo.' '.$numero_factura; }
    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .= 'id_cliente_'.$opcGrillaContable.'
                    observacion'.$opcGrillaContable.' = "'.$observacion.'";
                    ';

    if(!isset($fechaInicio)){
        $fechaInicio = date('Y-m-d');
    }

    $bodyArticle = cargaArticulosSave($id_documento,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

    if ($estado == '1'){ $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }
    else if($estado == '2'){  $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento Cruzado">'; }     //documento cruzado con otro
    else if ($estado == '3') { $acumScript .= $user_permiso_restaurar; }
    else{ $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento Cruzado">'; }


    if ($tipo_entrada=='AI') { $acumScript .='document.getElementById("div_content_ccos").style.display="block";'; }
    $tipo_entrada=($tipo_entrada=='AI')? 'Ajuste de Inventario' : 'Entrada de Almacen' ;

    //=============================// CDOCUMENTOS CRUCE //=============================//
    //*********************************************************************************//
    $divCruce  = '';
    $cruce     = '';
    $contCruce = 0;

    $acumDocReferencia  = '';
    $margin_left        = 'margin-left:5px';
    $sqlDocReferencia   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
                            FROM $tablaInventario
                            WHERE id_consecutivo_referencia>0 AND $idTablaPrincipal='$id_documento' AND activo=1
                            ORDER BY id ASC";
    $queryDocReferencia = mysql_query($sqlDocReferencia,$link);

    while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){

        if($rowDocReferencia['doc_referencia'] == 'R'){ $title = 'Eliminar los Articulos de la Requisicion'; }
        else if($rowDocReferencia['doc_referencia'] == 'O'){ $title = 'Eliminar los Articulos de la Orden de Compra'; }

        $typeDocCruce   = $rowDocReferencia['doc_referencia'];
        $numeroDocCruce = $rowDocReferencia['consecutivo_referencia'];

        $acumDocReferencia .='<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;height: 22px;" id="divDocReferencia'.$opcGrillaContable.'_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                    <div class="contenedorInputDocReferenciaFactura">
                                        <input type="text" class="inputDocReferenciaFactura" value="'.$rowDocReferencia['doc_referencia'].' '.$rowDocReferencia['cod_referencia'].'" style="border-bottom: 1px solid #d4d4d4;" readonly/>
                                    </div>
                                    <div title="'.$title.' # '.$rowDocReferencia['cod_referencia'].' en la presente entrada de almacen" onclick="eliminaDocReferencia'.$opcGrillaContable.'(\''.$rowDocReferencia['id_referencia'].'\',\''.$rowDocReferencia['doc_referencia'].'\',\''.$id_documento.'\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;display:none">
                                        <div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btn'.$opcGrillaContable.'_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                                        </div>
                                    </div>
                              </div>';
    }


?>

<div class="contenedorOrdenCompra">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <?php echo $divImagen ; ?>
                <div class="renglonTop">
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fechaInicio; ?>" Readonly /></div>
                </div>
                <div class="renglonTop" style="display:none">
                    <div class="labelTop">Fecha Final</div>
                    <div class="campoTop"><input type="text" id="fechaFinal<?php echo $opcGrillaContable; ?>" value="<?php echo $fechaFin; ?>" Readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Docs. Cruce</div>
                    <div class="campoTop" style="height:auto;"  id="contenedorDocsReferencia<?php echo $opcGrillaContable; ?>"><?php echo $acumDocReferencia; ?></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Codigo</div>
                    <div class="campoTop"><input type="text" value="<?php echo $cod_cliente; ?>" Readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" value="<?php echo $nit; ?>" Readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Proveedor</div>
                    <div class="campoTop" style="width:277px;"><input type="text" style="width:100%" value="<?php echo $cliente ?>" Readonly /></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tipo <img src="../nomina/img/help.png" onclick="ventanaAyuda()" style="width:19px;height:19px;float:right;margin-top:-3px;cursor:help;"></div>
                    <div class="campoTop"><input type="text" value="<?php echo $tipo_entrada; ?>" Readonly /></div>
                </div>

                <div class="renglonTop" id="div_content_ccos" style="width:137px;display:none;">
                    <div class="labelTop">Centro de Costo </div>
                    <div class="campoTop"><input type="text" value="<?php echo $labelCcos; ?>" Readonly /></div>
                </div>

                <!-- <div class="renglonTop" style="width:137px;display:none;" id="div_content_ccos">
                    <div class="labelTop" style="float:left; width:100%;">Centro de Costo</div>
                    <div id="renderSelectCcos" style="float:left; margin-left:-22px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop"><input type="text" id="cCos_<?php echo $opcGrillaContable; ?>" value="<?php echo $labelCcos; ?>" Readonly/></div>
                    <div class="iconBuscarProveedor" onclick="ventanaCcos<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Centro de Costo">
                       <img src="img/buscar20.png"/>
                    </div>
                </div> -->

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" style="width:100%" value="<?php echo $nombre_usuario; ?>" Readonly /></div>
                </div>

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
            url     : "entrada_almacen/bd/bd.php",
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
                    id_documento          : '<?php echo $id_documento; ?>',
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
                url     : 'entrada_almacen/bd/buscarGrillaContable.php',
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
        var url = 'entrada_almacen/bd/imprimir_entrada_almacen.php';
        window.open(url+"?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>");
    }

    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
        window.open("bd/exportar_excel_factura_compra.php?id="+'<?php echo $id_documento; ?>');
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
                cargando_documentos('Cancelando Documento...','');
                Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                    url  : 'entrada_almacen/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc               : 'cancelarDocumento',
                        id                : '<?php echo $id_documento; ?>',
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
            cargando_documentos('Editando Documento...','');
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'entrada_almacen/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id                : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_bodega         : '<?php echo $filtro_bodega; ?>',
                }
            });
        }
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar<?php echo $opcGrillaContable ?>(){
        cargando_documentos('Restaurando Documento...','');
        Ext.get('render_btns_<?php echo $opcGrillaContable ?>').load({
            url     : 'entrada_almacen/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id       : '<?php echo $id_documento; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idBodega          : '<?php echo $filtro_bodega; ?>'
            }
        });
    }

    function colorFila_<?php echo $opcGrillaContable ?>(idItem){
        console.log(idItem);
    }

    function validarArticulos<?php echo $opcGrillaContable ?>(){ return 2; }

</script>
