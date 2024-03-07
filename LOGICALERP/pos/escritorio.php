<?php
	include('../../configuracion/conectar.php');
	include('../../configuracion/define_variables.php');

	// VALIDAR LOS PERMISOS DE ACCESO A LAS FUNCIONALIDADDES DEL MODULO
	// 245	9010	2	<b>Pos</b>
	// 246	9020	2	<b>Operacion</b>
	// 251	9025	2	<b>Informes</b>
	$enablePos       = (user_permisos(245,'false') == 'true')? "true" : "false";
	$enableOperation = (user_permisos(246,'false') == 'true')? "true" : "false";
	$enableReports   = (user_permisos(251,'false') == 'true')? "true" : "false";

 ?>
 <html>
 	<head>
 		 <style>
		 	body{
		 		background-color: #FFF;
		 	}
		 </style>
	 	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta http-equiv="last-modified" content="Fri, 22 Jul 2016 12:40:00 GMT" />
		<meta name=viewport content="width=device-width, initial-scale=1">
		<title>LogicalPos</title>

		<!-- <script type="text/javascript" src="../../misc/extjs3/ext-base.js?v4.0.0.12-05-2013"></script> -->
		<!-- LIBRERIA WIN PARA EL FRONT -->
		<link rel="stylesheet" href="../../misc/Win/css/Win.min.css" />
		<link rel="stylesheet" href="../../misc/Win/css/Win-theme-blue.min.css" />
		<script type="text/javascript" src="../..//misc/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="../../misc/Win/js/Win.min.js"></script>

 	</head>
 	<body> </body>
 	<script>
			// logo        : './frontend/temas/logo/siho_icon.png', //190x58
			// logoMin     : './frontend/temas/logo/siho_icon.min.png', //90x58
 		$W.Desk({
			type        : 'dashboard',
			user        : "",
			typeContent : 'iframe',
			//foto      : '',
			logo        : './images/logo.main.png', //190x58
			logoMin     : './frontend/temas/logo/pos_icon.min.png', //90x58
			// contextItems : [
			// 	{
			// 		text    : 'Salir del Aplicativo',
			// 		icon    : 'close',
			// 		handler : function(){logout();}
			// 	}
			// ],
			items:[
				{
					icon   : 'dashboard',
					text   : "Dashboard",
					detail : "Dashboard e Indicadores del sistema",
					enable : <?= $enablePos; ?>,
					url    : "./frontend/POS/dashboard/index.php",
					params : {},
				},
				{
					icon   : 'room_service',
					text   : "POS",
					detail : "Genere las ventas del restaurante",
					enable : <?= $enablePos; ?>,
					url    : "./frontend/POS/pos/index.php",
					params : {},
				},
				{
					icon   : 'business_center',
					text   : "Operacion",
					detail : "Procesos, anulaciones, auditoria",
					enable : <?= $enableOperation; ?>,
					url    : "./frontend/POS/operacion/index.php",
					params : {},
				},
				{
					icon   : 'insert_chart',
					text   : "Informes",
					detail : "Estadisticas, reportes, informacion general",
					enable : <?= $enableReports; ?>,
					url    : "./frontend/POS/informes/index.php",
					params : {},
				},
			],
		});

		document.getElementById('ContentUser').style.display = "none";
 	</script>
 </html>