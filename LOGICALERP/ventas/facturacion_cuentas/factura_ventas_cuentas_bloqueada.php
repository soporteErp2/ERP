<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fechaActual = date('Y-m-d');
    $divImagen   = '';
?>

<?php

    if(!isset($id_factura_venta)){ exit; }
    include("functions_body_article.php");

    //el campo numero de factura solo esta en la tabla de ventas_facturas, si que verificamos que opcGrillaContable sea ventas facturas para pasar la cadena sql con ese campo, sino se pasa si ese campo
    $sql   = "SELECT COUNT(id) AS contFactura,
                    id_cliente,
                    prefijo,
                    numero_factura,
                    cod_cliente,
                    nit,
                    cliente,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                    date_format(fecha_vencimiento,'%Y-%m-%d') AS fechaFin,
                    observacion,
                    estado,
                    nombre_vendedor,
                    prefijo,
                    cuenta_pago,
                    configuracion_cuenta_pago,
                    centro_costo,
                    codigo_centro_costo,
                    total_factura
                FROM ventas_facturas
                WHERE id='$id_factura_venta'
                    AND activo = 1";
    $query = mysql_query($sql,$link);

    $contFactura       = mysql_result($query,0,'contFactura');
    $nit               = mysql_result($query,0,'nit');
    $cliente           = mysql_result($query,0,'cliente');
    $id_cliente        = mysql_result($query,0,'id_cliente');
    $fecha             = mysql_result($query,0,'fecha');
    $fechaFin          = mysql_result($query,0,'fechaFin');
    $codigo_cliente    = mysql_result($query,0,'cod_cliente');
    $estado            = mysql_result($query,0,'estado');
    $nombre_vendedor   = mysql_result($query,0,'nombre_vendedor');
    $numero_factura    = mysql_result($query,0,'numero_factura');
    $prefijo           = mysql_result($query,0,'prefijo');
    $codigoCcos        = mysql_result($query,0,'codigo_centro_costo');
    $nombreCcos        = mysql_result($query,0,'centro_costo');
    $cuentaPago        = mysql_result($query,0,'cuenta_pago');
    $configuracionPago = mysql_result($query,0,'configuracion_cuenta_pago');
    $total_factura     = mysql_result($query,0,'total_factura');

    if($contFactura == 0){ exit; }

    $labelCcos = $codigoCcos.' '.$nombreCcos;
    $divCuentaPago = ($cuentaPago > 0)? '<div class="renglonTop">
                                            <div class="labelTop">Cuenta de cobro</div>
                                            <div class="campoTop" title="'.$cuentaPago.'"><input type="text" id="cuentas_pago" value="'.$cuentaPago.' - '.$configuracionPago.'" readonly="readonly" /></div>
                                        </div>' : '';

    if($prefijo != ''){ $numero_factura = $prefijo.' '.$numero_factura; }

    // CONSULTAR LOS ASIENTOS CONTABLES DEL DOCUMENTO
    $sql   = "SELECT * FROM ventas_facturas_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_venta=$id_factura_venta";
    $query = mysql_query($sql,$link);
    $cont  = 1;
    while ($row=mysql_fetch_array($query)) {
        $row['debito']  = $row['debito'] * 1;
        $row['credito'] = $row['credito'] * 1;

        $acumDebito  += $row['debito'];
        $acumCredito += $row['credito'];
        $bodyCuentas .= '<div class="bodyDivArticulos'.$opcGrillaContable.'" id="bodyDivArticulos'.$opcGrillaContable.'" style="overflow:hidden;">

                            <div class="campo" style="width:40px !important; overflow:hidden;">
                                <div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
                                <div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
                            </div>

                            <div class="campo">
                                <input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$row['cuenta_puc'].'" readonly/>
                            </div>

                            <div class="campo ancho1"><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$row['descripcion_puc'].'" readonly/></div>
                            <div class="campo ancho2"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$row['tercero'].'" readonly/></div>

                            <div class="campo"><input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'" value="'.$row['debito'].'" readonly/></div>
                            <div class="campo"><input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" value="'.$row['credito'].'" readonly/></div>
                            <div class="campo"><input type="text" id="codigo_concepto'.$opcGrillaContable.'_'.$cont.'" value="'.$row['codigo_concepto'].'" readonly/></div>
                            <div class="campo" style="width:95px;"><input type="text" id="concepto'.$opcGrillaContable.'_'.$cont.'" value="'.$row['concepto'].'" style="text-align:left;" readonly/></div>

                        </div>';
        $cont++;
    }


?>

<div class="contenedorDocumentoVentaBloqueado" id="contenedorDocumentoVentaBloqueado">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <?php echo $divImagen ; ?>
                <div class="renglonTop">
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input readonly type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>"></div>
                </div>
                <div class="renglonTop">
                     <div class="labelTop">Vencimiento</div>
                    <div class="campoTop"  id="fechaLimitePago<?php echo $opcGrillaContable; ?>"> <input readonly type="text" id="<!-- fechaFin<?php echo $opcGrillaContable; ?> -->" value="<?php echo $fechaFin; ?>"> </div>
                </div>
                <div class="renglonTop" id="divFormaPago<?php echo $opcGrillaContable; ?>" style="display:none;">
                    <input type="hidden" id="fechaFinal<?php echo $opcGrillaContable; ?>"/>
                    <div class="labelTop">Forma de Pago</div>
                    <div class="campoTop" id="selectFormaPago<?php echo $opcGrillaContable; ?>">
                         <input type="text" readonly id="plazo<?php echo $opcGrillaContable; ?>"/>
                    </div>
                </div>

                <div class="renglonTop" id="divFormaPago<?php echo $opcGrillaContable; ?>">
                    <div class="labelTop">Numero de Factura</div>
                    <div class="campoTop">
                        <input type="text" readonly id="numeroFactura<?php echo $opcGrillaContable; ?>" value="<?php echo $numero_factura; ?>"/>
                    </div>
                </div>

                <?php echo $divCuentaPago; ?>
                <div class="renglonTop">
                    <div class="labelTop">Codigo</div>
                    <div class="campoTop"><input readonly type="text" value="<?php echo $codigo_cliente; ?>"></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input readonly type="text"  value="<?php echo $nit; ?>"/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Cliente</div>
                    <div class="campoTop" style="width:277px;"><input readonly type="text" style="width:100%" Readonly value="<?php echo $cliente ?>"/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Vendedor</div>
                    <div class="campoTop" style="width:277px;"><input readonly type="text" style="width:100%" Readonly value="<?php echo $nombre_vendedor; ?>"/></div>
                </div>
                <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Centro de Costo</div>
                    <div class="campoTop"><input type="text" id="cCos_<?php echo $opcGrillaContable; ?>" value="<?php echo $labelCcos; ?>" Readonly/></div>
                </div>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>">
            <div class="contenedorGrilla">
                    <div class="titleGrilla"><b>ARTICULOS FACTURA DE VENTA</b></div>
                    <div class="contenedorHeadArticulos">
                        <div class="headArticulos" id="headFacturaVenta<?php echo $opcGrillaContable; ?>">
                            <div class="label" style="width:40px !important;"></div>
                            <div class="label">Cuenta</div>
                            <div class="label ancho1">Descripcion</div>
                            <div class="label ancho2">Tercero</div>
                            <div class="label">Debito</div>
                            <div class="label">Credito</div>
                            <div class="label">Cod</div>
                            <div class="label" style="width:95px;">Concepto</div>
                        </div>
                    </div>
                    <div class="DivArticulos" id="DivArticulos<?php echo $opcGrillaContable; ?>" onscroll="resizeHeadMyGrilla(this,'head<?php echo $opcGrillaContable; ?>')">
                        <?php echo $bodyCuentas; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contenedor_totales" id="contenedor_totales_<?php echo $opcGrillaContable; ?>">

        <div class="contenedorDetalleTotales" style="margin-top: 20px;">
            <div class="renglon">
                <div class="label" style="width:170px !important; padding-left:5px;">Debito</div>
                <div class="labelSimbolo">$</div>
                <div class="labelTotal" id="debitoAcumulado<?php echo $opcGrillaContable; ?>"><?php echo number_format($acumDebito,$_SESSION['DECIMALESMONEDA']) ?></div>
            </div>
            <div class="renglon">
                <div class="label" style="width:170px !important; padding-left:5px;">Credito</div>
                <div class="labelSimbolo">$</div>
                <div class="labelTotal" id="creditoAcumulado<?php echo $opcGrillaContable; ?>"><?php echo number_format($acumCredito,$_SESSION['DECIMALESMONEDA']) ?></div>
            </div>

            <div class="renglon renglonTotal">
                <div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL FACTURA</div>
                <div class="labelSimbolo">$</div>
                <div class="labelTotal"  id="totalAcumulado<?php echo $opcGrillaContable; ?>"><?php echo number_format($total_factura,$_SESSION['DECIMALESMONEDA']) ?></div>
            </div>
        </div>
    </div>

</div>

<style type="text/css">
    #bodyArticulos<?php echo $opcGrillaContable; ?> .ancho1{ width: calc((100% - 40px - 12% - 17% - 95px) / 3) !important; }
    #bodyArticulos<?php echo $opcGrillaContable; ?> .ancho2{ width: calc((100% - 40px - 12% - 17% - 95px) / 2 ) !important; }
</style>

<script>
    var contenedor  = document.getElementById('contenedorDocumentoVentaBloqueado');
    var styleParent = contenedor.parentNode.getAttribute('style');
    contenedor.parentNode.setAttribute('style',styleParent+' overflow:auto;');

    //==================================// IMPRIMIR  //==================================//
    //***********************************************************************************//
    function imprimir<?php echo $opcGrillaContable; ?> (){
        var url = 'facturacion_cuentas/bd/imprimir_factura_cuentas.php';
        window.open(url+"?id=<?php echo $id_factura_venta; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>");
    }

    function cancelar<?php echo $opcGrillaContable; ?>(id) {
        if (!confirm('Realmente desea eliminar la factura?')) { return; }

        Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
            url     : 'facturacion_cuentas/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'eliminarFactura',
                id_factura        : '<?php echo $id_factura_venta ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>'
            }
        });
    }

    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").enable();
    Ext.getCmp("btnExportar_<?php echo $opcGrillaContable; ?>").enable();
</script>
