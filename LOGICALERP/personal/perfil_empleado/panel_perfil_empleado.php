<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');
?>
<link rel="stylesheet" type="text/css" href="perfil_empleado/index.css">
<div class="content">
	<div class="separator"><?php echo $nombre; ?> <div class="close" onclick="Win_Ventana_perfil_empleado.close();"></div></div>

	<!-- <div class="box"></div> -->

	<!-- <div class="separator">Datos Personales</div>
	<div class="separator">Perfil Profesional</div>
	<div class="separator">Experiencias Profesional</div>
	<div class="separator">Formacion</div>
	<div class="separator">Competencias y Habilidades</div> -->

	<div class="content-tab">
		<div id="tab1" onclick="tab_event('panel1', this);">DATOS PERSONALES</div>
		<div id="tab2" onclick="tab_event('panel2', this);">INFO. FAMILIAR Y DE CONTACTO</div>
		<div id="tab3" onclick="tab_event('panel3', this);">INFO. ACADEMICA</div>
		<div id="tab4" onclick="tab_event('panel4', this);">EXPERIENCIA LABORAL</div>
	</div>
	<div class="content-tab-content">
		<div id="panel1" class="tab-content"><?php include 'datos_personales/datos_personales.php'; ?></div>
		<div id="panel2" class="tab-content"><?php include 'informacion_familiar/informacion_familiar.php'; ?></div>
		<div id="panel3" class="tab-content"><?php include 'informacion_academica/informacion_academica.php'; ?></div>
		<div id="panel4" class="tab-content"><?php include 'experiencia_laboral/experiencia_laboral.php'; ?></div>
	</div>
</div>
<div id="loadForm" style="display:none;"></div>
<script>
	// INICIAR MOSTRANDO EL PRIMER TAB
	tab_event('panel1', document.getElementById('tab1'));

	// FUNCION DE LOS TABS
	function tab_event(idPanel, tab){
		var arrayPanel = document.querySelectorAll('.tab-content');
		[].forEach.call(arrayPanel, function(objDom) {
			objDom.style.display         = "none";
			objDom.style.backgroundColor = "none";
		});

		var arrayTabs = document.querySelectorAll('.content-tab > div');
		[].forEach.call(arrayTabs, function(objDom) {
			// objDom.style.margin = "2px 1px 0 1px";
			objDom.style.marginTop       = "3px";
			objDom.style.borderBottom    = "1px solid #2A80B9";
			objDom.style.backgroundColor = "#80C3EF";
			objDom.style.color           = "#FFF";
		});

		document.getElementById(idPanel).style.display = "block";
		// tab.style.margin = "3px 1px 0 1px";
		tab.style.marginTop       = "4px";
		tab.style.borderBottom    = "none";
		tab.style.backgroundColor = "#FFF";
		tab.style.color           = "#000";
	}

	function validate_int(Input) {
        var patron = /[^\d]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

	}

</script>