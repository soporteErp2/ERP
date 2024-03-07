<?php
    if(!session_start()){session_start();}
	$arrayEmpresa = explode("-", $_SESSION['NITEMPRESA']);
	// echo $arrayEmpresa[0];

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
	</script>

	<style>
		.panelColorIcons{
			color:#37474f !important;
		}
		.parentPosAccess{
			width: 100%;
			height: 100%;
		}
		.parentPosAccess div{
			position         : absolute;
			top              : 0;
			bottom           : 0;
			left             : 0;
			right            : 0;
			width            : 300px;
			height           : 30%;
			margin           : auto;
			text-align       : center;
			color            : #FFF;
			font-weight      : bold;
			background-color : #2a3f54;
			padding          : 10px;
			cursor           : pointer;
		}

	</style>
</head>
<body>

	<div class="desktopContainer">
		<div class="parentPosAccess">
			<div onclick="openPos()">
				<i class="material-icons" style="font-size: 70px;width: 100%;">room_service</i>
				INGRESAR AL MODULO DE VENTAS POS
			</div>
		</div>

	</div>

	<script type="text/javascript">
		var openPos = () => {

			$W.Loading();
			$W.Ajax({
				url    : "../../../backend/pos_admin/Controller.php",
				params :  {
					method :"getToken",
				},
				timeout : 2000,
				success : function(result,xhr){
					// console.log(result.responseText); //lee respuesta como texto
					// console.log(JSON.parse(result.responseText)); //lee respuesta como json
					let response = JSON.parse(result.responseText);
					if (response.status == 'success'){
						// ENCODAR 10 VECES EN BASE 64 LAS VARIABLES
						var params = btoa(`token=${response.token}&nit=<?= $arrayEmpresa[0] ?>&id_sucursal=<?= $_SESSION['SUCURSAL'] ?>`)
						// for (var i = 0; i >= 8; i++) {
						// 	params = btoa(params);
						// }
						window.open(`index.html?${params}`, '_blank');
						// alert(`success ${response.token}`);
					}
					else{ alert(response.message) }
					$W.Loading();
				},
				failure : function(xhr){
					console.log("fail");
					$W.Loading();
				}
			})

			// window.open(theURL, '', 'fullscreen=yes, scrollbars=auto');
		}
	</script>
</body>
</html>