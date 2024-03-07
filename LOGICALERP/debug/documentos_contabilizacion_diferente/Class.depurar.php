<?php



	/**
	* @class depuraDocumentos Clase para depurara los documentos en niif descuadros
	*/
	class depuraDocumentos
	{

		private $mysql;
		private $id_empresa;
		private $arrayDocumentosDescuadrados;

		/**
		* @method __construct constructor de la clase
		* @param obj objeto de conexion mysql
		* @param int id de empresa a depurar
		*/
		function __construct($mysql,$id_empresa)
		{
			$this->mysql      = $mysql;
			$this->id_empresa = $id_empresa;
		}

		/**
		* @method getDocumentos consulta documentos descuadrados
		*/
		public function getDocumentos()
		{

			set_time_limit(0);
			ini_set("memory_limit", "1024M");

			$sql="SELECT
						SUM(debe-haber) AS diferencia,
						SUM(debe) AS debe,
						SUM(haber) AS haber,
						codigo_cuenta,
						cuenta,
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido,
						nit_tercero,
						tercero,
						id_documento_cruce,
						tipo_documento_cruce,
						numero_documento_cruce,
						sucursal
					FROM asientos_colgaap
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					GROUP BY id_documento,tipo_documento,codigo_cuenta
					;
					";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_documento  = $row['id_documento'];
				$codigo_cuenta = $row['codigo_cuenta'];
				$tipo_documento = $row['tipo_documento'];

				if ($tipo_documento=='NCG') {
					# code...
				}
				else if ($tipo_documento=='NDFV') {
					$this->arrayDocumentosColgaapNDFVColgaap[$id_documento][$tipo_documento][$codigo_cuenta] = array(
																							'diferencia'               => $row['diferencia'],
																							'debe'                     => $row['debe'],
																							'haber'                    => $row['haber'],
																							'consecutivo_documento'    => $row['consecutivo_documento'],
																							'tipo_documento'           => $row['tipo_documento'],
																							'tipo_documento_extendido' => $row['tipo_documento_extendido'],
																							'nit_tercero'              => $row['nit_tercero'],
																							'tercero'                  => $row['tercero'],
																							'codigo_cuenta'            => $row['codigo_cuenta'],
																							'cuenta'                   => $row['cuenta'],
																						);

					$this->whereNotasDevolucion .= ($this->whereNotasDevolucion=='')? "id=$id_documento " : " OR id=$id_documento" ;

				}
				else{
					$this->arrayDocumentosColgaap[$id_documento][$tipo_documento][$codigo_cuenta] = array(
																							'diferencia'               => $row['diferencia'],
																							'debe'                     => $row['debe'],
																							'haber'                    => $row['haber'],
																							'consecutivo_documento'    => $row['consecutivo_documento'],
																							'tipo_documento'           => $row['tipo_documento'],
																							'tipo_documento_extendido' => $row['tipo_documento_extendido'],
																							'nit_tercero'              => $row['nit_tercero'],
																							'tercero'                  => $row['tercero'],
																							'codigo_cuenta'            => $row['codigo_cuenta'],
																							'cuenta'                   => $row['cuenta'],
																							'sucursal'                 => $row['sucursal'],
																						);
				}
				// $this->whereDocumentosColgaap .= ($this->whereDocumentosColgaap=='')? "(A.id_documento=$id_documento AND A.tipo_documento='$row[tipo_documento]')" : " OR (A.id_documento=$id_documento AND A.tipo_documento='$row[tipo_documento]')" ;
			}

			$sql="SELECT
						SUM(debe-haber) AS diferencia,
						SUM(debe) AS debe,
						SUM(haber) AS haber,
						codigo_cuenta,
						cuenta,
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido,
						nit_tercero,
						tercero,
						id_documento_cruce,
						tipo_documento_cruce,
						numero_documento_cruce
					FROM asientos_niif
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					GROUP BY id_documento,tipo_documento,codigo_cuenta
					;
					";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_documento   = $row['id_documento'];
				$codigo_cuenta  = $row['codigo_cuenta'];
				$tipo_documento = $row['tipo_documento'];

				if ($tipo_documento=='NCG') {
					# code...
				}
				else if ($tipo_documento=='NDFV') {
					$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$codigo_cuenta] = array(
																							'diferencia'               => $row['diferencia'],
																							'debe'                     => $row['debe'],
																							'haber'                    => $row['haber'],
																							'consecutivo_documento'    => $row['consecutivo_documento'],
																							'tipo_documento'           => $row['tipo_documento'],
																							'tipo_documento_extendido' => $row['tipo_documento_extendido'],
																							'nit_tercero'              => $row['nit_tercero'],
																							'tercero'                  => $row['tercero'],
																							'codigo_cuenta'            => $row['codigo_cuenta'],
																							'cuenta'                   => $row['cuenta'],
																						);

					$this->whereNotasDevolucion .= ($this->whereNotasDevolucion=='')? "id=$id_documento " : " OR id=$id_documento" ;

				}
				else{
					$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$codigo_cuenta] = array(
																							'diferencia'               => $row['diferencia'],
																							'debe'                     => $row['debe'],
																							'haber'                    => $row['haber'],
																							'consecutivo_documento'    => $row['consecutivo_documento'],
																							'tipo_documento'           => $row['tipo_documento'],
																							'tipo_documento_extendido' => $row['tipo_documento_extendido'],
																							'nit_tercero'              => $row['nit_tercero'],
																							'tercero'                  => $row['tercero'],
																							'codigo_cuenta'            => $row['codigo_cuenta'],
																							'cuenta'                   => $row['cuenta'],
																						);
				}

			}

			foreach ($this->arrayDocumentosColgaap as $id_documento => $arrayDoc) {

				foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
					$debe      = 0;
					$haber     = 0;
					$debeNiif  = 0;
					$haberNiif = 0;
					$cont      = 0;

					foreach ($arrayDoc2 as $cuenta => $arrayColgaap) {
						$debe  += $arrayColgaap['debe'];
						$haber += $arrayColgaap['haber'];

						// if ($this->arrayDocumentosNiif[$id_documento][$cuenta]['debe']<>$arrayColgaap['debe'] && $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayColgaap['haber']) {
						// 	if ($this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
						// 	$this->whereDocumentosColgaap .= ($this->whereDocumentosColgaap=='')? "(A.id_documento=$id_documento AND A.tipo_documento='$tipo_documento')" : " OR (A.id_documento=$id_documento AND A.tipo_documento='$tipo_documento')" ;
						// 	$this->arrayDocumentosDescuadrados[$id_documento] = array(
						// 															'consecutivo_documento'    => $arrayColgaap['consecutivo_documento'],
						// 															'tipo_documento'           => $arrayColgaap['tipo_documento'],
						// 															'tipo_documento_extendido' => $arrayColgaap['tipo_documento_extendido'],
						// 														);
						// }
					}

					foreach ($this->arrayDocumentosNiif[$id_documento][$tipo_documento] as $cuenta => $arrayNiif) {
						$debeNiif  += $arrayNiif['debe'];
						$haberNiif += $arrayNiif['haber'];
					}

					if ($debe <> $debeNiif || $haber <> $haberNiif) {
						foreach ($this->arrayDocumentosColgaap[$id_documento][$tipo_documento] as $cuenta => $arrayColgaap) {
							// echo  'consecutivo_documento: '.$arrayColgaap['consecutivo_documento'].' - tipo_documento: '.$tipo_documento.' - debe: '.$debe.' - haber: '.$haber.' - debeNiif: '.$debeNiif.' - haberNiif: '.$haberNiif.'</br>';
							$this->arrayDocumentosDescuadrados[$id_documento][$tipo_documento] = array(
																										'consecutivo_documento'    => $arrayColgaap['consecutivo_documento'],
																										'tipo_documento'           => $arrayColgaap['tipo_documento'],
																										'tipo_documento_extendido' => $arrayColgaap['tipo_documento_extendido'],
																									);
						}
					}

					//

				}



				// $debe      = round($debe,2);
				// $debeNiif  = round($debeNiif,2);
				// $haber     = round($haber,2);
				// $haberNiif = round($haberNiif,2);

				// if ($debe <> $debeNiif || $haber <> $haberNiif) {

				// 	foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
				// 		foreach ($arrayDoc2 as $cuenta => $arrayColgaap) {
				// 			echo  'consecutivo_documento: '.$arrayColgaap['consecutivo_documento'].' - tipo_documento: '.$arrayColgaap['tipo_documento'].' - debe: '.$debe.' - haber: '.$haber.' - debeNiif: '.$debeNiif.' - haberNiif: '.$haberNiif.'</br>';

				// 			$this->arrayDocumentosDescuadrados[$id_documento][$tipo_documento] = array(
				// 																						'consecutivo_documento'    => $arrayColgaap['consecutivo_documento'],
				// 																						'tipo_documento'           => $arrayColgaap['tipo_documento'],
				// 																						'tipo_documento_extendido' => $arrayColgaap['tipo_documento_extendido'],
				// 																					);
				// 		}

				// 	}

				// }

			}

			foreach ($this->arrayDocumentosDescuadrados as $id_documento => $arrayResul) {
				foreach ($arrayResul as $tipo_documento => $arrayResul2) {
					$this->whereDocumentosColgaap .= ($this->whereDocumentosColgaap=='')? "(A.id_documento=$id_documento AND A.tipo_documento='$tipo_documento')" : " OR (A.id_documento=$id_documento AND A.tipo_documento='$tipo_documento')" ;
				}
			}

			// print_r($this->arrayDocumentosDescuadrados);

		}

		/**
		* @method getAsientosDocumentos consulta los asientos de los documentos descuadrados
		*/
		public function getAsientosDocumentos()
		{

			// CONSULTAR LAS CUENTAS DE ESOS DOCUMENTOS EN COLGAAP Y SU HOMOLOGA EN NIIF
			$sql = "SELECT
						A.id,
						A.id_documento,
						A.consecutivo_documento,
						A.tipo_documento,
						A.tipo_documento_extendido,
						A.id_documento_cruce,
						A.tipo_documento_cruce,
						A.numero_documento_cruce,
						A.fecha,
						A.debe,
						A.haber,
						A.id_cuenta,
						A.codigo_cuenta,
						A.cuenta,
						A.id_tercero,
						A.nit_tercero,
						A.tercero,
						A.id_sucursal,
						A.sucursal,
						A.permiso_sucursal,
						A.id_empresa,
						A.id_centro_costos,
						A.codigo_centro_costos,
						A.centro_costos,
						A.id_flujo_efectivo,
						A.flujo_efectivo,
						A.id_sucursal_cruce,
						A.sucursal_cruce,
						A.activo,
						A.observacion,
						P.cuenta_niif
					FROM asientos_colgaap AS A INNER JOIN puc AS P ON P.id=A.id_cuenta
					WHERE A.activo=1
					AND A.id_empresa=$this->id_empresa
					AND ($this->whereDocumentosColgaap)
					GROUP BY A.id
					ORDER BY CAST(A.codigo_cuenta AS CHAR) ASC";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id           = $row['id'];
				$id_documento = $row['id_documento'];
				$this->asientosColgaap[$id_documento][$id] = array(
																'id_documento'             => $row['id_documento'],
																'consecutivo_documento'    => $row['consecutivo_documento'],
																'tipo_documento'           => $row['tipo_documento'],
																'tipo_documento_extendido' => $row['tipo_documento_extendido'],
																'id_documento_cruce'       => $row['id_documento_cruce'],
																'tipo_documento_cruce'     => $row['tipo_documento_cruce'],
																'numero_documento_cruce'   => $row['numero_documento_cruce'],
																'fecha'                    => $row['fecha'],
																'debe'                     => $row['debe'],
																'haber'                    => $row['haber'],
																'id_cuenta'                => $row['id_cuenta'],
																'codigo_cuenta'            => $row['codigo_cuenta'],
																'cuenta'                   => $row['cuenta'],
																'id_tercero'               => $row['id_tercero'],
																'nit_tercero'              => $row['nit_tercero'],
																'tercero'                  => $row['tercero'],
																'id_sucursal'              => $row['id_sucursal'],
																'sucursal'                 => $row['sucursal'],
																'permiso_sucursal'         => $row['permiso_sucursal'],
																'id_empresa'               => $row['id_empresa'],
																'id_centro_costos'         => $row['id_centro_costos'],
																'codigo_centro_costos'     => $row['codigo_centro_costos'],
																'centro_costos'            => $row['centro_costos'],
																'id_flujo_efectivo'        => $row['id_flujo_efectivo'],
																'flujo_efectivo'           => $row['flujo_efectivo'],
																'id_sucursal_cruce'        => $row['id_sucursal_cruce'],
																'sucursal_cruce'           => $row['sucursal_cruce'],
																'activo'                   => $row['activo'],
																'observacion'              => $row['observacion'],
																'cuenta_niif'              => $row['cuenta_niif']
																);

			}
		}

		/**
		* @method setSql generar los sql para la depuracion
		*/
		public function setAsientos()
		{

			// print_r($this->arrayDocumentosDescuadrados);

			// ASIENTOS DE LOS DOCUMENTOS EXCEPTO LAS NOTAS DE DEVOLUCION
			foreach ($this->arrayDocumentosDescuadrados as $id_documento => $arrayResul) {
				foreach ($arrayResul as $tipo_documento => $detalles) {
					$whereAsientos .= ($whereAsientos=='')? "(id_documento='$id_documento' AND consecutivo_documento='$detalles[consecutivo_documento]' AND tipo_documento='$tipo_documento')"
														: " OR (id_documento='$id_documento' AND consecutivo_documento='$detalles[consecutivo_documento]' AND tipo_documento='$tipo_documento')" ;
					foreach ($this->asientosColgaap[$id_documento] as $id => $arrayResul) {
						$valueInsert .= "(
											$id_documento,
											'$arrayResul[consecutivo_documento]',
											'$tipo_documento',
											'$arrayResul[tipo_documento_extendido]',
											'$arrayResul[id_documento_cruce]',
											'$arrayResul[tipo_documento_cruce]',
											'$arrayResul[numero_documento_cruce]',
											'$arrayResul[fecha]',
											'$arrayResul[debe]',
											'$arrayResul[haber]',
											'$arrayResul[cuenta_niif]',
											'$arrayResul[id_tercero]',
											'$arrayResul[nit_tercero]',
											'$arrayResul[tercero]',
											'$arrayResul[id_sucursal]',
											'$arrayResul[sucursal]',
											'$arrayResul[permiso_sucursal]',
											'$arrayResul[id_empresa]',
											'$arrayResul[id_centro_costos]',
											'$arrayResul[codigo_centro_costos]',
											'$arrayResul[centro_costos]',
											'$arrayResul[id_flujo_efectivo]',
											'$arrayResul[flujo_efectivo]',
											'$arrayResul[id_sucursal_cruce]',
											'$arrayResul[sucursal_cruce]',
											'Depurados cuadrados'
										),";
					}
				}
			}

			$sql = "UPDATE asientos_niif SET activo=0,observacion='Depurados cuadrados Drop' WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereAsientos)";
			$query=$this->mysql->query($sql,$this->mysql->link);

			$valueInsert = substr($valueInsert, 0, -1);
			$sql = "INSERT INTO asientos_niif
											(
												id_documento,
												consecutivo_documento,
												tipo_documento,
												tipo_documento_extendido,
												id_documento_cruce,
												tipo_documento_cruce,
												numero_documento_cruce,
												fecha,
												debe,
												haber,
												codigo_cuenta,
												id_tercero,
												nit_tercero,
												tercero,
												id_sucursal,
												sucursal,
												permiso_sucursal,
												id_empresa,
												id_centro_costos,
												codigo_centro_costos,
												centro_costos,
												id_flujo_efectivo,
												flujo_efectivo,
												id_sucursal_cruce,
												sucursal_cruce,
												observacion
											) VALUES $valueInsert ";
			$query=$this->mysql->query($sql,$this->mysql->link);

			// PROCESAR LAS NOTAS DE DEVOLUCION
			$sql="SELECT id_documento_venta,estado,plantillas_id,id_cliente,documento_venta
					FROM devoluciones_venta
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($this->whereNotasDevolucion) ";
			// $query=$this->mysql->query($sql,$this->mysql->link);
		}



		/**
		* @method depurar depurar los documentos
		*/
		public function muestraDocumentosDepurar()
		{
			$this->getDocumentos();
			$id_doc_render = 0;
			// print_r($this->arrayDocumentosDescuadrados);

			foreach ($this->arrayDocumentosDescuadrados as $id_documento => $arrayResul) {
				foreach ($arrayResul as $tipo_documento => $arrayDoc2) {
						foreach ($this->arrayDocumentosColgaap[$id_documento][$tipo_documento] as $cuenta => $arrayColgaap) {
							if ($id_doc_render==$id_documento) { continue;}
							else{ $id_doc_render=$id_documento; }
							// if ($this->arrayDocumentosNiif[$id_documento][$cuenta]['debe']<>$arrayColgaap['debe'] && $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayColgaap['haber']) {
								// if ($this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
								$bodyTable .= "<tr>
							 					<td style='$style' >$id_documento $arrayColgaap[consecutivo_documento]</td>
							 					<td style='$style' >$arrayColgaap[tipo_documento] $arrayColgaap[sucursal]</td>
							 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
							 					<td style='$style' >$arrayColgaap[debe]</td>
							 					<td style='$style' >$arrayColgaap[haber]</td>
							 					<td style='$style' >$arrayColgaap[nit_tercero]</td>
							 					<td style='$style' >$arrayColgaap[tercero]</td>
							 					<td style='$style' >$cuenta</td>
							 					<td style='$style' >$arrayColgaap[cuenta]</td>
							 					<td>&nbsp;</td>
							 					<!--<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$cuenta][debe]."</td>-->
							 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
							 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
							 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['debe']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['tercero']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['codigo_cuenta']."</td>
							 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['cuenta']."</td>
							 				</tr>";
							// }
						// }
					}
				}
			}


			// foreach ($this->arrayDocumentosColgaap as $id_documento => $arrayDoc) {
			// 	foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
			// 		foreach ($arrayDoc2 as $cuenta => $arrayColgaap) {

			// 			if ($this->arrayDocumentosNiif[$id_documento][$cuenta]['debe']<>$arrayColgaap['debe'] && $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayColgaap['haber']) {
			// 				if ($this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
			// 				$bodyTable .= "<tr>
			// 			 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
			// 			 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
			// 			 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
			// 			 					<td style='$style' >$arrayColgaap[debe]</td>
			// 			 					<td style='$style' >$arrayColgaap[haber]</td>
			// 			 					<td style='$style' >$arrayColgaap[nit_tercero]</td>
			// 			 					<td style='$style' >$arrayColgaap[tercero]</td>
			// 			 					<td style='$style' >$cuenta</td>
			// 			 					<td style='$style' >$arrayColgaap[cuenta]</td>
			// 			 					<td>&nbsp;</td>
			// 			 					<!--<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$cuenta][debe]."</td>-->
			// 			 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
			// 			 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
			// 			 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['debe']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['tercero']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['codigo_cuenta']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['cuenta']."</td>
			// 			 				</tr>";
			// 			}
			// 		}
			// 	}
			// }

			// foreach ($this->arrayDocumentosColgaapNDFVColgaap as $id_documento => $arrayDoc) {
			// 	foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
			// 		foreach ($arrayDoc2 as $cuenta => $arrayResult) {

			// 			if ($this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['debe']<>$arrayResult['debe'] && $this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayResult['haber']) {
			// 				// if ($this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
			// 				$bodyTable .= "<tr>
			// 			 					<td style='$style' >$arrayResult[consecutivo_documento]</td>
			// 			 					<td style='$style' >$arrayResult[tipo_documento]</td>
			// 			 					<td style='$style' >$arrayResult[tipo_documento_extendido]</td>
			// 			 					<td style='$style' >$arrayResult[debe]</td>
			// 			 					<td style='$style' >$arrayResult[haber]</td>
			// 			 					<td style='$style' >$arrayResult[nit_tercero]</td>
			// 			 					<td style='$style' >$arrayResult[tercero]</td>
			// 			 					<td style='$style' >$cuenta</td>
			// 			 					<td style='$style' >$arrayResult[cuenta]</td>
			// 			 					<td>&nbsp;</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['consecutivo_documento']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['tipo_documento']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['tipo_documento_extendido']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['debe']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['haber']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['tercero']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['codigo_cuenta']."</td>
			// 			 					<td style='$style' >".$this->arrayDocumentosColgaapNDFVNiif[$id_documento][$tipo_documento][$cuenta]['cuenta']."</td>
			// 			 				</tr>";
			// 			}
			// 		}
			// 	}
			// }

			// print_r($this->arrayDocumentosColgaapNDFVColgaap);
			$bodyTable= "<table>
							<thead>
								<tr>
									<td >consecutivo</td>
				 					<td >tipo</td>
				 					<td >documento</td>
									<td >debe COLGAAP</td>
									<td >haber COLGAAP</td>
									<td >nit_tercero COLGAAP</td>
									<td >tercero COLGAAP</td>
									<td >codigo_cuenta COLGAAP</td>
									<td >cuenta COLGAAP</td>
									<td>&nbsp;</td>
									<td >consecutivo</td>
				 					<td >tipo</td>
				 					<td >documento</td>
									<td >debe NIIF</td>
									<td >haber NIIF</td>
									<td >nit_tercero NIIF</td>
									<td >tercero NIIF</td>
									<td >codigo_cuenta NIIF</td>
									<td >cuenta NIIF</td>
								</tr>
							</thead>
							<tbody>
								$bodyTable
							</tbody>
						</table>";
			echo $bodyTable;
		}

		/**
		* @method depurar depurar los documentos
		*/
		public function muestraDocumentosDepurarXls()
		{
			$this->getDocumentos();
			foreach ($this->arrayDocumentosColgaap as $id_documento => $arrayDoc) {
				foreach ($arrayDoc as $tipo_documento => $arrayDoc2) {
					foreach ($arrayDoc2 as $cuenta => $arrayColgaap) {

						if ($this->arrayDocumentosNiif[$id_documento][$cuenta]['debe']<>$arrayColgaap['debe'] && $this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']<>$arrayColgaap['haber']) {
							if ($this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']=='') { continue; }
							$bodyTable .= "<tr>
						 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
						 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
						 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
						 					<td style='$style' >$arrayColgaap[debe]</td>
						 					<td style='$style' >$arrayColgaap[haber]</td>
						 					<td style='$style' >$arrayColgaap[nit_tercero]</td>
						 					<td style='$style' >$arrayColgaap[tercero]</td>
						 					<td style='$style' >$cuenta</td>
						 					<td style='$style' >$arrayColgaap[cuenta]</td>
						 					<td>&nbsp;</td>
						 					<!--<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$cuenta][debe]."</td>-->
						 					<td style='$style' >$arrayColgaap[consecutivo_documento]</td>
						 					<td style='$style' >$arrayColgaap[tipo_documento]</td>
						 					<td style='$style' >$arrayColgaap[tipo_documento_extendido]</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['debe']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['haber']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['nit_tercero']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['tercero']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['codigo_cuenta']."</td>
						 					<td style='$style' >".$this->arrayDocumentosNiif[$id_documento][$tipo_documento][$cuenta]['cuenta']."</td>
						 				</tr>";
						}
					}
				}
			}

			$bodyTable= "<table>
							<thead>
								<tr>
									<td >consecutivo</td>
				 					<td >tipo</td>
				 					<td >documento</td>
									<td >debe COLGAAP</td>
									<td >haber COLGAAP</td>
									<td >nit_tercero COLGAAP</td>
									<td >tercero COLGAAP</td>
									<td >codigo_cuenta COLGAAP</td>
									<td >cuenta COLGAAP</td>
									<td>&nbsp;</td>
									<td >consecutivo</td>
				 					<td >tipo</td>
				 					<td >documento</td>
									<td >debe NIIF</td>
									<td >haber NIIF</td>
									<td >nit_tercero NIIF</td>
									<td >tercero NIIF</td>
									<td >codigo_cuenta NIIF</td>
									<td >cuenta NIIF</td>
								</tr>
							</thead>
							<tbody>
								$bodyTable
							</tbody>
						</table>";

			header('Content-type: application/vnd.ms-excel');
   			header("Content-Disposition: attachment; filename=documentos depurados_$this->id_empresa.xls");
   			header("Pragma: no-cache");
   			header("Expires: 0");

			echo $bodyTable;
		}

		/**
		* @method depurar depurar los documentos
		*/
		public function depurar()
		{
			$this->getDocumentos();
			$this->muestraDocumentosDepurarXls();
			$this->getAsientosDocumentos();
			$this->setAsientos();
		}

	}




?>