<?php
  include('../../../../configuracion/conectar.php');
  include('../../../../configuracion/define_variables.php');

  $id_empresa  = $_SESSION['EMPRESA'];

  $sqlCargos = "SELECT id,nombre FROM empleados_cargos WHERE activo = 1 AND id_empresa = $id_empresa";
  $queryCargos = $mysql->query($sqlCargos,$mysql->link);

  if($queryCargos){
    while($row = $mysql->fetch_array($queryCargos)){
      $cargos .= "<option value='$row[id]'>$row[nombre]</option>";
    }
  }

  $sqlRoles = "SELECT id,nombre FROM empleados_roles WHERE activo = 1 AND id_empresa = $id_empresa";
  $queryRoles = $mysql->query($sqlRoles,$mysql->link);

  if($queryRoles){
    while($row = $mysql->fetch_array($queryRoles)){
      $roles .= "<option value='$row[id]'>$row[nombre]</option>";
    }
  }
?>
<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position="right"]{width: calc(35% - 2px); }
  .sub-content[data-position="left"]{width: 65%; overflow:hidden;}
  .content-grilla-filtro { height: calc(50% - 45px);}
  .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
  .sub-content [data-width="input"]{width: 150px;}
</style>
<div class="main-content">
  <div class="sub-content" data-position="right">
    <div class="title" style="margin-bottom:15px;">FILTRO ACCESO SISTEMA</div>
    <table style="margin:auto;">
      <tr>
        <td style="width:30%">Seleccione</td>
        <td style="width:70%">
          <select id="acceso">
            <option value="global">Todos</option>
            <option value="con_acceso">Con Acceso</option>
            <option value="sin_acceso">Sin Acceso</option>
          </select>
        </td>
      </tr>
    </table>
  </div>
  <div class="sub-content" data-position="left">
    <div class="title" style="margin-bottom:15px;">FILTRO CARGO</div>
    <table style="margin:auto;">
      <tr>
        <td style="width:30%">Seleccione</td>
        <td style="width:70%">
          <select id="cargo">
            <option value="global">Todos</option>
            <?php echo $cargos; ?>
          </select>
        </td>
      </tr>
    </table>
    <div class="title" style="margin-bottom:15px;margin-top:15px;">FILTRO ROL</div>
    <table style="margin:auto;">
      <tr>
        <td style="width:30%">Seleccione</td>
        <td style="width:70%">
          <select id="rol">
            <option value="global">Todos</option>
            <?php echo $roles; ?>
          </select>
        </td>
      </tr>
    </table>
  </div>
</div>
<script>
  Ext.onReady(function(){
    carga_sucursal_guardada();

    //CARGAR SUCURSAL GUARDADA
    function carga_sucursal_guardada(){
      try{
        if(typeof(localStorage.sucursalE) != "undefined" && localStorage.sucursalE != ""){
          setTimeout(function(){
            document.getElementById("filtro_sucursal_empleados").value = localStorage.sucursalE;
          },600);
        }
      } catch{
        setTimeout('carga_sucursal_guardada()',1500);
      }
    }

    //CARGAR FILTRO DE ACCESO AL SISTEMA
    if(typeof(localStorage.accesoE) != "undefined"){
      if(localStorage.accesoE != ""){
        document.getElementById("acceso").value = localStorage.accesoE;
      }
    }

    //CARGAR FILTRO CARGO
    if(typeof(localStorage.cargoE) != "undefined"){
      if(localStorage.cargoE != ""){
        document.getElementById("cargo").value = localStorage.cargoE;
      }
    }

    //CARGAR FILTRO ROL
    if(typeof(localStorage.rolE) != "undefined"){
      if(localStorage.rolE != ""){
        document.getElementById("rol").value = localStorage.rolE;
      }
    }
  });
</script>
