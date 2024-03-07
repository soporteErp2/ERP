<?php include("../../../../configuracion/define_variables.php"); ?>

<div class="divPaginaDefaultNotasDevolcion" >
    CARGUE UN DOCUMENTO PARA CONTINUAR<br><br> <img src="img/carga_doc.png">
    <div id="termina"></div>
</div>

<script >
    var heigtPadre   =document.getElementById('contenedorPadreVentana<?php echo $opcGrillaContable;?>').offsetHeight;
    document.getElementById('contenedor_<?php echo $opcGrillaContable;?>').setAttribute('style','height:'+(heigtPadre-119));

    document.getElementById("titleDocumento<?php echo $opcGrillaContable; ?>").innerHTML="";
    Ext.getCmp("Btn_exportar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").show();

    // DEHABILITAR EL BOTON DE GRUPOS SI ES UNA FACTURA DE VENTA
    // Ext.getCmp("Btn_itemsGrupos_DevolucionVenta").disable();

    ///////////////////////////////////// FUNCION PARA CARGAR UN DOCUMENTO //////////////////////////////////////////
    function buscarCotizacionPedido<?php echo $opcGrillaContable;?>(event,Input){
    	tecla   = (Input) ? event.keyCode : event.which;
        numero  = Input.value;

        if (tecla== 13 ) { ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value);}
    }

    function ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido,idDocumento){

       if ('<?php echo $opcGrillaContable; ?>'=='DevolucionVenta') {

            if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {             //CARGA REMISION
                tablaBuscar = "ventas_remisiones";
                opcCargar   = "remisionVenta";
            }
            else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/factura.png"){            //CARGA FACTURA
                opcCargar   = "facturaVenta";
                tablaBuscar ="ventas_facturas";
            }
        }
        else{
            titulo      = "Seleccione la Factura de Compra";
            tablaBuscar = "";
            opcCargar   = "facturaCompra";
        }

        divRender = Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>");

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
                idDocumento       : idDocumento
            }
        });
    }

    /////////////////////////////////////////// FUNCION PARA CARGAR UN DOCUMENTO DESDE EL BOTON BUSCAR //////////////////////////////////////////////////

    function ventanaBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(){

        if ('<?php echo $opcGrillaContable; ?>'=='DevolucionVenta') {

             if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {
                    opcCargar      = "remisionVenta";
                    titulo         = "Seleccione la remision";
                    tablaGrilla    = "ventas_remisiones";
                    id_tabla_carga = "id_remision_venta";
                    tabla_inventario_carga       = "ventas_remisiones_inventario";
                    nombreGrillaCotizacionPedido = "grillanotaRemision";
                }
                else if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/factura.png"){
                    opcCargar      = "facturaVenta";
                    titulo         ="Seleccione la factura";
                    tablaGrilla    ="ventas_facturas";
                    id_tabla_carga ="id_factura_venta";
                    tabla_inventario_carga       = "ventas_facturas_inventario";
                    nombreGrillaCotizacionPedido = "grillaNotaFactura";
                }
        }
        else{
            opcCargar      = 'facturaCompra';
            titulo         = "Seleccione la factura";
            tablaGrilla    = "compras_facturas";
            id_tabla_carga = "id_factura_compra";
            tabla_inventario_carga       = "compras_facturas_inventario";
            nombreGrillaCotizacionPedido = "grillaNotaFactura";
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
                url     : 'notas_inventario/notas_devolucion/bd/grillaBuscarCotizacionPedido.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                          : 'buscar_cotizacionPedido',
                    opcGrillaContable            : '<?php echo $opcGrillaContable; ?>',
                    opcCargar                    : opcCargar,
                    tablaCotizacionPedido        : tablaGrilla,
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

   //================================================= BUSCAR   ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }

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


    function limpiarGrillaContable(opc,filtro_bodega){
       if (document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").getAttribute("src")=="img/remisiones.png") {

            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("src","img/factura.png");
            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("width","20px");
            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("height","20px");

            document.getElementById("textoFacturardesde<?php echo $opcGrillaContable; ?>").innerHTML="<b>Factura</b>";
            document.getElementById("divContenedorCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("title","Click para cargar una Remision");
            document.getElementById("cotizacionPedido<?php echo $opcGrillaContable; ?>").focus();
        }
        else{

            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("src","img/remisiones.png");
            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("width","20px");
            document.getElementById("imgCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("height","20px");
            document.getElementById("textoFacturardesde<?php echo $opcGrillaContable; ?>").innerHTML="<b>Remision</b>";
            document.getElementById("divContenedorCargarDesde<?php echo $opcGrillaContable; ?>").setAttribute("title","Click para cargar una Factura");

            document.getElementById("cotizacionPedido<?php echo $opcGrillaContable; ?>").focus();
        }
    }

</script>