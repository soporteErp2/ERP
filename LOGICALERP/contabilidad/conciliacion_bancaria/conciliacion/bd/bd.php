<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");
	include("../../config_var_global.php");

	//============= FUNCIONES PARA DOCUMENTO CONCILIACION BANCARIA =============//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	switch($opc){

		case 'buscarCuenta':
			buscarCuenta($cuenta,$id_documento,$id_empresa,$id_sucursal,$opcGrillaContable,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$mysql,$link);
			break;

		case 'terminarGenerar':
			terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$notaCruce,$link);
			break;

		case 'modificarDocumentoGenerado':
			modificarDocumentoGenerado($idDocumento,$opcGrilla,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link);
			break;

		case 'guardarCuenta':
			guardarCuenta($id_empresa,$consecutivo,$id,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$idPuc,$cuenta,$debe,$haber,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$link);
			break;

		case 'actualizaCuenta':
			actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$cuenta,$debe,$haber,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$link);
			break;

		case 'guardarObservacion':
			guardarObservacion($observacion,$id,$tablaPrincipal,$link);
			break;

		case 'cancelarDocumento':
			cancelarDocumento($id,$opcGrilla,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link);
			break;

		case 'restaurarDocumento':
			restaurarDocumento($idDocumento,$opcGrilla,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link);
			break;

		case 'ventana_buscar_documento_cruce':
			ventana_buscar_documento_cruce($cont,$opcGrilla,$carpeta,$id_empresa,$id_sucursal,$link);
			break;

		case 'validarCuenta':
			validarCuenta($cuenta,$id_empresa,$link);
			break;

		case 'load_conciliacion':
			load_conciliacion($opcGrillaContable,$id_documento,$fecha_inicio,$fecha_fin,$cuenta,$id_empresa,$id_sucursal,$link);
			break;

	}

	//======================= BUSCAR UNA CUENTA CONTABLE =======================//
	function buscarCuenta($cuenta,$id_documento,$id_empresa,$id_sucursal,$opcGrillaContable,$tablaPrincipal,$idTablaPrincipal,$tablaInventario,$mysql,$link){

		$sql   			 = "SELECT id,descripcion FROM puc WHERE activo = 1 AND id_empresa = $id_empresa AND cuenta = '$cuenta' ";
		$query 			 = $mysql->query($sql,$mysql->link);
		$id          = $mysql->result($query,0,'id');
		$descripcion = $mysql->result($query,0,'descripcion');

		if($id > 0){
			// ACTUALIZAR LA CUENTA EN LA CONCILIACION
			$sql = "UPDATE $tablaPrincipal SET id_cuenta = $id,cuenta = '$cuenta',descripcion_cuenta = '$descripcion' WHERE activo = 1 AND id_empresa = $id_empresa AND id = $id_documento";
			$mysql->query($sql,$mysql->link);
			echo '<script>
							document.getElementById("cuenta'.$opcGrillaContable.'").value             = "'.$cuenta.'";
							document.getElementById("descripcion_cuenta'.$opcGrillaContable.'").value = "'.$descripcion.'";
							cargaTabla'.$opcGrillaContable.'();
						</script>';
		}
		else{
			echo '<script>
							alert("Aviso\nLa cuenta digitada no existe.");
							document.getElementById("cuenta'.$opcGrillaContable.'").value             = "";
							document.getElementById("descripcion_cuenta'.$opcGrillaContable.'").value = "";
							document.getElementById("cuenta'.$opcGrillaContable.'").focus();
						</script>';
		}
	}

	//=========================== FUNCION PARA TERMINAR 'GENERAR' LA NOTA ==========================================================================//
	function terminarGenerar($id,$id_empresa,$id_sucursal,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$notaCruce,$link){

	 	//MOVEMOS LAS CUENTAS
		moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'agregar',$id_tercero,$link);

		//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
		moverDocumentosSaldos($id_empresa,$id,'eliminar',$link);

		$fecha = date("Y-m-d");
		$sql   = "UPDATE $tablaPrincipal SET estado=1,fecha_finalizacion='$fecha' WHERE id='$id' AND activo=1 AND id_empresa=$id_empresa";
		$query = mysql_query($sql,$link);
		if (!$query){
			echo '<script>alert("Error!\nNo se pudo actualizar la nota, vuelva a intentarlo\nSi el problema persite comuniquese con el administrador del sistema");</script>';
			moverDocumentosSaldos($id_empresa,$id,'agregar',$link);
			moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',$id_tercero,$link);
		}
		else{
			$sqlConsecutivo        = "SELECT consecutivo FROM $tablaPrincipal WHERE activo=1 AND id='$id' AND id_empresa='$id_empresa'";
			$queryConsecutivo      = mysql_query($sqlConsecutivo,$link);
			$consecutivo_documento = mysql_result($queryConsecutivo,0,'consecutivo');

			$sqlUpdate = "UPDATE asientos_colgaap
							SET consecutivo_documento='$consecutivo_documento',
								numero_documento_cruce='$consecutivo_documento'
						 	WHERE id_documento='$id'
						 		AND tipo_documento='NCG'
						 		AND id_empresa='$id_empresa'";
			$queryUpdate=mysql_query($sqlUpdate,$link);

			$sqlUpdate = "UPDATE asientos_niif
							SET consecutivo_documento='$consecutivo_documento',
								numero_documento_cruce='$consecutivo_documento'
						 	WHERE id_documento='$id'
						 		AND tipo_documento='NCG'
						 		AND id_empresa='$id_empresa'";
			$queryUpdate=mysql_query($sqlUpdate,$link);
		}

		$fecha_actual = date('Y-m-d');
		$hora_actual  = date('H:i:s');

		//INSERTAR EL LOG DE EVENTOS
		$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
					     VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Generar','NCG','Nota Contable General',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
		$queryLog = mysql_query($sqlLog,$link);

	   	echo'<script>
   				Ext.get("contenedor_NotaGeneral").load({
 		            url     : "nota_general/bd/grillaContableBloqueada.php",
 		            scripts : true,
 		            nocache : true,
 		            params  :
 		            {
						id_nota           : '.$id.',
						opcGrilla : "NotaGeneral",
 		            }
 		        });
			</script>';
	}

	//=============================================== FUNCION EDITAR UNA FACTURA - REMISION YA GENERADA ===============================================================//
	function modificarDocumentoGenerado($idDocumento,$opcGrilla,$id_empresa,$id_sucursal,$id_bodega,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$link){

		$sql   = "SELECT consecutivo FROM $tablaPrincipal WHERE id='$idDocumento' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$consecutivo=mysql_result($query,0 ,'consecutivo');

		//VALIDAMOS QUE NO TENGA ARTICULOS RELACIONADOS, SE DEBE REVERSAR EL MOVIMIENTO QUE HICIERON LOS ARTICULOS
		$sqlValidaArticulos   = "SELECT COUNT(id) AS cont FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal'";
		$queryValidaArticulos = mysql_query($sqlValidaArticulos,$link);
		$cont = mysql_result($queryValidaArticulos,0,'cont');

		if ($cont>0) {

			//SI TIENE ARTICULOS SE DEBE REVERSAR EL MOVIMIENTO DEL MISMO, LLAMAMOS LA FUNCION PARA REALIZAR
			$sqlArticulos   = "SELECT id,tipo FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa'";
			$queryArticulos = mysql_query($sqlArticulos,$link);

			$resul=0;

			//RECORREMOS TODOS LOS ARTICULOS Y LLAMAMOS LA FUNCION PARA REVERSAR EL PROCESO DE CADA UNO
			while ($rowArticulos=mysql_fetch_array($queryArticulos)) { $resul=eliminarArticuloRelacionado($rowArticulos['id'],$rowArticulos['tipo'],'return',$link); }

			if ($resul>0) { echo '<script>alert("Erro!\nNo se actualizo el inventario\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }
		}

		//PARA MODIFICAR EL DOCUMENTO PRIMERO DEBEMOS DESCONTABILIZARLO Y LUEGO REGRESARLO A ESTADO=0
		moverCuentasDocumento($idDocumento,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);
		//YA QUE SE DIERON DE BAJA LOS ASIENTOS GENERADOS POR LA NOTA, PROCEDEMOS A ACTUALIZAR SU ESTADO A CERO
		$sql   = "UPDATE $tablaPrincipal SET estado=0 WHERE id='$idDocumento' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa'";
		$query = mysql_query($sql,$link);

		//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
		moverDocumentosSaldos($id_empresa,$idDocumento,'agregar',$link);

		if($query){
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Editar','NCG','Nota Contable General',$id_sucursal,'$id_empresa','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
				 	Ext.get("contenedor_'.$opcGrilla.'").load({
						url     : "nota_general/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_nota           : "'.$idDocumento.'",
							opcGrilla : "'.$opcGrilla.'"
						}
					});

					Ext.getCmp("Btn_exportar_NotaGeneral").disable();
				</script>';
		}
		else{ echo '<script>alert("Error!\nNo se insertaron los articulos nuevamente al inventario!");</script>'; }
	}

	//=========================== FUNCION PARA ACTUALIZAR UN ARTICULO YA AGREGADO EN LA GRILLA ===================================================//
	function actualizaCuenta($id_empresa,$id,$idInsertCuenta,$idPuc,$cuenta,$debe,$haber,$cont,$opcGrilla,$tablaCuentasNota,$idTablaPrincipal,$id_tercero,$terceroGeneral,$id_documento_cruce,$numeroDocumentoCruce,$prefijoDocumentoCruce,$tipoDocumentoCruce,$link){

		//VERIFICAR QUE EL DOCUMENTO (SI TIENE) EXISTA  Y SE ENCUENTRE DISPONIBLE Y CON EL SALDO QUE SE VA A INGRESAR LA CUENTA
		if ($tipoDocumentoCruce!='') { validaDocumentoCruce($id_empresa,$id_documento_cruce,$tipoDocumentoCruce,$prefijoDocumentoCruce,$numeroDocumentoCruce,$opcGrilla,$cont,$terceroGeneral,$id_tercero,$cuenta,'actualizar',$debe,$haber,$link); }

		// se agrega la funcionalidad para actualizar los costos de la factura, se elimina los costos del anterior articulo y se agregan los costos del nuevo
		//----- consultamos el articulo que estaba anteriormente, junto con todos sus datos para recalcular el monto de la factura
		$sqlArticuloAnterior   = "SELECT debe,haber FROM $tablaCuentasNota WHERE id='$idInsertCuenta' AND $idTablaPrincipal='$id' ";
		$queryArticuloAnterior = mysql_query($sqlArticuloAnterior,$link);

		$debeAnterior  = mysql_result($queryArticuloAnterior,0,'debe');
		$haberAnterior = mysql_result($queryArticuloAnterior,0,'haber');

		$numeroDocumentoCruce = ($numeroDocumentoCruce > 0)? $numeroDocumentoCruce: 'NULL';

		//llamamos la funcion para recalcular la factura y le enviamos los valores anteriores pára darlos de baja
		$sqlUpdateArticulo   = "UPDATE $tablaCuentasNota
								SET id_puc='$idPuc',
								debe='$debe',
								haber='$haber',
								id_tercero='$id_tercero',
								id_documento_cruce='$id_documento_cruce',
								tipo_documento_cruce = '$tipoDocumentoCruce',
								prefijo_documento_cruce = '$prefijoDocumentoCruce',
								numero_documento_cruce = $numeroDocumentoCruce
								WHERE $idTablaPrincipal=$id AND id=$idInsertCuenta";
		$queryUpdateArticulo = mysql_query($sqlUpdateArticulo,$link);


		if ($queryUpdateArticulo) {

			$debe  = ($debe=='')? 0: $debe;
			$haber = ($haber=='')? 0: $haber;

			echo'<script>
					document.getElementById("divImageSave'.$opcGrilla.'_'.$cont.'").style.display     = "none";
					document.getElementById("divImageDeshacer'.$opcGrilla.'_'.$cont.'").style.display = "none";

					//llamamos la funcion para recalcular el costo de la nota
					calcTotal'.$opcGrilla.'("'.$debeAnterior.'","'.$haberAnterior.'","eliminar");
					calcTotal'.$opcGrilla.'("'.$debe.'","'.$haber.'","agregar");
				</script>';
		}
		else{ echo '<script> alert("Error, no se actualizo la cuenta"); </script>'; }
	}

	//=========================== FUNCION PARA GUARDAR LA OBSERVACION DE LA NOTA =====================================================//
	function guardarObservacion($observacion,$id,$tablaPrincipal,$link){
		$observacion      = str_replace("[\n|\r|\n\r]", '<br>', $observacion);
		$sqlObservacion   = "UPDATE $tablaPrincipal SET  observacion='$observacion' WHERE id='$id' AND id_empresa='$_SESSION[EMPRESA]'";
		$queryObservacion = mysql_query($sqlObservacion,$link);
		if($queryObservacion){ echo 'true'; }
		else{ echo'false'; }
	}

	//============================ FUNCION PARA CANCELAR UN PEDIDO - COTIZACION ====================================================================//
	function cancelarDocumento($id,$opcGrilla,$tablaPrincipal,$tablaCuentasNota,$idTablaPrincipal,$id_sucursal,$id_empresa,$link){
		//CONSULTAMOS EL DOCUMENTO PARA SABER SI ESTA GENERADO
		$sql   = "SELECT estado,consecutivo FROM $tablaPrincipal WHERE id='$id' AND id_empresa='$id_empresa' LIMIT 0,1 ";
		$query = mysql_query($sql,$link);

		$estado      = mysql_result($query,0 ,'estado');
		$consecutivo = mysql_result($query,0 ,'consecutivo');

		if($estado=='3'){ echo '<script>alert("Error!\nEsta nota ya esta cancelada!");</script>'; return;}
		else if ($estado=='0' && $consecutivo=='') { $sqlUpdate="UPDATE $tablaPrincipal SET activo=0 WHERE id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";	}
		else if($estado=='0' && $consecutivo!=''){$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";	}
		else if ($estado=='1') {
			//VALIDAMOS QUE NO TENGA ARTICULOS RELACIONADOS, SE DEBE REVERSAR EL MOVIMIENTO QUE HICIERON LOS ARTICULOS
			$sqlValidaArticulos   ="SELECT COUNT(id) AS cont FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal'";
			$queryValidaArticulos =mysql_query($sqlValidaArticulos,$link);
			$cont=mysql_result($queryValidaArticulos,0,'cont');

			if ($cont>0) {

				//SI TIENE ARTICULOS SE DEBE REVERSAR EL MOVIMIENTO DEL MISMO, LLAMAMOS LA FUNCION PARA REALIZAR
				$sqlArticulos   = "SELECT id,tipo FROM inventario_movimiento_notas WHERE activo=1 AND consecutivo_nota='$consecutivo' AND id_empresa='$id_empresa'";
				$queryArticulos = mysql_query($sqlArticulos,$link);

				$resul=0;

				//RECORREMOS TODOS LOS ARTICULOS Y LLAMAMOS LA FUNCION PARA REVERSAR EL PROCESO DE CADA UNO
				while ($rowArticulos=mysql_fetch_array($queryArticulos)) {
					$resul=eliminarArticuloRelacionado($rowArticulos['id'],$rowArticulos['tipo'],'return',$link);
				}

				if ($resul>0) { echo '<script>alert("Erro!\nNo se actualizo el inventario\nSi el problema persiste comuniquese con el administrador del sistema");</script>'; exit; }
			}

			//DESCONTABILIZAMOS LA NOTA, ELIMINADO LOS ASIENTOS QUE SE GENERARON A PARTIR DE ELLA
			//MOVER LOS SALDOS DE LOS DOCUMENTOS RELACIONADOS
			moverDocumentosSaldos($id_empresa,$id,'agregar',$link);

			moverCuentasDocumento($id,$id_empresa,$id_sucursal,$tablaCuentasNota,$idTablaPrincipal,'eliminar',0,$link);
			//CADENA PARA ACTUALIZAR LA NOTA
			$sqlUpdate="UPDATE $tablaPrincipal SET estado=3 WHERE id='$id' AND activo=1 AND id_sucursal='$id_sucursal' AND id_empresa='$id_empresa' ";

		}

		//EJECUTAMOS EL QUERY PARA ACTUALIZAR EL DOCUMENTO CON SU ESTADO COMO CANCELADO
		$queryUpdate = mysql_query($sqlUpdate,$link);


		if (!$queryUpdate) {
			echo '<script>alert("Error!\nSe proceso el documento pero no se cancelo\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
			return;
		}
		else{
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
								 VALUES($id,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Cancelar','NCG','Nota Contable General',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo '<script>nueva'.$opcGrilla.'();</script>';
		}

	}

 	//============================ FUNCION PARA RESTAURAR UN DOCUMENTO CANCELADO ====================================================================//
 	function restaurarDocumento($idDocumento,$opcGrilla,$carpeta,$id_sucursal,$idBodega,$id_empresa,$tablaPrincipal,$link){

		$sqlUpdate   = "UPDATE $tablaPrincipal SET estado=0 WHERE activo=1 AND id='$idDocumento' AND id_sucursal='$id_sucursal'  AND id_empresa='$id_empresa' ";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		//VALIDAR QUE SE ACTUALIZO EL DOCUMENTO, Y CONTINUAR A MOSTRARLO
		if ($queryUpdate) {
			$fecha_actual = date('Y-m-d');
			$hora_actual  = date('H:i:s');

			//INSERTAR EL LOG DE EVENTOS
			$sqlLog = "INSERT INTO log_documentos_contables(id_documento,id_usuario,usuario,actividad,tipo_documento,descripcion,id_sucursal,id_empresa,ip,fecha,hora)
						     VALUES($idDocumento,'".$_SESSION['IDUSUARIO']."','".$_SESSION['NOMBREUSUARIO']."','Restaurar','NCG','Nota Contable General',$id_sucursal,'".$_SESSION['EMPRESA']."','".$_SERVER['REMOTE_ADDR']."','$fecha_actual','$hora_actual')";
			$queryLog = mysql_query($sqlLog,$link);

			echo'<script>
					Ext.get("contenedor_'.$opcGrilla.'").load({
						url     : "nota_general/grilla/grillaContable.php",
						scripts : true,
						nocache : true,
						params  :
						{
							id_nota           : "'.$idDocumento.'",
							opcGrilla : "'.$opcGrilla.'"
						}
					});
				</script>';
		}
		else{
			echo '<script>alert("Error!\nNo se pudo restaurar el documento\nSi el problema persiste comuniquese con el administrador del sistema");</script>';
			return;
		}

 	}

	//=========================== CARGAR CONCILIACION ==========================//
	function load_conciliacion($opcGrillaContable,$id_documento,$fecha_inicio,$fecha_fin,$cuenta,$id_empresa,$id_sucursal,$link){
		$sqlCuenta = "SELECT
										fecha,
										tipo_documento_cruce,
										consecutivo_documento,
										nit_tercero,
										tercero,
										debe,
										haber
									FROM
										asientos_colgaap
									WHERE
										id_empresa = $id_empresa
									AND
										activo = 1
									AND
										id_sucursal = $id_sucursal
									AND
										codigo_cuenta = '$cuenta'
									AND
										fecha
									BETWEEN
										'$fecha_inicio' AND '$fecha_fin'
									ORDER BY
										fecha ASC";
		$queryCuenta = mysql_query($sqlCuenta,$link);
		while($row = mysql_fetch_array($queryCuenta)){
			$bodyDetalleCuenta .=  "<div class='divTableRow'>
																<div class='divTableCell' style='text-align:center; max-width:70px;'>$row[fecha]</div>
																<div class='divTableCell' style='text-align:center; max-width:90px;'>$row[tipo_documento_cruce]</div>
																<div class='divTableCell' style='text-align:center; max-width:90px;'>$row[consecutivo_documento]</div>
																<div class='divTableCell' style='text-align:center; max-width:70px;'>$row[nit_tercero]</div>
																<div class='divTableCell' style='text-align:left; max-width:200px; white-space:nowrap; text-overflow:ellipsis; overflow:hidden;'>$row[tercero]</div>
																<div class='divTableCell' style='text-align:right; max-width:70px;'>".round($row['debe'],$_SESSION['DECIMALESMONEDA'])."</div>
																<div class='divTableCell' style='text-align:right; max-width:70px;'>".round($row['haber'],$_SESSION['DECIMALESMONEDA'])."</div>
															</div>";
		};
		$tableCuenta = "<div class='divTable' width='60%'>
											<div class='titleTable'>CUENTAS BANCARIAS</div>
											<div class='divTableHeading'>
												<div class='divTableRow'>
													<div class='divTableHead' style='max-width:70px;'>Fecha</div>
													<div class='divTableHead' style='max-width:90px;'>Tipo De Documento</div>
													<div class='divTableHead' style='max-width:90px;'>Consecutivo</div>
													<div class='divTableHead' style='max-width:70px;'>Numero Documento</div>
													<div class='divTableHead' style='max-width:200px;'>Tercero</div>
													<div class='divTableHead' style='max-width:70px;'>Debito</div>
													<div class='divTableHead' style='max-width:70px;'>Credito</div>
												</div>
											</div>
											<div class='divTableBody'>
												$bodyDetalleCuenta
											</div>
										</div>";

		$sqlExtracto = "SELECT
					           	tipo,
											numero_documento,
											fecha,
											valor
						        FROM
											extractos_detalle
						        WHERE
										  id_empresa = $id_empresa
										AND
										  activo = 1
										AND
											fecha
										BETWEEN
											'$fecha_inicio' AND '$fecha_fin'
										ORDER BY
											fecha ASC";
    $queryExtracto = mysql_query($sqlExtracto,$link);
		while($row = mysql_fetch_array($queryExtracto)){
			$bodyDetalleExtracto .=  "<div class='divTableRow'>
																	<div class='divTableCell' style='text-align: center; max-width70px;'>$row[fecha]</div>
																	<div class='divTableCell' style='text-align: center; max-width70px;'>$row[tipo]</div>
																	<div class='divTableCell' style='text-align: center; max-width70px;'>$row[numero_documento]</div>
																	<div class='divTableCell' style='text-align: right;  max-width70px;'>".round($row['valor'],$_SESSION['DECIMALESMONEDA'])."</div>
																</div>";
		}
		$tableExtracto = "<div class='divTable' width='40%'>
												<div class='titleTable'>EXTRACTOS</div>
											  <div class='divTableHeading'>
											    <div class='divTableRow'>
														<div class='divTableHead' style='max-width:70px'>Fecha</div>
														<div class='divTableHead' style='max-width:70px'>Tipo De Documento</div>
														<div class='divTableHead' style='max-width:70px'>Numero De Documento</div>
														<div class='divTableHead' style='max-width:70px'>Valor</div>
											    </div>
											  </div>
											  <div class='divTableBody'>
													$bodyDetalleExtracto
											  </div>
											</div>";

	  $body =  $tableCuenta.$tableExtracto;

		$body = str_replace(array("\r","\n","\t"),array("","",""),$body);

		echo '<script>
						document.getElementById("renderFilasConciliacion").innerHTML = "'.$body.'";
					</script>';
	}
?>
