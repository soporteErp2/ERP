<?php
	include("../../../../../configuracion/conectar.php");
	include("../../../../../configuracion/define_variables.php");

	$id_usuario     = $_SESSION['IDUSUARIO'];
	$nombre_usuario = $_SESSION['NOMBREFUNCIONARIO'];
	$id_empresa     = $_SESSION['EMPRESA'];
	$id_host        = $_SESSION['ID_HOST'];

	if(is_nan($id_empresa)) exit;

	switch ($op) {

		case "consultaSizeImageDocumentInventario":
			consultaSizeImageDocumentInventario($id_host,$nombre);
			break;

		case "ventanaVerImagenDocumentoItems":
			ventanaVerImagenDocumentoItems($nombreImage,$nombreDocumento,$type,$id_host);
			break;

		case "descargarArchivo":
			descargarArchivo($id_host,$nombreDocumento,$nombreRandomico);
			break;

		case "eliminar_archivo":
			eliminar_archivo($id_host,$idArchivo,$nombreRandomico,$link);
			break;

	}

	function consultaSizeImageDocumentInventario($id_host,$nombre){

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/panel_de_control/documentos_items/'.$nombre)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/panel_de_control/documentos_items/'.$nombre;
		}
		else{
			$url = '';
		}

		list($ancho, $alto, $tipo, $atributos) = getimagesize($url);
		$size['ancho'] = $ancho;
		$size['alto']  = $alto;
		$size['url']  = $nombre;

		echo json_encode($size);
	}

	function ventanaVerImagenDocumentoItems($nombreImage,$nombreDocumento,$type,$id_host){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/panel_de_control/documentos_items/'.$nombreImage)) {
			$url = '../../../ARCHIVOS_PROPIOS/empresa_'.$id_host.'/panel_de_control/documentos_items/'.$nombreImage;
		}
		else{
			$url = '';
		}
		if($type != 'pdf'){
			echo'<div style="margin:10px auto 10px auto; width:95%; height:80%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center; overflow:autoscroll; height:90%;">
						<a href="'.$url.'" download="'.$nombreDocumento.'">
							<img src="'.$url.'" id="imagenItems">
						</a>
					</div>
				</div>
				<script>
					document.getElementById("imagenItems").oncontextmenu = function(){ return false; }
				</script>';
		}
		else{
			echo'<div style="margin:10px auto 10px auto; width:95%; height:90%; background-color:#FFF; display:table">
					<div style="display: table-cell; vertical-align: middle; text-align:center;">
						<iframe src="'.$url.'" id="iframeViewDocumentItems"></iframe>
					</div>
				</div>
				<script>
					cambiaViewPdf();
					function cambiaViewPdf(){
						var iframe=document.getElementById("iframeViewDocumentItems");
						iframe.setAttribute("width",Ext.getBody().getWidth()-110);
						iframe.setAttribute("height",Ext.getBody().getHeight()-150);
					}
				</script>';
		}
	}

	function descargarArchivo($id_host,$nombreDocumento,$nombreRandomico){
		$enlace = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/imagenes_empresas/empresa_'.$id_host.'/panel_de_control/documentos_items/'.$nombreRandomico;

		$nombreDocumento = str_replace(' ', '_', $nombreDocumento);
		if (file_exists($enlace)) {
			// header('Content-Disposition: attachment; filename='.basename($nombreDocumento));
			header('Content-Disposition: attachment; filename='.$nombreDocumento);
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: '.filesize($enlace));
		    ob_clean();
		    flush();
		    readfile($enlace);
	    }
	    else{ echo "Error, el archivo no se encuentra almacenado"; }
	    exit;
	}

	function eliminar_archivo($id_host,$idArchivo,$nombreRandomico,$link){
		$sqlDelete   = "DELETE FROM items_documentos WHERE id='$idArchivo'";
		$queryDelete = mysql_query($sqlDelete,$link);

		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/panel_de_control/documentos_items/'.$nombreRandomico)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/panel_de_control/documentos_items/'.$nombreRandomico;
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

?>