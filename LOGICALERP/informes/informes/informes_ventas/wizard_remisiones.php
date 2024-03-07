<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position="right"]{width: calc(60% - 3px); }
  .sub-content[data-position="left"]{width: 40%; overflow:auto;}
  .content-grilla-filtro { height: calc(50% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
  .sub-content [data-width="input"]{ width: 150px; }
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">FILTRAR POR CLIENTE</div>
    <div class="content-grilla-filtro">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Documento</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Proveedor" onclick="ventanaBusquedaTerceroRV();"></div>
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
        <div class="cell" data-col="1" data-icon="search" title="Buscar Usuario" onclick="ventanaBusquedaTerceroRV('vendedores');"></div>
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
    <div class="title">ESTADO DEL DOCUMENTO</div>
    <p>
      <select data-width="input" id="estado_remision">
        <option value="todas">Todas</option>
        <option value="facturadas">Facturadas</option>
        <option value="pendientes">Pendiente Facturar</option>
        <option value="anuladas">Anuladas</option>
      </select>
    </p>
    <div class="title">FILTRAR POR CENTROS DE COSTOS</div>
    <div class="content-grilla-filtro" style=" height: calc(50% - 107px);">
      <div class="head">
        <div class="cell" data-col="2">Codigo</div>
        <div class="cell" data-col="2">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Centro de costos" onclick="ventanaBusquedaCentroCostosRV();"></div>
      </div>
      <div class="body" id="body_grilla_filtro_ccos">
      </div>
    </div>
  </div>
</div>
<script>

  Ext.onReady(function(){
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

    if(typeof(localStorage.sucursal_remisiones) != "undefined")
      if(localStorage.sucursal_remisiones != "")
        setTimeout(function(){document.getElementById("filtro_sucursal_remisiones").value = localStorage.sucursal_remisiones;},100);

    if(typeof(localStorage.MyInformeFiltroFechaInicioRemisionesVenta) != "undefined")
      if(localStorage.MyInformeFiltroFechaInicioRemisionesVenta != "")
        document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioRemisionesVenta;

    if(typeof(localStorage.MyInformeFiltroFechaFinalRemisionesVenta) != "undefined")
      if(localStorage.MyInformeFiltroFechaFinalRemisionesVenta != "")
        document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalRemisionesVenta;

    if(typeof(localStorage.detallado_itemsRV) != "undefined")
      if(localStorage.detallado_itemsRV != "")
        document.getElementById("detallado_items").value = localStorage.detallado_itemsRV;

    if(typeof(localStorage.estado_remision) != "undefined")
      if(localStorage.estado_remision != "")
        document.getElementById("estado_remision").value = localStorage.estado_remision;

    var rows = '';
    //RECORRER EL ARRAY PARA RENDERIZAR LOS PROVEEDORES DEL FILTRO
    tercerosConfiguradosRV.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML = rows;

    rows = '';
    //RECORRER EL ARRAY PARA RENDERIZAR LOS USUARIOS DEL FILTRO
    vendedoresConfiguradosRV.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_usuarios').innerHTML = rows;

    rows = '';
    //RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTOS DEL FILTRO
    CentroCostosConfiguradosRV.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_ccos').innerHTML = rows;
  });
</script>
