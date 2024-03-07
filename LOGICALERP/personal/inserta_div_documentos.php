<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$SQL1 = "SELECT * FROM empleados_documentos WHERE id_empleado = $id_empleado";
	$SQL2 = "SELECT * FROM empleados_documentos WHERE id = $elid";

	$counts = mysql_query($SQL1,$link);
	$count = mysql_num_rows($counts);
	$consul = mysql_query($SQL2,$link);
	$id                    = mysql_result($consul,0,'id');
	$tipo_documento_nombre = mysql_result($consul,0,'tipo_documento_nombre');
	$fecha_creacion        = mysql_result($consul,0,'fecha_creacion');
	$randomico_documento   = mysql_result($consul,0,'randomico_documento');
	$nombre_documento      = mysql_result($consul,0,'nombre_documento');
	$ext                   = mysql_result($consul,0,'ext');

?>
        <div class="my_grilla_celdas2" id="item_documentos_<?php echo $id; ?>" style="float:left; min-width:580px; width:100%" >
            <div ondblclick="">
                <div class="my_grilla_columna_insert" style="float:left; width:30px;"><?php echo $count;  ?></div>
                <div class="my_grilla_celdas" style="float:left; width:300px;" id="tipo_documento_nombre_<?php echo $id; ?>"><?php echo $tipo_documento_nombre; ?></div>
                <div class="my_grilla_celdas" style="float:left; width:230px;"><?php echo fecha_larga_hora($fecha_creacion); ?></div>
                <div class="my_grilla_celda" style="float:left; width:45px; text-align:center "><img src="../../../temas/clasico/images/BotonesTabs/buscar16.png" width="16" height="16" onClick="ver_documentos_empleado('<?php echo $id; ?>','<?php echo $randomico_documento; ?>','<?php echo $nombre_documento; ?>','<?php echo $ext; ?>')" style="margin:2px 0 0 0; cursor:pointer"></div>
                <div class="my_grilla_celda" style="float:left; width:45px; text-align:center "><img src="images/eliminar.png" width="16" height="16" onClick="EliminarDocumento('<?php echo $id; ?>','<?php echo $id_empleado; ?>','<?php  echo $tipo_documento_nombre; ?>','<?php echo $randomico_documento.'_'.$id.'.'.$ext; ?>')" style="margin:2px 0 0 0; cursor:pointer"></div>
            </div>
        </div>
        <div id="Recibidor_Celda_documentos<?php echo $count; ?>"></div>