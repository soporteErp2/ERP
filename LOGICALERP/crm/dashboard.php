<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");

	echo '<script>var periodo = "'.$periodo.'"</script>';

	$hoy = date("Y-m-d");
	$diaMes = date("j");
	$diaSemana = date("N");
	$numeroDiasMes = date("t");

	if($periodo == 'mes'){
		$Titulo = 'Indicadores del Mes';
		$resta  = $diaMes - 1;
		$fechai = date("Y-m-d",strtotime ( '-'.$resta.' day',strtotime($hoy)));
		$suma   = $numeroDiasMes - $diaMes;
		$fechaf = date("Y-m-d",strtotime ( '+'.$suma.' day',strtotime($hoy)));
		$rango = fecha_larga($fechai).'&nbsp;&nbsp; al &nbsp;&nbsp;'.fecha_larga($fechaf);
	}
	if($periodo == 'semana'){
		$Titulo = 'Indicadores de la Semana';
		$resta  = $diaSemana - 1;
		$fechai = date("Y-m-d",strtotime ( '-'.$resta.' day',strtotime($hoy)));
		$suma   = 7 - $diaSemana;
		$fechaf = date("Y-m-d",strtotime ( '+'.$suma.' day',strtotime($hoy)));
		$rango = fecha_larga($fechai).'&nbsp;&nbsp; al &nbsp;&nbsp;'.fecha_larga($fechaf);
	}
	if($periodo == 'dia'){
		$Titulo = 'Indicadores del D&iacute;a';
		$fechai = $hoy;
		$fechaf = $hoy;
		$rango = fecha_larga($fechai);
	}

	//CONSULTA DE PROSPECTOS/////////////////////////////////////////////////////////////////////////////////////
	$SQL1 = "SELECT
				terceros.id,
				terceros.nombre,
				terceros.nombre_comercial
			FROM
				terceros_asignados
			INNER JOIN terceros ON terceros_asignados.id_tercero = terceros.id
			WHERE
				terceros.id_tipo_identificacion = 0 AND
				terceros_asignados.id_asignado = $_SESSION[IDUSUARIO] AND
				terceros.fecha_creacion BETWEEN '$fechai' AND '$fechaf'
			GROUP BY terceros.id";
	$consul1 = $GLOBALS['mysql']->query($SQL1,$link);

	//CONSULTA DE ACTIVIDADES//////////////////////////////////////////////////////////////////////////////////////////////
	$SQL2 = "SELECT
				crm_objetivos_actividades.id
			FROM
				crm_objetivos_actividades
				LEFT JOIN crm_objetivos_actividades_personas ON crm_objetivos_actividades.id = crm_objetivos_actividades_personas.id_actividad
			WHERE
				(crm_objetivos_actividades.id_asignado = $_SESSION[IDUSUARIO] OR
				crm_objetivos_actividades_personas.id_asignado = $_SESSION[IDUSUARIO]) AND
				crm_objetivos_actividades.fechai BETWEEN '$fechai' AND '$fechaf'
				";

	$consul2 = $GLOBALS['mysql']->query($SQL2,$link);

	//CONSULTA DE ACTIVIDADES FINALIZADAS/////////////////////////////////////////////////////////////////////////////////////
	$SQL3 = "SELECT
				crm_objetivos_actividades.id
			FROM
				crm_objetivos_actividades
				LEFT JOIN crm_objetivos_actividades_personas ON crm_objetivos_actividades.id = crm_objetivos_actividades_personas.id_actividad
			WHERE
				(crm_objetivos_actividades.id_asignado = $_SESSION[IDUSUARIO] OR
				crm_objetivos_actividades_personas.id_asignado = $_SESSION[IDUSUARIO]) AND
				crm_objetivos_actividades.fechai BETWEEN '$fechai' AND '$fechaf' AND
				crm_objetivos_actividades.estado = 1
				";
	$consul3 = $GLOBALS['mysql']->query($SQL3,$link);

	//CONSULTA DE ACTIVIDADES PENDIENTES/////////////////////////////////////////////////////////////////////////////////////
	$SQL4 = "SELECT
				crm_objetivos_actividades.id
			FROM
				crm_objetivos_actividades
				LEFT JOIN crm_objetivos_actividades_personas ON crm_objetivos_actividades.id = crm_objetivos_actividades_personas.id_actividad
			WHERE
				(crm_objetivos_actividades.id_asignado = $_SESSION[IDUSUARIO] OR
				crm_objetivos_actividades_personas.id_asignado = $_SESSION[IDUSUARIO]) AND
				crm_objetivos_actividades.fechai BETWEEN '$fechai' AND '$fechaf' AND
				crm_objetivos_actividades.estado = 0
				";
	$consul4 = $GLOBALS['mysql']->query($SQL4,$link);

	//CONSULTA DE VISITAS REALIZADAS  /////////////////////////////////////////////////////////////////////////////////////
	$SQL5 = "SELECT
				crm_objetivos_actividades.id
			FROM
				crm_objetivos_actividades
				LEFT JOIN crm_objetivos_actividades_personas ON crm_objetivos_actividades.id = crm_objetivos_actividades_personas.id_actividad
				INNER JOIN crm_configuracion_actividades ON crm_objetivos_actividades.tipo = crm_configuracion_actividades.id
			WHERE
				(crm_objetivos_actividades.id_asignado = $_SESSION[IDUSUARIO] OR
				crm_objetivos_actividades_personas.id_asignado = $_SESSION[IDUSUARIO]) AND
				crm_objetivos_actividades.fechai BETWEEN '$fechai' AND '$fechaf' AND
				crm_objetivos_actividades.estado = 1 AND
				crm_configuracion_actividades.genera_visita = 'true'

				";
	$consul5 = $GLOBALS['mysql']->query($SQL5,$link);

	//CONSULTA DE LLAMADAS REALIZADAS  /////////////////////////////////////////////////////////////////////////////////////
	$SQL6 = "SELECT
				crm_objetivos_actividades.id
			FROM
				crm_objetivos_actividades
				LEFT JOIN crm_objetivos_actividades_personas ON crm_objetivos_actividades.id = crm_objetivos_actividades_personas.id_actividad
				INNER JOIN crm_configuracion_actividades ON crm_objetivos_actividades.tipo = crm_configuracion_actividades.id
			WHERE
				(crm_objetivos_actividades.id_asignado = $_SESSION[IDUSUARIO] OR
				crm_objetivos_actividades_personas.id_asignado = $_SESSION[IDUSUARIO]) AND
				crm_objetivos_actividades.fechai BETWEEN '$fechai' AND '$fechaf' AND
				crm_objetivos_actividades.estado = 1 AND
				crm_configuracion_actividades.genera_llamada = 'true'
				";
	$consul6 = $GLOBALS['mysql']->query($SQL6,$link);



?>

    <div style="width:100%; box-shadow:0 4px 6px rgba(0,0,0,.5); height:60px; padding:10px 30px 5px 30px; margin:0; font-size:30px; background:<?php echo $_SESSION["COLOR_CONTRASTE"] ?>">
        <div class="dashboardNext" style="float:left; width:48px; height:48px; margin:5px 10px 0 0; cursor:pointer" onClick="CambiaPeriodo()"></div>
        <div style="float:left; width:400px; cursor:pointer" onClick="CambiaPeriodo()">
          <?php echo $Titulo;?><br/>
          <span style="font-size:11px;"><?php echo $rango ?></span>
        </div>
        <div class="reload48" style="float:right; width:50px; height:48px; margin:5px 40px 0 0; cursor:pointer" onClick="RecargaPeriodo()"></div>
    </div>
    <div class="MainDashContent MainDashContentCRM">

        <div class="DashContenedorPerson">
            <div class="DashFotoPerson"><img alt="." src="/foto_generador.php?ID=<?php echo $_SESSION['IDUSUARIO'] ?>" /></div>
        </div>



        <div class="DashContenedor" style="margin-left:20px;">
            <div class="DashPersonIcono" style="background-image:url(images/user_help.png?2)"></div>
            <div id="" class="DashIndicador DashIndiOk"><?php echo $GLOBALS['mysql']->num_rows($consul1)?></div>
            <div class = "DashLabelTitle">Prospectos Creados en el periodo</div>
            <!--<div class = "DashLabelDat">Total Covertidos en Clientes 0 <br /> Efectividad 0%</div>-->
        </div>

        <!--<div class="DashContenedor" style="margin-left:20px;">
            <div id="" class="DashIndicador DashIndiOk"><?php echo '0'?></div>
            <div class = "DashLabelTitle">Clientes Asignados</div>
            <div class = "DashLabelDat"></div>
        </div>-->

        <div class="DashContenedor">
            <div class="DashPersonIcono" style="background-image:url(images/list_checkmark.png)"></div>
            <div id="" class="DashIndicador DashIndiOk"><?php echo $GLOBALS['mysql']->num_rows($consul3)?></div>
            <div id="chart1" class="DashPieChart" style="width:120px; height:100px; float:left"></div>
            <div class = "DashLabelTitle">Actividades Finalizadas</div>
            <div class = "DashLabelDat">(<?php echo $GLOBALS['mysql']->num_rows($consul2)?>) Actividades en Total</div>
        </div>

        <div class="DashContenedor">
            <div class="DashPersonIcono" style="background-image:url(images/list_info.png)"></div>
            <div id="" class="DashIndicador DashIndiOk"><?php echo $GLOBALS['mysql']->num_rows($consul4)?></div>
            <div id="chart2" class="DashPieChart" style="width:120px; height:100px; float:left"></div>
            <div class = "DashLabelTitle">Actividades Pendientes</div>
            <div class = "DashLabelDat">(<?php echo $GLOBALS['mysql']->num_rows($consul2)?>) Actividades en Total</div>
        </div>

        <div class="DashContenedor">
            <div class="DashPersonIcono" style="background-image:url(images/briefcase_checkmark.png)"></div>
            <div id="" class="DashIndicador DashIndiOk"><?php echo $GLOBALS['mysql']->num_rows($consul5)?></div>
            <div class = "DashLabelTitle">Visitas Realizadas</div>
            <div class = "DashLabelDat"></div>
        </div>

        <div class="DashContenedor">
            <div class="DashPersonIcono" style="background-image:url(images/phone_checkmark.png)"></div>
            <div id="" class="DashIndicador DashIndiOk"><?php echo $GLOBALS['mysql']->num_rows($consul6)?></div>
            <div class = "DashLabelTitle">LLamadas Realizadas</div>
            <div class = "DashLabelDat"></div>
        </div>



    </div>

<script>

	var calc1 = eval(<?php echo $GLOBALS['mysql']->num_rows($consul2) ?> - <?php echo $GLOBALS['mysql']->num_rows($consul3) ?>);
	new google.visualization.PieChart(document.getElementById('chart1')).draw(
		google.visualization.arrayToDataTable([
			['Actividades','porcentaje'],
			['Actividades Pendientes', calc1],
			['Avtividades Finalizadas', <?php echo $GLOBALS['mysql']->num_rows($consul3) ?>]
		]),
		{
			//is3D:true,
			'width':120,
			'height':100,
			chartArea:{width:'100%',height:'100%'},
			colors:['red','blue']

		}
	);

	var calc2 = eval(<?php echo $GLOBALS['mysql']->num_rows($consul2) ?> - <?php echo $GLOBALS['mysql']->num_rows($consul4) ?>);
	new google.visualization.PieChart(document.getElementById('chart2')).draw(
		google.visualization.arrayToDataTable([
			['Actividades','porcentaje'],
			['Actividades Finalizadas', calc2],
			['Avtividades Pendientes', <?php echo $GLOBALS['mysql']->num_rows($consul4) ?>]
		]),
		{
			//is3D:true,
			'width':120,
			'height':100,
			chartArea:{width:'100%',height:'100%'},
			colors:['blue','red']

		}
	);


function CambiaPeriodo(){

	if(periodo == 'mes'){var Newperiodo = 'semana';}
	if(periodo == 'semana'){var Newperiodo = 'dia';}
	if(periodo == 'dia'){var Newperiodo = 'mes';}

	Ext.getCmp('ContenedorDashboard').load(
		{
			url     : 'dashboard.php',
			scripts : true,
			nocache : true,
			params  : { periodo:Newperiodo }
		}
	);
}

function RecargaPeriodo(){
	Ext.getCmp('ContenedorDashboard').load(
		{
			url     : 'dashboard.php',
			scripts : true,
			nocache : true,
			params  : { periodo:periodo }
		}
	);
}


</script>