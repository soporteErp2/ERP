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

		case 'optionCiudad':
			optionCiudad($id_retencion,$id_departamento,$id_empresa,$link);
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

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{ echo'<script>document.getElementById("'.$idInput.'_niif").value = "'.$cuentaNiif.'";</script>'; }

		echo'<img src="img/refresh.png" onclick="sincronizaCuentaRetencionEnNiif(\''.$idInput.'\')"/>'.$sqlNiif;
	}

	function optionCiudad($id_retencion,$id_departamento,$id_empresa,$link){
		$id_ciudadBD = 0;

		if($id_retencion > 0){
			$sqlCiudadBd = "SELECT id_ciudad FROM retenciones WHERE id='$id_retencion' LIMIT 0,1";
			$id_ciudadBD = mysql_result(mysql_query($sqlCiudadBd,$link), 0, 'id_ciudad');
		}

		$comboCiudad = '<select class="myfield" name="retencion_id_ciudad" id="retencion_id_ciudad" style="width:150px">
							<option value="0">Seleccione...</option>';


		$sqlCiudades   = "SELECT id,ciudad FROM ubicacion_ciudad WHERE id_departamento='$id_departamento' AND id_departamento<>'' AND activo=1";
		$queryCiudades = mysql_query($sqlCiudades,$link);
		while ($row = mysql_fetch_array($queryCiudades)) {
			$selected    = ($id_ciudadBD == $row['id'])? 'selected': '';
			$comboCiudad .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['ciudad'].'</option>';
		}

		echo $comboCiudad .= '</select>';
	}

	function validarCuenta($nombreTabla,$cuenta,$id_empresa,$link){
		$sqlCuenta   = "SELECT COUNT(id) AS contCuenta FROM $nombreTabla WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta LIKE '$cuenta%'";
		$queryCuenta = mysql_query($sqlCuenta,$link);

		echo mysql_result($queryCuenta, 0, 'contCuenta');
	}

?>

