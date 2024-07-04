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
    var debitoAcumulado<?php echo $opcGrilla; ?>  = 0.00
    ,   creditoAcumulado<?php echo $opcGrilla; ?> = 0.00
    ,   total<?php echo $opcGrilla; ?>            = 0.00
    ,   contArticulos<?php echo $opcGrilla; ?>    = 1
    ,   id_cliente_<?php echo $opcGrilla;?>       = 0;

    var  timeOutObservacion<?php echo $opcGrilla; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoCliente<?php echo $opcGrilla; ?>       = 0
    ,   nitCliente<?php echo $opcGrilla; ?>          = 0
    ,   nombreCliente<?php echo $opcGrilla; ?>       = ''
    ,   nombre_grilla                                        = 'ventanaBucarCuenta<?php echo $opcGrilla; ?>';//nombre de la grilla cunado se busca un articulo

    Ext.getCmp("Btn_exportar_NotaGeneral").enable();
    Ext.getCmp("Btn_articulos_Relacionados").disable();
    Ext.getCmp("Btn_guardar_<?php echo $opcGrilla; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrilla; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrilla; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrilla; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrilla; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrilla; ?>").hide();

    //OCULTAR EL BUTTON GROUP DE CONTABILIZAR CON NIIF
    Ext.getCmp("GroupBtnSync").hide();
    Ext.getCmp("GroupBtnNoSync").hide();

</script>
<?php

    //PERMISOS BOTONES
    $user_permiso_editar    = 'Ext.getCmp("Btn_editar_'.$opcGrilla.'").enable();';        //editar
    $user_permiso_cancelar  = 'Ext.getCmp("Btn_cancelar_'.$opcGrilla.'").enable();';      //calcelar
    $user_permiso_restaurar = 'Ext.getCmp("Btn_restaurar_'.$opcGrilla.'").enable();Ext.getCmp("Btn_exportar_NotaGeneral").disable();';     //restaurar

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

    //CONSULTAR SI LA NOTA TIENE CRUCE DE DOCUMENTOS
    $sql   = "SELECT documento_cruce FROM tipo_nota_contable WHERE id_empresa='$id_empresa' AND id='$id_tipo_nota' ";
    $query = mysql_query($sql,$link);
    $documento_cruce=mysql_result($query,0,'documento_cruce');

    if ($estado == 0) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }         //documento por editar
    if ($estado == 1) { $acumScript .='Ext.getCmp("Btn_exportar_NotaGeneral").enable();'.$user_permiso_editar.$user_permiso_cancelar; }         //documento generado
    else if($estado == 3){ $acumScript .= $user_permiso_restaurar; }      //documento cancelado

    if($sinc_nota != 'colgaap_niif' && $sinc_nota != 'niif' && $sinc_nota != 'colgaap'){ echo "ESTA NOTA NO TIENE DEFINIDA UNA FORMA DE SINCRONIZACION CONTABLE"; exit; }
    $acumScript .= 'var sinc_nota_'.$opcGrilla.' = "'.$sinc_nota.'";';

    $arrayReplaceString = array("\n", "\r","<br>");
    $classBody          = ($documento_cruce == 'Si' )? 'contenedorNotaContableCruce' : 'contenedorNotaContable' ;
    $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .=  'document.getElementById("codigoTercero'.$opcGrilla.'").value = "'.$codigo_tercero.'";
                    document.getElementById("nitCliente'.$opcGrilla.'").value     = "'.$tipo_nit_tercero.' - '.$nit_tercero.'";
                    document.getElementById("nombreCliente'.$opcGrilla.'").value  = "'.$tercero.'";
                    document.getElementById("fecha'.$opcGrilla.'").value          = "'.$fecha_nota.'";
                    document.getElementById("usuario'.$opcGrilla.'").value        = "'.$usuario.'";
                    document.getElementById("selectTipoNota").value                       = "'.$tipo_nota.'";

                    id_cliente_'.$opcGrilla.'   = "'.$id_tercero.'";
                    observacion'.$opcGrilla.'   = "'.$observacion.'";
                    nitCliente'.$opcGrilla.'    = "'.$nit_tercero.'";
                    nombreCliente'.$opcGrilla.' = "'.$tercero.'";';

    $bodyArticle = cargaArticulosSaveConTercero($id_nota,$observacion,$estado,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$link);

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
            <div id="terminar<?php echo $opcGrilla; ?>"></div>
            <div class="contTopFila">
                 <?php echo $imgBloqueo; ?>
                <div class="renglonTop">
                    <div class="labelTop">Sucursal</div>
                    <div class="campoTop"><input type="text" id="nombreSucursal<?php echo $opcGrilla; ?>" value="<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" readonly></div>
                </div>
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrilla; ?>"></div>
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrilla; ?>" value="<?php echo $fecha; ?>" readonly></div>
                </div>
                <?php if($tercero!='NOTA INTERNA'){ ?>
                <div class="renglonTop">
                    <div class="labelTop">Codigo</div>
                    <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrilla; ?>" readonly/></div>
                </div>
                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha"></div>
                <div class="renglonTop">
                    <div class="labelTop">N. de Identificacion</div>
                    <div class="campoTop" style="width:160px">
                        <input type="text" style="width:160px"  id="nitCliente<?php echo $opcGrilla; ?>" readonly/>
                    </div>
                </div>
                <?php } ?>
                <div class="renglonTop">
                    <div class="labelTop">Entidad - Empresa - Tercero</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreCliente<?php echo $opcGrilla; ?>" style="width:100%" readonly/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrilla; ?>" value="<?php echo $usuario; ?>" readonly/></div>
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
                        <input type="checkbox" style="margin-top: 4px;"  id="notaInterna<?php echo $opcGrilla; ?>" onchange="this.checked=true;" > Nota Interna
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrilla; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrilla; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>
    var observacion<?php echo $opcGrilla; ?> = '';
    <?php echo $acumScript; ?>

    document.getElementById('titleDocumento_<?php echo $opcGrilla; ?>').innerHTML='Nota Contable<br>N. <?php echo $consecutivo; ?>';

    //=================================================  BUSCAR NOTA ================================================//
    function buscar_<?php echo $opcGrilla; ?>(){ ventanaBuscar<?php echo $opcGrilla; ?>(); }

    function ventanaBuscar<?php echo $opcGrilla; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_<?php echo $opcGrilla; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_<?php echo $opcGrilla; ?>',
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
                    opc               : 'buscar_<?php echo $opcGrilla; ?>',
                    opcGrilla : '<?php echo $opcGrilla; ?>'
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
                            handler     : function(){ Win_Ventana_buscar_<?php echo $opcGrilla; ?>.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    //================================== IMPRIMIR EN PDF ==================================================================//

    function imprimir_<?php echo $opcGrilla; ?> (cuentas){
       window.open("<?php echo $carpeta ?>bd/imprimirGrillaContable.php?id=<?php echo $id_nota; ?>&opcGrilla=<?php echo $opcGrilla; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaCuentasNota=<?php echo $tablaCuentasNota; ?>&cuentas="+cuentas);
    }

    //================================= CANCELAR UN DOCUMENTO =========================================================================//
    function cancelar_<?php echo $opcGrilla; ?>(){
        if (!confirm("Aviso!\nSi elimina la nota se descontabilizara y se actualizara <?php echo $mensajeEdit; ?> \nRealmente desea continuar?")) { return;}

        Ext.get("terminar<?php echo $opcGrilla; ?>").load({
            url  : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cancelarDocumento',
                id                : '<?php echo $id_nota; ?>',
                opcGrilla : '<?php echo $opcGrilla; ?>'
            }
        });
    }

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento_<?php echo $opcGrilla ?>(){

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo <?php echo $mensajeEdit; ?>")) {
            Ext.get('terminar<?php echo $opcGrilla; ?>').load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    idDocumento       : '<?php echo $id_nota; ?>',
                    opcGrilla : '<?php echo $opcGrilla; ?>'
                }
            });
        }
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar_<?php echo $opcGrilla ?>(){
        Ext.get('terminar<?php echo $opcGrilla ?>').load({
            url     : '<?php echo $carpeta ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                idDocumento       : '<?php echo $id_nota; ?>',
                opcGrilla : '<?php echo $opcGrilla; ?>'
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
                    opcGrilla : '<?php echo $opcGrilla; ?>',
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

</script>
