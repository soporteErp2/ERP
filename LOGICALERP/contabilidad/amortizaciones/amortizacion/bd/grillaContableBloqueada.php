<?php
    include("../../../../../configuracion/conectar.php");
    include("../../../../../configuracion/define_variables.php");
    include("../../config_var_global.php");

    $id_empresa        = $_SESSION['EMPRESA'];
    $id_sucursal       = $_SESSION['SUCURSAL'];
    $id_usuario        = $_SESSION['IDUSUARIO'];
    $documento_usuario = $_SESSION['CEDULAFUNCIONARIO'];
    $nombre_usuario    = $_SESSION['NOMBREFUNCIONARIO'];
    $bodyArticle       = '';
    $acumScript        = '';
    $estado            = '';
    $fecha             = date('Y-m-d');
    $exento_iva        = '';

    //CONSULTAMOS LA FECHA DE VENCIMIENTO QUE ESTE CONFIGURADA, SINO ESTA CONFIGURADA SE ASIGNA DE 30 DIAS
    //FILTRAMOS PARA DIFERENCIAR EL NOMBRE DE LAS TABLAS

    $sqlFecha     = "SELECT dias_vencimiento FROM ventas_cotizaciones_configuracion WHERE activo=1  AND id_empresa=".$_SESSION['EMPRESA'];
    $queryFecha   = mysql_query($sqlFecha,$link);
    $fechaDefault = mysql_result($queryFecha,0,'dias_vencimiento');
    if ($fechaDefault=='') { $fechaDefault='31'; }

?>
<script>

    //variables para calcular los valores de los costos y totales de la factura
    var subtotal<?php echo $opcGrillaContable; ?>  = 0.00
    ,   subtotalDetalle<?php echo $opcGrillaContable; ?> = 0.00
    ,   total<?php echo $opcGrillaContable; ?>          = 0.00
    ,   contDetalles<?php echo $opcGrillaContable; ?>      = 1
    ,   id_tercero_<?php echo $opcGrillaContable;?>         = 0;

    var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoTercero<?php echo $opcGrillaContable; ?>      = 0
    ,   nitTercero<?php echo $opcGrillaContable; ?>         = 0
    ,   nombreTercero<?php echo $opcGrillaContable; ?>      = ''
    ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

     //Bloqueo todos los botones
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();

    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_cargar_diferidos").hide();
    // Ext.getCmp("BtnGroup_exportar_<?php echo $opcGrillaContable; ?>").hide();


</script>
<?php

    // $acumScript .= (user_permisos(6,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    // $acumScript .= (user_permisos(8,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';       //cancelar

    // CONSULTAR LAS SUCURSALES
    $sql="SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $sucursales .= '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
    }


    include("functions_body_article.php");

    $sql   = "SELECT
                    fecha_documento,
                    fecha_diferidos,
                    consecutivo,
                    documento_usuario,
                    nombre_usuario,
                    observacion,
                    sucursal,
                    estado
                FROM $tablaPrincipal
                WHERE id='$id_documento' AND activo = 1";
    $query = $mysql->query($sql,$mysql->link);

    $fecha_documento   = $mysql->result($query,0,'fecha_documento');
    $fecha_diferidos   = $mysql->result($query,0,'fecha_diferidos');
    $consecutivo       = $mysql->result($query,0,'consecutivo');
    $documento_usuario = $mysql->result($query,0,'documento_usuario');
    $nombre_usuario    = $mysql->result($query,0,'nombre_usuario');
    $observacion       = $mysql->result($query,0,'observacion');
    $sucursal          = $mysql->result($query,0,'sucursal');
    $estado            = $mysql->result($query,0,'estado');

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .=  "document.getElementById('observacion$opcGrillaContable').value   = '$observacion';
                     document.getElementById('observacion$opcGrillaContable').readOnly = true;";

    $bodyArticle = cargaArticulosSave($id_documento,$id_empresa,$opcGrillaContable,$mysql);

    if ($estado==1) {
        $acumScript.='Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();
                      Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();
                      Ext.getCmp("btnImprimir'.$opcGrillaContable.'").enable();
                      Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';
    }
    else if ($estado==3) {
        $acumScript.='Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();
                        Ext.getCmp("btnImprimir'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();';
    }

    $acumScript .= (user_permisos(212,'false') == 'false')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(213,'false') == 'false')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();'   : '';
    $acumScript .= (user_permisos(214,'false') == 'false')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(215,'false') == 'false')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();' : '';


?>

<div class="contenedorExtracto" id="contenedor_Amortizacion">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div class="labelTop">Sucursal</div>
                    <div id="rendersucursal<?php echo $opcGrillaContable; ?>" style="width:20px; height:19px; overflow:hidden;margin-top: -21px;float:right;"></div>
                    <div class="campoTop" ><input  type="text" readonly value="<?php echo $sucursal ?>" /></div>
                </div>

                <div class="renglonTop" >
                    <div class="labelTop">Fecha Documento</div>
                    <div id="renderfecha_<?php echo $opcGrillaContable; ?>" style="width: 20px;height: 19px;overflow: hidden;margin-top: -21px;margin-left: 129px;position: absolute;"></div>
                    <div class="campoTop" ><input  type="text" readonly value="<?php echo $fecha_documento ?>" /></div>
                </div>
                <div class="renglonTop" >
                    <div class="labelTop">Fecha Diferidos</div>
                    <div id="renderfechaDiferidos_<?php echo $opcGrillaContable; ?>" style="width: 20px;height: 19px;overflow: hidden;margin-top: -21px;margin-left: 129px;position: absolute;"></div>
                    <div class="campoTop" > <input  type="text" readonly value="<?php echo $fecha_diferidos ?>" /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:271px;"><input type="text" Readonly value="<?php echo $nombre_usuario ?>"/></div>
                </div>

            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>

    <?php echo $acumScript; ?>
    // Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();  //disable btn imprimir


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
                url     : 'amortizaciones/amortizacion/bd/buscarGrillaContable.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    // filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
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
        window.open("amortizaciones/amortizacion/bd/imprimirGrillaContable.php?id_documento=<?php echo $id_documento; ?>");
    }

    //================================== IMPRIMIR EN EXCEL ============================================//
    // function imprimir<?php echo $opcGrillaContable; ?>Excel (){
    //    window.open("bd/exportarExcelGrillaContable.php?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    // }

    // ==================== MODIFICAR UN DOCUMENTO ===================//
    function modificarDocumento<?php echo $opcGrillaContable; ?>(){

        if(confirm('Esta seguro de Editar el presente Documento y su contenido relacionado')){
            cargando_documentos('Editando Documento...','');
            Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                url     : 'amortizaciones/amortizacion/bd/bd.php',
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

    //============================ CANCELAR UN DOCUMENTO ===================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){

        if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
            cargando_documentos('Cancelando Documento...','');
            Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                url     : 'amortizaciones/amortizacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'cancelarDocumento',
                    id_documento      : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                }
            });
        }

    }

    function restaurar<?php echo $opcGrillaContable; ?>(){

        cargando_documentos('Restaurando Documento...','');
        Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id_documento      : '<?php echo $id_documento; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            }
        });

    }


</script>
