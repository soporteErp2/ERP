<?php
	require_once('../../configuracion/conectar.php');
	require_once('../../configuracion/define_variables.php');

	function carga_datos_interface_origami(){
		$sql 							= "SELECT * FROM origami_config limit 1";
		$result							= mysql_query($sql);
		if ($row = mysql_fetch_array( $result )){
			global $vacio,$dir_web_service,$propiedad_web_service,$grupo_propiedad_web_service,$grupo_tx_web_service,$item_facturacion_web_service;
			$vacio							= false;
			$dir_web_service				= $row['webservice'];
			$propiedad_web_service			= $row['propiedad'];
			$grupo_propiedad_web_service	= $row['grupo_propiedad'];
			$grupo_tx_web_service			= $row['grupo_tx'];
			$item_facturacion_web_service	= $row['item_facturacion_salones'];
		}else{
			global $vacio;
			$vacio							= true;
		}
	}
	carga_datos_interface_origami();
?>
<style>
	.EmpConte	{width:300px;float:left; margin:5px 0 0 0; height:20px;}
	.EmpConte_item_facturacion_web_service	{width:300px;float:left; margin:5px 0 0 0; height:20px;  <?php if($vacio) echo "display:none";?>}
	.EmpField	{width:160px;float:left;}
	.Emplabel	{width:140px;float:left;}
	.myfield	{width:80px; height:18px}
</style>
<form name="FormularioMaestroRental" id="FormularioMaestroRental">
	<div style="width:300px; margin:15px 0 0 15px;">
		<div class="EmpConte" style="width:550px">
			<div class="EmpLabel">
				Direccion del WebService:
			</div>
			<div id="tipoDoc" class="EmpField" style="width:400px">
				http://  <input class="myfield" style="width:200px" name="dir_web_service" value="<?php echo $dir_web_service; ?>" type="text" id="dir_web_service" />  /WSASISTE.asmx
			</div>
		</div>
		<div class="EmpConte">
			<div class="EmpLabel">
				Propiedad:
			</div>
			<div id="noDoc" class="EmpField">
				<input class="myfield" name="propiedad_web_service" value="<?php echo $propiedad_web_service; ?>" type="text" id="propiedad_web_service" />
			</div>		
		</div>
		<div class="EmpConte">
			<div class="EmpLabel">
				Grupo - Propiedad:
			</div>
			<div class="EmpField">
				<input class="myfield" name="grupo_propiedad_web_service" value="<?php echo $grupo_propiedad_web_service; ?>" type="text" id="grupo_propiedad_web_service"  onclick="" />
			</div>
		</div>
		<div class="EmpConte">
			<div class="EmpLabel">
				Grupo - TX:
			</div>
			<div class="EmpField">
				<input class="myfield" name="grupo_tx_web_service" value="<?php echo $grupo_tx_web_service; ?>" type="text" id="grupo_tx_web_service"  onclick="" />
			</div>
		</div>	
		<div class="EmpConte_item_facturacion_web_service">
			<div class="EmpLabel">
				Item Facturacion Salones:
			</div>
			<div class="EmpField">
				<input class="myfield" name="item_facturacion_web_service" value="<?php echo $item_facturacion_web_service; ?>" type="text" id="item_facturacion_web_service"  onclick="selecciona_item_origami()" readonly />
			</div>
		</div>	
	</div>
</form>

<script>
	function guarda_datos_interface_origami(){
		
		Ext.Ajax.request(
			{
			url		: '../interface/origami/bd/bd.php',
			method	: 'post',
			timeout : 180000,
			params	:
				{
					op								: 'guarda_datos_interface_origami',
					dir_web_service 				: document.getElementById('dir_web_service').value,
					propiedad_web_service 			: document.getElementById('propiedad_web_service').value,
					grupo_propiedad_web_service 	: document.getElementById('grupo_propiedad_web_service').value,
					grupo_tx_web_service 			: document.getElementById('grupo_tx_web_service').value,
					item_facturacion_web_service 	: document.getElementById('item_facturacion_web_service').value
				},
			success: function (result, request)	{
					var resultado  =  result.responseText.split("{.}");
					if(resultado[0]=="true"){
						win_origami_config.close();	
					}else{
						alert("Sin resultados");}
				}
			}
		);	
	}
	
	function selecciona_item_origami(){
		win_selecciona_item_origami = new Ext.Window
		(
			{
				id			: 'win_selecciona_item_origami',
				height		: 500,
				width		: 400,
				title		: 'Seleccion Item de Salon Interfaz Origami',
				modal		: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		:'../interface/origami/seleccionaItemaSalon.php',
					scripts	:true,
					nocache	:true,
					params	:
							{
							}
				}	
			}
		).show();
	}
</script>