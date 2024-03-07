<?php
	///////////////////////////// OPCIONES DE ZONA ////////////////////////////////////////////////////////////////////////////////

	function cargaOptionZonas($empresa,$sucursal,$select){ 
		$query = 	"SELECT * FROM configuracion_zonas
					WHERE id_empresa='$empresa' AND id_sucursal='$sucursal'";
		
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		echo $query."/".$num."/".$result;
		$i=0;
		while ($i < $num) {
				 $id = mysql_result($result,$i,"id");
				 $nombre = mysql_result($result,$i,"nombre");
				 if($select==$id){
				 echo "<option value=".$id." selected>";
				 echo $nombre;
				 echo "</option>";
				 }else{
				 echo "<option value=".$id.">";
				 echo $nombre;
				 echo "</option>";
				 }
				 $i++;
		}
	}	
	
	///////////////////////////// OPCIONES DE TIPO ////////////////////////////////////////////////////////////////////////////////

	function cargaOptionTipos($empresa,$sucursal,$select){
		$query = 	"SELECT * FROM rental_tipos_espacios
					WHERE id_empresa='$empresa' AND id_sucursal='$sucursal'";
		
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		echo $query."/".$num."/".$result;
		$i=0;
		while ($i < $num) {
				 $id = mysql_result($result,$i,"id");
				 $nombre = mysql_result($result,$i,"nombre_tipo");
				 if($select==$id){
				 echo "<option value='".$id."' selected>";
				 echo $nombre;
				 echo "</option>";
				 }else{
				 echo "<option value='".$id."'>";
				 echo $nombre;
				 echo "</option>";
				 }
				 $i++;
		}
	}
	
	///////////////////////////// OPCIONES DE SUBTIPO ////////////////////////////////////////////////////////////////////////////////

	function cargaOptionSubtipos($tipo,$select){
		$query = 	"SELECT * FROM rental_subtipos_espacios
					WHERE tipo='$tipo'";
		
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		echo $query."/".$num."/".$result;
		$i=0;
		while ($i < $num) {
				 $id = mysql_result($result,$i,"id");
				 $nombre = mysql_result($result,$i,"nombre_subtipo");
				 if($select==$id){
				 echo "<option value=".$id." selected>";
				 echo $nombre;
				 echo "</option>";
				 }else{
				 echo "<option value=".$id.">";
				 echo $nombre;
				 echo "</option>";
				 }
				 $i++;
		}
	}
	///////////////////////////// OPCIONES DE TIPO DE DOCUMENTO EN DOCUMENTOS ////////////////////////////////////////////////////////////////////////////////

	function cargaOptionsTipoDoc($select){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true,false) sirve para traer todos los campos de la tabla
			
			$query = "SELECT * FROM configuracion_documentos_tipo";
					
			$result=mysql_query($query);
			$num=mysql_numrows($result);
			//echo $query."/".$num."/".$result;
			$i=0;
			while ($i < $num) {
					 $id = mysql_result($result,$i,"id");
					 $nombre = mysql_result($result,$i,"nombre");
					 if($select==$id){
					 echo "<option value=".$id." selected>";
					 echo $nombre;
					 echo "</option>";
					 }else{
					 echo "<option value=".$id.">";
					 echo $nombre;
					 echo "</option>";
					 }
					 $i++;
			}
		}
	
	function cargaOptionGrupos($tabla,$nombreid,$nombretext,$todos,$select){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true o false) sirve para traer todos los campos de la tabla
		
		if($todos){
			$query = "SELECT * FROM ".$tabla." WHERE id_empresa=".$_SESSION['EMPRESA'];
		}else{
			$query = "SELECT ".$nombreid.",".$nombretext." FROM ".$tabla." WHERE id_empresa=".$_SESSION['EMPRESA'];
		}
		
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		//echo $query."/".$num."/".$result;
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
	
	function cargaDatosVariable($id){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true,false) sirve para traer todos los campos de la tabla
				
		$query = "SELECT * FROM variables WHERE id='".$id."'";	
		global $tabla,$grupo,$nombre_variable,$detalle_variable,$campo,$funcion ;
		
		$result				= mysql_query($query);
		$tabla				= mysql_result($result,0,"tabla");
		$grupo 				= mysql_result($result,0,"id_grupo");
		$nombre_variable	= mysql_result($result,0,"nombre");
		$detalle_variable 	= mysql_result($result,0,"detalle");
		$campo 				= mysql_result($result,0,"campo");
		$funcion 			= mysql_result($result,0,"funcion");
		
	}
	
	function cargaOptionsTablas($select){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true,false) sirve para traer todos los campos de la tabla
		
		$query = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='logicalsofterp'";
				
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		//echo $query."/".$num."/".$result;
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
	
	function cargaOptionsCampo($tabla,$select){ /// Carga los options para un select html creandolos de una tabla en la base de datos, todos(true,false) sirve para traer todos los campos de la tabla
		
		$query = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='logicalsofterp' AND TABLE_NAME='".$tabla."'";
				
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		//echo $query."/".$num."/".$result;
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
	
	function cargaTextoDocumento($id){ 
		$query = "SELECT texto FROM configuracion_documentos where id=".$id;
		//echo $query;
		$result=mysql_query($query);
		
		global $texto;
		$texto= mysql_result($result,$i,"texto");
	}
?>