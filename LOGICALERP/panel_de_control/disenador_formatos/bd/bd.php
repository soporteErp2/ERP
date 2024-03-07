<?php
	include('../../../../../inc/conectar.php');
	mysql_select_db("registro",$link);	
	include('../../../../../inc/define_variables.php');
	 
$accion = (isset($_POST['op']) ? $_POST['op'] : $_GET['op']);

switch ($accion) 
{
	case 'planes':
	{
		planes();
		break;
	}
	case 'formatos':
	{
		formatos();
		break;
	}
	case 'validar_existe':
	{
		validar_existe();
		break;
	}
	case 'guardar_datos':
	{
		guardar_datos();
		break;
	}
	case 'campos':
	{
		campos();
		break;
	}
	case 'obtener_id':
	{
		obtener_id();
		break;
	}
	case 'guardar_elemento':
	{
		guardar_elemento();
		break;
	}
	case 'obtener_datos_old':
	{
		obtener_datos_old();
		break;
	}
	case 'guardar_coordenadas':
	{
		guardar_coordenadas();
		break;
	}
	case 'guardar_tam_elemento':
	{
		guardar_tam_elemento();
		break;
	}
	case 'guardar_datos_presentes':
	{
		guardar_datos_presentes();
		break;
	}
	case 'Eliminar_elementos':
	{
		Eliminar_elementos();
		break;
	}
	case 'guardar_tam_formato':
	{
		guardar_tam_formato();
		break;
	}
	case 'obtener_datos_elem_presentes':
	{
		obtener_datos_elem_presentes();
		break;
	}
	case 'unir_elementos':
	{
		unir_elementos();
		break;
	}
	case 'Eliminar_formatos':
	{
		Eliminar_formatos();
		break;
	}
	break;
};

function planes()//**
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	
	include 'json.php';
	$json = new Services_JSON();
	mysql_query("SET character_set_results=\"utf8\"");
	$sql = "SELECT id, nombre FROM paquete WHERE id_congreso = '$id'";
	$result = mysql_query($sql);
	$rows = mysql_num_rows($result);
	while($rec = mysql_fetch_array($result))
	{
		$arr[] = $rec;
	};
	//echo mysql_errno().": ".mysql_error();
	if($rows>0)
	{
		//$data = json_encode($arr);
		$data = $json->encode($arr);
		$data = utf8_decode($data);
		echo '({"total":"' . $rows . '","datos":' . $data . '})';
	}
	else
	{
		echo '({"total":"0", "datos":""})';
	}
};

function formatos()//**
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	
	include 'json.php';
	$json = new Services_JSON();
	mysql_query("SET character_set_results=\"utf8\"");
	$sql = "SELECT id, tipo, nombre FROM formatos WHERE id_congreso = '$id'";
	$result = mysql_query($sql);
	$rows = mysql_num_rows($result);
	while($rec = mysql_fetch_array($result))
	{
		$arr[] = $rec;
	};
	//echo mysql_errno().": ".mysql_error();
	if($rows>0)
	{
		//$data = json_encode($arr);
		$data = $json->encode($arr);
		$data = utf8_decode($data);
		echo '({"total":"' . $rows . '","datos":' . $data . '})';
	}
	else
	{
		echo '({"total":"0", "datos":""})';
	}
};

function validar_existe()//**
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	
	$camp = (isset($_POST['camp']) ? $_POST['camp'] : $_GET['camp']);
	$sql = "SELECT id FROM formatos WHERE nombre = '$camp' AND id_congreso = '$id'";
	$result = mysql_query($sql);
	$rows = mysql_num_rows($result);
	if($rows == 0)
	{
		echo '0';
	}
	else
	{
		echo '1';
	}
	//echo mysql_errno().": ".mysql_error();
};

function guardar_datos()//**
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	
	$datos = (isset($_POST['datos']) ? $_POST['datos'] : $_GET['datos']);
	$datos = utf8_decode($datos);
	$result = mysql_query("SELECT * FROM `formatos`");
	$rows = mysql_num_rows($result);
	$rows = $rows +1;

	$campos = explode( "{", $datos );	
	$sql = "INSERT INTO formatos (id, id_congreso, tipo, nombre, plan) VALUES ('$rows', '$id', '$campos[1]', '$campos[0]', '$campos[4]')";

	mysql_query($sql);
	/*$result2 = mysql_query("SELECT * FROM `formatos_config`");
	$rows2= mysql_num_rows($result2);
	$rows2 = $rows2 +1;*/
	$sql = "INSERT INTO formatos_config (id_formato, campo, width, height) VALUES ('$rows', 'documento', '$campos[3]', '$campos[2]')";
	mysql_query($sql);
	//echo mysql_errno().": ".mysql_error();
	echo $rows;
};

function campos()//**
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	
	include 'json.php';
	$json = new Services_JSON();
	mysql_query("SET character_set_results=\"utf8\"");
	$sql = "SELECT campo, nombre_campo FROM datos_configuracion WHERE id_congreso = '$id'";
	$result = mysql_query($sql);
	$rows = mysql_num_rows($result);
	
	while($rec = mysql_fetch_array($result))
	{
		$arr[] = $rec;
	};
	
	if($rows>0)
	{
		$data = $json->encode($arr);
		$data = utf8_decode($data);
		echo '({"total":"' . $rows . '","datos":' . $data . '})';
	}
	else
	{
		echo '({"total":"0", "datos":""})';
	}
};

function obtener_id()//**
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	
	$camp = (isset($_POST['camp']) ? $_POST['camp'] : $_GET['camp']);
	$camp = utf8_decode($camp);
	$sql = "SELECT id FROM formatos WHERE nombre = '$camp' AND id_congreso = '$id'";
	$result = mysql_query($sql);
	$rec = mysql_fetch_array($result);
	echo $rec['id'];
	//echo mysql_errno().": ".mysql_error();
};

function guardar_elemento()//*
{
	$datos = (isset($_POST['datos']) ? $_POST['datos'] : $_GET['datos']);
	$datos = utf8_decode($datos);

	$campo = explode( "{", $datos );

	$result = mysql_query("SELECT * FROM `formatos_config`");
	if($campo[6] != 'true')
	{
		$campo[6] = 'false';
	}
	$sql = "INSERT INTO formatos_config ( id_formato, campo, width, height, `top`, `left`, `font-family`, `font-size`, ";
	$sql.= "`font-weight`, `text-aling`, codigo) VALUES ('$campo[0]', '$campo[1]', '178', '37', '122', '75', '$campo[2]', ";
	$sql.= "'$campo[3]', '$campo[4]', '$campo[5]', '$campo[6]')";
	mysql_query($sql);
	echo mysql_insert_id();
	//echo mysql_errno().": ".mysql_error();
};

function obtener_datos_old()//*
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	mysql_query("SET character_set_results=\"utf8\"");
	$sql = "SELECT formatos.nombre, formatos.tipo, formatos_config.width, formatos_config.height, formatos.plan ";
	$sql.= "FROM formatos, formatos_config WHERE formatos.id = '$id' AND formatos_config.id_formato = '$id' AND ";
	$sql.= "formatos_config.campo = 'documento'";
	$result = mysql_query($sql);
	while($rec = mysql_fetch_array($result))
	{
		$arr[] = $rec;
	};
	//print_r($rec);
	//$arr = utf8_decode($arr);
	echo $arr[0]['nombre'].'{'.$arr[0]['tipo'].'{'.$arr[0]['width'].'{'.$arr[0]['height'].'{'.$arr[0]['plan'];
};

function guardar_coordenadas()//*
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	$posx = (isset($_POST['posx']) ? $_POST['posx'] : $_GET['posx']);
	$posy = (isset($_POST['posy']) ? $_POST['posy'] : $_GET['posy']);
	mysql_query("UPDATE `registro`.`formatos_config` SET `top` = '$posy ', `left` = '$posx' WHERE `formatos_config`.`id` = '$id'");
	//echo mysql_errno().": ".mysql_error();
};

function guardar_tam_elemento()//*
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	$ancho = (isset($_POST['ancho']) ? $_POST['ancho'] : $_GET['ancho']);
	$alto = (isset($_POST['alto']) ? $_POST['alto'] : $_GET['alto']);
	mysql_query("UPDATE `registro`.`formatos_config` SET `width` = '$ancho ', `height` = '$alto' WHERE `formatos_config`.`id` = '$id' ");
	//echo mysql_errno().": ".mysql_error();
};

function guardar_datos_presentes()//*
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	$formato = (isset($_POST['formato']) ? $_POST['formato'] : $_GET['formato']);
	$plan = (isset($_POST['plan']) ? $_POST['plan'] : $_GET['plan']);
	$ancho = (isset($_POST['ancho']) ? $_POST['ancho'] : $_GET['ancho']);
	$alto = (isset($_POST['alto']) ? $_POST['alto'] : $_GET['alto']);
	$elementos = (isset($_POST['elementos']) ? $_POST['elementos'] : $_GET['elementos']);
	include 'json.php';
	$json = new Services_JSON();
	$data = $json->decode($elementos);
	for($i = 0; $i < count($data); $i++)
	{
		$id_elm = $data[$i]['0'];
		$anch = $data[$i]['2'];
		$alt  = $data[$i]['3'];
		$posy = $data[$i]['4'];
		$posx = $data[$i]['5'];
		$font = $data[$i]['6'];
		$size = $data[$i]['7'];
		$forma = $data[$i]['8'];
		$aling = $data[$i]['9'];
		$barcode = $data[$i]['10'];
		if($barcode != 'true')
		{
			$barcode = 'false';
		}
		$sql = "UPDATE `registro`.`formatos_config` SET width = '$anch', height = '$alt', top = '$posy', `left`='$posx', ";
		$sql.= "`font-family` = '$font', `font-size` = '$size', `font-weight` = '$forma', `text-aling` = '$aling', `codigo` = '$barcode' ";
		$sql.= "WHERE id = '$id_elm'";
		//echo $sql;
		mysql_query($sql);
	}
	mysql_query("UPDATE `registro`.`formatos` SET `tipo` = '$formato', `plan` = '$plan' WHERE `formatos`.`id` = '$id'");
	$sql = "UPDATE `registro`.`formatos_config` SET `width` = '$ancho', `height` = '$alto' WHERE id_formato = '$id' AND campo = 'documento'";
	mysql_query($sql);
	echo mysql_errno().": ".mysql_error();
};

function Eliminar_elementos()//*
{
	$orden = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	$sql = "DELETE FROM formatos_config WHERE id = $orden";
	$result = mysql_query($sql);
	echo mysql_errno().": ".mysql_error();
};

function guardar_tam_formato()//*
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	$ancho = (isset($_POST['ancho']) ? $_POST['ancho'] : $_GET['ancho']);
	$alto = (isset($_POST['alto']) ? $_POST['alto'] : $_GET['alto']);
	mysql_query("UPDATE `registro`.`formatos_config` SET `width` = '$ancho ', `height` = '$alto' WHERE `formatos_config`.`id_formato` = '$id' AND `formatos_config`.`campo` = 'documento'");
	echo mysql_errno().": ".mysql_error();
};

function obtener_datos_elem_presentes()//*
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	$idc = (isset($_POST['idc']) ? $_POST['idc'] : $_GET['idc']);
	$array = '';
	
	$sql2 = "SELECT formatos_config.id, formatos_config.campo FROM formatos_config WHERE formatos_config.id_formato = '$id' AND formatos_config.campo != 'documento'";
	$result2 = mysql_query($sql2);
	$rows2 = mysql_num_rows($result2);
	
	while($rec2 = mysql_fetch_array($result2))
	{
		$arr2[] = $rec2;
	};
	
	for($j = 0; $j < $rows2; $j++)
	{
		$text = $arr2[$j]['campo'];
		
		if(substr_count($text, ',') > 0)
		{

			$campos = explode(",",$text);
			
			for($h=0; $h < count($campos); $h++)
			{
				$sql4 = "SELECT nombre_campo FROM datos_configuracion WHERE campo = '$campos[$h]' AND id_congreso = '$idc'";
				$result4 = mysql_query($sql4);
				$rec4 = mysql_fetch_array($result4);
				if($h == 0)
				{
					$var = $rec4['nombre_campo'];
				}
				else
				{
					$var = $var.'<>'.$rec4['nombre_campo'];
				}
			}
			
			$id_el = $arr2[$j]['id'];
			$sql5 = "SELECT id, width, height, top, `left`, `font-family`, `font-size`, `font-weight`, ";
			$sql5.= "`text-aling`, codigo FROM formatos_config WHERE id = '$id_el'";
			$result5 = mysql_query($sql5);
			$rec5 = mysql_fetch_array($result5);
			
			if($array == '')
			{
				$array = $rec5['id'].','.$var.','.$rec5['width'].','.$rec5['height'].',';
				$array.= $rec5['top'].','.$rec5['left'].','.$rec5['font-family'].','.$rec5['font-size'];
				$array.= ','.$rec5['font-weight'].','.$rec5['text-aling'].','.$rec5['codigo'];
			}
			else
			{
				$array = $array.';'.$rec5['id'].','.$var.','.$rec5['width'].','.$rec5['height'].',';
				$array.= $rec5['top'].','.$rec5['left'].','.$rec5['font-family'].','.$rec5['font-size'];
				$array.= ','.$rec5['font-weight'].','.$rec5['text-aling'].','.$rec5['codigo'];
			}
		}
	}
	
	$sql = "SELECT formatos_config.id, datos_configuracion.nombre_campo AS campo, formatos_config.width, formatos_config.height, formatos_config.top, ";
	$sql.= "formatos_config.`left`, formatos_config.`font-family`, formatos_config.`font-size`, formatos_config.`font-weight`, ";
	$sql.= "formatos_config.`text-aling`, formatos_config.codigo FROM formatos_config, datos_configuracion WHERE formatos_config.id_formato = '$id' AND datos_configuracion.id_congreso = '$idc' ";
	$sql.= "AND formatos_config.campo = datos_configuracion.campo GROUP BY formatos_config.id ";
	
	$result = mysql_query($sql);
	$rows = mysql_num_rows($result);
	
	while($rec = mysql_fetch_array($result))
	{
		$arr[] = $rec;
	};
	
	for($i = 0; $i < $rows; $i++)
	{
		if($array == '')
		{
			//if($i == 0)
			//{
				$array = $arr[$i]['id'].','.$arr[$i]['campo'].','.$arr[$i]['width'].','.$arr[$i]['height'].',';
				$array.= $arr[$i]['top'].','.$arr[$i]['left'].','.$arr[$i]['font-family'].','.$arr[$i]['font-size'];
				$array.= ','.$arr[$i]['font-weight'].','.$arr[$i]['text-aling'].','.$arr[$i]['codigo'];
			//}
		}
		else
		{
			$array = $array.';'.$arr[$i]['id'].','.$arr[$i]['campo'].','.$arr[$i]['width'].','.$arr[$i]['height'];
			$array.= ','.$arr[$i]['top'].','.$arr[$i]['left'].','.$arr[$i]['font-family'].','.$arr[$i]['font-size'];
			$array.= ','.$arr[$i]['font-weight'].','.$arr[$i]['text-aling'].','.$arr[$i]['codigo'];
		}
	}
	
	echo $array;
};

function unir_elementos()//*
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	$nombre = (isset($_POST['nombre']) ? $_POST['nombre'] : $_GET['nombre']);
	$id_congreso = (isset($_POST['id_congreso']) ? $_POST['id_congreso'] : $_GET['id_congreso']);

	$campo = explode( ",", $nombre );
	$sql = "SELECT campo FROM datos_configuracion WHERE nombre_campo = '$campo[0]' AND id_congreso = '$id_congreso'";

	echo $sql.'<br />';
	$result = mysql_query($sql);
	$rec = mysql_result($result,0,'campo');//mysql_fetch_array($result);
	
	$sql2 = "SELECT campo FROM datos_configuracion WHERE nombre_campo = '$campo[1]' AND id_congreso = '$id_congreso'";
	echo $sql2.'<br />';
	$result2 = mysql_query($sql2);
	$rec2 = mysql_result($result2,0,'campo');//mysql_fetch_array($result2);
	$varnew = $rec.','.$rec2;
	
	$sql3 = "UPDATE `registro`.`formatos_config` SET campo = '$varnew' WHERE id = '$id'";
	echo $sql3.'<br />';
	mysql_query($sql3);
	//echo mysql_errno().": ".mysql_error();
};

function Eliminar_formatos()//*
{
	$id = (isset($_POST['id']) ? $_POST['id'] : $_GET['id']);
	mysql_query("DELETE FROM formatos WHERE id = $id");
	mysql_query("DELETE FROM formatos_config WHERE id_formato = $id");
	echo mysql_errno().": ".mysql_error();
};

?>