<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	switch ($op) {
		case "responseGlobalAjax":
			responseGlobalAjax($condicional,$tabla,$campo,$link);
			break;
	}

	/*-------------------------- FUNCION RETORNA VALOR ---------------------------*/
	/******************************************************************************/
	function responseGlobalAjax($condicional,$tabla,$campo,$link){
		$sql   = "SELECT $campo FROM $tabla WHERE $condicional LIMIT 0,1";
		$query = mysql_query($sql,$link);

		while($row = mysql_fetch_array($query)){
			$campo = $row["$campo"];
			echo $campo;
		}
		if (!$query){ die('no valida'.mysql_error().$sql); }
	}
?>