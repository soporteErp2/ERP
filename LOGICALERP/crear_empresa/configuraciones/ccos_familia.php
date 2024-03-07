<?php
	//============================== INSERTAR FAMILIAS ITEMS =========================//

	//INSERTAR FAMILIAS ITEMS
	$sqlItemsFamilia = "INSERT INTO items_familia (nombre,id_empresa)
						VALUES ('Alimentos', $id_empresa),('Aseo',$id_empresa)";
	$queryItemsFamilia=mysql_query($sqlItemsFamilia,$link);
	if (!$queryItemsFamilia) { $error.=$sqlItemsFamilia."NO SE INSERTO lA CONFIGURACION DE LAS FAMILIAS ITEMS<br/>"; }

	$selectItemsFamilia = "SELECT id,nombre FROM items_familia WHERE id_empresa='$id_empresa'";
	$queryItemsFamilia  = mysql_query($selectItemsFamilia,$link);
	while ($row = mysql_fetch_array($queryItemsFamilia)) { $arrayFamilia[$row['nombre']] = $row['id']; }

	//INSERTAR GRUPOS ITEMS
	$sqlItemsGrupo = "INSERT INTO items_familia_grupo (id_familia,nombre,id_empresa)
						VALUES (".$arrayFamilia['Alimentos'].",'Bebidas', $id_empresa),
								(".$arrayFamilia['Alimentos'].",'Enlatados', $id_empresa),
								(".$arrayFamilia['Alimentos'].",'Fruver', $id_empresa),
								(".$arrayFamilia['Alimentos'].",'Lacteos', $id_empresa),
								(".$arrayFamilia['Aseo'].",'Articulos de Aseo', $id_empresa),
								(".$arrayFamilia['Aseo'].",'Aseo Personal', $id_empresa)";
	$queryItemsGrupo=mysql_query($sqlItemsGrupo,$link);
	if (!$queryItemsGrupo) { $error.="NO SE INSERTO lA CONFIGURACION DE LOS GRUPOS ITEMS<br/>"; }

	$selectItemsGrupo = "SELECT id,nombre,familia FROM items_familia_grupo WHERE id_empresa='$id_empresa'";
	$queryItemsGrupo  = mysql_query($selectItemsGrupo,$link);
	while ($row = mysql_fetch_array($queryItemsGrupo)) { $arrayGrupo[$row['familia']][$row['nombre']] = $row['id']; }

	//INSERTAR SUBGRUPOS ITEMS
	$sqlItemsSubgrupo = "INSERT INTO items_familia_grupo_subgrupo (id_familia,id_grupo,nombre,id_empresa)
						VALUES 	(".$arrayFamilia['Alimentos'].",".$arrayGrupo['Alimentos']['Bebidas'].",'Bebidas', $id_empresa),
								(".$arrayFamilia['Alimentos'].",".$arrayGrupo['Alimentos']['Enlatados'].",'Enlatados', $id_empresa),
								(".$arrayFamilia['Alimentos'].",".$arrayGrupo['Alimentos']['Fruver'].",'Fruver', $id_empresa),
								(".$arrayFamilia['Alimentos'].",".$arrayGrupo['Alimentos']['Lacteos'].",'Lacteos', $id_empresa),
								(".$arrayFamilia['Aseo'].",".$arrayGrupo['Aseo']['Articulos de Aseo'].",'Articulos de Aseo', $id_empresa),
								(".$arrayFamilia['Aseo'].",".$arrayGrupo['Aseo']['Aseo Personal'].",'Aseo Personal', $id_empresa)";

	$queryItemsSubgrupo=mysql_query($sqlItemsSubgrupo,$link);
	if (!$queryItemsSubgrupo) { $error .= "<br>NO SE INSERTO lA CONFIGURACION DE LOS SUBGRUPOS ITEMS<br/>"; }

	//CENTROS DE COSTOS
 	$sqlCentroCostos   = "INSERT INTO centro_costos (codigo,nombre,id_empresa) VALUES ('01','Ventas',$id_empresa)";
	$queryCentroCostos = mysql_query($sqlCentroCostos,$link);
	if (!$queryCentroCostos) { $error .= "<br>NO SE INSERTO EL CENTRO COSTOS POR DEFECTO<br/>"; }


?>