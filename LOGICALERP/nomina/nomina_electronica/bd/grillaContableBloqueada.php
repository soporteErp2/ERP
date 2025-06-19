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
    //VALIDAR  LOS PERMISOS SOBRE EL DOCUMENTO

    // echo $id_planilla;
?>
<script>

    //variables para calcular los valores de los costos y totales de la factura

    var observacion<?php echo $opcGrillaContable; ?>        = ''
    ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
    ,   timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   timeOutObservacionEmpleado<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
    ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

    //VARIABLES DE LOS TOTALES POR EMPLEADO
    var totalDevengoEmpleado     = 0
    ,   totalDeduccionEmpleado   = 0
    ,   totalApropiacionEmpleado = 0
    ,   totalProvisionEmpleado   = 0
    ,   totalNetoPagarEmpleado   = 0

    //Bloqueo todos los botones

    // Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    // Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    // Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    // Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();

</script>
<?php
    // PERMISOS DEL DOCUMENTO
    // $acumScript .= (user_permisos(159,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';
    // $acumScript .= (user_permisos(160,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();'   : '';
    // $acumScript .= (user_permisos(161,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';
    // $acumScript .= (user_permisos(162,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';

    //CONSULTAR LOS TIPOS DE PAGO DE NOMINA
    $sql="SELECT * FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=$id_empresa";
    $query=mysql_query($sql,$link);
    $dias_liquidacion='';
    $tipo_liquidacion='<select id="id_tipo_liquidacion"  disabled>
                        <option value="">Seleccione...</option>';
    while ($row=mysql_fetch_array($query)) {
        $dias_liquidacion=($dias_liquidacion=='')? $row['dias'] : $dias_liquidacion ;
        $tipo_liquidacion.='<option value="'.$row['id'].'">'.$row['nombre'].' ('.$row['dias'].' dias) </option>';
    }
    $tipo_liquidacion.='</select>';



    $sql="SELECT codigo,tipo,detalle FROM nomina_configuracion_tipo_documentos WHERE activo=1 AND id_empresa=$id_empresa";
    $query=mysql_query($sql,$link);
    $tipo_documento='<select id="id_tipo_documento" disabled>
                        <option value="">Seleccione...</option>';
    while ($row=mysql_fetch_array($query)) {
        $tipo_documento.='<option value="'.$row['codigo'].'" title="'.$row['detalle'].'" >'.$row['tipo'].'  </option>';
    }
    $tipo_documento.='</select>';



    // CONSULTAR LAS SUCURSALES DE LA EMPRESA, POR PERMISO
    $MSucursales = user_permisos(1);
    if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
    if($MSucursales == 'true'){ $filtroS = ""; }

    $sql="SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa $filtroS";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)){
        $sucursales.='<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
    }


    $sql   = "SELECT consecutivo,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha_inicio,
                    date_format(fecha_final,'%Y-%m-%d') AS fecha_final,
                    fecha_documento,
                    id_tipo_liquidacion,
                    codigo_tipo_documento,
                    usuario,
                    estado,
                    id_sucursal
                FROM $tablaPrincipal
                WHERE id='$id_planilla' AND activo = 1";
    $query = mysql_query($sql,$link);

    $fechaDoc              = mysql_result($query,0,'fecha_documento');
    $fecha                 = mysql_result($query,0,'fecha_inicio');
    $fechaFin              = mysql_result($query,0,'fecha_final');
    $estado                = mysql_result($query,0,'estado');
    $id_tipo_liquidacion   = mysql_result($query,0,'id_tipo_liquidacion');
    $codigo_tipo_documento = mysql_result($query,0,'codigo_tipo_documento');
    $nombre_vendedor       = mysql_result($query,0,'usuario');
    $consecutivo           = mysql_result($query,0,'consecutivo');
    $id_sucursal           = mysql_result($query,0,'id_sucursal');

    if ($consecutivo!='') {
        $acumScript.='document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Planilla electronica <br>N. '.$consecutivo.'";';
    }


    if ($estado=='2') { echo "DOCUMENTO BLOQUEADO "; exit; }

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .=  '
                    document.getElementById("fecha'.$opcGrillaContable.'").value       = "'.$fechaDoc.'";
                    document.getElementById("fechaInicio'.$opcGrillaContable.'").value          = "'.$fecha.'";
                    document.getElementById("fechaFinal'.$opcGrillaContable.'").value     = "'.$fechaFin.'";
                    document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$nombre_vendedor.'";
                    document.getElementById("id_tipo_liquidacion").value = "'.$id_tipo_liquidacion.'";
                    document.getElementById("id_tipo_documento").value = "'.$codigo_tipo_documento.'";
                    //document.getElementById("observacion'.$opcGrillaContable.'").value = "'.$observacion.'";
                    document.getElementById("id_sucursal").value="'.$id_sucursal.'";
                    //observacion'.$opcGrillaContable.'   = "'.$observacion.'";';

    //======================= CONSULTAR LOS EMPLEADOS CARGADOS EN LA PLANILLA ==================================//
    $sql="SELECT 
            id_empleado,
            documento_empleado,
            nombre_empleado,
            id_contrato,
            response_NE 
        FROM nomina_planillas_electronica_empleados 
        WHERE activo=1 
        AND id_empresa=$id_empresa 
        AND id_planilla=$id_planilla";
    $query=mysql_query($sql,$link);
    $bodyEmpleados='';
    $cont=1;
    while ($row=mysql_fetch_array($query)) {
        $response = $row['response_NE'];
        if((strpos($response, 'Comprobante fue generado') !== FALSE) || (strpos($response, 'Documento no enviado, Ya cuenta con') !== FALSE)){

            $sendBtn = '<div title="Planilla enviada a la dian no se puede realizar ninguna accion" class="iconBuscar" style="margin-left: -1px;">
                        <img src="img/estado_doc/2.png">
                        </div>';
            
        }
        else{
            $sendBtn =      '<div onclick="envioDian('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')"  title="Enviar a la Dian" class="iconBuscar" style="margin-left: -1px;" >
                                <img class="capturaImgCheck" src="../../../temas/clasico/images/BotonesTabs/enviar_doc.jpg" value="'.$row['verificado'].'" id="verifica_empleado_'.$row['id_contrato'].'">
                            </div>
                            <div onclick="exportar_json('.$row['id_contrato'].','.$row['id_empleado'].')" title="Exportar Json" class="iconBuscar" style="margin-left: -1px;">
                                <img src="../../../temas/clasico/images/BotonesTabs/page_gear.png">
                            </div>'; 

        }

        $bodyEmpleados.='<div class="bodyDivNominaPlanilla claseBuscar" >
                            <div class="campo" id="divLoadEmpleado_'.$row['id_contrato'].'">'.$cont.'</div>
                            <div class="campo" style="margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;" id="fila_selected_'.$row['id_contrato'].'"><img src="img/fila_selected.png"></div>
                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:100px;text-indent:5px;">'.$row['documento_empleado'].'</div>
                            <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:calc(100% - 100px - 49px - 20px);text-indent:5px;">'.$row['nombre_empleado'].'</div>
                            '.$sendBtn.'
                        </div>';
        $cont++;
    }

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
        border-left : 1px solid #D4D4D4;
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
        width         : 153px;
        height        : 33px;
        font-weight   : bold;
        font-size     : 12px;
        line-height   : 30px;
        text-indent   : 15px;
        font-family   : sans-serif;
    }

    .div_input_busqueda_grilla_manual{float: left;border-top:none;border-left:none;}
    .div_img_actualizar_datos_grilla_manual{ float: left;border-top:none;  height: 33px;border-bottom:1px solid #D4D4D4;}


</style>
<div class="contenedorOrdenCompra" style="height:100%;" id="contenedor_PlanillaLiquidacion">

    <!-- Campo Izquierdo -->
    <div class="bodyTop" style="background-color: #FFF;">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha Documento<div id="divLoadfecha_documento<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>"  readonly></div>
                </div>
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha Inicio<div id="divLoadfecha_inicio<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop"><input type="text" id="fechaInicio<?php echo $opcGrillaContable; ?>"  readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Fecha Final<div id="divLoadfecha_final<?php echo $opcGrillaContable; ?>" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop" >
                        <input type="text" id="fechaFinal<?php echo $opcGrillaContable; ?>" />
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tipo Pago <div id="divLoadTipoNomina" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop">
                        <?php echo $tipo_liquidacion; ?>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tipo Documento <div id="divLoadTipoDocumento" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop">
                        <?php echo $tipo_documento; ?>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreVendedor<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Sucursal<div id="divLoadSucursal" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop" >
                        <select id="id_sucursal" disabled>
                            <?php echo $sucursales; ?>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>">
            <div class="contenedorEmpleados">
                <!--<div class="iconBuscarEmpleado" onclick="ventanaBuscarEmpleados()">
                    <img src="img/buscar.png" title="Buscar empleado">
                </div>


                 <div class=" div_input_busqueda_grilla_manual" style="">
                    <input type="text" id="inputBuscarGrillaManual" placeholder="buscar..." onkeyup="buscarEmpleadoCargadoInput(event,this)" style="width: 90%;">
                </div>
                <div class="div_img_actualizar_datos_grilla_manual" style="">
                    <img src="img/reload_grilla.png" onclick="buscarEmpleadoCargado();" title="Actualizar">
                </div> -->
                <div class="labelCabecera">
                    EMPLEADOS
                </div>

                <div class="DivEmpleados" id="contenedorEmpleados">
                    <?php echo $bodyEmpleados; ?>
                </div>
            </div>
            <div class="contenedorEmpleadosConceptos" >

                <div class="DivEmpleados" id="contenedorPrincipalConceptos" style="height:100%;">
                </div>
            </div>
            <div class="contenedor_totales">
                <div class="contenedorObservacionGeneral" style="width:400px;">
                </div>
                <div class="contenedorConceptos" style="height:auto;margin-top:10px;width:auto; display: none;">
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;">
                        <div class="campoHeadConceptos" style="text-align: center;background-color : #F3F3F3;border-right:none;width:calc(100% - 20px);">TOTAL DOCUMENTO</div>
                        <div style="float:left;width:20px;height:22px;background-color : #F3F3F3;overflow:hidden;" id="divLoadTotalesPlanilla"></div>
                    </div>
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;">
                        <div class="campoHeadConceptos" style="width:110px;"><p style="float:left;"><img src="img/Devengo.png"></p><p style="float:left;padding: 0 5 0 5;">Devengo </p></div>
                        <div class="campo1" style="width:19px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
                        <div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalDevengoNomina"></div>
                    </div>
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;border-bottom:none;" >
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

<script>

    var contEmpleados=1;
    var contConceptos=1;
    var fila_selected='';
    <?php echo $acumScript; ?>

    //CALCULAR LOS VALORES DE LA PLANILLA
    // calcularValoresPlanilla();

    function envioDian(id_contrato,id_empleado,cont) {
        if (!confirm("Realmente quiere enviar este empleado a la dian? este proceso no se puede reversar")) { return }

        MyLoading2("on");
        Ext.get('divLoadEmpleado_'+id_contrato).load({
            url     : 'nomina_electronica/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'generate',
                id_contrato : id_contrato,
                id_empleado : id_empleado,
                id_planilla : '<?php echo $id_planilla; ?>',
                cont        : cont,
            }
        });

        
        // body...
    }

    function exportar_json(id_contrato,id_empleado,cont) {
        let jsonPage = window.open(`nomina_electronica/bd/bd.php?opc=generate&id_contrato=${id_contrato}&id_empleado=${id_empleado}&id_planilla=<?php echo $id_planilla; ?>&cont=${cont}&view=true`,'_blank');
        jsonPage.focus();
    }

    //====================== MOSTRAR LOS CONCEPTOS DE UN EMPLEADO DE LA PLANILLA ===========================//
    function cargarConceptosEmpleado(id_contrato,id_empleado,cont) {
        if (document.getElementById('fila_selected_'+fila_selected)) {
            document.getElementById('fila_selected_'+fila_selected).style.display='none';
        }

        document.getElementById('fila_selected_'+id_contrato).style.display='block';
        fila_selected=id_contrato;

        Ext.get('contenedorPrincipalConceptos').load({
            url     : 'nomina_electronica/bd/bd.php',
            scripts : true,
            nocache : true,
            text    : 'Cargando Conceptos...',
            params  :
            {
                opc         : 'showEmployeeConcepts',
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
        document.getElementById('totalDevengo').innerHTML     = ' $ '+formato_numero(totalDevengoEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'';
        document.getElementById('totalDeduccion').innerHTML   = ' $ '+formato_numero(totalDeduccionEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'';
        document.getElementById('totalApropiacion').innerHTML = ' $ '+formato_numero(totalApropiacionEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'';
        document.getElementById('totalProvision').innerHTML   = ' $ '+formato_numero(totalProvisionEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'';
        document.getElementById('totalNetoPagar').innerHTML   = ''+formato_numero(totalNetoPagarEmpleado,"<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'';
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
            url     : 'nomina_electronica/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'buscarEmpleadoCargado',
                id_planilla : '<?php echo $id_planilla; ?>',
                filtro      : filtro,
                estado      : '0',
            }
        });
    }

    function ventana_fechas_pago(id_empleado) {
        Win_Ventana_fechas_pago = new Ext.Window({
            width       : 350,
            height      : 350,
            id          : 'Win_Ventana_fechas_pago',
            title       : 'Vacaciones del Empleado',
            modal       : true,
            autoScroll  : false,
            closable    : true,
            autoDestroy : true,
            bodyStyle   : 'background-color:#FFF;',
            autoLoad    :
            {
                url     : 'nomina_electronica/bd/grillaFechasPago.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    id_empleado : id_empleado,
                    id_planilla : '<?php echo $id_planilla; ?>',
                }
            }

        }).show();
    }


    // ======= CARGAR LAS VACACIONES ====== //


    //////////////////////////////////////////////////
    /////////                               //////////
    /////////     FUNCIONES DE LA PLANILA   //////////
    /////////                               //////////
    //////////////////////////////////////////////////
    // calcularValoresPlanilla();
    function calcularValoresPlanilla() {
        Ext.get('divLoadTotalesPlanilla').load({
            url     : 'nomina_electronica/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'calcularValoresPlanilla',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }


    function cerrarPlanilla(opc){
        Win_ventana_planilla.close();
        if (opc=='delete') { Elimina_Div_nomina_planillas_electronica(<?php echo $id_planilla ?>); }
        else{ Actualiza_Div_nomina_planillas_electronica(<?php echo $id_planilla ?>); }
    }

    function editarPlanilla_nomina_electronica(){

        if (!confirm("Realmente desea editar la planilla?\nSolo podra modificar empleados que no se hallan enviado a la dian")) { return; }
        MyLoading2("on",{texto:"Editando documento"});
        Ext.get('render_btns_<?= $opcGrillaContable; ?>').load({
            url     : 'nomina_electronica/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'editElectronicPayroll',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function cancelarPlanilla_nomina_electronica(){

        if (!confirm("Realmente desea cancelar la planilla?\nEste documento no se puede restaurar\nContinuar?")) { return; }
        cargando_documentos('Eliminando Planilla electronica...','');
        Ext.get('render_btns_<?= $opcGrillaContable; ?>').load({
            url     : 'nomina_electronica/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'deleteElectronicPayroll',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function imprimirPlanillaLiquidacion(){}



</script>
