<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$consul = mysql_query("SELECT * FROM configuracion_carnet WHERE id_empresa = $id_empresa",$link);
?>
<div class="WizTitulo">Asistente Generador de Carnet</div>
<div class="WizContenido">
	Por favor seleccione el Formato que desdea utilizar en la Impresion del carnet.<br /><br />
    <select class="myfield" id="formato_carnet" name="">
		<?php
			while($row = mysql_fetch_array($consul)){
				echo '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
			}
	  	?>
    </select>

</div>

<script>
	//Ext.getCmp('BtnWizardPrev').enable();
	Ext.getCmp('BtnWizardNext').enable();

	/*function FncPrev(){
		alert('prev');
	}*/

	function FncNext(){
		id_carnet = document.getElementById('formato_carnet').value;
		window.open('carnet/carnet.php?id_carnet='+id_carnet+'&id=<?php echo $id?>&id_empresa=<?php echo $id_empresa ?>');
		Win_Wizard.close();
	}
</script>