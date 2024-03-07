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
    <div class="title">FILTRAR POR CLIENTE</div>
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
    <div class="title">FILTRAR POR VENDEDORES</div>
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
    <div class="title">DETALLADO ITEMS</div>
    <p>
      <select data-width="input" id="detallado_items">
        <option value="no">No</option>
        <option value="si">Si</option>
      </select>
    </p>
    <div class="title">DETALLADO DOCUMENTOS</div>
    <p>
      <select data-width="input" id="detallado_documentos">
        <option value="no">No</option>
        <option value="devolucion">Devoluciones</option>
      </select>
    </p>
    <div class="title">UTILIDAD</div>
    <p>
      <select data-width="input" id="utilidad">
        <option value="No">No</option>
        <option value="Si">Si</option>
      </select>
    </p>
    <div class="title">FILTRAR POR CENTROS DE COSTOS</div>
    <div class="content-grilla-filtro" style=" height: calc(50% - 107px);">
      <div class="head">
        <div class="cell" data-col="2">Codigo</div>
        <div class="cell" data-col="2">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Centro de costos" onclick="ventanaBusquedaCentroCostosFV();"></div>
      </div>
      <div class="body" id="body_grilla_filtro_ccos">
      </div>
    </div>
    <div class="title">FACTURACION ELECTRONICA</div>
    <p>
      <select data-width="input" id="facturacion_electronica">
        <option value="Todas">Todas las facturas</option>
        <option value="No">No Enviadas</option>
        <option value="Si">Enviadas</option>
      </select>
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

    if (typeof(localStorage.sucursal_facturas)!="undefined")
        if (localStorage.sucursal_facturas!="")
            setTimeout(function(){document.getElementById("filtro_sucursal_facturas").value=localStorage.sucursal_facturas;},100);

    if (typeof(localStorage.MyInformeFiltroFechaInicioFacturas)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioFacturas!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioFacturas;

    if (typeof(localStorage.MyInformeFiltroFechaFinalFacturas)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalFacturas!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalFacturas;

    if (typeof(localStorage.detallado_itemsFV)!="undefined")
        if (localStorage.detallado_itemsFV!="")
            document.getElementById("detallado_items").value=localStorage.detallado_itemsFV;

    if (typeof(localStorage.detallado_documentosFV)!="undefined")
        if (localStorage.detallado_documentosFV!="")
            document.getElementById("detallado_documentos").value=localStorage.detallado_documentosFV;

    if (typeof(localStorage.utilidadFV)!="undefined")
        if (localStorage.utilidadFV!="")
            document.getElementById("utilidad").value=localStorage.utilidadFV;

    if (typeof(localStorage.facturacion_electronica)!="undefined")
        if (localStorage.facturacion_electronica!="")
            document.getElementById("facturacion_electronica").value=localStorage.facturacion_electronica;


    //RECORRER EL ARRAY PARA RENDERIZAR LOS PROVEEDORES DEL FILTRO
    tercerosConfiguradosFV.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    rows = '';
    //RECORRER EL ARRAY PARA RENDERIZAR LOS USUARIOS DEL FILTRO
    vendedoresConfiguradosFV.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_usuarios').innerHTML=rows;

    rows = '';
    //RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTOS DEL FILTRO
    CentroCostosConfiguradosFV.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_ccos').innerHTML=rows;

</script>
