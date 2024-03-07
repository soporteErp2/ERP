<?php
	include("../../configuracion/conectar.php");
	include("../../configuracion/define_variables.php");
?>

<div id="ToolBarHorasExtras"></div>
<div id="tablaHoras" name="tablaHoras" style="width:100%; height:400; overflow:auto"></div>
    

<script>
	var myhtml = '<div style="width:200px; float:left; margin: 5px;"><div style="width:60px; float:left; margin:0 0 4px 0;">Desde</div><div style="width:100px; float:left; margin:0 0 4px 0;"><input name="fechai" type="text"  id="fechai" value="<?php echo $fechai; ?>" size="10" readonly></div><div style="width:60px; float:left">Hasta</div><div style="width:100px; float:left"><input name="fechaf" type="text"  id="fechaf"  value="<?php echo $fechaf; ?>" size="10" readonly></div></div>';

	new Ext.Toolbar
		(
			{
				renderTo	:'ToolBarHorasExtras',
				frame		:false,
				border		:false,
				items		:
				[
					{
						xtype		: 'buttongroup',
						title		: 'Filtros',
						items		:
						[			
							{
								xtype		: 'panel',
								border		: false,
								width		: 200,
								height		: 56,
								bodyStyle	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								html		: myhtml
							}
							
						]								
						
					},
					{
						xtype		: 'buttongroup',
						title		: 'Opciones',
						columns		: 3,
						items		: 
						[			
							{
								xtype		: 'button',
								text		: 'Generar Informe',
								scale		: 'large',
								iconCls		: 'genera_informe',
								iconAlign	: 'top',
								handler 	: function(){BloqBtn(this); proceso();}
							}							
						]
					}
				]
			}
		);


	function proceso(){
		if(document.getElementById("fechai").value == ""){alert("Inserte Fecha Inicial");return;}
		if(document.getElementById("fechaf").value == ""){alert("Inserte Fecha Final");return;}
		Ext.Ajax.request
			(
				{
				url		: '../personal/bd/bd.php',
				method	: 'post',
				timeout : 180000,
				params	:
					{
						op			: "cargaHorasExtras",
						id			: "<?php echo $id; ?>"	,
						fechai		: document.getElementById("fechai").value,
						fechaf		: document.getElementById("fechaf").value						
					},
				success: function (result, request)
					{
						var resultado  =  result.responseText.split("{.}");
						var respuesta = resultado[0];
						var elid = resultado[1];
						if(respuesta == 'false'){
							alert('Sin Horas Extras!');	
						}
						if(respuesta == 'true'){
							MyLoading();
							document.getElementById("tablaHoras").innerHTML = elid;
						}
					}
				}
			);
	}
	
	new Ext.form.DateField(
		{
			format 		:	'Y-m-d', 
			width		:	120,  
			allowBlank	:	false, 
			showToday	:	true, 
			applyTo		:	'fechaf',
			editable	:	false
		}
	);
	
	new Ext.form.DateField(
		{
			format 		:	'Y-m-d', 
			width		:	120,  
			allowBlank	:	false, 
			showToday	:	true, 
			applyTo		:	'fechai',
			editable	:	false
		}
	);
</script>