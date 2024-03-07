<?php
	include_once("../../../configuracion/conectar.php");
	include 'Class.depurar.php';
	// header('Content-type: application/vnd.ms-excel');
 //       header("Content-Disposition: attachment; filename=depurar_documentos.xls");
 //       header("Pragma: no-cache");
 //       header("Expires: 0");

	$objeto = new depuraDocumentos($mysql,$_GET['id_empresa']);

?>

<link rel="stylesheet" type="text/css" href="index.css"/>

<button onclick="retorna()">Regresar</button>

<?php $objeto->depurar(); ?>

<script>
	function retorna() {
		window.location.href="index.php";
	}
</script>