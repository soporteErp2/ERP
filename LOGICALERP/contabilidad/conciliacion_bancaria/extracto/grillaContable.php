<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");
  include("../config_var_global.php");
  require("bd/functions_body_article.php");
  include("../../../funciones_globales/funciones_php/randomico.php");

  $id_empresa        = $_SESSION['EMPRESA'];
  $id_sucursal       = $_SESSION['SUCURSAL'];
  $nombre_sucursal   = $_SESSION['NOMBRESUCURSAL'];
  $id_usuario        = $_SESSION['IDUSUARIO'];
  $documento_usuario = $_SESSION['CEDULAFUNCIONARIO'];
  $nombre_usuario    = $_SESSION['NOMBREFUNCIONARIO'];
  $bodyArticle       = '';
  $acumScript        = '';
  $estado            = '';
  $fecha             = date('Y-m-d');
  $exento_iva        = '';
?>
<script>
    //Variables para calcular los valores de los costos y totales de la factura
    var subtotal<?php echo $opcGrillaContable; ?>           = 0.00
    ,   subtotalDetalle<?php echo $opcGrillaContable; ?>    = 0.00
    ,   total<?php echo $opcGrillaContable; ?>              = 0.00
    ,   contDetalles<?php echo $opcGrillaContable; ?>       = 1
    ,   id_tercero_<?php echo $opcGrillaContable;?>         = 0
    ,   timeOutObservacion<?php echo $opcGrillaContable; ?> = ''
    ,   codigoTercero<?php echo $opcGrillaContable; ?>      = 0
    ,   nitTercero<?php echo $opcGrillaContable; ?>         = 0
    ,   nombreTercero<?php echo $opcGrillaContable; ?>      = ''
    ,   nombre_grilla                                       = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';

    //Bloqueo todos los botones
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();
</script>
<?php
  $acumScript .= (user_permisos(6,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';   //Guardar
  $acumScript .= (user_permisos(8,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';  //Cancelar

  //================ SI NO EXISTE EXTRACTO SE CREA EL ID UNICO ===============//
  if(!isset($id_documento)){
    //CONSULTAR LAS SUCURSALES
    $sql   = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
    $query = $mysql->query($sql,$mysql->link);

    while ($row=$mysql->fetch_array($query)) {
      $sucursales .= '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
    }

    // CREACION DEL ID UNICO
    $random_documento = responseUnicoRanomico();

    $sqlInsert = "INSERT INTO $tablaPrincipal (id_empresa,random,fecha_extracto,id_sucursal,id_usuario,documento_usuario,nombre_usuario,sucursal)
                  VALUES('$id_empresa','$random_documento','$fecha','$id_sucursal',$id_usuario,'$documento_usuario','$nombre_usuario','$nombre_sucursal')";

    $queryInsert = mysql_query($sqlInsert,$link);

    $sqlSelectId  = "SELECT id FROM $tablaPrincipal  WHERE random='$random_documento' LIMIT 0,1";
    $id_documento = mysql_result(mysql_query($sqlSelectId,$link),0,'id');

    $acumScript .= 'new Ext.form.DateField({
                        format     : "Y-m-d",
                        width      : 120,
                        allowBlank : false,
                        showToday  : false,
                        applyTo    : "fecha_form'.$opcGrillaContable.'",
                        editable   : false,
                        listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value); } }
                    });
                    document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";';

    $bodyArticle = cargaArticulosSave($saldo_extracto,$tablaPrincipal,$id_documento,$observacion,$estado,$opcGrillaContable,$idTablaPrincipal,$id_empresa,$link);
  }
  //========================== SI EXISTE EL EXTRACTO =========================//
  else{
    $sql = "SELECT
              fecha_extracto,
              id_tercero,
              documento_tercero,
              tercero,
              id_cuenta,
              cuenta,
              descripcion_cuenta,
              saldo_extracto,
              id_usuario,
              documento_usuario,
              nombre_usuario,
              estado,
              observacion,
              consecutivo,
              id_sucursal
            FROM
              $tablaPrincipal
            WHERE
              id = '$id_documento'";

    $query = mysql_query($sql,$link);

    $fecha_extracto     = mysql_result($query,0,'fecha_extracto');
    $id_tercero         = mysql_result($query,0,'id_tercero');
    $documento_tercero  = mysql_result($query,0,'documento_tercero');
    $tercero            = mysql_result($query,0,'tercero');
    $id_cuenta          = mysql_result($query,0,'id_cuenta');
    $cuenta             = mysql_result($query,0,'cuenta');
    $descripcion_cuenta = mysql_result($query,0,'descripcion_cuenta');
    $saldo_extracto     = mysql_result($query,0,'saldo_extracto');
    $id_usuario         = mysql_result($query,0,'id_usuario');
    $documento_usuario  = mysql_result($query,0,'documento_usuario');
    $nombre_usuario     = mysql_result($query,0,'nombre_usuario');
    $estado             = mysql_result($query,0,'estado');
    $observacion        = mysql_result($query,0,'observacion');
    $consecutivo        = mysql_result($query,0,'consecutivo');
    $codigo_sucursal    = mysql_result($query,0,'id_sucursal');

    $arrayReplaceString = array("\n","\r","<br>");
    $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    //CONSULTAR LAS SUCURSALES
    $sql   = "SELECT id,nombre FROM empresas_sucursales WHERE activo = 1 AND id_empresa = $id_empresa";
    $query = $mysql->query($sql,$mysql->link);

    while($row = $mysql->fetch_array($query)){
      if($row['id'] == $codigo_sucursal){
        $sucursales .= '<option value="' . $row['id'] . '" selected="selected">' . $row['nombre'] . '</option>';
      } else{
        $sucursales .= '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
      }
    }

    $acumScript .= 'new Ext.form.DateField({
                      format     : "Y-m-d",
                      width      : 120,
                      allowBlank : false,
                      showToday  : false,
                      applyTo    : "fecha_form'.$opcGrillaContable.'",
                      editable   : false,
                      value      : new Date(),
                      listeners  : { select: function() { UpdateFecha'.$opcGrillaContable.'(this.value); } }
                    });

                    document.getElementById("fecha_form'.$opcGrillaContable.'").value           = "'.$fecha_extracto.'";
                    document.getElementById("cuenta'.$opcGrillaContable.'").value               = "'.$cuenta.'";
                    document.getElementById("descripcion_cuenta'.$opcGrillaContable.'").value   = "'.$descripcion_cuenta.'";
                    document.getElementById("saldo_extracto'.$opcGrillaContable.'").value       = "'.$saldo_extracto.'";
                    document.getElementById("nitTercero'.$opcGrillaContable.'").value           = "'.$documento_tercero.'";
                    document.getElementById("nombreTercero'.$opcGrillaContable.'").value        = "'.$tercero.'";
                    document.getElementById("nombre_usuario'.$opcGrillaContable.'").value       = "'.$nombre_usuario.'";
                    document.getElementById("observacion'.$opcGrillaContable.'").value          = "'.$observacion.'";
                    document.getElementById("titleDocumento'.$opcGrillaContable.'").style.color = "#333";
                    calcTotalExtrac("sumar",0,'.$saldo_extracto.');';

    $bodyArticle = cargaArticulosSave($saldo_extracto,$tablaPrincipal,$id_documento,$observacion,$estado,$opcGrillaContable,$idTablaPrincipal,$id_empresa,$link);
  }
?>
<div class="contenedorExtractos" id="contenedorExtractos">
  <div class="bodyTop">
    <div class="contInfoFact">
      <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
      <div class="contTopFila">
        <div class="renglonTop">
          <div class="labelTop">Sucursal</div>
           <div id="rendersucursal<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop" >
            <select  onchange="guardarSucursal(this.value)" id="sucursal<?php echo $opcGrillaContable; ?>" >
              <optgroup label="Todas las sucursales">
                <option value="todas">Todas las Sucursales</option>
              </optgroup>
              <optgroup label="Sucursales">
                <?php echo $sucursales ?>
              </optgroup>
            </select>
          </div>
        </div>
        <div class="renglonTop2" >
          <div class="labelTop">Fecha Extracto</div>
          <div id="renderfecha_extracto<?php echo $opcGrillaContable; ?>" style="float:left; width:20px; overflow:hidden;"></div>
          <div class="campoTop" >
            <input class="input_extractos"  type="text" id="fecha_form<?php echo $opcGrillaContable; ?>" />
            <div id="renderSelectFormaPago<?php echo $opcGrillaContable; ?>" style="float:left;display:none;"></div>
          </div>
        </div>
        <div class="renglonTop" style="width:290px;">
          <div class="labelTop" style="float:left; width:100%;">Cuenta Contable</div>
          <div id="renderCuenta<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop">
            <input class="input_extractos" type="text" id="cuenta<?php echo $opcGrillaContable; ?>" style="width:22% !important; float:left;"  onkeyup="buscarCuenta<?php echo $opcGrillaContable; ?>(event,this)" >
            <div style="width:10% !important; float:left;background-color:#F3F3F3; height:100%; text-align:center;">-</div>
            <input class="input_extractos" type="text" id="descripcion_cuenta<?php echo $opcGrillaContable; ?>" style="width:68% !important; float:left;" Readonly>
          </div>
          <div class="iconBuscarProveedor" onclick="ventanaBuscarCuenta<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Tercero">
            <img src="img/buscar20.png"/>
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Saldo Extracto</div>
          <div class="campoTop"><input class="input_extractos" type="text" onkeyup="guardarSaldoExtracto(event,this)" id="saldo_extracto<?php echo $opcGrillaContable; ?>" /></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Nit</div>
          <div class="campoTop"><input class="input_extractos" type="text" onchange="buscarTercero<?php echo $opcGrillaContable; ?>(event,this)" id="nitTercero<?php echo $opcGrillaContable; ?>" onkeyup="buscarTercero<?php echo $opcGrillaContable; ?>(event,this)"/></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Empresa</div>
          <div id="renderfecha_tercero<?php echo $opcGrillaContable; ?>" style="width:20px; overflow:hidden;height: 20px;margin-top: -20px;"></div>
          <div class="campoTop" style="width:277px;"><input class="input_extractos" type="text" id="nombreTercero<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
          <div class="iconBuscarProveedor" onclick="buscarVentanaTercero<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Tercero">
            <img src="img/buscar20.png"/>
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Usuario</div>
          <div class="campoTop" style="width:271px;"><input class="input_extractos" type="text" id="nombre_usuario<?php echo $opcGrillaContable; ?>" Readonly value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
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
    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();  //disable btn imprimir

    //=========================== GUARDAR TERCERO ============================//
    function guardarSucursal(id_sucursal){
      select_sucursal = document.getElementById("sucursal<?php echo $opcGrillaContable; ?>");
      codigo_sucursal = select_sucursal.value;
      nombre_sucursal = select_sucursal.options[select_sucursal.selectedIndex].text;

      Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
        url     : 'conciliacion_bancaria/extracto/bd/bd.php',
        scripts : true,
        nocache : true,
        params  :
        {
          opc               : 'guardarSucursal',
          opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
          id_documento      : '<?php echo $id_documento; ?>',
          codigo_sucursal   : codigo_sucursal,
          nombre_sucursal   : nombre_sucursal
        }
      });
    }

    //======================== GUARDAR SALDO EXTRACTO ========================//
    function guardarSaldoExtracto(event,input) {
      var tecla   = input? event.keyCode : event.which
      ,   value   = input.value;

      if(tecla == 13){
        Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
          url     : 'conciliacion_bancaria/extracto/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
            opc               : 'guardarSaldoExtracto',
            opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            id_documento      : '<?php echo $id_documento; ?>',
            saldo             : value,
            tablaPrincipal    : '<?php echo $tablaPrincipal; ?>'
          }
        });
      }
    }

    //=========================== ACTUALIZAR FECHA ===========================//
    function UpdateFecha<?php echo $opcGrillaContable; ?>(fecha) {
        Ext.get("renderfecha_extracto<?php echo $opcGrillaContable; ?>").load({
               url     : 'conciliacion_bancaria/extracto/bd/bd.php',
               scripts : true,
               nocache : true,
               params  :
               {
                    opc               : 'UpdateFecha',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_documento      : '<?php echo $id_documento; ?>',
                    fecha             : fecha
               }
           });
    }

    //============================ BUSCAR TERCERO ============================//
    function buscarTercero<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla   = Input ? event.keyCode : event.which
        ,   numero  = Input.value;

        if(Input.value != '' && id_tercero_<?php echo $opcGrillaContable;?> == 0 && (tecla == 13 )){
            Input.blur();
            ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(Input.value);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        else if(id_tercero_<?php echo $opcGrillaContable;?>>0 && contDetalles<?php echo $opcGrillaContable; ?>>1){
            Input.blur();
            // if(confirm('Esta seguro de cambiar de Tercero y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){
            //     ajaxCambiaTercero<?php echo $opcGrillaContable; ?>(Input);
            // }
            // else{
                document.getElementById("nitTercero<?php echo $opcGrillaContable; ?>").value    = nitTercero<?php echo $opcGrillaContable; ?>;
                document.getElementById("codTercero<?php echo $opcGrillaContable; ?>").value    = codigoTercero<?php echo $opcGrillaContable; ?>;
                document.getElementById("nombreTercero<?php echo $opcGrillaContable;?>").value  = nombreTercero<?php echo $opcGrillaContable; ?>;
            // }
        }
        else if(id_tercero_<?php echo $opcGrillaContable;?>>0){
            ajaxCambiaTercero<?php echo $opcGrillaContable; ?>(Input);
        }
        return true;
    }

    function ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(codTercero){
        Ext.get('renderfecha_tercero<?php echo $opcGrillaContable; ?>').load({
            url     : 'conciliacion_bancaria/extracto/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarTercero',
                codTercero        : codTercero,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    function ajaxCambiaTercero<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreTercero<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="renderFilasArticulo" id="renderfecha_tercero<?php echo $opcGrillaContable; ?>"></div>';
        // if(Input.id != 'codTercero<?php echo $opcGrillaContable; ?>'){ document.getElementById("codTercero<?php echo $opcGrillaContable; ?>").value = ''; }
        // if(Input.id != 'nitTercero<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitTercero<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor si es una factura
        if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {
            var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
            for(i in checks){ checks[i].checked=false; checks[i].checked=false; }
        }
        Ext.get('renderfecha_tercero<?php echo $opcGrillaContable; ?>').load({
            url     : 'conciliacion_bancaria/extracto/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cambiaTercero',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_documento; ?>'
            }
        });
    }

    //============================ BUSCAR TERCERO ============================//
    function buscarVentanaTercero<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        // var sql = 'AND tipo_Tercero = \"Si\"';

        Win_VentanaTercero_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaTercero_<?php echo $opcGrillaContable; ?>',
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
                    // sql           : sql,
                    cargaFuncion  : 'renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id);',
                    nombre_grilla : 'Tercero<?php echo $opcGrillaContable; ?>'
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
                    handler     : function(){ Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id){
        var numero_documento = document.getElementById('div_TerceroExtractos_numero_identificacion_'+id).innerHTML;
        ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(numero_documento);
        Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close();
        return;

        if(id != id_tercero_<?php echo $opcGrillaContable;?> && contDetalles<?php echo $opcGrillaContable; ?>>1){
            if(!confirm('Esta seguro de cambiar de Tercero y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){ Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close(); return; }
        }
        else if (id == id_tercero_<?php echo $opcGrillaContable;?>){ Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close(); return; }

        ajaxCambiaTercero<?php echo $opcGrillaContable; ?>(document.getElementById("codTercero<?php echo $opcGrillaContable; ?>"));
        id_tercero_<?php echo $opcGrillaContable;?> = id;
        contDetalles<?php echo $opcGrillaContable; ?>  = 1;

        Win_VentanaTercero_<?php echo $opcGrillaContable; ?>.close();
        ajaxbuscarTercero<?php echo $opcGrillaContable; ?>(id,'idTercero<?php echo $opcGrillaContable; ?>');
    }

    //============================= BUSCAR CUENTA ============================//
    function ventanaBuscarCuenta<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_cuenta_extracto = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_cuenta_extracto',
            title       : 'Seleccionar Cuenta ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/buscar_cuenta_puc.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrilla    : 'puc',
                    tabla_puc    : 'puc',
                    cargaFuncion : "responseVentanaBuscarCuenta<?php echo $opcGrillaContable; ?>(id)",
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
                    handler     : function(){ Win_Ventana_buscar_cuenta_extracto.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarCuenta<?php echo $opcGrillaContable; ?>(id){

        var cuenta      = document.getElementById('div_puc_cuenta_'+id).innerHTML
        document.getElementById('cuenta<?php echo $opcGrillaContable; ?>').value = cuenta;

        Ext.get('renderCuenta<?php echo $opcGrillaContable; ?>').load({
                url     : 'conciliacion_bancaria/extracto/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscarCuenta',
                    cuenta            : cuenta,
                    id_documento      : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                }
            });

        Win_Ventana_buscar_cuenta_extracto.close(id);
    }

    function buscarCuenta<?php echo $opcGrillaContable; ?>(event,input) {
        var tecla  = input? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            Ext.get('renderCuenta<?php echo $opcGrillaContable; ?>').load({
                url     : 'conciliacion_bancaria/extracto/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscarCuenta',
                    cuenta            : value,
                    id_documento      : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                }
            });
        }
    }

    function changeInput(event,Input,opc,cont) {
      var tecla            = Input ? event.keyCode : event.which
      ,   numero           = Input.value
      ,   idRegistro       = document.getElementById('idRegistro<?php echo $opcGrillaContable; ?>_'+cont).value;
      if(idRegistro != 0){
        document.getElementById('deleteDetalleExtractos_'+cont).style = "width:20px; float:left; margin-top:3px;cursor:pointer;display:inline;";
        document.getElementById('divImageDeshacerExtractos_'+cont).style = "width:20px; float:left; margin-top:3px;cursor:pointer;display:inline;";
        document.getElementById('divImageDeshacerExtractos_'+cont).setAttribute('title','Devolver Cambios');
        document.getElementById('divImageSaveExtractos_'+cont).style = "width:20px; float:left; margin-top:3px;cursor:pointer;display:inline;";
        document.getElementById('divImageSaveExtractos_'+cont).setAttribute('title','Actualizar Cambios');
        document.getElementById('imgSaveDetalleExtractos_'+cont).setAttribute('src','img/reload.png');
      }
      if(tecla == 13 ){
        switch(opc){
          case 'guardar':
            Input.blur();
            guardarNewRegistro<?php echo $opcGrillaContable; ?>(cont);
          break
          default :
            document.getElementById(opc).focus();
        }
      }
    }

    function guardarNewRegistro<?php echo $opcGrillaContable; ?>(cont){

        var idInsertRegistro = document.getElementById('idInsertRegistro<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   idDetalle        = document.getElementById('idDetalle<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tipo             = document.getElementById('tipo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   numeroDocumento  = document.getElementById('numeroDocumento<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   fecha            = document.getElementById('fecha<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   valor            = document.getElementById('valor<?php echo $opcGrillaContable; ?>_'+cont).value;

        //Validar tipo de documento
        if (tipo == "Cheque" ){
        }else if(tipo == "Consignacion"){
        }else if(tipo == "Nota Debito"){
        }else if(tipo == "Nota Credito"){
        }else{
          alert("Tipo de documento no soportado");
          return;
        }

        //Validar fecha
        if(fecha == '' || fecha == 'Anio-mes-dia'){
          alert("Aviso\nDigite la fecha de la transaccion");
          document.getElementById('fecha<?php echo $opcGrillaContable; ?>_'+cont).focus();
          return;
        }

        //Validar valor del extracto
        if(valor == '' || valor == 0){
          alert("Aviso\nDigite el valor de la transaccion");
          document.getElementById('valor<?php echo $opcGrillaContable; ?>_'+cont).focus();
          return;
        }

        if(idInsertRegistro > 0){
          opc       = 'actualizaDetalle';
          divRender = 'renderDetalle<?php echo $opcGrillaContable; ?>_'+cont;
        } else{
          cont++;
          (cont > 1)? contDetalles<?php echo $opcGrillaContable; ?> = cont - 1 : contDetalles<?php echo $opcGrillaContable; ?>;
          opc       = 'guardarDetalle';
          divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+cont;
          div       = document.createElement('div');
          div.setAttribute('class','bodyDivArticulos');
          div.setAttribute('id','bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+cont);
          document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').appendChild(div);
        }

        Ext.get(divRender).load({
          url     : 'conciliacion_bancaria/extracto/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
            opc               : opc,
            id                : '<?php echo $id_documento; ?>',
            opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            consecutivo       : contDetalles<?php echo $opcGrillaContable; ?>,
            cont              : cont,
            idInsertRegistro  : idInsertRegistro,
            idDetalle         : idDetalle,
            tipo              : tipo,
            numeroDocumento   : numeroDocumento,
            fecha             : fecha,
            valor_extracto    : valor
          }
        });

        //Despues de registrar el primer articulo, habilitamos boton nuevo
        Ext.getCmp("btnNueva<?php echo $opcGrillaContable; ?>").enable();
    }

    function deleteDetalle<?php echo $opcGrillaContable; ?>(cont){
      var idDetalle       = document.getElementById('idDetalle<?php echo $opcGrillaContable; ?>_'+cont).value
      ,   tipo            = document.getElementById('tipo<?php echo $opcGrillaContable; ?>_'+cont).value
      ,   numeroDocumento = document.getElementById('numeroDocumento<?php echo $opcGrillaContable; ?>_'+cont).value
      ,   fecha           = document.getElementById('fecha<?php echo $opcGrillaContable; ?>_'+cont).value;

      if(confirm('\u00BFEsta Seguro de eliminar este articulo de la factura de compra?')){
        Ext.get('renderDetalle<?php echo $opcGrillaContable; ?>_'+cont).load({
          url     : 'conciliacion_bancaria/extracto/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
            idDetalle         : idDetalle,
            cont              : cont,
            id                : '<?php echo $id_documento; ?>',
            opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            opc               : 'deleteDetalle'
          }
        });
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
                url     : 'conciliacion_bancaria/extracto/bd/bd.php',
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
        observacion     = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacion<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'conciliacion_bancaria/extracto/bd/bd.php',
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

    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===============================================//
    function retrocederRegistro<?php echo $opcGrillaContable; ?>(cont){
      //Capturamos el id del registro del detalle
      idDetalle = document.getElementById("idDetalle<?php echo $opcGrillaContable; ?>_"+cont).value;

      Ext.get('renderDetalle<?php echo $opcGrillaContable; ?>_'+cont).load({
        url     : 'conciliacion_bancaria/extracto/bd/bd.php',
        scripts : true,
        nocache : true,
        params  :
        {
          opc               : 'retrocederRegistro',
          cont              : cont,
          opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
          idDetalle         : idDetalle,
          id                : '<?php echo $id_documento; ?>'
        }
      });
    }

    //=========================== GENERAR DOCUMENTO ==========================//
    function guardar<?php echo $opcGrillaContable; ?>(){

      //VALIDAR QUE LOS DATOS DE LA CABECERA ESTEN LLENOS
      inputNull = 0;
      $('.input_extractos').each(function(i, obj){
        if(obj.value == ""){
          inputNull++;
        }
      });
      if(inputNull > 0){
        alert("Faltan datos por completar en la cabecera del documento.")
        generar = 'false';
        return;
      } else{
        generar = 'true';
      }

      //VALIDAR QUE EXISTAN ARTICULOS GUARDADOS
      if($(".bodyDivArticulos").length > 1){
        generar = 'true';
      } else{
        alert("No hay detalles guardados en este extracto.")
        generar = 'false';
      }

      //VALIDAR QUE NO EXISTAN DIFERENCIAS ENTRE EL EXTRACTO Y EL DETALLE
      diferenciaExtracto = parseFloat(document.getElementById("totalAcumulado<?php echo $opcGrillaContable?>").innerHTML);
      if(diferenciaExtracto == 0.00 || diferenciaExtracto == 0){
        generar = 'true';
      } else{
        generar = 'false';
        alert("No se puede generar el documento porque la diferencia del extracto es mayor o menor a cero(0).");
      }

      //GENERAR DOCUMENTO
      if(generar != 'false'){
        var observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
        Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
          url     : 'conciliacion_bancaria/extracto/bd/bd.php',
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

    //=============================== BUSCAR =================================//
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
                url     : 'conciliacion_bancaria/extracto/bd/buscarGrillaContable.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    tablaPrincipal    : '<?php echo $opcGrillaContable=="Extractos" ? "extractos":"conciliaciones"; ?>'

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

    //====================== VALIDAR ARTICULOS GUARDADOS =====================//
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
            }

        }
        if(contTotal==1 || contTotal==0){  return 0; }      //no se han almacenado articulos
        else if(cont > 0){ return 1; }      //si hay articulos pendientes por guardar o actualizar
        else { return 2; }                  //ok
    }

    function nueva<?php echo $opcGrillaContable; ?>(){
      Ext.get("contenedorExtractos").load({
        url     : 'conciliacion_bancaria/extracto/grillaContable.php',
        scripts : true,
        nocache : true,
        params  :
        {
          id                : '<?php echo $id_documento; ?>',
          opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
        }
      });
    }

    //======================== CANCELAR UN DOCUMENTO =========================//
    function cancelar<?php echo $opcGrillaContable; ?>(){
        var contDetalles = 0;

        if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ alert('El documento esta en blanco, no hay nada para cancelar'); return; }

        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');
        for(i in arrayIdsArticulos){if(arrayIdsArticulos[i].innerHTML != '' ){ contDetalles++; } }

        if(contDetalles > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){

                Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                    url     : 'conciliacion_bancaria/extracto/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc               : 'cancelarDocumento',
                        id                : '<?php echo $id_documento; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>'

                    }
                });
            };
        }
    }

    //========================= GUARDAR OBSERVACION ==========================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
      document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
      tecla = (input)? event.keyCode : event.which;
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
      observacion     = observacion.replace(/[\#\<\>\'\"]/g, '');
      clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
      timeOutObservacion<?php echo $opcGrillaContable; ?> = '';

      Ext.Ajax.request({
          url     : 'conciliacion_bancaria/extracto/bd/bd.php',
          params  :
          {
              opc               : 'guardarObservacion',
              id                : '<?php echo $id_documento; ?>',
              tablaPrincipal    : '<?php echo $tablaPrincipal; ?>',
              observacion       : observacion,
              opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
          },
          success : function(result, request){
                      if(result.responseText == 'false'){
                        document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                        setTimeout(function () {
                          document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b>';
                        },1200);
                        document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value = observacion<?php echo $opcGrillaContable; ?>;
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
</script>
