<?php 

include("../../../../configuracion/conectar.php");
include("../../../../configuracion/define_variables.php");

// //eliminamos el archivo
$eliminaArchivo="../../../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_".$_SESSION['EMPRESA']."/logos/".$archivo;
if (!unlink($eliminaArchivo)) {
	echo 'false';
}else{
	$sql="SELECT nombre,ext FROM configuracion_imagenes_documentos WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
	$query=mysql_query($sql,$link);
	$nombre=	mysql_result($query,0,'nombre');
	$ext=		mysql_result($query,0,'ext');
	if ($query) {
		echo $nombre.'.'.$ext;
	}else{
		echo "false";
	}
		
}

 ?>