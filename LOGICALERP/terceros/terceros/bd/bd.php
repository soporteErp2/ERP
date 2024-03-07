<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include("../../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$id_host    = $_SESSION['ID_HOST'];

	switch ($opc) {
		case "downloadFile":
			downloadFile($nameFile,$id_empresa);
			break;

		case "ventana_tercerosPDF":
			ventana_tercerosPDF($data);
			break;

		case 'eliminarArchivosTemporales':
			eliminarArchivosTemporales($id_host,$files);
			break;



		default:
			# code...
			break;
	}

	function downloadFile($nameFile,$id_empresa){
		$enlace = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/archivos_erp/formatos_upload_terceros/'.$nameFile;

		if (file_exists($enlace)) {
			//header('Content-Disposition: attachment; filename='.basename($nameFile));
			header('Content-Disposition: attachment; filename='.$nameFile);
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

	function ventana_tercerosPDF($data){
		echo '<iframe style="width:100%; height:100%;" src="../terceros/terceros/reporte_terceros.php?IMPRIME_PDF=true&'.$data.'"></iframe>';

	}

		// ELIMINAR LOS ARCHIVOS TEMPORALES CREADOS PARA ENVIAR DOCUMENTOS POR CORREO ELECTRONICO
	function eliminarArchivosTemporales($id_host,$files){
		$serv = $_SERVER['DOCUMENT_ROOT']."/";
        $url  = $serv.'ARCHIVOS_PROPIOS/empresa_'.$id_host.'/terceros/adjuntos_terceros/';
        if($files != ''){
        	$files = explode(',',$files);

        	for($i=0;$i<count($files);$i++){   	

			    if (is_file($url.$files[$i])){
					unlink($url.$files[$i]);
				}	
			}
        }        
	}

?>