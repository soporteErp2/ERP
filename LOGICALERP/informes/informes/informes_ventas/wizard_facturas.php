<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
    <!-- DIV MENU IZQUIERDO -->
    <div style="width: calc(100% - 320px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

        <div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CLIENTE(S)</div>

        <!-- VENTANA BUSCAR TERCERO -->
        <div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
            <div id="contenedor_formulario_configuracion" >
                <div id="contenedor_tabla_configuracion" style="height:178px;">
                    <div class="headTablaBoletas">
                        <div class="campo0">&nbsp;</div>
                        <div class="campo1">Nit</div>
                        <div class="campo2" style="width: 150px;">Cliente</div>
                        <div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
                    </div>
                    <div id="bodyTablaConfiguracion" style="height:140px;"></div>
                </div>
            </div>
        </div>

        <div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR VENDEDORES(S)</div>

        <!-- VENTANA BUSCAR TERCERO -->
        <div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
            <div id="contenedor_formulario_configuracion" >
                <div id="contenedor_tabla_configuracion" style="height:178px;">
                    <div class="headTablaBoletas">
                        <div class="campo0">&nbsp;</div>
                        <div class="campo1">Identificacion</div>
                        <div class="campo2" style="width: 150px;">Vendedor</div>
                        <div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTerceroFV('vendedores');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
                    </div>
                    <div id="bodyTablaConfiguracionVendedores" style="height:140px;"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- DIV MENU DERECHO -->
    <div style="float:right; width:310px; background-color: #eff3fa; height: 100%; border-left:1px solid #8DB2E3;">
        <div style="margin-bottom:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:none;" class="x-panel-header">Fechas del Informe</div>
        <div style="display:table; margin:auto;">
            <div style="overflow:hidden;" id="divFechaInicio">
                <div style="float:left; width:70px; margin-right:5px">Fecha Inicial:</div>
                <div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaInicio"></div>
            </div>
            <div style="overflow:hidden; margin-top:20px;">
                <div style="float:left; width:70px; margin-right:5px">Fecha Final:</div>
                <div style="float:left; width:120px;"><input type="text" id="MyInformeFiltroFechaFinal"></div>
            </div>

            <div style="margin-top:20px;text-align:center;font-weight:bold;">
                <table style="font-size:11px;">
                    <tr>
                        <td><input type="checkbox" id="discriminar_items_facturas_venta"></td>
                        <td>Discriminar items</td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td><input type="checkbox" id="discriminar_utilidad_facturas_venta"></td>
                        <td>Discriminar Utilidad</td>
                    </tr>
                </table>

            </div>
        </div>

        <div style="margin:25px 0 25px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Centros de Costo</div>
        <div style="width: calc(100% - 12px);height:180px;background-color: #CDDBF0;overflow:hidden;margin-left: 5px;">
            <div id="contenedor_formulario_configuracion" >
                <div id="contenedor_tabla_configuracion" style="height:178px;">
                    <div class="headTablaBoletas">
                        <div class="campo0"><img src="img/buscar20.png" onclick="ventanaBusquedaCentroCostosFV();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
                        <div class="campo1" style="width: 70px;">Codigo</div>
                        <div class="campo2" style="width: 150px;">Nombre</div>
                    </div>
                    <div id="bodyTablaConfiguracionCentroCostos" style="height:140px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        id         :"cmpFechaInicio",
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaInicio",
        editable   : false,
        value      : "'.$fechaInicial.'"
        // listeners  : { select: function() {   } }
    });

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaFinal",
        editable   : false,
        value      : new Date(),
        // listeners  : { select: function() {   } }
    });


    if (typeof(localStorage.sucursal_facturas)!="undefined") {
        if (localStorage.sucursal_facturas!="") {
            setTimeout(function(){ document.getElementById("filtro_sucursal_facturas").value=localStorage.sucursal_facturas; },100);
        }
    }

    if (typeof(localStorage.MyInformeFiltroFechaInicioFacturas)!="undefined") {
        if (localStorage.MyInformeFiltroFechaInicioFacturas!="") {
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioFacturas;
        }
    }

    if (typeof(localStorage.MyInformeFiltroFechaFinalFacturas)!="undefined") {
        if (localStorage.MyInformeFiltroFechaFinalFacturas!="") {
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalFacturas;
        }
    }

    if (typeof(localStorage.discriminar_items_facturas_venta)!="undefined") {
        if (localStorage.discriminar_items_facturas_venta!="") {
            if (localStorage.discriminar_items_facturas_venta=="true") {
                document.getElementById("discriminar_items_facturas_venta").checked = true;
            }
            else{
                document.getElementById("discriminar_items_facturas_venta").checked = false;
            }
        }
    }

    if (typeof(localStorage.discriminar_utilidad_facturas_venta)!="undefined") {
        if (localStorage.discriminar_utilidad_facturas_venta!="") {
            if (localStorage.discriminar_utilidad_facturas_venta=="true") {
                document.getElementById("discriminar_utilidad_facturas_venta").checked = true;
            }
            else{
                document.getElementById("discriminar_utilidad_facturas_venta").checked = false;
            }
        }
    }

    //CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
    for ( i = 0; i < arraytercerosFV.length; i++) {
        if (typeof(arraytercerosFV[i])!="undefined" && arraytercerosFV[i]!="") {

            //CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            var div   = document.createElement("div");
            div.setAttribute("id","fila_cartera_tercero_"+i);
            div.setAttribute("class","filaBoleta");
            document.getElementById("bodyTablaConfiguracion").appendChild(div);

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById("fila_cartera_tercero_"+i).innerHTML=tercerosConfiguradosFV[i];

        }
    }
    //CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
    for ( i = 0; i < arrayvendedoresFV.length; i++) {
        if (typeof(arrayvendedoresFV[i])!="undefined" && arrayvendedoresFV[i]!="") {

            //CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            var div   = document.createElement("div");
            div.setAttribute("id","fila_empleado_"+i);
            div.setAttribute("class","filaBoleta");
            document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById("fila_empleado_"+i).innerHTML=vendedoresConfiguradosFV[i];

        }
    }

    //CREAMOS LOS DIV DE LOS CENTROS DE COSTO AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
    for ( i = 0; i < arrayCentroCostosFV.length; i++) {
        if (typeof(arrayCentroCostosFV[i])!="undefined" && arrayCentroCostosFV[i]!="") {

            //CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            var div   = document.createElement("div");
            div.setAttribute("id","fila_centro_costo_"+i);
            div.setAttribute("class","filaBoleta");
            document.getElementById("bodyTablaConfiguracionCentroCostos").appendChild(div);

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById("fila_centro_costo_"+i).innerHTML=CentroCostosConfiguradosFV[i];

        }
    }

</script>
