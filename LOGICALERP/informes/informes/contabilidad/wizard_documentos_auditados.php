<?php
  include("../../../../configuracion/conectar.php");
  include("../../../../configuracion/define_variables.php");

  $sql = "SELECT tipo_documento FROM documentos_auditados WHERE activo = 1 AND id_empresa = '$_SESSION[EMPRESA]' GROUP BY tipo_documento";
  $query = $mysql->query($sql,$mysql->link);

  while($row = $mysql->fetch_array($query)){
    $select .= "<option>$row[tipo_documento]</option>";
  }
?>
<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position="right"]{width: calc(60% - 3px); }
  .sub-content[data-position="left"]{width: 40%; overflow:auto;}
  .content-grilla-filtro { height: calc(100% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 170px;}
  .content-grilla-filtro .cell[data-col="4"]{width: 58px; text-align: right;border-right: none;}
  .sub-content [data-width="input"]{width: 120px;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">FILTRAR POR EMPLEADOS</div>
    <div class="content-grilla-filtro">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Documento</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div>
      </div>
      <div class="body" id="body_grilla_filtro_empleados">
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
    <div class="title">TIPO DOCUMENTO</div>
    <p>
      <select data-width="input" id="tipoDocumento">
        <option>Seleccione</option>
        <?php echo $select; ?>
      </select>
    </p>
  </div>
</div>
<script>
  var rows = '';

  new Ext.form.DateField({
    format     : "Y-m-d",
    width      : 120,
    id         : "cmpFechaInicio",
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
    editable   : false
  });

  carga_sucursal_bodega_guardada();

  //CARGAR SUCURSAL Y BODEGA GUARDADAS
  function carga_sucursal_bodega_guardada(){
    if(typeof(localStorage.sucursalDA) != "undefined" && localStorage.sucursalDA != ""){
      try{
        setTimeout(function(){
          document.getElementById("filtro_sucursal_sucursales_documentos_auditados").value = localStorage.sucursalDA;
        },800);
      } catch(e){
        carga_sucursal_bodega_guardada();
      }
    }
  }

  function cargarEmpleadosGuardados(){
    Ext.get('body_grilla_filtro_empleados').load({
      url     : '../informes/informes/contabilidad/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc                : 'cargarEmpleadosGuardados',
                  arrayEmpleadosJSON : localStorage.arrayEmpleadosJSONDA
                }
    });
  }

  if(typeof(localStorage.MyInformeFiltroFechaInicioDA) != "undefined")
    if(localStorage.MyInformeFiltroFechaInicioDA != "")
      document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioDA;

  if(typeof(localStorage.MyInformeFiltroFechaFinalDA) != "undefined")
    if(localStorage.MyInformeFiltroFechaFinalDA != "")
      document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalDA;

  if(typeof(localStorage.tipoDocumentoDA) != "undefined")
    if(localStorage.tipoDocumentoDA != "")
      document.getElementById("tipoDocumento").value = localStorage.tipoDocumentoDA;

  if(localStorage.arrayEmpleadosJSONDA != "" && typeof(localStorage.arrayEmpleadosJSONDA) != "undefined"){
    cargarEmpleadosGuardados();
  }
</script>
