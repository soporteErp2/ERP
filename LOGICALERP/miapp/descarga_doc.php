<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include("../../configuracion/mimetype.php");

	$sql = "SELECT formato, nombre_formato, ext_formato FROM empresas_formatos WHERE id=$id ";
	$consul = mysql_query($sql,$link);
	$formato = mysql_result($consul,0,'formato');
	$nombre_formato = mysql_result($consul,0,'nombre_formato');	
	$type = mysql_result($consul,0,'ext_formato');	
	$archivo=$nombre_formato.".".$type;
	$archivo = str_replace(" ", "_", $archivo); 
	$type = mime_content_type($archivo);
	header( "Content-Disposition: attachment; filename=$archivo");
	header("Content-Type: $type");
	echo $formato;
?>