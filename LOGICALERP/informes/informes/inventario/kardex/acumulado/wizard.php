<?php
  include('../../../../../../configuracion/conectar.php');
  include('../../../../../../configuracion/define_variables.php');
  $id_empresa = $_SESSION['EMPRESA'];

  $sql = "SELECT id,codigo,nombre FROM items_familia WHERE activo = 1 AND id_empresa = $id_empresa";
  $query = $mysql->query($sql,$mysql->link);
  while($row = $mysql->fetch_array($query)){
    $option .= '<option value="'.$row['id'].'" >'.$row['codigo'].' - '.$row['nombre'].'</option>';
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
    <div class="title">FILTRAR POR ITEMS</div>
    <div class="content-grilla-filtro" style="height:333px;">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Codigo</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Items" onclick="ventanaBusquedaItemKA();"></div>
      </div>
      <div class="body" id="body_grilla_filtro_item">
      </div>
    </div>
  </div>

  <div class="sub-content" data-position="left">
    <div class="title">FORMATO DE DIGITOS</div>
    <p>
      <table>
        <tbody>
          <tr>
            <td>Separador miles</td>
            <td>
              <select id="separador_miles_kardex_acumulado" data-width="input">
                <option value=".">Punto (.)</option>
                <option value=",">Coma (,)</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Separador decimales</td>
            <td>
              <select id="separador_decimales_kardex_acumulado" data-width="input">
                <option value=",">Coma (,)</option>
                <option value=".">Punto (.)</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </p>
    <div class="title">RANGO DE FECHAS (maximo un mes)</div>
    <p>
      <table>
          <tr>
              <td>Fecha Inicial</td>
              <td><input type="text" id="fecha_inicio_kardex_acumulado"/></td>
          </tr>
          <tr>
              <td>Fecha Final</td>
              <td><input type="text" id="fecha_final_kardex_acumulado"/></td>
          </tr>
      </table>
    </p>

  </div>
</div>
<script>

  new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        id         :"cmpFechaInicio",
        allowBlank : false,
        showToday  : false,
        applyTo    : "fecha_inicio_kardex_acumulado",
        editable   : false,
        value      : localStorage.fecha_inicio_ka || ''
        // value      : "'.$fechaInicial.'"
        // listeners  : { select: function() {   } }
    });

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        allowBlank : false,
        showToday  : false,
        applyTo    : "fecha_final_kardex_acumulado",
        editable   : false,
        value      : localStorage.fecha_final_ka || ''
        // value      : new Date(),
        // listeners  : { select: function() {   } }
    });


  carga_sucursal_bodega_guardada();

  //CARGAR SUCURSAL Y BODEGA GUARDADAS
  function carga_sucursal_bodega_guardada(){
    try{
      if(typeof(localStorage.sucursalKA) != "undefined" && localStorage.sucursalKA != ""){
        setTimeout(function(){
          document.getElementById("filtro_sucursal_kardex_acumulado").value = localStorage.sucursalKA;
        },200);
      }
      if(typeof(localStorage.bodegalKA) != "undefined" && localStorage.bodegalKA != ""){
        setTimeout(function(){
          document.getElementById("filtro_bodega_kardex_acumulado").value = localStorage.bodegalKA;
        },200);
      }
    } catch{
      setTimeout('carga_sucursal_bodega_guardada()',500);
    }
  }

  function cargarItemsGuardados(){
    if (localStorage.itemsKA && JSON.parse(localStorage.itemsKA)) {
				let items = JSON.parse(localStorage.itemsKA);
        let content = "";

        items.map(item  =>{
          content += `<div id="row_item_${id}">
                        <div class="row" id="row_item_${item.id}">
                          <div class="cell" data-col="1"></div>
                          <div class="cell" data-col="2">${item.codigo}</div>
                          <div class="cell" data-col="3" title="${item.nombre}">${item.nombre}</div>
                          <div class="cell" data-col="1" data-icon="delete" onclick="eliminaItem(${item.id})" title="Eliminar Item"></div>
                        </div>
                      </div>`;
        })
        document.getElementById("body_grilla_filtro_item").innerHTML = content
    }
  }

  cargarItemsGuardados();

</script>
