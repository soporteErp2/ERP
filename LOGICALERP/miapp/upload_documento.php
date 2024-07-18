<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");	
?>

<div style="margin:10px 0 0 15px; width:250px;">
	Utilice esta opcion si necesita cargar, descargar o borrar un Documento.
</div>
 <div style="margin:10px 0 0 15px">
	Nombre del Documento<br />
	<div id="logoEvento" name="logoEvento">
		<input id="documento" style="margin:10px 0 0 0px; width:253px;" type="text" value="" readonly />
	</div>
    <input id="Descargar" style="margin:10px 0 0 0px; width:253px;" onclick="descargaFormato();" type="button" value="Descargar" DISABLED />
	
</div>
<script>

	existeFormato();
	
	function existeFormato(){
		MyLoading();
		Ext.Ajax.request(
			{
				url		: 'bd/bd.php',
				failure	: function(){alert('Error mostrando Formato!');},
				params	: {
							op	: "existeFormato",
							id 	: "<?php echo $id ?>",
				},
				success: function (result, request)
				{
					var resultado  =  result.responseText.split("{.}");
					var respuesta = resultado[0];
					var doc = resultado[1];
					if(respuesta == 'true'){
						document.getElementById("documento").value = doc;
						document.getElementById("Descargar").disabled=false;
					}else{
						document.getElementById("documento").value = "Sin Documento";
						document.getElementById("Descargar").disabled=true;				
					}
				}
			}
		);	
	}
	
	function descargaFormato(){
		Ext.Ajax.request(
				{
					url		: 'bd/bd.php',
					failure	: function(){alert('Error Borrando Formato!');},
					params	: {
								op	: "descargaFormato",
								id 	: "<?php echo $id ?>",
					},
					success: function (result, request)
					{
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						var ext = resultado[1];
						if(respuesta == 'true'){
							
							ventana=window.open("descarga_doc.php?file="+ext,"","");
							ventana.focus();
						}else{
							alert("Error, No existe o Corrupto");
						
						}
					}
				}
		);
	}
	
</script>