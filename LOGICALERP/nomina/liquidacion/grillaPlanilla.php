<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../funciones_globales/funciones_php/randomico.php");
    include("config_var_global.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $id_usuario  = $_SESSION['IDUSUARIO'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');
    $exento_iva  = '';

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

    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();

</script>
<?php
    // PERMISOS DEL DOCUMENTO
    $acumScript .= (user_permisos(159,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';
    $acumScript .= (user_permisos(160,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();'   : '';
    $acumScript .= (user_permisos(161,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';
    $acumScript .= (user_permisos(162,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';

    //CONSULTAR LOS TIPOS DE PAGO DE NOMINA
    $sql="SELECT * FROM nomina_tipos_liquidacion WHERE activo=1 AND id_empresa=$id_empresa";
    $query=mysql_query($sql,$link);
    $dias_liquidacion='';
    $tipo_liquidacion='<select id="id_tipo_liquidacion" onchange="UpdateTipoLiquidacion(this.value)">';
    while ($row=mysql_fetch_array($query)) {
        $dias_liquidacion=($dias_liquidacion=='')? $row['dias'] : $dias_liquidacion ;
        $tipo_liquidacion.='<option value="'.$row['id'].'">'.$row['nombre'].' ('.$row['dias'].' dias) </option>';
    }
    $tipo_liquidacion.='</select>';

    // $acumScript .= (user_permisos(11,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    // $acumScript .= (user_permisos(13,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';       //cancelar

    // CONSULTAR LAS SUCURSALES DE LA EMPRESA, POR PERMISO
    $MSucursales = user_permisos(1);
    if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
    if($MSucursales == 'true'){ $filtroS = ""; }

    $sql="SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa $filtroS";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)){
        $sucursales.='<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
    }

    if($id_planilla==''){

        // CREACION DEL ID UNICO
        $random_factura = responseUnicoRanomico();

        $fecha_inicio=date("Y-m-d", strtotime ("-".$dias_liquidacion."days"));
        $fecha_final=date("Y-m-d");

        $sqlInsert   ="INSERT INTO $tablaPrincipal (id_empresa,id_sucursal,random,fecha_documento,fecha_inicio,fecha_final,id_usuario,usuario)
                    VALUES('$id_empresa','$id_sucursal','$random_factura','$fecha_final','$fecha_inicio','$fecha_final','".$_SESSION['CEDULAFUNCIONARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId      = "SELECT id FROM $tablaPrincipal  WHERE random='$random_factura' LIMIT 0,1";
        $id_planilla = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .=  'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha_inicio.'",
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_inicio"); } }
                        });
                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFinal'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha_final.'",
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_final"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaDoc'.$opcGrillaContable.'",
                            editable   : false,
                            value      : "'.$fecha_final.'",
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_final"); } }
                        });

                        document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";
                        document.getElementById("id_sucursal").value="'.$id_sucursal.'";
                        Inserta_Div_nomina_planillas_liquidacion('.$id_planilla.');';
    }
    //============================ SI EXISTE LA PLANILLA =============================//
    else{
        // ACTUALIZAR LA SUCURSAL DE LA PLANILLA


        $sql   = "SELECT consecutivo,
                        date_format(fecha_inicio,'%Y-%m-%d') AS fecha_inicio,
                        date_format(fecha_final,'%Y-%m-%d') AS fecha_final,
                        observacion,
                        fecha_documento,
                        usuario,
                        estado,
                        id_tipo_liquidacion,
                        id_sucursal
                    FROM $tablaPrincipal
                    WHERE id='$id_planilla' AND activo = 1";
        $query = mysql_query($sql,$link);

        $fechaDoc            = mysql_result($query,0,'fecha_documento');
        $fecha               = mysql_result($query,0,'fecha_inicio');
        $fechaFin            = mysql_result($query,0,'fecha_final');
        $estado              = mysql_result($query,0,'estado');
        $nombre_vendedor     = mysql_result($query,0,'usuario');
        $consecutivo         = mysql_result($query,0,'consecutivo');
        $id_tipo_liquidacion = mysql_result($query,0,'id_tipo_liquidacion');
        $id_sucursal         = mysql_result($query,0,'id_sucursal');

        if ($consecutivo!='') {
            $acumScript.='document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Planilla de Liquidacion <br>N. '.$consecutivo.'";';
        }


        if ($estado=='2') { echo "ESTA PLANILLA DE LIQUIDACION ESTA CERRADA "; exit; }

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_inicio"); } }
                        });
                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaFinal'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_final"); } }
                        });

                        new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaDoc'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value,"fecha_documento"); } }
                        });';


        $acumScript .=  '
                        document.getElementById("fechaDoc'.$opcGrillaContable.'").value       = "'.$fechaDoc.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha.'";
                        document.getElementById("fechaFinal'.$opcGrillaContable.'").value     = "'.$fechaFin.'";
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$nombre_vendedor.'";
                        document.getElementById("id_tipo_liquidacion").value = "'.$id_tipo_liquidacion.'";
                        document.getElementById("observacion'.$opcGrillaContable.'").value = "'.$observacion.'";
                        document.getElementById("id_sucursal").value="'.$id_sucursal.'";
                        observacion'.$opcGrillaContable.'   = "'.$observacion.'";';

        //======================= CONSULTAR LOS EMPLEADOS CARGADOS EN LA PLANILLA ==================================//
        $sql="SELECT id_empleado,documento_empleado,nombre_empleado,id_contrato,verificado FROM nomina_planillas_liquidacion_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id_planilla=$id_planilla";
        $query=mysql_query($sql,$link);
        $bodyEmpleados='';
        $cont=1;
        while ($row=mysql_fetch_array($query)) {
            $bodyEmpleados.='<div class="bodyDivNominaPlanilla claseBuscar" >
                                <div class="campo" id="divLoadEmpleado_'.$row['id_contrato'].'">'.$cont.'</div>
                                <div class="campo" style="margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;" id="fila_selected_'.$row['id_contrato'].'"><img src="img/fila_selected.png"></div>
                                <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:100px;text-indent:5px;">'.$row['documento_empleado'].'</div>
                                <div class="campo1" onclick="cargarConceptosEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')" style="width:calc(100% - 100px - 49px - 20px);text-indent:5px;">'.$row['nombre_empleado'].'</div>
                                <div onclick="verificaEmpleado('.$row['id_contrato'].','.$row['id_empleado'].','.$cont.')"  title="Verificar Empleado" class="iconBuscar" style="margin-left: -1px;" >
                                    <img class="capturaImgCheck" src="img/checkbox_'.$row['verificado'].'.png" value="'.$row['verificado'].'" id="verifica_empleado_'.$row['id_contrato'].'">
                                </div>
                                <div onclick="eliminarEmpleado('.$row['id_contrato'].','.$row['id_empleado'].')" title="Eliminar Empleado" class="iconBuscar" style="margin-left: -1px;">
                                    <img src="img/delete.png">
                                </div>
                            </div>';
            $cont++;
        }

        $acumScript.='contEmpleados='.$cont;

    }



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
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreVendedor<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Sucursal<div id="divLoadSucursal" style="float: right;width: 20px;height: 16px;margin-top: -2;"></div></div>
                    <div class="campoTop" >
                        <select id="id_sucursal" onchange="UpdateSucursal<?php echo $opcGrillaContable; ?>()">
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
                <div class="iconBuscarEmpleado" onclick="ventanaBuscarEmpleados()">
                    <img src="img/buscar.png" title="Buscar empleado">
                </div>


                <div class=" div_input_busqueda_grilla_manual" style="">
                    <input type="text" id="inputBuscarGrillaManual" placeholder="buscar..." onkeyup="buscarEmpleadoCargadoInput(event,this)" style="width: 90%;">
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

                <div class="DivEmpleados" id="contenedorPrincipalConceptos" style="height:100%;">
                </div>
            </div>
            <div class="contenedor_totales">
                <div class="contenedorObservacionGeneral" style="width:400px;">
                    <div style="padding:2px 0 0 3px;" id="labelObservacionPlanilla"><b>OBSERVACIONES</b></div>
                    <textarea id="observacion<?php echo $opcGrillaContable; ?>"  onKeydown="inputObservacion<?php echo $opcGrillaContable; ?>(event,this)"></textarea>
                </div>
                <div class="contenedorConceptos" style="height:auto;margin-top:10px;width:auto;">
                    <div class="bodyDivNominaPlanilla" style="background-color:#FFF;">
                        <div class="campoHeadConceptos" style="text-align: center;background-color : #F3F3F3;border-right:none;width:calc(100% - 20px);">TOTAL PLANILLA DE LIQUIDACION</div>
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

    var observacion<?php echo $opcGrillaContable; ?> = '';
    var contEmpleados=1;
    var contConceptos=1;
    var fila_selected='';
    <?php echo $acumScript; ?>

    //CALCULAR LOS VALORES DE LA PLANILLA
    calcularValoresPlanilla();


    // Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();               //disable boton imprimir

    function UpdateFechaFinContrato(fecha,id_empleado) {

        Ext.get('divLoadFechaFinContrato').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'UpdateFechaFinContrato',
                fecha       : fecha,
                id_planilla : '<?php echo $id_planilla ?>',
                id_empleado : id_empleado,
            }
        });
    }

    //================ FUNCION PARA ACTUALIZAR LAS FECHAS DE LA PLANILLA ======================//
    function UpdateFecha<?php echo $opcGrillaContable; ?>(fecha,campo){
        var empleados = document.querySelectorAll('.claseBuscar');

        // console.log(campo+'--');

        if (empleados.length>0) {
            if (campo!='fecha_documento') {
                if (!confirm("Hay empleados guardados en esta planilla, si cambia la fecha se eliminaran\nDesea continuar?")) {
                    var id=(campo=='fecha_inicio')? 'fecha<?php echo $opcGrillaContable; ?>' : 'fechaFinal<?php echo $opcGrillaContable; ?>' ;
                    // document.getElementById(id).value=document.getElementById(id).originalValue;
                    return;
                }
            }
        }

        var divLoad='divLoad'+campo+'<?php echo $opcGrillaContable; ?>';
        // console.log(divLoad);return;
        Ext.get(divLoad).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                opc               : 'guardarFechaPlanilla',
                fecha             : fecha,
                campo             : campo,
                id_planilla       : '<?php echo $id_planilla ?>',

            }
        });
    }

    //================ FUNCION PARA ACTUALIZAR LAS FECHAS DE LA PLANILLA ======================//
    function UpdateSucursal<?php echo $opcGrillaContable; ?>(){
        // console.log(divLoad);return;
        Ext.get('divLoadSucursal').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                opc               : 'actualizarSucursal',
                id_planilla       : '<?php echo $id_planilla ?>',
                sucursal       : document.getElementById('id_sucursal').value,

            }
        });
    }

    UpdateTipoLiquidacion(document.getElementById('id_tipo_liquidacion').value);
    //==================== FUNCION PARA ACTUALIZAR EL TIPO DE PAGO DE LA NOMINA ==========================//
    function UpdateTipoLiquidacion(valor){
        Ext.get('divLoadTipoNomina').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarTipoLiquidacion',
                id_tipo_liquidacion : valor,
                id_planilla       : '<?php echo $id_planilla ?>',
            }
        });
    }

    //=========================== VENTANA BUSCAR LOS EMPLEADOS =================================//
    function ventanaBuscarEmpleados() {
        var sucursal=document.getElementById('id_sucursal').value;
        // console.log(sucursal);
        Win_Ventana_buscar_empleados = new Ext.Window({
            width       : 600,
            height      : 500,
            id          : 'Win_Ventana_buscar_empleados',
            title       : 'Empleados a liquidar',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
            items       :
            [
                {
                    closable    : false,
                    border      : false,
                    autoScroll  : true,
                    iconCls     : '',
                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                    items       :
                    [

                        {
                            xtype       : "panel",
                            id          : 'contenedor_buscar_empleado',
                            border      : false,
                            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
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
                                            url_render    : 'liquidacion/bd/grillaBuscarEmpleados.php',
                                            opc           : 'buscar_empleado',
                                            contenedor    : 'contenedor_buscar_empleado',
                                            imprimeVarPhp : 'cargaFuncion : "guardarEmpleado(id);",id_planilla  : "<?php echo $id_planilla; ?>",',
                                            script        : 'document.getElementById("filtro_sucursal_buscar_empleado").value="'+sucursal+'"; //console.log("document.getElementById(\\"filtro_sucursal_buscar_empleado\\").value=\\"'+sucursal+'\\";");',
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Seleccionar Todo',
                            scale       : 'large',
                            iconCls     : 'reunionadd',
                            iconAlign   : 'top',
                            handler     : function(){ cargarTodosEmpleados(); }
                        },
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_buscar_empleados.close(id) }
                        }
                    ]
                }
            ]

        }).show();
    }

    //=========================== AGREGAR UN EMPLEADO A LA PLANILLA =================================//
    function guardarEmpleado(id_contrato) {
        // console.log(id_contrato);
        var div = document.getElementById('MuestraToltip_buscarEmpleadosPlanilla_'+id_contrato);
        div.style.overflow='hidden';
        div.style.textIndent= '10px';
        var cont = div.innerHTML;
        Ext.get('MuestraToltip_buscarEmpleadosPlanilla_'+id_contrato).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            text    : '',
            params  :
            {
                opc         : 'agregarEmpleado',
                id_contrato : id_contrato,
                cont        : cont,
                id_planilla : '<?php echo $id_planilla; ?>',

            }
        });
    }

    //====================== ELIMINAR UN EMPLEADO DE LA PLANILLA =====================================//
    function eliminarEmpleado(id_contrato,id_empleado,opc) {

        if (opc!='true') {
            if (!confirm("Eliminar el empleado de la planilla?")) { return; }
        }

        var cont=document.getElementById('divLoadEmpleado_'+id_contrato).innerHTML;
        Ext.get('divLoadEmpleado_'+id_contrato).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'eliminarEmpleado',
                id_contrato : id_contrato,
                id_empleado : id_empleado,
                id_planilla : '<?php echo $id_planilla; ?>',
                cont        : cont,
            }
        });
    }

    //=================== FUNCION PARA HACER CHECK DE VERIFICACION SOBRE UN EMPLEADO ====================//
    function verificaEmpleado(id_contrato,id_empleado,cont) {
        // if (document.getElementById('fila_selected_'+fila_selected)) {
        //     document.getElementById('fila_selected_'+fila_selected).style.display='none';
        // }

        // document.getElementById('fila_selected_'+cont).style.display='block';
        // fila_selected=cont;

        var check = (document.getElementById('verifica_empleado_'+id_contrato).getAttribute('src')=='img/checkbox_true.png')? 'true' : 'false' ;

        var cont=document.getElementById('divLoadEmpleado_'+id_contrato).innerHTML;
        Ext.get('divLoadEmpleado_'+id_contrato).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'verificaEmpleado',
                id_contrato : id_contrato,
                id_empleado : id_empleado,
                id_planilla : '<?php echo $id_planilla; ?>',
                cont        : cont,
                check       : check,
            }
        });
    }

    //====================== CARGAR TODOS LOS EMPLEADOS A LA PLANILLA ===================================//
    function cargarTodosEmpleados(){
        Ext.get('contenedorEmpleados').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            text : 'Cargando empleados...',
            params  :
            {
                id_planilla : '<?php echo $id_planilla; ?>',
                opc : 'cargarTodosEmpleados',
                sucursal : document.getElementById('filtro_sucursal_buscar_empleado').value,
            }
        });
    }

    //====================== MOSTRAR LOS CONCEPTOS DE UN EMPLEADO DE LA PLANILLA ===========================//
    function cargarConceptosEmpleado(id_contrato,id_empleado,cont) {
        if (document.getElementById('fila_selected_'+fila_selected)) {
            document.getElementById('fila_selected_'+fila_selected).style.display='none';
        }

        document.getElementById('fila_selected_'+id_contrato).style.display='block';
        fila_selected=id_contrato;

        Ext.get('contenedorPrincipalConceptos').load({
            url     : 'liquidacion/bd/bd.php',
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

    //====================== VENTANA PARA BUSCAR LOS CONCEPTOS ========================================//
    function ventanaBuscarConceptos(cont) {

        var id_empleado = document.getElementById('id_empleado_concepto_'+cont).value;
        // var id_contrato = document.getElementById('id_contrato_concepto_'+cont).value;

         Win_Ventana_bucar_concepto = new Ext.Window({
             width       : 500,
             height      : 450,
             id          : 'Win_Ventana_bucar_concepto',
             title       : 'Seleccione un Concepto',
             modal       : true,
             autoScroll  : false,
             closable    : false,
             autoDestroy : true,
             autoLoad    :
             {
                 url     : 'liquidacion/bd/grillaBuscarConceptos.php',
                 scripts : true,
                 nocache : true,
                 params  :
                 {
                     cargaFuncion : 'rederizaResultadoVentanaConcepto(id,"'+cont+'")',
                     id_planilla  : '<?php echo $id_planilla; ?>',
                     id_empleado  : id_empleado,
                 }
             }
         }).show();
    }

    //============ RENDERIZAR EL CONCEPTO BUSCADO ============================//
    function rederizaResultadoVentanaConcepto(id,cont) {

        var concepto = document.getElementById('div_buscarConceptos_descripcion_'+id).innerHTML;
        var naturaleza =document.getElementById('naturaleza_concepto_'+id).value;
        // var imprimir =document.getElementById('imprimir_volante_concepto_'+id).value;
        var formula =document.getElementById('formula_'+id).value;

        // var titlePrint=(imprimir=='true')? 'Imprimible' : 'No imprimible' ;
        document.getElementById('id_concepto_'+cont).value=id;
        document.getElementById('concepto_'+cont).innerHTML=concepto;
        document.getElementById('naturaleza_'+cont).setAttribute('title',naturaleza);
        document.getElementById('naturaleza_'+cont).innerHTML='<img src="img/'+naturaleza+'.png" title="'+naturaleza+'">';
        // document.getElementById('imprimir_volante_'+cont).innerHTML='<img src="img/'+imprimir+'.png" title="'+titlePrint+'">';
        document.getElementById('formula_concepto_'+cont).value=formula;

        // if (formula!='') {
            var id_empleado = document.getElementById('id_empleado_concepto_'+cont).value;
            var id_contrato = document.getElementById('id_contrato_concepto_'+cont).value;
            // console.log("id_contrato: "+id_contrato);
            Ext.get('divLoadConcepto_'+cont).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                    opc            : 'calculaValorConceptoBuscado',
                    id_concepto    : id,
                    id_contrato    : id_contrato,
                    id_empleado    : id_empleado,
                    cont           : cont,
                    id_planilla    : '<?php echo $id_planilla; ?>',
                }
            });

        // }
        // else{
        //     Win_Ventana_bucar_concepto.close();
        // }
    }

    //========================== GUARADAR EL CONCEPTO ================================//
    function guardarConcepto(cont,opc) {
        var opc                    = (opc=='')? 'guardarConcepto' : 'actualizarconcepto' ;
        var id_insert              = document.getElementById('id_insert_concepto_'+cont).value;
        var id_concepto            = document.getElementById('id_concepto_'+cont).value;
        var valor_concepto         = document.getElementById('valor_concepto_'+cont).value;
        var id_contrato            = document.getElementById('id_contrato_concepto_'+cont).value;
        var id_empleado            = document.getElementById('id_empleado_concepto_'+cont).value;
        var input_calculo          = document.getElementById('input_calculo_'+cont).value;
        var nivel_formula_concepto = document.getElementById('nivel_formula_concepto_'+cont).value;
        var formula                = document.getElementById('formula_concepto_'+cont).value;
        var naturaleza             = document.getElementById('naturaleza_'+cont).getAttribute('title');
        var dias_adicionales       = (document.getElementById('dias_adicionales_'+cont) )? document.getElementById('dias_adicionales_'+cont).value : '' ;


        if (id_concepto=='') {alert('Seleccione un concepto!'); return;}
        if (valor_concepto=='' || valor_concepto<0) {alert('Digite el valor del concepto!'); return;}

        Ext.get('divLoadConcepto_'+cont).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                    : opc,
                id_concepto            : id_concepto,
                id_contrato            : id_contrato,
                id_empleado            : id_empleado,
                cont                   : cont,
                id_planilla            : '<?php echo $id_planilla; ?>',
                valor_concepto         : valor_concepto,
                naturaleza             : naturaleza,
                input_calculo          : input_calculo,
                formula                : formula,
                nivel_formula_concepto : nivel_formula_concepto,
                id_insert              : id_insert,
                dias_adicionales       : dias_adicionales
            }
        });
    }

    //======================= ELIMINAR UN CONCEPTO =====================================//
    function eliminarConcepto(cont){
        if (!confirm('Eliminar el Concepto?')) {return;}
        var id_concepto = document.getElementById('id_concepto_'+cont).value;
        var id_contrato = document.getElementById('id_contrato_concepto_'+cont).value;
        var id_empleado = document.getElementById('id_empleado_concepto_'+cont).value;
        var naturaleza  = document.getElementById('naturaleza_'+cont).getAttribute('title');
        var valor_concepto = document.getElementById('valor_concepto_'+cont).value;

        Ext.get('divLoadConcepto_'+cont).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc            : 'eliminarConcepto',
                id_concepto    : id_concepto,
                id_contrato    : id_contrato,
                id_empleado    : id_empleado,
                cont           : cont,
                id_planilla    : '<?php echo $id_planilla; ?>',
                naturaleza     : naturaleza,
                valor_concepto : valor_concepto,
            }
        });
    }

    // ======================= CALCULAR EL VALOR DEL CONCEPTO CON INPUT =====================//
    function calculaValorConceptoFormulaInput(id_empleado,id_contrato,id_concepto,cont,event,Input){
        id_insert_concepto=document.getElementById('id_insert_concepto_'+cont).value;
        if (id_insert_concepto>0) {
            document.getElementById('divImageSaveConcepto_'+cont).style.display='block';
        }

        tecla  = (Input) ? event.keyCode : event.which;
        var variable = document.getElementById('input_calculo_'+cont).value;
        if(tecla != 13 || variable=='' || variable==0){ return;}
        Ext.get('divLoadConcepto_'+cont).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                : 'calculaValorConceptoFormulaInput',
                id_concepto        : id_concepto,
                id_contrato        : id_contrato,
                id_empleado        : id_empleado,
                cont               : cont,
                id_planilla        : '<?php echo $id_planilla; ?>',
                variable           : variable,
                id_insert_concepto : id_insert_concepto,
            }
        });
    }

    //======================= CONFIGURAR LAS CUENTAS POR CONCEPTO ==========================//
    function ventanaConfigurarCuentasConcepto(cont){

        var id_concepto    = document.getElementById('id_concepto_'+cont).value;
        var id_contrato    = document.getElementById('id_contrato_concepto_'+cont).value;
        var id_empleado    = document.getElementById('id_empleado_concepto_'+cont).value;

        Win_Ventana_configurar_cuentas_conceptos = new Ext.Window({
            height      : 350,
            width       : 400,
            id          : 'Win_Ventana_configurar_cuentas_conceptos',
            title       : 'Configuracion Cuentas',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
            items       :
            [
                {
                    closable    : false,
                    border      : false,
                    autoScroll  : true,
                    iconCls     : '',
                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                    items       :
                    [

                        {
                            xtype       : "panel",
                            id          : 'contenedor_configuracion_cuentas',
                            border      : false,
                            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                            autoLoad :
                            {
                                url     : 'liquidacion/bd/bd.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    opc         : 'ventanaConfigurarCuentasConcepto',
                                    id_planilla : '<?php echo $id_planilla; ?>',
                                    id_concepto : id_concepto,
                                    id_contrato : id_contrato,
                                    id_empleado : id_empleado,
                                }
                            }
                        }
                    ],
                    tbar        :
                    [
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Actualizar',
                            scale       : 'large',
                            iconCls     : 'guardar',
                            iconAlign   : 'top',
                            handler     : function(){ updateCuentasConcepto(id_concepto,id_contrato,id_empleado) }
                        },
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_configurar_cuentas_conceptos.close(id) }
                        }
                    ]
                }
            ]

        }).show();
    }

    //================= BUSCAR LA CUENTA PARA EL CONCEPTO =====================//
    function ventanaBuscarCuentasConcepto(opc,campoId,campoText) {
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_cuenta_concepto = new Ext.Window({
            width       : 680,
            height      : 500,
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
                    nombreGrilla : 'buscar_cuenta_concepto',
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

    //==================== ASIGNAR LOS VALORES DE LA NUEVA CUENTA ===============================//
    function renderizaCuentaConcepto(id,campoId,campoText) {
        var cuenta = document.getElementById('div_buscar_cuenta_concepto_cuenta_'+id).innerHTML;
        document.getElementById(campoId).value=id;
        document.getElementById(campoText).innerHTML=cuenta;
        Win_Ventana_buscar_cuenta_concepto.close();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////
    //                         CONFIGURACION DE LOS CONCEPTOS A DEDUCIR                          //
    ///////////////////////////////////////////////////////////////////////////////////////////////

    //=================== CONFIGURAR LOS CONCEPTOS A DEDUCIR ====================================//
    function ventanaConfigurarConceptoDeduccion(cont,id_prestamo){


        Win_Ventana_configrar_concepto = new Ext.Window({
            width       : 500,
            height      : 400,
            id          : 'Win_Ventana_configrar_concepto',
            title       : 'Configurar la Deduccion',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'liquidacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc         : 'ventanaConfigurarConceptoDeduccion',
                    id_planilla : '<?php echo $id_planilla; ?>',
                    id_concepto : document.getElementById('id_concepto_'+cont).value,
                    id_empleado : document.getElementById('id_empleado_concepto_'+cont).value,
                    id_contrato : document.getElementById('id_contrato_concepto_'+cont).value,
                    cont        : cont,
                    id_prestamo : id_prestamo,
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
                            handler     : function(){ BloqBtn(this); Win_Ventana_configrar_concepto.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    //=================== BUSCAR LOS CONCEPTOS QUE SE VAN A DEDUCIR DEL PRESTAMO ================//
    function ventanaBuscarConceptosConceptoDeduccion(cont,id_empleado,id_contrato) {
        Win_Ventana_ventana_buscar_concepto = new Ext.Window({
                width       : 500,
                height      : 400,
                id          : 'Win_Ventana_ventana_buscar_concepto',
                title       : 'Conceptos',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'liquidacion/bd/buscar_concepto.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        cargaFuncion : 'responseVentanaBuscarConcepto(id,"'+cont+'")',
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
                                handler     : function(){ Win_Ventana_ventana_buscar_concepto.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
    }

    function responseVentanaBuscarConcepto(id,cont) {
            var nombre    = document.getElementById('div_grilla_buscar_conceptos_concepto_'+id).innerHTML;
            var id_buscar = document.getElementById('detalles_concepto_'+id).dataset.id;
            document.getElementById('id_concepto_deducir_'+cont).value  =id_buscar;
            document.getElementById('concepto_deducir_'+cont).innerHTML =nombre;
            Win_Ventana_ventana_buscar_concepto.close();
    }

    function guardarConceptoDeducir(cont_deducir,cont,id_empleado,id_contrato,id_concepto,id_prestamo){

        var id_concepto_deducir =document.getElementById('id_concepto_deducir_'+cont_deducir).value;
        var valor_deducir       = document.getElementById('valor_deducir_'+cont_deducir).value;

        if (id_concepto_deducir==0) {alert('El campo Concepto es obligatorio');return;}
        if (valor_deducir==0) {alert('El campo valor a descontar es obligatorio');return;}

        Ext.get('loadFila_'+cont_deducir).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                 : 'guardarConceptoDeducir',
                cont_deducir        : cont_deducir,
                cont                : cont,
                id_empleado         : id_empleado,
                id_concepto         : id_concepto,
                id_concepto_deducir : id_concepto_deducir,
                valor_deducir       : valor_deducir,
                id_planilla         : '<?php echo $id_planilla; ?>',
                id_contrato         : id_contrato,
                id_prestamo         : id_prestamo,
            }
        });
    }

    function eliminarConceptoDeducir(cont_deducir,id_empleado,id_contrato,id_concepto,id_prestamo) {
        var id_concepto_deducir =document.getElementById('id_concepto_deducir_'+cont_deducir).value;

        if (id_concepto_deducir==0) {alert('El campo Concepto es obligatorio');return;}

        Ext.get('loadFila_'+cont_deducir).load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                 : 'eliminarConceptoDeducir',
                cont_deducir        : cont_deducir,
                id_empleado         : id_empleado,
                id_concepto         : id_concepto,
                id_concepto_deducir : id_concepto_deducir,
                id_planilla         : '<?php echo $id_planilla; ?>',
                id_contrato         : id_contrato,
                id_prestamo         : id_prestamo,
            }
        });
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////

    //================ SINCRONIZAR LA CUENTA NIIF A APARTIR DE LA COLGAAP =======================//
    function sincronizarCuentaNiif(id_colgaap,campoId,campoText) {
        var id = document.getElementById(id_colgaap).value;

        Ext.get(id_colgaap+'_sincLoad').load({
            url     : 'liquidacion/bd/bd.php',
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

    //=================== ACTUALIZAR LAS CUENTAS DEL CONCEPTO ===================================//
    function updateCuentasConcepto(id_concepto,id_contrato,id_empleado) {
        var id_cuenta_colgaap               = document.getElementById('id_cuenta_colgaap').value;
        var id_cuenta_niif                  = document.getElementById('id_cuenta_niif').value;
        var id_cuenta_contrapartida_colgaap = document.getElementById('id_cuenta_contrapartida_colgaap').value;
        var id_cuenta_contrapartida_niif    = document.getElementById('id_cuenta_contrapartida_niif').value;

        MyLoading2('on');

        Ext.get('divLoadConfigCuentas').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                             : 'updateCuentasConcepto',
                id_cuenta_colgaap               : id_cuenta_colgaap,
                id_cuenta_niif                  : id_cuenta_niif,
                id_cuenta_contrapartida_colgaap : id_cuenta_contrapartida_colgaap,
                id_cuenta_contrapartida_niif    : id_cuenta_contrapartida_niif,
                id_concepto                     : id_concepto,
                id_contrato                     : id_contrato,
                id_empleado                     : id_empleado,
                id_planilla                     : '<?php echo $id_planilla; ?>',
            }
        });
    }

    //================== GUARDAR SI ES FINALIZACION DE CONTRATO =================================//
    function updateFinalizaContrato(terminar_contrato,id_empleado,id_contrato) {
        // if (document.getElementById('vacaciones').value=='Si') {
        //     alert("Aviso\nEl empleado solo puede terminar contrato o vacaciones, no las dos opciones");
        //     var select_fin_contrato = document.getElementById('terminar_contrato');
        //     select_fin_contrato.value=(select_fin_contrato.value=='Si')? 'No' : 'Si';
        //     return;
        // }

        Ext.get('divLoadFinalizaContrato').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'updateFinalizaContrato',
                terminar_contrato : terminar_contrato,
                id_empleado       : id_empleado,
                id_contrato       : id_contrato,
                id_planilla       : '<?php echo $id_planilla; ?>',
            }
        });
    }

    //================== GUARDAR SI ES FINALIZACION DE CONTRATO =================================//
    function updateVacaciones(vacaciones,id_empleado,id_contrato) {
        // if (document.getElementById('terminar_contrato').value=='Si') {
        //     alert("Aviso\nEl empleado solo puede terminar contrato o vacaciones, no las dos opciones");
        //     var select_vacaciones = document.getElementById('vacaciones');
        //     select_vacaciones.value=(select_vacaciones.value=='Si')? 'No' : 'Si';
        //     return;
        // }

        Ext.get('divLoadVacaciones').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'updateVacaciones',
                vacaciones  : vacaciones,
                id_empleado : id_empleado,
                id_contrato : id_contrato,
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    //================== GUARDAR EL MOTIVO DE FINALIZACION DEL CONTRATO ==========================//
    function updateMotivoFinContrato(id_motivo_finalizacion,id_empleado,id_contrato) {
        var motivo_finalizacion = document.getElementById('motivo_fin_contrato').options[document.getElementById('motivo_fin_contrato').selectedIndex].text;

        Ext.get('divLoadMotivoFinContrato').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                    : 'updateMotivoFinContrato',
                id_motivo_finalizacion : id_motivo_finalizacion,
                motivo_finalizacion    : motivo_finalizacion,
                id_empleado            : id_empleado,
                id_contrato            : id_contrato,
                id_planilla            : '<?php echo $id_planilla; ?>',
            }
        });
    }

    //================== ACTUALIZAR LOS DIAS LABORADOS DE UN EMPLEADO ===========================//
    function updateDiasLaborados(event,Input,id_empleado,id_contrato) {
        if (Input.value==0) {return;}
        tecla  = (Input) ? event.keyCode : event.which;
        var cont=Input.id.split('_')[2];
        if(tecla == 13 || tecla == 9){
            Ext.get('divLoadDiasLaborados').load({
                url     : 'liquidacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc         : 'updateDiasLaborados',
                    dias        : Input.value,
                    id_empleado : id_empleado,
                    id_contrato : id_contrato,
                    id_planilla : '<?php echo $id_planilla; ?>',
                }
            });
        }
        patron = /[^\d]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }
    }

    function validaNumero(event,Input) {
        tecla  = (Input) ? event.keyCode : event.which;
        var cont=Input.id.split('_')[2];
        if(tecla == 13 || tecla == 9){

            if (document.getElementById('id_insert_concepto_'+cont).value>0){
                guardarConcepto(cont,'actualizarconcepto');
            }
            else{
                guardarConcepto(cont,'');
            }
        }
        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        if (document.getElementById('id_insert_concepto_'+cont).value>0) {
            document.getElementById('divImageSaveConcepto_'+cont).style.display='block';
        }
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
            url     : 'liquidacion/bd/bd.php',
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

    function ventana_libro_vacaciones(id_empleado,id_contrato) {

        Win_Ventana_libro_vacaciones = new Ext.Window({
            width       : 450,
            height      : 550,
            id          : 'Win_Ventana_libro_vacaciones',
            title       : 'Vacaciones del Empleado',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            bodyStyle   : 'background-color:#FFF;',
            autoLoad    :
            {
                url     : 'liquidacion/bd/libro_vacaciones.php',
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
                            xtype     : 'button',
                            width     : 60,
                            height    : 56,
                            text      : 'Guardar',
                            id        : 'Btn_guardar_libro_vacaciones',
                            scale     : 'large',
                            iconCls   : 'guardar',
                            iconAlign : 'top',
                            hidden    : false,
                            handler   : function(){ BloqBtn(this); guardarInfoVacaciones(id_empleado,id_contrato) }
                        },
                        {
                            xtype     : 'button',
                            width     : 60,
                            height    : 56,
                            text      : 'Consultar Vacaciones',
                            id        : 'Btn_consultar_vacaciones',
                            scale     : 'large',
                            iconCls   : 'doc_contrato',
                            iconAlign : 'top',
                            hidden    : false,
                            handler   : function(){ BloqBtn(this); ventanaConsultaVacciones(id_empleado,id_contrato) }
                        },
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

    function ventanaConsultaVacciones(id_empleado,id_contrato) {

        Win_Ventana_consulta_vacaciones = new Ext.Window({
            width       : 700,
            height      : 400,
            id          : 'Win_Ventana_consulta_vacaciones',
            title       : 'Vacaciones del empleado para el contrato',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            bodyStyle   : 'background-color:#FFF;',
            autoLoad    :
            {
                url     : 'liquidacion/bd/consulta_vacaciones.php',
                scripts : true,
                nocache : true,
                params  :
                {
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
                            handler     : function(){ BloqBtn(this); Win_Ventana_consulta_vacaciones.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    //==================  GUARDAR LA OBSERVACION DE LA FACTURA ==================================//
    function inputObservacionEmpleado<?php echo $opcGrillaContable; ?>(event,input,id){
        document.getElementById('label_observacion').innerHTML='Observaciones <div style="text-align: center;"><img src="img/loading.gif" ></div>';

        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13 || tecla == 9){
            guardarObservacionEmpleado<?php echo $opcGrillaContable; ?>(id);
        }

        clearTimeout(timeOutObservacionEmpleado<?php echo $opcGrillaContable; ?>);
        timeOutObservacionEmpleado<?php echo $opcGrillaContable; ?> = setTimeout(function(){
            guardarObservacionEmpleado<?php echo $opcGrillaContable; ?>(id);
        },1500);

    }

    function guardarObservacionEmpleado<?php echo $opcGrillaContable; ?>(id){

        var observacion = document.getElementById('observacionEmpleado').value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
        clearTimeout(timeOutObservacionEmpleado<?php echo $opcGrillaContable; ?>);
        timeOutObservacionEmpleado<?php echo $opcGrillaContable; ?> = '';

        Ext.Ajax.request({
            url     : 'liquidacion/bd/bd.php',
            params  :
            {
                opc         : 'guardarObservacionEmpleado',
                id_planilla : '<?php echo $id_planilla; ?>',
                observacion : observacion,
                id          : id,
            },
            success :function (result, request){
                        // console.log(result.responseText);
                        if(result.responseText != 'true'){
                            // alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById('label_observacion').innerHTML='Observaciones <div style="text-align: center;margin-top: -10px;font-style: italic;color: #999;">Error 1</div>';
                            setTimeout(function () {
                                document.getElementById('label_observacion').innerHTML='Observaciones';
                            },1200);
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                        }
                        else{
                            observacion<?php echo $opcGrillaContable; ?>=observacion;
                            document.getElementById('label_observacion').innerHTML='Observaciones <div style="text-align: center;margin-top: -10px;font-style: italic;color: #999;">Guardado</div>';
                            setTimeout(function () {
                                document.getElementById('label_observacion').innerHTML='Observaciones';
                            },1200);
                        }
                    },
            failure : function(){
                        // alert('Error de conexion con el servidor');
                        document.getElementById('label_observacion').innerHTML='Observaciones <div style="text-align: center;margin-top: -10px;font-style: italic;color: #999;">Error 2</div>';
                        setTimeout(function () {
                            document.getElementById('label_observacion').innerHTML='Observaciones';
                        },1200);
                        document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                    }
        });
    }

    // ======= CARGAR LAS VACACIONES ====== //


    //////////////////////////////////////////////////
    /////////                               //////////
    /////////     FUNCIONES DE LA PLANILA   //////////
    /////////                               //////////
    //////////////////////////////////////////////////
    calcularValoresPlanilla();
    function calcularValoresPlanilla() {
        Ext.get('divLoadTotalesPlanilla').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'calcularValoresPlanilla',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    //==================  GUARDAR LA OBSERVACION DE LA FACTURA ==================================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
        document.getElementById('labelObservacionPlanilla').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="img/loading.gif" ></div>';
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
            url     : 'liquidacion/bd/bd.php',
            params  :
            {
                opc            : 'guardarObservacion',
                id             : '<?php echo $id_planilla; ?>',
                observacion    : observacion
            },
            success :function (result, request){
                        if(result.responseText != 'true'){
                            // alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                            document.getElementById('labelObservacionPlanilla').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacionPlanilla').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                        }
                        else{
                            document.getElementById('labelObservacionPlanilla').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardado</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacionPlanilla').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                            observacion<?php echo $opcGrillaContable; ?>=observacion;
                        }
                    },
            failure : function(){
                // alert('Error de conexion con el servidor');
                document.getElementById('labelObservacionPlanilla').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                setTimeout(function () {
                    document.getElementById('labelObservacionPlanilla').innerHTML='<b>OBSERVACIONES</b>';
                },1200);
                document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;}
        });
    }

    function validarPlanillaLiquidacion(){
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

    function guardarPlanilla(){

        var validaPlanilla=validarPlanillaLiquidacion();
        if (validaPlanilla==1) {if (!confirm("Existen empleados sin verificar, desea continuar?")) { return; }}
        if (validaPlanilla==2) {alert("No hay nada que guardar!"); return;}
        cargando_documentos('Generando Planilla de liquidacion...','');
        Ext.get('render_btns_PlanillaLiquidacion').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            timeout: 60000,
            nocache : true,
            text : 'Generando Planilla...',
            params  :
            {
                opc         : 'terminarGenerar',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function cerrarPlanilla(opc){
        Win_ventana_planilla.close();
        if (opc=='delete') { Elimina_Div_nomina_planillas_liquidacion(<?php echo $id_planilla ?>); }
        else{ Actualiza_Div_nomina_planillas_liquidacion(<?php echo $id_planilla ?>); }
    }

    function nuevaPlanillaLiquidacion(){}

    function cancelarPlanillaLiquidacion(){
        var validaPlanilla=validarPlanillaLiquidacion();
        if (validaPlanilla==2) {return;}

        if (!confirm("Realmente desea cancelar la planilla?\nSe eliminaran los asientos generados\nContinuar?")) { return; }
        cargando_documentos('Cancelando Planilla de liquidacion...','');
        Ext.get('render_btns_PlanillaLiquidacion').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'cancelarPlanillaNomina',
                id_planilla : '<?php echo $id_planilla; ?>',
            }
        });
    }

    function imprimirPlanillaLiquidacion(){}

    function modificarDocumentoPlanillaLiquidacion(){
    }

    function restaurarPlanillaLiquidacion(){}

    function eliminarTodosEmpleados(){
        var parentEmpleados = document.querySelectorAll('.claseBuscar')
        ,   id_empleado
        ,   id_contrato;

        // document.querySelectorAll('.claseBuscar')[0].firstChild.nextSibling.nextSibling.getAttribute('onclick').split('(')[1].split(')')[0].split(',')

        for(i in parentEmpleados){
            // console.log(document.querySelectorAll('.claseBuscar')[i].firstChild);
            if(typeof(document.querySelectorAll('.claseBuscar')[i].firstChild)!='undefined'){
                // console.log(id_empleado = document.querySelectorAll('.claseBuscar')[i].firstChild.nextSibling.nextSibling.getAttribute('onclick'));
                id_empleado = document.querySelectorAll('.claseBuscar')[i].firstChild.nextSibling.nextSibling.getAttribute('onclick').split('(')[1].split(')')[0].split(',')[0];
                id_contrato = document.querySelectorAll('.claseBuscar')[i].firstChild.nextSibling.nextSibling.getAttribute('onclick').split('(')[1].split(')')[0].split(',')[1];
                eliminarEmpleado(id_empleado,id_contrato,'true');
                // console.log("eliminarEmpleado("+id_empleado+","+id_contrato+",'true');");
            }

        }
    }

    function check_provision(id_empleado,id_contrato){
        MyLoading2('on');
        var src = document.getElementById('img_provisionamiento').getAttribute('src');
        var accion = '';

        if (src == 'img/checkin.png') {
            // document.getElementById('img_provisionamiento').setAttribute('src','img/checkout.png');
            accion = 'checkout';
        }
        else{
            // document.getElementById('img_provisionamiento').setAttribute('src','img/checkin.png');
            accion = 'checkin';
        }

        Ext.get('divLoadVacaciones').load({
            url     : 'liquidacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'check_provision',
                accion      : accion,
                id_planilla : '<?php echo $id_planilla; ?>',
                id_empleado : id_empleado,
                id_contrato : id_contrato,
            }
        });
    }

    function helpTipoPago() {

        Win_Ventana_help = new Ext.Window({
            width       : 400,
            height      : 400,
            id          : 'Win_Ventana_help',
            title       : 'Descripcion del tipo de pago de vacaciones',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'liquidacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc : 'helpTipoPago',
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
                            handler     : function(){ BloqBtn(this); Win_Ventana_help.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    //======================= CONFIGURAR DETALLE CONCEPTOS ==========================//
    function ventanaConfigurarDatosNELE(cont,tipo_concepto){

        var id_concepto    = document.getElementById('id_concepto_'+cont).value;
        var id_contrato    = document.getElementById('id_contrato_concepto_'+cont).value;
        var id_empleado    = document.getElementById('id_empleado_concepto_'+cont).value;

        Win_Ventana_configurar_datos_ne = new Ext.Window({
            height      : 350,
            width       : 560,
            id          : 'Win_Ventana_configurar_datos_ne',
            title       : 'Registro de campos para nomina electronica',
            modal       : true,
            autoScroll  : false,
            closable    : true,
            autoDestroy : true,
            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
            items       :
            [
                {
                    closable    : false,
                    border      : false,
                    autoScroll  : true,
                    iconCls     : '',
                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                    items       :
                    [

                        {
                            xtype       : "panel",
                            id          : 'contenedor_configuracion_cuentas',
                            border      : false,
                            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                            autoLoad :
                            {
                                url     : 'liquidacion/bd/grillaDetalleConceptosNE.php',
                                scripts : true,
                                nocache : true,
                                params  : 
                                {
                                    //opc         : 'ventanaConfigurarCuentasConcepto',
                                    id_planilla : '<?php echo $id_planilla; ?>',
                                    id_concepto : id_concepto,
                                    id_contrato : id_contrato,
                                    id_empleado : id_empleado,
                                    tipo_concepto : tipo_concepto
                                }
                            }
                        }
                    ]/*,
                    tbar        :
                    [
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Actualizar',
                            scale       : 'large',
                            iconCls     : 'guardar',
                            iconAlign   : 'top',
                            handler     : function(){ updateCuentasConcepto(id_concepto,id_contrato,id_empleado) }
                        },
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_configurar_horas_extras_nomina.close(id) }
                        }
                    ]*/
                }
            ]

        }).show();
    }//end ventanaConfigurarDatosNELE

</script>
