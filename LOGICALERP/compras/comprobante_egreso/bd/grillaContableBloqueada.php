<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../funciones_globales/funciones_php/randomico.php");
    include("../../../funciones_globales/funciones_javascript/totalesComprobanteCuentas.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $divImagen   = '';
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
    ,   id_cliente_<?php echo $opcGrillaContable;?>       = 0
    ,   subtotalDebito<?php echo $opcGrillaContable;?>    = 0
    ,   subtotalCredito<?php echo $opcGrillaContable;?>   = 0;

    var  timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoCliente<?php echo $opcGrillaContable; ?>       = 0
    ,   nitCliente<?php echo $opcGrillaContable; ?>          = 0
    ,   nombreCliente<?php echo $opcGrillaContable; ?>       = ''
    ,   nombre_grilla = 'ventanaBucarCuenta<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

    Ext.getCmp("Btn_guardar_comprobante_egreso").disable();
    Ext.getCmp("Btn_editar_comprobante_egreso").disable();
    Ext.getCmp("Btn_cancelar_comprobante_egreso").disable();
    Ext.getCmp("Btn_restaurar_comprobante_egreso").disable();
    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").enable();
    Ext.getCmp("BtnGroup_Estado1_comprobante_egreso").show();
    Ext.getCmp("BtnGroup_Guardar_comprobante_egreso").hide();

</script>
<?php

    include("../bd/functions_body_article.php");

    $sql   = "SELECT fecha_comprobante,
                    id_tercero,
                    codigo_tercero,
                    nit_tercero,
                    tercero,
                    observacion,
                    estado,
                    usuario,
                    cuenta,
                    descripcion_cuenta,
                    flujo_efectivo,
                    numero_cheque
                FROM $tablaPrincipal
                WHERE id='$id_comprobante_egreso'";
    $query = mysql_query($sql,$link);

    $flujo_efectivo = mysql_result($query,0,'flujo_efectivo');
    $fecha_nota     = mysql_result($query,0,'fecha_comprobante');
    $id_tercero     = mysql_result($query,0,'id_tercero');
    $codigo_tercero = mysql_result($query,0,'codigo_tercero');
    $nit_tercero    = mysql_result($query,0,'nit_tercero');
    $tercero        = mysql_result($query,0,'tercero');
    $usuario        = mysql_result($query,0,'usuario');
    $estado         = mysql_result($query,0,'estado');
    $numeroCheque   = mysql_result($query,0,'numero_cheque');
    $cuenta         = mysql_result($query,0,'cuenta').' '.mysql_result($query,0,'descripcion_cuenta');

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $user_permiso_generar   = (user_permisos(43,'false') == 'true')? 'Ext.getCmp("Btn_guardar_comprobante_egreso").enable();' : '';       //guardar
    $user_permiso_editar    = (user_permisos(44,'false') == 'true')? 'Ext.getCmp("Btn_editar_comprobante_egreso").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos(45,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_comprobante_egreso").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos(46,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_comprobante_egreso").enable();' : '';     //restaurar

    if ($estado == 0) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }         //documento por editar
    if ($estado == 1) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar.' Ext.getCmp("btnExportar'.$opcGrillaContable.'").enable();'; }         //documento generado
    else if($estado == 2){  $divImagen ='<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Comprobante Egreso Cruzado" />'; $acumScript .=' Ext.getCmp("btnExportar'.$opcGrillaContable.'").enable();';}     //documento cruzado con otro
    else if($estado == 3){ $acumScript .= $user_permiso_restaurar.' Ext.getCmp("btnExportar'.$opcGrillaContable.'").enable();'; }      //documento cancelado


    $acumScript .=  'id_cliente_'.$opcGrillaContable.'   = "'.$id_tercero.'";
                    observacion'.$opcGrillaContable.'   = "'.$observacion.'";
                    nitCliente'.$opcGrillaContable.'    = "'.$nit_tercero.'";
                    nombreCliente'.$opcGrillaContable.' = "'.$tercero.'";';

    $bodyArticle = cargaArticulosSaveConTercero($id_comprobante_egreso,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);

    $habilita = ($estado=='1')? 'onclick="javascript: return false;" disabled ': '';

?>

<div class="contenedorComprobanteEgresoCuentas" >

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="renderRestaurar<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                 <?php echo $divImagen; ?>
                <div class="renglonTop">
                    <div class="labelTop">Sucursal</div>
                    <div class="campoTop"><input type="text" id="nombreSucursal<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" readonly/></div>
                </div>
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" value="<?php echo $fecha_nota; ?>" readonly/></div>
                </div>

                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha"></div>
                <div class="renglonTop" id="divCodigoTercero">
                    <div class="labelTop">Codigo Proveedor</div>
                    <div class="campoTop"><input type="text" value="<?php echo $codigo_tercero; ?>" readonly></div>
                </div>

                <div class="renglonTop" id="divIdentificacionTercero" >
                    <div class="labelTop">Nit</div>
                    <div class="campoTop" >
                        <input type="text" value="<?php echo $nit_tercero; ?>" readonly/>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Proveedor</div>
                    <div class="campoTop" style="width:277px;"><input type="text" value="<?php echo $tercero; ?>" readonly/></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">No. Cheque</div>
                    <div class="campoTop"><input type="text" value="<?php echo $numeroCheque; ?>" readonly/></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" value="<?php echo $usuario; ?>" readonly/></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Cuenta</div>
                    <div class="campoTop">
                        <input type="text" value="<?php echo $cuenta; ?>" readonly/>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Flujo de Efectivo</div>
                    <div class="campoTop">
                        <input type="text" value="<?php echo $flujo_efectivo; ?>" readonly/>
                    </div>
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
                url     : 'comprobante_egreso/bd/buscarGrillaContable.php',
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

    //===================================   IMPRIMIR ======================================================================//
    function imprimir<?php echo $opcGrillaContable; ?> (){
        window.open("comprobante_egreso/bd/imprimir_comprobante_egreso.php?id=<?php echo $id_comprobante_egreso; ?>&opc=cuentas&IMPRIME_PDF=true");
    }

    //================================== IMPRIMIR EN EXCEL =================================================================//
    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
       window.open("comprobante_egreso/bd/imprimir_comprobante_egreso.php?id=<?php echo $id_comprobante_egreso; ?>&opc=cuentas&IMPRIME_XLS=true");
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
                Ext.get("renderRestaurar<?php echo $opcGrillaContable; ?>").load({
                    url  : 'comprobante_egreso/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc               : 'cancelarDocumento',
                        id                : '<?php echo $id_comprobante_egreso; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                    }
                });
            };
        }
    }

    function ventanaObservacionCuenta<?php echo $opcGrillaContable; ?>(cont){
        var id = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_descripcion_cuenta = new Ext.Window({
            width       : 330,
            height      : 240,
            id          : 'Win_Ventana_descripcion_cuenta',
            title       : 'Observacion Cuenta',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'comprobante_egreso/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaObservacionCuenta',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idCuenta          : id,
                    cont              : cont,
                    id                : '<?php echo $id_comprobante_egreso; ?>',
                    readonly          : 'readonly'
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
                    handler     : function(){ Win_Ventana_descripcion_cuenta.close(id) }
                }
            ]
        }).show();
    }

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento<?php echo $opcGrillaContable ?>(){

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo")) {
            cargando_documentos('Editando Documento...','');
            Ext.get('renderRestaurar<?php echo $opcGrillaContable; ?>').load({
                url     : 'comprobante_egreso/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id       : '<?php echo $id_comprobante_egreso; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                }
            });
        }
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar<?php echo $opcGrillaContable ?>(){
        cargando_documentos('Restaurando Documento...','');
        Ext.get('renderRestaurar<?php echo $opcGrillaContable ?>').load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id       : '<?php echo $id_comprobante_egreso; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });
    }

    function ventanaEnviarCorreo_<?php echo $opcGrillaContable ?>(){

         var myalto  = Ext.getBody().getHeight();
         var myancho = Ext.getBody().getWidth();

         ventana_email = new Ext.Window({
             id          : 'Win_Ventana_EnviarOrden',
             title       : 'Enviar Cotizacion',
             iconCls     : 'pie2',
             width       : 950,
             height      : 530,
             modal       : true,
             autoDestroy : true,
             draggable   : false,
             resizable   : false,
             bodyStyle   : 'background-color:#DFE8F6;',
             autoLoad    :
             {
                 url     : "bd/mail_documentos_compras.php",
                 scripts : true,
                 nocache : true,
                 params  :
                 {
                     id                : '<?php echo $id_comprobante_egreso; ?>',
                     opcGrillaContable : '<?php echo $opcGrillaContable ?>',
                     urlImpresion      : 'comprobante_egreso/bd/imprimir_comprobante_egreso.php'
                 }
             }

         }).show();
    }


</script>
