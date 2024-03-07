<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];

	$sql = "SELECT codigo_puc_actividad_economica AS codigo_puc, cuenta_puc_actividad_economica AS cuenta_puc
			FROM empresas WHERE activo=1 AND id='$id_empresa' LIMIT 0,1";
	$query = mysql_query($sql,$link);

	$codigoPuc  = mysql_result($query,0,'codigo_puc');
	$cuentaPuc  = mysql_result($query,0,'cuenta_puc');

?>
<div id="barraBotonesActividadEconomica"></div>
<div style="margin:10px;">
	<div style="margin:0 auto 0 auto;width:100%; overflow:hidden">
		<div style="float:left; width:80px; height:30px;" id="cargarInsertDias">Cuenta PUC</div>
		<div style="float:left; width:18px; margin-left:-20px;" id="renderBuscarCuentaActividadEconomica"></div>
		<div style="float:left">
			<input type="text" class="myField" id="codigoPucActividadEconomica" style="width:100px; height:10px" onKeyup="validaCodigoPucActividadEconomica(event,this)" value="<?php echo $codigoPuc; ?>" />
		</div>
		<div style="float:left; margin-left:-18px; font-size:16px;"><b>&crarr;</b></div>
	</div>
	<div style="margin:0 auto 0 auto;width:100%; overflow:hidden">
		<div style="float:left; width:80px; height:30px;" id="cargarInsertDias">Detalle</div>
		<div style="float:left; width:200px; height:55px;">
			<textarea class="myField" id="detalleCuentaActividadEconomica" style="width:100%; height:50px" readonly><?php echo $cuentaPuc; ?></textarea>
		</div>
	</div>
</div>

<script>
	var id_actividad_economica = 0;
	function validaCodigoPucActividadEconomica(event,input){
		tecla   = (input) ? event.keyCode : event.which;
        numero  = input.value;

        if(input.value != '' && tecla == 13){
            input.blur();
            ajaxbuscarActividadEconomica(input.value);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d]/g;
        if(patron.test(input.value)){ input.value = (input.value).replace(/[^0-9]/g,''); return true; }

        document.getElementById("detalleCuentaActividadEconomica").value = '';
        return true;
	}

	function ajaxbuscarActividadEconomica(cuentaPuc){
		Ext.get('renderBuscarCuentaActividadEconomica').load({
			url     : 'actividad_economica/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				opc       : 'buscarActividadEconomica',
				cuentaPuc : cuentaPuc
			}
		});
	}

	function guardarActividadEconomica(){
		var cod_puc    = document.getElementById('codigoPucActividadEconomica').value
		,	cuenta_puc = document.getElementById('detalleCuentaActividadEconomica').value;

		if(id_actividad_economica == 0 || id_actividad_economica == '' || cod_puc == '' || cuenta_puc == ''){ return; }

		Ext.get('renderBuscarCuentaActividadEconomica').load({
			url     : 'actividad_economica/bd/bd.php',
			timeout : 180000,
			scripts : true,
			nocache : true,
			params  :
			{
				opc         : 'guardarActividadEconomica',
				idCuentaPuc : id_actividad_economica,
				cuentaPuc   : cod_puc
			}
		});
	}

	//barra de botones de la ventana
	var barBt = new Ext.Toolbar();
	barBt.render('barraBotonesActividadEconomica');
	barBt.add({
		xtype   : 'buttongroup',
		columns : 2,
		items   :
		[
			{
				text      : 'Guardar',
				width     : 60,
				height    : 56,
				scale     : 'large',
				iconCls   : 'guardar',
				iconAlign : 'top',
				handler   : function(){ guardarActividadEconomica(); }
			},
			{
				text      : 'Regresar',
				width     : 60,
				height    : 56,
				scale     : 'large',
				iconCls   : 'regresar',
				iconAlign : 'top',
				handler   : function(){ Win_Panel_Global.close(); }
			}
		]
	});
	barBt.doLayout();


</script>
