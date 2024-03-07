<?php

	$body = "";
	for($id_empresa=1; $id_empresa<=54; $id_empresa++){
		if($id_empresa == 1){ $id_grupo=30; }
		else{ $id_grupo = $id_empresa - 16; }

		$body .= "('OC_USUARIO', 'Usuario Elaboracion', '$id_grupo', 'Ordenes de Compra', 'usuario', 'compras_ordenes', NULL, '1', NULL, '$id_empresa'),
					('OC_CC_USUARIO', 'Identificacion Usuario', '$id_grupo', 'Ordenes de Compra', 'documento_usuario', 'compras_ordenes', NULL, '1', NULL, '$id_empresa'),";

		if($id_empresa == 1){ $id_empresa = 46; }
	}

	echo"INSERT INTO `variables` (`nombre`, `detalle`, `id_grupo`, `grupo`, `campo`, `tabla`, `funcion`, `automatica`, `where`, `id_empresa`)
		VALUES $body";


?>


