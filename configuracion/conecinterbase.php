<?php
$CUAL_BSD = $_SESSION[SAIOPEN]; 
//$host = "192.168.0.151:D:/SAIGROUP/$CUAL_BSD.GDB";
$host = "localhost:C:/SAIGROUP/$CUAL_BSD.GDB";
$dbh = ibase_connect($host, "SYSDBA", "masterkey");
?>