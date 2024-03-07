<?php
	header("Content-Type: application/json");

	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("tablesStructure.php");
	include("tablesData.php");

	$mysql->set_charset('utf8');

	$objPayRoll = new ElectronicPayRollConfig($mysql,$arrayTables,$arrayTablesData);

	switch ($opc) {
		case 'tableValidate':
			$objPayRoll->tableValidate($tableName);
			break;
		case 'createTable':
			$objPayRoll->createTable($index);
			break;
		case 'insertDataTable':
			$objPayRoll->insertDataTable($index);
			break;
		case 'updateTable':
			$objPayRoll->updateTable($index);
			break;
		case 'saveConsecutives':
			$objPayRoll->saveConsecutives();
			break;
		case 'getConsecutives':
			$objPayRoll->getConsecutives();
			break;
		case 'getWizardProcess':
			$objPayRoll->getWizardProcess();
			break;
		case 'payRollPeriods':
			$objPayRoll->payRollPeriods();
			break;
		
		default:
			// code...
			break;
	}

	/**
	 * 
	 */
	class ElectronicPayRollConfig
	{

		public function __construct($mysql,$tablesStructure,$tablesData){
			$this->mysql = $mysql;
			$this->tablesStructure = $tablesStructure;
			$this->tablesData = $tablesData;
		}
		/**
		 * tableValidate validar que una tabla dada exista en el sistema
		 * @param  String 	$tableName 	nombre de la tabla a validar
		 * @return Obj      			Objeto json con el valor de la consulta
		 */
		public  function tableValidate($tableName)
		{ 
			$sql = "SHOW TABLES LIKE '$tableName'";
			$query = $this->mysql->query($sql);
			if($this->mysql->num_rows($query)>0){
				$retVal = [ "status" => "success", "message"=>"table $tableName exist" ];
			}
			else{
				$retVal = [ "status" => "error", "message"=>"table $tableName not exist" ];
			}
			echo json_encode($retVal);
		}

		/**
		 * updateTable actualizar la estructura de una tabla en bd agregando columnas
		 */
		public function updateTable($tableName)
		{

			foreach ($this->tablesStructure[$tableName] as $key => $columns) {

				$sql = "ALTER TABLE $tableName ADD $columns[colum_name] $columns[properties]";
				$query = $this->mysql->query($sql);

				if($query){
					$retVal[] = [ "status" => "success", "message"=>"the table was updated" ];
				}
				else{
					$retVal[] = [ "status" => "error", "message"=>$this->mysql->error(), "structure" => $this->tablesStructure[$index]];
				}				
			}

			
			echo json_encode($retVal);
		}

		/**
		 * createTable crear tabla nueva en el sistema
		 * @param  String 	$index 	indice que es el nombre de la tabla a crear
		 * @return Obj      			Objeto json con el valor de la consulta
		 */
		public  function createTable($index)
		{
			$query = $this->mysql->query($this->tablesStructure[$index]);
			if($query){
				$retVal = [ "status" => "success", "message"=>"the table was created" ];
			}
			else{
				$retVal = [ "status" => "error", "message"=>$this->mysql->error(), "structure" => $this->tablesStructure[$index]];
			}
			echo json_encode($retVal);
		}

		

		/**
		 * insertDataTable crear tabla nueva en el sistema
		 * @param  String 	$index 	indice que es el nombre de la tabla a insertar en la tabla
		 * @return Obj      		Objeto json con el valor de la consulta
		 */
		public function insertDataTable($index)
		{	

			// INACTIVAR LOS REGISTROS ANTERIORES
			$sql = "UPDATE $index SET activo=0 WHERE id_empresa=$_SESSION[EMPRESA]";
			$query = $this->mysql->query($sql);

			// INACTIVAR EL REGISTRO DE CONFIGURACION DEL INSERT SI TIENE
			$sql = "UPDATE nomina_wizard_process SET activo=0 WHERE id_empresa=$_SESSION[EMPRESA] AND process='insert' AND table=`table` ";
			$query = $this->mysql->query($sql);

			// INSERTAR  EL PASO QUE SE INSERTO PARA QUE SE MUESTRE EN EL WIZARD QUE ESTA TABLA YA SE INSERTO LA DATA
			$sql = "INSERT INTO nomina_wizard_process (`table`,process,id_empresa) VALUES ('$index','insert',$_SESSION[EMPRESA])";
			$query = $this->mysql->query($sql);
			
			// Reemplazar variables de sesion
			$sql = str_replace("replace_SESSION_EMPRESA",$_SESSION[EMPRESA],$this->tablesData[$index]);
			$query = $this->mysql->query($sql);
			if($query){
				$retVal = [ "status" => "success", "message"=>"the data was inserted" ];
			}
			else{
				$retVal = [ "status" => "error", "message"=>$this->mysql->error(), "sql" => $sql];
			}
			echo json_encode($retVal);
		}


		/**
		 * getConsecutives consultar los consecutivos almacenados
		 * @return json respuesta de la peticion
		 */
		public function getConsecutives()
		{
			$sql = "SELECT * FROM nomina_configuracion_consecutivos WHERE activo=1 AND id_empresa='$_SESSION[EMPRESA]' ";
			$query = $this->mysql->query($sql);
			
			if($this->mysql->num_rows($query)<=0){
				echo json_encode(["status"=>"error","message"=>"without consecutives","sql"=>$sql,"num_rows"=>$this->mysql->num_rows($query)]);
				return;
			}

			while($row=$this->mysql->fetch_array($query)){
				$retVal[$row['tipo']] = $row;
			}
			echo json_encode($retVal);
		}

		/**
		 * getWizardProcess consultar que a que tablas se ha insertado los datos de la nomina
		 */
		public function getWizardProcess()
		{
			$sql = "SELECT * FROM nomina_wizard_process WHERE activo=1 AND id_empresa='$_SESSION[EMPRESA]'";
			$query = $this->mysql->query($sql);
			
			if($this->mysql->num_rows($query)<=0){
				echo json_encode(["status"=>"error","message"=>"without consecutives","sql"=>$sql,"num_rows"=>$this->mysql->num_rows($query)]);
				return;
			}

			while($row=$this->mysql->fetch_assoc($query)){
				$retVal[] = $row;
			}
			echo json_encode($retVal);
		}

		/**
		 * saveConsecutives guardar los consecutivos de los tipos de documentos
		 * @return json respuesta de la peticion
		 */
		public  function saveConsecutives()
		{
			$json   = file_get_contents('php://input');
			$data   = json_decode($json,true);

			$sql = "SELECT * FROM nomina_configuracion_consecutivos WHERE activo=1 AND tipo='NominaIndividual'";
			$query = $this->mysql->query($sql);
			if($this->mysql->num_rows($query)>0){
				$sql = "UPDATE nomina_configuracion_consecutivos 
							SET prefijo='$data[nomina_individual_prefijo]',
							consecutivo='$data[nomina_individual_consecutivo]'
							WHERE tipo= 'NominaIndividual' AND id_empresa=$_SESSION[EMPRESA]";
				$query = $this->mysql->query($sql);
			}
			else{
				$sql = "INSERT INTO nomina_configuracion_consecutivos (prefijo,consecutivo,codigo,tipo,id_empresa,id_sucursal) VALUES 
						( '$data[nomina_individual_prefijo]','$data[nomina_individual_consecutivo]','102','NominaIndividual','$_SESSION[EMPRESA]','$_SESSION[SUCURSAL]' )";
				$query = $this->mysql->query($sql);
			}

			$retVal[] = ($query)? ["tipo" => "NominaIndividual","status"=>"success"] : ["tipo" => "NominaIndividual","status"=>"error","message"=>$this->mysql->error()] ;

			$sql = "SELECT * FROM nomina_configuracion_consecutivos WHERE activo=1 AND tipo='NominaIndividualDeAjuste'";
			$query = $this->mysql->query($sql);
			if($this->mysql->num_rows($query)>0){
				$sql = "UPDATE nomina_configuracion_consecutivos 
							SET prefijo='$data[ajuste_modificacion_prefijo]',
							consecutivo='$data[ajuste_modificacion_consecutivo]'
							WHERE tipo= 'NominaIndividualDeAjuste' AND id_empresa=$_SESSION[EMPRESA]";
				$query = $this->mysql->query($sql);
			}
			else{
				$sql = "INSERT INTO nomina_configuracion_consecutivos (prefijo,consecutivo,codigo,tipo,id_empresa,id_sucursal) VALUES 
						( '$data[ajuste_modificacion_prefijo]','$data[ajuste_modificacion_consecutivo]','103','NominaIndividualDeAjuste','$_SESSION[EMPRESA]','$_SESSION[SUCURSAL]' )";
				$query = $this->mysql->query($sql);
			}

			$retVal[] = ($query)? ["tipo" => "NominaIndividualDeAjuste","status"=>"success"] : ["tipo" => "NominaIndividualDeAjuste","status"=>"error","message"=>$this->mysql->error()] ;


			echo json_encode($retVal);
	
		}

		/**
		 * payRollPeriods configurar los periodos de pago de la nomina
		 * @return json respuesta de la peticion
		 */
		public function payRollPeriods()
		{	
			// primero agregar la columna de codigo dian si no lo tiene la tabla
			$sql = "ALTER TABLE nomina_tipos_liquidacion ADD codigo VARCHAR( 255 ) after dias ";
			$query = $this->mysql->query($sql);

			$sql = "UPDATE nomina_tipos_liquidacion SET activo=0";
			$query = $this->mysql->query($sql);
			$retVal[] = ($query)? ["tipo" => "deleteRows","status"=>"success"] : ["tipo" => "deleteRows","status"=>"error","message"=>$this->mysql->error()] ;


			$sql = "INSERT INTO nomina_tipos_liquidacion (codigo_dian,nombre,dias,id_empresa) 
						VALUES 
						('1', 'Semanal',8,$_SESSION[EMPRESA]),
						('2', 'Decenal',10,$_SESSION[EMPRESA]),
						('3', 'Catorcenal',14,$_SESSION[EMPRESA]),
						('4', 'Quincenal',15,$_SESSION[EMPRESA]),
						('5', 'Mensual',30,$_SESSION[EMPRESA]),
						('6', 'Otro',0,$_SESSION[EMPRESA])";
			$query = $this->mysql->query($sql);
			$retVal[] = ($query)? ["tipo" => "insertedRoes","status"=>"success"] : ["tipo" => "insertedRoes","status"=>"error","message"=>$this->mysql->error()] ;

			echo json_encode($retVal);

		}

		


	}

