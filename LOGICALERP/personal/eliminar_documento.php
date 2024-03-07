<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	//include("bd/functions_bd.php");

	$idEmpresa=$_SESSION['EMPRESA'];
	$id_host=$_SESSION['ID_HOST'];

	if(!isset($opcion)){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/empleados/'.$archivo)) {
			$url = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/empresa_'.$id_host.'/empleados/'.$archivo;
		}
		else{
			$url = '';
		}

		$file_empleado = $_SERVER['DOCUMENT_ROOT'].'/ARCHIVOS_PROPIOS/imagenes_empresas/empresa_'.$idEmpresa.'/documentos_empleados/'.$archivo;

		if (file_exists($url)) {
		    if (unlink($url)) {
		    	$SQL = "DELETE FROM empleados_documentos WHERE id = $id AND id_empleado = $id_empleado";
				$consul = mysql_query($SQL,$link);

				if($consul){
					echo 'true{.}'.$id;
				}else{
					echo 'false{.}';
				}
		    }
		    else{
				echo 'false{.}';
		    }
		}
		else{
			$SQL = "DELETE FROM empleados_documentos WHERE id = $id AND id_empleado = $id_empleado";
			$consul = mysql_query($SQL,$link);

			if($consul){
				echo 'true{.}'.$id;
			}else{
				echo 'false{.}';
			}
		}


	}else{
		echo
		'
			<div class="my_grilla_celdas2" style="float:left; min-width:580px; width:100%; border: 0px" >
				<div ondblclick="">
					<div class="my_grilla_columna" style="float:left; width:30px;">&nbsp;</div>
					<div class="my_grilla_celdas" style="float:left; width:200px;">&nbsp;</div>
					<div class="my_grilla_celdas" style="float:left; width:230px;">&nbsp;</div>
					<div class="my_grilla_celda" style="float:left; width:30px; ">&nbsp;</div>
					<div class="my_grilla_celda" style="float:left; width:30px; ">&nbsp;</div>
				</div>
			</div>
        ';
	}



?>

