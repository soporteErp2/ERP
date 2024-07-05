<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$idEmpresa = $_SESSION['EMPRESA'];

	switch ($op) {
		case 'guardarConsecutivosDocumentos':
			guardarConsecutivosDocumentos($jsonConsecutivos,$idEmpresa,$link);
		break;
		
	}

	function guardarConsecutivosDocumentos($jsonConsecutivos,$idEmpresa,$link){

		$arrayConsecutivos = explode(',',$jsonConsecutivos);
		$contError         = 0;
		
		foreach ($arrayConsecutivos as $indice => $value) {
			$arrayvalues    = explode(':', $value);
			$newConsecutivo = $arrayvalues[1];
			$documento      = $arrayvalues[0];

			$updateConsecutivo = "UPDATE configuracion_consecutivos_documentos SET consecutivo = '$newConsecutivo' WHERE activo=1 AND id_empresa='$idEmpresa' AND documento='$documento'";
			$queryConsecutivo = mysql_query($updateConsecutivo,$link);

			if(!$queryConsecutivo){ $contError++; $msgError .= '\n'.$documento; }
		}

		if($contError > 0){
			$msgError = ($contError > 1)? 'Error,\nLos siguientes consecutivos no se han almacenado:\n'.$msgError : 'Error,\nEl siguiente consecutivo no se han almacenado:\n'.$msgError;
			echo '<script>alert("'.$msgError.'");</script>'; exit;
		}

		echo '<script>Win_Panel_Global.close();</script>';
	}


?>