<?php

	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");

	$arrayExepcionFamilia = array();
	$arrayExepcionGrupo   = array();

	$arrayExepcionGrupo['13'] = true;
	$arrayExepcionGrupo['14'] = true;

	$arrayExepcionSubGrupo['04']['17'] = true;
	$arrayExepcionSubGrupo['06']['14'] = true;

	exit; //BLOQUEO SE SEGURIDAD PARA QUE ESTE SCRIPT NO SE A EJECUTADO

	$sqlGruposSIIP   = "SELECT IG.id AS id_grupo,
							IG.codigo_grupo,
							IG.nombre_grupo,
							IG.vida_util,
							IGS.id AS id_subgrupo,
							IGS.codigo_subgrupo,
							IGS.nombre_subgrupo
						FROM siip.inventario_grupo AS IG
						LEFT JOIN siip.inventario_grupo_subgrupo AS IGS ON (
								IG.id = IGS.id_grupo
								AND IGS.activo=1
							)
						WHERE IG.activo=1";

	$queryGruposSIIP = mysql_query($sqlGruposSIIP,$link);

	$arrayGrupo    = array();
	$arraySubGrupo = array();

	while ($row = mysql_fetch_assoc($queryGruposSIIP)) {
		$codigoGrupo    = $row['codigo_grupo'];
		$codigoSubGrupo = $row['codigo_subgrupo'];

		if($codigoGrupo != '') $arrayGrupo[$codigoGrupo] = array("nombre"=>$row['nombre_grupo'], "vida_util"=>$row['vida_util']);
		if($codigoSubGrupo != '') $arraySubGrupo[$codigoGrupo][$codigoSubGrupo] = array("nombre"=>$row['nombre_subgrupo']);
	}

	// echo json_encode($arraySubGrupo);

	$sqlEmpresas   = "SELECT id FROM empresas WHERE grupo_empresarial=1 AND activo=1 AND id>0";
	$queryEmpresas = mysql_query($sqlEmpresas,$link);

	while ($row = mysql_fetch_assoc($queryEmpresas)) {
		$id_empresa = $row['id'];

		//============================// GRUPOS - SUBGRUPOS ACTIVOS FIJOS //============================//
		//**********************************************************************************************//
		//GRUPOS
		foreach ($arrayGrupo as $codigoGrupo => $datosGrupo) {

			if($arrayExepcionGrupo[$codigoGrupo] == true){ continue; }
			crear_grupos($id_empresa,$codigoGrupo,$datosGrupo,$link);
		}

		//SUBGRUPOS
		if(isset($arrayIdGrupo)){ unset($arrayIdGrupo); }
		$arrayIdGrupo = array();

		$sqlGrupo   = "SELECT id,codigo_grupo FROM inventario_grupo WHERE id_empresa='$id_empresa' AND activo=1 ORDER BY codigo_grupo ASC";
		$queryGrupo = mysql_query($sqlGrupo,$link);
		while ($rowGrupo = mysql_fetch_assoc($queryGrupo)) {
			$idGrupo     = $rowGrupo['id'];
			$codigoGrupo = $rowGrupo['codigo_grupo'];

			$arrayIdGrupo[$codigoGrupo] = $idGrupo;
		}

		foreach ($arraySubGrupo as $codigoGrupo => $arrayGrupos) {
			foreach ($arrayGrupos as $codigoSubGrupo => $datosSubGrupo) {

				if($arrayExepcionSubGrupo[$codigoGrupo][$codigoSubGrupo] == true){ continue; }

				$idGrupo = $arrayIdGrupo[$codigoGrupo];
				crear_sub_grupos($row['id'],$idGrupo,$codigoSubGrupo,$datosSubGrupo,$link);
			}
		}

		//======================// FRAMILIAS - GRUPOS - SUBGRUPOS ITEMS //======================//
		//**********************************************************************************************//

		//FAMILIA
		$idFamilia_item = familia_item($id_empresa,$link);

		//GRUPOS
		foreach ($arrayGrupo as $codigoGrupo => $datosGrupo) {
			crear_grupos_item($id_empresa,$idFamilia_item,$codigoGrupo,$datosGrupo,$link);
		}

		//SUBGRUPOS
		if(isset($arrayIdGrupoItems)){ unset($arrayIdGrupoItems); }
		$arrayIdGrupoItems = array();

		$sqlGrupo   = "SELECT id,codigo
						FROM items_familia_grupo
						WHERE id_empresa='$id_empresa'
							AND activo=1
							AND id_familia='$idFamilia_item'
							AND cod_familia='00'
						ORDER BY codigo ASC";
		$queryGrupo = mysql_query($sqlGrupo,$link);
		while ($rowGrupo = mysql_fetch_assoc($queryGrupo)) {
			$idGrupo     = $rowGrupo['id'];
			$codigoGrupo = $rowGrupo['codigo'];

			$arrayIdGrupoItems[$codigoGrupo] = $idGrupo;
		}

		foreach ($arraySubGrupo as $codigoGrupo => $arrayGrupos) {
			foreach ($arrayGrupos as $codigoSubGrupo => $datosSubGrupo) {
				$idGrupo = $arrayIdGrupoItems[$codigoGrupo];
				crear_sub_grupos_item($row['id'],$idFamilia_item,$idGrupo,$codigoGrupo,$codigoSubGrupo,$datosSubGrupo,$link);
			}
		}
	}


	//============================// GRUPOS - SUBGRUPOS ACTIVOS FIJOS //============================//
	//**********************************************************************************************//
	function crear_grupos($id_empresa,$codigoGrupo,$datosGrupo,$link){

		$sqlGrupo   = "SELECT COUNT(id) AS contGrupo FROM inventario_grupo WHERE codigo_grupo='$codigoGrupo' AND activo=1 AND id_empresa='$id_empresa'";
		$queryGrupo = mysql_query($sqlGrupo,$link);
		$contGrupo  = mysql_result($queryGrupo, 0, 'contGrupo');

		if($contGrupo == 0){
			$sqlInsert   = "INSERT INTO inventario_grupo(codigo_grupo,nombre_grupo,vida_util,id_empresa) VALUES('$codigoGrupo','$datosGrupo[nombre]','$datosGrupo[vida_util]',$id_empresa)";
			$queryInsert = mysql_query($sqlInsert,$link);
		}
		else{
			$sqlUpdate   = "UPDATE inventario_grupo SET nombre_grupo='$datosGrupo[nombre]', vida_util='$datosGrupo[vida_util]' WHERE codigo_grupo='$codigoGrupo' AND id_empresa='$id_empresa'";
			$queryUpdate = mysql_query($sqlUpdate,$link);
		}
	}

	function crear_sub_grupos($id_empresa,$idGrupo,$codigoSubGrupo,$datosSubGrupo,$link){

		$sqlSubgrupo   = "SELECT COUNT(id) AS contSubgrupo FROM inventario_grupo_subgrupo WHERE activo=1 AND id_grupo='$idGrupo' AND codigo_subgrupo='$codigoSubGrupo'";
		$querySubgrupo = mysql_query($sqlSubgrupo,$link);
		$contSubgrupo  = mysql_result($querySubgrupo, 0, 'contSubgrupo');

		if($contSubgrupo == 0){
			$sqlInsert   = "INSERT INTO inventario_grupo_subgrupo(codigo_subgrupo,nombre_subgrupo,id_grupo) VALUES('$codigoSubGrupo','$datosSubGrupo[nombre]',$idGrupo)";
			$queryInsert = mysql_query($sqlInsert,$link);
		}
		else{
			$sqlUpdate   = "UPDATE inventario_grupo_subgrupo SET nombre_subgrupo='$datosSubGrupo[nombre]' WHERE id_grupo='$idGrupo' AND activo=1 AND codigo_subgrupo='$codigoSubGrupo'";
			$queryUpdate = mysql_query($sqlUpdate,$link);
		}

	}

	//======================// FRAMILIAS - GRUPOS - SUBGRUPOS ACTIVOS FIJOS //======================//
	//**********************************************************************************************//

	function familia_item($id_empresa,$link){
		$sqlFamilia   = "SELECT COUNT(id) AS contFamilia, id FROM items_familia WHERE activo=1 AND id_empresa='$id_empresa' AND codigo='00'";
		$queryFamilia = mysql_query($sqlFamilia,$link);
		$contFamilia  = mysql_result($queryFamilia, 0, 'contFamilia');
		$idFamilia    = mysql_result($queryFamilia, 0, 'id');

		if($contFamilia == 0){
			$sqlInsert   = "INSERT INTO items_familia(codigo,nombre,id_empresa) VALUES('00', 'ACTIVOS FIJOS', '$id_empresa')";
			$queryInsert = mysql_query($sqlInsert,$link);

			$sql = "SELECT LAST_INSERT_ID()";
			return mysql_result(mysql_query($sql,$link),0,0);
		}
		else{
			$sqlUpdate   = "UPDATE items_familia SET nombre='ACTIVOS FIJOS' WHERE activo=1 AND id_empresa='$id_empresa' AND codigo='00'";
			$queryUpdate = mysql_query($sqlUpdate,$link);

			return $idFamilia;
		}
	}

	function crear_grupos_item($id_empresa,$idFamilia,$codigoGrupo,$datosGrupo,$link){
		$sqlGrupo   = "SELECT COUNT(id) AS contGrupos, id FROM items_familia_grupo
						WHERE activo=1 AND id_empresa='$id_empresa' AND codigo='$codigoGrupo' AND id_familia='$idFamilia'";
		$queryGrupo = mysql_query($sqlGrupo,$link);

		$idGrupo   = mysql_result($queryGrupo, 0, 'id');
		$contGrupo = mysql_result($queryGrupo, 0, 'contGrupos');

		if($contGrupo == 0){
			$sqlInsert   = "INSERT INTO items_familia_grupo(id_familia,codigo,nombre,id_empresa) VALUES($idFamilia,'$codigoGrupo','$datosGrupo[nombre]','$id_empresa')";
			$queryInsert = mysql_query($sqlInsert,$link);
		}
		else{
			$sqlUpdate   = "UPDATE items_familia_grupo SET nombre='$datosGrupo[nombre]' WHERE activo=1 AND id_empresa='$id_empresa' AND codigo='$codigoGrupo' AND id_familia='$idFamilia'";
			$queryUpdate = mysql_query($sqlUpdate,$link);
		}
	}

	function crear_sub_grupos_item($id_empresa,$idFamilia,$idGrupo,$codigoGrupo,$codigoSubGrupo,$datosSubGrupo,$link){
		$sqlSubgrupo   = "SELECT COUNT(id) AS contSubgrupos, id
						FROM items_familia_grupo_subgrupo
						WHERE activo=1
							AND id_familia='$idFamilia'
							AND id_grupo='$idGrupo'
							AND id_empresa='$id_empresa'
							AND codigo='$codigoSubGrupo'";

		$querySubgrupo = mysql_query($sqlSubgrupo,$link);

		$idSubgrupo   = mysql_result($querySubgrupo, 0, 'id');
		$contSubgrupo = mysql_result($querySubgrupo, 0, 'contSubgrupos');

		if($contSubgrupo == 0){
			$sqlInsert   = "INSERT INTO items_familia_grupo_subgrupo(id_familia,id_grupo,codigo,nombre,id_empresa) VALUES($idFamilia,$idGrupo,'$codigoSubGrupo','$datosSubGrupo[nombre]','$id_empresa')";
			$queryInsert = mysql_query($sqlInsert,$link);

			$sql        = "SELECT LAST_INSERT_ID()";
			$idSubgrupo = mysql_result(mysql_query($sql,$link),0,0);
		}
		else{
			$sqlUpdate = "UPDATE items_familia_grupo_subgrupo
							SET nombre='$datosSubGrupo[nombre]'
							WHERE activo=1
								AND id_empresa='$id_empresa'
								AND id_familia='$idFamilia'
								AND id_grupo='$idGrupo'
								AND codigo='$codigoSubGrupo'";
			$queryUpdate = mysql_query($sqlUpdate,$link);
		}
	}

?>