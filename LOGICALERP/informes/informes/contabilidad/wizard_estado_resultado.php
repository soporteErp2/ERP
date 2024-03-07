<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');

  $sql = "SELECT nombre FROM puc_configuracion WHERE activo = 1 AND id_empresa = $_SESSION[EMPRESA] GROUP BY nombre ORDER BY digitos ASC";
  $query = $mysql->query($sql,$mysql->link);

  while($row = $mysql->fetch_array($query)){
    $nivel_cuentas .= "<option value='$row[nombre]'>$row[nombre]</option>";
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
    <div class="title">FILTRAR CENTROS DE COSTOS</div>
    <div class="content-grilla-filtro">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Codigo</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Centro de costos" onclick="ventanaBuscarCentroCostos();"></div>
        <div class="cell" data-col="4" data-icon="un_checked" onclick="cambiaCheckER(this);" id="div_check_terceros"> <span>Todos </span> </div>
      </div>
      <div class="body" id="body_grilla_filtro">
      </div>
    </div>
  </div>
  <div class="sub-content" data-position="left">
    <div class="title">NIVEL DE CUENTAS</div>
    <p>
      <select id="nivel_cuenta" data-width="input">
        <?php echo $nivel_cuentas; ?>
      </select>
    </p>
    <div class="title">TIPO DE INFORME</div>
    <p>
      <select id="tipo_balance" data-width="input" onchange="checkTipoInforme();">
        <option value="mensual">Mensual</option>
        <option value="mensual_acumulado">Mensual Acumulado</option>
        <option value="comparativo_mensual">Comparativo Mensual</option>
        <option value="comparativo_anual">Comparativo Anual</option>
        <option value="rango_fechas">Rango de Fechas</option>
      </select>
    </p>
    <div class="title">FECHAS DEL INFORME</div>
    <p>
      <table id="tb_fecha_inicio">
        <tr>
          <td style="width: 80px;">Fecha Inicial</td>
          <td><input type="text" id="MyInformeFiltroFechaInicio"/></td>
        </tr>
      </table>
      <table>
        <tr>
          <td style="width: 80px;">Fecha Final</td>
          <td><input type="text" id="MyInformeFiltroFechaFinal"/></td>
        </tr>
      </table>
    </p>
    <div class="title">FORMATO DE DIGITOS</div>
    <p>
      <table>
        <tbody>
          <tr>
            <td>Separador miles</td>
            <td>
              <select id="separador_miles" data-width="input">
                <option value=".">Punto (.)</option>
                <option value=",">Coma (,)</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Separador decimales</td>
            <td>
              <select id="separador_decimales" data-width="input">
                <option value=",">Coma (,)</option>
                <option value=".">Punto (.)</option>
              </select>
            </td>
          </tr>
        </tbody>
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

  if(typeof(localStorage.MyInformeFiltroFechaInicioEstadoResultado) != "undefined")
    if(localStorage.MyInformeFiltroFechaInicioEstadoResultado != "")
      document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioEstadoResultado;

  if(typeof(localStorage.MyInformeFiltroFechaFinalEstadoResultado) != "undefined")
    if(localStorage.MyInformeFiltroFechaFinalEstadoResultado != "")
      document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalEstadoResultado;

  if(typeof(localStorage.nivel_cuentas_EstadoResultado) != "undefined")
    if(localStorage.nivel_cuentas_EstadoResultado != "")
      document.getElementById("nivel_cuenta").value = localStorage.nivel_cuentas_EstadoResultado;

  //RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTOS DEL FILTRO
  centroCostosConfigurados.forEach(function(elemento) {rows += elemento;});
  document.getElementById('body_grilla_filtro').innerHTML=rows;

  function cambiaCheckER(element){
    if(element.getAttribute("data-icon") == "un_checked"){
      element.dataset.icon="check";
      checkBoxSelectAllCcosER="true";
    }
    else{
      element.dataset.icon="un_checked";
      checkBoxSelectAllCcosER="false";
    }
  }

  checkTipoInforme();

  function checkTipoInforme(){
    var tipo = document.getElementById('tipo_balance').value;
    if(tipo == 'rango_fechas'){
      document.getElementById('tb_fecha_inicio').style.display = '';
    }
    else{
      document.getElementById('tb_fecha_inicio').style.display = 'none';
    }
  }
</script>
