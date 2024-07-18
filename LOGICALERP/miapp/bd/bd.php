<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	switch ($op) {

		case "cambiaPass":
			cambiaPass($id,$pass,$link);
			break;

		case "descargaFormato":
			descargaFormato($id);
			break;

		case "existeFormato":
			existeFormato($id);
			break;

		case "guardarStyleColorUsuario":
			guardarStyleColorUsuario($idEmpleado,$colorFondo,$colorMenu,$link);
			break;
		case 'guardaCorreo':
			guardaCorreo($email,$link);
			break;
	}

	function cambiaPass($id,$pass,$link){
		$pass      = md5($pass);
		$sql       = "UPDATE empleados SET password = '$pass' WHERE id = $id";
		$connectid = mysql_query($sql,$link);

		if($connectid){
			echo 'true{.}'.$id;
			mylog('ACTUALIZA CONTRASEÑA USUARIO -> '.$sql,17,$link);
		}else{
			echo 'false{.}';
		}
	}

	function existeFormato($id){
		$sql 				= "SELECT nombre_formato,ext_formato FROM empresas_formatos  WHERE id=".$id;

		$result				= mysql_query($sql);
		$nombre_formato		= mysql_result($result,$i,"nombre_formato");
		$ext				= mysql_result($result,$i,"ext_formato");

		if($ext!=null || $ext!=""){ echo  'true{.}'.$nombre_formato.".".$ext.'{.}'.$sql; }
		else{ echo  'false{.}No existe.'; }
	}

	function descargaFormato($id){
		$tfoto = null;
		$ext   = null;
		$sql   = "SELECT formato, nombre_formato, ext_formato FROM empresas_formatos WHERE id=$id ";

		$result         = mysql_query($sql);
		$nombre_formato = mysql_result($result,$i,"nombre_formato");
		$ext            = mysql_result($result,$i,"ext_formato");

		if($ext!=null || $ext!=""){
			$logo	= mysql_result($result,$i,"formato");
			$newfile="../../../ARCHIVOS_PROPIOS/temp/".$nombre_formato.".".$ext;
			$file = fopen ($newfile, "w");
			fwrite($file, $logo);
			fclose ($file);
			$path =$nombre_formato.".".$ext;
			echo  'true{.}'.$path;
		}
		else{
			echo  'false{.}No existe.';
		}
	}

	function guardarStyleColorUsuario($idEmpleado,$colorFondo,$colorMenu,$link){
		$sql       = "UPDATE empleados  SET color_menu = '$colorMenu', color_fondo='$colorFondo' WHERE id = $idEmpleado";
		$connectid = mysql_query($sql,$link);
		echo '<script> Win_Panel_Global.close();</script>';
	}

	function guardaCorreo($email,$link){

		$sql       = "UPDATE empleados SET email_empresa = '$email' WHERE id = ".$_SESSION['IDUSUARIO'];
		$connectid = mysql_query($sql,$link);

		if($connectid){
			echo 'true{.}'.$id;
			// mylog('ACTUALIZA CONTRASEÑA USUARIO -> '.$sql,17,$link);
		}else{
			echo 'false{.}';
		}
	}

?>