<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../funciones_globales/funciones_php/randomico.php");
    include("../../../funciones_globales/funciones_javascript/totalesNotaContable.php");

    $id_empresa   = $_SESSION['EMPRESA'];
    $id_sucursal  = $_SESSION['SUCURSAL'];
    $id_usuario   = $_SESSION['IDUSUARIO'];
    $usuario      = $_SESSION['NOMBREFUNCIONARIO'];

    $estado       = '';
    $bodyArticle  = '';
    $acumScript   = '';
    $fecha_nota   = date('Y');
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
    Ext.getCmp("Btn_exportar_NotaCierre").disable();
    // Ext.getCmp("Btn_articulos_Relacionados").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();

</script>
<?php
    $acumScript .= (user_permisos(184,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    $acumScript .= (user_permisos(186,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';

    // $acumScript .= 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' ;       //guardar
    // $acumScript .= 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';

    //========================= CONSULTAMOS LOS TIPOS DE LAS NOTAS ========================//
    $sqlTiposNotas   = "SELECT id,descripcion,documento_cruce FROM tipo_nota_contable WHERE activo=1 AND id_empresa='$id_empresa'";
    $queryTiposNotas = mysql_query($sqlTiposNotas,$link);
    // $tiposNotas      = '<select id="selectTipoNota" onchange="actualizarTipoNotaContable(this);">';

    while ($rowTiposNotas = mysql_fetch_array($queryTiposNotas)) {
        if($id_tipo_nota == 0){
            $id_tipo_nota    = $rowTiposNotas['id'];
            $cruce_tipo_nota = $rowTiposNotas['documento_cruce'];
        }
        $tiposNotas .= '<option value="'.$rowTiposNotas['id'].'">'.$rowTiposNotas['descripcion'].'</option>';
        // $acumScript .= 'arrayTipoNota['.$rowTiposNotas['id'].']= "'.$rowTiposNotas['documento_cruce'].'";';
    }
    $tiposNotas     .= '</select>';

    //============================================ SI NO EXISTE EL PROCESO SE CREA EL ID UNICO ===================================================
    if(!isset($id_nota)){

        if(!isset($sinc_nota)) $sinc_nota = 'colgaap_niif';
        // $acumScript .= 'var sinc_nota_'.$opcGrillaContable.' = "'.$sinc_nota.'";';

        $fecha_nota = date("Y", strtotime(date("Y-m-d") ) ).'-12-31';
        // CREACION DEL ID UNICO
        $random_nota = responseUnicoRanomico();

        $sqlInsert   ="INSERT INTO $tablaPrincipal (id_empresa,random,id_sucursal,id_usuario,cedula_usuario,usuario,id_tipo_nota,sinc_nota,fecha_nota)
                        VALUES('$id_empresa','$random_nota','$id_sucursal','".$_SESSION['IDUSUARIO']."','".$_SESSION['CEDULAFUNCIONARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."','$id_tipo_nota','$sinc_nota','$fecha_nota')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId = "SELECT id FROM $tablaPrincipal  WHERE random='$random_nota' LIMIT 0,1";
        $id_nota     = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y",
                            width      : 60,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : new Date(),
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value); } }
                        });

                        //OCULTAR EL BUTTON GROUP DE CONTABILIZAR CON NIIF
                        // Ext.getCmp("GroupBtnSync").hide();
                        // Ext.getCmp("GroupBtnNoSync").show();

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

    //=================================// SI EXISTE EL PROCESO //=================================//
    //********************************************************************************************//
    else{

        include("../bd/functions_body_article.php");

        $sql = "SELECT IF (
                            consecutivo > 0,
                            fecha_nota,
                            DATE_FORMAT(now(),'%Y-%m-%d')
                        ) AS fecha_nota,
                    id_tercero,
                    codigo_tercero,
                    tercero,
                    tipo_nota,
                    id_tipo_nota,
                    usuario,
                    observacion,
                    consecutivo,
                    estado,
                    tipo_identificacion_tercero,
                    numero_identificacion_tercero,
                    sinc_nota
                FROM $tablaPrincipal
                WHERE id='$id_nota'";
        $query = mysql_query($sql,$link);

        $fecha_nota     = mysql_result($query,0,'fecha_nota');
        $id_tercero     = mysql_result($query,0,'id_tercero');
        $codigo_tercero = mysql_result($query,0,'codigo_tercero');
        $tercero        = mysql_result($query,0,'tercero');
        $tipo_nota      = mysql_result($query,0,'tipo_nota');
        $id_tipo_nota   = mysql_result($query,0,'id_tipo_nota');
        $usuario        = mysql_result($query,0,'usuario');
        $observacion    = mysql_result($query,0,'observacion');
        $consecutivo    = mysql_result($query,0,'consecutivo');
        $estado         = mysql_result($query,0,'estado');
        $tipoNitTercero = mysql_result($query,0,'tipo_identificacion_tercero');
        $nitTercero     = mysql_result($query,0,'numero_identificacion_tercero');
        $sinc_nota      = mysql_result($query,0,'sinc_nota');

        if ($estado==1) { echo "ESTA NOTA SE ENCUENTRA CERRADA POR QUE YA HA SIDO GENERADA"; exit; }
        if($consecutivo > 0){
            $usuario    = mysql_result($query,0,'usuario');
            // $tiposNotas = '<input type="text" value="'.$tipo_nota.'" readonly/><input type="hidden" value="'.$id_tipo_nota.'" id="selectTipoNota" style="width:135;"/>';
        }
        else { /*$acumScript .= 'document.getElementById("selectTipoNota").value = "'.$id_tipo_nota.'";';*/ }

        if($estado == 0 && ($consecutivo == 0 || $consecutivo=='')){
            $sqlFecha   = "UPDATE $tablaPrincipal SET fecha_nota='$fecha_nota' WHERE id='$id_nota'";
            $queryFecha = mysql_query($sqlFecha,$link);
        }

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

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y",
                            width      : 60,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value); } }
                        });';

        $acumScript .=  'id_cliente_'.$opcGrillaContable.'  = "'.$id_tercero.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nitTercero.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$tercero.'";
                        observacion'.$opcGrillaContable.'   = "'.$observacion.'";';

        $bodyArticle = cargaArticulosSaveConTercero($id_nota,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
    }

    $habilita = ($estado=='1')? 'onclick="javascript: return false;" disabled ': '';

    //======================================= CONSULTAMOS LOS TIPOS DE DOCUMENTO ==================================================================//
    $sqlTipoDocumento   = "SELECT id,nombre,detalle,tipo FROM tipo_documento WHERE id_empresa='$id_empresa' AND activo=1";
    $queryTipoDocumento = mysql_query($sqlTipoDocumento,$link);
    $tipoDocumento      = '<select id="tipoDocumento'.$opcGrillaContable.'" style="width:65px;" onchange="document.getElementById(\'nitCliente'.$opcGrillaContable.'\').focus();">';

    while ($rowTipoDoc=mysql_fetch_array($queryTipoDocumento)) {
        $tipoDocumento.='<option value="'.$rowTipoDoc['nombre'].'" title="'.$rowTipoDoc['detalle'].'">'.$rowTipoDoc['nombre'].'</option>';
    }

    $tipoDocumento.='</select>';

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
<div class="contenedorNotaContable" id="contenedorNotaContable" style="height:calc(100% - 95px);overflow:auto;">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div class="labelTop">Sucursal</div>
                    <div class="campoTop"><input type="text" id="nombreSucursal<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" readonly></div>
                </div>
                <div class="renglonTop" style="min-width: 50px !important; width: 60px;">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Periodo</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha_nota; ?>" readonly></div>
                </div>

                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha"></div>
                <div class="renglonTop" id="divCodigoTercero">
                    <div class="labelTop">Codigo Tercero</div>
                    <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $codigo_tercero; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" ></div>
                </div>
                <div class="renglonTop" id="divIdentificacionTercero">
                    <div class="labelTop">N. de Identificacion</div>
                    <div class="campoTop" style="width:230px">
                        <?php echo  $tipoDocumento; ?>
                        <input type="text" style="width:161px"  id="nitCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $nitTercero; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tercero</div>
                    <div class="campoTop" style="width:277px;">
                        <input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $tercero; ?>" Readonly/>
                    </div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaCliente<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Proveedor">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" value="<?php echo $usuario; ?>" readonly/></div>
                </div>

                <!-- BOTON PARA ELIMINAR TODAS LAS CUENTAS DEL CUERPO DE LA NOTA -->
                <!-- <div class="button-delete-acounts" id="button-delete-acounts" title="Eliminar todas las cuentas" onclick="eliminar_cuentas('<?php echo $opcGrillaContable; ?>','<?php echo $id_nota; ?>')">
                </div> -->

                <!-- <div class="renglonTop">
                    <div class="labelTop">Filtro</div>
                    <div class="campoTop">
                      <?php echo $tiposNotas; ?>
                    </div>
                </div> -->
                <div style="float:left;max-width:20px; height:20px;margin-top:17px;overflow:hidden;" id="renderTipoNota">
                </div>

                <div class="renglonTop" style="display:none;">
                    <div class="labelTop">Opciones</div>
                    <div class="campoTop" align="center">
                        <input type="checkbox" style="margin-top: 4px;"  id="notaInterna<?php echo $opcGrillaContable; ?>" onchange="cambiaNotaInterna<?php echo $opcGrillaContable; ?>(event,this);" > Nota Interna
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

    // REDIRECCIONAR A LA FUNCION EN SUBINDEX.PHP QUE ELIMINA LAS CUENTAS
    function dir_eliminar_cuentas() {
        eliminar_cuentas('<?php echo $opcGrillaContable; ?>','<?php echo $id_nota; ?>');
    }

    //==================== FUNCION PARA CAMBIAR LA FECHA DE LA NOTA ==============================//
    function updateFechaNota<?php echo $opcGrillaContable; ?>(fecha){
        Ext.get('cargarFecha').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizarFechaNota',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_nota; ?>',
                fecha             : fecha
            }
        });
    }



    //======================== GUARDAR LA OBSERVACION DE LA NOTA =============================//
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
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            params  :
            {
                opc               : 'guardarObservacion',
                id                : '<?php echo $id_nota; ?>',
                observacion       : observacion,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
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
                        // alert('Error de conexion con el servidor'); document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                        document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                        setTimeout(function () {
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
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
            ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(Input.value, Input.id, document.getElementById('tipoDocumento<?php echo $opcGrillaContable; ?>').value);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        return true;
    }

    function ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codCliente, inputId, tipoDocumento){
        Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCliente',
                codCliente        : codCliente,
                tipoDocumento     : tipoDocumento,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_nota; ?>',
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

        ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codigo, 'nitCliente<?php echo $opcGrillaContable; ?>', typeDoc);
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

        var tercero = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML;

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
                id                : '<?php echo $id_nota; ?>'
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
                url     : '<?php echo $carpeta;?>bd/buscarCuentaPuc.php',
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
                if (credito>0) {
                    alert("Error!\nIngrese  el valor para el asiento en debito");
                    setTimeout(function(){ document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
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
                    && id_documento_cruce == document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+i).value
                    && tipoDocumentoCruce == document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+i).value)
                {
                    alert("Error!\nEl documento ya esta en la nota!\nNo se pueden repetir los documentos con las mismas cuentas!");
                    setTimeout(function(){ document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
                    return;
                }
            }
        }

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
                id                    : '<?php echo $id_nota; ?>',
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
                    id                : '<?php echo $id_nota; ?>'
                }
            });
            calcTotal<?php echo $opcGrillaContable ?>(debito,credito,'eliminar');
        }
    }

    //====================== CAMBIAR LA CUENTA NIIF POR DEFECTO DE LA COLGAAP ==================================//
    function cambiaCuentaNiif<?php echo $opcGrillaContable; ?>(cont) {
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

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
                    cont              : cont,
                    opc               : 'cargaConfiguracionCuenta',
                    idInsertCuenta    : idInsertCuenta,
                    idCuenta          : idCuenta,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_nota           : '<?php echo $id_nota; ?>'
                }
            }
        }).show();
    }

    //=========================// VENTANA BUSCAR EL CENTRO DE COSTOS //=========================//
    //******************************************************************************************//
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
                url     : 'nota_general/bd/centro_costos.php',
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

    function renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id,cont){

        var nombre         = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML
        var codigo         = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_codigo_'+id).innerHTML;
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
                id_centro_costos     : id,
                codigo_centro_costos : codigo,
                id_nota              : '<?php echo $id_nota; ?>'
            },
            success :function (result, request){
                        var response = (result.responseText).replace(/[^a-z]/g,'');
                        if(response == 'true'){

                            Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
                            document.getElementById('codigoCcos_<?php echo $opcGrillaContable; ?>').innerHTML=codigo;
                            document.getElementById('nombreCcos_<?php echo $opcGrillaContable; ?>').innerHTML=nombre;

                        }
                        else if(response == 'padre'){ alert("Error,\nNo se puede seleccionar un centro de costo padre"); }
                        else{ alert("Error,\nNo se acualizo el centro de costo en la cuenta"); }
                    },
            failure : function(){  }
        });
    }

    //===================== FUNCION QUE ABRE LA VENTANA PARA BUSCAR LA CUENTA NIIF A CAMBIAR ============================//
    function responseVentanaBuscarCuentaNiif<?php echo $opcGrillaContable; ?>(id,cont) {
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cuenta         = document.getElementById('div_<?php echo $opcGrillaContable; ?>_cuenta_'+id).innerHTML
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
                            Win_Ventana_cambiar_cuenta_niif.close();
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
                id                : '<?php echo $id_nota; ?>'
            }
        });
    }

    //=============================================== FUNCION SIN USO =================================================//
    function guardar<?php echo $opcGrillaContable; ?>(){ }                  // REEMPLAZADA POR VALIDAR QUE VALIDA Y GENERA LA NOTA.

    //===================================== VALIDAR LA NOTA ANTES DE GENERARLA ========================================//
    function validar<?php echo $opcGrillaContable; ?>(opcionGenerar){
        cargando_documentos('Generando Cierre...');

        opc = 'validaNota'
        var fechaDocumento = document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value;

        Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : opc,
                id                : '<?php echo $id_nota; ?>',
                fechaDocumento    : fechaDocumento,
                // notaCruce      : arrayTipoNota[id_tipo_nota],
                // id_tercero     : id_cliente_<?php echo $opcGrillaContable; ?>,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });

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
        ventanaBuscar<?php echo $opcGrillaContable; ?>();
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

    //============================ CANCELAR UN DOCUMENTO =========================================================================//

    function cancelar<?php echo $opcGrillaContable; ?>(){
        var contArticulos = 0;

        if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }

        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');

        for(i in arrayIdsArticulos){ if(arrayIdsArticulos[i].innerHTML != '' ){ contArticulos++; } }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
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
            };
        }
    }

</script>