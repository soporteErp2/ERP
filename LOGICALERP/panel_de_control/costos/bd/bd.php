<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../../../funciones_globales/funciones_php/randomico.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($op) {
		case 'guardarDiasOrdenes':
			guardarDiasOrdenes($dias,$opc,$id_empresa,$link);
		break;

		case 'guardarConsecutivosDocumentos':
			guardarConsecutivosDocumentos($jsonConsecutivos,$filtro_sucursal,$id_empresa,$link);
		break;

		case 'guardarCostosDocumento':
		    guardarCostosDocumento($jsonCostos,$nombre_documento,$id_empresa,$link);
		break;

		case 'updateCostosDocumento':
		    updateCostosDocumento($jsonCostos,$id_documento,$nombre_documento,$id_empresa,$link);
		break;

		case 'sincronizaPucImpuestoNiif':
			sincronizaPucImpuestoNiif($idInput,$idInput1,$idInput2,$cuenta,$id_empresa,$link);
			break;

		case 'validarCuenta':
			validarCuenta($nombreTabla,$cuenta,$id_empresa,$link);
			break;

		case 'deleteCostosDocumento':
			deleteCostosDocumento($id,$id_empresa,$link);
			break;
	}

	function guardarDiasOrdenes($dias,$opc,$id_empresa,$link){

		//consultamos para determibnar si ya se habia insertado un valor, si se inserto entonces se actualiza y si no se inserta
		$sql   = "SELECT id FROM configuracion_vencimiento_documentos WHERE activo=1 AND documento='$opc' AND id_empresa='$id_empresa' LIMIT 0,1";
		$query = mysql_query($sql,$link);
		$id    = mysql_result($query,0,'id');

		if ($id>0) {
			$sql   = "UPDATE configuracion_vencimiento_documentos SET dias_vencimiento = '$dias' WHERE activo=1  AND documento='$opc' AND id_empresa='$id_empresa'";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizo la informacion");</script>
				<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!" >';
			}
			else{
				echo ' <img src="../../../../temas/clasico/images/BotonesTabs/ok16.png" style="margin-top: 3px;" title="Guardado" >';
			}
		}
		else
		{
			$sql   = "INSERT INTO configuracion_vencimiento_documentos (dias_vencimiento,documento,id_empresa) VALUES ('$dias','$opc','$id_empresa')";
			$query = mysql_query($sql,$link);
			if (!$query) {
				echo '<script>alert("Error!\nNo se actualizo la informacion");</script>
				<img src="../../../../temas/clasico/images/BotonesTabs/alert.png" style="margin-top: 3px;" title="No se guardo!" >';
			}
			else{
				echo ' <img src="../../../../temas/clasico/images/BotonesTabs/ok16.png" style="margin-top: 3px;" title="Guardado" >';
			}
		}
	}

	function guardarConsecutivosDocumentos($jsonConsecutivos,$filtro_sucursal,$id_empresa,$link){

		$arrayConsecutivos = explode(',',$jsonConsecutivos);
		$contError         = 0;
		$msgError          = '';

		foreach ($arrayConsecutivos as $indice => $value) {
			$arrayvalues    = explode(':', $value);
			$newConsecutivo = $arrayvalues[1];
			$documento      = $arrayvalues[0];

			$updateConsecutivo = "UPDATE configuracion_consecutivos_documentos SET consecutivo = '$newConsecutivo' WHERE activo=1 AND id_empresa='$id_empresa' AND id_sucursal='$filtro_sucursal' AND documento='$documento' AND modulo='compra'";
			$queryConsecutivo  = mysql_query($updateConsecutivo,$link);

			if(!$queryConsecutivo){ $contError++; $msgError .= '\n'.$documento; }
		}

		if($contError > 0){
			$msgError = ($contError > 1)? 'Error,\nLos siguientes consecutivos no se han almacenado:\n'.$msgError : 'Error,\nEl siguiente consecutivo no se han almacenado:\n'.$msgError;
			echo '<script>alert("'.$msgError.'");</script>'; exit;
		}

		echo '<script>Win_Ventana_Sucursal.close();</script>';
	}

	function  guardarCostosDocumento($jsonCostos,$nombre_documento,$id_empresa,$link){

			$jsonCostos    = json_decode($jsonCostos);
			$random        = responseUnicoRanomico();

			$sqlInsert_1   = "INSERT INTO costo_documento(random,nombre,id_empresa) VALUES ('$random','$nombre_documento','$id_empresa')";
			$queryInsert_1 = mysql_query($sqlInsert_1,$link);

			$sqlSelect     = "SELECT id FROM costo_documento WHERE random = '$random' AND id_empresa = '$id_empresa'";
			$querySelect   = mysql_query($sqlSelect,$link);
			$id_costo      = mysql_result($querySelect,0,'id');



		    foreach ($jsonCostos as $id => $valor) {


                   //echo 'id_costo: '.$id_costo.' id: '.$id.' valor: '.$valor->valor.' centro_costo: '.$valor->ccos.' cuenta: '.$valor->cuenta_colgaap.' cuenta: '.$valor->cuenta_niif.'<br>';

                   $valueInsert .= "('$id_costo',
                   	                 '$valor->id_costo_tipo',
                   	                 '$valor->valor',
                   	                 '$valor->ccos',
                   	                 '$valor->cuenta_colgaap',
                   	                 '$valor->cuenta_niif',
                                     '$id_empresa'),";




		    }

		    $valueInsert = substr($valueInsert, 0, -1);

	        $sqlInsert   = "INSERT INTO costo_documento_porcentaje(id_costo_documento,id_costo_tipo,valor,id_centro_costos,id_cuenta_colgaap,id_cuenta_niif,id_empresa)
	        			    VALUES $valueInsert";

	        $queryInsert = mysql_query($sqlInsert,$link);


		    echo'<script>
		    		Inserta_Div_costosDocumentos('.$id_costo.');
		    		Win_Ventana_Costos_Documento_insert.close();
		    	</script>';
	}

	function updateCostosDocumento($jsonCostos,$id_documento,$nombre_documento,$id_empresa,$link){

			$jsonCostos   = json_decode($jsonCostos);

			$updateNombre = "UPDATE costo_documento SET nombre = '$nombre_documento' WHERE activo=1 AND id_empresa='$id_empresa' AND id = '$id_documento'";
			$queryNombre  = mysql_query($updateNombre,$link);

			if(!$queryNombre){ echo '<script>alert("Ha habido un error")</script>'; }

			foreach ($jsonCostos as $id => $valor) {


                $update = "UPDATE costo_documento_porcentaje
                                    SET valor = '$valor->valor',
                                        id_centro_costos = '$valor->ccos',
                                        id_cuenta_colgaap = '$valor->cuenta_colgaap',
                                        id_cuenta_niif = '$valor->cuenta_niif'
                           WHERE activo=1 AND id_empresa='$id_empresa' AND id = '$id'";


		        $query  = mysql_query($update,$link);


		    }

		    echo'<script>
		    		Actualiza_Div_costosDocumentos('.$id_documento.');
		    		Win_Ventana_Costos_Documento_update.close();
		    	</script>';

	}

	function sincronizaPucImpuestoNiif($idInput,$idInput1,$idInput2,$cuenta,$id_empresa,$link){

		$sqlNiif         = "SELECT COUNT(PN.id) AS cont_niif, PN.descripcion, P.cuenta_niif,PN.id
								FROM puc AS P, puc_niif AS PN
							WHERE P.activo=1
								AND P.cuenta='$cuenta'
								AND P.id_empresa='$id_empresa'
								AND PN.activo=1
								AND PN.id_empresa=P.id_empresa
								AND PN.cuenta=P.cuenta_niif
								LIMIT 0,1";
		$queryNiif       = mysql_query($sqlNiif,$link);

		$contNiif        = mysql_result($queryNiif,0,'cont_niif');
		$id_Niif         = mysql_result($queryNiif,0,'id');
		$cuentaNiif      = mysql_result($queryNiif,0,'cuenta_niif');
		$descripcionNiif = mysql_result($queryNiif,0,'descripcion');

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{
			  echo'<script>
			             document.getElementById("'.$idInput1.'").value = "'.$cuentaNiif.'";
		                 document.getElementById("'.$idInput2.'").value = "'.$id_Niif.'";
		           </script>';
		}

		echo'<img src="img/refresh.png" onclick="sincronizaCuentaDocumentosEnNiif(\''.$idInput.'\',\''.$idInput1.'\',\''.$idInput2.'\')"/>';

		echo $idInput;
	}

	function validarCuenta($nombreTabla,$cuenta,$id_empresa,$link){
		$sqlCuenta   = "SELECT COUNT(id) AS contCuenta FROM $nombreTabla WHERE activo=1 AND id_empresa='$id_empresa' AND cuenta LIKE '$cuenta%'";
		$queryCuenta = mysql_query($sqlCuenta,$link);

		//echo $sqlCuenta;

		echo mysql_result($queryCuenta, 0, 'contCuenta');
	}

	function deleteCostosDocumento($id,$id_empresa,$link){

		$updateDelete  = "UPDATE costo_documento SET activo = 0 WHERE id_empresa='$id_empresa' AND id = '$id'";
		$queryDelete   = mysql_query($updateDelete,$link);

		$updateDelete2 = "UPDATE costo_documento_porcentaje
                               SET activo = 0
                          WHERE id_empresa='$id_empresa' AND id_costo_documento = '$id'";


		$queryDelete2  = mysql_query($updateDelete2,$link);

		if(!$queryDelete){ echo '<script>alert("Ha habido un error")</script>'; }
		if(!$queryDelete2){ echo '<script>alert("Ha habido un error")</script>'; }

		echo'<script>
		    		Elimina_Div_costosDocumentos('.$id.');
		    		Win_Ventana_Costos_Documento_update.close();
		    </script>';

	}


?>