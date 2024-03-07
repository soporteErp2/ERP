<?php

	include_once("interface.apiDb.php");

	/**
	* Api MySqli
	*
	* @return obj $link conexion a la base de datos
	*/
	class MySqli implements apiDb{

		public 	$link;
		private $totalQuery = 0;
		private $ServidorDb = "";
		private $UsuarioDb  = "";
		private $PasswordDb = "";
		private $NameDb     = "";

		/**
		* Constructor que instancia las variables de conexion
		*
		* @param str ServidorDb
		* @param str UsuarioDb
		* @param str PasswordDb
		* @param str NameDb
		*/

		function __construct($ServidorDb,$UsuarioDb,$PasswordDb,$NameDb){
			// parent::__construct();
			$this->ServidorDb = $ServidorDb;
			$this->UsuarioDb  = $UsuarioDb;
			$this->PasswordDb = $PasswordDb;
			$this->NameDb     = $NameDb;
		}

		public function conectar(){
			if(!isset($this->link)){
				$this->link = (mysqli_connect($this->ServidorDb,$this->UsuarioDb,$this->PasswordDb));
				if(!$this->link){ echo 'Error Conectando a Mysql<br />'; exit; }

				mysqli_select_db($this->link,$this->NameDb) or die(mysqli_error());
				if(!@mysql_select_db($this->NameDb,$this->link)){ echo '-Error Conectando a la la base de datos "'.$this->NameDb.'" <br />'; exit; }

				return $this->link;
			}
		}

		public function connect($server,$usuario,$password,$bd=null){
			return mysqli_connect($server,$usuario,$password,$bd);
		}

		public function select_db($nombre,$link){
			return mysqli_select_db($link,$nombre);
		}

		public function query($sql,$link=null){
			$link = $link==null? $this->link: $link;

			$this->totalQuery++;
			return mysqli_query($link,$sql);
		}

		public function fetch_array($query){
			return mysqli_fetch_array($query);
		}

		public function fetch_assoc($query){
			return mysqli_fetch_assoc($query);
		}

		public function fetch_object($query){
			return mysqli_fetch_object($query);
		}

		public function fetch_row($query){
			return mysqli_fetch_row($query);
		}

		public function num_rows($query){
			return mysqli_num_rows($query);
		}

		public function numrows($query){
			return mysqli_num_rows($query);
		}

		public function result($query,$fila=0,$col=0){
			$matriz = mysqli_fetch_all($query, MYSQLI_BOTH);
			return $matriz[$fila][$col];
		}

		public function insert_id($link=null){
			return $link == null? mysqli_insert_id(): mysqli_insert_id($link);
		}

		public function num_fields($query){
			return mysqli_num_fields($query);
		}

		public function set_charset($charset,$link=null){
			return $link == null? mysqli_set_charset($this->link,$charset): mysqli_set_charset($link,$charset);
		}

		public function close($connect=null){
			return $link == null? mysqli_close(): mysqli_close($link);
		}

		public function free_result($query=null){
			return $query == null? mysqli_free_result(): mysqli_free_result($query);
		}

		public function errno(){
			return mysqli_errno();
		}

		public function error(){
			return mysqli_error();
		}

		public function getQuerys(){
			return $this->totalQuery;
		}
	}

?>