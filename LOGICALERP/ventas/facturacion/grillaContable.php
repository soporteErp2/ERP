<?php
  include("../../../configuracion/conectar.php");
  include("../../../configuracion/define_variables.php");
  include("../config_var_global.php");
  include("../../funciones_globales/funciones_php/randomico.php");
  include("../../funciones_globales/funciones_javascript/totalesCompraVenta.php");

  //ARRAY GLOBAL PARA ACTUALIZAR LA CANTIDAD DE ARTICULOS VENDIDOS, EL INDICE VA A SER EL ID DEL ARTICULO Y LA CANTIDAD SU CONTENIDO
  $arrayCantidades;

  $id_empresa  = $_SESSION['EMPRESA'];
  $id_sucursal = $_SESSION['SUCURSAL'];
  $id_usuario  = $_SESSION['IDUSUARIO'];
  $bodyArticle = '';
  $acumScript  = '';
  $estado      = '';
  $fecha       = date('Y-m-d');
  $exento_iva  = '';
  $labelCcos   = '';

  $sucursalesCliente    = '<option value="0">Seleccione...</option>';
  $arrayTypeRetenciones = '';

  //CONSULTAMOS LA FECHA DE VENCIMIENTO QUE ESTE CONFIGURADA, SINO ESTA CONFIGURADA SE ASIGNA DE 30 DIAS
  $sqlFecha     = "SELECT dias_vencimiento FROM ventas_facturas_configuracion WHERE activo=1 AND estado=1 AND id_empresa='$id_empresa'";
  $queryFecha   = mysql_query($sqlFecha,$link);
  $fechaDefault = mysql_result($queryFecha,0,'dias_vencimiento');

  if ($fechaDefault=='') { $fechaDefault='31'; }

  function validateConsecutivo($opcGrillaContable,$id_empresa,$link){
      $sqlValidateConsecutivo =  "SELECT
                                    VFC.numero_inicial_resolucion AS inicial_r,
                                    VFC.numero_final_resolucion AS final_r,
                                    CCD.consecutivo
                                  FROM
                                    ventas_facturas_configuracion AS VFC,
                                    configuracion_consecutivos_documentos AS CCD
                                  WHERE
                                    VFC.id_empresa = '$id_empresa'
                                  AND
                                    VFC.activo = 1
                                  AND
                                    CCD.id_empresa = VFC.id_empresa
                                  AND
                                    CCD.documento = 'factura_venta'
                                  ORDER BY
                                    VFC.id DESC
                                  LIMIT
                                    0,1";

      $queryValidateConsecutivo = mysql_query($sqlValidateConsecutivo,$link);

      $limiteInferior      = mysql_result($queryValidateConsecutivo,0,'inicial_r');
      $limiteSuperior      = mysql_result($queryValidateConsecutivo,0,'final_r');
      $consecutivoFacturas = mysql_result($queryValidateConsecutivo,0,'consecutivo');

      if($limiteInferior > $consecutivoFacturas){
          echo'<script>
                  alert("Error!\nEl consecutivo de la presente factura es inferior al configurado en la resolucion Dian");
                  Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
              </script>';
          exit;
      }
      else if($limiteSuperior < $consecutivoFacturas){
          echo'<script>
                  alert("Error!\nEl consecutivo de la presente factura es superior al configurado en la resolucion Dian");
                  Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
              </script>';
          exit;
      }
  }
?>
<script>
  var arrayRemisionesSaldos                                   = new Array();  // ARRAY QUE CONTIENE CANTIDADES MAXIMAS CUANDO SE CARGA UNA REMISION
  var arrayTypeRetenciones<?php echo $opcGrillaContable; ?>   = new Array();  // ARRAY QUE CONTIENE LAS RETENCIONES QUE NO SON DESCONTADAS DEL TOTAL
  var arrayRetenciones<?php echo $opcGrillaContable; ?>       = new Array();

  arrayIva<?php echo $opcGrillaContable; ?>     = [];                         // ARRAY CON LOS VALORES DE LOS IVAS
  arrayIva<?php echo $opcGrillaContable; ?>[0]  = {nombre:"",valor:""};

  var objectRetenciones_<?php echo $opcGrillaContable; ?>=[];
  var objDocumentosCruce<?php echo $opcGrillaContable; ?>=[];

  var subtotalAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
  ,   descuentoAcumulado<?php echo $opcGrillaContable; ?> = 0.00
  ,   descuento<?php echo $opcGrillaContable; ?>          = 0.00
  ,   acumuladodescuentoArticulo                          = 0.00
  ,   ivaAcumulado<?php echo $opcGrillaContable; ?>       = 0.00
  ,   total<?php echo $opcGrillaContable; ?>              = 0.00
  ,   contArticulos<?php echo $opcGrillaContable; ?>      = 1
  ,   id_cliente_<?php echo $opcGrillaContable;?>         = 0;


  var timeOutObservacion<?php echo $opcGrillaContable; ?> = ''                       // var time out autoguardado onkeyup campo observaciones
  ,   timeOutOC<?php echo $opcGrillaContable; ?>          = ''
  ,   codigoCliente<?php echo $opcGrillaContable; ?>      = ''
  ,   nitCliente<?php echo $opcGrillaContable; ?>         = 0
  ,   nombreCliente<?php echo $opcGrillaContable; ?>      = ''
  ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';       //nombre de la grilla cunado se busca un articulo

  //Bloqueo todos los botones
  Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
  Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
  Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();
  Ext.getCmp('Btn_itemsGrupos_<?php echo $opcGrillaContable; ?>').enable();

  //variable con la fecha del dia mas treinta dias, para cargar por defecto la fecha de vencimiento
  var fechaVencimientoFactura = new Date();
  fechaVencimientoFactura.setDate(fechaVencimientoFactura.getDate()+parseInt('<?php echo $fechaDefault; ?>'));
</script>
<?php

    $acumScript .= (user_permisos(21,'false') == 'true')? 'Ext.getCmp("Btn_guardar_'.$opcGrillaContable.'").enable();' : '';        //guardar
    $acumScript .= (user_permisos(23,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';       //cancelar
    $permiso_editar_factura_eviada = (user_permisos(253,'false') == 'true')? "true" : "false";

    //===========================// FORMAS DE PAGO //=========================//
    //************************************************************************//

    $formasPago      = '';
    $idFormaPago     = '';
    $arrayFormasPago = '<script>var arrayFormaPago'.$opcGrillaContable.' = new Array();';

    $sqlFormasPago   = "SELECT id,nombre,plazo FROM configuracion_formas_pago WHERE activo=1 AND id_empresa='$id_empresa'";
    $queryFormasPago = mysql_query($sqlFormasPago,$link);


    while ($rowFormasPago=mysql_fetch_array($queryFormasPago)) {
        if ($idFormaPago=='') {
            $idFormaPago     = $rowFormasPago['id'];
            $diasFormaPago   = $rowFormasPago['plazo'];
            $nombreFormaPago = $rowFormasPago['nombre'];
        }
        $formasPago      .= '<option value="'.$rowFormasPago['id'].'" >'.$rowFormasPago['nombre'].'</option>';
        $arrayFormasPago .= 'arrayFormaPago'.$opcGrillaContable.'['.$rowFormasPago['id'].'] = "'.$rowFormasPago['plazo'].'";';
    }
    $arrayFormasPago .= '</script>';


    if ($formasPago=='') {
        echo'<script>
                alert("Error!\nNo hay ninguna forma de pago configurada\nDirijase al panel de control->formas de pago\nCree una y vuelva a intentarlo");
                Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
            </script>';
        exit;
    }

    //========================// METODOS DE PAGO //===========================//
    //************************************************************************//

    $sqlMetodosPago   = "SELECT id,nombre FROM configuracion_metodos_pago WHERE activo = 1 AND id_empresa = $id_empresa";
    $queryMetodosPago = mysql_query($sqlMetodosPago,$link);

    while($rowMetodosPago = mysql_fetch_array($queryMetodosPago)){
      $idMetodoPago       = $rowMetodosPago['id'];
      $nombreMetodoPago   = $rowMetodosPago['nombre'];
      $metodosPago       .= '<option value="' . $rowMetodosPago['id'] . '" >' . $rowMetodosPago['nombre'] . '</option>';
    }

    //=========================// CUENTAS DE PAGO //==========================//
    //************************************************************************//

    $cuentasPago      = '<option value="0">Seleccione...</option>';
    $sqlCuentasPago   = "SELECT id,nombre,cuenta,cuenta_niif
                            FROM configuracion_cuentas_pago
                            WHERE activo=1 AND id_empresa='$id_empresa' AND tipo='Venta' AND (id_sucursal='$id_sucursal' OR id_sucursal=0)";
    $queryCuentasPago = mysql_query($sqlCuentasPago,$link);

    while ($rowCuentasPago=mysql_fetch_array($queryCuentasPago)) {
        if ($idConfigCuentaPago == ''){
            $idConfigCuentaPago      = $rowCuentasPago['id'];
            $configuracionCuentaPago = $rowCuentasPago['nombre'];
            $CuentaPago              = $rowCuentasPago['cuenta'];
            $CuentaPagoNiif          = $rowCuentasPago['cuenta_niif'];
        }
        $cuentasPago .= '<option value="'.$rowCuentasPago['id'].'" >'.$rowCuentasPago['nombre'].' '.$rowCuentasPago['cuenta'].'</option>';
    }

    if ($cuentasPago=='') {
        echo'<script>
                alert("Error!\nNo hay ninguna cuenta de pago configurada\nDirijase al panel de control->cuentas de pago\nCree una y vuelva a intentarlo");
                Ext.getCmp("Btn_nueva_factura_compra").enable();
            </script>';
        exit;
    }


    //========================// CHECKBOX RETENCIONES //======================//
    //************************************************************************//
    $sqlArrayRetenciones   = "SELECT id_retencion AS id,
                                    retencion,
                                    tipo_retencion
                                    valor,
                                    base,
                                    cuenta
                                FROM $tablaRetenciones
                                WHERE activo=1
                                    AND $idTablaPrincipal='$id_factura_venta'";
    $queryArrayRetenciones = mysql_query($sqlArrayRetenciones,$link);

    while ($row=mysql_fetch_array($queryArrayRetenciones)) {
        $row['valor'] = $row['valor']*1;
        $arrayTypeRetenciones .= 'arrayTypeRetenciones'.$opcGrillaContable.'['.$row['id'].'] = "'.$row['tipo_retencion'].'";';

        if (gettype($checkboxRetenciones[$row['id']]) =='NULL') {
            $checkboxRetenciones[$row['id']] = '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetenciones'.$opcGrillaContable.'_'.$row['id'].'">
                                                    <div id="cargarCheckbox'.$opcGrillaContable.'_'.$row['id'].'" class="renderCheck"></div>
                                                    <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" checked name="checkbox'.$opcGrillaContable.'" value="'.$row['valor'].'" />
                                                    <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                        <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                        <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                    </label>
                                                </div>';
        }
        else{
            $checkboxRetenciones[$row['id']] = '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetenciones'.$opcGrillaContable.'_'.$row['id'].'">
                                                    <div id="cargarCheckbox'.$opcGrillaContable.'_'.$row['id'].'" class="renderCheck"></div>
                                                    <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" checked name="checkbox'.$opcGrillaContable.'" value="'.$row['valor'].'"  />
                                                    <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                        <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                        <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                    </label>
                                                </div>';
        }

        $objectRetenciones[$row['id']]  = 'objectRetenciones_'.$opcGrillaContable.'['.$row['id'].'] = {'
                                                                                    .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                    .'base           : "'.$row['base'].'",'
                                                                                    .'valor          : "'.$row['valor'].'",'
                                                                                    .'cuenta         : "'.$row['cuenta_venta'].'",'
                                                                                    .'estado         : "0"'
                                                                                .'}';
    }
    echo '<script>'.$arrayTypeRetenciones.'; var objectRetenciones_'.$opcGrillaContable.' = [];</script>';


    //==========================// DOCUMENTOS CRUCE //========================//
    //************************************************************************//
    if(isset($idConsecutivoCotizacionPedido)){

        $whereCcos     = ",id_centro_costo,centro_costo,codigo_centro_costo";
        $campoCarga    = '';
        $campoCantidad = "cantidad";

        if ($opcCargar =="cotizacion") {
            $referencia_consecutivo = "Cotizacion";
            $tablaCarga             = "ventas_cotizaciones";
            $idTablaCargar          = "id_cotizacion_venta";
            $tablaCargaInventario   = "ventas_cotizaciones_inventario";
            $whereCcos              = '';
            $referencia_input       = "C";
        }
        else if ($opcCargar == "pedido") {
            $referencia_consecutivo = "Pedido";
            $tablaCarga             = "ventas_pedidos";
            $idTablaCargar          = "id_pedido_venta";
            $tablaCargaInventario   = "ventas_pedidos_inventario";
            $whereCcos              = '';
            $referencia_input       = "P";
        }
        elseif ($opcCargar == "remision") {
            $campoCantidad          = "saldo_cantidad";
            $referencia_consecutivo = "Remision";
            $tablaCarga             = "ventas_remisiones";
            $idTablaCargar          = "id_remision_venta";
            $tablaCargaInventario   = "ventas_remisiones_inventario";
            $campoCarga             = ',id_sucursal_cliente,sucursal_cliente';
            $referencia_input       = "R";
        }

        //TABLA CRUCE
        $sqlConsulOrden =  "SELECT
                              id,
                              nit,
                              id_cliente,
                              cod_cliente,
                              cliente,
                              observacion,
                              documento_vendedor,
                              nombre_vendedor
                              $campoCarga
                              $whereCcos
                            FROM
                              $tablaCarga
                            WHERE
                              consecutivo = '$idConsecutivoCotizacionPedido'
                            AND
                              id_sucursal = '$id_sucursal'
                            AND
                              id_bodega   = '$filtro_bodega'
                            AND
                              id_empresa  = '$id_empresa'";

        $queryConsulOrden = mysql_query($sqlConsulOrden,$link);

        $idDocCruce             = mysql_result($queryConsulOrden,0,'id');
        $nit                    = mysql_result($queryConsulOrden,0,'nit');
        $id_cliente             = mysql_result($queryConsulOrden,0,'id_cliente');
        $cod_cliente            = mysql_result($queryConsulOrden,0,'cod_cliente');
        $cliente                = mysql_result($queryConsulOrden,0,'cliente');
        $observacion_cotizacion = mysql_result($queryConsulOrden,0,'observacion');
        $documento_vendedor     = mysql_result($queryConsulOrden,0,'documento_vendedor');
        $nombre_vendedor        = mysql_result($queryConsulOrden,0,'nombre_vendedor');

        if($observacion_cotizacion != ''){
            $arrayReplaceString = array("\n", "\r","<br>");
            $observacion_cotizacion = $referencia_input.' '.$idConsecutivoCotizacionPedido.' : '.str_replace($arrayReplaceString, "\\n", $observacion_cotizacion);
        }

        $idCcos     = mysql_result($queryConsulOrden,0,'id_centro_costo');
        $nombreCcos = mysql_result($queryConsulOrden,0,'centro_costo');
        $codigoCcos = mysql_result($queryConsulOrden,0,'codigo_centro_costo');

        // CARGAR LAS RETENCIONES POR DEFECTO DEL TERCERO
        if ($opcCargar == "remision") {
            $idSucursalCliente =  mysql_result($queryConsulOrden,0,'id_sucursal_cliente');
            $sucursal_cliente  =  mysql_result($queryConsulOrden,0,'sucursal_cliente');
            $whereRemision = " AND COI.saldo_cantidad > 0";

		    $sql_ubicacion 		= "SELECT id_departamento, id_ciudad FROM terceros_direcciones WHERE id=$idSucursalCliente";
		    $query_departamento = mysql_query($sql_ubicacion,$link);
		    $id_departamento 	= mysql_result($query_departamento,0,'id_departamento');
		    $id_ciudad 			= mysql_result($query_departamento,0,'id_ciudad');

		    $sql_empresa 		= "SELECT documento FROM empresas WHERE id=$id_empresa";
		    $query_empresa 		= mysql_query($sql_empresa,$link);
		    $NIT 				= mysql_result($query_empresa,0,'documento');

		    $municipios_exentos = array(2293, 4722, 4720);
		    //Verificamos que sea plataforma colombia o comunicaciones
		    //Insertamos de manera automatica una observacion debido al decreto 1085 del 2 de Julio
		    if($NIT == 900013664 || $NIT == 830509557){
		    	if($id_departamento == 672 && !(in_array($id_ciudad,$municipios_exentos))){
                    $observacion_cotizacion = 'Servicios Exentos - Decreto 1085 del 2 de julio de 2023';
		    	}
		    }

        }

        $labelCcos      = $codigoCcos.' '.$nombreCcos;
        $random_factura = responseUnicoRanomico();                                          // CREACION DEL ID UNICO, Y LE INSERTAMOS lOS VALORES QUE SE VAN A CARGAR

        validateConsecutivo($opcGrillaContable,$id_empresa,$link);                     // VALIDACION DEL NUMERO CONSECUTIVO

        //INSERT EN LA TABLA FACTURAS
        $sqlInsertFactura  = "INSERT INTO
                                $tablaPrincipal
                                  (id_empresa,random,nit,id_cliente,
                                  cod_cliente,cliente,fecha_inicio,
                                  id_sucursal,id_bodega,observacion,
                                  documento_vendedor,nombre_vendedor,id_metodo_pago,
                                  metodo_pago,id_forma_pago,dias_pago,
                                  forma_pago,id_configuracion_cuenta_pago,
                                  configuracion_cuenta_pago,cuenta_pago,
                                  cuenta_pago_niif,id_centro_costo,centro_costo,
                                  codigo_centro_costo,id_sucursal_cliente,sucursal_cliente)
                              VALUES
                                ('$id_empresa','$random_factura','$nit',
                                '$id_cliente','$cod_cliente','$cliente',
                                '$fecha','$id_sucursal','$filtro_bodega',
                                '$observacion_cotizacion','$documento_vendedor',
                                '$nombre_vendedor','$idMetodoPago',
                                '$nombreMetodoPago','$idFormaPago',
                                '$diasFormaPago','$nombreFormaPago',
                                '$idConfigCuentaPago','$configuracionCuentaPago',
                                '$CuentaPago','$CuentaPagoNiif','$idCcos',
                                '$nombreCcos','$codigoCcos','$idSucursalCliente',
                                '$sucursal_cliente')";

        $queryInsertFactura = mysql_query($sqlInsertFactura,$link);

        if (!$queryInsertFactura) { '<script>alert("Error!\nSe produjo un error y no se cargo la cotizacion a la factura");</script>'; exit; }      //SI NO SE REALIZO EL INSERT

        $sqlSelectIdFactura   = "SELECT id,exento_iva FROM $tablaPrincipal WHERE random='$random_factura' LIMIT 0,1";
        $querySelectIdFactura = mysql_query($sqlSelectIdFactura,$link);

        $id_factura_venta = mysql_result($querySelectIdFactura,0,'id');
        $exento_iva       = mysql_result($querySelectIdFactura,0,'exento_iva');


        //COPIA DE LA TABLA CARGA INVENTARIO A LA TABLA FACTURACION
        $sqlConsultaInventario = "SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.saldo_cantidad AS cantidad,COI.costo_unitario,
                                    COI.tipo_descuento,COI.descuento,
                                    COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
                                    CO.id AS id_documento,CO.consecutivo AS consecutivo_documento
                                  FROM $tablaCargaInventario AS COI
                                  INNER JOIN  $tablaCarga AS CO ON COI.$idTablaCargar=CO.id
                                  WHERE CO.consecutivo   ='$idConsecutivoCotizacionPedido'
                                  AND COI.activo     = 1
                                  AND CO.id_sucursal = '$id_sucursal'
                                  AND CO.id_bodega   = '$filtro_bodega'
                                  AND CO.id_empresa  = '$id_empresa'
                                  $whereRemision";

        $queryConsultaInventario = mysql_query($sqlConsultaInventario,$link);

        $valorImpuestoBd = ($exento_iva == 'Si')? 0: 'NULL';
        while ($rowCotizacion=mysql_fetch_array($queryConsultaInventario)) {

            $cadenaInsert .="('$id_factura_venta',
                            '".$rowCotizacion['id_inventario']."',
                            '".$rowCotizacion['cantidad']."',
                            '".$rowCotizacion['costo_unitario']."',
                            '".$rowCotizacion['tipo_descuento']."',
                            '".$rowCotizacion['descuento']."',
                            ".$valorImpuestoBd.",
                            '".$rowCotizacion['observaciones']."',
                            '".$rowCotizacion['id']."',
                            '".$rowCotizacion['id_documento']."',
                            '".$rowCotizacion['consecutivo_documento']."',
                            '$referencia_consecutivo'),";
        }

        $cadenaInsert = substr($cadenaInsert,0,-1);

        $sqlInsertArticulos =  "INSERT INTO $tablaInventario (
                                  $idTablaPrincipal,
                                  id_inventario,
                                  cantidad,
                                  costo_unitario,
                                  tipo_descuento,
                                  descuento,
                                  valor_impuesto,
                                  observaciones,
                                  id_tabla_inventario_referencia,
                                  id_consecutivo_referencia,
                                  consecutivo_referencia,
                                  nombre_consecutivo_referencia)
                                VALUES $cadenaInsert";

        $queryInsertArticulos = mysql_query($sqlInsertArticulos,$link);

        if (!$queryInsertArticulos) { echo '<script>alert("Error!\nNo se cargaron los articulos al documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; }

        //RETENCIONES ALMACENADAS O CON CARGA AUTOMATICA
        $sqlRetenciones  = "SELECT
                              R.id,
                              R.tipo_retencion,
                              R.cuenta,
                              R.retencion,
                              R.valor,
                              R.base
                            FROM
                              retenciones AS R
                            RIGHT JOIN
                              terceros_retenciones AS TR ON (
                                (
                                  TR.id_retencion = R.id
                                  AND TR.id_proveedor = '$id_cliente'
                                  OR R.factura_auto = 'true'
                                )
                                AND TR.activo = 1
                                AND TR.id_empresa = '$id_empresa'
                              )
                            WHERE
                              R.id_empresa = '$id_empresa'
                            AND R.activo = 1
                            AND R.cuenta > 0
                            AND R.modulo = 'Venta'
                            GROUP BY
                                R.id";

        $queryRetenciones = mysql_query($sqlRetenciones,$link);

        while ($row=mysql_fetch_array($queryRetenciones)){
            $sqlInsertRetenciones   = "INSERT INTO ventas_facturas_retenciones (id_factura_venta,id_retencion) VALUES ('$id_factura_venta','".$row['id']."')";
            $queryInsertRetenciones = mysql_query($sqlInsertRetenciones,$link);

            $row['valor'] = $row['valor']*1;
            if($queryInsertRetenciones){
              if (gettype($checkboxRetenciones[$row['id']]) =='NULL') {
              $checkboxRetenciones[$row['id']] = '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetenciones'.$opcGrillaContable.'_'.$row['id'].'">
                                                    <div id="cargarCheckbox'.$opcGrillaContable.'_'.$row['id'].'" class="renderCheck"></div>
                                                    <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" checked name="checkbox'.$opcGrillaContable.'" value="'.$row['valor'].'" />
                                                    <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                      <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                      <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                    </label>
                                                  </div>';
              }
              else{
                $checkboxRetenciones[$row['id']] = '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetenciones'.$opcGrillaContable.'_'.$row['id'].'">
                                                      <div id="cargarCheckbox'.$opcGrillaContable.'_'.$row['id'].'" class="renderCheck"></div>
                                                      <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" checked name="checkbox'.$opcGrillaContable.'" value="'.$row['valor'].'" />
                                                      <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                        <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                        <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                      </label>
                                                    </div>';
              }
            }

            $acumScript.='arrayRetenciones'.$opcGrillaContable.'['.$row['id'].']='.$row['id'].';';
            $objectRetenciones[$row['id']]  = 'objectRetenciones_'.$opcGrillaContable.'['.$row['id'].'] = {'
                                                                                                            .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                                            .'base           : "'.$row['base'].'",'
                                                                                                            .'valor          : "'.$row['valor'].'",'
                                                                                                            .'cuenta         : "'.$row['cuenta_venta'].'",'
                                                                                                            .'estado         : "0"'
                                                                                                        .'}';

        }

        // CARGAR LA FORMA DE PAGO POR DEFECTO
        $sql   = "SELECT id_forma_pago FROM terceros WHERE activo=1 AND id_empresa='$id_empresa' AND id='$id_cliente' ";
        $query = mysql_query($sql,$link);
        $id_forma_pago = mysql_result($query,0,'id_forma_pago');
        if ($id_forma_pago>0) {
            $acumScript .= 'document.getElementById("formasDePagoFacturaVenta").value="'.$id_forma_pago.'";
                            UpdateFormaPago'.$opcGrillaContable.'("'.$id_forma_pago.'"); ';
        }

       //CARGA DATOS EN LA INTERFACE
        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 130,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaVencimiento'.$opcGrillaContable.'",
                            editable   : false,
                            value      : fechaVencimientoFactura,
                            listeners  : { select: function() { UpdateFormaPago'.$opcGrillaContable.'(document.getElementById("formasDePago'.$opcGrillaContable.'").value);} }
                        });

                        document.getElementById("codCliente'.$opcGrillaContable.'").value     = "'.$cod_cliente.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value     = "'.$nit.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value  = "'.$cliente.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha.'";
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$nombre_vendedor.'";

                        id_cliente_'.$opcGrillaContable.'   = "'.$id_cliente.'";
                        observacion'.$opcGrillaContable.'   = "'.$observacion_cotizacion.'";
                        codigoCliente'.$opcGrillaContable.' = "'.$cod_cliente.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$cliente.'";

                    ';

        include("../bd/functions_body_article.php");
        $bodyArticle = cargaArticulosSave($id_factura_venta,$observacion,0,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

    }

    //=========================== NUEVA FACTURA ==============================//
    //************************************************************************//
    else if(!isset($id_factura_venta)){
        // VALIDAR QUE EL USUARIO SE ENCUETRE EN VARIABLES DE SESION
        if ($id_usuario=='' || $id_usuario==0) { echo '<script>alert("Error!n\Cierre sesion e inicie de nuevo");</script>'; exit;}
        // CREACION DEL ID UNICO
        $random_factura = responseUnicoRanomico();
        validateConsecutivo($opcGrillaContable,$id_empresa,$link);                     // VALIDACION DEL NUMERO CONSECUTIVO

        $sqlInsert   = "INSERT INTO $tablaPrincipal (id_empresa,random,fecha_inicio,id_sucursal,id_bodega,documento_vendedor,nombre_vendedor,id_metodo_pago,metodo_pago,id_forma_pago,dias_pago,forma_pago,id_configuracion_cuenta_pago,configuracion_cuenta_pago,cuenta_pago,cuenta_pago_niif,id_usuario)
                        VALUES('$id_empresa','$random_factura','$fecha','$id_sucursal','$filtro_bodega','".$_SESSION['CEDULAFUNCIONARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."','$idMetodoPago','$nombreMetodoPago','$idFormaPago','$diasFormaPago','$nombreFormaPago','$idConfigCuentaPago','$configuracionCuentaPago','$CuentaPago','$CuentaPagoNiif','$id_usuario')";
        $queryInsert = mysql_query($sqlInsert,$link);

        $sqlSelectId      = "SELECT id FROM $tablaPrincipal  WHERE random='$random_factura' LIMIT 0,1";
        $id_factura_venta = mysql_result(mysql_query($sqlSelectId,$link),0,'id');


        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 130,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaVencimiento'.$opcGrillaContable.'",
                            editable   : false,
                            value      : fechaVencimientoFactura,
                            listeners  : { select: function() { UpdateFormaPago'.$opcGrillaContable.'(document.getElementById("formasDePago'.$opcGrillaContable.'").value); } }
                        });
                        document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";';
    }

    //========================== FACTURA EXISTENTE ===========================//
    //************************************************************************//
    else{

        include("../bd/functions_body_article.php");

       $sql = "SELECT
                  id_cliente,
                  cod_cliente,
                  numero_factura AS consecutivo,
                  nit,
                  cliente,
                  id_sucursal_cliente,
                  date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                  fecha_vencimiento,
                  REPLACE(observacion,'\"','') AS observacion,
                  orden_compra,
                  estado,
                  exento_iva,
                  nombre_vendedor,
                  valor_anticipo,
                  plantillas_id,
                  id_configuracion_cuenta_pago,
                  codigo_centro_costo,
                  centro_costo,
                  id_forma_pago,
                  id_metodo_pago,
                  response_FE,
                  id_configuracion_resolucion,
                  numero_factura_completo
                FROM
                  $tablaPrincipal
                WHERE
                  id = '$id_factura_venta'
                AND
                  activo = 1";

        $query = mysql_query($sql,$link);

        $nit                         = mysql_result($query,0,'nit');
        $cliente                     = mysql_result($query,0,'cliente');
        $id_cliente                  = mysql_result($query,0,'id_cliente');
        $cod_cliente                 = mysql_result($query,0,'cod_cliente');
        $idPlantilla                 = mysql_result($query,0,'plantillas_id');
        $fecha                       = mysql_result($query,0,'fecha');
        $fecha_vencimiento           = mysql_result($query,0,'fecha_vencimiento');
        $estado                      = mysql_result($query,0,'estado');
        $exento_iva                  = mysql_result($query,0,'exento_iva');
        $nombre_vendedor             = mysql_result($query,0,'nombre_vendedor');
        $valor_anticipo              = mysql_result($query,0,'valor_anticipo');
        $consecutivo                 = mysql_result($query,0,'consecutivo');
        $idConfigCuentaPago          = mysql_result($query,0,'id_configuracion_cuenta_pago');
        $codigoCcos                  = mysql_result($query,0,'codigo_centro_costo');
        $nombreCcos                  = mysql_result($query,0,'centro_costo');
        $idSucursalCliente           = mysql_result($query,0,'id_sucursal_cliente');
        $id_forma_pago               = mysql_result($query,0,'id_forma_pago');
        $id_metodo_pago              = mysql_result($query,0,'id_metodo_pago');
        $response_FE                 = mysql_result($query,0,'response_FE');
        $id_configuracion_resolucion = mysql_result($query,0,'id_configuracion_resolucion');
        $numero_factura_completo     = mysql_result($query,0,'numero_factura_completo');

        $labelCcos = $codigoCcos.' '.$nombreCcos;

        if ($opcGrillaContable=='FacturaVenta' && $estado=='1') { echo "ESTA FACTURA DE VENTA ESTA CERRADA "; exit; }
        if ($opcGrillaContable=='FacturaVenta' && $estado== 0 && ($consecutivo==0 || $consecutivo=='')){ $fecha = date('Y-m-d'); }
        if ($id_configuracion_resolucion<>'' && $numero_factura_completo<>'' ) {
            $acumScript .= "document.getElementById('id_configuracion_resolucion').disabled = true;
                            document.getElementById('id_configuracion_resolucion').value = $id_configuracion_resolucion;";
        }

        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));
        $orden_compra        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'orden_compra'));

        $acumScript .= 'new Ext.form.DateField({
                            format     : "Y-m-d",
                            width      : 130,
                            allowBlank : false,
                            showToday  : false,
                            applyTo    : "fechaVencimiento'.$opcGrillaContable.'",
                            editable   : false,
                            listeners  : { select: function() {  UpdateFormaPago'.$opcGrillaContable.'(document.getElementById("formasDePago'.$opcGrillaContable.'").value); } }
                        });

                        document.getElementById("plantilla").value                                  = "'.$idPlantilla.'";
                        document.getElementById("codCliente'.$opcGrillaContable.'").value           = "'.$cod_cliente.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value           = "'.$nit.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value        = "'.$cliente.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value                = "'.$fecha.'";
                        document.getElementById("fechaVencimiento'.$opcGrillaContable.'").value     = "'.$fecha_vencimiento.'";
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value       = "'.$nombre_vendedor.'";
                        document.getElementById("formasDePago'.$opcGrillaContable.'").value         = "'.$id_forma_pago.'";
                        document.getElementById("metodosDePago'.$opcGrillaContable.'").value        = "'.$id_metodo_pago.'";
                        document.getElementById("orden_compra_'.$opcGrillaContable.'").value        = "'.$orden_compra.'";

                        id_cliente_'.$opcGrillaContable.'   = "'.$id_cliente.'";
                        codigoCliente'.$opcGrillaContable.' = "'.$cod_cliente.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$cliente.'";
                        observacion'.$opcGrillaContable.'   = "'.$observacion.'";

                        ';


        $sqlArrayRetenciones = "SELECT
                                  id_retencion AS id,
                                  retencion,
                                  tipo_retencion,
                                  valor,
                                  base
                                FROM
                                  $tablaRetenciones
                                WHERE
                                  activo=1
                                AND
                                  $idTablaPrincipal = '$id_factura_venta'
                                GROUP BY
                                  id_retencion";

        $queryArrayRetenciones = mysql_query($sqlArrayRetenciones,$link);

        while($row = mysql_fetch_array($queryArrayRetenciones)){
            $row['valor'] = $row['valor'] * 1;
            $arrayTypeRetenciones .= 'arrayTypeRetenciones'.$opcGrillaContable.'['.$row['id'].'] = "'.$row['tipo_retencion'].'";';

            if (gettype($checkboxRetenciones[$row['id']]) =='NULL') {
                $checkboxRetenciones[$row['id']] = '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetenciones'.$opcGrillaContable.'_'.$row['id'].'">
                                                        <div id="cargarCheckbox'.$opcGrillaContable.'_'.$row['id'].'" class="renderCheck"></div>
                                                        <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" checked name="checkbox'.$opcGrillaContable.'" value="'.$row['valor'].'" />
                                                        <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                            <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                            <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                        </label>
                                                    </div>';
            }
            else{
                $checkboxRetenciones[$row['id']] = '<div class="campoCheck" title="'.$row['retencion'].'" id="contenedorRetenciones'.$opcGrillaContable.'_'.$row['id'].'">
                                                        <div id="cargarCheckbox'.$opcGrillaContable.'_'.$row['id'].'" class="renderCheck"></div>
                                                        <input type="hidden" class="capturarCheckboxAcumulado'.$opcGrillaContable.'" id="checkboxRetenciones'.$opcGrillaContable.'_'.$row['id'].'" checked name="checkbox'.$opcGrillaContable.'" value="'.$row['valor'].'" />
                                                        <label class="capturaLabelAcumulado'.$opcGrillaContable.'" for="checkbox_'.$row['retencion'].'">
                                                            <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                                            <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                                        </label>
                                                    </div>';
            }

            $acumScript.='arrayRetenciones'.$opcGrillaContable.'['.$row['id'].']='.$row['id'].';';

            $objectRetenciones[$row['id']]  = 'objectRetenciones_'.$opcGrillaContable.'['.$row['id'].'] = {'
                                                                                        .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                        .'base           : "'.$row['base'].'",'
                                                                                        .'valor          : "'.$row['valor'].'",'
                                                                                        .'cuenta         : "'.$row['cuenta_venta'].'",'
                                                                                        .'estado         : "0"'
                                                                                    .'}';
        }
        echo '<script>'.$arrayTypeRetenciones.' </script>';

        if ( $response_FE == 'Ejemplar recibido exitosamente pasara a verificacion' && $permiso_editar_factura_eviada == 'false') {
            $estado = 1;
            $addDiv = "<div class='bodyDivArticulosFacturaVenta' id='bodyDivArticulosFacturaVenta_99999999999' style='display:none;'><div id='divImageSaveFacturaVenta_99999999999' title='Guardar Articulo' style='width:20px; float:left; margin-top:3px;cursor:pointer;'><img src='img/edit.png' id='imgSaveArticuloFacturaVenta_99999999999'></div><input type='hidden' id='idArticuloFacturaVenta_99999999999' value='0'></div>";

            $acumScript .= "$('.hideFE').hide();
                            $('#iconoRetenciones').hide();
                            $('#formasDePagoFacturaVenta').attr('disabled', true);
                            $('#metodosDePagoFacturaVenta').attr('disabled', true);
                            $('#selectCuentaPagoVenta').attr('disabled', true);
                            $('#codClienteFacturaVenta').attr('readonly', 'readonly');
                            $('#nitClienteFacturaVenta').attr('readonly', 'readonly');
                            $('#sucursalClienteFacturaVenta').attr('disabled', true);
                            $('#orden_compra_FacturaVenta').attr('readonly', 'readonly');
                            $('#DivArticulosFacturaVenta').append(\"$addDiv\");
                            ";
            $divInfo ="<div class='infMsj'>
                            <strong>Informacion</strong><br>
                            Esta factura ha sido enviada como factura electronica, por esa razon solo se podra modificar el centro de costos al editarla
                        </div>";

        }

        $bodyArticle = cargaArticulosSave($id_factura_venta,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);
    }

    //========================= SUCURSALES TERCERO ===========================//
    //************************************************************************//
    $sqlSucursales   = "SELECT id,nombre FROM terceros_direcciones WHERE id_tercero=$id_cliente AND activo=1";
    $querySucursales = mysql_query($sqlSucursales,$link);

    while ($row=mysql_fetch_array($querySucursales)) {
        $sucursalesCliente .= '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
    }
    $acumScript .= 'document.getElementById("sucursalCliente'.$opcGrillaContable.'").value="'.$idSucursalCliente.'";';


    $acumScript .= 'exento_iva_'.$opcGrillaContable.' = "'.$exento_iva.'";
                    document.getElementById("selectCuentaPagoVenta").value="'.$idConfigCuentaPago.'";';
    $habilita   = ($estado=='1')? 'onclick="javascript: return false;" disabled': '';

    //============== DOCUMENTOS AGREGADOS A LA PRESENTE FACTURA ==============//
    //************************************************************************//

    $acumDocReferencia  = '';
    $margin_left        = 'margin-left:5px';
    $sqlDocReferencia   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
                            FROM ventas_facturas_inventario
                            WHERE id_consecutivo_referencia>0 AND id_factura_venta='$id_factura_venta' AND activo=1 AND id_empresa='$id_empresa'
                            ORDER BY id ASC";
    $queryDocReferencia = mysql_query($sqlDocReferencia,$link);

    while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){
        if($rowDocReferencia['doc_referencia'] == 'C'){ $title = 'Eliminar los Articulos de la Cotizacion'; }
        else if($rowDocReferencia['doc_referencia'] == 'P'){ $title = 'Eliminar los Articulos del Pedido'; }
        else if($rowDocReferencia['doc_referencia'] == 'R'){ $title = 'Eliminar los Articulos de la Remision'; }

        $typeDocCruce   = $rowDocReferencia['doc_referencia'];
        $numeroDocCruce = $rowDocReferencia['consecutivo_referencia'];

        if ($response_FE == 'Ejemplar recibido exitosamente pasara a verificacion' && $permiso_editar_factura_eviada == 'false') {
            $btn = '<span style="display:none;" id="labelFacturaVenta_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['cod_referencia'].'"></span>';
        }
        else{
            $btn = '<div title="'.$title.' # '.$rowDocReferencia['cod_referencia'].' en la presente factura" onclick="eliminaDocReferenciaFactura(\''.$rowDocReferencia['id_referencia'].'\',\''.$rowDocReferencia['doc_referencia'].'\',\''.$id_factura_venta.'\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">
                        <div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btn'.$opcGrillaContable.'_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                        </div>
                    </div>';
        }

        $acumDocReferencia .= '<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;height: 22px;" id="divDocReferenciaFactura_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                    <div class="contenedorInputDocReferenciaFactura">
                                        <input type="text" class="inputDocReferenciaFactura" value="'.$rowDocReferencia['doc_referencia'].' '.$rowDocReferencia['cod_referencia'].'" readonly style="border-bottom: 1px solid #d4d4d4;" />
                                    </div>
                                    '.$btn.'
                                </div>';
    }


    $selectPlantilla   = '<option value="0">Seleccione...</option>';
    $sqlPlantilla      = "SELECT id,descripcion FROM plantillas WHERE id_empresa='$id_empresa' AND activo=1 AND referencia='Venta'";
    $querySqlPlantilla = mysql_query($sqlPlantilla,$link);
    while($rowPlantilla = mysql_fetch_array($querySqlPlantilla)){ $selectPlantilla .= '<option value="'.$rowPlantilla['id'].'">'.$rowPlantilla['descripcion'].'</option>'; }

    //============================// ANTICIPOS //=============================//
    //************************************************************************//
    $sqlAnticipos  = "SELECT
                        SUM(valor) AS valorAnticipos
                      FROM
                        anticipos
                      WHERE
                        id_documento = '$id_factura_venta'
                      AND
                        activo = 1
                      AND
                        tipo_documento = 'FV'
                      AND
                        id_empresa = '$id_empresa'";

    $queryAnticipos = mysql_query($sqlAnticipos,$link);
    $totalAnticipo  = mysql_result($queryAnticipos,0,'valorAnticipos');
    $totalAnticipo *= 1;

    //IMPRIMIR OBJECT RETENCIONES//
    $plainRetenciones = implode(';', $objectRetenciones).';';
    echo '<script>'.$plainRetenciones.' //console.log(objectRetenciones_FacturaVenta);</script>';

    //CONSULTAR LAS RESOLUCIONES DE FACTURACION
    $sql="SELECT id_resolucion,predeterminada FROM ventas_facturas_configuracion_sucursales WHERE activo=1 AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $whereResoluciones .= ($whereResoluciones=='')? " id=$row[id_resolucion] " : " OR id=$row[id_resolucion] " ;
        $arrayResoluciones[$row['id_resolucion']] = $row['predeterminada'];
    }

    //SI NO HAY RESOLUCIONES CONFIGURADAS MOSTRAR ERROR
    if(empty($arrayResoluciones)){
      echo '<script>alert("Aviso\nNo hay resoluciones configuradas!\nDirijase al panel de control y configure las resoluciones");</script>';
    }
    else{
      $sql = "SELECT id,prefijo,consecutivo_resolucion,fecha_resolucion,fecha_final_resolucion,numero_inicial_resolucion,numero_final_resolucion
              FROM ventas_facturas_configuracion WHERE activo = 1 AND id_empresa = $id_empresa AND consecutivo_factura <= numero_final_resolucion AND($whereResoluciones)  AND fecha_final_resolucion >= '$fecha'";
      $query = $mysql->query($sql,$mysql->link);
      while($row = $mysql->fetch_array($query)){
        $prefijo = ($row['prefijo'] <> '')? " $row[prefijo] - " : "";
        $selected = ($arrayResoluciones[$row['id']] == 'Si')? "selected" : "";
        $id_resolucion = ($arrayResoluciones[$row['id']] == 'Si')? $row['id'] : $id_resolucion;
        $fecha_res .= ($arrayResoluciones[$row['id']] == 'Si')?$row['fecha_final_resolucion']:'';
        $optionResoluciones .= "<option value='$row[id]' $selected>$prefijo $row[consecutivo_resolucion]</option>";
      }

      //SI HAY UNA RESOLUCION POR DEFECTO ENTONCES ACTUALIZAR LA CABECERA DE LA FACTURA
      if($id_resolucion > 0 && $id_configuracion_resolucion == ""){
        $sql = "UPDATE ventas_facturas SET id_configuracion_resolucion = $id_resolucion WHERE activo = 1 AND id_empresa = $id_empresa AND id = $id_factura_venta";
        $query = $mysql->query($sql,$mysql->link);
      }

        $current_timestamp = time();
        $fecha_res_timestamp = strtotime($fecha_res);
        // Calculate the difference in seconds
        $seconds_difference = abs($current_timestamp - $fecha_res_timestamp);

        // Calculate the difference in days
        $days_difference = floor($seconds_difference / (60 * 60 * 24));
    }
    if(($estado !== '' && $consecutivo !== '') || $days_difference > 7){
       echo "<script>if(document.querySelector('#titleRes') !==null){document.querySelector('#titleRes').innerHTML='';}</script>";
    }elseif($days_difference < 7){
    echo "<script>if(document.querySelector('#titleRes') !==null){ document.querySelector('#titleRes').innerHTML='<b>Fecha de vencimeinto resolucion</b><br>$fecha_res';}</script>";
    }
?>
<style>
    .infMsj{
        color            : #8a6d3b;
        background-color : #fcf8e3;
        border-color     : #faebcc;
        padding          : 10px;
        margin-left      : 10px;
        margin-right     : 10px;
        margin-top       : 10px;
        border           : 1px solid #d0c2aa;
    }
    .infMsj strong{
        font-weight: 700;
    }
</style>
<div class="contenedorOrdenCompra">
  <div class="bodyTop">
    <div class="contInfoFact">
      <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <?php echo $divInfo; ?>

      <div class="contTopFila">

        <div class="renglonTop">
          <div class="labelTop">Resolucion Facturacion</div>
          <div id="loadResolucion" style="float:left; margin-top:-22px; width:20px; height:19px;"></div>
          <div class="campoTop" style="height:auto;"  id="contenedorDocsReferenciaFactura">
            <select style="height: auto;" onchange="actualizaResolucion(this.value)" id="id_configuracion_resolucion">
                <option value="">Seleccione...</option>
                <?php echo $optionResoluciones ?>
            </select>
          </div>
        </div>

        <div class="renglonTop">
          <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
          <div class="labelTop">Fecha</div>
          <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly/></div>
          <div class="iconBuscarProveedor hideFE" onclick="abrirVentanaUpdateFecha<?php echo $opcGrillaContable; ?>(this)" title="Editar Fecha">
            <img src="img/config16.png" style="margin: 2px 0 0 2px;"/>
          </div>
        </div>
        <div class="renglonTop" style="display:none;">
          <div id="cargaFechaVencimiento<?php echo $opcGrillaContable; ?>"></div>
          <div class="labelTop">Fecha Vencimiento</div>
          <div class="campoTop"><input type="text" id="fechaVencimiento<?php echo $opcGrillaContable; ?>"  readonly></div>
        </div>

        <div class="renglonTop" style="width:137px;">
          <div class="labelTop" style="float:left; width:100%;">Forma de pago</div>
          <div id="renderSelectFormaPago<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop" id="selectFormaPago<?php echo $opcGrillaContable; ?>">
            <select name="formas_de_pago" id="formasDePago<?php echo $opcGrillaContable; ?>" onChange="UpdateFormaPago<?php echo $opcGrillaContable; ?>(this.value)" style="float:left;"/>
              <?php echo $formasPago; ?>
            </select>
            <?php echo $arrayFormasPago; ?>
            <input type="hidden" id="fechaFinal<?php echo $opcGrillaContable; ?>" />
          </div>
        </div>

        <div class="renglonTop" style="width:150px;">
          <div class="labelTop" style="float:left; width:100%;">Metodo de pago</div>
          <div id="renderSelectMetodoPago<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop" id="selectMetodoPago<?php echo $opcGrillaContable; ?>">
            <select name="metodos_de_pago" id="metodosDePago<?php echo $opcGrillaContable; ?>" onChange="UpdateMetodoPago<?php echo $opcGrillaContable; ?>(this.value)" style="float:left;"/>
              <?php echo $metodosPago; ?>
            </select>
          </div>
        </div>

        <div class="renglonTop" style="width:137px;">
          <div class="labelTop" style="float:left; width:100%;">Cuenta de pago</div>
          <div id="renderSelectCuentaPago" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop">
            <select id="selectCuentaPagoVenta" onChange="UpdateCuentaPagoVenta(this.value)" style="float:left;"/>
              <?php echo $cuentasPago; ?>
            </select>
          </div>
        </div>

        <div class="renglonTop">
          <div class="labelTop">Docs. Cruce</div>
          <div class="campoTop" style="height:auto;"  id="contenedorDocsReferenciaFactura"><?php echo $acumDocReferencia; ?></div>
        </div>

        <div class="renglonTop">
          <div class="labelTop">Codigo Cliente</div>
          <div class="campoTop"><input type="text" id="codCliente<?php echo $opcGrillaContable; ?>" /></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Nit</div>
          <div class="campoTop"><input type="text" id="nitCliente<?php echo $opcGrillaContable; ?>" /></div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Cliente</div>
          <div class="campoTop" style="width:277px;"><input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" Readonly/></div>
          <div class="iconBuscarProveedor hideFE" onclick="buscarVentanaCliente<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Cliente">
            <img src="img/buscar20.png"/>
          </div>
        </div>
        <div class="renglonTop" style="width:137px;">
          <div class="labelTop" style="float:left; width:100%;">Sucursal Cliente</div>
          <div id="renderSelectSucursalCliente<?php echo $opcGrillaContable; ?>" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop" id="contenedorSucursalCliente<?php echo $opcGrillaContable; ?>">
            <select id="sucursalCliente<?php echo $opcGrillaContable; ?>" onChange="updateSucursalCliente<?php echo $opcGrillaContable; ?>(this)" style="float:left;"/>
              <?php echo $sucursalesCliente; ?>
            </select>
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Vendedor</div>
          <div class="campoTop" style="width:277px;"><input type="text" id="nombreVendedor<?php echo $opcGrillaContable; ?>" value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>" Readonly/></div>
          <div class="iconBuscarProveedor hideFE" onclick="buscarVentanaVendedor<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Vendedor">
            <img src="img/buscar20.png"/>
          </div>
        </div>
        <div class="renglonTop" id="checksRetenciones<?php echo $opcGrillaContable; ?>">
          <div class="labelTop" ><div id="renderCheck" style="margin-left:0px;" class="renderCheck" ></div>Retenciones <img src="img/config16.png" id="iconoRetenciones" style="float:right;cursor:pointer;margin: 0 3 0 0;width: 14;height: 14;" onclick="ventanaConfigurarRetenciones<?php echo $opcGrillaContable; ?>()" title="Configurar Retenciones" /></div>
          <div class="contenedorCheckbox" id="contenedorCheckbox<?php echo $opcGrillaContable; ?>">
            <?php foreach ($checkboxRetenciones as $valor) { echo  $valor; } ?>
          </div>
        </div>
        <div class="renglonTop" style="width:137px;display:none">
          <div class="labelTop" style="float:left; width:100%; ">Plantilla</div>
          <div id="renderSelectPlantillaVenta" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop">
            <select id="plantilla" onChange="UpdateCuentaPlantillaVenta(this.value)" style="float:left;">
              <?php echo $selectPlantilla; ?>
            </select>
          </div>
        </div>
        <div class="renglonTop" style="width:137px;">
          <div class="labelTop" style="float:left; width:100%;">Centro de Costo</div>
          <div id="renderSelectCcos" style="float:left; margin-left:-22px; width:20px; height:19px; overflow:hidden;"></div>
          <div class="campoTop"><input type="text" id="cCos_<?php echo $opcGrillaContable; ?>" value="<?php echo $labelCcos; ?>" Readonly/></div>
          <div class="iconBuscarProveedor" onclick="ventanaCcos_<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Centro de Costo">
            <img src="img/buscar20.png"/>
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop">Anticipos</div>
          <div class="campoTop"><input type="text" id="anticipo_<?php echo $opcGrillaContable; ?>" value="<?php echo '$ '.number_format($totalAnticipo); ?>" Readonly/></div>
          <div class="iconBuscarProveedor hideFE" onclick="ventanaAnticipo_<?php echo $opcGrillaContable; ?>()" title="Valor anticipo">
            <img src="img/config16.png" style="margin: 2px 0 0 2px;"/>
          </div>
        </div>
        <div class="renglonTop">
          <div class="labelTop" title="Orden(es) de Compra" ><div style="float: left;width: 90%;">OC</div><div id="labelOC<?php echo $opcGrillaContable; ?>" style="float: left;width: 10%;"></div> </div>
          <div class="campoTop"><input type="text" id="orden_compra_<?php echo $opcGrillaContable; ?>" onkeydown="inputOC<?php echo $opcGrillaContable; ?>(event,this)"/></div>
        </div>


      </div>
    </div>
  </div>
  <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
    <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>">
      <?php echo $bodyArticle; ?>
    </div>
  </div>
  <div id="load<?php echo $opcGrillaContable; ?>" style="display: none;" ></div>
</div>
<script>

    var observacion<?php echo $opcGrillaContable; ?> = '';
    <?php echo $acumScript; ?>

    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();               //disable boton imprimir
    document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").focus();     //foco campo codigo cliente

    document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").onkeyup = function(event){ return buscarCliente<?php echo $opcGrillaContable; ?>(event,this); };
    document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").onkeyup = function(event){ return buscarCliente<?php echo $opcGrillaContable; ?>(event,this); };

    calculaPlazo<?php echo $opcGrillaContable; ?>();
    UpdateFormaPago<?php echo $opcGrillaContable; ?>(document.getElementById("formasDePago<?php echo $opcGrillaContable; ?>").value);
    UpdateMetodoPago<?php echo $opcGrillaContable; ?>(document.getElementById("metodosDePago<?php echo $opcGrillaContable; ?>").value);

   document.getElementById('fecha<?php echo $opcGrillaContable; ?>').style.overflow='hidden !important';

   // ACTUALIZAR LA RESOLUCION DE LA CABECERA
   function actualizaResolucion(id_resolucion) {
       Ext.get('loadResolucion').load({
           url     : 'bd/bd.php',
           scripts : true,
           nocache : true,
           params  :
           {
               opc              : 'actualizaResolucion',
               id_factura_venta : '<?php echo $id_factura_venta ?>',
               id_resolucion    : id_resolucion,
           }
       });
   }

    //========= FUNCION PARA CALCULAR LOS DIAS RESTANTES DE PLAZO DE PAGO DE LA FACTURA ==============//
    function calculaPlazo<?php echo $opcGrillaContable; ?>(){
        var combo    = document.getElementById("formasDePago<?php echo $opcGrillaContable; ?>")
        ,   idFecha  = document.getElementById("formasDePago<?php echo $opcGrillaContable; ?>").value;

        var meses     = new Array ("Ene.","Feb.","Marzo","Abril","Mayo","Jun.","Jul.","Agos.","Sept.","Oct.","Nov.","Dic.");
        var arrayDays = new Array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");

        var fechalimite   =  Date.parse(document.getElementById('fecha<?php echo $opcGrillaContable; ?>').value);
        var myDate        = new Date(fechalimite);
        var diasRestantes = parseInt(arrayFormaPago<?php echo $opcGrillaContable; ?>[idFecha])+ parseInt(1);
        myDate.setDate(myDate.getDate()+diasRestantes);

        // Display the month, day, and year. getMonth() returns a 0-based number.
        var month = myDate.getMonth();
        var day = myDate.getDate();
        var day1 = myDate.getDay();
        var year = myDate.getFullYear();
        //AL MES LE SUMAMOS 1 DEBIDO A QUE TIENE UN NUMERO MENOS POR ESTRUCTURA DE JAVASCRIPT
        document.getElementById("fechaFinal<?php echo $opcGrillaContable; ?>").value=year+"-"+(month+1)+"-"+day;
        document.getElementById('selectFormaPago<?php echo $opcGrillaContable; ?>').setAttribute('title',(arrayDays[day1]+" "+day+" de "+meses[month]+" del "+year));

        //LLAMAMOS LA FUNCION PARA ACTUALIZAR LAS FECHAS EN LA FACTURA
        //updateFechas<?php echo $opcGrillaContable; ?>(document.getElementById("fecha<?php echo $opcGrillaContable; ?>").value,document.getElementById("fechaFinal<?php echo $opcGrillaContable; ?>").value);
    }

    //================== UPDATE CHECK RETENCIONES PROVEEDOR ==================//
    function checkboxRetenciones<?php echo $opcGrillaContable; ?>(Input){
        var action = 'insertar';
        if (!Input.checked){ action = 'eliminar'; }

        Ext.get("renderCheck").load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'checkboxRetenciones',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_factura_venta; ?>',
                idRetencion       : (Input.id).split('_')[1],
                accion            : action
            }
        });
        //recalculamos los valores de la factura
        calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(0,0,0,'','',0);
    }

    //======================= UPDATE FORMAS DE PAGO ==========================//
    function UpdateFormaPago<?php echo $opcGrillaContable; ?>(idFormaPago){
        Ext.get('renderSelectFormaPago<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'UpdateFormaPago',
                idFormaPago       : idFormaPago,
                plazoFormaPago    : arrayFormaPago<?php echo $opcGrillaContable; ?>[idFormaPago],
                fechaVencimiento  : document.getElementById("fechaVencimiento<?php echo $opcGrillaContable; ?>").value,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    //====================== UPDATE METODOS DE PAGO ==========================//
    function UpdateMetodoPago<?php echo $opcGrillaContable; ?>(idMetodoPago){
        var idSelectMetodo    = document.getElementById("metodosDePago<?php echo $opcGrillaContable; ?>");
        if(idSelectMetodo.selectedIndex == -1){ return; }
        var nombreMetodoPago  = idSelectMetodo.options[idSelectMetodo.selectedIndex].text;
        Ext.get('renderSelectMetodoPago<?php echo $opcGrillaContable; ?>').load({
            url     : "bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : "UpdateMetodoPago",
                idMetodoPago      : idMetodoPago,
                nombreMetodoPago  : nombreMetodoPago,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : "<?php echo $id_factura_venta; ?>"
            }
        });
    }

    //====================== UPDATE CUENTAS DE PAGO ==========================//
    function UpdateCuentaPagoVenta(idCuentaPago){
        Ext.get('renderSelectCuentaPago').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc          : 'UpdateCuentaPago',
                idCuentaPago : idCuentaPago,
                id           : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    //========================== UPDATE PLANTILLA ============================//
    function UpdateCuentaPlantillaVenta(idPlantilla){
        Ext.get('renderSelectPlantillaVenta').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'UpdateIdPlantilla',
                idPlantilla : idPlantilla,
                id          : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    //================= CAMBIA TIPO DE DESCUENTO POR ARTICULO ================//
    function tipoDescuentoArticulo<?php echo $opcGrillaContable; ?> (cont){
        document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
        // Si existe un articulo almacenado muestra el boton deshacer
        if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'block';
        }

        //si esta en signo porcentaje cambia a pesos, y viceversa
        if (document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png') {
            document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("src","img/pesos.png");
            document.getElementById('tipoDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("title","En Pesos");
            document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();
        }
        else{
            document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("src","img/porcentaje.png");
            document.getElementById('tipoDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).setAttribute("title","En Porcentaje");
            document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();
        }
    }

    //================  GUARDAR LA OBSERVACION DE LA FACTURA =================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
        document.getElementById('labelObservacion<?php echo $opcGrillaContable; ?>').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13 || tecla == 9){
            guardarObservacion<?php echo $opcGrillaContable; ?>();
        }

        clearTimeout(timeOutOC<?php echo $opcGrillaContable; ?>);
        timeOutOC<?php echo $opcGrillaContable; ?> = setTimeout(function(){
            guardarObservacion<?php echo $opcGrillaContable; ?>();
        },1500);

    }

    function guardarObservacion<?php echo $opcGrillaContable; ?>(){
        var observacion = document.getElementById('observacion<?php echo $opcGrillaContable; ?>').value;
        observacion = observacion.replace(/[\<\>\'\"]/g, '');
        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = '';

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  :
            {
                opc            : 'guardarObservacion',
                id             : '<?php echo $id_factura_venta; ?>',
                tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                observacion    : observacion
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

    //================  GUARDAR LA OBSERVACION DE LA FACTURA =================//
    function inputOC<?php echo $opcGrillaContable; ?>(event,input){
        document.getElementById('labelOC<?php echo $opcGrillaContable; ?>').innerHTML=' <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13 || tecla == 9){
            guardarOC<?php echo $opcGrillaContable; ?>();
        }

        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = setTimeout(function(){
            guardarOC<?php echo $opcGrillaContable; ?>();
        },1500);

    }

    function guardarOC<?php echo $opcGrillaContable; ?>(){
        var OCS = document.getElementById('orden_compra_<?php echo $opcGrillaContable; ?>').value;
        OCS = OCS.replace(/[\#\<\>\'\"]/g, '');
        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = '';

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  :
            {
                opc            : 'guardarOC',
                id             : '<?php echo $id_factura_venta; ?>',
                tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                OCS            : OCS
            },
            success :function (result, request){
                        if(result.responseText != 'true'){
                            // alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            // document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=OCS<?php echo $opcGrillaContable; ?>;
                            document.getElementById('labelOC<?php echo $opcGrillaContable; ?>').innerHTML=' <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                            setTimeout(function () {
                                document.getElementById('labelOC<?php echo $opcGrillaContable; ?>').innerHTML='';
                            },1200);
                        }
                        else{
                            observacion<?php echo $opcGrillaContable; ?>=OCS;
                            document.getElementById('labelOC<?php echo $opcGrillaContable; ?>').innerHTML=' <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardado</div>';
                            setTimeout(function () {
                                document.getElementById('labelOC<?php echo $opcGrillaContable; ?>').innerHTML='';
                            },1200);
                        }
                    },
            failure : function(){
                // alert('Error de conexion con el servidor');
                // document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=OCS<?php echo $opcGrillaContable; ?>;
                document.getElementById('labelOC<?php echo $opcGrillaContable; ?>').innerHTML=' <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
                setTimeout(function () {
                    document.getElementById('labelOC<?php echo $opcGrillaContable; ?>').innerHTML='';
                },1200);
            }
        });
    }

    //==================== FILTRO TECLA BUSCAR PROVEEDOR =====================//
    function buscarCliente<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla  = (Input) ? event.keyCode : event.which
        ,   numero = Input.value;

        if(Input.value != '' && id_cliente_<?php echo $opcGrillaContable;?> == 0 && tecla == 13){
            Input.blur();
            ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(Input.value, Input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        else if(id_cliente_<?php echo $opcGrillaContable;?> > 0 && contArticulos<?php echo $opcGrillaContable; ?> > 1){
            Input.blur();
            if(confirm('Esta seguro de cambiar de cliente y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){
                ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input);
            }
            else{
                document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value    = nitCliente<?php echo $opcGrillaContable; ?>;
                document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value    = codigoCliente<?php echo $opcGrillaContable; ?>;
                document.getElementById("nombreCliente<?php echo $opcGrillaContable;?>").value  = nombreCliente<?php echo $opcGrillaContable; ?>;
            }
        }
        else if(id_cliente_<?php echo $opcGrillaContable;?> > 0){ ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input); }
        return true;
    }

    function ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codCliente, inputId){
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCliente',
                codCliente        : codCliente,
                inputId           : inputId,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    function ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("sucursalCliente<?php echo $opcGrillaContable; ?>").innerHTML = '<option value="0">Seleccione...</option>';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor si es una factura
        if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {
            var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
            for(i in checks){ checks[i].checked=false; checks[i].checked=false; }
        }
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cambiaCliente',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_factura_venta; ?>',
            }
        });
    }

    //===================== VENTANA BUSCAR CLIENTE ===========================//
    function buscarVentanaCliente<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND tipo_cliente = \"Si\"';

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaCliente_<?php echo $opcGrillaContable; ?>',
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
                    sql           : sql,
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

        if(id != id_cliente_<?php echo $opcGrillaContable;?> && contArticulos<?php echo $opcGrillaContable; ?>>1){
            if(!confirm('Esta seguro de cambiar de cliente y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }
        }
        else if (id == id_cliente_<?php echo $opcGrillaContable;?>){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }

        ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(document.getElementById("codCliente<?php echo $opcGrillaContable; ?>"));
        id_cliente_<?php echo $opcGrillaContable;?>    = id;
        contArticulos<?php echo $opcGrillaContable; ?> = 1;

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
        ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(id,'idCliente<?php echo $opcGrillaContable; ?>');
    }

    //===================== VENTANA BUSCAR VENDEDOR ==========================//
    function buscarVentanaVendedor<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_VentanaVendedor_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>',
            title       : 'Vendedores',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaVendedor.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : '',
                    cargaFuncion  : 'renderizaResultadoVentanaVendedor<?php echo $opcGrillaContable; ?>(id);',
                    nombre_grilla : 'vendedor<?php echo $opcGrillaContable; ?>'
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
                    handler     : function(){ Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentanaVendedor<?php echo $opcGrillaContable; ?>(id){
        var documento = document.getElementById('div_vendedor<?php echo $opcGrillaContable; ?>_documento_'+id).innerHTML;
        var nombre    = document.getElementById('div_vendedor<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML;
        //mostramos el nombre del vendedor en el campo
        document.getElementById("nombreVendedor<?php echo $opcGrillaContable; ?>").value = nombre;
        ajaxGuardarVendedor<?php echo $opcGrillaContable; ?>(documento,nombre);

        Win_VentanaVendedor_<?php echo $opcGrillaContable; ?>.close();
    }

    //=========================================== AJAX VENDEDOR =============================================================//
    function ajaxGuardarVendedor<?php echo $opcGrillaContable; ?>(documento,nombre){
        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  :
            {
                opc            : 'guardarVendedor',
                id             : '<?php echo $id_factura_venta; ?>',
                tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                documento      : documento,
                nombre         : nombre
            },
            success :function (result, request){
                       if(result.responseText=="false"){
                        alert("Error!\nNo se almaceno el vendedor\nSi el problema persiste comuniquese con el administrador del sistema");
                       }
                    },
            failure : function(){ alert('Error de conexion con el servidor'); }
        });
    }

    //============================== FILTRO TECLA BUSCAR ARTICULO ==========================================================//
    function buscarArticulo<?php echo $opcGrillaContable; ?>(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   numero      = input.value
        ,   tecla       = (input) ? event.keyCode : event.which;

        if (tecla == 13 && numero>0) {
            input.blur();
            ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(input.value, input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }

        if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value      = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value     = "";
            document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";

            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display     = 'inline';
        }
        else if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value      = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value     = "";
            document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        }
        return true;
    }

    function ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(valor,input){
        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                whereBodega       : 'AND IT.id_sucursal=<?php echo $id_sucursal; ?>  AND IT.id_ubicacion=<?php echo $filtro_bodega ?>',
                campo             : arrayIdInput[0],
                valorArticulo     : valor,
                idArticulo        : arrayIdInput[1],
                exentoIva         : exento_iva_<?php echo $opcGrillaContable; ?>,
                idCliente         : id_cliente_<?php echo $opcGrillaContable;?>,
                id                : '<?php echo $id_factura_venta; ?>'
            }
        });
    }

    //======================== FUNCION PARA BUSCAR LA COTIZACION-PEDIDO POR SU NUMERO =======================================//
    function buscarCotizacionPedido<?php echo $opcGrillaContable; ?>(event,Input){
        var tecla  = (Input) ? event.keyCode : event.which
        ,   numero = Input.value;

        if(tecla == 13){
            var validacion= validarArticulos<?php echo $opcGrillaContable; ?>();
            if (validacion==1) {
                if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
            }
            else if (validacion== 2 || validacion== 0) { ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
            return;
        }
        patron = /[^\d]/;
        if(patron.test(numero)){ Input.value = numero.replace(patron,''); }

        return true;
    }

    function ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido){

        agregarDocumento<?php echo $opcGrillaContable; ?>(idCotizacionPedido);
        return;

        if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") {
            tablaBuscar ="ventas_cotizaciones";
            opcCargar   = "cotizacion";
        }
        else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/pedido.png"){
            opcCargar   = "pedido";
            tablaBuscar ="ventas_pedidos";
        }
        else{
            opcCargar   = "remision";
            tablaBuscar = "ventas_remisiones";
        }

        Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>").load({
            url     : "facturacion/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cargarDocuementoNewFactura',
                opcCargar         : opcCargar,
                tablaBuscar       : tablaBuscar,
                id                : idCotizacionPedido,
                id_factura        : '<?php echo $id_factura_venta ?>',
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            }
        });
    }

    //=========================== FUNCION PARA AGREGAR LA COTIZACION-PEDIDO-REMISION ====================================//
    function agregarDocumento<?php echo $opcGrillaContable; ?>(codigo){
        if (codigo!='') { var codDocAgregar=codigo; }
        else{ var codDocAgregar = document.getElementById('cotizacionPedido<?php echo $opcGrillaContable; ?>').value; }

        if(isNaN(codDocAgregar) || codDocAgregar==0){ alert('digite el consecutivo del documento que desea cargar.'); return;}

        if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") { typeDoc = "cotizacion"; }
        else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/pedido.png"){ typeDoc = "pedido"; }
        else{ typeDoc = "remision"; }

        Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>").load({
            url     : "facturacion/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'agregarDocumento',
                typeDoc           : typeDoc,
                codDocAgregar     : codDocAgregar,
                id_factura        : '<?php echo $id_factura_venta ?>',
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            }
        });
    }

    //======================================= FUNCION CAMBIA PROVEEDOR ==================================================//
    function ajaxCambia<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="contTopFila" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor y se deshabilitan
        var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
        for(i in checks){ checks[i].checked=false; checks[i].disabled=true; }

        Ext.get("contenedor_facturacion_compras").load({
            url     : "facturacion_compras.php",
            scripts : true,
            nocache : true,
            params  : { filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value }
        });
    }

    //======================== VENTANA BUSCAR PEDIDO - COTIZACION ===========================================================//
    function ventanaBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(){

        //SI VAMOS A CARGAR A UNA FACTURA
        if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/cotizacion.png") {
            //cargar factura desde una cotizacion
            titulo         = "Seleccione la Cotizacion";
            tablaGrilla    = "ventas_cotizaciones";
            nameGrillaLoad = "grillaCotizacionFactura";
        }
        else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/pedido.png"){
            //cargar factura desde un pedido
            titulo         = "Seleccione el Pedido";
            tablaGrilla    = "ventas_pedidos";
            nameGrillaLoad = "grillaPedidoFactura";
        }
        else{
            titulo         = "Seleccione la Remision";
            tablaGrilla    = "ventas_remisiones";
            nameGrillaLoad = "grillaRemisionFactura";
        }

        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>',
            title       : titulo,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'bd/grillaBuscarCotizacionPedido.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                   : 'buscar_cotizacionPedido',
                    id_documento          : '<?php echo $id_factura_venta; ?>',
                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                    tablaCotizacionPedido : tablaGrilla,
                    nameGrillaLoad        : nameGrillaLoad,
                    filtro_bodega         : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value,
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

    function cargarDocumento<?php echo $opcGrillaContable; ?>(){
        var idCotizacionPedido = document.getElementById('cotizacionPedido<?php echo $opcGrillaContable; ?>').value;
        if(idCotizacionPedido > 0 ){ ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido); }
        else{ return; }
    };

    //============================= FILTRO TECLA GUARDAR ARTICULO ==========================================================//
    function guardarAuto<?php echo $opcGrillaContable; ?>(event,input,cont){
        var idInsertArticulo = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tecla = (input) ? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur();
            guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont);
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        else if(idInsertArticulo>0) {
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

        patron = /[^\d.]/g;
        if(patron.test(value)){ input.value = input.value.replace(patron,''); }
        return true;
    }

    function guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont){

        var idRecetaArticulo  = 0;
        var idInsertArticulo  = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idInventario      = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var descuentoArticulo = document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;;
        var opc               = 'guardarArticulo';
        var divRender         = 'load<?php echo $opcGrillaContable; ?>';
        var accion            = 'agregar';
        var iva               = document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDesc          = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0];

        // VALIDAR SI ES UN INGREDIENTE DE UNA RECETA
        if (document.getElementById('idRecetaArticulo<?php echo $opcGrillaContable; ?>_'+cont)){
            idRecetaArticulo = document.getElementById('idRecetaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        }

        if (idInventario == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
        else if(cantArticulo <= 0 || cantArticulo == ''){document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo Cantidad es obligatorio'); setTimeout(function(){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);  return; }
        else if(costoArticulo < 0 || costoArticulo == ''){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur();alert('El campo precio es obligatorio'); document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();  return; }

        if(isNaN(descuentoArticulo)){
            setTimeout(function(){ document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20);
            setTimeout(function(){ alert('El campo descuento debe ser numerico'); },80);
            return;
        }

        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertArticulo > 0){
            opc       = 'actualizaArticulo';
            divRender = 'renderArticulo<?php echo $opcGrillaContable; ?>_'+cont;
            accion    = 'actualizar';
        }
        else if(idRecetaArticulo==0){
            //VALIDAMOS PARA NO REPETIR FILAS DE LAN GRILLA
            contArticulos<?php echo $opcGrillaContable; ?>++;
            temp=1+cont;
            if (contArticulos<?php echo $opcGrillaContable; ?> > temp) {
                contArticulos<?php echo $opcGrillaContable; ?>--;
                divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>;
                return;
            }else{
                divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>;
                var div   = document.createElement('div');
                div.setAttribute('id','bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>);
                div.setAttribute('class','bodyDivArticulos<?php echo $opcGrillaContable; ?>');
                document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').appendChild(div);
            }
        }

        Ext.get(divRender).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : opc,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                consecutivo       : contArticulos<?php echo $opcGrillaContable; ?>,
                cont              : cont,
                idRecetaArticulo  : idRecetaArticulo,
                idInsertArticulo  : idInsertArticulo,
                idInventario      : idInventario,
                cantArticulo      : cantArticulo,
                tipoDesc          : tipoDesc,
                descuentoArticulo : descuentoArticulo,
                costoArticulo     : costoArticulo,
                exento_iva        : exento_iva_<?php echo $opcGrillaContable; ?>,
                iva               : iva,
                id                : '<?php echo $id_factura_venta; ?>',
            }
        });

        //despues de registrar el primer articulo, habilitamos los botones de nueva factura
        Ext.getCmp("btnNueva<?php echo $opcGrillaContable; ?>").enable();

        //llamamos la funcion para calcular los totales de la facturan si accion = agregar
        if (accion=="agregar" && idRecetaArticulo==0) {
            calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo,descuentoArticulo,costoArticulo,accion,tipoDesc,iva,cont);
        }
    }

    //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
    function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont,callback='responseVentanaBuscarArticulo'){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND id_sucursal=<?php echo $id_sucursal; ?> AND id_ubicacion=<?php echo $filtro_bodega; ?> AND estado_venta="true"';
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
                url     : '../funciones_globales/grillas/BusquedaInventariosVentas.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : sql,
                    nombre_grilla : nombre_grilla,
                    nombreTabla   : 'inventario_totales',
                    cargaFuncion  : callback+'<?php echo $opcGrillaContable; ?>(id,'+cont+');'
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
                    handler     : function(){ Win_Ventana_buscar_Articulo_factura.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,cont){
        document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();

        var costoTotal     = 0
        ,   totalDescuento = 0
        ,   idArticulo     = document.getElementById('ventas_id_item_'+id).innerHTML
        ,   codigo         = document.getElementById('div_'+nombre_grilla+'_codigo_'+id).innerHTML
        ,   costo          = document.getElementById('div_'+nombre_grilla+'_precio_venta_'+id).innerHTML
        ,   unidadMedida   = document.getElementById('unidad_medida_grilla_'+id).innerHTML
        ,   nombreArticulo = document.getElementById('div_'+nombre_grilla+'_nombre_equipo_'+id).innerHTML
        ,   cantidad       = (document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1
        ,   tipoDescuento  = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0]
        ,   descuento      = (document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1;

        //SI EL TERCERO ESTA EXENTO DE IVA
        if(exento_iva_<?php echo $opcGrillaContable; ?> == 'Si'){
            if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
                document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';
            }
            else{ document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = "none"; }

            document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value       = unidadMedida;
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value     = idArticulo;
            document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = codigo;
            document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value  = costo;
            document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value = nombreArticulo;
            document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = 0;

            Win_Ventana_buscar_Articulo_factura.close();
            return;
        }
        else{
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscarImpuestoArticulo',
                    id                : '<?php echo $id_factura_venta; ?>',
                    id_inventario     : idArticulo,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cont              : cont,
                    unidadMedida      : unidadMedida,
                    idArticulo        : idArticulo,
                    codigo            : codigo,
                    costo             : costo,
                    nombreArticulo    : nombreArticulo,
                }
            });
        }
    }

    function responseVentanaBuscarIngrediente<?php echo $opcGrillaContable; ?>(id,cont){
        var cantidad = window.prompt("Cantidad del Ingrediente","1");
        if(isNaN(`${cantidad}`)){
          alert('La cantidad debe ser un valor numerico y si es decimal debe ser separado por punto');
          return;
        }
        // alert(cantidad); return;
        var div_ing = document.getElementById(`divIngredientes<?php echo $opcGrillaContable ?>_${cont}`);
        if(!div_ing){ return; }
        var costoTotal          = 0
        ,   id_fila_item_receta = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable ?>_'+cont).value
        ,   totalDescuento      = 0
        ,   idArticulo          = document.getElementById('ventas_id_item_'+id).innerHTML
        ,   codigo              = document.getElementById('div_'+nombre_grilla+'_codigo_'+id).innerHTML
        ,   precio              = document.getElementById('div_'+nombre_grilla+'_precio_venta_'+id).innerHTML
        ,   costo_inventario    = document.getElementById('div_'+nombre_grilla+'_costos_'+id).innerHTML
        ,   unidadMedida        = document.getElementById('unidad_medida_grilla_'+id).innerHTML
        ,   nombreArticulo      = document.getElementById('div_'+nombre_grilla+'_nombre_equipo_'+id).innerHTML
        ,   tipoDescuento       = ((document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute("src")).split('/')[1]).split('.')[0]
        ,   descuento           = (document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1
        ,   total               = 0

        costo=('<?php echo $tipo_valor ?>'=='CI')? costo_inventario : precio ;
        if (costo<=0){
            costo = window.prompt("El item no tiene costos, por favor digite el costo del item");
            if(isNaN(`${costo}`) || costo<=0 || costo==''){
              alert('El costo debe ser un valor numerico y si es decimal debe ser separado por punto');
              return;
            }
        }
        total = costo*cantidad;

        // CAPTURAR EL ULTIMO CONT DE LOS INGREDIENTES
        var contIng = 0;
        div_ing.childNodes.forEach(function(element) {
            contIng = (element.id.split('_')[1]*1>contIng)? element.id.split('_')[1]*1 : contIng ;
        });
        contIng += 0.01;
        var div_row_ing = `
                            <div class='campo' style='width:40px !important; overflow:hidden;'>
                            <div style='float:left; margin:3px 0 0 2px;'>${contIng}</div>
                            <div style='float:left; width:18px; overflow:hidden;' id='renderArticulo<?php echo $opcGrillaContable ?>_${contIng}'></div>
                            </div>
                            <div class='campo' style='width:12%;'>
                                <input type='text' id='eanArticulo<?php echo $opcGrillaContable ?>_${contIng}' value='${codigo}' onkeyup='buscarArticulo<?php echo $opcGrillaContable ?>(event,this);'>
                            </div>
                            <div class='campoNombreArticulo'><input type='text' value='${nombreArticulo}' id='nombreArticulo<?php echo $opcGrillaContable ?>_${contIng}' style='text-align:left;' readonly=''></div>
                            <div onclick='ventanaBuscarArticulo<?php echo $opcGrillaContable ?>contIng});' title='Buscar Articulo' class='iconBuscarArticulo'>
                                <img src='img/buscar20.png'>
                            </div>
                            <div class='campo'><input type='text' id='unidades<?php echo $opcGrillaContable ?>_${contIng}' style='text-align:left;' readonly='' value='${unidadMedida}'></div>
                            <div class='campo'><input type='text' id='cantArticulo<?php echo $opcGrillaContable ?>_${contIng}' value='${cantidad}' onkeyup=\"validarNumberArticulo<?php echo $opcGrillaContable ?>(event,this,'double','${contIng}\");'></div>
                            <div class='campo campoDescuento'>
                                <div onclick='tipoDescuentoArticulo<?php echo $opcGrillaContable ?>(${contIng})' id='tipoDescuentoArticulo<?php echo $opcGrillaContable ?>_${contIng}' title='En porcentaje'>
                                    <img src='img/porcentaje.png' id='imgDescuentoArticulo<?php echo $opcGrillaContable ?>_${contIng}'>
                                </div>
                                <input type='text' id='descuentoArticulo<?php echo $opcGrillaContable ?>_${contIng}' value='0' readonly onkeyup=\"validarNumberArticulo<?php echo $opcGrillaContable ?>(event,this,'double','${contIng}')\">
                            </div>
                            <div class='campo'><input type='text' id='costoArticulo<?php echo $opcGrillaContable ?>_${contIng}' onkeyup='guardarAuto<?php echo $opcGrillaContable ?>(event,this,${contIng});' value='${costo}'></div>
                            <div class='campo'><input type='text' id='costoTotalArticulo<?php echo $opcGrillaContable ?>_${contIng}' readonly='' value='${total}'></div>
                            <div style='float:right; min-width:80px;'>
                                <div onclick='guardarNewArticulo<?php echo $opcGrillaContable ?>(${contIng})' id='divImageSave<?php echo $opcGrillaContable ?>_${contIng}' title='Actualizar Articulo' style='width: 20px; float: left; margin-top: 3px; cursor: pointer; display: none;'><img src='img/reload.png' id='imgSaveArticulo<?php echo $opcGrillaContable ?>_${contIng}'></div>
                                <div onclick='retrocederArticulo<?php echo $opcGrillaContable ?>(${contIng})' id='divImageDeshacer<?php echo $opcGrillaContable ?>_${contIng}' title='Deshacer Cambios' style='width:20px; float:left; margin-top:3px;cursor:pointer;display:none'><img src='img/deshacer.png' id='imgDeshacerArticulo<?php echo $opcGrillaContable ?>_${contIng}'></div>
                                <div onclick='ventanaDescripcionArticulo<?php echo $opcGrillaContable ?>(${contIng})' id='descripcionArticulo<?php echo $opcGrillaContable ?>_${contIng}' title='Agregar Observacion' style='width: 20px; float: left; margin-top: 3px; display: block; cursor: pointer;'><img src='img/edit.png'></div>
                                <div onclick='deleteArticulo<?php echo $opcGrillaContable ?>(${contIng})' id='deleteArticulo<?php echo $opcGrillaContable ?>_${contIng}' title='Eliminar Articulo' style='width: 20px; float: left; margin-top: 3px; display: block; cursor: pointer;'><img src='img/delete.png'></div>
                            </div>
                            <input type='hidden' id='idRecetaArticulo<?php echo $opcGrillaContable ?>_${contIng}' value='${id_fila_item_receta}'>
                            <input type='hidden' id='idArticulo<?php echo $opcGrillaContable ?>_${contIng}' value='${idArticulo}'>
                            <input type='hidden' id='idInsertArticulo<?php echo $opcGrillaContable ?>_${contIng}' value=''>
                            <input type='hidden' id='ivaArticulo<?php echo $opcGrillaContable ?>_${contIng}' value=''>
                                `;

        var div_content_ing = document.createElement('div')
        div_content_ing.setAttribute('id',`bodyDivArticulos<?php echo $opcGrillaContable ?>_${contIng}`);
        div_content_ing.setAttribute('class',`bodyDivArticulos<?php echo $opcGrillaContable ?>`);
        div_content_ing.innerHTML = div_row_ing;
        div_ing.appendChild(div_content_ing);

        // Win_Ventana_buscar_Articulo_factura.close();

        //SI EL TERCERO ESTA EXENTO DE IVA
        if(exento_iva_<?php echo $opcGrillaContable; ?> == 'Si'){
            if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){                    //mostrar la imagen deshacer y actualizar
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
                document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display     = 'inline';
            }
            else{ document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = "none"; }

            document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value       = unidadMedida;
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value     = idArticulo;
            document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = codigo;
            document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value  = costo;
            document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value = nombreArticulo;
            document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = 0;

            Win_Ventana_buscar_Articulo_factura.close();
            return;
        }
        else{
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+contIng).load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscarImpuestoArticulo',
                    id                : '<?php echo $id_factura_venta; ?>',
                    id_inventario     : idArticulo,
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    cont              : contIng,
                    unidadMedida      : unidadMedida,
                    idArticulo        : idArticulo,
                    codigo            : codigo,
                    costo             : costo,
                    nombreArticulo    : nombreArticulo,

                },
                callback: function() {
                    guardarNewArticulo<?php echo $opcGrillaContable; ?>(contIng);
                }
            });
        }
    }

    function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){
        //antes de eliminar tomamos las variable para enviarlas a la funcion para recalcular los totales
        var idArticulo        = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var descuentoArticulo = document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var iva               = document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var tipoDesc          = '';
        var idRecetaArticulo = 0;

        // VALIDAR SI ES UN INGREDIENTE DE UNA RECETA
        if (document.getElementById('idRecetaArticulo<?php echo $opcGrillaContable; ?>_'+cont)){
            idRecetaArticulo = document.getElementById('idRecetaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        }

        if (document.getElementById('imgDescuentoArticulo<?php echo $opcGrillaContable; ?>_'+cont).getAttribute('src') == 'img/porcentaje.png') { tipoDesc='porcentaje'; }
        else{ tipoDesc='pesos'; }

        if(confirm('Esta Seguro de eliminar este articulo de la factura de compra?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : idArticulo,
                    cont              : cont,
                    id                : '<?php echo $id_factura_venta; ?>'
                }
            });

            if (idRecetaArticulo==0) {
              calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo,descuentoArticulo,costoArticulo,'eliminar',tipoDesc,iva,cont);
            }
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
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaDescripcionArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : id,
                    cont              : cont,
                    id                : '<?php echo $id_factura_venta; ?>'
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
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarDescripcionArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idArticulo        : idArticulo,
                id                : '<?php echo $id_factura_venta; ?>',
                observacion       : observacion
            }
        });
    }

    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===============================================//
    function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
        //capturamos el id que esta asignado en la variable oculta
        id_actual=document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;

        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'retrocederArticulo',
                cont              : cont,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idArticulo        : id_actual,
                id                : '<?php echo $id_factura_venta; ?>',
                exento_iva        : exento_iva_<?php echo $opcGrillaContable; ?>,
            }
        });
    }

    //===================================== FINALIZAR 'CERRAR' 'GENERAR' ===================================//
    function guardar<?php echo $opcGrillaContable; ?>(){
        if (document.getElementById('sucursalCliente<?php echo $opcGrillaContable; ?>').value==0 || document.getElementById('sucursalCliente<?php echo $opcGrillaContable; ?>').value=='') {
            alert('Seleccione la sucursal del cliente');
            return;
        }

        <?php
        if($_SESSION['EMPRESA']==1 || $_SESSION['EMPRESA']==47){
        ?>
        // CAMBIO APLICADO A PRODUCCION SOLO PARA JIMMY
        if (document.getElementById('cCos_<?php echo $opcGrillaContable; ?>').value==0 || document.getElementById('cCos_<?php echo $opcGrillaContable; ?>').value==''){
            alert('Seleccione el centro de costos');
            return;
        }
        <?php
        }
        ?>

        var idPlantilla  = document.getElementById("plantilla").value
        ,   idCuentaPago = document.getElementById('selectCuentaPagoVenta').value
        ,   fechaFactura = document.getElementById("fecha<?php echo $opcGrillaContable; ?>").value
        ,   validacion   = validarArticulos<?php echo $opcGrillaContable; ?>();
        // console.log(validacion);
        if(idCuentaPago == 0 && idPlantilla==0){ alert("Seleccione una cuenta de pago o plantilla en la presente factura de Venta!"); return; }
        else if (validacion==0) { alert("No se puede generar una factura sin articulos relacionados!"); return; }
        else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return; }
        else if (validacion== 2 || validacion== 0) {

            var idBodega    = document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            ,   observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;

            observacion = observacion.replace(/[\<\>\'\"]/g, '');
            cargando_documentos('Generando Factura de Venta...','');
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'facturacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'terminarGenerar',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_factura_venta; ?>',
                    idPlantilla       : idPlantilla,
                    idBodega          : idBodega,
                    observacion       : observacion,
                    fechaFactura      : fechaFactura
                }
            });
        }
    }

    //================================================= BUSCAR ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){
        var validacion= validarArticulos<?php echo $opcGrillaContable; ?>();
        if (validacion==1) {
            if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
        }
        else if (validacion== 2 || validacion== 0) {ventanaBuscar<?php echo $opcGrillaContable; ?>();}
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
                url     : 'bd/buscarGrillaContable.php',
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

        if(tecla == 13){
            if(nombreInput == 'cantArticulo<?php echo $opcGrillaContable; ?>'){
                var id_insert = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value;
                //SI EL OBJETO NO EXISTE, SE CARGO NORMALMENTE
                if (typeof(objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert])=='undefined') {
                    ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>');
                    document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
                }
                //SI EL OBJETO EXISTE, Y SUPERA LA CANTIDAD DEL DOCUMENTO CARGADO
                else if((objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].saldo_cantidad * 1)< (numero*1)){
                    input.blur();
                    alert('la cantidad no puede ser mayor a la ya registrada en el documento '+objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].typeDoc);
                    input.value = objDocumentosCruce<?php echo $opcGrillaContable; ?>[id_insert].saldo_cantidad;
                    setTimeout(function(){ input.focus(); },100);
                }
                //SI EL OBJETO EXISTE Y NO SUPERA LA CANTIDAD CARGADA
                else{
                    ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>');
                    document.getElementById('descuentoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus();
                }
            }
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
    function ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,cantidad,opc){
        var id = document.getElementById("idArticulo"+opc+"_"+cont).value;

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  :
            {
                opc               : 'verificaCantidadArticulo',
                id                : id,
                filtro_bodega     : '<?php echo $filtro_bodega; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            },
            success :function (result, request){
                        var saldoArticulo = result.responseText;
                        if((cantidad *1) > (saldoArticulo*1)){
                            alert("Error!\nLa cantidad ingresada es mayor a la existente\nSolo restan "+saldoArticulo+" unidades");
                            document.getElementById("cantArticulo"+opc+"_"+cont).value='';
                            setTimeout(function(){document.getElementById("cantArticulo"+opc+"_"+cont).focus();},100);
                        }
                        else if((cantidad *1) < saldoArticulo){ document.getElementById("descuentoArticulo"+opc+"_"+cont).focus(); }
                        else if(saldoArticulo=='false'){ alert("Error!\nSe produjo un problema con la validacion\nNo se verifico la cantidad del Articulo\nSi el problema persiste comuniquese con el administrador del sistema"); }
                    },
            failure : function(){ alert('Error de conexion con el servidor'); }
        });
    }

    //================================== IMPRIMIR EN PDF ==================================================================//
    function imprimir<?php echo $opcGrillaContable; ?> (){
        window.open("facturacion/bd/imprimir_factura_venta.php?id=<?php echo $id_factura_venta; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    //================================== IMPRIMIR EN EXCEL =================================================================//
    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
       window.open("bd/exportarExcelGrillaContable.php?id=<?php echo $id_factura_venta; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    //============================  VALIDAR QUE NO HAYA NINGUN ARTICULO POR GUARDAR O POR ACTULIZAR =======================//
    function validarArticulos<?php echo $opcGrillaContable; ?>(){

        var cont = 0
        ,   contTotal = 0
        ,   contArticulo
        ,   nameArticulo
        ,   divsArticulos<?php echo $opcGrillaContable; ?> = document.querySelectorAll(".bodyDivArticulos<?php echo $opcGrillaContable; ?>");

        divsArticulos<?php echo $opcGrillaContable; ?>.forEach(element => {
            contTotal++;
            // console.log(element)
            nombreArticulo = element.id.split('_')[0];
            contArticulo   = element.id.split('_')[1];
            if (nombreArticulo=='bodyDivGruposFacturaVenta') { return; }
            // console.log(contArticulo);
            if(     document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).value > 0
                    &&  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                    ||  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
                    &&  document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contArticulo).style.display == 'inline')
                    { cont++; }

            //console.log(element)
            //console.log( document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).value);
            //console.log( document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src'));
            //console.log(cont);
            //console.log(contTotal);
        });

        // for(i in divsArticulos<?php echo $opcGrillaContable; ?>){

        //     if(typeof(divsArticulos<?php echo $opcGrillaContable; ?>[i].id)!='undefined'){

        //         contTotal++;

        //         nameArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[0]
        //         contArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[1]


        //         if(     document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).value > 0
        //             &&  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
        //             ||  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
        //             &&  document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contArticulo).style.display == 'inline')
        //             { cont++; }
        //     }

        // }
        if(contTotal==1 || contTotal==0){  return 0; }      //no se han almacenado articulos
        else if(cont > 0){ return 1; }      //si hay articulos pendientes por guardar o actualizar
        else { return 2; }                  //ok

    }

    //============================ CANCELAR UN DOCUMENTO =========================================================================//
    function cancelar<?php echo $opcGrillaContable; ?>(){
        var contArticulos = 0;

        if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }
        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');

        for(i in arrayIdsArticulos){ if(arrayIdsArticulos[i].innerHTML != '' ){ contArticulos++; } }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
                cargando_documentos('Cancelando Factura de Venta...','');
                Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                    url  : 'bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc      : 'cancelarDocumento',
                        id       : '<?php echo $id_factura_venta; ?>',
                        idBodega : '<?php echo $filtro_bodega; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    }
                });
            };
        }
    }

    function eliminaDocReferenciaFactura(idDocReferencia,docReferencia,id_factura_venta){
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'eliminaDocReferencia',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id_factura        : '<?php echo $id_factura_venta; ?>',
                id_doc_referencia : idDocReferencia,
                docReferencia     : docReferencia,
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            }
        });
    }

    function abrirVentanaUpdateFecha<?php echo $opcGrillaContable; ?>(inputFechaFactura){

        Win_Ventana_update_fecha_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 275,
            height      : 235,
            id          : 'Win_Ventana_update_fecha_<?php echo $opcGrillaContable; ?>',
            title       : 'Validacion de usuario',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  : { opc : 'ventanaUpdateFecha' }
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
                            xtype     : 'button',
                            text      : 'Guardar',
                            scale     : 'large',
                            iconCls   : 'guardar',
                            width     : 60,
                            height    : 56,
                            iconAlign : 'top',
                            handler   : function(){ cambiar_update_fecha_<?php echo $opcGrillaContable; ?>() }
                        },
                        {
                            xtype     : 'button',
                            text      : 'Regresar',
                            scale     : 'large',
                            iconCls   : 'regresar',
                            width     : 60,
                            height    : 56,
                            iconAlign : 'top',
                            handler   : function(){ Win_Ventana_update_fecha_<?php echo $opcGrillaContable; ?>.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    function cambiar_update_fecha_<?php echo $opcGrillaContable; ?>(){
        var fecha    = document.getElementById('fecha_updateFechafactura').value
        ,   usuario  = document.getElementById('usuario_updateFechafactura').value
        ,   password = document.getElementById('password_updateFechafactura').value;

        usuario  = usuario.replace(/[\#\<\>\'\"]/g, '');
        password = password.replace(/[\#\<\>\'\"]/g, '');

        if(usuario.length <= 5){ alert("Aviso,\nInserte un usuario valido"); return; }
        else if(password.length <= 1){ alert("Aviso,\nInserte su codigo password valido"); return; }

        Ext.get('loadValidaUpdatefecha').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'validateUpdateFecha',
                fecha             : fecha,
                usuario           : usuario,
                password          : password,
                idFacturaVenta    : '<?php echo $id_factura_venta; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            }
        });
    }

    //==================================// ANTICIPOS DE FACTURAS //==================================//
    function ventanaAnticipo_<?php echo $opcGrillaContable; ?>(){

        if(!document.getElementById("totalAcumulado<?php echo $opcGrillaContable; ?>")){ alert('Aviso,\nSolo se puede agregar el anticipo cuando el total de la factura es superior a cero.'); return; }

        var divTotalFactura = document.getElementById("totalAcumulado<?php echo $opcGrillaContable; ?>").innerHTML;
        divTotalFactura     = divTotalFactura.replace(/[^\d]/g, '');

        if(isNaN(divTotalFactura) || divTotalFactura == 0){ alert('Aviso,\nSolo se puede agregar el anticipo cuando el total de la factura es superior a cero.'); return; }

        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_cuenta_anticipo_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho - 100,
            height      : 400,
            id          : 'Win_Ventana_cuenta_anticipo_<?php echo $opcGrillaContable; ?>',
            title       : 'Configuracion de Anticipos',
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
                            id          : 'contenedor_cuenta_anticipo_<?php echo $opcGrillaContable; ?>',
                            border      : false,
                            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
                        }
                    ]
                }
            ],
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Anticipos',
                    style   : 'border-right:none;',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 150,
                            height      : 46,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : 'facturacion/bd/bd.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
                                    opc       : 'filtro_anticipo',
                                    idTercero : id_cliente_<?php echo $opcGrillaContable; ?>,
                                    opcGrilla : 'anticipo_<?php echo $opcGrillaContable; ?>',
                                    idFactura : '<?php echo $id_factura_venta ?>'
                                }
                            }
                        }
                    ]
                },'-',
                {
                    xtype       : 'button',
                    id          : 'Btn_cancelar_anticipo_<?php echo $opcGrillaContable; ?>',
                    width       : 60,
                    height      : 56,
                    text        : 'Eliminar Anticipos',
                    scale       : 'large',
                    iconCls     : 'cancel',
                    iconAlign   : 'top',
                    handler     : function(){ cancelar_anticipo_<?php echo $opcGrillaContable; ?>(); }
                },
                {
                    xtype     : 'button',
                    text      : 'Regresar',
                    scale     : 'large',
                    iconCls   : 'regresar',
                    width     : 60,
                    height    : 56,
                    iconAlign : 'top',
                    handler   : function(){ Win_Ventana_cuenta_anticipo_<?php echo $opcGrillaContable; ?>.close(id); }
                },'->',
                {
                    xtype       : "tbtext",
                    text        : '<div style="margin:10px;">'
                                        +'<div style="font-weight:bold; font-size:15px;">TOTAL ANTICIPO</div>'
                                        +'<div id="total_anticipo_<?php echo $opcGrillaContable; ?>" style="font-weight:bold; font-size:13px; margin-top:5px;"></div>'
                                    +'</div>',
                    scale       : "large",
                }
            ]
        }).show();
    }

    function ventanaCcos_<?php echo $opcGrillaContable; ?>(){
        Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 600,
            height      : 450,
            id          : 'Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>',
            title       : 'Seleccione el Centro de Costo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/bd/centro_costos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable     : '<?php echo $opcGrillaContable; ?>',
                    impressFunctionScript : 'renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id)'
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
                            text        : 'Cancelar C. de costo',
                            scale       : 'large',
                            iconCls     : 'eliminar',
                            iconAlign   : 'top',
                            handler     : function(){ cancelar_ccos_<?php echo $opcGrillaContable; ?>(); }
                        },
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close(); }
                        }
                    ]
                }
            ]
        }).show();
    }

    function cancelar_ccos_<?php echo $opcGrillaContable; ?>(){
        Ext.get('renderSelectCcos').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cancelar_ccos',
                id_documento      : '<?php echo $id_factura_venta; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
            }
        });
    }

    function renderSelectedCcos_<?php echo $opcGrillaContable; ?>(id){

        var nombre = ''
        ,   codigo = '';

        if(id > 0){
            nombre = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_nombre_'+id).innerHTML
            codigo = document.getElementById('div_centroCostos_<?php echo $opcGrillaContable; ?>_codigo_'+id).innerHTML;
        }

        Ext.get('renderSelectCcos').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                idCcos     : id,
                nombre     : nombre,
                codigo     : codigo,
                opc        : 'updateCcos',
                id_factura : '<?php echo $id_factura_venta; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });

        Win_Ventana_Ccos_<?php echo $opcGrillaContable; ?>.close();
    }

    function updateSucursalCliente<?php echo $opcGrillaContable;?>(input){
        if(isNaN(id_cliente_<?php echo $opcGrillaContable;?>) || id_cliente_<?php echo $opcGrillaContable;?> == 0){ alert("Aviso\nSeleccione el cliente antes de continuar!"); }

        var id_scl_cliente     = input.value
        ,   nombre_scl_cliente = input.options[input.selectedIndex].text;

        if(nombre_scl_cliente == 'Seleccione...') nombre_sucursal = '';

        Ext.get('renderSelectSucursalCliente<?php echo $opcGrillaContable; ?>').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc            : 'updateSucursalCliente',
                id_factura     : '<?php echo $id_factura_venta; ?>',
                id_cliente     : id_cliente_<?php echo $opcGrillaContable;?>,
                id_scl_cliente : id_scl_cliente,
                nombre_scl     : nombre_scl_cliente
            }
        });
    }

    //VENTANA PARA CONFIGURAR LAS RETENCIONES DEL DOCUMENTO
    function ventanaConfigurarRetenciones<?php echo $opcGrillaContable; ?>(){

        Win_Ventana_configRetenciones_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 800,
            height      : 450,
            id          : 'Win_Ventana_configRetenciones_<?php echo $opcGrillaContable; ?>',
            title       : 'Seleccione las retenciones',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/configuracion_retenciones.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable    : '<?php echo $opcGrillaContable; ?>',
                    modulo               : 'Venta',
                    id_documento         : '<?php echo $id_factura_venta; ?>',
                    tabla_retenciones    : 'ventas_facturas_retenciones',
                    id_tabla_retenciones : 'id_factura_venta',
                    ejecutaFuncion       : 'checkboxRetenciones<?php echo $opcGrillaContable; ?>',

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
                            handler     : function(){ Win_Ventana_configRetenciones_<?php echo $opcGrillaContable; ?>.close() }
                        }
                    ]
                }
            ]
        }).show();
    }

    // AGREGAR UN NUEVO GRUPO DE ITEMS PARA AGRUPAR
    function ventanaAgregarAgrupacionItems() {
        if (!document.getElementById('DivArticulosFacturaVenta')) { alert("Aviso\nNo se han cargado items!");return;}
        Win_Ventana_agrega_agrupacion = new Ext.Window({
            width       : 800,
            height      : 400,
            id          : 'Win_Ventana_agrega_agrupacion',
            title       : 'Agrupacion de Items',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            bodyStyle   : "background-color:#FFF;",
            autoLoad    :
            {
                url     : 'facturacion/bd/grupos_items.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_documento      : '<?php echo $id_factura_venta; ?>',
                    opcForm           : 'newGroup',
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
                            text        : 'Guardar',
                            scale       : 'large',
                            iconCls     : 'guardar',
                            iconAlign   : 'top',
                            hidden      : false,
                            handler     : function(){ BloqBtn(this); saveUpdateGroup(); }
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
                            handler     : function(){ BloqBtn(this); Win_Ventana_agrega_agrupacion.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    // MODIFICAR UN GRUPO DE ITEMS YA CREADO
    function ventanaActualizaAgrupacionItems(id_row) {
        if (!document.getElementById('DivArticulosFacturaVenta')) { return;}
        Win_Ventana_actualiza_agrupacion = new Ext.Window({
            width       : 800,
            height      : 650,
            id          : 'Win_Ventana_actualiza_agrupacion',
            title       : 'Agrupacion de Items',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            bodyStyle   : "background-color:#FFF;",
            autoLoad    :
            {
                url     : 'facturacion/bd/grupos_items.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_documento      : '<?php echo $id_factura_venta; ?>',
                    opcForm           : 'updateGroup',
                    id_row            : id_row
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
                            text        : 'Guardar',
                            scale       : 'large',
                            iconCls     : 'guardar',
                            iconAlign   : 'top',
                            hidden      : false,
                            handler     : function(){ BloqBtn(this); saveUpdateGroup(); }
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
                            handler     : function(){ BloqBtn(this); Win_Ventana_actualiza_agrupacion.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    function deleteGrupoFacturaVenta(id_row) {
        if (!confirm("Aviso\nRealmente desea eliminar el grupo?")) { return;}

         Ext.get('renderGrupoFacturaVenta_'+id_row).load({
             url     : 'facturacion/bd/bd.php',
             scripts : true,
             nocache : true,
             params  :
             {
                opc               : 'deleteGrupo',
                id_row            : id_row,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id_documento      : '<?php echo $id_factura_venta; ?>',
             }
         });
    }

    // MOSTRAR U OCULTAR LOS ITEMS DE UN GRUPO
    function showHiddenItems(id_grupo){
        var grupo = document.getElementById('content-group-'+id_grupo);
        if (grupo.style.display=="none") {grupo.style.display="block"; }
        else{grupo.style.display="none"; }
    }

</script>
