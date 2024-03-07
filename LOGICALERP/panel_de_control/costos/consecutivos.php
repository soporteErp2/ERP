<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	$sqlConsecutivo   = "SELECT documento, consecutivo, modulo
						FROM configuracion_consecutivos_documentos
						WHERE activo=1 AND id_empresa='$id_empresa' AND id_sucursal='$filtro_sucursal' AND modulo='compra'
						ORDER BY modulo ASC";
	$queryConsecutivo = mysql_query($sqlConsecutivo,$link);

	$body  = '';
	$title = '';
	while ($rowConsecutivo = mysql_fetch_array($queryConsecutivo)) {
		$label = str_replace("_", " ", $rowConsecutivo['documento']);
		$body .= '<div style="float:left; width:60%; height:23px; margin-top:10px; text-transform:capitalize;">'.$label.'</div>
				<div style="float:left; width:38%; margin-top:10px;">
					<input type="text" class="myField" id="'.$rowConsecutivo['documento'].'" style="width:100%; height:25px" onKeyup="validaNumero(this)" value="'.$rowConsecutivo['consecutivo'].'" />
				</div>';
	}

	$arrayDocs = array('orden_de_compra','factura','comprobante_de_egreso');


?>
<div id="barraBotonesConsecutivos"></div>
<div id="loadConsecutivosDocumentos"></div>
<div style="margin:10px; overflow:hidden; padding-bottom:10px;" id="contenedorConsecutivos"><?php echo $body; ?></div>

<script>

	function guardarConsecutivosDocumentos(){
		var jsonConsecutivos  = ''
		,	arrayConsecutivos = document.getElementById('contenedorConsecutivos').querySelectorAll('.myField');

		for(indice in arrayConsecutivos){
			if(typeof(arrayConsecutivos[indice].id) != 'undefined'){
				if(isNaN(arrayConsecutivos[indice].value)){ alert("Aviso,\nEl campo "+arrayConsecutivos[indice].id+" debe ser numerico."); return; }
				jsonConsecutivos += ''+arrayConsecutivos[indice].id+':'+arrayConsecutivos[indice].value+',';
			}
		}

		jsonConsecutivos += '}';
		jsonConsecutivos = jsonConsecutivos.replace(',}','');

		Ext.get('loadConsecutivosDocumentos').load({
			url     : 'compras/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				op               : 'guardarConsecutivosDocumentos',
				filtro_sucursal  : '<?php echo $filtro_sucursal; ?>',
				jsonConsecutivos : jsonConsecutivos
			}
		});
	}

	function validaNumero(input){
		var patron = /[^0-9]/g
		if (patron.test(input.value)) { input.value = (input.value).replace(/[^0-9]/g,''); }
		return true;
	}

	//barra de botones de la ventana
	var tb = new Ext.Toolbar();
	tb.render('barraBotonesConsecutivos');
	tb.add({
		xtype   : 'buttongroup',
		columns : 2,
		items   :
		[
			{
				text		: 'Guardar',
				scale		: 'large',
				width       : 80,
				height 		: 60,
				iconCls		: 'guardar',
				iconAlign	: 'top',
				handler		: function(){ guardarConsecutivosDocumentos(); }
			},
			{
				text		: 'Regresar',
				scale		: 'large',
				width       : 80,
				height 		: 60,
				iconCls		: 'regresar',
				iconAlign	: 'top',
				handler		: function(){ Win_Ventana_Sucursal.close(); }
			}
		]
	});
	tb.doLayout();


</script>
