<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");
  include("../config_var_global.php");
  include("../../../funciones_globales/funciones_php/randomico.php");

  $id_empresa  = $_SESSION['EMPRESA'];
  $id_sucursal = $filtro_sucursal;
  $bodyArticle = '';
  $acumScript  = '';
?>
<script>
  //Variables para calcular los valores de los costos y totales de la factura
  var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
  ,   total<?php echo $opcGrillaContable; ?>              = 0.00
  ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
  ,   codigoCliente<?php echo $opcGrillaContable; ?>      = 0
  ,   nitCliente<?php echo $opcGrillaContable; ?>         = 0
  ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
  ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';//nombre de la grilla cunado se busca un articulo

  //Bloqueo todos los botones
  Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
  Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();
</script>
<?php
  $acumScript .= (user_permisos(232,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //Guardar
  $acumScript .= (user_permisos(233,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();';       //Editar
  $acumScript .= (user_permisos(234,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();';   //Cancelar
  $acumScript .= (user_permisos(235,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();'; //Restaurar

  include("functions_body_article.php");

  $sql = "SELECT
            consecutivo,
            fecha_inicio,
            codigo_tercero,
            numero_identificacion_tercero,
            tipo_identificacion_tercero,
            tercero,
            usuario,
            observacion,
            estado
          FROM $tablaPrincipal
          WHERE id = '$id_baja'
          AND activo = 1
          AND id_empresa = $id_empresa";
  $query = mysql_query($sql,$link);

  $consecutivo                   = mysql_result($query,0,'consecutivo');
  $fecha_inicio                  = mysql_result($query,0,'fecha_inicio');
  $usuario                       = mysql_result($query,0,'usuario');
  $observacion                   = mysql_result($query,0,'observacion');
  $estado                        = mysql_result($query,0,'estado');
  $codigo_tercero                = mysql_result($query,0,'codigo_tercero');
  $numero_identificacion_tercero = mysql_result($query,0,'numero_identificacion_tercero');
  $tipo_identificacion_tercero   = mysql_result($query,0,'tipo_identificacion_tercero');
  $tercero                       = mysql_result($query,0,'tercero');

  $arrayReplaceString = array("\n", "\r","<br>");
  $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

  $bodyArticle = cargaArticulosSave($id_baja,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

  if($estado == "1"){
    $acumScript .= 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").disable();
                    Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();
                    Ext.getCmp("btnExportar'.$opcGrillaContable.'").enable();
                    Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();
                    document.getElementById("titleDocumento'.$opcGrillaContable.'").style.color = "#333";';
  }
  else if($estado == "3"){
    $acumScript .= 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").disable();
                    Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();
                    Ext.getCmp("btnExportar'.$opcGrillaContable.'").enable();
                    Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();
                    document.getElementById("titleDocumento'.$opcGrillaContable.'").style.color = "red";';
  }

  $acumScript .= 'document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML = "Baja<br>No. '.$consecutivo.'";';
?>
<style type="text/css">
  .contenedorGrilla{
    margin-top  : 0;
    height      : calc(100% - 70%);
  }
</style>
<div class="contenedorOrdenCompra">
  <div class="bodyTop">
    <div class="contInfoFact">
      <div id="render_btns_<?php echo $opcGrillaContable; ?>" style="width: 20px;height: 20px;position: fixed;margin: 5px 10px;overflow: hidden;"></div>
      <div class="contTopFila">
        <div class="renglonTop">
          <div class="labelTop">Fecha</div><div id="divLoadFecha" style="width:20px;height:20px;margin-top: -20;margin-left: -22;overflow:hidden;float: right;"></div>
          <div class="campoTop" >
            <input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha_inicio; ?>" Readonly />
          </div>
        </div>
        <div class="renglonTop" id="divCodigoTercero">
          <div class="labelTop">Codigo Tercero</div>
          <div class="campoTop">
            <input type="text" id="codigoTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $codigo_tercero; ?>" Readonly >
          </div>
        </div>
        <div class="renglonTop" id="divIdentificacionTercero">
          <div class="labelTop">N. de Identificacion</div>
          <div class="campoTop" style="width:230px">
            <input type="text" style="width:161px"  id="nitCliente<?php echo $opcGrillaContable; ?>" value="<?php echo $tipo_identificacion_tercero.' - '.$numero_identificacion_tercero ?>" Readonly/>
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Tercero</div>
          <div class="campoTop" style="width:277px;">
            <input type="text" id="nombreTercero<?php echo $opcGrillaContable; ?>" value="<?php echo $tercero; ?>" Readonly/>
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
    <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>">
      <?php echo $bodyArticle; ?>
    </div>
  </div>
</div>
<script>
  var observacion<?php echo $opcGrillaContable; ?> = '';
  <?php echo $acumScript; ?>

  //============================== BOTON BUSCAR ==============================//
  function buscar<?php echo $opcGrillaContable; ?>(){
    ventanaBuscar<?php echo $opcGrillaContable; ?>();
  }

  //====================== VENTANA PARA BUSCAR TRASLADOS =====================//
  function ventanaBuscar<?php echo $opcGrillaContable; ?>(){
    var myalto  = Ext.getBody().getHeight();
    var myancho = Ext.getBody().getWidth();

    Win_Ventana_buscar_<?php echo $opcGrillaContable; ?> = new Ext.Window({
      width       : myancho - 100,
      height      : myalto - 50,
      id          : 'Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>',
      title       : 'Seleccionar ',
      modal       : true,
      autoScroll  : false,
      closable    : false,
      autoDestroy : true,
      autoLoad    : {
                      url     : 'baja/bd/buscarGrillaContable.php',
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

  //============================== IMPRIMIR PDF ==============================//
  function imprimir<?php echo $opcGrillaContable; ?> (){
    window.open("baja/bd/imprimirGrillaContable.php?id=<?php echo $id_baja; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>");
  }

  //=========================== MODIFICAR DOCUMENTO ==========================//
  function modificarDocumento<?php echo $opcGrillaContable; ?>(){
    if(confirm('\u00BFEsta seguro de editar el presente documento y su contenido relacionado?')){
      cargando_documentos('Modificando Documento...');
      Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
        url     : 'baja/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc               : 'modificarDocumentoGenerado',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_baja; ?>',
                    tablaPrincipal    : '<?php echo $tablaPrincipal; ?>',
                    tablaInventario   : '<?php echo $tablaInventario; ?>'
                  }
      });
    }
  }

  //=========================== CANCELAR DOCUMENTO ===========================//
  function cancelar<?php echo $opcGrillaContable; ?>(){
    if(confirm('\u00BFEsta seguro de eliminar el presente documento y su contenido relacionado?')){
      cargando_documentos('Cancelando Documento...');
      Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
        url     : 'baja/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc               : 'cancelarDocumento',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_baja; ?>',
                    tablaPrincipal    : '<?php echo $tablaPrincipal; ?>',
                    tablaInventario   : '<?php echo $tablaInventario; ?>'
                  }
      });
    }
  }

  //=========================== RESTAURAR DOCUMENTO ==========================//
  function restaurar<?php echo $opcGrillaContable; ?>(){
    cargando_documentos('Restaurando Documento...');
    Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
      url     : 'baja/bd/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc               : 'restaurarDocumento',
                  id                : '<?php echo $id_baja; ?>',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  id_sucursal       : '<?php echo $id_sucursal; ?>',
                }
    });
  }
</script>
