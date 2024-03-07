<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    //include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php"); LOS CALCULOS DE LOS TOTALES ESTAN DESACTIVADOS

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $id_usuario  = $_SESSION['IDUSUARIO'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');

?>
<script>
    //variables para calcular los valores de los costos y totales de la factura
    var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   descuentoAcumulado<?php echo $opcGrillaContable; ?> = 0.00
    ,   descuento<?php echo $opcGrillaContable; ?>          = 0.00
    ,   acumuladodescuentoArticulo                          = 0.00
    ,   arrayBodegas_<?php echo $opcGrillaContable;?>       = new Array()
    ,   ivaAcumulado<?php echo $opcGrillaContable; ?>       = 0.00
    ,   total<?php echo $opcGrillaContable; ?>              = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
    ,   id_cliente_<?php echo $opcGrillaContable;?>         = 0;
    <?php echo $acumScript; ?>


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
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").enable();

</script>
<?php

    // echo "string";
    $user_permiso_editar    = (user_permisos(241,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos(242,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos(243,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';     //restaurar

    // print_r($_SESSION["PERMISOS"]);
    include("bd/functions_body_article.php");
    $sql   = "SELECT
                    fecha_documento,
                    id_sucursal,
                    id_bodega,
                    documento_usuario,
                    usuario,
                    id_sucursal_traslado,
                    sucursal_traslado,
                    id_bodega_traslado,
                    bodega_traslado,
                    observacion,
                    estado
                FROM $tablaPrincipal
                WHERE id='$id_documento' AND activo = 1";
    $query = mysql_query($sql,$link);

    $fecha_documento      = $mysql->result($query,0,'fecha_documento');
    $id_sucursal          = $mysql->result($query,0,'id_sucursal');
    $id_bodega            = $mysql->result($query,0,'id_bodega');
    $documento_usuario    = $mysql->result($query,0,'documento_usuario');
    $usuario              = $mysql->result($query,0,'usuario');
    $id_sucursal_traslado = $mysql->result($query,0,'id_sucursal_traslado');
    $sucursal_traslado    = $mysql->result($query,0,'sucursal_traslado');
    $id_bodega_traslado   = $mysql->result($query,0,'id_bodega_traslado');
    $bodega_traslado      = $mysql->result($query,0,'bodega_traslado');
    $estado               = $mysql->result($query,0,'estado');

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion        = str_replace($arrayReplaceString, "\\n", $mysql->result($query,0,'observacion'));

    $bodyArticle = cargaArticulosSave($id_documento,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$mysql);

    if ($estado == '1'){ $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }
    else if($estado == '2'){ $acumScript .= $user_permiso_editar.$user_permiso_cancelar.$user_permiso_anexar;}     //documento cruzado con otro
    else if ($estado == '3') { $acumScript .= $user_permiso_restaurar; }

?>

<div class="contenedorTraslados" id>
    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 19px;overflow: hidden;margin-top: -3px;"></div>
                    <div class="labelTop">Fecha Documento</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha_documento; ?>" readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div style="float:right; margin-left:-25px; margin-top: -18px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" value="<?php echo $usuario; ?>" style="width:100%" readonly></div>
                </div>

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 19px;overflow: hidden;margin-top: -3px;"></div>
                    <div class="labelTop">Sucursal Destino</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $sucursal_traslado; ?>" readonly></div>
                </div>

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 19px;overflow: hidden;margin-top: -3px;"></div>
                    <div class="labelTop">Bodega Destino</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $bodega_traslado; ?>" readonly></div>
                </div>

            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>
<div id="loadForm" style="display:none;"></div>
<script>
    <?php echo $acumScript; ?>

    //======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA ==========================================//
    function ventanaDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,idInsertArticulo,observacionArt){
            var id = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

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
                    url     : 'traslados/bd/bd.php',
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
                    // {
                    //     xtype       : 'button',
                    //     text        : 'Guardar',
                    //     scale       : 'large',
                    //     iconCls     : 'guardar',
                    //     iconAlign   : 'left',
                    //     handler     : function(){ btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,id); }
                    // },
                    // {
                    //     xtype       : 'button',
                    //     text        : 'Guardar',
                    //     scale       : 'large',
                    //     iconCls     : 'guardar',
                    //     iconAlign   : 'left',
                    //     handler     : function(){ guardarObservacionArt<?php echo $opcGrillaContable; ?>(cont,id); }
                    // },
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
                url     : 'traslados/bd/buscarGrillaContable.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_sucursal       : document.getElementById("filtro_sucursal_<?php echo $opcGrillaContable; ?>").value,
                    id_bodega         : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value,
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

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificar<?php echo $opcGrillaContable ?>(){
        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?")) {
            cargando_documentos('Editando Documento...','');
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'traslados/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id_documento      : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                }
            });
        }
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar<?php echo $opcGrillaContable ?>(){
        cargando_documentos('Restaurando Documento...','');
        Ext.get('render_btns_<?php echo $opcGrillaContable ?>').load({
            url     : 'traslados/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id                : '<?php echo $id_documento; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            }
        });
    }

    //============================ CANCELAR UN DOCUMENTO ===================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){
        if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
            cargando_documentos('Cancelando Documento...','');
            Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                url     : 'traslados/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'cancelarDocumento',
                    id                : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                }
            });
        };
    }

    function imprimir<?php echo $opcGrillaContable; ?> (){
        window.open("traslados/bd/imprimir_traslado.php?id_documento=<?php echo $id_documento?>");
    }

</script>
