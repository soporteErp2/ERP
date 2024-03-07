<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];
	$tipo_documento = $_SESSION['DOCUMENTO_CONFIG'];

	$PrimerParte = '<div style="float:left; width:100%; margin:5px;">
					    <b>Documento</b></br>
					    <div style="float:left; width:100%; margin:1px 0 1px 10px; cursor:pointer;" onClick="inserta(\'CONTENIDO_CABECERA\');WinVarAsiste.close();">
						    Contenido Cabecera
					    </div>
					    <div style="float:left; width:100%; margin:1px 0 1px 10px; cursor:pointer;" onClick="inserta(\'CONTENIDO_DOCUMENTO\');WinVarAsiste.close();">
						    Contenido Documento
					    </div>
				    </div>';

	$sql	= "SELECT * FROM variables_grupos WHERE id_empresa=$id_empresa AND activo=1 AND nombre='$tipo_documento'";
	$result = mysql_query($sql,$link);
	$total	= mysql_num_rows($result);

    echo $PrimerParte;

	if ($total> 0) {
		while ($salida = mysql_fetch_assoc($result)) {
			echo'<div style="float:left; width:100%; margin:5px;">
					<b>'.$salida['nombre'].'</b>';

				$sql1    = "SELECT * FROM variables WHERE id_grupo=".$salida['id'];
				$result1 = mysql_query($sql1,$link);
				$total1  = mysql_num_rows($result1);
				echo "</br>";
				if ($total1> 0) {
					while ($salida1 = mysql_fetch_assoc($result1)) {
						echo'<div style="float:left; width:100%; margin:1px 0 1px 10px; cursor:pointer;" onClick="inserta('."'".$salida1['nombre']."'".');WinVarAsiste.close();">
								'.$salida1['detalle'].'
							</div>';
				   }
				}
			echo'</div>';
	   }
	}
?>

<script>
function inserta(variable){
	CKEDITOR.instances.editarDocumentosErp.insertHtml('<span style="background-color: rgb(255, 0, 0);">['+variable+']</span>&nbsp;');
}
</script>



