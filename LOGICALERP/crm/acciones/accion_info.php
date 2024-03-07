<?php 
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$accion = mysql_result(mysql_query("SELECT accion FROM crm_objetivos_actividades_acciones WHERE id = $id",$link),0,'accion');
	//$accion = str_replace("<br>", "\n", $accion);
?>

<div style="float:left; margin:10px;">
	<?php echo $accion ?>
</div>