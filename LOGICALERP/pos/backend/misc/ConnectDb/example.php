<?php

	include_once('class.ConnectDb.php');

	$objConectDB = new ConnectDb(
					"MySql",			// API SQL A UTILIZAR  MySql, MySqli
					"192.168.8.202",	// SERVIDOR
					"root",				// USUARIO DATA BASE
					"serverchkdsk",		// PASSWORD DATA BASE
					"siip"				// NOMBRE DATA BASE
				);

	$mysql = $objConectDB->getApi();
	$link  = $mysql->conectar();

	$sql   = "SELECT id,nombre FROM terceros WHERE id=1";
	$query = $mysql->query($sql,$link);

	echo $mysql->result($query,0,1);

?>