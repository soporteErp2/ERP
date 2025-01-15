<?php
	/**
	 * ClassInfoFacturasAnuladas informe de chequecuentas
	 */
	class ClassInfoFacturasAnuladas
	{

		public $id_ambiente;
		public $fechaInicio;
		public $fechaFin;
		public $tipo;
		public $numFactura;
		public $id_empresa;
		public $mysql;

		function __construct($id_empresa,$ambiente,$fechaInicio,$fechaFin,$tipo,$numFactura,$mysql){
			$this->id_ambiente = $ambiente;
			$this->fechaInicio = $fechaInicio;
			$this->fechaFin    = $fechaFin;
			$this->tipo        = $tipo;
			$this->numFactura  = $numFactura;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 * @return [type] [description]
		 */
		public function getData(){
			$where .= ($this->id_ambiente<>'' && $this->id_ambiente<>'Todos')? " AND VP.id_seccion=$this->id_ambiente" : "" ;
			if ($this->tipo<>'' && $this->tipo<>'Todos') {
				if ($this->tipo<>'Facturas') {
					$where .= " AND CP.tipo = '$this->tipo' ";
				}
				else{
					$where .= " AND (CP.tipo <> 'Cheque Cuenta' OR CP.tipo <> 'Cortesia' )";
				}
			}
			$where = ($numFactura<>'')? " AND VP.consecutivo='$numFactura' " : $where ;
			$sql   = "SELECT
							VP.id,
							VP.consecutivo,
							VP.fecha_documento,
							VP.seccion,
							VP.mesa,
							VP.documento_cliente,
							VP.cliente,
							VP.usuario,
							VP.detalle_estado,
							IF(CP.tipo='Cheque Cuenta','Transferencia Cuentas',IF(CP.tipo='Cortesia','Cortesias','Facturas')) AS tipo,
							VPP.forma_pago,
							VPP.valor,
							VP.valor_propina,
							VP.valor_descuento,
							LDC.nombre_usuario AS usuario_anulacion
						FROM
							ventas_pos AS VP
						INNER JOIN ventas_pos_formas_pago AS VPP ON VPP.id_pos = VP.id
						INNER JOIN configuracion_cuentas_pago_pos AS CP ON CP.id = VPP.id_forma_pago
						INNER JOIN log_documentos_contables AS LDC ON VP.id = LDC.id_documento
							AND tipo_documento = 'POS'
						WHERE
							VP.activo = 1
						AND VP.estado = 3
						AND VP.fecha_documento >= '$this->fechaInicio'
						AND VP.fecha_documento <= '$this->fechaFin'
						AND VPP.activo = 1
						$where
						GROUP BY VP.id";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayPos[$row['id']] = array(
											'fecha_documento'    => $row['fecha_documento'],
											'tipo'               => $row['tipo'],
											'consecutivo'        => $row['consecutivo'],
											'documento_cliente'  => $row['documento_cliente'],
											'cliente'            => $row['cliente'],
											'valor_propina'      => $row['valor_propina'],
											'valor_descuento'    => $row['valor_descuento'],
											'usuario'            => $row['usuario'],
											'seccion'            => $row['seccion'],
											'detalle_estado'     => $row['detalle_estado'],
											'usuario_anulacion'    => $row['usuario_anulacion'],
										);

			}

			$wherePos = "id_pos='".implode("' OR id_pos='", array_keys($arrayPos))."'";
			$sql = "SELECT
						id_pos,
						id_item,
						cantidad,
						precio_venta,
						valor_impuesto
					FROM ventas_pos_inventario
					WHERE activo=1 AND id_empresa=$this->id_empresa AND ($wherePos)";
			$query = $this->mysql->query($sql);
			while ($row=$this->mysql->fetch_array($query)){
				$arrayPos[$row['id_pos']]['items'][] = array(
																'id_pos'       => $row['id_pos'],
																'id_item'      => $row['id_item'],
																'cantidad'     => $row['cantidad'],
																'precio_venta' => $row['precio_venta'],
																'impuesto'	   => $row['valor_impuesto'],
															);
			}

			foreach ($arrayPos as $id_pos => $arrayResult){
				foreach ($arrayResult['items'] as $key => $arrayResultItems){
					$taxPercent   = ( $arrayResultItems['impuesto'] * 0.01 )+1;
					$neto         = ROUND($arrayResultItems['precio_venta']/$taxPercent);
					$acumNeto     += $neto;
					$acumImpuesto += ROUND(($neto*$arrayResultItems['impuesto'])/100);
				}
				
				$bodyReturn .= "<tr>
									<td>$arrayResult[fecha_documento]</td>
									<td>$arrayResult[tipo]</td>
									<td>$arrayResult[consecutivo]</td>
									<td>$arrayResult[usuario]</td>
									<td>$arrayResult[seccion]</td>
									<td>$arrayResult[detalle_estado]</td>
									<td>".number_format(($acumNeto+$acumImpuesto-$arrayResult['valor_descuento']),0,",",".")."</td>
									<td>$arrayResult[usuario_anulacion]</td>
								</tr>";
				$acumNeto = 0;
				$acumImpuesto = 0;
			}

			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../../backend/pos_admin/informes/index.css">
			<div class="title">Informe Factura de Venta (Anuladas)</div>
			<div class="subtitle">Desde: <?= $this->fechaInicio; ?> Hasta: <?= $this->fechaFin; ?> </div>
			<table id="InformeData">
				<thead>
					<tr>
						<td>Fecha</td>
						<td>Tipo</td>
						<td>Numero</td>
						<td>Usuario</td>
						<td>Ambiente</td>
						<td>Observacion</td>
						<td>Valor</td>
						<td>Usuario Anulacion</td>
					</tr>
				</thead>
				<tbody> <?= $this->getData(); ?>
				</tbody>
			</table>
			<?php
		}

		public function generate(){
			$this->getView();
		}



	}
?>

