<?php 
    include("../../configuracion/conectar.php");
    include("../../configuracion/define_variables.php");

    $estado = mysql_result(mysql_query("SELECT estado FROM crm_objetivos WHERE id = $id",$link),0,'estado');

?>
	<div id="ToolbarFinalizarObjetivo"></div>
	<div class='ActividadesReglon'>
		<div class='Actividadesfield'>Detalles de la<br />Finalizacion del<br />Objetivo</div>
		<div class='ActividadesControl'><textarea id="ObservacionAccion" class="MyFieldObligatorio" onKeyup="ValidarFieldVacio(this)" style="width:310px; height:80px;"></textarea></div>
	</div>

<script>
	var ToolbarAgregarAcciones = new Ext.Toolbar(
		{
			renderTo	: 'ToolbarFinalizarObjetivo',
			items: [
				{
					xtype		: 'button',
					text		: 'Finalizar Objetivo',
					scale		: 'large',
					iconCls		: 'guardar',
					iconAlign	: 'top',
					handler 	: function(){GuardarFinalizarObjetivo();}
				}
			]
		}
	);

	function GuardarFinalizarObjetivo(){
		
		var accion = document.getElementById('ObservacionAccion').value;

		accion = accion.replace(/[\#\<\>\'\"]/g, '');
		if(accion == ""){alert('Por favor diligencie la observacion de la finalizacion del Objetivo');return false;}

		Ext.Ajax.request(
			{
				url		: '../crm/objetivos_finalizar_guarda.php',
				params	: {
					id  	: 	'<?php echo $id ?>',
					accion 			: 	accion 
				},
				success	: function (result, request){
								var resultado  =  result.responseText.split("{.}");
								var elid = resultado[0];
								Win_FinalizaObjetivo.close();
								Actualiza_Div_Objetivos(elid);
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
    	echo '<script>Win_FinalizaObjetivo.close();</script>';
    }
?>