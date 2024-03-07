<?php

include("../inc/conectar.php");
	
error_reporting(0);
$w  = (int)$_POST['width'];
$h  = (int)$_POST['height'];
$id = $_POST['id'];
$url =  "../fotos/".$id.".jpeg";

$img = imagecreatetruecolor($w, $h);

imagefill($img, 0, 0, 0xFFFFFF);

$rows = 0;
$cols = 0;

//Reconstruyendo la Imagen
for($rows = 0; $rows < $h; $rows++)
{
	$c_row = explode(",", $_POST['px' . $rows]);
	for($cols = 0; $cols < $w; $cols++)
	{
		$value = $c_row[$cols];
		if($value != "")
		{
			$hex = $value;
			while(strlen($hex) < 6)
			{
				$hex = "0" . $hex;
			}
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));

			$test = imagecolorallocate($img, $r, $g, $b);

			imagesetpixel($img, $cols, $rows, $test);
		}
	}
}

//Almacenando la Imagen en el disco duro
imagejpeg($img, $url, 100);

//Abriendo el Archivo imagen.
$fp = fopen($url, "rb");
$tfoto = fread($fp, filesize($url));
$tfoto = addslashes($tfoto);
fclose($fp);

//Guardando los datos binarios de la imagen
mysql_query("UPDATE datos SET foto='$tfoto' WHERE id=$id");
$result = mysql_query("select foto from datos where id=$id");
$image  = mysql_result($result,0,'foto');

//Imprimiendo la imagen 

/*
header("Content-Type:image/jpeg");
echo $image;*/
?>
	<style type="text/css">body {background-color: #DFE8F6;}</style>
    <div style="float:left; width:100px;  height:130px; margin:2px 0 0 7px; border:1px solid #8DB2E3">
    	<img src="../foto_generador.php?ID=<?php echo $id ?>" width="100" height="130" />
	</div>
    <div style="float:left; cursor:pointer; width:120px; height:47px; margin: 49px 0 0 37px; background-image:url(../images/BOTON3.png)" onclick="document.location='index.php?ID=<?php echo $id ?>'"></div>