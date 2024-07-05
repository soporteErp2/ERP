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
    <div class="title">FILTRAR POR TERCEROS</div>
    <div class="content-grilla-filtro">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Documento</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div>
        <div class="cell" data-col="4" data-icon="un_checked" onclick="cambiaCheckERC(this);" id="div_check_terceros"> <span>Todos </span> </div>
      </div>
      <div class="body" id="body_grilla_filtro">
      </div>
    </div>
  </div>
  <div class="sub-content" data-position="left">
    <div class="title">NIVEL DE CUENTAS</div>
    <p>
      <select data-width="input" id="nivel_cuentas_BC"
        <option value="1">Clase</option>
        <option value="2">Grupo</option>
        <option value="4">Cuenta</option>
        <option value="6">Subcuenta</option>
        <option value="8">Auxiliares</option>
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
    <div class="title">RANGO DE CUENTAS</div>
    <p>
      <table>
        <tr>
          <td>Cuenta Inicial</td>
          <td><input type="text" id="cuenta_inicial" data-width="input" onkeyup="validaCuentaPuc(event,this);" /></td>
          <td data-icon="btn-search" onclick="ventanaBuscarCuentaPuc('cuenta_inicial')"></td>
        </tr>
        <tr>
          <td>Cuenta Final</td>
          <td><input type="text" id="cuenta_final" data-width="input" onkeyup="validaCuentaPuc(event,this);"/></td>
          <td data-icon="btn-search" onclick="ventanaBuscarCuentaPuc('cuenta_final')"></td>
        </tr>
      </table>
    </p>
    <div class="title">TOTALIZADO</div>
    <p>
      <select data-width="input" id="totalizadoBC">
        <option value="General">General</option>
        <option value="Por Cuentas">Por cuentas</option>
      </select>
    </p>
    <div class="title">CUENTAS DE CIERRE</div>
    <p>
      <select data-width="input" id="incluir_cuentas_cierre_BC">
        <option value="false">No mostrar</option>
        <option value="true">Mostrar</option>
      </select>
    </p>
    <div class="title">FORMATO DE DIGITOS</div>
    <p>
      <table>
        <tbody>
          <tr>
            <td>Separador miles</td>
            <td>
              <select id="separador_milesBC" data-width="input">
                <option value=".">Punto (.)</option>
                <option value=",">Coma (,)</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Separador decimales</td>
            <td>
              <select id="separador_decimalesBC" data-width="input">
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

  document.getElementById("cuenta_inicial").value = (typeof(localStorage.cuenta_inicialBC ) != "undefined")? localStorage.cuenta_inicialBC  : "" ;
  document.getElementById("cuenta_final").value = (typeof(localStorage.cuenta_finalBC ) != "undefined")? localStorage.cuenta_finalBC  : "" ;

  if(typeof(localStorage.nivel_cuentasBC) != "undefined")
    if(localStorage.nivel_cuentasBC != "")
      document.getElementById("nivel_cuentas_BC").value = localStorage.nivel_cuentasBC;

  if(typeof(localStorage.sucursal_balance_comprobacion) != "undefined")
    if(localStorage.sucursal_balance_comprobacion != "")
      setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_balance_comprobacion").value = localStorage.sucursal_balance_comprobacion;},100);

  if(typeof(localStorage.MyInformeFiltroFechaInicioBalanceComprobacion) != "undefined")
    if(localStorage.MyInformeFiltroFechaInicioBalanceComprobacion != "")
      document.getElementById("MyInformeFiltroFechaInicio").value = localStorage.MyInformeFiltroFechaInicioBalanceComprobacion;

  if(typeof(localStorage.MyInformeFiltroFechaFinalBalanceComprobacion) != "undefined")
    if(localStorage.MyInformeFiltroFechaFinalBalanceComprobacion != "")
      document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalBalanceComprobacion;

  if(typeof(localStorage.totalizadoBC) != "undefined")
    if(localStorage.totalizadoBC != "")
      document.getElementById("totalizadoBC").value = localStorage.totalizadoBC;

  if(typeof(localStorage.separador_milesBC) != "undefined")
    if(localStorage.separador_milesBC != "")
      document.getElementById("separador_milesBC").value = localStorage.separador_milesBC;

  if(typeof(localStorage.separador_decimalesBC) != "undefined")
    if(localStorage.separador_decimalesBC != "")
      document.getElementById("separador_decimalesBC").value = localStorage.separador_decimalesBC;

  if(checkBoxSelectAllTercerosBC == "true")
    document.getElementById('div_check_terceros').dataset.icon = 'check';

  //RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
  tercerosConfiguradosBC.forEach(function(elemento) {rows += elemento;});
  document.getElementById('body_grilla_filtro').innerHTML=rows;

  function cambiaCheckERC(element){
    if(element.getAttribute("data-icon") == "un_checked"){
      element.dataset.icon="check";
      checkBoxSelectAllTercerosBC="true";
    }
    else{
      element.dataset.icon = "un_checked";
      checkBoxSelectAllTercerosBC = "false";
    }
  }
</script>
