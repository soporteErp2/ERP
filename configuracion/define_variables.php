<?php
// ARRAY CON LOS CARACTERES A REEMPLAZAR
// $caracteres_buscar_define_variables_include= array("'");
// $caracteres_reemplazar_define_variables_include= array("",'');

foreach($_POST as $nombre_campo => $valor){
   // $valor=str_replace($caracteres_buscar_define_variables_include, "", $valor);
   $asignacion = "\$" . $nombre_campo . "='" .utf8_decode($valor). "';";
   eval($asignacion);
   //echo '[POST] -> '.$asignacion.'<br />';
}
foreach($_GET as $nombre_campo => $valor){
   // $valor=str_replace($caracteres_buscar_define_variables_include, "", $valor);
   $asignacion = "\$" . $nombre_campo . "='" .utf8_decode($valor). "';";
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