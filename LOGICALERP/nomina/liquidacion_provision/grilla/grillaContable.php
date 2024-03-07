<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../funciones_globales/funciones_php/randomico.php");
    include("../../../funciones_globales/funciones_javascript/totalesNotaContable.php");

    $id_empresa   = $_SESSION['EMPRESA'];
    $id_sucursal  = $filtro_sucursal;
    $bodyArticle  = '';
    $acumScript   = '';
    $estado       = '';
    $fecha        = date('Y').'-01-01';
    $fecha_final  = date('Y-m-d');

    $sql="SELECT nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_sucursal";
    $query=mysql_query($sql,$link);
    $nombre_sucursal=mysql_result($query,0,'nombre');
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
    Ext.getCmp("Btn_nueva_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();

</script>
<?php

     // PERMISOS DEL DOCUMENTO
    $acumScript .= (user_permisos(101,'false') == 'true')? 'Ext.getCmp("Btn_nueva_'.$opcGrillaContable.'").enable();' : '';
    $acumScript .= (user_permisos(101,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';
    $acumScript .= (user_permisos(102,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();'   : '';
    $acumScript .= (user_permisos(103,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';
    $acumScript .= (user_permisos(104,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';


    //========================= CONSULTAMOS LOS CONCEPTOS  TIPO PROVISION ==============================//
    //$sqlTiposNotas   = "SELECT id,descripcion,documento_cruce FROM tipo_nota_contable WHERE activo=1 AND id_empresa='$id_empresa'";
    // $queryTiposNotas = mysql_query($sqlTiposNotas,$link);

    $sql="SELECT id,descripcion FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND naturaleza='Provision'";
    $query=mysql_query($sql,$link);

    $tiposProvision      = '<select id="selectConcepto" onchange="actualizarTipoProvision(this);">';
    $id_concepto=0;
    while ($row = mysql_fetch_array($query)) {
        if($id_concepto == 0){
            $id_concepto    = $row['id'];
            // $cruce_tipo_nota = $rowTiposNotas['documento_cruce'];
        }
        $tiposProvision .= '<option value="'.$row['id'].'">'.$row['descripcion'].'</option>';
        // $acumScript .= 'arrayTipoNota['.$rowTiposNotas['id'].']= "'.$rowTiposNotas['documento_cruce'].'";';
    }
    $tiposProvision     .= '</select>';

    //============================================ SI NO EXISTE EL PROCESO SE CREA EL ID UNICO ===================================================
    if(!isset($id_nota)){

        // if(!isset($sinc_nota)) $sinc_nota = 'colgaap_niif';
        // $acumScript .= 'var sinc_nota_'.$opcGrillaContable.' = "'.$sinc_nota.'";';


        // CREACION DEL ID UNICO
        $random_nota = responseUnicoRanomico();

        $sqlInsert   ="INSERT INTO $tablaPrincipal (id_empresa,random,id_sucursal,id_usuario,cedula_usuario,usuario,id_concepto,fecha_nota,fecha_inicio,fecha_final)
                        VALUES('$id_empresa','$random_nota','$id_sucursal','".$_SESSION['IDUSUARIO']."','".$_SESSION['CEDULAFUNCIONARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."','$id_concepto','$fecha_final','$fecha','$fecha_final')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId = "SELECT id FROM $tablaPrincipal  WHERE random='$random_nota' LIMIT 0,1";
        $id_nota     = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fecha_documento'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha_final.'",
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value,"fecha_documento'.$opcGrillaContable.'"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha.'",
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value,"fecha'.$opcGrillaContable.'"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fecha_final'.$opcGrillaContable.'",
                            editable   : false,
                            value      :"'.$fecha_final.'",
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value,"fecha_final'.$opcGrillaContable.'"); } }
                        });

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
                        });
                        document.getElementById("titleDocumentoLiquidacionProvision").innerHTML="";
                        ';
    }

    //======================================================= SI EXISTE EL PROCESO =================================================================
    else{

        include("../bd/functions_body_article.php");

        $sql = "SELECT  fecha_nota,
                        fecha_inicio,
                        fecha_final,
                        id_tercero,
                        codigo_tercero,
                        tercero,
                        cuenta_colgaap_cruce,
                        id_concepto,
                        concepto,
                        usuario,
                        observacion,
                        consecutivo,
                        estado,
                        tipo_identificacion_tercero,
                        numero_identificacion_tercero
                FROM $tablaPrincipal
                WHERE id='$id_nota'";

        $query = mysql_query($sql,$link);

        $fecha_nota           = mysql_result($query,0,'fecha_nota');
        $fecha_inicio         = mysql_result($query,0,'fecha_inicio');
        $fecha_final          = mysql_result($query,0,'fecha_final');
        $id_tercero           = mysql_result($query,0,'id_tercero');
        $codigo_tercero       = mysql_result($query,0,'codigo_tercero');
        $tercero              = mysql_result($query,0,'tercero');
        $tipo_nota            = mysql_result($query,0,'tipo_nota');
        $id_concepto          = mysql_result($query,0,'id_concepto');
        $concepto             = mysql_result($query,0,'concepto');
        $usuario              = mysql_result($query,0,'usuario');
        $observacion          = mysql_result($query,0,'observacion');
        $consecutivo          = mysql_result($query,0,'consecutivo');
        $estado               = mysql_result($query,0,'estado');
        $tipoNitTercero       = mysql_result($query,0,'tipo_identificacion_tercero');
        $nitTercero           = mysql_result($query,0,'numero_identificacion_tercero');
        $cuenta_colgaap_cruce = mysql_result($query,0,'cuenta_colgaap_cruce');

        if ($estado==1) { echo "ESTA NOTA SE ENCUENTRA CERRADA POR QUE YA HA SIDO GENERADA"; exit; }
        if($consecutivo > 0){ $tiposProvision = '<input type="text" value="'.$concepto.'" readonly/><input type="hidden" value="'.$id_concepto.'" id="selectConcepto" style="width:135;"/>'; }
        else { $acumScript .= 'document.getElementById("selectConcepto").value = "'.$id_concepto.'";'; }

        $labelConsecutivo=($consecutivo>0)? 'LIQUIDACION '.$concepto.'<br>No. '.$consecutivo : '' ;

        // if ($consecutivo>0) {
        //     $acumScript.='document.getElementById("divTipoProvision").innerHTML="<input type=\'text\' id=\'selectConcepto\' value=\''.$concepto.'\' readonly>";';
        // }

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", $observacion);

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fecha_documento'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value,"fecha_documento'.$opcGrillaContable.'"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value,"fecha'.$opcGrillaContable.'"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : true,
                            applyTo    : "fecha_final'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value,"fecha_final'.$opcGrillaContable.'"); } }
                        });';

        $acumScript .=  '   id_cliente_'.$opcGrillaContable.'  = "'.$id_tercero.'";
                            nitCliente'.$opcGrillaContable.'    = "'.$nitTercero.'";
                            nombreCliente'.$opcGrillaContable.' = "'.$tercero.'";
                            observacion'.$opcGrillaContable.'   = "'.$observacion.'";

                            document.getElementById("fecha_documento'.$opcGrillaContable.'").value = "'.$fecha_nota.'";
                            document.getElementById("fecha'.$opcGrillaContable.'").value           = "'.$fecha_inicio.'";
                            document.getElementById("fecha_final'.$opcGrillaContable.'").value     = "'.$fecha_final.'";
                            document.getElementById("codigoTercero'.$opcGrillaContable.'").value   = "'.$codigo_tercero.'";
                            document.getElementById("nitCliente'.$opcGrillaContable.'").value      = "'.$nitTercero.'";
                            document.getElementById("nombreCliente'.$opcGrillaContable.'").value   = "'.$tercero.'";
                            document.getElementById("usuario'.$opcGrillaContable.'").value         = "'.$usuario.'";
                            document.getElementById("cuenta_cruce'.$opcGrillaContable.'").value         = "'.$cuenta_colgaap_cruce.'";

                            document.getElementById("titleDocumentoLiquidacionProvision").innerHTML="'.$labelConsecutivo.'";
                         ';


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
      z-index          : 99999;
      top              : 0px;
      width            : 100%;
      height           : 100%;
      display          : table;
      left             : 0px;
      position         : absolute !important;

    }

    #modal{
        display: table-cell;
        vertical-align: middle;

    }
</style>
<div class="contenedorNotaContable" id="contenedorNotaContable">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div class="labelTop">Sucursal</div>
                    <div class="campoTop"><input type="text" id="nombreSucursal<?php echo $opcGrillaContable; ?>" value="<?php echo $nombre_sucursal; ?>"  readonly></div>
                </div>
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha Documento</div>
                    <div class="campoTop"><input type="text" id="fecha_documento<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
                </div>
                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha_documento"></div>
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha Inicial Planillas</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
                </div>
                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha_inicial"></div>
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha Final Planillas</div>
                    <div class="campoTop"><input type="text" id="fecha_final<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha_final; ?>" readonly></div>
                </div>

                <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha_final"></div>
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

                <div class="renglonTop" style="max-width: 150px;">
                    <div class="labelTop" style="float:left;">Cuenta Cruce</div><div style="float: left;margin-left: -20px;width: 20px;height: 19px;overflow: hidden;" id="divLoadCuentaPago"></div>
                    <div class="campoTop"><input type="text" id="cuenta_cruce<?php echo $opcGrillaContable; ?>" value="<?php echo $cuenta_cruce; ?>" readonly></div>
                    <div class="iconBuscarProveedor" style="margin-left:-42px;width:40px;" >
                       <img src="img/buscar20.png" onclick="ventanaBuscarCuentaCruce()" id="imgBuscarProveedor"  title="Buscar Cuenta" />
                       <img src="img/config16.png" onclick="cargaNiifCruce()" id="imgConfiguraNiif"  title="Configurar Cuenta Niif" />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>" readonly/></div>
                </div>
                <div class="renglonTop" style="max-width: 200px;">
                    <div class="labelTop">Provision</div>
                    <div class="campoTop" id="divTipoProvision">
                      <?php echo $tiposProvision; ?>
                    </div>
                </div>
                <div style="float:left;max-width:20px; height:20px;margin-top:17px;overflow:hidden;" id="renderTipoNota">
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

    // var id_tipo_nota = document.getElementById('selectConcepto').value;
    // var classNota    = (arrayTipoNota[id_tipo_nota] == "Si")? 'contenedorNotaContableCruce': 'contenedorNotaContable';
    var classNota    = 'contenedorNotaContableCruce';

    document.getElementById("contenedorNotaContable").setAttribute("class",classNota);
    document.getElementById('codigoTercero<?php echo $opcGrillaContable; ?>').focus();


    //==================== FUNCION PARA CAMBIAR LA FECHA DE LA NOTA ==============================//
    function updateFechaNota<?php echo $opcGrillaContable; ?>(fecha,campo){

        var divLoad='';
        var campoInsert='';
        if (campo=='fecha_documento<?php echo $opcGrillaContable;?>') {
            divLoad='cargarFecha_documento';
            campoInsert='fecha_nota';
        }
        if (campo=='fecha<?php echo $opcGrillaContable;?>') {
            divLoad='cargarFecha_inicial';
            campoInsert='fecha_inicio';
        }
        if (campo=='fecha_final<?php echo $opcGrillaContable;?>') {
            divLoad='cargarFecha_final';
            campoInsert='fecha_final';
        }

        Ext.get(divLoad).load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizarFechaNota',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_nota; ?>',
                fecha             : fecha,
                campo             : campoInsert,
            }
        });

    }

    //==================== GUARDAR EL TIPO DE NOTA ================================================//
    function actualizarTipoProvision(selectConcepto){
        // VALIDAR SI HAY CUENTAS YA GUARDADAS PARA MOSTRAR LA ALERTA DE QUE SI SE CAMBIA SE ELIMINAN LAS GUARDADAS
        var validar=validarCuentas<?php echo $opcGrillaContable; ?>();
        if (validar>0) {
            if (!confirm('Si cambia el tipo de Provision se eliminaran las cuentas y documentos relacionados\nDesea continuar?')) { return; }
        }

        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizarTipoNota',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_nota; ?>',
                id_concepto        : selectConcepto.value,
            }
        });

    }

    //======================== GUARDAR LA OBSERVACION DE LA NOTA =============================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
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
                        result=result.responseText;
                        result=result.split('{.}')[0];
                        if(result != 'true'){
                            alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                        }
                        else{ observacion<?php echo $opcGrillaContable; ?>=observacion; }

                    },
            failure : function(){ alert('Error de conexion con el servidor'); document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;}
        });

    }

    //============================= FUNCION PARA BUSCAR LA CUENTA CRUCE ==========================//
    function ventanaBuscarCuentaCruce() {
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();


        Win_Ventana_buscar_cuenta = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_cuenta',
            title       : 'Seleccionar Cuenta colgaap',
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
                    nombreGrilla      : 'buscar_cuenta_colgaap',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cargaFuncion      : 'responseVentanaBuscarCuentaCruce(id);',
                    opc               : 'colgaap',
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
                    handler     : function(){ Win_Ventana_buscar_cuenta.close(id) }
                },'-'
            ]
        }).show();
    }

    //======================== RESPONDER A LA VENTANA DE CUENTA DE CRUCE ========================//
    function responseVentanaBuscarCuentaCruce(id) {
        Win_Ventana_buscar_cuenta.close();

        Ext.get('divLoadCuentaPago').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizarCuentaCruce',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_nota; ?>',
                id_cuenta         : id,
            }
        });
    }

    //============================= FUNCION PARA CARGAR LA CUENTA NIIF CRUCE Y ACTUALIZARLA =====================//
    function cargaNiifCruce() {
        var cuenta=document.getElementById('cuenta_cruce<?php echo $opcGrillaContable;?>').value;
        if (cuenta=='' || cuenta==0) {alert("Debe seleccionar primero la cuenta colgaap"); return;}

        Win_Ventana_cambiar_cuenta_niif = new Ext.Window({
            width       : 400,
            height      : 140,
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
                    opc               : 'cargaNiifCruce',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_nota; ?>'
                }
            }
        }).show();
    }

    //============================= FUNCION PARA BUSCAR LA CUENTA CRUCE NIIF ==========================//
    function ventanaBuscarCuentaCruceNiif() {


        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();


        Win_Ventana_buscar_cuenta = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_cuenta',
            title       : 'Seleccionar Cuenta colgaap',
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
                    nombreGrilla      : 'buscar_cuenta_niif',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cargaFuncion      : 'responseVentanaBuscarCuentaCruceNiif(id);',
                    opc               : 'niif',
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
                    handler     : function(){ Win_Ventana_buscar_cuenta.close(id) }
                },'-'
            ]
        }).show();
    }

    //======================== RESPONDER A LA VENTANA DE CUENTA DE CRUCE NIIF ========================//
    function responseVentanaBuscarCuentaCruceNiif(id) {
        // console.log('div_buscar_cuenta_niif_descripcion_'+id);


        var cuenta = document.getElementById('div_buscar_cuenta_niif_cuenta_'+id).innerHTML;
        // return;
        Ext.get('divLoadCuentaPago').load({
            url     : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'actualizarCuentaCruceNiif',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_nota; ?>',
                cuenta            : cuenta,
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
            var numeroDocumentoCruce=document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
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

        var id_tabla_referencia   = document.getElementById('idTablaReferencia<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   id_documento_cruce    = document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   numeroDocumentoCruce  = document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tipoDocumentoCruce    = document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tipo_nota             = document.getElementById('selectConcepto').value;

        //VALIDAR QUE LA FILA TENGA UNA CUENTA
        if (idPuc == 0){ alert('El campo cuenta es Obligatorio'); setTimeout(function(){ document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        //VALIDAR QUE TENGA ALMENOS UN VALOR EN EL DEBITO O CREDITO
        else if (debito>0 && credito>0) { alert('La cuenta no puede tener valores debito y credito\nSolo puede tener uno'); setTimeout(function(){ document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        else if (debito==0 && credito==0){ alert('La cuenta debe tener un valor para debito o credito'); setTimeout(function(){ document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }

        if(tipo_nota == 0
            && ( tipoDocumentoCruce != ''
                && (numeroDocumentoCruce==0 || isNaN(numeroDocumentoCruce) || id_tercero==0)
                    || (numeroDocumentoCruce==0 || isNaN(numeroDocumentoCruce) || tipoDocumentoCruce=='' || id_tercero==0)
                    || numeroDocumentoCruce > 0 && (tipoDocumentoCruce=='' || id_tercero==0)
                )
            ){
            alert('Aviso,\nCampos obligatorios para cruce con documento:\n* Tercero\n* Tipo Documento Cruce\n* Numero Documento Cruce');
            return;
        }

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
                opc                  : opc,
                opcGrillaContable    : '<?php echo $opcGrillaContable; ?>',
                consecutivo          : contArticulos<?php echo $opcGrillaContable; ?>,
                cont                 : cont,
                idInsertCuenta       : idInsertCuenta,
                idPuc                : idPuc,
                debe                 : debito,
                haber                : credito,
                cuenta               : cuenta,
                id                   : '<?php echo $id_nota; ?>',
                id_tercero           : id_tercero,
                terceroGeneral       : terceroGeneral,
                id_tabla_referencia  : id_tabla_referencia,
                id_documento_cruce   : id_documento_cruce,
                numeroDocumentoCruce : numeroDocumentoCruce,
                tipoDocumentoCruce   : tipoDocumentoCruce
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
        var idCuenta = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_cambiar_cuenta_niif = new Ext.Window({
            width       : 400,
            height      : 140,
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
                    opc               : 'cargaCuentaNiif',
                    idInsertCuenta    : idInsertCuenta,
                    idCuenta          : idCuenta,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cont              : cont,
                    id                : '<?php echo $id_nota; ?>'
                }
            }
        }).show();
    }

    //===================== FUNCION QUE ABRE LA VENTANA PARA BUSCAR LA CUENTA NIIF A CAMBIAR ============================//
    function responseVentanaBuscarCuentaNiif<?php echo $opcGrillaContable; ?>(id,cont) {
        var idInsertCuenta = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idCuenta       = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cuenta      = document.getElementById('div_<?php echo $opcGrillaContable; ?>_cuenta_'+id).innerHTML
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
                        // console.log(result.responseText);
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
        opc = 'terminarGenerar'
        // if (opcionGenerar =='terminar') { opc = 'terminarGenerar'; }

        //VALIDACION CUENTAS POR GUARDAR
        var validacion = validarCuentas<?php echo $opcGrillaContable; ?>();
        if (validacion==0) {alert("No hay nada que guardar!"); return;}
        else if (validacion==1) { alert("Hay cuentas pendientes por guardar!"); return; }

        else if (validacion == 2 || validacion == 0) {
            var tipo_nota   = document.getElementById('selectConcepto').value
            ,   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;

            // var arrayReturn=verificaDebitoCredito();
            // var diferencia=parseFloat(arrayReturn[1])-parseFloat(arrayReturn[2]);
            // if (arrayReturn[0]<2) {alert("Error!\nDebe ingresar minimo dos cuentas!");return;}
            // if (diferencia!=0) {alert("Error!\nLa nota no esta correctamente balanceada\nTiene una diferencia de: $ "+(parseFloat(arrayReturn[1])-parseFloat(arrayReturn[2]))+"\nPor favor verifiquela y intentelo nuevamente"); return;}

            Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
                url     : '<?php echo $carpeta; ?>bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : opc,
                    id                : '<?php echo $id_nota; ?>',
                    id_tercero        : id_cliente_<?php echo $opcGrillaContable; ?>,
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
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_sucursal       : document.getElementById('filtro_sucursal_liquidacion_provision').value,
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

        if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }

        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campo');
        // console.log(arrayIdsArticulos.length);
        for(i in arrayIdsArticulos){ /*console.log(arrayIdsArticulos[i]);*/ if(arrayIdsArticulos[i]!= '' ){ contArticulos++;  } }
        // console.log(arrayIdsArticulos);
        // if(contArticulos > 0){
        if(arrayIdsArticulos.length > 1){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
                Ext.get("terminar<?php echo $opcGrillaContable; ?>").load({
                    url  : '<?php echo $carpeta; ?>bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc               : 'cancelarDocumento',
                        id                : '<?php echo $id_nota; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    }
                });
            };
        }
    }

    //============================= FUNCION PARA BUSCAR EL DOCUMENTO CRUCE DEL COMPROBANTE ========================================//
    function ventanaBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>(cont){

        var fecha_inicio = document.getElementById('fecha<?php echo $opcGrillaContable;?>').value;
        var fecha_final  = document.getElementById('fecha_final<?php echo $opcGrillaContable;?>').value;
        var id_concepto  = document.getElementById('selectConcepto').value;
        var sucursal     = document.getElementById('filtro_sucursal_liquidacion_provision').value;

        // console.log(fecha_inicio);
        // console.log(fecha_final);

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
                    bodyStyle   : 'background-color:#DFE8F6;',
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
                            height      : 56,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : '../funciones_globales/filtros/filtro_unico_sucursal.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    renderizaBody : 'true',
                                    url_render    : '<?php echo $carpeta; ?>bd/grillaCuentasPlanillaNomina.php',
                                    opc           : 'buscar_documento_cruce',
                                    contenedor    : 'contenedor_buscar_documento_cruce_<?php echo $opcGrillaContable; ?>',
                                    imprimeVarPhp : 'opcGrillaContable : "<?php echo $opcGrillaContable; ?>",fecha_inicio      : "'+fecha_inicio+'",fecha_final       : "'+fecha_final+'",id_concepto       : "'+id_concepto+'",id_nota           : "<?php echo $id_nota; ?>",cont              : "'+cont+'",id_sucursal       : "'+sucursal+'",',
                                    script        : 'document.getElementById("filtro_sucursal_buscar_documento_cruce").value="'+sucursal+'"; //console.log("document.getElementById(\\"filtro_sucursal_buscar_empleado\\").value=\\"'+sucursal+'\\";");',
                                }
                            }
                        }
                    ]
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Cargar Todo',
                    scale       : 'large',
                    iconCls     : 'restaurar32',
                    iconAlign   : 'top',
                    handler     : function(){ cargarTodasProvisiones(); }
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
        document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('title','Buscar Documento Cruce');
        document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('onclick',"ventanaBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>("+cont+")");

        var idInsert=document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'block';
            document.getElementById("divImageDeshacer<?php echo $opcGrillaContable; ?>_"+cont).style.display = 'block';
        }

        //LIMPIAR LOS CAMPOS
        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).value               = "";
        document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value             = "";
        document.getElementById("documentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value        = "";
        document.getElementById("numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value  = "";
        document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value      = "";

        arrayCuentaPago[cont]      = 0;
        arraySaldoCuentaPago[cont] = 0;

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('title','Buscar Tercero');
        document.getElementById('imgBuscarTercero<?php echo $opcGrillaContable; ?>_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+cont+")");
    }


</script>