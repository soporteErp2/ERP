<?php 
error_reporting(0);

//header('<!DOCTYPE html>')
header('Content-Type: text/html; charset=iso-8859-1');

if(isset($_SESSION)){
	//setcookie($_SESSION['APP']."_cookie", $_SESSION["valor_cookie"], time() + (7 * 86400) );
	setcookie("LogicalERP", $_SESSION["valor_cookie"], time() + (7 * 86400) );
}
?>