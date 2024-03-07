<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	verificaEstadoDocumento($id_factura,$opcGrillaContable,$link);

	switch ($opc) {
		case 'updateCcos':
			updateCcos($idCcos,$nombre,$codigo,$opcGrillaContable,$id_factura,$id_empresa,$link);
			break;
		case 'UpdateFechaRemision':
			UpdateFechaRemision($id,$fecha,$opcGrillaContable,$id_empresa,$mysql);
			break;
	}

	function updateCcos($idCcos,$nombre,$codigo,$opcGrillaContable,$id_factura,$id_empresa,$link){
		$sql   = "UPDATE ventas_remisiones SET id_centro_costo='$idCcos' WHERE id='$id_factura' AND id_empresa='$id_empresa' AND activo=1";
		$query = mysql_query($sql,$link);
		if(!$query){ echo'<script>alert("Error! Sin conexion con el servidor.\nSi el problema persiste comuniquese con el administrador del sistema.");</script>'; exit; }

		echo'<script>
				document.getElementById("cCos_'.$opcGrillaContable.'").value = "'.$codigo.' '.$nombre.'";
				Win_Ventana_Ccos_'.$opcGrillaContable.'.close();
			</script>';
	}

		//FUNCION PARA VERIFICAR EL ESTADO DEL DOCUMENTO
	function verificaEstadoDocumento($id_documento,$opcGrillaContable,$link){
		$sql   = "SELECT estado,id_bodega,consecutivo FROM ventas_remisiones WHERE id=$id_documento";
		$query = mysql_query($sql,$link);

		$estado    = mysql_result($query,0,'estado');
		$id_bodega = mysql_result($query,0,'id_bodega');
		$consecutivo = mysql_result($query,0,'consecutivo');
		if ($estado==1) {
			$mensaje='Error!\nEl Documento a sido generado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==2) {
			$mensaje='Error!\nEl Documento a sido cruzado \nNo se puede realizar mas acciones sobre el';
		}
		else if ($estado==3) {
			$mensaje='Error!\nEl Documento a sido cancelado \nNo se puede realizar mas acciones sobre el';
		}

		if ($estado>0) {
			echo'<script>
					alert("'.$mensaje.'");
					if (document.getElementById("Win_Ventana_descripcion_Articulo_factura")) {
						Win_Ventana_descripcion_Articulo_factura.close();
					}
					Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "bd/grillaContableBloqueada.php",
						scripts : true,
						nocache : true,
						params  :
						{
							filtro_bodega     : "'.$id_bodega.'",
							opcGrillaContable : "'.$opcGrillaContable.'",
							id_factura_venta  : "'.$id_documento.'"
						}
					});

					Ext.getCmp("btnNueva'.$opcGrillaContable.'").enable();
					document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="Remision de Venta<br>N. '.$consecutivo.'";
				</script>';
			exit;
		}
	}

	function UpdateFechaRemision($id,$fecha,$opcGrillaContable,$id_empresa,$mysql){
		$sql="UPDATE ventas_remisiones SET fecha_inicio='$fecha' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id ";
		$query=$mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>alert("Error!\nNo se actualizo la fecha del documento");</script>';
		}
	}

?>