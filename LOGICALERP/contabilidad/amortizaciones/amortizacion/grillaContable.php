<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../funciones_globales/funciones_php/randomico.php");

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
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_cargar_diferidos").show();

</script>
<?php

    $acumScript .= (user_permisos(212,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    $acumScript .= (user_permisos(214,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';       //cancelar

    // CONSULTAR LAS SUCURSALES
    $sql="SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $sucursales .= '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
    }

    //============================ SI NO EXISTE COTIZACION SE CREA EL ID UNICO =======================//
    if(!isset($id_documento)){

        // CREACION DEL ID UNICO
        $random_documento = responseUnicoRanomico();

        $sqlInsert   = "INSERT INTO $tablaPrincipal (id_empresa,random,fecha_documento,fecha_diferidos,id_sucursal,id_usuario,documento_usuario,nombre_usuario)
                        VALUES('$id_empresa','$random_documento','$fecha','$fecha','$id_sucursal',$id_usuario,'$documento_usuario','$nombre_usuario')";
        $queryInsert = $mysql->query($sqlInsert,$mysql->link);

        $sqlSelectId   = "SELECT id FROM $tablaPrincipal  WHERE random='$random_documento' LIMIT 0,1";
        $querySelectId = $mysql->query($sqlSelectId ,$mysql->link);
        $id_documento  = $mysql->result($querySelectId,0,'id');
        // $id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 145,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_documento"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 145,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha_diferidos'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_diferidos"); } }
                        });

                        document.getElementById("sucursal'.$opcGrillaContable.'").value      = "'.$id_sucursal.'";
                        document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";';
    }

    //============================== SI EXISTE LA COTIZACION ===============================//
    else{

        include("bd/functions_body_article.php");

        $sql   = "SELECT
                        fecha_documento,
                        fecha_diferidos,
                        consecutivo,
                        documento_usuario,
                        nombre_usuario,
                        observacion,
                        id_sucursal
                    FROM $tablaPrincipal
                    WHERE id='$id_documento' AND activo = 1";
        $query = $mysql->query($sql,$mysql->link);

        $fecha_documento   = $mysql->result($query,0,'fecha_documento');
        $fecha_diferidos   = $mysql->result($query,0,'fecha_diferidos');
        $consecutivo       = $mysql->result($query,0,'consecutivo');
        $documento_usuario = $mysql->result($query,0,'documento_usuario');
        $nombre_usuario    = $mysql->result($query,0,'nombre_usuario');
        $observacion       = $mysql->result($query,0,'observacion');
        $id_sucursal       = $mysql->result($query,0,'id_sucursal');

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha_documento.'",
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_documento"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 145,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha_diferidos'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha_diferidos.'",
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_diferidos"); } }
                        });
                        ';

        $acumScript .=  "
                            document.getElementById('sucursal$opcGrillaContable').value      = '$id_sucursal';
                            document.getElementById('nombreUsuario$opcGrillaContable').value = '$nombre_usuario';
                            document.getElementById('observacion$opcGrillaContable').value   = '$observacion';

                        ";

        $bodyArticle = cargaArticulosSave($id_documento,$id_empresa,$opcGrillaContable,$mysql);
    }


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
                    <div class="campoTop" >
                        <select onchange="actualiza_sucursal<?php echo $opcGrillaContable; ?>(this.value)" id="sucursal<?php echo $opcGrillaContable; ?>" >
                            <optgroup label="Todas las sucursales">
                                <option value="todas">Todas las Sucursales</option>
                            <optgroup>
                            <optgroup label="Sucursales">
                                <?php echo $sucursales ?>
                            <optgroup>
                        </select>
                    </div>
                </div>

                <div class="renglonTop" >
                    <div class="labelTop">Fecha Documento</div>
                    <div id="renderfecha_<?php echo $opcGrillaContable; ?>" style="width: 20px;height: 19px;overflow: hidden;margin-top: -21px;margin-left: 129px;position: absolute;"></div>
                    <div class="campoTop" >
                        <input  type="text" id="fecha<?php echo $opcGrillaContable; ?>" />
                        <div id="renderSelectFormaPago<?php echo $opcGrillaContable; ?>" style="float:left;display:none;"></div>

                    </div>
                </div>
                <div class="renglonTop" >
                    <div class="labelTop">Fecha Diferidos</div>
                    <div id="renderfechaDiferidos_<?php echo $opcGrillaContable; ?>" style="width: 20px;height: 19px;overflow: hidden;margin-top: -21px;margin-left: 129px;position: absolute;"></div>
                    <div class="campoTop" >
                        <input  type="text" id="fecha_diferidos<?php echo $opcGrillaContable; ?>" />
                        <div id="renderSelectFormaPago<?php echo $opcGrillaContable; ?>" style="float:left;display:none;"></div>

                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:271px;"><input type="text" id="nombreUsuario<?php echo $opcGrillaContable; ?>" Readonly value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
                </div>

            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>

    var observacion<?php echo $opcGrillaContable; ?>= '';
    <?php echo $acumScript; ?>
    Ext.getCmp("btnImprimir<?php echo $opcGrillaContable; ?>").disable();  //disable btn imprimir

    // ACTUALIZAR LA SUCURSAL DEL DOCUMENTO
    function actualiza_sucursal<?php echo $opcGrillaContable; ?>(id_sucursal){
        Ext.get('rendersucursal<?php echo $opcGrillaContable; ?>').load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizaSucursal',
                id_sucursal       : id_sucursal,
                id_documento      : '<?php echo $id_documento ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable ?>'
            }
        });
    }

    function UpdateFecha<?php echo $opcGrillaContable; ?>(fecha,campoId){
        if (campoId=='fecha_diferidos' && $(".bodyDivArticulos").length>0){
            if (!confirm("Aviso\nSi modifica esta fecha se eliminaran los diferidos cargado, desea continuar?")) {return;}
        }

        var renderfecha =  (campoId!='fecha_documento')? 'renderfechaDiferidos_<?php echo $opcGrillaContable; ?>' : 'renderfecha_<?php echo $opcGrillaContable; ?>' ;
        Ext.get(renderfecha).load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizaFecha',
                fecha             : fecha,
                campoId           : campoId,
                id_documento      : '<?php echo $id_documento ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable ?>'
            }
        });
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
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            params  :
            {
                opc            : 'guardarObservacion',
                id             : '<?php echo $id_documento; ?>',
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
    //============================= FILTRO TECLA BUSCAR PROVEEDOR ==============================//
    function buscarTercero<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla   = Input ? event.keyCode : event.which
        ,   numero  = Input.value;

        if(Input.value != '' && id_tercero_<?php echo $opcGrillaContable;?> == 0 && (tecla == 13 )){
            Input.blur();
            ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(Input.value);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        else if(id_tercero_<?php echo $opcGrillaContable;?>>0 && contDetalles<?php echo $opcGrillaContable; ?>>1){
            Input.blur();
            // if(confirm('Esta seguro de cambiar de Tercero y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){
            //     ajaxCambiaTercero<?php echo $opcGrillaContable; ?>(Input);
            // }
            // else{
                document.getElementById("nitTercero<?php echo $opcGrillaContable; ?>").value    = nitTercero<?php echo $opcGrillaContable; ?>;
                document.getElementById("codTercero<?php echo $opcGrillaContable; ?>").value    = codigoTercero<?php echo $opcGrillaContable; ?>;
                document.getElementById("nombreTercero<?php echo $opcGrillaContable;?>").value  = nombreTercero<?php echo $opcGrillaContable; ?>;
            // }
        }
        else if(id_tercero_<?php echo $opcGrillaContable;?>>0){
            ajaxCambiaTercero<?php echo $opcGrillaContable; ?>(Input);
        }
        return true;
    }
    function ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(codTercero){
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarTercero',
                codTercero        : codTercero,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }
    function ajaxCambiaTercero<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreTercero<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        // if(Input.id != 'codTercero<?php echo $opcGrillaContable; ?>'){ document.getElementById("codTercero<?php echo $opcGrillaContable; ?>").value = ''; }
        // if(Input.id != 'nitTercero<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitTercero<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor si es una factura
        if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {
            var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
            for(i in checks){ checks[i].checked=false; checks[i].checked=false; }
        }
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cambiaTercero',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }
    //============================ VENTANA BUSCAR Tercero =======================================//
    function buscarVentanaTercero<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        // var sql = 'AND tipo_Tercero = \"Si\"';

        Win_VentanaTercero_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaTercero_<?php echo $opcGrillaContable; ?>',
            title       : 'Proveedores',
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
                    // sql           : sql,
                    cargaFuncion  : 'renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id);',
                    nombre_grilla : 'Tercero<?php echo $opcGrillaContable; ?>'
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
                    handler     : function(){ Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }
    function renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id){
        var numero_documento = document.getElementById('div_TerceroExtractos_numero_identificacion_'+id).innerHTML;
        ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(numero_documento);
        Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close();
        return;

        if(id != id_tercero_<?php echo $opcGrillaContable;?> && contDetalles<?php echo $opcGrillaContable; ?>>1){
            if(!confirm('Esta seguro de cambiar de Tercero y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){ Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close(); return; }
        }
        else if (id == id_tercero_<?php echo $opcGrillaContable;?>){ Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close(); return; }

        ajaxCambiaTercero<?php echo $opcGrillaContable; ?>(document.getElementById("codTercero<?php echo $opcGrillaContable; ?>"));
        id_tercero_<?php echo $opcGrillaContable;?> = id;
        contDetalles<?php echo $opcGrillaContable; ?>  = 1;

        Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close();
        ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(id,'idTercero<?php echo $opcGrillaContable; ?>');
    }
    // EVENTO DEL INPUT DE LA CUENTA PARA BUSCARLA
    function buscarCuenta<?php echo $opcGrillaContable; ?>(event,input) {
        var tecla  = input? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            Ext.get('renderCuenta<?php echo $opcGrillaContable; ?>').load({
                url     : 'amortizaciones/amortizacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscarCuenta',
                    cuenta            : value,
                    id_documento      : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                }
            });
        }
    }
    function changeInput(event,Input,opc,cont) {
        var tecla   = Input ? event.keyCode : event.which
        ,   numero  = Input.value;

        if(tecla == 13 ){
            switch(opc){
                case 'guardar':
                    Input.blur();
                    guardarNewRegistro<?php echo $opcGrillaContable; ?>(cont);
                break
                default :
                    document.getElementById(opc).focus();
            }
        }
    }
    function guardarNewRegistro<?php echo $opcGrillaContable; ?>(cont){

        var idInsertRegistro = document.getElementById('idInsertRegistro<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tipo             = document.getElementById('tipo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   numeroDocumento  = document.getElementById('numeroDocumento<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   fecha            = document.getElementById('fecha<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   valor            = document.getElementById('valor<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   accion           = 'agregar'
        ,   opc              = 'guardarDetalle';

        if (fecha=='' || fecha=='Anio-mes-dia') {
            alert("Aviso\nDigite la fecha de la transaccion");
            document.getElementById('fecha<?php echo $opcGrillaContable; ?>_'+cont).focus();
            return;
        }
        if (valor=='' || valor==0) {
            alert("Aviso\nDigite el valor de la transaccion");
            document.getElementById('valor<?php echo $opcGrillaContable; ?>_'+cont).focus();
            return;
        }

        // if (idInventario == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
        // else if(cantArticulo < 1 || cantArticulo == ''){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo Cantidad es obligatorio'); setTimeout(function(){document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }
        // else if(costoArticulo < 1 || costoArticulo == ''){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo costo es obligatorio'); setTimeout(function(){document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }

        // if(isNaN(descuentoArticulo)){
        //     setTimeout(function(){ document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20);
        //     setTimeout(function(){ alert('El campo descuento debe ser numerico'); },80);
        //     return;
        // }


        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertRegistro > 0){
            opc       = 'actualizaDetalle';
            divRender = 'renderDetalle<?php echo $opcGrillaContable; ?>_'+cont;
            accion    = 'actualizar';
        }
        else{
            //VALIDAMOS PARA NO REPETIR FILAS DE LAN GRILLA
            contDetalles<?php echo $opcGrillaContable; ?>++;
            divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contDetalles<?php echo $opcGrillaContable; ?>;
            var div   = document.createElement('div');
            div.setAttribute('id','bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contDetalles<?php echo $opcGrillaContable; ?>);
            div.setAttribute('class','bodyDivArticulos<?php echo $opcGrillaContable; ?>');
            document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').appendChild(div);
        }

        Ext.get(divRender).load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : opc,
                id                : '<?php echo $id_documento; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                consecutivo       : contDetalles<?php echo $opcGrillaContable; ?>,
                cont              : cont,
                idInsertRegistro  : idInsertRegistro,
                tipo              : tipo,
                numeroDocumento   : numeroDocumento,
                fecha             : fecha,
                valor             : valor,

                // idInsertArticulo  : idInsertArticulo,
                // idInventario      : idInventario,
                // cantArticulo      : cantArticulo,
                // tipoDesc          : tipoDesc,
                // descuentoArticulo : descuentoArticulo,
                // costoArticulo     : costoArticulo,
                // exento_iva        : exento_iva_<?php echo $opcGrillaContable; ?>,
                // iva               : iva,
            }
        });

        //despues de registrar el primer articulo, habilitamos boton nuevo
        Ext.getCmp("btnNueva<?php echo $opcGrillaContable; ?>").enable();

        //llamamos la funcion para calcular los totales de la facturan si accion = agregar
        if (accion=="agregar") {
            calcTotal<?php echo $opcGrillaContable ?>(accion,valor);
        }

    }

    function deleteDetalle<?php echo $opcGrillaContable; ?>(cont){

        var idDetalle       = document.getElementById('idInsertRegistro<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipo            = document.getElementById('tipo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var numeroDocumento = document.getElementById('numeroDocumento<?php echo $opcGrillaContable; ?>_'+cont).value;
        var fecha           = document.getElementById('fecha<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDesc        = '';

        // if (document.getElementById('imgDescuentoDetalle<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png') { tipoDesc='porcentaje';}
        // else{ tipoDesc='pesos'; }

        if(confirm('Esta Seguro de eliminar este articulo de la factura de compra?')){
            Ext.get('renderDetalle<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'amortizaciones/amortizacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteDetalle',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idDetalle         : idDetalle,
                    cont              : cont,
                    id                : '<?php echo $id_documento; ?>'
                }
            });
            // calcTotal<?php echo $opcGrillaContable ?>(cantArticulo,descuentoArticulo,costoArticulo,'eliminar',tipoDesc,iva,cont);
        }
    }

    //======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA ==========================================//
    function ventanaDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont){
        var id = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_descripcion_Articulo_factura = new Ext.Window({
            width       : 330,
            height      : 240,
            id          : 'Win_Ventana_descripcion_Articulo_factura',
            title       : 'Observacion articulo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'amortizaciones/amortizacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaDescripcionArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : id,
                    cont              : cont,
                    id                : '<?php echo $id_documento; ?>'
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
                    handler     : function(){ btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,id); }
                },
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_Ventana_descripcion_Articulo_factura.close(id) }
                }
            ]
        }).show();
    }

    function btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,idArticulo){
        var observacion = document.getElementById("observacionArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;
        observacion     = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacion<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarDescripcionArticulo',
                idArticulo        : idArticulo,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_documento; ?>',
                observacion       : observacion
            }
        });
    }

    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===============================================//
    function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
         //capturamos el id que esta asignado en la variable oculta
         id_actual=document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;

         Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'retrocederArticulo',
                cont              : cont,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idArticulo        : id_actual,
                id                : '<?php echo $id_documento; ?>',
                exento_iva        : exento_iva_<?php echo $opcGrillaContable; ?>,
            }
         });
    }

    //===================================== FINALIZAR 'CERRAR' 'GENERAR' ===================================//
    function guardar<?php echo $opcGrillaContable; ?>(){

        if ($(".bodyDivArticulos").length==0) { alert("No hay diferidos por guardar en esta amortizacion!"); return; }

        var  observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;

        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

        cargando_documentos('Generando Documento...','');
        //Si se va a generar una cotizacion
        Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'terminarGenerar',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id_documento                : '<?php echo $id_documento; ?>',
                observacion       : observacion
            }
        });
    }

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

    //================================== VALIDACION NUMERICA EN CANTIDAD Y DESCUENTO ===================================//
    function validarNumberArticulo<?php echo $opcGrillaContable; ?>(event,input,typeValidate,cont){
        var contIdInput = (input.id).split('_')[1];
        var nombreInput = (input.id).split('_')[0];

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;

        //VALIDACION DE LA CANTIDAD DEL ARTICULO
        if (nombreInput=='cantArticulo<?php echo $opcGrillaContable; ?>') {
            if(tecla == 13 || tecla == 9){ ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>'); return;}
        }

        if(tecla == 13){
            if(nombreInput == 'cantArticulo<?php echo $opcGrillaContable; ?>'){ document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+contnombreInput).focus(); }
            else if(nombreInput == 'descuentoArticulo<?php echo $opcGrillaContable; ?>'){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus(); }
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
                    &&  document.getElementById('imgSaveDetalle<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                    ||  document.getElementById('imgSaveDetalle<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
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

    function cargarDiferidos() {
        var fecha      =  document.getElementById('fecha_diferidos<?php echo $opcGrillaContable; ?>').value
        ,   id_sucursal = document.getElementById('sucursal<?php echo $opcGrillaContable; ?>').value

        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'amortizaciones/amortizacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cargarDiferidos',
                fecha             : fecha,
                id_sucursal       : id_sucursal,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id_documento      : '<?php echo $id_documento ?>',
            }
        });
    }

</script>
