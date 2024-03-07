<?php
  include('../../../../configuracion/conectar.php');
  include('../../../../configuracion/define_variables.php');

  $id_empresa = $_SESSION['EMPRESA'];

  $sqlCuentasPago  = "SELECT cuenta,nombre
                      FROM configuracion_cuentas_pago
                      WHERE id_empresa = $id_empresa AND activo = 1 AND tipo = 'Venta' AND estado = 'Credito'
                      ORDER BY cuenta ASC";

  $queryCuentasPago = mysql_query($sqlCuentasPago,$link);
  while ($rowCuenta = mysql_fetch_assoc($queryCuentasPago)){
    $optionCuentas .=  '<div style="float:left; width:100%;" class="div_check_cuentas_pago_FV">
                          <input type="checkbox" value="'.$rowCuenta['cuenta'].'" style="float:left; width:30px;" class="check_cuentas_pago_FV">
                          <div style="float:left; text-overflow:ellipsis; overflow:hidden; width:260px; white-space: nowrap;">'.$rowCuenta['cuenta'].' '.$rowCuenta['nombre'].'</div>
                        </div>';
  }
?>
<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position="right"]{width: calc(60% - 3px); }
  .sub-content[data-position="left"]{width: 40%; overflow:auto;}
  .sub-content[data-width="input"]{width: 150px;}
  .content-grilla-filtro {height: calc(50% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
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

    <div class="title">CUENTAS</div>
    <div style="margin:10px 0 20px 0; overflow:hidden;">
      <div style="float:left; width:100%; margin-bottom: 15px;" class="check_cuentas_pago_FV">
        <input type="checkbox" value="" style="float:left; width:30px;" id="check_todas_cuentas_pago_FV" onclick="check_cuentas_pago_FV(this.checked)" checked />
        <div style="float:left; text-overflow:ellipsis; overflow:hidden; width:160px; white-space: nowrap; font-weight:bold;">TODAS LAS CUENTAS</div>
      </div>
      <div style="margin:auto; width:100%; height:80px; max-height:80px;" id="contenedor_check_cuentas_pago_FV">
        <?php echo $optionCuentas; ?>
      </div>
    </div>
  </div>

  <div class="sub-content" data-position="left">
    <div class="title">EDADES DE VENCIMIENTO</div>
    <table style="margin: 0 auto;">
      <tr>
        <td>
          <input type="checkbox" id="plazo_por_vencer">
        </td>
        <td>Por vencer</td>
      </tr>
      <tr>
        <td>
          <input type="checkbox" id="vencido_1_30">
        </td>
        <td>Vencido de 1 a 30 dias</td>
      </tr>
      <tr>
        <td>
          <input type="checkbox" id="vencido_31_60">
        </td>
        <td>Vencido de 31 a 60 dias</td>
      </tr>
      <tr>
        <td>
          <input type="checkbox" id="vencido_61_90">
        </td>
        <td>Vencido de 61 a 90 dias</td>
      </tr>
      <tr>
        <td>
          <input type="checkbox" id="vencido_mas_90">
        </td>
        <td>Mas de 90 dias</td>
      </tr>
    </table>

    <div class="title">ESTRUCTURA DEL INFORME</div>
    <table style="margin: 0 auto;">
      <tr>
        <td>
          <input type="radio" name="tipo_informe" value="detallado"  style="float:left; width:30px">
        </td>
        <td>Detallado</td>
      </tr>
      <tr>
        <td>
          <input type="radio" name="tipo_informe" value="totalizado_edades"  style="float:left; width:30px">
        </td>
        <td>Totalizado por Edades</td>
      </tr>
    </table>

    <div class="title">FECHA DE CORTE</div>
    <table style="margin: 0 auto;">
      <tr>
        <td>
          <input type="radio" name="tipo_fecha_informe" value="corte" onchange="mostrarOcultarDivFechaInicio(this.value);" style="float:left; width:30px">
          Con corte a:
        </td>
      </tr>
      <tr>
        <td>
          <input type="radio" name="tipo_fecha_informe" value="rango_fechas" onchange="mostrarOcultarDivFechaInicio(this.value);" style="float:left; width:30px">
          Rango de Fechas:
        </td>
      </tr>
    </table>
    <table style="margin: 0 auto;">
      <tr>
        <td id="divFechaInicio">
          Fecha Inicial:<br>
          <input type="text" id="MyInformeFiltroFechaInicio">
        </td>
        <td>
          Fecha Corte:<br>
          <input type="text" id="MyInformeFiltroFechaFinal">
        </td>
      </tr>
    </table>
  </div>
</div>
<script>
  var rows                = ''
  , estructura_informeEC	= document.getElementsByName('tipo_informe')
  , fecha_corteEC 				= document.getElementsByName('tipo_fecha_informe')

  //============================= FECHAS CON EXT =============================//
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

  //================================ SUCURSAL ================================//
  carga_sucursal_guardada();

  function carga_sucursal_guardada(){
    try{
      if(typeof(localStorage.sucursalEC) != "undefined" && localStorage.sucursalEC != ""){
        setTimeout(function(){
          document.getElementById("filtro_sucursal_estado_cuenta").value = localStorage.sucursalEC;
        },600);
      }    
    } catch{
      setTimeout('carga_sucursal_guardada()',1500);
    }
  }

  //============================ DATOS DEL CLIENTE ===========================//
  if(typeof(localStorage.clienteEC) != "undefined"){
    if(localStorage.clienteEC != ""){
      document.getElementById('id_cliente').value             = localStorage.clienteEC;
      document.getElementById('documento_cliente').innerHTML  = localStorage.documento_clienteEC;
      document.getElementById('nombre_cliente').innerHTML     = localStorage.nombre_clienteEC;
    }
  }

  //========================= ESTRUCTURA DEL INFORME =========================//
  if(typeof(localStorage.tipo_informeEC) != "undefined" && localStorage.tipo_informeEC != ""){
    if(localStorage.tipo_informeEC == 'detallado'){
      estructura_informeEC[0].checked = true;
    } else if(localStorage.tipo_informeEC == 'totalizado_edades'){
      estructura_informeEC[1].checked = true;
    } else{
      estructura_informeEC[0].checked = true;
    }
  }
  

  //============================= FECHA DE CORTE =============================//
  if(typeof(localStorage.tipo_fecha_informeEC) != "undefined" && localStorage.tipo_fecha_informeEC != ""){

    for(i = 0; i < fecha_corteEC.length; i++){
      if(fecha_corteEC[i].value == localStorage.tipo_fecha_informeEC){
        tipo_fecha_informeEC = fecha_corteEC[i].checked = true;
      }
    }

    document.getElementById("MyInformeFiltroFechaFinal").value = localStorage.MyInformeFiltroFechaFinalEC;

    if(localStorage.tipo_fecha_informeEC == "corte"){
			document.getElementById("MyInformeFiltroFechaInicio").value = '';
		}
		else if(localStorage.tipo_fecha_informeEC == "rango_fechas"){
      document.getElementById("MyInformeFiltroFechaInicio").value = (localStorage.MyInformeFiltroFechaInicioEC != "" || localStorage.MyInformeFiltroFechaInicioEC != "undefined")? localStorage.MyInformeFiltroFechaInicioEC : "";
    }
  }
  
  if(localStorage.tipo_fecha_informeEC == "corte"){
    document.getElementById("divFechaInicio").style.display = "none";
    fecha_corteEC[0].checked = true;
  }
  else if(localStorage.tipo_fecha_informeEC == "rango_fechas"){
    document.getElementById("divFechaInicio").style.display = "block";
    fecha_corteEC[1].checked = true;
  }
  else{
    document.getElementById("divFechaInicio").style.display = "none";
    fecha_corteEC[0].checked = true;
  }

  //========================== EDADES DE VENCIMIENTO =========================//
  if (typeof(localStorage.plazo_por_vencer) != "undefined"){
    if (localStorage.plazo_por_vencer != "") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("plazo_por_vencer").checked = JSON.parse(localStorage.plazo_por_vencer) ;
    }
    else{ document.getElementById("plazo_por_vencer").checked = true; }
  }
  else{ document.getElementById("plazo_por_vencer").checked = true; }

  if (typeof(localStorage.vencido_1_30) != "undefined"){
    if (localStorage.vencido_1_30 != "") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("vencido_1_30").checked = JSON.parse(localStorage.vencido_1_30) ;
    }
    else{ document.getElementById("vencido_1_30").checked = true; }
  }
  else{ document.getElementById("vencido_1_30").checked = true; }

  if (typeof(localStorage.vencido_31_60) != "undefined"){
    if (localStorage.vencido_31_60 != "") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("vencido_31_60").checked = JSON.parse(localStorage.vencido_31_60) ;
    }
    else{ document.getElementById("vencido_31_60").checked = true; }
  }
  else{ document.getElementById("vencido_31_60").checked = true; }

  if (typeof(localStorage.vencido_61_90) != "undefined"){
    if (localStorage.vencido_61_90 != "") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("vencido_61_90").checked = JSON.parse(localStorage.vencido_61_90) ;
    }
    else{ document.getElementById("vencido_61_90").checked = true; }
  }
  else{ document.getElementById("vencido_61_90").checked = true; }

  if (typeof(localStorage.vencido_mas_90) != "undefined"){
    if (localStorage.vencido_mas_90 != "") {
      //COMO LOCALSTORAGE ES STRING, SE PARSEA PARA BOLEANO
      document.getElementById("vencido_mas_90").checked = JSON.parse(localStorage.vencido_mas_90) ;
    }
    else{ document.getElementById("vencido_mas_90").checked = true; }
  }
  else{ document.getElementById("vencido_mas_90").checked = true; }

  //====================== MOSTRAR/OCULTAR FECHA INICIO ======================//
  function mostrarOcultarDivFechaInicio(id){
    if(id == "corte"){ document.getElementById("divFechaInicio").style.display = "none"; }
    else if(id == "rango_fechas"){ document.getElementById("divFechaInicio").style.display = "block"; }
    else{ Win_Ventana_configurar_cartera_edades.close(); }
  }

  //========================= MOSTRAR/OCULTAR CUENTAS ========================//
  check_cuentas_pago_FV(document.getElementById("check_todas_cuentas_pago_FV").checked);
</script>
