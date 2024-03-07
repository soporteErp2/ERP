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
  .sub-content[data-position="right"]{width: calc(50% - 3px); }
  .sub-content[data-position="left"]{width: 50%; overflow:auto;}
  .content-grilla-filtro { height: calc(50% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
  .sub-content [data-width="input"]{width: 120px;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">TIPO BALANCE</div>
    <p>
      <select data-width="input" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio()">
        <option value="clasificado">Clasificado</option>
        <option value="comparativo">Comparativo</option>
      </select>
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
  <div class="sub-content" data-position="left">
    <div class="title">NIVEL DE CUENTAS</div>
    <p>
      <select id="nivel_cuenta" data-width="input">
        <?php echo $nivel_cuentas; ?>
      </select>
    </p>
    <div class="title">FECHAS DEL INFORME</div>
    <p>
      <table>
        <tr id="trFechaInicio">
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

  function mostrarOcultarDivFechaInicio(){
    var tipo_balance = document.getElementById('tipo_balance').value;
    if (tipo_balance=="clasificado") { document.getElementById("trFechaInicio").style.visibility = "hidden"; }
    else if (tipo_balance=="comparativo") { document.getElementById("trFechaInicio").style.visibility = "visible"; }
  }

  if (localStorage.tipo_balance=="clasificado") {
    document.getElementById('tipo_balance').value = "clasificado";
    mostrarOcultarDivFechaInicio();
  }
  else if (localStorage.tipo_balance=="comparativo") {
    document.getElementById('tipo_balance').value = "comparativo";
    mostrarOcultarDivFechaInicio();
  }
  else{
    mostrarOcultarDivFechaInicio();
  }

  if (typeof(localStorage.generar)!="undefined")
    if (localStorage.generar!="")
        document.getElementById("nivel_cuenta").value=localStorage.generar;

  if (typeof(localStorage.MyInformeFiltroFechaInicio)!="undefined")
    if (localStorage.MyInformeFiltroFechaInicio!="")
        document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicio;

  if (typeof(localStorage.MyInformeFiltroFechaFinal)!="undefined")
    if (localStorage.MyInformeFiltroFechaFinal!="")
        document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinal;
</script>
