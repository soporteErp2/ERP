<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(60% - 3px); }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: calc(50% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
    .sub-content [data-width="input"]{width: 150px;}
</style>

<div class="main-content">
    <div class="sub-content" data-position="right">
        <div class="title">FILTRAR POR PROVEEDOR</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Documento</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Proveedor" onclick="ventanaBusquedaTerceroFV();"></div>
            </div>
            <div class="body" id="body_grilla_filtro">
            </div>
        </div>

        <div class="title">FILTRAR POR USUARIOS</div>
        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Codigo</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Usuario" onclick="ventanaBusquedaTerceroFV('vendedores');"></div>
            </div>
            <div class="body" id="body_grilla_filtro_usuarios">
            </div>
        </div>

    </div>

    <div class="sub-content" data-position="left">

        <div class="title">FECHAS DEL INFORME</div>
        <p>
            <table>
                <tr>
                    <td>Fecha Inicial</td>
                    <td><input type="text" id="MyInformeFiltroFechaInicio"/></td>
                </tr>
                <tr>
                    <td>Fecha Final</td>
                    <td><input type="text" id="MyInformeFiltroFechaFinal"/></td>
                </tr>
            </table>
        </p>
        <div class="title">DETALLADO</div>
        <p>
            <select data-width="input" id="discriminar_items_facturas_compra">
              <option value="no">No</option>
              <option value="items">Por Item</option>
              <option value="ordenes">Ordenes de Compra</option>
              <option value="comprobantes">Comprobantes de Egreso</option>
            </select>
        </p>
        <div class="title">FILTRAR POR TIPO DE FACTURA</div>
        <p>
            <select data-width="input" id="discriminar_tipo_factura">
                <option value="">Todos</option>
                <option value="FC">Facturas de compra</option>    
                <option value="DSE">Documento soporte</option>
            </select>
        </p>
        <div class="title">FILTRAR POR CENTROS DE COSTOS</div>
        <div class="content-grilla-filtro">
            <div class="head">
                <!-- <div class="cell" data-col="1"></div> -->
                <div class="cell" data-col="2">Codigo</div>
                <div class="cell" data-col="2">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Centro de costos" onclick="ventanaBusquedaCentroCostosFC();"></div>
            </div>
            <div class="body" id="body_grilla_filtro_ccos">
            </div>
        </div>
    </div>
</div>
<script>
    var rows = '';

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        id         :"cmpFechaInicio",
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaInicio",
        editable   : false,
    });

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaFinal",
        editable   : false,
    });

    if (typeof(localStorage.sucursal_facturas_compra)!="undefined")
        if (localStorage.sucursal_facturas_compra!="")
            setTimeout(function(){document.getElementById("filtro_sucursal_facturas_compra").value=localStorage.sucursal_facturas_compra;},100);

    if (typeof(localStorage.MyInformeFiltroFechaInicioFacturasCompra)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioFacturasCompra!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioFacturasCompra;

    if (typeof(localStorage.MyInformeFiltroFechaFinalFacturasCompra)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalFacturasCompra!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalFacturasCompra;

    if (typeof(localStorage.discriminar_items_facturas_compra)!="undefined")
        if (localStorage.discriminar_items_facturas_compra!="")
            document.getElementById("discriminar_items_facturas_compra").value=localStorage.discriminar_items_facturas_compra;

    if (typeof(localStorage.discriminar_tipo_factura)!="undefined")
        if (localStorage.discriminar_tipo_factura!="")
            document.getElementById("discriminar_tipo_factura").value=localStorage.discriminar_tipo_factura;    


    //RECORRER EL ARRAY PARA RENDERIZAR LOS PROVEEDORES DEL FILTRO
    tercerosConfiguradosFC.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    rows = '';
    //RECORRER EL ARRAY PARA RENDERIZAR LOS USUARIOS DEL FILTRO
    vendedoresConfiguradosFC.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_usuarios').innerHTML=rows;

    rows = '';
    //RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTOS DEL FILTRO
    CentroCostosConfiguradosFC.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_ccos').innerHTML=rows;

</script>
