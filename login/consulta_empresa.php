<?php

	//=======================// CONEXION A LA BD DEL CLIENTE //=======================//
	//********************************************************************************//
	$nit_empresa = $_POST['empresa'];
    require('../configuracion/conectar_global.php');
    $sql = "SELECT nombre,id FROM empresas WHERE documento = '$nit_empresa' AND activo = 1";
    $query = mysql_query($sql,$link);
    if(mysql_num_rows($query)){			//SI LA EMPRESA SI EXISTE

		$IdEmpresa = mysql_result($query,0,'id');
		$NombreEmpresa = mysql_result($query,0,'nombre');
		
		$consul2 = mysql_query("SELECT id,nombre FROM empresas_sucursales WHERE id_empresa = '$IdEmpresa' AND activo = 1",$link);
		if(mysql_num_rows($consul2)==1){// SI ES UNA SOLA SUCURSAL

			echo 'true{.}'.$NombreEmpresa.'{.}'.$IdEmpresa.'{.}field{.}'.mysql_result($consul2,0,"id").'{.}'.mysql_result($consul2,0,"nombre");
		}
		else{//SI SON VARIAS SUCURSALES

			$IDS;
			$NOMBRES;
			while($row = mysql_fetch_array($consul2)){
				$IDS .= $row['id'].",";
				$NOMBRES .= $row['nombre'].",";
			}
			$IDS     = substr($IDS, 0, -1);				//Elimina la Ultima coma
			$NOMBRES = substr($NOMBRES, 0, -1);			//Elimina la Ultima coma

			echo 'true{.}'.$NombreEmpresa.'{.}'.$IdEmpresa.'{.}combo{.}'.$IDS.'{.}'.$NOMBRES;
		}
	}
	else{ echo 'false{.}Identificacion de Empresa no Existe{.}false{.}false'; }

?>
