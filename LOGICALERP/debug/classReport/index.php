<html>
<head>
	<link rel="stylesheet" type="text/css" href="Win/css/Win.min.css">
	<link rel="stylesheet" type="text/css" href="Win/css/Win-theme-blue.css">

	<link rel="stylesheet" type="text/css" href="Sortable/st/app.css">
	<link rel="stylesheet" type="text/css" href="style.css">

	<!-- <link rel="stylesheet" type="text/css" href="dragula/dragula.min.css">-->
	<script type="text/javascript" src="Sortable/Sortable.min.js"></script>
	<script type="text/javascript" src="Win/js/Win.min.js"></script>
	<!--<script type="text/javascript" src="Win/js/Win.widget.js"></script>
	<script type="text/javascript" src="Win/js/Win.ajax.js"></script>
	<script src="Sortable/Sortable.js"></script>
	<script src="Sortable/ng-sortable.js"></script>
	<script src="Sortable/st/app.js"></script>-->
</head>
<?php
	include_once("../../../configuracion/conectar.php");
	include ('Report.php');



	$arrayTablas[] = array(
							'table'        => 'nomina_planillas',
							'alias'        => 'Planilla',
							'PK'           => 'id',
							'alias_fields' => array(
													'fecha_documento'  => 'Fecha',
													'fecha_inicio'     => 'Fecha Inicio',
													'fecha_final'      => 'Fecha Final',
													'consecutivo'      => 'Consecutivo',
													'tipo_liquidacion' => 'Tipo Liquidacion',
													),
							'dependencies' =>
											array('' =>  ''),
							);

	$arrayTablas[] = array(
							'table'        => 'nomina_planillas_empleados',
							'alias'        => 'Empleados',
							'PK'           => 'id',
							'alias_fields' => array(
													'tipo_documento'     => 'Tipo Documento',
													'documento_empleado' => 'Documento',
													'nombre_empleado'    => 'Empleado',
													'dias_laborados'     => 'Dias laborados',
													),
							'dependencies' =>
											array('nomina_planillas' => 'id_planilla' )
							);

	$arrayTablas[] = array(
							'table'        => 'nomina_planillas_empleados_conceptos',
							'alias'        => 'Conceptos',
							'PK'           => 'id',
							'alias_fields' => array(
													'codigo_concepto' => 'Tipo Documento',
													'concepto'        => 'Documento',
													'naturaleza'      => 'Empleado',
													'valor_concepto'  => 'Dias laborados',
													),
							'dependencies' =>
											array(
													'nomina_planillas'           => 'id_planilla',
													'nomina_planillas_empleados' => array( 1 => 'id_empleados', 2 => 'id_contrato'),
												  )
							);

	$Report = new Report($mysql,$arrayTablas);
	// $Report->showTables();
	$Report->inicializa();


 ?>

<body>

</body>
</html>
<script>

</script>