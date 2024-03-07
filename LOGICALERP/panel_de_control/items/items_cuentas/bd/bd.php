<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	include("../../../../configuracion/naturaleza_cuentas.php");

	$id_empresa         = $_SESSION['EMPRESA'];
	$id_usuario         = $_SESSION['IDUSUARIO'];
	$nombre_usuario     = $_SESSION['NOMBREFUNCIONARIO'];

	switch ($op) {
		case 'saveCuenta':
			saveCuenta($typeResponse,$puc,$estado,$descripcion,$idItems,$nombre,$idItemCuenta,$arrayNaturaleza,$id_empresa,$link);
			break;

		case 'updateCuentaNiif':
			updateCuentaNiif($idItems,$texto,$cuenta,$arrayNaturaleza,$id_empresa,$link);
			break;
	}

	function saveCuenta($typeResponse,$puc,$estado,$descripcion,$idItems,$nombre,$idItemCuenta,$arrayNaturaleza,$id_empresa,$link){
		$naturaleza_cuenta  = $arrayNaturaleza[$_SESSION['PAIS']]['items_'.$estado.'_'.$descripcion]['naturaleza'];
		$prefijo_naturaleza = $arrayNaturaleza[$_SESSION['PAIS']]['items_'.$estado.'_'.$descripcion]['prefijo'];

		$idInput     = '';
		$nombreArray = 'arrayIdItemsCuenta';

		if($typeResponse == 'niif'){	//NIIF
			$idInput     = '_niif';
			$nombreArray = 'arrayIdItemsCuentaNiif';

			if($idItemCuenta > 0){ $sqlCuenta   = "UPDATE items_cuentas_niif SET puc='$puc',tipo='$naturaleza_cuenta' WHERE activo=1 AND id_items='$idItems' AND descripcion='$descripcion' AND estado='$estado' AND id_empresa='$id_empresa'"; }
			else{ $sqlCuenta   = "INSERT INTO items_cuentas_niif (descripcion,id_items,puc,estado,tipo,id_empresa) VALUES('$descripcion','$idItems','$puc','$estado','$naturaleza_cuenta','$id_empresa')";  }
		}
		else{							//COLGAAP
			if($idItemCuenta > 0){ $sqlCuenta   = "UPDATE items_cuentas SET puc='$puc',tipo='$naturaleza_cuenta' WHERE activo=1 AND id_items='$idItems' AND descripcion='$descripcion' AND estado='$estado' AND id_empresa='$id_empresa'"; }
			else{ $sqlCuenta   = "INSERT INTO items_cuentas (descripcion,id_items,puc,estado,tipo,id_empresa) VALUES('$descripcion','$idItems','$puc','$estado','$naturaleza_cuenta','$id_empresa')";  }
		}

		$queryCuenta = mysql_query($sqlCuenta,$link);
		if(!$queryCuenta){ echo '<script>alert("Error,\nNo se ha establecido la conexion con el servidor si el problema persiste comuniquese con el administrador del sistema")</script>'; return; }

		echo '<script>
					  document.getElementById("cuenta_'.$estado.'_'.$descripcion.$idInput.'").innerHTML = "'.$nombre.'";
						document.getElementById("'.$estado.'_'.$descripcion.$idInput.'").value = "'.$puc.'";
						document.getElementById("btnTipoAsiento_'.$estado.'_'.$descripcion.'").innerHTML = "'.$prefijo_naturaleza.'";
					</script>';

		if(is_nan($idItemCuenta) || $idItemCuenta==0 || 2==2){
			$sql_last_id = "SELECT LAST_INSERT_ID()";
			$lastId      = mysql_result(mysql_query($sql_last_id,$link),0,0);

			echo'<script>'.$nombreArray.'["'.$estado.'_'.$descripcion.'"] = "'.$lastId.'";</script>';
		}

	}

	function updateCuentaNiif($idItems,$texto,$cuenta,$arrayNaturaleza,$id_empresa,$link){
		$naturaleza_cuenta  = $arrayNaturaleza[$_SESSION['PAIS']]['items_'.$texto.'_'.$descripcion.'niif']['naturaleza'];
		$prefijo_naturaleza = $arrayNaturaleza[$_SESSION['PAIS']]['items_'.$texto.'_'.$descripcion.'niif']['prefijo'];

		$sqlNiif = "SELECT COUNT(PN.id) AS cont_niif, PN.descripcion, P.cuenta_niif
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

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{
			$sqlUpdateNiif   = "UPDATE items_cuentas_niif SET puc='$cuentaNiif',tipo='$naturaleza_cuenta' WHERE id_empresa='$id_empresa' AND CONCAT(estado,'_',descripcion)='$texto' AND id_items='$idItems'";
			$queryUpdateNiif = mysql_query($sqlUpdateNiif,$link);
			echo '<script>
							if(document.getElementById("'.$texto.'_niif")){
								document.getElementById("'.$texto.'_niif").value = "'.$cuentaNiif.'";
								document.getElementById("cuenta_'.$texto.'_niif").innerHTML = "'.$descripcionNiif.'";
								document.getElementById("btnTipoAsiento_'.$texto.'").innerHTML = "'.$prefijo_naturaleza.'";
							}
						</script>';
		}

		echo'<div class="btnItemsCuentas" style="margin:0;" onclick="homologarCuentaEnNiif(\''.$texto.'\')"><img src="items/images/refresh.png" /></div>';
	}

?>
