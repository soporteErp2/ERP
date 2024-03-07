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

	$sql="SELECT 
	         E.id,
		  	 E.randomico_archivo,
		  	 E.ext,
		  	 E.fecha_hora,
		  	 EMP.nombre
		  FROM empleados_contratos_documentos AS E
		  INNER JOIN empleados AS EMP ON (E.id_usuario = EMP.id)
		  WHERE
		  	E.activo=1		  
		  AND E.id_contrato = '$id_contrato'
		  ";
	$query=$mysql->query($sql,$mysql->link);
	$style='';
	while ($row=$mysql->fetch_array($query)) {
		$img = ($estado==0)? '<img src="../personal/images/delete_file.png" title="Eliminar Archivo" onclick="eliminarDocumentoVencimiento(\''.$row['id'].'\',\''.$row['randomico_archivo'].'.'.$row['ext'].'\')">' : '&nbsp;' ;
		$bodyTable .= '<tr '.$style.' id="archivo_adjunto_'.$row['id'].'">
						<td>'.$row['randomico_archivo'].'.'.$row['ext'].'</td>
						<td>'.$row['fecha_hora'].'</td>
						<td>'.$row['nombre'].'</td>
						<td><img src="../personal/images/view.png" title="Ver Archivo" onclick="ver_documento_contrato(\''.$row['id'].'\',\''.$row['randomico_archivo'].'\',\''.$row['ext'].'\')"></td>
						<td>'.$img.'</td>
					</tr>';
		$style = ($style=='')? 'style="background-color:#EAF4FA;" ' : '' ;
	}

?>

<link rel="stylesheet" type="text/css" href="contratos_vencimientos/style.css">
<script>
	function mostrar_ocultar_almacenamiento() {
		var element  = document.getElementById('div_almacenamiento');
		var element2 = document.getElementById('divAdjuntos');
		var btn      = document.getElementById('btn_almacenamiento');

		if (element.getAttribute('style')=='display:none;' || element.getAttribute('style')==''){
			element.setAttribute('style','display:block;');
			element2.setAttribute('style','display:none;');
			btn.style.backgroundImage = "url('../personal/images/regresar.png')";
			btn.setAttribute('title','Regresar');
			loadAlmacenamiento();
		}
		else{
			element.setAttribute('style','display:none;');
			element2.setAttribute('style','display:block;');
			btn.style.backgroundImage = "url('../personal/images/almacenamiento.png')";
			btn.setAttribute('title','Ver Almacenamiento');
		}
	}
	function loadAlmacenamiento(){
		Ext.get('div_almacenamiento').load({
			url     : '../personal/contratos_vencimientos/bd/bd.php',
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
		<div class="close" onclick="Win_ventanaContratosVencimientos.close();" title="Cerrar"></div>
		<div class="close" title="Ver Almacenamiento" onclick="mostrar_ocultar_almacenamiento()" id="btn_almacenamiento" style="margin-right: 10px;background-image: url('../personal/images/almacenamiento.png');height: 40px;width: 40px;margin-top: -10px;" ></div>
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
				<button class="button" data-value="new" onclick="CargarImagenDocumentoVencimiento()">Nuevo</button>
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


		function ver_documento_contrato(id,nombre,ext){

			if(ext!='bmp' && ext!='BMP' && ext!='jpg' && ext!='JPG' && ext!='png' && ext!='PNG' && ext!='gif' && ext!='GIF' && ext!='pdf' && ext!='PDF'){
				// window.open('../../../ARCHIVOS_PROPIOS/documentos_tercero/'+nombre+'_'+id+'.'+ext);
				window.location.href='../../../../ARCHIVOS_PROPIOS/empresa_<?php echo $_SESSION["ID_HOST"]; ?>/compras/facturas/'+nombre+'.'+ext;
			}
			else{
				if(ext=='pdf'){ viewDocumentoVencimiento(id,nombre,ext,Ext.getBody().getWidth()-50,Ext.getBody().getHeight()-50); return; }
				else{
					Ext.Ajax.request({
						url		: "../personal/contratos_vencimientos/bd/bd.php",
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

									viewDocumentoVencimiento(id,nombre,ext,ancho,alto);
								  },
						params	:
						{
							opc     : 'consultaSizeImageDocumentoVencimiento',
							nombre : nombre+'.'+ext
						}
					});
				}
			}
		}

		function viewDocumentoVencimiento(id,nombre,ext,ancho,alto){

			Win_Ventana_VerDocumentoVencimiento = new Ext.Window({
				width		: ancho,
				height		: alto,
				id			: 'Win_Ventana_VerDocumentoVencimiento',
				title		: 'Archivo',
				modal		: true,
				autoScroll	: true,
				closable	: true,
				autoDestroy : true,
				autoLoad	:
				{
					url		: '../personal/contratos_vencimientos/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc              : 'ventanaVerImagenDocumentoVencimiento',
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
						handler   : function(){ Win_Ventana_VerDocumentoVencimiento.close(); }
					}
				]
			}).show();
		}

		function eliminarDocumentoVencimiento(id,nombre){
			if (confirm('Validacion\nDesea eliminar permanentemente el archivo?')){
				MyLoading2('on');
				Ext.get('loadForm').load({
					url     : '../personal/contratos_vencimientos/bd/bd.php',
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

		function CargarImagenDocumentoVencimiento(){
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
					url		: '../personal/contratos_vencimientos/upload_vencimiento2.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						id_contrato : '<?php echo $id_contrato; ?>',						
					}
				}
			}).show();
		}

	</script>