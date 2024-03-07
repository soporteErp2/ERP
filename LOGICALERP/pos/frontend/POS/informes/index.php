<?php

	include('../../../../../configuracion/conectar.php');
	include('../../../../../configuracion/define_variables.php');
	$arrayEmpresa = explode("-", $_SESSION['NITEMPRESA']);

?>
<link rel="stylesheet" href="../../../../../misc/Win/css/Win.min.css" />
<link rel="stylesheet" href="../../../../../misc/Win/css/Win-theme-blue.min.css" />
<script type="text/javascript" src="../../../../..//misc/jquery/jquery.min.js"></script>
<script type="text/javascript" src="../../../../../misc/Win/js/Win.min.js"></script>
<div id="tab-soporte" style="width:100%; height: 100%"></div>
<script>
	$W.Add({
		idApply : "tab-soporte",
		items :
		[
			{
				xtype       : "tabpanel",
				id          : "idTabSoporte",
				items       :
				[
					{
						xtype    : "tab",
						title    : "Informe Productos",
						selected : true,
						scrollY  : true,
						icon 	 : 'shopping_basket',
						autoLoad : {url : "informes_productos/index.php"	}
					},
					{
						xtype    : "tab",
						title    : "Informe Movimientos",
						icon	 : 'receipt',
						autoLoad : {url : "informes_movimientos/index.php"	}
					},
				]
			}
		]
	})

	var WWizard = (params) => {
		WinWizard = new $W.Window({
			id          : "wGlobal",
			title       : `${params.title}`,
			width       : `${params.width}`,
			height      : `${params.height}`,
			scrollY 	: true,
			modal       : true,
			closable    : true,
			drag        : true,
			resize      : true,
			autoLoad    :
			{
				url     : `${params.url}`,
				params  : {}
			},
		});
	}

	var prinTDoc = (id_documento) =>{
		window.open(`http://logicalsoft-erp.com/LOGICALERP/pos/backend/pos/Controller.php?method=printTiquet&id_documento=${id_documento}&nit=<?= $arrayEmpresa[0] ?>`);
	}

	var prinTComanda = (id_documento) =>{
		window.open(`http://logicalsoft-erp.com/LOGICALERP/pos/backend/pos/Controller.php?method=printComanda&id_comanda=${id_documento}&nit=<?= $arrayEmpresa[0] ?>`);
	}

</script>