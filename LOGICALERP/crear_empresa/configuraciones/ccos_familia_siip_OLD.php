<?php
	// include_once("../../../configuracion/conectar.php");

	// $id_empresa = 9;

	//============================== FAMILIA GRUPO ================================//
	/*******************************************************************************/
	$arrayF[] = array('codigo'=>'10', 'nombre'=>'Alquiler de Equipos');
	$arrayF[] = array('codigo'=>'20', 'nombre'=>'Servicios');
	$arrayF[] = array('codigo'=>'30', 'nombre'=>'Venta de Equipos');
	$arrayF[] = array('codigo'=>'50', 'nombre'=>'Refinanciacion');

	$valueInsertF = "";
	foreach ($arrayF as $subArray) {
		$valueInsertF .= "('".$subArray['codigo']."','".$subArray['nombre']."','$id_empresa'),";
	}
	$valueInsertF = substr($valueInsertF, 0, -1);
	$sqlInsertF   = "INSERT INTO items_familia (codigo,nombre,id_empresa) VALUES $valueInsertF";
	$queryInsertF = mysql_query($sqlInsertF,$link);

	if (!$queryInsertF) { $error.="NO SE INSERTARON LAS FAMILIAS POR DEFECTO DE LOS ITEMS <br/>"; }

	$sqlIdF = "SELECT codigo,id FROM items_familia WHERE id_empresa=$id_empresa";

	$queryIdF = mysql_query($sqlIdF,$link);
	while ($row = mysql_fetch_array($queryIdF)) {
		$arrayIdF[$row['codigo']] = $row['id'];
	}

	//============================== FAMILIA GRUPO ================================//
	/*******************************************************************************/
	$arrayFG[] = array('codigo_F'=>10, 'codigo'=>'01', 'nombre'=>'Video');
	$arrayFG[] = array('codigo_F'=>10, 'codigo'=>'02', 'nombre'=>'Audio');
	$arrayFG[] = array('codigo_F'=>10, 'codigo'=>'03', 'nombre'=>'Informatica');
	$arrayFG[] = array('codigo_F'=>10, 'codigo'=>'04', 'nombre'=>'Iluminacion');
	$arrayFG[] = array('codigo_F'=>10, 'codigo'=>'05', 'nombre'=>'Audiovisuales');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'01', 'nombre'=>'Traduccion Simultanea');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'02', 'nombre'=>'Centro de Negocios');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'03', 'nombre'=>'Mantenimiento');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'04', 'nombre'=>'Comunicaciones Digitales');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'05', 'nombre'=>'Registrese!');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'06', 'nombre'=>'Servicios Varios');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'07', 'nombre'=>'Soluciones Innovadoras');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'08', 'nombre'=>'Software ASISTE');
	$arrayFG[] = array('codigo_F'=>50, 'codigo'=>'01', 'nombre'=>'Refinanciacion cartera');

	$valueInsertFG = "";
	foreach ($arrayFG as $subArray) {
		$codigo_F = $subArray['codigo_F'];
		$valueInsertFG .= "('".$arrayIdF[$codigo_F]."','".$subArray['codigo']."','".$subArray['nombre']."','$id_empresa'),";
	}
	$valueInsertFG = substr($valueInsertFG, 0, -1);

	$sqlInsertFG   = "INSERT INTO items_familia_grupo (id_familia,codigo,nombre,id_empresa) VALUES $valueInsertFG";
	$queryInsertFG = mysql_query($sqlInsertFG,$link);

	if (!$queryInsertFG) { $error.="NO SE INSERTARON LOS GRUPOS POR DEFECTO DE LOS ITEMS <br/>"; }

	$sqlIdFG   = "SELECT cod_familia,codigo,id,nombre FROM items_familia_grupo WHERE id_empresa=$id_empresa";
	$queryIdfG = mysql_query($sqlIdFG,$link);
	while ($row = mysql_fetch_array($queryIdfG)) {
		$arrayIdFG[$row['cod_familia']][$row['codigo']] = $row['id'];

		$arrayIdFGS[$row['nombre']] = $row['id'];
	}

	// INSERTAR LA CONFIGURACION CONTABLE COLGAAP
	$sql="INSERT INTO asientos_colgaap_default_grupos  (id_grupo,grupo,descripcion,estado,cuenta,id_empresa)
			VALUES
			('".$arrayIdFGS['Video']."', 'Video', 'items_compra_costo', 'debito', 74202008,$id_empresa),
			('".$arrayIdFGS['Video']."', 'Video', 'items_venta_precio', 'credito', 41552501,$id_empresa),
			('".$arrayIdFGS['Video']."', 'Video', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Video']."', 'Video', 'items_compra_precio', 'debito', 74202008,$id_empresa),
			('".$arrayIdFGS['Video']."', 'Video', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_compra_precio', 'debito', 74202008,$id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_compra_costo', 'debito', 74202008,$id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_venta_precio', 'credito', 41552501,$id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_compra_precio', 'debito', 74202008,$id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_compra_costo', 'debito', 74202008,$id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_venta_precio', 'credito', 41552502,$id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_compra_precio', 'debito', 74202008,$id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_compra_costo', 'debito', 74202008,$id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_venta_precio', 'credito', 41552502,$id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_compra_precio', 'debito', 74202009,$id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_compra_costo', 'debito', 74202009,$id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_venta_precio', 'credito', 41552501,$id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_compra_precio', 'debito', 74202012,$id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_compra_costo', 'debito', 74202012,$id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_venta_precio', 'credito', 41552501,$id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_compra_precio', 'debito', 73953001,$id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_compra_impuesto', 'debito', 24080219,$id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_compra_costo', 'debito', 73953001,$id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_venta_precio', 'credito', 41558501,$id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_venta_impuesto', 'credito', 24080223,$id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_compra_precio', 'debito', 73451501,$id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_compra_costo', 'debito', 73451501,$id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_venta_precio', 'credito', 41554001,$id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_compra_precio', 'debito', 74202011,$id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_compra_costo', 'debito', 74202011,$id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_venta_precio', 'credito', 41559503,$id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_compra_precio', 'debito', 74202014,$id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_compra_activo_fijo', 'debito', 74202014,$id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_compra_costo', 'debito', 74202014,$id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_venta_precio', 'credito', 41553002,$id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_compra_precio', 'debito', 74202013,$id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_compra_costo', 'debito', 74202013,$id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_venta_precio', 'credito', 41704001,$id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_compra_precio', 'debito', 74202016,$id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_compra_costo', 'debito', 74202016,$id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_venta_precio', 'credito', 41709501,$id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_precio', 'debito', 73352001,$id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_gasto', 'debito', 51352001,$id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_costo', 'debito', 73352001,$id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_venta_precio', 'credito', 41553001,$id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_impuesto', 'debito', 24080223,$id_empresa)";
	$query=mysql_query($sql,$link);

	// INSERTAR LA CONFIGURACION CONTABLE NIIF
	$sql="INSERT INTO asientos_niif_default_grupos (id_grupo,grupo,descripcion,estado,cuenta,id_empresa)
			VALUES
			('".$arrayIdFGS['Video']."', 'Video', 'items_venta_precio', 'credito',  41552501, $id_empresa),
			('".$arrayIdFGS['Video']."', 'Video', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Video']."', 'Video', 'items_compra_costo', 'debito',  74202008, $id_empresa),
			('".$arrayIdFGS['Video']."', 'Video', 'items_compra_precio', 'debito',  74202008, $id_empresa),
			('".$arrayIdFGS['Video']."', 'Video', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_compra_precio', 'debito',  74202008, $id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_compra_costo', 'debito',  74202008, $id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_venta_precio', 'credito',  41552501, $id_empresa),
			('".$arrayIdFGS['Audio']."', 'Audio', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_compra_precio', 'debito',  74202008, $id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_compra_costo', 'debito',  74202008, $id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_venta_precio', 'credito',  41552502, $id_empresa),
			('".$arrayIdFGS['Informatica']."', 'Informatica', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_compra_precio', 'debito',  74202008, $id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_compra_costo', 'debito',  74202008, $id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_venta_precio', 'credito',  41552502, $id_empresa),
			('".$arrayIdFGS['Iluminacion']."', 'Iluminacion', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_compra_precio', 'debito',  74202009, $id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_compra_costo', 'debito',  74202009, $id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_venta_precio', 'credito',  41552501, $id_empresa),
			('".$arrayIdFGS['Audiovisuales']."', 'Audiovisuales', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_compra_precio', 'debito',  74202012, $id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_compra_costo', 'debito',  74202012, $id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_venta_precio', 'credito',  41552501, $id_empresa),
			('".$arrayIdFGS['Traduccion Simultanea']."', 'Traduccion Simultanea', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_compra_precio', 'debito',  73953001, $id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_compra_impuesto', 'debito',  24080219, $id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_compra_costo', 'debito',  73953001, $id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_venta_impuesto', 'credito',  24080223, $id_empresa),
			('".$arrayIdFGS['Centro de Negocios']."', 'Centro de Negocios', 'items_venta_precio', 'credito',  41558501, $id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_compra_precio', 'debito',  73451501, $id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_compra_costo', 'debito',  73451501, $id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_venta_precio', 'credito',  41554001, $id_empresa),
			('".$arrayIdFGS['Mantenimiento']."', 'Mantenimiento', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_compra_precio', 'debito',  74202011, $id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_compra_costo', 'debito',  74202011, $id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_venta_precio', 'credito',  41559503, $id_empresa),
			('".$arrayIdFGS['Comunicaciones Digitales']."', 'Comunicaciones Digitales', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_compra_precio', 'debito',  74202014, $id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_compra_activo_fijo', 'debito',  74202014, $id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_compra_costo', 'debito',  74202014, $id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_venta_precio', 'credito',  41553002, $id_empresa),
			('".$arrayIdFGS['Registrese!']."', 'Registrese!', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_compra_precio', 'debito',  74202013, $id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_compra_costo', 'debito',  74202013, $id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_venta_precio', 'credito',  41704001, $id_empresa),
			('".$arrayIdFGS['Servicios Varios']."', 'Servicios Varios', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_compra_precio', 'debito',  74202016, $id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_compra_costo', 'debito',  74202016,  $id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_venta_precio', 'credito',  41709501, $id_empresa),
			('".$arrayIdFGS['Soluciones Innovadoras']."', 'Soluciones Innovadoras', 'items_venta_impuesto', 'credito',  24080107,  $id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_precio', 'debito',  73352001,  $id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_gasto', 'debito',  51352001, $id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_costo', 'debito',  73352001, $id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_venta_precio', 'credito',  41553001, $id_empresa),
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_venta_impuesto', 'credito',  24080107, $id_empresa)";
	$query=mysql_query($sql,$link);

	//======================== FAMILIA GRUPO - SUBGRUPO ===========================//
	/*******************************************************************************/
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Video Beam');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'02', 'nombre'=>'Telones');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'03', 'nombre'=>'Pantallas de Proyeccion');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'04', 'nombre'=>'Pantallas de Video');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'05', 'nombre'=>'Camaras y CCTV');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'06', 'nombre'=>'Accesorios de Video');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'02', 'codigo'=>'01', 'nombre'=>'Sonido');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'03', 'codigo'=>'01', 'nombre'=>'Computadores');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'03', 'codigo'=>'02', 'nombre'=>'Impresoras - Fotocopiador');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'03', 'codigo'=>'03', 'nombre'=>'Tableta');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'03', 'codigo'=>'04', 'nombre'=>'Accesorios Informatica');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'04', 'codigo'=>'01', 'nombre'=>'Luces');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'04', 'codigo'=>'02', 'nombre'=>'Efectos');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'04', 'codigo'=>'03', 'nombre'=>'Accesorios de Iluminacion');
	$arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'05', 'codigo'=>'01', 'nombre'=>'Combos');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Equipos de Traduccion');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'01', 'codigo'=>'02', 'nombre'=>'Interprete');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'01', 'codigo'=>'03', 'nombre'=>'Accesorios Traduccion Simultanea');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'02', 'codigo'=>'01', 'nombre'=>'Servicios Centros de Negocios');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'03', 'codigo'=>'01', 'nombre'=>'Reparacion');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'03', 'codigo'=>'02', 'nombre'=>'Mantenimiento');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'04', 'codigo'=>'01', 'nombre'=>'Streaming');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'04', 'codigo'=>'02', 'nombre'=>'Video Conferencia');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'05', 'codigo'=>'01', 'nombre'=>'Servicio de Registro');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'05', 'codigo'=>'02', 'nombre'=>'Servicios Adicionales de Registro');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'08', 'codigo'=>'01', 'nombre'=>'Servicios Asiste');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'06', 'codigo'=>'01', 'nombre'=>'Varios');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'07', 'codigo'=>'01', 'nombre'=>'Smart Bar');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'07', 'codigo'=>'02', 'nombre'=>'Automata');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'07', 'codigo'=>'03', 'nombre'=>'Sistema de Llamado');
	$arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'07', 'codigo'=>'04', 'nombre'=>'Airport Guide');
	$arrayFGS[] = array('codigo_F'=>50, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Cartera');

	$valueInsertFGS = "";
	foreach ($arrayFGS as $subArray){
		$codigo_F = $subArray['codigo_F'];
		$codigo_FG = $subArray['codigo_FG'];
		$valueInsertFGS .= "('".$arrayIdF[$codigo_F]."','".$arrayIdFG[$codigo_F][$codigo_FG]."','".$subArray['codigo']."','".$subArray['nombre']."','$id_empresa'),";
	}
	$valueInsertFGS = substr($valueInsertFGS, 0, -1);

	$sqlInsertFGS   = "INSERT INTO items_familia_grupo_subgrupo (id_familia,id_grupo,codigo,nombre,id_empresa) VALUES $valueInsertFGS";
	$queryInsertFGS = mysql_query($sqlInsertFGS,$link);

	if (!$queryInsertFGS) { $error.="NO SE INSERTARON LOS GRUPOS POR DEFECTO DE LOS ITEMS <br/>"; }




	//INSERT DE LOS CENTROS DE COSTOS
	$sqlCentroCostos="INSERT INTO centro_costos (codigo,nombre,id_empresa) VALUES
						('101001','GERENCIA GENERAL',$id_empresa),
						('101002','SUBDIRECCION',$id_empresa),
						('101003','DIRECCION DE CALIDAD',$id_empresa),
						('101004','DIRECCION RECURSO HUMANO',$id_empresa),
						('101005','DIRECCION FINANCIERA',$id_empresa),
						('101006','DIRECCION DE SISTEMAS',$id_empresa),
						('102001','DIRECCION DE ZONA',$id_empresa),
						('102002','CARTERA',$id_empresa),
						('102003','CONTABILIDAD',$id_empresa),
						('102004','TESORERIA',$id_empresa),
						('102005','RESIDENTES',$id_empresa),
						('200001','DIRECCION COMERCIAL',$id_empresa),
						('201001','OTROS CLIENTES CORPORATIVOS',$id_empresa),
						('202001','CCCI',$id_empresa),
						('202002','CENTRO DE EVENTOS VALLE DEL PACIFICO',$id_empresa),
						('202003','CENTRO DE CONVENCIONES HILTON GARDEN',$id_empresa),
						('203001','CLUB COLOMBIA',$id_empresa),
						('203002','CLUB DE BANQUEROS',$id_empresa),
						('204000','EJECUTIVO COMERCIAL DIVISION HOTELES',$id_empresa),
						('204001','HOTEL SMART SUITE - MEDELLIN',$id_empresa),
						('204002','HOTEL FOUR POINT SHERATON MEDELLIN',$id_empresa),
						('204003','HOTEL MOVICH CASA DEL ALFEREZ',$id_empresa),
						('204004','HOTEL COUNTRY BARRANQUILLA',$id_empresa),
						('204005','HOTEL COSMOS EXPRESS 100 - CALI',$id_empresa),
						('204006','HOTEL IROTAMA',$id_empresa),
						('204007','HOTEL VIZCAYA REAL',$id_empresa),
						('204008','HOTEL DANN COMBEIMA',$id_empresa),
						('204009','HOTEL SORATAMA',$id_empresa),
						('204010','HOTEL PAVILLON ROYAL - BOGOTA',$id_empresa),
						('204011','HOTEL HELICONIAS',$id_empresa),
						('204012','HOTEL SONESTA BARRANQUILLA',$id_empresa),
						('204013','ARMENIA HOTEL',$id_empresa),
						('204014','HOTEL PLAZA LAS AMERICAS',$id_empresa),
						('204015','HOTEL BELVEDER',$id_empresa),
						('204016','HOTEL CASTILLA',$id_empresa),
						('204017','HOTEL SONESTA VALLEDUPAR',$id_empresa),
						('204018','HOTEL COSMOS PACIFICO - BUENAVENTURA',$id_empresa),
						('204019','HOTEL ZUANA',$id_empresa),
						('204020','HOTEL CASA SANTAMONICA',$id_empresa),
						('204021','HOTEL SMART SUITE - BARRANQUILLA',$id_empresa),
						('204022','HOTEL ALMIRANTE',$id_empresa),
						('204023','HOTEL SAN DIEGO DE MEDELLIN',$id_empresa),
						('204024','HOTEL CAPILLA DE MAR',$id_empresa),
						('204025','HOTEL HOLLYDAY INN BOGOTA',$id_empresa),
						('204026','HOTEL MS CHIPICHAPE',$id_empresa),
						('204027','HOTEL REGENCY SANTORINI',$id_empresa),
						('204028','HOTEL TORRE DE CALI',$id_empresa),
						('204029','HOTEL HOLLIDAY INN CARTAGENA',$id_empresa),
						('204030','HOTEL TRYP MEDELLIN',$id_empresa),
						('204031','HOTEL BEST WESTER PLUS BOGOTA',$id_empresa),
						('204032','HOTEL WAYA GUAJIRA',$id_empresa),
						('204033','HOTEL MILENIUM BARRANCA',$id_empresa),
						('204034','HOTEL CORALES',$id_empresa),
						('204035','HOTEL HEROES',$id_empresa),
						('204036','HOTEL CITY EXPRESS CALI',$id_empresa),
						('204037','HOTEL TRYP USAQUEN',$id_empresa),
						('204038','HOTEL FOUR POINT BOGOTA',$id_empresa),
						('204039','HOTEL COSMOS 116',$id_empresa),
						('204040','HOTEL BOG',$id_empresa),
						('204041','HOTEL SONESTA VILLAVICENCIO',$id_empresa),
						('204042','HOTEL HOJO BARRANQUILLA',$id_empresa),
						('204043','HOTEL PUERTA DEL SOL',$id_empresa),
						('204044','HOTEL 101 PARK HOUSE',$id_empresa),
						('204045','HOTEL LAS LOMAS',$id_empresa),
						('204046','HOTEL ESTELAR LA FERIA',$id_empresa),
						('204047','HOTEL ESTELAR SANTA MAR',$id_empresa),
						('204048','HOTEL ESTELAR LAS COLINAS',$id_empresa),
						('204049','HOTEL ESTELAR RECINTO DEL PENSAMIENTO',$id_empresa),
						('204050','HOTEL ESTELAR  SUITES JONES',$id_empresa),
						('204051','HOTEL ESTELAR MILLA DE ORO',$id_empresa),
						('204052','HOTEL ESTELAR ALTO PRADO',$id_empresa),
						('204053','HOTEL ESTELAR WINSORD HOUSE',$id_empresa),
						('204054','HOTEL ESTELAR ALTAMIRA',$id_empresa),
						('204055','HOTEL ESTELAR MANZANILLO',$id_empresa),
						('204056','HOTEL ESTELAR PAIPA',$id_empresa),
						('204057','HOTEL ESTELAR LA FONTANA',$id_empresa),
						('204058','HOTEL ESTALAR VILLAVICENCIO',$id_empresa),
						('204059','HOTEL ESTELAR YOPAL',$id_empresa),
						('204060','HOTEL ARAWAK',$id_empresa),
						('204061','HOTEL CAPITAL',$id_empresa),
						('204062','HOTEL STYLE 93',$id_empresa),
						('204063','HOTEL TRYP BUCARAMANGA',$id_empresa),
						('204064','HOTEL ABADIA',$id_empresa),
						('205001','HOTEL FOUR POINT SHERATON CALI',$id_empresa),
						('205002','HOTEL RADISSON CALI',$id_empresa),
						('205003','HOTEL DANN BARRANQUILLA',$id_empresa),
						('205004','HOTEL SHERATON BOGOTA',$id_empresa),
						('205005','HOTEL ROYAL PARK BOGOTA',$id_empresa),
						('205006','HOTEL MARRIOT BOGOTA',$id_empresa),
						('205007','HOTEL MOVICH PEREIRA',$id_empresa),
						('205008','HOTEL JW MARRIOT BOGOTA',$id_empresa),
						('205009','HOTEL COSMOS 100 BOGOTA',$id_empresa),
						('205010','HOTEL LAS AMERICAS',$id_empresa),
						('205011','HOTEL SONESTA BOGOTA',$id_empresa),
						('205012','HOTEL CARIBE',$id_empresa),
						('205013','HOTEL SPIWAK',$id_empresa),
						('205014','HOTEL SONESTA CERRITOS',$id_empresa),
						('205015','HOTEL RADISSON CARTAGENA',$id_empresa),
						('205016','HOTEL MARRIOT CALI',$id_empresa),
						('205017','HOTEL HILTON BOGOTA',$id_empresa),
						('205018','HOTEL MOCAWUA',$id_empresa),
						('205019','HOTEL WYNDHAM BOGOTA',$id_empresa),
						('205020','HOTEL BURO 26',$id_empresa),
						('205021','HOTEL HILTON GARDEN BARRANQUILLA',$id_empresa),
						('205022','HOTEL INTERCONTINENTAL MEDELLIN',$id_empresa),
						('205023','HOTEL ESTELAR INTERCONTINENTAL',$id_empresa),
						('205024','HOTEL TRYP BOGOTA',$id_empresa),
						('301001','DIRECCION OPERATIVA NACIONAL',$id_empresa),
						('301002','DIRECCION DE MANTENIMEINTO',$id_empresa),
						('302001','OPERACION LOGISTICA LOCAL',$id_empresa),
						('1010','ADMINISTRACION NACIONAL',$id_empresa),
						('1020','ADMINISTRACION LOCAL',$id_empresa),
						('1030','CARTERA',$id_empresa),
						('1040','CONTABILIDAD',$id_empresa),
						('1050','GERENCIA GENERAL',$id_empresa),
						('1060','REPRESENTACION GENERAL',$id_empresa),
						('1070','SISTEMAS',$id_empresa),
						('1080','TALENTO HUMANO',$id_empresa),
						('1090','TESORERIA',$id_empresa),
						('2010','CORPORATIVO',$id_empresa),
						('2020','CENTRO DE CONVENCIONES',$id_empresa),
						('2030','CLUBES',$id_empresa),
						('2040','DIVISION HOTELES',$id_empresa),
						('2550','HOTELES UNIDADES INDEPENDIENTES',$id_empresa),
						('3010','OPERACION LOGISTICA DE EVENTOS',$id_empresa),
						('4010','ASISTE',$id_empresa),
						('4020','REGISTRO',$id_empresa),
						('4030','CDE (CARTELERA DIGITAL DE EVENTOS)',$id_empresa),
						('4040','DESARROLLO',$id_empresa),
						('5010','DOMOTICA - AUTOMATIZACIONES',$id_empresa),
						('5020','MANTENIMIENTO DE EQUIPOS',$id_empresa),
						('5030','VENTA DE EQUIPOS',$id_empresa),
						('6010','PREPRODUCCION',$id_empresa),
						('6020','PRODUCCION',$id_empresa),
						('6030','POSTPRODUCCION',$id_empresa),
						('6040','VIDEO INSTITUCIONAL O PROMOCIONAL',$id_empresa),
						('6050','MULTIMEDIA',$id_empresa),
						('10','ADMINISTRACION',$id_empresa),
						('20','VENTAS',$id_empresa),
						('30','OPERATIVOS',$id_empresa),
						('40','TECNOLOGIA',$id_empresa),
						('50','MANTENIMIENTO',$id_empresa),
						('60','AUDIOVISUALES',$id_empresa)";

	$queryInsertCentroCostos=mysql_query($sqlCentroCostos,$link);


	if (!$queryInsertCentroCostos) { $error.="NO SE INSERTARON LOS CENTROS DE COSTOS POR DEFECTO <br/>"; }
?>