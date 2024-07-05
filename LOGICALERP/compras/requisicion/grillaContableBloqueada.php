<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    //include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php"); LOS CALCULOS DE LOS TOTALES ESTAN DESACTIVADOS

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fechaActual = date('Y-m-d');
    $divImagen   = '';

    $arrayTypeRetenciones = '';

    $styleCamposProveedor = 'display:none';//CONTROL PARA OCULTAR LOS CAMPOS DEL PROVEEDOR
?>
<script>

    var arrayTypeRetenciones  = new Array();// ARRAY QUE CONTIENE LAS RETENCIONES QUE NO SON DESCONTADAS DEL TOTAL

    //variables para calcular los valores de los costos y totales de la factura
    var subtotalAcumulado<?php echo $opcGrillaContable; ?> = 0.00
    ,   ivaAcumulado<?php echo $opcGrillaContable; ?>      = 0.00
    ,   totalAcumulado<?php echo $opcGrillaContable; ?>    = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>     = 1
    ,   id_proveedor_<?php echo $opcGrillaContable; ?>     = 0;

    var objectRetenciones_<?php echo $opcGrillaContable; ?>=[];

    //Bloqueo todos los botones
    // Ext.getCmp("Btn_upload_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").enable();

</script>
<?php
    include("bd/functions_body_article.php");

    if ($opcGrillaContable=='RequisicionCompra'){ $titulo = 'Requisicion de Compra'; $arrayPermisos = array(171, 172, 173, 174); }
    else if ($opcGrillaContable == 'PedidoVenta'){ $titulo = 'Pedido de Venta'; $arrayPermisos = array(11, 12, 13, 14); }
    else if ($opcGrillaContable == 'RemisionesVenta'){ $arrayPermisos = array(16, 17, 18, 19); }
    else if ($opcGrillaContable == 'FacturaVenta'){ $arrayPermisos = array(21, 22, 23, 24); }

    list($permisoGuardar, $permisoEditar, $permisoEliminar, $permisoRestaurar) = $arrayPermisos;

    $user_permiso_editar    = (user_permisos($permisoEditar,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos($permisoEliminar,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos($permisoRestaurar,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';     //restaurar
    $user_permiso_anexar    = 'Ext.getCmp("Btn_upload_'.$opcGrillaContable.'").enable();';

    /*$user_permiso_editar    = 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();';
    $user_permiso_cancelar  = 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';
    $user_permiso_restaurar = 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();';*/

    //el campo numero de factura solo esta en la tabla de ventas_facturas, si que verificamos que opcGrillaContable sea ventas facturas para pasar la cadena sql con ese campo, sino se pasa si ese campo
    $div_sucursal_cliente = '';

    $sql   = "SELECT
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                    date_format(fecha_vencimiento,'%Y-%m-%d') AS fechaFin,
                    DATE_ADD(fecha_inicio, INTERVAL 24 MONTH) AS fecha_bloqueo,
                    observacion,
                    estado,
                    nombre_solicitante,
                    id_area_solicitante,
                    codigo_area_solicitante,
                    area_solicitante,
                    id_centro_costo,
                    codigo_centro_costo,
                    centro_costo,
                    tipo_nombre,
                    usuario

            FROM $tablaPrincipal  WHERE id='$id_documento' AND activo = 1";


    $query = mysql_query($sql,$link);

    $fecha                   = mysql_result($query,0,'fecha');
    $fechaFin                = mysql_result($query,0,'fechaFin');
    $estado                  = mysql_result($query,0,'estado');
    $nombre_solicitante      = mysql_result($query,0,'nombre_solicitante');
    $id_area_solicitante     = mysql_result($query,0,'id_area_solicitante');
    $codigo_area_solicitante = mysql_result($query,0,'codigo_area_solicitante');
    $area_solicitante        = mysql_result($query,0,'area_solicitante');
    $codigo_centro_costo     = mysql_result($query,0,'codigo_centro_costo');
    $centro_costo            = mysql_result($query,0,'centro_costo');
    $tipo_nombre             = mysql_result($query,0,'tipo_nombre');
    $usuario                 = mysql_result($query,0,'usuario');

    $centro_costo=($codigo_centro_costo<>'')? $codigo_centro_costo.' - '.$centro_costo : $centro_costo ;

    $labelCcos = $codigoCcos.' '.$nombreCcos;

    if($prefijo != ''){ $numero_factura = $prefijo.' '.$numero_factura; }
    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .= 'id_proveedor_'.$opcGrillaContable.'
                    observacion'.$opcGrillaContable.' = "'.$observacion.'";
                    ';

    $bodyArticle = cargaArticulosSave($id_documento,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);


    if ($estado == '1'){ $acumScript .= $user_permiso_editar.$user_permiso_cancelar.$user_permiso_anexar; }
    else if($estado == '2'){  $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento Cruzado">'; $acumScript .= $user_permiso_editar.$user_permiso_cancelar.$user_permiso_anexar;}     //documento cruzado con otro
    else if ($estado == '3') { $acumScript .= $user_permiso_restaurar; }
    else{ $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento Cruzado">'; }



?>

<div class="contenedorRequisicionCompra">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <?php echo $divImagen ; ?>
                <div class="renglonTop">
                    <div class="labelTop">Fecha Inicio</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" Readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Fecha Vencimiento</div>
                    <div class="campoTop"><input type="text" id="fechaFin<?php echo $opcGrillaContable; ?>" value="<?php echo $fechaFin; ?>" Readonly /></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Persona Solicitante</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreSolcitante<?php echo $opcGrillaContable; ?>" style="width:100%" readonly value="<?php echo $nombre_solicitante; ?>"> </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Area o Departamento Solicitante</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="areaSolcitante<?php echo $opcGrillaContable; ?>" style="width:100%" readonly value="<?php echo $area_solicitante; ?>"/></div>
                </div>

               <!--  <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Centro de Costo</div>
                    <div class="campoTop"><input type="text" id="cCos_<?php echo $opcGrillaContable; ?>" Readonly value="<?php echo $centro_costo; ?>"/></div>
                </div>
 -->
                <div class="renglonTop" style="<?php echo $styleCamposProveedor; ?>">
                    <div class="labelTop">Codigo</div>
                    <div class="campoTop"><input type="text" value="<?php echo $cod_proveedor; ?>" Readonly /></div>
                </div>
                <div class="renglonTop" style="<?php echo $styleCamposProveedor; ?>">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" value="<?php echo $nit; ?>" Readonly /></div>
                </div>
                <div class="renglonTop" style="<?php echo $styleCamposProveedor; ?>">
                    <div class="labelTop">Proveedor</div>
                    <div class="campoTop" style="width:277px;"><input type="text" style="width:100%" value="<?php echo $proveedor ?>" Readonly /></div>
                </div>
                <div class="renglonTop" style="display:none">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" style="width:100%" value="<?php echo $nombre_vendedor; ?>" Readonly /></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tipo</div>
                    <div class="campoTop"><input type="text" style="width:100%" value="<?php echo $tipo_nombre; ?>" Readonly /> </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div style="float:right; margin-left:-25px; margin-top: -18px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="width:277px;"><input type="text" value="<?php echo $usuario; ?>" style="width:100%" readonly></div>
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


    document.getElementById('fecha<?php echo $opcGrillaContable; ?>').style.overflow='hidden !important';

        //======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA ==========================================//
    function ventanaDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,idInsertArticulo){
             var id = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_descripcion_Articulo_factura = new Ext.Window({
            width       : 330,
            height      : 280,
            id          : 'Win_Ventana_descripcion_Articulo_factura',
            title       : 'Observacion articulo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'requisicion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaDescripcionArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : id,
                    cont              : cont,
                    id                : '<?php echo $id_documento; ?>',

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
                    handler     : function(){ Win_Ventana_descripcion_Articulo_factura.close(id) }
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
                url     : 'requisicion/bd/buscarGrillaContable.php',
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

    function imprimir<?php echo $opcGrillaContable; ?> (opc){
        if (opc=='pdf') {
            window.open("requisicion/bd/imprimir_requisicion.php?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>");
        }
        else if (opc=='xls') {
            window.open("requisicion/bd/exportar_requisicion_excel.php?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>");
        }
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
                if ('<?php echo $estado; ?>' != 2){
                    cargando_documentos('Cancelando Documento...','');
                }
                Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                    url  : 'requisicion/bd/bd.php',
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
    function modificar<?php echo $opcGrillaContable ?>(){
        var texto = '';
        if ('<?php echo $opcGrillaContable; ?>' == 'RemisionesVenta' || '<?php echo $opcGrillaContable; ?>' == 'FacturaVenta'){ texto = "\nSi lo hace se eliminara el movimiento contable del mismo y\nRegresaran Los articulos al Inventario"; }

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?"+texto)) {
            if ('<?php echo $estado; ?>' != 2){
                cargando_documentos('Editando Documento...','');
            }
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'requisicion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id       : '<?php echo $id_documento; ?>',
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
            url     : 'requisicion/bd/bd.php',
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

    function ventanaAutorizarRequisicionCompra(){

        Win_Ventana_autoriza_documento = new Ext.Window({
            width       : 635,
            height      : 350,
            id          : 'Win_Ventana_autoriza_documento',
            title       : 'Ventana de Verificacion del Documento',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'requisicion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaAutorizaDocumento',
                    id                : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idBodega          : '<?php echo $filtro_bodega; ?>'
                }
            },
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Opciones',
                    style   : 'border-right:none;',
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
                            hidden      : false,
                            handler     : function(){ BloqBtn(this); Win_Ventana_autoriza_documento.close(id) }
                        }
                    ]
                }
            ]
        }).show();

    }

    function autorizar<?php echo $opcGrillaContable ?>(id_row,id_area,orden) {
        var tipo_autorizacion = document.getElementById('tipo_autorizacion_'+id_row).value;
        // if (tipo_autorizacion=='') {return;}

        MyLoading2('on');

        Ext.get('loadAut').load({
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                : 'autorizar<?php echo $opcGrillaContable;?>',
                id_documento       : '<?php echo $id_documento; ?>',
                opcGrillaContable  : '<?php echo $opcGrillaContable; ?>',
                idBodega           : '<?php echo $filtro_bodega; ?>',
                tipo_autorizacion  : tipo_autorizacion,
                // id_autorizacion : id_autorizacion,
                id_area            : id_area,
                orden              : orden,
            }
        });
    }

</script>
