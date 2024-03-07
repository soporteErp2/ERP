<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$result    = mysql_query("SELECT * FROM terceros_documentos WHERE id=$id");
	$documento = mysql_result($result,0,'documento');
	$type      = mysql_result($result,0,'document_type');
	$nombre    = mysql_result($result,0,'tipo_documento_nombre');
	$ext       = mysql_result($result,0,'ext');

	header("Content-Type:".$type);
	if($ext != 'jpg' && $ext != 'jpeg' && $ext != 'png' && $ext != 'gif' && $ext != 'bmp' && $ext != 'pdf'){
		header('Content-Disposition: attachment; filename="'.$nombre.'.'.$ext.'"');
	}else{
		header('Content-Disposition: inline; filename="'.$nombre.'.'.$ext.'"');
	}
	print $documento;
?>
