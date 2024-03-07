<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
?>

<div style="margin:20px">

		<div style="float:left; width:100%; margin:5px 0 0 0">
			<div style="float:left; width:50px">
				Rol :
			</div>
			<div style="float:left; width:150px">
				<input class="myfieldObligatorio" name="rol" type="text" id="rol" style="width:190px" onBlur="ValidarFieldVacio(this)" />
			</div>
		</div>

		<div style="float:left; width:100%; margin:5px 0 0 0">
			<div style="float:left; width:50px">
				Nivel :
			</div>
			<div style="float:left; width:150px">
                 <select class="myfieldObligatorio" style="width:190px" onBlur="ValidarFieldVacio(this)" name="rolnivel" id="rolnivel" />
                    <option value="" selected>Seleccione...</option>
                    <?php

                        $i = $_SESSION["ROLVALOR"];

                        for($i;$i<21;$i++){
                            echo '<option value="'.$i.'">Nivel '.$i.'</option>';
                        }
                    ?>
                </select>
			</div>
		</div>
</div>
<script>

function guardaRol(){
	var op       = "agregarRol";
	var rol      = document.getElementById('rol').value;
	var rolnivel = document.getElementById('rolnivel').value;

	if(rol==""){
		alert("Falta el nombre de rol");
	}else if(rolnivel==""){
		alert("Debe seleccionar el rol");
	}else{
		Ext.Ajax.request
		(
			{
			url		: 'bd/bd.php',
			params	:
				{
					op       : op,
					rol      : rol,
					rolnivel : rolnivel
				},
			success: function (result, request)
				{
					var resultado   =  result.responseText.split("{.}");
					var respuesta   = resultado[0];
					var observacion = resultado[1];
					if(respuesta == 'false'){
						alert('Error Enviando la Solicitud!\n\n'+observacion);
					}
					if(respuesta == 'true'){
						document.getElementById('rol').value = '';
						
						MyLoading();
						Inserta_Div_Rol(observacion);
						Win_Agregar_Rol1.close();

					}
				}
			}
		);
	}
}
</script>
