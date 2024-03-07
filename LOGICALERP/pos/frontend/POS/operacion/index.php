<?php
	include('../../../../../configuracion/conectar.php');
	$arrayEmpresa = explode("-", $_SESSION['NITEMPRESA']);
    // if(!session_start()){session_start();}
	// print_r($_SESSION[PERMISOS]);
	// 247	9021	3	Anular Comandas
	// 248	9022	3	Anular Facturas
	// 249	9023	3	Precierre
	// 250	9024	3	Cierre
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>

	<!-- Win Styles-->
	<!-- <link rel="stylesheet" href="../../misc/Win/css/Win-grilla.min.css" /> -->
	<link rel="stylesheet" href="../../../../../misc/Win/css/Win.min.css" />
	<link rel="stylesheet" href="../../../../../misc/Win/css/Win-theme-blue.min.css" />

	<!-- Estilos globales de la app -->
	<link rel="stylesheet" type="text/css" href="../../temas/estilo.css">

	<!-- jQuery -->
	<script type="text/javascript" src="../../../../../misc/jquery/jquery.min.js"></script>

	<!-- CKEditor -->
	<!-- <script type="text/javascript" src="../../misc/ckeditor/ckeditor.js"></script> -->
	<!-- <script src="https://cdn.ckeditor.com/ckeditor5/11.1.1/decoupled-document/ckeditor.js"></script> -->

	<!-- Win.js -->
	<script type="text/javascript" src="../../../../../misc/Win/js/Win.min.js"></script>
	<script>
		const authToken = `<?= $_SESSION['api_token']; ?>`;
		const authBD    = `<?= $_SESSION['api_bd']; ?>`;
	</script>

	<style>
		.panelColorIcons{
			color:#37474f !important;
		}
	</style>
</head>
<body>

	<div class="desktopContainer">

		<div class="form_separador">OPERACION</div>
		<?php if(user_permisos(247,'false') == 'true'){ ?>
			<div class="IconoPanelControl" id="wAnularComandas">
				<div class="IconoPanelControlimg">
					<i class="material-icons panelColorIcons">layers_clear</i>
				</div>
				<div class="IconoPanelControltxt myIconstyle">Anular Comandas</div>
			</div>
		<?php }
			if(user_permisos(248,'false') == 'true'){
		?>
			<div class="IconoPanelControl" id="wAnularFacturas">
				<div class="IconoPanelControlimg">
					<i class="material-icons panelColorIcons">money_off</i>
				</div>
				<div class="IconoPanelControltxt myIconstyle">Anular Facturas</div>
			</div>
		<?php } ?>
		<div class="IconoPanelControl" id="wCambioClave">
			<div class="IconoPanelControlimg">
				<i class="material-icons panelColorIcons">account_box</i>
			</div>
			<div class="IconoPanelControltxt myIconstyle">Cambio Pin</div>
		</div>

		<div class="form_separador">AUDITORIA</div>
		<?php if(user_permisos(249,'false') == 'true'){ ?>
			<div class="IconoPanelControl" id="wPrecierre">
				<div class="IconoPanelControlimg">
					<i class="material-icons panelColorIcons" >lock_open</i>
				</div>
				<div class="IconoPanelControltxt myIconstyle">Precierre</div>
			</div>
		<?php }
			if(user_permisos(250,'false') == 'true'){
		?>
			<div class="IconoPanelControl" id="wCierre">
				<div class="IconoPanelControlimg">
					<i class="material-icons panelColorIcons" >lock</i>
				</div>
				<div class="IconoPanelControltxt myIconstyle">Cierre</div>
			</div>
		<?php } ?>
	</div>

	<script type="text/javascript">

		/*INFORMACION DE LA USER*/
		$W("#wAnularComandas").on("click", function(){ wGlobal({title:'Anular Comandas',width:'calc(100% - 50px)',height:'calc(100% - 50px)',url:'anular_comandas/index.php'}); }); // Add event click
		$W("#wAnularFacturas").on("click", function(){ wGlobal({title:'Anular Facturas',width:'calc(100% - 50px)',height:'calc(100% - 50px)',url:'anular_facturas/index.php'}); }); // Add event click
		$W("#wCambioClave").on("click", function(){ wGlobal({title:'Cambiar Pin',width:'200',height:'200',url:'cambio_pin/index.php'}); }); // Add event click
		$W("#wPrecierre").on("click", function(){ wGlobal({title:'Precierre Pos',width:'calc(100% - 50px)',height:'calc(100% - 50px)',url:'precierre/index.php'}); }); // Add event click
		$W("#wCierre").on("click", function(){ wGlobal({title:'Cierre de Auditoria POS',width:'calc(100% - 50px)',height:'calc(100% - 50px)',url:'cierre/index.php'}); }); // Add event click

		function wGlobal(params){
			WinGlobal = new $W.Window({
				id          : "wGlobal",
				title       : `${params.title}`,
				// width       : "calc(100% - 50px)",
				width       : `${params.width}`,
				height      : `${params.height}`,
				scrollY 	: true,
				modal       : true,
				closable    : true,
				drag        : true,
				resize      : true,
				autoLoad    :
				{
					url     : `${params.url}`,
					params  : {}
				},
			});
		}

		var prinTDoc = (id_documento) =>{
			window.open(`http://logicalsoft-erp.com/LOGICALERP/pos/backend/pos/Controller.php?method=printTiquet&id_documento=${id_documento}&nit=<?= $arrayEmpresa[0] ?>`);
		}

		var prinTComanda = (id_documento) =>{
			window.open(`http://logicalsoft-erp.com/LOGICALERP/pos/backend/pos/Controller.php?method=printComanda&id_comanda=${id_documento}&nit=<?= $arrayEmpresa[0] ?>`);
		}
	</script>
</body>
</html>