<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $observacion = str_replace("\n", "<br>", $observacion);
	if(!isset($fechai)){$fechai = $fechaf;}
	if(!isset($horai)){$horai = $horaf;}

	if($opcion == 'insert'){

		if($id_objetivo_crm == ""){
			$ElCampObjet = "";
			$LaVarObje = "";

		}else{
			$ElCampObjet = "id_objetivo_crm,";
			$LaVarObje = "'".$id_objetivo_crm."',";

		}
    	$mysql->query("INSERT INTO calendario
    								(
    									id_empleado,
    									empleado,
    									tipo,
    									fechai,
    									horai,
										fechaf,
										horaf,
    									tema,
    									descripcion,
										color,
										$ElCampObjet
										id_actividad_crm
    								)
    								VALUES
    								(
    									'$id_empleado',
    									'$empleado',
    									'$tipo',
    									'$fechai',
    									'$horai',
										'$fechaf',
										'$horaf',
    									'$tema',
    									'$descripcion',
										'$color',
										$LaVarObje
										'$id_actividad_crm'
    								)",$link);

        $id = $mysql->insert_id();

        echo $id.'{.}';
    }

    if($opcion == 'update'){

    	$mysql->query("UPDATE calendario SET
								id_empleado = '$id_empleado',
								empleado = '$empleado',
								tipo = '$tipo',
								fechai = '$fechai',
								horai = '$horai',
								fechaf = '$fechaf' ,
								horaf = '$horaf' ,
								tema =  '$tema',
								descripcion = '$descripcion' ,
								color =  '$color'
						WHERE id = $id",$link);
    }

    if($opcion == 'delete'){		
		$mysql->query("DELETE FROM calendario WHERE id = $id",$link);
        $mysql->query("DELETE FROM calendario_personas WHERE id_calendario = $id",$link);
        $mysql->query("DELETE FROM calendario_notificaciones WHERE id_calendario = $id",$link); 
        $mysql->query("DELETE FROM calendario_notificaciones_personas WHERE id_calendario = $id",$link);

        if($checkCRM == 'true'){
            $mysql->query("UPDATE crm_objetivos_actividades SET activo = 0 WHERE id = $id_actividad",$link);//eliminar actividad
            $mysql->query("DELETE FROM crm_objetivos_actividades_personas WHERE id_actividad = $id_actividad",$link);//eliminar funcionarios
        }
    }

    if($opcion == 'ventanaEliminarActividad'){
        echo '<div id = "renderEliminarAct" style="display:none"></div>
              <div style="width:100%">
                  <div style="text-align:center;font-size:14px;padding-top:10px">                        
                       Desea eliminar la actividad seleccionada?
                  </div>
                  <div style="text-align:left;padding-left:20px;padding-top:10px">                        
                       <input type="checkbox" id="checkEliminarCRM" style="height:13px">Eliminar informacion vinculada al CRM
                  </div>
                  <div style="text-align:center;padding-top:15px">
                       <input name="btn_trc" type="button" onClick="eliminar_actividad_calendario()" style="width:100px;height:25px;font-weight:bold;font-size:13px" value="Aceptar">
                  </div>                  
              </div>';
    }
   
?>