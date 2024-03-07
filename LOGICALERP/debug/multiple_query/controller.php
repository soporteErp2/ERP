<?php 
	require_once('model.php');
	$synchronize=new Synchronize();

	foreach ($_POST as $key => $value) {
		$$key = $value;
	}
?>
<html>
<head>
	<title>Resultado de la operacion</title>
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
	<style type="text/css">
		body{
			margin-top: 20px;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col">
				<h1>Resultado de ejecucion de script.</h1>
			</div>
		</div>
		<div class="row">
			<div class="col">
<?php 
					if(isset($script) && isset($dataBases))
					{

						if(empty($script) || trim($script)==""){
?>
							<div class="alert alert-warning">
								<span>Debe ingresar el script a ejecutar</span>
							</div>
<?php
							die;
						}

						if(count($dataBases)<1){
?>
							<div class="alert alert-danger">
								<span>Debe seleccionar minimo una base de datos.</span>
							</div>
<?php
							die;
						}

						if(trim($script)!="" && count($dataBases)>0){
							//echo htmlspecialchars($script);
							//var_dump($script);exit();
							$result = $synchronize->runScript(htmlspecialchars($script),$dataBases);
							foreach ($result as $key) {
								foreach ($key as $response => $value) {
									if($response=='error')
									{
										echo '<div class="alert alert-danger">
											<span>'.$value.'</span>
										</div>';
									}else{
										echo '<div class="alert alert-success">
											<span>'.$value.'</span>
										</div>';
									}
								}
							}
						}
					}else{
?>
						<div class="alert alert-danger">
							<span>No se ha recibido informacion para procesar.</span>
						</div>
<?php
					}
				?>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<a href="index.php">Volver</a>
			</div>
		</div>
	</div>
</body>
</html>