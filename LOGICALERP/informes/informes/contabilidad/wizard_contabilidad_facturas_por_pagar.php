<?php
  include_once('../../../../configuracion/conectar.php');
  include_once('../../../../configuracion/define_variables.php');

  $optionCuentas = '';

  $sqlCuentasPago =  "SELECT cuenta,nombre
                      FROM configuracion_cuentas_pago
                      WHERE id_empresa = $_SESSION[EMPRESA] AND activo = 1 AND tipo = 'Compra' AND estado = 'Credito' ORDER BY cuenta ASC";
  $queryCuentasPago = mysql_query($sqlCuentasPago,$link);
  while ($rowCuenta = mysql_fetch_assoc($queryCuentasPago)) {
    $optionCuentas .=  '<div style="float:left; width:100%;" class="div_check_cuentas_pago_FC">
                          <input type="checkbox" value="'.$rowCuenta['cuenta'].'" style="float:left; width:30px;" class="check_cuentas_pago_FC">
                          <div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap;">'.$rowCuenta['cuenta'].' '.$rowCuenta['nombre'].'</div>
                        </div>';
  }

  $date = strtotime(date("Y-m-d"));
  $anio = date("Y", $date);
  $mes  = date("m", $date);
  $dia  = date("d",$date);

  //CALCULAR EL FINAL DEL MES
  $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));
?>
<style>
  .div_check_cuentas_pago_FC:hover{
    color : red;
  }
  .sub-content[data-position="right"]{width: calc(60% - 3px); }
  .sub-content[data-position="left"]{width: 40%; overflow:auto;}
  .content-grilla-filtro { height: calc(80% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 190px;}¿
  .sub-content [data-width="input"]{width: 150px;}
  .selected_check{float: right;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title">FILTRAR POR PROVEEDOR</div>
      <div class="content-grilla-filtro">
        <div class="head">
          <div class="cell" data-col="1"></div>
          <div class="cell" data-col="2">Nit</div>
          <div class="cell" data-col="3">Proveedor</div>
          <div class="cell" data-col="1" data-icon="search" title="Buscar Proveedores" onclick="ventanaBusquedaTercero();"></div>
        </div>
        <div class="body" id="body_grilla_filtro">
        </div>
      </div>
  </div>
  <div class="sub-content" data-position="left">
    <div class="title">EDADES DE VENCIMIENTO</div
    <p>
      <table>
        <tbody>
          <tr>
            <td>
              <input type="checkbox" id="plazo_por_vencer" value="por_vencer" style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Por vencer</div>
            </td>
          </tr>
          <tr>
            <td>
              <input type="checkbox" id="vencido_1_30" value="vencido_1_30" style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Vencido 1 - 30 dias</div>
            </td>
          </tr>
          <tr>
            <td>
              <input type="checkbox" id="vencido_31_60" value="vencido_31_60" style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Vencido 31 - 60 dias</div>
            </td>
          </tr>
          <tr>
            <td>
              <input type="checkbox" id="vencido_61_90" value="vencido_61_90" style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Vencido 61 - 90 dias</div>
            </td>
          </tr>
          <tr>
            <td>
              <input type="checkbox" id="vencido_mas_90" value="vencido_mas_90" style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Vencido mas de 90 dias</div>
            </td>
          </tr>
        </tbody>
      </table>
    </p>
    <div class="title">TIPO DE INFORME</div>
    <p>
      <table>
        <tbody>
          <tr>
            <td>
              <input type="radio" name="tipo_informe" value="detallado"  style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Detallado</div>
            </td>
          </tr>
          <tr>
            <td>
              <input type="radio" name="tipo_informe" value="totalizado_terceros"  style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Totalizado Por Terceros</div>
            </td>
          </tr>
          <tr>
            <td>
              <input type="radio" name="tipo_informe" value="totalizado_edades"  style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Totalizado Por Edades</div>
            </td>
          </tr>
          <tr>
            <td>
              <input type="checkbox" id="imprime_observaciones" value="imprime_observaciones" style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Imprime observaciones</div>
            </td>
          </tr>
        </tbody>
      </table>
    </p>
    <div class="title">FECHAS</div>
    <p>
      <table>
        <tbody>
          <tr>
            <td>
              <input type="radio" name="tipo_fecha_informe" value="corte" onchange="mostrarOcultarDivFechaInicioFacturasPagar(this.value);" style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Con corte a</div>
            </td>
          </tr>
          <tr>
            <td>
              <input type="radio" name="tipo_fecha_informe" value="rango_fechas" onchange="mostrarOcultarDivFechaInicioFacturasPagar(this.value);" style="float:left; width:30px">
            </td>
            <td>
              <div style="float:left;">Rango de Fechas</div>
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <div style="float:left;" id="divFechaInicio">
                Fecha de Inicio:<br>
                <input type="text" id="MyInformeFiltroFechaInicio">
              </div>&nbsp;
              <div style="float:right;">
                Fecha de Corte:<br>
                <input type="text" id="MyInformeFiltroFechaFinal">
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </p>
    <div class="title">ORDENAR</div>
    <p>
      <table>
        <tbody>
          <tr>
            <td>
              <select style="width: 130px;" id="ordenCampo">
                <option value="consecutivo">Consecutivo</option>
                <option value="facturaProveedor">Factura Proveedor</option>
                <option value="fecha">Fecha</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>
              <select style="width: 130px;" id="ordenFlujo">
                <option value="ascendente">Ascendente</option>
                <option value="descendente">Descendente</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </p>
    <div class="title">CUENTAS</div>
    <div style="margin:10px 0 20px 0; overflow:hidden;">
      <div style="float:left; width:100%; margin-bottom: 15px;" class="check_cuentas_pago_FC">
        <input type="checkbox" value="" style="float:left; width:30px;" id="check_todas_cuentas_pago_FC" onclick="check_cuentas_pago_FC(this.checked)" checked />
        <div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap; font-weight:bold;">TODAS LAS CUENTAS</div>
      </div>
      <div style="margin:auto; width:100%; height:80px; max-height:80px;" id="contenedor_check_cuentas_pago_FC"><?php echo $optionCuentas; ?></div>
    </div>
  </div>
</div>
<script>
  check_cuentas_pago_FC(document.getElementById("check_todas_cuentas_pago_FC").checked);

  new Ext.form.DateField({
    format     : "Y-m-d",
    width      : 100,
    id         :"cmpFechaInicio",
    allowBlank : false,
    showToday  : false,
    applyTo    : "MyInformeFiltroFechaInicio",
    editable   : false,
    value      : "'.$fechaInicial.'"
  });

  new Ext.form.DateField({
    format     : "Y-m-d",
    width      : 100,
    allowBlank : false,
    showToday  : false,
    applyTo    : "MyInformeFiltroFechaFinal",
    editable   : false,
    value      : new Date(),
  });

  var elementos = document.getElementsByName("tipo_fecha_informe");

  var elementos_informe = document.getElementsByName("tipo_informe");

  // //SI LAS VARIABLES LOCALSTORAGE TIENEN VALORES, ENTONCES MOSTRAR EN LA CONFIGURACION DE IMPRESION DEL INFORME ESAS VARIABLES

  if ( typeof(localStorage.tipo_informe_facturas_por_pagar)!="undefined" && localStorage.tipo_informe_facturas_por_pagar!="") {
    for(var i=0; i<elementos_informe.length; i++) {
      if (elementos_informe[i].value==localStorage.tipo_informe_facturas_por_pagar) {tipo_informe=elementos_informe[i].checked=true;}
    }
  }
  else{ elementos_informe[0].checked=true; }

  carga_sucursal_guardada();

  //CARGAR SUCURSAL GUARDADA
  function carga_sucursal_guardada(){
    try{
      if(typeof(localStorage.sucursal_facturas_por_pagar) != "undefined" && localStorage.sucursal_facturas_por_pagar != ""){
        setTimeout(function(){
          document.getElementById("filtro_sucursal_facturas_por_pagar").value = localStorage.sucursal_facturas_por_pagar;
        },1000);
      }
    } catch{
      setTimeout('carga_sucursal_guardada()',1500);
    }
  }

  // //SI LAS VARIABLES LOCALSTORAGE TIENEN VALORES, ENTONCES MOSTRAR EN LA CONFIGURACION DE IMPRESION DEL INFORME ESAS VARIABLES

  if ( typeof(localStorage.tipo_fecha_informe_facturas_pagar)!="undefined" && localStorage.tipo_fecha_informe_facturas_pagar!="") {

    for(var i=0; i<elementos.length; i++) {
      if (elementos[i].value==localStorage.tipo_fecha_informe_facturas_pagar) {tipo_fecha_informe=elementos[i].checked=true;}
    }

    document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinal_facturas_pagar;

    if (localStorage.tipo_fecha_informe_facturas_pagar=="rango_fechas") {
      document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicio_facturas_pagar;
    }
  }

  if (localStorage.tipo_fecha_informe_facturas_pagar=="corte") {
    document.getElementById("divFechaInicio").style.display="none";
    elementos[0].checked=true;
  }
  else if (localStorage.tipo_fecha_informe_facturas_pagar=="rango_fechas") {
    document.getElementById("divFechaInicio").style.display="block";
    elementos[1].checked=true;
  }
  else{
    document.getElementById("divFechaInicio").style.display="none";
    elementos[0].checked=true;
  }

  function mostrarOcultarDivFechaInicioFacturasPagar(id){
    if(id == "corte"){
      document.getElementById("divFechaInicio").style.display = "none";
    }
    else if(id == "rango_fechas"){
      document.getElementById("divFechaInicio").style.display = "block";
    }
    else{
      Win_Ventana_configurar_facturas_pagar.close();
    }
  }

  //CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
  for ( i = 0; i < arrayProveedores.length; i++) {
    if (typeof(arrayProveedores[i])!="undefined" && arrayProveedores[i]!="") {

      //CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            var div   = document.createElement("div");
            div.setAttribute("id","fila_cartera_cliente_"+i);
            div.setAttribute("class","filaBoleta");
            document.getElementById("body_grilla_filtro").appendChild(div);

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById("fila_cartera_cliente_"+i).innerHTML=proveedoresConfigurados[i];
    }
  }

  //SELECCIONAMOS LOS CHECKBOX DE LA CONSULTA ANTERIOR

  if (typeof(localStorage.plazo_por_vencer_facturas_pagar)!="undefined" ) {
    if (localStorage.plazo_por_vencer_facturas_pagar!="") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("plazo_por_vencer").checked = JSON.parse(localStorage.plazo_por_vencer_facturas_pagar) ;
    }
    else{ document.getElementById("plazo_por_vencer").checked = true; }
  }
  else{ document.getElementById("plazo_por_vencer").checked = true; }

  if (typeof(localStorage.vencido_1_30_facturas_pagar)!="undefined" ) {
    if (localStorage.vencido_1_30_facturas_pagar!="") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("vencido_1_30").checked = JSON.parse(localStorage.vencido_1_30_facturas_pagar) ;
    }
    else{ document.getElementById("vencido_1_30").checked = true; }
  }
  else{ document.getElementById("vencido_1_30").checked = true; }

  if (typeof(localStorage.vencido_31_60_facturas_pagar)!="undefined" ) {
    if (localStorage.vencido_31_60_facturas_pagar!="") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("vencido_31_60").checked = JSON.parse(localStorage.vencido_31_60_facturas_pagar) ;
    }
    else{ document.getElementById("vencido_31_60").checked = true; }
  }
  else{ document.getElementById("vencido_31_60").checked = true; }

  if (typeof(localStorage.vencido_61_90_facturas_pagar)!="undefined" ) {
    if (localStorage.vencido_61_90_facturas_pagar!="") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("vencido_61_90").checked = JSON.parse(localStorage.vencido_61_90_facturas_pagar) ;
    }
    else{ document.getElementById("vencido_61_90").checked = true; }
  }
  else{ document.getElementById("vencido_61_90").checked = true; }

  if (typeof(localStorage.vencido_mas_90_facturas_pagar)!="undefined" ) {
    if (localStorage.vencido_mas_90_facturas_pagar!="") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("vencido_mas_90").checked = JSON.parse(localStorage.vencido_mas_90_facturas_pagar) ;
    }
    else{ document.getElementById("vencido_mas_90").checked = true; }
  }
  else{ document.getElementById("vencido_mas_90").checked = true; }

  if(typeof(localStorage.ordenCampo_facturas_por_pagar) != "undefined" && localStorage.ordenCampo_facturas_por_pagar != ""){
    document.getElementById("ordenCampo").value = localStorage.ordenCampo_facturas_por_pagar;
  }

  if(typeof(localStorage.ordenFlujo_facturas_por_pagar) != "undefined" && localStorage.ordenFlujo_facturas_por_pagar != ""){
    document.getElementById("ordenFlujo").value = localStorage.ordenFlujo_facturas_por_pagar;
  }
</script>
