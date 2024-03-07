<?php

// SCRIPTS PROPI0S DE LA APLICACION ----------------------------------------------------------------------------------------------------
// error_reporting(E_ALL);

include("conectar.php");
$cualver = '?v1.0.0.19-06-2013';
$cualver = str_replace('?v', "", $cualver );
$_SESSION["VERSION"] = $cualver;//.$_SESSION["IDUSUARIO"];
$TITULO_ESCRITORIO =  $_SESSION["APP"].' V.'.$_SESSION["VERSION"].' - "'.$_SESSION['NOMBREEMPRESA'].'"';
if(file_exists("ARCHIVOS_PROPIOS/permisos.php")){
	include("ARCHIVOS_PROPIOS/permisos.php");
}else{
	include("permisos.php");
}

// LLAMAR AL ARCHIVO QUE DEPURA LOS DOCUMENTOS EN BORRADOR
$depurar='true';
include_once("LOGICALERP/notificaciones/depurar_documentos.php");
$dias = 2;
$objeto = new depurarDocumentos($dias,$arrayTables,$link);
$objeto->depurar();

// FIN SCRIPTS PROPIOS DE LA APLICACION -----------------------------------------------------------------------------------------------------

// SCRIPST DEL ESCRITORIO -------------------------------------------------------------------------------------------------------------------

$consul =  mysql_query("SELECT * FROM modulos_erp WHERE inicio = 'true' ORDER BY id",$link);//CONSULTA DE LOS ICONOS DEL MENU DE INICIO
$consulescritorio =  mysql_query("SELECT * FROM modulos_erp WHERE escritorio = 'true' ORDER BY id",$link);//CONSULTA DE LOS ICONOS DEL ESCRITORIO
$consul1 =  mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para las declaraciones css de las capas de los iconos del escritorio y ventanas
$consul2 =  mysql_query("SELECT * FROM modulos_erp WHERE escritorio = 'true' ORDER BY id",$link);//consulta para los iconos del escritorio
$consul3 =  mysql_query("SELECT * FROM modulos_erp WHERE escritorio = 'true' ORDER BY id",$link);//consulta para while de la rutina que carga LIB.JS de los iconos
$consul4 =  mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para generar las ventanas
$consul5 =  mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para while de la rutina que carga LIB.JS de las ventanas
$consul6 =  mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para generar las ventanas a barra de tareas
$consul7 =  mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para while de la rutina que carga LIB.JS de las ventanas de la barra de tareas
$consul8 =  mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para la rutina comun()
$consul9 =  mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para CONTROLAR NUMERO DE VENTANAS ABIERTAS
$consul11 = mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para while de la rutina que carga LIB.JS de los submenus
$consul12 = mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para generar los submenus
$consul13 = mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para CONTROLAR LA MINIMIZADA DE VENTNAS DESDE LOS BOTONES DE LAS BARRAS DE TAREAS
$consul14 = mysql_query("SELECT * FROM modulos_erp ORDER BY id",$link);//consulta para GENERAR LOS NOMBRES DE VENTANAS DE LOS BOTONES DE TAREAS

// FIN SCRIPTS DEL ESCRITORIO ----------------------------------------------------------------------------------------------------------------
?>