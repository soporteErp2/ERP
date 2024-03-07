<?php
		include("../../../configuracion/conectar.php");
		include("../../../configuracion/define_variables.php");
		$result = mysql_query("SELECT foto FROM empleados WHERE id=$ID");
		$image  = mysql_result($result,0,'foto');
		
		if($image == ''){
			$fp = fopen("../images/foto0.png", "rb");
			$tfoto = fread($fp, filesize("../images/foto0.png"));
			fclose($fp);
			$image = $tfoto;	
		}
		
		header("Content-Type:image/jpeg");
		echo $image;
?>
