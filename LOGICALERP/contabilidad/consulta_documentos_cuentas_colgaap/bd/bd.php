<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	switch ($opc) {
		case 'consultarCuenta':
			consultarCuenta($cont,$cuenta,$id_sucursal,$id_empresa,$opcGrilla,$tabla_asiento,$link);
			break;

		case 'updateContabilizacionCuentas':
				if ($tabla_asiento=='asientos_colgaap') {
					updateContabilizacionCuentas($jsonCuentas,$id_sucursal,$id_empresa,$link);
				}else{
					updateContabilizacionCuentasNiif($jsonCuentas,$id_sucursal,$id_empresa,$link);
				}

			break;

	}

	//======================== BUSCAR LA CUENTA CUANDO SE DIGITA EN LA VENTANA EDICION ==================//
	function consultarCuenta($contFila,$cuenta,$id_sucursal,$id_empresa,$opcGrilla,$tabla_asiento,$link){
		$tabla_puc=( $tabla_asiento=='asientos_colgaap')? 'puc' : 'puc_niif' ;
		$sql   = "SELECT COUNT(id) AS cont, id, id_sucursal, descripcion FROM $tabla_puc WHERE id_empresa='$id_empresa' AND activo=1 AND cuenta='$cuenta' AND (id_sucursal='' OR id_sucursal='$id_sucursal') ";
		$query = mysql_query($sql,$link);

		$id               = mysql_result($query,0,'id');
		$contCuenta       = mysql_result($query,0,'cont');
		$descripcion      = mysql_result($query,0,'descripcion');
		$idSucursalCuenta = mysql_result($query,0,'id_sucursal');

		if($contCuenta == 0){
			echo'<script>
					document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").value          = "";
					document.getElementById("idCuenta'.$opcGrilla.'_'.$contFila.'").value        = "";
					document.getElementById("descripcion'.$opcGrilla.'_'.$contFila.'").innerHTML = "";

					document.getElementById("cuenta'.$opcGrilla.'_'.$contFila.'").focus();
					alert("Aviso,\nLa cuenta No. '.$cuenta.' no se encuentra registrada en el sistema.\nO pertenece a otra sucursal de la empresa");
				</script>';
			return;
		}
		if($idSucursalCuenta > 0 && $idSucursalCuenta != $id_sucursal){ echo'<script>alert("Aviso,\nLa cuenta No. '.$cuenta.' no se encuentra asociada a la presente sucursal.");</script>'; return; }

		echo'<script>
				document.getElementById("idCuenta'.$opcGrilla.'_'.$contFila.'").value    = "'.$cuenta.'";
				document.getElementById("descripcion'.$opcGrilla.'_'.$contFila.'").innerHTML = "'.$descripcion.'";
				// console.log("'.$tabla_puc.'");
			</script>';
	}

	//======================== ACTUALIZAR LAS CUENTAS CUANDO SE EDITA EN COLGAAP =======================//
	function updateContabilizacionCuentas($jsonCuentas,$id_sucursal,$id_empresa,$link){
		$contCuentas = 0;
		$whereNiif   = "";
		$jsonCuentas = json_decode($jsonCuentas);

		$valueInsertNiif    = "";
		$valueInsertColgaap = "";

		//CICLO JSON PARA AGRUPAR CUENTAS REPETIDAS
		foreach ($jsonCuentas as $indice => $arrayCuenta) {
			if($indice == 'datos'){ continue; }

			if($arraySaldoCuenta[$arrayCuenta->cuenta]['debe'] >= 0){
				$arraySaldoCuenta[$arrayCuenta->cuenta]['debe']  += $arrayCuenta->debito;
				$arraySaldoCuenta[$arrayCuenta->cuenta]['haber'] += $arrayCuenta->credito;
			}
			else{
				$arraySaldoCuenta[$arrayCuenta->cuenta]['debe']  = $arrayCuenta->debito;
				$arraySaldoCuenta[$arrayCuenta->cuenta]['haber'] = $arrayCuenta->credito;
			}
			$arraySaldoCuenta[$arrayCuenta->cuenta]['id_centro_costos']=$arrayCuenta->id_centro_costos;
		}

		foreach ($arraySaldoCuenta as $cuenta => $arrayCuenta) {
			$contCuentas++;
			$whereNiif .= "cuenta = '".$cuenta."' OR ";
			$valueInsertColgaap .= "('".$jsonCuentas->datos->id_documento."',
										'".$jsonCuentas->datos->consecutivo_documento."',
										'".$jsonCuentas->datos->tipo_documento."',
										'".$jsonCuentas->datos->tipo_documento_extendido."',
										'".$jsonCuentas->datos->id_documento."',
										'".$jsonCuentas->datos->tipo_documento."',
										'".$jsonCuentas->datos->consecutivo_documento."',
										'".$jsonCuentas->datos->fecha_documento."',
										'".$arrayCuenta['debe']."',
										'".$arrayCuenta['haber']."',
										'".$cuenta."',
										'".$jsonCuentas->datos->id_tercero."',
										'".$arrayCuenta['id_centro_costos']."',
										'".$id_sucursal."',
										'".$id_empresa."'),";
		}

		if($contCuentas == 1){ echo'<script>alert("Aviso,\nLa nueva contabilizacion no cumple doble partida."); Ext.getCmp("btnGuardarActualizarCuentas").enable();</script>'; }

		$whereNiif = substr($whereNiif, 0, -4);
		$selectCuentasNiif = "SELECT cuenta,cuenta_niif
								FROM puc
								WHERE id_empresa='$id_empresa'
									AND (id_sucursal='$id_sucursal' OR id_sucursal=0)
									AND activo=1
									AND ($whereNiif)";
		$queryCuentasNiif  = mysql_query($selectCuentasNiif,$link);

		while ($row = mysql_fetch_array($queryCuentasNiif)) {
			$valueInsertNiif .= "('".$jsonCuentas->datos->id_documento."',
										'".$jsonCuentas->datos->consecutivo_documento."',
										'".$jsonCuentas->datos->tipo_documento."',
										'".$jsonCuentas->datos->tipo_documento_extendido."',
										'".$jsonCuentas->datos->id_documento."',
										'".$jsonCuentas->datos->tipo_documento."',
										'".$jsonCuentas->datos->consecutivo_documento."',
										'".$jsonCuentas->datos->fecha_documento."',
										'".$arraySaldoCuenta[$row['cuenta']]['debe']."',
										'".$arraySaldoCuenta[$row['cuenta']]['haber']."',
										'".$row['cuenta_niif']."',
										'".$jsonCuentas->datos->id_tercero."',
										'".$arraySaldoCuenta[$row['cuenta']]['id_centro_costos']."',
										'".$id_sucursal."',
										'".$id_empresa."'),";
		}
		$sqlDeleteColgaap = "DELETE
							FROM asientos_colgaap
							WHERE id_documento='".$jsonCuentas->datos->id_documento."'
								AND tipo_documento='".$jsonCuentas->datos->tipo_documento."'
								AND id_sucursal='$id_sucursal'
								AND id_empresa = '$id_empresa'";
		$queryDeleteColgaap = mysql_query($sqlDeleteColgaap,$link);

		$sqlDeleteNiif = "DELETE
							FROM asientos_niif
							WHERE id_documento='".$jsonCuentas->datos->id_documento."'
								AND tipo_documento='".$jsonCuentas->datos->tipo_documento."'
								AND id_sucursal='$id_sucursal'
								AND id_empresa = '$id_empresa'";
		$queryDeleteNiif = mysql_query($sqlDeleteNiif,$link);


		$valueInsertNiif    = substr($valueInsertNiif, 0, -1);
		$valueInsertColgaap = substr($valueInsertColgaap, 0, -1);

		$sqlInsertColgaap = "INSERT INTO asientos_colgaap(
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								id_documento_cruce,
								tipo_documento_cruce,
								numero_documento_cruce,
								fecha,
								debe,
								haber,
								codigo_cuenta,
								id_tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa)
							VALUES $valueInsertColgaap";

		$queryColgaap = mysql_query($sqlInsertColgaap,$link);

		$sqlInsertNiif = "INSERT INTO asientos_niif(
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								id_documento_cruce,
								tipo_documento_cruce,
								numero_documento_cruce,
								fecha,
								debe,
								haber,
								codigo_cuenta,
								id_tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa)
							VALUES $valueInsertNiif";

		$queryNiif = mysql_query($sqlInsertNiif,$link);
		// echo $sqlInsertNiif.'<script>Ext.getCmp("btnGuardarActualizarCuentas").enable();</script>'; exit;
		echo'<script>
				Ext.getCmp("btnGuardarActualizarCuentas").enable();
				MyBusquedaconsultarCuentasColgaap();
				Win_Ventana_editar_cuentas_documento.close();
			</script>';
	}

	//======================== ACTUALIZAR LAS CUENTAS CUANDO SE EDITA DESDE NIIF ========================//
	function updateContabilizacionCuentasNiif($jsonCuentas,$id_sucursal,$id_empresa,$link){
		$contCuentas = 0;
		$whereNiif   = "";
		$jsonCuentas = json_decode($jsonCuentas);

		$valueInsertNiif    = "";

		//CICLO JSON PARA AGRUPAR CUENTAS REPETIDAS
		foreach ($jsonCuentas as $indice => $arrayCuenta) {
			if($indice == 'datos'){ continue; }

			if($arraySaldoCuenta[$arrayCuenta->cuenta]['debe'] >= 0){
				$arraySaldoCuenta[$arrayCuenta->cuenta]['debe']  += $arrayCuenta->debito;
				$arraySaldoCuenta[$arrayCuenta->cuenta]['haber'] += $arrayCuenta->credito;
			}
			else{
				$arraySaldoCuenta[$arrayCuenta->cuenta]['debe']  = $arrayCuenta->debito;
				$arraySaldoCuenta[$arrayCuenta->cuenta]['haber'] = $arrayCuenta->credito;
			}
			$arraySaldoCuenta[$arrayCuenta->cuenta]['id_centro_costos']=$arrayCuenta->id_centro_costos;
		}

		foreach ($arraySaldoCuenta as $cuenta => $arrayCuenta) {
			$contCuentas++;
			$valueInsertNiif .= "('".$jsonCuentas->datos->id_documento."',
										'".$jsonCuentas->datos->consecutivo_documento."',
										'".$jsonCuentas->datos->tipo_documento."',
										'".$jsonCuentas->datos->tipo_documento_extendido."',
										'".$jsonCuentas->datos->id_documento."',
										'".$jsonCuentas->datos->tipo_documento."',
										'".$jsonCuentas->datos->consecutivo_documento."',
										'".$jsonCuentas->datos->fecha_documento."',
										'".$arrayCuenta['debe']."',
										'".$arrayCuenta['haber']."',
										'".$cuenta."',
										'".$jsonCuentas->datos->id_tercero."',
										'".$arrayCuenta['id_centro_costos']."',
										'".$id_sucursal."',
										'".$id_empresa."'),";
		}

		if($contCuentas == 1){ echo'<script>alert("Aviso,\nLa nueva contabilizacion no cumple doble partida."); Ext.getCmp("btnGuardarActualizarCuentas").enable();</script>'; }

		$sqlDeleteNiif = "DELETE
							FROM asientos_niif
							WHERE id_documento='".$jsonCuentas->datos->id_documento."'
								AND tipo_documento='".$jsonCuentas->datos->tipo_documento."'
								AND id_sucursal='$id_sucursal'
								AND id_empresa = '$id_empresa'";
		$queryDeleteNiif = mysql_query($sqlDeleteNiif,$link);


		$valueInsertNiif    = substr($valueInsertNiif, 0, -1);

		$sqlInsertNiif = "INSERT INTO asientos_niif(
								id_documento,
								consecutivo_documento,
								tipo_documento,
								tipo_documento_extendido,
								id_documento_cruce,
								tipo_documento_cruce,
								numero_documento_cruce,
								fecha,
								debe,
								haber,
								codigo_cuenta,
								id_tercero,
								id_centro_costos,
								id_sucursal,
								id_empresa)
							VALUES $valueInsertNiif";

		$queryNiif = mysql_query($sqlInsertNiif,$link);
		// echo print_r($jsonCuentas).$sqlInsertNiif.'<script>Ext.getCmp("btnGuardarActualizarCuentas").enable();</script>';exit;
		echo'<script>
				Ext.getCmp("btnGuardarActualizarCuentas").enable();
				MyBusquedaconsultarCuentasColgaap();
				Win_Ventana_editar_cuentas_documento.close();
			</script>';
	}

?>