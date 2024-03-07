<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	header('Content-Type: text/html; charset=utf-8');
	$id_empresa = $_SESSION['EMPRESA'];

	// SI ES EDICION DE UN ACTIVO
	if($id_activo > 0){
		$sql = "SELECT
							code_bar,
							id_empresa,
							empresa,
							id_sucursal,
							sucursal,
							id_bodega,
							bodega,
							id_familia,
							codigo_familia,
							familia,
							id_grupo,
							codigo_grupo,
							grupo,
							id_subgrupo,
							codigo_subgrupo,
							subgrupo,
							codigo_automatico,
							codigo_activo,
							nombre_equipo,
							tipo,
							fecha_compra,
							id_documento_referencia,
							documento_referencia,
							documento_referencia_consecutivo,
							id_documento_referencia_inventario,
							costo,
							costo_sin_depreciar_anual,
							id_centro_costos,
							codigo_centro_costos,
							centro_costos,
							fecha_vencimiento_garantia,
							id_proveedor,
							nit_proveedor,
							proveedor,
							id_funcionario_asignado,
							documento_funcionario_asignado,
							funcionario_asignado,
							numero_serial,
							numero_placa,
							marca,
							modelo,
							color,
							chasis,
							id_unidad,
							unidad,
							numero_piezas,
							longitud,
							ancho,
							alto,
							volumen,
							peso,
							descripcion1,
							descripcion2,
							depreciable,
							fecha_inicio_depreciacion,
							vida_util,
							vida_util_restante,
							valor_salvamento,
							metodo_depreciacion_colgaap,
							depreciacion_acumulada,
							depreciable_niif,
							fecha_inicio_depreciacion_niif,
							vida_util_niif,
							vida_util_niif_restante,
							valor_salvamento_niif,
							metodo_depreciacion_niif,
							depreciacion_acumulada_niif,
							deteriorable,
							deterioro_acumulado,
							fecha_baja,
							quien_elimino,
							observaciones_eliminacion,
							usuario_elimino,
							id_usuario_elimino,
							id_usuario_creacion,
							id_usuario_compra,
							id_item,
							id_saldo_inicial,
							fecha_creacion,
							tenencia,
							fecha_vencimiento_tenencia,
							sincronizar_siip,
							estado
						FROM activos_fijos
						WHERE activo = 1
						AND id_empresa = $id_empresa
						AND id_sucursal = $id_sucursal
						AND id_bodega = $id_bodega
						AND id = $id_activo";
		$query = $mysql->query($sql,$mysql->link);

		$code_bar                           = $mysql->result($query,0,'code_bar');
		$id_empresa                         = $mysql->result($query,0,'id_empresa');
		$empresa                            = $mysql->result($query,0,'empresa');
		$id_sucursal                        = $mysql->result($query,0,'id_sucursal');
		$sucursal                           = $mysql->result($query,0,'sucursal');
		$id_bodega                          = $mysql->result($query,0,'id_bodega');
		$bodega                             = $mysql->result($query,0,'bodega');
		$id_familia                         = $mysql->result($query,0,'id_familia');
		$codigo_familia                     = $mysql->result($query,0,'codigo_familia');
		$familia                            = $mysql->result($query,0,'familia');
		$id_grupo                           = $mysql->result($query,0,'id_grupo');
		$codigo_grupo                       = $mysql->result($query,0,'codigo_grupo');
		$grupo                              = $mysql->result($query,0,'grupo');
		$id_subgrupo                        = $mysql->result($query,0,'id_subgrupo');
		$codigo_subgrupo                    = $mysql->result($query,0,'codigo_subgrupo');
		$subgrupo                           = $mysql->result($query,0,'subgrupo');
		$codigo_automatico                  = $mysql->result($query,0,'codigo_automatico');
		$codigo_activo                      = $mysql->result($query,0,'codigo_activo');
		$nombre_equipo                      = $mysql->result($query,0,'nombre_equipo');
		$tipo                               = $mysql->result($query,0,'tipo');
		$fecha_compra                       = $mysql->result($query,0,'fecha_compra');
		$id_documento_referencia            = $mysql->result($query,0,'id_documento_referencia');
		$documento_referencia               = $mysql->result($query,0,'documento_referencia');
		$documento_referencia_consecutivo   = $mysql->result($query,0,'documento_referencia_consecutivo');
		$id_documento_referencia_inventario = $mysql->result($query,0,'id_documento_referencia_inventario');
		$costo                              = $mysql->result($query,0,'costo');
		$costo_sin_depreciar_anual          = $mysql->result($query,0,'costo_sin_depreciar_anual');
		$id_centro_costos                   = $mysql->result($query,0,'id_centro_costos');
		$codigo_centro_costos               = $mysql->result($query,0,'codigo_centro_costos');
		$centro_costos                      = $mysql->result($query,0,'centro_costos');
		$fecha_vencimiento_garantia         = $mysql->result($query,0,'fecha_vencimiento_garantia');
		$id_proveedor                       = $mysql->result($query,0,'id_proveedor');
		$nit_proveedor                      = $mysql->result($query,0,'nit_proveedor');
		$proveedor                          = $mysql->result($query,0,'proveedor');
		$id_funcionario_asignado            = $mysql->result($query,0,'id_funcionario_asignado');
		$documento_funcionario_asignado     = $mysql->result($query,0,'documento_funcionario_asignado');
		$funcionario_asignado               = $mysql->result($query,0,'funcionario_asignado');
		$numero_serial                      = $mysql->result($query,0,'numero_serial');
		$numero_placa                       = $mysql->result($query,0,'numero_placa');
		$marca                              = $mysql->result($query,0,'marca');
		$modelo                             = $mysql->result($query,0,'modelo');
		$color                              = $mysql->result($query,0,'color');
		$chasis                             = $mysql->result($query,0,'chasis');
		$id_unidad                          = $mysql->result($query,0,'id_unidad');
		$unidad                             = $mysql->result($query,0,'unidad');
		$numero_piezas                      = $mysql->result($query,0,'numero_piezas');
		$longitud                           = $mysql->result($query,0,'longitud');
		$ancho                              = $mysql->result($query,0,'ancho');
		$alto                               = $mysql->result($query,0,'alto');
		$volumen                            = $mysql->result($query,0,'volumen');
		$peso                               = $mysql->result($query,0,'peso');
		$descripcion1                       = $mysql->result($query,0,'descripcion1');
		$descripcion2                       = $mysql->result($query,0,'descripcion2');
		$depreciable                        = $mysql->result($query,0,'depreciable');
		$fecha_inicio_depreciacion          = $mysql->result($query,0,'fecha_inicio_depreciacion');
		$vida_util                          = $mysql->result($query,0,'vida_util');
		$vida_util_restante                 = $mysql->result($query,0,'vida_util_restante');
		$valor_salvamento                   = $mysql->result($query,0,'valor_salvamento');
		$metodo_depreciacion_colgaap        = $mysql->result($query,0,'metodo_depreciacion_colgaap');
		$depreciacion_acumulada             = $mysql->result($query,0,'depreciacion_acumulada');
		$depreciable_niif                   = $mysql->result($query,0,'depreciable_niif');
		$fecha_inicio_depreciacion_niif     = $mysql->result($query,0,'fecha_inicio_depreciacion_niif');
		$vida_util_niif                     = $mysql->result($query,0,'vida_util_niif');
		$vida_util_niif_restante            = $mysql->result($query,0,'vida_util_niif_restante');
		$valor_salvamento_niif              = $mysql->result($query,0,'valor_salvamento_niif');
		$metodo_depreciacion_niif           = $mysql->result($query,0,'metodo_depreciacion_niif');
		$depreciacion_acumulada_niif        = $mysql->result($query,0,'depreciacion_acumulada_niif');
		$deteriorable                       = $mysql->result($query,0,'deteriorable');
		$deterioro_acumulado                = $mysql->result($query,0,'deterioro_acumulado');
		$fecha_baja                         = $mysql->result($query,0,'fecha_baja');
		$quien_elimino                      = $mysql->result($query,0,'quien_elimino');
		$observaciones_eliminacion          = $mysql->result($query,0,'observaciones_eliminacion');
		$usuario_elimino                    = $mysql->result($query,0,'usuario_elimino');
		$id_usuario_elimino                 = $mysql->result($query,0,'id_usuario_elimino');
		$id_usuario_creacion                = $mysql->result($query,0,'id_usuario_creacion');
		$id_usuario_compra                  = $mysql->result($query,0,'id_usuario_compra');
		$id_item                            = $mysql->result($query,0,'id_item');
		$id_saldo_inicial                   = $mysql->result($query,0,'id_saldo_inicial');
		$fecha_creacion                     = $mysql->result($query,0,'fecha_creacion');
		$tenencia                           = $mysql->result($query,0,'tenencia');
		$fecha_vencimiento_tenencia         = $mysql->result($query,0,'fecha_vencimiento_tenencia');
		$sincronizar_siip                   = $mysql->result($query,0,'sincronizar_siip');
		$estado                             = $mysql->result($query,0,'estado');
	}
	else{
		$classDisable = "disabled";
	}
?>
<div class="content">
	<div class="separator">FICHA TECNICA ACTIVO FIJO <div class="close" onclick="Win_ActivosFijos.close();"></div></div>
	<div class="content-tab">
		<div id="tab1" onclick="tab_event('panel1', this);">INFO. ACTIVO</div>
		<div id="tab2" onclick="tab_event('panel2', this);" class="<?php echo $classDisable; ?>">CONTABILIDAD</div>
		<div id="tab2" onclick="tab_event('panel3', this);" class="<?php echo $classDisable; ?>">DEPRECIACION</div>
		<div id="tab2" onclick="tab_event('panel4', this);" class="<?php echo $classDisable; ?>">DEPRECIACION NIIF</div>
		<div id="tab2" onclick="tab_event('panel5', this);" class="<?php echo $classDisable; ?>">DETERIORO</div>
	</div>
	<div class="content-tab-content">
		<div id="panel1" class="tab-content"><?php include 'informacion_basica.php'; ?></div>
		<div id="panel2" class="tab-content"><?php include 'contabilidad.php'; ?></div>
		<div id="panel3" class="tab-content"><?php include 'depreciacion.php'; ?></div>
		<div id="panel4" class="tab-content"><?php include 'depreciacion_niif.php'; ?></div>
		<div id="panel5" class="tab-content"><?php include 'deterioro_niif.php'; ?></div>
	</div>
</div>
<div id="loadForm" style="display:none;"></div>
<script>
	// INICIAR MOSTRANDO EL PRIMER TAB
	tab_event('panel1', document.getElementById('tab1'));

	// FUNCION DE LOS TABS
	function tab_event(idPanel, tab){
		if(tab.getAttribute('class') == 'disabled'){
			return;
		}

		var arrayPanel = document.querySelectorAll('.tab-content');
		[].forEach.call(arrayPanel, function(objDom) {
			objDom.style.display         = "none";
			objDom.style.backgroundColor = "none";
		});

		var arrayTabs = document.querySelectorAll('.content-tab > div');
		[].forEach.call(arrayTabs, function(objDom){
			objDom.style.marginTop       = "3px";
			objDom.style.borderBottom    = "1px solid #2A80B9";
			objDom.style.backgroundColor = "#80C3EF";
			objDom.style.color           = "#FFF";
		});

		document.getElementById(idPanel).style.display = "block";
		tab.style.marginTop       = "4px";
		tab.style.borderBottom    = "none";
		tab.style.backgroundColor = "#FFF";
		tab.style.color           = "#000";
	}

	function validate_int(Input){
    var patron = /[^\d]/g;
    if(patron.test(Input.value)){
			Input.value = (Input.value).replace(/[^0-9]/g,'');
		}
	}
</script>
