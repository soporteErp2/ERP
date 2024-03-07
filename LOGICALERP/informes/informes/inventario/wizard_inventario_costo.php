<?php
  include('../../../../configuracion/conectar.php');
  include('../../../../configuracion/define_variables.php');
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
    <div class="content-grilla-filtro" style="height:186px;">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Codigo</div>
        <div class="cell" data-col="3">Nombre</div>
        <div class="cell" data-col="1" data-icon="search" title="Buscar Items" onclick="ventanaBusquedaItemIC();"></div>
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
              <select id="separador_milesIC" data-width="input">
                <option value=".">Punto (.)</option>
                <option value=",">Coma (,)</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Separador decimales</td>
            <td>
              <select id="separador_decimalesIC" data-width="input">
                <option value=",">Coma (,)</option>
                <option value=".">Punto (.)</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </p>
    <div class="title">ITEM</div>
    <p>
      <select data-width="input" id="filtro_receta" onchange="cargarGrupo()">
        <option value="">Todos</option>
        <option value="sin_receta">Sin receta</option>
        <option value="con_receta">Con receta</option>
      </select>
    </p>
    <div class="title">RECETA</div>
    <p>
      <select data-width="input" id="filtro_receta_detalle" onchange="cargarGrupo()">
        <option value="No">Sin detallar</option>
        <option value="Si">Detallar</option>
      </select>
    </p>
    <div class="title">FILTRO POR FAMILIA</div>
    <p>
      <select data-width="input" id="filtro_familia" onchange="cargarGrupo()">
        <option value="">Seleccione</option>
        <?php echo $option; ?>
      </select>
    </p>
    <div class="title">FILTRO POR GRUPO</div>
    <p>
      <select data-width="input" id="filtro_grupo" onchange="cargarSubGrupo()">
        <option value="">Seleccione</option>
      </select>
    </p>
    <div class="title">FILTRO POR SUBGRUPO</div>
    <p>
      <select data-width="input" id="filtro_subgrupo">
        <option value="">Seleccione</option>
      </select>
    </p>
  </div>
</div>
<script>
  carga_sucursal_bodega_guardada();

  //CARGAR SUCURSAL Y BODEGA GUARDADAS
  function carga_sucursal_bodega_guardada(){
    try{
      if(typeof(localStorage.sucursalIC) != "undefined" && localStorage.sucursalIC != ""){
        setTimeout(function(){
          document.getElementById("filtro_sucursal_inventario_costo").value = localStorage.sucursalIC;
        },200);
      }
      if(typeof(localStorage.bodegalIC) != "undefined" && localStorage.bodegalIC != ""){
        setTimeout(function(){
          document.getElementById("filtro_bodega_inventario_costo").value = localStorage.bodegalIC;
        },200);
      }
    } catch{
      setTimeout('carga_sucursal_bodega_guardada()',500);
    }
  }

  if(localStorage.arrayItemsJSONIC != "" && typeof(localStorage.arrayItemsJSONIC) != "undefined"){
    cargarItemsGuardados();
  }

  if(localStorage.filtro_familiaIC != "" && typeof(localStorage.filtro_familiaIC) != "undefined"){
    document.getElementById('filtro_familia').value = localStorage.filtro_familiaIC;

    cargarGrupo();

    if(localStorage.filtro_grupoIC != "" && typeof(localStorage.filtro_grupoIC) != "undefined"){
      setTimeout(function(){
        document.getElementById('filtro_grupo').value = localStorage.filtro_grupoIC;
      },3600);
    }
  }

  function cargarItemsGuardados(){
    Ext.get('body_grilla_filtro_item').load({
      url     : '../informes/informes/inventario/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc            : 'cargarItemsGuardados',
                  arrayItemsJSON : localStorage.arrayItemsJSONIC
                }
    });
  }

  function cargarGrupo(){
    var id_familia = document.getElementById("filtro_familia").value;

    Ext.get('filtro_grupo').load({
      url     : '../informes/informes/inventario/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc        : 'cargarGrupo',
                  id_familia : id_familia
                }
    });

    Ext.get('filtro_subgrupo').load({
      url     : '../informes/informes/inventario/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc : 'reiniciarSelect'
                }
    });
  }

  function cargarSubGrupo(){
    var id_familia = document.getElementById("filtro_familia").value;
    var id_grupo = document.getElementById("filtro_grupo").value;

    Ext.get('filtro_subgrupo').load({
      url     : '../informes/informes/inventario/bd.php',
      scripts : true,
      nocache : true,
      params  : {
                  opc        : 'cargarSubGrupo',
                  id_familia : id_familia,
                  id_grupo   : id_grupo
                }
    });
  }
</script>
