<?php
	require_once("../../../configuracion/conectar.php");
	require_once("../../../configuracion/define_variables.php");
	$sql   = "SELECT tipo FROM api_conections WHERE activo=1 AND id=$id_api";
	$query = $mysql->query($sql);
	$tipo  = $mysql->result($query,0,'tipo');
	// $method = ($tipo=='INGRESO')? "setCausacionIngreso" : "setCausacionReversion" ;
 ?>
 <style>
 	.contentApiResult{
		background-color: #FFF;
		width: 100%;
		height: calc(100% - 84px);

 	}
 </style>
 <div>
 	<div id="tbar_api"></div>
 	<div class="contentApiResult" id="contentApiResult"></div>
 </div>
 <script>
 	if (!document.getElementById('tbar_secciones')) {
 		var htmlPanel = `<div style='width:100%;height:100%;text-align:center;padding-top:15px;' >
							<input type="text" id="fecha_api"/>
						</div>
						`;
		new Ext.Panel
		(
			{
				renderTo :'tbar_api',
				id       : "tbar_secciones",
				frame    :false,
				border   :false,
				tbar     :
				[
					{
						xtype   : 'buttongroup',
						columns : 3,
						title   : 'Fecha',
						items   :
						[
							{
								xtype     : 'panel',
								border    : false,
								width     : 160,
								height    : 56,
								bodyStyle : 'background-color:rgba(255,255,255,0)',
								html      : htmlPanel
							},
						]
					},
					{
						xtype   : 'buttongroup',
						columns : 3,
						title   : 'Sincronizar',
						items   :
						[
							{
								xtype     : 'button',
								text      : 'Sincronizar',
								scale     : 'large',
								iconCls   : 'sync',
								iconAlign : 'top',
								handler   : function(){BloqBtn(this); sincronizarApi();}
							},
						]
					}
				]
			}
		);
	}

	new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 130,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'fecha_api',
	    editable   : false,                 //EDITABLE
	    value      : new Date(),             //VALOR POR DEFECTO
	});

	var sincronizarApi = () =>{
		fecha_api = document.getElementById('fecha_api').value
		if (fecha_api=='') { alert("debe seleccionar una fecha para la sincronizacion!"); return; }

		Ext.get('contentApiResult').load({
			url     : '../external_apis/LOGICALHOTELS/backend/controller.php',
			scripts : true,
			nocache : true,
			text    : 'Sincronizando...',
			timeout : 600000,
			params  :
			{
				id_api    : '<?php echo $id_api ?>',
				fecha_api : fecha_api,
				method    : "setCausacion",
				tipo      : "<?php echo $tipo ?>"
			}
		});

	}

 </script>