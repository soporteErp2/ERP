<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");

	// VERIFICAR EL TAMAÑO DISPONIBLE PARA LA SESION DE ESA EMPRESA
	$size = getFolderSize($_SESSION['ID_HOST'],'../../../../');
	$porcentaje = $size*100/$_SESSION['ALMACENAMIENTO'];
	$proporcion = 400*$porcentaje/100;

	$id_empresa = $_SESSION['EMPRESA'];
	// CONSULTAR EL ESTADO DE LA FACTURA
	$sql    = "SELECT estado FROM compras_facturas WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_factura_compra";
	$query  = $mysql->query($sql,$mysql->link);
	$estado = $mysql->result($query,0,'estado');

	$sql="SELECT id,nombre_archivo,ext,fecha_creacion,usuario
			FROM compras_facturas_archivos_adjuntos
			WHERE
				activo=1
			AND id_empresa        = $id_empresa
			AND id_factura_compra = $id_factura_compra
			AND id_tercero        = $id_tercero
			AND tipo_documento    = '$tipo_documento_cruce'
			AND prefijo_documento = '$prefijo_documento_cruce'
			AND numero_documento  = '$numero_documento_cruce'
			";
	$query=$mysql->query($sql,$mysql->link);
	$style='';
	while ($row=$mysql->fetch_array($query)) {
		$img = ($estado==0)? '<img src="img/delete_file.png" title="Eliminar Archivo" onclick="eliminarArchivoAdjunto(\''.$row['id'].'\',\''.$row['nombre_archivo'].'.'.$row['ext'].'\')">' : '&nbsp;' ;
		$bodyTable .= '<tr '.$style.' id="archivo_adjunto_'.$row['id'].'">
						<td>'.$row['nombre_archivo'].'.'.$row['ext'].'</td>
						<td>'.$row['fecha_creacion'].'</td>
						<td>'.$row['usuario'].'</td>
						<td><img src="img/view.png" title="Ver Archivo" onclick="ver_documento_terceros(\''.$row['id'].'\',\''.$row['nombre_archivo'].'\',\''.$row['ext'].'\')"></td>
						<td>'.$img.'</td>
					</tr>';
		$style = ($style=='')? 'style="background-color:#EAF4FA;" ' : '' ;
	}
?>

<link rel="stylesheet" type="text/css" href="facturacion_cuentas/grilla/style.css">
<script>
	function mostrar_ocultar_almacenamiento() {
		var element = document.getElementById('div_almacenamiento');
		var btn = document.getElementById('btn_almacenamiento');

		if (element.getAttribute('style')=='display:none;' || element.getAttribute('style')==''){
			element.setAttribute('style','display:block;');
			btn.style.backgroundImage = "url('img/regresar.png')";
			btn.setAttribute('title','Regresar');
		}
		else{
			element.setAttribute('style','display:none;');
			btn.style.backgroundImage = "url('img/almacenamiento.png')";
			btn.setAttribute('title','Ver Almacenamiento');
		}
	}
</script>
<div class="content">
	<div class="separator">DOCUMENTOS ADJUNTOS
		<div class="close" onclick="Win_Ventana_adjuntos_documentos.close();"></div>
		<div class="close" title="Ver Almacenamiento" onclick="mostrar_ocultar_almacenamiento()" id="btn_almacenamiento" style="margin-right: 10px;background-image: url('img/almacenamiento.png');height: 40px;width: 40px;margin-top: -10px;" ></div>
	</div>
	<div class="content-almacenamiento" id="div_almacenamiento" style="display:none;">
		<div class="content-sin-espacio">
			<div class="title-sin-espacio" id="label_almacenamiento">INFORMACION DE ALMACENAMIENTO</div>
			<div class="espacio-disponible">
				<div class="espacio-no-disponible" style="width:<?php echo $proporcion; ?>">
				</div>
			</div>
			<div class="content-label">
				<table class="table-espace">
					<tr>
						<td data-color="asignado">&nbsp;</td><td>&nbsp;&nbsp;Espacio Asignado</td><td><?php echo number_format($_SESSION['ALMACENAMIENTO']) ?> MB</td>
					</tr>
					<tr>
						<td data-color="ocupado">&nbsp;</td><td>&nbsp;&nbsp;Espacio Ocupado</td><td><?php echo number_format($size,2) ?> MB</td>
					</tr>
					<tr>
						<td data-color="disponible">&nbsp;</td><td>&nbsp;&nbsp;Espacio Disponible</td><td><?php echo number_format( ($_SESSION['ALMACENAMIENTO']-$size),2) ?> MB</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<?php
		if ($size >= $_SESSION['ALMACENAMIENTO'] ) {
			echo '<script>
					document.getElementById("div_almacenamiento").setAttribute("style","display:block;");
					document.getElementById("btn_almacenamiento").setAttribute("style","display:none;");
					document.getElementById("label_almacenamiento").innerHTML="NO HAY ESPACIO DE ALMACENAMIENTO";
				</script>';
			exit;
		}

		if ($estado==0) {
			?>
			<div class="buttom-content">
				<button class="button" data-value="new" onclick="CargarImagenDocumentoTercero()">Nuevo</button>
			</div>
			<?php
		}
	 ?>
	 <div class="content-table">
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
	</div>
	<div id="loadForm" style="display:none;"></div>
	</div>
	<script>



		function ver_documento_terceros(id,nombre,ext){

			if(ext!='bmp' && ext!='BMP' && ext!='jpg' && ext!='JPG' && ext!='png' && ext!='PNG' && ext!='gif' && ext!='GIF' && ext!='pdf' && ext!='PDF'){
				// window.open('../../../ARCHIVOS_PROPIOS/documentos_tercero/'+nombre+'_'+id+'.'+ext);
				window.location.href='../../../../ARCHIVOS_PROPIOS/empresa_<?php echo $_SESSION["ID_HOST"]; ?>/compras/facturas_cuentas/'+nombre+'.'+ext;
			}
			else{
				if(ext=='pdf'){ viewDocumentosAdjuntos(id,nombre,ext,Ext.getBody().getWidth()-50,Ext.getBody().getHeight()-50); return; }
				else{
					Ext.Ajax.request({
						url		: "facturacion_cuentas/bd/bd.php",
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

									viewDocumentosAdjuntos(id,nombre,ext,ancho,alto);
								  },
						params	:
						{
							opc     : 'consultaSizeImageDocumentTerceros',
							nombre :  nombre+'.'+ext
						}
					});
				}
			}
		}

		function viewDocumentosAdjuntos(id,nombre,ext,ancho,alto){

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
					url		: 'facturacion_cuentas/bd/bd.php',
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

		function eliminarArchivoAdjunto(id,nombre){
			if (confirm('Validacion\nDesea eliminar permanentemente el archivo?')){
				MyLoading2('on');
				Ext.get('loadForm').load({
					url     : 'facturacion_cuentas/bd/bd.php',
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

		function CargarImagenDocumentoTercero(id){
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
					url		: 'facturacion_cuentas/grilla/upload_documento_terceros.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						id_factura_compra       : '<?php echo $id_factura_compra; ?>',
						id_tercero              : '<?php echo $id_tercero; ?>',
						tipo_documento_cruce    : '<?php echo $tipo_documento_cruce ?>',
						prefijo_documento_cruce : '<?php echo $prefijo_documento_cruce ?>',
						numero_documento_cruce  : '<?php echo $numero_documento_cruce ?>',
					}
				}
			}).show();
		}

	</script>