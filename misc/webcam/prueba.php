<?
	include("../inc/conectar.php");
	$result = mysql_query("select foto from datos where id=5");
	$image  = mysql_result($result,0,'foto');
	echo base64_encode  ($image);
		
	//imagejpeg($image, "imagen_prueba.jpeg", 100);
?>
