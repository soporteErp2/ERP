<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];
	switch ($op) {

		case "OptionSubgrupo":
			OptionSubgrupo($id_grupo,$link,$opcion,$id_inventario);
			break;

		case "OptionChecklist":
			OptionChecklist($link);
			break;

		case "ventana_eliminar_campo_inventario":
			VentanaEliminarCampoInventario($link,$id);
			break;

		case "eliminar_campo_inventario":
			EliminarCampoInventario($link,$id,$observaciones,$id_usuario,$nombre_usuario);
			break;

		case "nuevo_inventatrio_proceso":
			nuevo_inventatrio_proceso($link,$id_usuario,$nombre_usuario,$empresa_proceso,$sucursal_proceso,$ubicacion_proceso);
			break;

		case "filtro_inventario_parcial":
			filtro_inventario_parcial();
			break;

		case "eliminar_proceso_inventario":
			eliminar_proceso_inventario($link,$id);
			break;

		case "eliminar_documento":
			eliminar_documento($link,$id);
			break;

		case "ventanaVerImagenDocumentoInventario":
			ventanaVerImagenDocumentoInventario($nombreImage,$type);
			break;

		case "consultaSizeImageDocumentInventario":
				consultaSizeImageDocumentInventario($nombre);
			break;

		case 'verificaArticuloBodegas':
			  verificaArticuloBodegas($idArticulo,$link);
			break;

		case 'sincronizaCuentaNiif':
			  sincronizaCuentaNiif($estado,$cuenta,$idInput,$id_empresa,$link);
			break;

		case 'OptionCentroCostos':
			OptionCentroCostos($idItem, $id_empresa, $link);
			break;
		case 'msjErrorUpload':
			msjErrorUpload($idError,$id_empresa,$prospecto,$link);
			break;
	}

	function OptionSubgrupo($id_grupo,$link,$opcion,$id_inventario){
		if($id_grupo>=1){
			$SQL        = "SELECT id,nombre_subgrupo FROM inventario_grupo_subgrupo WHERE id_grupo='$id_grupo' AND activo=1";
			$query      = mysql_query($SQL,$link);

			$SQLDB      = "SELECT id_grupo FROM items WHERE id='$id_inventario' AND activo=1";
			$id_grupoDB = mysql_result(mysql_query($SQLDB,$link),0,'id_grupo');

			echo'<select class="myfieldObligatorio" name="ActivosFijos_id_subgrupo" id="ActivosFijos_id_subgrupo" style="width:240px" onchange="ValidarFieldVacio(this)">
					<option value="">Seleccione...</option>';

			while($row = mysql_fetch_array($query)){
				if($id_grupoDB == $row['id']){ $selected = 'selected'; }
				else{ $selected  =''; }

				echo'<option value="'.$row['id'].'" '.$selected.'>'.$row['nombre_subgrupo'].'</option>';
			}
			echo '</select>';

			/*-----------------------------------Carga valor por default en el campo Vida Util--------------------------------------*/
			$SQLI  = "SELECT vida_util FROM inventario_grupo WHERE id=$id_grupo LIMIT 0,1";
			$query = mysql_query($SQLI,$link);
			while($rowb = mysql_fetch_array($query)){
				echo '<script>document.getElementById("ActivosFijos_vida_util").value='.$rowb['vida_util'].';	</script>';
			}
		}
		else{
			echo'<select class="myfieldObligatorio" name="ActivosFijos_id_subgrupo" id="ActivosFijos_id_subgrupo" style="width:240px" onchange="ValidarFieldVacio(this)">
					<option value="">Seleccione...</option>
				</select>';
		}

	}


	function OptionChecklist($link){
		echo '<select class="myfield" name="Inventario_id_checklist" id="Inventario_id_checklist" style="width:240px" >';
		$SQL    = "SELECT id,nombre FROM configuracion_mantenimiento_checklist WHERE activo=1";
		$consul = mysql_query($SQL,$link);
		while($rowc = mysql_fetch_array($consul)){
			echo '<option value="'.$rowc['id'].'">'.$rowc['nombre'].'</option>';
		}
		echo '</select>';
	}


	function VentanaEliminarCampoInventario($link,$id){
		echo'<div style="width:100%; margin:10px;">
				<div style="width:100%; margin:10px;">Observaciones:</div>
				<textarea style="width:340px; margin:10px;" rows="8" id="observaciones_eliminar_inventario"></textarea>
				<div id="div_guadar_eliminacion"></div>
			</div>';
	}

	function EliminarCampoInventario($link,$id,$observaciones,$id_usuario,$nombre_usuario){
		$sql       = "UPDATE inventarios SET  activo=0, observaciones_eliminacion='".$observaciones."', id_usuario_elimino=$id_usuario, usuario_elimino='".$nombre_usuario."' WHERE id=$id";
		$connectid = mysql_query($sql,$link);
		if (!$connectid){die('no Actualizo'.mysql_error());}

		echo "	<script>
					Win_Ventana_EliminarCampoInventario.close(id)
					Win_Editar_Inventario.close();
					Elimina_Div_Inventario(".$id.");
					//Ext.getCmp('Btn_Inventario_prestamo').disable();
				</script>";

	}


	function nuevo_inventatrio_proceso($link,$id_usuario,$nombre_usuario,$empresa_proceso,$sucursal_proceso,$ubicacion_proceso){


		//------------------------------------------------------Insert Nuevo Inventario Proceso----------------------------------------------------------------//
		$SQL_Insert_Proceso = "INSERT INTO inventario_proceso
				(fecha,hora,id_usuario,usuario,id_empresa,id_sucursal,id_bodega)
				VALUES
				(now(),now(),'$id_usuario','$nombre_usuario','$empresa_proceso','$sucursal_proceso','$ubicacion_proceso')";
		$connectids =mysql_query($SQL_Insert_Proceso,$link);
		//---------------------------------------------------Se Optiene El Id Del Ultimo Insert-----------------------------------------------------------//
		$id_InventarioProceso = mysql_insert_id();

		if (!$connectids){die('Error al insertar nuevo funcionario'.mysql_error());}


		$SQL_Inventarios     = "SELECT * from items WHERE activo=1 AND estado_equipo<>3 AND (id_ubicacion = $ubicacion_proceso AND prestado='false' OR id_bodega_prestamo = $ubicacion_proceso AND prestado = 'true')";
		$consulta_inventario = mysql_query($SQL_Inventarios,$link);
		while($row = mysql_fetch_array($consulta_inventario)){

				$SQL_Insert_Proceso_item = "INSERT INTO inventario_proceso_items (
															id_inventario_proceso,
															id_equipo,
															codigo,
															equipo,
															prestado,
															es_prestado
														)
											VALUES 	(
														'$id_InventarioProceso',
														'".$row['id']."',
														'".$row['codigo']."',
														'".$row['nombre_equipo']."',
														'".$row['prestado']."',
														'".$row['es_prestado']."'
													)";

				$connectids =mysql_query($SQL_Insert_Proceso_item,$link);
		}


		echo '	<script>
					alert("Se ha generado un nuevo proceso de inventario.");
					Inserta_Div_InventarioProceso('.$id_InventarioProceso.');
				</script>';
	}

	function filtro_inventario_parcial(){
		echo '	<div style="width:100%; margin:10px; overflow:hidden;">
					<div style="float:left; width:30%;">Fecha Inicial</div>
					<div style="float:left; width:65%;"><input type="text" id="fecha_ini"/></div>
				</div>
				<div style="width:100%; margin:10px; overflow:hidden;">
					<div style="float:left; width:30%;">Fecha Final</div>
					<div style="float:left; width:65%;"><input type="text" id="fecha_fin"/></div>
				</div>
				<script>
					new Ext.form.DateField(
						{
							format 		:	"Y-m-d",
							width		:	150,
							allowBlank	:	false,
							showToday	:	false,
							applyTo		:	"fecha_ini",
							editable	:	false
						}
					);

					new Ext.form.DateField(
						{
							format 		:	"Y-m-d",
							width		:	150,
							allowBlank	:	false,
							showToday	:	false,
							applyTo		:	"fecha_fin",
							editable	:	false
						}
					);

				</script>';
	}

	function eliminar_proceso_inventario($link,$id){
		$sql       = "UPDATE inventario_proceso SET activo=0 WHERE id=$id";
		$connectid = mysql_query($sql,$link);
		if (!$connectid){die('no Actualizo'.mysql_error());}

		echo "eliminado";
	}

	function eliminar_documento($link,$id){
		$sql       = "UPDATE inventario_documentos SET activo=0 WHERE id=$id";
		$connectid = mysql_query($sql,$link);
		if (!$connectid){die('No se elimino'.mysql_error());}

	}

	function ventanaVerImagenDocumentoInventario($nombreImage,$type){

		if($type!='pdf'){
			echo'<div style="margin:10px auto 10px auto; width:95%; height:80%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center; overflow:autoscroll; height:90%;">
						<img src="../../../ARCHIVOS_PROPIOS/documentos_inventario/empresa_'.$_SESSION['EMPRESA'].'/'.$nombreImage.'" style="">
					</div>
				</div>';
		}
		else{
			echo'<div style="margin:10px auto 10px auto; width:95%; height:90%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center;">
						<iframe src="../../../ARCHIVOS_PROPIOS/documentos_inventario/empresa_'.$_SESSION['EMPRESA'].'/'.$nombreImage.'" id="iframeViewDocumentInventario"></iframe>
					</div>
				</div>
				<script>
					cambiaViewPdf();
					function cambiaViewPdf(){
						var iframe=document.getElementById("iframeViewDocumentInventario");
						iframe.setAttribute("width",Ext.getBody().getWidth()-110);
						iframe.setAttribute("height",Ext.getBody().getHeight()-150);
					}
				</script>';
		}
	}

	function consultaSizeImageDocumentInventario($nombre){
		list($ancho, $alto, $tipo, $atributos) = getimagesize($nombre);
		$size['ancho'] = $ancho;
		$size['alto']  = $alto;

		echo json_encode($size);
	}

	//===================================== VERIFICAR QUE EL ARTICULO EXISTA EN LAS BODEGAS =====================================//
	function verificaArticuloBodegas($idArticulo,$link){
		$empresa = $_SESSION['EMPRESA'];
		$sql     = "SELECT  COUNT(id_item) AS cont FROM inventario_totales WHERE id_item='$idArticulo' AND id_empresa=".$_SESSION['EMPRESA'];

		$query   = mysql_query($sql,$link);
		$cont    = mysql_result($query,0,'cont');

		//si cont es menor a 1 es decir = a 0, entonces se genera la insercion del articulo en las bodegas
		if ($cont<1) {

			$valueInsert = '';
			$sqlSucursalBodega   = "SELECT DISTINCT id, id_sucursal
									FROM empresas_sucursales_bodegas
									WHERE id_empresa='$empresa'";
			$querySucursalBodega = mysql_query($sqlSucursalBodega,$link);

			while ($row = mysql_fetch_array($querySucursalBodega)) {
				$valueInsert .= ($valueInsert!='')? ",":"";
				$valueInsert .= "('$idArticulo','".$row['id_sucursal']."','".$row['id']."')";
			}

			$sqlInsertArticulo   = "INSERT INTO inventario_totales (id_item,id_sucursal,id_ubicacion) VALUES $valueInsert";
			$queryInsertArticulo = mysql_query($sqlInsertArticulo,$link);

			echo 'true';
		}
		else{ echo 'false'; }
	}

	function sincronizaCuentaNiif($estado,$cuenta,$idInput,$id_empresa,$link){
		$sqlNiif   = "SELECT COUNT(id) AS cont_niif,id,cuenta AS cuenta_niif, descripcion FROM puc_niif WHERE activo=1 AND cuenta_colgaap='$cuenta' AND id_empresa='$id_empresa' LIMIT 0,1";
		$queryNiif = mysql_query($sqlNiif,$link);

		$idNiif          = mysql_result($queryNiif,0,'id');
		$contNiif        = mysql_result($queryNiif,0,'cont_niif');
		$cuentaNiif      = mysql_result($queryNiif,0,'cuenta_niif');
		$descripcionNiif = mysql_result($queryNiif,0,'descripcion');

		if($contNiif == 0){ echo'<script>alert("No existe una cuenta niif asociada a la cuenta colgaap No. '.$cuenta.'");</script>'; }
		else{
			$inputIdNiif = str_replace("ActivosFijos", "ActivosFijos_id", $idInput);

			echo'<script>
					document.getElementById("'.$inputIdNiif.'_niif").value = "'.$idNiif.'";
					document.getElementById("'.$idInput.'_niif").value     = "'.$cuentaNiif.'";
				</script>';
		}

		echo'<img src="images/refresh.png" onclick="sincronizaCuentaEnNiif(\''.$estado.'\',\''.$idInput.'\')"/>';
	}

	function OptionCentroCostos($idItem, $id_empresa, $link){
		$SQLDB              = "SELECT id_centro_costos,centro_costos FROM activos_fijos WHERE id='$idItem' AND activo=1 AND id_empresa='$id_empresa'";
		$id_centro_costosDB = mysql_result(mysql_query($SQLDB,$link),0,'id_centro_costos');
		$centro_costosDB    = mysql_result(mysql_query($SQLDB,$link),0,'centro_costos');

		$SQL   = "SELECT id,codigo,nombre FROM centro_costos WHERE activo=1 AND id_empresa='$id_empresa' AND  id=$id_centro_costosDB";
		$query = mysql_query($SQL,$link);
		$codigo_centro_costosDB    = mysql_result(mysql_query($SQL,$link),0,'codigo');


		if ($id_centro_costosDB!="" && $centro_costosDB!="") {
			echo '	<script>
						document.getElementById("ActivosFijos_id_centro_costos").value="'.$id_centro_costosDB.'";
					</script>
					<input class="myfield" name="ActivosFijos_centro_costos" type="text" id="ActivosFijos_centro_costos" value="'.$codigo_centro_costosDB.' - '.$centro_costosDB.'" style="width:240px;float:left;" onkeyup="" readonly="" onclick="ventanaBuscarCentroCostos()">
					<div id="imgEliminarCcos" style="width:19px;height:16px;background-color: #FFF;cursor:pointer;background-image: url(\'images/false.png\');background-repeat: no-repeat;float:left;margin-left: -20;margin-top: 2;border-left: 1px solid #BDB4B4;"  title="Eliminar Centro Costos" onclick="eliminaCcosItem()"></div>';
		}else{
			echo '	<script>
						document.getElementById("ActivosFijos_id_centro_costos").value="";
					</script>
					<input class="myfield" name="ActivosFijos_centro_costos" type="text" id="ActivosFijos_centro_costos" value="" style="width:240px;float:left;" onkeyup="" readonly="" onclick="ventanaBuscarCentroCostos()">
					<div id="imgEliminarCcos" style="width:19px;height:16px;background-color: #FFF;cursor:pointer;background-image: url(\'images/buscar20.png\');background-repeat: no-repeat;float:left;margin-left: -20;margin-top: 2;border-left: 1px solid #BDB4B4;"  title="Buscar Centro Costos" onclick="ventanaBuscarCentroCostos()"></div>';
		}

	}

	function msjErrorUpload($idError,$id_empresa,$prospecto,$link){

		$sqlError   = "SELECT mensaje_error FROM activos_fijos_upload_registro WHERE id='$idError' AND id_empresa='$id_empresa'";
		$queryError = mysql_query($sqlError,$link);
		$msjError   = mysql_result($queryError, 0, 'mensaje_error');

		echo'<div style="background-color:#FFF; width:100%; height:100%; padding:2%;overflow-y:auto;">'.$msjError.'</div>';
	}
?>