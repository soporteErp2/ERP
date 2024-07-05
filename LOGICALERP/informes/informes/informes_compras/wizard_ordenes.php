<?php
  include('../../../../configuracion/conectar.php');
  include('../../../../configuracion/define_variables.php');
  $id_empresa = $_SESSION['EMPRESA'];

  $sql = "SELECT id,nombre FROM compras_ordenes_tipos WHERE activo = 1 AND id_empresa = $id_empresa";
  $query = $mysql->query($sql,$mysql->link);
  while($row = $mysql->fetch_array($query)){
    $option .= '<option value="'.$row['id'].'" >'.$row['nombre'].'</option>';
  }
?>
<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position="right"]{width: calc(60% - 3px); }
  .sub-content[data-position="left"]{width: 40%; overflow:auto;}
  .content-grilla-filtro { height: 175px; }
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
  .sub-content [data-width="input"]{width: 150px;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">FILTRAR POR PROVEEDORES</div>
    <div class="content-grilla-filtro" style="height:108px;">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Documento</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Proveedores" onclick="ventanaBusquedaTerceroOC();"></div>
      </div>
      <div class="body" id="body_grilla_filtro_tercero">
      </div>
    </div>
    <div class="title">FILTRAR POR CENTROS DE COSTOS</div>
    <div class="content-grilla-filtro" style="height:108px;">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Codigo</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaCcos();"></div>
      </div>
      <div class="body" id="body_grilla_filtro_ccos">
      </div>
    </div>
    <div class="title">FILTRAR POR USUARIOS</div>
    <div class="content-grilla-filtro" style="height:108px;">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Documento</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Usuarios" onclick="ventanaBusquedaTerceroOC('empleados');"></div>
      </div>
      <div class="body" id="body_grilla_filtro_empleado">
      </div>
    </div>
  </div>

  <div class="sub-content" data-position="left">
    <div class="title">FECHAS DEL INFORME</div>
    <p>
      <table>
        <tr>
          <td>Fecha Inicial</td>
          <td>
            <input type="text" id="MyInformeFiltroFechaInicio"/>
          </td>
        </tr>
        <tr>
          <td>Fecha Final</td>
          <td>
            <input type="text" id="MyInformeFiltroFechaFinal"/>
          </td>
        </tr>
      </table>
    </p>
    <div class="title">TIPO ORDEN</div>
    <p>
      <select data-width="input" id="tipo_orden_compra">
        <option value="todo">Todo</option>
        <?php echo $option; ?>
      </select>
    </p>
    <div class="title">FILTRAR POR ESTADO</div>
    <p>
      <select data-width="input" id="estado">
        <option value="todo">Todo</option>
        <option value="facturado">Facturado</option>
        <option value="pendientefacturado">Pendiente Por Facturar</option>
      </select>
    </p>
    <div class="title">DISCRIMINAR POR ITEM</div>
    <p>
      <select data-width="input" id="item">
        <option value="no">No</option>
        <option value="si">Si</option>
      </select>
    </p>
    <div class="title">AUTORIZACIONES</div>
    <p>
      <select data-width="input" id="autorizado">
        <option value="todo">Todo</option>
        <option value="autorizadas">Autorizadas</option>
        <option value="porautorizar">Por Autorizar</option>
        <option value="aplazadas">Aplazadas</option>
        <option value="rechazadas">Rechazadas</option>
      </select>
    </p>
  </div>
</div>
<script>
  var rowsTerceros      = '';
  var rowsEmpleados     = '';
  var rowsCentroCostos  = '';

  new Ext.form.DateField({
    format     : "Y-m-d",
    width      : 120,
    id         : "cmpFechaInicio",
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



  if(typeof(localStorage.MyInformeFiltroFechaInicioOrdenesCompra) != "undefined")
    if(localStorage.MyInformeFiltroFechaInicioOrdenesCompra != "")
      document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioOrdenesCompra;

  if(typeof(localStorage.MyInformeFiltroFechaFinalOrdenesCompra) != "undefined")
    if(localStorage.MyInformeFiltroFechaFinalOrdenesCompra != "")
      document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalOrdenesCompra;

  if(typeof(localStorage.tipo_ordenes_compra) != "undefined")
    if(localStorage.tipo_ordenes_compra != "")
      document.getElementById("tipo_orden_compra").value = localStorage.tipo_ordenes_compra;

  if(typeof(localStorage.estado_ordenes_compra) != "undefined")
    if(localStorage.estado_ordenes_compra != "")
      document.getElementById("estado").value = localStorage.estado_ordenes_compra;

  if(typeof(localStorage.item_ordenes_compra) != "undefined")
    if(localStorage.item_ordenes_compra != "")
      document.getElementById("item").value = localStorage.item_ordenes_compra;

  if(typeof(localStorage.autorizado_ordenes_compra) != "undefined")
    if(localStorage.autorizado_ordenes_compra != "")
      document.getElementById("autorizado").value = localStorage.autorizado_ordenes_compra;

  //RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
  tercerosConfiguradosOC.forEach(function(elemento) {rowsTerceros += elemento;});
  document.getElementById('body_grilla_filtro_tercero').innerHTML=rowsTerceros;

  empleadosConfiguradosOC.forEach(function(elemento) {rowsEmpleados += elemento;});
  document.getElementById('body_grilla_filtro_empleado').innerHTML=rowsEmpleados;

  centroCostosConfiguradosOC.forEach(function(elemento) {rowsCentroCostos += elemento;});
  document.getElementById('body_grilla_filtro_ccos').innerHTML=rowsCentroCostos;

  if(typeof(localStorage.sucursal_ordenes_compra) != "undefined")
    if(localStorage.sucursal_orden_compra != "")
    setTimeout(function(){
      try{
        document.getElementById("filtro_sucursal_ordenes_compra").value = localStorage.sucursal_ordenes_compra;
        cambia_filtro_bodega_ordenes_compra();
      } catch(e){}
    },200);

  if(typeof(localStorage.bodega_ordenes_compra) != "undefined")
    if(localStorage.bodega_ordenes_compra != "")
      setTimeout(function(){
        try{
          document.getElementById("filtro_bodega_ordenes_compra").value = localStorage.bodega_ordenes_compra;
        } catch(e){}
      },800);
</script>
