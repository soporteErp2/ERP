<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	//CONSULTAMOS LA RUTA DEL LOGO Y YA SE CARGO CON ENTERIORIDAD PARA MOSTRARLO
	$sql   ="SELECT nombre,ext FROM configuracion_imagenes_documentos WHERE activo=1 AND id_empresa=".$_SESSION['EMPRESA'];
	$query =mysql_query($sql,$link);

	$nombre = mysql_result($query,0,'nombre');
	$ext    = mysql_result($query,0,'ext');

	if ($nombre=='') {
		$imagen='../../ARCHIVOS_PROPIOS/question.jpg';
	}
	else{
		$imagen='../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_'.$_SESSION['EMPRESA']."/logos/".$nombre.'.'.$ext;
	}

?>

<style>
	.qq-uploader {position:relative; width: 100%; height:240px;}
	.qq-upload-button {display:block; width:100%; height:116px; background-image:url(subir_imagen/upload/boton2.png?v4.0.0.12-05-2013)}
	.qq-upload-button-hover {/*background:#fff;*/; background-image:url(subir_imagen/upload/boton2.png?v4.0.0.12-05-2013)}
	.qq-upload-button-focus {/*outline:1px dotted black;*/}
	.qq-upload-drop-area {position:absolute; top:116; left:0; width:100%; height:100%; min-height: 70px; z-index:2;background:none; text-align:center; display:none; }
	.qq-upload-drop-area span { display:block; position:absolute; top: 50%; width:100%; margin-top:-8px; font-size:16px;}
	.qq-upload-drop-area-active {background:#FF0000; opacity: 0.3; filter:alpha(opacity=30); -moz-opacity:0.3; -khtml-opacity: 0.3;}
	/**/
	.qq-upload-list {margin:65px 15px 15px 15px; padding:5px; height:100px; list-style:none; color:#333; text-align:center; background-color:#FFFFFF;opacity:-30; -webkit-box-shadow: 1px 1px 3px #333;	-moz-box-shadow: 1px 1px 2px #333;}
	.qq-upload-list li { margin:0; padding:0; line-height:15px; font-size:12px;}
	.qq-upload-file, .qq-upload-spinner, .qq-upload-size, .qq-upload-cancel, .qq-upload-failed-text { margin-right: 7px;}
	.qq-upload-file {}
	.qq-upload-spinner {display:inline-block; background: url("subir_imagen/upload/loading.gif"); width:15px; height:15px; vertical-align:text-bottom;}
	.qq-upload-size,.qq-upload-cancel {font-size:11px;}
	.qq-upload-failed-text {display:none;}
	.qq-upload-fail .qq-upload-failed-text {display:inline;}
</style>



<div id="fileuploader" style="background-image: url(<?php echo $imagen; ?>?v4.0.0.12-05-2013_);background-repeat: no-repeat;background-position:50% 100%;background-size: 200px 125px;">
</div>

<div style="background-color: #FFF;height:100px;">
	
</div>
<script>

	function createUploader(){
		var uploader = new qq.FileUploader({
                element	 	:  document.getElementById('fileuploader'),
                action 		: 'subir_imagen/upload/guarda_documento.php',
                debug		: true,
				params		: {
					id	:	'<?php echo $id; ?>',
					td	:	'<?php echo $td; ?>'
				},
				button		: null,
				multiple	: false,
				maxConnections: 3,
				allowedExtensions: ['jpg','png','gif', 'bmp'],
				sizeLimit	: 10*1024*1024,
				minSizeLimit: 0*0*0,
				minWidthLimit: 0,
				onSubmit	: function(id, fileName){},
				onProgress	: function(id, fileName, loaded, total){},
				onComplete	: function(id, fileName, responseJSON){
									var RespJson = eval(responseJSON);
									var id = (RespJson.idRow);
									console.log(RespJson.responseText);
									document.getElementById("fileuploader").style.backgroundImage = "url(../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_<?php echo $_SESSION[EMPRESA]; ?>/logos/"+RespJson.imagen+")";
									
									if((RespJson.ancho*1)<500 && (RespJson.alto*1)<200){
										archivo=RespJson.imagen;
										verificaImagen(archivo);
									}


							  },
				onCancel	: function(id, fileName){},
				messages: {
					typeError: "{file}\nArchivo no permitido.\n\n Solo se permiten los siguientes archivo:\n\nImagenes con extension (jpg, png, gif y bmp)",
					sizeError: "\"{file}\"  Archivo muy grande, Tamano Maximo Permitido ( {sizeLimit} ).",
					minSizeError: "{file} Archivo muy pequeno, Tamano Minimo Permitido {minSizeLimit}.",
					minWidthError: "Archivo muy pequeno, debe se mas grande",
					emptyError: "{file} is empty, please select files again without it.",
					onLeave: "Cargando Archivo."
				}
		});
	}

	createUploader();

	//FUNCION SI LA IMAGEN ES MAS PEQUEÑA DE LO PERMITIDO
	function verificaImagen(archivo){
		
		//si es mas pequeña, mostramos un alert, eliminamos la imagen cargada y mostramos el signo de pregunta
	
		alert("Error!\nEl tamano de la imagen es muy bajo\ndebe se igual o mayor a 500 x 200 pixeles");

		Ext.Ajax.request({
            url     : 'subir_imagen/upload/bd.php',
            params  : {
               archivo   : archivo
            },
            success :function (result, request){
                        if(result.responseText == 'false'){
                            alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                        }else{
                        	if (result.responseText=='.') {
                        		document.getElementById("fileuploader").style.backgroundImage = "url(../../ARCHIVOS_PROPIOS/question.jpg)";
                        	}else{
                        		document.getElementById("fileuploader").style.backgroundImage = "url(../../ARCHIVOS_PROPIOS/imagenes_empresas/empresa_<?php echo $_SESSION[EMPRESA]; ?>/logos/"+result.responseText+")";	
                        	}
                            

                        }

                    },
            failure : function(){ alert('Error de conexion con el servidor');}
        });
		
	}

</script>
