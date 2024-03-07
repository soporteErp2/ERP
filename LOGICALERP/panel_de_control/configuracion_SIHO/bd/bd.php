<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'guardaTercero':
			guardaTercero($id,$nit,$tercero,$id_empresa,$mysql);
			break;
		case 'guardaTipoValor':
			guardaTipoValor($tipo,$id_empresa,$mysql);
			break;
		case 'guardaTipoValorPedido':
			guardaTipoValorPedido($tipo,$id_empresa,$mysql);
			break;

	}

	function guardaTercero($id_tercero,$nit,$tercero,$id_empresa,$mysql){

		// CONSULTAR SI YA SE INSERTO EL REGISTRO
		$sql         = "SELECT id,nit,tercero FROM web_service_tercero_causacion WHERE activo=1 AND id_empresa=$id_empresa";
		$query       = $mysql->query($sql,$link);
		$id_registro = $mysql->result($query,0,'id');
		// $nit         = $mysql->result($query,0,'nit');
		// $tercero     = $mysql->result($query,0,'tercero');

		// SI EXISTEN REGISTROS SE ACTUALIZAN
		if ($id_registro>0) {

			$sql="UPDATE web_service_tercero_causacion SET id_tercero=$id_tercero WHERE activo=1 AND id_empresa=$id_empresa";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo '<script>
							MyLoading2("off");
							Win_Ventana_buscar_tercero.close();
							document.getElementById("nit_tercero").innerHTML = "'.$nit.'";
							document.getElementById("tercero").innerHTML     = "'.$tercero.'";
						</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{texto:"No se actualizo el tercero",icono:"fail",duracion:2000});
					</script>';
			}

		}
		// SI NO EXISTEN REGISTROS SE INSERTA
		else{

			$sql="INSERT INTO web_service_tercero_causacion (id_tercero,id_empresa) VALUES ($id_tercero,$id_empresa)";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo '<script>
							MyLoading2("off");
							Win_Ventana_buscar_tercero.close();
							document.getElementById("nit_tercero").innerHTML = "'.$nit.'";
							document.getElementById("tercero").innerHTML     = "'.$tercero.'";
						</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{texto:"No se Inserto el tercero",icono:"fail",duracion:2000} );
					</script>';
			}
		}
	}

	function guardaTipoValor($tipo,$id_empresa,$mysql){
		$sql    = "SELECT id FROM ventas_remisiones_configuracion WHERE activo=1 AND id_empresa=$id_empresa";
		$query  = $mysql->query($sql,$mysql->link);
		$id_row = $mysql->result($query,0,'id');

		if ($id_row>0) {
			$sql="UPDATE ventas_remisiones_configuracion SET valor='$tipo' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_row ";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo "<script>MyLoading2('off');</script>";
			}
			else{
				echo "<script>MyLoading2('off',{texto:'No se actualizo el registro!',duracion:2000,icono:'fail'});</script>";
			}
		}
		else{
			$sql="INSERT INTO ventas_remisiones_configuracion (valor,id_empresa) VALUES('$tipo',$id_empresa)";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo "<script>MyLoading2('off');</script>";
			}
			else{
				echo "<script>MyLoading2('off',{texto:'No se actualizo el registro!',duracion:2000,icono:'fail'});</script>";
			}
		}
	}

	function guardaTipoValorPedido($tipo,$id_empresa,$mysql){
		$sql    = "SELECT id FROM ventas_pedidos_configuracion WHERE activo=1 AND id_empresa=$id_empresa";
		$query  = $mysql->query($sql,$mysql->link);
		$id_row = $mysql->result($query,0,'id');

		if ($id_row>0) {
			$sql="UPDATE ventas_pedidos_configuracion SET valor='$tipo' WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_row ";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo "<script>MyLoading2('off');</script>";
			}
			else{
				echo "<script>MyLoading2('off',{texto:'No se actualizo el registro!',duracion:2000,icono:'fail'});</script>";
			}
		}
		else{
			$sql="INSERT INTO ventas_pedidos_configuracion (valor,id_empresa) VALUES('$tipo',$id_empresa)";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo "<script>MyLoading2('off');</script>";
			}
			else{
				echo "<script>MyLoading2('off',{texto:'No se actualizo el registro!',duracion:2000,icono:'fail'});</script>";
			}
		}
	}


 ?>