<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'eliminarFactura':
			eliminarFactura($id_factura,$id_empresa,$opcGrillaContable,$link);
			break;
	}

	function eliminarFactura($id_factura,$id_empresa,$opcGrillaContable,$link){

		// ELIMINAR LOS ASIENTOS CONTABLES
		$sql   = "DELETE FROM asientos_colgaap WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$id_factura AND tipo_documento='FV' ";
		$query = mysql_query($sql,$link);
		if (!$query) {
			echo'<script>alert("Error!\nNo se puedo eliminar los asientos colgaap, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema")</script>';
			return;
		}

		$sql   = "DELETE FROM asientos_niif WHERE activo=1 AND id_empresa=$id_empresa AND id_documento=$id_factura AND tipo_documento='FV' ";
		$query = mysql_query($sql,$link);
		if (!$query) {
			echo'<script>alert("Error!\nNo se puedo eliminar los asientos niif, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema")</script>';
			return;
		}

		$sql   = "DELETE FROM ventas_facturas_cuentas WHERE activo=1 AND id_empresa=$id_empresa AND id_factura_venta=$id_factura ";
		$query = mysql_query($sql,$link);
		if (!$query) {
			echo'<script>alert("Error!\nNo se eliminaron las cuentas de la factura, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema")</script>';
			return;
		}

		$sql   = "DELETE FROM ventas_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_factura";
		$query = mysql_query($sql,$link);
		if (!$query) {
			echo'<script>alert("Error!\nNo se puedo eliminar la factura, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema")</script>';
			return;
		}

		echo'<script>
				Ext.get("contenedor_'.$opcGrillaContable.'").load({
						url     : "facturacion_cuentas/factura_ventas_cuentas_bloqueada.php",
						scripts : true,
						nocache : true,
						params  : { opcGrillaContable : "'.$opcGrillaContable.'" }
					});

				Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").disable();
				Ext.getCmp("btnExportar_'.$opcGrillaContable.'").disable();
				document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="";
			</script>';
	}



?>