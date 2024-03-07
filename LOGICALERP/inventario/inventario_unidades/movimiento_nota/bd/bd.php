<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");

	$empresa =$_SESSION['EMPRESA'];

	switch ($opc) {
		case 'guardarEntradaSalida':
			guardarEntradaSalida($idFila,$idItem,$cantidad,$costo,$tipoMovimiento,$id_nota,$consecutivo,$observaciones,$idBodega,$idSucursal,$empresa,$mysql);
		break;
	}

	//=========== FUNCION PARA GUARDAR UNA ENTRADA O UNA SALIDA DEL INVENTARIO ========//
	function guardarEntradaSalida($idFila,$idItem,$cantidad,$costo,$tipoMovimiento,$id_nota,$consecutivo,$observaciones,$idBodega,$idSucursal,$empresa,$mysql){

		// VALIDAR SI LA NOTA FUE UTILIZADA EN ALGUN OTRO MOVIMIENTO
		$sql="SELECT DISTINCT tipo
				FROM inventario_movimiento_notas
				WHERE
					activo           = 1
				AND id_nota 		 = '$id_nota'
				AND id_empresa       = '$empresa'
				AND id_sucursal      = '$idSucursal'
				AND id_bodega        = '$idBodega'";
		$query=$mysql->query($sql,$mysql->link);
		$tipoMovimientoNota = $mysql->result($query,0,'tipo');
		if ($tipoMovimientoNota=='' || $tipoMovimientoNota == $tipoMovimiento) {
			// INSERTAR EL REGISTRO DEL MOVIMIENTO
			$sql="INSERT INTO inventario_movimiento_notas
						(id_item,id_inventario_total,id_nota,consecutivo_nota,cantidad,costo,tipo,observaciones,id_bodega,id_sucursal,id_empresa)
					VALUES
						('$idItem','$idFila','$id_nota','$consecutivo','$cantidad','$costo','$tipoMovimiento','$observaciones','$idBodega','$idSucursal','$empresa')";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				if ($tipoMovimiento=='salida') {
					// MOVER EL KARDEX
		 			$sql   = "UPDATE inventario_totales AS IT
								SET IT.costos=IF(IT.cantidad-$cantidad = 0, 0, ((IT.cantidad * IT.costos) - ($cantidad*$costo))/(IT.cantidad-$cantidad)),
									IT.cantidad=IT.cantidad-$cantidad,
									IT.id_documento_update          = '$id_nota',
									IT.tipo_documento_update        = 'Nota Contable',
									IT.consecutivo_documento_update = ''
								WHERE IT.id_item=$idItem
			 						AND IT.activo = 1
			 						AND IT.id_ubicacion = '$idBodega'";
					$query=$mysql->query($sql,$mysql->link);
				}
				else{
					// MOVER EL KARDEX
		 			$sql   = "UPDATE inventario_totales AS IT
								SET IT.costos=((IT.cantidad * IT.costos)+($cantidad*$costo))/(IT.cantidad+$cantidad),
									IT.cantidad=IT.cantidad+$cantidad,
									IT.id_documento_update          = '$id_nota',
									IT.tipo_documento_update        = 'Nota Contable',
									IT.consecutivo_documento_update = ''
								WHERE IT.id_item=$idItem
			 						AND IT.activo = 1
			 						AND IT.id_ubicacion = '$idBodega'";
					$query=$mysql->query($sql,$mysql->link);
				}

				if (!$query) { echo '<script>
										//alert("Error!\nNo se actualizaron las cantidades del inventario!\nSi el problema persiste comuniquese con el administrador del sistema");
										MyLoading2("off",{icono:"fail",texto:"No se actulizo el inventario" });
									</script>';
				}
				else{
					echo'<script>
							Actualiza_Div_InventarioTotales("'.$idFila.'");
							Win_Ventana_entrada_salida.close();
							MyLoading2("off");
						</script>';
				}
			}
			else{
				echo '<script>
						//alert("Error!\nNo se inserto la nota relacionada ");
						MyLoading2("off",{icono:"fail",texto:"Error al insertar la nota" });
					</script>';
			}
		}
		else{
			echo '<script>
					//alert("Error!\nLa nota a cargar solo acepta articulos para '.$tipoMovimientoNota.' ");
					MyLoading2("off",{icono:"fail",texto:"La nota solo acepta movimientos de'.$tipoMovimientoNota.'" });
				</script>';
		}

	}

 ?>