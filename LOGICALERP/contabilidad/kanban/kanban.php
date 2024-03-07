<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$archivoGit = 'C:\PROYECTOS/'.$proyecto.'/.git/logs/HEAD';
	if(!file_exists($archivoGit)){ echo 'No se ha encontrado el archivo '.$archivoGit.''; exit; }

	$arrayFilasArchivo = file($archivoGit);
	$contFilasArchivo  = COUNT($arrayFilasArchivo) - 1;

	$tareaDone = '';

	for ($fila = $contFilasArchivo; $fila >= 0; $fila--) {
		if(strpos($arrayFilasArchivo[$fila], 'commit') === false /*|| strpos($arrayFilasArchivo[$fila], '[kanban]') === false*/) continue;

		list($dataGit, $commit) = explode('commit (merge): ', $arrayFilasArchivo[$fila]);
		if($commit == '') list($dataGit, $commit) = explode('commit: ', $arrayFilasArchivo[$fila]);

		$arrayData = explode(' ', $dataGit);
		$contData  = COUNT($arrayData) - 2;

		$dateTime = date("d/m/Y H:i", $arrayData[$contData]);
		$tareaDone .= '<div>
							<div>
								<div style="width:calc(100% - 55px); float:left;">'.$dateTime.'</div>
								<div style="width:25px; float:left;"><img src="kanban/img/git.png" style="width:23px; height:25px;"/></div>
								<div style="width:25px; float:left; margin-left:2px; padding-top:3px;"><img src="kanban/img/calendar.png" style="width:16px; height:21px;"/></div>
							</div>
							<div>'.$commit.'</div>
						</div>';
	}


	// $descriptorspec = array(
	// 	1 => array('pipe', 'r'),
	// 	2 => array('pipe', 'r'),
	// );
	// $env   = array();
	// $pipes = array();

	$cwd = 'C:\Program Files (x86)\Git\bin\git.exe';
	$resource = proc_open('git log', $descriptorspec, $pipes, $cwd, $env);
	// if(!$resource){ echo 'false'; }
	// else{ echo 'true'; }
	// print_r($cwd);
	// "C:\Program Files (x86)\Git\bin\sh.exe" "--login -i"
	// echo system('"C:\Program Files (x86)\Git\bin\git.exe" "--version"',$response);
	// echo system('"cd C:\PROYECTOS\LOGICALERP" "git --version"');
	// echo $response;
	// echo system('git log');

?>

<script src="kanban/jquery-ui/jquery-ui.js"></script>

<div id="table_kanban">
	<div>
		<div  class="title_kanban">
			<div>PENDIENTES</div>
			<div onclick="ventanaNuevaActividad('pendiente');">+</div>
		</div>
		<div id="pendientes" class="act actPendientes connectWith">
			<div id="act_1">
				<div>
					<div style="width:calc(100% - 55px); float:left;">'.$dateTime.'</div>
					<div style="width:25px; float:left;"><img src="kanban/img/git.png" style="width:23px; height:25px;"/></div>
					<div style="width:25px; float:left; margin-left:2px; padding-top:3px;"><img src="kanban/img/calendar.png" style="width:16px; height:21px;"/></div>
				</div>
				<div>'.$commit.'</div>
			</div>

			<div id="act_2">
				<div>
					<div style="width:calc(100% - 55px); float:left;">'.$dateTime.'</div>
					<div style="width:25px; float:left;"><img src="kanban/img/git.png" style="width:23px; height:25px;"/></div>
					<div style="width:25px; float:left; margin-left:2px; padding-top:3px;"><img src="kanban/img/calendar.png" style="width:16px; height:21px;"/></div>
				</div>
				<div>'.$commit.'</div>
			</div>

			<div id="act_3">
				<div>
					<div style="width:calc(100% - 55px); float:left;">'.$dateTime.'</div>
					<div style="width:25px; float:left;"><img src="kanban/img/git.png" style="width:23px; height:25px;"/></div>
					<div style="width:25px; float:left; margin-left:2px; padding-top:3px;"><img src="kanban/img/calendar.png" style="width:16px; height:21px;"/></div>
				</div>
				<div>'.$commit.'</div>
			</div>


		</div>
	</div>
	<div>
		<div class="title_kanban">
			<div>EN PROCESO</div>
			<div onclick="ventanaNuevaActividad('proceso');">+</div>
		</div>
		<div id="en_proceso" class="act actProceso connectWith"></div>
	</div>
	<div>
		<div class="title_kanban">
			<div>REALIZADOS</div>
			<div onclick="ventanaNuevaActividad('realizada');">+</div>
		</div>
		<div id="realizados" class="act actRealizadas connectWith"><?php echo $tareaDone ?></div>
	</div>
</div>
<script>
// console.log($( "#pendientes, #en_proceso, #realizados" ).sortable);

	destino = '';
	origen  = '';

  	$(function() {
	  	setTimeout(function() {
	      $( "#en_proceso, #pendientes, #realizados" ).sortable({
	      connectWith: ".connectWith",
	      opacity: 0.5,
	      // containment: 'parent',
	      receive: function( event, ui ) {}
	    }).disableSelection();
		}, 1000);

  	});

  $( "#en_proceso, #pendientes, #realizados" ).on( "sortreceive", function( event, ui ) {
																							 origen = ui.sender[0].id;
																							 destino = ui.item[0].parentNode.id;
  																						} );
  $( "#en_proceso, #pendientes, #realizados" ).on( "sortstop", function( event, ui ) {
  																							if (ui.item[0].parentNode.id!=destino) {reorganizarLista(ui.item[0].parentNode.id);}
  																							else{cambiarItemLista(origen,destino) }
																							destino = '';	origen  = '';
  																						} );

  function reorganizarLista(id_lista) {
  	var divs = document.getElementById(id_lista).querySelectorAll('.ui-sortable-handle');
  	// console.log(divs);
  	if (divs.length <1) {return;}
  	var json=[];
  	for (var i =0;i< divs.length ; i++) {
  		json[i]=divs[i].id;
  		// console.log(divs[i]);
  	};

 //  	json.forEach(function(valor,indice) {
 //    	console.log(indice+' - '+valor);
	// });

	json=JSON.stringify(json);
	console.log(json);
	return json;
  }

  function cambiarItemLista(origen,destino) {
  	// console.log(origen+' - '+destino);
  	json=reorganizarLista(destino);
  	// console.log(json);
  }

</script>
<style type="text/css">

	#contenedor_kanban{
		height : 100%;
	}

	#table_kanban{
		width    : 100%;
		height   : 100%;
		overflow : hidden;
	}

	#table_kanban > div{
		float    : left;
		width    : 31%;
		height   : 95%;
		margin   : 1%;
		border   : 1px solid #333;
		overflow : hidden;
	}

	.title_kanban{
		color            : #FFF;
		text-align       : center;
		background-color : #333;
		height           : 20px;
		padding-top      : 5px;
		font-weight      : bold;
	}

	.title_kanban > div{
		float    : left;
		overflow : hidden;
	}

	.title_kanban > div:nth-child(1){
		width: 100%;
	}

	.title_kanban > div:nth-child(2){
		margin-left: -20px;
		font-size: 20px;
		margin-top: -6px;
	}

	.act{
		float : left;
		width : 100%;
		height: calc(100% - 25px);
		overflow-x : hidden;
		overflow-y : auto;
	}

	.act > div{
		float       : left;
		width       : calc(100% - 10px);
		text-indent : 5px;
		overflow    : hidden;
		min-height  : 50px;
		margin      : 3px;
	}

	.act > div > div{
		overflow : hidden;
	}

	.actRealizadas > div, .actPendientes > div, .actProceso > div{
		border           : 2px solid #7DBB00;
		background-color : #DCFFC2;
	}

	.actRealizadas > div > div:nth-child(1), .actPendientes > div > div:nth-child(1), .actProceso > div > div:nth-child(1){
		border-bottom : 1px dashed #7DBB00;
		min-height    : 18px;
		font-weight   : bold;
	}
</style>

