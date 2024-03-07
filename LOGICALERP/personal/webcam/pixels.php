<?php
include("../../../configuracion/conectar.php");
//include("../../../configuracion/define_variables.php");
	
error_reporting(0);
$w  = (int)$_POST['width'];
$h  = (int)$_POST['height'];
$id = $_POST['id'];
$url =  "../../../ARCHIVOS_PROPIOS/temp/".$id.".jpeg";

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
unlink($url);

//Guardando los datos binarios de la imagen
mysql_query("UPDATE empleados SET foto='$tfoto' WHERE id=$id");
$result = mysql_query("select foto from empleados where id=$id");
$image  = mysql_result($result,0,'foto');

echo 'Imagen Guardada!.';


?>
<script>
	document.location = '../foto.php?ID=<?php echo $id ?>';
</script>