<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	include('bd/functions_bd.php');
	
	cargaTextoContrato($id_contrato, $propiedad);

	include("../../misc/ckeditor/ckeditor_php5.php");
	include("../../misc/ckfinder/ckfinder.php");
	
	$PathApps	=	'../../misc';	
	$InstanciaName = 'editor1';
	$CKEditor = new CKEditor();
	$config['height']				=  $myalto - 195;
	$CKEditor->basePath 			=  $PathApps.'/ckeditor/';
	$CKEditor->config['filebrowserBrowseUrl'] = $PathApps.'/pdwfilebrowser/index.php?editor=ckeditor';
	$CKEditor->config['filebrowserImageBrowseUrl'] = $PathApps.'/pdwfilebrowser/index.php?editor=ckeditor&filter=image';
	$CKEditor->config['filebrowserFlashBrowseUrl'] = $PathApps.'/pdwfilebrowser/index.php?editor=ckeditor&filter=flash';

 	$CKEditor->editor($InstanciaName, $texto, $config);

?>

<script type='text/javascript'>

function guardarBodyContrato() {
	
	editor = CKEDITOR.instances['<?php echo $InstanciaName ?>'];
	data = editor.getData();
	Ext.Ajax.request
	(
		{
		url		: 'bd/bd.php',
		method	: 'post',
		timeout : 180000,
		params	:
			{
				op			:	'actualTextoContrato',
				id_contrato	:	<?php echo $id_contrato?>,
				texto	 	:	data
			},
		success: function (result, request)
			{
				var resultado  =  result.responseText.split("{.}");
				var respuesta = resultado[0];
				var id = resultado[1];
				if(respuesta == 'true'){
					MyLoading();
				}else{
					alert('Error Guardando El Formato del Contrato!');	
				}
			}
		}
	);		
}

function cerrarBodyContrato(){
	editor = CKEDITOR.instances['<?php echo $InstanciaName ?>'];
	editor.destroy();
	editor = null;
	win_editor.close();
}

</script>



