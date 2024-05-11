<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	// include 'bd/beauty_xml.php';
	header('Content-Type: text/html; charset=utf-8');

	$id_empresa  = $_SESSION['EMPRESA'];
	// CONSULTAR LAS SECCIONES DEL FORMATO
	$sql="SELECT
				id,
				orden,
				nombre,
				id_padre,
				padding,
				restaurante
			FROM ventas_pos_secciones WHERE activo=1 AND id_empresa=$id_empresa ORDER BY id, id_padre ASC";
	$query=$mysql->query($sql);
	while ($row=$mysql->fetch_array($query)) {
		$firstNode = ($firstNode=='')? $row['id'] : $firstNode ;
		$arraySecciones['child'][$row['id']] = array(
													'id'          => $row['id'],
													'orden'       => $row['orden'],
													'nombre'      => $row['nombre'],
													'id_padre'    => $row['id_padre'],
													'padding'     => $row['padding'],
													'restaurante' => $row['restaurante'],
													);

		$arraySecciones['parent'][$row['id_padre']][] = $row['id'];
	}
	// var_dump($arraySecciones);
	// print_r($arraySecciones);
	// echo setFilasSecciones(8);
	// VISUALIZAR EL REPORTE EN FORMA DE ARBOL
	function createTreeView($id_padre,$arraySecciones){
		$body        = "";
		$label_totalizado = "";
		// SI EXISTE LA SECCION PADRE
		if (isset($arraySecciones['parent']) ) {
			$body .= "<div class='tree'>";
			foreach ($arraySecciones['parent'][$id_padre] as $id_seccion) {
				// var_dump($arraySecciones['parent'][$id_padre]);
				$label_totalizado = "";
				if(!isset($arraySecciones['child'][$id_seccion])) {
					$body .= "<div class='hover' ondblclick='agregarModificarSeccion(".$id_seccion.")' >
									 ".$arraySecciones['child'][$id_seccion]['nombre']."
								</div>";
				}

				if(isset($arraySecciones['child'][$id_seccion])) {
					$labelNombre = $arraySecciones['child'][$id_seccion]['nombre'];
					if ($arraySecciones['child'][$id_seccion]['restaurante']=='Si') {
						$labelNombre = "<b>".$arraySecciones['child'][$id_seccion]['nombre']."</b> <img onclick='items_by_section(".$arraySecciones['child'][$id_seccion]['id'].")' title='Inventario para la seccion' src='../../temas/clasico/images/BotonesTabs/inventario16.png' >";
					}

					$body .= "<div class='hover' ondblclick='agregarModificarSeccion($id_seccion)'>
								 $labelNombre
							</div>";
					$body .= createTreeView($id_seccion, $arraySecciones);
				}
			}

			$body .= "</div>";

		}
		return $body;
	}

?>

<style>
	.content-sections{
		height: 100%;
		width: 100%;
		display: flex;
    	justify-content: center;
		background-color:FFF;
	}
	img{
		cursor: pointer;
	}

	.tree{
		margin-left: 10px;
	}

	.tree > div{
		/* margin-left: 15px; */
		padding: 2px 0px 2px 0px;
	}

	.hover:hover{
		cursor:hand;
		background-color: #d4d4d4;
	}

	.total_seccion{
		margin-left: 0px !important;
	}

	.content-sections .table-form {
		width : 90%;
		margin : 0;
		border: 1px solid #a2a2a2;
		height: 350px;
		overflow: hidden;
		margin-top : 10px;
	}

	.content-sections .table-form .hover {
		display :  flex;
		justify-content: space-between;
    	align-items: center;
		padding: 5px 10px 5px 5px;
	}

	.content-sections .table-form .title {
		cursor: default; 
		font-size: 14px;
		background-color: #a2a2a2;
		color : FFF;
		font-family: arial, sans-serif;
	}

</style>
<div id="toolbar_secciones"></div>
<div class="content-sections" id="form_secciones" >

	<div class="table-form" >
		<div class='hover title'  >
			<span>SECCION</span>
			<span>INVENTARIO</span>
		</div>
		<?php
			echo createTreeView(0,$arraySecciones);
		 ?>
	 </div>
	
	 <div id="loadForm" style="display:none;"></div>
</div>
<script>
	if (!document.getElementById('tbar_secciones')) {
		new Ext.Panel
		(
			{
				renderTo :'toolbar_secciones',
				id       : "tbar_secciones",
				frame    :false,
				border   :false,
				tbar     :
				[
					{
						xtype   : 'buttongroup',
						columns : 3,
						title   : 'Opciones',
						items   :
						[
							{
								xtype     : 'button',
								text      : 'Nueva Seccion',
								scale     : 'large',
								iconCls   : 'addsucursal',
								iconAlign : 'top',
								handler   : function(){BloqBtn(this); agregarModificarSeccion();}
							},
						]
					}
				]
			}
		);
	}

	function agregarModificarSeccion(id) {
		var title = id > 0 ? "ACTUALIZAR SECCION" : "AGREGAR SECCION" ;
		Win_Ventana_Seccion = new Ext.Window({
		    width       : 480,
		    height      : 480,
		    id          : 'Win_Ventana_Seccion',
		    title       : title,
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'configuracion_secciones_pos/form_secciones.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					id_seccion : id,
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
		     //            {
							// xtype     : 'button',
							// id        : 'conf_cuentas',
							// width     : 60,
							// height    : 56,
							// text      : 'Cuentas de la seccion',
							// scale     : 'large',
							// iconCls   : 'configurar_informe',
							// iconAlign : 'top',
							// hidden    : false,
							// handler   : function(){ BloqBtn(this); cuentasSeccion() }
		     //            },
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

	var items_by_section = (id_seccion) =>{
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_item_cod_tx = new Ext.Window({
		    width       : myancho-100,
		    height      : myalto-50,
		    id          : 'Win_Ventana_item_cod_tx',
		    title       : 'Configuracion de inventario por seccion',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'configuracion_secciones_pos/items.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					id_seccion : id_seccion,
					var2       : 'var2',
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : 'Opciones',
		            style   : 'border-right:none;',
		            items   :
		            [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_item_cod_tx.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}

</script>