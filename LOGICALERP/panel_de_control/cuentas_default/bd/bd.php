<?php

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

	switch ($op) {
		case 'saveCuenta':
			saveCuenta($typeResponse,$puc,$estado,$texto,$nombre,$idItemCuenta,$id_empresa,$link);
			break;

		case 'updateCuentaNiif':
			updateCuentaNiif($texto,$cuenta,$id_empresa,$link);
			break;
	}


	function saveCuenta($typeResponse,$puc,$estado,$texto,$nombre,$idItemCuenta,$id_empresa,$link){

		if($typeResponse == 'niif'){ 	//NIIF
			$textoSql  = str_replace('_niif', '', $texto);
			$sqlCuenta = "UPDATE asientos_niif_default SET cuenta='$puc' WHERE descripcion='$textoSql' AND id_empresa='$id_empresa'";
			$texto .="_niif";
		}
		else{ $sqlCuenta = "UPDATE asientos_colgaap_default SET cuenta='$puc' WHERE descripcion='$texto' AND id_empresa='$id_empresa'"; }							//COLGAAP

		$queryCuenta = mysql_query($sqlCuenta,$link);
		if(!$queryCuenta){ echo '<script>alert("Error,\nNo se ha establecido la conexion con el servidor si el problema persiste comuniquese con el administrador del sistema")</script>'; return; }

		echo "<script>
				document.getElementById('cuenta_$texto').innerHTML = '$puc - $nombre';
				document.getElementById('cuenta_$texto').title = '$puc - $nombre';
				document.getElementById('cuenta_$texto').dataset.cuenta = '$puc';
			</script>";

	}

	function updateCuentaNiif($texto,$cuenta,$id_empresa,$link){

		echo$sqlNiif   = "SELECT COUNT(PN.id) AS cont_niif, PN.descripcion, P.cuenta_niif
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
			$sqlUpdateNiif   = "UPDATE asientos_niif_default SET cuenta='$cuentaNiif' WHERE id_empresa='$id_empresa' AND descripcion = '$texto'";
			$queryUpdateNiif = mysql_query($sqlUpdateNiif,$link);

			echo'<script>
					if(document.getElementById("cuenta_'.$texto.'_niif")){
						document.getElementById("cuenta_'.$texto.'_niif").innerHTML = "'.$cuentaNiif.' - '.$descripcionNiif.' ";
						document.getElementById("cuenta_'.$texto.'_niif").title = "'.$cuentaNiif.' - '.$descripcionNiif.' ";
					}
				</script>';
		}

	}

?>
