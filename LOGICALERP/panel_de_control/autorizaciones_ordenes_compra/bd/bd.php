<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	switch($opc){
		case 'ventana_insert_update':
			ventana_insert_update($id,$accion,$id_empresa,$mysql);
			break;
		case 'guardarRango':
			guardarRango($rango_inicial,$rango_final,$id_empresa,$mysql);
			break;
		case 'actualizarRango':
			actualizarRango($id,$rango_inicial,$rango_final,$id_empresa,$mysql);
			break;
		case 'delete_registro':
			delete_registro($id,$id_empresa,$mysql);
			break;
	}

	function ventana_insert_update($id,$accion,$id_empresa,$mysql){
		$hidden = 'true';

		if ($accion=='update') {
			$text_btn='Actualizar';
			$sql   = "SELECT rango_inicial,rango_final FROM rango_autorizaciones_ordenes_compra WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
			$query = $mysql->query($sql,$mysql->link);
			$rango_inicial = $mysql->result($query,0,'rango_inicial');
			$rango_final   = $mysql->result($query,0,'rango_final');
			$hidden = 'false';

		}
		else if ($accion=='insert') {
			$text_btn='Guardar';
		}

		echo '<style>
				.contenedor{
					width      : 100%;
					height     : 100%;
					text-align :center;
				}
				.contenedor table{
					margin     : 12px auto;
					text-align : left;
					font-size  : 12px;
				}
				.contenedor table td{
					padding: 5px 20px 0px 0px;
				}

				.divLoad{
					width: 20px;
					height: 20px;
					position : absolute;
					overflow: hidden;
				}

			</style>
			<div class="contenedor">
				<div class="divLoad" id="divLoad"></div>
				<table>
					<tr>
						<td>Rango Inicial</td>
						<td><input type="text" onkeypress="return isNumberKey(event)" id="rango_inicial" class="myfieldObligatorio" value="'.$rango_inicial.'"></td>
					</tr>
					<tr>
						<td>Rango Final</td>
						<td><input type="text" onkeypress="return isNumberKey(event)" id="rango_final" class="myfieldObligatorio" value="'.$rango_final.'"></td>
					</tr>
				</table>
				<input type="hidden" id="id_registro" value="'.$id.'">
			</div>

			<script>
				var toolbar = Ext.getCmp("Win_Ventana_insert_update").getTopToolbar();
				toolbar.add({
					xtype   : "buttongroup",
					columns : 3,
					title   : "Opciones",
					items   :
					[
						{
							text      : "'.$text_btn.'",
							id        : "btn_save_rango",
							scale     : "large",
							iconCls   : "guardar",
							iconAlign : "top",
							handler   : function(){ insert_update("'.$accion.'","'.$id.'"); }
						},
						{
							text      : "Eliminar",
							id        : "btn_delete_rango",
							scale     : "large",
							iconCls   : "cancel",
							hidden    : '.$hidden.',
							iconAlign : "top",
							handler   : function(){ Ext.MessageBox.confirm(\'Advertencia\', \'Realmente desea eliminar el registro ?\', function (id, value){
															if (id === \'yes\') {
																Ext.getCmp("btn_delete_rango").disable();
																delete_registro('.$id.');
															}
														}, this);
													}
						},
						{
							text      : "Regresar",
							scale     : "large",
							iconCls   : "regresar",
							iconAlign : "top",
							handler   : function(){ BloqBtn(this); Win_Ventana_insert_update.close(id); }
						},

					]
				});
				toolbar.doLayout();
				function isNumberKey(evt){
					var charCode = (evt.which) ? evt.which : event.keyCode
					if (charCode > 31 && (charCode < 48 || charCode > 57))
					return false;

					return true;
		      	}
      		</script>

			';
	}

	function guardarRango($rango_inicial,$rango_final,$id_empresa,$mysql){
		$sql   = "SELECT COUNT(id) AS cont FROM rango_autorizaciones_ordenes_compra WHERE activo=1 AND id_empresa=$id_empresa AND (rango_inicial>=$rango_inicial AND rango_final<=$rango_final)";
		$query = $mysql->query($sql,$mysql->link);
		$cont = $mysql->result($query,0,'cont');
		if ($cont>0) {
			echo '<script>
					Ext.MessageBox.alert("Validacion","Ya existe '.$cont.' registro(s) con ese rango<br> seleccione otros e intentelo de nuevo ");
					Ext.getCmp("btn_save_rango").enable();
				</script>';
			exit;
		}

		$sql   = "INSERT INTO rango_autorizaciones_ordenes_compra (rango_inicial,rango_final,id_empresa) VALUES($rango_inicial,$rango_final,$id_empresa)";
		$query = $mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>
					alert("Error\nNo se guardo el registro, intentelo de nuevo, si el problema persiste comuniquese con el administrador del sistema");
					Ext.getCmp("btn_save_rango").enable();
				</script>';
		}
		else{
			$id = mysql_insert_id();
			echo '<script>
					Inserta_Div_rango_autorizaciones_ordenes_compra('.$id.');
					Win_Ventana_insert_update.close(id);
				</script>';
		}
	}

	function actualizarRango($id,$rango_inicial,$rango_final,$id_empresa,$mysql){
		$sql   = "SELECT COUNT(id) AS cont
					FROM rango_autorizaciones_ordenes_compra
					WHERE activo=1
					AND id<>$id
					AND id_empresa=$id_empresa
					AND (rango_inicial>=$rango_inicial
					AND rango_final<=$rango_final)";
		$query = $mysql->query($sql,$mysql->link);
		$cont = $mysql->result($query,0,'cont');
		if ($cont>0) {
			echo '<script>
					Ext.MessageBox.alert("Validacion","Ya existe '.$cont.' registro(s) con ese rango<br> seleccione otros e intentelo de nuevo ");
					Ext.getCmp("btn_save_rango").enable();
				</script>';
			exit;
		}

		$sql   = "UPDATE rango_autorizaciones_ordenes_compra SET rango_inicial=$rango_inicial,rango_final=$rango_final WHERE activo=1 AND id_empresa=$id_empresa AND id=$id";
		$query = $mysql->query($sql,$mysql->link);
		if (!$query) {
			echo '<script>
					alert("Error\nNo se Actualizar el registro, intentelo de nuevo, si el problema persiste comuniquese con el administrador del sistema");
					Ext.getCmp("btn_save_rango").enable();
				</script>';
		}
		else{
			echo '<script>
					Actualiza_Div_rango_autorizaciones_ordenes_compra('.$id.');
					Win_Ventana_insert_update.close(id);
				</script>';
		}
	}

	function delete_registro($id,$id_empresa,$mysql){
		$sqlRango   = "UPDATE rango_autorizaciones_ordenes_compra SET activo = 0 WHERE id = $id AND id_empresa = $id_empresa";
		$queryRango = $mysql->query($sqlRango,$mysql->link);

		$sqlCosto = "UPDATE costo_autorizadores_ordenes_compra SET activo = 0 WHERE id_rango = $id AND id_empresa = $id_empresa";
		$queryCosto = $mysql->query($sqlCosto,$mysql->link);

		if(!$queryRango && !$queryCosto){
			echo '<script>
							alert("Error\nNo se Eliminar el registro, intentelo de nuevo, si el problema persiste comuniquese con el administrador del sistema");
							Ext.getCmp("btn_delete_rango").enable();
						</script>';
		}
		else{
			echo '<script>
							Elimina_Div_rango_autorizaciones_ordenes_compra('.$id.');
							Win_Ventana_insert_update.close(id);
						</script>';
		}
	}
?>
