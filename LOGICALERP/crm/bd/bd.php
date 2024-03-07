<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	//require("functions_body_article.php");	

	$id_host     = $_SESSION['ID_HOST'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];	

	switch ($opc) {	

		case 'ventanaVerImagen':
			ventanaVerImagen($nombreImage,$nombreDocumento,$type,$folder,$id_host);
			break;
		case 'consultaSizeImage':
			consultaSizeImage($id_host,$folder,$nombre);
			break;
		case 'eliminarArchivoAdjunto':
			eliminarArchivoAdjunto($id,$nombre,$id_host,$folder,$mysql);
			break;

		case 'mostrarAlmacenamiento':
			mostrarAlmacenamiento();
			break;
	}

	function ventanaVerImagen($nombreImage,$nombreDocumento,$type,$folder,$id_host){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/'.$folder.$nombreImage)) {
			$url = '../../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/'.$folder.$nombreImage;
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
						<iframe src="'.$url.'" id="iframeView"></iframe>
					</div>
				</div>
				<script>
					cambiaViewPdf();
					console.log(Ext.getBody().getWidth()-110);
					console.log(Ext.getBody().getHeight()-150);
					function cambiaViewPdf(){
						var iframe=document.getElementById("iframeView");
						iframe.setAttribute("width",Ext.getBody().getWidth()-110);
						iframe.setAttribute("height",Ext.getBody().getHeight()-150);
					}
				</script>';
		}
	}

	function consultaSizeImage($id_host,$folder,$nombre){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/'.$folder.$nombre)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/'.$folder.$nombre;
		}
		else{
			$url = '';
		}

		list($ancho, $alto, $tipo, $atributos) = getimagesize($url);
		$size['ancho'] = $ancho;
		$size['alto']  = $alto;
		echo json_encode($size);
	}

	function eliminarArchivoAdjunto($id,$nombre,$id_host,$folder,$mysql){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/'.$folder.$nombre)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/'.$folder.$nombre;
		}
		else{
			$url = '';
		}

		if ( unlink($url) ) {
			if($folder == 'crm/objetivos_adjuntos/'){
				$tabla = 'crm_objetivos_adjuntos';				
			}

			$sql="DELETE FROM $tabla WHERE activo=1 AND id=$id";
			$query=$mysql->query($sql,$mysql->link);
			if ($query) {
				echo '<script>
						var element = document.getElementById("archivo_adjunto_'.$id.'");
						element.parentNode.removeChild(element);
						MyLoading2("off",{texto:"Registro Eliminado"});
					</script>';
			}
			else{
				echo '<script>
						MyLoading2("off",{icono:"fail",texto:"Error! No se elimino el registro en base de datos",duracion:2500});
						// alert("Error!\nSe elimino el archivo, pero no el registro en base de datos");
					</script>';
			}
		}
		else{
			echo '<script>
					MyLoading2("off",{icono:"fail",texto:"Error! no se elimino el archivo adjunto",duracion:2500});
					// alert("Error!\nNo se Elimino el Archivo Adjunto");
				</script>';
		}

	}

	function mostrarAlmacenamiento(){

		$size       = getFolderSize($_SESSION['ID_HOST'],'../../../');
		$porcentaje = $size*100/$_SESSION['ALMACENAMIENTO'];
		$proporcion = 400*$porcentaje/100;

		$title = "INFORMACION DE ALMACENAMIENTO";

		if ($size >= $_SESSION['ALMACENAMIENTO'] ) {
			$title = "NO HAY ESPACIO DE ALMACENAMIENTO";
		}

		echo '<div class="content-sin-espacio">
			  	  <div class="title-sin-espacio" id="label_almacenamiento">'.$title.'</div>
			  	  <div class="espacio-disponible">
			  	  	<div class="espacio-no-disponible" style="width:'.$proporcion.'">
			  	  	</div>
			  	  </div>
			  	  <div class="content-label">
			  	  	<table class="table-espace">
			  	  		<tr>
			  	  			<td data-color="asignado">&nbsp;</td><td>&nbsp;&nbsp;Espacio Asignado</td><td>'.number_format($_SESSION['ALMACENAMIENTO']).'MB</td>
			  	  		</tr>
			  	  		<tr>
			  	  			<td data-color="ocupado">&nbsp;</td><td>&nbsp;&nbsp;Espacio Ocupado</td><td>'.number_format($size,2).'MB</td>
			  	  		</tr>
			  	  		<tr>
			  	  			<td data-color="disponible">&nbsp;</td><td>&nbsp;&nbsp;Espacio Disponible</td><td>'.number_format( ($_SESSION['ALMACENAMIENTO']-$size),2).'MB</td>
			  	  		</tr>
			  	  	</table>
			  	  </div>
			  </div>';
	}



?>