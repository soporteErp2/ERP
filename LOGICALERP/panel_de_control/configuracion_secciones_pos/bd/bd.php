<?php
	include("../../../../configuracion/conectar.php");
	include('../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	switch ($opc) {
		// FUNCIONES PARA SECCIONES
		case 'agregarSeccion':
			agregarSeccion($nombre,$id_padre,$restaurante,$id_sucursal,$bodega,$idCcos,$codigoCcos,$nombreCcos,$cuenta_ingreso_colgaap,$cuenta_ingreso_niif,$eventos_asiste,$cod_tx,$cambia_precio_items,$cuenta_pago,$metodo_pago,$id_empresa,$mysql);
			break;
		case 'actualizarSeccion':
			actualizarSeccion($id_seccion,$nombre,$id_padre,$restaurante,$bodega,$idCcos,$codigoCcos,$nombreCcos,$cuenta_ingreso_colgaap,$cuenta_ingreso_niif,$eventos_asiste,$cod_tx,$cambia_precio_items,$cuenta_pago,$metodo_pago,$id_empresa,$mysql);
			break;
		case 'eliminarSeccion':
			eliminarSeccion($id_seccion,$id_empresa,$mysql);
			break;
		case 'codTxItem':
			codTxItem($id_item,$id_seccion,$mysql);
			break;
		case 'saveCodTx':
			saveCodTx($codTx,$id_item,$id_seccion,$mysql);
			break;

	}

	// FUNCIONES PARA LAS SECCIONES
	function agregarSeccion($nombre,$id_padre,$restaurante,$id_sucursal,$bodega=0,$idCcos,$codigoCcos,$nombreCcos,$cuenta_ingreso_colgaap,$cuenta_ingreso_niif,$eventos_asiste,$cod_tx,$cambia_precio_items,$cuenta_pago,$metodo_pago,$id_empresa,$mysql){
		$sql="INSERT INTO ventas_pos_secciones
				(
					nombre,
					id_padre,
					restaurante,
					id_sucursal,
					id_bodega,
					id_centro_costos,
					codigo_centro_costos,
					centro_costos,
					cuenta_ingreso_colgaap,
					cuenta_ingreso_niif,
					eventos_asiste,
					codigo_transaccion,
					cambia_precio_items,
					cuenta_pago,
					metodo_pago,
					id_empresa
				)
				VALUES
				(
					'$nombre',
					'$id_padre',
					'$restaurante',
					'$id_sucursal',
					'$bodega',
					'$idCcos',
					'$codigoCcos',
					'$nombreCcos',
					'$cuenta_ingreso_colgaap',
					'$cuenta_ingreso_niif',
					'$eventos_asiste',
					'$cod_tx',
					'$cambia_precio_items',
					'$cuenta_pago',
					'$metodo_pago',
					$id_empresa
				)";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_secciones_pos/secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
						}
					});
					Win_Ventana_Seccion.close();
				</script>";
		}
		else{
			// $id_seccion = $mysql->insert_id($mysql->link);
			echo "<script>
					MyLoading2('off',{icono:'fail',texto:'No se inserto la Seccion' });
					console.log(`$sql`)
				</script>";
		}
	}

	function actualizarSeccion($id_seccion,$nombre,$id_padre,$restaurante,$bodega=0,$idCcos,$codigoCcos,$nombreCcos,$cuenta_ingreso_colgaap,$cuenta_ingreso_niif,$eventos_asiste,$cod_tx,$cambia_precio_items,$cuenta_pago,$metodo_pago,$id_empresa,$mysql){
		$sql="UPDATE ventas_pos_secciones
				SET
					nombre                 = '$nombre',
					id_padre               = '$id_padre',
					restaurante            = '$restaurante',
					id_bodega              = '$bodega',
					id_centro_costos       = '$idCcos',
					codigo_centro_costos   = '$codigoCcos',
					centro_costos          = '$nombreCcos',
					cuenta_ingreso_colgaap = '$cuenta_ingreso_colgaap',
					cuenta_ingreso_niif    = '$cuenta_ingreso_niif',
					eventos_asiste         = '$eventos_asiste',
					codigo_transaccion     = '$cod_tx',
					cambia_precio_items    = '$cambia_precio_items',
					cuenta_pago 		   = '$cuenta_pago',
					metodo_pago            = '$metodo_pago'
				WHERE activo   = 1
				AND id_empresa = $id_empresa
				AND id         = $id_seccion";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_secciones_pos/secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
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

	function eliminarSeccion($id_seccion,$id_empresa,$mysql){
		$sql="UPDATE ventas_pos_secciones 
				SET activo=0 
				WHERE  id_empresa = $id_empresa AND id = $id_seccion";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo "<script>
					MyLoading2('off');
					Ext.get('form_secciones').load({
						url     :  'configuracion_secciones_pos/secciones.php',
						scripts : true,
						nocache : true,
						params  :
						{
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

	function codTxItem($id_item,$id_seccion,$mysql){
		// CONSULTAR SI ESE ITEM YA TIENE UN COD TX CONFIGURADO
		$sql="SELECT cod_tx FROM items_cod_tx WHERE id_item='$id_item' AND id_seccion='$id_seccion' ";
		$query=$mysql->query($sql);
		$cod_tx = $mysql->result($query,0,'cod_tx');
		?>
			<style>
				.contentCodTx{
					background-color : #FFF;
					width            : 100%;
					height           : 100%;
					text-align       : center;
				}
			</style>
			<div class="contentCodTx">
				<table class="table-form">
					<tr>
						<td>Codigo TX</td>
						<td><input type="text" style="width: 150px;" id="codTx" value="<?= $cod_tx; ?>" ></td>
					</tr>
				</table>
			</div>

		<?php
	}

	function saveCodTx($codTx,$id_item,$id_seccion,$mysql){
		$sql="SELECT id,cod_tx FROM items_cod_tx WHERE activo=1 AND id_item='$id_item' AND id_seccion='$id_seccion' ";
		$query=$mysql->query($sql);
		$id = $mysql->result($query,0,'id');
		if ($id>0) {
			$sql="UPDATE items_cod_tx SET cod_tx='$codTx' WHERE id=$id";
			$query=$mysql->query($sql);
		}
		else{
			$sql="INSERT INTO items_cod_tx (id_item,id_seccion,cod_tx) VALUES ('$id_item','$id_seccion','$codTx') ";
			$query=$mysql->query($sql);
		}

		if (!$query) { $arrayResult = array('response' => 'failed', 'msg'=>'No se almaceno la informacion', 'debug'=>$sql ); }
		else{ $arrayResult = array('response' => 'success', 'msg'=>'se almaceno la informacion' ); }

		echo json_encode($arrayResult);
		// echo "<script>console.log('$codTx');</script>";
	}

?>

