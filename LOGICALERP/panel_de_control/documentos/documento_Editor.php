<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/ckeditor/ckeditor_php5.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$_SESSION['DOCUMENTO_CONFIG'] = $tipo_documento;

	$sqlTexto = "SELECT COUNT(id) AS contTexto, texto
				FROM configuracion_documentos_erp
				WHERE id='$id_documento' AND id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' LIMIT 0,1";
	$queryTexto = mysql_query($sqlTexto, $link);
	$contTexto  = mysql_result($queryTexto,0,"contTexto");
	$texto      = mysql_result($queryTexto,0,"texto");

	if($contTexto == 0){ echo '<script>alert("Aviso,\nNo existe un formato por la presente sucursal!")</script>'; exit; }

	$InstanciaName    = 'editarDocumentosErp';
	$CKEditor         = new CKEditor();
	$config['height'] = $myalto - 195;

 	$CKEditor->editor($InstanciaName, $texto, $config);

?>

<script type='text/javascript'>

	win_editor.on('close',function(w){cerrarBodydocumento();});

	function guardarBodydocumento() {

		editor = CKEDITOR.instances['<?php echo $InstanciaName ?>'];
		data   = editor.getData();
		Ext.Ajax.request({
			url		: 'documentos/bd/bd.php',
			method	: 'post',
			timeout : 180000,
			params	:
			{
				op           : 'actualTextoDocumento',
				id_documento : <?php echo $id_documento?>,
				texto        : data
			},
			success: function (result, request)
			{
				var resultado = result.responseText.split("{.}");
				var respuesta = resultado[0];
				var id = resultado[1];
				if(respuesta == 'true'){ MyLoading(); }
				else{ alert('Error Guardando El Formato del documento!'); }
			}
		});
	}

	function cerrarBodydocumento(){
		editor = CKEDITOR.instances['<?php echo $InstanciaName ?>'];
		editor.destroy();
		editor=null;
		//win_editor.close();
	}

	function inserta(variable){
		editor = CKEDITOR.instances['<?php echo $InstanciaName ?>'];
		editor.insertHtml('<span style="background-color: rgb(255, 0, 0);">['+variable+']</span>');
	}
</script>