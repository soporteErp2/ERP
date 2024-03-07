<?php
include("../../../../configuracion/conectar.php");
include("../../../../configuracion/define_variables.php");
	$empresa=$_SESSION['EMPRESA'];
	switch ($opc) {
		case 'guardarEntradaSalida':
			guardarEntradaSalida($idFila,$idItem,$consecutivo,$observaciones,$idSucursal,$idBodega,$empresa,$link);
		break;
	}

	//=========== FUNCION PARA GUARDAR UNA ENTRADA O UNA SALIDA DEL INVENTARIO ========//
	function guardarEntradaSalida($idFila,$idItem,$consecutivo,$observaciones,$idSucursal,$idBodega,$empresa,$link){
		// echo ' idFila: '.$idFila.' idItem: '.$idItem.' cantidad: '.$cantidad.' tipoMovimiento: '.$tipoMovimiento.' consecutivo: '.$consecutivo.' observaciones: '.$observaciones.' idBodega: '.$idBodega.' idSucursal: '.$idSucursal;
		//CONSULTAMOS LA NOTA DEL CONSECUTIVO Y VALIDAMOS SI ESTA GENERADA Y DISPONIBLE
		$sqlNota="SELECT id FROM nota_contable_general WHERE consecutivo='$consecutivo' AND id_sucursal='$idSucursal' AND activo=1 ";
		$queryNota=mysql_query($sqlNota,$link);
		$res=mysql_result($queryNota,0,'id');
		if ($res>0) {
			//SI LA NOTA EXISTE ENTONCES VALIDAMOS SI YA FUE UTILIZADA, ES DECIR SI YA SE ASIGNO UN ARTICULO A ESA NOTA, PARA VALIDAR QUE SEA SOLO DE ENTRADA O DE SALIDA
			$sqlTipoMovimiento="SELECT DISTINCT tipo FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$empresa' AND id_sucursal='$idSucursal' AND id_bodega='$idBodega' ";
			$queryTipoMovimiento=mysql_query($sqlTipoMovimiento,$link);
			$tipoMovimientoRegistro=mysql_result($queryTipoMovimiento,0,'tipo');

			if ($tipoMovimientoRegistro=='' || $tipoMovimientoRegistro=='baja_activo_fijo') {
				//INSERTAMOS EL REGISTRO EN LA TABLA inventario_movimiento_notas
				$sqlMovimiento="INSERT INTO inventario_movimiento_notas (id_item,id_inventario_total,consecutivo_nota,cantidad,tipo,observaciones,id_bodega,id_sucursal,id_empresa)
								VALUES ('$idItem','$idFila','$consecutivo','1','baja_activo_fijo','$observaciones','$idBodega','$idSucursal','$empresa')";
				$queryMovimiento=mysql_query($sqlMovimiento,$link);
				if ($queryMovimiento) {

					//SI SE INGRESARON ARTICULOS AL INVENTARIO
					$sqlUpdate="UPDATE activos_fijos SET activo=0 WHERE id='$idItem' AND id_sucursal='$idSucursal' AND id_bodega='$idBodega' AND id_empresa='$empresa'";
					$queryUpdate=mysql_query($sqlUpdate,$link);


					if (!$queryUpdate) {
						echo '<script>alert("Error!\nNo se dio de baja el activo fijo!\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
					}else{
						echo '<script>
							 	Elimina_Div_ActivosFijos("'.$idItem.'");
							 	Win_Ventana_baja_activo.close();
							 </script>';
					}

				}else{
					echo '<script>alert("Error!\nNo se inserto el registro en el sistema!");</script>';
				}
			}else{
				echo '<script>alert("Error!\nLa nota a cargar ya esta relacionada con entrada o salida de inventario! ");</script>';
			}

		}else{
			echo '<script>alert("Error!\nEl consecutivo no corresponde a una nota existente");</script>';
		}
	}

		// SET NEW.codigo_item = (SELECT code_bar FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);
		// SET NEW.nombre      = (SELECT nombre_equipo FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);
		// SET NEW.sucursal    = (SELECT sucursal  FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);
		// SET NEW.bodega      = (SELECT bodega  FROM activos_fijos  WHERE id=NEW.id_item AND id_empresa=NEW.id_empresa);

 ?>