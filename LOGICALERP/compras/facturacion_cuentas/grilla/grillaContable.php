<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../funciones_globales/funciones_php/randomico.php");
    include("../../../funciones_globales/funciones_javascript/totalesNotaContable.php");

    $id_empresa   = $_SESSION['EMPRESA'];
    $id_sucursal  = $_SESSION['SUCURSAL'];
    $id_usuario   = $_SESSION['IDUSUARIO'];
    $bodyArticle  = '';
    $acumScript   = '';
    $estado       = '';
    $fecha_actual = date('Y-m-d');
    $id_tipo_nota = 0;
?>
<script>

    //variables para calcular los valores de los costos y totales de la factura
    var debitoAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   creditoAcumulado<?php echo $opcGrillaContable; ?> = 0.00
    ,   total<?php echo $opcGrillaContable; ?>            = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>    = 1
    ,   id_cliente_<?php echo $opcGrillaContable;?>       = 0;

    var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoCliente<?php echo $opcGrillaContable; ?>      = 0
    ,   nitCliente<?php echo $opcGrillaContable; ?>         = 0
    ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
    ,   arrayCuentaPago                                     = new Array()
    ,   arraySaldoCuentaPago                                = new Array()
    ,   arrayTipoNota                                       = new Array()
    ,   nombre_grilla                                       = 'ventanaBucarCuenta<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

    // document.getElementById("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").setAttribute('style','display:none');
    <?php if($editarFacturaItem != 'true'){ echo 'Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").disable();'; } ?>

    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();

</script>
<?php

    if($editarFacturaItem != 'true'){
        $acumScript .= (user_permisos(154,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';       //guardar
        $acumScript .= (user_permisos(156,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';      //calcelar
    }
    else{ include_once('../cuentas_factura_items/contabilidad_item_cuentas.php'); }

    // FORMAS DE PAGO
    $formasPago      = '';
    $idFormaPago     = '';
    $arrayFormasPago = 'var idFechaSavePagoFactura = "";
                        var arrayFormaPagoFacutraCompra = new Array();';

    $sqlFormasPago   = "SELECT id,nombre,plazo FROM configuracion_formas_pago WHERE activo=1 AND id_empresa='$id_empresa'";
    $queryFormasPago = mysql_query($sqlFormasPago,$link);

    while ($rowFormasPago=mysql_fetch_array($queryFormasPago)) {
        if ($idFormaPago=='') {
            $idFormaPago     = $rowFormasPago['id'];
            $diasFormaPago   = $rowFormasPago['plazo'];
            $nombreFormaPago = $rowFormasPago['nombre'];

            $arrayFormasPago .= 'idFechaSavePagoFactura = "'.$idFormaPago.'";';
        }
        $formasPago      .= '<option value="'.$rowFormasPago['id'].'" >'.$rowFormasPago['nombre'].'</option>';
        $arrayFormasPago .= 'arrayFormaPagoFacutraCompra['.$rowFormasPago['id'].'] = "'.$rowFormasPago['plazo'].'";';
    }


    if ($formasPago=='') {
        echo'<script>
                alert("Error!\nNo hay ninguna forma de pago configurada\nDirijase al panel de control->formas de pago\nCree una y vuelva a intentarlo");
                // Ext.getCmp("Btn_cancelar_FacturaCompra").enable();
            </script>';
        exit;
    }

    // CUENTA DE PAGO
    $cuentasPago      = '<option value="0" >Seleccione...</option>';
    $sqlCuentasPago   = "SELECT id,nombre,cuenta,cuenta_niif
                        FROM configuracion_cuentas_pago
                        WHERE activo=1 AND id_empresa='$id_empresa' AND tipo='Compra' AND (id_sucursal='$id_sucursal' OR id_sucursal=0)";
    $queryCuentasPago = mysql_query($sqlCuentasPago,$link);

    while ($rowCuentasPago=mysql_fetch_array($queryCuentasPago)) {
        if ($idConfigCuentaPago == ''){
            $idConfigCuentaPago = $rowCuentasPago['id'];
            $cuentaPago         = $rowCuentasPago['cuenta'];
            $cuentaPagoNiif     = $rowCuentasPago['cuenta_niif'];
            $configuracionCuentaPago = $rowCuentasPago['nombre'];
        }
        $cuentasPago .= '<option value="'.$rowCuentasPago['id'].'" >'.$rowCuentasPago['nombre'].' '.$rowCuentasPago['cuenta'].'</option>';
    }

    if ($cuentasPago=='') {
        echo'<script>
                alert("Error!\nNo hay ninguna cuenta de pago configurada\nDirijase al panel de control->cuentas de pago\nCree una y vuelva a intentarlo");
                // Ext.getCmp("Btn_cancelar_FacturaCompra").enable();
            </script>';
        exit;
    }

    //=================================// NUEVA FACTURA //==================================//
    //**************************************************************************************//
    if(!isset($id_factura_compra)){

        // CREACION DEL ID RANDOMICO, Y CONSULTA ID INSERT FACTURA
        $random_factura = responseUnicoRanomico();

        $sqlInsertFactura   = "INSERT INTO compras_facturas(
                                    id_empresa,
                                    random,
                                    id_sucursal,
                                    fecha_final,
                                    id_forma_pago,
                                    dias_pago,
                                    forma_pago,
                                    id_configuracion_cuenta_pago,
                                    configuracion_cuenta_pago,
                                    cuenta_pago,
                                    cuenta_pago_niif,
                                    id_usuario,
                                    factura_por_cuentas)
                                VALUES('$id_empresa',
                                    '$random_factura',
                                    '$id_sucursal',
                                    '$fecha_actual',
                                    '$idFormaPago',
                                    '$diasFormaPago',
                                    '$nombreFormaPago',
                                    '$idConfigCuentaPago',
                                    '$configuracionCuentaPago',
                                    '$cuentaPago',
                                    '$cuentaPagoNiif',
                                    '$id_usuario',
                                    'true')";
        $queryInsertFactura = mysql_query($sqlInsertFactura,$link);

        $sqlSelectIdFactura = "SELECT id FROM compras_facturas WHERE random='$random_factura' LIMIT 0,1";
        $id_factura_compra  = mysql_result(mysql_query($sqlSelectIdFactura,$link),0,'id');

        $acumScript .= ' new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fechaFacturaCuentas",
                            editable   : false,
                            value      : "'.$fecha_actual.'",
                            listeners  : { select: function(combo, value) { guardaFechaFacturaCuentas(this);  } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fechaFinalFacturaCuentas",
                            editable   : false,
                            value      : "'.$fecha_actual.'",
                            listeners  : { select: function(combo, value) { guardaFechaFacturaCuentas(this);  } }
                        });
                        document.getElementById("titleDocumentoFacturaCompraCuentas").innerHTML="";

                        Ext.get("renderizaNewArticulo'.$opcGrillaContable.'").load({
                            url     : "'.$carpeta.'bd/bd.php",
                            scripts : true,
                            nocache : true,
                            params  :
                            {
                                opc               : "cargaHeadInsertUnidadesConTercero",
                                formaConsulta     : "return",
                                cont              : 1,
                                opcGrillaContable : "'.$opcGrillaContable.'",
                            }
                        });';
    }

    //==============================// SI EXISTE LA FACTURA //==============================//
    //**************************************************************************************//
    else{

        include("../bd/functions_body_article.php");

        $sql   = "SELECT id_proveedor,
                    prefijo_factura,
                    if(numero_factura > 0,numero_factura,'') AS numero_factura,
                    nit,
                    cod_proveedor,
                    proveedor,
                    plantillas_id,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha_inicio,
                    date_format(fecha_final,'%Y-%m-%d') AS fecha_final,
                    observacion,
                    estado,
                    id_forma_pago,
                    id_configuracion_cuenta_pago,
                    usuario_recibe_en_almacen,
                    contabilidad_manual,
                    subtotal_manual,
                    iva_manual,
                    total_manual,
                    id_tipo_factura
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
        $id_forma_pago             = mysql_result($query,0,'id_forma_pago');
        $idConfigCuentaPago        = mysql_result($query,0,'id_configuracion_cuenta_pago');
        $usuario_recibe_en_almacen = mysql_result($query,0,'usuario_recibe_en_almacen');
        $observacion               = mysql_result($query,0,'observacion');
        $id_tipo_factura       = mysql_result($query,0,'id_tipo_factura');

        // if ($estado==1) { echo "ESTA NOTA SE ENCUENTRA CERRADA POR QUE YA HA SIDO GENERADA"; exit; }
        // if($consecutivo > 0){ $tiposNotas = '<input type="text" value="'.$tipo_nota.'" readonly/><input type="hidden" value="'.$id_tipo_nota.'" id="selectTipoNota" style="width:135;"/>'; }
        // else { $acumScript .= 'document.getElementById("selectTipoNota").value = "'.$id_tipo_nota.'";'; }

        //BTN SINC NOTA OCULTAR EL BUTTON GROUP DE CONTABILIZAR CON NIIF
        // if($sinc_nota == 'colgaap_niif'){
        //     $acumScript .= 'Ext.getCmp("GroupBtnSync").hide();
        //                     Ext.getCmp("GroupBtnNoSync").show();';
        // }
        // else if($sinc_nota == 'colgaap'){
        //     $acumScript .= 'Ext.getCmp("GroupBtnSync").show();
        //                     Ext.getCmp("GroupBtnNoSync").hide();';
        // }
        // else{ echo "ESTA NOTA NO TIENE DEFINIDA UNA FORMA DE SINCRONIZACION COLGAAP"; exit; }
        // $acumScript .= 'var sinc_nota_'.$opcGrillaContable.' = "'.$sinc_nota.'";';


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


        $acumScript .=  '
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
                        document.getElementById("codigoTercero'.$opcGrillaContable.'").value = "'.$cod_proveedor.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$proveedor.'";
                        document.getElementById("prefijoFacturaCuentas").value               = "'.$prefijo_factura.'";
                        document.getElementById("numeroFacturaCuentas").value                = "'.$numero_factura.'";
                        // document.getElementById("nombreEmpleadoRecibioAlmacen").value     = "'.$usuario_recibe_en_almacen.'";
                        document.getElementById("selectFormaPagoCompraCuentas").value        = "'.$id_forma_pago.'";
                        document.getElementById("fechaFacturaCuentas").value                 = "'.$fecha_inicio.'";
                        document.getElementById("fechaFinalFacturaCuentas").value            = "'.$fecha_final.'";
                        document.getElementById("accounts_invoice_type").value        = "'.$id_tipo_factura.'";

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fechaFacturaCuentas",
                            editable   : false,
                            listeners  : { select: function(combo, value) { guardaFechaFacturaCuentas(this);  } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fechaFinalFacturaCuentas",
                            editable   : false,
                            listeners  : { select: function(combo, value) { guardaFechaFacturaCuentas(this);  } }
                        });';


        $bodyArticle = cargaArticulosSaveConTercero($id_factura_compra,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
    }

    $habilita = ($estado=='1')? 'onclick="javascript: return false;" disabled ': '';
    $acumScript .= 'document.getElementById("selectCuentaPagoCompraCuentas").value="'.$idConfigCuentaPago.'"; '.$arrayFormasPago ;

    // CONSULTAR LOS TIPOS DE FACTURAS
    $sql   = "SELECT id,nombre FROM compras_facturas_tipos WHERE activo=1 AND id_empresa='$id_empresa'";
    $query = $mysql->query($sql);
    while ($row=$mysql->fetch_array($query)) {
        $tipos .= "<option value='$row[id]' >$row[nombre]</option>";
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
                    <div class="campoTop"><input type="text" id="fechaFacturaCuentas"></div>
                </div>
                <div class="renglonTop" >
                    <div class="labelTop">Fecha de Vencimiento</div>
                    <div class="campoTop"><input type="text" id="fechaFinalFacturaCuentas"></div>
                </div>
                <div class="renglonTop" style="width:137px;display:none;">
                    <div class="labelTop" style="float:left; width:100%;">Forma de pago</div>
                    <div id="renderSelectFormaPago" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="overflow:hidden;">
                        <select id="selectFormaPagoCompraCuentas" onChange="UpdateFormaPagoCompra(this.value)" style="float:left;"/>
                            <?php echo $formasPago; ?>
                        </select>
                    </div>
                </div>
                <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Cuenta de pago</div>
                    <div id="renderSelectCuentaPago" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <select id="selectCuentaPagoCompraCuentas" onChange="UpdateCuentaPagoCompraCuentas(this.value)" style="float:left;"/>
                            <?php echo $cuentasPago; ?>
                        </select>
                    </div>
                </div>

                <div class="renglonTop" style="width:135px;">
                    <div class="labelTop">Factura #</div>
                    <div id="renderNumeroFactura" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop">
                        <input type="text" id="prefijoFacturaCuentas" style="width:30% !important; float:left;" onKeyup="convertirMayusculas(this)" onchange="validarNumeroFactura();">
                        <div style="width:10% !important; float:left;background-color:#F3F3F3; height:100%; text-align:center;">-</div>
                        <input type="text" id="numeroFacturaCuentas" style="width:60% !important; float:left;" onchange="validarNumeroFactura();">
                    </div>
                </div>

                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha"></div>
                <div class="renglonTop" id="divCodigoTercero">
                    <div class="labelTop">Codigo Proveedor</div>
                    <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $codigo_tercero; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" ></div>
                </div>
                <div class="renglonTop" id="divIdentificacionTercero">
                    <div class="labelTop">Numero Identificacion</div>
                    <div class="campoTop">
                        <input type="text" style="width:161px"  id="nitCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $nitTercero; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Proveedor</div>
                    <div class="campoTop" style="width:277px;">
                        <input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $tercero; ?>" Readonly/>
                    </div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaCliente<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Proveedor">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>" readonly/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Tipo de factura</div>
                    <div class="campoTop">
                        <select id="accounts_invoice_type" onchange="updateInvoceType({invoice_id:'<?= $id_factura_compra?>',select:this})">
                            <option>Seleccione...</option>
                            <?=$tipos?>
                        </select>
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
    var globalNameFileUpload = '';
    <?php echo $acumScript; ?>

    document.getElementById("numeroFacturaCuentas").onkeyup  = function(event){ return validarNumeroPrefijoFactura(event,this); };
    document.getElementById("prefijoFacturaCuentas").onkeyup = function(event){ return validarNumeroPrefijoFactura(event,this); };

    function convertirMayusculas(input){ input.value=input.value.toUpperCase(); }

    function validarNumeroFactura(input){
      if(document.getElementById('nombreClienteFacturaCompraCuentas').value == ""){
        alert("Por favor añadir primero un proveedor a la factura.");
        return;
      }

      nitProveedor = document.getElementById('nitClienteFacturaCompraCuentas').value;
      prefijo      = document.getElementById('prefijoFacturaCuentas').value;
      numero       = document.getElementById('numeroFacturaCuentas').value;

      if(numero == "" || numero == null || numero == "undefined"){
        return;
      }

      Ext.get('renderNumeroFactura').load({
        url     : 'facturacion_cuentas/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc            : 'validarNumeroFactura',
                    prefijoFactura : prefijo,
                    numeroFactura  : numero,
                    idFactura      : '<?php echo $id_factura_compra; ?>',
                    nitProveedor   : nitProveedor
                  }
      });
    }

    function validarNumeroPrefijoFactura(event,input){
      var tecla = input ? event.keyCode : event.which
      , inputId = input.id
      , numero  = input.value;

      if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

      if(inputId == 'numeroFacturaCuentas'){ patron = /[^\d]/; }
      else{ patron = /[^A-Za-z0-9]/; }

      if(patron.test(numero)){ input.value = numero.replace(patron,''); }
      return true;
    }

    function guardaFechaFacturaCuentas(inputDate){
        var idInputDate  = inputDate.getEl().id
        ,   valInputDate = inputDate.value;

        Ext.Ajax.request({
            url     : 'facturacion_cuentas/bd/bd.php',
            params  :
            {
                opc          : 'guardarFechaFactura',
                idInputDate  : idInputDate,
                valInputDate : valInputDate,
                idFactura    : '<?php echo $id_factura_compra; ?>'
            },
            success :function (result, request){
                        var resul=result.responseText;
                        resul=resul.replace(/\s/g,'');
                        // console.log(result.responseText);
                        // console.log(resul+'--');
                        if(resul == 'true'){
                            if(idInputDate=='fechaFactura'){ fecha_inicio=valInputDate; }
                            else if(idInputDate=='fechaFinalFactura'){ fecha_final=valInputDate; }
                        }
                        else{
                            if(idInputDate=='fechaFactura'){ document.getElementById(idInputDate).value= fecha_inicio; }
                            else if(idInputDate=='fechaFinalFactura'){ document.getElementById(idInputDate).value= fecha_final; }
                            alert(result.responseText+'No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                        }
                    },
            failure : function(){
                        if(idInputDate=='fechaFactura'){ document.getElementById(idInputDate).value= fecha_inicio; }
                        else if(idInputDate=='fechaFinalFactura'){ document.getElementById(idInputDate).value= fecha_final; }
                        alert('Error de conexion con el servidor');
                    }
        });
    }

    //================================== UPDATE CUENTAS DE PAGO ====================================//
    function UpdateCuentaPagoCompraCuentas(idCuentaPago){
        Ext.get('renderSelectCuentaPago').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc : 'UpdateCuentaPago',
                id  : '<?php echo $id_factura_compra; ?>',
                idCuentaPago : idCuentaPago,
            }
        });
    }

    //======================== GUARDAR LA OBSERVACION DE LA NOTA =============================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
        document.getElementById('labelObservacionFacturaCuentas').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13 || tecla == 9){
            guardarObservacion<?php echo $opcGrillaContable; ?>();
        }

        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = setTimeout(function(){
            guardarObservacion<?php echo $opcGrillaContable; ?>();
        },1500);
    }

    function guardarObservacion<?php echo $opcGrillaContable; ?>(){
        var observacion = document.getElementById('observacion<?php echo $opcGrillaContable; ?>').value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = '';

        Ext.Ajax.request({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            params  :
            {
                opc               : 'guardarObservacion',
                id                : '<?php echo $id_factura_compra; ?>',
                observacion       : observacion,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            },
            success :function (result, request){
                        result.responseText= (result.responseText).replace(/[^a-z]/g,'');
                        if(result.responseText != 'true'){
                            // alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                            document.getElementById('labelObservacionFacturaCuentas').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacionFacturaCuentas').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
                        else{
                            document.getElementById('labelObservacionFacturaCuentas').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardada</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacionFacturaCuentas').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                            observacion<?php echo $opcGrillaContable; ?>=observacion;
                        }

                    },
            failure : function(){
                        // alert('Error de conexion con el servidor');
                        document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                        document.getElementById('labelObservacionFacturaCuentas').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                        setTimeout(function () {
                            document.getElementById('labelObservacionFacturaCuentas').innerHTML='<b>OBSERVACIONES</b>';
                        },1200);
                    }
        });
    }

    //============================= FILTRO TECLA BUSCAR TERCERO ==============================//
    function buscarCliente<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla   = Input ? event.keyCode : event.which
        ,   inputId = Input.id
        ,   numero  = Input.value;

        if(inputId == "nitCliente<?php echo $opcGrillaContable; ?>" && numero==nitCliente<?php echo $opcGrillaContable; ?>){ return true; }
        else if(inputId == "codCliente<?php echo $opcGrillaContable; ?>" && numero==codigoCliente<?php echo $opcGrillaContable; ?>){ return true; }
        else if(Input.value != '' && (tecla == 13 )){
            Input.blur();
            ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(Input.value, Input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        return true;
    }

    function ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codCliente, inputId){
        Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCliente',
                codCliente        : codCliente,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_factura_compra; ?>',
                inputId           : inputId
            }
        });
    }

    function buscarTerceroCuenta<?php echo $opcGrillaContable; ?>(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   nit = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if (tecla == 13) {
            if (nit=='') {document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+contIdInput).focus(); return;}
            input.blur();
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscarTerceroCuenta',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    nit               : nit,
                    contFila          : contIdInput,
                }
            });
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_'+contIdInput).value  = 0;
            document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';
        }
        else if(document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idTercero<?php echo $opcGrillaContable; ?>_'+contIdInput).value    = 0;
            document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        }
    }

    //============================ VENTANA BUSCAR CLIENTE =======================================//
    function buscarVentanaCliente<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaCliente_<?php echo $opcGrillaContable; ?>',
            title       : 'Terceros',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : '',
                    cargaFuncion  : 'renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id);',
                    nombre_grilla : 'cliente<?php echo $opcGrillaContable; ?>'
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
                    handler     : function(){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id){

        var codigo  = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_numero_identificacion_'+id).innerHTML;

        if (id == id_cliente_<?php echo $opcGrillaContable;?>){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }

        ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codigo, 'nitCliente<?php echo $opcGrillaContable; ?>');
        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
    }

    //============================ VENTANA BUSCAR CLIENTE =======================================//
    function buscarVentanaTercero<?php echo $opcGrillaContable; ?>(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaCliente_<?php echo $opcGrillaContable; ?>',
            title       : 'Terceros',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : '',
                    cargaFuncion  : 'renderizaResultadoVentanaTercero<?php echo $opcGrillaContable; ?>(id,'+cont+');',
                    nombre_grilla : 'cliente<?php echo $opcGrillaContable; ?>'
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
                    handler     : function(){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentanaTercero<?php echo $opcGrillaContable; ?>(id,cont){

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('src','img/eliminar.png');
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('title','Eliminar Tercero');
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('onclick'," eliminaTercero<?php echo $opcGrillaContable; ?>("+cont+")");

        var idInsert=document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display = 'block';
            document.getElementById("divImageDeshacer<?php echo $opcGrillaContable; ?>_"+cont).style.display = 'block';
        }

        var tercero = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML
        ,   nit = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_numero_identificacion_'+id).innerHTML

        document.getElementById("nit<?php echo $opcGrillaContable; ?>_"+cont).value=nit;
        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).value=tercero;
        document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value=id;

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
    }

    //============================== FUNCION PARA ELIMINAR EL TERCERO ====================================================//
    function eliminaTercero<?php echo $opcGrillaContable; ?>(cont){

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('title','Buscar Tercero');
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+cont+")");

        var idInsert=document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display='block';
            document.getElementById("divImageDeshacer<?php echo $opcGrillaContable; ?>_"+cont).style.display='block';
        }

        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).value='';
        document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value='';
        // document.getElementById("prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value='';
        // document.getElementById("numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value='';
    }

    //============================== FILTRO TECLA BUSCAR CUENTA ==========================================================//
    function buscarCuenta<?php echo $opcGrillaContable; ?>(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if (tecla == 13) {
            input.blur();
            ajaxBuscarCuenta<?php echo $opcGrillaContable; ?>(input.value, input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }
        if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value  = 0;
            document.getElementById("descripcion<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';
        }
        else if(document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value    = 0;
            document.getElementById("descripcion<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        }
        return true;
    }

    function ajaxBuscarCuenta<?php echo $opcGrillaContable; ?>(valor,input){
        if (valor=='') { document.getElementById(input).focus(); return; }

        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCuenta',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                campo             : arrayIdInput[0],
                cuenta            : valor,
                contFila          : arrayIdInput[1],
                idProveedor       : id_cliente_<?php echo $opcGrillaContable;?>,
                id                : '<?php echo $id_factura_compra; ?>'
            }
        });
    }


    function ajaxCambia<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="contTopFila" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor y se deshabilitan
        var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
        for(i in checks){ checks[i].checked=false; checks[i].disabled=true; }

        Ext.get("contenedor_facturacion_compras").load({
            url     : "facturacion_compras.php",
            scripts : true,
            nocache : true,
            params  : { filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value }
        });
    }

    //====================================== VENTANA BUSCAR CUENTA  =======================================================//
    function ventanaBuscarCuenta<?php echo $opcGrillaContable; ?>(cont,opc){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();
        var titulo  = 'Colgaap';
        var sql     = '';
        var documentoCruce = document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDocumentoCruce = document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
        if (documentoCruce>0 && opc!='niif') {
            sql=' AND id IN(SELECT id_cuenta AS id FROM asientos_colgaap WHERE id_documento='+documentoCruce+' AND tipo_documento="'+tipoDocumentoCruce+'" AND id_empresa=<?php echo $id_empresa ?> AND activo=1)';
            var prefijoDocumentoCruce=document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
            var numeroDocumentoCruce=document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
            numeroDocumentoCruce=(prefijoDocumentoCruce!='')?prefijoDocumentoCruce+' - '+numeroDocumentoCruce: numeroDocumentoCruce;
            titulo = 'del Documento cruce '+numeroDocumentoCruce;
        }
        var cargaFuncion= 'responseVentanaBuscarCuenta<?php echo $opcGrillaContable; ?>(id,'+cont+');';
        if (opc=='niif') {
            titulo='Niif';
            cargaFuncion='responseVentanaBuscarCuentaNiif<?php echo $opcGrillaContable; ?>(id,'+cont+')';
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
                url     : '<?php echo $carpeta;?>/bd/buscarCuentaPuc.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    nombre_grilla     : nombre_grilla,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cargaFuncion      : cargaFuncion,
                    opc               : opc,
                    sql               : sql,
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

    function responseVentanaBuscarCuenta<?php echo $opcGrillaContable; ?>(id,cont){

        var cuenta      = document.getElementById('div_<?php echo $opcGrillaContable; ?>_cuenta_'+id).innerHTML
        ,   descripcion = document.getElementById('div_<?php echo $opcGrillaContable; ?>_descripcion_'+id).innerHTML;

        if (cuenta.length<6) {alert("Error!\nDebe seleccionar una cuenta con minimo 6 digitos"); return; }

        document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus();

        if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';

            document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value    = id;
            document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value      = cuenta;
            document.getElementById('descripcion<?php echo $opcGrillaContable; ?>_'+cont).value = descripcion;
        }
        else{
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = "none";

            document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value    = id;
            document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value      = cuenta;
            document.getElementById('descripcion<?php echo $opcGrillaContable; ?>_'+cont).value = descripcion;
        }

        Win_Ventana_buscar_cuenta_nota.close(id);
    }

    //===================================== VENTANA PARA BUSCAR EL CENTRO DE COSTOS ========================================//
    function ventanaBuscarCentroCostos(cont) {
        Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 600,
            height      : 450,
            id          : 'Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>',
            title       : 'Seleccione el Centro de Costo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion_cuentas/bd/centro_costos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                    cont : cont,
                    impressFunctionScript : 'renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id,'+cont+')'
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
                            handler     : function(){ Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close() }
                        }
                    ]
                }
            ]
        }).show();
    }

    function renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id,cont){
        MyLoading2('on');
        var nombre = document.getElementById('div_centroCostos_FacturaCompraCuentas_nombre_'+id).innerHTML
        var codigo = document.getElementById('div_centroCostos_FacturaCompraCuentas_codigo_'+id).innerHTML;
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        Ext.Ajax.request({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            params  :
            {
                opc                  : 'actualizarCcos',
                idInsertCuenta       : idInsertCuenta,
                idCuenta             : idCuenta,
                opcGrillaContable    : '<?php echo $opcGrillaContable; ?>',
                nombre_centro_costos : nombre,
                codigo_centro_costos : codigo,
                id_centro_costos     : id,
            },
            success :function (result, request){
                        result.responseText= (result.responseText).replace(/[^a-z]/g,'');
                        if(result.responseText == 'true'){

                            // Win_Ventana_buscar_cuenta_nota.close();
                            Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
                            document.getElementById('codigo_centro_costos').innerHTML=codigo;
                            document.getElementById('nombre_centro_costos').innerHTML=nombre;
                            var img = document.getElementById('imgCentroCostos');
                            img.src='../inventario/img/false_inv.png';
                            img.setAttribute('onclick','eliminarCentroCostos('+cont+','+idInsertCuenta+')');

                            MyLoading2('off',{texto:'Se actualizo el centro de costos'});

                            document.getElementById("label_cont_<?php echo $opcGrillaContable; ?>_"+cont+"").innerHTML=cont;

                        }
                        else{
                            // alert("Error\nNo se actualizo la cuenta niif, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");
                            MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                            document.getElementById("label_cont_<?php echo $opcGrillaContable; ?>_"+cont+"").innerHTML="<div style='float:right;'>"+cont+"</div><div style='float:left;'><img src='img/warning.png' title='Requiere Centro de Costos'></div>";
                        }
                    },
            failure : function(){
                        MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                        document.getElementById("label_cont_<?php echo $opcGrillaContable; ?>_"+cont+"").innerHTML="<div style='float:right;'>"+cont+"</div><div style='float:left;'><img src='img/warning.png' title='Requiere Centro de Costos'></div>";
                    }
        });

        // Ext.get('renderSelectCcos').load({
        //     url     : 'remisiones/bd/bd.php',
        //     scripts : true,
        //     nocache : true,
        //     params  :
        //     {
        //         idCcos     : id,
        //         nombre     : nombre,
        //         codigo     : codigo,
        //         opc        : 'updateCcos',
        //         id_factura : '<?php echo $id_factura_venta; ?>',
        //         opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
        //     }
        // });

        // Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
    }

    function eliminarCentroCostos(cont,idInsertCuenta){
        MyLoading2('on');
        Ext.Ajax.request({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            params  :
            {
                opc            : 'eliminarCentroCostos',
                cont           : cont,
                idInsertCuenta : idInsertCuenta,
            },
            success :function (result, request){
                        if(result.responseText.split('{.}')[0] == 'true'){
                            var img = document.getElementById('imgCentroCostos');
                            img.src='img/buscar20.png';
                            img.setAttribute('onclick','ventanaBuscarCentroCostos('+cont+')');
                            document.getElementById('codigo_centro_costos').innerHTML='';
                            document.getElementById('nombre_centro_costos').innerHTML='';

                            MyLoading2('off',{texto:'Se elimino el centro de costos'});
                            document.getElementById("label_cont_<?php echo $opcGrillaContable; ?>_"+cont).innerHTML="<div style='float:right;'>"+cont+"</div><div style='float:left;'><img src='img/warning.png' title='Requiere Centro de Costos'></div>";

                        }
                        else{
                            // alert("Error!\nNo se elimino el centro de costos intentelo de nuevo");
                            MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                            document.getElementById("label_cont_<?php echo $opcGrillaContable; ?>_"+cont).innerHTML=cont;
                        }
                    },
            failure : function(){
                        // alert("Error!\nSin conexion al servidor!");
                        MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                        document.getElementById("label_cont_<?php echo $opcGrillaContable; ?>_"+cont).innerHTML=cont;
                    }
        });
    }

    //============================= FILTRO TECLA GUARDAR CUENTA ==========================================================//
    function guardarAuto<?php echo $opcGrillaContable; ?>(event,input,cont){

        var idInsertCuenta  = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tecla = input? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            guardarNewCuenta<?php echo $opcGrillaContable; ?>(cont);
        }

        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        else if (idInsertCuenta>0) {
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display     = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

        patron = /[^\d.]/g;
        if(patron.test(value)){ input.value = input.value.replace(patron,''); }
        return true;
    }

    function guardarNewCuenta<?php echo $opcGrillaContable; ?>(cont){

        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idPuc          = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cuenta         = document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var debito         = document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).value;
        var credito        = document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).value;
        var opc            = 'guardarCuenta';
        var divRender      = '';
        var accion         = 'agregar';
        var terceroGeneral = document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value;
        var id_tercero     = document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value;

        var id_documento_cruce    = document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   numeroDocumentoCruce  = document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   prefijoDocumentoCruce = document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tipoDocumentoCruce    = document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   idTablaReferencia    = document.getElementById('idTablaReferencia<?php echo $opcGrillaContable; ?>_'+cont).value
        // ,   tipo_nota             = document.getElementById('selectTipoNota').value;

        //VALIDAR QUE LA FILA TENGA UNA CUENTA
        if (idPuc == 0){ alert('El campo cuenta es Obligatorio'); setTimeout(function(){ document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        //VALIDAR QUE TENGA ALMENOS UN VALOR EN EL DEBITO O CREDITO
        else if (debito>0 && credito>0) { alert('La cuenta no puede tener valores debito y credito\nSolo puede tener uno'); setTimeout(function(){ document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        else if (debito==0 && credito==0){ alert('La cuenta debe tener un valor para debito o credito'); setTimeout(function(){ document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }

        // if(tipo_nota == 0
        //     && ( tipoDocumentoCruce != ''
        //         && (numeroDocumentoCruce==0 || isNaN(numeroDocumentoCruce) || id_tercero==0)
        //             || prefijoDocumentoCruce != '' && (numeroDocumentoCruce==0 || isNaN(numeroDocumentoCruce) || tipoDocumentoCruce=='' || id_tercero==0)
        //             || numeroDocumentoCruce > 0 && (tipoDocumentoCruce=='' || id_tercero==0)
        //         )
        //     ){
        //     alert('Aviso,\nCampos obligatorios para cruce con documento:\n* Tercero\n* Tipo Documento Cruce\n* Numero Documento Cruce');
        //     return;
        // }

        //SI LA CUENTA ES LA MISMA QUE LA CUENTA DE PAGO VALIDAR LOS ABONOS
        if(cuenta==arrayCuentaPago[cont]){

            saldo = (debito>0)? debito : credito ;        //VALIDAR QUE EL ABONO NO SEA MAYOR AL SALDO DEL DOCUMENTO CRUCE

            //SI ES UNA FV NO PUEDE TENER VALOR EN DEBITO, DEBE SER CREDITO
            if (tipoDocumentoCruce=='FV') {
                if (debito>0) {
                    alert("Error!\nIngrese  el valor para el asiento en credito");
                    setTimeout(function(){ document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
                    return;
                }
            }
            //SI ES UNA FC NO PUEDE TENER VALOR EN CREDITO, DEBE SER DEBITO
            else if (tipoDocumentoCruce=='FC') {
                // if (credito>0) {
                //     alert("Error!\nIngrese  el valor para el asiento en debito");
                //     setTimeout(function(){ document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
                //     return;
                // }
                if (numeroDocumentoCruce==0) {
                    alert("Aviso!\nSi selecciona un Documento a cruzar debe digitar el numero del documento");
                    setTimeout(function(){ document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
                    return;
                }
            }

             if ((saldo*1)>(arraySaldoCuentaPago[cont]*1) && tipoDocumentoCruce!='FC' && tipoDocumentoCruce!='') {
                alert(" Error!\nEl valor ingresado es mayor al saldo disponible!\nSaldo disponible: "+arraySaldoCuentaPago[cont]);
                return;
            }
        }

        // VALIDAR QUE DIGITE UN NUMERO CUANDO CRUZA UNA FACTURA
        if (tipoDocumentoCruce=='FC') {
            if (numeroDocumentoCruce==0) {
                alert("Aviso!\nSi selecciona un Documento a cruzar debe digitar el numero del documento");
                setTimeout(function(){ document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
                return;
            }
        }

        //VALIDAR QUE NO SE REPITAN DOCUMENTOS CON LA MISMA INFORMACION EN CUENTAS
        // for ( i = 0; i < cont; i++) {

        //     if (cuenta==arrayCuentaPago[i]) {

        //         if (tipoDocumentoCruce != ''
        //             && id_documento_cruce == document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+i).value
        //             && tipoDocumentoCruce == document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+i).value)
        //         {
        //             alert("Error!\nEl documento ya esta en la nota!\nNo se pueden repetir los documentos con las mismas cuentas!");
        //             setTimeout(function(){ document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
        //             return;
        //         }
        //     }
        // }

        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertCuenta > 0){
            opc       = 'actualizaCuenta';
            divRender = 'renderArticulo<?php echo $opcGrillaContable; ?>_'+cont;
            accion    = 'actualizar';
        }
        else{
            //VALIDAMOS PARA NO REPETIR FILAS DE LAN GRILLA
            contArticulos<?php echo $opcGrillaContable; ?>++;
            divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>;
            var div   = document.createElement('div');
            div.setAttribute('id','bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>);
            div.setAttribute('class','bodyDivArticulos<?php echo $opcGrillaContable; ?>');
            document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').appendChild(div);
        }


        Ext.get(divRender).load({
            url     : '<?php echo $carpeta;?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                   : opc,
                opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                consecutivo           : contArticulos<?php echo $opcGrillaContable; ?>,
                cont                  : cont,
                idInsertCuenta        : idInsertCuenta,
                idPuc                 : idPuc,
                debe                  : debito,
                haber                 : credito,
                cuenta                : cuenta,
                id                    : '<?php echo $id_factura_compra; ?>',
                id_tercero            : id_tercero,
                terceroGeneral        : terceroGeneral,
                id_documento_cruce    : id_documento_cruce,
                numeroDocumentoCruce  : numeroDocumentoCruce,
                prefijoDocumentoCruce : prefijoDocumentoCruce,
                tipoDocumentoCruce    : tipoDocumentoCruce,
                idTablaReferencia     : idTablaReferencia,
            }
        });
    }

    //======================= BORRAR UNA CUENTA =============================================================//
    function deleteCuenta<?php echo $opcGrillaContable; ?>(cont){
        var idCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   debito   = document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   credito  = document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).value;

        debito  = (isNaN(debito))? 0: debito;
        credito = (isNaN(credito))? 0: credito;

        if(confirm('Esta Seguro de eliminar esta cuenta de la Nota Contable?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteCuenta',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idCuenta          : idCuenta,
                    cont              : cont,
                    id                : '<?php echo $id_factura_compra; ?>'
                }
            });
            calcTotal<?php echo $opcGrillaContable ?>(debito,credito,'eliminar');
        }
    }

    //====================== CAMBIAR LA CUENTA NIIF POR DEFECTO DE LA COLGAAP ==================================//
    function cambiaCuentaNiif<?php echo $opcGrillaContable; ?>(cont) {
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idCuenta = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_cambiar_cuenta_niif = new Ext.Window({
            width       : 400,
            height      : 320,
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
                    opc               : 'cargaConfiguracionCuenta',
                    idInsertCuenta    : idInsertCuenta,
                    idCuenta          : idCuenta,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cont              : cont,
                    id                : '<?php echo $id_factura_compra; ?>'
                }
            }
        }).show();
    }

    //===================== FUNCION QUE ABRE LA VENTANA PARA BUSCAR LA CUENTA NIIF A CAMBIAR ============================//
    function responseVentanaBuscarCuentaNiif<?php echo $opcGrillaContable; ?>(id,cont) {
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cuenta         = document.getElementById('div_<?php echo $opcGrillaContable; ?>_cuenta_'+id).innerHTML
        var descripcion    = document.getElementById('div_<?php echo $opcGrillaContable; ?>_descripcion_'+id).innerHTML
        Ext.Ajax.request({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            params  :
            {
                opc               : 'actualizarNiif',
                idInsertCuenta    : idInsertCuenta,
                idCuenta          : idCuenta,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                cont              : cont,
                cuenta            : cuenta,
                id_niif           : id,
            },
            success :function (result, request){
                        result.responseText= (result.responseText).replace(/[^a-z]/g,'');
                        if(result.responseText == 'true'){
                            Win_Ventana_buscar_cuenta_nota.close();
                            // Win_Ventana_cambiar_cuenta_niif.close();
                            document.getElementById('cuenta_niif').innerHTML      =cuenta;
                            document.getElementById('descripcion_niif').innerHTML =descripcion;
                        }
                        else{
                            alert("Error\nNo se actualizo la cuenta niif, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");
                        }
                    },
            failure : function(){  }
        });

    }

    //===================== CANCELAR LOS CAMBIOS DE UN CUENTA ===============================================//
    function retrocederCuenta<?php echo $opcGrillaContable; ?>(cont){
        //capturamos el id que esta asignado en la variable oculta
        id_actual = document.getElementById("idInsertCuenta<?php echo $opcGrillaContable; ?>_"+cont).value;

        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'retrocederCuenta',
                cont              : cont,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idCuentaInsert    : id_actual,
                id                : '<?php echo $id_factura_compra; ?>'
            }
        });
    }

    //=============================================== FUNCION GENERAR LA FACTURA =================================================//
    function guardar<?php echo $opcGrillaContable; ?>(){
        var nitProveedor = document.getElementById('nitClienteFacturaCompraCuentas').value;
        var validacion = validarCuentas<?php echo $opcGrillaContable; ?>();
        if (validacion==0) {alert("No hay nada que guardar!"); return;}
        else if (validacion==1) { alert("Hay cuentas pendientes por guardar!"); return; }

        else if (validacion == 2 || validacion == 0) {
            // var tipo_nota   = document.getElementById('selectTipoNota').value
            var   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;

            var arrayReturn=verificaDebitoCredito();
            // var diferencia=parseFloat(arrayReturn[1])-parseFloat(arrayReturn[2]);
            // if (arrayReplaceStringurn[0]<2) {alert("Error!\nDebe ingresar minimo dos cuentas!");return;}
            // if (diferencia!=0) {alert("Error!\nLa nota no esta correctamente balanceada\nTiene una diferencia de: $ "+(parseFloat(arrayReturn[1])-parseFloat(arrayReturn[2]))+"\nPor favor verifiquela y intentelo nuevamente"); return;}
            cargando_documentos('Generando Factura de Compra...','');
            Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'terminarGenerar',
                    id                : '<?php echo $id_factura_compra; ?>',
                    id_tercero        : id_cliente_<?php echo $opcGrillaContable; ?>,
                    prefijo_factura   : document.getElementById('prefijoFacturaCuentas').value,
                    numero_factura    : document.getElementById('numeroFacturaCuentas').value,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    nitProveedor      : nitProveedor
                }
            });
        }
    }

    //===================================== VALIDAR LA NOTA ANTES DE GENERARLA ========================================//
    function validar<?php echo $opcGrillaContable; ?>(opcionGenerar){
        opc = 'validaNota'
        if (opcionGenerar =='terminar') { opc = 'terminarGenerar'; }

        // var id_tipo_nota = document.getElementById('selectTipoNota').value;

        //VALIDACION CUENTAS POR GUARDAR
        var validacion = validarCuentas<?php echo $opcGrillaContable; ?>();
        if (validacion==0) {alert("No hay nada que guardar!"); return;}
        else if (validacion==1) { alert("Hay cuentas pendientes por guardar!"); return; }

        else if (validacion == 2 || validacion == 0) {
            // var tipo_nota   = document.getElementById('selectTipoNota').value
            var   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;

            var arrayReturn=verificaDebitoCredito();
            // var diferencia=parseFloat(arrayReturn[1])-parseFloat(arrayReturn[2]);
            // if (arrayReplaceStringurn[0]<2) {alert("Error!\nDebe ingresar minimo dos cuentas!");return;}
            // if (diferencia!=0) {alert("Error!\nLa nota no esta correctamente balanceada\nTiene una diferencia de: $ "+(parseFloat(arrayReturn[1])-parseFloat(arrayReturn[2]))+"\nPor favor verifiquela y intentelo nuevamente"); return;}
            cargando_documentos('Generando Factura de Compra...','');
            Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : opc,
                    id                : '<?php echo $id_factura_compra; ?>',
                    id_tercero        : id_cliente_<?php echo $opcGrillaContable; ?>,
                    prefijo_factura   : document.getElementById('prefijoFacturaCuentas').value,
                    numero_factura    : document.getElementById('numeroFacturaCuentas').value,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                }
            });
        }
    }

    //=================== FUNCION PARA CALCULAR LA DIFERENCIA ENTRE EL DEBITO Y CREDITO ACUMULADO ====================//
    function verificaDebitoCredito(){
        var cont = 0
        ,   acumDebito = 0
        ,   acumCredito = 0
        ,   contTotal = 0 //numero de filas
        ,   contArticulo
        ,   nameArticulo
        ,   divsArticulos<?php echo $opcGrillaContable; ?> = document.querySelectorAll(".bodyDivArticulos<?php echo $opcGrillaContable; ?>");

        for(i in divsArticulos<?php echo $opcGrillaContable; ?>){

            if(typeof(divsArticulos<?php echo $opcGrillaContable; ?>[i].id)!='undefined'){

                contTotal++;

                nameArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[0]
                contArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[1]

                if(!isNaN(parseFloat(document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+contArticulo).value))){
                    //aumulamos el debito
                    acumDebito +=parseFloat(document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+contArticulo).value);
                }

                if(!isNaN(parseFloat(document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+contArticulo).value))){
                    //aumulamos el debito
                    acumCredito +=parseFloat(document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+contArticulo).value);
                }
            }
        }
        acumDebito  = (parseFloat(acumDebito).toFixed(2))*1;
        acumCredito = (parseFloat(acumCredito).toFixed(2))*1;

        contTotal--;
        var arrayReturn=[contTotal,acumDebito,acumCredito];
        return arrayReturn;
    }

    //=================================================  BUSCAR NOTA ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){

        var validacion = validarCuentas<?php echo $opcGrillaContable; ?>();
        if (validacion==1) {
            if(confirm("Aviso!\nHay cuentas pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
        }
        else if (validacion== 2 || validacion== 0) {ventanaBuscar<?php echo $opcGrillaContable; ?>();}
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

    //================================== VALIDACION NUMERICA EN CANTIDAD Y DESCUENTO ===================================//
    function validarNumberCuenta<?php echo $opcGrillaContable; ?>(event,input,typeValidate,cont){
        var contIdInput = (input.id).split('_')[1];
        var nombreInput = (input.id).split('_')[0];

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;

        //VALIDACION DE LA CANTIDAD DEL ARTICULO
        if (nombreInput=='cantArticulo<?php echo $opcGrillaContable; ?>') {
            if(tecla == 13 || tecla == 9){ ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>'); return; }
        }

        if(tecla == 13){
            document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
            return true;
        }

        patron = /[^\d.]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
        else{
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display    = 'inline';

            if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
            }
        }
    }

    //============================  VALIDAR QUE NO HAYA NINGUN CUENTA POR GUARDAR O POR ACTULIZAR =======================//
    function validarCuentas<?php echo $opcGrillaContable; ?>(){

        var cont = 0
        ,   contTotal = 0
        ,   contArticulo
        ,   nameArticulo
        ,   divsArticulos<?php echo $opcGrillaContable; ?> = document.querySelectorAll(".bodyDivArticulos<?php echo $opcGrillaContable; ?>");

        for(i in divsArticulos<?php echo $opcGrillaContable; ?>){

            if(typeof(divsArticulos<?php echo $opcGrillaContable; ?>[i].id)!='undefined'){

                contTotal++;

                nameArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[0]
                contArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[1]

                if(     document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+contArticulo).value > 0
                    &&  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                    ||  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
                    &&  document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contArticulo).style.display == 'inline')
                    { cont++; }
            }
        }

        if(contTotal==0 || contTotal==1){ return 0; }       // si no se han almacenado articulos retornamos 0
        else if(cont > 0){ return 1; }                      // si hay articulos pendientes por guardar o actualizar retornamos 1
        else { return 2; }                                  // si toda la validacion esta bien retornamos 2
    }

    //============================ CANCELAR UN DOCUMENTO =========================================================================//

    function cancelar<?php echo $opcGrillaContable; ?>(){

        var contArticulos = 0;
        if(!document.getElementById('DivArticulosFacturaCompraCuentas')){ return; }

        arrayIdsArticulos = document.getElementById('DivArticulosFacturaCompraCuentas').querySelectorAll('.campoDescripcion');
        for(i in arrayIdsArticulos){
            if(arrayIdsArticulos[i] > 0){ contArticulos++; }
        }
        // if(contArticulos > 0){
        // }
        // var contArticulos = 0;

        // if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }

        // arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');

        // for(i in arrayIdsArticulos){ if(arrayIdsArticulos[i].innerHTML != '' ){ contArticulos++; } }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
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
            };
        }
    }

    //============================= FUNCION PARA BUSCAR EL DOCUMENTO CRUCE DEL COMPROBANTE ========================================//
    function ventanaBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>(cont,tipoDocumento){

        var myalto  = Ext.getBody().getHeight()
        ,   myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?>',
            title       : 'Seleccionar Documento Cruce ',
            modal       : true,
            autoScroll  : true,
            closable    : false,
            autoDestroy : true,
            items       :
            [
                {
                    xtype       : "panel",
                    id          : 'contenedor_buscar_documento_cruce_<?php echo $opcGrillaContable; ?>',
                    border      : false,
                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                }
            ],
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Sucursal',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 210,
                            height      : 46,
                            bodyStyle   : 'background-color:rgba(255,255,0,0);',
                            autoLoad    :
                            {
                                url     : '../funciones_globales/filtros/filtro_unico_sucursal.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    contenedor           : "contenedor_buscar_documento_cruce_<?php echo $opcGrillaContable; ?>",
                                    opc                  : '<?php echo $opcGrillaContable; ?>',
                                    cont                 : cont,
                                    documento_cruce      : tipoDocumento,
                                    carpeta              : "facturacion_cuentas/",
                                    tablaPrincipal       : "<?php echo $tablaPrincipal; ?>",
                                    idTablaPrincipal     : "id_factura_compra",
                                    tablaCuentasNota     : "<?php echo $tablaCuentasNota; ?>",
                                    url_render           : 'facturacion_cuentas/bd/grillaDocumentoCruce.php',
                                    // imprimeVarPhp        : "tipo_documento_cruce : 'CE',",

                                }
                            }
                        }
                    ]
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?>.close(); }
                }
            ]
        }).show();
    }

    //============================== FUNCION PARA ELIMINAR EL DOCUMENTO CRUCE ====================================================//
    function eliminaDocumentoCruce<?php echo $opcGrillaContable; ?>(cont){
        // console.log(cont);
        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN DOCUMENTO CRUCE
        document.getElementById('imgBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('title','Buscar Documento Cruce');
        document.getElementById('imgBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('onclick',"ventanaBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>("+cont+")");

        var idInsert=document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'block';
            document.getElementById("divImageDeshacer<?php echo $opcGrillaContable; ?>_"+cont).style.display = 'block';
        }

        //LIMPIAR LOS CAMPOS
        document.getElementById("idCuenta<?php echo $opcGrillaContable; ?>_"+cont).value              = "";
        document.getElementById("cuenta<?php echo $opcGrillaContable; ?>_"+cont).value                = "";
        document.getElementById("descripcion<?php echo $opcGrillaContable; ?>_"+cont).value           = "";
        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).value               = "";
        document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value             = "";
        document.getElementById("prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value = "";
        document.getElementById("numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value  = "";
        document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value      = "";
        document.getElementById('idTablaReferencia<?php echo $opcGrillaContable; ?>_'+cont).value     = "";
        document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).value                = "";
        document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).value               = "";

        arrayCuentaPago[cont]      = 0;
        arraySaldoCuentaPago[cont] = 0;

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('title','Buscar Tercero');
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+cont+")");

        // cambiaDocumentoCruce('',cont,'false');
    }

    //============================= FUNCION QUE SE LLAMA CUANDO SE CANBIA EL SELECT DEL DOCUMENTO CRUCE ===================================//
    function cambiaDocumentoCruce(typeDoc,cont,opc) {

        document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).readOnly = true;
        document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).readOnly  = true;
        if (typeDoc=='') {
            document.getElementById('iconBuscarArticulo_'+cont).style.display = 'none';
            document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('style','width:100%;border:none;');
        }else{
            document.getElementById('iconBuscarArticulo_'+cont).style.display                                = 'inline';
            document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('style','width :calc(100% - 20px);border:none;');
        
        }

        if (opc=='') {
            eliminaDocumentoCruce<?php echo $opcGrillaContable; ?>(cont,'true');
        }
        document.getElementById('imgBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('onclick','ventanaBuscarDocumentoCruceFacturaCompraCuentas('+cont+',"'+typeDoc+'")');
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

    //VENTANA MODAL CON LA IMAGEN DE AYUDA PARA CARGAR EL EXCEL
    function imagenAyudaModal() {


        var contenido = '<div style="margin: 0px auto;width:778px;" >'+
                        '<img src="img/saldos_notas.png"  >'+
                        '<br><spam style="color:#FFF;font-weight:bold;font-size:9px;">HAGA CLICK PARA CERRAR</spam>'+
                        '</div>';

        parentModal = document.createElement("div");
        parentModal.innerHTML = '<div id="modal">'+contenido+'</div>';
        parentModal.setAttribute("id", "divPadreModal");
        parentModal.setAttribute("onclick", "cerrarVentanaModal()");
        document.body.appendChild(parentModal);
        document.getElementById("divPadreModal").className = "fondo_modal_saldos";



        // document.getElementById('experiment').style.top="calc(50% - 100px)";
        // document.getElementById('experiment').style.left="calc(50% - 100px)";
    }

    function cerrarVentanaModal(){
        document.getElementById('divPadreModal').parentNode.removeChild(document.getElementById('divPadreModal'));
    }

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

    function guardarObservacionCuenta(cont){
        MyLoading2('on');
        var observacion = document.getElementById('observacionCuenta<?php echo $opcGrillaContable; ?>').value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
        var id = document.getElementById('idInsertCuenta<?php  echo $opcGrillaContable; ?>_'+cont).value;
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc          : 'guardarObservacionCuenta',
                id_documento : '<?php echo $id_factura_compra ?>',
                id           : id,
                observacion  : observacion,
            }
        });
    }

</script>
