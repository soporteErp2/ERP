<?php 
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $formatoFecha= mysql_result(mysql_query("SELECT formato_hora FROM empresas WHERE id=$_SESSION[EMPRESA]",$link),0,"formato_hora");
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
	
	$ConsulCli = mysql_query("SELECT nombre_comercial FROM terceros WHERE id = $id_cliente",$link);
	$Cliente   = mysql_result($ConsulCli,0,"nombre_comercial");

	$title = 'Agregar un nuevo Proyecto';

	$objetivo    = '';
	$observacion = '';
	$valor       = '';
	$estado      = 0;

	echo '<script>var opcion = "insert";</script>';

	if($id_objetivo > 0){
		$Consul  = mysql_query("SELECT objetivo,
									   vencimiento,
								       observacion,
								       valor,
								       prioridad,
								       id_linea,
								       id_estado AS estado_proyecto,
								       id_tipo,
								       probabilidad_exito AS probabilidad,
								       estado 
								FROM crm_objetivos 
								WHERE id = $id_objetivo",$link);
		
		$objetivo        = mysql_result($Consul,0,"objetivo");
		$vencimiento     = mysql_result($Consul,0,"vencimiento");
		$valor           = mysql_result($Consul,0,"valor");
		$prioridad       = mysql_result($Consul,0,"prioridad");
		$tipo_proyecto   = mysql_result($Consul,0,"id_tipo");		
		$id_linea        = mysql_result($Consul,0,"id_linea");
		$estado_proyecto = mysql_result($Consul,0,"estado_proyecto");		
		$estado          = mysql_result($Consul,0,"estado");
		$probabilidad    = mysql_result($Consul,0,"probabilidad");

		$fecha = substr($vencimiento,0,10);
		$hora  = substr($vencimiento,11,5);

		$observacion = mysql_result($Consul,0,"observacion");

		$title   = 'Editar Proyecto';	

		echo '<script>var opcion = "update";</script>';	

	}

	if($valor == '' || $valor < 0){
		$valor = '0.00';
	}
	//LISTADO DE LINEAS DE NEGOCIO
	$sql1 = mysql_query("SELECT * FROM configuracion_lineas_negocio WHERE activo = 1 AND id_empresa = '$_SESSION[EMPRESA]'",$link);
	while($row=mysql_fetch_array($sql1)){
		$selected = '';
		if($row['id'] == $id_linea){ $selected = 'selected'; }
		$option_negocio .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
	}

	//LISTADO DE LINEAS DE NEGOCIO
	$sql2 = mysql_query("SELECT * FROM configuracion_estados_proyectos WHERE activo = 1 AND id_empresa = '$_SESSION[EMPRESA]'",$link);
	while($row=mysql_fetch_array($sql2)){
		$selected = '';
		if($row['id'] == $estado_proyecto){ $selected = 'selected'; }
		$option_estados .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
	}

	//LISTADO DE LOS TIPOS DE PROYECTO
	$sql3 = mysql_query("SELECT * FROM crm_configuracion_tipos_proyecto WHERE activo = 1 AND id_empresa = '$_SESSION[EMPRESA]'",$link);
	while($row=mysql_fetch_array($sql3)){
		$selected = '';
		if($row['id'] == $tipo_proyecto){ $selected = 'selected'; }
		$option_tipos .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
	}

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

<div id="ToolbarTareasAgregarObjetivos" style="width:100%; height:70px; padding: 15px 10px 0 10px; overflow:hidden; box-sizing:border-box;">

    <!-- Botón Cerrar con SVG -->
    <div style="width:60px; height:60px; float:right; margin:0 10px 0 0; cursor:pointer;" onclick="Win_Agrega_Objetivos.close();">
        <div style="float:center; width:36px; height:36px; margin: 4px 0 0 15px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </div>
        <div style="text-align:center; color:#dc3545; font-weight:bold; font-size:13px;">Cerrar</div>
    </div>

    <!-- Botón Guardar con SVG -->
    <div style="width:60px; height:60px; float:right; margin:0 10px 0 0; cursor:pointer;" onclick="GuardaProyecto();">
        <div style="float:center; width:36px; height:36px; margin: 4px 0 0 15px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <div style="text-align:center; color:#28a745; font-weight:bold; font-size:13px;">Guardar</div>
    </div>

    <!-- Título del formulario -->
    <div style="float:left; width:calc(100% - 140px); font-size:22px; font-weight:bold; color:#003366; line-height:28px;">
        <?php echo $title; ?>
        <br>
        <span style="font-size:13px; font-weight:normal;"><?php echo $Cliente ?></span>
    </div>
</div>
	
    
    <div class='ActividadesReglon' style="width:500px">
		<div class='Actividadesfield' style="width:130px">Objetivo o Proyecto</div>
		<div class='ActividadesControl'><input id="ActividadTema" type="text" class="MyFieldObligatorio" style="width:450px;" onBlur="ValidarFieldVacio(this)" placeholder=" Descripcion del tema &oacute; accion a realizar..." value="<?php echo $objetivo ?>"></div>
	</div>
	<div class='ActividadesReglon' style="width:600px">
		<div class='Actividadesfield' style="width:130px">Tipo</div>
		<div class='ActividadesControl' style="width:210px">
			<select id="TipoProyecto" type="text" class="MyFieldObligatorio" style="width:190px;">
				<option value="">Seleccione</option>
				<?php echo $option_tipos ?>
			</select>
		</div>
		<div class='Actividadesfield' style="width:80px;">Prioridad</div>
		<div class='ActividadesControl' style="width:160px;">
			<select id="ActividadPrioridad" type="text" class="MyFieldObligatorio" style="width:160px;">
			<option value="">Seleccione</option>
			<option value="alta">Alta</option>
			<option value="media">Media</option>
			<option value="baja">Baja</option>
			</select>
		</div>
	</div>
	<div class='ActividadesReglon' style="width:600px">
		<div class='Actividadesfield' style="width:130px">Linea de Negocio</div>
		<div class='ActividadesControl' style="width:210px">
			<select id="LineaNegocio" type="text" class="MyFieldObligatorio" style="width:190px;">
				<option value="">Seleccione</option>
				<?php echo $option_negocio ?>
			</select>
		</div>
		<div class='Actividadesfield' style="width:80px;">Estado</div>
		<div class='ActividadesControl' style="width:160px">
			<select id="EstadosProyecto" type="text" class="MyFieldObligatorio" style="width:160px;">
				<option value="">Seleccione</option>
				<?php echo $option_estados ?>
			</select>
		</div>
	</div>
	<!--<div class='ActividadesReglon' style="width:500px">
		<div class='Actividadesfield' style="width:130px">Estado</div>
		<div class='ActividadesControl'>
			<select id="EstadosProyecto" type="text" class="MyFieldObligatorio" style="width:220px;">
				<?php echo $option_estados ?>
			</select>
		</div>
	</div>-->
	<div class='ActividadesReglon' style="width:600px">
		<div class='Actividadesfield' style="width:130px">Vencimiento</div>
		<div class='ActividadesControl' style="width:110px"><input id="ActividadFecha" type="text" class="MyField" style="width:100px;" ></div>
		<div class='ActividadesControl' style="width:100px"><input id="ActividadHora" type="text" class="MyField" style="width:100px;" ></div>
		<div class='Actividadesfield' style="width:80px">% Exito</div>
		<div class='ActividadesControl' style="width:160px">
			<select id="ProbabilidadProyecto" type="text" class="MyFieldObligatorio" style="width:160px;">				
				<option value="">Seleccione</option>
				<option value="alta">Alta</option>
				<option value="media">Media</option>
				<option value="baja">Baja</option>			
			</select>
		</div>
	</div>
	<div class='ActividadesReglon' style="width:500px">
		<div class='Actividadesfield' style="width:130px">Observaciones</div>
		<div class='ActividadesControl'><textarea id="ActividadObservacion" class="MyField" style="width:450px; height:80px;"><?php echo $observacion ?></textarea></div>
	</div>	
	<div class='ActividadesReglon' style="width:600px">
		<div class='Actividadesfield' style="width:130px">Valor</div>
		<div class='ActividadesControl' style="width:150px;"><input id="ActividadValor" type="text" class="MyFieldObligatorio" style="width:140px;" value="<?php echo $valor; ?>"></div>		
	</div>


<script>

	document.getElementById('ActividadPrioridad').value   = '<?php echo $prioridad ?>';
	document.getElementById('ProbabilidadProyecto').value = '<?php echo $probabilidad ?>';	

	new Ext.form.TimeField(
		{
	        format     	:   '<?php echo $formatTimeField; ?>',
	        id 			: 	'ActividadLaHora',
	        width     	:   80,
	        allowBlank 	:   true,
	        showToday  	:   true,
	        applyTo    	:   'ActividadHora',
	        editable   	:   false,			
		    //increment  	:   15,
		    //disabled	: 	true, 
		    value	   	: 	'<?php echo $hora ?>'		    
		}
	);

	new Ext.form.DateField(
		{
	        applyTo		: 	'ActividadFecha', 
	        id 			: 	'ActividadLaFecha',
	        format     	: 	'Y-m-d',
	        width      	:   100,
	        allowBlank 	:   false,
	        showToday  	:   true,
	        editable   	:   false,
	        //disabled	: 	true,
	        value	   	: 	'<?php echo $fecha; ?>'
		}
	);	

	/*var ToolbarTareas = new Ext.Toolbar(
		{
			renderTo	: 'ToolbarTareas',
			items: [
				{
					xtype		: 'button',
					text		: 'Guardar',
					scale		: 'large',
					iconCls		: 'guardar',
					iconAlign	: 'top',
					handler 	: function(){GuardaActividad();}
				},
				{
					xtype		: 'button',
					text		: 'Guardar y Finalizar',
					scale		: 'large',
					iconCls		: 'ok',
					iconAlign	: 'top',
					handler 	: function(){GuardaActividad('true');}
				}
			]
		}
	);*/


	function GuardaProyecto(){

		var tema            = document.getElementById('ActividadTema').value;
		var fecha           = Ext.getCmp('ActividadLaFecha').value;
		var hora            = Ext.getCmp('ActividadLaHora').value;
		var observacion     = document.getElementById('ActividadObservacion').value;
		var valorObj        = document.getElementById('ActividadValor').value;
		var prioridad       = document.getElementById('ActividadPrioridad').value;	
		var linea_negocio   = document.getElementById('LineaNegocio').value;
		var tipo_proyecto   = document.getElementById('TipoProyecto').value;
		var estado_proyecto = document.getElementById('EstadosProyecto').value;	
		var probabilidad    = document.getElementById('ProbabilidadProyecto').value;			




		hora = (horaMYSQL(hora));
		tema = tema.replace(/[\#\<\>\'\"]/g, '');
		observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

		if(tema == ''){alert('Faltan Datos Obligatorios por diligenciar!');return false;}
		if(valorObj == '' || valorObj < 0){alert('Campo Valor no puede ser vacio o negativo!');return false;}
		if(prioridad == ''){alert('Por favor seleccione una prioridad para el Proyecto!');return false;}


		hora_array = hora.split(":");
		
		if(hora >= '24:00:00'){
			hora = '00:'+hora_array[1]+':00';
		}

		Ext.Ajax.request(
			{
				url		: '../crm/objetivos_guarda.php',
				params	: {
					opcion		 	:   opcion,
					id_cliente  	: 	'<?php echo $id_cliente ?>',
					tema 			: 	tema,
					fecha 			: 	fecha, 
					hora 			: 	hora,
					observacion 	: 	observacion,
					id_objetivo     :   '<?php echo $id_objetivo; ?>',
					valorObj        :   valorObj,
					prioridad       :   prioridad,
					linea_negocio   :   linea_negocio,
					tipo_proyecto   :   tipo_proyecto,
					estado_proyecto :   estado_proyecto,
					probabilidad    :   probabilidad
				},
				success	: function (result, request){
								var resultado  =  result.responseText.split("{.}");
								var elid = resultado[0];
								Win_Agrega_Objetivos.close();
								if(opcion == 'insert'){
									Inserta_Div_Objetivos(elid);
								}
								else{
									Actualiza_Div_Objetivos(elid);
								}
						  },
				failure : function(){
								alert('Error guardando Tarea : '+result);
						  }
			}
		);

	}

	<?php if($estado==1){ ?>

		document.getElementById('ActividadTema').disabled        = true;		
		document.getElementById('ActividadObservacion').disabled = true;
		document.getElementById('ActividadValor').disabled       = true;
		document.getElementById('ActividadPrioridad').disabled   = true;
		document.getElementById('TipoProyecto').disabled   = true;
		Ext.getCmp('ActividadLaFecha').disable();
		Ext.getCmp('ActividadLaHora').disable();	

   	<?php } ?>

</script>