<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position="right"]{width: calc(60% - 3px); }
  .sub-content[data-position="left"]{width: 40%; overflow:hidden;}
  .content-grilla-filtro { height: calc(50% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
  .sub-content [data-width="input"]{width: 150px;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">SELECCIONAR CLIENTE</div>
    <div class="content-grilla-filtro" style="height:56px;">
      <div class="head">
        <div class="cell" data-col="2">Documento</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Proveedores" onclick="ventanaBusquedaTercero();"></div>
      </div>
      <div id="body_grilla_filtro_tercero">
        <div class="row">
					<input id="id_cliente" type="hidden" value="">
					<div id="documento_cliente" class="cell" data-col="2"></div>
					<div id="nombre_cliente" class="cell" data-col="3"></div>
				</div>
      </div>
    </div>
    <div class="title">SELECCIONAR CENTROS DE COSTOS</div>
    <div class="content-grilla-filtro" style="height:108px;">
      <div class="head">
        <div class="cell" data-col="2">Codigo</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaCcos();"></div>
      </div>
      <div class="body" id="body_grilla_filtro_ccos">
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
  </div>
</div>
<script>
  var rowsCentroCostos  = '';

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

  carga_sucursal_guardada();

  function carga_sucursal_guardada(){
    try{
      if(typeof(localStorage.sucursal_facturas) != "undefined" && localStorage.sucursal_facturas != ""){
        setTimeout(function(){
          document.getElementById("filtro_sucursal_facturas").value = localStorage.sucursal_facturas;
        },600);
      }    
    } catch{
      setTimeout('carga_sucursal_guardada()',1500);
    }
  }

  if(typeof(localStorage.MyInformeFiltroFechaInicioFacturas) != "undefined"){
    if(localStorage.MyInformeFiltroFechaInicioFacturas != ""){
      document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioFacturas;
    }
  }

  if(typeof(localStorage.MyInformeFiltroFechaFinalFacturas) != "undefined"){
    if(localStorage.MyInformeFiltroFechaFinalFacturas != ""){
      document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalFacturas;
    }
  }

  if(typeof(localStorage.cliente) != "undefined"){
    if(localStorage.cliente != ""){
      var divTercero 	=  `<div class="row">
                            <input id="id_cliente" type="hidden" value="` + localStorage.cliente + `">
                            <div id="documento_cliente" class="cell" data-col="2">` + localStorage.documento_cliente + `</div>
                            <div id="nombre_cliente" class="cell" data-col="3">` + localStorage.nombre_cliente + `</div>
                          </div>`;
      document.getElementById('body_grilla_filtro_tercero').innerHTML = divTercero;
    }
  }

  //RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
  centroCostosConfiguradosFR.forEach(function(elemento) {rowsCentroCostos += elemento;});
  document.getElementById('body_grilla_filtro_ccos').innerHTML = rowsCentroCostos;
</script>
