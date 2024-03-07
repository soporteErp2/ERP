<?php
  include("../../../../../configuracion/conectar.php");
  include("../../../../../configuracion/define_variables.php");
  require("../../config_var_global.php");
  require("functions_body_article.php");
  include("../../../../funciones_globales/funciones_php/randomico.php");
  include("../../../../funciones_globales/funciones_javascript/totalesNotaContable.php");

  if($opcGrillaContable == 'Extractos'){
    $tablaPrincipal = 'extractos';
  }

  $id_empresa  = $_SESSION['EMPRESA'];
  $id_sucursal = $_SESSION['SUCURSAL'];
  $bodyArticle = '';
  $acumScript  = '';
  $estado      = '';
  $fecha       = date('Y-m-d');
?>
<script>
  //Variables para calcular los valores de los costos y totales de la factura
  var subtotal<?php echo $opcGrillaContable; ?>            = 0.00
  ,   subtotalDetalle<?php echo $opcGrillaContable; ?>     = 0.00
  ,   total<?php echo $opcGrillaContable; ?>               = 0.00
  ,   contArticulos<?php echo $opcGrillaContable; ?>       = 1
  ,   id_cliente_<?php echo $opcGrillaContable;?>          = 0
  ,   timeOutObservacion<?php echo $opcGrillaContable; ?>  = ''
  ,   codigoCliente<?php echo $opcGrillaContable; ?>       = 0
  ,   total_<?php echo $opcGrillaContable; ?>              = 0
  ,   nitCliente<?php echo $opcGrillaContable; ?>          = 0
  ,   nombreCliente<?php echo $opcGrillaContable; ?>       = ''
  ,   nombre_grilla                                        = 'ventanaBucarCuenta<?php echo $opcGrillaContable; ?>';

  Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").enable();
  Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").enable();
  Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").enable();
  Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
  Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();
</script>
<?php
  $sql = "SELECT
            id,
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
            sucursal
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
  $sucursal           = mysql_result($query,0,'sucursal');

  $arrayReplaceString = array("\n", "\r","<br>");
  $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

  $acumScript .= 'document.getElementById("fecha'.$opcGrillaContable.'").value              = "'.$fecha_extracto.'";
                  document.getElementById("cuenta'.$opcGrillaContable.'").value             = "'.$cuenta.'";
                  document.getElementById("descripcion_cuenta'.$opcGrillaContable.'").value = "'.$descripcion_cuenta.'";
                  document.getElementById("saldo_extracto'.$opcGrillaContable.'").value     = "'.$saldo_extracto.'";
                  document.getElementById("nitTercero'.$opcGrillaContable.'").value         = "'.$documento_tercero.'";
                  document.getElementById("nombreTercero'.$opcGrillaContable.'").value      = "'.$tercero.'";
                  document.getElementById("nombre_usuario'.$opcGrillaContable.'").value     = "'.$nombre_usuario.'";
                  document.getElementById("observacion'.$opcGrillaContable.'").value        = "'.$observacion.'";
                  calcTotalExtrac("sumar",0,'.$saldo_extracto.');

                  if('.$estado.' == 3){
                    Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();
                    Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();
                    Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();
                  }';


  $bodyArticle = cargaArticulosSave($saldo_extracto,$tablaPrincipal,$id_documento,$observacion,$estado,$opcGrillaContable,$idTablaPrincipal,$id_empresa,$link);
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
            <input readonly class="input_extractos"  type="text" value="<?php echo $sucursal; ?>" id="sucursal<?php echo $opcGrillaContable; ?>" />
          </div>
        </div>
        <div class="renglonTop2" >
          <div class="labelTop">Fecha Extracto</div>
          <div id="renderfecha_extracto<?php echo $opcGrillaContable; ?>" style="float:left; width:20px; overflow:hidden;"></div>
          <div class="campoTop" >
            <input readonly class="input_extractos"  type="text" id="fecha<?php echo $opcGrillaContable; ?>" />
            <div id="renderSelectFormaPago<?php echo $opcGrillaContable; ?>" style="float:left;display:none;"></div>
          </div>
        </div>
        <div class="renglonTop" style="width:290px;">
          <div class="labelTop" style="float:left; width:100%;">Cuenta Contable</div>
          <div id="renderCuenta<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop">
            <input readonly class="input_extractos" type="text" id="cuenta<?php echo $opcGrillaContable; ?>" style="width:22% !important; float:left;"  onkeyup="buscarCuenta<?php echo $opcGrillaContable; ?>(event,this)" >
            <div style="width:10% !important; float:left;background-color:#F3F3F3; height:100%; text-align:center;">-</div>
            <input readonly class="input_extractos" type="text" id="descripcion_cuenta<?php echo $opcGrillaContable; ?>" style="width:68% !important; float:left;" Readonly>
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Saldo Extracto</div>
          <div class="campoTop"><input readonly class="input_extractos" type="text" onchange="instant_saved(this,'saldo_extracto')" id="saldo_extracto<?php echo $opcGrillaContable; ?>" /></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Nit</div>
          <div class="campoTop"><input readonly class="input_extractos" type="text" onchange="buscarTercero<?php echo $opcGrillaContable; ?>(event,this)" id="nitTercero<?php echo $opcGrillaContable; ?>" onkeyup="buscarTercero<?php echo $opcGrillaContable; ?>(event,this)"/></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Empresa</div>
          <div class="campoTop" style="width:277px;"><input readonly class="input_extractos" type="text" id="nombreTercero<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Usuario</div>
          <div class="campoTop" style="width:271px;"><input readonly class="input_extractos" type="text" id="nombre_usuario<?php echo $opcGrillaContable; ?>" style="width:80%" Readonly value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
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

    //============================= BUSCAR NOTA ==============================//
    function buscar_<?php echo $opcGrillaContable; ?>(){
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

    //=========================== IMPRIMIR EN PDF ============================//
    function imprimir<?php echo $opcGrillaContable; ?> (cuentas){
      window.open("conciliacion_bancaria/extracto/bd/imprimirGrillaContable.php?id_documento=<?php echo $id_documento; ?>&opcGrilla=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaCuentasNota=<?php echo $tablaCuentasNota; ?>&cuentas="+cuentas);
    }

    //======================== CANCELAR UN DOCUMENTO =========================//
    function cancelar<?php echo $opcGrillaContable; ?>(){
      if (!confirm("Aviso!\nSi elimina la nota se descontabilizara y se actualizara <?php echo $mensajeEdit; ?> \nRealmente desea continuar?")) { return;}

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
    }

    //========================= EDITAR UN DOCUMENTO ==========================//
    function modificarDocumento<?php echo $opcGrillaContable ?>(){
      if(confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo <?php echo $mensajeEdit; ?>")){
        Ext.get('render_btns_<?php echo $opcGrillaContable ?>').load({
          url     : 'conciliacion_bancaria/extracto/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
            opc               : 'modificarDocumentoGenerado',
            id_documento      : '<?php echo $id_documento; ?>',
            opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            tablaPrincipal    : '<?php echo $tablaPrincipal;?>'
          }
        });
      }
    }

    //======================== RESTAURAR UN DOCUMENTO ========================//
    function restaurar<?php echo $opcGrillaContable ?>(){
      Ext.get('render_btns_<?php echo $opcGrillaContable ?>').load({
        url     : 'conciliacion_bancaria/extracto/bd/bd.php',
        scripts : true,
        nocache : true,
        params  :
        {
          opc               : 'restaurarDocumento',
          id_documento      : '<?php echo $id_documento; ?>',
          opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
          consecutivo       : '<?php echo $consecutivo; ?>'  
        }
      });
    }

    //======================== ARTICULOS RELACIONADOS ========================//
    function ventanaArticulosRelacionados(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_articulos_relacionados = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_articulos_relacionados',
            title       : 'Articulos Relacionados en la nota No. <?php echo $consecutivo; ?>',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '<?php echo $carpeta; ?>bd/buscarArticulosRelacionados.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrilla : '<?php echo $opcGrillaContable; ?>',
                    consecutivo       : '<?php echo $consecutivo; ?>'
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
                            handler     : function(){ Win_Ventana_articulos_relacionados.close(id) }
                        }
                    ]
                },
                '->',
                {
                    xtype : "tbtext",
                    text  : '<div id="motivoMovimientoArticulos" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
                    scale : "large",
                }
            ]
        }).show();
    }
</script>
