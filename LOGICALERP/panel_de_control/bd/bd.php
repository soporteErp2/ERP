<?php
include("../../../configuracion/conectar.php");
include('../../../configuracion/define_variables.php');

switch ($op) {

///////		FORMATOS DESCARGABLES		//////////////////////////////////////////////////

	case "visualFormato":
		visualFormato($id);
		break;

	case "borraFormato":
		borraFormato($id);
		break;

	case "existeFormato":
		existeFormato($id);
		break;

	case "descargaFormato":
		descargaFormato($id);
		break;

///////		SUB TIPOS DE ESPACIOS		//////////////////////////////////////////////////

	case "cargaSubtipoEspacio":
		cargaSubtipoEspacio($id,$link);
		break;

	case "actualizaSubtipoEspacio":
		actualizaSubtipoEspacio($id,$nombre,$link);
		break;

	case "guardaSubtipoEspacio":
		guardaSubtipoEspacio($nombre,$id_tipo,$link);
		break;

	case "eliminaSubtipoEspacio":
		eliminaSubtipoEspacio($id,$link);
		break;

///////		TIPOS DE ESPACIOS		//////////////////////////////////////////////////

	case "cargaTipoEspacio":
		cargaTipoEspacio($id,$link);
		break;

	case "actualizaTipoEspacio":
		actualizaTipoEspacio($id,$nombre,$link);
		break;

	case "guardaTipoEspacio":
		guardaTipoEspacio($nombre,$empresa,$sucursal,$link);
		break;

	case "eliminaTipoEspacio":
		eliminaTipoEspacio($id,$link);
		break;

///////		ESPACIOS		//////////////////////////////////////////////////

	case "cargaInfoEspacios":
		cargaInfoEspacios($id,$link);
		break;

	case "eliminaEspacio":
		eliminaEspacio($id,$link);
		break;

	case "guardaEspacio":
		guardaEspacio($nombre,$ubicacion,$zona,$tipo,$subtipo,$arrendamiento,$administracion,$area,$categoria,$trafico,$pertenencia,$descripcion,$sucursal,$empresa,$link);
		break;

	case "actualizaEspacio":
		actualizaEspacio($id,$nombre,$ubicacion,$zona,$tipo,$subtipo,$arrendamiento,$administracion,$area,$categoria,$trafico,$pertenencia,$descripcion,$sucursal,$empresa,$link);
		break;


///////		ZONAS		//////////////////////////////////////////////////

	case "cargaZona":
		cargaZona($id,$link);
		break;

	case "guardaZona":
		guardaZona($nombre,$empresa,$sucursal,$link);
		break;

	case "actualizaZona":
		actualizaZona($id,$nombre,$link);
		break;

	case "eliminaZona":
		eliminaZona($id,$link);
		break;

///////		DOCUMENTOS		//////////////////////////////////////////////////

	case "importarDocumento":
		importarDocumento($id,$id_empresa,$id_sucursal,$link);
		break;

	case "actualTextoDocumento":
		actualTextoDocumento($id_documento,$texto,$link);
		break;

	case "actualizaDocumento":
		actualizaDocumento($id,$nombre,$tipo_doc,$link);
		break;

	case "cargaDocumento":
		cargaDocumento($id,$link);
		break;

	case "guardaDocumento":
		guardaDocumento($nombre,$tipo_doc,$id,$sucursal,$link); // id es el id de la empresa
		break;

	case "eliminaDocumento":
		eliminaDocumento($id,$link);
		break;

///////		TIPOS	DOCUMENTOS		//////////////////////////////////////////////////

	case "cargaDocumentosTipo":
		cargaDocumentosTipo($id,$link);
		break;

	case "guardaTipoDocumento":
		guardaTipoDocumento($nombre,$link);
		break;

	case "actualizaTipoDocumento":
		actualizaTipoDocumento($id,$nombre,$link);
		break;

	case "eliminaTipoDocumento":
		eliminaTipoDocumento($id,$link);
		break;

///////		OPTION CAMPO		//////////////////////////////////////////////////

	case "optionCampo":
		optionCampo($tabla,$link);
        break;

///////		OPTION SUBTIPO		//////////////////////////////////////////////////

	case "optionSubtipos":
		optionSubtipos($tipo,$link);
        break;

///////		VARIABLES		//////////////////////////////////////////////////

	case "existeVariable":
		existeVariable($id,$tabla,$campo,$link);
		break;

	case "guardaVariable":
		guardaVariable($nombre_variable,$detalle_variable,$grupo,$campo,$tabla,$funcion,$link);
		break;

	case "actualizaVariable":
		actualizaVariable($id,$nombre_variable,$detalle_variable,$grupo,$campo,$tabla,$funcion,$link);
		break;

	case "eliminaVariable":
		eliminaVariable($id,$link);
		break;

///////		GRUPO VARIABLES		//////////////////////////////////////////////////

	case "cargaGrupoVariable":
		cargaGrupoVariable($id,$link);
		break;

	case "guardaGrupoVariable":
		guardaGrupoVariable($nombre,$link);
		break;

	case "actualizaGrupoVariable":
		actualizaGrupoVariable($id,$nombre,$link);
		break;

	case "eliminaGrupoVariable":
		eliminaGrupoVariable($id,$link);
		break;

///////		CONFIGURACION CORREOS SMTP		//////////////////////////////////////////////////

	case "cargaConfig":
		cargaConfig($filtro_empresa,$link);
		break;

	case "guardaConfig":
		guardaConfig($filtro_empresa,$servidor,$correo,$password,$puerto,$seguridad,$autenticacion,$link);
		break;

	// ROLES /////////////////////////////////////////
	case "agregarRol": //OK
		agregarRol($rol,$rolnivel,$link);
		break;


//////		CONFIGURACION TITULO NUEVA VENTANA///////////////////////////////////////////////////
	case "TituloVentana":
		titulo_ventana($id,$link,$tabla,$titulo_nueva_ventana);
		break;


//////		CONFIGURACION FESTIVOS PAIS///////////////////////////////////////////////////
	case "Configurar_festivo_pais":
		configurar_festivo_pais($link,$checked,$fecha,$id_pais);
		break;


//////		MODULO ITEM GLOBAL Y X EMPRESA///////////////////////////////////////////////////
	case "VentanaImpuestoValorItems":
		VentanaImpuestoValorItems();
		break;

	case "GuardarImpuestoValorItem":
		GuardarImpuestoValorItem($id_items_general,$impuesto_item,$valor_item,$link,$id_empresa,$obs_comercial,$obs_logistica);
		break;

	case "VentanaEditarItemsEmpresa":
		VentanaEditarItemsEmpresa($id,$link);
		break;

	case "GuardarEdicionItemsEmpresa":
		GuardarEdicionItemsEmpresa($id,$impuesto_item,$valor_item,$link,$obs_comercial,$obs_logistica);
		break;

	case "OptionSelectSubgrupoItems":
		OptionSelectSubgrupoItems($id_item,$id_item_grupo,$link);
		break;

	case "OptionSelectGrupoItems":
		OptionSelectGrupoItems($id_item,$id_item_familia,$link);
		break;

	case "OptionSelectFamiliaItems":
		OptionSelectFamiliaItems($id_item,$link);
		break;

/////// 	ELIMINAR UN ROL 	/////////////////////////////////////////////////////////////////
	
	case "OptionEliminarRol":

		break;			


}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

	// FORMATOS DESCARGABLES /////////////////////////////////////////////////////////////////////////////////////////

	function descargaFormato($id){
		$tfoto = null;
		$ext = null;
		$sql = "SELECT formato, nombre_formato, ext_formato FROM empresas_formatos WHERE id=$id ";
		//echo $sql;
		$result				= mysql_query($sql);
		$nombre_formato		= mysql_result($result,$i,"nombre_formato");
		$ext				= mysql_result($result,$i,"ext_formato");
		if($ext!=null || $ext!=""){
			$logo	= mysql_result($result,$i,"formato");
			$newfile="../../../ARCHIVOS_PROPIOS/temp/".$nombre_formato.".".$ext;
			$file = fopen ($newfile, "w");
			fwrite($file, $logo);
			fclose ($file);
			$path =$nombre_formato.".".$ext;
			echo  'true{.}'.$path;
		}else{
			echo  'false{.}No existe.';
		}
	}

	function borraFormato($id){
		$nombre_formato = null;
		$ext_formato = null;
		$sql = "UPDATE empresas_formatos SET formato='$nombre_formato' , ext_formato='$ext_formato' WHERE id=$id ";
		if(mysql_query($sql)){
			echo  'true{.}'.$sql;
		}else{
			echo  'false{.}No existe.'.$sql;
		}
	}

	function existeFormato($id){
		$sql 				= "SELECT nombre_formato,ext_formato FROM empresas_formatos  WHERE id=".$id;
		//echo $sql ;
		$result				= mysql_query($sql);
		$nombre_formato		= mysql_result($result,$i,"nombre_formato");
		$ext				= mysql_result($result,$i,"ext_formato");
		if($ext!=null || $ext!=""){
			echo  'true{.}'.$nombre_formato.".".$ext.'{.}'.$sql;
		}else{
			echo  'false{.}No existe.';
		}
	}

	function visualFormato($id){
		$sql 				= "SELECT nombre_formato,ext FROM empresas_formatos  WHERE id=".$id;
		$result				= mysql_query($sql);
		$nombre_formato		= mysql_result($result,$i,"nombre_formato");
		$ext				= mysql_result($result,$i,"ext");
		if($ext!=null || $ext!=""){
			$logo	= mysql_result($result,$i,"logo");
			$newfile="../../ARCHIVOS_PROPIOS/temp/logo.".$ext;
			$file = fopen ($newfile, "w");
			fwrite($file, $logo);
			fclose ($file);
			echo  'true{.}'.$nombre_formato.".".$ext.'{.}'.$sql;
		}else{
			echo  'false{.}No existe.';
		}
	}

	// ROLES /////////////////////////////////////////////////////////////////////////////////////////

	function agregarRol($rol,$rolnivel,$link){

	   $sql="INSERT INTO empleados_roles (nombre,valor,id_empresa) VALUES ('".$rol."','".$rolnivel."',".$_SESSION['EMPRESA'].")";
	   $connectid = mysql_query($sql,$link);
		if($connectid){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id.'{.}';
			mylog('AGREGAR ROL ->'.$sql,17,$link);
		}else{
			echo 'false{.}';
		}

	}

///////////////////////////// SUB TIPOS DE ESPACIOS ////////////////////////////////////////////////////////////////////////////////////////////////////

function cargaSubtipoEspacio($id,$link){
	$sql="SELECT * FROM rental_subtipos_espacios WHERE id='".$id."'";
	$result = mysql_query($sql,$link);
	if(mysql_num_rows($result)){
		$nombre		= mysql_result($result,$i,"nombre_subtipo");
		echo  'true{.}'.$nombre;
	}else
		echo  'false{.}No existen datos.';
}

function guardaSubtipoEspacio($nombre,$id_tipo,$link){
	$query = "SELECT 1 FROM rental_subtipos_espacios WHERE nombre_subtipo='".$nombre."'";
	$result=mysql_query($query,$link);
	$num=mysql_numrows($result);
	if($num==0){
		$sql = "INSERT INTO rental_subtipos_espacios
			(nombre_subtipo,tipo)
			VALUES
			('$nombre','$id_tipo')";
		$connectid =mysql_query($sql,$link);

		if($connectid){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id;
			mylog('AGREGAR SUBTIPO ESPACIO -> '.$sql,4,$link);
		}else{
			echo 'false{.}Error al guardar'.$sql;
		}
	}else{
		echo 'false{.}Nombre existente, No se guardo.';
	}

}

function actualizaSubtipoEspacio($id,$nombre,$link){
	$query = "SELECT 1 FROM rental_subtipos_espacios WHERE nombre_subtipo='".$nombre."'";
	$result=mysql_query($query,$link);
	$num=mysql_numrows($result);
	if($num==0){
		$sql = "UPDATE rental_subtipos_espacios
				SET nombre_subtipo='".$nombre.
				"' WHERE id=".$id;

	   $connectid =mysql_query($sql,$link);

		if($connectid){
			echo 'true{.}'.$id.'{.}'.$sql;
			mylog('ACTUALIZA SUBTIPO ESPACIO -> '.$sql,4,$link);
		}else{
			echo 'false{.}No se realizo{.}'.$sql;
		}
	}else{
		echo 'false{.}Nombre existente, No se guardo.';
	}

}

function eliminaSubtipoEspacio($id,$link){
	$sql = "UPDATE rental_subtipos_espacios
			SET activo='0'
			WHERE id=".$id;

   $connectid =mysql_query($sql,$link);

	if($connectid){
		echo 'true{.}'.$id.'{.}'.$sql;
		mylog('ELIMINA SUBTIPO ESPACIO -> '.$sql,4,$link);
	}else{
		echo 'false{.}No se realizo{.}'.$sql;
	}
}



///////////////////////////// TIPOS DE ESPACIOS ////////////////////////////////////////////////////////////////////////////////////////////////////

function cargaTipoEspacio($id,$link){
	$sql="SELECT * FROM rental_tipos_espacios WHERE id='".$id."'";
	$result = mysql_query($sql,$link);
	if(mysql_num_rows($result)){
		$nombre		= mysql_result($result,$i,"nombre_tipo");
		echo  'true{.}'.$nombre;
	}else
		echo  'false{.}No existen datos.';
}

function guardaTipoEspacio($nombre,$empresa,$sucursal,$link){
	$query = "SELECT 1 FROM rental_tipos_espacios WHERE nombre_tipo='".$nombre."'";
	$result=mysql_query($query,$link);
	$num=mysql_numrows($result);
	if($num==0){
		$sql = "INSERT INTO rental_tipos_espacios
			(nombre_tipo,id_empresa,id_sucursal)
			VALUES
			('$nombre','$empresa','$sucursal')";
		$connectid =mysql_query($sql,$link);

		if($connectid){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id;
			mylog('AGREGAR TIPO ESPACIO -> '.$sql,4,$link);
		}else{
			echo 'false{.}Error al guardar'.$sql;
		}
	}else{
		echo 'false{.}Nombre existente, No se guardo.';
	}

}

function actualizaTipoEspacio($id,$nombre,$link){
	$query = "SELECT 1 FROM rental_tipos_espacios WHERE nombre_tipo='".$nombre."'";
	$result=mysql_query($query,$link);
	$num=mysql_numrows($result);
	if($num==0){
		$sql = "UPDATE rental_tipos_espacios
				SET nombre_tipo='".$nombre.
				"' WHERE id=".$id;

	   $connectid =mysql_query($sql,$link);

		if($connectid){
			echo 'true{.}'.$id.'{.}'.$sql;
			mylog('ACTUALIZA TIPO ESPACIO -> '.$sql,4,$link);
		}else{
			echo 'false{.}No se realizo{.}'.$sql;
		}
	}else{
		echo 'false{.}Nombre existente, No se guardo.';
	}
}

function eliminaTipoEspacio($id,$link){
	$sql = "UPDATE rental_tipos_espacios
			SET activo='0'
			WHERE id=".$id;

   $connectid =mysql_query($sql,$link);

	if($connectid){
		echo 'true{.}'.$id.'{.}'.$sql;
		mylog('ELIMINA TIPO ESPACIO -> '.$sql,4,$link);
	}else{
		echo 'false{.}No se realizo{.}'.$sql;
	}
}

////////////////////		ESPACIOS		//////////////////////////////////////////////////////////////////////////////////////////////////////////////

function guardaEspacio($nombre,$ubicacion,$zona,$tipo,$subtipo,$arrendamiento,$administracion,$area,$categoria,$trafico,$pertenencia,$descripcion,$sucursal,$empresa,$link){
	$query = "SELECT 1 FROM rental_espacios WHERE nombre_espacio='".$nombre."'";
		$result=mysql_query($query,$link);
		$num=mysql_numrows($result);
		if($num==0){
			$sql ="INSERT INTO `rental_espacios` VALUES
			(NULL, '".$nombre."', '".$ubicacion."', '1', '".$zona."', NULL, '".$tipo."', NULL, '".$subtipo."', NULL, '".$arrendamiento."', '".$administracion."', '".$categoria."', '".$area."', '".$trafico."', '".$pertenencia."', '".$descripcion."', NULL, '".$sucursal."', NULL, '".$empresa."', NULL);";

			$connectid =mysql_query($sql,$link);
			if($connectid){
				$id = mysql_insert_id($link);
				echo 'true{.}'.$id.'{.}'.$sql;
				mylog('AGREGA ESPACIO -> '.$sql,4,$link);
			}else{
				echo 'false{.}No se realizo!{.}'.$sql;
			}
		}else{
			echo 'false{.}Nombre de espacio existente, No se guardo.';
		}
}

function eliminaEspacio($id,$link){
	$sql = "UPDATE rental_espacios
			SET activo='0'
			WHERE id=".$id;

   $connectid =mysql_query($sql,$link);

	if($connectid){
		echo 'true{.}'.$id.'{.}'.$sql;
		mylog('ELIMINA ESPACIO -> '.$sql,4,$link);
	}else{
		echo 'false{.}'.$sql;
	}
}

function actualizaEspacio($id,$nombre,$ubicacion,$zona,$tipo,$subtipo,$arrendamiento,$administracion,$area,$categoria,$trafico,$pertenencia,$descripcion,$sucursal,$empresa,$link){
	$query = "SELECT 1 FROM rental_espacios WHERE nombre_espacio='".$nombre."'";
	$result=mysql_query($query,$link);
	$num=mysql_numrows($result);
	if($num==0){
		$sql = "UPDATE rental_espacios
				SET nombre_espacio='".$nombre."',ubicacion='".$ubicacion."',id_zona='".$zona."',id_tipo='".$tipo."',id_subtipo='".$subtipo."',arrendamiento='".$arrendamiento."',administracion='".$administracion."',area='".$area."',categoria='".$categoria."',trafico='".$trafico."',pertenencia='".$pertenencia."',descripcion='".$descripcion."',id_sucursal='".$sucursal."',id_empresa='".$empresa."'
				WHERE id=".$id;

	   $connectid =mysql_query($sql,$link);

		if($connectid){
			echo 'true{.}'.$id.'{.}'.$sql;
			mylog('ACTUALIZA ZONA -> '.$sql,4,$link);
		}else{
			echo 'false{.}No se Guardo.{.}'.$sql;
		}
	}else{
			echo 'false{.}Nombre de espacio existente, No se guardo.';
		}
}

function cargaInfoEspacios($id,$link){
		global $nombre,$ubicacion,$arrendamiento,$administracion,$zona,$tipo,$subtipo,$activo,$area,$categoria,$pertenencia,$trafico;
		$query = "SELECT * FROM rental_espacios where id=".$id;

		$result=mysql_query($query,$link);

		if(mysql_num_rows($result)){
			$nombre = mysql_result($result,$i,"nombre_espacio");
			$ubicacion = mysql_result($result,$i,"ubicacion");
			$arrendamiento = mysql_result($result,$i,"arrendamiento");
			$administracion = mysql_result($result,$i,"administracion");
			$zona = mysql_result($result,$i,"id_zona");
			$tipo = mysql_result($result,$i,"id_tipo");
			$subtipo = mysql_result($result,$i,"id_subtipo");
			$area = mysql_result($result,$i,"area");
			$categoria = mysql_result($result,$i,"categoria");
			$pertenencia = mysql_result($result,$i,"pertenencia");
			$trafico = mysql_result($result,$i,"trafico");
			$descripcion = mysql_result($result,$i,"descripcion");
			echo  'true{.}'.	$nombre.'{.}'.$ubicacion.'{.}'.$arrendamiento.'{.}'.$administracion.'{.}'.$zona.'{.}'.$tipo.'{.}'.$subtipo.'{.}'.
								$area.'{.}'.$categoria.'{.}'.$pertenencia.'{.}'.$trafico.'{.}'.$descripcion;
		}else
			echo  'false{.}No existen datos.';
}

///////////////////////////// ZONAS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function cargaZona($id,$link){
	$sql="SELECT * FROM configuracion_zonas WHERE id='".$id."'";
	$result = mysql_query($sql,$link);
	if(mysql_num_rows($result)){
		$nombre		= mysql_result($result,$i,"nombre");
		echo  'true{.}'.$nombre;
	}else
		echo  'false{.}No existen datos.';
}

function actualizaZona($id,$nombre,$link){
	$query = "SELECT 1 FROM configuracion_zonas WHERE nombre='".$nombre."'";
	$result=mysql_query($query,$link);
	$num=mysql_numrows($result);
	if($num==0){
		$sql = "UPDATE configuracion_zonas
				SET nombre='".$nombre.
				"' WHERE id=".$id;
		$connectid =mysql_query($sql,$link);

		if($connectid){
			echo 'true{.}'.$id.'{.}'.$sql;
			mylog('ACTUALIZA ZONA -> '.$sql,4,$link);
		}else{
			echo 'false{.}Error al guardar{.}'.$sql;
		}
	}else{
		echo 'false{.}Nombre existente, No se guardo.';
	}
}

function eliminaZona($id,$link){
	$sql = "UPDATE configuracion_zonas
			SET activo='0'
			WHERE id=".$id;

   $connectid =mysql_query($sql,$link);

	if($connectid){
		echo 'true{.}'.$id.'{.}'.$sql;
		mylog('ELIMINA ZONA -> '.$sql,4,$link);
	}else{
		echo 'false{.}';
		echo $sql;
	}
}

function guardaZona($nombre,$empresa,$sucursal,$link){
	$query = "SELECT 1 FROM configuracion_zonas WHERE nombre='".$nombre."'";
	$result=mysql_query($query,$link);
	$num=mysql_numrows($result);
	if($num==0){
		$sql = "INSERT INTO configuracion_zonas
			(nombre,id_empresa,id_sucursal)
			VALUES
			('$nombre','$empresa','$sucursal')";
		$connectid =mysql_query($sql,$link);

		if($connectid){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id;
			mylog('AGREGAR ZONA -> '.$sql,4,$link);
		}else{
			echo 'false{.}Error al guardar{.}'.$sql;
		}
	}else{
		echo 'false{.}Nombre existente, No se guardo.';
	}
}

///////////////////////////// DOCUMENTOS ////////////////////////////////////////////////////////////////////////////////////////////////////

function importarDocumento($id,$id_empresa,$id_sucursal,$link){
	$sql="SELECT * FROM configuracion_documentos WHERE id='".$id."'";
	$result 	= mysql_query($sql,$link);
	$nombre		= mysql_result($result,$i,"nombre");
	$texto		= mysql_result($result,$i,"texto");
	$id_tipo	= mysql_result($result,$i,"id_tipo");

	$sql = "INSERT INTO configuracion_documentos
		(nombre,id_tipo,id_empresa,id_sucursal,texto)
		VALUES
		('$nombre','$id_tipo','$id_empresa','$id_sucursal','$texto')";
	$connectid =mysql_query($sql,$link);

	if($connectid){
		$id = mysql_insert_id($link);
		echo 'true{.}'.$id;
		mylog('IMPORTAR DOCUMENTO -> '.$sql,4,$link);
	}else{
		echo 'false{.}Error al guardar{.}'.$sql;
	}
}

function actualTextoDocumento($id_documento,$texto,$link){
	$sql = "UPDATE configuracion_documentos
			SET texto='".$texto.
			"' WHERE id=".$id_documento;

   $connectid =mysql_query($sql,$link);

	if($connectid){
		echo 'true{.}'.$id.'{.}'.$sql;
		mylog('ACTUALIZA TEXTO DOCUMENTO -> '.$sql,4,$link);
	}else{
		echo 'false{.}';
		echo $sql;
	}
}

function actualizaDocumento($id,$nombre,$tipo_doc,$link){
	$sql = "UPDATE configuracion_documentos
			SET nombre='".$nombre.
			"' , id_tipo='".$tipo_doc.
			"' WHERE id=".$id;

   $connectid =mysql_query($sql,$link);

	if($connectid){
		echo 'true{.}'.$id.'{.}'.$sql;
		mylog('ACTUALIZA DOCUMENTO -> '.$sql,4,$link);
	}else{
		echo 'false{.}';
		echo $sql;
	}
}

function cargaDocumento($id,$link){
	$sql="SELECT * FROM configuracion_documentos WHERE id='".$id."'";
	$result = mysql_query($sql,$link);
	if(mysql_num_rows($result)){
		$nombre		= mysql_result($result,$i,"nombre");
		$id_tipo		= mysql_result($result,$i,"id_tipo");
		echo  'true{.}'.$nombre.'{.}'.$id_tipo;
	}else
		echo  'false{.}No existen datos.';
}

function guardaDocumento($nombre,$tipo_doc,$id,$sucursal,$link){

	$sql = "INSERT INTO configuracion_documentos
		(nombre,id_tipo,id_empresa,id_sucursal)
		VALUES
		('$nombre','$tipo_doc','$id','$sucursal')";
	$connectid =mysql_query($sql,$link);

	if($connectid){
		$id = mysql_insert_id($link);
		echo 'true{.}'.$id;
		mylog('AGREGAR DOCUMENTO -> '.$sql,4,$link);
	}else{
		echo 'false{.}Error al guardar';
		echo $sql;
	}
}

function eliminaDocumento($id,$link){
	$sql="DELETE FROM configuracion_documentos WHERE id='".$id."'";
	if(mysql_query($sql,$link)){
		echo  'true{.}';
	}else
		echo  'false{.}No existen datos.{.}'.$sql;
}

///////////////////////////// TIPOS  DOCUMENTOS ////////////////////////////////////////////////////////////////////////////////////////////////////

function cargaDocumentosTipo($id,$link){
	$sql="SELECT * FROM configuracion_documentos_tipo WHERE id='".$id."'";
	$result = mysql_query($sql,$link);
	if(mysql_num_rows($result)){
		$nombre		= mysql_result($result,$i,"nombre");
		echo  'true{.}'.$nombre;
	}else
		echo  'false{.}No existen datos.';
}

function guardaTipoDocumento($nombre,$link){
	$sql = 	"INSERT INTO configuracion_documentos_tipo
			(nombre)
			VALUES
			('$nombre')";
   $connectid =mysql_query($sql,$link);

	if($connectid){
		$id = mysql_insert_id($link);
		echo 'true{.}'.$id;
		mylog('AGREGAR TIPO DOCUMENTO -> '.$sql,4,$link);
	}else{
		echo 'false{.}Error al guardar';
		echo $sql;
	}
}

function actualizaTipoDocumento($id,$nombre,$link){
	$sql = "UPDATE configuracion_documentos_tipo
			SET nombre='".$nombre.
			"' WHERE id=".$id;

   $connectid =mysql_query($sql,$link);

	if($connectid){
		echo 'true{.}'.$id;
		mylog('ACTUALIZA TIPO DOCUMENTO -> '.$sql,4,$link);
	}else{
		echo 'false{.}';
		echo $sql;
	}
}

function eliminaTipoDocumento($id,$link){
	$sql = "UPDATE configuracion_documentos_tipo
			SET activo='0'
			WHERE id=".$id;
	if(mysql_query($sql,$link)){
		echo  'true{.}';
	}else
		echo  'false{.}No existen datos.{.}'.$sql;
}


///////////////////////////// VARIABLES ////////////////////////////////////////////////////////////////////////////////////////////////////


function existeVariable($id,$tabla,$campo,$link){
	$query = "SELECT tabla,campo FROM variables WHERE tabla='".$tabla."' AND campo='".$campo."' AND id!='".$id."'";

	$result=mysql_query($query);
	$num=mysql_numrows($result);

	if($num>0){
		echo "true";
	}
	else{
		//return $query;
		echo "false";
	}
}

function existeVariableBD($id,$tabla,$campo,$nombre_variable,$link){
	$query = "SELECT tabla,campo FROM variables WHERE tabla='".$tabla."' AND campo='".$campo."' AND id!='".$id."' OR nombre='".$nombre_variable."' AND id!='".$id."'";

	$result=mysql_query($query);
	$num=mysql_numrows($result);

	if($num>0){
		//echo $query."//".$num;
		return true;
	}
	else{
		return false;
	}
}

function actualizaVariable($id,$nombre_variable,$detalle_variable,$grupo,$campo,$tabla,$funcion,$link){

	if(!existeVariableBD($id,$tabla,$campo,$nombre_variable,$link)){
		$sql = "UPDATE variables
				SET detalle='$detalle_variable' ,id_grupo='$grupo' ,campo='$campo' ,tabla='$tabla' ,funcion='$funcion'
				WHERE id=".$id;

	   $connectid =mysql_query($sql,$link);

		if($connectid){
			echo 'true{.}'.$id;
			mylog('ACTUALIZA VARIABLE -> '.$sql,4,$link);
		}else{
			echo 'false{.}';
			echo $sql;
		}
	}else{
		echo 'false{.}Variable ya existe{.}'.$sql;
	}
}

function guardaVariable($nombre_variable,$detalle_variable,$grupo,$campo,$tabla,$funcion,$link){


	if(!existeVariableBD($id,$tabla,$campo,$nombre_variable,$link)){
	  $sql = 	"INSERT INTO variables
			(nombre,detalle,id_grupo,campo,tabla,funcion)
			VALUES
			('$nombre_variable','$detalle_variable','$grupo','$campo','$tabla','$funcion')";
	   $connectid =mysql_query($sql,$link);

		if($connectid){
			$id = mysql_insert_id($link);
			echo 'true{.}'.$id;
			mylog('AGREGAR VARIABLE -> '.$sql,4,$link);
		}else{
			echo 'false{.}Error al guardar';
			echo $sql;
			}
	}else{
		echo 'false{.}Variable ya existe{.}'.$sql;
	}
}

function eliminaVariable($id,$link){
	$sql="DELETE FROM variables WHERE id='".$id."'";
	if(mysql_query($sql,$link)){
		echo  'true{.}';
	}else
		echo  'false{.}No existen datos.{.}'.$sql;
}

///////////////////////////// GRUPO DE VARIABLES ////////////////////////////////////////////////////////////////////////////////


function eliminaGrupoVariable($id,$link){
	$sql1="SELECT * FROM variables WHERE id_grupo='".$id."'";
	$result = mysql_query($sql1,$link);
	if(mysql_num_rows($result)==0){
		$sql="DELETE FROM variables_grupos WHERE id='".$id."'";
		if(mysql_query($sql,$link)){
			echo  'true{.}';
		}else
			echo  'false{.}No existen datos.{.}'.$sql;
	}else
		echo  'false{.}Existen variables asociadas a este grupo, no se puede borrar.{.}'.$sql1;
}

function cargaGrupoVariable($id,$link){
	$sql="SELECT * FROM variables_grupos WHERE id='".$id."'";
	$result = mysql_query($sql,$link);
	if(mysql_num_rows($result)){
		$nombre		= mysql_result($result,$i,"nombre");
		echo  'true{.}'.$nombre;
	}else
		echo  'false{.}No existen datos.';
}

function guardaGrupoVariable($nombre,$link){
   $sql = 	"INSERT INTO variables_grupos
			(nombre)
			VALUES
			('".$nombre."')";

   $connectid =mysql_query($sql,$link);

	if($connectid){
		$id = mysql_insert_id($link);
		echo 'true{.}'.$id;
		mylog('AGREGAR GRUPO VARIABLE -> '.$sql,4,$link);
	}else{
		echo 'false{.}'.$sql;
		}
}

function actualizaGrupoVariable($id,$nombre,$link){
	$sql = "UPDATE variables_grupos
			SET nombre='".$nombre."'
			WHERE id=".$id;

   $connectid =mysql_query($sql,$link);

	if($connectid){
		echo 'true{.}'.$id;
		mylog('ACTUALIZA GRUPO VARIABLE -> '.$sql,4,$link);
	}else{
		echo 'false{.}No se pudo actualizar{.}'.$sql;
	}
}


///////////////////////////// OPCIONES DE CAMPO ////////////////////////////////////////////////////////////////////////////////

function optionCampo($tabla,$link){

	$query = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='logicalsofterp' AND TABLE_NAME='".$tabla."'";

	echo'<select class="myfieldObligatorio" name="campo" id="campo" style="width:200px" onChange="existeVariable()" onBlur="ValidarFieldVacio(this)">
			<div class="debug1" style="display:none"> '.$query.'</div>
			<option value="" selected>Seleccione...</option>';

	$result = mysql_query($query);
	$num    = mysql_numrows($result);
	$i      = 0;

	while ($i < $num) {
		$id          = mysql_result($result,$i,"COLUMN_NAME");
		$nombre_tipo = mysql_result($result,$i,"COLUMN_NAME");

		if($id_subtipo==$id){ echo '<option value="'.$id.'" selected>'.$nombre_tipo.'</option>'; }
		else{ echo '<option value="'.$id.'">'.$nombre_tipo.'</option>'; }

		$i++;
	}
	echo'</select>';
}

///////////////////////////// OPCIONES DE SUBTIPO ////////////////////////////////////////////////////////////////////////////////

function optionSubtipos($tipo,$link){
	echo '<select class="myfieldObligatorio" name="Espacios_id_subtipo" id="Espacios_id_subtipo" style="width:250px" onBlur="ValidarFieldVacio(this)">';
	echo '	<option value="">Seleccione...</option>';
		$query = "SELECT id,nombre_subtipo FROM rental_subtipos_espacios where tipo=".$tipo;
		//echo '<div class="debug1" style="display:none"> '.$query.'</div>';
		$result=mysql_query($query);
		$num=mysql_numrows($result);
		$i=0;
		while ($i < $num) {
				 $id = mysql_result($result,$i,"id");
				 $nombre_subtipo = mysql_result($result,$i,"nombre_subtipo");
				 if($id_subtipo==$id){
						 echo "<option value='".$id."' selected>";
						 echo $nombre_subtipo;
						 echo "</option>";
						 }else{
						 echo "<option value='".$id."'>";
						 echo $nombre_subtipo;
						 echo "</option>";
						 }
				 $i++;
		}
		echo "</select>";
}

///////////////////////////// CONFIGURACION CORREOS SMTP ////////////////////////////////////////////////////////////////////////////////

function cargaConfig($filtro_empresa,$link){
	$sql="SELECT * FROM empresas_config_correo WHERE id_empresa='".$filtro_empresa."'";
	$result = mysql_query($sql,$link);
	if(mysql_num_rows($result)){
		$servidor		= mysql_result($result,$i,"servidor");
		$correo 		= mysql_result($result,$i,"correo");
		$password		= mysql_result($result,$i,"password");
		$puerto			= mysql_result($result,$i,"puerto");
		$seguridad		= mysql_result($result,$i,"seguridad_smtp");
		$autenticacion	= mysql_result($result,$i,"autenticacion");

		echo  'true{.}'.$servidor.'{.}'.$correo.'{.}'.$password.'{.}'.$puerto.'{.}'.$seguridad.'{.}'.$autenticacion.'{.}';
	}else
		echo  'false{.}No existen datos, debes ingresar la configuracion.';
}

function guardaConfig($filtro_empresa,$servidor,$correo,$password,$puerto,$seguridad,$autenticacion,$link){
	$sql = "UPDATE empresas_config_correo
			SET servidor='$servidor' ,correo='$correo' ,password='$password' ,puerto='$puerto' ,seguridad_smtp='$seguridad' ,autenticacion='$autenticacion'
			WHERE id_empresa=".$filtro_empresa;

   $connectid = mysql_query($sql,$link);

   $num = mysql_affected_rows($link);

   if($num=="0"){
	   $sql = 	"INSERT INTO empresas_config_correo
				(id_empresa,servidor,correo,password,puerto,seguridad_smtp,autenticacion)
				VALUES
				('".$filtro_empresa."','".$servidor."','".$correo."','".$password."','".$puerto."','".$seguridad."','".$autenticacion."')";

	   $connectid =mysql_query($sql,$link);
   }


	if($connectid){
		echo 'true{.}Guardado.';
		mylog('ACTUALIZA CONFIG SMTP EMPRESA '.$filtro_empresa.' -> '.$sql,4,$link);
	}else{
		echo 'false{.}Error, no se guardo.'.$sql;
	}
}

//////////CONFIGURACION TITULO NUEVA VENTANA///////////////////////////////////////////////////

function titulo_ventana($id,$link,$tabla,$titulo_nueva_ventana){
	$SQL="select * from ".$tabla." WHERE activo=1 AND id=".$id;
	$consulta  =mysql_query($SQL,$link);
	while($row = mysql_fetch_array($consulta)){
		$campo=$row["$titulo_nueva_ventana"];
		echo $campo;
	}
	if (!$consulta){die('no valida'.mysql_error().$SQL);}


}


//////		CONFIGURACION FESTIVOS PAIS///////////////////////////////////////////////////

function configurar_festivo_pais($link,$checked,$fecha,$id_pais){

	if($checked=="true"){
		echo"true";
		$sql="INSERT INTO configuracion_festivos (id_pais,fecha) VALUES ('".$id_pais."','".$fecha."')";
	   	$connectid = mysql_query($sql,$link);
	   	if (!$connectid){die('no valida'.mysql_error().$sql);}
	}
	else{

		echo"false";
		$sql="DELETE FROM configuracion_festivos WHERE id_pais='".$id_pais."' AND fecha='".$fecha."'";
	   	$connectid = mysql_query($sql,$link);
	   	if (!$connectid){die('no valida'.mysql_error().$sql);}

	}

}

//////		MODULO ITEMS ///////////////////////////////////////////////////

// ventana nuevo items empresa (impuesto,valor) -->
function VentanaImpuestoValorItems(){

	echo '	<div style="width=100%; margin:15px 10px; overflow:hidden;">
				<div style"overflow:hidden; width:100%;">
					<div style="width:35%; float:left;">Impuesto</div>
					<div style="width:65%; float:left;"><input type="text" id="impuesto_item" /></div>
				</div>
				<div style"overflow:hidden; width:100%;">
					<div style="width:35%; float:left; margin-top:10px;">Valor</div>
					<div style="width:65%; float:left; margin-top:10px;"><input type="text" id="valor_item" /></div>
				</div>
				<div style"overflow:hidden; width:100%;">
					<div style="width:35%; float:left; margin-top:10px;">Observacion Comercial</div>
					<div style="width:65%; float:left; margin-top:10px;"><textarea id="obs_comercial" style="width:100%; height: 50px" onkeyup="validarCadena(this)"></textarea></div>
				</div>
				<div style"overflow:hidden; width:100%;">
					<div style="width:35%; float:left; margin-top:10px;">Observacion Logistica</div>
					<div style="width:65%; float:left; margin-top:10px;"><textarea id="obs_logistica" style="width:100%; height: 50px" onkeyup="validarCadena(this)"></textarea></div>

				</div>
			</div>
		';
}

// guardar items empresa (impuesto,valor) -->
function GuardarImpuestoValorItem($id_items_general,$impuesto_item,$valor_item,$link,$id_empresa,$obs_comercial,$obs_logistica){

	$SQL = "INSERT INTO items(id_items_general,impuesto,valor,id_empresa,obs_comercial,obs_logistica) VALUES ('$id_items_general','$impuesto_item','$valor_item','$id_empresa','".addslashes($obs_comercial)."','".addslashes($obs_logistica)."')";
	$connectids =mysql_query($SQL,$link);
	//---------------------------------------------------Se Optiene El Id Del Ultimo Insert-----------------------------------------------------------//
	$returnId = mysql_insert_id();
	if (!$connectids){die('Error al insertar nuevo item'.mysql_error());}
	else{echo $returnId;}
}

// ventana update items empresa (impuesto,valor) -->
function VentanaEditarItemsEmpresa($id,$link){
	$SQL="SELECT impuesto,valor,obs_comercial,obs_logistica FROM items WHERE activo=1 AND id=$id";
	$consulta  =mysql_query($SQL,$link);
	while($row = mysql_fetch_array($consulta)){ $valueImpuesto=$row["impuesto"];  $valueValor=$row["valor"]; $valueObsComercial=$row["obs_comercial"]; $valueObsLogistica=$row["obs_logistica"];}
	if (!$consulta){die('no valida'.mysql_error().$SQL);}

	echo '	<div style="width=100%; margin:15px 10px; overflow:hidden;">
				<div style"overflow:hidden; width:100%;">
					<div style="width:35%; float:left;">Impuesto</div>
					<div style="width:65%; float:left;"><input type="text" id="impuesto_item" value="'.$valueImpuesto.'" /></div>
				</div>
				<div style"overflow:hidden; width:100%;">
					<div style="width:35%; float:left; margin-top:10px;">Valor</div>
					<div style="width:65%; float:left; margin-top:10px;"><input type="text" id="valor_item" value="'.$valueValor.'" /></div>
				</div>
				<div style"overflow:hidden; width:100%;">
					<div style="width:35%; float:left; margin-top:10px;">Observacion Comercial</div>
					<div style="width:65%; float:left; margin-top:10px;"><textarea id="obs_comercial" style="width:100%; height: 50px" onkeyup="validarCadena(this)">'.$valueObsComercial.'</textarea></div>
				</div>
				<div style"overflow:hidden; width:100%;">
					<div style="width:35%; float:left; margin-top:10px;">Observacion Logistica</div>
					<div style="width:65%; float:left; margin-top:10px;"><textarea id="obs_logistica" style="width:100%; height: 50px" onkeyup="validarCadena(this)">'.$valueObsLogistica.'</textarea></div>
				</div>
			</div>';
}

// guardar items empresa (impuesto,valor) -->
function GuardarEdicionItemsEmpresa($id,$impuesto_item,$valor_item,$link,$obs_comercial,$obs_logistica){
	$obs_comercial=str_replace("'",'"',$obs_comercial);
	$obs_logistica=str_replace("'",'"',$obs_logistica);

	$sql = "UPDATE items SET impuesto='$impuesto_item', valor='$valor_item', obs_comercial='".addslashes($obs_comercial)."', obs_logistica='".addslashes($obs_logistica)."' WHERE id=$id";
    $connectid = mysql_query($sql,$link);
	if (!$connectid){die('Error al actualizar items empresa'.mysql_error().$sql);}
	else{echo "Item Actualizado";}
}

function OptionSelectGrupoItems($id_item,$id_item_grupo,$link){
	$selected="";
	$id_subgrupo_itemsDB="";

	if($id_item_grupo>=1){
		$SQL = "SELECT id_grupo FROM items_general WHERE id=".$id_item." AND activo=1";
        //echo $SQL;
			$consul  =mysql_query($SQL,$link);
			while($row = mysql_fetch_array($consul)){
				$id_subgrupo_itemsDB=$row['id_grupo'];
			}
		echo '<select class="myfieldObligatorio" name="MaestroGeneralItems_id_grupo" id="MaestroGeneralItems_id_grupo" style="width:230px" onchange="ValidarFieldVacio(this)">';
		echo '	<option value="">Seleccione...</option>';
			$SQL1 = "SELECT id,nombre,codigo FROM items_familia_grupo WHERE id_familia=".$id_item_grupo." AND activo=1";
            //echo $SQL1 ;
			$consul  =mysql_query($SQL1,$link);
			while($row = mysql_fetch_array($consul)){
				if($id_subgrupo_itemsDB==$row['id']){$selected="selected";}
				else {$selected="";}
				echo "<option value='".$row['id']."' ".$selected.">";
				echo $row['codigo'].' - '.$row['nombre'];
				echo "</option>";
			}
		echo "</select>";
        echo "
                <script>
                    var ComboGrupoItems = Ext.get('MaestroGeneralItems_id_grupo');
		            ComboGrupoItems.addListener(
			            'change',
			            function(event,element,options){
				            idGrupoItem = document.getElementById('MaestroGeneralItems_id_grupo').value;
				            ActualizaSubgrupoItems(idGrupoItem);
			            },
			            this
		            );

		            idGrupoItem = document.getElementById('MaestroGeneralItems_id_grupo').value;
		            ActualizaSubgrupoItems(idGrupoItem);
                </script>
                ";
	}
	else{
		echo '<select class="myfieldObligatorio" name="MaestroGeneralItems_id_grupo" id="MaestroGeneralItems_id_grupo" style="width:230px" onchange="ValidarFieldVacio(this)">';
		echo '	<option value="">Seleccione...</option>';
		echo "</select>";
	}
}


function OptionSelectSubgrupoItems($id_item,$id_item_grupo,$link){
	$selected="";
	$id_subgrupo_itemsDB="";

	if($id_item_grupo>=1){
		$SQL = "SELECT id_subgrupo FROM items_general WHERE id=".$id_item." AND activo=1";
			$consul  =mysql_query($SQL,$link);
			while($row = mysql_fetch_array($consul)){
				$id_subgrupo_itemsDB=$row['id_subgrupo'];
			}
		echo '<select class="myfieldObligatorio" name="MaestroGeneralItems_id_subgrupo" id="MaestroGeneralItems_id_subgrupo" style="width:230px" onchange="ValidarFieldVacio(this)">';
		echo '	<option value="">Seleccione...</option>';
			$SQL1 = "SELECT id,nombre,codigo FROM items_familia_grupo_subgrupo WHERE id_grupo=".$id_item_grupo." AND activo=1";
            //echo $SQL1;
			$consul  =mysql_query($SQL1,$link);
			while($row = mysql_fetch_array($consul)){
				if($id_subgrupo_itemsDB==$row['id']){$selected="selected";}
				else {$selected="";}
				echo "<option value='".$row['id']."' ".$selected.">";
				echo $row['codigo'].' - '.$row['nombre'];
				echo "</option>";
			}
		echo "</select>";
	}
	else{
		echo '<select class="myfieldObligatorio" name="MaestroGeneralItems_id_subgrupo" id="MaestroGeneralItems_id_subgrupo" style="width:230px" onchange="ValidarFieldVacio(this)">';
		echo '	<option value="">Seleccione...</option>';
		echo "</select>";
	}
}

function OptionSelectFamiliaItems($id_item,$link){
	$selected="";
	$id_subgrupo_itemsDB="";


	$SQL = "SELECT id_familia FROM items_general WHERE id=".$id_item." AND activo=1";
    //echo $SQL;
		$consul  =mysql_query($SQL,$link);
		while($row = mysql_fetch_array($consul)){
			$id_subgrupo_itemsDB=$row['id_familia'];
		}
	echo '<select class="myfieldObligatorio" name="MaestroGeneralItems_id_familia" id="MaestroGeneralItems_id_familia" style="width:230px" onchange="ValidarFieldVacio(this)">';
	echo '	<option value="">Seleccione...</option>';
		$SQL1 = "SELECT id,nombre,codigo FROM items_familia WHERE activo=1";
        //echo $SQL1;
		$consul  = mysql_query($SQL1,$link);
		while($row = mysql_fetch_array($consul)){
			if($id_subgrupo_itemsDB==$row['id']){$selected="selected";}
			else {$selected="";}
			echo "<option value='".$row['id']."' ".$selected.">";
			echo $row['codigo'].' - '.$row['nombre'];
			echo "</option>";
		}
	echo "</select>";

    echo "  <script>
                var ComboGrupoFamilia = Ext.get('MaestroGeneralItems_id_familia');
                ComboGrupoFamilia.addListener(
			        'change',
			        function(event,element,options){
				        idFamiliaItem = document.getElementById('MaestroGeneralItems_id_familia').value;
				        ActualizaGrupoItems(idFamiliaItem);
			        },
			        this
		        );

                idFamiliaItem = document.getElementById('MaestroGeneralItems_id_familia').value;
                ActualizaGrupoItems(idFamiliaItem);
            </script>
        ";
}



?>

