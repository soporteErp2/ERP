<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$SQL     = "SELECT id,nombre
				FROM empresas_sucursales_bodegas
				WHERE id_empresa = '$id_empresa'
					AND id_sucursal = '$id_sucursal'
					AND activo=1
				ORDER BY nombre ASC";
	$consulS = mysql_query($SQL,$link);

	$idSelected = ($varSelected != '')? $varSelected: $id_sucursal;

?>

<style type="text/css">

	#recibidor_filtro_bodega_<?php echo $opc; ?>{
		height     : 100%;
		width      : 100%;
		text-align : center;
	}

	#filtro_ubicacion_<?php echo $opc; ?>{
		height         : 25px;
		width          : 155px;
		font-size      : 11px;
		margin-top     : 12px;
	}

</style>

<div id="recibidor_filtro_bodega_<?php echo $opc; ?>">
    <select class="myfield" name="filtro_ubicacion_<?php echo $opc; ?>" id="filtro_ubicacion_<?php echo $opc; ?>" onchange="carga_<?php echo $opc; ?>('true')">
        <?php
			while($rowS=mysql_fetch_array($consulS)){
				$selected = ($rowS['id'] != $idSelected)? '': 'selected';
				echo '<option value="'.$rowS['id'].'" '.$selected.'>'.$rowS['nombre'].'</option>';
			}
        ?>
	</select>
</div>


<script>
	// validar que se cargo la libreria si no se dar alerta para borrar cache y recargar la pagina
	// if (!EditableSelect) { alert("Aviso\nSe han realizado nuevas actualizaciones!\nDebe borrar cache del navegador y recargar toda la pagina para que funcione correctamente"); }
	// else{

		// $('#filtro_ubicacion_<?php echo $opc; ?>').editableSelect({ filter: false });

		// $('#filtro_ubicacion_<?php echo $opc; ?>')
	 //    .editableSelect()
	 //    .on('select.editable-select', function (e, li) {
	 //    	carga_<?php echo $opc; ?>('true');
	 //    });

		function carga_<?php echo $opc; ?>(reloadBody){
			var filtro_bodega = document.getElementById('filtro_ubicacion_<?php echo $opc; ?>').value;
			// console.log(filtro_bodega);
			<?php
				if($newUrlRender != ''){ $url_render = $newUrlRender; }
				if($renderizaBody == 'true'){
					echo'Ext.get("contenedor_'.$opc.'").load({
							url     : "'.$url_render.'",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega : filtro_bodega,
								'.$imprimeVarPhp.'
							}
						});';
				}
			?>
		}
		carga_<?php echo $opc; ?>('true');
	// }

	// function focusInput(){  document.getElementById('filtro_ubicacion_<?php echo $opc; ?>').value=''; }
	// function blurInput(){  document.getElementById('filtro_ubicacion_<?php echo $opc; ?>').value=document.getElementById('filtro_ubicacion_<?php echo $opc; ?>').getAttribute('data-name') }

</script>
