<?php
	session_start();
	include("../../../configuracion/define_variables.php");
?>
<style type="text/css">
	body {
		margin-left   : 0px;
		margin-top    : 0px;
		margin-right  : 0px;
		margin-bottom : 0px;
	}
</style>
<?php
	if(isset($enviado)){

		$serv = $_SERVER['DOCUMENT_ROOT']."/";
		$id_host = $_SESSION['ID_HOST'];

		$ruta1 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$_SESSION['ID_HOST'].'/';
		if(!file_exists($ruta1)){ mkdir ($ruta1); }


		$ruta2 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$_SESSION['ID_HOST'].'/compras';
		$url  = $ruta2.'/';
		if(!file_exists($ruta2)){ mkdir ($ruta2); }

		$ruta3 = $serv.'ARCHIVOS_PROPIOS/empresa_'.$_SESSION['ID_HOST'].'/compras/archivos_temporales';
		$url  = $ruta3.'/';
		if(!file_exists($ruta3)){ mkdir ($ruta3); }

		$nombre_arc = $_FILES['file_ad'.$id]['name'];
		move_uploaded_file($_FILES['file_ad'.$id]['tmp_name'],$url.$nombre_arc);

		echo '<div style="float:left; width:20px">
						<img src="../../../../temas/clasico/images/formularios/correcto.gif" width="16" height="16">
					</div>
					<div style="float:left; width:230px; font-size:10px; font-family:sans-serif">'.$nombre_arc.'&nbsp;&nbsp;[<a href="#" onClick="eliminar('.$id.')">Eliminar</a>]</div>';

		echo '<script>
						if(parent.document.getElementById("adjuntos2").value == ""){
							parent.document.getElementById("adjuntos2").value = "'.$nombre_arc.'";
						}
						else{
							parent.document.getElementById("adjuntos2").value = parent.document.getElementById("adjuntos2").value +",'.$nombre_arc.'";
						}
					</script>';
	}
	else{
	?>
	<form action="eje_upload_archivo.php" method="post" enctype="multipart/form-data" name="form_mail<?php if($_GET){echo $_GET['id'];}else{echo $_POST['id'];} ?>">
		<input name="file_ad<?php if($_GET){echo $_GET['id'];}else{echo $_POST['id'];} ?>" type="file" style="width:260px" class="inputs" id="file_ad<?php if($_GET){echo $_GET['id'];}else{echo $_POST['id'];} ?>" onChange="sube_imagen_mail(<?php if($_GET){echo $_GET['id'];}else{echo $_POST['id'];} ?>)" />
		<input name="enviado" id="enviado" type="hidden" value="true">
		<input name="id" id="id" type="hidden" value="<?php if($_GET){echo $_GET['id'];}else{echo $_POST['id'];} ?>">
		<input name="dir" id="dir" type="hidden" value="<?php if($_GET){echo $_GET['dir'];}else{echo $_POST['dir'];} ?>">
	</form>
	<?php
	}
?>
<script>
	function sube_imagen_mail(id){
		setTimeout('document.form_mail<?php if($_GET){echo $_GET['id'];}else{echo $_POST['id'];} ?>.submit()',200);
	}

	<?php if(isset($enviado)){ ?>
	function eliminar(id){
		document.location = "eje_upload_archivo.php?id=" + id + "&dir=<?php echo $dir; ?>";
		var cadena1 = parent.document.getElementById("adjuntos2").value;

		if(cadena1.indexOf('<?php echo $nombre_arc.','; ?>') != -1){
			cadena1 = cadena1.replace('<?php echo $nombre_arc.','; ?>',"");
		}
		else{
			cadena1 = cadena1.replace('<?php echo $nombre_arc; ?>',"");
		}

		var compa = cadena1.charAt(cadena1.length-1);

		if(compa == ','){
			cadena1 = cadena1.substring(0,cadena1.length-1);
		}

		parent.document.getElementById("adjuntos2").value = cadena1;
	}
	<?php } ?>
</script>
