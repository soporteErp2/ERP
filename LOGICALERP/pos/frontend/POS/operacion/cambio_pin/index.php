<?php

	include('../../../../../../configuracion/conectar.php');
	include('../../../../../../configuracion/define_variables.php');


?>
<div id="tbarCambioPin" style="width:100%;height:68px;"></div>
<div id="contentAnularComandas" style="width:100%;height:calc(100% - 68px);">
	<div class="form_body" style="padding-left: 10px;">
		<div id="form_content_field_Productos_nombre" materialdesign="true" class="form_content_field input-filled" style="width:320px; min-height:25px; display:block; max-width : falsepx;"> 
			<input 
			type="password" 
			style="width:150px;" 
			class="with-label" 
			obligatorio="true" 
			input-ok="true" 
			name="nombre" 
			id="form_pin" 
			value="" 
			data-value="" 
			data-required="true" 
			data-style="none" 
			data-label="Nuevo Pin"> 
			<label class="floating-label" for="form_Productos_nombre">Nuevo Pin (4 Digitos)</label> 
		</div>
	</div>
	
</div>

<script>
	"use strict";
	(function() {

		$W.Add({
		idApply : "tbarCambioPin",
		items   :
		[
			{
				xtype : "tbar",
				id    : "tbarCambioPinPos",
				items :
				[
					{
						xtype     : "button",
						text      : "Guardar",
						scale     : "large",
						cls       : "done_all",
						iconAlign : "top",
						handler   : function(){
							setPin();
						}
					},"--"
				]
			}
		]})

	})();

	var setPin = ()=>{
		$W.Loading();
		$W.Ajax({
			url    : "../../../backend/pos_admin/Controller.php",
			params :  {
				method : "setPin",
				pin    : document.getElementById('form_pin').value
			},
			timeout : 2000,
			success : function(result,xhr){
				console.log(result.responseText); //lee respuesta como texto
				console.log(JSON.parse(result.responseText)); //lee respuesta como json
				let response = JSON.parse(result.responseText);
				if (response.status=='success'){ alert(response.message); WinGlobal.close(); }
				else{ alert(response.message) }
				$W.Loading();
			},
			failure : function(xhr){
				console.log("fail");
				// console.log(xhr);
				$W.Loading();
			}
		})

	}

	// loadGrilla();
</script>