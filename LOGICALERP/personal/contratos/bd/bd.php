<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa=$_SESSION['EMPRESA'];

	switch ($opc) {
		case 'valida_cantidad_contrato':
			valida_cantidad_contrato($id_empleado,$id_empresa,$mysql);
			break;
		case "ventanaAgregarContrato":
			ventanaAgregarContrato($accion,$id,$id_empleado,$id_empresa,$link);
			break;
		case 'agregarContrato':
			agregarContrato(
							$numero_contrato,
							$fecha_inicio_contrato,
							$fecha_fin_contrato,
							$salario_basico,
							$salario_integral,
							$fecha_inicio_nomina,
							$id_tipo_contrato,
							$numero_cuenta_bancaria,
							$id_grupo_trabajo,
							$id_nivel_riesgo_laboral,
							$id_tipo_trabajador,
							$id_subtipo_trabajador,
							$id_forma_pago,
							$id_medio_pago,
							$nombre_banco,
							$tipo_cuenta_bancaria,
							$id_empleado,
							$id_centro_costo,
							$sucursal,
							$id_cargo,
							$link
							);
			// agregarContrato($id_centro_costo,$numero_contrato,$fecha_inicio_contrato,$fecha_fin_contrato,$salario_basico,$salario_integral,$fecha_inicio_nomina,$id_tipo_contrato,$numero_cuenta_bancaria,$id_grupo_trabajo,$id_nivel_riesgo_laboral,$id_empleado,$id_empresa,$sucursal,$id_cargo,$id_grupo_trabajo,$id_nivel_riesgo_laboral,$id_tipo_trabajador,$id_subtipo_trabajador,$link);
			break;
		case 'editarContrato':
			editarContrato(
							$numero_contrato,
							$fecha_inicio_contrato,
							$fecha_fin_contrato,
							$salario_basico,
							$salario_integral,
							$fecha_inicio_nomina,
							$id_tipo_contrato,
							$numero_cuenta_bancaria,
							$id_grupo_trabajo,
							$id_nivel_riesgo_laboral,
							$id_tipo_trabajador,
							$id_subtipo_trabajador,
							$id_forma_pago,
							$id_medio_pago,
							$nombre_banco,
							$tipo_cuenta_bancaria,
							$id_empleado,
							$id_centro_costo,
							$id,
							$sucursal,
							$id_cargo,
							$link
						);

			// editarContrato($id_centro_costo,$id,$numero_contrato,$fecha_inicio_contrato,$fecha_fin_contrato,$salario_basico,$salario_integral,$fecha_inicio_nomina,$id_tipo_contrato,$numero_cuenta_bancaria,$id_grupo_trabajo,$id_nivel_riesgo_laboral,$id_empleado,$id_empresa,$sucursal,$id_cargo,$id_grupo_trabajo,$id_nivel_riesgo_laboral,$id_tipo_trabajador,$id_subtipo_trabajador,$link);
			break;
		case 'ventana_cancelar_contrato':
			ventana_cancelar_contrato($id_empresa,$link);
			break;
		case 'cancelarContrato':
			cancelarContrato($id,$id_empleado,$fecha_cancelacion,$observacion_cancelacion,$id_motivo_cancelacion,$motivo_cancelacion,$id_empresa,$link);
			break;
		case 'ventanaEntidadesEmpleado':
			ventanaEntidadesEmpleado($id_empresa,$id_empleado,$id_contrato,$estado,$link);
			break;
		case 'guardarEntidadConcepto':
			guardarEntidadConcepto($id_empleado,$id_contrato,$cont,$id_entidad,$id_concepto,$id_empresa,$link);
			break;
		case 'eliminarEntidadConcepto':
			eliminarEntidadConcepto($cont,$id_entidad,$id_concepto,$id_empleado,$id_contrato,$id_empresa,$link);
			break;
		case 'validar_centro_costo':
			validar_centro_costo($id_ccos,$codigo_centro_costos,$centro_costos,$id_empresa,$link);
			break;
		case 'generar_traslado':
			generar_traslado($id_contrato,$id_concepto,$id_empleado,$id_tercero_old,$id_tercero_traslado,$fecha_inicial,$fecha_final,$id_empresa,$cont,$link);
			break;
		case 'duplicar_contrato':
			duplicar_contrato($id_contrato,$id_empresa,$link);
			break;
	}

	// VALIDAR QUE EL EMPLEADO SOLO TENGA UN CONTRATO ACTIVO
	function valida_cantidad_contrato($id_empleado,$id_empresa,$mysql){
		//VALIDAR SI EXISTEN MAS CONTRATOS GENERADOS, Y SI EXISTEN QUE NO ESTEN VIGENTES, DE LO CONTRARIO NO SE PUEDE AGREGAR MAS CONTRATOS
		$sql   = "SELECT COUNT(id) AS cont FROM empleados_contratos WHERE activo=1 AND id_empleado=$id_empleado AND (estado=0 OR estado=2) AND id_empresa=$id_empresa";
		$query = $mysql->query($sql,$mysql->link);
		$cont  = $mysql->result($query,0,'cont');

		if ($cont>0) {
			echo 'true';
			// echo'<script>
			// 		alert("Aviso\nEste empleado ya tiene un contrato vigente\nsi desea agregar uno nuevo debe terminar el contrato anterior");
			// 		Win_Ventana_agregar_contrato.close();
			// 	</script>';exit;
		}
		else{
			echo 'false';
		}
	}

	//AGREAGR UN NUEVO CONTRATO A UN EMPLEADO
	// function agregarContrato($id_centro_costo,$numero_contrato,$fecha_inicio_contrato,$fecha_fin_contrato,$salario_basico,$salario_integral,$fecha_inicio_nomina,$id_tipo_contrato,$numero_cuenta_bancaria,$id_grupo_trabajo,$id_nivel_riesgo_laboral,$id_empleado,$id_empresa,$sucursal,$id_cargo,$id_grupo_trabajo,$id_nivel_riesgo_laboral,$id_tipo_trabajador,$id_subtipo_trabajador,$link){
	function agregarContrato(
							$numero_contrato,
							$fecha_inicio_contrato,
							$fecha_fin_contrato,
							$salario_basico,
							$salario_integral,
							$fecha_inicio_nomina,
							$id_tipo_contrato,
							$numero_cuenta_bancaria,
							$id_grupo_trabajo,
							$id_nivel_riesgo_laboral,
							$id_tipo_trabajador = NULL,
							$id_subtipo_trabajador = NULL,
							$id_forma_pago = NULL,
							$id_medio_pago = NULL,
							$nombre_banco = NULL,
							$tipo_cuenta_bancaria = NULL,
							$id_empleado,
							$id_centro_costo,
							$sucursal,
							$id_cargo,
							$link
						)
	{

		$temp=split(',', $id_tipo_contrato);
		$id_tipo_contrato=$temp[0];
		echo$sql="INSERT INTO empleados_contratos(
							numero_contrato,
							fecha_inicio_contrato,
							fecha_fin_contrato,
							salario_basico,
							salario_integral,
							fecha_inicio_nomina,
							id_tipo_contrato,
							numero_cuenta_bancaria,
							id_grupo_trabajo,
							id_nivel_riesgo_laboral,
							id_empleado,
							id_cargo,
							id_centro_costos,
							id_empresa,
							id_sucursal,
							id_sucursal_creacion)
				VALUES('$numero_contrato',
						'$fecha_inicio_contrato',
						'$fecha_fin_contrato',
						'$salario_basico',
						'$salario_integral',
						'$fecha_inicio_nomina',
						'$id_tipo_contrato',
						'$numero_cuenta_bancaria',
						'$id_grupo_trabajo',
						'$id_nivel_riesgo_laboral',
						'$id_empleado',
						'$id_cargo',
						'$id_centro_costo',
						'$_SESSION[EMPRESA]',
						'$sucursal',
						'$sucursal')";

		$query=mysql_query($sql,$link);
		if ($query) {

			$sql="SELECT LAST_INSERT_ID() AS id";
			$query=mysql_query($sql,$link);
			$id=mysql_result($query,0,'id');

			// ESTOS REGISTROS SE ACTUALIZAN Y NO SE INSERTAN ARRIBA POR QUE SON CAMPOS DE NOMINA ELECTRONICA Y PUEDE QUE NO TODOS TENGAN EL SISTEMA CONFIGURADO, POR ESE MOTIVO DESPUES 
			// DE INSERTAR PARA EVITAR ERRORES SE ACTUALIZA, SI ACA RESULTA ERROR POR QUE NO HAY IMPLEMENTACION, NO PASA NADA, PUES YA SE INSERTO EL CONTRATO NORMALMENTE
			$sql =  "UPDATE empleados_contratos SET
							id_tipo_trabajador='$id_tipo_trabajador',
							id_subtipo_trabajador='$id_subtipo_trabajador',
							id_forma_pago='$id_forma_pago',
							id_medio_pago='$id_medio_pago',
							nombre_banco='$nombre_banco',
							tipo_cuenta_bancaria='$tipo_cuenta_bancaria'							
						WHERE activo=1 AND id_empleado=$id_empleado AND id_empresa=$_SESSION[EMPRESA] AND id=$id";
			$query=mysql_query($sql,$link);

			echo '<script>
					Inserta_Div_empleados_contratos('.$id.');
					Win_Ventana_agregar_contrato.close();
				</script>';
		}
		else{
			echo '<script>alert("Erro!\nNo se guardo el contrato, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	//MODIFICAR LA INFORMACION DE UN CONTRATO
	// function editarContrato($id_centro_costo,$id,$numero_contrato,$fecha_inicio_contrato,$fecha_fin_contrato,$salario_basico,$salario_integral,$fecha_inicio_nomina,$id_tipo_contrato,$numero_cuenta_bancaria,$id_grupo_trabajo,$id_nivel_riesgo_laboral,$id_empleado,$id_empresa,$sucursal,$id_cargo,$id_grupo_trabajo,$id_nivel_riesgo_laboral,$id_tipo_trabajador,$id_subtipo_trabajador,$link){
	
	function editarContrato(
							$numero_contrato,
							$fecha_inicio_contrato,
							$fecha_fin_contrato,
							$salario_basico,
							$salario_integral,
							$fecha_inicio_nomina,
							$id_tipo_contrato,
							$numero_cuenta_bancaria,
							$id_grupo_trabajo,
							$id_nivel_riesgo_laboral,
							$id_tipo_trabajador = NULL,
							$id_subtipo_trabajador = NULL,
							$id_forma_pago = NULL,
							$id_medio_pago = NULL,
							$nombre_banco = NULL,
							$tipo_cuenta_bancaria = NULL,
							$id_empleado,
							$id_centro_costo,
							$id,
							$sucursal,
							$id_cargo,
							$link
						)
	{
		$sql="SELECT salario_basico,fecha_fin_contrato,vencimiento_firmado,usuario_vencimiento_firmado,archivo_vencimiento_firmado,fecha_vencimiento_firmado FROM empleados_contratos WHERE activo=1 AND id_empleado=$id_empleado AND id_empresa=$id_empresa AND id=$id";
		$query=mysql_query($sql,$link);
		$salario_basico_anterior = mysql_result($query,0,'salario_basico');
		$fin_contrato_anterior   = mysql_result($query,0,'fecha_fin_contrato');
		$fin_archivo_anterior    = mysql_result($query,0,'archivo_vencimiento_firmado');
		$fin_usuario_anterior    = mysql_result($query,0,'usuario_vencimiento_firmado');
		$fin_fecha_anterior      = mysql_result($query,0,'fecha_vencimiento_firmado');
		$vencimiento_firmado     = mysql_result($query,0,'vencimiento_firmado');

		if($fin_contrato_anterior <> $fecha_fin_contrato){
			$fin_archivo_anterior = '';
			$fin_usuario_anterior = '';
			$fin_fecha_anterior   = '';
			$vencimiento_firmado  = 'false';
			//SI HAY UN CAMBIO EN LA FINALIZACION, ENTONCES DE DEBE GUARDAR EL REGISTRO
			$sql = "INSERT INTO empleados_contratos_modificacion_vencimientos(id_contrato,fecha_terminacion_anterior,fecha_terminacion_nueva,fecha_modificacion,id_usuario,id_empresa)
					VALUES ($id,'$fin_contrato_anterior','$fecha_fin_contrato','".date("Y-m-d")."','$_SESSION[IDUSUARIO]',$id_empresa)";
			$query= mysql_query($sql,$link);
		}		

		// SI HAY UN CAMBIO EN EL SALARIO, ENTONCES SE DEBE GUARDAR EL REGISTRO
		if ( ($salario_basico_anterior*1) <> ($salario_basico*1) ) {
			$sql = "INSERT INTO empleados_contratos_modificacion_salarios (id_empleado,id_contrato,salario_anterior,salario_nuevo,fecha_modificacion,id_sucursal,id_empresa)
					VALUES ($id_empleado,$id,$salario_basico_anterior,$salario_basico,'".date("Y-m-d")."',$sucursal,$id_empresa) ";
			$query= mysql_query($sql,$link);
		}

		$temp=split(',', $id_tipo_contrato);
		$id_tipo_contrato=$temp[0];
		$sql="UPDATE empleados_contratos SET
						numero_contrato             = '$numero_contrato',
						fecha_inicio_contrato       = '$fecha_inicio_contrato',
						fecha_fin_contrato          = '$fecha_fin_contrato',
						salario_basico              = '$salario_basico',
						salario_integral            = '$salario_integral',
						fecha_inicio_nomina         = '$fecha_inicio_nomina',
						id_cargo                    = '$id_cargo',
						id_tipo_contrato            = '$id_tipo_contrato',
						numero_cuenta_bancaria      = '$numero_cuenta_bancaria',
						id_grupo_trabajo            = '$id_grupo_trabajo',
						id_nivel_riesgo_laboral     = '$id_nivel_riesgo_laboral',
						id_centro_costos            = '$id_centro_costo',
						vencimiento_firmado         = '$vencimiento_firmado',
						archivo_vencimiento_firmado = '$fin_archivo_anterior',
						usuario_vencimiento_firmado = '$fin_usuario_anterior',
						fecha_vencimiento_firmado   = '$fin_fecha_anterior'
						WHERE activo=1 AND id_empleado=$id_empleado AND id_empresa=$_SESSION[EMPRESA] AND id=$id";
		$query=mysql_query($sql,$link);
		if ($query) {
			// ESTOS REGISTROS SE ACTUALIZAN Y NO SE INSERTAN ARRIBA POR QUE SON CAMPOS DE NOMINA ELECTRONICA Y PUEDE QUE NO TODOS TENGAN EL SISTEMA CONFIGURADO, POR ESE MOTIVO DESPUES 
			// DE INSERTAR PARA EVITAR ERRORES SE ACTUALIZA, SI ACA RESULTA ERROR POR QUE NO HAY IMPLEMENTACION, NO PASA NADA, PUES YA SE INSERTO EL CONTRATO NORMALMENTE
			$sql =  "UPDATE empleados_contratos SET
							id_tipo_trabajador='$id_tipo_trabajador',
							id_subtipo_trabajador='$id_subtipo_trabajador',
							id_forma_pago='$id_forma_pago',
							id_medio_pago='$id_medio_pago',
							nombre_banco='$nombre_banco',
							tipo_cuenta_bancaria='$tipo_cuenta_bancaria'							
						WHERE activo=1 AND id_empleado=$id_empleado AND id_empresa=$_SESSION[EMPRESA] AND id=$id";
			$query=mysql_query($sql,$link);

			echo '<script>
					Actualiza_Div_empleados_contratos('.$id.');
					Win_Ventana_agregar_contrato.close();
				</script>';
		}
		else{
			echo '<script>alert("Erro!\nNo se actualizo el contrato, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	//VENTANA CANCELAR CONTRATO
	function ventana_cancelar_contrato($id_empresa,$link){

		$sql="SELECT id,descripcion FROM nomina_motivo_fin_contrato WHERE activo=1 AND id_empresa=$id_empresa";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$select.='<option value="'.$row['id'].'">'.$row['descripcion'].'</option>';
		}

		echo '<style>
				.fila{
					width      :100%;
					margin-top :10px;
					float      :left;
				}
				.label{
					margin-left : 10px ;
					width       :30%;
					float       :left;
				}
				.input{
					width :40%;
					float :left;
				}
				.myTexrArea{
					border             : 0px solid #999;
					font-size          : 12px;
					border-radius      : 2px;
					-webkit-box-shadow : 0px 0px 3px #666;
					-moz-box-shadow    : 1px 1px 3px #999;
					box-shadow         : 1px 1px 3px #999;
					padding            : 0 0 0 5px;
				}
				.myfieldObligatorio{
					width:120px;
				}

			  </style>
			<div style="width:100%;height:100%;">
					<div style="height:20px;position: absolute;margin-left: 90;" id="divLoadContrato"></div>

					<div class="fila" style="margin-top:30px;">
						<div class="label">Fecha cancelacion</div>
						<div class="input"><input type="text" id="fecha_cancelacion" class="myfieldObligatorio"></div>
					</div>

					<div class="fila">
						<div class="label">Motivo</div>
						<div class="input"><select id="id_motivo_fin_contrato" class="myfieldObligatorio"><option value="">Seleccione...</option>'.$select.'</select></div>
					</div>

					<div class="fila">
						<div class="label">Observacion</div>
						<div class="input">
							<textarea rows="7" cols="25" class="myTexrArea" id="observacion_cancelacion"></textarea>
						</div>
					</div>

			</div>
			<script>
				new Ext.form.DateField({
				    format     : "Y-m-d",
				    width      : 130,
				    allowBlank : false,
				    showToday  : false,
				    applyTo    : "fecha_cancelacion",
				    editable   : false,
				    value : "'.date("Y-m-d").'",
				    listeners  : { select: function() {   } }
				});
			</script>
			';
	}

	//CANCELAR UN CONTRATO
	function cancelarContrato($id,$id_empleado,$fecha_cancelacion,$observacion_cancelacion,$id_motivo_cancelacion,$motivo_cancelacion,$id_empresa,$link){
		$sql="UPDATE empleados_contratos
				SET estado=1,
					fecha_cancelacion       = '$fecha_cancelacion',
					observacion_cancelacion = '$observacion_cancelacion',
					id_motivo_cancelacion   = '$id_motivo_cancelacion',
					motivo_cancelacion      = '$motivo_cancelacion'
				WHERE activo=1
					AND id_empleado=$id_empleado
					AND id_empresa=$id_empresa
					AND id=$id";
		$query=mysql_query($sql,$link);
		if ($query) {
			echo '<script>
					Actualiza_Div_empleados_contratos('.$id.');
					Win_Ventana_cancelar_contrato.close();
				</script>';
		}
		else{
			echo '<script>alert("Erro!\nNo se cancelo el documento, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function ventanaEntidadesEmpleado($id_empresa,$id_empleado,$id_contrato,$estado,$link){

		$sql="SELECT id_entidad,entidad,id_concepto,concepto FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
		$query=mysql_query($sql,$link);
		$body='';
		$cont=1;
		while ($row=mysql_fetch_array($query)) {
			$btnEliminar=($estado==0)? '<div style="float:right; min-width:55px;padding-top: 0px !important;"><div onclick="eliminarEntidadConcepto('.$cont.')" id="delete_'.$cont.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="images/delete.png"></div></div>
										<div onclick="ventana_trasladar_concepto('.$row['id_concepto'].','.$row['id_entidad'].','.$cont.')" id="traslate_'.$cont.'" title="Traslado" style="width:20px; float:left; margin-top:3px;margin-left: 8px;cursor:pointer;">
                                                <img src="images/traslado.png">
                                            </div>' : '' ;

			$body.='<div class="filaBoleta" id="fila_boleta_'.$cont.'">
						<div class="campo0" id="loadFila_'.$cont.'">'.$cont.'</div>
						<div class="campo2" id="entidad_'.$cont.'">'.$row['entidad'].'</div>
						<div class="campo2" id="concepto_'.$cont.'">'.$row['concepto'].'</div>
						'.$btnEliminar.'
						<input type="hidden" id="id_entidad_'.$cont.'" value="'.$row['id_entidad'].'">
						<input type="hidden" id="id_concepto_'.$cont.'" value="'.$row['id_concepto'].'">
					</div>';
			$cont++;
		}
		if ($estado==0) {
			$body.='<div class="filaBoleta" id="fila_boleta_'.$cont.'">
						<div class="campo0" id="loadFila_'.$cont.'">'.$cont.'</div>
						<div class="campo1" id="entidad_'.$cont.'"></div>
						<div class="campoImg" id="divImageBuscarEntidad_'.$cont.'" title="Buscar Entidad"><img src="images/buscar20.png" onclick="ventanaBuscarEntidad(\''.$cont.'\','.$id_contrato.')"></div>
						<div class="campo1" id="concepto_'.$cont.'"></div>
						<div class="campoImg" id="divImageBuscarConcepto_'.$cont.'" title="Buscar Concepto"><img src="images/buscar20.png" onclick="ventanaBuscarConcepto(\''.$cont.'\','.$id_contrato.')"></div>
						<div style="float:right; min-width:55px;padding-top: 0px !important;">
							<div onclick="guardarEntidadConcepto('.$cont.')" id="divImageSave_'.$cont.'" title="Guardar" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="images/save_true.png" ></div>
							<div onclick="eliminarEntidadConcepto('.$cont.')" id="delete_'.$cont.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/delete.png"></div>
							<div onclick="ventana_trasladar_concepto('.$row['id_concepto'].','.$row['id_entidad'].','.$cont.')" id="traslate_'.$cont.'" title="Traslado" style="width:20px; float:left; margin-top:3px;margin-left: 8px;cursor:pointer; display:none;">
                                <img src="images/traslado.png">
                            </div>
						</div>

						<input type="hidden" id="id_entidad_'.$cont.'" value="0">
						<input type="hidden" id="id_concepto_'.$cont.'" value="0">
					</div>';
		}
		echo '
			<style>
				#contenedor_formulario{
					overflow   : hidden;
					width      : calc(100% - 30px);
					height     : calc(100% - 10px);
					margin     : 15px;
					margin-top : 0px;
				}
				#contenedor_tabla_boletas{
					overflow              : hidden;
					width                 : calc(100% - 2px);
					height                : calc(100% - 5px);
					/*border              : 1px solid #d4d4d4;*/
					border                : 1px solid #D4D4D4 	;
					border-radius         : 4px;
					-webkit-border-radius : 4px;
					-webkit-box-shadow    : 1px 1px 1px #d4d4d4;
					-moz-box-shadow       : 1px 1px 1px #d4d4d4;
					box-shadow            : 1px 1px 1px #d4d4d4;
					background-color      :#F3F3F3;
				}
				.campoImg{
					float            : left;
					width            : 22px;
					border-right     : 1px solid #d4d4d4;
					background-color :#F3F3F3;
					padding-top      : 1px !important;
					height           : 22px !important;
					cursor           : hand;
				}
				.campo0{
					float            : left;
					width            : 26px;
					text-indent      : 5px;
					border-right     : 1px solid #d4d4d4;
					background-color:#F3F3F3;
					padding-top      : 0px !important;
					height: 22px !important;
				}

				.campo1{
					float            : left;
					width            : 152px;
					text-indent      : 5px;
					background-color : #FFF;
					border-right: 1px solid #d4d4d4;
					white-space:nowrap;
					text-overflow: ellipsis;
					overflow:hidden;
				}

				.campo2{
					float            : left;
					width            : 175px;
					text-indent      : 5px;
					background-color : #FFF;
					border-right: 1px solid #d4d4d4;
					white-space:nowrap;
					text-overflow: ellipsis;
					overflow:hidden;
				}

				.filaBoleta{ background-color:#F3F3F3; }

				.filaBoleta input[type=text]{
					border:0px;
					width: 90%;
					height: 100%;
				}

				.filaBoleta input[type=text]:focus { background: #FFF; }

				#bodyTablaBoletas{
					overflow-x       : hidden;
					overflow-y       : auto;
					width            : 100%;
					height           : calc(100% - 30px);
					background-color : #FFF;
					border-bottom    : 1px solid #d4d4d4;
				}

				#bodyTablaBoletas > div{
					overflow      : hidden;
					height        : 22px;
					border-bottom : 1px solid #d4d4d4;
				}

				#bodyTablaBoletas > div > div { height: 18px; /*background-color : #FFF;*/ padding-top: 4px; }

				.headTablaBoletas{
					overflow      : hidden;
					font-weight   : bold;
					width         : 100%;
					border-bottom : 1px solid #d4d4d4;
					height        : 22px;
				}

				.headTablaBoletas div{
					background-color :#F3F3F3;
					height           : 22px;
					padding-top      : 3;
				}
			</style>

			<div id="contenedor_formulario">
				<!-- <div class="loadSaveFormulario" id="loadSaveFormulario_"></div> -->

				<div id="contenedor_tabla_boletas">
					<div class="headTablaBoletas">
						<div class="campo0">&nbsp;</div>
						<div class="campo2">Entidad</div>
						<div class="campo2">Concepto</div>
					</div>
					<div id="bodyTablaBoletas">
						'.$body.'
					</div>

				</div>
			</div>
			<script>
				//GUARDAR LAS ENTIDADES DEL EMPLEADO EJ: EPS, ARL, ETC
    			function guardarEntidadConcepto(cont) {
    			    var id_entidad = document.getElementById("id_entidad_"+cont).value;
    			    var id_concepto = document.getElementById("id_concepto_"+cont).value;

    			    if (id_entidad==0) { alert("Seleccione la entidad!"); return; }
    			    if (id_concepto==0) { alert("Seleccione el concepto!"); return; }

    			    Ext.get("loadFila_"+cont).load({
    			        url     : "contratos/bd/bd.php",
    			        scripts : true,
    			        nocache : true,
    			        params  :
    			        {
    			            opc         : "guardarEntidadConcepto",
    			            cont        : cont,
    			            id_entidad  : id_entidad,
    			            id_concepto : id_concepto,
    			            id_empleado : "'.$id_empleado.'",
    			            id_contrato : "'.$id_contrato.'",
    			        }
    			    });

    			}

    			//ELIMINAR LAS ENTIDADES CON SUS CONCEPTOS
    			function eliminarEntidadConcepto(cont){
    				if (!confirm("Aviso\nRealmente desea eliminar el registro?")) {
    					return;
    				}

    				var id_entidad = document.getElementById("id_entidad_"+cont).value;
    			    var id_concepto = document.getElementById("id_concepto_"+cont).value;

    				Ext.get("loadFila_"+cont).load({
    			        url     : "contratos/bd/bd.php",
    			        scripts : true,
    			        nocache : true,
    			        params  :
    			        {
    			            opc         : "eliminarEntidadConcepto",
    			            cont        : cont,
    			            id_entidad  : id_entidad,
    			            id_concepto : id_concepto,
    			            id_empleado : "'.$id_empleado.'",
    			            id_contrato : "'.$id_contrato.'",
    			        }
    			    });
    			}

			</script>
		';
	}

	function guardarEntidadConcepto($id_empleado,$id_contrato,$cont,$id_entidad,$id_concepto,$id_empresa,$link){
		$sql="INSERT INTO empleados_contratos_entidades (id_empleado,id_contrato,id_entidad,id_concepto,id_empresa) VALUES
				('$id_empleado','$id_contrato','$id_entidad','$id_concepto','$id_empresa')";
		$query=mysql_query($sql,$link);
		if ($query) {
			$contOLD=$cont;
			$cont++;
			$body=' \'<div class="campo0" id="loadFila_'.$cont.'">'.$cont.'</div>\'+
					\'<div class="campo1" id="entidad_'.$cont.'"></div>\'+
					\'<div class="campoImg" id="divImageBuscarEntidad_'.$cont.'" title="Buscar Entidad"><img src="images/buscar20.png" onclick="ventanaBuscarEntidad('.$cont.')"></div>\'+
					\'<div class="campo1" id="concepto_'.$cont.'"></div>\'+
					\'<div class="campoImg" id="divImageBuscarConcepto_'.$cont.'" title="Buscar Concepto"><img src="images/buscar20.png" onclick="ventanaBuscarConcepto('.$cont.','.$id_contrato.')"></div>\'+
					\'<div style="float:right; min-width:55px;padding-top: 0px !important;">\'+
						\'<div onclick="guardarEntidadConcepto('.$cont.')" id="divImageSave_'.$cont.'" title="Guardar" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="images/save_true.png" ></div>\'+
						\'<div onclick="eliminarEntidadConcepto('.$cont.')" id="delete_'.$cont.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/delete.png"></div>\'+
						\'<div  id="traslate_'.$cont.'" title="Traslado" style="width:20px; float:left; margin-top:3px;margin-left: 8px;cursor:pointer;display:none;"><img src="images/traslado.png"></div>\'+
					\'</div>\'+
					\'<input type="hidden" id="id_entidad_'.$cont.'" value="0">\'+
					\'<input type="hidden" id="id_concepto_'.$cont.'" value="0">\'';
			// ELIMINAR ESPACIOS EN BLANCO
			// $body=trim($body);
			// echo $body;
			echo $contOLD.'<script>
								var fila=document.createElement("div");
								fila.setAttribute("class","filaBoleta");
								fila.setAttribute("id","fila_boleta_'.$cont.'");
								fila.innerHTML='.$body.';
								document.getElementById("bodyTablaBoletas").appendChild(fila);
								document.getElementById("divImageSave_'.$contOLD.'").style.display="none";
								document.getElementById("divImageBuscarConcepto_'.$contOLD.'").style.display="none";
								document.getElementById("divImageBuscarEntidad_'.$contOLD.'").style.display="none";
								document.getElementById("delete_'.$contOLD.'").style.display="block";
								document.getElementById("traslate_'.$contOLD.'").style.display="block";

								document.getElementById("traslate_'.$contOLD.'").setAttribute("onclick","ventana_trasladar_concepto('.$id_concepto.','.$id_entidad.','.$contOLD.')");

								document.getElementById("entidad_'.$contOLD.'").setAttribute("class","campo2");
								document.getElementById("concepto_'.$contOLD.'").setAttribute("class","campo2");
							</script>';

		}
		else{
			echo $cont.'<script>alert("Error!\nNo se Guardo el registro, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function eliminarEntidadConcepto($cont,$id_entidad,$id_concepto,$id_empleado,$id_contrato,$id_empresa,$link){
		$sql="DELETE FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=$id_empresa AND id_entidad=$id_entidad AND id_concepto=$id_concepto AND id_empleado=$id_empleado";
		$query=mysql_query($sql,$link);
		if ($query) {
			echo '<script>
					document.getElementById("fila_boleta_'.$cont.'").parentNode.removeChild(document.getElementById("fila_boleta_'.$cont.'"));
				</script>';
		}
		else{
			echo $cont.'<script>alert("Error!\nNo se elimino el registro, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function validar_centro_costo($id_ccos,$codigo_centro_costos,$centro_costos,$id_empresa,$link){
		$sql="SELECT COUNT(id) AS cont FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND codigo LIKE '$codigo_centro_costos%' AND codigo<>'$codigo_centro_costos' ";
		$query=mysql_query($sql,$link);
		$cont = mysql_result($query,0,'cont');
		if ($cont>0) {
			echo '<script>
					Actualiza_Div_centroCostos('.$id_ccos.');
					alert("Debe seleccionar un centro de costos hijo!");

				</script>';
		}
		else{
			echo "<script>
					document.getElementById('centro_costo').value='$codigo_centro_costos - $centro_costos';
					document.getElementById('centro_costo').dataset.value = '$id_ccos';
					Win_Ventana_buscar_centro_costos.close();
				</script>";
		}
	}

	function generar_traslado($id_contrato,$id_concepto,$id_empleado,$id_tercero_old,$id_tercero_traslado,$fecha_inicial,$fecha_final,$id_empresa,$cont,$link){
		$response = 'true{.}';
		//INSERTAR EL TRASLADO DEL EMPLEADO
		$sql="INSERT INTO empleados_contratos_entidades_traslados (id_contrato,id_empleado,id_entidad,fecha_inicio,fecha_final,id_concepto,id_empresa)
				VALUES($id_contrato,$id_empleado,$id_tercero_old,'$fecha_inicial','$fecha_final',$id_concepto,$id_empresa)";
		$query=mysql_query($sql,$link);
		if (!$query) { $response='false{.}'.$sql;}

		// ACTUALIZAR EL CAMPO ENTIDAD DE LOS CONTRATOS
		$sql="UPDATE empleados_contratos_entidades SET id_entidad=$id_tercero_traslado WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_concepto=$id_concepto";
		$query=mysql_query($sql,$link);
		if (!$query) { $response='false{.}'.$sql;}

		// CONSULTAR EL TERCERO PARA MOSTRARLO
		$sql="SELECT id,nombre FROM terceros WHERE activo=1 AND id_empresa=$id_empresa ANd id=$id_tercero_traslado";
		$query=mysql_query($sql,$link);
		$id_tercero = mysql_result($query,0,'id');
		$tercero    = mysql_result($query,0,'nombre');

		$response.=$id_tercero.'{.}'.$tercero.'{.}';

		echo $response;

	}
	//consulta la informacion del contrato y crea un duplicado
	function duplicar_contrato($id_contrato,$id_empresa,$link){
		$SQL = "SELECT * FROM empleados_contratos WHERE id=$id_contrato";
		$query = mysql_query($SQL,$link);

		$row = mysql_fetch_assoc($query);

		foreach ($row as $campo => $result) {
			if($campo == 'id' || $campo == 'estado' || $campo == 'vencimiento_firmado' || $campo == 'usuario_vencimiento_firmado' 
				|| $campo == 'fecha_vencimiento_firmado' || $campo == 'archivo_vencimiento_firmado'){ continue; }

			$result = '\''.$result.'\'';

			$campos  .= $campo.',';
			$valores .= $result.',';
		}
		//quito la coma
		$campos  .= 'estado';
		$valores .= 0;

		$SQL  = "INSERT INTO empleados_contratos($campos) VALUES($valores)";
		$query = mysql_query($SQL,$link);

		if($query){
			$id = mysql_insert_id();

			//copio tambien todas las entidades del contrato seleccionado
			$SQL   = "SELECT * FROM empleados_contratos_entidades WHERE id_contrato=$id_contrato AND activo = 1";
			$query = mysql_query($SQL,$link);

			while($row = mysql_fetch_assoc($query)){
				$array[$row['id']] = $row;
			}
			$i=0;
			$campos       = '';
			$valuesInsert = '';
			foreach ($array as $key => $result1) {
				$valores = '';
				foreach ($array[$key] as $key2 => $value2) {
				    if($key2 == 'id'||$key2 == 'id_contrato'){
						continue;
					}
					if($i == 0){
						$campos .= $key2.',';
					}
					$valores .= '\''.$value2.'\',';
				}
				$valuesInsert .= '('.$valores.$id.'),';
				$i++;
				//echo '<script>console.log("----------------------------------------------")</script>';
			}
			$campos = $campos.'id_contrato';
			$valuesInsert = substr($valuesInsert,0,-1);

			$SQL   = "INSERT INTO empleados_contratos_entidades($campos) VALUES $valuesInsert";
			$query = mysql_query($SQL,$link);
			//consultar entidades
			echo '<script> Inserta_Div_empleados_contratos('.$id.');Win_Ventana_agregar_contrato.close();</script>';
		}else{
			echo '<script>alert("Error!\nNo se Guardo el registro, intentelo de nuevo\nSi el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

?>