<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$MSucursales = user_permisos(1);

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	if($MSucursales == 'false'){ $filtroS = "AND id = $id_sucursal"; }
	if($MSucursales == 'true'){ $filtroS = ''; }

	$SQL = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa = '$id_empresa' $filtroS";
	$consulS = mysql_query($SQL,$link);
?>
<div style="float:left; margin:5px 5px">
    <div style="float:left; width:50px; padding:3px 0 0 0"></div>
    <div id="recibidor_filtro_empresa_<?php echo $opc; ?>" style="float:left; width:150px">
	    <select class="myfield" name="filtro_sucursal_<?php echo $opc; ?>" id="filtro_sucursal_<?php echo $opc; ?>" style="width:100%">
	        <?php
	        	$optionInput = '<optgroup label="Todas las Sucursales">
	        						<option value="global">Saldos Globales</option>
	        						<option value="sucursal">Saldos por Sucursal</option>
	        					</optgroup>
	        					<optgroup label="Sucursales">';
				while($rowS=mysql_fetch_array($consulS)){
					$selected = ($rowS['id'] == $id_sucursal)? 'selected': '';
					$optionInput .= '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
				}
				$optionInput .= '</optgroup>';

				echo $optionInput;
	        ?>
	    </select>
	</div>
</div>