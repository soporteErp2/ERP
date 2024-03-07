<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include '../../funciones_globales/Clases/ClassRecursive/ClassRecursive.php';
	include 'bd/beauty_xml.php';
	header('Content-Type: text/html; charset=utf-8');
	$objRecursive = new ClassRecursive('expand');

	$id_empresa  = $_SESSION['EMPRESA'];
	$menus = array(
		'items'   => array(),
		'parents' => array()
	);

	$sql="SELECT codigo,nombre FROM informes_formatos WHERE id=$id_formato";
	$query=$mysql->query($sql);
	$codigo = $mysql->result($query,0,'codigo');
	$nombre = $mysql->result($query,0,'nombre');

	// CONSULTAR LAS SECCIONES DEL FORMATO
	$sql="SELECT
				id,
				codigo_seccion,
				id_formato,
				orden,
				nombre,
				totalizado,
				label_totalizado,
				formula_totalizado,
				codigo_seccion_padre
			FROM informes_formatos_secciones
			WHERE activo=1
			AND id_formato=$id_formato
			ORDER BY codigo_seccion_padre, orden ASC";
	$query=$mysql->query($sql,$mysql->link);
	while ($row=$mysql->fetch_array($query)) {
		// Create current menus item id into array
		$menus['items'][$row['id']] = $row;
		$menus['items'][$row['id']]['html'] = "<i onclick='agregarModificarSeccion(".$row['id'].")' class='material-icons' title='Editar' style='font-size:17px;' >edit</i>";
		// Creates list of all items with children
		$menus['parents'][$row['codigo_seccion_padre']][] = $row['id'];
	}

?>

<style>

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
			echo $objRecursive->createTreeView(0,$menus);
		 ?>
	 </div>
	<div id="loadForm" style="display:none;"></div>
</div>
<script>

	function agregarModificarSeccion(id) {
		var title = id > 0 ? "ACTUALIZAR FILA" : "AGREGAR FILA" ;
		Win_Ventana_Seccion = new Ext.Window({
		    width       : 400,
		    height      : 470,
		    id          : 'Win_Ventana_Seccion',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'informes_formatos/form_secciones_dinamicas.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					id_seccion : id,
					id_formato : '<?php echo $id_formato ?>'
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 5,
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
							text      : 'Cuentas de la fila',
							scale     : 'large',
							iconCls   : 'configurar_informe',
							iconAlign : 'top',
							hidden    : false,
							handler   : function(){ BloqBtn(this); cuentasSeccion() }
		                },
		                {
							xtype     : 'button',
							id        : 'conf_ccos',
							width     : 60,
							height    : 56,
							text      : 'Centro costos <br>de la fila',
							scale     : 'large',
							iconCls   : 'configurar_informe',
							iconAlign : 'top',
							hidden    : false,
							handler   : function(){ BloqBtn(this); ventana_centro_costos() }
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


</script>