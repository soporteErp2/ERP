<?php
    include("../../../../../configuracion/conectar.php");
    include("../../../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../../../funciones_globales/funciones_php/randomico.php");
    include("../../../../funciones_globales/funciones_javascript/totalesCompraVenta.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fecha       = date('Y-m-d');
    $tablaCarga  = "compras_facturas";

    $arrayTypeRetenciones = '';
?>
<script>
  var arrayTypeRetenciones = new Array();                  // ARRAY QUE CONTIENE LAS RETENCIONES QUE NO SON DESCONTADAS DEL TOTAL

  var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
  ,   descuentoAcumulado<?php echo $opcGrillaContable; ?> = 0.00
  ,   descuento<?php echo $opcGrillaContable; ?>          = 0.00
  ,   acumuladodescuentoArticulo                          = 0.00
  ,   ivaAcumulado<?php echo $opcGrillaContable; ?>       = 0.00
  ,   total<?php echo $opcGrillaContable; ?>              = 0.00
  ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
  ,   id_cliente_<?php echo $opcGrillaContable;?>         = 0
  ,   contabilidad_manual                                 = ''
  ,   subtotal_manual                                     = 0
  ,   iva_manual                                          = 0
  ,   total_manual                                        = 0;

  var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''     // var time out autoguardado onkeydows campo observaciones
  ,   codigoCliente<?php echo $opcGrillaContable; ?>      =  0
  ,   nitCliente<?php echo $opcGrillaContable; ?>         =  0
  ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
  ,   nombre_grilla                                       = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>'//nombre de la grilla cunado se busca un articulo
  ,   nombreTabla                                         = ''; //nombre de la tabla para la grilla de buscar articulos para agregar a la nota

  arrayIva<?php echo $opcGrillaContable;?> = [];
  var objectRetenciones_<?php echo $opcGrillaContable; ?>=[];

  var fechaVencimientoFactura<?php echo $opcGrillaContable;?> = new Date();
  var cantidadesArticulos<?php echo $opcGrillaContable; ?>    = new Array();

  Ext.getCmp("Btn_exportar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
  Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();
</script>
<?php

    if (isset($id_nota)) {                      //CARGAR UNA NOTA EXISTENTE
        include("../bd/functions_body_article.php");

        $sql   = "SELECT id_proveedor,
                    cod_proveedor,
                    id_documento_compra,
                    numero_documento_compra,
                    documento_compra,
                    nit,
                    proveedor,
                    date_format(fecha_registro,'%Y-%m-%d') AS fecha,
                    observacion,
                    estado,
                    consecutivo
                FROM $tablaPrincipal
                WHERE id='$id_nota'
                    AND activo = 1";
        $query = mysql_query($sql,$link);

        $nit         = mysql_result($query,0,'nit');
        $tercero     = mysql_result($query,0,'proveedor');
        $id_tercero  = mysql_result($query,0,'id_proveedor');
        $cod_tercero = mysql_result($query,0,'cod_proveedor');
        $fecha       = mysql_result($query,0,'fecha');
        $estado      = mysql_result($query,0,'estado');
        $documento_cruce        = mysql_result($query,0,'documento_compra');
        $id_documento_cruce     = mysql_result($query,0,'id_documento_compra');
        $numero_documento_cruce = mysql_result($query,0,'numero_documento_compra');

        if($prefijo_documento_cruce != ''){ $numero_documento_cruce = $prefijo_documento_cruce.' '.$numero_documento_cruce; }

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 130,
                            allowBlank : false,
                            showToday  : false,
                            value      : "'.$fecha.'",
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value); } }
                        });

                        document.getElementById("codCliente'.$opcGrillaContable.'").value    = "'.$cod_tercero.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$tercero.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value         = "'.$fecha.'";

                        id_cliente_'.$opcGrillaContable.'   = "'.$id_tercero.'";
                        codigoCliente'.$opcGrillaContable.' = "'.$cod_tercero.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$tercero.'";';

        $bodyArticle = cargaArticulosSave($id_nota,$observacion,$estado,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$tablaCarga,'id_documento_compra',$link);
    }
    else{                               //NOTA NUEVA
        $sqlConsulOrden = "SELECT id,
                                nit,
                                id_proveedor,
                                cod_proveedor,
                                proveedor,
                                plantillas_id,
                                prefijo_factura,
                                numero_factura,
                                contabilidad_manual,
                                subtotal_manual,
                                iva_manual,
                                total_manual
                            FROM compras_facturas
                            WHERE id='$id_documento'
                                AND total_factura_sin_abono > 0
                                AND id_sucursal='$id_sucursal'
                                AND id_bodega='$filtro_bodega'
                                AND id_empresa='$id_empresa'";

        $queryConsulOrden   = mysql_query($sqlConsulOrden,$link);

        $nit                     = mysql_result($queryConsulOrden,0,'nit');
        $id_tercero              = mysql_result($queryConsulOrden,0,'id_proveedor');
        $cod_tercero             = mysql_result($queryConsulOrden,0,'cod_proveedor');
        $nombre_tercero          = mysql_result($queryConsulOrden,0,'proveedor');
        $idPlantilla             = mysql_result($queryConsulOrden,0,'plantillas_id');
        $documento_cruce         = "FC";
        $id_documento_cruce      = mysql_result($queryConsulOrden,0,'id');
        $prefijo_documento_cruce = mysql_result($queryConsulOrden,0,'prefijo_factura');
        $numero_documento_cruce  = mysql_result($queryConsulOrden,0,'numero_factura');
        $contabilidad_manual     = mysql_result($queryConsulOrden,0,'contabilidad_manual');
        $subtotal_manual         = mysql_result($queryConsulOrden,0,'subtotal_manual');
        $iva_manual              = mysql_result($queryConsulOrden,0,'iva_manual');
        $total_manual            = mysql_result($queryConsulOrden,0,'total_manual');

        if ($contabilidad_manual=='true') {
            $acumScript.='contabilidad_manual = "'.$contabilidad_manual.'";
                         subtotal_manual     = "'.$subtotal_manual.'";
                         iva_manual          = "'.$iva_manual.'";
                         total_manual        = "'.$total_manual.'";';
        }

        if($prefijo_documento_cruce != ''){ $numero_documento_cruce = $prefijo_documento_cruce.' '.$numero_documento_cruce; }

        $random_factura = responseUnicoRanomico();          // CREACION DEL ID UNICO, Y LE INSERTAMOS lOS VALORES QUE SE VAN A CARGAR
        $sqlInsertFactura = "INSERT INTO $tablaPrincipal(
                                id_empresa,
                                random,
                                id_documento_compra,
                                documento_compra,
                                numero_documento_compra,
                                nit,
                                id_proveedor,
                                cod_proveedor,
                                proveedor,
                                id_sucursal,
                                id_bodega,
                                plantillas_id)
                            VALUES('$id_empresa',
                                '$random_factura',
                                '$id_documento_cruce',
                                '$documento_cruce',
                                '$numero_documento_cruce',
                                '$nit',
                                '$id_tercero',
                                '$cod_tercero',
                                '$nombre_tercero',
                                '$id_sucursal',
                                '$filtro_bodega',
                                '$idPlantilla')";

        $queryInsertFactura = mysql_query($sqlInsertFactura,$link);

        //VALIDAR SI SE CREO EL DOCUMENTO
        if (!$queryInsertFactura) { echo '<script>alert("Aviso No. 1!\nSin conexion con la base de datos.");</script>'; }

        $sqlSelectIdFactura = "SELECT id FROM $tablaPrincipal WHERE random='$random_factura' LIMIT 0,1";
        $id_nota = mysql_result(mysql_query($sqlSelectIdFactura,$link),0,'id');


        //======================== INSERTAR LOS ARTICULOS DEL DOCUMENTO A LA NOTA ==============================//
        $sqlArticulo = "SELECT
                            id,
                            codigo,
                            nombre,
                            id_inventario,
                            nombre_unidad_medida,
                            id_unidad_medida,
                            cantidad_unidad_medida,
                            tipo_descuento,
                            descuento,
                            costo_unitario,
                            id_impuesto,
                            impuesto,
                            valor_impuesto,
                            inventariable,
                            check_opcion_contable,
                            saldo_cantidad AS cantidad,
                            id_centro_costos
                        FROM compras_facturas_inventario
                        WHERE activo = 1  AND id_factura_compra = '$id_documento_cruce'";

        $queryArticulo = mysql_query($sqlArticulo,$link);

       while ($rowInsertArticulos=mysql_fetch_array($queryArticulo)) {

            if ($rowInsertArticulos['cantidad']>0) {

                $cadenaInsert.="('$id_nota',
                                '".$rowInsertArticulos['id']."',
                                '".$rowInsertArticulos['id_inventario']."',
                                '".$rowInsertArticulos['codigo']."',
                                '".$rowInsertArticulos['id_unidad_medida']."',
                                '".$rowInsertArticulos['nombre_unidad_medida']."',
                                '".$rowInsertArticulos['cantidad_unidad_medida']."',
                                '".$rowInsertArticulos['nombre']."',
                                '".$rowInsertArticulos['cantidad']."',
                                '".$rowInsertArticulos['costo_unitario']."',
                                '".$rowInsertArticulos['tipo_descuento']."',
                                '".$rowInsertArticulos['descuento']."',
                                '".$rowInsertArticulos['id_impuesto']."',
                                '".$rowInsertArticulos['impuesto']."',
                                '".$rowInsertArticulos['valor_impuesto']."',
                                '".$rowInsertArticulos['check_opcion_contable']."',
                                '".$rowInsertArticulos['inventariable']."',
                                '".$rowInsertArticulos['id_centro_costos']."'),";
            }
        }

        $cadenaInsert = substr($cadenaInsert,0,-1);
        $sqlInsert    = "INSERT INTO $tablaInventario (
                            id_devolucion_compra
                            ,id_fila_cargada
                            ,id_inventario
                            ,codigo
                            ,id_unidad_medida
                            ,nombre_unidad_medida
                            ,cantidad_unidad_medida
                            ,nombre
                            ,cantidad
                            ,costo_unitario
                            ,tipo_descuento
                            ,descuento
                            ,id_impuesto
                            ,impuesto
                            ,valor_impuesto
                            ,check_opcion_contable
                            ,inventariable
                            ,id_centro_costos)
                        VALUES $cadenaInsert";

        $queryInsert = mysql_query($sqlInsert,$link);
        if (!$queryInsert) { echo '<script>alert("Aviso!\nNo se cargaron los articulos en la presente nota.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; }

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

       //UNA INSERTADA TODA LA INFORMACION, LA VISUALIZAMOS
        $arrayReplaceString = array("\n","\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", $rowCotizacion['observaciones']);

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 130,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fecha'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() { updateFechaNota'.$opcGrillaContable.'(this.value); } }
                        });

                        document.getElementById("codCliente'.$opcGrillaContable.'").value    = "'.$cod_tercero.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value    = "'.$nit.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value = "'.$nombre_tercero.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value         = "'.$fecha.'";

                        observacion'.$opcGrillaContable.'   ="'.$observacion_cotizacion.'";
                        id_cliente_'.$opcGrillaContable.'   = "'.$id_tercero.'";
                        codigoCliente'.$opcGrillaContable.' = "'.$cod_tercero.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$nombre_tercero.'";';

        include("../bd/functions_body_article.php");

        $bodyArticle = cargaArticulosSave($id_nota,$observacion,'0',$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$tablaCarga,'id_documento_compra',$link);
    }

    $sqlArticulo = "SELECT
                        costo_unitario,
                        saldo_cantidad AS cantidad
                    FROM compras_facturas_inventario
                    WHERE activo = 1  AND id_factura_compra = '$id_documento_cruce'";

    $queryArticulo = mysql_query($sqlArticulo,$link);

   while ($rowInsertArticulos=mysql_fetch_array($queryArticulo)) {
        if ($rowInsertArticulos['cantidad']>0) {
            $subtotalAcumulado    += ($rowInsertArticulos['cantidad']*$rowInsertArticulos['costo_unitario']);
            $subtotalIvaAcumulado += ($rowInsertArticulos['cantidad']*$rowInsertArticulos['costo_unitario'])*$rowInsertArticulos['valor_impuesto']/100;
        }
    }

    //CONSULTAMOS LAS RETENCIONES, SI LAS HAY, Y LAS MOSTRAMOS EN LA NOTA
    $sqlRetenciones   = "SELECT id,retencion,valor,tipo_retencion,base FROM compras_facturas_retenciones WHERE id_factura_compra='$id_documento_cruce' AND activo=1";
    $queryRetenciones = mysql_query($sqlRetenciones,$link);
    // echo $subtotalAcumulado;
    while ($row=mysql_fetch_array($queryRetenciones)) {
        $row['valor'] = $row['valor']*1;
        $arrayTypeRetenciones .= 'arrayTypeRetenciones['.$row['id'].'] = "'.$row['tipo_retencion'].'";';

        $checkboxRetenciones[$row['id']] = '<div class="campoCheck" title="'.$row['retencion'].'">
                                                <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" name="checkbox'.$opcGrillaContable.'" checked value="'.$row['valor'].'" onchange="this.checked=true" />
                                                <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                    <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                    <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                </label>
                                            </div>';
        // echo $row['tipo_retencion'].' - '.$row['base'].' <-> '.$subtotalAcumulado.'<br>';
        if ($row['tipo_retencion']=='ReteIva') {
            $base =($subtotalIvaAcumulado>=$row['base'])? 0 : $row['base'] ;
        }
        else{
            $base =($subtotalAcumulado>=$row['base'])? 0 : $row['base'] ;
        }

        $objectRetenciones[$row['id']]  = 'objectRetenciones_'.$opcGrillaContable.'['.$row['id'].'] = {'
                                                                                        .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                        .'base           : "'.$base.'",'
                                                                                        .'valor          : "'.$row['valor'].'",'
                                                                                        .'estado         : "0"'
                                                                                    .'}';

    }
    echo '<script>
                exento_iva_'.$opcGrillaContable.'="'.$exento_iva.'";
                '.$arrayTypeRetenciones.'
            </script>';

    //IMPRIMIR OBJECT RETENCIONES//
    $plainRetenciones = implode(';', $objectRetenciones).';';
    echo '<script>'.$plainRetenciones.' //console.log(objectRetenciones_FacturaVenta);</script>';
?>
<div class="contenedorOrdenCompra" style="width:100% !important">
  <div class="bodyTop">
    <div class="contInfoFact">
      <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
      <div class="contTopFila">
        <div class="renglonTop">
          <div class="labelTop">Documento Cruce</div>
          <div class="campoTop campoDocCruce">
            <div class="labelTypeDocCruce" title="Factura de Compra">FC</div>
            <div class="numeroTypeDocCruce"><?php echo $numero_documento_cruce; ?></div>
          </div>
        </div>
        <div class="renglonTop">
          <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
          <div class="labelTop">Fecha</div>
          <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
        </div>
        <div style="float:left;max-width:20px;overflow:hidden;margin-top:17px;<?php echo $style; ?>" id="cargarFecha"></div>
        <div class="renglonTop">
          <div class="labelTop">Codigo Proveedor</div>
          <div class="campoTop"><input type="text" id="codCliente<?php echo $opcGrillaContable; ?>" readonly ></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Nit</div>
          <div class="campoTop"><input type="text" id="nitCliente<?php echo $opcGrillaContable; ?>" readonly /></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Proveedor</div>
          <div class="campoTop" style="width:277px;"><input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
        </div>
        <div class="renglonTop" id="checksRetenciones<?php echo $opcGrillaContable; ?>">
          <div class="labelTop">Retenciones</div>
          <div class="contenedorCheckbox">
            <?php foreach ($checkboxRetenciones as $valor) { echo  $valor; } ?>
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

  //==================== FUNCION PARA CAMBIAR LA FECHA DE LA NOTA ==============================//
  function updateFechaNota<?php echo $opcGrillaContable; ?>(fecha){
      Ext.get('cargarFecha').load({
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
              opc               : 'actualizarFechaNota',
              opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
              id                : '<?php echo $id_nota; ?>',
              fecha             : fecha
          }
      });
  }

  //DESHABILITAR EL BOTON DE IMPRIMIR
  Ext.getCmp("Btn_exportar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").enable();

  //==================  GUARDAR LA OBSERVACION DE LA FACTURA ==================================//
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
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          params  :
          {
              opc            : 'guardarObservacion',
              id             : '<?php echo $id_nota; ?>',
              tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
              observacion    : observacion
          },
          success :function (result, request){
                      if(result.responseText != 'true'){
                          alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                          document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                      }
                      else{ observacion<?php echo $opcGrillaContable; ?>=observacion; }
                  },
          failure : function(){ alert('Error de conexion con el servidor'); document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value = observacion<?php echo $opcGrillaContable; ?>; }
      });
  }

  //============================== FILTRO TECLA BUSCAR ARTICULO ==========================================================//
  function buscarArticulo<?php echo $opcGrillaContable; ?>(event,input){
      var contIdInput = (input.id).split('_')[1]
      ,   numero = input.value
      ,   tecla  = (input) ? event.keyCode : event.which;

      if (tecla == 13) {
          input.blur();
          ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(input.value, input.id);
          return true;
      }
      else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

      patron = /[^\d]/;
      if(patron.test(numero)){ input.value = numero.replace(patron,''); }
      if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
          document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
          document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';
      }
      else if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
          document.getElementById("cantArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value      = "";
          document.getElementById("descuentoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
      }

      document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value               = 0;
      document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value                 = "";
      document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value            = "";
      document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
      document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value       = "";
      return true;
  }

  function ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(valor,input){
      var arrayIdInput = input.split('_')
      ,   opcCargar    = "facturaCompra";

      Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
              opc               : 'buscarArticulo',
              opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
              campo             : arrayIdInput[0],
              eanArticulo       : valor,
              cont              : arrayIdInput[1],
              idCliente         : id_cliente_<?php echo $opcGrillaContable;?>,
              id                : '<?php echo $id_documento_cruce; ?>',
              opcCargar         : opcCargar,
              idNota            : '<?php echo $id_nota; ?>'
          }
      });
  }

  //======================== FUNCION PARA BUSCAR LA COTIZACION-PEDIDO POR SU NUMERO =======================================//
  function buscarCotizacionPedido<?php echo $opcGrillaContable; ?>(event,Input){
      tecla   = (Input) ? event.keyCode : event.which;
      numero  = Input.value;

      if(tecla == 13){
          var validacion= validarArticulos<?php echo $opcGrillaContable; ?>();
          if (validacion==1) {
              if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
          }
          else if (validacion== 2 || validacion== 0) { ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
          return true;
      }
      return true;
  }

  function ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido,idDocumento){
      var tablaBuscar = ""
      ,   opcCargar   = "facturaCompra"
      ,   divRender   = Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>");

      Ext.get(divRender).load({
          url     : "notas_inventario/notas_devolucion/bd/bd.php",
          scripts : true,
          nocache : true,
          params  :
          {
              opc               : 'buscarCotizacionPedido',
              opcCargar         : opcCargar,
              tablaBuscar       : tablaBuscar,
              id                : idCotizacionPedido,
              opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
              filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value,
              idDocumento       :  idDocumento
          }
      });
  }

  //======================== VENTANA BUSCAR PEDIDO - COTIZACION ===========================================================//
  function ventanaBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(){
      var myalto  = Ext.getBody().getHeight()
      ,   myancho = Ext.getBody().getWidth()
      ,   id_tabla_carga               = "id_factura_compra"
      ,   tabla_inventario_carga       = "compras_facturas_inventario"
      ,   nombreGrillaCotizacionPedido = "grillaNotaFactura";

      Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?> = new Ext.Window({
          width       : myancho-100,
          height      : myalto-50,
          id          : 'Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>',
          title       : 'Seleccione la factura de compra',
          modal       : true,
          autoScroll  : false,
          closable    : false,
          autoDestroy : true,
          autoLoad    :
          {
              url     : 'notas_inventario/notas_devolucion/bd/grillaBuscarCotizacionPedido.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opc                          : 'buscar_cotizacionPedido',
                  opcGrillaContable            : '<?php echo $opcGrillaContable; ?>',
                  opcCargar                    : 'facturaCompra',
                  tablaCotizacionPedido        : 'compras_facturas',
                  id_tabla_carga               : id_tabla_carga,
                  tabla_inventario_carga       : tabla_inventario_carga,
                  nombreGrillaCotizacionPedido : nombreGrillaCotizacionPedido,
                  filtro_bodega                : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
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
                          handler     : function(){ Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>.close(id) }
                      }
                  ]
              }
          ]
      }).show();
  }

  //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
  function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont){
      var myalto      = Ext.getBody().getHeight()
      ,   myancho     = Ext.getBody().getWidth()
      ,   nombreTabla = 'compras_facturas_inventario'
      ,   sql         = 'AND id_factura_compra=<?php echo $id_documento_cruce; ?> AND id_sucursal=<?php echo $id_sucursal; ?> AND id_bodega=<?php echo $filtro_bodega; ?> AND saldo_cantidad>0';

      Win_Ventana_buscar_Articulo_factura = new Ext.Window({
          width       : myancho-100,
          height      : myalto-50,
          id          : 'Win_Ventana_buscar_Articulo_factura',
          title       : 'Seleccionar articulo ',
          modal       : true,
          autoScroll  : false,
          closable    : false,
          autoDestroy : true,
          autoLoad    :
          {
              url     : '../funciones_globales/grillas/BusquedaInventariosNotas.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  sql               : sql,
                  nombre_grilla     : nombre_grilla,
                  nombreTabla       : nombreTabla,
                  cargaFuncion      : 'responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,'+cont+');',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  idNota            : '<?php echo $id_nota; ?>',
                  tablaInventario   : '<?php echo $tablaInventario ?>',
                  idTablaPrincipal  : '<?php echo $idTablaPrincipal ?>'
              }
          },
          tbar:
          [
              {
                  xtype       : 'button',
                  text        : 'Regresar',
                  scale       : 'large',
                  iconCls     : 'regresar',
                  iconAlign   : 'left',
                  handler     : function(){ Win_Ventana_buscar_Articulo_factura.close(id) }
              },'-'
          ]
      }).show();
  }

  function responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,cont){
      document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();

      var costoTotal = 0
      ,   totalDescuento = 0
      ,   idArticulo     = document.getElementById('notas_id_item_'+id).innerHTML
      ,   valor_impuesto = document.getElementById('notas_valor_impuesto_'+id).innerHTML
      ,   codigo         = document.getElementById('div_'+nombre_grilla+'_codigo_'+id).innerHTML
      ,   costo          = document.getElementById('div_'+nombre_grilla+'_costo_unitario_'+id).innerHTML
      ,   unidadMedida   = document.getElementById('unidad_medida_grilla_'+id).innerHTML
      ,   nombreArticulo = document.getElementById('div_'+nombre_grilla+'_nombre_'+id).innerHTML
      ,   cantidad       = document.getElementById('div_'+nombre_grilla+'_saldo_cantidad_'+id).innerHTML
      ,   tipoDescuento  = document.getElementById('notas_tipo_descuento_'+id).innerHTML
      ,   descuento      = document.getElementById('notas_descuento_'+id).innerHTML;

      titulo = (tipoDescuento=='porcentaje')? 'En porcentaje': 'En pesos';

      Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
              idInsert          : id,
              opc               : 'buscarImpuestoArticulo',
              idNota            : '<?php echo $id_nota; ?>',
              idFactura         : '<?php echo $id_documento_cruce; ?>',
              opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
              cont              : cont,
              unidadMedida      : unidadMedida,
              idArticulo        : idArticulo,
              codigo            : codigo,
              cantidad          : cantidad,
              descuento         : descuento,
              costo             : costo,
              nombreArticulo    : nombreArticulo,
          }
      });

      //si ya hay un articulo mostrar la imagen deshacer y actualizar
      if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
          document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
          document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';
      }
      else{ document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = "none"; }

      document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value          = unidadMedida;
      document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value        = idArticulo;
      document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value       = codigo;
      document.getElementById("cantArticulo<?php echo $opcGrillaContable; ?>_"+cont).value      = cantidad;
      document.getElementById("descuentoArticulo<?php echo $opcGrillaContable; ?>_"+cont).value = descuento;
      document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value     = costo;
      document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = nombreArticulo;
      document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value       = valor_impuesto;

      document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("src","img/"+tipoDescuento+".png");
      document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("title",titulo);

      cantidadesArticulos<?php echo $opcGrillaContable; ?>[cont] = cantidad;
      Win_Ventana_buscar_Articulo_factura.close();
  }

  //============================= FILTRO CAMPO CANTIDAD ARTICULO ==========================================================//
  function cantidadArticulo<?php echo $opcGrillaContable; ?>(cantidad){ }

  //============================= FILTRO TECLA GUARDAR ARTICULO ==========================================================//
  function guardarAuto<?php echo $opcGrillaContable; ?>(event,input,cont){
      var idInsertArticulo  = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
      ,   tecla = input? event.keyCode : event.which
      ,   value = input.value;

      if(tecla == 13){
          input.blur();
          guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont);
      }
      else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
      else if (idInsertArticulo>0) {
          document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
          document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
      }

      patron = /[^\d.]/g;
      if(patron.test(value)){ input.value = input.value.replace(patron,''); }
      return true;
  }

  function guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont){
      var idInsertArticulo       = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
      var idInsertFilaCargada    = document.getElementById('idInsertFilaCargada<?php echo $opcGrillaContable; ?>_'+cont).value;
      var idInsertNewFilaCargada = document.getElementById('idInsertNewFilaCargada<?php echo $opcGrillaContable; ?>_'+cont).value;
      var codigo                 = document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
      var idInventario           = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
      var cantArticulo           = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
      var costoArticulo          = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
      var opc                    = 'guardarArticulo';
      var divRender              = 'renderArticulo<?php echo $opcGrillaContable; ?>_'+cont;
      var accion                 = 'agregar';
      var tipoDesc               = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0];
      var opcCargar              = 'facturaCompra';

      if (idInventario == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
      else if(cantArticulo <= 0 || cantArticulo == ''){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo cantidad es obligatorio'); setTimeout(function(){document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },80); return; }

      //VALIDACION SI ES UPDATE O INSERT
      if(idInsertArticulo > 0){
          opc       = 'actualizaArticulo';
          divRender = 'renderArticulo<?php echo $opcGrillaContable; ?>_'+cont;
          accion    = 'actualizar';

          //VALIDACION SI ES UPDATE NO HAYAN 2 ELEMENTOS IGUALES POR ACTUALIZAR
          divsArticulos<?php echo $opcGrillaContable; ?> = document.querySelectorAll(".bodyDivArticulos<?php echo $opcGrillaContable; ?>");
          for(i in divsArticulos<?php echo $opcGrillaContable; ?>){

              if(typeof(divsArticulos<?php echo $opcGrillaContable; ?>[i].id)!='undefined'){

                  contArticulo = ((divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[1])*1;
                  if(cont !=  contArticulo){
                      newIdArticulo = document.getElementById('idInsertNewFilaCargada<?php echo $opcGrillaContable; ?>_'+contArticulo).value;
                      if(idInsertNewFilaCargada == newIdArticulo){ alert("Aviso.\nEl presente articulo esta repetido en la presente grilla."); return; }
                  }
              }
          }
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
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
              opc                    : opc,
              opcGrillaContable      : '<?php echo $opcGrillaContable; ?>',
              consecutivo            : contArticulos<?php echo $opcGrillaContable; ?>,
              cont                   : cont,
              idInsertArticulo       : idInsertArticulo,
              idInsertFilaCargada    : idInsertFilaCargada,
              idInsertNewFilaCargada : idInsertNewFilaCargada,
              codigo                 : codigo,
              idInventario           : idInventario,
              cantArticulo           : cantArticulo,
              costoArticulo          : costoArticulo,
              id                     : '<?php echo $id_documento_cruce; ?>',
              idBodega               : '<?php echo $filtro_bodega ?>',
              idNota                 : '<?php echo $id_nota ?>',
              accion                 : accion,
              opcCargar              : opcCargar
          }
      });
  }

  function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){
      var idArticulo        = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
      ,   cantArticulo      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
      ,   descuentoArticulo = document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
      ,   costoArticulo     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
      ,   iva               = document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

      tipoDesc = (document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png')? 'porcentaje': 'pesos';

      Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
              opc               : 'deleteArticulo',
              opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
              idArticulo        : idArticulo,
              cont              : cont,
              id                : '<?php echo $id_nota; ?>'
          }
      });
      calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo,descuentoArticulo,costoArticulo,'eliminar',tipoDesc,iva,cont);
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
              url     : 'notas_inventario/notas_devolucion/bd/bd.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opc               : 'ventanaDescripcionArticulo',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  idArticulo        : id,
                  cont              : cont,
                  id                : '<?php echo $id_nota; ?>'
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
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
              opc               : 'guardarDescripcionArticulo',
              idArticulo        : idArticulo,
              opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
              id                : '<?php echo $id_nota; ?>',
              observacion       : observacion
          }
      });
  }

  //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===============================================//
  function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
       id_actual = document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;

       Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
          url     : 'notas_inventario/notas_devolucion/bd/bd.php',
          scripts : true,
          nocache : true,
          params  :
          {
              opc                  : 'retrocederArticulo',
              cont                 : cont,
              opcGrillaContable    : '<?php echo $opcGrillaContable; ?>',
              tablaCargaInventario : '<?php echo $tablaCarga; ?>',
              idArticulo           : id_actual,
              id                   : '<?php echo $id_nota; ?>'
          }
      });
  }

  //===================================== FINALIZAR 'CERRAR' 'GENERAR' ===================================//
  function guardar<?php echo $opcGrillaContable; ?>(){
    var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();

    if(validacion == 0){
      alert("No hay nada que guardar!");
      return;
    }
    else if(validacion == 1){
      alert("Hay articulos pendientes por guardar!");
      return;
    }
    else if(validacion ==  2 || validacion == 0){
      var idBodega    = document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
      ,   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value
      ,   opcCargar   = "FacturaCompra";

      cargando_documentos('Generando Documento');

      Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
        url     : 'notas_inventario/notas_devolucion/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
          opc                    : 'terminarGenerar',
          opcGrillaContable      : '<?php echo $opcGrillaContable; ?>',
          id                     : '<?php echo $id_nota; ?>',
          idDocumentoCarga       : '<?php echo $id_documento_cruce; ?>',
          idBodega               : idBodega,
          observacion            : observacion,
          opcCargar              : opcCargar,
          fecha                  : document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value,
          numero_documento_cruce : '<?php echo $numero_documento_cruce; ?>'
        }
      });
    }
  }

  //==================================== BUSCAR   ==============================================//
  function buscar<?php echo $opcGrillaContable; ?>(){

      var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();
      if (validacion==1) {
          if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
      }
      else if (validacion== 2 || validacion== 0) { ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
  }

  function ventanaBuscar<?php echo $opcGrillaContable; ?>(){
      var myalto  = Ext.getBody().getHeight()
      ,   myancho = Ext.getBody().getWidth();

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
              url     : 'notas_inventario/notas_devolucion/bd/buscarGrillaContable.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
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
          if(tecla == 13 || tecla == 9){ verificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>'); return; }
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
          document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display    = 'inline';

          if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
              document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
          }
      }
  }

  //=================================== VALIDACION DE LA CANTIDAD DE ARTICULOS EXISTENTES ================================//
  function verificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,cantidad,opc){

      if ((cantidad*1)>(cantidadesArticulos<?php echo $opcGrillaContable; ?>[cont]*1)) {
          document.getElementById("cantArticulo"+opc+"_"+cont).blur();

          alert("Error!\nLa cantidad ingresada es mayor a la registrada en el documento");

          document.getElementById("cantArticulo"+opc+"_"+cont).value=cantidadesArticulos<?php echo $opcGrillaContable; ?>[cont];
          setTimeout(function(){document.getElementById("cantArticulo"+opc+"_"+cont).focus();},100);
      }
      else{ document.getElementById("descuentoArticulo"+opc+"_"+cont).focus(); }
  }

  //================================== IMPRIMIR EN PDF ==================================================================//
  function imprimir<?php echo $opcGrillaContable; ?> (){
      window.open("notas_inventario/notas_devolucion/bd/imprimirGrillaContable.php?id=<?php echo $id_nota; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
  }

  //============================  VALIDAR QUE NO HAYA NINGUN ARTICULO POR GUARDAR O POR ACTULIZAR =======================//
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

              if(     document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).value > 0
                  &&  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                  ||  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
                  &&  document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contArticulo).style.display == 'inline')
                  { cont++; }
          }
      }

      if(contTotal==0 || contTotal==1){  return 0; }      //SIN ARTICULOS ALMACENADOS
      else if(cont > 0){ return 1; }      //PENDIENTES POR GUARDAR
      else {return 2;}        //OK

  }

  //============================ CANCELAR UN DOCUMENTO =========================================================================//

  function cancelar<?php echo $opcGrillaContable; ?>(){
      var contArticulos = 0;

      if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }
      arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');

      for(i in arrayIdsArticulos){ if(arrayIdsArticulos[i].innerHTML != '' ){ contArticulos++; } }
      if(contArticulos == 0){ return; }

      if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
          Ext.get("terminar<?php echo $opcGrillaContable; ?>").load({
              url  : 'notas_inventario/notas_devolucion/bd/bd.php',
              scripts : true,
              nocache : true,
              params  :
              {
                  opc               : 'cancelarDocumento',
                  id                : '<?php echo $id_nota; ?>',
                  opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                  idBodega          : '<?php echo $filtro_bodega; ?>',
                  idDocumentoCarga  : '<?php echo $id_documento_cruce; ?>'
              }
          });
      };
  }

  //============================ LIMPIAR LA GRILLA SI SE MUEVE LA OPCION DE CARGAR DOCUMENTO ==============================//
  function limpiarGrillaContable(opc,filtro_bodega){
      var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();
      if (validacion==1) {
          if(!confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ return; }
      }

      if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {

          document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("src","img/factura.png");
          document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("width","20px");
          document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("height","20px");

          document.getElementById("textoFacturardesde<?php echo $opcGrillaContable; ?>").innerHTML="<b>Factura</b>";
          document.getElementById("divContenedorCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("title","Click para cargar una Remision");
          document.getElementById("tipo_id_carga").innerHTML="Numero";
          document.getElementById("tipo_id_carga").setAttribute("title","Numero de factura");
          document.getElementById("cotizacionPedido<?php echo $opcGrillaContable; ?>").focus();

      }
      else{

          document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("src","img/remisiones.png");
          document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("width","20px");
          document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("height","20px");
          document.getElementById("tipo_id_carga").innerHTML="Numero";
          document.getElementById("tipo_id_carga").setAttribute("title","Consecutivo de remision");
          document.getElementById("textoFacturardesde<?php echo $opcGrillaContable; ?>").innerHTML="<b>Remision</b>";
          document.getElementById("divContenedorCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("title","Click para cargar una Factura");

          document.getElementById("cotizacionPedido<?php echo $opcGrillaContable; ?>").focus();
      }

      Ext.get("contenedor_"+opc).load({
          url     : "notas_inventario/notas_devolucion/default.php",
          scripts : true,
          nocache : true,
          params  :
          {
              filtro_bodega     : filtro_bodega,
              opcGrillaContable : opc
          }
      });
  }
</script>
