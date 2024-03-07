<?php
		include("../../configuracion/conectar.php");
		include("../../configuracion/define_variables.php");
		
		$result = mysql_query("SELECT qrcode FROM empleados WHERE documento='$documento'");
		$image  = mysql_result($result,0,'qrcode');
		
		/*if($image == ''){
			$fp = fopen("images/foto0.png", "rb");
			$tfoto = fread($fp, filesize("images/foto0.png"));
			fclose($fp);
			$image = $tfoto;	
		}*/
		
		header("Content-Type:image/png");
		echo $image;
?>
