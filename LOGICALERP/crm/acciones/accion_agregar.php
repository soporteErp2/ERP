<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
?>
	<!--<div id="ToolbarAgregarAcciones">-->
    <div id="ToolbarTareas">
    	<div style="float:left; width:270px; font-size:20px; margin:0 0 0 0;">Agregar una acci&oacute;n<br /><span style="font-size:12px"><?php echo '' ?></span></div>

        <div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="Win_Agrega_Accion.close();">
    		<div class="ic_highlight_remove_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        	<div style="text-align:center">Cerrar</div>
        </div>
		<div style="width:48px; height:48px; float:right; cursor:pointer;" onclick="GuardarAccion();">
       		<div class="ic_check_circle_white_24dp" style="float:left; width:36px; height:36px; margin: 0 0 0 6px;"></div>
        	<div style="text-align:center">Guardar</div>
        </div>
    </div>
	<div class='ActividadesReglon'>
		<div class='Actividadesfield'>Detalles de la<br />Accion Realizada</div>
		<div class='ActividadesControl'><textarea id="ObservacionAccion" class="MyFieldObligatorio" onKeyup="ValidarFieldVacio(this)" style="width:310px; height:80px;"></textarea></div>
	</div>

<script>
	/*var ToolbarAgregarAcciones = new Ext.Toolbar(
		{
			renderTo	: 'ToolbarAgregarAcciones',
			items: [
				{
					xtype		: 'button',
					text		: 'Guardar Accion',
					scale		: 'large',
					iconCls		: 'guardar',
					iconAlign	: 'top',
					handler 	: function(){GuardarAccion();}
				}
			]
		}
	);*/

	function GuardarAccion(){

		var accion = document.getElementById('ObservacionAccion').value;

		accion = accion.replace(/[\#\<\>\'\"]/g, '');
		if(accion == ""){alert('Por favor diligencie la observacion de la accion');return false;}

		Ext.Ajax.request(
			{
				url		: '../crm/acciones/accion_guarda.php',
				params	: {
					id_objetivo  	: 	'<?php echo $id_objetivo ?>',
					id_actividad  	: 	'<?php echo $id_actividad ?>',
					accion 			: 	accion
				},
				success	: function (result, request){
								var resultado  =  result.responseText.split("{.}");
								var elid = resultado[0];
								Win_Agrega_Accion.close();
								Inserta_Div_GrillaAcciones(elid);
								if (VentanaActi2 == 1) {
									Actualiza_Div_Actividades2(<?php echo $id_actividad ?>);
								}
								if (VentanaActi1 == 1) {
									Actualiza_Div_Actividades(<?php echo $id_actividad ?>);
								}

						  },
				failure : function(){
								alert('Error guardando Tarea : '+result);
						  }
			}
		);
	}

</script>