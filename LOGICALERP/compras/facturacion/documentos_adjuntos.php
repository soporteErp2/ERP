<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	// VERIFICAR EL TAMAÑO DISPONIBLE PARA LA SESION DE ESA EMPRESA
	if($_SERVER['SERVER_NAME'] != 'erp.plataforma.co'){
		$size = getFolderSize($_SESSION['ID_HOST'],'../../../');
		$porcentaje = $size*100/$_SESSION['ALMACENAMIENTO'];
		$proporcion = 400*$porcentaje/100;
	}
	else{ $proporcion = 0; }



	$id_empresa = $_SESSION['EMPRESA'];
	// CONSULTAR EL ESTADO DE LA FACTURA
	$sql    = "SELECT estado,id_proveedor FROM compras_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_factura_compra";
	$query  = $mysql->query($sql,$mysql->link);
	$estado     = $mysql->result($query,0,'estado');
	$id_tercero = $mysql->result($query,0,'id_proveedor');

	$sql="SELECT id,nombre_archivo,ext,fecha_creacion,usuario
			FROM compras_facturas_archivos_adjuntos
			WHERE
				activo=1
			AND id_empresa        = $id_empresa
			AND id_factura_compra = $id_factura_compra
			AND id_tercero        = $id_tercero
			";
	$query=$mysql->query($sql,$mysql->link);
	$style='';
	while ($row=$mysql->fetch_array($query)) {
		$img = ($estado==0)? '<img src="img/delete_file.png" title="Eliminar Archivo" onclick="eliminarArchivoAdjuntoFacturaCompra(\''.$row['id'].'\',\''.$row['nombre_archivo'].'.'.$row['ext'].'\')">' : '&nbsp;' ;
		$bodyTable .= '<tr '.$style.' id="archivo_adjunto_'.$row['id'].'">
						<td>'.$row['nombre_archivo'].'.'.$row['ext'].'</td>
						<td>'.$row['fecha_creacion'].'</td>
						<td>'.$row['usuario'].'</td>
						<td><img src="img/view.png" title="Ver Archivo" onclick="ver_documento_factura_compra(\''.$row['id'].'\',\''.$row['nombre_archivo'].'\',\''.$row['ext'].'\')"></td>
						<td>'.$img.'</td>
					</tr>';
		$style = ($style=='')? 'style="background-color:#EAF4FA;" ' : '' ;
	}

?>

<link rel="stylesheet" type="text/css" href="facturacion_cuentas/grilla/style.css">
<script>
	function mostrar_ocultar_almacenamiento() {
		var element  = document.getElementById('div_almacenamiento');
		var element2 = document.getElementById('divAdjuntos');
		var btn      = document.getElementById('btn_almacenamiento');

		if (element.getAttribute('style')=='display:none;' || element.getAttribute('style')==''){
			element.setAttribute('style','display:block;');
			element2.setAttribute('style','display:none;');
			btn.style.backgroundImage = "url('img/regresar.png')";
			btn.setAttribute('title','Regresar');
			loadAlmacenamiento();
		}
		else{
			element.setAttribute('style','display:none;');
			element2.setAttribute('style','display:block;');
			btn.style.backgroundImage = "url('img/almacenamiento.png')";
			btn.setAttribute('title','Ver Almacenamiento');
		}
	}
	function loadAlmacenamiento(){
		Ext.get('div_almacenamiento').load({
			url     : 'facturacion/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc : 'mostrarAlmacenamiento',
			}
		});
	}
</script>
<div class="content" style="overflow:hidden;">
	<div class="separator">DOCUMENTOS ADJUNTOS
		<div class="close" onclick="Win_ventanaDocumentosCruceFacturaCompra.close();" title="Cerrar"></div>
		<div class="close" title="Ver Almacenamiento" onclick="mostrar_ocultar_almacenamiento()" id="btn_almacenamiento" style="margin-right: 10px;background-image: url('img/almacenamiento.png');height: 40px;width: 40px;margin-top: -10px;" ></div>
	</div>
	<div class="content-almacenamiento" id="div_almacenamiento" style="display:none;">

	</div>
	<div id="divAdjuntos">
	<?php
		if ($size >= $_SESSION['ALMACENAMIENTO'] ) {
			echo '<script>
					document.getElementById("div_almacenamiento").setAttribute("style","display:block;");
					document.getElementById("btn_almacenamiento").setAttribute("style","display:none;");
					loadAlmacenamiento();
				</script>';
			exit;
		}

		if ($id_tercero=='' || $id_tercero==0) {
			echo '<div class="content-sin-espacio">
					<div class="title-sin-espacio"> Debe seleccionar primero un tercero en la factura</div>
				</div>';
			exit;
		}

		if ($estado==0) {
			?>
			<div class="buttom-content">
				<button class="button" data-value="new" onclick="CargarImagenDocumentoTerceroFacturaCompra()">Nuevo</button>
			</div>
			<?php
		}


	 ?>

	<table class="table-grilla">
		<tr class="thead">
			<td>NOMBRE</td>
			<td>FECHA</td>
			<td>USUARIO</td>
			<td></td>
			<td></td>
		</tr>

		<tbody class="tbody" id="archivos_adjuntos">
			<?php echo $bodyTable; ?>
		</tbody>

	</table>
	<div id="loadForm" style="display:none;"></div>
	</div>
	<script>


		function ver_documento_factura_compra(id,nombre,ext){

			if(ext!='bmp' && ext!='BMP' && ext!='jpg' && ext!='JPG' && ext!='png' && ext!='PNG' && ext!='gif' && ext!='GIF' && ext!='pdf' && ext!='PDF'){
				// window.open('../../../ARCHIVOS_PROPIOS/documentos_tercero/'+nombre+'_'+id+'.'+ext);
				window.location.href='../../../../ARCHIVOS_PROPIOS/empresa_<?php echo $_SESSION["ID_HOST"]; ?>/compras/facturas/'+nombre+'.'+ext;
			}
			else{
				if(ext=='pdf'){ viewDocumentosAdjuntosFacturaCompra(id,nombre,ext,Ext.getBody().getWidth()-50,Ext.getBody().getHeight()-50); return; }
				else{
					Ext.Ajax.request({
						url		: "facturacion/bd/bd.php",
						success	: function(response){
									// console.log(response);
									response  = response.responseText;
									response  = JSON.parse(response);
									var alto  = response.alto
									,	ancho = response.ancho;

									if(response.alto<96){ alto=96; }
									else if(response.alto>Ext.getBody().getHeight()-170){ alto = Ext.getBody().getHeight()-170; }
									else{ alto += 10; }

									if(response.ancho<96){ ancho=96; }
									else if(response.ancho>Ext.getBody().getWidth()-120){ ancho = Ext.getBody().getWidth()-120; }
									else{ ancho += 10; }

									alto  += 100;
									ancho += 70;

									viewDocumentosAdjuntosFacturaCompra(id,nombre,ext,ancho,alto);
								  },
						params	:
						{
							opc     : 'consultaSizeImageDocumentTerceros',
							nombre : nombre+'.'+ext
						}
					});
				}
			}
		}

		function viewDocumentosAdjuntosFacturaCompra(id,nombre,ext,ancho,alto){

			Win_Ventana_VerDocumento_Terceros = new Ext.Window({
				width		: ancho,
				height		: alto,
				id			: 'Win_Ventana_VerDocumento_Terceros',
				title		: 'Archivo',
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'facturacion/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc              : 'ventanaVerImagenDocumentoTerceros',
						nombreImage     : nombre+'.'+ext,
						nombreDocumento : nombre+'.'+ext,
						type            : ext
					}
				},
				tbar		:
				[
					{
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'left',
						handler   : function(){ Win_Ventana_VerDocumento_Terceros.close(); }
					}
				]
			}).show();
		}

		function eliminarArchivoAdjuntoFacturaCompra(id,nombre){
			if (confirm('Validacion\nDesea eliminar permanentemente el archivo?')){
				MyLoading2('on');
				Ext.get('loadForm').load({
					url     : 'facturacion/bd/bd.php',
					scripts : true,
					nocache : true,
					params  :
					{
						opc    : 'eliminarArchivoAdjunto',
						id     : id,
						nombre : nombre,
					}
				});
			}
		}

		function CargarImagenDocumentoTerceroFacturaCompra(id){
			Win_Ventana_Agregar_archivos_adjuntos = new Ext.Window({
				width		: 330,
				height		: 220,
				id			: 'Win_Ventana_Agregar_archivos_adjuntos',
				title		: 'Agregar Archivo Adjunto',
				bodyStyle   : 'background-color:#FFF;',
				modal		: true,
				autoScroll	: false,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'facturacion/upload_documento.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						id_factura_compra : '<?php echo $id_factura_compra; ?>',
						id_tercero        : '<?php echo $id_tercero; ?>',
					}
				}
			}).show();
		}

	</script>