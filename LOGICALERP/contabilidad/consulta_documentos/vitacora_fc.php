<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $id_empresa   = $_SESSION['EMPRESA'];
    $id_sucursal  = $_SESSION['SUCURSAL'];
    $id_usuario   = $_SESSION['IDUSUARIO'];
    $bodyArticle  = '';
    $acumScript   = '';
    $estado       = '';
    $fecha_nota   = date('Y-m-d');
    $id_tipo_nota = 0;

    $arrayDocumento = array();

    // CONSULTAR LOS ASIENTOS CONTABLES
    $sql   = "SELECT * FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$id_documento AND tipo_documento='$documento'";
    $query = mysql_query($sql,$link);
    $bodyAsientos = '<table class="tabla_cuentas">
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
    while ($row=mysql_fetch_array($query)) {

        $bodyAsientos.='<tr>
                            <td>'.$row['codigo_cuenta'].'</td>
                            <td>'.$row['cuenta'].'</td>
                            <td>'.$row['fecha'].'</td>
                            <td>'.$row['tipo_documento_cruce'].'</td>
                            <td>'.$row['numero_documento_cruce'].'</td>
                            <td>'.$row['debe'].'</td>
                            <td>'.$row['haber'].'</td>
                            <td>'.$row['nit_tercero'].'</td>
                            <td>'.$row['tercero'].'</td>
                            <td>'.$row['sucursal'].'</td>
                            <td>'.$row['codigo_centro_costos'].' '.$row['centro_costos'].'</td>
                        </tr>';

        $where_id_fc_cruce .= ($where_id_fc_cruce=='')? 'id='.$row['id_documento_cruce'] : ' OR id='.$row['id_documento_cruce'];
    }
    $bodyAsientos.='</table>';


    //////////////////////////////////
    //      FACTURAS DE COMPRAS     //
    //////////////////////////////////

    if ($documento=='FC') {
        // DATOS PRINCIPALES DEL DOCUMENTO
        $titulo_documento = 'FACTURA DE COMPRA';
        $sql="SELECT * FROM compras_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_documento ORDER BY fecha_inicio DESC";
        $query=mysql_query($sql,$link);
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
                            <div class="label_fila_doc_info">'.((mysql_result($query,0,'prefijo_factura')=='')? mysql_result($query,0,'numero_factura') : mysql_result($query,0,'prefijo_factura').' '.mysql_result($query,0,'numero_factura') ).'</div>
                        </div>
                        <div class="contenedorInfo" >
                            <div class="label_fila_doc">Consecutivo</div>
                            <div class="label_fila_doc_info">'.mysql_result($query,0,'consecutivo').'</div>
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
                        </div>';

        $sql="SELECT * FROM compras_facturas_inventario WHERE activo=1 AND id_factura_compra=$id_documento GROUP BY id_consecutivo_referencia";
        $query=mysql_query($sql,$link);
        while ($row=mysql_fetch_array($query)) {
            $whereIdOC.=($whereIdOC=='')? 'id='.$row['id_consecutivo_referencia'] : ' OR id='.$row['id_consecutivo_referencia'] ;
        }
        if($whereIdOC!='') {

            $bodyDocsCruce.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin:0px 10px 0px 10px">
                                            ORDENES DE COMPRA
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
                                    </tr>
                                </thead>
                            ';

            $sql="SELECT * FROM compras_ordenes WHERE activo=1 AND id_empresa=$id_empresa AND($whereIdOC) ";
            $query=mysql_query($sql,$link);
            while ($row=mysql_fetch_array($query)) {
                $bodyDocsCruce.='<tr>
                                    <td>'.$row['consecutivo'].'</td>
                                    <td>'.$row['fecha_inicio'].'</td>
                                    <td>'.$row['fecha_vencimiento'].'</td>
                                    <td>'.$row['nit'].'</td>
                                    <td>'.$row['proveedor'].'</td>
                                    <td>'.$row['bodega'].'</td>
                                    <td>'.$row['sucursal'].'</td>
                                </tr>';
            }
            $bodyDocsCruce.='</table>';
        }



        if (mysql_result($query,0,'factura_por_cuentas')=='true') {
            $sql="SELECT * FROM compras_facturas_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_compra=$id_documento";
            $query=mysql_query($sql,$link);
            while ($row=mysql_fetch_array($query)) {
                $whereIdCE.=($whereIdCE=='')? 'id='.$row['id_documento_cruce'] : ' OR id='.$row['id_documento_cruce'] ;
            }
            $titulo_documento.=' POR CUENTAS';

            if ($whereIdCE!="") {

                $bodyDocsCruce.='<table class="tabla_cuentas">
                                <thead>
                                    <tr>
                                        <td colspan="7" style="border-bottom:1px solid #999">
                                            <img src="../../../temas/clasico/images/BotonesTabs/doc.png" style="margin:0px 10px 0px 10px">
                                            COMPROBANTES DE EGRESO
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
                    $bodyDocsCruce.='<tr>
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

                $bodyDocsCruce.='</table>';

            }
        }

        foreach ($variable as $key => $value) {
            # code...
        }

    }

    //////////////////////////////////
    //      COMPROBANTE EGRESO      //
    //////////////////////////////////

    else if ($documento=='CE') {
        $titulo_documento = 'COMPROBANTE DE EGRESO';
    }

    //////////////////////////////////
    //      REMISION DE VENTA       //
    //////////////////////////////////

    else if ($documento=='RV') {
        $titulo_documento = 'REMISION DE VENTA';
    }

    //////////////////////////////////
    //      FACTURAS DE VENTAS      //
    //////////////////////////////////

    else if ($documento=='FV') {
        $titulo_documento = 'FACTURA DE VENTA';
    }

    //////////////////////////////////
    //      RECIBOS DE CAJA         //
    //////////////////////////////////

    else if ($documento=='RC') {
        $titulo_documento = 'RECIBO DE CAJA';
    }

    //======================================================================//
    //         RECORRER LOS DOCUMENTOS DONDE SE CRUZA EL DOCUMENTO          //
    //======================================================================//
    $sql="";
    $query=mysql_query($sql,$link);


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
    }

    .contenedor_fila_info_doc{
        width     : 90%;
        float     : left;
        margin    : 0px 25px;
        max-width : 1047px;
    }

    .tabla_cuentas{
        border          :1px solid #EEE;
        font-size       : 11px;
        width           : 100%;
        border-collapse : collapse;
    }

    .tabla_cuentas thead{
        background-color : #EEE;
        font-weight      : bold;
        color            : #999;
        height           : 25px;
    }

    .tabla_cuentas tr{
        height : 25px;
        border : 1px solid #EEE;
    }

    .tabla_cuentas td{
        padding-left: 1px;
        padding-right: 1px;
    }

    .contenedorInfo{
        float: left;
        height: 25px;
        margin: 3px 3px;
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
        border : 1px solid #EEE;
    }

</style>
<div class="contenedorDocumento" >
    <div class="contenedor_titulo_fila_doc"><?php echo $titulo_documento; ?></div>
    <div class="contenedor_fila_info_doc"><?php echo $cabecera_doc; ?></div>
    <div class="contenedor_titulo_fila_doc">CUENTAS CONTABLES</div>
    <div class="contenedor_fila_info_doc"><?php echo $bodyAsientos; ?></div>
    <div class="contenedor_titulo_fila_doc">DOCUMENTOS CARGADOS</div>
    <div class="contenedor_fila_info_doc"><?php echo $bodyDocsCruce; ?></div>
    <div class="contenedor_titulo_fila_doc">DOCUMENTOS DONDE SE CRUZA</div>
    <div class="contenedor_fila_info_doc"><?php  ?></div>

</div>

<script>

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

</script>