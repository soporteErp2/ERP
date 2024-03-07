<?php
	// error_reporting('ALL');
	include_once("../../../configuracion/conectar.php");
	include 'Class.depurar.php';
	// header('Content-type: application/vnd.ms-excel');
 //       header("Content-Disposition: attachment; filename=depurar_documentos.xls");
 //       header("Pragma: no-cache");
 //       header("Expires: 0");
	// $id_empresa =  1;
	// $id_empresa = 58;
	// $id_empresa = 48;
	$id_empresa = 47;
	// $id_empresa = 49;
	// $id_empresa = 1003;
	// $id_empresa = 50;
	// $id_empresa = 51;
	// $id_empresa = 52;
	// $id_empresa = 54;
	// $id_empresa = 55;
	// $id_empresa = 56;
	// $id_empresa = 57;
	// $id_empresa = 1002
	$objeto = new depuraDocumentos($mysql,$id_empresa);
	// $objeto->depurar();

?>

<link rel="stylesheet" type="text/css" href="index.css"/>

<button onclick="window.location.reload()">listar Documentos</button>
<button onclick="depurar()">Depurar Documentos</button>

<?php $objeto->muestraDocumentosDepurar(); ?>
<script>
	function depurar() {
		if (confirm('Realmente desea depurar los documentos?')){
			window.location.href="depurar.php?id_empresa=<?php echo $id_empresa ?>";
		}
	}
</script>
