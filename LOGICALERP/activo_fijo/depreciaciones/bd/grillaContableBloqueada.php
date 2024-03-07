<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../funciones_globales/funciones_php/randomico.php");
    // include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $filtro_sucursal;
    $id_usuario  = $_SESSION['IDUSUARIO'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');
    $exento_iva  = '';
?>
<script>

    //variables para calcular los valores de los costos y totales de la factura
    var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   total<?php echo $opcGrillaContable; ?>              = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1

    arrayIva<?php echo $opcGrillaContable; ?>=[]; // ARRAY CON LOS VALORES DE LOS IVAS
    arrayIva<?php echo $opcGrillaContable; ?>[0]={nombre:"",valor:""};

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
    Ext.getCmp("BtnGroup_carga_activos_Depreciaciones").hide();

     //variable con la fecha del dia mas treinta dias, para cargar por defecto la fecha de vencimiento
    var fechaVencimientoFactura<?php echo $opcGrillaContable;?>  = new Date();
    fechaVencimientoFactura<?php echo $opcGrillaContable;?>.setDate(fechaVencimientoFactura<?php echo $opcGrillaContable;?>.getDate()+parseInt('<?php echo $fechaDefault; ?>'));

</script>
<?php

    $acumScript .= (user_permisos(108,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    $acumScript .= (user_permisos(109,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();';       //cancelar
    $acumScript .= (user_permisos(110,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();';       //cancelar
    $acumScript .= (user_permisos(111,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();';       //cancelar

    include("../bd/functions_body_article.php");

    $sql   = "SELECT consecutivo,
                    fecha_inicio,
                    codigo_tercero,
                    numero_identificacion_tercero,
                    tipo_identificacion_tercero,
                    tercero,
                    usuario,
                    observacion,
                    estado
                FROM $tablaPrincipal
                WHERE id='$id_depreciacion' AND activo = 1";
    $query = mysql_query($sql,$link);

    $consecutivo                   = mysql_result($query,0,'consecutivo');
    $fecha_inicio                  = mysql_result($query,0,'fecha_inicio');
    $usuario                       = mysql_result($query,0,'usuario');
    $observacion                   = mysql_result($query,0,'observacion');
    $estado                        = mysql_result($query,0,'estado');
    $codigo_tercero                = mysql_result($query,0,'codigo_tercero');
    $numero_identificacion_tercero = mysql_result($query,0,'numero_identificacion_tercero');
    $tipo_identificacion_tercero   = mysql_result($query,0,'tipo_identificacion_tercero');
    $tercero                       = mysql_result($query,0,'tercero');

    if ( $estado=='2' ) { echo "ESTE DOCUMENTO ESTA CERRADO "; exit; }

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .=  '
                    document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha_inicio.'";
                    document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$usuario.'";
                    document.getElementById("observacion'.$opcGrillaContable.'").value    = "'.$observacion.'";
                    document.getElementById("titleDocumentoDepreciaciones").innerHTML     = "Consecutivo N.<br>'.$consecutivo.'";
                    document.getElementById("codigoTercero'.$opcGrillaContable.'").value  = "'.$codigo_tercero.'";
                    document.getElementById("nitCliente'.$opcGrillaContable.'").value     = "'.$tipo_identificacion_tercero.' - '.$numero_identificacion_tercero.'";
                    document.getElementById("nombreTercero'.$opcGrillaContable.'").value  = "'.$tercero.'";

                    observacion'.$opcGrillaContable.'   = "'.$observacion.'";';

    $bodyArticle = cargaArticulosSave($id_depreciacion,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

    if ($estado==1) {
        $acumScript.='Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();
                      //Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();
                      Ext.getCmp("btnExportar'.$opcGrillaContable.'").enable();
                      //Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';
    }
    else if ($estado==3) {
        $acumScript.='Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();
                        //Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();
                        Ext.getCmp("btnExportar'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();';
    }

    $habilita   = ($estado=='1')? 'onclick="javascript: return false;" disabled ': '';

?>

<div class="contenedorOrdenCompra" id>

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">

                <div class="renglonTop">
                    <div class="labelTop">Fecha</div><div id="divLoadFecha" style="width:20px;height:20px;margin-top: -20;margin-left: -22;overflow:hidden;float: right;"></div>
                    <div class="campoTop" >
                        <input type="text" id="fecha<?php echo $opcGrillaContable; ?>" Readonly />
                    </div>
                </div>

                <div class="renglonTop" id="divCodigoTercero">
                    <div class="labelTop">Codigo Tercero</div>
                    <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $codigo_tercero; ?>" Readonly ></div>
                </div>
                <div class="renglonTop" id="divIdentificacionTercero">
                    <div class="labelTop">N. de Identificacion</div>
                    <div class="campoTop" style="width:230px">
                        <input type="text" style="width:161px"  id="nitCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $nitTercero; ?>" Readonly/>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tercero</div>
                    <div class="campoTop" style="width:277px;">
                        <input type="text" id="nombreTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $tercero; ?>" Readonly/>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreVendedor<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly /></div>

                </div>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>

//OCULTAR EL BUTTON GROUP DE CONTABILIZAR CON NIIF
    Ext.getCmp("GroupBtnSync").hide();
    Ext.getCmp("GroupBtnNoSync").hide();

    var observacion<?php echo $opcGrillaContable; ?> = '';
    <?php echo $acumScript; ?>

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
                url     : 'depreciaciones/bd/buscarGrillaContable.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    filtro_sucursal     : document.getElementById("filtro_sucursal_<?php echo $opcGrillaContable; ?>").value
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

    //=================================== IMPRIMIR EN PDF =============================================//

    function imprimir<?php echo $opcGrillaContable; ?> (){
        window.open("depreciaciones/bd/imprimirGrillaContable.php?id=<?php echo $id_depreciacion; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    //================================== IMPRIMIR EN EXCEL ============================================//
    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
       window.open("depreciaciones/bd/exportarExcelGrillaContable.php?id=<?php echo $id_depreciacion; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    function modificarDocumento<?php echo $opcGrillaContable; ?>() {
        if(confirm('Esta seguro de Editar el presente Documento y su contenido relacionado')){
            cargando_documentos('Modificando Documento...');
            Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                url     : 'depreciaciones/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id       : '<?php echo $id_depreciacion; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_sucursal       : '<?php echo $id_sucursal; ?>',
                }
            })
        }
    }

    //============================ CANCELAR UN DOCUMENTO ===================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){

        if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
            cargando_documentos('Cancelando Documento...');
            Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                url     : 'depreciaciones/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'cancelarDocumento',
                    id                : '<?php echo $id_depreciacion; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_sucursal       : '<?php echo $id_sucursal; ?>',
                }
            });
        }

    }

    function restaurar<?php echo $opcGrillaContable; ?>(){
        cargando_documentos('Restaurando Documento...');
        Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
            url     : 'depreciaciones/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id       : '<?php echo $id_depreciacion; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id_sucursal       : '<?php echo $id_sucursal; ?>',
            }
        });
    }


</script>
