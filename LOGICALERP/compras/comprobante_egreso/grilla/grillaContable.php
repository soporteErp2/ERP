<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");
  include("../config_var_global.php");
  include("../../../funciones_globales/funciones_php/randomico.php");
  include("../../../funciones_globales/funciones_javascript/totalesComprobanteCuentas.php");

  $id_empresa    = $_SESSION['EMPRESA'];
  $id_sucursal   = $_SESSION['SUCURSAL'];
  $bodyArticle   = '';
  $selectCuentas = '';
  $acumScript    = '';
  $estado        = '';
  $fecha         = date('Y-m-d');
?>
<script>

  //variables para calcular los valores de los costos y totales de la factura
  var debitoAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
  ,   creditoAcumulado<?php echo $opcGrillaContable; ?> = 0.00
  ,   total<?php echo $opcGrillaContable; ?>            = 0.00
  ,   contArticulos<?php echo $opcGrillaContable; ?>    = 1
  ,   id_cliente_<?php echo $opcGrillaContable;?>       = 0
  ,   subtotalDebito<?php echo $opcGrillaContable;?>    = 0
  ,   subtotalCredito<?php echo $opcGrillaContable;?>   = 0;

  var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
  ,   codigoCliente<?php echo $opcGrillaContable; ?>      = 0
  ,   nitCliente<?php echo $opcGrillaContable; ?>         = 0
  ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
  ,   nombre_grilla = 'ventanaBucarCuenta<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

  //Bloqueo todos los botones
  Ext.getCmp("Btn_guardar_comprobante_egreso").disable();
  Ext.getCmp("Btn_editar_comprobante_egreso").disable();
  Ext.getCmp("Btn_cancelar_comprobante_egreso").disable();
  Ext.getCmp("Btn_restaurar_comprobante_egreso").disable();
  Ext.getCmp("BtnGroup_Estado1_comprobante_egreso").hide();
  Ext.getCmp("BtnGroup_Guardar_comprobante_egreso").show();

</script>
<?php
    $acumScript .= (user_permisos(43,'false') == 'true')? 'Ext.getCmp("Btn_guardar_comprobante_egreso").enable();' : '';        //guardar
    $acumScript .= (user_permisos(45,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_comprobante_egreso").enable();' : '';       //cancelar

    //CUENTAS DE PAGO
    $sqlCuentasPago  = "SELECT id,nombre,cuenta
                        FROM configuracion_cuentas_pago
                        WHERE activo=1
                            AND id_empresa='$id_empresa'
                            AND tipo='Compra'
                            AND (id_sucursal='$id_sucursal' OR id_sucursal=0)
                            AND estado='Contado'";
    $queryCuentasPago = mysql_query($sqlCuentasPago,$link);

    while ($rowCuentasPago=mysql_fetch_array($queryCuentasPago)) {
        $selectCuentas .= '<option value="'.$rowCuentasPago['id'].'" >'.$rowCuentasPago['nombre'].' '.$rowCuentasPago['cuenta'].'</option>';
    }

    //FLUJO DE EFECTIVO
    $sqlFlujoEfectivo   = "SELECT id,nombre FROM flujo_efectivo WHERE activo=1 AND id_empresa='$id_empresa'";
    $queryFlujoEfectivo = mysql_query($sqlFlujoEfectivo,$link);

    $selectFlujoEfectivo= '';
    while ($rowFlujoEfectivo=mysql_fetch_array($queryFlujoEfectivo)) {
        $selectFlujoEfectivo .= '<option value="'.$rowFlujoEfectivo['id'].'">'.$rowFlujoEfectivo['nombre'].'</option>';
    }

    //============================================ SI NO EXISTE EL PROCESO SE CREA EL ID UNICO ===================================================
    if(!isset($id_comprobante_egreso)){

        // CREACION DEL ID UNICO
        $random_comprobante = responseUnicoRanomico();

        $sqlInsert   = "INSERT INTO $tablaPrincipal (random,id_empresa,id_sucursal,id_bodega,id_usuario,usuario)VALUES('$random_comprobante','$id_empresa','".$_SESSION['SUCURSAL']."','$filtro_bodega',".$_SESSION['IDUSUARIO'].",'".$_SESSION['NOMBREFUNCIONARIO']."')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId = "SELECT id FROM $tablaPrincipal WHERE random='$random_comprobante' LIMIT 0,1";
        $id_comprobante_egreso  = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

        $acumScript .= "new Ext.form.DateField({
                            format     : 'Y-m-d',
                            width      : 130,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : 'fecha".$opcGrillaContable."',
                            editable   : false,
                            listeners  : { select: function(combo, value) { updateFechaNota".$opcGrillaContable."(this.value); } }
                        });
                        Ext.get('renderizaNewArticulo$opcGrillaContable').load({
                            url     : 'comprobante_egreso/bd/bd.php',
                            scripts : true,
                            nocache : true,
                            params  :
                            {
                                opc               : 'cargaHeadInsertUnidadesConTercero',
                                formaConsulta     : 'echo',
                                cont              : 1,
                                opcGrillaContable : '$opcGrillaContable',
                            }
                        });";
    }

    //====================================== SI EXISTE COMPROBANTE DE EGRESO ======================================//
    else{

        include("../bd/functions_body_article.php");

        $sql = "SELECT
                  fecha_comprobante,
                  id_tercero,
                  codigo_tercero,
                  nit_tercero,
                  tercero,
                  observacion,
                  estado,
                  usuario,
                  numero_cheque,
                  id_configuracion_cuenta,
                  id_flujo_efectivo
                FROM $tablaPrincipal
                WHERE id = '$id_comprobante_egreso'";
        $query = mysql_query($sql,$link);

        $fecha_nota      = mysql_result($query,0,'fecha_comprobante');
        $id_tercero      = mysql_result($query,0,'id_tercero');
        $codigo_tercero  = mysql_result($query,0,'codigo_tercero');
        $nit_tercero     = mysql_result($query,0,'nit_tercero');
        $tercero         = mysql_result($query,0,'tercero');
        $usuario         = mysql_result($query,0,'usuario');
        $estado          = mysql_result($query,0,'estado');
        $idConfigCuenta  = mysql_result($query,0,'id_configuracion_cuenta');
        $numero_cheque   = mysql_result($query,0,'numero_cheque');
        $idFlujoEfectivo = mysql_result($query,0,'id_flujo_efectivo');
        // $disponible      = mysql_result($query,0,'disponible_archivo_plano');

        if ($estado==1) { echo "ESTA COMPROBANTE DE EGRESO SE ENCUENTRA CERRADO POR QUE YA HA SIDO GENERADO"; exit; }

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 135,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value); } }
                        });

                        document.getElementById("codigoTercero'.$opcGrillaContable.'").value          = "'.$codigo_tercero.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value             = "'.$nit_tercero.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value          = "'.$tercero.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value                  = "'.$fecha_nota.'";
                        document.getElementById("usuario'.$opcGrillaContable.'").value                = "'.$usuario.'";
                        document.getElementById("cuentaCredito'.$opcGrillaContable.'").value          = "'.$idConfigCuenta.'";
                        document.getElementById("numeroCheque'.$opcGrillaContable.'").value           = "'.$numero_cheque.'";
                        document.getElementById("flujoEfectivo'.$opcGrillaContable.'").value          = "'.$idFlujoEfectivo.'";
                        document.getElementById("disponibleArchivoPlano'.$opcGrillaContable.'").value = "'.$disponible.'";

                        id_cliente_'.$opcGrillaContable.'   = "'.$id_tercero.'";
                        observacion'.$opcGrillaContable.'   = "'.$observacion.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit_tercero.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$tercero.'";
                        codigoCliente'.$opcGrillaContable.' = "'.$codigo_tercero.'";';

        $bodyArticle = cargaArticulosSaveConTercero($id_comprobante_egreso,$observacion,$estado,$opcGrillaContable,$tablaCuentasNota,$idTablaPrincipal,$link);
    }

    $habilita = ($estado=='1')? 'onclick="javascript: return false;" disabled ': '';
?>
<div class="contenedorComprobanteEgresoCuentas">
  <div class="bodyTop">
    <div class="contInfoFact">
      <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
      <div class="contTopFila">
        <div class="renglonTop">
          <div class="labelTop">Sucursal</div>
          <div class="campoTop"><input type="text" id="nombreSucursal<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBRESUCURSAL']; ?>" readonly></div>
        </div>
        <div class="renglonTop">
          <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
          <div class="labelTop">Fecha</div>
          <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
        </div>
        <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;" id="cargarFecha"></div>
        <div class="renglonTop" id="divCodigoTercero">
          <div style="float:left; overflow:hidden; width:16px; height:16px; position:fixed;" id="loadProveedor"></div>
          <div class="labelTop">Codigo Proveedor</div>
          <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" onKeyup="validarNumero_<?php echo $opcGrillaContable; ?>(event,this);" onchange="updateTerceroHead<?php echo $opcGrillaContable; ?>(this);" /></div>
        </div>
        <div class="renglonTop" id="divIdentificacionTercero" >
          <div class="labelTop">Nit</div>
          <div class="campoTop">
            <input type="text" id="nitCliente<?php echo $opcGrillaContable; ?>" onKeyup="validarNumero_<?php echo $opcGrillaContable; ?>(event,this);" onchange="updateTerceroHead<?php echo $opcGrillaContable; ?>(this);" />
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Proveedor</div>
          <div class="campoTop" style="width:277px;"><input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
          <div class="iconBuscarProveedor" onclick="buscarVentanaCliente<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Proveedor">
            <img src="img/buscar20.png"/>
          </div>
        </div>
        <div class="renglonTop">
          <div style="float:left; overflow:hidden; width:16px; height:16px; position:fixed;" id="cargarNumeroCheque"></div>
          <div class="labelTop">No. Cheque</div>
          <div class="campoTop"><input type="text" id="numeroCheque<?php echo $opcGrillaContable; ?>" onKeyup="validaCheque(event,this);" ></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Usuario</div>
          <div class="campoTop"style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" readonly="" value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
        </div>
        <div class="renglonTop" style="width:137px;">
          <div class="labelTop" style="float:left; width:100%;">Cuenta</div>
          <div id="renderSelectCuenta_<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop">
            <select id="cuentaCredito<?php echo $opcGrillaContable; ?>" onChange="updateCuenta<?php echo $opcGrillaContable; ?>(this.value)" style="float:left;">
              <option value="0" selected>Seleccione...</option>
              <?php echo $selectCuentas; ?>
            </select>
          </div>
        </div>
        <div class="renglonTop" style="width:137px;">
          <div class="labelTop" style="float:left; width:100%;">Flujo de Efectivo</div>
          <div id="renderSelectFlujoEfectivo_<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop">
            <select id="flujoEfectivo<?php echo $opcGrillaContable; ?>" onChange="updateFlujoEfectivo<?php echo $opcGrillaContable; ?>(this)" style="float:left;">
              <option value="0" selected>Seleccione...</option>
              <?php echo $selectFlujoEfectivo; ?>
            </select>
          </div>
        </div>
        <div class="renglonTop" style="width:160px;">
          <div class="labelTop" style="float:left; width:100%;">Disponible Archivo Plano</div>
          <div id="renderDisponibleArchivoPlano<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop">
            <select id="disponibleArchivoPlano<?php echo $opcGrillaContable; ?>" onChange="updateDisponibleArchivoPlano<?php echo $opcGrillaContable; ?>(this)" style="float:left;">
              <option value="" selected>Seleccione...</option>
              <option value="Si">Si</option>
              <option value="No">No</option>
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
<script>
    var observacion<?php echo $opcGrillaContable; ?> = '';
    <?php echo $acumScript; ?>

    document.getElementById('codigoTercero<?php echo $opcGrillaContable; ?>').focus();
    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();

    //=============================== UPDATE CUENTA PAGO =================================//
    function updateCuenta<?php echo $opcGrillaContable; ?>(idConfiguracion){
        Ext.get('renderSelectCuenta_<?php echo $opcGrillaContable; ?>').load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc             : 'updateCuentaPago',
                idConfiguracion : idConfiguracion,
                id              : '<?php echo $id_comprobante_egreso; ?>'
            }
        });
    }

    //=========================== UPDATE FLUJO DE EFECTIVO ===============================//
    function updateFlujoEfectivo<?php echo $opcGrillaContable; ?>(select){
        var flujo_efectivo    = select.options[select.selectedIndex].text
        ,   id_flujo_efectivo = select.value;

        if (id_flujo_efectivo == 0 || id_flujo_efectivo == '') flujo_efectivo = '';

        Ext.get('renderSelectFlujoEfectivo_<?php echo $opcGrillaContable; ?>').load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc             : 'updateFlujoEfectivo',
                flujo_efectivo  : flujo_efectivo,
                idFlujoEfectivo : id_flujo_efectivo,
                id              : '<?php echo $id_comprobante_egreso; ?>'
            }
        });
    }

    //=================== UPDATE DISPONIBLE ARCHIVO PLANO ====================//
    function updateDisponibleArchivoPlano<?php echo $opcGrillaContable; ?>(select){
      var disponible = select.value;

      Ext.get('renderDisponibleArchivoPlano<?php echo $opcGrillaContable; ?>').load({
        url     : 'comprobante_egreso/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc        : 'updateDisponibleArchivoPlano',
                    disponible : disponible,
                    id         : '<?php echo $id_comprobante_egreso; ?>'
                  }
      });
    }

    //==================== MANEJAR EL NUMERO DE CHEQUE =========================================//
    function validaCheque(event,input){
        var numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if(tecla == 13 || tecla == 9){ guardarNumeroCheque(input.value); return; }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }

        if(typeof(timeOutNumeroCheque<?php echo $opcGrillaContable; ?> = 'function')){ clearTimeout(timeOutNumeroCheque<?php echo $opcGrillaContable; ?>); }
        timeOutNumeroCheque<?php echo $opcGrillaContable; ?> = setTimeout(function(){
            guardarNumeroCheque(input.value);
        },1500);
    }

    //==================== GUARDAR NUMERO DE CHEQUE ==============================================//
    function guardarNumeroCheque(numeroCheque){
        Ext.get('cargarNumeroCheque').load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                numeroCheque : numeroCheque,
                opc          : 'guardarNumeroCheque',
                id           : '<?php echo $id_comprobante_egreso; ?>'
            }
        });
    }

    //==================== FUNCION PARA CAMBIAR LA FECHA DE LA NOTA ==============================//
    function updateFechaNota<?php echo $opcGrillaContable; ?>(fecha){
        Ext.get('cargarFecha').load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                fecha : fecha,
                opc   : 'actualizarFechaNota',
                id    : '<?php echo $id_comprobante_egreso; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
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
            url     : 'comprobante_egreso/bd/bd.php',
            params  :
            {
                opc               : 'guardarObservacion',
                id                : '<?php echo $id_comprobante_egreso; ?>',
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
                        // alert('Error de conexion con el servidor');
                        document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                        document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);
                    }
        });
    }

    //========================= FILTRO TECLA BUSCAR TERCERO =========================//
    function validarNumero_<?php echo $opcGrillaContable; ?>(event,input){
        var numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if(tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
    }

    function updateTerceroHead<?php echo $opcGrillaContable; ?>(inputTercero){
        var inputId    = inputTercero.id
        ,   codTercero = inputTercero.value;

        Ext.get('loadProveedor').load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'updateTerceroHead',
                codTercero        : codTercero,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_comprobante_egreso; ?>',
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

        var codigo = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_numero_identificacion_'+id).innerHTML;
        if (id == id_cliente_<?php echo $opcGrillaContable;?>){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }

        //PONEMOS EL VALOR DEL NUMERO DE IDENTIFICACION
        document.getElementById('nitCliente<?php echo $opcGrillaContable; ?>').value=document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_numero_identificacion_'+id).innerHTML;

        updateTerceroHead<?php echo $opcGrillaContable; ?>(document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>"));
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
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/eliminar.png');
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Eliminar Tercero');
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick'," eliminaTercero<?php echo $opcGrillaContable; ?>("+cont+")");

        var idInsert = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display='block';
            document.getElementById("divImageDeshacer<?php echo $opcGrillaContable; ?>_"+cont).style.display='block';
        }

        //console.log('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont);
        var tercero = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML;

        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).value=tercero;
        document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value=id;

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
    }

    //============================== FUNCION PARA ELIMINAR EL TERCERO ====================================================//
    function eliminaTercero<?php echo $opcGrillaContable; ?>(cont){

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Buscar Tercero');
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+cont+")");

        idInsert=document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display='block';
            document.getElementById("divImageDeshacer<?php echo $opcGrillaContable; ?>_"+cont).style.display='block';
        }
        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).value   = '';
        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).title   = '';
        document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value = '';
    }

    //============================== FILTRO TECLA BUSCAR CUENTA ==========================================================//
    function buscarCuenta<?php echo $opcGrillaContable; ?>(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   numero      = input.value
        ,   tecla       = (input) ? event.keyCode : event.which;

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
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';

        }
        else if(document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value  = 0;
        }

        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+contIdInput).value   = "";
        document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        document.getElementById("documentoCruce<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        document.getElementById("prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        document.getElementById("numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero_'+contIdInput).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarTercero_'+contIdInput).setAttribute('title','Buscar Tercero');
        document.getElementById('imgBuscarTercero_'+contIdInput).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+contIdInput+")");

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN DOCUMENTO CRUCE
        document.getElementById('imgBuscarDocumentoCruce_'+contIdInput).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarDocumentoCruce_'+contIdInput).setAttribute('title','Buscar Documento Cruce');
        document.getElementById('imgBuscarDocumentoCruce_'+contIdInput).setAttribute('onclick',"ventanaBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>("+contIdInput+")");

        return true;
    }

    function ajaxBuscarCuenta<?php echo $opcGrillaContable; ?>(valor,input){
        if (valor=='') { document.getElementById(input).focus(); return; }

        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
            url     : 'comprobante_egreso/bd/bd.php',
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
                id                : '<?php echo $id_comprobante_egreso; ?>'
            }
        });
    }

    //====================================== VENTANA BUSCAR CUENTA  =======================================================//
    function ventanaBuscarCuenta<?php echo $opcGrillaContable; ?>(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();
        var titulo  = '';
        var sql     = '';
        var documentoCruce = document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDocumentoCruce = document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
        if (documentoCruce>0 ) {
            sql=' AND id IN(SELECT id_cuenta AS id FROM asientos_colgaap WHERE id_documento='+documentoCruce+' AND tipo_documento="FC" AND haber>0 AND id_empresa=<?php echo $id_empresa ?> AND activo=1)';
            var prefijoDocumentoCruce=document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
            var numeroDocumentoCruce=document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value;
            numeroDocumentoCruce=(prefijoDocumentoCruce!='')?prefijoDocumentoCruce+' - '+numeroDocumentoCruce: numeroDocumentoCruce;
            titulo = 'del Documento cruce '+numeroDocumentoCruce;
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
                url     : 'comprobante_egreso/bd/buscarCuentaPuc.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql               : sql,
                    nombre_grilla     : nombre_grilla,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cargaFuncion      : 'responseVentanaBuscarCuenta<?php echo $opcGrillaContable; ?>(id,'+cont+');'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    height      : 56,
                    width       : 60,
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_buscar_cuenta_nota.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarCuenta<?php echo $opcGrillaContable; ?>(id,cont){
        document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).focus();

        var cuenta = document.getElementById('div_<?php echo $opcGrillaContable; ?>_cuenta_'+id).innerHTML;
        if (cuenta.length<6) {alert("Error!\nDebe seleccionar una cuenta con minimo 6 digitos"); return;}

        if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';

            document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value = id;
            document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value   = cuenta;
        }
        else{
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = "none";
            document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value = id;
            document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value   = cuenta;
        }
        Win_Ventana_buscar_cuenta_nota.close(id);
    }

    //============================= FILTRO TECLA GUARDAR CUENTA ==========================================================//
    function guardarAuto<?php echo $opcGrillaContable; ?>(event,input,cont){

        var idInsertCuenta  = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tecla  = input? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            guardarNewCuenta<?php echo $opcGrillaContable; ?>(cont);
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        else if (idInsertCuenta>0) {
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

        patron = /[^\d.]/g;
        if(patron.test(value)){ input.value = input.value.replace(patron,''); }
        return true;
    }

    function guardarNewCuenta<?php echo $opcGrillaContable; ?>(cont){
        var idInsertCuenta          = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   idPuc                   = document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   cuenta                  = document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   id_documento_cruce      = document.getElementById('idDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   documento_cruce         = document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   prefijo_documento_cruce = document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   numero_documento_cruce  = document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   debito                  = document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   credito                 = document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   opc                     = 'guardarCuenta'
        ,   divRender               = ''
        ,   accion                  = 'agregar'
        ,   terceroGeneral          = document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value
        ,   id_tabla_referencia   = document.getElementById('idTablaReferencia<?php echo $opcGrillaContable; ?>_'+cont).value;

        debito  = (isNaN(debito))? 0: debito;
        credito = (isNaN(credito))? 0: credito;

        //VALIDAR QUE LA FILA TENGA UNA CUENTA
        if (idPuc == 0){ alert('El campo cuenta es Obligatorio'); setTimeout(function(){ document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        //SI ESCOGE UN TIPO DE DOC. CRUCE PERO NO ESCRIBE LA CUENTA
        else if (documento_cruce!='' && (numero_documento_cruce=='' || numero_documento_cruce==0)) { alert('Si relaciona un documento, ingrese el numero!'); setTimeout(function(){ document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return;}
        //SI PONE UN PREFIJO
        else if (prefijo_documento_cruce!='') {

            mensaje = '';
            input   = '';
            //PERO NO ESCRIBIO EL NUMERO DEL DOC. CRUCE
            if (numero_documento_cruce=='' || numero_documento_cruce==0) {
                alert('Si digita el prefijo, digite tambien el numero del documento');
                setTimeout(function(){ document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
                return;
            }
            //PERO NO SELECCIONO EL TIPO DE DOC. CRUCE
            if (documento_cruce=='' || documento_cruce==0) {
                alert('Si digita el prefijo, Seleccione el tipo de documento');
                setTimeout(function(){ document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
                return;
            }
        }
        //SI TIENE UN NUMERO DE DOC. CRUCE, PERO NO UN TIPO DE DOC. CRUCE
        else if (numero_documento_cruce!='' && documento_cruce=='') {
            alert("Si digita el numero de Doc. cruce, Seleccione el tipo del documento");
            setTimeout(function(){ document.getElementById('documentoCruce<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
            return;
        }

        //VALIDAR QUE TENGA ALMENOS UN VALOR EN EL DEBITO O CREDITO
        else if (debito==0 && credito==0) { alert("Debe ingresar un valor debito o credito en la cuenta!"); setTimeout(function(){ document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return;}

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

        var id_tercero = document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value;

        Ext.get(divRender).load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                     : opc,
                opcGrillaContable       : '<?php echo $opcGrillaContable; ?>',
                consecutivo             : contArticulos<?php echo $opcGrillaContable; ?>,
                cont                    : cont,
                idInsertCuenta          : idInsertCuenta,
                idPuc                   : idPuc,
                cuenta                  : cuenta,
                id_documento_cruce      : id_documento_cruce,
                documento_cruce         : documento_cruce,
                prefijo_documento_cruce : prefijo_documento_cruce,
                numero_documento_cruce  : numero_documento_cruce,
                debe                    : debito,
                haber                   : credito,
                id                      : '<?php echo $id_comprobante_egreso; ?>',
                id_tercero_general      : id_cliente_<?php echo $opcGrillaContable; ?>,
                id_tercero              : id_tercero,
                terceroGeneral          : terceroGeneral,
                id_tabla_referencia     : id_tabla_referencia,
            }
        });
    }

    function deleteCuenta<?php echo $opcGrillaContable; ?>(cont){
        //antes de eliminar tomamos las variable para enviarlas a la funcion para recalcular los totales
        var idCuenta<?php echo $opcGrillaContable; ?> = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   debito  = document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   credito = document.getElementById('credito<?php echo $opcGrillaContable; ?>_'+cont).value;

        debito  = (debito=='')? 0: debito;
        credito = (credito=='')? 0: credito;

        if(confirm('Esta Seguro de eliminar esta cuenta del comprobante de egreso?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'comprobante_egreso/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteCuenta',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idCuenta          : idCuenta<?php echo $opcGrillaContable; ?>,
                    cont              : cont,
                    id                : '<?php echo $id_comprobante_egreso; ?>'
                }
            });
            calcTotal<?php echo $opcGrillaContable ?>(debito,credito,'eliminar');
        }
    }

    //===================== CANCELAR LOS CAMBIOS DE UNA CUENTA ===============================================//
    function retrocederCuenta<?php echo $opcGrillaContable; ?>(cont){

        var id_actual = document.getElementById("idInsertCuenta<?php echo $opcGrillaContable; ?>_"+cont).value;

        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'retrocederCuenta',
                cont              : cont,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idCuentaInsert    : id_actual,
                id                : '<?php echo $id_comprobante_egreso; ?>'
            }
        });
    }

    //===================================== FINALIZAR 'GENERAR' ===================================//
    function guardar<?php echo $opcGrillaContable; ?>(){

        var validacion = validarCuentas<?php echo $opcGrillaContable; ?>();
        if (validacion==0) {alert("No hay nada que guardar!"); return;}
        else if (validacion==1) { alert("Hay cuentas pendientes por guardar!"); return; }

        observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;
        cuenta      = document.getElementById('cuentaCredito<?php echo $opcGrillaContable; ?>');

        if (cuenta.value==0) { alert("Seleccione la cuenta!"); cuenta.focus(); return;}
        else if (validacion== 2 || validacion== 0) {
            cargando_documentos('Validando Documento...','');
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'comprobante_egreso/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'validaNota',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_comprobante_egreso; ?>',
                    id_tercero        : id_cliente_<?php echo $opcGrillaContable; ?>,
                    cuenta            : cuenta.value
                }
            });
         }
    }

    //===================================== VALIDAR LA NOTA ANTES DE GENERARLA ========================================//
    function generar<?php echo $opcGrillaContable; ?>(){
        cargando_documentos('Generando Documento...','');
        Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                : 'terminarGenerar',
                opcGrillaContable  : '<?php echo $opcGrillaContable; ?>',
                id                 : '<?php echo $id_comprobante_egreso; ?>',
                id_tercero         : id_cliente_<?php echo $opcGrillaContable; ?>,
                id_tercero_general : id_cliente_<?php echo $opcGrillaContable; ?>,
            }
        });
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
                url     : 'comprobante_egreso/bd/buscarGrillaContable.php',
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

    //================================== VALIDACION NUMERICA ===================================//
    function validarNumberCuenta<?php echo $opcGrillaContable; ?>(event,input,typeValidate,cont){

        var contIdInput = (input.id).split('_')[1]
        ,   nombreInput = (input.id).split('_')[0]
        ,   numero      = input.value
        ,   tecla       = (input) ? event.keyCode : event.which;

        if(tecla == 13){

            if (nombreInput=='prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>') {
                document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
            }
            if (nombreInput=='documentoCruce<?php echo $opcGrillaContable; ?>') {
                document.getElementById('numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
            }
            if (nombreInput=='numeroDocumentoCruce<?php echo $opcGrillaContable; ?>') {
                document.getElementById('debito<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
            }
            if (nombreInput=='documentoCruce<?php echo $opcGrillaContable; ?>') {
                document.getElementById('prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
            }
            return true;
        }

        patron = /[^\d.]/g;
        if(patron.test(numero)){
            if (typeValidate=='double') {
                numero      = numero.replace(patron,'');
                input.value = numero;
            }
            else if(typeValidate=='mayuscula'){ input.value = input.value.toUpperCase(); }
        }
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
        else{
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display    = 'inline';

            if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
            }
        }
    }

    //============================  VALIDAR QUE NO HAYA NINGUNA CUENTA POR GUARDAR O POR ACTULIZAR =======================//
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

        if(contTotal == 0 || contTotal == 1){  return 0; }      //Si no hay elementos guardados
        else if(cont > 0){ return 1; }      //Pendiente por guardar
        else { return 2; }
    }

    //============================ CANCELAR UN DOCUMENTO =========================================================================//
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
                    url     : 'comprobante_egreso/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc : 'cancelarDocumento',
                        id  : '<?php echo $id_comprobante_egreso; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
                    }
                });
            };
        }
    }

    //============================= VENTANA DE LA OBSERVACION / DETALLE DE LA CUENTA ============================================//
    function ventanaObservacionCuenta<?php echo $opcGrillaContable; ?>(cont){
        var id = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_descripcion_cuenta = new Ext.Window({
            width       : 330,
            height      : 230,
            id          : 'Win_Ventana_descripcion_cuenta',
            title       : 'Observacion Cuenta',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'comprobante_egreso/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaObservacionCuenta',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idCuenta          : id,
                    cont              : cont,
                    id                : '<?php echo $id_comprobante_egreso; ?>',
                    readonly          : ''
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
                    handler     : function(){ btnGuardarDescripcionCuenta<?php echo $opcGrillaContable; ?>(cont,id); }
                },
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_Ventana_descripcion_cuenta.close(id) }
                }
            ]
        }).show();
    }

    //============================ FUNCION DEL BOTON DE LA VENTANA PARA GUARDAR LA OBSERVACION/DETALLE
    function btnGuardarDescripcionCuenta<?php echo $opcGrillaContable; ?>(cont,idArticulo){
        var observacion = document.getElementById("observacionArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacion<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'comprobante_egreso/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarDescripcionCuenta',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idCuenta          : idArticulo,
                id                : '<?php echo $id_comprobante_egreso; ?>',
                observacion       : observacion,
            }
        });
    }

    //============================= FUNCION PARA EL SELECT DE LA CUENTA CUANDO CAMBIA ============================================//
    function selectTipoDocumento<?php echo $opcGrillaContable; ?>(cont){
        if(document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'block';
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display     = 'block';
        }
    }

    //============================= FUNCION PARA BUSCAR EL DOCUMENTO CRUCE DEL COMPROBANTE ========================================//
    function ventanaBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>(cont){
        // console.log("<?php echo $id_comprobante_egreso ?>");
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_documento_cruce<?php echo $opcGrillaContable; ?> = new Ext.Window({
            height      : myalto-50,
            width       : myancho-100,
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
                            height      : 33,
                            bodyStyle   : 'background-color:rgba(255,255,0,0);',
                            autoLoad    :
                            {
                                url     : 'comprobante_egreso/bd/bd.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    opc                   : 'ventana_buscar_sucursal',
                                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                                    id_comprobante_egreso : <?php echo $id_comprobante_egreso; ?>,
                                    cont                  : cont,
                                }
                            }
                        }
                    ]
                },
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Documento',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 190,
                            height      : 33,
                            bodyStyle   : 'background-color:rgba(255,255,0,0);',
                            autoLoad    :
                            {
                                url     : 'comprobante_egreso/bd/bd.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    cont                  : cont,
                                    opc                   : 'ventana_buscar_documento_cruce',
                                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                                    id_comprobante_egreso : <?php echo $id_comprobante_egreso; ?>,
                                }
                            }
                        }
                    ]
                },
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Terceros',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 110,
                            height      : 33,
                            bodyStyle   : 'background-color:rgba(255,255,0,0);',
                            autoLoad    :
                            {
                                url     : 'comprobante_egreso/bd/bd.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    cont                  : cont,
                                    opc                   : 'ventana_buscar_terceros',
                                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                                    id_comprobante_egreso : <?php echo $id_comprobante_egreso; ?>,
                                }
                            }
                        }
                    ]
                },
                {
                    xtype       : 'button',
                    id          : 'Btn_editar_orden_compra',
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

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN DOCUMENTO CRUCE
        document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('title','Buscar Documento Cruce');
        document.getElementById('imgBuscarDocumentoCruce_'+cont).setAttribute('onclick',"ventanaBuscarDocumentoCruce<?php echo $opcGrillaContable; ?>("+cont+")");

        var idInsert = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value;
        if (idInsert>0) {
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     ='block';
            document.getElementById("divImageDeshacer<?php echo $opcGrillaContable; ?>_"+cont).style.display ='block';
        }

        //LIMPIAR LOS CAMPOS
        document.getElementById('idCuenta<?php echo $opcGrillaContable; ?>_'+cont).value  = 0;
        document.getElementById('cuenta<?php echo $opcGrillaContable; ?>_'+cont).value    = "";
        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).value   = "";
        document.getElementById("tercero<?php echo $opcGrillaContable; ?>_"+cont).title   = "";
        document.getElementById("idTercero<?php echo $opcGrillaContable; ?>_"+cont).value = "";
        document.getElementById("documentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value = "";
        document.getElementById("idDocumentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value = "";
        document.getElementById("prefijoDocumentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value = "";
        document.getElementById("numeroDocumentoCruce<?php echo $opcGrillaContable; ?>_"+cont).value = "";
        document.getElementById("debito<?php echo $opcGrillaContable; ?>_"+cont).value = "";

        //CAMBIAR LOS ATRIBUTOS DE LA IMAGEN PARA QUE ELIMINE UN TERCERO
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('src','img/buscar20.png');
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('title','Buscar Tercero');
        document.getElementById('imgBuscarTercero_'+cont).setAttribute('onclick',"buscarVentanaTercero<?php echo $opcGrillaContable; ?>("+cont+")");
    }

    function ventanaBuscarCentroCostos<?php echo $opcGrillaContable; ?>(cont,idRow){
        Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 550,
            height      : 450,
            id          : 'Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>',
            title       : 'Seleccione el Centro de Costo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/grillaBuscarCentroCostos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cargaFunction     : 'renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id,'+idRow+','+cont+');return;'
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

    function renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id,idRow,cont){
        MyLoading2('on');
        var nombre = document.getElementById('div_CentroCostos_nombre_'+id).innerHTML;
        var codigo = document.getElementById('div_CentroCostos_codigo_'+id).innerHTML;

        Ext.Ajax.request({
            url     : 'comprobante_egreso/bd/bd.php',
            params  :
            {
                opc                  : 'actualizarCcos',
                id_centro_costos     : id,
                idRow                : idRow,
                codigo_centro_costos : codigo,
            },
            success :function (result, request){
                        result.responseText= (result.responseText).replace(/[^a-z]/g,'');
                        if(result.responseText == 'true'){

                            // Win_Ventana_buscar_cuenta_nota.close();
                            Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
                            document.getElementById('codigo_centro_costos').innerHTML=codigo;
                            document.getElementById('nombre_centro_costos').innerHTML=nombre;
                            var img = document.getElementById('imgCentroCostos');
                            img.src='../inventario/img/false_inv.png';
                            img.setAttribute('onclick','eliminarCentroCostos<?php echo $opcGrillaContable; ?>('+cont+','+idRow+')');

                            Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close()
                            MyLoading2('off',{texto:'Se actualizo el centro de costos'});

                            document.getElementById("label_cont_"+cont+"").innerHTML=cont;

                        }
                        else if(result.responseText=='padre') {
                             MyLoading2('off',{texto:'Debe seleccionar un centro de costos hijo',icono:'fail',duracion:3000});
                            document.getElementById("label_cont_"+cont+"").innerHTML="<div style='float:right;'>"+cont+"</div><div style='float:left;'><img src='img/warning.png' title='Requiere Centro de Costos'></div>";
                        }
                        else{
                            // alert("Error\nNo se actualizo la cuenta niif, intentelo de nuevo\nSi el problema persiste comuniquese con el administrador del sistema");
                            MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                            document.getElementById("label_cont_"+cont+"").innerHTML="<div style='float:right;'>"+cont+"</div><div style='float:left;'><img src='img/warning.png' title='Requiere Centro de Costos'></div>";
                        }
                    },
            failure : function(){
                                MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                                document.getElementById("label_cont_"+cont+"").innerHTML="<div style='float:right;'>"+cont+"</div><div style='float:left;'><img src='img/warning.png' title='Requiere Centro de Costos'></div>";
                            }
        });
    }

    function eliminarCentroCostos<?php echo $opcGrillaContable; ?>(cont,idRow) {
        MyLoading2('on');
        Ext.Ajax.request({
            url     : 'comprobante_egreso/bd/bd.php',
            params  :
            {
                opc              : 'actualizarCcos',
                idRow            : idRow,
                id_centro_costos : '',

            },
            success :function (result, request){
                        if(result.responseText.split('{.}')[0] == 'true'){
                            var img = document.getElementById('imgCentroCostos');
                            img.src='img/buscar20.png';
                            img.setAttribute('onclick','ventanaBuscarCentroCostos<?php echo $opcGrillaContable; ?>('+cont+','+idRow+')');
                            document.getElementById('codigo_centro_costos').innerHTML='&nbsp;';
                            document.getElementById('nombre_centro_costos').innerHTML='';
                            MyLoading2('off',{texto:'Se elimino el centro de costos'});
                            document.getElementById("label_cont_"+cont).innerHTML="<div style='float:right;'>"+cont+"</div><div style='float:left;'><img src='img/warning.png' title='Requiere Centro de Costos'></div>";
                        }
                        else{
                            MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                            document.getElementById("label_cont_"+cont).innerHTML=cont;
                        }
                    },
            failure : function(){
                        MyLoading2('off',{texto:'No se actualizo el centro de costos, intentelo de nuevo',icono:'fail'});
                        document.getElementById("label_cont_"+cont).innerHTML=cont;
                    }
        });
    }

    function ventanaDocumentosCruce<?php echo $opcGrillaContable; ?>(){
        Win_ventanaDocumentosCruceComprobanteEgreso = new Ext.Window({
            width       : 550,
            height      : 500,
            id          : 'Win_ventanaDocumentosCruceComprobanteEgreso',
            title       : '',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    : {
                            url     : 'comprobante_egreso/documentos_adjuntos.php',
                            scripts : true,
                            nocache : true,
                            params  : {
                                        id_comprobante_egreso : '<?php echo $id_comprobante_egreso; ?>',
                                      }
                          },
        }).show();

    }

    let debounce_tercero;

    function debounceRequest (input, delay=1000,cont) {
        clearTimeout(debounce_tercero);

        debounce_tercero = setTimeout(() => {
            makeRequest(input,cont);
        }, delay);
        
        // Verificar si se presiona Enter
        if (event.key === 'Enter') {
            clearTimeout(debounce_tercero);
            makeRequest(input,cont);
        }
    };

    function makeRequest (input,cont) {

        var idInsertCuenta  = document.getElementById('idInsertCuenta<?php echo $opcGrillaContable; ?>_'+cont).value
        if (idInsertCuenta>0) {
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

        if (input=="") {
            document.getElementById("tercero<?=$opcGrillaContable?>_"+cont).value = ""
            document.getElementById("idTercero<?=$opcGrillaContable?>_"+cont).value = ""

            return;
        }
        Ext.Ajax.request({
            url: 'comprobante_egreso/bd/bd.php',
            method: 'GET',
            params: {
                opc: "buscarTercero",
                document: input
            },
            success: (response) => {
                let result = JSON.parse(response.responseText)
                if (result.status=="success") {
                    document.getElementById("tercero<?=$opcGrillaContable?>_"+cont).value = result.nombre
                    document.getElementById("idTercero<?=$opcGrillaContable?>_"+cont).value = result.id
                }
                else{
                    document.getElementById("tercero<?=$opcGrillaContable?>_"+cont).value = ""
                    document.getElementById("idTercero<?=$opcGrillaContable?>_"+cont).value = ""
                }
                console.log('Response:', result);
            },
            failure: (response) => {
                console.error('Error:', response);
            }
        });
    };


</script>
