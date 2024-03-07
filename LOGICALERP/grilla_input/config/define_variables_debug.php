<?php
foreach($_POST as $nombre_campo => $valor){ 
   $asignacion = "\$" . $nombre_campo . "='" .utf8_decode($valor). "';"; 
   eval($asignacion); 
   echo '[POST] -> '.$asignacion.'<br />';
}
echo "<br /><br />";
foreach($_GET as $nombre_campo => $valor){ 
   $asignacion = "\$" . $nombre_campo . "='" .utf8_decode($valor). "';"; 
   eval($asignacion);
   echo '[GET] -> '.$asignacion.'<br />'; 
}
echo "<br /><br />";
/*
foreach($_SERVER as $nombre_campo => $valor){ 
   $asignacion = "\$" . $nombre_campo . "='" .utf8_decode($valor). "';"; 
   eval($asignacion); 
}
echo "<br /><br />";
foreach($_COOKIE as $nombre_campo => $valor){ 
   $asignacion = "\$" . $nombre_campo . "='" .utf8_decode($valor). "';"; 
   eval($asignacion); 
}
echo "<br /><br />";
*/
foreach($_SESSION as $nombre_campo => $valor){ 
	echo "SESSION['".$nombre_campo."'] = '".utf8_decode($valor)."'<br />";
}
echo "<br /><br />";
foreach($_SERVER as $nombre_campo => $valor){ 
	echo "SERVER['".$nombre_campo."'] = '".utf8_decode($valor)."'<br />";
}


?>
