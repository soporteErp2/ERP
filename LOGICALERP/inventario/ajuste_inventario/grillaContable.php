<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("config_var_global.php");
    include("../../funciones_globales/funciones_php/randomico.php");
    // include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php");
    include("bd/functions_body_article.php");


    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $id_usuario  = $_SESSION['IDUSUARIO'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');


?>
<script>

    //variables para calcular los valores de los costos y totales de la factura

    var subtotalIngreso<?php echo $opcGrillaContable; ?>    = 0.00
    ,   subtotalSalida<?php echo $opcGrillaContable; ?>     = 0.00
    ,   total<?php echo $opcGrillaContable; ?>              = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
    ,   id_tercero_<?php echo $opcGrillaContable;?>         = 0
    ,   timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   codigoTercero<?php echo $opcGrillaContable; ?>      = 0
    ,   nitTercero<?php echo $opcGrillaContable; ?>         = 0
    ,   nombreTercero<?php echo $opcGrillaContable; ?>      = ''
    ,   nombre_grilla                                       = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>' //nombre de la grilla cunado se busca un articulo
    ,   globalNameFileUpload                                = '';

    //Bloqueo todos los botones
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("Btn_load_excel_body_nota").enable();

    //variable con la fecha del dia mas treinta dias, para cargar por defecto la fecha de vencimiento
    var fechaVencimientoFactura<?php echo $opcGrillaContable;?>  = new Date();
    fechaVencimientoFactura<?php echo $opcGrillaContable;?>.setDate(fechaVencimientoFactura<?php echo $opcGrillaContable;?>.getDate()+parseInt('<?php echo $fechaDefault; ?>'));

</script>
<?php

    $acumScript .= (user_permisos(220,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    $acumScript .= (user_permisos(222,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';       //cancelar

    //============================================ SI NO EXISTE EL PROCESO SE CREA EL ID UNICO ===================================================
    if(!isset($id_documento)){

        // CREACION DEL ID UNICO
        $random_factura = responseUnicoRanomico();
        $nit_empresa = split('-',$_SESSION['NITEMPRESA'])[0];

        // CONSULTAR LA EMPRESA PARA INSERTARLA COMO TERCERO
        $sql="SELECT id,codigo,numero_identificacion,nombre FROM terceros WHERE activo=1 AND id_empresa=$id_empresa AND numero_identificacion='$nit_empresa' ";
        $query=$mysql->query($sql,$mysql->link);

        $id_tercero     = $mysql->result($query,0,'id');
        $codigo         = $mysql->result($query,0,'codigo');
        $nombre_tercero = $mysql->result($query,0,'nombre');

        if ($id_tercero>0) {
            $camposInsert =",id_tercero,cod_tercero,nit,tercero";
            $valuesInsert =",$id_tercero,'$codigo','$nit_empresa','$nombre_tercero'";

            $acumScript .= "document.getElementById('codTercero$opcGrillaContable').value    = '$codigo';
                            document.getElementById('nitTercero$opcGrillaContable').value    = '$nit_empresa';
                            document.getElementById('nombreTercero$opcGrillaContable').value = '$nombre_tercero';

                            id_tercero_$opcGrillaContable   = '$id_tercero';
                            codigoTercero$opcGrillaContable = '$codigo';
                            nitTercero$opcGrillaContable    = '$nit_empresa';
                            nombreTercero$opcGrillaContable = '$nombre_tercero';

                            ";
        }

        $sql="INSERT INTO $tablaPrincipal
                    (id_empresa,random,fecha_documento,id_sucursal,id_bodega,id_usuario$camposInsert)
                VALUES('$id_empresa',
                        '$random_factura',
                        '$fecha',
                        '$id_sucursal',
                        '$filtro_bodega',
                        '$id_usuario'
                        $valuesInsert
                        )";
        $query=$mysql->query($sql,$mysql->link);

        $sql="SELECT id FROM $tablaPrincipal  WHERE random='$random_factura' LIMIT 0,1";
        $query=$mysql->query($sql,$mysql->link);
        $id_documento=$mysql->result($query,0,'id');

        // $sqlSelectId      = "";
        // $id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function() { UpdateFechaDocumento'.$opcGrillaContable.'(); } }
                        });
                        document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";';

        $bodyArticle = cargaArticulosSave($id_documento,$observacion,0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
    }
    //======================================================= SI EXISTE EL DOCUMENTO =================================================================
    else{

        $sql   = "SELECT
                        fecha_documento,
                        id_usuario,
                        documento_usuario,
                        usuario,
                        id_tercero,
                        cod_tercero,
                        nit,
                        tercero,
                        observacion,
                        estado,
                        id_centro_costo,
                        codigo_centro_costo,
                        centro_costo,
                        consecutivo_remision_venta,
                        consecutivo_entrada_almacen
                    FROM $tablaPrincipal
                    WHERE id='$id_documento' AND activo = 1";
        $query=$mysql->query($sql,$mysql->link);

        $fecha_documento             = $mysql->result($query,0,'fecha_documento');
        $id_usuario                  = $mysql->result($query,0,'id_usuario');
        $documento_usuario           = $mysql->result($query,0,'documento_usuario');
        $usuario                     = $mysql->result($query,0,'usuario');
        $id_tercero                  = $mysql->result($query,0,'id_tercero');
        $cod_tercero                 = $mysql->result($query,0,'cod_tercero');
        $nit                         = $mysql->result($query,0,'nit');
        $tercero                     = $mysql->result($query,0,'tercero');
        $observacion                 = $mysql->result($query,0,'observacion');
        $estado                      = $mysql->result($query,0,'estado');
        $id_centro_costo             = $mysql->result($query,0,'id_centro_costo');
        $codigo_centro_costo         = $mysql->result($query,0,'codigo_centro_costo');
        $centro_costo                = $mysql->result($query,0,'centro_costo');
        $consecutivo_remision_venta  = $mysql->result($query,0,'consecutivo_remision_venta');
        $consecutivo_entrada_almacen = $mysql->result($query,0,'consecutivo_entrada_almacen');

        $labelCcos = $codigo_centro_costo.' '.$centro_costo;

        if($estado=='2') { echo "ESTA REMISION DE VENTA ESTA CERRADA "; exit; }

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
                            listeners  : { select: function() { UpdateFechaDocumento'.$opcGrillaContable.'(); } }
                        });';

        $acumScript .=  'document.getElementById("codTercero'.$opcGrillaContable.'").value       = "'.$cod_tercero.'";
                        document.getElementById("nitTercero'.$opcGrillaContable.'").value    = "'.$nit.'";
                        document.getElementById("nombreTercero'.$opcGrillaContable.'").value = "'.$tercero.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value         = "'.$fecha_documento.'";
                        document.getElementById("remision'.$opcGrillaContable.'").value      = "'.$consecutivo_remision_venta.'";
                        document.getElementById("entrada'.$opcGrillaContable.'").value       = "'.$consecutivo_entrada_almacen.'";
                        document.getElementById("usuario'.$opcGrillaContable.'").value       = "'.$usuario.'";
                        observacion'.$opcGrillaContable.'                                    = "'.$observacion.'";

                        id_tercero_'.$opcGrillaContable.'   = "'.$id_tercero.'";
                        codigoTercero'.$opcGrillaContable.' = "'.$cod_tercero.'";
                        nitTercero'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreTercero'.$opcGrillaContable.' = "'.$tercero.'";';

        $bodyArticle = cargaArticulosSave($id_documento,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
    }

?>

<div class="contenedorOrdenCompra" id>

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div class="contTopFila">
                <div id= 'cargaFechaRenglonTop' class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">
                        Fecha
                        <div id="loadFecha" style="float:right; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    </div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>"></div>
                </div>


                <div class="renglonTop">
                    <div class="labelTop">Codigo Tercero</div>
                    <div class="campoTop"><input type="text" id="codTercero<?php echo $opcGrillaContable; ?>" onKeyup="buscarTercero<?php echo $opcGrillaContable; ?>(event,this);" ></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" id="nitTercero<?php echo $opcGrillaContable; ?>" onKeyup="buscarTercero<?php echo $opcGrillaContable; ?>(event,this);" /></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">
                        Tercero
                        <div id="loadTercero" style="float:right; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    </div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreTercero<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaTercero<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Tercero">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>

                <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Centro de Costo</div>
                    <div id="renderSelectCcos" style="float:left; margin-left:-22px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop"><input type="text" id="cCos_<?php echo $opcGrillaContable; ?>" value="<?php echo $labelCcos; ?>" Readonly/></div>
                    <div class="iconBuscarProveedor" onclick="ventanaCcos_<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Centro de Costo">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">
                        Remision de Venta
                    </div>
                    <div class="campoTop"><input type="text" id="remision<?php echo $opcGrillaContable; ?>"  readonly></div>
                </div>

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">
                        Entrada de Almacen
                    </div>
                    <div class="campoTop"><input type="text" id="entrada<?php echo $opcGrillaContable; ?>" readonly></div>
                </div>


                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" readonly="" ></div>
                </div>
                <div class="renglonTop">
                <div id="cargaAjuste"></div>
                  <div class="labelTop">Ajuste mensual</div>
                  <div class="campoTop" style="width:150px">
                    <select id='selectAjusteMensual' onchange="UpdateFechaInventarioMensual()">
                      <option value='NO'>No</option>
                      <option value='SI'>Si</option>
                      <?php echo $optionfechas; ?>
                    </select>
                  </div>
                </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>

    <div id="render_btns_<?php echo $opcGrillaContable; ?>" style="display:none;"></div>
</div>


<script>

    var observacion<?php echo $opcGrillaContable; ?> = '';
    <?php echo $acumScript; ?>

    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();                   //disable btn imprimir
    document.getElementById("codTercero<?php echo $opcGrillaContable; ?>").focus();         //dar el foco

    //=========================== UPDATE FORMAS DE PAGO ============================================//
    function  UpdateFechaInventarioMensual(){
        if(document.getElementById('selectAjusteMensual').value!=='NO'){
            // Obtener la fecha actual
        let today = new Date();
        
        // Establecer la fecha al primer día del mes actual
        let firstDayOfCurrentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        
        // Restar un día para obtener el último día del mes anterior
        let lastDayOfPreviousMonth = new Date(firstDayOfCurrentMonth);
        lastDayOfPreviousMonth.setDate(firstDayOfCurrentMonth.getDate() - 1);
        
        // Formatear la fecha en yyyy-mm-dd
        let year = lastDayOfPreviousMonth.getFullYear();
        let month = String(lastDayOfPreviousMonth.getMonth() + 1).padStart(2, '0');
        let day = String(lastDayOfPreviousMonth.getDate()).padStart(2, '0');

        document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value = `${year}-${month}-${day}`;
        document.getElementById('cargaFechaRenglonTop').style.pointerEvents = 'none';
        UpdateFechaDocumento<?php echo $opcGrillaContable; ?>();
        UpdateTipoAjuste();
        return;
        }
        document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value =  new Date().toISOString().split('T')[0];
        document.getElementById('cargaFechaRenglonTop').style.pointerEvents = '';
        UpdateFechaDocumento<?php echo $opcGrillaContable; ?>();
        UpdateTipoAjuste();
    }
    function UpdateTipoAjuste(){
        var isMensual = document.getElementById('selectAjusteMensual').value;
        Ext.get('cargaAjuste').load({
            url     : 'ajuste_inventario/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'UpdateTipoAjuste',
                isMensual   : isMensual,
                id          : '<?php echo $id_documento; ?>'
            }
        });
    }
    function UpdateFechaDocumento<?php echo $opcGrillaContable; ?>(){
        var fecha = document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value;
        Ext.get('loadFecha').load({
            url     : 'ajuste_inventario/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'UpdateFechaDocumento',
                fecha             : fecha,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    //==================  GUARDAR LA OBSERVACION DEL DOCUMENTO ==================================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
        document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13 || tecla == 9){ guardarObservacion<?php echo $opcGrillaContable; ?>(); }

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
            url     : 'ajuste_inventario/bd/bd.php',
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
                            observacion<?php echo $opcGrillaContable; ?>=observacion;
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
        ,   inputId = Input.id
        ,   numero  = Input.value;

        if(inputId == "nitTercero<?php echo $opcGrillaContable; ?>" && numero==nitTercero<?php echo $opcGrillaContable; ?>){ return true;}
        else if(inputId == "codTercero<?php echo $opcGrillaContable; ?>" && numero==codigoTercero<?php echo $opcGrillaContable; ?>){ return true;}
        else if(Input.value != '' && tecla == 13 ){
            Input.blur();
            ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(Input.value, Input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        return true;
    }

    function ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(codTercero, inputId){
        Ext.get('loadTercero').load({
            url     : 'ajuste_inventario/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarTercero',
                codTercero        : codTercero,
                inputId           : inputId,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    //============================ VENTANA BUSCAR CLIENTE =======================================//
    function buscarVentanaTercero<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

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
                    cargaFuncion  : 'renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id);',
                    nombre_grilla : '<?php echo $opcGrillaContable; ?>'
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
        if (id == id_tercero_<?php echo $opcGrillaContable;?>){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }


        id_tercero_<?php echo $opcGrillaContable;?> = id;
        var nit = document.getElementById('div_<?php echo $opcGrillaContable;?>_numero_identificacion_'+id).innerHTML;

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
        ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(nit,'nitTercero<?php echo $opcGrillaContable; ?>');
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
            document.getElementById("ajuste<?php echo $opcGrillaContable; ?>_"+contIdInput).value       = "";
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';
        }
        else if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value      = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value     = "";
            document.getElementById("ajuste<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        }
        return true;
    }

    function ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(valor,input){
        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
            url     : 'ajuste_inventario/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id_bodega         : '<?php echo $filtro_bodega ?>',
                valor_campo       : valor,
                cont              : arrayIdInput[1],
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    function ajaxCambia<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreTercero<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="contTopFila" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codTercero<?php echo $opcGrillaContable; ?>'){ document.getElementById("codTercero<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitTercero<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitTercero<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Cliente y se deshabilitan
        var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
        for(i in checks){ checks[i].checked=false; checks[i].disabled=true; }

        Ext.get("contenedor_facturacion_compras").load({
            url     : "facturacion_compras.php",
            scripts : true,
            nocache : true,
            params  : { filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value }
        });
    }

    //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
    function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND id_sucursal=<?php echo $id_sucursal; ?> AND id_ubicacion=<?php echo $filtro_bodega; ?> AND inventariable="true" ';
        Win_Ventana_buscar_Articulo = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_Articulo',
            title       : 'Seleccionar articulo ',
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
                    nombre_grilla : nombre_grilla,
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
                    handler     : function(){ Win_Ventana_buscar_Articulo.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,cont){

        var idArticulo       = document.getElementById('ventas_id_item_'+id).innerHTML
        ,   codigo           = document.getElementById('div_'+nombre_grilla+'_codigo_'+id).innerHTML
        ,   costo_inventario = document.getElementById('div_'+nombre_grilla+'_costos_'+id).innerHTML
        ,   unidadMedida     = document.getElementById('unidad_medida_grilla_'+id).innerHTML
        ,   nombreArticulo   = document.getElementById('div_'+nombre_grilla+'_nombre_equipo_'+id).innerHTML
        ,   cantidad         = document.getElementById('div_'+nombre_grilla+'_cantidad_'+id).innerHTML

        document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value        = unidadMedida;
        document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value      = idArticulo;
        document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value     = codigo;
        document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value   = costo_inventario;
        document.getElementById('cantInvArticulo<?php echo $opcGrillaContable; ?>_'+cont).value = cantidad;
        document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value  = nombreArticulo;
        document.getElementById('cantidad<?php echo $opcGrillaContable; ?>_'+cont).focus();

        Win_Ventana_buscar_Articulo.close();
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

        var idInsertArticulo                                 = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   idInventario<?php echo $opcGrillaContable; ?>    = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   cantInvArticulo<?php echo $opcGrillaContable; ?> = document.getElementById('cantInvArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   costoArticulo<?php echo $opcGrillaContable; ?>   = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   cantidad<?php echo $opcGrillaContable; ?>        = document.getElementById('cantidad<?php echo $opcGrillaContable; ?>_'+cont).value


        var opc       = 'guardarArticulo';
        var divRender = '';
        var accion    = 'agregar';

        if (idInventario<?php echo $opcGrillaContable; ?> == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
        else if(cantidad<?php echo $opcGrillaContable; ?> < 0 || cantidad<?php echo $opcGrillaContable; ?> == ''){ document.getElementById('cantidad<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo Cantidad es obligatorio'); setTimeout(function(){document.getElementById('cantidad<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }
        else if(costoArticulo<?php echo $opcGrillaContable; ?> <= 0 || costoArticulo<?php echo $opcGrillaContable; ?> == ''){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo costo es obligatorio'); setTimeout(function(){document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }

        if (idInventario<?php echo $opcGrillaContable; ?> == 0){
            alert('El campo articulo es Obligatorio');
            setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
            return;
        }
        // else if(cantidad<?php echo $opcGrillaContable; ?> <= 0 || cantidad<?php echo $opcGrillaContable; ?> == ''){
        //     document.getElementById('cantidad<?php echo $opcGrillaContable; ?>_'+cont).blur();
        //     setTimeout(function(){ alert('El campo Cantidad es obligatorio!'); },80);
        //     //setTimeout(function(){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20);
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
            url     : 'ajuste_inventario/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : opc,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                consecutivo       : contArticulos<?php echo $opcGrillaContable; ?>,
                cont              : cont,
                idInsertArticulo  : idInsertArticulo,
                idInventario      : idInventario<?php echo $opcGrillaContable; ?>,
                cantInvArticulo   : cantInvArticulo<?php echo $opcGrillaContable; ?>,
                cantidad          : cantidad<?php echo $opcGrillaContable; ?>,
                costoArticulo     : costoArticulo<?php echo $opcGrillaContable; ?>,
                id                : '<?php echo $id_documento; ?>',
            }
        });

        //despues de registrar el primer articulo, habilitamos el boton nuevo
        Ext.getCmp("btnNueva<?php echo $opcGrillaContable; ?>").enable();


        //llamamos la funcion para calcular los totales de la facturan si accion = agregar
        if (accion=="agregar") {
            // calcTotalDoc<?php echo $opcGrillaContable ?>(cantArticulo<?php echo $opcGrillaContable; ?>,descuentoArticulo<?php echo $opcGrillaContable; ?>,costoArticulo<?php echo $opcGrillaContable; ?>,accion,tipoDesc,iva,cont);
        }
    }

    function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){

        var idInsertArticulo<?php echo $opcGrillaContable; ?> = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   idInventario<?php echo $opcGrillaContable; ?>     = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   cantInvArticulo<?php echo $opcGrillaContable; ?>  = document.getElementById('cantInvArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   costoArticulo<?php echo $opcGrillaContable; ?>    = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   cantidad<?php echo $opcGrillaContable; ?>         = document.getElementById('cantidad<?php echo $opcGrillaContable; ?>_'+cont).value

        if(confirm('Esta Seguro de eliminar este articulo de la factura de compra?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'ajuste_inventario/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idInsertArticulo  : idInsertArticulo<?php echo $opcGrillaContable; ?>,
                    cont              : cont,
                    id                : '<?php echo $id_documento; ?>',
                    cantInvArticulo   : cantInvArticulo<?php echo $opcGrillaContable; ?>,
                    costoArticulo     : costoArticulo<?php echo $opcGrillaContable; ?>,
                    cantidad          : cantidad<?php echo $opcGrillaContable; ?>,
                }
            });
            // calcTotalDoc<?php echo $opcGrillaContable ?>(cantArticulo<?php echo $opcGrillaContable; ?>,descuentoArticulo<?php echo $opcGrillaContable; ?>,costoArticulo<?php echo $opcGrillaContable; ?>,'eliminar',tipoDesc,iva,cont);
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
                url     : 'ajuste_inventario/bd/bd.php',
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
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacion<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'ajuste_inventario/bd/bd.php',
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

    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===================//
    function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
        //capturamos el id que esta asignado en la variable oculta
        id_actual=document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;

        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'ajuste_inventario/bd/bd.php',
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

    //====================== FINALIZAR 'CERRAR' 'GENERAR' =====================//
    function guardar<?php echo $opcGrillaContable; ?>(){

        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();

        if (validacion==0) { alert("No hay articulos por guardar en el documento!"); return; }
        else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return; }

        else if (validacion== 2 || validacion== 0) {
            var idBodega    = document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            ,   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;
            observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

            cargando_documentos('Generando Documento...','');
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'ajuste_inventario/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'terminarGenerar',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_documento; ?>',
                    idBodega          : idBodega,
                    observacion       : observacion
                }
            });
        }
    }

    //================================================= BUSCAR ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){

        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();
        if (validacion==1) {
            if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
        }
        else if (validacion== 2 || validacion== 0) { ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
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
                url     : 'ajuste_inventario/bd/buscarGrillaContable.php',
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

        if(tecla == 13){                                                                             costoArticuloAjusteInventario_1
            if(nombreInput == 'cantidad<?php echo $opcGrillaContable; ?>'){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus(); }
            else if(nombreInput == 'costoArticulo<?php echo $opcGrillaContable; ?>'){ guardarNewArticulo<?php echo $opcGrillaContable; ?>(contIdInput) }
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

            if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
            }
        }
    }

    //============================ VALIDAR QUE NO HAYA NINGUN ARTICULO POR GUARDAR O POR ACTULIZAR =======================//
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

        if(contTotal==0 || contTotal==1){ return 0; }      //no hay articulos ni tercero relacionado
        else if(cont > 0){ return 1; }  //articulos pendientes por guardar
        else { return 2; }              //ok
    }

    //============================// CANCELAR UN DOCUMENTO //============================//
    //***********************************************************************************//

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
                Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                    url  : 'ajuste_inventario/bd/bd.php',
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

    function ventanaCcos_<?php echo $opcGrillaContable; ?>(){
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
                url     : 'ajuste_inventario/bd/centro_costos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                    impressFunctionScript : 'renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id)'
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

    function renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id){

        var nombre = ''
        ,   codigo = '';

        if(id > 0){
            nombre = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML
            codigo = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_codigo_'+id).innerHTML;
        }

        Ext.get('renderSelectCcos').load({
            url     : 'ajuste_inventario/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                idCcos            : id,
                nombre            : nombre,
                codigo            : codigo,
                opc               : 'updateCcos',
                id                : '<?php echo $id_documento; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });

        Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
    }

    //====================================// UPLOAD FILE NOTA CONTABLE //====================================//
    //*******************************************************************************************************//
    function createUploader(){
        var id_bodega = document.getElementById('filtro_ubicacion_AjusteInventario').value;

        var uploader = new qq.FileUploader({
            element : document.getElementById('div_upload_file'),
            action  : 'upload_file/upload_file.php',
            debug   : false,
            params  : { AjusteMensual: document.getElementById('selectAjusteMensual').value,
                        fechaAjusteMensual : document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value,
                        opcion: 'loadExcelNota',
                        id_documento :'<?php echo $id_documento ?>',
                        id_bodega : id_bodega
                      },
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

                                    console.log(JsonText);

                                    if(JsonText == '{}'){ alert("Aviso\nLo sentimos ha ocurrido un problema con la carga del archivo, por favor verifique si se logro subir el excel en caso contrario intentelo nuevamente!"); return; }
                                    else{

                                        // document.getElementById('btn_cancel_doc_upload').style.display = 'block';
                                        document.getElementById('divPadreModalUploadFile').setAttribute('style','');
                                        // document.getElementById('titleDocumentoNotaGeneral').innerHTML='';

                                        Ext.get("contenedor_<?php echo $opcGrillaContable; ?>").load({
                                            url     : 'ajuste_inventario/grillaContable.php',
                                            scripts : true,
                                            nocache : true,
                                            params  :
                                            {
                                                id_documento      : '<?php echo $id_documento ?>',
                                                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                                                filtro_bodega     : id_bodega,
                                            }
                                        });

                                        // if(contCuentaNoExiste > 0)alert("Aviso\nSe han agregado "+contCuentaNoExiste+" cuentas que no existen en la contabilidad, por favor asigne las cuentas antes de generar el documento!");
                                    }
                                },
            onCancel : function(fileName){},
            messages :
            {
                typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\n'xls', 'ods'",
                sizeError    : "\"{file}\" Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
                minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
                emptyError   : "{file} is empty, please select files again without it.",
                onLeave      : "Cargando Archivo."
            }
        });
    }
    createUploader();

</script>
