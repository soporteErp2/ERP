<?php
	include("../../../../configuracion/conectar.php");
	include("../../../../configuracion/define_variables.php");
	include("items/bd/selec_items_documentos.php");

	$id_empresa   = $_SESSION['EMPRESA'];
	$varNewOption = '';

	$sqlModuloPostItem = "SELECT modulo_pos FROM items WHERE id=$id AND activo=1 AND id_empresa='$id_empresa' LIMIT 0,1";
	$varModuloPost     = mysql_result(mysql_query($sqlModuloPostItem,$link),0,'modulo_pos');

	$sqlDocumentosItem  = "SELECT COUNT(id) AS cont FROM items_documentos WHERE  activo=1 AND id_inventario='$id' AND tipo_documento_nombre = 'Imagen Logo'";
	$contDocumentosItem = mysql_result(mysql_query($sqlDocumentosItem,$link),0,'cont');


	if($varModuloPost == 'true' && $contDocumentosItem == 0){ $varNewOption = '<option value="4">Imagen Logo</option>'; }
	
?>
<div id="DivBtnDocumentsSigui" style="float:left; width:320px; height:auto; margin:0 0 0 0"></div>

<div style="float:left; width:287px; height:60px; border:1px #000">
    <div class="EmpConte" style="margin:10px 0 0 10px; width:300px">
        <div style="float:left; width:60px;">Documento</div>
        <div style="float:left; width:150px;">
            <select class="myfield" name="documentoItems" id="documentoItems" style="width:100%;">
              	<option value="0" selected>Seleccione...</option>
                <option value="1">Imagen</option>
                <option value="2">Carta</option>
                <option value="3">Documento General</option>
                <?php echo $varNewOption; ?>
            </select>
    	</div>
    </div>
</div>

<script>

	new Ext.Panel({
		border		: false,
		renderTo	: 'DivBtnDocumentsSigui',
		bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
		tbar		:
		[
			{
				xtype		: 'button',
				text		: 'Siguiente',
				scale		: 'large',
				iconCls		: 'siguiente',
				iconAlign	: 'left',
				handler 	: function(){
									BloqBtn(this);
									var tipo_documento = document.getElementById("documentoItems").value;
									if(tipo_documento == 0){ alert('Primero debe seleccionar el tipo de Documento!'); return false; }
									SiguienteDocumentoItems(tipo_documento);
									Win_select_items_documentos.close();
							  }
			}
		]
	});
////////////////////////////////..genera ventana upload.////////////////////////////////////////////
	function SiguienteDocumentoItems(idTipoArchivo){
		var tipoDocumento = document.getElementById('documentoItems').value;
		Win_Ventana_siguiente_documentos = new Ext.Window({
			width		: 320,
			id			: 'Win_Ventana_siguiente_documentos',
			height		: 230,
			title		: 'Cargar Archivo',
			modal		: true,
			autoScroll	: false,
			closable	: true,
			autoDestroy : true,
			autoLoad	:
			{
				url		: 'items/items_documentos/upload/documento_items.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					op            : "createUploader",
					idTipoArchivo : idTipoArchivo,
					idItem        : <?php echo $id;?>,
				}
			}
		}).show();
	}

	document.getElementById('documentoItems').onchange = function(){ actionCambiaTipoDocumento(this.value); };
	function actionCambiaTipoDocumento(tipoDocumento){
		if(tipoDocumento==4){ alert("Se recomienda el uso de imagenes de 100px de alto por 75px de ancho en los logos para el sistema pos."); }
	}

</script>