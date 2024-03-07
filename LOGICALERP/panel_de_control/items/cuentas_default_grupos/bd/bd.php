<?php

	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

	switch ($op) {
		case 'saveCuenta':
			saveCuenta($typeResponse,$puc,$estado,$texto,$nombre,$idItemCuenta,$id_empresa,$id_grupo,$link);
			break;

		case 'updateCuentaNiif':
			updateCuentaNiif($id_grupo,$estado,$texto,$cuenta,$id_empresa,$link);
			break;
	}


	function saveCuenta($typeResponse,$puc,$estado,$texto,$nombre,$idItemCuenta,$id_empresa,$id_grupo,$link){

		// NIIF
		if($typeResponse == 'niif'){
			$input = '_niif';
			$textoSql = str_replace('_niif', '', $texto);
			// CONSULTAR SI EXISTE LA CUENTA EN LA CONFIGURACION
			$sql   = "SELECT id FROM asientos_niif_default_grupos WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo=$id_grupo AND descripcion='$textoSql'";
			$query = mysql_query($sql,$link);
			$id    = mysql_result($query,0,'id');

			// SI EXISTE SE ACTUALIZA
			if ($id>0) {
				$sqlCuenta   = "UPDATE asientos_niif_default_grupos SET cuenta='$puc',estado='$estado' WHERE descripcion='$textoSql' AND id_empresa='$id_empresa' AND id_grupo=$id_grupo";
				$queryCuenta = mysql_query($sqlCuenta,$link);
				if(!$queryCuenta){ echo '<script>alert("Error,\nNo se actualizo la cuenta parael grupo\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema")</script>'; return; }
			}
			// SI NO EXISTE SE INSERTA
			else{
				$sql   = "INSERT INTO asientos_niif_default_grupos (id_grupo,descripcion,estado,cuenta,id_empresa) VALUES('$id_grupo','$textoSql','$estado','$puc','$id_empresa')";
				$query = mysql_query($sql,$link);
				if (!$query) {
					echo '<script>alert("Error!\no se inserto la configracion de cuentas para el grupo\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
				}
			}
		}
		// COLGAAP
		else{
			// CONSULTAR SI EXISTE LA CUENTA EN LA CONFIGURACION
			$sql   = "SELECT id FROM asientos_colgaap_default_grupos WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo=$id_grupo AND descripcion='$texto'";
			$query = mysql_query($sql,$link);
			$id    = mysql_result($query,0,'id');

			// SI EXISTE SE ACTUALIZA
			if ($id>0) {
				$sqlCuenta   = "UPDATE asientos_colgaap_default_grupos SET cuenta='$puc',estado='$estado' WHERE descripcion='$texto' AND id_empresa='$id_empresa' AND id_grupo='$id_grupo'";
				$queryCuenta = mysql_query($sqlCuenta,$link);
				if(!$queryCuenta){ echo '<script>alert("Error,\nNo se actualizo la cuenta parael grupo\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema")</script>'; return; }
			}
			// SI NO EXISTE SE INSERTA
			else{
				$sql   = "INSERT INTO asientos_colgaap_default_grupos (id_grupo,descripcion,estado,cuenta,id_empresa) VALUES('$id_grupo','$texto','$estado','$puc','$id_empresa')";
				$query = mysql_query($sql,$link);
				if (!$query) {
					echo '<script>alert("Error!\no se inserto la configracion de cuentas para el grupo\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
				}
			}
		}

		echo"<script>
				MyLoading2('off');
				document.getElementById('$texto$input').innerHTML      = '$puc - $nombre';
				document.getElementById('$texto$input').title          = '$puc - $nombre';
				document.getElementById('$texto$input').dataset.cuenta = '$puc';
			</script>";
	}

	function updateCuentaNiif($id_grupo,$estado,$texto,$cuenta,$id_empresa,$link){

		$sqlNiif   = "SELECT COUNT(PN.id) AS cont_niif, PN.descripcion, P.cuenta_niif
						FROM puc AS P, puc_niif AS PN
						WHERE P.activo=1
							AND P.cuenta='$cuenta'
							AND P.id_empresa='$id_empresa'
							AND PN.activo=1
							AND PN.id_empresa=P.id_empresa
							AND PN.cuenta=P.cuenta_niif
							LIMIT 0,1";
		$queryNiif = mysql_query($sqlNiif,$link);

		$contNiif        = mysql_result($queryNiif,0,'cont_niif');
		$cuentaNiif      = mysql_result($queryNiif,0,'cuenta_niif');
		$descripcionNiif = mysql_result($queryNiif,0,'descripcion');

		if($contNiif == 0){
			echo"<script>
					MyLoading2('off'{texto:'No existe una cuenta niif asociada a la cuenta colgaap No. $cuenta',icono:'fail',duracion:3000});
				</script>";
			}
		else{
			// CONSULTAR SI EXISTE LA CUENTA EN LA CONFIGURACION
			$sql   = "SELECT id FROM asientos_niif_default_grupos WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo=$id_grupo AND descripcion='$texto'";
			$query = mysql_query($sql,$link);
			$id    = mysql_result($query,0,'id');
			// SI EXISTE SE ACTUALIZA
			if ($id>0) {
				$sqlCuenta   = "UPDATE asientos_niif_default_grupos SET cuenta='$cuentaNiif',estado='$estado' WHERE descripcion='$texto' AND id_grupo=$id_grupo  AND id_empresa='$id_empresa'";
				$queryCuenta = mysql_query($sqlCuenta,$link);
				if(!$queryCuenta){ echo '<script>alert("Error,\nNo se actualizo la cuenta parael grupo\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema")</script>'; return; }
			}
			// SI NO EXISTE SE INSERTA
			else{
				$sql   = "INSERT INTO asientos_niif_default_grupos (id_grupo,descripcion,estado,cuenta,id_empresa) VALUES('$id_grupo','$texto','$estado','$cuentaNiif','$id_empresa')";
				$query = mysql_query($sql,$link);
				if (!$query) {
					echo '<script>alert("Error!\no se inserto la configracion de cuentas para el grupo\nIntentelo de nuevo, si el problema continua comuniquese con el administrador del sistema");</script>';
					return;
				}
			}
			// $sqlUpdateNiif   = "UPDATE asientos_niif_default_grupos SET cuenta='$cuentaNiif' WHERE id_empresa='$id_empresa' AND descripcion = '$texto'";
			// $queryUpdateNiif = mysql_query($sqlUpdateNiif,$link);

			echo"<script>
					if(document.getElementById('".$texto."_niif')){
						document.getElementById('".$texto."_niif').innerHTML      = '$cuentaNiif - $descripcionNiif';
						document.getElementById('".$texto."_niif').title          = '$cuentaNiif - $descripcionNiif';
					}
					MyLoading2('off');
				</script>";
		}

		// echo'<div class="btnItemsCuentas" style="margin:0;" onclick="homologarCuentaEnNiif(\''.$texto.'\')"><img src="items/images/refresh.png" /></div>';
	}

?>
