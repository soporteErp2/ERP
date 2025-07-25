<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

		$consul = $mysql->query("SELECT * FROM crm_objetivos_actividades WHERE id = $id",$link);
		$consul2 = $mysql->query("SELECT * FROM calendario WHERE id_actividad_crm = $id",$link);
		$consul3 = $mysql->query("SELECT * FROM crm_objetivos_actividades_personas WHERE id_actividad = $id",$link);


		$estado				= $mysql->result($consul,0,'estado');
		$tipo 				= $mysql->result($consul,0,'tipo');
		$tema 				= $mysql->result($consul,0,'tema');
		$fechai 			= $mysql->result($consul,0,'fechai');
		$horai 				= $mysql->result($consul,0,'horai');
		$fechaf 			= $mysql->result($consul,0,'fechaf');
		$horaf 				= $mysql->result($consul,0,'horaf');
		$id_objetivo  		= $mysql->result($consul,0,'id_objetivo');
		$id_cliente  		= $mysql->result($consul,0,'id_cliente');
		$id_asignado 		= $mysql->result($consul,0,'id_asignado');
		$asignado 			= $mysql->result($consul,0,'asignado');
		//$id_actividad 		= $id;
		$splitHorai = explode(':',$horai);
		$splitHoraf = explode(':',$horaf);
		$descripcion  		= str_replace("<br>", "\\n", $mysql->result($consul,0,'observacion'));

		echo '<script>var id_objetivo = '.$id_objetivo.';</script>';
		echo '<script>var id_cliente = '.$id_cliente.';</script>';


		/*if($mysql->num_rows($consul3)>0){;
			$sum = 0;
			$tot = $mysql->num_rows($consul3);
			echo '<script> var Funcionarios = new Array(';
				while($rowPer = $mysql->fetch_array($consul3)){

					echo ' [["'.$rowPer['id_asignado'].'"],';
					echo ' ["'.$rowNoti['asignado'].'"]]';
					$sum++;
					if($sum<$tot){echo ',';}
				}
			echo '); </script>';
		}else{
			echo '<script> var Funcionarios = new Array(); </script>';
		}*/
		echo '<script> var TotalFun = '.$mysql->num_rows($consul3).' </script>';


		if($mysql->num_rows($consul2)>0){
			echo '<script>var id_calendario = '.$mysql->result($consul2,0,'id').';</script>';
			$CopiaCALENDARIO = 'true';
			$color = $mysql->result($consul2,0,'color');
			$id_calendario = $mysql->result($consul2,0,'id');
			//SCRIPT QUE TRAE LAS ALARMAS Y LAS CONVIERTE EN UN SCRIP JS
			$consulNoti = $mysql->query("SELECT * FROM calendario_notificaciones WHERE id_calendario = $id_calendario",$link);
			if($mysql->num_rows($consulNoti)>0){;
				$sum = 0;
				$tot = $mysql->num_rows($consulNoti);
				echo '<script> var Alarmas = new Array(';
					while($rowNoti = $mysql->fetch_array($consulNoti)){

						echo ' [["'.$rowNoti['time'].'"],';
						echo ' ["'.$rowNoti['time_type'].'"]]';
						$sum++;
						if($sum<$tot){echo ',';}
					}
				echo '); </script>';
			}else{
				echo '<script> var Alarmas = new Array(); </script>';
			}

		}else{
			echo '<script>var id_calendario = 0</script>';
			$CopiaCALENDARIO = 'false';
		}

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //$formatoFecha= $mysql->result($mysql->query("SELECT formato_hora FROM empresas WHERE id=$_SESSION[EMPRESA]",$link),0,"formato_hora");
	$formatoFecha = '24Hrs';
	if($formatoFecha == '24Hrs'){
		$formatTimeField='H:i';
		$hora  = date('H:i');
		echo '<script>var formatoHora = "24Hrs";</script>';
	} else {
		$formatTimeField='h:i A';
		$hora  = date('h:i A');
		echo '<script>var formatoHora = "AM/PM";</script>';
	}
	$fecha = date('Y-m-d');
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	//SCRIPT QUE TRAE LOS GRUPOS DE LAS OPCIONES DE ACTIVIDADES Y LAS CONVIENRTE EN UN ARRAY JS
	$consulActi = $mysql->query("SELECT id_departamento,departamento FROM crm_configuracion_actividades WHERE activo = 1 GROUP BY id_departamento",$link);
	$sum = 0;
	$tot = $mysql->num_rows($consulActi);
	echo '<script> var Grupos = new Array(';
		while($rowActi = $mysql->fetch_array($consulActi)){

			echo ' [["'.$rowActi['id_departamento'].'"],';
			echo ' ["'.$rowActi['departamento'].'"]]';
			$sum++;
			if($sum<$tot){echo ',';}
		}
	echo '); </script>';

	//SCRIPT QUE TRAE LAS OPCIONES DE ACTIVIDADES Y LAS CONVIENRTE EN UN ARRAY JS
	$consulActi = $mysql->query("SELECT * FROM crm_configuracion_actividades WHERE activo = 1",$link);
	$sum = 0;
	$tot = $mysql->num_rows($consulActi);
	echo '<script> var Opciones = new Array(';
		while($rowActi = $mysql->fetch_array($consulActi)){

			echo ' [["'.$rowActi['id'].'"],';
			echo ' ["'.$rowActi['nombre'].'"],';
			echo ' ["'.$rowActi['fecha_completa'].'"],';
			echo ' ["'.$rowActi['fecha_vencimiento'].'"],';
			echo ' ["'.$rowActi['copiar_crm_obligatorio'].'"],';
			echo ' ["'.$rowActi['id_departamento'].'"]]';
			$sum++;
			if($sum<$tot){echo ',';}
		}
	echo '); </script>';


?>
	<style>
		#ToolbarTareas{
			font-family		:   RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
			width			:	calc(100% - 40px);
			height			:	48px;
			background-color:	<?php echo $_SESSION["COLOR_MD_CALENDARIO"] ?>;
			margin			:	0 0 10px 0;
			padding			:	20px;
			color			:	#FFF;

		}
		.TituloGrupo{
			font-size		: 	18px;
			font-weight		:	normal;
			font-family		:	RobotoDraft, 'Helvetica Neue', Helvetica, Arial;
			color 			: 	#333;
			padding			:	10px 0 5px 0 ;
			margin-top		:	10px;
		}
	</style>

	<div id="ToolbarTareas" style="width:100%; height:70px; padding: 15px 10px 0 10px; overflow:hidden; box-sizing:border-box; display: flex; justify-content: space-between; align-items: center;">
    	<!-- Título de la actividad -->
    	<div style="font-size:20px; margin:0 0 0 0; color:#003366; font-weight:bold;">
    	    Programaci&oacute;n de Actividad
    	    <br>
    	    <span style="font-size:12px; font-weight:normal;"><?php echo fecha_larga($fecha)?></span>
    	</div>

    	<!-- Contenedor para los botones -->
    	<div style="display: flex; align-items: center; gap: 10px;">
		
    	    <!-- Botón Cerrar con SVG -->
    	    <div style="width:48px; height:48px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor:pointer;" onclick="Win_Agrega_Registro.close();">
    	        <div style="display: flex; justify-content: center; align-items: center; width:36px; height:36px;">
    	            <!-- Icono Cerrar -->
    	            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    	                <line x1="18" y1="6" x2="6" y2="18"/>
    	                <line x1="6" y1="6" x2="18" y2="18"/>
    	            </svg>
    	        </div>
    	        <div style="text-align:center; color:#dc3545; font-weight:bold; font-size:13px;">Cerrar</div>
    	    </div>

    	    <!-- Botón Guardar con SVG -->
    	    <div style="width:48px; height:48px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor:pointer;" onclick="GuardaActividad();">
    	        <div style="display: flex; justify-content: center; align-items: center; width:36px; height:36px;">
    	            <!-- Icono Guardar -->
    	            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    	                <polyline points="20 6 9 17 4 12"/>
    	            </svg>
    	        </div>
    	        <div style="text-align:center; color:#28a745; font-weight:bold; font-size:13px;">Guardar</div>
    	    </div>

    	</div>
    </div>

    <div id="ContenedorFormuActividad" style="width:100%; height:100%; overflow:hidden; overflow-y:auto">
        <!-- PROGRAMACION ############################################################### -->
        <div class='ActividadesReglon TituloGrupo'>Programaci&oacute;n</div>
        <div class='ActividadesReglon'>
            <div id="LabelColor" class='Actividadesfield'>Tipo de Actividad</div>
            <div class='ActividadesControl'>
                    <select id="Tipo"  name="Tipo" class="MyField" style="width:150px;" onchange="SelectTipo()"></select>
            </div>
        </div>
        <div class='ActividadesReglon'>
            <div class='Actividadesfield'>Tema</div>
            <div class='ActividadesControl'><input id="ActividadTema" type="text" class="MyFieldObligatorio" style="width:310px;" onBlur="ValidarFieldVacio(this)" placeholder=" Nombre &oacute; Tema de la Actividad" value="<?php echo $tema?>" ></div>
        </div>


        <div class='ActividadesReglon' style="width:550px;">
            <div class='Actividadesfield'>Asignado a</div>
            <div id="ContenedorPersonas" class='ActividadesControl' style="width:400px;">
            	<div >
                    <div style="width:315px; float:left;">
                        <input id="ActividadId_asignado" type="hidden" value="<?php echo $id_asignado ?>">
                        <input id="ActividadAsignado" type="text" class="MyField" style="width:310px; font-weight:bold" value="<?php echo $asignado ?>" onclick="BuscarFuncionario('ActividadId_asignado','ActividadAsignado')" onBlur="ValidarFieldVacio(this)" readonly placeholder=" Funcionario al cual se le asigna la Cita...">
                    </div>
                    <div style="width:80px; float:left; margin:-2px 0 0 0; font-size:9px">Funcionario Principal</div>
                </div>
                <?php $CountFunc = 0; while($rowPer = $mysql->fetch_array($consul3)){ ?>
                    <div>
                        <div style="width:315px; float:left; margin:2px 0 1px 0">
                            <input class="ClassIdAsignado" id="ActividadId_asignado<?php echo $CountFunc ?>" type="hidden" value="<?php echo $rowPer['id_asignado'] ?>">
                            <input class="MyField ClassAsignado" id="ActividadAsignado<?php echo $CountFunc ?>" type="text" style="width:310px;" value="<?php echo $rowPer['asignado'] ?>" onclick="BuscarFuncionario('ActividadId_asignado<?php echo $CountFunc ?>','ActividadAsignado<?php echo $CountFunc ?>')" onBlur="ValidarFieldVacio(this)" readonly placeholder=" Funcionario al cual se le asigna la Cita...">
                        </div>
                        <div class="delete" style="width:16px; height:16px; float:left; margin:0 5px 0 0" onclick="DeleteAlarma(this)"></div>
                    </div>
                <?php $CountFunc++; } ?>
            </div>
        </div>
        <div class='ActividadesReglon'>
            <div style="float:left; margin:2px 0 1px 0">
                <div class='Actividadesfield'>&nbsp;</div>
                <div id="DivAddPersona" class='ActividadesControl'>
                  <a onclick="AddPersona()" style="cursor:pointer">
                      <div class="add16" style="width:16px; height:16px; float:left; margin:0 5px 0 0"></div>
                      <div style="float:left; text-decoration:underline">Agregar Funcionario</div>
                  </a>
                </div>
            </div>
        </div>


        <div id="CapaCitas" class='ActividadesReglon' style="width:570px;">
            <div class='Actividadesfield'>Fecha</div>
            <div class='ActividadesControl' style="width:110px;"><input id="ActividadFechai" type="text" class="MyField" style="width:100px;" value="<?php echo $fechai?>" ></div>
            <div class='ActividadesControl' id="CapaFechai" style="width:60px;"><input id="ActividadLaHorai" type="text" class="MyField" style="width:50px;"></div>
            <div class='ActividadesControl' style="width:17px;">a</div>
            <div class='ActividadesControl' style="width:110px"><input id="ActividadFechaf" type="text" class="MyField" style="width:100px;" value="<?php echo $fechaf?>" ></div>
            <div class='ActividadesControl' id="CapaFechaf" style="width:60px;"><input id="ActividadLaHoraf" type="text" class="MyField" style="width:50px;"></div>
            <div style="float:left; width:100px;">
                <div class='ActividadesControl' style="width:20px"><input id="ActividadCheckAhora" type="checkbox" onChange="CheckAhora(this);" disabled="disabled"></div>
                <div class='ActividadesControl' style="width:80px; color:#ccc;">Todo el Dia</div>
            </div>
        </div>

        <div id="CapaTareas" class='ActividadesReglon' style="display:none">
            <div class='Actividadesfield'>Fecha Vencimiento</div>
            <div class='ActividadesControl' style="width:110px;"><input id="ActividadFechai2" type="text" class="MyField" style="width:100px;" ></div>
            <div class='ActividadesControl' id="CapaFechai" style="width:80px;"><input id="ActividadLaHorai2" type="text" class="MyField" style="width:50px;"  ></div>
            <div style="float:left; width:120px;">
                <div class='ActividadesControl' style="width:20px"><input id="ActividadCheckAhora2" type="checkbox" onChange="CheckAhora2(this);" disabled="disabled"></div>
                <div class='ActividadesControl' style="width:100px; color:#ccc;">En este momento</div>
            </div>
        </div>


        <div class='ActividadesReglon'>
            <div class='Actividadesfield'>Observaciones</div>
            <div class='ActividadesControl'><textarea id="ActividadObservacion" class="MyField" style="width:470px; height:50px;"></textarea></div>
        </div>


        <!-- ACCIONES ############################################################### -->
        <div class='ActividadesReglon TituloGrupo' style="width:570px; border-top:1px solid <?php echo $_SESSION['COLOR_LINEA']?>;">Acci&oacute;nes</div>
        <div id="ToolbarAcciones" style="float:left; width:100%; margin:10px 0 0 10px;">
            <?php if($estado==0){ ?>
                <a onclick="AgregarAccion();" style="cursor:pointer">
                    <div class="add16" style="width:16px; height:16px; float:left; margin:0 5px 0 0"></div>
                    <div style="float:left; text-decoration:underline">Agregar una Acci&oacute;n</div>
                </a>
            <?php } ?>
        </div>
        <div id="PanelAcciones" style="float:left; width:100%; padding:0 0 0 0; margin:0 0 0 0;"> </div>


        <!-- CALENDARIO ############################################################### -->
        <div class='ActividadesReglon TituloGrupo' style="width:570px; border-top:1px solid <?php echo $_SESSION['COLOR_LINEA']?>;">Calendario</div>
        <div class='ActividadesReglon' style="width:500px;">
            <!--<div class='Actividadesfield'><span id="labelCRM3">Calendario</span></div>-->
            <div class='ActividadesControl' style="width:20px"><input id="ActividadCopiarCRM" type="checkbox" onChange="CheckCALENDARIO(this);"></div>
            <div class='ActividadesControl' style="width:350px;"><span id="labelCRM2">Copiar esta actividad al Calendario</span></div>
        </div>
        <div id="CapaColor">
            <div class='ActividadesReglon'>
                <div id="LabelColor" class='Actividadesfield'>Color</div>
                <div class='ActividadesControl'>
                        <select id="ActividadColor"  name="ActividadColor" class="MyField" style="width:50px; font-size:18px;" onchange="this.style.background = this.value">
                            <option value="#1E88E5" style="background:#1E88E5;" selected="selected">&nbsp;</option>
                            <option value="#E53935" style="background:#E53935;">&nbsp;</option>
                            <option value="#8E24AA" style="background:#8E24AA;">&nbsp;</option>
                            <option value="#3949AB" style="background:#3949AB;">&nbsp;</option>
                            <option value="#1E88E5" style="background:#1E88E5;">&nbsp;</option>
                            <option value="#00ACC1" style="background:#00ACC1;">&nbsp;</option>
                            <option value="#00897B" style="background:#00897B;">&nbsp;</option>
                            <option value="#9E9D24" style="background:#9E9D24;">&nbsp;</option>
                            <option value="#EF6C00" style="background:#EF6C00;">&nbsp;</option>
                            <option value="#BF360C" style="background:#BF360C;">&nbsp;</option>
                            <option value="#6D4C41" style="background:#6D4C41;">&nbsp;</option>
                            <option value="#757575" style="background:#757575;">&nbsp;</option>
                            <option value="#546E7A" style="background:#546E7A;">&nbsp;</option>
                        </select>
                </div>
            </div>
        </div>

        <!-- RECORDATORIOS ############################################################### -->
        <div id="CapaNotificaciones">
                <div class='ActividadesReglon TituloGrupo' style="width:570px; border-top:1px solid <?php echo $_SESSION['COLOR_LINEA']?>;">Recordatorios</div>

                <div class='ActividadesReglon'>
                    <div>
                        <div id="LabelAlarma" class='Actividadesfield'>Alarmas</div>
                        <div id="ContenedorAlarmas" class='ActividadesControl'></div>
                    </div>
                </div>
                <div class='ActividadesReglon'>
                    <div>
                        <div class='Actividadesfield'>&nbsp;</div>
                        <div id="DivAddAlarma" class='ActividadesControl'>
                            <?php if($estado==0){ ?>
                                <a onclick="AddAlarma()" style="cursor:pointer">
                                    <div class="add16" style="width:16px; height:16px; float:left; margin:0 5px 0 0"></div>
                                    <div style="float:left; text-decoration:underline">Agregar Alarma</div>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
        </div>
	</div>



<script>
	var Finalizar = 'false';
	document.getElementById("ContenedorFormuActividad").style.height = Ext.getBody().getHeight() - 140;

	Ext.get('PanelAcciones').load(
		{
			url		: 	'../crm/acciones/accion_grilla.php',
			scripts : 	true,
			nochache: 	true,
			params : 	{
				id_objetivo  : 	'<?php echo $id_objetivo ?>',
				id_actividad : 	'<?php echo $id ?>',
			}
		}
	);

	function AgregarAccion(){

		 	Win_Agrega_Accion = new Ext.Window({
				id			: 'Win_Agrega_Accion',
				width		: 450,
				height		: 250,
				//title		: 'Agregar Accion' ,
				//iconCls 	: '1',
				modal		: true,
				autoScroll	: true,
				closable	: false,
				border		: false,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'../crm/acciones/accion_agregar.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
								id_objetivo  : 	'<?php echo $id_objetivo ?>',
								id_actividad : 	'<?php echo $id ?>',
							}
				}
			}).show();
	}

	var Finalizar = 'false';

	var ComboTipo = document.getElementById('Tipo');
	for(g=0;g<Grupos.length;g++){
		var gru = document.createElement('optgroup');
		gru.label = Grupos[g][1];
		//gru.style = "font-size:14px; font-style:bold; padding:0 0 0 5px; color:#F00";
			for(i=0;i<Opciones.length;i++){
				//console.log(Grupos[g][0]+' = '+Opciones[i][5]);
				if(parseInt(Grupos[g][0]) == parseInt(Opciones[i][5])){
					console.log('si');
					var opt = document.createElement('option');
					opt.value = Opciones[i][0];
					opt.innerHTML = Opciones[i][1];
					gru.appendChild(opt);
				}
			}
		ComboTipo.appendChild(gru);
	}

	function retorna(c){
		if(c=='true') return true;
		if(c=='false') return false;
	}

	function SelectTipo(){
		var tipo = document.getElementById('Tipo').value;

		for(i=0;i<Opciones.length;i++){
			if(Opciones[i][0]==tipo){
				document.getElementById('CapaCitas').style.display = Opciones[i][2];
				document.getElementById('CapaTareas').style.display = Opciones[i][3];
			}
		}
	}

	function CheckCALENDARIO(cual){
		if(cual.checked == true){
			document.getElementById('CapaNotificaciones').style.display = 'inline';
			document.getElementById('CapaColor').style.display = 'inline';
		}else{
			document.getElementById('CapaNotificaciones').style.display = 'none';
			document.getElementById('CapaColor').style.display = 'none';
		}
	}

	var input = $('#ActividadLaHorai');
	input.clockpicker({
		autoclose: true
	});

	var input = $('#ActividadLaHoraf');
	input.clockpicker({
		autoclose: true
	});

	var input = $('#ActividadLaHorai2');
	input.clockpicker({
		autoclose: true
	});

	countP = TotalFun;
	function AddPersona(){

		var CapaContenedora = document.getElementById('ContenedorPersonas');
			var divContenedor = document.createElement("div");
				divContenedor.setAttribute('style','float:left; margin:2px 0 1px 0');
				//DIV DEL INPUT Y EL COMBO
				var div1 = document.createElement("div");
					div1.setAttribute('style','width:315px; float:left');
					//CREACION INPUT 1
					var a = document.createElement("input");
						a.setAttribute('type','hidden');
						a.setAttribute('class','ClassIdAsignado');
						a.setAttribute('id','ActividadId_asignado'+countP);
						a.setAttribute('name','ActividadId_asignado');
					div1.appendChild(a);
					//CREACION INPUT 2
					var b = document.createElement("input");
						b.setAttribute('type','text');
						b.setAttribute('class','MyField ClassAsignado');
						b.setAttribute('id','ActividadAsignado'+countP);
						b.setAttribute('name','ActividadAsignado');
						b.setAttribute('onBlur','ValidarFieldVacio(this)');
						b.setAttribute('style','width:310px;');
						b.setAttribute('readonly','readonly');
						b.setAttribute('placeholder','Funcionario al cual se le asigna la Cita...');
					div1.appendChild(b);
				divContenedor.appendChild(div1)
				//DIV DE LA IMAGEN DE ELIMINAR
				var div3 = document.createElement("div");
					div3.setAttribute('class','delete');
					div3.setAttribute('style','width:16px; height:16px; float:left; margin:0 5px 0 0');
					div3.setAttribute('onclick','DeleteAlarma(this)');
				divContenedor.appendChild(div3)
			CapaContenedora.appendChild(divContenedor);
		//AddOptios(countP);
		BuscarFuncionario('ActividadId_asignado'+countP,'ActividadAsignado'+countP);
		countP++
	}

	function DeleteAlarma(cual){
		padre 	= cual.parentNode;
		abuelo 	= padre.parentNode;
		abuelo.removeChild(padre);
	}

	count = 0;
	function AddAlarma(time,time_type){
		if(typeof(time) == "undefined"){var time = 1;}
		if(typeof(time_type) == "undefined"){var time_type = 'H';}

		function AddOptios(cual){
			if(document.getElementById('ActividadAlarmaTipo_'+cual)){
				var Op = document.getElementById('ActividadAlarmaTipo_'+cual);
				Op.add(new Option("Minutos","M"));
				Op.add(new Option("Horas","H"));
				Op.add(new Option("Dias","D"));
				Op.value = time_type;
			}else{
				SetTimeout('AddOptios('+cual+')',200);
			}
		}

		var CapaContenedora = document.getElementById('ContenedorAlarmas');
			var divContenedor = document.createElement("div");
				divContenedor.setAttribute('style','float:left; margin:2px 0 1px 0');
				//DIV DEL INPUT Y EL COMBO
				var div1 = document.createElement("div");
					div1.setAttribute('style','width:140px; float:left');
					//CREACION INPUT
					var a = document.createElement("input");
						a.setAttribute('type','text');
						a.setAttribute('class','MyField ClassActividadTiempo');
						a.setAttribute('id','ActividadAlarmaTiempo_'+count);
						a.setAttribute('name','ActividadAlarmaTiempo');
						a.setAttribute('value',time);
						a.setAttribute('style','width:40px; font-size:12px; margin:0 10px 0 0');
					div1.appendChild(a);
					//CREACION COMBO
					var b = document.createElement("select");
						b.setAttribute('class','MyField ClassActividadTipo');
						b.setAttribute('id','ActividadAlarmaTipo_'+count);
						b.setAttribute('name','ActividadAlarmaTipo');
						b.setAttribute('style','width:80px; font-size:12px; margin:0 10px 0 0');
					div1.appendChild(b);
				divContenedor.appendChild(div1)
				//DIV DEL TEXTO
				var div2 = document.createElement("div");
					div2.setAttribute('style','width:120px; float:left');
					//CREACION LABEL
					var c = document.createTextNode("antes del vencimiento");
					div2.appendChild(c);
				divContenedor.appendChild(div2)
				//DIV DE LA IMAGEN DE ELIMINAR
				var div3 = document.createElement("div");
					div3.setAttribute('class','delete');
					div3.setAttribute('style','width:16px; height:16px; float:left; margin:0 5px 0 0');
					div3.setAttribute('onclick','DeleteAlarma(this)');
				divContenedor.appendChild(div3)
			CapaContenedora.appendChild(divContenedor);
		AddOptios(count);
		count++
	}

	function CheckAhora(cual){//CITAS
		if(cual.checked == true){
			var fecha = fechaJS();
			var hora = horaJS(formatoHora);
			document.getElementById('ActividadLaHorai').value = '00:00';
			document.getElementById('CapaFechai').style.display = "none";
			document.getElementById('ActividadLaHoraf').value = '23:59';
			document.getElementById('CapaFechaf').style.display = "none";

		}else{
			document.getElementById('ActividadLaHorai').value = '08:00';
			document.getElementById('CapaFechai').style.display = "inline";
			document.getElementById('ActividadLaHoraf').value = '18:00';
			document.getElementById('CapaFechaf').style.display = "inline";
		}
	}

	function CheckAhora2(cual){//TAREAS - LLAMADAS - CORREOS - ETC..
		if(cual.checked == true){
			var fecha = fechaJS();
			var hora = horaJS(formatoHora);
			document.getElementById('ActividadLaHorai2').value = hora;
			document.getElementById('ActividadLaHorai2').disabled = true;
			document.getElementById('ActividadFechai2').value = fecha;
			Ext.getCmp('ActividadLaFechai2').disable();
			//document.getElementById('ActividadFechai').disabled = true
		}else{
			document.getElementById('ActividadLaHorai2').disabled = false;
			Ext.getCmp('ActividadLaFechai2').enable();
			//document.getElementById('ActividadFechai').disabled = false
		}
	}

	//////CITAS/////////////////////////////////////////
	new Ext.form.DateField(
		{
	        applyTo		: 	'ActividadFechai',
	        id 			: 	'ActividadLaFechai',
	        format     	: 	'Y-m-d',
	        width      	:   100,
	        allowBlank 	:   false,
	        showToday  	:   true,
	        editable   	:   false,
	        disabled	: 	false,
	        value	   	: 	'<?php echo $fechai; ?>'
		}
	);

	new Ext.form.DateField(
		{
	        applyTo		: 	'ActividadFechaf',
	        id 			: 	'ActividadLaFechaf',
	        format     	: 	'Y-m-d',
	        width      	:   100,
	        allowBlank 	:   false,
	        showToday  	:   true,
	        editable   	:   false,
	        disabled	: 	false,
	        value	   	: 	'<?php echo $fechaf; ?>'
		}
	);
	/////TAREAS-LAMDAS-ETC../////////////////////////////////
	new Ext.form.DateField(
		{
	        applyTo		: 	'ActividadFechai2',
	        id 			: 	'ActividadLaFechai2',
	        format     	: 	'Y-m-d',
	        width      	:   100,
	        allowBlank 	:   false,
	        showToday  	:   true,
	        editable   	:   false,
	        disabled	: 	false,
	        value	   	: 	'<?php echo $fechai; ?>'
		}
	);
	//////////////////////////////////////////////////////////

	document.getElementById('ActividadLaHorai').value = '<?php echo $splitHorai[0].':'.$splitHorai[1]?>';
	document.getElementById('ActividadLaHoraf').value = '<?php echo $splitHoraf[0].':'.$splitHoraf[1]?>';
	document.getElementById('ActividadLaHorai2').value = '<?php echo $splitHorai[0].':'.$splitHorai[1]?>';

	function GuardaFinalizarActividad(){
		Finalizar = 'true';
		GuardaActividad();
	}

	function GuardaActividad(){

        MyLoading2('on');

		var tipo = document.getElementById('Tipo').value;

		for(i=0;i<Opciones.length;i++){ //HACE LE FOR DE LAS OPCIONES DE ACTIVIDADES
			if(Opciones[i][0]==tipo){ //SELECCIONA LA ACTIVIDAD
				if(Opciones[i][2]=='inline'){//DECIDE SI ES LA FECHA COMPLETA O SOLO LA FECHA DE FINALIZACION
					var fechai 		= Ext.getCmp('ActividadLaFechai').value;
					var fechaf 		= Ext.getCmp('ActividadLaFechaf').value;
					var horai 		= document.getElementById('ActividadLaHorai').value;
					var horaf 		= document.getElementById('ActividadLaHoraf').value;
				}else{
					var fechai 		= Ext.getCmp('ActividadLaFechai2').value;
					var fechaf 		= Ext.getCmp('ActividadLaFechai2').value;
					var horai 		= document.getElementById('ActividadLaHorai2').value;
					var horaf 		= document.getElementById('ActividadLaHorai2').value;
				}
			}
		}


		var tema 		= document.getElementById('ActividadTema').value;
		var id_asignado = document.getElementById('ActividadId_asignado').value;
		var asignado 	= document.getElementById('ActividadAsignado').value;
		var observacion = document.getElementById('ActividadObservacion').value;
		var color		= document.getElementById('ActividadColor').value;
		if(id_objetivo == 0){var tipo_crm = 'cliente';}else{var tipo_crm = 'objetivo';}

		var ContadorPersonas = new Array(id_asignado);
		var id_asignados = document.getElementsByClassName('ClassIdAsignado');
		var personas  	 = "false";
		if(id_asignados.length>0){
			personas = "";
			for(i=0;i<id_asignados.length;i++){
				var id_asig		= id_asignados[i].value;
				if(id_asig != ""){//SI EL CAMPO NO ESTA EN BLANCO
					if(ContadorPersonas.indexOf(id_asig) < 0){//SI EL CAMPO NO ESTA REPETIDO (OSEA SI NO LO ENCUENTRAN EN EL ARRAY)
						personas += id_asig; //+','+asig
						if(i<(id_asignados.length-1)){personas += '{.}';}
						ContadorPersonas.push(id_asig);
					}
				}
			}
		}

		horai = (horaMYSQL(horai));
		horaf = (horaMYSQL(horaf));
		tema = tema.replace(/[\#\<\>\'\"]/g, '');
		observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

		if(tema == ''|| asignado == ''){alert('Faltan Datos Obligatorios por diligenciar!');MyLoading2('off');return false;}

		Ext.Ajax.request(
			{
				url		: '../crm/actividades/agregaRegistro.php',
				params	: {
					opcion		 	:   'update',
					id				:	<?php echo $id ?>,
					id_objetivo  	: 	id_objetivo,
					tema 			: 	tema,
					empleado 		: 	asignado,
					id_asignado 	: 	id_asignado,
					tipo			:	tipo,
					fechai 			: 	fechai,
					horai 			: 	horai,
					fechaf 			: 	fechaf,
					horaf 			: 	horaf,
					observacion 	: 	observacion,
					color			:   color,
					tipo_crm		:	tipo_crm,
					id_cliente		:   id_cliente,
					personas		:	personas,
                    finaliza        :   Finalizar
				},
				success	: function (result, request){
								var resultado  =  result.responseText.split("{.}");

								if (VentanaActi2 == 1) {
									Actualiza_Div_Actividades2(<?php echo $id ?>);
									var NombreGrilla = 'Actividades2';
								}
								if (VentanaActi1 == 1) {
									Actualiza_Div_Actividades(<?php echo $id ?>);
									var NombreGrilla = 'Actividades';
								}

								if(Finalizar == 'true'){
									FinalizaActividad(<?php echo $id ?>,NombreGrilla);
								}

								////// COPIO AL CALENDARIO LA INFORMACION /////////////////////////////////////////////////////////////////////////
								if(document.getElementById('ActividadCopiarCRM').checked == true){
									var OpciCal = 'update';
								}else{
									var OpciCal = 'delete';
								}
								Ext.Ajax.request(
									{
										url		: '../crm/actividades/agregaRegistroCALENDARIO.php',
										params	: {
											opcion		 	:   OpciCal,
											tema 			: 	tema,
											empleado 		: 	asignado,
											id_empleado 	: 	id_asignado,
											tipo			:	tipo,
											fechai 			: 	fechai,
											horai 			: 	horai,
											fechaf 			: 	fechaf,
											horaf 			: 	horaf,
											descripcion 	: 	observacion,
											color			:   color,
											id_objetivo_crm	: 	id_objetivo,
											id_actividad_crm:	<?php echo $id ?>,
											id_calendario	: 	id_calendario,
                                            personas        :   personas,

										},
										success	: function (result, request){

												var resultadoCal  =  result.responseText.split("{.}");

												////// AGREGO LAS NOTIFICACIONES  //////////////////////////////////////////////////////////////////////////
												var ActividadTiempo = document.getElementsByClassName('ClassActividadTiempo');
												var ActividadTipo   = document.getElementsByClassName('ClassActividadTipo');
												if(ActividadTiempo.length>0){
													var datos  = "";
													for(i=0;i<ActividadTiempo.length;i++){
														var time 		= ActividadTiempo[i].value;
														var tipo		= ActividadTipo[i].value;
														datos += time+','+tipo
														if(i<(ActividadTiempo.length-1)){datos += '{.}';}
													}
													console.log(datos);
													Ext.Ajax.request(
														{
															url		: '../calendario/agregaAlarmas.php',
															params	: {
                                                                opcion           :  'insert',
                                                                id_calendario    : 	resultadoCal[0],
                                                                datos            : 	datos,
                                                                fechai           : 	fechai,
                                                                horai            : 	horai,
                                                                fechaf           : 	fechaf,
                                                                horaf            : 	horaf,
                                                                personas         :  personas,
                                                                id_empleado      :  id_asignado,
                                                                id_calendarioOLD :  id_calendario//SI ES UPDATE DEBE BORRAR LAS ALARMAS DEL CALENDARIO QUE SE BORRA
															},
															success	: function (result, request){},
															failure : function(){alert('Error guardando las Notificaciones : '+result);}
														}
													);
												}
												////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                                MyLoading2('off');
                                                Win_Agrega_Registro.close();
										},
										failure : function(){alert('Error guardando en el calendario : '+result);}
									}
								);
								MyLoading2('off');
				},
				failure : function(){alert('Error guardando la actividad en el calendario : '+result);MyLoading2('off');}
			}
		);
	}

    function EliminaActividad(){

        var confir1 = Ext.MessageBox.confirm('Eliminar Actividad', 'Seguro que desea eliminar esta Actividad?<br><br>Esta Actividad ser&aacute; tambi&eacute;n eliminada del calendario.', salga);
        function salga(btn){
            if(btn == 'yes'){
                MyLoading2('on');
                Ext.Ajax.request(
                    {
                        url     : '../crm/actividades/agregaRegistro.php',
                        params  : {
                            opcion          :   'delete',
                            id              :   '<?php echo $id ?>',
                            id_calendario   :   id_calendario
                        },
                        success : function (result, request){
                                        //var resultado  =  result.responseText.split("{.}");                                        
                            Win_Agrega_Registro.close();
                            if (VentanaActi2 == 1) {
                                Elimina_Div_Actividades2(<?php echo $id ?>);                               
                            }
                            if (VentanaActi1 == 1) {
                                Elimina_Div_Actividades(<?php echo $id ?>);                                
                            }
                            setTimeout("MyLoading2('off')",1000);
                        }
                    }
                );
            }
        }

    }

	document.getElementById('Tipo').value						= "<?php echo $tipo ?>";
	document.getElementById('ActividadObservacion').value 		= "<?php echo $descripcion ?>";
	document.getElementById('ActividadCopiarCRM').checked		= <?php echo $CopiaCALENDARIO ?>;

	ValidarFieldVacio(document.getElementById('ActividadTema'));
	SelectTipo();

	<?php
		if($CopiaCALENDARIO == 'true'){
	?>
			document.getElementById('ActividadColor').value				= "<?php echo $color ?>";
			document.getElementById('ActividadColor').style.background	= "<?php echo $color ?>";
			for(i=0;i<Alarmas.length;i++){
				AddAlarma(Alarmas[i][0],Alarmas[i][1]);
			}
	<?php
		}else{
			echo "document.getElementById('ActividadColor').style.background = document.getElementById('ActividadColor').value";
		}
	?>

	CheckCALENDARIO(document.getElementById('ActividadCopiarCRM'));


	<?php if($estado==1){ ?>

		document.getElementById('Tipo').disabled = true;
		document.getElementById('ActividadTema').disabled = true;
		document.getElementById('ActividadAsignado').disabled = true;
		document.getElementById('ActividadObservacion').disabled= true;
		document.getElementById('ActividadColor').disabled = true;
		document.getElementById('ActividadCopiarCRM').disabled = true;
		document.getElementById('ActividadLaHorai').disabled = true;
		document.getElementById('ActividadLaHoraf').disabled = true;
		document.getElementById('ActividadLaHorai2').disabled = true;
		Ext.getCmp('ActividadLaFechai').disable();
		Ext.getCmp('ActividadLaFechaf').disable();
		Ext.getCmp('ActividadLaFechai2').disable();
		Ext.getCmp('ActividadLaFechai2').disable();

   	<?php } ?>


</script>