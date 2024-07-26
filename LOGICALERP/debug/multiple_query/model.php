<?php

	require_once('conexion.php');
	/**
	* 
	*/
	class Synchronize
	{
		
		private $conn;
		private $link;
		private $dbHost;
		private $column;

		function __construct()
		{
			$this->conn   = new Conexion();
			$this->link   = $this->conn->conectarse();
			$this->dbHost = 'host';//Nombre de la tabla donde se guardan las multiples bases de datos
			$this->column = 'bd';//Nombre del campo donde se guarda la base de datos
		}

		public function getDataBases()
		{
			$query="SELECT DISTINCT ".$this->column." AS bd FROM ".$this->dbHost." WHERE activo = 1";
			$result=mysql_query($query, $this->link);
			$data=array();
			while ($data[]=@mysql_fetch_assoc($result));
			array_pop($data);
			return $data;
		}

		public function runScript($script, $dataBases)
		{
			$message=array();
			foreach ($dataBases as $key => $value)
			{
				$newLink = $this->conn->conectarse($value);
				$sql=explode("{.}", $script);

				foreach ($sql as $indice => $query)
				{
					if(trim($query)!="")
					{
						$result=mysql_query(htmlspecialchars_decode($query), $newLink);
						if($result)
						{
							$message[]=array('success'=>'Script ejecutado correctamente en la base de datos: '.$value.'<br/>'.$query);
						}else
						{
							$message[]=array('error'=>'Error al ejecutar el Script en la base de datos: '.$value.'<br/>'.$query);
						}
					}
				}
				mysql_close($newLink);
			}
			return $message;
		}

		public function getConn(){
			return $this->conn;
		}
	}