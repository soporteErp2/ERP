<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	// CONSULTAR EL CONTRATO
	$sql="SELECT numero_contrato,tipo_contrato FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id_contrato";
	$query=$mysql->query($sql,$mysql->link);
	$numero_contrato = $mysql->result($query,0,'numero_contrato');
	$tipo_contrato   = $mysql->result($query,0,'tipo_contrato');

	// CONSULTAR LAS VACACIONES DEL CONTRATO
	$sql="SELECT
				id_planilla,
				id_contrato,
				fecha_inicio_contrato,
				id_empleado,
				tipo_documento,
				documento_empleado,
				nombre_empleado,
				fecha_inicio_periodo_vacaciones,
				fecha_final_periodo_vacaciones,
				fecha_inicio_vacaciones_disfrutadas,
				fecha_fin_vacaciones_disfrutadas,
				dias_vacaciones_disfrutadas,
				id_concepto_vacaciones,
				concepto_vacaciones,
				tipo_base,
				base,
				valor_vacaciones_disfrutadas,
				dias_vacaciones_compensadas,
				valor_vacaciones_compensadas,
				fecha_inicio_labores,
				tipo_pago_vacaciones,
				estado,
				(SELECT consecutivo FROM nomina_planillas_liquidacion WHERE id=id_planilla) AS consecutivo_planilla
 				FROM nomina_vacaciones_empleados WHERE activo=1 AND estado=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_contrato=$id_contrato ";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$tableBody .= "<div class='row'>
							<div class='cell' data-col='1'></div>
			                <div class='cell' data-col='3'>$row[fecha_inicio_periodo_vacaciones]</div>
			                <div class='cell' data-col='3'>$row[fecha_final_periodo_vacaciones]</div>
			                <div class='cell' data-col='5'>".number_format($row['base'])."</div>
			                <div class='cell' data-col='3'>$row[fecha_inicio_vacaciones_disfrutadas]</div>
			                <div class='cell' data-col='3'>$row[fecha_fin_vacaciones_disfrutadas]</div>
			                <div class='cell' data-col='4'>$row[dias_vacaciones_disfrutadas]</div>
			                <div class='cell' data-col='5'>$row[dias_vacaciones_compensadas]</div>
			                <div class='cell mainText' data-col='5'>$row[consecutivo_planilla]</div>
						</div>";
	}

?>
<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: 100%; height: 100%; }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: 235px;}
    .content-grilla-filtro .cell[data-col="1"]{width: 2px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 131px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 60px;}
    .content-grilla-filtro .cell[data-col="4"]{width: 50px;}
    .content-grilla-filtro .cell[data-col="5"]{width: 80px;}
    .content-grilla-filtro .mainText{ font-weight: bold; text-align: center;}
    /*.content-grilla-filtro .cell[data-col="6"]{width: 80px;}*/
    .sub-content [data-width="input"]{width: 120px;}

</style>

<div class="main-content" style="height: 280px;overflow-y: auto;overflow-x: hidden;">
    <div class="sub-content" data-position="right">
        <div class="title">VACACIONES ANTERIORES DEL CONTRATO # <?php echo $numero_contrato; ?> (TIPO: <?php echo $tipo_contrato ?>)</div>

        <div class="content-grilla-filtro">
            <div class="head" style="height: 40px; ">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Periodo Vacaciones</div>
                <div class="cell" data-col="5">Base <br> Vacaciones</div>
                <div class="cell" data-col="2">Periodo Disfrute</div>
                <div class="cell" data-col="4">Dias <br>disfrute</div>
                <div class="cell" data-col="5">Dias <br>Compensados</div>
                <div class="cell" data-col="5">No. Planilla <br>Liquidacion</div>
                <!-- <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div> -->
            </div>
            <div class="body" id="body_grilla_filtro">
            	<?php echo $tableBody; ?>
            </div>
        </div>

    </div>

</div>


<script>


</script>