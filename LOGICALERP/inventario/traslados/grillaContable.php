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
    $fecha_documento       = date('Y-m-d');
    $exento_iva  = '';

    //CONSULTAR LAS SUCURSALES DE LA EMPRESA
    $sql = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa ";
    $query = $mysql->query($sql,$mysql->link);
    while($row = $mysql->fetch_array($query)){
        $optionSucursales .= "<option value='$row[id]'>$row[nombre]</option>";
        $acumScript .= "arrayBodegas_".$opcGrillaContable."[$row[id]]=new Array();";
    }

     //CONSULTAR LAS BODEGAS DE LA EMPRESA
      $sql = "SELECT id,id_sucursal,nombre FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$id_empresa ";
      $query = $mysql->query($sql,$mysql->link);
      while($row = $mysql->fetch_array($query)){
        $acumScript .= "arrayBodegas_".$opcGrillaContable."[$row[id_sucursal]][$row[id]]='$row[nombre]'; ";
      }

?>
<script>
    //variables para calcular los valores de los costos y totales de la factura
    var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   arrayBodegas_<?php echo $opcGrillaContable;?>       = new Array()
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
    ,   id_cliente_<?php echo $opcGrillaContable;?>         = 0;
    <?php echo $acumScript; ?>

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
    $acumScript = '';
    $acumScript .= (user_permisos(240,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    $acumScript .= (user_permisos(242,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';       //cancelar

    //============================ SI NO EXISTE COTIZACION SE CREA EL ID UNICO =======================//
    if(!isset($id_documento)){

        // CREACION DEL ID UNICO
        $random_documento = responseUnicoRanomico();

        $id_usuario        = $_SESSION['IDUSUARIO'];
        $documento_usuario = $_SESSION['CEDULAFUNCIONARIO'];
        $usuario           = $_SESSION['NOMBREFUNCIONARIO'];

        $sql   = "INSERT INTO $tablaPrincipal
                            (
                                random,
                                fecha_documento,
                                id_empresa,
                                id_sucursal,
                                id_bodega,
                                id_usuario,
                                documento_usuario,
                                usuario
                            )
                        VALUES
                            (
                                '$random_documento',
                                '$fecha_documento',
                                '$id_empresa',
                                '$filtro_sucursal',
                                '$filtro_bodega',
                                '$id_usuario',
                                '$documento_usuario',
                                '$usuario'
                            )";
        $queryInsert = $mysql->query($sql,$mysql->link);

        $sql="SELECT id FROM $tablaPrincipal  WHERE random='$random_documento' LIMIT 0,1";
        $query=$mysql->query($sql,$mysql->link);
        $id_documento = $mysql->result($query,0,'id');

        $acumScript .= "
                        // new Ext.form.DateField({
                        //     format     : 'Y-m-d',
                        //     width      : 135,
                        //     allowBlank : false,
                        //     showToday  : false,
                        //     applyTo    : 'fecha$opcGrillaContable',
                        //     editable   : false,
                        //     value      : '$fecha',
                        //     listeners  : { select: function(combo, value) { guardaFecha$opcGrillaContable(this); } }
                        // });

                        document.getElementById('titleDocumento$opcGrillaContable').innerHTML = '';
                        document.getElementById('usuario$opcGrillaContable').value            = '$nombre_solicitante';

                        Ext.get('renderizaNewArticulo$opcGrillaContable').load({
                            url     : 'traslados/bd/bd.php',
                            scripts : true,
                            nocache : true,
                            params  :
                            {
                                opc               : 'loadArticulos',
                                opcGrillaContable : '$opcGrillaContable',
                            }
                        });";
    }

    //============================== SI EXISTE LA REQUISICION ===============================//
    else{

        include("bd/functions_body_article.php");

        $sql   = "SELECT
                        fecha_documento,
                        id_sucursal,
                        id_bodega,
                        documento_usuario,
                        usuario,
                        id_sucursal_traslado,
                        sucursal_traslado,
                        id_bodega_traslado,
                        bodega_traslado,
                        id_sucursal,
                        id_bodega,
                        observacion,
                        estado
                    FROM $tablaPrincipal
                    WHERE id='$id_documento' AND activo = 1";
        $query = mysql_query($sql,$link);

        $fecha_documento      = $mysql->result($query,0,'fecha_documento');
        $id_sucursal          = $mysql->result($query,0,'id_sucursal');
        $id_bodega            = $mysql->result($query,0,'id_bodega');
        $documento_usuario    = $mysql->result($query,0,'documento_usuario');
        $usuario              = $mysql->result($query,0,'usuario');
        $id_sucursal_traslado = $mysql->result($query,0,'id_sucursal_traslado');
        $sucursal_traslado    = $mysql->result($query,0,'sucursal_traslado');
        $id_bodega_traslado   = $mysql->result($query,0,'id_bodega_traslado');
        $bodega_traslado      = $mysql->result($query,0,'bodega_traslado');
        $estado               = $mysql->result($query,0,'estado');
        $filtro_sucursal      = $mysql->result($query,0,'id_sucursal');
        $filtro_bodega        = $mysql->result($query,0,'id_bodega');

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", $mysql->result($query,0,'observacion'));

        $acumScript .= "
                        cambiaBodegaDestino$opcGrillaContable($id_sucursal_traslado);
                        // new Ext.form.DateField({
                        //     format     : 'Y-m-d',
                        //     width      : 135,
                        //     allowBlank : false,
                        //     showToday  : false,
                        //     applyTo    : 'fecha$opcGrillaContable',
                        //     editable   : false,
                        //     value      : '$fecha_inicio',
                        //     listeners  : { select: function(combo, value) { guardaFecha$opcGrillaContable(this)  } }
                        // });
                        document.getElementById('sucursal_destino').value = '$id_sucursal_traslado';
                        document.getElementById('bodega_destino').value   = '$id_bodega_traslado';
                        ";



        $acumScript .=  '
                        document.getElementById("fecha'.$opcGrillaContable.'").value            = "'.$fecha.'";
                        document.getElementById("usuario'.$opcGrillaContable.'").value          = "'.$usuario.'";

                        observacion'.$opcGrillaContable.'   = "'.$observacion.'";
                        ';

        $bodyArticle = cargaArticulosSave($id_documento,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$mysql);
    }

    $habilita   = ($estado=='1')? 'onclick="javascript: return false;" disabled ': '';

?>

<div class="contenedorTraslados" id>
    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 19px;overflow: hidden;margin-top: -3px;"></div>
                    <div class="labelTop">Fecha Documento</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha_documento; ?>" readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div style="float:right; margin-left:-25px; margin-top: -18px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" value="<?php echo $usuario; ?>" style="width:100%" readonly></div>
                </div>

                <div class="renglonTop" id="divIdentificacionTercero">
                  <div class="labelTop">Sucursal Destino</div>
                  <div class="campoTop" style="width:230px">
                    <select id='sucursal_destino' data-destino='sucursal' onchange="cambiaBodegaDestino<?php echo $opcGrillaContable; ?>(this.value);actualizaDestinoTraslado(this)">
                      <option value=''>Seleccione...</option>
                      <?php echo $optionSucursales; ?>
                    </select>
                  </div>
                </div>
                <div class="renglonTop" id="divIdentificacionTercero">
                  <div class="labelTop">Bodega Destino</div>
                  <div class="campoTop" style="width:230px">
                    <select id='bodega_destino' data-destino='bodega' onchange='actualizaDestinoTraslado(this)'>
                      <option value=''>Seleccione...</option>
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
<div id="loadForm" style="display:none;"></div>
<script>

    var observacion<?php echo $opcGrillaContable; ?> = '';
    <?php echo $acumScript; ?>

    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();               //disable btn imprimir
    // document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").focus();     //dar el foco

    function cambiaBodegaDestino<?php echo $opcGrillaContable; ?>(id_sucursal){
        if(id_sucursal == ''){
            document.getElementById('bodega_destino').innerHTML='<option value="">Seleccione...</option>';
            return;
        }
        var optionBodegas = '';
        arrayBodegas_<?php echo $opcGrillaContable; ?>[id_sucursal].forEach(function(elemento,index) { optionBodegas += `<option value='${index}'>${elemento}</option>`;  ;});
        document.getElementById('bodega_destino').innerHTML=`<option value='' >Seleccione...</option>${optionBodegas}`;
    }

    //======================== GUARDAR LAS FECHAS DE LA ORDEN =============================//
    function guardaFecha<?php echo $opcGrillaContable; ?>(inputDate){
        var fecha_documento = inputDate.value

        Ext.get('cargaFecha<?php echo $opcGrillaContable; ?>').load({
            url     : 'traslados/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarFecha',
                id_documento      : '<?php echo $id_documento; ?>',
                fecha             : fecha_documento,
                opcGrillaContable : '<?php echo $opcGrillaContable ?>'
            }
        });
    }

    /**
     * actualizaDestinoTraslado Actualizar la sucursal o bodega de destino del traslado
     * @param  Object input Elemento del DOM
     */
    function actualizaDestinoTraslado(input) {
        var idInput        = input.id
        ,   id_destino     = input.value
        ,   nombre_destino = input.options[input.selectedIndex].text
        ,   destino        = input.dataset.destino
        if (id_destino=='') {return;}

        Ext.get('loadForm').load({
            url     : 'traslados/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizaDestinoTraslado',
                id_documento      : '<?php echo $id_documento; ?>',
                id_destino        : id_destino,
                nombre_destino    : nombre_destino,
                destino           : destino,
                opcGrillaContable : '<?php echo $opcGrillaContable ?>'
            }
        });

        // console.log(`id: ${idInput} value ${value} selectedIndex ${input.selectedIndex} option ${input.options[input.selectedIndex].text} destino ${destino}`);
        // console.log(input);
    }


    //==================  GUARDAR LA OBSERVACION DEL DOCUMENTO ==================================//
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
            url     : 'traslados/bd/bd.php',
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
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';
        }
        else if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value     = "";
        }
        return true;
    }

    function ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(valor,input){
        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
            url     : 'traslados/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                whereBodega       : 'AND IT.id_sucursal=<?php echo $filtro_sucursal; ?>  AND IT.id_ubicacion=<?php echo $filtro_bodega ?>',
                campo             : arrayIdInput[0],
                valorArticulo     : valor,
                idArticulo        : arrayIdInput[1],
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
    function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();
        var sql     = 'AND id_sucursal=<?php echo $id_sucursal; ?> AND id_ubicacion=<?php echo $filtro_bodega; ?> AND inventariable="true" AND cantidad>0';

        Win_Ventana_buscar_Articulo_traslado = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_Articulo_traslado',
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
                    nombre_grilla : 'ventanaBucarArticulotraslados',
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
                    handler     : function(){ Win_Ventana_buscar_Articulo_traslado.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,cont){
        document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();

        var costoTotal     = 0
        ,   totalDescuento = 0
        ,   idArticulo     = document.getElementById('ventas_id_item_'+id).innerHTML
        ,   eanArticulo    = document.getElementById('div_ventanaBucarArticulotraslados_codigo_'+id).innerHTML
        ,   codigo         = document.getElementById('div_ventanaBucarArticulotraslados_code_bar_'+id).innerHTML
        ,   unidadMedida   = document.getElementById('unidad_medida_grilla_'+id).innerHTML
        ,   nombreArticulo = document.getElementById('div_ventanaBucarArticulotraslados_nombre_equipo_'+id).innerHTML
        ,   cantidad       = (document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1

        document.getElementById(`unidades<?php echo $opcGrillaContable; ?>_${cont}`).value       = unidadMedida;
        document.getElementById(`idArticulo<?php echo $opcGrillaContable; ?>_${cont}`).value     = idArticulo;
        document.getElementById(`eanArticulo<?php echo $opcGrillaContable; ?>_${cont}`).value    = eanArticulo;
        document.getElementById(`nombreArticulo<?php echo $opcGrillaContable; ?>_${cont}`).value = nombreArticulo;

        Win_Ventana_buscar_Articulo_traslado.close();
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
        var idInsertArticulo  = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   idInventario      = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   cantArticulo      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   opc               = 'guardarArticulo'
        ,   divRender         = ''
        ,   accion            = 'agregar'

        if (idInventario == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
        else if(cantArticulo < 1 || cantArticulo == ''){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo Cantidad es obligatorio'); setTimeout(function(){document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }

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
            url     : 'traslados/bd/bd.php',
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
                id_documento      : '<?php echo $id_documento; ?>',
                id_sucursal       : '<?php echo $filtro_sucursal ?>',
                id_ubicacion      : '<?php echo $filtro_bodega ?>',
            }
        });

    }

    function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){
        var idArticulo        = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        if(confirm('Esta Seguro de eliminar este articulo de la factura de compra?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'traslados/bd/bd.php',
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
                    url     : 'traslados/bd/bd.php',
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

    function btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,idArticulo){

        var id_centro_costos = document.getElementById("id_centro_costos").value;
        var   idImpuesto     = document.getElementById("id_impuestoItem_oc").value;

        var observacionArt = document.getElementById("observaciones<?php echo $opcGrillaContable; ?>_"+cont).value;
        observacion     = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacion<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'traslados/bd/bd.php',
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
            url     : 'traslados/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'retrocederArticulo',
                cont              : cont,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idArticulo        : id_actual,
                id                : '<?php echo $id_documento; ?>',
            }
         });
    }

    //===================================== FINALIZAR 'CERRAR' 'GENERAR' ===================================//
    function guardar<?php echo $opcGrillaContable; ?>(){
        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();

        if (validacion==0) { alert("No hay articulos por guardar en la presente requisicion de compra!"); return; }
        else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return; }
        else if ( validacion== 2 ) {

            cargando_documentos('Generando Documento...','');
            //Si se va a generar una cotizacion
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'traslados/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'terminarGenerar',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_documento; ?>',
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
                url     : 'traslados/bd/buscarGrillaContable.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_sucursal       : document.getElementById("filtro_sucursal_<?php echo $opcGrillaContable; ?>").value,
                    id_bodega         : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value,
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
                    url     : 'traslados/bd/bd.php',
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
