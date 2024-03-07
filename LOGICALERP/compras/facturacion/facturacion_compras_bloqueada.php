<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../funciones_globales/funciones_php/randomico.php");

    #update compras

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fechaActual = date('Y-m-d');
    $divImagen   = '';
    $divAnticipo = '';

    $arrayTypeRetenciones = '';
?>
<script>
    var arrayTypeRetenciones = new Array();                  // ARRAY QUE CONTIENE LAS RETENCIONES QUE NO SON DESCONTADAS DEL TOTAL
    var objectRetenciones_FacturaCompra = new Array();

    var subtotalFacturaCompra      = 0.00
    ,   acumuladodescuentoArticulo = 0.00
    ,   ivaFacturaCompra           = 0.00
    ,   ivaFacturaCompra1          = 0.00
    ,   totalFacturaCompra         = 0.00
    ,   numeroOrdenCompra          = 0
    ,   contabilidad_manual        = ''
    ,   subtotal_manual            = 0
    ,   iva_manual                 = 0
    ,   total_manual               = 0;

    Ext.getCmp("Btn_imprimir_FacturaCompra").enable();
    Ext.getCmp("Btn_guardar_FacturaCompra").disable();
    Ext.getCmp("Btn_editar_FacturaCompra").disable();
    Ext.getCmp("Btn_cancelar_FacturaCompra").disable();
    Ext.getCmp("Btn_restaurar_FacturaCompra").disable();
    Ext.getCmp("BtnGroup_Estado1_FacturaCompra").show();
    Ext.getCmp("BtnGroup_Guardar_FacturaCompra").hide();

</script>
<?php

    include("bd/functions_body_article.php");

    $sql   ="SELECT id_proveedor,
                prefijo_factura,
                numero_factura,
                nit,
                cod_proveedor,
                proveedor,
                date_format(fecha_inicio,'%Y-%m-%d') AS fecha_inicio,
                date_format(fecha_final,'%Y-%m-%d') AS fecha_final,
                DATE_ADD(fecha_inicio, INTERVAL 36 MONTH) AS fecha_bloqueo,
                observacion,
                estado,
                forma_pago,
                observacion,
                configuracion_cuenta_pago,
                cuenta_pago,
                usuario_recibe_en_almacen,
                contabilidad_manual,
                tipo_documento,
                id_metodo_pago,
                response_DS
            FROM compras_facturas
            WHERE id='$id_factura_compra' AND activo = 1";
    $query = mysql_query($sql,$link);

    $nit                       = mysql_result($query,0,'nit');
    $proveedor                 = mysql_result($query,0,'proveedor');
    $id_proveedor              = mysql_result($query,0,'id_proveedor');
    $cod_proveedor             = mysql_result($query,0,'cod_proveedor');
    $fecha_inicio              = mysql_result($query,0,'fecha_inicio');
    $fecha_final               = mysql_result($query,0,'fecha_final');
    $fechaBloqueo              = mysql_result($query,0,'fecha_bloqueo');
    $forma_pago                = mysql_result($query,0,'forma_pago');
    $estado                    = mysql_result($query,0,'estado');
    $prefijo_factura           = mysql_result($query,0,'prefijo_factura');
    $numero_factura            = mysql_result($query,0,'numero_factura');
    $configuracionCuentaPago   = mysql_result($query,0,'configuracion_cuenta_pago');
    $cuentaPago                = mysql_result($query,0,'cuenta_pago');
    $usuario_recibe_en_almacen = mysql_result($query,0,'usuario_recibe_en_almacen');
    $contabilidad_manual       = mysql_result($query,0,'contabilidad_manual');
    $tipo_documento            = mysql_result($query,0,'tipo_documento');
    $id_metodo_pago            = mysql_result($query,0,'id_metodo_pago');
    $response_DS               = mysql_result($query,0,'response_DS');

    if ($contabilidad_manual=='true') {
        $sql = "SELECT subtotal_manual,iva_manual,total_manual
                FROM compras_facturas_contabilidad_manual
                WHERE activo=1
                    AND id_empresa=$id_empresa
                    AND id_factura_compra=$id_factura_compra";
        $query = mysql_query($sql,$link);

        $subtotal_manual = mysql_result($query,0,'subtotal_manual');
        $iva_manual      = mysql_result($query,0,'iva_manual');
        $total_manual    = mysql_result($query,0,'total_manual');
    }

    $user_permiso_editar    = (user_permisos(39,'false') == 'true')? 'Ext.getCmp("Btn_editar_FacturaCompra").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos(40,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_FacturaCompra").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos(41,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_FacturaCompra").enable();Ext.getCmp("Btn_imprimir_FacturaCompra").disable();' : '';     //restaurar

    // SI ES UN DOCUMENTO SOPORTE ENTONCES MOSTRAR EL BOTON
    if($tipo_documento == "05"){
        $acumScript .= "Ext.getCmp('Btn_enviar_factura_electronica_FacturaCompra').show();";

        if($response_DS == "Ejemplar recibido exitosamente pasara a verificacion"){
            $acumScript .= "Ext.getCmp('Btn_enviar_factura_electronica_FacturaCompra').disable();";
        } 
        else{
            $acumScript .= "Ext.getCmp('Btn_enviar_factura_electronica_FacturaCompra').enable();";
        }
    }
    else{
        $acumScript .= "Ext.getCmp('Btn_enviar_factura_electronica_FacturaCompra').hide();";
    }

    if (strtotime($fechaBloqueo) <= strtotime($fechaActual)){ $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Factura de Compra Bloqueada">'; }
    else{
        if ($estado==1) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar.'Ext.getCmp("Btn_imprimir_FacturaCompra").enable();'; }   //documento generado
        else if($estado == 3){ $acumScript .= $user_permiso_restaurar.'Ext.getCmp("Btn_imprimir_FacturaCompra").enable();'; }      //documento cancelado
    }

    $arrayReplaceString       = array("\n", "\r","<br>");
    $observacionFacturaCompra = str_replace($arrayReplaceString, " ", mysql_result($query,0,'observacion'));

    $acumScript .=  'document.getElementById("nitProveedorFactura").value         = "'.$nit.'";
                    document.getElementById("codProveedorFactura").value          = "'.$cod_proveedor.'";
                    document.getElementById("nombreProveedorFactura").value       = "'.$proveedor.'";
                    document.getElementById("tipoDocumento").value                = "'.(($tipo_documento=="05")?"documento soporte":"Factura Compra").'";
                    document.getElementById("fechaFactura").value                 = "'.$fecha_inicio .'";
                    document.getElementById("fechaFinalFactura").value            = "'.$fecha_final .'";
                    document.getElementById("observacionFacturaCompra").value     = "'.$observacionFacturaCompra.'";
                    document.getElementById("numeroFactura").value                = "'.$numero_factura.'";
                    document.getElementById("prefijoFactura").value               = "'.$prefijo_factura.'";
                    document.getElementById("nombreEmpleadoRecibioAlmacen").value = "'.$usuario_recibe_en_almacen.'";

                    id_proveedor_factura_compra = "'.$id_proveedor.'";
                    codigoProveedorFactura      = "'.$cod_proveedor.'";
                    nitProveedorFactura         = "'.$nit.'";
                    nombreProveedorFactura      = "'.$proveedor.'";';


    $bodyArticle = cargaArticulosFacturaCompraSave($id_factura_compra,$observacionFacturaCompra,$estado,$link);

    //============================================= ORDENES DE COMPRA ===================================================//
    $acumOrdenesCompra  = '';
    $margin_left        = '';
    $sqlOrdenesCompra   = "SELECT DISTINCT consecutivo_referencia, LEFT(nombre_consecutivo_referencia,1) AS string_cruce
                            FROM compras_facturas_inventario
                            WHERE consecutivo_referencia>0 AND id_factura_compra='$id_factura_compra' AND activo=1 AND id_empresa='$id_empresa'
                            ORDER BY consecutivo_referencia ASC";
    $queryOrdenesCompra = mysql_query($sqlOrdenesCompra,$link);

    while($rowOrdenesCompra = mysql_fetch_array($queryOrdenesCompra)){
        $acumOrdenesCompra .='<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;">
                                    <div style="float:left; width:134px; height:100%; border-left: 1px solid #d4d4d4; border-right: 1px solid #d4d4d4;">
                                        <input id= "OCFacturaCompra_'.$rowOrdenesCompra['consecutivo_referencia'].'" type="text" style="height:100%; width:100%;" value="'.$rowOrdenesCompra['string_cruce'].' '.$rowOrdenesCompra['consecutivo_referencia'].'" readonly/>
                                    </div>
                                </div>';
        $margin_left = 'margin-left:5px';
    }

    //============================================= CHECKBOX RETENCIONES ===================================================//
    $checkboxRetenciones = '';
    $sqlRetenciones      = "SELECT id,retencion,valor,tipo_retencion,base
							FROM compras_facturas_retenciones
							WHERE activo=1 AND id_factura_compra='$id_factura_compra'";

    $queryRetenciones    = mysql_query($sqlRetenciones,$link);
    $checkboxRetenciones.= '<div class="contenedorCheckbox">';

    while ($row=mysql_fetch_array($queryRetenciones)) {
        $row['valor'] = $row['valor']*1;
        $arrayTypeRetenciones .= 'arrayTypeRetenciones['.$row['id'].'] = "'.$row['tipo_retencion'].'";';

        $checkboxRetenciones .= '<div class="campoCheck" title="'.$row['retencion'].'">
                                    <div id="cargarCheckbox_'.$row['id'].'" class="renderCheck"></div>
                                    <input type="hidden" class="capturarCheckboxAcumuladoFacturaCompra" id="checkboxRetencionesFactura_'.$row['id'].'" name="checkboxFacturaCompra"  value="'.$row['valor'].'" />
                                    <label class="capturaLabelAcumuladoFacturaCompra" for="checkbox_'.$row['retencion'].'">
                                        <div class="labelNombreRetencion">'.$row['retencion'].'</div>
                                        <div class="labelValorRetencion">('.$row['valor'].'%)</div>
                                    </label>
                                </div>';

        $objectRetenciones[$row['id']]  = 'objectRetenciones_FacturaCompra['.$row['id'].'] = {'
                                                                                                .'tipo_retencion : "'.$row['tipo_retencion'].'",'
                                                                                                .'base           : "'.$row['base'].'",'
                                                                                                .'valor          : "'.$row['valor'].'",'
                                                                                            .'}';
    }
    $checkboxRetenciones .= '</div>';
    echo '<script>'.$arrayTypeRetenciones.'</script>';

    if ($contabilidad_manual=='true') {
        if ($subtotal_manual>0 && $total_manual>0) {
            $acumScript.='contabilidad_manual="true";
                            subtotal_manual='.$subtotal_manual.';
                            iva_manual='.$iva_manual.';
                            total_manual='.$total_manual.';
                            //recalculamos los valores de la factura
                            calcularValoresFactura(0,0,0,"","",0);';
        }
    }


    //IMPRIMIR OBJECT RETENCIONES//
    $plainRetenciones = implode(';', $objectRetenciones).';';
    echo '<script>'.$plainRetenciones.' //console.log(objectRetenciones_FacturaVenta);</script>';


    //=============================// DOCUMENTOS CRUCE //=============================//
    //*********************************************************************************//
    $divCruce = '';
    $sqlCruce = "SELECT
                        IF (
                            id_documento_cruce = $id_factura_compra
                            AND tipo_documento_cruce = 'FC',
                            tipo_documento,
                            tipo_documento_cruce
                        ) AS documento_cruce,

                        IF (
                            id_documento_cruce = $id_factura_compra
                            AND tipo_documento_cruce = 'FC',
                            consecutivo_documento,
                            numero_documento_cruce
                        ) AS numero_cruce,
                        tipo_documento,consecutivo_documento
                        FROM asientos_colgaap
                        WHERE
                            activo = 1
                            AND id_empresa = $id_empresa
                            AND (id_documento_cruce = $id_factura_compra
                                AND tipo_documento_cruce = 'FC'
                                AND (id_documento <> $id_factura_compra
                                    OR tipo_documento <> 'FC')
                                OR id_documento = $id_factura_compra
                                AND tipo_documento = 'FC'
                                AND (id_documento_cruce <> $id_factura_compra
                                    OR tipo_documento_cruce <> 'FC'))

                GROUP BY id_documento,tipo_documento";
    $queryCruce = mysql_query($sqlCruce,$link);

    $cruce     = '';
    $contCruce = 0;
    while ($row = mysql_fetch_assoc($queryCruce)) {
        $contCruce++;
        $cruce .= '<div title="'.$row['documento_cruce'].' #'.$row['numero_cruce'].'" style="float:left; border-right:1px solid #d4d4d4; padding: 3px; height:100%;"><span style="color:blue; font-weight:bold;">'.$row['documento_cruce'].'</span> '.$row['numero_cruce'].'</div>';
    }

    if($contCruce > 0){
        $divCruce = '<div class="renglonTop">
                        <div class="labelTop">Cruzado con:</div>
                        <div class="campoTop">'.$cruce.'</div>
                    </div>';
    }

    //=============================// ANTICIPOS //=============================//
    //*************************************************************************//
    $sqlAnticipos   = "SELECT SUM(valor) AS valorAnticipos FROM anticipos WHERE id_documento='$id_factura_compra' AND activo=1 AND tipo_documento='FC' AND id_empresa='$id_empresa'";
    $queryAnticipos = mysql_query($sqlAnticipos,$link);
    $totalAnticipo  = mysql_result($queryAnticipos, 0, 'valorAnticipos');

    if($totalAnticipo > 0){
        $totalAnticipo *= 1;

        $divAnticipo = '<div class="renglonTop">
                            <div class="labelTop">Anticipos</div>
                            <div class="campoTop"><input type="text" value="$ '.ROUND($totalAnticipo,$_SESSION['DECIMALESMONEDA']).'" Readonly/></div>
                        </div>';
    }

    $sql   = "SELECT id,nombre,codigo_metodo_pago_dian FROM configuracion_metodos_pago WHERE activo=1 AND id=$id_metodo_pago";
    $query = $mysql->query($sql);
    $metodo_pago = mysql_result($query,0,'nombre');

?>

<div class="contenedorFacturaCompra">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_factura_compra"></div>
            <div class="contTopFila">
            	<?php echo  $divImagen; ?>
                <div class="renglonTop">
                    <div class="labelTop">tipo documento</div>
                    <div class="campoTop"><input type="text" id="tipoDocumento" readonly ></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Fecha de Inicio</div>
                    <div class="campoTop"><input type="text" id="fechaFactura" readonly ></div>
                </div>
                 <div class="renglonTop" >
                    <div class="labelTop">Fecha de Vencimiento</div>
                    <div class="campoTop"><input type="text" id="fechaFinalFactura" readonly/></div>
                </div>
                <!-- <div class="renglonTop" >
                    <div class="labelTop">Forma de pago</div>
                    <div class="campoTop"><input type="text" value="<?php echo $forma_pago; ?>" readonly/></div>
                </div> -->
                <div class="renglonTop" >
                    <div class="labelTop">Medio de pago</div>
                    <div class="campoTop"><input type="text" value="<?php echo $metodo_pago; ?>" readonly/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Cuenta de pago</div>
                    <div class="campoTop" title="<?php echo $cuentaPago ?>"><input type="text" value="<?php echo $configuracionCuentaPago; ?>" readonly/></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Documentos Relacionados</div>
                    <div class="campoTop"><?php echo $acumOrdenesCompra; ?></div>
                </div>
                <div class="renglonTop" style="width:135px;">
                    <div class="labelTop">Factura #</div>
                    <div class="campoTop">
                        <input type="text" id="prefijoFactura" style="width:30% !important; float:left;" readonly >
                        <div style="width:10% !important; float:left; background-image: url(img/MyGrillaFondo.png); height:100%; text-align:center;">-</div>
                        <input type="text" id="numeroFactura" style="width:60% !important; float:left;" readonly >
                    </div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Codigo Proveedor</div>
                    <div class="campoTop"><input type="text" id="codProveedorFactura" readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" id="nitProveedorFactura" readonly /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Proveedor</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreProveedorFactura" Readonly /></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Empleado que recibio en el almacen</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreEmpleadoRecibioAlmacen" Readonly /></div>
                </div>

                <div class="renglonTop" id="checksRetencionesFactura">
                    <div class="labelTop">Retenciones</div>
                    <?php echo $checkboxRetenciones; ?>
                </div>
                <?php echo $divCruce; ?>
                <?php echo $divAnticipo; ?>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulosFactura">
        <div class="renderFilasArticulo" id="renderizaNewArticuloFactura"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>
    <?php echo $acumScript; ?>
    

    var contArticulosFactura   = 1
    ,   codigoProveedorFactura = 0
    ,   nitProveedorFactura    = 0
    ,   nombreProveedorFactura = '';

    //======================== FUNCION PARA BUSCAR LA ORDEN DE LA COMPRA POR SU NUMERO ================================//

    function buscarOrdenCompraFactura(event,Input){
        tecla   = (Input) ? event.keyCode : event.which;
        numero  = Input.value;

        if(tecla == 13 || tecla == 9){
            ajaxBuscarFacturaCompraOrden(Input.value);
            return;
        }
        patron = /[^\d]/;
        if(patron.test(numero)){ Input.value = numero.replace(patron,''); }
        return true;
    }

    function ajaxBuscarFacturaCompraOrden(idOrdenCompra){

        opcCargar = '';

        if (document.getElementById('imgCargarDesdeFacturaCompra').getAttribute('src')=='img/pedido.png'){ opcCargar='ordenCompra'; }               //CARGA UNA ORDEN DE COMPRA
        else if (document.getElementById('imgCargarDesdeFacturaCompra').getAttribute('src')=='img/cotizacion.png'){ opcCargar='entradaAlmacen'; }  //CARGA UNA ENTRADA A ALMACEN

        Ext.get("renderCargaCotizacionPedidoFacturaCompra").load({
            url     : "facturacion/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc           : 'buscarOrdenCompra',
                idOrdenCompra : idOrdenCompra,
                opcCargar     : opcCargar,
                confirm       : '',
                filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value
            }
        });
    }

    //======================== VENTANA BUSCAR ORDEN DE COMPRA ==================================//

    function ventanaBuscarOrdenCompraFactura(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_orden_compra_factura = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_orden_compra_factura',
            title       : 'Seleccionar Orden de compra',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/grilla_buscar_orden_compra.php',
                scripts : true,
                nocache : true,
                params  : { filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_Ventana_buscar_orden_compra_factura.close(id) }
                },'-'
            ]
        }).show();
    }


   //========================= FUNCION PARA CALCULAR LOS TOTALES DE LA FACTURA DE COMPRA ==============================//
   /*
        ->subtotal      = (suma de (cantidad * costo) de cada uno de los articulos)
        ->iva           = ((suma de ivas de todos los articulos)*(subtotal-descuento))/100
        ->retefuente    = ((suma de las retenciones de la factura)*(subtotal-descuento))/100
        ->total         = (subtotal-descuento) + iva + retefuente
   */

    function calcularValoresFactura(cantidad,descuento,costo,accion,tipoDesc,iva,cont){
        if(!document.getElementById('contenedor_totales_facturas_compras')){ return; }

        var subtotal         = 0
        ,   valor_iva        = 0
        ,   descuentoTotal   = 0
        ,   descuentoMostrar = 0
        ,   subtotal_anterior = subtotalFacturaCompra
        ,   iva_anterior      = ivaFacturaCompra
        ,   total_anterior    = totalFacturaCompra;

        subtotal = (cantidad * costo);

        if (tipoDesc=='porcentaje') { subtotal = subtotal-(subtotal*descuento/100); } // DESCUENTO POR ARTICULO ITEM
        else if(tipoDesc=='pesos'){ subtotal = subtotal-descuento; }

        if (iva >0) {
            valor_iva = (parseFloat(arrayIvaFacturaCompra[iva].valor)*parseFloat(subtotal))/100; //IVA NETO
        }
        else{
            valor_iva = 0; //IVA NETO
            iva       = 0;
        }

        if (accion=='agregar') {
            // valor_iva             = (parseFloat(iva)*parseFloat(subtotal))/100;
            subtotalFacturaCompra = (parseFloat(subtotalFacturaCompra) + parseFloat(subtotal));   // ACUMULADOR SUBTOTAL
            ivaFacturaCompra      = parseFloat(ivaFacturaCompra)+parseFloat(valor_iva);         // ACUMULADOR IVA

            //SI EL OBJETO SALDO EN EL ARRAY DEL IVA NO EXISTE, CREAR EL CAMPO SALDO CON EL PRIMER VALOR
            if (typeof(arrayIvaFacturaCompra[iva].saldo)=='undefined') {
                arrayIvaFacturaCompra[iva].saldo=valor_iva;
            }
            //SI YA EXISTE EL CAMPO SALDO EN EL OBJETO, ENTONCES ACUMULAR EL VALOR
            else{
                arrayIvaFacturaCompra[iva].saldo=arrayIvaFacturaCompra[iva].saldo+valor_iva;
            }

            document.getElementById("costoTotalArticuloFactura_"+cont).value = subtotal;
        }
        else if (accion=='eliminar') {
            // valor_iva             = (parseFloat(iva)*parseFloat(subtotal))/100;
            subtotalFacturaCompra = parseFloat(subtotalFacturaCompra) - parseFloat(subtotal);   // ACUMULADOR SUBTOTAL
            ivaFacturaCompra      = parseFloat(ivaFacturaCompra) - parseFloat(valor_iva);         // ACUMULADOR IVA

            //SI EL OBJETO SALDO EN EL ARRAY DEL IVA EXISTE, RESTAR EL VALOR DEL IVA
            if (typeof(arrayIvaFacturaCompra[iva].saldo)!='undefined') {
                arrayIvaFacturaCompra[iva].saldo-=valor_iva;
            }
        }

        //RECORRER EL ARRAY DE LOS IVA Y ARMAR ELEMENTOS PARA EL DOM
        var labelIva   = ''
        ,   simboloIva = ''
        ,   valoresIva = '';

        for(var id_iva in arrayIvaFacturaCompra){
            // console.log(arrayIvaFacturaCompra[id_iva].nombre+' - '+arrayIvaFacturaCompra[id_iva].valor+' - '+arrayIvaFacturaCompra[id_iva].saldo);
            if (typeof(arrayIvaFacturaCompra[id_iva].saldo)!='undefined') {
                if (arrayIvaFacturaCompra[id_iva].saldo>0) {
                    // console.log(arrayIvaFacturaCompra[id_iva].saldo);
                    labelIva+='<div style=\"margin-bottom:5px; overflow:hidden; width:100%; padding-left:3px; font-weight:bold; overflow:hidden;margin-bottom:5px;\"><div class=\"labelNombreRetencion\">'+arrayIvaFacturaCompra[id_iva].nombre+'</div><div class=\"labelValorRetencion\">('+(arrayIvaFacturaCompra[id_iva].valor*1)+'%)</div></div>';
                    simboloIva+='<div style=\"margin-bottom:5px\">$</div>';
                    valoresIva+='<div style=\"margin-bottom:5px\" title=\"'+formato_numero(arrayIvaFacturaCompra[id_iva].saldo, "<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'\" >'+formato_numero(arrayIvaFacturaCompra[id_iva].saldo, "<?php echo $_SESSION['DECIMALESMONEDA'] ?>", '.', ',')+'</div>';
                }
            }
        }

        // CALCULO DE RETENCIONES
        var contador           = 0
        ,   retenciones        = document.querySelectorAll('.capturarCheckboxAcumuladoFacturaCompra')
        ,   labelRetenciones   = document.querySelectorAll('.capturaLabelAcumuladoFacturaCompra')
        ,   id_retencion       = 0
        ,   valorRetencion     = 0
        ,   listadoRetenciones = ''
        ,   simboloRetencion   = ''
        ,   valoresRetenciones = ''
        ,   divValoresRetenciones = '';

        //CAMBIAR LOS VALORES AUTOMATICOS POR LOS MANUALES
        if (contabilidad_manual=='true') {
            subtotalFacturaCompra = subtotal_manual;
            ivaFacturaCompra      = iva_manual;
            totalFacturaCompra    = total_manual;
        }

        // CIClO PARA RECORRER LOS CHECKBOKS DE RETENCIONES
        for(i in retenciones){
            if(typeof(retenciones[i].id)!='undefined' ){

                id_retencion = (retenciones[i].id).split('_')[1];
                if (objectRetenciones_FacturaCompra[id_retencion].tipo_retencion=='ReteIva') {
                    if (objectRetenciones_FacturaCompra[id_retencion].base>ivaFacturaCompra){  continue; }
                    valorRetencion+= (parseFloat(ivaFacturaCompra)* objectRetenciones_FacturaCompra[id_retencion].valor)/100;
                    valoresRetenciones = formato_numero((parseFloat(ivaFacturaCompra)* objectRetenciones_FacturaCompra[id_retencion].valor)/100,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
                }
                else if (objectRetenciones_FacturaCompra[id_retencion].tipo_retencion=='AutoRetencion') { continue; }
                else{
                    if (objectRetenciones_FacturaCompra[id_retencion].base>subtotalFacturaCompra) {continue;}
                    valorRetencion+= ((parseFloat(subtotalFacturaCompra)* objectRetenciones_FacturaCompra[id_retencion].valor)/100);
                    valoresRetenciones = formato_numero((parseFloat(subtotalFacturaCompra)* objectRetenciones_FacturaCompra[id_retencion].valor)/100,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
                }
                listadoRetenciones += '<div style="margin-bottom:5px; overflow:hidden; width:100%;">'+labelRetenciones[i].innerHTML+'</div>';
                simboloRetencion   += '<div style="margin-bottom:5px">$</div>';
                divValoresRetenciones += '<div style="margin-bottom:5px">'+valoresRetenciones+'</div>';
            }
        }

        totalFacturaCompra = (parseFloat(subtotalFacturaCompra.toFixed(<?php echo $_SESSION['DECIMALESMONEDA']; ?>)) - parseFloat(valorRetencion.toFixed(<?php echo $_SESSION['DECIMALESMONEDA']; ?>))) + parseFloat(ivaFacturaCompra.toFixed(<?php echo $_SESSION['DECIMALESMONEDA']; ?>));

        //renderizamos los valores en la ventana
        document.getElementById("subtotalFacturaCompra").innerHTML           = formato_numero(subtotalFacturaCompra,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
        document.getElementById("divRetencionesFacturaCompra").style.display = 'inline';
        document.getElementById("idretencionFacturaCompra").innerHTML        = listadoRetenciones;
        document.getElementById("simboloRetencionFacturaCompra").innerHTML   = simboloRetencion;
        document.getElementById("retefuenteFacturaCompra").innerHTML         = divValoresRetenciones;
        document.getElementById("totalFacturaCompra").innerHTML              = formato_numero(totalFacturaCompra,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
        document.getElementById('labelIvaFacturaCompra').innerHTML           = labelIva;
        document.getElementById('simboloIvaFacturaCompra').innerHTML         = simboloIva;
        document.getElementById('ivaFacturaCompra').innerHTML                = valoresIva;

        if (contabilidad_manual=='true') {
            if (subtotal_manual>0 && total_manual>0) {
                document.getElementById("subtotalFacturaCompra").innerHTML = subtotal_manual;
                document.getElementById("labelIvaFacturaCompra").innerHTML = "Iva";
                document.getElementById("ivaFacturaCompra").innerHTML      = iva_manual;
                document.getElementById("totalFacturaCompra").innerHTML    = total_manual;
            }
            subtotalFacturaCompra = subtotal_anterior;
            ivaFacturaCompra      = iva_anterior;
            totalFacturaCompra    = total_anterior;
        }
    }

    function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
        numero = parseFloat(numero);
        if(isNaN(numero)){ return ''; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); }        // Redondeamos

        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) { numero=numero.replace(miles, '$1' + separador_miles + '$2'); }
        }
        return numero;
    }


    //========================= BUSCAR FACTURA DE COMPRA ==============================================//
    function buscarFacturaCompra(){ ventanaBuscarFacturaCompra(); }

    function ventanaBuscarFacturaCompra(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_factura_compra = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_factura_compra',
            title       : 'Seleccionar articulo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/grilla_buscar_factura_compra.php',
                scripts : true,
                nocache : true,
                params  : { filtro_bodega   : document.getElementById("filtro_ubicacion_facturacion_compras").value }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    height      : 56,
                    iconAlign   : 'left',
                    handler     : function(){ Win_Ventana_buscar_factura_compra.close(id) }
                },'-'
            ]
        }).show();
    }

    //=========================== IMPRIMIR FACTURA DE COMPRA ==========================================//

    function imprimirFacturaCompra (){
        window.open("facturacion/bd/imprimir_factura_compra.php?id="+'<?php echo $id_factura_compra; ?>');
    }

    function imprimirFacturaCompraExcel (){
        window.open("facturacion/bd/exportar_excel_factura_compra.php?id="+'<?php echo $id_factura_compra; ?>');
    }

    function validarArticulosFactura(){ return 3; }

    function cargarOrdenCompraFactura(){
        var ordenCompra = document.getElementById('ordenCompra').value;
        if(isNaN(ordenCompra) || ordenCompra==0){ alert('Numero de orden de compra no valido'); return }
        ajaxBuscarFacturaCompraOrden(ordenCompra); }

    function agregarOrdenCompraFactura(){ alert('No se pueden agregar ordenes de compra a una factura ya cerrada'); return; }

    //================================ CANCELA LA FACTURA DE COMPRA ====================================//
    function cancelarFacturaCompra(){

        var contArticulos = 0;
        if(!document.getElementById('DivArticulosFactura')){ console.log("in"); return; }

        arrayIdsArticulos = document.getElementById('DivArticulosFactura').querySelectorAll('.classInputInsertArticuloFactura');
        for(i in arrayIdsArticulos){
            if(arrayIdsArticulos[i].value > 0){ contArticulos++; }
        }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar la presente Factura de compra y su contenido relacionado')){
                cargando_documentos('Cancelando Factura de Compra...','');
                Ext.get('render_btns_factura_compra').load({
                    url     : 'facturacion/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc       : 'cancelarFacturaCompra',
                        idFactura : '<?php echo $id_factura_compra; ?>',
                        idBodega  : '<?php echo $filtro_bodega ?>'
                    }
                });
            };
        }
    }

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento(){

        var cont = 0
        ,   contArticulo
        ,   divsArticulosFactura = document.querySelectorAll(".bodyDivArticulosFactura");

        for(i in divsArticulosFactura){
            if(typeof(divsArticulosFactura[i].id)!='undefined'){
                contArticulo = (divsArticulosFactura[i].id).split('_')[1]
                if(document.getElementById('check_factura_activo_fijo_'+contArticulo))
                {
                    if(document.getElementById('check_factura_activo_fijo_'+contArticulo).checked){ cont++; }
                }
            }
        }

        mensaje=(cont>0)? 'Se eliminaran los activos fijos relacionados con el documento' : '' ;

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?\nSi lo hace se eliminara el movimiento contable del mismo y\nRegresaran Los articulos al Inventario\n"+mensaje)) {
            cargando_documentos('Editando Factura de Compra...','');
            Ext.get('render_btns_factura_compra').load({
                url     : 'facturacion/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc         : 'modificarDocumentoGenerado',
                    idFactura : '<?php echo $id_factura_compra; ?>',
                    id_bodega   : document.getElementById("filtro_ubicacion_facturacion_compras").value
                }
            });
        }
    }

    function restaurarFacturaCompra(){
        cargando_documentos('Restaurando Factura de Compra...','');
        Ext.get('render_btns_factura_compra').load({
            url     : 'facturacion/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc       : 'restaurarFacturaCompra',
                idFactura : '<?php echo $id_factura_compra; ?>',
                idBodega  : '<?php echo $filtro_bodega; ?>'
            }
        });
    }

    function ventanaDocumentosCruceFacturaCompra() {

        Win_ventanaDocumentosCruceFacturaCompra = new Ext.Window({
            width       : 550,
            height      : 500,
            id          : 'Win_ventanaDocumentosCruceFacturaCompra',
            title       : '',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'facturacion/documentos_adjuntos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    id_factura_compra   : '<?php echo $id_factura_compra; ?>',
                }
            },
        }).show();

    }

    //======================= ENVIAR FACTURA ELECTRONICA =======================//
  function enviarDIANFacturaCompra(){
    if(confirm('\u00BFEstas seguro de enviar la factura electronica?')){
      cargando_documentos('Enviando Documento soporte...','');
      Ext.get('render_btns_factura_compra').load({
        url     : 'facturacion/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
                    opc               : 'enviarFacturaDIAN',
                    id_factura        : '<?php echo $id_factura_compra; ?>',
                    opcGrillaContable : 'FacturaCompra'
                  }
      });
    }
  }


</script>
