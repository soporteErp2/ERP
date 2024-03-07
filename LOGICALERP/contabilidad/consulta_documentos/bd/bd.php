<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	switch ($opc) {
		case 'ventana_buscar_documento_cruce':
			ventana_buscar_documento_cruce($opcGrillaContable,$id_empresa,$id_sucursal,$link);
			break;

		case 'log_documento':
			log_documento($id_documento,$tipo_documento,$id_empresa,$link);
			break;

	}

	//======================== BUSCAR LA CUENTA CUANDO SE DIGITA EN LA VENTANA EDICION ==================//
	function log_documento($id_documento,$tipo_documento,$id_empresa,$link){
		switch ($tipo_documento) {
			case 'FC':
				$descripcion='Factura de Compra';
				$whereDescripcion = " OR descripcion='Factura de Compra por Cuentas' ";
			break;

			case 'CE':
				$descripcion='Comprobante de Egreso';
			break;

			case 'RV':
				$descripcion='Remision de Venta';
			break;

			case 'FV':
				$descripcion='Factura de Venta';
			break;

			case 'RC':
				$descripcion='Recibo de Caja';
			break;

			case 'LN':
				$descripcion='Planilla de Nomina';

			case 'LE':
				$descripcion='Planilla de Liquidacion';
			break;

			case 'PA':
				$descripcion='Planilla Ajuste de Nomina';
			break;

			case 'NCG':
				$descripcion='Nota Contable General';
			break;

		}

		$sql="SELECT * FROM log_documentos_contables WHERE id_empresa=$id_empresa AND id_documento=$id_documento AND( descripcion='$descripcion' $whereDescripcion ) ";
		$query=mysql_query($sql,$link);
		$whereIdEmpleados='';
		while ($row=mysql_fetch_array($query)) {
			$whereIdEmpleados.=($whereIdEmpleados=='')? 'id='.$row['id_usuario'] : ' OR id='.$row['id_usuario']  ;
			$arrayLog[] = array(
								'fecha'       => $row['fecha'],
								'hora'        => $row['hora'],
								'actividad'   => $row['actividad'],
								'sucursal'    => $row['sucursal'],
								'descripcion' => $row['descripcion'],
								'id_usuario'  => $row['id_usuario'],
								);
		}

		$sql="SELECT id,nombre FROM empleados WHERE activo=1 AND id_empresa=$id_empresa  AND ($whereIdEmpleados)";
		$query=mysql_query($sql,$link);
		while ($row=mysql_fetch_array($query)) {
			$arrayUsuario[$row['id']]=$row['nombre'];
		}

		foreach ($arrayLog as $key => $arrayLogResul) {
			$bodyTable.='<tr>
							<td>'.$arrayLogResul['sucursal'].'</td>
							<td>'.$arrayLogResul['fecha'].'</td>
							<td>'.$arrayLogResul['hora'].'</td>
							<td>'.$arrayLogResul['actividad'].'</td>
							<td>'.$arrayUsuario[$arrayLogResul['id_usuario']].'</td>
						</tr>';
		}

		echo '<style>
					.contenedorLog{
						width      : 100%;
						height     : 100%;
						background-color: #FFF;
						padding-top: 20px;
						// overflow : auto;
					}

					.tableLog{
						width           : 95%;
						font-size       : 12px;
						border-collapse : collapse;
					}

					.tableLog thead{
				        height           : 32px;
				        font-weight      : bold;
				        background-color : #999;
				        color            : #FFF;
				        font-size        : 12px;
				        margin           : 5px 25px;
				        line-height      : 2.5;
				        text-indent      : 15px;
					}

					.tableLog tr{
						height : 25px;
    					border : 1px solid #EEE;
					}

					.tableLog td{
						border:1px solid #999;
						text-indent: 5px;
					}

				</style>

			<div class="contenedorLog">
				<div style="height: 250px;overflow: auto;">
					<table class="tableLog" align="center">
						<thead>
							<tr>
								<td>SUCURSAL</td>
								<td>FECHA</td>
								<td>HORA</td>
								<td>EVENTO</td>
								<td>USUARIO</td>
							</tr>
						</thead>
						<tbody>
							'.$bodyTable.'
						</tbody>
					</table>
				</div>
			</div>';
	}


 	function ventana_buscar_documento_cruce($opcGrillaContable,$id_empresa,$id_sucursal,$link){
 		echo'
    		<select id="filtro_tipo_documento" style="width:135px;float: left;margin: 8px 5px;" onChange="carga_filtro_tipo_documento(this.value)">
                <optgroup label="Compras">
                    <option value="FC">FC - Factura</option>
                    <option value="CE">CE - Comprobante Egreso</option>
                </optgroup>
                <optgroup label="Ventas">
                    <option value="RV">RV - Remision</option>
                    <option value="FV">FV - Factura</option>
                    <option value="RC">RC - Recibo Caja</option>
                </optgroup>
                <optgroup label="Nomina">
                    <option value="LN">LN - Planilla de Nomina</option>
                    <option value="LE">LE - Planilla de Liquidacion</option>
                    <option value="PA">PA - Planilla de Ajuste</option>
                </optgroup>
                <optgroup label="Contabilidad">
                    <option value="NCG">NCG - Nota Contable General</option>
                </optgroup>
            </select>;
    		<script>
				function carga_filtro_tipo_documento(tipo_documento_cruce){
					if (document.getElementById("filtro_sucursal_")) {
						filtro_sucursal=document.getElementById("filtro_sucursal_").value;
					}
					else{
						filtro_sucursal="'.$id_sucursal.'";
					}
					Ext.get("contenedor_Win_Panel_Global").load({
						url     : "consulta_documentos/consulta_documentos.php",
						scripts : true,
						nocache : true,
						params  :
						{
							opc                  : "'.$opc.'",
							filtro_sucursal      : filtro_sucursal,
							tipo_documento_cruce : tipo_documento_cruce,
							opcGrillaContable    : "'.$opcGrillaContable.'",
						}
					});
				}
				// carga_filtro_tipo_documento();
			</script>';
 	}

?>