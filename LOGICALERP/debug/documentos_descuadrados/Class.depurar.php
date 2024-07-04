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
		* @method getDocumentosDescuadrados consulta documentos descuadrados
		*/
		public function getDocumentosDescuadrados()
		{
			$sql="SELECT
						SUM(debe-haber) AS diferencia,
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido
					FROM asientos_niif
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					GROUP BY id_documento,tipo_documento
					HAVING SUM(debe-haber)<>0
					";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id_documento = $row['id_documento'];
				$this->arrayDocumentosDescuadrados[$id_documento] = array(
																			'consecutivo_documento'    => $row['consecutivo_documento'],
																			'tipo_documento'           => $row['tipo_documento'],
																			'tipo_documento_extendido' => $row['tipo_documento_extendido'],
																		);
				$this->whereDocumentos .= ($this->whereDocumentos=='')? "(id_documento=$id_documento AND tipo_documento='$row[tipo_documento]')" : " OR (id_documento=$id_documento AND tipo_documento='$row[tipo_documento]')" ;
				$this->whereDocumentosColgaap .= ($this->whereDocumentosColgaap=='')? "(A.id_documento=$id_documento AND A.tipo_documento='$row[tipo_documento]')" : " OR (A.id_documento=$id_documento AND A.tipo_documento='$row[tipo_documento]')" ;
			}

		}

		/**
		* @method getAsientosDocumentos consulta los asientos de los documentos descuadrados
		*/
		public function getAsientosDocumentos()
		{
			$sql = "SELECT
						id,
						id_documento,
						consecutivo_documento,
						tipo_documento,
						tipo_documento_extendido,
						debe,
						haber,
						nit_tercero,
						tercero,
						codigo_cuenta,
						cuenta
					FROM asientos_niif
					WHERE activo=1
					AND id_empresa=$this->id_empresa
					AND ($this->whereDocumentos)
					ORDER BY CAST(codigo_cuenta AS CHAR) ASC";
			$query=$this->mysql->query($sql,$this->mysql->link);
			while ($row=$this->mysql->fetch_array($query)) {
				$id           = $row['id'];
				$id_documento = $row['id_documento'];
				$this->asientosNiif[$id_documento][$id] = array(
																'debe'          => $row['debe'],
																'haber'         => $row['haber'],
																'nit_tercero'   => $row['nit_tercero'],
																'tercero'       => $row['tercero'],
																'codigo_cuenta' => $row['codigo_cuenta'],
																'cuenta'        => $row['cuenta'],
																);
			}

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
		* @method depurar depurar los documentos
		* @return retorna los documentos descuadrados en Niif
		*/
		public function muestraDocumentosDepurar()
		{
			$this->getDocumentosDescuadrados();
			$this->getAsientosDocumentos();
			foreach ($this->arrayDocumentosDescuadrados as $id_documento => $detalles) {
				$bodyTable .= "<tr class='info-documento'>
									<td>$id_documento</td>
									<td>$detalles[consecutivo_documento]</td>
									<td>$detalles[tipo_documento]</td>
									<td>$detalles[tipo_documento_extendido]</td>
								</tr>
								<tr>
									<td >
										<table>
											<thead>
												<tr>
													<td colspan=6>NIIF</td>
												</tr>
												<tr>
													<td>Debe</td>
													<td>Haber</td>
													<td>Nit</td>
													<td>Tercero</td>
													<td>Cuenta</td>
													<td>Descripcion</td>
												</tr>
											</thead>
											<tbody>
								";

				foreach ($this->asientosNiif[$id_documento] as $id => $asientos) {
					$style = '';
					// COMPARAR CUENTAS
					foreach ($this->asientosColgaap[$id_documento] as $idComparar => $asientosComparar) {
						if ($asientos['codigo_cuenta'] == $asientosComparar['cuenta_niif'] && $asientos['nit_tercero'] == $asientosComparar['nit_tercero']) {
							if ($asientos['debe']<> $asientosComparar['debe'] || $asientos['haber']<>$asientosComparar['haber']) {
								$style="background-color : #f99595;";

								$bodyTableDif .= "<tr>
													<td>$id_documento</td>
													<td>$asientos[codigo_cuenta]</td>
													<td>$asientos[nit_tercero]</td>
													<td>$asientos[tercero]</td>
													<td>$asientosComparar[cuenta_niif]</td>
													<td>$asientosComparar[nit_tercero]</td>
													<td>$asientosComparar[tercero]</td>
													<td>$asientos[debe]</td>
													<td>$asientosComparar[debe]</td>
													<td>$asientos[haber]</td>
													<td>$asientosComparar[haber]</td>
												</tr>";
								// $arrayDiferencia[$id_documento][$id] = array(
								// 												//
								// 											);
							}
						}
					}


					$bodyTable .= "<tr>
										<td style='$style' >$asientos[debe]</td>
										<td style='$style' >$asientos[haber]</td>
										<td style='$style' >$asientos[nit_tercero]</td>
										<td style='$style' >$asientos[tercero]</td>
										<td style='$style' >$asientos[codigo_cuenta]</td>
										<td style='$style' >$asientos[cuenta]</td>
								</tr>";
				}


				$bodyTable .= "				</tbody>
										</table>
									</td>
									<!--<td>
										<table>
											<thead>
												<tr>
													<td>Documento Colgaap</td>
													<td>Cuenta Colgaap</td>
													<td>Nit</td>
													<td>Tercero</td>
													<td>Cuenta Niif</td>
													<td>Nit</td>
													<td>Tercero</td>
													<td>debe Colgaap </td>
													<td>debe Niif</td>
													<td>haber Colgaap</td>
													<td>haber Niif</td>
												</tr>
											</thead>
											<tbody>
												$bodyTableDif
											</tbody>
										</table>
									</td>-->
									<td >
										<table>
											<thead>
												<tr>
													<td colspan=6>COLGAAP</td>
												</tr>
												<tr>
													<td>Debe</td>
													<td>Haber</td>
													<td>Nit</td>
													<td>Tercero</td>
													<td>Cuenta</td>
													<td>Descripcion</td>
												</tr>
											</thead>
											<tbody>";
				$bodyTableDif = '';

				foreach ($this->asientosColgaap[$id_documento] as $id => $asientos) {
					$bodyTable .= "<tr>
										<td>$asientos[debe]</td>
										<td>$asientos[haber]</td>
										<td>$asientos[nit_tercero]</td>
										<td>$asientos[tercero]</td>
										<td>$asientos[codigo_cuenta]</td>
										<td>$asientos[cuenta]</td>
								</tr>";
				}

				$bodyTable .= "				</tbody>
										</table>
									</td>
								</tr>";

			}

			$bodyTable= "<table>
							<thead>
								<tr>
									<td>ID</td>
									<td>Consecutivo</td>
									<td>Tipo</td>
									<td>Documento</td>
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
		* @return retorna los documentos descuadrados en Niif
		*/
		public function muestraDocumentosDepurarXls()
		{
			$this->getDocumentosDescuadrados();
			$this->getAsientosDocumentos();
			foreach ($this->arrayDocumentosDescuadrados as $id_documento => $detalles) {

				foreach ($this->asientosNiif[$id_documento] as $id => $asientos) {

					$bodyTable .= "<tr>
										<td>NIIF</td>
										<td>$id_documento</td>
										<td>$detalles[consecutivo_documento]</td>
										<td>$detalles[tipo_documento]</td>
										<td>$detalles[tipo_documento_extendido]</td>
										<td style='$style' >$asientos[debe]</td>
										<td style='$style' >$asientos[haber]</td>
										<td style='$style' >$asientos[nit_tercero]</td>
										<td style='$style' >$asientos[tercero]</td>
										<td style='$style' >$asientos[codigo_cuenta]</td>
										<td style='$style' >$asientos[cuenta]</td>
								</tr>";
				}

				foreach ($this->asientosColgaap[$id_documento] as $id => $asientos) {
					$bodyTable .= "<tr>
										<td>COLGAAP</td>
										<td>$id_documento</td>
										<td>$detalles[consecutivo_documento]</td>
										<td>$detalles[tipo_documento]</td>
										<td>$detalles[tipo_documento_extendido]</td>
										<td>$asientos[debe]</td>
										<td>$asientos[haber]</td>
										<td>$asientos[nit_tercero]</td>
										<td>$asientos[tercero]</td>
										<td>$asientos[codigo_cuenta]</td>
										<td>$asientos[cuenta]</td>
								</tr>";
				}


			}

			header('Content-type: application/vnd.ms-excel');
   			header("Content-Disposition: attachment; filename=documentos depurados_$this->id_empresa.xls");
   			header("Pragma: no-cache");
   			header("Expires: 0");

			$bodyTable= "<table>
							<thead>
								<tr>
									<td>COLGAAP</td>
									<td>id documento</td>
									<td>consecutivo_documento</td>
									<td>tipo_documento</td>
									<td>tipo_documento_extendido</td>
									<td>debe</td>
									<td>haber</td>
									<td>nit_tercero</td>
									<td>tercero</td>
									<td>codigo_cuenta</td>
									<td>cuenta</td>
								</tr>
							</thead>
							<tbody>
								$bodyTable
							</tbody>
						</table>";
			echo $bodyTable;
		}

		/**
		* @method setSql generar los sql para la depuracion
		*/
		public function setAsientos()
		{
			foreach ($this->arrayDocumentosDescuadrados as $id_documento => $detalles) {
				$whereAsientos .= ($whereAsientos=='')? "(id_documento='$id_documento' AND consecutivo_documento='$detalles[consecutivo_documento]' AND tipo_documento='$detalles[tipo_documento]')"
														: " OR (id_documento='$id_documento' AND consecutivo_documento='$detalles[consecutivo_documento]' AND tipo_documento='$detalles[tipo_documento]')" ;
				foreach ($this->asientosColgaap[$id_documento] as $id => $arrayResul) {
					$valueInsert .= "(
										$id_documento,
										'$arrayResul[consecutivo_documento]',
										'$arrayResul[tipo_documento]',
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
										'Depurados'

									),";
				}
			}

			echo$sql = "UPDATE asientos_niif SET activo=0,observacion='Depurados' WHERE activo=1 AND id_empresa=$this->id_empresa AND ($whereAsientos)";
			$query=$this->mysql->query($sql,$this->mysql->link);

			$valueInsert = substr($valueInsert, 0, -1);
			echo$sql = "INSERT INTO asientos_niif
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

		}

		/**
		* @method depurar depurar los documentos
		*/
		public function depurar()
		{
			$this->muestraDocumentosDepurarXls();
			$this->setAsientos();
		}

	}


?>