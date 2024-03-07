<?php
	// include_once("../../../configuracion/conectar.php");

	// $id_empresa = 9;

	//============================== FAMILIA GRUPO ================================//
	/*******************************************************************************/
	$arrayF[] = array('codigo'=>'10', 'nombre'=>'Alquiler de Equipos');
	$arrayF[] = array('codigo'=>'20', 'nombre'=>'Servicios');
	$arrayF[] = array('codigo'=>'30', 'nombre'=>'Venta de Equipos');
	$arrayF[] = array('codigo'=>'50', 'nombre'=>'Refinanciacion');
	$arrayF[] = array('codigo'=>'60', 'nombre'=>'Desarrollo de Software');
	$arrayF[] = array('codigo'=>'70', 'nombre'=>'Venta de Software');
	$arrayF[] = array('codigo'=>'80', 'nombre'=>'Alquiler de Software');
	$arrayF[] = array('codigo'=>'90', 'nombre'=>'Costos y Gastos');

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
	$arrayFG[] = array('codigo_F'=>10, 'codigo'=>'06', 'nombre'=>'Inmuebles');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'01', 'nombre'=>'Traduccion Simultanea');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'02', 'nombre'=>'Centro de Negocios');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'03', 'nombre'=>'Mantenimiento');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'04', 'nombre'=>'Comunicaciones Digitales');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'05', 'nombre'=>'Registrese!');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'06', 'nombre'=>'Servicios Varios');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'07', 'nombre'=>'Soluciones Innovadoras');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'08', 'nombre'=>'Software ASISTE');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'09', 'nombre'=>'Soporte y Mantenimiento');
	$arrayFG[] = array('codigo_F'=>20, 'codigo'=>'10', 'nombre'=>'Servicios de Hosting y Dominios');
	$arrayFG[] = array('codigo_F'=>50, 'codigo'=>'01', 'nombre'=>'Refinanciacion cartera');
	$arrayFG[] = array('codigo_F'=>60, 'codigo'=>'01', 'nombre'=>'Desarrollo de Software');
	$arrayFG[] = array('codigo_F'=>70, 'codigo'=>'01', 'nombre'=>'Licencias');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'05', 'nombre'=>'Costos y Gastos de Personal');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'10', 'nombre'=>'Honorarios');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'15', 'nombre'=>'Impuestos');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'20', 'nombre'=>'Arrendamientos');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'25', 'nombre'=>'Contribuciones y Afiliaciones');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'30', 'nombre'=>'Seguros');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'35', 'nombre'=>'Servicios');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'40', 'nombre'=>'Costos y Gastos Legales');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'45', 'nombre'=>'Mantenimiento y Reparaciones');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'50', 'nombre'=>'Adecuación e Instalación');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'55', 'nombre'=>'Costos y Gastos de Viaje');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'60', 'nombre'=>'Depreciaciones');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'65', 'nombre'=>'Amortizaciones');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'95', 'nombre'=>'Diversos');
	$arrayFG[] = array('codigo_F'=>90, 'codigo'=>'99', 'nombre'=>'Provisiones');

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
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_compra_precio', 'debito', 51201001,$id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_compra_gasto', 'debito', 51201001,$id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_compra_costo', 'debito', 74201001,$id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_venta_precio', 'credito', 42201001,$id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
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
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_venta_precio', 'credito', 41553501,$id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_compra_costo', 'debito', 74202013,$id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_compra_gasto', 'debito', 51352001,$id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_compra_impuesto', 'debito', 24080219,$id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_compra_precio', 'debito', 74202013,$id_empresa),
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
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_compra_precio', 'debito', 74202013,$id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_compra_impuesto', 'debito', 24080219,$id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_compra_gasto', 'debito', 51352001,$id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_compra_costo', 'debito', 74202013,$id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_venta_precio', 'credito', 41553003,$id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_compra_precio', 'debito', 51202006,$id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_compra_impuesto', 'debito', 24080223,$id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_compra_gasto', 'debito', 51202006,$id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_venta_precio', 'credito', 41553004,$id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_compra_costo', 'debito', 74202013,$id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_venta_impuesto', 'credito', 24080107,$id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_venta_precio', 'credito', 41355401,$id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_compra_gasto', 'debito', 74202013,$id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_compra_impuesto', 'debito', 24080219,$id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_compra_precio', 'debito', 74202013,$id_empresa)";
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
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_compra_precio', 'debito',  51201001, $id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_compra_gasto', 'debito',  51201001, $id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_compra_costo', 'debito',  74201001, $id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_venta_precio', 'credito',  42201001, $id_empresa),
			('".$arrayIdFGS['Inmuebles']."', 'Inmuebles', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
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
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_compra_precio', 'debito',  74202013, $id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_compra_impuesto', 'debito',  24080219, $id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_compra_gasto', 'debito',  51352001, $id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_compra_costo', 'debito',  74202013, $id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_venta_precio', 'credito',  41553501, $id_empresa),
			('".$arrayIdFGS['Desarrollo de Software']."', 'Desarrollo de Software', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
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
			('".$arrayIdFGS['Software ASISTE']."', 'Software ASISTE', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_compra_precio', 'debito',  74202013, $id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_compra_impuesto', 'debito',  24080219, $id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_compra_gasto', 'debito',  51352001, $id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_compra_costo', 'debito',  74202013, $id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_venta_precio', 'credito',  41553003, $id_empresa),
			('".$arrayIdFGS['Soporte y Mantenimiento']."', 'Soporte y Mantenimiento', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_compra_precio', 'debito',  51202006, $id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_compra_impuesto', 'debito',  24080223, $id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_compra_gasto', 'debito',  51202006, $id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_venta_precio', 'credito',  41553004, $id_empresa),
			('".$arrayIdFGS['Servicios de Hosting  y Dominios']."', 'Servicios de Hosting  y Dominios', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_compra_precio', 'debito',  74202013, $id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_compra_impuesto', 'debito',  24080219, $id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_compra_gasto', 'debito',  74202013, $id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_venta_precio', 'credito',  41355401, $id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_venta_impuesto', 'credito',  24080107, $id_empresa),
			('".$arrayIdFGS['Licencias']."', 'Licencias', 'items_compra_costo', 'debito',  74202013, $id_empresa)";
	$query=mysql_query($sql,$link);

	//======================== FAMILIA GRUPO - SUBGRUPO ===========================//
	/*******************************************************************************/
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Video Beam');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'02', 'nombre'=>'Telones');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'03', 'nombre'=>'Pantallas de Proyeccion');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'04', 'nombre'=>'Pantallas de Video');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'05', 'nombre'=>'Camaras y CCTV');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'01', 'codigo'=>'06', 'nombre'=>'Accesorios de Video');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'02', 'codigo'=>'01', 'nombre'=>'Sonido');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'03', 'codigo'=>'01', 'nombre'=>'Computadores');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'03', 'codigo'=>'02', 'nombre'=>'Impresoras - Fotocopiador');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'03', 'codigo'=>'03', 'nombre'=>'Tableta');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'03', 'codigo'=>'04', 'nombre'=>'Accesorios Informatica');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'04', 'codigo'=>'01', 'nombre'=>'Luces');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'04', 'codigo'=>'02', 'nombre'=>'Efectos');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'04', 'codigo'=>'03', 'nombre'=>'Accesorios de Iluminacion');
	// $arrayFGS[] = array('codigo_F'=>10, 'codigo_FG'=>'05', 'codigo'=>'01', 'nombre'=>'Combos');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Equipos de Traduccion');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'01', 'codigo'=>'02', 'nombre'=>'Interprete');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'01', 'codigo'=>'03', 'nombre'=>'Accesorios Traduccion Simultanea');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'02', 'codigo'=>'01', 'nombre'=>'Servicios Centros de Negocios');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'03', 'codigo'=>'01', 'nombre'=>'Reparacion');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'03', 'codigo'=>'02', 'nombre'=>'Mantenimiento');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'04', 'codigo'=>'01', 'nombre'=>'Streaming');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'04', 'codigo'=>'02', 'nombre'=>'Video Conferencia');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'05', 'codigo'=>'01', 'nombre'=>'Servicio de Registro');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'05', 'codigo'=>'02', 'nombre'=>'Servicios Adicionales de Registro');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'08', 'codigo'=>'01', 'nombre'=>'Servicios Asiste');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'06', 'codigo'=>'01', 'nombre'=>'Varios');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'07', 'codigo'=>'01', 'nombre'=>'Smart Bar');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'07', 'codigo'=>'02', 'nombre'=>'Automata');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'07', 'codigo'=>'03', 'nombre'=>'Sistema de Llamado');
	// $arrayFGS[] = array('codigo_F'=>20, 'codigo_FG'=>'07', 'codigo'=>'04', 'nombre'=>'Airport Guide');
	// $arrayFGS[] = array('codigo_F'=>50, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Cartera');

	$arrayFGS[] = array('codigo_F'=> 50, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Cartera');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Video Beam');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'01', 'codigo'=>'02', 'nombre'=>'Telones');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'01', 'codigo'=>'03', 'nombre'=>'Pantallas de Proyeccion');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'01', 'codigo'=>'04', 'nombre'=>'Pantallas de Video');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'01', 'codigo'=>'05', 'nombre'=>'Camaras y CCTV');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'01', 'codigo'=>'06', 'nombre'=>'Accesorios de Video');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'02', 'codigo'=>'01', 'nombre'=>'Sonido');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'03', 'codigo'=>'01', 'nombre'=>'Computadores');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'03', 'codigo'=>'02', 'nombre'=>'Impresoras - Fotocopiador');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'03', 'codigo'=>'03', 'nombre'=>'Tableta');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'03', 'codigo'=>'04', 'nombre'=>'Accesorios Informatica');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'04', 'codigo'=>'01', 'nombre'=>'Luces');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'04', 'codigo'=>'02', 'nombre'=>'Efectos');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'04', 'codigo'=>'03', 'nombre'=>'Accesorios de Iluminacion');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'05', 'codigo'=>'01', 'nombre'=>'Combos');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Equipos de Traduccion');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'01', 'codigo'=>'02', 'nombre'=>'Interprete');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'01', 'codigo'=>'03', 'nombre'=>'Accesorios Traduccion Simultanea');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'02', 'codigo'=>'01', 'nombre'=>'Servicios Centros de Negocios');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'03', 'codigo'=>'01', 'nombre'=>'Reparacion');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'03', 'codigo'=>'02', 'nombre'=>'Mantenimiento');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'04', 'codigo'=>'01', 'nombre'=>'Streaming');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'04', 'codigo'=>'02', 'nombre'=>'Video Conferencia');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'05', 'codigo'=>'01', 'nombre'=>'Servicio de Registro');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'05', 'codigo'=>'02', 'nombre'=>'Servicios Adicionales de Registro');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'08', 'codigo'=>'01', 'nombre'=>'Servicios Asiste');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'06', 'codigo'=>'01', 'nombre'=>'Varios');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'07', 'codigo'=>'01', 'nombre'=>'Smart Bar');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'07', 'codigo'=>'02', 'nombre'=>'Automata');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'07', 'codigo'=>'03', 'nombre'=>'Sistema de Llamado');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'07', 'codigo'=>'04', 'nombre'=>'Airport Guide');
	$arrayFGS[] = array('codigo_F'=> 60, 'codigo_FG'=>'01', 'codigo'=>'01', 'nombre'=>'Diseño y Desarrollo');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'06', 'codigo'=>'01', 'nombre'=>'Casas y Oficinas');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'06', 'codigo'=>'02', 'nombre'=>'Terrenos y Lotes');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'02', 'codigo'=>'02', 'nombre'=>'Traduccion');
	$arrayFGS[] = array('codigo_F'=> 10, 'codigo_FG'=>'05', 'codigo'=>'02', 'nombre'=>'Accesorios Audiovisuales y otros NCP');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'09', 'codigo'=>'01', 'nombre'=>'Soporte de Software');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'09', 'codigo'=>'02', 'nombre'=>'Mantenimiento de Software y Bases de Datos');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'09', 'codigo'=>'03', 'nombre'=>'Servicios de Backup');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'09', 'codigo'=>'04', 'nombre'=>'Soporte Y Mantenimiento de Infraestructura de Datos');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'10', 'codigo'=>'01', 'nombre'=>'Registro de Dominios');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'10', 'codigo'=>'02', 'nombre'=>'Servicio de Hosting');
	$arrayFGS[] = array('codigo_F'=> 20, 'codigo_FG'=>'04', 'codigo'=>'03', 'nombre'=>'Producción de Audiovisuales');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'03', 'nombre'=>'Salario Integral');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'06', 'nombre'=>'Sueldos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'15', 'nombre'=>'Horas Extras y Recargos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'18', 'nombre'=>'Comisiones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'21', 'nombre'=>'Viáticos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'24', 'nombre'=>'Incapacidades');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'27', 'nombre'=>'Auxilio de Transporte');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'30', 'nombre'=>'Cesantías');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'33', 'nombre'=>'Intereses Sobre Cesantías');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'36', 'nombre'=>'Prima de Servicios');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'39', 'nombre'=>'Vacaciones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'42', 'nombre'=>'Primas Extralegales');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'45', 'nombre'=>'Auxilios');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'48', 'nombre'=>'Bonificaciones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'51', 'nombre'=>'Dotación y Suministro a Trabajadores');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'54', 'nombre'=>'Seguros');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'57', 'nombre'=>'Cuotas Partes Pensiones de Jubilación');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'59', 'nombre'=>'Pensiones de Jubilación');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'60', 'nombre'=>'Indemnizaciones Laborales');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'63', 'nombre'=>'Capacitación Al Personal');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'66', 'nombre'=>'Gastos Deportivos y de Recreación');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'68', 'nombre'=>'Aportes A ARL');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'69', 'nombre'=>'Aportes Al I.S.S');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'70', 'nombre'=>'Aportes A Fondos de Pensiones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'72', 'nombre'=>'Aportes Cajas de Compensación Familiar');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'75', 'nombre'=>'Aportes I.C.B.F.');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'78', 'nombre'=>'Sena');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'84', 'nombre'=>'Gastos Medicos y Drogas');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'05', 'codigo'=>'95', 'nombre'=>'Otros C&G Gastos de Personal');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'10', 'codigo'=>'05', 'nombre'=>'Junta Directiva');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'10', 'codigo'=>'10', 'nombre'=>'Revisoria Fiscal');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'10', 'codigo'=>'20', 'nombre'=>'Avaluos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'10', 'codigo'=>'25', 'nombre'=>'Asesoria Juridica');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'10', 'codigo'=>'30', 'nombre'=>'Asesoria Financiera');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'10', 'codigo'=>'35', 'nombre'=>'Asesoria Tecnica');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'10', 'codigo'=>'95', 'nombre'=>'Otros C&G Honorarios');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'15', 'codigo'=>'05', 'nombre'=>'Industria y Comercio');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'15', 'codigo'=>'10', 'nombre'=>'De Timbres');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'15', 'codigo'=>'15', 'nombre'=>'A La Propiedad Raiz');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'15', 'codigo'=>'25', 'nombre'=>'De Valorizacion');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'15', 'codigo'=>'40', 'nombre'=>'De Vehiculos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'15', 'codigo'=>'70', 'nombre'=>'Iva Descontable');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'15', 'codigo'=>'95', 'nombre'=>'Otros C&G Impuestos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'20', 'codigo'=>'05', 'nombre'=>'Terrenos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'20', 'codigo'=>'10', 'nombre'=>'Construcciones y Edificaciones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'20', 'codigo'=>'15', 'nombre'=>'Maquinaria y Equipo');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'20', 'codigo'=>'20', 'nombre'=>'Equipo de Oficina');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'20', 'codigo'=>'25', 'nombre'=>'Equipo de Computacion y Comunicacion');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'20', 'codigo'=>'40', 'nombre'=>'Flota y Equipo de Transporte');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'20', 'codigo'=>'95', 'nombre'=>'Otros C&G Arrendamientos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'25', 'codigo'=>'05', 'nombre'=>'Contribuciones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'25', 'codigo'=>'10', 'nombre'=>'Afiliaciones y Sostenimiento');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'05', 'nombre'=>'Manejo');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'10', 'nombre'=>'Cumplimiento');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'15', 'nombre'=>'Corriente Debil');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'20', 'nombre'=>'Vida Colectiva');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'25', 'nombre'=>'Incendio');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'30', 'nombre'=>'Terremoto');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'35', 'nombre'=>'Sustraccion y Hurto');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'40', 'nombre'=>'Flota y Equipo de Transporte');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'60', 'nombre'=>'Responsabilidad Civil y Extracontractual');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'75', 'nombre'=>'Obligatorio Accidente de Transito');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'30', 'codigo'=>'95', 'nombre'=>'Otros C&G Seguros');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'05', 'nombre'=>'Aseo y Vigilancia');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'10', 'nombre'=>'Temporales');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'15', 'nombre'=>'Asistencia Tecnica');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'20', 'nombre'=>'Procesamiento Electronico de Datos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'25', 'nombre'=>'Acueducto y Alcantarillado');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'30', 'nombre'=>'Energia Electrica');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'35', 'nombre'=>'Telefono');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'40', 'nombre'=>'Correo Portes y Telegramas');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'45', 'nombre'=>'Fax y Telex');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'50', 'nombre'=>'Transporte Fletes y Acarreos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'55', 'nombre'=>'Gas');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'60', 'nombre'=>'Propaganda y Publicidad');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'35', 'codigo'=>'95', 'nombre'=>'Otros C&G Servicios');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'40', 'codigo'=>'05', 'nombre'=>'Notariales');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'40', 'codigo'=>'10', 'nombre'=>'Registro Mercantil');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'40', 'codigo'=>'15', 'nombre'=>'Tramites y Licencias');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'40', 'codigo'=>'20', 'nombre'=>'Aduaneros');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'40', 'codigo'=>'95', 'nombre'=>'Otros C&G Gastos Legales');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'45', 'codigo'=>'05', 'nombre'=>'Terrenos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'45', 'codigo'=>'10', 'nombre'=>'Construcciones y Edificaciones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'45', 'codigo'=>'15', 'nombre'=>'Maquinaria y Equipo');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'45', 'codigo'=>'20', 'nombre'=>'Equipo de Oficina');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'45', 'codigo'=>'25', 'nombre'=>'Equipo de Computacion y Comunicacion');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'45', 'codigo'=>'40', 'nombre'=>'Flota y Equipo de Transporte');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'50', 'codigo'=>'05', 'nombre'=>'Instalaciones Electricas');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'50', 'codigo'=>'10', 'nombre'=>'Arreglos Ornamentales');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'50', 'codigo'=>'15', 'nombre'=>'Reparaciones Locativas');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'50', 'codigo'=>'95', 'nombre'=>'Otros C&G Adecuación e Instalación');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'55', 'codigo'=>'05', 'nombre'=>'Alojamiento y Manutencion');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'55', 'codigo'=>'10', 'nombre'=>'Pasajes Fluviales Y/O Maritimos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'55', 'codigo'=>'15', 'nombre'=>'Pasajes Aereos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'55', 'codigo'=>'20', 'nombre'=>'Pasajes Terrestres');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'55', 'codigo'=>'95', 'nombre'=>'Otros C&G Gastos de Viaje');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'60', 'codigo'=>'05', 'nombre'=>'Construcciones y Edificaciones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'60', 'codigo'=>'10', 'nombre'=>'Maquinaria y Equipo');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'60', 'codigo'=>'15', 'nombre'=>'Equipo de Oficina');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'60', 'codigo'=>'20', 'nombre'=>'Equipo de Computacion y Comunicacion');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'60', 'codigo'=>'35', 'nombre'=>'Flota y Equipo de Transporte');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'60', 'codigo'=>'55', 'nombre'=>'Acueductos Plantas y Redes');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'60', 'codigo'=>'99', 'nombre'=>'Ajustes Por Inflacion');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'05', 'nombre'=>'Comisiones');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'10', 'nombre'=>'Libros Suscripciones Periodicos y Revistas');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'15', 'nombre'=>'Musica Ambiental');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'20', 'nombre'=>'Gastos de Representacion y Relaciones Publicas');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'25', 'nombre'=>'Elementos de Aseo y Cafeteria');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'30', 'nombre'=>'Utiles Papeleria y Fotocopias');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'35', 'nombre'=>'Combustibles y Lubricantes');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'40', 'nombre'=>'Envases y Empaques');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'45', 'nombre'=>'Taxis y Buses');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'50', 'nombre'=>'Estampillas');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'60', 'nombre'=>'Casino y Restaurante');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'65', 'nombre'=>'Parqueaderos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'70', 'nombre'=>'Indemnizacion Por Danos a Terceros');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'95', 'nombre'=>'Otros C&G Diversos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'95', 'codigo'=>'99', 'nombre'=>'Ajustes Por Inflacion');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'99', 'codigo'=>'10', 'nombre'=>'Deudores');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'99', 'codigo'=>'15', 'nombre'=>'Propiedades Planta y Equipo');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'99', 'codigo'=>'95', 'nombre'=>'Otros Activos');
	$arrayFGS[] = array('codigo_F'=> 90, 'codigo_FG'=>'99', 'codigo'=>'99', 'nombre'=>'Ajustes Por Inflacion');


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