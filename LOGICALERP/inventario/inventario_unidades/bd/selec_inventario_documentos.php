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

		if($todos){
			$query = "SELECT * FROM ".$tabla." where activo=1";
		}else{
			$query = "SELECT ".$nombreid.",".$nombretext." FROM ".$tabla." where activo=1";
		}

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
/*
	/*function cargaEmpleado($id){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true,false) sirve para traer todos los campos de la tabla

		$query = "SELECT * FROM empleados where id=".$id;
		$result=mysql_query($query);
		$i=0;

		global $activo,$tipo_id1,$username1,$id1,$nombre1,$nombre2,$apellido1,$apellido2,$rol,$cargo,$mail1,$mail2,$nacimiento1,$direccion1,$telefono1,$telefono2,$celular1,$celular2,$contrato,$rolvalor,$eps,$arp,$salario,$vendedor,$tecnico_operativo,$conductor;

		$activo            = mysql_result($result,$i,"activo");
		$tipo_id1          = mysql_result($result,$i,"tipo_documento");
		$id1               = mysql_result($result,$i,"documento");
		$nombre1           = mysql_result($result,$i,"nombre1");
		$nombre2           = mysql_result($result,$i,"nombre2");
		$apellido1         = mysql_result($result,$i,"apellido1");
		$apellido2         = mysql_result($result,$i,"apellido2");
		$rol               = mysql_result($result,$i,"id_rol");
		$cargo             = mysql_result($result,$i,"id_cargo");
		$username1         = mysql_result($result,$i,"username");
		$mail1             = mysql_result($result,$i,"email_empresa");
		$mail2             = mysql_result($result,$i,"email_personal");
		$nacimiento1       = mysql_result($result,$i,"nacimiento");
		$direccion1        = mysql_result($result,$i,"direccion");
		$telefono1         = mysql_result($result,$i,"telefono1");
		$telefono2         = mysql_result($result,$i,"telefono2");
		$celular1          = mysql_result($result,$i,"celular_empresa");
		$celular2          = mysql_result($result,$i,"celular1");
		$arp               = mysql_result($result,$i,"arp");
		$eps               = mysql_result($result,$i,"eps");
		$contrato          = mysql_result($result,$i,"id_contrato");
		$salario           = mysql_result($result,$i,"salario");
		$vendedor          = mysql_result($result,$i,"vendedor");
		$tecnico_operativo = mysql_result($result,$i,"tecnico_operativo");
		$conductor         = mysql_result($result,$i,"conductor");

		$rolvalor			 = mysql_result(mysql_query("SELECT valor FROM empleados_roles WHERE id = $rol"),0,'valor');
	}

	function cargaTextoContrato($id){
		$query = "SELECT contrato FROM empleados_contratos where id=".$id;
		//echo $query;
		$result=mysql_query($query);

		global $texto;
		$texto= mysql_result($result,$i,"contrato");
	}*/
?>