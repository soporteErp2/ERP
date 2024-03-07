<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../../../funciones_globales/funciones_php/randomico.php");
    include("../config_var_global.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $id_usuario  = $_SESSION['IDUSUARIO'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');
    $exento_iva  = '';

?>
<script>

    //variables para calcular los valores de los costos y totales de la factura

    var observacion<?php echo $opcGrillaContable; ?>        = ''
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
    ,   timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

    //VARIABLES DE LOS TOTALES POR EMPLEADO
    var totalDevengoEmpleado     = 0
    ,   totalDeduccionEmpleado   = 0
    ,   totalApropiacionEmpleado = 0
    ,   totalProvisionEmpleado = 0
    ,   totalNetoPagarEmpleado   = 0

    //Bloqueo todos los botones
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();

    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();


</script>
<?php



    $sql   = "SELECT consecutivo,
                    fecha_documento,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha_inicio,
                    date_format(fecha_final,'%Y-%m-%d') AS fecha_final,
                    observacion,
                    usuario,
                    estado,
                    tipo_liquidacion,
                    sucursal,
                    saldo_inicial
                FROM $tablaPrincipal
                WHERE id='$id_planilla' AND activo = 1";
    $query = mysql_query($sql,$link);

    $fechaDoc         = mysql_result($query,0,'fecha_documento');
    $fecha            = mysql_result($query,0,'fecha_inicio');
    $fechaFin         = mysql_result($query,0,'fecha_final');
    $estado           = mysql_result($query,0,'estado');
    $nombre_vendedor  = mysql_result($query,0,'usuario');
    $consecutivo      = mysql_result($query,0,'consecutivo');
    $tipo_liquidacion = mysql_result($query,0,'tipo_liquidacion');
    $sucursal         = mysql_result($query,0,'sucursal');
    $saldo_inicial    = mysql_result($query,0,'saldo_inicial');
    $saldo_inicial=($saldo_inicial=='true')? 'Si' : 'No' ;

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .= 'document.getElementById("fechaDoc'.$opcGrillaContable.'").value          = "'.$fechaDoc.'";
                    document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha.'";
                    document.getElementById("fechaFinal'.$opcGrillaContable.'").value     = "'.$fechaFin.'";
                    document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$nombre_vendedor.'";
                    // document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Planilla de Consolidacion <br>N. '.$consecutivo.'";
                    document.getElementById("observacion'.$opcGrillaContable.'").value = "'.$observacion.'";
                    document.getElementById("sucursal").value = "'.$sucursal.'";
                    observacion'.$opcGrillaContable.'   = "'.$observacion.'";';
    $acumScript .=($consecutivo>0)? 'document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Planilla de Consolidacion <br>N. '.$consecutivo.'";' : '';

    //======================= CONSULTAR LOS EMPLEADOS CARGADOS EN LA PLANILLA ==================================//
    $sql="SELECT id_empleado,documento_empleado,nombre_empleado,id_contrato,verificado,email_enviado FROM nomina_planillas_consolidacion_provision_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
    $query=mysql_query($sql,$link);
    $bodyEmpleados='';
    $cont=1;
    while ($row=mysql_fetch_array($query)) {
        $titleImg=($row['email_enviado']=='true')? 'Reenviar Volante por Email' : 'Enviar Volante por Email' ;
        if ($estado==1) {
            // $btnEmail='<div  class="iconBuscar" style="margin-left: -1px;" onclick="enviarVolanteUnicoEmpleado(\''.$row['id_contrato'].'\',\''.$row['id_empleado'].'\',\''.$row['nombre_empleado'].'\')">
            //                     <img class="capturaImgCheck" id="imgEmail_'.$row['id_contrato'].'" src="img/enviaremail_'.$row['email_enviado'].'.png" title="'.$titleImg.'">
            //                 </div>';
            // $btnPrint='<div  class="iconBuscar" style="margin-left: -1px;" onclick="imprimirVolanteUnicoEmpleado(\''.$row['id_empleado'].'\')">
            //                     <img class="capturaImgCheck" id="imgPrint_'.$row['id_contrato'].'" src="img/printer.png" title="Imprimir Volante de nomina">
            //                 </div>';
        }
        $titleImg=($row['verificado']=='true')? 'Empleado Verificado' : 'Empleado no Verificado' ;
        $bodyEmpleados.='<div class="bodyDivNominaPlanilla" >
                            <div class="campo" id="divLoadEmpleado_'.$row['id_contrato'].'">'.$cont.'</div>
                            <div class="campo" style="margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;" id="fila_selected_'.$row['id_contrato'].'"><img src="img/fila_selected.png"></div>
                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:100px;text-indent:5px;">'.$row['documento_empleado'].'</div>
                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:calc(100% - 100px - 49px - 40px);text-indent:5px;">'.$row['nombre_empleado'].'</div>
                            <div title="'.$titleImg.'" class="iconBuscar" style="margin-left: -1px;" >
                                <img class="capturaImgCheck" src="img/checkbox_'.$row['verificado'].'.png" value="'.$row['verificado'].'" id="verifica_empleado_'.$row['id_contrato'].'">
                            </div>

                        </div>';
        $cont++;
    }


    if ($estado==1) {
        $acumScript.='Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();
                      Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();
                      Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").enable();
                      Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();';
    }
    else if ($estado==3) {
        $acumScript.='Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();
                        Ext.getCmp("Btn_exportar_'.$opcGrillaContable.'").disable();
                        Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();';
    }

    $acumScript .= (user_permisos(101,'false') == 'false')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(102,'false') == 'false')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();'   : '';
    $acumScript .= (user_permisos(103,'false') == 'false')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();' : '';
    $acumScript .= (user_permisos(104,'false') == 'false')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();' : '';

    $acumScript.='contEmpleados='.$cont;

?>
<style>

#inputBuscarGrillaManual:focus{
      background-image: url(../../temas/clasico/images/BotonesTabs/buscar16.png);
      background-repeat: no-repeat;
}

.iconBuscarEmpleado{
    height        : 33px;
    width         : 33px;
    border-bottom : 1px solid #D4D4D4;
    float         : right;
    text-align: center;
}

.iconBuscarEmpleado img{
    margin-top: 7px;
    cursor: hand;
}

.labelCabecera{
    float         : left;
    border-bottom : 1px solid #D4D4D4;
    width         : 186px;
    height        : 33px;
    font-weight   : bold;
    font-size     : 12px;
    line-height   : 30px;
    text-indent   : 15px;
    font-family   : sans-serif;
}

.div_input_busqueda_grilla_manual{float: left;border-top:none; border-left: none;}
.div_img_actualizar_datos_grilla_manual{ float: left;border-top:none;  height: 33px;border-bottom:1px solid #D4D4D4;}

    #divimgQuestion{
        float  : left;
        width  : 20px;
        height : 20px;
        margin : 2px 0 0 -20px;
        cursor: hand;
        position: relative;
    }

    #imgQuestion{
        width  : 100%;
        height : 100%;
        float  : left;
    }


    #divimgQuestion:hover ~ #divTitle{
        height     : 75px;
        width      : 157px;
        background : #d8e5f7;
        padding    : 5px;
        border     : 1px solid #000;
    }

    #divTitle{
        width              : 167px;
        height             : 0px;
        float              : right;
        position           : fixed;
        margin             : 5px 0px 0px 0px;
        background         : #d8e5f7;
        overflow           : hidden;
        -webkit-transition :all 0.5s ease;
        -moz-transition    :all 0.5s ease;
        -o-transition      :all 0.5s ease;
        transition         :all 0.5s ease;
    }

</style>
<div class="contenedorOrdenCompra" style="height:100%;" id="contenedor_<?php echo $opcGrillaContable; ?>">

    <!-- Campo Izquierdo -->
    <div class="bodyTop" style="background-color: #FFF;">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha<div id="divLoadfecha_documento<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop"><input type="text" id="fechaDoc<?php echo $opcGrillaContable; ?>"  readonly></div>
                </div>

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha Inicio<div id="divLoadfecha_inicio<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>"  readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Fecha Final<div id="divLoadfecha_final<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop" >
                        <input type="text" id="fechaFinal<?php echo $opcGrillaContable; ?>" readonly />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreVendedor<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">
                        Sucursal
                        <div id="divLoadTipoNomina" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div>
                    </div>
                    <div class="campoTop">
                        <input type="text" id="sucursal" readonly />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">
                        Saldo Inicial
                        <div style="float: right;width: 20px;height: 16px;margin-top: -2;" id="divimgQuestion">
                            <img id="imgQuestion" src="../../temas/clasico/images/BotonesTabs/help.png">
                        </div>
                        <div id="divTitle">La planilla saldo inicial no genera contabilidad, solo la referencia de la cuenta para cruzarla en otros documentos</div>
                        <div id="divLoadTipoPlanilla" style="float: right;width: 20px;height: 16px;margin-top: -2;overflow:hidden;"></div></div>
                    <div class="campoTop">
                        <input type="text" readonly value="<?php echo $saldo_inicial; ?>" />
                    </div>
                </div>

                <!-- <div class="renglonTop">
                    <div class="labelTop">Planilla de saldo Inicial <div id="divLoadTipoNomina" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop">
                        <input type="text" id="sucursal" readonly />
                    </div>
                </div> -->

            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>">
            <div class="contenedorEmpleados">
                <div class=" div_input_busqueda_grilla_manual" style="   ">
                    <input type="text" id="inputBuscarGrillaManual" placeholder="buscar..." onkeyup="buscarEmpleadoCargadoInput(event,this)">
                </div>
                <div class="div_img_actualizar_datos_grilla_manual" style="">
                    <img src="img/reload_grilla.png" onclick="buscarEmpleadoCargado();" title="Actualizar">
                </div>
                <div class="labelCabecera">
                    EMPLEADOS
                </div>

                <div class="DivEmpleados" id="contenedorEmpleados">
                    <?php echo $bodyEmpleados; ?>
                </div>
            </div>
            <div class="contenedorEmpleadosConceptos" >
                <!-- <div class="headEmpleadosConceptos">
                    Conceptos a pagar
                </div> -->

                <div class="DivEmpleados" id="contenedorPrincipalConceptos" style="height:100%;">
                </div>

            </div>

            <div class="contenedor_totales">
                <div class="contenedorObservacionGeneral" style="width:400px;">
                    <div style="padding:2px 0 0 3px;"><b>OBSERVACIONES</b></div>
                    <textarea id="observacion<?php echo $opcGrillaContable; ?>"  readonly ></textarea>
                </div>
                <div class="contenedorConceptos" style="height:auto;margin-top:10px;width:auto;">
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;">
                        <div class="campoHeadConceptos" style="text-align: center;background-color : #F3F3F3;border-right:none;width:calc(100% - 20px);">TOTAL PLANILLA DE CONSOLIDACION</div>
                        <div style="float:left;width:20px;height:22px;background-color : #F3F3F3;overflow:hidden;" id="divLoadTotalesPlanilla"></div>
                    </div>
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;border-bottom:none;">
                        <div class="campoHeadConceptos" style="width:110px;display:none;"><p style="float:left;"><img src="img/Devengo.png"></p><p style="float:left;padding: 0 5 0 5;">Devengo </p></div>
                        <div class="campo1" style="width:19px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
                        <div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalDevengoNomina"></div>
                    </div>
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;border-bottom:none;display:none;" >
                        <div class="campoHeadConceptos" style="width:110px;"><p style="float:left;"><img src="img/Deduccion.png"></p><p style="float:left;padding: 0 5 0 5;">Deduccion</p></div>
                        <div class="campo1" style="width:19px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
                        <div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalDeduccionNomina"></div>
                    </div>
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;display:none;">
                        <div class="campoHeadConceptos" style="width:110px;"><p style="float:left;"><img src="img/Apropiacion.png"></p><p style="float:left;padding: 0 5 0 5;">Apropiacion</p></div>
                        <div class="campo1" style="width:19px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
                        <div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalApropiacionNomina"></div>
                    </div>
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;border-bottom:none;display:none;">
                        <div class="campoHeadConceptos" style="width:110px;"><p style="float:left;"><img src="img/Provision.png"></p><p style="float:left;padding: 0 5 0 5;">Provision</p></div>
                        <div class="campo1" style="width:19px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
                        <div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalProvisionNomina"></div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

<div id="divLoadEnviarEmail" style="display:none;"></div>

<script>


    var observacion<?php echo $opcGrillaContable; ?> = '';
    var contEmpleados=1;
    var contConceptos=1;
    var fila_selected='';
    <?php echo $acumScript; ?>

    //CALCULAR LOS VALORES DE LA PLANILLA
    calcularValoresPlanilla();


    // Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();               //disable boton imprimir

    //====================== MOSTRAR LOS CONCEPTOS DE UN EMPLEADO DE LA PLANILLA ===========================//
    function cargarConceptosEmpleado(id_contrato,id_empleado,cont) {
        if (document.getElementById('fila_selected_'+fila_selected)) {
            document.getElementById('fila_selected_'+fila_selected).style.display='none';
        }

        document.getElementById('fila_selected_'+id_contrato).style.display='block';
        fila_selected=id_contrato;

        Ext.get('contenedorPrincipalConceptos').load({
            url     : 'consolidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            text    : 'Cargando Conceptos...',
            params  :
            {
                opc         : 'cargarConceptosEmpleado',
                id_contrato : id_contrato,
                id_empleado : id_empleado,
                id_planilla : '<?php echo $id_planilla ?>',
            }
        });
    }

    //==================  CALCULAR EL VALOR POR EMPLEADO ========================================//
    function calculaValoresEmpleado(valor,accion,naturaleza) {

        if (accion=='agregar') {
            if (naturaleza=='Devengo' || naturaleza=='Provision') {
                totalDevengoEmpleado+=valor;
            }
            if (naturaleza=='Deduccion') {
                totalDeduccionEmpleado+=valor;
            }
            if (naturaleza=='Apropiacion') {
                totalApropiacionEmpleado+=valor;
            }
            // if (naturaleza=='Provision') {
            //     totalProvisionEmpleado+=valor;
            // }

        }
        if (accion=='eliminar') {
            if (naturaleza=='Devengo' || naturaleza=='Provision') {
                totalDevengoEmpleado-=valor;
            }
            if (naturaleza=='Deduccion') {
                totalDeduccionEmpleado-=valor;
            }
            if (naturaleza=='Apropiacion') {
                totalApropiacionEmpleado-=valor;
            }
            // if (naturaleza=='Provision') {
            //     totalProvisionEmpleado-=valor;
            // }
        }
        totalNetoPagarEmpleado=totalDevengoEmpleado-totalDeduccionEmpleado;
        document.getElementById('totalDevengo').innerHTML     = '<p> $ '+formato_numero(totalDevengoEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'</p>';
        document.getElementById('totalDeduccion').innerHTML   = '<p> $ '+formato_numero(totalDeduccionEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'</p>';
        document.getElementById('totalApropiacion').innerHTML = '<p> $ '+formato_numero(totalApropiacionEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'</p>';
        document.getElementById('totalProvision').innerHTML   = '<p> $ '+formato_numero(totalProvisionEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'</p>';
        document.getElementById('totalNetoPagar').innerHTML   = '<p>'+formato_numero(totalNetoPagarEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'</p>';

    }

    function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06

        numero=parseFloat(numero);
        if(isNaN(numero)){ return ''; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); }  // Redondeamos

        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) { numero=numero.replace(miles, '$1' + separador_miles + '$2'); }
        }

        return numero;
    }

            //================= FUNCION PARA LA BUSQUEDA DEL EMPLEADO DESDE LA INTERFAZ =====================//
    function buscarEmpleadoCargadoInput(event,input){
        var tecla = input ? event.keyCode : event.which
        // ,   valor = input.value;

        if (tecla==13) { buscarEmpleadoCargado(); }

    }
    function buscarEmpleadoCargado(){
        document.getElementById('contenedorPrincipalConceptos').innerHTML='';
        var valor=document.getElementById('inputBuscarGrillaManual').value;
        var filtro=(valor=='')? '' : ' AND (documento_empleado LIKE "%'+valor+'%" OR nombre_empleado LIKE "%'+valor+'%") ' ;
        Ext.get('contenedorEmpleados').load({
            url     : 'consolidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'buscarEmpleadoCargado',
                id_planilla : '<?php echo $id_planilla; ?>',
                filtro      : filtro,
                estado      : '<?php echo $estado; ?>',
            }
        });
    }

    function ventana_libro_vacaciones(id_empleado,id_contrato) {

        Win_Ventana_libro_vacaciones = new Ext.Window({
            width       : 450,
            height      : 600,
            id          : 'Win_Ventana_libro_vacaciones',
            title       : 'Vacaciones del Empleado',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'consolidacion_provision/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc         : 'ventana_libro_vacaciones',
                    id_empleado : id_empleado,
                    id_contrato : id_contrato,
                    id_planilla : '<?php echo $id_planilla; ?>',
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
                            handler     : function(){ BloqBtn(this); Win_Ventana_libro_vacaciones.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    //////////////////////////////////////////////////
    /////////                               //////////
    /////////     FUNCIONES DE LA PLANILA   //////////
    /////////                               //////////
    //////////////////////////////////////////////////

    function calcularValoresPlanilla() {
        Ext.get('divLoadTotalesPlanilla').load({
            url     : 'consolidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'calcularValoresPlanilla',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function validarPlanillaNomina(){
        var inputs = document.getElementById('contenedorEmpleados').querySelectorAll('.capturaImgCheck');
        var cont = inputs.length;
        var contFaltantes = 0;
        var contListos = 0;
        var valorImg='';

        for (i = 0 ; i < cont; i++){
            valorImg=document.getElementById(inputs[i].id).getAttribute("src");

            if (valorImg=='img/checkbox_false.png') { contFaltantes++; }
            else if (valorImg=='img/checkbox_true.png'){ contListos++; }

        }

        // if (contFaltantes>0) {
        //     if (!confirm("Existen empleados sin verificar, desea continuar?")) { return; }
        //     guardarPlanilla();
        // }
        // else if (contListos<=0) { alert('No hay nada q guardar'); return; }
        // else{
        //     guardarPlanilla();
        // }

        if (contFaltantes>0) { return 1 ;}
        else if (contListos<=0) { return 2; }
        else { return 0; }

    }

    function nuevaPlanillaConsolidacionProvision(){}

    function cancelarPlanillaConsolidacionProvision(){
        var validaPlanilla=validarPlanillaNomina();
        if (validaPlanilla==2) {return;}

        if (!confirm("Realmente desea cancelar la planilla?\nSe eliminaran los asientos generados\nContinuar?")) { return; }
        cargando_documentos('Cancelado Planilla de<br> Consolidacion de Provision...','');
        Ext.get('render_btns_PlanillaConsolidacionProvision').load({
            url     : 'consolidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'cancelarPlanillaNomina',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function imprimirPlanillaConsolidacionProvision(){
        window.open("consolidacion_provision/bd/imprimirPlanilla.php?id_planilla=<?php echo $id_planilla; ?>");
    }

    function modificarDocumentoPlanillaConsolidacionProvision(){
        cargando_documentos('Editando Planilla de<br> Consolidacion de Provision...','');
        Ext.get('render_btns_PlanillaConsolidacionProvision').load({
            url     : 'consolidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'modificarDocumentoPlanillaNomina',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function cerrarPlanilla(){
        Win_ventana_planilla.close();
        Actualiza_Div_nomina_planillas_consolidacion_provision(<?php echo $id_planilla ?>);
    }

    function restaurarPlanillaConsolidacionProvision(){
        cargando_documentos('Restaurando Planilla de<br> Consolidacion de Provision...','');
        Ext.get('render_btns_PlanillaConsolidacionProvision').load({
            url     : 'consolidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'restaurarPlanillaNomina',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function enviarPlanillaConsolidacionProvision(){
        var contenido = '<div id="modal">'+
                            '<div id="experiment" style="position:static !important;">'+
                                '<div style="text-align:center;"><img style="cursor:pointer;" src="img/email.gif"></div>'+
                                '<div id="LabelCargando">Enviando Volantes de Liquidacion...</div>'+
                            '</div>'+
                        '</div>';

        var div=document.createElement('div');
        div.setAttribute('id','divPadreModal');
        div.setAttribute('class','fondo_modal');
        div.innerHTML=contenido;
        document.body.appendChild(div);
        // return;
        Ext.get('divLoadEnviarEmail').load({
            url     : 'consolidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'enviarPlanillaNomina',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function enviarVolanteUnicoEmpleado(id_contrato,id_empleado,nombre_empleado) {
        var contenido = '<div id="modal">'+
                            '<div id="experiment" style="position:static !important;">'+
                                '<div style="text-align:center;"><img style="cursor:pointer;" src="img/email.gif"></div>'+
                                '<div id="LabelCargando">Enviando Volante de Liquidacion a <br>'+nombre_empleado+'...</div>'+
                            '</div>'+
                        '</div>';

        var div=document.createElement('div');
        div.setAttribute('id','divPadreModal');
        div.setAttribute('class','fondo_modal');
        div.innerHTML=contenido;
        document.body.appendChild(div);
        // return;
        Ext.get('divLoadEnviarEmail').load({
            url     : 'consolidacion_provision/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'enviarVolanteUnicoEmpleado',
                id_planilla : '<?php echo $id_planilla; ?>',
                id_contrato : id_contrato,
                id_empleado : id_empleado,
            }
        });
    }

    function imprimirVolanteUnicoEmpleado(id_empleado) {
       window.open('consolidacion_provision/bd/imprimirVolanteEmpleado.php?id_planilla=<?php echo $id_planilla; ?>&id_empleado='+id_empleado);
    }


</script>
