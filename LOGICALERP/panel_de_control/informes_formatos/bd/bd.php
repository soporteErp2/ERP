<?php
	include("../../../../configuracion/conectar.php");
	include('../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];
	switch ($opc) {
		case 'cargar_formatos_basicos':
			cargar_formatos_basicos($id_empresa,$mysql);
			break;
	}

	function cargar_formatos_basicos($id_empresa,$mysql){
		include ('array_formatos.php');
		$resul = "{.}true{.}";
		$error = '';
		// echo '{.}'.$mysql->query;
		// return;
		// CONSULTAR LOS QUE ESTAN ALMACENADOS PARA NO INSERTARLOS DE NUEVO
		$sql="SELECT codigo,nombre FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		while ($row = $mysql->fetch_array($query)) {
			foreach ($array_formatos as $codigo => $arrayResul) {
				// SI ESTA INSERTADO, ACTUALIZAR CAMPO INSERT A FALSE PARA Q NO SEA INSERTADO
				if ($codigo==$row['codigo']) {
					$array_formatos[$codigo]['insert'] = 'false';
				}
			}
		}

		// RECORRER EL ARRAY CON LOS FORMATOS QUE SE VA A INSERTAR
		foreach ($array_formatos as $codigo => $arrayResul) {
			if ($arrayResul['insert']=='true') {
				$valueInsertFormato.="(
										'$codigo',
										'$arrayResul[nombre]',
										$id_empresa
										),";
			}
		}
		// SI EL STRING CONTIENE DATOS PARA INSERTARSE, ENTONCES INSERTAR LOS FORMATOS
		if ($valueInsertFormato<>'') {
			$valueInsertFormato = substr($valueInsertFormato,0,-1);
			$sql="INSERT INTO medios_magneticos_formatos (codigo,nombre,id_empresa) VALUES $valueInsertFormato";
			$query=$mysql->query($sql,$mysql->link);
			if (!$query) {
				$resul = "false{.}";
				$error.='\nNo se insertaron los formatos';
			}
		}

		// CONSULTAR LOS ID DE TODOS LOS FORMATOS PARA UTILIZARLOS EN LOS INSERT DE COLUMNAS Y CONCEPTOS
		$sql="SELECT id,codigo FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		while ($row = $mysql->fetch_array($query)) {
			$array_formatos[$row['codigo']]['id']=$row['id'];
		}

		// CONSULTAR LAS COLUMNAS QUE YA ESTAN PARA INSERTARLAS
		$sql="SELECT id_formato,orden FROM medios_magneticos_formatos_columnas WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			foreach ($array_formatos_columnas as $codigo => $arrayCol) {
				foreach ($arrayCol as $id_default => $arrayResul) {
					if ($row['id_formato']==$array_formatos[$codigo]['id'] && $arrayResul['orden']==$row['orden']) {
						$array_formatos_columnas[$codigo][$id_default]['insert'] = 'false';
					}
				}
			}

		}

		// RECORRER EL ARRAY CON LAS COLUMNAS A INSERTAR
		foreach ($array_formatos_columnas as $codigo => $arrayCol) {
			foreach ($arrayCol as $id_default => $arrayResul) {
				if ($arrayResul['insert']=='true') {
					$valueInsertColumnas.="(
											'".$array_formatos[$codigo]['id']."',
											'$arrayResul[orden]',
											'$arrayResul[nombre]',
											$id_empresa
											),";
				}
			}
		}

		// SI EL STRING CONTIENE DATOS PARA INSERTARSE, ENCONCES SE INSERTAN LAS COLUMNAS
		if ($valueInsertColumnas<>'') {
			$valueInsertColumnas = substr($valueInsertColumnas,0,-1);
			$sql="INSERT INTO medios_magneticos_formatos_columnas(id_formato,orden,nombre,id_empresa) VALUES $valueInsertColumnas ";
			$query=$mysql->query($sql,$mysql->link);
			if (!$query) {
				$resul = "false{.}";
				$error.='\nNo se insertaron las columnas';
			}
		}

		// CONSULTAR LOS CONCEPTOS ALMACENADOS PARA NO INSERTARLOS DE NUEVO
		$sql="SELECT id_formato,concepto FROM medios_magneticos_formatos_conceptos WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			foreach ($array_formatos_conceptos as $codigo => $arrayCon) {
				foreach ($arrayCon as $id_default => $arrayResul) {
					if ($row['id_formato']==$array_formatos[$codigo]['id'] && $arrayResul['concepto']==$row['concepto']) {
						$array_formatos_conceptos[$codigo][$id_default]['insert'] = 'false';
					}
				}
			}
		}

		// RECORRER EL ARRAY CON LOS CONCEPTOS
		foreach ($array_formatos_conceptos as $codigo => $arrayCon) {
			foreach ($arrayCon as $id_default => $arrayResul) {
				if ($arrayResul['insert']=='true') {
					$valueInsertConceptos.="(
											'".$array_formatos[$codigo]['id']."',
											'$arrayResul[concepto]',
											'$arrayResul[descripcion]',
											$id_empresa
											),";
				}
			}
		}

		// SI EL STRING CONTIENE DATOS PARA INSERTARSE, ENCONCES SE INSERTAN LAS COLUMNAS
		if ($valueInsertConceptos<>'') {
			$valueInsertConceptos = substr($valueInsertConceptos,0,-1);
			$sql="INSERT INTO medios_magneticos_formatos_conceptos(id_formato,concepto,descripcion,id_empresa) VALUES $valueInsertConceptos ";
			$query=$mysql->query($sql,$mysql->link);
			if (!$query) {
				$resul = "false{.}";
				$error.='\nNo se insertaron los conceptos'.mysql_error().mysql_errno().$sql;
			}
		}

		$resul = $resul.$error;

		echo $resul;
	}

?>

