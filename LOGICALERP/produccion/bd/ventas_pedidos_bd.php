<?php

		include("../../../configuracion/conectar.php");
		include("../../../configuracion/define_variables.php");
		include("../config_var_global.php");

//function detalleDashboard($bodega,$sucursal,$table,$tabOpcion){

		  //visualizador de datos del dashboard

		  echo 'bodega: '.$bodega.'\n Sucursal: '.$sucursal.'\n tabla: '.$table.' pesta&ntilde;a: '.$tabOpcion;

          $SQL1 = mysql_query("SELECT * FROM ".$tabla."WHERE estado = 1 AND activo = 1 AND id_bodega = '".$filtro_bodega."' AND id_sucursal = '".$filtro_sucursal."'".$agrupa_por,$link);
	      $IND1 = mysql_num_rows($SQL1);
	      //if($IND1 == 0){$IMG1 = 'ok';}else{$IMG1 = 'alert';}

	      switch($tabOpcion){

	      	   case 'Global':
	      	       echo '<div><br>grafico '.$tabOpcion.'de tabla: '.$table.'</div>';
	      	       break;
	      	   case 'Cliente':
	      	       echo '<div><br>grafico '.$tabOpcion.'de tabla: '.$table.'</div>';
	      	       break;
	      	   case 'Vendedor':
	      	       echo '<div><br>grafico '.$tabOpcion.'de tabla: '.$table.'</div>';
	      	       break;
	      	   case 'Centro costo':
	      	       echo '<div><br>grafico '.$tabOpcion.'de tabla: '.$table.'</div>';
	      	       break;
	      	   case 'Pendientes por facturar':
	      	       echo '<div><br>grafico '.$tabOpcion.'de tabla: '.$table.'</div>';
	      	       break;
	      	   case 'Facturadas':
	      	       echo '<div><br>grafico '.$tabOpcion.'de tabla: '.$table.'</div>';
	      	       break;
	      }

	      echo '<div><br>grafico '.$tabOpcion.'de tabla: '.$table.'</div>';

	      echo '



	           	<canvas id="myChart" width="800" height="600"></canvas>
	            <script type="text/javascript" src="../../misc/chartjs/Chart.js"></script>
	       		<script>
					function prueba() {


					// Get the context of the canvas element we want to select
					var ctx = document.getElementById("myChart").getContext("2d");

	               var data = {
					    labels: ["January", "February", "March", "April", "May", "June", "July"],
					    datasets: [
					        {
					            label: "My First dataset",
					            fillColor: "rgba(220,220,220,0.2)",
					            strokeColor: "rgba(220,220,220,1)",
					            pointColor: "rgba(220,220,220,1)",
					            pointStrokeColor: "#fff",
					            pointHighlightFill: "#fff",
					            pointHighlightStroke: "rgba(220,220,220,1)",
					            data: [65, 59, 80, 81, 56, 55, 40]
					        },
					        {
					            label: "My Second dataset",
					            fillColor: "rgba(151,187,205,0.2)",
					            strokeColor: "rgba(151,187,205,1)",
					            pointColor: "rgba(151,187,205,1)",
					            pointStrokeColor: "#fff",
					            pointHighlightFill: "#fff",
					            pointHighlightStroke: "rgba(151,187,205,1)",
					            data: [28, 48, 40, 19, 86, 27, 90]
					        }
					    ]
					};
					   var myLineChart = new Chart(ctx).Line(data);
					 //new Chart(ctx).Line(data);
					//alert("hola");
				}

				setTimeout(function(){
					prueba();
				},1000)
				</script>';



	//}


?>