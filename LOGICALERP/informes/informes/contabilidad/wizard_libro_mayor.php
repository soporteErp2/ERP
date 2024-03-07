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
  .sub-content[data-position="left"]{width: 50%;}
  .content-grilla-filtro { height: calc(100% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 170px;}
  .content-grilla-filtro .cell[data-col="4"]{width: 58px; text-align: right;border-right: none;}
  .sub-content [data-width="input"]{width: 120px;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">NIVEL DE CUENTAS</div>
    <p>
      <select data-width="input" id="nivel_cuentas_LM">
        <?php echo $nivel_cuentas; ?>
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
  <div class="sub-content" data-position="left">
    <div class="title">FORMATO DE DIGITOS</div>
    <p>
      <table>
        <tbody>
          <tr>
            <td>Separador miles</td>
            <td>
              <select id="separador_milesLM" data-width="input">
                <option value=".">Punto (.)</option>
                <option value=",">Coma (,)</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Separador decimales</td>
            <td>
              <select id="separador_decimalesLM" data-width="input">
                <option value=",">Coma (,)</option>
                <option value=".">Punto (.)</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </p>
    <div class="title">CUENTAS DE CIERRE</div>
    <p>
      <table>
        <tbody>
          <tr
            <td>
              <select id="cuentas_cierreLM" data-width="input">
                <option value="si">Mostrar</option>
                <option value="no">No Mostrar</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </p>
  </div>
</div>
<script>
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

  carga_sucursal_guardada();

  //CARGAR SUCURSAL GUARDADA
  function carga_sucursal_guardada(){
    try{
      if(typeof(localStorage.sucursalLM) != "undefined" && localStorage.sucursalLM != ""){
        setTimeout(function(){
          document.getElementById("filtro_sucursal_sucursales_libro_mayor").value = localStorage.sucursalLM;
        },1000);
      }
    } catch{
      setTimeout('carga_sucursal_guardada()',1500);
    }
  }

  if(typeof(localStorage.nivel_cuentasLM) != "undefined")
    if(localStorage.nivel_cuentasLM != "")
      document.getElementById("nivel_cuentas_LM").value = localStorage.nivel_cuentasLM;

  if(typeof(localStorage.MyInformeFiltroFechaInicioLM) != "undefined")
    if(localStorage.MyInformeFiltroFechaInicioLM != "")
      document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioLM;

  if(typeof(localStorage.MyInformeFiltroFechaFinalLM) != "undefined")
    if(localStorage.MyInformeFiltroFechaFinalLM != "")
      document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalLM;

  if(typeof(localStorage.separador_milesLM) != "undefined")
    if(localStorage.separador_milesLM != "")
      document.getElementById("separador_milesLM").value = localStorage.separador_milesLM;

  if(typeof(localStorage.separador_decimalesLM) != "undefined")
    if(localStorage.separador_decimalesLM != "")
      document.getElementById("separador_decimalesLM").value = localStorage.separador_decimalesLM;

  if(typeof(localStorage.cuentas_cierreLM) != "undefined")
    if(localStorage.cuentas_cierreLM != "")
      document.getElementById("cuentas_cierreLM").value = localStorage.cuentas_cierreLM;
</script>
