<?php

	include_once("interface.apiDb.php");

	/**
	* Api Mysql
	*
	* @return obj $link conexion a la base de datos
	*/
	class MySql implements apiDb{

		public 	$link;
		private $totalQuery = 0;
		public $ServidorDb = "";
		public $UsuarioDb  = "";
		public $PasswordDb = "";
		public $NameDb     = "";

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
				$this->link = mysql_connect($this->ServidorDb,$this->UsuarioDb,$this->PasswordDb);
				if(!$this->link){ echo 'Error Conectando a Mysql<br />'; exit; }

				mysql_select_db($this->NameDb,$this->link);
				if(!@mysql_select_db($this->NameDb,$this->link)){ echo '-Error Conectando a la la base de datos "'.$this->NameDb.'" <br />'; exit; }

				return $this->link;
			}
		}

		public function connect($server,$usuario,$password,$bd=null){
			return mysql_connect($server,$usuario,$password,$bd);
		}

		public function select_db($nombre,$link){
			return mysql_select_db($nombre,$link);
		}

		public function query($sql,$link=null){
			$link = $link==null? $this->link: $link;

			$this->totalQuery++;
			return mysql_query($sql,$link);
		}

		public function fetch_array($query){
			return mysql_fetch_array($query);
		}

		public function fetch_assoc($query){
			return mysql_fetch_assoc($query);
		}

		public function fetch_object($query){
			return mysql_fetch_object($query);
		}

		public function real_escape_string($value){
			return mysql_real_escape_string($value);
		}

		public function fetch_row($query){
			return mysql_fetch_row($query);
		}

		public function num_rows($query){
			return mysql_num_rows($query);
		}

		public function numrows($query){
			return mysql_num_rows($query);
		}

		public function result($result,$fila=0,$col=null){
			return mysql_result($result,$fila,$col);
		}

		public function insert_id($link=null){
			return $link == null? mysql_insert_id(): mysql_insert_id($link);
		}

		public function num_fields($query){
			return mysql_num_fields($query);
		}

		public function set_charset($charset,$link=null){
			return $link == null? mysql_set_charset($charset,$this->link): mysql_set_charset($charset,$link);
		}

		public function close($link=null){
			return $link == null? mysql_close($this->link): mysql_close($link);
		}

		public function free_result($query=null){
			return $query == null? mysql_free_result(): mysql_free_result($query);
		}

		public function errno(){
			return mysql_errno();
		}

		public function error(){
			return mysql_error();
		}

		public function getQuerys(){
			return $this->totalQuery;
		}

		public function mysql_affected_rows($link=null){
			$link = $link==null? $this->link: $link;
			return mysql_affected_rows($link);
		}
		
	}

?>