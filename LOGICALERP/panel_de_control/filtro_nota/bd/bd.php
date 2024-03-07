<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($op) {

		// INSERT NUEVA CUENTA DE BANCO
		case 'ventanaInsertFiltroNota':
			ventanaInsertFiltroNota();
			break;

		case 'saveInsertFiltroNota':
			saveInsertFiltroNota($nombreNota,$consecutivoNota,$consecutivoNotaNiif,$documentoCruce,$id_empresa,$link);
			break;

		// UPDATE NUEVA CUENTA DE BANCO
		case 'ventanaUpdateFiltroNota':
			ventanaUpdateFiltroNota($id,$link);
			break;

		case 'saveUpdateFiltroNota':
			saveUpdateFiltroNota($nombreNota,$consecutivoNota,$consecutivoNotaNiif,$documentoCruce,$id,$id_empresa,$link);
			break;

		// ELIMINA CUENTA BANCO
		case 'eliminaFiltroNota':
			eliminaFiltroNota($id,$id_empresa,$link);
			break;
	}

	//================================== INSERT NUEVA CUENTA DE BANCO =======================================//

	function ventanaInsertFiltroNota(){
		echo'<div style="float:left; margin: 0 10px 10px 10px;">
				<div id="renderInserUpdateFiltroNota" style="float:left; width:100%; height:20px;"></div>
			    <div style="float:left; width:110px; margin:3px 0 0 0">Descripcion</div>
			    <div style="float:left; width:180px">
			    	<input type="text" class="myfieldObligatorio" id="nombreFiltroNota" style="width:180px" onkeyup="this.value = this.value.toUpperCase()">
				</div>

				<div style="float:left; width:110px; margin-top:10px;">Consecutivo Colgaap</div>
			    <div style="float:left; width:180px; margin-top:10px;">
			    	<input type="text" class="myfieldObligatorio" id="consecutivoFiltroNota" style="width:180px" value="1" onkeyup="this.value = (this.value).replace(/[^\\d]/g, \'\');">
				</div>

				<div style="float:left; width:110px; margin-top:10px;">Consecutivo Niif</div>
			    <div style="float:left; width:180px; margin-top:10px;">
			    	<input type="text" class="myfieldObligatorio" id="consecutivoFiltroNotaNiif" style="width:180px" value="1" onkeyup="this.value = (this.value).replace(/[^\\d]/g, \'\');">
				</div>

				<div style="float:left; width:110px; margin-top:10px;">Documento Cruce</div>
			    <div style="float:left; width:180px; margin-top:10px;">
			    	<select class="myfieldObligatorio" id="documentoCruceFiltroNota" style="width:180px">
			    		<option value="">Seleccione...</option>
			    		<option value="Si">Si</option>
			    		<option value="No">No</option>
			    	</select>
				</div>
			</div>';
	}

	function saveInsertFiltroNota($nombreNota,$consecutivoNota,$consecutivoNotaNiif,$documentoCruce,$id_empresa,$link){

		$sqlInsert      = "INSERT INTO tipo_nota_contable (descripcion,consecutivo,consecutivo_niif,documento_cruce,id_empresa) VALUES('$nombreNota','$consecutivoNota','$consecutivoNotaNiif','$documentoCruce','$id_empresa')";
		$queryInsert    = mysql_query($sqlInsert,$link);
		$ultimoIdInsert = mysql_insert_id();

		if($queryInsert){
			echo '<script>
					Inserta_Div_panelControlFiltroNota("'.$ultimoIdInsert.'");
					Win_Ventana_Agregar_panelControlFiltroNota.close();
				</script>';
		}
		else { echo '<script>alert("Aviso,\nHa ocurrido un error en la conexion con la base de datos!")</script>'; }

	}

	//==================================== UPDATE CUENTA DE BANCO =========================================//

	function ventanaUpdateFiltroNota($id,$link){
		$sql   = "SELECT descripcion,consecutivo,consecutivo_niif,documento_cruce FROM tipo_nota_contable WHERE id='$id' AND activo = 1 LIMIT 0,1";
		$query = mysql_query($sql,$link);

		$tipoNota        = mysql_result($query,0,'descripcion');
		$consecutivoNota = mysql_result($query,0,'consecutivo');
		$consecutivoNotaNiif = mysql_result($query,0,'consecutivo_niif');
		$documentoCruce  = mysql_result($query,0,'documento_cruce');

		echo'<div style="float:left; margin: 0 10px 10px 10px;">
				<div id="renderInserUpdateFiltroNota" style="float:left; width:100%; height:20px;"></div>
			    <div style="float:left; width:110px; margin:3px 0 0 0">Descripcion</div>
			    <div style="float:left; width:180px">
			    	<input type="text" class="myfieldObligatorio" id="nombreFiltroNota" style="width:180px" value="'.$tipoNota.'" onkeyup="this.value = this.value.toUpperCase()">
				</div>

				<div style="float:left; width:110px; margin-top:10px;">Consecutivo Colgaap</div>
			    <div style="float:left; width:180px; margin-top:10px;">
			    	<input type="text" class="myfieldObligatorio" id="consecutivoFiltroNota" style="width:180px" value="'.$consecutivoNota.'" onkeyup="this.value = (this.value).replace(/[^\\d]/g, \'\');">
				</div>

				<div style="float:left; width:110px; margin-top:10px;">Consecutivo Niif</div>
			    <div style="float:left; width:180px; margin-top:10px;">
			    	<input type="text" class="myfieldObligatorio" id="consecutivoFiltroNotaNiif" style="width:180px" value="'.$consecutivoNotaNiif.'" onkeyup="this.value = (this.value).replace(/[^\\d]/g, \'\');">
				</div>

				<div style="float:left; width:110px; margin-top:10px;">Documento Cruce</div>
			    <div style="float:left; width:180px; margin-top:10px;">
			    	<select class="myfieldObligatorio" id="documentoCruceFiltroNota" style="width:180px">
			    		<option value="">Seleccione...</option>
			    		<option value="Si">Si</option>
			    		<option value="No">No</option>
			    	</select>
				</div>
			</div>
			<script>
				document.getElementById("documentoCruceFiltroNota").value="'.$documentoCruce.'";
			</script>';
	}

	function saveUpdateFiltroNota($nombreNota,$consecutivoNota,$consecutivoNotaNiif,$documentoCruce,$id,$id_empresa,$link){
		$sqlUpdate   = "UPDATE tipo_nota_contable SET descripcion='$nombreNota', consecutivo='$consecutivoNota',consecutivo_niif='$consecutivoNotaNiif', documento_cruce='$documentoCruce' WHERE activo=1 AND id='$id' AND id_empresa='$id_empresa'";
		$queryUpdate = mysql_query($sqlUpdate,$link);

		if($queryUpdate){
			echo '<script>
					Actualiza_Div_panelControlFiltroNota("'.$id.'");
					Win_Ventana_Editar_panelControlFiltroNota.close();
				</script>';
		}
		else { echo '<script>alert("Aviso,\nHa ocurrido un error en la conexion con la base de datos!")</script>'; }

	}

	//==================================== ELIMINA CUENTA DE BANCO =========================================//

	function eliminaFiltroNota($id,$id_empresa,$link){
		$sqlDelete   = "UPDATE tipo_nota_contable SET activo=0 WHERE activo=1 AND id='$id' AND id_empresa='$id_empresa'";
		$queryDelete = mysql_query($sqlDelete,$link);

		echo'<script>
				Elimina_Div_panelControlFiltroNota("'.$id.'");
				Win_Ventana_Editar_panelControlFiltroNota.close();
			</script>';
	}

?>