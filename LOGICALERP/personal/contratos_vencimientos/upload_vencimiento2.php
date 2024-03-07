<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_usuario = $_SESSION['IDUSUARIO'];
	$id_empresa = $_SESSION['EMPRESA'];

	$nit_empresa = $mysql->result($mysql->query("SELECT documento FROM empresas WHERE id = '$id_empresa '"),0,'documento');

?>

<style>
	.qq-uploader {position:relative; width: 100%; height:170px;}
	.qq-upload-button {display:block; width:100%; height:44px; background-image:url(../personal/contratos_vencimientos/images_upload/boton.png)}
	.qq-upload-button-hover {/*background:#fff;*/; background-image:url(../personal/contratos_vencimientos/images_upload/boton.png)}
	.qq-upload-button-focus {/*outline:1px dotted black;*/}
	.qq-upload-drop-area {position:absolute; top:40; left:0; width:100%; height:100%; min-height: 70px; z-index:2;background:none; text-align:center; display:none; }
	.qq-upload-drop-area span { display:block; position:absolute; top: 50%; width:100%; margin-top:-8px; font-size:16px;}
	.qq-upload-drop-area-active {background:#FF0000; opacity: 0.3; filter:alpha(opacity=30); -moz-opacity:0.3; -khtml-opacity: 0.3;}
	.qq-upload-list {margin:65px 15px 15px 15px; padding:5px; height:40px; list-style:none; color:#333; text-align:center; background-color:#FFFFFF; border: 1px solid #D4D4D4; overflow: auto;}
	.qq-upload-list li { margin:0; padding:0; line-height:15px; font-size:12px;}
	.qq-upload-file, .qq-upload-spinner, .qq-upload-size, .qq-upload-cancel, .qq-upload-failed-text { margin-right: 7px;}
	.qq-upload-file {}
	.qq-upload-spinner {display:inline-block; background: url(../personal/contratos_vencimientos/images_upload/loading.gif); width:15px; height:15px; vertical-align:text-bottom;}
	.qq-upload-size,.qq-upload-cancel {font-size:11px;}
	.qq-upload-failed-text {display:none;}
	.qq-upload-fail .qq-upload-failed-text {display:inline;}
</style>

<div id="fileuploader" style="background-image:url(../personal/contratos_vencimientos/images_upload/fondo3.png)"></div>

<script>

	function createUploader(){
		var uploader = new qq.FileUploader({
            element	 	:  document.getElementById('fileuploader'),
            action 		: '../personal/contratos_vencimientos/guarda_vencimiento.php',
            debug		: true,
			params		:
			{
				id_contrato : '<?php echo $id_contrato; ?>',
				id_usuario  : '<?php echo $id_usuario; ?>',
				nit_empresa : '<?php echo $nit_empresa; ?>',
			},
			button		: null,
			multiple	: false,
			maxConnections: 3,
			allowedExtensions: ['jpg','png','gif', 'bmp', 'doc', 'docx', 'pdf'],
			sizeLimit	: 10*1024*1024*1024,
			minSizeLimit: 0,
			onSubmit	: function(id, fileName){},
			onProgress	: function(id, fileName, loaded, total){},
			onComplete	: function(id, fileName, responseJSON){
								var RespJson = eval(responseJSON);
								if (typeof(RespJson.error)!='undefined') { return; }

								var id = (RespJson.idRow)
								var table = document.getElementById('archivos_adjuntos');

								table.innerHTML = table.innerHTML+'<tr id="archivo_adjunto_'+RespJson.idRow+'">'+
																		'<td>'+RespJson.filename+'.'+RespJson.ext+'</td>'+
																		'<td>'+RespJson.fecha+'</td>'+
																		'<td>'+RespJson.usuario+'</td>'+
																		'<td><img src="../personal/images/view.png" title="Ver Archivo" onclick="ver_documento_contrato(\''+RespJson.idRow+'\',\''+RespJson.filename+'\',\''+RespJson.ext+'\')"></td>'+
																		'<td><img src="../personal/images/delete_file.png" title="Eliminar Archivo" onclick="eliminarDocumentoVencimiento(\''+RespJson.idRow+'\',\''+RespJson.filename+'.'+RespJson.ext+'\')" ></td>'+
																	'</tr>';
								// Inserta_Div_TercerosDocumentos(id);
								Actualiza_Div_empleados_contratos('<?php echo $id_contrato; ?>');
								Win_Ventana_Agregar_archivos_adjuntos.close();
						  },
			onCancel	: function(id, fileName){},
			messages: {
				typeError    : "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\nImagenes con extension (jpg, png, gif y bmp)\nDocumentos con extension (doc, docx y pdf)",
				sizeError    : "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
				minSizeError : "{file} is too small, minimum file size is {minSizeLimit}.",
				emptyError   : "{file} is empty, please select files again without it.",
				onLeave      : "Cargando Archivo."
			}
		});
	}

	createUploader();

</script>
