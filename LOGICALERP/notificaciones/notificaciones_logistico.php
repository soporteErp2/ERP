<?php
	////////// DESARROLLO ////////////
	$path       ='C:/PROYECTOS';
	$conexionDB ='192.168.8.202';
	$user       ='root';
	$pass       ='serverchkdsk';

	//Conexion DB -->
	$link = mysql_connect($conexionDB,$user,$pass);
	if(!$link){echo 'Error Conectando a Mysql<br />';};
	mysql_select_db('siip',$link);
	if(!@mysql_select_db('siip',$link)){echo 'Error Conectando a la la base de datos "'.$bd.'" <br />'; }

	$contSelect    =0;
	$sqlSelect     ="SELECT id FROM pedido WHERE estado=3 AND estado_pedido=4 AND tipo_responsable='remisionado'";
	$sqlAutoUpdate ="UPDATE pedido set estado_pedido=5 WHERE ";
	$querySelect   =mysql_query($sqlSelect,$link);

	while($row=mysql_fetch_array($querySelect)){
		if($contSelect!=0){ $sqlAutoUpdate.= " OR ";}
		$sqlAutoUpdate.="id='".$row['id']."'";
		$contSelect++;
	}
	$queryAutoUpdate=mysql_query($sqlAutoUpdate,$link);
?>