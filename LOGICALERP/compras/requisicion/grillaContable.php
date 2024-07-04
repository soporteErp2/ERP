<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../funciones_globales/funciones_php/randomico.php");
    //include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php"); LOS CALCULOS DE LOS TOTALES ESTAN DESACTIVADOS

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
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

    $styleCamposProveedor = 'display:none';

?>
<script>

    //variables para calcular los valores de los costos y totales de la factura
    var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   descuentoAcumulado<?php echo $opcGrillaContable; ?> = 0.00
    ,   descuento<?php echo $opcGrillaContable; ?>          = 0.00
    ,   acumuladodescuentoArticulo                          = 0.00
    ,   ivaAcumulado<?php echo $opcGrillaContable; ?>       = 0.00
    ,   total<?php echo $opcGrillaContable; ?>              = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
    ,   id_cliente_<?php echo $opcGrillaContable;?>         = 0;

    arrayIva<?php echo $opcGrillaContable; ?>=[]; // ARRAY CON LOS VALORES DE LOS IVAS
    arrayIva<?php echo $opcGrillaContable; ?>[0]={nombre:"",valor:""};

    var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoCliente<?php echo $opcGrillaContable; ?>      = 0
    ,   nitCliente<?php echo $opcGrillaContable; ?>         = 0
    ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
    ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

    //Bloqueo todos los botones
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();

</script>
<?php

    $acumScript .= (user_permisos(171,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    $acumScript .= (user_permisos(173,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';       //cancelar

    //============================ SI NO EXISTE COTIZACION SE CREA EL ID UNICO =======================//
    if(!isset($id_documento)){

        // CREACION DEL ID UNICO
        $random_factura = responseUnicoRanomico();

        $id_solicitante        = $_SESSION['IDUSUARIO'];
        $documento_solicitante = $_SESSION['CEDULAFUNCIONARIO'];
        $nombre_solicitante    = $_SESSION['NOMBREFUNCIONARIO'];

        $sqlInsert   = "INSERT INTO $tablaPrincipal (id_empresa,random,fecha_inicio,fecha_vencimiento,id_sucursal,id_bodega,id_usuario,id_solicitante,documento_solicitante,nombre_solicitante,documento_usuario,usuario)
                        VALUES('$id_empresa','$random_factura','$fecha','$fecha','$id_sucursal','$filtro_bodega','$id_usuario','$id_solicitante','$documento_solicitante','$nombre_solicitante','$documento_solicitante','$nombre_solicitante')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId  = "SELECT id FROM $tablaPrincipal  WHERE random='$random_factura' LIMIT 0,1";
        $id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function(combo, value) { guardaFecha'.$opcGrillaContable.'(this);  } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFinal'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function(combo, value) { guardaFecha'.$opcGrillaContable.'(this);  } }
                        });

                        document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML ="";
                        document.getElementById("nombreSolcitante'.$opcGrillaContable.'").value   = "'.$nombre_solicitante.'";
                        document.getElementById("usuario'.$opcGrillaContable.'").value            = "'.$nombre_solicitante.'";

                        Ext.get("renderizaNewArticulo'.$opcGrillaContable.'").load({
                            url     : "requisicion/bd/bd.php",
                            scripts : true,
                            nocache : true,
                            params  :
                            {
                                opc               : "loadArticulos",
                                //codCliente        : codCliente,
                                //inputId           : inputId,
                                opcGrillaContable : "'.$opcGrillaContable.'",
                            }
                        });';
    }

    //============================== SI EXISTE LA REQUISICION ===============================//
    else{

        include("bd/functions_body_article.php");

        $sql   = "SELECT
                        date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                        date_format(fecha_vencimiento,'%Y-%m-%d') AS fechaFin,
                        observacion,
                        estado,
                        nombre_solicitante,
                        id_area_solicitante,
                        codigo_area_solicitante,
                        area_solicitante,
                        codigo_centro_costo,
                        centro_costo,
                        documento_usuario,
                        usuario,
                        id_tipo
                    FROM $tablaPrincipal
                    WHERE id='$id_documento' AND activo = 1";
        $query = mysql_query($sql,$link);

        $fecha               = mysql_result($query,0,'fecha');
        $fechaFin            = mysql_result($query,0,'fechaFin');
        $estado              = mysql_result($query,0,'estado');
        $consecutivo         = mysql_result($query,0,'consecutivo');
        $consecutivo_carga   = mysql_result($query,0,'consecutivo_carga');
        $nombre_solicitante  = mysql_result($query,0,'nombre_solicitante');
        $codigo_centro_costo = mysql_result($query,0,'codigo_centro_costo');
        $centro_costo        = mysql_result($query,0,'centro_costo');
        $area_solicitante    = mysql_result($query,0,'area_solicitante');
        $documento_usuario   = mysql_result($query,0,'documento_usuario');
        $usuario             = mysql_result($query,0,'usuario');
        $id_tipo             = mysql_result($query,0,'id_tipo');


        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha_inicio.'",
                            listeners  : { select: function(combo, value) { guardaFecha'.$opcGrillaContable.'(this);  } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            //minValue   : "'.$fecha_inicio.'",
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFinal'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha_vencimiento.'",
                            listeners  : { select: function(combo, value) { guardaFecha'.$opcGrillaContable.'(this);  } }
                        });';



        $acumScript .=  '
                        document.getElementById("fecha'.$opcGrillaContable.'").value            = "'.$fecha.'";
                        document.getElementById("fechaFinal'.$opcGrillaContable.'").value       = "'.$fechaFin.'";
                        document.getElementById("nombreSolcitante'.$opcGrillaContable.'").value = "'.$nombre_solicitante.'";
                        document.getElementById("areaSolcitante'.$opcGrillaContable.'").value   = "'.$area_solicitante.'";
                        document.getElementById("usuario'.$opcGrillaContable.'").value          = "'.$usuario.'";
                        document.getElementById("selectTipoRequisicionCompra").value          = "'.$id_tipo.'";


                        observacion'.$opcGrillaContable.'   = "'.$observacion.'";
                        ';

        $bodyArticle = cargaArticulosSave($id_documento,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
    }

    $habilita   = ($estado=='1')? 'onclick="javascript: return false;" disabled ': '';

    $acumScript .= 'exento_iva_'.$opcGrillaContable.' = "'.$exento_iva.'";';

    // TIPO DE REQUISICION
    $sql="SELECT id,nombre FROM compras_requisicion_tipo   WHERE activo =1 AND id_empresa='$id_empresa'";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $requisicion_tipo .= '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
    }

?>

<div class="contenedorRequisicionCompra" id>

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha Inicio</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Fecha Vencimiento</div>
                    <div class="campoTop" >
                        <input type="text" id="fechaFinal<?php echo $opcGrillaContable; ?>" />

                        <div id="renderSelectFormaPago<?php echo $opcGrillaContable; ?>" style="float:left;display:none;"></div>
                        <div id="fechaLimitePago<?php echo $opcGrillaContable; ?>" style="float:left; font-size: 11px; text-align:center;  padding-top: 5px;"></div>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Persona Solicitante</div>
                    <div id="loadPersonaSolicitante" style="float:right; margin-left:-25px; margin-top: -18px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreSolcitante<?php echo $opcGrillaContable; ?>" style="width:100%" readonly/></div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaSolicitante<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Solicitante">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Area o Departamento Solicitante</div>
                    <div id="loadAreaSolicitante" style="float:right; margin-left:-25px; margin-top: -18px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="areaSolcitante<?php echo $opcGrillaContable; ?>" style="width:100%" readonly></div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaAreaSolicitante<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Solicitante">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tipo</div>
                    <div id="renderSelectTipoRequisicion" style="float:right; margin-left:-20px; width:18px; height:18px; overflow:hidden; margin-top: -19px;"></div>
                    <div class="campoTop">
                        <select id="selectTipoRequisicionCompra" onChange="updateTipoRequisicion(this)" style="float:left;"/>
                            <option value="0" >Seleccione...</option>
                            <?php echo $requisicion_tipo; ?>
                        </select>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div style="float:right; margin-left:-25px; margin-top: -18px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" style="width:100%" readonly></div>
                </div>

            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>
<div id="loadForm" style="display:none;"></div>
<script>

    var observacion<?php echo $opcGrillaContable; ?> = '';
    <?php echo $acumScript; ?>

    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();               //disable btn imprimir
    // document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").focus();     //dar el foco

    //======================== GUARDAR LAS FECHAS DE LA ORDEN =============================//
    function guardaFecha<?php echo $opcGrillaContable; ?>(inputDate){
        var idInputDate  = inputDate.getEl().id
        ,   valInputDate = inputDate.value
        ,   fecha_inicio = document.getElementById(idInputDate).value
        ,   fecha_final  = document.getElementById(idInputDate).value;

        Ext.Ajax.request({
            url     : 'requisicion/bd/bd.php',
            params  :
            {
                opc          : 'guardarFechaOrden',
                idInputDate  : idInputDate,
                valInputDate : valInputDate,
                idRequisicion    : '<?php echo $id_documento; ?>'
            },
            success :function (result, request){
                        if(result.responseText == 'true'){
                            if(idInputDate=='fechaRequisicionCompra'){ fecha_inicio=valInputDate; }
                            else if(idInputDate=='fechaFinalRequisicionCompra'){ fecha_final=valInputDate; }
                        }
                        else{
                            if(idInputDate=='fechaRequisicionCompra'){ document.getElementById(idInputDate).value= fecha_inicio; }
                            else if(idInputDate=='fechaFinalRequisicionCompra'){ document.getElementById(idInputDate).value= fecha_final; }
                            alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            // console.log(result.responseText);
                        }
                    },
            failure : function(){
                        if(idInputDate=='fechaRequisicionCompra'){ document.getElementById(idInputDate).value= fecha_inicio; }
                        else if(idInputDate=='fechaFinalRequisicionCompra'){ document.getElementById(idInputDate).value= fecha_final; }
                        alert('Error de conexion con el servidor');
                    }
        });
    }

    function updateTipoRequisicion(selct){
        var id_tipo = selct.options[selct.selectedIndex].value
        ,   nombre  = selct.options[selct.selectedIndex].text

       Ext.get('renderSelectTipoRequisicion').load({
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc          : 'updateTipoRequisicion',
                id_documento : '<?php echo $id_documento; ?>',
                id_tipo      : id_tipo,
                nombre      : nombre
            }
        });
    }


    //==================== CAMBIA TIPO DE DESCUENTO POR ARTICULO ===================================//
    function tipoDescuentoArticulo<?php echo $opcGrillaContable; ?> (cont){
        document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
        // Si existe un articulo almacenado muestra el boton deshacer
        if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'block';
        }
        //si esta en signo porcentaje cambia a pesos, y viceversa
        if (document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png') {
            document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("src","img/pesos.png");
            document.getElementById('tipoDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("title","En Pesos");
            document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();
        }else{
            document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("src","img/porcentaje.png");
            document.getElementById('tipoDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("title","En Porcentaje");
            document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();
        }
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
            url     : 'requisicion/bd/bd.php',
            params  :
            {
                opc            : 'guardarObservacion',
                id             : '<?php echo $id_documento; ?>',
                tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                observacion    : observacion,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            },
            success :function(result, request){
                        if(result.responseText != 'true'){
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                        }
                        else{
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardado</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                            observacion<?php echo $opcGrillaContable; ?> = observacion;
                        }
                    },
            failure : function(){
                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                setTimeout(function () {
                    document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                },1200);
            }
        });
    }

    //============================= FILTRO TECLA BUSCAR PROVEEDOR ==============================//
    function buscarCliente<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla   = Input ? event.keyCode : event.which
        ,   inputId = Input.id
        ,   numero  = Input.value;

        if(inputId == "nitCliente<?php echo $opcGrillaContable; ?>" && numero==nitCliente<?php echo $opcGrillaContable; ?>){ return true;}
        else if(inputId == "codCliente<?php echo $opcGrillaContable; ?>" && numero==codigoCliente<?php echo $opcGrillaContable; ?>){ return true;}
        else if(Input.value != '' && id_cliente_<?php echo $opcGrillaContable;?> == 0 && (tecla == 13 )){
            Input.blur();
            ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(Input.value, Input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        else if(id_cliente_<?php echo $opcGrillaContable;?>>0 && contArticulos<?php echo $opcGrillaContable; ?>>1){
            Input.blur();
            if(confirm('Esta seguro de cambiar de cliente y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){
                ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input);
            }
            else{
                document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value    = nitCliente<?php echo $opcGrillaContable; ?>;
                document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value    = codigoCliente<?php echo $opcGrillaContable; ?>;
                document.getElementById("nombreCliente<?php echo $opcGrillaContable;?>").value  = nombreCliente<?php echo $opcGrillaContable; ?>;
            }
        }
        else if(id_cliente_<?php echo $opcGrillaContable;?>>0){
            ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input);
        }
        return true;
    }

    function ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codCliente, inputId){
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCliente',
                codCliente        : codCliente,
                inputId           : inputId,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    function ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor si es una factura
        if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {
            var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
            for(i in checks){ checks[i].checked=false; checks[i].checked=false; }
        }
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cambiaCliente',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    //============================ VENTANA BUSCAR CLIENTE =======================================//
    function buscarVentanaCliente<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND tipo_cliente = \"Si\"';

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaCliente_<?php echo $opcGrillaContable; ?>',
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
                    sql           : sql,
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

        if(id != id_cliente_<?php echo $opcGrillaContable;?> && contArticulos<?php echo $opcGrillaContable; ?>>1){
            if(!confirm('Esta seguro de cambiar de cliente y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }
        }
        else if (id == id_cliente_<?php echo $opcGrillaContable;?>){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }

        ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(document.getElementById("codCliente<?php echo $opcGrillaContable; ?>"));
        id_cliente_<?php echo $opcGrillaContable;?> = id;
        contArticulos<?php echo $opcGrillaContable; ?>  = 1;

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
        ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(id,'idCliente<?php echo $opcGrillaContable; ?>');
    }

    //=========================== VENTANA BUSCAR VENDEDOR ======================================//
    function buscarVentanaSolicitante<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaVendedor_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>',
            title       : 'Empleados',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaVendedor.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    nombre_grilla : 'vendedor<?php echo $opcGrillaContable; ?>',
                    cargaFuncion  : 'renderizaResultadoVentanaSolicitante<?php echo $opcGrillaContable; ?>(id);',
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
                    handler     : function(){ Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentanaSolicitante<?php echo $opcGrillaContable; ?>(id){
        var documento = document.getElementById('div_vendedor<?php echo $opcGrillaContable; ?>_documento_'+id).innerHTML;
        var nombre = document.getElementById('div_vendedor<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML;
        //mostramos el nombre del vendedor en el campo
        document.getElementById("nombreSolcitante<?php echo $opcGrillaContable; ?>").value = nombre;
        ajaxGuardarSolicitante<?php echo $opcGrillaContable; ?>(id,documento,nombre);

        Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>.close();
    }

    //=========================================== AJAX VENDEDOR =============================================================//
    function ajaxGuardarSolicitante<?php echo $opcGrillaContable; ?>(id,documento,nombre){

        Ext.get('loadPersonaSolicitante').load({
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                   : 'guardarSolicitante',
                id                    : '<?php echo $id_documento; ?>',
                tablaPrincipal        : '<?php echo $tablaPrincipal; ?>',
                id_solicitante        : id,
                documento_solicitante : documento,
                nombre_solicitante    : nombre,
            }
        });

        // Ext.Ajax.request({
        //     url     : 'requisicion/bd/bd.php',
        //     params  :
        //     {
        //         opc                   : 'guardarSolicitante',
        //         id_documento          : '<?php echo $id_documento; ?>',
        //         tablaPrincipal        : '<?php echo $tablaPrincipal; ?>',
        //         id_solicitante        : id,
        //         documento_solicitante : documento,
        //         nombre_solicitante    : nombre,

        //     },
        //     success :function (result, request){
        //             console.log(result);
        //                if(result.responseText=="false"){
        //                 alert("Error!\nNo se almaceno el solicitante\nSi el problema persiste comuniquese con el administrador del sistema");
        //                }
        //             },
        //     failure : function(){ alert('Error de conexion con el servidor'); }
        // });
    }

    //=========================== VENTANA BUSCAR VENDEDOR ======================================//
    function buscarVentanaAreaSolicitante<?php echo $opcGrillaContable; ?>(){

        Win_Ventana_area_solicitante_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 550,
            height      : 400,
            id          : 'Win_Ventana_area_solicitante_<?php echo $opcGrillaContable; ?>',
            title       : 'Departamentos',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/grillaBuscarDepartamentos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    cargaFunction : 'renderizaResultadoVentanaAreaSolicitante<?php echo $opcGrillaContable; ?>(id);',
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
                    handler     : function(){ Win_Ventana_area_solicitante_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentanaAreaSolicitante<?php echo $opcGrillaContable; ?>(id){
        var departamento = document.getElementById('div_costo_departamentos_nombre_'+id).innerHTML;
        var codigo = document.getElementById('div_costo_departamentos_codigo_'+id).innerHTML;
        //mostramos el nombre del vendedor en el campo
        document.getElementById("areaSolcitante<?php echo $opcGrillaContable; ?>").value = departamento;
        ajaxGuardarAreaSolicitante<?php echo $opcGrillaContable; ?>(id,codigo,departamento);

        Win_Ventana_area_solicitante_<?php echo $opcGrillaContable; ?>.close();
    }

    //=========================================== AJAX VENDEDOR =============================================================//
    function ajaxGuardarAreaSolicitante<?php echo $opcGrillaContable; ?>(id,codigo,departamento){

        Ext.get('loadAreaSolicitante').load({
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                           : 'guardarAreaSolicitante',
                id                            : '<?php echo $id_documento; ?>',
                tablaPrincipal                : '<?php echo $tablaPrincipal; ?>',
                id_area_solicitante           : id,
                codigo_area_solicitante       : codigo,
                departamento_area_solicitante : departamento,
            }
        });

        // Ext.Ajax.request({
        //     url     : 'requisicion/bd/bd.php',
        //     params  :
        //     {
        //         opc                   : 'guardarSolicitante',
        //         id_documento          : '<?php echo $id_documento; ?>',
        //         tablaPrincipal        : '<?php echo $tablaPrincipal; ?>',
        //         id_solicitante        : id,
        //         documento_solicitante : documento,
        //         nombre_solicitante    : nombre,

        //     },
        //     success :function (result, request){
        //             console.log(result);
        //                if(result.responseText=="false"){
        //                 alert("Error!\nNo se almaceno el solicitante\nSi el problema persiste comuniquese con el administrador del sistema");
        //                }
        //             },
        //     failure : function(){ alert('Error de conexion con el servidor'); }
        // });
    }



    //================== BUSCAR CENTRO DE COSTOS =======================//
    // function ventanaCcos_<?php echo $opcGrillaContable; ?>(){
    //     Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?> = new Ext.Window({
    //         width       : 550,
    //         height      : 450,
    //         id          : 'Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>',
    //         title       : 'Seleccione el Centro de Costo',
    //         modal       : true,
    //         autoScroll  : false,
    //         closable    : false,
    //         autoDestroy : true,
    //         autoLoad    :
    //         {
    //             url     : '../funciones_globales/grillas/grillaBuscarCentroCostos.php',
    //             scripts : true,
    //             nocache : true,
    //             params  :
    //             {
    //                 opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
    //                 cargaFunction : 'renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id);return;'
    //             }
    //         },
    //         tbar        :
    //         [
    //             {
    //                 xtype   : 'buttongroup',
    //                 columns : 3,
    //                 title   : 'Opciones',
    //                 items   :
    //                 [
    //                     {
    //                         xtype       : 'button',
    //                         width       : 60,
    //                         height      : 56,
    //                         text        : 'Regresar',
    //                         scale       : 'large',
    //                         iconCls     : 'regresar',
    //                         iconAlign   : 'top',
    //                         handler     : function(){ Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close() }
    //                     }
    //                 ]
    //             }
    //         ]
    //     }).show();
    // }

    // //================= RENDERIZAR LA BUSQUEDA DEL CENTRO DE COSTO ================//
    // function renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id){

    //     var nombre = ''
    //     ,   codigo = '';

    //     if(id > 0){
    //         nombre = document.getElementById('div_CentroCostos_nombre_'+id).innerHTML
    //         codigo = document.getElementById('div_CentroCostos_codigo_'+id).innerHTML;
    //     }

    //     Ext.get('renderSelectCcos').load({
    //         url     : 'requisicion/bd/bd.php',
    //         scripts : true,
    //         nocache : true,
    //         params  :
    //         {
    //             idCcos            : id,
    //             nombre            : nombre,
    //             codigo            : codigo,
    //             opc               : 'updateCcos',
    //             id                : '<?php echo $id_documento; ?>',
    //             opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
    //         }
    //     });

    //     Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
    // }


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
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                whereBodega       : 'AND IT.id_sucursal=<?php echo $id_sucursal; ?>  AND IT.id_ubicacion=<?php echo $filtro_bodega ?>',
                campo             : arrayIdInput[0],
                valorArticulo     : valor,
                idArticulo        : arrayIdInput[1],
                exentoIva         : exento_iva_<?php echo $opcGrillaContable; ?>,
                idProveedor       : id_cliente_<?php echo $opcGrillaContable;?>,
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
    function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();
        var sql     = 'AND id_sucursal=<?php echo $id_sucursal; ?> AND id_ubicacion=<?php echo $filtro_bodega; ?> AND estado_compra="true" ';

        Win_Ventana_buscar_Articulo_factura = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_Articulo_factura',
            title       : 'Seleccione un item ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaInventariosVentas.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : sql,
                    nombre_grilla : 'ventanaBucarArticuloRequisicion',
                    nombreTabla   : 'inventario_totales',
                    cargaFuncion  : 'responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,'+cont+');'
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

        var costoTotal     = 0
        ,   totalDescuento = 0
        ,   idArticulo     = document.getElementById('ventas_id_item_'+id).innerHTML
        ,   eanArticulo    = document.getElementById('div_ventanaBucarArticuloRequisicion_codigo_'+id).innerHTML
        ,   codigo         = document.getElementById('div_ventanaBucarArticuloRequisicion_code_bar_'+id).innerHTML
        ,   costo          = document.getElementById('div_ventanaBucarArticuloRequisicion_costos_'+id).innerHTML
        ,   unidadMedida   = document.getElementById('unidad_medida_grilla_'+id).innerHTML
        ,   nombreArticulo = document.getElementById('div_ventanaBucarArticuloRequisicion_nombre_equipo_'+id).innerHTML
        ,   cantidad       = (document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1
        ,   tipoDescuento  = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0]
        ,   descuento      = (document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1;
      //  console.log(eanArticulo);
        //SI EL TERCERO ESTA EXENTO DE IVA
        // if(exento_iva_<?php echo $opcGrillaContable; ?> == 'Si'){
        //     if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
        //         document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        //         document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';
        //     }
        //     else{ document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = "none"; }

        //     document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value       = unidadMedida;
        //     document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value     = idArticulo;
        //     document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = codigo;
        //     document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value  = costo;
        //     document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value = nombreArticulo;
        //     document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = 0;

        //     Win_Ventana_buscar_Articulo_factura.close();
        //     return;
        // }
        // else{


            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'requisicion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscarImpuestoArticulo',
                    id                : '<?php echo $id_documento; ?>',
                    id_inventario     : idArticulo,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cont              : cont,
                    unidadMedida      : unidadMedida,
                    idArticulo        : idArticulo,
                    codigo            : codigo,
                    costo             : costo,
                    nombreArticulo    : nombreArticulo,
                    eanArticulo       : eanArticulo

                }
            });
        // }
    }

    //============================= FILTRO CAMPO CANTIDAD ARTICULO ==========================================================//
    function cantidadArticulo<?php echo $opcGrillaContable; ?>(cantidad){ }

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
        var idInsertArticulo  = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idInventario      = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var descuentoArticulo = document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;;
        var opc               = 'guardarArticulo';
        var divRender         = '';
        var accion            = 'agregar';
        var iva               = document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDesc          = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0];

        if (idInventario == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
        else if(cantArticulo < 1 || cantArticulo == ''){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo Cantidad es obligatorio'); setTimeout(function(){document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }
        //else if(costoArticulo < 1 || costoArticulo == ''){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo costo es obligatorio'); setTimeout(function(){document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }

        if(isNaN(descuentoArticulo)){
            setTimeout(function(){ document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20);
            setTimeout(function(){ alert('El campo descuento debe ser numerico'); },80);
            return;
        }


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
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : opc,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                consecutivo       : contArticulos<?php echo $opcGrillaContable; ?>,
                cont              : cont,
                idInsertArticulo  : idInsertArticulo,
                idInventario      : idInventario,
                cantArticulo      : cantArticulo,
                tipoDesc          : tipoDesc,
                descuentoArticulo : descuentoArticulo,
                costoArticulo     : costoArticulo,
                exento_iva        : exento_iva_<?php echo $opcGrillaContable; ?>,
                iva               : iva,
                id                : '<?php echo $id_documento; ?>',
            }
        });

        //despues de registrar el primer articulo, habilitamos boton nuevo
        // Ext.getCmp("btnNueva<?php echo $opcGrillaContable; ?>").enable();

        //llamamos la funcion para calcular los totales de la facturan si accion = agregar
        //if (accion=="agregar") { calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo,descuentoArticulo,costoArticulo,accion,tipoDesc,iva,cont); }
    }

    function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){
        var idArticulo        = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var descuentoArticulo = document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var iva               = document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDesc          = '';

        if (document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png') { tipoDesc='porcentaje';}
        else{ tipoDesc='pesos'; }

        if(confirm('Esta Seguro de eliminar este articulo de la factura de compra?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'requisicion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : idArticulo,
                    cont              : cont,
                    id                : '<?php echo $id_documento; ?>'
                }
            });
            //calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo,descuentoArticulo,costoArticulo,'eliminar',tipoDesc,iva,cont);
        }
    }

    //======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA ==========================================//
    function ventanaDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,idInsertArticulo,observacionArt){
             var id = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_descripcion_Articulo_factura = new Ext.Window({
            width       : 330,
            height      : 280,
            id          : 'Win_Ventana_descripcion_Articulo_factura',
            title       : 'Observacion articulo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'requisicion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaDescripcionArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : id,
                    cont              : cont,
                    id                : '<?php echo $id_documento; ?>',

                }
            },

            tbar        :
            [
                // {
                //     xtype       : 'button',
                //     text        : 'Guardar',
                //     scale       : 'large',
                //     iconCls     : 'guardar',
                //     iconAlign   : 'left',
                //     handler     : function(){ btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,id); }
                // },
                // {
                //     xtype       : 'button',
                //     text        : 'Guardar',
                //     scale       : 'large',
                //     iconCls     : 'guardar',
                //     iconAlign   : 'left',
                //     handler     : function(){ guardarObservacionArt<?php echo $opcGrillaContable; ?>(cont,id); }
                // },
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
 function ventanaBuscarCentroCostos_<?php echo $opcGrillaContable; ?>(cont) {
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
                url     : 'requisicion/centro_costos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
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

    function renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id,cont,idArticulo){
        MyLoading2('on');
        var nombre         = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML;
        var codigo         = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_codigo_'+id).innerHTML;
        var idInsertArticulo = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        Ext.Ajax.request({
            url     : '<?php echo $carpeta; ?>/bd/bd.php',
            params  :
            {
                opc                  : 'actualizarCcos',
                opcGrillaContable    : '<?php echo $opcGrillaContable; ?>',
                id_centro_costos     : id,
                codigo_centro_costo  : codigo,
                centro_costo         : nombre,
                idInsertArticulo     : idInsertArticulo
            },
            success :function (result, request){
                        var response = (result.responseText).replace(/[^a-z]/g,'');
                        if(response == 'true'){

                            Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
                            document.getElementById('codigoCcos_<?php echo $opcGrillaContable; ?>').innerHTML=codigo;
                            document.getElementById('nombreCcos_<?php echo $opcGrillaContable; ?>').innerHTML=nombre;



                            MyLoading2('off',{texto:'Se actualizo el centro de costos'});
                           // document.getElementById("nombreCcos_<?php echo $opcGrillaContable; ?>_"+cont+"").innerHTML=cont;
                            // MyLoading2('off',{texto:'Se actualizo el centro de costos'});
                            // //document.getElementById("nombreCcos_<?php echo $opcGrillaContable; ?>").innerHTML=nombre;

                        }
                        else if(response == 'padre'){
                            MyLoading2('off',{texto:'No se puede seleccionar un centro de costo padre',icono:'fail',duracion:'3000'});
                             //alert("Error,\nNo se puede seleccionar un centro de costo padre");
                        }
                        else{
                            MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                           // document.getElementById("nombreCcos_<?php echo $opcGrillaContable; ?>").innerHTML=cont;
                        }
                    },
            // failure : function(){
            //             MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
            //             document.getElementById("nombreCcos_<?php echo $opcGrillaContable; ?>").innerHTML="<div style='float:right;'>"+cont+"</div><div style='float:left;'><img src='../compras/img/warning.png' title='Requiere Centro de Costos'></div>";
            //         }
        });
    }


    function btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,idArticulo){

        var id_centro_costos = document.getElementById("id_centro_costos").value;
        var   idImpuesto     = document.getElementById("id_impuestoItem_oc").value;

        var observacionArt = document.getElementById("observaciones<?php echo $opcGrillaContable; ?>_"+cont).value;
        observacion     = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacion<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'requisicion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarDescripcionArticulo',
                idArticulo        : idArticulo,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_documento; ?>',
                observaciones       : observacionArt,
                id_centro_costos  : id_centro_costos,
                id_impuesto       : idImpuesto
            }
        });
    }



    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===============================================//
    function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
         //capturamos el id que esta asignado en la variable oculta
         id_actual=document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;

         Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'requisicion/bd/bd.php',
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
        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();

        if (validacion==0) { alert("No hay articulos por guardar en la presente requisicion de compra!"); return; }
        else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return; }
        else if ( validacion== 2 ) {

            var idBodega    = document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            ,   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;
            observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
            cargando_documentos('Generando Documento...','');
            //Si se va a generar una cotizacion
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'requisicion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'terminarGenerar',
                    opc2              : 'cotizacion',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_documento; ?>',
                    idBodega          : idBodega,
                    observacion       : observacion
                }
            });
        }
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
                url     : 'requisicion/bd/buscarGrillaContable.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
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

    //=================================== VALIDACION DE LA CANTIDAD DE ARTICULOS EXISTENTES ================================//
    function ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,cantidad,opc){
        id=document.getElementById("idArticulo"+opc+"_"+cont).value;

        // Ext.Ajax.request({
        //     url     : 'bd/bd.php',
        //     params  :
        //     {
        //         opc               : 'verificaCantidadArticulo',
        //         id                : id,
        //         filtro_bodega     : '<?php echo $filtro_bodega; ?>',
        //         opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
        //     },
        //     success :function (result, request){
        //                 if(parseInt(cantidad) > parseInt(result.responseText)){
        //                     if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta' || '<?php echo $opcGrillaContable; ?>'=='RemisionesVenta') {
        //                         alert("Error!\nLa cantidad ingresada es mayor a la existente\nSolo restan "+result.responseText);
        //                         document.getElementById("cantArticulo"+opc+"_"+cont).value='';
        //                         setTimeout(function(){document.getElementById("cantArticulo"+opc+"_"+cont).focus();},100);
        //                     }
        //                     else{
        //                         alert("Advertencia!\nLa cantidad ingresada es mayor a la existente\nSolo restan "+result.responseText);
                                document.getElementById("descuentoArticulo"+opc+"_"+cont).focus();
            //                 }
            //             }
            //             else if (result.responseText=='false') {  alert("Error!\nSe produjo un problema con la validacion\nNo se verifico la cantidad del Articulo\nSi el problema persiste comuniquese con el administrador del sistema"); }
            //             else{ document.getElementById("descuentoArticulo"+opc+"_"+cont).focus(); }

            //         },
            // failure : function(){ alert('Error de conexion con el servidor'); }
        // });
    }

    //=================================== IMPRIMIR EN PDF =============================================//

    function imprimir<?php echo $opcGrillaContable; ?> (){
        window.open("bd/imprimirGrillaContable.php?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    //================================== IMPRIMIR EN EXCEL ============================================//
    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
       window.open("bd/exportarExcelGrillaContable.php?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
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

        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.classInputInsertArticulo');
        for(i in arrayIdsArticulos){if(arrayIdsArticulos[i].value > 0 ){ contArticulos++; } }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
                cargando_documentos('Cancelando Documento...','');
                Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                    url     : 'requisicion/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc               : 'cancelarDocumento',
                        id                : '<?php echo $id_documento; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                        idBodega          : '<?php echo $filtro_bodega; ?>'
                    }
                });
            };
        }
    }

    function btnDelete<?php echo $opcGrillaContable; ?>(){

        cancelar<?php echo $opcGrillaContable; ?>();

    }

    // function ventana_centros_costos_<?php echo $opcGrillaContable; ?>(){
    //     Win_Ventana_Ccos_rc = new Ext.Window({
    //         width       : 600,
    //         height      : 450,
    //         id          : 'Win_Ventana_Ccos_rc',
    //         title       : 'Seleccione el Centro de Costo',
    //         modal       : true,
    //         autoScroll  : false,
    //         closable    : false,
    //         autoDestroy : true,
    //         autoLoad    :
    //         {
    //             url     : 'requisicion/centro_costos.php',
    //             scripts : true,
    //             nocache : true,
    //             params  : { }
    //         },
    //         tbar        :
    //         [
    //             {
    //                 xtype   : 'buttongroup',
    //                 columns : 3,
    //                 title   : 'Opciones',
    //                 items   :
    //                 [
    //                     {
    //                         xtype       : 'button',
    //                         width       : 60,
    //                         height      : 56,
    //                         text        : 'Regresar',
    //                         scale       : 'large',
    //                         iconCls     : 'regresar',
    //                         iconAlign   : 'top',
    //                         handler     : function(){ Win_Ventana_Ccos_rc.close() }
    //                     }
    //                 ]
    //             }
    //         ]
    //     }).show();
    // }

    function guardarObservacionArt(cont,idInsertArticulo,observacionArt){
        MyLoading2('on');
        var observacionArt = document.getElementById('observacionArt<?php echo $opcGrillaContable; ?>').value
         , observacionArt = observacionArt.replace(/[\#\<\>\'\"]/g, '');
        //console.log(document.getElementById('observacionArt<?php echo $opcGrillaContable; ?>').value);
        var idInsertArticulo = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
      //  var id = document.getElementById('observacionArtRequisicionCompra').value;
        Ext.get('loadForm').load({
            url     : '<?php echo $carpeta; ?>/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                  : 'guardarObservacionArt',
                opcGrillaContable    : '<?php echo $opcGrillaContable; ?>',
                idInsertArticulo     : idInsertArticulo,
                observacion          : observacionArt
            }
        });
    }

</script>
