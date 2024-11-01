<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	$sql = "SELECT id,data FROM configuracion_general 
		WHERE activo=1 AND id_empresa=$id_empresa AND modulo='panel_de_control' AND descripcion='envio automatico de FV electronica' ";
	$query = mysql_query($sql,$link);
	if (mysql_result($query,0,'id')>0) {
		$data = json_decode(mysql_result($query,0,'data'));
	}
	if ($data[0]) {
		$toggle = ($data[0]->is_active=="true")? "bg-blue-400" : "";
		$toggleCircle = ($data[0]->is_active=="true")? "translate-x-11" : "";
		$toggle_label = ($data[0]->is_active=="true")? "Desactivar" : "Activar";
	}
?>

<div class="w-full bg-white h-full">
	<div class="p-5 text-sm font-medium ">
		Si activa esta opcion las facturas electronicas se enviaran automaticamente a la Dian inmediatamente se generen
	</div>	
	<div class="flex justify-center flex-col items-center">
		<div class="bg-gray-200 rounded-2xl w-20 h-8 cursor-pointer p-1 transition-all <?=$toggle?> " id="toggle">
			<div class="rounded-full h-full w-6 bg-white transition-all <?=$toggleCircle?>" id="toggle-circle" ></div>
		</div>
		<span id="toggle-label"><?=$toggle_label?></span>
	</div>
</div>

<script>
	let toggle = document.getElementById('toggle');
	let toggleCircle = document.getElementById('toggle-circle');
	let toggle_label = document.getElementById('toggle-label')

	toggle.addEventListener('click', () => {
		toggleCircle.classList.toggle('translate-x-11');
		if (toggleCircle.classList.contains('translate-x-11')) {
			toggle.classList.toggle('bg-blue-400');
			toggle_label.innerHTML = 'Desactivar';
			toggle_handler(true)
		} else {
			toggle.classList.toggle('bg-blue-400');
			toggle_label.innerHTML = 'Activar';
			toggle_handler(false)
		}
	});

	async function toggle_handler(is_active) {
		try {
			// Realiza la solicitud y espera la respuesta
			const response = await fetch(`envio_facturas/bd/bd.php?opc=set_automatic_sending&is_active=${is_active}`);

			// Verifica si la respuesta es exitosa (status 200-299)
			if (!response.ok) {
				throw new Error('Network response was not ok ' + response.statusText);
			}

			// Espera a que la respuesta se convierta a JSON
			const data = await response.json();

			// Retorna los datos obtenidos
			console.log(data)
			return data;
		} catch (error) {
			console.error('Error fetching data:', error);
		}
	}

</script>