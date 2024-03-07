<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    ob_start();

    $id_empresa   = $_SESSION['EMPRESA'];
    $id_sucursal  = $_SESSION['SUCURSAL'];
    $id_usuario   = $_SESSION['IDUSUARIO'];
    $bodyArticle  = '';
    $acumScript   = '';
    $estado       = '';
    $fecha_nota   = date('Y-m-d');
    $id_tipo_nota = 0;

    ////////////////////////////////////////////////////
    // CONSULTAR LOS ASIENTOS CONTABLES DEL DOCUMENTO //
    ////////////////////////////////////////////////////

    $sql="SELECT * FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$id_documento AND tipo_documento='$documento'";
    $query=mysql_query($sql,$link);
    $bodyAsientos='<table class="tabla_cuentas">
                        <thead>
                            <tr>
                                <td>CUENTA</td>
                                <td>DESCRIPCION</td>
                                <td>FECHA</td>
                                <td>DOC CRUCE</td>
                                <td>N. DOC CRUCE</td>
                                <td>DEBITO</td>
                                <td>CREDITO</td>
                                <td style="text-align:center;">NIT</td>
                                <td>TERCERO</td>
                                <td>SUCURSAL</td>
                                <td>CENTRO COSTOS</td>
                            </tr>
                        </thead>';
    $bodyTableAsientos='';
    while ($row=mysql_fetch_array($query)) {
        $centro_costos=($row['codigo_centro_costos']<>'')? $row['codigo_centro_costos'].' - '.$row['centro_costos'] : '' ;
        $bodyTableAsientos.='<tr>
                                <td>'.$row['codigo_cuenta'].'</td>
                                <td>'.$row['cuenta'].'</td>
                                <td>'.$row['fecha'].'</td>
                                <td>'.$row['tipo_documento_cruce'].'</td>
                                <td>'.$row['numero_documento_cruce'].'</td>
                                <td>'.number_format($row['debe'],$_SESSION['DECIMALESMONEDA']).'</td>
                                <td>'.number_format($row['haber'],$_SESSION['DECIMALESMONEDA']).'</td>
                                <td>'.$row['nit_tercero'].'</td>
                                <td>'.$row['tercero'].'</td>
                                <td>'.$row['sucursal'].'</td>
                                <td>'.$centro_costos.'</td>
                            </tr>';

    }

    if ($bodyTableAsientos=='') {
        $bodyAsientos='<span style="  font-family: sans-serif;font-size: 13px;font-weight: bold;font-style: italic;color: #999;padding: 10px 10px 10px 10px;">El documento no tiene asientos contables...</span>';
    }
    else{
        $bodyAsientos.=$bodyTableAsientos.'</table>';
    }

    ///////////////////////////////////////////////////////////////
    // CONSULTAR DONDE SE CRUZA EL DOCUMENTO, DESDE LOS ASIENTOS //
    ///////////////////////////////////////////////////////////////
    $sql="SELECT id_documento,consecutivo_documento,tipo_documento
            FROM asientos_colgaap
            WHERE
                activo=1 AND
                id_empresa=$id_empresa AND
                id_documento_cruce=$id_documento AND
                tipo_documento_cruce='$documento' AND
                tipo_documento<>'$documento' ";
    $query=mysql_query($sql,$link);
    while ($row=mysql_fetch_array($query)) {
        if ($row['tipo_documento'] =='FC') {$where_id_fc_cruce  .= ($where_id_fc_cruce=='')? ' id='.$row['id_documento'] : ' OR id='.$row['id_documento'] ;}
        if ($row['tipo_documento'] =='CE') {$where_id_ce_cruce  .= ($where_id_ce_cruce=='')? ' id='.$row['id_documento'] : ' OR id='.$row['id_documento'] ;}
        if ($row['tipo_documento'] =='NCG') {$where_id_ncg_cruce .= ($where_id_ncg_cruce=='')? ' id='.$row['id_documento'] : ' OR id='.$row['id_documento'] ;}
        if ($row['tipo_documento'] =='NDFC') {$where_id_ndc_cruce .= ($where_id_ndc_cruce=='')? ' id='.$row['id_documento'] : ' OR id='.$row['id_documento'] ;}
        if ($row['tipo_documento'] =='NDFV' || $row['tipo_documento']=='NDRV') {$where_id_ndv_cruce .= ($where_id_ndv_cruce=='')? ' id='.$row['id_documento'] : ' OR id='.$row['id_documento'] ;}
        if ($row['tipo_documento'] =='RC') {$where_id_rc_cruce  .= ($where_id_rc_cruce=='')? ' id='.$row['id_documento'] : ' OR id='.$row['id_documento'] ;}
    }

    //========================== CONSULTAR LAS TABLAS DONDE ESTA CRUZADO EL DOCUMENTO =============================//
    // FACTURAS DE COMPRA
    if ($where_id_fc_cruce<>'') {
        $sql="SELECT fecha_inicio,fecha_final,prefijo_factura,numero_factura,consecutivo,nit,proveedor,sucursal,bodega,total_factura_sin_abono,factura_por_cuentas
                FROM compras_facturas WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND ($where_id_fc_cruce)";
        $query=mysql_query($sql,$link);
        $bodyDocs='';
        while ($row=mysql_fetch_array($query)) {
            $bodyDocs.='<tr>
                            <td>'.$row['consecutivo'].'</td>
                            <td>'.$row['fecha_inicio'].'</td>
                            <td>'.$row['fecha_final'].'</td>
                            <td>'.$row['nit'].'</td>
                            <td>'.$row['proveedor'].'</td>
                            <td>'.$row['bodega'].'</td>
                            <td>'.$row['sucursal'].'</td>
                            <td>'.$row['total_factura_sin_abono'].'</td>
                            <td>'.(($row['factura_por_cuentas']=='true')? 'Si' : 'No' ).'</td>
                        </tr>';

        }
        if ($bodyDocs<>'') {
            $bodyDocsCruce.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>FACTURAS DE COMPRA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA INICIO</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td >NIT</td>
                                        <td>PROVEEDOR</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                        <td>SALDO</td>
                                        <td>FACTURA X CUENTAS</td>
                                    </tr>
                                </thead>
                                '.$bodyDocs.'
                            </table>
                            ';
        }
    }
    // COMPROBANTES DE EGRESO
    if($where_id_ce_cruce<>''){
        $sql="SELECT consecutivo,numero_cheque,nit_tercero,tercero,fecha_comprobante,sucursal
                FROM comprobante_egreso WHERE activo=1 AND estado=1 AND id_empresa=$id_empresa AND ($where_id_ce_cruce)";
        $query=mysql_query($sql,$link);
        $bodyDocs='';
        while ($row=mysql_fetch_array($query)) {
            $bodyDocs.='<tr>
                            <td>'.$row['consecutivo'].'</td>
                            <td>'.$row['numero_cheque'].'</td>
                            <td>'.$row['fecha_comprobante'].'</td>
                            <td>'.$row['nit_tercero'].'</td>
                            <td>'.$row['tercero'].'</td>
                            <td>'.$row['sucursal'].'</td>
                        </tr>';

        }
        if ($bodyDocs<>'') {
            $bodyDocsCruce.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>COMPROBANTES DE EGRESO</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>N. CHEQUE</td>
                                        <td>FECHA COMPROBANTE</td>
                                        <td >NIT</td>
                                        <td>PROVEEDOR</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>
                                '.$bodyDocs.'
                            </table>
                            ';
        }
    }
    // NOTA CONTABLE GENERAL
    if($where_id_ncg_cruce<>''){
        $sql="SELECT consecutivo,consecutivo_niif,sucursal,fecha_nota,tipo_nota,numero_identificacion_tercero,tercero
                FROM nota_contable_general WHERE activo=1 AND estado=1 AND id_empresa=$id_empresa AND ($where_id_ncg_cruce)";
        $query=mysql_query($sql,$link);
        $bodyDocs='';
        while ($row=mysql_fetch_array($query)) {
            $bodyDocs.='<tr>
                            <td>'.$row['consecutivo'].'</td>
                            <td>'.$row['consecutivo_niif'].'</td>
                            <td>'.$row['fecha_nota'].'</td>
                            <td>'.$row['tipo_nota'].'</td>
                            <td>'.$row['numero_identificacion_tercero'].'</td>
                            <td>'.$row['tercero'].'</td>
                            <td>'.$row['sucursal'].'</td>
                        </tr>';

        }
        if ($bodyDocs<>'') {
            $bodyDocsCruce.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>NOTA CONTABLE GENERAL</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>CONSECUTIVO NIIF</td>
                                        <td>FECHA NOTA</td>
                                        <td>TIPO NOTA</td>
                                        <td>N. IDENTIFICACION</td>
                                        <td>TERCERO</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>
                                '.$bodyDocs.'
                            </table>
                            ';
        }
    }
    // NOTA DEVOLUCION COMPRA
    if($where_id_ndc_cruce<>''){
        $sql="SELECT numero_documento_compra,consecutivo,sucursal,bodega,nit,proveedor,fecha_registro
                FROM devoluciones_compra WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND ($where_id_ndc_cruce)";
        $query=mysql_query($sql,$link);
        $bodyDocs='';
        while ($row=mysql_fetch_array($query)) {
            $bodyDocs.='<tr>
                            <td>'.$row['numero_documento_compra'].'</td>
                            <td>'.$row['sucursal'].'</td>
                            <td>'.$row['bodega'].'</td>
                            <td>'.$row['fecha_registro'].'</td>
                            <td>'.$row['consecutivo'].'</td>
                            <td>'.$row['nit'].'</td>
                            <td>'.$row['proveedor'].'</td>
                        </tr>';

        }
        if ($bodyDocs<>'') {
            $bodyDocsCruce.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>NOTA DEVOLUCION COMPRA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>N. FACT. COMPRA</td>
                                        <td>SUCURSAL FACT.</td>
                                        <td>BODEGA FACT.</td>
                                        <td >FECHA</td>
                                        <td>CONSECUTIVO</td>
                                        <td>NIT</td>
                                        <td>PROVEEDOR</td>
                                    </tr>
                                </thead>
                                '.$bodyDocs.'
                            </table>
                            ';
        }
    }
    // NOTA DEVOLUCION VENTA (FACTURA-REMISION)
    if($where_id_ndv_cruce<>''){
        $sql="SELECT documento_venta,numero_documento_venta,consecutivo,sucursal,bodega,fecha_registro,nit,cliente
                FROM  devoluciones_venta WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND ($where_id_ndv_cruce)";
        $query=mysql_query($sql,$link);
        $bodyDocs='';
        while ($row=mysql_fetch_array($query)) {
            $bodyDocs.='<tr>
                            <td>'.$row['documento_venta'].'</td>
                            <td>'.$row['numero_documento_venta'].'</td>
                            <td>'.$row['sucursal'].'</td>
                            <td>'.$row['bodega'].'</td>
                            <td>'.$row['fecha_registro'].'</td>
                            <td>'.$row['consecutivo'].'</td>
                            <td>'.$row['nit'].'</td>
                            <td>'.$row['cliente'].'</td>
                        </tr>';

        }
        if ($bodyDocs<>'') {
            $bodyDocsCruce.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>NOTA DEVOLUCION DE VENTA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>DOC. VENTA</td>
                                        <td>N. DOC.</td>
                                        <td>SUCURSAL</td>
                                        <td >BODEGA</td>
                                        <td>FECHA</td>
                                        <td>CONSECUTIVO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                    </tr>
                                </thead>
                                '.$bodyDocs.'
                            </table>
                            ';
        }
    }
    // RECIBO DE CAJA
    if($where_id_rc_cruce<>''){
        $sql="SELECT nit_tercero,tercero,fecha_recibo,consecutivo,sucursal,cuenta,descripcion_cuenta FROM recibo_caja WHERE activo=1 AND id_empresa=$id_empresa AND estado=1 AND ($where_id_rc_cruce)";
        $query=mysql_query($sql,$link);
        $bodyDocs='';
        while ($row=mysql_fetch_array($query)) {
            $bodyDocs.='<tr>
                            <td>'.$row['consecutivo'].'</td>
                            <td>'.$row['fecha_recibo'].'</td>
                            <td>'.$row['nit_tercero'].'</td>
                            <td>'.$row['tercero'].'</td>
                            <td>'.$row['cuenta'].' - '.$row['descripcion_cuenta'].'</td>
                            <td>'.$row['sucursal'].'</td>
                        </tr>';

        }
        if ($bodyDocs<>'') {
            $bodyDocsCruce.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>RECIBOS DE CAJA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>NIT</td>
                                        <td>TERCERO</td>
                                        <td>CUENTA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>
                                '.$bodyDocs.'
                            </table>
                            ';
        }
    }


    //////////////////////////////////
    //      FACTURAS DE COMPRAS     //
    //////////////////////////////////

    if ($documento=='FC') {
        // DATOS PRINCIPALES DEL DOCUMENTO
        $titulo_documento = 'FACTURA DE COMPRA';
        $sql="SELECT * FROM compras_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento ORDER BY fecha_inicio DESC";
        $query=mysql_query($sql,$link);
        $estado=mysql_result($query,0,'estado');

        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado=='0'){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }

        // RETENCIONES DE LAS FACTURA DE VENTA
        $sql_retenciones="SELECT tipo_retencion,retencion,valor,base,codigo_cuenta
                            FROM compras_facturas_retenciones WHERE activo=1 AND id_factura_compra=".mysql_result($query,0,'id');
        $query_retenciones=mysql_query($sql_retenciones,$link);
        while ($row=mysql_fetch_array($query_retenciones)) {
            $retencionesBody.='<div class="label_fila_doc_info"  style="border-left:none;border-top:none;">'.$row['retencion'].' ('.$row['valor'].' %) </div>';
        }

        $retenciones=($retencionesBody<>'')? '<div class="contenedorInfo" style="height: auto;">
                                                <div class="label_fila_doc" style="float:none;">Retenciones</div>
                                                '.$retencionesBody.'
                                                </div>'
                                                : '' ;

        $cabecera_doc=  '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Bodega</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'bodega').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Inicio</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_inicio').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Vencimiento</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_final').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Cuenta Pago</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'cuenta_pago').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Forma de Pago</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'forma_pago').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Numero Factura</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">
                                '.((mysql_result($query,0,'prefijo_factura')=='')? mysql_result($query,0,'numero_factura') : mysql_result($query,0,'prefijo_factura').' '.mysql_result($query,0,'numero_factura') ).'
                            </div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;" >'.mysql_result($query,0,'consecutivo').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Nit</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'nit').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Proveedor</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'proveedor').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Documento Usuario</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'documento_usuario').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Usuario</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'usuario').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Saldo Pendiente</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'total_factura_sin_abono').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>
                        '.$retenciones;

        // CONSULTAR COTIZACIONES RELACIONADAS EN LA FACTURA
        $sql="SELECT
                    CO.sucursal,
                    CO.bodega,
                    CO.fecha_inicio,
                    CO.fecha_vencimiento,
                    CO.nit,
                    CO.proveedor,
                    CO.consecutivo,
                    CO.documento_usuario,
                    CO.usuario,
                    CFI.codigo
                FROM
                    compras_ordenes AS CO
                INNER JOIN compras_ordenes_inventario AS COI ON CO.id = COI.id_orden_compra
                INNER JOIN compras_facturas_inventario AS CFI ON COI.id = CFI.id_tabla_referencia
                WHERE
                    CFI.activo = 1
                AND CO.id_empresa = $id_empresa
                AND CFI.nombre_consecutivo_referencia = 'Orden de Compra'
                AND CFI.id_factura_compra =".mysql_result($query,0,'id').' GROUP BY CO.id ';
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyOrdenesFC.='<tr>
                                    <td>'.$row['consecutivo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_finalizacion'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['cliente'].'</td>
                                    <td>'.$row['nombre_vendedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCargados.=($bodyOrdenesFC<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>ORDENES DE COMPRA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                        <td>VENDEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyOrdenesFC.'</table>' : '' ;


        if (mysql_result($query,0,'factura_por_cuentas')=='true') {
            $sql="SELECT * FROM compras_facturas_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$id_documento";
            $query=mysql_query($sql,$link);
            while ($row=mysql_fetch_array($query)) {
                $whereIdCE.=($whereIdCE=='')? 'id='.$row['id_documento_cruce'] : ' OR id='.$row['id_documento_cruce'] ;
            }
            $titulo_documento.=' POR CUENTAS';

            if ($whereIdCE!="") {

                $bodyDocsCargados.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>COMPROBANTES DE EGRESO</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>FECHA</td>
                                        <td>CONSECUTIVO</td>
                                        <td>N. CHEQUE</td>
                                        <td>CUENTA CRUCE</td>
                                        <td >NIT</td>
                                        <td>TERCERO</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>
                            ';

                $sql="SELECT * FROM comprobante_egreso WHERE activo=1 AND id_empresa=$id_empresa AND ($whereIdCE) ";
                $query=mysql_query($sql,$link);
                while ($row=mysql_fetch_array($query)) {
                    $bodyDocsCargados.='<tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">COMPROBANTES DE EGRESO</td>
                                    </tr>
                                    <tr>
                                        <td>'.$row['fecha_comprobante'].'</td>
                                        <td>'.$row['consecutivo'].'</td>
                                        <td>'.$row['numero_cheque'].'</td>
                                        <td>'.$row['cuenta'].'</td>
                                        <td>'.$row['nit_tercero'].'</td>
                                        <td>'.$row['tercero'].'</td>
                                        <td>'.$row['sucursal'].'</td>
                                    </tr>
                            ';
                }

                $bodyDocsCargados.='</table>';

            }
        }

    }

    //////////////////////////////////
    //      COMPROBANTE EGRESO      //
    //////////////////////////////////

    else if ($documento=='CE') {
        $titulo_documento = 'COMPROBANTE DE EGRESO';
        $sql="SELECT nit_tercero,tercero,fecha_comprobante,consecutivo,numero_cheque,cuenta,descripcion_cuenta,usuario,sucursal,observacion,estado
                FROM comprobante_egreso WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
        $query=mysql_query($sql,$link);
        $estado=mysql_result($query,0,'estado');
        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado==0){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }
        $cabecera_doc=  '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Comprobante</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_comprobante').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">'.mysql_result($query,0,'consecutivo').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">N. Cheque</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'numero_cheque').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Nit</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'nit_tercero').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Tercero</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'tercero').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Cuenta</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'cuenta').' - '.mysql_result($query,0,'descripcion_cuenta').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Usuario</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'usuario').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>';

    }

    //////////////////////////////////
    //      REMISION DE VENTA       //
    //////////////////////////////////

    else if ($documento=='RV') {
        $titulo_documento = 'REMISION DE VENTA';

        $sql="SELECT * FROM ventas_remisiones WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
        $query=mysql_query($sql,$link);

        $estado=mysql_result($query,0,'estado');
        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado=='0'){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }

        $cabecera_doc=  '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Bodega</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'bodega').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Inicio</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_inicio').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Vencimiento</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_finalizacion').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo </div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">
                                '.mysql_result($query,0,'consecutivo').'
                            </div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Nit</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'nit').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Cliente</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'cliente').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal Cliente</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal_cliente').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Centro de Costos</div>
                            <div class="label_fila_doc_info">'.((mysql_result($query,0,'codigo_centro_costo')<>'')? mysql_result($query,0,'codigo_centro_costo').' - '.mysql_result($query,0,'centro_costo')  : '' ).'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Documento Vendedor</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'documento_vendedor').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Nombre Vendedor</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'nombre_vendedor').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Pendientes Facturar</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'pendientes_facturar').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>
                        ';

        $id_remision_venta=mysql_result($query,0,'id');

        // CONSULTAR COTIZACIONES RELACIONADAS EN LA FACTURA
        $sql="SELECT
                    VC.sucursal,
                    VC.bodega,
                    VC.fecha_inicio,
                    VC.fecha_finalizacion,
                    VC.documento_vendedor,
                    VC.nombre_vendedor,
                    VC.nit,
                    VC.cliente,
                    VC.consecutivo,
                    VC.documento_usuario,
                    VC.usuario,
                    VRI.codigo
                FROM
                    ventas_cotizaciones AS VC
                INNER JOIN ventas_cotizaciones_inventario AS VCI ON VC.id=VCI.id_cotizacion_venta
                INNER JOIN ventas_remisiones_inventario AS VRI ON VCI.id = VRI.id_tabla_inventario_referencia
                WHERE
                    VRI.activo = 1
                AND VC.id_empresa = $id_empresa
                AND VRI.nombre_consecutivo_referencia = 'Cotizacion'
                AND VRI.id_remision_venta = $id_remision_venta
                GROUP BY VC.id";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyCotizacionesRV.='<tr>
                                    <td>'.$row['consecutivo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_finalizacion'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['cliente'].'</td>
                                    <td>'.$row['nombre_vendedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCargados.=($bodyCotizacionesRV<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>COTIZACIONES DE VENTA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                        <td>VENDEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyCotizacionesRV.'</table>' : '' ;

        // CONSULTAR LOS PEDIDOS RELACIONADAS EN LA FACTURA
        $sql="SELECT
                    VP.sucursal,
                    VP.bodega,
                    VP.fecha_inicio,
                    VP.fecha_finalizacion,
                    VP.documento_vendedor,
                    VP.nombre_vendedor,
                    VP.nit,
                    VP.cliente,
                    VP.consecutivo,
                    VP.documento_usuario,
                    VP.usuario,
                    VRI.codigo
                FROM
                    ventas_pedidos AS VP
                INNER JOIN ventas_pedidos_inventario AS VPI ON VP.id=VPI.id_pedido_venta
                INNER JOIN ventas_remisiones_inventario AS VRI ON VPI.id = VRI.id_tabla_inventario_referencia
                WHERE
                    VRI.activo = 1
                AND VP.id_empresa = $id_empresa
                AND VRI.nombre_consecutivo_referencia = 'Pedido'
                AND VRI.id_remision_venta = $id_remision_venta
                GROUP BY VP.id";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyPedidosRV.='<tr>
                                    <td>'.$row['consecutivo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_finalizacion'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['cliente'].'</td>
                                    <td>'.$row['nombre_vendedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCargados.=($bodyPedidosRV<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>PEDIDOS DE VENTA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                        <td>VENDEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyPedidosRV.'</table>' : '' ;


        //  CONSULTAR LAS FACTURAS QUE CARGARON LA REMISION
        $sql="SELECT
                        VF.numero_factura_completo,
                        VF.fecha_inicio,
                        VF.fecha_vencimiento,
                        VF.nit,
                        VF.cliente,
                        VF.bodega,
                        VF.sucursal,
                        VF.nombre_vendedor,
                        VF.usuario
                FROM
                    ventas_facturas AS VF
                INNER JOIN ventas_facturas_inventario AS VFI ON VF.id = VFI.id_factura_venta
                WHERE
                    VF.activo = 1
                AND VFI.id_tabla_inventario_referencia = (
                    SELECT
                        VRI.id
                    FROM
                        ventas_remisiones_inventario AS VRI
                    WHERE
                        VRI.activo = 1
                    AND VRI.id_remision_venta = $id_remision_venta
                )
                AND VFI.nombre_consecutivo_referencia = 'Remision'
                AND VF.id = VFI.id_factura_venta
                AND VF.estado=1
                AND VF.id_empresa=$id_empresa";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyFacturasRV.='<tr>
                                    <td>'.$row['numero_factura_completo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_vencimiento'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['cliente'].'</td>
                                    <td>'.$row['nombre_vendedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCruce.=($bodyFacturasRV<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>Facturas de Venta</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                        <td>VENDEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyFacturasRV.'</table>' : '' ;

    }

    //////////////////////////////////
    //      FACTURAS DE VENTAS      //
    //////////////////////////////////

    else if ($documento=='FV') {
        $titulo_documento = 'FACTURA DE VENTA';

        // CABECERA DE LA FACTURA
        $sql="SELECT * FROM ventas_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
        $query=mysql_query($sql,$link);
        $estado=mysql_result($query,0,'estado');

        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado=='0'){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }

        // RETENCIONES DE LAS FACTURA DE VENTA
        $sql_retenciones="SELECT tipo_retencion,retencion,valor,base,codigo_cuenta
                            FROM ventas_facturas_retenciones WHERE activo=1 AND id_factura_venta=".mysql_result($query,0,'id');
        $query_retenciones=mysql_query($sql_retenciones,$link);
        while ($row=mysql_fetch_array($query_retenciones)) {
            $retencionesBody.='<div class="label_fila_doc_info"  style="border-left:none;border-top:none;">'.$row['retencion'].' ('.$row['valor'].' %) </div>';
        }

        $retenciones=($retencionesBody<>'')? '<div class="contenedorInfo" style="height: auto;">
                                                <div class="label_fila_doc" style="float:none;">Retenciones</div>
                                                '.$retencionesBody.'
                                                </div>'
                                                : '' ;

        $cabecera_doc=  '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Bodega</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'bodega').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Inicio</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_inicio').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Vencimiento</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_vencimiento').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo Factura</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">
                                '.mysql_result($query,0,'numero_factura_completo').'
                            </div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Nit</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'nit').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Cliente</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'cliente').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal Cliente</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal_cliente').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Forma de Pago</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'forma_pago').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Cuenta Pago</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'cuenta_pago').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Centro de Costos</div>
                            <div class="label_fila_doc_info">'.((mysql_result($query,0,'codigo_centro_costo')<>'')? mysql_result($query,0,'codigo_centro_costo').' - '.mysql_result($query,0,'centro_costo')  : '' ).'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Documento Vendedor</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'documento_vendedor').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Nombre Vendedor</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'nombre_vendedor').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Saldo Pendiente</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'total_factura_sin_abono').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>
                        '.$retenciones;

        $id_factura_venta=mysql_result($query,0,'id');
        // CONSULTAR COTIZACIONES RELACIONADAS EN LA FACTURA
        $sql="SELECT
                    VC.sucursal,
                    VC.bodega,
                    VC.fecha_inicio,
                    VC.fecha_finalizacion,
                    VC.documento_vendedor,
                    VC.nombre_vendedor,
                    VC.nit,
                    VC.cliente,
                    VC.consecutivo,
                    VC.documento_usuario,
                    VC.usuario,
                    VFI.codigo
                FROM
                    ventas_cotizaciones AS VC
                INNER JOIN ventas_cotizaciones_inventario AS VCI ON VC.id=VCI.id_cotizacion_venta
                INNER JOIN ventas_facturas_inventario AS VFI ON VCI.id = VFI.id_tabla_inventario_referencia
                WHERE
                    VFI.activo = 1
                AND VC.id_empresa = $id_empresa
                AND VFI.nombre_consecutivo_referencia = 'Cotizacion'
                AND VFI.id_factura_venta = $id_factura_venta
                GROUP BY VC.id";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyCotizacionesFV.='<tr>
                                    <td>'.$row['consecutivo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_finalizacion'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['cliente'].'</td>
                                    <td>'.$row['nombre_vendedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCargados.=($bodyCotizacionesFV<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>COTIZACIONES DE VENTA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                        <td>VENDEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyCotizacionesFV.'</table>' : '' ;

        // CONSULTAR LOS PEDIDOS RELACIONADAS EN LA FACTURA
        $sql="SELECT
                    VP.sucursal,
                    VP.bodega,
                    VP.fecha_inicio,
                    VP.fecha_finalizacion,
                    VP.documento_vendedor,
                    VP.nombre_vendedor,
                    VP.nit,
                    VP.cliente,
                    VP.consecutivo,
                    VP.documento_usuario,
                    VP.usuario,
                    VFI.codigo
                FROM
                    ventas_pedidos AS VP
                INNER JOIN ventas_pedidos_inventario AS VPI ON VP.id=VPI.id_pedido_venta
                INNER JOIN ventas_facturas_inventario AS VFI ON VPI.id = VFI.id_tabla_inventario_referencia
                WHERE
                    VFI.activo = 1
                AND VP.id_empresa = $id_empresa
                AND VFI.nombre_consecutivo_referencia = 'Pedido'
                AND VFI.id_factura_venta = $id_factura_venta
                GROUP BY VP.id";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyPedidosFV.='<tr>
                                    <td>'.$row['consecutivo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_finalizacion'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['cliente'].'</td>
                                    <td>'.$row['nombre_vendedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCargados.=($bodyPedidosFV<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>PEDIDOS DE VENTA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                        <td>VENDEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyPedidosFV.'</table>' : '' ;

        // CONSULTAR LAS REMISIONES RELACIONADAS EN LA FACTURA
        $sql="SELECT
                    VR.sucursal,
                    VR.bodega,
                    VR.fecha_inicio,
                    VR.fecha_finalizacion,
                    VR.documento_vendedor,
                    VR.nombre_vendedor,
                    VR.nit,
                    VR.cliente,
                    VR.consecutivo,
                    VR.documento_usuario,
                    VR.usuario,
                    VFI.codigo
                FROM
                    ventas_remisiones AS VR
                INNER JOIN ventas_remisiones_inventario AS VRI ON VR.id=VRI.id_remision_venta
                INNER JOIN ventas_facturas_inventario AS VFI ON VRI.id = VFI.id_tabla_inventario_referencia
                WHERE
                    VFI.activo = 1
                AND VR.id_empresa = $id_empresa
                AND VFI.nombre_consecutivo_referencia = 'Remision'
                AND VFI.id_factura_venta = $id_factura_venta
                GROUP BY VR.id";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyRemisionesFV.='<tr>
                                    <td>'.$row['consecutivo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_finalizacion'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['cliente'].'</td>
                                    <td>'.$row['nombre_vendedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCargados.=($bodyRemisionesFV<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>REMISIONES DE VENTA</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                        <td>VENDEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyRemisionesFV.'</table>' : '' ;

    }

    //////////////////////////////////
    //      RECIBOS DE CAJA         //
    //////////////////////////////////

    else if ($documento=='RC') {
        $titulo_documento = 'RECIBO DE CAJA';
        $sql="SELECT nit_tercero,tercero,fecha_recibo,consecutivo,cuenta,descripcion_cuenta,usuario,sucursal,observacion,estado
                FROM recibo_caja WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
        $query=mysql_query($sql,$link);

        $estado=mysql_result($query,0,'estado');
        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado=='0'){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }

        $cabecera_doc= '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Recibo</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_recibo').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">'.mysql_result($query,0,'consecutivo').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Nit</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'nit_tercero').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Tercero</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'tercero').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Cuenta</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'cuenta').' - '.mysql_result($query,0,'descripcion_cuenta').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Usuario</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'usuario').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>
                        ';

    }


    /////////////////////////////////////
    //        PLANILLA DE NOMINA       //
    /////////////////////////////////////

    else if ($documento=='LN') {
        $titulo_documento = 'PLANILLA DE NOMINA';
        $sql="SELECT fecha_documento,fecha_inicio,fecha_final,consecutivo,tipo_liquidacion,usuario,sucursal,observacion,estado
                FROM nomina_planillas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
        $query=mysql_query($sql,$link);

        $estado=mysql_result($query,0,'estado');
        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado=='0'){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }

        $cabecera_doc= '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Documento</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_documento').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Inicio</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_inicio').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Final</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_final').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">'.mysql_result($query,0,'consecutivo').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Tipo de pago</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'tipo_liquidacion').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Usuario</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'usuario').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>
                        ';
    }

    //////////////////////////////////////////
    //        PLANILLA DE LIQUIDACION       //
    //////////////////////////////////////////

    else if ($documento=='LE') {
        $titulo_documento = 'LIQUIDACION EMPLEADO';
        $sql="SELECT fecha_documento,fecha_inicio,fecha_final,consecutivo,tipo_liquidacion,usuario,sucursal,observacion,estado
                FROM nomina_planillas_liquidacion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
        $query=mysql_query($sql,$link);

        $estado=mysql_result($query,0,'estado');
        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado=='0'){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }

        $cabecera_doc= '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Documento</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_documento').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Inicio</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_inicio').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Final</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_final').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">'.mysql_result($query,0,'consecutivo').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Tipo de pago</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'tipo_liquidacion').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Usuario</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'usuario').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>';
    }

    //////////////////////////////////////////
    //        PLANILLA DE AJUSTE            //
    //////////////////////////////////////////

    else if ($documento=='PA') {
        $titulo_documento = 'PLANILLA DE AJUSTE';
        $sql="SELECT fecha_documento,fecha_inicio,fecha_final,consecutivo,tipo_liquidacion,usuario,sucursal,observacion,estado
                FROM nomina_planillas_ajuste WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
        $query=mysql_query($sql,$link);

        $estado=mysql_result($query,0,'estado');
        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado=='0'){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }

        $cabecera_doc= '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Documento</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_documento').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Inicio</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_inicio').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Final</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_final').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">'.mysql_result($query,0,'consecutivo').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Tipo de pago</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'tipo_liquidacion').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Usuario</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'usuario').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>';
    }

    //////////////////////////////////////////
    //        NOTA CONTABLE GENERAL         //
    //////////////////////////////////////////

    else if ($documento=='NCG') {
        $titulo_documento = 'NOTA CONTABLE GENERAL';
        $sql="SELECT consecutivo,consecutivo_niif,sinc_nota,sucursal,fecha_nota,tipo_nota,numero_identificacion_tercero,tercero,usuario,observacion,estado
                FROM nota_contable_general WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento";
        $query=mysql_query($sql,$link);

        $estado=mysql_result($query,0,'estado');
        if ($estado==3) { $titulo_documento.=' - ( DOCUMENTO ELIMINADO ) '; }
        else if($estado=='0'){ $titulo_documento.=' - ( DOCUMENTO EDITADO ) '; }

        $sinc_nota = mysql_result($query,0,'sinc_nota');
        if ($sinc_nota=='colgaap_niif') {
            $contabilidad = 'Colgaap - Niif';
        }
        else if ($sinc_nota=='colgaap') {
            $contabilidad = 'Colgaap';
        }
        else if ($sinc_nota=='niif') {
            $contabilidad = 'Niif';
        }

        $cabecera_doc= '<div class="contenedorInfo" >
                            <div class="label_fila_doc">Sucursal</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'sucursal').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Fecha Documento</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'fecha_nota').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Tipo de Nota</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'tipo_nota').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Contabilidad</div>
                            <div class="label_fila_doc_info">'.$contabilidad.'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Numero Identificacion Tercero</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'numero_identificacion_tercero').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Tercero</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'tercero').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo Colgaap</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">'.mysql_result($query,0,'consecutivo').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo Niif</div>
                            <div class="label_fila_doc_info" style="font-weight:bold;font-size:20px;font-family: sans-serif;  line-height: 1.2;">'.mysql_result($query,0,'consecutivo_niif').'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Usuario</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'usuario').'</div>
                        </div>
                        <div class="contenedorInfo" style="height: auto;">
                            <div class="label_fila_doc" style="float:none;">Observaciones</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'observacion').'</div>
                        </div>
                        ';

        //  CONSULTAR LAS FACTURAS QUE CARGARON LA NOTA
        $sql="SELECT
                    VF.numero_factura_completo,
                    VF.fecha_inicio,
                    VF.fecha_vencimiento,
                    VF.nit,
                    VF.cliente,
                    VF.bodega,
                    VF.sucursal,
                    VF.nombre_vendedor,
                    VF.usuario
                FROM
                    ventas_facturas AS VF
                WHERE
                    VF.activo = 1
                AND VF.id = (
                    SELECT
                        NCGC.id_documento_cruce
                    FROM
                        nota_contable_general_cuentas AS NCGC
                    WHERE
                        NCGC.activo = 1
                    AND NCGC.tipo_documento_cruce = 'FV'
                    AND id_nota_general = $id_documento
                    AND id_empresa = $id_empresa
                )
                AND VF.estado=1
                AND VF.id_empresa=$id_empresa
                GROUP BY VF.id";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyFacturasFV.='<tr>
                                    <td>'.$row['numero_factura_completo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_vencimiento'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['cliente'].'</td>
                                    <td>'.$row['nombre_vendedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCargados.=($bodyFacturasFV<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>Facturas de Venta</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>FECHA VENCIMIENTO</td>
                                        <td>NIT</td>
                                        <td>CLIENTE</td>
                                        <td>VENDEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyFacturasFV.'</table>' : '' ;

        //  CONSULTAR LAS FACTURAS QUE CARGARON LA NOTA
        $sql="SELECT
                    CF.prefijo_factura,
                    CF.numero_factura,
                    CF.consecutivo,
                    CF.nit,
                    CF.proveedor,
                    CF.sucursal,
                    CF.bodega,
                    CF.usuario,
                    CF.fecha_inicio
                FROM
                    compras_facturas AS CF
                WHERE
                    CF.activo = 1
                AND CF.id = (
                    SELECT
                        NCGC.id_documento_cruce
                    FROM
                        nota_contable_general_cuentas AS NCGC
                    WHERE
                        NCGC.activo = 1
                    AND NCGC.tipo_documento_cruce = 'FC'
                    AND id_nota_general = $id_documento
                    AND id_empresa = $id_empresa
                )
                AND CF.estado=1
                AND CF.id_empresa=$id_empresa
                GROUP BY CF.id";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $bodyFacturasFC.='<tr>
                                    <td>'.$row['prefijo_factura'].' '.$row['numero_factura'].'</td>
                                    <td>'.$row['consecutivo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['proveedor'].'</td>
                                    <td>'.$row['usuario'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
        }

        $bodyDocsCargados.=($bodyFacturasFC<>'')? '<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="9" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin: 4px 5px 0px 1px;">
                                            <span>Facturas de Compra</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>NUMERO FACTURA</td>
                                        <td>CONSECUTIVO</td>
                                        <td>FECHA</td>
                                        <td>NIT</td>
                                        <td>PROVEEDOR</td>
                                        <td>USUARIO</td>
                                        <td>BODEGA</td>
                                        <td>SUCURSAL</td>
                                    </tr>
                                </thead>'.$bodyFacturasFC.'</table>' : '' ;


    }



    if ($bodyDocsCargados=='') {
        $bodyDocsCargados='<span style="  font-family: sans-serif;font-size: 13px;font-weight: bold;font-style: italic;color: #999;padding: 10px 10px 10px 10px;">El documento no cargo otros documentos...</span>';
    }
    if ($bodyDocsCruce=='') {
        $bodyDocsCruce='<span style="  font-family: sans-serif;font-size: 13px;font-weight: bold;font-style: italic;color: #999;padding: 10px 10px 10px 10px;">El documento no se encuentra cruzado por otros documentos...</span>';
    }

    $display_div=($IMPRIME_PDF=='true')? 'style="display:block;"' : '' ;
    $display_btn=($IMPRIME_PDF=='true')? 'style="display:none;"' : '' ;
    $style=($IMPRIME_PDF=='true')? 'width:150px;"' : '' ;

?>

<style>
    .contenedorDocumento{
        width    : 100%;
        height   : calc(100% - 85px);
        overflow : auto;
    }

    .contenedor_titulo_fila_doc{
        width            : 90%;
        height           : 32px;
        font-weight      : bold;
        background-color : #999;
        color            : #FFF;
        font-size        : 12px;
        margin           : 5px 25px;
        float            : left;
        line-height      : 2.5;
        text-indent      : 15px;
        cursor           : hand;
    }

    .contenedor_titulo_fila_doc img{
        float  : right;
        cursor : hand;
        margin : 5px 10px 0px 0px;
    }

    .contenedor_fila_info_doc{
        width     : 90%;
        float     : left;
        margin    : 0px 25px;
        max-width : 1047px;
        display   : none;
    }

    .tabla_cuentas{
        border          : 1px solid #EEE;
        font-size       : 11px;
        width           : 100%;
        border-collapse : collapse;
        margin-bottom   : 10px;
        max-height      : 50px;
    }

    .tabla_cuentas thead{
        background-color : #EEE;
        font-weight      : bold;
        color            : #999;
        height           : 25px;
        border           : 1px solid #999;
    }

    .tabla_cuentas thead span{
        line-height: 2.5;
    }

    .tabla_cuentas tbody{
        overflow-y: scroll;
    }

    .tabla_cuentas tr{
        height : 25px;
        border : 1px solid #EEE;
    }

    .tabla_cuentas td{
        padding-left  : 1px;
        padding-right : 1px;
        border        : 1px solid #999;
        text-indent   : 5;
    }

    .contenedorInfo{
        float  : left;
        height : 25px;
        margin : 3px 3px;
        border : 1px solid #999;
        <?php echo $style ?>
    }

    .label_fila_doc{
        font-weight      : bold;
        font-size        : 11px;
        float            : left;
        text-indent      : 5px;
        background-color : #EEE;
        color            : #999;
        height           : 25px;
        line-height      : 2;
        padding-right    : 5px;
    }

    .label_fila_doc_info{
        font-size     : 11px;
        float         : left;
        height        : 23px;
        text-indent   : 5px;
        line-height   : 2;
        padding-right : 5px;
        border        : 1px solid #EEE;
    }

    .delete_doc_label{
        /*float: right;*/
        /*padding-right: 10px;*/
    }

</style>

<div class="contenedorDocumento" >
    <div class="contenedor_titulo_fila_doc"><?php echo $titulo_documento; ?></div>
    <div class="contenedor_fila_info_doc" style="display:block;"><?php echo $cabecera_doc; ?></div>
    <div class="contenedor_titulo_fila_doc" onclick="ver_ocultar_info('cuentas_contables','ocultar','div_cuentas_contables','img_cuentas_contables')" id="div_cuentas_contables">CUENTAS CONTABLES<img <?php echo $display_btn; ?>  id="img_cuentas_contables" src="img/ocultar.png" ></div>
    <div class="contenedor_fila_info_doc" style="display:block;max-height:300px;overflow:auto;" id="cuentas_contables"><?php echo $bodyAsientos; ?></div>
    <div class="contenedor_titulo_fila_doc" onclick="ver_ocultar_info('documentos_cargados','ver','div_documentos_cargados','img_documentos_cargados')" id="div_documentos_cargados" <?php echo $display_div; ?> >DOCUMENTOS CARGADOS<img <?php echo $display_btn; ?> id="img_documentos_cargados" src="img/ver.png" ></div>
    <div class="contenedor_fila_info_doc" id="documentos_cargados" <?php echo $display_div; ?> ><?php echo $bodyDocsCargados; ?></div>
    <div class="contenedor_titulo_fila_doc" onclick="ver_ocultar_info('documentos_cruzados','ver','div_documentos_cruzados','img_documentos_cruzados')" id="div_documentos_cruzados" <?php echo $display_div; ?> >DOCUMENTOS DONDE SE CRUZA<img <?php echo $display_btn; ?> id="img_documentos_cruzados" src="img/ver.png" ></div>
    <div class="contenedor_fila_info_doc" id="documentos_cruzados" <?php echo $display_div; ?> ><?php echo $bodyDocsCruce; ?></div>

</div>

<script>

    function ver_ocultar_info(div_evento,accion,id_div,id_img) {
        if (accion=='ver') {
            document.getElementById(div_evento).style.display='block';
            document.getElementById(id_div).setAttribute('onclick','ver_ocultar_info("'+div_evento+'","ocultar","'+id_div+'","'+id_img+'")');
            document.getElementById(id_img).setAttribute('src','img/ocultar.png');
        }
        if (accion=='ocultar') {
            document.getElementById(div_evento).style.display='none';
            document.getElementById(id_div).setAttribute('onclick','ver_ocultar_info("'+div_evento+'","ver","'+id_div+'","'+id_img+'")');
            document.getElementById(id_img).setAttribute('src','img/ver.png');
        }

    }

    function ventana_log_documento() {

        Win_Ventana_log_documento = new Ext.Window({
            width       : 550,
            height      : 400,
            id          : 'Win_Ventana_log_documento',
            title       : 'Eventos del Documento',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'consulta_documentos/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc            : 'log_documento',
                    id_documento   : '<?php echo $id_documento ?>',
                    tipo_documento : '<?php echo $documento ?>',
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
                            handler     : function(){ BloqBtn(this); Win_Ventana_log_documento.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    function imprimir_bitacora () {
        window.open('consulta_documentos/grillaDocumento.php?IMPRIME_PDF=true&documento=<?php echo $documento ?>&consecutivo=<?php echo $consecutivo ?>&id_documento=<?php echo $id_documento ?>');
    }

</script>
<?php

    $texto = ob_get_contents(); ob_end_clean();

    if(isset($TAM)){ $HOJA = $TAM; }
    else{ $HOJA = 'LETTER-L'; }

    if(!isset($ORIENTACION)){ $ORIENTACION = 'P'; }
    if(!isset($PDF_GUARDA)){ $PDF_GUARDA = 'false'; }
    if(!isset($IMPRIME_PDF)){ $IMPRIME_PDF = 'false'; }

    if(isset($MARGENES)){list($MS, $MD, $MI, $ML) = split( ',', $MARGENES ); }
    else{ $MS=10; $MD=10; $MI=10; $ML=10; }

    if(!isset($TAMANO_ENCA)){ $TAMANO_ENCA = 12 ; }
    if($IMPRIME_PDF == 'true'){
        include("../../../misc/MPDF54/mpdf.php");
        $mpdf = new mPDF(
                    'utf-8',        // mode - default ''
                    $HOJA,          // format - A4, for example, default ''
                    12,             // font size - default 0
                    '',             // default font family
                    $MI,            // margin_left
                    $MD,            // margin right
                    $MS,            // margin top
                    $ML,            // margin bottom
                    10,             // margin header
                    10,             // margin footer
                    $ORIENTACION    // L - landscape, P - portrait
                );
        // $mpdf-> debug = true;
        $mpdf->useSubstitutions = true;
        // $mpdf->simpleTables = true;
        $mpdf->packTableData= true;
        $mpdf->SetAutoPageBreak(TRUE, 15);
        //$mpdf->SetTitle ( $documento );
        $mpdf->SetAuthor ( $_SESSION['NOMBREFUNCIONARIO']." // ".$_SESSION['NOMBREEMPRESA'] );
        $mpdf->SetDisplayMode ( 'fullpage' );
        $mpdf->SetHeader("");
        $mpdf->SetFooter('Pagina {PAGENO}/{nb}');

        $mpdf->WriteHTML(utf8_encode($texto));

        if($PDF_GUARDA=='true'){ $mpdf->Output($documento.".pdf",'D'); }
        else{ $mpdf->Output($documento.".pdf",'I'); }

        exit;
    }
    else{ echo $texto; }
?>