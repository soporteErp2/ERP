<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include 'bd/beauty_xml.php';
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];
	// CONSULTAR LAS SECCIONES DEL FORMATO
	$sql="SELECT
				id,
				codigo_seccion,
				id_formato,
				orden,
				nombre,
				tipo,
				descripcion_tipo,
				totalizado,
				label_totalizado,
				formula_totalizado,
				codigo_seccion_padre
			FROM informes_niif_formatos_secciones WHERE activo=1 AND id_formato=$id_formato ORDER BY codigo_seccion_padre, orden ASC";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$codigo_seccion       = $row['codigo_seccion'];
		$codigo_seccion_padre = $row['codigo_seccion_padre'];
		$firstNode = ($firstNode=='')? $codigo_seccion : $firstNode ;
		$arraySecciones['child'][$codigo_seccion] = array(
															'id'                   =>$row['id'],
															'codigo_seccion'       =>$codigo_seccion,
															'id_formato'           =>$row['id_formato'],
															'orden'                =>$row['orden'],
															'nombre'               =>$row['nombre'],
															'tipo'                 =>$row['tipo'],
															'descripcion_tipo'     =>$row['descripcion_tipo'],
															'totalizado'           =>$row['totalizado'],
															'label_totalizado'     =>$row['label_totalizado'],
															'formula_totalizado'   =>$row['formula_totalizado'],
															'codigo_seccion_padre' =>$row['codigo_seccion_padre'],
															);

		$arraySecciones['parent'][$codigo_seccion_padre][] = $codigo_seccion;
	}

	// CONSULTAR LAS FILAS DE LAS SECCIONES DEL FORMATO
	$sql="SELECT
				id,
				id_seccion,
				codigo,
				orden,
				nombre,
				naturaleza,
				formula
			FROM informes_niif_formatos_secciones_filas WHERE activo=1 AND id_empresa=$id_empresa AND id_formato=$id_formato ORDER BY orden ASC";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		$id_seccion = $row['id_seccion'];
		$arrayFilas[$id_seccion][] = array(
											'id'         => $row['id'],
											'codigo'     => $row['codigo'] ,
											'orden'      => $row['orden'] ,
											'nombre'     => $row['nombre'] ,
											'naturaleza' => $row['naturaleza'],
											'formula'    => $row['formula'],
										);
	}
	// print_r($arrayFilas);
	// echo setFilasSecciones(8);
	// VISUALIZAR EL REPORTE EN FORMA DE ARBOL
	function createTreeView($codigo_seccion_padre,$arraySecciones){
		$body        = "";
		$label_totalizado = "";
		// SI EXISTE LA SECCION PADRE
		if (isset($arraySecciones['parent'][$codigo_seccion_padre]) ) {
			$body .= "<div class='tree'>";
			foreach ($arraySecciones['parent'][$codigo_seccion_padre] as $codigo_seccion) {
				$label_totalizado = "";
				if(!isset($arraySecciones['child'][$codigo_seccion])) {
					$body .= "<div class='hover' ondblclick='agregarModificarSeccion(".$codigo_seccion.")' ><b>$codigo_seccion</b> - ".$arraySecciones['child'][$codigo_seccion]['nombre']." </div>";
					// if ($arraySecciones['child'][$codigo_seccion_padre]['totalizado']=='true') {
					// 	$label_totalizado = ($arraySecciones['child'][$codigo_seccion_padre]['label_totalizado']<>'')? $arraySecciones['child'][$codigo_seccion_padre]['label_totalizado'] : 'Total '.$arraySecciones['child'][$codigo_seccion_padre]['nombre'] ;
					// 	$body .= "<div class='total_seccion'>$label_totalizado</div>";
					// }
				}

				if(isset($arraySecciones['child'][$codigo_seccion])) {
					$body .= "<div class='hover' ondblclick='agregarModificarSeccion(".$codigo_seccion.")'><b>$codigo_seccion</b> - <b>".$arraySecciones['child'][$codigo_seccion]['nombre']." </b></div>".setFilasSecciones($codigo_seccion);
					$body .= createTreeView($codigo_seccion, $arraySecciones);
					// $body .= ;
					if ($arraySecciones['child'][$codigo_seccion]['totalizado']=='true' && $codigo_seccion_padre<>$codigo_seccion ) {
						$label_totalizado = ($arraySecciones['child'][$codigo_seccion]['label_totalizado']<>'')? $arraySecciones['child'][$codigo_seccion]['label_totalizado'] : 'Total '.$arraySecciones['child'][$codigo_seccion]['nombre'] ;
						$body .= "<div class='hover'  ><b>$label_totalizado</b></div>";
					}
				}
			}

			$body .= "</div>";

		}
		return $body;
	}

	function setFilasSecciones($id_seccion){
		global $arrayFilas;
		$filas = "";
		foreach ($arrayFilas[$id_seccion] as $key => $arrayResult) {
			$labelFormula = ($arrayResult['formula']<>'')? "(Calculado por formula)" : "" ;
			$filas .= "<div style='margin-left: 30px; !important;' class='hover' ondblclick='agregarModificarFila($arrayResult[id],$id_seccion)' ><b>$arrayResult[codigo]</b> - $arrayResult[nombre] <b><i>($arrayResult[naturaleza]) $labelFormula</i></b></div>";
		}
		return $filas;
	}

?>

<style>
	img{
		cursor: pointer;
	}

	.tree{
		margin-left: 10px;
	}

	.tree > div{
		margin-left: 15px;
		    padding: 2px 0px 2px 0px;
	}

	.hover:hover{
		cursor:hand;
		background-color: #d4d4d4;
	}

	.total_seccion{
		margin-left: 0px !important;
	}
</style>
<div class="content" id="form_secciones" >
	<table class="table-form" style="width:calc(100% - 10px);" >
		<tbody>
			<tr class="thead">
				<td colspan="2"><?php echo $codigo." - ".utf8_encode($nombre); ?></td>
			</tr>
		</tbody>
	</table>
	<div class="table-form" style="padding-bottom: 20px;">
		<?php
			echo createTreeView(0,$arraySecciones);
		 ?>
	 </div>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	function agregarModificarSeccion(id) {
		var title = id > 0 ? "ACTUALIZAR SECCION" : "AGREGAR SECCION" ;
		Win_Ventana_Seccion = new Ext.Window({
		    width       : 400,
		    height      : 520,
		    id          : 'Win_Ventana_Seccion',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'configuracion_informes_niif/form_secciones.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					codigo_seccion : id,
					id_formato     : '<?php echo $id_formato ?>'
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 4,
		            title   : 'Opciones',
		            style   : 'border-right:none;',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Guardar',
		                    scale       : 'large',
		                    iconCls     : 'guardar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); guardarActualizarSeccion() }
		                },
		                {
							xtype     : 'button',
							id        : 'conf_cuentas',
							width     : 60,
							height    : 56,
							text      : 'Cuentas de la seccion',
							scale     : 'large',
							iconCls   : 'configurar_informe',
							iconAlign : 'top',
							hidden    : false,
							handler   : function(){ BloqBtn(this); cuentasSeccion() }
		                },
		                {
							xtype     : 'button',
							id        : 'btn_eliminar',
							width     : 60,
							height    : 56,
							text      : 'Eliminar',
							scale     : 'large',
							iconCls   : 'eliminar',
							iconAlign : 'top',
							hidden    : false,
							handler   : function(){ BloqBtn(this); eliminarSeccion() }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_Seccion.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

	function agregarModificarFila(id,id_seccion) {
		var title = id > 0 ? "ACTUALIZAR FILA" : "AGREGAR FILA" ;
		Win_Ventana_fila = new Ext.Window({
		    width       : 400,
		    height      : 380,
		    id          : 'Win_Ventana_fila',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'configuracion_informes_niif/form_filas.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					id_fila    : id,
					id_seccion : id_seccion,
					id_formato : '<?php echo $id_formato ?>'
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 4,
		            title   : 'Opciones',
		            style   : 'border-right:none;',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Guardar',
		                    scale       : 'large',
		                    iconCls     : 'guardar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); guardarActualizarFila() }
		                },
		                {
							xtype     : 'button',
							id        : 'conf_cuentas',
							width     : 60,
							height    : 56,
							text      : 'Cuentas de la Fila',
							scale     : 'large',
							iconCls   : 'configurar_informe',
							iconAlign : 'top',
							hidden    : false,
							handler   : function(){ BloqBtn(this); cuentasFila() }
		                },
		                {
							xtype     : 'button',
							id        : 'btn_eliminar',
							width     : 60,
							height    : 56,
							text      : 'Eliminar',
							scale     : 'large',
							iconCls   : 'eliminar',
							iconAlign : 'top',
							hidden    : false,
							handler   : function(){ BloqBtn(this); Win_Ventana_fila.close(id) }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_fila.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

</script>