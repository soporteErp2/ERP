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

    Ext.getCmp("Btn_exportar_NotaGeneral").enable();
    Ext.getCmp("Btn_articulos_Relacionados").disable();
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();

    //OCULTAR EL BUTTON GROUP DE CONTABILIZAR CON NIIF
    Ext.getCmp("GroupBtnSync").hide();
    Ext.getCmp("GroupBtnNoSync").hide();

</script>
<?php

    //PERMISOS BOTONES
    $user_permiso_editar    = 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();';        //editar
    $user_permiso_cancelar  = 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';      //calcelar
    $user_permiso_restaurar = 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();Ext.getCmp("Btn_exportar_NotaGeneral").disable();';     //restaurar

    include("../bd/functions_body_article.php");

    $sql  = "SELECT consecutivo,
                    fecha_nota,
                    id_tercero,
                    codigo_tercero,
                    tipo_identificacion_tercero,
                    numero_identificacion_tercero,
                    tercero,
                    id_tipo_nota,
                    tipo_nota,
                    usuario,
                    observacion,
                    estado,
                    sinc_nota
                FROM $tablaPrincipal
                WHERE id='$id_nota'";
    $query = mysql_query($sql,$link);

    $consecutivo      = mysql_result($query,0,'consecutivo');
    $fecha_nota       = mysql_result($query,0,'fecha_nota');
    $id_tercero       = mysql_result($query,0,'id_tercero');
    $codigo_tercero   = mysql_result($query,0,'codigo_tercero');
    $tipo_nit_tercero = mysql_result($query,0,'tipo_identificacion_tercero');
    $nit_tercero      = mysql_result($query,0,'numero_identificacion_tercero');
    $tercero          = mysql_result($query,0,'tercero');
    $id_tipo_nota     = mysql_result($query,0,'id_tipo_nota');
    $tipo_nota        = mysql_result($query,0,'tipo_nota');
    $usuario          = mysql_result($query,0,'usuario');
    $estado           = mysql_result($query,0,'estado');
    $sinc_nota        = mysql_result($query,0,'sinc_nota');
    $observacion      = mysql_result($query,0,'observacion');

    //CONSULTAR SI LA NOTA TIENE CRUCE DE DOCUMENTOS
    $sql   = "SELECT documento_cruce FROM tipo_nota_contable WHERE id_empresa='$id_empresa' AND id='$id_tipo_nota' ";
    $query = mysql_query($sql,$link);
    $documento_cruce=mysql_result($query,0,'documento_cruce');

    if ($estado == 0) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }         //documento por editar
    if ($estado == 1) { $acumScript .='Ext.getCmp("Btn_exportar_NotaGeneral").enable();'.$user_permiso_editar.$user_permiso_cancelar; }         //documento generado
    else if($estado == 3){ $acumScript .= $user_permiso_restaurar; }      //documento cancelado

    if($sinc_nota != 'colgaap_niif' && $sinc_nota != 'niif' && $sinc_nota != 'colgaap'){ echo "ESTA NOTA NO TIENE DEFINIDA UNA FORMA DE SINCRONIZACION CONTABLE"; exit; }
    $acumScript .= 'var sinc_nota_'.$opcGrillaContable.' = "'.$sinc_nota.'";';

    $arrayReplaceString = array("\n", "\r","<br>");
    $classBody          = ($documento_cruce == 'Si' )? 'contenedorNotaContableCruce' : 'contenedorNotaContable' ;
    $observacion        = str_replace($arrayReplaceString, "\\n", $observacion);

    $acumScript .=  'document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$codigo_tercero.'";
                    document.getElementById("nitCliente'.$opcGrillaContable.'").value     = "'.$tipo_nit_tercero.' - '.$nit_tercero.'";
                    document.getElementById("nombreCliente'.$opcGrillaContable.'").value  = "'.$tercero.'";
                    document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha_nota.'";
                    document.getElementById("usuario'.$opcGrillaContable.'").value        = "'.$usuario.'";
                    document.getElementById("observacion'.$opcGrillaContable.'").value    = "'.$observacion.'";
                    document.getElementById("selectTipoNota").value                       = "'.$tipo_nota.'";

                    id_cliente_'.$opcGrillaContable.'   = "'.$id_tercero.'";
                    observacion'.$opcGrillaContable.'   = "'.$observacion.'";
                    nitCliente'.$opcGrillaContable.'    = "'.$nit_tercero.'";
                    nombreCliente'.$opcGrillaContable.' = "'.$tercero.'";';

    $bodyArticle = cargaArticulosSaveConTercero($id_nota,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);

    //VERIFICAR SI LA NOTA TIENE ARTICULOS RELACIONADOS
    $sqlArticulos   = "SELECT COUNT(id) AS cant FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo ' AND id_empresa='$id_empresa'";
    $queryArticulos = mysql_query($sqlArticulos,$link);
    $cantArticulos  = mysql_result($queryArticulos,0,'cant');

    if ($cantArticulos>0) {
        $acumScript  .= 'Ext.getCmp("Btn_articulos_Relacionados").enable();';
        $mensajeEdit  = '\nY los articulos relacionados se eliminaran y se reversara el proceso de los mismos';
    }

?>

<div class="<?php echo $classBody; ?>" style="height:calc(100% - 95px);overflow:auto;">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                 <?php echo $imgBloqueo; ?>
                <div class="renglonTop">
                    <div class="labelTop">Sucursal</div>
                    <div class="campoTop"><input type="text" id="nombreSucursal<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" readonly></div>
                </div>
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
                </div>
                <?php if($tercero!='NOTA INTERNA'){ ?>
                <div class="renglonTop">
                    <div class="labelTop">Codigo</div>
                    <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" readonly/></div>
                </div>
                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha"></div>
                <div class="renglonTop">
                    <div class="labelTop">N. de Identificacion</div>
                    <div class="campoTop" style="width:160px">
                        <input type="text" style="width:160px"  id="nitCliente<?php echo $opcGrillaContable; ?>" readonly/>
                    </div>
                </div>
                <?php } ?>
                <div class="renglonTop">
                    <div class="labelTop">Entidad - Empresa - Tercero</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" style="width:100%" readonly/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" value="<?php echo $usuario; ?>" readonly/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Filtro</div>
                    <div class="campoTop" style="width:150px;">
                        <input type="text" id="selectTipoNota" title="<?php echo $tipo_nota; ?>" style="width:135;" readonly/>
                    </div>
                </div>
                <?php if ($tercero=='NOTA INTERNA') { ?>
                <div class="renglonTop">
                    <div class="labelTop">Opciones</div>
                    <div class="campoTop" align="center">
                        <input type="checkbox" style="margin-top: 4px;"  id="notaInterna<?php echo $opcGrillaContable; ?>" onchange="this.checked=true;" > Nota Interna
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
    var observacion<?php echo $opcGrillaContable; ?> = '';
    <?php echo $acumScript; ?>

    document.getElementById('titleDocumento<?php echo $opcGrillaContable; ?>').innerHTML='Nota Contable<br>N. <?php echo $consecutivo; ?>';

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
       window.open("<?php echo $carpeta ?>bd/imprimirGrillaContable.php?id=<?php echo $id_nota; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaCuentasNota=<?php echo $tablaCuentasNota; ?>&cuentas="+cuentas);
    }

    //================================= CANCELAR UN DOCUMENTO =========================================================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){
        if (!confirm("Aviso!\nSi elimina la nota se descontabilizara y se actualizara <?php echo $mensajeEdit; ?> \nRealmente desea continuar?")) { return;}
        cargando_documentos('Cancelando Documento...');
        Ext.get("terminar<?php echo $opcGrillaContable; ?>").load({
            url  : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cancelarDocumento',
                id                : '<?php echo $id_nota; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });
    }

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento<?php echo $opcGrillaContable ?>(){

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo <?php echo $mensajeEdit; ?>")) {
            cargando_documentos('Editando Documento...');
            Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id       : '<?php echo $id_nota; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                }
            });
        }
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar<?php echo $opcGrillaContable ?>(){
        cargando_documentos('Restaurando Documento...');
        Ext.get('terminar<?php echo $opcGrillaContable ?>').load({
            url     : '<?php echo $carpeta ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id       : '<?php echo $id_nota; ?>',
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
            allowedExtensions : ['xls', 'ods'],
            sizeLimit         : 10*1024*1024,
            minSizeLimit      : 0,
            onSubmit          : function(id, fileName){},
            onProgress        : function(id, fileName, loaded, total){},
            onComplete        : function(id, fileName, responseJSON){
                                    document.getElementById('div_upload_file').querySelector('.qq-upload-list').innerHTML='';

                                    var JsonText = JSON.stringify(responseJSON);

                                    if(JsonText == '{}'){ alert("Aviso\nNo se ha logrado subir el excel verifique el archivo e intentelo nuevamente!"); return; }
                                    else{

                                        var idNotaContable     = responseJSON.idNotaContable;
                                        var contCuentaNoExiste = responseJSON.contCuentaNoExiste;

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
                                    }
                                },
            onCancel : function(fileName){},
            messages :
            {
                typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'xls', 'xlsx', 'ods'",
                sizeError    : "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError   : "{file} is empty, please select files again without it.",
                onLeave      : "Cargando Archivo."
            }
        });
    }
    createUploader();

    function ventanaConfiguracion<?php echo $opcGrillaContable ?>(cont){
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_cambiar_cuenta_niif = new Ext.Window({
            width       : 400,
            height      : 340,
            id          : 'Win_Ventana_cambiar_cuenta_niif',
            title       : '',
            modal       : true,
            autoScroll  : false,
            closable    : true,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    cont              : cont,
                    opc               : 'cargaConfiguracionCuenta',
                    idInsertCuenta    : idInsertCuenta,
                    idCuenta          : idCuenta,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_nota           : '<?php echo $id_nota; ?>',
                    block             : 'true',
                }
            }
        }).show();
    }

</script>
