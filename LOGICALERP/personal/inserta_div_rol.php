<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	$counts = mysql_query("SELECT * FROM empleados_roles WHERE activo = 1",$link);
	$count = mysql_num_rows($counts);
	$consul = mysql_query("SELECT * FROM empleados_roles WHERE id = $elid",$link);
	$id  = mysql_result($consul,0,'id');
	$nombre  = mysql_result($consul,0,'nombre');
?>
        <div class="my_grilla_celdas2" id="item_<?php echo $id; ?>" style="float:left; min-width:450px; width:100%" >
        	<div ondblclick="Editar_Rol('<?php echo $id; ?>','<?php echo $nombre; ?>')">
                <div class="my_grilla_columna" style="float:left; width:30px;"><?php echo $count;  ?></div>
                <div class="my_grilla_celdas" style="float:left; width:60px;"><?php echo str_pad($id, 3, "0", STR_PAD_LEFT); ?></div>
                <div class="my_grilla_celdas" style="float:left; width:300px;"><?php echo $nombre; ?></div>
            </div>
        </div>
        <div id="Recibidor_Celda_Rol<?php echo $count; ?>"></div>