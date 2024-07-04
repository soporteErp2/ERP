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

    //CONSULTAMOS LA FECHA DE VENCIMIENTO QUE ESTE CONFIGURADA, SINO ESTA CONFIGURADA SE ASIGNA DE 30 DIAS
    //FILTRAMOS PARA DIFERENCIAR EL NOMBRE DE LAS TABLAS

    $sqlFecha     = "SELECT dias_vencimiento FROM ventas_cotizaciones_configuracion WHERE activo=1  AND id_empresa=".$_SESSION['EMPRESA'];
    $queryFecha   = mysql_query($sqlFecha,$link);
    $fechaDefault = mysql_result($queryFecha,0,'dias_vencimiento');
    if ($fechaDefault=='') { $fechaDefault='31'; }

?>
<script>

    //variables para calcular los valores de los costos y totales de la factura
    var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   total<?php echo $opcGrillaContable; ?>              = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1

    var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoCliente<?php echo $opcGrillaContable; ?>      = 0
    ,   nitTercero<?php echo $opcGrillaContable; ?>         = 0
    ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
    ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

    //Bloqueo todos los botones
    Ext.getCmp("Btn_guardar_Depreciaciones").disable();
    Ext.getCmp("Btn_editar_Depreciaciones").disable();
    Ext.getCmp("Btn_cancelar_Depreciaciones").disable();
    Ext.getCmp("Btn_restaurar_Depreciaciones").disable();
    Ext.getCmp("BtnGroup_Estado1_Depreciaciones").hide();
    Ext.getCmp("BtnGroup_Guardar_Depreciaciones").show();
    Ext.getCmp("BtnGroup_carga_activos_Depreciaciones").show();

     //variable con la fecha del dia mas treinta dias, para cargar por defecto la fecha de vencimiento
    var fechaVencimientoFactura<?php echo $opcGrillaContable;?>  = new Date();
    fechaVencimientoFactura<?php echo $opcGrillaContable;?>.setDate(fechaVencimientoFactura<?php echo $opcGrillaContable;?>.getDate()+parseInt('<?php echo $fechaDefault; ?>'));

</script>
<?php

    $acumScript .= (user_permisos(108,'false') == 'true')? 'Ext.getCmp("Btn_guardar_Depreciaciones").enable();Ext.getCmp("btnNuevaDepreciaciones").enable();Ext.getCmp("Btn_buscar_Depreciaciones").enable();' : 'Ext.getCmp("btnNuevaDepreciaciones").disable();Ext.getCmp("Btn_buscar_Depreciaciones").disable();document.getElementById("contenidoHijoDoc").style.display="none";';        //guardar
    $acumScript .= (user_permisos(110,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_Depreciaciones").enable();' : '';       //cancelar


    //============================ SI NO EXISTE COTIZACION SE CREA EL ID UNICO =======================//
    if(!isset($id_depreciacion)){
        // if(!isset($sinc_nota)) {$sinc_nota = 'colgaap_niif';}
        // else{$sinc_nota=($sinc_nota=='colgaap')? 'colgaap_niif' : 'niif' ;}
        $sinc_nota='niif';
        $acumScript .= 'var sinc_nota_'.$opcGrillaContable.' = "'.$sinc_nota.'";';

        include("../bd/functions_body_article.php");
        // CREACION DEL ID UNICO
        $random_factura = responseUnicoRanomico();

        $sqlInsert   = "INSERT INTO $tablaPrincipal (id_empresa,random,fecha_inicio,id_sucursal,id_usuario,usuario,sinc_nota)
                        VALUES('$id_empresa','$random_factura','$fecha','$id_sucursal','".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."','".$sinc_nota."')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId      = "SELECT id FROM $tablaPrincipal  WHERE random='$random_factura' LIMIT 0,1";
        $id_depreciacion = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : fechaVencimientoFactura'.$opcGrillaContable.',
                            listeners  : { select: function() { actualizaFechaDocumento(); } }
                        });

                        //OCULTAR EL BUTTON GROUP DE CONTABILIZAR CON NIIF
                        Ext.getCmp("GroupBtnSync").hide();
                        Ext.getCmp("GroupBtnNoSync").hide();

                        actualizaFechaDocumento();
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value       = "'.$_SESSION['NOMBREFUNCIONARIO'].'";
                        document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";';
        // INSERTAR LOS ACTIVOS DE LA SUCURSZAL
        // cargarActivosFijosSucursal($id_depreciacion,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$id_empresa,$id_sucursal,$link);
        // MOSTRAR LOS ACTIVOS CARGADOS AL DOCUMENTO
        $bodyArticle = cargaArticulosSave($id_depreciacion,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
    }

    //============================== SI EXISTE LA COTIZACION ===============================//
    else{

        include("../bd/functions_body_article.php");

        $sql   = "SELECT consecutivo_niif,
                        fecha_inicio,
                        usuario,
                        observacion,
                        estado,
                        sinc_nota,
                        codigo_tercero,
                        numero_identificacion_tercero,
                        tipo_identificacion_tercero,
                        tercero
                    FROM $tablaPrincipal
                    WHERE id='$id_depreciacion' AND activo = 1";
        $query = mysql_query($sql,$link);

        $codigo_tercero                = mysql_result($query,0,'codigo_tercero');
        $numero_identificacion_tercero = mysql_result($query,0,'numero_identificacion_tercero');
        $tipo_identificacion_tercero   = mysql_result($query,0,'tipo_identificacion_tercero');
        $tercero                       = mysql_result($query,0,'tercero');
        $consecutivo                   = mysql_result($query,0,'consecutivo_niif');
        $fecha_inicio                  = mysql_result($query,0,'fecha_inicio');
        $usuario                       = mysql_result($query,0,'usuario');
        $observacion                   = mysql_result($query,0,'observacion');
        $estado                        = mysql_result($query,0,'estado');
        $sinc_nota                     = mysql_result($query,0,'sinc_nota');

        if ( $estado=='2' ) { echo "ESTE DOCUMENTO ESTA CERRADO "; exit; }

        if($sinc_nota == 'colgaap_niif'){
            $acumScript .= 'Ext.getCmp("GroupBtnSync").hide();
                            Ext.getCmp("GroupBtnNoSync").hide();';
        }
        else if($sinc_nota == 'colgaap'){
            $acumScript .= 'Ext.getCmp("GroupBtnSync").hide();
                            Ext.getCmp("GroupBtnNoSync").hide();';
        }

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { actualizaFechaDocumento(); } }
                        });';
        if ($consecutivo>0) {
            $acumScript.='document.getElementById("titleDocumentoDepreciaciones").innerHTML = "Consecutivo N.<br>'.$consecutivo.'";';
        }
        else{
            $acumScript.='document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";';
        }

        $acumScript .=  '
                        document.getElementById("fecha'.$opcGrillaContable.'").value       = "'.$fecha_inicio.'";
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value       = "'.$usuario.'";
                        document.getElementById("observacion'.$opcGrillaContable.'").value = "'.$observacion.'"
                        document.getElementById("codigoTercero'.$opcGrillaContable.'").value  = "'.$codigo_tercero.'";
                        document.getElementById("nitTercero'.$opcGrillaContable.'").value     = "'.$numero_identificacion_tercero.'";
                        document.getElementById("nombreTercero'.$opcGrillaContable.'").value  = "'.$tercero.'";;

                        observacion'.$opcGrillaContable.'   = "'.$observacion.'";';

        $bodyArticle = cargaArticulosSave($id_depreciacion,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
    }

    $acumScript .= 'exento_iva_'.$opcGrillaContable.' = "'.$exento_iva.'";';
    $habilita   = ($estado=='1')? 'onclick="javascript: return false;" disabled ': '';

    //======================================= CONSULTAMOS LOS TIPOS DE DOCUMENTO ==================================================================//
    $sqlTipoDocumento   = "SELECT id,nombre,detalle,tipo FROM tipo_documento WHERE id_empresa='$id_empresa' AND activo=1";
    $queryTipoDocumento = mysql_query($sqlTipoDocumento,$link);
    $tipoDocumento      = '<select id="tipoDocumento'.$opcGrillaContable.'" style="width:65px;" onchange="document.getElementById(\'nitTercero'.$opcGrillaContable.'\').focus();">';

    while ($rowTipoDoc=mysql_fetch_array($queryTipoDocumento)) {
        $selected = ($tipo_identificacion_tercero==$rowTipoDoc['nombre'])? 'selected' : '' ;
        $tipoDocumento.='<option value="'.$rowTipoDoc['nombre'].'" title="'.$rowTipoDoc['detalle'].'" '.$selected.'>'.$rowTipoDoc['nombre'].'</option>';
    }

    $tipoDocumento.='</select>';

?>
<style type="text/css">
    .contenedorGrilla{ margin-top: 0; }
</style>

<div class="contenedorOrdenCompra" id="contenidoHijoDoc">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">

                <div class="renglonTop">
                    <div class="labelTop">Fecha</div><div id="divLoadFecha" style="width:20px;height:20px;margin-top: -20;margin-left: -22;overflow:hidden;float: right;"></div>
                    <div class="campoTop" >
                        <input type="text" id="fecha<?php echo $opcGrillaContable; ?>" />
                    </div>
                </div>

                <div class="renglonTop" id="divCodigoTercero">
                    <div class="labelTop">Codigo Tercero</div>
                    <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $codigo_tercero; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" ></div>
                </div>
                <div class="renglonTop" id="divIdentificacionTercero">
                    <div class="labelTop">N. de Identificacion</div>
                    <div class="campoTop" style="width:230px">
                        <?php echo  $tipoDocumento; ?>
                        <input type="text" style="width:161px"  id="nitTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $nitTercero; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tercero</div>
                    <div class="campoTop" style="width:277px;">
                        <input type="text" id="nombreTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $tercero; ?>" Readonly/>
                    </div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaCliente<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Proveedor">
                       <img src="images/buscar20.png"/>
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
        <div class="toolbar_grilla_manual" style="display:none;">
            <div class="div_input_busqueda_grilla_manual">
                <input type="text" id="inputBuscarGrillaManual" onkeyup="inputBuscarGrillaManual(event,this);">
            </div>
            <div class="div_img_actualizar_datos_grilla_manual">
                <img src="images/reload_grilla.png" onclick="actualizarDatosGrillaManual();">
            </div>
        </div>
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>

    var observacion<?php echo $opcGrillaContable; ?> = '';
    <?php echo $acumScript; ?>

    Ext.getCmp("btnExportarDepreciaciones").disable();               //disable btn imprimir
    // document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").focus();     //dar el foco

    //FUNCION PARA CAMBIAR LA NOTA, SI SE CONTABILIZA O NO CON NIIF
    function cambiaSyncNota(tipo) {
        Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cambiaSyncNota',
                tipo              : tipo,
                id                : '<?php echo $id_depreciacion; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            }
        });
    }

    function actualizaFechaDocumento() {
        // console.log("in");
        fecha=document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value;
        Ext.get('divLoadFecha').load({
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizaFechaDocumento',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_depreciacion ?>',
                fecha             : fecha,

            }
        });
    }

    //============================= FILTRO TECLA BUSCAR TERCERO ==============================//
    function buscarCliente<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla   = Input ? event.keyCode : event.which
        ,   inputId = Input.id
        ,   numero  = Input.value;

        if(inputId == "nitTercero<?php echo $opcGrillaContable; ?>" && numero==nitTercero<?php echo $opcGrillaContable; ?>){ return true; }
        else if(inputId == "codCliente<?php echo $opcGrillaContable; ?>" && numero==codigoCliente<?php echo $opcGrillaContable; ?>){ return true; }
        else if(Input.value != '' && (tecla == 13 )){
            Input.blur();
            ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(Input.value, Input.id, document.getElementById('tipoDocumento<?php echo $opcGrillaContable; ?>').value);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        return true;
    }

    function ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codCliente, inputId, tipoDocumento){
        Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
            url     : 'depreciaciones/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCliente',
                codCliente        : codCliente,
                tipoDocumento     : tipoDocumento,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_depreciacion; ?>',
                inputId           : inputId
            }
        });
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

        var codigo  = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_numero_identificacion_'+id).innerHTML
        ,   typeDoc = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_tipo_identificacion_'+id).innerHTML;

        if (id == id_cliente_<?php echo $opcGrillaContable;?>){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }

        ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codigo, 'nitTercero<?php echo $opcGrillaContable; ?>', typeDoc);
        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
    }

    //==================  GUARDAR LA OBSERVACION DE LA FACTURA ==================================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
        document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
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
            url     : 'depreciaciones_niif/bd/bd.php',
            params  :
            {
                opc            : 'guardarObservacion',
                id             : '<?php echo $id_depreciacion; ?>',
                tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                observacion    : observacion
            },
            success :function (result, request){
                        if(result.responseText != 'true'){
                            // alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
                        else{
                            observacion<?php echo $opcGrillaContable; ?> = observacion;
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardado</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
                    },
            failure : function(){
                            // alert('Error de conexion con el servidor');
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
        });
    }

    //============================== FILTRO TECLA BUSCAR ARTICULO ==========================================================//
    function buscarArticulo<?php echo $opcGrillaContable; ?>(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if (tecla == 13 && numero>0) {
            input.blur();
            ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(input.value, input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }
        if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value               = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value                 = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value            = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value       = "";
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';
        }
        else if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value      = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value     = "";
            document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        }
        return true;
    }

    function ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(valor,input){
        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                whereBodega       : 'AND id_sucursal=<?php echo $id_sucursal; ?>',
                campo             : arrayIdInput[0],
                valorArticulo     : valor,
                idArticulo        : arrayIdInput[1],
                id                : '<?php echo $id_depreciacion; ?>'
            }
        });
    }


    //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
    function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND id_sucursal=<?php echo $id_sucursal; ?> AND estado="1"';
        Win_Ventana_buscar_Articulo_factura = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_Articulo_factura',
            title       : 'Seleccione el activo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'bd/BusquedaInventarios.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql             : sql,
                    nombre_grilla   : 'buscar_activo_fijo',
                    nombreTabla     : 'activos_fijos',
                    cargaFuncion    : 'responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,'+cont+');',
                    id_depreciacion : '<?php echo $id_depreciacion; ?>',
                    contabilidad    : 'niif',
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
                    handler     : function(){ Win_Ventana_buscar_Articulo_factura.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,cont){
        document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();

        var costoTotal             = 0
        ,   totalDescuento         = 0
        ,   idArticulo             = document.getElementById('id_activo_'+id).innerHTML
        ,   codigo                 = document.getElementById('div_buscar_activo_fijo_codigo_activo_'+id).innerHTML
        ,   costo                  = document.getElementById('div_buscar_activo_fijo_costo_'+id).innerHTML
        ,   unidadMedida           = document.getElementById('unidad_medida_activo_'+id).innerHTML
        ,   depreciacion_acumulada = document.getElementById('div_buscar_activo_fijo_depreciacion_acumulada_'+id).innerHTML
        ,   nombreArticulo         = document.getElementById('div_buscar_activo_fijo_nombre_equipo_'+id).innerHTML
        ,   cantidad               = (document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1

        document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value              = unidadMedida;
        document.getElementById('depreciacionAcumulada<?php echo $opcGrillaContable; ?>_'+cont).value = depreciacion_acumulada;
        document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value            = id;
        document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value           = codigo;
        document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value         = costo;
        document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value        = nombreArticulo;
        document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value           = 0;

        Ext.get('renderArticuloDepreciaciones_'+cont).load({
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'calculaValorDepreciacion',
                opcGrillaContable : '<?php echo $opcGrillaContable ?>',
                id_activo         : id,
                accion            : 'mostrar',
                cont              : cont,
            }
        });

        Win_Ventana_buscar_Articulo_factura.close();
        return;

    }

    //============================= FILTRO TECLA GUARDAR ARTICULO ==========================================================//
    function guardarAuto<?php echo $opcGrillaContable; ?>(event,input,cont){
        var idInsertArticulo  = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tecla  = input? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont);
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        else if (idInsertArticulo>0) {
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

        patron = /[^\d.]/g;
        if(patron.test(value)){ input.value = input.value.replace(patron,''); }
        return true;
    }

    function guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont){
        var idInsertArticulo     = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idInventario         = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo         = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var deprecicionAcumulada = document.getElementById('depreciacionAcumulada<?php echo $opcGrillaContable; ?>_'+cont).value;
        var valorDeprecicion     = document.getElementById('costoTotalArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        var costoArticulo     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;;
        var opc               = 'guardarArticulo';
        var divRender         = '';
        var accion            = 'agregar';
        // var tipoDesc          = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0];

        if (idInventario == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
        else if(valorDeprecicion < 1 || valorDeprecicion == ''){ document.getElementById('costoTotalArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo Valor depreciacion es obligatorio'); setTimeout(function(){document.getElementById('costoTotalArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }
        else if(costoArticulo < 1 || costoArticulo == ''){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo costo es obligatorio'); setTimeout(function(){document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }

        // if(isNaN(descuentoArticulo)){
        //     setTimeout(function(){ document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20);
        //     setTimeout(function(){ alert('El campo descuento debe ser numerico'); },80);
        //     return;
        // }

        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertArticulo > 0){
            opc       = 'actualizaArticulo';
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
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                  : opc,
                opcGrillaContable    : '<?php echo $opcGrillaContable; ?>',
                consecutivo          : contArticulos<?php echo $opcGrillaContable; ?>,
                cont                 : cont,
                idInsertArticulo     : idInsertArticulo,
                idInventario         : idInventario,
                cantArticulo         : 1,
                costoArticulo        : costoArticulo,
                deprecicionAcumulada : deprecicionAcumulada,
                valorDeprecicion     : valorDeprecicion,
                id                   : '<?php echo $id_depreciacion; ?>',
            }
        });

        //despues de registrar el primer articulo, habilitamos boton nuevo
        Ext.getCmp("btnNuevaDepreciaciones").enable();

        //llamamos la funcion para calcular los totales de la facturan si accion = agregar
        // if (accion=="agregar") { calculaValorTotalesDocumento(accion,valorDeprecicion); }
    }

    function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){
        var idArticulo        = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;


        if(confirm('Esta Seguro de eliminar este articulo del documento?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'depreciaciones_niif/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : idArticulo,
                    cont              : cont,
                    id                : '<?php echo $id_depreciacion; ?>'
                }
            });
            // calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo,descuentoArticulo,costoArticulo,'eliminar',tipoDesc,iva,cont);
        }
    }

    //======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA ==========================================//
    function ventanaDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont){
        var id = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        ventanaDescripcionArticulo = new Ext.Window({
            height      : 200,
            width       : 400,
            id          : 'ventanaDescripcionArticulo',
            title       : 'Cuentas Activo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'depreciaciones_niif/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaConfiguracionArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : id,
                    cont              : cont,
                    id                : '<?php echo $id_depreciacion; ?>'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Guardar',
                    scale       : 'large',
                    iconCls     : 'guardar',
                    iconAlign   : 'left',
                    handler     : function(){ updateCuentasConcepto(id); }
                },
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ ventanaDescripcionArticulo.close(id); }
                }
            ]
        }).show();
    }

        //================= BUSCAR LA CUENTA PARA EL CONCEPTO =====================//
    function ventanaBuscarCuentasArticulo(opc,campoId,campoText) {
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_cuenta_concepto = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_cuenta_concepto',
            title       : '',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BuscarCuentaPuc.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    nombreGrilla : 'buscar_cuenta',
                    opc : opc,
                    cargaFuncion : 'renderizaCuentaConcepto(id,"'+campoId+'","'+campoText+'")',
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
                            iconAlign   : 'left',
                            handler     : function(){ Win_Ventana_buscar_cuenta_concepto.close() }
                        }
                    ]
                }
            ]
        }).show();
    }

    function cargarActivosFijosSucursal() {
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc          : 'cargarActivosFijosSucursal',
                id_documento : '<?php echo $id_depreciacion ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });
    }

    //==================== ASIGNAR LOS VALORES DE LA NUEVA CUENTA ===============================//
    function renderizaCuentaConcepto(id,campoId,campoText) {
        var cuenta = document.getElementById('div_buscar_cuenta_cuenta_'+id).innerHTML;
        // document.getElementById(campoId).value=id;
        document.getElementById(campoText).innerHTML=cuenta;
        Win_Ventana_buscar_cuenta_concepto.close();

    }

    //================ SINCRONIZAR LA CUENTA NIIF A APARTIR DE LA COLGAAP ==========================//
    function sincronizarCuentaNiif(id_colgaap,campoId,campoText) {
        var id = document.getElementById(id_colgaap).innerHTML;

        Ext.get('id_'+id_colgaap+'_sincLoad').load({
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc       : 'sincronizarCuentaNiif',
                id        : id,
                campoId   : campoId,
                campoText : campoText,
            }
        });
    }

        //=================== ACTUALIZAR LAS CUENTAS DEL CONCEPTO ==================================//
    function updateCuentasConcepto(idArticulo) {
        var cuenta_colgaap               = document.getElementById('cuenta_colgaap').innerHTML;
        var cuenta_niif                  = document.getElementById('cuenta_niif').innerHTML;
        var cuenta_contrapartida_colgaap = document.getElementById('cuenta_contrapartida_colgaap').innerHTML;
        var cuenta_contrapartida_niif    = document.getElementById('cuenta_contrapartida_niif').innerHTML;

        Ext.get('divLoadConfigCuentas').load({
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            text : 'Actualizando...',
            params  :
            {
                opc                          : 'updateCuentasConcepto',
                cuenta_colgaap               : cuenta_colgaap,
                cuenta_niif                  : cuenta_niif,
                cuenta_contrapartida_colgaap : cuenta_contrapartida_colgaap,
                cuenta_contrapartida_niif    : cuenta_contrapartida_niif,
                idArticulo                   : idArticulo,
                id                           : '<?php echo $id_depreciacion; ?>',
            }
        });
    }


    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===============================================//
    function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
         //capturamos el id que esta asignado en la variable oculta
         id_actual=document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;

         Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'depreciaciones_niif/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'retrocederArticulo',
                cont              : cont,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idArticulo        : id_actual,
                id                : '<?php echo $id_depreciacion; ?>',
                exento_iva        : exento_iva_<?php echo $opcGrillaContable; ?>,
            }
         });
    }

    //===================================== VALIDAR LA NOTA ANTES DE GENERARLA ========================================//
    function validar<?php echo $opcGrillaContable; ?>(opcionGenerar){
        opc = 'validaNota'

        //VALIDACION CUENTAS POR GUARDAR
        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();
        if (validacion==0) {alert("No hay activos para generar el documento!"); return;}
        else if (validacion==1) { alert("Hay activos pendientes por guardar!"); return; }
        else if (validacion == 2 || validacion == 0) {
            var observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;

            if (opcionGenerar =='terminar') { opc = 'terminarGenerar'; document.getElementById('LabelCargando').innerHTML='Generado Documento...'; }
            else{ cargando_documentos('Validando Documento...'); }

            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'depreciaciones_niif/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : opc,
                    id                : '<?php echo $id_depreciacion; ?>',
                    id_tercero        : id_cliente_<?php echo $opcGrillaContable; ?>,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                }
            });
        }
    }

    //===================================== FINALIZAR 'CERRAR' 'GENERAR' ===================================//
    // function guardar<?php echo $opcGrillaContable; ?>(){
    //     var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();

    //     if (validacion==0) { alert("No hay articulos por guardar en la presente cotizacion de venta!"); return; }
    //     else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return; }
    //     else if ( validacion== 2 ) {

    //         var id_sucursal    = document.getElementById("filtro_sucursal_Depreciaciones").value
    //         ,   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;

    //         //Si se va a generar una cotizacion
    //         Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
    //             url     : 'depreciaciones_niif/bd/bd.php',
    //             scripts : true,
    //             nocache : true,
    //             params  :
    //             {
    //                 opc               : 'terminarGenerar',
    //                 opc2              : 'cotizacion',
    //                 opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
    //                 id                : '<?php echo $id_depreciacion; ?>',
    //                 sucursal          : id_sucursal,
    //                 observacion       : observacion
    //             }
    //         });
    //     }
    // }

    //================================================= BUSCAR   ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){

        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();
        if (validacion==1) {
            if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
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
                url     : 'depreciaciones_niif/bd/buscarGrillaContable.php',
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

    //================================== VALIDACION NUMERICA EN CANTIDAD Y DESCUENTO ===================================//
    function validarNumberArticulo<?php echo $opcGrillaContable; ?>(event,input,typeValidate,cont){
        var contIdInput = (input.id).split('_')[1];
        var nombreInput = (input.id).split('_')[0];

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;


        if(tecla == 13){
            if (nombreInput=='cantArticuloDepreciaciones') { document.getElementById('costoTotalArticuloDepreciaciones_'+contIdInput).focus(); return;}
            // if(nombreInput == 'cantArticulo<?php echo $opcGrillaContable; ?>'){ document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+contnombreInput).focus(); }
            // else if(nombreInput == 'descuentoArticulo<?php echo $opcGrillaContable; ?>'){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus(); }
            guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont);
            return true;
        }

        patron = /[^\d.]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
        else{
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';

            if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
            }
        }
    }

    //===============  VALIDAR QUE NO HAYA NINGUN ARTICULO POR GUARDAR O POR ACTULIZAR ================//
    function validarArticulos<?php echo $opcGrillaContable; ?>(){

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

                if(     document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).value > 0
                    &&  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                    ||  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
                    &&  document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contArticulo).style.display == 'inline')
                    { cont++; }
            }

        }
        if(contTotal==1 || contTotal==0){  return 0; }      //no se han almacenado articulos
        else if(cont > 0){ return 1; }      //si hay articulos pendientes por guardar o actualizar
        else { return 2; }                  //ok
    }

    //============================ CANCELAR UN DOCUMENTO ===================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){
        var contArticulos = 0;

        if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }

        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');
        for(i in arrayIdsArticulos){if(arrayIdsArticulos[i].innerHTML != '' ){ contArticulos++; } }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
                cargando_documentos('Cancelando Documento...');
                Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                    url     : 'depreciaciones_niif/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc               : 'cancelarDocumento',
                        id                : '<?php echo $id_depreciacion; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                        idBodega          : '<?php echo $filtro_bodega; ?>'
                    }
                });
            };
        }
    }



</script>
