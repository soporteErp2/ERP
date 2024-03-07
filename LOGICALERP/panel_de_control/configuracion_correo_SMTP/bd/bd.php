<?php
include("../../../../configuracion/conectar.php");
include('../../../../configuracion/define_variables.php');

$id_empresa=$_SESSION['EMPRESA'];

switch ($op) {

	case "cargaConfig":
		cargaConfig($id_empresa,$link);
		break;

	case "guardaConfig":
		guardaConfig($id_empresa,$servidor,$correo,$password,$puerto,$seguridad,$autenticacion,$link);
		break;




}


///////////////////////////// CONFIGURACION CORREOS SMTP ////////////////////////////////////////////////////////////////////////////////

function cargaConfig($id_empresa,$link){
	$sql="SELECT * FROM empresas_config_correo WHERE id_empresa='".$id_empresa."'";
	$result = mysql_query($sql,$link);
	if(mysql_num_rows($result)){
		$servidor		= mysql_result($result,$i,"servidor");
		$correo 		= mysql_result($result,$i,"correo");
		$password		= mysql_result($result,$i,"password");
		$puerto			= mysql_result($result,$i,"puerto");
		$seguridad		= mysql_result($result,$i,"seguridad_smtp");
		$autenticacion	= mysql_result($result,$i,"autenticacion");

		echo  'true{.}'.$servidor.'{.}'.$correo.'{.}'.$password.'{.}'.$puerto.'{.}'.$seguridad.'{.}'.$autenticacion.'{.}';
	}else
		echo  'false{.}No existen datos, debes ingresar la configuracion.';
}

function guardaConfig($id_empresa,$servidor,$correo,$password,$puerto,$seguridad,$autenticacion,$link){
	$sql = "UPDATE empresas_config_correo
			SET servidor='$servidor' ,correo='$correo' ,password='$password' ,puerto='$puerto' ,seguridad_smtp='$seguridad' ,autenticacion='$autenticacion'
			WHERE id_empresa=".$id_empresa;

   $connectid = mysql_query($sql,$link);

   $num = mysql_affected_rows($link);

   if($num=="0"){
	   $sql = 	"INSERT INTO empresas_config_correo
				(id_empresa,servidor,correo,password,puerto,seguridad_smtp,autenticacion)
				VALUES
				('".$id_empresa."','".$servidor."','".$correo."','".$password."','".$puerto."','".$seguridad."','".$autenticacion."')";

	   $connectid =mysql_query($sql,$link);
   }


	if($connectid){
		echo 'true{.}Guardado.';
		mylog('ACTUALIZA CONFIG SMTP EMPRESA '.$id_empresa.' -> '.$sql,4,$link);
	}else{
		echo 'false{.}Error, no se guardo.'.$sql;
	}
}


?>

