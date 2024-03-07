<?php
	header('Content-Type: text/html; charset=utf-8');
    // echo $_SESSION['SUCURSAL'];
    // echo $_SESSION['NOMBRESUCURSAL'];
?>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<style>
	html{
		background-color: #6bcdff;
	}

	.modalDialog {
		position           : absolute;
		width              :100%;
		height             :100%;
		font-family        : 'Raleway', sans-serif;
		top                : 0;
		right              : 0;
		bottom             : 0;
		left               : 0;
		background         : rgba(0,0,0,0.8);
		z-index            : 1000000;
		-webkit-transition : opacity 400ms ease-in;
		-moz-transition    : opacity 400ms ease-in;
		transition         : opacity 400ms ease-in;
	}

	.modalDialog > div {
		width                : 400px;
		position             : relative;
		margin               : 10% auto;
		text-align           : center;
		background           : #fff;
		-webkit-transition   : opacity 400ms ease-in;
		-moz-transition      : opacity 400ms ease-in;
		transition           : opacity 400ms ease-in;
	}

	.modalDialog > div > h2 {
		background : #0091cd;
		color      : #FFF;
		text-align : center;
		padding    : 10px;
		font-size  : 18px;
	}

	.modalDialog > div > h2 >span {
		font-size  : 12px;
	}

	.modalDialog > div > div {
		width      : 100%;
		height     : calc(100% - 76px);
		overflow-y : auto;
	}

	.modalDialog > div > p {
		padding   : 5px 20px 13px 20px;
		font-size : 12px;
		text-align: center;
	}

	.modalDialog > div > form > select {
		height  : 35px;
		padding : 8;
	}

	.modalDialog > div > button {
		margin-bottom      : 10px;
		color              : #fff;
		outline            : none;
		padding            : 10px 10px;
		cursor             : pointer;
		font-size          : 12px;
		font-weight        : bold;
		border             : none;
		text-transform     : uppercase;
		transition         : 0.9s all;
		-webkit-transition : 0.9s all;
		-moz-transition    : 0.9s all;
		-o-transition      : 0.9s all;
		-ms-transition     : 0.9s all;
	}

	.modalDialog > div > button[data-value="aceptar"]{
		background-color   : #5dc799;
	}

	.modalDialog > div > button[data-value="cancelar"]{
		background-color   : #d16463;
	}

	.modalDialog:target {
		opacity        : 1;
		pointer-events : auto;
		z-index        : 1000000;
	}

</style>

<div  class="modalDialog" id="openModal">
	<div>
		<h2>Seleccione la Sucursal</h2>
		<form action="escritorio.php" id="form" method="post" >
			<select id="id_sucursal" name="id_sucursal">
				<?php echo $optionSucursal; ?>
			</select>
			<input type="hidden" id="sucursal" name="sucursal">
			<input type="hidden" id="cloud" name="cloud" value="true">
		</form>
		<button data-value="aceptar" onclick="seleccionaSucursal()">Aceptar</button>
	</div>
	<!-- <form action="escritorio.php" id="form" method="post" style="display: none;">
		<input type="text" name="id_sucursal" id="id_sucursal">
		<input type="text" name="sucursal" id="sucursal">
		<input type="text" name="cloud" id="cloud" value="true">
	</form> -->
</div>

<script>
	function seleccionaSucursal(){
		var form     = document.getElementById("form")
		,	sucursal = document.getElementById('id_sucursal')

		// document.getElementById('id_sucursal').value = sucursal.value;
		document.getElementById('sucursal').value = sucursal.options[sucursal.selectedIndex].text;

		// alert(document.getElementById('sucursal').value+" - "+document.getElementById('id_sucursal').value);

		// console.log(sucursal.value+' - '+sucursal.options[sucursal.selectedIndex].text);
		// console.log(document.getElementById('id_sucursal').value+' - '+document.getElementById('sucursal').value);

		form.submit();
	}

</script>

