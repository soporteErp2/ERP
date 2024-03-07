<?php
	include('../../../../../configuracion/conectar.php');
	include('../../../../../configuracion/define_variables.php');

	/**
	 * ByItem informe de chequecuentas
	 */
	class ByItem
	{

		public $id_bodega;
		public $fecha;
		public $id_empresa;
		public $cod_item;
        public $mysql;

		function __construct($fecha,$id_bodega,$mysql,$cod_item){
			if ($fecha == "" || $id_bodega == "" || $cod_item=="") {
                ?>
                    <div style="display: flex;justify-content: space-around;align-items: center;margin-top:2rem" >
                        <span style="color:#f44336;font-weight:bold;" >
                            Los campos de fecha, bodega y codigo de item son obligatorios
                        </span>
                    </div>
                <?php
				exit;
			}
			$this->id_bodega   = $id_bodega;
			$this->fecha       = $fecha;
			$this->id_empresa  = $_SESSION["EMPRESA"];
			$this->mysql       = $mysql;
			$this->cod_item    = $cod_item;

		}

		public function getStoreInfo(){
			$sql = "SELECT nombre FROM empresas_sucursales_bodegas WHERE id=".$this->id_bodega;
			$query = $this->mysql->query($sql);
			return $this->mysql->result($query,0,'nombre');
		}

		/**
		 * getData Consultar los datos de las ventas tipo cheque cuenta
		 * @return [type] [description]
		 */
		public function getData(){
			$sql   = "SELECT
							tipo_documento,
                            consecutivo_documento,
                            fecha_documento,
                            accion_documento,
                            accion_inventario,
                            codigo,
                            item,
                            unidad_medida,
                            cantidad_unidades,
                            costo,
                            cantidad,
                            costo_anterior,
                            costo_nuevo,
                            cantidad_anterior,
                            cantidad_nueva,
                            usuario
						FROM
							logs_inventario 
						WHERE activo = 1
						AND codigo = '$this->cod_item'
                        AND id_bodega=$this->id_bodega 
                        AND fecha_movimiento>='$this->fecha'
						";
			$query = $this->mysql->query($sql);
            $row=$this->mysql->fetch_array($query);

            if (!$query) {
                exit('Error en la consulta: ' . $this->mysql->error);
            }

            if (!$row) {  
                ?>
                    <div style="display: flex;justify-content: space-around;align-items: center;margin-top:2rem" >
                        <span style="color:#ff5722;font-weight:bold;" >
                            No se encontraron registros con los filtros seleccionados
                        </span>
                    </div>
                <?php    
            }

			$bodyReturn  = "";
			while ($row=$this->mysql->fetch_array($query)){

                if ($bodyReturn  == "") {
                    $bodyReturn = "<thead>
                                        <tr>
                                            <td colspan='12' style='text-align: center;border-bottom:1px solid #FFF;'>
                                                <b>$row[codigo]</b> $row[item] ($row[unidad_medida] x $row[cantidad_unidades])
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tipo</td>
                                            <td>Cons.</td>
                                            <td>Fecha Doc.</td>
                                            <td>Evento Doc.</td>
                                            <td>Evento en Inv.</td>
                                            <td>Costo Doc.</td>
                                            <td>Cantidad Doc.</td>
                                            <td>Costo Anterior</td>
                                            <td>Costo nuevo</td>
                                            <td>Cantidad Anterior</td>
                                            <td>Cantidad Nueva</td>
                                            <td>Usuario</td>
                                        </tr>
                                    </thead>";
                }

				$bodyReturn .= "<tr>
                                    <td>$row[tipo_documento]</td>
                                    <td>$row[consecutivo_documento]</td>
                                    <td>$row[fecha_documento]</td>
                                    <td>$row[accion_documento]</td>
                                    <td>$row[accion_inventario]</td>
                                    <td>$row[costo]</td>
                                    <td>$row[cantidad]</td>
                                    <td>$row[costo_anterior]</td>
                                    <td>$row[costo_nuevo]</td>
                                    <td>$row[cantidad_anterior]</td>
                                    <td>$row[cantidad_nueva]</td>
                                    <td>$row[usuario]</td>
                                </tr>";
			}

			return $bodyReturn;
		}

		public function getView(){
			?>
			<link rel="stylesheet" type="text/css" href="../../pos/backend/pos_admin/informes/index.css">
			<div class="title">Log detallado de Item</div>
			<div class="subtitle">Bodega: <?= $this->getStoreInfo(); ?></div>
			<div class="subtitle">Desde: <?= $this->fecha; ?></div>
            
			<table id="InformeData">
				<tbody> <?= $this->getData(); ?>
				</tbody>
			</table>
			<?php
		}

		public function generate(){
			$this->getView();
		}



	}

	$obj = new ByItem($fecha,$bodega,$mysql,$cod_item);
	$obj->generate();
?>

