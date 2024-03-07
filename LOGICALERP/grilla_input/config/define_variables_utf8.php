<?php
foreach($_POST as $nombre_campo => $valor){ 
   $asignacion = "\$" . $nombre_campo . "='" .$valor. "';"; 
   eval($asignacion); 
   //echo '[POST] -> '.$asignacion.'<br />';
}
foreach($_GET as $nombre_campo => $valor){ 
   $asignacion = "\$" . $nombre_campo . "='" .$valor. "';"; 
   eval($asignacion);
   //echo '[GET] -> '.$asignacion.'<br />'; 
}
/*foreach($_SERVER as $nombre_campo => $valor){ 
   $asignacion = "\$" . $nombre_campo . "='" .utf8_decode($valor). "';"; 
   eval($asignacion); 
}
foreach($_COOKIE as $nombre_campo => $valor){ 
   $asignacion = "\$" . $nombre_campo . "='" .utf8_decode($valor). "';"; 
   eval($asignacion); 
}*/


?>