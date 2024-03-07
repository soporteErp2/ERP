<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");

	$id_host        = $_SESSION['ID_HOST'];
	$id_empresa     = $_SESSION['EMPRESA'];
	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];

	if(!isset($clase)){ $clase = 'Terceros'; }

	switch ($op) {

		case "OptionSelectDepartamento":
			OptionSelectDepartamento($id_pais,$id_cliente,$clase,$link);
			break;

		case "OptionSelectCiudad":
			OptionSelectCiudad($id_departamento,$id_cliente,$clase,$link);
			break;

		case "OptionSelectComuna":
			OptionSelectComuna($id_ciudad,$id_cliente,$clase,$mysql);
			break;

		case "OptionSelectDepartamentoDireccion":
			OptionSelectDepartamentoDireccion($id_departamento,$id_cliente,$clase,$link);
			break;

		case "OptionSelectComunaDireccion":
			OptionSelectComunaDireccion($id_ciudad,$id_cliente,$clase,$mysql);
			break;

		case "OptionSelectActividad":
			OptionSelectActividad($id_proyecto,$id_cliente,$link);
			break;

		case "OptionSelectTerceroDocumentos":
			OptionSelectTerceroDocumentos($id_empresa,$link);
			break;

		case "buscarDatosSucursalPrincipal":
			buscarDatosSucursalPrincipal($id_cliente,$typeDireccion,$link);
			break;

		case "cargaTextoDocumentoMail":
			cargaTextoDocumentoMail($id_mail,$link);
			break;

		case "selectActividades":
			selectActividades($id_proyecto,$id_maestro,$type,$link);
			break;

		case "selectProyectos":
			selectProyectos($id_cliente,$link);
			break;

		case "ventanaVerImagenDocumentoTerceros":
			ventanaVerImagenDocumentoTerceros($nombreImage,$nombreDocumento,$type,$id_host);
			break;

		case "consultaSizeImageDocumentTerceros":
			consultaSizeImageDocumentTerceros($id_host,$nombre);
			break;

		case 'eliminar_archivo':
			eliminar_archivo($id_host,$idArchivo,$fileName,$link);
			break;

		case 'guardar_retencion':
			guardar_retencion($id_empresa,$idRetencion,$idTercero,$id_empresa,$link);
			break;

		case 'cargarRetencionesItem':
			cargarRetencionesItem($id_empresa,$idItem,$link);
			break;

		case 'eliminarRetencionItem':
			eliminarRetencionItem($idItem,$idRetencion,$link);
			break;

		case 'msjErrorUpload':
			msjErrorUpload($idError,$id_empresa,$prospecto,$link);
			break;

		case "cargaBuscadorAsignadosTerceros":
			cargaBuscadorAsignadosTerceros($grillaName,$id_asignado,$nombre_asignado,$link);
			break;

		case "ventana_eliminar_prospecto":
			VentanaEliminarProspecto($link,$id);
			break;

		case "eliminar_campo_prospecto":
			eliminar_campo_prospecto($link,$id,$observaciones);
			break;
	}

	function OptionSelectDepartamento($id_pais,$id_cliente,$clase,$link){

		echo'<select class="myfieldObligatorio" name="'.$clase.'_id_departamento" id="'.$clase.'_id_departamento" style="width:200px" onchange="ValidarFieldVacio(this); ActualizaCiudad'.$clase.'(); ">
				<option value="">Seleccione...</option>';

		if($id_pais>=1){
			$SQL               = "SELECT id_departamento FROM terceros WHERE id='$id_cliente' AND activo=1";
			$consul            = mysql_query($SQL,$link);
			$id_departamentoDB = mysql_result($consul, 0, 'id_departamento');

			$SQL    = "SELECT id,departamento FROM ubicacion_departamento WHERE id_pais= $id_pais AND activo=1  ORDER BY departamento ASC";
			$consul = mysql_query($SQL,$link);
			while($row = mysql_fetch_array($consul)){
				$selected = ($id_departamentoDB == $row['id'])? 'selected': '';
				echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['departamento'].'</option>';
			}
		}
		echo'</select>
			<script>
				id_departamento = document.getElementById("'.$clase.'_id_departamento").value;
				ActualizaCiudad'.$clase.'(id_departamento);
				document.getElementById("'.$clase.'_id_departamento").className="myfield";
			</script>';
	}

	function OptionSelectCiudad($id_departamento,$id_cliente,$clase,$link){

		echo'<select class="myfieldObligatorio" name="'.$clase.'_id_ciudad" id="'.$clase.'_id_ciudad" style="width:200px" onchange="ValidarFieldVacio(this);ActualizaComunaTerceros(this.value)">
				<option value="">Seleccione...</option>';

		if($id_departamento>=1){
			$SQL         = "SELECT id_ciudad FROM terceros WHERE id='$id_cliente' AND activo=1";
			$consul      = mysql_query($SQL,$link);
			$id_ciudadDB = mysql_result($consul, 0, 'id_ciudad');

			$SQL    = "SELECT id,ciudad FROM ubicacion_ciudad WHERE id_departamento=$id_departamento AND activo=1 ORDER BY ciudad ASC";
			$consul = mysql_query($SQL,$link);
			while($row = mysql_fetch_array($consul)){
				$selected = ($id_ciudadDB==$row['id'])? 'selected': '';
				echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['ciudad'].'</option>';
			}
		}

		echo '<script>document.getElementById("'.$clase.'_id_ciudad").className="myfield";</script>';
	}

	function OptionSelectComuna($id_ciudad,$id_cliente,$clase,$mysql){

		echo'<select class="myfieldObligatorio" name="'.$clase.'_id_ciudad" id="'.$clase.'_id_comuna" style="width:200px" onchange="ValidarFieldVacio(this)">
				<option value="">Seleccione...</option>';

		if($id_ciudad>=1){
			$sql="SELECT id_comuna FROM terceros WHERE id='$id_cliente' AND activo=1";
			$query=$mysql->query($sql,$mysql->link);
			$id_ciudadDB = $mysql->result($query, 0, 'id_comuna');

			$sql="SELECT id,comuna FROM ubicacion_comuna WHERE id_ciudad=$id_ciudad AND activo=1 ORDER BY comuna ASC";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)) {
				$selected = ($id_ciudadDB==$row['id'])? 'selected': '';
				echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['comuna'].'</option>';
			}

		}

		echo '<script>document.getElementById("'.$clase.'_id_ciudad").className="myfield";</script>';
	}

	function OptionSelectDepartamentoDireccion($id_departamento,$id_cliente,$clase,$link){
		$id_ciudadDB="";

		if($id_departamento>=1){
			$SQL1   = "SELECT id_ciudad FROM terceros_direcciones WHERE id=".$id_cliente." AND activo=1";
			$consul = mysql_query($SQL1,$link);

			while($row = mysql_fetch_array($consul)){ $id_ciudadDB=$row['id_ciudad']; }

			echo '<select class="myfieldObligatorio" name="'.$clase.'Direcciones_id_ciudad" id="'.$clase.'Direcciones_id_ciudad" style="width:150px" onchange="ValidarFieldVacio(this);ActualizaComunaDirecciones(this.value);">';
			echo '	<option value="">Seleccione...</option>';
				$SQL = "SELECT id,ciudad FROM ubicacion_ciudad WHERE id_departamento= $id_departamento AND activo=1";
				$consul  =mysql_query($SQL,$link);
				while($row = mysql_fetch_array($consul)){
					$selected="";
					if($id_ciudadDB==$row['id']){$selected="selected";}
					echo "<option value='".$row['id']."' ".$selected.">";
					echo $row['ciudad'];
					echo "</option>";
				}
			echo '</select>';
		}
		else{
			echo'<select class="myfieldObligatorio" name="TercerosDirecciones_id_ciudad" id="TercerosDirecciones_id_ciudad" style="width:150px" onchange="ValidarFieldVacio(this)">
					<option value="">Seleccione...</option>
				</select>';
		}

	}

	function OptionSelectComunaDireccion($id_ciudad,$id_cliente,$clase,$mysql){

		echo'<select class="myfieldObligatorio" name="'.$clase.'_id_comuna" id="'.$clase.'_id_comuna" style="width:150px" onchange="ValidarFieldVacio(this)">
				<option value="">Seleccione...</option>';

		if($id_ciudad>=1){
			$sql="SELECT id_comuna FROM terceros_direcciones WHERE id='$id_cliente' AND activo=1";
			$query=$mysql->query($sql,$mysql->link);
			$id_ciudadDB = $mysql->result($query, 0, 'id_comuna');

			echo$sql="SELECT id,comuna FROM ubicacion_comuna WHERE id_ciudad=$id_ciudad AND activo=1 ORDER BY comuna ASC";
			$query=$mysql->query($sql,$mysql->link);
			while ($row=$mysql->fetch_array($query)) {
				$selected = ($id_ciudadDB==$row['id'])? 'selected': '';
				echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['comuna'].'</option>';
			}

		}

		echo '<script>document.getElementById("'.$clase.'_id_comuna").className="myfield";</script>';
	}

	function OptionSelectActividad($id_proyecto,$id_cliente,$link){
		$id_actividad = "";

		if($id_cliente>=1){
			$SQL    = "SELECT id_actividad FROM terceros WHERE id=".$id_cliente." AND activo=1";
			$consul = mysql_query($SQL,$link);
			while($row = mysql_fetch_array($consul)){
				$id_actividad=$row['id_actividad'];
			}
		}
		echo '<select class="myfieldObligatorio" name="Terceros_id_actividad" id="Terceros_id_actividad" style="width:200px" onchange="ValidarFieldVacio(this)">';
		echo '	<option value="">Seleccione...</option>';

		$SQL    = "SELECT id,codigo,nombre FROM configuracion_proyectos_actividades WHERE id_proyecto= $id_proyecto AND activo=1 ORDER BY codigo ASC";
		$consul = mysql_query($SQL,$link);
		while($row = mysql_fetch_array($consul)){
			$selected="";
			if($id_actividad==$row['id']){$selected="selected";}
			echo "<option value='".$row['id']."' ".$selected.">".$row['codigo']." ".$row['nombre']."</option>";
		}

		echo '</select>';
		echo '<script>document.getElementById("Terceros_id_actividad").className="myfield";</script>';

	}

	function OptionSelectTerceroDocumentos($id_empresa,$link){
		$SQL      = "SELECT id,nombre FROM terceros_tipo_documento WHERE activo=1 AND id_empresa='$id_empresa'";
		$consulta = mysql_query($SQL,$link);

		echo '	<div style="width=100%; margin:15px 10px;">
					<div style"overflow:hidden; width:100%;">
						<div style="width:30%; float:left" >Tipo Documento</div>
						<div style="width:65%; float:left" >
							<select id="Terceros_id_documento" style="width:200px" class="myfield">
								<option value="0">Seleccione...</option>';

		while($row = mysql_fetch_array($consulta)){
		echo 					'<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
		}

		echo '				</select>
						</div>
					</div>
				</div>';
	}

	function buscarDatosSucursalPrincipal($id_cliente,$typeDireccion,$link){
		$SQL   = "SELECT id,nombre,direccion,telefono1,departamento,ciudad FROM terceros_direcciones WHERE id_tercero='$id_cliente' AND activo=1 AND direccion_principal=1";
		$query = mysql_query($SQL,$link);
        while($row = mysql_fetch_array($query)){
            echo $row['nombre'].'{br}'.$row['direccion'].'{br}'.$row['telefono1'].'{br}'.$row['departamento'].' - '.$row['ciudad'].'{id}'.$row['id'];
        }
	}


	function cargaTextoDocumentoMail($id,$link){
		if($id!=""){
			$query = "SELECT texto FROM configuracion_documentos where id=".$id;

			include("functions_bd.php");

			$result = mysql_query($query);
			$texto  = mysql_result($result,$i,"texto");
			$texto  = reemplazarVariables($id_intercambio,$texto,'3');
			if($result){ echo 'true{.}'.$texto; }
			else{ echo 'false{.}No se realizo{.}'.$query; }
		}
		else{ echo ""; }
	}


	function selectActividades($id_proyecto,$id_maestro,$type,$link){
		$id_actividad="";

		if($type=="buscarMaestro"){
			$SQL    = "SELECT id_actividad FROM pedido WHERE id='$id_maestro' AND activo=1";
			$consul = mysql_query($SQL,$link);
			$id_actividad = mysql_result($consul,0,'id_actividad');
		}
		else if($type=="inputClientes"){
			$SQL    = "SELECT id_actividad,id_proyecto FROM terceros WHERE id='$id_maestro' AND activo=1";
			$consul = mysql_query($SQL,$link);
			while($row=mysql_fetch_array($consul)){
				$id_actividad = $row['id_actividad'];
				$id_proyecto  = $row['id_proyecto'];
			}
		}

		echo '	<select class="myfield" id="actividad" style="width:250px" onchange="ValidarFieldVacio(this)">';
		echo '		<option value="">Seleccione...</option>';
			$SQL    = "SELECT id,codigo,nombre FROM configuracion_proyectos_actividades WHERE id_proyecto= $id_proyecto AND activo=1 ORDER BY codigo ASC";
			$consul = mysql_query($SQL,$link);
			while($row = mysql_fetch_array($consul)){
				$selected="";
				if($id_actividad==$row['id']){$selected="selected";}
				echo "<option value='".$row['id']."' ".$selected.">".$row['codigo']." ".$row['nombre']."</option>";
			}
		echo "</select>";
	}

	function selectProyectos($id_cliente,$link){
		$id_proyecto="";

		$SQL    = "SELECT id_proyecto FROM terceros WHERE id='$id_cliente' AND activo=1";
		$consul = mysql_query($SQL,$link);
		$id_proyecto = mysql_result($consul,0,'id_proyecto');//mysql_fetch_array($consul)[0];

		echo '	<select id="proyecto" style="width:250px" class="myfield" onchange="actualizarActividad(this.value)">';
		echo '		<option value="">Seleccione...</option>';
		$SQL    = "SELECT id,codigo,nombre FROM configuracion_proyectos WHERE activo=1 ORDER BY codigo ASC";
		$consul = mysql_query($SQL,$link);
		while($row = mysql_fetch_array($consul)){
			$selected="";
			if($id_proyecto==$row['id']){$selected="selected";}
			echo "<option value='".$row['id']."' ".$selected.">".$row['codigo']." ".$row['nombre']."</option>";
		}
		echo "</select>";
	}


	function ventanaVerImagenDocumentoTerceros($nombreImage,$nombreDocumento,$type,$id_host){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/terceros/'.$nombreImage)) {
			$url = '../../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/terceros/'.$nombreImage;
		}
		else{
			$url = '';
		}

		if($type!='pdf'){
			echo'<div style="margin:10px auto 10px auto; width:95%; height:80%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center; overflow:autoscroll; height:90%;">
						<a href="'.$url.'" download="'.$nombreDocumento.'">
							<img src="'.$url.'" style="">
						</a>
					</div>
				</div>';
		}
		else{
			echo'<div style="margin:10px auto 10px auto; width:95%; height:90%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center;">
						<iframe src="'.$url.'" id="iframeViewDocumentTerceros"></iframe>
					</div>
				</div>
				<script>
					cambiaViewPdf();
					function cambiaViewPdf(){
						var iframe=document.getElementById("iframeViewDocumentTerceros");
						iframe.setAttribute("width",Ext.getBody().getWidth()-110);
						iframe.setAttribute("height",Ext.getBody().getHeight()-150);
					}
				</script>';
		}
	}

	function consultaSizeImageDocumentTerceros($id_host,$nombre){

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/terceros/'.$nombre)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/terceros/'.$nombre;
		}
		else{
			$url = '';
		}

		list($ancho, $alto, $tipo, $atributos) = getimagesize($url);
		$size['ancho'] = $ancho;
		$size['alto']  = $alto;
		$size['url']  = $url;

		echo json_encode($size);
	}

	function eliminar_archivo($id_host,$idArchivo,$fileName,$link){
		$sqlDelete   = "DELETE FROM terceros_documentos WHERE id='$idArchivo'";
		$queryDelete = mysql_query($sqlDelete,$link);

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/terceros/'.$fileName)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/terceros/'.$fileName;
		}
		else{
			$url = '';
		}

		if($queryDelete){
			// $enlace = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/imagenes_empresas/empresa_'.$id_host.'/documentos_items/'.$nombreRandomico;
			unlink($url);
			echo 'true'; exit;
		}
		echo 'false';
	}

	/****************************  agregar un retencion al item **************************************/
	function guardar_retencion($id_empresa,$idRetencion,$idTercero,$id_empresa,$link){

		$sql   = "INSERT INTO terceros_retenciones (id_retencion,id_proveedor,id_empresa) VALUES ($idRetencion,$idTercero,'$id_empresa')";
		$query = mysql_query($sql,$link);

		$sqlId  = "SELECT LAST_INSERT_ID()";
		$lastId = mysql_result(mysql_query($sqlId,$link),0,0);

		$cont = explode("_", $campo);
		$cont = $cont[1];

		if ($query) {
			echo'<script>
					Inserta_Div_tercerosRetenciones('.$lastId.');
					Elimina_Div_busquedaRetencionesTercero('.$idRetencion.');
				</script>';
		}
		else{ echo '<script>alert("Se produjo un error y no se agrego el retencion!");</script>'; exit; }
	}

	/********************************************* funcion para eliminar retenciones agregados al articulo ***************************************/
	function eliminarRetencionItem($idProveedor,$idRetencion,$link){
		$sql = "UPDATE  terceros_retenciones SET activo=0 WHERE id_retencion=$idRetencion AND id_proveedor=$idProveedor AND id_empresa=".$_SESSION['EMPRESA'];
		mysql_query($sql,$link);
	}

	function msjErrorUpload($idError,$id_empresa,$prospecto,$link){

		if($prospecto == 'true'){//SE CARGO UN EXCEL PARA INSERTAR PROSPECTOS
			$tabla = 'prospectos_upload_registro';
		}
		else{
			$tabla = 'terceros_upload_registro';
		}

		$sqlError   = "SELECT mensaje_error FROM $tabla WHERE id='$idError' AND id_empresa='$id_empresa'";
		$queryError = mysql_query($sqlError,$link);
		$msjError   = mysql_result($queryError, 0, 'mensaje_error');

		echo'<div style="background-color:#FFF; width:100%; height:100%; padding:2%;">'.$msjError.'</div>';
	}

	function cargaBuscadorAsignadosTerceros($grillaName,$id_asignado,$nombre_asignado,$link){

		$value = "";
		if(isset($id_asignado) AND $id_asignado > 0){
			$value = "value =\"".$nombre_asignado."\"";
		}

		echo '<div style="width:300px;height:100%">
				  <div style="width:100%;padding-left:65px;color:#5077AC">Filtrar por Asignado</div>
				  <div style="width:100%">
			  	  	 <div style="width:209px;padding-left:11px;padding-bottom: 2px;float:left;padding-right:5px;">
				  	 	 <input class="MyField" type="text" id="nombreAsignado_'.$grillaName.'" style="margin-top:10px; width:209px;font-size:12px !important" onclick="BuscarFuncionario(\'idAsignado_'.$grillaName.'\',\'nombreAsignado_'.$grillaName.'\',\'false\')" placeholder="Seleccione funcionario..." '.$value.'>
			  	  	 	 <input id="idAsignado_'.$grillaName.'" type="hidden" value="'.$id_asignado.'">
			  	  	 </div>
			  	  	 <div style="float:left; width:25px; height:17px; text-align:center; cursor:pointer;margin-top:10px;" onclick="recargaGrillaAsignado(\''.$grillaName.'\')" class="buscar16">
				  	 	 &nbsp;
    		  	  	 </div>
    		  	  </div>
    		  </div>';

	}

	function VentanaEliminarProspecto($link,$id){
		echo '<div style="width:100%; margin:10px;">
				  <div style="width:100%; margin:10px;">Observaciones:</div>
				  <textarea style="width:340px; margin:10px;" rows="8" id="observaciones_eliminar_prospecto"></textarea>
				  <div id="div_eliminacion_prospecto" style="display:none"></div>
			  </div>';
	}

	function eliminar_campo_prospecto($link,$id,$observaciones){

		$arrayReplaceString = array("\n","\r","<br>");
		$observaciones      = str_replace($arrayReplaceString, "<br>", $observaciones);

		$sql =   "INSERT INTO terceros_log(id_tercero,fecha,hora,id_usuario,accion,observacion)  values('$id',NOW(),NOW(),'$_SESSION[IDUSUARIO]','elimina_prospecto','$observaciones');";
		$connectid =$GLOBALS['mysql']->query($sql,$link);
		if (!$connectid){die('No se guardo la informacion '.$GLOBALS['mysql']->error());}

		echo "	<script>
					Win_Ventana_EliminarProspecto.close();
					eliminaProspectos();
				</script>";
	}

?>
