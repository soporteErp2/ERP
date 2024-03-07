<?php
  include('../../../../configuracion/conectar.php');
  include('../../../../configuracion/define_variables.php');
  $id_empresa = $_SESSION['EMPRESA'];

  $sql = "SELECT id,nombre FROM compras_requisicion_tipo WHERE activo=1 AND id_empresa = $id_empresa";
  $query = $mysql->query($sql,$mysql->link);
  while($row = $mysql->fetch_array($query)){
    $option .= '<option value="'.$row['id'].'" >'.$row['nombre'].'</option>';
  }
?>
<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position = "right"]{width:calc(60% - 3px);}
  .sub-content[data-position = "left"]{width:40%;overflow:auto;}
  .content-grilla-filtro{height:calc(50% - 45px);}
  .content-grilla-filtro .cell[data-col = "1"]{width:22px;}
  .content-grilla-filtro .cell[data-col = "2"]{width:89px;}
  .content-grilla-filtro .cell[data-col = "3"]{width:190px;}
  .sub-content [data-width = "input"]{width:120px;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">FILTRAR POR PERSONA SOLICITANTE</div>
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
    <div class="title">FILTRAR POR CENTROS DE COSTOS</div>
      <div class="content-grilla-filtro">
        <div class="head">
          <div class="cell" data-col="1"></div>
          <div class="cell" data-col="2">Codigo</div>
          <div class="cell" data-col="3">Nombre</div>
          <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaCcos();"></div>
        </div>
        <div class="body" id="body_grilla_filtro_ccos">
        </div>
      </div>
  </div>
  <div class="sub-content" data-position="left">
    <div class="title">TIPO</div>
    <p>
      <select data-width="input" id="tipo_requisicion">
        <option value="Todos">Todos</option>
        <?php echo $option; ?>
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
    <div class="title">REQUISICIONES CRUZADAS</div>
    <p>
      <select data-width="input" id="tipo_cruce">
        <option value="Todas">Todas</option>
        <option value="cruzadas">Cruzadas</option>
        <option value="pendientes">Pendientes</option>
      </select>
    </p>
    <div class="title">DISCRIMINAR ITEMS</div>
    <p>
      <select data-width="input" id="discrimina_items">
        <option value="No">No</option>
        <option value="Si">Si</option>
      </select>
    </p>
    <div class="title">AUTORIZACIONES</div>
    <p>
      <select data-width="input" id="autorizado">
        <option value="Todas">Todas</option>
        <option value="Autorizada">Autorizadas</option>
        <option value="Aplazada">Aplazadas</option>
        <option value="Rechazada">Rechazadas</option>
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
    editable   : false
  });

  new Ext.form.DateField({
    format     : "Y-m-d",
    width      : 120,
    allowBlank : false,
    showToday  : false,
    applyTo    : "MyInformeFiltroFechaFinal",
    editable   : false,
    value      : new Date(),
    listeners  : { select: function(){} }
  });

  if(typeof(localStorage.MyInformeFiltroFechaInicioRQ) != "undefined")
    if(localStorage.MyInformeFiltroFechaInicioRQ != "")
      document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioRQ;

  if(typeof(localStorage.MyInformeFiltroFechaFinalRQ) != "undefined")
    if(localStorage.MyInformeFiltroFechaFinalRQ != "")
      document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalRQ;

  if(typeof(localStorage.tipo_requisicion) != "undefined")
    if(localStorage.tipo_requisicion != "")
      document.getElementById("tipo_requisicion").value = localStorage.tipo_requisicion;

  if(typeof(localStorage.tipo_cruce_RQ) != "undefined")
    if (localStorage.tipo_cruce_RQ != "")
      document.getElementById("tipo_cruce").value = localStorage.tipo_cruce_RQ;

  if(typeof(localStorage.discrimina_items_RQ) != "undefined")
    if(localStorage.discrimina_items_RQ != "")
      document.getElementById("discrimina_items").value = localStorage.discrimina_items_RQ;

  if(typeof(localStorage.autorizado_RQ) != "undefined")
    if(localStorage.autorizado_RQ != "")
      document.getElementById("autorizado").value = localStorage.autorizado_RQ;

  // RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
  solicitanteConfigurado.forEach(function(elemento) {rows += elemento;});
  document.getElementById('body_grilla_filtro').innerHTML = rows;

  rows = '';
  // RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTO DEL FILTRO
  centroCostosConfiguradosRQ.forEach(function(elemento) {rows += elemento;});
  document.getElementById('body_grilla_filtro_ccos').innerHTML = rows;
</script>
