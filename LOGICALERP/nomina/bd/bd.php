<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa     = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'habilitar_empleados_en_vacaciones':
			habilitar_empleados_en_vacaciones($id_empresa,$link);
			break;

		case 'filtro_fecha_dashboard':
			filtro_fecha_dashboard();
			break;

		default:
			# code...
			break;
	}

	// FUNCION CARGADA AL INGRESAR AL MODULO DE NOMINA PARA HABILITAR LOS EMPLEADOS A LOS QUE SE LES CUMPLIO EL PERIODO DE VACACIONES
	function habilitar_empleados_en_vacaciones($id_empresa,$link){
		$fecha = date("Y-m-d");

		// CONSULTAR
		echo$sql="SELECT
				NVE.id,
				NVE.id_planilla,
				NVE.id_empleado,
				NVE.id_contrato
			FROM
				nomina_vacaciones_empleados AS NVE
			INNER JOIN nomina_planillas_liquidacion NPL ON NPL.id = NVE.id_planilla
			WHERE
				NVE.activo = 1
			AND NVE.id_empresa = $id_empresa
			AND NVE.estado = 0
			AND NVE.fecha_inicio_labores >= '$fecha'
			AND NPL.activo = 1
			AND NPL.id_empresa = $id_empresa
			AND NPL.estado = 1";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$whereContratos.=($whereContratos=='')? " (id='".$row['id_contrato']."' AND id_empleado='".$row['id_empleado']."') "
													: " OR (id='".$row['id_contrato']."' AND id_empleado='".$row['id_empleado']."') " ;

			$whereVacaciones.=($whereVacaciones=='')? " (id='".$row['id']."' AND id_planilla='".$row['id_planilla']."' AND id_empleado='".$row['id_empleado']."' AND id_contrato='".$row['id_contrato']."') "
													: " OR (id='".$row['id']."' AND id_planilla='".$row['id_planilla']."' AND id_empleado='".$row['id_empleado']."' AND id_contrato='".$row['id_contrato']."') " ;
		}

		// HABILITAR LOS CONTRATOS DE LOS EMPLEADOS
		$sql = "UPDATE empleados_contratos
				SET estado=0
				WHERE activo=1
				AND id_empresa=$id_empresa
				AND ($whereContratos)";
		$query=mysql_query($sql,$link);

		if (!$query) {
			echo " false1 ";
		}

		// ACTUALIZAR EL REGISTRO DEL LIBRO DE VACACIONES
		$sql="UPDATE nomina_vacaciones_empleados
				SET estado=1
				WHERE activo=1
				AND id_empresa=$id_empresa
				AND estado=0
				AND ($whereVacaciones)";
		$query2=mysql_query($sql,$link);
		if (!$query2) {
			echo " false2 ";
		}

		echo "true";
	}

	// FILTROS DE FECHA EN EL DASHBOARD
	function filtro_fecha_dashboard(){
		$fecha_final = date("Y-m-d");
		$fecha_inicio = date('Y-m-d', strtotime('-40 day')) ;
		//

		?>
			<style>
				.dashboard_filter td {
					padding: 2px;
				}
			</style>

			<div>
				<table class="dashboard_filter" align="center">
					<tr>
						<td>Fecha Inicio</td>
						<td><input type="text" id="fechai"></td>
					</tr>
					<tr>
						<td>Fecha Final</td>
						<td><input type="text" id="fechaf"></td>
					</tr>
				</table>
			</div>
			<script>

				new Ext.form.DateField({
				    format     : 'Y-m-d',               //FORMATO
				    width      : 100,                   //ANCHO
				    allowBlank : false,
				    applyTo    : 'fechai',
				    editable   : false,                 //EDITABLE
				    value      : "<?php echo $fecha_inicio ?>",             //VALOR POR DEFECTO
				    listeners  : { select: function() { cambiaPeriodoDashboard(); } }
				});

				new Ext.form.DateField({
				    format     : 'Y-m-d',               //FORMATO
				    width      : 100,                   //ANCHO
				    allowBlank : false,
				    applyTo    : 'fechaf',
				    editable   : false,                 //EDITABLE
				    value      : "<?php echo $fecha_final ?>",             //VALOR POR DEFECTO
				    listeners  : { select: function() { cambiaPeriodoDashboard(); } }
				});

			</script>
		<?php
	}

?>