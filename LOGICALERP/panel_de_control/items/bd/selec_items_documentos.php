<?php
	function cargaOptionTrueFalse($valueBooleano){
		$selectedTrue  ='';
		$selectedFalse ='';
		$comboOption   ='';

		if($valueBooleano=="true"){ $selectedTrue  ='selected="selected"'; }
		else { $selectedFalse  ='selected="selected"'; }

		$comboOption.='<option value="false" '.$selectedFalse.'>No</option>';
		$comboOption.='<option value="true" '.$selectedTrue.'>Si</option>';
		echo $comboOption;
	}

	function cargaOption($tabla,$nombreid,$nombretext,$todos,$select){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true,false) sirve para traer todos los campos de la tabla

		if($todos){ $query = "SELECT * FROM ".$tabla." where activo=1"; }
		else{ $query = "SELECT ".$nombreid.",".$nombretext." FROM ".$tabla." where activo=1"; }

		$result=mysql_query($query);
		$num=mysql_numrows($result);
		echo $query."/".$num."/".$result;
		$i=0;
		while ($i < $num) {
				$id = mysql_result($result,$i,$nombreid);
				$nombre_tipo = mysql_result($result,$i,$nombretext);
				if($select==$id){
					echo "<option value=".$id." selected>";
					echo $nombre_tipo;
					echo "</option>";
				}else{
					echo "<option value=".$id.">";
					echo $nombre_tipo;
					echo "</option>";
				}
				$i++;

		}
	}

?>