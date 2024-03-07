<?php
	
    include("../configuracion/conectar.php");

	$usuario    = mysql_real_escape_string($_POST['usuario']);
	$IdEmpresa  = $_POST['IdEmpresa'];
	$IdSucursal = $_POST['IdSucursal'];
	$sqlEmpresa   = "SELECT username,id_sucursal,id_rol FROM empleados WHERE username = '$usuario' AND (id_empresa = $IdEmpresa OR id_empresa = 0) AND activo = 1 ORDER BY id_empresa LIMIT 0,1";
	$queryEmpresa = mysql_query($sqlEmpresa,$link);
	if(mysql_num_rows($queryEmpresa)){
		$id_sucursal = mysql_result($queryEmpresa,0,"id_sucursal");
		$id_rol      = mysql_result($queryEmpresa,0,"id_rol");

		if($id_sucursal == $IdSucursal || $id_sucursal == 0){ echo 'true'; exit; }

		$sqlPermisoRol   = "SELECT COUNT(id) AS contPermiso FROM empleados_roles_permisos WHERE id_rol='$id_rol' AND id_permiso=1 LIMIT 0,1";
		$queryPermisoRol = mysql_query($sqlPermisoRol,$link);
		$contPermiso     = mysql_result($queryPermisoRol, 0, 'contPermiso');

		if($contPermiso > 0){ echo 'true'; exit; }
		else{ echo 'false{.}sucursal{.}Usuario no tiene Privilegios para entrar en esta sucursal'; }

	}
	else{ echo 'false{.}empresa{.}Usuario no Existe &oacute; no tiene Privilegios para entrar en esta Empresa'; }

?>
