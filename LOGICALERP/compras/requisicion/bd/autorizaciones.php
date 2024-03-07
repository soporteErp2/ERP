<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$id_empleado = $_SESSION['IDUSUARIO'];

	// CONSULTAR EL AREA DEL DOCUMENTO
	$sql="SELECT id_area_solicitante,estado FROM compras_requisicion WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
	$query=$mysql->query($sql,$mysql->link);
	$id_area = $mysql->result($query,0,'id_area_solicitante');
	$estado  = $mysql->result($query,0,'estado');

	// CONSULTAR LOS REGISTROS DE LAS AUTORIZACIONES
	$sql = "SELECT id,id_empleado,documento_empleado,nombre_empleado,cargo,tipo_autorizacion,orden,fecha
 			    FROM autorizacion_requisicion WHERE activo = 1 AND id_empresa = $id_empresa AND id_requisicion = $id AND id_area = $id_area ORDER BY orden ASC";
	$query = $mysql->query($sql,$mysql->link);
	while($row = $mysql->fetch_array($query)){
		$arrayAutorizaciones[$row['orden']][$row['id_empleado']] = array('id' => $row['id'], 'tipo_autorizacion' => $row['tipo_autorizacion'], 'fecha' => $row['fecha'] );
	}

	// CONSULTAR LOS USUARIOS AUTORIZADORES
	$sql = "SELECT id,id_empleado,documento_empleado,nombre_empleado,cargo,orden
			    FROM costo_autorizadores_requisicion WHERE activo = 1 AND id_empresa = $id_empresa AND id_area = $id_area ORDER BY orden ASC";
	$query = $mysql->query($sql,$mysql->link);

	while($row = $mysql->fetch_array($query)){
		$contentAut = "";
		if($row['id_empleado'] != $id_empleado){
			$arrayEmpleados[] = $row['id_empleado'];
		}

		// VERIFICAMOS SI YA EXISTE ALGUN REGISTRO DE AUTORIZACION
		if(empty($arrayAutorizaciones)){
			if($row['orden'] == 1){
				if($id_empleado == $row['id_empleado']){
					$contentAut = "<select id='tipo_autorizacion_$row[id]' style='border:none;' onchange='autorizar$opcGrillaContable($row[id],$id_area,$row[orden])'>
													<option value=''>Sin Autorizacion</option>
													<option value='Autorizada'>Autorizada</option>
													<option value='Aplazada'>Aplazada</option>
													<option value='Rechazada'>Rechazada</option>
												</select>";

					// CARGAMOS EL VALOR ASIGNADO A EL SELECT
					if($arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion'] != ''){
						$script .= "document.getElementById('tipo_autorizacion_$row[id]').value='".$arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion']."';";
					}
				}
			}
		}
		else{
			if($id_empleado == $row['id_empleado']){
				$contentAut = "<select id='tipo_autorizacion_$row[id]' style='border:none;' onchange='autorizar$opcGrillaContable($row[id],$id_area,$row[orden])'>
												<option value=''>Sin Autorizacion</option>
												<option value='Autorizada'>Autorizada</option>
												<option value='Aplazada'>Aplazada</option>
												<option value='Rechazada'>Rechazada</option>
											</select>";

				// CARGAMOS EL VALOR ASIGNADO A EL SELECT
				if($arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion'] != ''){
					$script .= "document.getElementById('tipo_autorizacion_$row[id]').value='".$arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion']."';";
				}

				foreach($arrayEmpleados as $key){
					if($arrayAutorizaciones[$row['orden'] - 1][$key]['tipo_autorizacion'] != '' && $arrayAutorizaciones[$row['orden'] + 1][$key]['tipo_autorizacion'] != ''){
						$script .= "document.getElementById('tipo_autorizacion_$row[id]').disabled=true;";
					}
					else if($arrayAutorizaciones[$row['orden'] - 1][$key]['tipo_autorizacion'] != '' && $arrayAutorizaciones[$row['orden'] + 1][$key]['tipo_autorizacion'] == NULL){
						$script .= "document.getElementById('tipo_autorizacion_$row[id]').disabled=false;";
					}
					else if($arrayAutorizaciones[$row['orden'] - 1][$key]['tipo_autorizacion'] == NULL && $arrayAutorizaciones[$row['orden'] + 1][$key]['tipo_autorizacion'] == NULL){
						$script .= "document.getElementById('tipo_autorizacion_$row[id]').disabled=true;";
					}
				}

			}
			else{
	      if($arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion'] == "" || $arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion'] == NULL){
	        $src = "";
	      }
	      else{
	        $src = 'img/' . $arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion'] . '.png';
	      }
				$contentAut = "<img style='float:left;' src='$src' ><span style='float:left;padding-left:5px;'>".$arrayAutorizaciones[$row['orden']][$row['id_empleado']]['tipo_autorizacion']."</span>";
			}
		}

		$divBody .=  "<div class='row'>
										<div class='cell' data-col='1'></div>
										<div class='cell' data-col='2'>$row[documento_empleado]</div>
			          		<div class='cell' data-col='3' title='$row[nombre_empleado]' >$row[nombre_empleado]</div>
			          		<div class='cell' data-col='4' title='$row[cargo]' >$row[cargo]</div>
			          		<div class='cell' data-col='5'>$contentAut</div>
			          		<div class='cell' data-col='6'>".$arrayAutorizaciones[$row['orden']][$row['id_empleado']]['fecha']."</div>
									</div>";
	}
?>
<style>
  /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
  .sub-content[data-position="right"]{width: 100%; height: 100%; }
  .sub-content[data-position="left"]{width: 40%; overflow:auto;}
  .content-grilla-filtro { height: 193px;}
  .content-grilla-filtro .cell[data-col="1"]{width: 2px;}
  .content-grilla-filtro .cell[data-col="2"]{width: 70px;}
  .content-grilla-filtro .cell[data-col="3"]{width: 160px;}
  .content-grilla-filtro .cell[data-col="4"]{width: 100px;}
  .content-grilla-filtro .cell[data-col="5"]{width: 100px;}
  .content-grilla-filtro .cell[data-col="6"]{width: 60px;}
  .content-grilla-filtro .mainText{ font-weight: bold; text-align: center;}
  .sub-content [data-width="input"]{width: 120px;}
</style>
<div class="main-content" style="height: 280px;overflow-y: auto;overflow-x: hidden;">
  <div class="sub-content" data-position="right">
    <div class="title">AUTORIZACIONES REQUERIDAS </div>
    <div class="content-grilla-filtro">
      <div class="head">
        <div class="cell" data-col="1"></div>
        <div class="cell" data-col="2">Documento</div>
        <div class="cell" data-col="3">Usuario</div>
        <div class="cell" data-col="4">Cargo</div>
        <div class="cell" data-col="5">Autorizacion</div>
        <div class="cell" data-col="6">Fecha</div>
      </div>
      <div class="body" id="body_grilla_filtro">
      	<?php echo $divBody; ?>
      </div>
    </div>
  </div>
</div>
<div id="loadAut" style='display:none;'></div>
<script>
	<?php echo $script; ?>
</script>
