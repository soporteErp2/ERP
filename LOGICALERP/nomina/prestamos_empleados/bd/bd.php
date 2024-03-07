<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("../config_var_global.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	switch ($opc) {
		case 'ventana_prestamo':
			ventana_prestamo($id,$id_empleado,$id_prestamo,$id_empresa,$link);
			break;
		case 'guardarPago':
			guardarPago($tipo_documento,$consecutivo_documento,$id_documento,$abono,$observacion,$id_empleado,$id_prestamo,$id_empresa,$link);
			break;
		case 'actualizarPago':
			actualizarPago($id,$tipo_documento,$consecutivo_documento,$id_documento,$abono,$observacion,$id_empleado,$id_prestamo,$id_empresa,$link);
			break;
		case 'eliminarPago':
			eliminarPago($id,$id_prestamo,$id_empresa,$link);
			break;
	}

	function ventana_prestamo($id,$id_empleado,$id_prestamo,$id_empresa,$link){
		$labelBtnGuardar= 'Guardar' ;
		$script='document.getElementById("abono").onkeypress = function(event){return validaNumeroPrestamo(event);};';
		if ($id>0) {
			$labelBtnGuardar='Actualizar';
			$sql="SELECT * FROM nomina_prestamos_empleados_pagos WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id=$id";
			$query=mysql_query($sql,$link);

			$id_documento          = mysql_result($query,0,'id_documento');
			$tipo_documento        = mysql_result($query,0,'tipo_documento');
			$consecutivo_documento = (mysql_result($query,0,'consecutivo_documento')>0)? mysql_result($query,0,'consecutivo_documento') : '' ;
			$valor                 = mysql_result($query,0,'valor');
			$observacion           = mysql_result($query,0,'observacion');
			$btn_eliminar = '{
								xtype		: "button",
								text		: "Eliminar",
								scale		: "large",
								iconCls		: "eliminar",
								iconAlign	: "top",
								handler 	: function(){BloqBtn(this); eliminarPago('.$id.');}
							},';
		}
		else{
			$id=0;
			// VERIFICAR QUE TENGA SALDO PENDIENTE EL PRESTAMO
			$sql="SELECT valor_prestamo_restante FROM nomina_prestamos_empleados WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_prestamo";
			$query=mysql_query($sql,$link);
			if (mysql_result($query,0,'valor_prestamo_restante')==0) {
				echo '<script>
						alert("Aviso!\nEl prestamo esta totalmente pagado!");
						Win_Ventana_ventana_prestamo.close();
					</script>';
				exit;
			}
		}

		echo '
			<style>
			 	.divContenedor{
					width      : 100%;
					height     : 100%;
			 	}

			 	.fila{
			 		width: 90%;
			 		height: 25px;
			 		margin-top: 8px;
			 		float: left;
			 	}
			 	.fila>div{
			 		width: 49%;
			 		height: 100%;
			 		float: left;

			 	}
			 	.fila+div{
			 		text-indent:15px;
			 	}
		 </style>

		 <div class="divContenedor">
		 	<div id="toolbar_correo" style="height:85px;"></div>
		 	<div id="divLoad"></div>
			<div class="fila">
				<div style="text-indent: 15px;">Tipo de Documento</div>
				<div style="text-indent: 15px;">
					<select id="tipo_documento" class="myfield">
						<option value="RC">Recibo de Caja</option>
						<option value="NC">Nota Contable</option>
					</select>
				</div>
			</div>

			<div class="fila">
				<div>Consecutivo Documento</div>
				<div>
					<input type="text" id="consecutivo_documento" class="myfieldObligatorio" style="width:110px;float:left;margin-left:15px;" value="'.$consecutivo_documento.'" readonly>
					<div onclick="ventanaBuscardocumentoCruce()" style="width:20px;height:20px;float:left;cursor:pointer;background-image:url(img/buscar20.png);background-color:#F3F3F3;border:1px solid #D4D4D4;" title="Buscar Documento Cruce"></div>
					<input type="hidden" id="id_documento" value="'.$id_documento.'">
				</div>

			</div>

			<div class="fila">
				<div>Valor a Pagar</div>
				<div> <input type="text" id="abono" class="myfieldObligatorio" value="'.$valor.'"></div>
			</div>
			<div class="fila">
				<div>Observacion</div>
				<div><textarea id="observacion" class="myfield" cols="4" rows="10">'.$observacion.'</textarea></div>
			</div>

		 </div>

		 <script>
		 	document.getElementById("abono").onkeypress = function(event){return validaNumeroPrestamo(event);};
		 	new Ext.Panel
			(
				{
					renderTo	:"toolbar_correo",
					frame		:false,
					border		:false,
					tbar		:
					[
						{
							xtype	: "buttongroup",
							columns	: 3,
							title	: "Opciones",
							items	:
							[
								{
									xtype		: "button",
									text		: "'.$labelBtnGuardar.'",
									scale		: "large",
									iconCls		: "guardar",
									iconAlign	: "top",
									handler 	: function(){BloqBtn(this); guardarActualizarPago('.$id.');}
								},
								'.$btn_eliminar.'

							]
						}
					]
				}
			);

			function validaNumeroPrestamo(e){
				tecla = (document.all)?e.keyCode:e.which;
				if (tecla==8 		//BACKSPACE
				 	|| tecla==9 	//TAB
				 	|| tecla==0 	//TAB
				 	|| tecla==13 	//ENTER
				 	|| tecla==46 	//.
				 	) return true;
				patron = /\d/;
				te = String.fromCharCode(tecla);
				return patron.test(te);
			}
		 </script>
			';
	}

	function guardarPago($tipo_documento,$consecutivo_documento,$id_documento,$abono,$observacion,$id_empleado,$id_prestamo,$id_empresa,$link){
		$sql="INSERT INTO nomina_prestamos_empleados_pagos
				(id_documento,tipo_documento,consecutivo_documento,valor,observacion,id_empleado,id_prestamo,id_empresa)
				VALUES
				(
					'$id_documento',
					'$tipo_documento',
					'$consecutivo_documento',
					'$abono',
					'$observacion',
					'$id_empleado',
					'$id_prestamo',
					'$id_empresa'
				)";
		$query=mysql_query($sql,$link);
		if ($query) {
			$id=mysql_insert_id();
			// DESCONTAR EL VALOR DEL SALDO RESTANTE DEL PRESTAMO
			$resul=actualizaSaldoPrestamo('eliminar',$abono,$id_prestamo,$id_empresa,$link);
			if ($resul=='false') {
				echo 'Error!\nNo se desconto el valor del prestamo\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema';
				$sql="DELETE FROM nomina_prestamos_empleados_pagos WHERE id=$id";
				$query=mysql_query($sql,$link);
			}
			else{
				echo '<script>
						Actualiza_Div_nomina_prestamos_empleados('.$id_prestamo.');
						Inserta_Div_nomina_prestamos_empleados_pagos('.$id.');
						Win_Ventana_ventana_prestamo.close();
					</script> ';
			}

		}
		else{
			echo '<script>alert("Error!\nNo se registro el pago!\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function actualizarPago($id,$tipo_documento,$consecutivo_documento,$id_documento,$abono,$observacion,$id_empleado,$id_prestamo,$id_empresa,$link){
		$sql="SELECT * FROM nomina_prestamos_empleados_pagos WHERE activo=1 AND id_empresa=$id_empresa AND id_prestamo=$id_prestamo AND id=$id";
		$query=mysql_query($sql,$link);
		$abono_anterior                 = mysql_result($query,0,'valor');
		$id_documento_anterior          = mysql_result($query,0,'id_documento');
		$tipo_documento_anterior        = mysql_result($query,0,'tipo_documento');
		$consecutivo_documento_anterior = mysql_result($query,0,'consecutivo_documento');
		$observacion_anterior           = mysql_result($query,0,'observacion');

		$resul=actualizaSaldoPrestamo('agregar',$abono_anterior,$id_prestamo,$id_empresa,$link);
		if ($resul=='true') {
			$sql="UPDATE nomina_prestamos_empleados_pagos
					SET
						id_documento          = '$id_documento',
						tipo_documento        = '$tipo_documento',
						consecutivo_documento = '$consecutivo_documento',
						valor                 = '$abono',
						observacion           = '$observacion'
					WHERE
						activo=1
						AND id_empresa  = $id_empresa
						AND id_empleado = $id_empleado
						AND id          = $id
					";
			$query=mysql_query($sql,$link);
			if ($query) {
				$resul=actualizaSaldoPrestamo('eliminar',$abono,$id_prestamo,$id_empresa,$link);
				if ($resul=='true') {
					echo '<script>
							Actualiza_Div_nomina_prestamos_empleados('.$id_prestamo.');
							Actualiza_Div_nomina_prestamos_empleados_pagos('.$id.');
							Win_Ventana_ventana_prestamo.close();
						</script>';
				}
				else{
					$sql="UPDATE nomina_prestamos_empleados_pagos
							SET
								id_documento          = '$id_documento_anterior',
								tipo_documento        = '$tipo_documento_anterior',
								consecutivo_documento = '$consecutivo_documento_anterior',
								valor                 = '$abono_anterior',
								observacion           = '$observacion_anterior'
							WHERE
								activo=1
								AND id_empresa  = $id_empresa
								AND id_empleado = $id_empleado
								AND id          = $id
							";
					$query=mysql_query($sql,$link);
				}
			}
			else{
				$resul=actualizaSaldoPrestamo('eliminar',$abono_anterior,$id_prestamo,$id_empresa,$link);
			}

		}
		else{
			echo '<script>alert("No se actualizo el saldo del prestamo\nIntentelo de nuevo si el problema continua comuniquese con el administrador del sistema");</script>';
		}
	}

	function eliminarPago($id,$id_prestamo,$id_empresa,$link){
		$sql="SELECT * FROM nomina_prestamos_empleados_pagos WHERE activo=1 AND id_empresa=$id_empresa AND id_prestamo=$id_prestamo AND id=$id";
		$query = mysql_query($sql,$link);
		$abono = mysql_result($query,0,'valor');
		$resul = actualizaSaldoPrestamo('agregar',$abono,$id_prestamo,$id_empresa,$link);
		if ($resul=='true') {
			$sql="DELETE FROM nomina_prestamos_empleados_pagos WHERE activo=1 AND id_empresa=$id_empresa AND id_prestamo=$id_prestamo AND id=$id";
			$query=mysql_query($sql,$link);
			echo '<script>
					document.getElementById("item_nomina_prestamos_empleados_pagos_'.$id.'").parentNode.removeChild(document.getElementById("item_nomina_prestamos_empleados_pagos_'.$id.'"));
					Actualiza_Div_nomina_prestamos_empleados('.$id_prestamo.');
					Win_Ventana_ventana_prestamo.close();
				</script>';
		}
		else{
			echo '<script>alert("No se actualizo el saldo del prestamo!");</script>';
		}
	}

	function actualizaSaldoPrestamo($accion,$abono,$id_prestamo,$id_empresa,$link){
		if ($accion=='agregar') {
			$sql="UPDATE nomina_prestamos_empleados
					SET
					valor_prestamo_restante=IF(valor_prestamo_restante+$abono<0,0,valor_prestamo_restante+$abono),
					cuotas_restantes=IF(cuotas_restantes+1>cuotas,cuotas,cuotas_restantes+1)
					WHERE activo=1 AND id=$id_prestamo AND id_empresa=$id_empresa";
		}
		else if ($accion=='eliminar') {
			$sql="UPDATE nomina_prestamos_empleados
					SET
					valor_prestamo_restante=IF(valor_prestamo_restante-$abono<0,0,valor_prestamo_restante-$abono),
					cuotas_restantes=IF(cuotas_restantes-1<0,0,cuotas_restantes-1)
					WHERE activo=1 AND id=$id_prestamo AND id_empresa=$id_empresa";

		}

		$query=mysql_query($sql,$link);
		if ($query){
			return 'true';
		}
		else{
			return 'false';
		}

	}

?>