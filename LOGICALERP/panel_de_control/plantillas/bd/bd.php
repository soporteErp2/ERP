<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

	switch ($op) {

		case 'sincronizaCuentaNiif':
			sincronizaCuentaNiif($estado,$cuenta,$idInput,$id_empresa,$link);
			break;
	}


	function sincronizaCuentaNiif($estado,$cuenta,$idInput,$id_empresa,$link){
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

		$idInputNiif = str_replace('_puc', '_niif', $idInput);
		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{ echo'<script>document.getElementById("'.$idInputNiif.'").value = "'.$cuentaNiif.'";</script>'; }

		echo'<img src="img/refresh.png" onclick="sincronizaCuentaEnNiifPlantilla(\''.$estado.'\',\''.$idInput.'\')"/>';
	}
?>

