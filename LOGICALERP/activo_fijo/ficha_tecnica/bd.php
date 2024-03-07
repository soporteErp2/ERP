<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'busca_grupo':
			busca_grupo($id_familia,$id_grupo,$id_empresa,$mysql);
			break;
		case 'buscaSubgrupo':
			buscaSubgrupo($id_familia,$id_grupo,$id_subgrupo,$id_empresa,$mysql);
			break;
		case 'guardarActivo':
			guardarActivo($id_familia,$codigo_familia,$familia,$id_grupo,$codigo_grupo,$grupo,$id_subgrupo,$codigo_subgrupo,$subgrupo,$id_item,$codigo_item,$codigo_automatico,$codigo_activo,$nombre_equipo,$tipo,$fecha_compra,$id_documento_referencia,$documento_referencia,$documento_referencia_consecutivo,$costo,$id_centro_costos,$codigo_centro_costos,$centro_costos,$fecha_vencimiento_garantia,$id_proveedor,$nit_proveedor,$proveedor,$id_funcionario_asignado,$documento_funcionario_asignado,$funcionario_asignado,$numero_serial,$numero_placa,$marca,$modelo,$color,$chasis,$id_unidad,$unidad,$numero_piezas,$longitud,$ancho,$alto,$volumen,$peso,$descripcion1,$descripcion2,$tenencia,$fecha_vencimiento_tenencia,$vida_util,$vida_util_niif,$sincronizar_siip,$id_sucursal,$id_bodega,$id_empresa,$mysql);
			break;
		case 'actualizarActivo':
			actualizarActivo($id_activo,$id_familia,$codigo_familia,$familia,$id_grupo,$codigo_grupo,$grupo,$id_subgrupo,$codigo_subgrupo,$subgrupo,$id_item,$codigo_item,$codigo_automatico,$codigo_activo,$nombre_equipo,$tipo,$fecha_compra,$id_documento_referencia,$documento_referencia,$documento_referencia_consecutivo,$id_documento_referencia_inventario,$costo,$id_centro_costos,$codigo_centro_costos,$centro_costos,$fecha_vencimiento_garantia,$id_proveedor,$nit_proveedor,$proveedor,$id_funcionario_asignado,$documento_funcionario_asignado,$funcionario_asignado,$numero_serial,$numero_placa,$marca,$modelo,$color,$chasis,$id_unidad,$unidad,$numero_piezas,$longitud,$ancho,$alto,$volumen,$peso,$descripcion1,$descripcion2,$tenencia,$fecha_vencimiento_tenencia,$vida_util,$vida_util_niif,$sincronizar_siip,$id_sucursal,$id_bodega,$id_empresa,$mysql);
			break;
		case 'cargarCuentasDefaultGrupos':
			cargarCuentasDefaultGrupos($id_activo,$id_empresa,$mysql);
			break;
		case 'guardaActualizaContabilidad':
			guardaActualizaContabilidad($id_activo,$depreciable,$fecha_inicio_depreciacion,$valor_salvamento,$metodo_depreciacion_colgaap,$depreciable_niif,$fecha_inicio_depreciacion_niif,$valor_salvamento_niif,$metodo_depreciacion_niif,$deteriorable,$cuentas,$id_sucursal,$id_bodega,$id_empresa,$mysql);
			break;
	}

	//////////////////////////////////////
	//		 PESTAÑA INFORMACION BASICA		//
	//////////////////////////////////////

	// BUSCAR GRUPO DE ITEMS
	function busca_grupo($id_familia,$id_grupo='',$id_empresa,$mysql){
		$sql="SELECT id,codigo,nombre FROM items_familia_grupo WHERE activo=1 AND id_empresa=$id_empresa AND id_familia=$id_familia";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$selected =($id_grupo==$row['id'])? 'selected' : '';
			$optionGrupo .="<option value='$row[id]' data-id='$row[id]' data-nombre='$row[nombre]' data-codigo='$row[codigo]' $selected >$row[nombre]</option>";
		}

		echo "<option value=''>Seleccione...</option>
					$optionGrupo";
	}

	// BUSCAR SUBGRUPO DE LOS ITEMS
	function buscaSubgrupo($id_familia,$id_grupo,$id_subgrupo='',$id_empresa,$mysql){
		$sql="SELECT id,codigo,nombre FROM items_familia_grupo_subgrupo WHERE activo=1 AND id_empresa=$id_empresa AND id_familia=$id_familia AND id_grupo=$id_grupo";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$selected =($id_subgrupo==$row['id'])? 'selected' : '';
			$optionGrupo .="<option value='$row[id]'  data-requiere='true' data-id='$row[id]' data-nombre='$row[nombre]' data-codigo='$row[codigo]' $selected >$row[nombre]</option>";
		}

		echo "<option value=''>Seleccione...</option>
					$optionGrupo";
	}

	// GUARDAR UN NUEVO ACTIVO
	function guardarActivo($id_familia,$codigo_familia,$familia,$id_grupo,$codigo_grupo,$grupo,$id_subgrupo,$codigo_subgrupo,$subgrupo,$id_item,$codigo_item,$codigo_automatico,$codigo_activo,$nombre_equipo,$tipo,$fecha_compra,$id_documento_referencia,$documento_referencia,$documento_referencia_consecutivo,$costo,$id_centro_costos,$codigo_centro_costos,$centro_costos,$fecha_vencimiento_garantia,$id_proveedor,$nit_proveedor,$proveedor,$id_funcionario_asignado,$documento_funcionario_asignado,$funcionario_asignado,$numero_serial,$numero_placa,$marca,$modelo,$color,$chasis,$id_unidad,$unidad,$numero_piezas,$longitud,$ancho,$alto,$volumen,$peso,$descripcion1,$descripcion2,$tenencia,$fecha_vencimiento_tenencia,$vida_util,$vida_util_niif,$sincronizar_siip,$id_sucursal,$id_bodega,$id_empresa,$mysql){
		$sql = "INSERT INTO activos_fijos(
							id_familia,
							codigo_familia,
							familia,
							id_grupo,
							codigo_grupo,
							grupo,
							id_subgrupo,
							codigo_subgrupo,
							subgrupo,
							id_item,
							code_bar,
							codigo_automatico,
							codigo_activo,
							nombre_equipo,
							tipo,
							fecha_compra,
							id_documento_referencia,
							documento_referencia,
							documento_referencia_consecutivo,
							costo,
							id_centro_costos,
							codigo_centro_costos,
							centro_costos,
							fecha_vencimiento_garantia,
							id_proveedor,
							nit_proveedor,
							proveedor,
							id_funcionario_asignado,
							documento_funcionario_asignado,
							funcionario_asignado,
							numero_serial,
							numero_placa,
							marca,
							modelo,
							color,
							chasis,
							id_unidad,
							unidad,
							numero_piezas,
							longitud,
							ancho,
							alto,
							volumen,
							peso,
							descripcion1,
							descripcion2,
							tenencia,
							fecha_vencimiento_tenencia,
							vida_util,
							vida_util_niif,
							sincronizar_siip,
							id_bodega,
							id_sucursal,
							id_empresa,
							estado
						)
						VALUES(
							'$id_familia',
							'$codigo_familia',
							'$familia',
							'$id_grupo',
							'$codigo_grupo',
							'$grupo',
							'$id_subgrupo',
							'$codigo_subgrupo',
							'$subgrupo',
							'$id_item',
							'$codigo_item',
							'$codigo_automatico',
							'$codigo_activo',
							'$nombre_equipo',
							'$tipo',
							'$fecha_compra',
							'$id_documento_referencia',
							'$documento_referencia',
							'$documento_referencia_consecutivo',
							'$costo',
							'$id_centro_costos',
							'$codigo_centro_costos',
							'$centro_costos',
							'$fecha_vencimiento_garantia',
							'$id_proveedor',
							'$nit_proveedor',
							'$proveedor',
							'$id_funcionario_asignado',
							'$documento_funcionario_asignado',
							'$funcionario_asignado',
							'$numero_serial',
							'$numero_placa',
							'$marca',
							'$modelo',
							'$color',
							'$chasis',
							'$id_unidad',
							'$unidad',
							'$numero_piezas',
							'$longitud',
							'$ancho',
							'$alto',
							'$volumen',
							'$peso',
							'$descripcion1',
							'$descripcion2',
							'$tenencia',
							'$fecha_vencimiento_tenencia',
							'$vida_util',
							'$vida_util_niif',
							'$sincronizar_siip',
							'$id_bodega',
							'$id_sucursal',
							'$id_empresa',
							'1'
						)";
		$query = $mysql->query($sql,$mysql->link);

		if($query){
			$id_activo = $mysql->insert_id();

			$cargaCuentas = guardarActualizarCuentasActivoDefault($id_activo,$id_grupo,$id_empresa,$mysql);

			$sql = "SELECT codigo_activo FROM activos_fijos WHERE id = $id_activo";
			$query = $mysql->query($sql,$mysql->link);
			$codigo_activo = $mysql->result($query,0,'codigo_activo');

			if($sincronizar_siip == "si"){
				include_once("../../external_apis/LOGICALHOTELS/backend/ClassExternalApis.php");

				$datosActivoFijo = array(
					"codigo" => "$codigo_activo",
					"id_empresa" => $_SESSION['ID_HOST'],
					"id_sucursal" => $id_sucursal,
					"id_bodega" => $id_bodega,
					"cod_grupo" => $codigo_grupo,
					"cod_subgrupo" => $codigo_subgrupo,
					"id_centro_costos" => $id_centro_costos,
					"cod_centro_costos" => $codigo_centro_costos,
					"nom_centro_costos" => "$centro_costos",
					"nombre_equipo" => "$nombre_equipo",
					"marca" => "$marca",
					"modelo" => "$modelo",
					"serie" => "$numero_serial",
					"color" => "$color",
					"numero_piezas" => $numero_piezas,
					"descripcion1" => "$descripcion1",
					"descripcion2" => "$descripcion2",
					"nit_proveedor" => "$nit_proveedor",
					"fecha_compra" => "$fecha_compra",
					"fecha_vencimiento_garantia" => "$fecha_vencimiento_garantia",
					"tenencia" => $tenencia,
					"fecha_vencimiento_tenencia" => "$fecha_vencimiento_tenencia",
					"documento_contable" => "$documento_referencia",
					"numero_documento" => "$documento_referencia_consecutivo",
					"costos" => $costo,
					"vida_util" => $vida_util_niif
				);

				$server_name = $_SERVER['SERVER_NAME'];

				if($server_name == "logicalerp.localhost"){
					$url_api = "http://siip.localhost/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}
				else{
					$url_api = "http://siip.plataforma.co/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}

				$params = [];
				$params['request_url']    = $url_api;
				$params['request_method'] = "POST";
				$params['Authorization']  = "";
				$params['data']           = json_encode($datosActivoFijo);

				$nuevoActivo = new ClassExternalApis($id_sucursal,$id_empresa,$mysql);
				$respuesta = $nuevoActivo->curlApi($params);

				$respuesta = json_decode($respuesta,true);

				// GUARDAMOS LA RESPUESTA DEL API
				$sql = "UPDATE activos_fijos
								SET estado_sincronizar_siip = '$respuesta[estado]'
								WHERE id = $id_activo
								AND activo = 1
								AND id_empresa = $id_empresa
								AND id_sucursal = $id_sucursal
								AND id_bodega = $id_bodega";
				$query = $mysql->query($sql,$mysql->link);

				if($respuesta['estado'] == "error" || $respuesta['estado'] == "existe"){
					$sql = "UPDATE activos_fijos
									SET estado = 0
									WHERE id = $id_activo
									AND activo = 1
									AND id_empresa = $id_empresa
									AND id_sucursal = $id_sucursal
									AND id_bodega = $id_bodega";
					$query = $mysql->query($sql,$mysql->link);

					echo "<script>
									alert('$respuesta[msg]');
									MyLoading2('off',{texto:'El activo fijo se registro, pero no fue sincronizado con el SIIP.'});
									Inserta_Div_ActivosFijos($id_activo);
									Win_ActivosFijos.close();
								</script>";
				}
				else if($respuesta['estado'] == "true"){
					echo "<script>
									MyLoading2('off',{texto:'Activo registrado y sincronizado con el SIIP'});
									Inserta_Div_ActivosFijos($id_activo);
									Win_ActivosFijos.close();
								</script>";
				}
			}
			else{
				echo "<script>
								MyLoading2('off',{texto:'Activo Registrado'});
								Inserta_Div_ActivosFijos($id_activo);
								Win_ActivosFijos.close();
							</script>";
			}
		}
		else{
			echo "<script>MyLoading2('off',{texto:'Error al registrar el activo',icono : 'fail',duracion:2000});</script>";
		}
	}

	// ACTUALIZAR UN ACTIVO
	function actualizarActivo($id_activo,$id_familia,$codigo_familia,$familia,$id_grupo,$codigo_grupo,$grupo,$id_subgrupo,$codigo_subgrupo,$subgrupo,$id_item,$codigo_item,$codigo_automatico,$codigo_activo,$nombre_equipo,$tipo,$fecha_compra,$id_documento_referencia,$documento_referencia,$documento_referencia_consecutivo,$id_documento_referencia_inventario,$costo,$id_centro_costos,$codigo_centro_costos,$centro_costos,$fecha_vencimiento_garantia,$id_proveedor,$nit_proveedor,$proveedor,$id_funcionario_asignado,$documento_funcionario_asignado,$funcionario_asignado,$numero_serial,$numero_placa,$marca,$modelo,$color,$chasis,$id_unidad,$unidad,$numero_piezas,$longitud,$ancho,$alto,$volumen,$peso,$descripcion1,$descripcion2,$tenencia,$fecha_vencimiento_tenencia,$vida_util,$vida_util_niif,$sincronizar_siip,$id_sucursal,$id_bodega,$id_empresa,$mysql){
		// VALIDAR QUE EL CODIGO DEL ACTIVO NO SE HUBIESE INSERTADO
		$sql = "SELECT id,estado FROM activos_fijos WHERE activo = 1 AND id_empresa = $id_empresa AND codigo_activo = $codigo_activo";
		$query = $mysql->query($sql,$mysql->link);
		$idRepedito = $mysql->result($query,0,'id');
		$estado     = $mysql->result($query,0,'estado');

		if($idRepedito > 0 && $idRepedito <> $id_activo){
			echo "<script>MyLoading2('off',{texto:'Codigo de activo ya existe!',icono : 'warning',duracion:3000});</script>";
			exit;
		}

		// SI SE ACTUALIZA UN ACTIVO INGRESADO MANUALMENTE DESDE EL MODULO
		if($id_documento_referencia_inventario == '' || $id_documento_referencia_inventario == 0 || is_null($id_documento_referencia_inventario == '')){
			$camposUpdate  = "id_documento_referencia          = '$id_documento_referencia',
												documento_referencia             = '$documento_referencia',
												documento_referencia_consecutivo = '$documento_referencia_consecutivo',
												costo                            = '$costo',
												id_proveedor                     = '$id_proveedor',
												nit_proveedor                    = '$nit_proveedor',
												proveedor                        = '$proveedor',";
		}

		$sql = "UPDATE activos_fijos
						SET
							$camposUpdate
							id_familia                     = '$id_familia',
							codigo_familia                 = '$codigo_familia',
							familia                        = '$familia',
							id_grupo                       = '$id_grupo',
							codigo_grupo                   = '$codigo_grupo',
							grupo                          = '$grupo',
							id_subgrupo                    = '$id_subgrupo',
							codigo_subgrupo                = '$codigo_subgrupo',
							subgrupo                       = '$subgrupo',
							id_item                        = '$id_item',
							code_bar                       = '$codigo_item',
							codigo_automatico              = '$codigo_automatico',
							codigo_activo                  = '$codigo_activo',
							nombre_equipo                  = '$nombre_equipo',
							tipo                           = '$tipo',
							fecha_compra                   = '$fecha_compra',
							id_centro_costos               = '$id_centro_costos',
							codigo_centro_costos           = '$codigo_centro_costos',
							centro_costos                  = '$centro_costos',
							fecha_vencimiento_garantia     = '$fecha_vencimiento_garantia',
							id_funcionario_asignado        = '$id_funcionario_asignado',
							documento_funcionario_asignado = '$documento_funcionario_asignado',
							funcionario_asignado           = '$funcionario_asignado',
							numero_serial                  = '$numero_serial',
							numero_placa                   = '$numero_placa',
							marca                          = '$marca',
							modelo                         = '$modelo',
							color                          = '$color',
							chasis                         = '$chasis',
							id_unidad                      = '$id_unidad',
							unidad                         = '$unidad',
							numero_piezas                  = '$numero_piezas',
							longitud                       = '$longitud',
							ancho                          = '$ancho',
							alto                           = '$alto',
							volumen                        = '$volumen',
							peso                           = '$peso',
							descripcion1                   = '$descripcion1',
							descripcion2                   = '$descripcion2',
							tenencia                       = '$tenencia',
							fecha_vencimiento_tenencia     = '$fecha_vencimiento_tenencia',
							vida_util                      = '$vida_util',
							vida_util_niif                 = '$vida_util_niif',
							sincronizar_siip               = '$sincronizar_siip',
							estado                         = '1'
						WHERE	activo    = 1
						AND id_empresa  = $id_empresa
						AND id_sucursal = $id_sucursal
						AND id_bodega   = $id_bodega
						AND id          = $id_activo";
		$query = $mysql->query($sql,$mysql->link);

		if($query){

			if($sincronizar_siip == "si"){
				include_once("../../external_apis/LOGICALHOTELS/backend/ClassExternalApis.php");

				$datosActivoFijo = array(
					"codigo" => "$codigo_activo",
					"id_empresa" => $_SESSION['ID_HOST'],
					"id_sucursal" => $id_sucursal,
					"id_bodega" => $id_bodega,
					"cod_grupo" => $codigo_grupo,
					"cod_subgrupo" => $codigo_subgrupo,
					"id_centro_costos" => $id_centro_costos,
					"cod_centro_costos" => $codigo_centro_costos,
					"nom_centro_costos" => "$centro_costos",
					"nombre_equipo" => "$nombre_equipo",
					"marca" => "$marca",
					"modelo" => "$modelo",
					"serie" => "$numero_serial",
					"color" => "$color",
					"numero_piezas" => $numero_piezas,
					"descripcion1" => "$descripcion1",
					"descripcion2" => "$descripcion2",
					"nit_proveedor" => "$nit_proveedor",
					"fecha_compra" => "$fecha_compra",
					"fecha_vencimiento_garantia" => "$fecha_vencimiento_garantia",
					"tenencia" => $tenencia,
					"fecha_vencimiento_tenencia" => "$fecha_vencimiento_tenencia",
					"documento_contable" => "$documento_referencia",
					"numero_documento" => "$documento_referencia_consecutivo",
					"costos" => $costo,
					"vida_util" => $vida_util_niif
				);

				$server_name = $_SERVER['SERVER_NAME'];

				if($server_name == "logicalerp.localhost"){
					$url_api = "http://siip.localhost/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}
				else{
					$url_api = "http://siip.plataforma.co/SIIP/webservice_erp/api_siip/eliminarInventarios";
				}

				$params = [];
				$params['request_url']    = $url_api;
				$params['request_method'] = ($estado == 0)? "POST" : "PUT";
				$params['Authorization']  = "";
				$params['data']           = json_encode($datosActivoFijo);

				$nuevoActivo = new ClassExternalApis($id_sucursal,$id_empresa,$mysql);
				$respuesta = $nuevoActivo->curlApi($params);

				$respuesta = json_decode($respuesta,true);

				// GUARDAMOS LA RESPUESTA DEL API
				$sql = "UPDATE activos_fijos
								SET estado_sincronizar_siip = '$respuesta[estado]'
								WHERE id = $id_activo
								AND activo = 1
								AND id_empresa = $id_empresa
								AND id_sucursal = $id_sucursal
								AND id_bodega = $id_bodega";
				$query = $mysql->query($sql,$mysql->link);

				if($respuesta['estado'] == "error" || $respuesta['estado'] == "existe"){
					$sql = "UPDATE activos_fijos
									SET estado = 0
									WHERE id = $id_activo
									AND activo = 1
									AND id_empresa = $id_empresa
									AND id_sucursal = $id_sucursal
									AND id_bodega = $id_bodega";
					$query = $mysql->query($sql,$mysql->link);

					echo "<script>
									alert('$respuesta[msg]');
									MyLoading2('off',{texto:'El activo fijo se actualizo, pero no fue sincronizado con el SIIP.'});
									Win_ActivosFijos.close();
								</script>";
				}
				else if($respuesta['estado'] == "no_existe"){
					$params['request_method'] = "POST";
					$respuesta = $nuevoActivo->curlApi($params);

					$respuesta = json_decode($respuesta,true);
					if($respuesta['estado'] == "true"){
						// GUARDAMOS LA RESPUESTA DEL API
						$sql = "UPDATE activos_fijos
										SET estado_sincronizar_siip = '$respuesta[estado]'
										WHERE id = $id_activo
										AND activo = 1
										AND id_empresa = $id_empresa
										AND id_sucursal = $id_sucursal
										AND id_bodega = $id_bodega";
						$query = $mysql->query($sql,$mysql->link);

						echo "<script>
										MyLoading2('off',{texto:'Activo registrado y sincronizado con el SIIP'});
										Actualiza_Div_ActivosFijos($id_activo);
										Win_ActivosFijos.close();
									</script>";
					} else{
						echo "<script>
										MyLoading2('off',{texto:'Activo no fue sincronizado con el SIIP'});
										Actualiza_Div_ActivosFijos($id_activo);
										Win_ActivosFijos.close();
									</script>";
					}
				}
				else{
					echo "<script>
									MyLoading2('off',{texto:'Activo actualizado y sincronizado con el SIIP'});
									Actualiza_Div_ActivosFijos($id_activo);
									Win_ActivosFijos.close();
								</script>";
				}
			}
			else{
				echo "<script>
								MyLoading2('off',{texto:'Activo Actualizado'});
								Actualiza_Div_ActivosFijos($id_activo);
							</script>";
			}
		}
		else{
			echo "<script>
							MyLoading2('off',{texto:'Error al actualizar',icono : 'fail',duracion:2000});
						</script>";
		}
	}

	/////////////////////////////////////
	//	     PESTAÑA CONTABILIDAD      //
	/////////////////////////////////////

	// CARGAR LAS CUENTAS POR DEFECTO DEL GRUPO DEL ACTIVO
	function cargarCuentasDefaultGrupos($id_activo,$id_empresa,$mysql){
		// CONSULTAR EL GRUPO DEL ACTIVO
		$sql="SELECT id_grupo FROM activos_fijos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_activo";
		$query=$mysql->query($sql,$mysql->link);
		$id_grupo = $mysql->result($query,0,'id_grupo');

		$cargaCuentas = guardarActualizarCuentasActivoDefault($id_activo,$id_grupo,$id_empresa,$mysql);
		if ($cargaCuentas==true) {
			echo "<script>
					MyLoading2('off',{texto:'Cuentas Cargadas'});
					Actualiza_Div_ActivosFijos($id_activo);
				</script>";
		}
		else{
			echo "<script>MyLoading2('off',{texto:'Error al cargar cuentas',icono : 'fail',duracion:2000});</script>";
		}
	}

	// GUARDAR O ACTUALIZAR LA INFORMACION DE LA PESTAÑA DE CONTABILIDAD
	function guardaActualizaContabilidad($id_activo,$depreciable,$fecha_inicio_depreciacion,$valor_salvamento,$metodo_depreciacion_colgaap,$depreciable_niif,$fecha_inicio_depreciacion_niif,$valor_salvamento_niif,$metodo_depreciacion_niif,$deteriorable,$cuentas,$id_sucursal,$id_bodega,$id_empresa,$mysql){
		$arrayCuentas = json_decode($cuentas,true);

		$sql = "UPDATE activos_fijos
						SET
							depreciable                    = '$depreciable',
							fecha_inicio_depreciacion      = '$fecha_inicio_depreciacion',
							valor_salvamento               = '$valor_salvamento',
							metodo_depreciacion_colgaap    = '$metodo_depreciacion_colgaap',
							depreciable_niif               = '$depreciable_niif',
							fecha_inicio_depreciacion_niif = '$fecha_inicio_depreciacion_niif',
							valor_salvamento_niif          = '$valor_salvamento_niif',
							metodo_depreciacion_niif       = '$metodo_depreciacion_niif',
							deteriorable                   = '$deteriorable'
						WHERE	activo    = 1
						AND id_empresa  = $id_empresa
						AND id_sucursal = $id_sucursal
						AND id_bodega   = $id_bodega
						AND id          = $id_activo";
		$query = $mysql->query($sql,$mysql->link);

		// CONSULTAR LAS CUENTAS PARA NO ACTUALIZARLAS SI ESTAN INSERTADAS
		$sql = "SELECT descripcion,estado,contabilidad FROM activos_fijos_cuentas WHERE activo = 1 AND id_activo = $id_activo AND id_empresa = $id_empresa";
		$query = $mysql->query($sql,$mysql->link);

		while($row = $mysql->fetch_array($query)){
			$descripcion  = $row['descripcion'];
			$estado       = $row['estado'];
			$contabilidad = $row['contabilidad'];

			$arrayCuentasUpdate[$contabilidad][$estado][$descripcion] = true;
		}

		foreach ($arrayCuentas as $contabilidad => $arrayNaturaleza) {
			foreach ($arrayNaturaleza as $naturaleza => $arrayDocumento) {
				foreach ($arrayDocumento as $documento => $cuentasArray) {
					// SI EL REGISTRO YA EXISTE EN LAS CUENTAS DEL ACTIVO, NO SE DEBE INSERTAR SINO ACTUALIZAR LA CUENTA
					if($arrayCuentasUpdate[$contabilidad][$naturaleza][$documento] == true){
						$sql = "UPDATE activos_fijos_cuentas
										SET
										id_cuenta          = '$cuentasArray[id_cuenta]',
										cuenta             = '$cuentasArray[cuenta]',
										descripcion_cuenta = '$cuentasArray[descripcion_cuenta]'
										WHERE activo		 = 1
										AND id_activo    = '$id_activo'
										AND id_empresa   = $id_empresa
										AND descripcion  = '$documento'
										AND estado       = '$naturaleza'
										AND contabilidad = '$contabilidad'";
						$query = $mysql->query($sql,$mysql->link);
						continue;
					}
					// CREAR STRING PARA INSERTAR LAS CUENTAS INEXISTENTES
					$valueInsert .= "(
														'$id_activo',
														'$documento',
														'$naturaleza',
														'$cuentasArray[id_cuenta]',
														'$cuentasArray[cuenta]',
														'$cuentasArray[descripcion_cuenta]',
														'$contabilidad',
														'$id_empresa'
													),";
				}
			}
		}

		// INSERTAR LAS CUENTAS DEL ACTIVO
		if($valueInsert != ''){
			$valueInsert = substr($valueInsert,0,-1);
			$sql = "INSERT INTO
								activos_fijos_cuentas(
									id_activo,
									descripcion,
									estado,
									id_cuenta,
									cuenta,
									descripcion_cuenta,
									contabilidad,
									id_empresa
								)
							VALUES
								$valueInsert";
			$query = $mysql->query($sql,$mysql->link);
		}

		if($query){
			echo "<script>
							MyLoading2('off',{texto:'Informacion actualizada'});
							Actualiza_Div_ActivosFijos($id_activo);
						</script>";
		}
		else{
			echo "<script>MyLoading2('off',{texto:'Error al actualizar',icono : 'fail',duracion:2000});</script>";
		}
	}

	// GUARDAR O ACTUALIZAR LAS CUENTAS DE UN ACTIVO CON LAS DEL GRUPO QUE RELACIONA
	function guardarActualizarCuentasActivoDefault($id_activo,$id_grupo,$id_empresa,$mysql){
		// CONSULTAR LAS CUENTAS PARA NO ACTUALIZARLAS SI ESTAN INSERTADAS
		$sql="SELECT descripcion,estado,contabilidad FROM activos_fijos_cuentas WHERE activo=1 AND id_activo=$id_activo AND id_empresa=$id_empresa";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$descripcion  = $row['descripcion'];
			$estado       = $row['estado'];
			$contabilidad = $row['contabilidad'];

			$arrayCuentas[$contabilidad][$estado][$descripcion]=true;
		}

		// CONSULTAR LAS CUENTAS POR DEFECTO DEL GRUPO PARA INSERTARLAS
		$sql="SELECT
					id_grupo,
					grupo,
					descripcion,
					SUBSTRING(descripcion, 19) AS tipo,
					estado,
					id_cuenta,
					cuenta,
					detalle_cuenta
				FROM asientos_colgaap_default_grupos
				WHERE
					activo=1
				AND id_empresa = $id_empresa
				AND id_grupo   = $id_grupo
				AND descripcion LIKE 'items_activo_fijo_%'
				";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$tipo = explode("_", $row['tipo']);

			$descripcion  = $tipo[0];
			$estado       = $row['estado'];

			$acumScript .="
							document.getElementById('$tipo[0]_$row[estado]_colgaap').value='$row[cuenta] - $row[detalle_cuenta]';
							document.getElementById('$tipo[0]_$row[estado]_colgaap').dataset.id_cuenta   ='$row[id_cuenta]';
							document.getElementById('$tipo[0]_$row[estado]_colgaap').dataset.cuenta      ='$row[cuenta]';
							document.getElementById('$tipo[0]_$row[estado]_colgaap').dataset.descripcion ='$row[detalle_cuenta]';
							";

			// SI EL REGISTRO YA EXISTE EN LAS CUENTAS DEL ACTIVO, NO SE DEBE INSERTAR SINO ACTUALIZAR LA CUENTA
			if ($arrayCuentas['colgaap'][$estado][$descripcion]==true) {
				$sql_update="UPDATE activos_fijos_cuentas
						SET
						id_cuenta          = '$row[id_cuenta]',
						cuenta             = '$row[cuenta]',
						descripcion_cuenta = '$row[detalle_cuenta]'
						WHERE activo=1
							AND id_activo    = '$id_activo'
							AND id_empresa   = $id_empresa
							AND descripcion  ='$descripcion'
							AND estado       ='$estado'
							AND contabilidad ='colgaap' ";
				$query_update=$mysql->query($sql_update,$mysql->link);
				continue;
			}

			// CREAR STRING PARA INSERTAR LAS CUENTAS INEXISTENTES
			$valueInsert .= "(
								'$id_activo',
								'$tipo[0]',
								'$row[estado]',
								'$row[id_cuenta]',
								'$row[cuenta]',
								'$row[detalle_cuenta]',
								'colgaap',
								'$id_empresa'
							),";

		}

		$sql="SELECT
					id_grupo,
					grupo,
					descripcion,
					SUBSTRING(descripcion, 19) AS tipo,
					estado,
					id_cuenta,
					cuenta,
					detalle_cuenta
				FROM asientos_niif_default_grupos
				WHERE
					activo=1
				AND id_empresa = $id_empresa
				AND id_grupo   = $id_grupo
				AND descripcion LIKE 'items_activo_fijo_%'
				";
		$query=$mysql->query($sql,$mysql->link);
		while ($row=$mysql->fetch_array($query)) {
			$tipo = explode("_", $row['tipo']);

			$descripcion  = $tipo[0];
			$estado       = $row['estado'];
			$acumScript .="
							document.getElementById('$tipo[0]_$row[estado]_niif').value='$row[cuenta] - $row[detalle_cuenta]';
							document.getElementById('$tipo[0]_$row[estado]_niif').dataset.id_cuenta   ='$row[id_cuenta]';
							document.getElementById('$tipo[0]_$row[estado]_niif').dataset.cuenta      ='$row[cuenta]';
							document.getElementById('$tipo[0]_$row[estado]_niif').dataset.descripcion ='$row[detalle_cuenta]';
							";

			// SI EL REGISTRO YA EXISTE EN LAS CUENTAS DEL ACTIVO, NO SE DEBE INSERTAR SINO ACTUALIZAR LA CUENTA
			if ($arrayCuentas['niif'][$estado][$descripcion]==true) {
				$sql_update="UPDATE activos_fijos_cuentas
						SET
						id_cuenta          = '$row[id_cuenta]',
						cuenta             = '$row[cuenta]',
						detalle_cuenta = '$row[detalle_cuenta]'
						WHERE activo=1
							AND id_activo    = '$id_activo'
							AND id_empresa   = $id_empresa
							AND descripcion  ='$descripcion'
							AND estado       ='$estado'
							AND contabilidad ='niif' ";
				$query_update=$mysql->query($sql_update,$mysql->link);
				continue;
			}

			// CREAR STRING PARA INSERTAR LAS CUENTAS INEXISTENTES
			$valueInsert .= "(
								'$id_activo',
								'$tipo[0]',
								'$row[estado]',
								'$row[id_cuenta]',
								'$row[cuenta]',
								'$row[detalle_cuenta]',
								'niif',
								'$id_empresa'
							),";
		}

		// INSERTAR LAS CUENTAS DEL ACTIVO
		if ($valueInsert<>'') {
			$valueInsert = substr($valueInsert,0,-1);
			$sql="INSERT INTO activos_fijos_cuentas
					(
						id_activo,
						descripcion,
						estado,
						id_cuenta,
						cuenta,
						descripcion_cuenta,
						contabilidad,
						id_empresa
					)
					VALUES $valueInsert";
			$query=$mysql->query($sql,$mysql->link);

			if (!$query) { return false; }



		}

		echo "<script>
						$acumScript
					</script>";
		return true;
	}

?>
