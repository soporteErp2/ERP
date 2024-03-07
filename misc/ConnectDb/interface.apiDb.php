<?php
	/**
	* Reglas para la construccion de interfaces de conexion
	*/

	interface apiDb{

		public function conectar();

		public function connect($server,$usuario,$password,$bd);

		public function select_db($nombre,$link);

		public function query($sql,$connect);

		public function fetch_array($query);

		public function fetch_assoc($query);

		public function fetch_object($query);

		public function fetch_row($query);

		public function num_rows($query);

		public function numrows($query);

		public function result($query,$fila,$col);

		public function insert_id($link);

		public function num_fields($query);

		public function set_charset($charset,$link);

		public function close($connect);

		public function free_result($query);

		public function errno();

		public function error();

		public function getQuerys();
	}

?>