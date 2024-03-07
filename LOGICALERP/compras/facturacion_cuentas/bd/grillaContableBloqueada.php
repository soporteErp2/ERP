<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../funciones_globales/funciones_php/randomico.php");
    include("../../../funciones_globales/funciones_javascript/totalesNotaContable.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');
?>
<script>

    //variables para calcular los valores de los costos y totales de la factura
    var debitoAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   creditoAcumulado<?php echo $opcGrillaContable; ?> = 0.00
    ,   total<?php echo $opcGrillaContable; ?>            = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>    = 1
    ,   id_cliente_<?php echo $opcGrillaContable;?>       = 0;

    var  timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoCliente<?php echo $opcGrillaContable; ?>       = 0
    ,   nitCliente<?php echo $opcGrillaContable; ?>          = 0
    ,   nombreCliente<?php echo $opcGrillaContable; ?>       = ''
    ,   nombre_grilla                                        = 'ventanaBucarCuenta<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

    Ext.getCmp("Btn_exportar_<?php echo $opcGrillaContable; ?>").enable();
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();

    //OCULTAR EL BUTTON GROUP DE CONTABILIZAR CON NIIF
    // Ext.getCmp("GroupBtnSync").hide();
    // Ext.getCmp("GroupBtnNoSync").hide();

</script>
<?php

    //PERMISOS BOTONES
    $user_permiso_editar    = (user_permisos(155,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos(156,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos(157,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").disable();' : '';     //restaurar


    include("../bd/functions_body_article.php");

        $sql   = "SELECT id_proveedor,
                    prefijo_factura,
                    if(numero_factura > 0,numero_factura,'') AS numero_factura,
                    consecutivo,
                    nit,
                    cod_proveedor,
                    proveedor,
                    plantillas_id,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha_inicio,
                    date_format(fecha_final,'%Y-%m-%d') AS fecha_final,
                    observacion,
                    estado,
                    forma_pago,
                    configuracion_cuenta_pago,
                    usuario
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
        $forma_pago                = mysql_result($query,0,'forma_pago');
        $observacion               = mysql_result($query,0,'observacion');
        $configuracion_cuenta_pago = mysql_result($query,0,'configuracion_cuenta_pago');
        $usuario                   = mysql_result($query,0,'usuario');
        $consecutivo               = mysql_result($query,0,'consecutivo');

        // if ($estado==1) { echo "ESTA NOTA SE ENCUENTRA CERRADA POR QUE YA HA SIDO GENERADA"; exit; }
        // if($consecutivo > 0){ $tiposNotas = '<input type="text" value="'.$tipo_nota.'" readonly/><input type="hidden" value="'.$id_tipo_nota.'" id="selectTipoNota" style="width:135;"/>'; }
        // else { $acumScript .= 'document.getElementById("selectTipoNota").value = "'.$id_tipo_nota.'";'; }

        if ($estado==1) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar.'Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").enable();'; }   //documento generado
        else if($estado == 3){ $acumScript .= $user_permiso_restaurar.'Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").enable();'; }      //documento cancelado



        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", $observacion);

        // $acumScript .= 'new Ext.form.DateField({
        //                     format     : "Y-m-d",
        //                     width      : 135,
        //                     allowBlank : false,
        //                     showToday  : false,
        //                     applyTo    : "fecha'.$opcGrillaContable.'",
        //                     editable   : false,
        //                     listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value); } }
        //                 });';


        $acumScript .= 'document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
                        document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$cod_proveedor.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$proveedor.'";
                        document.getElementById("prefijoFacturaCuentas").value               = "'.$prefijo_factura.'";
                        document.getElementById("numeroFacturaCuentas").value                = "'.$numero_factura.'";
                        document.getElementById("selectFormaPagoCompraCuentas").value        = "'.$forma_pago.'";
                        document.getElementById("fechaFacturaCuentas").value                 = "'.$fecha_inicio.'";
                        document.getElementById("fechaFinalFacturaCuentas").value            = "'.$fecha_final.'";
                        document.getElementById("selectCuentaPagoCompraCuentas").value       = "'.$configuracion_cuenta_pago.'";
                        document.getElementById("usuario'.$opcGrillaContable.'").value       = "'.$usuario.'";';

        $bodyArticle = cargaArticulosSaveConTercero($id_factura_compra,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);

?>

<div class="contenedorNotaContable" id="contenedorNotaContable">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <!-- <div class="renglonTop">
                    <div class="labelTop">Sucursal</div>
                    <div class="campoTop"><input type="text" id="nombreSucursal<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" readonly></div>
                </div> -->
                <div class="renglonTop">
                    <div class="labelTop">Fecha de inicio</div>
                    <div class="campoTop"><input type="text" id="fechaFacturaCuentas" readonly></div>
                </div>
                <div class="renglonTop" >
                    <div class="labelTop">Fecha de Vencimiento</div>
                    <div class="campoTop"><input type="text" id="fechaFinalFacturaCuentas" readonly></div>
                </div>
                <div class="renglonTop" style="width:137px;display:none;">
                    <div class="labelTop" style="float:left; width:100%;">Forma de pago</div>
                    <div id="renderSelectFormaPago" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="overflow:hidden;">
                        <input type="text" id="selectFormaPagoCompraCuentas" readonly />
                    </div>
                </div>
                <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Cuenta de pago</div>
                    <div id="renderSelectCuentaPago" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <input type="text" id="selectCuentaPagoCompraCuentas" readonly/>
                    </div>
                </div>

                <div class="renglonTop" style="width:135px;">
                    <div class="labelTop">Factura #</div>
                    <div class="campoTop">
                        <input type="text" id="prefijoFacturaCuentas" style="width:30% !important; float:left;"  readonly>
                        <div style="width:10% !important; float:left;background-color:#F3F3F3; height:100%; text-align:center;">-</div>
                        <input type="text" id="numeroFacturaCuentas" style="width:60% !important; float:left;" readonly>
                    </div>
                </div>

                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha"></div>
                <div class="renglonTop" id="divCodigoTercero">
                    <div class="labelTop">Codigo Tercero</div>
                    <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $codigo_tercero; ?>" readonly ></div>
                </div>
                <div class="renglonTop" id="divIdentificacionTercero">
                    <div class="labelTop">Numero Identificacion</div>
                    <div class="campoTop">
                        <input type="text" style="width:161px"  id="nitCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $nitTercero; ?>" readonly />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tercero</div>
                    <div class="campoTop" style="width:277px;">
                        <input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $tercero; ?>" Readonly/>
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>"  readonly/></div>
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

    if('<?php echo $estado ?>'== 1){

        document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML='Consecutivo<br>N. <?php echo $consecutivo; ?>';
    }
    //=================================================  BUSCAR NOTA ================================================//
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
                url     : '<?php echo $carpeta; ?>bd/buscarGrillaContable.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
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

    function imprimir<?php echo $opcGrillaContable; ?> (cuentas){
       window.open("<?php echo $carpeta ?>bd/imprimirGrillaContable.php?id=<?php echo $id_factura_compra; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaCuentasNota=<?php echo $tablaCuentasNota; ?>&cuentas="+cuentas);
    }

    //================================= CANCELAR UN DOCUMENTO =========================================================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){
        if (!confirm("Aviso!\nSi elimina la nota se descontabilizara y se actualizara <?php echo $mensajeEdit; ?> \nRealmente desea continuar?")) { return;}
        cargando_documentos('Cancelando Factura de Compra...','');
        Ext.get("terminar<?php echo $opcGrillaContable; ?>").load({
            url  : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cancelarDocumento',
                id                : '<?php echo $id_factura_compra; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });
    }

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento<?php echo $opcGrillaContable ?>(){

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo <?php echo $mensajeEdit; ?>")) {
            cargando_documentos('Editando Factura de Compra...','');
            Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id       : '<?php echo $id_factura_compra; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                }
            });
        }
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar<?php echo $opcGrillaContable ?>(){
        cargando_documentos('Restaurando Factura de Compra...','');
        Ext.get('terminar<?php echo $opcGrillaContable ?>').load({
            url     : '<?php echo $carpeta ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id       : '<?php echo $id_factura_compra; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });
    }

    //=============== FUNCION PARA LOS ARTICULOS RELACIONADOS ====================================================//
    function ventanaArticulosRelacionados(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_articulos_relacionados = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_articulos_relacionados',
            title       : 'Articulos Relacionados en la nota No. <?php echo $consecutivo; ?>',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '<?php echo $carpeta; ?>bd/buscarArticulosRelacionados.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    consecutivo       : '<?php echo $consecutivo; ?>'
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
                            handler     : function(){ Win_Ventana_articulos_relacionados.close(id) }
                        }
                    ]
                },
                '->',
                {
                    xtype : "tbtext",
                    text  : '<div id="motivoMovimientoArticulos" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
                    scale : "large",
                }
            ]
        }).show();
    }


    function validarCuentasFacturaCompraCuentas(){ return 3; }

    //====================================// UPLOAD FILE NOTA CONTABLE //====================================//
    //*******************************************************************************************************//
    function createUploader(){
        var tipo_nota = document.getElementById('filtro_tipo_contabilidad_NotaGeneral').value;

        var uploader = new qq.FileUploader({
            element : document.getElementById('div_upload_file'),
            action  : 'upload_file/upload_file.php',
            debug   : false,
            params  : { opcion: 'loadExcelNota', typeNota: tipo_nota, sinc_nota: sinc_nota_<?php echo $opcGrillaContable; ?> },
            button            : null,
            multiple          : false,
            maxConnections    : 3,
            allowedExtensions : ['xls', 'csv'],
            sizeLimit         : 10*1024*1024,
            minSizeLimit      : 0,
            onSubmit          : function(id, fileName){},
            onProgress        : function(id, fileName, loaded, total){},
            onComplete        : function(id, fileName, responseJSON){
                                    document.getElementById('div_upload_file').querySelector('.qq-upload-list').innerHTML='';

                                    idNotaContable     = responseJSON.idNotaContable;
                                    contCuentaNoExiste = responseJSON.contCuentaNoExiste;
                                    // document.getElementById('btn_cancel_doc_upload').style.display = 'block';
                                    document.getElementById('divPadreModalUploadFile').setAttribute('style','');
                                    document.getElementById('titleDocumentoNotaGeneral').innerHTML='';

                                    Ext.get("contenedor_<?php echo $opcGrillaContable; ?>").load({
                                        url     : 'nota_general/grilla/grillaContable.php',
                                        scripts : true,
                                        nocache : true,
                                        params  :
                                        {
                                            id_nota           : idNotaContable,
                                            opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                                        }
                                    });

                                    if(contCuentaNoExiste > 0)alert("Aviso\nSe han agregado "+contCuentaNoExiste+" cuentas que no existen en la contabilidad, por favor asigne las cuentas antes de generar el documento!");
                                },
            onCancel : function(fileName){},
            messages :
            {
                typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'xls', 'xlsx', 'csv'",
                sizeError    : "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError   : "{file} is empty, please select files again without it.",
                onLeave      : "Cargando Archivo."
            }
        });
    }
    // createUploader();

    function ventanaDocumentosCruce() {

        Win_Ventana_documentos_cruce = new Ext.Window({
            width       : 550,
            height      : 500,
            id          : 'Win_Ventana_documentos_cruce',
            title       : '',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion_cuentas/grilla/documentos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    id_factura_compra   : '<?php echo $id_factura_compra; ?>',
                }
            },
        }).show();
    }

    function ventanaArchivosAdjuntos(cont) {
        var id_tabla_referencia = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value

        Win_Ventana_archivos_adjuntos = new Ext.Window({
            width       : 550,
            height      : 500,
            id          : 'Win_Ventana_archivos_adjuntos',
            title       : '',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion_cuentas/grilla/documentos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    id_factura_compra   : '<?php echo $id_factura_compra; ?>',
                    id_tabla_referencia : id_tabla_referencia,
                }
            },
        }).show();
    }

</script>
