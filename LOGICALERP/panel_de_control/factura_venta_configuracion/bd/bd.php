<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	switch ($op) {
		case 'guardarPiePagina':
			guardarPiePagina($cabecera_pie_pagina,$contenido_pie_pagina,$link);
			break;
	}

	function guardarPiePagina($cabecera_pie_pagina,$contenido_pie_pagina,$link){
		//CONSULTAMOS SI YA EXISTE UNA CONFIGURACION, PARA SABER SI SE VA A ACTUALIZA R O SE VA A INSERTAR UN NUEVO REGISTRO
		$sqlConsul   = "SELECT id FROM ventas_facturas_configuracion_impresion WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
		$queryConsul = mysql_query($sqlConsul,$link);

		$resul=mysql_result($queryConsul,0,'id');

		if ($resul!='') {
			//ACTUALIZAR
			$sql   = "UPDATE ventas_facturas_configuracion_impresion SET cabecera_pie_pagina='$cabecera_pie_pagina',contenido_pie_pagina='$contenido_pie_pagina' WHERE id_empresa=".$_SESSION['EMPRESA'];
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizo la configuracion de pie de pagina de la factura\nSi el problema persiste comunmiquese con el administrador del sistema");</script>';
			}
			else{ echo'<script>Win_Panel_Global.close();</script>'; }
		}
		else{
			//INSERTAR
			$sql   = "INSERT INTO ventas_facturas_configuracion_impresion (cabecera_pie_pagina,contenido_pie_pagina,id_empresa) VALUES ('$cabecera_pie_pagina','$contenido_pie_pagina',".$_SESSION['EMPRESA'].") ";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se inserto la configuracion de pie de pagina de la factura\nSi el problema persiste comunmiquese con el administrador del sistema");</script>';
			}
			else{ echo'<script>Win_Panel_Global.close();</script>'; }
		}
	}

 ?>