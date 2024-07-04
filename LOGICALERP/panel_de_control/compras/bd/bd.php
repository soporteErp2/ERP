<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($op) {
		case 'guardarDiasOrdenes':
			guardarDiasOrdenes($dias,$opc,$id_empresa,$link);
		break;

		case 'guardarConsecutivosDocumentos':
			guardarConsecutivosDocumentos($jsonData,$filtro_sucursal,$id_empresa,$link);
		break;

	}

	function guardarDiasOrdenes($dias,$opc,$id_empresa,$link){

		//consultamos para determinar si ya se habia insertado un valor, si se inserto entonces se actualiza y si no se inserta
		$sql   = "SELECT id FROM configuracion_vencimiento_documentos WHERE activo=1 AND documento='$opc' AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($sql,$link);
		$id    = mysql_result($query,0,'id');

		if ($id>0) {
			$sql   = "UPDATE configuracion_vencimiento_documentos SET dias_vencimiento = '$dias' WHERE activo=1  AND documento='$opc' AND id_empresa='$id_empresa'";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizo la informacion");</script>
				<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!" >';
			}
			else{
				echo ' <img src="../../../../temas/clasico/images/BotonesTabs/ok16.png" style="margin-top: 3px;" title="Guardado" >';
			}
		}
		else
		{
			$sql   = "INSERT INTO configuracion_vencimiento_documentos (dias_vencimiento,documento,id_empresa) VALUES ('$dias','$opc','$id_empresa')";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizo la informacion");</script>
				<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!" >';
			}
			else{
				echo ' <img src="../../../../temas/clasico/images/BotonesTabs/ok16.png" style="margin-top: 3px;" title="Guardado" >';
			}
		}
	}

	function guardarConsecutivosDocumentos($jsonData,$filtro_sucursal,$id_empresa,$link){

        $array = json_decode($jsonData,true);
		$errores = 0;

		foreach ($array as $documento => $arrayValue ) {

		   $consecutivo=$arrayValue["value"];
		   $digitos=$arrayValue["digitos"];


           $updateConsecutivo = "UPDATE configuracion_consecutivos_documentos
                        SET consecutivo = '$consecutivo',
                            digitos = '$digitos'
                        WHERE activo=1
                            AND id_empresa='$id_empresa'
                            AND id_sucursal='$filtro_sucursal'
                            AND documento='$documento'
                            AND modulo='compra'";




		   $queryUpdate  = mysql_query($updateConsecutivo,$link);
		   if(!$queryUpdate){ $errores++; $msgError .= '\n'.$documento; }

	   }

	    if($contError > 0){
			$msgError = ($contError > 1)? 'Error,\nLos siguientes consecutivos no se han almacenado:\n'.$msgError : 'Error,\nEl siguiente consecutivo no se han almacenado:\n'.$msgError;
			echo '<script>alert("'.$msgError.'");</script>'; exit;
		}

		echo '<script>Win_Panel_Sucursal.close();</script>';


	}


?>