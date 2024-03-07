<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

		$consul = $mysql ->query("SELECT * FROM calendario WHERE id = $id",$link);

		$tipo 				= $mysql ->result($consul,0,'tipo');
		$tema 				= $mysql ->result($consul,0,'tema');
		$fechai 			= $mysql ->result($consul,0,'fechai');
		$horai 				= $mysql ->result($consul,0,'horai');
		$fechaf 			= $mysql ->result($consul,0,'fechaf');
		$horaf 				= $mysql ->result($consul,0,'horaf');
		$color 				= $mysql ->result($consul,0,'color');
		$id_objetivo_crm  	= $mysql ->result($consul,0,'id_objetivo_crm');
		$id_actividad_crm 	= $mysql ->result($consul,0,'id_actividad_crm');
		$id_asignado   	    = $mysql ->result($consul,0,'id_empleado');
		$asignado       	= $mysql ->result($consul,0,'empleado');

		//ESTO ES POR SI EL EMPLEADO ES UN ADICIONAL EL NOMBRE LE APAREZCA A ABRIR LA VENTANA Y NO SALGA EL DEL FUNCIONARIO PRINCIPAL
		$consul_1 = $mysql ->query("SELECT id_asignado,asignado FROM calendario_personas WHERE id_calendario = $id",$link);

		echo '<script>
				  var arrayAsignados = new Array();';

		$cual = 0;

		while($row = $mysql->fetch_array($consul_1)){
			echo 'arrayAsignados['.$cual.'] = new Array();
                  arrayAsignados['.$cual.']["id_asignado"] = "'.$row['id_asignado'].'";
                  arrayAsignados['.$cual.']["asignado"]	   = "'.$row['asignado'].'";';
            $cual++;
		}

		echo '</script>';

		$id_asignado_1 = $mysql ->result($consul_1,0,'id_asignado');
		$asignado_1    = $mysql ->result($consul_1,0,'asignado');

		$splitHorai = explode(':',$horai);
		$splitHoraf = explode(':',$horaf);
		$descripcion  		= str_replace("<br>", "\\n", $mysql ->result($consul,0,'descripcion'));

		if($id_objetivo_crm == 0 && $id_actividad_crm == 0){$CopiaCRM = 'false';}
		if($id_objetivo_crm == 0 && $id_actividad_crm != 0){$CopiaCRM = 'cliente';}
		if($id_objetivo_crm != 0 && $id_actividad_crm != 0){$CopiaCRM = 'objetivo';}

		if($CopiaCRM != 'false'){
			$consul_obj = $mysql ->query("SELECT cliente,objetivo,id_objetivo,id_cliente,estado FROM crm_objetivos_actividades WHERE id = $id_actividad_crm",$link);
			if($CopiaCRM == 'cliente'){
				$FieldCRM 		= $mysql ->result($consul_obj,0,'cliente');
				$id_objetivo 	= "";
				$id_cliente 	= $mysql ->result($consul_obj,0,'id_cliente');
			}
			if($CopiaCRM == 'objetivo'){
				$FieldCRM 		= $mysql ->result($consul_obj,0,'cliente')." / ".$mysql ->result($consul_obj,0,'objetivo');
				$id_objetivo 	= $mysql ->result($consul_obj,0,'id_objetivo');
				$id_cliente	 	= $mysql ->result($consul_obj,0,'id_cliente');
			}
			$estado = $mysql->result($consul_obj,0,'estado');
			echo '<script>var CRMBloqueo = "true";</script>';
		}else{
			echo '<script>var CRMBloqueo = "false";</script>';
		}

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //$formatoFecha= $mysql ->result($mysql ->query("SELECT formato_hora FROM empresas WHERE id=$_SESSION[EMPRESA]",$link),0,"formato_hora");
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
	//$fecha = date('Y-m-d');
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//SCRIPT QUE TRAE LAS OPCIONES DE ACTIVIDADES Y LAS CONVIENRTE EN UN ARRAY JS
	$consulActi = $mysql ->query("SELECT * FROM crm_configuracion_actividades WHERE activo = 1",$link);
	$sum = 0;
	$tot = $mysql ->num_rows($consulActi);
	echo '<script> var Opciones = new Array(';
		while($rowActi = $mysql ->fetch_array($consulActi)){

			echo ' [["'.$rowActi['id'].'"],';
			echo ' ["'.$rowActi['nombre'].'"],';
			echo ' ["'.$rowActi['fecha_completa'].'"],';
			echo ' ["'.$rowActi['fecha_vencimiento'].'"],';
			echo ' ["'.$rowActi['copiar_crm_obligatorio'].'"]]';
			$sum++;
			if($sum<$tot){echo ',';}
		}
	echo '); </script>';

	//SCRIPT QUE TRAE LAS ALARMAS Y LAS CONVIERTE EN UN SCRIP JS
	$consulNoti = $mysql ->query("SELECT * FROM calendario_notificaciones WHERE id_calendario = $id",$link);
	if($mysql ->num_rows($consulNoti)>0){;
		$sum = 0;
		$tot = $mysql ->num_rows($consulNoti);
		echo '<script> var Alarmas = new Array(';
			while($rowNoti = $mysql ->fetch_array($consulNoti)){

				echo ' [["'.$rowNoti['time'].'"],';
				echo ' ["'.$rowNoti['time_type'].'"]]';
				$sum++;
				if($sum<$tot){echo ',';}
			}
		echo '); </script>';
	}else{
		echo '<script> var Alarmas = new Array(); </script>';
	}
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

	<div id="ToolbarTareas">

    	<div style="float:left; width:300px; font-size:20px; margin:25px 0 0 0;">Edici&oacute;n de Actividades<br /><span style="font-size:12px"><?php echo fecha_larga($fecha)?></span></div>

        <div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="Win_Agrega_Registro.close();">
    		<div class="ic_highlight_remove_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        	<div style="text-align:center">Cerrar</div>
        </div>
        <?php if($estado==0){ ?>
		    <div style="width:48px; height:48px; float:right; cursor:pointer;  margin:0 10px 0 0;" onclick="EliminaActividad();">
       	      	<div class="ic_delete_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
              	<div style="text-align:center">Eliminar</div>
            </div>
      
            <div style="width:48px; height:48px; float:right; cursor:pointer; margin:0 10px 0 0;" onclick="GuardaFinalizarActividad();">
                  <div class="ic_check_circle_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
                  <div style="text-align:center">Finalizar</div>
            </div>
      
		    <div style="width:48px; height:48px; float:right; cursor:pointer; margin:0 10px 0 0;" onclick="GuardaActividad();">
       	      	<div class="ic_autorenew_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
              	<div style="text-align:center">Guardar</div>
            </div>
        <?php } ?>
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
        <div class='ActividadesReglon'>
            <div class='Actividadesfield'>Asignado a</div>
            <div class='ActividadesControl'>
                <input id="ActividadId_asignado" type="hidden" value="<?php echo $id_asignado; ?>">
                <input id="ActividadAsignado" type="text" class="MyField" style="width:310px;" value="<?php echo $asignado; ?>" onBlur="ValidarFieldVacio(this)" readonly="readonly" placeholder=" Funcionario al cual se le asigna la Cita...">
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

        <!-- CRM ############################################################### -->
        <div class='ActividadesReglon TituloGrupo' style="width:570px; border-top:1px solid <?php echo $_SESSION['COLOR_LINEA']?>;">CRM</div>
        <div class='ActividadesReglon' style="width:500px;">
            <div class='Actividadesfield'><span id="labelCRM3">CRM</span></div>
            <div class='ActividadesControl' style="width:20px"><input id="ActividadCopiarCRM" type="checkbox" onChange="CheckCRM(this);"></div>
            <div class='ActividadesControl' style="width:350px;"><span id="labelCRM2">Esta actividad esta vinculada con el seguimiento a un cliente (CRM)</span></div>
        </div>
        <div class='ActividadesReglon'>
            <div class='Actividadesfield'><span id="labelCRM">Vinculado a</span></div>
            <div class='ActividadesControl'>
                <input id="tipo_crm" type="hidden" ><!-- "cliente" / "objetivo" -->
                <input id="id_objetivo" type="hidden" >
                <input id="id_cliente" type="hidden" >
                <input id="cliente" type="text" class="MyFieldObligatorio" style="width:310px;" onclick="BuscaClientesObjetivosProyectos()" onBlur="ValidarFieldVacio(this)" placeholder="Cliente / Objetivo &oacute; Proyecto.">
            </div>
        </div>

        <!-- RECORDATORIOS ############################################################### -->
        <div class='ActividadesReglon TituloGrupo' style="width:570px; border-top:1px solid <?php echo $_SESSION['COLOR_LINEA']?>;">Recordatorios</div>

        <!--<div class='ActividadesReglon'>
            <div class='Actividadesfield'>Calendario</div>
            <div class='ActividadesControl' style="width:20px"><input id="ActividadCopiarCalendario" type="checkbox" onChange="CheckCopiarCalendario(this);" disabled="disabled" checked="checked"></div>
            <div class='ActividadesControl' style="width:250px; color:#AAA">Copiar esta Cita al Calendario</div>
        </div>-->

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
                  <a onclick="AddAlarma()" style="cursor:pointer">
                      <div class="add16" style="width:16px; height:16px; float:left; margin:0 5px 0 0"></div>
                      <div style="float:left; text-decoration:underline">Agregar Alarma</div>
                  </a>
                </div>
            </div>
        </div>
	</div>


<script>

	var Finalizar = 'false';

	for(i=0;i<arrayAsignados.length;i++){
		if(arrayAsignados[i]["id_asignado"] == usuario_calendario){
			document.getElementById('ActividadId_asignado').value = arrayAsignados[i]["id_asignado"];
			document.getElementById('ActividadAsignado').value    = arrayAsignados[i]["asignado"];
		}
	}

	document.getElementById("ContenedorFormuActividad").style.height = Ext.getBody().getHeight() - 140;

	var ComboTipo = document.getElementById('Tipo');
	for(i=0;i<Opciones.length;i++){
		var opt = document.createElement('option');
    	opt.value = Opciones[i][0];
    	opt.innerHTML = Opciones[i][1];
    	ComboTipo.appendChild(opt);
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
				if(CRMBloqueo=='false'){
					document.getElementById('ActividadCopiarCRM').disabled = retorna(Opciones[i][4]);
					if(Opciones[i][4]=='true'||Opciones[i][4]==true){
						document.getElementById('ActividadCopiarCRM').checked = retorna(Opciones[i][4]);
						CheckCRM(document.getElementById('ActividadCopiarCRM'));
					}
				}
			}
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

	count = 0;
	function DeleteAlarma(cual){
		padre 	= cual.parentNode;
		abuelo 	= padre.parentNode;
		abuelo.removeChild(padre);
	}

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

	function CheckCRM(cual){
		var Campo = document.getElementById('cliente');
		if(cual.checked == true){
			document.getElementById('labelCRM').style.color = "#333";
			Campo.disabled = false;
			ValidarFieldVacio(Campo);
		}else{
			Campo.disabled = true;
			Campo.className = 'MyField';
			document.getElementById('labelCRM').style.color = "#AAA";
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
		var cliente		= document.getElementById('cliente').value;
		var tipo_crm	= document.getElementById('tipo_crm').value;
        var id_objetivo	= document.getElementById('id_objetivo').value;
		var id_cliente	= document.getElementById('id_cliente').value;


		horai = (horaMYSQL(horai));
		horaf = (horaMYSQL(horaf));
		tema = tema.replace(/[\#\<\>\'\"]/g, '');
		observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

		if(tema == ''|| asignado == ''){alert('Faltan Datos Obligatorios por diligenciar!');return false;}

		if(document.getElementById('ActividadCopiarCRM').checked == true){
			if(cliente == ''){alert('Faltan los datos de enlace con el CRM!');return false;}
		}

		MyLoading2('on');
		Ext.Ajax.request(
			{
				url		: '../../LOGICALERP/calendario/actividades/agregaRegistro.php',
				params	: {
					opcion		 	:   'update',
					id				: 	'<?php echo $id ?>',
					tema 			: 	tema,
					empleado 		: 	asignado,
					id_empleado 	: 	id_asignado,
					tipo			:	tipo,
					fechai 			: 	fechai,
					horai 			: 	horai,
					fechaf 			: 	fechaf,
					horaf 			: 	horaf,
					descripcion 	: 	observacion,
					color			:   color
				},
				success	: function (result, request){
								//var resultado  =  result.responseText.split("{.}");
								recarga();

								////// AGREGO LAS NOTIFICACIONES  //////////////////////////////////////////////////////////////////////////
								var ActividadTiempo = document.getElementsByClassName('ClassActividadTiempo');
								var ActividadTipo   = document.getElementsByClassName('ClassActividadTipo');
								if(ActividadTiempo.length>0){
									var datos  = "";
									for(i=0;i<ActividadTiempo.length;i++){
										var time 		= ActividadTiempo[i].value;
										var type		= ActividadTipo[i].value;
										datos += time+','+type
										if(i<(ActividadTiempo.length-1)){datos += '{.}';}
									}
									console.log(datos);
									Ext.Ajax.request(
										{
											url		: '../../LOGICALERP/calendario/agregaAlarmas.php',
											params	: {
												opcion		 	:   'insert',
												id_calendario  	: 	'<?php echo $id ?>',
												datos 			: 	datos,
												fechai 			: 	fechai,
												horai 			: 	horai,
												fechaf 			: 	fechaf,
												horaf 			: 	horaf,
												id_empleado 	: 	id_asignado,
											},
											success	: function (result, request){},
											failure : function(){alert('Error guardando las Notificaciones : '+result);setTimeout("MyLoading2('off')",1000);}
										}
									);
								}
								////// ACTULIZO LA INFORMACION EN EL CRM /////////////////////////////////////////////////////////////////////////
								if(CRMBloqueo=='true'){opcion = 'update';}else{opcion = 'insert';}
								if(document.getElementById('ActividadCopiarCRM').checked == true){
									Ext.Ajax.request(
										{
											url		: '../../LOGICALERP/calendario/actividades/agregaRegistroCRM.php',
											params	: {
												opcion		 	:   opcion,
												id_calendario  	: 	'<?php echo $id ?>',
												id_actividad_crm:	'<?php echo $id_actividad_crm ?>',
												id_objetivo  	: 	id_objetivo,
												tipo			:	tipo,
												tema 			: 	tema,
												asignado 		: 	asignado,
												id_asignado 	: 	id_asignado,
												id_cliente		:	id_cliente,
												fechai 			: 	fechai,
												horai 			: 	horai,
												fechaf 			: 	fechaf,
												horaf 			: 	horaf,
												observacion 	: 	observacion,
												tipo_crm		:	tipo_crm
											},
											success	: function (result, request){
												Win_Agrega_Registro.close();
												setTimeout("MyLoading2('off')",1000);
												if(Finalizar == 'true'){
                                                    FinalizaActividad('<?php echo $id_actividad_crm ?>');
                                                }
											},
											failure : function(){alert('Error guardando Tarea : '+result);setTimeout("MyLoading2('off')",1000);}
										}
									);
								}else{
									Win_Agrega_Registro.close();
									setTimeout("MyLoading2('off')",1000);
								}
								////////////////////////////////////////////////////////////////////////////////////////////////////////////
						  },
				failure : function(){alert('Error guardando la actividad en el calendario : '+result);}
			}
		);
	}

	function EliminaActividad(){

		var confir1 = Ext.MessageBox.confirm('Eliminar Actividad', 'Seguro que desea eliminar esta Actividad?<br><br>Si esta Actividad esta Asocidad con un proyecto o un cliente del CRM, solo se eliminara del calendario, la actividad en el CRM seguira registrada.', salga);
		function salga(btn){
			if(btn == 'yes'){
				MyLoading2('on');
				Ext.Ajax.request(
					{
						url		: '../../LOGICALERP/calendario/actividades/agregaRegistro.php',
						params	: {
							opcion		 	:   'delete',
							id				: 	'<?php echo $id ?>'
						},
						success	: function (result, request){
										//var resultado  =  result.responseText.split("{.}");
										recarga();
										Win_Agrega_Registro.close();
										setTimeout("MyLoading2('off')",1000);

						}
					}
				);
			}
		}

	}

	<?php
		if($CopiaCRM != 'false'){
	?>
			ElCheckCRM = document.getElementById('ActividadCopiarCRM')
			ElCheckCRM.checked = true;
			ElCheckCRM.disabled = true;
			Campo = document.getElementById('cliente');
			document.getElementById('labelCRM').style.color = "#CCC";
			document.getElementById('labelCRM2').style.color = "#CCC";
			document.getElementById('labelCRM3').style.color = "#CCC";
			Campo.disabled = true;
			document.getElementById('cliente').value = '<?php echo $FieldCRM ?>';
			document.getElementById('tipo_crm').value = '<?php echo $FieldCRM ?>';
			document.getElementById('id_objetivo').value = '<?php echo $id_objetivo ?>';
			document.getElementById('id_cliente').value = '<?php echo $id_cliente ?>';
	<?php
		}else{
	?>
			ElCheckCRM = document.getElementById('ActividadCopiarCRM')
			CheckCRM(ElCheckCRM);
	<?php
		}
	?>


	document.getElementById('Tipo').value						= "<?php echo $tipo ?>";
	document.getElementById('ActividadObservacion').value		= "<?php echo $descripcion ?>";
	document.getElementById('ActividadColor').value				= "<?php echo $color ?>";
	document.getElementById('ActividadColor').style.background	= "<?php echo $color ?>";
	ValidarFieldVacio(document.getElementById('ActividadTema'));
	SelectTipo();

	if(CRMBloqueo=='true'){
		document.getElementById('ActividadCopiarCRM').disabled = true;
		ValidarFieldVacio(document.getElementById('cliente'));
	}

	for(i=0;i<Alarmas.length;i++){
		AddAlarma(Alarmas[i][0],Alarmas[i][1]);
	}

	function FinalizaActividad(elid){

        Win_FinalizaActividad = new Ext.Window({
            id          : 'Win_FinalizaActividad',
            width       : 450,
            height      : 250,
            //title     : 'FinalizarActividad' ,
            //iconCls   : '1',
            modal       : true,
            border      : false,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     :'../../LOGICALERP/crm/actividades/actividades_finalizar.php',
                scripts :true,
                nocache :true,
                params  :
                        {
                            id_actividad        :   elid,                            
                            calendario          :   true,
                        }
            }
        }).show();
    }

    function EliminaActividad(){

        win_elimina_actividad = new Ext.Window({
            id          : "win_elimina_actividad",
            title       : "Eliminar Actividad",
            width       : 300,
            height      : 140,
            scrollY     : true,
            modal       : true,
            closable    : true,
            drag        : true,
            resize      : true,
            autoLoad    :
            {
                url     : '../../LOGICALERP/calendario/actividades/agregaRegistro.php',
                params  :{  opcion : 'ventanaEliminarActividad' }
            }
        }).show();     
    }

    function eliminar_actividad_calendario(){

        var checkEliminarCRM = document.getElementById("checkEliminarCRM").checked;     

        Ext.Ajax.request({
            url     : '../../LOGICALERP/calendario/actividades/agregaRegistro.php',
            params  : {
                opcion          :   'delete',
                id              :   '<?php echo $id ?>',
                checkCRM        :   checkEliminarCRM,
                id_actividad    :   '<?php echo $id_actividad_crm ?>',
            },
            success : function (result, request){
                //var resultado  =  result.responseText.split("{.}");
                recarga();
                win_elimina_actividad.close();
                Win_Agrega_Registro.close();
            }
        });
        
    }

</script>