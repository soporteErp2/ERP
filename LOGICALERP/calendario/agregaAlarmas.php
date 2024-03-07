<?php
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

	if($opcion == 'insert'){

		//ESTO BORRA LAS ALARMAS QUE PERTENECEN AL CALENDARIO BORRADO CUANDO SE actualiza en agregaRegistroCALENDARIO
		$mysql->query("DELETE FROM calendario_notificaciones WHERE id_calendario = $id_calendarioOLD",$link);

		$mysql->query("DELETE FROM calendario_notificaciones_personas WHERE id_calendario = $id_calendarioOLD",$link);

		$mysql->query("DELETE FROM calendario_notificaciones WHERE id_calendario = $id_calendario",$link);
		$alarmas =  explode('{.}',trim($datos));

		$funcionarios = (isset($personas))? explode('{.}',$personas) : '';

		//echo $fechai.' '.$horai.'<br /><br />';

		for($i=0;$i<count($alarmas);$i++){
			//echo $alarmas[$i].'<br />';
			$detalle = split(',',$alarmas[$i]);
			if($detalle[1]=='M'){$T='minute';}
			if($detalle[1]=='H'){$T='hour';}
			if($detalle[1]=='D'){$T='day';}

			$nuevafecha = strtotime ( '-'.$detalle[0].' '.$T , strtotime ( $fechaf.' '.$horaf ) ) ;
			$nuevafecha = date ( 'Y-m-d H:i:s' , $nuevafecha );
			$eldato = explode(' ',$nuevafecha);

			//EL ASIGNADO PRINCIPAL

			$mysql->query("INSERT INTO calendario_notificaciones
											(
												id_calendario,
												fecha,
												hora,
												fecha_hora,
												time,
												time_type,
												id_empleado
											)
											VALUES
											(
							  					'$id_calendario',
							  					'$eldato[0]',
							  					'$eldato[1]',
							  					'$nuevafecha',
							  					'$detalle[0]',
							  					'$detalle[1]',
							  					'$id_empleado'
							  				)",$link);

			$id = $mysql->insert_id();

			$stringInsert = '';

			//LOS ASIGNADOS ADICIONALES TAMBIEN RECIBIRAN LA ALARMA
			if(isset($personas) && $personas != 'false'){

				for($j=0;$j<count($funcionarios);$j++){
					$stringInsert .= "(
									  	 '$id',
									  	 '$id_calendario',
									  	 '$funcionarios[$j]'
									  ),";
				}


				$stringInsert    = substr($stringInsert, 0, -1);

				$mysql->query("INSERT INTO calendario_notificaciones_personas
												(
													id_notificacion,
													id_calendario,
													id_asignado
												)
												VALUES
												$stringInsert",$link);

			}
		}
    }



   if($opcion == 'update'){

   }

    //echo $id.'{.}';
?>