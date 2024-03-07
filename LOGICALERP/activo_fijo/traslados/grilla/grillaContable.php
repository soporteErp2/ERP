<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");
  include("../config_var_global.php");
  include("../../../funciones_globales/funciones_php/randomico.php");

  $id_empresa  = $_SESSION['EMPRESA'];
  $id_sucursal = $filtro_sucursal;
  $id_usuario  = $_SESSION['IDUSUARIO'];
  $bodyArticle = '';
  $acumScript  = '';
  $estado      = '';
  $fecha       = date('Y-m-d');

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
  //Variables para calcular los valores de los costos y totales de la factura
  var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
  ,   total<?php echo $opcGrillaContable; ?>              = 0.00
  ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
  ,   id_cliente_<?php echo $opcGrillaContable;?>         = 0
  ,   arrayBodegas_<?php echo $opcGrillaContable;?>       = new Array()
  ,   timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
  ,   codigoCliente<?php echo $opcGrillaContable; ?>      = 0
  ,   nitTercero<?php echo $opcGrillaContable; ?>         = 0
  ,   nombreTercero<?php echo $opcGrillaContable; ?>      = ''
  ,   nombre_grilla                                       = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

  //Bloqueo todos los botones
  Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
  Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();
</script>
<?php
  $acumScript .= (user_permisos(227,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();
                                                          Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
                                                          Ext.getCmp("Btn_buscar_'.$opcGrillaContable.'").enable();'
                                                       : 'Ext.getCmp("btnNueva'.$opcGrillaContable.'").disable();
                                                          document.getElementById("contenidoHijoTraslado").style.display="none";';

  $acumScript .= (user_permisos(229,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';

  //======================== SI NO EXISTE EL DOCUMENTO =======================//
  if(!isset($id_traslado) || $id_traslado == ''){
    include("../bd/functions_body_article.php");
    // CREACION DEL ID UNICO
    $random_factura = responseUnicoRanomico();

    $sql = "INSERT INTO $tablaPrincipal (id_empresa,random,fecha_inicio,id_sucursal,id_bodega,id_usuario,usuario)
            VALUES('$id_empresa','$random_factura','$fecha','$id_sucursal','$filtro_ubicacion','".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."')";
    $query = $mysql->query($sql,$mysql->link);

    $sql         = "SELECT id FROM $tablaPrincipal WHERE random = '$random_factura' LIMIT 0,1";
    $query       = $mysql->query($sql,$mysql->link);
    $id_traslado = $mysql->result($query,0,'id');

    $acumScript .= 'new Ext.form.DateField({
                      format     : "Y-m-d",
                      width      : 135,
                      allowBlank : false,
                      showToday  : false,
                      applyTo    : "fecha'.$opcGrillaContable.'",
                      editable   : false,
                      value      : new Date(),
                      listeners  : {
                        select: function() { actualizaFechaDocumento(); }
                      }
                    });

                    actualizaFechaDocumento();
                    document.getElementById("nombreVendedor'.$opcGrillaContable.'").value     = "' . $_SESSION['NOMBREFUNCIONARIO'] . '";
                    document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML = "";';

    //MOSTRAR LOS ACTIVOS CARGADOS AL DOCUMENTO
    $bodyArticle = cargaArticulosSave($id_traslado,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
  }
  //========================= SI EXISTE EL DOCUMENTO =========================//
  else{
    include("../bd/functions_body_article.php");

    $sql = "SELECT
              consecutivo,
              fecha_inicio,
              codigo_tercero,
              tipo_identificacion_tercero,
              numero_identificacion_tercero,
              tercero,
              usuario,
              observacion,
              id_sucursal,
              id_bodega,
              id_sucursal_destino,
              id_bodega_destino,
              estado,
              tipo_identificacion_tercero
            FROM
              $tablaPrincipal
            WHERE
              id = '$id_traslado'
            AND
              activo = 1";
    $query = mysql_query($sql,$link);
    $consecutivo                   = mysql_result($query,0,'consecutivo');
    $fecha_inicio                  = mysql_result($query,0,'fecha_inicio');
    $codigo_tercero                = mysql_result($query,0,'codigo_tercero');
    $tipo_identificacion_tercero   = mysql_result($query,0,'tipo_identificacion_tercero');
    $numero_identificacion_tercero = mysql_result($query,0,'numero_identificacion_tercero');
    $tercero                       = mysql_result($query,0,'tercero');
    $usuario                       = mysql_result($query,0,'usuario');
    $observacion                   = mysql_result($query,0,'observacion');
    $estado                        = mysql_result($query,0,'estado');
    $id_sucursal                   = mysql_result($query,0,'id_sucursal');
    $id_bodega                     = mysql_result($query,0,'id_bodega');
    $id_sucursal_destino           = mysql_result($query,0,'id_sucursal_destino');
    $id_bodega_destino             = mysql_result($query,0,'id_bodega_destino');
    $tipo_identificacion_tercero   = mysql_result($query,0,'tipo_identificacion_tercero');

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
                      listeners  : { select: function() { actualizaFechaDocumento(); } }
                    });

                    document.getElementById("tipoDocumento'.$opcGrillaContable.'").value        = "'.$tipo_identificacion_tercero.'";
                    document.getElementById("sucursal_destino").value                           = "'.$id_sucursal_destino.'";
                    cambiaBodegaDestinoTraslado("'.$id_sucursal_destino.'");
                    document.getElementById("bodega_destino").value                             = "'.$id_bodega_destino.'";
                    document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML   = "Traslado<br>No. '.$consecutivo.'";
                    document.getElementById("titleDocumento'.$opcGrillaContable.'").style.color = "#333";';

    $bodyArticle = cargaArticulosSave($id_traslado,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
  }

  //=================== CONSULTAMOS LOS TIPOS DE DOCUMENTO ===================//
  $sqlTipoDocumento   = "SELECT id,nombre,detalle,tipo FROM tipo_documento WHERE id_empresa='$id_empresa' AND activo=1";
  $queryTipoDocumento = mysql_query($sqlTipoDocumento,$link);
  $tipoDocumento      = '<select id="tipoDocumento'.$opcGrillaContable.'" style="width:65px;" onchange="document.getElementById(\'nitTercero'.$opcGrillaContable.'\').focus();">';
  while($rowTipoDoc = mysql_fetch_array($queryTipoDocumento)){
    $selected = ($tipo_identificacion_tercero == $rowTipoDoc['nombre'])? 'selected' : '';
    $tipoDocumento .= '<option value="'.$rowTipoDoc['nombre'].'" title="'.$rowTipoDoc['detalle'].'" '.$selected.'>'.$rowTipoDoc['nombre'].'</option>';
  }
  $tipoDocumento .= '</select>';
?>
<style type="text/css">
  .contenedorGrilla{
    margin-top  : 0;
    height      : calc(100% - 75%);
  }
</style>
<div class="contenedorOrdenCompra" id="contenidoHijoTraslado">
  <div class="bodyTop">
    <div class="contInfoFact">
      <div id="render_btns_<?php echo $opcGrillaContable; ?>" style="width: 20px;height: 20px;position: fixed;margin: 5px 10px;overflow: hidden;"></div>
      <div class="contTopFila">
        <div class="renglonTop">
          <div class="labelTop">Fecha</div><div id="divLoadFecha" style="width:20px;height:20px;margin-top: -20;margin-left: -22;overflow:hidden;float: right;"></div>
          <div class="campoTop">
            <input type="text" id="fecha<?php echo $opcGrillaContable; ?>" />
          </div>
        </div>
        <div class="renglonTop" id="divCodigoTercero">
          <div class="labelTop">Codigo Tercero</div>
          <div class="campoTop"><input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $codigo_tercero; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" ></div>
        </div>
        <div class="renglonTop" id="divIdentificacionTercero">
          <div class="labelTop">N. de Identificacion</div>
          <div class="campoTop" style="width:230px">
            <?php echo  $tipoDocumento; ?>
            <input type="text" style="width:161px"  id="nitTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $numero_identificacion_tercero; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" />
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Tercero</div>
          <div class="campoTop" style="width:277px;">
            <input type="text" id="nombreTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $tercero; ?>" Readonly/>
          </div>
          <div class="iconBuscarProveedor" onclick="buscarVentanaCliente<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Proveedor">
            <img src="images/buscar20.png"/>
          </div>
        </div>
        <div class="renglonTop" id="divIdentificacionTercero">
          <div class="labelTop">Sucursal Destino</div>
          <div class="campoTop" style="width:230px">
            <select id='sucursal_destino' onchange="cambiaBodegaDestinoTraslado(this.value)">
              <option value=''>Seleccione...</option>
              <?php echo $optionSucursales; ?>
            </select>
          </div>
        </div>
        <div class="renglonTop" id="divIdentificacionTercero">
          <div class="labelTop">Bodega Destino</div>
          <div class="campoTop" style="width:230px">
            <select id='bodega_destino'>
              <option value=''>Seleccione...</option>
            </select>
          </div>
        </div>
        <div class="renglonTop">
            <div class="labelTop">Usuario</div>
            <div class="campoTop" style="width:277px;">
              <input type="text" id="nombreVendedor<?php echo $opcGrillaContable; ?>" style="width:100%" value="<?php echo $usuario; ?>" Readonly />
            </div>
          </div>
      </div>
    </div>
  </div>
  <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
    <div class="toolbar_grilla_manual" style="display:none;">
      <div class="div_input_busqueda_grilla_manual">
        <input type="text" id="inputBuscarGrillaManual" onkeyup="inputBuscarGrillaManual(event,this);">
      </div>
      <div class="div_img_actualizar_datos_grilla_manual">
        <img src="images/reload_grilla.png" onclick="actualizarDatosGrillaManual();">
      </div>
    </div>
    <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
  </div>
</div>
<script>
  var observacion<?php echo $opcGrillaContable; ?> = '';
  <?php echo $acumScript; ?>

  //======================= CAMBIAR SUCURSAL DE DESTINO ======================//
  function cambiaBodegaDestinoTraslado(id_sucursal){
    if(id_sucursal == ''){
      document.getElementById('bodega_destino').innerHTML='<option value="">Seleccione...</option>';
      return;
    }
    var optionBodegas = '';
    arrayBodegas_<?php echo $opcGrillaContable; ?>[id_sucursal].forEach(function(elemento,index) { optionBodegas += `<option value='${index}'>${elemento}</option>`;  ;});
    document.getElementById('bodega_destino').innerHTML=`<option value=''>Seleccione...</option>${optionBodegas}`;
  }

  //======================= CAMBIAR FECHA DEL DOCUMENTO ======================//
  function actualizaFechaDocumento(){
    fecha = document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value;
    Ext.get('divLoadFecha').load({
      url     : 'traslados/bd/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc               : 'actualizaFechaDocumento',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  id                : '<?php echo $id_traslado ?>',
                  fecha             : fecha
                }
    });
  }

  //================= BUSCAR TERCERO POR CODIGO O # DOCUMENTO ================//
  function buscarCliente<?php echo $opcGrillaContable; ?>(event,Input){
    var tecla   = Input ? event.keyCode : event.which
    ,   inputId = Input.id
    ,   numero  = Input.value;

    if(inputId == "nitTercero<?php echo $opcGrillaContable; ?>" && numero == nitTercero<?php echo $opcGrillaContable; ?>){
      return true;
    }
    else if(inputId == "codCliente<?php echo $opcGrillaContable; ?>" && numero == codigoCliente<?php echo $opcGrillaContable; ?>){
      return true;
    }
    else if(Input.value != '' && (tecla == 13 )){
      Input.blur();
      ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(Input.value, Input.id, document.getElementById('tipoDocumento<?php echo $opcGrillaContable; ?>').value);
      return true;
    }
    else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){
      return true;
    }

    patron = /[^\d.]/g;
    if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

    return true;
  }

  //========================= BUSCAR CLIENTE EN EL BD ========================//
  function ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codCliente,inputId,tipoDocumento){
    Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
      url     : 'traslados/bd/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc               : 'buscarCliente',
                  codCliente        : codCliente,
                  tipoDocumento     : tipoDocumento,
                  opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                  id                : '<?php echo $id_traslado; ?>',
                  inputId           : inputId
                }
    });
  }

  //========================= VENTANA BUSCAR CLIENTE =========================//
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
      autoLoad    : {
                      url     : '../funciones_globales/grillas/BusquedaTerceros.php',
                      scripts : true,
                      nocache : true,
                      params  : {
                        sql           : '',
                        cargaFuncion  : 'renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id);',
                        nombre_grilla : 'cliente<?php echo $opcGrillaContable; ?>'
                      }
                    },
      tbar        : [
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

  //====================== MOSTRAR RESULTADO EN PANTALLA =====================//
  function renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id){
    var codigo = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_numero_identificacion_' + id).innerHTML
    ,  typeDoc = document.getElementById('div_cliente<?php echo $opcGrillaContable; ?>_tipo_identificacion_' + id).innerHTML;

    if(id == id_cliente_<?php echo $opcGrillaContable;?>){
      Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
      return;
    }

    ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codigo, 'nitTercero<?php echo $opcGrillaContable; ?>', typeDoc);
    Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
  }

  //=========================== OBTENER OBSERVACION ==========================//
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

  //=========================== GUARDAR OBSERVACION ==========================//
  function guardarObservacion<?php echo $opcGrillaContable; ?>(){
    var observacion = document.getElementById('observacion<?php echo $opcGrillaContable; ?>').value;
    observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
    clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
    timeOutObservacion<?php echo $opcGrillaContable; ?> = '';

    Ext.Ajax.request({
      url     : 'traslados/bd/bd.php',
      params  : {
                  opc            : 'guardarObservacion',
                  id             : '<?php echo $id_traslado; ?>',
                  tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                  observacion    : observacion
                },
      success : function(result, request){
                  if(result.responseText != 'true'){
                    alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                    document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                    document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                    setTimeout(function(){
                      document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                    },1200);
                  }
                  else{
                    observacion<?php echo $opcGrillaContable; ?> = observacion;
                    document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardado</div>';
                    setTimeout(function () {
                      document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                    },1200);
                  }
                },
      failure : function(){
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                            document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML = '<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                            setTimeout(function(){
                              document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML = '<b>OBSERVACIONES</b>';
                            },1200);
                          }
    });
  }

  //============================ BUSCAR ARTICULO =============================//
  function buscarArticulo<?php echo $opcGrillaContable; ?>(event,input){
    var contIdInput = (input.id).split('_')[1]
    ,   numero = input.value
    ,   tecla  = (input) ? event.keyCode : event.which;

    if(tecla == 13 && numero > 0){
      input.blur();
      ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(input.value, input.id);
      return true;
    }
    else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){
      return true;
    }

    patron = /[^\d]/;

    if(patron.test(numero)){
      input.value = numero.replace(patron,'');
    }

    if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
      document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value                 = 0;
      document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+contIdInput).value                   = '';
      document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value              = '';
      document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value             = '';
      document.getElementById('depreciacionAcumulada<?php echo $opcGrillaContable; ?>_'+contIdInput).value      = '';
      document.getElementById('depreciacionAcumuladaNiif<?php echo $opcGrillaContable; ?>_'+contIdInput).value  = '';
      document.getElementById('deterioroAcumulado<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = '';
      document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display   = 'block';
      document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display       = 'inline';
    }
    else if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
      document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value                = 0;
      document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+contIdInput).value                  = '';
      document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value             = '';
      document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value            = '';
      document.getElementById('depreciacionAcumulada<?php echo $opcGrillaContable; ?>_'+contIdInput).value     = '';
      document.getElementById('depreciacionAcumuladaNiif<?php echo $opcGrillaContable; ?>_'+contIdInput).value = '';
      document.getElementById('deterioroAcumulado<?php echo $opcGrillaContable; ?>_'+contIdInput).value        = '';
    }
    return true;
  }

  //======================== BUSCAR ARTICULO EN EL BD ========================//
  function ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(valor,input){
    var arrayIdInput = input.split('_');

    Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
      url     : 'traslados/bd/bd.php',
      scripts : true,
      nocache : true,
      params  : {
        opc               : 'buscarArticulo',
        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
        campo             : arrayIdInput[0],
        codigo_activo     : valor,
        idArticulo        : arrayIdInput[1],
        id                : '<?php echo $id_traslado; ?>'
      }
    });
  }

  //======================= VENTANA BUSCAR ACTIVO FIJO =======================//
  function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont){
    var id_sucursal = document.getElementById('filtro_sucursal_Traslados').value
    ,   id_bodega   = document.getElementById('filtro_ubicacion_Traslados').value
    ,   myalto      = Ext.getBody().getHeight()
    ,   myancho     = Ext.getBody().getWidth()
    ,   sql         = ` AND id_sucursal = ${id_sucursal} AND id_bodega = ${id_bodega}`;

    Win_Ventana_buscar_Articulo_factura = new Ext.Window({
      width       : myancho - 100,
      height      : myalto - 50,
      id          : 'Win_Ventana_buscar_Articulo_factura',
      title       : 'Seleccione el activo',
      modal       : true,
      autoScroll  : false,
      closable    : false,
      autoDestroy : true,
      autoLoad    : {
                      url     : 'bd/BusquedaInventarios.php',
                      scripts : true,
                      nocache : true,
                      params  : {
                                  sql           : sql,
                                  nombre_grilla : 'buscar_activo_fijo',
                                  nombreTabla   : 'activos_fijos',
                                  cargaFuncion  : 'responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,' + cont + ');',
                                  id_traslado   : '<?php echo $id_traslado; ?>',
                                  option        : 'traslados',
                                }
                    },
      tbar        : [
                      {
                        xtype       : 'button',
                        text        : 'Regresar',
                        scale       : 'large',
                        iconCls     : 'regresar',
                        iconAlign   : 'left',
                        handler     : function(){ Win_Ventana_buscar_Articulo_factura.close(id) }
                      }
                    ]
    }).show();
  }

  //====================== MOSTRAR ARTICULO EN PANTALLA ======================//
  function responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,cont){
    var costoTotal                  = 0
    ,   totalDescuento              = 0
    ,   idArticulo                  = document.getElementById('id_activo_'+id).innerHTML
    ,   codigo                      = document.getElementById('div_buscar_activo_fijo_codigo_activo_'+id).innerHTML
    ,   costo                       = document.getElementById('div_buscar_activo_fijo_costo_'+id).innerHTML
    ,   unidadMedida                = document.getElementById('unidad_medida_activo_'+id).innerHTML
    ,   depreciacion_acumulada      = document.getElementById('div_buscar_activo_fijo_depreciacion_acumulada_'+id).innerHTML
    ,   depreciacion_acumulada_niif = document.getElementById('div_buscar_activo_fijo_depreciacion_acumulada_niif_'+id).innerHTML
    ,   deterioroAcumulado          = document.getElementById('div_buscar_activo_fijo_deterioro_acumulado_'+id).innerHTML
    ,   nombreArticulo              = document.getElementById('div_buscar_activo_fijo_nombre_equipo_'+id).innerHTML

    document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value                = id;
    document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value               = codigo;
    document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value            = nombreArticulo;
    document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value                  = unidadMedida;
    document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value             = costo;
    document.getElementById('depreciacionAcumulada<?php echo $opcGrillaContable; ?>_'+cont).value     = depreciacion_acumulada;
    document.getElementById('depreciacionAcumuladaNiif<?php echo $opcGrillaContable; ?>_'+cont).value = depreciacion_acumulada_niif;
    document.getElementById('deterioroAcumulado<?php echo $opcGrillaContable; ?>_'+cont).value        = deterioroAcumulado;

    Win_Ventana_buscar_Articulo_factura.close();
    return;
  }

  //========================= GUARDAR NUEVO ARTICULO =========================//
  function guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont){
    var idInsertArticulo          = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_' + cont).value //VARIABLE PARA IDENTIFICAR SI EL ACTIVO YA ESTA GUARDADO 1 = SI || 0 = NO
    ,   idInventario              = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_' + cont).value //ID ACTIVO FIJO
    ,   sucursalOrigen            = document.getElementById('filtro_sucursal_<?php echo $opcGrillaContable; ?>').value //ID SUCURSAL ORIGEN
    ,   opc                       = ''
    ,   divRender                 = ''
    ,   accion                    = ''

    //SI EL CODIGO DEL ACTIVO NO EXISTE
    if(idInventario == 0){
      alert('El campo articulo es Obligatorio');
      setTimeout(function(){
        document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_' + cont).focus();
      },20);
      return;
    }

    //SI EL ACTIVO YA HABIA SIDO GUARDADO
    if(idInsertArticulo > 0){
      opc       = 'actualizaArticulo';
      divRender = 'renderArticulo<?php echo $opcGrillaContable; ?>_' + cont;
      accion    = 'actualizar';
    }
    //SI EL ACTIVO NO HA SIDO GUARDADO LA PRIMERA VEZ
    else{
      //VALIDAMOS PARA NO REPETIR FILAS DE LAN GRILLA
      contArticulos<?php echo $opcGrillaContable; ?>++;

      opc       = 'guardarArticulo';
      divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_' + contArticulos<?php echo $opcGrillaContable; ?>;
      accion    = 'agregar';

      var div   = document.createElement('div');
      div.setAttribute('id','bodyDivArticulos<?php echo $opcGrillaContable; ?>_' + contArticulos<?php echo $opcGrillaContable; ?>);
      div.setAttribute('class','bodyDivArticulos<?php echo $opcGrillaContable; ?>');
      document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').appendChild(div);
    }

    Ext.get(divRender).load({
      url     : 'traslados/bd/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc                       : opc, //FUNCION QUE SE BUSCARA EN EL ARCHIVO BD
                  idTraslado                : '<?php echo $id_traslado; ?>', //ID DEL TRASLADO
                  idArticulo                : idInventario, //ID DEL ACTIVO FIJO
                  idSucursal                : sucursalOrigen,
                  consecutivo               : contArticulos<?php echo $opcGrillaContable; ?>, //CONTADOR DE LA PROXIMA FILA
                  cont                      : cont, //CONTADOR DE LA FILA ACTUAL
                  opcGrillaContable         : '<?php echo $opcGrillaContable; ?>', //NOMBRE DE LA GRILLA
                  tablaInventario           : '<?php echo $tablaInventario; ?>', //NOMBRE DE LA TABLA DE INVENTARIO
                  idInsertArticulo          : idInsertArticulo //ID DEL DETALLE DEL TRASLADO
                }
    });

    //despues de registrar el primer articulo, habilitamos boton nuevo
    Ext.getCmp("btnNueva<?php echo $opcGrillaContable; ?>").enable();

    //llamamos la funcion para calcular los totales de la facturan si accion = agregar
    // if (accion=="agregar") { calculaValorTotalesDocumento(accion,valorDeterioro); }
  }

  //============================= BORRAR ARTICULO ============================//
  function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){
    var idArticulo = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

    if(confirm('\u00BFEsta seguro de eliminar este articulo del documento?')){
      Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
        url     : 'traslados/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
          opc               : 'deleteArticulo',
          opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
          idArticulo        : idArticulo,
          cont              : cont,
          id                : '<?php echo $id_traslado; ?>',
          tablaInventario   : '<?php echo $tablaInventario; ?>',
          idTablaPrincipal  : '<?php echo $idTablaPrincipal; ?>'
        }
      });
    }
  }

  //===================== RETROCEDER ARTICULO MODIFICADO =====================//
  function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
    //Capturamos el id que esta asignado en la variable oculta
    idArticulo = document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_" + cont).value;

    Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
      url     : 'traslados/bd/bd.php',
      scripts : true,
      nocache : true,
      params  : {
        opc               : 'retrocederArticulo',
        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
        idArticulo        : idArticulo,
        cont              : cont,
        id                : '<?php echo $id_traslado; ?>',
        tablaInventario   : '<?php echo $tablaInventario; ?>',
        idTablaPrincipal  : '<?php echo $idTablaPrincipal; ?>'
      }
    });
  }

  //=================== VALIDAR DOCUMENTO ANTES DE GENERAR ===================//
  function validar<?php echo $opcGrillaContable; ?>(){
    codigoTercero     = document.getElementById("codigoTercero<?php echo $opcGrillaContable; ?>").value;
    nitTercero        = document.getElementById("nitTercero<?php echo $opcGrillaContable; ?>").value;
    nombreTercero     = document.getElementById("nombreTercero<?php echo $opcGrillaContable; ?>").value;
    sucursal_destino  = document.getElementById("sucursal_destino").value;
    bodega_destino    = document.getElementById("bodega_destino").value;

    if(codigoTercero == ''){
      alert("El codigo del tercero es obligatorio.");
      return;
    }

    if(nitTercero == ''){
      alert("El numero de documento del tercero es obligatorio.");
      return;
    }

    if(nombreTercero == ''){
      alert("El nombre del tercero es obligatorio.");
      return;
    }

    if(sucursal_destino == ''){
      alert("La sucursal de destino es obligatoria.");
      return;
    }

    if(bodega_destino == ''){
      alert("La bodega de destino es obligatoria.");
      return;
    }

    //VALIDAR SI HAY ARTICULOS POR GUARDAR
    var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();

    if(validacion == 0){
      alert("No hay activos para generar el documento");
      return;
    }
    else if(validacion == 1){
      alert("Hay activos pendientes por guardar");
      return;
    }
    else if(validacion == 2){
      var observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;

      cargando_documentos('Generando Documento...');

      Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
        url     : 'traslados/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc               : 'terminarGenerar',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_traslado; ?>',
                    tablaPrincipal    : '<?php echo $tablaPrincipal; ?>',
                    tablaInventario   : '<?php echo $tablaInventario; ?>',
                    sucursal_destino  : sucursal_destino,
                    bodega_destino    : bodega_destino
                  }
      });
    }
  }

  //============================== BOTON BUSCAR ==============================//
  function buscar<?php echo $opcGrillaContable; ?>(){
    var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();

    if(validacion == 1){
      if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
    }
    else if(validacion == 2 || validacion == 0){
      ventanaBuscar<?php echo $opcGrillaContable; ?>();
    }
  }

  //====================== VENTANA PARA BUSCAR TRASLADOS =====================//
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
      autoLoad    : {
                      url     : 'traslados/bd/buscarGrillaContable.php',
                      scripts : true,
                      nocache : true,
                      params  : {
                                  opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                                  filtro_sucursal   : document.getElementById("filtro_sucursal_<?php echo $opcGrillaContable; ?>").value
                                }
                    },
      tbar        : [
                      {
                        xtype   : 'buttongroup',
                        columns : 3,
                        title   : 'Opciones',
                        items   : [
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

  //================== VALIDAR SI HAY ARTICULOS POR GUARDAR ==================//
  function validarArticulos<?php echo $opcGrillaContable; ?>(){
    var cont    = 0
    , contTotal = 0   //Total de articulos en el detalle
    , contArticulo    //Identificador de la fila
    , divsArticulos<?php echo $opcGrillaContable; ?> = document.querySelectorAll(".bodyDivArticulos<?php echo $opcGrillaContable; ?>");

    for(i in divsArticulos<?php echo $opcGrillaContable; ?>){
      if(typeof(divsArticulos<?php echo $opcGrillaContable; ?>[i].id) != 'undefined'){
        contTotal++;
        contArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[1]

        if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).value > 0
        && document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == "images/save_true.png"
        || document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == "images/reload.png"
        && document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contArticulo).style.display == "inline"){
          cont++;
        }

      }
    }

    if(contTotal == 1 && cont == 0){
      return 0;
    } else{
      if(cont > 0){
        return 1;
      } else if(cont == 0){
        return 2;
      }
    }

  }

  //========================== CANCELAR UN DOCUMENTO =========================//
  function cancelar<?php echo $opcGrillaContable; ?>(){
    var contArticulos = 0;

    if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }

    arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');
    for(i in arrayIdsArticulos){
      if(arrayIdsArticulos[i].innerHTML != '' ){
        contArticulos++;
      }
    }

    if(contArticulos > 0){
      if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
        cargando_documentos('Cancelando Documento...');
        Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
          url     : 'traslados/bd/bd.php',
          scripts : true,
          nocache : true,
          params  : {
            opc               : 'cancelarDocumento',
            id                : '<?php echo $id_traslado; ?>',
            opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            idBodega          : '<?php echo $filtro_bodega; ?>'
          }
        });
      };
    }
  }
</script>
