<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("config_var_global.php");
    include("../../funciones_globales/funciones_php/randomico.php");
    include("../../funciones_globales/funciones_javascript/totalesNotaContable.php");

    $id_empresa   = $_SESSION['EMPRESA'];
    $id_sucursal  = $_SESSION['SUCURSAL'];
    $id_usuario   = $_SESSION['IDUSUARIO'];
    $bodyArticle  = '';
    $acumScript   = '';
    $estado       = '';
    $fecha_nota   = date('Y-m-d');
    $cuenta_banco = 0;
?>
<script>

    //variables para calcular los valores de los costos y totales de la factura
    var debitoAcumulado<?php echo $opcGrilla; ?>  = 0.00
    ,   creditoAcumulado<?php echo $opcGrilla; ?> = 0.00
    ,   total<?php echo $opcGrilla; ?>            = 0.00
    ,   contItems<?php echo $opcGrilla; ?>        = 1
    ,   id_cliente_<?php echo $opcGrilla;?>       = 0;

    var timeOutObservacion<?php echo $opcGrilla; ?> = ''     // var timeOut autoguardado observaciones
    ,   codigoCliente<?php echo $opcGrilla; ?>      = 0
    ,   nitCliente<?php echo $opcGrilla; ?>         = 0
    ,   nombreCliente<?php echo $opcGrilla; ?>      = ''
    ,   nombre_grilla                               = 'ventanaBucarCuenta<?php echo $opcGrilla; ?>'; // nombre de la grilla cuando se busca un articulo

    Ext.getCmp("Btn_exportar_<?php echo $opcGrilla; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrilla; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrilla; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrilla; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrilla; ?>").show();

</script>
<?php

    //=================================// CUENTAS DE BANCOS //=================================//
    //*****************************************************************************************//
    $sqlBancos    = "SELECT cuenta,descripcion FROM puc WHERE activo=1 AND id_empresa='$id_empresa' AND tipo='Banco'";
    $queryBancos  = mysql_query($sqlBancos,$link);
    $optionBancos = '<select id="selectCuentaBanco" onchange="actualizarBanco(this);">';

    while ($rowBancos = mysql_fetch_array($queryBancos)) {
        if($cuenta_banco == 0){ $cuenta_banco = $rowBancos['id']; }

        $optionBancos .= '<option value="'.$rowBancos['cuenta'].'">'.$rowBancos['cuenta'].' '.$rowBancos['descripcion'].'</option>';
    }
    $optionBancos .= '</select>';

    //==================================// NUEVO DOCUMENTO //==================================//
    //*****************************************************************************************//
    if(!isset($id_documento)){

        // CREACION DEL ID UNICO
        $random_nota = responseUnicoRanomico();

        $sqlInsert   = "INSERT INTO $tablaPrincipal (id_empresa,random,id_sucursal,id_usuario,cedula_usuario,usuario,id_tipo_nota,sinc_nota)
                        VALUES('$id_empresa','$random_nota','$id_sucursal','".$_SESSION['IDUSUARIO']."','".$_SESSION['CEDULAFUNCIONARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."','$id_tipo_nota','$sinc_nota')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId  = "SELECT id FROM $tablaPrincipal  WHERE random='$random_nota' LIMIT 0,1";
        $id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fecha'.$opcGrilla.'",
                            editable   : false,
                            value      : new Date(),
                            listeners  : { select: function() { updateFechaNota'.$opcGrilla.'(this.value); } }
                        });

                        Ext.get("renderizaNewArticulo'.$opcGrilla.'").load({
                            url     : "'.$carpeta.'bd/bd.php",
                            scripts : true,
                            nocache : true,
                            params  :
                            {
                                opc           : "cargaHeadInsertUnidadesConTercero",
                                formaConsulta : "return",
                                cont          : 1,
                                opcGrilla     : "'.$opcGrilla.'",
                            }
                        });';
    }

    //=================================// DOCUMENTO GENERADO //=================================//
    //******************************************************************************************//
    else{

        include("../bd/functions_body_article.php");

        $sql = "SELECT IF (
                            consecutivo > 0,
                            fecha_nota,
                            DATE_FORMAT(NOW(),'%Y-%m-%d')
                        ) AS fecha_nota,
                    tipo_nota,
                    id_tipo_nota,
                    usuario,
                    observacion,
                    consecutivo,
                    estado
                FROM $tablaPrincipal
                WHERE id='$id_documento'";
        $query = mysql_query($sql,$link);

        $fecha_nota     = mysql_result($query,0,'fecha_nota');
        $tipo_nota      = mysql_result($query,0,'tipo_nota');
        $id_tipo_nota   = mysql_result($query,0,'id_tipo_nota');
        $usuario        = mysql_result($query,0,'usuario');
        $observacion    = mysql_result($query,0,'observacion');
        $consecutivo    = mysql_result($query,0,'consecutivo');
        $estado         = mysql_result($query,0,'estado');

        if ($estado==1) { echo "ESTA NOTA SE ENCUENTRA CERRADA POR QUE YA HA SIDO GENERADA"; exit; }
        if($consecutivo > 0){ $optionBancos = '<input type="text" value="'.$tipo_nota.'" readonly/><input type="hidden" value="'.$id_tipo_nota.'" id="selectCuentaBanco" style="width:135;"/>'; }
        else { $acumScript .= 'document.getElementById("selectCuentaBanco").value = "'.$id_tipo_nota.'";'; }

        if($estado == 0 && ($consecutivo == 0 || $consecutivo=='')){
            $sqlFecha   = "UPDATE $tablaPrincipal SET fecha_nota='$fecha_nota' WHERE id='$id_documento'";
            $queryFecha = mysql_query($sqlFecha,$link);
        }

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", $observacion);

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrilla.'",
                            editable   : false,
                            listeners  : { select: function() { updateFechaNota'.$opcGrilla.'(this.value); } }
                        });';

        $acumScript .=  'observacion'.$opcGrilla.'   = "'.$observacion.'";';

        $bodyArticle = loadItemsSave($id_documento,$observacion,$estado,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$id_empresa,$link);
    }

?>
<style>

    .fondo_modal_saldos{
        z-index  : 99999;
        top      : 0px;
        width    : 100%;
        height   : 100%;
        display  : table;
        left     : 0px;
        position : absolute !important;
    }

    #modal{
        display        : table-cell;
        vertical-align : middle;
    }

</style>
<div class="contenedorConciliacion" id="contenedorConciliacion" style="height:calc(100% - 95px); overflow:auto;">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="terminar<?php echo $opcGrilla; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop" style="width:135px;">
                    <div id="cargaFecha<?php echo $opcGrilla; ?>"></div>
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrilla; ?>" value="<?php echo $fecha_nota; ?>" readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrilla; ?>" value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>" readonly/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Banco</div>
                    <div class="campoTop">
                      <?php echo $optionBancos; ?>
                    </div>
                </div>
                <div style="float:left;max-width:20px; height:20px;margin-top:17px;overflow:hidden;" id="renderTipoNota">
                </div>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrilla; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrilla; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>

    var observacion<?php echo $opcGrilla; ?> = '';
    var globalNameFileUpload = '';
    <?php echo $acumScript; ?>

    var cuenta_banco = document.getElementById('selectCuentaBanco').value;


    //==================== FUNCION PARA CAMBIAR LA FECHA DE LA NOTA ==============================//
    function updateFechaNota<?php echo $opcGrilla; ?>(fecha){
        Ext.get('cargarFecha').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc       : 'actualizarFechaNota',
                opcGrilla : '<?php echo $opcGrilla; ?>',
                id        : '<?php echo $id_documento; ?>',
                fecha     : fecha
            }
        });
    }
    //====================// ACTUALIZA CUENTA DE BANCO //====================//
    //***********************************************************************//
    function actualizarBanco(selectCuentaBanco){
        confirm("Si cambia de Cuenta Bancaria toda la informacion registrada se eliminara desea continuar!");

        Ext.get('renderTipoNota').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc        : 'actualizarTipoNota',
                opcGrilla  : '<?php echo $opcGrilla; ?>',
                id         : '<?php echo $id_documento; ?>',
                idTipoNota : selectCuentaBanco.value,
            }
        });
    }

    //======================== GUARDAR LA OBSERVACION DE LA NOTA =============================//
    function inputObservacion<?php echo $opcGrilla; ?>(event,input){
        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13 || tecla == 9){
            guardarObservacion<?php echo $opcGrilla; ?>();
        }

        clearTimeout(timeOutObservacion<?php echo $opcGrilla; ?>);
        timeOutObservacion<?php echo $opcGrilla; ?> = setTimeout(function(){
            guardarObservacion<?php echo $opcGrilla; ?>();
        },1500);
    }

    function guardarObservacion<?php echo $opcGrilla; ?>(){
        var observacion = document.getElementById('observacion<?php echo $opcGrilla; ?>').value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

        clearTimeout(timeOutObservacion<?php echo $opcGrilla; ?>);
        timeOutObservacion<?php echo $opcGrilla; ?> = '';

        Ext.Ajax.request({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            params  :
            {
                opc         : 'guardarObservacion',
                id          : '<?php echo $id_documento; ?>',
                observacion : observacion,
                opcGrilla   : '<?php echo $opcGrilla; ?>'
            },
            success :function (result, request){
                        if(result.responseText != 'true'){
                            alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById("observacion<?php echo $opcGrilla; ?>").value=observacion<?php echo $opcGrilla; ?>;
                        }
                        else{ observacion<?php echo $opcGrilla; ?>=observacion; }

                    },
            failure : function(){ alert('Error de conexion con el servidor'); document.getElementById("observacion<?php echo $opcGrilla; ?>").value=observacion<?php echo $opcGrilla; ?>; }
        });
    }

    //============================= FILTRO TECLA BUSCAR TERCERO ==============================//
    function buscarCliente<?php echo $opcGrilla; ?>(event,Input){
        var tecla   = Input ? event.keyCode : event.which
        ,   inputId = Input.id
        ,   numero  = Input.value;

        if(inputId == "nitCliente<?php echo $opcGrilla; ?>" && numero==nitCliente<?php echo $opcGrilla; ?>){ return true; }
        else if(inputId == "codCliente<?php echo $opcGrilla; ?>" && numero==codigoCliente<?php echo $opcGrilla; ?>){ return true; }
        else if(Input.value != '' && (tecla == 13 )){
            Input.blur();
            ajaxbuscarCliente<?php echo $opcGrilla; ?>(Input.value, Input.id, document.getElementById('tipoDocumento<?php echo $opcGrilla; ?>').value);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        return true;
    }

    function ajaxbuscarCliente<?php echo $opcGrilla; ?>(codCliente, inputId, tipoDocumento){
        Ext.get('terminar<?php echo $opcGrilla; ?>').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc           : 'buscarCliente',
                codCliente    : codCliente,
                tipoDocumento : tipoDocumento,
                opcGrilla     : "<?php echo $opcGrilla; ?>",
                id            : '<?php echo $id_documento; ?>',
                inputId       : inputId
            }
        });
    }


    function renderizaResultadoVentanaTercero<?php echo $opcGrilla; ?>(id,cont){

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero<?php echo $opcGrilla; ?>_'+cont).setAttribute('src','img/eliminar.png');
        document.getElementById('imgBuscarTercero<?php echo $opcGrilla; ?>_'+cont).setAttribute('title','Eliminar Tercero');
        document.getElementById('imgBuscarTercero<?php echo $opcGrilla; ?>_'+cont).setAttribute('onclick'," eliminaTercero<?php echo $opcGrilla; ?>("+cont+")");

        var idInsert=document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value;

        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrilla; ?>_"+cont).style.display = 'block';
            document.getElementById("divImageDeshacer<?php echo $opcGrilla; ?>_"+cont).style.display = 'block';
        }

        var tercero = document.getElementById('div_cliente<?php echo $opcGrilla; ?>_nombre_'+id).innerHTML;

        document.getElementById("tercero<?php echo $opcGrilla; ?>_"+cont).value=tercero;
        document.getElementById("idTercero<?php echo $opcGrilla; ?>_"+cont).value=id;

        Win_VentanaCliente_<?php echo $opcGrilla; ?>.close();
    }

    //============================== FUNCION PARA ELIMINAR EL TERCERO ====================================================//
    function eliminaTercero<?php echo $opcGrilla; ?>(cont){

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero<?php echo $opcGrilla; ?>_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarTercero<?php echo $opcGrilla; ?>_'+cont).setAttribute('title','Buscar Tercero');
        document.getElementById('imgBuscarTercero<?php echo $opcGrilla; ?>_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrilla; ?>("+cont+")");

        var idInsert=document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value;

        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrilla; ?>_"+cont).style.display='block';
            document.getElementById("divImageDeshacer<?php echo $opcGrilla; ?>_"+cont).style.display='block';
        }

        document.getElementById("tercero<?php echo $opcGrilla; ?>_"+cont).value='';
        document.getElementById("idTercero<?php echo $opcGrilla; ?>_"+cont).value='';
    }

    //============================== FILTRO TECLA BUSCAR CUENTA ==========================================================//
    function buscarCuenta<?php echo $opcGrilla; ?>(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if (tecla == 13) {
            input.blur();
            ajaxBuscarCuenta<?php echo $opcGrilla; ?>(input.value, input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }
        if(document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+contIdInput).value > 0){
            document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+contIdInput).value  = 0;
            document.getElementById("descripcion<?php echo $opcGrilla; ?>_"+contIdInput).value = "";
            document.getElementById('divImageDeshacer<?php echo $opcGrilla; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrilla; ?>_"+contIdInput).style.display     = 'inline';
        }
        else if(document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+contIdInput).value > 0){
            document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+contIdInput).value    = 0;
            document.getElementById("descripcion<?php echo $opcGrilla; ?>_"+contIdInput).value = "";
        }
        return true;
    }

    function ajaxBuscarCuenta<?php echo $opcGrilla; ?>(valor,input){
        if (valor=='') { document.getElementById(input).focus(); return; }

        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrilla; ?>_'+arrayIdInput[1]).load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCuenta',
                opcGrilla : '<?php echo $opcGrilla; ?>',
                campo             : arrayIdInput[0],
                cuenta            : valor,
                contFila          : arrayIdInput[1],
                idProveedor       : id_cliente_<?php echo $opcGrilla;?>,
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    //====================================== VENTANA BUSCAR CUENTA  =======================================================//
    function ventanaBuscarCuenta<?php echo $opcGrilla; ?>(cont,opc){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();
        var titulo  = 'Colgaap';
        var sql     = '';
        var documentoCruce = document.getElementById('idDocumentoCruce<?php echo $opcGrilla; ?>_'+cont).value;
        var tipoDocumentoCruce = document.getElementById('documentoCruce<?php echo $opcGrilla; ?>_'+cont).value;
        if (documentoCruce>0 && opc!='niif') {
            sql=' AND id IN(SELECT id_cuenta AS id FROM asientos_colgaap WHERE id_documento='+documentoCruce+' AND tipo_documento="'+tipoDocumentoCruce+'" AND id_empresa=<?php echo $id_empresa ?> AND activo=1)';
            var prefijoDocumentoCruce=document.getElementById('prefijoDocumentoCruce<?php echo $opcGrilla; ?>_'+cont).value;
            var numeroDocumentoCruce=document.getElementById('numeroDocumentoCruce<?php echo $opcGrilla; ?>_'+cont).value;
            numeroDocumentoCruce=(prefijoDocumentoCruce!='')?prefijoDocumentoCruce+' - '+numeroDocumentoCruce: numeroDocumentoCruce;
            titulo = 'del Documento cruce '+numeroDocumentoCruce;
        }

        var cargaFuncion= 'responseVentanaBuscarCuenta<?php echo $opcGrilla; ?>(id,'+cont+');';
        if (opc=='niif') {
            titulo='Niif';
            cargaFuncion='responseVentanaBuscarCuentaNiif<?php echo $opcGrilla; ?>(id,'+cont+')';
        }

        Win_Ventana_buscar_cuenta_nota = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_cuenta_nota',
            title       : 'Seleccionar Cuenta '+titulo,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '<?php echo $carpeta;?>bd/buscarCuentaPuc.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    nombre_grilla : nombre_grilla,
                    opcGrilla     : '<?php echo $opcGrilla; ?>',
                    cargaFuncion  : cargaFuncion,
                    opc           : opc,
                    sql           : sql,
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
                    handler     : function(){ Win_Ventana_buscar_cuenta_nota.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarCuenta<?php echo $opcGrilla; ?>(id,cont){

        var cuenta      = document.getElementById('div_<?php echo $opcGrilla; ?>_cuenta_'+id).innerHTML
        ,   descripcion = document.getElementById('div_<?php echo $opcGrilla; ?>_descripcion_'+id).innerHTML;

        if (cuenta.length<6) { alert("Error!\nDebe seleccionar una cuenta con minimo 6 digitos"); return; }

        document.getElementById('debito<?php echo $opcGrilla; ?>_'+cont).focus();

        if(document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value > 0){
            document.getElementById('divImageDeshacer<?php echo $opcGrilla; ?>_'+cont).style.display = 'inline';
            document.getElementById("divImageSave<?php echo $opcGrilla; ?>_"+cont).style.display     = 'inline';

            document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value    = id;
            document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).value      = cuenta;
            document.getElementById('descripcion<?php echo $opcGrilla; ?>_'+cont).value = descripcion;
        }
        else{
            document.getElementById('divImageDeshacer<?php echo $opcGrilla; ?>_'+cont).style.display = "none";

            document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value    = id;
            document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).value      = cuenta;
            document.getElementById('descripcion<?php echo $opcGrilla; ?>_'+cont).value = descripcion;
        }

        Win_Ventana_buscar_cuenta_nota.close(id);
    }

    //============================= FILTRO TECLA GUARDAR CUENTA ==========================================================//
    function guardarAuto<?php echo $opcGrilla; ?>(event,input,cont){

        var idInsertCuenta  = document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value
        ,   tecla = input? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            guardarNewCuenta<?php echo $opcGrilla; ?>(cont);
        }

        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        else if (idInsertCuenta>0) {
            document.getElementById('divImageSave<?php echo $opcGrilla; ?>_'+cont).style.display     = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrilla; ?>_'+cont).style.display = 'inline';
        }

        patron = /[^\d.]/g;
        if(patron.test(value)){ input.value = input.value.replace(patron,''); }
        return true;
    }

    function guardarNewCuenta<?php echo $opcGrilla; ?>(cont){

        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value;
        var idPuc          = document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value;
        var cuenta         = document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).value;
        var debito         = document.getElementById('debito<?php echo $opcGrilla; ?>_'+cont).value;
        var credito        = document.getElementById('credito<?php echo $opcGrilla; ?>_'+cont).value;
        var opc            = 'guardarCuenta';
        var divRender      = '';
        var accion         = 'agregar';
        var terceroGeneral = document.getElementById("nombreCliente<?php echo $opcGrilla; ?>").value;
        var id_tercero     = document.getElementById("idTercero<?php echo $opcGrilla; ?>_"+cont).value;

        var id_documento_cruce    = document.getElementById('idDocumentoCruce<?php echo $opcGrilla; ?>_'+cont).value
        ,   numeroDocumentoCruce  = document.getElementById('numeroDocumentoCruce<?php echo $opcGrilla; ?>_'+cont).value
        ,   prefijoDocumentoCruce = document.getElementById('prefijoDocumentoCruce<?php echo $opcGrilla; ?>_'+cont).value
        ,   tipoDocumentoCruce    = document.getElementById('documentoCruce<?php echo $opcGrilla; ?>_'+cont).value
        ,   tipo_nota             = document.getElementById('selectCuentaBanco').value;

        //VALIDAR QUE LA FILA TENGA UNA CUENTA
        if (idPuc == 0){ alert('El campo cuenta es Obligatorio'); setTimeout(function(){ document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).focus(); },100); return; }
        //VALIDAR QUE TENGA ALMENOS UN VALOR EN EL DEBITO O CREDITO
        else if (debito>0 && credito>0) { alert('La cuenta no puede tener valores debito y credito\nSolo puede tener uno'); setTimeout(function(){ document.getElementById('debito<?php echo $opcGrilla; ?>_'+cont).focus(); },100); return; }
        else if (debito==0 && credito==0){ alert('La cuenta debe tener un valor para debito o credito'); setTimeout(function(){ document.getElementById('debito<?php echo $opcGrilla; ?>_'+cont).focus(); },100); return; }

        if(tipo_nota == 0
            && ( tipoDocumentoCruce != ''
                && (numeroDocumentoCruce==0 || isNaN(numeroDocumentoCruce) || id_tercero==0)
                    || prefijoDocumentoCruce != '' && (numeroDocumentoCruce==0 || isNaN(numeroDocumentoCruce) || tipoDocumentoCruce=='' || id_tercero==0)
                    || numeroDocumentoCruce > 0 && (tipoDocumentoCruce=='' || id_tercero==0)
                )
            ){
            alert('Aviso,\nCampos obligatorios para cruce con documento:\n* Tercero\n* Tipo Documento Cruce\n* Numero Documento Cruce');
            return;
        }

        //SI LA CUENTA ES LA MISMA QUE LA CUENTA DE PAGO VALIDAR LOS ABONOS
        if(cuenta==arrayCuentaPago[cont]){

            saldo = (debito>0)? debito : credito;        //VALIDAR QUE EL ABONO NO SEA MAYOR AL SALDO DEL DOCUMENTO CRUCE

            //SI ES UNA FV NO PUEDE TENER VALOR EN DEBITO, DEBE SER CREDITO
            if (tipoDocumentoCruce=='FV') {
                if (debito>0) {
                    alert("Error!\nIngrese  el valor para el asiento en credito");
                    setTimeout(function(){ document.getElementById('credito<?php echo $opcGrilla; ?>_'+cont).focus(); },100);
                    return;
                }
            }
            //SI ES UNA FC NO PUEDE TENER VALOR EN CREDITO, DEBE SER DEBITO
            else if (tipoDocumentoCruce=='FC') {
                if (credito>0) {
                    alert("Error!\nIngrese  el valor para el asiento en debito");
                    setTimeout(function(){ document.getElementById('debito<?php echo $opcGrilla; ?>_'+cont).focus(); },100);
                    return;
                }
            }

             if ((saldo*1)>(arraySaldoCuentaPago[cont]*1)) {
                alert(" Error!\nEl valor ingresado es mayor al saldo disponible!\nSaldo disponible: "+arraySaldoCuentaPago[cont]);
                return;
            }
        }

        //VALIDAR QUE NO SE REPITAN DOCUMENTOS CON LA MISMA INFORMACION EN CUENTAS
        for ( i = 0; i < cont; i++) {

            if (cuenta==arrayCuentaPago[i]) {

                if (tipoDocumentoCruce != ''
                    && id_documento_cruce == document.getElementById('idDocumentoCruce<?php echo $opcGrilla; ?>_'+i).value
                    && tipoDocumentoCruce == document.getElementById('documentoCruce<?php echo $opcGrilla; ?>_'+i).value)
                {
                    alert("Error!\nEl documento ya esta en la nota!\nNo se pueden repetir los documentos con las mismas cuentas!");
                    setTimeout(function(){ document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).focus(); },100);
                    return;
                }
            }
        }

        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertCuenta > 0){
            opc       = 'actualizaCuenta';
            divRender = 'renderArticulo<?php echo $opcGrilla; ?>_'+cont;
            accion    = 'actualizar';
        }
        else{
            //VALIDAMOS PARA NO REPETIR FILAS DE LAN GRILLA
            contItems<?php echo $opcGrilla; ?>++;
            divRender = 'bodyDivArticulos<?php echo $opcGrilla; ?>_'+contItems<?php echo $opcGrilla; ?>;
            var div   = document.createElement('div');
            div.setAttribute('id','bodyDivArticulos<?php echo $opcGrilla; ?>_'+contItems<?php echo $opcGrilla; ?>);
            div.setAttribute('class','bodyDivArticulos<?php echo $opcGrilla; ?>');
            document.getElementById('DivArticulos<?php echo $opcGrilla; ?>').appendChild(div);
        }


        Ext.get(divRender).load({
            url     : '<?php echo $carpeta;?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                   : opc,
                opcGrilla             : '<?php echo $opcGrilla; ?>',
                consecutivo           : contItems<?php echo $opcGrilla; ?>,
                cont                  : cont,
                idInsertCuenta        : idInsertCuenta,
                idPuc                 : idPuc,
                debe                  : debito,
                haber                 : credito,
                cuenta                : cuenta,
                id                    : '<?php echo $id_documento; ?>',
                id_tercero            : id_tercero,
                terceroGeneral        : terceroGeneral,
                id_documento_cruce    : id_documento_cruce,
                numeroDocumentoCruce  : numeroDocumentoCruce,
                prefijoDocumentoCruce : prefijoDocumentoCruce,
                tipoDocumentoCruce    : tipoDocumentoCruce
            }
        });
    }

    //======================= BORRAR UNA CUENTA =============================================================//
    function deleteCuenta<?php echo $opcGrilla; ?>(cont){
        var idCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value
        ,   debito   = document.getElementById('debito<?php echo $opcGrilla; ?>_'+cont).value
        ,   credito  = document.getElementById('credito<?php echo $opcGrilla; ?>_'+cont).value;

        debito  = (isNaN(debito))? 0: debito;
        credito = (isNaN(credito))? 0: credito;

        if(confirm('Esta Seguro de eliminar esta cuenta de la Nota Contable?')){
            Ext.get('renderArticulo<?php echo $opcGrilla; ?>_'+cont).load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc       : 'deleteCuenta',
                    opcGrilla : '<?php echo $opcGrilla; ?>',
                    idCuenta  : idCuenta,
                    cont      : cont,
                    id        : '<?php echo $id_documento; ?>'
                }
            });
            calcTotal<?php echo $opcGrilla ?>(debito,credito,'eliminar');
        }
    }

    //====================== CAMBIAR LA CUENTA NIIF POR DEFECTO DE LA COLGAAP ==================================//
    function cambiaCuentaNiif<?php echo $opcGrilla; ?>(cont) {
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value;

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
                    cont           : cont,
                    opc            : 'cargaConfiguracionCuenta',
                    idInsertCuenta : idInsertCuenta,
                    idCuenta       : idCuenta,
                    opcGrilla      : '<?php echo $opcGrilla; ?>',
                    id_nota        : '<?php echo $id_documento; ?>'
                }
            }
        }).show();
    }

    //=========================// VENTANA BUSCAR EL CENTRO DE COSTOS //=========================//
    //******************************************************************************************//
    function ventanaBuscarCentroCostos_<?php echo $opcGrilla; ?>(cont) {
        Win_Ventana_Ccos_<?php echo $opcGrilla; ?> = new Ext.Window({
            width       : 600,
            height      : 450,
            id          : 'Win_Ventana_Ccos_<?php echo $opcGrilla; ?>',
            title       : 'Seleccione el Centro de Costo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'nota_general/bd/centro_costos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrilla             : '<?php echo $opcGrilla; ?>',
                    impressFunctionScript : 'renderSelectedCcos_<?php echo $opcGrilla; ?>(id,'+cont+')'
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
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_Ccos_<?php echo $opcGrilla; ?>.close() }
                        }
                    ]
                }
            ]
        }).show();
    }

    function renderSelectedCcos_<?php echo $opcGrilla; ?>(id,cont){

        var nombre         = document.getElementById('div_centroCostos_<?php echo $opcGrilla; ?>_nombre_'+id).innerHTML
        var codigo         = document.getElementById('div_centroCostos_<?php echo $opcGrilla; ?>_codigo_'+id).innerHTML;
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value;

        Ext.Ajax.request({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            params  :
            {
                opc                  : 'actualizarCcos',
                idInsertCuenta       : idInsertCuenta,
                idCuenta             : idCuenta,
                opcGrilla            : '<?php echo $opcGrilla; ?>',
                id_centro_costos     : id,
                codigo_centro_costos : codigo,
                id_nota              : '<?php echo $id_documento; ?>'
            },
            success :function (result, request){
                        var response = (result.responseText).replace(/[^a-z]/g,'');
                        if(response == 'true'){

                            Win_Ventana_Ccos_<?php echo $opcGrilla; ?>.close();
                            document.getElementById('codigoCcos_<?php echo $opcGrilla; ?>').innerHTML=codigo;
                            document.getElementById('nombreCcos_<?php echo $opcGrilla; ?>').innerHTML=nombre;
                        }
                        else if(response == 'padre'){ alert("Error,\nNo se puede seleccionar un centro de costo padre"); }
                        else{ alert("Error,\nNo se acualizo el centro de costo en la cuenta"); }
                    },
            failure : function(){  }
        });
    }

    //===================== FUNCION QUE ABRE LA VENTANA PARA BUSCAR LA CUENTA NIIF A CAMBIAR ============================//
    function responseVentanaBuscarCuentaNiif<?php echo $opcGrilla; ?>(id,cont) {
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value;
        var cuenta         = document.getElementById('div_<?php echo $opcGrilla; ?>_cuenta_'+id).innerHTML
        Ext.Ajax.request({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            params  :
            {
                opc            : 'actualizarNiif',
                idInsertCuenta : idInsertCuenta,
                idCuenta       : idCuenta,
                opcGrilla      : '<?php echo $opcGrilla; ?>',
                cont           : cont,
                cuenta         : cuenta,
                id_niif        : id,
            },
            success :function (result, request){
                        result.responseText= (result.responseText).replace(/[^a-z]/g,'');
                        if(result.responseText == 'true'){
                            Win_Ventana_buscar_cuenta_nota.close();
                            Win_Ventana_cambiar_cuenta_niif.close();
                        }
                        else{ alert("Error\nNo se actualizo la cuenta niif, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema"); }
                    },
            failure : function(){  }
        });

    }

    //===================== CANCELAR LOS CAMBIOS DE UN CUENTA ===============================================//
    function retrocederCuenta<?php echo $opcGrilla; ?>(cont){
        //capturamos el id que esta asignado en la variable oculta
        id_actual = document.getElementById("idInsertCuenta<?php echo $opcGrilla; ?>_"+cont).value;

        Ext.get('renderArticulo<?php echo $opcGrilla; ?>_'+cont).load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc            : 'retrocederCuenta',
                cont           : cont,
                opcGrilla      : '<?php echo $opcGrilla; ?>',
                idCuentaInsert : id_actual,
                id             : '<?php echo $id_documento; ?>'
            }
        });
    }

    //=============================================== FUNCION SIN USO =================================================//
    function guardar<?php echo $opcGrilla; ?>(){ }                  // REEMPLAZADA POR VALIDAR QUE VALIDA Y GENERA LA NOTA.

    //===================================== VALIDAR LA NOTA ANTES DE GENERARLA ========================================//
    function validar_<?php echo $opcGrilla; ?>(opcionGenerar){
        opc = 'validaNota'
        if (opcionGenerar =='terminar') { opc = 'terminarGenerar'; }

        var cuenta_banco = document.getElementById('selectCuentaBanco').value;

        //VALIDACION CUENTAS POR GUARDAR
        var validacion = validarCuentas<?php echo $opcGrilla; ?>();
        if (validacion==0) { alert("No hay nada que guardar!"); return; }
        else if (validacion==1) { alert("Hay cuentas pendientes por guardar!"); return; }

        else if (validacion == 2 || validacion == 0) {
            var tipo_nota   = document.getElementById('selectCuentaBanco').value
            ,   observacion = document.getElementById("observacion<?php echo $opcGrilla; ?>").value;

            var arrayReturn=verificaDebitoCredito();
            var diferencia=parseFloat(arrayReturn[1])-parseFloat(arrayReturn[2]);
            if (arrayReturn[0]<2) { alert("Error!\nDebe ingresar minimo dos cuentas!");return; }
            if (diferencia!=0) { alert("Error!\nLa nota no esta correctamente balanceada\nTiene una diferencia de: $ "+(parseFloat(arrayReturn[1])-parseFloat(arrayReturn[2]))+"\nPor favor verifiquela y intentelo nuevamente"); return; }

            Ext.get('terminar<?php echo $opcGrilla; ?>').load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc        : opc,
                    id         : '<?php echo $id_documento; ?>',
                    id_tercero : id_cliente_<?php echo $opcGrilla; ?>,
                    opcGrilla  : '<?php echo $opcGrilla; ?>'
                }
            });
        }
    }


    //====================================// BUSCAR DOCUMENTO //====================================//
    //**********************************************************************************************//

    function buscar_<?php echo $opcGrilla; ?>(){

        var validacion = validarCuentas<?php echo $opcGrilla; ?>();
        if (validacion==1) {
            if(confirm("Aviso!\nHay cuentas pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrilla; ?>(); }
        }
        else if (validacion== 2 || validacion== 0) { ventanaBuscar<?php echo $opcGrilla; ?>(); }
    }

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
                    opc       : 'buscar_<?php echo $opcGrilla; ?>',
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

    //================================== VALIDACION NUMERICA EN CANTIDAD Y DESCUENTO ===================================//
    function validarNumberCuenta<?php echo $opcGrilla; ?>(event,input,typeValidate,cont){
        var contIdInput = (input.id).split('_')[1];
        var nombreInput = (input.id).split('_')[0];

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;

        //VALIDACION DE LA CANTIDAD DEL ARTICULO
        if (nombreInput=='cantArticulo<?php echo $opcGrilla; ?>') {
            if(tecla == 13 || tecla == 9){ ajaxVerificaCantidadArticulo<?php echo $opcGrilla; ?>(cont,input.value,'<?php echo $opcGrilla; ?>'); return; }
        }

        if(tecla == 13){
            document.getElementById('credito<?php echo $opcGrilla; ?>_'+contIdInput).focus();
            return true;
        }

        patron = /[^\d.]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
        else{
            document.getElementById('divImageSave<?php echo $opcGrilla; ?>_'+contIdInput).style.display = 'inline';

            if(document.getElementById('idInsertCuenta<?php echo $opcGrilla; ?>_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrilla; ?>_'+contIdInput).style.display = 'inline';
            }
        }
    }

    //============================  VALIDAR QUE NO HAYA NINGUN CUENTA POR GUARDAR O POR ACTULIZAR =======================//
    function validarCuentas<?php echo $opcGrilla; ?>(){

        var cont = 0
        ,   contTotal = 0
        ,   contArticulo
        ,   nameArticulo
        ,   divsArticulos<?php echo $opcGrilla; ?> = document.querySelectorAll(".bodyDivArticulos<?php echo $opcGrilla; ?>");

        for(i in divsArticulos<?php echo $opcGrilla; ?>){

            if(typeof(divsArticulos<?php echo $opcGrilla; ?>[i].id)!='undefined'){

                contTotal++;

                nameArticulo = (divsArticulos<?php echo $opcGrilla; ?>[i].id).split('_')[0];
                contArticulo = (divsArticulos<?php echo $opcGrilla; ?>[i].id).split('_')[1];

                if(     document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+contArticulo).value > 0
                    &&  document.getElementById('imgSaveArticulo<?php echo $opcGrilla; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                    ||  document.getElementById('imgSaveArticulo<?php echo $opcGrilla; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
                    &&  document.getElementById('divImageSave<?php echo $opcGrilla; ?>_'+contArticulo).style.display == 'inline')
                    { cont++; }
            }
        }

        if(contTotal==0 || contTotal==1){ return 0; }       // si no se han almacenado articulos retornamos 0
        else if(cont > 0){ return 1; }                      // si hay articulos pendientes por guardar o actualizar retornamos 1
        else { return 2; }                                  // si toda la validacion esta bien retornamos 2
    }

    //============================ CANCELAR UN DOCUMENTO =========================================================================//

    function cancelar_<?php echo $opcGrilla; ?>(){
        var contItems = 0;

        if(!document.getElementById('DivArticulos<?php echo $opcGrilla; ?>')){ return; }

        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrilla; ?>').querySelectorAll('.campoNombreArticulo');

        for(i in arrayIdsArticulos){ if(arrayIdsArticulos[i].innerHTML != '' ){ contItems++; } }

        if(contItems > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
                Ext.get("terminar<?php echo $opcGrilla; ?>").load({
                    url  : '<?php echo $carpeta; ?>bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc       : 'cancelarDocumento',
                        id        : '<?php echo $id_documento; ?>',
                        opcGrilla : '<?php echo $opcGrilla; ?>'
                    }
                });
            };
        }
    }

</script>