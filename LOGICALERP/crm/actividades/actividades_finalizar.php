<?php 
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $consul = $mysql->query("SELECT estado,tema FROM crm_objetivos_actividades WHERE id = $id_actividad",$link);
	$estado = $mysql->result($consul,0,'estado');
	$tema   = $mysql->result($consul,0,'tema');

?>
	<!--<div id="ToolbarFinalizarActividad"></div>-->
    <div id="ToolbarTareas">
    	<div style="float:left; width:270px; font-size:20px; margin:0 0 0 0;">Finalizaci&oacute;n de Actividad<br /><span style="font-size:12px"><?php echo $tema ?></span></div>
        
        <div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="Win_FinalizaActividad.close();">
    		<div class="ic_highlight_remove_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        	<div style="text-align:center">Cerrar</div>	
        </div>              
		<div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="GuardarFinalizarActividad();">        
       		<div class="ic_check_circle_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        	<div style="text-align:center">Guardar</div>	
        </div>     
    </div>
	<div class='ActividadesReglon'>
		<div class='Actividadesfield'>Detalles de la<br />Finalizacion de la<br />Actividad</div>
		<div class='ActividadesControl'><textarea id="ObservacionAccion" class="MyFieldObligatorio" onKeyup="ValidarFieldVacio(this)" style="width:310px; height:80px;"></textarea></div>
	</div>

<script>

	function GuardarFinalizarActividad(){
		
		var accion = document.getElementById('ObservacionAccion').value;

		accion = accion.replace(/[\#\<\>\'\"]/g, '');
		if(accion == ""){alert('Por favor diligencie la observacion de la finalizacion de la Actividad');return false;}

		Ext.Ajax.request(
			{
				url		: '../crm/actividades/actividades_finalizar_guarda.php',
				params	: {
					id_actividad  	: 	'<?php echo $id_actividad ?>',
					accion 			: 	accion 
				},
				success	: function (result, request){
								var resultado  =  result.responseText.split("{.}");
								var elid = resultado[0];
								Win_FinalizaActividad.close();
								if('<?php echo $calendario ?>' != 'true'){//SI VIENE O NO DEL CALENDARIO
									Actualiza_Div_<?php echo $NombreGrillaActiva ?>(elid);
								}
								else{
									recarga();
								}
								MyLoading2('off');
						  },
				failure : function(){
								alert('Error guardando : '+result);
						  }
			}
		);
	}

</script>

<?php
    if($estado == 1){
    	echo '<script>Win_FinalizaActividad.close();</script>';
    }
?>