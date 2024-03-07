<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../funciones_globales/funciones_php/randomico.php");
    include("../../../funciones_globales/funciones_javascript/totalesNotaContable.php");

    $id_empresa   = $_SESSION['EMPRESA'];
    $id_sucursal  = $_SESSION['SUCURSAL'];
    $bodyArticle  = '';
    $acumScript   = '';
    $estado       = '';
    $fecha        = date('Y').'-01-01';
    $fecha_final  = date('Y-m-d');
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
    // Ext.getCmp("Btn_exportar_<?php echo $opcGrillaContable; ?>").enable();
    // Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").enable();
    // Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").enable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();

</script>
<?php



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
    $concepto             = mysql_result($query,0,'concepto');
    $usuario              = mysql_result($query,0,'usuario');
    $observacion          = mysql_result($query,0,'observacion');
    $consecutivo          = mysql_result($query,0,'consecutivo');
    $estado               = mysql_result($query,0,'estado');
    $tipoNitTercero       = mysql_result($query,0,'tipo_identificacion_tercero');
    $nitTercero           = mysql_result($query,0,'numero_identificacion_tercero');
    $cuenta_colgaap_cruce = mysql_result($query,0,'cuenta_colgaap_cruce');

    // if ($estado==1) { echo "ESTA NOTA SE ENCUENTRA CERRADA POR QUE YA HA SIDO GENERADA"; exit; }
    // if($consecutivo > 0){ $tiposNotas = '<input type="text" value="'.$tipo_nota.'" readonly/><input type="hidden" value="'.$concepto.'" id="selectConcepto" style="width:135;"/>'; }
    // else { $acumScript .= 'document.getElementById("selectConcepto").value = "'.$concepto.'";'; }

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion        = str_replace($arrayReplaceString, "\\n", $observacion);

    $acumScript .=  '   id_cliente_'.$opcGrillaContable.'  = "'.$id_tercero.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nitTercero.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$tercero.'";
                        observacion'.$opcGrillaContable.'   = "'.$observacion.'";

                        document.getElementById("fecha_documento'.$opcGrillaContable.'").value = "'.$fecha_nota.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value           = "'.$fecha_inicio.'";
                        document.getElementById("fecha_final'.$opcGrillaContable.'").value     = "'.$fecha_final.'";
                        document.getElementById("codigoTercero'.$opcGrillaContable.'").value   = "'.$codigo_tercero.'";
                        document.getElementById("tipoDocumento'.$opcGrillaContable.'").value   = "'.$tipoNitTercero.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value      = "'.$nitTercero.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value   = "'.$tercero.'";
                        document.getElementById("usuario'.$opcGrillaContable.'").value         = "'.$usuario.'";
                        document.getElementById("cuenta_cruce'.$opcGrillaContable.'").value         = "'.$cuenta_colgaap_cruce.'";

                        document.getElementById("titleDocumentoLiquidacionProvision").innerHTML="LIQUIDACION '.$concepto.'<br>No. '.$consecutivo.'";

                     ';


    $bodyArticle = cargaArticulosSaveConTercero($id_nota,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);


    if ($estado==1) {
        $acumScript.='Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").enable();
                        Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();
                        Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_enviar_email_'.$opcGrillaContable.'").enable();
                        Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';
    }
    else if($estado==3){
        $acumScript.='Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();
                        Ext.getCmp("Btn_enviar_email_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();';
    }

    $acumScript .= (user_permisos(101,'false') == 'false')? 'Ext.getCmp("Btn_nueva_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(101,'false') == 'false')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(102,'false') == 'false')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();'   : '';
    $acumScript .= (user_permisos(103,'false') == 'false')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(104,'false') == 'false')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();' : '';



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
                    <div class="campoTop"><input type="text" id="nombreSucursal<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" readonly></div>
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
                    <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $codigo_tercero; ?>" readonly ></div>
                </div>
                <div class="renglonTop" id="divIdentificacionTercero">
                    <div class="labelTop">N. de Identificacion</div>
                    <div class="campoTop" style="width:230px">
                        <input type="text" style="width:61px" readonly id="tipoDocumento<?php echo $opcGrillaContable; ?>">
                        <input type="text" style="width:161px"  id="nitCliente<?php echo $opcGrillaContable; ?>"  readonly />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tercero</div>
                    <div class="campoTop" style="width:277px;">
                        <input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $tercero; ?>" Readonly/>
                    </div>
                </div>

                <div class="renglonTop" style="max-width: 150px;">
                    <div class="labelTop" style="float:left;">Cuenta Cruce</div><div style="float: left;margin-left: -20px;width: 20px;height: 19px;overflow: hidden;" id="divLoadCuentaPago"></div>
                    <div class="campoTop"><input type="text" id="cuenta_cruce<?php echo $opcGrillaContable; ?>" value="<?php echo $cuenta_cruce; ?>" readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>" readonly/></div>
                </div>
                <div class="renglonTop" style="max-width: 200px;">
                    <div class="labelTop">Provision</div>
                    <div class="campoTop">
                      <input type="text" id="concepto<?php echo $opcGrillaContable; ?>" value="<?php echo $concepto; ?>" readonly/></div>
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
<div id="divLoadEnviarEmail" style="display:none;"></div>
<script>

    var observacion<?php echo $opcGrillaContable; ?> = '';
    var globalNameFileUpload = '';
    <?php echo $acumScript; ?>

    // var id_tipo_nota = document.getElementById('selectConcepto').value;
    // var classNota    = (arrayTipoNota[id_tipo_nota] == "Si")? 'contenedorNotaContableCruce': 'contenedorNotaContable';
    var classNota    = 'contenedorNotaContableCruce';

    document.getElementById("contenedorNotaContable").setAttribute("class",classNota);
    document.getElementById('codigoTercero<?php echo $opcGrillaContable; ?>').focus();


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
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_sucursal       : '<?php echo $id_sucursal; ?>',
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

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento<?php echo $opcGrillaContable ?>(){

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo")) {
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

    //============================ CANCELAR UN DOCUMENTO =========================================================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){

        if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
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


    //============================ CANCELAR UN DOCUMENTO =========================================================================//
    function restaurar<?php echo $opcGrillaContable; ?>(){

        Ext.get("terminar<?php echo $opcGrillaContable; ?>").load({
            url  : '<?php echo $carpeta; ?>bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                idDocumento                : '<?php echo $id_nota; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });
    }

    function imprimirLiquidacionProvision(){
        window.open("liquidacion_provision/bd/imprimirGrillaContable.php?id=<?php echo $id_nota;?>");
    }

    function enviaremail() {

        Win_Ventana_enviar_email = new Ext.Window({
            width       : 500,
            height      : 500,
            id          : 'Win_Ventana_enviar_email',
            title       : 'Enviar Email a cada Empleado',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'liquidacion_provision/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventana_enviar_email',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_nota           : '<?php echo $id_nota; ?>',
                    concepto          : '<?php echo $concepto; ?>',
                    consecutivo       : '<?php echo $consecutivo ?>',
                    fecha_inicio      : '<?php echo $fecha_inicio; ?>',
                    fecha_final       : '<?php echo $fecha_final; ?>',
                }
            },
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Opciones',
                    style   : 'border-right:none;',
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
                            hidden      : false,
                            handler     : function(){ BloqBtn(this); Win_Ventana_enviar_email.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    function enviarVolanteUnicoEmpleadoLiquidacion(id_tercero,consecutivo,concepto,fecha_inicio,fecha_final,nombre_empleado,saldo) {
        var contenido = '<div id="modal">'+
                            '<div id="experiment" style="position:static !important;">'+
                                '<div style="text-align:center;"><img style="cursor:pointer;" src="img/email.gif"></div>'+
                                '<div id="LabelCargando">Enviando Liquidacion de Provision a <br>'+nombre_empleado+'...</div>'+
                            '</div>'+
                        '</div>';

        var div=document.createElement('div');
        div.setAttribute('id','divPadreModal');
        div.setAttribute('class','fondo_modal');
        div.innerHTML=contenido;
        document.body.appendChild(div);
        // return;
        Ext.get('divLoadEnviarEmail').load({
            url     : 'liquidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc          : 'enviarVolanteUnicoEmpleado',
                id_tercero   : id_tercero,
                consecutivo  : consecutivo,
                concepto     : concepto,
                fecha_inicio : fecha_inicio,
                fecha_final  : fecha_final,
                saldo        : saldo,
            }
        });
    }


</script>