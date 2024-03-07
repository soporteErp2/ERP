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

    $arrayTypeRetenciones = '';
?>
<script>




</script>
<?php
    include("functions_body_article.php");

    //el campo numero de factura solo esta en la tabla de ventas_facturas, si que verificamos que opcGrillaContable sea ventas facturas para pasar la cadena sql con ese campo, sino se pasa si ese campo
    $sql   = "SELECT id_cliente,
                    prefijo,
                    numero_factura,
                    cod_cliente,
                    nit,cliente,
                    date_format(fecha_inicio,'%Y-%m-%d') AS fecha,
                    date_format(fecha_vencimiento,
                    '%Y-%m-%d') AS fechaFin,
                    observacion,
                    estado,
                    nombre_vendedor,
                    prefijo,
                    cuenta_pago,
                    configuracion_cuenta_pago,
                    centro_costo,
                    codigo_centro_costo
                FROM ventas_facturas  WHERE id='$id_factura_venta' AND activo = 1";



    $query = mysql_query($sql,$link);

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

    $labelCcos = $codigoCcos.' '.$nombreCcos;

    $divCuentaPago = ($cuentaPago > 0)? '<div class="renglonTop">
                                            <div class="labelTop">Cuenta de cobro</div>
                                            <div class="campoTop" title="'.$cuentaPago.'"><input type="text" id="cuentas_pago" value="'.$configuracionPago.'" readonly="readonly" /></div>
                                        </div>' : '';

    if($prefijo != ''){ $numero_factura = $prefijo.' '.$numero_factura; }

    // CONSULTAR LOS ASIENTOS CONTABLES DEL DOCUMENTO
    $sql="SELECT * FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$id_factura_venta AND tipo_documento='FV' ";
    $query=mysql_query($sql,$link);
    $cont=1;
    while ($row=mysql_fetch_array($query)) {
        $acumDebito += $row['debe'];
        $acumCredito += $row['haber'];
        $bodyCuentas.='<div class="bodyDivArticulosFacturaVenta" id="bodyDivArticulosFacturaVenta_1">

                            <div class="campo" style="width:40px !important; overflow:hidden;">
                                <div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
                                <div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
                            </div>

                            <div class="campo" style="width:95px;">
                                <input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$row['codigo_cuenta'].'" readonly/>
                            </div>

                            <div class="campo " style="width:250px;"><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$row['cuenta'].'" readonly/></div>
                            <div class="campo " style="width:250px;"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$row['tercero'].'" readonly/></div>

                            <div class="campo" style="width:95px;"> <input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'" value="'.$row['debe'].'" readonly/></div>
                            <div class="campo" style="width:95px;"> <input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" value="'.$row['haber'].'" readonly/></div>

                        </div>';
        $cont++;
    }


?>

<div class="contenedorDocumentoVentaBloqueado">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_factura_ws"></div>
            <div class="contTopFila">
                <?php echo $divImagen ; ?>
                <div class="renglonTop">
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input readonly type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" ></div>
                </div>
                <div class="renglonTop" >
                     <div class="labelTop">Vencimiento</div>
                    <div class="campoTop"  id="fechaLimitePago<?php echo $opcGrillaContable; ?>"> <input readonly type="text" id="<!-- fechaFin<?php echo $opcGrillaContable; ?> -->" value="<?php echo $fechaFin; ?>" > </div>
                </div>
                <div class="renglonTop" id="divFormaPago<?php echo $opcGrillaContable; ?>" style="display:none;">
                    <input type="hidden" id="fechaFinal<?php echo $opcGrillaContable; ?>"  />
                    <div class="labelTop"> Forma de Pago</div>
                    <div class="campoTop" id="selectFormaPago<?php echo $opcGrillaContable; ?>">
                         <input type="text" readonly id="plazo<?php echo $opcGrillaContable; ?>"   />
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
                    <div class="titleGrilla"><b>ARTICULOS FACTURA DE COMPRA</b></div>
                    <div class="contenedorHeadArticulos">
                        <div class="headArticulos" id="headFacturaVenta">
                            <div class="label" style="width:40px !important;"></div>
                            <div class="label" style="width:95px;">Cuenta</div>
                            <div class="label" style="width:250px;">Descripcion</div>
                            <div class="label" style="width:250px;">Tercero</div>
                            <div class="label" style="width:95px;">Debito</div>
                            <div class="label" style="width:95px;">Credito</div>
                        </div>
                    </div>
                    <div class="DivArticulos" id="DivArticulosFacturaVenta" onscroll="resizeHeadMyGrilla(this,'headFacturaVenta')">
                        <?php echo $bodyCuentas; ?>
                        <!-- <div class="bodyDivArticulosFacturaVenta" id="bodyDivArticulosFacturaVenta_1">

                            <div class="campo" style="width:40px !important; overflow:hidden;">
                                <div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
                                <div style="float:left; width:18px; overflow:hidden;" id="renderArticulo'.$opcGrillaContable.'_'.$cont.'"></div>
                            </div>

                            <div class="campo" style="width:95px;">
                                <input type="text" id="cuenta'.$opcGrillaContable.'_'.$cont.'" value="'.$cuenta.'" readonly/>
                            </div>

                            <div class="campo " style="width:250px;"><input type="text" id="descripcion'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" readonly value="'.$descripcion.'" readonly/></div>
                            <div class="campo " style="width:250px;"><input type="text" id="tercero'.$opcGrillaContable.'_'.$cont.'" style="text-align:left;" value="'.$tercero.'" readonly/></div>

                            <div class="campo" style="width:95px;"> <input type="text" id="debito'.$opcGrillaContable.'_'.$cont.'" value="'.$debe.'" readonly/></div>
                            <div class="campo" style="width:95px;"> <input type="text" id="credito'.$opcGrillaContable.'_'.$cont.'" value="'.$haber.'" readonly/></div>

                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="contenedor_totales" id="contenedor_totales_FacturaVenta">

        <div class="contenedorDetalleTotales" style="margin-top: 20px;">
            <div class="renglon">
                <div class="label" style="width:170px !important; padding-left:5px;">Debito</div>
                <div class="labelSimbolo">$</div>
                <div class="labelTotal" id="debitoAcumuladoFacturaVenta"><?php echo number_format($acumDebito,$_SESSION['DECIMALESMONEDA']) ?></div>
            </div>
            <div class="renglon">
                <div class="label" style="width:170px !important; padding-left:5px;">Credito</div>
                <div class="labelSimbolo" >$</div>
                <div class="labelTotal" id="creditoAcumuladoFacturaVenta"><?php echo number_format($acumCredito,$_SESSION['DECIMALESMONEDA']) ?></div>
            </div>

            <div class="renglon renglonTotal" >
                <div class="label" style="width:170px !important; padding-left:5px; text-align:center;">TOTAL (DEBITO - CREDITO)</div>
                <div class="labelSimbolo" >$</div>
                <div class="labelTotal"  id="totalAcumuladoFacturaVenta"><?php echo number_format(($acumDebito - $acumCredito),$_SESSION['DECIMALESMONEDA']) ?></div>
            </div>
        </div>
    </div>

</div>

<script>


</script>
