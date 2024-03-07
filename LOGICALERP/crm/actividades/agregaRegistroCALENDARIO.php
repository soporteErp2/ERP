<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $observacion = str_replace("\n", "<br>", $observacion);
	if(!isset($fechai)){$fechai = $fechaf;}
	if(!isset($horai)){$horai = $horaf;}

	if($opcion == 'insert'){
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
										id_objetivo_crm,
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
										'$id_objetivo_crm',
										'$id_actividad_crm'
    								)",$link);
        $id = $mysql->insert_id();
		echo $id.'{.}';

        //SE AGREGAN LOS EMPLEADOS ADICIONALES AL CALENDARIO CREADO
        if(isset($personas) && $personas != 'false'){
            $funcionarios = explode('{.}',$personas);
            for($i=0;$i<count($funcionarios);$i++){
                $mysql->query("INSERT INTO calendario_personas
                                                    (
                                                        id_calendario,
                                                        id_asignado
                                                    )VALUES(
                                                        $id,
                                                        $funcionarios[$i]
                                                    )",$link);

            }
        }
    }

   	if($opcion == 'update'){
		$mysql->query("DELETE FROM calendario WHERE id = $id_calendario",$link);
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
										id_objetivo_crm,
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
										'$id_objetivo_crm',
										'$id_actividad_crm'
    								)",$link);
        $id = $mysql->insert_id();
		echo $id.'{.}';

		/*$mysql->query("UPDATE calendario SET
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
					WHERE id = $id_calendario",$link);*/

        $mysql->query("DELETE FROM calendario_personas WHERE id_calendario = $id_calendario",$link);

        //SE AGREGAN LOS EMPLEADOS ADICIONALES AL CALENDARIO CREADO
        if(isset($personas) && $personas != 'false'){
            $funcionarios = explode('{.}',$personas);
            for($i=0;$i<count($funcionarios);$i++){
                $mysql->query("INSERT INTO calendario_personas
                                                    (
                                                        id_calendario,
                                                        id_asignado
                                                    )VALUES(
                                                        $id,
                                                        $funcionarios[$i]
                                                    )",$link);

            }
        }
   	}

   	if($opcion == 'delete'){
		if($id_calendario != 0){
			$mysql->query("DELETE FROM calendario WHERE id = $id_calendario",$link);
            $mysql->query("DELETE FROM calendario_personas WHERE id_calendario = $id_calendario",$link);
		}
   	}




?>