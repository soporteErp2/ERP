<?php
	include("../../../configuracion/define_variables.php");
?>
<style>
	.qq-uploader {position:relative; width: 100%; height:170px;}
	
	.qq-upload-button {display:block; width:100%; height:44px; background-image:url(upload/boton.png?v4.0.0.15-07-2013)}
	.qq-upload-button-hover {/*background:#fff;*/; background-image:url(upload/boton.png?v4.0.0.15-07-2013)}
	.qq-upload-button-focus {/*outline:1px dotted black;*/}
	
	.qq-upload-drop-area {
    position:absolute; top:40; left:0; width:100%; height:100%; min-height: 70px; z-index:2;
    background:none; text-align:center; display:none; ; 
	}
	.qq-upload-drop-area span {
    display:block; position:absolute; top: 50%; width:100%; margin-top:-8px; font-size:16px;
	}
	.qq-upload-drop-area-active {background:#FF0000; opacity: 0.3; filter:alpha(opacity=30); -moz-opacity:0.3; -khtml-opacity: 0.3;}
		
	.qq-upload-list {margin:65px 15px 15px 15px; padding:5px; height:40px; list-style:none; color:#333; text-align:center; background-color:#FFFFFF; -webkit-box-shadow: 1px 1px 3px #333;	-moz-box-shadow: 1px 1px 2px #333;}
	.qq-upload-list li { margin:0; padding:0; line-height:15px; font-size:12px;}
	.qq-upload-file, .qq-upload-spinner, .qq-upload-size, .qq-upload-cancel, .qq-upload-failed-text { margin-right: 7px;}
	.qq-upload-file {}
	.qq-upload-spinner {display:inline-block; background: url("upload/loading.gif"); width:15px; height:15px; vertical-align:text-bottom;}
	.qq-upload-size,.qq-upload-cancel {font-size:11px;}
	.qq-upload-failed-text {display:none;}
	.qq-upload-fail .qq-upload-failed-text {display:inline;}
</style>

<div id="fileuploader" style="background-image:url(upload/fondo.png?v4.0.0.15-07-2013)">

</div>
<script>

	function createUploader(){
		var uploader = new qq.FileUploader({
                element	 	:  document.getElementById('fileuploader'),
                action 		: 'upload/guarda_foto.php',
                debug		: true,
				params		: { id	:	'<?php echo $id; ?>'},
				button		: false,
				multiple	: false,
				maxConnections: 3,
				allowedExtensions: ['jpg','png','gif', 'bmp'],               
				sizeLimit	: 0,   
				minSizeLimit: 0,                             
				onSubmit	: function(id, fileName){},
				onProgress	: function(id, fileName, loaded, total){},
				onComplete	: function(id, fileName, responseJSON){Habilita_Frame_Webcam('<?php echo $id; ?>');Win_Fotos_Empleados.close();},
				onCancel	: function(id, fileName){},               
				messages: {
					typeError: "{file}\nArchivo no permitido.\n\n Solo se permiten imagenes con extension (jpg, png, gif y bmp)",
					sizeError: "{file} Archivo muy grande, Tamaño Maximo {sizeLimit}.",
					minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
					emptyError: "{file} is empty, please select files again without it.",
					onLeave: "Cargando Archivo."            
				}
		});	
	}
	
	createUploader();
	
</script>
