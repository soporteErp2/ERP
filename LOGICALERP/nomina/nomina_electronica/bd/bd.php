<?php

	// error_reporting(E_ALL);
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");
	//============================================= ARCHIVO GLOBAL PARA LOS MODULOS DE COTIZACION, PEDIDO, FACTURAS ======================================//
	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$electronicPayRollObject = new ElectronicPayRoll($id_planilla,$id_empresa,$link);

	switch ($opc) {
		case 'updateFields':
			$electronicPayRollObject->updateFields($data);
			break;
		case 'addEmployee':
			$electronicPayRollObject->addEmployee($id_contrato,$cont);
			break;
		case 'deleteEmployee':
			$electronicPayRollObject->deleteEmployee($id_contrato,$id_empleado);
			break;
			
		case 'showEmployeeConcepts':
			$electronicPayRollObject->showEmployeeConcepts($id_contrato);
			break;
		case 'showWorkedTimeWindow':
			$electronicPayRollObject->showWorkedTimeWindow($id_contrato,$id_empleado);
			break;
		case 'deleteElectronicPayroll':
			$electronicPayRollObject->deleteElectronicPayroll();
			break;
		case 'savePayRoll':
			$electronicPayRollObject->savePayRoll();
			break;
		case 'editElectronicPayroll':
			$electronicPayRollObject->editElectronicPayroll();
			break;
		case 'generate':
			$electronicPayRollObject->generate($id_empleado);
			break;
		default:
			// code...
			break;
	}


	/**
	 * 
	 */
	class ElectronicPayRoll
	{
		/* array with overtime types*/
		public $overTimeTypes = array(
										["type"=>"hora_extra_diurna","percent"=>25],
										["type"=>"hora_extra_nocturna","percent"=>75],
										["type"=>"hora_recargo_nocturno","percent"=>35],
										["type"=>"hora_recargo_diario_dominicales_y_festivas","percent"=>75],
										["type"=>"hora_extra_nocturna_dominicales_y_festivas","percent"=>100],
										["type"=>"hora_recargo_nocturno_dominicales_y_festivas","percent"=>150],
									);
		
		public $inabilityConcepts =  array(
										"licencia_maternidad_paternidad",
										"licencia_remunerada",
										"licencia_no_remunerada",
										);

		/* array with type gains concepts*/
	    public $gains = array('hora_extra_diurna',
    						'hora_extra_nocturna',
	    					'hora_recargo_nocturno',
	    					'hora_recargo_diario_dominicales_y_festivas',
	    					'hora_extra_nocturna_dominicales_y_festivas',
	    					'hora_recargo_nocturno_dominicales_y_festivas',
	    					'vacaciones',
	    					'prima',
	    					'cesantias',
							'intereses_de_cesantias',
	    					'incapacidad',
	    					'licencia',
	    					'salario',
	    					'auxilio_transporte',
	    					'viaticos_salariales',
	    					'viaticos_no_salariales',
	    					'bonififacion_salarial',
	    					'bonififacion_no_salarial',
	    					'auxilio_salarial',
	    					'auxilio_no_salarial',
	    					'comision_salarial',
	    					'comision_no_salarial',
	    					'apoyo_sostenimiento',
	    					'teletrabajo',
	    					'bonificacion_retiro',
	    					'indemnizacion',
	    					'reintegro',
							'licencia_maternidad_paternidad',
							'licencia_remunerada',
							'pago_terceros');

	    /* array with type deduction concepts*/
	   	public $deduction = array('salud',
	    						'pension',
	    						'fondo_solidaridad_pensional',
	    						'libranza',
		    					'otras_deducciones',
		    					'pension_voluntaria',
		    					'retefuente',
		    					'AFC',
		    					'cooperativa',
		    					'embargo_fiscal',
		    					'eps_plan_complementario',
		    					'educacion',
		    					'deuda',
								'pago_terceros_no_salarial',
								'anticipo_salarial', 
								'licencia_no_remunerada');

		function __construct($id_planilla,$id_empresa,$mysql)
		{
			$this->id_planilla = $id_planilla;
			$this->id_empresa  = $id_empresa;
			$this->mysql       = $mysql;
			$this->payRollInfo = new stdClass;

			/* get payroll information */
			$this->getPayRollInfo();
		}

		/**
		 * getPayRollInfo get payroll information 
		 * @return Array Array with the payRoll header information
		 */
		public function getPayRollInfo()
		{
			$sql="SELECT 
					NE.consecutivo,
					NE.codigo_tipo_documento,
					NE.fecha_inicio,
					NE.fecha_final,
					NE.id_sucursal,
					NE.estado,
					TL.codigo AS periodo_pago
				FROM nomina_planillas_electronica AS NE 
				LEFT JOIN nomina_tipos_liquidacion AS TL ON TL.id=NE.id_tipo_liquidacion
				WHERE NE.activo=1 
				AND NE.id_empresa=$this->id_empresa 
				AND NE.id=$this->id_planilla";
			$query=mysql_query($sql,$this->mysql);			
			
			$this->payRollInfo->consecutivo           = mysql_result($query,0,'consecutivo');
			$this->payRollInfo->codigo_tipo_documento = mysql_result($query,0,'codigo_tipo_documento');
			$this->payRollInfo->fecha_inicio          = mysql_result($query,0,'fecha_inicio');
			$this->payRollInfo->fecha_final           = mysql_result($query,0,'fecha_final');
			$this->payRollInfo->id_sucursal           = mysql_result($query,0,'id_sucursal');
			$this->payRollInfo->estado                = mysql_result($query,0,'estado');
			$this->payRollInfo->periodo_pago          = mysql_result($query,0,'periodo_pago');
		}

		/**
		 * getWorkContract get the work contract from employee
		 * @param Array Array with contracts id
		 * @return Array Contract information
		 */
		public function getWorkContract($contracts)
		{
			$ids = implode(",", $contracts);

			$sql="SELECT 
						empleados_contratos.*,
						IF(empleados_contratos.salario_integral='Si','true','false') AS salario_integral,
						nomina_tipo_contrato.codigo_dian AS 'codigo_tipo_contrato',
						nomina_configuracion_tipo_trabajador.codigo AS 'codigo_tipo_trabajador',
						nomina_configuracion_subtipo_trabajador.codigo AS 'codigo_subtipo_trabajador',
						nomina_configuracion_formas_pago.codigo AS 'codigo_forma_pago',
						nomina_configuracion_medios_pago.codigo AS 'codigo_medio_pago'
					FROM empleados_contratos 
					LEFT JOIN nomina_tipo_contrato ON nomina_tipo_contrato.id=empleados_contratos.id_tipo_contrato
					LEFT JOIN nomina_configuracion_tipo_trabajador ON nomina_configuracion_tipo_trabajador.id=empleados_contratos.id_tipo_trabajador
					LEFT JOIN nomina_configuracion_subtipo_trabajador ON nomina_configuracion_subtipo_trabajador.id=empleados_contratos.id_subtipo_trabajador
					LEFT JOIN nomina_configuracion_formas_pago ON nomina_configuracion_formas_pago.id=empleados_contratos.id_forma_pago
					LEFT JOIN nomina_configuracion_medios_pago ON nomina_configuracion_medios_pago.id=empleados_contratos.id_medio_pago
					WHERE empleados_contratos.activo=1 
					AND empleados_contratos.id_empresa=$this->id_empresa 
					AND empleados_contratos.id IN ($ids)";
			$query=mysql_query($sql,$this->mysql);
			$retVal = [];
			while ($row=mysql_fetch_assoc($query)) {
				$retVal[$row["id"]] = $row;
			}

			return $retVal;
		}

		/**
		 * updateFields Update fields in a table on the db
		 * @param  Array $data Array 	with the data requiered for the update
		 * @param  Array $data[rows] 	Array with field and new value to update, like [["field"=>"xxx","newValue"=>"xxx"]...]
		 * @return String $data[table]  Table name to update
		 * @return String $data[where]  Where condition to update
		 */
		public function updateFields($data)
		{	
			$data = json_decode($data,true);
			// SET THE STRING TO UPDATE
			$fields = "";
			foreach ($data["rows"] as $key => $value) {
				$fields .= "$value[field]='$value[newValue]' ";
			}

			$sql="UPDATE $data[table] SET $fields WHERE $data[where]";
			$query=mysql_query($sql,$this->mysql);
			
			$response = (!$query)? ["status"=>"error","detail"=>mysql_error()] : ["status"=>"success"] ;
			if (!$query) {
				echo "<script>
						alert(\"".mysql_error()."\");
					</script>";
			}
		}

		/**
		 * getPayRollsData get all concepts from all types of payrolls
		 * @param  [array] $contractInfo employees current workcontract information
		 * @return [array]  list with all conceptos from the payroll between given dates             
		 */
		public function getPayRollsData($contractInfo)
		{
			/* get payroll data*/
			$sql = "SELECT
						NP.id,
						NPE.dias_laborados,
						NPE.id_empleado,
						NPEC.id_concepto,
						NPEC.concepto,
						NPEC.codigo_concepto,
						NPEC.valor_campo_texto,
						NPEC.valor_concepto,
						C.clasificacion
					FROM
						nomina_planillas AS NP
					INNER JOIN nomina_planillas_empleados AS NPE ON NPE.id_planilla = NP.id
					INNER JOIN nomina_planillas_empleados_conceptos AS NPEC ON NPEC.id_empleado = NPE.id_empleado
					AND NPEC.id_planilla = NP.id
					INNER JOIN nomina_conceptos AS C ON C.id = NPEC.id_concepto
					WHERE
						NP.activo = 1
					AND (NP.estado = 1 OR NP.estado = 2)
					AND NP.id_empresa = $this->id_empresa
					AND NP.fecha_inicio >= '".$this->payRollInfo->fecha_inicio."'
					AND NP.fecha_final <= '".$this->payRollInfo->fecha_final."'
					AND NPE.id_planilla = NP.id
					AND NPE.id_empleado = '$contractInfo[id_empleado]'
					AND C.clasificacion IS NOT NULL
					AND C.clasificacion <> ''
					AND C.clasificacion NOT IN ('vacaciones','prima','cesantias')
					AND C.naturaleza NOT IN ('Provision','Apropiacion');";
			$query=mysql_query($sql,$this->mysql);
			while($row=mysql_fetch_assoc($query)){
				/* get concepts data */
				$data = $this->getConceptsData('LN',$row['id'],$row['id_empleado'],$row['id_concepto']);
				$row['data'] = $this->setConceptArrayStructure($row,$data,$row['clasificacion']);
				$arrayConceptos[] = $row;
			}

			/* get payroll liquidation*/
			$sql = "SELECT
						NP.id,
						NPE.dias_laborados,
						NPE.id_empleado,
						NPEC.id_concepto,
						NPEC.codigo_concepto,
						NPEC.concepto,
						NPEC.valor_campo_texto,
						NPEC.valor_concepto,
						NPEC.valor_concepto_ajustado,
						SUM(NPEC.dias_laborados+NPEC.dias_adicionales) AS dias_liquidados,
						C.clasificacion
					FROM
						nomina_planillas_liquidacion AS NP
					INNER JOIN nomina_planillas_liquidacion_empleados AS NPE ON NPE.id_planilla = NP.id
					INNER JOIN nomina_planillas_liquidacion_empleados_conceptos AS NPEC ON NPEC.id_empleado = NPE.id_empleado
					AND NPEC.id_planilla = NP.id
					INNER JOIN nomina_conceptos AS C ON C.id = NPEC.id_concepto
					WHERE
						NP.activo = 1
					AND (NP.estado = 1 OR NP.estado = 2)
					AND NP.id_empresa = $this->id_empresa
					AND NP.fecha_documento >= '".$this->payRollInfo->fecha_inicio."'
					AND NP.fecha_documento <= '".$this->payRollInfo->fecha_final."'
					AND NPE.id_planilla = NP.id
					AND NPE.id_empleado = '$contractInfo[id_empleado]'
					AND C.clasificacion IS NOT NULL
					AND C.clasificacion <> ''
					GROUP BY NPEC.id;";
			$query=mysql_query($sql,$this->mysql);
			while($row=mysql_fetch_assoc($query)){
				$row["valor_concepto"] = ($row['valor_concepto_ajustado']>0)? $row['valor_concepto_ajustado'] : $row["valor_concepto"];
				/* get concepts data */
				$data = $this->getConceptsData('LE',$row['id'],$row['id_empleado'],$row['id_concepto']);				
				
				$row['data'] = $this->setConceptArrayStructure($row,$data,$row['clasificacion']);
				$arrayConceptos[] = $row;

			}
			return $arrayConceptos;
		}

		/**
		 * getConceptsData get json data from the payroll for each concept
		 * @param  [String] $tipo_planilla payroll type (LN : Liquidacion Nomina, LE : Liquidacion Empleado)
		 * @param  [int] $id_planilla   payroll id
		 * @param  [int] $id_empleado   employee id
		 * @param  [int] $id_concepto   concept id
		 * @return [array]              list with all data from the concept of the payroll
		 */
		public function getConceptsData($tipo_planilla,$id_planilla,$id_empleado,$id_concepto)
		{
			$sql = "SELECT data FROM nomina_planillas_empleados_conceptos_datos_nomina_electronica
					WHERE tipo_planilla = '$tipo_planilla' AND
					id_planilla = '$id_planilla' AND
					id_empleado = '$id_empleado' AND
					id_concepto = '$id_concepto';";
			$query=mysql_query($sql,$this->mysql);
			while($row = mysql_fetch_assoc($query)){
				$retVal[] = $row;
			}
			return $retVal;
		}

		/* create concept array structure */
		public function setConceptArrayStructure($concept,$data,$clasificacion)
		{
			$assign=null;
			/* overtime concepts*/
			if (in_array($clasificacion,array_column( $this->overTimeTypes),"type")) {
				foreach ($data as $key => $value) {
					$structure='';
					/* order array in a minimal and order array*/
					foreach (json_decode($value['data'],true) as $key => $detail) {
						$structure[$detail['name']]=$detail['value'];
					}
					$assign[] = [
									"HoraInicio" => "$structure[fecha_inicio]T$structure[hora_inicio]:00",
									"HoraFin"    => "$structure[fecha_fin]T$structure[hora_fin]:00",
									"Cantidad"   => $structure['cantidad'],
									"Porcentaje" => $structure['porcentaje'],
									"Pago"       => $structure['valor']

					];					
				}
			}
			elseif ($clasificacion=="incapacidad") {
				foreach ($data as $key => $value) {
					$structure='';
					/* order array in a minimal and order array*/
					foreach (json_decode($value['data'],true) as $key => $detail) {
						$structure[$detail['name']]=$detail['value'];
					}
					$assign[] = [
								"FechaInicio" => $structure['fecha_inicio'],
								"FechaFin"    => $structure['fecha_fin'],
								"Cantidad"    => $concept['valor_campo_texto'],
								"Tipo"        => $structure['tipo'],
								"Pago"        => $concept['valor_concepto']

					];					
				}
			}
			elseif (in_array($clasificacion,$this->inabilityConcepts)) {
				foreach ($data as $key => $value) {
					$structure='';
					/* order array in a minimal and order array*/
					foreach (json_decode($value['data'],true) as $key => $detail) {
						$structure[$detail['name']]=$detail['value'];
					}
					$assign[] = [
								"FechaInicio" => $structure['fecha_inicio'],
								"FechaFin"    => $structure['fecha_fin'],
								"Cantidad"    => $concept['valor_campo_texto'],
								"Pago"        => $concept['valor_concepto']

					];					
				}
			}
			elseif ($clasificacion=="cesantias") {
				foreach ($data as $key => $value) {
					$structure='';
					/* order array in a minimal and order array*/
					foreach (json_decode($value['data'],true) as $key => $detail) {
						$structure[$detail['name']]=$detail['value'];
					}
					$assign[] = [
								"Pago"          => $concept['valor_concepto'],
								"Porcentaje"    => $structure['porcentaje'],
								"PagoIntereses" => $structure['pago_intereses'],
					];					
				}
			}
			elseif ($clasificacion=="prima") {
				$assign[] = [
							"Cantidad" => $concept['dias_liquidados'],
							"Pago"     => $concept['valor_concepto'],
							"PagoNS"   => 0,
					];
			}
			elseif ($clasificacion=="fondo_solidaridad_pensional") {
				foreach ($data as $key => $value) {
					$structure='';
					/* order array in a minimal and order array*/
					foreach (json_decode($value['data'],true) as $key => $detail) {
						$structure[$detail['name']]=$detail['value'];
					}
					$assign[] = [
								"Porcentaje"    => $structure['porcentaje'],
								"Deduccion"     => $structure['deduccion'],
								"PorcentajeSub" => $structure['porcentaje_fondo_subsistencia'],
								"DeduccionSub"  => $structure['deduccion_fondo_subsistencia'],

					];					
				}
			}
			

			return $assign;
		}

		/**
		 * getWorkedDays get worked days between dates period
		 * @param  [int] $id_empleado employee's id
		 * @return int 		employee worked days              
		 */
		public function getWorkedDays($id_empleado)
		{
			$sql = "SELECT
						SUM(dias_laborados) AS dias_laborados
					FROM
						nomina_planillas AS NP,
						nomina_planillas_empleados AS NPE
					WHERE
						NP.activo = 1
					AND (NP.estado = 1 OR NP.estado = 2)
					AND NP.id_empresa = $this->id_empresa
					AND NP.fecha_inicio >= '".$this->payRollInfo->fecha_inicio."'
					AND NP.fecha_final <= '".$this->payRollInfo->fecha_final."'
					AND NPE.id_planilla = NP.id
					AND NPE.id_empleado = '$id_empleado'";
			$query=mysql_query($sql,$this->mysql);
			return mysql_result($query,0,'dias_laborados');
		}

		/**
		 * addEmployee add an employee
		 * @param [int] $id_contrato id of workcontract 
		 * @param [int] $cont        list count
		 */
		public function addEmployee($id_contrato,$cont){
			$fecha=date("Y-m-d");	

			/* get employee contract  information */
			$contractInfo = $this->getWorkContract([$id_contrato]);

			$workedDays = $this->getWorkedDays($contractInfo[$id_contrato]['id_empleado']);
			
			/* get payrolls data for json */
			$payRollsData = $this->getPayRollsData($contractInfo[$id_contrato]);
			// print_r($payRollsData);
			
			/**/
			if (count($payRollsData)==0) {
				echo "<script>alert('No es posible agregar al empleado por que no tiene datos en ese periodo de tiempo seleccionado en la plantilla')</script>";
				exit;
			}

			/*employee*/
			$sql="INSERT INTO nomina_planillas_electronica_empleados 
					(
						id_planilla,
						id_empleado,
						tipo_documento,
						documento_empleado,
						nombre_empleado,
						dias_laborados,
						id_contrato,
						verificado,
						observaciones,
						id_empresa
					) VALUES 
					(
						'$this->id_planilla',
						'".$contractInfo[$id_contrato]['id_empleado']."',
						'".$contractInfo[$id_contrato]['tipo_documento_empleado']."',
						'".$contractInfo[$id_contrato]['documento_empleado']."',
						'".$contractInfo[$id_contrato]['nombre_empleado']."',
						'$workedDays',
						'$id_contrato',
						'false',
						'',
						$this->id_empresa
					)";
			$query=mysql_query($sql,$this->mysql);
			// print_r($payRollsData);
			/*concepts*/
			$insertString = "";
			foreach ($payRollsData as $key => $data) {
				$data['data'] = json_encode($data['data']);
				$insertString .= "(
										'$this->id_planilla',
										'".$contractInfo[$id_contrato]['id_empleado']."',
										'$id_contrato',
										'$data[id_concepto]',
										'$data[codigo_concepto]',
										'$data[concepto]',
										'$data[valor_concepto]',
										'$data[valor_campo_texto]',										
										'$data[data]',
										'$this->id_empresa'
									),";
			}
			$insertString = substr($insertString,0,-1);
			$sql="INSERT INTO nomina_planillas_electronica_empleados_conceptos 
					(
						id_planilla,
						id_empleado,
						id_contrato,
						id_concepto,
						codigo_concepto,
						concepto,
						valor_concepto,
						valor_campo_texto,
						data,
						id_empresa
					) VALUES $insertString";
			$query=mysql_query($sql,$this->mysql);

			if ($query) {
				?>

				<script>
					//AGREGAR EL EMPLEADO A LA PLANILLA DE NOMINA
					let div=document.createElement("div");
					div.setAttribute("class","bodyDivNominaPlanilla");
					let content = `<div class="campo" id="divLoadEmpleado_<?= $id_contrato ?>"><?=$cont?></div>
									<div class="campo" style="margin-left: -20px;border: none;width: 10px;margin-top: 1px;display:none;" id="fila_selected_<?=$id_contrato?>"><img src="img/fila_selected.png"></div>
                    				<div class="campo1" onclick="cargarConceptosEmpleado('<?=$id_contrato?>','<?= $contractInfo[$id_contrato]['id_empleado'] ?>');" style="width:100px;text-indent:5px;"><?= $contractInfo[$id_contrato]['documento_empleado'] ?></div>
                    				<div class="campo1" onclick="cargarConceptosEmpleado('<?=$id_contrato?>','<?= $contractInfo[$id_contrato]['id_empleado'] ?>');" style="width:calc(100% - 100px - 49px - 20px);text-indent:5px;"><?= $contractInfo[$id_contrato]['nombre_empleado'] ?></div>
                    				<!--<div onclick="verificaEmpleado('<?=$id_contrato?>','.$id_empleado.','<?=$id_contrato?>')"  title="Verificar Empleado" class="iconBuscar" style="margin-left: -1px;" >
                            		    <img class="capturaImgCheck" src="img/checkbox_false.png" value="false" id="verifica_empleado_<?=$id_contrato?>">
                            		</div>-->
                    				<div onclick="eliminarEmpleado('<?=$id_contrato?>','<?= $contractInfo[$id_contrato]['id_empleado'] ?>')" title="Eliminar Empleado" class="iconBuscar" style="margin-left: -1px;">
                    				    <img src="img/delete.png">
                    				</div>
									`;

					div.innerHTML= content;

                    document.getElementById("contenedorEmpleados").appendChild(div);
                    contEmpleados++;
                    //ELIMINAR EL EMPLEADO DE LA GRILLA DE BUSQUEDA
					document.getElementById("item_buscarEmpleadosPlanilla_<?=$id_contrato?>").parentNode.removeChild(document.getElementById("item_buscarEmpleadosPlanilla_<?=$id_contrato?>"));
					calcularValoresPlanilla();
				</script>';

				<?php
			}
			else{
				?>
					<script>
						alert("ocurrio un error al agregar los conceptos, intentelo de nuevo");
					</script>
				<?php
			}		
		}

		/**
		 * showEmployeeConcepts show all employee concepts
		 * @param  [int] $id_contrato employee id workcontract
		 */
		public function showEmployeeConcepts($id_contrato)
		{	

			/* get employee contract  information */
			$contractInfo = $this->getWorkContract([$id_contrato]);

			?>

				<div style="width:100%; height:35px;text-transform: uppercase;font-weight:bold;font-size:18px;color:#999;text-indent: 10px;line-height:1.5;">
					<?= $contractInfo[$id_contrato]['nombre_empleado'] ?>
				</div>
				<?php
					/* get employee concepts*/
            		$sql =  "SELECT 
            					prefijo,
								consecutivo,
								codigo_tipo_ajuste,
								planilla_relacionada_al_ajuste
        					 FROM nomina_planillas_electronica_empleados
        					 WHERE 
        					 id_planilla = $this->id_planilla
        					 AND id_empleado = ".$contractInfo[$id_contrato]['id_empleado'];
					$query=mysql_query($sql,$this->mysql);
					$prefijo = mysql_result($query,0,'prefijo');
					$consecutivo                    = mysql_result($query,0,'consecutivo');
					$codigo_tipo_ajuste             = mysql_result($query,0,'codigo_tipo_ajuste');
					$planilla_relacionada_al_ajuste = mysql_result($query,0,'planilla_relacionada_al_ajuste');
	    			if ($prefijo<>'') {
				?>
				<div style="width:100%; height:35px;text-transform: uppercase;font-weight:bold;font-size:14px;color:#999;text-indent: 10px;line-height:1.5;">
					Nomina Electronica <?=$prefijo?> <?=$consecutivo?>
				</div>
				<?php
        			}
        		?>
				<div style="float:left;  width: calc(100% - 50% - 10px);">

                	<div class="renglonTop" style="margin-left:10px;float:none;width: 95%;margin-top:5px;min-height:0px;">
                		
                		<?php
                			if ($this->payRollInfo->estado==0) {
                				?>

                					<div class="labelTop" id="div_contenedor_libro_vacaciones_campo" style="border:1px solid #d4d4d4;width:calc(100% - 51%);float:left;height:23px;border-top:none;border-right:none;cursor:hand;" onclick="ventana_fechas_pago(<?=$contractInfo[$id_contrato]['id_empleado']?>)">
				            	    	<div style="float:left;"><img src="img/libro_vacaciones.png"></div><div style="float:left;line-height: 2;">Fechas de Pago</div>
				            	    </div>
				            	    <div class="labelTop" id="div_contenedor_libro_vacaciones_campo" style="border:1px solid #d4d4d4;width:calc(100% - 50%);float:left;height:23px;border-top:none;border-right:none;cursor:hand;" onclick="ventana_tiempo_laborado(<?=$contractInfo[$id_contrato]['id_empleado']?>,<?=$id_contrato?>)">
				            	    	<div style="float:left;"><img src="img/libro_vacaciones.png"></div><div style="line-height: 2;">Tiempo laborado</div>
				            	    </div>

                				<?php
                			}
                		?>
                	</div>
            	</div>

            	<?php
            		if ($this->payRollInfo->codigo_tipo_documento==103) {
            			?>

    			<div style="float:left;  width: calc(100% - 50% - 10px);">
                	<div class="renglonTop" style="margin-left:10px;float:none;width: 95%;margin-top:5px;min-height:0px;border:none;">
                	    <div class="labelTop" style="width:45%;float:left;height:20px;border-bottom:0px;border:1px solid #d4d4d4;">
                	    	Tipo de Ajuste
                	    </div>
                	    <div class="campoTop" style="width:calc(55% - 4px);border:1px solid #d4d4d4;border-left:none;">
                	    	<select onchange="UpdateTipoAjusteNE(this.value,<?= $contractInfo[$id_contrato]['id_empleado'] ?>)" <?php if($this->payRollInfo->estado<>0){ echo 'disabled';} ?> >
                	    		<option>Seleccione...</option>
                	    	<?php
                	    		$types = $this->getAdjustmentType();
                	    		
                	    		foreach ($types as $key => $row) {
                	    			$selected = ($codigo_tipo_ajuste==$row['codigo'])? "selected" : "" ;
            	    				?>
            	    				<option value="<?=$row['codigo']?>" <?= $selected ?> ><?=$row['codigo']?> - <?=$row['nombre']?></option>
            	    				<?php
                	    		}
                	    		// planilla_relacionada_al_ajuste
                	    	?>
                	    	</select>
                	    </div>
                	    <div id="divLoadAjuste" style="width: 20px;height: 18px;position: absolute;margin-left: 250;display: none;"></div>
                	    <div class="labelTop" style="width:45%;float:left;height:20px;border-bottom:0px;border:1px solid #d4d4d4;">
                	    	Planilla a ajustar
                	    </div>
                	    <div class="campoTop" style="width:calc(55% - 4px);border:1px solid #d4d4d4;border-left:none;">
                	    	<input type="text" id="consecutivo_planilla_cruce" style="width:calc(100% - 20px)" readonly value="<?= $planilla_relacionada_al_ajuste ?>">
                	    	<img src="img/find.png" onclick="ventana_planilla_cruce(<?= $contractInfo[$id_contrato]['id_empleado'] ?>)" style="width: 20px;float:right;cursor: pointer; <?php if($this->payRollInfo->estado<>0){ echo 'display: none;'; }?>" title="Relacionar planilla a ajustar">
                	    </div>
                	</div>
            	</div>

            			<?php
            		}
            	?>
            	

            	<div class="headConceptos" >
                	<div class="bodyDivNominaPlanilla" style="border-bottom:none;" id="headConceptos">
                        <div class="campo" style=""></div>
                        <div class="campoHeadConceptos" style="width:calc(100% - 50% - 70px - 25px );">Concepto  </div>
                        <!-- <div class="campoHeadConceptos" style="width:30px;">Dias</div> -->
                        <!-- <div class="campoHeadConceptos" style="width:50px;" title="Dias Adicionales">Dias + </div> -->
                        <div class="campoHeadConceptos" style="width:100px;" title="Valor">Clasificacion</div>
                        <div class="campoHeadConceptos" style="width:70px;" title="Valor">Valor</div>
                        <!-- <div class="campoHeadConceptos" style="width:30px;text-align:center;" title="Naturaleza del Concepto">Nat.</div> -->
                    </div>
                </div>
                <div class="contenedorConceptos" id="contenedorConceptos" >
                	<?php
                		/* get employee concepts*/
                		$sql =  "SELECT 
                					NC.id,
									NC.id_planilla,
									NC.id_empleado,
									NC.id_contrato,
									NC.id_concepto,
									NC.codigo_concepto,
									NC.concepto,
									NC.valor_concepto,
									NC.data,
									C.naturaleza,
									C.clasificacion
            					 FROM nomina_planillas_electronica_empleados_conceptos AS NC
            					 INNER JOIN nomina_conceptos AS C ON C.id=NC.id_concepto
            					 WHERE NC.activo=1
            					 AND NC.id_planilla = $this->id_planilla
            					 AND NC.id_empleado = ".$contractInfo[$id_contrato]['id_empleado'];
						$query=mysql_query($sql,$this->mysql);
						$cont = 1;
						$gainsAcum = 0;
						$deductionAcum = 0;
						while($row=mysql_fetch_array($query)){
							/* ---- OPCION USANDO LA NATURALEZA --- */
							/*$gainsAcum += ($row["naturaleza"]=="Devengo")? $row["valor_concepto"] : 0 ;*/
							/*#$deductionAcum += ($row["naturaleza"]=="Deduccion")? $row["valor_concepto"] : 0 ;*/
							
							$gainsAcum += (in_array($row["clasificacion"],$this->gains))? $row["valor_concepto"] : 0 ;
							$deductionAcum += (in_array($row["clasificacion"],$this->deduction))? $row["valor_concepto"] : 0 ;
							
							$botones=($this->payRollInfo->estado==1 || $this->payRollInfo->estado==3 )? '<div style="float:left;margin-left:10px; min-width:60px;">
														<div onclick="ventanaConfigurarCuentasConcepto('.$cont.')" id="divImageConfiConcepto_'.$cont.'" title="Ver configuracion" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/config16.png"></div>
														</div>'
														: '<div style="float:left;margin-left:10px; min-width:60px;">
                        	     						    <div onclick="guardarConcepto('.$cont.',\'actualizarconcepto\')" id="divImageSaveConcepto_'.$cont.'" title="Actualizar Concepto" style="width:20px; float:left; margin-top:3px;cursor:pointer;display:none;"><img src="img/reload.png" id="ImageSaveConcepto_'.$cont.'"></div>
                        	     						    <div onclick="ventanaConfigurarCuentasConcepto('.$cont.')" id="divImageConfiConcepto_'.$cont.'" title="Configurar Cuentas" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="img/config16.png"></div>
                        	     						    <div onclick="alert(\'No se pueden eliminar, se deben reportar a la dian\');return;eliminarConcepto('.$cont.')" id="deleteConcepto_'.$cont.'" title="Eliminar Concepto" style="width:20px; float:left; margin-top:3px; cursor:pointer;"><img src="img/delete.png"></div>
                        	     						</div>' ;
							/* show concepts*/
							?>
								<div class="bodyDivNominaPlanilla">
	                        	     <div class="campo" id="divLoadConcepto_<?=$cont?>"><?=$cont?></div>

	                        	     <div class="campo1" id="concepto_<?=$cont?>" style="width:calc(100% - 50% - 70px - 18px );text-indent:5px;" title="<?=$row['concepto']?>" ><?=$row['concepto']?></div>
	                        	     <div class="campo1" id="concepto_<?=$cont?>" style="width:100px;text-indent:5px;" title="<?=$row['clasificacion']?>"><?=$row['clasificacion']?></div>
	                        	     <div class="campo1" style="width:70px;text-indent:0;">
	                        	     	<input type="text" style="width:100%;border-right:1px solid #d4d4d4;padding-right: 0px;"  id="input_calculo_<?=$cont?>" value="<?=$row['valor_concepto']?>" readonly>
	                        	     </div>

	                        	     <!-- <div class="campo1" id="naturaleza_'.$cont.'" style="width:30px;text-align:center;" title="'.$row['naturaleza'].'"><img src="img/'.$row['naturaleza'].'.png" ></div> -->
	                        	     <!--<div class="campo1" id="imprimir_volante_'.$cont.'" style="width:30px;text-align:center;text-indent:0px;" title="'.$titlePrint.'"><img src="img/'.$row['imprimir_volante'].'.png"></div>-->
	                        	     <!-- <?=$botones?> -->
	                        	     <input type="hidden" id="id_insert_concepto_<?=$cont?>" value="<?=$row['id']?>">
	                        	     <input type="hidden" id="id_concepto_<?=$cont?>" value="<?=$row['id_concepto']?>">
	                        	     <input type="hidden" id="id_contrato_concepto_<?=$cont?>" value="<?=$id_contrato?>">
	                        	     <input type="hidden" id="id_empleado_concepto_<?=$cont?>" value="<?=$id_empleado?>">
	                        	 </div>

							<?php
							// if concept type is intereses cesantias, add new row to show it
							// if ($row["clasificacion"]=='cesantias') {
							// 	$json_decode = json_decode($row["data"],true);
							// 	$interest = $json_decode[0]['PagoIntereses'];
							// 	$gainsAcum += $interest;
							// 	$cont++;
							// 	?>
							 		<!-- <div class="bodyDivNominaPlanilla"> -->
		                         	     <!-- <div class="campo" id="divLoadConcepto_<?=$cont?>"><?=$cont?></div> -->

		                         	     <!-- <div class="campo1" id="concepto_<?=$cont?>" style="width:calc(100% - 50% - 70px - 18px );text-indent:5px;" title="<?=$row['concepto']?>" >Intereses de cesantias</div> -->
		                         	     <!-- <div class="campo1" id="concepto_<?=$cont?>" style="width:100px;text-indent:5px;" title="<?=$row['clasificacion']?>">intereses_cesantias</div> -->
		                         	     <!-- <div class="campo1" style="width:70px;text-indent:0;"> -->
		                         	     	<!-- <input type="text" style="width:100%;border-right:1px solid #d4d4d4;padding-right: 0px;"  id="input_calculo_<?=$cont?>" value="<?= $interest?>" readonly> -->
		                         	     <!-- </div> -->

		                         	     <!-- <div class="campo1" id="naturaleza_'.$cont.'" style="width:30px;text-align:center;" title="'.$row['naturaleza'].'"><img src="img/'.$row['naturaleza'].'.png" ></div>
		                         	     <div class="campo1" id="imprimir_volante_'.$cont.'" style="width:30px;text-align:center;text-indent:0px;" title="'.$titlePrint.'"><img src="img/'.$row['imprimir_volante'].'.png"></div> -->

										 <!-- <input type="hidden" id="id_insert_concepto_<?=$cont?>" value="<?=$row['id']?>"> -->
		                         	     <!-- <input type="hidden" id="id_concepto_<?=$cont?>" value="<?=$row['id_concepto']?>"> -->
		                         	     <!-- <input type="hidden" id="id_contrato_concepto_<?=$cont?>" value="<?=$id_contrato?>"> -->
		                         	     <!-- <input type="hidden" id="id_empleado_concepto_<?=$cont?>" value="<?=$id_empleado?>"> -->
		                         	 <!-- </div> -->

							 	<?php
							// }
							$cont++;

						}
                	?>

            	</div>
                <div class="contenedorConceptos" style="height:auto;margin-top:10px;">
					<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);"> 
						<div class="campoHeadConceptos" style="width:22px;border-right:none;"><p style="float:left;" title="Devengo"><img src="img/Devengo.png"></p></div>
						<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalDevengo"> $<?= number_format($gainsAcum,0,'.',','); ?></div>
					</div>
					<div class="bodyDivNominaPlanilla" style="float:left;background-color:#FFF;width:calc((100% - 5px) / 4);">
						<div class="campoHeadConceptos" style="width:22px;border-left:1px solid #d4d4d4;border-right:none;"><p style="float:left;" title="Deduccion"><img src="img/Deduccion.png"></p></div>
						<div class="campo1" style="width:calc(100% - 26px);height:auto;text-align:right;font-weight:bold;float:left;border-right:none;" id="totalDeduccion"> $ <?= number_format($deductionAcum,0,'.',',') ?></div>
					</div>
					<div class="bodyDivNominaPlanilla" style="background-color:#FFF;border-bottom:none;width:100%;">
						<div class="campoHeadConceptos" style="padding-right:5px;padding-left:5px;text-align: center;background-color : #F3F3F3;">Neto  Empleado</div>
						<div class="campo1" style="width:18px;border-right:1px solid;border-right: 1px solid #d4d4d4;text-align:center;font-weight:bold;">$</div>
						<div class="campo1" style="width:auto;padding: 0 10 0 10;font-weight:bold;border-right:none;" id="totalNetoPagar"><?= number_format(($gainsAcum-$deductionAcum),0,'.',',') ?></div>
					</div>
				</div>
        	
        	<?php
		}

		/**
		 * deleteEmployee delete an employee from the database
		 * @param  [int] $id_contrato employee id workcontract
		 * @param  [int] $id_empleado employee id
		 */
		public function deleteEmployee($id_contrato,$id_empleado)
		{
			$payrollEmployee = $this->getEmployeePayRollInfo($id_empleado);
			if ($payrollEmployee['consecutivo']>0) {
				echo '<script>
        				MyLoading2("off",{texto:"No se puede eliminar un empleado enviado a la dian",icono:"warning","duracion":5000});
					</script>';
				return;
			}


			$sql = "DELETE FROM nomina_planillas_electronica_empleados_conceptos 
					WHERE activo=1 AND
					id_planilla='$this->id_planilla' AND
					id_empleado='$id_empleado' AND
					id_contrato='$id_contrato' AND
					id_empresa='$this->id_empresa' ";
			$query=mysql_query($sql,$this->mysql);
			if (!$query) { echo "<script>alert('No se eliminaron los conceptos')</script>";}

			$sql = "DELETE FROM nomina_planillas_electronica_empleados 
					WHERE activo=1 AND
					id_planilla='$this->id_planilla' AND
					id_empleado='$id_empleado' AND
					id_contrato='$id_contrato' AND
					id_empresa='$this->id_empresa' ";
			$query=mysql_query($sql,$this->mysql);
			if (!$query) { 
				echo '<script>
        				MyLoading2("off",{texto:"No se elimino el empleado",icono:"failed"});
					</script>';
			}
			else{
				echo '<script>
						document.getElementById("divLoadEmpleado_'.$id_contrato.'").parentNode.parentNode.removeChild(document.getElementById("divLoadEmpleado_'.$id_contrato.'").parentNode);
        				MyLoading2("off",{texto:"empleado eliminado"});
					</script>';
			}
		}

		public function showWorkedTimeWindow($id_contrato,$id_empleado)
		{
			header('Content-Type: text/html; charset=utf-8');
			$sql = "SELECT tiempo_laborado 
					FROM nomina_planillas_electronica_empleados 
					WHERE activo=1 
					AND id_empresa=$this->id_empresa 
					AND id_planilla=$this->id_planilla
					AND id_empleado= $id_empleado";
			$query=mysql_query($sql,$this->mysql);
			$days = mysql_result($query,0,"tiempo_laborado");

			?>
				<style>
					.table-form tr td button{
						border           : none;
						background-color : #008CBA;
						color            : #FFF;
						padding          : 15px;
						cursor           : pointer;
					}
					.table-form tr td button:hover{
						background-color : #5ca3bb;						
					}
				</style>
				<table class="table-form">
					<tr class="thead">
						<td colspan="2">TIEMPO LABORADO (OBLIGATORIO)</td>
					</tr>
					<tr>
						<td colspan="2">
							Este campo corresponde a la cantidad de dias desde la fecha que ingresan a la empresa al dia que se liquida la nomina<br><br>
							<b>Calculo del tiempo laborado</b><br>
							1 año = 360 dias<br>
							1 mes = 30 dias<br>
							ejemplo: 5 años + 3 meses + 18 dias<br>
							(5*360)+(3*30)+18 = 1908
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td>Dias</td>
						<td><input type="number" id="workedTime" value="<?=$days?>"></td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td colspan="2"><button onclick="saveWorkedTime()">Guardar</button></td></tr>
					<tr id="divLoadWorkedDays"></tr>
				</table>
				<script>
					function saveWorkedTime(){
						let days = document.getElementById('workedTime').value;
						if(days==''){alert("campo dias es obligatorio");return;}

						MyLoading2('on')
						Ext.get("divLoadWorkedDays").load({
				            url     : 'nomina_electronica/bd/bd.php',
				            scripts : true,
				            nocache : true,
				            params  :
				            {
				                opc               : 'updateFields',
				                data              : JSON.stringify({
				                    rows  : [{ "field":"tiempo_laborado","newValue":days}],
				                    table : "nomina_planillas_electronica_empleados",
				                    where : "activo=1 AND id_empresa=<?= $this->id_empresa; ?> AND id_planilla=<?= $this->id_planilla; ?> AND id_empleado=<?= $id_empleado; ?>"
				                }),

				            }
				        });
					
					setTimeout(()=>{
						MyLoading2('off')
						Win_Ventana_tiempo_laborado.close()
					},1500) 
						
						
					}
				</script>
			<?php
		}

		/**
		 * savePayRoll save payroll, here the code just change the payroll status
		 */
		public function savePayRoll()
		{

			$sql="SELECT MAX(consecutivo) AS consecutivo FROM nomina_planillas_electronica WHERE activo=1 AND id_empresa=".$this->id_empresa." AND codigo_tipo_documento=".$this->payRollInfo->codigo_tipo_documento;
			$query=mysql_query($sql,$this->mysql);		
			$consecutive = mysql_result($query,0,'consecutivo')+1;
			// $consecutive = $this->payRollInfo->consecutivo + 1;
			$updateRows = ($this->payRollInfo->consecutivo>0)? " estado=1 " : " estado=1,consecutivo=$consecutive " ;
			$sql="UPDATE nomina_planillas_electronica SET $updateRows WHERE activo=1 AND id='$this->id_planilla' AND id_empresa='$this->id_empresa' ";
			$query=mysql_query($sql,$this->mysql);
	    	if ($query) {
	    		$this->insertDocumentLog('Generar');				
				
	    		echo '<script>
						cerrarPlanilla();
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
	    	}
	    	else{
	    		echo '<script>
	    				alert("Error\nIntentelo de nuevo si el problema continua comuniquese con soporte tecnico");
	    				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
	    	}
		}

		public function editElectronicPayroll()
		{

	   		if ($this->payRollInfo->estado==3) {
	   			echo '<script>
        				MyLoading2("off",{texto:"La planilla esta cancelada no se puede editar",icono:"failed"});
					</script>';
	   			exit;
	   		}

			$sql="UPDATE nomina_planillas_electronica SET estado=0 WHERE activo=1 AND id='$this->id_planilla' AND id_empresa='$this->id_empresa' ";
			$query=mysql_query($sql,$this->mysql);
	    	if ($query) {
	    		$this->insertDocumentLog('Editar');			
	    		echo '<script>
						cerrarPlanilla();
        				MyLoading2("off",{texto:"se edito el documento"});
					</script>';
	    	}
	    	else{
	    		echo '<script>
        				MyLoading2("off",{texto:"error, intentelo de nuevo",icono:"failed"});
					</script>';
	    	}
		}

		/**
		 * deleteElectronicPayroll update to delete state of th payroll
		 */
		public function deleteElectronicPayroll(){

			if($this->validateEmployeePayRoll()){
				echo '<script>
	   					alert("No se puede eliminar por que ya hay empleados enviados a la dian");
	   					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
	   			exit;
			}

	    	$sql="UPDATE nomina_planillas_electronica SET estado=3 WHERE activo=1 AND id='$this->id_planilla' AND id_empresa='$this->id_empresa' ";

	   		if ($this->payRollInfo->estado==3) {
	   			echo '<script>
	   					alert("La planilla ya esta cancelada");
	   					document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
	   			exit;
	   		}
	   		else if ($this->payRollInfo->estado==0 && ($this->payRollInfo->consecutivo==0 || $this->payRollInfo->consecutivo=='') ) {
				$sql="UPDATE nomina_planillas_electronica SET activo=0 WHERE activo=1 AND id='$this->id_planilla' AND id_empresa='$this->id_empresa'";
			}
			// echo $sql;
			$query=mysql_query($sql,$this->mysql);
	    	if ($query) {
	    		$this->insertDocumentLog('Cancelar');
				
				
	    		echo '<script>
						cerrarPlanilla("delete");
						document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
	    	}
	    	else{
	    		echo '<script>
	    				alert("Error\nIntentelo de nuevo si el problema continua comuniquese con soporte tecnico");
	    				document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
					</script>';
	    	}
	    }

	    public function validateEmployeePayRoll()
	    {
	    	$sql = "SELECT COUNT(id) AS records FROM nomina_planillas_electronica_empleados 
	    			WHERE activo=1 
	    			AND id_empresa=$this->id_empresa 
	    			AND id_planilla=$this->id_planilla
	    			AND consecutivo>0";
			$query=mysql_query($sql,$this->mysql);
			$retVal = (mysql_result($query,0,'records')>0)? true : false;
			return $retVal;
	    }

	    /**
	     * getAdjustmentType get types of payroll adjustment
	     * @return array with type list
	     */
	    public function getAdjustmentType()
	    {
	    	$sql = "SELECT id,codigo,nombre FROM nomina_configuracion_tipo_documentos_ajuste WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=mysql_query($sql,$this->mysql);
			while($row = mysql_fetch_assoc($query)){
				$retVal[] = $row;
			}
			return $retVal;

	    }

		public function getOvertimeType()
	    {
	    	$sql = "SELECT tipo_hora FROM nomina_configuracion_hora_extra WHERE activo=1 AND id_empresa=$this->id_empresa";
			$query=mysql_query($sql,$this->mysql);
			return mysql_result($query,0,'tipo_hora');
	    }

	    /**
	     * insertDocumentLog insert document log
	     * @param  [String] $action log action (Generar,Editar,Cancelar)
	     */
	    public function insertDocumentLog($action)
	    {
	    	$sqlLog = "INSERT INTO log_documentos_contables
						(
							id_documento,
							id_usuario,
							usuario,
							actividad,
							tipo_documento,
							descripcion,
							id_sucursal,
							id_empresa,
							ip,
							fecha,hora
						)
						VALUES
						(
							$id_planilla,
							'".$_SESSION['IDUSUARIO']."',
							'".$_SESSION['NOMBREUSUARIO']."',
							'$action',
							'PE',
							'Planilla electronica',
							'".$_SESSION['SUCURSAL']."',
							'$this->id_empresa',
							'".$_SERVER['REMOTE_ADDR']."',
							'".date('Y-m-d')."',
							'".date('H:i:s')."')";
			$query=mysql_query($sql,$this->mysql);
	    }

	    /********************************************/
	    /* JSON AND DIAN SEND AND SETTINGS
	    /********************************************/

	    /**
	     * getEmployeePayRollInfo get employee data and employee payroll information
	     * @param  [int] $id_empleado employee id
	     * @return [array]            array with all information
	     */
	    public function getEmployeePayRollInfo($id_empleado)
	    {
	    	$sql = "SELECT 
						NE.fecha_inicio,
						NE.fecha_final,
						NPE.tipo_documento,
						NPE.documento_empleado,
						NPE.nombre_empleado,
						NPE.dias_laborados,
						NPE.dias_laborados_empleado,
						NPE.id_contrato,
						NPE.prefijo,
						NPE.consecutivo,
						NPE.tiempo_laborado,
						NPE.id_usuario_NE,
						NPE.nombre_usuario_NE,
						NPE.cedula_usuario_NE,
						NPE.fecha_NE,
						NPE.hora_NE,
						NPE.response_NE,
						NPE.UUID,
						tipo_documento.codigo_tipo_documento_dian,
						empleados.documento,
						empleados.nombre1,
						empleados.nombre2,
						empleados.apellido1,
						empleados.apellido2,
						empleados.direccion,
						empleados.email_personal,
						empleados.pais,
						pais.iso2,
						empleados.departamento,
						depto.codigo_departamento,
						empleados.ciudad,
						ciudad.codigo_ciudad
	    			FROM nomina_planillas_electronica_empleados NPE
					INNER JOIN nomina_planillas_electronica AS NE ON NE.id=NPE.id_planilla
	    			INNER JOIN empleados ON empleados.id = NPE.id_empleado
	    			LEFT JOIN tipo_documento ON tipo_documento.id=empleados.tipo_documento
					LEFT JOIN ubicacion_pais AS pais ON pais.id = empleados.id_pais
					LEFT JOIN ubicacion_departamento AS depto ON depto.id = empleados.id_departamento
					LEFT JOIN ubicacion_ciudad AS ciudad ON ciudad.id = empleados.id_ciudad
	    			WHERE NPE.id_planilla=$this->id_planilla 
	    			AND NPE.id_empleado=$id_empleado";	    	
			$query=mysql_query($sql,$this->mysql);
			return mysql_fetch_assoc($query);	
	    }

	    /**
	     * setConsecutive update consecutive to the employee payroll
	     * @param  [int] $id_empleado employee id
	     */
	    public function setConsecutive($id_empleado)
	    {

			$sql   = "SELECT prefijo,consecutivo 
						FROM nomina_configuracion_consecutivos 
						WHERE codigo='".$this->payRollInfo->codigo_tipo_documento."' AND id_empresa=$this->id_empresa";
			$query =mysql_query($sql,$this->mysql);	
			$prefijo     = mysql_result($query,0,"prefijo");
			$consecutivo = mysql_result($query,0,"consecutivo");
			
			$sql   = "UPDATE nomina_planillas_electronica_empleados SET prefijo='$prefijo',consecutivo=$consecutivo+1 WHERE id_planilla=$this->id_planilla AND id_empleado=$id_empleado";
			$query =mysql_query($sql,$this->mysql);	

			$sql   = "UPDATE nomina_configuracion_consecutivos SET consecutivo=consecutivo+1 WHERE codigo='".$this->payRollInfo->codigo_tipo_documento."' AND id_empresa=$this->id_empresa";
			$query = mysql_query($sql,$this->mysql);
	    }

	    /**
	     * getCompanyInformation get company information to set the json
	     * @return [array] associative array wiht all need information
	     */
	    public function getCompanyInformation()
	    {
			$sql = "SELECT
						empresas.documento,
						empresas.digito_verificacion,
						empresas.razon_social,
						empresas.primer_apellido,
						empresas.segundo_apellido,
						empresas.primer_nombre,
						empresas.otros_nombres,
						empresas.client_token,
						empresas.access_token,
						pais.iso2,
						empresas.pais,
						depto.codigo_departamento,
						empresas.departamento,
						ciudad.codigo_ciudad,
						empresas.ciudad,
						empresas.direccion
					FROM
						empresas
					LEFT JOIN ubicacion_pais AS pais ON pais.id = empresas.id_pais
					LEFT JOIN ubicacion_departamento AS depto ON depto.id = empresas.id_departamento
					LEFT JOIN ubicacion_ciudad AS ciudad ON ciudad.id = empresas.id_ciudad
					WHERE
						empresas.id = $this->id_empresa";
			$query=mysql_query($sql,$this->mysql);
			return mysql_fetch_assoc($query);
	    }

	    /**
	     * getCompanyBranchInformation get company branch information to set the json
	     * @return [array] associative array wiht all need information
	     */
	    public function getCompanyBranchInformation()
	    {
	    	$sql = "SELECT 
						sucursal.id_empresa,
						sucursal.nombre,
						sucursal.id_responsable,
						sucursal.responsable,
						sucursal.bodegas,
						sucursal.id_departamento,
						sucursal.departamento,
						sucursal.id_ciudad,
						sucursal.ciudad,
						sucursal.codigo,
						sucursal.activo,
						sucursal.direccion,
						sucursal.telefono,
						sucursal.codigo_postal,
						sucursal.numero_matricula_mercantil,
						sucursal.UUID,
						depto.codigo_departamento,
						ciudad.codigo_ciudad
					FROM empresas_sucursales AS sucursal
					LEFT JOIN ubicacion_departamento AS depto ON depto.id = sucursal.id_departamento
					LEFT JOIN ubicacion_ciudad AS ciudad ON ciudad.id = sucursal.id_ciudad
					WHERE sucursal.id_empresa=$this->id_empresa 
					AND sucursal.id=".$_SESSION['SUCURSAL'];
			$query=mysql_query($sql,$this->mysql);
			return mysql_fetch_assoc($query);
	    }

	    /**
	     * getMoneyData get money data configuration
	     * @return [array] money data
	     */
	    public function getMoneyData()
	    {
	    	$sql = "SELECT moneda FROM configuracion_moneda  WHERE id=".$_SESSION["MONEDA"];
			$query=mysql_query($sql,$this->mysql);
			return mysql_fetch_assoc($query);
	    }

	    /**
	     * getPayDates get pay dates from the payroll
	     * @param  [int] $id_empleado  employee id
	     * @return [json]              array with all dates
	     */
	    public function getPayDates($id_empleado)
		{		
				/* first look for the personal pay date, if does not have, then take the general date*/
				$sql = "SELECT fecha AS FechaPago 
						FROM nomina_planillas_electronica_empleados_fechas_pago 
						WHERE activo=1
						AND id_planilla=$this->id_planilla 
						AND id_empleado=$id_empleado";
				$query=mysql_query($sql,$this->mysql);
				while($row = mysql_fetch_assoc($query)){
					$retVal[] = $row;
				}
				if (!$retVal) {
					$sql = "SELECT fecha AS FechaPago 
							FROM nomina_planillas_electronica_fechas_pago 
							WHERE activo=1 
							AND id_planilla=$this->id_planilla";
					$query=mysql_query($sql,$this->mysql);
					while($row = mysql_fetch_assoc($query)){
						$retVal[] = $row;
					}
				}

				return $retVal;
		}	

		public function getConcepts($id_empleado)
		{
			$sql = "SELECT 
						EC.id_concepto,
						EC.codigo_concepto,
						EC.concepto,
						EC.valor_concepto,
						EC.valor_campo_texto,
						EC.data,
						C.clasificacion,
						C.naturaleza
					FROM nomina_planillas_electronica_empleados_conceptos AS EC
					INNER JOIN nomina_conceptos AS C ON C.id=EC.id_concepto
					WHERE 
						EC.activo=1
					AND EC.id_planilla=$this->id_planilla
					AND EC.id_empleado=$id_empleado";
			$query=mysql_query($sql,$this->mysql);
			while($row=mysql_fetch_assoc($query)){

				$row['data']=($row['data']<>null)? json_decode($row['data'],true) : $row['data'];

				/* if array exist, then sum value, and add detail concept*/
				if (isset($retVal[$row['clasificacion']])) {
					$retVal[$row['clasificacion']]['valor_concepto']+=$row['valor_concepto'];
					$retVal[$row['clasificacion']]['valor_campo_texto']+=$row['valor_campo_texto'];
					if ($row['data']<>null) {
						foreach ($row['data'] as $key => $value) {
							array_push($retVal[$row['clasificacion']]["data"],$value);
						}
					}
				}
				/* if does not exist, create the array position*/
				else{
					
					$retVal[$row['clasificacion']]["id_concepto"]     = $row["id_concepto"];
					$retVal[$row['clasificacion']]["codigo_concepto"] = $row["codigo_concepto"];
					$retVal[$row['clasificacion']]["concepto"]        = $row["concepto"];
					$retVal[$row['clasificacion']]["valor_concepto"]  = $row["valor_concepto"] ;
					$retVal[$row['clasificacion']]["valor_campo_texto"]  = $row["valor_campo_texto"] ;
					if ($row['data']<>null) {
						$retVal[$row['clasificacion']]["data"] = [];
						foreach ($row['data'] as $key => $value) {
							array_push($retVal[$row['clasificacion']]["data"],$value);
						}
					}

					$retVal[$row['clasificacion']]["clasificacion"]   = $row["clasificacion"];
				}
				
				/* --- OPCION USANDO NATURALEZA --- */
				/*if ($row["naturaleza"]=="Devengo") {
					$retVal['DevengadosTotal']+=$row['valor_concepto'];
				}
				if ($row["naturaleza"]=="Deduccion") {
					$retVal['DeduccionesTotal']+=$row['valor_concepto'];
				}*/

				if (in_array($row['clasificacion'],$this->gains)) {
					$retVal['DevengadosTotal']+=$row['valor_concepto'];
				}
				if (in_array($row['clasificacion'],$this->deduction)) {
					$retVal['DeduccionesTotal']+=$row['valor_concepto'];
				}				

			}
			
			$retVal['ComprobanteTotal']=$retVal['DevengadosTotal']-$retVal['DeduccionesTotal'];
						
			return $retVal;
		}

		public function getVacations($id_empleado)
		{
			$sql = "SELECT 
							V.fecha_inicio_vacaciones_disfrutadas,
							V.fecha_fin_vacaciones_disfrutadas,
							V.dias_vacaciones_disfrutadas,
							V.valor_vacaciones_disfrutadas,
							V.dias_vacaciones_compensadas,
							V.valor_vacaciones_compensadas
						FROM
							nomina_planillas_liquidacion AS NP
						INNER JOIN nomina_vacaciones_empleados AS V ON V.id_planilla = NP.id
						WHERE
							NP.activo = 1
						AND (NP.estado = 1 OR NP.estado = 2)
						AND NP.id_empresa = $this->id_empresa
						AND NP.fecha_documento >= '".$this->payRollInfo->fecha_inicio."'
						AND NP.fecha_documento <= '".$this->payRollInfo->fecha_final."'
						AND V.id_empleado = '$id_empleado'";
			$query=mysql_query($sql,$this->mysql);
			while ($row=mysql_fetch_array($query)) {
				$retVal["VacacionesComunes"]= [
					"FechaInicio" => $row["fecha_inicio_vacaciones_disfrutadas"],
					"FechaFin"    => $row["fecha_fin_vacaciones_disfrutadas"],
					"Cantidad"    => $row["dias_vacaciones_disfrutadas"],
					"Pago"        => $row["valor_vacaciones_disfrutadas"],
				];

				$retVal["VacacionesCompensadas"]= [
					"Cantidad" => $row["dias_vacaciones_compensadas"],
					"Pago"     => $row["valor_vacaciones_compensadas"],
				];
			}
			return $retVal;
		}

		public function setStructure($id_empleado)
		{	
			date_default_timezone_set($_SESSION['TIMEZONE']);
			/* get employee peyroll information */
			$employeePayroll = $this->getEmployeePayRollInfo($id_empleado);

			
			// if ($employeePayroll["tiempo_laborado"]==0 || $employeePayroll["tiempo_laborado"]=='') {
			// 	if ($_GET["view"]==true) {
			// 		echo "Empleado sin tiempo laborado, edite la planilla y registre el tiempo";
			// 	}
			// 	else{
			// 		echo '<script>
			// 				MyLoading2("off",{texto:"Empleado sin tiempo laborado, edite la planilla y registre el tiempo",icono:"fail",duracion:5000});
			// 			</script>';
			// 	}
				
			// 	exit;
			// }

			/* if the employee peyroll does not have a consecutive, here asign itself*/
			if ($employeePayroll['consecutivo']=='' || $employeePayroll['consecutivo']==0) {
				$this->setConsecutive($id_empleado);
			}
			
			/* requiered information for the json */
			$companyData       = $this->getCompanyInformation();
			$companyBranchData = $this->getCompanyBranchInformation();
			$moneyData         = $this->getMoneyData();
			$contractInfo      = $this->getWorkContract([$employeePayroll['id_contrato']]);

			$outDate = $contractInfo[$employeePayroll['id_contrato']]["fecha_fin_contrato"];
			
			/* get pay dates and validated it*/
			$payDates          = $this->getPayDates($id_empleado);
			if (count($payDates)==0 || gettype($payDates)<>'array') {
				echo '<script>
						MyLoading2("off",{texto:"El empleado no tiene fechas de pago, edite la planilla y registre fecha(s)",icono:"fail",duracion:5000});
					</script>';
				exit;
			}

			/* employee worked days*/
			$workedDays = $this->getWorkedDays($id_empleado);

			/* get employee concepts */
			$concepts = $this->getConcepts($id_empleado);

			$overTimeType = $this->getOvertimeType();
			
			
			
			switch ($overTimeType) {
				case 'hora':
					$overtimeCalc = 1;
					break;
				case 'minutos':
					$overtimeCalc = 60;
					break;
				case 'segundos':
					$overtimeCalc = 3600;
					break;				
				default:
					$overtimeCalc = 1;
					break;
			}
			// print_r($concepts);
			// header('Content-Type: application/json; charset=utf-8');
			// echo json_encode($concepts);exit;
			
			/* get employee vacations*/
			$vacations = $this->getVacations($id_empleado);

			/* Tipo de nota ajuste */
			$sql = "SELECT 
					codigo_tipo_ajuste,
					planilla_relacionada_al_ajuste
		 			FROM nomina_planillas_electronica_empleados
		 			WHERE 
		 			id_planilla = $this->id_planilla
		 			AND id_empleado = ".$id_empleado;

			$query=mysql_query($sql,$this->mysql);
			
			while ($row=mysql_fetch_array($query)){
				$planilla_relacionada_al_ajuste = (is_null($row["planilla_relacionada_al_ajuste"]))? "" : $row["planilla_relacionada_al_ajuste"];
				$tipo_nota_ajuste = ($row["codigo_tipo_ajuste"] != 1 && $row["codigo_tipo_ajuste"] != 2) ? 0 : $row["codigo_tipo_ajuste"];
			}

			/* credentials position*/
			$this->JsonStructure["Credencial"]=[
				"ClientToken" => $companyData["client_token"],
				"AccessToken" => $companyData["access_token"]
			];
			/* document position, contains branch information*/
			date_default_timezone_set("America/Bogota");
			// Obtiene la hora actual del servidor
			$hora_servidor = time();

			// Ajusta la hora para reflejar la hora local en tu equipo restando 5 minutos (5*60)
			$hora_local = $hora_servidor - (300);

			// Convierte la hora ajustada en un formato legible
			$hora_formateada = date('Y-m-d H:i:s', $hora_local);

			$this->JsonStructure["Documento"] = [
				"OrigenDocumento"        => "",
				"TipoDocumento"          => $this->payRollInfo->codigo_tipo_documento,
				"Prefijo"                => $employeePayroll['prefijo'],
				"Consecutivo"            => $employeePayroll['consecutivo'],
				"Fecha"                  => $hora_formateada,
				"PeriodoNominaCodigo"    => $this->payRollInfo->periodo_pago,
				"FechaLiquidacionInicio" => $this->payRollInfo->fecha_inicio,
				"FechaLiquidacionFin"    => $this->payRollInfo->fecha_final,
				"Pais"                   => $companyData["pais"],
				"PaisCodigo"             => $companyData["iso2"],
				"Departamento"           => $this->formatText($companyBranchData["departamento"]),
				"DepartamentoCodigo"     => $companyBranchData["codigo_departamento"],
				"Ciudad"                 => $this->formatText($companyBranchData["ciudad"]),
				"CiudadCodigo"           => $companyBranchData["codigo_departamento"].$companyBranchData["codigo_ciudad"],
				"Idioma"                 => "es",
				"Moneda"                 => $moneyData["moneda"],
				"Trm"                    => 0,
				"Redondeo"               => 0.00,
				"DevengadosTotal"        => $concepts["DevengadosTotal"],
				"DeduccionesTotal"       => $concepts["DeduccionesTotal"],
				"ComprobanteTotal"       => $concepts["ComprobanteTotal"],
				"Notas"                  => "-",
				"Usuario"                => $_SESSION['CEDULAFUNCIONARIO'],
				"TipoNotaAjuste"         => $tipo_nota_ajuste,
				"NumeroNotaAjuste"       => $planilla_relacionada_al_ajuste
			];

			/* boss position, contains company information*/
			$this->JsonStructure["Empleador"] = [
				"Sucursal"           => $this->formatText($companyBranchData["nombre"]),
				"Identificacion"     => $companyData["documento"],
				"DigitoVerificador"  => $companyData["digito_verificacion"],
				"RazonSocial"        => $this->formatText($companyData["razon_social"]),
				"PrimerApellido"     => $this->formatText($companyData["primer_apellido"]),
				"SegundoApellido"    => $this->formatText($companyData["segundo_apellido"]),
				"PrimerNombre"       => $this->formatText($companyData["primer_nombre"]),
				"OtrosNombres"       => $this->formatText($companyData["otros_nombres"]),
				"Pais"               => $this->formatText($companyData["pais"]),
				"PaisCodigo"         => $companyData["iso2"],
				"Departamento"       => $this->formatText($companyData["departamento"]),
				"DepartamentoCodigo" => $companyData["codigo_departamento"],
				"Ciudad"             => $this->formatText($companyData["ciudad"]),
				"CiudadCodigo"       => $companyData["codigo_departamento"].$companyData["codigo_ciudad"],
				"Direccion"          => $this->formatText($companyData["direccion"])
			];

			/* employee position, contains employee information*/
			$this->JsonStructure["Trabajador"] = [
				"TipoIdentificacion" => $employeePayroll["codigo_tipo_documento_dian"],
				"Identificacion"     => $employeePayroll["documento"],
				"CodigoTrabajador"   => $employeePayroll["documento"],
				"PrimerApellido"     => $this->formatText($employeePayroll["apellido1"]),
				"SegundoApellido"    => $this->formatText($employeePayroll["apellido2"]),
				"PrimerNombre"       => $this->formatText($employeePayroll["nombre1"]),
				"OtrosNombres"       => $this->formatText($employeePayroll["nombre2"]),
				"Pais"               => $this->formatText($employeePayroll["pais"]),
				"PaisCodigo"         => $employeePayroll["iso2"],
				"Departamento"       => $this->formatText($employeePayroll["departamento"]),
				"DepartamentoCodigo" => $employeePayroll["codigo_departamento"],
				"Ciudad"             => $this->formatText($employeePayroll["ciudad"]),
				"CiudadCodigo"       => $employeePayroll["codigo_departamento"].$employeePayroll["codigo_ciudad"],
				"Direccion"          => $this->formatText($employeePayroll["direccion"]),
				"Email"              => $this->formatText($employeePayroll["email_personal"]),
				"TipoContrato"       => $contractInfo[$employeePayroll['id_contrato']]["codigo_tipo_contrato"],
				"TipoTrabajador"     => $contractInfo[$employeePayroll['id_contrato']]["codigo_tipo_trabajador"],
				"SubTipoTrabajador"  => $contractInfo[$employeePayroll['id_contrato']]["codigo_subtipo_trabajador"],
				"AltoRiesgoPension"  => "false",
				"SalarioIntegral"    => $contractInfo[$employeePayroll['id_contrato']]["salario_integral"],
				"FechaIngreso"       => $contractInfo[$employeePayroll['id_contrato']]["fecha_inicio_contrato"],
				"FechaRetiro"        => ($outDate=='0000-00-00' || $outDate==null)? null : $outDate ,
				"TiempoLaborado"     => $employeePayroll["tiempo_laborado"],
				"Sueldo"             => $contractInfo[$employeePayroll['id_contrato']]["salario_basico"],
				"FormaPago"          => $contractInfo[$employeePayroll['id_contrato']]["codigo_forma_pago"],
				"MedioPago"          => $contractInfo[$employeePayroll['id_contrato']]["codigo_medio_pago"],
				"Banco"              => $this->formatText($contractInfo[$employeePayroll['id_contrato']]["nombre_banco"]),
				"TipoCuenta"         => $contractInfo[$employeePayroll['id_contrato']]["tipo_cuenta_bancaria"],
				"NumeroCuenta"       => $contractInfo[$employeePayroll['id_contrato']]["numero_cuenta_bancaria"]
			];

			/* employee pay dates */
			$this->JsonStructure["FechasPagos"] = $payDates;

			// public $overTimeTypes = array(
			// 	["type"=>"hora_extra_diurna","percent"=>0],
			// 	["type"=>"hora_extra_nocturna","percent"=>0],
			// 	["type"=>"hora_recargo_nocturno","percent"=>0],
			// 	["type"=>"hora_recargo_diario_dominicales_y_festivas","percent"=>0.75],
			// 	["type"=>"hora_extra_nocturna_dominicales_y_festivas","percent"=>0],
			// 	["type"=>"hora_recargo_nocturno_dominicales_y_festivas","percent"=>0],
			// );

			/* employee gains */
			$this->JsonStructure["Devengados"] = [
				"DiasTrabajados"     => $workedDays,
				"SueldoTrabajado"    => ($concepts["salario"]["valor_concepto"]==null)? 0: $concepts["salario"]["valor_concepto"],
				"AuxilioTransporte"  => ($concepts["auxilio_transporte"]["valor_concepto"]==null)? 0: $concepts["auxilio_transporte"]["valor_concepto"],
				"ViaticoManutAlojS"  => ($concepts["viaticos_salariales"]["valor_concepto"]==null)? 0: $concepts["viaticos_salariales"]["valor_concepto"],
				"ViaticoManutAlojNS" => ($concepts["viaticos_no_salariales"]["valor_concepto"]==null)? 0: $concepts["viaticos_no_salariales"]["valor_concepto"],
				
				"HEDs"               => ($concepts["hora_extra_diurna"]["valor_concepto"])
										? [
											"Hora" =>[[
											"HoraInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
											"HoraFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
											"Cantidad" 		=> round($concepts["hora_extra_diurna"]["valor_campo_texto"]/$overtimeCalc,2),
											"Porcentaje"	=> $this->overTimeTypes[0]["percent"],
											"Pago"			=> $concepts["hora_extra_diurna"]["valor_concepto"],
											]]
										] : null,
										
				"HENs"               => ($concepts["hora_extra_nocturna"]["valor_concepto"])
										? [
											"Hora" => [[
											"HoraInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
											"HoraFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
											"Cantidad" 		=> round($concepts["hora_extra_nocturna"]["valor_campo_texto"]/$overtimeCalc,2),
											"Porcentaje"	=> $this->overTimeTypes[1]["percent"],
											"Pago"			=> $concepts["hora_extra_nocturna"]["valor_concepto"],
											]]
										] : null,
				"HRNs"               => ($concepts["hora_recargo_nocturno"]["valor_concepto"])
										? [
											"Hora" => [[
											"HoraInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
											"HoraFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
											"Cantidad" 		=> round($concepts["hora_recargo_nocturno"]["valor_campo_texto"]/$overtimeCalc,2),
											"Porcentaje"	=> $this->overTimeTypes[2]["percent"],
											"Pago"			=> $concepts["hora_recargo_nocturno"]["valor_concepto"],
											]]
										] : null,
				"HEDDFs"             => null,
				"HRDDFs"             => ($concepts["hora_recargo_diario_dominicales_y_festivas"]["valor_concepto"])
										? [
											"Hora" => [[
											"HoraInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
											"HoraFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
											"Cantidad" 		=> round($concepts["hora_recargo_diario_dominicales_y_festivas"]["valor_campo_texto"]/$overtimeCalc,2),
											"Porcentaje"	=> $this->overTimeTypes[3]["percent"],
											"Pago"			=> $concepts["hora_recargo_diario_dominicales_y_festivas"]["valor_concepto"],
											]]
										] : null,
				"HENDFs"             => ($concepts["hora_extra_nocturna_dominicales_y_festivas"]["valor_concepto"])
										? [
											"Hora" => [[
											"HoraInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
											"HoraFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
											"Cantidad" 		=> round($concepts["hora_extra_nocturna_dominicales_y_festivas"]["valor_campo_texto"]/$overtimeCalc,2),
											"Porcentaje"	=> $this->overTimeTypes[4]["percent"],
											"Pago"			=> $concepts["hora_extra_nocturna_dominicales_y_festivas"]["valor_concepto"],
											]]
										] : null,
				"HRNDFs"             => ($concepts["hora_recargo_nocturno_dominicales_y_festivas"]["valor_concepto"])
										? [
											"Hora" => [[
											"HoraInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
											"HoraFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
											"Cantidad" 		=> round($concepts["hora_recargo_nocturno_dominicales_y_festivas"]["valor_campo_texto"]/$overtimeCalc,2),
											"Porcentaje"	=> $this->overTimeTypes[5]["percent"],
											"Pago"			=> $concepts["hora_recargo_nocturno_dominicales_y_festivas"]["valor_concepto"],
											]]
										] : null,
				

				// overtime old way
				// "HEDs"               => ($concepts["hora_extra_diurna"]["data"]<>null)? [ "Hora" 	=> $concepts["hora_extra_diurna"]["data"]] : null,
				// "HENs"               => ($concepts["hora_extra_nocturna"]["data"]<>null)? [ "Hora" => $concepts["hora_extra_nocturna"]["data"]] : null,
				// "HRNs"               => ($concepts["hora_recargo_nocturno"]["data"]<>null)? [ "Hora" => $concepts["hora_recargo_nocturno"]["data"]] : null,
				// "HEDDFs"             => null,
				// "HRDDFs"             => ($concepts["hora_recargo_diario_dominicales_y_festivas"]["data"]<>null)? [ "Hora" => $concepts["hora_recargo_diario_dominicales_y_festivas"]["data"]] : null,
				// "HENDFs"             => ($concepts["hora_extra_nocturna_dominicales_y_festivas"]["data"]<>null)? [ "Hora" => $concepts["hora_extra_nocturna_dominicales_y_festivas"]["data"]] : null,
				// "HRNDFs"             => ($concepts["hora_recargo_nocturno_dominicales_y_festivas"]["data"]<>null)? [ "Hora" => $concepts["hora_recargo_nocturno_dominicales_y_festivas"]["data"]] : null,
				
				"Vacaciones"         => $vacations,
				"Primas"             => $concepts["prima"]["data"],
				"Cesantias"          => ($concepts["cesantias"]["valor_concepto"])
										? [
											[
											"Porcentaje"	=> 12,
											"Pago"			=> $concepts["cesantias"]["valor_concepto"],
											"PagoIntereses" => $concepts["intereses_de_cesantias"]["valor_concepto"],
											]
										] : null,
				"Incapacidades"      => $concepts["incapacidad"]["data"],
				"Licencias"          => [
											"LicenciaMP"         => ($concepts["licencia_maternidad_paternidad"]["valor_concepto"])
											? [
												[
												"FechaInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
												"FechaFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
												"Cantidad" 		=> $concepts["licencia_maternidad_paternidad"]["valor_campo_texto"],
												"Pago"			=> $concepts["licencia_maternidad_paternidad"]["valor_concepto"],
												]
											] : null,
											"LicenciaR"          => ($concepts["licencia_remunerada"]["valor_concepto"])
											? [
												[
												"FechaInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
												"FechaFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
												"Cantidad" 		=> $concepts["licencia_remunerada"]["valor_campo_texto"],
												"Pago"			=> $concepts["licencia_remunerada"]["valor_concepto"],
												]
											] : null,
											"LicenciaNR"         => ($concepts["licencia_no_remunerada"]["valor_concepto"])
											? [
												[
												"FechaInicio" 	=> $employeePayroll["fecha_inicio"]."T00:00:00",
												"FechaFin" 		=> $employeePayroll["fecha_final"]."T00:00:00",
												"Cantidad" 		=> $concepts["licencia_no_remunerada"]["valor_campo_texto"],
												"Pago"			=> $concepts["licencia_no_remunerada"]["valor_concepto"],
												]
											] : null,
											],
				"Bonificaciones"     => [
											"Bonificacion"=>[
																"BonificacionS"  => $concepts["bonififacion_salarial"]["valor_concepto"],
																"BonificacionNS" => $concepts["bonififacion_no_salarial"]["valor_concepto"],
															]
										],
				"Auxilios"     => [
											"Auxilio"=>[
																"AuxilioS"  => $concepts["auxilio_salarial"]["valor_concepto"],
																"AuxilioNS" => $concepts["auxilio_no_salarial"]["valor_concepto"],
															]
										],
				/*estos no se toma, para que lo tome se debe agregar a la clasificacion de conceptos del panel de control y agregarlo para que se sume en los devengos de la planilla*/
				"HuelgasLegales"     => [
											"HuelgasLegal"=>[
																"AuxilioS"  => $concepts["huelga_salarial"]["valor_concepto"],
																"AuxilioNS" => $concepts["huelga_no_salarial"]["valor_concepto"],
															]
										],
				"OtrosConceptos"     => [
											"OtrosConcepto"=>[
																"AuxilioS"  => $concepts["huelga_salarial"]["valor_concepto"],
																"AuxilioNS" => $concepts["huelga_no_salarial"]["valor_concepto"],
															]
										],
				"Compensaciones"     => [
											"Compensacion"=>[
																"AuxilioS"  => $concepts["compensacion_salarial"]["valor_concepto"],
																"AuxilioNS" => $concepts["compensacion_no_salarial"]["valor_concepto"],
															]
										],
				"BonoEPCTVs"     => [
											"BonoEPCTV"=>[
																"AuxilioS"  => $concepts["bonoepctv_salarial"]["valor_concepto"],
																"AuxilioNS" => $concepts["bonoepctv_no_salarial"]["valor_concepto"],
															]
										],
				/* fin bloque conceptos no funcionales */
				"Comision"      => ($concepts["comision_salarial"]["valor_concepto"]==null)? 0 : $concepts["comision_salarial"]["valor_concepto"],
				"PagosTerceros" => ($concepts["pago_terceros"]["valor_concepto"]==null)? 0 : $concepts["pago_terceros"]["valor_concepto"],
				"Anticipos"     => ($concepts["anticipo_salarial"]["valor_concepto"]==null)? 0 : $concepts["anticipo_salarial"]["valor_concepto"],
				"Dotacion"      => ($concepts["dotacion"]["valor_concepto"]==null)? 0 : $concepts["dotacion"]["valor_concepto"],
				"ApoyoSost"     => ($concepts["apoyo_sostenimiento"]["valor_concepto"]==null)? 0 : $concepts["apoyo_sostenimiento"]["valor_concepto"],
				"Teletrabajo"   => ($concepts["teletrabajo"]["valor_concepto"]==null)? 0 : $concepts["teletrabajo"]["valor_concepto"],
				"BonifRetiro"   => ($concepts["bonificacion_retiro"]["valor_concepto"]==null)? 0 : $concepts["bonificacion_retiro"]["valor_concepto"],
				"Indemnizacion" => ($concepts["indemnizacion"]["valor_concepto"]==null)? 0 : $concepts["indemnizacion"]["valor_concepto"],
				"Reintegro"     => ($concepts["reintegro"]["valor_concepto"]==null)? 0 : $concepts["reintegro"]["valor_concepto"],

			];

			/* employee deductions */
			$this->JsonStructure["Deducciones"] = [
				"Salud" => [
								"Porcentaje" => 4.00,
								"Deduccion"  => $concepts["salud"]["valor_concepto"]
				],
				"FondoPension" => [
								"Porcentaje" => 4.00,
								"Deduccion"  => $concepts["pension"]["valor_concepto"]
				],
				"FondoSP"             => ($concepts["fondo_solidaridad_pensional"]["data"][0]==null)? [
											"Porcentaje" => 0,
											"Deduccion" => 0,
											"PorcentajeSub" => 0,
											"DeduccionSub" => null,
										] : $concepts["fondo_solidaridad_pensional"]["data"][0],
				"Sindicatos"          => null,
				"Sanciones"           => null,
				"Libranzas"           => ($concepts["libranza"]["valor_concepto"]>0)? [
																						"Libranza"=>[
																										[
																											"Descripcion"=>$concepts["libranza"]["concepto"],
																											"Deduccion"=>$concepts["libranza"]["valor_concepto"]
																										]
																									]
																					] : null,
				"PagosTerceros"       => 0,
				"Anticipos"           => 0,
				"OtrasDeducciones"    => ($concepts["otras_deducciones"]["valor_concepto"]==null)? 0 :$concepts["otras_deducciones"]["valor_concepto"],
				"PensionVoluntaria"   => ($concepts["pension_voluntaria"]["valor_concepto"]==null)? 0 : $concepts["pension_voluntaria"]["valor_concepto"],
				"RetencionFuente"     => ($concepts["retefuente"]["valor_concepto"]==null)?0 : $concepts["retefuente"]["valor_concepto"],
				"AFC"                 => ($concepts["AFC"]["valor_concepto"]==null)? 0 : $concepts["AFC"]["valor_concepto"],
				"Cooperativa"         => ($concepts["cooperativa"]["valor_concepto"]==null)? 0 : $concepts["cooperativa"]["valor_concepto"],
				"EmbargoFiscal"       => ($concepts["embargo_fiscal"]["valor_concepto"]==null)? 0 : $concepts["embargo_fiscal"]["valor_concepto"],
				"PlanComplementarios" => ($concepts["eps_plan_complementario"]["valor_concepto"]==null)? 0 : $concepts["eps_plan_complementario"]["valor_concepto"],
				"Educacion"           => ($concepts["educacion"]["valor_concepto"]==null)? 0 : $concepts["educacion"]["valor_concepto"],
				"Reintegro"           => ($concepts["reintegro"]["valor_concepto"]==null)? 0 : $concepts["reintegro"]["valor_concepto"],
				"Deuda"               => ($concepts["deuda"]["valor_concepto"]==null)? 0 : $concepts["deuda"]["valor_concepto"],

			];

			/* aditional */
			$this->JsonStructure["DocumentoAdicional"] = [

			];
			/* view json on screen*/
			if ($_GET['view']!='true') {
				echo '<script>MyLoading2("off",{texto:"Nomina Electronica enviada"});</script>';
			}
			return $this->JsonStructure;
			// $json = json_encode($this->JsonStructure);
			// var_dump($json);
			// print_r($this->JsonStructure);
		}

		public function getJson($array)
		{
			$json = json_encode($array,JSON_PRETTY_PRINT);
			$error = null;
			switch (json_last_error()) {
			case JSON_ERROR_DEPTH:
				$error = "Maximum stack depth exceeded";
				break;
			case JSON_ERROR_STATE_MISMATCH:
				$error = "Invalid or malformed JSON";
				break;
			case JSON_ERROR_CTRL_CHAR:
				$error = "Control character error";
				break;
			case JSON_ERROR_SYNTAX:
				$error = "Syntax error";
				break;
			case JSON_ERROR_UTF8:
				$error = "Malformed UTF-8 characters";
				break;
			}

			return ($error==null)? $json : $error;
		}

		/**
		 * generate generate and send json to dian document
		 * @param  [int] $id_empleado employee id
		 */
		public function generate($id_empleado)
		{
			
			$arrayStructure = $this->setStructure($id_empleado);
			$json = $this->getJson($arrayStructure);
			/* view json on screen*/
			if ($_GET['view']=='true') {
				header('Content-Type: application/json; charset=utf-8');
				echo($json);
				exit;
			}
			$sql = "SELECT 
						nombre_usuario_NE,
						cedula_usuario_NE,
						fecha_NE,
						hora_NE,
						response_NE
					FROM nomina_planillas_electronica_empleados 
					WHERE id_planilla=$this->id_planilla AND id_empleado=$id_empleado AND response_NE LIKE '%Comprobante fue generado%' ";
			$query=mysql_query($sql,$this->mysql);

			$nombre_usuario_NE = mysql_result($query,0,'nombre_usuario_NE');
			$cedula_usuario_NE = mysql_result($query,0,'cedula_usuario_NE');
			$fecha_NE          = mysql_result($query,0,'fecha_NE');
			$hora_NE           = mysql_result($query,0,'hora_NE');
			$response_NE       = mysql_result($query,0,'response_NE');

			if($response_NE<>"") {
				?>
					<script>
						alert("El documento ya se envio!");
					</script>
				<?php
				exit;
			}

			// $send = $this->sendJson($json);
			$response = $this->sendJson($json);
			// $response = json_decode($send,true);
			// var_dump($response);
			/* according the response, save it on bd */
			$sql = "UPDATE nomina_planillas_electronica_empleados 
					SET 
						id_usuario_NE     = '$_SESSION[IDUSUARIO]',
						nombre_usuario_NE = '$_SESSION[NOMBREUSUARIO]',
						cedula_usuario_NE = '$_SESSION[CEDULAFUNCIONARIO]',
						fecha_NE          = '".date("Y-m-d")."',
						hora_NE           = '".date("H:i:s")."',
						response_NE       = '$response'
					WHERE id_planilla=$this->id_planilla AND id_empleado=$id_empleado";
			$query=mysql_query($sql,$this->mysql);
			if((strpos($response, 'Comprobante fue generado') !== FALSE) || (strpos($response, 'procesado anteriormente') !== FALSE)){
					?>
					<script>
						alert("Planilla electronica enviada correctamente");
					</script>
					<?php
			}
			else{
				?>
					<script>
						alert("Error\n<?=$response?>");
						console.log("<?=$response?>");
					</script>
					
				<?php
			}

		}

		public function sendJson($json){
			$server_name = $_SERVER['SERVER_NAME'];

			if($server_name == "logicalerp.localhost"){
				// API para enviar el JSON a la DIAN
				$url_api = "http://facse.eastus2.cloudapp.azure.com:8092/Nomina/Documento";

				// Cambiamos la url de validacion por la del envio
				$params                   = [];
				$params['request_url']    = $url_api;
				$params['request_method'] = "POST";
				$params['Authorization']  = "";
				$params['data']           = $json;

				// Consumimos el API y obtenemos sus resultados
				return $respuesta = $this->curlApi($params);
				$respuesta = json_decode($respuesta,true);
				// print_r($respuesta); return;
				$validar = $respuesta['RespuestaFacse'];

				$respuestaFinal['validar']     = $this->formatText($validar);
				$respuestaFinal['comprobante'] = "Se ejecuto el envio en desarrollo";
				$respuestaFinal['id_factura']  = $respuesta['IdDocumento']['Contenido'];
				$respuestaFinal['cufe']        = $respuesta['CufeDocumento']['Contenido'];

				return $respuesta;
			}
			else{
				// API para enviar el JSON a la DIAN
				$url_api = "https://web.facse.net:444/Nomina/Documento";

				// Creamos los parametros para consumir la API
				$params                   = [];
				$params['request_url']    = $url_api;
				$params['request_method'] = "POST";
				$params['Authorization']  = "";
				$params['data']           = $json;

				// Consumimos el API y obtenemos sus resultados
				$respuesta = $this->curlApi($params);
				$respuesta = json_decode($respuesta,true);

				$validar = $respuesta['respuesta'];
				// var_dump($respuesta['respuesta']);

				$respuestaFinal['validar']     = $this->formatText($validar);
				$respuestaFinal['comprobante'] = "Se ejecuto el envio en produccion";
				$respuestaFinal['id_factura']  = $respuesta['IdDocumento']['Contenido'];
				$respuestaFinal['cufe']        = $respuesta['CufeDocumento']['Contenido'];

				return $respuesta['respuesta'];
			}
		}

		public function curlApi($params){
			$client = curl_init();
			$options = array(
											CURLOPT_HTTPHEADER     => array('Content-Type: application/json',"$params[Authorization]"),
											CURLOPT_URL            => "$params[request_url]",
											CURLOPT_CUSTOMREQUEST  => "$params[request_method]",
											CURLOPT_RETURNTRANSFER => true,
											CURLOPT_POSTFIELDS     => $params['data'],
											CURLOPT_SSL_VERIFYPEER => false
											);
			curl_setopt_array($client,$options);
			$response    = curl_exec($client);
			$curl_errors = curl_error($client);

			if(!empty($curl_errors)){
				$response['status']               = 'failed';
				$response['errors'][0]['titulo']  = curl_getinfo($client);
				$response['errors'][0]['detalle'] = curl_error($client);
			}

			$httpCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
			curl_close($client);
			return $response;
		}

		/**
		 * formatText format text for the json encode
		 * @param  [String] $string estring to format
		 * @return [String]         formated string
		 */
		public function formatText($string){
			$caracterEspecial = array("\t","\r","\n",chr(160));
			$originales  = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿ°ª&º/';
			$modificadas = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyoayo/';
			$string = strtr($string, utf8_decode($originales), $modificadas);
			$string = str_replace($caracterEspecial,"",$string);
			return utf8_encode($string);
		}

	}





?>
