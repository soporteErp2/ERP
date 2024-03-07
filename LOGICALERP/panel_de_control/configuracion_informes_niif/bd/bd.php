<?php
	include("../../../../configuracion/conectar.php");
	include('../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];
	switch ($opc) {
		case 'cargar_formatos_basicos':
			cargar_formatos_basicos($id_empresa,$mysql);
			break;
		// FUNCIONES PARA SECCIONES
		case 'agregarSeccion':
			agregarSeccion($id_seccion,$codigo,$seccion_padre,$orden,$nombre,$tipo,$descripcion_tipo,$formula,$totalizado,$label_totalizado,$formula_totalizado,$id_formato,$id_empresa,$mysql);
			break;
		case 'actualizarSeccion':
			actualizarSeccion($id_seccion,$codigo,$seccion_padre,$orden,$nombre,$tipo,$descripcion_tipo,$formula,$totalizado,$label_totalizado,$formula_totalizado,$id_formato,$id_empresa,$mysql);
			break;
		case 'eliminarSeccion':
			eliminarSeccion($id_seccion,$id_formato,$id_empresa,$mysql);
			break;
		// FUNCIONES PARA LISTAS DE LAS SECCIONES
		case 'agregarFila':
			agregarFila($id_seccion,$id_formato,$id_fila,$seccion_padre,$codigo,$orden,$nombre,$naturaleza,$formula,$id_empresa,$mysql);
			break;
		case 'actualizarFila':
			actualizarFila($id_seccion,$id_formato,$id_fila,$seccion_padre,$codigo,$orden,$nombre,$naturaleza,$formula,$id_empresa,$mysql);
			break;
		case 'eliminarFila':
			eliminarFila($id_seccion,$id_formato,$id_fila,$id_empresa,$mysql);
			break;

	}

	// CARGAR FORMATOS BASICOS DE INFORMES NIIF
	function cargar_formatos_basicos($id_empresa,$mysql){
		include('array_formatos.php');
		$resul = "{.}true{.}";
		$error = '';
		// echo '{.}'.$mysql->query;
		// return;
		// CONSULTAR LOS QUE ESTAN ALMACENADOS PARA NO INSERTARLOS DE NUEVO
		$sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		while ($row = $mysql->fetch_array($query)) {
			foreach ($array_formatos as $codigo => $arrayResul) {
				// SI ESTA INSERTADO, ACTUALIZAR CAMPO INSERT A FALSE PARA Q NO SEA INSERTADO
				if ($codigo==$row['codigo']) {
					$array_formatos[$codigo]['insert'] = 'false';
				}
			}
		}
		// print_r($array_formatos);
		// RECORRER EL ARRAY CON LOS FORMATOS QUE SE VA A INSERTAR
		foreach ($array_formatos as $codigo => $arrayResul) {
			if ($arrayResul['insert']=='true') {
				$valueInsertFormato.="(
										'$codigo',
										'".htmlentities($arrayResul['nombre'])."',
										'$arrayResul[filtro_corte_anual]',
										'$arrayResul[filtro_rango_fechas]',
										'$arrayResul[filtro_cuentas]',
										$id_empresa
										),";
			}
		}
		// echo $valueInsertFormato;
		// SI EL STRING CONTIENE DATOS PARA INSERTARSE, ENTONCES INSERTAR LOS FORMATOS
		if ($valueInsertFormato<>'') {
			$valueInsertFormato = substr($valueInsertFormato,0,-1);
			$sql="INSERT INTO informes_niif_formatos (codigo,nombre,filtro_corte_anual,filtro_rango_fechas,filtro_cuentas,id_empresa) VALUES $valueInsertFormato";
			$query=$mysql->query($sql,$mysql->link);
			if (!$query) {
				$resul = "false{.}";
				$error.='\nNo se insertaron los formatos';
			}
		}
		// print_r($array_formatos);
		// CONSULTAR LOS ID DE TODOS LOS FORMATOS PARA UTILIZARLOS EN LOS INSERT DE SECCIONES Y FILAS
		$sql="SELECT id,codigo FROM informes_niif_formatos WHERE activo=1 AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		while ($row = $mysql->fetch_array($query)) {
			$array_formatos[$row['codigo']]['id']=$row['id'];
		}

		foreach ($array_formatos_secciones as $codigo_formato => $secciones) {
			foreach ($secciones as $id => $arrayResul) {
				if ($array_formatos[$codigo_formato]['insert']<>'true') { continue; }
				$valueInsertFormatoSecciones.="(
												$id,
												".$array_formatos[$codigo_formato]['id'].",
												'$arrayResul[orden]',
												'".htmlentities($arrayResul['nombre'])."',
												'$arrayResul[tipo]',
												'$arrayResul[descripcion_tipo]',
												'$arrayResul[totalizado]',
												'$arrayResul[label_totalizado]',
												'$arrayResul[formula_totalizado]',
												'$arrayResul[codigo_seccion_padre]',
												$id_empresa
												),";
			}
		}

		// INSERTAR LAS SECCIONES DE LOS FORMATOS
		$valueInsertFormatoSecciones = substr($valueInsertFormatoSecciones,0,-1);
		$sql="INSERT INTO informes_niif_formatos_secciones
				(
					codigo_seccion,
					id_formato,
					orden,
					nombre,
					tipo,
					descripcion_tipo,
					totalizado,
					label_totalizado,
					formula_totalizado,
					codigo_seccion_padre,
					id_empresa
				) VALUES $valueInsertFormatoSecciones";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			$resul = "false{.}";
			$error.='\nNo se insertaron las secciones';
		}

		foreach ($array_formatos_secciones_filas as $codigo_formato => $seccion) {
			foreach ($seccion as $codigo_seccion_padre => $filas) {
				foreach ($filas as $key => $arrayResul) {
					if ($array_formatos[$codigo_formato]['insert']<>'true') { continue; }
					$valueInsertFormatoFilas .="(
													".$array_formatos[$codigo_formato]['id'].",
													$codigo_seccion_padre,
													'$arrayResul[codigo]',
													'$arrayResul[orden]',
													'".htmlentities($arrayResul['nombre'])."',
													'$arrayResul[naturaleza]',
													$id_empresa
												),";
				}
			}
		}

		// INSERTAR LAS FILAS DE LAS SECCIONES
		$valueInsertFormatoFilas = substr($valueInsertFormatoFilas,0,-1);
		$sql="INSERT INTO informes_niif_formatos_secciones_filas
				(
					id_formato,
					id_seccion,
					codigo,
					orden,
					nombre,
					naturaleza,
					id_empresa
				) VALUES $valueInsertFormatoFilas ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			$resul = "false{.}";
			$error.='\nNo se insertaron las filas';
		}

		$resul = $resul.$error;

		echo $resul;
	}

	// FUNCIONES PARA LAS SECCIONES
	function agregarSeccion($id_seccion,$codigo,$seccion_padre,$orden,$nombre,$tipo,$descripcion_tipo,$formula,$totalizado,$label_totalizado,$formula_totalizado,$id_formato,$id_empresa,$mysql){
		$sql="INSERT INTO informes_niif_formatos_secciones
				(
					id_formato,
					codigo_seccion,
					codigo_seccion_padre,
					orden,
					nombre,
					tipo,
					descripcion_tipo,
					formula,
					totalizado,
					label_totalizado,
					formula_totalizado,
					id_empresa
				)
				VALUES
				(
					'$id_formato',
					'$codigo',
					'$seccion_padre',
					'$orden',
					'$nombre',
					'$tipo',
					'$descripcion_tipo',
					'$totalizado',
					'$label_totalizado',
					'$formula_totalizado',
					'$id_empresa'
				)
				";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_informes_niif/formatos_secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_formato : '$id_formato',
							id_seccion : '$id_seccion',
						}
					});
					Win_Ventana_Seccion.close();
				</script>";
		}
		else{
			// $id_seccion = $mysql->insert_id($mysql->link);
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se inserto la Seccion' });
				</script>";
		}
	}

	function actualizarSeccion($id_seccion,$codigo,$seccion_padre,$orden,$nombre,$tipo,$descripcion_tipo,$formula,$totalizado,$label_totalizado,$formula_totalizado,$id_formato,$id_empresa,$mysql){
		$sql="UPDATE informes_niif_formatos_secciones
				SET
					codigo_seccion       = '$codigo',
					codigo_seccion_padre = '$seccion_padre',
					orden                = '$orden',
					nombre               = '$nombre',
					tipo                 = '$tipo',
					descripcion_tipo     = '$descripcion_tipo',
					formula              = '$formula',
					totalizado           = '$totalizado',
					label_totalizado     = '$label_totalizado',
					formula_totalizado   = '$formula_totalizado '
				WHERE activo   = 1
				AND id_empresa = $id_empresa
				AND id         = $id_seccion
				AND id_formato = $id_formato";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE id=$id_formato";
			$query=$mysql->query($sql,$mysql->link);
			$codigo = $mysql->result($query,0,'codigo');
			$nombre = $mysql->result($query,0,'nombre');

			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_informes_niif/formatos_secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_formato : '$id_formato',
							id_seccion : '$id_seccion',
							codigo     : '$codigo',
							nombre     : '$nombre',
						}
					});
					Win_Ventana_Seccion.close();
				</script>";
		}
		else{
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se actualizo la Seccion' });
				</script>";
		}
	}

	function eliminarSeccion($id_seccion,$id_formato,$id_empresa,$mysql){
		$sql="UPDATE informes_niif_formatos_secciones SET activo=0 WHERE  id_empresa = $id_empresa AND id = $id_seccion AND id_formato = $id_formato";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE id=$id_formato";
			$query=$mysql->query($sql,$mysql->link);
			$codigo = $mysql->result($query,0,'codigo');
			$nombre = $mysql->result($query,0,'nombre');

			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_informes_niif/formatos_secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_formato : '$id_formato',
							id_seccion : '$id_seccion',
							codigo     : '$codigo',
							nombre     : '$nombre',
						}
					});
					Win_Ventana_Seccion.close();
				</script>";
		}
		else{
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se actualizo la Seccion' });
				</script>";
		}
	}

	// FUNCIONES PARA LAS FILAS DE LAS SECCIONES
	function agregarFila($id_seccion,$id_formato,$id_fila,$seccion_padre,$codigo,$orden,$nombre,$naturaleza,$formula,$id_empresa,$mysql){
		$sql="INSERT INTO informes_niif_formatos_secciones_filas
				(
					id_formato,
					id_seccion,
					codigo,
					orden,
					nombre,
					naturaleza,
					formula,
					id_empresa
				)
				VALUES
				(
					'$id_formato',
					'$id_seccion',
					'$codigo',
					'$orden',
					'$nombre',
					'$naturaleza',
					'$formula',
					'$id_empresa'
				)";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_informes_niif/formatos_secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_formato : '$id_formato',
							id_seccion : '$id_seccion',
						}
					});
					Win_Ventana_fila.close();
				</script>";
		}
		else{
			// $id_seccion = $mysql->insert_id($mysql->link);
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se inserto la Fila' });
				</script>";
		}
	}

	function actualizarFila($id_seccion,$id_formato,$id_fila,$seccion_padre,$codigo,$orden,$nombre,$naturaleza,$formula,$id_empresa,$mysql){
		$sql="UPDATE informes_niif_formatos_secciones_filas
				SET
					codigo     = '$codigo',
					orden      = '$orden',
					nombre     = '$nombre',
					naturaleza = '$naturaleza',
					formula    = '$formula'
				WHERE activo=1 AND id_empresa=$id_empresa AND id_formato=$id_formato AND id=$id_fila";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE id=$id_formato";
			$query=$mysql->query($sql,$mysql->link);
			$codigo = $mysql->result($query,0,'codigo');
			$nombre = $mysql->result($query,0,'nombre');

			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_informes_niif/formatos_secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_formato : '$id_formato',
							id_seccion : '$id_seccion',
							codigo     : '$codigo',
							nombre     : '$nombre',
						}
					});
					Win_Ventana_fila.close();
				</script>";
		}
		else{
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se actualizo la Fila' });
				</script>";
		}
	}


	function eliminarFila($id_seccion,$id_formato,$id_fila,$id_empresa,$mysql){
		$sql="UPDATE informes_niif_formatos_secciones_filas SET activo=0 WHERE id_empresa=$id_empresa AND id_formtato=$id_formato AND id_seccion=$id_seccion AND id=$id_fila";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			$sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE id=$id_formato";
			$query=$mysql->query($sql,$mysql->link);
			$codigo = $mysql->result($query,0,'codigo');
			$nombre = $mysql->result($query,0,'nombre');

			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_informes_niif/formatos_secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
							id_formato : '$id_formato',
							id_seccion : '$id_seccion',
							codigo     : '$codigo',
							nombre     : '$nombre',
						}
					});
					Win_Ventana_fila.close();
				</script>";
		}
		else{
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se actualizo la Fila' });
				</script>";
		}
	}

?>

