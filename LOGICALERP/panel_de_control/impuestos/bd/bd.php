<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

	switch ($opc) {
		case 'sincronizaPucImpuestoNiif':
			sincronizaPucImpuestoNiif($idInput,$cuenta,$id_empresa,$link);
			break;

		case 'validarCuenta':
			validarCuenta($nombreTabla,$cuenta,$id_empresa,$link);
			break;
	}


	function sincronizaPucImpuestoNiif($idInput,$cuenta,$id_empresa,$link){
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

		$inputNiif = $idInput.'_niif';

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{ echo'<script>document.getElementById("'.$inputNiif.'").value = "'.$cuentaNiif.'";</script>'; }

		echo'<img src="img/refresh.png" onclick="sincronizaCuentaImpuestoEnNiif(\''.$idInput.'\')"/>'.$sqlNiif;
	}

	function validarCuenta($nombreTabla,$cuenta,$id_empresa,$link){
		$sqlCuenta   = "SELECT COUNT(id) AS contCuenta FROM $nombreTabla WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta LIKE '$cuenta%'";
		$queryCuenta = mysql_query($sqlCuenta,$link);

		echo mysql_result($queryCuenta, 0, 'contCuenta');
	}

?>

