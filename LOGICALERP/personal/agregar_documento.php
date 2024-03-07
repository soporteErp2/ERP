<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
	//include("bd/functions_bd.php");

	if (isset($id) && $id != 'false') {
		$cargo  = mysql_result(mysql_query("SELECT nombre FROM empleados_tipo_documento WHERE id = '$id'",$link),0,'nombre');
		$valores=true; //INDICA QUE SE ESTA CARGANDO PARA EDICION
		$id_empleado = $id; //VARIABLE REQUERIDA CUANDO EL FORMULARIO SEA PARA MODIFICACION
		echo '<script>var opcion_guardar = '.$id.';</script>';
	}else
		echo '<script> var opcion_guardar = false;</script>'; //DEFINE LA VARIABLE JAVASCRIPT QUE INDICA QUE ES NUEVO
?>
<div style="margin:10px">

		<div style="float:left; width:100%; margin:25px 0 0 0">
			<div style="float:left; width:70px">
				Documento :
			</div>
			<div style="float:left; width:170px">
				<input class="myfield" name="cargo" type="text" id="cargo" style="width:170px" value="<?php if(isset($valores)){echo $cargo;}?>" />
			</div>
		</div>
</div>
<script>

function guardaDocumento(){
	if(opcion_guardar==false){
		var op	= "agregarDocumento";
	}else{
		var op	= "actualizarDocumento";
	}
	var cargo	= document.getElementById('cargo').value;

	if(cargo==""){
		alert("Falta el nombre de Documento");
	}else{
		Ext.Ajax.request
		(
			{
			url		: 'bd/bd.php',
			method	: 'post',
			timeout : 180000,
			params	:
				{
					op			:	op,
					cargo		:	cargo,
					id			:	opcion_guardar
				},
			success: function (result, request)
				{
					var resultado  =  result.responseText.split("{.}");
					var respuesta = resultado[0];
					var observacion = resultado[1];
					if(respuesta == 'false'){
						alert('Error Enviando la Solicitud!\n\n'+observacion);
					}
					if(respuesta == 'true'){
						if(op == "agregarDocumento"){
							Win_Agregar_Documento.close();
							MyLoading();
							Inserta_Div_Documentos(observacion);

						}
						if(op == "actualizarDocumento"){
							Win_Agregar_Documento.close();
							MyLoading();
							Actualiza_Div_Documentos(observacion);

						}
					}
				}
			}
		);
	}
}
</script>
