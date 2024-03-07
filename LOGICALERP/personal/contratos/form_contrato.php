<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];

	if ($id_contrato>0) {
		$sql="SELECT *
				FROM empleados_contratos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_contrato";
		$query=$mysql->query($sql,$mysql->link);

		$numero_contrato         = $mysql->result($query,0,'numero_contrato');
		$fecha_inicio_contrato   = $mysql->result($query,0,'fecha_inicio_contrato');
		$fecha_fin_contrato      = $mysql->result($query,0,'fecha_fin_contrato');
		$salario_basico          = $mysql->result($query,0,'salario_basico');
		$salario_integral        = $mysql->result($query,0,'salario_integral');
		$fecha_inicio_nomina     = $mysql->result($query,0,'fecha_inicio_nomina');
		$id_tipo_contrato        = $mysql->result($query,0,'id_tipo_contrato');
		$id_grupo_trabajo        = $mysql->result($query,0,'id_grupo_trabajo');
		$id_nivel_riesgo_laboral = $mysql->result($query,0,'id_nivel_riesgo_laboral');
		$id_centro_costo         = $mysql->result($query,0,'id_centro_costos');
		$codigo_centro_costos    = $mysql->result($query,0,'codigo_centro_costos');
		$nombre_centro_costos    = $mysql->result($query,0,'nombre_centro_costos');
		$numero_cuenta_bancaria  = $mysql->result($query,0,'numero_cuenta_bancaria');
		$id_cargo                = $mysql->result($query,0,'id_cargo');
		$fecha_cancelacion       = $mysql->result($query,0,'fecha_cancelacion');
		$motivo_cancelacion      = $mysql->result($query,0,'motivo_cancelacion');
		$observacion_cancelacion = $mysql->result($query,0,'observacion_cancelacion');
		$estado                  = $mysql->result($query,0,'estado');

		$id_tipo_trabajador    = $mysql->result($query,0,'id_tipo_trabajador');
		$id_subtipo_trabajador = $mysql->result($query,0,'id_subtipo_trabajador');
		$id_forma_pago         = $mysql->result($query,0,'id_forma_pago');
		$id_medio_pago         = $mysql->result($query,0,'id_medio_pago');
		$nombre_banco          = $mysql->result($query,0,'nombre_banco');
		$tipo_cuenta_bancaria  = $mysql->result($query,0,'tipo_cuenta_bancaria');

		$centro_costo=($codigo_centro_costos!='')? $codigo_centro_costos.' - '.$nombre_centro_costos : '' ;

		if ($estado==1) {
			$titulo_estado = '<i>(CONTRATO FINALIZADO)</i>';
			$acumscript = "$('.table-form input').attr('readonly',true);
							$('.table-form select').attr('disabled',true);
							document.getElementById('btn_buscar_ccos').style.display='none';
							Ext.getCmp('btn_contrato').disable();
							";

			$bodyTable="<tr>
							<td>Fecha Retiro</td>
							<td><input type='text' value='$fecha_cancelacion' readonly></td>
						</tr>
						<tr>
							<td>Motivo</td>
							<td><input type='text' value='$motivo_cancelacion' readonly></td>
						</tr>
						<tr>
							<td>Observacion</td>
							<td><textarea cols='5' rows='4' readonly>$observacion_cancelacion</textarea></td>
						</tr>";

		}
		else if ($estado==2) {
			$titulo_estado = '<i>(EMPLEADO EN VACACIONES)</i>';
		}
		else{
			$acumscript.='//PONER LOS CAMPO COMO FECHAS DEL EXT
        					new Ext.form.DateField({
        					    format     : "Y-m-d",
        					    width      : 170,
        					    allowBlank : false,
        					    showToday  : false,
        					    applyTo    : "fecha_inicio_contrato",
        					    editable   : false,
        					    listeners  : { select: function() {   } }
        					});

        					new Ext.form.DateField({
        					    format     : "Y-m-d",
        					    width      : 170,
        					    allowBlank : false,
        					    showToday  : false,
        					    applyTo    : "fecha_fin_contrato",
        					    editable   : false,
        					    listeners  : { select: function() {   } }
        					});

        					new Ext.form.DateField({
        					    format     : "Y-m-d",
        					    width      : 170,
        					    allowBlank : false,
        					    showToday  : false,
        					    applyTo    : "fecha_inicio_nomina",
        					    editable   : false,
        					    listeners  : { select: function() {   } }
        					});';
		}

	}
	else{
		$acumscript .= '
						//PONER LOS CAMPO COMO FECHAS DEL EXT
    					new Ext.form.DateField({
    					    format     : "Y-m-d",
    					    width      : 170,
    					    allowBlank : false,
    					    showToday  : false,
    					    applyTo    : "fecha_inicio_contrato",
    					    editable   : false,
    					    listeners  : { select: function() {   } }
    					});

    					new Ext.form.DateField({
    					    format     : "Y-m-d",
    					    width      : 170,
    					    allowBlank : false,
    					    showToday  : false,
    					    applyTo    : "fecha_fin_contrato",
    					    editable   : false,
    					    listeners  : { select: function() {   } }
    					});

    					new Ext.form.DateField({
    					    format     : "Y-m-d",
    					    width      : 170,
    					    allowBlank : false,
    					    showToday  : false,
    					    applyTo    : "fecha_inicio_nomina",
    					    editable   : false,
    					    listeners  : { select: function() {   } }
    					});
						';
	}

	//CONSULTAR EL TIPO DE CONTRATO
	$tipo_contrato='<option value="">Seleccione...</option>';
	$sql="SELECT id,descripcion,dias FROM nomina_tipo_contrato WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		if ($row['id']==$id_tipo_contrato) {
			$selected = 'selected';
			$acumscript .= ($row['dias']==0)? 'document.getElementById("tr_fecha_fin_contrato").style.display="none";' : '' ;
		}
		else
		{
			$selected = '';
		}
		$selected = ($row['id']==$id_tipo_contrato)? 'selected' : '' ;
		$tipo_contrato.='<option value="'.$row['id'].','.$row['dias'].'" '.$selected.'>'.utf8_encode($row['descripcion']).'</option>';

	}

	//CONSULTAR LOS TIPO DE TRABAJADOR
	$tipo_trabajador='<option value="">Seleccione...</option>';
	$sql="SELECT id,tipo FROM nomina_configuracion_tipo_trabajador WHERE activo=1  AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$selected = ($row['id']==$id_tipo_trabajador)? 'selected' : '' ;
		$tipo_trabajador.='<option value="'.$row['id'].'" '.$selected.'>'.utf8_encode($row['tipo']).'</option>';
	}

	//CONSULTAR LOS SUBTIPO DE TRABAJADOR
	$subtipo_trabajador='<option value="">Seleccione...</option>';
	$sql="SELECT id,tipo FROM nomina_configuracion_subtipo_trabajador WHERE activo=1  AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$selected = ($row['id']==$id_subtipo_trabajador)? 'selected' : '' ;
		$subtipo_trabajador.='<option value="'.$row['id'].'" '.$selected.'>'.utf8_encode($row['tipo']).'</option>';
	}

	//CONSULTAR LOS GRUPOS DE TRABAJO
	$grupo_trabajo='<option value="">Seleccione...</option>';
	$sql="SELECT id,nombre FROM nomina_grupos_trabajo WHERE activo=1  AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$selected = ($row['id']==$id_grupo_trabajo)? 'selected' : '' ;
		$grupo_trabajo.='<option value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
	}

	// CONSULTAR LOS NIVELES DE RIESGOS LABORALES DEL EMPLEADO
	$riesgo_laboral='<option value="">Seleccione...</option>';
	$sql="SELECT id,nombre FROM nomina_niveles_riesgos_laborales WHERE activo=1  AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$selected = ($row['id']==$id_nivel_riesgo_laboral)? 'selected' : '' ;
		$riesgo_laboral.='<option value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
	}

	// CONSULTAR LOS CARGOS
	$cargos='<option value="">Seleccione...</option>';
	$sql="SELECT id,nombre FROM empleados_cargos WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$selected = ($row['id']==$id_cargo)? 'selected' : '' ;
		$cargos.='<option value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
	}

	$acumscript .= "document.getElementById('salario_integral').value='$salario_integral';";

	// CONSULTAR SI SE REALIZARON MODIFICACIONES EN EL SALARIO
	$sql="SELECT COUNT(id) AS cont FROM empleados_contratos_modificacion_salarios WHERE activo=1 AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
	$query=$mysql->query($sql,$mysql->link);
	$cont_deal_change = $mysql->result($query,0,'cont');
	$btnSalario = '';
	if ($cont_deal_change>0) {
		$btnSalario = '<td style="padding:0px;">
							<img src="images/historial.png" style="width:32px;height:32px;" title="Historico" onclick="ver_historial_salarios('.$id_empleado.','.$id_contrato.')">
						</td>';
	}
	// CONSULTAR SI SE REALIZARON MODIFICACIONES EN LA FECHA DE VENCIMIENTO
	$sql="SELECT COUNT(id) AS cont FROM empleados_contratos_modificacion_vencimientos WHERE activo=1 AND id_contrato=$id_contrato";
	$query=$mysql->query($sql,$mysql->link);
	$cont_deal_change = $mysql->result($query,0,'cont');
	$btnTerminacion = '';
	if ($cont_deal_change>0) {
		$btnTerminacion = '<td style="padding:0px;">
							  <img src="images/historial.png" style="width:32px;height:32px;" title="Historico" onclick="ver_historial_vencimientos('.$id_contrato.')">
						   </td>';
	}

	// CONSULTAR LAS FORMAS DE PAGO PARA NOMINA ELECTRONICA
	$formasPago='<option value="">Seleccione...</option>';
	$sql="SELECT id,nombre FROM nomina_configuracion_formas_pago WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$selected = ($row['id']==$id_forma_pago)? 'selected' : '' ;
		$formasPago.='<option value="'.$row['id'].'" '.$selected.'>'.utf8_encode($row['nombre']).'</option>';
	}

	// CONSULTAR LOS MEDIOS DE PAGO PARA NOMINA ELECTRONICA
	$mediosPagos='<option value="">Seleccione...</option>';
	$sql="SELECT id,nombre FROM nomina_configuracion_medios_pago WHERE activo=1 AND id_empresa=$id_empresa";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$selected = ($row['id']==$id_medio_pago)? 'selected' : '' ;
		$mediosPagos.='<option value="'.$row['id'].'" '.$selected.'>'.utf8_encode($row['nombre']).'</option>';
	}


	// $btnSalario=( $mysql->result($query,0,'cont') > 0 )? '<img src="../contabilidad/img/carga_doc.png" style="float:left;cursor:pointer;margin-left:5px;width:25px;height:25px;" title="Historico" onclick="ver_historial_salarios('.$id_empleado.','.$id.')">' : '' ;
?>
<style>
	img{
		cursor: pointer;
	}
</style>
<div class="content" >

	<table class="table-form-contrato" style="width:95%;" >
		<tr class="thead" style="background-color: #a2a2a2;">
			<td colspan="3">DATOS DEL CONTRATO <?php echo $titulo_estado ?></td>
		</tr>
		<tr>
			<td>Numero de Contrato</td>
			<td><input type="text"  value="<?php echo $numero_contrato; ?>"  data-requiere="true" id="numero_contrato" data-value="" ></td>
		</tr>
		<tr>
			<td>Fecha inicio contrato</td>
			<td><input type="text"   value="<?php echo $fecha_inicio_contrato; ?>" id="fecha_inicio_contrato" data-value=""></td>
		</tr>
		<tr id="tr_fecha_fin_contrato">
			<td>Fecha fin contrato</td>
			<td><input type="text"  value="<?php echo $fecha_fin_contrato; ?>"   id="fecha_fin_contrato"></td><?php echo $btnTerminacion ?>
		</tr>
		<tr>
			<td>Salario Basico</td>
			<td><input type="text" value="<?php echo $salario_basico; ?>" data-requiere="true"  id="salario_basico"  ></td><?php echo $btnSalario ?>
		</tr>
		<tr>
			<td>Salario Integral</td>
			<td>
				<select id="salario_integral">
					<option value="No">No</option>
					<option value="Si">Si</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Fecha inicio nomina</td>
			<td><input type="text" value="<?php echo $fecha_inicio_nomina; ?>"   id="fecha_inicio_nomina" ></td>
		</tr>
		<tr>
			<td>Tipo contrato</td>
			<td>
				<select id="id_tipo_contrato"  onchange="cambia_fecha_fin_contrato(this.value)" data-requiere="true">
					<?php echo $tipo_contrato ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Tipo trabajador</td>
			<td>
				<select id="id_tipo_trabajador"  >
					<?php echo $tipo_trabajador ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Subtipo trabajador</td>
			<td>
				<select id="id_subtipo_trabajador"  >
					<?php echo $subtipo_trabajador ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Cargo</td>
			<td>
				<select id="id_cargo" data-requiere="true">
					<?php echo $cargos ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Grupo de trabajo</td>
			<td>
				<select id="id_grupo_trabajo" data-requiere="true">
					<?php echo $grupo_trabajo ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Nivel de riesgo (ARL)</td>
			<td>
				<select id="id_nivel_riesgo_laboral" data-requiere="true">
					<?php echo $riesgo_laboral ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Centro de Costos</td>
			<td><input type="text"  value="<?php echo $centro_costo; ?>" title="<?php echo $centro_costo; ?>" id="centro_costo" data-requiere="true" data-value="<?php echo $id_centro_costo; ?>"></td>
			<td style="padding:0px;" onclick="ventanaBuscarCentroCostos()"><img src="../contabilidad/img/buscar.png" id="btn_buscar_ccos"></td>
		</tr>
		<tr>
			<td>Nombre Banco</td>
			<td><input type="text"  value="<?php echo $nombre_banco; ?>"   id="nombre_banco" ></td>
		</tr>
		<tr>
			<td>Tipo cuenta bancaria</td>
			<td><input type="text"  value="<?php echo $tipo_cuenta_bancaria; ?>"   id="tipo_cuenta_bancaria" ></td>
		</tr>
		<tr>
			<td>Numero Cuenta Bancaria</td>
			<td><input type="text"  value="<?php echo $numero_cuenta_bancaria; ?>"   id="numero_cuenta_bancaria" ></td>
		</tr>
		<tr>
			<td>Forma de pago</td>
			<td>
				<select id="id_forma_pago" >
					<?php echo $formasPago ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Medio de pago</td>
			<td>
				<select id="id_medio_pago" >
					<?php echo $mediosPagos ?>
				</select>
			</td>
		</tr>
		<?php echo $bodyTable; ?>
	</table>
	<div id="divLoadContrato" style="display:none;"></div>
</div>
<script>


	<?php echo $acumscript; ?>
	Ext.getCmp("btn_regresar").show();

	//consulta si no hay contrato vigente para habilitar el boton copiar
	if(contratoActivo == "false" && <?php echo $estado ?> == 1){
		Ext.getCmp("btn_duplicar_contrato").show();
	}


	function cambia_fecha_fin_contrato(valor){
		var dias = valor.split(",")[1];
		if (dias>0) {
			document.getElementById("tr_fecha_fin_contrato").setAttribute('style','');
			document.getElementById("fecha_fin_contrato").value="";
		}
		else{
			document.getElementById("tr_fecha_fin_contrato").style.display="none";
			document.getElementById("fecha_fin_contrato").value="0000-00-00";
		}
	}

</script>
