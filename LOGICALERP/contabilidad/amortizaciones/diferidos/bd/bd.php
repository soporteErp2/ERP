<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];

	switch ($opc) {

		case 'agregarActualizarDiferido':
			agregarActualizarDiferido($id_diferido,$id_sucursal,$id_documento,$tipo_documento,$consecutivo_documento,$id_tercero,$documento_tercero,$tercero,$fecha_inicio,$estado,$valor_diferido,$meses,$cuota,$id_cuenta_debito,$cuenta_debito,$descripcion_cuenta_debito,$id_cuenta_credito,$cuenta_credito,$descripcion_cuenta_credito,$id_centro_costos,$cod_centro_costos,$centro_costos,$id_empresa,$mysql);
			break;
		case 'eliminarDiferido':
			eliminarDiferido($id_diferido,$id_empresa,$mysql);
			break;

	}

	function agregarActualizarDiferido($id_diferido,$id_sucursal,$id_documento,$tipo_documento,$consecutivo_documento,$id_tercero,$documento_tercero,$tercero,$fecha_inicio,$estado,$valor_diferido,$meses,$cuota,$id_cuenta_debito,$cuenta_debito,$descripcion_cuenta_debito,$id_cuenta_credito,$cuenta_credito,$descripcion_cuenta_credito,$id_centro_costos,$cod_centro_costos,$centro_costos,$id_empresa,$mysql){
		// VALIDAR SI ES INSERT O UPDATE
		if ($id_diferido>0) {
			$sql="UPDATE diferidos
					SET
						estado                     = '$estado',
						meses                      = '$meses',
						id_cuenta_debito           = '$id_cuenta_debito',
						cuenta_debito              = '$cuenta_debito',
						descripcion_cuenta_debito  = '$descripcion_cuenta_debito',
						id_cuenta_credito          = '$id_cuenta_credito',
						cuenta_credito             = '$cuenta_credito',
						descripcion_cuenta_credito = '$descripcion_cuenta_credito',
						id_centro_costos           = '$id_centro_costos',
						cod_centro_costos          = '$cod_centro_costos',
						centro_costos              = '$centro_costos'

				WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_diferido ";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo '<script>
						Actualiza_Div_Diferidos('.$id_diferido.');
						Win_Ventana_form_diferidos.close();
						MyLoading2("off",{icono:"sucess",duracion:2000});
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"No se Actualizo El registro",duracion:2000});
					</script>';;
			}
		}
		else{

			$id_usuario        = $_SESSION['IDUSUARIO'];
			$documento_usuario = $_SESSION['CEDULAFUNCIONARIO'];
			$nombre_usuario    = $_SESSION['NOMBREFUNCIONARIO'];

			$sql="INSERT INTO diferidos
					(
						id_documento,
						tipo_documento,
						consecutivo_documento,
						id_tercero,
						documento_tercero,
						tercero,
						fecha_inicio,
						estado,
						id_cuenta_debito,
						cuenta_debito,
						descripcion_cuenta_debito,
						id_cuenta_credito,
						cuenta_credito,
						descripcion_cuenta_credito,
						id_centro_costos,
						cod_centro_costos,
						centro_costos,
						valor,
						meses,
						saldo,
						id_usuario,
						documento_usuario,
						nombre_usuario,
						id_sucursal,
						id_empresa
					)
					VALUES
					(
						'$id_documento',
						'$tipo_documento',
						'$consecutivo_documento',
						'$id_tercero',
						'$documento_tercero',
						'$tercero',
						'$fecha_inicio',
						'$estado',
						'$id_cuenta_debito',
						'$cuenta_debito',
						'$descripcion_cuenta_debito',
						'$id_cuenta_credito',
						'$cuenta_credito',
						'$descripcion_cuenta_credito',
						'$id_centro_costos',
						'$cod_centro_costos',
						'$centro_costos',
						'$valor_diferido',
						'$meses',
						'$valor_diferido',
						'$id_usuario',
						'$documento_usuario',
						'$nombre_usuario',
						'$id_sucursal',
						'$id_empresa'
					)
					";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				$insert_id=$mysql->insert_id();
				echo '<script>
						Inserta_Div_Diferidos('.$insert_id.');
						Win_Ventana_form_diferidos.close();
						MyLoading2("off",{icono:"sucess",duracion:2000});
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"No se inserto El registro",duracion:2000});
					</script>';
			}
		}
	}

	function eliminarDiferido($id_diferido,$id_empresa,$mysql){
		// PENDIENTE, VALIDAR SI ESTA CRUZADO EN UN PROCESO DE AMORTIZACION
		$sql="SELECT COUNT(id_diferido) AS cont FROM amortizaciones_diferidos WHERE activo=1 AND id_empresa=$id_empresa AND id_diferido=$id_diferido";
		$query=$mysql->query($sql,$mysql->link);
		$cantDiferido = $mysql->result($query,0,'cont');

		if ($cantDiferido>0) {
			echo '<script>
					alert("Error\nNo se Elimino El registro, por que esta cruzado en '.$cantDiferido.' procesos de amortizacion");
					MyLoading2("off",{icono:"fail",texto:"No se Elimino El registro ",duracion:2000});
				</script>';
			return;
		}

		$sql="UPDATE diferidos SET activo=0 WHERE activo=1 ANd id_empresa=$id_empresa ANd id=$id_diferido";
		$query=$mysql->query($sql,$mysql->link);
		if ($query) {
			echo '<script>
					Elimina_Div_Diferidos('.$id_diferido.');
					Win_Ventana_form_diferidos.close();
					MyLoading2("off",{icono:"sucess",texto:"Se Elimino El registro",duracion:2000});
				</script>';
		}
		else{
			echo '<script>
					Elimina_Div_Diferidos('.$id_diferido.');
					Win_Ventana_form_diferidos.close();
					MyLoading2("off",{icono:"fail",texto:"No se Elimino El registro",duracion:2000});
				</script>';
		}
	}


?>