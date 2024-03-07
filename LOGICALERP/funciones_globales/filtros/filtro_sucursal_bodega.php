<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	// echo __FILE__.'<br>'.$_SERVER['SCRIPT_NAME'];

	$width         = '190';
	$option        = json_decode($config);
	$id_empresa    = $_SESSION['EMPRESA'];
	$id_sucursal   = $_SESSION['SUCURSAL'];
	$MSucursales   = user_permisos(1);
	$whereSucursal = '';
	$optionInput   = '';

	if($option->width > 0) $width = $option->width;

	//=====================// METODOS DE CONFIGURACION //=====================//
	/*
		@$option->todasSucursales 	= 	Muestra opcion todas las sucursales
		@$option->todasBodegas 		= 	Muestra opcion todas las bodegas
		@$option->loadFunction 		= 	Function a ejecutar al seleccionar la bodega
	*/

	//==========================// FILTRO SUCURSAL //==========================//
	//*************************************************************************//
	if($action != 'loadBodega'){

		if($MSucursales == 'true'){

			if($option->todasSucursales == 'true'){
				$optionInput = '<option value="todas">TODAS LAS SUCURSALES</option>
		    					<optgroup></optgroup>';
			}
		}
		else{ $whereSucursal = 'AND id = '.$id_sucursal; }

		$sqlSucursal   = "SELECT id,nombre FROM empresas_sucursales WHERE activo=1 AND id_empresa = '$id_empresa' $whereSucursal ORDER BY nombre ASC";
		$querySucursal = mysql_query($sqlSucursal,$link);

		echo '<div style="float:left; margin: 5px 0 0 10px">
				    <div style="float:left; width:50px; padding:3px 0 0 0">Sucursal</div>
				    <div id="recibidor_filtro_empresa_'.$opc.'" style="float:left; width:'.$width.'px">
					    <select class="myfield" id="filtro_sucursal_'.$opc.'" style="width:'.$width.'px" onChange="cambia_filtro_bodega_'.$opc.'()">
					    	'.$optionInput;

							while($row=mysql_fetch_array($querySucursal)){
								$selected = ($row['id'] == $_SESSION['SUCURSAL'])? 'selected': '';
							 	echo '<option style="font-style:italic" value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
							}

		echo '			</select>
					</div>
				</div>
				<div style="float:left; margin: 5px 0 0 10px">
				    <div style="float:left; width:50px; padding:3px 0 0 0">
				        Bodega
				    </div>
				    <div id="recibidor_filtro_bodega_'.$opc.'" style="float:left; width:'.$width.'px"></div>
				</div>

				<script>

					if (typeof(localStorage.sucursal_'.$opc.')!="undefined") {
						if (localStorage.sucursal_'.$opc.'!="") {
							document.getElementById("filtro_sucursal_'.$opc.'").value=localStorage.sucursal_'.$opc.';
						}
					}

					function cambia_filtro_bodega_'.$opc.'(){
						var filtro_sucursal = document.getElementById("filtro_sucursal_'.$opc.'").value;
						localStorage.sucursal_items = filtro_sucursal;

						Ext.get("recibidor_filtro_bodega_'.$opc.'").load({
							url     : "'.$_SERVER['SCRIPT_NAME'].'",
							scripts : true,
							nocache : true,
							params  :
							{
								opc             : \''.$opc.'\',
								config          : \''.$config.'\',
								action          : \'loadBodega\',
								filtro_sucursal : filtro_sucursal,
							}
						});
					}
					cambia_filtro_bodega_'.$opc.'();

				</script>';
	}
	//===========================// FILTRO BODEGA //===========================//
	//*************************************************************************//
	else{

		if($option->todasBodegas == 'true'){
			$optionInput = '<option value="todas">TODAS LAS BODEGAS</option>';
		}

		$function      = ($option->loadFunction=="true")? 'carga_'.$opc.'()': $option->loadFunction;
		$whereSucursal = ($filtro_sucursal > 0)? "AND id_sucursal = '$filtro_sucursal'": "";

		$optionSucursal = "";
		if ($filtro_sucursal <> 'todas'){

			$optionInput = '<optgroup></optgroup>';

			$sqlBodega     = "SELECT id,nombre FROM empresas_sucursales_bodegas WHERE activo=1 AND id_empresa = '$id_empresa' $whereSucursal ORDER BY nombre ASC";
			$queryBodega   = mysql_query($sqlBodega,$link);

			while($row=mysql_fetch_array($queryBodega)){
				$selected = ($row['id'] == $id_bodega)? 'selected': '';
				$optionSucursal .= '<option style="font-style:italic" value="'.$row['id'].'" '.$selected.'>'.$row['nombre'].'</option>';
			}
		}

		echo '<select class="myfield" name="filtro_ubicacion_'.$opc.'" id="filtro_ubicacion_'.$opc.'" style="width:'.$width.'px" onChange="'.$function.'">
					'.$optionInput.'
					'.$optionSucursal.'
				</select>';

		if($option->loadFunction == "true"){

			echo '<script>

					function carga_'.$opc.'(){
						var filtro_bodega = document.getElementById("filtro_ubicacion_'.$opc.'").value;

						Ext.get("contenedor_'.$opc.'").load({
							url     : "'.$option->urlRender.'",
							scripts : true,
							nocache : true,
							params  :
							{
								filtro_bodega   : filtro_bodega,
								filtro_sucursal : \''.$filtro_sucursal.'\',
								opcGrilla       : \''.$opc.'\',
								config          : \''.$config.'\',
							}
						});

					}
					carga_'.$opc.'();

				</script>';

		}
		else{ echo '<script>'.$option->loadFunction.'</script>'; }

	}

?>