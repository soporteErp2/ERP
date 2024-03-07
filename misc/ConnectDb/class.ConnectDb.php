<?php
	/**
	* CLASS ConnectDb
	*
	* Clase que extrae el desarrollo de la Api de conexion a la base de datos
	* Instancia la clase con la Api de conexion de la base de datos a usar
	*
	* @author Jhon Erick <jhon.marroquin@logicalsoft.co>
	*/
	class ConnectDb{

		private $ApiDb;

		/**
		* Constructor - crea el objeto Api de la base de datos con las variables de conexion
		*
		* @param str Api a utilizar para la conexion a la base de datos MySql, MySqli
		* @param str ServidorDb
		* @param str UsuarioDb
		* @param str PasswordDb
		* @param str NameDb
		*/
		function __construct($apiSql,$ServidorDb,$UsuarioDb,$PasswordDb,$NameDb){
			// parent::__construct();

			/**
			* Define la Api o motor de base de datos a utilizar
			*/
			// echo "class.".$apiSql.".php";
			if (!class_exists($apiSql)) {
				require_once("class.".$apiSql.".php");
			}
			$this->ApiDb = new $apiSql($ServidorDb,$UsuarioDb,$PasswordDb,$NameDb);
		}

		function __destruct() { $this->ApiDb->close(); }

		/**
		* @return ApiDb obj Api del motor base de datos seleccionado
		*/
	   	function getApi(){ return $this->ApiDb; }
	}


?>