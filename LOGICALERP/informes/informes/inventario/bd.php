<?php
  include('../../../../configuracion/conectar.php');
  include('../../../../configuracion/define_variables.php');

  $id_empresa = $_SESSION['EMPRESA'];

  switch($opc){
  	case 'cargarGrupo':
  		cargarGrupo($id_familia,$id_empresa,$mysql);
  		break;

    case 'cargarSubGrupo':
  		cargarSubGrupo($id_familia,$id_grupo,$id_empresa,$mysql);
  		break;

    case 'reiniciarSelect':
  		reiniciarSelect();
  		break;

    case 'cargarItemsGuardados':
      cargarItemsGuardados($arrayItemsJSON,$id_empresa,$mysql);
      break;
  }

	function cargarGrupo($id_familia,$id_empresa,$mysql){
		$sql = "SELECT id,codigo,nombre FROM items_familia_grupo WHERE activo = 1 AND id_empresa = $id_empresa AND id_familia = $id_familia";
	  $query = $mysql->query($sql,$mysql->link);

    $option = '<option value="">Seleccione</option>';
    while($row = $mysql->fetch_array($query)){
	    $option .= '<option value="'.$row['id'].'" >'.$row['codigo'].' - '.$row['nombre'].'</option>';
    }

    echo $option;
	}

  function cargarSubGrupo($id_familia,$id_grupo,$id_empresa,$mysql){
		$sql = "SELECT id,codigo,nombre FROM items_familia_grupo_subgrupo WHERE activo = 1 AND id_empresa = $id_empresa AND id_familia = $id_familia AND id_grupo = $id_grupo";
	  $query = $mysql->query($sql,$mysql->link);

    $option = '<option value="">Seleccione</option>';
	  while($row = $mysql->fetch_array($query)){
	    $option .= '<option value="'.$row['id'].'" >'.$row['codigo'].' - '.$row['nombre'].'</option>';
    }

    echo $option;
	}

  function reiniciarSelect(){
    $option = '<option value="">Seleccione</option>';
    echo $option;
  }

  function cargarItemsGuardados($arrayItemsJSON,$id_empresa,$mysql){
    $arrayItemsJSON = json_decode($arrayItemsJSON);

    if(!empty($arrayItemsJSON)){
      foreach($arrayItemsJSON as $indice => $id_item){
        $items .= ($items == "")? "id = '$id_item'" : " OR id = '$id_item'";
      }
      $whereItems .= " AND ($items)";

      $sql = "SELECT id,codigo,nombre_equipo FROM items WHERE activo = 1 AND id_empresa = $id_empresa $whereItems";
      $query = $mysql->query($sql,$mysql->link);

      while($row = $mysql->fetch_array($query)){
        $grillaItems .=  "<div class='row' id='row_item_$row[id]'>
                            <div class='cell' data-col='1'></div>
                            <div class='cell' data-col='2'>$row[codigo]</div>
                            <div class='cell' data-col='3' title='$row[nombre_equipo]'>$row[nombre_equipo]</div>
                            <div class='cell' data-col='1' data-icon='delete' onclick='eliminaItem($row[id])' title='Eliminar Item'></div>
                          </div>
                          <script>
                            arrayItemsIC[$row[id]] = $row[id];
                          </script>";
      }

      echo $grillaItems;
    }
  }
