<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");

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

    Ext.getCmp("Btn_exportar_NotaCierre").enable();
    // Ext.getCmp("Btn_articulos_Relacionados").disable();
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
    $user_permiso_restaurar = 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();Ext.getCmp("Btn_exportar_NotaCierre").disable();';     //restaurar

    $sql  = "SELECT 
                    consecutivo,
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
                    sucursal,
                    estado
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
    $sucursal          = mysql_result($query,0,'sucursal');
    $estado           = mysql_result($query,0,'estado');
    $observacion      = mysql_result($query,0,'observacion');


    if ($estado == 0) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }         //documento por editar
    if ($estado == 1) { $acumScript .='Ext.getCmp("Btn_exportar_NotaCierre").enable();'.$user_permiso_editar.$user_permiso_cancelar; }         //documento generado
    else if($estado == 3){ $acumScript .= $user_permiso_restaurar; }      //documento cancelado

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion        = str_replace($arrayReplaceString, "\\n", $observacion);


    if ($estado==1) {
        $acumScript.='Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();
                      Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();
                      Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").enable();
                      Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';
    }
    else if ($estado==3) {
        $acumScript.='Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();
                        Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();';
    }

    $acumScript .= (user_permisos(184,'false') == 'false')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(185,'false') == 'false')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();'   : '';
    $acumScript .= (user_permisos(186,'false') == 'false')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(187,'false') == 'false')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();' : '';

?>

<div class="w-full bg-white p-6 ">
    <div class="flex justify-between w-full gap-2">
        <table class="">
            <tr>
                <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm">Sucursal</th>
                <th class="p-2 text-sm"><?= $sucursal; ?></th>
                <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm">periodo</th>
                <th class="p-2 text-sm"><?= explode("-",$fecha_nota)[0]?></th>
            </tr>
            <tr>
                <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm">N. documento</th>
                <th class="p-2 text-sm"><?= $nit_tercero; ?></th>
                <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm">Nombre</th>
                <th class="p-2 text-sm"><?= $tercero; ?></th>
            </tr>
            <tr>
                <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm">Usuario</th>
                <th class="p-2 text-sm"><?= $usuario;?></th>
            </tr>
        </table>
        <table class="animate-pulse" id="total-pulse">
            <tr>
                <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm"><div class="border-4 rounded-full w-10  border-white"></div></th>
                <th class="p-2 text-sm"><div class="border-4 rounded-full w-10  border-white"></div></th>
            </tr>
            <tr>
            <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm"><div class="border-4 rounded-full w-10  border-white"></div></th>
                <th class="p-2 text-sm"><div class="border-4 rounded-full w-10  border-white"></div></th>
            </tr>
        </table>
        <table class="hidden" id="content-total">
            <tr>
                <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm">Debito</th>
                <th class="p-2 text-sm" id="total-debit"></th>
            </tr>
            <tr>
                <th class="px-3 py-2 bg-gray-300 text-gray-800 font-semibold text-sm">Credito</th>
                <th class="p-2 text-sm" id="total-credit"></th>
            </tr>
        </table>
    </div>    
    <table class="w-full mt-4">
        <tr>
            <th class="px-6 py-3 bg-gray-300 text-gray-800 font-semibold text-sm">Observaciones</th>
        </tr>
        <tr>
            <th><?= $observacion; ?></th>
        </tr>
    </table>
    <div class="w-full" style="height: 50%;">
        <data-table 
            endpoint='{
                        "url" : "nota_cierre/bd/Api.php",
                        "method" : "GET",
                        "params" : [
                                    {"id_nota":"<?=$id_nota?>"}
                                ]
                    }' 
            columns='[
                        {"field":"cuenta_puc", "alias":"Cuenta", "class":"","type":"","options":"","callback":""},
                        {"field":"descripcion_puc", "alias":"Descripcion", "class":"","type":"","options":"","callback":""},
                        {"field":"tercero", "alias":"Tercero", "class":"","type":"","options":"","callback":""},
                        {"field":"debe", "alias":"Debito", "class":"","type":"","options":"","callback":""},
                        {"field":"haber", "alias":"Credito", "class":"","type":"","options":"","callback":""}
                    ]'
        ></data-table>
    </div>
</div>

<script>
    <?php echo $acumScript; ?>
    
    async function fetch_debit_credit() {
        try {
            const response = await fetch("nota_cierre/bd/bd.php?opc=get_debit_credit&id_nota=<?=$id_nota?>"); // Espera la respuesta de la API
            if (!response.ok) {
                throw new Error(`Error: ${response.status}`); // Manejo de errores
            }
            const data = await response.json(); // Espera la conversión a JSON

            document.getElementById("total-pulse").classList.toggle("hidden")
            document.getElementById("content-total").classList.toggle("hidden")
            document.getElementById("total-debit").innerHTML = data.debito;
            document.getElementById("total-credit").innerHTML = data.credito;

        } catch (error) {
            console.error("Error obteniendo los datos:", error);
        }
    }
    fetch_debit_credit();

    // CERRAR LA VENTANA MODAL
    if (document.getElementById("modal")) {
        document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
    }
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
        if (!confirm("Aviso!\nSi elimina la nota se descontabilizara y se actualizara <?php echo $mensajeEdit; ?> \nAdemas se eliminaran las cuentas de la misma\nRealmente desea continuar?")) { return;}
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

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo <?php echo $mensajeEdit; ?>\nAdemas se eliminaran las cuentas de la misma")) {
            cargando_documentos('Editando Documento...');
            Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    idDocumento       : '<?php echo $id_nota; ?>',
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
                idDocumento       : '<?php echo $id_nota; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });
    }


</script>
