<?php
	include_once("../../configuracion/conectar.php");
	include_once("../../configuracion/define_variables.php");
		
	if(isset($ID)&& $ID != ''){
		$result = mysql_query("SELECT foto FROM empleados WHERE id=$ID");
		$image  = mysql_result($result,0,'foto');
		if($ID != ''){
			echo '<script> var ID = '.$ID.'</script>';	
		}
	}
?>

<div id="Recibidor_Foto_Empleado" style="float:left; width:220px; height:140px; margin:0 0 0 7px;">
    <div style="float:left; width:103; height:133px; margin:4px 0 0 32px; -webkit-box-shadow: 0px 0px 3px #666; -moz-box-shadow: 0px 0px 2px #666;">
		<?php if(isset($ID)&& $ID != '' && $image != ''){ ?>
            <img src="webcam/foto_generador.php?ID=<?php echo $ID ?>" width="103" height="133" />
        <?php }else{ ?>
        	<img src="images/foto0.png" width="103" height="133">
        <?php }?>        
    </div>
    <div style="float:left; width:45px; margin:4px 0 0 7px; cursor:pointer" onClick="carga();">
        <img src="images/buscar2.png" width="33" height="33">
    </div>
    <div style="float:left; width:45px; margin:6px 0 0 7px; cursor:pointer" onClick="CargaFlashFoto();" >
        <img src="images/Webcam.png" width="33" height="33">
    </div>
</div>

<script>
function CargaFlashFoto(){
	if(typeof(ID) == "undefined"){
		alert('Primero debe Guardar el Empleado y despues Capturar la Fotografia!.');
	}else{
		document.location = 'webcam/webcam_empleados.php?ID=<?php echo $ID ?>';
	}
}
function carga(){
	window.parent.cargaFotoEmpleado(<?php echo $ID ?>);
}
</script>