<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	// VERIFICAR EL TAMAÑO DISPONIBLE PARA LA SESION DE ESA EMPRESA
	$size = getFolderSize($_SESSION['ID_HOST'],'../../../');
	$porcentaje = $size*100/$_SESSION['ALMACENAMIENTO'];
	$proporcion = 400*$porcentaje/100;


	$id_empresa = $_SESSION['EMPRESA'];
	// CONSULTAR EL ESTADO DE LA FACTURA

	if($opc == 'Proyectos'){
		$tabla    = 'crm_objetivos_adjuntos';
		$tabla_id = 'id_objetivo';
		$folder   = 'crm/objetivos_adjuntos/';
	}
	
	$sql="SELECT id,nombre_archivo,ext,fecha_creacion,usuario
			FROM $tabla
			WHERE
				activo=1
			AND id_empresa = $id_empresa
			AND $tabla_id  = $id			
			";
	$query=$mysql->query($sql,$mysql->link);
	$style='';
	while ($row=$mysql->fetch_array($query)) {
		$img = '<img src="img/delete_file.png" title="Eliminar Archivo" onclick="eliminarArchivoAdjunto(\''.$row['id'].'\',\''.$row['nombre_archivo'].'.'.$row['ext'].'\',\''.$folder.'\')">';
		$bodyTable .= '<tr '.$style.' id="archivo_adjunto_'.$row['id'].'">
						<td style="width:40%;text-overflow:ellipsis;overflow:hidden;">'.$row['nombre_archivo'].'.'.$row['ext'].'</td>
						<td style="width:20%">'.$row['fecha_creacion'].'</td>
						<td style="width:20%">'.$row['usuario'].'</td>
						<td style="width:10%"><img src="img/view.png" title="Ver Archivo" onclick="ver_documentos_adjuntos(\''.$row['id'].'\',\''.$row['nombre_archivo'].'\',\''.$row['ext'].'\',\''.$folder.'\')"></td>
						<td style="width:10%">'.$img.'</td>
					</tr>';
		$style = ($style=='')? 'style="background-color:#EAF4FA;" ' : '' ;
	}

?>

<link rel="stylesheet" type="text/css" href="upload_files/style.css">
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
			url     : 'bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc : 'mostrarAlmacenamiento',					
			}
		});
	}
</script>
<div class="content" style="overflow:auto;">
	<div class="separator">DOCUMENTOS ADJUNTOS
		<div class="close" onclick="Win_ventanaDocumentos<?php echo $opc ?>.close();" title="Cerrar"></div>
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

		if ($estado==0) {
			?>
			<div class="buttom-content">
				<button class="button" data-value="new" onclick="CargarImagenDocumento('<?php echo $folder; ?>')">Nuevo</button>
			</div>
			<?php
		}


	?>
	
		<table class="table-grilla" style="table-layout: fixed">
			<tr class="thead">
				<td style="width:40%">NOMBRE</td>
				<td style="width:20%">FECHA</td>
				<td style="width:20%">USUARIO</td>
				<td style="width:10%"></td>
				<td style="width:10%"></td>
			</tr>
	
			<tbody class="tbody" id="archivos_adjuntos">
				<?php echo $bodyTable; ?>
			</tbody>
	
		</table>
	</div>
	<div id="loadForm" style="display:none;"></div>
	</div>
	<script>


		function ver_documentos_adjuntos(id,nombre,ext,folder){

			if(ext!='bmp' && ext!='BMP' && ext!='jpg' && ext!='JPG' && ext!='png' && ext!='PNG' && ext!='gif' && ext!='GIF' && ext!='pdf' && ext!='PDF'){
				// window.open('../../../ARCHIVOS_PROPIOS/documentos_tercero/'+nombre+'_'+id+'.'+ext);
				window.location.href='../../../../ARCHIVOS_PROPIOS/empresa_<?php echo $_SESSION["ID_HOST"]; ?>/'+folder+nombre+'.'+ext;
			}
			else{
				if(ext=='pdf'){ viewDocumentosAdjuntos(id,nombre,ext,Ext.getBody().getWidth()-50,Ext.getBody().getHeight()-50,folder); return; }
				else{
					Ext.Ajax.request({
						url		: "bd/bd.php",
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

									viewDocumentosAdjuntos(id,nombre,ext,ancho,alto,folder);
								  },
						params	:
						{
							opc    : 'consultaSizeImage',
							nombre : nombre+'.'+ext,
							folder : folder
						}
					});
				}
			}
		}

		function viewDocumentosAdjuntos(id,nombre,ext,ancho,alto,folder){

			Win_Ventana_VerDocumento_Adjunto = new Ext.Window({
				width		: ancho,
				height		: alto,
				id			: 'Win_Ventana_VerDocumento_Adjunto',
				title		: 'Archivo',
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: 'bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc             : 'ventanaVerImagen',
						nombreImage     : nombre+'.'+ext,
						nombreDocumento : nombre+'.'+ext,
						type            : ext,
						folder          : folder
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
						handler   : function(){ Win_Ventana_VerDocumento_Adjunto.close(); }
					}
				]
			}).show();
		}

		function eliminarArchivoAdjunto(id,nombre,folder){
			if (confirm('Validacion\nDesea eliminar permanentemente el archivo?')){
				MyLoading2('on');
				Ext.get('loadForm').load({
					url     : 'bd/bd.php',
					scripts : true,
					nocache : true,
					params  :
					{
						opc    : 'eliminarArchivoAdjunto',
						id     : id,
						nombre : nombre,
						folder : folder
					}
				});
			}
		}

		function CargarImagenDocumento(folder){
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
					url		: 'upload_files/upload_documento.php',
					scripts	: true,
					nocache	: true,
					params	:
					{						
						id     : '<?php echo $id; ?>',
						opc    : '<?php echo $opc; ?>',
						folder : folder
					}
				}
			}).show();
		}

	</script>