<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	switch ($op) {

		case "OptionSelectFamiliaItems":
			OptionSelectFamiliaItems($id_item,$id_empresa,$link);
			break;

		case "OptionSelectGrupoItems":
			OptionSelectGrupoItems($id_item,$id_item_familia,$link);
			break;

		case "OptionSelectSubgrupoItems":
			OptionSelectSubgrupoItems($id_item,$id_item_grupo,$link);
			break;

		case 'configuracionItemsCuentasInventario':
			configuracionItemsCuentasInventario($idItem,$id_empresa,$link);
			break;

		case "OptionCentroCostos":
			OptionCentroCostos($idItem, $id_empresa, $link);
			break;

		case 'OptionIvaItem':
			 OptionIvaItem($idItem, $id_empresa, $link);
			break;

		case 'eliminaImpuestoItem':
			eliminaImpuestoItem($idItem,$impuesto, $id_empresa, $link);
			break;
		case 'delteTemporalFile':
			delteTemporalFile($file,$id_empresa);
			break;
		case 'descargar_items_excel':
			descargar_items_excel($id_empresa,$mysql);
			break;
		case 'copiaBD':
			copiaBD($idItem,$id_empresa,$link);
			break;
	}

	function copiaBD($idItem, $id_empresa, $link) {
		$nombreItem = "SELECT nombre_equipo FROM items WHERE id = '$idItem' LIMIT 1";
		$query_nombre_item = mysql_query($nombreItem, $link);
	
		if (!$query_nombre_item || mysql_num_rows($query_nombre_item) == 0) {
			echo "No existe el item"; 
			return;
		}
	
		$row = mysql_fetch_assoc($query_nombre_item);
		$nombre_equipo = $row['nombre_equipo'];
	
		mysql_query("START TRANSACTION", $link);
	
		$id_new_item = duplicarRegistro($link, 'items', 'id', $idItem, [
			'codigo_auto' => 'true',
			'code_bar' => '',
			'nombre_equipo' => 'copia de ' . $nombre_equipo
		]);
	
		if (empty($id_new_item)) {
			mysql_query("ROLLBACK", $link);
			echo "No se duplicó el registro en la tabla de items"; 
			return;
		}
	
		$tablasRelacionadas = [
			['tabla' => 'items_cuentas', 'campoClave' => 'id_items'],
			['tabla' => 'items_cuentas_niif', 'campoClave' => 'id_items'],
			['tabla' => 'items_recetas', 'campoClave' => 'id_item']
		];
	
		foreach ($tablasRelacionadas as $relacion) {
			$tabla = $relacion['tabla'];
			$campoClave = $relacion['campoClave'];
	
			$resultado = duplicarRegistro($link, $tabla, $campoClave, $idItem, [
				$campoClave => $id_new_item[0]
			]);
	
			if (!$resultado || count($resultado) == 0) {
				mysql_query("ROLLBACK", $link);
				echo "Error al duplicar registros en $tabla";
				return;
			}
		}
	
		mysql_query("COMMIT", $link);
		echo "true";
	}
	
	function duplicarRegistro($link, $tabla, $campoClave, $valorClave, $modificaciones = []) {
		$query = "SELECT * FROM $tabla WHERE $campoClave = '$valorClave'";
		$result = mysql_query($query, $link);
	
		if (!$result || mysql_num_rows($result) == 0) {
			return false;
		}
	
		$nuevosIDs = [];
	
		while ($valores = mysql_fetch_assoc($result)) {
			unset($valores['id']);
	
			foreach ($modificaciones as $campo => $valor) {
				$valores[$campo] = $valor;
			}
	
			$lista_campos = implode(", ", array_keys($valores));
			$lista_valores = implode(", ", array_map(function ($valor) use ($link) {
				if (is_null($valor)) return "NULL";
				return "'" . mysql_real_escape_string($valor, $link) . "'";
			}, $valores));
	
			$sql_insert = "INSERT INTO $tabla ($lista_campos) VALUES ($lista_valores)";
			mysql_query($sql_insert, $link);
	
			$nuevoID = mysql_insert_id($link);
			if (!$nuevoID) {
				return false;
			}
	
			$nuevosIDs[] = $nuevoID;
		}
	
		return $nuevosIDs;
	}
	

	function descargar_items_excel($id_empresa,$mysql){
		$sql = "SELECT
							codigo,
							nombre_equipo,
							familia,
							grupo,
							subgrupo,
							unidad_medida,
							cantidad_unidades,
							centro_costos,
							impuesto,
							estado_compra,
							estado_venta
						FROM
							items
						WHERE
							activo = 1
						AND
						  id_empresa = $id_empresa
						ORDER BY
							codigo ASC";
		$query = $mysql->query($sql,$mysql->link);

		$style = "";

		while($row = $mysql->fetch_array($query)){
			$disponibleCompra = ($row['estado_compra'] == 'true')? "Si" : "No";
			$disponibleVenta  = ($row['estado_venta'] == 'true')? "Si" : "No";

			//CUERPO DEL INFORME
			$bodyTable .=  "<tr style='height:20px; $style'>
												<td style='width:70px; text-align:center; font-size:11px;'>&nbsp;$row[codigo]</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$row[nombre_equipo]</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$row[familia]</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$row[grupo]</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$row[subgrupo]</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$row[unidad_medida] x $row[cantidad_unidades]</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$row[centro_costos]</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$row[impuesto]</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$disponibleCompra</td>
												<td style='width:70px; text-align:center; font-size:11px;'>$disponibleVenta</td>
											</tr>";

			if($style == "background-color:#d0c4c4;"){
				$style = "";
			}
			else{
				$style = "background-color:#d0c4c4;";
			}
		}
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=LISTADO_ITEMS_$_SESSION[NOMBREEMPRESA]_" . date("Y_m_d") . ".xls");
		header("Pragma: no-cache");
		header("Expires: 0");
		?>
		<table>
			<tr>
				<td style="text-align:center;" colspan="10"><b><?php echo $_SESSION['NOMBREEMPRESA']; ?></b></td>
			</tr>
			<tr>
				<td style="text-align:center;" colspan="10"><b>NIT</b> <?php echo $_SESSION['NITEMPRESA']; ?></td>
			</tr>
			<tr>
				<td style="text-align:center;" colspan="10"><b>LISTADO DE ITEMS</td>
			</tr>
		</table>
		<table>
			<tr style="background: #999;padding-left: 10px;height: 25px;color: #FFF;font-weight: bold;">
				<td style='width:70px;text-align:center;'><b>CODIGO</b></td>
				<td style='width:70px;text-align:center;'><b>NOMBRE</b></td>
				<td style='width:70px;text-align:center;'><b>FAMILIA</b></td>
				<td style='width:70px;text-align:center;'><b>GRUPO</b></td>
				<td style='width:70px;text-align:center;'><b>SUBGRUPO</b></td>
				<td style='width:70px;text-align:center;'><b>UNIDAD DE MEDIDA</b></td>
				<td style='width:70px;text-align:center;'><b>CENTRO DE COSTO</b></td>
				<td style='width:70px;text-align:center;'><b>IMPUESTO</b></td>
				<td style='width:70px;text-align:center;'><b>COMPRAS</b></td>
				<td style='width:70px;text-align:center;'><b>VENTAS</b></td>
			</tr>
			<?php echo $bodyTable; ?>
		</table>
		<?php
	}

	function OptionSelectFamiliaItems($id_item,$id_empresa,$link){
		$selected            = '';
		$id_subgrupo_itemsDB = '';

		if($id_item > 0){
			$sqlSubgrupoItems    = "SELECT id_familia FROM items WHERE id='$id_item' AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
			$id_subgrupo_itemsDB = mysql_result(mysql_query($sqlSubgrupoItems,$link),0,'id_familia');
		}

		$SQL1   = "SELECT id,nombre,codigo FROM items_familia WHERE activo=1 AND id_empresa='$id_empresa'";
		$consul = mysql_query($SQL1,$link);

		echo'<select class="myfieldObligatorio" name="itemsGeneral_id_familia" id="itemsGeneral_id_familia" style="width:240px" onchange="ValidarFieldVacio(this)">
				<option value="">Seleccione...</option>';

		while($row = mysql_fetch_array($consul)){
			$selected = ($id_subgrupo_itemsDB==$row['id'])? "selected": "";
			echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['codigo']." - ".$row['nombre'].'</option>';
		}

		echo'</select>
			<script>
                var ComboGrupoFamilia = Ext.get("itemsGeneral_id_familia");
                ComboGrupoFamilia.addListener(
			        "change",
			        function(event,element,options){
				        idFamiliaItem = document.getElementById("itemsGeneral_id_familia").value;
				        ActualizaGrupoItems(idFamiliaItem);
			        },
			        this
		        );

                idFamiliaItem = document.getElementById("itemsGeneral_id_familia").value;
                ActualizaGrupoItems(idFamiliaItem);
            </script>';
	}

	function OptionSelectGrupoItems($id_item,$id_item_grupo,$link){
		$selected            ='';
		$id_subgrupo_itemsDB ='';

		if($id_item_grupo>=1){
			$sqlSubgrupoItems    = "SELECT id_grupo FROM items WHERE id='$id_item' AND activo=1 LIMIT 0,1";
			$id_subgrupo_itemsDB = mysql_result(mysql_query($sqlSubgrupoItems,$link),0,'id_grupo');

			$SQL1   = "SELECT id,nombre,codigo FROM items_familia_grupo WHERE id_familia=".$id_item_grupo." AND activo=1";
			$consul = mysql_query($SQL1,$link);

			echo '<select class="myfieldObligatorio" name="itemsGeneral_id_grupo" id="itemsGeneral_id_grupo" style="width:240px" onchange="ValidarFieldVacio(this)">
					<option value="">Seleccione...</option>';

				while($row = mysql_fetch_array($consul)){
					$selected = ($id_subgrupo_itemsDB==$row['id'])? "selected": "";
					echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['codigo']." - ".$row['nombre'].'</option>';
				}
			echo'</select>
			    <script>
                    var ComboGrupoItems = Ext.get("itemsGeneral_id_grupo");
		            ComboGrupoItems.addListener(
			            "change",
			            function(event,element,options){
				            idGrupoItem = document.getElementById("itemsGeneral_id_grupo").value;
				            ActualizaSubgrupoItems(idGrupoItem);
			            },
			            this
		            );

		            idGrupoItem = document.getElementById("itemsGeneral_id_grupo").value;
		            ActualizaSubgrupoItems(idGrupoItem);
                </script>';
		}
		else{
			echo'<select class="myfieldObligatorio" name="itemsGeneral_id_grupo" id="itemsGeneral_id_grupo" style="width:240px" onchange="ValidarFieldVacio(this)">
					<option value="">Seleccione...</option>
				</select>';
		}
	}

	function OptionSelectSubgrupoItems($id_item,$id_item_grupo,$link){
		$selected            = "";
		$id_subgrupo_itemsDB = "";

		if($id_item_grupo>=1){

			if ($id_item>0) {
				$sqlSubgrupoItems    = "SELECT id_subgrupo FROM items WHERE id='$id_item' AND activo=1 LIMIT 0,1";
				$id_subgrupo_itemsDB = mysql_result(mysql_query($sqlSubgrupoItems,$link),0,'id_subgrupo');
			}

			$SQL1    = "SELECT id,nombre,codigo FROM items_familia_grupo_subgrupo WHERE id_grupo=".$id_item_grupo." AND activo=1";
			$consul1 = mysql_query($SQL1,$link);

			echo'<select class="myfieldObligatorio" name="itemsGeneral_id_subgrupo" id="itemsGeneral_id_subgrupo" style="width:240px" onchange="ValidarFieldVacio(this)">
					<option value="">Seleccione...</option>';
			while($row = mysql_fetch_array($consul1)){
				$selected = ($id_subgrupo_itemsDB==$row['id'])? "selected": "";
				echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['codigo']." - ".$row['nombre'].'</option>';
			}
			echo '</select>';
		}
		else{
			echo'<select class="myfieldObligatorio" name="itemsGeneral_id_subgrupo" id="itemsGeneral_id_subgrupo" style="width:240px" onchange="ValidarFieldVacio(this)">
					<option value="">Seleccione...</option>
				</select>';
		}
	}

	//===================== VERIFICAR QUE EL ARTICULO EXISTA EN LAS BODEGAS ======================//
	function configuracionItemsCuentasInventario($idItem,$id_empresa,$link){
		validacionCuentasDefault($idItem,$id_empresa,$link);
		validacionCuentasNiifDefault($idItem,$id_empresa,$link);

		// CONSULTAR LAS BODEGAS
		$sql="SELECT id,id_sucursal FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa=$id_empresa;";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$id_bodega   = $row['id'];
			$id_sucursal = $row['id_sucursal'];
			$arrayBodegas[$id_bodega] = $id_sucursal;
		}

		// CONSULTAR EN QUE BODEGA EXISTE ESE ITEM Y ELIMINAR LA POSICION DEL ARRAY PARA QUE NO SE INSERTE
		$sql   = "SELECT id_sucursal,id_ubicacion AS id_bodega FROM inventario_totales WHERE activo=1 AND id_empresa=$id_empresa AND id_item=$idItem; ";
		$query = mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			if (isset($arrayBodegas[$row['id_bodega']])) {
				unset($arrayBodegas[$row['id_bodega']]);
			}
		}

		// SI NO EXISTE EL ITEM EN ALGUNA BODEGA, ENTONCES SE PROCEDE A INSERTAR
		if (count($arrayBodegas)>0) {
			foreach ($arrayBodegas as $id_bodega => $id_sucursal) {
				$valueInsert   .= "('$idItem',$id_empresa,$id_sucursal,$id_bodega ),";
			}

			$valueInsert = substr($valueInsert, 0, -1);
			$sql="INSERT INTO inventario_totales (id_item,id_empresa,id_sucursal,id_ubicacion) VALUES $valueInsert; ";
			$query=mysql_query($sql,$link);
			if ($query) {
				echo "true";
			}
			else{
				echo "No se inserto el items en las bodegas $sql";
			}
		}
		else{ echo 'true'; }
	}

	function validacionCuentasDefault($idItem,$id_empresa,$link){

		$sqlItems   = "SELECT IT.inventariable,
							IT.id_impuesto,
							IT.opcion_costo,
							IT.opcion_gasto,
							IT.opcion_activo_fijo,
							IT.estado_compra,
							IT.estado_venta,
							IT.id_grupo,
							IM.cuenta_compra,
							IM.cuenta_venta
						FROM items AS IT LEFT JOIN impuestos AS IM ON(
								IM.activo=1
								AND IT.id_impuesto=IM.id
							)
						WHERE IT.id='$idItem'
							AND IT.id_empresa='$id_empresa'
							AND IT.activo=1
						LIMIT 0,1";
		$queryItems = mysql_query($sqlItems,$link);
		if(!$queryItems){ echo 'false_4'; exit; }

		$inventariable      = mysql_result($queryItems,0,'inventariable');
		$id_impuesto        = mysql_result($queryItems,0,'id_impuesto');
		$opcion_gasto       = mysql_result($queryItems,0,'opcion_gasto');
		$opcion_costo       = mysql_result($queryItems,0,'opcion_costo');
		$opcion_activo_fijo = mysql_result($queryItems,0,'opcion_activo_fijo');
		$estadoCompra       = mysql_result($queryItems,0,'estado_compra');
		$estadoVenta        = mysql_result($queryItems,0,'estado_venta');
		$idGrupo            = mysql_result($queryItems,0,'id_grupo');

		$cuenta_impuesto_compra = mysql_result($queryItems,0,'cuenta_compra');
		$cuenta_impuesto_venta  = mysql_result($queryItems,0,'cuenta_venta');

		// ASIENTOS POR DEFECTO
		$sqlCuentasDefault   = "SELECT descripcion, estado, cuenta FROM asientos_colgaap_default WHERE id_empresa='$id_empresa' AND descripcion LIKE 'items_%'";
		$queryCuentasDefault = mysql_query($sqlCuentasDefault,$link);
		if(!$queryCuentasDefault){ echo 'false_5'; exit; }

		while ($rowCuentasDefault = mysql_fetch_array($queryCuentasDefault)) {
			$descripcion = str_replace('items_', '', $rowCuentasDefault['descripcion']);
			$arrayCuentasDefault[$descripcion] = Array('estado' => $rowCuentasDefault['estado'], 'cuenta' => $rowCuentasDefault['cuenta']);
		}

		// ASIENTOS POR DEFECTO POR GRUPO
		$sqlCuentasDefault   = "SELECT descripcion, estado, cuenta
								FROM asientos_colgaap_default_grupos
								WHERE id_empresa='$id_empresa' AND descripcion LIKE 'items_%' AND id_grupo='$idGrupo'";
		$queryCuentasDefault = mysql_query($sqlCuentasDefault,$link);
		if(!$queryCuentasDefault){ echo 'false_5'; exit; }

		$cuentaDevolucion          = false;
		$cuentaGrupoImpuestoCompra = '';
		$cuentaGrupoImpuestoVenta  = '';
		while ($rowCuentasDefault = mysql_fetch_array($queryCuentasDefault)) {
			$descripcion = str_replace('items_', '', $rowCuentasDefault['descripcion']);
			$arrayCuentasDefault[$descripcion] = Array('estado' => $rowCuentasDefault['estado'], 'cuenta' => $rowCuentasDefault['cuenta']);
		}

		// print_r($arrayCuentasDefault);

		$sqlDeleteCuentas   = "DELETE FROM items_cuentas WHERE id_empresa='$id_empresa' AND id_items='$idItem' AND activo=1 AND tipo=''";
		$queryDeleteCuentas = mysql_query($sqlDeleteCuentas,$link);

		//===============================// CUENTAS ITEMS //==============================//
		//********************************************************************************//

		$sqlCuentasItems   = "SELECT id, descripcion, estado, puc AS cuenta FROM items_cuentas WHERE id_items='$idItem' AND activo=1 GROUP BY id";
		$queryCuentasItems = mysql_query($sqlCuentasItems,$link);
		if(!$queryCuentasItems){ echo 'false_6'; exit; }

		$cuentaPrecioItem = '';
		while ($rowCuentasItems = mysql_fetch_array($queryCuentasItems)) {
			$estado      = $rowCuentasItems['estado'];
			$descripcion = $rowCuentasItems['descripcion'];

			$arrayCuentasItems[$estado.'_'.$descripcion] = 'true';

			if($descripcion == 'precio' AND $estado == 'venta'){ $cuentaPrecioItem = $rowCuentasItems['cuenta']; }
		}

		//VALIDACION SI EXISTE LA CUENTA DE DEVOLUCION DE INVENTARIO
		if(!is_array($arrayCuentasDefault['venta_devprecio'])){
			$estadoPrecio = $arrayCuentasDefault['venta_precio']['estado'];
			$cuentaPrecio = $arrayCuentasDefault['venta_precio']['cuenta'];

			if($cuentaPrecioItem > 0){ $cuentaPrecio = $cuentaPrecioItem; }

			$estadoPrecio = ($estadoPrecio == 'credito')? 'debito': 'credito';
			$arrayCuentasDefault['venta_devprecio'] = Array('estado' => $estadoPrecio, 'cuenta' => $cuentaPrecio);
		}

		$whereDelete        = "";
		$valueInsertCuentas = "";

		//========================// COMPRA //========================//
		//************************************************************//
		if($estadoCompra == 'true'){

			// WHERE DELETE CUENTAS QUE NO PERTENECEN A LA CONFIGURACION DEL ITEM
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_precio' AND CONCAT(estado,'_',descripcion)<>'compra_contraPartida_precio' AND ";
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_impuesto' AND ";

			if($opcion_gasto == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_gasto' AND "; }
			if($opcion_activo_fijo == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_activo_fijo' AND "; }
			if($opcion_costo == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_costo' AND "; }

			if($arrayCuentasItems['compra_precio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['compra_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_precio']['estado'];

				$valueInsertCuentas .= "('precio','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($arrayCuentasItems['compra_contraPartida_precio'] != 'true'){									// CONTRA-PARTIDA PRECIO
				$cuenta = $arrayCuentasDefault['compra_contraPartida_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_contraPartida_precio']['estado'];

				$valueInsertCuentas .= "('contraPartida_precio','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($arrayCuentasItems['compra_impuesto'] != 'true'){												// IMPUESTO
				$cuenta = ($cuenta_impuesto_compra > 0)? $cuenta_impuesto_compra: $arrayCuentasDefault['compra_impuesto']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_impuesto']['estado'];

				$valueInsertCuentas .= "('impuesto','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_gasto == 'true' && $arrayCuentasItems['compra_gasto'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_gasto']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_gasto']['estado'];

				$valueInsertCuentas .= "('gasto','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_costo == 'true' && $arrayCuentasItems['compra_costo'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_costo']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_costo']['estado'];

				$valueInsertCuentas .= "('costo','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_activo_fijo == 'true' && $arrayCuentasItems['compra_activo_fijo'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_activo_fijo']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_activo_fijo']['estado'];

				$valueInsertCuentas .= "('activo_fijo','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
		}

		// print_r($arrayCuentasDefault);

		//========================// VENTA //========================//
		//***********************************************************//
		if($estadoVenta == 'true'){

			// WHERE DELETE CUENTAS QUE NO PERTENECEN A LA CONFIGURACION DEL ITEM
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_devprecio' AND ";
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_precio' AND CONCAT(estado,'_',descripcion)<>'venta_contraPartida_precio' AND ";

			if($id_impuesto > 0){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_impuesto' AND "; }
			if($inventariable == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_costo' AND CONCAT(estado,'_',descripcion)<>'venta_contraPartida_costo' AND "; }


			if($arrayCuentasItems['venta_precio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['venta_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_precio']['estado'];

				$valueInsertCuentas .= "('precio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_devprecio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['venta_devprecio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_devprecio']['estado'];

				$valueInsertCuentas .= "('devprecio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_contraPartida_precio'] != 'true'){										// CONTRA-PARTIDA PRECIO
				$cuenta = $arrayCuentasDefault['venta_contraPartida_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_contraPartida_precio']['estado'];

				$valueInsertCuentas .= "('contraPartida_precio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_impuesto'] != 'true'){													// IMPUESTO
				$cuenta = ($cuenta_impuesto_venta > 0)? $cuenta_impuesto_venta: $arrayCuentasDefault['venta_impuesto']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_impuesto']['estado'];

				$valueInsertCuentas .= "('impuesto','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}

			if($inventariable == 'true'){																		// COSTO
				if($arrayCuentasItems['venta_costo'] != 'true'){
					$cuenta = $arrayCuentasDefault['venta_costo']['cuenta'];
					$tipo   = $arrayCuentasDefault['venta_costo']['estado'];

					$valueInsertCuentas .= "('costo','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
				}

				if($arrayCuentasItems['venta_contraPartida_costo'] != 'true'){
					$cuenta = $arrayCuentasDefault['venta_contraPartida_costo']['cuenta'];
					$tipo   = $arrayCuentasDefault['venta_contraPartida_costo']['estado'];

					$valueInsertCuentas .= "('contraPartida_costo','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
				}
			}
		}

		if($valueInsertCuentas != ""){																			// INSERT CUENTAS DEFAULT
			$valueInsertCuentas        = substr($valueInsertCuentas, 0, -1);
			$sqlInsertCuentasDefault   = "INSERT INTO items_cuentas (descripcion,id_items,puc,tipo,id_empresa,estado) VALUES $valueInsertCuentas";
			$queryInsertCuentasDefault = mysql_query($sqlInsertCuentasDefault,$link);
			if(!$queryInsertCuentasDefault){ echo 'false_7'; exit; }
		}

		if($whereDelete != ""){ $whereDelete = "AND ".substr($whereDelete, 0, -4); }
		$sqlDeleteCuentas   = "DELETE FROM items_cuentas WHERE id_empresa='$id_empresa' AND id_items='$idItem' $whereDelete";
		$queryDeleteCuentas = mysql_query($sqlDeleteCuentas,$link);
		if(!$queryDeleteCuentas){ echo 'false_8'; exit; }
	}

	function validacionCuentasNiifDefault($idItem,$id_empresa,$link){

		$sqlItems   = "SELECT IT.inventariable,
							IT.id_impuesto,
							IT.opcion_costo,
							IT.opcion_gasto,
							IT.opcion_activo_fijo,
							IT.estado_compra,
							IT.estado_venta,
							IT.id_grupo,
							IM.cuenta_compra_niif,
							IM.cuenta_venta_niif
						FROM items AS IT LEFT JOIN impuestos AS IM ON(
								IM.activo=1
								AND IT.id_impuesto=IM.id
							)
						WHERE IT.id='$idItem'
							AND IT.id_empresa='$id_empresa'
							AND IT.activo=1
						LIMIT 0,1";
		$queryItems = mysql_query($sqlItems,$link);
		if(!$queryItems){ echo 'false_4'; exit; }

		$inventariable      = mysql_result($queryItems,0,'inventariable');
		$id_impuesto        = mysql_result($queryItems,0,'id_impuesto');
		$opcion_gasto       = mysql_result($queryItems,0,'opcion_gasto');
		$opcion_costo       = mysql_result($queryItems,0,'opcion_costo');
		$opcion_activo_fijo = mysql_result($queryItems,0,'opcion_activo_fijo');
		$estadoCompra       = mysql_result($queryItems,0,'estado_compra');
		$estadoVenta        = mysql_result($queryItems,0,'estado_venta');
		$idGrupo            = mysql_result($queryItems,0,'id_grupo');

		$cuenta_impuesto_compra_niif = mysql_result($queryItems,0,'cuenta_compra_niif');
		$cuenta_impuesto_venta_niif  = mysql_result($queryItems,0,'cuenta_venta_niif');

		$sqlCuentasDefault   = "SELECT descripcion, estado, cuenta FROM asientos_niif_default WHERE id_empresa='$id_empresa' AND descripcion LIKE 'items_%'";
		$queryCuentasDefault = mysql_query($sqlCuentasDefault,$link);
		if(!$queryCuentasDefault){ echo 'false_5'; exit; }

		while ($rowCuentasDefault = mysql_fetch_array($queryCuentasDefault)) {
			$descripcion = str_replace('items_', '', $rowCuentasDefault['descripcion']);
			$arrayCuentasDefault[$descripcion] = Array('estado' => $rowCuentasDefault['estado'], 'cuenta' => $rowCuentasDefault['cuenta']);
		}

		//ASIENTOS POR DEFECTO POR GRUPO
		$sqlCuentasDefault   = "SELECT descripcion, estado, cuenta
								FROM asientos_niif_default_grupos
								WHERE id_empresa='$id_empresa' AND descripcion LIKE 'items_%' AND id_grupo='$idGrupo'";
		$queryCuentasDefault = mysql_query($sqlCuentasDefault,$link);
		if(!$queryCuentasDefault){ echo 'false_5'; exit; }

		while ($rowCuentasDefault = mysql_fetch_array($queryCuentasDefault)) {
			$descripcion = str_replace('items_', '', $rowCuentasDefault['descripcion']);
			$arrayCuentasDefault[$descripcion] = Array('estado' => $rowCuentasDefault['estado'], 'cuenta' => $rowCuentasDefault['cuenta']);
		}

		$sqlDeleteCuentas   = "DELETE FROM items_cuentas_niif WHERE id_empresa='$id_empresa' AND id_items='$idItem' AND activo=1 AND tipo=''";
		$queryDeleteCuentas = mysql_query($sqlDeleteCuentas,$link);

		$sqlCuentasItems = "SELECT id,descripcion,estado FROM items_cuentas_niif WHERE id_items='$idItem' AND activo=1";
		$queryCuentasItems = mysql_query($sqlCuentasItems,$link);
		if(!$queryCuentasItems){ echo 'false_6'; exit; }

		while ($rowCuentasItems = mysql_fetch_array($queryCuentasItems)) {
			$estado      = $rowCuentasItems['estado'];
			$descripcion = $rowCuentasItems['descripcion'];

			$arrayCuentasItems[$estado.'_'.$descripcion] = 'true';
		}

		$whereDelete        = "";
		$valueInsertCuentas = "";

		//======================== COMPRA =======================//
		if($estadoCompra == 'true'){

			// WHERE DELETE CUENTAS QUE NO PERTENECEN A LA CONFIGURACION DEL ITEM
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_precio' AND CONCAT(estado,'_',descripcion)<>'compra_contraPartida_precio' AND ";
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_impuesto' AND ";

			if($opcion_gasto == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_gasto' AND "; }
			if($opcion_costo == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_costo' AND "; }
			if($opcion_activo_fijo == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'compra_activo_fijo' AND "; }


			if($arrayCuentasItems['compra_precio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['compra_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_precio']['estado'];

				$valueInsertCuentas .= "('precio','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($arrayCuentasItems['compra_contraPartida_precio'] != 'true'){									// CONTRA-PARTIDA PRECIO
				$cuenta = $arrayCuentasDefault['compra_contraPartida_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_contraPartida_precio']['estado'];

				$valueInsertCuentas .= "('contraPartida_precio','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($arrayCuentasItems['compra_impuesto'] != 'true'){												// IMPUESTO
				$cuenta = ($cuenta_impuesto_compra_niif > 0)? $cuenta_impuesto_compra_niif: $arrayCuentasDefault['compra_impuesto']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_impuesto']['estado'];

				$valueInsertCuentas .= "('impuesto','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_gasto == 'true' && $arrayCuentasItems['compra_gasto'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_gasto']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_gasto']['estado'];

				$valueInsertCuentas .= "('gasto','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_costo == 'true' && $arrayCuentasItems['compra_costo'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_costo']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_costo']['estado'];

				$valueInsertCuentas .= "('costo','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}

			if($opcion_activo_fijo == 'true' && $arrayCuentasItems['compra_activo_fijo'] != 'true'){
				$cuenta = $arrayCuentasDefault['compra_activo_fijo']['cuenta'];
				$tipo   = $arrayCuentasDefault['compra_activo_fijo']['estado'];

				$valueInsertCuentas .= "('activo_fijo','$idItem','$cuenta','$tipo','$id_empresa','compra'),";
			}
		}

		//======================== VENTA =======================//
		if($estadoVenta == 'true'){

			// WHERE DELETE CUENTAS QUE NO PERTENECEN A LA CONFIGURACION DEL ITEM
			$whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_precio' AND CONCAT(estado,'_',descripcion)<>'venta_contraPartida_precio' AND ";
			if($id_impuesto > 0){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_impuesto' AND "; }
			if($inventariable == 'true'){ $whereDelete .= "CONCAT(estado,'_',descripcion)<>'venta_costo' AND CONCAT(estado,'_',descripcion)<>'venta_contraPartida_costo' AND "; }


			if($arrayCuentasItems['venta_precio'] != 'true'){													// PRECIO
				$cuenta = $arrayCuentasDefault['venta_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_precio']['estado'];

				$valueInsertCuentas .= "('precio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_contraPartida_precio'] != 'true'){										// CONTRA-PARTIDA PRECIO
				$cuenta = $arrayCuentasDefault['venta_contraPartida_precio']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_contraPartida_precio']['estado'];

				$valueInsertCuentas .= "('contraPartida_precio','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}
			if($arrayCuentasItems['venta_impuesto'] != 'true'){													// IMPUESTO
				$cuenta = ($cuenta_impuesto_venta_niif > 0)? $cuenta_impuesto_venta_niif: $arrayCuentasDefault['venta_impuesto']['cuenta'];
				$tipo   = $arrayCuentasDefault['venta_impuesto']['estado'];

				$valueInsertCuentas .= "('impuesto','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
			}

			if($inventariable == 'true'){																		// COSTO
				if($arrayCuentasItems['venta_costo'] != 'true'){
					$cuenta = $arrayCuentasDefault['venta_costo']['cuenta'];
					$tipo   = $arrayCuentasDefault['venta_costo']['estado'];

					$valueInsertCuentas .= "('costo','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
				}

				if($arrayCuentasItems['venta_contraPartida_costo'] != 'true'){
					$cuenta = $arrayCuentasDefault['venta_contraPartida_costo']['cuenta'];
					$tipo   = $arrayCuentasDefault['venta_contraPartida_costo']['estado'];

					$valueInsertCuentas .= "('contraPartida_costo','$idItem','$cuenta','$tipo','$id_empresa','venta'),";
				}
			}
		}

		if($valueInsertCuentas != ""){																			// INSERT CUENTAS DEFAULT
			$valueInsertCuentas        = substr($valueInsertCuentas, 0, -1);
			$sqlInsertCuentasDefault   = "INSERT INTO items_cuentas_niif (descripcion,id_items,puc,tipo,id_empresa,estado) VALUES $valueInsertCuentas";
			$queryInsertCuentasDefault = mysql_query($sqlInsertCuentasDefault,$link);
			if(!$queryInsertCuentasDefault){ echo 'false_7'; exit; }
		}

		if($whereDelete != ""){ $whereDelete = "AND ".substr($whereDelete, 0, -4); }
		$sqlDeleteCuentas   = "DELETE FROM items_cuentas_niif WHERE id_empresa='$id_empresa' AND id_items='$idItem' $whereDelete";
		$queryDeleteCuentas = mysql_query($sqlDeleteCuentas,$link);
		if(!$queryDeleteCuentas){ echo 'false_8'; exit; }
	}

	function OptionCentroCostos($idItem, $id_empresa, $link){
		$SQLDB              = "SELECT id_centro_costos,centro_costos,codigo FROM items WHERE id='$idItem' AND activo=1 AND id_empresa='$id_empresa'";
		$id_centro_costosDB = mysql_result(mysql_query($SQLDB,$link),0,'id_centro_costos');
		$centro_costosDB    = mysql_result(mysql_query($SQLDB,$link),0,'centro_costos');

		$SQL   = "SELECT id,codigo,nombre FROM centro_costos WHERE activo=1 AND id_empresa='$id_empresa' AND  id=$id_centro_costosDB";
		$query = mysql_query($SQL,$link);
		$codigo_centro_costosDB    = mysql_result(mysql_query($SQL,$link),0,'codigo');


		if ($id_centro_costosDB!="" && $centro_costosDB!="") {
			echo '	<script>
						document.getElementById("itemsGeneral_id_centro_costos").value="'.$id_centro_costosDB.'";
					</script>
					<input class="myfield" name="itemsGeneral_centro_costos" type="text" id="itemsGeneral_centro_costos" value="'.$codigo_centro_costosDB.' - '.$centro_costosDB.'" style="width:240px;float:left;" onkeyup="" readonly="" onclick="ventanaBuscarCentroCostos()">
					<div id="imgEliminarCcos" style="width:19px;height:16px;background-color: #FFF;cursor:pointer;background-image: url(\'img/false.png\');background-repeat: no-repeat;float:left;margin-left: -20;margin-top: 2;border-left: 1px solid #BDB4B4;"  title="Eliminar Centro Costos" onclick="eliminaCcosItem()"></div>';
		}
		else{
			echo '	<script>
						document.getElementById("itemsGeneral_id_centro_costos").value="";
					</script>
					<input class="myfield" name="itemsGeneral_centro_costos" type="text" id="itemsGeneral_centro_costos" value="" style="width:240px;float:left;" onkeyup="" readonly="" onclick="ventanaBuscarCentroCostos()">
					<div id="imgEliminarCcos" style="width:19px;height:16px;background-color: #FFF;cursor:pointer;background-image: url(\'img/buscar20.png\');background-repeat: no-repeat;float:left;margin-left: -20;margin-top: 2;border-left: 1px solid #BDB4B4;"  title="Buscar Centro Costos" onclick="ventanaBuscarCentroCostos()"></div>';
		}

	}

	//ACTUALIZAR EL IVA DE UN ITEM
	function OptionIvaItem($idItem, $id_empresa, $link){
		$SQLDB       = "SELECT id_impuesto FROM items WHERE id='$idItem' AND activo=1 AND id_empresa='$id_empresa'";
		$id_impuesto = mysql_result(mysql_query($SQLDB,$link),0,'id_impuesto');

		$SQL      = "SELECT impuesto,valor FROM impuestos WHERE activo=1 AND id_empresa='$id_empresa' AND  id=$id_impuesto";
		$query    = mysql_query($SQL,$link);
		$impuesto = mysql_result(mysql_query($SQL,$link),0,'impuesto');
		$valor    = mysql_result(mysql_query($SQL,$link),0,'valor')*1;


		if ($id_impuesto!="" && $impuesto!="") {
			echo'<script>document.getElementById("itemsGeneral_id_impuesto").value="'.$id_impuesto.'";</script>
				<input class="myfield" name="itemsGeneral_impuesto" type="text" id="itemsGeneral_impuesto" value="'.$impuesto.' ('.$valor.'%)" style="width:240px;float:left;" onkeyup="" readonly="" onclick="ventanaBuscarIva()">
				<div id="imgEliminarImpuesto" style="width:19px;height:16px;background-color: #FFF;cursor:pointer;background-image: url(\'img/false.png\');background-repeat: no-repeat;float:left;margin-left: -20;margin-top: 2;border-left: 1px solid #BDB4B4;" onclick="eliminaImpuestoItem(\''.$idItem.'\',\''.$impuesto.' ('.$valor.'%)\')" title="Eliminar Impuesto"></div>';
		}
		else{
			echo'<script>document.getElementById("itemsGeneral_id_impuesto").value="";</script>
				<input class="myfield" name="itemsGeneral_impuesto" type="text" id="itemsGeneral_impuesto" value="" style="width:240px;float:left;" onkeyup="" readonly="" onclick="ventanaBuscarIva()">
				<div id="imgEliminarImpuesto" style="width:19px;height:16px;background-color: #FFF;cursor:pointer;background-image: url(\'img/buscar20.png\');background-repeat: no-repeat;float:left;margin-left: -20;margin-top: 2;border-left: 1px solid #BDB4B4;"  title="Buscar Centro Costos" onclick="ventanaBuscarIva()"></div>';
		}

	}

	//ELIMINAR EL IVA DE UN ITEM
	function eliminaImpuestoItem($idItem,$impuesto, $id_empresa, $link){
		$sql   = "UPDATE items SET id_impuesto=NULL,impuesto=NULL WHERE id='$idItem' AND activo=1 AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		if (!$query) {
			echo'<script>alert("Error!\nNo se elimino el iva del item, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>
				<input class="myfield" name="itemsGeneral_impuesto" type="text" id="itemsGeneral_impuesto" value="'.$impuesto.'" style="width:240px;float:left;" onkeyup="" readonly="" onclick="ventanaBuscarIva()">
				<div id="imgEliminarImpuesto" style="width:16px;height:16px;cursor:pointer;background-image: url(\'img/false.png\');background-repeat: no-repeat;float:left;margin-left: -20;margin-top: 2;border-left: 1px solid #BDB4B4;" onclick="eliminaImpuestoItem(\''.$idItem.'\',\''.$impuesto.'\')" title="Eliminar Impuesto"></div>';
		}
		else{
			echo'<script>document.getElementById("itemsGeneral_id_impuesto").value="";</script>
				<input class="myfield" name="itemsGeneral_impuesto" type="text" id="itemsGeneral_impuesto" value="" style="width:240px;float:left;" onkeyup="" readonly="" onclick="ventanaBuscarIva()">';
		}

	}

	function delteTemporalFile($file,$id_empresa){
		$id_host = $_SESSION['ID_HOST'];
		$serv    = $_SERVER['DOCUMENT_ROOT'];
		// if(unlink("$serv/ARCHIVOS_PROPIOS/empresa_$id_host/archivos_temporales/$file")){echo "true"; }
		// else{echo "false"; }
	}

?>
