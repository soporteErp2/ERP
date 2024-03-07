<?php
	require_once('model.php');
	$synchronize=new Synchronize();
	$dataBases=$synchronize->getDataBases();
?>

<html>
<head>
	<title>Sincronizacion de Base de Datos 2.0</title>
	<link rel="stylesheet" type="text/css" href="bootstrap.min.css">
	<style type="text/css">
		body{
			margin-top: 20px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1 class="text-center">
			Sincronizar multiples Bases de Datos
		</h1>
		<div class="row">
			<div class="col">&nbsp;</div>
		</div>
		<div class="form-group">
			<form action="controller.php" method="POST" accept-charset="utf-8">
				<div class="row">
					<div class="col-3">
						<span>
							<strong>Script a ejecutar</strong>
						</span>
					</div>
					<div class="col">
						<p>Si se desea ejecutar mas de un script, debe tener al final del query el punto y coma {.}, por ejemplo:<br/>
							<code>ALTER TABLE permisos ADD estado integer(5) NOT NULL;</code><br/>
							<code>ALTER TABLE usuarios ADD prueba integer(10) NULL;</code>
						</p>
						<p>
							Recuerde que no debe especificar la base de datos en el query a ejecutar, por ejemplo:<br/>
							<strong>Incorrecto: </strong><code>ALTER TABLE asiste.usuarios ADD estado integer(5) NOT NULL;</code><br/>
							<strong>Correcto: </strong><code>ALTER TABLE usuarios ADD estado integer(5) NOT NULL;</code><br/>
						</p>
						<textarea name="script" class="form-control" rows="5" cols="15" autofocus placeholder="ALTER TABLE usuarios ADD activo integer(10);" required></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-3">
						<span>
							<strong>Selecciones las base de datos:</strong>
						</span>
					</div>
					<div class="col-3">
						<?php
						$i=0;
							foreach ($dataBases as $key) {
								$i++;
						?>
							<input type="checkbox" name="dataBases[]" id="<?php echo $i; ?>" value="<?php echo $key['bd']; ?>" class="form-check-input">
							<label for="<?php echo $i; ?>" class="form-check-label"><?php echo $key['bd']; ?></label><br/>
						<?php
							}
						?>
					</div>
				</div>
				<div class="row">
					<div class="col text-center">
						<button type="submit" class="btn btn-primary">Sincronizar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
