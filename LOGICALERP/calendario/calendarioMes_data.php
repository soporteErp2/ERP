<?php

    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

	function hora_corta($date){
		$LaList = explode(":",$date);
		$h = $LaList[0]; $m = $LaList[1]; $s = $LaList[2];//list($h,$m,$s) = split(":",$date);
		return $h.':'.$m;
	}

	//SE HACE LEFT JOIN CON CALENDARIO PERSONAS PARA QUE A LOS FUNCIONARIOS ADICIONALES LES SALGA LA ACTIVIDAD EN EL CALENDARIO
    $SQL = "SELECT
				C.fechai,
				C.horai,
				C.fechaf,
				C.horaf,
				C.tipo,
				C.id,
				C.empleado,
				C.id_empleado,
				P.id_asignado,
				P.asignado,
				C.tema,
				C.color,
				C.icono,
				A.estado
			FROM
				calendario AS C
			LEFT JOIN crm_objetivos_actividades AS A ON(C.id_actividad_crm = A.id)
			LEFT JOIN calendario_personas AS P ON(C.id = P.id_calendario)
			WHERE
				((C.fechai BETWEEN '$fechai' AND '$fechaf')
				OR(C.fechaf BETWEEN '$fechai' AND '$fechaf'))
				AND (C.id_empleado = $id_empleado OR P.id_asignado = $id_empleado)
			GROUP BY C.id";


	//echo $SQL;
    $consul = $GLOBALS['mysql']->query($SQL,$link);

    $cual = 0;

    $permiso_editar_calendario = (user_permisos(195,'false') == 'true')? 'true' : 'false';

    echo '<script>
            var myarray = new Array();
            var usuario_calendario = '.$id_empleado.';
            var usuario_login      = '.$_SESSION["IDUSUARIO"].';
            var permiso_edicion_calendario = "'.$permiso_editar_calendario.'";
            //console.log(usuario_calendario+" "+usuario_login);
         ';

    while($row=$GLOBALS['mysql']->fetch_array($consul)){
    	//CON ESTO PONGO LOS IDS Y EL NOMBRE DEL EMPLEADO O EL ASIGNADO CON EL QUE COINCIDA EL ID QUE LLEGA
    	if($row['id_asignado'] == $id_empleado){
			$row['id_empleado'] = $row['id_asignado'];
			$row['empleado']    = $row['asignado'];
    	}

    	//echo 'console.log("'.$row['id_empleado'].'");';

        echo   '
					myarray['.$cual.'] = new Array();
                    myarray['.$cual.']["fechai"]		= "'.$row['fechai'].'";
                    myarray['.$cual.']["horai"]			= "'.hora_corta($row['horai']).'";
                    myarray['.$cual.']["fechaf"]		= "'.$row['fechaf'].'";
                    myarray['.$cual.']["horaf"]			= "'.hora_corta($row['horaf']).'";
                    myarray['.$cual.']["tipo"]			= "'.$row['tipo'].'";
					myarray['.$cual.']["id"]			= "'.$row['id'].'";
                    myarray['.$cual.']["empleado"]		= "'.$row['empleado'].'";
					myarray['.$cual.']["id_empleado"]	= "'.$row['id_empleado'].'";
					myarray['.$cual.']["tema"]			= "'.$row['tema'].'";
					myarray['.$cual.']["color"]			= "'.$row['color'].'";
					myarray['.$cual.']["icono"]			= "'.$row['icono'].'";
					myarray['.$cual.']["estado"]	    = "'.$row['estado'].'";
               ';
        $cual++;
    }
    echo '</script>';

?>

<script>

    for(i=0;i<myarray.length;i++){

    	var estado = myarray[i]['estado'];
    	var border = '';
    	if(estado == 1){
    		border = 'border:2px solid black;width: calc(100% - 30px) !important';
    		//myarray[i]['color'] = 'green';
    	}

    	if(usuario_login == usuario_calendario || permiso_edicion_calendario == 'true'){
    		cursor = '';
    	}
    	else{
    		cursor = 'cursor:default';
    	}

		var horai = horasAminutos(myarray[i]['horai']);
		horai = (horai * 100) / 1440;
		var horaf = horasAminutos(myarray[i]['horaf']);
		horaf = ((horaf * 100) / 1440) - horai;
		////////////////////////////////////////////////

		var NoDias = DiferenciaFechas(myarray[i]['fechaf'],myarray[i]['fechai']);

		if(myarray[i]['fechai'] == myarray[i]['fechaf']){ //SI EL EVENTO ES DE UN SOLO DIA

			var CapaContenedora = document.getElementById('Capa_'+myarray[i]['fechai']);
			var d = document.createElement("div");
			d.setAttribute('class','evento Tipo_'+myarray[i]['icono']);
			d.setAttribute('style','background-color:'+myarray[i]['color']+';'+border+';'+cursor);
			if(usuario_login == usuario_calendario || permiso_edicion_calendario == 'true'){
				d.setAttribute('onclick','EditaRegistro('+myarray[i]['id']+',\''+myarray[i]['fechai']+'\');event.stopPropagation();');
			}
			d.setAttribute('id', 'Capa_'+myarray[i]['id']+'_'+myarray[i]['fechai']);
			CapaContenedora.appendChild(d);
			document.getElementById('Capa_'+myarray[i]['id']+'_'+myarray[i]['fechai']).innerHTML = myarray[i]['horai']+" "+myarray[i]['tema'];
			new Ext.ToolTip({
				target		: 'Capa_'+myarray[i]['id']+'_'+myarray[i]['fechai'],
				anchor		: "left",
				dismissDelay: 60000,
				trackMouse	: true,
				minWidth	: 250,
				html		: '<b>'+myarray[i]['tema']+'</b><br />Inicio&nbsp; : &nbsp;'+fecha_larga(myarray[i]['fechai'])+' '+myarray[i]['horai']+'<br />Fin:&nbsp;&nbsp;&nbsp; : &nbsp;'+fecha_larga(myarray[i]['fechaf'])+' '+myarray[i]['horaf'],

			});

		}else{// SI LA FECHA DE INICIO ES DIFERENTE A LA FECHA FINAL OSEA EVENTOS DE VARIOS DIAS

			///////////PRIMER DIA////////////
			if(document.getElementById('Capa_'+myarray[i]['fechai'])){
			   var horai = horasAminutos(myarray[i]['horai']);
			   horai = (horai * 100) / 1440;
			   horaf = 100;
			   ////////////////////////////////////////////////
				var CapaContenedora = document.getElementById('Capa_'+myarray[i]['fechai']);
				var d = document.createElement("div");
				d.setAttribute('class','evento Tipo_'+myarray[i]['icono']);
				d.setAttribute('style','background-color:'+myarray[i]['color']+';'+border+';'+cursor);
				if(usuario_login == usuario_calendario || permiso_edicion_calendario == 'true'){
					d.setAttribute('onclick','EditaRegistro('+myarray[i]['id']+',\''+myarray[i]['fechai']+'\');event.stopPropagation();');
				}
				d.setAttribute('id', 'Capa_'+myarray[i]['id']+'_'+myarray[i]['fechai']);
				CapaContenedora.appendChild(d);
				document.getElementById('Capa_'+myarray[i]['id']+'_'+myarray[i]['fechai']).innerHTML = myarray[i]['horai']+" "+myarray[i]['tema'];
				new Ext.ToolTip({
					target		: 'Capa_'+myarray[i]['id']+'_'+myarray[i]['fechai'],
					anchor		: "left",
					dismissDelay: 60000,
					trackMouse	: true,
					minWidth	: 250,
					html		: '<b>'+myarray[i]['tema']+'</b><br />Inicio&nbsp; : &nbsp;'+fecha_larga(myarray[i]['fechai'])+' '+myarray[i]['horai']+'<br />Fin:&nbsp;&nbsp;&nbsp; : &nbsp;'+fecha_larga(myarray[i]['fechaf'])+' '+myarray[i]['horaf'],

				});
			}

			///////////DIAS INTERMEDIOS//////
			for(a=1;a<NoDias;a++){
					var ElDia = SumarDias(myarray[i]['fechai'],a);
					if(document.getElementById('Capa_'+ElDia)){
						horai = 0;
						horaf = 99;
					   ////////////////////////////////////////////////
						var CapaContenedora = document.getElementById('Capa_'+ElDia);
						var d = document.createElement("div");
						d.setAttribute('class','evento Tipo_'+myarray[i]['icono']);
						d.setAttribute('style','background-color:'+myarray[i]['color']+';'+border+';'+cursor);
						//d.setAttribute('style','width:'+horaf+'%; margin-left:'+horai+'%;');
						if(usuario_login == usuario_calendario || permiso_edicion_calendario == 'true'){
							d.setAttribute('onclick','EditaRegistro('+myarray[i]['id']+',\''+ElDia+'\');event.stopPropagation();');
						}
						d.setAttribute('id', 'Capa_'+myarray[i]['id']+'_'+ElDia);
						CapaContenedora.appendChild(d);
						document.getElementById('Capa_'+myarray[i]['id']+'_'+ElDia).innerHTML = myarray[i]['tema'];
						new Ext.ToolTip({
							target		: 'Capa_'+myarray[i]['id']+'_'+ElDia,
							anchor		: "left",
							dismissDelay: 60000,
							trackMouse	: true,
							minWidth	: 250,
							html		: '<b>'+myarray[i]['tema']+'</b><br />Inicio&nbsp; : &nbsp;'+fecha_larga(myarray[i]['fechai'])+' '+myarray[i]['horai']+'<br />Fin:&nbsp;&nbsp;&nbsp; : &nbsp;'+fecha_larga(myarray[i]['fechaf'])+' '+myarray[i]['horaf'],
						});
					}
			}

			////////ULTIMO DIA//////////////
			if(document.getElementById('Capa_'+myarray[i]['fechaf'])){
				horai = 0;
				var horaf = horasAminutos(myarray[i]['horaf']);
				horaf = ((horaf * 100) / 1440) - horai;
				////////////////////////////////////////////////
				var CapaContenedora = document.getElementById('Capa_'+myarray[i]['fechaf']);
				var d = document.createElement("div");
				d.setAttribute('class','evento Tipo_'+myarray[i]['icono']);
				d.setAttribute('style','background-color:'+myarray[i]['color']+';'+border+';'+cursor);
				if(usuario_login == usuario_calendario || permiso_edicion_calendario == 'true'){
					d.setAttribute('onclick','EditaRegistro('+myarray[i]['id']+',\''+myarray[i]['fechaf']+'\');event.stopPropagation();');
				}
				d.setAttribute('id','Capa_'+myarray[i]['id']+'_'+myarray[i]['fechaf']);
				CapaContenedora.appendChild(d);
				document.getElementById('Capa_'+myarray[i]['id']+'_'+myarray[i]['fechaf']).innerHTML = myarray[i]['tema'];
				new Ext.ToolTip({
					target		: 'Capa_'+myarray[i]['id']+'_'+myarray[i]['fechaf'],
					anchor		: "left",
					dismissDelay: 60000,
					trackMouse	: true,
					minWidth	: 250,
					html		: '<b>'+myarray[i]['tema']+'</b><br />Inicio&nbsp; : &nbsp;'+fecha_larga(myarray[i]['fechai'])+' '+myarray[i]['horai']+'<br />Fin:&nbsp;&nbsp;&nbsp; : &nbsp;'+fecha_larga(myarray[i]['fechaf'])+' '+myarray[i]['horaf'],
				});
			}
		}
    }

</script>
