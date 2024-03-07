<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	$sqlConsecutivo   = "SELECT documento, consecutivo, modulo,digitos
						FROM configuracion_consecutivos_documentos
						WHERE activo=1 AND id_empresa='$id_empresa' AND id_sucursal='$filtro_sucursal' AND modulo='compra'
						ORDER BY modulo ASC";
	$queryConsecutivo = mysql_query($sqlConsecutivo);
	if(!$queryConsecutivo){ echo mysql_errno().mysql_error(); }

	$body  = '';
	$title = '';
	while ($rowConsecutivo = $mysql->fetch_assoc($queryConsecutivo)) {
		$label = str_replace("_", " ", $rowConsecutivo['documento']);
		$body .= '<div style="float:left; width:90px; height:25px; margin-top:10px; text-transform:capitalize;">'.$label.'</div>
				<div style="float:left; width:135px; margin-top:10px; margin-left:20px;">
					<input type="text" class="myField" id="'.$rowConsecutivo['documento'].'" style="width:75%; margin-right:1px;" margin-left:"5px;" data-tipo="consecutivo" onKeyup="validaNumero(this)" value="'.$rowConsecutivo['consecutivo'].'"/>
					<input type="text" class="myField" id="_'.$rowConsecutivo['documento'].'" style="width:24%;" onKeyup="validaNumero(this)" value="'.$rowConsecutivo['digitos'].'"/>
				</div>';
	}

	$arrayDocs = array('orden_de_compra','factura','comprobante_de_egreso');
?>


<style type="text/css">
	#contenedorConsecutivos{
		padding   : 10px; 
		height   : 245px; 
		width    : 100%;
		overflow : hidden;
		box-sizing : border-box;
		display : inline-block;
	}

	#contenedorConsecutivos input{
		float  : left;
		height : 25px;
		padding: 3px;
		text-align: right;

	}

	#contenedorConsecutivos > div{
		width    : 100%;
		overflow : hidden;
	}

	#contenedorConsecutivos p{
		float    : left;
		overflow : hidden;
	}

	#contenedorConsecutivos *{
		box-sizing : border-box;
	}

	#loadConsecutivosDocumentos{
		position : fixed;
		width    : 20px;
		height   : 20px;
		margin   : 5px;
		overflow : hidden;
	}
</style>
<div id="barraBotonesConsecutivos"></div>
<div id="loadConsecutivosDocumentos"></div>
<div id="contenedorConsecutivos">
    <div>
		<p style="width:90px; margin-right:1%;">&nbsp;</p>
		<p style="width:92px; margin-right:3%; margin-left: 16px;">CONSECUTIVO</p>
		<p style="width:42px; ">DÍGITOS</p>
	</div> 
<?php echo $body; ?>
</div>

<script>

	function guardarConsecutivosDocumentos(){
        
        var objData = {}
		,	inputs = document.querySelectorAll('[data-tipo=consecutivo]')
		,	camposVacios = 0
		,	totalConsecutivos = 0;

		[].forEach.call(inputs,function(element){
			var consecutivo = element.value;
			var digitos     = document.getElementById("_"+element.id).value;

			if(consecutivo && digitos){

				if(consecutivo>0){

					totalConsecutivos++;
					objData[element.id] = {
						value   : consecutivo,
						digitos : digitos
					}

				}else{ alert("Por favor ingrese números mayores a 0."); }
			  
			}else{
			   camposVacios++;
			   if (camposVacios==1) alert("Todos los campos son obligatorios.");
			}
		});
		
		//Solamente se envia el objeto lleno.
		if(totalConsecutivos!==inputs.length){ return false; }
		  
     
   			Ext.get('loadConsecutivosDocumentos').load({
			url     : 'compras/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				op               : 'guardarConsecutivosDocumentos',
				filtro_sucursal  : '<?php echo $filtro_sucursal; ?>',
				jsonData : JSON.stringify(objData)
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
	tb.add([{
			text		: 'Guardar',
			scale		: 'large',
			width       : 80,
			height 		: 60,
			iconCls		: 'guardar',
			iconAlign	: 'top',
			handler		: function(){ guardarConsecutivosDocumentos(); }  
		}
	]);
	tb.doLayout();


</script>
