<?php
	include("../../../../configuracion/conectar.php");
	include('../../../../configuracion/define_variables.php');

	switch ($op) {
		case "actualTextoDocumento":
			actualTextoDocumento($id_documento,$texto,$link);
			break;
	}

	function actualTextoDocumento($id_documento,$texto,$link){
		$sql   = "UPDATE configuracion_documentos_erp SET texto='$texto' WHERE id='$id_documento'";
		$query = mysql_query($sql,$link);

		if($query){ echo 'true{.}'.$id.'{.}'.$sql; mylog('ACTUALIZA TEXTO DOCUMENTO -> '.$sql,4,$link); }
		else{ echo 'false{.}'.$sql; }
	}

?>