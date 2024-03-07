<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(60% - 3px); }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: calc(98% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
    .sub-content [data-width="input"]{width: 150px;}
</style>

<div class="main-content">
    <div class="sub-content" data-position="right">
        <div class="title">FILTRAR POR TERCEROS</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Documento</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div>
            </div>
            <div class="body" id="body_grilla_filtro">
            </div>
        </div>

    </div>

    <div class="sub-content" data-position="left">
        <div class="title">FILTRAR POR TIPO</div>
        <p>
            <select data-width="input" id="tipo">
              <option value="todos">Todo</option>
              <option value="ajusteInventario">Ajuste Inventario</option>
              <option value="entradaAlmacen">Entrada Almacen</option>
            </select>
        </p>
        <div class="title">DISCRIMINAR POR ITEM</div>
        <p>
            <select data-width="input" id="item">
              <option value="si">SI</option>
              <option value="no">NO</option>
            </select>
        </p>
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

    if (typeof(localStorage.sucursal_entrada_almacen)!="undefined")
        if (localStorage.sucursal_entrada_almacen!="")
            setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_entrada_almacen").value=localStorage.sucursal_entrada_almacen;},100);

    if (typeof(localStorage.MyInformeFiltroFechaInicioEntradaAlmacen)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioEntradaAlmacen!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioEntradaAlmacen;

    if (typeof(localStorage.MyInformeFiltroFechaFinalEntradaAlmacen)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalEntradaAlmacen!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalEntradaAlmacen;

    if (typeof(localStorage.tipo_entrada_almacen)!="undefined")
        if (localStorage.tipo_entrada_almacen!="")
            document.getElementById("tipo").value=localStorage.tipo_entrada_almacen;

    if (typeof(localStorage.item_entrada_almacen)!="undefined")
        if (localStorage.item_entrada_almacen!="")
            document.getElementById("item").value=localStorage.item_entrada_almacen;

    //RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
    tercerosConfiguradosAA.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

</script>
