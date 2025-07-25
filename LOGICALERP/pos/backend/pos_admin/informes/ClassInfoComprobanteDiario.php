<?php

	/**
	 * ClassInfoComprobanteDiario informe de comprobante diario exigido por la dian
	 */
	class ClassInfoComprobanteDiario
	{

		public $fecha;
		public $id_empresa;
		public $datosEmpresa;
		public $mysql;

		function __construct($id_empresa,$fecha,$mysql){
			$this->fecha      = $fecha;
			$this->id_empresa = $id_empresa;
			$this->mysql      = $mysql;
		}

		/**
		 * getEmpresaInfo Consultar la informacion de la empresa
		 */
		public function getEmpresaInfo(){
			$sql   = "SELECT nombre,tipo_documento_nombre,nit_completo,razon_social FROM empresas WHERE activo=1 AND id=$this->id_empresa";
			$query = $this->mysql->query($sql);
			$this->datosEmpresa['nombre']       = $this->mysql->result($query,0,'nombre');
			$this->datosEmpresa['tipo_doc']     = $this->mysql->result($query,0,'tipo_documento_nombre');
			$this->datosEmpresa['documento']    = $this->mysql->result($query,0,'nit_completo');
			$this->datosEmpresa['razon_social'] = $this->mysql->result($query,0,'razon_social');
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 * @return [type] [description]
		 */
		public function getData(){
			// $where .= " AND (CP.tipo <> 'Cheque Cuenta' OR CP.tipo <> 'Cortesia' )";
			$where .= " AND ( CP.tipo <> 'Cortesia' )";
			$sql   = "SELECT
							VP.id,
							VP.prefijo,
							VP.consecutivo,
							VP.fecha_documento,
							VP.id_seccion,
							VP.seccion,
							VP.mesa,
							VP.documento_cliente,
							VP.cliente,
							VP.usuario,
							VP.id_caja,
							IF(CP.tipo='Cheque Cuenta','CH',IF(CP.tipo='Cortesia','Cortesias','FV')) AS tipo,
							VPP.forma_pago,
							VPP.valor,
							VP.valor_propina,
							VP.valor_descuento
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
						WHERE
							VP.activo = 1
						AND VP.estado<>3
						AND VP.fecha_documento = '$this->fecha'
						AND VPP.activo = 1
						$where
						ORDER BY VP.consecutivo ASC";
			$query = $this->mysql->query($sql);
			// $arrayConsIni = '';
			// $arrayConsFin = '';
			while ($row=$this->mysql->fetch_array($query)){
				$arrayPos[$row['id']] = array(
											'fecha_documento'   => $row['fecha_documento'],
											'id_seccion'        => $row['id_seccion'],
											'seccion'           => $row['seccion'],
											'tipo'              => $row['tipo'],
											'consecutivo'       => $row['consecutivo'],
											'documento_cliente' => $row['documento_cliente'],
											'cliente'           => $row['cliente'],
											'valor_propina'     => $row['valor_propina'],
											'valor_descuento'   => $row['valor_descuento'],
											'id_caja'           => $row['id_caja'],
											'prefijo'           => $row['prefijo'],
										);
				$arrayValorCaja[$row['id_caja']]++;

				if ($arrayConsecutivos[$row['id_caja']][$row['tipo']]['inicial'] <= $row['consecutivo'] ) {
					$arrayConsecutivos[$row['id_caja']][$row['tipo']]['inicial'] = $row['consecutivo'];
					$arrayConsecutivos[$row['id_caja']][$row['tipo']]['prefijo'] = $row['prefijo'];
				}
				if ($arrayConsecutivos[$row['id_caja']][$row['tipo']]['final'] >= $row['consecutivo'] ) {
					$arrayConsecutivos[$row['id_caja']][$row['tipo']]['final']   = $row['consecutivo'];
					$arrayConsecutivos[$row['id_caja']][$row['tipo']]['prefijo'] = $row['prefijo'];
				}


				$arrayMediosPago[$row['forma_pago']]['cantidad']++;
				$arrayMediosPago[$row['forma_pago']]['valor']   += $row['valor'];

			}

			$wherePos = "id_pos='".implode("' OR id_pos='", array_keys($arrayPos))."'";
			$sql = "SELECT
						id_pos,
						id_item,
						cantidad,
						precio_venta
					FROM ventas_pos_inventario
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($wherePos)";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$whereIdItems .= ($whereIdItems=='')? " I.id=$row[id_item] " : " OR I.id=$row[id_item] " ;
				$arrayPos[$row['id_pos']]['items'][] = array(
																'id_pos'       => $row['id_pos'],
																'id_item'      => $row['id_item'],
																'cantidad'     => $row['cantidad'],
																'precio_venta' => $row['precio_venta'],
															);
			}

			$sql   = "SELECT
							VC.id,
							VC.nombre_equipo,
							VC.serial_equipo,
							VCS.nombre_caja,
							VCS.seccion
						FROM
							ventas_pos_cajas AS VC
						INNER JOIN ventas_pos_cajas_secciones AS VCS ON VCS.id_caja = VC.id
						WHERE
							VC.activo = 1
						AND VCS.activo = 1 ";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)) {
				$arrayCajas[$row['id']] = array(
												"nombre_equipo"       => $row['nombre_equipo'],
												"serial_equipo"       => $row['serial_equipo'],
												"nombre_caja"         => $row['nombre_caja'],
												"seccion"             => $row['seccion'],
												"num_transacciones"   => 0,
												"valor_transacciones" => 0,
												);

			}

			$sql   = "SELECT
						I.id,
						I.id_impuesto,
						IM.impuesto,
						IM.valor
					FROM
						items AS I
					INNER JOIN impuestos AS IM ON IM.id = I.id_impuesto
					WHERE
						I.activo = 1
					AND($whereIdItems) ";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayImp[$row['id']] = $row['valor'];
			}
			$arrayConsecutivos['CH']['inicial'] = 0;
			$arrayConsecutivos['FV']['inicial'] = 0;
			foreach ($arrayPos as $id_pos => $arrayResult){
				$tarifa          = 0;
				$acumImpuesto    = 0;
				$acumNeto        = 0;
				$acumNetoUnround = 0;
				foreach ($arrayResult['items'] as $key => $arrayResultItems){
					$subtotal = $arrayResultItems['precio_venta']*$arrayResultItems['cantidad'];
					$acumCantidad += $arrayResultItems['cantidad'];
					$acumTotal    += $subtotal;
					$labelSubtotal = number_format($subtotal,$this->decimalesMoneda,",",".");
    				if ($arrayResult['valor_descuento']>0) {
    					$subtotal = $subtotal-($arrayResult['valor_descuento']/$contItems);
    				}
					$tarifa          = $arrayImp[$arrayResultItems['id_item']];
					$taxPercent      = ( $arrayImp[$arrayResultItems['id_item']] * 0.01 )+1;
					$neto            = ROUND($subtotal/$taxPercent);
					$acumNetoUnround += ($subtotal/$taxPercent);
					$acumNeto        += $neto;
					$acumImpuesto    += ROUND(($neto*$arrayImp[$arrayResultItems['id_item']])/100);
				}

				$tipoOp = ($tarifa>0)? "Gravada IPC" : "" ;
				// $bodyTransCaja .= "<tr>
				// 					<td>$arrayResult[seccion]</td>
				// 					<td>".$arrayCajas[$arrayResult['id_caja']]['nombre_equipo']."</td>
				// 					<td>$arrayResult[consecutivo]</td>
				// 					<td>$tipoOp</td>
				// 					<td>$tarifa</td>
				// 					<td>".number_format($arrayResult['valor_descuento'],$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
				// 					<td>".number_format($acumImpuesto,$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
				// 					<td>".number_format($acumNeto+$acumImpuesto,$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
				// 				</tr>";

				$arrayTransSeccion[$arrayResult['id_seccion']][$tipoOp ]['seccion']         = $arrayResult['seccion'];
				$arrayTransSeccion[$arrayResult['id_seccion']][$tipoOp ]['tarifa']          = $tarifa;
				$arrayTransSeccion[$arrayResult['id_seccion']][$tipoOp ]['valor_descuento'] += $arrayResult['valor_descuento'];
				$arrayTransSeccion[$arrayResult['id_seccion']][$tipoOp ]['impuesto']        += $acumImpuesto;
				$arrayTransSeccion[$arrayResult['id_seccion']][$tipoOp ]['base']            += $acumNeto;


				$acumDescuento += $arrayResult['valor_descuento'];
				$acumImpuestos += $acumImpuesto;
				// $arrayTransacciones[$arrayResult['id_caja']] ++;
				$arrayCajas[$arrayResult['id_caja']]['num_transacciones'] ++;
				$arrayCajas[$arrayResult['id_caja']]['valor_transacciones'] += ($acumNeto+$acumImpuesto);

				// RANGO DE CONSECUTIVOS
				if ($arrayResult['tipo']=='FV') {
					$arrayConsecutivos['FV']['cantidad'] ++;
					$arrayConsecutivos['FV']['total'] += $acumNeto+$acumImpuesto;
					$arrayConsecutivos['FV']['inicial'] = ($arrayConsecutivos['FV']['inicial']==0)? $arrayResult['consecutivo'] : $arrayConsecutivos['FV']['inicial'] ;
					if ($arrayConsecutivos['FV']['inicial'] > $arrayResult['consecutivo']) {
						$arrayConsecutivos['FV']['inicial'] = $arrayResult['consecutivo'];
					}
					if ($arrayConsecutivos['FV']['final'] < $arrayResult['consecutivo']) {
						$arrayConsecutivos['FV']['final'] = $arrayResult['consecutivo'];
					}
				}
				else{
					$arrayConsecutivos['CH']['cantidad'] ++;
					$arrayConsecutivos['CH']['total'] += $acumNeto+$acumImpuesto;
					$arrayConsecutivos['CH']['inicial'] = ($arrayConsecutivos['CH']['inicial']==0)? $arrayResult['consecutivo'] : $arrayConsecutivos['CH']['inicial'] ;
					if ($arrayConsecutivos['CH']['inicial'] > $arrayResult['consecutivo']) {
						$arrayConsecutivos['CH']['inicial'] = $arrayResult['consecutivo'];
					}
					if ($arrayConsecutivos['CH']['final'] < $arrayResult['consecutivo']) {
						$arrayConsecutivos['CH']['final'] = $arrayResult['consecutivo'];
					}
				}


			}

			foreach ($arrayTransSeccion as $id_seccion => $arrayTransSeccionResul){
				foreach($arrayTransSeccionResul as $tipoOp => $arrayResult){
					$bodyTransCaja .= "<tr>
										<td>$arrayResult[seccion]</td>
										<td>$tipoOp</td>
										<td>$arrayResult[tarifa]</td>
										<td>".number_format($arrayResult['base'],$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
										<td>".number_format($arrayResult['impuesto'],$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
										<td>".number_format($arrayResult['valor_descuento'],$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
									</tr>";
					$acumBase += $arrayResult['base'];
				}
			}



			$bodyTransCaja .= "<tfoot>
									<tr>
										<td colspan='3' >Totales</td>
										<td>".number_format($acumBase,$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
										<td>".number_format($acumImpuestos,$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
										<td>".number_format($acumDescuento,$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
									</tr>
								</tfoot>";

			if ($arrayConsecutivos['CH']['inicial'] > 0) {
				$bodyTransConsecutivos = "<tr>
											<td>CH</td>
											<td>".$arrayConsecutivos['CH']['inicial']."</td>
											<td>".$arrayConsecutivos['CH']['final']."</td>
											<td>".$arrayConsecutivos['CH']['cantidad']."</td>
											<td>".number_format($arrayConsecutivos['CH']['total'],$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
										</tr>";
			}
			if ($arrayConsecutivos['FV']['inicial'] > 0) {
				$bodyTransConsecutivos .= "<tr>
											<td>FV</td>
											<td>".$arrayConsecutivos['FV']['inicial']."</td>
											<td>".$arrayConsecutivos['FV']['final']."</td>
											<td>".$arrayConsecutivos['FV']['cantidad']."</td>
											<td>".number_format($arrayConsecutivos['FV']['total'],$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
										</tr>";
			}

			$bodyTransConsecutivos .= "<tfoot>
											<tr>
												<td colspan='3' >TOTALES</td>
												<td>".($arrayConsecutivos['FV']['cantidad']+$arrayConsecutivos['CH']['cantidad'])."</td>
												<td>".number_format(($arrayConsecutivos['FV']['total']+$arrayConsecutivos['CH']['total']),$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
											</tr>
										</tfoot>";

			$acumTotal=0;
			foreach ($arrayMediosPago as $forma_pago => $arrayResult) {
				$bodyMediosPago .= "<tr>
										<td>$forma_pago</td>
										<td>$arrayResult[cantidad]</td>
										<td>".number_format($arrayResult['valor'],$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
									</tr>";
				$acumTotal += $arrayResult['valor'];
			}

			$bodyMediosPago .= "<tfoot>
									<tr>
										<td>Totales</td>
										<td></td>
										<td>".number_format($acumTotal,$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
									</tr>
								</tfoot>";

			foreach ($arrayCajas as $id_caja => $arrayResult) {
				$nTransacciones = ($arrayTransacciones[$id_caja]['num_transacciones']>0)? $arrayTransacciones[$id_caja]['num_transacciones'] : 0 ;
				$bodyMaquinas .= "<tr>
									<td>$arrayResult[nombre_equipo]</td>
									<td>$arrayResult[serial_equipo]</td>
									<td>$arrayResult[seccion]</td>
									<td>$arrayResult[num_transacciones]</td>
									<td>".number_format($arrayResult['valor_transacciones'],$_SESSION['DECIMALESMONEDA'],'.',',')."</td>
								</tr>";
			}

			$arrayReturn['bodyMaquinas']          = $bodyMaquinas;
			$arrayReturn['bodyMediosPago']        = $bodyMediosPago;
			$arrayReturn['bodyTransCaja']         = $bodyTransCaja;
			$arrayReturn['bodyTransConsecutivos'] = $bodyTransConsecutivos;

			return $arrayReturn;
		}

		public function getView(){
			$arrayBody = $this->getData();
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title2"><?= $this->datosEmpresa['nombre']; ?></div>
			<div class="title2"><?= $this->datosEmpresa['razon_social']; ?></div>
			<div class="title2"><?php echo $this->datosEmpresa['tipo_doc'].": ".$this->datosEmpresa['documento']; ?></div>
			<div class="title2">Fecha: <?= $this->fecha; ?></div>
			<div class="title2">Impreso: <p id="pcName"></p></div>
			<div class="title">COMPROBANTE DE INFORME DIARIO POS</div>

			<table>
				<thead>
					<tr>
						<td colspan="3">CONSECUTIVO</td>
						<td colspan="2">TRANSACCIONES</td>
					</tr>
					<tr>
						<td>Tipo</td>
						<td>Cons. Inicial</td>
						<td>Cons. Final</td>
						<td>Cantidad</td>
						<td>Valor</td>
					</tr>
				</thead>
				<tbody> <?= $arrayBody['bodyTransConsecutivos'];  ?> </tbody>

			</table>
			<br>


			<table>
				<thead>
					<tr>
						<td colspan="6">TRANSACCIONES</td>
					</tr>
					<tr>
						<td>Departamento</td>
						<td>Tipo. Op.</td>
						<td>Tarifa</td>
						<td>Vr. Base</td>
						<td>Vr. Impuesto</td>
						<td>Vr. Descuento</td>
					</tr>
				</thead>
				<tbody> <?= $arrayBody['bodyTransCaja'];  ?> </tbody>
			</table>
			<!--<table>
				<thead>
					<tr>
						<td colspan="8">TRANSACCIONES POR MAQUINA</td>
					</tr>
					<tr>
						<td>Ambiente</td>
						<td>Nombre Equipo</td>
						<td>Consecutivo</td>
						<td>Tipo Operacion</td>
						<td>Tarifa</td>
						<td>Vr. Descuento</td>
						<td>Vr. Impuesto</td>
						<td>Vr. Neto</td>
					</tr>
				</thead>
				<tbody> <?= $arrayBody['bodyTransCaja'];  ?> </tbody>

			</table>-->
			<br>
			<table>
				<thead>
					<tr>
						<td colspan="3">TOTALES POR MEDIO DE PAGO</td>
					</tr>
					<tr>
						<td>Medio de Pago</td>
						<td>Cantidad</td>
						<td>Valor</td>
					</tr>
				</thead>
				<tbody><?= $arrayBody['bodyMediosPago']; ?></tbody>
			</table>
			<br>
			<table>
				<thead>
					<tr>
						<td colspan="5">INVENTARIO DE MAQUINAS</td>
					</tr>
					<tr>
						<td>Nombre Equipo</td>
						<td>No. Serial</td>
						<td>Ubicacion</td>
						<td>No. de Transacciones</td>
						<td>Valor</td>
					</tr>
				</thead>
				<tbody><?= $arrayBody['bodyMaquinas']; ?></tbody>
			</table>
			<div class="footer">
				Documento impreso en computador por :<br>
				Logicalsoft-ERP - Logicalsoft<br>
				Impreso: <?php echo fecha_larga2(date("Y-m-d"))." ".date("H:i:s"); ?>
			</div>
			<script>
				var nombreEquipo = (localStorage.pcName=='' || typeof(localStorage.pcName)==undefined || localStorage.pcName==undefined)?
				"Configure el nombre del equipo desde el boton nombre de equipo" : localStorage.pcName ;
				document.getElementById('pcName').innerHTML = nombreEquipo;
			</script>
			<?php
		}

		public function generate(){
			$this->getEmpresaInfo();
			$this->getView();
		}



	}
?>

